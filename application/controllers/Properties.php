<?php
class Properties extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('user_accounts_model');
		$this->load->model('properties_model');
		$this->load->model('profile_model');
		$this->load->helper('download');
		$this->load->library('email');
		$this->load->model('mixed_db_model');
		$this->load->library('pagination');

		$this->load->helper('url');
        $this->load->model('Alarm_job_type_model');
		
		$this->load->library('HashEncryption');
		// Instantiate HashEncryption Class
		$this->hashIds = new HashEncryption($this->config->item('hash_salt'), 6);
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

		$propertyServiceTypes = $this->db->select("ps.property_services_id, ps.property_id, ps.`service`, ajt.`id` AS ajt_id, ajt.`type`, ajt.`short_name`, ajt.html_id")
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

		
		$latestJobsForProperties = $this->db->select("MAX(date) AS latest, property_id, assigned_tech")
			->from('jobs')
			->where_in("property_id", $propertyIds)
			->group_start()
			->where("status", "Completed")
			->or_where("status", "Merged Certificates")
			->group_end()
			->where("del_job", 0)
			->where("assigned_tech!=",2) //not Upfront Bill
			->group_by("property_id")
			->get()->result();

		for ($x = 0; $x < count($latestJobsForProperties); $x++) {
			$propertiesById[$latestJobsForProperties[$x]->property_id]['last_service_date'] = $latestJobsForProperties[$x]->latest;
		}
	}

	public function index($aua_id=null){

		$this->load->model('jobs_model'); //load jobs model

		$data['title'] = 'My Properties';

		// photo patch
		$data['user_photo_path'] = '/uploads/user_accounts/photo/';

		// property manager dropdown list
		//$data['property_manager_list'] = $this->properties_model->get_property_manager_by_agencyv2($this->session->agency_id);
		$data['property_manager_list'] = $this->properties_model->get_property_manager_by_agency($this->session->agency_id);

		// service type dropdown list
		$data['service_list'] = $this->properties_model->get_all_service_type_by_agency($this->session->agency_id);

		// pagination/search/tab attributes/values
		$condi = array(); // condition array for tab 1 sats
		$condi2 = array(); // condition array for tab 2 nonsats
		$condi3 = array(); // condition array for tab 3 onece-off

		$type = $this->input->get('type');


		$agency_state = '';
		$agency = $this->db->select('state')
			->from('agency')
			->where('agency_id',$this->session->agency_id)
			->get();
		if ($agency->num_rows() > 0){
			$row = $agency->row(); 
			$agency_state =  $row->state;
		}

		$data['agency_state'] = $agency_state;
		$data['type_sats'] = "sats";
		$data['type_nonSats'] = "nonSats";
		$data['type_onceOff'] = "onceOff";
		$data['sales_prop'] = "sales_prop";
		$data['type_not_compliant'] = "not_compliant";

		$per_page = $this->config->item('pagi_per_page'); // per page applied to 3 tabs


		//calculate offset/start number
		// if(!$this->input->get('per_page')){
		// 	$offset = 0;
		// }else{
		// 	$offset = $this->input->get('per_page');
		// }


		/**  TAB SWITCHING */

		if( !$this->input->get('type') || ($this->input->get('type')=='sats' || $this->input->get('type')=='sas') ){  //IF TAB IS SATS

			/*$prop_notOnceOff= $this->properties_model->get_once_off_service();
			$propNotInArray_onceOff = "";
			foreach($prop_notOnceOff as $notInRow2){
				$propNotInArray_onceOff .= ",".$notInRow2->j_property_id;
			}
			$proNotIn2 = explode(',',substr($propNotInArray_onceOff,1));
			*/

			// IS-SATS total rows
			//$exclude_sales_prop = "p.is_sales!=1";
			$exclude_sales_prop = "p.is_sales!=1 AND (j.prop_comp_with_state_leg=1 OR j.prop_comp_with_state_leg IS NULL)";

			// SATS ALL LIST
			$params = array(
				'sel_query'=>1,
				//'pm_id' => $aua_id,
				'custom_where' => $exclude_sales_prop,
				'custom_joins_arr' => array(
					array(
						'join_table' => 'jobs as j',
						'join_on' => 'p.property_id = j.property_id',
						'join_type' => 'left'
					)
				)
			);

			// all property list that is SATS SERVICED filtered by agency
			$properties = $this->properties_model->get_property_list_ver2($this->session->agency_id, $condi,$params)->result_array();
   
			$this->attachAdditionalProperties($properties);
			$data['prop_list'] = $properties;


		}elseif($this->input->get_post('type')=="nonsats" || $this->input->get('type')=='nonsas'){ // IF TAB IS NON SATS

			$excludedPropertyIds = [];

			// PROPERTY LIST NONSATS SERVICE ------------------------------
			$sel_query_not_in = "ps.`property_id`";
			$params_not_in = array(
				'sel_query'=> $sel_query_not_in,
				'pm_id' => $aua_id
			);

			$tmp_agency_id = $this->session->agency_id;
			//$tmp_agency_id = 6930;

			if($tmp_agency_id != 6930){
				$prop_list2 = $this->properties_model->get_property_list_ver2($this->session->agency_id,NULL,$params_not_in)->result();

				foreach($prop_list2 as $notInRow){
					$excludedPropertyIds[] = $notInRow->property_id;
				}
			}


			//ALL NON SATS LISTING
			$condi2_params = array(
				'sel_query' => 1,
                'concat_select' => ",MAX(j.assigned_tech) as assigned_tech, MAX(j.date) as jdate",
				//'pm_id' => $aua_id
				'pm_id' => $pm_post,
                'custom_joins_arr' => [
                    [
                        'join_table' => 'jobs as j',
                        'join_on' => 'p.property_id = j.property_id',
                        'join_type' => 'left'
                    ]
                ]
			);

			//$properties = $this->properties_model->get_property_list_non_sats_ver2($this->session->agency_id, $excludedPropertyIds, $condi2,$condi2_params)->result_array();  //disabled by Gherx > created new query for NON SATS
			$properties = $this->properties_model->get_property_list_non_sats_ver3($this->session->agency_id, $excludedPropertyIds, $condi2,$condi2_params)->result_array();

            $this->attachAdditionalProperties($properties);
			$data['properties'] = $properties;


		}elseif($this->input->get_post('type')=="onceOff"){  // IF TAB IS ONCE OFF



			$params_tt = array();

			$ooServiceProperties = $this->properties_model->get_once_off_service($condi3, $params_tt);
			$properties = [];
			if($ooServiceProperties){
				$i=0;
				foreach($ooServiceProperties as $row){
					$property = [];

					$property['prop_address'] = $row->address_1." ".$row->address_2.", ".$row->address_3;
					$property['property_id'] = $row->j_property_id;
					$property['nlm_display'] = $row->nlm_display;

					// get last service by property id

					// get property managers by id
					$property['aua_id'] = $row->agency_user_account_id;
					$property['pm_fname'] = $row->properties_model_fname;
					$property['pm_lname'] = $row->properties_model_lname;
					$property['photo'] = $row->photo;

					$properties[] = $property;
				}
			}

			$this->attachAdditionalProperties($properties);

			$data['properties'] = $properties;

		}elseif($this->input->get_post('type')=="sales_prop"){ 

			$sales_prop_only = "p.is_sales=1";


			// SATS ALL LIST
			$params = array(
				'sel_query'=>1,
				'custom_where' => $sales_prop_only
			);


			// all property list that is SATS SERVICED filtered by agency
			$properties = $this->properties_model->get_property_list_ver2($this->session->agency_id, $condi,$params)->result_array();
			$this->attachAdditionalProperties($properties);
			$data['prop_list_sales_prop'] = $properties;


		}elseif($this->input->get_post('type')=="not_compliant"){ 

			//No Compliant tab moved out from not_compliant condition to outside in order for bubble to work
			$tt_no_compliant_sel = "
				j1.property_id AS j_property_id,
				j1.id AS j_id,
				j1.`service` AS j_service,
				p.`property_id`,
				p.`address_1`,
				p.`address_2`,
				p.`address_3`,
				p.`state`,
				p.`postcode`,
				aua.agency_user_account_id,
				aua.fname AS pm_fname,
				aua.lname AS pm_lname,
				ejn.not_compliant_notes";

			$tt_params = array(
				'sel_query'=> $tt_no_compliant_sel
			);
			$tt_no_compliant_q = $this->properties_model->get_no_compliant_prop_for_properties_page($tt_params);
			$jobs = $tt_no_compliant_q->result_array();

			//total rows
			$tt_params_total = array(
				'sel_query' => "p.property_id",
			);
			$not_compliant_total_rows = $this->properties_model->get_no_compliant_prop_for_properties_page($tt_params_total)->num_rows();

			$this->attachAdditionalProperties($jobs);

			$data['list_not_compliant'] = $jobs;
		}

		$this->load->view('templates/home_header',$data);
		$this->load->view('properties/index', $data);
		$this->load->view('templates/home_footer');

	}



	/**
	 * ADD NEW PROPERTIES
	 * action add_property - return last id
	 * action add_property_services
	 * action add_property_type
	 * action add_jobs - return last id
	 * action add_bundle_services
	 * action add_job_log
	 * action add_agency_user_activity (log)
	 * action add_property_event_log
	 */
	public function add(){

		$data['title'] = 'Add a New Property';
		// photo patch
		$data['user_photo_path'] = '/uploads/user_accounts/photo/';
		// property manager dropdown list
		//$data['property_manager_list'] = $this->properties_model->get_property_manager_by_agencyv2($this->session->agency_id);
		$data['property_manager_list'] = $this->properties_model->get_property_manager_by_agency($this->session->agency_id);
		//get agency services data
		$agencyServices = $this->properties_model->get_agency_services($this->session->agency_id);


		if($agencyServices){

			$serviceIds = [];
			$agencyServicesById = [];
			$alarmJobTypeIds = [];
			for ($x = 0; $x < count($agencyServices); $x++) {
				$agencyService =& $agencyServices[$x];

				$agencyService->alarm_job_type = null;

				$alarmJobTypeIds[] = $agencyService->service_id;

				$agencyServicesById[$agencyService->agency_services_id] =& $agencyService;
			}

			$asAlarmJobTypes = $this->db->select("id, bundle, bundle_ids")
				->from("alarm_job_type")
				->where_in("id", $alarmJobTypeIds)
				->get()->result();

			$asAlarmJobTypesById = [];

			for ($y = 0; $y < count($asAlarmJobTypes); $y++) {
				$asAlarmJobTypesById[$asAlarmJobTypes[$y]->id] =& $asAlarmJobTypes[$y];
			}

			for ($x = 0; $x < count($agencyServices); $x++) {
				$agencyService =& $agencyServices[$x];

				if (isset($asAlarmJobTypesById[$agencyService->service_id])) {
					$agencyService->alarm_job_type = $asAlarmJobTypesById[$agencyService->service_id];
				}
			}

			$agency_services = [];
			for ($x = 0; $x < count($agencyServices); $x++){

				$row = $agencyServices[$x];

				$agency_service = [];
				$agency_service['service_id'] = $row->service_id;
				$agency_service['type'] = $row->type;
				$agency_service['full_name'] = $row->full_name;
				$agency_service['price'] = $row->price;

				//get bundle type
				// $bundle = $this->properties_model->get_alarm_job_type_bundle($row->service_id);

				if (!is_null($row->alarm_job_type)) {
					$agency_service['is_bundle'] = $row->alarm_job_type->bundle;
					$agency_service['bundle_ids'] = $row->alarm_job_type->bundle_ids;
					$agency_service['id'] = $row->alarm_job_type->id;
					$agency_service['excluded_bundle_ids'] = $row->alarm_job_type->excluded_bundle_ids;
				}

				$agency_services[] = $agency_service;
			}

			$data['agency_services'] = $agency_services;
		}

		$prop_vacant = $this->input->post('prop_vacant');

		//validation
		$this->form_validation->set_rules('address_1', 'Street No.', 'required');
		$this->form_validation->set_rules('address_2', 'Sreet Name', 'required');
		$this->form_validation->set_rules('address_3', 'Suburb', 'required');
		$this->form_validation->set_rules('state', 'State', 'required');
		$this->form_validation->set_rules('postcode', 'Postcode', 'required');
		$this->form_validation->set_rules('pm', 'Property Manager', 'required');

		if ( $this->form_validation->run() == true ) {
            $completeAddress = $this->input->post('address_1') . " " . $this->input->post(
                    'address_2'
                ) . " " . $this->input->post('address_3') . " " . $this->input->post(
                    'state'
                ) . " " . $this->input->post('postcode');
            $duplicate_prop = $this->properties_model->check_property_duplicate($completeAddress);

            // if 'No Property Manager' checkbox is ticked, set PM as null
            $no_pm_chk = $this->input->post('no_pm_chk');
            $pm = ($no_pm_chk == 1) ? null : $this->input->post('pm');

            // somehow the db setting is strict, it cannot be submitted if empty, needs to default to 0 if empty
            $service_garage = (is_numeric($this->input->post('service_garage'))) ? $this->input->post(
                'service_garage'
            ) : 0;

            if (!$duplicate_prop) {
                // properties array post values
                $post_field = array(
                    'address_1'            => $this->input->post('address_1'),
                    'address_2'            => $this->input->post('address_2'),
                    'address_3'            => $this->input->post('address_3'),
                    'state'                => $this->input->post('state'),
                    'postcode'             => $this->input->post('postcode'),
                    'agency_id'            => $this->session->agency_id,
                    'landlord_firstname'   => $this->input->post('landlord_firstname'),
                    'landlord_lastname'    => $this->input->post('landlord_lastname'),
                    'landlord_mob'         => $this->input->post('landlord_mobile'),
                    'landlord_ph'          => $this->input->post('landlord_landline'),
                    'landlord_email'       => $this->input->post('landlord_email'),
                    'key_number'           => $this->input->post('key_number'),
                    'alarm_code'           => $this->input->post('alarm_code'),
                    'property_managers_id' => $pm,
                    'added_by'             => '',
                    'holiday_rental'       => $this->input->post('holiday_rental'),
                    'service_garage'       => $service_garage,
                    'comments'             => $this->input->post('job_comments'),
                    'pm_id_new'            => $pm,
                    'lat'                  => $this->input->post('prop_lat'),
                    'lng'                  => $this->input->post('prop_lng')
                );
                // insert property - return last id
                $post_field = $this->security->xss_clean($post_field);

                $this->db->trans_begin();

                $result = $this->properties_model->add_property($post_field);

                // get property ID properly
                $property_id = ($result > 0) ? $result : null;
                $lockbox_code = $this->input->post('lockbox_code');

                if ($property_id > 0) {
                    // check if lockbox exist
                    $lb_sql = $this->db->query(
                        "
				SELECT COUNT(`id`) AS pl_count
				FROM `property_lockbox`
				WHERE `property_id` = {$property_id}
				"
                    );
                    $lb_row = $lb_sql->row();

                    if ($lb_row->pl_count > 0) { // it exist, update

                        $this->db->query(
                            "
					UPDATE `property_lockbox`
					SET `code` = '{$lockbox_code}'
					WHERE `property_id` = {$property_id}
					"
                        );
                    } else { // doesnt exist, insert

                        if ($lockbox_code != '') {
                            $this->db->query(
                                "
						INSERT INTO 
						`property_lockbox`(
							`code`,
							`property_id`
						)
						VALUE(
							'{$lockbox_code}',
							{$property_id}
						)	
						"
                            );
                        }
                    }
                }

                if ($result) {
                    // add property_tenants
                    if (!empty($this->input->post('tenant_firstname')) || !empty(
                        $this->input->post(
                            'tenant_lastname'
                        )
                        )) {
                        $tenant_fname = $this->input->post('tenant_firstname');

                        foreach ($tenant_fname as $index => $tenant_fname_val) {
                            if ($tenant_fname_val != "" || $this->input->post('tenant_lastname')[$index] != "") {
                                $post_array[] = array(
                                    'property_id'      => $result,
                                    'tenant_firstname' => $tenant_fname_val,
                                    'tenant_lastname'  => $this->input->post('tenant_lastname')[$index],
                                    'tenant_mobile'    => $this->input->post('tenant_mob')[$index],
                                    'tenant_landline'  => $this->input->post('tenant_ph')[$index],
                                    'tenant_email'     => $this->input->post('tenant_email')[$index],
                                    'active'           => 1
                                );
                            }
                        }
                        if (!empty($post_array)) {
                            $this->properties_model->add_tenants(
                                $post_array,
                                true
                            ); //  param insert batch otherwise 0 for normal
                        }
                    }


                    $alarm_job_type_id = $this->input->post('alarm_job_type_id');
                    $job_msg = ($this->input->post('is_new_tent') == 1) ? "New Tenancy Start " . $this->input->post(
                            'new_ten_start'
                        ) . " " . ((($this->input->post('job_comments') != "") ? $this->input->post(
                            'job_comments'
                        ) : "")) . "" : $this->input->post('job_comments');
                    $vacant_from = ($this->input->post('vacant_from') != "") ? date(
                        "Y-m-d H:i:s",
                        strtotime(str_replace("/", "-", $this->input->post('vacant_from')))
                    ) : null;
                    $vacant_to = (!empty($this->input->post('vacant_to'))) ? date(
                        "Y-m-d",
                        strtotime(str_replace("/", "-", $this->input->post('vacant_to')))
                    ) : null;

                    if ($prop_vacant == 1) {
                        $job_msg = "Property Currently Vacant From: " . $this->input->post(
                                'vacant_from'
                            ) . " - " . $this->input->post('vacant_to') . ". " . $this->input->post('job_comments');
                    }
                    if ($this->input->post('is_new_tent') == 1) {
                        $vacant_from = null;
                        $vacant_to = null;
                        if ($this->input->post('new_ten_start') != '') {
                            $vacant_from = date(
                                "Y-m-d H:i:s",
                                strtotime(str_replace("/", "-", $this->input->post('new_ten_start')))
                            );
                        }
                    }

                    $loopData = [];

                    for ($x = 0; $x < count($alarm_job_type_id); $x++) {
                        $loopData[] = [
                            'alarm_job_type_id' => $alarm_job_type_id[$x],
                            'service'           => $this->input->post('service' . $x),
                            'price'             => $this->input->post('price')[$x],
                        ];
                    }

                    $alarmJobTypes = $this->db->select("id, bundle, bundle_ids")
                        ->from("alarm_job_type")
                        ->where_in("id", $alarm_job_type_id)
                        ->get()->result();

                    $alarmJobTypesById = [];

                    for ($y = 0; $y < count($alarmJobTypes); $y++) {
                        $alarmJobTypesById[$alarmJobTypes[$y]->id] =& $alarmJobTypes[$y];
                    }

                    $work_order = $this->input->post('workorder_num');

                    //set DHA NEED PROCESSING
                    $dha_need_processing = 0;
                    if ($this->gherxlib->isDHAagenciesV2( $this->session->agency_id ) == true || $this->gherxlib->agencyHasMaintenanceProgram($this->session->agency_id) == true) {
                        $dha_need_processing = 1;
                    }

                    $has_job = false;

                    for ($i = 0; $i < count($loopData); $i++) {
                        $loopDataEntry = $loopData[$i];

                        $service = $loopDataEntry['service'];

                        // dont insert property services if NR(2), requested by Ben
                        if ($service != 2) {
                            // Property services array post values (ADD PROPERTY SERVICES)
                            $post_array = array(
                                'property_id'       => $result,
                                'alarm_job_type_id' => $loopDataEntry['alarm_job_type_id'],
                                'service'           => $service,
                                'price'             => $loopDataEntry['price'],
                                'status_changed'    => date("Y-m-d H:i:s"),
                            );

                            // if service = SATS, mark as payable
                            if ($service == 1) {
                                $post_array['is_payable'] = 1;
                            }

                            // insert property services
                            $this->properties_model->add_property_services($post_array);
                        }


                        // if picked services add jobs
                        if ($service == 1) {
                            // insert add_property_type
                            $this->properties_model->add_property_type(
                                $this->security->xss_clean([
                                    'property_id'       => $result,
                                    'alarm_job_type_id' => $loopDataEntry['alarm_job_type_id'],
                                ])
                            );


                            // get price increase excluded agency
                            $piea_sql = $this->db->query(
                                "
						SELECT *
						FROM `price_increase_excluded_agency`
						WHERE `agency_id` = {$this->session->agency_id}                  
						AND (
							`exclude_until` >= '" . date('Y-m-d') . "' OR
							`exclude_until` IS NULL
						)
						"
                            );

                            // price increase variation
                            $price_var_params = array(
                                'service_type' => $loopDataEntry['alarm_job_type_id'],
                                'property_id'  => $property_id
                            );
                            $price_var_arr = $this->system_model->get_property_price_variation($price_var_params);
                            $dynamic_price = $price_var_arr['dynamic_price_total'];
                            // $price_text = substr($price_var_arr['price_text'],1);

                            // jobs array post values (ADD JOBS)
                            $post_array3 = array(
                                'job_type'            => "Yearly Maintenance",
                                'property_id'         => $result,
                                'status'              => "Send Letters",
                                'work_order'          => $work_order,
                                'service'             => $loopDataEntry['alarm_job_type_id'],
                                //'job_price' => $loopDataEntry['price'],
                                'job_price'           => $dynamic_price,
                                'comments'            => $job_msg,
                                'property_vacant'     => $prop_vacant,
                                'start_date'          => $vacant_from,
                                'due_date'            => $vacant_to,
                                'dha_need_processing' => $dha_need_processing
                            );
                            // insert jobs - return last id
                            $job_id = $this->properties_model->add_jobs($post_array3);

                            //UPDATE INVOICE DETAILS
                            $this->gherxlib->updateInvoiceDetails($job_id);

                            // job alarms sync is not needed here bec property is new and no alarms

                            // mark is_eo is also not needed since it needs the latest completed job


                            // if bundle (ADD BUNDLE)
                            $alarmJobType = $alarmJobTypesById[$loopDataEntry['alarm_job_type_id']];
                            $isbundle = $alarmJobType->bundle;

                            if ($isbundle == 1) {
                                $bundle_ids = explode(",", trim($alarmJobType->bundle_ids));

                                $bundleData = [];

                                foreach ($bundle_ids as $newbi) {
                                    $bundleData[] = [
                                        'job_id'            => $job_id,
                                        'alarm_job_type_id' => $newbi,
                                    ];
                                }

                                $this->db->insert_batch("bundle_services", $bundleData);
                            }

                            //insert job log (inside loop to catch each services selected)
                            $details2 = "{p_address} added and job created";
                            $params2 = array(
                                'title'          => 1, //New job created
                                'details'        => $details2,
                                'display_in_vjd' => 1,
                                'agency_id'      => $this->session->agency_id,
                                'created_by'     => $this->session->aua_id,
                                'property_id'    => $result,
                                'job_id'         => $job_id
                            );
                            $this->jcclass->insert_log($params2);


                            $has_job = true;
                        }
                    }

                    //Create log without Job Log
                    if ($has_job) {
                        //Insert Log
                        $details = "{p_address} added and job created";
                        $params = array(
                            'title'             => 2, //New Property Added
                            'details'           => $details,
                            'display_in_vpd'    => 1,
                            'display_in_portal' => 1,
                            'agency_id'         => $this->session->agency_id,
                            'created_by'        => $this->session->aua_id,
                            'property_id'       => $result
                        );
                        $this->jcclass->insert_log($params);
                    } else {
                        //Insert Log without Job
                        $details = "{p_address} and NO job created";
                        $params = array(
                            'title'             => 2, //New Property Added
                            'details'           => $details,
                            'display_in_vpd'    => 1,
                            'display_in_portal' => 1,
                            'agency_id'         => $this->session->agency_id,
                            'created_by'        => $this->session->aua_id,
                            'property_id'       => $result,
                        );
                        $this->jcclass->insert_log($params);
                    }

                    if ($this->db->trans_status()) {
                        $this->db->trans_commit();
                        //SEND E-MAIL WHEN NEW PROPERTY SUCCESSFULLY ADDED
                        $mail_prop_id = $result;
                        //$mail_email = "itsmegherx@gmail.com";
                        $email_data['agent_name'] = $this->gherxlib->agent_full_name();


                        $agency_info = $this->properties_model->get_agency_info($this->session->agency_id);
                        $email_data['agency_name'] = $agency_info->agency_name;

                        //get agency emails
                        $to_email_agency = array();
                        $agency_email_res = explode("\n", trim($agency_info->agency_emails));
                        foreach ($agency_email_res as $new_agency_email_res) {
                            $new_agency_email_res2 = preg_replace('/\s+/', '', $new_agency_email_res);
                            if (filter_var($new_agency_email_res2, FILTER_VALIDATE_EMAIL)) {
                                $to_email_agency[] = $new_agency_email_res2;
                            }
                        }

                        $email_data['mail_prop_address'] = $this->input->post('address_1') . " " . $this->input->post(
                                'address_2'
                            ) . ", " . $this->input->post('address_3') . " " . $this->input->post(
                                'state'
                            ) . " " . $this->input->post('postcode');

                        $email_params_active = array('property_id' => $mail_prop_id, 'active' => 1);
                        $email_data['active_tenants'] = $this->properties_model->get_new_tenants($email_params_active);

                        $email_data['get_property_services'] = $this->properties_model->get_property_services_list(
                            $mail_prop_id
                        );

                        $email_data['job_comments'] = $this->input->post('job_comments');

                        $email_data['landlord_firstname'] = $this->input->post('landlord_firstname');
                        $email_data['landlord_lastname'] = $this->input->post('landlord_lastname');
                        $email_data['landlord_mobile'] = $this->input->post('landlord_mobile');
                        $email_data['landlord_landline'] = $this->input->post('landlord_landline');
                        $email_data['landlord_email'] = $this->input->post('landlord_email');

                        $email_data['holiday_rental'] = $this->input->post('holiday_rental');
                        $email_data['prop_vacant'] = $prop_vacant;
                        $email_data['vacant_from'] = $this->input->post('vacant_from');
                        $email_data['vacant_to'] = $this->input->post('vacant_to');
                        $email_data['is_new_tent'] = $this->input->post('is_new_tent');
                        $email_data['new_ten_start'] = $this->input->post('new_ten_start');

                        $subject = 'New property added by ' . $email_data['agency_name'];
                        $this->email->to(make_email('authority'));
                        $this->email->subject($subject);
                        $this->email->message($this->load->view('emails/new-property-email', $email_data, true));
                        $result = $this->email->send();
                        if (!$result) {
                            log_message('error', 'Email failed to send: ' . $subject);
                        }

                        //Only send if 'All New Jobs Emailed to Agency?' preferences option checked!
                        if ($agency_info->new_job_email_to_agent == 1) {
                            $subject = 'Ready for Booking';
                            $this->email->to($new_to_email_agency);
                            $this->email->subject($subject);
                            $this->email->message($this->load->view('emails/ready-for-booking', $email_data, true));
                            $result2 = $this->email->send();
                            if (!$result2) {
                                log_message('error', 'Email failed to send: ' . $subject);
                            }
                        }

                        // set success message
                        if ($result) {
                            $flash = [
                                'status'      => 'success',
                                'success_msg' => 'Property Successfully Added',
                            ];
                        } else {
                            $flash = [
                                'status'    => 'error',
                                'error_msg' => 'Error sending email notifications, please contact support',
                            ];
                        }
                        $this->session->set_flashdata($flash);
                    } else {
                        $this->db->trans_rollback();

                        // set error message
                        $this->session->set_flashdata(
                            array('error_msg' => 'Error: Please try again!', 'status' => 'error')
                        );
                    }
                } else {
                    $this->db->trans_rollback();

                    // set error message
                    $this->session->set_flashdata(array('error_msg' => 'Error: Please try again!', 'status' => 'error'));
                }
            } else {
                $this->session->set_flashdata(array('error_msg' => 'Error: Property already exist', 'status' => 'error'));
            }

            redirect(base_url('properties/add'));
        }

		$this->load->view('templates/home_header', $data);
		$this->load->view('properties/add', $data);
		$this->load->view('templates/home_footer');

	}


	// check propety dupctcate-------
	public function check_property_duplicate(){

		$complete_address = $this->input->post('complete_address');
		$res = $this->properties_model->check_property_duplicate($complete_address);

		if($res){
			$jData['match'] =  1;
			$jData['agency_id'] = $res->agency_id;
			$jData['property_id'] =  $res->property_id;
		}else{
			$jData['match'] =  0;
		}
		echo json_encode($jData);

	}



	/**
	 * property details VPD
	 * get_new_tenants (new tenants system)
	 * action get_agency_services
	 * action get_last_service_row (get last service)
	 * action get_last_yearly_maintenance_row
	 */
	public function property_detail(){
		
		$this->load->model('api_model');		

		$data['agent_phone'] = $this->jcclass->get_agency_phone()->agent_number;

		// no direct access
		if( !isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], "/properties") === -1 ) {
			redirect('/properties/','refresh');
		}

		$data['user_photo_path'] = '/uploads/user_accounts/photo/';


		$data['title'] = "Property Detail";
		$data['prop_id'] = $prop_id = ($this->uri->segment(3))?$this->uri->segment(3):redirect(base_url('/properties'));
		$pm_agency = $this->profile_model->get_agency($this->session->agency_id); //get agency info
		$data['checkCurrentPropertyServices'] = $this->properties_model->get_current_services_status($data['prop_id']); //check if property services has selected service

		// get property detail
		$data['prop_det'] = ($this->properties_model->get_property_detail_by_id($data['prop_id']))?$this->properties_model->get_property_detail_by_id($data['prop_id']):redirect(base_url('/properties'));

		// property manager dropdown list
		//$data['property_manager_list'] = $this->properties_model->get_property_manager_by_agencyv2($this->session->agency_id);
		$data['property_manager_list'] = $this->properties_model->get_property_manager_by_agency($this->session->agency_id);

		//get agency services data
		$prop_service_info = $this->properties_model->get_property_services_list_detail_by_prop_id($data['prop_id']);

		//get job status
		$data['job_status'] = $this->gherxlib->NLMjobStatusCheck($data['prop_id']);

		$this->db->select("
			a_s.service_id,
			a_s.price,
			a_s.agency_services_id,
			a_s.agency_id,

			ajt.bundle AS ajt_bundle,
			ajt.bundle_ids AS ajt_bundle_ids,
			ajt.excluded_bundle_ids AS ajt_excluded_bundle_ids,
			ajt.type,
			ajt.full_name,
			ajt.short_name
		");
		$this->db->from('agency_services as a_s');
		$this->db->join('alarm_job_type as ajt', 'ajt.id = a_s.service_id', 'left');
		$this->db->where('a_s.agency_id', $this->session->agency_id);
		$this->db->where('ajt.active', 1);
		$this->db->order_by('a_s.agency_services_id', "ASC");

		$services = $this->db->get()->result();

		$alarmJobTypeIds = [];

		$agencyServices = [];
		if ($services) {
			foreach ($services as $row) {
				$agencyService['service_id'] = $row->service_id;
				$agencyService['type'] = $row->type;
				$agencyService['full_name'] = $row->full_name;
				$agencyService['price'] = $row->price;

				$agencyService['is_bundle'] = $row->ajt_bundle;
				$agencyService['bundle_ids'] = $row->ajt_bundle_ids;
				$agencyService['job_type_id'] = $row->service_id;
				$agencyService['excluded_bundle_ids'] = $row->ajt_excluded_bundle_ids;

				$agencyService['new_price'] = $agencyService['price'];
				$agencyService['psi_val'] = '';
				$agencyService['service_type_short'] = $row->short_name;

				$agencyServices[] = $agencyService;

				$alarmJobTypeIds[] = $row->service_id;
			}

			$alarmJobTypeIds = array_unique($alarmJobTypeIds);

            $this->db->select('alarm_job_type_id,price,service');
            $this->db->from('property_services as ps');
            $this->db->where('ps.property_id', $prop_id);
			$this->db->where_in('ps.alarm_job_type_id', $alarmJobTypeIds);

			$alarmJobTypes = $this->db->get()->result();

			$alarmJobTypesById = [];

			foreach ($alarmJobTypes as $alarmJobType) {
				$alarmJobTypesById[$alarmJobType->alarm_job_type_id] = $alarmJobType;
			}

			for ($x = 0; $x < count($agencyServices); $x++) {
				$agencyService =& $agencyServices[$x];

				if (isset($alarmJobTypesById[$agencyService['service_id']])) {
					$alarmJobType = $alarmJobTypesById[$agencyService['service_id']];
					$agencyService['new_price'] = $alarmJobType->price;
					$agencyService['psi_val'] = $alarmJobType->service;
					$agencyService['ps_service'] = $alarmJobType->service;
				}
			}

			$data['agency_services'] = $agencyServices;

			// get last service
			$data['last_service'] = $this->properties_model->get_last_service_row($data['prop_id']);

			if($data['last_service']){

				//new redirect to CI version >Gherx
				$encrypted_job_id_last_service = $this->hashIds->encodeString($data['last_service']->id);
				$data['invoice_url'] =  $this->config->item('crmci_link')."/pdf/invoices/{$encrypted_job_id_last_service}"; // invoice url
				$data['certificate_url'] = $this->config->item('crmci_link')."/pdf/certificates/{$encrypted_job_id_last_service}"; // certificate url
				$data['combined_url'] = $this->config->item('crmci_link')."/pdf/combined/{$encrypted_job_id_last_service}"; // combined url
			}

			// get Last yearly maintenance info
			$data['last_yearly_maintenance'] = $this->properties_model->get_last_yearly_maintenance_row($data['prop_id']);

			if($data['last_yearly_maintenance']){

				//new redirect to CI version >Gherx
				$encrypted_job_id_last_yearly_maintenance = $this->hashIds->encodeString($data['last_yearly_maintenance']->id);
				$data['lym_invoice_url'] =  $this->config->item('crmci_link')."/pdf/invoices/{$encrypted_job_id_last_yearly_maintenance}"; // invoice url
				$data['lym_certificate_url'] = $this->config->item('crmci_link')."/pdf/certificates/{$encrypted_job_id_last_yearly_maintenance}"; // certificate url
				$data['lym_combined_url'] = $this->config->item('crmci_link')."/pdf/combined/{$encrypted_job_id_last_yearly_maintenance}"; // combined url
			}

			// get alarm details
			$data['alarm_det'] = $this->properties_model->alarm_det($data['prop_id']);

			//get corded window details
			$data['corded_windows'] = $this->properties_model->get_corded_windows($data['prop_id']);

			//get safety switch location
			$data['safety_switch'] = $this->properties_model->get_safety_switch_location($data['prop_id']); //return row

			//get safety switch details
			$data['safety_switch_details'] = $this->properties_model->get_safety_switch_details($data['prop_id']); //return object

			//get water meter details/info
			$data['water_meter_details'] = $this->properties_model->get_water_meter_details($data['prop_id']); //return row

			##get user info
			$data['user'] = $this->user_accounts_model->get_user_account_via_id($this->session->aua_id);

		}

		// get service with CW
		$ps_sql_str = "
		SELECT COUNT(ps.`property_services_id`) AS ps_count
		FROM `property_services` AS ps		
		WHERE ps.`alarm_job_type_id` IN(9,14,18)
		AND ps.`service` = 1
		AND ps.`property_id` = {$prop_id}
		";
		$ps_sql = $this->db->query($ps_sql_str);
		$data['has_cw_bundle_service'] = (  $ps_sql->row()->ps_count > 0 )?true:false;

		// get service with SA, WM and SA.WM
		$ps_sql_str = "
		SELECT COUNT(ps.`property_services_id`) AS ps_count
		FROM `property_services` AS ps		
		WHERE ps.`alarm_job_type_id` IN(2,7,11)
		AND ps.`service` = 1
		AND ps.`property_id` = {$prop_id}
		";
		$ps_sql = $this->db->query($ps_sql_str);
		$data['has_sa_wm_service'] = (  $ps_sql->row()->ps_count > 0 )?true:false;

		// get service count 
		$ps_sql_main = "
		SELECT COUNT(ps.`property_services_id`) AS ps_count
		FROM `property_services` AS ps		
		WHERE ps.`service` = 1
		AND ps.`property_id` = {$prop_id}		
		";
		$ps_sql_main = $this->db->query($ps_sql_main);
		$prop_ser_count =  $ps_sql_main->row()->ps_count;
		$data['prop_ser_count'] = $prop_ser_count;

		// get CW service
		$ps_sql_str = "
		SELECT COUNT(ps.`property_services_id`) AS ps_count
		FROM `property_services` AS ps		
		WHERE ps.`alarm_job_type_id` IN(6)
		AND ps.`service` = 1
		AND ps.`property_id` = {$prop_id}		
		";
		$ps_sql = $this->db->query($ps_sql_str);
		
		// if only has CW service
		$data['only_has_cw'] = ( $prop_ser_count == 1 && $ps_sql->row()->ps_count > 0 )?true:false;

		$data['is_nlm'] = $this->properties_model->get_property_status($prop_id);

		// load templates
		$this->load->view('templates/home_header', $data);
		$this->load->view('properties/detail', $data);
		$this->load->view('templates/home_footer');
	}


	public function ajax_update_service_type(){

		$property_id = $this->input->get_post('property_id');
		$agency_id = $this->input->get_post('agency_id');
		$from_service_type = $this->input->get_post('from_service_type');
		$to_service_type = $this->input->get_post('to_service_type');

		$today = date('Y-m-d H:i:s');
		$this_month_start = date("Y-m-01");
		$this_month_end = date("Y-m-t");

		// email settings

		// check if IC service type is availble on agency
		$agency_serv_sql_str = "
		SELECT 
			`agency_services_id`,
			`price`
		FROM `agency_services` 
		WHERE `agency_id` = {$agency_id}
		AND `service_id` = {$to_service_type}
		";

		$agency_serv_sql = $this->db->query($agency_serv_sql_str);
		if( $agency_serv_sql->num_rows() > 0 ){

			$agency_serv_row = $agency_serv_sql->row();
			$agency_serv_price = $agency_serv_row->price; // agency service price    
			
			// get agency variation
			$price_var_params = array(
				'service_type' => $to_service_type,
				'agency_id'  => $agency_id
			);
			$price_var_arr = $this->system_model->get_agency_price_variation($price_var_params);
			$agency_price_var = $price_var_arr['dynamic_price_total'];

			if( $to_service_type > 0 ){		

				// get property
				$prop_sql = $this->db->query("
				SELECT 
					`address_1`,
					`address_2`,
					`address_3`
				FROM `property`
				WHERE `property_id` = {$property_id} 
				");
				$prop_row = $prop_sql->row();
				$prop_address = "{$prop_row->address_1} {$prop_row->address_2} {$prop_row->address_3}";

				// get FROM service type name
				$ajt_sql = $this->db->query("
				SELECT 
					ps.`price`,
					ajt.`type`
				FROM `property_services` AS ps
				LEFT JOIN `alarm_job_type` AS ajt ON ps.`alarm_job_type_id` = ajt.`id`				
				WHERE ps.`alarm_job_type_id` = {$from_service_type} 
				");
				$ajt_row = $ajt_sql->row();
				$from_service_type_name = $ajt_row->type;
				$from_ps_price = $ajt_row->price;

				// get TO service type name
				$ajt_sql = $this->db->query("
				SELECT `type`
				FROM `alarm_job_type`
				WHERE `id` = {$to_service_type} 
				");
				$ajt_row = $ajt_sql->row();
				$to_service_type_name = $ajt_row->type;
				
				// get status changed date          
				$ps_sql_str = "
				SELECT `status_changed` 
				FROM `property_services`
				WHERE `alarm_job_type_id` = {$from_service_type} 
				AND `property_id` = {$property_id}  
				";       
				$ps_sql = $this->db->query($ps_sql_str); 
				$ps_sql_row = $ps_sql->row();
				$status_changed = date('Y-m-d',strtotime($ps_sql_row->status_changed));

				// if status changed is within the current month its payable
				$is_payable = ( $status_changed >= $this_month_start && $status_changed <= $this_month_end )?1:0;

				// update service
				$service_to = 1; // SATS

				// clear, this will also fix issues on duplicates
				$delete_sql_str = "
				DELETE 
				FROM `property_services`
				WHERE `alarm_job_type_id` = {$from_service_type} 
				AND `property_id` = {$property_id}  
				";
				$this->db->query($delete_sql_str); 

				// clear, this will also fix issues on duplicates
				$delete_sql_str = "
				DELETE 
				FROM `property_services`
				WHERE `alarm_job_type_id` = {$to_service_type} 
				AND `property_id` = {$property_id}  
				";
				$this->db->query($delete_sql_str); 							

				// insert service type
				$insert_serv_type_sql_str = "
				INSERT INTO
				`property_services` (
					`property_id`,
					`alarm_job_type_id`,
					`service`,
					`price`,
					`status_changed`,
					`is_payable`
				)
				VALUE(
					{$property_id},
					{$to_service_type},
					{$service_to},
					{$agency_price_var},
					'{$today}',
					{$is_payable}
				)       
				"; 
				$this->db->query($insert_serv_type_sql_str);

				/*
				$details =  "{p_address} Service changed from <b>{$from_service_type_name}</b> to <b>{$to_service_type_name}</b>";
				$params = array(
					'title' => 3, // Property Service Updated
					'details' => $details,
					'display_in_vpd' => 1,
					'display_in_portal' => 1,
					'agency_id' => $this->session->agency_id,
					'created_by' => $this->session->aua_id,
					'property_id' => $property_id
				);
				$this->jcclass->insert_log($params);	
				*/

				if( $agency_price_var > 0 ){
					//$details =  "{p_address} service updated to <b>{$to_service_type_name}</b>, job service and price of <b>\${$agency_serv_price}</b> updated to match";

					// sir dan wants to update the log to like this
					$details = "{p_address} services was updated from <b>".$from_service_type_name." $".number_format($from_ps_price,2)."</b> to <b>".$to_service_type_name." $".number_format($agency_price_var,2)."</b>. Service and job prices were both adjusted";
				}else{
					$details =  "{p_address} service updated to <b>{$to_service_type_name}</b>, job updated to match";
				}												
				
				$params = array(
					'title' => 3, // Property Service Updated
					'details' => $details,
					'display_in_vpd' => 1,
					'agency_id' => $this->session->agency_id,
					'created_by' => $this->session->aua_id,
					'property_id' => $property_id
				);
				$this->jcclass->insert_log($params);

				// get active jobs
				$job_sql_str = "
				SELECT `id` AS jid, `job_type`, `status`
				FROM `jobs`
				WHERE `property_id` = {$property_id} 				
				AND `status` NOT IN('Pre Completion','Merged Certificates','Completed','Cancelled')
				";
				$job_sql = $this->db->query($job_sql_str);

				$has_active_ym_job =  false;
				if( $job_sql->num_rows() > 0  ){

					foreach( $job_sql->result() as $job_row ){

						if( $job_row->jid > 0 ){

							$update_job_field = null;
							if( $job_row->job_type == 'Yearly Maintenance' ){

								// price increase variation
								$price_var_params = array(
									'service_type' => $to_service_type,
									'property_id' => $property_id
								);
								$price_var_arr = $this->system_model->get_property_price_variation($price_var_params);
								$dynamic_price = $price_var_arr['dynamic_price_total'];

								// update service type and price
								$update_job_field = "
								`service` = {$to_service_type},
								`job_price` = {$dynamic_price} 
								";	
							
								$has_active_ym_job = true;

							}else{

								// update service type
								$update_job_field = "
								`service` = {$to_service_type}
								";					

							}
							
							// update active jobs to new service type
							$update_job_sql_str = "
							UPDATE `jobs`
							SET {$update_job_field}
							WHERE `id` = {$job_row->jid}
							";
							$this->db->query($update_job_sql_str);								

						}					

					}	

				}


				// get YM jobs with precomp
				$job_sql_str = "
				SELECT `id` AS jid, `job_type`, `status`
				FROM `jobs`
				WHERE `property_id` = {$property_id} 
				AND `job_type` = 'Yearly Maintenance'				
				AND `status` = 'Pre Completion' 
				";
				$job_sql = $this->db->query($job_sql_str);

				if( $job_sql->num_rows() > 0 ){				
					
					$email_data['property_address'] = $prop_address;


					$this->email->to(make_email('info'));
					//$this->email->to('vaultdweller123@gmail.com');				
					$this->email->subject('Property Service Updated');
					$body = $this->load->view('emails/update_service_has_ym_job_precomp', $email_data, TRUE);
					$this->email->message($body);
					$this->email->send();								

				}																				
								

				// no active YM job found
				$has_completed_ym = false;
				if( $has_active_ym_job == false ){

					// look for completed YM
					$job_sql = $this->db->query("
					SELECT `id` AS jid, `date`
					FROM `jobs`
					WHERE `property_id` = {$property_id} 	
					AND `job_type` = 'Yearly Maintenance'			
					AND `status` = 'Completed'	
					ORDER BY `date` DESC
					LIMIT 1				
					");					

					if( $job_sql->num_rows() > 0 ){ // found completed YM job

						$has_completed_ym = true;
						$job_row = $job_sql->row();

						// price increase variation
						$price_var_params = array(
							'service_type' => $to_service_type,
							'property_id' => $property_id
						);
						$price_var_arr = $this->system_model->get_property_price_variation($price_var_params);
						$dynamic_price = $price_var_arr['dynamic_price_total'];

						//create new job here....
						$assigned_tech = 1; // Other Supplier
						$post_data = array(
							'job_type' => "Yearly Maintenance",
							'property_id' => $property_id,
							'status' => "Completed",
							'service' => $to_service_type,
							'job_price' => $dynamic_price,
							'created' => date("Y-m-d H:i:s"),
							'date' => $job_row->date,
							'assigned_tech' => $assigned_tech
						);
						$post_data = $this->security->xss_clean($post_data);
						// insert jobs - return last id
						$job_id = $this->properties_model->add_jobs($post_data);

						//UPDATE INVOICE DETAILS
						$this->gherxlib->updateInvoiceDetails($job_id);

						//RUN JOB SYNC
						$this->gherxlib->runJobSync($job_id,$to_service_type,$property_id);

						// mark is_eo
						$this->system_model->mark_is_eo($job_id);

						$details = "Yearly Maintenance created to match new property service of <b>{$to_service_type_name}</b>";
						$params = array(
							'title' => 1,  // New Job Created
							'details' => $details,
							'display_in_vjd' => 1,
							'agency_id' => $this->session->agency_id,
							'created_by' => $this->session->aua_id,
							'property_id' => $property_id,
							'job_id' => $job_id
						);
						$this->jcclass->insert_log($params);

					}
					
					// property has no active job and no YM completed in 11 months
					$last_11_months = date("Y-m-d",strtotime("-11 months"));

					// look for completed YM
					$job_sql = $this->db->query("
					SELECT COUNT(`id`) AS jcount
					FROM `jobs`
					WHERE `property_id` = {$property_id} 	
					AND `job_type` = 'Yearly Maintenance'			
					AND `status` = 'Completed'	
					AND `date` >= '{$last_11_months}'											
					");
					$jcount = $job_sql->row()->jcount;

					if( $jcount == 0 ){

						$email_data['property_address'] = $prop_address;
						$email_data['property_id'] = $property_id;


						$this->email->to(make_email('info'));
						//$this->email->to('vaultdweller123@gmail.com');				
						$this->email->subject('Property Service Updated');
						$body = $this->load->view('emails/update_service_no_active_job_no_ym_comp_11_months', $email_data, TRUE);
						$this->email->message($body);
						$this->email->send();

					}
														

				}


				// no active YM job found and no completed YM
				if( $has_active_ym_job == false && $has_completed_ym == false ){					

					echo "Bugo";
					exit();

					$email_data['property_address'] = $prop_address;
					$email_data['property_id'] = $property_id;


					$this->email->to(make_email('info'));
					//$this->email->to('vaultdweller123@gmail.com');				
					$this->email->subject('Property Service Updated');
					$body = $this->load->view('emails/update_service_has_no_ym_no_completed_ym', $email_data, TRUE);
					$this->email->message($body);
					$this->email->send();						

				}
				

				
			} 


		}

	}


	public function ajax_update_service_type_status(){

		$property_id = $this->input->get_post('property_id');
		$agency_id = $this->input->get_post('agency_id');
		$update_service_status_to = $this->input->get_post('update_service_status_to');	
		$current_service_type = $this->input->get_post('current_service_type');
		
		if( $property_id > 0 && $current_service_type > 0 ){

			//update property services (service)
			$data_post = array(
				'service' => $update_service_status_to
			);

			// check if property service status is really changed
			$ps_sql = $this->db->query("
			SELECT `service`
			FROM `property_services`
			WHERE `property_id` = {$property_id}
			AND `alarm_job_type_id` = {$current_service_type}
			");
			$ps_row = $ps_sql->row();
			$ps_status = $ps_row->service;

			// only update status_changed if service status is really changed
			if( $ps_status != $update_service_status_to ){
				$data_post['status_changed'] = date("Y-m-d H:i:s");
			}

			$data_post = $this->security->xss_clean($data_post);
			$this->properties_model->vpd_update_property_services($property_id,$current_service_type, $data_post);

			// DIY or Other Provider, cancel jobs(intructed by ben)
			if( $current_service_type > 0 && ( is_numeric($update_service_status_to) && $update_service_status_to == 0 ) || $update_service_status_to == 3 ){

				$service_status_txt = null;
				if( is_numeric($update_service_status_to) && $update_service_status_to == 0 ){
					$service_status_txt = 'DIY';
				}else if( $update_service_status_to == 3 ){
					$service_status_txt = 'Other Provider'; 
				}

				//Cancel jobs
				$log_txt = "This property service was updated to {$service_status_txt} by ".$this->gherxlib->agent_full_name()." on ".date("d/m/Y")." and all jobs cancelled";
				$this->db->query("
				UPDATE `jobs`
				SET
					`status` = 'Cancelled',
					`comments` = '{$log_txt}',
					`cancelled_date` = '".date('Y-m-d')."'
				WHERE `status` != 'Completed'
				AND `property_id` = {$property_id}
				AND `service` = {$current_service_type}
				");

				// insert logs
				$params_vpd_log = array(
					'title' => 3, // Property Service Updated
					'details' => $log_txt,
					'display_in_vpd' => 1,			
					'agency_id' => $this->session->agency_id,
					'created_by' => $this->session->aua_id,
					'property_id' => $property_id,
				);
				$this->jcclass->insert_log($params_vpd_log);

			}
			
		}		

	}


	public function ajax_add_new_service_type(){

		$property_id = $this->input->get_post('property_id');
		$agency_id = $this->input->get_post('agency_id');
		$new_service_type = $this->input->get_post('new_service_type');
		$upgrage_to_ic = $this->input->get_post('upgrage_to_ic');
		$preferred_alarm = $this->input->get_post('preferred_alarm');
		$state = $this->input->get_post('state');	
			
		$today = date('Y-m-d H:i:s');

		// check if IC service type is availble on agency
		$agency_serv_sql_str = "
		SELECT 
			`agency_services_id`,
			`price`
		FROM `agency_services` 
		WHERE `agency_id` = {$agency_id}
		AND `service_id` = {$new_service_type}
		";	
		$agency_serv_sql = $this->db->query($agency_serv_sql_str);
		if( $agency_serv_sql->num_rows() > 0 ){

			$agency_serv_row = $agency_serv_sql->row();
			$agency_serv_price = $agency_serv_row->price; // agency service price      
			
			$price_var_params = array(
				'service_type' =>$new_service_type,
				'property_id'  => $property_id
			);
			$price_var_arr = $this->system_model->get_property_price_variation($price_var_params);
			$job_price = $price_var_arr['dynamic_price_total'];

			if( $new_service_type > 0 ){

				// clear by property ID and service type, this will also fix issues on duplicates
				$delete_sql_str = "
				DELETE 
				FROM `property_services`
				WHERE `alarm_job_type_id` = {$new_service_type} 
				AND `property_id` = {$property_id}  
				";
				$this->db->query($delete_sql_str); 

				// new service type
				$ajt_sql = $this->db->query("
				SELECT `type`
				FROM `alarm_job_type`
				WHERE `id` = {$new_service_type}
				");
				$ajt_row = $ajt_sql->row();
				$service_type_new = $ajt_row->type;

				// ben's mark/unmark payable logic
				$this_month_start = date("Y-m-01");
				$this_month_end = date("Y-m-t");
				$is_payable = 1;

				$ps_sql_str = "
				SELECT COUNT(`property_services_id`) AS ps_count
				FROM `property_services`
				WHERE `property_id` = {$property_id}         
				";
				$ps_sql = $this->db->query($ps_sql_str);
				$ps_row = $ps_sql->row();
				$ps_count =  $ps_row->ps_count;

				if( $ps_count == 0 ){
					$is_payable = 1;
				}else{
					
					// loop through existing property services                
					$ps_sql =  $this->db->query("
					SELECT `service`, `status_changed` 
					FROM `property_services`                                 
					WHERE `property_id` = {$property_id}    
					ORDER BY `status_changed` DESC
					");
	
					$non_sats_count = 0;					
					$sixty_one_days_ago = date("Y-m-d",strtotime("-61 days"));
					$sixt_one_days_older = false;
	
					foreach( $ps_sql->result() as $ps_row ){

						$status_changed = date('Y-m-d',strtotime($ps_row->status_changed));
	
						if( $ps_row->service != 1 ){ // non SATS
							$non_sats_count++;
						}
	
						if( $status_changed < $sixty_one_days_ago ){
							$sixt_one_days_older = true;
						}
	
					}
	
					if( $ps_sql->num_rows() == $non_sats_count && $sixt_one_days_older ){
	
						// loop through existing property services                
						$ps_sql = $this->db->query("
						SELECT 
							ps.`is_payable`,
							ajt.`type` AS service_type_name 
						FROM `property_services` AS ps  
						LEFT JOIN `alarm_job_type` AS ajt ON ps.`alarm_job_type_id` = ajt.`id`              
						WHERE ps.`property_id` = {$property_id}    
						");
	
						foreach( $ps_sql->result() as $ps_row ){
	
							if( $ps_row->is_payable == 1 ){ 

								$details =  "Property Service <b>{$ps_row->service_type_name}</b> unmarked <b>payable</b>";
								$params = array(
									'title' => 3, // Property Service Updated
									'details' => $details,
									'display_in_vpd' => 1,									
									'agency_id' => $this->session->agency_id,
									'created_by' => $this->session->aua_id,
									'property_id' => $property_id
								);
								$this->jcclass->insert_log($params);
	
							}                    
	
						} 
						
						// clear is payable
						$this->db->query("
						UPDATE `property_services`
						SET `is_payable` = 0
						WHERE `property_id` = {$property_id}    
						");
	
						// is payable state for new service
						$is_payable = 1;
	
					}else{
	
						// is payable state for new service
						$is_payable = 0;
	
					}          

				}   

				// TO        
				$service_to = 1; // SATS				

				// insert service type
				$insert_serv_type_sql_str = "
				INSERT INTO
				`property_services` (
					`property_id`,
					`alarm_job_type_id`,
					`service`,
					`price`,
					`status_changed`,
					`is_payable`
				)
				VALUE(
					{$property_id},
					{$new_service_type},
					{$service_to},
					{$agency_serv_price},
					'{$today}',
					{$is_payable}
				)       
				";  
				$this->db->query($insert_serv_type_sql_str);   
							

				$details =  "{p_address} service added: <b>{$service_type_new}</b>";
				$params = array(
					'title' => 3, // Property Service Updated
					'details' => $details,
					'display_in_vpd' => 1,
					'display_in_portal' => 1,
					'agency_id' => $this->session->agency_id,
					'created_by' => $this->session->aua_id,
					'property_id' => $property_id
				);
				$this->jcclass->insert_log($params);

				if( $state == 'QLD' && $upgrage_to_ic == 1 ){ // QLD and IC upgrade

					// create IC upgrade job			
					$post_data = array(
						'job_type' => "IC Upgrade",
						'property_id' => $property_id,
						'status' => "Send Letters",
						'service' => $new_service_type,
						'job_price' => $job_price,
						'created' => date("Y-m-d H:i:s")
					);
					$post_data = $this->security->xss_clean($post_data);
					// insert jobs - return last id
					$job_id = $this->properties_model->add_jobs($post_data);

					if( $property_id > 0 && $preferred_alarm > 0 ){

						// store preferred alarm
						$this->db->query("
						UPDATE `property`
						SET `preferred_alarm_id` = {$preferred_alarm}
						WHERE `property_id` = {$property_id}  
						");

						// get preferred alarm MAKE
						$alarm_pwr_sql = $this->db->query("
						SELECT `alarm_make` 
						FROM `alarm_pwr`
						WHERE `alarm_pwr_id` = {$preferred_alarm}
						");
						$alarm_pwr_row = $alarm_pwr_sql->row();

					}	
					
					// insert logs
					$title = 65; // Property Update
					$details = "{agency_user:{$this->session->aua_id}} has given " . config_item('COMPANY_NAME_SHORT') . " permission to upgrade this property to Interconnected Smoke Alarms, {$alarm_pwr_row->alarm_make} alarms chosen.";

					$params = array(
						'title' => $title,
						'details' => $details,
						'display_in_vjd' => 1,
						'display_in_vpd' => 1,
						'display_in_portal' => 1,
						'agency_id' => $this->session->agency_id,
						'created_by' => $this->session->aua_id,
						'property_id' => $property_id,
						'job_id' => $job_id
					);

					$this->jcclass->insert_log($params);

				}else{ // default

					// create job			
					$post_data = array(
						'job_type' => "Yearly Maintenance",
						'property_id' => $property_id,
						'status' => "Send Letters",
						'service' => $new_service_type,
						'job_price' => $job_price,
						'created' => date("Y-m-d H:i:s")
					);
					$post_data = $this->security->xss_clean($post_data);
					// insert jobs - return last id
					$job_id = $this->properties_model->add_jobs($post_data);

				}				

				//UPDATE INVOICE DETAILS
				$this->gherxlib->updateInvoiceDetails($job_id);

				//RUN JOB SYNC
				$this->gherxlib->runJobSync($job_id,$new_service_type,$property_id);

				// mark is_eo
				$this->system_model->mark_is_eo($job_id);


			} 


		}

	}


	public function ajax_non_active_service_update(){

		$property_id = $this->input->get_post('property_id');
		$agency_id = $this->input->get_post('agency_id');
		$non_active_ps_id_arr = $this->input->get_post('non_active_ps_id_arr');		
		$non_active_service_status_arr = $this->input->get_post('non_active_service_status_arr');
		$today = date('Y-m-d H:i:s');

		foreach( $non_active_ps_id_arr as $index => $non_active_ps_id ){

			if( $non_active_ps_id > 0 && is_numeric($non_active_service_status_arr[$index]) ){

				// insert service type
				$insert_serv_type_sql_str = "
				UPDATE `property_services` 
				SET `service` = {$non_active_service_status_arr[$index]}
				WHERE `property_services_id` = {$non_active_ps_id}   
				AND `property_id` = {$property_id}
				";          
				$this->db->query($insert_serv_type_sql_str); 

			}    

		}		

	}



	/**
	 * UPDDATE PROPERTY TO NO LONGER MANAGED (via ajax)
	 * action update_property (update property status)
	 * action update_jobs (cancel jobs)
	 * action update_property_services (update property services)
	 * action add_property_event_log (add log)
	 * action add_agency_user_activity (insert agency activity)
	 */
	public function no_longer_managed(){

		$data['status'] = false;

		$agent_nlm_from = $this->security->xss_clean($this->input->post('agent_nlm_from'));
		$agent_nlm_reason = $this->security->xss_clean($this->input->post('agent_nlm_reason'));
		$prop_id = $this->security->xss_clean($this->input->post('prop_id'));
		$reason_they_left = $this->security->xss_clean($this->input->post('reason_they_left'));
		$other_reason = $this->security->xss_clean($this->input->post('other_reason'));


		// get property info for agency log
		$prop_info = $this->properties_model->get_property_detail_by_id($prop_id);

		if($this->gherxlib->NLMjobStatusCheck($prop_id)===TRUE){

			$cntry = $this->gherxlib->getCountryViaCountryId();

			$data['status'] = true;
			$data['has_active_jobs'] = true;
			$data['stat_msg'] = "This Property has active jobs, please contact " . config_item('COMPANY_NAME_SHORT') . " on ".$cntry->tenant_number;

		}else{											

			$nlm_params = array(
				'agent_nlm_from'=> $agent_nlm_from,
				'agent_nlm_reason'=> $agent_nlm_reason,
				'reason_they_left'=> $reason_they_left,
				'other_reason'=> $other_reason
			);
			$this->properties_model->nlm_property($prop_id, $nlm_params);

			$data['status'] = true;

		}


		/*$update_property_data = array(
			'agency_deleted' => 1,
			'deleted' => 1,
			'deleted_date' => date('Y-m-d H:i:s'),
			'booking_comments' => "No longer managed as of ".date("d/m/Y")." - by agency.",
			'is_nlm' => 1,
			'nlm_timestamp' => date('Y-m-d H:i:s'),
			'nlm_by_agency' => $this->session->agency_id
		);

		// check if property has money owing and needs to verify paid
		if( $this->system_model->check_verify_paid($prop_id) == true ){
			$update_property_data['nlm_display'] = 1;
		}

		$data_update_property = $this->properties_model->update_property($prop_id,$update_property_data);
		if($data_update_property){
			$data['status'] = true;
		}else{
			$data['error_property']  = "Server Error (property): Please contact admin!";
		}*/

		/*
		$udpate_jobs_data = array(
			'status' => 'Cancelled',
			'comments' => "This property was marked No Longer Managed by ".$this->gherxlib->agent_full_name()." on ".date("d/m/Y")." and all jobs cancelled",
			'cancelled_date' => date('Y-m-d')
		);
		$udpate_jobs_data = $this->security->xss_clean($udpate_jobs_data);
		$params = array('prop_id'=> $prop_id);
		$update_jobs = $this->properties_model->update_jobs($params,$udpate_jobs_data);
		*/

		// changed to manual update
		/*if( $prop_id > 0 ){

			$this->db->query("
			UPDATE `jobs`
			SET
				`status` = 'Cancelled',
				`comments` = 'This property was marked No Longer Managed by ".$this->gherxlib->agent_full_name()." on ".date("d/m/Y")." and all jobs cancelled',
				`cancelled_date` = '".date('Y-m-d')."'
			WHERE `status` != 'Completed'
			AND `property_id` = {$prop_id}
			");

			$data['status'] = true;

		}else{

			$data['error_jobs']  = "Server Error (jobs): Please contact admin!";

		}*/

		/*
		if($update_jobs){
			$data['status'] = true;
		}else{
			$data['error_jobs']  = "Server Error (jobs): Please contact admin!";
		}
		*/

		/* Disable as per Joe's instruction
		$update_property_services_data = array(
			'status_changed' => date('Y-m-d H:i:s')
		);
		$update_property_services = $this->properties_model->update_property_services($prop_id,$update_property_services_data);
		*/

		/*if($update_property_services){
			$data['status'] = true;
		}else{
			$data['error_prop_services']  = "Server Error (property services): Please contact admin!";
		}*/


		// if property has completed job with a price this month and service changed this month
		/*$this_month_start = date("Y-m-01");
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
		


		//Insert Log
		/*$details =  "{p_address} has been marked as No Longer Managed by ".$this->gherxlib->agent_full_name()."<br/> Details: ".$agent_nlm_reason;
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
		$this->jcclass->insert_log($params);*/


		echo json_encode($data);

	}



	/**
	 * Update Landlord (via ajax)
	 */
	public function update_landlord(){

		$data['status'] = false;
		$prop_id = $this->security->xss_clean($this->input->post('prop_id'));

		// get property info for agency log
		$prop_info = $this->properties_model->get_property_detail_by_id($prop_id);

		$update_landlord_data = array(
			'landlord_firstname' => $this->input->post('landlord_firstname'),
			'landlord_lastname' => $this->input->post('landlord_lastname'),
			'landlord_mob' => $this->input->post('landlord_mob'),
			'landlord_ph' => $this->input->post('landlord_ph'),
			'landlord_email' => $this->input->post('landlord_email')
		);
		$update_landlord_data = $this->security->xss_clean($update_landlord_data);

		$update_landlord = $this->properties_model->update_landlord($prop_id,$update_landlord_data);
		if($update_landlord){

			$data['status'] = true;
			$data['landlord_firstname'] = $update_landlord->landlord_firstname;
			$data['landlord_lastname'] = $update_landlord->landlord_lastname;
			$data['landlord_mob'] = $update_landlord->landlord_mob;
			$data['landlord_ph'] = $update_landlord->landlord_ph;
			$data['landlord_email'] = $update_landlord->landlord_email;


			//Insert Log
			$details =  "Landlord Updated for {p_address}";
			$params = array(
				'title' => 5,
				'details' => $details,
				'display_in_vpd' => 1,
				'display_in_portal' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id,
				'property_id' => $prop_id,
			);

			$this->jcclass->insert_log($params);

		}


		echo json_encode($data);

	}

	/**
	 * Update comment (via ajax)
	 */
	public function update_comment(){

		$data['status'] = false;
		$prop_id = $this->security->xss_clean($this->input->post('prop_id'));

		// get property info for agency log
		$prop_info = $this->properties_model->get_property_detail_by_id($prop_id);

		$update_comment_data = array(
			'comments' => $this->input->post('comments'),
		);
		$update_comment_data = $this->security->xss_clean($update_comment_data);

		$update_comment = $this->properties_model->update_property($prop_id,$update_comment_data);
		if($update_comment){

			$data['status'] = true;
			$data['comments'] = $update_comment->comments;


			//Insert Log
			$details =  "Comment Updated for {p_address}";
			$params = array(
				'title' => 5,
				'details' => $details,
				'display_in_vpd' => 1,
				'display_in_portal' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id,
				'property_id' => $prop_id,
			);

			$this->jcclass->insert_log($params);

		}


		echo json_encode($data);

	}


	/**
	 * UPDATE TENANTS (ajax)
	 * action deactivate/reactivate tenants
	 */
	public function update_tenant(){

		$data['status'] = false;
		$prop_id = $this->security->xss_clean($this->input->post('prop_id'));
		$tenant_id = $this->security->xss_clean($this->input->post('tenant_id'));
		$action = $this->security->xss_clean($this->input->post('action'));
		$is_details = $this->security->xss_clean($this->input->post('is_details'));

		$jobs = $this->properties_model->countpropertyJobs($prop_id);

		if($jobs > 0 && $is_details==1){
			$data['status'] = true;
			$data['error'] = "Cannot $action the tenants as this property has a job booked via Entry Notice. Please contact our office to update the tenant details.";
			
		} else {
			$prop_info = $this->properties_model->get_property_detail_by_id($prop_id); //get infor for agency log/activity

			if($action && $action=='deactivate'){
				$deactivate_data = array(
					'active' => 0
				);
				$deactivate_data = $this->security->xss_clean($deactivate_data);

				$this->db->trans_start();
				$this->properties_model->active_deactive($tenant_id,$deactivate_data);

				//insert agency activity - tenant removed
				$details = "Tenant Removed for {p_address}";
				$params = array(
					'title' => 7,
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

					foreach($fetch_active_job as $new_row){
						$details = "Tenant Removed for {p_address}";
						$params = array(
							'title' => 7,
							'details' => $details,
							'display_in_vjd' => 1,
							'agency_id' => $this->session->agency_id,
							'created_by' => $this->session->aua_id,
							'property_id' => $prop_id,
							'job_id' => $new_row->id
						);
						$this->jcclass->insert_log($params);
					}

				}

				$this->db->trans_complete();

				$data['status'] = true;
				$data['action'] = 'deactivate';

			}else if($action && $action=='reactivate'){
				$reactivate_data = array(
					'active' => 1
				);
				$reactivate_data = $this->security->xss_clean($reactivate_data);

				$this->db->trans_start();

				$this->properties_model->active_deactive($tenant_id,$reactivate_data);

				//insert agency activity - tenant reactivate
				$details = "Tenant Reactivated for {p_address}";
				$params = array(
					'title' => 8,
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

					foreach($fetch_active_job as $new_row){
						$details = "Tenant Reactivated for {p_address}";
						$params = array(
							'title' => 8,
							'details' => $details,
							'display_in_vjd' => 1,
							'agency_id' => $this->session->agency_id,
							'created_by' => $this->session->aua_id,
							'property_id' => $prop_id,
							'job_id' => $new_row->id
						);
						$this->jcclass->insert_log($params);
					}

				}

				$this->db->trans_complete();

				$data['status'] = true;
				$data['action'] = 'reactivate';
			}
		}

		

		echo json_encode($data);

	}

	/**
	 * Update Tenant Details/info (via ajax)
	 * Update tenants details/info
	 */
	

	public function update_tenant_details(){

		$data['status'] = false;
		$prop_id = $this->security->xss_clean($this->input->post('prop_id'));
		$is_details = $this->security->xss_clean($this->input->post('is_details'));
		$tenant_id = $this->security->xss_clean($this->input->post('tenant_id'));
		$prop_info = $this->properties_model->get_property_detail_by_id($this->input->post('prop_id')); //get infor for log

		//Add email notification

		$jobs = $this->properties_model->countpropertyJobs($prop_id);

		if($jobs > 0 && $is_details==1){
			$data['status'] = true;
			$data['error'] = 'Cannot update the tenants as this property has a job booked via Entry Notice. Please contact our office to update the tenant details.';
		} else {
			#get tenant orig values
			$orig_tenant_q = $this->db->select('*')->from('property_tenants')->where('property_tenant_id', $tenant_id)->get()->row_array();

			$property_address = $this->gherxlib->prop_address($prop_id);
			$email_params = array(
				'property_address' => $property_address,
				'fname' => $this->input->post('tenant_fname'),
				'orig_fname' => $orig_tenant_q['tenant_firstname'],
				'lname' =>  $this->input->post('tenant_lname'),
				'orig_lname' => $orig_tenant_q['tenant_lastname'],
				'mobile' => $this->input->post('tenant_mobile'),
				'orig_mobile' => $orig_tenant_q['tenant_mobile'],
				'landline' => $this->input->post('tenant_landline'),
				'orig_landline' => $orig_tenant_q['tenant_landline'],
				'email' => $this->input->post('tenant_email'),
				'orig_email' => $orig_tenant_q['tenant_email']

			);
			$this->email_tenant_updates($email_params);
			$this->email->send();
			//Add email notification end

			$data_post = array(
				'tenant_firstname' => $this->input->post('tenant_fname'),
				'tenant_lastname' => $this->input->post('tenant_lname'),
				'tenant_mobile' => $this->input->post('tenant_mobile'),
				'tenant_landline' => $this->input->post('tenant_landline'),
				'tenant_email' => $this->input->post('tenant_email')
			);

			$data_post = $this->security->xss_clean($data_post);

			$this->db->trans_start();

			$update_tenant_details = $this->properties_model->update_tenant_details($tenant_id, $data_post);

			if($update_tenant_details){

			// insert agency activity
			$details = "Tenant Updated for {p_address}";
			$params = array(
				'title' => 9,
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

			if (!empty($fetch_active_job) && $fetch_active_job) {

				foreach($fetch_active_job as $new_row){
					$details = "Tenant Updated for {p_address}";
					$params = array(
						'title' => 9,
						'details' => $details,
						'display_in_vjd' => 1,
						'agency_id' => $this->session->agency_id,
						'created_by' => $this->session->aua_id,
						'property_id' => $prop_id,
						'job_id' => $new_row->id
					);
					$this->jcclass->insert_log($params);
				}
			}
				$data['status'] = true;

			} else {
				//update > no affected rows > return true to prevent page reload and show success popup instead
				$data['status'] = true;
			}

			$this->db->trans_complete();
		}
		echo json_encode($data);

	}


	/**
	 * Add New Tenant via ajax
	 */
	public function add_tenant(){
		$data['status'] = false;
		$prop_id = $this->security->xss_clean($this->input->post('prop_id'));
		$is_details = $this->security->xss_clean($this->input->post('is_details'));

		$jobs = $this->properties_model->countpropertyJobs($prop_id);

		if($jobs > 0 && $is_details==1){
			$data['status'] = true;
			$data['error'] = 'Cannot add the tenants as this property has a job booked via Entry Notice. Please contact our office to add the tenant details.';
		} else {

			$prop_info = $this->properties_model->get_property_detail_by_id($prop_id); //get infor for agency log

			//validate email
			if(filter_var($this->input->post('tenant_email'), FILTER_VALIDATE_EMAIL)){
				$tenant_email = $this->input->post('tenant_email');
			}else{
				$tenant_email = "";
			}

			$post_data = array(
				'property_id' => $prop_id,
				'tenant_firstname' => $this->input->post('tenant_fname'),
				'tenant_lastname' => $this->input->post('tenant_lname'),
				'tenant_mobile' => $this->input->post('tenant_mobile'),
				'tenant_landline' => $this->input->post('tenant_landline'),
				'tenant_email' => $tenant_email,
				'active' => 1

			);
			$post_data = $this->security->xss_clean($post_data);

			$this->db->trans_start();

			$add_tenant = $this->properties_model->add_tenants($post_data);

			if($add_tenant){

				// Insert Log
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

				}

				$data['status'] = true;

			}

			$this->db->trans_complete();

		}

		echo json_encode($data);
	}


	/**
	 * Load Tenants (active/inactive) tab
	 * Load via ajax
	 */
	public function tenants_ajax(){

		$data['title'] = "Tenants";

		$data['prop_id'] = $this->security->xss_clean($this->input->post('prop_id'));

			if($data['prop_id']){

			// get active property tenants (new)
			$params_active = array('property_id'=>$data['prop_id'], 'active' => 1);
			$data['active_tenants'] = $this->properties_model->get_new_tenants($params_active);

			// get inactive property tenants (new)
			$params_inactive = array('property_id'=>$data['prop_id'], 'active' => "!=1");
			$data['in_active_tenants'] = $this->properties_model->get_new_tenants($params_inactive);

		}else{
			redirect(base_url('properties'),'refresh');
		}

		//$this->load->view('templates/home_header',$data);
		$this->load->view('templates/tenants',$data);
		//$this->load->view('templates/home_footer');

	}

	public function vpd_update_services(){

		$serv_prop_id = $this->input->post('serv_prop_id');
		$alarm_job_type_id = $this->input->post('alarm_job_type_id');
		$property_address = $this->input->post('property_address');
		$hid_btn_update_services = $this->input->post('hid_btn_update_services');
		$successStatus = false;

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

		if($hid_btn_update_services){

			if($this->input->post('default_no_services')=="No"){
				$counter = 0;

				$hasJobs = $this->db->select('property_id, service')
					->from('jobs')
					->where_in("service", $alarm_job_type_id)
					->where("property_id", $serv_prop_id)
					->where("status !=", "Cancelled")
					->group_by("service")
					->get()->result();

				$hasJobsBySevice = [];
				for ($x = 0; $x < count($hasJobs); $x++) {
					$hasJobsBySevice[$hasJobs[$x]->service] = $hasJobs;
				}

				$latestJobsSubquery = $this->db->select('property_id, MAX(id) AS max_id')
					->from("jobs")
					->where("property_id", $serv_prop_id)
					->where_in("service", $alarm_job_type_id)
					->group_by("service")
					->get_compiled_select();

				$latestJobs = $this->db->select('id, service')
					->from("jobs AS j")
					->join("({$latestJobsSubquery}) AS j2", "j.property_id = j2.property_id AND j.id = j2.max_id", "inner")
					->get()->result();

				$latestJobsByService = [];

				for ($x = 0; $x < count($latestJobs); $x++) {
					$latestJobsByService[$latestJobs[$x]->service] = $latestJobs[$x];
				}

				$this->db->trans_begin();

				foreach($alarm_job_type_id as $key => $alarm_job_type_id_val){

					$service_status_array = array(0=>'DIY', 1=>config_item('COMPANY_NAME_SHORT'), 2=>'No Response', 3=>'Other Provider', 4=>'No Longer Managed');
					$psi_val = $this->input->post('psi_val')[$key]; //orig value
					$services_name = $this->input->post('services_name')[$key];
					$services = $this->input->post('service'.$counter);
					$alarm_job_type_id_val = $this->input->post('alarm_job_type_id')[$key];

					//update property services (service)
					$data_post = array(
						'service' => $services
					);

					// check if property service status is really changed
					$ps_sql = $this->db->query("
					SELECT `service`
					FROM `property_services`
					WHERE `property_id` = {$serv_prop_id}
					AND `alarm_job_type_id` = {$alarm_job_type_id_val}
					");
					$ps_row = $ps_sql->row();
					$ps_status = $ps_row->service;

					// only update status_changed if service status is really changed
					if( $ps_status != $services ){
						$data_post['status_changed'] = date("Y-m-d H:i:s");
					}

					$data_post = $this->security->xss_clean($data_post);
					$vpd_update_services = $this->properties_model->vpd_update_property_services($serv_prop_id, $alarm_job_type_id_val, $data_post);

					//if($vpd_update_services){

						//create new job if services = SATS otherwise cancel job
						if($services==1){ //add/create new job

							//check if jobs already exist - insert new job if no job exist
							$checkJobIfExistx = $this->properties_model->checkJobIfExist($serv_prop_id,$alarm_job_type_id_val);
							$checkJobIfExist = isset($hasJobsBySevice[$alarm_job_type_id_val]);

							if($checkJobIfExist===false){
									//create new job here....
								$post_data = array(
									'job_type' => "Yearly Maintenance",
									'property_id' => $serv_prop_id,
									'status' => "Send Letters",
									'service' => $alarm_job_type_id_val,
									'job_price' => $this->input->post('price')[$key],
									'created' => date("Y-m-d H:i:s")
								);
								$post_data = $this->security->xss_clean($post_data);
								// insert jobs - return last id
								$job_id = $this->properties_model->add_jobs($post_data);

								//UPDATE INVOICE DETAILS
								$this->gherxlib->updateInvoiceDetails($job_id);

								//RUN JOB SYNC
								$this->gherxlib->runJobSync($job_id,$alarm_job_type_id_val,$serv_prop_id);

								// mark is_eo
								$this->system_model->mark_is_eo($job_id);


								//add logs for job created
								if($services!=$psi_val){
									$details = "Job Created {$services_name} for {p_address}";
									$params = array(
										'title' => 1,  //job created
										'details' => $details,
										'display_in_vjd' => 1,
										'agency_id' => $this->session->agency_id,
										'created_by' => $this->session->aua_id,
										'property_id' => $serv_prop_id,
										'job_id' => $job_id
									);
									$this->jcclass->insert_log($params);
								}


							}

						}else{ //cancel jobs

							/*
							$params = array('prop_id'=>$serv_prop_id,'ajt_id'=> $alarm_job_type_id_val);
							$udpate_jobs_data = array(
								'status' => 'Cancelled',
								'comments' => "Job Cancelled on ".date("d/m/Y")." due to status change to {$service_status_array[$services]}",
								'cancelled_date' => date('Y-m-d')
							);
							$udpate_jobs_data = $this->security->xss_clean($udpate_jobs_data);
							$this->properties_model->update_jobs($params, $udpate_jobs_data);
							*/



							// changed to manual update
							if( $serv_prop_id > 0 && $alarm_job_type_id_val > 0 ){

								$result = $this->db->query("
								UPDATE `jobs`
								SET
									`status` = 'Cancelled',
									`comments` = 'Job Cancelled on ".date("d/m/Y")." due to status change to {$service_status_array[$services]}',
									`cancelled_date` = '".date('Y-m-d')."'
								WHERE `status` != 'Completed'
								AND `property_id` = {$serv_prop_id}
								AND `service` = {$alarm_job_type_id_val}
								");

							}

							//get match latest job id that has been cancelled
							// $job_id_query = $this->db->order_by('id','DESC')->get_where('jobs',array('property_id'=>$serv_prop_id,'service'=> $alarm_job_type_id_val));
							$orig_job_id = $latestJobsByService[$alarm_job_type_id_val]->id; // ($job_id_query->num_rows()>0)?$job_id_query->row()->id:false;

							//add job log for cancelled job
							if($services!=$psi_val){
								$details = "Job Cancelled {$services_name} for {p_address}";
								$params_log = array(
									'title' => 20,  //job cancelled
									'details' => $details,
									'display_in_vjd' => 1,
									'agency_id' => $this->session->agency_id,
									'created_by' => $this->session->aua_id,
									'property_id' => $serv_prop_id,
									'job_id' => $orig_job_id
								);
								$this->jcclass->insert_log($params_log);
							}

						}

						//get/set jobs that are changed and store into array
						if($services!=$psi_val){

							$email_data['email_services_array'][]= array(
								'service'=> $services_name,
								'service_from' => $service_status_array[$psi_val],
								'service_to' => $service_status_array[$services]
							);

						}

						//set session for success message/status
						$successStatus = true;
					/*
					}else{
						$successStatus = $successStatus || false;
					}
					*/


					$counter ++;


				} // end loop

				if($successStatus){

					// INSERT PROPERTY EVENT LOG HERE... Type 2 = property event log
					$agent_name = $this->gherxlib->agent_full_name();

					foreach($email_data['email_services_array'] as $newjoblog){
						$details = "Property Service Updated for {p_address} <br/>".$newjoblog['service']." From ".$newjoblog['service_from']." to ".$newjoblog['service_to'];

						$params = array(
							'title' => 3,
							'details' => $details,
							'display_in_vpd' => 1,
							'display_in_portal' => 1,
							'agency_id' => $this->session->agency_id,
							'created_by' => $this->session->aua_id,
							'property_id' => $serv_prop_id,
						);
						$this->jcclass->insert_log($params);
					}

					if ($this->db->trans_status()) {
						$this->db->trans_commit();
					}
					else {
						$this->db->trans_rollback();
					}

					$email_data['property_address'] = $property_address;
					$email_data['agent_name'] = $agent_name;

					$agency_info = $this->properties_model->get_agency_info($this->session->agency_id);
					$email_data['agency_name'] = $agency_info->agency_name;


					$this->email->to($to_email_agency);
					$this->email->subject('Property Service Status Changed');
					$body = $this->load->view('emails/services-status-changed', $email_data, TRUE);
					$this->email->message($body);
					$this->email->send();

				}
				else {
					$this->db->trans_rollback();
				}

			}
			else {// DEFAULT NO SERVICES SELECTED

				$counter = 0;

				$this->db->trans_begin();

				foreach($alarm_job_type_id as $key => $alarm_job_type_id_val){

					$service_status_array = array(0=>'DIY', 1=>config_item('COMPANY_NAME_SHORT'), 2=>'No Response', 3=>'Other Provider', 4=>'No Longer Managed');
					$psi_val = $this->input->post('psi_val')[$key];
					$services_name = $this->input->post('services_name')[$key];
					$services = $this->input->post('service'.$counter);
					$alarm_job_type_id = $this->input->post('alarm_job_type_id')[$key];


					// !!!OPTIMIZE
					$prop_services_exist = $this->properties_model->check_property_services_exist($serv_prop_id, $alarm_job_type_id); //check if servies already added in the property_services table (add if false otherwise update)


					if( $services == 1 ){

						$this_month_start = date("Y-m-01");
						$this_month_end = date("Y-m-t");
	
						$sixty_days_ago = date("Y-m-d",strtotime("-60 days"));
	
						// check for recent status_changed
						$prop_sql_str = "
						SELECT `status_changed` 
						FROM `property_services`
						WHERE `property_id` = {$serv_prop_id}
						ORDER BY `status_changed` DESC
						LIMIT 1
						";		
									
						$prop_sql = $this->db->query($prop_sql_str); 
						$prop_row = $prop_sql->row();
						$status_changed = date('Y-m-d',strtotime($prop_row->status_changed));	
	
						// if status change is within 60 days ago but not within this month
						if(  $status_changed >= $sixty_days_ago && !( $status_changed >= $this_month_start && $status_changed <= $this_month_end ) ){
	
							// clear is_payable
							$update_ps_sql_str = "
							UPDATE `property_services`
							SET `is_payable` = 0   
							WHERE `property_id` = {$serv_prop_id}            
							";														
							$this->db->query($update_ps_sql_str);
	
						}else{

							// update selected service to is_payable to 1
							$update_ps_sql_str = "
							UPDATE `property_services`
							SET 
								`is_payable` = 1 
							WHERE `property_id` = {$serv_prop_id}   
							AND `alarm_job_type_id` = {$alarm_job_type_id_val}         
							";													
							$this->db->query($update_ps_sql_str);

						}
	
					}
					
					
					
					if ($prop_services_exist===true) {
						//update property services (service)
						$data_post = array(
							'service' => $services
						);

						// check if property service status is really changed
						$ps_sql = $this->db->query("
						SELECT `service`
						FROM `property_services`
						WHERE `property_id` = {$serv_prop_id}
						AND `alarm_job_type_id` = {$alarm_job_type_id_val}
						");
						$ps_row = $ps_sql->row();
						$ps_status = $ps_row->service;

						// only update status_changed if service status is really changed
						if( $ps_status != $services ){
							$data_post['status_changed'] = date("Y-m-d H:i:s");
						}

						$data_post = $this->security->xss_clean($data_post);
						$vpd_update_services = $this->properties_model->vpd_update_property_services($serv_prop_id,$alarm_job_type_id_val, $data_post);
					}
					else {
						//insert property services
						$post_array_prop_services = array(
							'property_id' => $serv_prop_id,
							'alarm_job_type_id' => $alarm_job_type_id,
							'service' => $services,
							'price' => $this->input->post('price')[$key],
							'status_changed' => date("Y-m-d H:i:s"),
						);
						$post_array_prop_services = $this->security->xss_clean($post_array_prop_services);
						$vpd_add_services = $this->properties_model->add_property_services($post_array_prop_services);
					}

					//if($vpd_update_services || $vpd_add_services){

						if($services==1){ //insert property_type, job_type, job_log

							// Add property type   (ADD PROPERTY TYPE)
							$post_array_prop_type = array(
								'property_id' => $serv_prop_id,
								'alarm_job_type_id' => $alarm_job_type_id,
							);
							$post_array_prop_type = $this->security->xss_clean($post_array_prop_type);
							$this->properties_model->add_property_type($post_array_prop_type);


							//create new job here....
							$post_data_job = array(
								'job_type' => "Yearly Maintenance",
								'property_id' => $serv_prop_id,
								'status' => "Send Letters",
								'service' => $alarm_job_type_id,
								'job_price' => $this->input->post('price')[$key],
								'created' => date("Y-m-d H:i:s")
							);
							$post_data_job = $this->security->xss_clean($post_data_job);
							// insert jobs - return last id
							$job_last_id = $this->properties_model->add_jobs($post_data_job);


							// If BUNDLE > INSERT BUNDLE SERVICES > new added (Jan 24 2019)
							$ajt = $this->db->select('*')->from('alarm_job_type')->where('id',$alarm_job_type_id)->get()->row_array(); //get ajt

							if($ajt['bundle']==1){ //if bundle
								$b_ids = explode(",",trim($ajt['bundle_ids']));
								// insert bundles
								foreach($b_ids as $val){
									$ajt_post_arr = array('job_id'=>$job_last_id, 'alarm_job_type_id'=> $val);
									$this->db->insert('bundle_services',$ajt_post_arr);
								}
							}
							// If BUNDLE > INSERT BUNDLE SERVICES > new added (Jan 24 2019) end


							// add job log - type 1 = new job log
							$details = "Job Created";
							$params = array(
								'title' => 1,
								'details' => $details,
								'display_in_vjd' => 1,
								'agency_id' => $this->session->agency_id,
								'created_by' => $this->session->aua_id,
								'property_id' => $serv_prop_id,
								'job_id' => $job_last_id
							);
							$this->jcclass->insert_log($params);

						}

						$successStatus = true;

					/*
					}else{
						$successStatus = false;
						break;
					}
					*/

					$counter++;

				}

				if ($successStatus) {
					// INSERT LOG
					$details = "Property Service Updated for {p_address}";
					$params = array(
						'title' => 3,
						'details' => $details,
						'display_in_vpd' => 1,
						'display_in_portal' => 1,
						'agency_id' => $this->session->agency_id,
						'created_by' => $this->session->aua_id,
						'property_id' => $serv_prop_id
					);
					$this->jcclass->insert_log($params);

					if ($this->db->trans_status()) {
						$this->db->trans_commit();
					}
					else {
						$this->db->trans_rollback();
					}
				}
				else {
					$this->db->trans_rollback();
				}

			}

			if ($successStatus) {
				$this->session->set_flashdata(array('success_msg'=> 'Services for '.$property_address.' have been updated','status'=>'success'));
				redirect(base_url('properties/property_detail/'.$serv_prop_id));
			}
			else {
				$this->session->set_flashdata(array('error_msg'=>'Update Services Error: Please try again','status'=>'error'));
				redirect(base_url('properties/property_detail/'.$serv_prop_id));
			}

		}

	}


	public function edit_pm_properties(){

		$this->load->model('api_model');
		$this->load->model('pme_model');
		$this->load->model('property_tree_model');
		$this->load->model('palace_model');

		$data['title'] = "Edit Property Manager Properties";

		$per_page = $this->config->item('pagi_per_page');
		$offset = $this->input->get_post('offset');

		$pm_id = $this->input->get_post('pm_id');
		$search = $this->input->get_post('search');

		// property manager dropdown list
		//$data['property_manager_list'] = $this->properties_model->get_property_manager_by_agencyv2($this->session->agency_id);
		$data['property_manager_list'] = $this->properties_model->get_property_manager_by_agency($this->session->agency_id); ## fetch only default agencies excluded alt


		//$custom_where = "aua.agency_id = {$this->session->agency_id}";

		//get agency API
		$tt_api_params = array(
			'sel_query' => 'agen_api_int.connected_service,a_api_t.access_token',
			'agency_id' => $this->session->agency_id,
			'custom_joins' => array(
				'join_table' => "agency_api_tokens as a_api_t",
				'join_on' => " (agen_api_int.agency_id = a_api_t.agency_id AND agen_api_int.connected_service = a_api_t.api_id) ",
				'join_type' => "INNER"
			),
		);
		$data['tt_api'] = $this->api_model->get_agency_api_integration($tt_api_params);

		$params = array(
			'pm_id' => $pm_id,
			'search' => $search,
			'p_deleted' => 0,
			'limit' => $per_page,
			'offset' => $offset,
			'sort_list' => array(
				array(
					'order_by' => 'p.`address_2`',
					'sort' => 'ASC'
				),
				array(
					'order_by' => 'p.`address_3`',
					'sort' => 'ASC'
				)
			),
			'p_deleted' => 0,
			'display_query' => 0
		);
		$data['prop_list'] = $this->properties_model->get_all_property_list($params);

		//total rows
		$params2 = array(
			'pm_id' => $pm_id,
			'search' => $search,
			'p_deleted' => 0,
		);
		if(!empty($this->properties_model->get_all_property_list($params2))){
			$total_rows = count($this->properties_model->get_all_property_list($params2));
		}else{
			$total_rows = 0;
		}


		// pagination settings
		$config['page_query_string'] = TRUE;
		$config['query_string_segment'] = 'offset';
		$config['total_rows'] = $total_rows;
		$config['per_page'] = $per_page;
		$config['base_url'] = "/properties/edit_pm_properties/?pm_id={$pm_id}&search={$search}";

		$this->pagination->initialize($config);

		$data['pagination'] = $this->pagination->create_links();


		// pagination count
		$pc_params = array(
			'total_rows' => $total_rows,
			'offset' => $offset,
			'per_page' => $per_page
		);
		$data['pagi_count'] = $this->jcclass->pagination_count($pc_params);


		$this->load->view('templates/home_header',$data);
		$this->load->view('properties/edit_pm_properties', $data);
		$this->load->view('templates/home_footer');

	}

	public function update_property_manager(){
		$data['status'] = false;

		$prop_id = $this->input->post('prop_id');
		$pm_id = $this->input->post('pm_id');
		$insert_type = $this->input->post('insert_type');


		//insert type 1 = multiple/batch | default single update
		if($insert_type && $insert_type==1){ //multiple update

			$prop_id_array[] = $prop_id;

			$this->db->trans_start();
			foreach($prop_id as $index => $val){

				$data = array(
					'pm_id_new' => $pm_id
				);
				$udpate_pm = $this->properties_model->update_property_manager($val,$data);

				// insert log
				$details = "Property Manager Updated for {p_address}";
				$params = array(
					'title' => 18,
					'details' => $details,
					'display_in_vpd' => 1,
					'display_in_portal' => 1,
					'agency_id' => $this->session->agency_id,
					'created_by' => $this->session->aua_id,
					'property_id' => $val,
				);
				$this->jcclass->insert_log($params);

			}
			$this->db->trans_complete();

		}else{ //single update

			$data = array(
				'pm_id_new' => $pm_id
			);
			$udpate_pm = $this->properties_model->update_property_manager($prop_id,$data);

			// insert log
			$details = "Property Manager Updated for {p_address}";
			$params = array(
				'title' => 18,
				'details' => $details,
				'display_in_vpd' => 1,
				'display_in_portal' => 1,
				'agency_id' => $this->session->agency_id,
				'created_by' => $this->session->aua_id,
				'property_id' => $prop_id,
			);
			$this->jcclass->insert_log($params);

		}

		if($udpate_pm){
			$data['status'] = true;
		}

		echo json_encode($data);
	}


	public function vpd_ajax_update_key_number(){

		$data['res'] = false;
		$key_number = $this->input->post('key_number');
		$prop_id = $this->input->post('property_id');

		$data = array(
			'key_number' => $key_number
		);
		$updateKeyNumber = $this->properties_model->update_key_number($prop_id, $data);

		if($updateKeyNumber){
			$data['res'] = true;
		}

		echo json_encode($data);

	}

	public function update_lockbox_code(){
		
		$lockbox_code = $this->input->post('lockbox_code');
		$property_id = $this->input->post('property_id');

		if( $property_id > 0 ){

			// check if lockbox exist
			$lb_sql = $this->db->query("
			SELECT COUNT(`id`) AS pl_count
			FROM `property_lockbox`
			WHERE `property_id` = {$property_id}
			");
			$lb_row = $lb_sql->row();

			if( $lb_row->pl_count > 0 ){ // it exist, update

				$this->db->query("
				UPDATE `property_lockbox`
				SET `code` = '{$lockbox_code}'
				WHERE `property_id` = {$property_id}
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
						{$property_id}
					)	
					");

				}		

			}

		}		

	}


	public function vpd_ajax_update_compass_index_num(){

		$data['res'] = false;
		$compass_index_num = $this->input->post('compass_index_num');
		$prop_id = $this->input->post('property_id');

		$data = array(
			'compass_index_num' => $compass_index_num
		);
		$updateKeyNumber = $this->properties_model->update_key_number($prop_id, $data);

		if($updateKeyNumber){
			$data['res'] = true;
		}

		echo json_encode($data);

	}



	public function email_template(){

		$data['title'] = "Test Email Template";
		$data['country'] = $this->jcclass->get_country_data()->row();

		$this->load->view('emails/template/email_header.php', $data);
		$this->load->view('emails/sample', $data);
		$this->load->view('emails/template/email_footer.php', $data);

	}


	public function error_template(){

		$data['title'] = "Test Error Template";
		$data['error_header'] = "test error header";
		$data['error_txt'] = "test error text";
		$this->load->view('errors/template/error_header.php', $data);
		$this->load->view('generic/error_page', $data);
		$this->load->view('errors/template/error_footer.php', $data);

	}

	public function test_embed(){

		$data['title'] = "Test Embed";
		$this->load->view('templates/home_header',$data);
		$this->load->view('test/embed', $data);
		$this->load->view('templates/home_footer');

	}


	//get PM Via ajax
	public function getPMAjax(){

		$user_photo_path = '/uploads/user_accounts/photo/';
		$property_manager_list = $this->properties_model->get_property_manager_by_agency($this->session->agency_id);


		echo '<option value="">All</option>';

		foreach($property_manager_list as $row){
			//$selected = ($this->input->post('search_pm')==$row->agency_user_account_id)?'selected':'';
			$photo  = ($row->photo!="")?$user_photo_path.$row->photo:'/images/avatar-2-64.png';
			echo "<option data-photo='".$photo."' >".$row->fname." ".$row->lname."</option>";
		}



	}

	public function update_vpd_pm(){

		$data['status'] = false;

		$property_id = $this->input->post('prop_id');
		$pm_id = $this->input->post('pm_id');

		if($property_id!="" && $pm_id!=""){

			$update_data = array(
				'pm_id_new' => $pm_id
			);
			$this->db->where('property_id',$property_id);
			$update_pm = $this->db->update('property', $update_data);

			if($this->db->affected_rows()>0){
				$data['status'] = true;
			}

		}

		echo json_encode($data);


	}

	/**
	 * check if job is Precom/Merge/COmpleted && invoice_balance >0
	 * $params property_id
	 * return boolean
	 */
	public function ajax_check_property_jobs_status(){

		$json_stat['status'] = false;
		$prop_id = $this->input->post('property_id');
		$financial_year = $this->config->item('accounts_financial_year');
		if($prop_id){
			/* $custom_where = "(j.status = 'Pre Completion' OR j.status = 'Merged Certificates' OR j.status='To Be Invoiced' OR
			(j.status = 'Completed' AND j.invoice_balance>0 AND a.status!='target') AND (j.date >= '$financial_year' OR j.unpaid = 1))"; */

			$custom_where = "(j.status = 'Pre Completion' OR j.status = 'Merged Certificates' OR j.status='To Be Invoiced')";
			$this->db->select('j.id as j_id, a.phone as agency_phone');
			$this->db->from('`jobs` AS j');
			$this->db->join('`property` AS p', 'j.`property_id` = p.`property_id`', 'left');
			$this->db->join('`agency` AS a', 'p.`agency_id` = a.`agency_id`', 'left');
			$this->db->where('j.property_id',$prop_id);
			$this->db->where('a.agency_id', $this->session->agency_id);
			$this->db->where('j.del_job',0);
			$this->db->where($custom_where);
			$query = $this->db->get();

			if($query->num_rows()>0){
				$json_stat['status'] = true;
			}else{
				$json_stat['status'] = false;
			}
		}

		echo json_encode($json_stat);

	}

	/**
	 * Load Tenants (active/inactive) tab
	 * Load via ajax
	 */
	public function tenants_ajax_for_help_needed(){

		$data['title'] = "Tenants";

		$data['prop_id'] = $this->security->xss_clean($this->input->post('prop_id'));
		$data['remove_add_tenant_felds'] = $this->input->get_post('remove_add_tenant_felds');

			if($data['prop_id']){

			// get active property tenants (new)
			$params_active = array('property_id'=>$data['prop_id'], 'active' => 1);
			$data['active_tenants'] = $this->properties_model->get_new_tenants($params_active);

			// get inactive property tenants (new)
			$params_inactive = array('property_id'=>$data['prop_id'], 'active' => "!=1");
			$data['in_active_tenants'] = $this->properties_model->get_new_tenants($params_inactive);

		}else{
			redirect(base_url('properties'),'refresh');
		}

		//$this->load->view('templates/home_header',$data);
		$this->load->view('templates/tenants_for_help_needed',$data);
		//$this->load->view('templates/home_footer');

	}

	// short term rental ajaxy update, copied from PM update
	public function update_short_term_rental(){

		$data['status'] = false;

		$property_id = $this->input->post('prop_id');
		$holiday_rental = $this->input->post('holiday_rental');

		if( $property_id > 0 && is_numeric($holiday_rental) ){

			$update_data = array(
				'holiday_rental' => $holiday_rental
			);
			$this->db->where('property_id',$property_id);
			$this->db->update('property', $update_data);

			if($this->db->affected_rows()>0){
				$data['status'] = true;
			}

		}

		echo json_encode($data);


	}

	public function request_mark_as_vacant(){

		$prop_id = $this->input->get_post('prop_id');
		$vacant_from_date = $this->input->get_post('vacant_from_date');
		$vacant_to_date = $this->input->get_post('vacant_to_date');
		$clear_tenants = $this->input->get_post('clear_tenants');

		$aua_id = $this->session->aua_id;

		$email_data['vacant_from_date'] = $vacant_from_date;
		$email_data['vacant_to_date'] = $vacant_to_date;
		$start_date = date("Y-m-d H:i:s", strtotime(str_replace("/", "-", $vacant_from_date) . ' 00:00:00'));
		$due_date = date("Y-m-d", strtotime(str_replace("/", "-", $vacant_to_date)));
		
		$update_job_sql_str = "UPDATE `jobs` SET `status`='To Be Booked', `start_date`='{$start_date}', `due_date`='{$due_date}', `property_vacant`=1 WHERE `property_id`={$prop_id} && status NOT IN('Completed','Cancelled') ORDER BY `id` DESC LIMIT 1";
		$this->db->query($update_job_sql_str);

		// get property details
		$prop_sql_str = "
		SELECT 
			p.`property_id`, 
			p.`address_1` AS p_address_1, 
			p.`address_2` AS p_address_2, 
			p.`address_3` AS p_address_3, 
			p.`state` AS p_state,
			p.`postcode` AS p_postcode, 			
			
			a.`agency_id`, 
			a.`agency_name` 
		FROM `property` AS p
		LEFT JOIN `agency` AS a ON p.`agency_id` = a.`agency_id`
		WHERE p.`property_id` = {$prop_id}
		";
		
		$prop_sql = $this->db->query($prop_sql_str);				
		$email_data['prop_row'] = $prop_sql->row();	

		// logged agency user 
		$params = array(
			'sel_query' => '
				aua.`agency_user_account_id`,
				aua.`fname`,
				aua.`lname`				
			',
			'aua_id' => $aua_id,
			'agency_id' => $this->session->agency_id
		);

		$user_sql = $this->user_accounts_model->get_user_accounts($params);
		$email_data['user_row'] = $user_sql->row();
		
		// deactivate current tenants
		if( $clear_tenants == 1 ){
				
			$this->db->query("
			UPDATE `property_tenants`
			SET `active` = 0
			WHERE `property_id` = {$prop_id}
			AND `active` = 1
			");

		}		


		$this->email->to(make_email('info'));
		$this->email->subject('Property marked as vacant request');
		$body = $this->load->view('emails/request_mark_as_vacant', $email_data, TRUE);
		$this->email->message($body);
		$this->email->send();
		
	}

	private function email_tenant_updates($params){

		$email_data['property_address'] = $params['property_address'];
		$email_data['fname'] = $params['fname'];
		$email_data['orig_fname'] = $params['orig_fname'];
		$email_data['lname'] = $params['lname'];
		$email_data['orig_lname'] = $params['orig_lname'];
		$email_data['mobile'] = $params['mobile'];
		$email_data['orig_mobile'] = $params['orig_mobile'];
		$email_data['landline'] = $params['landline'];
		$email_data['orig_landline'] = $params['orig_landline'];
		$email_data['email'] = $params['email'];
		$email_data['orig_email'] = $params['orig_email'];

		$this->email->to(make_email('info'));
		$this->email->subject("Tenant Updated for {$params['property_address']}");
		$body = $this->load->view('emails/tenant_update', $email_data, TRUE);
		$this->email->message($body);
		$this->email->send();

	}

	public function check_active_jobs(){
		$prop_id = $this->input->get_post('prop_id');
		$jobs =  $this->properties_model->get_active_job_by_propIdV2($prop_id);
		$status = ($jobs == false ? 0 : 1);
		echo json_encode([
			'status' => $status,
		]);
	}

	/**
	 * Get pm by agency and include not assigned pm
	 */
	public function ajax_get_pm_by_agency_id(){
		$agency_id = $this->input->post('agency_id');
		$params = array(
			'sel_query' => '
				aua.`agency_user_account_id`,
				aua.`fname`,
				aua.`lname`				
			',
			'agency_id' => $agency_id,
			'active' => 1
		);
		$user_sql = $this->user_accounts_model->get_user_accounts($params);

		echo "<option value=''>Please select who will be managing this Property</option>";
		echo "<option value='0'>No PM Assigned</option>";
		foreach( $user_sql->result_array() as $row ){
			echo "<option value='{$row["agency_user_account_id"]}'>{$row['fname']} {$row['lname']}</option>";
		}
	}

	public function ajax_moved_property(){

		$this->load->model('agency_model');
		$agency_id = $this->input->post('agency_id');
		$pm_id = $this->input->post('pm_id');
		$property_id = $this->input->post('property_id');
		$old_agecy_id = $this->input->post('old_agecy_id');

		## get old agency details
		$agency_params = array(
			'sel_query' => 'a.agency_name',
			'agency_id' => $old_agecy_id
		);
		$old_agency_q = $this->agency_model->get_agency_data($agency_params)->row();
		$old_agency_name = $old_agency_q->agency_name;

		## get new agency details
		$new_agency_params = array(
			'sel_query' => 'a.agency_name',
			'agency_id' => $agency_id
		);
		$new_agency_q = $this->agency_model->get_agency_data($new_agency_params)->row();
		$new_agency_name = $new_agency_q->agency_name;

		##get property details
		$prop_params = array(
			'sel_query'=> 'p.property_id, p.address_1, p.address_2, p.address_3',
			'property_id' => $property_id
		);
		$prop_q = $this->properties_model->get_properties($prop_params)->row();
		$prop_name = "{$prop_q->address_1} {$prop_q->address_2} {$prop_q->address_3}";

		## get current user details
		$user = $this->user_accounts_model->get_user_account_via_id($this->session->aua_id);

		## get pm details
		$pm_detail = $this->user_accounts_model->get_user_account_via_id($pm_id);
		
		if( $property_id!="" ){

			##Update property agency
			$update_data = array(
				'agency_id' => $agency_id,
				'pm_id_new' => $pm_id,
				'propertyme_prop_id' => 'NULL',
				'palace_prop_id' => 'NULL'
			);
			$this->db->where('property_id', $property_id);
			$this->db->update('property', $update_data);

			##Update property API
			$update_data_api = array(
				'api_prop_id' => NULL,
				'active' => 0
			);
			$this->db->where('crm_prop_id', $property_id);
			$this->db->update('api_property_data', $update_data_api);

			#######Inser logs
			$agency_ids_arr = array($old_agecy_id, $agency_id);
			foreach( $agency_ids_arr as $agency_ids ){ ## loop old and new agency and send logs to both

				##Insert Log for Portal
				$log_details1 = "<strong>{$prop_name}</strong> was moved from <strong>{$old_agency_name}</strong> to <strong>{$new_agency_name}</strong> by <strong>{$user->fname} $user->lname</strong>";
				$log_params1 = array(
					'title' => 92, ##Property Moved
					'details' => $log_details1,
					'display_in_portal' => 1,
					'agency_id' => $agency_ids,
					'property_id' => $property_id,
					'created_by' => $this->session->aua_id
				);
				$this->jcclass->insert_log($log_params1);

			}

			##Inser Log for VPD
			$log_details2 = "Changed from <strong>{$old_agency_name}</strong> to <strong>{$new_agency_name}</strong> and <strong>{$pm_detail->fname} {$pm_detail->lname}</strong> assigned";
			$log_params2 = array(
				'title' => 92, ##Property Moved
				'details' => $log_details2,
				'display_in_vpd' => 1,
				'agency_id' => $agency_ids,
				'property_id' => $property_id,
				'created_by' => $this->session->aua_id
			);
			$this->jcclass->insert_log($log_params2);
			

		}

	}

	public function save_pm(){

		$property_id = $this->db->escape_str($this->input->get_post('property_id'));
		$pm = $this->db->escape_str($this->input->get_post('pm'));

		if( $property_id > 0 ){

			// get current PM 
			$current_pm_sql = $this->db->query("
			SELECT 
				aua.`agency_user_account_id` AS aud_id,
				aua.`fname`,
				aua.`lname`
			FROM property AS p
			INNER JOIN `agency_user_accounts` AS aua ON p.`pm_id_new` = aua.`agency_user_account_id`
			WHERE p.`property_id` = {$property_id}
			");
			$current_pm_row = $current_pm_sql->row();
			$current_pm = "{$current_pm_row->fname} {$current_pm_row->lname}";

			// update PM to
			$new_pm_sql = $this->db->query("
			SELECT 
				`agency_user_account_id` AS aud_id,
				`fname`,
				`lname`,
				`photo`
			FROM `agency_user_accounts` 
			WHERE `agency_user_account_id` = {$pm}
			");
			$new_pm_row = $new_pm_sql->row();
			$new_pm_id = $new_pm_row->aud_id;
			$new_pm_name = "{$new_pm_row->fname} {$new_pm_row->lname}";
			//$new_pm_pp = ( $new_pm_row->photo != '' )?"/uploads/user_accounts/photo/{$new_pm_row->photo}":'avatar-2-64.png';
			$new_pm_pp = $this->gherxlib->avatarv2($new_pm_row->photo);
			
			if( $current_pm_sql->num_rows() > 0 ){

				$log_details = "Property Manager updated from <b>{$current_pm}</b> to <b>{$new_pm_name}</b>";
				
			}else{
				
				$log_details = "Property Manager updated to <b>{$new_pm_name}</b>";

			}			

			// update
			$data = array(
				'pm_id_new' => $pm
			);			
			$this->db->where('property_id', $property_id);
			$this->db->update('property', $data);

			// insert log			
			$log_params = array(
				'title' => 65, // Property Update
				'details' => $log_details,
				'display_in_vpd' => 1,
				'display_in_portal' => 1,
				'agency_id' => $this->session->agency_id,
				'property_id' => $property_id,
				'created_by' => $this->session->aua_id
			);
			$this->jcclass->insert_log($log_params);

			// return in json
			$json_ret_arr = array(
				'new_pm_id' => $new_pm_id,
				'new_pm_name' => $new_pm_name,
				'new_pm_pp' => $new_pm_pp
			);				
			echo json_encode($json_ret_arr);

		}		

	}


}
