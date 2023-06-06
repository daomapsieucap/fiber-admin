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
			add_filter('theme_page_templates', [$this, 'fiad_maintenance_template']);
			add_action('template_redirect', [$this, 'fiad_enable_maintenance_mode']);
			add_action('template_redirect', [$this, 'fiad_maintenance_content']);
		}
	}
	
	public function fiad_enable_maintenance_mode(){
		if(!current_user_can('edit_themes') || !is_user_logged_in()){
			$selected_post_types = fiad_get_maintenance_mode_option('maintenance_mode_page');
			
			$maintenance_page_id = (int) $selected_post_types[0];
			global $posts;
			$posts = get_post($maintenance_page_id);
			setup_postdata($posts);
			if(is_front_page()){
				wp_redirect(get_permalink($maintenance_page_id));
			}
		}
	}
	
	//No Header & Footer Page
	public function fiad_maintenance_content(){
		$html = 'Hello world';
//		ob_start();
//		get_template_part('partials/maintenance-mode');
//		$html .= ob_get_clean();
		var_dump($html);
		
//		return $html;
	}
	
	public function fiad_maintenance_template($template){
		$template['maintenance.php'] = 'Maintenance';
		
		return $template;
	}
}

new Fiber_Admin_Maintenance_Mode();