<?php

class States_def_model extends MY_Model 
{
    public $table = 'states_def';
    public $primary_key = 'StateID';

    public function __construct() {
        parent::__construct();
    }

}