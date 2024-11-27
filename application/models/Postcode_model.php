<?php

class Postcode_model extends MY_Model 
{
    public $table = 'postcode';
    public $primary_key = 'id';

    public function __construct() {
        parent::__construct();
    }

}