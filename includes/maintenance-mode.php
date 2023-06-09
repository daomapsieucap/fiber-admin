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
			add_action('wp_head', [$this, 'fiad_maintenance_extra_css']);
			add_action('wp_footer', [$this, 'fiad_maintenance_extra_js']);
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
		if(!current_user_can('edit_themes') || !is_user_logged_in()){
			$this->fiad_create_template_if_not_exists();
			$new_template = dirname(__FILE__) . '/templates/maintenance.php';
			if($new_template){
				return $new_template;
			}
		}
		
		return $template;
	}
	
	public function fiad_create_template_if_not_exists(){
		$templates_file_path = dirname(__FILE__) . '/templates/maintenance.php';
		$html                = '';
		fopen($templates_file_path, 'w');
		
		$title = get_bloginfo('name');
		
		$php = '<?php';
		$php .= PHP_EOL;
		$php .= 'header(\'HTTP/1.1 503 Service Temporarily Unavailable\', true, 503 );';
		$php .= PHP_EOL;
		$php .= 'header(\'Status: 503 Service Temporarily Unavailable\');';
		$php .= PHP_EOL;
		$php .= 'header(\'Retry-After: 3600\');';
		$php .= PHP_EOL;
		$php .= '?>';
		
		$html .= $php;
		$html .= '<!DOCTYPE HTML>';
		$html .= '<html ' . get_language_attributes() . '>';
		$html .= '<head>';
		$html .= '<title>' . $title . '</title>';
		$html .= '<link rel="icon" type="image/png" href="' . get_site_icon_url() . '"/>';
		$html .= '<?php wp_head(); ?>';
		$html .= '</head>';
		$html .= '<body>';
		$html .= '<div class="fiad-maintenance-content">';
		$html .= '<?= get_the_content(); ?>';
		$html .= '</div>';
		$html .= '<?php wp_footer(); ?>';
		$html .= '</body>';
		$html .= '</html>';
		file_put_contents($templates_file_path, $html);
	}
	
	public function fiad_maintenance_extra_css(){
		$extra_css = fiad_get_maintenance_mode_option('maintenance_mode_extra_css');
		if($extra_css){
			echo "<style>$extra_css</style>";
		}
	}
	
	public function fiad_maintenance_extra_js(){
		$extra_js = fiad_get_maintenance_mode_option('maintenance_mode_extra_js');
		if($extra_js){
			echo "<script>$extra_js</script>";
		}
	}
}

new Fiber_Admin_Maintenance_Mode();