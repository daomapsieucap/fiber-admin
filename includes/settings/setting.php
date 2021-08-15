<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Setting Page
 */
class Fiber_Admin_Setting{
	
	public function __construct(){
		add_action('admin_menu', array($this, 'fiberadmin_setting_admin'));
		
		// register styles
		add_action("admin_enqueue_scripts", array($this, 'fiber_enqueue_styles'));
	}
	
	public function fiber_enqueue_styles(){
		wp_enqueue_style('fiber-admin', FIBERADMIN_ASSETS_URL . 'css/admin.css', false, FIBERADMIN_VERSION, 'all');
	}
	
	public function fiberadmin_setting_admin(){
		add_menu_page(
			'Fiber Admin',
			'Fiber Admin',
			'manage_options',
			'fiber-admin',
			'',
			'dashicons-art',
			80
		);
	}
	
}

new Fiber_Admin_Setting();