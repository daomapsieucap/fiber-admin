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
		
		// Revisions
		$revision_number = fiad_get_miscellaneous_option('revision_number');
		if(empty($revision_number)){
			$revision_number = 5;
		}
		$revision_number = intval($revision_number);
		if($revision_number > 0){
			add_filter('wp_revisions_to_keep', array($this, 'fiad_limit_wp_revisions'), 10, 2);
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
				document.addEventListener("keydown", function(e){
			        if((navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey)){
			            if(e.key === "s" || e.key === "a" || e.key === "c" || e.key === "x" || (e.shiftKey && e.key === "I")){
			                e.preventDefault();
			            }
			        }
			    }, false);
			</script>
			';
	}
	
	public function fiad_limit_wp_revisions($num, $post): int{
		$revision_number = fiad_get_miscellaneous_option('revision_number');
		if(empty($revision_number)){
			$revision_number = 5;
		}
		
		return intval($revision_number);
	}
}

new Fiber_Admin_Content();