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
		add_filter('theme_page_templates', [$this, 'fiad_csm_page_templates']);
		
		// Only apply when enable
		if(fiad_get_csm_mode_option('enable')){
			add_filter('template_include', [$this, 'fiad_csm_content']);
		}
		
		// create page when saving option the first time
		add_filter('pre_update_option_fiad_csm_mode', [$this, 'fiad_create_default_csm_page']);
		add_filter('pre_update_option_fiad_csm_mode', [$this, 'fiad_add_default_css']);
		
		// Apply for both enable and preview mode
		add_action('script_loader_src', [$this, 'fiad_dequeue_all_for_csm'], PHP_INT_MAX);
		add_action('style_loader_src', [$this, 'fiad_dequeue_all_for_csm'], PHP_INT_MAX);
		add_filter('fiad_csm_extra_css', [$this, 'fiad_csm_extra_css']);
		add_filter('fiad_csm_extra_js', [$this, 'fiad_csm_extra_js']);
		add_filter('template_include', [$this, 'fiad_preview_csm_page']);
	}
	
	public function fiad_csm_page_templates($templates){
		$templates['csm.php'] = "CSM";
		
		return $templates;
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
		
		return '';
	}
	
	public function fiad_csm_extra_js(){
		$extra_js = fiad_get_csm_mode_option('csm_extra_js');
		if($extra_js){
			return "<script>$extra_js</script>";
		}
		
		return '';
	}
	
	public function fiad_add_default_css($value){
		$mode            = fiad_get_csm_mode_option('mode');
		$extra_css       = fiad_get_csm_mode_option('csm_extra_css');
		if(!$extra_css && !$mode){
			$default_extra_css = "body { text-align: center; padding: 150px; }\n";
			$default_extra_css .= "h1 { font-size: 50px; }\n";
			$default_extra_css .= "body { font: 20px Helvetica, sans-serif; color: #333; }\n";
			
			$value['csm_extra_css'] = $default_extra_css;
		}
		
		return $value;
	}
	
	public function fiad_dequeue_all_for_csm($src){
		$preview_mode = sanitize_title(fiad_array_key_exists('preview', $_GET));
		$csm_enable   = fiad_get_csm_mode_option('enable');
		
		if(!is_login()){
			// always dequeue if it is preview mode;
			if($preview_mode){
				return fiad_dequeue_assets($src);
			}
			
			// dequeue when activate
			if(!fiad_is_admin_user_role() && $csm_enable){
				return fiad_dequeue_assets($src);
			}
		}
		
		return $src;
	}
	
	public function fiad_create_default_csm_page($value){
		$csm_mode    = fiad_get_csm_mode_option('mode');
		$page_titles = [
			'coming-soon' => 'Coming Soon',
			'maintenance' => 'Maintenance',
		];
		if(!$csm_mode){
			foreach($page_titles as $mode => $title){
				$content_url = FIBERADMIN_ASSETS_URL . 'generate-pages/csm-mode/' . $mode . '.txt';
				$post_args   = [
					'post_type'     => 'page',
					'post_title'    => $title,
					'post_content'  => fiad_file_get_content($content_url),
					'post_status'   => 'publish',
					'page_template' => 'csm.php',
				];
				wp_insert_post($post_args);
			}
		}
		
		return $value;
	}
}

new Fiber_Admin_CSM_Mode();