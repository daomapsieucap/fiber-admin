<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * White Label Page
 */
class Fiber_Admin_White_Label_Settings{
	
	public function __construct(){
	}
	
	public function fiad_enqueue_scripts(){
		// Upload field
		wp_enqueue_media();
		
		// Color picker field
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script('wp-color-picker');
		
		// Plugin scripts
		$suffix = '';
		if(!FIBERADMIN_DEV_MODE){
			$suffix = '.min';
		}
		wp_enqueue_script('fiber-admin', FIBERADMIN_ASSETS_URL . 'js/fiber-admin' . $suffix . '.js', array('jquery'), FIBERADMIN_VERSION);
	}
	
	public function fiad_page_init(){
		register_setting(
			'fiad_white_label_group',
			'fiber-admin-white-label',
			array($this, 'sanitize_text_field')
		);
		
		add_settings_section(
			'fiad_branding_section',
			'<span class="dashicons dashicons-wordpress"></span> Branding',
			array($this, 'fiad_section_info'),
			'fiber-admin-white-label'
		);
		
		add_settings_field(
			'hide_wordpress_branding', // id
			'Hide WordPress Branding', // title
			array($this, 'fiad_hide_wordpress_branding'), // callback
			'fiber-admin-white-label', // page
			'fiad_branding_section' // section
		);
		
		add_settings_section(
			'fiad_white_label_section',
			'<span class="dashicons dashicons-admin-network"></span> Login',
			array($this, 'fiad_section_info'),
			'fiber-admin-white-label'
		);
		
		add_settings_field(
			'login_logo',
			'Logo',
			array($this, 'fiad_login_logo'),
			'fiber-admin-white-label',
			'fiad_white_label_section'
		);
		
		add_settings_field(
			'login_logo_size',
			'Logo size',
			array($this, 'fiad_login_logo_size'),
			'fiber-admin-white-label',
			'fiad_white_label_section'
		);
		
		add_settings_field(
			'login_bg_color',
			'Background Color / Image',
			array($this, 'fiad_login_bg'),
			'fiber-admin-white-label',
			'fiad_white_label_section'
		);
		
		add_settings_field(
			'form_color',
			'Form',
			array($this, 'fiad_form'),
			'fiber-admin-white-label',
			'fiad_white_label_section'
		);
		
		add_settings_field(
			'link_color',
			'Link',
			array($this, 'fiad_link'),
			'fiber-admin-white-label',
			'fiad_white_label_section'
		);
		
		add_settings_field(
			'login_extra_css',
			'Extra CSS',
			array($this, 'fiad_login_extra_css'),
			'fiber-admin-white-label',
			'fiad_white_label_section'
		);
	}
	
	public function fiad_section_info(){
	}
	
	public function fiad_hide_wordpress_branding(){
		?>
        <fieldset>
            <label for="hide_wordpress_branding" class="fiber-admin-toggle">
                <input type="checkbox" name="fiber_admin[hide_wordpress_branding]" id="hide_wordpress_branding"
                       value="yes" <?php checked(esc_attr(fiad_get_general_option('hide_wordpress_branding')), 'yes'); ?> />
                <span class="slider round"></span>
            </label>
        </fieldset>
		<?php
	}
	
	public function fiad_login_logo(){
		$logo = fiad_get_general_option('login_logo');
		?>
        <fieldset class="fiber-admin-input__img">
            <div class="fiber-admin-preview">
                <img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>"/>
            </div>
            <label>
                <input class="regular-text" type="text" name="fiber_admin[login_logo]"
                       placeholder="<?php echo __('Input / Choose your logo image', 'fiber-admin'); ?>"
                       value="<?php echo esc_url($logo); ?>"/>
            </label>
            <button class="button fiber-admin-upload"><?php echo __('Insert / Replace Image', 'fiber-admin'); ?></button>
        </fieldset>
		<?php
	}
	
