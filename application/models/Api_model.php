<?php
class Api_model extends CI_Model {

	public function __construct(){
        $this->load->database();
        
        $this->clientId = $this->config->item('PME_CLIENT_ID');
        $this->clientSecret = $this->config->item('PME_CLIENT_SECRET');
        $this->clientScope = $this->config->item('PME_CLIENT_Scope');
        $this->urlCallBack = urlencode($this->config->item('PME_URL_CALLBACK'));
        $this->accessTokenUrl = $this->config->item('PME_ACCESS_TOKEN_URL');
        $this->authorizeUrl = $this->config->item('PME_AUTHORIZE_URL');

        $this->load->model('palace_model');
        $this->load->model('pme_model');
    }
	
	public function getPmeAccessToken($authorization_code) {

        $token_url = $this->accessTokenUrl;
        $client_id = $this->clientId;
        $client_secret = $this->clientSecret;
        $callback_uri = $this->urlCallBack;

        $authorization = base64_encode("$client_id:$client_secret");
        $header = array("Authorization: Basic {$authorization}","Content-Type: application/x-www-form-urlencoded");
        $content = "grant_type=authorization_code&code=$authorization_code&redirect_uri=$callback_uri";

        $curl_opt = array(
            CURLOPT_URL => $token_url,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $content
        );

        $curl = curl_init();
        
        curl_setopt_array($curl, $curl_opt);
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;

    }


    public function refreshPmeToken($refresh_token) {

        $token_url = $this->accessTokenUrl;
        $client_id = $this->clientId;
        $client_secret = $this->clientSecret;
        $callback_uri = $this->urlCallBack;

        $authorization = base64_encode("$client_id:$client_secret");
        $header = array("Authorization: Basic {$authorization}","Content-Type: application/x-www-form-urlencoded");
        $content = "grant_type=refresh_token&refresh_token=$refresh_token&redirect_uri=$callback_uri";

        $curl_opt = array(
            CURLOPT_URL => $token_url,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $content
        );

        $curl = curl_init();
        
        curl_setopt_array($curl, $curl_opt);
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;

    }

    public function pme_auth_link(){

        return $this->config->item('PME_AUTHORIZE_URL') . "?response_type=code&state=abc123&client_id=".$this->config->item('PME_CLIENT_ID')."&scope=".$this->config->item('PME_CLIENT_Scope')."&redirect_uri=".$this->config->item('PME_URL_CALLBACK');

    }

    public function get_agency_api($params)
    {

        if (isset($params['sel_query'])) {
            $sel_query = $params['sel_query'];
        } else {
            $sel_query = '*';
        }

        $this->db->select($sel_query);
        $this->db->from('`agency_api`');
		
        // filter
        if ( $params['agency_api_id'] > 0 ) {
            $this->db->where('`agency_api_id`', $params['agency_api_id']);
        }

        if( $params['active'] > 0 ){
			$this->db->where('`active`', $params['active']);
		}

      	// custom filter
        if( isset($params['custom_where']) ){
             $this->db->where($params['custom_where']);
        }
		
		// custom filter arr
        if( isset($params['custom_where_arr']) ){
			foreach( $params['custom_where_arr'] as $index => $custom_where ){
				if( $custom_where != '' ){
					$this->db->where($custom_where);
				}				
			}              
        }		
		
		// group by
        if( isset($params['group_by']) && $params['group_by'] != '' ){
              $this->db->group_by($params['group_by']);
        }		

        // sort
        if (isset($params['sort_list'])) {
            foreach ($params['sort_list'] as $sort_arr) {
                if ($sort_arr['order_by'] != "" && $sort_arr['sort'] != '') {
                    $this->db->order_by($sort_arr['order_by'], $sort_arr['sort']);
                }
            }
        }
		
		// custom filter
        if( isset($params['custom_sort']) ){
              $this->db->order_by($params['custom_sort']);
        }

        // limit
		if( isset($params['limit']) && $params['limit'] > 0 ){
			$this->db->limit( $params['limit'], $params['offset']);
		}	

		$query = $this->db->get();
		if( isset($params['display_query']) && $params['display_query'] == 1 ){
			echo $this->db->last_query();
		}
		
		return $query;
		
    }


