<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

 function active_link($menu_controller, $menu_method, $className = 'opened')
    {
        $CI         =& get_instance();
        $uri_string = $CI->uri->uri_string();
		
		// current page
		$url_controller = $CI->router->fetch_class(); 
		$url_method = $CI->router->fetch_method();

        if( ( $menu_controller == $url_controller ) && ($menu_method == $url_method) ){
			$menu_item_class = $className;
		}else{
			$menu_item_class ='';
		}
		
		return $menu_item_class;

    }




