<?php

class AccessCheck {

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
		
		// check pages
		$pages = array(
			'user_accounts/index',
			'user_accounts/logins',
			//'agency/profile'
		);

		if( in_array($url, $pages) ){
			
			// get user
			$user_params = array( 
				'sel_query' => 'aua.`user_type`',
				'active' => 1,
				'aua_id' => $this->CI->session->aua_id
			);
			$user_sql = $this->CI->user_accounts_model->get_user_accounts($user_params);
			$user_row = $user_sql->row();
			
			// access denied for PM
			if( $user_row->user_type == 2 ){
				$this->CI->session->set_flashdata('access_denied', 1);
				redirect('/home');
			}
			
		}
		
		
		
		
	}

}

