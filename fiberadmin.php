<?php
/**
 * Plugin Name:       Fiber Admin
 * Plugin URI:        https://wordpress.org/plugins/fiber-admin/
 * Description:       💈 Another helpful tool for WordPress admin
 * Version:           1.0.7
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Dao Chau
 * Author URI:        https://daochau.com/
 * Text Domain:       fiber-admin
 */

// If this file is called directly, abort.
if(!defined('WPINC')){
	die;
}

// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Definitions
 */

define('FIBERADMIN_VERSION', '1.0.7');
define("FIBERADMIN_DIR", plugin_dir_path(__FILE__));
define("FIBERADMIN_ASSETS_URL", plugin_dir_url(__FILE__) . 'assets/');

/**
 * Init Functions
 */

add_action('init', 'fiad_init');
function fiad_init(){
	// helper functions
	include_once(FIBERADMIN_DIR . 'includes/helper.php');
	
	// options pages
	include_once(FIBERADMIN_DIR . 'includes/settings/setting.php');
	include_once(FIBERADMIN_DIR . 'includes/settings/white-label.php');
	include_once(FIBERADMIN_DIR . 'includes/settings/miscellaneous.php');
	
	//default functions
	include_once(FIBERADMIN_DIR . 'includes/default.php');
	
	//functions
	include_once(FIBERADMIN_DIR . 'includes/login.php');
	include_once(FIBERADMIN_DIR . 'includes/image.php');
}