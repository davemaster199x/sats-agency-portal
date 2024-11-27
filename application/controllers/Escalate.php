<?php
class Escalate extends CI_Controller {

	public function __construct(){
		parent::__construct();
		//$this->load->model('news_model');
		//$this->load->helper('url_helper');
	}

	public function index(){
		$data['title'] = 'Help Needed';

		$this->load->view('templates/home_header', $data);
		$this->load->view('escalate/index', $data);
		$this->load->view('templates/home_footer');
	}
	
		
}
