<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Default functions
 */
class Fiber_Admin_Default{
	
	private $fiber_admin_branding;
	
	public function __construct(){
		//default value
		$this->fiber_admin = get_option('fiber_admin');
		
		if($this->fiber_admin['hide_wordpress_branding']){
			// Replace WordPress in the page titles.
			add_filter('admin_title', array($this, 'fiber_admin_title'), 10, 2);
			
			// Remove WordPress admin bar logo
			add_action('wp_before_admin_bar_render', array($this, 'fiber_remove_admin_bar_logo'), 0);
			
			// Admin footer modification
			add_filter('admin_footer_text', array($this, 'fiber_update_admin_footer'));
			
			// Update dashboard title
			add_action('admin_head', array($this, 'fiber_update_dashboard_name'));
			
			// Remove unused dashboard widgets
			add_action('admin_init', array($this, 'fiber_remove_dashboard_widgets'));
			
			// Update logo link and title
			add_filter('login_headerurl', array($this, 'fiber_login_logo_url'));
			add_filter('login_headertitle', array($this, 'fiber_login_logo_title'));
			
			// Remove Lost your password link
			add_filter('gettext', array($this, 'fiber_remove_lostpassword'));
			
			// Remove Back to blog
			add_action('login_enqueue_scripts', array($this, 'fiber_remove_backtoblog'));
			
			// Hide Admin Bar Frontend for all users
			add_filter('show_admin_bar', '__return_false');
		}
	}
	
	public function fiber_admin_title($admin_title, $title){
		return get_bloginfo('name') . ' &bull; ' . $title;
	}
	
	public function fiber_remove_admin_bar_logo(){
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu('wp-logo');
	}
	
	public function fiber_update_admin_footer(){
		$current_theme            = wp_get_theme();
		$current_theme_author_url = esc_html($current_theme->get('AuthorURI'));
		$current_theme_author     = esc_html($current_theme->get('Author'));
		
		echo '<span id="footer-thankyou">Developed by <a href="' . $current_theme_author_url . '" target="_blank">' . $current_theme_author . '</a></span>';
	}
	
	public function fiber_update_dashboard_name(){
		if($GLOBALS['title'] != 'Dashboard'){
			return;
		}
		
		$GLOBALS['title'] = get_bloginfo('name');
	}
	
	public function fiber_remove_dashboard_widgets(){
		remove_meta_box('dashboard_primary', 'dashboard', 'core');
		remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
		remove_meta_box('dashboard_activity', 'dashboard', 'normal');
		remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
		remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
	}
	
	public function fiber_login_logo_url(){
		return home_url();
	}
	
	public function fiber_login_logo_title(){
		return get_bloginfo('name');
	}
	
	public function fiber_remove_lostpassword($text){
		if($text == 'Lost your password?'){
			$text = '';
		}
		
		return $text;
	}
	
	public function fiber_remove_backtoblog(){
		echo '<style>#nav,#backtoblog{display:none}</style>';
	}
}

new Fiber_Admin_Default();