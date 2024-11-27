<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['company_full_name'] = $_ENV['COMPANY_FULL_NAME'];

// This should be changed to country code AU/NZ
$config['country'] = (int) $_ENV['COMPANY_COUNTRY_ID'] ?? 1;

// set timezone
if ($config['country'] == 1) {
	date_default_timezone_set('Australia/Sydney');
	$config['country_code'] = '+61';
} else if ($config['country'] == 2) {
	date_default_timezone_set('Pacific/Auckland');
	$config['country_code'] = '+64';
}

$config['user_photo'] = '/uploads/user_accounts/photo';
$config['photo_empty'] = '/images/avatar-2-64.png';
$config['pagi_per_page'] = 50; // pagination per page

// encryption
$config['encrpytion_cipher'] = 'aes-128-gcm';
$config['encrpytion_key'] = 'sats123';

// set this if agency CI is now using the agency.sats.com domain
$agency_domain_used = 0;

// CRM link
$pattern = "/^agency\.(.+)$/";
if(preg_match($pattern, $_SERVER['HTTP_HOST'], $matches)){
	$config['base_domain'] = $matches[1];
	$config['agency_link'] = 'https://agency.' . $config['base_domain'];
	$config['crm_link']    = 'https://crm.' . $config['base_domain'];
	$config['crmci_link']  = 'https://crm.' . $config['base_domain'];

} else {
	error_log('sats_config could not match HTTP_HOST pattern');
}

// default 240v RF alarm price
$config['default_qld_upgrade_quote_price'] = $_ENV['COMPANY_DEFAULT_QUOTE_AMOUNT_FOR_QLD_UPGRADES'];

// Property Me
$config['PME_CLIENT_ID'] = $_ENV['PME_CLIENT_ID'];
$config['PME_CLIENT_SECRET'] = $_ENV['PME_CLIENT_SECRET'];
$config['PME_URL_CALLBACK'] = $_ENV['APP_URL'] . "api/callback_pme"; // No need to switch
$config['PME_CLIENT_Scope'] = "contact:read%20property:read%20property:write%20activity:read%20communication:read%20transaction:read%20transaction:write%20offline_access";
$config['PME_ACCESS_TOKEN_URL'] = "https://login.propertyme.com/connect/token";
$config['PME_AUTHORIZE_URL'] = "https://login.propertyme.com/connect/authorize";


$config['PME_AUTH_LINK'] = $config['PME_AUTHORIZE_URL'] . "?response_type=code&state=abc123&client_id=".$config['PME_CLIENT_ID']."&scope=".$config['PME_CLIENT_Scope']."&redirect_uri=".$config['PME_URL_CALLBACK'];

// accounts date filter
if( $config['country'] == 1 ){ // AU
	$config['accounts_financial_year'] = '2020-02-01'; 
}else if( $config['country'] == 2 ){ // NZ
	$config['accounts_financial_year'] = '2019-12-01'; 
}

// PALACE API base url
if( $config['country'] == 1 ){ // AU
	$config['palace_api_base_liquid'] = 'https://api.getpalace.com'; // liquid system (new)
	$config['palace_api_base_legacy'] = 'https://serviceapia.realbaselive.com'; // legacy system (old)
}else if( $config['country'] == 2 ){ // NZ
	$config['palace_api_base_liquid'] = 'https://api.getpalace.com'; // liquid system (new)
	$config['palace_api_base_legacy'] = 'https://serviceapi.realbaselive.com'; // legacy system (old)
}
$config['disable_cavius_option'] = 1; ##1=Yes | 0=No
$config['harris_agencies'] = array(1961,6203,6974,10648);

// Wholesale SMS
$config['ws_sms_reply_url'] = $_ENV['APP_URL'] . 'sms/wholesalesms_reply';
$config['ws_sms_dlvr_url'] = $_ENV['APP_URL'] . 'sms/wholesalesms_delivery';
$config['ws_sms_api_key'] = $_ENV['WHOLESALE_SMS_API_KEY'];
$config['ws_sms_api_secret'] = $_ENV['WHOLESALE_SMS_API_SECRET'];

// YABBR SMS AU/SAS - use switch to disable SMS
$config['sms_allow'] = $_ENV['SMS_ALLOW'] ?? 0;
$config['yabbr_virtual_number'] = $_ENV['YABBR_VIRTUAL_NUMBER'];
$config['yabbr_sms_api_key'] = $_ENV['YABBR_API_KEY'];

// Google
$config['gmap_api_key'] = $_ENV['GOOGLE_MAP_API_KEY'];

$config['google_recaptcha_site_key'] = $_ENV['GOOGLE_RECAPTCHA_SITE_KEY'];
$config['google_recaptcha_secret_key'] = $_ENV['GOOGLE_RECAPTCHA_SECRET_KEY'];

## HashIds Salt
$config['hash_salt'] = $_ENV['HASH_SALT'];