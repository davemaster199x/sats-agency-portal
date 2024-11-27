<?php
class Resources extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('resources_model');
		$this->load->model('agency_model');
	}

	public function index(){

		$data['title'] = 'Resources';

		$country_id = $this->session->country_id;
		$agency_id = $this->session->agency_id;

		// get agency details
		$params = array(
			'sel_query' => '
				a.`state`
			',
			'agency_id' => $this->session->agency_id
		);
		$agency_sql = $this->agency_model->get_agency_data($params);
		$agency = $agency_sql->row();
		$data['a_state'] = $agency->state;

		$states_filter_append = '';
		$stateId = null;
		if( $country_id == 1 ){ // states are AU ONLY

			// get states ID
			$params = array(
				'sel_query' => '
					s.`StateID`
				',
				'state' => $agency->state
			);
			$states_sql = $this->mixed_db_model->get_states($params);
			$states_row = $states_sql->row();
			$stateId = $states_row->StateID;
			$data['state_id'] = $stateId;
			$states_filter_append = "AND r.`states` LIKE '%{$states_row->StateID}%'";

		}

		// get distinct resource headers
		$sel_query = "DISTINCT (
				r.`resources_header_id`
			),
			rh.`name`
		";

		$custom_where = "rh.`resources_header_id` > 0 {$states_filter_append}";
		$params = array(
			'sel_query' => $sel_query,
			'country_id' => $country_id,
			'custom_where' => $custom_where
		);
		$resourceHeaders = $this->resources_model->get_resources_data($params)->result();

		$resourceHeadersById = [];
		$resourceHeaderIds = [];
		for ($x = 0; $x < count($resourceHeaders); $x++) {
			$resourceHeader = &$resourceHeaders[$x];

			$resourceHeader->resource_data = [];

			$resourceHeadersById[$resourceHeader->resources_header_id] =& $resourceHeader;
			$resourceHeaderIds[] = $resourceHeader->resources_header_id;
		}

        if(!empty($resourceHeaderIds)){
            $this->db->select("
			r.`resources_header_id`,
			r.`type`,
			r.`filename`,
			r.`title`,
			r.`url`,
			r.`date`,
			r.path
			")
            ->from('`resources` as r')
            ->where_in('r.`resources_header_id`', $resourceHeaderIds);
            if( !is_null($stateId) ){ // states filter are AU only
                $this->db->like('r.`states`', $stateId);
            }
            $resourcesData = $this->db->get()->result();
            foreach ($resourcesData as $resourceData) {
                $resourceHeadersById[$resourceData->resources_header_id]->resource_data[] = $resourceData;
            }
        }

		$data['resource_headers'] = $resourceHeaders ?? [];

		// get country
		$params = array(
			'sel_query' => 'c.`iso`',
			'country_id' => $country_id
		);
		$country_sql = $this->mixed_db_model->get_countries($params);
		$country_row = $country_sql->row();
		$data['country_iso'] = strtolower($country_row->iso);

		$this->load->view('templates/home_header', $data);
		$this->load->view('resources/index', $data);
		$this->load->view('templates/home_footer');

	}


}
