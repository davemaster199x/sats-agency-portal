<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//THEME MANAGEMENT 
$config['theme'] = $_ENV['THEME'] ?? 'sats';
$config['theme_email_from'] = $_ENV['THEME_EMAIL_FROM'] ?? strtoupper($_ENV['THEME']).' Team';
$config['COMPANY_NAME_SHORT'] = strtoupper($_ENV['THEME']) ?? 'SATS';
$config['COMPANY_FULL_NAME'] = $_ENV['COMPANY_FULL_NAME'] ?? 'Smoke Alarm Testing Services';

$config["CUSTOMER_SERVICE"] = $_ENV['CUSTOMER_SERVICE'] ?? "1300 41 66 67";
$config["COMPANY_PHONE"] = $_ENV['COMPANY_PHONE'] ?? "1300 852 301";