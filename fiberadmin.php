<?php
/**
 * Plugin Name:       Fiber Admin
 * Plugin URI:        https://wordpress.org/plugins/fiber-admin/
 * Description:       💈 Bring multiple customization features to make your own WordPress admin.
 * Version:           3.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.0
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

if(!function_exists('get_plugin_data')){
	require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}
$plugin_data = get_plugin_data(__FILE__);

define("FIBERADMIN_VERSION", $plugin_data['Version']);
const FIBERADMIN_DEV_MODE = true;
const FIBERADMIN_FILENAME = __FILE__;
define("FIBERADMIN_DIR", plugin_dir_path(__FILE__));
define("FIBERADMIN_ASSETS_URL", plugin_dir_url(__FILE__) . 'assets/');

// Coming Soon / Maintenance
const FIBERADMIN_ASSETS_DIR   = FIBERADMIN_DIR . 'assets/';
const FIBERADMIN_CSM_PATH     = FIBERADMIN_DIR . 'templates/csm-mode.php';
const FIBERADMIN_CSM_TEMPLATE = 'csm.php';

/**
 * Init Functions
 */

add_action('init', 'fiad_init');
function fiad_init(){
	// helper functions
	include_once(FIBERADMIN_DIR . 'includes/helpers.php');
	
	// options pages
	include_once(FIBERADMIN_DIR . 'includes/settings/setting.php');
	include_once(FIBERADMIN_DIR . 'includes/settings/white-label.php');
	include_once(FIBERADMIN_DIR . 'includes/settings/cpo.php');
	include_once(FIBERADMIN_DIR . 'includes/settings/duplicate.php');
	include_once(FIBERADMIN_DIR . 'includes/settings/db-error.php');
	include_once(FIBERADMIN_DIR . 'includes/settings/miscellaneous.php');
	include_once(FIBERADMIN_DIR . 'includes/settings/csm-mode.php');
	
	//default functions
	include_once(FIBERADMIN_DIR . 'includes/default.php');
	
	//functions
	include_once(FIBERADMIN_DIR . 'includes/login.php');
	include_once(FIBERADMIN_DIR . 'includes/image.php');
	include_once(FIBERADMIN_DIR . 'includes/content.php');
	include_once(FIBERADMIN_DIR . 'includes/cpo.php');
	include_once(FIBERADMIN_DIR . 'includes/duplicate.php');
	include_once(FIBERADMIN_DIR . 'includes/db-error.php');
	include_once(FIBERADMIN_DIR . 'includes/attachment.php');
	include_once(FIBERADMIN_DIR . 'includes/csm-mode.php');
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
	$url           = get_admin_url() . "options-general.php?page=fiber-admin";
	$title         = __('Settings', 'fiber-admin');
	$settings_link = '<a href="' . $url . '" title="' . $title . '">' . $title . '</a>';
	$links[]       = $settings_link;
	
	return $links;
}

add_action('admin_head', 'fiad_admin_additional_css');
function fiad_admin_additional_css(){
	$page_csm_ids = fiad_get_page_template_ids("csm", true, true);
	if($page_csm_ids){
		$extra_styles = '<style>';
		foreach($page_csm_ids as $index => $id){
			$extra_styles .= $index > 0 ? ',' : '';
			$extra_styles .= '#the-list #post-' . $id . ' .column-title .row-title:before';
		}
		$extra_styles .= '
					{content: "CSM";
				    background-color: #f3efe3;
				    color: #333;
				    display: inline-block;
				    padding: 0 5px;
				    margin-right: 10px;}';
		$extra_styles .= '</style>';
		
		echo $extra_styles;
	}
}

/**
 * Enqueue admin script
 */

add_action('admin_enqueue_scripts', 'fiad_enqueue_admin_script');
function fiad_enqueue_admin_script(){
	$suffix = '';
	if(!FIBERADMIN_DEV_MODE){
		$suffix = '.min';
	}
	wp_register_script('fiber-admin', FIBERADMIN_ASSETS_URL . 'js/fiber-admin' . $suffix . '.js', ['jquery'], FIBERADMIN_VERSION);
}