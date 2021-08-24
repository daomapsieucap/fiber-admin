<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Helper functions
 */
class Fiber_Admin_Helper{
	
	public function fiber_get_settings($key, $option = 'fiber_admin'){
		$fiber_admin = get_option($option);
		
		return $fiber_admin[$key];
	}
}