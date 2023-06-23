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
		// enqueue scripts
		add_action('admin_enqueue_scripts', [$this, 'fiad_content_scripts']);
		
		// Convert email text to link
		if(!fiad_get_miscellaneous_option('disable_email_converter')){
			add_filter('the_content', [$this, 'fiad_auto_convert_email_address']);
			
			// Divi theme
			add_filter('et_builder_render_layout', [$this, 'fiad_auto_convert_email_address']);
		}
		
		// Content protection
		if(!fiad_is_admin_user_role()){
			if(!fiad_get_miscellaneous_option('disable_protection')){
				//Disable prevent copy content
				add_action('wp_footer', [$this, 'fiad_content_protection_scripts']);
				// Disable right click and drag on image
				add_action('wp_footer', [$this, 'fiad_image_protection_scripts']);
			}else{
				//Disable prevent copy content
				if(!fiad_get_miscellaneous_option('disable_content_protection')){
					add_action('wp_footer', [$this, 'fiad_content_protection_scripts']);
				}
				// Disable right click and drag on image
				if(!fiad_get_miscellaneous_option('disable_image_protection')){
					add_action('wp_footer', [$this, 'fiad_image_protection_scripts']);
				}
			}
		}
		
		// Revisions
		$revision_number = fiad_get_miscellaneous_option('revision_number');
		if(empty($revision_number)){
			$revision_number = 5;
		}
		$revision_number = intval($revision_number);
		if($revision_number > 0){
			add_filter('wp_revisions_to_keep', [$this, 'fiad_limit_wp_revisions'], 10, 2);
		}
		
		// Disable comments
		if(!fiad_get_miscellaneous_option('enable_comments')){
			// Remove from admin menu
			add_action('admin_menu', [$this, 'fiad_remove_comment_admin_menus']);
			
			// Remove from post types
			add_action('init', [$this, 'fiad_remove_comment_support'], 100);
			
			// Remove from admin bar
			add_action('wp_before_admin_bar_render', [$this, 'fiad_remove_comment_admin_bar']);
		}
	}
	
	public function fiad_content_scripts(){
		if(is_admin()){
			// scripts
			$suffix = '';
			if(!FIBERADMIN_DEV_MODE){
				$suffix = '.min';
			}
			wp_enqueue_script('fiber-admin-content', FIBERADMIN_ASSETS_URL . 'js/fiber-content' . $suffix . '.js', false, FIBERADMIN_VERSION, true);
			
		}
	}
	
	public function fiad_auto_convert_email_address($content){
		$enable_auto_convert = true;
		
		// Skip if the content has mailto link
		if(strpos($content, 'mailto') !== false){
			return $content;
		}
		
		// Skip if the content has email in HTML attribute
		$att_email_regex = '/<\w+.*?\K[\w-]+=["\']*\s*(?:\w+\s*)*[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\s*(?:[\'"]?(?:\w+\s*)*[\'"]?)?["\']*(?=.*?>)/mi';
		preg_match($att_email_regex, $content, $email_matches);
		if($email_matches){
			$enable_auto_convert = false;
		}
		
		// Skip replace email address
		if(!$enable_auto_convert){
			return $content;
		}
		
		// Detect and create email link
		$search  = ['/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z]{2,})/'];
		$replace = ['<a href="mailto:$1" title="$1">$1</a>'];
		
		return preg_replace($search, $replace, $content);
	}
	
	public function fiad_content_protection_scripts(){
		echo '
			<script type="text/javascript">
				const disable_keys = ["s","a","c","x","C","J","U","I"];
				document.addEventListener("keydown", function(e){
			        if(((navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey) && disable_keys.includes(e.key)) || e.key === "F12"){
           				e.preventDefault();
			        }
			    }, false);
                
                ["cut", "copy"].forEach(even => {
				    document.querySelector("body").addEventListener(even, e => {
				        e.preventDefault();
				    });
				});
			</script>
			';
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
	
	public function fiad_limit_wp_revisions($num, $post): int{
		$revision_number = fiad_get_miscellaneous_option('revision_number');
		if(empty($revision_number)){
			$revision_number = 5;
		}
		
		return intval($revision_number);
	}
	
	public function fiad_remove_comment_admin_menus(){
		remove_menu_page('edit-comments.php');
	}
	
	public function fiad_remove_comment_support(){
		$post_types = get_post_types();
		foreach($post_types as $post_type){
			if(post_type_supports($post_type, 'comments')){
				remove_post_type_support($post_type, 'comments');
				remove_post_type_support($post_type, 'trackbacks');
			}
		}
	}
	
	public function fiad_remove_comment_admin_bar(){
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu('comments');
	}
}

new Fiber_Admin_Content();