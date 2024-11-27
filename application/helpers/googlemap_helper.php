<?php

if (!function_exists('getGoogleMapCoordinates')) {
    function getGoogleMapCoordinates($address){
        $CI =& get_instance();

        $coordinates = array();

        // init curl object
        $ch = curl_init();

        $API_key = $CI->config->item('gmap_api_key');

        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . rawurlencode($address) . "&key={$API_key}";

        // define options
        $optArray = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
        );

        // apply those options
        curl_setopt_array($ch, $optArray);

        // execute request and get response
        $result = curl_exec($ch);


        $result_json = json_decode($result);

        $lat = $result_json->results[0]->geometry->location->lat;
        $lng = $result_json->results[0]->geometry->location->lng;


        $coordinates['lat'] = $lat;
        $coordinates['lng'] = $lng;

        curl_close($ch);

        return $coordinates;
    }
}