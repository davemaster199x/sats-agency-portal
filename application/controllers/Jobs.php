<?php
class Jobs extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('properties_model');
		$this->load->library('pagination');
		$this->load->model('jobs_model');
		$this->load->model('profile_model');
		$this->load->library('email');
        $this->load->library('encryption');
        $this->load->model('Alarm_job_type_model');
	}
    
    /**
     * Get Portal Jobs Page
     * @param $aua_id
     * @return void
     */
	public function index($aua_id=null)
    {
        ini_set('memory_limit', '512M');
		$data['title'] = 'Active Jobs';

		$country_id = $this->session->country_id;
		$agency_id = $this->session->agency_id;

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

		$custom_where = " j.status != 'Completed' AND j.status != 'Cancelled' AND j.status != 'Pending'";

		$params = array(
			'sel_query' => $sel_query,
			'p_deleted' => 0,
			'a_status' => 'active',
			'del_job' => 0,
			'agency_id' => $agency_id,
			'country_id' => $country_id,

			'j_status' => $j_status,
			'search' => $search,

			'custom_where' => $custom_where,

			'sort_list' => array(
				array(
					'order_by' => 'j.created',
					'sort' => 'ASC'
				)
			),
			'display_query' => 0
		);

	
		$list = $this->jobs_model->get_jobs($params);
        
        $list_arr = new stdClass();
        foreach ($list->result() as $k => $row) {
            $list_arr->{$k} = $row;
            $list_arr->{$k}->encrypted_job_id = $this->encryption->encrypt($row->j_id);
        }
        
		$data['list'] = $list_arr;

		$this->load->view('templates/home_header', $data);
		$this->load->view('jobs/index', $data);
		$this->load->view('templates/home_footer');

	
	}

	public function create(){

		// title
		$data['title'] = 'Create a Job';

		// pagination
		$per_page = $this->config->item('pagi_per_page');

		// get user PM
		$query_params = array(
			'active' => 1,
			'agency_id' => $this->session->agency_id
		);
		$data['user_pm'] = $this->user_accounts_model->get_user_accounts($query_params);


		//search
		$condi = array();
		$search_pm = $this->input->get_post('pm');
		$search_keyword = $this->input->get_post('search');
		if(!empty($search_pm) || !empty($search_keyword)){
			//$condi['search']['pm'] = $search_pm;
			$condi['search']['keyword'] = $search_keyword;
		}


		// get property services
		// get all
		$sel_query = 'ps.`property_services_id`';
		$query_params = array(
			'sel_query' => $sel_query,
			'p_deleted' => 0,
			'agency_id' => $this->session->agency_id,
			'ps_service' => 1,
			'pm_id' => $search_pm
		);
		$get_all = $this->properties_model->get_property_services($query_params, $condi);

		//calculate offset/start number
		if(!$this->input->get('per_page')){
			$offset = 0;
		}else{
			$offset = $this->input->get('per_page');
		}


		// get by pagination
		$exclude_sales_prop = "p.is_sales!=1";
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
		$query_params = array(
			'sel_query' => $sel_query,
			'p_deleted' => 0,
			'agency_id' => $this->session->agency_id,
			'ps_service' => 1,
			'pm_id' => $search_pm,
			'custom_where' => $exclude_sales_prop,

			'limit' => $per_page,
			'offset' => $offset
		);
		$data['ps'] = $this->properties_model->get_property_services($query_params, $condi);
		$base_url_a = '/jobs/create/?pm='.$search_pm.'&search='.$search_keyword;
		// pagination settings
		$config['enable_query_strings']=TRUE;
		$config['base_url'] = $base_url_a;
		$config['total_rows'] = $get_all->num_rows();
		$config['per_page'] = $per_page;
		$config['num_links'] = 10;
        $config['page_query_string'] = TRUE;
        $config['enable_query_strings'] = TRUE;
		$config['reuse_query_string'] = TRUE;
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
		$this->load->view('jobs/create', $data);
		$this->load->view('templates/home_footer');

	}

	public function create_job(){

		$prop_id = $this->security->xss_clean($this->input->post('hid_prop_id'));

		/**
		 * Check if property has active job
		 * Redirect if has active job and show error popup to users/agent
		 */
		if($this->jobs_model->hasActiveJob($prop_id)===TRUE){

			$this->session->set_flashdata(array('error_msg'=>"An active job already exists on this property. Please contact {$this->config->item('COMPANY_NAME_SHORT')} to create another job.",'status'=>'error'));
			redirect(base_url('jobs/create'));

		}

		if($this->input->post('hid_job_type') && $this->input->post('hid_job_type')!=""){

				$job_type = $this->security->xss_clean($this->input->post('hid_job_type'));
				$ajt = $this->security->xss_clean($this->input->post('hid_ajt'));
				$hid_ajt_id = $this->security->xss_clean($this->input->post('hid_ajt_id'));


				$prop_vacant = $this->security->xss_clean($this->input->post('prop_vacant'));
				$lease_start_date = $this->security->xss_clean($this->input->post('lease_start_date'));
				$work_order = $this->security->xss_clean($this->input->post('work_order'));
				$lockbox_code = $this->security->xss_clean($this->input->post('lockbox_code'));
				$key_number = $this->security->xss_clean($this->input->post('key_number'));
				$vacant_from = $this->security->xss_clean($this->input->post('vacant_from'));
				$vacant_to = $this->security->xss_clean($this->input->post('vacant_to'));
				$job_comments = $this->security->xss_clean($this->input->post('job_comments'));
				$agent_firstname = $this->security->xss_clean($this->input->post('agent_firstname'));
				$agent_lastname = $this->security->xss_clean($this->input->post('agent_lastname'));
				
				$job_comments_faulty_alarm = $this->security->xss_clean($this->input->post('job_comments_faulty_alarm'));
				$job_comments_wrong_with_the_alarm = $this->security->xss_clean($this->input->post('job_comments_wrong_with_the_alarm'));
				$job_comments_faulty_brand = $this->security->xss_clean($this->input->post('job_comments_faulty_brand'));				
				
				
				//Chops
				$urgent_job = $this->input->post('urgent_job');


				//UPDATE PROPERTY
				$update_profile_data = array(
						'key_number' => $key_number
				);
				$where = array('property_id' => $prop_id);
				$this->jobs_model->udpate_property($where,$update_profile_data);


				if( $prop_id > 0 ){

					// check if lockbox exist
					$lb_sql = $this->db->query("
					SELECT COUNT(`id`) AS pl_count
					FROM `property_lockbox`
					WHERE `property_id` = {$prop_id}
					");
					$lb_row = $lb_sql->row();
		
					if( $lb_row->pl_count > 0 ){ // it exist, update
		
						$this->db->query("
						UPDATE `property_lockbox`
						SET `code` = '{$lockbox_code}'
						WHERE `property_id` = {$prop_id}
						");
		
					}else{ // doesnt exist, insert
		
						if( $lockbox_code != '' ){
		
							$this->db->query("
							INSERT INTO 
							`property_lockbox`(
								`code`,
								`property_id`
							)
							VALUE(
								'{$lockbox_code}',
								{$prop_id}
							)	
							");
		
						}		
		
					}
		
				}


				//new vacant from/to reformat date/time
				$new_vacant_from =  date('Y-m-d',strtotime(str_replace('/','-',$vacant_from)));
				$new_vacant_to =  date('Y-m-d',strtotime(str_replace('/','-',$vacant_to)));
				$new_start = date('Y-m-d',strtotime(str_replace('/','-',$lease_start_date)));

				$agent_full_name = $this->gherxlib->agent_full_name();

				$no_dates_provided = 0;
				$start_date_str = NULL;
				$end_date_str = NULL;
				$jcomments = '';
				$return_message = '';
				#$urgent_job = 0; //for fix/replace job type
				#$urgent_job_reason = ""; //for fix/replace job type


				//SWITCH JOB TYPE - SET VALUES,MESSAGE etc. FOR INSERT/UPDATe JOBS EACH RELEVANT JOB TYPE
				switch($job_type){
						case 'Change of Tenancy':   //JOB TYPE CHANGED OF TENANCY

									$create_job_status = "To Be Booked";

									if($prop_vacant==1){  //property vacant = YES
											$start_date_str = ($vacant_from!="")?$new_vacant_from:NULL;
											$end_date_str = ($vacant_to!="")?$new_vacant_to:NULL;

											$log_start_date =  ($vacant_from!="")?$vacant_from:NULL;
											$log_end_date =  ($vacant_to!="")?$vacant_to:NULL;
											$jcomments = "Vacant from ".$log_start_date." - ".$log_end_date;
									}else{  //property vacant = NO

											if($lease_start_date!=""){

												$start_date_str = NULL;
												$end_date_str = $new_start;
												$jcomments = "Lease Start Date: $lease_start_date";

											}else{

												$start_date_str = NULL;
												$end_date_str = NULL;
												$no_dates_provided = 1;

											}

									}

									if($jcomments!=""){
										$jcomments .= ", {$job_comments}";
									}else{
										$jcomments .= "{$job_comments}";
									}

									//log format
									$job_com_str = ( $job_comments!='' )?" Comments: <strong>{$job_comments}</strong>":'';
									$log_com = "<strong>{$job_type}</strong> Job Created </strong>.{$job_com_str}";

									$return_message = 'Change of Tenancy Job Created';

						break;


						case 'Fix or Replace':  //JOB TYPE REPAIR

									$create_job_status = "Allocate";

									$problem = $job_comments;

									if($prop_vacant==1){ //property vacant = YES

										$start_date_str = ($vacant_from!="")?$new_vacant_from:NULL;
										$end_date_str = ($vacant_to!="")?$new_vacant_to:NULL;

										$log_start_date =  ($vacant_from!="")?$vacant_from:NULL;
										$log_end_date =  ($vacant_to!="")?$vacant_to:NULL;
										$jcomments = "Vacant from ".$log_start_date." - ".$log_end_date;

									}else{ //property vacant = NO
										$start_date_str = NULL;
										$end_date_str = NULL;

										$jcomments = "";
									}

									//colums here - urgent_job and urgent_job_reason for fix/replace job type
									#$urgent_job = 1;
									#$urgent_job_reason = "URGENT REPAIR";


									if($jcomments!=""){
										$jcomments .= ", {$job_comments}";
									}else{
										$jcomments .= "{$job_comments}";
									}

									//log format
									$job_com_str = ( $problem!='' )?" Problem: <strong>{$problem}</strong>":'';
									$log_com = "<strong>{$job_type}</strong> Job Created </strong>.{$job_com_str}";

									$return_message = 'Repair Job Created';

						break;


						case 'Lease Renewal':  //JOB TYPE LEASE RENEWAL

									$create_job_status = "To Be Booked";

									$problem = $job_comments;

									if($prop_vacant==1){ //property vacant = YES

											$start_date_str = ($vacant_from!="")?$new_vacant_from:NULL;
											$end_date_str = ($vacant_to!="")?$new_vacant_to:NULL;

											$log_start_date =  ($vacant_from!="")?$vacant_from:NULL;
											$log_end_date =  ($vacant_to!="")?$vacant_to:NULL;
											$jcomments = "Lease Renewal {$log_start_date} - {$log_end_date} {$problem}";

									}else{ //property vacant = NO

											if($lease_start_date!=""){

												$end_date_str = $new_start;
												$start_date = date('Y-m-d',strtotime("{$end_date_str} -30 days"));
												$start_date_str = $start_date;

												$jcomments = "Lease Renewal ".date('d/m/Y', strtotime($start_date))." - ".date('d/m/Y', strtotime($new_start))." {$problem}";

											}else{
												$end_date_str = NULL;
												$start_date_str = NULL;
												$no_dates_provided = 1;
												$jcomments = 'No Dates Provided';
											}

											/*if($jcomments!=''){
												$jcomments .= ", {$job_comments}";
											}else{
												$jcomments .= "{$job_comments}";
											}*/

									}

									//log format
									$job_com_str = ( $job_comments!='' )?" Comments: <strong>{$job_comments}</strong>":'';
									$log_com = "<strong>{$job_type}</strong> Job Created </strong>. <strong>Tenancy starts {$lease_start_date}</strong>.{$job_com_str}";

									$return_message = 'Lease Renewal Job Created';


						break;
				} //switch job type end here...


				//agencyHasMaintenanceProgram/isDHAagenciesV2
				//set DHA NEED PROCESSING
				$dha_need_processing = 0;
				if( $this->gherxlib->isDHAagenciesV2($this->session->agency_id)==true || $this->gherxlib->agencyHasMaintenanceProgram($this->session->agency_id)==true ){
					$dha_need_processing = 1;
				}


				//ADD JOBS
				$add_jobs_data = array(
					'job_type' => $job_type,
					'status' => $create_job_status,
					'property_id' => $prop_id,
					'created' => date("Y-m-d H:i:s"),
					'comments' => $jcomments,
					'work_order' => $work_order,
					'service' => $hid_ajt_id,
					'start_date' => $start_date_str,
					'due_date' => $end_date_str,
					'no_dates_provided' => $no_dates_provided,
					'property_vacant' => $prop_vacant,
					'urgent_job' => $urgent_job,
					'dha_need_processing' => $dha_need_processing
					#'urgent_job' => $urgent_job,
					#'urgent_job_reason' => $urgent_job_reason
				);
				$add_job_id = $this->properties_model->add_jobs($add_jobs_data);

				if($add_job_id){ //add/create job success

					//UPDATE INVOICE DETAILS
					$this->gherxlib->updateInvoiceDetails($add_job_id);

					//RUN JOB SYNC
					$this->gherxlib->runJobSync($add_job_id,$hid_ajt_id,$prop_id);

					// mark is_eo
					$this->system_model->mark_is_eo($add_job_id);

					$combine_job_comments_arr = [];

					// added FR, requested by ness
					$rebook_240v_job_comments = null;
					if( 
						( ( $job_type == 'Change of Tenancy' ||  $job_type == 'Lease Renewal'  ) && $this->system_model->findExpired240vAlarm($add_job_id) == true ) ||
						( $job_type == 'Fix or Replace' && $this->system_model->getAll240vAlarm($add_job_id) == true  )
					){
						
						$combine_job_comments_arr[] = "240v REBOOK - {$jcomments}";

					}

					// combine all FR job comments
					if($job_type == "Fix or Replace"){
						$fr_job_comments_arr = [];
						if( $job_comments_faulty_alarm != '' ){
							$fr_job_comments_arr[] = "- Faulty Alarm: {$job_comments_faulty_alarm}";
						}

						if( $job_comments_wrong_with_the_alarm != '' ){
							$fr_job_comments_arr[] = "- Alarm is ".strtolower($job_comments_wrong_with_the_alarm);
						}

						if( $job_comments_faulty_brand != '' ){
							$fr_job_comments_arr[] = "- {$job_comments_faulty_brand}";
						}

						$fr_job_comments_imp = null;
						if( count($fr_job_comments_arr) > 0 ){
							$fr_job_comments_imp = implode("\n",$fr_job_comments_arr);
							$combine_job_comments_arr[] = $fr_job_comments_imp;
						}
					}

					// combine 240v rebook and FR job comments
					$combine_job_comments_imp = null;
					if( count($combine_job_comments_arr) > 0 ){
						$combine_job_comments_imp = implode("\n\n",$combine_job_comments_arr);

						// append comments
						$this->db->query("
							UPDATE `jobs` 
							SET `comments` = '".$this->db->escape_str($combine_job_comments_imp)."'
							WHERE `id` = {$add_job_id}
						");
					}					

					

					//SEND MAIL

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
					$email_data['new_tenancy_start'] = $lease_start_date;
					$email_data['vacant_from'] = $vacant_from;
					$email_data['vacant_to'] = $vacant_to;
					$email_data['work_order'] = $work_order;
					$email_data['comment'] = $jcomments;
					$email_data['agent_full_name'] = $agentFullName;

					//get tenants info for email
					$email_params_active = array('property_id'=>$prop_id, 'active' => 1);
					$email_data['active_tenants'] = $this->properties_model->get_new_tenants($email_params_active);

					// For the Fix or Replace, update the from email to info@sats.com.au
					if ($job_type == 'Fix or Replace' || $job_type == 'Change of Tenancy') {

					} else {
						$this->email->from(make_email('accounts'), $this->config->item('COMPANY_NAME_SHORT'));
					}
					$this->email->to($to_email_agency);
					$this->email->subject($job_type.' added by '.$email_data['agency_name']);
					$body = $this->load->view('emails/create-job', $email_data, TRUE);
					$this->email->message($body);
					$this->email->send();
					//SEND MAIL END


					//Insert Job Log
					$details_job_Log = $log_com;
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
					$details_prop_event_log = "{$return_message} For {p_address}";
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


					//set session success message
					$this->session->set_flashdata(array('success_msg'=>$return_message,'status'=>'success'));
					redirect(base_url('jobs/create'));

				}else{
					$this->session->set_flashdata(array('error_msg'=>'Error: Submission Fail, Something went wrong','status'=>'error'));
					redirect(base_url('jobs/create'));
				}




		} //post btn_create_new_job end here

	}


	public function help_needed(){

		$data['title'] = 'Help Needed';

		// pagination
		$per_page = $this->config->item('pagi_per_page');
		$offset = $this->input->get_post('offset');

		$country_id = $this->session->country_id;
		$agency_id = $this->session->agency_id;

		$pm_id = $this->input->get_post('pm_id');
		$search = $this->input->get_post('search');

		// paginated
		$sel_query = "
			DISTINCT (
				j.`property_id`
			),
			j.`id` AS j_id,
			j.`property_id`,
			j.`work_order`,
			j.`service` AS j_service,
			j.start_date,
			j.due_date,

			ajt.`type` AS ajt_type,
			ajt.`short_name` AS ajt_short_name,

			p.`address_1` AS p_address_1,
			p.`address_2` AS p_address_2,
			p.`address_3` AS p_address_3,
			p.`state` AS p_state,
			p.`postcode` AS p_postcode,
			p.`alarm_code`,
			p.`key_number`,
			p.`pm_id_new`,

			a.`agency_id`,
			a.`agency_name`,

			aua.`agency_user_account_id`,
			aua.`fname` AS pm_fname,
			aua.`lname` AS pm_lname,
			aua.`email` AS pm_email,
			aua.photo,

			ejr.reason,

			sejr.escalate_job_reasons_id
		";

		$custom_where = "
			sejr.`escalate_job_reasons_id` != 4
			AND (
				j.`agency_approve_en` = 0 ||
				j.`agency_approve_en` IS NULL
			)
		";

		$params = array(
			'sel_query' => $sel_query,
			'p_deleted' => 0,
			'a_status' => 'active',
			'del_job' => 0,
			'agency_id' => $agency_id,
			'j_status' => 'Escalate',
			'country_id' => $country_id,
			'custom_where' => $custom_where,

			'pm_id' => $pm_id,
			'search' => $search,

			'limit' => $per_page,
			'offset' => $offset,
			'sort_list' => array(
                array(
                    'order_by' => 'j.created',
                    'sort' => 'ASC',
                ),
            ),
		);

		$escalatedJobs = $this->jobs_model->get_escalated_jobs($params)->result();

		$this->properties_model->attach_new_tenants_count_to_list($escalatedJobs);

		$data['escalatedJobs'] = $escalatedJobs;

		// all row
		//$sel_query = "DISTINCT(j.`property_id`)";

		$custom_where = "
			sejr.`escalate_job_reasons_id` != 4
			AND (
				j.`agency_approve_en` = 0 ||
				j.`agency_approve_en` IS NULL
			)
		";

		$params_total = array(
			'sel_query' => $sel_query,
			'p_deleted' => 0,
			'a_status' => 'active',
			'del_job' => 0,
			'agency_id' => $agency_id,
			'j_status' => 'Escalate',
			'country_id' => $country_id,
			'custom_where' => $custom_where,

			'pm_id' => $pm_id,
			'search' => $search,
			'sort_list' => array(
                array(
                    'order_by' => 'j.created',
                    'sort' => 'ASC',
                ),
            ),
		);
		$query_total = $this->jobs_model->get_escalated_jobs($params_total);
		$total_rows = $query_total->num_rows();


		// header filter
		// PM
		$sel_query = "
			DISTINCT(p.`pm_id_new`),
			aua.`fname`,
			aua.`lname`,
			aua.photo
		";

		$custom_where = "
			sejr.`escalate_job_reasons_id` != 4
			AND (
				j.`agency_approve_en` = 0 ||
				j.`agency_approve_en` IS NULL
			)
		";

		$params = array(
			'sel_query' => $sel_query,
			'p_deleted' => 0,
			'a_status' => 'active',
			'del_job' => 0,
			'agency_id' => $agency_id,
			'j_status' => 'Escalate',
			'country_id' => $country_id,
			'custom_where' => $custom_where,

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
		$data['pm_filter'] = $this->jobs_model->get_escalated_jobs($params);


		// pagination settings
		$config['page_query_string'] = TRUE;
		$config['query_string_segment'] = 'offset';
		$config['total_rows'] = $total_rows;
		$config['per_page'] = $per_page;
		$config['base_url'] = "/jobs/help_needed/?pm_id={$pm_id}&search={$search}";

		$this->pagination->initialize($config);

		$data['pagination'] = $this->pagination->create_links();

		$pc_params = array(
			'total_rows' => $total_rows,
			'offset' => $offset,
			'per_page' => $per_page
		);
		$data['pagi_count'] = $this->jcclass->pagination_count($pc_params);

		$this->load->view('templates/home_header', $data);
		$this->load->view('jobs/help_needed', $data);
		$this->load->view('templates/home_footer');

	}


	public function service_due(){

		// get agency details
		$params = array(
			'sel_query' => 'a.`allow_upfront_billing`',
			'agency_id' => $this->session->agency_id
		);
		$agency_sql = $this->agency_model->get_agency_data($params);
		$agency_row = $agency_sql->row();
		$data['agency_row'] = $agency_row;

		$data['title'] = ( $agency_row->allow_upfront_billing == 1 )?'Due for Subscription':'Due for Service';

		// pagination
		$per_page = $this->config->item('pagi_per_page');
		$offset = $this->input->get_post('offset');

		$country_id = $this->session->country_id;
		$agency_id = $this->session->agency_id;

		$pm_id = $this->input->get_post('pm_id');
		$search = $this->input->get_post('search');
		$export = $this->input->get_post('export');

		// paginated
		$sel_query = "
			j.`id` AS j_id,
			j.`service` AS j_service,
			j.`property_id` AS j_property_id,
			j.`work_order`,
			j.`property_vacant`,
			j.start_date,
			j.due_date,
			j.comments,

			ajt.`type` AS ajt_type,
			ajt.`short_name` AS ajt_short_name,

			p.`property_id`,
			p.`address_1` AS p_address_1,
			p.`address_2` AS p_address_2,
			p.`address_3` AS p_address_3,
			p.`state` AS p_state,
			p.`postcode` AS p_postcode,
			p.`pm_id_new`,
			p.`retest_date`,
			p.`alarm_code`,
			p.`key_number`,
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
			'j_status' => 'Pending',
			'country_id' => $country_id,
			'pm_id' => $pm_id,
			'search' => $search,
			'display_query' => 0
		);

		if ($export != 1){
			array_push($params,
				array('limit' => $per_page, 'offset' => $offset)
			);
		}

		$jobs = $this->jobs_model->get_jobs($params)->result();

		if (!empty($jobs)) {
			$jobsById = [];

			$services = array_unique(array_map(function($j) {
				return $j->j_service;
			}, $jobs));

			$properties = array_unique(array_map(function($j) {
				return $j->j_property_id;
			}, $jobs));

			for($x = 0; $x < count($jobs); $x++) {
				$job =& $jobs[$x];
				$job->ym = null;

				$jobsById[$job->j_id] =& $job;
			}

			$jobIds = array_keys($jobsById);

			$this->db->select("service, property_id, max(date) as max_date")
			->from("jobs")
			->where_in('service', $services)
			->where_in('property_id', $properties)
			->group_by('property_id, service');

			$maxDateSubquery = $this->db->get_compiled_select();

			$ymServices = $this->db->select("`j`.`id` AS `j_id`, `j`.`date` AS `jdate` ")
			->from('jobs AS j')
			->join("({$maxDateSubquery}) as j2", "j2.service = j.service AND j2.property_id = j.property_id AND j2.max_date = j.date", "inner")
			->join("property AS p", "j.`property_id` = p.`property_id`", "left")
			->join("agency_user_accounts AS aua", "p.`pm_id_new` = aua.`agency_user_account_id`", "left")
			->join("agency AS a", "p.`agency_id` = a.`agency_id`", "left")
			->where("j.del_job", 0)
			->where("j.job_type", "Yearly Maintenance")
			->where("j.status", "Completed")
			->where("p.deleted", 0)
			->where("a.status", "active")
			->get()->result();

			$data['ym'] = $ymServices;

			foreach ($ymServices as $ym) {
				$jobsById[$ym->j_id] = new StdClass;
				$jobsById[$ym->j_id]->ym = $ym;
			}

			$this->properties_model->attach_new_tenants_count_to_list($jobs);
		}

		if ( $export == 1 ) { // EXPORT         

            // file name
            $date_export = date('YmdHis');
            $filename = "Service Due ({$date_export}).csv";

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename={$filename}");
            header("Pragma: no-cache");
            header("Expires: 0");

            // file creation 
            $csv_file = fopen('php://output', 'w');            

            $header = array("Address","Property Manager","Service Type","Last YM","Active Tenants");
			if( $this->system_model->is_hume_housing_agency() == true ){
				array_push($header, 'Propery Code');
			}
            fputcsv($csv_file, $header);            
                                    
            foreach ( $jobs as $row_inner ){
                $csv_row = [];
                $prop_address = "{$row_inner->p_address_1} {$row_inner->p_address_2}, {$row_inner->p_address_3}";
				$prop_pm = "$row_inner->pm_fname $row_inner->pm_lname";
                $csv_row[] = $prop_address;
                $csv_row[] = $prop_pm;   
                $csv_row[] = $row_inner->ajt_type;   
				if (!is_null($row_inner->ym)){
					$csv_row[] = $this->jcclass->isDateNotEmpty($row_inner->ym->jdate)?date('d/m/Y',strtotime($row_inner->jdate)):'';
				} else {
					$csv_row[] = '';
				}                    
                $csv_row[] = ($row_inner->new_tenants_count > 0) ? $row_inner->new_tenants_count : '';
				if( $this->system_model->is_hume_housing_agency() == true ){
					$csv_row[] = $row_inner->compass_index_num;   
				}
                fputcsv($csv_file,$csv_row); 
            }
                                
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
				'j_status' => 'Pending',
				'country_id' => $country_id,

				'pm_id' => $pm_id,
				'search' => $search
			);
			$query = $this->jobs_model->get_jobs($params);
			$total_rows = $query->num_rows();


			// PM filter
			$sel_query = "
				DISTINCT(p.`pm_id_new`),
				aua.`fname`,
				aua.`lname`,
				aua.photo
			";
			$custom_where = "p.`pm_id_new` > 0";

			$params = array(
				'sel_query' => $sel_query,
				'p_deleted' => 0,
				'a_status' => 'active',
				'del_job' => 0,
				'agency_id' => $agency_id,
				'j_status' => 'Pending',
				'country_id' => $country_id,
				'custom_where' => $custom_where,

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

			// pagination settings
			$config['page_query_string'] = TRUE;
			$config['query_string_segment'] = 'offset';
			$config['total_rows'] = $total_rows;
			$config['per_page'] = $per_page;
			$config['base_url'] = "/jobs/service_due/?pm_id={$pm_id}&search={$search}";

			$this->pagination->initialize($config);

			$data['pagination'] = $this->pagination->create_links();

			$pc_params = array(
				'total_rows' => $total_rows,
				'offset' => $offset,
				'per_page' => $per_page
			);
			$data['pagi_count'] = $this->jcclass->pagination_count($pc_params);

			$data['agencyIsAutoRenew'] = $this->gherxlib->agencyIsAutoRenew($this->session->agency_id);

			$this->load->view('templates/home_header', $data);
			$this->load->view('jobs/service_due', $data);
			$this->load->view('templates/home_footer');
		}
	}

	/**
	 * SERVICE DUE UDPATE DETAILS VIA AJAX
	 * action update_property
	 * action update_jobs
	 * NOTE: THIS FUNCTION IS USED BY HELP_NEDDED AND SERVICE DUE PAGE
	 */
	public function job_ajax_update_details(){
		$data['status'] = false;
		$prop_id = $this->security->xss_clean($this->input->post('prop_id'));
		$job_id =$this->security->xss_clean($this->input->post('job_id'));

		$is_verify_ten = $this->security->xss_clean($this->input->post('is_verify_ten'));
		$prop_vacant = $this->security->xss_clean($this->input->post('prop_vacant'));

		$start_date = ($this->input->post('start_date')!='') ? date("Y-m-d", strtotime(str_replace("/", "-", $this->input->post('start_date')))) : NULL ;
		$due_date = ($this->input->post('due_date')!='') ? date("Y-m-d", strtotime(str_replace("/", "-", $this->input->post('due_date')))) : NULL ;
		
		$start_date_v2 = ($this->input->post('start_date')!='') ? "'".$this->jcclass->formatDate($this->input->post('start_date'))."'" : 'NULL' ;
		$due_date_v2 = ($this->input->post('due_date')!='') ? "'".$this->jcclass->formatDate($this->input->post('due_date'))."'" : 'NULL' ;
		
		$is_response = $this->input->post('is_response');
		$page_type = $this->input->post('page_type');

		$tenants_arr = $this->input->post('tenants_arr');
		$clear_tenants = $this->input->post('clear_tenants');
		$job_vacant = $this->input->post('job_vacant');

		$job_onhold = $this->input->post('job_onhold');
		$onhold_start_date = $this->input->post('onhold_start_date');
		$onhold_end_date = $this->input->post('onhold_end_date');


		// check if property is already NLM'ed
		$is_nlm_check_sql = $this->db->query("
		SELECT COUNT(`property_id`) AS pcount
		FROM `property`
		WHERE `property_id` = {$prop_id}
		AND `is_nlm` = 1
		");

		if( $is_nlm_check_sql->row()->pcount == 0 ){
		
		//ADD TENANT START
		if($prop_id!=""){ //prop_id must not be empty


			// deactivate current tenants
			if( $clear_tenants == 1 ){
				
				$this->db->query("
				UPDATE `property_tenants`
				SET `active` = 0
				WHERE `property_id` = {$prop_id}
				AND `active` = 1
				");

			}


			$post_data = [];

			foreach($tenants_arr as $tnt){
				//decode json
				$json_enc = json_decode($tnt);
				$new_tenant_fname = $json_enc->new_tenant_fname;
				$new_tenant_lname = $json_enc->new_tenant_lname;
				$new_tenant_mobile = $json_enc->new_tenant_mobile;
				$new_tenant_landline = $json_enc->new_tenant_landline;
				$new_tenant_email = $json_enc->new_tenant_email;

				if( ($new_tenant_fname && $new_tenant_fname!="") || ($new_tenant_lname && $new_tenant_lname!="")){ //check if tenant field has value > process adding tenant when has value
					//validate email
					if(filter_var($new_tenant_email, FILTER_VALIDATE_EMAIL)){
						$new_tenant_email_filtered = $new_tenant_email;
					}else{
						$new_tenant_email_filtered = "";
					}

					$post_data[] = array(
						'property_id' => $prop_id,
						'tenant_firstname' => $new_tenant_fname,
						'tenant_lastname' => $new_tenant_lname,
						'tenant_mobile' => $new_tenant_mobile,
						'tenant_landline' => $new_tenant_landline,
						'tenant_email' => $new_tenant_email_filtered,
						'active' => 1
					);

					$is_add_tenant = 1;
				}
			}

			if ($is_add_tenant == 1) {
				$this->properties_model->add_tenants($post_data, true);
			}
		}
		//ADD TENANT START END

		//UPDATE PROPERTY
		if($prop_id && $prop_id!=""){
			$data_update_prop = array(
					'alarm_code' => $this->input->post('alarm_code'),
					'key_number' => $this->input->post('key_number')
			);
			$data_update_prop = $this->security->xss_clean($data_update_prop);
			$update_prop = $this->properties_model->update_property($prop_id,$data_update_prop);
		}
		
		


		//UPDATE  JOBS
		if($job_id && $job_id!=""){ //check job id not empty
			//Check if job is status = PENDING
			if($job_vacant == 1 && !empty($start_date) && !empty($due_date)){

				$status_data = array(
					'status' => 'To Be Booked',
					'start_date' => $start_date,
					'due_date' => $due_date,
					'property_vacant' => $job_vacant
				);
				$status_data = $this->security->xss_clean($status_data);
				$where_id = array('id'=> $job_id);
				$update_job = $this->jobs_model->update_jobs($where_id,$status_data);
				//echo $this->db->last_query();
				//exit();
			}

			//Check if job onhold status
			if($job_onhold == 1 && !empty($onhold_start_date) && !empty($onhold_end_date)){
				
				$tmp_onhold_date = $onhold_start_date;
				$date        = DateTime::createFromFormat('d/m/Y H:i:s', "$tmp_onhold_date 00:00:00");
				$onhold_date = $date->format('Y-m-d H:i:s');

				$tmp_onhold_end_date = $onhold_end_date;
				$on_hold_end_date        = DateTime::createFromFormat('d/m/Y H:i:s', "$tmp_onhold_end_date 00:00:00");
				$onhold_end_date = $on_hold_end_date->format('Y-m-d H:i:s');

				

				$status_data = array(
					'status' => 'On Hold',
					'start_date' => $onhold_date,
					'due_date' => $onhold_end_date
				);
				$status_data = $this->security->xss_clean($status_data);
				$where_id = array('id'=> $job_id);
				$update_job = $this->jobs_model->update_jobs($where_id,$status_data);
				
			}
			
			// conditions from alger
			$append_update_sql_str = null;
			if( $page_type=='help_needed' || $page_type=='service_due' ){ 
				//if($prop_vacant==1){
				if($job_vacant == 1){

					$append_update_sql_str = "
					`start_date` = {$start_date_v2}, 
					`due_date` = {$due_date_v2},
					";

					// manual update
					$update_job_sql_str = "
					UPDATE `jobs` 
					SET 
						`work_order` = '{$this->input->post('work_order')}', 
						{$append_update_sql_str}
						`property_vacant` = '{$job_vacant}'				
					WHERE `id` = {$job_id}
					";
									
					if( $this->db->query($update_job_sql_str) ){
						$update_job = true;
					}else{
						$update_job = false;
					}	
										
				}
			}	

		}

		
		if($update_prop || $update_job){

			$prop_det = $this->properties_model->get_property_detail_by_id($prop_id);
			$data['alarm_code'] = $prop_det->alarm_code;
			$data['key_number'] = $prop_det->key_number;


			$job_det = $this->jobs_model->get_job_by_id($job_id);
			$data['work_order'] = $job_det->work_order;


			$data['status'] = true;
		}


		//if($is_verify_ten==1){  //for escalate >>>>>disable for now so that all submit on Update Property lightbox will trigger log > by gherx

			if( $page_type=='help_needed' || $page_type=='service_due'){ //(UPDATE TENANT LIGHTBOX) UPDATE JOB STATUS ONLY OFR HELP_NEEDE NOT ON SERVICE DUE
				//UPDATE  JOBS
				if($job_id && $job_id!="" && empty($onhold_start_date)){ //check job id not empty
					$update_data2 = array(
						'status' => 'To Be Booked'
					);
					$update_data2 = $this->security->xss_clean($update_data2);
					$where2 = array('id'=> $job_id);
					$update_job = $this->jobs_model->update_jobs($where2,$update_data2);
				}

				//---------------------ADD JOB LOG------------------//
				//response/escalate reason switch > used for log dynamic Escalate Reason
				switch($is_response){
					case 1:
					$is_response_log = "Verify Tenant Details";
					break;
					case 2:
					$is_response_log = "Old Jobs";
					break;
					case 3:
					$is_response_log = "Other - See Notes";
					break;
					case 5:
					$is_response_log = "Short Term Rental";
					break;
					case 6:
					$is_response_log = "Verify NLM";
					break;
					case 8:
					$is_response_log = "Unresponsive";
					break;
					case 9:
					$is_response_log = "Needs Agent to Verify";
					break;
					case 10:
					$is_response_log = "To Book With Agent";
					break;
					default:
					$is_response_log = $is_response;

				}

				if( $page_type == 'help_needed' ){
					$log_title = 15; // Escalate Job
				}else if( $page_type == 'service_due' ){
					$log_title = 51; // Service Due
				}else{
					$log_title = 63; // Job Update
				}
				
				$start_date_append_txt = ( $this->input->post('start_date') != '' )?$this->input->post('start_date'):'null';
				$due_date_append_txt = ( $this->input->post('due_date') != '' )?$this->input->post('due_date'):'null';

				if($job_vacant==1){ //property vacant log
					
					$start_date_log_text = $this->input->post('start_date');
					$due_date_log_text = $this->input->post('due_date');
															
					if( $page_type == 'service_due' ){
						$details = "Agency responded Service Due and marked vacant: <b>{$start_date_append_txt}</b> to <b>{$due_date_append_txt}</b>. Job type change to <b>To Be Booked</b>.";
					}else{ // default from alger
						$details = "Agency responded to <strong>{$is_response_log}</strong>, and marked vacant: <strong>{$start_date_log_text}</strong> to <strong>{$due_date_log_text}</strong>. Job type changed to <strong>To Be Booked</strong>.";
					}
										
					$params_job_Log = array(
						'title' => $log_title,
						'details' => $details,
						'display_in_vpd' => 0,
						'display_in_portal' => 1,
						'display_in_vjd' =>1,
						'agency_id' => $this->session->agency_id,
						'created_by' => $this->session->aua_id,
						'property_id' => $prop_id,
						'job_id' => $job_id
					);
					$this->jcclass->insert_log($params_job_Log);

				} else if($job_onhold==1){ //property Onhold log

					$onhold_end_date = $this->input->post('onhold_end_date');
									
					if( $page_type == 'service_due' ){
						$details = "Agency responded Service Due and marked onhold: <b>{$onhold_start_date}</b> to <b>{$onhold_end_date}</b>. Job type change to <b>On Hold</b>.";
					}else{ // default from alger
						$details = "Agency responded to <strong>{$is_response_log}</strong>, and marked onhold: <strong>{$onhold_start_date}</strong> to <strong>{$onhold_end_date}</strong>. Job type changed to <strong>On Hold</strong>.";
					}
										
					$params_job_Log = array(
						'title' => $log_title,
						'details' => $details,
						'display_in_vpd' => 0,
						'display_in_portal' => 1,
						'display_in_vjd' =>1,
						'agency_id' => $this->session->agency_id,
						'created_by' => $this->session->aua_id,
						'property_id' => $prop_id,
						'job_id' => $job_id
					);
					$this->jcclass->insert_log($params_job_Log);

				}else{ //general log

					if( $page_type == 'service_due' ){

						//$details = "Agency responded Service Due and marked vacant: <b>{$start_date_append_txt}</b> to <b>{$due_date_append_txt}</b>. Job type change to <b>To Be Booked</b>.";
						$details = "Agency responded to Service Due. Job type changed to <b>To Be Booked</b>.";
						
					}else{ // default from alger
						$details = "Agency responded to <strong>{$is_response_log}</strong> and the job has been changed to <strong>To Be Booked</strong>.";
					}
					
					$params_job_Log = array(
						'title' => $log_title,
						'details' => $details,
						'display_in_vpd' => 1,
						'display_in_portal' => 1,
						'display_in_vjd' =>1,
						'agency_id' => $this->session->agency_id,
						'created_by' => $this->session->aua_id,
						'property_id' => $prop_id,
						'job_id' => $job_id
					);
					$this->jcclass->insert_log($params_job_Log);
				}

				if($is_add_tenant==1){ // additional log for adding tenant

					// Insert Job and Portal Log
					$details = "Tenant Added for {p_address}";
					$params = array(
						'title' => 10,
						'details' => $details,
						'display_in_vpd' => 1,
						'display_in_portal' => 1,
						'agency_id' => $this->session->agency_id,
						'created_by' => $this->session->aua_id,
						'property_id' => $prop_id,
					);
					$this->jcclass->insert_log($params);

					//add log for all active job under property
					$fetch_active_job = $this->properties_model->get_active_job_by_propId($prop_id);

					if(!empty($fetch_active_job) && $fetch_active_job){

						$this->db->trans_start();
						foreach($fetch_active_job as $new_row){
							$details = "Tenant Added for {p_address}";
							$params = array(
								'title' => 10,
								'details' => $details,
								'display_in_vjd' => 1,
								'agency_id' => $this->session->agency_id,
								'created_by' => $this->session->aua_id,
								'property_id' => $prop_id,
								'job_id' => $new_row->id
							);
							$this->jcclass->insert_log($params);
						}
						$this->db->trans_complete();

					}

				}

			}
			//---------------------ADD JOB LOG END------------------//

			if($page_type=='service_due'){
				$this->system_model->mark_is_eo($job_id);
			}
			$data['status'] = true;
		//}


		echo json_encode($data);


		}


	}


	public function service_due_create_job(){

		$sd_create_job_flag = $this->input->post('sd_create_job_flag');
		$sd_nlm_flag = $this->input->post('sd_nlm_flag');

		$j_id = $this->input->post('j_id');
		$sel_job = $this->input->post('sel_job');
		$prop_id = $this->input->post('prop_id');
		$serv_id = $this->input->post('serv_id');
		$chkbox = $this->input->post('chkbox');
		$prop_state = $this->input->post('prop_state');
		$retest_date = $this->input->post('retest_date');
		$agent_nlm_from = $this->input->post('nlm_from');
		$agent_nlm_reason = $this->input->post('nlm_reason');
		$reason_they_left = $this->input->post('reason_they_left');
		$other_reason = $this->input->post('other_reason');
		$today = date("Y-m-d");
		//$prop_vacant = $this->input->post('prop_vacant');
		//$vacant_from = $this->input->post('vacant_from');
		//$vacant_to = $this->input->post('vacant_to');
		//$job_comments = $this->input->post('job_comments');

		$update_job = false;

		if($sd_create_job_flag ==1 && $sd_nlm_flag==0){ //CREATE JOB

			// !!! ADD ERROR HANDLING
			$this->db->trans_start();

			$processed_jobs_count = 0;
			foreach($j_id as $index => $val){
				$job_id = $val;

				// check if property is already NLM'ed
				$is_nlm_check_sql = $this->db->query("
				SELECT COUNT(`property_id`) AS pcount
				FROM `property`
				WHERE `property_id` = {$prop_id[$index]}
				AND `is_nlm` = 1
				");
				
				if( ($sel_job[$index])==1 && $is_nlm_check_sql->row()->pcount == 0 ){ //check selected index

					//UPDATE JOBS
					/*if( $prop_vacant[$index]==1 ){
						$vacant_from2 = ($vacant_from[$index]!="")?date("Y-m-d",strtotime(str_replace("/","-",$vacant_from[$index]))):NULL;
						$vacant_to2 = ($vacant_to[$index]!="")?date("Y-m-d",strtotime(str_replace("/","-",$vacant_to[$index]))):NULL;

						$update_job_data = array(
							'status' => 'On Hold',
							'auto_renew' => 2,
							'property_vacant' => $prop_vacant[$index],
							'start_date' => $vacant_from2,
							'due_date' => $vacant_to2,
							'comments' => $job_comments[$index]

						);

					}else{
						$update_job_data = array(
							'status' => 'On Hold',
							'auto_renew' => 2,
							'property_vacant' => $prop_vacant[$index],
						);
					} */

					if($job_id>0){ //check job id not empty

						// update on 'auto_renew' => 2 removed per bens direction, old field and no longer used

						/*
						$days_60 = date('Y-m-d', strtotime($today. '+60 days'));
						if( $prop_state[$index]=='NSW' && ($this->jcclass->isDateNotEmpty($retest_date[$index]) && $retest_date[$index]<$days_60) ){ //property == NSW and retest_date < 300 days
							$update_job_data = array(
								'status' => 'To Be Booked',
								'start_date' => NULL
							);
							$this->db->set($update_job_data);
							$where = array('id'=> $job_id, 'property_id' => $prop_id[$index], 'status'=> 'Pending');
							//$update_job = $this->jobs_model->update_jobs($where,$update_job_data);
							$this->db->where($where);
							$update_job = $this->db->update('jobs');

							//update property retest_date
							$prop_update_data = array(
								'retest_date' => $today
							);
							$this->db->where('property_id', $prop_id[$index]);
							$this->db->update('property', $prop_update_data);
						}else{
							$update_job_data = array(
								'status' => 'On Hold'
							);
							$where = array('id'=> $job_id, 'property_id' => $prop_id[$index], 'status'=> 'Pending');
							$update_job = $this->jobs_model->update_jobs($where,$update_job_data);
						}
						*/

						$update_job_data = array(
							'status' => 'On Hold'
						);
						$where = array('id'=> $job_id, 'property_id' => $prop_id[$index], 'status'=> 'Pending');
						$update_job = $this->jobs_model->update_jobs($where,$update_job_data);

						if( $prop_id[$index] > 0 ){

							// reset property `manual_renewal` marker
							$this->db->query("
								UPDATE `property`
								SET `manual_renewal` = 0
								WHERE `property_id` = {$prop_id[$index]}
							");

							// property log
							$details_prop_event_log = "Property unmarked <b>Manual Renewal</b> due to job creation";
							$params_event_log = array(
								'title' => 65, // Property Update
								'details' => $details_prop_event_log,
								'display_in_vpd' => 1,
								'agency_id' => $this->session->agency_id,
								'created_by' => $this->session->aua_id,
								'property_id' => $prop_id[$index]
							);
							$this->jcclass->insert_log($params_event_log);

						}


					}

					if($update_job){

						//insert log
						switch($serv_id[$index]){
							case 2:
								$s = "Smoke Alarms";
							break;
							case 5:
								$s = "Safety Switch";
							break;
							case 6:
								$s = "Corded Window";
							break;
							case 7:
								$s = "Pool Barriers";
							break;
						}
						$details = $s." Service Renewed for {p_address}";
						$params_job_Log3 = array(
							'title' => 12, //job pending
							'details' => $details,
							'display_in_vpd' => 1,
							'display_in_portal' => 1,
							'display_in_vjd' => 1,
							'agency_id' => $this->session->agency_id,
							'created_by' => $this->session->aua_id,
							'property_id' => $prop_id[$index],
							'job_id' => $job_id
						);
						$this->jcclass->insert_log($params_job_Log3);

					}

					$processed_jobs_count++;

				}

			}
			$this->db->trans_complete();

			if ($this->db->trans_status()) {
				if($update_job){
					if( $processed_jobs_count > 0 ){ // make sure it processed some jobs before displaying success
						$this->session->set_flashdata(array('success_msg'=>'Job Created','status'=>'success'));
					}					
					redirect(base_url('jobs/service_due'));
				}else{
					if( $processed_jobs_count > 0 ){ // make sure it processed some jobs before displaying success
						$this->session->set_flashdata(array('error_msg'=>'Job Not Created','status'=>'error'));
					}					
					redirect(base_url('jobs/service_due'));
				}
			}
			else {
				$this->session->set_flashdata(array('error_msg'=>'Job(s) Not Updated. Database Error.','status'=>'error'));
				redirect(base_url('jobs/service_due'));
			}


		}
		else if ($sd_create_job_flag==0 && $sd_nlm_flag==1) { //NO LONGER MANAGE

			$update_status = false;
			$nlm_chk_flag = 0;
			$nlm_prop_arr = [];

			$agentFullName = $this->gherxlib->agent_full_name();

			$properties = $this->db->select("property_id, address_1, address_2, address_3")
				->from('property')
				->where_in("property_id", $prop_id)
				->get()->result();

			for ($x = 0; $x < count($properties); $x++) {
				$property =& $properties[$x];

				$property->address = $property->address_1." ".$property->address_2." ".$property->address_3;
				$property->nlm_check = false;
				$property->verified_paid = false;

				$propertiesById[$property->property_id] =& $property;
			}

			$sortOfCompletedJobCounts = $this->db->select("property_id, IFNULL(COUNT(id), 0) AS count")
				->from("jobs")
				->where_in("property_id", $prop_id)
				->where("del_job", 0)
				->where_in("status", ["Booked", "Pre Completion", "Merged Certificates"])
				->group_by("property_id")
				->get()->result();

			for ($x = 0; $x < count($sortOfCompletedJobCounts); $x++) {
				$propertiesById[$sortOfCompletedJobCounts[$x]->property_id]->nlm_check = $sortOfCompletedJobCounts[$x]->count > 0;
			}

			$completedJobCounts = $this->db->select("property_id, IFNULL(COUNT(id), 0) AS count")
				->from("jobs")
				->where_in("property_id", $prop_id)
				->where("status", "Completed")
				->where("invoice_balance >", 0)
				->group_start()
				->where("date >=", $this->config->item('accounts_financial_year'))
				->or_where("unpaid", 1)
				->group_end()
				->group_by("property_id")
				->get()->result();

			for ($x = 0; $x < count($completedJobCounts); $x++) {
				$propertiesById[$completedJobCounts[$x]->property_id]->verified_paid = $completedJobCounts[$x]->count > 0;
			}

			$loopData = [];
			for ($x = 0; $x < count($prop_id); $x++) {
				$d = [];

				$d['property_id'] = $prop_id[$x];
				$d['job_id'] = $j_id[$x];
				$d['sel_job'] = $sel_job[$x];
				$d['serv_id'] = $serv_id[$x];
				$d['prop_state'] = $prop_state[$x];
				$d['retest_date'] = $retest_date[$x];
				$d['chkbox'] = $chkbox[$x];

				if (isset($propertiesById[$d['property_id']])) {
					$d['property'] = $propertiesById[$d['property_id']];
				}

				$loopData[] = $d;
			}

			//$this->db->trans_begin();

			$logData = [];

			foreach ($loopData as $d) {

				$jobId = $d['job_id'];
				$selJob = $d['sel_job'];
				$prop_id_val = $d['property_id'];

				$prop_address = $d['property']->address;

				if( $selJob == 1 ){ //check selected index

					$nlmjobstatus = $d['property']->nlm_check;

					if($nlmjobstatus===FALSE){

						$nlm_params = array(
							'job_id'=> $jobId,
							'agent_nlm_from'=> $agent_nlm_from,
							'agent_nlm_reason'=> $agent_nlm_reason,
							'reason_they_left'=> $reason_they_left,
							'other_reason'=> $other_reason
						);
						$this->properties_model->nlm_property($prop_id_val, $nlm_params);
						$update_status = true;


					/*	//UPDATE PROPERTY
						$update_property_data = array(
							'agency_deleted' => 1,
							'deleted' => 1,
							'deleted_date' => date('Y-m-d H:i:s'),
							'booking_comments' => "No longer managed as of ".date("d/m/Y")." - by agency.",
							'is_nlm' => 1,
							'nlm_timestamp' => date('Y-m-d H:i:s'),
							'nlm_by_agency' => $this->session->agency_id
						);

						// check if property has money owing and needs to verify paid
						if( $d['property']->verfied_paid == true ){
							$update_property_data['nlm_display'] = 1;
						}

						$update_property_data = $this->security->xss_clean($update_property_data);
						$data_update_property = $this->properties_model->update_property($d['property_id'], $update_property_data);


						// if property has completed job with a price this month and service changed this month
						$this_month_start = date("Y-m-01");
						$this_month_end = date("Y-m-t");

						if( $d['property_id'] > 0 ){
							
							// get completed job this month
							$job_sql_str = "
							SELECT j.`id`
							FROM `jobs` AS j               
							WHERE j.`property_id` = {$d['property_id']}
							AND j.`status` = 'Completed'
							AND j.`job_price` > 0
							AND j.`date` BETWEEN '{$this_month_start}' AND '{$this_month_end}'                         
							";
							$job_sql = $this->db->query($job_sql_str);

							// get status change this month
							$ps_sql_str = "
							SELECT ps.`status_changed`
							FROM `property` AS p 
							INNER JOIN `property_services` AS ps ON p.`property_id` = ps.`property_id`
							WHERE p.`property_id` = {$d['property_id']} 
							AND CAST( ps.`status_changed` AS DATE ) BETWEEN '{$this_month_start}' AND '{$this_month_end}'
							";
							$ps_sql = $this->db->query($ps_sql_str);

							if( $job_sql->num_rows() > 0 && $ps_sql->num_rows() > 0 ){

								// DO nothing, leave is_payable as it is

							}else{

								// clear is_payable
								$update_ps_sql_str = "
								UPDATE `property_services`
								SET `is_payable` = 0   
								WHERE `property_id` = {$d['property_id']}            
								";
								$this->db->query($update_ps_sql_str);

							}

						}
						*/

						/*if($data_update_property){


							// changed to manual update
							if( $d['property_id'] > 0 ){

								$this->db->query("
								UPDATE `jobs`
								SET
									`status` = 'Cancelled',
									`comments` = 'This property was marked No Longer Managed by ".$agentFullName." on ".date("d/m/Y")." and all jobs cancelled',
									`cancelled_date` = '".date('Y-m-d')."'
								WHERE `status` != 'Completed'
								AND `property_id` = {$d['property_id']}
								");
							}

							

							$logData[] = $d;

							

							//Insert Log
							$details =  "{p_address} has been marked as No Longer Managed by ".$agentFullName;
							$params = array(
								'title' => 6,
								'details' => $details,
								'display_in_vpd' => 1,
								'display_in_vad' => 1,
								'display_in_portal' => 1,
								'agency_id' => $this->session->agency_id,
								'created_by' => $this->session->aua_id,
								'property_id' => $prop_id_val,
							);
							$this->jcclass->insert_log($params);


						}*/


					}
					else {
						$nlm_prop_arr[] = array(
							'prop_id' => $prop_id_val,
							'prop_address' => $prop_address
						);

						$this->session->set_flashdata(array('nlm_chk_flag'=>1,'propArray'=>$nlm_prop_arr));
						$nlm_chk_flag = 1;
					}


				}

			}

			//set redirection here...
			if($update_status){
				$this->session->set_flashdata(array('success_msg'=>'Property Marked No Longer Managed','status'=>'success'));
				redirect(base_url('jobs/service_due'));
			}else{
				$this->session->set_flashdata(array('error_msg'=>'An error has occurred, it looks like the records may have already been deleted!','status'=>'error'));
				redirect(base_url('jobs/service_due'));
			}

			if($nlm_chk_flag == 1){
				redirect(base_url('jobs/service_due'));
			}



		} else{
			redirect(base_url('jobs/service_due'));
		}



	}



	public function ajax_escalate_capture_response(){
		$data['status'] = false;

		$response = $this->security->xss_clean($this->input->post('response'));
		$prop_id = $this->security->xss_clean($this->input->post('prop_id'));
		$job_id = $this->security->xss_clean($this->input->post('job_id'));
		$is_holiday_rental = $this->input->post('is_holiday_rental');
		$is_to_be_book_with_agent = $this->input->post('is_to_be_book_with_agent');
		$is_old_job_options = $this->input->post('is_old_job_options');
		$start_date = ($this->input->post('start_date')!='') ? date("Y-m-d", strtotime(str_replace("/", "-", $this->input->post('start_date')))) : NULL ;
		$due_date = ($this->input->post('due_date')!='') ? date("Y-m-d", strtotime(str_replace("/", "-", $this->input->post('due_date')))) : NULL ;
		$is_response = $this->input->post('is_response'); //Escalate Reason

		//Escalate Reason switch > for log
		switch($is_response){
			case 1:
			$is_response_log = "Verify Tenant Details";
			break;
			case 2:
			$is_response_log = "Old Jobs";
			break;
			case 3:
			$is_response_log = "Other - See Notes";
			break;
			case 5:
			$is_response_log = "Short Term Rental";
			break;
			case 6:
			$is_response_log = "Verify NLM";
			break;
			case 8:
			$is_response_log = "Unresponsive";
			break;
			case 9:
			$is_response_log = "Needs Agent to Verify";
			break;
			case 10:
			$is_response_log = "To Book With Agent";
			break;
			default:
			$is_response_log = $is_response;

		}
		//Escalate Reason switch end

		if($response!=""){

			if($job_id && $job_id!=""){ //check job_id

				// clear selected job escalate reason first
				$this->db->delete('selected_escalate_job_reasons', array('job_id' => $job_id));

				//UPDATE JOB
				if($is_to_be_book_with_agent && $is_to_be_book_with_agent!="" && $is_to_be_book_with_agent==1){ // update job to ALLOCATE for To Book With Agent (10) escalate reason
					$response_date_time = $this->input->post('response_date_time'); //date/time post
					$response_date_time_formated = ($response_date_time!="") ? $response_date_time : NULL ;
					//$response_date_time_formated = DateTime::createFromFormat('Y-m-d', '2009-08-12')->format('d/m/Y H:i');

					#check if allocated 2nd time and clear allocate_response
					$this->gherxlib->clear_job_allocate_response($job_id);

					#update datas
					$update_job_data = array(
						'status' => 'Allocate',
						'allocate_notes' => $response_date_time_formated,
						'allocate_opt' => 3
					);
				}else if($is_old_job_options!="" && $is_old_job_options==1 && $response=='EN Property'){ //update job to Allocate for Short Term Rental reason (Entry Notice Property/En Property) option radiobox

					#check if allocated 2nd time and clear allocate_response
					$this->gherxlib->clear_job_allocate_response($job_id);

					#update datas
					$update_job_data = array(
						'status' => 'Allocate',
						'allocate_notes' => 'Agent authorised EN'
					);

				}else{ //rest of job update to TBB
					$update_job_data = array(
						'status' => 'To Be Booked'
					);

					if($is_holiday_rental && $is_holiday_rental!="" && $is_holiday_rental==1){ // add update field for Short Term Rental
						$update_job_data['start_date'] = $start_date;
						$update_job_data['due_date'] = $due_date;
					}
				}
				$where2 = array('id'=> $job_id);
				$update_job = $this->jobs_model->update_jobs($where2,$update_job_data);

			}

			//FOR (Short Term Rental) ESCALATE REASON > Update property holiday_rental
			if($is_holiday_rental && $is_holiday_rental!="" && $is_holiday_rental==1){ //Escalate reason is Short Term Rental
				if($prop_id && $prop_id!=""){ //check prop_id > property id exist and not empty
					$update_prop_data = array('holiday_rental' => $response);
					$this->db->where('property_id', $prop_id);
					$this->db->update('property',$update_prop_data);
				}
				//Short Term Rental log response
				if($response==1){
					$holiday_start_date_log = $this->input->get_post('start_date');
					$holiday_due_date_log = $this->input->get_post('due_date');
					$response = "Agency responded to <strong>{$is_response_log}</strong> with <strong>Yes</strong>, and marked vacant: <strong>{$holiday_start_date_log} to <strong>{$holiday_due_date_log}</strong>";
				}else{
					$response = "Agency responded <strong>Short Term Rental</strong> to <strong>No</strong>";
				}
			}
			//FOR Short Term Rental ESCALATE REASON END


			//FOR (TO BE BOOK WITH AGENT) ESCALATE REASON (10)
			/*if($is_to_be_book_with_agent && $is_to_be_book_with_agent!="" && $is_to_be_book_with_agent==1){
				$response_date_time = $this->input->post('response_date_time'); //date/time post
				$response_date_time_formated = ($response_date_time!="") ? date('d/m/y H:i:s', strtotime($response_date_time)) : NULL ;

				//update job allocate notes
				if($job_id && $job_id!=""){ //check job id
					$update_job_data_tbb_with_agent = array(
						'allocate_notes' => $response_date_time_formated,
						'allocate_opt' => 3
					);
					$where2_tbb_with_agent = array('id'=> $job_id);
					$update_job_tbb_agent = $this->jobs_model->update_jobs($where2_tbb_with_agent,$update_job_data_tbb_with_agent);

				}

			}*/
			//FOR TO BE BOOK WITH AGENT ESCALATE REASON END


			//insert job log
			if($update_job){

				//log details switch
				$log_details = "";
				if($is_old_job_options!="" && $is_old_job_options==1){
					if($response=='EN Property'){
						$log_details = "Agency responded to <strong>Escalate</strong>:<strong>Old Jobs</strong> with <strong>{$response}</strong> and job type was updated from <strong>Escalate</strong> to <strong>Allocate</strong>";
					}else{
						$log_details = "Agency responded to <strong>Escalate</strong>:<strong>Old Jobs</strong> with <strong>{$response}</strong> and job type was updated from <strong>Escalate</strong> to <strong>To Be Booked</strong>";
					}
				}else if($is_to_be_book_with_agent!="" && $is_to_be_book_with_agent==1){
					$response_date = DateTime::createFromFormat('d/m/Y H:i', $response_date_time)->format('d/m/Y');
					//$response_time = date('H:i', strtotime($response_date_time));
					$response_time = DateTime::createFromFormat('d/m/Y H:i', $response_date_time)->format('H:i');
					$log_details = "Agency requested <strong>{$response_date}</strong> at <strong>{$response_time}</strong> to attend with our technician and the job type was updated from <strong>Escalate</strong> to <strong>Allocate</strong>";
				}else if($is_holiday_rental!="" && $is_holiday_rental==1){ //Short Term Rental log
					$log_details = "{$response}";
				}else{
					$log_details = "Agency responded <strong>".$response."</strong>";
				}


				$params_job_Log = array(
					'title' => 12, //escalte
					'details' => $log_details,
					'display_in_vpd' => 1,
					'display_in_vad' => 1,
					'display_in_portal' => 1,
					'display_in_vjd' => 1,
					'agency_id' => $this->session->agency_id,
					'created_by' => $this->session->aua_id,
					'property_id' => $prop_id,
					'job_id' => $job_id
				);
				$this->jcclass->insert_log($params_job_Log);


				$data['status'] = true;

			}


		}


		echo json_encode($data);

	}

	public function ajax_escalate_verify_nlm(){

		$data['status'] = false;

		$prop_id = $this->security->xss_clean($this->input->post('prop_id'));
		$job_id = $this->security->xss_clean($this->input->post('job_id'));
		$verify_nlm_val = $this->security->xss_clean($this->input->post('verify_nlm_val'));
		$reason_they_left = $this->security->xss_clean($this->input->post('reason_they_left'));
		$other_reason = $this->security->xss_clean($this->input->post('other_reason'));


		if($verify_nlm_val=='yes'){

				if($job_id && $job_id!=""){ //check job id

					// clear selected job escalate reason first
					$this->db->delete('selected_escalate_job_reasons', array('job_id' => $job_id));


					//update job
					$update_job_data = array(
						'status' => 'To Be Booked'
					);
					$where2 = array('id'=> $job_id);
					$update_job = $this->jobs_model->update_jobs($where2,$update_job_data);

				}

				if($update_job){

					//add job log
					$details = "Agency verified - <strong>Still under Management</strong>";
					$params_job_Log = array(
						'title' => 15,
						'details' => $details,
						'display_in_vpd' => 1,
						'display_in_portal' => 1,
						'display_in_vjd' => 1,
						'agency_id' => $this->session->agency_id,
						'created_by' => $this->session->aua_id,
						'property_id' => $prop_id,
						'job_id' => $job_id
					);
					$this->jcclass->insert_log($params_job_Log);

					$data['status'] = true;
					$data['stat_msg'] = "Verified Property Still Under Management";

				}


		}else if($verify_nlm_val=='no'){ //no response means NLM the property

			$data['has_active_jobs'] = false;

			if($this->gherxlib->NLMjobStatusCheck($prop_id)===TRUE){

				$cntry = $this->gherxlib->getCountryViaCountryId();

				$data['status'] = true;
				$data['has_active_jobs'] = true;
				$data['stat_msg'] = "This Property has active jobs, please contact ".$this->config->item('COMPANY_NAME_SHORT')." on ".$cntry->tenant_number;

			}else{

				if($job_id && $prop_id){ //check job and prop id

					$nlm_params = array(
						'job_id'=> $job_id,
						'reason_they_left'=> $reason_they_left,
						'other_reason'=> $other_reason
					);
					$this->properties_model->nlm_property($prop_id, $nlm_params);

					// update deleted to 1
					/*$update_property_data = array(
						'agency_deleted' => 1,
						'deleted' => 1,
						'deleted_date' => date('Y-m-d H:i:s'),
						'booking_comments' => "No longer managed as of ".date("d/m/Y")." - by agency.",
						'is_nlm' => 1,
						'nlm_timestamp' => date('Y-m-d H:i:s'),
						'nlm_display' => 1,
						'nlm_by_agency' => $this->session->agency_id
					);
					$update_property_data = $this->security->xss_clean($update_property_data);
					$data_update_property = $this->properties_model->update_property($prop_id,$update_property_data);

					$agentFullName = $this->gherxlib->agent_full_name();

					// if property has completed job with a price this month and service changed this month
					$this_month_start = date("Y-m-01");
					$this_month_end = date("Y-m-t");

					if( $prop_id > 0 ){

						// get completed job this month
						$job_sql_str = "
						SELECT j.`id`
						FROM `jobs` AS j               
						WHERE j.`property_id` = {$prop_id}
						AND j.`status` = 'Completed'
						AND j.`job_price` > 0
						AND j.`date` BETWEEN '{$this_month_start}' AND '{$this_month_end}'                         
						";
						$job_sql = $this->db->query($job_sql_str);

						// get status change this month
						$ps_sql_str = "
						SELECT ps.`status_changed`
						FROM `property` AS p 
						INNER JOIN `property_services` AS ps ON p.`property_id` = ps.`property_id`
						WHERE p.`property_id` = {$prop_id} 
						AND CAST( ps.`status_changed` AS DATE ) BETWEEN '{$this_month_start}' AND '{$this_month_end}'
						";
						$ps_sql = $this->db->query($ps_sql_str);

						if( $job_sql->num_rows() > 0 && $ps_sql->num_rows() > 0 ){

							// DO nothing, leave is_payable as it is

						}else{

							// clear is_payable
							$update_ps_sql_str = "
							UPDATE `property_services`
							SET `is_payable` = 0   
							WHERE `property_id` = {$prop_id}            
							";
							$this->db->query($update_ps_sql_str);

						}

					}*/
					

					/*
					// cancel jobs
					$udpate_jobs_data = array(
						'status' => 'Cancelled',
						'comments' => "This property was marked No Longer Managed by ".$agentFullName." on ".date("d/m/Y")." and all jobs cancelled",
						'cancelled_date' => date('Y-m-d')
					);
					$params = array('prop_id'=> $prop_id);
					$update_jobs = $this->properties_model->update_jobs($params,$udpate_jobs_data);
					*/

					// changed to manual update
					/*if( $prop_id > 0 ){

						$this->db->query("
						UPDATE `jobs`
						SET
							`status` = 'Cancelled',
							`comments` = 'This property was marked No Longer Managed by ".$agentFullName." on ".date("d/m/Y")." and all jobs cancelled',
							`cancelled_date` = '".date('Y-m-d')."'
						WHERE `status` != 'Completed'
						AND `property_id` = {$prop_id}
						");

					}*/


					// update status changed (property services)
					/* Disable as per Joe's instruction

					$update_property_services_data = array(
						'status_changed' => date('Y-m-d H:i:s')
					);
					$update_property_services = $this->properties_model->update_property_services($prop_id,$update_property_services_data);

					*/


					// insert job log
					/*$details = "Job <strong>Cancelled</strong> due to <strong>NLM</strong>";
					$params = array(
						'title' => 15,
						'details' => $details,
						'display_in_vjd' => 1,
						'agency_id' => $this->session->agency_id,
						'created_by' => $this->session->aua_id,
						'property_id' => $prop_id,
						'job_id' => $job_id
					);
					$this->jcclass->insert_log($params);
					*/

					/*
					// insert property logs
					$details_prop_event_log =  "Agency Marked property as No Longer Managed";
					$params_ab = array(
						'title' => 6, //no longer managed
						'details' => $details_prop_event_log,
						'display_in_vpd' => 1,
						'display_in_vad' => 1,
						'display_in_portal' => 1,
						'agency_id' => $this->session->agency_id,
						'created_by' => $this->session->aua_id,
						'property_id' => $prop_id
					);
					$this->jcclass->insert_log($params_ab);
					*/

					//Insert Log
					/*$details =  "{p_address} has been marked as No Longer Managed by ".$agentFullName;
					$params = array(
						'title' => 6,
						'details' => $details,
						'display_in_vpd' => 1,
						'display_in_vad' => 1,
						'display_in_portal' => 1,
						'agency_id' => $this->session->agency_id,
						'created_by' => $this->session->aua_id,
						'property_id' => $prop_id,
					);
					$this->jcclass->insert_log($params); */


					$data['status'] = true;
					$data['stat_msg'] = " Property Marked 'No Longer Managed' ";

				}


			}



		}

		echo json_encode($data);

	}

	//get tenant count by prop_id via ajax
	function get_tenant_count(){

		$prop_id = $this->input->post('prop_id');
		$params_active = array('property_id'=>$prop_id, 'active' => 1);
		$active_tenants = $this->properties_model->get_new_tenants($params_active);
		if(!empty($active_tenants)){
			$data['count'] =  count($active_tenants);
		}else{
			$data['count'] =  0;
		}

		echo json_encode($data);

	}


	public function calendar(){

		$data['title'] = "Job's Calendar";

		// view
		$this->load->view('templates/home_header', $data);
		$this->load->view('jobs/calendar', $data);
		$this->load->view('templates/home_footer');

	}

	public function json_cal_job(){

		$data = array();
		$agency_id = $this->session->agency_id;
		$country_id = $this->session->country_id;
		$search = $this->input->post('search');

		$custom_where = " (j.status='Booked' OR j.status='Completed') ";

		$sel_query = "
			j.`id` AS j_id,
			j.`service` AS j_service,
			j.`property_id` AS j_property_id,
			j.`work_order`,
			j.`property_vacant`,
			j.`status` AS j_status,
			j.`date` AS j_date,
			j.`created` AS j_created,

			ajt.`type` AS ajt_type,
			ajt.`short_name` AS ajt_short_name,

			p.`property_id`,
			p.`address_1` AS p_address_1,
			p.`address_2` AS p_address_2,
			p.`address_3` AS p_address_3,
			p.`state` AS p_state,
			p.`postcode` AS p_postcode,

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
			'search' => $search,


		);

		$job_in_a_cal_list = $this->jobs_model->get_jobs($params);

		if(!empty($job_in_a_cal_list)){
			foreach($job_in_a_cal_list->result() as $row){

				$color = ($row->j_status=="Booked")?'event-blue':'event-green';

				$data[] = array(
					'id' => $row->property_id,
					'title' => $row->p_address_1." ".$row->p_address_2.", ".$row->p_address_3." ".$row->p_state." ".$row->p_postcode,
					'start' => $row->j_date,
					'className' => $color,
					'status' => $row->j_status,
					'job_type' => $row->ajt_type
				);
			}
		}


		echo json_encode($data);


	}







}
