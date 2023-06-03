<?php

// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Enable Maintenance Mode
 */
class Fiber_Admin_Maintenance_Mode{
	public function __construct(){
		// Enable Maintenance Mode
		if(fiad_get_maintenance_mode_option('enable_maintenance_mode')){
			add_filter('enable_maintenance_mode', '__return_true');
		}
	}
}

new Fiber_Admin_Maintenance_Mode();