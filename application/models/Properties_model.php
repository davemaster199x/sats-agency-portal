<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Extends to MY_Model
 */
class Properties_model extends MY_Model
{
    public $table = 'property'; // Set the name of the table for this model.
    public $primary_key = 'property_id'; // Set the primary key
    
    // ...Or you can set an array with the fields that cannot be filled by insert/update
    public $protected = [
//       'property_id'
    ];

    public function __construct(){
        parent::__construct();
//        $this->load->database();
    }

    // get all property manager by agency
    public function get_property_manager_by_agency($agency_id){

        $this->db->select('aua.agency_user_account_id, aua.photo, aua.fname, aua.lname, aua.email, aua.phone, aua.alt_agencies');
        $this->db->from('agency_user_accounts aua');
        $this->db->where('aua.agency_id',$agency_id);
        $this->db->where('aua.active',1);
        $this->db->order_by('aua.fname','ASC');
        $this->db->order_by('aua.lname','ASC');
        $query = $this->db->get();
        return ($query->num_rows()>0)?$query->result():FALSE;

    }

    // get all property manager by agency v2 (with alt_agency/multiple agency)
    public function get_property_manager_by_agencyv2($agency_id){

        $alt_agency_where = " aua.`alt_agencies` LIKE '%{$this->session->agency_id}%' ";

        $this->db->select('aua.agency_user_account_id, aua.photo, aua.fname, aua.lname, aua.email, aua.phone');
        $this->db->from('agency_user_accounts aua');
        $this->db->group_start();
        $this->db->where('aua.agency_id',$agency_id);
        $this->db->or_where($alt_agency_where);
        $this->db->group_end();
        $this->db->where('aua.active',1);
        $this->db->order_by('aua.fname','ASC');
        $this->db->order_by('aua.lname','ASC');
        $query = $this->db->get();
        return ($query->num_rows()>0)?$query->result():FALSE;

    }

    //get agency by agency id
    public function get_agency_info($agency_id){
        $this->db->select('agency_name,agency_emails, new_job_email_to_agent');
        $this->db->from('agency');
        $this->db->where('agency_id',$agency_id);
        $query = $this->db->get();
        return ($query->num_rows()>0)?$query->row():false;

    }

    // get property manager filter pm_id ID (row)
    // return object
    public function get_property_manager_by_pm_id($id){

        $this->db->select('aua.agency_user_account_id, aua.fname, aua.lname');
        $this->db->from('agency_user_accounts aua');
        $this->db->where('aua.agency_user_account_id',$id);
        $this->db->where('aua.agency_id',$this->session->agency_id);
        $this->db->where('aua.user_type',2);
        $query = $this->db->get();
        return ($query->num_rows()>0)?$query->result():FALSE;

    }


    public function update_property_manager($prop_id,$data){

        $where = array('property_id' => $prop_id, 'agency_id' => $this->session->agency_id);
        $this->db->where($where);
        $this->db->update('property',$data);
        return ($this->db->affected_rows()>0)?TRUE:FALSE;

    }

    //add new property - return last id
    public function add_property($data){

        $this->db->insert('property',$data);
        return ($this->db->affected_rows() > 0)?$this->db->insert_id():FALSE;

     }

     // update property details (NO LONGER MANAGED)
    public function update_property($prop_id,$data){
        $where = array('property_id' => $prop_id, 'agency_id' => $this->session->agency_id);
        $this->db->where($where);
        $this->db->update('property',$data);
        return ($this->db->affected_rows()>0)?TRUE:FALSE;
    }

    // update property services (No Longer Managed)
    public function update_property_services($prop_id, $data){
        $where = array('property_id' => $prop_id);
        $this->db->where($where);
        $this->db->update('property_services',$data);
        return ($this->db->affected_rows()>0)?TRUE:FALSE;
    }

     //add property services
     public function add_property_services($data){

        $this->db->insert('property_services',$data);
        return ($this->db->affected_rows() > 0)?true:false;

    }

