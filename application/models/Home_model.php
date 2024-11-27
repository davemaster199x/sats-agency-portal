<?php
class Home_model extends CI_Model
{
	public function __construct(){
		parent::__construct();
		$this->load->database();
	}

	// get completed jobs
	public function get_completed_jobs()
	{
		$agency_id = $this->session->agency_id;

		$completed_jobs_sql_str = "
			SELECT COUNT(j.`id`) AS jcount
			FROM `jobs` AS j
			INNER JOIN `property` AS p ON j.`property_id` = p.`property_id`
			INNER JOIN `agency` AS a ON p.`agency_id` = a.`agency_id`
			WHERE j.`del_job` = 0
			AND j.`assigned_tech` != 1
			AND j.`status` = 'Completed'
			AND p.`deleted` = 0
			AND a.`status` = 'active'
			AND a.`agency_id` = '{$agency_id}'
			AND j.`date` BETWEEN '".date('Y-m-01')."' AND '".date('Y-m-t')."'
		";

		return $this->db->query($completed_jobs_sql_str);
	}

	// get booked jobs
	public function get_booked_jobs(){

		$agency_id = $this->session->agency_id;

		$sel_query = '
			COUNT(j.`id`) AS jcount
		';
		$query_params = array(
			'sel_query' => $sel_query,
			'del_job' => 0,
			'p_deleted' => 0,
			'a_status' => 'active',
			'j_status' => 'Booked',
			'agency_id' => $agency_id,
			'display_query' => 0
		);
		return $this->jobs_model->get_jobs($query_params);

	}

	// get Tenant Job Feedback / Thank you SMS
	public function get_job_feedback()
	{
		$agency_id = $this->session->agency_id;

		$sel_query = '
			COUNT(sas.`sms_api_sent_id`) AS jcount
		';

		$sms_type = 18; // SMS (Thank You)
		$last_60_days = date('Y-m-d',strtotime("-60 days"));

		// get current month
		$from_f = date('Y-m-01');
		$to_f = date('Y-m-t');

		$custom_where = "
			CAST(j.`date` AS Date)  BETWEEN '{$from_f}' AND '{$to_f}'
			AND CAST(sar.`created_date` AS DATE) >= '{$last_60_days}'
		";

		$query_params = array(
			'sel_query' => $sel_query,
			'sas_active' => 1,
			'sms_type' => $sms_type,
			'a_status' => 'active',
			'display_query' => 0,
			'custom_where' => $custom_where,
			'agency_id' => $agency_id,
			'display_query' => 0
		);
		return $this->sms_api_model->get_sms_api_data($query_params);
	}
}
?>
