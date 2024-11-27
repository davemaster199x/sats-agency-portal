<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use App\Exceptions\HttpException;
use Exception;

class PageLoadHooks {

    protected $CI;
    protected $startTime;

    public function registerStart() {
        $this->startTime = microtime(true);
    }

    public function recordDuration() {
        $this->CI =& get_instance();

        $controller = $this->CI->router->fetch_class();
        $action = $this->CI->router->fetch_method();

        $page = "{$controller}/{$action}";

        $trackedPages = [
            "compliance/nsw_inspection_details",
            "properties/index",
            "jobs/index",
            "jobs/help_needed",
            "jobs/service_due",
            "reports/completed_jobs",
            "reports/active_services",
            "reports/new_tenancy",
            "reports/non_compliant",
            "reports/qld_upgrade",
            "reports/qld_upgrade_quotes",
            "reports/approved_qld_upgrade_quotes",
            "reports/upgraded_properties",
            "reports/subscription_dates",
            "reports/key_pick_up",
        ];

        if (in_array($page, $trackedPages)) {

            $endTime = microtime(true);

            $duration = $endTime - $this->startTime;

            $db =& $this->CI->db;

            $db->insert('logged_page_durations', [
                'page' => "agency-portal:".$page,
                'duration' => round($duration * 1000),
                'created' => date('Y-m-d H:i:s'),
            ]);
        }
    }

}