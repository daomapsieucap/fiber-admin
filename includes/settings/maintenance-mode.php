<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Maintenance Mode
 */
class Fiber_Admin_Maintenance_Mode{
	public function __construct(){
	}
	
	public function fiad_maintenance_mode_init(){
		register_setting(
			'fiad_maintenance_mode_group',
			'fiad_maintenance_mode',
			array($this, 'sanitize_text_field')
		);
		
		add_settings_section(
			'fiad_maintenance_mode_section',
			'<span class="dashicons dashicons-admin-generic"></span> General',
			[$this, 'fiad_section_info'],
			'fiber-admin-maintenance-mode'
		);
	}
	
	public function fiad_section_info(){
	}
}