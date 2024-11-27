<?php
class Service_due extends CI_Controller {

	public function __construct(){
		parent::__construct();
		//$this->load->model('news_model');
		//$this->load->helper('url_helper');
	}

	public function index(){
		$data['title'] = 'Due for Service';

		$this->load->view('templates/home_header', $data);
		$this->load->view('service_due/index', $data);
		$this->load->view('templates/home_footer');
	}
	
		
}
