<?php

class Property_tree_model extends CI_Model
{

    private $api_gateway;
    private $application_key;
    private $subscription_key;
    private $request_limit;
    private $sleep_interval_sec;

    public function __construct(){

        $this->load->database();

        // AU and NZ used the same live/production keys
        if( ENVIRONMENT == 'production' ){ // LIVE

            $this->api_gateway = 'https://api.propertytree.io';
            $this->application_key = '3941b249-5113-4d49-9c38-f1bf6386cc35';
            $this->subscription_key = '8e5d8cb6af5f41bf8408c40de41b8d82';            

        }else{ // DEV

            $this->api_gateway = 'https://uatapi.propertytree.io';
            $this->application_key = '246f503a-7487-4d09-8ef8-2f1bd8c69fd4';
            $this->subscription_key = '3e9b29d41df3414cad93b9b043e52ec6';                                                

        }

        $this->request_limit = 240; //  PropertyTree API request limit
        $this->sleep_interval_sec = 60; // delay 1 minute    

    }

    public function getAccessToken($params){

        $agency_id = $params['agency_id'];
        $api_id = ( $params['api_id'] != '' )?$params['api_id']:4; // default is Palace

        if( $agency_id > 0 ){

            // get Pme tokens
                $sel_query = "
                access_token,
                expiry,
                refresh_token
            ";
            $this->db->select($sel_query);
            $this->db->from('agency_api_tokens');
            $this->db->where('agency_id', $agency_id);
            $this->db->where('api_id', $api_id);
            $pme_sql = $this->db->get();
            $pme_row = $pme_sql->row();

            $access_token = $pme_row->access_token;

            return $access_token;

        }        

    }

