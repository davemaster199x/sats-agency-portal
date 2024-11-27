<?php
class Tenants_model extends CI_Model {

	public function __construct(){
			$this->load->database();
	}
	
	public function get_tenants($params){
		
		if( isset($params['sel_query']) ){
			$sel_query = $params['sel_query'];
		}else{
			$sel_query = '*';
		}
		
		$this->db->select($sel_query);
		$this->db->from('property_tenants AS pt');
		if( isset($params['property_id']) ){
			$this->db->where('pt.`property_id`', $params['property_id']);
		}
		if( isset($params['active']) ){
			$this->db->where('pt.`active`', $params['active']);
		}
		//$this->db->order_by('aua.fname', 'ASC');
		//$this->db->order_by('aua.lname', 'ASC');
		if( isset($params['limit']) && $params['limit'] > 0 ){
			$this->db->limit( $params['limit'], $params['offset']);
		}	
		return $this->db->get();
		
	}
		
		
}
