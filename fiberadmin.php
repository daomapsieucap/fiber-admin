<?php
/**
 * Plugin Name:       Fiber Admin
 * Plugin URI:        https://wordpress.org/plugins/fiber-admin/
 * Description:       💈 Another helpful admin with some extra functions for WordPress backend
 * Version:           1.5.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Dao
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

define('FIBERADMIN_VERSION', '1.5.3');
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
	include_once(FIBERADMIN_DIR . 'includes/settings/cpo.php');
	include_once(FIBERADMIN_DIR . 'includes/settings/duplicate.php');
	include_once(FIBERADMIN_DIR . 'includes/settings/miscellaneous.php');
	include_once(FIBERADMIN_DIR . 'includes/settings/db-error.php');
	
	//default functions
	include_once(FIBERADMIN_DIR . 'includes/default.php');
	
	//functions
	include_once(FIBERADMIN_DIR . 'includes/login.php');
	include_once(FIBERADMIN_DIR . 'includes/image.php');
	include_once(FIBERADMIN_DIR . 'includes/content.php');
	include_once(FIBERADMIN_DIR . 'includes/cpo.php');
	include_once(FIBERADMIN_DIR . 'includes/duplicate.php');
	include_once(FIBERADMIN_DIR . 'includes/db-error.php');
}

/**
 * Update Database for CPO v1.1
 */

add_action('plugins_loaded', 'fiad_update_db_check');
function fiad_update_db_check(){
	if(get_option('fiber_admin_db_version') != FIBERADMIN_VERSION){
		fiad_update_db();
	}
}

function fiad_update_db(){
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	$column = $wpdb->get_results($wpdb->prepare(
		"SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s ",
		DB_NAME, $wpdb->terms, 'term_order'
	));
	
	if(empty($column)){
		$wpdb->query("ALTER TABLE $wpdb->terms ADD `term_order` INT (11) NOT NULL DEFAULT 0;");
		update_option('fiber_admin_db_version', FIBERADMIN_VERSION);
	}
}

/**
 * Delete data after uninstall
 */

register_uninstall_hook(__FILE__, 'fiad_db_uninstall');
function fiad_db_uninstall(){
	if(get_option('fiber_admin_db_version')){
		global $wpdb;
		
		// Delete CPO data
		$wpdb->query("ALTER TABLE $wpdb->terms DROP `term_order`");
		delete_option('fiber_admin_db_version');
		
		// Delete db-error.php
		if(fiad_check_db_error_file()){
			wp_delete_file(WP_CONTENT_DIR . '/db-error.php');
		}
	}
}

/**
 * Add Settings link
 */

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'fiad_settings_page');
function fiad_settings_page($links){
	$url           = get_admin_url() . "admin.php?page=fiber-admin";
	$title         = __('Settings', 'fiber-admin');
	$settings_link = '<a href="' . $url . '" title="' . $title . '">' . $title . '</a>';
	$links[]       = $settings_link;
	
	return $links;
}