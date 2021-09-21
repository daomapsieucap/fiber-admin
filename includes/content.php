<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Content
 */
class Fiber_Admin_Content{
	public function __construct(){
		// Convert email text to link
		if(!fiad_get_miscellaneous_option('disable_email_converter')){
			add_filter('the_content', array($this, 'fiad_auto_convert_email_address'));
		}
		
		// Enable SVG
		if(fiad_get_miscellaneous_option('enable_svg')){
			add_filter('upload_mimes', array($this, 'fiad_svg_mime_types'));
			add_action('admin_head', array($this, 'fiad_fix_svg_display'));
		}
		
		// Content protection
		if(!fiad_get_miscellaneous_option('disable_content_protection') && !fiad_is_admin_user_role()){
			add_action('wp_footer', array($this, 'fiad_content_protection_scripts'));
		}
	}
	
	public function fiad_auto_convert_email_address($content){
		// Skip if the content has mailto link or input type email
		if(strpos($content, 'mailto') !== false || strpos($content, 'type="email"') !== false){
			return $content;
		}
		
		// Detect and create email link
		$search  = array('/([a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4})/');
		$replace = array('<a href="mailto:$1">$1</a>');
		
		return preg_replace($search, $replace, $content);
	}
	
	public function fiad_svg_mime_types($mimes){
		$mimes['svg'] = 'image/svg+xml';
		
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
	
	public function fiad_content_protection_scripts(){
		echo '
			<script type="text/javascript">
				document.addEventListener("keydown", function(e) {
                    if((navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey)){
                        if(e.key === "s" || e.key === "a" || e.key === "c" || e.key === "x"){
                             e.preventDefault();
                        }
                    }
				}, false);
			</script>
			';
	}
}

new Fiber_Admin_Content();