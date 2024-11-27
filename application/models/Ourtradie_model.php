<?php
class Ourtradie_model extends CI_Model {

	public function __construct(){
		$this->load->database();
	}

	public function index(){
		echo "Test";
	}

	//Get token
	public function getToken($agency_id, $api_id){
        return $this->db->select('expiry,refresh_token,created')
        ->from('agency_api_tokens')
        ->where('agency_id', $agency_id)
        ->where('api_id', $api_id)
        ->get()->result_object();
        $this->db->get('agency_api_tokens');
    }

	//Insert token
    public function insertToken($insert_data) {
        $this->db->insert('agency_api_tokens', $insert_data);
        return $this->db->insert_id();
    }

    //Update token
    public function updateToken($agency_id, $api_id, $update_data) {
        $this->db->where('agency_id', $agency_id);
        $this->db->where('api_id', $api_id);
        $this->db->update('agency_api_tokens', $update_data);

        if($this->db->affected_rows()>0){
            return true;
        }
        else{
            return false;
        }
    }//endfct

    //Update token
    public function updateAgencyId($agency_id, $api_id, $update_data) {
        $this->db->where('agency_id', $agency_id);
        $this->db->where('api_id', $api_id);
        $this->db->update('agency_api_tokens', $update_data);

        if($this->db->affected_rows()>0){
            return true;
        }
        else{
            return false;
        }
    }//endfct


}//endclass
