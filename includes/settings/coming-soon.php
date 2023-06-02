<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Custom Post Order
 */
class Fiber_Admin_Coming_Soon{
	public function __construct(){
	}
	
	public function fiad_coming_soon_init(){
		register_setting(
			'fiad_coming_soon_group',
			'fiad_coming_soon',
			array($this, 'sanitize_text_field')
		);
		
		add_settings_section(
			'fiad_coming_soon_section',
			'<span class="dashicons dashicons-admin-generic"></span> General',
			[$this, 'fiad_section_info'],
			'fiber-admin-coming-soon'
		);
	}
	
	public function fiad_section_info(){
	}
}