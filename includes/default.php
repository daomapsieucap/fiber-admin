<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Default functions
 */
class Fiber_Admin_Default{
	public function __construct(){
		//default value
		if(fiad_get_general_option('hide_wordpress_branding')){
			// Replace WordPress in the page titles.
			add_filter('admin_title', array($this, 'fiad_title'), 10, 2);
			
			// Admin footer modification
			add_filter('admin_footer_text', array($this, 'fiad_update_admin_footer'));
			
			// Update dashboard title
			add_action('admin_head', array($this, 'fiad_update_dashboard_name'));
			
			// Remove unused dashboard widgets
			add_action('admin_init', array($this, 'fiad_remove_dashboard_widgets'));
			
			// Update logo link and title
			add_filter('login_headerurl', array($this, 'fiad_login_logo_url'));
			add_filter('login_headertext', array($this, 'fiad_login_logo_title'));
			
			// Remove Lost your password link
			add_filter('gettext', array($this, 'fiad_remove_lostpassword'));
			
			// Remove Back to blog
			add_action('login_enqueue_scripts', array($this, 'fiad_remove_backtoblog'));
			
			// Hide Admin Bar Frontend for all users
			add_filter('show_admin_bar', '__return_false');
			
			// Remove WordPress admin bar logo
			add_action('wp_before_admin_bar_render', array($this, 'fiad_remove_admin_bar_logo'), 0);
			
			// Remove Welcome Dashboard Widget
			remove_action('welcome_panel', 'wp_welcome_panel');
			
			// Remove generators from feed
			remove_action('rss2_head', 'the_generator');
			remove_action('rss_head', 'the_generator');
			remove_action('rdf_header', 'the_generator');
			remove_action('atom_head', 'the_generator');
			remove_action('commentsrss2_head', 'the_generator');
			remove_action('opml_head', 'the_generator');
			remove_action('app_head', 'the_generator');
			remove_action('comments_atom_head', 'the_generator');
			
			// Disable plugin generator tags
			remove_action('wp_head', 'wp_generator'); // default WordPress
			add_filter('the_generator', '__return_null'); // default WordPress
			remove_filter('get_the_generator_html', 'wc_generator_tag'); // Woocommerce
			remove_filter('get_the_generator_xhtml', 'wc_generator_tag'); // Woocommerce
			add_filter('revslider_meta_generator', '__return_empty_string'); // revslider
			remove_action('wp_head', 'xforwc__add_meta_information_action', 99); // Product Filter for WooCommerce
			add_action('wp_head', array($this, 'fiad_remove_meta_generators'), 1); // other plugins
		}
		
		// disable auto update
		if(!fiad_get_miscellaneous_option('enable_auto_update')){
			// wordpress automatic udpate
			add_filter('auto_update_core', '__return_false');
			add_filter('automatic_updater_disabled', '__return_false');
			add_filter('auto_update_theme', '__return_false');
			add_filter('auto_update_plugin', '__return_false');
			add_filter('auto_update_translation', '__return_false');
			
			// disable email notification
			apply_filters('auto_core_update_send_email', '__return_false');
			apply_filters('send_core_update_notification_email', '__return_false');
			apply_filters('automatic_updates_send_debug_email', '__return_false');
		}
	}
	
	public function fiad_title($admin_title, $title){
		return get_bloginfo('name') . ' &bull; ' . $title;
	}
	
	public function fiad_update_admin_footer(){
		$current_theme            = wp_get_theme();
		$current_theme_author_url = $current_theme->get('AuthorURI');
		$current_theme_author     = $current_theme->get('Author');
		
		echo '<span id="footer-thankyou">Developed by <a href="' . esc_url($current_theme_author_url) . '" target="_blank">' . esc_attr($current_theme_author) . '</a></span>';
		
	}
	
	public function fiad_update_dashboard_name(){
		if($GLOBALS['title'] != 'Dashboard'){
			return;
		}
		
		$GLOBALS['title'] = get_bloginfo('name');
	}
	
	public function fiad_remove_dashboard_widgets(){
		remove_meta_box('dashboard_primary', 'dashboard', 'core');
		remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
		remove_meta_box('dashboard_activity', 'dashboard', 'normal');
		remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
		remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
	}
	
	public function fiad_login_logo_url(){
		return home_url();
	}
	
	public function fiad_login_logo_title(){
		return get_bloginfo('name');
	}
	
	public function fiad_remove_lostpassword($text){
		if($text == 'Lost your password?'){
			$text = '';
		}
		
		return $text;
	}
	
	public function fiad_remove_backtoblog(){
		echo '<style>#nav,#backtoblog{display:none}</style>';
	}
	
	public function fiad_remove_admin_bar_logo(){
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu('wp-logo');
	}
	
	public function fiad_remove_meta_generators(){
		// WPBakery Page Builder
		if(class_exists('Vc_Manager')){
			remove_action('wp_head', array(visual_composer(), 'addMetaData'));
		}
		
		// WPML
		if(function_exists('icl_object_id')){
			global $sitepress;
			remove_action('wp_head', array($sitepress, 'meta_generator_tag'));
		}
	}
}

new Fiber_Admin_Default();