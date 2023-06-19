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
		$this->mode = fiad_get_csm_mode_option('mode');
		$this->fiad_create_template_if_not_exists();
		add_action('template_redirect', [$this, 'fiad_preview_csm_page']);
		if(fiad_get_csm_mode_option('enable')){
			add_filter('template_include', [$this, 'fiad_csm_content']);
			add_action('wp_head', [$this, 'fiad_csm_extra_css']);
			add_action('wp_footer', [$this, 'fiad_csm_extra_js']);
		}
	}
	
	//No Header & Footer Page
	public function fiad_csm_content($template){
		if(!current_user_can('edit_themes') || !is_user_logged_in()){
			$new_template = FIBERADMIN_TEMPLATES_URL . $this->mode . '.php';
			if($new_template){
				return $new_template;
			}
		}
		
		return $template;
	}
	
	public function fiad_preview_csm_page(){
		$preview_mode = ev_array_key_exists('preview', $_GET);
		if($preview_mode){
			add_filter('template_include', function($template){
				$new_template = FIBERADMIN_TEMPLATES_URL . $this->mode . '.php';
				if($new_template){
					return $new_template;
				}
				
				return $template;
			});
		}
	}
	
	public function fiad_create_template_if_not_exists(){
		$file_name           = $this->mode . '.php';
		$templates_file_path = FIBERADMIN_TEMPLATES_URL . $file_name;
		$selected_page       = fiad_get_csm_mode_option('page');
		$html                = '';
		
		if(!file_exists(FIBERADMIN_TEMPLATES_URL)){
			mkdir(FIBERADMIN_TEMPLATES_URL);
		}
		fopen($templates_file_path, 'w');
		
		$title = get_bloginfo('name');
		global $post;
		$post = get_post($selected_page);
		setup_postdata($post);
		$content = preg_replace('#\[[^\]]+\]#', '', $post->post_content);
		
		if($this->mode == 'maintenance'){
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
		}
		$html .= '<!DOCTYPE HTML>';
		$html .= '<html ' . get_language_attributes() . '>';
		$html .= '<head>';
		$html .= '<title>' . $title . '</title>';
		$html .= '<link rel="icon" type="image/png" href="' . get_site_icon_url() . '"/>';
		$html .= '<?php wp_head(); ?>';
		$html .= '</head>';
		$html .= '<body>';
		$html .= '<div class="fiad-' . $this->mode . '-content">';
		$html .= ev_vc_content($content);
		$html .= '</div>';
		$html .= '<?php wp_footer(); ?>';
		$html .= '</body>';
		$html .= '</html>';
		file_put_contents($templates_file_path, $html);
		
		wp_reset_postdata();
	}
	
	public function fiad_csm_extra_css(){
		$extra_css = fiad_get_csm_mode_option('csm_extra_css');
		if($extra_css){
			echo "<style>$extra_css</style>";
		}
	}
	
	public function fiad_csm_extra_js(){
		$extra_js = fiad_get_csm_mode_option('csm_extra_js');
		if($extra_js){
			echo "<script>$extra_js</script>";
		}
	}
}

new Fiber_Admin_CSM_Mode();