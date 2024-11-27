<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Console extends CI_Controller {

    function __construct(){

        parent::__construct();
		$this->load->model('console_model');

    }

    public function index(){
        
        
    }    

    public function display_webhook_data()
    {        
        
        $sql = $this->db->query("
        SELECT `json`
        FROM `console_webhooks_data`        
        ");
        $row = $sql->result();     
       
        foreach( $sql->result() as $row ){

            $json_dec = json_decode($row->json);

            echo "<pre>";
            print_r($json_dec);
            echo "</pre>";

            echo "---------------<br /><br />";

            echo "<table>";
            echo "<tr>";
            echo "<th>eventId</th><td>{$json_dec->eventId}</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<th>officeId</th><td>{$json_dec->officeId}</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<th>recipientPartnerCode</th><td>{$json_dec->event->recipientPartnerCode}</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<th>lastUpdatedDateTime</th><td>".date('Y-m-d H:i',strtotime($json_dec->event->lastUpdatedDateTime))."</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<th>propertyId</th><td>{$json_dec->event->relatedResources->property->propertyId}</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<th>Full Address</th><td>{$json_dec->event->relatedResources->property->displayName}</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<th>Compliance Notes</th><td>{$json_dec->event->relatedResources->propertyCompliance->notes}</td>";
            echo "</tr>";
            echo "</table>";

            echo "--------------<br /><br />";
          
        }
      
        
        
    }

    public function verify_integration() {

        $api_key = $this->input->get_post('api_key');
        $agency_id = $this->session->agency_id;
        
        if( $api_key != '' ){

            $this->console_model->verify_integration($api_key);   
            $json_data = $this->console_model->get_integration($api_key);       
            //print_r($json_data); 

            if( $agency_id > 0 && $json_data->officeId != '' ){

                // clear incase duplicates
                $this->db->where('agency_id', $agency_id);
                $this->db->delete('console_api_keys');

                // capture API key and assign it to agency
                $insert_data = array(
                    'api_key' => $api_key,
                    'agency_id' => $agency_id,
                    'office_id' => $json_data->officeId
                );
                
                $this->db->insert('console_api_keys', $insert_data);

                //Insert Job Log
                $details_log = "API Key for Console saved and this connection is now active.";
                $params_job_log = array(
                    'title' => 90, // Console API
                    'details' => $details_log,
                    'display_in_vad' => 1,
                    'display_in_portal' => 1,
                    'agency_id' => $this->session->agency_id,
                    'created_by' => $this->session->aua_id
                );
                $this->jcclass->insert_log($params_job_log);

                $this->session->set_flashdata('console_api_integ_success', 1);
                $console_surcharge = 3;
                $variation_reason = 9; // Console Fee
                $variation_scope = 1; // property
                $variation_type = 2; // surcharge

                // check if it already exist
                $apv_sql = $this->db->query("
                SELECT COUNT(`id`) AS apv_count
                FROM `agency_price_variation`
                WHERE `agency_id` = {$this->session->agency_id}
                AND `type` = {$variation_type}
                AND `amount` = {$console_surcharge}
                AND `reason` = {$variation_reason}
                AND `scope` = {$variation_scope}
                ");

                if( $apv_sql->row()->apv_count == 0 ){

                    // insert agency price variation
                    $insert_data = array(
                        'agency_id' => $this->session->agency_id,
                        'type' => $variation_type,
                        'amount' => $console_surcharge,
                        'reason' => $variation_reason,
                        'scope' => $variation_scope,
                        'created_date' => date('Y-m-d H:i:s')
                    );
                    $this->db->insert('agency_price_variation', $insert_data);

                }

                //Insert agency Log
                $details_log = "Accepted Console surcharge of $".number_format($console_surcharge,2)." per property annually";
                $params_job_log = array(
                    'title' => 90, // Console API
                    'details' => $details_log,
                    'display_in_vad' => 1,
                    'display_in_portal' => 1,
                    'agency_id' => $this->session->agency_id,
                    'created_by' => $this->session->aua_id
                );
                $this->jcclass->insert_log($params_job_log);

                //send "surcharge accepted" email
                $send_email_params_obj = (object) [
                    'agency_id' => $this->session->agency_id,
                    'agency_user' => $this->session->aua_id
                ];
                $this->send_surcharge_accepted_email($send_email_params_obj);

                //send email notification
                $this->console_connection_notification_email($agency_id);

            }            

        }        

    }

    // "surcharge accepted" email
    public function send_surcharge_accepted_email($params_obj){

        $agency_id = $params_obj->agency_id;
        $agency_user = $params_obj->agency_user;

        // get agency name
        $agency_sql = $this->db->query("
        SELECT `agency_name` 
        FROM `agency`
        WHERE `agency_id` = {$agency_id}
        ");
        $agency_row = $agency_sql->row();
        $agency_name = $agency_row->agency_name;

        // get agency user
        $aua_sql = $this->db->query("
        SELECT `fname`, `lname` 
        FROM `agency_user_accounts`
        WHERE `agency_user_account_id` = {$agency_user}
        ");
        $aua_row = $aua_sql->row();
        $agency_user_full_name = "{$aua_row->fname} {$aua_row->lname}";

        // email settings
        $email_config = Array(
            'mailtype' => 'html',
            'charset' => 'utf-8'
        );
        $this->email->initialize($email_config);
        $this->email->clear(TRUE);
        $this->email->from($this->config->item('sats_info_email'), 'Smoke Alarm Testing Services');
        $this->email->to($this->config->item('sats_info_email'));
        $this->email->bcc('vanessah@sats.com.au'); // BCC to ness

        $this->email->subject('Console Integration fee');

        $email_body = "
        <p>
            Hi Team,
        </p>
        <p>
        {$agency_user_full_name} from {$agency_name} has accepted the Console Surcharge. Please set an agency level variation of $3.00 per property. Type is 'Surcharge', Scope is 'Property', reason is 'Platform Cost' and Display on is 'Invoice and Agency Portal'
        </p>
        <p>
            Regards,<br />
            The Devs
        </p>
        ";

        $this->email->message($email_body);

        // send email
        $this->email->send();

    }

    public function console_connection_notification_email($agency_id){

        $this->load->model('profile_model');
        
        $agency_info = $this->profile_model->get_agency($agency_id);
		$agency_name = $agency_info->agency_name;

        // email settings
        $email_config = Array(
            'mailtype' => 'html',
            'charset' => 'utf-8'
        );

        $html_content  = "
        <p>
            Hi Team,
        </p>
        <p>
            {$agency_name} has connected to Console Cloud, please confirm all properties are connected, and webhooks are processed.
        </p>
        <p>
            Regards,<br />
            The Devs
        </p>
        ";

		$this->email->to(make_email('data'));
        $this->email->subject("Agency Connects to Console Cloud");
        $this->email->message($html_content);
        $this->email->send();

    }


    public function log_user_who_accepted_terms(){

        $title = 90; // Console API
		$details = "{agency_user:{$this->session->aua_id}} has accepted the <a href='javascript:void' class='view_console_terms'>terms and conditions</a> for integrating Console";

		$params = array(
			'title' => $title,
			'details' => $details,
			'display_in_portal' => 1,
			'display_in_vad' => 1,
			'agency_id' => $this->session->agency_id,
			'created_by' => $this->session->aua_id
		);
		$this->jcclass->insert_log($params);

    }

    
}