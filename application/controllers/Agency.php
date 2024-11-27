<?php
class Agency extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('pagination');
		$this->load->library('form_validation');

		$this->load->model('agency_model');
		$this->load->model('profile_model');
		$this->load->model('properties_model');
		$this->load->model('user_accounts_model');
		$this->load->model('states_def_model');
		$this->load->model('postcode_model');
		$this->load->model('logs_model');
		$this->load->model('agency_maintenance_model');
		$this->load->model('maintenance_model');
	}

	public function index($agency_id){

	}

	public function activity($aua_id=null){

		//$data['title'] =  ( $aua_id>0 )?'Your Account Logins':'User Logins';
		$data['title'] =  'Activity Logs';

		// pagination
		$per_page = 50;
		if( $aua_id > 0 ){
			$uri_offset = 4;
		}else{
			$uri_offset = 3;
		}
		$config['uri_segment'] = $uri_offset;
		$offset = $this->uri->segment($uri_offset, 0);

		// paginated results
		$params = array(
			'sel_query' => '
				ac.`date_created` AS date_created,
				ac.`title`,
				ac.`details`,
				aua.`fname`,
				aua.`lname`,
				aua.`photo`
			',
			'limit' => $per_page,
			'offset' => $offset,
			'aua_id' => $aua_id
		);
		$query = $this->agency_model->get_agency_activity($params);
		$data['users'] = $query->result();

		$view_uri = '/agency/activity';

		// all rows
		$params = array(
			'sel_query' => '
				ac.`agency_activity_id`
			',
			'aua_id' => $aua_id
		);
		$query = $this->agency_model->get_agency_activity($params);
		$config['total_rows'] = $query->num_rows();
		$config['per_page'] = $per_page;
		$config['base_url'] = $view_uri;
		$data['user_photo_upload_path'] = '/uploads/user_accounts/photo';
		$data['default_avatar'] = '/images/avatar-2-64.png';

		$this->pagination->initialize($config);

		$data['pagination'] = $this->pagination->create_links();

		$this->load->view('templates/home_header',$data);
        $this->load->view($view_uri,$data);
		$this->load->view('templates/home_footer');
	}

	public function profile(){
		$data['title'] = 'Agency Profile';
		// photo patch
		$data['user_photo_path'] = '/uploads/user_accounts/photo/';
		$data['default_photo'] = "/images/avatar-2-64.png";

		$data['user'] = $this->user_accounts_model->get_user_account_via_id($this->session->aua_id);

		// get agency details
		$params = array(
			'sel_query' => '
				a.`address_1`,
				a.`address_2`,
				a.`address_3`,
				a.`state`,
				a.`postcode`,
				a.`phone`,
				a.`tot_properties`,
				a.`tot_prop_timestamp`,
				a.`account_emails`,
				a.`agency_emails`,
				a.`trust_account_software`,
				a.`contact_first_name`,
				a.`contact_last_name`,
				a.`contact_phone`,
				a.`contact_email`,
				a.`accounts_name`,
				a.`accounts_phone`,
				a.`country_id`,
				a.`allow_indiv_pm`,
				a.`postcode_region_id`,
				a.`agency_hours`,
				a.`website`,
				a.`abn`,
				a.`display_bpay`,
				a.`send_en_to_agency`,
				a.`send_48_hr_key`,
				a.`tenant_details_contact_name`,
				a.`tenant_details_contact_phone`,

				sa.`FirstName` as saFirstname,
				sa.`LastName` as saLastname,
				sa.`Email` as saEmail,
				sa.`ContactNumber` as saContactNumber,
				sa.`profile_pic` as saProfilePic,

				am.`maintenance_id`,

				m.`name` as mName,

				tsa.`tsa_name`,

				sr.subregion_name as postcode_region_name
			',
			'agency_id' => $this->session->agency_id
		);
		$agency_sql = $this->agency_model->get_agency_data($params);
		$data['agency_info'] = $agency_sql->row();

		// add complete address to objects
		$data['agency_info']->complete_address = "{$data['agency_info']->address_1} {$data['agency_info']->address_2} {$data['agency_info']->address_3} {$data['agency_info']->state} {$data['agency_info']->postcode}";

		$data['agency_name']  =  $this->profile_model->get_agent_name_by_id($this->session->agency_id);
		$data['propeties_pm'] = $this->properties_model->get_property_manager_by_agency($this->session->agency_id);

		$data['country'] = $this->jcclass->get_country_data()->row();

		// Get country states list
		$data['country_states'] = $this->states_def_model->as_array()->get_all(array('country_id' => $this->config->item('country')));

		// Get maintenance list from db
		$data['maintenance'] = $this->maintenance_model->get_all_by_status_ordered_by_name();

		//Get Selected maintenance data
		$data['agency_maintenance'] = $this->agency_maintenance_model->get(array('agency_id' => $this->session->agency_id));
		// echo "<pre/>"; var_dump(count($data['agency_maintenance']));exit;

		// get agency API invoice/cert send preference
		$ageny_pref_sql = $this->db->query("
		SELECT 
			agen_api.`api_name`,

			agen_api_doc.`is_invoice`,
			agen_api_doc.`is_certificate`
		FROM `agency` AS a
		LEFT JOIN `agency_api_integration` AS agen_api_integ ON a.`agency_id` = agen_api_integ.`agency_id`
		LEFT JOIN `agency_api` AS agen_api ON agen_api_integ.`connected_service` = agen_api.`agency_api_id`
		LEFT JOIN `agency_api_documents` AS agen_api_doc ON a.`agency_id` = agen_api_doc.`agency_id`
		WHERE a.`agency_id` = {$this->session->agency_id}
		");

		$data['ageny_pref_row'] = $ageny_pref_sql->row();		

		$this->load->view('templates/home_header', $data);
		$this->load->view('agency/profile', $data);
		$this->load->view('templates/home_footer');
	}

	//delete property manager inline
	public function delete_property_manager(){

		$hid_pm_id = $this->security->xss_clean($this->input->post('hid_pm_id'));

		$res = $this->user_accounts_model->delete_user_account($hid_pm_id);
		$msg['success'] = false;
		if($res){
			$msg['success'] = true;
		}
		echo json_encode($msg);

	}

	public function update_profile(){


		$tot_prop =  $this->input->post('tot_prop');
		$agency_email =  $this->input->post('agency_email');
		$acc_email =  $this->input->post('acc_email');
		$tsa =  $this->input->post('tsa');

		$postData = [];


		// get agency details
		$params = array(
			'sel_query' => '
				a.`tot_properties`,
				a.`trust_account_software`,
				a.`account_emails`,
				a.`agency_emails`,
				a.`contact_first_name`,
				a.`contact_last_name`,
				a.`contact_phone`,
				a.`contact_email`,
				a.`accounts_name`,
				a.`accounts_phone`
			',
			'agency_id' => $this->session->agency_id
		);
		$agency_sql = $this->agency_model->get_agency_data($params);

		$agency = $agency_sql->row();



		// Agency Emails:
		if( $agency_email != $agency->agency_emails ){

			// agency emails
			$agency_emails_orig_exp = explode("\n",trim($agency->agency_emails));
			$agency_emails_orig = implode(", ",$agency_emails_orig_exp);

			// agency emails post
			$agency_emails_post_exp = explode("\n",trim($agency_email));
			$agency_emails_post = implode(", ",$agency_emails_post_exp);

			// compare email difference via array
			$email_diff = array_diff($agency_emails_post_exp, $agency_emails_orig_exp);

			if( count($email_diff) > 0 ){

				$title = 14; // Agency Profile Update
				$details = "Agency Emails updated from {$agency_emails_orig} to {$agency_emails_post}";
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

		}


		// Account Emails:
		if( $acc_email != $agency->account_emails ){

			// account emails
			$acount_emails_orig_exp = explode("\n",trim($agency->account_emails));
			$acount_emails_orig = implode(", ",$acount_emails_orig_exp);

			// account emails post
			$acount_emails_post_exp = explode("\n",trim($acc_email));
			$acount_emails_post = implode(", ",$acount_emails_post_exp);

			// compare email difference via array
			$email_diff = array_diff($acount_emails_orig_exp, $acount_emails_post_exp);

			if( count($email_diff) > 0 ){

				$title = 14; // Agency Profile Update
				$details = "Account Emails updated from {$acount_emails_orig} to {$acount_emails_post}";
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

		}

		// Trust Account Software
		if( $tsa != $agency->trust_account_software ){

			// from
			$params = array('tsa_id' => $agency->trust_account_software);
			$tsa_from_sql = $this->agency_model->get_trust_account_software($params);
			$tsa_from = $tsa_from_sql->row();

			// to
			$params = array('tsa_id' => $tsa);
			$tsa_to_sql = $this->agency_model->get_trust_account_software($params);
			$tsa_to = $tsa_to_sql->row();

			$title = 14; // Agency Profile Update
			$details = "Trust Account Software updated from {$tsa_from->tsa_name} to {$tsa_to->tsa_name}";
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


		// post val
		$postData = array(
			'tot_properties' => $tot_prop,
			'agency_emails' =>	 $agency_email,
			'account_emails' => $acc_email,
			'trust_account_software' => $tsa
		);

		// total properties managed
		if( $tot_prop != $agency->tot_properties ){


			$postData['tot_prop_timestamp'] = date('Y-m-d H:i:s');

			$title = 14; // Agency Profile Update
			$details = "Total Properties Managed updated from {$agency->tot_properties} to {$tot_prop}";
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

		$postData = $this->security->xss_clean($postData);
		$result = $this->profile_model->update_profile($this->session->agency_id, $postData);
		if($result){
			echo 1;
			$this->session->set_flashdata('agency_profile_update_success', 1);
		}else{
			echo 0;
		}




	}


	public function update_main_agency_contact(){

		$contact_first_name =  $this->input->post('ac_fname');
		$contact_last_name =  $this->input->post('ac_lname');
		$contact_phone =  $this->input->post('ac_contact');
		$contact_email =  $this->input->post('ac_email');

		// get agency details
		$params = array(
			'sel_query' => '
				a.`contact_first_name`,
				a.`contact_last_name`,
				a.`contact_phone`,
				a.`contact_email`
			',
			'agency_id' => $this->session->agency_id
		);
		$agency_sql = $this->agency_model->get_agency_data($params);
		$agency = $agency_sql->row();


		// main contact
		// first name
		if( $contact_first_name != $agency->contact_first_name ){

			$title = 14; // Agency Profile Update
			$details = "Agency Contact First Name updated from {$agency->contact_first_name} to {$contact_first_name}";
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

		// last name
		if( $contact_last_name != $agency->contact_last_name ){

			$title = 14; // Agency Profile Update
			$details = "Agency Contact Last Name updated from {$agency->contact_last_name} to {$contact_last_name}";
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

		// Phone
		if( $contact_phone != $agency->contact_phone ){

			$title = 14; // Agency Profile Update
			$details = "Agency Contact Phone updated from {$agency->contact_phone} to {$contact_phone}";
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

		// Email
		if( $contact_email != $agency->contact_email ){

			$title = 14; // Agency Profile Update
			$details = "Agency Contact Email updated from {$agency->contact_email} to {$contact_email}";
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

		$data = array(
				'contact_first_name' => $contact_first_name,
				'contact_last_name' => $contact_last_name,
				'contact_phone' => $contact_phone,
				'contact_email' => $contact_email
		);

		$this->db->where('agency_id', $this->session->agency_id);
		$this->db->update('agency', $data);

		if($this->db->affected_rows() > 0){
			// success
			echo 1;
			$this->session->set_flashdata('agency_profile_update_success', 1);
		}else{
			// fail
			echo 0;
		}

	}


	public function update_accounts_agency_contact(){

		$accounts_name =  $this->input->post('acc_contact_name');
		$accounts_phone =  $this->input->post('acc_phone');


		// get agency details
		$params = array(
			'sel_query' => '
				a.`accounts_name`,
				a.`accounts_phone`
			',
			'agency_id' => $this->session->agency_id
		);
		$agency_sql = $this->agency_model->get_agency_data($params);
		$agency = $agency_sql->row();

		// accounts contact
		// name
		if( $accounts_name != $agency->accounts_name ){

			$title = 14; // Agency Profile Update
			$details = "Accounts Contact Name updated from {$agency->accounts_name} to {$accounts_name}";
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

		// phone
		if( $accounts_phone != $agency->accounts_phone ){

			$title = 14; // Agency Profile Update
			$details = "Accounts Contact Phone updated from{$agency->accounts_phone} to {$accounts_phone}";
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

		$data = array(
				'accounts_name' => $accounts_name,
				'accounts_phone' => $accounts_phone
		);

		$this->db->where('agency_id', $this->session->agency_id);
		$this->db->update('agency', $data);

		if($this->db->affected_rows() > 0){
			// success
			echo 1;
			$this->session->set_flashdata('agency_profile_update_success', 1);
		}else{
			// fail
			echo 0;
		}

	}

	/**
	 * AJAX Request
	 * Get agency including alt
	 * echo options for dropdown
	 */
	public function ajax_get_agency_by_pm_including_alt(){

		$pm_id = $this->input->post('pm_id');
		$logged_in_agency = $this->input->post('logged_in_agency');
		$imp_harris_agency = implode(',',$this->config->item('harris_agencies'));

		$alt_id = $this->db->query("
			Select agency_id, alt_agencies 
			FROM agency_user_accounts 
			WHERE agency_user_account_id = {$pm_id}
		");
		$alt_id_row = $alt_id->row();

		if( in_array($logged_in_agency, $this->config->item('harris_agencies')) ){ //show harris agencies even if no alt_agencies > for Harris agencies only

			$q = "
				SELECT a.agency_id, a.agency_name FROM agency as a
				WHERE a.agency_id IN( {$imp_harris_agency} )
			";
			$query = $this->db->query($q);

		}elseif( $alt_id->num_rows() > 0 && $alt_id_row->alt_agencies!="" ){

			$q = "
				SELECT a.agency_id, a.agency_name FROM agency as a
				WHERE a.agency_id IN( {$alt_id_row->agency_id},{$alt_id_row->alt_agencies} )
			";
			$query = $this->db->query($q);

		}else{
			$query = NULL;
		}

		if( $query!="" ){
			echo "<option value=''>Please Select New Agency</option>";
			foreach( $query->result_array() as $row ){
				if( $row['agency_id']!=$this->session->agency_id ){
					echo "<option value='{$row["agency_id"]}'>{$row['agency_name']}</option>";
				}
			}
		}
		
	}

	public function ajax_move_agency_pm(){

		$agency = $this->input->post('agency');
		$orig_agency_name = $this->input->post('orig_agency_name');
		$orig_agency_id = $this->input->post('orig_agency_id');
		$pm = $this->input->post('pm');
		$transfer = $this->input->post('transfer');
		
		## get old/current user info
		$old_agency = $this->user_accounts_model->get_user_account_via_id($pm);

		##alt agencies
		$alt_agencies_arr = explode(",", "{$old_agency->alt_agencies}"); ## current alt agencies	

		if( $old_agency->alt_agencies=="" ){ //empty alt_agencies > insert orig current orig agency > catch for AU Hariss Agency Only

			$new_alt_agencies = $orig_agency_id;

		}else{	//insert old agencies and orig agencies

			$old_alt_agencies_arr = [];
			foreach( $alt_agencies_arr as $alt_agencies_rows ){
				## get current user alt_agencies excluding new agency and join/add old agency_id
				if( $alt_agencies_rows != $agency){ ## get agencies that is not same new agency and store it in array  (in short exclude new selected agency)
					$old_alt_agencies_arr[] = $alt_agencies_rows;	
				}
			}
			array_push($old_alt_agencies_arr, $orig_agency_id); ##join old agency_id
			$new_alt_agencies = implode( ',', $old_alt_agencies_arr );

		}
		
		##alt agencies end

		##update user to new agency
		$update_to_new_agency_data = array(
			'agency_id' => $agency,
			'alt_agencies' => $new_alt_agencies
		);
		$this->db->where('agency_user_account_id', $pm);
		$this->db->update('agency_user_accounts', $update_to_new_agency_data);

		## get new agency user info
		$new_agency = $this->user_accounts_model->get_user_account_via_id($pm);

		if( $transfer==0 ){ ##update properties PM attached to null

			$update_data = array(
				'pm_id_new' => 'NULL'
			);
			$this->db->where('pm_id_new',$pm);
			$this->db->update('property', $update_data);

		}elseif( $transfer==1 ){

			$update_data = array(
				'agency_id' => $agency
			);
			$this->db->where('pm_id_new',$pm);
			$this->db->update('property', $update_data);

		}

		##insert log
		$agency_id_log_arr = array($agency,$orig_agency_id);
		foreach( $agency_id_log_arr as $agency_id_log_row ){ ##loop two agencies and send logs

			if( $transfer==1 ){

				$log_details = "<strong>User {$new_agency->fname} {$new_agency->lname}</strong> and all attached properties was changed from <strong>{$orig_agency_name}</strong> to <strong>{$new_agency->agency_name}</strong>";
				$log_params = array(
					'title' => 84,
					'details' => $log_details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $agency_id_log_row,
					'created_by' => $this->session->aua_id
				);
				
			}else{
	
				$log_details = "<strong>User {$new_agency->fname} {$new_agency->lname}</strong> was changed from <strong>{$orig_agency_name}</strong> to <strong>{$new_agency->agency_name}</strong>";
				$log_params = array(
					'title' => 84,
					'details' => $log_details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $agency_id_log_row,
					'created_by' => $this->session->aua_id
				);
	
			}

			$this->jcclass->insert_log($log_params);

		}
		##insert log end

	}


	public function ajax_check_pm_if_has_prop_attached(){

		$data['status'] = false;
		$data['old_agency_name'] = NULL;
		$data['old_agency_id'] = NULL;

		$user_id = $this->input->post('pm_id');
		
		$check_pm_prop_attached_params = array(
			'sel_query' => 'pm_id_new',
			'pm_id' => $user_id
		);
		$check_pm_prop_attached = $this->properties_model->get_properties($check_pm_prop_attached_params);

		if( $check_pm_prop_attached->num_rows()>0 ){
			$data['status'] = true;
		}else{
			$data['status'] = false;
		}

		$old_agency = $this->user_accounts_model->get_user_account_via_id($user_id);
		$data['old_agency_name'] = $old_agency->agency_name;
		$data['old_agency_id'] = $old_agency->agency_id;

		echo json_encode($data);

	}



}
