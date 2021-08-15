<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * White Label Page
 */
class Fiber_Admin_White_Label_Settings{
	
	private $fiber_admin;
	
	public function __construct(){
		$this->fiber_admin = get_option('fiber_admin');
		
		add_action('admin_menu', array($this, 'fiber_white_label_admin'));
		add_action('admin_init', array($this, 'fiber_admin_page_init'));
		
		// Register scripts
		add_action("admin_enqueue_scripts", array($this, 'fiber_enqueue_scripts'));
	}
	
	public function fiber_enqueue_scripts(){
		// Upload field
		wp_enqueue_media();
		
		// Colorpicker field
		wp_enqueue_style('wp-color-picker');
		
		// Plugin scripts
		wp_enqueue_script('fiber-admin', FIBERADMIN_ASSETS_URL . 'js/admin.js', array('jquery'), FIBERADMIN_VERSION);
	}
	
	public function fiber_white_label_admin(){
		add_submenu_page(
			'fiber-admin',
			'Fiber Admin White Label',
			'White Label',
			'manage_options',
			'fiber-admin',
			array($this, 'fiber_admin_white_label_admin_page')
		);
	}
	
	public function fiber_admin_white_label_admin_page(){
		?>
        <div class="wrap">
            <h2>Fiber Admin White Label</h2>
			<?php settings_errors(); ?>

            <form class="fiber-admin" method="post" action="options.php">
				<?php
				settings_fields('fiber_admin_white_label_group');
				do_settings_sections('fiber-admin');
				
				submit_button();
				?>
            </form>
        </div>
		<?php
	}
	
	public function fiber_admin_page_init(){
		register_setting(
			'fiber_admin_white_label_group',
			'fiber_admin',
			array($this, 'fiber_admin_sanitize')
		);
		
		add_settings_section(
			'fiber_admin_branding_section',
			'<span class="dashicons dashicons-wordpress"></span> Branding',
			array($this, 'fiber_admin_section_info'),
			'fiber-admin'
		);
		
		add_settings_field(
			'hide_wordpress_branding', // id
			'Hide WordPress Branding', // title
			array($this, 'fiber_hide_wordpress_branding'), // callback
			'fiber-admin', // page
			'fiber_admin_branding_section' // section
		);
		
		add_settings_section(
			'fiber_admin_white_label_section',
			'<span class="dashicons dashicons-admin-network"></span> Login',
			array($this, 'fiber_admin_section_info'),
			'fiber-admin'
		);
		
		add_settings_field(
			'login_logo',
			'Logo',
			array($this, 'fiber_login_logo'),
			'fiber-admin',
			'fiber_admin_white_label_section'
		);
		
		add_settings_field(
			'login_logo_size',
			'Logo size',
			array($this, 'fiber_login_logo_size'),
			'fiber-admin',
			'fiber_admin_white_label_section'
		);
		
		add_settings_field(
			'login_bg_color',
			'Background Color',
			array($this, 'fiber_login_bg_color'),
			'fiber-admin',
			'fiber_admin_white_label_section'
		);
		
		add_settings_field(
			'form_color',
			'Form',
			array($this, 'fiber_form'),
			'fiber-admin',
			'fiber_admin_white_label_section'
		);
	}
	
	public function fiber_admin_section_info(){
	}
	
	public function fiber_admin_sanitize($input){
		$sanitary_values = array();
		
		if($input['hide_wordpress_branding']){
			$sanitary_values['hide_wordpress_branding'] = true;
		}else{
			$sanitary_values['hide_wordpress_branding'] = false;
		}
		
		if($input){
			foreach($input as $key => $item){
				if($key !== 'hide_wordpress_branding' && $item){
					if($item){
						$sanitary_values[$key] = $item;
					}
				}
			}
		}
		
		return $sanitary_values;
	}
	
	public function fiber_hide_wordpress_branding(){
		$checked = ($this->fiber_admin['hide_wordpress_branding'] == true) ? 'checked' : '';
		?>
        <fieldset>
            <label for="hide_wordpress_branding" class="fiber-admin-toggle">
                <input type="checkbox" name="fiber_admin[hide_wordpress_branding]" id="hide_wordpress_branding"
                       value="yes" <?= $checked; ?> />
                <span class="slider round"></span>
            </label>
        </fieldset>
		<?php
	}
	
