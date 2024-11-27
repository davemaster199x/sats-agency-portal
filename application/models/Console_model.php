<?php
class Console_model extends CI_Model {

    private $api_gateway;
    private $client_id;
    private $secret;

	public function __construct(){
        $this->load->database();
        
        if( ENVIRONMENT == 'production' ){ // live    
            
            if( $this->config->item('country') == 1 ){ // AU

                $this->api_gateway = 'https://api.console.com.au';
                $this->client_id = 'partner_sats';
                $this->secret = 'mZiUl5IUE2hCha49NSNL14MhABbm9M';                  

            }else if( $this->config->item('country') == 2 ){ // NZ

                $this->api_gateway = 'https://api.console.com.au';
                $this->client_id = 'partner_sats_nz';
                $this->secret = 'fvDUAh0UPQ8wa9cE4383Ke6ZBmKKxcjr';                 

            }  

        }else{ // dev
            
            // sandbox test
            $this->api_gateway = 'https://sandbox-apigw.saas-uat.console.com.au';
            $this->client_id = 'partner_sats';
            $this->secret = 'password';

        }
        
    }
	
	public function verify_integration($api_key) {

        if( $api_key != '' ){

            // init curl object        
            $ch = curl_init();

            $token_url = "{$this->api_gateway}/integration/v1/integrations/_verify";
            $client_id = $this->client_id;
            $secret = $this->secret;    

            $authorization = base64_encode("$client_id:$secret");
            $header = array("Authorization: Basic {$authorization}", "API-Key: {$api_key}","Content-Type: application/json");        

            $optArray = array(
                CURLOPT_URL => $token_url,
                CURLOPT_HTTPHEADER => $header,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true
            );

            // apply those options
            curl_setopt_array($ch, $optArray);

            // execute request and get response
            $result = curl_exec($ch);
            $result_json = json_decode($result);
            return json_encode($result_json);

        }                

    }
    
    public function get_integration($api_key) {

        if( $api_key != '' ){

            // init curl object        
            $ch = curl_init();

            $token_url = "{$this->api_gateway}/integration/v1/integrations/_current";
            $client_id = $this->client_id;
            $secret = $this->secret;    

            $authorization = base64_encode("$client_id:$secret");
            $header = array("Authorization: Basic {$authorization}", "API-Key: {$api_key}","Content-Type: application/json");        

            $optArray = array(
                CURLOPT_URL => $token_url,
                CURLOPT_HTTPHEADER => $header,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_RETURNTRANSFER => true
            );

            // apply those options
            curl_setopt_array($ch, $optArray);

            // execute request and get response
            $result = curl_exec($ch);
            $result_json = json_decode($result);
            return $result_json;

        }                

    }
		
}
