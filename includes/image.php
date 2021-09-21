<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Image
 */
class Fiber_Admin_Image{
	public function __construct(){
		// Set image meta on upload
		if(fiad_get_miscellaneous_option('auto_img_meta')){
			add_action('add_attachment', array($this, 'fiad_set_image_meta_on_image_upload'));
		}
		
		// Disable right click and drag on image v1.2.0
		if(!fiad_get_miscellaneous_option('disable_image_protection')){
			add_action('wp_footer', array($this, 'fiad_image_protection_scripts'));
		}
	}
	
	public function fiad_set_image_meta_on_image_upload($post_id){
		if(wp_attachment_is_image($post_id)){
			$fiber_image_title = get_post($post_id)->post_title;
			
			$fiber_image_title = preg_replace('%\s*[-_\s]+\s*%', ' ', $fiber_image_title);
			$fiber_image_title = ucwords(strtolower($fiber_image_title));
			
			$fiber_image_meta = array(
				'ID'           => $post_id,
				'post_title'   => $fiber_image_title,
				'post_excerpt' => $fiber_image_title,
			);
			
			update_post_meta($post_id, '_wp_attachment_image_alt', $fiber_image_title);
			wp_update_post($fiber_image_meta);
		}
	}
	
	public function fiad_image_protection_scripts(){
		echo "
			<script type='text/javascript'>
				setTimeout(function(){
			        const currentURL = window.location.hostname,
			            images = document.getElementsByTagName('img');
			
			        let imageURL = '';
			        for(let i = 0; i < images.length; i++){
			            imageURL = images[i].src;
			            if(imageURL.includes(currentURL)){
			                images[i].addEventListener('contextmenu', event => event.preventDefault());
			                images[i].setAttribute('draggable', false);
			            }
			        }
			    }, 1000);
			</script>
			";
	}
}

new Fiber_Admin_Image();