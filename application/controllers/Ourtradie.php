<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('API_URL', 'https://tradie.ourtradie.com.au/api/');

class Ourtradie extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->model('ourtradie_model');
        //$this->load->model('api_model');
    }

    public function index(){
        $tokenArray = array();
        $agency_id  = $this->session->agency_id;
        $api_id = 6;

        $tokenArray = $this->ourtradie_model->getToken($agency_id, $api_id);

        if(empty($tokenArray)){
        $options = array(
            'client_id'		  => 'br6ucKvcPRqDNA1V2s7x',
            'client_secret'	  => 'd5YOJHb6EYRw5oypl73CJFWGLob5KB9A',
            'redirect_uri'		=> 'https://agencydev.sats.com.au/ourtradie'
            );

        $api = new OurtradieApi($options, $_REQUEST);
        $response = $api->authenticate();
        }

        if(!empty($_GET)){
            $this->getToken($_GET);
        }

        if(!empty($tokenArray)){
          $this->checkToken();
        }

    }

    public function getToken(){

        $unixtime 	= time();
		$expiry 	= date('Y-m-d H:i:s',strtotime('+3600 seconds'));
		$now 		= date("Y-m-d H:i:s",$unixtime);
        $agency_id  = $this->session->agency_id;

        $tokenArray = array();
        $tokenArray = $_GET;

		if(!empty($tokenArray['access_token'])){

			$access_token   = $tokenArray['access_token'];
			$refresh_token  = $tokenArray['refresh_token'];
			$created        = $now;

			$insert_data = array(
				'access_token'    => $access_token,
				'refresh_token'   => $refresh_token,
				'created'         => $created,
				'expiry'          => $expiry,
				'agency_id'		  => $agency_id,
				'api_id'          => 6,
				'connection_date' => $now
			);

			$status = $this->ourtradie_model->insertToken($insert_data);
            //redirect("/api/connections?list=true");

            $api = new OurtradieApi();

            $token = array('access_token' => $access_token);

            //GetAgencies
            $params = array(
                'Skip' 	 		=> 'No',
                'Count'     => 'No'
            );
            $agency = $api->query('GetAgencies', $params, '', $token, true);

            $data_agency = array();
            $data_agency = json_decode($agency, true);

            $data['agency_list'] = array_filter($data_agency, function ($v) {
            return $v !== 'OK';
            });

            $_SESSION['list'] = $data['agency_list'];
            redirect("/api/connections?list=true");

            /*
            print_r($data['agency_list']);
            echo "TEST";
            exit();
            */

		}
    }

    public function checkToken(){

        $unixtime 	= time();
        $now 		= date("Y-m-d H:i:s",$unixtime);

        $api_id = 6;
        $agency_id  = $this->session->agency_id;
        $token['token'] = $this->ourtradie_model->getToken($agency_id, $api_id);

        $created         = $token['token'][0]->created;
        $expiry          = $token['token'][0]->expiry;
        $expired         = strtotime($now) - strtotime($expiry);
        $tmp_refresh_token   = $token['token'][0]->refresh_token;
        $tmp_arr_refresh_token = explode("+/-]",$tmp_refresh_token);
        $refresh_token = $tmp_arr_refresh_token[0];

        //echo "====Expired: ".$expired;
        //echo $refresh_token;
        //echo $tmp_arr_refresh_token[1];

        //$refresh_token = "1654578cef286cf59e4dad1634129c56e42cfbe6-d7156da53a0ec07cd4970f76abb9def081ac61d9";

        if($expired > 0){

        $options = array(
            'grant_type'      => 'refresh_token',
            'refresh_token'   =>  $refresh_token,
            'client_id'		  => 'br6ucKvcPRqDNA1V2s7x',
            'client_secret'	  => 'd5YOJHb6EYRw5oypl73CJFWGLob5KB9A',
            'redirect_uri'	  => 'https://agencydev.sats.com.au/ourtradie/refreshToken'
            );

        $api = new OurtradieApi($options, $_REQUEST);
        $token = $refresh_token;

        $response = $api->refreshToken($token);

        if(!empty($response)){
            $access_token   = $response->access_token;
            $refresh_token  = $response->refresh_token;
            $expiry         = date('Y-m-d H:i:s',strtotime('+3600 seconds'));
            $created        = $now;

            $update_data = array(
                'access_token'    => $access_token,
                'refresh_token'   => $refresh_token."+/-]".$tmp_arr_refresh_token[1],
                'created'         => $created,
                'expiry'          => $expiry,
            );

            $this->ourtradie_model->updateToken($agency_id, $api_id, $update_data);
            
            redirect("/api/connections");
        }
        }
        else{
            //echo "Token not expired!";
            redirect("/api/connections");
        }
    }

    public function refreshToken(){
        $agency_id  = $this->session->agency_id;
        $api_id = 6;

        $unixtime = time();
        $now = date("Y-m-d H:i:s",$unixtime);

        $token['token'] = $this->ourtradie_model->getToken($agency_id, $api_id);
        $tmp_refresh_token   = $token['token'][0]->refresh_token;
        $tmp_arrref_refresh_token = explode("+/-]",$tmp_refresh_token);
        $ref_api_id = $tmp_arrref_refresh_token[1];

        $refTokenArray = array();
        $refTokenArray = $_GET;

        if(!empty($refTokenArray['access_token'])){
        $access_token   = $refTokenArray['access_token'];
        $refresh_token  = $refTokenArray['refresh_token'].$ref_api_id;
        $expiry         = date('Y-m-d H:i:s',strtotime('+3600 seconds'));
        $created        = $now;

        $update_data = array(
            'access_token'    => $access_token,
            'refresh_token'   => $refresh_token,
            'created'         => $created,
            'expiry'          => $expiry,
        );

        $status = $this->ourtradie_model->updateToken($agency_id, $api_id, $update_data);
        redirect("/api/connections");
        }
    }

    public function updateAgencyId(){
        $agency_id  = $this->session->agency_id;
        $api_agency_id = $this->input->get_post('agency_id');
        $api_id = 6;

        $token['token'] = $this->ourtradie_model->getToken($agency_id, $api_id);
        $refresh_token   = $token['token'][0]->refresh_token;

        $update_data = array(
            'refresh_token'    => $refresh_token."+/-]".$api_agency_id
        );

        $status = $this->ourtradie_model->updateAgencyId($agency_id, $api_id, $update_data);
        //echo $status;
        //exit();

        //Response data from Model
        if ($status) {

            //send email notification
            $this->ourtradie_connection_notification_email($agency_id);

            $this->session->set_flashdata(array('success_msg' => "Agency connected successfully", 'status' => 'success'));
            redirect("/api/connections?updated=true");
        }
    }

    public function ourtradie_connection_notification_email($agency_id){
        
        $this->load->model('profile_model');
        
        $agency_info = $this->profile_model->get_agency($agency_id);
		$agency_name = $agency_info->agency_name;

        $html_content  = "
        <p>
            Hi Team,
        </p>
        <p>
            {$agency_name} has connected to OurTradie, please confirm all properties are connected, and supplier is added.
        </p>
        <p>
            Regards,<br />
            The Devs
        </p>
        ";
        
		$this->email->to(make_email('data'));
        $this->email->subject("Agency Connects to OurTradie");
        $this->email->message($html_content);
        $this->email->send();

    }


}//endclass
