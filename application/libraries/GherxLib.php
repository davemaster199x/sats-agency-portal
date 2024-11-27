<?php

class GherxLib {

	protected $CI;

	public function __construct(){
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->model('jobs_model'); //load jobs model
		 $this->CI->load->model('properties_model'); //load jobs model
    }

    function agency_info(){
        $query = $this->CI->db->get_where('agency', array('agency_id'=>$this->CI->session->agency_id));
        return ($query->num_rows()>0)?$query->row():false;
    }


     //return true otherwise false
    public function isDHAagenciesV2($agency_id){

            $this->CI->db->select('franchise_groups_id');
            $this->CI->db->from('agency');
            $this->CI->db->where('agency_id',$agency_id);
            $query = $this->CI->db->get();
            if($query->num_rows()>0){

                    if( $query->row()->franchise_groups_id == 14 ){
                        return true;
                    }else{
                        return false;
                    }

            }else{
                return false;
            }

    }


    //return true otherwise false
    function agencyHasMaintenanceProgram($agency_id){

            $this->CI->db->select('*');
            $this->CI->db->from('agency_maintenance');
            $this->CI->db->where('agency_id',$agency_id);
            $this->CI->db->where('maintenance_id >',0);
            $this->CI->db->where('status',1);
            $query = $this->CI->db->get();

            if( $query->num_rows()>0 ){
                return true;
            }else{
                return false;
            }

    }

    //get agent full name
    //return full name
    function agent_full_name(){

        $this->CI->load->model('user_accounts_model');
        $user_row = $this->CI->user_accounts_model->get_user_account_via_id($this->CI->session->aua_id);
        $agent_full_name = $user_row->fname." ".$user_row->lname;
        return $agent_full_name;

    }

    function getCountryViaCountryId(){
       $query =  $this->CI->db->get_where('countries', array('country_id'=> $this->CI->session->country_id));
       return $query->row();
    }

    //get property address
    function prop_address($prop_id){
        $this->CI->db->select('address_1,address_2,address_3');
        $this->CI->db->from('property');
        $this->CI->db->where('property_id',$prop_id);
        $query = $this->CI->db->get();
        $row =  $query->row();
        return $row->address_1." ".$row->address_2." ".$row->address_3;

    }

