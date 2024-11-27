<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct(){
		parent::__construct(); 
		$this->load->model('user_accounts_model');
		$this->load->model('agency_model');
		$this->load->model('sms_api_model');
		$this->load->model('email_model');
		$this->load->library('email');
    }   

	public function index(){	
		//unset($_SESSION['loginFailedCounter']);

		$crm_login = trim($this->input->get_post('crm_login'));
		
		$this->form_validation->set_rules('username', 'Email', 'required');
		if( $crm_login != 1 ){
			$this->form_validation->set_rules('password', 'Password', 'required');

			if($this->session->userdata('loginFailedCounter')==3){ //validate captcha if failed login == 3
				$this->form_validation->set_rules('g-recaptcha-response','reCaptcha','required|callback_validate_recaptcha');
			}
			
		}		
		
		if ($this->form_validation->run() == FALSE)
		{
			$data['title'] = $this->config->item('COMPANY_NAME_SHORT').' Agency Portal';
			$this->load->view('templates/main_header', $data);			
			$this->load->view('login/index',$data);
			$this->load->view('templates/main_footer');
		}
		else
		{
			
			// form input
			$username = trim($this->input->get_post('username'));
			$password = trim($this->input->get_post('password'));
			$agency_id = trim($this->input->get_post('agency_id'));
			$hid_pass = trim($this->input->get_post('hid_pass'));
			
			
			
			// get user data via username
			$params = array( 
				'sel_query' => '
					aua.`agency_user_account_id`,
					aua.`agency_id`,
					aua.`password`,
					aua.`fname`,

					a.`country_id`
				',
				'email' => $username,
				'active' => 1,
				'agency_status' => 'active'
			);
			
			// login from CRM site
			if( $crm_login == 1 ){
				$params['agency_id'] = $agency_id;
				$params['password'] = $hid_pass;
			}
			
			// get user details
			$user_account_sql = $this->user_accounts_model->get_user_accounts($params);
			
			if( $user_account_sql->num_rows() > 0 ){
				
				// user data
				$user_account = $user_account_sql->row();
				$password_hash = $user_account->password;
				$agency_id = $user_account->agency_id;
				$country_id = $user_account->country_id;
				$aua_id = $user_account->agency_user_account_id;
				$user_fname = $user_account->fname;
				
				// create session array
				$sess_arr = array(
					'agency_id' => $agency_id,
					'country_id' => $country_id,
					'aua_id' => $aua_id,
					'crm_login' => $crm_login
				);
				
				// login from CRM site
				if( $crm_login == 1 ){ // direct access no password needed
					
					// set session					
					$this->store_session_and_login_logs($sess_arr);
					// initial setup
					$this->initial_setup($agency_id);
					
				}else if ( password_verify($password, $password_hash) ) { // verify password

					if( hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']) ){

						// initial setup
						//$this->initial_setup($agency_id);

						// check if 2FA of user already exist
						$user_2fa_sql = $this->db->query("
						SELECT 
							`id`,
							`type`,
							`send_to`,
							`code`
						FROM `agency_user_2fa`
						WHERE `user_id` = {$aua_id}
						AND `active` = 1
						");					

						if(  $user_2fa_sql->num_rows() > 0 ){
							
							// send 2FA code
							$user_2fa_params =  array('aua_id' => $aua_id);
							$this->system_model->send_2fa_code($user_2fa_params);


							redirect("login/enter_2fa_code/{$aua_id}");

						}else{ // login, redirect to home

							// set session
							$this->store_session_and_login_logs($sess_arr);
							redirect('home');

						}

						//unset login counter session
						unset($_SESSION['loginFailedCounter']);
						
					}									
					
				} else { // fail

					//set session counter fo failed login
					if(isset($_SESSION['loginFailedCounter'])){
						$loginFailedCounter = $this->session->userdata('loginFailedCounter');
						$this->session->set_userdata('loginFailedCounter', $loginFailedCounter + 1);
					}else{
						$this->session->set_userdata('loginFailedCounter',1);
					}

					//send failed login email
					$this->user_accounts_model->send_failed_login_email();

					$this->session->set_flashdata('password_incorrect', 1);
					redirect('/');
				}

				
			}else{

				//set session counter fo failed login
				if(isset($_SESSION['loginFailedCounter'])){
					$loginFailedCounter = $this->session->userdata('loginFailedCounter');
					$this->session->set_userdata('loginFailedCounter', $loginFailedCounter + 1);
				}else{
					$this->session->set_userdata('loginFailedCounter',1);
				}

				//send failed login email
				$this->user_accounts_model->send_failed_login_email();
				
				// kick out
				$this->session->set_flashdata('account_doesnt_exist', 1);
				redirect('/');
			}
			
		}
		
	}
	
	
	public function store_session_and_login_logs($sess_arr){
		
		// set session
		$this->session->set_userdata($sess_arr);
		
		if( $sess_arr['crm_login'] != 1 ){ // do not log, if came from crm link
			// insert user login logs
			$this->user_accounts_model->insert_agency_user_logins($sess_arr['aua_id']);
		}
		
		
	}
	
	public function initial_setup($agency_id){
				
		// get agency data
		$params = array(
			'sel_query' => '
				a.`initial_setup_done`
			',
			'agency_id' => $agency_id
		);
		$agency_sql = $this->agency_model->get_agency_data($params);
		$agency_info = $agency_sql->row();
		
		/*
		if( $agency_info->initial_setup_done == 1 ){ // initial setup is done, redirect to home
			redirect('home');
		}else{
			redirect('/home/initial_setup'); // initial setup
		}
		*/

		redirect('home');
		
	}

	public function validate_recaptcha(){

		$recaptcha = trim($this->input->post('g-recaptcha-response'));
		$ip = $this->input->ip_address();
		$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$this->config->item('google_recaptcha_secret_key')."&response=" . $recaptcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
		
		if($response . 'success' == true){
			return TRUE;
		}else{
			$this->form_validation->set_message('validate_recaptcha', 'Prove your not a robot!');
			return FALSE;
		}

	}
	

	public function enter_2fa_code($aua_id,$error=0){
		
		$data['title'] = 'Enter Two-factor Authentication';
		$data['user_id'] = $aua_id;

		// get 2FA data for user
		$user_2fa_sql = $this->db->query("
		SELECT *
		FROM `agency_user_2fa`
		WHERE `user_id` = {$aua_id}
		");	
		$user_2fa_row = $user_2fa_sql->row();
		$data['user_2fa_type'] = $user_2fa_row->type;
		$data['user_2fa_send_to'] = $user_2fa_row->send_to;

		$data['error'] = $error;
		$this->load->view('templates/main_header', $data);			
		$this->load->view('login/enter_2fa_code',$data);
		$this->load->view('templates/main_footer');
		
	}

	public function submit_2fa_code(){
		
		$user_id = $this->db->escape_str(trim($this->input->get_post('user_id')));
		$user_2fa_code = $this->db->escape_str(trim($this->input->get_post('user_2fa_code')));

		$now = date('Y-m-d H:i:s');
		$error = 0;

		if( $user_id > 0 ){			

			// confirm
			$confirm_2fa_params = array(
				'user_id' => $user_id,
				'user_2fa_code' => $user_2fa_code
			);
			$ret_arr = $this->system_model->confirm_2fa_code($confirm_2fa_params);

			if( $ret_arr['success'] == 1 ){ // success

				// get user data
				$params = array( 
					'sel_query' => '
						aua.`agency_user_account_id`,
						aua.`agency_id`,
						aua.`password`,
						aua.`fname`,

						a.`country_id`
					',
					'aua_id' => $user_id,
					'active' => 1,
					'agency_status' => 'active'
				);
				$user_account_sql = $this->user_accounts_model->get_user_accounts($params);			
				$user_account_row = $user_account_sql->row();

				$agency_id = $user_account_row->agency_id;
				$country_id = $user_account_row->country_id;
				$aua_id = $user_account_row->agency_user_account_id;
							
				// create session array
				$sess_arr = array(
					'agency_id' => $agency_id,
					'country_id' => $country_id,
					'aua_id' => $aua_id
				);

				// set session					
				$this->store_session_and_login_logs($sess_arr);

				redirect('home');

			}else{

				$error = $ret_arr['error'];
				redirect("login/enter_2fa_code/{$user_id}/{$error}");

				/*
				if( $error == 1 ){ // Code Incorrect

					redirect("login/enter_2fa_code/{$user_id}/{$error}");

				}else if( $error == 2 ){ // Code Expired

					$this->session->set_flashdata('user_2fa_code_expired', 1);
					redirect("login/index");

				}	
				*/			
				
			}
			

		}		
		
	}


}

