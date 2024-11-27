<?php
class System_model extends CI_Model{

    public function __construct(){
        $this->load->database();
        $this->load->model('sms_api_model');
		$this->load->model('email_model');
        $this->load->library('email');
    }

    public function get_agency_portal_maintenance_mode(){

        if( $this->config->item('country') > 0 ){

            // get agency maintenance mode
            $agency_portal_mm_sql_str = "
            SELECT `agency_portal_mm`
            FROM `crm_settings` 
            WHERE `country_id` = {$this->config->item('country')}
            ";
            $agency_portal_mm_sql = $this->db->query($agency_portal_mm_sql_str);
            $agency_portal_mm_row = $agency_portal_mm_sql->row();        
            return $agency_portal_mm_row->agency_portal_mm;

        }        		

    }

    // check if property has money owing and needs to verify paid
    public function check_verify_paid($property_id){

        $job_sql_str = "
        SELECT COUNT(j.`id`) AS jcount
        FROM `jobs` AS j
        WHERE j.`property_id` = {$property_id}
        AND j.`status` = 'Completed'
        AND j.`invoice_balance` > 0
        AND (
            j.`date` >= '{$this->config->item('accounts_financial_year')}'  OR
            j.`unpaid` = 1	
        )
        ";

        $job_sql = $this->db->query($job_sql_str);
        $job_count = $job_sql->row()->jcount;

        if( $job_count > 0 ){
            return true;
        }else{
            return false;
        }

    }

    // Hume housinng agency AU
    public function is_hume_housing_agency(){

        // Hume housinng agency AU
        $hume_housing_agency = 1598;

        // Adams for testing
        //$hume_housing_agency = 1448; 

        if( $this->session->agency_id == $hume_housing_agency && $this->session->country_id == 1 ){
            return true;
        }else{
            return false;
        }

    }

