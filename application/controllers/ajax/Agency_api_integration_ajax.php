<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agency_api_integration_ajax extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('agency_api_integration_model');
    }

    

    public function is_api_integrated()
    {
        $agency_id = $this->session->agency_id;
        $connected_service = $this->input->get('con_service');

        if ($this->input->is_ajax_request()) {
            #check api if already integrated in the agency
            $is_integrated = $this->agency_api_integration_model->get(array('agency_id' => $agency_id, 'connected_service' => $connected_service));
            
            echo json_encode($is_integrated);
        }
        
    }
}