<?php

if (!function_exists('theme')) {
    function theme($param){
        $env_path = FCPATH.'.env';

        if (file_exists($env_path)) {
            return base_url('theme/'.$_ENV['THEME'].'/'.$param);
        }
        return base_url('theme/sats/'.$param);
    }
}


if(!function_exists('favicon')){
    function favicon(){
        $env_path = FCPATH.'.env';

        if(file_exists($env_path)){
            if($_ENV['THEME'] === 'sats'){
                return base_url('/favicon.png');
            } else {
                return 'https://smokealarmsolutions.com.au/wp-content/uploads/2023/04/cropped-smoke-alarm-solutions-favicon-01-192x192.png';
            }
        }
        return base_url('/favicon.png');
    }
}

if (!function_exists('logo')) {
    function logo() {
        $env_path = FCPATH.'.env'; 

        if (file_exists($env_path)) {
            if($_ENV['THEME'] === 'sats'){
                return base_url('theme/sats/images/logo.png');
            } else {
                // return base_url('theme/sas/images/login_logo.png');
                return 'https://smokealarmsolutions.com.au/wp-content/uploads/2023/04/smoke-alarm-solutions-logo.svg';
            }
        }
        return base_url('theme/sats/images/logo.png');
    }
}

if (!function_exists('get_app_name')) {
    function get_app_name() {
        $env_path = FCPATH .'.env'; 
        if (file_exists($env_path)) {
            if ($_ENV['THEME'] === 'sats') {
                return 'SATS';
            } elseif ($_ENV['THEME'] === 'sas') {
                return 'SAS';
            }
        }
        return 'SATS'; // Default value
    }
}

if (!function_exists('get_app_email_from_header')) {
    function get_app_email_from_header() {
        $env_path = FCPATH .'.env';
        if (file_exists($env_path)) {
            return strtoupper($_ENV['theme'])." - " . strtoupper($_ENV['company_full_name']) ?? "";
        }
    }
}