    function getJobTotalAmount($job_id){

        $grand_total = 0;

        $this->CI->db->select('*');
        $this->CI->db->from('jobs as j');
        $this->CI->db->join('property as p','p.property_id = j.property_id','left');
        $this->CI->db->join('agency as a','a.agency_id = p.agency_id','left');
        $this->CI->db->where('j.id',$job_id);
        $query = $this->CI->db->get();
        $row = $query->row_array();

        $grand_total = $row['job_price'];

        //get new alarm
        $this->CI->db->select('*');
        $this->CI->db->from('alarm');
        $this->CI->db->where('job_id', $job_id);
        $this->CI->db->where('new',1);
        $this->CI->db->where('ts_discarded',0);
        $query = $this->CI->db->get();

        foreach($query->result_array() as $a){
            $grand_total += $a['alarm_price'];
        }

        // get safety switch
        $ss_sql = $this->CI->db->query("
        SELECT ss_stock.`sell_price`
        FROM `safety_switch` AS ss
        LEFT JOIN `safety_switch_stock` AS ss_stock ON ss.`ss_stock_id` = ss_stock.`ss_stock_id`
        WHERE ss.`job_id` = {$job_id}
        AND ss.`new` = 1
        AND ss.`discarded` = 0
        ");
        foreach( $ss_sql->result() as $ss_row ) {
            $grand_total += $ss_row->sell_price;
        }

        //surcharge
        $this->CI->db->select('m.name as m_name');
        $this->CI->db->from('agency_maintenance as am');
        $this->CI->db->join('maintenance as m','m.maintenance_id = am.maintenance_id','left');
        $this->CI->db->where('am.agency_id', $row['agency_id']);
        $this->CI->db->where('am.maintenance_id >', 0);
        $query = $this->CI->db->get();
        $sc = $query->row_array();

        if( $grand_total!=0 && $sc['surcharge']==1 ){
			$grand_total += $sc['price'];
		}

        return $grand_total;

    }

    public function getJobInvoicePayments($job_id){

        $this->CI->db->select('SUM(amount_paid) AS amount_paid_tot');
        $this->CI->db->from('invoice_payments');
        $this->CI->db->where('job_id', $job_id);
        $this->CI->db->where('active', 1);
        $query = $this->CI->db->get();
        $row = $query->row_array();
        return $row['amount_paid_tot'];

    }

    public function getJobInvoiceRefunds($job_id){

        $this->CI->db->select('SUM(amount_paid) AS refund_paid_tot');
        $this->CI->db->from('invoice_refunds');
        $this->CI->db->where('job_id', $job_id);
        $this->CI->db->where('active', 1);
        $query = $this->CI->db->get();
        $row = $query->row_array();
        return $row['refund_paid_tot'];

    }

    public function getJobInvoiceCredits($job_id){

        $this->CI->db->select('SUM(credit_paid) AS credit_paid_tot');
        $this->CI->db->from('invoice_credits');
        $this->CI->db->where('job_id', $job_id);
        $this->CI->db->where('active', 1);
        $query = $this->CI->db->get();
        $row = $query->row_array();
        return $row['credit_paid_tot'];

    }


    function updateInvoiceDetails($job_id){

            if($job_id){

                    //get current job details
                    $this->CI->db->select('invoice_amount,invoice_payments,invoice_refunds,invoice_credits,invoice_balance');
                    $this->CI->db->from('jobs');
                    $this->CI->db->where('id', $job_id);
                    $query = $this->CI->db->get();
                    $job = $query->row_array();

                    $invoice_amount_orig = $job['invoice_amount'];
                    $invoice_payments_orig = $job['invoice_payments'];
                    $invoice_refunds_orig = $job['invoice_refunds'];
                    $invoice_credits_orig = $job['invoice_credits'];
                    $invoice_balance_orig = $job['invoice_balance'];

                    // get the calculated values
                    // invoice amount
                    $inv_a = $this->getJobTotalAmount($job_id);
                    $invoice_amount = ( $inv_a > 0 )?$inv_a:0;

                    // invoice payments
                    $inv_p = $this->getJobInvoicePayments($job_id);
                    $invoice_payments = ( $inv_p > 0 )?$inv_p:0;

                    // invoice refunds
                    $inv_r = $this->getJobInvoiceRefunds($job_id);
                    $invoice_refunds = ( $inv_r > 0 )?$inv_r:0;

                    // invoice credits
                    $inv_c = $this->getJobInvoiceCredits($job_id);
                    $invoice_credits = ( $inv_c > 0 )?$inv_c:0;

                    // invoice balance
                    $invoice_balance = ($invoice_amount+$invoice_refunds) - ( $invoice_payments + $invoice_credits);

                    //update jobs if invoice details changed
                    if(
                        $invoice_amount_orig == '' ||
                        $invoice_amount_orig != $invoice_amount ||
                        $invoice_payments_orig != $invoice_payments ||
                        $invoice_refunds_orig != $invoice_refunds ||
                        $invoice_credits_orig != $invoice_credits ||
                        $invoice_balance_orig != $invoice_balance
                    ){
                        //update jobs
                        $data = array(
                            'invoice_amount' => $invoice_amount,
                            'invoice_payments' => $invoice_payments,
                            'invoice_refunds' => $invoice_refunds,
                            'invoice_credits' => $invoice_credits,
                            'invoice_balance' => $invoice_balance
                        );
                        $this->CI->db->where('id',$job_id);
                        $this->CI->db->update('jobs',$data);

                    }

            }

    }

    //get bundled services
    //return row array
    function getbundleServices($job_id,$bs_id){

        $this->CI->db->select('*');
        $this->CI->db->from('bundle_services as bs');
        $this->CI->db->join('alarm_job_type as ajt','ajt.id = bs.alarm_job_type_id','left');
        $this->CI->db->where('job_id', $job_id);
        if($bs_id!=""){
            $this->CI->db->where('bundle_services_id',$bs_id);
        }
        $this->CI->db->order_by('ajt.id');

        return $this->CI->db->get()->row_array();

    }

    //get job details by job id
    //return row_array otherwise false
    function jGetJobDetails($job_id){

        // get job details
        $this->CI->db->select('*');
        $this->CI->db->from('jobs as j');
        $this->CI->db->join('alarm_job_type as ajt','ajt.id = j.service','left');
        $this->CI->db->where('j.id',$job_id);
        $query = $this->CI->db->get();
        return ($query->num_rows()>0)?$query->row_array():false;

    }


    function SnycSmokeAlarmData($job_id,$prev_job_id){

        //$prev_job2 = $prev_job_id;

        //$get_job1 = $this->CI->db->get_where('jobs', array('id' => $prev_job_id));
        $get_job1 = $this->CI->db->select('*')->from('jobs')->where('id',$prev_job_id)->get();
        $prev_job = $get_job1->row_array();


        // update safety alarm details
        $data = array(
            'survey_numlevels' => $prev_job['survey_numlevels'],
            'survey_ceiling' => $prev_job['survey_ceiling'],
            'survey_ladder' => $prev_job['survey_ladder'],
            'ts_safety_switch' => $prev_job['ts_safety_switch'],
            'ss_location' => $prev_job['ss_location'],
            'ss_quantity' => $prev_job['ss_quantity'],
            'ts_safety_switch_reason' => $prev_job['ts_safety_switch_reason'],
            'ss_image' => $prev_job['ss_image']
        );
        $this->CI->db->where('id',$job_id);
        $this->CI->db->update('jobs',$data);

        // get previous job and insert previous alarm to this
       /* $get_alarm_sql = $this->CI->db->get_where('alarm',array('job_id'=>$prev_job['id'], 'ts_discarded' => 0));

        if( $get_alarm_sql->num_rows()>0){

            $get_alarm_sql_row = $get_alarm_sql->row_array();

            $data_insert_alarm = array(
                'job_id' => $job_id,
                'alarm_power_id' => $get_alarm_sql_row['alarm_power_id'],
                'alarm_type_id' => $get_alarm_sql_row['alarm_type_id'],
                'make' => $get_alarm_sql_row['make'],
                'model' => $get_alarm_sql_row['model'],
                'ts_position' => $get_alarm_sql_row['ts_position'],
                'alarm_job_type_id' => $get_alarm_sql_row['alarm_job_type_id'],
                'expiry' => $get_alarm_sql_row['expiry'],
                'ts_required_compliance' => $get_alarm_sql_row['ts_required_compliance']
            );
            $this->CI->db->insert('alarm',$data_insert_alarm);
         }
         */

        $this->CI->db->query("
		INSERT INTO
		`alarm` (
			`job_id`,
			`alarm_power_id`,
			`alarm_type_id`,
			`make`,
			`model`,
			`ts_position`,
			`alarm_job_type_id`,
			`expiry`,
			`ts_required_compliance`
		)
		SELECT
			{$job_id},
			`alarm_power_id`,
			`alarm_type_id`,
			UPPER( `make` ),
			UPPER( `model` ),
			UPPER( `ts_position` ),
			`alarm_job_type_id`,
			`expiry`,
			`ts_required_compliance`
		FROM `alarm`
		WHERE `job_id` = {$prev_job['id']}
		AND `ts_discarded` = 0
	");

    }


    function SnycSafetySwitchData($job_id,$prev_job_sql2){

        //get prop id
        $get_prop_id = $this->CI->db->get_where('jobs',array('id'=>$job_id));
        $p = $get_prop_id->row_array();

        //check if no ss data yet
        $this->CI->db->select('*');
        $this->CI->db->from('safety_switch as ss');
        $this->CI->db->join('jobs as j','j.id = ss.job_id','left');
        $this->CI->db->where('property_id',$p['property_id']);
        $this->CI->db->where('j.status','Completed');
        $this->CI->db->where('ss.discarded',0);
        $ss_query = $this->CI->db->get();

        if($ss_query->num_rows()>0){ 	// has already SS data, get previous SS

            //get previous ss data
           // $prev_job2 = $prev_job_sql2;
            $query1 = $this->CI->db->get_where('jobs',array('id'=> $prev_job_sql2));

            $prev_job = $query1->row_array();

            // update safety switch job details
            $data = array(
                'ss_location' => $prev_job['ss_location'],
                'ss_quantity' => $prev_job['ss_quantity']
            );
            $this->CI->db->where('id',$job_id);
            $this->CI->db->update('jobs',$data);


        }else{ // no SS data yet, get it from alarm

            // get previous SA data
            $prev_job2 = $this->CI->jm->getPrevSmokeAlarm($p['property_id']);
            $query1 = $this->CI->db->get_where('jobs',array('id'=> $job_id));
            $prev_job = $query1->row_array();

            // update safety switch job details
            $data = array(
                'ss_location' => $prev_job['ss_location'],
                'ss_quantity' => $prev_job['ss_quantity']
            );
            $this->CI->db->where('id',$job_id);
            $this->CI->db->update('jobs',$data);

        }

        // get previous job and insert previous safety switch to this job
       /* $prev_ss_query = $this->CI->db->get_where('safety_switch',array('job_id'=>$prev_job['id']));
        $prev_ss_query_row = $prev_ss_query->row_array();

        $ss_data = array(
            'job_id' => $job_id,
            'make' => $prev_ss_query_row['make'],
            'model' => $prev_ss_query_row['model']
        );
        $this->CI->db->insert('safety_switch',$ss_data); */

        $this->CI->db->query("
		INSERT INTO
		`safety_switch` (
			`job_id`,
			`make`,
			`model`,
            `ss_stock_id`
		)
		SELECT {$job_id}, `make`, `model`, `ss_stock_id`
		FROM `safety_switch`
		WHERE `job_id` = {$prev_job['id']}
        AND `discarded` = 0
	");


    }

    function SnycCordedWindowData($job_id,$prev_job_sql2){

        //$prev_job2 = $prev_job_sql2;
        $query1 = $this->CI->db->get_where('jobs',array('id'=> $prev_job_sql2));

        $prev_job = $query1->row_array();

        // get previous job and insert previous corded window to this job
       /* $cc_query = $this->CI->db->get_where('corded_window',array('job_id'=>$prev_job['id']));
        $cc_query_row = $cc_query->row_array();

        $cc_data = array(
            'job_id' => $job_id,
            'covering' => $cc_query_row['covering'],
            'ftllt1_6m' => $cc_query_row['ftllt1_6m'],
            'tag_present' => $cc_query_row['tag_present'],
            'clip_rfc' => $cc_query_row['clip_rfc'],
            'clip_present' => $cc_query_row['clip_present'],
            'loop_lt220m' => $cc_query_row['loop_lt220m'],
            'seventy_n' => $cc_query_row['seventy_n'],
            'cw_image' => $cc_query_row['cw_image'],
            'location' => $cc_query_row['location'],
            'num_of_windows' => $cc_query_row['num_of_windows']
        );
        $this->CI->db->insert('corded_window',$cc_data); */

        $this->CI->db->query("
		INSERT INTO
		`corded_window` (
			`job_id`,
			`covering`,
			`ftllt1_6m`,
			`tag_present`,
			`clip_rfc`,
			`clip_present`,
			`loop_lt220m`,
			`seventy_n`,
			`cw_image`,
			`location`,
			`num_of_windows`
		)
		SELECT
			'{$job_id}',
			`covering`,
			`ftllt1_6m`,
			`tag_present`,
			`clip_rfc`,
			`clip_present`,
			`loop_lt220m`,
			`seventy_n`,
			`cw_image`,
			`location`,
			`num_of_windows`
		FROM `corded_window`
		WHERE `job_id` = {$prev_job['id']}
	");

    }

    function SnycWaterMeter($job_id,$prev_job_sql2){

        //$prev_job2 = $prev_job_sql2;
        $query1 = $this->CI->db->get_where('jobs',array('id'=> $prev_job_sql2));

        $prev_job = $query1->row_array();

        // get previous job and insert previous water meter to this job
        /*$wm_query = $this->CI->db->get_where('water_meter',array('job_id'=>$prev_job['id']));
        $wm_query_row = $wm_query->row_array();

        $wm_data = array(
            'job_id' => $job_id,
            'location' => $wm_query_row['location'],
            'meter_image' => $wm_query_row['meter_image'],
            'created_date' => $wm_query_row['created_date'],
            'active' => 1
        );
        $this->CI->db->insert('water_meter',$wm_data); */

        $this->CI->db->query("
		INSERT INTO
		`water_meter` (
			`job_id`,
			`location`,
			`meter_image`,
			`created_date`,
			`active`
		)
		SELECT
			'{$job_id}',
			`location`,
			`meter_image`,
			'".date('Y-m-d H:i:s')."',
			'1'
		FROM `water_meter`
		WHERE `job_id` = {$prev_job['id']}
	");

    }

    // get previous job and insert previous corded window to this job
    function SnycWaterEfficiency($job_id, $prev_job_sql2) {

        $today_full_ts = date('Y-m-d H:i:s');

        // get previous job
        $query1 = $this->CI->db->get_where('jobs',array('id'=> $prev_job_sql2));

        $prev_job = $query1->row_array();


        if(  $job_id > 0 && $prev_job['id'] > 0 ){

            $ss_sql2 = "
                INSERT INTO
                `water_efficiency` (
                    `job_id`,
                    `device`,
                    `location`,
                    `note`,
                    `created_date`
                )
                SELECT
                    '{$job_id}',
                    `device`,
                    `location`,
                    `note`,
                    '{$today_full_ts}'
                FROM `water_efficiency`
                WHERE `job_id` = {$prev_job['id']}
            ";
            $this->CI->db->query($ss_sql2);

        }

    }

    function markAsSyncBundle($bundle_id){

        // marked as synced
        $data = array('sync'=>1);
        $this->CI->db->where('bundle_services_id',$bundle_id);
        $this->CI->db->update('bundle_services',$data);

    }
    function markAsSync($job_id,$jserv){

        switch($jserv){
                case 2:
                    $sync_field = '`alarms_synced`';
                break;
                // SA IC
                case 12:
                    $sync_field = '`alarms_synced`';
                break;
                case 5:
                    $sync_field = '`ss_sync`';
                break;
                case 6:
                    $sync_field = '`cw_sync`';
                break;
                case 7:
                    $sync_field = '`wm_sync`';
                break;
                case 15: // WE
                    $sync_field = '`we_sync`';
                break;
            }

        // mark as sync
        $data = array($sync_field => 1);
        $this->CI->db->where('id',$job_id);
        $this->CI->db->update('jobs',$data);

    }



    function runSync($job_id,$jserv,$bundle_id_param=NULL){

        //get job details
        $j5 =  $this->jGetJobDetails($job_id);

        //is bundle
	    if($j5['bundle']==1){ //is bundle

            // get bundle id
            $bun_ids = explode(",",trim($j5['bundle_ids']));

            $bundle_id = ($bundle_id_param!="")?$bundle_id_param:$bun_ids[0];

            // check if jobs are already synced
            $query = $this->CI->db->get_where('bundle_services',array('bundle_services_id' => $bundle_id));
            $js = $query->row_array();

            // not yet snyc, do sync
            if($js['sync']==0){

                    // get previous safety switch that is job status completed
                    switch($jserv){
                        case 2:
                            $prev_job_sql = $this->CI->jobs_model->getPrevSmokeAlarm($j5['property_id']);
                        break;
                        case 12:
                            $prev_job_sql = $this->CI->jobs_model->getPrevSmokeAlarm($j5['property_id']);
                        break;
                        case 5:
                            $prev_job_sql = $this->CI->jobs_model->getPrevSafetySwitch($j5['property_id']);
                        break;
                        case 6:
                            $prev_job_sql = $this->CI->jobs_model->getPrevCordedWindow($j5['property_id']);
                        break;
                        case 7:
                            $prev_job_sql = $this->CI->jobs_model->getPrevWaterMeter($j5['property_id']);
                        break;
                        case 15: // WE
                            $prev_job_sql = $this->CI->jobs_model->getPrevWaterEfficiency($j5['property_id']);
                        break;
                    }

                    if($prev_job_sql){

                        switch($jserv){
                            case 2:
                                $this->SnycSmokeAlarmData($job_id, $prev_job_sql['id']);
                            break;
                            case 12:
                                $this->SnycSmokeAlarmData($job_id, $prev_job_sql['id']);
                            break;
                            case 5:
                                $this->SnycSafetySwitchData($job_id, $prev_job_sql['id']);
                            break;
                            case 6:
                                $this->SnycCordedWindowData($job_id, $prev_job_sql['id']);
                            break;
                            case 7:
                                $this->SnycWaterMeter($job_id, $prev_job_sql['id']);
                            break;
                            case 15: // WE
                                $this->SnycWaterEfficiency($job_id, $prev_job_sql['id']);
                            break;
                        }

                        $this->markAsSyncBundle($bundle_id);

                    }



            }

        }else{  //is not bundle

            switch($jserv){
                case 2:
                    $is_sync = $j5['alarms_synced'];
                break;
                case 12:
                    $is_sync = $j5['alarms_synced'];
                break;
                case 5:
                    $is_sync = $j5['ss_sync'];
                break;
                case 6:
                    $is_sync = $j5['cw_sync'];
                break;
                case 7:
                    $is_sync = $j5['wm_sync'];
                break;
                case 15: // WE
                    $is_sync = $j5['we_sync'];
                break;
            }

            if($is_sync==0){

                	// get previous safety switch that is job status completed
                    switch($jserv){
                        case 2:
                            $prev_job_sql = $this->CI->jobs_model->getPrevSmokeAlarm($j5['property_id']);
                        break;
                        case 12:
                            $prev_job_sql = $this->CI->jobs_model->getPrevSmokeAlarm($j5['property_id']);
                        break;
                        case 5:
                            $prev_job_sql =  $this->CI->jobs_model->getPrevSafetySwitch($j5['property_id']);
                            if( $prev_job_sql == false ){
                                $prev_job_sql = $this->CI->jobs_model->getPrevSmokeAlarm($j5['property_id']);
                            }
                        break;
                        case 6:
                            $prev_job_sql = $this->CI->jobs_model->getPrevCordedWindow($j5['property_id']);
                        break;
                        case 7:
                            $prev_job_sql = $this->CI->jobs_model->getPrevWaterMeter($j5['property_id']);
                        break;
                        case 15: // WE
                            $prev_job_sql = $this->CI->jobs_model->getPrevWaterEfficiency($j5['property_id']);
                        break;
                    }


                    if($prev_job_sql){
                        switch($jserv){
                            case 2:
                                $this->SnycSmokeAlarmData($job_id, $prev_job_sql['id']);
                            break;
                            // SA IC
                            case 12:
                                $this->SnycSmokeAlarmData($job_id,  $prev_job_sql['id']);
                            break;
                            case 5:
                                $this->SnycSafetySwitchData($job_id,  $prev_job_sql['id']);
                            break;
                            case 6:
                                $this->SnycCordedWindowData($job_id,  $prev_job_sql['id']);
                            break;
                            case 7:
                                $this->SnycWaterMeter($job_id,  $prev_job_sql['id']);
                            break;
                            case 15: // WE
                                $this->SnycWaterEfficiency($job_id,  $prev_job_sql['id']);
                            break;
                        }

                        $this->markAsSync($job_id,$jserv);

                    }


            }




        }
    }


    function runJobSync($job_id,$alarm_job_type_id,$property_id){

            //get alarm job type
            $this->CI->db->select('*');
            $this->CI->db->from('alarm_job_type');
            $this->CI->db->where('id', $alarm_job_type_id);
            $ajt = $this->CI->db->get()->row_array();

            if($ajt['bundle']==1){ //bundle

                    $b_ids = explode(",",trim($ajt['bundle_ids']));

                    foreach($b_ids as $val){

                        //insert bundles
                        $data = array('job_id' => $job_id, 'alarm_job_type_id' => $val);
                        $this->CI->db->insert('bundle_services',$data);

                        //bundle services last id
                        $bundle_id = $this->CI->db->insert_id();
                        $bs_id = $bundle_id;

                        //get alarm job type id
                        $bs2 = $this->getbundleServices($job_id,$bs_id);
                        $ajt_id = $bs2['alarm_job_type_id'];

                        // sync alarm
                        $this->runSync($job_id,$ajt_id,$bundle_id);

                    }

            }else{ //not bundle

                $this->runSync($job_id,$alarm_job_type_id);  //sync alarm

            }

            $ps_data = array(
                'property_id' => $property_id,
                'alarm_job_type_id' => $alarm_job_type_id
            );
            $this->CI->db->insert('property_propertytype',$ps_data);


    }

    function if240vRebook($job_id){

        // if alarm is 240v and expiry is current year, changed job type to 240v rebook

                $this->CI->db->select('*');
                $this->CI->db->from('alarm');
                $this->CI->db->where('job_id',$job_id);
                $this->CI->db->where('expiry', date('Y'));
                $this->CI->db->group_start();
                $this->CI->db->where('alarm_power_id', 2);
                $this->CI->db->or_where('alarm_power_id', 4);
                $this->CI->db->group_end();
                $query = $this->CI->db->get();

                if($query->num_rows()>0){
                    return true;
                }else{
                    return false;
                }

    }

    // check job status
    // return true if job/status == BOOKED, Pre Completion etc....
    function NLMjobStatusCheck($prop_id){

        $this->CI->db->select("COUNT(jobs.id) AS count");
        $this->CI->db->from('jobs');
        $this->CI->db->where('property_id', $prop_id);
        $this->CI->db->where('del_job', 0);
        $this->CI->db->group_start();
        $this->CI->db->where('status','Booked');
        $this->CI->db->or_where('status','Pre Completion');
        $this->CI->db->or_where('status','Merged Certificates');
        //$this->CI->db->or_where('status','Cancelled');
        $this->CI->db->group_end();
        $this->CI->db->limit(1);
        $query = $this->CI->db->get();
        try {
            return ($query->row()->count > 0) ? TRUE : FALSE;
        }
        catch (\Exception $ex) {
            return FALSE;
        }

    }

    // Job status (rename job status)
    function jobStatusNewName($status){
        $status_new = "";
        switch($status){
            case 'Escalate':
            $status_new = 'Help Needed';
            break;

            case 'Booked':
            $status_new = "Booked";
            break;

            case 'Merged Certificates':
            $status_new = "Completed";
            break;

            case 'On Hold':
            $status_new = "On Hold";
            break;

            case 'Pending':
            $status_new = "Due for Service";
            break;

            case 'Send Letters':
            $status_new = "To be Booked";
            break;

            case 'To Be Booked':
            $status_new = "To be Booked";
            break;

            case 'Cancelled':
            $status_new = "Cancelled";
            break;

            case 'Completed':
            $status_new = "Completed";
            break;

            default:
            $status_new = $status;

        }
        return $status_new;
    }

    function jobStatusNewNameMouseHover($status){
        $status_new = "";
        switch($status){
            case 'Escalate':
            $status_new = 'Job requires your instruction';
            break;

            case 'Booked':
            $status_new = "Job is booked";
            break;

            case 'Merged Certificates':
            $status_new = "Job is completed";
            break;

            case 'On Hold':
            $status_new = "Job is on hold";
            break;

            case 'Pending':
            $status_new = "Due for Service";
            break;

            case 'Send Letters':
            $status_new = "We have received a job";
            break;

            case 'To Be Booked':
            $status_new = config_item('COMPANY_NAME_SHORT') . " are actively working on this job";
            break;

            case 'Cancelled':
            $status_new = "Cancelled";
            break;

            case 'Completed':
            $status_new = "Completed";
            break;

            case 'Pre Completion':
            $status_new = "Job Details are being verified";
            break;

            case 'To Be Invoiced':
            $status_new = "Job is being invoiced";
            break;

            case 'Allocate':
            $status_new = "Job is being allocated to available timeslot";
            break;

            default:
            $status_new = $status;

        }
        return $status_new;
    }



	function jobStatusNewName_v2($status){

        $status_new = "";
        switch($status){

            case 'Escalate':
				$status_new = 'Help Needed';
            break;

            case 'To Be Booked':
				$status_new = 'Booking In Progress';
            break;

            case 'On Hold - COVID':

                if( config_item('theme') == 'sas' ){ // SAS only
                    $status_new = 'On Hold - Verifying Payment';
                }else{ // default
                    $status_new = $status;
                }
				
            break;

			default:
				$status_new = $status;

        }
        return $status_new;

    }

    function selected_service_label($status){
        $label = "";
        switch($status){
            case 1:
            $label = config_item('COMPANY_NAME_SHORT');
            break;

            case 0:
            $label = "DIY";
            break;

            case 2:
            $label = 'No Response';
            break;

            case 3:
            $label = "Other Provider";
            break;
        }
        return $label;
    }

    function avatarv2($photo){

        if($photo && $photo!=""){
            return "<img class='profile_pic_small border-0 border-info' src='/uploads/user_accounts/photo/{$photo}'>";
        }else{
            return "<img class='profile_pic_small border-0 border-info' src='{$this->CI->config->item('photo_empty')}'>";
        }

    }

    //get user info that is currently logined
    //return row
    function get_user_login_info(){

        $this->CI->db->select('*, MAX(aul.date_created ) as last_login');
        $this->CI->db->from('agency_user_accounts as aua');
        $this->CI->db->join('agency_user_logins as aul','aul.user = aua.agency_user_account_id','left');
        $this->CI->db->where('aua.agency_user_account_id',$this->CI->session->aua_id);
        $this->CI->db->order_by('aul.date_created','DESC');
        $this->CI->db->limit(1);
        $query = $this->CI->db->get();

        return ($query->num_rows()>0)?$query->row():false;

    }


    //return true otherwise false
    function agencyIsAutoRenew($agency_id){

       $this->CI->db->select('auto_renew');
       $this->CI->db->from('agency');
       $this->CI->db->where('agency_id',$agency_id);
       $this->CI->db->where('auto_renew',1);
       $query = $this->CI->db->get();
       return ($query->num_rows()>0)?true:false;

    }

    /**
     * Check if Property PM is in corrent agency
     * @params user id
     * return boolean
     */
	function checkPmAgency($aua_id){
        $this->CI->db->select('*');
        $this->CI->db->from('agency_user_accounts as aua');
        $this->CI->db->where('aua.agency_user_account_id', $aua_id);
        $this->CI->db->where('agency_id', $this->CI->session->agency_id);
        $query = $this->CI->db->get();
        if($query->num_rows()>0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Check agency accounts_reports preference
     * return boolean
     */
    public function check_agency_accounts_reports_preference(){
        $this->CI->db->select('accounts_reports');
        $this->CI->db->from('agency');
        $this->CI->db->where('agency_id', $this->CI->session->agency_id);
        $query = $this->CI->db->get();
        $row = $query->row_array();
        if($row['accounts_reports']==1){
            return true;
        }else{
            return false;
        }
    }

    public function clear_job_allocate_response($job_id){

        $this->CI->db->select('allocate_response');
        $this->CI->db->from('jobs');
        $this->CI->db->where('id', $job_id);
        $query = $this->CI->db->get();
        $row = $query->row_array();
        if($row['allocate_response']!=""){

            //clear allocate_response
            $udpdate_data = array('allocate_response'=> NULL);
            $this->CI->db->where('id', $job_id);
            $this->CI->db->update('jobs', $udpdate_data);
            $this->CI->db->limit(1);

        }

    }

    public function isCompassFG($agency_id){

        $this->CI->db->select('franchise_groups_id');
        $this->CI->db->from('agency');
        $this->CI->db->where('agency_id',$agency_id);
        $this->CI->db->where('status!=','target');
        $this->CI->db->where('franchise_groups_id', 39);
        $query = $this->CI->db->get();
        if($query->num_rows()>0){
            return true;
        }else{
            return false;
        }

}


    function getStaffInfo($params) {
        if ($params['sel_query'] && $params['sel_query'] != "") {
            $this->CI->db->select($params['sel_query']);
        } else {
            $this->CI->db->select('*');
        }

        $this->CI->db->from('staff_accounts as sa');
        $this->CI->db->join('country_access ca', 'ca.staff_accounts_id = sa.StaffID', 'INNER');

        // custom joins
        if (isset($params['custom_joins']) && $params['custom_joins'] != '') {
            $this->CI->db->join($params['custom_joins']['join_table'], $params['custom_joins']['join_on'], $params['custom_joins']['join_type']);
        }

        $this->CI->db->where('sa.active', 1);
        $this->CI->db->where('sa.Deleted', 0);
        $this->CI->db->where('ca.country_id', $this->CI->config->item('country'));

        //staff_id
        if ($params['staff_id'] != "") {
            $this->CI->db->where('sa.StaffID', $params['staff_id']);
        }

        // sort
        if (isset($params['sort_list'])) {
            foreach ($params['sort_list'] as $sort_arr) {
                if ($sort_arr['order_by'] != "" && $sort_arr['sort'] != '') {
                    $this->CI->db->order_by($sort_arr['order_by'], $sort_arr['sort']);
                }
            }
        }

        $query = $this->CI->db->get();
        return $query;
    }


}