	public function fiad_login_logo_size(){
		?>
        <fieldset class="fiber-admin-input__multiples">
            <label class="fiber-admin-input__label"
                   for="login_logo_width"><?php echo __('Width', 'fiber-admin'); ?></label>
            <input class="small-text" type="number" name="fiber_admin[login_logo_width]" id="login_logo_width"
                   value="<?php echo esc_attr(fiad_get_general_option('login_logo_width')); ?>"/> px
            <br/>
            <label class="fiber-admin-input__label"
                   for="login_logo_height"><?php echo __('Height', 'fiber-admin'); ?></label>
            <input class="small-text" type="number" name="fiber_admin[login_logo_height]" id="login_logo_height"
                   value="<?php echo esc_attr(fiad_get_general_option('login_logo_height')); ?>"/> px
        </fieldset>
		<?php
	}
	
	public function fiad_login_bg(){
		$bg_img = fiad_get_general_option('login_bg_img');
		?>
        <fieldset>
            <label>
                <input class="fiber-color-field" name="fiber_admin[login_bg_color]" type="text"
                       value="<?php echo esc_attr(fiad_get_general_option('login_bg_color')); ?>"/>
            </label>
        </fieldset>
        <fieldset class="fiber-admin-input__img">
            <div class="fiber-admin-preview">
                <img src="<?php echo esc_url($bg_img); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>"/>
            </div>
            <label>
                <input class="regular-text" type="text" name="fiber_admin[login_bg_img]"
                       placeholder="<?php echo __('Input / Choose your background image', 'fiber-admin'); ?>"
                       value="<?php echo esc_url($bg_img); ?>"/>
            </label>
            <button class="button fiber-admin-upload"><?php echo __('Insert / Replace Image', 'fiber-admin'); ?></button>
            <p class="description"><?php echo __('The minimum sizes should be 2000px width and 1000px height', 'fiber-admin'); ?></p>
        </fieldset>
		<?php
	}
	
	public function fiad_form(){
		?>
        <fieldset class="fiber-admin-input__multiples fiber-color-group">
            <label class="fiber-admin-input__label"
                   for="form_bg_color"><?php echo __('Background Color', 'fiber-admin'); ?></label>
            <input id="form_bg_color" class="fiber-color-field" name="fiber_admin[form_bg_color]" type="text"
                   value="<?php echo esc_attr(fiad_get_general_option('form_bg_color')); ?>"/>
            <br/>
            <label class="fiber-admin-input__label"
                   for="form_button_color"><?php echo __('Button Color', 'fiber-admin'); ?></label>
            <input id="form_button_color" class="fiber-color-field" name="fiber_admin[form_button_color]" type="text"
                   value="<?php echo esc_attr(fiad_get_general_option('form_button_color')); ?>"/>
            <br/>
            <label class="fiber-admin-input__label"
                   for="form_btn_text_color"><?php echo __('Button Text Color', 'fiber-admin'); ?></label>
            <input id="form_btn_text_color" class="fiber-color-field" name="fiber_admin[form_btn_text_color]"
                   type="text"
                   value="<?php echo esc_attr(fiad_get_general_option('form_btn_text_color')); ?>"/>
            <br/>
            <div class="fiber-admin-input__label"><?php echo __('Disable Form Border', 'fiber-admin'); ?></div>
            <label for="form_disable_border" class="fiber-admin-toggle">
                <input type="checkbox" name="fiber_admin[form_disable_border]" id="form_disable_border"
                       value="yes" <?php checked(esc_attr(fiad_get_general_option('form_disable_border')), 'yes'); ?> />
                <span class="slider round"></span>
            </label>
        </fieldset>
		<?php
	}
	
	public function fiad_link(){
		?>
        <fieldset class="fiber-admin-input__multiples fiber-color-group">
            <label class="fiber-admin-input__label"
                   for="link_color"><?php echo __('Link Color', 'fiber-admin'); ?></label>
            <input id="link_color" class="fiber-color-field" name="fiber_admin[link_color]" type="text"
                   value="<?php echo esc_attr(fiad_get_general_option('link_color')); ?>"/>
        </fieldset>
		<?php
	}
	
	public function fiad_login_extra_css(){
		?>
        <fieldset>
            <textarea
                    name="fiber_admin[login_extra_css]"><?php echo esc_html(fiad_get_general_option('login_extra_css')); ?></textarea>
        </fieldset>
		<?php
	}
}