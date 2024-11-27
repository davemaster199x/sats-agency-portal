<?php
class Jobs_model extends CI_Model {

	public function __construct(){
			$this->load->database();
	}

	// wip
	public function get_jobs($params){

		if( isset($params['sel_query']) ){
			$sel_query = $params['sel_query'];
		}else{
			$sel_query = '*';
		}

		$this->db->select($sel_query);
		$this->db->from('`jobs` AS j');
		$this->db->join('`alarm_job_type` AS ajt', 'j.`service` = ajt.`id`', 'left');
		$this->db->join('`property` AS p', 'j.`property_id` = p.`property_id`', 'left');
		$this->db->join('`agency_user_accounts` AS aua', 'p.`pm_id_new` = aua.`agency_user_account_id`', 'left');
		$this->db->join('`agency` AS a', 'p.`agency_id` = a.`agency_id`', 'left');


		// custom joins
		if( isset($params['custom_joins']) && $params['custom_joins'] != '' ){
			$this->db->join($params['custom_joins']['join_table'],$params['custom_joins']['join_on'], $params['custom_joins']['join_type']);
        }

		// filters
		//job
		if( isset($params['del_job']) ){
			$this->db->where('j.`del_job`', $params['del_job']);
		}
		if( isset($params['job_type']) ){
			$this->db->where('j.`job_type`', $params['job_type']);
		}
		if( isset($params['j_status']) && $params['j_status'] != '' ){
			$this->db->where('j.`status`', $params['j_status']);
		}
		if( isset($params['j_service']) ){
			$this->db->where('j.`service`', $params['j_service']);
		}
		if( isset($params['j_date']) ){
			$this->db->where('j.`date`', $params['j_date']);
		}
		if ( is_numeric($params['country_id']) ) {
            $this->db->where('a.`country_id`', $params['country_id']);
        }

		// property
		if( isset($params['property_id']) ){
			$this->db->where('p.`property_id`', $params['property_id']);
		}
		if( isset($params['p_deleted']) ){
			$this->db->where('p.`deleted`', $params['p_deleted']);
		}
		if( isset($params['pm_id']) && $params['pm_id'] != '' ){
			if( $params['pm_id']==0 ){
				$pm_no_assigend_where = "(p.pm_id_new=0 OR p.pm_id_new IS NULL)";
				$this->db->where($pm_no_assigend_where);
			}else{
				$this->db->where("p.`pm_id_new`", $params['pm_id']);
			}
		}

		// agency
		if( isset($params['a_status']) ){
			$this->db->where('a.`status`', $params['a_status']);
		}
		if( isset($params['agency_id']) ){
			$this->db->where('a.`agency_id`', $params['agency_id']);
		}

		// search
		if( isset($params['search']) && $params['search'] != '' ){
			$search_filter = "CONCAT_WS(' ', LOWER(p.address_1), LOWER(p.address_2), LOWER(p.address_3), LOWER(p.state), LOWER(p.postcode))";
			$this->db->like($search_filter, $params['search']);
		}

		// custom filter
		if( isset($params['custom_where']) ){
			$this->db->where($params['custom_where']);
		}

		// custom filter arr
        if( isset($params['custom_where_arr']) ){
			foreach( $params['custom_where_arr'] as $index => $custom_where ){
				if( $custom_where != '' ){
					$this->db->where($custom_where);
				}
			}
		}

		// sort
		if( isset($params['group_by']) ){
			$this->db->group_by($params['group_by']);
		}

		// having
        if (isset($params['having']) && $params['having'] != '') {
            $this->db->having($params['having']);
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

	/**
	 * Update Property
	 * @ $data array
	 * @ $where array
	 * return true otherwise false
	 */
	public function udpate_property($where,$data){
		$this->db->where($where);
		$this->db->update('property',$data);
		$this->db->limit(1);
		if($this->db->affected_rows()>0){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * GEt job
	 * return row
	 */
	public function get_job_by_id($job_id){
		$this->db->select("*");
		$this->db->from('jobs');
		$this->db->where('id',$job_id);
		$query = $this->db->get();
		return $query->row();
	}

	// update jobs
	public function update_jobs($where,$data){
		$this->db->where($where);
		$this->db->limit(1);
		$this->db->update('jobs',$data);
		if($this->db->affected_rows()>0){
			return true;
		}else{
			return false;
		}
	}

	//return row array
	public function getPrevSmokeAlarm($prop_id){

		// get alarm that is job status completed
		$this->db->distinct('j.id');
		$this->db->select('j.id');
		$this->db->from('alarm as a');
		$this->db->join('jobs as j','j.id = a.job_id','left');
		$this->db->where('property_id',$prop_id);
		//$this->db->where('status', 'Completed');
		$this->db->where_in('j.status', array('Completed','Merged Certificates'));
		$this->db->where('id!=', '');
		$this->db->where('a.ts_discarded',0);
		$this->db->where('del_job', 0);
		$this->db->where('j.`assigned_tech` != 1 AND j.`assigned_tech` != 2'); // exclude OS and UB
		//$this->db->order_by('j.id','DESC');
		$this->db->order_by('j.`date` DESC, j.`id` DESC');
		$this->db->limit(1);
		$query = $this->db->get();
		return ($query->num_rows()>0)?$query->row_array():false;

	}

	//return row array
	public function getPrevSafetySwitch($prop_id){

		// get safety switch that is job status completed
		$this->db->distinct('j.id');
		$this->db->select('j.id');
		$this->db->from('safety_switch as ss');
		$this->db->join('jobs as j','j.id = ss.job_id','left');
		$this->db->where('property_id',$prop_id);
		//$this->db->where('status', 'Completed');
		$this->db->where_in('j.status', array('Completed','Merged Certificates'));
		$this->db->where('id!=', '');
		$this->db->where('del_job', 0);
		$this->db->where('j.`assigned_tech` != 1 AND j.`assigned_tech` != 2'); // exclude OS and UB
		//$this->db->order_by('j.id','DESC');
		$this->db->where('ss.discarded', 0);
		$this->db->order_by('j.`date` DESC, j.`id` DESC');
		$this->db->limit(1);
		$query = $this->db->get();
		return ($query->num_rows()>0)?$query->row_array():false;

	}

	//return row array
	public function getPrevCordedWindow($prop_id){

		// get scorded windows that is job status completed
		$this->db->distinct('j.id');
		$this->db->select('j.id');
		$this->db->from('corded_window as cw');
		$this->db->join('jobs as j','j.id = cw.job_id','left');
		$this->db->where('property_id',$prop_id);
		//$this->db->where('status', 'Completed');
		$this->db->where_in('j.status', array('Completed','Merged Certificates'));
		$this->db->where('id!=', '');
		$this->db->where('del_job', 0);
		$this->db->where('j.`assigned_tech` != 1 AND j.`assigned_tech` != 2'); // exclude OS and UB
		//$this->db->order_by('j.id','DESC');
		$this->db->order_by('j.`date` DESC, j.`id` DESC');
		$this->db->limit(1);
		$query = $this->db->get();
		return ($query->num_rows()>0)?$query->row_array():false;

	}

	// WE
	function getPrevWaterEfficiency($prop_id) {

        $query = $this->db->query("
            SELECT j.`id`
            FROM `water_efficiency` AS we
            LEFT JOIN `jobs` AS j ON j.`id` = we.`job_id`
            WHERE j.`property_id` = {$prop_id}
            AND j.status IN('Completed','Merged Certificates')
            AND j.`id` > 0
			AND j.`del_job` = 0
			AND j.`assigned_tech` != 1
			AND j.`assigned_tech` != 2
            ORDER BY j.`date` DESC, j.`id` DESC
            LIMIT 0,1
		");
		return ($query->num_rows()>0)?$query->row_array():false;

    }


	//return row array
	public function getPrevWaterMeter($prop_id){

		// get water meter that is job status completed
		$this->db->distinct('j.id');
		$this->db->select('j.id');
		$this->db->from('water_meter as wm');
		$this->db->join('jobs as j','j.id = wm.job_id','left');
		$this->db->where('property_id',$prop_id);
		//$this->db->where('status', 'Completed');
		$this->db->where_in('j.status', array('Completed','Merged Certificates'));
		$this->db->where('id!=', '');
		$this->db->where('del_job', 0);
		$this->db->where('j.`assigned_tech` != 1 AND j.`assigned_tech` != 2'); // exclude OS and UB
		//$this->db->order_by('j.id','DESC');
		$this->db->order_by('j.`date` DESC, j.`id` DESC');
		$this->db->limit(1);
		$query = $this->db->get();
		return ($query->num_rows()>0)?$query->row_array():false;

	}

	public function get_escalated_jobs($params){

		if( isset($params['sel_query']) ){
			$sel_query = $params['sel_query'];
		}else{
			$sel_query = '*';
		}

		$this->db->select($sel_query);
		$this->db->from('`selected_escalate_job_reasons` AS sejr');
		$this->db->join('escalate_job_reasons as ejr','ejr.escalate_job_reasons_id = sejr.escalate_job_reasons_id','left');
		$this->db->join('`jobs` AS j', 'sejr.`job_id` = j.`id`', 'left');
		$this->db->join('`alarm_job_type` AS ajt', 'j.`service` = ajt.`id`', 'left');
		$this->db->join('`property` AS p', 'j.`property_id` = p.`property_id`', 'left');
		$this->db->join('`agency_user_accounts` AS aua', 'p.`pm_id_new` = aua.`agency_user_account_id`', 'left');
		$this->db->join('`agency` AS a', 'p.`agency_id` = a.`agency_id`', 'left');

		// filters
		//job
		if( isset($params['del_job']) ){
			$this->db->where('j.`del_job`', $params['del_job']);
		}
		if( isset($params['job_type']) ){
			$this->db->where('j.`job_type`', $params['job_type']);
		}
		if( isset($params['j_status']) && $params['j_status'] != '' ){
			$this->db->where('j.`status`', $params['j_status']);
		}
		if( isset($params['j_service']) ){
			$this->db->where('j.`service`', $params['j_service']);
		}

		// property
		if( isset($params['property_id']) ){
			$this->db->where('p.`property_id`', $params['property_id']);
		}
		if( isset($params['p_deleted']) ){
			$this->db->where('p.`deleted`', $params['p_deleted']);
		}
		if( isset($params['pm_id']) && $params['pm_id'] != '' ){
			$this->db->where('p.`pm_id_new`', $params['pm_id']);
		}

		// agency
		if( isset($params['a_status']) ){
			$this->db->where('a.`status`', $params['a_status']);
		}
		if( isset($params['agency_id']) ){
			$this->db->where('a.`agency_id`', $params['agency_id']);
		}

		// search
		if( isset($params['search']) && $params['search'] != '' ){
			$search_filter = "CONCAT_WS(' ', LOWER(p.address_1), LOWER(p.address_2), LOWER(p.address_3), LOWER(p.state), LOWER(p.postcode))";
			$this->db->like($search_filter, $params['search']);
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

	/**
	 * Total Invoice Balance
	 * return total invoice balance
	 */
	public function getTotalUnpaidAmount($params) {

        $country_id = $this->session->country_id;
		$agency_id = $this->session->agency_id;
        $today = date('Y-m-d');

        $financial_year = $this->config->item('accounts_financial_year');

        $sel_query = "
        j.`id`,
        j.`invoice_balance`,
        j.`date`,
        DATE_ADD(j.`date`, INTERVAL 30 DAY) AS due_date,
        DATEDIFF( '{$today}', j.`date`) AS DateDiff
        ";

		$custom_where = "`j`.`invoice_balance` >0
				AND `j`.`status` = 'Completed'
				AND a.`status` != 'target'
				AND (
						j.`date` >= '$financial_year' OR
						j.`unpaid` = 1
				)
		";

        $params_job = array(
			'sel_query' => $sel_query,
			'p_deleted' => 0,
			'a_status' => 'active',
			'del_job' => 0,
			'agency_id' => $agency_id,
			'country_id' => $country_id,
			'custom_where' => $custom_where,
			'having' => $params['having'],
			'display_query' => 0
		);

        $sql = $this->get_jobs($params_job);
        $tot = 0;
        foreach ($sql->result() as $row) {
            $tot += $row->invoice_balance;
        }

        return $tot;
	}

	// compute check digit
	public function getCheckDigit($number) {

        $sumTable = array(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9), array(0, 2, 4, 6, 8, 1, 3, 5, 7, 9));
        $length = strlen($number);
        $sum = 0;
        $flip = 1;
        // Sum digits (last one is check digit, which is not in parameter)
        for ($i = $length - 1; $i >= 0; --$i)
            $sum += $sumTable[$flip++ & 0x1][$number[$i]];
        // Multiply by 9
        $sum *= 9;

        return (int) substr($sum, -1, 1);
    }

	/**
	 * Check if property has active job
	 * 
	 * @param mixed $prop_id
	 * 
	 * @return bool TRUE|FALSE
	 */
	public function hasActiveJob($prop_id)
	{
		if(empty($prop_id)){
			log_message('error', 'hasActiveJob: Empty property id');
			return FALSE;
		}

		$country_id = $this->session->country_id;
		$job_params = [
			'sel_query' 	=> 'j.id',
			'p_deleted' 	=> 0,
			'a_status' 		=> 'active',
			'del_job' 		=> 0,
			'country_id'	=> $country_id,
			'property_id'	=> $prop_id,
			'custom_where' 	=> "(
				j.`status` != 'Completed' AND
				j.`status` != 'Pending' AND
				j.`status` != 'Cancelled' AND
				j.`status` != 'Merged Certificates'
			)",
			'display_query'	=> 0
		];
		$job_q = $this->get_jobs($job_params);

		if($job_q->num_rows() > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}


}
