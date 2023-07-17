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
		if(fiad_get_csm_mode_option('enable')){
			add_filter('template_include', [$this, 'fiad_csm_content']);
			add_action('wp_head', [$this, 'fiad_csm_extra_css']);
			add_action('wp_footer', [$this, 'fiad_csm_extra_js']);
		}
		add_filter('template_include', [$this, 'fiad_preview_csm_page']);
		$this->fiad_create_default_csm_page();
	}
	
	//No Header & Footer Page
	public function fiad_csm_content($template){
		if(!fiad_is_admin_user_role()){
			return FIBERADMIN_CSM_URL;
		}
		
		return $template;
	}
	
	public function fiad_preview_csm_page($template){
		//Sanitizes a string into a slug, which can be used in URLs or HTML attributes.
		$preview_mode = sanitize_title(fiad_array_key_exists('preview', $_GET));
		if($preview_mode && fiad_is_admin_user_role()){
			return FIBERADMIN_CSM_URL;
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
	
	public function fiad_create_default_csm_page(){
		$pages_added                   = fiad_get_csm_mode_option('page_added');
		$csm_mode_option               = get_option('fiad_csm_mode');
		$page_titles                   = [
			'coming-soon' => 'Coming Soon',
			'maintenance' => 'Maintenance',
		];
		if(!$pages_added){
			foreach($page_titles as $mode => $title){
				$page_content = FIBERADMIN_DIR . 'includes/generate-pages/csm-mode/' . $mode . '.txt';
				$post_args    = [
					'post_type'    => 'page',
					'post_title'   => $title,
					'post_content' => file_get_contents($page_content),
					'post_status'  => 'publish',
				];
				wp_insert_post($post_args);
			}
			$csm_mode_option['page_added'] = true;
			update_option('fiad_csm_mode', $csm_mode_option);
		}
	}
}

new Fiber_Admin_CSM_Mode();