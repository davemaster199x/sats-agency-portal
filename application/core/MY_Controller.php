<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * SATS Custom Controller Class
 *
 * We can use this class to set debugging for all controllers when not on production
 * Though we want to ensure for security its not shown to general public
 *
 * @property Output $output
 * @property DB $db
 * @property System_model $system_model
 * @property Session $session
 */
class MY_Controller extends CI_Controller {
	public function __construct() {
        parent::__construct();

        // If its NOT production AND if its NOT an ajax request then show profiling
        if(
            ENVIRONMENT != 'production' &&
            (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest')
        ){
            // https://codeigniter.com/userguide3/general/profiling.html?highlight=profiler
            $this->output->enable_profiler(TRUE);
	    }
	}
}