    public function get_agency_api_integration($params)
    {

        if (isset($params['sel_query'])) {
            $sel_query = $params['sel_query'];
        } else {
            $sel_query = '*';
        }

        $this->db->select($sel_query);
        $this->db->from('`agency_api_integration` AS agen_api_int');
        $this->db->join('`agency_api` AS agen_api', 'agen_api_int.`connected_service` = agen_api.`agency_api_id`', 'left');

        // set joins
		if( $params['join_table'] > 0 ){
			
			foreach(  $params['join_table'] as $join_table ){
				
				if( $join_table == 'agency' ){
					$this->db->join('`agency` AS a', 'agen_api_int.`agency_id` = a.`agency_id`', 'left');
                }
                			
			}			
			
		}

        // custom joins
		if( isset($params['custom_joins']) && $params['custom_joins'] != '' ){
			$this->db->join($params['custom_joins']['join_table'],$params['custom_joins']['join_on'], $params['custom_joins']['join_type']);
        }
		
        // filter
        if ( $params['api_integration_id'] > 0 ) {
            $this->db->where('agen_api_int.`api_integration_id`', $params['api_integration_id']);
        }

        if ( is_numeric($params['active']) ) {
            $this->db->where('agen_api_int.`active`', $params['active']);
        }

        if ( $params['agency_id'] > 0 ) {
            $this->db->where('agen_api_int.`agency_id`', $params['agency_id']);
        }

        if ( $params['api_id'] > 0 ) {
            $this->db->where('agen_api_int.`connected_service`', $params['api_id']);
        }

      	// custom filter
        if( isset($params['custom_where']) ){
             $this->db->where($params['custom_where']);
        }
		
		// custom filter arr
        if( isset($params['custom_where_arr']) ){
			foreach( $params['custom_where_arr'] as $index => $custom_where ){
				if( $custom_where != '' ){
					$this->db->where($custom_where);
				}				
			}              
        }		
		
		// group by
        if( isset($params['group_by']) && $params['group_by'] != '' ){
              $this->db->group_by($params['group_by']);
        }		

        // sort
        if (isset($params['sort_list'])) {
            foreach ($params['sort_list'] as $sort_arr) {
                if ($sort_arr['order_by'] != "" && $sort_arr['sort'] != '') {
                    $this->db->order_by($sort_arr['order_by'], $sort_arr['sort']);
                }
            }
        }
		
		// custom filter
        if( isset($params['custom_sort']) ){
              $this->db->order_by($params['custom_sort']);
        }

        // limit
		if( isset($params['limit']) && $params['limit'] > 0 ){
			$this->db->limit( $params['limit'], $params['offset']);
		}	

		$query = $this->db->get();
		if( isset($params['display_query']) && $params['display_query'] == 1 ){
			echo $this->db->last_query();
		}
		
		return $query;
		
    }

    public function get_agency_api_tokens($params)
    {

        if (isset($params['sel_query'])) {
            $sel_query = $params['sel_query'];
        } else {
            $sel_query = '*';
        }

        $this->db->select($sel_query);
        $this->db->from('`agency_api_tokens`');
		
        // filter
        if ( $params['agency_api_token_id'] > 0 ) {
            $this->db->where('`agency_api_token_id`', $params['agency_api_token_id']);
        }

        if ( $params['api_id'] > 0 ) {
            $this->db->where('`api_id`', $params['api_id']);
        }

        if ( $params['agency_id'] > 0 ) {
            $this->db->where('`agency_id`', $params['agency_id']);
        }

        if( $params['active'] > 0 ){
			$this->db->where('`active`', $params['active']);
		}

      	// custom filter
        if( isset($params['custom_where']) ){
             $this->db->where($params['custom_where']);
        }
		
		// custom filter arr
        if( isset($params['custom_where_arr']) ){
			foreach( $params['custom_where_arr'] as $index => $custom_where ){
				if( $custom_where != '' ){
					$this->db->where($custom_where);
				}				
			}              
        }		
		
		// group by
        if( isset($params['group_by']) && $params['group_by'] != '' ){
              $this->db->group_by($params['group_by']);
        }		

        // sort
        if (isset($params['sort_list'])) {
            foreach ($params['sort_list'] as $sort_arr) {
                if ($sort_arr['order_by'] != "" && $sort_arr['sort'] != '') {
                    $this->db->order_by($sort_arr['order_by'], $sort_arr['sort']);
                }
            }
        }
		
		// custom filter
        if( isset($params['custom_sort']) ){
              $this->db->order_by($params['custom_sort']);
        }

        // limit
		if( isset($params['limit']) && $params['limit'] > 0 ){
			$this->db->limit( $params['limit'], $params['offset']);
		}	

		$query = $this->db->get();
		if( isset($params['display_query']) && $params['display_query'] == 1 ){
			echo $this->db->last_query();
		}
		
		return $query;
		
    }


