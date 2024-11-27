<?php
class Logs_model extends MY_Model {
	public $table = 'logs';
	public $primary_key = 'log_id';

	public function __construct(){
		parent::__construct();
			$this->load->database();
	}

	public function get_logs($params){

		if( isset($params['sel_query']) ){
			$sel_query = $params['sel_query'];
		}else{
			$sel_query = '*';
		}

		$this->db->select($sel_query);
		$this->db->from('logs AS l');
		$this->db->join('log_titles AS ltit', 'l.`title` = ltit.`log_title_id`', 'left');
		$this->db->join('agency_user_accounts AS aua', 'l.`created_by` = aua.`agency_user_account_id`', 'left');

		if ( isset($params['joins']) ) {
			foreach ($params['joins'] as $join) {
				$j = array_merge([
					'type' => 'left',
				], $join);
				$this->db->join($j['table'], $j['condition'], $j['type']);
			}
		}

		// filters
		if( isset($params['user_filter']) && $params['user_filter']!="" ){
			$this->db->where('aua.`agency_user_account_id`', $params['user_filter']);
		}
		if( isset($params['log_id']) ){
			$this->db->where('l.`log_id`', $params['log_id']);
		}
		if( isset($params['log_title']) ){
			$this->db->where('l.`title`', $params['log_title']);
		}
		if( isset($params['log_type']) ){
			$this->db->where('l.`log_type`', $params['log_type']);
		}
		if( isset($params['created_by']) && $params['created_by'] != '' ){
			$this->db->where('l.`created_by`', $params['created_by']);
		}
		if( isset($params['job_id']) ){
			$this->db->where('l.`job_id`', $params['job_id']);
		}
		if( isset($params[' property_id ']) ){
			$this->db->where('l.`property_id`', $params['property_id']);
		}
		if( isset($params['agency_id']) ){
			$this->db->where('l.`agency_id`', $params['agency_id']);
		}
		if( isset($params['deleted']) ){
			$this->db->where('l.`deleted`', $params['deleted']);
		}
		if( isset($params['deleted_by']) ){
			$this->db->where('l.`deleted_by`', $params['deleted_by']);
		}

		// markers
		if( isset($params['display_in_vjd']) && is_numeric($params['display_in_vad']) ){
			$this->db->where('l.`display_in_vjd`', $params['display_in_vjd']);
		}
		if( isset($params['display_in_vpd']) && is_numeric($params['display_in_vpd']) ){
			$this->db->where('l.`display_in_vpd`', $params['display_in_vpd']);
		}
		if( isset($params['display_in_vad']) && is_numeric($params['display_in_vad']) ){
			$this->db->where('l.`display_in_vad`', $params['display_in_vad']);
		}
		if( isset($params['display_in_portal']) && is_numeric($params['display_in_portal']) ){
			$this->db->where('l.`display_in_portal`', $params['display_in_portal']);
		}
		if( isset($params['display_in_accounts']) && is_numeric($params['display_in_accounts']) ){
			$this->db->where('l.`display_in_accounts`', $params['display_in_accounts']);
		}
		if( isset($params['display_in_accounts_hid']) && is_numeric($params['display_in_accounts_hid']) ){
			$this->db->where('l.`display_in_accounts_hid`', $params['display_in_accounts_hid']);
		}
		if( isset($params['display_in_sales']) && is_numeric($params['display_in_sales']) ){
			$this->db->where('l.`display_in_sales`', $params['display_in_sales']);
		}

		// custom filter
		if( isset($params['custom_where']) && $params['custom_where'] != '' ){
			$this->db->where($params['custom_where']);
		}



		// sort
		if( isset($params['sort_list']) ){
			foreach( $params['sort_list'] as $sort_arr ){
				if( $sort_arr['order_by']!="" && $sort_arr['sort']!='' ){
					$this->db->order_by($sort_arr['order_by'], $sort_arr['sort']);
				}
			}
		}else{
			// default
			$this->db->order_by('l.`created_date`', 'DESC');
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

	/**
     * Insert Logs
     */
    public function insert_log($params) {

        $data = [];

        $data = array(
            'title' => $params['title'],
            'details' => $params['details'],
            'created_date' => date('Y-m-d H:i:s')
        );

        if (isset($params['created_by']) && $params['created_by'] > 0) {
            $data['created_by'] = $params['created_by'];
        }

        if ( is_numeric($params['created_by_staff']) ) {
            $data['created_by_staff'] = $params['created_by_staff'];
        }

        // ID's
        if (isset($params['job_id']) && $params['job_id'] > 0) {
            $data['job_id'] = $params['job_id'];
        }

        if (isset($params['property_id']) && $params['property_id'] > 0) {
            $data['property_id'] = $params['property_id'];
        }

        if (isset($params['agency_id']) && $params['agency_id'] > 0) {
            $data['agency_id'] = $params['agency_id'];
        }

        // markers
        if (isset($params['display_in_vjd']) && is_numeric($params['display_in_vjd'])) {
            $data['display_in_vjd'] = $params['display_in_vjd'];
        }

        if (isset($params['display_in_vpd']) && is_numeric($params['display_in_vpd'])) {
            $data['display_in_vpd'] = $params['display_in_vpd'];
        }

        if (isset($params['display_in_vad']) && is_numeric($params['display_in_vad'])) {
            $data['display_in_vad'] = $params['display_in_vad'];
        }

        if (isset($params['display_in_portal']) && is_numeric($params['display_in_portal'])) {
            $data['display_in_portal'] = $params['display_in_portal'];
        }

        if (isset($params['display_in_accounts']) && is_numeric($params['display_in_accounts'])) {
            $data['display_in_accounts'] = $params['display_in_accounts'];
        }

        if (isset($params['display_in_accounts_hid']) && is_numeric($params['display_in_accounts_hid'])) {
            $data['display_in_accounts_hid'] = $params['display_in_accounts_hid'];
        }

        if (isset($params['display_in_sales']) && is_numeric($params['display_in_sales'])) {
            $data['display_in_sales'] = $params['display_in_sales'];
        }

        if (isset($params['auto_process']) && is_numeric($params['auto_process'])) {
            $data['auto_process'] = $params['auto_process'];
        }

        if (isset($params['log_type']) && is_numeric($params['log_type'])) {
            $data['log_type'] = $params['log_type'];
        }

        if (isset($params['important']) && is_numeric($params['important'])) {
            $data['important'] = $params['important'];
        }

        if ($this->db->insert('logs', $data)) {
            return true;
        } else {
            return false;
        }
    }


}
