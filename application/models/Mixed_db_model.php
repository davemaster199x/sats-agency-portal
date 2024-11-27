<?php
class Mixed_db_model extends CI_Model {

	public function __construct(){
			$this->load->database();
	}
	
	// countries
	public function get_countries($params){
		
		if( isset($params['sel_query']) ){
			$sel_query = $params['sel_query'];
		}else{
			$sel_query = '*';
		}
		
		$this->db->select($sel_query);
		$this->db->from('`countries` AS c');
		
		// filters
		if( isset($params['country_id']) ){
			$this->db->where('c.`country_id`', $params['country_id']);
		}		
		
		// custom filter
		if( isset($params['custom_where']) ){
			$this->db->where($params['custom_where']);
		}
		
		// sort
		if( isset($params['sort_list']) ){
			foreach( $params['sort_list'] as $sort_arr ){
				if( $sort_arr['order_by']!="" && $sort_arr['sort']!='' ){
					$this->db->order_by($sort_arr['order_by'], $sort_arr['sort']);
				}
			}
		}
		
		// limit
		if( isset($params['limit']) && $params['limit'] > 0 ){
			$this->db->limit( $params['limit'], $params['offset']);
		}	
	
		
		$query = $this->db->get();
		if( isset($params['display_query']) && $params['display_query'] == 1 ){
			echo $this->db->last_query();
		}
		
		return $query;
		
	}

	public function usertype_is_pm($aua_id){

		$query = $this->db->get_where('agency_user_accounts',array('agency_user_account_id'=>$aua_id,'user_type'=>2));
		if($query->num_rows()>0){
			return true;
		}else{
			return false;
		}

	}
	
	
	// 240v RF (Required for Quotes)
	public function get240vRfAgencyAlarmPrice($agency_id){
		
		$this->db->select('aa.`price`');
		$this->db->from('`agency_alarms` AS aa');
		$this->db->where('aa.`agency_id`', $agency_id);
		$this->db->where('aa.`alarm_pwr_id`', 10);
		$this->db->limit( 1, 0);
		$query = $this->db->get();
		$row = $query->row();
		
		return $row->price;
		
	}
	
	// Smoke Alarms (IC) (Required for Quotes)
	public function get_sa_ic_data(){
		
		$this->db->select('*');
		$this->db->from('`agency_services` AS agen_serv');
		$this->db->where('agen_serv.`service_id`', 12); // Smoke Alarms (IC)
		$this->db->where('agen_serv.`agency_id`', $this->session->agency_id);
		return $query = $this->db->get();
		
	}
	
	
	// states
	public function get_states($params){
		
		if( isset($params['sel_query']) ){
			$sel_query = $params['sel_query'];
		}else{
			$sel_query = '*';
		}
		
		$this->db->select($sel_query);
		$this->db->from('`states_def` AS s');
		
		// filters
		if( isset($params['state_id']) ){
			$this->db->where('s.`StateID`', $params['state_id']);
		}	
		
		if( isset($params['state']) ){
			$this->db->where('s.`state`', $params['state']);
		}
		
		if( isset($params['country_id']) ){
			$this->db->where('s.`country_id`', $params['country_id']);
		}
		
		// custom filter
		if( isset($params['custom_where']) ){
			$this->db->where($params['custom_where']);
		}
		
		// sort
		if( isset($params['sort_list']) ){
			foreach( $params['sort_list'] as $sort_arr ){
				if( $sort_arr['order_by']!="" && $sort_arr['sort']!='' ){
					$this->db->order_by($sort_arr['order_by'], $sort_arr['sort']);
				}
			}
		}
		
		// limit
		if( isset($params['limit']) && $params['limit'] > 0 ){
			$this->db->limit( $params['limit'], $params['offset']);
		}	
	
		
		$query = $this->db->get();
		if( isset($params['display_query']) && $params['display_query'] == 1 ){
			echo $this->db->last_query();
		}
		
		return $query;
		
	}
	
		
}
