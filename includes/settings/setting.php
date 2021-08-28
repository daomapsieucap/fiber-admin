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
		add_action('admin_menu', array($this, 'fiad_setting'));
		
		// register styles
		add_action("admin_enqueue_scripts", array($this, 'fiad_styles'));
	}
	
	public function fiad_styles(){
		wp_enqueue_style('fiber-admin', FIBERADMIN_ASSETS_URL . 'css/admin.css', false, FIBERADMIN_VERSION, 'all');
	}
	
	public function fiad_setting(){
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