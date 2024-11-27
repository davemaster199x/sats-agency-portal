<?php
class Logs extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('agency_model');
		$this->load->library('pagination');
		$this->load->model('profile_model');
		$this->load->model('properties_model');
		$this->load->model('logs_model');
	}

	public function activity($aua_id_param=null){

		$data['title'] =  'Activity Logs';

		// pagination
		$per_page = $this->config->item('pagi_per_page');
		$offset = $this->input->get('offset');

		$aua_id = ( isset($aua_id_param) && isset($aua_id_param) > 0 )?$aua_id_param:null;
		$agency_id = $this->session->agency_id;

		$user_filter = $this->input->get_post('user');

		// date filter
		$from = $this->input->get_post('from');
		$to = $this->input->get_post('to');

		$from_f = null;
		$to_f = null;

		if( ( isset($from) && $from != '' ) && ( isset($to) && $to != '' ) ){

			// formated to be database ready Y-m-d
			$from_f = $this->jcclass->formatDate($from);
			$to_f = $this->jcclass->formatDate($to);

			$custom_where = "CAST(l.`created_date` AS Date)  BETWEEN '{$from_f}' AND '{$to_f}'";

		}



		// paginated results
		$params = [
			'sel_query' => '
				l.`log_id`,
				l.`created_date`,
				l.`title`,
				l.`details`,
				l.`created_by`,
				l.`created_by_staff`,

				ltit.`title_name`,

				aua.`fname`,
				aua.`lname`,
				aua.`photo`,

				p.property_id,
				p.`address_1` AS p_address_1,
				p.`address_2` AS p_address_2,
				p.`address_3` AS p_address_3,
				p.`state` AS p_state,
				p.`postcode`
			',
			'deleted' => 0,
			'created_by' => $aua_id,
			'agency_id' => $agency_id,
			'display_in_portal' => 1,

			'user_filter' => $user_filter,
			'custom_where' => $custom_where,

			'display_query' => 0,

			'limit' => $per_page,
			'offset' => $offset,
			'joins' => [
				[
					'table' => 'property AS p',
					'condition' => 'p.property_id = l.property_id',
					'type' => 'left',
				],
			],
		];
		$query = $this->logs_model->get_logs($params);
		$logs = $query->result();

		$logsById = [];
		$pattern = "/agency_user:\d+/";
		$taggedAgencyUserIds = [];
		for ($x = 0; $x < count($logs); $x++) {
			$log =& $logs[$x];

			$matches = [];
			if( preg_match($pattern, $log->details, $matches) == 1 ){
				$agencyUserId = explode(':', $matches[0])[1];

				$taggedAgencyUserIds[] = $agencyUserId;

				$log->taggedAgencyUserId = $agencyUserId;
			}

			$logsById[$log->log_id] =& $log;
		}


		if (!empty($taggedAgencyUserIds)) {
			$taggedAgencyUserIds = array_unique($taggedAgencyUserIds);

			$taggedAgencyUsers = $this->db->select("
				aua.`agency_user_account_id`,
				aua.`fname`,
				aua.`lname`
			")
				->from('agency_user_accounts AS aua')
				->where_in('agency_user_account_id', $taggedAgencyUserIds)
				->get()->result();

			foreach ($logs as &$log) {
				if (isset($log->taggedAgencyUserId)) {
					foreach ($taggedAgencyUsers as $taggedAgencyUser) {
						if ($log->taggedAgencyUserId == $taggedAgencyUser->agency_user_account_id) {
							$log->taggedAgencyUser = $taggedAgencyUser;
							break;
						}
					}
				}
			}
		}

		$data['logs'] = $logs;

		// all rows
		$params = array(
			'sel_query' => 'l.`log_id`',
			'deleted' => 0,
			'created_by' => $aua_id,
			'agency_id' => $agency_id,
			'display_in_portal' => 1,

			'user_filter' => $user_filter,
			'custom_where' => $custom_where,
		);
		$query = $this->logs_model->get_logs($params);
		$total_rows = $query->num_rows();


		// PM filter
		$sel_query = "
			DISTINCT(aua.`agency_user_account_id`),
			aua.`fname`,
			aua.`lname`,
			aua.photo
		";
		$custom_where2 = "aua.`agency_user_account_id` > 0 AND aua.agency_id = {$agency_id}";

		$params = array(
			'sel_query' => $sel_query,
			'deleted' => 0,

			'custom_where' => $custom_where,
			'custom_where' => $custom_where2,
			'agency_id' => $agency_id,
			'sort_list' => array(
				array(
					'order_by' => 'aua.`fname`',
					'sort' => 'ASC'
				),
				array(
					'order_by' => 'aua.`lname`',
					'sort' => 'ASC'
				)
			)
		);
		$data['pm_filter'] = $this->logs_model->get_logs($params);


		$config['total_rows'] = $total_rows;
		$config['page_query_string'] = TRUE;
		$config['query_string_segment'] = 'offset';
		$config['per_page'] = $per_page;
		$config['base_url'] = "/logs/activity/{$aua_id}?from={$from}&to={$to}";
		$data['user_photo_upload_path'] = '/uploads/user_accounts/photo';
		$data['default_avatar'] = '/images/avatar-2-64.png';

		$this->pagination->initialize($config);

		$data['pagination'] = $this->pagination->create_links();

		$pc_params = array(
			'total_rows' => $total_rows,
			'offset' => $offset,
			'per_page' => $per_page
		);
		$data['pagi_count'] = $this->jcclass->pagination_count($pc_params);

		$data['indiv_user'] = $aua_id_param;
		$data['from_f'] = $from_f;
		$data['to_f'] = $to_f;

		// breadcrumb
		$data['aua_id'] = $aua_id;

		// get user data
		$params = array(
			'sel_query' => '
				aua.`fname`,
				aua.`lname`
			',
			'aua_id' => $aua_id
		);

		$user_sql = $this->user_accounts_model->get_user_accounts($params);
		$user = $user_sql->row();

		$data['inner_bc_txt'] = ( $this->session->aua_id == $aua_id )?'My Activity':"{$user->fname}'s Activity";

		$this->load->view('templates/home_header',$data);
        $this->load->view('logs/activity',$data);
		$this->load->view('templates/home_footer');
	}


}
