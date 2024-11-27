<?php

class IsLoggedCheck {

	protected $CI;

	// We'll use a constructor, as you can't directly call a function
	// from a property definition.
	public function __construct(){
		
		// Assign the CodeIgniter super-object
		$this->CI =& get_instance();
		
		$this->CI->load->model('user_accounts_model');
		$this->CI->load->model('agency_model');

		$controller = $this->CI->router->fetch_class(); 
		$method = $this->CI->router->fetch_method();
		$url = "{$controller}/{$method}";
		
		// get maintenance mode status
		$this->CI->db->select('agency_portal_mm');
		$this->CI->db->from('crm_settings');
		$this->CI->db->where('country_id',$this->CI->config->item('country'));		
		$m_sql = $this->CI->db->get();
		$m_row = $m_sql->row();
		
		if( $m_row->agency_portal_mm == 1 ){ // maintenance mode is ON
		
			// excluded page
			$pages = array(
				'sys/under_maintenance'
			);	
			
			if( !in_array($url, $pages) ){
								
				// kick out
				$this->CI->session->sess_destroy();
				redirect('/sys/under_maintenance');	

			}		
			
			
		}else{
			
			// excluded page
			$pages = array(
				'login/index', 
				'login/enter_2fa_code',
				'login/submit_2fa_code', 
				
				'user_accounts/reset_password_form',
				'user_accounts/set_password',
				'user_accounts/logout',
				'user_accounts/save_password',
				'user_accounts/send_2fa_code',
				
				'home/initial_setup',			
				'home/is_email_exist_ajax',
				'home/email_check_json',
				
				'test/index',
				'test/send_email',
				'test/create_users_for_agency',
				'test/db_connection',
				'test/create_users_for_agency_list_run',
				'test/session',
				'test/php_info',
				
				'sys/send_reset_password_email',
				'sys/send_invite_email',
				'sys/under_maintenance',

				'console/verify_integration'
			);		
			
			if( !in_array($url, $pages) ){

				// session active
				if( isset($this->CI->session->aua_id) && $this->CI->session->aua_id > 0 ){

					
					// do not allow direct url acces (copy paste url)
                    if (!isset($_SERVER['HTTP_REFERER'])) {
                        echo "<script>window.location='/home'</script>"; // php redirect won't work. i have to use js :(
					}
					
									
					// get user
					$user_params = array( 
						'sel_query' => 'aua.`agency_user_account_id`',
						'active' => 1,
						'aua_id' => $this->CI->session->aua_id
					);
					$user = $this->CI->user_accounts_model->get_user_accounts($user_params);
					
					if( $user->num_rows() == 0 ){
						
						// kick out
						$this->CI->session->sess_destroy();
						redirect('/');	
						
					}else{
						
						// get agency data
						$params = array(
							'sel_query' => '
								a.`initial_setup_done`
							',
							'agency_id' => $this->CI->session->agency_id
						);
						$agency_sql = $this->CI->agency_model->get_agency_data($params);
						$agency_info = $agency_sql->row();
						
						/*
						// initial setup is not done, redirect back to initial setup page
						if( $agency_info->initial_setup_done == 0 ){ 
							redirect('/home/initial_setup'); // initial setup
						}
						*/
						
					}				
					
					
				}else{ // if session expired
					
					// kick out
					$this->CI->session->sess_destroy();
					redirect('/');	
					
				}				
				
			}

			
		}
		
		
		
		
		
	}

}

