<?php
class Compliance extends CI_Controller {

	public function __construct(){
		parent::__construct();
        $this->load->model('properties_model');
        $this->load->library('pagination');
        $this->load->model('profile_model');
        $this->load->library('email');
        $this->load->model('jobs_model');
	}

	public function index(){

    }

    public function nsw_inspection_details(){

        $data['btn_search'] = $this->input->get_post('btn_search');//pass data to view
        $btn_search = $this->input->get_post('btn_search');
        $pdf_post = $this->input->get_post('pdf');
        if($this->config->item('country') ==1){ //visible AU only

            $agencyInfo = $this->gherxlib->agency_info();
            if ( true || in_array($agencyInfo->state, ['ACT', 'NSW']) ){ //Viewable for NSW OR ACT ONLY

                $condi = array();
                $pm_id = $this->input->get_post('pm_id');
                $search = $this->input->get_post('search');
                if(!empty($pm_id) || !empty($search)){
                    $condi['search']['pm'] = $pm_id;
                    $condi['search']['keyword'] = $search;
                }

                //pagiation offset and per page
                $per_page = $this->config->item('pagi_per_page');
                $offset = $this->input->get_post('offset');

                //main query
                $sel_query = '
                    p.`property_id`,
                    p.`address_1` AS p_address_1,
                    p.`address_2` AS p_address_2,
                    p.`address_3` AS p_address_3,
                    p.`state` AS p_state,
                    p.`postcode` AS p_postcode,

                    ajt.`id` AS ajt_id,
                    ajt.`type` AS ajt_type,
                    ajt.`short_name` AS ajt_short_name,

                    ps.property_services_id,
                    ps.alarm_job_type_id,
                    p.pm_id_new,
                    p.key_number,

                    aua.agency_user_account_id,
                    aua.fname as pm_fname,
                    aua.lname as pm_lname,
                    aua.email as pm_email,
                    aua.photo
                ';
                $custom_where = "ps.alarm_job_type_id!=6 AND (p.`state` = 'NSW' OR p.`state` = 'ACT')";


                $query_params = array(
                    'sel_query' => $sel_query,
                    'p_deleted' => 0,
                    'agency_id' => $this->session->agency_id,
                    'ps_service' => 1,
                    'custom_where' => $custom_where,
                    'limit' => $per_page,
                    'offset' => $offset,
                    'display_query' => 0
                );

                if($pdf_post==1){ // remove limit/offset if pdf dl/export
                    unset($query_params['limit'],$query_params['offset']);
                }

                $propertyServices = $this->properties_model->get_property_services($query_params, $condi)->result();

                $this->properties_model->attach_last_service_row_to_list($propertyServices);
                $this->properties_model->attach_next_schedule_row_to_list($propertyServices);
                $this->properties_model->attach_alarm_details_to_list($propertyServices);

                if($pdf_post==1){ //pdf dl/export

                    $this->load->library('JPDF');
                    $output_type = $this->input->get_post('output_type');

                    // pdf initiation
                    $pdf = new JPDF();

                    // settings
                    $pdf->SetTopMargin(40);
                    $pdf->SetAutoPageBreak(true,30);
                    $pdf->AliasNbPages();
                    $pdf->AddPage();

                    // header
                    $font_size_h = 12;
                    $cell_height_h = 10;

                    // row
                    $font_size = 7;
                    $col_width1 = 45;
                    $col_width2 = 45;
                    $col_width3 = 30;
                    $col_width4 = 20;
                    $cell_height = 5;

                    $pdf->SetFont('Arial',null,$font_size_h);
                    $pdf->Cell($col_width1,$cell_height_h,"Compliance Helper");
                    $pdf->Ln();

                    // body
                    $pdf->SetFillColor(211,211,211);
                    $pdf->SetFont('Arial','B',$font_size);
                    $pdf->Cell($col_width1,$cell_height,'Address',1,null,null,true);
                    $pdf->Cell($col_width3,$cell_height,'Property Manager',1,null,null,true);
                    $pdf->Cell($col_width4,$cell_height,'Service Type',1,null,null,true);
                    $pdf->Cell(35,$cell_height,'Serviced in Last 12 months',1,null,null,true);
                    $pdf->Cell(30,$cell_height,'Last Battery Change ',1,null,null,true);
                    $pdf->Cell($col_width3,$cell_height,'Next Scheduled ',1,null,null,true);
                    $pdf->Ln();

                    $pdf->SetFont('Arial','',$font_size);
                    foreach ($propertyServices as $row){

                        $p_address = "{$row->p_address_1} {$row->p_address_2}, {$row->p_address_3}";
                        $pm_name = ( isset($row->properties_model_id_new) && isset($row->properties_model_id_new) > 0 )?"{$row->properties_model_fname} {$row->properties_model_lname}":null;

                        //last service
                        $last_service = $row->last_service;
                        if($last_service){
                            $now = date('Y-m');
                            $last_service_date = date('Y-m', strtotime($last_service->date));
                            $last_12_month = date('Y-m', strtotime($now . " -12 month"));
                            $tt = (strtotime($last_service_date)>=strtotime($last_12_month)) ? 'Yes' :'No';
                        }else{
                            $tt = "No";
                        }

                        //last batt changed
                        $last_bat_changed = (!empty($last_service->date)) ? date('d/m/Y', strtotime($last_service->date)) : 'N/A';

                        //next sched
                        $next_schedule = $row->next_schedule;
                        $last_sched_date= $next_schedule->date;
                        $next_sched_date_plus_monts = date('Y-m-d', strtotime($last_sched_date . " +12 month"));
                        if($next_schedule->status == "Booked"){
                            $nex_sced_date = date('d/m/Y', strtotime($next_schedule->date));
                        }else if($next_schedule->status =="Completed" && $next_schedule->job_type == "Yearly Maintenance"){
                            $nex_sced_date = date('M, Y', strtotime($next_sched_date_plus_monts));
                        }else{
                            $nex_sced_date = "Booking in Progress";
                        }

                        $pdf->Cell($col_width1,$cell_height,$p_address,1);
                        $pdf->Cell($col_width3,$cell_height,$pm_name,1);
                        $pdf->Cell($col_width4,$cell_height,$row->ajt_short_name,1);
                        $pdf->Cell(35,$cell_height,$tt,1);
                        $pdf->Cell(30,$cell_height,$last_bat_changed,1);
                        $pdf->Cell($col_width3,$cell_height,$nex_sced_date,1);
                        $pdf->Ln();

                    }

                    $file_name = 'non_compliant_report'.date('YmdHis').'.pdf';
                    $pdf->Output($output_type,$file_name);

                }else{ //normal listing page

                    $data['propertyServices'] = $propertyServices;

                    //total rows
                    $p_params = array(
                        'sel_query' => 'COUNT(p.property_id) as p_count',
                        'p_deleted' => 0,
                        'agency_id' => $this->session->agency_id,
                        'ps_service' => 1,
                        'custom_where' => $custom_where,
                        'display_query' => 0
                    );
                    $query = $this->properties_model->get_property_services($p_params, $condi);
                    $total_rows = $query->row()->p_count;

                    //property manager dropdown filter
                    $pm_param = array(
                        'sel_query' => 'DISTINCT(p.pm_id_new), p.pm_id_new as pm_id,aua.fname, aua.lname',
                        'p_deleted' => 0,
                        'agency_id' => $this->session->agency_id,
                        'ps_service' => 1,
                        'custom_where' => $custom_where,
                        'sort_list' => array(
                            array(
                                'order_by' => 'aua.`fname`',
                                'sort' => 'ASC'
                            )
                        )
                    );
                    $data['pm'] = $this->properties_model->get_property_services($pm_param);


                    //PAGINATION
                    $config['page_query_string'] = TRUE;
                    $config['query_string_segment'] = 'offset';
                    $config['base_url'] = "/compliance/nsw_inspection_details/?pm_id={$pm_id}&search={$search}";
                    $config['total_rows'] = $total_rows;
                    $config['per_page'] = $per_page;
                    $this->pagination->initialize($config);

                    //pagination links
                    $data['pagi_links_non_sats'] = $this->pagination->create_links(); // NON SATS PAGINATION

                    // pagination count
                    $pc_params = array(
                        'total_rows' => $total_rows,
                        'offset' => $offset,
                        'per_page' => $per_page
                    );
                    $data['pagi_count'] = $this->jcclass->pagination_count($pc_params);

                    $data['title'] = 'NSW Inspection Details';
                    $this->load->view('templates/home_header', $data);
                    $this->load->view('properties/compliance_helper', $data);
                    $this->load->view('templates/home_footer');

                }

            }else{
                exit('Error: Please contact admin!');
            }
        }else{
            exit('Error: Please contact admin!');
        }

    }

