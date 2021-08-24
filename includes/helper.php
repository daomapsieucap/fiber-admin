<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Helper functions
 */

if(!function_exists('fiber_get_option')){
	function fiber_get_option($key, $fiber_admin){
		if(is_array($fiber_admin)){
			if(array_key_exists($key, $fiber_admin)){
				return $fiber_admin[$key];
			}
		}
		
		return '';
	}
}

if(!function_exists('fiber_get_general_option')){
	function fiber_get_general_option($key){
		return fiber_get_option($key, get_option('fiber_admin'));
	}
}

if(!function_exists('fiber_get_miscellaneous_option')){
	function fiber_get_miscellaneous_option($key){
		return fiber_get_option($key, get_option('fiber_admin_miscellaneous'));
	}
}