    // add property type
    public function add_property_type($data){

        $this->db->insert('property_propertytype',$data);
        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }

    }

    /**
     * Add/Insert Jobs
     * return last id
     */
    public function add_jobs($data){

        $this->db->insert('jobs',$data);
        if($this->db->affected_rows() > 0){
            return $this->db->insert_id();
        }else{
            return false;
        }

    }

     /**update/cancel jobs (NO LONGER MANAGED)
      * @params array prop_id property_id
      * @params array ajt_id alarm_job_type_id
      * return true
      */
     public function update_jobs($params=array(), $data){
        //$where = array('property_id' => $prop_id, 'status !=' => 'Completed');
        //$this->db->where($where);
        if(!empty($params['prop_id'])){
            $this->db->where('property_id',$params['prop_id']);
        }
        if(!empty($params['ajt_id'])){
            $this->db->where('service',$params['ajt_id']);
        }

        $this->db->where('status!=','Completed');
        $this->db->update('jobs',$data);
        return ($this->db->affected_rows()>0)?TRUE:FALSE;
    }

    // add bundle_services
    public function add_bundle_services($data){

        $this->db->insert('bundle_services',$data);
        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }

    }

    // add job-Log
    public function add_job_log($data){

        $this->db->insert('job_log',$data);
        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }

    }

    // add property alarms
    public function add_property_alarms($data){

        $this->db->insert('property_alarms',$data);
        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }

    }

    // insert property event log
    public function add_property_event_log($data){
        $this->db->insert('property_event_log',$data);
        if($this->db->affected_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    //check property ducplicate
    public function check_property_duplicate( $complete_address){

       $scp_str = $this->db->escape_str($complete_address);

        $sql_str = "
        SELECT property_id, agency_id, CONCAT(address_1, ' ',address_2, ' ',address_3, ' ',state, ' ', postcode) as c_address
        FROM property
        WHERE TRIM(LOWER(CONCAT(address_1, ' ',address_2, ' ',address_3, ' ',state, ' ', postcode))) = ?
        ";

        $query =  $this->db->query($sql_str, trim(strtolower($scp_str)));
        return ($query->num_rows()>0)?$query->row():FALSE;

    }
    
    /**
     * Search for Agency Property Address
     * @param $complete_address
     * @return array|array[]|false|object|object[]
     */
    public function search_property($complete_address)
    {
        // Escape the input string to prevent SQL injection
        $search_str = $this->db->escape_str($complete_address);
        
        // Construct the SQL query
        $this->db->select('p.property_id, p.address_1, p.address_2, p.address_3, p.state, p.postcode, p.pm_id_new, aua.fname, aua.lname');
        $this->db->from('property as p');
        $this->db->join('agency_user_accounts as aua','p.pm_id_new = aua.agency_user_account_id','left');
        $this->db->where('p.agency_id', $this->session->agency_id);
        $this->db->where('p.deleted', 0);
        $this->db->where("(p.is_nlm = 0 OR p.is_nlm IS NULL)");
        $this->db->like( "CONCAT(p.address_1, ' ', p.address_2, ', ', p.address_3, ', ', p.state, ', ', p.postcode)", $search_str);
        
        // Execute the query
        $query = $this->db->get();
        
        // Check if there are results
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return FALSE;
        }
    }
        
        
        public function get_agency_alarms(){
        $query = $this->db->get_where('agency_alarms',array('agency_id'=>$this->session->agency_id));
        return ($query->num_rows()>0)?$query->result():FALSE;
    }


    /**
     * Get all services assigned by agency
     * Note: exluding alarm job type id 12,13 and 14 which is IC
     * param agency_id - uniquq agency id
     * return object
     */
    public function get_agency_services($agency_id){

        $this->db->select('a_s.service_id, a_s.price, a_s.agency_services_id, a_s.agency_id, ajt.id, ajt.type, ajt.full_name');
        $this->db->from('agency_services as a_s');
        $this->db->join('alarm_job_type as ajt','ajt.id = a_s.service_id','left');
        $this->db->where('a_s.agency_id',$agency_id);
        $this->db->where('ajt.active',1);
        /*$this->db->where('ajt.id!=',12);
        $this->db->where('ajt.id!=',13);
        $this->db->where('ajt.id!=',14);*/
        $query = $this->db->get();
        return ($query->num_rows()>0)?$query->result():FALSE;

    }

    /**
     * Get Property services price by filter by property
     * param property id, alart job type id
     * return row
     */
    public function get_property_services_price($prop_id,$alarmJobTypeID){
        $this->db->select('price,service');
        $this->db->from('property_services as ps');
        $this->db->where('ps.property_id', $prop_id);
        $this->db->where('ps.alarm_job_type_id', $alarmJobTypeID);
        $query = $this->db->get();
        return ($query->num_rows()>0)?$query->row():FALSE;
    }

    //return services ID
    public function get_services_id($agency_id){
        $query = $this->db->query("
            SELECT *
            FROM `agency_services` AS a_s
            LEFT JOIN `alarm_job_type` AS ajt ON a_s.`service_id` = ajt.`id`
            WHERE `agency_id` = ".$this->db->escape($agency_id)."
            AND ajt.`active` = 1
        ");
        return $query->row()->service_id;
    }

    public function get_alarm_job_type_bundle($service_id){
        $query = $this->db->get_where('alarm_job_type', array('id'=>$service_id));
        return ($query->num_rows()>0)? $query->result():false;
    }

    public function get_all_service_type_by_agency($agency_id){

        $this->db->distinct('agen_serv.service_id');
        $this->db->select('ajt.id, ajt.type');
        $this->db->from('agency_services as agen_serv');
        $this->db->join('alarm_job_type as ajt', 'ajt.id = agen_serv.service_id', 'left');
        $this->db->where('agen_serv.agency_id', $agency_id);
        $query =  $this->db->get();
        return ($query->num_rows()>0)?$query->result():FALSE;

    }

    public function get_all_property_list($params){
        $agency_id = $this->session->agency_id;
        $where = array('a.agency_id'=> $agency_id, 'a.status' => 'active', 'ps.alarm_job_type_id !=' => 0);
        $this->db->distinct('ps.property_id');
        $this->db->select("p.property_id as p_property_id, p.address_1, p.address_2, p.address_3, p.state, p.postcode, p.property_managers_id as p_property_managers_id, p.pm_id_new, p.agency_id, p.holiday_rental, p.nlm_display, p.nlm_display, aua.agency_user_account_id, aua.fname, aua.lname, aua.`fname` AS pm_fname, aua.`lname` AS pm_lname, aua.`email` AS pm_email, aua.photo, apd.api, apd.api_prop_id");
        $this->db->from("property as p");
        $this->db->join('property_services as ps','p.property_id = ps.property_id', 'left');
        $this->db->join('agency_user_accounts as aua', 'p.`pm_id_new` =  aua.`agency_user_account_id`', 'left');
        $this->db->join('agency as a','a.agency_id = p.agency_id', 'left');
        $this->db->join('alarm_job_type as ajt','ajt.id = ps.alarm_job_type_id', 'left');
        $this->db->join('`api_property_data` AS apd', 'p.`property_id` = apd.`crm_prop_id`', 'left');
        $this->db->where($where);
       // $this->db->where('aua.agency_id',$agency_id);

        //pm_id params
        if( isset($params['pm_id']) && $params['pm_id'] != '' ){
            if( $params['pm_id']==0 ){
                $pm_no_assigend_where = "(p.pm_id_new=0 OR p.pm_id_new IS NULL)";
                $this->db->where($pm_no_assigend_where);
            }else{
                $this->db->where("p.`pm_id_new`", $params['pm_id']);
            }
        }

         //pm_id params
        if( is_numeric($params['p_deleted']) ){
            $this->db->where('p.`deleted`', $params['p_deleted']);
        }

        // search params
        if( isset($params['search']) && $params['search'] != '' ){
            $search_filter = "CONCAT_WS(' ', LOWER(p.address_1), LOWER(p.address_2), LOWER(p.address_3), LOWER(p.state), LOWER(p.postcode))";
            $this->db->like($search_filter, $params['search']);
        }

        // custom_where params
        if( isset($params['custom_where']) ){
            $this->db->where($params['custom_where']);
        }

        // sort
        if( isset($params['sort_list']) ){
            foreach( $params['sort_list'] as $sort_arr ){
                if( $sort_arr['order_by']!="" && $sort_arr['sort']!='' ){
                    $this->db->order_by($sort_arr['order_by'], $sort_arr['sort']);
                }
            }
        }

        // limit params
        if( isset($params['limit']) && $params['limit'] > 0 ){
            $this->db->limit( $params['limit'], $params['offset']);
        }

        $query = $this->db->get();
        if( isset($params['display_query']) && $params['display_query'] == 1 ){
            echo $this->db->last_query();
        }

        return ($query->num_rows()>0)?$query->result():FALSE;

    }

    //get sats service list SATS LIST -- service = 1
    public function get_property_list($agency_id, $condi = array(),$params=array()){

        $where = "a.agency_id = $agency_id AND a.status = 'active' AND ps.service = 1 AND ps.alarm_job_type_id != 0 AND (p.deleted = 0 OR (p.deleted = 1 AND p.nlm_display = 1))";
        $this->db->distinct('ps.property_id');
        $this->db->select("ps.property_id as ps_property_id, p.address_1, p.address_2, p.address_3, p.state, p.postcode, p.property_managers_id as p_property_managers_id, p.pm_id_new, p.agency_id, p.holiday_rental, p.nlm_display, p.nlm_display, aua.agency_user_account_id, aua.fname, aua.lname, aua.`fname` AS pm_fname, aua.`lname` AS pm_lname, aua.`email` AS pm_email, aua.photo");
        $this->db->from("property_services as ps");
        $this->db->join('property as p','p.property_id = ps.property_id', 'left');
        $this->db->join('agency_user_accounts as aua', 'p.`pm_id_new` =  aua.`agency_user_account_id`', 'left');
        $this->db->join('agency as a','a.agency_id = p.agency_id', 'left');
        $this->db->join('alarm_job_type as ajt','ajt.id = ps.alarm_job_type_id', 'left');
        $this->db->join('jobs as j','j.property_id = p.property_id', 'left');

       // $this->db->where('j.job_type!=','Once-off');
        $this->db->where($where);
        //$this->db->where('aua.agency_id',$agency_id);

        $address_search_filter = "CONCAT_WS(' ', LOWER(p.address_1), LOWER(p.address_2), LOWER(p.address_3), LOWER(p.state), LOWER(p.postcode))";
        //search
        if(!empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // 3 serch field has value
           $this->db->like($address_search_filter, $condi['search']['keyword']);
           $this->db->where("p.pm_id_new", $condi['search']['pm']);
           $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
        }else if(!empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // keyword and pm
            $this->db->like($address_search_filter, $condi['search']['keyword']);
            $this->db->where("p.pm_id_new", $condi['search']['pm']);
        }else if(!empty($condi['search']['keyword']) && empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ //keyword and service type
            $this->db->like($address_search_filter, $condi['search']['keyword']);
            $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
        }else if(empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // pm and service type
            $this->db->where("p.pm_id_new", $condi['search']['pm']);
            $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
        }else if(!empty($condi['search']['keyword']) && empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // only keyword
            $this->db->like($address_search_filter, $condi['search']['keyword']);
        }else if(empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // only pm
            $this->db->where("p.pm_id_new", $condi['search']['pm']);
        }else if(empty($condi['search']['keyword']) && empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // only service type
            $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
        }

        if($params['not_in']!=""){
            $this->db->where_not_in('ps.property_id', $params['not_in']);
        }

        if( isset($params['pm_id']) && $params['pm_id'] > 0 ){
            if( $params['pm_id']==0 ){
                $pm_no_assigend_where = "(p.pm_id_new=0 OR p.pm_id_new IS NULL)";
                $this->db->where($pm_no_assigend_where);
            }else{
                $this->db->where("p.`pm_id_new`", $params['pm_id']);
            }
        }

        if(array_key_exists('limit',$condi) && array_key_exists('offset',$condi)){
            $this->db->limit($condi['limit'],$condi['offset']);
        }else if(array_key_exists('limit',$condi) && !array_key_exists('offset',$condi)){
            $this->db->limit($condi['limit']);
        }


        $this->db->order_by('p.address_2','ASC');

        $query =  $this->db->get();
        return ($query->num_rows()>0)?$query->result():FALSE;

    }

    public function get_property_list_ver2($agency_id, $condi = array(),$params=array()){

        if($params['sel_query'] && $params['sel_query']==1){
            $sel_query = "p.property_id as property_id, p.address_1, p.address_2, p.address_3, p.state, p.postcode, p.property_managers_id as p_property_managers_id, p.pm_id_new, p.agency_id, p.holiday_rental, p.nlm_display, p.nlm_display, p.`compass_index_num`, aua.agency_user_account_id, aua.fname, aua.lname, aua.`fname` AS pm_fname, aua.`lname` AS pm_lname, aua.`email` AS pm_email, aua.photo";
        }elseif($params['sel_query'] && $params['sel_query']!=1){
            $sel_query = $params['sel_query'];
        }

        //$where = "a.agency_id = $agency_id AND a.status = 'active' AND ps.service = 1 AND ps.alarm_job_type_id != 0 AND ((p.is_nlm = 0 OR p.is_nlm IS NULL) OR (p.is_nlm = 1 AND p.nlm_display = 1))"; ##updated below
        $where = "a.agency_id = $agency_id AND a.status = 'active' AND ps.service = 1 AND ps.alarm_job_type_id != 0 AND (p.is_nlm = 0 OR p.is_nlm IS NULL)"; ##removed nlm_display stuff
        
        $this->db->distinct('ps.property_id');
        $this->db->select($sel_query);
        $this->db->from("property_services as ps");
        $this->db->join('property as p','p.property_id = ps.property_id', 'left');
        $this->db->join('agency_user_accounts as aua', 'p.`pm_id_new` =  aua.`agency_user_account_id` AND aua.`active` = 1', 'left');
        $this->db->join('agency as a','a.agency_id = p.agency_id', 'left');
        $this->db->join('alarm_job_type as ajt','ajt.id = ps.alarm_job_type_id', 'left');
        #$this->db->join('jobs as j','j.property_id = p.property_id', 'left');

        // multiple custom joins
        if( $params['custom_joins_arr'] !="" ){

            foreach( $params['custom_joins_arr'] as $custom_joins ){
                $this->db->join($custom_joins['join_table'], $custom_joins['join_on'], $custom_joins['join_type']);
            }

        }

        $this->db->where($where);
        $this->db->where('p.deleted',0);

        $address_search_filter = "CONCAT_WS(' ', LOWER(p.address_1), LOWER(p.address_2), LOWER(p.address_3), LOWER(p.state), LOWER(p.postcode))";
        //search
        if(!empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // 3 serch field has value
           $this->db->like($address_search_filter, $condi['search']['keyword']);
           $this->db->where("p.pm_id_new", $condi['search']['pm']);
           $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
        }else if(!empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // keyword and pm
            $this->db->like($address_search_filter, $condi['search']['keyword']);
            $this->db->where("p.pm_id_new", $condi['search']['pm']);
        }else if(!empty($condi['search']['keyword']) && empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ //keyword and service type
            $this->db->like($address_search_filter, $condi['search']['keyword']);
            $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
        }else if(empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // pm and service type
            $this->db->where("p.pm_id_new", $condi['search']['pm']);
            $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
        }else if(!empty($condi['search']['keyword']) && empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // only keyword
            $this->db->like($address_search_filter, $condi['search']['keyword']);
        }else if(empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // only pm
            $this->db->where("p.pm_id_new", $condi['search']['pm']);
        }else if(empty($condi['search']['keyword']) && empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // only service type
            $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
        }

        if($params['not_in']!=""){
            $this->db->where_not_in('ps.property_id', $params['not_in']);
        }

        //if( isset($params['pm_id']) && $params['pm_id'] > 0 ){
        if( isset($params['pm_id']) && $params['pm_id'] !='' ){
            if( $params['pm_id']==0 ){
                $pm_no_assigend_where = "(p.pm_id_new=0 OR p.pm_id_new IS NULL)";
                $this->db->where($pm_no_assigend_where);
            }else{
                $this->db->where("p.`pm_id_new`", $params['pm_id']);
            }
        }

        // custom filter
        if( $params['custom_where']!=""){
            $this->db->where($params['custom_where']);
        }

        if(array_key_exists('limit',$condi) && array_key_exists('offset',$condi)){
            $this->db->limit($condi['limit'],$condi['offset']);
        }else if(array_key_exists('limit',$condi) && !array_key_exists('offset',$condi)){
            $this->db->limit($condi['limit']);
        }

        $this->db->group_by('p.property_id');
        $this->db->order_by('p.address_2','ASC');

        $query =  $this->db->get();

        //return ($query->num_rows()>0)?$query->result():FALSE;
        return $query;

    }

    //get non sats serviced list NONE SATS
    public function get_property_list_non_sats($agency_id,$propNotIn,$condi=array()){

        if( !empty($condi['pm_distinct']) ){
            $this->db->distinct($condi['pm_distinct']);
        }else{
            $this->db->distinct('ps.property_id');
        }

        if( !empty($condi['sel_query']) ){
            $sel_query = $condi['sel_query'];
            $this->db->select($sel_query);
        }else{
            $this->db->select("ps.property_id,ps.service, p.address_1, p.address_2, p.address_3, p.state, p.postcode, p.property_managers_id as p_property_managers_id, p.pm_id_new, p.nlm_display, a.agency_id, aua.fname as pm_fname, aua.lname as pm_lname, aua.agency_user_account_id, aua.photo");
        }

        $this->db->from('property_services as ps');
        $this->db->join('property as p', 'p.property_id =  ps.property_id','left');
        $this->db->join('agency as a', 'a.agency_id = p.agency_id','left');
        $this->db->join('alarm_job_type as ajt','ajt.id = ps.alarm_job_type_id','left');
        $this->db->join('agency_user_accounts as aua', 'aua.agency_user_account_id =  p.pm_id_new', 'left');
        $this->db->where_not_in('ps.property_id', $propNotIn);
        $this->db->where('p.agency_id', $agency_id);
        $this->db->where('ps.service !=',1);
        $this->db->where('ps.alarm_job_type_id !=',0);
       // $this->db->where('p.deleted',0);
        $this->db->where('a.status','active');
        $this->db->where("(p.deleted = 0 OR (p.deleted = 1 AND p.nlm_display = 1))");
        //$this->db->where('aua.agency_id',$agency_id);


        $address_search_filter = "CONCAT_WS(' ', LOWER(p.address_1), LOWER(p.address_2), LOWER(p.address_3), LOWER(p.state), LOWER(p.postcode))";
        //search
        if(!empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // 3 serch field has value
            $this->db->like($address_search_filter, $condi['search']['keyword']);
            $this->db->where("p.pm_id_new", $condi['search']['pm']);
            $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
         }else if(!empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // keyword and pm
             $this->db->like($address_search_filter, $condi['search']['keyword']);
             $this->db->where("p.pm_id_new", $condi['search']['pm']);
         }else if(!empty($condi['search']['keyword']) && empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ //keyword and service type
             $this->db->like($address_search_filter, $condi['search']['keyword']);
             $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
         }else if(empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // pm and service type
             $this->db->where("p.pm_id_new", $condi['search']['pm']);
             $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
         }else if(!empty($condi['search']['keyword']) && empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // only keyword
             $this->db->like($address_search_filter, $condi['search']['keyword']);
         }else if(empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // only pm
             $this->db->where("p.pm_id_new", $condi['search']['pm']);
         }else if(empty($condi['search']['keyword']) && empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // only service type
             $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
         }


         // limit and offset
        if(array_key_exists('limit',$condi) && array_key_exists('offset',$condi)){
            $this->db->limit($condi['limit'],$condi['offset']);
        }else if(array_key_exists('limit',$condi) && !array_key_exists('offset',$condi)){
            $this->db->limit($condi['limit']);
        }

        if( !empty($condi['sort_by']) && !empty($condi['sort']) ){
            $this->db->order_by($condi['sort_by'], $condi['sort']);
        }

        if(!empty($condi['group_by'])){
            $this->db->group_by($condi['group_by']);
        }

        $query = $this->db->get();
        return ($query->num_rows()>0)?$query->result():FALSE;

    }

    public function get_property_list_non_sats_ver2($agency_id,$propNotIn,$condi=array(), $params){

        if($params['sel_query'] && $params['sel_query']==1){
            $sel_query = "ps.property_id,ps.service, p.address_1, p.address_2, p.address_3, p.state, p.postcode, p.property_managers_id as p_property_managers_id, p.pm_id_new, p.nlm_display, a.agency_id, aua.fname as pm_fname, aua.lname as pm_lname, aua.agency_user_account_id, aua.photo";
        }elseif($params['sel_query'] && $params['sel_query']!=1){
            $sel_query = $params['sel_query'];
        }

        $this->db->distinct('ps.property_id');
        $this->db->select($sel_query);
        $this->db->from('property_services as ps');
        $this->db->join('property as p', 'p.property_id =  ps.property_id','left');
        $this->db->join('agency as a', 'a.agency_id = p.agency_id','left');
        $this->db->join('alarm_job_type as ajt','ajt.id = ps.alarm_job_type_id','left');
        $this->db->join('agency_user_accounts as aua', 'aua.agency_user_account_id =  p.pm_id_new', 'left');
        if (!empty($propNotIn)) {
            $this->db->where_not_in('ps.property_id', $propNotIn);
        }
        $this->db->where('p.agency_id', $agency_id);
        $this->db->where('ps.service !=',1);
        $this->db->where('ps.alarm_job_type_id !=',0);
       // $this->db->where('p.deleted',0);
        $this->db->where('a.status','active');
        $this->db->where("(p.deleted = 0 OR (p.deleted = 1 AND p.nlm_display = 1))");
        //$this->db->where('aua.agency_id',$agency_id);

        $address_search_filter = "CONCAT_WS(' ', LOWER(p.address_1), LOWER(p.address_2), LOWER(p.address_3), LOWER(p.state), LOWER(p.postcode))";
        //search
        if(!empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // 3 serch field has value
            $this->db->like($address_search_filter, $condi['search']['keyword']);
            $this->db->where("p.pm_id_new", $condi['search']['pm']);
            $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
         }else if(!empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // keyword and pm
             $this->db->like($address_search_filter, $condi['search']['keyword']);
             $this->db->where("p.pm_id_new", $condi['search']['pm']);
         }else if(!empty($condi['search']['keyword']) && empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ //keyword and service type
             $this->db->like($address_search_filter, $condi['search']['keyword']);
             $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
         }else if(empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // pm and service type
             $this->db->where("p.pm_id_new", $condi['search']['pm']);
             $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
         }else if(!empty($condi['search']['keyword']) && empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // only keyword
             $this->db->like($address_search_filter, $condi['search']['keyword']);
         }else if(empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // only pm
             $this->db->where("p.pm_id_new", $condi['search']['pm']);
         }else if(empty($condi['search']['keyword']) && empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // only service type
             $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
         }



         if( isset($params['pm_id']) && $params['pm_id'] > 0 ){
            if( $params['pm_id']==0 ){
                $pm_no_assigend_where = "(p.pm_id_new=0 OR p.pm_id_new IS NULL)";
                $this->db->where($pm_no_assigend_where);
            }else{
                $this->db->where("p.`pm_id_new`", $params['pm_id']);
            }
        }

         // limit and offset
        if(array_key_exists('limit',$condi) && array_key_exists('offset',$condi)){
            $this->db->limit($condi['limit'],$condi['offset']);
        }else if(array_key_exists('limit',$condi) && !array_key_exists('offset',$condi)){
            $this->db->limit($condi['limit']);
        }

        if(!empty($params['group_by'])){
            $this->db->group_by($params['group_by']);
        }

        $query = $this->db->get();
        //return ($query->num_rows()>0)?$query->result():FALSE;
        return $query;

    }

    public function get_property_list_non_sats_ver2_total($agency_id,$propNotIn,$condi=array(), $params){

        $this->db->select("IFNULL(COUNT(DISTINCT ps.property_id), 0) AS count");
        $this->db->from('property_services as ps');
        $this->db->join('property as p', 'p.property_id =  ps.property_id','left');
        $this->db->join('agency as a', 'a.agency_id = p.agency_id','left');
        $this->db->join('alarm_job_type as ajt','ajt.id = ps.alarm_job_type_id','left');
        $this->db->join('agency_user_accounts as aua', 'aua.agency_user_account_id =  p.pm_id_new', 'left');
        if (!empty($propNotIn)) {
            $this->db->where_not_in('ps.property_id', $propNotIn);
        }
        $this->db->where('p.agency_id', $agency_id);
        $this->db->where('ps.service !=',1);
        $this->db->where('ps.alarm_job_type_id !=',0);
       // $this->db->where('p.deleted',0);
        $this->db->where('a.status','active');
        $this->db->where("(p.deleted = 0 OR (p.deleted = 1 AND p.nlm_display = 1))");
        //$this->db->where('aua.agency_id',$agency_id);

        $address_search_filter = "CONCAT_WS(' ', LOWER(p.address_1), LOWER(p.address_2), LOWER(p.address_3), LOWER(p.state), LOWER(p.postcode))";
        //search
        if(!empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // 3 serch field has value
            $this->db->like($address_search_filter, $condi['search']['keyword']);
            $this->db->where("p.pm_id_new", $condi['search']['pm']);
            $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
         }else if(!empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // keyword and pm
             $this->db->like($address_search_filter, $condi['search']['keyword']);
             $this->db->where("p.pm_id_new", $condi['search']['pm']);
         }else if(!empty($condi['search']['keyword']) && empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ //keyword and service type
             $this->db->like($address_search_filter, $condi['search']['keyword']);
             $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
         }else if(empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // pm and service type
             $this->db->where("p.pm_id_new", $condi['search']['pm']);
             $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
         }else if(!empty($condi['search']['keyword']) && empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // only keyword
             $this->db->like($address_search_filter, $condi['search']['keyword']);
         }else if(empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // only pm
             $this->db->where("p.pm_id_new", $condi['search']['pm']);
         }else if(empty($condi['search']['keyword']) && empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // only service type
             $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
         }

         if( isset($params['pm_id']) && $params['pm_id'] > 0 ){
            if( $params['pm_id']==0 ){
                $pm_no_assigend_where = "(p.pm_id_new=0 OR p.pm_id_new IS NULL)";
                $this->db->where($pm_no_assigend_where);
            }else{
                $this->db->where("p.`pm_id_new`", $params['pm_id']);
            }
        }

        return $this->db->get()->row()->count;

    }



    //get sats once-off service
    public function get_once_off_service($condi=array(), $params){

        $this->db->distinct('j.property_id');
        $this->db->select('j.property_id as j_property_id, p.address_1, p.address_2, p.address_3, p.state, p.postcode, p.property_managers_id as p_property_managers_id, p.pm_id_new, p.nlm_display, aua.fname as pm_fname, aua.lname as pm_lname, aua.agency_user_account_id, aua.photo');
        $this->db->from('jobs as j');
        $this->db->join('property as p','p.property_id = j.property_id','left');
        $this->db->join('agency_user_accounts as aua','aua.agency_user_account_id = p.pm_id_new AND aua.`active` = 1','left');
        $this->db->join('property_services as ps','ps.property_id = p.property_id','left');
        $this->db->where('j.job_type','Once-off');
        $this->db->where('j.del_job',0);
        $this->db->where('p.agency_id', $this->session->agency_id);
        //$this->db->where('p.deleted',0);
        //$this->db->where("(p.deleted = 0 OR (p.deleted = 1 AND p.nlm_display = 1))");
        $this->db->where("(p.is_nlm = 0 OR p.is_nlm IS NULL)"); ## new update > removed nlm_display stuff
        $this->db->where('p.deleted',0); ## new update

        $address_search_filter = "CONCAT_WS(' ', LOWER(p.address_1), LOWER(p.address_2), LOWER(p.address_3), LOWER(p.state), LOWER(p.postcode))";
        //search
        if(!empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // 3 serch field has value
            $this->db->like($address_search_filter, $condi['search']['keyword']);
            $this->db->where("p.pm_id_new", $condi['search']['pm']);
            $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
         }else if(!empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // keyword and pm
             $this->db->like($address_search_filter, $condi['search']['keyword']);
             $this->db->where("p.pm_id_new", $condi['search']['pm']);
         }else if(!empty($condi['search']['keyword']) && empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ //keyword and service type
             $this->db->like($address_search_filter, $condi['search']['keyword']);
             $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
         }else if(empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // pm and service type
             $this->db->where("p.pm_id_new", $condi['search']['pm']);
             $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
         }else if(!empty($condi['search']['keyword']) && empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // only keyword
             $this->db->like($address_search_filter, $condi['search']['keyword']);
         }else if(empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // only pm
             $this->db->where("p.pm_id_new", $condi['search']['pm']);
         }else if(empty($condi['search']['keyword']) && empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // only service type
             $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
         }

        if( isset($params['pm_id']) && $params['pm_id'] !='' ){
            if( $params['pm_id']==0 ){
                $pm_no_assigend_where = "(p.pm_id_new=0 OR p.pm_id_new IS NULL)";
                $this->db->where($pm_no_assigend_where);
            }else{
                $this->db->where("p.`pm_id_new`", $params['pm_id']);
            }
        }

         // limist and offset
        if(array_key_exists('limit',$condi) && array_key_exists('offset',$condi)){
            $this->db->limit($condi['limit'],$condi['offset']);
        }else if(array_key_exists('limit',$condi) && !array_key_exists('offset',$condi)){
            $this->db->limit($condi['limit']);
        }

        $this->db->order_by('j.created','ASC');
        $query = $this->db->get();
        return ($query->num_rows()>0)?$query->result():FALSE;

    }
    //get sats once-off service
    public function get_once_off_service_total($condi=array(), $params){

        $this->db->select('IFNULL(COUNT(DISTINCT j.property_id), 0) AS count');
        $this->db->from('jobs as j');
        $this->db->join('property as p','p.property_id = j.property_id','left');
        $this->db->join('agency_user_accounts as aua','aua.agency_user_account_id = p.pm_id_new','left');
        $this->db->join('property_services as ps','ps.property_id = p.property_id','left');
        $this->db->where('j.job_type','Once-off');
        $this->db->where('j.del_job',0);
        $this->db->where('p.agency_id', $this->session->agency_id);
        //$this->db->where('p.deleted',0);
        //$this->db->where("(p.deleted = 0 OR (p.deleted = 1 AND p.nlm_display = 1))"); ## disabled
        $this->db->where("(p.is_nlm = 0 OR p.is_nlm IS NULL)"); ## new update > removed nlm_display stuff
        $this->db->where('p.deleted',0); ## new update

        $address_search_filter = "CONCAT_WS(' ', LOWER(p.address_1), LOWER(p.address_2), LOWER(p.address_3), LOWER(p.state), LOWER(p.postcode))";
        //search
        if(!empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // 3 serch field has value
            $this->db->like($address_search_filter, $condi['search']['keyword']);
            $this->db->where("p.pm_id_new", $condi['search']['pm']);
            $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
        }else if(!empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // keyword and pm
            $this->db->like($address_search_filter, $condi['search']['keyword']);
            $this->db->where("p.pm_id_new", $condi['search']['pm']);
        }else if(!empty($condi['search']['keyword']) && empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ //keyword and service type
            $this->db->like($address_search_filter, $condi['search']['keyword']);
            $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
        }else if(empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // pm and service type
            $this->db->where("p.pm_id_new", $condi['search']['pm']);
            $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
        }else if(!empty($condi['search']['keyword']) && empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // only keyword
            $this->db->like($address_search_filter, $condi['search']['keyword']);
        }else if(empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // only pm
            $this->db->where("p.pm_id_new", $condi['search']['pm']);
        }else if(empty($condi['search']['keyword']) && empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // only service type
            $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
        }

        if( isset($params['pm_id']) && $params['pm_id'] !='' ){
            if( $params['pm_id']==0 ){
                $pm_no_assigend_where = "(p.pm_id_new=0 OR p.pm_id_new IS NULL)";
                $this->db->where($pm_no_assigend_where);
            }else{
                $this->db->where("p.`pm_id_new`", $params['pm_id']);
            }
        }

        // $this->db->group_by("p.property_id");
        $this->db->limit(1);

        return $this->db->get()->row()->count;

    }

    //get property services where sevice = 1
    public function get_property_services_typeAndIcons($params){
       // $this->db->distinct('property_id');
        if( !empty($params['sel_query']) ){
            $sel_query = $params['sel_query'];
        }else{
            $sel_query = "*";
        }

        $this->db->select($sel_query);
        $this->db->from('property_services as ps');
        $this->db->join('alarm_job_type as ajt', 'ajt.id = ps.alarm_job_type_id','left');

        if( !empty($params['prop_id']) ){
            $this->db->where('property_id',$params['prop_id']);
        }

        $this->db->where('service',1);
        return $this->db->get()->result();
    }

    //get all property services
    public function get_property_services_list($prop_id){
        $this->db->select("*");
        $this->db->from('property_services as ps');
        $this->db->join('alarm_job_type as ajt', 'ajt.id = ps.alarm_job_type_id','left');
        $this->db->where('property_id',$prop_id);
        $query = $this->db->get();
        return ($query->num_rows()>0)?$query->result():false;
    }

    public function servicesIcons($id,$service){
        $img = "";
        switch($id){
            case "2": $img = ($service == 1)?'smoke_colored.png':'smoke_grey.png'; break; // Smoke Alarms
            case "6": $img = ($service == 1)?'corded_colored.png':'corded_grey.png'; break; // COrded windows
            case "8": $img = ($service == 1)?'sa_ss_colored.png':'sa_ss_grey.png'; break; // Smoke Alarm & Safety Switch
            case "9": $img = ($service == 1)?'sa_cw_ss_colored.png':'sa_cw_ss_grey.png'; break;  // Bundle SA.CW.SS
            case "11": $img = ($service == 1)?'sa_wm_colored.png':'sa_wm_grey.png'; break;  // Smoke Alarm & Water Meter
            case "12": $img = ($service == 1)?'sa_colored_IC.png':'sa_grey_IC.png'; break;  // Smoke Alarms (IC)
            case "13": $img = ($service == 1)?'sa_ss_colored_IC.png':'sa_ss_grey_IC.png'; break;  // Smoke Alarm & Safety Switch (IC)
            case "14": $img = ($service == 1)?'sa_cw_ss_colored_IC.png':'sa_cw_ss_grey_IC.png';  // Bundle SA.CW.SS (IC)
        }
        return $img;
    }

    public function get_agency_service_type_row_by_agency_id($agency_id,$property_id){

        $data = array();

        $this->db->select('*');
        $this->db->from('agency_services as agen_serv');
        $this->db->join('alarm_job_type as ajt', 'ajt.id = agen_serv.service_id', 'left');
        $this->db->where('agen_serv.agency_id', $agency_id);
        $this->db->where('ajt.active', 1);
        $this->db->order_by('agen_serv.service_id');

        $query =  $this->db->get();

        foreach($query->result() as $newtt){

            $this->db->select('ps.property_services_id as ps_properpty_services_id, ps.property_id as ps_prop_id, ps.alarm_job_type_id, ps.service as ps_service, ps.price, ajt.type as ajt_type, ajt.id as ajt_id');
            $this->db->from('property_services as ps');
            $this->db->join('alarm_job_type as ajt', 'ajt.id = ps.alarm_job_type_id','left');
            $this->db->where('ps.property_id', $property_id );
            $this->db->where('ps.alarm_job_type_id',$newtt->service_id);
            $this->db->where('ajt.active', 1);

            $query2 =  $this->db->get();
        }
       return $query2->result();

    }

    // get last service - return object
    public function get_last_service($property_id){
        $this->db->select("j.id, j.date");
        $this->db->from("jobs j");
        $this->db->join('property as p', 'p.property_id = j.property_id','left');
        $this->db->where('j.property_id', $property_id);
        $this->db->where('j.status','Completed');
        $this->db->where('j.del_job', 0);
        $this->db->order_by('j.date','DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows()>0){
          return $query->result();
        }else{
            return false;
        }

    }

    // get last service  - return row
    public function get_last_service_row($property_id){
        $this->db->select("j.id, j.date, j.status, j.assigned_tech, j.job_type, p.qld_new_leg_alarm_num, p.prop_upgraded_to_ic_sa, p.state, p.is_nlm");
        $this->db->from("jobs j");
        $this->db->join('property as p', 'p.property_id = j.property_id','left');
        $this->db->where('j.property_id', $property_id);
        $this->db->group_start();
        $this->db->where('j.status','Completed');
        $this->db->or_where('j.status','Merged Certificates');
        $this->db->group_end();
        $this->db->where('j.assigned_tech!=', 1);
        $this->db->where('j.assigned_tech!=', 2);
        $this->db->where('j.assigned_tech!=', NULL);
        $this->db->where('j.del_job', 0);
        $this->db->order_by('j.date','DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows()>0){
          return $query->row();
        }else{
            return false;
        }

    }

    // get Last yearly maintenance  - return row
    public function get_last_yearly_maintenance_row($property_id){
        $this->db->select("j.id, j.date, j.status, j.assigned_tech, j.job_type, p.qld_new_leg_alarm_num, p.prop_upgraded_to_ic_sa, p.state");
        $this->db->from("jobs j");
        $this->db->join('property as p', 'p.property_id = j.property_id','left');
        $this->db->where('j.property_id', $property_id);
        $this->db->group_start();
        $this->db->where('j.status','Completed');
        $this->db->or_where('j.status','Merged Certificates');
        $this->db->group_end();
        $this->db->where('j.job_type','Yearly Maintenance');
        $this->db->where('j.del_job', 0);
        $this->db->order_by('j.date','DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows()>0){
          return $query->row();
        }else{
            return false;
        }

    }

    /**
     * GET ALARM DETAILS
     * get last job - return job_id
     * get alarm details  - return object
     * return object
     */
    public function alarm_det($prop_id){

        $this->db->distinct('j.id as job_id');
        $this->db->select("j.id as job_id, j.assigned_tech");
        $this->db->from("alarm as a");
        $this->db->join('jobs as j', 'j.id = a.job_id','left');
        $this->db->where('j.property_id',$prop_id);
        $this->db->where('j.status','Completed');

        $this->db->where('j.assigned_tech!=', NULL); //fetch only alarm that has tech
        $this->db->where('j.assigned_tech!=', 1); //fetch only alarm that has tech

        $this->db->where('j.del_job',0);
        $this->db->order_by('j.date','DESC');
        $this->db->limit(1);
        $query = $this->db->get();

        if($query->num_rows()>0){
            $job_id = $query->row()->job_id;
            $this->db->select("a.ts_position, ap.alarm_pwr, ap.battery_type, ap.is_replaceable, ap.alarm_pwr_source, at.alarm_type, a.make, a.model, a.expiry, a.ts_required_compliance, a.ts_newbattery");
            $this->db->from('alarm a');
            $this->db->join('alarm_pwr ap', 'ap.alarm_pwr_id =  a.alarm_power_id','left');
            $this->db->join('alarm_type at','at.alarm_type_id = a.alarm_type_id','left');
            $this->db->where('a.job_id', $job_id);
            $this->db->where('a.ts_discarded',0);
            $query2 = $this->db->get();
            return ($query2->num_rows()>0)?$query2->result():FALSE;
        }

    }

    /**
     * GET CORDED WINDOWS
     * get last job - return job id
     * get corded windows - return object
     * return object
     */
    public function get_corded_windows($prop_id){
        $this->db->distinct('j.id as job_id');
        $this->db->select("j.id as job_id");
        $this->db->from("corded_window as cw");
        $this->db->join('jobs as j', 'j.id = cw.job_id','left');
        $this->db->where('j.property_id',$prop_id);
        $this->db->where('j.status','Completed');
        $this->db->where('j.del_job',0);
        $this->db->order_by('j.date','DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows()>0){
            $job_id = $query->row()->job_id;
            $this->db->select("cw.location, cw.num_of_windows");
            $this->db->from('corded_window as cw');
            $this->db->join('blind_type_cw as btc', 'btc.blind_type_cw_id = cw.covering','left');
            $this->db->where('cw.job_id', $job_id);
            $query2 = $this->db->get();
            return ($query2->num_rows()>0)?$query2->result():FALSE;
        }
    }

    //get safety switch location
    public function get_safety_switch_location($prop_id){
        $this->db->select('ss_location, ss_quantity, ss_image');
        $this->db->from('jobs');
        $this->db->where('property_id', $prop_id);
        $this->db->where('del_job',0);

        $this->db->where('assigned_tech!=',NULL); //fetch only ss whith tech
        $this->db->where('assigned_tech!=',1); //fetch only ss - not supplier

        $this->db->order_by('id','DESC');
        $this->db->limit(1);
        $query =  $this->db->get();
        return ($query->num_rows()>0)?$query->row():false;
    }

    //get safety switch details
    public function get_safety_switch_details($prop_id){

        $this->db->select('id');
        $this->db->from('jobs');
        $this->db->where('property_id', $prop_id);
        $this->db->where('status!=','Cancelled');
        $this->db->where('del_job',0);
        $this->db->where('status','Completed');
        $this->db->order_by('date','DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows()>0){
            foreach($query->result() as $newTT){
                $this->db->select('job_id,make,model,test');
                $this->db->from('safety_switch');
                $this->db->where('job_id',$newTT->id);
                $query2 = $this->db->get();
            }
            return $query2->result();
        }else{
            return false;
        }

    }


    //get water meter details/info (return row)
    public function get_water_meter_details($prop_id){
        $this->db->select('id');
        $this->db->from('jobs');
        $this->db->where('property_id', $prop_id);
        $this->db->where('status!=','Cancelled');
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows()>0){
            foreach($query->result() as $newTT){
                $this->db->select('location,reading,meter_image,meter_reading_image');
                $this->db->from('water_meter');
                $this->db->where('job_id',$newTT->id);
                $query2 = $this->db->get();
            }
            return $query2->row();
        }else{
            return false;
        }
    }



    // get property info filter by property id
    public function get_property_detail_by_id($prop_id){

       $this->db->select("*,
        p.property_id as p_property_id,
        p.prop_upgraded_to_ic_sa as ic_upgrade,
        p.address_1 as p_address_1,
        p.address_2 as p_address_2,
        p.address_3 as p_address_3,
        p.state as p_state,
        p.postcode as p_postcode,
        p.landlord_ph,
        p.landlord_mob,
        p.alarm_code,
        p.key_number,
        p.pm_id_new,
        p.comments,
        aua.fname as pm_fname,
        aua.lname as pm_lname
       ");
       $this->db->from('property as p');

       $this->db->join('agency_user_accounts as aua','aua.agency_user_account_id = p.pm_id_new','left');

       $this->db->where('p.property_id',$prop_id);
       $this->db->where('p.agency_id',$this->session->agency_id);
       $query = $this->db->get();
       return ($query->num_rows()>0)?$query->row():FALSE;

    }


    public function get_property_services_list_detail_by_prop_id($prop_id){

        $this->db->select("*");
        $this->db->from('property_services as ps');
        $this->db->join('alarm_job_type as ajt', 'ajt.id = ps.alarm_job_type_id','left');

        $this->db->join('agency_services as as', 'as.service_id = ps.alarm_job_type_id','left');
        $this->db->where('as.agency_id',$this->session->agency_id);


        $this->db->where('property_id',$prop_id);
        $this->db->where('ajt.id!=',12);
        $this->db->where('ajt.id!=',13);
        $this->db->where('ajt.id!=',14);
        $query = $this->db->get();
        return ($query->num_rows()>0)?$query->result():FALSE;

    }




    //check if job that is not cancelled already exist
    public function checkJobIfExist($prop_id,$ajt_id){
        $query = $this->db->get_where('jobs',array('property_id'=>$prop_id,'service'=>$ajt_id, 'status!=' => 'Cancelled'));
        return ($query->num_rows()>0)?true:false;
    }



    // added by jc
    public function get_property_services($params,$condi=array()){

        if( isset($params['sel_query']) ){
            $sel_query = $params['sel_query'];
        }else{
            $sel_query = '*';
        }

        $this->db->select($sel_query);
        $this->db->from('`property_services` AS ps');
        $this->db->join('`property` AS p', 'ps.property_id = p.property_id', 'left');
        $this->db->join('`agency_user_accounts` AS aua', 'p.`pm_id_new` = aua.`agency_user_account_id`', 'left');
        $this->db->join('`alarm_job_type` AS ajt', 'ps.`alarm_job_type_id` = ajt.`id`', 'left');

        if( $params['custom_join_sub_query_for_qld_upgrade']!="" ){ //for qld_upgrade page only
            $this->db->join( $params['custom_join_sub_query_for_qld_upgrade'] ,'complJob ON complJob.property_id = p.property_id','inner');
        }

        // agency
        if( isset($params['agency_id']) ){
            $this->db->where('p.`agency_id`', $params['agency_id']);
        }
        if( isset($params['a_status']) ){
            $this->db->where('a.`status`', $params['a_status']);
        }

        // property
        if( isset($params['property_id']) ){
            $this->db->where('p.`property_id`', $params['property_id']);
        }
        if( isset($params['p_deleted']) ){
            $this->db->where('p.`deleted`', $params['p_deleted']);
        }else{
            $this->db->where('p.`deleted`',0);
        }
        if( isset($params['pm_id']) && $params['pm_id'] != '' ){
            if( $params['pm_id']==0 ){
                $pm_no_assigend_where = "(p.pm_id_new=0 OR p.pm_id_new IS NULL)";
                $this->db->where($pm_no_assigend_where);
            }else{
                $this->db->where("p.`pm_id_new`", $params['pm_id']);
            }
        }
        // search
        if( isset($params['search']) && $params['search'] != '' ){
            $search_filter = "CONCAT_WS(' ', LOWER(p.address_1), LOWER(p.address_2), LOWER(p.address_3), LOWER(p.state), LOWER(p.postcode))";
            $this->db->like($search_filter, $params['search']);
        }

        // property services
        if( isset($params['ps_service']) ){
            $this->db->where('ps.`service`', $params['ps_service']);
        }
        if( isset($params['ajt_id']) && $params['ajt_id'] !="" ){
            $this->db->where('ps.`alarm_job_type_id`', $params['ajt_id']);
        }



        //search start - (added by gherx)
        if(!empty($condi['search']['pm'])){
            $this->db->where('p.pm_id_new',$condi['search']['pm']);

        }
        if(!empty($condi['search']['keyword'])){
            $search_filter = "CONCAT_WS(' ', LOWER(p.address_1), LOWER(p.address_2), LOWER(p.address_3), LOWER(p.state), LOWER(p.postcode))";
            $this->db->like($search_filter, $condi['search']['keyword']);
        }
        if(!empty($condi['search']['keyword']) && !empty($condi['search']['pm'])){
            $this->db->where('p.pm_id_new',$condi['search']['pm']);
            $this->db->like("CONCAT_WS('', p.address_1, p.address_2, p.address_3)", $condi['search']['keyword']);
        }
        //search end


        // custom filter
        if( isset($params['custom_where']) ){
            $this->db->where($params['custom_where']);
        }

        $is_nlm_where = '(p.is_nlm = 0 OR p.is_nlm IS NULL)';
        if( isset($params['is_nlm']) ){
            if($params['is_nlm']==0){
                $this->db->where($is_nlm_where);
            }else{
                $this->db->where('p.`is_nlm`', $params['is_nlm']);
            }
            
        }else{//default
            $this->db->where($is_nlm_where);
        }

        // sort
        if( isset($params['sort_list']) ){
            foreach( $params['sort_list'] as $sort_arr ){
                if( $sort_arr['order_by']!="" && $sort_arr['sort']!='' ){
                    $this->db->order_by($sort_arr['order_by'], $sort_arr['sort']);
                }
            }
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

   // update landlord (return object of last modified )
   public function update_landlord($prop_id,$data){
        $where = array('agency_id'=>$this->session->agency_id, 'property_id' => $prop_id);
        $this->db->where($where);
        $this->db->update('property', $data);
        $this->db->limit(1);
        //return ($this->affected_rows()>0)?TRUE:FALSE;

        if($this->db->affected_rows()>0){
            $query = $this->db->select('property_id,landlord_firstname,landlord_lastname,landlord_mob,landlord_ph,landlord_email')->from('property')->where('property_id',$prop_id)->limit(1)->get();;
            return $query->row();
        }else{
            return false;
        }


   }


   /**
    * ADD NEW TENANTS (used for Add Property)
    * Insert tenants by batch
    * param $data array
    * param $type normal/batch insert
    */
   public function add_tenants($data, $batch=false){

        if($batch){ // type is set and = batch insert batch
            $this->db->insert_batch('property_tenants', $data);
            return ($this->db->affected_rows()>0)?true:false;
        }else{ // type not set/normal insert normal
            $this->db->insert('property_tenants',$data);
            return ($this->db->affected_rows()>0)?true:false;
        }

   }


   /**
    * Delete/Deactivate Tenants
    * Activate Tenants
    * active 1 = active
    * active 0 = inactive
    */
   public function active_deactive($tenant_id,$data){

        $this->db->where('property_tenant_id', $tenant_id);
        $this->db->update('property_tenants',$data);
        $this->db->limit(1);
        return ($this->db->affected_rows()>0)?true:false;

   }

   /**
    * Update Tenant Details/Info
    */
    public function update_tenant_details($tenant_id, $data){
        $this->db->where('property_tenant_id', $tenant_id);
        $this->db->update('property_tenants',$data);
        $this->db->limit(1);
        return ($this->db->affected_rows()>0)?true:false;
    }


   /**
    * Get New Tenant from new tenants table
    * params array
    * return object
    */
    public function get_new_tenants($params=array()){

        $this->db->select('pt.property_tenant_id, pt.property_id, pt.tenant_firstname, pt.tenant_lastname, pt.tenant_mobile, pt.tenant_landline, pt.tenant_email');
        $this->db->from('property_tenants as pt');

        if(isset($params['property_id'])){
             if (is_array($params['property_id'])) {
                 $this->db->where_in('property_id', $params['property_id']);
             }
             else {
                 $this->db->where('property_id', $params['property_id']);
             }
        }

        if(!empty($params['active'])){
             $this->db->where('active', $params['active']);
        }

        return $this->db->get()->result();
    }
    /**
     * Get New Tenant from new tenants table
     * params array
     * return object
     */
    public function get_new_tenants_count($params=array()){

        $this->db->select('pt.property_id, COUNT(pt.property_tenant_id) AS new_tenants_count');
        $this->db->from('property_tenants as pt');

        if(isset($params['property_id'])){
             if (is_array($params['property_id'])) {
                 $this->db->where_in('property_id', $params['property_id']);
                 $this->db->group_by('pt.property_id');
             }
             else {
                 $this->db->where('property_id', $params['property_id']);
             }
        }

        if(!empty($params['active'])){
             $this->db->where('active', $params['active']);
        }

        return $this->db->get()->result();
    }

   /**
    * Check if property services has response or not
    */
   public function get_current_services_status($prop_id){
       $query = $this->db->get_where('property_services',array('property_id'=>$prop_id,'service'=>1));
       if($query->num_rows()>0){
           return true;
       }else{
           return false;
       }

   }

   /**
    * Check if services has already added in property_services table
    * return boolean
    */
   public function check_property_services_exist($prop_id, $ajt_id){
        $this->db->select('property_id');
        $this->db->from('property_services');
        $this->db->where('property_id', $prop_id);
        $this->db->where('alarm_job_type_id', $ajt_id);
        $this->db->limit(1);
        $query = $this->db->get();
        return ($query->num_rows()>0)?true:false;
   }

    // Update VPD property Services
    public function vpd_update_property_services($prop_id,$alarm_job_type_id, $data){

        $where = array('property_id' => $prop_id, 'alarm_job_type_id' => $alarm_job_type_id);
        $this->db->where($where);
        $this->db->update('property_services',$data);
        return ($this->db->affected_rows()>0)?TRUE:FALSE;

    }


    public function clear_pm_prop($aua_id){

        $data = array(
                'pm_id_new' => null
        );

        $this->db->where('pm_id_new', $aua_id);
        $this->db->where('deleted', 0);
        $this->db->update('property', $data);

        if( $this->db->affected_rows() > 0 ){
            return true;
        }else{
            return false;
        }

    }


    // property
    public function get_property_data($params){

        if( isset($params['sel_query']) ){
            $sel_query = $params['sel_query'];
        }else{
            $sel_query = '*';
        }

        $this->db->select($sel_query);
        $this->db->from('`property` AS p');
        $this->db->join('`agency_user_accounts` AS aua', 'p.`pm_id_new` = aua.`agency_user_account_id`', 'left');
        $this->db->join('`agency` AS a', 'p.`agency_id` = a.`agency_id`', 'left');

        // filters
        // property
        if( isset($params['property_id']) ){
            $this->db->where('p.`property_id`', $params['property_id']);
        }
        if( isset($params['p_deleted']) ){
            $this->db->where('p.`deleted`', $params['p_deleted']);
        }
        if( isset($params['pm_id']) && $params['pm_id'] != '' ){
            if( $params['pm_id']==0 ){
                $pm_no_assigend_where = "(p.pm_id_new=0 OR p.pm_id_new IS NULL)";
                $this->db->where($pm_no_assigend_where);
            }else{
                $this->db->where("p.`pm_id_new`", $params['pm_id']);
            }
        }

        // agency
        if( isset($params['a_status']) ){
            $this->db->where('a.`status`', $params['a_status']);
        }
        if( isset($params['agency_id']) ){
            $this->db->where('a.`agency_id`', $params['agency_id']);
        }

        // search
        if( isset($params['search']) && $params['search'] != '' ){
            $search_filter = "CONCAT_WS(' ', LOWER(p.address_1), LOWER(p.address_2), LOWER(p.address_3), LOWER(p.state), LOWER(p.postcode))";
            $this->db->like($search_filter, $params['search']);
        }

        // custom filter
        if( isset($params['custom_where']) ){
            $this->db->where($params['custom_where']);
        }

        // sort
        if( isset($params['sort_list']) ){
            foreach( $params['sort_list'] as $sort_arr ){
                if( $sort_arr['order_by']!="" && $sort_arr['sort']!='' ){
                    $this->db->order_by($sort_arr['order_by'], $sort_arr['sort']);
                }
            }
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


    /**
     * Get all job belong to property that is not completed and cancelled
     * @params property_id
     * return object
     */
    public function get_active_job_by_propId($prop_id){

        $where = " property_id=$prop_id AND status!='Completed' AND status!='Cancelled' ";

        $this->db->select('id,status');
        $this->db->from('jobs');
        $this->db->where($where);
        $query = $this->db->get();

        return ($query->num_rows()>0)?$query->result():false;

    }

    //Mark - Also filtered out Deleted jobs
    public function get_active_job_by_propIdV2($prop_id){
        $active_jobs = $this->db->select('status')
            ->from('jobs')
            ->where('property_id', $prop_id)
            ->where('del_job', 0)
            ->where_not_in('status', array('Completed','Cancelled'))
            ->get();

        return ($active_jobs->num_rows()>0)?true:false;

    }


    public function update_key_number($prop_id, $data){

        $where = array('property_id' => $prop_id, 'agency_id' => $this->session->agency_id);
        $this->db->where($where);
        $this->db->update('property',$data);
        return ($this->db->affected_rows()>0)?TRUE:FALSE;

    }

    public function agency_services_check($params){

        $this->db->select('COUNT(agency_services_id) AS as_count');
        $this->db->from('agency_services');
        $this->db->where('service_id', $params['ajt_id']);
        $this->db->where('agency_id', $params['agency_id']);
        $as_sql = $this->db->get();
        if( $params['display_query'] == 1 ){
            echo $this->db->last_query();
        }
        return $as_row = $as_sql->row();

    }

    /**
     * Property next job schedule (last job)
     * @params property_id
     * return row
     */
    public function get_next_schedule_row_by_prop_id($property_id){
        $this->db->select("j.id, j.date, j.status, j.assigned_tech, j.job_type, p.qld_new_leg_alarm_num, p.prop_upgraded_to_ic_sa, .p.state");
        $this->db->from("jobs j");
        $this->db->join('property as p', 'p.property_id = j.property_id','left');
        $this->db->where('j.property_id', $property_id);
        $this->db->where('j.del_job', 0);
        $this->db->order_by('j.date','DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows()>0){
          return $query->row();
        }else{
            return false;
        }
    }

    public function checkjob_alarm_installed_and_is_worked($job_id){
        $this->db->select('*');
        $this->db->from('jobs');
        $this->db->where('id', $job_id);
        $query = $this->db->get();
        if($query->num_rows()>0){
            return $query->row();
        }else{
              return false;
        }
    }

    public function attach_last_service_row_to_list(&$list) {
        $propertyIds = [];

        for ($x = 0; $x < count($list); $x++) {
            $list[$x]->last_service = null;
            if ($list[$x]->property_id) {
                $propertyIds[] = $list[$x]->property_id;
            }
        }

        if (!empty($propertyIds)) {
            $propertyIds = array_unique($propertyIds);

            $subQuery = $this->db->select("j.property_id, max(j.date) AS latest_date")
                ->from('jobs AS j')
                ->where_in('j.property_id', $propertyIds)
                ->where_in('j.status', ['Completed', 'Merged Certificates'])
                ->where('j.assigned_tech!=', 1)
                ->where('j.assigned_tech!=', 2)
                ->where('j.assigned_tech!=', NULL)
                ->where('j.del_job', 0)
                ->group_by('j.property_id')
                ->get_compiled_select();

            $this->db->reset_query();

            $latestServices = $this->db->select("j.id, j.date, j.status, j.assigned_tech, j.job_type, j.property_id, p.qld_new_leg_alarm_num, p.prop_upgraded_to_ic_sa, p.state")
                ->from("jobs j")
                ->join('property as p', 'p.property_id = j.property_id', 'left')
                ->join("({$subQuery}) AS j2", "j2.property_id = j.property_id AND j2.latest_date = j.date", "inner")
                ->where('j.del_job', 0)
                ->get()->result();

            $latestServicesByPropertyId = [];

            for ($x = 0; $x < count($latestServices); $x++) {
                $latestServicesByPropertyId[$latestServices[$x]->property_id] =& $latestServices[$x];
            }

            for ($x = 0; $x < count($list); $x++) {
                $obj =& $list[$x];

                if (isset($latestServicesByPropertyId[$obj->property_id])) {
                    $obj->last_service =& $latestServicesByPropertyId[$obj->property_id];
                }
            }
        }

    }

    public function attach_next_schedule_row_to_list(&$list) {
        $propertyIds = [];

        for ($x = 0; $x < count($list); $x++) {
            $list[$x]->next_schedule = null;
            if ($list[$x]->property_id) {
                $propertyIds[] = $list[$x]->property_id;
            }
        }

        if (!empty($propertyIds)) {
            $propertyIds = array_unique($propertyIds);

            $subQuery = $this->db->select("j.property_id, max(j.date) AS latest_date")
                ->from('jobs AS j')
                ->where_in('j.property_id', $propertyIds)
                ->where('j.del_job', 0)
                ->group_by('j.property_id')
                ->get_compiled_select();

            $this->db->reset_query();

            $nextSchedules = $this->db->select("j.id, j.date, j.status, j.assigned_tech, j.job_type, j.property_id, p.qld_new_leg_alarm_num, p.prop_upgraded_to_ic_sa, .p.state")
                ->from("jobs j")
                ->join('property as p', 'p.property_id = j.property_id', 'left')
                ->join("({$subQuery}) AS j2", "j2.property_id = j.property_id AND j2.latest_date = j.date", "inner")
                ->where('j.del_job', 0)
                ->get()->result();

            $nextSchedulesByPropertyId = [];

            for ($x = 0; $x < count($nextSchedules); $x++) {
                $nextSchedulesByPropertyId[$nextSchedules[$x]->property_id] =& $nextSchedules[$x];
            }

            for ($x = 0; $x < count($list); $x++) {
                $obj =& $list[$x];

                if (isset($nextSchedulesByPropertyId[$obj->property_id])) {
                    $obj->next_schedule =& $nextSchedulesByPropertyId[$obj->property_id];
                }
            }
        }

    }

    public function attach_alarm_details_to_list(&$list) {
        $propertyIds = [];

        for ($x = 0; $x < count($list); $x++) {
            $list[$x]->alarm_details = [];
            if ($list[$x]->property_id) {
                $propertyIds[] = $list[$x]->property_id;
            }
        }

        if (!empty($propertyIds)) {
            $propertyIds = array_unique($propertyIds);

            $subQuery = $this->db->select("j.property_id, max(j.date) AS latest_date")
                ->from('jobs AS j')
                ->where_in('j.property_id', $propertyIds)
                ->where('j.status','Completed')
                ->where('j.assigned_tech!=', NULL)
                ->where('j.assigned_tech!=', 1)
                ->where('j.del_job', 0)
                ->group_by('j.property_id')
                ->get_compiled_select();

            $this->db->reset_query();

            $alarmDetails = $this->db->select("j.property_id, a.ts_position, ap.alarm_pwr, ap.battery_type, ap.is_replaceable, ap.alarm_pwr_source, at.alarm_type, a.make, a.model, a.expiry, a.ts_required_compliance, a.ts_newbattery")
                ->from("alarm AS a")
                ->join('alarm_pwr ap', 'ap.alarm_pwr_id =  a.alarm_power_id','left')
                ->join('alarm_type at','at.alarm_type_id = a.alarm_type_id','left')
                ->join("jobs as j", "j.id = a.job_id", "inner")
                ->join("({$subQuery}) AS j2", "j2.property_id = j.property_id AND j2.latest_date = j.date", "inner")
                ->where('j.del_job', 0)
                ->where('a.ts_discarded', 0)
                ->get()->result();

            for ($x = 0; $x < count($list); $x++) {
                $obj =& $list[$x];

                $obj->alarm_details = array_filter($alarmDetails, function($a) use ($obj) {
                    return $a->property_id == $obj->property_id;
                });
            }
        }

    }

    public function attach_new_tenants_to_list(&$list) {

        $propertyIds = [];

        for ($x = 0; $x < count($list); $x++) {
            $list[$x]->new_tenants = [];
            if ($list[$x]->property_id) {
                $propertyIds[] = $list[$x]->property_id;
            }
        }

        if (!empty($propertyIds)) {
            $propertyIds = array_unique($propertyIds);

            $newTenants = $this->get_new_tenants([
                'property_id' => $propertyIds,
                'active' => 1,
            ]);

            for ($x = 0; $x < count($list); $x++) {
                $obj =& $list[$x];
                $obj->new_tenants = array_filter($newTenants, function($tenant) use ($obj) {
                    return $tenant->property_id == $obj->property_id;
                });
            }
        }
    }

    public function attach_new_tenants_count_to_list(&$list) {

        $propertyIds = [];

        for ($x = 0; $x < count($list); $x++) {
            $list[$x]->new_tenants_count = 0;
            if ($list[$x]->property_id) {
                $propertyIds[] = $list[$x]->property_id;
            }
        }

        if (!empty($propertyIds)) {
            $propertyIds = array_unique($propertyIds);

            $newTenantsCounts = $this->get_new_tenants_count([
                'property_id' => $propertyIds,
                'active' => 1,
            ]);

            $newTenantsCountsByPropertyId = [];

            for ($x = 0; $x < count($newTenantsCounts); $x++) {
                $newTenantsCountsByPropertyId[$newTenantsCounts[$x]->property_id] =& $newTenantsCounts[$x];
            }

            for ($x = 0; $x < count($list); $x++) {
                $obj =& $list[$x];
                if (isset($newTenantsCountsByPropertyId[$obj->property_id])) {
                    $obj->new_tenants_count = $newTenantsCountsByPropertyId[$obj->property_id]->new_tenants_count;
                }
            }
        }
    }

    public function getKeyMapRoutes($params){

        if (isset($params['sel_query'])) {
            $sel_query = $params['sel_query'];
        } else {
            $sel_query = '*';
        }

        $this->db->select($sel_query);
        $this->db->from('tech_run_keys AS kr');
        $this->db->join('agency as a',"a.agency_id = kr.agency_id",'left');
        $this->db->join('staff_accounts as sa',"sa.StaffID = kr.assigned_tech",'left');
        $this->db->where('kr.tech_run_keys_id >',0);

        if( $params['date'] != "" && $params['date'] ){
            $this->db->where('kr.date', $params['date']);
        }

        if( $params['agency_id'] != "" && $params['agency_id'] ){
            $this->db->where('kr.agency_id', $params['agency_id']);
        }

        if( $params['tech_id'] != "" && $params['tech_id'] ){
            $this->db->where('kr.assigned_tech', $params['tech_id']);
        }

        if(  is_numeric($params['completed']) && $params['completed'] ){
            $this->db->where('kr.completed', $params['completed']);
        }

        if(  $params['country_id'] != '' && $params['country_id'] ){
            $this->db->where('a.country_id', $params['country_id']);
        }

        // sort
        if (isset($params['sort_list'])) {
            foreach ($params['sort_list'] as $sort_arr) {
                if ($sort_arr['order_by'] != "" && $sort_arr['sort'] != '') {
                    $this->db->order_by($sort_arr['order_by'], $sort_arr['sort']);
                }
            }
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

    public function getKeyMapRoutesCount($params){

        $this->db->select("COUNT(kr.tech_run_keys_id) AS j_count");
        $this->db->from('tech_run_keys AS kr');
        $this->db->join('agency as a',"a.agency_id = kr.agency_id",'left');
        $this->db->join('staff_accounts as sa',"sa.StaffID = kr.assigned_tech",'left');
        $this->db->where('kr.tech_run_keys_id >',0);

        if( $params['date'] != "" && $params['date'] ){
            $this->db->where('kr.date', $params['date']);
        }

        if( $params['agency_id'] != "" && $params['agency_id'] ){
            $this->db->where('kr.agency_id', $params['agency_id']);
        }

        if( $params['tech_id'] != "" && $params['tech_id'] ){
            $this->db->where('kr.assigned_tech', $params['tech_id']);
        }

        if(  is_numeric($params['completed']) && $params['completed'] ){
            $this->db->where('kr.completed', $params['completed']);
        }

        if(  $params['country_id'] != '' && $params['country_id'] ){
            $this->db->where('a.country_id', $params['country_id']);
        }

        // sort
        if (isset($params['sort_list'])) {
            foreach ($params['sort_list'] as $sort_arr) {
                if ($sort_arr['order_by'] != "" && $sort_arr['sort'] != '') {
                    $this->db->order_by($sort_arr['order_by'], $sort_arr['sort']);
                }
            }
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

    public function get_property_list_non_sats_ver3( $agency_id,$propNotIn,$condi=array(), $params )
    {
        $concat_select = isset($params['concat_select']) && !empty($params['concat_select']) ? $params['concat_select'] : null;
        if(isset($params['sel_query']) && $params['sel_query']==1){
            $sel_query = "p.property_id, p.address_1, p.address_2, p.address_3, p.state, p.postcode, p.property_managers_id as p_property_managers_id, p.pm_id_new, p.nlm_display, a.agency_id, aua.fname as pm_fname, aua.lname as pm_lname, aua.agency_user_account_id, aua.photo {$concat_select}";
        }elseif($params['sel_query'] && $params['sel_query']!=1){
            $sel_query = $params['sel_query'];
        }

        $this->db->select($sel_query);
        $this->db->from('property as p');
        $this->db->join('agency as a','a.agency_id = p.agency_id','left');
        $this->db->join('agency_user_accounts as aua','aua.agency_user_account_id = p.pm_id_new AND aua.`active` = 1','left');

        // multiple custom joins
        if( !empty($params['custom_joins_arr']) ){
            foreach( $params['custom_joins_arr'] as $custom_joins ){
                $this->db->join($custom_joins['join_table'], $custom_joins['join_on'], $custom_joins['join_type']);
            }
        }

        $this->db->where('p.agency_id', $agency_id);
        if(!empty($propNotIn)){
            $this->db->where_not_in('p.property_id', $propNotIn);
        }
        //$this->db->where("(p.deleted = 0 OR (p.deleted = 1 AND p.nlm_display = 1))"); ## disabled > removed nlm_display stuff
        $this->db->where("(p.is_nlm = 0 OR p.is_nlm IS NULL)");
        $this->db->where('p.deleted',0); ## new update

        $address_search_filter = "CONCAT_WS(' ', LOWER(p.address_1), LOWER(p.address_2), LOWER(p.address_3), LOWER(p.state), LOWER(p.postcode))";
        //search
        if(!empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // 3 serch field has value
            $this->db->like($address_search_filter, $condi['search']['keyword']);
            $this->db->where("p.pm_id_new", $condi['search']['pm']);
           // $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
         }else if(!empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // keyword and pm
             $this->db->like($address_search_filter, $condi['search']['keyword']);
             $this->db->where("p.pm_id_new", $condi['search']['pm']);
         }else if(!empty($condi['search']['keyword']) && empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ //keyword and service type
             $this->db->like($address_search_filter, $condi['search']['keyword']);
            // $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
         }else if(empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // pm and service type
             $this->db->where("p.pm_id_new", $condi['search']['pm']);
            // $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
         }else if(!empty($condi['search']['keyword']) && empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // only keyword
             $this->db->like($address_search_filter, $condi['search']['keyword']);
         }else if(empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // only pm
             $this->db->where("p.pm_id_new", $condi['search']['pm']);
         }else if(empty($condi['search']['keyword']) && empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // only service type
            // $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
         }

        if( isset($params['pm_id']) && $params['pm_id'] !="" ){
            if( $params['pm_id']==0 ){
                $pm_no_assigend_where = "(p.pm_id_new=0 OR p.pm_id_new IS NULL)";
                $this->db->where($pm_no_assigend_where);
            }else{
                $this->db->where("p.`pm_id_new`", $params['pm_id']);
            }
        }

        // limit and offset
        if(array_key_exists('limit',$condi) && array_key_exists('offset',$condi)){
            $this->db->limit($condi['limit'],$condi['offset']);
        }else if(array_key_exists('limit',$condi) && !array_key_exists('offset',$condi)){
            $this->db->limit($condi['limit']);
        }

        $this->db->group_by('p.property_id');
        $this->db->order_by('p.address_2','ASC');

        $query = $this->db->get();
        return $query;

    }

    public function get_properties($params) {

        if (isset($params['sel_query'])) {
            $sel_query = $params['sel_query'];
        } else {
            $sel_query = '*';
        }

        $this->db->select($sel_query);
        $this->db->from('`property` AS p');
        $this->db->join('`agency` AS a', 'p.`agency_id` = a.`agency_id`', 'left');
        $this->db->join('`agency_user_accounts` AS aua', 'p.`pm_id_new` = aua.`agency_user_account_id`', 'left');
        $this->db->join('`api_property_data` AS apd', 'p.`property_id` = apd.`crm_prop_id`', 'left');

        // set joins
        if (!empty($params['join_table'])) {

            foreach ($params['join_table'] as $join_table) {

                if ($join_table == 'property_services') {
                    $this->db->join('`property_services` AS ps', 'p.`property_id` = ps.`property_id`', 'inner');
                }

                if ($join_table == 'jobs') {
                    $this->db->join('`jobs` AS j', 'p.`property_id` = j.`property_id`', 'inner');
                }

                if ($join_table == 'staff_accounts') {
                    $this->db->join('`staff_accounts` AS sa', 'sa.`StaffID` = a.`salesrep`', 'left');
                }

                if ($join_table == 'countries') {
                    $this->db->join('`countries` AS c', 'a.`country_id` = c.`country_id`', 'left');
                }
            }
        }

        // custom joins
        if (isset($params['custom_joins']) && $params['custom_joins'] != '') {
            $this->db->join($params['custom_joins']['join_table'], $params['custom_joins']['join_on'], $params['custom_joins']['join_type']);
        }

        // filters
        // property
        if (isset($params['property_id'])) {
            $this->db->where('p.`property_id`', $params['property_id']);
        }
        if (isset($params['p_deleted'])) {
            $this->db->where('p.`deleted`', $params['p_deleted']);
        }
        if (isset($params['pm_id']) && $params['pm_id'] != '') {
            if( $params['pm_id']==0 ){
                $pm_no_assigend_where = "(p.pm_id_new=0 OR p.pm_id_new IS NULL)";
                $this->db->where($pm_no_assigend_where);
            }else{
                $this->db->where("p.`pm_id_new`", $params['pm_id']);
            }
        }
        if (isset($params['job_id']) && $params['job_id'] != '') {
            $this->db->where('j.`id`', $params['job_id']);
        }
        if (isset($params['ps_service']) && $params['ps_service'] != '') {
            $this->db->where('ps.`service`', $params['ps_service']);
        }

        // agency filters
        if (isset($params['agency_filter']) && $params['agency_filter'] != '') {
            $this->db->where('a.`agency_id`', $params['agency_filter']);
        }
        if (isset($params['a_status'])) {
            $this->db->where('a.`status`', $params['a_status']);
        }
        if (isset($params['a_deleted'])) {
            $this->db->where('p.`agency_deleted`', $params['a_deleted']);
        }

        // Date filters
        if (isset($params['date']) && $params['date'] != '') {
            $this->db->where('p.`deleted_date`', $params['date']);
        }

        // search
        if (isset($params['search']) && $params['search'] != '') {
            $search_filter = "CONCAT_WS(' ', LOWER(p.address_1), LOWER(p.address_2), LOWER(p.address_3), LOWER(p.state), LOWER(p.postcode))";
            $this->db->like($search_filter, $params['search']);
        }

        // postcodes
        if (isset($params['postcodes']) && $params['postcodes'] != '') {
            $this->db->where("p.`postcode` IN ( {$params['postcodes']} )");
        }

        //state
        if (isset($params['state_filter']) && $params['state_filter'] != '') {
            $this->db->where('p.`state`', $params['state_filter']);
        }

        if (is_numeric($params['country_id'])) {
            $this->db->where('a.`country_id`', $params['country_id']);
        }

        // Electrician Only(EO)
        if ( is_numeric($params['is_eo']) ) {
            $this->db->where('j.`is_eo`', $params['is_eo']);
        }


        // custom filter
        if ( $params['custom_where'] != '' ) {
            $this->db->where($params['custom_where']);
        }

        // custom filter
        if (isset($params['custom_where_arr'])) {
            foreach ($params['custom_where_arr'] as $index => $custom_where) {
                if ($custom_where != '') {
                    $this->db->where($custom_where);
                }
            }
        }

        // group by
        if (isset($params['group_by']) && $params['group_by'] != '') {
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

        // limit
        if (isset($params['limit']) && $params['limit'] > 0) {
            $this->db->limit($params['limit'], $params['offset']);
        }


        $query = $this->db->get();
        if (isset($params['display_query']) && $params['display_query'] == 1) {
            echo $this->db->last_query();
        }

        return $query;
    }

    /**
     * param > agent_nlm_reason > applicable if request was from VPD Portal
     * param >job_id > use to cancel jobs log (escalate and service_due page)
     *
     */
    public function nlm_property($prop_id, $params){

        if($prop_id<=0){ //property id validate
            echo "Error: Empty ID";exit();
        }
        // leaving reason data
        $reason_they_left = $params['reason_they_left'];
        $other_reason = $params['other_reason'];

        if( is_numeric($reason_they_left) ){

            // insert agency leaving reason
            $agency_res_insert_data = array(
                'property_id' => $prop_id,
                'reason' => $reason_they_left
            );

            // "other" reason
            if( $reason_they_left == -1 ){
                $agency_res_insert_data['other_reason'] = $other_reason;
                $reason_logs = $other_reason;
            } else {
                $get_reason = $this->db->select('reason')->from('leaving_reason')->where('id', $reason_they_left)->get()->row_array();
                $reason_logs = $get_reason['reason'];
            }

            $this->db->insert('property_nlm_reason', $agency_res_insert_data);

        }

        //update property to nlm
       /* $update_property_data = array(
            'agency_deleted' => 1,
            'deleted' => 1,
            'deleted_date' => date('Y-m-d H:i:s'),
            'booking_comments' => "No longer managed as of ".date("d/m/Y")." - by agency.",
            'is_nlm' => 1,
            'nlm_timestamp' => date('Y-m-d H:i:s'),
            'nlm_by_agency' => $this->session->agency_id
        );*/

        ##new NLM remove deleted  and deleted_date field
        $update_property_data = array(
            'agency_deleted' => 1,
            'booking_comments' => "No longer managed as of ".date("d/m/Y")." - by agency.",
            'reason' => $params['agent_nlm_reason'],
            'is_nlm' => 1,
            'nlm_timestamp' => date('Y-m-d H:i:s'),
            'nlm_by_agency' => $this->session->agency_id
        );

        // unlink property
        if( $prop_id > 0 ){
            $this->db->delete('api_property_data', array('crm_prop_id' => $prop_id));
        }

        // check if property has money owing and needs to verify paid
        if( $this->system_model->check_verify_paid($prop_id) == true ){
            $update_property_data['nlm_display'] = 1;
        }
        $data_update_property = $this->update_property($prop_id,$update_property_data);
        //update property to nlm end

        if( $prop_id > 0 ){

            // get jobs to be cancelled, except completed jobs
            $to_cancel_jobs_sql = $this->db->query("
            SELECT `id` AS jid
            FROM `jobs`
            WHERE `property_id` = {$prop_id}
            AND `status` != 'Completed'
            ");

            foreach( $to_cancel_jobs_sql->result() as $to_cancel_jobs_row ){

                if( $to_cancel_jobs_row->jid > 0 ){

                    // Cancel jobs
                    $this->db->query("
                    UPDATE `jobs`
                    SET
                        `status` = 'Cancelled',
                        `comments` = 'This property was marked No Longer Managed by ".$this->gherxlib->agent_full_name()." on ".date("d/m/Y")." and all jobs cancelled',
                        `cancelled_date` = '".date('Y-m-d')."'
                    WHERE `id` = {$to_cancel_jobs_row->jid}
                    AND `property_id` = {$prop_id}
                    ");
                    // Cancel jobs end

                    // insert log for cancelled jobs
                    $details = "Job <strong>Cancelled</strong> due to <strong>NLM</strong> by Agency";
                    $params_job_log = array(
                        'title' => 20, // Job Cancelled
                        'details' => $details,
                        'display_in_vjd' => 1,
                        'agency_id' => $this->session->agency_id,
                        'created_by' => $this->session->aua_id,
                        'property_id' => $prop_id,
                        'job_id' => $to_cancel_jobs_row->jid
                    );
                    $this->jcclass->insert_log($params_job_log);

                }

            }
            
        }

        //update property_services
        // if property has completed job with a price this month and service changed this month
        $this_month_start = date("Y-m-01");
        $this_month_end = date("Y-m-t");

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

        $clear_is_payable = null;
        $payable = '';
        if( $job_sql->num_rows() > 0 && $ps_sql->num_rows() > 0 ){

            // DO nothing, leave is_payable as it is

        }else{

            // clear is_payable
            $clear_is_payable = "`is_payable` = 0,";
            $payable = '0';

        }
        //update property_services end

        // loop through existing property services
        $ps_sql2 = $this->db->query("
        SELECT
            ps.`property_services_id` AS ps_id,
            ps.`is_payable`,
            ajt.`type` AS service_type_name
        FROM `property_services` AS ps
        LEFT JOIN `alarm_job_type` AS ajt ON ps.`alarm_job_type_id` = ajt.`id`
        WHERE ps.`property_id` = {$prop_id}
        AND ps.`service` NOT IN(0,3)
        AND ps.`service` = 1
        ");

        foreach( $ps_sql2->result() as $ps_row2 ){

            if( $ps_row2->ps_id > 0 ){

                $this->db->query("
                UPDATE `property_services`
                SET
                    `service` = 2,
                    {$clear_is_payable}
                    `status_changed` = '".date('Y-m-d H:i:s')."'
                WHERE `property_services_id` = {$ps_row2->ps_id}
                AND `property_id` = {$prop_id}
                ");

                if ($payable == '0') {
                    $details =  "Property Service <b>{$ps_row2->service_type_name}</b> unmarked <b>payable</b>";
                    $params = array(
                        'title' => 3, // Property Service Updated
                        'details' => $details,
                        'display_in_vpd' => 1,
                        'agency_id' => $this->session->agency_id,
                        'created_by' => $this->session->aua_id,
                        'property_id' => $prop_id
                    );
                    $this->jcclass->insert_log($params);
                }
            }

        }

        //Insert cancel job log  > service_due and escalate page
        if($params['job_id']>0){
            $details = "Job <strong>Cancelled</strong> due to <strong>NLM</strong> by Agency";
            $params_job_log = array(
                'title' => 20, //Job Cancelled
                'details' => $details,
                'display_in_vjd' => 1,
                'agency_id' => $this->session->agency_id,
                'created_by' => $this->session->aua_id,
                'property_id' => $prop_id,
                'job_id' => $params['job_id']
            );
            $this->jcclass->insert_log($params_job_log);
        }
        //Insert cancel job log end

        if ($params['agent_nlm_from']!='') {
            $nlm_date = $params['agent_nlm_from'];
        } else {
            $nlm_date = date("d/m/Y");
        }
        //Insert Property Log
        if($reason_logs!=""){
            //$details_vpd_log = "{p_address} has been marked as No Longer Managed by {$this->gherxlib->agent_full_name()} <br/> Details: ".$params['agent_nlm_reason'];
            $details_vpd_log = "{p_address} has been marked as No Longer Managed by {$this->gherxlib->agent_full_name()}, Details: {$reason_logs}, NLM date: {$nlm_date}";
        }else{
            $details_vpd_log = "{p_address} has been marked as No Longer Managed by {$this->gherxlib->agent_full_name()}";
        }
       // $details_vpd_log =  "{p_address} has been marked as No Longer Managed by {$this->gherxlib->agent_full_name()}".($params['agent_nlm_reason']!="")?'<br/> Details: '.$params['agent_nlm_reason']:null;
       
       $params_vpd_log = array(
            'title' => 3, // Property Service Updated
            'details' => $details_vpd_log,
            'display_in_vpd' => 1,
            'display_in_vad' => 1,
            'display_in_portal' => 1,
            'agency_id' => $this->session->agency_id,
            'created_by' => $this->session->aua_id,
            'property_id' => $prop_id,
        );
        $this->jcclass->insert_log($params_vpd_log);
        //Insert Log end

        // NLM Email Notification
        // if($this->config->item('country')==1){ //Email for AU only
            $noti_params = array('property_id' => $prop_id);
            $this->nlm_email_notification($noti_params);
        // }
        // NLM Email Notification end


    }

    public function nlm_email_notification($params){

        ##get property details
        $p_params = array(
            'sel_query'=> "p.property_id, p.address_1, p.address_2, p.address_3",
            'property_id' => $params['property_id']
        );
        $prop_q = $this->get_properties($p_params);
        $prop_row = $prop_q->row_array();

        $email_data['prop_id'] = $prop_row['property_id'];
        $email_data['prop_name'] = "{$prop_row['address_1']} {$prop_row['address_2']}, {$prop_row['address_3']}";

        $getCountryInfo = $this->gherxlib->getCountryViaCountryId();

        $email_to = make_email('accounts');
        $email_subject = "Property NLM Notification";
        $body = $this->load->view('/emails/nlm_email_notification.php', $email_data, TRUE);

        $this->email->to($email_to);
        $this->email->subject($email_subject);
        $this->email->message($body);
        $this->email->send();

    }

    public function get_no_compliant_prop_for_properties_page($params){

        if( $params['sel_query']!="" ){
            $sel_query = $params['sel_query'];
        }else{
            $sel_query = '*';
        }

        $str_paginate = "";
        if( $params['paginate']!="" ){
            $str_paginate .="LIMIT {$params['paginate']['offset']}, {$params['paginate']['limit']} ";
        }

        // filters
        $filter_arr = []; // clear
        if( $params['pm_id']!="" ){
            $filter_arr[] = "AND p.`pm_id_new` = {$params['pm_id']}";
        }

        if( $params['service']!="" ){
            $filter_arr[] = "AND j1.`service` = {$params['service']}";
        }

        if( $params['search']!="" ){
            $filter_arr[] = "AND (
                CONCAT_WS(' ', LOWER(p.address_1), LOWER(p.address_2), LOWER(p.address_3), LOWER(p.state), LOWER(p.postcode)) LIKE '%{$params['search']}%'
             )";
        }

        // combine all filters
        $filter_arr_imp = null;
        if (count($filter_arr) > 0) {
            $filter_arr_imp = implode(' ', $filter_arr);
        }

        $today = date('Y-m-d');

        $tt_no_compliant_q = "
        SELECT {$sel_query}
        FROM jobs as j1
        LEFT JOIN `property` AS `p` ON j1.`property_id` = p.`property_id`
        LEFT JOIN `agency_user_accounts` as `aua` ON p.`pm_id_new` =  aua.`agency_user_account_id` AND aua.`active` = 1
        LEFT JOIN `agency` AS `a` ON p.`agency_id` = a.`agency_id`
        LEFT JOIN `extra_job_notes` AS `ejn` ON  j1.`id` = ejn.`job_id`
        WHERE j1.id = (SELECT id
                    FROM jobs as j2
                    WHERE j2.property_id = j1.property_id
                    ORDER BY j2.date DESC
                    LIMIT 1)
        AND `j1`.`del_job` = 0
        AND `a`.`country_id` = {$this->session->country_id}
        AND `p`.`deleted` = 0
        AND (`p`.`is_nlm` = 0 OR `p`.`is_nlm` IS NULL)
        AND `a`.`status` = 'active'
        AND `a`.`agency_id` = {$this->session->agency_id}
        AND (`j1`.`prop_comp_with_state_leg` = 0 OR  (p.state='QLD' AND p.prop_upgraded_to_ic_sa=0) )
        AND j1.assigned_tech NOT IN(1,2)
        {$filter_arr_imp}
        GROUP BY j1.property_id
        {$str_paginate}
    ";
    $q = $this->db->query($tt_no_compliant_q);

    return $q;

    }

    public function countpropertyJobs($prop_id){
        return $this->db->select('id')
        ->from('jobs')
        ->where('property_id', $prop_id)
        ->where('status', 'Booked')
        ->where('job_entry_notice ', 1)
        ->get()
        ->num_rows();
    }

    public function get_property_list_non_serviced($agency_id,$propNotIn,$condi=array()){

        if( !empty($condi['pm_distinct']) ){
            $this->db->distinct($condi['pm_distinct']);
        }else{
            $this->db->distinct('ps.property_id');
        }

        if( !empty($condi['sel_query']) ){
            $sel_query = $condi['sel_query'];
            $this->db->select($sel_query);
        }else{
            $this->db->select("ps.property_id,ps.service, p.address_1, p.address_2, p.address_3, p.state, p.postcode, p.property_managers_id as p_property_managers_id, p.pm_id_new, p.nlm_display, a.agency_id, aua.fname as pm_fname, aua.lname as pm_lname, aua.agency_user_account_id, aua.photo");
        }

        $this->db->from('property_services as ps');
        $this->db->join('property as p', 'p.property_id =  ps.property_id','left');
        $this->db->join('agency as a', 'a.agency_id = p.agency_id','left');
        $this->db->join('alarm_job_type as ajt','ajt.id = ps.alarm_job_type_id','left');
        $this->db->join('agency_user_accounts as aua', 'aua.agency_user_account_id =  p.pm_id_new', 'left');
        $this->db->where_not_in('ps.property_id', $propNotIn);
        $this->db->where('p.agency_id', $agency_id);
        $this->db->where('ps.service !=',1);
        $this->db->where('ps.alarm_job_type_id !=',0);
        $this->db->where('p.deleted',0);
        $this->db->where('a.status','active');
        $this->db->where(" (p.is_nlm = 0 OR p.is_nlm IS NULL) ");
        //$this->db->where('aua.agency_id',$agency_id);


        $address_search_filter = "CONCAT_WS(' ', LOWER(p.address_1), LOWER(p.address_2), LOWER(p.address_3), LOWER(p.state), LOWER(p.postcode))";
        //search
        if(!empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // 3 serch field has value
            $this->db->like($address_search_filter, $condi['search']['keyword']);
            $this->db->where("p.pm_id_new", $condi['search']['pm']);
            $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
         }else if(!empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // keyword and pm
             $this->db->like($address_search_filter, $condi['search']['keyword']);
             $this->db->where("p.pm_id_new", $condi['search']['pm']);
         }else if(!empty($condi['search']['keyword']) && empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ //keyword and service type
             $this->db->like($address_search_filter, $condi['search']['keyword']);
             $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
         }else if(empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // pm and service type
             $this->db->where("p.pm_id_new", $condi['search']['pm']);
             $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
         }else if(!empty($condi['search']['keyword']) && empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // only keyword
             $this->db->like($address_search_filter, $condi['search']['keyword']);
         }else if(empty($condi['search']['keyword']) && !empty($condi['search']['pm']) && empty($condi['search']['servType'])){ // only pm
            // $this->db->where("p.pm_id_new", $condi['search']['pm']);
         }else if(empty($condi['search']['keyword']) && empty($condi['search']['pm']) && !empty($condi['search']['servType'])){ // only service type
             $this->db->where("ps.alarm_job_type_id", $condi['search']['servType']);
         }

         if( isset($condi['search']['pm']) && $condi['search']['pm']!="" ){
            if( $condi['search']['pm']==0 ){
                $pm_no_assigend_where = "(p.pm_id_new=0 OR p.pm_id_new IS NULL)";
                $this->db->where($pm_no_assigend_where);
            }else{
                $this->db->where("p.`pm_id_new`",  $condi['search']['pm']);
            }
         }

         // limit and offset
        if(array_key_exists('limit',$condi) && array_key_exists('offset',$condi)){
            $this->db->limit($condi['limit'],$condi['offset']);
        }else if(array_key_exists('limit',$condi) && !array_key_exists('offset',$condi)){
            $this->db->limit($condi['limit']);
        }

        if( !empty($condi['sort_by']) && !empty($condi['sort']) ){
            $this->db->order_by($condi['sort_by'], $condi['sort']);
        }

        if(!empty($condi['group_by'])){
            $this->db->group_by($condi['group_by']);
        }

        $query = $this->db->get();
        return ($query->num_rows()>0)?$query->result():FALSE;

    }

    /**
     * get Property Status return is_nlm column
     * @param $property_id
     * @return mixed
     */
    public function get_property_status($property_id)
    {
        $data = $this->db->select("p.is_nlm")->from('property as p')->where('property_id', $property_id)->get()->row();
        return $data->is_nlm;
    }
    
    /**
     * get property addresses based on agency for autocomplete
     * @return array
     */
    public function get_address()
    {
        $agency_id = $this->session->agency_id;
        
        $result = $this->db->select('CONCAT(address_1, " ", address_2, ", ", address_3, ", ",state, ", ", postcode) as full_address')
            ->from('property')
            ->where('agency_id', $agency_id)
            ->get()
            ->result_array();
        
        $addresses = [];
        foreach ($result as $row) {
            $addresses[] = $row['full_address'];
        }
        
        return $addresses;
    }
}
