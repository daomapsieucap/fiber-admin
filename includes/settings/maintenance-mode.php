<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Maintenance Mode
 */
class Fiber_Admin_Setting_Maintenance_Mode{
	public function __construct(){
	}
	
	public function fiad_maintenance_init(){
		register_setting(
			'fiad_maintenance_mode_group',
			'fiad_maintenance_mode',
			array($this, 'sanitize_text_field')
		);
	}
	
	public function fiad_section_info(){
	}
}