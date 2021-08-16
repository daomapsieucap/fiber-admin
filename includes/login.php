<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Login
 */
class Fiber_Admin_Login{
	private $helper;
	
	public function __construct(){
		$this->helper = new Fiber_Admin_Helper();
		
		if($this->helper->fiber_get_settings('login_logo')){
			add_action('login_enqueue_scripts', array($this, 'fiber_login_css'));
		}
	}
	
	public function fiber_login_css(){
		// Logo CSS
		$login_logo_css = '#login h1 a, .login h1 a { ';
		
		if($login_logo = $this->helper->fiber_get_settings('login_logo')){
			$login_logo_css .= 'background-image: url(' . $login_logo . ');';
		}
		
		$login_logo_css .= 'max-width:100%;';
		
		$has_width = false;
		if($logo_width = $this->helper->fiber_get_settings('login_logo_width')){
			$login_logo_css .= 'width:' . $logo_width . 'px;';
			$has_width      = true;
		}else{
			$login_logo_css .= 'width:auto!important;';
		}
		
		$has_height = false;
		if($logo_height = $this->helper->fiber_get_settings('login_logo_height')){
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
		
		// Login Background Color CSS
		$bg_css = '';
		if($background_color = $this->helper->fiber_get_settings('login_bg_color')){
			$bg_css = 'body.login{ background-color:' . $background_color . '!important;' . '}';
		}
		
		// Form CSS
		$form_css = '';
		
		if($form_bg_color = $this->helper->fiber_get_settings('form_bg_color')){
			$form_css .= '#loginform{ background-color:' . $form_bg_color . '}';
		}
		
		// Form Border
		if($this->helper->fiber_get_settings('form_disable_border')){
			$form_css .= '#loginform{ border: none !important; box-shadow: none !important;}';
		}
		
		// Button
		$form_btn_text_color = $this->helper->fiber_get_settings('form_btn_text_color');
		$form_btn_color      = $this->helper->fiber_get_settings('form_button_color');
		
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
		if($form_link_color = $this->helper->fiber_get_settings('form_link_color')){
			$form_css .= '#login a{ color: ' . $form_link_color . ';}';
		}
		
		// Extra CSS
		$extra_css = '';
		if($form_extra_css = $this->helper->fiber_get_settings('login_extra_css')){
			$extra_css = $form_extra_css;
		}
		?>
        <style>
            <?= $login_logo_css; ?>
            <?= $bg_css; ?>
            <?= $form_css; ?>
            <?= $extra_css; ?>
        </style>
	<?php }
}

new Fiber_Admin_Login();