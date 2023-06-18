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
			add_action('template_redirect', [$this, 'fiad_enable_csm_mode']);
			add_filter('template_include', [$this, 'fiad_csm_content']);
			add_action('wp_head', [$this, 'fiad_csm_extra_css']);
			add_action('wp_footer', [$this, 'fiad_csm_extra_js']);
		}
	}
	
	public function fiad_enable_csm_mode(){
		if(!current_user_can('edit_themes') || !is_user_logged_in()){
			if(fiad_get_csm_mode_option('enable')){
				$title = $this->mode === 'maintenance' ? 'Maintenance' : 'Coming Soon';
				if(!($page_id = post_exists($title))){
					$page_id = wp_insert_post([
						'post_title'  => $title,
						'post_name'   => $this->mode,
						'post_status' => 'publish',
						'post_type'   => 'page',
					]);
				}
				if(!is_page($page_id)){
					wp_redirect(get_permalink($page_id));
					exit();
				}
			}
		}
	}
	
	//No Header & Footer Page
	public function fiad_csm_content($template){
		if(!current_user_can('edit_themes') || !is_user_logged_in()){
			$new_template = dirname(__FILE__) . '/templates/' . $this->mode . '.php';
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
				$new_template = dirname(__FILE__) . '/templates/' . $this->mode . '.php';
				if($new_template){
					return $new_template;
				}
				
				return $template;
			});
		}
	}
	
	public function fiad_create_template_if_not_exists(){
		$templates_file_dir  = dirname(__FILE__) . '/templates/';
		$file_name           = $this->mode . '.php';
		$templates_file_path = $templates_file_dir . $file_name;
		$selected_page       = fiad_get_csm_mode_option('page');
		$html                = '';
		
		if(!file_exists($templates_file_dir)){
			mkdir($templates_file_dir);
		}
		fopen($templates_file_path, 'w');
		
		$title = get_bloginfo('name');
		global $post;
		$post = get_post($selected_page);
		setup_postdata($post);
		$content = preg_replace('#\[[^\]]+\]#', '', $post->post_content);
		
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
		$html .= ev_vc_content($content);
		$html .= '</div>';
		$html .= '<?php wp_footer(); ?>';
		$html .= '</body>';
		$html .= '</html>';
		file_put_contents($templates_file_path, $html);
		
		wp_reset_postdata();
	}
	
	public function fiad_csm_extra_css(){
		$extra_css = fiad_get_csm_mode_option('extra_css');
		if($extra_css){
			echo "<style>$extra_css</style>";
		}
	}
	
	public function fiad_csm_extra_js(){
		$extra_js = fiad_get_csm_mode_option('extra_js');
		if($extra_js){
			echo "<script>$extra_js</script>";
		}
	}
}

new Fiber_Admin_CSM_Mode();