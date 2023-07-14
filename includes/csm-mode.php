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
		// Enable Coming Soon/Maintenance Mode
		$this->mode = fiad_get_csm_mode_option('mode');
		if(fiad_get_csm_mode_option('enable')){
			add_filter('template_include', [$this, 'fiad_csm_content']);
			add_action('wp_head', [$this, 'fiad_csm_extra_css']);
			add_action('wp_footer', [$this, 'fiad_csm_extra_js']);
		}
		add_filter('template_include', [$this, 'fiad_preview_csm_page']);
	}
	
	//No Header & Footer Page
	public function fiad_csm_content($template){
		if(!current_user_can('edit_themes') || !is_user_logged_in()){
			return FIBERADMIN_TEMPLATES_URL;
		}
		
		return $template;
	}
	
	public function fiad_preview_csm_page($template){
		//Sanitizes a string into a slug, which can be used in URLs or HTML attributes.
		$preview_mode = sanitize_title(ev_array_key_exists('preview', $_GET));
		if($preview_mode){
			return FIBERADMIN_TEMPLATES_URL;
		}
		
		return $template;
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