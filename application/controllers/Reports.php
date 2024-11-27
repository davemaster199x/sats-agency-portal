<?php
class Reports extends CI_Controller {
	
	/**
	 * @var HashEncryption
	 */
	protected $hashIds;
	
	public function __construct(){
		parent::__construct();
        
        $this->load->library('pagination');
        $this->load->library('encryption');
		$this->load->model('properties_model');
		$this->load->model('jobs_model');
		$this->load->model('agency_model');
        $this->load->model('Alarm_job_type_model');
	}

	public function index(){
		$data['title'] = 'My Reports';

		$agency_sql_str = "
			SELECT agency_id`, allow_upfront_billing`
			FROM `agency`
			WHERE `agency_id` = {$this->session->agency_id}
			AND `allow_upfront_billing` = 1
		";
		$agency_sql = $this->db->query($agency_sql_str);
		$data['numberOfAgencies'] = $agency_sql->num_rows();

		$data['agency_state'] = $this->gherxlib->agency_info()->state;

		$data['check_agency_accounts_reports_preference'] = $this->gherxlib->check_agency_accounts_reports_preference();

		$this->load->view('templates/home_header', $data);
		$this->load->view('reports/index', $data);
		$this->load->view('templates/home_footer');
	}


	public function completed_jobs(){

		$data['title'] = 'Completed Jobs';

		$uri = '/reports/completed_jobs';
        $data['uri'] = $uri;

		$country_id = $this->session->country_id;
		$agency_id = $this->session->agency_id;
		$search = $this->input->get_post('search');


		$pm_id = $this->input->get_post('pm_id');
		$pdf_post = $this->input->get_post('pdf');
		$j_status = 'Completed';

		// pagination
		$per_page = $this->config->item('pagi_per_page');
		$offset = $this->input->get_post('offset');

		// date filter
		$from = ( $this->input->get_post('from') !='' )?$this->input->get_post('from'):date('01/m/Y');
		$to = ( $this->input->get_post('to') !='' )?$this->input->get_post('to'):date('t/m/Y');
		// format date to Y-m-d
		$from_f = $this->jcclass->formatDate($from);
		$to_f = $this->jcclass->formatDate($to);

		$order_by = ( $this->input->get_post('order_by') !='' )?$this->input->get_post('order_by'):'p.address_2';
		$sort = ( $this->input->get_post('sort') !='' )?$this->input->get_post('sort'):'asc';

		$export = $this->input->get_post('export');

		$custom_where = "CAST(j.`date` AS Date)  BETWEEN '{$from_f}' AND '{$to_f}' AND j.`assigned_tech` != 1";

		// paginate
		$sel_query = "
			j.`id` AS j_id,
			j.`service` AS j_service,
			j.`property_id` AS j_property_id,
			j.`work_order`,
			j.`property_vacant`,
			j.`status` AS j_status,
			j.`date` AS j_date,
			j.`created` AS j_created,
			j.`job_type`,

			ajt.`type` AS ajt_type,
			ajt.`short_name` AS ajt_short_name,

			p.`property_id`,
			p.`address_1` AS p_address_1,
			p.`address_2` AS p_address_2,
			p.`address_3` AS p_address_3,
			p.`state` AS p_state,
			p.`postcode` AS p_postcode,
			p.`alarm_code`,
			p.`key_number`,
			p.`pm_id_new`,
			p.`holiday_rental`,
			p.`compass_index_num`,

			aua.`agency_user_account_id`,
			aua.`fname` AS pm_fname,
			aua.`lname` AS pm_lname,
			aua.`email` AS pm_email,
			aua.photo
		";

		$params = array(
			'sel_query' => $sel_query,
			'p_deleted' => 0,
			'a_status' => 'active',
			'del_job' => 0,
			'agency_id' => $agency_id,
			'country_id' => $country_id,
			'pm_id' => $pm_id,
			'j_status' => $j_status,
			'assigned_tech' => 1,
			'search' => $search,
			'custom_where' => $custom_where,
			'sort_list' => array(
				array(
					'order_by' => $order_by,
					'sort' => $sort
				)
			),
			'limit' => $per_page,
			'offset' => $offset,
			'display_query' => 0
		);

		if($pdf_post==1 || $export==1){
			unset($params['limit']);
			unset($params['offset']);
		}

		$jobs_sql = $this->jobs_model->get_jobs($params);

		// PDF
		if( $pdf_post == 1 ){
			$this->load->library('JPDF');
			
			$output_type = $this->input->get_post('output_type');

			// pdf initiation
			$pdf = new JPDF() ;
			$this->config->item('theme') == 'sas' ? $pdf->headerText = 'COMPLETED JOBS' : '';
			// settings
			$pdf->SetTopMargin(40);
			$pdf->SetAutoPageBreak(true,30);
			$pdf->AliasNbPages();
			$pdf->AddPage();

			// header
			$font_size_h = 12;
			$cell_height_h = 10;

			// row
			$font_size = 10;
			$col_width1 = 70;
			$col_width2 = 35;
			$col_width3 = 35;
			$col_width4 = 30;
			$col_width5 = 30;
			$cell_height = 5;


			// get agency
			$params = array(
				'sel_query' => 'a.`agency_name`',
				'agency_id' => $agency_id
			);
			$agency_sql = $this->agency_model->get_agency_data($params);
			$agency = $agency_sql->row();

			//$append_str = '(No dates Selected)';
			if( ( isset($from) && $from != '' ) && ( isset($to) && $to != '' ) ){
				$append_str = "{$from} - {$to}";
			}else{
				$append_str = date('01/m/Y'). "-" .date('t/m/Y');
			}


			$pdf->SetFont('Arial',null,$font_size_h);
			$pdf->Cell($col_width1,$cell_height_h,"Completed Jobs for {$agency->agency_name} {$append_str}");
			$pdf->Ln();

			// body
			$pdf->SetFillColor(211,211,211);
			$pdf->SetFont('Arial','B',$font_size);
			$pdf->Cell($col_width1,$cell_height,'Address',1,null,null,true);
			$pdf->Cell($col_width2,$cell_height,'Property Manager',1,null,null,true);
			$pdf->Cell($col_width3,$cell_height,'Job Type',1,null,null,true);
			$pdf->Cell($col_width4,$cell_height,'Completed Date',1,null,null,true);
			if( $this->gherxlib->isCompassFG( $this->session->agency_id) ){
				$pdf->Cell($col_width5,$cell_height,'Compass Index',1,null,null,true);
			}
			$pdf->Ln();

			$pdf->SetFont('Arial','',$font_size);
			foreach ($jobs_sql->result() as $row){

				$p_address = "{$row->p_address_1} {$row->p_address_2} {$row->p_address_3}";
				$pm_name = ( isset($row->properties_model_id_new) && isset($row->properties_model_id_new) > 0 )?"{$row->properties_model_fname} {$row->properties_model_lname}":null;
				$jdate = ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) )?date('d/m/Y',strtotime($row->j_date)):null;

				$pdf->Cell($col_width1,$cell_height,$p_address,1);
				$pdf->Cell($col_width2,$cell_height,$pm_name,1);
				$pdf->Cell($col_width3,$cell_height,$row->job_type,1);
				$pdf->Cell($col_width4,$cell_height,$jdate,1);

				if( $this->gherxlib->isCompassFG( $this->session->agency_id) ){
					$pdf->Cell($col_width5,$cell_height,$row->compass_index_num,1);
				}

				$pdf->Ln();

			}


			$file_name = 'completed_jobs_report_'.date('YmdHis').'.pdf';
			$pdf->Output($output_type,$file_name);

			// insert log
			if($output_type=='I'){
				$title = 22; // Report Displayed
				$details = "{created_by} displayed Completed Jobs report";
			}elseif ($output_type=='D'){
				$title = 21; // Report Downloaded
				$details = "{created_by} downloaded Completed Jobs report";
			}

			$params = array(
				'title' => $title,
				'details' => $details,
				'display_in_portal' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id
			);

