<?php

class Palace_model extends CI_Model
{

    public function __construct()
    {
        $this->load->database();
    }

    public function call_end_points($params, $is_json = false)
    {
        $curl = curl_init();

        // HTTP headers

        if ($is_json) {
            $http_header = array(
                "Authorization: Basic {$params['access_token']}",
                "Content-Type: application/json"
            );
        }else {
            $http_header = array(
                "Authorization: Basic {$params['access_token']}",
                "Content-Type: application/xml"
            );
        }

        curl_setopt_array($curl, array(
          CURLOPT_URL => $params['end_points'],
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => $http_header,
          CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12',
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $xml_snippet = simplexml_load_string( $response );
        $json_convert = json_encode( $xml_snippet );
        $json = json_decode( $json_convert );
        if ($is_json) {
            return $response;
        }else {
            return (array)($json);
        }

    }


    public function call_end_points_v2($params)
    {

        $curl = curl_init();

        // HTTP headers
        $http_header = array(
            "Authorization: Basic {$params['access_token']}",
            "Content-Type: application/json"
        );

        // curl options
        $curl_opt = array(
            CURLOPT_URL => $params['end_points'],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $http_header
        );     
        
        // parameters
        if( count($params['param_data']) > 0 ){  

            $curl_opt[CURLOPT_POST] = true;                                                        
		    $data_string = json_encode($params['param_data']);  
            $curl_opt[CURLOPT_POSTFIELDS] = $data_string;
            
        }  
              

        // display - debug
        if( $params['display'] == 1 ){
            print_r($curl_opt);
        }

        curl_setopt_array($curl, $curl_opt); // curl options        
        $response = curl_exec($curl); // get plain response
        $response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE); // get status code
        
        curl_close($curl);

        //$response_decode = json_decode($response);

        return array(
            'response' => $response,
            'response_code' => $response_code
        );        
		
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

    public function get_all_palace_owner($agency_id) {

        $api_id = 4; // Palace
        $pme_params = array(
            'agency_id' => $agency_id,
            'api_id' => $api_id
        );
        $access_token = $this->getAccessToken($pme_params);
        $system_use = $this->get_system_use_url($agency_id);
        $end_points = $system_use."/Service.svc/RestService/ViewAllDetailedOwner";

        // call end point
        $pme_params = array(
            'access_token' => $access_token,
            'end_points' => $end_points
        );

        $palaceList = $this->call_end_points($pme_params);

        return $palaceList['ViewAllDetailedOwner'];
    }

    public function get_all_palace_supplier($agency_id) {

      $api_id = 4; // Palace
      $pme_params = array(
          'agency_id' => $agency_id,
          'api_id' => $api_id
      );
      $access_token = $this->getAccessToken($pme_params);
      $system_use = $this->get_system_use_url($agency_id);
      $end_points = $system_use."/Service.svc/RestService/v2ViewAllDetailedSupplier/JSON";

      // call end point
      $pme_params = array(
          'access_token' => $access_token,
          'end_points' => $end_points
      );
      $palaceList = $this->call_end_points($pme_params, true);

      $suppList = json_decode($palaceList);

      $resArr = array();
      foreach ($suppList as $key => $value) {
          if ($value->SupplierArchived == false) {
              array_push($resArr, $suppList[$key]);
          }
      }

      return $resArr;

    }

    public function get_all_palace_agent($agency_id) {

      $api_id = 4; // Palace
      $pme_params = array(
          'agency_id' => $agency_id,
          'api_id' => $api_id
      );
      $access_token = $this->getAccessToken($pme_params);
      $system_use = $this->get_system_use_url($agency_id);
      $end_points = $system_use."/Service.svc/RestService/v2ViewAllDetailedAgent/JSON";

      // call end point
      $pme_params = array(
          'access_token' => $access_token,
          'end_points' => $end_points
      );
      $palaceList = $this->call_end_points($pme_params, true);

      $suppList = json_decode($palaceList);

      $resArr = array();
      foreach ($suppList as $key => $value) {
          if ($value->AgentArchived == false) {
              array_push($resArr, $suppList[$key]);
          }
      }

      return $resArr;

    }

    public function get_palace_supplier_by_id($agency_id, $code) {

      $api_id = 4; // Palace
      $pme_params = array(
          'agency_id' => $agency_id,
          'api_id' => $api_id
      );
      $access_token = $this->getAccessToken($pme_params);
      $system_use = $this->get_system_use_url($agency_id);
      $end_points = $system_use."/Service.svc/RestService/v2ViewAllDetailedSupplier/JSON";

      // call end point
      $pme_params = array(
          'access_token' => $access_token,
          'end_points' => $end_points
      );
      $palaceList = $this->call_end_points($pme_params, true);

      $suppList = json_decode($palaceList);

      $resArr = array();
      foreach ($suppList as $key => $value) {
          if ($value->SupplierCode == $code) {
              array_push($resArr, $suppList[$key]);
          }
      }

      return $resArr;

    }

    public function get_palace_agent_by_id($agency_id, $code) {

      $api_id = 4; // Palace
      $pme_params = array(
          'agency_id' => $agency_id,
          'api_id' => $api_id
      );
      $access_token = $this->getAccessToken($pme_params);
      $system_use = $this->get_system_use_url($agency_id);
      $end_points = $system_use."/Service.svc/RestService/v2ViewAllDetailedAgent/JSON";

      // call end point
      $pme_params = array(
          'access_token' => $access_token,
          'end_points' => $end_points
      );
      $palaceList = $this->call_end_points($pme_params, true);

      $suppList = json_decode($palaceList);

      $resArr = array();
      foreach ($suppList as $key => $value) {
          if ($value->AgentCode == $code) {
              array_push($resArr, $suppList[$key]);
          }
      }

      return $resArr;

    }

    public function get_supplier_id_by_agency_id($agencyId) {
        $q_supp = "
            SELECT 
                `palace_supplier_id`
            FROM `agency`
            WHERE `agency_id` = {$agencyId} 
        ";
        $get_supp_id = $this->db->query($q_supp);
        $id = $get_supp_id->row();

        return $id;
    }

    public function get_agent_id_by_agency_id($agencyId) {
        $q_supp = "
            SELECT 
                `palace_agent_id`
            FROM `agency`
            WHERE `agency_id` = {$agencyId} 
        ";
        $get_agent_id = $this->db->query($q_supp);
        $id = $get_agent_id->row();

        return $id;
    }

    public function get_all_palace_diary($agency_id) {

      $api_id = 4; // Palace
      $pme_params = array(
          'agency_id' => $agency_id,
          'api_id' => $api_id
      );
      $access_token = $this->getAccessToken($pme_params);
      $system_use = $this->get_system_use_url($agency_id);
      $end_points = $system_use."/Service.svc/RestService/v2ViewAllDetailedDiaryGroup/JSON";

      // call end point
      $pme_params = array(
          'access_token' => $access_token,
          'end_points' => $end_points
      );
      $diaryList = $this->call_end_points($pme_params, true);

      $diaryList = json_decode($diaryList);

      $resArr = array();
      foreach ($diaryList as $key => $value) {
          array_push($resArr, $diaryList[$key]);
      }

      return $resArr;

    }

    public function get_palace_diary_by_id($agency_id, $code) {

      $api_id = 4; // Palace
      $pme_params = array(
          'agency_id' => $agency_id,
          'api_id' => $api_id
      );
      $access_token = $this->getAccessToken($pme_params);
      $system_use = $this->get_system_use_url($agency_id);
      $end_points = $system_use."/Service.svc/RestService/v2ViewAllDetailedDiaryGroup/JSON";

      // call end point
      $pme_params = array(
          'access_token' => $access_token,
          'end_points' => $end_points
      );
      $diaryList = $this->call_end_points($pme_params, true);

      $diaryList = json_decode($diaryList);

      $resArr = array();
      foreach ($diaryList as $key => $value) {
          if ($value->DiaryGroupCode == $code) {
              array_push($resArr, $diaryList[$key]);
          }
      }

      return $resArr;

    }

    public function get_diary_id_by_agency_id($agencyId) {
        $q_supp = "
            SELECT 
                `palace_diary_id`
            FROM `agency`
            WHERE `agency_id` = {$agencyId} 
        ";
        $get_diary_id = $this->db->query($q_supp);
        $id = $get_diary_id->row();

        return $id;
    }

    public function get_system_use_url($agencyId) {

        $sel_query = "system_use";
        $this->db->select($sel_query);
        $this->db->from('agency_api_tokens');
        $this->db->where('agency_id', $agencyId);
        $this->db->where('api_id', 4);
        $pme_sql = $this->db->get();
        $pme_row = $pme_sql->row();

        $system = $pme_row->system_use;
        if ($system == "Legacy" || is_null($system)) {
          $basePalace = $this->config->item('palace_api_base_legacy');
        }else {
          $basePalace = $this->config->item('palace_api_base_liquid');
        }
        return $basePalace;
    }

    public function get_api_getway($system) {

        if( $system == 'Liquid' ) { // new system

          $api_gateway = $this->config->item('palace_api_base_liquid');

        }else if ( $system == 'Legacy' ) { // old system

            $api_gateway = $this->config->item('palace_api_base_legacy');

        }

        return $api_gateway;

    }

    public function get_end_points($params)
    {

        $curl = curl_init();

        // HTTP headers
        $http_header = array(
            "Authorization: Basic {$params['access_token']}",
            "Content-Type: application/json"
        );

        // curl options
        $curl_opt = array(
            CURLOPT_URL => $params['end_points'],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $http_header
        );     
        
        // parameters
        if( count($params['param_data']) > 0 ){  

            $curl_opt[CURLOPT_POST] = true;                                                        
		    $data_string = json_encode($params['param_data']);  
            $curl_opt[CURLOPT_POSTFIELDS] = $data_string;
            
        }  
              

        // display - debug
        if( $params['display'] == 1 ){
            print_r($curl_opt);
        }

        curl_setopt_array($curl, $curl_opt);

        $response = curl_exec($curl);
        curl_close($curl);

        //$response_decode = json_decode($response);

        return $response;
        
		
    }

    public function get_property($params) {

     
        $agency_id = $params['agency_id'];
        $palace_prop_id = $params['palace_prop_id'];

        // get access token
        $api_id = 4; // Palace
        $pme_params = array(
            'agency_id' => $agency_id,
            'api_id' => $api_id
        );
        $access_token = $this->getAccessToken($pme_params);
        $system_use = $this->get_system_use_url($agency_id);        
        
        $end_points = "{$system_use}/Service.svc/RestService//v2DetailedProperty/JSON/{$palace_prop_id}";

        // call end point
        $pme_params = array(
            'access_token' => $access_token,
            'end_points' => $end_points
        );
        return $this->get_end_points($pme_params);              
        
    }
    
}