    public function ajax_compliance_create_job(){
       //$data['status'] = false;
        $response = ['status' => false, 'err_msg' => ""];
        $err = "";
        $prop_id = $this->input->post('prop_id');
        $job_type = "Fix or Replace";
        $job_status = "To Be Booked";
        $ajt_id = $this->input->post('ajt_id');

        if(empty($prop_id) || !is_numeric($prop_id)){ //validate property id
            log_message('error', 'ajax_compliance_create_job: Invalid property id');
            return false;
        }

        /**
		 * Check if property has active job
		 * Redirect if has active job and show error popup to users/agent
		 */
		if($this->jobs_model->hasActiveJob($prop_id)===TRUE){
            $err = "An active job already exists on this property. Please contact {$this->config->item('COMPANY_NAME_SHORT')} to create another job.";
		}

        if(trim($err) != ""){
            $response['status'] = false;
            $response['err_msg'] = $err;
        }else{

            //agencyHasMaintenanceProgram/isDHAagenciesV2
            //set DHA NEED PROCESSING
            $dha_need_processing = 0;
            if( $this->gherxlib->isDHAagenciesV2($this->session->agency_id)==true || $this->gherxlib->agencyHasMaintenanceProgram($this->session->agency_id)==true ){
                $dha_need_processing = 1;
            }

            $jcomments = "Job created for NSW Compliance Purposes";

            //ADD JOBS
            $add_jobs_data = array(
                'job_type' => $job_type,
                'status' => $job_status,
                'property_id' => $prop_id,
                'created' => date("Y-m-d H:i:s"),
                'service' => $ajt_id,
                'start_date' => NULL,
                'due_date' => NULL,
                'no_dates_provided' => 1,
                'property_vacant' => 0,
                'dha_need_processing' => $dha_need_processing,
                'comments' => $jcomments
            );
            $add_job_id = $this->properties_model->add_jobs($add_jobs_data); //add job and return last id

            if($add_job_id){ //create job success

                //UPDATE INVOICE DETAILS
                $this->gherxlib->updateInvoiceDetails($add_job_id);

                //RUN JOB SYNC
                $this->gherxlib->runJobSync($add_job_id,$ajt_id,$prop_id);

                // mark is_eo
                $this->system_model->mark_is_eo($add_job_id);


                // added FR, requested by ness
                if( 
                    ( ( $job_type == 'Change of Tenancy' ||  $job_type == 'Lease Renewal'  ) && $this->system_model->findExpired240vAlarm($add_job_id) == true ) ||
                    ( $job_type == 'Fix or Replace' && $this->system_model->getAll240vAlarm($add_job_id) == true  )
                ){
                                    
                    // append comments
                    $this->db->query("
                    UPDATE `jobs` 
                    SET `comments` = '240v REBOOK - {$jcomments}'
                    WHERE `id` = {$add_job_id}
                    ");

                }

                //SEND MAIL
                $config = Array(
                    'mailtype'  => 'html',
                    'charset'   => 'iso-8859-1'
                );

                $this->email->initialize($config);
                $this->email->set_newline("\r\n");

                //get agency emails
                $to_email_agency = array();
                $agency_email =  $this->profile_model->get_agency($this->session->agency_id); //get agency email (model)
                $agency_email_res = explode("\n",trim($agency_email->agency_emails));
                foreach($agency_email_res as $new_agency_email_res){
                    $new_agency_email_res2 = preg_replace('/\s+/', '', $new_agency_email_res);
                    if(filter_var($new_agency_email_res2, FILTER_VALIDATE_EMAIL)){
                        $to_email_agency[] = $new_agency_email_res2;
                    }
                }

                $agentFullName = $this->gherxlib->agent_full_name();
                $email_data['property_address'] = $this->gherxlib->prop_address($prop_id);
                $email_data['agent_name'] = $agentFullName;

                $agency_info = $this->properties_model->get_agency_info($this->session->agency_id);
                $email_data['agency_name'] = $agency_info->agency_name;
                $email_data['job_type'] = $job_type;
                $email_data['new_tenancy_start'] = NULL;
                $email_data['vacant_from'] = NULL;
                $email_data['vacant_to'] = NULL;
                $email_data['work_order'] = NULL;
                $email_data['comment'] = NULL;
                $email_data['agent_full_name'] = $agentFullName;

                //get tenants info for email
                $email_params_active = array('property_id'=>$prop_id, 'active' => 1);
                $email_data['active_tenants'] = $this->properties_model->get_new_tenants($email_params_active);


                $this->email->from($this->config->item('accounts_email'), $this->config->item('COMPANY_NAME_SHORT'));
                $this->email->to($to_email_agency);
                $this->email->subject($job_type.' added by '.$agency_info->agency_name);
                $body = $this->load->view('emails/create-job', $email_data, TRUE);
                $this->email->message($body);
                $this->email->send();
                //SEND MAIL END

                //Insert Job Log
                $details_job_Log = "New job created";
                $params_job_log = array(
                    'title' => 1,
                    'details' => $details_job_Log,
                    'display_in_vjd' => 1,
                    'agency_id' => $this->session->agency_id,
                    'created_by' => $this->session->aua_id,
                    'property_id' => $prop_id,
                    'job_id' => $add_job_id
                );
                $this->jcclass->insert_log($params_job_log);

                //Insert Log
                $details_prop_event_log = "New job created For {p_address}";
                $params_event_log = array(
                    'title' => 1,
                    'details' => $details_prop_event_log,
                    'display_in_vpd' => 1,
                    'display_in_portal' => 1,
                    'agency_id' => $this->session->agency_id,
                    'created_by' => $this->session->aua_id,
                    'property_id' => $prop_id
                );
                $this->jcclass->insert_log($params_event_log);

                //set status true
                $response['status'] = true;
            }
            
        }
        
        

        echo json_encode($response);

    }




}
