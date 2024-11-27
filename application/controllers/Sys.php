<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sys extends CI_Controller {

	protected $user_photo_upload_path = '/uploads/user_accounts/photo';
	protected $default_avatar = '/images/avatar-2-64.png';

	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('user_accounts_model');
		$this->load->model('properties_model');
		//$this->load->model('jobs_model');
		//$this->load->library('pagination');
    }

	private function attachAdditionalProperties(&$properties) {

		if (empty($properties)) {
			return;
		}

		$propertiesById = [];

		for ($x = 0; $x < count($properties); $x++) {
			$property =& $properties[$x];

			$property['property_services'] = [];
			// $property['last_service_date'] = null;

			$propertiesById[$property['property_id']] =& $property;
		}

		$propertyIds = array_keys($propertiesById);

		$propertyServiceTypes = $this->db->select("ps.property_services_id, ps.property_id, ps.`service`, ajt.`id` AS ajt_id, ajt.`type`, ajt.`short_name`, ajt.html_id")
			->from("property_services AS ps")
			->join("alarm_job_type AS ajt", "ajt.id = ps.alarm_job_type_id", "inner")
			->where_in("property_id", $propertyIds)
			->where("service", 1)->get()->result();

		$propertyServiceTypeById = [];
		$alarmJobTypeIds = [];

		for ($x = 0; $x < count($propertyServiceTypes); $x++) {
			$propertyServiceType =& $propertyServiceTypes[$x];

			$propertyServiceType->agency_service_count = 0;

			$alarmJobTypeIds[] = $propertyServiceType->ajt_id;

			$propertyServiceTypeById[$propertyServiceType->property_services_id] =& $propertyServiceType;
		}

		$alarmJobTypeIds = array_unique($alarmJobTypeIds);

		$agencyServiceCounts = $this->db->select("service_id, COUNT(agency_services_id) AS as_count")
			->from("agency_services")
			->where("agency_id", $this->session->agency_id)
			->where_in("service_id", $alarmJobTypeIds)
			->group_by("service_id")
			->get()->result();

		$agencyServiceCountsById = [];

		for ($x = 0; $x < count($agencyServiceCounts); $x++) {
			$agencyServiceCount = $agencyServiceCounts[$x];

			$agencyServiceCountsById[$agencyServiceCount->service_id] = $agencyServiceCount->as_count;
		}

		for ($x = 0; $x < count($propertyServiceTypes); $x++) {
			$ps =& $propertyServiceTypes[$x];
			if (isset($agencyServiceCountsById[$ps->ajt_id])) {
				$ps->agency_service_count = $agencyServiceCountsById[$ps->ajt_id];
			}
		}

		foreach ($propertyServiceTypes as $pst) {
			$propertiesById[$pst->property_id]['property_services'][] = $pst;
		}

		// $latestJobsForProperties = $this->db->select("MAX(date) AS latest, property_id")
		// 	->from('jobs')
		// 	->where_in("property_id", $propertyIds)
		// 	->where("status", "Completed")
		// 	->where("del_job", 0)
		// 	->group_by("property_id")
		// 	->get()->result();

		// for ($x = 0; $x < count($latestJobsForProperties); $x++) {
		// 	$propertiesById[$latestJobsForProperties[$x]->property_id]['last_service_date'] = $latestJobsForProperties[$x]->latest;
		// }
	}

	public function search()
    {
		$data['title'] = 'Search Result';


		$data['search_str'] = $this->input->post('search');

		if($data['search_str']){

			$searchResults = array_map(function($r) {
				return (array)$r;
			}, $this->properties_model->search_property($data['search_str']));

			$this->attachAdditionalProperties($searchResults);

			$data['search_res'] = $searchResults;
		}else{
			redirect(base_url('/home'));
		}
        
        // autocomplete for tags
        $data['address_tags'] = $this->properties_model->get_address();

		$this->load->view('templates/home_header',$data);
        $this->load->view('sys/search',$data);
		$this->load->view('templates/home_footer', $data);
	}


	public function send_reset_password_email(){

		$aua_id = $this->input->get_post('aua_id');

		if( isset($aua_id) && $aua_id > 0 ){

			if( $this->user_accounts_model->send_reset_password_email($aua_id) ){

				$data['title'] = "Success";

				$data['error_header'] = "SUCCESS";
				$data['error_txt'] = "Reset Email has been sent!";

				$this->load->view('templates/success_header.php', $data);
				$this->load->view('generic/success_page', $data);
				$this->load->view('templates/success_footer.php', $data);

			}else{

				$data['title'] = "Error";

				$data['error_header'] = "ERROR";
				$data['error_txt'] = "Sending reset email failed.";

				$this->load->view('errors/template/error_header.php', $data);
				$this->load->view('generic/error_page', $data);
				$this->load->view('errors/template/error_footer.php', $data);

			}

		}

	}


	public function send_invite_email(){

		$aua_id = $this->input->get_post('aua_id');

		if( isset($aua_id) && $aua_id > 0 ){

			$set_pass_email_data = array(
				'sender_id' => null,
				'invited_id' => $aua_id,
				'admin_full_name' => $this->config->item('COMPANY_NAME_SHORT'),
			);
			if( $this->user_accounts_model->send_invite_set_password_email($set_pass_email_data) ){

				$data['title'] = "Success";

				$data['error_header'] = "SUCCESS";
				$data['error_txt'] = "Invite Email has been sent!";

				$this->load->view('templates/success_header.php', $data);
				$this->load->view('generic/success_page', $data);
				$this->load->view('templates/success_footer.php', $data);

			}else{

				$data['title'] = "Error";

				$data['error_header'] = "ERROR";
				$data['error_txt'] = "Sending invite email failed.";

				$this->load->view('errors/template/error_header.php', $data);
				$this->load->view('generic/error_page', $data);
				$this->load->view('errors/template/error_footer.php', $data);

			}

		}

	}

	public function send_book_portal_training_email(){

		$aua_id = $this->session->aua_id;

		$response = [
			'status' => false,
			'message'=> 'Sending portal training email failed.'
		];

		if( isset($aua_id) && $aua_id > 0 ){

			if( $this->user_accounts_model->send_book_portal_training_email($aua_id) ){

				$response = [
					'status' => true,
					'message'=> 'Portal training email sent successfully.'
				];

			}

		}
		header('Content-Type: application/json');
        echo json_encode($response);
	}


	public function under_maintenance(){

		// get agency maintenance mode
		if( $this->system_model->get_agency_portal_maintenance_mode() == 1 ){
			$this->load->view('generic/under_maintenance');
		}else{
			redirect('/');
		}

	}

	public function switch_agency($agency_id){
		$this->session->set_userdata('agency_id', $agency_id);
		redirect('/home');
	}

	public function check_agency_session(){
		$isExist = true;
	  	$sess_id = $this->session->userdata('agency_id');

	   	if(empty($sess_id) || !$sess_id){
	   		$isExist = false;
	   	}
	   	echo json_encode($isExist);
	}
 
    public function auto_suggest_properties()
    {
        $result = $this->properties_model->get_address();
        
        echo json_encode([
            "data" => $result
        ]);
    }

}