	public function fiber_login_logo(){
		$logo = $this->fiber_admin['login_logo'];
		?>
        <fieldset class="fiber-admin-input__img">
			<?php
			if($logo){
				?>
                <div class="fiber-preview">
                    <img src="<?= $logo; ?>" alt="<?= get_bloginfo('name'); ?>"/>
                </div>
				<?php
			}
			?>
            <label>
                <input class="regular-text" type="text" name="fiber_admin[login_logo]" value="<?= $logo; ?>"/>
            </label>
            <button class="button fiberadmin-upload"><?= __('Insert / Replace Image', 'fiberadmin'); ?></button>
        </fieldset>
		<?php
	}
	
	public function fiber_login_logo_size(){
		$width  = $this->fiber_admin['login_logo_width'];
		$height = $this->fiber_admin['login_logo_height'];
		?>
        <fieldset class="fiber-admin-input__multiples">
            <label class="fiber-admin-input__label" for="login_logo_width"><?= __('Width', 'fiberadmin'); ?></label>
            <input class="small-text" type="number" name="fiber_admin[login_logo_width]" id="login_logo_width"
                   value="<?= $width; ?>"/> px
            <br/>
            <label class="fiber-admin-input__label" for="login_logo_height"><?= __('Height', 'fiberadmin'); ?></label>
            <input class="small-text" type="number" name="fiber_admin[login_logo_height]" id="login_logo_height"
                   value="<?= $height; ?>"/> px
        </fieldset>
		<?php
	}
	
	public function fiber_login_bg_color(){
		$bg_color = $this->fiber_admin['login_bg_color'];
		?>
        <fieldset>
            <label>
                <input class="fiber-color-field" name="fiber_admin[login_bg_color]" type="text"
                       value="<?= $bg_color; ?>"/>
            </label>
        </fieldset>
		<?php
	}
	
	public function fiber_form(){
		$bg_color       = $this->fiber_admin['form_bg_color'];
		$button_color   = $this->fiber_admin['form_button_color'];
		$btn_text_color = $this->fiber_admin['form_btn_text_color'];
		$link_color     = $this->fiber_admin['form_link_color'];
		$disable_border = ($this->fiber_admin['form_disable_border'] == true) ? 'checked' : '';
		?>
        <fieldset class="fiber-admin-input__multiples fiber-color-group">
            <label class="fiber-admin-input__label"
                   for="form_bg_color"><?= __('Background Color', 'fiberadmin'); ?></label>
            <input id="form_bg_color" class="fiber-color-field" name="fiber_admin[form_bg_color]" type="text"
                   value="<?= $bg_color; ?>"/>
            <br/>
            <label class="fiber-admin-input__label"
                   for="form_button_color"><?= __('Button Color', 'fiberadmin'); ?></label>
            <input id="form_button_color" class="fiber-color-field" name="fiber_admin[form_button_color]" type="text"
                   value="<?= $button_color; ?>"/>
            <br/>
            <label class="fiber-admin-input__label"
                   for="form_btn_text_color"><?= __('Button Text Color', 'fiberadmin'); ?></label>
            <input id="form_btn_text_color" class="fiber-color-field" name="fiber_admin[form_btn_text_color]"
                   type="text"
                   value="<?= $btn_text_color; ?>"/>
            <br/>
            <label class="fiber-admin-input__label" for="form_link_color"><?= __('Link Color', 'fiberadmin'); ?></label>
            <input id="form_link_color" class="fiber-color-field" name="fiber_admin[form_link_color]" type="text"
                   value="<?= $link_color; ?>"/>
            <br/>
            <div class="fiber-admin-input__label"><?= __('Disable Form Border', 'fiberadmin'); ?></div>
            <label for="form_disable_border" class="fiber-admin-toggle">
                <input type="checkbox" name="fiber_admin[form_disable_border]" id="form_disable_border"
                       value="yes" <?= $disable_border; ?> />
                <span class="slider round"></span>
            </label>
        </fieldset>
		<?php
	}
}

new Fiber_Admin_White_Label_Settings();