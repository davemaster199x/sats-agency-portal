<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

	public function __construct(){
		parent::__construct();
		//$this->load->model('user_accounts_model');
		$this->load->model('sms_api_model');
		$this->load->model('email_model');
		$this->load->library('pagination');
    }

	public function index(){

		$data['title'] = "Error";

		$data['error_header'] = "The password link you are trying to use has expired";

		// get agent number from countries table
		$countries_sql = $this->db->query("
		SELECT `agent_number`
		FROM `countries`
		WHERE `country_id` = {$_ENV['COMPANY_COUNTRY_ID']}			
		");
		$countries_row = $countries_sql->row();
		$agent_number = $countries_row->agent_number;

		$data['error_txt'] = "Please contact us at <a href='mailto:{$_ENV['COMPANY_EMAIL']}'>{$_ENV['COMPANY_EMAIL']}</a> or call us on {$agent_number}";

		$this->load->view('errors/template/error_header.php', $data);
		$this->load->view('generic/error_page', $data);
		$this->load->view('errors/template/error_footer.php', $data);

	}

	public function php_info(){
		phpinfo();
	}

	public function session(){
		print_r($_SESSION);
	}	

}
