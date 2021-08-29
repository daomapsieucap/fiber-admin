<?php
/**
 * Plugin Name:       Fiber Admin
 * Plugin URI:        https://daochau.com/
 * Description:       💈 Another helpful tool for WordPress admin
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Dau Chau
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

define('FIBERADMIN_VERSION', '1.0.0');
define("FIBERADMIN_DIR", plugin_dir_path(__FILE__));
define("FIBERADMIN_ASSETS_URL", plugin_dir_url(__FILE__) . 'assets/');

/**
 * Init Functions
 */

add_action('init', 'fiberadmin_init');
function fiberadmin_init(){
	// helper
	include_once('includes/helper.php');
	
	// options pages
	include_once('includes/settings/setting.php');
	include_once('includes/settings/white-label.php');
	include_once('includes/settings/miscellaneous.php');
	
	//default functions
	include_once('includes/default.php');
	
	//functions
	include_once('includes/login.php');
	include_once('includes/image.php');
}