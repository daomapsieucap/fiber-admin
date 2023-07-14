<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Enable Coming Soon/Maintenance Mode
 */
class Fiber_Admin_CSM_Mode{
	private $default_pages = [];
	private $page_id = '';
	private $page = null;
	
	public function __construct(){
		// Enable Coming Soon/Maintenance Mode
		$this->page_id = fiad_get_csm_mode_option('page');
		$this->page    = get_post($this->page_id);
		if(fiad_get_csm_mode_option('enable')){
			add_filter('template_include', [$this, 'fiad_csm_content']);
			add_action('wp_head', [$this, 'fiad_csm_extra_css']);
			add_action('wp_footer', [$this, 'fiad_csm_extra_js']);
			add_filter('wpseo_opengraph_desc', [$this, 'fiad_change_meta_desc']);
			add_filter('wpseo_opengraph_title', [$this, 'fiad_change_meta_title']);
			add_filter('wpseo_metadesc', [$this, 'fiad_change_meta_desc']);
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
	
	public function fiad_change_meta_desc($description){
		return wp_strip_all_tags($this->page->post_content);
	}
	
	public function fiad_change_meta_title($title){
		return wp_strip_all_tags($this->page->post_title);
	}
	
	public function fiad_create_default_csm_page(){
		$pages          = ["Coming Soon", "Maintenance"];
		$is_maintenance = fiad_get_csm_mode_option('mode') == 'maintenance';
		$page_heading   = $is_maintenance ? "We&rsquo;ll be back soon!" : "Coming Soon";
		$page_content   = $is_maintenance ? "Sorry for the inconvenience but we&rsquo;re performing some maintenance at the moment" : "Our website is currently undergoing scheduled maintenance. We Should be back shortly. Thank you for your patience.";
		
		$html = '<h1>' . $page_heading . '</h1>';
		$html .= '<p>' . $page_content . '</p>';
		foreach($pages as $page){
			if(!post_exists($page)){
				$post_args = [
					'post_type'    => 'page',
					'post_title'   => $page,
					'post_content' => $html,
					'post_status'  => 'publish',
				];
				wp_insert_post($post_args);
			}
		}
	}
}

new Fiber_Admin_CSM_Mode();