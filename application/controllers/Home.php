<?php
class Home extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('agency_model');
		$this->load->model('jobs_model');
		$this->load->model('sms_api_model');
		$this->load->model('user_accounts_model');
		$this->load->model('profile_model');
		$this->load->model('logs_model');
		$this->load->model('properties_model');
		$this->load->model('home_model');
	}

	public function index(){

		$data['title'] = 'Home Page';

		$agency_id = $this->session->agency_id;
		$country_id = $this->session->country_id;

		// pagination
		$per_page = 15;
		$offset = 0;

		// paginated results
		$params = array(
			'sel_query' => '
				l.`log_id`,
				l.`created_date`,
				l.`title`,
				l.`details`,
				l.`created_by`,
				l.`created_by_staff`,

				aua.`fname`,
				aua.`lname`,
				aua.`photo`,

				ltit.`title_name`,

				p.`property_id`,
				p.`address_1` AS p_address_1,
				p.`address_2` AS p_address_2,
				p.`address_3` AS p_address_3,
				p.`state` AS p_state,
				p.`postcode`
			',
			'deleted' => 0,
			'agency_id' => $agency_id,
			'display_in_portal' => 1,
			'limit' => $per_page,
			'offset' => $offset,
			'display_query' => 0,
			'joins' => [
				[
					'table' => 'property AS p',
					'condition' => 'p.property_id = l.property_id',
					'type' => 'left',
				],
			],
		);
		$query = $this->logs_model->get_logs($params);
		$logs = $query->result();

		$logsById = [];
		$pattern = "/agency_user:\d+/";
		$taggedAgencyUserIds = [];
		for ($x = 0; $x < count($logs); $x++) {
			$log =& $logs[$x];

			$matches = [];
			if( preg_match($pattern, $log->details, $matches) == 1 ){
				$agencyUserId = explode(':', $matches[0])[1];

				$taggedAgencyUserIds[] = $agencyUserId;

				$log->taggedAgencyUserId = $agencyUserId;
			}

			$logsById[$log->log_id] =& $log;
		}

		if (!empty($taggedAgencyUserIds)) {
			$taggedAgencyUserIds = array_unique($taggedAgencyUserIds);

			$taggedAgencyUsers = $this->db->select("
				aua.`agency_user_account_id`,
				aua.`fname`,
				aua.`lname`
			")
				->from('agency_user_accounts AS aua')
				->where_in('agency_user_account_id', $taggedAgencyUserIds)
				->get()->result();

			foreach ($logs as &$log) {
				if (isset($log->taggedAgencyUserId)) {
					foreach ($taggedAgencyUsers as $taggedAgencyUser) {
						if ($log->taggedAgencyUserId == $taggedAgencyUser->agency_user_account_id) {
							$log->taggedAgencyUser = $taggedAgencyUser;
							break;
						}
					}
				}
			}
		}

		$data['recent_activity'] = $logs;
		$data['user_photo_upload_path'] = '/uploads/user_accounts/photo';
		$data['default_avatar'] = '/images/avatar-2-64.png';

		//get escalate total
		$data['esc_jobs_num'] = $this->jcclass->get_escalate_jobs();

		//get approve quotes total
		$data['get_qld_upgrade_quotes_total'] = $this->jcclass->get_qld_upgrade_quotes_total();

		// get completed jobs
		$sql_query = $this->home_model->get_completed_jobs();
		$row = $sql_query->row();
		$data['comp_jobs'] = $row->jcount;

		// get booked jobs
		$sql_query = $this->home_model->get_booked_jobs();
		$row = $sql_query->row();
		$data['booked_jobs'] = $row->jcount;

		// get non fully paid jobs
		$sql_query = $this->home_model->get_job_feedback();
		$row = $sql_query->row();
		$data['job_feedback'] = $row->jcount;

		// total unpaid balance box <= 30 days
		$tot_unpaid_params_having = "DateDiff <= 30";
		$tot_unpaid_params = array(
			'having' => $tot_unpaid_params_having
		);
		$data['tot_invoice_bal_not_overdue'] = $this->jobs_model->getTotalUnpaidAmount($tot_unpaid_params);

		//total overdue invoice balance box >= 31 days
		$tot_overdue_params_having = "DateDiff >= 31";
		$tot_overdue_params = array(
			'having' => $tot_overdue_params_having
		);
		$data['tot_invoice_bal_overdue'] = $this->jobs_model->getTotalUnpaidAmount($tot_overdue_params);


		// upgraded prop
		$sel_query = '
			DISTINCT(p.`property_id`)
		';
		// number of IC alarm on techsheet
		$custom_where = "( p.`prop_upgraded_to_ic_sa` = 1 AND p.`qld_new_leg_alarm_num` = 0 )";

		$query_params = array(
			'sel_query' => $sel_query,
			'p_deleted' => 0,
			'agency_id' => $agency_id,
			'ps_service' => 1,
			'custom_where' => $custom_where,
			'display_query' => 0
		);
		$ps_sql = $this->properties_model->get_property_services($query_params);
		$data['upgraded_prop'] = $ps_sql->num_rows();


		## QLD Upgrage Quotes Count (PENDING ONLY) > Gherx
	/*	$sql_str = "
			SELECT COUNT( j.`id` ) AS jcount
			FROM jobs AS j
			INNER JOIN (

				SELECT j4.property_id, MAX(j4.date) AS latest_date
				FROM jobs AS j4
				LEFT JOIN property AS p2 ON j4.property_id = p2.property_id
				LEFT JOIN property_services AS ps2 ON p2.property_id = ps2.property_id
				LEFT JOIN agency AS a2 ON p2.agency_id = a2.agency_id
				WHERE j4.del_job = 0
				AND j4.status = 'Completed'
				AND a2.country_id = {$country_id}
				AND p2.deleted = 0
				AND a2.status = 'active'
				AND a2.agency_id = {$agency_id}
				AND p2.qld_new_leg_alarm_num >0 AND (p2.prop_upgraded_to_ic_sa = 0 OR p2.prop_upgraded_to_ic_sa IS NULL)
				AND (j4.assigned_tech !=1 AND j4.assigned_tech !=2)
				AND ps2.service = 1
				GROUP BY j4.property_id DESC

			) AS j3 ON ( j.property_id = j3.property_id AND j.date = j3.latest_date )
			LEFT JOIN `alarm_job_type` AS `ajt` ON j.`service` = ajt.`id`
			LEFT JOIN property AS p ON j.property_id = p.property_id
			LEFT JOIN `agency_user_accounts` AS `aua` ON p.`pm_id_new` = aua.`agency_user_account_id`
			LEFT JOIN agency AS a ON p.agency_id = a.agency_id
			WHERE j.del_job = 0
			AND j.status = 'Completed'
			AND a.country_id = {$country_id}
			AND p.deleted = 0
			AND a.status = 'active'
			AND a.agency_id = {$agency_id}
			AND p.qld_new_leg_alarm_num >0 AND (p.prop_upgraded_to_ic_sa = 0 OR p.prop_upgraded_to_ic_sa IS NULL)
			AND (j.assigned_tech !=1 AND j.assigned_tech !=2)
			AND j.date > '".date('Y-m-d', strtotime('-1095 days'))."'
			AND p.qld_upgrade_quote_approved_ts IS NULL
			";
			*/
			$sql_str = "
			SELECT COUNT( j.`id` ) AS jcount
			FROM jobs AS j
			INNER JOIN (

				SELECT j4.property_id, MAX(j4.date) AS latest_date, j4.id
				FROM jobs AS j4
				LEFT JOIN property AS p2 ON j4.property_id = p2.property_id
				LEFT JOIN property_services AS ps2 ON p2.property_id = ps2.property_id
				LEFT JOIN agency AS a2 ON p2.agency_id = a2.agency_id
				WHERE j4.del_job = 0
				AND ( j4.status = 'Completed' OR (j4.job_type = 'IC Upgrade' AND p2.qld_upgrade_quote_approved_ts IS NOT NULL) )
				AND a2.country_id = {$country_id}
				AND p2.deleted = 0
				AND a2.status = 'active'
				AND a2.agency_id = {$agency_id}
				AND p2.qld_new_leg_alarm_num >0 AND (p2.prop_upgraded_to_ic_sa = 0 OR p2.prop_upgraded_to_ic_sa IS NULL)
				AND ( (j4.assigned_tech !=1 AND j4.assigned_tech !=2) OR j4.assigned_tech IS NULL )  
				AND ps2.service = 1
				GROUP BY j4.property_id

			) AS j3 ON ( j.property_id = j3.property_id AND j.id = j3.id )
			LEFT JOIN `alarm_job_type` AS `ajt` ON j.`service` = ajt.`id`
			LEFT JOIN property AS p ON j.property_id = p.property_id
			LEFT JOIN `agency_user_accounts` AS `aua` ON p.`pm_id_new` = aua.`agency_user_account_id`
			LEFT JOIN agency AS a ON p.agency_id = a.agency_id
			WHERE j.del_job = 0
			AND ( j.status = 'Completed' 	OR (j.job_type = 'IC Upgrade' AND p.qld_upgrade_quote_approved_ts IS NOT NULL) )
			AND a.country_id = {$country_id}
			AND p.deleted = 0
			AND ( p.`is_nlm` = 0 OR p.`is_nlm` IS NULL )
			AND a.status = 'active'
			AND a.agency_id = {$agency_id}
			AND p.qld_new_leg_alarm_num >0 AND (p.prop_upgraded_to_ic_sa = 0 OR p.prop_upgraded_to_ic_sa IS NULL)
			AND ( (j.assigned_tech !=1 AND j.assigned_tech !=2) OR j.assigned_tech IS NULL )
			AND ( j.date > '".date('Y-m-d', strtotime('-1095 days'))."' OR (j.date IS NULL AND j.job_type = 'IC Upgrade' ) )
			AND `p`.`qld_upgrade_quote_approved_ts` IS NULL
			";
			$jobs_tot_sql = $this->db->query($sql_str);
			$data['qld_upgrade_quotes_count'] = $jobs_tot_sql->row()->jcount;
		## QLD Upgrage Quotes Count (PENDING ONLY) End


		// compliance
		$sel_query = "DISTINCT(p.`property_id`)";
		$custom_where_pm = "ps.`alarm_job_type_id` > 0";

		$query_params = array(
			'sel_query' => $sel_query,
			'custom_where' => $custom_where_pm,
			'ps_service' => 1,
			'p_deleted' => 0,
			'agency_id' => $agency_id,
			'display_query' => 0
		);
		$comp_sql = $this->properties_model->get_property_services($query_params);
		$data['compliance'] = $comp_sql->num_rows();

		/*
		// non compliance
		$custom_where_pm = "ps.`alarm_job_type_id` > 0 AND ps.`service` != 1";
		$query_params = array(
			'sel_query' => $sel_query,
			'custom_where' => $custom_where_pm,
			'p_deleted' => 0,
			'agency_id' => $agency_id,
			'display_query' => 0
		);
		$non_comp_sql = $this->properties_model->get_property_services($query_params);
		$data['non_compliance'] = $non_comp_sql->num_rows();
		*/

		// get non complicance
		$non_complicance_params = array(
			'sel_query'=> "j1.id as j_id",
		);
		$tt_no_compliant_q = $this->properties_model->get_no_compliant_prop_for_properties_page($non_complicance_params);
		$data['non_compliance_count'] = $tt_no_compliant_q->num_rows();


		// total portfolio
		$agency_info = $this->profile_model->get_agency($agency_id);
		$data['tot_porfolio'] = $agency_info->tot_properties;



		// pending jobs
		$sel_query = "COUNT(j.`id`) AS jcount";

		$params = array(
			'sel_query' => $sel_query,
			'p_deleted' => 0,
			'a_status' => 'active',
			'del_job' => 0,
			'agency_id' => $agency_id,
			'j_status' => 'Pending',
			'country_id' => $country_id,

			'sort_list' => array(
				array(
					'order_by' => 'aua.`fname`',
					'sort' => 'ASC'
				),
				array(
					'order_by' => 'aua.`lname`',
					'sort' => 'ASC'
				)
			),
			'display_query' => 0
		);
		$pendings_sql = $this->jobs_model->get_jobs($params);
		$pending_row = $pendings_sql->row();
		$data['pending_jobs'] = $pending_row->jcount;



		// our team
		$params = array(
			'sel_query' => '
				aua.`agency_user_account_id`,
				aua.`fname`,
				aua.`lname`,
				aua.`email`,
				auat.`user_type_name`,
				aua.`photo`,
				aua.`job_title`
			',
			'agency_id' => $agency_id,
			'limit' => $per_page,
			'offset' => $offset,
			'display_query' => 0,
			'active' => 1
		);
		$sql_query = $this->user_accounts_model->get_user_accounts($params);
		$data['our_team'] = $sql_query->result();

		// noticeboard
		$nb_sql = $this->agency_model->get_noticeboard();
		$nb_row = $nb_sql->row();
		$data['noticeboard'] = $nb_row->notice;

		// state
		$a_params = array(
			'sel_query' => 'a.`state`, a.`auto_renew`',
			'agency_id' => $agency_id
		);
		$a_sql = $this->agency_model->get_agency_data($a_params);
		$agency = $a_sql->row();
		$data['a_state'] = $agency->state;
		$data['a_auto_renew'] = $agency->auto_renew;

		// welcome message
		$params = array(
			'sel_query' => '
				aua.`hide_welcome_msg`
			',
			'aua_id' => $this->session->aua_id
		);
		$user_sql = $this->user_accounts_model->get_user_accounts($params);
		$user_row = $user_sql->row();
		$data['hide_welcome_msg'] = $user_row->hide_welcome_msg;

		$data['get_tot_prop_timestamp']  = $this->db->select('tot_prop_timestamp')->from('agency')->where('agency_id',$this->session->agency_id)->get()->row();

		$data['check_agency_accounts_reports_preference'] = $this->gherxlib->check_agency_accounts_reports_preference();

		// get logged agency user		
		$aua_params = array(
			'sel_query' => '				
				aua.`fname`,
				aua.`lname`
			',
			'aua_id' => $this->session->aua_id
		);
		$aua_sql = $this->user_accounts_model->get_user_accounts($aua_params);
		$data['aua_row'] = $aua_sql->row();


		// 2FA
		$today = date('Y-m-d');
		$next_3_months = date('Y-m-01',strtotime("+3 months"));
		$offer_2fa = false;

		// for testing future dates
		//$today = '2023-03-01';
		//$next_3_months = date('Y-m-01',strtotime("{$today} +3 months"));

		// get 2FA data for user
		$user_2fa_sql = $this->db->query("
		SELECT *
		FROM `agency_user_2fa`
		WHERE `user_id` = {$this->session->aua_id}
		");				

		if( $user_2fa_sql->num_rows() > 0 ){ // exist, update

			$user_2fa_row = $user_2fa_sql->row();

			// if user has no active 2FA, offer it
			if( $user_2fa_row->active != 1 ){ 
			
				if( $user_2fa_row->offer_2fa_date != '' ){ // 2FA offer date is not empty
					
					if( $today == $user_2fa_row->offer_2fa_date ){ 

						$offer_2fa = true; // display 2FA popup

						// offer 2FA again in the next 3 months
						$update_data = array(
							'offer_2fa_date' => $next_3_months
						);
						
						$this->db->where('id', $user_2fa_row->id);
						$this->db->update('agency_user_2fa', $update_data);

					}

				}else{  // 2FA offer date is empty
					
					$offer_2fa = true; // display 2FA popup

					// offer 2FA again in the next 3 months
					$update_data = array(
						'offer_2fa_date' => $next_3_months
					);
					
					$this->db->where('id', $user_2fa_row->id);
					$this->db->update('agency_user_2fa', $update_data);
					
				}
				
			}

		}else{ // new, insert

			$offer_2fa = true; // display 2FA popup

			// offer 2FA again in the next 3 months
			$insert_data = array(
					'offer_2fa_date' => $next_3_months,
					'user_id' => $this->session->aua_id
			);			
			$this->db->insert('agency_user_2fa', $insert_data);

		}
		
		$data['offer_2fa'] = $offer_2fa;

		// Disable Due For service and Auto Renew
		$renewal_agency = $this->db->query(
			sprintf("
					select * from agency_preference_selected 
					where agency_pref_id = 25 AND agency_id = %s", $agency_id
			)
		)->row();
		$data['renewal_agency_status'] = ( is_null($renewal_agency) || (int)$renewal_agency->sel_pref_val === 1 && (int)$renewal_agency->agency_pref_id === 25) ? 1 : 0;

		
		// check if connected to PropertyTree
		$pt_sql = $this->db->query("
		SELECT `agency_api_token_id` AS aat_count
		FROM `agency_api_tokens`
		WHERE `agency_id` = {$agency_id}
		AND `api_id` = 3
		AND `active` = 1
		");
		$data['pt_has_tokens'] = ( $pt_sql->row()->aat_count > 0 )?true:false;

		// check if full connected
		$pt_agen_pref_sql = $this->db->query("
		SELECT COUNT(`pt_ap_id`) AS pt_ap_count
		FROM `propertytree_agency_preference`
		WHERE `agency_id` = {$agency_id}
		AND `active` = 1
		");
		$data['pt_has_preference'] = ( $pt_agen_pref_sql->row()->pt_ap_count > 0 )?true:false;

		// get PropertyTree popup settings
		$pt_popup_sql = $this->db->query("
		SELECT *
		FROM `agency_portal_popup_settings`
		WHERE `agency_id` = {$agency_id}
		");
		$data['has_set_pt_popup_settings'] = ( $pt_popup_sql->num_rows() > 0 )?true:false;
		$data['pt_popup_row'] = $pt_popup_sql->row();
		
		// check if agency is connected to console
		$console_sql = $this->db->query("
		SELECT COUNT(`id`) AS console_count
		FROM `console_api_keys`
		WHERE `agency_id` = {$agency_id}
		AND `active` = 1
		");
		
		$data['connected_to_console_api'] = ( $console_sql->row()->console_count > 0 )?true:false;
        
        // autocomplete for tags
        $data['address_tags'] = $this->properties_model->get_address();
		
		$this->load->view('templates/home_header', $data);
		$this->load->view('home/index', $data);
		$this->load->view('templates/home_footer', $data);

	}

	// get outstanding invoice jobs
	public function get_non_fully_paid_jobs(){

		$agency_id = $this->session->agency_id;

		$sel_query = '
			COUNT(j.`id`) AS jcount
		';
		$query_params = array(
			'sel_query' => $sel_query,
			'del_job' => 0,
			'p_deleted' => 0,
			'a_status' => 'active',
			'custom_where' => 'j.`invoice_balance` > 0',
			'agency_id' => $agency_id,
			'display_query' => 0
		);
		return $this->jobs_model->get_jobs($query_params);

	}

	public function update_total_portfolio(){

		$tot_porfolio = $this->input->post('tot_porfolio');

		// get agency details
		$params = array(
			'sel_query' => 'a.`tot_properties`',
			'agency_id' => $this->session->agency_id
		);
		$agency_sql = $this->agency_model->get_agency_data($params);
		$agency = $agency_sql->row();

		// update
		$data = array(
				'tot_properties' => $tot_porfolio,
				'tot_prop_timestamp' => date('Y-m-d H:i:s')
		);

		$this->db->where('agency_id', $this->session->agency_id);
		$this->db->update('agency', $data);


		$title = 14; // Agency Profile Update
		$details = "Total Properties Managed updated from {$agency->tot_properties} to {$tot_porfolio}";
		$log_type = 3; // Agency

		$params = array(
			'title' => $title,
			'details' => $details,
			'display_in_portal' => 1,
			'display_in_vad' => 1,
			'agency_id' => $this->session->agency_id,
			'created_by' => $this->session->aua_id
		);

		$this->jcclass->insert_log($params);


	}


	public function initial_setup(){

		$data['title'] = 'Initial Setup';

		$agency_id = $this->session->agency_id;

		// get PM's
		$params = array(
			'sel_query' => '
				aua.`agency_user_account_id`,
				aua.`fname`,
				aua.`lname`,
				aua.`email`,
				auat.`user_type_name`,
				aua.`photo`,
				aua.`active`
			',
			'agency_id' => $agency_id,
			'active' => 1,
			'user_type' => 2
		);


		$query = $this->user_accounts_model->get_user_accounts($params);
		$data['users'] = $query->result();

		// get agency
		$params = array(
			'sel_query' => '
				a.`agency_name`,
				a.`login_id`
			',
			'agency_id' => $agency_id
		);
		$agency_sql = $this->agency_model->get_agency_data($params);
		$data['agency_info'] = $agency_sql->row();

		// get user types
		$data['user_types'] = $this->user_accounts_model->get_all_user_types();

		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		//$this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_check_if_email_exist');

		// SUCCESS
		if ( $this->form_validation->run() == true ){

			$email = $this->input->post('email');
			$pm_id_arr = $this->input->post('pm_id');
			$fname = $this->input->post('fname');
			$lname = $this->input->post('lname');
			$job_title = $this->input->post('job_title');
			$phone = $this->input->post('phone');

			$delete_admin = $this->input->post('delete_admin');
			$selected_pm = $this->input->post('selected_pm');


			if( $delete_admin == 1 ){ // selects PM email

				// get admin password
				$admin_params = array(
					'sel_query' => '
						aua.`password`,
						aua.`fname`,
						aua.`lname`
					',
					'aua_id' => $this->session->aua_id
				);
				$admin_sql = $this->user_accounts_model->get_user_accounts($admin_params);
				$admin_row = $admin_sql->row();

				$admin_password = $admin_row->password;
				$admin_fname = $admin_row->fname;
				$admin_lname = $admin_row->lname;

				// insert password from admin to PM, and make it admin
				$update_pm_params = array(
					'password' => $admin_password,
					'user_type' => 1
				);
				$this->db->where('agency_user_account_id', $selected_pm);
				$this->db->update('agency_user_accounts', $update_pm_params);


				// deactivate admin
				$deac_admin_params = array(
					'active' => 0,
					'email' => null
				);
				$this->db->where('agency_user_account_id', $this->session->aua_id);
				$this->db->update('agency_user_accounts', $deac_admin_params);

				$sender_id = $selected_pm;


			}else{ // normal login

				$admin_fname = $fname;
				$admin_lname = $lname;

				// update email
				$update_data = array(
					'email' => $email,
					'fname' => $fname,
					'lname' => $lname,
					'job_title' => $job_title,
					'phone' => $phone
				);
				$this->db->where('agency_user_account_id', $this->session->aua_id);
				$this->db->update('agency_user_accounts', $update_data);

				$sender_id = $this->session->aua_id;

			}

			// PM's
			if( isset($pm_id_arr) ){

				// send reset password email to PM's
				// !!!!LOOPED NOT USED
				foreach( $pm_id_arr as $pm_id ){

					$set_pass_email_data = array(
						'sender_id' => $sender_id,
						'invited_id' => $pm_id,
						'admin_full_name' => "{$admin_fname} {$admin_lname}"
					);
					$this->user_accounts_model->send_invite_set_password_email($set_pass_email_data);

				}

			}


			// update email
			$update_data = array(
				'initial_setup_done' => 1
			);
			$this->db->where('agency_id', $agency_id);
			$this->db->update('agency', $update_data);

			$this->session->set_flashdata('initial_setup_success', 1);

			if( $delete_admin == 1 ){
				$this->session->set_flashdata('initial_setup_relogin', 1);
				redirect("/");
			}else{
				redirect("/home");
			}




		}else{

			/*
			$this->load->view('templates/home_header', $data);
			$this->load->view('home/initial_setup', $data);
			$this->load->view('templates/home_footer');
			*/

		}



	}


	public function check_if_email_exist($email){
		if ( $this->user_accounts_model->check_if_email_exist($email) == true ){
			$this->form_validation->set_message('check_if_email_exist', '{field} already exist');
			return false;
		}else{
			return true;
		}
	}

	public function is_email_exist_ajax(){

		$email = $this->input->post('email');

		if ( $this->user_accounts_model->check_if_email_exist($email) == true ){
			echo 1;
		}else{
			echo 0;
		}
	}

	public function email_check_json(){

		$email = $this->input->post('email');
		echo $this->user_accounts_model->check_if_email_exist_in_agency_json($email);

	}

	public function audit_properties(){

		$this->load->library('email');

		$data['status'] = false;

		$agency_info = $this->properties_model->get_agency_info($this->session->agency_id);
		$email_data['agency_name'] = $agency_info->agency_name;
		$email_data['user'] = $this->gherxlib->agent_full_name();

		$this->email->to(make_email('sales'));
		$this->email->subject('Audit Request');
		$body = $this->load->view('emails/audit_portfolio', $email_data, TRUE);
		$this->email->message($body);
		if($this->email->send()){

			//inser log
			$details = "Request Audit Service for Medium to High Risk";
			$params = array(
				'title' => 26,  //audit properties
				'details' => $details,
				'display_in_vad' => 1,
				'display_in_portal' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id,
			);
			$log = $this->jcclass->insert_log($params);

			$data['status'] = true;
		}

		echo json_encode($data);

	}

	public function pt_popup_snooze(){

		$agency_id = $this->input->get_post('agency_id');
		$popup_id = $this->input->get_post('popup_id');
		$snooze_days = $this->input->get_post('snooze_days');
		$snooze_days_ts = date("Y-m-d",strtotime("+{$snooze_days} days"));

		// check if popup data already exist
		$sql = $this->db->query("
		SELECT COUNT(agen_por_pu_id) AS agen_por_pu_count
		FROM `agency_portal_popup_settings`
		WHERE `agency_id` = {$agency_id}
		AND `popup_id` = '{$popup_id}'
		");

		if( $agency_id > 0 && $popup_id != '' ){

			if( $sql->row()->agen_por_pu_count ){ // exist, update

				// update
				$update_data = array(
					'show_again_in' => $snooze_days_ts
				);
	
				$this->db->where('agency_id', $agency_id);
				$this->db->where('popup_id', $popup_id);
				$this->db->update('agency_portal_popup_settings', $update_data);
	
			}else{ // new, insert

				$insert_data = array(
					'show_again_in' => $snooze_days_ts,
					'agency_id' => $agency_id,
					'popup_id' => $popup_id
				);
				
				$this->db->insert('agency_portal_popup_settings', $insert_data);
	
			}

		}		

	}

}
