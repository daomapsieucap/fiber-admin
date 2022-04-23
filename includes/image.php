<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

if(fiad_get_miscellaneous_option('enable_svg')){
	require_once(FIBERADMIN_DIR . 'lib/svg-sanitizer/data/AttributeInterface.php');
	require_once(FIBERADMIN_DIR . 'lib/svg-sanitizer/data/TagInterface.php');
	require_once(FIBERADMIN_DIR . 'lib/svg-sanitizer/data/AllowedAttributes.php');
	require_once(FIBERADMIN_DIR . 'lib/svg-sanitizer/data/AllowedTags.php');
	require_once(FIBERADMIN_DIR . 'lib/svg-sanitizer/data/XPath.php');
	require_once(FIBERADMIN_DIR . 'lib/svg-sanitizer/ElementReference/Resolver.php');
	require_once(FIBERADMIN_DIR . 'lib/svg-sanitizer/ElementReference/Subject.php');
	require_once(FIBERADMIN_DIR . 'lib/svg-sanitizer/ElementReference/Usage.php');
	require_once(FIBERADMIN_DIR . 'lib/svg-sanitizer/Exceptions/NestingException.php');
	require_once(FIBERADMIN_DIR . 'lib/svg-sanitizer/Helper.php');
	require_once(FIBERADMIN_DIR . 'lib/svg-sanitizer/Sanitizer.php');
}

use enshrined\svgSanitize\Sanitizer;

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
		if(!fiad_get_miscellaneous_option('disable_image_protection') && !fiad_is_admin_user_role()){
			add_action('wp_footer', array($this, 'fiad_image_protection_scripts'));
		}
		
		// Enable SVG
		if(fiad_get_miscellaneous_option('enable_svg')){
			add_filter('upload_mimes', array($this, 'fiad_svg_mime_types'));
			add_action('admin_head', array($this, 'fiad_fix_svg_display'));
			
			add_action("admin_enqueue_scripts", array($this, 'fiad_svg_enqueue_scripts'));
			add_filter('wp_handle_upload_prefilter', array($this, 'fiad_check_for_svg'));
			
			// SVG metadata
			add_filter('wp_get_attachment_metadata', array($this, 'fiad_svg_attachment_metadata'), 10, 2);
			add_filter('wp_generate_attachment_metadata', array($this, 'fiad_svg_attachment_metadata'), 10, 2);
			// Fix the output of images using wp_get_attachment_image
			add_filter('wp_get_attachment_image_attributes', array($this, 'fiad_svg_image_attributes'), 10, 3);
		}
	}
	
	public function fiad_svg_enqueue_scripts(){
		$screen = get_current_screen();
		if($screen->id == 'upload'){
			$suffix = !FIBERADMIN_DEV_MODE ? '.min' : '';
			wp_enqueue_script('fiber-admin-svg', FIBERADMIN_ASSETS_URL . 'js/fiber-svg' . $suffix . '.js', array('jquery'), FIBERADMIN_VERSION);
			wp_localize_script('fiber-admin-svg', 'script_vars',
				array(
					'ajaxurl' => admin_url('admin-ajax.php')
				)
			);
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
	
	public function fiad_svg_mime_types($mimes){
		$mimes['svg']  = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';
		
		return $mimes;
	}
	
	public function fiad_fix_svg_display(){
		echo '<style>
		    td.media-icon img[src$=".svg"], img[src$=".svg"].attachment-post-thumbnail{
		      width: 100% !important;
		      height: auto !important;
		    }
		  </style>';
	}
	
	public function fiad_svg_attachment_metadata($data, $id){
		$attachment = get_post($id);
		$mime_type  = $attachment->post_mime_type;
		
		//If the attachment is an SVG
		if($mime_type == 'image/svg+xml'){
			//If the svg metadata are empty or the width is empty or the height is empty
			//then get the attributes from xml.
			if(empty($data) || empty($data['width']) || empty($data['height'])){
				$xml     = simplexml_load_file(wp_get_attachment_url($id));
				$attr    = $xml->attributes();
				$viewbox = explode(' ', $attr->viewBox);
				$width   = isset($attr->width) && preg_match('/\d+/', $attr->width, $value) ? (int) $value[0] : (count($viewbox) == 4 ? (int) $viewbox[2] : null);
				$height  = isset($attr->height) && preg_match('/\d+/', $attr->height, $value) ? (int) $value[0] : (count($viewbox) == 4 ? (int) $viewbox[3] : null);
				
				$data['width']  = ceil($width);
				$data['height'] = ceil($height);
			}
		}
		
		return $data;
	}
	
	public function fiad_svg_image_attributes($attr, $attachment, $size){
		
		// If we're not getting a WP_Post object, bail early.
		// @see https://wordpress.org/support/topic/notice-trying-to-get-property-id/
		if(!$attachment instanceof WP_Post){
			return $attr;
		}
		
		$mime = get_post_mime_type($attachment->ID);
		if('image/svg+xml' === $mime){
			$default_height = 100;
			$default_width  = 100;
			
			$dimensions = $this->fiad_get_svg_dimensions(get_attached_file($attachment->ID));
			
			if($dimensions){
				$default_height = $dimensions['height'];
				$default_width  = $dimensions['width'];
			}
			
			$attr['height'] = ceil($default_height);
			$attr['width']  = ceil($default_width);
		}
		
		return $attr;
	}
	
	protected function fiad_get_svg_dimensions($svg){
		$svg    = @simplexml_load_file($svg);
		$width  = 0;
		$height = 0;
		if($svg){
			$attributes = $svg->attributes();
			if(isset($attributes->width, $attributes->height)){
				$width  = floatval($attributes->width);
				$height = floatval($attributes->height);
			}elseif(isset($attributes->viewBox)){
				$sizes = explode(' ', $attributes->viewBox);
				if(isset($sizes[2], $sizes[3])){
					$width  = floatval($sizes[2]);
					$height = floatval($sizes[3]);
				}
			}else{
				return false;
			}
		}
		
		return array(
			'width'       => ceil($width),
			'height'      => ceil($height),
			'orientation' => ($width > $height) ? 'landscape' : 'portrait'
		);
	}
	
	public function fiad_check_for_svg($file){
		if($file['type'] == 'image/svg+xml'){
			if(!$this->fiad_santialize_svg($file['tmp_name'])){
				$file['error'] = __("Sorry, this file couldn't be sanitized so for security reasons wasn't uploaded",
					'safe-svg');
			}
		}
		
		return $file;
	}
	
	protected function fiad_santialize_svg($file_path){
		// Create a new sanitizer instance
		$sanitizer = new Sanitizer();
		
		// Load the dirty svg
		$dirtySVG = file_get_contents($file_path);
		
		// Pass it to the sanitizer and get it back clean
		$cleanSVG = $sanitizer->sanitize($dirtySVG);
		
		file_put_contents($file_path, $cleanSVG);
		
		return true;
	}
}

new Fiber_Admin_Image();