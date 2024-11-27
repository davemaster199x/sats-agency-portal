<?php

class Agency_maintenance_model extends MY_Model 
{
    public $table = 'agency_maintenance';
    public $primary_key = 'agency_maintenance_id';

    public function __construct() {
        $this->has_one['maintenance'] = array('Maintenance_model', 'maintenance_id', 'maintenance_id');

        parent::__construct();
    }

}