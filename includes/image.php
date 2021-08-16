<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Image
 */
class Fiber_Admin_Image{
	private $helper;
	
	private $option;
	
	public function __construct(){
		$this->helper = new Fiber_Admin_Helper();
		$this->option = 'fiber_admin_miscellaneous';
		
		if($this->helper->fiber_get_settings('auto_img_meta', $this->option)){
			add_action('add_attachment', array($this, 'fiber_set_image_meta_on_image_upload'));
		}
		
		if($this->helper->fiber_get_settings('disable_img_right_click', $this->option)){
			add_action('wp_enqueue_scripts', array($this, 'fiber_image_scripts'));
		}
	}
	
	public function fiber_set_image_meta_on_image_upload($post_id){
		if(wp_attachment_is_image($post_id)){
			$fiber_image_title = get_post($post_id)->post_title;
			
			$fiber_image_title = preg_replace('%\s*[-_\s]+\s*%', ' ', $fiber_image_title);
			$fiber_image_title = ucwords(strtolower($fiber_image_title));
			
			$fiber_image_meta = array(
				'ID'           => $post_id,            // Specify the image (ID) to be updated
				'post_title'   => $fiber_image_title,        // Set image Title to sanitized title
				'post_excerpt' => $fiber_image_title,        // Set image Caption (Excerpt) to sanitized title
			);
			
			update_post_meta($post_id, '_wp_attachment_image_alt', $fiber_image_title);
			wp_update_post($fiber_image_meta);
		}
	}
	
	public function fiber_image_scripts(){
		wp_enqueue_script('fiber-admin', FIBERADMIN_ASSETS_URL . 'js/image.js', array('jquery'), FIBERADMIN_VERSION, true);
	}
}

new Fiber_Admin_Image();