<?php 
class Resources_model extends CI_Model {

	public function __construct(){
			$this->load->database();
	}

	// resource header
	public function get_resources_header($params){
		
		if( isset($params['sel_query']) ){
			$sel_query = $params['sel_query'];
		}else{
			$sel_query = '*';
		}
		
		$this->db->select($sel_query);
		$this->db->from('`resources_header` AS rh');
		
		// filters
		if( isset($params['country_id']) ){
			$this->db->where('rh.`country_id`', $params['country_id']);
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
	
	// resources
	public function get_resources_data($params){
		
		if( isset($params['sel_query']) ){
			$sel_query = $params['sel_query'];
		}else{
			$sel_query = '*';
		}
		
		$this->db->select($sel_query);
		$this->db->from('`resources` AS r');
		$this->db->join('`resources_header` AS rh', 'r.`resources_header_id` = rh.`resources_header_id`', 'left');
		
		// filters
		if( isset($params['country_id']) ){
			$this->db->where('r.`country_id`', $params['country_id']);
		}
		
		if( isset($params['resources_id']) ){
			$this->db->where('r.`resources_id`', $params['resources_id']);
		}
		
		if( isset($params['resources_header_id']) ){
			$this->db->where('r.`resources_header_id`', $params['resources_header_id']);
		}
		
		if( isset($params['states']) ){
			$this->db->where('r.`states`', $params['states']);
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
