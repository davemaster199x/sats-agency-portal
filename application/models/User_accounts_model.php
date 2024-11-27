<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_accounts_model extends MY_Model {

    public function __construct(){
        $this->load->database();
	}

	// august 18, 2021
	// copied from harney's original function but recoded, improved
	public function update_user_password() {

		$user_id = $this->input->post('user_id');
		$old_code = $this->input->post('enc');
		$new_password = $this->input->post('new-pass');

		// get agency user 
		$this->db->select('agency_user_account_id,agency_id');
		$this->db->from('agency_user_accounts');
		$this->db->where('reset_password_code', $old_code);
		$this->db->where('agency_user_account_id', $user_id);
		$user_sql = $this->db->get();		

		// ensure user exist and reset code is passed
		if( $user_sql->num_rows() > 0 && $old_code != '' ){ 

			$user_row = $user_sql->row();

			$aua_id = $user_row->agency_user_account_id;
			$agency_id = $user_row->agency_id;			

			// update password		
			$update_sql_params = array(
			'password' => password_hash ( $new_password, PASSWORD_DEFAULT ),
			'reset_password_code' => NULL,
			'reset_password_code_ts' => NULL,
			'password_changed_ts' => date('Y-m-d H:i:s')
			);

			$this->db->where('reset_password_code', $old_code);
			$this->db->where('agency_user_account_id', $user_id);
			$this->db->update('agency_user_accounts', $update_sql_params);

			// RESET PASSWORD LOG
			$title = 24; // Password Updated
			$details = "{agency_user:{$user_id}}'s password was updated";
								
			$params = array(
				'title' => $title,
				'details' => $details,
				'display_in_portal' => 1,
				'display_in_vad' => 1,
				'agency_id' => $agency_id,
				'created_by' => $aua_id
			);			
			$this->jcclass->insert_log($params);

		}				

	}
	

	public function insert_reset_code($encryption_key, $user_id) {

		$data_query = array(
			'reset_password_code' => $encryption_key,
			'reset_password_code_ts' => date('Y-m-d H:i:s')
		);
		$this->db->where('agency_user_account_id', $user_id);
		return $this->db->update('agency_user_accounts', $data_query);
	}

	public function get_reset_code($reset_code) {
		$this->db->select('*');
		$this->db->from('agency_user_accounts');
		$this->db->where('reset_password_code', $reset_code);

		$query = $this->db->get();

		return $query->result_array();
	}

	public function save_user_account(){
		
		$agency_id = $this->session->agency_id;
		
		$email = $this->input->post('email');
		$fname = $this->input->post('fname');
		$lname = $this->input->post('lname');
		$user_type = $this->input->post('user_type');
		$phone = $this->input->post('phone');
		$job_title = $this->input->post('job_title');	
		
		$data = array(
				'email' => $email,
				'fname' => $fname,
				'lname' => $lname,
				'user_type' => $user_type,
				'phone' => $phone,
				'job_title' => $job_title,
				'agency_id' => $agency_id,
				'date_created' => date('Y-m-d H:i:s')
		);

		if( $this->db->insert('agency_user_accounts', $data) ){
			
			// ID of user added
			$aua_id = $this->db->insert_id();
			
			// send invite email
			// get sender name
			$sender_params = array( 
				'sel_query' => '
					aua.`fname`,
					aua.`lname`
				',
				'aua_id' => $this->session->aua_id
			);			
			$sender_sql = $this->get_user_accounts($sender_params);
			$sender_row = $sender_sql->row();
			$sender_name = "{$sender_row->fname} {$sender_row->lname}";
			
			$set_pass_email_data = array(
				'sender_id' => $this->session->aua_id,
				'invited_id' => $aua_id,
				'admin_full_name' => $sender_name
			);
			
			// send email
			$this->send_invite_set_password_email($set_pass_email_data);
			$this->session->set_flashdata('new_user_added', 1);
			
			$title = 17; // User Account Added
			$details = "User account {agency_user:{$aua_id}} has been added";

			$params = array(
				'title' => $title,
				'details' => $details,
				'display_in_portal' => 1,
				'display_in_vad' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id
			);

			$this->jcclass->insert_log($params);
			
			return true;
		}else{
			return false;
		}
			
	}
	
	
	public function update_user_account(){
		
		$aua_id = $this->input->post('aua_id');
		
		$email = $this->input->post('email');
		$fname = $this->input->post('fname');
		$lname = $this->input->post('lname');
		$user_type = $this->input->post('user_type');
		$phone = $this->input->post('phone');
		$job_title = $this->input->post('job_title');
		$edit_pw_flag = $this->input->post('edit_pw_flag');
		$password = $this->input->post('password');
		
		
		
		// get user account	
		$aua_params = array( 
			'aua_id' => $aua_id,
			'sel_query' => '
				aua.`agency_user_account_id`,
				aua.`fname`,
				aua.`lname`
			'
		);
		$user_sql = $this->get_user_accounts($aua_params);
		$user = $user_sql->row();
		
		// user name
		$user_fullname = "{$user->fname} {$user->lname}";
		
		
		$data = array(
				'email' => trim($email),
				'fname' => $fname,
				'lname' => $lname,
				'user_type' => $user_type,
				'phone' => $phone,
				'job_title' => $job_title
		);
		
		
		if( $edit_pw_flag == 1 ){
			
			$password_hash = password_hash ( $password, PASSWORD_DEFAULT );			
			$data['password'] = $password_hash;

			// logs
			$title = 11; // Password
			$details = "{agency_user:{$aua_id}}'s password updated";
			
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



		//added by gherx start 
		
		$gh_query = $this->db->get_where('agency_user_accounts', array('agency_user_account_id'=>$aua_id)); //get current/old users info/data
		$old_user_info = $gh_query->row();
		
		$old_user_photo = $old_user_info->photo;
		$old_user_email = $old_user_info->email;
		$old_user_fname = $old_user_info->fname;
		$old_user_lname = $old_user_info->lname;
		$old_user_phone = $old_user_info->phone;
		$old_user_jobTitle = $old_user_info->job_title;
		$old_user_userType = $old_user_info->user_type;

		//added by gherx end


		
		$this->db->where('agency_user_account_id', $aua_id);
		$this->db->update('agency_user_accounts', $data);

		if( $this->db->affected_rows() > 0 ){


			if($email!=$old_user_email){ //trigger email log

				$title = 4; // User Account Updated
				$details = "{agency_user:{$aua_id}}'s email updated";
				
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

			if($fname!=$old_user_fname){ //trigger fname log

				$title = 4; // User Account Updated
				$details = "{agency_user:{$aua_id}}'s first name updated";
				
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

			if($lname!=$old_user_lname){ //trigger lname log

				$title = 4; // User Account Updated
				$details = "{agency_user:{$aua_id}}'s last name updated";
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

			if($phone!=$old_user_phone){ //trigger phone log

				$title = 4; // User Account Updated
				$details = "{agency_user:{$aua_id}}'s phone updated";
				
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

			if($job_title!=$old_user_jobTitle){ //trigger job title log

				$title = 4; // User Account Updated
				$details = "{agency_user:{$aua_id}}'s job title updated";
				
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

			
			return true;
		}else{
			return false;
		}



		
			
	}
	
	public function get_user_accounts($params){
		
		if( isset($params['sel_query']) ){
			$sel_query = $params['sel_query'];
		}else{
			$sel_query = '*';
		}
		
		$this->db->select($sel_query);
		$this->db->from('agency_user_accounts AS aua');
		$this->db->join('agency_user_account_types AS auat', 'aua.`user_type` = auat.`agency_user_account_type_id`', 'left');
		$this->db->join('agency AS a', 'aua.`agency_id` = a.`agency_id`', 'left');
		$this->db->where('a.deleted',0);
		
		if( isset($params['active']) ){
			$this->db->where('aua.`active`', $params['active']);
		}
		if( isset($params['user_type']) ){
			$this->db->where('aua.`user_type`', $params['user_type']);
		}
		if( isset($params['email']) ){
			$this->db->where('aua.`email`', $params['email']);
		}
		if( isset($params['agency_id']) ){
			$this->db->where('aua.`agency_id`', $params['agency_id']);
		}
		if( isset($params['aua_id']) ){
			$this->db->where('aua.`agency_user_account_id`', $params['aua_id']);
		}
		if( isset($params['reset_password_code']) ){
			$this->db->where('aua.`reset_password_code`', $params['reset_password_code']);
		}
		if( isset($params['password']) ){
			$this->db->where('aua.`password`', $params['password']);
		}
		if( isset($params['agency_status']) ){
			$this->db->where('a.`status`', $params['agency_status']);
		}

		if( isset($params['custom_where']) && $params['custom_where'] != '' ){
			$this->db->where($params['custom_where']);
		}

		$this->db->order_by('aua.fname', 'ASC');
		$this->db->order_by('aua.lname', 'ASC');
		if( isset($params['limit']) && $params['limit'] > 0 ){
			$this->db->limit( $params['limit'], $params['offset']);
		}	
		
		$query = $this->db->get();
		if( isset($params['display_query']) && $params['display_query'] == 1 ){
			echo $this->db->last_query();
		}
		
		return $query;
		
	}
	
	
	public function get_all_user_accounts($limit,$offset){
		
		$this->db->select('*');
		$this->db->from('agency_user_accounts');
		$this->db->join('agency_user_account_types', 'agency_user_accounts.user_type = agency_user_account_types.agency_user_account_type_id', 'left');
		$this->db->where('agency_user_accounts.`active`', 1);
		$this->db->order_by('fname', 'ASC');
		$this->db->order_by('lname', 'ASC');
		if( $limit > 0 ){
			$this->db->limit($limit,$offset);
		}	
		return $this->db->get();
		
	}
	
	public function get_user_account_via_email($email){
		
		$this->db->select('
			aua.`agency_user_account_id`,
			aua.`agency_id`,
			aua.`password`
		');
		$this->db->from('agency_user_accounts AS aua');
		$this->db->where('aua.`active`', 1);
		$this->db->where('aua.`email`', $email);
		$query = $this->db->get();
		return $query->row();
	
	}
	
	public function get_user_account_via_id($aua_id){
		
		$this->db->select('
			aua.`agency_user_account_id`,
			aua.`fname`,
			aua.`lname`,
			aua.`photo`,
			aua.`email`,
			aua.`user_type`,
			aua.`phone`,
			aua.`job_title`,
			aua.agency_id,
			aua.alt_agencies,
			a.`agency_name`,
			auat.user_type_name
		');
		$this->db->from('agency_user_accounts AS aua');
		$this->db->join('agency AS a', 'aua.agency_id = a.agency_id', 'left');
		$this->db->join('agency_user_account_types AS auat', 'aua.user_type = auat.agency_user_account_type_id', 'left');
		$this->db->where('aua.`active`', 1);
		$this->db->where('aua.`agency_user_account_id`', $aua_id);
		$this->db->order_by('aua.fname', 'ASC');
		$this->db->order_by('aua.lname', 'ASC');
		$query = $this->db->get();
		return $query->row();
	
	}
	
	public function get_all_user_types(){
		$this->db->select('*');
		$this->db->from('agency_user_account_types');
		$this->db->order_by('sort_index','ASC');
		$query = $this->db->get();
		return $query->result();
		
	}
	
	public function delete_user_account($aua_id){
		

		// update
		$data = array(
				'active' => 0
		);

		$this->db->where('agency_user_account_id', $aua_id);
		$this->db->update('agency_user_accounts', $data);

		if( $this->db->affected_rows() > 0 ){
			
			// get user account	
			$aua_params = array( 
				'aua_id' => $aua_id,
				'sel_query' => '
					aua.`agency_user_account_id`,
					aua.`fname`,
					aua.`lname`
				'
			);
			$user_sql = $this->get_user_accounts($aua_params);
			$user = $user_sql->row();
			
			// user name
			$user_fullname = "{$user->fname} {$user->lname}";
			
			$title = 13; // User Account Deactivated
			$details = "{agency_user:{$aua_id}}'s user account has been deactivated";
			
			$params = array(
				'title' => $title,
				'details' => $details,
				'display_in_portal' => 1,
				'display_in_vad' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id
			);
			
			$this->jcclass->insert_log($params);
			
			return true;
		}else{
			return false;
		}
	
	}
	
	public function delete_user_photo($aua_id){
		
		$data = array(
				'photo' => null
		);

		$this->db->where('agency_user_account_id', $aua_id);
		$this->db->update('agency_user_accounts', $data);

		if( $this->db->affected_rows() > 0 ){
			return true;
		}else{
			return false;
		}
	
	}
	
	public function insert_agency_user_logins($aua_id){
		
		$data = array(
				'user' => $aua_id,
				'ip' => $_SERVER['REMOTE_ADDR'],
				'date_created' => date('Y-m-d H:i:s')
		);

		if( $this->db->insert('agency_user_logins', $data) ){
			return true;
		}else{
			return false;
		}
	
	}
	
	
	public function get_all_user_logs($params){
		
		// select
		if($params['sel_query'] != ''){
			$sel_query = $params['sel_query'];
		}else{
			$sel_query = '*,aul.date_created AS aul_date_created';
		}
		
		// filters
		$this->db->select($sel_query);
		$this->db->from('agency_user_logins AS aul');
		$this->db->join('agency_user_accounts AS aua', 'aul.user = aua.agency_user_account_id', 'left');
		$this->db->where('aul.`active`', 1);
		if( isset($params['aua_id']) ){
			$this->db->where('aul.`user`', $params['aua_id']);
		}
		if( isset($params['agency_id']) ){
			$this->db->where('aua.`agency_id`', $params['agency_id']);
		}

		// sort
		if( isset($params['sort_list']) ){
			foreach( $params['sort_list'] as $sort_arr ){
				if( $sort_arr['order_by']!="" && $sort_arr['sort']!='' ){
					$this->db->order_by($sort_arr['order_by'], $sort_arr['sort']);
				}
			}
		}
		
		// limit
		$this->db->order_by('aul.date_created', 'DESC');
		if( isset($params['limit']) && $params['limit'] > 0 ){
			$this->db->limit($params['limit'],$params['offset']);
		}			
		return $this->db->get();
		
	}
	
	public function check_if_email_exist($email){
		
		$this->db->select('aua.email');
		$this->db->from('agency_user_accounts AS aua');
		$this->db->where('aua.`email`', $email);
		$query = $this->db->get();
		if( $query->num_rows() > 0 ){
			return true;
		}else{
			return false;
		}
		
	}
	
	public function add_agency_user_activity($title,$details){		
		
		// logged user
		$aua_id = $this->session->aua_id;
		
		$data = array(
			'user' => $aua_id,
			'title' => $title,
			'details' => $details,
			'date_created' => date('Y-m-d H:i:s')
		);

		if( $this->db->insert('agency_activity', $data) ){
			return true;
		}else{
			return false;
		}
		
	}

	//added by gherx
	//get agent phone number (display in header)
	public function get_agency_phone(){
		$this->db->select('c.agent_number');
		$this->db->from('agency as a');
		$this->db->join('countries as c','c.country_id = a.country_id','LEFT');
		$this->db->where('a.agency_id',$this->session->agency_id);
		$query = $this->db->get();
		return ($query->num_rows()>0)?$query->row():false;
	}
	
	
	public function send_reset_password_email($aua_id){
		
		$this->load->library('email');

		$data = [];
		
		// get agency details
		$aua_params = array( 
			'sel_query' => '
				aua.`agency_user_account_id`,
				aua.`fname`,
				aua.`lname`,
				aua.`email`,
				aua.`agency_id`
			',
			'aua_id' => $aua_id
		);
		$user_sql = $this->get_user_accounts($aua_params);
		$user = $user_sql->row();

		if( isset($user) ){
			
			$aua_id = $user->agency_user_account_id;
			$agency_id = $user->agency_id;

			// reset password code
			$reset_password_code = md5($this->jcclass->randomPassword());
			
			// insert reset password code
			$this->insert_reset_code($reset_password_code, $aua_id);

			// send reset password code to email
			$this->email->to($user->email);


			$data['id_hash'] = $reset_password_code;
			$data['user_fname'] = $user->fname;
			$this->email->subject("Password Reset Confirmation");
			$email_content = $this->load->view('emails/reset-password', $data, true);
			$this->email->message($email_content);
			if( $this->email->send() ){
				
				// logs
				$title = 23; // Password Reset
				$details = "{agency_user:{$aua_id}} requested a password reset";
				
				$params = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $agency_id,
					'created_by' => $aua_id
				);
				
				$this->jcclass->insert_log($params);
				
				return true;
			
			} else {
                log_message('error', 'Error sending reset password email to' . $user->email);
				return false;
			}

			
		}
		
	}
	
	
	public function send_invite_set_password_email($params){
		
		$sender_id = $params['sender_id'];
		$invited_id = $params['invited_id'];
		$admin_full_name = $params['admin_full_name'];
		
		$this->load->library('email');

		$data = [];
		
		// get user details
		$aua_params = array( 
			'sel_query' => '
				aua.`agency_user_account_id`,
				aua.`fname`,
				aua.`lname`,
				aua.`email`
			',
			'aua_id' => $invited_id
		);
		$user_sql = $this->get_user_accounts($aua_params);
		$user = $user_sql->row();

		if( isset($user) ){

			// reset password code
			$reset_password_code = md5($this->jcclass->randomPassword());
			
			// insert reset password code
			$this->insert_reset_code($reset_password_code, $invited_id);

			// send reset password code to email

			$this->email->to($user->email);

			// data
			$data['id_hash'] = $reset_password_code;
			$data['user_fname'] = $user->fname;
			$data['admin_full_name'] = $this->config->item('COMPANY_FULL_NAME');
			
			// email
			$this->email->subject($this->config->item('COMPANY_NAME_SHORT')." Portal Invitation");
			$email_content = $this->load->view('emails/invite_set_password', $data, true);
			$this->email->message($email_content);
			
			if( $this->email->send() ){
				
				if( isset($sender_id) && $sender_id > 0 ){
					
					// logs
					$title = 25; // Invitation Sent
					$details = "{agency_user:{$sender_id}} invited {$user->fname} to join";
					
					$params = array(
						'title' => $title,
						'details' => $details,
						'display_in_portal' => 1,
						'display_in_vad' => 1,
						'agency_id' => $this->session->agency_id,
						'created_by' => $sender_id
					);
					
					$this->jcclass->insert_log($params);
					
				}				
				
				return true;
			
			}else{
				return false;
			}

			
		}

	}

	public function send_book_portal_training_email($aua_id){
		
		$this->load->library('email');

		$data = [];
		
		// get agency details
		$aua_params = array( 
			'sel_query' => '
				aua.`agency_user_account_id`,
				aua.`fname`,
				aua.`lname`,
				CONCAT(aua.`fname`, " ", aua.`lname`) as fullname,
				aua.`email`,
				aua.`agency_id`,
				a.agency_name
			',
			'aua_id' => $aua_id
		);
		$user_sql = $this->get_user_accounts($aua_params);
		$user = $user_sql->row();

		if( isset($user) ){
			
			$aua_id = $user->agency_user_account_id;
			$agency_id = $user->agency_id;

			// send reset password code to email
			$this->email->to(make_email('sales'));
			$this->email->from($user->email, $user->fullname);

			$data['user_fname'] = $user->fname;
			$data['fullname']	= $user->fullname;
			$data['agency_name']= $user->agency_name;
			$data['user_email']	= $user->email;

			$this->email->subject("Portal Training Requested from ".$user->agency_name);
			$email_content = $this->load->view('emails/book_portal_training', $data, true);
			$this->email->message($email_content);

			if( $this->email->send() ){
				
				// logs
				$title = 110; // Portal Training Requested
				$details = "{agency_user:{$aua_id}} requested portal training.";
				
				$params = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $agency_id,
					'created_by' => $aua_id
				);
				
				$this->jcclass->insert_log($params);
				
				return true;
			
			} else {
                log_message('error', 'Error sending book portal training email.');
				return false;
			}

			
		}
		
	}

	public function check_if_email_exist_in_agency_json($email){

		$this->db->select('
			aua.`agency_user_account_id`,
			aua.`email`,
			aua.`fname`,
			aua.`lname`
		');
		$this->db->from('`agency_user_accounts` AS aua');
		$this->db->where('aua.`email`', $email);
		$this->db->where('aua.`agency_id`', $this->session->agency_id);
		$query = $this->db->get();
		$row = $query->row();

		$arr = array(
			"aua_id" => $row->agency_user_account_id,
			"email" => $row->email,
			"fname" => $row->fname,
			"lname" => $row->lname
		);

		return json_encode($arr);

	}

	public function send_failed_login_email(){
		$this->load->library('email');

		//email failed logins start
		$email_data['title'] = "Login Failed";
		$email_data['username'] = $this->input->get_post('username');
		$email_data['password'] = $this->input->get_post('password');
		$config = Array('mailtype' => 'html', 'charset' => 'iso-8859-1');
		$this->email->initialize($config);
		$this->email->set_newline("\r\n");
		$this->email->from(make_email('info'), 'Agency '.$this->config->item('theme_uppercase'));
		$this->email->to(make_email('auth'));
		$this->email->subject('Agency '.$this->config->item('theme_uppercase').' - Login Failed!');
		$body = $this->load->view('emails/failed_login', $email_data, TRUE);
		$this->email->message($body);
		if($this->email->send()){
			return true;
		}else{
			return false;
		}
		//email failed logins end
	}


}
