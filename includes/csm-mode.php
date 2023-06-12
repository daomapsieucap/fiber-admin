<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Enable Coming Soon/Maintenance Mode
 */
class Fiber_Admin_CSM_Mode{
	private $mode = '';
	
	public function __construct(){
		// Enable Coming Soon/Maintenance Mode
		if(fiad_get_csm_mode_option('enable_maintenance_mode') || fiad_get_csm_mode_option('enable_coming_soon_mode')){
			if(fiad_get_csm_mode_option('enable_maintenance_mode')){
				$this->mode = 'maintenance';
			}elseif(fiad_get_csm_mode_option('enable_coming_soon_mode')){
				$this->mode = 'coming-soon';
			}
			add_action('template_redirect', [$this, 'fiad_enable_csm_mode']);
			add_filter('template_include', [$this, 'fiad_csm_content']);
			add_action('wp_head', [$this, 'fiad_csm_extra_css']);
			add_action('wp_footer', [$this, 'fiad_csm_extra_js']);
		}
	}
	
	public function fiad_enable_csm_mode(){
		if(!current_user_can('edit_themes') || !is_user_logged_in()){
			$selected_post_types = fiad_get_csm_mode_option('csm_mode_page');
			$maintenance_page_id = (int) $selected_post_types[0];
			if(!is_page($maintenance_page_id)){
				wp_redirect(get_permalink($maintenance_page_id));
				exit();
			}
		}
	}
	
	//No Header & Footer Page
	public function fiad_csm_content($template){
		if(!current_user_can('edit_themes') || !is_user_logged_in()){
			$this->fiad_create_template_if_not_exists();
			$new_template = WP_CONTENT_DIR . '/templates/' . $this->mode . '.php';
			if($new_template){
				return $new_template;
			}
		}
		
		return $template;
	}
	
	public function fiad_create_template_if_not_exists(){
		$templates_file_dir  = WP_CONTENT_DIR . '/templates/';
		$file_name           = $this->mode . '.php';
		$templates_file_path = $templates_file_dir . $file_name;
		$html                = '';
		
		if(!file_exists($templates_file_path)){
			if(!file_exists($templates_file_dir)){
				mkdir($templates_file_dir);
			}
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
			$html .= '<div class="fiad-' . $this->mode . '-content">';
			$html .= '<?= ev_vc_content(); ?>';
			$html .= '</div>';
			$html .= '<?php wp_footer(); ?>';
			$html .= '</body>';
			$html .= '</html>';
			file_put_contents($templates_file_path, $html);
		}
	}
	
	public function fiad_csm_extra_css(){
		$extra_css = fiad_get_csm_mode_option('csm_mode_extra_css');
		if($extra_css){
			echo "<style>$extra_css</style>";
		}
	}
	
	public function fiad_csm_extra_js(){
		$extra_js = fiad_get_csm_mode_option('csm_mode_extra_js');
		if($extra_js){
			echo "<script>$extra_js</script>";
		}
	}
}

new Fiber_Admin_CSM_Mode();