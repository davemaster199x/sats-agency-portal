<?php
class Agency_model extends MY_Model {
	public $table = 'agency';
	public $primary_key = 'agency_id';
	
	public function __construct(){
		parent::__construct();
			$this->load->database();
	}
	
	public function get_agency_activity($params){
		
		if($params['sel_query'] != ''){
			$sel_query = $params['sel_query'];
		}else{
			$sel_query = '*,ac.date_created AS ac_date_created';
		}
		
		$this->db->select($sel_query);
		$this->db->from('agency_activity AS ac');
		$this->db->join('agency_user_accounts AS aua', 'ac.user = aua.agency_user_account_id', 'left');
		$this->db->where('ac.`active`', 1);
		if( isset($params['aua_id']) ){
			$this->db->where('ac.`user`', $params['aua_id']);
		}		
		$this->db->order_by('ac.date_created', 'DESC');
		if( isset($params['limit']) && $params['limit'] > 0 ){
			$this->db->limit($params['limit'],$params['offset']);
		}		
		return $this->db->get();
		
	}
	
	
	public function get_noticeboard(){

		$this->db->select('*');
		$this->db->from('noticeboard');
		$this->db->where('country_id', $this->session->country_id);	
		return $this->db->get();
		
	}
	
	
	public function get_agency_data($params){
		
		// select
		if( isset($params['sel_query']) ){
			$sel_query = $params['sel_query'];
		}else{
			$sel_query = '*';
		}
		$this->db->select($sel_query);
		
		// tables
		$this->db->from('agency AS a');
		$this->db->join('staff_accounts as sa', 'sa.StaffID = a.salesrep', 'left');
		$this->db->join('agency_maintenance as am', 'am.agency_id = a.agency_id', 'left');
		$this->db->join('maintenance as m', 'm.maintenance_id = am.maintenance_id', 'left');
		$this->db->join('trust_account_software as tsa', 'a.trust_account_software = tsa.trust_account_software_id', 'left');
		$this->db->join('sub_regions AS sr', 'sr.sub_region_id = a.postcode_region_id', 'left');

		// set joins
        if( !empty($params['join_table']) ) {

            foreach( $params['join_table'] as $join_table ){

               	if( $join_table == 'countries' ) {
                    $this->db->join('countries as c', 'a.`country_id` = c.`country_id`', 'left');
                }

            }

        }

        // custom joins
        if (isset($params['custom_joins']) && $params['custom_joins'] != '') {
            $this->db->join($params['custom_joins']['join_table'], $params['custom_joins']['join_on'], $params['custom_joins']['join_type']);
        }

        // multiple custom joins
		if( !empty($params['custom_joins_arr']) ) {
            foreach( $params['custom_joins_arr'] as $custom_joins ){
                $this->db->join($custom_joins['join_table'], $custom_joins['join_on'], $custom_joins['join_type']);
            }
        }
		
		// filters
		if( isset($params['agency_id']) ){
			$this->db->where('a.`agency_id`', $params['agency_id']);
		}
		if( isset($params['user_type']) ){
			$this->db->where('a.`user_type`', $params['user_type']);
		}
		if( isset($params['state']) ){
			$this->db->where('a.`state`', $params['state']);
		}
		if( isset($params['postcode']) ){
			$this->db->where('a.`postcode`', $params['postcode']);
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

		// echo query
		$query = $this->db->get();
		if( isset($params['display_query']) && $params['display_query'] == 1 ){
			echo $this->db->last_query();
		}
		
		return $query;
		
	}
	
	
	// Trust Account Software
	public function get_trust_account_software($params){
		
		// select
		if( isset($params['sel_query']) ){
			$sel_query = $params['sel_query'];
		}else{
			$sel_query = '*';
		}
		$this->db->select($sel_query);
		
		// tables
		$this->db->from('trust_account_software AS tsa');
		
		// filters
		if( isset($params['tsa_id']) ){
			$this->db->where('tsa.`trust_account_software_id`', $params['tsa_id']);
		}
		if( isset($params['active']) ){
			$this->db->where('tsa.`active`', $params['active']);
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

		// echo query
		$query = $this->db->get();
		if( isset($params['display_query']) && $params['display_query'] == 1 ){
			echo $this->db->last_query();
		}
		
		return $query;
		
	}
	
		
		
}
