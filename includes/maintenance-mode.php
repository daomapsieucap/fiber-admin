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
			add_action('template_redirect', [$this, 'fiad_enable_maintenance_mode']);
			add_filter('template_include', [$this, 'fiad_maintenance_content']);
		}
	}
	
	public function fiad_enable_maintenance_mode(){
		if(!current_user_can('edit_themes') || !is_user_logged_in()){
			if(is_front_page()){
				$selected_post_types = fiad_get_maintenance_mode_option('maintenance_mode_page');
				$maintenance_page_id = (int) $selected_post_types[0];
				
				wp_redirect(get_permalink($maintenance_page_id));
				exit();
			}
		}
	}
	
	//No Header & Footer Page
	public function fiad_maintenance_content($template){
		$this->fiad_create_template_if_not_exists();
		$new_template = dirname(__FILE__) . '/templates/maintenance.php';
		if($new_template){
			return $new_template;
		}
		
		return $template;
	}
	
	public function fiad_create_template_if_not_exists(){
		$templates_file_path = dirname(__FILE__) . '/templates/maintenance.php';
		$html                = '';
		if(!file_exists($templates_file_path)){
			fopen($templates_file_path, 'w');
			$html .= "<div class='fiad-maintenance-content'>" . get_the_content() . "</div>";
			file_put_contents($templates_file_path, $html);
		}
	}
}

new Fiber_Admin_Maintenance_Mode();