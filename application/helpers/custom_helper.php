<?php

if (!function_exists('format_empty_val')) {
    function format_empty_string($val){
        if($val==""){
            return "NULL";
        }else{
            return $val;
        }
    }
}

if (!function_exists('format_empty_number')) {
    function format_empty_val($val){
        if($val==""){
            return 0;
        }else{
            return $val;
        }
    }
}

if (!function_exists('format_bool_val')) {
    function format_bool_val($val){
        if($val=="" || $val==0){
            return "NO";
        }else{
            return "YES";
        }
    }
}

if (!function_exists('profileAvatar')) {
    function profileAvatar($avatar){
        $CI =& get_instance();

        if($avatar && $avatar!=""){
            return "/uploads/user_accounts/photo/{$avatar}";
        }else{
            return $CI->config->item('photo_empty');
        }
    }
}
if (!function_exists('yes_no')) {
	function yes_no($value): string
	{
		if ($value == 1) {
			return "YES";
		} else {
			return "NO";
		}
	}
}