<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Templatedatahandler
{
	private $data = [];

    public function __construct()
    {
        $this->CI =& get_instance();
	}

	public function preloadData() {
        $this->data['user'] = $this->CI->user_accounts_model->get_user_account_via_id($this->CI->session->aua_id);

        $alt_agen_params = [
            'sel_query' => '
                aua.`agency_id`,
                aua.`alt_agencies`
            ',
            'custom_where' => "aua.`alt_agencies` != ''",
            'aua_id' => $this->CI->session->aua_id,
            'display_query' => 0
        ];

        $altAgenciesResult = $this->CI->user_accounts_model->get_user_accounts($alt_agen_params);
        $this->data['alt_agencies_count'] = $altAgenciesResult->num_rows();

        $altAgency = $altAgenciesResult->row();
        $this->data['alt'] = $altAgency;

        if (isset($altAgency->alt_agencies) && $altAgency->alt_agencies != '') {

			$custom_where = "
			a.`agency_id` IN({$alt_agen_row->agency_id},{$alt_agen_row->alt_agencies})
			AND a.`agency_id` != {$this->session->agency_id}
			";
			/*
            $params = [
				'sel_query' => '
					a.`agency_id`,
					a.`agency_name`
				',
				'custom_where' => "
                    a.`agency_id` IN({$altAgency->agency_id},{$altAgency->alt_agencies})
                    AND a.`agency_id` != {$this->CI->session->agency_id}
                ",
				'sort_list' => [
					[
						'order_by' => 'a.`agency_name`',
						'sort' => 'ASC'
                    ]
                ],
				'display_query' => 0
            ];
            */

            ## Gherx > new query - removed  AND a.`agency_id` != {$this->CI->session->agency_id} to include current agency on 'Switch Agency' fancybox
            $params = [
				'sel_query' => '
					a.`agency_id`,
					a.`agency_name`
				',
				'custom_where' => "
                    a.`agency_id` IN({$altAgency->agency_id},{$altAgency->alt_agencies})
                ",
				'sort_list' => [
					[
						'order_by' => 'a.`agency_name`',
						'sort' => 'ASC'
                    ]
                ],
				'display_query' => 0
            ];

			$this->data['agencies'] = $this->CI->agency_model->get_agency_data($params)->result();
        }
        else {
            $this->data['agencies'] = [];
        }
        $this->data['agency_state'] = $this->CI->gherxlib->agency_info()->state;

        $this->data['esc_jobs_num'] = $this->CI->jcclass->get_escalate_jobs();
        $this->data['service_due_jobs'] = $this->CI->jcclass->get_service_due_jobs();
        $this->data['agency_info'] = $this->CI->jcclass->get_agency_phone();
        $this->data['get_qld_upgrade_quotes_total'] = $this->CI->jcclass->get_qld_upgrade_quotes_total();
	}

	public function getData() {
        return $this->data;
	}
}
