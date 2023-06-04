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
//			add_filter('enable_maintenance_mode', [$this, 'fiad_enable_maintenance_mode'], 10, 2);
			add_action('get_header', [$this, 'fiad_enable_maintenance_mode']);
		}
	}
	
	public function fiad_enable_maintenance_mode(){
		if(!current_user_can('edit_themes') || !is_user_logged_in()){
			wp_die('<h1>Under Maintenance</h1><br />Website under planned maintenance. Please check back later.');
		}
	}
}

new Fiber_Admin_Maintenance_Mode();