<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agency_ajax extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('states_def_model');
		$this->load->model('postcode_model');
		$this->load->model('logs_model');
		$this->load->model('agency_maintenance_model');
		$this->load->model('maintenance_model');
    }

    
	/**
	 * AJAX Request
	 * Update agency address
	 */
	public function update_agency_address()
	{
		$response = [
			'status' => false,
			'message'=> 'Update failed.'
		];

		if ($this->input->is_ajax_request()) {
			$agency_id 		= $this->session->agency_id;
			$address_1		= $this->input->post('address_1');
			$address_2		= $this->input->post('address_2');
			$address_3		= $this->input->post('address_3');
			$state			= $this->input->post('state');
			$postcode		= $this->input->post('postcode');
			$fullAdd		= $this->input->post('fullAdd');
			//original full address
			$og_fullAdd		= $this->input->post('og_fullAdd');

			
			//Get google coordinates
			$address = "{$address_1} {$address_2} {$address_3} {$state} {$postcode}, {$this->config->item('country')}";
			$coordinates = getGoogleMapCoordinates($address);

			//get region
			// $postcode_region = $this->postcode_model->as_array()->getRegionViaPostCode($postcode);
			$postcode_region = $this->postcode_model->as_array()->get(array('postcode' => $postcode));

			$agency_data = [
				'address_1'	=> $address_1,
				'address_2'	=> $address_2,
				'address_3'	=> $address_3,
				'state'		=> $state,
				'postcode'	=> $postcode,
				'lat'		=> $coordinates['lat'],
				'lng'		=> $coordinates['lng'],
				'postcode_region_id' => $postcode_region['sub_region_id']
			];

			if ($this->agency_model->update($agency_data, array('agency_id' => $agency_id))) {
				// create logs
				$title = 46; // Agency Update
				$details = "Agency address updated from {$og_fullAdd} to {$address}";
				
				$params = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $this->session->agency_id,
					'created_by' => $this->session->aua_id,
					'created_by_staff' => $this->session->aua_id,
					'created_date' => date('Y-m-d H:i:s')
				);
				$this->logs_model->insert_log($params);

				$response = [
					'status' => true,
					'message'=> 'Updated successfully.'
				];
			}
			
		}

		header('Content-Type: application/json');
        echo json_encode($response);
	}

	/**
	 * AJAX Request
	 * Update agency contact_phone
	 */
	// public function update_agency_phone()
	// {
	// 	$response = [
	// 		'status' => false,
	// 		'message'=> 'Update failed.'
	// 	];

	// 	if ($this->input->is_ajax_request()) {
	// 		$agency_id 		= $this->session->agency_id;
	// 		$contact_phone	= $this->input->post('phone');

	// 		// get the agency phone from db for logs use
	// 		$old_phone = $this->agency_model->fields('phone')->as_array()->get(array('agency_id' => $agency_id));
		
	// 		$agency_data = [
	// 			'phone'	=> $contact_phone
	// 		];

	// 		if ($this->agency_model->update($agency_data, array('agency_id' => $agency_id))) {
	// 			// create logs
	// 			$title = 14; // Agency Profile Update
	// 			$details = "Agency phone updated from {$old_phone['phone']} to {$contact_phone}";
				
	// 			$params = array(
	// 				'title' => $title,
	// 				'details' => $details,
	// 				'display_in_portal' => 1,
	// 				'display_in_vad' => 1,
	// 				'agency_id' => $this->session->agency_id,
	// 				'created_by' => $this->session->aua_id
	// 			);
	// 			$this->logs_model->insert_log($params);

	// 			$response = [
	// 				'status' => true,
	// 				'message'=> 'Updated successfully.'
	// 			];
	// 		}
			
	// 	}

	// 	header('Content-Type: application/json');
    //     echo json_encode($response);
	// }

	/**
	 * AJAX Request
	 * Update agency profile
	 * table: `agency`
	 * fields: `tot_properties, agency_emails, account_emails, phone, agency_hours, website, abn, display_bpay, send_en_to_agency, send_48_hr_key ,
	 * 	contact_first_name, contact_last_name, contact_phone, contact_email, accounts_name, accounts_phone, 
	 * 	tenant_details_contact_name, tenant_details_contact_phone`
	 */
	public function update_agency_profile()
	{
		$response = [
			'status' => false,
			'message'=> 'Update failed. No data to be change.'
		];

		if ($this->input->is_ajax_request()) {
			$agency_id 		= $this->session->agency_id;
			$logged_user_id = $this->session->aua_id;

			$phone			= $this->input->post('phone');
			$agency_hours	= $this->input->post('agency_hours');
			$website	    = $this->input->post('website');
			$abn	        = $this->input->post('abn');

			$tot_properties	= $this->input->post('tot_properties');
			$agency_emails	= $this->input->post('agency_emails');
			$account_emails	= $this->input->post('account_emails');

			// agency main contact details
			$contact_first_name	= $this->input->post('contact_first_name');
			$contact_last_name	= $this->input->post('contact_last_name');
			$contact_phone		= $this->input->post('contact_phone');
			$contact_email		= $this->input->post('contact_email');

			// Accounts Contact details
			$accounts_name		= $this->input->post('accounts_name');
			$accounts_phone		= $this->input->post('accounts_phone');

			// agency prefences
			$display_bpay		= $this->input->post('display_bpay');
			$send_en_to_agency	= $this->input->post('send_en_to_agency');
			$send_48_hr_key		= $this->input->post('send_48_hr_key');

			// Tenants Contact Details
			$tenant_details_contact_name		= $this->input->post('tenant_details_contact_name');
			$tenant_details_contact_phone		= $this->input->post('tenant_details_contact_phone');


			//fetch data from database
			$agencyData = $this->agency_model
				->fields('tot_properties, agency_emails, account_emails, phone, agency_hours, website, abn, display_bpay, send_en_to_agency, send_48_hr_key, contact_first_name, contact_last_name, contact_phone, contact_email, accounts_name, accounts_phone, tenant_details_contact_name, tenant_details_contact_phone')
				->as_array()
				->get(array('agency_id' =>  $agency_id));

			$postAgencyData = [];
			$postLogsData = [];

			//check if phone has value to be save to db
			if (isset($phone) && $agencyData['phone'] != $phone) {		
				$postData['phone'] = $phone;

                $old_phone = format_empty_string($agencyData['phone']);
                $new_phone = format_empty_string($phone);
				
				//logs data
				$title = 46; // Agency Update
				$details = "<strong>Agency phone</strong> updated from {$old_phone} to {$new_phone}";
			
				$params = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $agency_id,
					'created_by' => $logged_user_id,
					'created_by_staff' => $logged_user_id,
					'created_date' => date('Y-m-d H:i:s')
				);
			
				$postLogsData[] = $params;	
			} 

			//check if office hours has value to be save to db
			if (isset($agency_hours) && $agencyData['agency_hours'] != $agency_hours) {		
				$postData['agency_hours'] = $agency_hours;

                $old_agency_hours = format_empty_string($agencyData['agency_hours']);
                $new_agency_hours = format_empty_string($agency_hours);
				
				//logs data
				$title = 46; // Agency Update
				$details = "<strong>Agency office hours</strong> updated from {$old_agency_hours} to {$new_agency_hours}";
			
				$params = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $agency_id,
					'created_by' => $logged_user_id,
					'created_by_staff' => $logged_user_id,
					'created_date' => date('Y-m-d H:i:s')
				);
			
				$postLogsData[] = $params;	
			} 

            //check if agency website has value to be save to db
			if (isset($website) && $agencyData['website'] != $website) {		
				$postData['website'] = $website;

                $old_website = format_empty_string($agencyData['website']);
                $new_website = format_empty_string($website);
				
				//logs data
				$title = 46; // Agency Update
				$details = "<strong>Website</strong> updated from {$old_website} to {$new_website}";
			
				$params = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $agency_id,
					'created_by' => $logged_user_id,
					'created_by_staff' => $logged_user_id,
					'created_date' => date('Y-m-d H:i:s')
				);
			
				$postLogsData[] = $params;	
			} 

            //check if agency website has value to be save to db
			if (isset($abn) && $agencyData['abn'] != $abn) {		
				$postData['abn'] = $abn;

                $old_abn = format_empty_string($agencyData['abn']);
                $new_abn = format_empty_string($abn);
				
				//logs data
				$title = 46; // Agency Update
				$details = "<strong>ABN Name</strong> updated from {$old_abn} to {$new_abn}";
			
				$params = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $agency_id,
					'created_by' => $logged_user_id,
					'created_by_staff' => $logged_user_id,
					'created_date' => date('Y-m-d H:i:s')
				);
			
				$postLogsData[] = $params;	
			} 

			//check if tot_properties has value to be saved to db
			if (isset($tot_properties) && $agencyData['tot_properties'] != $tot_properties) {		
				$postData['tot_properties'] = $tot_properties;
				$postData['tot_prop_timestamp'] = date('Y-m-d H:i:s');

                $old_tot_propterties = format_empty_val($agencyData['tot_properties']);
                $new_tot_propterties = format_empty_val($tot_properties);
				
				//logs data
				$title = 46; // Agency Update
				$details = "<strong>Total Properties Managed</strong> updated from {$old_tot_propterties} to {$new_tot_propterties}";
			
				$params = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $agency_id,
					'created_by' => $logged_user_id,
					'created_by_staff' => $logged_user_id,
					'created_date' => date('Y-m-d H:i:s')
				);

				$postLogsData[] = $params;
				
			} 

			// Agency Emails:
			if(isset($agency_emails) && $agencyData['agency_emails'] != $agency_emails ){
				$postData['agency_emails'] = $agency_emails; 

				// agency emails
				$agency_emails_orig_exp = explode("\n",trim($agencyData['agency_emails']));
				$agency_emails_orig = implode(", ",$agency_emails_orig_exp);

				// agency emails post
				$agency_emails_post_exp = explode("\n",trim($agency_emails));
				$agency_emails_post = implode(", ",$agency_emails_post_exp);
			
		
				//Set validation rules for each email
				foreach($agency_emails_post_exp as $key => $email){
					if(!empty($email)){
						if(!filter_var(trim($email), FILTER_VALIDATE_EMAIL)){
							$message = "Invalid email address for $email.";
							$response = [
								'message'=> $message
							];
							header('Content-Type: application/json');
        					echo json_encode($response);
							exit();
						}
					}
				}

				// compare email difference via array
				$email_diff = array_diff($agency_emails_post_exp, $agency_emails_orig_exp);

				if( count($email_diff) > 0 ){

					$title = 46; // Agency Update
					$details = "<strong>Agency Emails</strong> updated from {$agency_emails_orig} to {$agency_emails_post}";

					$params = array(
						'title' => $title,
						'details' => $details,
						'display_in_portal' => 1,
						'display_in_vad' => 1,
						'agency_id' => $agency_id,
						'created_by' => $logged_user_id,
						'created_by_staff' => $logged_user_id,
						'created_date' => date('Y-m-d H:i:s')
					);

					$postLogsData[] = $params;

				}
		
			}

			// Account Emails:
			if(isset($account_emails) && $agencyData['account_emails'] != $account_emails ){
				$postData['account_emails'] = $account_emails; 

				// account emails
				$account_emails_orig_exp = explode("\n",trim($agencyData['account_emails']));
				$account_emails_orig = implode(", ",$account_emails_orig_exp);

				// account emails post
				$account_emails_post_exp = explode("\n",trim($account_emails));
				$account_emails_post = implode(", ",$account_emails_post_exp);
			
		
				//Set validation rules for each email
				foreach($account_emails_post_exp as $key => $email){
					if(!empty($email)){
						if(!filter_var(trim($email), FILTER_VALIDATE_EMAIL)){
							$message = "Invalid email address for $email.";
							$response = [
								'message'=> $message
							];
							header('Content-Type: application/json');
        					echo json_encode($response);
							exit();
						}
					}
				}

				// compare email difference via array
				$email_diff = array_diff($account_emails_post_exp, $account_emails_orig_exp);

				if( count($email_diff) > 0 ){

					$title = 46; // Agency Update
					$details = "<strong>Account Emails</strong> updated from {$account_emails_orig} to {$account_emails_post}";

					$params = array(
						'title' => $title,
						'details' => $details,
						'display_in_portal' => 1,
						'display_in_vad' => 1,
						'agency_id' => $agency_id,
						'created_by' => $logged_user_id,
						'created_by_staff' => $logged_user_id,
						'created_date' => date('Y-m-d H:i:s')
					);

					$postLogsData[] = $params;

				}
		
			}

			//check if agency preference display_bpay has value to be save to db
			if (isset($display_bpay) && $agencyData['display_bpay'] != $display_bpay) {		
				$postData['display_bpay'] = $display_bpay;

                $old_display_bpay = format_bool_val($agencyData['display_bpay']);
                $new_display_bpay = format_bool_val($display_bpay);
				
				//logs data
				$title = 46; // Agency Update
				$details = "<strong>Preferences Display BPAY on Invoices</strong> updated from {$old_display_bpay} to {$new_display_bpay}";
			
				$params = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $agency_id,
					'created_by' => $logged_user_id,
					'created_by_staff' => $logged_user_id,
					'created_date' => date('Y-m-d H:i:s')
				);
			
				$postLogsData[] = $params;	
			} 

			//check if agency preference send_en_to_agency has value to be save to db
			if (isset($send_en_to_agency) && $agencyData['send_en_to_agency'] != $send_en_to_agency) {		
				$postData['send_en_to_agency'] = $send_en_to_agency;

                $old_send_en_to_agency = format_bool_val($agencyData['send_en_to_agency']);
                $new_send_en_to_agency = format_bool_val($send_en_to_agency);
				
				//logs data
				$title = 46; // Agency Update
				$details = "<strong>Preferences Send copy of EN to Agency</strong> updated from {$old_send_en_to_agency} to {$new_send_en_to_agency}";
			
				$params = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $agency_id,
					'created_by' => $logged_user_id,
					'created_by_staff' => $logged_user_id,
					'created_date' => date('Y-m-d H:i:s')
				);
			
				$postLogsData[] = $params;	
			} 

			//check if agency preference send_48_hr_key has value to be save to db
			if (isset($send_48_hr_key) && $agencyData['send_48_hr_key'] != $send_48_hr_key) {		
				$postData['send_48_hr_key'] = $send_48_hr_key;

                $old_send_48_hr_key = format_bool_val($agencyData['send_48_hr_key']);
                $new_send_48_hr_key = format_bool_val($send_48_hr_key);
				
				//logs data
				$title = 46; // Agency Update
				$details = "<strong>Preferences Send 48 hour key email</strong> updated from {$old_send_48_hr_key} to {$new_send_48_hr_key}";
			
				$params = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $agency_id,
					'created_by' => $logged_user_id,
					'created_by_staff' => $logged_user_id,
					'created_date' => date('Y-m-d H:i:s')
				);
			
				$postLogsData[] = $params;	
			} 

			//check if agency main contact_first_name has value to be save to db
			if (isset($contact_first_name) && $agencyData['contact_first_name'] != $contact_first_name) {		
				$postData['contact_first_name'] = $contact_first_name;

                $old_contact_first_name = format_empty_string($agencyData['contact_first_name']);
                $new_contact_first_name = format_empty_string($contact_first_name);
				
				//logs data
				$title = 46; // Agency Update
				$details = "<strong>Acounts Contact First Name</strong> updated from {$old_contact_first_name} to {$new_contact_first_name}";
			
				$params = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $agency_id,
					'created_by' => $logged_user_id,
					'created_by_staff' => $logged_user_id,
					'created_date' => date('Y-m-d H:i:s')
				);
			
				$postLogsData[] = $params;	
			} 

			//check if agency main contact_first_name has value to be save to db
			if (isset($contact_last_name) && $agencyData['contact_last_name'] != $contact_last_name) {		
				$postData['contact_last_name'] = $contact_last_name;

                $old_contact_last_name = format_empty_string($agencyData['contact_last_name']);
                $new_contact_last_name = format_empty_string($contact_last_name);
				
				//logs data
				$title = 46; // Agency Update
				$details = "<strong>Acounts Contact Last Name</strong> updated from {$old_contact_last_name} to {$new_contact_last_name}";
			
				$params = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $agency_id,
					'created_by' => $logged_user_id,
					'created_by_staff' => $logged_user_id,
					'created_date' => date('Y-m-d H:i:s')
				);
			
				$postLogsData[] = $params;	
			} 

			//check if agency main contact phone has value to be save to db
			if (isset($contact_phone) && $agencyData['contact_phone'] != $contact_phone) {		
				$postData['contact_phone'] = $contact_phone;

                $old_contact_phone = format_empty_string($agencyData['contact_phone']);
                $new_contact_phone = format_empty_string($contact_phone);
				
				//logs data
				$title = 46; // Agency Update
				$details = "<strong>Agency Contact Phone</strong> updated from {$old_contact_phone} to {$new_contact_phone}";
			
				$params = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $agency_id,
					'created_by' => $logged_user_id,
					'created_by_staff' => $logged_user_id,
					'created_date' => date('Y-m-d H:i:s')
				);
			
				$postLogsData[] = $params;	
			} 
		
			//check if agency main contact email has value to be save to db
			if (isset($contact_email) && $agencyData['contact_email'] != $contact_email) {		
				$postData['contact_email'] = $contact_email;

                $old_contact_email = format_empty_string($agencyData['contact_email']);
                $new_contact_email = format_empty_string($contact_email);
				
				//logs data
				$title = 46; // Agency Update
				$details = "<strong>Agency Contact Email</strong> updated from {$old_contact_email} to {$new_contact_email}";
			
				$params = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $agency_id,
					'created_by' => $logged_user_id,
					'created_by_staff' => $logged_user_id,
					'created_date' => date('Y-m-d H:i:s')
				);
			
				$postLogsData[] = $params;	
			} 
			
			//check if accounts contact name has value to be save to db
			if (isset($accounts_name) && $agencyData['accounts_name'] != $accounts_name) {		
				$postData['accounts_name'] = $accounts_name;

                $old_accounts_name = format_empty_string($agencyData['accounts_name']);
                $new_accounts_name = format_empty_string($accounts_name);
				
				//logs data
				$title = 46; // Agency Update
				$details = "<strong>Accounts Contact Name</strong> updated from {$old_accounts_name} to {$new_accounts_name}";
			
				$params = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $agency_id,
					'created_by' => $logged_user_id,
					'created_by_staff' => $logged_user_id,
					'created_date' => date('Y-m-d H:i:s')
				);
			
				$postLogsData[] = $params;	
			} 

			//check if accounts contact phone has value to be save to db
			if (isset($accounts_phone) && $agencyData['accounts_phone'] != $accounts_phone) {		
				$postData['accounts_phone'] = $accounts_phone;

                $old_accounts_phone = format_empty_string($agencyData['accounts_phone']);
                $new_accounts_phone = format_empty_string($accounts_phone);
				
				//logs data
				$title = 46; // Agency Update
				$details = "<strong>Accounts Contact Phone</strong> updated from {$old_accounts_phone} to {$new_accounts_phone}";
			
				$params = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $agency_id,
					'created_by' => $logged_user_id,
					'created_by_staff' => $logged_user_id,
					'created_date' => date('Y-m-d H:i:s')
				);
			
				$postLogsData[] = $params;	
			}

			//check if Tenants contact name has value to be save to db
			if (isset($tenant_details_contact_name) && $agencyData['tenant_details_contact_name'] != $tenant_details_contact_name) {		
				$postData['tenant_details_contact_name'] = $tenant_details_contact_name;

                $old_tenant_details_contact_name = format_empty_string($agencyData['tenant_details_contact_name']);
                $new_tenant_details_contact_name = format_empty_string($tenant_details_contact_name);
				
				//logs data
				$title = 46; // Agency Update
				$details = "<strong>Tenant Details Contact Name</strong> updated from {$old_tenant_details_contact_name} to {$new_tenant_details_contact_name}";
			
				$params = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $agency_id,
					'created_by' => $logged_user_id,
					'created_by_staff' => $logged_user_id,
					'created_date' => date('Y-m-d H:i:s')
				);
			
				$postLogsData[] = $params;	
			} 

			//check if Tenants contact phone has value to be save to db
			if (isset($tenant_details_contact_phone) && $agencyData['tenant_details_contact_phone'] != $tenant_details_contact_phone) {		
				$postData['tenant_details_contact_phone'] = $tenant_details_contact_phone;

                $old_tenant_details_contact_phone = format_empty_string($agencyData['tenant_details_contact_phone']);
                $new_tenant_details_contact_phone = format_empty_string($tenant_details_contact_phone);
				
				//logs data
				$title = 46; // Agency Update
				$details = "<strong>Tenant Details Contact Phone</strong> updated from {$old_tenant_details_contact_phone} to {$new_tenant_details_contact_phone}";
			
				$params = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $agency_id,
					'created_by' => $logged_user_id,
					'created_by_staff' => $logged_user_id,
					'created_date' => date('Y-m-d H:i:s')
				);
			
				$postLogsData[] = $params;	
			} 

		
			$postData = $this->security->xss_clean($postData);
			$postLogsData = $this->security->xss_clean($postLogsData);
			if ($postData) {
				//update agency
				$this->agency_model->update($postData, $agency_id);

				// batch insert logs
				$this->logs_model->insert($postLogsData);

				$response = [
					'status' => true,
					'message'=> 'Updated successfully.'
				];
			}
			
		}

		header('Content-Type: application/json');
        echo json_encode($response);
	}

	/**
	 * AJAX Request
	 * Update agency maintenance
	 * table 'agency_maintenance
	 * fields `maintenance_id, price, surcharge, display_surcharge, surcharge_msg, updated_date
	 */
	public function update_agency_maintenance()
	{
		$response = [
			'status' => false,
			'message'=> 'Update failed. No data to be change.'
		];

		if ($this->input->is_ajax_request()) {
			$agency_id 				= $this->session->agency_id;
			$agency_maintenance_id	= $this->input->post('agency_maintenance_id');
			$maintenance_id			= $this->input->post('maintenance_provider');
			$surcharge				= $this->input->post('surcharge');
			$display_surcharge		= $this->input->post('display_surcharge');
			$price					= $this->input->post('surcharge_price');
			$surcharge_msg			= $this->input->post('surcharge_msg');

			if(empty($maintenance_id)){
				$response = [
					'status' => false,
					'message'=> 'Update failed. Maintenance Provider Cannot be empty.'
				];
				header('Content-Type: application/json');
        		echo json_encode($response);
				exit;
			}
			
			$agency_maintenance_data = [
				'agency_id'			=> $agency_id,
				'maintenance_id'	=> $maintenance_id,
				'price'				=> $price,
				'surcharge'			=> $surcharge,
				'display_surcharge'	=> $display_surcharge,
				'surcharge_msg'		=> $surcharge_msg,
				'updated_date'		=> date("Y-m-d")
			];

			$postLogsData = [];
			
			// get current data from db
			$agency_maintenance_db_data = $this->agency_maintenance_model->as_array()->get(array('agency_maintenance_id' => $agency_maintenance_id,'agency_id' => $agency_id));

			if(!empty($agency_maintenance_id)){ #if maintenance_id is not empty update the data
				$this->agency_maintenance_model
					->update($agency_maintenance_data, array('agency_maintenance_id' => $agency_maintenance_id,'agency_id' => $agency_id));

				$response = [
					'status' => true,
					'message'=> 'Updated successfully.'
				];
			} else { # else save the data to db
				$this->agency_maintenance_model->insert($agency_maintenance_data);
				$response = [
					'status' => true,
					'message'=> 'Maintenance Program added successfully.'
				];
			}
				

			// create logs
			$title = 79; // Maintenance Program

			// check if current agency maintenance provider is not equal to new input maintenance provider, if true insert logs else escape
			if($agency_maintenance_db_data['maintenance_id'] != $agency_maintenance_data['maintenance_id']){
				// get maintenance name base on maintenance_id for current maintenance provider
				$maintenance_provider_name_old = $this->maintenance_model->as_array()
					->fields('name')
					->get(array('maintenance_id' => $agency_maintenance_db_data['maintenance_id']));
				$maintenance_provider_name_old = format_empty_string($maintenance_provider_name_old['name']);

				// get maintenance name base on maintenance_id for new maintenance provider
				$maintenance_provider_name_new = $this->maintenance_model->as_array()
					->fields('name')
					->get(array('maintenance_id' => $agency_maintenance_data['maintenance_id']));
				$maintenance_provider_name_new = format_empty_string($maintenance_provider_name_new['name']);

				$details = "<strong>Maintenance Provider</strong> updated from {$maintenance_provider_name_old} to {$maintenance_provider_name_new}";
				$params = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $this->session->agency_id,
					'created_by_staff' => $this->session->aua_id,
					'created_by' => $this->session->aua_id,
					'created_date' => date('Y-m-d H:i:s')
				);

				$postLogsData[] = $params;
			}
		
			// check if current agency maintenance price is not equal to new input maintenance price, if true insert logs else escape
			if($agency_maintenance_db_data['price'] != $agency_maintenance_data['price']){
				$old_price = format_empty_val($agency_maintenance_db_data['price']);
				$new_price = format_empty_val($agency_maintenance_data['price']);

				$details = "<strong>Surcharge</strong> updated from {$old_price} to {$new_price}";
				$priceLogs = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $this->session->agency_id,
					'created_by_staff' => $this->session->aua_id,
					'created_by' => $this->session->aua_id,
					'created_date' => date('Y-m-d H:i:s')
				);

				$postLogsData[] = $priceLogs;
			}

			// check if current agency maintenance surcharge is not equal to new input maintenance surcharge, if true insert logs else escape
			if($agency_maintenance_db_data['surcharge'] != $agency_maintenance_data['surcharge']){
				$old_surcharge = format_bool_val($agency_maintenance_db_data['surcharge']);
				$new_surcharge = format_bool_val($agency_maintenance_data['surcharge']);

				$details = "<strong>Apply Surcharge to all Invoices</strong> updated from {$old_surcharge} to {$new_surcharge}";

				$params = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $this->session->agency_id,
					'created_by_staff' => $this->session->aua_id,
					'created_by' => $this->session->aua_id,
					'created_date' => date('Y-m-d H:i:s')
				);

				$postLogsData[] = $params;
			}

			// check if current agency maintenance display_surcharge is not equal to new input maintenance display_surcharge, if true insert logs else escape
			if($agency_maintenance_db_data['display_surcharge'] != $agency_maintenance_data['display_surcharge']){
				$old_display_surcharge = format_bool_val($agency_maintenance_db_data['display_surcharge']);
				$new_display_surcharge = format_bool_val($agency_maintenance_data['display_surcharge']);

				$details = "<strong>Display Message on all Invoices</strong> updated from {$old_display_surcharge} to {$new_display_surcharge}";

				$params = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $this->session->agency_id,
					'created_by_staff' => $this->session->aua_id,
					'created_by' => $this->session->aua_id,
					'created_date' => date('Y-m-d H:i:s')
				);

				$postLogsData[] = $params;
			}

			// check if current agency maintenance surcharge message is not equal to new input maintenance surcharge message, if true insert logs else escape
			if($agency_maintenance_db_data['surcharge_msg'] != $agency_maintenance_data['surcharge_msg']){
				$old_surcharge_msg = format_empty_string($agency_maintenance_db_data['surcharge_msg']);
				$new_surcharge_msg = format_empty_string($agency_maintenance_data['surcharge_msg']);

				$details = "<strong>Invoice Message</strong> updated from {$old_surcharge_msg} to {$new_surcharge_msg}";
				$params = array(
					'title' => $title,
					'details' => $details,
					'display_in_portal' => 1,
					'display_in_vad' => 1,
					'agency_id' => $this->session->agency_id,
					'created_by_staff' => $this->session->aua_id,
					'created_by' => $this->session->aua_id,
					'created_date' => date('Y-m-d H:i:s')
				);

				$postLogsData[] = $params;
			}
			
			$this->logs_model->insert($postLogsData);
		}

		header('Content-Type: application/json');
        echo json_encode($response);
	}

}