    public function get_integrated_api($property_id){

        $aat_sql_str = "
        SELECT 
            p.`property_id`,
            p.`address_1`,
            p.`address_2`,
            p.`address_3`,
            p.`state`,
            p.`postcode`,
        
            p.`propertyme_prop_id`,
            p.`palace_prop_id`,

            aa.`agency_api_id`          
        FROM `property` AS p
        LEFT JOIN `agency_api_tokens` AS aat ON  p.`agency_id` = aat.`agency_id`
        LEFT JOIN `agency_api_integration` AS aai ON  aai.`agency_id` = aat.`agency_id`
        LEFT JOIN `agency_api` AS aa ON aai.`connected_service` = aa.`agency_api_id`
        WHERE p.`property_id` = {$property_id}
        AND aai.`active` = 1
        AND aa.`active` = 1
        ";        

        $aat_sql = $this->db->query($aat_sql_str);

        $aat_row = $aat_sql->row();

        switch( $aat_row->agency_api_id ){

            case 1: // PMe
                $prop_api_id = $aat_row->propertyme_prop_id;
                $agency_api_icon = 'pme_connected.png';
            break;

            case 4: // Palace
                $prop_api_id = $aat_row->palace_prop_id;
                $agency_api_icon = 'palace_connected.png';
            break;

        }

        return array(
            'agency_api_id' => $aat_row->agency_api_id,
            'prop_api_id' => $prop_api_id,
            'agency_api_icon' => $agency_api_icon
        );

    }



    public function get_property_data($property_id){

        if( $property_id > 0 ){

            $get_integrated_api_data = $this->api_model->get_integrated_api($property_id);

            if( $get_integrated_api_data['prop_api_id'] != '' && $this->session->agency_id > 0 ){

                if( $get_integrated_api_data['agency_api_id'] == 1 ){ // PME

                    $pme_params = array(
                        'agency_id' => $this->session->agency_id,
                        'prop_id' => $get_integrated_api_data['prop_api_id']
                    );
                                
                    $pme_prop_json = $this->pme_model->get_property($pme_params);				
                    $pme_prop_json_dec = json_decode($pme_prop_json);		
                    
                    $api_prop_id = $pme_prop_json_dec->Id;
                    $api_prop_address = $pme_prop_json_dec->AddressText;

                } else if( $get_integrated_api_data['agency_api_id'] == 4 ){	// palace
                        
                    $palace_params = array(
                        'agency_id' => $this->session->agency_id,
                        'palace_prop_id' => $get_integrated_api_data['prop_api_id']
                    );	
            
                    $palace_prop_json = $this->palace_model->get_property($palace_params);				
                    $palace_prop_json_dec = json_decode($palace_prop_json);		                                    

                    $api_prop_id = $palace_prop_json_dec->PropertyCode;
                    $api_prop_address = "{$palace_prop_json_dec->PropertyUnit} {$palace_prop_json_dec->PropertyAddress1} {$palace_prop_json_dec->PropertyAddress2} {$palace_prop_json_dec->PropertyAddress3} {$palace_prop_json_dec->PropertyFeatures->PropertyPostCode} {$palace_prop_json_dec->PropertyAddress4}";

                }

            } 
            
            return array(
                'agency_api_id' => $get_integrated_api_data['agency_api_id'],
                'agency_api_icon' => $get_integrated_api_data['agency_api_icon'],
                'api_prop_id' => $api_prop_id,
                'api_prop_address' => $api_prop_address
            );

        }        

    }

    public function add_agency_api_integration($data)
	{
		return $this->db->insert('agency_api_integration', $data);
	}

		
		
}
