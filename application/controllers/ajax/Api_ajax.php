<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_ajax extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('api_model');
        $this->load->model('logs_model');
    }

    public function add_agency_api_integration()
    {
        $response = [
            'success'   => false,
            'message'   => 'Failed to integrate API.'
        ];

        if ($this->input->is_ajax_request()) {
            $agency_id = $this->session->agency_id;
            $staff_id = $this->session->aua_id;
            $connected_service = $this->input->post('connected_service');

            #data to be inserted
            $insert_data = array(
                'connected_service' => $connected_service,
                'agency_id' => $agency_id,
                'date_activated' => date('Y-m-d')
            );

            if ($this->api_model->add_agency_api_integration($insert_data)) {
                ##get agency API name
                $api_param = [
                    'sel_query' => 'api_name',
                    'agency_api_id' => $connected_service
                ];

                $api_row = $this->api_model->get_agency_api($api_param)->row_array();
                $api_name = $api_row['api_name'];

                ##insert logs
                $log_details = "{$api_name} API integration added";
                $log_params = [
                    'title'             => 85,  //API Integration
                    'details'           => $log_details,
                    'display_in_vad'    => 1,
                    'created_by_staff'  => $staff_id,
                    'created_by'        => $staff_id,
                    'agency_id'         => $agency_id
                ];

                if (!$this->logs_model->insert_log($log_params)) {
                    $response = [
                        'success'   => false,
                        'message'   => 'failed to save log.'
                    ];
                }
            
                $response = [
                    'success'   => true,
                    'message'   => 'API integrated successfully.'
                ];
            }
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function get_api_logs()
    {
        $agency_id = $this->session->agency_id;
        $staff_id = $this->session->aua_id;
        $logTitle = $this->input->get('logTitle') ?? null;

        $params = array(
			'sel_query' => '
				l.`log_id`,
				l.`created_date`,
				l.`title`,
				l.`details`,
				l.`created_by`,
				l.`created_by_staff`,

				aua.`fname`,
				aua.`lname`,
				aua.`photo`,

				ltit.`title_name`,

				p.`property_id`,
				p.`address_1` AS p_address_1,
				p.`address_2` AS p_address_2,
				p.`address_3` AS p_address_3,
				p.`state` AS p_state,
				p.`postcode`
			',
            'log_title' =>$logTitle,
			'deleted' => 0,
			'agency_id' => $agency_id,
			'display_in_portal' => 1,
			'display_query' => 0,
			'joins' => [
				[
					'table' => 'property AS p',
					'condition' => 'p.property_id = l.property_id',
					'type' => 'left',
				],
			],
		);
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
                $log->image =  (isset($log->photo) && $log->photo != '' ) ? "/uploads/user_accounts/photo/{$log->photo}" : "/images/avatar-2-64.png";
                $log->created_date =  date('d/m/Y H:i',strtotime($row->created_date));
                $log->name =  $this->jcclass->formatStaffName($log->fname,$log->lname);
                $log->details   = $this->jcclass->parseDynamicLink($log);
			}
		}

        echo json_encode([
            'recent_activity' => $logs,
        ]);
    }
}