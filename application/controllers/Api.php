<?php
class Api extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('api_model');
		$this->load->model('profile_model');
		$this->load->model('agency_user_account_model');		
        $this->load->model('logs_model');
		$this->load->model('agency_api_integration_model');
        $this->load->library('email');
	}

	public function index(){

    }


    public function connections(){

		$this->load->model('user_accounts_model');
		$this->load->model('agency_model');

		$data['title'] = "API Connections";

		$sel_query = "
			`agency_api_id`,
			`api_name`,
			`img_name`
		";
		$agency_api_params = array(
			'sel_query' => $sel_query,
			'active' => 1,
			'sort_list' => array(
				array(
					'order_by' => 'api_name',
					'sort' => 'ASC'
				)
			),
			'display_query' => 0
		);
		$agencyApis = $this->api_model->get_agency_api($agency_api_params)->result();
		//echo $this->db->last_query();

		$sel_query = "COUNT(`agency_api_id`) AS jcount";
		$agency_api_params = array(
			'sel_query' => $sel_query,
			'active' => 1
		);
		$agency_api_sql = $this->api_model->get_agency_api($agency_api_params);
		$data['agency_api_count'] = $agency_api_sql->row()->jcount;

		// if multi agency user
		$sel_query = '
			aua.`agency_user_account_id`,
			aua.`alt_agencies`
		';
		$custom_where = "aua.`alt_agencies` != ''";
		$user_params = array(
			'sel_query' => $sel_query,
			'custom_where' => $custom_where,
			'aua_id' => $this->session->aua_id,
			'display_query' => 0
		);
		$user_sql = $this->user_accounts_model->get_user_accounts($user_params);
		$data['user_has_alt_agency'] = ( $user_sql->num_rows() > 0 )?true:false;

		// get agency name
		$params = array(
			'sel_query' => '
				a.`agency_id`,
				a.`agency_name`,
				a.`state`,

				c.`agent_number`
			',
			'join_table' => array('countries'),
			'agency_id' => $this->session->agency_id
		);
		$agency_sql = $this->agency_model->get_agency_data($params);
		$agency_row = $agency_sql->row();
		$data['agency_row'] = $agency_row;
		$data['agency_name'] = $agency_row->agency_name;

		$agencyApisById = [];
		$apiIds = [];
		for ($x = 0; $x < count($agencyApis); $x++) {
			$agencyApi =& $agencyApis[$x];
			$apiIds[] = $agencyApi->agency_api_id;

			$agencyApi->permission_granted = false;
			$agencyApi->connected = false;

			$agencyApisById[$agencyApi->agency_api_id] = $agencyApi;
		}

		if (!empty($apiIds)) {
			$apiIntegCounts = $this->db->select('connected_service, COUNT(`api_integration_id`) as jcount')
				->from('agency_api_integration')
				->where_in('connected_service', $apiIds)
				->where('agency_id', $this->session->agency_id)
				->group_by('connected_service')
				->get()->result();

			$apiTokenCounts = $this->db->select('api_id, COUNT(`agency_api_token_id`) AS jcount')
				->from('agency_api_tokens')
				->where_in('api_id', $apiIds)
				->where('agency_id', $this->session->agency_id)
				->group_by('api_id')
				->get()->result();
		}
		else {
			$apiIntegCounts = [];
			$apiTokenCounts = [];
		}

		foreach($apiIntegCounts as $apiIntegCount) {
			$agencyApisById[$apiIntegCount->connected_service]->permission_granted = $apiIntegCount->jcount > 0;
		}

		foreach($apiTokenCounts as $apiTokenCount) {
			$agencyApisById[$apiTokenCount->api_id]->connected = $apiTokenCount->jcount > 0;
		}
		$data['agency_apis'] = $agencyApis;

		//check if there is integrated api in agency
		$connectedAPI = array_filter($agencyApis, function ($item) {
			return ( $item->permission_granted === true && $item->connected === true );
		});
		$data['is_integrated'] = $connectedAPI;


		$login_user_data = $this->agency_user_account_model->get(array('agency_user_account_id' => $this->session->aua_id));
		$data['user_type'] = $login_user_data->user_type;

		$data['agency_admin_users'] = $this->agency_user_account_model
			->fields('fname,lname')
			->get_all(array('agency_id' => $this->session->agency_id, 'user_type' => 1));

		// get BSB and account number from countries db
		$country_sql = $this->db->query("
		SELECT `bsb`, `ac_number`
		FROM `countries`
		WHERE `country_id` = {$this->config->item('country')}
		");
		$data['country_row'] = $country_sql->row();
		
        $this->load->view('templates/home_header', $data);
		$this->load->view('api/connections', $data);
		$this->load->view('templates/home_footer');

	}

	public function callback_pme(){

		$uri = 'api/callback_pme';

		$authorization_code = $_GET['code'];

		if (isset($authorization_code)) {

            // get Pme tokens
			$auth_tokens_json = $this->api_model->getPmeAccessToken($authorization_code);
			//print_r($auth_tokens_json);

            $access_token = json_decode($auth_tokens_json)->access_token;
			$refresh_token = json_decode($auth_tokens_json)->refresh_token;

			$agency_id = $this->session->agency_id;
			$api_id = 1; // PMe
			$expiry = date('Y-m-d H:i:s',strtotime('+3600 seconds'));
			$today = date('Y-m-d H:i:s');

			/**
			 * Save PropertyMe API in in `agency_api_integration` table
			 * @parameter $connected_service // 1 = propertyMe id
			 */
			$this->add_agency_api_integration(1);


			// check if already connected
            $sel_query = "COUNT(`agency_api_token_id`) AS jcount";
            $api_token_params = array(
                'sel_query' => $sel_query,
				'active' => 1,
				'api_id' => $api_id,
                'agency_id' => $this->session->agency_id,
                'display_query' => 0
            );
            $api_token_sql = $this->api_model->get_agency_api_tokens($api_token_params);
			$api_token_count =  $api_token_sql->row()->jcount;


			if( $api_token_count > 0 ){ // already connected, token already exist

				$data = array(
					'access_token' => $access_token,
					'expiry' => $expiry,
                    'refresh_token' => $refresh_token,
                    'connection_date' => $today
				);

				$this->db->where('agency_id', $agency_id);
				$this->db->where('api_id', $api_id);
				$this->db->update('agency_api_tokens', $data);

			}else{

				// capture agency PMe tokens
				$data = array(
						'agency_id' => $agency_id,
						'api_id' => $api_id,
						'access_token' => $access_token,
						'expiry' => $expiry,
						'refresh_token' => $refresh_token,
                        'created' => $today,
                        'connection_date' => $today
				);

				$this->db->insert('agency_api_tokens', $data);

			}

			# insert logs
			$staff_id = $this->session->aua_id;
			$agency_id = $this->session->agency_id;

			#fetch user data
			$login_user_data = $this->agency_user_account_model->get(array('agency_user_account_id' => $staff_id));
			##insert logs
			$log_details = "PropertyMe Connected by {$login_user_data->fname} {$login_user_data->lname}";
			$log_params = [
				'title'             => 69,  // PMe API
				'details'           => $log_details,
				'display_in_vad'    => 1,
				'created_by_staff'  => $staff_id,
				'created_by'        => $staff_id,
				'agency_id'         => $agency_id
			];

			$this->logs_model->insert_log($log_params);
		}

		$this->authentication_notice_email();
		$this->session->set_flashdata('pme_api_integ_success', 1);
		//redirect("/api/connections");
		redirect("/api/select_agency_preference/?api=1");

	}

	public function select_agency_preference(){

		$api = $this->input->get_post('api');

		// get API name
		$agency_sql = $this->db->query("
		SELECT 
			`agency_api_id`,
			`api_name`
		FROM `agency_api`
		WHERE `agency_api_id` = {$api}
		");
		$data['agency_row'] = $agency_sql->row();	

		$this->load->view('templates/home_header');
		$this->load->view('api/select_agency_preference', $data);
		$this->load->view('templates/home_footer');

	}

	public function pme_save_preference(){

		$recieve_compliance = $this->input->get_post('recieve_compliance');
		$recieve_invoice = $this->input->get_post('recieve_invoice');
		$free_invoice = $this->input->get_post('free_invoice');
		$api = $this->input->get_post('api');
		$agency_id = $this->session->agency_id;

		// get API name
		$agency_sql = $this->db->query("
		SELECT `api_name`
		FROM `agency_api`
		WHERE `agency_api_id` = {$api}
		");
		$agency_row = $agency_sql->row();

		// get agency preference
		$agen_api_doc_sql = $this->db->query("
		SELECT 
			`is_invoice`,
			`is_certificate`
		FROM `agency_api_documents`
		WHERE `agency_id` = {$agency_id}
		");

		// save API invoice/cert preference
		if( $agen_api_doc_sql->num_rows() > 0 ){ // exist, update

			$update_data = array(
				'is_invoice' => $recieve_invoice,
				'is_certificate' => $recieve_compliance
			);			
			$this->db->where('agency_id', $agency_id);
			$this->db->update('agency_api_documents', $update_data);

		}else{ // new, insert

			$insert_data = array(
				'agency_id' => $agency_id,
				'is_invoice' => $recieve_invoice,
				'is_certificate' => $recieve_compliance
			);			
			$this->db->insert('agency_api_documents', $insert_data);

		}

		$log_title = 46; // Agency Update
		$recieve_invoice_log_txt = ( $recieve_invoice == 1 )?$agency_row->api_name:'Email';
		$recieve_compliance_log_txt = ( $recieve_compliance == 1 )?$agency_row->api_name:'Email';
		$free_invoice_invoice_log_txt = ( $free_invoice == 1 )?'Ignore':'Send';

		// receive 'Invoice' way		
		$details = "Preference updated to receive Invoice via {$recieve_invoice_log_txt}";
		$params_job_Log = array(
			'title' => $log_title,
			'details' => $details,
			'display_in_vad' =>1,
			'display_in_portal' => 1,
			'agency_id' => $this->session->agency_id,
			'created_by' => $this->session->aua_id
		);
		$this->jcclass->insert_log($params_job_Log);

		// receive 'Statement of Compliance' way
		$details = "Preference updated to receive Statement of Compliance via {$recieve_compliance_log_txt}";
		$params_job_Log = array(
			'title' => $log_title,
			'details' => $details,
			'display_in_vad' =>1,
			'display_in_portal' => 1,
			'agency_id' => $this->session->agency_id,
			'created_by' => $this->session->aua_id
		);
		$this->jcclass->insert_log($params_job_Log);		

		// update free invoice
		$this->db->query("
		UPDATE `agency`
		SET `exclude_free_invoices` = {$free_invoice}
		WHERE `agency_id` = {$agency_id}
		");

		// send or ignore 'Free invoice' 
		$details = "Preference updated to {$free_invoice_invoice_log_txt} $0.00 Invoices";
		$params_job_Log = array(
			'title' => $log_title,
			'details' => $details,
			'display_in_vad' =>1,
			'display_in_portal' => 1,
			'agency_id' => $this->session->agency_id,
			'created_by' => $this->session->aua_id
		);
		$this->jcclass->insert_log($params_job_Log);

		redirect("/api/connections");

	}

	public function authentication_notice_email() {

		$agency_info = $this->profile_model->get_agency($this->session->agency_id);
		$agency_name = $agency_info->agency_name;

        $html_content  = "
        <p>
            Hi Team,
        </p>
        <p>
            {$agency_name} has connected to PMe, please confirm all properties are connected, and supplier is added.
        </p>
        <p>
            Regards,<br />
            The Devs
        </p>
        ";

		$this->email->to(make_email('data'));
        $this->email->subject("Agency Connects to PMe");
        $this->email->message($html_content);
        $this->email->send();
		
	}

	public function connect_palace(){

		// load palace model
		$this->load->model('palace_model');

		$palaceUser = $this->input->get_post('palaceUser');
		$palacePass = $this->input->get_post('palacePass');
		$systemUse_dp = $this->input->get_post('palacePermi');
		$token = base64_encode($palaceUser.":".$palacePass);
		$expiry = date('Y-m-d H:i:s',strtotime('+3600 seconds'));
		$today = date('Y-m-d H:i:s');
		$agency_id = $this->session->agency_id;

		// call palace API model
		$system_use = $this->palace_model->get_api_getway($systemUse_dp);
		// sample end point, can be any end point, preferrably a call that returned small data
		$api_end_point = $system_use."/Service.svc/RestService/v2ViewAllDetailedSupplier/JSON";

		// call end point
		$pme_params = array(
			'access_token' => $token,
			'end_points' => $api_end_point
		);
		$curl_response = $this->palace_model->call_end_points_v2($pme_params);

		if( $curl_response['response_code'] == 200 ){ // OK status

			$api_id = 4; // Palace Api

			// check if already connected
			$sel_query = "COUNT(`agency_api_token_id`) AS jcount";
			$api_token_params = array(
				'sel_query' => $sel_query,
				'active' => 1,
				'api_id' => $api_id,
				'agency_id' => $this->session->agency_id,
				'display_query' => 0
			);
			$api_token_sql = $this->api_model->get_agency_api_tokens($api_token_params);
			$api_token_count =  $api_token_sql->row()->jcount;

			if( $api_token_count > 0 ){ // already connected, token already exist
				$this->db->where('api_id', $api_id);
				$this->db->where('agency_id', $this->session->agency_id);
				$data['access_token'] = $token;
				$data['expiry'] = $expiry;
				$data['system_use'] = $systemUse_dp;
				$this->db->update('agency_api_tokens',$data);
			}else {
				$data['api_id'] = $api_id;
				$data['agency_id'] = $this->session->agency_id;
				$data['access_token'] = $token;
				$data['created'] = $today;
				$data['expiry'] = $expiry;
				$data['system_use'] = $systemUse_dp;
				$data['connection_date'] = $today;
				$this->db->insert('agency_api_tokens',$data);
			}

			$result = ( $this->db->affected_rows() > 0 ) ? 1 : 0;

			if ($result) {
				// $scope = $this->config->item('PME_CLIENT_Scope');
				// $scope = explode("%20", $scope);
				$params['api_id'] = 4;
				$params['agency_id'] = $this->session->agency_id;
				$params['user_id'] = $this->session->aua_id;
				// $params['permission'] = json_encode($scope);
				// $params['permission'] = $palacePermi;
				$params['permission'] = "Standard";
				$this->add_login_logs($params);
			}

		}else if( $curl_response['response_code'] == 401 ){ // Unauthorized

			$result = 0;
			$error = 'Incorrect username or password, please try again';

		}else{ // fail

			$result = 0;
			$error = 'You must be connected to Liquid on Palace to be able to proceed, please contact Palace to update your account.';

		}


		// send in json format
		$json_arr = array(
			"result" => $result,
			"error"=> $error
		);
		echo json_encode($json_arr);

	}

	public function add_login_logs($params) {

		$data['api_id'] = $params['api_id'];
		$data['agency_id'] = $params['agency_id'];
		$data['user_id'] = $params['user_id'];
		$data['permission'] = $params['permission'];
		$data['login_date'] = date('Y-m-d H:i:s');
      	$this->db->insert('agency_api_login',$data);

	   	$result = ($this->db->affected_rows() != 1) ? false : true;
		return $result;
	}

	public function api_properties(){

		$this->load->library('pagination');
		$this->load->model('properties_model');

		$data['title'] = $this->system_model->integrated_api()." Properties";

		$uri = '/api/api_properties';
		$data['uri'] = $uri;

		$per_page = $this->config->item('pagi_per_page');
        $offset = ( $this->input->get_post('offset') != '' )?$this->input->get_post('offset'):0;
		
		$pm_filter = $this->input->get_post('pm_filter');
		$service_type_filter = $this->input->get_post('service_type_filter');  
		$service_status_filter = ( $this->input->get_post('service_status_filter') != '' )?$this->input->get_post('service_status_filter'):1;   
		$data['service_status_filter'] = $service_status_filter; 
		$search_phrase = $this->input->get_post('search_phrase');      

		
		// active (serviced to SATS) toggle
		$service_to_sats_filter_sql = null;
		$exclude_service_to_sats_prop_sql = null;
		if( $service_status_filter == 1 ){ // service to SATS

			$service_to_sats_filter_sql = " AND ps.`service` = 1 ";

		}else if( $service_status_filter == 2 ){ // not service to SATS

			// exclude properties with serviced to SATS
			$exclude_service_to_sats_prop_sql = "
			AND p.`property_id` NOT IN(

				SELECT DISTINCT(p_inner.`property_id`)
				FROM `property_services` AS ps_inner 
				LEFT JOIN `property` AS p_inner ON ps_inner.`property_id` = p_inner.`property_id`
				LEFT JOIN `agency` AS a_inner ON p_inner.`agency_id` = a_inner.`agency_id`
				WHERE a_inner.`agency_id` = {$this->session->agency_id}	
				AND ps_inner.`service` = 1 

			)
			";
			
			$service_to_sats_filter_sql = " AND ( ps.`service` != 1 OR ps.`service` IS NULL ) ";
			
		}

		// PM filter
		$pm_filter_sql_str = null;
		if( $pm_filter > 0 ){
			$pm_filter_sql_str = " AND p.`pm_id_new` = {$pm_filter} ";
		}

		// service type filter
		$service_type_filter_sql_str = null;
		if( $service_type_filter > 0 ){
			$service_type_filter_sql_str = " AND ajt.`id` = {$service_type_filter} ";
		}

		// phrase search
		$search_phrase_filter_sql_str = null;
		if( $search_phrase != '' ){
			$search_phrase_filter_sql_str = " 
			AND ( 
				CONCAT_WS(
					' ', 
					LOWER(p.`address_1`), 
					LOWER(p.`address_2`), 
					LOWER(p.`address_3`), 
					LOWER(p.`state`), 
					LOWER(p.`postcode`)
				) LIKE '%{$search_phrase}%' 
			) 
			";
		}				

		$sel_query = "
		SELECT 
			p.`property_id`, 
			p.`address_1` AS `p_address_1`,
			p.`address_2` AS `p_address_2`, 
			p.`address_3` AS `p_address_3`, 
			p.`state` AS `p_state`, 
			p.`postcode` AS `p_postcode`, 
			p.`is_nlm`, 
			p.`deleted`, 

			ps.`service` AS ps_service,

			ajt.`id` AS ajt_id,
			ajt.`type` AS ajt_type,
			ajt.`short_name` AS ajt_short_name,
			
			a.`agency_id`, 
			a.`agency_name`, 
			
			aua.`agency_user_account_id`,
			aua.`fname` AS pm_fname,
			aua.`lname` AS pm_lname,
			aua.`photo` AS pm_photo,
			
			apd.`api_prop_id`,
			
			cp.`console_prop_id`
		";

		$main_query = "
		FROM `property` AS p
		LEFT JOIN `property_services` AS ps ON p.`property_id` = ps.`property_id` 
		LEFT JOIN `alarm_job_type` AS ajt ON ps.`alarm_job_type_id` =  ajt.`id`
		LEFT JOIN `agency` AS a ON p.`agency_id` = a.`agency_id`
		LEFT JOIN `agency_user_accounts` AS aua ON p.`pm_id_new` = aua.`agency_user_account_id`
		LEFT JOIN `api_property_data` AS apd ON p.`property_id` = apd.`crm_prop_id`
		LEFT JOIN `console_properties` AS cp ON ( p.`property_id` = cp.`crm_prop_id` AND cp.`active` = 1 )
		WHERE p.`deleted` = 0
		AND (p.`is_nlm` = 0 OR p.`is_nlm` IS NULL)
		AND a.`agency_id` = {$this->session->agency_id}	
		AND (
			apd.`id` > 0 OR
			cp.`id` > 0
		)
		{$service_to_sats_filter_sql}	
		{$exclude_service_to_sats_prop_sql}
		";

		// main listing
		$data['list'] = $this->db->query("
		{$sel_query}
		{$main_query}
		{$pm_filter_sql_str}
		{$service_type_filter_sql_str}
		{$search_phrase_filter_sql_str}
		ORDER BY p.`address_2` ASC, p.`address_3` ASC          
		LIMIT {$offset}, {$per_page}     		 
		");
		//echo $this->db->last_query();

		// total rows            
		$total_rows_sql = $this->db->query("
		SELECT DISTINCT(p.`property_id`)
		{$main_query}
		{$pm_filter_sql_str}
		{$service_type_filter_sql_str}
		{$search_phrase_filter_sql_str}
		");
		$total_rows = $total_rows_sql->num_rows();   

		// PM filter
		$data['pm_filter_sql'] = $this->db->query("
		SELECT 
			DISTINCT(aua.`agency_user_account_id`), 
			aua.`fname`,
			aua.`lname`,
			aua.`photo`
		{$main_query} 
		{$service_type_filter_sql_str}
		{$search_phrase_filter_sql_str}
		AND aua.`agency_user_account_id` > 0
		ORDER BY aua.`fname` ASC, aua.`lname` ASC          
		");

		// service type filter
		$data['serv_type_filter_sql'] = $this->db->query("
		SELECT 
			DISTINCT(ajt.`id`),
			ajt.`type`
		{$main_query} 
		{$pm_filter_sql_str}
		{$search_phrase_filter_sql_str}
		AND ajt.`id` > 0
		ORDER BY ajt.`type` ASC           
		");
		//echo $this->db->last_query();

		// pagination
		$pagi_links_params_arr = array(            
			'pm_filter' => $pm_filter,
			'service_type_filter' => $service_type_filter,
			'service_status_filter' => $service_status_filter,
			'search_phrase' => $search_phrase
		);
		$pagi_link_params = "{$uri}/?".http_build_query($pagi_links_params_arr);
		$data['pagi_links_params_arr'] = $pagi_links_params_arr;

		// pagination settings
		$config['page_query_string'] = TRUE;
		$config['query_string_segment'] = 'offset';
		$config['total_rows'] = $total_rows;
		$config['per_page'] = $per_page;
		$config['base_url'] = $pagi_link_params;

		$this->pagination->initialize($config);

		$data['pagination'] = $this->pagination->create_links();

		// pagination count
		$pc_params = array(
			'total_rows' => $total_rows,
			'offset' => $offset,
			'per_page' => $per_page
		);
		$data['pagi_count'] = $this->jcclass->pagination_count($pc_params);
		
		$this->load->view('templates/home_header', $data);
		$this->load->view($uri, $data);
		$this->load->view('templates/home_footer');

	}

	/**
	 * Save PropertyMe API in in `agency_api_integration` table
	 * @parameter $connected_service // agency_api_id
	 */
	public function add_agency_api_integration($connected_service) {
		$agency_id = $this->session->agency_id;
		$staff_id = $this->session->aua_id;

		$is_integrated = $this->agency_api_integration_model->get(array('agency_id' => $agency_id, 'connected_service' => $connected_service));

		if(!$is_integrated){
			//data to be inserted
			$insert_data = array(
				'connected_service' => $connected_service,
				'agency_id' => $agency_id,
				'date_activated' => date('Y-m-d')
			);

			if ($this->api_model->add_agency_api_integration($insert_data)) {
				//get agency API name
				$api_param = [
					'sel_query' => 'api_name',
					'agency_api_id' => $connected_service
				];
	
				$api_row = $this->api_model->get_agency_api($api_param)->row_array();
				$api_name = $api_row['api_name'];
	
				//insert logs
				$log_details = "{$api_name} API integration added";
				$log_params = [
					'title'             => 85, 
					'details'           => $log_details,
					'display_in_vad'    => 1,
					'created_by_staff'  => $staff_id,
					'created_by'        => $staff_id,
					'agency_id'         => $agency_id
				];
	
				$this->logs_model->insert_log($log_params);
			}
		}
	}


	// sends palace API account request email
	public function send_palace_api_account_request(){

		$agency_id = $this->session->agency_id;
		$user_id = $this->session->aua_id;

		$info_email = make_email('info');

		if( $user_id > 0 && $agency_id ){

			// get logged agency user		
			$aua_params = array(
				'sel_query' => '				
					aua.`fname`,
					aua.`lname`,
					aua.`email`
				',
				'aua_id' => $user_id
			);
			$aua_sql = $this->user_accounts_model->get_user_accounts($aua_params);
			$aua_row = $aua_sql->row();

			// get agency details
			$params = array(
				'sel_query' => '
					a.`agency_id`,
					a.`agency_name`,
					a.`salesrep`
				',
				'join_table' => array('countries'),
				'agency_id' => $agency_id
			);
			$agency_sql = $this->agency_model->get_agency_data($params);
			$agency_row = $agency_sql->row();	
			$salesrep = $agency_row->salesrep; // get sales rep	
			
			$salesrep_email = null;
			if( $salesrep > 0 ){

				// get salesrep email			
				$sales_rep_sql = $this->db->query("
				SELECT `Email`
				FROM `staff_accounts`
				WHERE `StaffID` = {$salesrep}
				");				
				$sales_rep_row = $sales_rep_sql->row();
				$salesrep_email = $sales_rep_row->Email;

			}			

			// email settings
			$email_config = Array(
				'mailtype' => 'html',
				'charset' => 'utf-8'
			);
			$this->email->initialize($email_config);
			$this->email->clear(TRUE);
			
			$this->email->from($info_email, $_ENV['COMPANY_FULL_NAME']);
			$this->email->to('PS.PalaceAPISupport@mrisoftware.com');		

			$bcc_arr = array($aua_row->email,$info_email,$salesrep_email);
			$this->email->cc(array_filter($bcc_arr));

			$this->email->subject("API log-in for {$agency_row->agency_name}");

			// send palace account to agency user or info email
			$send_account_to_text = ( $aua_row->email != '' )?$aua_row->email:$info_email;

			$email_body = "
			<p>Hi Jermaine & Remi,</p>

			<p>
				We are writing to you on behalf of {$agency_row->agency_name} to confirm that we wish to proceed with the API connection between Palace and {$this->config->item('COMPANY_NAME_SHORT')}. 
				Can you please provide {$agency_row->agency_name} with the API login credentials so that we can complete the integration? Please send it to {$send_account_to_text}
			</p>

			<p>
				Thanks,<br />
				{$_ENV['COMPANY_FULL_NAME']}
			</p>
			";

			$this->email->message($email_body);
			$this->email->send();

		}				

	}

}
