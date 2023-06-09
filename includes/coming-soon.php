<?php

// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Enable Coming Soon Mode
 */
class Fiber_Admin_Coming_Soon{
	public function __construct(){
		// Enable Coming Soon Mode
		if(fiad_get_coming_soon_option('enable_coming_soon')){
			add_action('template_redirect', [$this, 'fiad_enable_coming_soon']);
			add_filter('template_include', [$this, 'fiad_coming_soon_content']);
			add_action('wp_head', [$this, 'fiad_coming_soon_extra_css']);
			add_action('wp_footer', [$this, 'fiad_coming_soon_extra_js']);
		}
	}
	
	public function fiad_enable_coming_soon(){
		if(!current_user_can('edit_themes') || !is_user_logged_in()){
			if(is_front_page()){
				$selected_post_types = fiad_get_coming_soon_option('coming_soon_page');
				$coming_soon_page_id = (int) $selected_post_types[0];
				
				wp_redirect(get_permalink($coming_soon_page_id));
				exit();
			}
		}
	}
	
	//No Header & Footer Page
	public function fiad_coming_soon_content($template){
		if(!current_user_can('edit_themes') || !is_user_logged_in()){
			$this->fiad_create_template_if_not_exists();
			$new_template = dirname(__FILE__) . '/templates/coming-soon.php';
			if($new_template){
				return $new_template;
			}
		}
		
		return $template;
	}
	
	public function fiad_create_template_if_not_exists(){
		$templates_file_path = dirname(__FILE__) . '/templates/coming-soon.php';
		$html                = '';
		if(!file_exists($templates_file_path)){
			fopen($templates_file_path, 'w');
			
			$html .= '<!DOCTYPE HTML>';
			$html .= '<html <?php language_attributes(); ?>>';
			$html .= '<head>';
			$html .= '<meta charset="<?php bloginfo( "charset" ); ?>" />';
			$html .= '<meta name="viewport" content="width=device-width, initial-scale=1" />';
			$html .= '<?php wp_head(); ?>';
			$html .= '</head>';
			$html .= '<body>';
			$html .= '<div class="fiad-coming-soon-content">';
			$html .= '<?= get_the_content(); ?>';
			$html .= '</div>';
			$html .= '<?php wp_footer(); ?>';
			$html .= '</body>';
			$html .= '</html>';
			file_put_contents($templates_file_path, $html);
		}
	}
	
	public function fiad_coming_soon_extra_css(){
		$extra_css = fiad_get_coming_soon_option('coming_soon_extra_css');
		if($extra_css){
			echo "<style>$extra_css</style>";
		}
	}
	
	public function fiad_coming_soon_extra_js(){
		$extra_js = fiad_get_coming_soon_option('coming_soon_extra_js');
		if($extra_js){
			echo "<script>$extra_js</script>";
		}
	}
}

new Fiber_Admin_Coming_Soon();