			$this->jcclass->insert_log($params);


		}else if( $export == 1 ){

			// file name
            $date_export = date('YmdHis');
            $filename = "completed_job_{$date_export}.csv";

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename={$filename}");
            header("Pragma: no-cache");
            header("Expires: 0");

            // file creation 
            $csv_file = fopen('php://output', 'w');                    

            //$csv_header = array("Address","Property Manager","Job Type","Completed Date");

			$csv_header = [];

			$csv_header[] = "Address";
			if( $this->system_model->is_hume_housing_agency() == true ){
				$csv_header[] = "Property Code";
			}
			$csv_header[] = "Property Manager";
			$csv_header[] = "Job Type";
			$csv_header[] = "Completed Date";

			if( $this->gherxlib->isCompassFG( $this->session->agency_id) ){
				$csv_header[] = "Compass Index";
			}

            fputcsv($csv_file, $csv_header);
            
            foreach($jobs_sql->result() as $row){ 

                $csv_row = [];  
				$jdate = ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) )?date('d/m/Y',strtotime($row->j_date)):null;
                
                $prop_address = "{$row->p_address_1} {$row->p_address_2}, {$row->p_address_3} {$row->p_state} {$row->p_postcode}";
                                                
                $csv_row[] = $prop_address;
				if( $this->system_model->is_hume_housing_agency() == true ){
					$csv_row[] = $row->compass_index_num;
				}
                $csv_row[] = "{$row->properties_model_fname} {$row->properties_model_lname}";
                $csv_row[] = $row->job_type;
                $csv_row[] = $jdate;     
				
				if( $this->gherxlib->isCompassFG( $this->session->agency_id) ){
					$csv_row[] = $row->compass_index_num;  
				}
                
                fputcsv($csv_file,$csv_row); 

            }
        
            fclose($csv_file); 
            exit; 

		}else{

			$data['list'] = $jobs_sql;

			// all rows
			$sel_query = "j.`id` AS j_id";

			$params = array(
				'sel_query' => $sel_query,
				'p_deleted' => 0,
				'a_status' => 'active',
				'del_job' => 0,
				'agency_id' => $agency_id,
				'country_id' => $country_id,
				'search' => $search,

				'custom_where' => $custom_where,

				'pm_id' => $pm_id,
				'j_status' => $j_status
			);
			$query = $this->jobs_model->get_jobs($params);
			$total_rows = $query->num_rows();


			// header filters
			// PM
			$sel_query = "
				DISTINCT(p.`pm_id_new`),
				aua.`fname`,
				aua.`lname`,
				aua.photo
			";
			$custom_where_pm = "p.`pm_id_new` > 0";

			$params = array(
				'sel_query' => $sel_query,
				'p_deleted' => 0,
				'a_status' => 'active',
				'j_status' => $j_status,
				'del_job' => 0,
				'agency_id' => $agency_id,
				'country_id' => $country_id,
				'custom_where' => $custom_where_pm,
				'search' => $search,
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
			$data['pm_filter'] = $this->jobs_model->get_jobs($params);

			$pagi_links_params_arr = array(
                'pm_id' => $pm_id,
                'from' => $from_f,
                'to' => $to_f,
                'search' => $search,
                'order_by' => $this->input->get_post('order_by'),
				'sort' => $this->input->get_post('sort')
            );
            $pagi_link_params = "{$uri}/?".http_build_query($pagi_links_params_arr);
            $data['pagi_links_params_arr'] = $pagi_links_params_arr;

            // explort link
            $data['export_link'] = "{$uri}/?export=1&".http_build_query($pagi_links_params_arr);


			// pagination settings
			$config['page_query_string'] = TRUE;
			$config['query_string_segment'] = 'offset';
			$config['total_rows'] = $total_rows;
			$config['per_page'] = $per_page;
			$config['base_url'] = $pagi_link_params;

			$this->pagination->initialize($config);

			$data['pagination'] = $this->pagination->create_links();

			// pagination count
			$pc_params = array(
				'total_rows' => $total_rows,
				'offset' => $offset,
				'per_page' => $per_page
			);
			$data['pagi_count'] = $this->jcclass->pagination_count($pc_params);

			// passed to view
			// date
			$data['from'] = $from;
			$data['to'] = $to;

			// sort
			$data['order_by'] = $order_by;
			$data['sort'] = $sort;

			$this->load->view('templates/home_header', $data);
			$this->load->view('reports/completed_jobs', $data);
			$this->load->view('templates/home_footer');

		}

	}




	public function active_services(){

		// title
		$data['title'] = 'Active Services';
		$agency_id = $this->session->agency_id;

		// pagination
		$per_page = $this->config->item('pagi_per_page');
		$offset = $this->input->get_post('offset');

		$pm_id = $this->input->get_post('pm_id');
		$service_type = $this->input->get_post('service_type');
		$pdf_post = $this->input->get_post('pdf');
		$export = $this->input->get_post('export');

		//search
		$condi = array();
		$search_keyword = $this->input->get_post('search');
		if(!empty($pm_id) || !empty($search_keyword)){

			//$condi['search']['pm'] = $pm_id;
			$condi['search']['keyword'] = $search_keyword;

		}

		// paginated
		$sel_query = '
			p.`property_id`,
			p.`address_1` AS p_address_1,
			p.`address_2` AS p_address_2,
			p.`address_3` AS p_address_3,
			p.`state` AS p_state,
			p.`postcode` AS p_postcode,
			p.pm_id_new,
			p.key_number,
			p.compass_index_num,

			ajt.`id` AS ajt_id,
			ajt.`type` AS ajt_type,
			ajt.`short_name` AS ajt_short_name,

			ps.property_services_id,
			ps.alarm_job_type_id,

			aua.`agency_user_account_id`,
			aua.`fname` AS pm_fname,
			aua.`lname` AS pm_lname,
			aua.`email` AS pm_email,
			aua.photo
		';
		$query_params = array(
			'sel_query' => $sel_query,
			'p_deleted' => 0,
			'agency_id' => $this->session->agency_id,
			'ps_service' => 1,
			'ajt_id' => $service_type,
		//	'search' => $search_keyword,
			'pm_id' =>$pm_id,
			'limit' => $per_page,
			'offset' => $offset,

			'sort_list' => array(
				array(
					'order_by' => 'p_address_2',
                    'sort' => 'ASC',
				)
            ),
		);

		if($pdf_post==1 || $export ==1){ ## removed limit and offset for exports
			unset($query_params['limit']);
			unset($query_params['offset']);
		}

		$get_services_query = $this->properties_model->get_property_services($query_params, $condi);

		//added by gherx start here...
		//PDF EXPORT
		if($pdf_post==1){

				// $this->load->library('JPDF');
				// $output_type = $this->input->get_post('output_type');

				// // pdf initiation
				// $pdf = new JPDF();

				$this->load->library('JPDF');
			
				$output_type = $this->input->get_post('output_type');

				$pdf = new JPDF() ;
				$this->config->item('theme') == 'sas' ? $pdf->headerText = 'ACTIVE SERVICES' : '';
				// settings
				$pdf->SetTopMargin(40);
				$pdf->SetAutoPageBreak(true,30);
				$pdf->AliasNbPages();
				$pdf->AddPage();

				// header
				$font_size_h = 12;
				$cell_height_h = 10;

				// row
				$font_size = 10;
				$col_width1 = 85;
				$col_width2 = 40;
				$col_width3 = 60;
				$col_width4 = 30;
				$cell_height = 5;


				// get agency
				$params = array(
					'sel_query' => 'a.`agency_name`',
					'agency_id' => $agency_id
				);
				$agency_sql = $this->agency_model->get_agency_data($params);
				$agency = $agency_sql->row();

				$append_str = '(No dates Selected)';
				if( ( isset($from) && $from != '' ) && ( isset($to) && $to != '' ) ){
					$append_str = "{$from} - {$to}";
				}

				$pdf->SetFont('Arial',null,$font_size_h);
				$pdf->Cell($col_width1,$cell_height_h,"Active Services for {$agency->agency_name} as of ".date('d/m/Y'));
				$pdf->Ln();


				// body
				$pdf->SetFillColor(211,211,211);
				$pdf->SetFont('Arial','B',$font_size);
				$pdf->Cell($col_width1,$cell_height,'Address',1,null,null,true);
				$pdf->Cell($col_width2,$cell_height,'Property Manager',1,null,null,true);
				$pdf->Cell($col_width3,$cell_height,'Service Type',1,null,null,true);
				$pdf->Ln();

				$pdf->SetFont('Arial','',$font_size);
				foreach ($get_services_query->result() as $row){

					$p_address = "{$row->p_address_1} {$row->p_address_2} {$row->p_address_3} {$row->p_state} {$row->p_postcode}";
					$pm_name = ( isset($row->properties_model_id_new) && isset($row->properties_model_id_new) > 0 )?"{$row->properties_model_fname} {$row->properties_model_lname}":null;
					$jdate = ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) )?date('d/m/Y',strtotime($row->j_date)):null;

					$pdf->Cell($col_width1,$cell_height,$p_address,1);
					$pdf->Cell($col_width2,$cell_height,$pm_name,1);
					$pdf->Cell($col_width3,$cell_height,$row->ajt_type,1);
					$pdf->Ln();

				}

				$file_name = 'active_services_report'.date('YmdHis').'.pdf';
				$pdf->Output($output_type,$file_name);


				// insert log
				if($output_type=='I'){
					$title = 22; // Report Displayed
					$details = "{created_by} displayed Active Services report";
				}elseif ($output_type=='D'){
					$title = 21; // Report Downloaded
					$details = "{created_by} downloaded Active Services report";
				}

				$params = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'agency_id' => $this->session->agency_id,
					'created_by' => $this->session->aua_id
				);

				$this->jcclass->insert_log($params);



		}elseif($export==1){
			
			// file name
            $date_export = date('YmdHis');
            $filename = "active_services_{$date_export}.csv";

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename={$filename}");
            header("Pragma: no-cache");
            header("Expires: 0");

            // file creation 
            $csv_file = fopen('php://output', 'w');   

			$csv_header = [];
			$csv_header[] = "Adress";
			if( $this->system_model->is_hume_housing_agency() == true ){
				$csv_header[] = "Property Code";
			}
			$csv_header[] = "Property Manager";
			$csv_header[] = "Service Type";

            fputcsv($csv_file, $csv_header);

			foreach($get_services_query->result() as $row){ 

                $csv_row = [];  

				$p_address = "{$row->p_address_1} {$row->p_address_2} {$row->p_address_3} {$row->p_state} {$row->p_postcode}";
				$pm_name = ( isset($row->properties_model_id_new) && isset($row->properties_model_id_new) > 0 )?"{$row->properties_model_fname} {$row->properties_model_lname}":null;
				$jdate = ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) )?date('d/m/Y',strtotime($row->j_date)):null;

                $csv_row[] = $p_address;
				if( $this->system_model->is_hume_housing_agency() == true ){
					$csv_row[] = $row->compass_index_num;
				}
                $csv_row[] = "{$pm_name}";
                $csv_row[] = $row->ajt_type;  
                
                fputcsv($csv_file,$csv_row); 

            }

			// insert log
			$title = 21; // Report Downloaded
			$details = "{created_by} downloaded Active Services report";

			$params = array(
				'title' => $title,
				'details' => $details,
				'display_in_portal' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id
			);

			$this->jcclass->insert_log($params);
			// insert log end
        
            fclose($csv_file); 
            exit; 

		}else{


			$data['ps'] = $get_services_query;

			// all row
			$sel_query = 'ps.`property_services_id`';
			$query_params = array(
				'sel_query' => $sel_query,
				'p_deleted' => 0,
				'ps_service' => 1,
				'ajt_id' => $service_type,
				'search' => $search_keyword,
				'agency_id' => $this->session->agency_id,
				'pm_id' =>$pm_id
			);
			$get_all = $this->properties_model->get_property_services($query_params, $condi);


			// filter
			// PM
			$sel_query = "
				DISTINCT(p.`pm_id_new`),
				aua.`fname`,
				aua.`lname`,
				aua.photo
			";
			$custom_where_pm = "p.`pm_id_new` > 0";

			$query_params = array(
				'sel_query' => $sel_query,
				'p_deleted' => 0,
				'agency_id' => $this->session->agency_id,
				'ps_service' => 1,
				'custom_where' => $custom_where_pm,
				'limit' => $per_page,
				'offset' => $offset,
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
			$data['pm_filter'] = $this->properties_model->get_property_services($query_params);

			// service type
			$sel_query = "
				DISTINCT(ps.`alarm_job_type_id`),
				ajt.`id`,
				ajt.`type`
			";
			$custom_where_pm = "ps.`alarm_job_type_id` > 0";

			$query_params = array(
				'sel_query' => $sel_query,
				'p_deleted' => 0,
				'agency_id' => $this->session->agency_id,
				'ps_service' => 1,
				'custom_where' => $custom_where_pm,
				'limit' => $per_page,
				'offset' => $offset
			);
			$data['serv_type_filter'] = $this->properties_model->get_property_services($query_params);

			$pagi_base_url_params = array(
				'pm_id' => $pm_id,
				'service_type' => $service_type,
				'search' => $search_keyword
			);
			$pagi_base_url = '/reports/active_services/?'.http_build_query($pagi_base_url_params);

			// pagination settings
			$config['page_query_string'] = TRUE;
			$config['query_string_segment'] = 'offset';
			$config['base_url'] = $pagi_base_url;
			$config['total_rows'] = $get_all->num_rows();
			$config['per_page'] = $per_page;
			$this->pagination->initialize($config);

			// create pagination
			$data['pagination'] = $this->pagination->create_links();

			// pagi counter
			$pagi_params = array(
				'total_rows' => $config['total_rows'],
				'offset' => $offset,
				'per_page' => $per_page
			);
			$data['pagi_count'] = $this->jcclass->pagination_count($pagi_params);

			// view
			$this->load->view('templates/home_header', $data);
			$this->load->view('reports/active_services', $data);
			$this->load->view('templates/home_footer');



		}

		//added by gherx end here...



	}



	public function new_tenancy(){

		$data['title'] = "New Tenancy Report";

		$country_id = $this->session->country_id;
		$agency_id = $this->session->agency_id;

		$pdf_post = $this->input->get_post('pdf');
		$export = $this->input->get_post('export');
		$pm_id = $this->input->get_post('pm_id');
		$search = $this->input->get_post('search');

		// pagination
		$per_page = $this->config->item('pagi_per_page');
		$offset = $this->input->get_post('offset');

		$custom_where = " j.status!='Completed' AND j.status!='Cancelled' AND (j.job_type='Lease Renewal' OR j.job_type='Change of Tenancy') ";

		$sel_query = "
		j.`id` AS j_id,
		j.`service` AS j_service,
		j.`property_id` AS j_property_id,
		j.`work_order`,
		j.`property_vacant`,
		j.`status` AS j_status,
		j.`date` AS j_date,
		j.`created` AS j_created,
		j.`job_type`,
		j.start_date,
		j.due_date,
		j.no_dates_provided,

		ajt.`type` AS ajt_type,
		ajt.`short_name` AS ajt_short_name,

		p.`property_id`,
		p.`address_1` AS p_address_1,
		p.`address_2` AS p_address_2,
		p.`address_3` AS p_address_3,
		p.`state` AS p_state,
		p.`postcode` AS p_postcode,
		p.`alarm_code`,
		p.`key_number`,
		p.`pm_id_new`,
		p.`holiday_rental`,

		aua.`agency_user_account_id`,
		aua.`fname` AS pm_fname,
		aua.`lname` AS pm_lname,
		aua.`email` AS pm_email,
		aua.photo
		";

		$params = array(
			'sel_query' => $sel_query,
			'p_deleted' => 0,
			'a_status' => 'active',
			'del_job' => 0,
			'agency_id' => $agency_id,
			'country_id' => $country_id,

			'pm_id' => $pm_id,
			'search' => $search,

			'custom_where' => $custom_where,

			'limit' => $per_page,
			'offset' => $offset,

			'sort_list' => array(
				array(
					'order_by' => 'p_address_2',
					'sort' => 'ASC'
				)
			)
		);

		if($pdf_post==1 || $export==1){ ##Remove query offset and limit for exports
			unset($params['limit']);
			unset($params['offset']);
		}

		$jobs_sql = $this->jobs_model->get_jobs($params);

		//PDF EXPORT
		if($pdf_post==1){

			// $this->load->library('JPDF');
			// $output_type = $this->input->get_post('output_type');

			// // pdf initiation
			// $pdf = new JPDF();

			$this->load->library('JPDF');
			
			$output_type = $this->input->get_post('output_type');

			$pdf = new JPDF() ;
			$this->config->item('theme') == 'sas' ? $pdf->headerText = 'NEW TENANCY' : '';

			// settings
			$pdf->SetTopMargin(40);
			$pdf->SetLeftMargin(5);
			$pdf->SetAutoPageBreak(true,30);
			$pdf->AliasNbPages();
			$pdf->AddPage();

			// header
			$font_size_h = 12;
			$cell_height_h = 10;

			// row
			$font_size = 7;
			$col_width1 = 60;
			$col_width2 = 27;
			$col_width3 = 30;
			$col_width4 = 18;
			$col_width5 = 18;
			$col_width6 = 28;
			$col_width7 = 18;
			$cell_height = 5;


			// get agency
			$params = array(
				'sel_query' => 'a.`agency_name`',
				'agency_id' => $agency_id
			);
			$agency_sql = $this->agency_model->get_agency_data($params);
			$agency = $agency_sql->row();

			/*$append_str = '(No dates Selected)';
			if( ( isset($from) && $from != '' ) && ( isset($to) && $to != '' ) ){
				$append_str = "{$from} - {$to}";
			}
			*/

			$pdf->SetFont('Arial',null,$font_size_h);
			$pdf->Cell($col_width1,$cell_height_h,"New Tenancy for {$agency->agency_name} as of ".date('d/m/Y'));
			$pdf->Ln();


			// body
			$pdf->SetFillColor(211,211,211);
			$pdf->SetFont('Arial','B',$font_size);
			$pdf->Cell($col_width1,$cell_height,'Address',1,null,null,true);
			$pdf->Cell($col_width2,$cell_height,'Property Manager',1,null,null,true);
			$pdf->Cell($col_width3,$cell_height,'Job Type',1,null,null,true);
			$pdf->Cell($col_width4,$cell_height,'Start Date',1,null,null,true);
			$pdf->Cell($col_width5,$cell_height,'End Date',1,null,null,true);
			$pdf->Cell($col_width6,$cell_height,'Job Status',1,null,null,true);
			$pdf->Cell($col_width7,$cell_height,'Job Date',1,null,null,true);
			$pdf->Ln();

			$pdf->SetFont('Arial','',$font_size);
			foreach ($jobs_sql->result() as $row){

				$p_address = "{$row->p_address_1} {$row->p_address_2} {$row->p_address_3} {$row->p_state} {$row->p_postcode}";
				$pm_name = ( isset($row->properties_model_id_new) && isset($row->properties_model_id_new) > 0 )?"{$row->properties_model_fname} {$row->properties_model_lname}":null;
				$start_date = ($this->jcclass->isDateNotEmpty( $row->start_date ))?date('d/m/Y', strtotime($row->start_date)):'';
				$end_date = ($this->jcclass->isDateNotEmpty( $row->start_date ))?date('d/m/Y', strtotime($row->due_date)):'';
				$jdate = ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) )?date('d/m/Y',strtotime($row->j_date)):'N/A';

				$pdf->Cell($col_width1,$cell_height,$p_address,1);
				$pdf->Cell($col_width2,$cell_height,$pm_name,1);
				$pdf->Cell($col_width3,$cell_height,$row->job_type,1);
				$pdf->Cell($col_width4,$cell_height,$start_date,1);
				$pdf->Cell($col_width5,$cell_height,$end_date,1);
				$pdf->Cell($col_width6,$cell_height,$row->j_status,1);
				$pdf->Cell($col_width7,$cell_height,$jdate,1);
				$pdf->Ln();

			}

			$file_name = 'new_tenancy_report'.date('YmdHis').'.pdf';
			$pdf->Output($output_type,$file_name);

			// insert log
			if($output_type=='I'){
				$title = 22; // Report Displayed
				$details = "{created_by} displayed New Tenancy report";
			}elseif ($output_type=='D'){
				$title = 21; // Report Downloaded
				$details = "{created_by} downloaded New Tenancy report";
			}

			$params = array(
				'title' => $title,
				'details' => $details,
				'display_in_portal' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id
			);

			$this->jcclass->insert_log($params);


		}elseif($export==1){ ## Export CSV

			// file name
			$date_export = date('YmdHis');
			$filename = "new_tenancy_{$date_export}.csv";

			header("Content-type: application/csv");
			header("Content-Disposition: attachment; filename={$filename}");
			header("Pragma: no-cache");
			header("Expires: 0");

			// file creation 
			$csv_file = fopen('php://output', 'w');   

			$csv_header = [];
			$csv_header[] = "Adress";
			$csv_header[] = "Property Manager";
			$csv_header[] = "Job Type";
			$csv_header[] = "Start Date";
			$csv_header[] = "End Date";
			$csv_header[] = "Job Status";
			$csv_header[] = "Job Date";

			fputcsv($csv_file, $csv_header);

			foreach($jobs_sql->result() as $row){ 

				$csv_row = [];  

				$p_address = "{$row->p_address_1} {$row->p_address_2} {$row->p_address_3} {$row->p_state} {$row->p_postcode}";
				$pm_name = ( isset($row->properties_model_id_new) && isset($row->properties_model_id_new) > 0 )?"{$row->properties_model_fname} {$row->properties_model_lname}":null;
				$start_date = ($this->jcclass->isDateNotEmpty( $row->start_date ))?date('d/m/Y', strtotime($row->start_date)):'';
				$end_date = ($this->jcclass->isDateNotEmpty( $row->start_date ))?date('d/m/Y', strtotime($row->due_date)):'';
				$jdate = ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) )?date('d/m/Y',strtotime($row->j_date)):'N/A';

				$csv_row[] = $p_address;
				$csv_row[] = "{$pm_name}";
				$csv_row[] = $row->job_type;
				$csv_row[] = $start_date;
				$csv_row[] = $end_date;
				$csv_row[] = $$row->j_status;
				$csv_row[] = $jdate;
				
				fputcsv($csv_file,$csv_row); 

			}

			// insert log
			$title = 21; // Report Downloaded
			$details = "{created_by} downloaded New Tenancy report";

			$params = array(
				'title' => $title,
				'details' => $details,
				'display_in_portal' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id
			);

			$this->jcclass->insert_log($params);
			// insert log end

			fclose($csv_file); 
			exit; 

		}else{

			$data['job_list'] = $jobs_sql;


			$params = array(
				'sel_query' => $sel_query,
				'p_deleted' => 0,
				'a_status' => 'active',
				'del_job' => 0,
				'agency_id' => $agency_id,
				'country_id' => $country_id,

				'pm_id' => $pm_id,
				'search' => $search,

				'custom_where' => $custom_where
			);
			$query = $this->jobs_model->get_jobs($params);
			$total_rows = $query->num_rows();


			// header filters
			// PM
			$sel_query = "
				DISTINCT(p.`pm_id_new`),
				aua.`fname`,
				aua.`lname`,
				aua.photo
			";
			//$custom_where_pm = "p.`pm_id_new` > 0";
			$custom_where_pm = " p.pm_id_new >0 AND j.status!='Completed' AND j.status!='Cancelled' AND (j.job_type='Fix or Replace' OR j.job_type='Lease Renewal' OR j.job_type='Change of Tenancy') ";

			$params = array(
				'sel_query' => $sel_query,
				'p_deleted' => 0,
				'a_status' => 'active',
				'del_job' => 0,
				'agency_id' => $agency_id,
				'country_id' => $country_id,
				'custom_where' => $custom_where_pm,
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
			$data['pm_filter'] = $this->jobs_model->get_jobs($params);

			$pagi_link_params = array(
				'pm_id' => $pm_id,
				'search' => $search
			);
			$pagi_base_url = '/reports/new_tenancy/?'.http_build_query($pagi_link_params);

			// pagination settings
			$config['page_query_string'] = TRUE;
			$config['query_string_segment'] = 'offset';
			$config['base_url'] = $pagi_base_url;
			$config['total_rows'] = $total_rows;
			$config['per_page'] = $per_page;
			$this->pagination->initialize($config);


			$data['pagination'] = $this->pagination->create_links();

			// pagination count
			$pc_params = array(
				'total_rows' => $total_rows,
				'offset' => $offset,
				'per_page' => $per_page
			);
			$data['pagi_count'] = $this->jcclass->pagination_count($pc_params);

			// view
			$this->load->view('templates/home_header', $data);
			$this->load->view('reports/new_tenancy', $data);
			$this->load->view('templates/home_footer');

		}


	}


	private function addQuotes(&$jobs) {

		if (empty($jobs)) {
			return;
		}

		$jobsById = [];
		$agencyIds = [];
		for ($x = 0; $x < count($jobs); $x++) {
			$job =& $jobs[$x];

			$job->qld_upgrade_quote_amount = $this->config->item('default_qld_upgrade_quote_price') * $job->qld_new_leg_alarm_num;

			$jobsById[$job->j_id] =& $job;

			$agencyIds[] = $job->agency_id;
		}

		$agencyIds = array_unique($agencyIds);

		$this->db->select('aa.`agency_id`, aa.`price`');
		$this->db->from('`agency_alarms` AS aa');
		$this->db->where_in('aa.`agency_id`', $agencyIds);
		$this->db->where('aa.`alarm_pwr_id`', 10);
		$agencyAlarms = $this->db->get()->result();

		for ($x = 0; $x < count($jobs); $x++) {
			$job =& $jobs[$x];

			foreach ($agencyAlarms as $aa) {
				if ($aa->agency_id == $job->agency_id) {
					$job->qld_upgrade_quote_amount = $aa->price * $job->qld_new_leg_alarm_num;
					break;
				}
			}

			if ($job->qld_upgrade_quote_amount == 0) {
				$job->qld_upgrade_quote_amount = $this->config->item('default_qld_upgrade_quote_price') * $job->qld_new_leg_alarm_num;
			}
		}

	}

	public function qld_upgrade_quotes(){

		$this->load->model('encryption_model');
		$data['title'] = 'QLD Upgrade Quotes';

		$country_id = $this->session->country_id;
		$agency_id = $this->session->agency_id;

		$search = $this->input->get_post('search');
		$status = $this->input->get_post('status_filter');

		$pm_id = $this->input->get_post('pm_id');
		$pdf_post = $this->input->get_post('pdf');
		$export = $this->input->get_post('export');
		$j_status = 'Completed';
		$pm_filter = '';
		$search_filter = '';
		$status_filter = '';

		// pagination
		$per_page = $this->config->item('pagi_per_page');
		$offset = ( $this->input->get_post('offset') > 0 )?$this->input->get_post('offset'):0;

		//$special_agency = array(1448,2821,2533,2773,3812,6502); // show all for this agencies, no date filter
		$today = date('Y-m-d');

		if( isset($pm_id) && $pm_id != '' ){
			$pm_filter = " AND `p`.`pm_id_new` = {$pm_id}  ";
		}

		if( $search != '' ){
			$search_filter = " AND  CONCAT_WS(' ', LOWER(p.address_1), LOWER(p.address_2), LOWER(p.address_3), LOWER(p.state), LOWER(p.postcode)) LIKE '%{$search}%' ";
		}

		if($status!=""){
			if($status==2){
				$status_filter = " AND `p`.`qld_upgrade_quote_approved_ts` IS NOT NULL ";
			}elseif($status==1){
				$status_filter = " AND `p`.`qld_upgrade_quote_approved_ts` IS NULL ";
			}
			
		}

		$sql_str = "
		SELECT
			j.`id` AS j_id,
			j.`date` AS j_date,
			j.`service` AS j_service,
			j.`property_id` AS j_property_id,
			j.`work_order`,
			j.`property_vacant`,
			j.`status` AS j_status,
			j.`created` AS j_created,
			j.`job_type`,

			p.`property_id`,
			p.`address_1` AS p_address_1,
			p.`address_2` AS p_address_2,
			p.`address_3` AS p_address_3,
			p.`state` AS p_state,
			p.`postcode` AS p_postcode,
			p.`alarm_code`,
			p.`key_number`,
			p.`pm_id_new`,
			p.`holiday_rental`,
			p.`qld_new_leg_alarm_num`,
			p.`qld_upgrade_quote_approved_ts`,

			al_p.`alarm_make` AS pref_alarm_make,
			al_p.`alarm_pwr_id` AS alarm_pwr_id,

			a.`agency_id`,

			aua.`agency_user_account_id`,
			aua.`fname` AS pm_fname,
			aua.`lname` AS pm_lname,
			aua.`email` AS pm_email,
			aua.photo,

			qa.title AS quote_title
		FROM jobs AS j
		INNER JOIN (

			SELECT j4.property_id, MAX(j4.date) AS latest_date, j4.id
			FROM jobs AS j4
			LEFT JOIN property AS p2 ON j4.property_id = p2.property_id
			LEFT JOIN property_services AS ps2 ON p2.property_id = ps2.property_id
			LEFT JOIN agency AS a2 ON p2.agency_id = a2.agency_id
			WHERE j4.del_job = 0
			AND ( j4.status = '{$j_status}' OR (j4.job_type = 'IC Upgrade' AND p2.qld_upgrade_quote_approved_ts IS NOT NULL) )
			AND a2.country_id = {$country_id}
			AND p2.deleted = 0
			AND a2.status = 'active'
			AND a2.agency_id = {$agency_id}
			AND p2.qld_new_leg_alarm_num >0 AND (p2.prop_upgraded_to_ic_sa = 0 OR p2.prop_upgraded_to_ic_sa IS NULL)
			AND ( (j4.assigned_tech !=1 AND j4.assigned_tech !=2) OR j4.assigned_tech IS NULL )
			AND ps2.service = 1
			ORDER BY MAX(j4.date) DESC

		) AS j3 ON ( j.property_id = j3.property_id AND j.id = j3.id )
		LEFT JOIN `alarm_job_type` AS `ajt` ON j.`service` = ajt.`id`
		LEFT JOIN property AS p ON j.property_id = p.property_id
		LEFT JOIN `alarm_pwr` AS al_p ON p.`preferred_alarm_id` = al_p.`alarm_pwr_id`
		LEFT JOIN `agency_user_accounts` AS `aua` ON p.`pm_id_new` = aua.`agency_user_account_id`
		LEFT JOIN agency AS a ON p.agency_id = a.agency_id
		LEFT JOIN quote_alarms AS qa ON al_p.alarm_pwr_id = qa.alarm_pwr_id
		WHERE j.del_job = 0
		AND ( j.status = '{$j_status}' 	OR (j.job_type = 'IC Upgrade' AND p.qld_upgrade_quote_approved_ts IS NOT NULL) )
		AND a.country_id = {$country_id}
		AND p.deleted = 0
		AND ( p.`is_nlm` = 0 OR p.`is_nlm` IS NULL )
		AND a.status = 'active'
		AND a.agency_id = {$agency_id}
		AND p.qld_new_leg_alarm_num >0
		AND (p.prop_upgraded_to_ic_sa = 0 OR p.prop_upgraded_to_ic_sa IS NULL)
		AND ( (j.assigned_tech !=1 AND j.assigned_tech !=2) OR j.assigned_tech IS NULL )
		AND ( j.date > '".date('Y-m-d', strtotime('-1095 days'))."' OR (j.date IS NULL AND j.job_type = 'IC Upgrade' ) )
		{$pm_filter}
		{$search_filter}
		{$status_filter}
		ORDER BY p.`address_2` ASC
		";

		// PDF
		if( $pdf_post == 1 ){

			$jobs_pdf = $this->db->query($sql_str)->result();

			$this->addQuotes($jobs_pdf);

			// $this->load->library('JPDF');
			// $output_type = $this->input->get_post('output_type');

			// // pdf initiation
			// $pdf = new JPDF();

			$this->load->library('JPDF');
			
			$output_type = $this->input->get_post('output_type');

			$pdf = new JPDF() ;
			$this->config->item('theme') == 'sas' ? $pdf->headerText = 'QLD UPGRADE QUOTES' : '';

			// settings
			$pdf->SetTopMargin(40);
			$pdf->SetAutoPageBreak(true,30);
			$pdf->AliasNbPages();
			$pdf->AddPage();

			// header
			$font_size_h = 12;
			$cell_height_h = 10;

			// row
			$font_size = 10;
			$col_width1 = 80;
			$col_width2 = 40;
			$col_width3 = 40;
			$col_width4 = 30;
			$cell_height = 5;


			// get agency
			$params = array(
				'sel_query' => 'a.`agency_name`',
				'agency_id' => $agency_id
			);
			$agency_sql = $this->agency_model->get_agency_data($params);
			$agency = $agency_sql->row();

			$append_str = '(No dates Selected)';
			if( ( isset($from) && $from != '' ) && ( isset($to) && $to != '' ) ){
				$append_str = "{$from} - {$to}";
			}

			$pdf->SetFont('Arial',null,$font_size_h);
			$pdf->Cell($col_width1,$cell_height_h,"QLD Upgrade Quotes for {$agency->agency_name} {$append_str}");
			$pdf->Ln();

			// body
			$pdf->SetFillColor(211,211,211);
			$pdf->SetFont('Arial','B',$font_size);
			$pdf->Cell($col_width1,$cell_height,'Address',1,null,null,true);
			$pdf->Cell($col_width2,$cell_height,'Property Manager',1,null,null,true);
			$pdf->Cell($col_width3,$cell_height,'Quote Valid Until',1,null,null,true);
			$pdf->Cell($col_width4,$cell_height,'Quote Amount',1,null,null,true);
			$pdf->Ln();

			$pdf->SetFont('Arial','',$font_size);
			foreach ($jobs_pdf as $row){

				$p_address = "{$row->p_address_1} {$row->p_address_2} {$row->p_address_3} {$row->p_state} {$row->p_postcode}";
				$pm_name = ( isset($row->properties_model_id_new) && isset($row->properties_model_id_new) > 0 )?"{$row->properties_model_fname} {$row->properties_model_lname}":null;

				// live
				// Hot Property Management Hendra
				$spec_agency_id = 3759;

				// dev
				// Adams Test Agency
				//$spec_agency_id = 1448;
				if( $agency_id == $spec_agency_id ){
					$jdate = ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) )?date('d/m/Y',strtotime($row->j_date." +9 months")):null;
				}else{
					$jdate = ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) )?date('d/m/Y',strtotime($row->j_date." +90 days")):null;
				}
				

				$quote_amount = $row->qld_upgrade_quote_amount;

				$pdf->Cell($col_width1,$cell_height,$p_address,1);
				$pdf->Cell($col_width2,$cell_height,$pm_name,1);
				$pdf->Cell($col_width3,$cell_height,$jdate,1);
				$pdf->Cell($col_width4,$cell_height,'$'.number_format($quote_amount,2),1, 0, 'R');
				$pdf->Ln();

			}

			$file_name = 'qld_upgrade_quote_report_'.date('YmdHis').'.pdf';
			$pdf->Output($output_type,$file_name);

			// insert log
			if($output_type=='I'){
				$title = 22; // Report Displayed
				$details = "{created_by} displayed QLD Upgrade Quotes report";
			}elseif ($output_type=='D'){
				$title = 21; // Report Downloaded
				$details = "{created_by} downloaded QLD Upgrade Quotes report";
			}

			$params = array(
				'title' => $title,
				'details' => $details,
				'display_in_portal' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id
			);

			$this->jcclass->insert_log($params);


		}elseif($export==1){

			$jobs_export = $this->db->query($sql_str)->result();

			$this->addQuotes($jobs_export);

			// file name
			$date_export = date('YmdHis');
			$filename = "qld_upgrade_quotes_{$date_export}.csv";

			header("Content-type: application/csv");
			header("Content-Disposition: attachment; filename={$filename}");
			header("Pragma: no-cache");
			header("Expires: 0");

			// file creation 
			$csv_file = fopen('php://output', 'w');   

			$csv_header = [];
			$csv_header[] = "Adress";
			$csv_header[] = "Property Manager";
			$csv_header[] = "Quote Valid Until";
			$csv_header[] = "Quote Amount";

			fputcsv($csv_file, $csv_header);

			foreach($jobs_export as $row){ 

				$csv_row = [];

				$p_address = "{$row->p_address_1} {$row->p_address_2} {$row->p_address_3} {$row->p_state} {$row->p_postcode}";
				$pm_name = ( isset($row->properties_model_id_new) && isset($row->properties_model_id_new) > 0 )?"{$row->properties_model_fname} {$row->properties_model_lname}":null;

				// live
				// Hot Property Management Hendra
				$spec_agency_id = 3759;

				if( $agency_id == $spec_agency_id ){
					$jdate = ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) )?date('d/m/Y',strtotime($row->j_date." +9 months")):null;
				}else{
					$jdate = ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) )?date('d/m/Y',strtotime($row->j_date." +90 days")):null;
				}

				$quote_amount = $row->qld_upgrade_quote_amount;

				$csv_row[] = $p_address;
				$csv_row[] = "{$pm_name}";
				$csv_row[] = $jdate;
				$csv_row[] = "$ ".number_format($quote_amount,2);
				
				fputcsv($csv_file,$csv_row); 

			}

			##Insert log
			$title = 21; // Report Downloaded
			$details = "{created_by} downloaded QLD Upgrade Quotes report";

			$params = array(
				'title' => $title,
				'details' => $details,
				'display_in_portal' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id
			);

			$this->jcclass->insert_log($params);
			##Insert log end

			fclose($csv_file); 
			exit; 

		}else{

			$sql_str .= "
			LIMIT {$offset}, {$per_page}
			";

			$jobs = $this->db->query($sql_str)->result();

			//$this->addQuotes($jobs);

			$data['list'] = $jobs;

			## NOTE: When update query here please update query on HOME QLD Upgrage Quotes Count also ##
			$sql_str = "
			SELECT COUNT( j.`id` ) AS jcount
			FROM jobs AS j
			INNER JOIN (

				SELECT j4.property_id, MAX(j4.date) AS latest_date, j4.id
				FROM jobs AS j4
				LEFT JOIN property AS p2 ON j4.property_id = p2.property_id
				LEFT JOIN property_services AS ps2 ON p2.property_id = ps2.property_id
				LEFT JOIN agency AS a2 ON p2.agency_id = a2.agency_id
				WHERE j4.del_job = 0
				AND ( j4.status = '{$j_status}' OR (j4.job_type = 'IC Upgrade' AND p2.qld_upgrade_quote_approved_ts IS NOT NULL) )
				AND a2.country_id = {$country_id}
				AND p2.deleted = 0
				AND a2.status = 'active'
				AND a2.agency_id = {$agency_id}
				AND p2.qld_new_leg_alarm_num >0 AND (p2.prop_upgraded_to_ic_sa = 0 OR p2.prop_upgraded_to_ic_sa IS NULL)
				AND ( (j4.assigned_tech !=1 AND j4.assigned_tech !=2) OR j4.assigned_tech IS NULL )  
				AND ps2.service = 1
				ORDER BY MAX(j4.date) DESC

			) AS j3 ON ( j.property_id = j3.property_id AND j.id = j3.id )
			LEFT JOIN `alarm_job_type` AS `ajt` ON j.`service` = ajt.`id`
			LEFT JOIN property AS p ON j.property_id = p.property_id
			LEFT JOIN `agency_user_accounts` AS `aua` ON p.`pm_id_new` = aua.`agency_user_account_id`
			LEFT JOIN agency AS a ON p.agency_id = a.agency_id
			WHERE j.del_job = 0
			AND ( j.status = '{$j_status}' 	OR (j.job_type = 'IC Upgrade' AND p.qld_upgrade_quote_approved_ts IS NOT NULL) )
			AND a.country_id = {$country_id}
			AND p.deleted = 0
			AND ( p.`is_nlm` = 0 OR p.`is_nlm` IS NULL )
			AND a.status = 'active'
			AND a.agency_id = {$agency_id}
			AND p.qld_new_leg_alarm_num >0 AND (p.prop_upgraded_to_ic_sa = 0 OR p.prop_upgraded_to_ic_sa IS NULL)
			AND ( (j.assigned_tech !=1 AND j.assigned_tech !=2) OR j.assigned_tech IS NULL )
			AND ( j.date > '".date('Y-m-d', strtotime('-1095 days'))."' OR (j.date IS NULL AND j.job_type = 'IC Upgrade' ) )
			{$pm_filter}
			{$search_filter}
			{$status_filter}
			";
			$jobs_tot_sql = $this->db->query($sql_str);
			$total_rows = $jobs_tot_sql->row()->jcount;

			$this->load->library('JPDF');
			$output_type = $this->input->get_post('output_type');


			// header filters
			// PM
			$sql_str = "
			SELECT 	DISTINCT(p.`pm_id_new`),
				aua.`fname`,
				aua.`lname`,
				aua.photo
			FROM jobs AS j
			INNER JOIN (

				SELECT j4.property_id, MAX(j4.date) AS latest_date, j4.id
				FROM jobs AS j4
				LEFT JOIN property AS p2 ON j4.property_id = p2.property_id
				LEFT JOIN agency AS a2 ON p2.agency_id = a2.agency_id
				WHERE j4.del_job = 0
				AND ( j4.status = '{$j_status}' OR (j4.job_type = 'IC Upgrade' AND p2.qld_upgrade_quote_approved_ts IS NOT NULL) )
				AND a2.country_id = {$country_id}
				AND p2.deleted = 0
				AND a2.status = 'active'
				AND a2.agency_id = {$agency_id}
				AND p2.qld_new_leg_alarm_num >0 AND (p2.prop_upgraded_to_ic_sa = 0 OR p2.prop_upgraded_to_ic_sa IS NULL)
				AND (j4.assigned_tech !=1 AND j4.assigned_tech !=2)
				ORDER BY MAX(j4.date) DESC

				) AS j3 ON ( j.property_id = j3.property_id AND j.id = j3.id )
			LEFT JOIN `alarm_job_type` AS `ajt` ON j.`service` = ajt.`id`
			LEFT JOIN property AS p ON j.property_id = p.property_id
			LEFT JOIN `agency_user_accounts` AS `aua` ON p.`pm_id_new` = aua.`agency_user_account_id`
			LEFT JOIN agency AS a ON p.agency_id = a.agency_id
			WHERE j.del_job = 0
			AND ( j.status = '{$j_status}' 	OR (j.job_type = 'IC Upgrade' AND p.qld_upgrade_quote_approved_ts IS NOT NULL) )
			AND a.country_id = {$country_id}
			AND p.deleted = 0
			AND ( p.`is_nlm` = 0 OR p.`is_nlm` IS NULL )
			AND a.status = 'active'
			AND a.agency_id = {$agency_id}
			AND p.qld_new_leg_alarm_num >0 AND (p.prop_upgraded_to_ic_sa = 0 OR p.prop_upgraded_to_ic_sa IS NULL)
			AND (j.assigned_tech !=1 AND j.assigned_tech !=2)
			AND j.date > '".date('Y-m-d', strtotime('-365 days'))."'
			";
			$data['pm_filter'] = $this->db->query($sql_str);

			// get 240v RF brooks agency service
			$has_240v_rf_brooks = false;
			$this->db->select('aa.`agency_id`, aa.`price`, al_p.`alarm_pwr_id`, al_p.`alarm_make`');
			$this->db->from('`agency_alarms` AS aa');
			$this->db->join('`alarm_pwr` AS al_p', 'aa.`alarm_pwr_id` = al_p.`alarm_pwr_id`', 'left');
			$this->db->where('aa.`agency_id`', $agency_id);
			$this->db->where('aa.`alarm_pwr_id`', 10);
			$agency_alarms_sql = $this->db->get();
			if( $agency_alarms_sql->num_rows() ){

				$has_240v_rf_brooks = true;
				$agency_alarms_row = $agency_alarms_sql->row();
				$data['agency_price_240v_rf_brooks'] = $agency_alarms_row->price;
				$data['alarm_pwr_id_240v_rf_brooks'] = $agency_alarms_row->alarm_pwr_id;
				$data['alarm_make_240v_rf_brooks'] = $agency_alarms_row->alarm_make;
				

			}
			$data['has_240v_rf_brooks'] = $has_240v_rf_brooks;
			

			// get 240v RF cavius agency service
			$has_240v_rf_cavius = false;
			$this->db->select('aa.`agency_id`, aa.`price`, al_p.`alarm_pwr_id`, al_p.`alarm_make`');
			$this->db->from('`agency_alarms` AS aa');
			$this->db->join('`alarm_pwr` AS al_p', 'aa.`alarm_pwr_id` = al_p.`alarm_pwr_id`', 'left');
			$this->db->where('aa.`agency_id`', $agency_id);
			$this->db->where('aa.`alarm_pwr_id`', 14);
			$agency_alarms_sql = $this->db->get();			
			if( $agency_alarms_sql->num_rows() ){

				$has_240v_rf_cavius = true;
				$agency_alarms_row = $agency_alarms_sql->row();
				$data['agency_price_240v_rf_cavius'] = $agency_alarms_row->price;
				$data['alarm_pwr_id_240v_rf_cavius'] = $agency_alarms_row->alarm_pwr_id;
				$data['alarm_make_240v_rf_cavius'] = $agency_alarms_row->alarm_make;

			}		
			$data['has_240v_rf_cavius'] = $has_240v_rf_cavius;	


			// get 240v RF emerald agency service
			$has_240v_rf_emerald = false;
			$this->db->select('aa.`agency_id`, aa.`price`, al_p.`alarm_pwr_id`, al_p.`alarm_make`');
			$this->db->from('`agency_alarms` AS aa');
			$this->db->join('`alarm_pwr` AS al_p', 'aa.`alarm_pwr_id` = al_p.`alarm_pwr_id`', 'left');
			$this->db->where('aa.`agency_id`', $agency_id);
			$this->db->where('aa.`alarm_pwr_id`', 22);
			$agency_alarms_sql = $this->db->get();			
			if( $agency_alarms_sql->num_rows() ){

				$has_240v_rf_emerald = true;
				$agency_alarms_row = $agency_alarms_sql->row();
				$data['agency_price_240v_rf_emerald'] = $agency_alarms_row->price;
				$data['alarm_pwr_id_240v_rf_emerald'] = $agency_alarms_row->alarm_pwr_id;
				$data['alarm_make_240v_rf_emerald'] = $agency_alarms_row->alarm_make;

			}		
			$data['has_240v_rf_emerald'] = $has_240v_rf_emerald;


			// pagination settings
			$config['page_query_string'] = TRUE;
			$config['query_string_segment'] = 'offset';
			$config['total_rows'] = $total_rows;
			$config['per_page'] = $per_page;
			$config['base_url'] = "/reports/qld_upgrade_quotes/?pm_id={$pm_id}&search={$search}&status_filter={$status}";

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
			$this->load->view('reports/qld_upgrade_quotes', $data);
			$this->load->view('templates/home_footer');

		}

	}



	public function approved_qld_upgrade_quotes(){

		$this->load->model('encryption_model');
		$data['title'] = 'QLD Approved Quotes';

		$country_id = $this->session->country_id;
		$agency_id = $this->session->agency_id;
		$search = $this->input->get_post('search');
		$j_status_q = $this->input->get_post('show_status');
		//job status tweak
		if($j_status_q == "Completed"){ //completed
			$custom_where_arr_job_status = "j.`status` = 'Completed'";
		}else if($j_status_q == "Incomplete"){ //incomplete
			$custom_where_arr_job_status = "j.`status` != 'Completed'";
		}else{ //all
			$custom_where_arr_job_status = "";
		}
		$j_type = "IC Upgrade";

		$pm_id = $this->input->get_post('pm_id');
		$pdf_post = $this->input->get_post('pdf');
		$export = $this->input->get_post('export');
		//$j_status = 'Completed'; //disable as per Ben's request to show both completed and not completed jobs

		// pagination
		$per_page = $this->config->item('pagi_per_page');
		$offset = $this->input->get_post('offset');

		//$custom_where = "p.`qld_new_leg_alarm_num` > 0 AND p.`qld_upgrade_quote_approved_ts` IS NOT NULL";
		//$custom_where = "p.`qld_new_leg_alarm_num` > 0 AND DATE_ADD(j.`date`, INTERVAL 90 DAY) >= '{$today}'";

		$group_by = 'p.`property_id`';

		// paginate
		$sel_query = "
			MAX(j.`date`) AS j_date,

			j.`id` AS j_id,
			j.`service` AS j_service,
			j.`property_id` AS j_property_id,
			j.`work_order`,
			j.`property_vacant`,
			j.`status` AS j_status,
			j.`created` AS j_created,
			j.`job_type`,

			p.`property_id`,
			p.`address_1` AS p_address_1,
			p.`address_2` AS p_address_2,
			p.`address_3` AS p_address_3,
			p.`state` AS p_state,
			p.`postcode` AS p_postcode,
			p.`alarm_code`,
			p.`key_number`,
			p.`pm_id_new`,
			p.`holiday_rental`,
			p.`qld_new_leg_alarm_num`,
			p.`qld_upgrade_quote_approved_ts`,
			p.`preferred_alarm_id`,

			a.`agency_id`,

			aua.`agency_user_account_id`,
			aua.`fname` AS pm_fname,
			aua.`lname` AS pm_lname,
			aua.`email` AS pm_email,
			aua.photo
		";

		$params = array(
			'sel_query' => $sel_query,
			'p_deleted' => 0,
			'a_status' => 'active',
			'del_job' => 0,
			'agency_id' => $agency_id,
			'country_id' => $country_id,

			'pm_id' => $pm_id,
			'search' => $search,
			'job_type' => $j_type,

			//'custom_where' => $custom_where,
			'custom_where_arr' => array($custom_where_arr_job_status),
			'group_by' => $group_by,

			'limit' => $per_page,
			'offset' => $offset,

			'sort_list' => array(
				array(
					'order_by' => 'p.`address_2`',
					'sort' => 'ASC'
				)
			),
			'display_query' => 0
		);

		$jobs = $this->jobs_model->get_jobs($params)->result();

		if (!empty($jobs)) {
			$jobsById = [];
			$agencyIds = [];
			$preferred_alarm_id_arr = [];
			for ($x = 0; $x < count($jobs); $x++) {
				$job =& $jobs[$x];

				$job->qld_upgrade_quote_amount = $this->config->item('default_qld_upgrade_quote_price') * $job->qld_new_leg_alarm_num;

				$jobsById[$job->j_id] =& $job;

				$agencyIds[] = $job->agency_id;

				$preferred_alarm_id_arr[] = $job->preferred_alarm_id;

			}

			$agencyIds = array_unique($agencyIds);

			$this->db->select('aa.`agency_id`, aa.`alarm_pwr_id`, aa.`price`');
			$this->db->from('`agency_alarms` AS aa');
			$this->db->where('aa.`agency_id`', $agency_id);
			$this->db->where_in('aa.`alarm_pwr_id`', $preferred_alarm_id_arr);
			$agencyAlarms = $this->db->get()->result();

			for ($x = 0; $x < count($jobs); $x++) {
				$job =& $jobs[$x];

				foreach ($agencyAlarms as $aa) {
					if ( $job->preferred_alarm_id == $aa->alarm_pwr_id ) {
						$job->qld_upgrade_quote_amount = $aa->price * $job->qld_new_leg_alarm_num;
						break;
					}
				}
			}
		}

		// PDF
		if( $pdf_post == 1 ){

			// $this->load->library('JPDF');
			// $output_type = $this->input->get_post('output_type');

			// // pdf initiation
			// $pdf = new JPDF();

			$this->load->library('JPDF');
			
			$output_type = $this->input->get_post('output_type');

			$pdf = new JPDF() ;
			$this->config->item('theme') == 'sas' ? $pdf->headerText = 'QLD APPROVED QOUTES' : '';

			// settings
			$pdf->SetTopMargin(40);
			$pdf->SetAutoPageBreak(true,30);
			$pdf->AliasNbPages();
			$pdf->AddPage();

			// header
			$font_size_h = 12;
			$cell_height_h = 10;

			// row
			$font_size = 10;
			$col_width1 = 80;
			$col_width2 = 40;
			$col_width3 = 40;
			$col_width4 = 30;
			$cell_height = 5;


			// get agency
			$params = array(
				'sel_query' => 'a.`agency_name`',
				'agency_id' => $agency_id
			);
			$agency_sql = $this->agency_model->get_agency_data($params);
			$agency = $agency_sql->row();

			$append_str = '(No dates Selected)';
			if( ( isset($from) && $from != '' ) && ( isset($to) && $to != '' ) ){
				$append_str = "{$from} - {$to}";
			}

			$pdf->SetFont('Arial',null,$font_size_h);
			$pdf->Cell($col_width1,$cell_height_h,"QLD Approved Quotes for {$agency->agency_name} {$append_str}");
			$pdf->Ln();

			// body
			$pdf->SetFillColor(211,211,211);
			$pdf->SetFont('Arial','B',$font_size);
			$pdf->Cell($col_width1,$cell_height,'Address',1,null,null,true);
			$pdf->Cell($col_width2,$cell_height,'Property Manager',1,null,null,true);
			$pdf->Cell($col_width3,$cell_height,'Quote Valid Until',1,null,null,true);
			$pdf->Cell($col_width4,$cell_height,'Quote Amount',1,null,null,true);
			$pdf->Ln();

			$pdf->SetFont('Arial','',$font_size);
			foreach ($jobs as $row){

				$p_address = "{$row->p_address_1} {$row->p_address_2} {$row->p_address_3} {$row->p_state} {$row->p_postcode}";
				$pm_name = ( isset($row->properties_model_id_new) && isset($row->properties_model_id_new) > 0 )?"{$row->properties_model_fname} {$row->properties_model_lname}":null;
				$jdate = ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) )?date('d/m/Y',strtotime($row->j_date." +90 days")):null;

				$quote_amount = $row->qld_upgrade_quote_amount;

				$pdf->Cell($col_width1,$cell_height,$p_address,1);
				$pdf->Cell($col_width2,$cell_height,$pm_name,1);
				$pdf->Cell($col_width3,$cell_height,$jdate,1);
				$pdf->Cell($col_width4,$cell_height,'$'.number_format($quote_amount,2),1, 0, 'R');
				$pdf->Ln();

			}

			$file_name = 'qld_upgrade_quote_report_'.date('YmdHis').'.pdf';
			$pdf->Output($output_type,$file_name);

			// insert log
			if($output_type=='I'){
				$title = 22; // Report Displayed
				$details = "{created_by} displayed QLD Upgrade Quotes report";
			}elseif ($output_type=='D'){
				$title = 21; // Report Downloaded
				$details = "{created_by} downloaded QLD Upgrade Quotes report";
			}

			$params = array(
				'title' => $title,
				'details' => $details,
				'display_in_portal' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id
			);

			$this->jcclass->insert_log($params);


		}elseif($export == 1){

			// file name
			$date_export = date('YmdHis');
			$filename = "approved_qld_upgrade_quotes_{$date_export}.csv";

			header("Content-type: application/csv");
			header("Content-Disposition: attachment; filename={$filename}");
			header("Pragma: no-cache");
			header("Expires: 0");

			// file creation 
			$csv_file = fopen('php://output', 'w');   

			$csv_header = [];
			$csv_header[] = "Adress";
			$csv_header[] = "Property Manager";
			$csv_header[] = "Quote Valid Until";
			$csv_header[] = "Quote Amount";

			fputcsv($csv_file, $csv_header);

			foreach($jobs as $row){ 

				$csv_row = [];  
				$p_address = "{$row->p_address_1} {$row->p_address_2} {$row->p_address_3} {$row->p_state} {$row->p_postcode}";
				$pm_name = ( isset($row->properties_model_id_new) && isset($row->properties_model_id_new) > 0 )?"{$row->properties_model_fname} {$row->properties_model_lname}":null;
				$jdate = ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) )?date('d/m/Y',strtotime($row->j_date." +90 days")):null;

				$quote_amount = $row->qld_upgrade_quote_amount;

				$csv_row[] = $p_address;
				$csv_row[] = "{$pm_name}";
				$csv_row[] = $jdate;
				$csv_row[] = number_format($quote_amount,2);
				
				fputcsv($csv_file,$csv_row); 

			}

			// insert log
			$title = 21; // Report Downloaded
			$details = "{created_by} downloaded QLD Upgrade Quotes report";

			$params = array(
				'title' => $title,
				'details' => $details,
				'display_in_portal' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id
			);

			$this->jcclass->insert_log($params);
			// insert log end

			fclose($csv_file); 
			exit; 
			
		}else{

			$data['list'] = $jobs;

			// all rows
			$sel_query = "j.`id` AS j_id";

			$params = array(
				'sel_query' => $sel_query,
				'p_deleted' => 0,
				'a_status' => 'active',
				'del_job' => 0,
				'agency_id' => $agency_id,
				'country_id' => $country_id,
				'search' => $search,
				'job_type' => $j_type,

				//'custom_where' => $custom_where,
				'custom_where_arr' => array($custom_where_arr_job_status),
				'group_by' => $group_by,

				'pm_id' => $pm_id
			);
			// TODO: use COUNT instead
			$query = $this->jobs_model->get_jobs($params);
			$total_rows = $query->num_rows();


			// header filters
			// PM
			$sel_query = "
				DISTINCT(p.`pm_id_new`),
				aua.`fname`,
				aua.`lname`,
				aua.photo
			";

			$params = array(
				'sel_query' => $sel_query,
				'p_deleted' => 0,
				'a_status' => 'active',
				//'j_status' => $j_status,
				'job_type' => $j_type,
				'del_job' => 0,
				'agency_id' => $agency_id,
				'country_id' => $country_id,
				//'custom_where' => $custom_where,
				'custom_where_arr' => array($custom_where_arr_job_status),
				'search' => $search,
				'sort_list' => array(
					array(
						'order_by' => 'aua.`fname`',
						'sort' => 'ASC'
					),
					array(
						'order_by' => 'aua.`lname`',
						'sort' => 'ASC'
					)
				),
				'display_query' => 0
			);
			$data['pm_filter'] = $this->jobs_model->get_jobs($params);


			// pagination settings
			$config['page_query_string'] = TRUE;
			$config['query_string_segment'] = 'offset';
			$config['total_rows'] = $total_rows;
			$config['per_page'] = $per_page;
			$config['base_url'] = "/reports/approved_qld_upgrade_quotes/?pm_id={$pm_id}";

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
			$this->load->view('reports/approved_qld_upgrade_quotes', $data);
			$this->load->view('templates/home_footer');

		}

	}




	public function proceed_with_quote(){

		$property_id = $this->input->post('property_id');
		$amount = $this->input->post('amount');
		$j_service = $this->input->post('j_service');
		$preferred_alarm_id = $this->input->post('preferred_alarm_id');

		if( $j_service > 0 && $property_id > 0 ){

			// get service ID and price
			$prop_serv_sql = $this->db->query("
			SELECT
				`property_services_id`,
				`alarm_job_type_id` AS service_type_id,
				`price`
			FROM `property_services`
			WHERE `property_id` = {$property_id}
			AND `alarm_job_type_id` = {$j_service}
			");

			if( $prop_serv_sql->num_rows() > 0 ){

				$prop_serv_row = $prop_serv_sql->row();

				// DHA NEED PROCESSING check
				$dha_need_processing = 0;
				if( $this->gherxlib->isDHAagenciesV2($this->session->agency_id)==true || $this->gherxlib->agencyHasMaintenanceProgram($this->session->agency_id)==true ){
					$dha_need_processing = 1;
				}

				$jobs_data = array(
					'job_type' => 'IC Upgrade',
					'status' => 'To Be Booked',
					'property_id' => $property_id,
					'service' => $prop_serv_row->service_type_id,
					//'job_price' => $prop_serv_row->price,
					'job_price' => 0,
					'created' => date("Y-m-d H:i:s"),
					'dha_need_processing' => $dha_need_processing
				);

				if( $this->db->insert('jobs', $jobs_data) ){

					$job_id = $this->db->insert_id();

					//UPDATE INVOICE DETAILS
					$this->gherxlib->updateInvoiceDetails($job_id);

					//RUN JOB SYNC
					$this->gherxlib->runJobSync($job_id,$prop_serv_row->service_type_id,$property_id);

					// mark is_eo
					$this->system_model->mark_is_eo($job_id);

					// approve QLD upgrade quote
					$p_data = array(
						'qld_upgrade_quote_approved_ts' => date('Y-m-d H:i:s')
					);
					$this->db->where('property_id', $property_id);
					$this->db->update('property', $p_data);

					// update property preferred_alarm
					if( $property_id > 0 && $preferred_alarm_id > 0 ){
						
						$update_prop_sql_str = "
						UPDATE `property`
						SET `preferred_alarm_id` = {$preferred_alarm_id}
						WHERE `property_id` = {$property_id}
						";
						$this->db->query($update_prop_sql_str);

					}					
					
					// get 240v RF make					
					$this->db->select('al_p.`alarm_pwr_id`, al_p.`alarm_make`');
					$this->db->from('`alarm_pwr` AS al_p');										
					$this->db->where('al_p.`alarm_pwr_id`', $preferred_alarm_id);
					$agency_alarms_sql = $this->db->get();			
					$agency_alarms_row = $agency_alarms_sql->row();								
					
					$log_details = "Accepted quote for $".$amount." using {$agency_alarms_row->alarm_make} and created an IC Upgrade job for {p_address}";
					$params_event_log = array(
						'title' => 39, //Upgrade Quote
						'details' => $log_details,
						'display_in_vpd' => 1,
						'display_in_vad' => 1,
						'display_in_portal' => 1,
						'display_in_vjd' => 1,
						'agency_id' => $this->session->agency_id,
						'created_by' => $this->session->aua_id,
						'property_id' => $property_id,
						'job_id' => $job_id
					);
					$this->jcclass->insert_log($params_event_log);

				}

			}

		}

	}

	private function get_property_managers(){
		$prop_list2 = $this->properties_model->get_property_list($this->session->agency_id);
		$propNotInArray = "";
		foreach($prop_list2 as $notInRow){
			$propNotInArray .= ",".$notInRow->ps_property_id;
		}
		$proNotIn = explode(',',substr($propNotInArray,1));


		//pm filter
		$condi1['pm_distinct'] = 'p.pm_id_new';
		$condi1['group_by'] = 'p.pm_id_new';

		$data = [ "property_managers" => $this->properties_model->get_property_list_non_sats($this->session->agency_id, $proNotIn, $condi1),
				 "prop_not_in" => $proNotIn
				];

		return $data;
	}

	public function not_serviced(){

		$data['title'] = "Not Serviced";
		$condi2 = array();

		//pagiation offset and per page
		$per_page = $this->config->item('pagi_per_page');
		$offset = $this->input->get_post('offset');

		$pdf_post = $this->input->get_post('pdf');
		$export = $this->input->get_post('export');
		$keyword_post = $this->input->get_post('search');
		$pm_post = $this->input->get_post('pm_id');

		$prop_managers = $this->get_property_managers();
		$proNotIn = $prop_managers['prop_not_in']; 

		$data['pm_filter'] = $prop_managers['property_managers']; 


		//search and pm search
		$condi2['search']['keyword'] = $keyword_post;
		$condi2['search']['pm'] = $pm_post;

		//custom main select
		$condi2['sel_query'] = "ps.property_id, p.address_1, p.address_2, p.address_3, p.state, p.postcode, p.property_managers_id as p_property_managers_id, p.pm_id_new, p.nlm_display, a.agency_id, aua.fname as pm_fname, aua.lname as pm_lname, aua.agency_user_account_id, aua.photo";

		//get total rows
		if(!empty($this->properties_model->get_property_list_non_serviced($this->session->agency_id, $proNotIn, $condi2))){
			$total_rows_not_sats = count($this->properties_model->get_property_list_non_serviced($this->session->agency_id, $proNotIn, $condi2));
		}else{
			$total_rows_not_sats = 0;
		}

		//pagination perpage and offset
		//by gherx (add params limit/offset if not pdf)
		$condi2['limit'] = $per_page;
		$condi2['offset'] = $offset;

		if( $pdf_post == 1 || $export==1 ){
			unset($condi2['limit']);
			unset($condi2['offset']);
		}


		//sorting
		$condi2['sort_by'] = 'p.`address_2`';
		$condi2['sort'] = 'ASC';


		// non sats pagination
		$config['page_query_string'] = TRUE;
		$config['query_string_segment'] = 'offset';
		$config['base_url'] = "/reports/not_serviced/?pm_id={$pm_post}&search={$keyword_post}";
		$config['total_rows'] = $total_rows_not_sats;
		$config['per_page'] = $per_page;
		$this->pagination->initialize($config);


		//compliant listing sql
		$non_sats_sql = $this->properties_model->get_property_list_non_serviced($this->session->agency_id, $proNotIn, $condi2);


		//pdf view/download
		if($pdf_post==1){

			// $this->load->library('JPDF');
			// $output_type = $this->input->get_post('output_type');

			// // pdf initiation
			// $pdf = new JPDF();

			$this->config->item('theme') == 'sas' ? $this->load->library('SASPDF') : $this->load->library('JPDF');
			
			$output_type = $this->input->get_post('output_type');

			$pdf = new JPDF() ;
			$this->config->item('theme') == 'sas' ? $pdf->headerText = 'NOT SERVICED' : '';

			// settings
			$pdf->SetTopMargin(40);
			$pdf->SetAutoPageBreak(true,30);
			$pdf->AliasNbPages();
			$pdf->AddPage();

			// header
			$font_size_h = 12;
			$cell_height_h = 10;

			// row
			$font_size = 10;
			$col_width1 = 85;
			$col_width2 = 50;
			$col_width3 = 50;
			$cell_height = 5;


			// get agency
			$params = array(
				'sel_query' => 'a.`agency_name`',
				'agency_id' => $this->session->agency_id
			);
			$agency_sql = $this->agency_model->get_agency_data($params);
			$agency = $agency_sql->row();

			/*$append_str = '(No dates Selected)';
			if( ( isset($from) && $from != '' ) && ( isset($to) && $to != '' ) ){
				$append_str = "{$from} - {$to}";
			}
			*/

			$pdf->SetFont('Arial',null,$font_size_h);
			$pdf->Cell($col_width1,$cell_height_h,"Non-Compliant for {$agency->agency_name} as of ".date('d/m/Y'));
			$pdf->Ln();


			// body
			$pdf->SetFillColor(211,211,211);
			$pdf->SetFont('Arial','B',$font_size);
			$pdf->Cell($col_width1,$cell_height,'Address',1,null,null,true);
			$pdf->Cell($col_width2,$cell_height,'Property Manager',1,null,null,true);
			$pdf->Cell($col_width3,$cell_height,'Service Status',1,null,null,true);
			$pdf->Ln();

			$pdf->SetFont('Arial','',$font_size);
			foreach ($non_sats_sql as $row){

				$p_address = "{$row->address_1} {$row->address_2} {$row->address_3}";
				$pm_name = ( isset($row->properties_model_id_new) && isset($row->properties_model_id_new) > 0 )?"{$row->properties_model_fname} {$row->properties_model_lname}":null;
				//$jdate = ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) )?date('d/m/Y',strtotime($row->j_date)):'N/A';

				$pdf->Cell($col_width1,$cell_height,$p_address,1);
				$pdf->Cell($col_width2,$cell_height,$pm_name,1);
				$pdf->Cell($col_width3,$cell_height,$this->gherxlib->selected_service_label($row->service),1);
				$pdf->Ln();

			}

			$file_name = 'not_serviced_report'.date('YmdHis').'.pdf';
			$pdf->Output($output_type,$file_name);

			// insert log
			if($output_type=='I'){
				$title = 22; // Report Displayed
				$details = "{created_by} displayed Non-Compliant report";
			}elseif ($output_type=='D'){
				$title = 21; // Report Downloaded
				$details = "{created_by} downloaded Non-Compliant report";
			}

			$params = array(
				'title' => $title,
				'details' => $details,
				'display_in_portal' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id
			);

			$this->jcclass->insert_log($params);


		}elseif($export==1){

			// file name
            $date_export = date('YmdHis');
            $filename = "not_serviced_{$date_export}.csv";

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename={$filename}");
            header("Pragma: no-cache");
            header("Expires: 0");

            // file creation 
            $csv_file = fopen('php://output', 'w');   

			$csv_header = [];
			$csv_header[] = "Adress";
			$csv_header[] = "Property Manager";
			$csv_header[] = "Service Status";

            fputcsv($csv_file, $csv_header);

			foreach($non_sats_sql as $row){ 

                $csv_row = [];

				$p_address = "{$row->address_1} {$row->address_2} {$row->address_3}";
				$pm_name = ( isset($row->properties_model_id_new) && isset($row->properties_model_id_new) > 0 )?"{$row->properties_model_fname} {$row->properties_model_lname}":null;
				
                $csv_row[] = $p_address;
                $csv_row[] = "{$pm_name}";
                $csv_row[] = $this->gherxlib->selected_service_label($row->service);
                
                fputcsv($csv_file,$csv_row); 

            }

			## insert log
			$title = 21; // Report Downloaded
			$details = "{created_by} downloaded Not-Serviced report";

			$params = array(
				'title' => $title,
				'details' => $details,
				'display_in_portal' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id
			);

			$this->jcclass->insert_log($params);
			## insert log end
        
            fclose($csv_file); 
            exit; 

		}else{

			 $data['prop_list_not'] = $non_sats_sql;

			//pagination links
			$data['pagi_links_non_sats'] = $this->pagination->create_links(); // NON SATS PAGINATION

			// pagination count
			$pc_params = array(
				'total_rows' => $total_rows_not_sats,
				'offset' => $offset,
				'per_page' => $per_page
			);
			$data['pagi_count'] = $this->jcclass->pagination_count($pc_params);


			$this->load->view('templates/home_header', $data);
			$this->load->view('reports/not_serviced', $data);
			$this->load->view('templates/home_footer');


		}
	}

	private function attachAdditionalProperties(&$properties) {

		if (empty($properties)) {
			return;
		}

		$propertiesById = [];

		for ($x = 0; $x < count($properties); $x++) {
			$property =& $properties[$x];

			$property['property_services'] = [];
			$property['last_service_date'] = null;

			$propertiesById[$property['property_id']] =& $property;
		}

		$propertyIds = array_keys($propertiesById);

		$propertyServiceTypes = $this->db->select("ps.property_services_id, ps.property_id, ps.`service`, ajt.`id` AS ajt_id, ajt.`type`, ajt.`short_name`")
			->from("property_services AS ps")
			->join("alarm_job_type AS ajt", "ajt.id = ps.alarm_job_type_id", "inner")
			->where_in("property_id", $propertyIds)
			->where("service", 1)->get()->result();

		$propertyServiceTypeById = [];
		$alarmJobTypesId = [];

		for ($x = 0; $x < count($propertyServiceTypes); $x++) {
			$propertyServiceType =& $propertyServiceTypes[$x];

			$propertyServiceType->agency_service_count = 0;

			$alarmJobTypesId[] = $propertyServiceType->ajt_id;

			$propertyServiceTypeById[$propertyServiceType->property_services_id] =& $propertyServiceType;
		}

		$alarmJobTypeIds = array_unique($alarmJobTypeIds);

		$agencyServiceCounts = $this->db->select("service_id, COUNT(agency_services_id) AS as_count")
			->from("agency_services")
			->where("agency_id", $this->session->agency_id)
			->where_in("service_id", $alarmJobTypeIds)
			->group_by("service_id")
			->get()->result();

		$agencyServiceCountsById = [];

		for ($x = 0; $x < count($agencyServiceCounts); $x++) {
			$agencyServiceCount = $agencyServiceCounts[$x];

			$agencyServiceCountsById[$agencyServiceCount->service_id] = $agencyServiceCount->as_count;
		}

		for ($x = 0; $x < count($propertyServiceTypes); $x++) {
			$ps =& $propertyServiceTypes[$x];
			if (isset($agencyServiceCountsById[$ps->ajt_id])) {
				$ps->agency_service_count = $agencyServiceCountsById[$ps->ajt_id];
			}
		}

		foreach ($propertyServiceTypes as $pst) {
			$propertiesById[$pst->property_id]['property_services'][] = $pst;
		}

		$latestJobsForProperties = $this->db->select("MAX(date) AS latest, property_id")
			->from('jobs')
			->where_in("property_id", $propertyIds)
			->where("status", "Completed")
			->where("del_job", 0)
			->group_by("property_id")
			->get()->result();

		for ($x = 0; $x < count($latestJobsForProperties); $x++) {
			$propertiesById[$latestJobsForProperties[$x]->property_id]['last_service_date'] = $latestJobsForProperties[$x]->latest;
		}
	}

	public function not_compliant(){

		$data['title'] = "Not Compliant";

		//pagiation offset and per page
		$per_page = $this->config->item('pagi_per_page');
		$offset = $this->input->get_post('offset') > 0 ? $this->input->get_post('offset') : 0;

		$pdf_post = $this->input->get_post('pdf');
		$export = $this->input->get_post('export');

		$search = $this->input->get_post('search');
		$pm_post = $this->input->get_post('pm_id');

		$prop_managers = $this->get_property_managers();
		$data['pm_filter'] = $prop_managers['property_managers']; 


		$tt_params_total = array(
			'sel_query' => "p.property_id",
			'search' => $search,
			'pm_id' => $pm_post,
			'paginate' => NULL
		);
		$total_rows = $this->properties_model->get_no_compliant_prop_for_properties_page($tt_params_total)->num_rows();


		$params['limit'] = $per_page;
		$params['offset'] = $offset;

		$tt_no_compliant_sel = "
					p.property_id as property_id, 
					CONCAT(p.address_1, ' ', p.address_2, ', ',p.address_3) address, 
					p.state, 
					p.postcode, 
					p.property_managers_id as p_property_managers_id, 
					p.pm_id_new, 
					p.agency_id, 
					aua.fname, 
					aua.lname, 
					aua.`fname` AS pm_fname, 
					aua.`lname` AS pm_lname, 
					aua.`email` AS pm_email,
					aua.photo,
					ejn.not_compliant_notes";

			$tt_params = array(
				'sel_query'=> $tt_no_compliant_sel,
				'pm_id' => $pm_post,
				'search' => $search,
				'paginate' => array(
					'offset' => $offset,
					'limit' => $per_page
				),
			);

		$properties = $this->properties_model->get_no_compliant_prop_for_properties_page($tt_params)->result_array();
		$this->attachAdditionalProperties($properties);
		$data['not_compliants'] = $properties;


		// non sats pagination
		$config['page_query_string'] = TRUE;
		$config['query_string_segment'] = 'offset';
		$config['base_url'] = "/reports/not_compliant/?pm_id={$params['prop_manager_id'] }&search={$params['search']}";
		$config['total_rows'] = $total_rows;
		$config['per_page'] = $per_page;
		$this->pagination->initialize($config);


		

		//pdf view/download
		if($pdf_post==1){

			$tt_params = array(
				'sel_query'=> $tt_no_compliant_sel,
				'search' => $search,
				'pm_id' => $pm_post,
				'paginate' => NULL,
			);
			$not_compliant_props = $this->properties_model->get_no_compliant_prop_for_properties_page($tt_params)->result_array();

			// $this->load->library('JPDF');
			// $output_type = $this->input->get_post('output_type');

			// // pdf initiation
			// $pdf = new JPDF();

			$this->load->library('JPDF');
			
			$output_type = $this->input->get_post('output_type');

			$pdf = new JPDF() ;
			$this->config->item('theme') == 'sas' ? $pdf->headerText = 'NON-COMPLIANT' : '';

			// settings
			$pdf->SetTopMargin(40);
			$pdf->SetAutoPageBreak(true,30);
			$pdf->AliasNbPages();
			$pdf->AddPage();

			// header
			$font_size_h = 12;
			$cell_height_h = 10;

			// row
			$font_size = 10;
			$col_width1 = 85;
			$col_width2 = 50;
			$col_width3 = 50;
			$cell_height = 5;


			// get agency
			$params = array(
				'sel_query' => 'a.`agency_name`',
				'agency_id' => $this->session->agency_id
			);
			$agency_sql = $this->agency_model->get_agency_data($params);
			$agency = $agency_sql->row();


			$pdf->SetFont('Arial',null,$font_size_h);
			$pdf->Cell($col_width1,$cell_height_h,"Non-Compliant for {$agency->agency_name} as of ".date('d/m/Y'));
			$pdf->Ln();


			// body
			$pdf->SetFillColor(211,211,211);
			$pdf->SetFont('Arial','B',$font_size);
			$pdf->Cell($col_width1,$cell_height,'Address',1,null,null,true);
			$pdf->Cell($col_width2,$cell_height,'Property Manager',1,null,null,true);
			$pdf->Cell($col_width3,$cell_height,'Service Status',1,null,null,true);
			$pdf->Ln();

			$pdf->SetFont('Arial','',$font_size);
			foreach ($not_compliant_props  as $row){

				$row = (object)$row;
				
				$pm_name = ( isset($row->properties_model_id_new) && isset($row->properties_model_id_new) > 0 )?"{$row->properties_model_fname} {$row->properties_model_lname}":null;

				$pdf->Cell($col_width1,$cell_height,$row->address,1);
				$pdf->Cell($col_width2,$cell_height,$pm_name,1);
				$pdf->Cell($col_width3,$cell_height,$this->gherxlib->selected_service_label($row->service),1);
				$pdf->Ln();

			}

			$file_name = 'not_compliant'.date('YmdHis').'.pdf';
			$pdf->Output($output_type,$file_name);

			// insert log
			if($output_type=='I'){
				$title = 22; // Report Displayed
				$details = "{created_by} displayed Non-Compliant report";
			}elseif ($output_type=='D'){
				$title = 21; // Report Downloaded
				$details = "{created_by} downloaded Non-Compliant report";
			}

			$params = array(
				'title' => $title,
				'details' => $details,
				'display_in_portal' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id
			);

			$this->jcclass->insert_log($params);


		}elseif($export==1){

			$tt_params = array(
				'sel_query'=> $tt_no_compliant_sel,
				'search' => $search,
				'pm_id' => $pm_post,
				'paginate' => NULL,
			);
			$not_compliant_props = $this->properties_model->get_no_compliant_prop_for_properties_page($tt_params)->result_array();

			// file name
            $date_export = date('YmdHis');
            $filename = "not_compliant_{$date_export}.csv";

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename={$filename}");
            header("Pragma: no-cache");
            header("Expires: 0");

            // file creation 
            $csv_file = fopen('php://output', 'w');   

			$csv_header = [];
			$csv_header[] = "Adress";
			$csv_header[] = "Property Manager";
			$csv_header[] = "Service Status";

            fputcsv($csv_file, $csv_header);

			foreach($not_compliant_props  as $row){ 
				$row = (object)$row;

                $csv_row = [];

				$pm_name = ( isset($row->properties_model_id_new) && isset($row->properties_model_id_new) > 0 )?"{$row->properties_model_fname} {$row->properties_model_lname}":null;
				
                $csv_row[] = $row->address;
                $csv_row[] = "{$pm_name}";
                $csv_row[] = $this->gherxlib->selected_service_label($row->service);
                
                fputcsv($csv_file,$csv_row); 

            }

			## insert log
			$title = 21; // Report Downloaded
			$details = "{created_by} downloaded Non-Compliant report";

			$params = array(
				'title' => $title,
				'details' => $details,
				'display_in_portal' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id
			);

			$this->jcclass->insert_log($params);
			## insert log end
        
            fclose($csv_file); 
            exit; 

		}else{

			// $data['prop_list_not'] = $non_sats_sql;

			//pagination links
			$data['pagi_links_non_sats'] = $this->pagination->create_links(); // NON SATS PAGINATION

			// pagination count
			$pc_params = array(
				'total_rows' => $total_rows,
				'offset' => $offset,
				'per_page' => $per_page
			);
			$data['pagi_count'] = $this->jcclass->pagination_count($pc_params);


			$this->load->view('templates/home_header', $data);
			$this->load->view('reports/not_compliant', $data);
			$this->load->view('templates/home_footer');


		}
	}


	public function qld_upgrade(){

		$this->load->model('encryption_model');
		$data['title'] = 'QLD Upgrade';
		$condi = array();

		// pagination
		$per_page = $this->config->item('pagi_per_page');
		$offset = $this->input->get_post('offset');

		$pm_id = $this->input->get_post('pm_id');
		$search_keyword = $this->input->get_post('search');
		$pdf_post = $this->input->get_post('pdf');
		$export = $this->input->get_post('export');

		#new added by Gherx
		#get property that has IC jobs and ixclude in main query
		$ic_job_q = "
			SELECT DISTINCT(property_id) FROM `jobs` 
			WHERE del_job = 0 
			AND job_type = 'IC Upgrade'
			AND status!= 'Cancelled'
		";
		#new added by Gherx end

		// paginated
		$sel_query = '
			complJob.id AS j_id,
			p.`property_id`,
			p.`address_1` AS p_address_1,
			p.`address_2` AS p_address_2,
			p.`address_3` AS p_address_3,
			p.`state` AS p_state,
			p.`postcode` AS p_postcode,
			p.pm_id_new,
			p.key_number,
			p.`qld_new_leg_alarm_num`,
			p.`qld_upgrade_quote_approved_ts`,
			p.`preferred_alarm_id`,

			ajt.`id` AS ajt_id,
			ajt.`type` AS ajt_type,
			ajt.`short_name` AS ajt_short_name,

			ps.property_services_id,
			ps.alarm_job_type_id,

			aua.`agency_user_account_id`,
			aua.`fname` AS pm_fname,
			aua.`lname` AS pm_lname,
			aua.`email` AS pm_email,
			aua.photo
		';

		$custom_where = "p.state='QLD' AND ajt.id!=12 AND ajt.id!=13 AND ajt.id!=14
		AND ( p.qld_new_leg_alarm_num > 0 OR p.qld_new_leg_alarm_num IS NULL )
		AND ( p.prop_upgraded_to_ic_sa = 0 OR p.prop_upgraded_to_ic_sa IS NULL )                
		AND ( p.is_nlm = 0 OR p.is_nlm IS NULL )
		AND p.deleted = 0
		AND p.`is_sales` != 1  
		AND p.qld_upgrade_quote_approved_ts IS NULL
		AND p.property_id NOT IN(
			SELECT DISTINCT(property_id) FROM `jobs` 
			WHERE del_job = 0 
			AND job_type = 'IC Upgrade'
			AND status!= 'Cancelled'
		)
		"; //is QLD and type is not IC

		//custom join added by gherx > to show only completed job AND tech!=1 and 2
		/*$custom_join_sub_query_for_qld_upgrade = " 
			INNER JOIN (
			SELECT j2.property_id,j2.id
		  	FROM jobs AS j2
		  	WHERE j2.status = 'Completed'
		  	AND ( j2.assigned_tech!=1 AND j2.assigned_tech!=2 )
		  	AND j2.del_job = 0
		  	ORDER BY j2.date DESC
		) complJob ON complJob.property_id = p.property_id";*/
		
		//custom join added by gherx > to show only completed job AND tech!=1 and 2
		$custom_join_sub_query_for_qld_upgrade = " 
		(SELECT j2.property_id, j2.id, j2.status, j2.job_type
		FROM jobs AS j2
		WHERE j2.status = 'Completed'
		AND ( j2.assigned_tech!=1 AND j2.assigned_tech!=2 )
		AND j2.del_job = 0
		GROUP By j2.property_id
		ORDER BY j2.date DESC) complJob";

		$query_params = array(
			'sel_query' => $sel_query,
			'custom_join_sub_query_for_qld_upgrade' => $custom_join_sub_query_for_qld_upgrade,
			'p_deleted' => 0,
			'agency_id' => $this->session->agency_id,
			'ps_service' => 1,
			'pm_id' => $pm_id,
			'search' => $search_keyword,
			'custom_where' => $custom_where,
			'limit' => $per_page,
			'offset' => $offset,

			'sort_list' => array(
				array(
					'order_by' => 'p_address_2',
					'sort' => 'ASC'
				)
			),
			'display_query' => 0
		);

		if($export==1 || $pdf_post==1){
			unset($query_params['limit']);
			unset($query_params['offset']);
		}

		$get_services_query = $this->properties_model->get_property_services($query_params);

		if($pdf_post==1){

			// $this->load->library('JPDF');
			// $output_type = $this->input->get_post('output_type');

			// // pdf initiation
			// $pdf = new JPDF();

			$this->load->library('JPDF');
			
			$output_type = $this->input->get_post('output_type');

			$pdf = new JPDF() ;
			$this->config->item('theme') == 'sas' ? $pdf->headerText = 'QLD UPGRADE' : '';

			// settings
			$pdf->SetTopMargin(40);
			$pdf->SetAutoPageBreak(true,30);
			$pdf->AliasNbPages();
			$pdf->AddPage();

			// header
			$font_size_h = 12;
			$cell_height_h = 10;

			// row
			$font_size = 10;
			$col_width1 = 85;
			$col_width2 = 40;
			$col_width3 = 65;
			$cell_height = 5;


			// get agency
			$params = array(
				'sel_query' => 'a.`agency_name`',
				'agency_id' => $this->session->agency_id
			);
			$agency_sql = $this->agency_model->get_agency_data($params);
			$agency = $agency_sql->row();

			/*$append_str = '(No dates Selected)';
			if( ( isset($from) && $from != '' ) && ( isset($to) && $to != '' ) ){
				$append_str = "{$from} - {$to}";
			}
			*/

			$pdf->SetFont('Arial',null,$font_size_h);
			$pdf->Cell($col_width1,$cell_height_h,"QLD Upgrade for {$agency->agency_name} as of ".date('d/m/Y'));
			$pdf->Ln();


			// body
			$pdf->SetFillColor(211,211,211);
			$pdf->SetFont('Arial','B',$font_size);
			$pdf->Cell($col_width1,$cell_height,'Address',1,null,null,true);
			$pdf->Cell($col_width2,$cell_height,'Property Manager',1,null,null,true);
			$pdf->Cell($col_width3,$cell_height,'Service Type',1,null,null,true);
			$pdf->Ln();

			$pdf->SetFont('Arial','',$font_size);
			foreach ($get_services_query->result() as $row){

				// get IC job per property > if ic exist - dont include to export
				$this->db->select('COUNT(id) as ic_count');
				$this->db->from('jobs');
				$this->db->where('del_job',0);
				$this->db->where('property_id',$row->property_id);
				$this->db->where('job_type','IC Upgrade');
				$this->db->where('status!=','Cancelled');
				$j_q_tt = $this->db->get();
				$count_row = $j_q_tt->row()->ic_count;
				// get IC job per property > if ic exist - dont include to export end

				$p_address = "{$row->p_address_1} {$row->p_address_2} {$row->p_address_3} {$row->p_state} {$row->p_postcode}";
				$pm_name = ( isset($row->properties_model_id_new) && isset($row->properties_model_id_new) > 0 )?"{$row->properties_model_fname} {$row->properties_model_lname}":null;
				//$jdate = ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) )?date('d/m/Y',strtotime($row->j_date)):'N/A';

				if( $count_row<=0 ){
					$pdf->Cell($col_width1,$cell_height,$p_address,1);
					$pdf->Cell($col_width2,$cell_height,$pm_name,1);
					$pdf->Cell($col_width3,$cell_height,$row->ajt_type,1);
					$pdf->Ln();
				}

			}

			$file_name = 'qld_upgrade_report'.date('YmdHis').'.pdf';
			$pdf->Output($output_type,$file_name);

			// insert log
			if($output_type=='I'){
				$title = 22; // Report Displayed
				$details = "{created_by} displayed QLD Upgrade report";
			}elseif ($output_type=='D'){
				$title = 21; // Report Downloaded
				$details = "{created_by} downloaded QLD Upgrade report";
			}

			$params = array(
				'title' => $title,
				'details' => $details,
				'display_in_portal' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id
			);

			$this->jcclass->insert_log($params);


		}elseif($export == 1){

			// file name
			$date_export = date('YmdHis');
			$filename = "qld_upgrade_{$date_export}.csv";

			header("Content-type: application/csv");
			header("Content-Disposition: attachment; filename={$filename}");
			header("Pragma: no-cache");
			header("Expires: 0");

			// file creation 
			$csv_file = fopen('php://output', 'w');   

			$csv_header = [];
			$csv_header[] = "Adress";
			$csv_header[] = "Property Manager";
			$csv_header[] = "Service Type";

			fputcsv($csv_file, $csv_header);

			foreach($get_services_query->result() as $row){ 

				// get IC job per property > if ic exist - dont include to export
				$this->db->select('COUNT(id) as ic_count');
				$this->db->from('jobs');
				$this->db->where('del_job',0);
				$this->db->where('property_id',$row->property_id);
				$this->db->where('job_type','IC Upgrade');
				$this->db->where('status!=','Cancelled');
				$j_q_tt = $this->db->get();
				$count_row = $j_q_tt->row()->ic_count;
				// get IC job per property > if ic exist - dont include to export end

				$csv_row = [];

				$p_address = "{$row->p_address_1} {$row->p_address_2} {$row->p_address_3} {$row->p_state} {$row->p_postcode}";
				$pm_name = ( isset($row->properties_model_id_new) && isset($row->properties_model_id_new) > 0 )?"{$row->properties_model_fname} {$row->properties_model_lname}":null;
				
				if( $count_row<=0 ){

					$csv_row[] = $p_address;
					$csv_row[] = "{$pm_name}";
					$csv_row[] = $row->ajt_type;
					
					fputcsv($csv_file,$csv_row); 

				}

			}

			##Insert log
			$title = 21; // Report Downloaded
			$details = "{created_by} downloaded QLD Upgrade report";

			$params = array(
				'title' => $title,
				'details' => $details,
				'display_in_portal' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id
			);

			$this->jcclass->insert_log($params);
			##Insert log end

			fclose($csv_file); 
			exit; 

		}else{

			$data['qld_services_list'] = $get_services_query;

			// all row
			$sel_query = 'ps.`property_services_id`';
			$query_params = array(
				'sel_query' => $sel_query,
				'p_deleted' => 0,
				'ps_service' => 1,
				'pm_id' => $pm_id,
				'search' => $search_keyword,
				'custom_where' => $custom_where,
				'agency_id' => $this->session->agency_id,
				'custom_join_sub_query_for_qld_upgrade' => $custom_join_sub_query_for_qld_upgrade,
			);
			$get_all = $this->properties_model->get_property_services($query_params);


			// filter
			// PM
			$sel_query = "
				DISTINCT(p.`pm_id_new`),
				aua.`fname`,
				aua.`lname`,
				aua.photo
			";

			$custom_where_pm = "p.pm_id_new > 0 AND p.state='QLD' AND ajt.id!=12 AND ajt.id!=13 AND ajt.id!=14
			AND ( p.qld_new_leg_alarm_num > 0 OR p.qld_new_leg_alarm_num IS NULL )
			AND ( p.prop_upgraded_to_ic_sa = 0 OR p.prop_upgraded_to_ic_sa IS NULL )                
			AND ( p.is_nlm = 0 OR p.is_nlm IS NULL )
			AND p.deleted = 0
			AND p.`is_sales` != 1
			";

			$query_params = array(
				'sel_query' => $sel_query,
				'p_deleted' => 0,
				'agency_id' => $this->session->agency_id,
				'ps_service' => 1,
				'custom_where' => $custom_where,
				'limit' => $per_page,
				'offset' => $offset,
				'sort_list' => array(
					array(
						'order_by' => 'aua.`fname`',
						'sort' => 'ASC'
					),
					array(
						'order_by' => 'aua.`lname`',
						'sort' => 'ASC'
					)
					),
					'custom_join_sub_query_for_qld_upgrade' => $custom_join_sub_query_for_qld_upgrade,
			);
			$data['pm_filter'] = $this->properties_model->get_property_services($query_params);

			// get 240v RF brooks agency service
			$has_240v_rf_brooks = false;
			$this->db->select('aa.`agency_id`, aa.`price`, al_p.`alarm_pwr_id`, al_p.`alarm_make`');
			$this->db->from('`agency_alarms` AS aa');
			$this->db->join('`alarm_pwr` AS al_p', 'aa.`alarm_pwr_id` = al_p.`alarm_pwr_id`', 'left');
			$this->db->where('aa.`agency_id`', $this->session->agency_id);
			$this->db->where('aa.`alarm_pwr_id`', 10);
			$agency_alarms_sql = $this->db->get();
			if( $agency_alarms_sql->num_rows() ){

				$has_240v_rf_brooks = true;
				$agency_alarms_row = $agency_alarms_sql->row();
				$data['agency_price_240v_rf_brooks'] = $agency_alarms_row->price;
				$data['alarm_pwr_id_240v_rf_brooks'] = $agency_alarms_row->alarm_pwr_id;
				$data['alarm_make_240v_rf_brooks'] = $agency_alarms_row->alarm_make;

			}
			$data['has_240v_rf_brooks'] = $has_240v_rf_brooks;
			
			// get 240v RF cavius agency service
			$has_240v_rf_cavius = false;
			$this->db->select('aa.`agency_id`, aa.`price`, al_p.`alarm_pwr_id`, al_p.`alarm_make`');
			$this->db->from('`agency_alarms` AS aa');
			$this->db->join('`alarm_pwr` AS al_p', 'aa.`alarm_pwr_id` = al_p.`alarm_pwr_id`', 'left');
			$this->db->where('aa.`agency_id`', $this->session->agency_id);
			$this->db->where('aa.`alarm_pwr_id`', 14);
			$agency_alarms_sql = $this->db->get();			
			if( $agency_alarms_sql->num_rows() ){

				$has_240v_rf_cavius = true;
				$agency_alarms_row = $agency_alarms_sql->row();
				$data['agency_price_240v_rf_cavius'] = $agency_alarms_row->price;
				$data['alarm_pwr_id_240v_rf_cavius'] = $agency_alarms_row->alarm_pwr_id;
				$data['alarm_make_240v_rf_cavius'] = $agency_alarms_row->alarm_make;

			}		
			$data['has_240v_rf_cavius'] = $has_240v_rf_cavius;	

			// get 240v RF emerald agency service
			$has_240v_rf_emerald = false;
			$this->db->select('aa.`agency_id`, aa.`price`, al_p.`alarm_pwr_id`, al_p.`alarm_make`');
			$this->db->from('`agency_alarms` AS aa');
			$this->db->join('`alarm_pwr` AS al_p', 'aa.`alarm_pwr_id` = al_p.`alarm_pwr_id`', 'left');
			$this->db->where('aa.`agency_id`', $this->session->agency_id);
			$this->db->where('aa.`alarm_pwr_id`', 22);
			$agency_alarms_sql = $this->db->get();			
			if( $agency_alarms_sql->num_rows() ){

				$has_240v_rf_emerald = true;
				$agency_alarms_row = $agency_alarms_sql->row();
				$data['agency_price_240v_rf_emerald'] = $agency_alarms_row->price;
				$data['alarm_pwr_id_240v_rf_emerald'] = $agency_alarms_row->alarm_pwr_id;
				$data['alarm_make_240v_rf_emerald'] = $agency_alarms_row->alarm_make;

			}		
			$data['has_240v_rf_emerald'] = $has_240v_rf_emerald;

			// pagination settings
			$config['page_query_string'] = TRUE;
			$config['query_string_segment'] = 'offset';
			$config['base_url'] = "/reports/qld_upgrade/?pm_id={$pm_id}&search={$search_keyword}";
			$config['total_rows'] = $get_all->num_rows();
			$config['per_page'] = $per_page;
			$this->pagination->initialize($config);

			// create pagination
			$data['pagination'] = $this->pagination->create_links();

			// pagi counter
			$pagi_params = array(
				'total_rows' => $config['total_rows'],
				'offset' => $offset,
				'per_page' => $per_page
			);
			$data['pagi_count'] = $this->jcclass->pagination_count($pagi_params);

			$this->load->view('templates/home_header', $data);
			$this->load->view('reports/qld_upgrade', $data);
			$this->load->view('templates/home_footer');

		}



	}



	public function subscription_dates(){

        $data['start_load_time'] = microtime(true);
        $data['title'] = "Subscription Dates";
        $uri = '/reports/subscription_dates';

        $country_id = $this->config->item('country');
		$agency_id = $this->session->agency_id;

		$date_from_filter = ( $this->input->get_post('date_from_filter') != '' )?$this->input->get_post('date_from_filter'):date('Y-m-01',strtotime('+1 month'));
		$date_to_filter = ( $this->input->get_post('date_to_filter') != '' )?$this->input->get_post('date_to_filter'):date('Y-m-t',strtotime('+1 month'));

		$pm_id = $this->input->get_post('pm_id');

		$last_year = date('Y-m-d',strtotime("{$date_from_filter} -12 months"));
		$from = date('Y-m-d',strtotime($last_year));
		$to = date('Y-m-t',strtotime($last_year));

		$pdf_post = $this->input->get_post('pdf');
		$export = $this->input->get_post('export');

		// pagination
        $per_page = $this->config->item('pagi_per_page');
		$offset = $this->input->get_post('offset');


		// list
		$custom_date_filter = "
			j.date BETWEEN '{$from}' AND '{$to}'
			AND a.`allow_upfront_billing` = 1
		";
		$sel_query = "
			j.`id` AS jid,
			j.`property_id`,
			j.`status` AS jstatus,
			j.`date` AS j_date,

			a.`agency_id`,
			a.`agency_name`,

			p.`address_1` AS p_address_1,
			p.`address_2` AS p_address_2,
			p.`address_3` AS p_address_3,
			p.`state` AS p_state,
			p.`postcode` AS p_postcode,

			ajt.`id` AS ajt_id,
			ajt.`type` AS ajt_type,
			ajt.`short_name` AS ajt_short_name
		";

		$jparams = array(
			'sel_query' => $sel_query,
			'del_job' => 0,
			'p_deleted' => 0,
			'a_status' => 'active',
			'country_id' => $country_id,
			'agency_id' => $agency_id,
			'pm_id' => $pm_id,

			'j_status' => 'Completed',
			'job_type' => 'Yearly Maintenance',

			'custom_where_arr' => array(
				$custom_date_filter
			),
			'limit' => $per_page,
			'offset' => $offset,
			'display_query' => 0
		);

		if($export==1 || $pdf_post==1){
			unset($jparams['limit']);
			unset($jparams['offset']);
		}

		$jobs_sql = $this->jobs_model->get_jobs($jparams);

		// PDF
		if( $pdf_post == 1 ){

			// $this->load->library('JPDF');
			// $output_type = $this->input->get_post('output_type');

			// // pdf initiation
			// $pdf = new JPDF();

			$this->load->library('JPDF');
			
			$output_type = $this->input->get_post('output_type');

			$pdf = new JPDF() ;
			$this->config->item('theme') == 'sas' ? $pdf->headerText = 'SUBSCRIPTIONS' : '';

			// settings
			$pdf->SetTopMargin(40);
			$pdf->SetAutoPageBreak(true,30);
			$pdf->AliasNbPages();
			$pdf->AddPage();

			// header
			$font_size_h = 12;
			$cell_height_h = 10;

			// row
			$font_size = 10;
			$col_width1 = 80;
			$col_width2 = 40;
			$col_width3 = 40;
			$col_width4 = 30;
			$cell_height = 5;


			// body
			$pdf->SetFillColor(211,211,211);
			$pdf->SetFont('Arial','B',$font_size);
			$pdf->Cell($col_width1,$cell_height,'Property Address',1,null,null,true);
			$pdf->Cell($col_width3,$cell_height,'Service Type',1,null,null,true);
			$pdf->Cell($col_width4,$cell_height,'Month of Invoice',1,null,null,true);
			$pdf->Ln();

			$pdf->SetFont('Arial','',$font_size);
			foreach ($jobs_sql->result() as $row){

				$p_address = "{$row->p_address_1} {$row->p_address_2} {$row->p_address_3} {$row->p_state} {$row->p_postcode}";
				$month_of_inv = ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) )?date('F Y',strtotime($row->j_date.'+12 months')):null;

				$pdf->Cell($col_width1,$cell_height,$p_address,1);
				$pdf->Cell($col_width3,$cell_height,$row->ajt_type,1);
				$pdf->Cell($col_width4,$cell_height,$month_of_inv,1);
				$pdf->Ln();

			}


			$file_name = 'subscription_dates_'.date('YmdHis').'.pdf';
			$pdf->Output($output_type,$file_name);

			// insert log
			if($output_type=='I'){
				$title = 22; // Report Displayed
				$details = "{created_by} displayed Subscription Dates report";
			}elseif ($output_type=='D'){
				$title = 21; // Report Downloaded
				$details = "{created_by} downloaded Subscription Dates report";
			}

			$params = array(
				'title' => $title,
				'details' => $details,
				'display_in_portal' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id
			);

			$this->jcclass->insert_log($params);


		}elseif($export==1){

			// file name
            $date_export = date('YmdHis');
            $filename = "subscription_dates_{$date_export}.csv";

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename={$filename}");
            header("Pragma: no-cache");
            header("Expires: 0");

            // file creation 
            $csv_file = fopen('php://output', 'w');   

			$csv_header = [];
			$csv_header[] = "Property Adress";
			$csv_header[] = "Service Type";
			$csv_header[] = "Month of Invoice";

            fputcsv($csv_file, $csv_header);

			foreach($jobs_sql->result() as $row){ 

                $csv_row = [];  

				$p_address = "{$row->p_address_1} {$row->p_address_2} {$row->p_address_3} {$row->p_state} {$row->p_postcode}";
				$month_of_inv = ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) )?date('F Y',strtotime($row->j_date.'+12 months')):null;

                $csv_row[] = $p_address;
                $csv_row[] = $row->ajt_type;
                $csv_row[] = $month_of_inv;
                
                fputcsv($csv_file,$csv_row); 

            }

			// insert log
			$title = 21; // Report Downloaded
			$details = "{created_by} downloaded Subscription Dates report";

			$params = array(
				'title' => $title,
				'details' => $details,
				'display_in_portal' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id
			);

			$this->jcclass->insert_log($params);
			// insert log end
        
            fclose($csv_file); 
            exit; 

		}else{

			// get list
			$data['list'] = $jobs_sql;

			// total rows
			$sel_query = "COUNT(j.id) as j_count";
			$jparams = array(
				'sel_query' => $sel_query,
				'del_job' => 0,
				'p_deleted' => 0,
				'a_status' => 'active',
				'country_id' => $country_id,
				'agency_id' => $agency_id,
				'pm_id' => $pm_id,

				'j_status' => 'Completed',
				'job_type' => 'Yearly Maintenance',

				'custom_where_arr' => array(
					$custom_date_filter
				),
				'display_query' => 0
			);

			// TODO: use COUNT instead
			$tot_row_sql = $this->jobs_model->get_jobs($jparams);
			$total_rows = $tot_row_sql->row()->j_count;

			// header filters
			// PM
			$sel_query = "
				DISTINCT(p.`pm_id_new`),
				aua.`fname`,
				aua.`lname`,
				aua.photo
			";
			$custom_where = "p.`pm_id_new` > 0";

			$params = array(
				'sel_query' => $sel_query,
				'del_job' => 0,
				'p_deleted' => 0,
				'a_status' => 'active',
				'country_id' => $country_id,
				'agency_id' => $agency_id,
				'pm_id' => $pm_id,

				'j_status' => 'Completed',
				'job_type' => 'Yearly Maintenance',

				'custom_where_arr' => array(
					$custom_date_filter
				),
				'display_query' => 0
			);
			$data['pm_filter'] = $this->jobs_model->get_jobs($params);



			// pagination settings
			$pagi_links_params_arr = array(
				'agency_filter' => $agency_filter
			);
			$pagi_link_params = $uri.'/?'.http_build_query($pagi_links_params_arr);

			$config['page_query_string'] = TRUE;
			$config['query_string_segment'] = 'offset';
			$config['total_rows'] = $total_rows;
			$config['per_page'] = $per_page;
			$config['base_url'] = $pagi_link_params;

			$this->pagination->initialize($config);

			$data['pagination'] = $this->pagination->create_links();

			// pagination count
			$pc_params = array(
				'total_rows' => $total_rows,
				'offset' => $offset,
				'per_page' => $per_page
			);

			$data['pagi_count'] = $this->jcclass->pagination_count($pc_params);

			$data['uri'] = $uri;
			$data['date_from_filter'] = $date_from_filter;
			$data['date_to_filter'] = $date_to_filter;

			$this->load->view('templates/home_header', $data);
			$this->load->view($uri, $data);
			$this->load->view('templates/home_footer', $data);

		}


	}

	public function key_pick_up(){


		$data['title'] = "Key Pick Up";

		$country_id = $this->session->country_id;
		$agency_id = $this->session->agency_id;

		// pagination
		$per_page = $this->config->item('pagi_per_page');
		$offset = $this->input->get_post('offset');

		$date = ($this->input->get_post('date')!="")? $this->jcclass->formatDate($this->input->get_post('date')):date('Y-m-d');
		$search = $this->input->get_post('search');
		$pdf = $this->input->get_post('pdf');
		$export = $this->input->get_post('export');
		$techId = $this->input->get_post('tech_id');

        $sel_query = "
			kr.tech_run_keys_id as techRun_id,
			kr.date as tech_date,
			kr.action,
			kr.completed_date,
			kr.number_of_keys,
			kr.agency_staff as kr_agency_staff,
			kr.signature_svg,
			a.agency_id as a_id,
			a.agency_name as a_name,
			sa.FirstName as staff_fName,
			sa.LastName as staff_lName
		";

		$params = array(
			'sel_query' => $sel_query,			
			'completed' => 1,
			'date' => $date,
			'agency_id' => $agency_id,
			'tech_id' => $techId,
			'sort_list' => array(
				array(
					'order_by' => 'kr.`date`',
					'sort' => 'DESC'
				)
			),
			'limit' => $per_page,
			'offset' => $offset,
			'display_query' => 0
		);

		if($export==1 || $pdf==1){
			unset($params['limit']);
			unset($params['offset']);
		}

		$keyRunRows = $this->properties_model->getKeyMapRoutes($params)->result_array();

		if($pdf && $pdf ==1){ //pdf view/dl

			$pdf_date = date('d/m/Y', strtotime($date));
			// $this->load->library('JPDF');
			// $output_type = $this->input->get_post('output_type');

			// // pdf initiation
			// $pdf = new JPDF();

			$this->load->library('JPDF');
		
			$output_type = $this->input->get_post('output_type');

			$pdf = new JPDF() ;

			$this->config->item('theme') == 'sas' ? $pdf->headerText = 'KEY PICK-UP' : '';

			// settings
			$pdf->SetTopMargin(40);
			$pdf->SetAutoPageBreak(true,30);
			$pdf->AliasNbPages();
			$pdf->AddPage();

			// header
			$font_size_h = 12;
			$cell_height_h = 10;

			// row
			$font_size = 6;
			$col_width1 = 20;
			$col_width2 = 55;
			$col_width3 = 15;
			$col_width4 = 25;
			$col_width5 = 25;
			$col_width6 = 50;
			$col_width7 = 15;
			$cell_height = 5;

			$pdf->SetFont('Arial',null,$font_size_h);
			$pdf->Cell($col_width1,$cell_height_h,"Key Pick Up for {$pdf_date}");
			$pdf->Ln();

			// body
			$pdf->SetFillColor(211,211,211);
			$pdf->SetFont('Arial','B',$font_size);
			$pdf->Cell($col_width1,$cell_height,'Date',1,null,null,true);
			$pdf->Cell($col_width2,$cell_height,'Technician',1,null,null,true);
			$pdf->Cell($col_width3,$cell_height,'Action',1,null,null,true);
			$pdf->Cell($col_width4,$cell_height,'Time',1,null,null,true);
			$pdf->Cell($col_width5,$cell_height,'Number of Keys',1,null,null,true);
			$pdf->Cell($col_width6,$cell_height,'Agency Staff',1,null,null,true);
			// $pdf->Cell($col_width7,$cell_height,'Signature',1,null,null,true);
			$pdf->Ln();

			$pdf->SetFont('Arial','',$font_size);

			foreach ($keyRunRows as $counter => $row){

				$pdf_full_address = "{$row['address_1']} {$row['address_2']}, {$row['address_3']} {$row['p_state']} {$row['p_postcode']}";
				$pdf_tech = "{$row['FirstName']} {$row['LastName']}";
				$pdf_pm = "{$row['fname']} {$row['lname']}";

				$pdf->Cell($col_width1, $cell_height, date('d/m/Y', strtotime($row['tech_date'])), 1);
				$pdf->Cell($col_width2, $cell_height, "{$row['staff_fName']} {$row['staff_lName']}", 1);
				$pdf->Cell($col_width3, $cell_height, $row['action'], 1);
				$pdf->Cell($col_width4, $cell_height, date('H:i', strtotime($row['completed_date'])), 1);
				$pdf->Cell($col_width5, $cell_height, $row['number_of_keys'], 1);
				$pdf->Cell($col_width6, $cell_height, $row['kr_agency_staff'], 1);
				// if ($row['signature_svg'] != '') {
				// 	$pdf->Cell($col_width7, $cell_height, $pdf->Image($row['signature_svg'], $pdf->GetX(), $pdf->GetY()), 1, 0, 'C');
				// }
				// else {
				// 	$pdf->Cell($col_width6, $cell_height, 'N/A', 1);
				// }

				$pdf->Ln();
			}

			$file_name = 'key_pick_up'.date('YmdHis').'.pdf';
			$pdf->Output($output_type,$file_name);


			// insert log
			if($output_type=='I'){
				$title = 22; // Report Displayed
				$details = "{created_by} displayed Key Pick Up report";
			}elseif ($output_type=='D'){
				$title = 21; // Report Downloaded
				$details = "{created_by} downloaded Key Pick Up report";
			}

			$params = array(
				'title' => $title,
				'details' => $details,
				'display_in_portal' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id
			);

			$this->jcclass->insert_log($params);



		}elseif($export==1){

			// file name
            $date_export = date('YmdHis');
            $filename = "Key_pick_up_{$date_export}.csv";

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename={$filename}");
            header("Pragma: no-cache");
            header("Expires: 0");

            // file creation 
            $csv_file = fopen('php://output', 'w');   

			$csv_header = [];
			$csv_header[] = "Date";
			$csv_header[] = "Technician";
			$csv_header[] = "Action";
			$csv_header[] = "Time";
			$csv_header[] = "Number of Keys ";
			$csv_header[] = "Agency Staff";
		

            fputcsv($csv_file, $csv_header);

			foreach($keyRunRows as $row){ 

                $csv_row = [];  

				$p_address = "{$row->p_address_1} {$row->p_address_2}, {$row->p_address_3} {$row->p_state} {$row->p_postcode}";
				$pm_name = ( isset($row->properties_model_id_new) && isset($row->properties_model_id_new) > 0 )?"{$row->properties_model_fname} {$row->properties_model_lname}":null;
				$jdate =  ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) )?date('d/m/Y',strtotime($row->j_date)):null;

                $csv_row[] = $p_address;
                $csv_row[] = "{$pm_name}";
                $csv_row[] = $jdate;
                $csv_row[] = $jdate;
                $csv_row[] = $jdate;
                $csv_row[] = $jdate;
                
                fputcsv($csv_file,$csv_row); 

            }

			// insert log
			$title = 21; // Report Downloaded
			$details = "{created_by} downloaded Key Pick Up report";

			$params = array(
				'title' => $title,
				'details' => $details,
				'display_in_portal' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id
			);

			$this->jcclass->insert_log($params);
			// insert log end
        
            fclose($csv_file); 
            exit; 

		}else{ // normal listing

			//ALl Rows
			$params = array(

				'country_id' => $country_id,
				'completed' => 1,
				'date' => $date,
				'agency_id' => $agency_id,
				'tech_id' => $techId,

			);
			$allrow = $this->properties_model->getKeyMapRoutesCount($params);
			$total_rows = $allrow->row()->j_count;

			$data['keyRunRows'] = $keyRunRows;

			// pagination settings
			$config['page_query_string'] = TRUE;
			$config['query_string_segment'] = 'offset';
			$config['total_rows'] = $total_rows;
			$config['per_page'] = $per_page;
			$config['base_url'] = "/reports/key_pick_up/?date={$date}&tech_id={$techId}";

			//Tech Filter
			$sel_query = "DISTINCT('sa.StaffID'), sa.StaffID, sa.FirstName as staff_fName, sa.LastName as staff_lName";
			$params = array(
				'sel_query' => $sel_query,
				'country_id' => $country_id,
				'completed' => 1,
				'sort_list' => array(
					array(
						'order_by' => 'sa.FirstName',
						'sort' => 'ASC'
					)
				)
			);
			$data['tech_list'] = $this->properties_model->getKeyMapRoutes($params)->result_array();

			// pagination count
			$pc_params = array(
				'total_rows' => $total_rows,
				'offset' => $offset,
				'per_page' => $per_page
			);
			$data['pagi_count'] = $this->jcclass->pagination_count($pc_params);

		}

		$this->load->view('templates/home_header', $data);
		$this->load->view('reports/key_pick_up', $data);
		$this->load->view('templates/home_footer');
	}

	/**
	 * List unpaid invoice <= 30 days (not overdue)
	 */
	public function unpaid_invoices(){

		//no permit show 404 page
		if(!$this->gherxlib->check_agency_accounts_reports_preference()){
			show_404();
		}

		$this->load->model('encryption_model');

		$country_id = $this->session->country_id;
		$agency_id = $this->session->agency_id;
		$today = date('Y-m-d');

		// pagination
		$per_page = $this->config->item('pagi_per_page');
		$offset = $this->input->get_post('offset');

		$financial_year =  $financial_year = $this->config->item('accounts_financial_year');

		$sel_query = "
			j.`id` AS j_id,
			j.`service` AS j_service,
			j.`property_id` AS j_property_id,
			j.`work_order`,
			j.`property_vacant`,
			j.`status` AS j_status,
			j.`date` AS j_date,
			j.`created` AS j_created,
			j.`job_entry_notice`,
			j.`job_type`,
			j.`invoice_balance`,
			DATE_ADD(j.`date`, INTERVAL 31 DAY) AS due_date,
			DATEDIFF( '{$today}', j.`date`) AS DateDiff,

			ajt.`type` AS ajt_type,
			ajt.`short_name` AS ajt_short_name,

			p.`property_id`,
			p.`address_1` AS p_address_1,
			p.`address_2` AS p_address_2,
			p.`address_3` AS p_address_3,
			p.`state` AS p_state,
			p.`postcode` AS p_postcode,
			p.`alarm_code`,
			p.`key_number`,
			p.`pm_id_new`,
			p.`holiday_rental`,

			aua.`agency_user_account_id`,
			aua.`fname` AS pm_fname,
			aua.`lname` AS pm_lname,
			aua.`email` AS pm_email,
			aua.photo
		";

		$custom_where = "`j`.`invoice_balance` >0
                    AND `j`.`status` = 'Completed'
                    AND a.`status` != 'target'
                    AND (
                            j.`date` >= '$financial_year' OR
                            j.`unpaid` = 1
                    )
		";

		$params = array(
			'sel_query' => $sel_query,
			'p_deleted' => 0,
			'a_status' => 'active',
			'del_job' => 0,
			'agency_id' => $agency_id,
			'country_id' => $country_id,

			'custom_where' => $custom_where,
			'having' => "DateDiff <= 30",

			'sort_list' => array(
				array(
					'order_by' => 'j.created',
					'sort' => 'ASC'
				)
			),
			'display_query' => 0
		);
		$data['list'] = $this->jobs_model->get_jobs($params);

		$data['title'] = "Unpaid Invoices";
		$this->load->view('templates/home_header', $data);
		$this->load->view('reports/unpaid_invoices', $data);
		$this->load->view('templates/home_footer');

	}

	/**
	 * List unpaid overdue invoice >= 31 days
	 */
	public function overdue_invoices(){

		//no permit show 404 page
		if(!$this->gherxlib->check_agency_accounts_reports_preference()){
			show_404();
		}

		$this->load->model('encryption_model');

		$country_id = $this->session->country_id;
		$agency_id = $this->session->agency_id;
		$today = date('Y-m-d');

		// pagination
		$per_page = $this->config->item('pagi_per_page');
		$offset = $this->input->get_post('offset');

		$financial_year =  $financial_year = $this->config->item('accounts_financial_year');

		$sel_query = "
			j.`id` AS j_id,
			j.`service` AS j_service,
			j.`property_id` AS j_property_id,
			j.`work_order`,
			j.`property_vacant`,
			j.`status` AS j_status,
			j.`date` AS j_date,
			j.`created` AS j_created,
			j.`job_entry_notice`,
			j.`job_type`,
			j.`invoice_balance`,
			DATE_ADD(j.`date`, INTERVAL 31 DAY) AS due_date,
			DATEDIFF( '{$today}', j.`date`) AS DateDiff,

			ajt.`type` AS ajt_type,
			ajt.`short_name` AS ajt_short_name,

			p.`property_id`,
			p.`address_1` AS p_address_1,
			p.`address_2` AS p_address_2,
			p.`address_3` AS p_address_3,
			p.`state` AS p_state,
			p.`postcode` AS p_postcode,
			p.`alarm_code`,
			p.`key_number`,
			p.`pm_id_new`,
			p.`holiday_rental`,

			aua.`agency_user_account_id`,
			aua.`fname` AS pm_fname,
			aua.`lname` AS pm_lname,
			aua.`email` AS pm_email,
			aua.photo
		";

		$custom_where = "`j`.`invoice_balance` >0
                    AND `j`.`status` = 'Completed'
                    AND a.`status` != 'target'
                    AND (
                            j.`date` >= '$financial_year' OR
                            j.`unpaid` = 1
                    )
		";

		$params = array(
			'sel_query' => $sel_query,
			'p_deleted' => 0,
			'a_status' => 'active',
			'del_job' => 0,
			'agency_id' => $agency_id,
			'country_id' => $country_id,

			'custom_where' => $custom_where,
			'having' => "DateDiff >= 31",

			'sort_list' => array(
				array(
					'order_by' => 'j.created',
					'sort' => 'ASC'
				)
			),
			'display_query' => 0
		);
		$data['list'] = $this->jobs_model->get_jobs($params);
		$data['agency_state'] = $this->gherxlib->agency_info()->state;

		$data['title'] = "Overdue Invoices";
		$this->load->view('templates/home_header', $data);
		$this->load->view('reports/overdue_invoices', $data);
		$this->load->view('templates/home_footer');

	}

	public function upgraded_properties(){

		$data['title'] = "Upgraded Properties";

		$country_id = $this->session->country_id;
		$agency_id = $this->session->agency_id;
		$pdf = $this->input->get_post('pdf');
		$export = $this->input->get_post('export');

		// pagination
		$per_page = $this->config->item('pagi_per_page');
		$offset = $this->input->get_post('offset');

		//$j_type = "IC Upgrade";

		$custom_where = "p.`qld_new_leg_alarm_num` = 0 AND (j.job_type = 'IC Upgrade' OR j.service=12 OR j.service=13 OR j.service=14)";


		// paginate
		$sel_query = "
			MAX(j.`date`) AS j_date,

			j.`id` AS j_id,
			j.`service` AS j_service,
			j.`property_id` AS j_property_id,
			j.`work_order`,
			j.`property_vacant`,
			j.`status` AS j_status,
			j.`created` AS j_created,
			j.`date` AS j_date,
			j.`job_type`,

			p.`property_id`,
			p.`address_1` AS p_address_1,
			p.`address_2` AS p_address_2,
			p.`address_3` AS p_address_3,
			p.`state` AS p_state,
			p.`postcode` AS p_postcode,
			p.`alarm_code`,
			p.`key_number`,
			p.`pm_id_new`,
			p.`holiday_rental`,
			p.`qld_new_leg_alarm_num`,
			p.`qld_upgrade_quote_approved_ts`,

			a.`agency_id`,

			aua.`agency_user_account_id`,
			aua.`fname` AS pm_fname,
			aua.`lname` AS pm_lname,
			aua.`email` AS pm_email,
			aua.photo
		";

		$params = array(
			'sel_query' => $sel_query,
			'p_deleted' => 0,
			'a_status' => 'active',
			'del_job' => 0,
			'agency_id' => $agency_id,
			'country_id' => $country_id,
			'custom_where' => $custom_where,
			'group_by' => 'p.`property_id`',
			'j_status' => 'Completed',
			'sort_list' => array(
				array(
					'order_by' => 'p.`address_2`',
					'sort' => 'ASC'
				)
			),
			'limit' => $per_page,
			'offset' => $offset,
			'display_query' => 0
		);

		// removed pagination if  pdf DL/Export
		if($pdf==1 || $export==1){
			unset($params['limit']);
			unset($params['offset']);
		}

		$main_query = $this->jobs_model->get_jobs($params);

		if($pdf==1){ // PDF EXPORT/DOWNLOAD HERE...

			// $this->load->library('JPDF');
			// $output_type = $this->input->get_post('output_type');

			// // pdf initiation
			// $pdf = new JPDF();

			$this->load->library('JPDF');
			
			$output_type = $this->input->get_post('output_type');

			$pdf = new JPDF() ;
			$this->config->item('theme') == 'sas' ? $pdf->headerText = 'UPGRADED PROPERTIES' : '';

			// settings
			$pdf->SetTopMargin(40);
			$pdf->SetAutoPageBreak(true,30);
			$pdf->AliasNbPages();
			$pdf->AddPage();

			// header
			$font_size_h = 12;
			$cell_height_h = 10;

			// row
			$font_size = 10;
			$col_width1 = 85;
			$col_width2 = 40;
			$col_width3 = 30;
			$col_width4 = 30;
			$cell_height = 5;

			// get agency
			$params = array(
				'sel_query' => 'a.`agency_name`',
				'agency_id' => $agency_id
			);
			$agency_sql = $this->agency_model->get_agency_data($params);
			$agency = $agency_sql->row();

			$pdf->SetFont('Arial',null,$font_size_h);
			$pdf->Cell($col_width1,$cell_height_h,"Upgraded Properties for {$agency->agency_name} as of ".date('d/m/Y'));
			$pdf->Ln();

			// body
			$pdf->SetFillColor(211,211,211);
			$pdf->SetFont('Arial','B',$font_size);
			$pdf->Cell($col_width1,$cell_height,'Address',1,null,null,true);
			$pdf->Cell($col_width2,$cell_height,'Property Manager',1,null,null,true);
			$pdf->Cell($col_width3,$cell_height,'Completed Date',1,null,null,true);
			$pdf->Ln();
			$pdf->SetFont('Arial','',$font_size);

			foreach ($main_query->result() as $row){

				$p_address = "{$row->p_address_1} {$row->p_address_2}, {$row->p_address_3} {$row->p_state} {$row->p_postcode}";
				$pm_name = ( isset($row->properties_model_id_new) && isset($row->properties_model_id_new) > 0 )?"{$row->properties_model_fname} {$row->properties_model_lname}":null;
				$jdate =  ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) )?date('d/m/Y',strtotime($row->j_date)):null;

				$pdf->Cell($col_width1,$cell_height,$p_address,1);
				$pdf->Cell($col_width2,$cell_height,$pm_name,1);
				$pdf->Cell($col_width3,$cell_height,$jdate,1);
				$pdf->Ln();

			}

			$file_name = 'Upgraded_properties_'.date('YmdHis').'.pdf';
			$pdf->Output($output_type,$file_name);

		}elseif($export==1){
			
			// file name
            $date_export = date('YmdHis');
            $filename = "upgraded_properties_{$date_export}.csv";

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename={$filename}");
            header("Pragma: no-cache");
            header("Expires: 0");

            // file creation 
            $csv_file = fopen('php://output', 'w');   

			$csv_header = [];
			$csv_header[] = "Adress";
			$csv_header[] = "Property Manager";
			$csv_header[] = "Completed Date";

            fputcsv($csv_file, $csv_header);

			foreach($main_query->result() as $row){ 

                $csv_row = [];  

				$p_address = "{$row->p_address_1} {$row->p_address_2}, {$row->p_address_3} {$row->p_state} {$row->p_postcode}";
				$pm_name = ( isset($row->properties_model_id_new) && isset($row->properties_model_id_new) > 0 )?"{$row->properties_model_fname} {$row->properties_model_lname}":null;
				$jdate =  ( isset($row->j_date) && $this->jcclass->isDateNotEmpty($row->j_date) )?date('d/m/Y',strtotime($row->j_date)):null;

                $csv_row[] = $p_address;
                $csv_row[] = "{$pm_name}";
                $csv_row[] = $jdate;
                
                fputcsv($csv_file,$csv_row); 

            }

			// insert log
			$title = 21; // Report Downloaded
			$details = "{created_by} downloaded Active Services report";

			$params = array(
				'title' => $title,
				'details' => $details,
				'display_in_portal' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id
			);

			$this->jcclass->insert_log($params);
			// insert log end
        
            fclose($csv_file); 
            exit; 

		}else{ // NORMAL MAIN LISTING HERE...

			//main listing
			$data['lists'] = $main_query;

			//total rows
			$sel_query = "j.`id` AS j_id,p.`property_id`,";
			$total_params = array(
				'sel_query' => $sel_query,
				'p_deleted' => 0,
				'a_status' => 'active',
				'del_job' => 0,
				'agency_id' => $agency_id,
				'country_id' => $country_id,
				'custom_where' => $custom_where,
				'group_by' => 'p.`property_id`',
				'j_status' => 'Completed'
			);
			$query = $this->jobs_model->get_jobs($total_params);
			$total_rows = $query->num_rows();

			// pagination settings
			$config['page_query_string'] = TRUE;
			$config['query_string_segment'] = 'offset';
			$config['total_rows'] = $total_rows;
			$config['per_page'] = $per_page;
			$config['base_url'] = "/reports/upgraded_properties";

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
			$this->load->view('reports/upgraded_properties', $data);
			$this->load->view('templates/home_footer');

		}

	}


	public function expiring_alarms_hume(){

		$data['title'] = 'Expiring Alarms';

		$country_id = $this->session->country_id;
		$agency_id = $this->session->agency_id;
		$uri = '/reports/expiring_alarms_hume';
        $data['uri'] = $uri;

		$pm_filter = $this->db->escape_str($this->input->get_post('pm_filter'));
		$alarm_expiry = $this->db->escape_str($this->input->get_post('alarm_expiry'));
		$pdf = $this->db->escape_str($this->input->get_post('pdf'));

		$query_filter = null;

		// pagination
		$per_page = $this->config->item('pagi_per_page');
        $offset = ( $this->input->get_post('offset') != '' )?$this->input->get_post('offset'):0;

		if( $pm_filter != '' ){
			$query_filter .= " AND p.`pm_id_new` = '{$pm_filter}' ";
		}


		if( $alarm_expiry != '' ){

			$query_filter .= " AND al.`expiry` = '{$alarm_expiry}' ";

			// main listing
			$list_sql_str = "
			SELECT
				COUNT(al.`alarm_id`) AS al_qty,
				COUNT(
					CASE
						WHEN al.`alarm_power_id` = 1
						OR al.`alarm_power_id` = 3
						OR al.`alarm_power_id` = 5
						OR al.`alarm_power_id` = 6
						OR al.`alarm_power_id` = 7
						OR al.`alarm_power_id` = 8
						OR al.`alarm_power_id` = 12
						OR al.`alarm_power_id` = 13
						THEN al.`alarm_id`
					END
				)  AS al_9v_count,
				COUNT(
					CASE
						WHEN al.`alarm_power_id` = 2
						OR al.`alarm_power_id` = 4
						OR al.`alarm_power_id` = 9
						OR al.`alarm_power_id` = 10
						OR al.`alarm_power_id` = 11
						OR al.`alarm_power_id` = 14
						THEN al.`alarm_id`
					END
				) AS al_240v_count,

				p.`property_id`,
				p.`address_1` AS p_street_num,
				p.`address_2` AS p_street_name,
				p.`address_3` AS p_suburb,
				p.`state` AS p_state,
				p.`postcode` AS p_postcode,
				p.`pm_id_new`,

				pm.`agency_user_account_id` AS aua_id,
				pm.`fname` AS pm_fname,
				pm.`lname` AS pm_lname
			FROM `alarm` AS al
			LEFT JOIN `alarm_pwr` AS al_pwr ON al.`alarm_power_id` = al_pwr.`alarm_pwr_id`
			LEFT JOIN `jobs` AS j ON al.`job_id` = j.`id`
			INNER JOIN (

				SELECT j_inner.property_id, MAX(j_inner.date) AS latest_date
				FROM `jobs` AS j_inner
				LEFT JOIN `property` AS p_inner ON j_inner.`property_id` = p_inner.`property_id`
				LEFT JOIN `agency` AS a_inner ON p_inner.`agency_id` = a_inner.`agency_id`
				WHERE j_inner.`del_job` = 0
				AND p_inner.`deleted` = 0
				AND a_inner.`status` = 'active'
				AND j_inner.`status` = 'Completed'
				AND j_inner.`assigned_tech` NOT IN(1,2)
				AND a_inner.`agency_id` = {$agency_id}
				GROUP BY j_inner.`property_id` DESC

			) AS j3 ON ( j.property_id = j3.property_id AND j.date = j3.latest_date )
			LEFT JOIN `property` AS p ON j.`property_id` = p.`property_id`
			LEFT JOIN `agency` AS a ON p.`agency_id` = a.`agency_id`
			LEFT JOIN `agency_user_accounts` AS pm ON p.`pm_id_new` = pm.`agency_user_account_id`
			WHERE j.`del_job` = 0
			AND p.`deleted` = 0
			AND a.`status` = 'active'
			AND al.`ts_discarded` = 0
			AND j.`assigned_tech` NOT IN(1,2)
			AND a.`agency_id` = {$agency_id}
			{$query_filter}
			GROUP BY p.`property_id`
			LIMIT {$offset}, {$per_page}
			";
			$list_sql = $this->db->query($list_sql_str);


			// get total
			$list_sql_str = "
			SELECT COUNT(al.`alarm_id`) AS al_qty
			FROM `alarm` AS al
			LEFT JOIN `alarm_pwr` AS al_pwr ON al.`alarm_power_id` = al_pwr.`alarm_pwr_id`
			LEFT JOIN `jobs` AS j ON al.`job_id` = j.`id`
			INNER JOIN (

				SELECT j_inner.property_id, MAX(j_inner.date) AS latest_date
				FROM `jobs` AS j_inner
				LEFT JOIN `property` AS p_inner ON j_inner.`property_id` = p_inner.`property_id`
				LEFT JOIN `agency` AS a_inner ON p_inner.`agency_id` = a_inner.`agency_id`
				WHERE j_inner.`del_job` = 0
				AND p_inner.`deleted` = 0
				AND a_inner.`status` = 'active'
				AND j_inner.`status` = 'Completed'
				AND j_inner.`assigned_tech` NOT IN(1,2)
				AND a_inner.`agency_id` = {$agency_id}
				GROUP BY j_inner.`property_id` DESC

			) AS j3 ON ( j.property_id = j3.property_id AND j.date = j3.latest_date )
			LEFT JOIN `property` AS p ON j.`property_id` = p.`property_id`
			LEFT JOIN `agency` AS a ON p.`agency_id` = a.`agency_id`
			LEFT JOIN `agency_user_accounts` AS pm ON p.`pm_id_new` = pm.`agency_user_account_id`
			WHERE j.`del_job` = 0
			AND p.`deleted` = 0
			AND a.`status` = 'active'
			AND al.`ts_discarded` = 0
			AND j.`assigned_tech` NOT IN(1,2)
			AND a.`agency_id` = {$agency_id}
			{$query_filter}
			GROUP BY p.`property_id`
			";
			$tot_row_list_sql = $this->db->query($list_sql_str);
			$total_rows = $tot_row_list_sql->num_rows();

			// get expiring alarm total
			$list_sql_str = "
			SELECT
				COUNT(al.`alarm_id`) AS al_qty,
				COUNT(
					CASE
						WHEN al.`alarm_power_id` = 1
						OR al.`alarm_power_id` = 3
						OR al.`alarm_power_id` = 5
						OR al.`alarm_power_id` = 6
						OR al.`alarm_power_id` = 7
						OR al.`alarm_power_id` = 8
						OR al.`alarm_power_id` = 12
						OR al.`alarm_power_id` = 13
						THEN al.`alarm_id`
					END
				)  AS al_9v_count,
				COUNT(
					CASE
						WHEN al.`alarm_power_id` = 2
						OR al.`alarm_power_id` = 4
						OR al.`alarm_power_id` = 9
						OR al.`alarm_power_id` = 10
						OR al.`alarm_power_id` = 11
						OR al.`alarm_power_id` = 14
						THEN al.`alarm_id`
					END
				) AS al_240v_count
			FROM `alarm` AS al
			LEFT JOIN `alarm_pwr` AS al_pwr ON al.`alarm_power_id` = al_pwr.`alarm_pwr_id`
			LEFT JOIN `jobs` AS j ON al.`job_id` = j.`id`
			INNER JOIN (

				SELECT j_inner.property_id, MAX(j_inner.date) AS latest_date
				FROM `jobs` AS j_inner
				LEFT JOIN `property` AS p_inner ON j_inner.`property_id` = p_inner.`property_id`
				LEFT JOIN `agency` AS a_inner ON p_inner.`agency_id` = a_inner.`agency_id`
				WHERE j_inner.`del_job` = 0
				AND p_inner.`deleted` = 0
				AND a_inner.`status` = 'active'
				AND j_inner.`status` = 'Completed'
				AND j_inner.`assigned_tech` NOT IN(1,2)
				AND a_inner.`agency_id` = {$agency_id}
				GROUP BY j_inner.`property_id` DESC

			) AS j3 ON ( j.property_id = j3.property_id AND j.date = j3.latest_date )
			LEFT JOIN `property` AS p ON j.`property_id` = p.`property_id`
			LEFT JOIN `agency` AS a ON p.`agency_id` = a.`agency_id`
			LEFT JOIN `agency_user_accounts` AS pm ON p.`pm_id_new` = pm.`agency_user_account_id`
			WHERE j.`del_job` = 0
			AND p.`deleted` = 0
			AND a.`status` = 'active'
			AND al.`ts_discarded` = 0
			AND j.`assigned_tech` NOT IN(1,2)
			AND a.`agency_id` = {$agency_id}
			{$query_filter}
			";
			$total_list_sql = $this->db->query($list_sql_str);
			$tot_exp_al = $total_list_sql->row()->al_qty;
			$tot_exp_9v = $total_list_sql->row()->al_9v_count;
			$tot_exp_240v = $total_list_sql->row()->al_240v_count;


			if($pdf && $pdf ==1){ //pdf view/dl


				$this->load->library('JPDF');
				$output_type = $this->input->get_post('output_type');

				// pdf initiation
				$pdf = new JPDF();

				// settings
				$pdf->SetTopMargin(40);
				$pdf->SetAutoPageBreak(true,30);
				$pdf->AliasNbPages();
				$pdf->AddPage();

				// row
				$font_size = 10;
				$col_width1 = 80;
				$col_width2 = 35;
				$col_width3 = 25;
				$col_width4 = 25;
				$col_width5 = 27;
				$cell_height = 5;

				// body
				$pdf->SetFillColor(211,211,211);
				$pdf->SetFont('Arial','B',$font_size);
				$pdf->Cell($col_width1,$cell_height,'Address',1,null,null,true);
				$pdf->Cell($col_width2,$cell_height,'Property Manager',1,null,null,true);
				$pdf->Cell($col_width3,$cell_height,'Alarm Total',1,null,null,true);
				$pdf->Cell($col_width4,$cell_height,'9v expiring',1,null,null,true);
				$pdf->Cell($col_width5,$cell_height,'240v expiring',1,null,null,true);
				$pdf->Ln();

				$pdf->SetFont('Arial','',$font_size);
				$counter = 1;
				foreach ( $list_sql->result() as $row ){

					$prop_address = "{$row->p_street_num} {$row->p_street_name} {$row->suburb} {$row->p_state} {$row->p_postcode}";
					$pm = "{$row->properties_model_fname} {$row->properties_model_lname}";

					$pdf->Cell($col_width1,$cell_height,$prop_address,1);
					$pdf->Cell($col_width2,$cell_height,$pm,1);
					$pdf->Cell($col_width3,$cell_height,$row->al_qty,1);
					$pdf->Cell($col_width4,$cell_height,$row->al_9v_count,1);
					$pdf->Cell($col_width5,$cell_height,$row->al_240v_count,1);

					$pdf->Ln();

					$counter ++;
				}

				$pdf->SetFont('Arial','B',$font_size);
				$pdf->Cell($col_width1,$cell_height,'TOTAL',1);
				$pdf->Cell($col_width2,$cell_height,'',1);
				$pdf->Cell($col_width3,$cell_height,$tot_exp_al,1);
				$pdf->Cell($col_width4,$cell_height,$tot_exp_9v,1);
				$pdf->Cell($col_width5,$cell_height,$tot_exp_240v,1);
				$pdf->SetFont('Arial','',$font_size);


				$file_name = 'expiring_alarm_'.date('YmdHis').rand().'.pdf';
				$pdf->Output($output_type,$file_name);

			}else{ // normal listing

				// main listing
				$data['list_sql'] = $list_sql;

				// total alarms
				$data['tot_exp_al'] = $tot_exp_al;
				$data['tot_exp_9v'] = $tot_exp_9v;
				$data['tot_exp_240v'] = $tot_exp_240v;

			}


		}

		// get property managers
		$data['pm_sql'] = $this->db->query("
		SELECT
			`agency_user_account_id` AS aua_id,
			`fname`,
			`lname`,
			`active`
		FROM `agency_user_accounts`
		WHERE `agency_id` = {$agency_id}
		ORDER BY `fname`, `lname`
		");

		// pagination links
		$pagi_links_params_arr = array(
			'pm_filter' => $pm_filter,
			'alarm_expiry' => $this->input->get_post('alarm_expiry')
		);
		$pagi_link_params = $uri.'/?'.http_build_query($pagi_links_params_arr);


		// pagination
		$config['page_query_string'] = TRUE;
		$config['query_string_segment'] = 'offset';
		$config['total_rows'] = $total_rows;
		$config['per_page'] = $per_page;
		$config['base_url'] = $pagi_link_params;

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
		$this->load->view($uri, $data);
		$this->load->view('templates/home_footer');


	}

	public function stra(){


		// pagination
		$per_page = $this->config->item('pagi_per_page');
		$offset = ( $this->input->get_post('offset') != '' )?$this->input->get_post('offset'):0;

		$pm_id = $this->input->get_post('pm_id');
		$search_keyword = $this->input->get_post('search');
		$pdf_post = $this->input->get_post('pdf');
		$export = $this->input->get_post('export');

		$compliant = $this->input->get_post('compliant');

		// compliant filter
		$compliant_sql_str = null;
		$sql_filter_arr = [];
		if( $compliant == 1 ){			
			$sql_filter_arr[] = "AND ( p.`holiday_rental` = 1 AND nsw_pc.`short_term_rental_compliant` = 1 )";
			$title = 'STRA Compliant';
		}else{			
			$sql_filter_arr[] = "AND NOT ( p.`holiday_rental` = 1 AND nsw_pc.`short_term_rental_compliant` = 1 )";
			$title = 'STRA Non-Compliant';
		}

		$uri = "/reports/stra";
        $data['uri'] = $uri;
		$data['title'] = $title;
		

		// PM filter
		//if( $pm_id > 0 ){			
		if( isset($pm_id) && $pm_id!="" ){			
			$sql_filter_arr[] = "AND p.`pm_id_new` = {$pm_id}";
		}

		// search filter
		if( $search_keyword != '' ){			
			$sql_filter_arr[] = "AND CONCAT_WS(' ', LOWER(p.`address_1`), LOWER(p.`address_2`), LOWER(p.`address_3`), LOWER(p.`state`), LOWER(p.`postcode`)) LIKE '%{$search_keyword}%'";
		}

		if( count($sql_filter_arr) > 0 ){
			$sql_filter_imp = implode(' ',$sql_filter_arr);
		}

		// paginated
		$sql_str = "
		SELECT 
			p.`property_id`, 
			p.`address_1` AS `p_address_1`, 
			p.`address_2` AS `p_address_2`, 
			p.`address_3` AS `p_address_3`, 
			p.`state` AS `p_state`, 
			p.`postcode` AS `p_postcode`, 			
			p.`pm_id_new`, 
			p.`key_number`, 	
			
			nsw_pc.`short_term_rental_compliant`,
			nsw_pc.`req_num_alarms` AS nsw_leg_num_alarms,
			nsw_pc.`req_heat_alarm`,
			
			aua.`agency_user_account_id`, 
			aua.`fname` AS `pm_fname`, 
			aua.`lname` AS `pm_lname`, 
			aua.`email` AS `pm_email`, 
			aua.`photo`
		FROM `property` AS p
		LEFT JOIN `nsw_property_compliance` as nsw_pc ON p.`property_id` = nsw_pc.`property_id`
		LEFT JOIN `agency_user_accounts` AS aua ON p.`pm_id_new` = aua.`agency_user_account_id`		
		WHERE p.`agency_id` = {$this->session->agency_id}
		AND p.`deleted` = 0		
		AND p.`state` = 'NSW'
		{$sql_filter_imp}
		ORDER BY p.`address_2` ASC
		";		

		if($export!=1){
			$sql_str .= "LIMIT {$offset}, {$per_page}";
		}

		//$data['list'] = $this->db->query($sql_str);
		$main_query_list = $this->db->query($sql_str);

		if($export==1){

			// file name
			if($compliant==1){
				$export_name = 'compliant';
			}else{
				$export_name = 'not_compliant';
			}

           // file name
		   $date_export = date('YmdHis');
		   $filename = "{$export_name}_{$date_export}.csv";

		   header("Content-type: application/csv");
		   header("Content-Disposition: attachment; filename={$filename}");
		   header("Pragma: no-cache");
		   header("Expires: 0");

		   // file creation 
		   $csv_file = fopen('php://output', 'w');   

		   $csv_header = [];
		   $csv_header[] = "Adress";
		   $csv_header[] = "Property Manager";

		   fputcsv($csv_file, $csv_header);

		   foreach($main_query_list->result() as $row){ 

			   $csv_row = [];  

			   $p_address = "{$row->p_address_1} {$row->p_address_2} {$row->p_address_3} {$row->p_state} {$row->p_postcode}";
			   $pm_name = ( isset($row->properties_model_id_new) && isset($row->properties_model_id_new) > 0 )?"{$row->properties_model_fname} {$row->properties_model_lname}":null;

			   $csv_row[] = $p_address;
			   $csv_row[] = "{$pm_name}";
			   
			   fputcsv($csv_file,$csv_row); 

		   }

		   // insert log
		   $title = 21; // Report Downloaded
		   $details = "{created_by} downloaded {$export_name} report";

		   $params = array(
			   'title' => $title,
			   'details' => $details,
			   'display_in_portal' => 1,
			   'agency_id' => $this->session->agency_id,
			   'created_by' => $this->session->aua_id
		   );

		   $this->jcclass->insert_log($params);
		   // insert log end
	   
		   fclose($csv_file); 
		   exit; 

		}else{

			$data['list'] = $main_query_list;

			// all row
			$sql_str = "
			SELECT COUNT(p.`property_id`) AS p_count
			FROM `property` AS p
			LEFT JOIN `nsw_property_compliance` as nsw_pc ON p.`property_id` = nsw_pc.`property_id`
			LEFT JOIN `agency_user_accounts` AS aua ON p.`pm_id_new` = aua.`agency_user_account_id`		
			WHERE p.`agency_id` = {$this->session->agency_id}
			AND p.`deleted` = 0		
			AND p.`state` = 'NSW'
			{$sql_filter_imp}
			";
			$get_all = $this->db->query($sql_str);
			$total_rows = $get_all->row()->p_count;

			// disctinct PM filter
			$sql_str = "
			SELECT 
				DISTINCT(p.`pm_id_new`),
				aua.`fname`,
				aua.`lname`,
				aua.`photo`
			FROM `property` AS p
			LEFT JOIN `nsw_property_compliance` as nsw_pc ON p.`property_id` = nsw_pc.`property_id`
			LEFT JOIN `agency_user_accounts` AS aua ON p.`pm_id_new` = aua.`agency_user_account_id`		
			WHERE p.`agency_id` = {$this->session->agency_id}
			AND p.`deleted` = 0		
			AND p.`state` = 'NSW'
			{$sql_filter_imp}
			";
			$data['pm_filter'] = $this->db->query($sql_str);
			
			$pagi_links_params_arr = array(
				'compliant' => $compliant,
				'pm_id' => $pm_id,
				'search' => $search_keyword
			);
			$pagi_link_params = $uri.'/?'.http_build_query($pagi_links_params_arr);


			// pagination settings
			$config['page_query_string'] = TRUE;
			$config['query_string_segment'] = 'offset';
			$config['base_url'] = $pagi_link_params;
			$config['total_rows'] = $total_rows;
			$config['per_page'] = $per_page;
			$this->pagination->initialize($config);

			// create pagination
			$data['pagination'] = $this->pagination->create_links();

			// pagi counter
			$pagi_params = array(
				'total_rows' => $config['total_rows'],
				'offset' => $offset,
				'per_page' => $per_page
			);
			$data['pagi_count'] = $this->jcclass->pagination_count($pagi_params);

			$this->load->view('templates/home_header', $data);
			$this->load->view('reports/stra', $data);
			$this->load->view('templates/home_footer');

		}

	}


	public function hume_job_logs() {

		$hume_house_agency_id = 1598; // Hume Housing     
        //$hume_house_agency_id = 1448; // adams     

		if( $this->session->agency_id == $hume_house_agency_id ){

			$data['start_load_time'] = microtime(true);
			$data['title'] = "Hume Job Logs";
			$country_id = $this->config->item('country');
			$uri = '/reports/hume_job_logs';
			$data['uri'] = $uri;		

			// pagination
			$per_page = $this->config->item('pagi_per_page');
			$offset = ($this->input->get_post('offset')!="")?$this->input->get_post('offset'):0;			

			$export = $this->input->get_post('export');						 

			// export should show all
			$limit_sql_str = null;
			if ( $export != 1 ){ 
				$limit_sql_str = "LIMIT {$offset}, {$per_page}";
			}
		

			// get paginated list
			$job_log_main_sql_str = "
			(
				SELECT
					DISTINCT(p.`property_id`),
					p.`address_1`,
					p.`address_2`,
					p.`address_3`,
					p.`state`,
					p.`postcode`
				FROM `job_log` AS jl
				LEFT JOIN `jobs` AS j ON jl.`job_id` = j.`id` 
				LEFT JOIN `property` AS p ON j.`property_id` = p.`property_id`
				LEFT JOIN `agency` AS a ON p.`agency_id` = a.`agency_id`
				WHERE a.`agency_id` = {$this->session->agency_id}                
				AND j.`status` = 'To Be Booked'
				AND j.`del_job` = 0
				AND p.`deleted` = 0                    
				AND jl.`deleted` = 0                                        
				AND jl.`contact_type` IN (
					'Phone Call',
					'E-mail',
					'SMS Sent',
					'Work Order',
					'Unavailable',
					'Problematic',
					'SMS Received',
					'Duplicate Property'
				)                        
			)   
			UNION
			(
				SELECT
					DISTINCT(p.`property_id`),
					p.`address_1`,
					p.`address_2`,
					p.`address_3`,
					p.`state`,
					p.`postcode`
				FROM `logs` AS l
				LEFT JOIN `jobs` AS j ON l.`job_id` = j.`id` 
				LEFT JOIN `property` AS p ON j.`property_id` = p.`property_id`
				LEFT JOIN `agency` AS a ON p.`agency_id` = a.`agency_id`
				WHERE a.`agency_id` = {$this->session->agency_id}               
				AND j.`status` = 'To Be Booked'
				AND j.`del_job` = 0
				AND p.`deleted` = 0                    
				AND l.`deleted` = 0                                        
				AND l.`title` IN (40,78)                        
			) 
			ORDER BY address_2 ASC, address_1 ASC
			{$limit_sql_str}       
			";   

			$job_log_main_sql = $this->db->query($job_log_main_sql_str)->result();

			// get all property ID
			$property_id_arr = [];
			foreach ( $job_log_main_sql as $row ){          
				$property_id_arr[] = $row->property_id;
			}

			if( count($property_id_arr) > 0 ){

				$property_id_imp = implode(",",$property_id_arr);

				// get old logs via contact type and get new logs via SMS(40) and Email(78) sent 
				$job_log_merge_sql_str = "
				(
					SELECT
						jl.`comments` AS jl_comments,
						jl.`eventdate` AS jl_date,
				
						p.`property_id`,
						p.`address_1`,
						p.`address_2`,
						p.`address_3`,
						p.`state`,
						p.`postcode`,
						p.`compass_index_num`
					FROM `job_log` AS jl
					LEFT JOIN `jobs` AS j ON jl.`job_id` = j.`id` 
					LEFT JOIN `property` AS p ON j.`property_id` = p.`property_id`
					LEFT JOIN `agency` AS a ON p.`agency_id` = a.`agency_id`
					WHERE a.`agency_id` = {$this->session->agency_id}    
					AND p.`property_id` IN({$property_id_imp})
					AND j.`status` = 'To Be Booked'
					AND j.`del_job` = 0
					AND p.`deleted` = 0                    
					AND jl.`deleted` = 0                                        
					AND jl.`contact_type` IN (
						'Phone Call',
						'E-mail',
						'SMS Sent',
						'Work Order',
						'Unavailable',
						'Problematic',
						'SMS Received',
						'Duplicate Property'
					)                        
				)   
				UNION
				(
					SELECT
						l.`details` AS jl_comments,
						DATE(l.`created_date`) AS jl_date,
				
						p.`property_id`,
						p.`address_1`,
						p.`address_2`,
						p.`address_3`,
						p.`state`,
						p.`postcode`,
						p.`compass_index_num`
					FROM `logs` AS l
					LEFT JOIN `jobs` AS j ON l.`job_id` = j.`id` 
					LEFT JOIN `property` AS p ON j.`property_id` = p.`property_id`
					LEFT JOIN `agency` AS a ON p.`agency_id` = a.`agency_id`
					WHERE a.`agency_id` = {$this->session->agency_id}   
					AND p.`property_id` IN({$property_id_imp})
					AND j.`status` = 'To Be Booked'
					AND j.`del_job` = 0
					AND p.`deleted` = 0                    
					AND l.`deleted` = 0                                        
					AND l.`title` IN (40,78)                        
				) 
				ORDER BY address_2 ASC, address_1 ASC, jl_date DESC
				";  
	
				$job_log_merge_sql = $this->db->query($job_log_merge_sql_str)->result();

			}

			// merge/join two queries
			$merge_query_arr = [];
			foreach ($job_log_main_sql as $postcode_row) {
	
				// merged job logs logs   
				$count = 0;      
				$log_limit = 6;   
				foreach ($job_log_merge_sql as $job_log_merge_row) {
									
					if ( $postcode_row->property_id == $job_log_merge_row->property_id ) { // match   
	
						$count++;
						
						// get row object
						$merge_query_arr[] = $job_log_merge_row; 
						if( $count == $log_limit ){
							break;
						}                                                                            
	
					}                
	
				}
	
			} 
				
			
			if ( $export == 1 ) { // EXPORT         

				// file name
				$date_export = date('YmdHis');
				$filename = "hume_job_logs_export_{$date_export}.csv";

				header("Content-type: application/csv");
				header("Content-Disposition: attachment; filename={$filename}");
				header("Pragma: no-cache");
				header("Expires: 0");

				// file creation 
				$csv_file = fopen('php://output', 'w');            

				$header = array("Property Address","Property Code","Log","Log Date");
				fputcsv($csv_file, $header);            
										
				foreach ( $merge_query_arr as $row_inner ){

					$csv_row = []; 

					$prop_address = "{$row_inner->address_1} {$row_inner->address_2}, {$row_inner->address_3}";

					$csv_row[] = $prop_address;
					$csv_row[] = $row_inner->compass_index_num;   
					$csv_row[] = strip_tags($row_inner->jl_comments);          
					$csv_row[] = date("d/m/Y", strtotime($row_inner->jl_date));  
					
					fputcsv($csv_file,$csv_row); 

				}
									
				fclose($csv_file); 
				exit; 

			}else{             

				$data['list'] = $job_log_main_sql;
				$data['page_query'] = $job_log_main_sql_str;

				// get all
				$property_sql_str = "
				(
					SELECT
						DISTINCT(p.`property_id`),
						p.`address_1`,
						p.`address_2`,
						p.`address_3`,
						p.`state`,
						p.`postcode`
					FROM `job_log` AS jl
					LEFT JOIN `jobs` AS j ON jl.`job_id` = j.`id` 
					LEFT JOIN `property` AS p ON j.`property_id` = p.`property_id`
					LEFT JOIN `agency` AS a ON p.`agency_id` = a.`agency_id`
					WHERE a.`agency_id` = {$this->session->agency_id}                
					AND j.`status` = 'To Be Booked'
					AND j.`del_job` = 0
					AND p.`deleted` = 0                    
					AND jl.`deleted` = 0                                        
					AND jl.`contact_type` IN (
						'Phone Call',
						'E-mail',
						'SMS Sent',
						'Work Order',
						'Unavailable',
						'Problematic',
						'SMS Received',
						'Duplicate Property'
					)                        
				)   
				UNION
				(
					SELECT
						DISTINCT(p.`property_id`),
						p.`address_1`,
						p.`address_2`,
						p.`address_3`,
						p.`state`,
						p.`postcode`
					FROM `logs` AS l
					LEFT JOIN `jobs` AS j ON l.`job_id` = j.`id` 
					LEFT JOIN `property` AS p ON j.`property_id` = p.`property_id`
					LEFT JOIN `agency` AS a ON p.`agency_id` = a.`agency_id`
					WHERE a.`agency_id` = {$hume_house_agency_id}               
					AND j.`status` = 'To Be Booked'
					AND j.`del_job` = 0
					AND p.`deleted` = 0                    
					AND l.`deleted` = 0                                        
					AND l.`title` IN (40,78)                        
				) 
				";
				$property_sql = $this->db->query($property_sql_str);
				$total_rows = $property_sql->num_rows();                

				
				$pagi_links_params_arr = array();
				
				// pagination link
				$pagi_link_params = "{$uri}/?".http_build_query($pagi_links_params_arr);

				// explort link
				$data['export_link'] = "{$uri}/?export=1&".http_build_query($pagi_links_params_arr);


				// pagination settings
				$config['page_query_string'] = TRUE;
				$config['query_string_segment'] = 'offset';
				$config['total_rows'] = $total_rows;
				$config['per_page'] = $per_page;
				$config['base_url'] = $pagi_link_params;

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
				$this->load->view($uri, $data);
				$this->load->view('templates/home_footer');

			}

		}                                        

    }

	public function qld_upgrade_proceed_with_quote(){

		$property_id = $this->input->post('property_id');  //reserved
		$amount = $this->input->post('amount'); //reserved
		$j_service = $this->input->post('j_service');  //reserved
		$preferred_alarm_id = $this->input->post('preferred_alarm_id');  //reserved
		$job_id = $this->input->post('job_id');

		/*if( is_numeric($job_id) && $job_id!="" ){ //validate job id
			$update_data = array(
				'status' => 'Completed',
				'date' => date('Y-m-d')
			);
			$this->db->where('jobs_id', $job_id);
			$this->db->update('jobs', $update_data);
		}*/

		$custom_where_str = "";
		if( $j_service!="" ){
			$custom_where_str = "AND `alarm_job_type_id` = {$j_service}";
		}else{
			$custom_where_str = "AND `service` = 1";
		}

		$prop_serv_sql = $this->db->query("
			SELECT
				`property_services_id`,
				`alarm_job_type_id` AS service_type_id,
				`price`
			FROM `property_services`
			WHERE `property_id` = {$property_id}
			{$custom_where_str}
			");
	
		if( $prop_serv_sql->num_rows() > 0 ){

			$prop_serv_row = $prop_serv_sql->row();

			// DHA NEED PROCESSING check
			$dha_need_processing = 0;
			if( $this->gherxlib->isDHAagenciesV2($this->session->agency_id)==true || $this->gherxlib->agencyHasMaintenanceProgram($this->session->agency_id)==true ){
				$dha_need_processing = 1;
			}

			$jobs_data = array(
				'job_type' => 'IC Upgrade',
				'status' => 'To Be Booked',
				'property_id' => $property_id,
				'service' => $prop_serv_row->service_type_id,
				//'job_price' => $amount, ##disabled as per Anthony's request > same on qld_upgrade_quotes page
				'created' => date("Y-m-d H:i:s"),
				'dha_need_processing' => $dha_need_processing
			);

			if( $this->db->insert('jobs', $jobs_data) ){

				$job_id = $this->db->insert_id();

				//UPDATE INVOICE DETAILS
				$this->gherxlib->updateInvoiceDetails($job_id);

				//RUN JOB SYNC
				$this->gherxlib->runJobSync($job_id,$prop_serv_row->service_type_id,$property_id);

				// mark is_eo
				$this->system_model->mark_is_eo($job_id);

				// approve QLD upgrade quote
				$p_data = array(
					'qld_upgrade_quote_approved_ts' => date('Y-m-d H:i:s')
				);
				$this->db->where('property_id', $property_id);
				$this->db->update('property', $p_data);

				// update property preferred_alarm
				if( $property_id > 0 && $preferred_alarm_id > 0 ){
					
					$update_prop_sql_str = "
					UPDATE `property`
					SET `preferred_alarm_id` = {$preferred_alarm_id}
					WHERE `property_id` = {$property_id}
					";
					$this->db->query($update_prop_sql_str);

				}					
				
				//Insert LOg-------

				// get 240v RF make					
				$this->db->select('al_p.`alarm_pwr_id`, al_p.`alarm_make`');
				$this->db->from('`alarm_pwr` AS al_p');										
				$this->db->where('al_p.`alarm_pwr_id`', $preferred_alarm_id);
				$agency_alarms_sql = $this->db->get();			
				$agency_alarms_row = $agency_alarms_sql->row();								
				
				$log_details = "Accepted quote for $".$amount." using {$agency_alarms_row->alarm_make} and created an IC Upgrade job for {p_address}";
				$log_details = "{agency_user:{$this->session->aua_id}} approved this property for upgrade using {$agency_alarms_row->alarm_make} alarms.";
				$params_event_log = array(
					'title' => 39, //Upgrade Quote
					'details' => $log_details,
					'display_in_vpd' => 1,
					'display_in_vad' => 1,
					'display_in_portal' => 1,
					'display_in_vjd' => 1,
					'agency_id' => $this->session->agency_id,
					'created_by' => $this->session->aua_id,
					'property_id' => $property_id,
					'job_id' => $job_id
				);
				$this->jcclass->insert_log($params_event_log);

			}
		}

		
	}

	/**
	 * upgrade property service to IC 
	 * if IC job cancel job and log
	 * check other jobs (!=IC) > Update job.service --> New service type and log
	 */
	public function qld_upgrade_advise_upgrade(){
		$property_id = $this->input->post('property_id');
		//$job_id = $this->input->post('job_id');
		//$hasicjob = $this->input->post('hasicjob');
		//$ic_job_id = $this->input->post('ic_job_id');

		if(is_numeric($property_id) && $property_id!="" ){ //validate
			

			//get active/SATS property services and update to IC
			$where = array( 'property_id' => $property_id,'service' => 1 );
			$ps_q = $this->db->select('property_services_id,alarm_job_type_id,service,property_id')
						->from('property_services')
						->where($where)->get();

			if( $ps_q->num_rows()>0 ){

					foreach( $ps_q->result_array() as $row ){

						$property_services_id = $row['property_services_id'];

						$do_update = false;
						if( $row['alarm_job_type_id']==2 ){ //If SA update to SA IC

							$to_ajt_ic = 12; //to SAIC
							$do_update = true;

						}elseif( $row['alarm_job_type_id']==8 ){ //if SASS update to SASS IC

							$to_ajt_ic = 13; //to SASS IC
							$do_update = true;
						}elseif( $row['alarm_job_type_id']==9  ){
							$to_ajt_ic = 14; //to SASS IC
							$do_update = true;
						}else{
							$do_update = false;
						}

						if($do_update){ //update property services alarm_job_type

							$update_services_data = array('alarm_job_type_id' => $to_ajt_ic);
							$this->db->where('property_services_id',$property_services_id);
							$this->db->set($update_services_data);
							$this->db->update('property_services');

						}

					}

					/* DISABLED/no use because of additional changes not to show Properties that has IC jobs
					//get/check IC Upgrade job***
					$status_not_in = array('Booked','Pre Completion','Merged Certificates','Completed','Cancelled');
					$this->db->select('id');
					$this->db->from('jobs');
					$this->db->where('property_id', $property_id);
					$this->db->where('job_type', "IC Upgrade");
					$this->db->where_not_in('status',$status_not_in);
					$this->db->where('del_job', 0);
					$ic_q = $this->db->get();

					if( $ic_q->num_rows()>0 ){ //IC JOB FOUND
						
						//Cancel IC JOBS
						foreach( $ic_q->result_array() as $ic_row ){

							if( $ic_row['id']!="" && is_numeric($ic_row['id']) ){

								$cancel_log_text = "Job was cancelled as the property was marked Upgraded by the agent";
								$job_cancel_data = array(
									'status' => 'Cancelled',
									'comments' => $cancel_log_text,
									'cancelled_date' => date('Y-m-d')
								);
								$this->db->where( array('property_id'=>$property_id,'id'=>$ic_row['id'],'status!=' =>'Completed') );
								$this->db->set($job_cancel_data);
								$this->db->update('jobs');	

								//insert log
								$title = 39; 
								$params = array(
									'title' => $title,
									'details' => $cancel_log_text,
									'display_in_portal' => 1,
									'display_in_vjd' => 1,
									'agency_id' => $this->session->agency_id,
									'created_by' => $this->session->aua_id,
									'property_id' => $property_id,
									'job_id' => $ic_row['id']
								);
								$this->jcclass->insert_log($params);
								
							}

						}

					} 
					*/
					
					
					//get/check other jobs (not IC) and update***
					//SELECT `id` FROM `jobs` WHERE `del_job` = 0 AND `property_id` = '441' AND `job_type` != 'IC Upgrade' AND (status!='Completed' || status!='Cancelled' || status!='Pre Completion' || status != 'Merged Certificates')
					$or_where = "(status!='Completed' || status!='Cancelled' || status!='Pre Completion' || status!='Merged Certificates')";
					$this->db->select('id,service');
					$this->db->from('jobs');
					$this->db->where('del_job',0);
					$this->db->where('property_id', $property_id);
					$this->db->where('job_type!=', "IC Upgrade");
					$this->db->where($or_where);
					$not_ic_q = $this->db->get();
				
					//update not ic jobs
					if( $not_ic_q->num_rows()>0 ){

						foreach( $not_ic_q->result_array() as $not_ic_row ){
						
							$curr_service = $not_ic_row['service'];

							//set job service switch
							$update_not_ic_jobs = false;
							if( $curr_service==8 ){
								$to_service = 13;
								$update_not_ic_jobs = true;
							}elseif( $curr_service==2 ){
								$to_service = 12;
								$update_not_ic_jobs = true;
							}elseif( $curr_service==9 ){
								$to_service = 14;
								$update_not_ic_jobs = true;
							}
						
							if( $update_not_ic_jobs == true ){
								
								if( $not_ic_row['id']!="" && is_numeric($not_ic_row['id']) ){

									$not_ic_job_data = array(
										'service' => $to_service
									);
									$this->db->where(array('property_id'=>$property_id,'id'=>$not_ic_row['id']));
									$this->db->set($not_ic_job_data);
									$this->db->update('jobs');
		
									//insert log
									$curr_service_name = $this->_get_job_services_name($curr_service);
									$to_service_name = $this->_get_job_services_name($to_service);
	
									$not_ic_job_text = "<strong>{agency_user:{$this->session->aua_id}}</strong> marked this property upgraded, job service changed from <strong>{$curr_service_name}</strong> to <strong>{$to_service_name}</strong>";
									$title = 39; 
									$params = array(
										'title' => $title,
										'details' => $not_ic_job_text,
										'display_in_portal' => 1,
										'display_in_vjd' => 1,
										'agency_id' => $this->session->agency_id,
										'created_by' => $this->session->aua_id,
										'property_id' => $property_id,
										'job_id' => $not_ic_row['id']
									);
									$this->jcclass->insert_log($params);

								}

							}
							
						}
						
					}
					

					//get/check if property has precom|merge > email ***
					$or_where2 = " (status='Pre Completion' || status='Merged Certificates') ";
					$this->db->select('id');
					$this->db->from('jobs');
					$this->db->where('del_job',0);
					$this->db->where('property_id', $property_id);
					$this->db->where($or_where2);
					$prop_has_merge_or_precom = $this->db->get();

					if( $prop_has_merge_or_precom->num_rows()>0 ){ //precom|merge job found > do email

						$this->load->library('email'); //load email library

						//get agency name
						$agency_params = array(
							'sel_query' => "a.agency_name",
							'agency_id' => $this->session->agency_id
						);
						$agency_q = $this->agency_model->get_agency_data($agency_params)->row_array();
						$email_data['agency_name'] = $agency_q['agency_name'];
						//get agency user name
						$agency_user_q = $this->db->select('fname')->from('agency_user_accounts')->where(array('agency_id'=>$this->session->agency_id,'agency_user_account_id'=>$this->session->aua_id))->get()->row_array();
						$email_data['agency_user_fname'] = $agency_user_q['fname'];
						//get property data
						$prop_params = array(
							'sel_query'=> "p.property_id, p.address_1, p.address_2, p.address_3",
							'property_id' => $property_id
						);
						$prop_q = $this->properties_model->get_properties($prop_params)->row_array();
						$email_data['prop_id'] = $prop_q['property_id'];
						$email_data['prop_address'] = "{$prop_q['address_1']} {$prop_q['address_2']} {$prop_q['address_3']}"; 
						
						//email
						$this->email->from(make_email('accounts'), $this->config->item('COMPANY_NAME_SHORT'));
						$this->email->to(make_email('info'));
						$this->email->subject('Property Advise Upgrade');
						$body = $this->load->view('emails/property_advise_upgrade', $email_data, TRUE);
						$this->email->message($body);
						$this->email->send();

					}

			}

		}
	}

	/**
	 * Get alarm_job_type name
	 * return type
	 */
	private function _get_job_services_name($alarm_job_type_id){
		$this->db->select('id,type,short_name');
		$this->db->from('alarm_job_type');
		$this->db->where('id', $alarm_job_type_id);
		$q = $this->db->get();
		$row = $q->row_array();
		return $row['type'];
	}


}
