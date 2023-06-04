<?php

// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Enable Maintenance Mode
 */
class Fiber_Admin_Maintenance_Mode{
	public function __construct(){
		// Enable Maintenance Mode
		if(fiad_get_maintenance_mode_option('enable_maintenance_mode')){
//			add_filter('enable_maintenance_mode', [$this, 'fiad_enable_maintenance_mode'], 10, 2);
			add_action('get_header', [$this, 'fiad_enable_maintenance_mode']);
		}
	}
	
	public function fiad_enable_maintenance_mode(){
		if(!current_user_can('edit_themes') || !is_user_logged_in()){
			$selected_post_types = fiad_get_maintenance_mode_option('maintenance_mode_page');
			$id = $selected_post_types[0];
			global $post;
			$post = get_post($id);
			setup_postdata($id);
			$content = $this->fiad_maintenance_content();
			
			wp_die($content);
		}
	}
	
	public function fiad_maintenance_content(){
		$html = '';
		ob_start();
		get_template_part('partials/maintenance-mode');
		$html .= ob_get_clean();
		
		return $html;
	}
}

new Fiber_Admin_Maintenance_Mode();