    public function get_all_properties($agency_id) {  

        $api_id = 3; // Property Tree

        // API request limit solution
        $req_limit_params = array(
            'api_id' => $api_id,
            'request_limit' => $this->request_limit,
            'sleep_interval_sec' => $this->sleep_interval_sec,
            'agency_id' => $agency_id
        );
        $this->api_request_limit_counter_and_delay($req_limit_params);

        // get access token        
        $pme_params = array(
            'agency_id' => $agency_id,
            'api_id' => $api_id
        );
        $access_token = $this->getAccessToken($pme_params);             
        
        $end_points = "{$this->api_gateway}/residentialproperty/v1/Properties";

        $curl = curl_init();

        // HTTP headers
        $http_header = array(
            "Authorization: Bearer {$access_token}",
            "Content-Type: application/json"
        );

        // API call
        $curl_opt = array(
            CURLOPT_URL => $end_points,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $http_header
        );

        curl_setopt_array( $curl, $curl_opt );

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response);

    }

    public function get_property($property_id) {  

        $api_id = 3; // Property Tree

        if( $property_id > 0 ){            

            // get agency ID from property 
            $prop_sql = $this->db->query("
            SELECT `agency_id`
            FROM `property`
            WHERE `property_id` = {$property_id}
            ");
            $prop_row = $prop_sql->row();
            $agency_id = $prop_row->agency_id;

            // API request limit solution
            $req_limit_params = array(                
                'api_id' => $api_id,
                'request_limit' => $this->request_limit,
                'sleep_interval_sec' => $this->sleep_interval_sec,
                'agency_id' => $agency_id
            );
            $this->api_request_limit_counter_and_delay($req_limit_params);

            // get access token            
            $pme_params = array(
                'agency_id' => $agency_id,
                'api_id' => $api_id
            );
            $access_token = $this->getAccessToken($pme_params);              
            
            // get API property ID
            $crm_connected_prop_sql_str = "
            SELECT `api_prop_id`
            FROM `api_property_data`
            WHERE `crm_prop_id` = {$property_id}
            AND `api` = {$api_id}
            ";
            $crm_connected_prop_sql = $this->db->query($crm_connected_prop_sql_str);
            $crm_connected_prop_row = $crm_connected_prop_sql->row();
            $api_prop_id = $crm_connected_prop_row->api_prop_id;
            
            $end_points = "{$this->api_gateway}/residentialproperty/v1/Properties/{$api_prop_id}";

            $curl = curl_init();

            // HTTP headers
            $http_header = array(
                "Authorization: Bearer {$access_token}",
                "Content-Type: application/json"
            );

            // API call
            $curl_opt = array(
                CURLOPT_URL => $end_points,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => $http_header
            );

            curl_setopt_array( $curl, $curl_opt );

            $response = curl_exec($curl);
            curl_close($curl);

            return json_decode($response);

        }        

    }

    public function get_tenant($agency_id,$tenant_id){  
        
        $api_id = 3; // Property Tree

        // API request limit solution
        $req_limit_params = array(
            'api_id' => $api_id,
            'request_limit' => $this->request_limit,
            'sleep_interval_sec' => $this->sleep_interval_sec,
            'agency_id' => $agency_id
        );
        $this->api_request_limit_counter_and_delay($req_limit_params);

        // get access token
        $pme_params = array(
            'agency_id' => $agency_id,
            'api_id' => $api_id
        );
        $access_token = $this->getAccessToken($pme_params);             
        
        $end_points = "{$this->api_gateway}/residentialproperty/v1/Tenancies/{$tenant_id}";

        $curl = curl_init();

        // HTTP headers
        $http_header = array(
            "Authorization: Bearer {$access_token}",
            "Content-Type: application/json"
        );

        // API call
        $curl_opt = array(
            CURLOPT_URL => $end_points,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $http_header
        );

        curl_setopt_array( $curl, $curl_opt );

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response);

    }  
    
    public function get_property_tree_auth_keys(){            
        
        $end_points = "{$this->api_gateway}/apikey/v1/application_keys/{$this->application_key}";

        $curl = curl_init();

        // HTTP headers
        $http_header = array(
            "Ocp-Apim-Subscription-Key: {$this->subscription_key}"
        );

        // API call
        $curl_opt = array(
            CURLOPT_URL => $end_points,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $http_header
        );

        curl_setopt_array( $curl, $curl_opt );

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response);

    }

    public function get_agency_using_auth_key($access_token){   
        
        $end_points = "{$this->api_gateway}/residentialproperty/v1/Agencies";

        $curl = curl_init();

        // HTTP headers
        $http_header = array(
            "Authorization: Bearer {$access_token}",
            "Content-Type: application/json"
        );

        // API call
        $curl_opt = array(
            CURLOPT_URL => $end_points,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $http_header
        );

        curl_setopt_array( $curl, $curl_opt );

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response);

    }

    public function get_property_tree_agent_by_id($params) {

        $property_id = $params['property_id'];
        $agent_id = $params['agent_id'];
        
        $api_id = 3; // Property Tree 

        if( $property_id > 0 ){            

            // get agency ID from property 
            $prop_sql = $this->db->query("
            SELECT `agency_id`
            FROM `property`
            WHERE `property_id` = {$property_id}
            ");
            $prop_row = $prop_sql->row();
            $agency_id = $prop_row->agency_id;

            // API request limit solution
            $req_limit_params = array(
                'api_id' => $api_id,
                'request_limit' => $this->request_limit,
                'sleep_interval_sec' => $this->sleep_interval_sec,
                'agency_id' => $agency_id
            );
            $this->api_request_limit_counter_and_delay($req_limit_params);

            // get access token
            $pme_params = array(
                'agency_id' => $agency_id,
                'api_id' => $api_id
            );
            $access_token = $this->getAccessToken($pme_params);   
            
            //echo "api_prop_json: <br />";
            $end_points = "{$this->api_gateway}/residentialproperty/v1/Agents/{$agent_id}";
            //echo "<br />";

            $curl = curl_init();

            // HTTP headers
            $http_header = array(
                "Authorization: Bearer {$access_token}",
                "Content-Type: application/json"
            );

            // API call
            $curl_opt = array(
                CURLOPT_URL => $end_points,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => $http_header
            );

            curl_setopt_array( $curl, $curl_opt );

            $response = curl_exec($curl);
            $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            /*
            echo "response: <br />";
            echo "<pre>";
            print_r($response);
            echo "</pre>";
            */
            curl_close($curl);

            return array(
                'raw_response' => $response,
                'json_decoded_response' => json_decode($response),
                'http_status_code' => $responseCode
            );

        }        

    }

    // residential API rate limit, 240 request per minute
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

    public function get_agents($agency_id){

        $api_id = 3; // Property Tree

        // API request limit solution
        $req_limit_params = array(
            'api_id' => $api_id,
            'request_limit' => $this->request_limit,
            'sleep_interval_sec' => $this->sleep_interval_sec,
            'agency_id' => $agency_id
        );
        $this->api_request_limit_counter_and_delay($req_limit_params);        

        // get access token        
        $pme_params = array(
            'agency_id' => $agency_id,
            'api_id' => $api_id
        );
        $access_token = $this->getAccessToken($pme_params);             
        
        $end_points = "{$this->api_gateway}/residentialproperty/v1/Agents";

        // HTTP headers
        $http_header = array(
            "Authorization: Bearer {$access_token}"
        );

        // API call
        $curl = curl_init();
        
        $curl_opt = array(
            CURLOPT_URL => $end_points,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $http_header
        );
        
        curl_setopt_array( $curl, $curl_opt );

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE); // HTTP status code

        curl_close($curl);

        return (object) [
            'json_decoded_response' => json_decode($response),
            'httpcode' => $httpcode
        ];
        
    }

    public function get_creditors($agency_id){

        $api_id = 3; // Property Tree

        // API request limit solution
        $req_limit_params = array(
            'api_id' => $api_id,
            'request_limit' => $this->request_limit,
            'sleep_interval_sec' => $this->sleep_interval_sec,
            'agency_id' => $agency_id
        );
        $this->api_request_limit_counter_and_delay($req_limit_params);        

        // get access token        
        $pme_params = array(
            'agency_id' => $agency_id,
            'api_id' => $api_id
        );
        $access_token = $this->getAccessToken($pme_params);             
        
        $end_points = "{$this->api_gateway}/residentialproperty/v1/Creditors";

        // HTTP headers
        $http_header = array(
            "Authorization: Bearer {$access_token}"
        );

        // API call
        $curl = curl_init();
        
        $curl_opt = array(
            CURLOPT_URL => $end_points,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $http_header
        );
        
        curl_setopt_array( $curl, $curl_opt );

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE); // HTTP status code

        curl_close($curl);

        return (object) [
            'json_decoded_response' => json_decode($response),
            'httpcode' => $httpcode
        ];
        
    }

    public function get_creditor($params_obj){

        $agency_id = $params_obj->agency_id;
        $creditor_id = $params_obj->creditor_id; 

        $api_id = 3; // Property Tree

        // API request limit solution
        $req_limit_params = array(
            'api_id' => $api_id,
            'request_limit' => $this->request_limit,
            'sleep_interval_sec' => $this->sleep_interval_sec,
            'agency_id' => $agency_id
        );
        $this->api_request_limit_counter_and_delay($req_limit_params);        

        // get access token        
        $pme_params = array(
            'agency_id' => $agency_id,
            'api_id' => $api_id
        );
        $access_token = $this->getAccessToken($pme_params);             
        
        $end_points = "{$this->api_gateway}/residentialproperty/v1/Creditors/{$creditor_id}";

        // HTTP headers
        $http_header = array(
            "Authorization: Bearer {$access_token}"
        );

        // API call
        $curl = curl_init();
        
        $curl_opt = array(
            CURLOPT_URL => $end_points,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $http_header
        );
        
        curl_setopt_array( $curl, $curl_opt );

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE); // HTTP status code

        curl_close($curl);

        return (object) [
            'json_decoded_response' => json_decode($response),
            'httpcode' => $httpcode
        ];
        
    }

    public function get_accounts($agency_id){

        $api_id = 3; // Property Tree

        // API request limit solution
        $req_limit_params = array(
            'api_id' => $api_id,
            'request_limit' => $this->request_limit,
            'sleep_interval_sec' => $this->sleep_interval_sec,
            'agency_id' => $agency_id
        );
        $this->api_request_limit_counter_and_delay($req_limit_params);        

        // get access token        
        $pme_params = array(
            'agency_id' => $agency_id,
            'api_id' => $api_id
        );
        $access_token = $this->getAccessToken($pme_params);             
        
        $end_points = "{$this->api_gateway}/residentialproperty/v1/Accounts";

        // HTTP headers
        $http_header = array(
            "Authorization: Bearer {$access_token}"
        );

        // API call
        $curl = curl_init();
        
        $curl_opt = array(
            CURLOPT_URL => $end_points,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $http_header
        );
        
        curl_setopt_array( $curl, $curl_opt );

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE); // HTTP status code

        curl_close($curl);

        return (object) [
            'json_decoded_response' => json_decode($response),
            'httpcode' => $httpcode
        ];
        
    }


    public function property_compliance_categories($agency_id){

        $api_id = 3; // Property Tree

        // API request limit solution
        $req_limit_params = array(
            'api_id' => $api_id,
            'request_limit' => $this->request_limit,
            'sleep_interval_sec' => $this->sleep_interval_sec,
            'agency_id' => $agency_id
        );
        $this->api_request_limit_counter_and_delay($req_limit_params);        

        // get access token        
        $pme_params = array(
            'agency_id' => $agency_id,
            'api_id' => $api_id
        );
        $access_token = $this->getAccessToken($pme_params);             
        
        $end_points = "{$this->api_gateway}/residentialproperty/v1/PropertyComplianceCategory";

        // HTTP headers
        $http_header = array(
            "Authorization: Bearer {$access_token}"
        );

        // API call
        $curl = curl_init();
        
        $curl_opt = array(
            CURLOPT_URL => $end_points,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $http_header
        );
        
        curl_setopt_array( $curl, $curl_opt );

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE); // HTTP status code

        curl_close($curl);

        return (object) [
            'json_decoded_response' => json_decode($response),
            'httpcode' => $httpcode
        ];
        
    }
    
    
}
