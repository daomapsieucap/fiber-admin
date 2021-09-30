<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Login
 */
class Fiber_Admin_Login{
	public function __construct(){
		// Login Interface
		add_action('login_enqueue_scripts', array($this, 'fiad_login_css'));
		
		// Admin Bar Logo
		if(fiad_get_general_option('admin_bar_logo')){
			add_action('wp_before_admin_bar_render', array($this, 'fiad_admin_bar_logo'));
		}else{
			// Remove WordPress admin bar logo
			add_action('wp_before_admin_bar_render', array($this, 'fiad_remove_admin_bar_logo'), 0);
		}
	}
	
	public function fiad_login_css(){
		// Logo CSS
		$login_logo_css = '#login h1 a, .login h1 a { ';
		
		if($login_logo = fiad_get_general_option('login_logo')){
			$login_logo_css .= 'background-image: url(' . $login_logo . ');';
		}
		
		$login_logo_css .= 'max-width:100%;';
		
		$has_width = false;
		if($logo_width = fiad_get_general_option('login_logo_width')){
			$login_logo_css .= 'width:' . $logo_width . 'px;';
			$has_width      = true;
		}else{
			$login_logo_css .= 'width:auto!important;';
		}
		
		$has_height = false;
		if($logo_height = fiad_get_general_option('login_logo_height')){
			$has_height     = true;
			$login_logo_css .= 'height:' . $logo_height . 'px;';
		}
		
		// Add logo background size
		$logo_background_size = 'background-size:contain;background-position-y: center;';
		if($has_height && $has_width){
			$logo_background_size = sprintf('background-size:%s %s;', $logo_width . 'px', $logo_height . 'px');
		}
		$login_logo_css .= $logo_background_size;
		
		// Add bottom spacing
		$login_logo_css .= 'margin-bottom: 60px!important;';
		
		$login_logo_css .= '}';
		
		$bg_css = '';
		
		// Login Background Color CSS
		if($background_color = fiad_get_general_option('login_bg_color')){
			$bg_css = 'body.login{ background-color:' . $background_color . '!important;' . '}';
		}
		
		// Login Background Image CSS
		$background_image = fiad_get_general_option('login_bg_img');
		if($background_image && !$bg_css){
			$bg_css = 'body.login{';
			$bg_css .= 'background:url("' . $background_image . '") center / cover no-repeat !important;';
			$bg_css .= '}';
		}
		
		// Form CSS
		$form_css = '';
		
		if($form_bg_color = fiad_get_general_option('form_bg_color')){
			$form_css .= '#loginform{ background-color:' . $form_bg_color . '}';
		}
		
		// Form Border
		if(fiad_get_general_option('form_disable_border')){
			$form_css .= '#loginform{ border: none !important; box-shadow: none !important;}';
		}
		
		// Button
		$form_btn_text_color = fiad_get_general_option('form_btn_text_color');
		$form_btn_color      = fiad_get_general_option('form_button_color');
		
		if($form_btn_text_color || $form_btn_color){
			$form_css .= '#loginform input[type=submit]{ ';
			if($form_btn_text_color){
				$form_css .= 'color:' . $form_btn_text_color . '!important;';
				$form_css .= 'text-shadow: none;';
				$form_css .= 'border-color: none;';
				$form_css .= 'box-shadow: none;';
			}
			
			if($form_btn_color){
				$form_css .= 'background-color:' . $form_btn_color . '!important; border: 0;box-shadow:none';
			}
			
			$form_css .= '}';
		}
		
		// Link
		if($form_link_color = fiad_get_general_option('form_link_color')){
			$form_css .= '#login a{ color: ' . $form_link_color . ';}';
		}
		
		// Extra CSS
		$extra_css = '';
		if($form_extra_css = fiad_get_general_option('login_extra_css')){
			$extra_css = $form_extra_css;
		}
		?>
        <style>
            <?php echo esc_html($login_logo_css); ?>
            <?php echo esc_html($bg_css); ?>
            <?php echo esc_html($form_css); ?>
            <?php echo esc_html($extra_css); ?>
        </style>
		<?php
	}
	
	public function fiad_admin_bar_logo(){
		$logo_url = fiad_get_general_option('admin_bar_logo');
		echo '<style>
		    #wpadminbar #wp-admin-bar-wp-logo>.ab-item {
                padding: 0 7px;
                background-image: url(' . $logo_url . ') !important;
                background-size: 70%;
                background-position: center;
                background-repeat: no-repeat;
                opacity: 0.8;
            }
            #wpadminbar #wp-admin-bar-wp-logo>.ab-item .ab-icon:before {
                content: " ";
                top: 2px;
            }
		  </style>';
	}
	
	public function fiad_remove_admin_bar_logo(){
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu('wp-logo');
	}
}

new Fiber_Admin_Login();