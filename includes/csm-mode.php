<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Enable Coming Soon/Maintenance Mode
 */
class Fiber_Admin_CSM_Mode{
	public function __construct(){
		// Only apply when enable
		if(fiad_get_csm_mode_option('enable')){
			add_filter('template_include', [$this, 'fiad_csm_content']);
			$this->fiad_create_default_csm_page();
			$this->fiad_add_default_css();
		}
		// Apply for both enable and preview mode
		add_action('wp_enqueue_scripts', [$this, 'fiad_dequeue_all_for_csm'], 20);
		add_filter('fiad_csm_extra_css', [$this, 'fiad_csm_extra_css']);
		add_filter('fiad_csm_extra_js', [$this, 'fiad_csm_extra_js']);
		add_filter('template_include', [$this, 'fiad_preview_csm_page']);
	}
	
	//No Header & Footer Page
	public function fiad_csm_content($template){
		if(!fiad_is_admin_user_role()){
			return FIBERADMIN_CSM_PATH;
		}
		
		return $template;
	}
	
	public function fiad_preview_csm_page($template){
		//Sanitizes a string into a slug, which can be used in URLs or HTML attributes.
		$preview_mode = sanitize_title(fiad_array_key_exists('preview', $_GET));
		if($preview_mode && fiad_is_admin_user_role()){
			return FIBERADMIN_CSM_PATH;
		}
		
		return $template;
	}
	
	public function fiad_csm_extra_css(){
		$extra_css = fiad_get_csm_mode_option('csm_extra_css');
		if($extra_css){
			return "<style>$extra_css</style>";
		}
	}
	
	public function fiad_csm_extra_js(){
		$extra_js = fiad_get_csm_mode_option('csm_extra_js');
		if($extra_js){
			return "<script>$extra_js</script>";
		}
	}
	
	public function fiad_add_default_css(){
		$extra_css       = fiad_get_csm_mode_option('csm_extra_css');
		$csm_mode_option = get_option('fiad_csm_mode');
		$css_added       = fiad_get_csm_mode_option('added_css');
		if(!$extra_css && !$css_added){
			$default_extra_css = "body { text-align: center; padding: 150px; }\n";
			$default_extra_css .= "h1 { font-size: 50px; }\n";
			$default_extra_css .= "body { font: 20px Helvetica, sans-serif; color: #333; }\n";
			
			$csm_mode_option['csm_extra_css'] = $default_extra_css;
			$csm_mode_option['added_css']     = true;
			update_option('fiad_csm_mode', $csm_mode_option);
		}
	}
	
	public function fiad_dequeue_all_for_csm(){
		$preview_mode = sanitize_title(fiad_array_key_exists('preview', $_GET));
		$csm_enable   = fiad_get_csm_mode_option('enable');
		
		// always dequeue if it is preview mode;
		if($preview_mode){
			fiad_dequeue_assets();
		}
		
		// dequeue when activate
		if(!fiad_is_admin_user_role() && $csm_enable){
			fiad_dequeue_assets();
		}
	}
	
	public function fiad_create_default_csm_page(){
		$pages_added     = fiad_get_csm_mode_option('added_pages');
		$csm_mode_option = get_option('fiad_csm_mode');
		$page_titles     = [
			'coming-soon' => 'Coming Soon',
			'maintenance' => 'Maintenance',
		];
		if(!$pages_added){
			foreach($page_titles as $mode => $title){
				$content_url = FIBERADMIN_ASSETS_URL . 'generate-pages/csm-mode/' . $mode . '.txt';
				$post_args   = [
					'post_type'    => 'page',
					'post_title'   => $title,
					'post_content' => fiad_file_get_content($content_url),
					'post_status'  => 'publish',
				];
				wp_insert_post($post_args);
			}
			$csm_mode_option['added_pages'] = true;
			update_option('fiad_csm_mode', $csm_mode_option);
		}
	}
}

new Fiber_Admin_CSM_Mode();