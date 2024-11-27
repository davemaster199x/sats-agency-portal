<?php
class Sms extends CI_Controller {

	public function __construct(){
        parent::__construct();
        $this->load->library('pagination');
		$this->load->model('sms_api_model');
		$this->load->model('properties_model');
	}

	public function index(){

		redirect(base_url('/home'));

    }

    public function job_feedback(){

        $data['title'] = 'Job Feedback';

        $search = $this->input->get_post('search');

        //date post
        $data['date_from'] = ($this->input->get_post('from'))?$this->input->get_post('from'):date('01/m/Y');
        $data['date_to'] = ($this->input->get_post('to'))?$this->input->get_post('to'):date('t/m/Y');
        //format date for sql query
        $from_f = $this->jcclass->formatDate($data['date_from']);
		$to_f = $this->jcclass->formatDate($data['date_to']);

        // perpage and offset
		$per_page = $this->config->item('pagi_per_page');
		$offset = $this->input->get_post('offset');

        $sms_type = 18;
		$last_60_days = date('Y-m-d',strtotime("-60 days"));
        //$custom_where = "CAST(sar.`created_date` AS DATE) >= '{$last_60_days}'";
        $custom_where = "
			CAST(j.`date` AS Date)  BETWEEN '{$from_f}' AND '{$to_f}'
			AND CAST(sar.`created_date` AS DATE) >= '{$last_60_days}'
		";
        $sel_query = '
            p.property_id,
            p.address_1,
            p.address_2,
            p.address_3,
            p.state,
            p.postcode,
            sa.FirstName,
            sa.LastName,
            sas.sms_type,
            sas.sent_by,
            sat.type_name,
            sar.created_date,
            sar.mobile,
            sar.response,
            ass_tech.FirstName as tech_fname,
            ass_tech.sa_position as tech_lname
        ';
        $query_params = array(
            'sel_query' => $sel_query,
			'sas_active' => 1,
			'a_status' => 'active',
            'display_query' => 0,
            'sms_type' => $sms_type,
            'custom_where' => $custom_where,
            'agency_id' => $this->session->agency_id,
            'search' => $search,
            'limit' => $per_page,
            'offset' => $offset,
            'sort_list' => array(
				array(
					'order_by' => 'sar.created_date',
					'sort' => 'ASC'
				)
            ),
            'display_query' => 0
		);
        $feedbacks = $this->sms_api_model->get_sms_api_data($query_params)->result();

        $this->properties_model->attach_new_tenants_to_list($feedbacks);

        $data['feedback_list'] = $feedbacks;

        //get all rows
        $totalRowParams = array(
			'sas_active' => 1,
			'a_status' => 'active',
            'display_query' => 0,
            'sms_type' => $sms_type,
            'custom_where' => $custom_where,
            'agency_id' => $this->session->agency_id,
            'search' => $search,
            'sort_list' => array(
				array(
					'order_by' => 'sar.created_date',
					'sort' => 'ASC'
				)
			)
		);
        $query = $this->sms_api_model->get_sms_api_data($totalRowParams);
        $total_rows = $query->num_rows();

        // pagination settings
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'offset';
        $config['total_rows'] = $total_rows;
        $config['per_page'] = $per_page;
        $config['base_url'] = "/sms/job_feedback/?search={$search}&from={$from_f}&to={$to_f}";

        $this->pagination->initialize($config);

        $data['pagination'] = $this->pagination->create_links();

        // pagination count
        $pc_params = array(
            'total_rows' => $total_rows,
            'offset' => $offset,
            'per_page' => $per_page
        );
        $data['pagi_count'] = $this->jcclass->pagination_count($pc_params);

		$this->load->view('templates/home_header', $data);
		$this->load->view('sms/sms', $data);
		$this->load->view('templates/home_footer');

    }






}
