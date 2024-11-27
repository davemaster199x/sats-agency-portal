<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Palace extends CI_Controller {

    function __construct(){
        parent::__construct();

        $this->load->model('palace_model');
        $this->load->model('api_model');
        $this->load->model('agency_user_account_model');
        $this->load->model('logs_model');
    }

    // GET Palace Suppliers per agency
    public function get_palace_supplier(){
        $agency_id = $this->input->get_post('agency_id');
        $result = $this->palace_model->get_all_palace_supplier($agency_id);
        $id = $this->get_palace_supplier_id_by_agency($agency_id);
        $contact = $this->palace_model->get_palace_supplier_by_id($agency_id, $id->palace_supplier_id);
        echo json_encode(array("supp"=>$id->palace_supplier_id, "palace"=> $result, "suppName" =>$contact[0], "agentId" => $id->palace_agent_id, "diaryId" => $id->palace_diary_id));
    }

    public function get_palace_supplier_id_by_agency($agencyId) {
        $sql_str = "
        SELECT palace_supplier_id, palace_agent_id, palace_diary_id
        FROM `agency`
        WHERE agency_id = {$agencyId}    
        ";
        $sql = $this->db->query($sql_str);
        return $sql->row(); 

    }

    public function remove_supplier_id_by_agency() {

        $agencyId = $this->input->get_post('agencyId');

        // $agenConn = $this->get_palace_supplier_id_by_agency($agencyId);
        // if ((!is_null($agenConn->palace_agent_id) && $agenConn->palace_agent_id !== "")) {
            // echo false;
        // }else {
            if (isset($agencyId) && !empty($agencyId) ) {
                $query = "UPDATE agency SET palace_supplier_id = NULL WHERE agency_id = {$agencyId}";

                if($this->db->query($query)){
                    echo true;
                }
                else {
                    echo false;
                }
            }else {
                echo false;
            }
        // }
    }

    public function update_supplier_id_by_agency() {

        $id = $this->input->get_post('id');
        $agencyId = $this->input->get_post('agencyId');

        $agenConn = $this->get_palace_supplier_id_by_agency($agencyId);
        // if (is_null($agenConn->palace_agent_id) || $agenConn->palace_diary_id == "") {
            // echo false;
        // }else {
            $query = "UPDATE agency SET palace_supplier_id = '{$id}' WHERE agency_id = {$agencyId}";

            if($this->db->query($query)){
                echo true;
            }
            else {
                echo false;
            }
        // }

    }

    // GET Palace Agent per agency
    public function get_palace_agent(){
        $agency_id = $this->input->get_post('agency_id');
        $result = $this->palace_model->get_all_palace_agent($agency_id);
        $id = $this->get_palace_agent_id_by_agency($agency_id);
        $contact = $this->palace_model->get_palace_agent_by_id($agency_id, $id->palace_agent_id);
        echo json_encode(array("agent"=>$id->palace_agent_id, "palace"=> $result, "agentName" =>$contact[0], "diaryId" => $id->palace_diary_id));
    }

    public function get_palace_agent_id_by_agency($agencyId) {
        $sql_str = "
        SELECT palace_agent_id, palace_supplier_id, palace_diary_id
        FROM `agency`
        WHERE agency_id = {$agencyId}    
        ";
        $sql = $this->db->query($sql_str);
        return $sql->row(); 

    }

    public function update_agent_id_by_agency() {

        $id = $this->input->get_post('id');
        $agencyId = $this->input->get_post('agencyId');
        
        $agenConn = $this->get_palace_agent_id_by_agency($agencyId);
        // if (is_null($agenConn->palace_diary_id) || $agenConn->palace_diary_id == "") {
            // echo false;
        // }else {
            $query = "UPDATE agency SET palace_agent_id = '{$id}' WHERE agency_id = {$agencyId}";

            if($this->db->query($query)){
                echo true;
            }
            else {
                echo false;
            }
        // }
    }

    public function remove_agent_id_by_agency() {

        $agencyId = $this->input->get_post('agencyId');

        $agenConn = $this->get_palace_agent_id_by_agency($agencyId);
        if ((!is_null($agenConn->palace_supplier_id) && $agenConn->palace_supplier_id !== "")) {
            echo false;
        }else {
            if (isset($agencyId) && !empty($agencyId) ) {
                $query = "UPDATE agency SET palace_agent_id = NULL WHERE agency_id = {$agencyId}";

                if($this->db->query($query)){
                    echo true;
                }
                else {
                    echo false;
                }
            }else {
                echo false;
            }
        }

    }

    // GET Palace diary per agency
    public function get_palace_diary(){
        $agency_id = $this->input->get_post('agency_id');
        $result = $this->palace_model->get_all_palace_diary($agency_id);
        $id = $this->get_palace_diary_id_by_agency($agency_id);
        $diaryCode = $this->palace_model->get_palace_diary_by_id($agency_id, $id->palace_diary_id);
        echo json_encode(array("diary"=>$id->palace_diary_id, "palace"=> $result, "diaryName" =>$diaryCode[0]));
    }

    public function get_palace_diary_id_by_agency($agencyId) {
        $sql_str = "
        SELECT palace_diary_id, palace_agent_id 
        FROM `agency`
        WHERE agency_id = {$agencyId}    
        ";
        $sql = $this->db->query($sql_str);
        return $sql->row(); 

    }

    public function update_diary_id_by_agency() {

        $id = $this->input->get_post('id');
        $agencyId = $this->input->get_post('agencyId');

        $query = "UPDATE agency SET palace_diary_id = '{$id}' WHERE agency_id = {$agencyId}";

        if($this->db->query($query)){
            echo true;
        }
        else {
            echo false;
        }
    }

    public function remove_diary_id_by_agency() {

        $agencyId = $this->input->get_post('agencyId');

        $agenConn = $this->get_palace_diary_id_by_agency($agencyId);
        if ((!is_null($agenConn->palace_agent_id) && $agenConn->palace_agent_id !== "")) {
            echo false;
        }else {
            if (isset($agencyId) && !empty($agencyId) ) {
                $query = "UPDATE agency SET palace_diary_id = NULL WHERE agency_id = {$agencyId}";

                if($this->db->query($query)){
                    echo true;
                }
                else {
                    echo false;
                }
            }else {
                echo false;
            }
        }
    }

    public function ajax_palace_connection_notification_email(){
        $j_data['status'] = false;
        $this->load->model('profile_model');
        
        $agency_id = $this->input->post('agency_id');
        $agency_info = $this->profile_model->get_agency($agency_id);
		$agency_name = $agency_info->agency_name;

        $html_content  = "
        <p>
            Hi Team,
        </p>
        <p>
            {$agency_name} has connected to PALACE, please confirm all properties are connected, and supplier is added.
        </p>
        <p>
            Regards,<br />
            The Devs
        </p>
        ";
        
		$this->email->to(make_email('data'));
        $this->email->subject("Agency Connects to PALACE");
        $this->email->message($html_content);

        # insert logs
        $staff_id = $this->session->aua_id;

        #fetch user data
        $login_user_data = $this->agency_user_account_model->get(array('agency_user_account_id' => $staff_id));
        ##insert logs
        $log_details = "Palace Connected by {$login_user_data->fname} {$login_user_data->lname}";
        $log_params = [
            'title'             => 70,  // Palace API
            'details'           => $log_details,
            'display_in_vad'    => 1,
            'created_by_staff'  => $staff_id,
            'created_by'        => $staff_id,
            'agency_id'         => $agency_id
        ];

        if($this->email->send()){
            $this->session->set_flashdata('palace_api_integ_success', 1);
            $this->logs_model->insert_log($log_params);
            $j_data['status'] = true;
        }else{
            $j_data['status'] = false;
        }

        echo json_encode($j_data);

    }

    public function api_login_request_email(){
        $response['status'] = false;
        $this->load->model('profile_model');
        
        $agency_id = $this->input->post('agency_id');
        $agency_info = $this->profile_model->get_agency($agency_id);
		$agency_name = $agency_info->agency_name;

        $html_content  = "
        <p>
            Hi Jermaine & Remi,
        </p>
        <p>
            We are writing to you on behalf of {$agency_name} to confirm that we wish to proceed with the API connection between Palace and {$this->config->item('COMPANY_NAME_SHORT')}. Can you please provide {$agency_name} with the API credentials login information so that we can complete the integration. 
        </p>
        <p>
            {$this->config->item('COMPANY_NAME_SHORT')}
        </p>
        ";
        
		$this->email->to(make_email('data'));
        $this->email->subject("API log in for {$agency_name}");
        $this->email->message($html_content);
        if($this->email->send()){
            $response['status'] = true;
        }else{
            $response['status'] = false;
        }

        echo json_encode($response);

    }

    public function api_admin_access_request_email(){
        $response['status'] = false;
        $this->load->model('profile_model');
        $this->load->model('agency_user_account_model');

        $login_user_data = $this->agency_user_account_model
            ->fields('fname, lname')
            ->get(array('agency_user_account_id' => $this->session->aua_id));
        
        $agency_id = $this->input->post('agency_id');
        $agency_info = $this->profile_model->get_agency($agency_id);
		$agency_name = $agency_info->agency_name;

        $html_content  = "
        <p>
            Hi team,
        </p>
        <p>
            {$login_user_data->fname} {$login_user_data->lname} from {$agency_name} has requested admin access to the {$this->config->item('COMPANY_NAME_SHORT')} Portal as they are wanting to integrate {$this->config->item('COMPANY_NAME_SHORT')} and Palace. Please verify and update accordingly. 
        </p>
        <p>
            {$this->config->item('COMPANY_NAME_SHORT')}
        </p>
        ";
        
		$this->email->to(make_email('info'));
        $this->email->subject("Admin Access Requested");
        $this->email->message($html_content);
        if($this->email->send()){
            $response['status'] = true;
        }else{
            $response['status'] = false;
        }

        echo json_encode($response);

    }
    
}