    // find 240v alarms
    function getAll240vAlarm($job_id) {        

        $alarm_sql = $this->db->query("
			SELECT COUNT(al.`alarm_id`) AS al_count
            FROM `alarm` AS al
            LEFT JOIN `alarm_pwr` AS al_pwr ON al.`alarm_power_id` = al_pwr.`alarm_pwr_id`
			WHERE al.`job_id` = {$job_id}			
            AND al.`ts_discarded` = 0
            AND al_pwr.`is_240v` = 1
        ");
        $alarm_count = $alarm_sql->row()->al_count;

        if ( $alarm_count > 0) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * find an expired 240v alarm
     * @params job_id
     * return boolean
     */
    function findExpired240vAlarm($job_id, $year=null) {

        $year2 = ( $year != '' ) ? $year : date("Y");

        $alarm_sql = $this->db->query("
			SELECT COUNT(al.`alarm_id`) AS al_count
            FROM `alarm` AS al
            LEFT JOIN `alarm_pwr` AS al_pwr ON al.`alarm_power_id` = al_pwr.`alarm_pwr_id`
			WHERE al.`job_id` = {$job_id}
			AND al.`expiry` <= '{$year2}'
            AND al.`ts_discarded` = 0
            AND al_pwr.`is_240v` = 1
        ");
        $alarm_count = $alarm_sql->row()->al_count;

        if ( $alarm_count > 0) {
            return true;
        } else {
            return false;
        }

    }
    
    public function mark_is_eo($job_id, $year=null) {  
        
        if( $job_id > 0 ){

            // copied from findExpired240vAlarm
            $year2 = ( $year != '' ) ? $year : date("Y");
            $alarm_sql = $this->db->query("
                SELECT COUNT(al.`alarm_id`) AS al_count
                FROM `alarm` AS al
                LEFT JOIN `alarm_pwr` AS al_pwr ON al.`alarm_power_id` = al_pwr.`alarm_pwr_id`
                WHERE al.`job_id` = {$job_id}
                AND al.`expiry` <= '{$year2}'
                AND al.`ts_discarded` = 0
                AND al_pwr.`is_240v` = 1
            ");
            $alarm_count = $alarm_sql->row()->al_count;

            // FR - 240v check, find 240v alarms even if not expired
            $alarm_sql2 = $this->db->query("
                SELECT COUNT(al.`alarm_id`) AS al_count
                FROM `alarm` AS al
                LEFT JOIN `jobs` AS j ON al.`job_id` = j.`id`
                LEFT JOIN `alarm_pwr` AS al_pwr ON al.`alarm_power_id` = al_pwr.`alarm_pwr_id`
                WHERE al.`job_id` = {$job_id}	
                AND j.`job_type` = 'Fix or Replace'		
                AND al.`ts_discarded` = 0
                AND al_pwr.`is_240v` = 1
            ");
            $alarm_count2 = $alarm_sql2->row()->al_count;

            if ( $alarm_count > 0 || $alarm_count2 > 0 ) {
            
                // set this job as EO = for electrician only
                $this->db->query("
                UPDATE `jobs`
                SET `is_eo` = 1
                WHERE `id` = {$job_id}
                ");
                
            } 

        }        

    }

    public function get_agency_price_variation($params){

        $service_type = $params['service_type'];
        $agency_id = $params['agency_id'];
    
        $today = date('Y-m-d');
    
        // get dynamic price
        $dynamic_price = 0;
        $ret_arr = [];
    
        $price_variation_total = 0;
        $price_variation_total_str = null;
        $variation = 0;
    
        // get price increase excluded agency
        $piea_sql = $this->db->query("
        SELECT *
        FROM `price_increase_excluded_agency`
        WHERE `agency_id` = {$agency_id}
        AND (
            `exclude_until` >= '{$today}' OR
            `exclude_until` IS NULL
        )
        ");        
    
        // get agency specific service price
        $assp_sql = $this->db->query("
        SELECT *
        FROM `agency_specific_service_price`
        WHERE `service_type` = {$service_type}
        AND `agency_id` = {$agency_id}
        ");
        
        // get agency default service price
        $adsp_sql = $this->db->query("
        SELECT *
        FROM `agency_default_service_price`
        WHERE `service_type` = {$service_type}
        ");  
    
        // get agency price variation
        $apv_sql = $this->db->query("
        SELECT 
            apv.`type` AS apv_type,
            apv.`amount`,
            apv.`scope`,
    
            ajt.`type` AS ajt_type,
            ajt.`short_name`,
            dv.display_on
        FROM `agency_price_variation` AS apv
        LEFT JOIN `alarm_job_type` AS ajt ON ( apv.`scope` = ajt.`id` AND apv.`scope` >= 2 )
        LEFT JOIN `display_variation` AS dv ON apv.`id` = dv.`variation_id`
        WHERE apv.`agency_id` = {$agency_id}    
        AND (
            apv.`scope` = 0 OR
            apv.`scope` = {$service_type}
        )
        AND (
            apv.expiry >= '{$today}'
            OR apv.expiry IS NULL
        )
        AND apv.`active` = 1
        GROUP BY apv.id
        ");
            
        foreach( $apv_sql->result() as $apv_row ){  
            
            $service_type_str = ( $apv_row->scope >= 2 )?"{$apv_row->short_name} Service ":null;
                        
            if( $apv_row->apv_type == 1 ){ // discount
                $price_variation_total-=$apv_row->amount;
                if ($apv_row->display_on == 3 || $apv_row->display_on == 4 || $apv_row->display_on == 5 || $apv_row->display_on == 7) {
                    $price_variation_total_str .= " - \$".number_format($apv_row->amount,2)." {$service_type_str}Discount";
                } else {
                    $variation-=$apv_row->amount;
                }
            }else{ // surcharge
                $price_variation_total+=$apv_row->amount;
                if ($apv_row->display_on == 3 || $apv_row->display_on == 4 || $apv_row->display_on == 5 || $apv_row->display_on == 7) {
                    $price_variation_total_str .= " + \$".number_format($apv_row->amount,2)." {$service_type_str}Surcharge";
                } else {
                    $variation+=$apv_row->amount;
                }
            }            
    
        }
                            
        if( $piea_sql->num_rows() > 0 ){ // price increase excluded agency IF block
            
            // get agency services
            $agen_serv_sql = $this->db->query("
            SELECT *
            FROM `agency_services`
            WHERE `service_id` = {$service_type}
            AND `agency_id` = {$agency_id}
            ");
            $agen_serv_row = $agen_serv_sql->row();                
            
            $dynamic_price = $agen_serv_row->price;
            $dynamic_price_total = $dynamic_price; // no added price variation
    
        }else if( $assp_sql->num_rows() > 0 ){ // agency specific service price IF block
    
            $assp_row = $assp_sql->row();
            $dynamic_price = $assp_row->price;
            $dynamic_price_total = $dynamic_price+$price_variation_total; // add variations
            
        }else if( $adsp_sql->num_rows() > 0 ){ // agency default service price IF block
    
            $adsp_row = $adsp_sql->row();    
            $dynamic_price = $adsp_row->price;
            $dynamic_price_total = $dynamic_price+$price_variation_total; // add variations
            
        }
    
        $final_total_str = ( $price_variation_total != 0 )?' = $'.number_format($dynamic_price_total,2):null;
    
        $dynamic_price_text = '$'.number_format($dynamic_price,2);
        $price_text = '$'.number_format($dynamic_price_total,2);
        $price_breakdown_text = '$'.number_format($dynamic_price+$variation,2).$price_variation_total_str.$final_total_str;
    
        return $ret_arr = array(
            'dynamic_price' => $dynamic_price,
            'price_variation_total' => $price_variation_total,
            'dynamic_price_total' => $dynamic_price_total,
            'dynamic_price_text' => $dynamic_price_text,
            'price_text' => $price_text,
            'price_breakdown_text' => $price_breakdown_text
        );
    
    }

    public function free_alarms($alarm_price,$agency_id){

        $today = date('Y-m-d');

        // get price increase excluded agency
        $piea_sql = $this->db->query("
        SELECT *
        FROM `price_increase_excluded_agency`
        WHERE `agency_id` = {$agency_id}
        AND (
            `exclude_until` >= '{$today}' OR
            `exclude_until` IS NULL
        )
        ");    
        
        if( $piea_sql->num_rows() > 0 ){ // agency is excluded to price increase
            return $alarm_price;
        }else{ // price increase, alarm price is 0
            return 0;
        }

    }

    public function get_property_price_variation($params){

        $service_type = $params['service_type'];
        $property_id = $params['property_id'];
    
        $today = date('Y-m-d');
    
        // get dynamic price
        $dynamic_price = 0;
        $ret_arr = [];
    
        $price_variation_total = 0;
        $price_variation_total_str = null;
        $variation = 0;
    
        // get property data
        $prop_sql = $this->db->query("
        SELECT 
            `agency_id`,
            `holiday_rental`,
            `state`
        FROM `property`
        WHERE `property_id` = {$property_id}
        ");
        $prop_row = $prop_sql->row();
        $agency_id = $prop_row->agency_id;              
    
        // get price increase excluded agency
        $piea_sql = $this->db->query("
        SELECT *
        FROM `price_increase_excluded_agency`
        WHERE `agency_id` = {$agency_id}
        AND (
            `exclude_until` >= '{$today}' OR
            `exclude_until` IS NULL
        )
        ");     
        
        // get short term service price
        $stsp_sql = $this->db->query("
        SELECT *
        FROM `short_term_service_price`
        WHERE `service_type` = {$service_type}
        AND `state` = '{$prop_row->state}'
        ");       
                            
        if( $piea_sql->num_rows() > 0 ){ // agency is price increase excluded

            // get property services
            $ps_sql = $this->db->query("
            SELECT *
            FROM `property_services`
            WHERE `alarm_job_type_id` = {$service_type}
            AND `service` = 1
            AND `property_id` = {$property_id}
            ");
                          
            if( $ps_sql->num_rows() > 0 ){

                $ps_row = $ps_sql->row();
                $dynamic_price = $ps_row->price;                

            }else{

                // get agency services
                $agen_serv_sql = $this->db->query("
                SELECT *
                FROM `agency_services`
                WHERE `service_id` = {$service_type}
                AND `agency_id` = {$agency_id}
                ");

                $agen_serv_row = $agen_serv_sql->row();                                
                $dynamic_price = $agen_serv_row->price;                
                
            }

            $dynamic_price_total = $dynamic_price; // no added price variation            
    
        }else if( $prop_row->holiday_rental == 1 && $stsp_sql->num_rows() > 0 ){ // short term service price
            
            $stsp_row = $stsp_sql->row();
            $dynamic_price = $stsp_row->price;
            $dynamic_price_total = $dynamic_price; // no added price variation
            
        }else{ // agency and property variation    
            
            // get agency specific service price
            $assp_sql = $this->db->query("
            SELECT *
            FROM `agency_specific_service_price`
            WHERE `service_type` = {$service_type}
            AND `agency_id` = {$agency_id}
            "); 
            $assp_row = $assp_sql->row(); 
    
            // get agency default service price
            $adsp_sql = $this->db->query("
                SELECT *
                    FROM `agency_default_service_price`
                    WHERE `service_type` = {$service_type}
                "); 
            $adsp_row = $adsp_sql->row();
    
            if ($assp_sql->num_rows() > 0) {
                $dynamic_price = $assp_row->price;
            } else {
                $dynamic_price = $adsp_row->price;
            }
    
            // get agency price variation
            $apv_sql = $this->db->query("
            SELECT 
                apv.`type` AS apv_type,
                apv.`amount`,
                apv.`scope`,
        
                ajt.`type` AS ajt_type,
                ajt.`short_name`,
                dv.display_on
            FROM `agency_price_variation` AS apv
            LEFT JOIN `alarm_job_type` AS ajt ON ( apv.`scope` = ajt.`id` AND apv.`scope` >= 2 )
            LEFT JOIN `display_variation` AS dv ON ( apv.`id` = dv.`variation_id` AND dv.`type` = 1 )
            WHERE apv.`agency_id` = {$agency_id}    
            AND (
                apv.`scope` = 0 OR
                apv.`scope` = {$service_type}
            )
            AND (
                apv.expiry >= '{$today}'
                OR apv.expiry IS NULL
            )
            AND apv.`active` = 1
            ");                  

            foreach( $apv_sql->result() as $apv_row ){  

                $service_type_str = ( $apv_row->scope >= 2 )?"{$apv_row->short_name} Service ":null;
                            
                if( $apv_row->apv_type == 1 ){ // discount
                    $price_variation_total-=$apv_row->amount;
                    if ($apv_row->display_on == 3 || $apv_row->display_on == 4 || $apv_row->display_on == 5 || $apv_row->display_on == 7) {
                        $price_variation_total_str .= " - \$".number_format($apv_row->amount,2)." {$service_type_str}Discount";
                    } else {
                        $variation-=$apv_row->amount;
                    }
                }else{ // surcharge
                    $price_variation_total+=$apv_row->amount;
                    if ($apv_row->display_on == 3 || $apv_row->display_on == 4 || $apv_row->display_on == 5 || $apv_row->display_on == 7) {
                        $price_variation_total_str .= " + \$".number_format($apv_row->amount,2)." {$service_type_str}Surcharge";
                    } else {
                        $variation+=$apv_row->amount;
                    }
                }            
        
            }
    
            // get property variation
            $pv_sql = $this->db->query("
            SELECT 
                apv.`type` AS apv_type,
                apv.`amount`,
                apv.`scope`,
        
                ajt.`type` AS ajt_type,
                ajt.`short_name`,
                dv.display_on
            FROM `property_variation` AS pv        
            LEFT JOIN `agency_price_variation` AS apv ON ( pv.`agency_price_variation` = apv.`id` AND pv.`property_id` = {$property_id} )
            LEFT JOIN `alarm_job_type` AS ajt ON ( apv.`scope` = ajt.`id` AND apv.`scope` >= 2 )
            LEFT JOIN `display_variation` AS dv ON ( apv.`id` = dv.`variation_id` AND dv.`type` = 1 )
            WHERE apv.`agency_id` = {$agency_id}    
            AND (
                apv.`scope` = 1 OR
                apv.`scope` = {$service_type}
            )
            AND (
                apv.expiry >= '{$today}'
                OR apv.expiry IS NULL
            )
            AND apv.`active` = 1
            AND pv.`active` = 1
            ");  
        
            foreach( $pv_sql->result() as $pv_row ){  

                $service_type_str = ( $pv_row->scope >= 2 )?"{$pv_row->short_name} Service ":null;
                            
                if( $pv_row->apv_type == 1 ){ // discount
                    $price_variation_total-=$pv_row->amount;
                    if ($pv_row->display_on == 3 || $pv_row->display_on == 4 || $pv_row->display_on == 5 || $pv_row->display_on == 7) {
                        $price_variation_total_str .= " - \$".number_format($pv_row->amount,2)." {$service_type_str}Discount";
                    } else {
                        $variation-=$pv_row->amount;
                    }
                }else{ // surcharge
                    $price_variation_total+=$pv_row->amount;
                    if ($pv_row->display_on == 3 || $pv_row->display_on == 4 || $pv_row->display_on == 5 || $pv_row->display_on == 7) {
                        $price_variation_total_str .= " + \$".number_format($pv_row->amount,2)." {$service_type_str}Surcharge";
                    } else {
                        $variation+=$pv_row->amount;
                    }
                }            
        
            }
    
            //$dynamic_price = $ps_row->price;
            $dynamic_price_total = $dynamic_price+$price_variation_total; // add variations
    
        }
    
        $final_total_str = ( $price_variation_total )?' = $'.number_format($dynamic_price_total,2):null;

        $dynamic_price_text = '$'.number_format($dynamic_price,2);
        $price_text = '$'.number_format($dynamic_price_total,2);
        $price_breakdown_text = '$'.number_format($dynamic_price+$variation,2).$price_variation_total_str.$final_total_str;
    
        return $ret_arr = array(
            'dynamic_price' => $dynamic_price,
            'price_variation_total' => $price_variation_total,
            'dynamic_price_total' => $dynamic_price_total,
            'dynamic_price_text' => $dynamic_price_text,
            'price_text' => $price_text,
            'price_breakdown_text' => $price_breakdown_text                  
        );
    
    }

    public function get_quotes_new_name($alarm_pwr_id){

        $sel = "
            SELECT ap.`alarm_make`, qa.`title`
            FROM `alarm_pwr` as ap
            LEFT JOIN `quote_alarms` AS qa ON ap.`alarm_pwr_id` = qa.`alarm_pwr_id`
            WHERE ap.`alarm_pwr_id` = $alarm_pwr_id
        ";
        $sql = $this->db->query($sel); 
        $row = $sql->row_array();

        if( $row['title'] != "" ){
            return $row['title'];
        }else{
            return $row['alarm_make'];
        }

    }

    public function send_2fa_code($params){

        $aua_id = $params['aua_id'];

        // get 2FA data
        $user_2fa_sql = $this->db->query("
        SELECT 
            `id`,
            `type`,
            `send_to`,
            `code`
        FROM `agency_user_2fa`
        WHERE `user_id` = {$aua_id}
        ");	
        $user_2fa_row = $user_2fa_sql->row();	
        
        if(  $user_2fa_sql->num_rows() > 0 ){

            // generate 6 digit code
            $twofa_code = rand(000000,999999);

            // update 2FA code
            $update_data = array(
                'code' => $twofa_code,
                'code_sent_ts' => date('Y-m-d H:i:s'),
            );            
            $this->db->where('user_id', $aua_id);
            $this->db->update('agency_user_2fa', $update_data);

            // send 2FA via email or mobile
            if(  $user_2fa_row->type == 2 ){ // email

                $email_body = null;

                // email settings
                $this->email->to($user_2fa_row->send_to);

                // get email template
                $email_type = $this->get_dynamic_2fa_email_template_id(); // Agency Staff User 2FA Code Request

                $total_params = array(
                    'echo_query' => 0,
                    'email_templates_id' => $email_type
                );

                $email_temp_sql = $this->email_model->get_email_templates($total_params);     
                $email_temp_row = $email_temp_sql->row();
                
                // parse tags
                $email_temp_params = array('aua_id' => $aua_id);
                $subject_parsed = $this->email_model->parseEmailTemplateTags($email_temp_params, $email_temp_row->subject);  							     
                $body_parsed = nl2br($this->email_model->parseEmailTemplateTags($email_temp_params, $email_temp_row->body));  

                $this->email->subject($subject_parsed);

                $return_as_string =  true;
                
                // get country data
                $country_sql = $this->db->query("
                SELECT *
                FROM `countries`
                WHERE `country_id` = {$this->config->item('country')}
                ");
                $email_data['country'] = $country_sql->row();

                // email content
                $email_body .= $this->load->view('emails/template/email_header.php', $email_data, $return_as_string);
                $email_body .= nl2br($body_parsed);
                $email_body .= $this->load->view('emails/template/email_footer.php', $email_data, $return_as_string);

                $this->email->message($email_body);
                $this->email->send();

            }else if( $user_2fa_row->type == 1 ){ // SMS

                $mobile = $user_2fa_row->send_to;
                $send_to = $this->sms_api_model->formatToInternationNumber($mobile); 
                
                // get SMS template
		        $sms_api_type_id = $this->get_dynamic_2fa_sms_template_id(); // Agency Staff User 2FA Code Request
                
                $sms_temp_sql = $this->db->query("
                SELECT `body`
                FROM `sms_api_type`
                WHERE sms_api_type_id = {$sms_api_type_id}
                ");
                $sms_temp_row = $sms_temp_sql->row();

                // parse tags							
                $sms_params = array(
                    'aua_id' => $aua_id,
                    'unparsed_template' => $sms_temp_row->body
                );
                $parsed_template_body = $this->sms_api_model->parseTags($sms_params);

                // send SMS
                $sms_params = array(
                    'sms_msg' => $parsed_template_body,
                    'mobile' => $send_to
                );
                $this->sms_api_model->sendSMS($sms_params);

            }

            return true;

        }else{

            return false;

        }        						

    }

    public function confirm_2fa_code($params){

        $user_id = $params['user_id'];
        $user_2fa_code = $params['user_2fa_code'];
        $ret_arr = [];

        if( $user_id > 0 ){

			// match 2FA code
			$user_2fa_sql = $this->db->query("
			SELECT 
				`code`,
				`code_sent_ts`
			FROM `agency_user_2fa`
			WHERE `user_id` = {$user_id}
			");		
			$user_2fa_row = $user_2fa_sql->row();

			// code valid for 5 minutes
			$valid_until = date('Y-m-d H:i:s',strtotime($user_2fa_row->code_sent_ts.' +5 minutes'));
			$now = date('Y-m-d H:i:s');

			
			if( ( $user_2fa_code == $user_2fa_row->code ) && $now <= $valid_until ){				

				// clear 2FA code 
				$update_data = array(
					'code' => null,
					'code_sent_ts' => null
				);				
				$this->db->where('user_id', $user_id);
				$this->db->update('agency_user_2fa', $update_data);

				$success = 1; //success

			}else{

				if( $now > $valid_until ){
					$error = 2; // code has expired
				}else{
					$error = 1; // code incorrect
				}				
				
                $success = 0; //success

			}

            $ret_arr = array(
                'success' => $success,
                'error' => $error
            );

            return $ret_arr;
			

		}


    }

    /**
     * Get the id of the sms template to be used for 2FA
     * NZ has a different table TODO: centralise table
     * @return int
     */
    public function get_dynamic_2fa_sms_template_id(){
        $template_id = 58;

        if( $this->config->item('country') == 2 ){ // NZ
            $template_id = 45;
        }

        return $template_id;
    }

    /**
     * Get the id of the email template to be used for 2FA
     * NZ has a different table TODO: centralise table
     * @return int
     */
    public function get_dynamic_2fa_email_template_id(){

        $template_id = 101;

        if( $this->config->item('country') == 2 ){
            $template_id = 69;
        }

        return $template_id;
    }

    public function api_request_limit_counter_and_delay($params){

        $agency_id = $params['agency_id'];
        $api_id = $params['api_id'];
        $request_limit = $params['request_limit'];
        $sleep_interval_sec = $params['sleep_interval_sec'];        

        // get count
        $sql = $this->db->query("
        SELECT `count`
        FROM `agency_api_request_count`
        WHERE `api_id` = {$api_id}
        AND `agency_id` = {$agency_id}
        ");
        $row = $sql->row();

        if( $row->count >= $request_limit ){ // request limit

            // sleep interval
            sleep($sleep_interval_sec); // 1 minute

            // reset to 1
            $count = 1;

        }else{

            // increment count
            $count = ($row->count+1);

        }

        if( $sql->num_rows() > 0 ){ // exist, update

            // update count
            $update_data = array(
                'count' => $count
            );            
            $this->db->where('api_id', $api_id);
            $this->db->where('agency_id', $agency_id);
            $this->db->update('agency_api_request_count', $update_data);

        }else{ //  new, insert

            $insert_data = array(
                'api_id' => $api_id,
                'count' => $count,
                'agency_id' => $agency_id
            );            
            $this->db->insert('agency_api_request_count', $insert_data);

        }                

    }

    public function integrated_api(){

        // check API integration
		$aai_sql = $this->db->query("
		SELECT `connected_service`
		FROM `agency_api_integration`
		WHERE `agency_id` = {$this->session->agency_id}
		AND `active` = 1
		");
		$aai_row = $aai_sql->row();

        $integrated_api_name = null;
        if( $aai_sql->num_rows() > 0 ){
            
            if( $aai_row->connected_service == 5 ){ // console, using webhooks

                $cak_sql = $this->db->query("
                SELECT COUNT(id) AS cak_count
                FROM `console_api_keys`
                WHERE `agency_id` = {$this->session->agency_id}
                ");

                if( $cak_sql->row()->cak_count > 0 ){
                    $integrated_api_name = 'Console';
                }
                

            }else{ // default, check tokens

                $aat_sql = $this->db->query("
                SELECT a_api.`api_name`
                FROM `agency_api_tokens` AS aat
                LEFT JOIN `agency_api` AS a_api ON aat.`api_id` = a_api.`agency_api_id`
                WHERE aat.`agency_id` = {$this->session->agency_id}
                AND aat.`active` = 1
                AND a_api.`active` = 1
                ");

                if( $aat_sql->num_rows() > 0 ){

                    $aat_row = $aat_sql->row();
                    $integrated_api_name = $aat_row->api_name;

                }
                
            }	

        }
				
        return $integrated_api_name;

    }

    public function console_terms_and_conditions(){

        $this->load->model('agency_model');

        // get agency name
		$params = array(
			'sel_query' => '
				a.`agency_id`,
				a.`agency_name`,
				a.`state`,

				c.`agent_number`
			',
			'join_table' => array('countries'),
			'agency_id' => $this->session->agency_id
		);
		$agency_sql = $this->agency_model->get_agency_data($params);
		$agency_row = $agency_sql->row();
    ?>
        <b>ADDING COMPLIANCE ITEMS:</b>
        <ul class="ul_terms">
            <li>Any compliance items assigned to <?=$this->config->item('COMPANY_NAME_SHORT')?> will trigger a visit and an annual Subscription Fee</li>
            <li><?=$this->config->item('COMPANY_NAME_SHORT')?> will attend all active properties only when required, to meet your states legislation</li>
        </ul>
        <br />
    
        <b>VISITS NOT REQUIRED FOR COMPLIANCE:</b>
        <ul class="ul_terms">
            <li>If you require any additional visits outside of what is required for legislation (eg, Beeping Alarm<?php echo ( $this->session->country_id == 1 && $agency_row->state != 'QLD' )?'/Change of Tenancy':null; ?>) please create a job in our Agency Portal or email us at <?php echo make_email('info'); ?></li>
        </ul> 
        <br />

        <b>DATA DISCREPANCIES:</b>
        <ul class="ul_terms">
            <li><?=$this->config->item('COMPANY_NAME_SHORT')?> will not amend our existing expiry dates if the property has previously been serviced by <?=$this->config->item('COMPANY_NAME_SHORT')?>, and there is a discrepancy between Console/<?=$this->config->item('COMPANY_NAME_SHORT')?> expiry date data. However, for new properties, where applicable, please add the last inspection date and subscription expiry date so that <?=$this->config->item('COMPANY_NAME_SHORT')?> can ensure that there are no data discrepancies.</li>
        </ul>
        <br />

        <b>CURRENTLY SERVICED PROPERTIES:</b>
        <ul class="ul_terms">
            <li>Any property that is currently serviced by <?=$this->config->item('COMPANY_NAME_SHORT')?> will remain on the same service option, we will not adjust the service based on data received from Console. If you wish to change the services that <?=$this->config->item('COMPANY_NAME_SHORT')?> conducts on a property, you must do this via the <a href="/jobs/create">Agency Portal</a> or by contacting our friendly Customer Service team on <?php echo $agency_row->agent_number; ?>. Any new properties added to our database via Console will only have the service type applied that is the compliance item.</li>
        </ul>
        <br />

        <b>DELIVERY OF DOCUMENTS INTO CONSOLE</b>
        <ul class="ul_terms">
            <li>Upon job completion, we will upload into Console, the Statement of Compliance (Workflows > Compliance) and where applicable, the Invoice (Accounts>Bills).</li>
        </ul>
        <br />
    <?php
    }

}
