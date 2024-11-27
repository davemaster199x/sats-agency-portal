<?php

class Maintenance_model extends MY_Model 
{
    public $table = 'maintenance';
    public $primary_key = 'maintenance_id';

    public function __construct() {
        $this->has_many['agency_maintenance'] = "Agency_maintenace_model";

        parent::__construct();
    }

    public function get_all_by_status_ordered_by_name($status = 1) {
        // Assume get_all method exists in MY_Model
        $this->db->order_by('name', 'asc');
        
        // Assume get_all method exists in MY_Model
        return $this->get_all(array('status' => $status));
    }

}