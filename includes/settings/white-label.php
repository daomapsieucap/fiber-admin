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
		wp_enqueue_script('fiber-admin');
	}
	
	public function fiad_page_init(){
		register_setting(
			'fiad_white_label_group',
			'fiber-admin-white-label',
			[$this, 'sanitize_text_field']
		);
		
		add_settings_section(
			'fiad_branding_section',
			'<span class="dashicons dashicons-wordpress"></span> Branding',
			[$this, 'fiad_section_info'],
			'fiber-admin-white-label'
		);
		
		add_settings_field(
			'hide_wordpress_branding', // id
			'Hide WordPress Branding', // title
			[$this, 'fiad_hide_wordpress_branding'], // callback
			'fiber-admin-white-label', // page
			'fiad_branding_section' // section
		);
		
		add_settings_field(
			'enable_admin_toolbar', // id
			'Enable Admin Toolbar', // title
			[$this, 'fiad_enable_admin_toolbar'], // callback
			'fiber-admin-white-label', // page
			'fiad_branding_section' // section
		);
		
		add_settings_section(
			'fiad_login_logo_section',
			'<span class="dashicons dashicons-format-image"></span> Login Logo',
			[$this, 'fiad_section_info'],
			'fiber-admin-white-label'
		);

		add_settings_field(
			'login_logo',
			'Logo',
			[$this, 'fiad_login_logo'],
			'fiber-admin-white-label',
			'fiad_login_logo_section'
		);

		add_settings_field(
			'login_logo_size',
			'Logo Size',
			[$this, 'fiad_login_logo_size'],
			'fiber-admin-white-label',
			'fiad_login_logo_section'
		);

		add_settings_section(
			'fiad_login_background_section',
			'<span class="dashicons dashicons-art"></span> Login Background',
			[$this, 'fiad_section_info'],
			'fiber-admin-white-label'
		);

		add_settings_field(
			'login_bg_color',
			'Background Color',
			[$this, 'fiad_login_bg_color'],
			'fiber-admin-white-label',
			'fiad_login_background_section'
		);

		add_settings_field(
			'login_bg_img',
			'Background Image',
			[$this, 'fiad_login_bg_image'],
			'fiber-admin-white-label',
			'fiad_login_background_section'
		);

		add_settings_section(
			'fiad_login_colors_section',
			'<span class="dashicons dashicons-admin-appearance"></span> Login Colors',
			[$this, 'fiad_section_info'],
			'fiber-admin-white-label'
		);

		add_settings_field(
			'form_color',
			'Form Colors',
			[$this, 'fiad_form'],
			'fiber-admin-white-label',
			'fiad_login_colors_section'
		);

		add_settings_field(
			'link_color',
			'Link Color',
			[$this, 'fiad_link'],
			'fiber-admin-white-label',
			'fiad_login_colors_section'
		);

		add_settings_section(
			'fiad_login_advanced_section',
			'<span class="dashicons dashicons-editor-code"></span> Login Custom CSS',
			[$this, 'fiad_section_info'],
			'fiber-admin-white-label'
		);

		add_settings_field(
			'login_extra_css',
			'CSS',
			[$this, 'fiad_login_extra_css'],
			'fiber-admin-white-label',
			'fiad_login_advanced_section'
		);
	}
	
	public function fiad_section_info(){
	}
	
	public function fiad_hide_wordpress_branding(){
		?>
        <fieldset>
            <label class="fiber-admin-checkbox-field" for="hide_wordpress_branding">
                <input type="checkbox" name="fiber_admin[hide_wordpress_branding]" id="hide_wordpress_branding"
                       value="yes" <?php checked(esc_attr(fiad_get_general_option('hide_wordpress_branding')), 'yes'); ?> />
                <?php echo __('Remove "WordPress" text and logos from the dashboard, login page, and admin bar.', 'fiber-admin'); ?>
            </label>
        </fieldset>
		<?php
	}
	
	public function fiad_enable_admin_toolbar(){
		?>
        <fieldset>
            <label class="fiber-admin-checkbox-field" for="enable_admin_toolbar">
                <input type="checkbox" name="fiber_admin[enable_admin_toolbar]" id="enable_admin_toolbar"
                       value="yes" <?php checked(esc_attr(fiad_get_general_option('enable_admin_toolbar')), 'yes'); ?> />
                <?php echo __('Keep the front-end admin bar visible to admins. Only applies when Hide WordPress Branding is on.', 'fiber-admin'); ?>
            </label>
        </fieldset>
		<?php
	}
	
	public function fiad_login_logo(){
		fiad_image_upload_field(
			'fiber_admin[login_logo]',
			fiad_get_general_option('login_logo'),
			__('Set logo image', 'fiber-admin'),
			__('Remove logo image', 'fiber-admin')
		);
	}
	
	public function fiad_login_logo_size(){
		fiad_logo_size_field(
			'fiber_admin',
			'login_logo_width',
			'login_logo_height',
			fiad_get_general_option('login_logo_width'),
			fiad_get_general_option('login_logo_height'),
			__('Logo size', 'fiber-admin')
		);
	}
	
	public function fiad_login_bg_color(){
		?>
        <fieldset class="fiber-admin-color-field--with-note">
            <label>
                <input class="fiber-color-field" name="fiber_admin[login_bg_color]" type="text"
                       value="<?php echo esc_attr(fiad_get_general_option('login_bg_color')); ?>"/>
            </label>
            <p class="fiber-admin-field-note"><?php echo __('Takes priority over the Background Image field below. Clear it to use the image instead.', 'fiber-admin'); ?></p>
        </fieldset>
		<?php
	}

	public function fiad_login_bg_image(){
		fiad_image_upload_field(
			'fiber_admin[login_bg_img]',
			fiad_get_general_option('login_bg_img'),
			__('Set background image', 'fiber-admin'),
			__('Remove background image', 'fiber-admin'),
			__('Minimum size 1920x900px. Recommended 1920x1080px (Full HD) for the sharpest result.', 'fiber-admin'),
			1920,
			900
		);
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
            <label for="form_disable_border">
                <input type="checkbox" name="fiber_admin[form_disable_border]" id="form_disable_border"
                       value="yes" <?php checked(esc_attr(fiad_get_general_option('form_disable_border')), 'yes'); ?> />
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
		$id = "login-extra-css";
		fiad_code_editor('text/css', $id);
		?>

        <fieldset>
            <textarea
                    id=<?= $id; ?>
                    name="fiber_admin[login_extra_css]"><?php echo esc_html(fiad_get_general_option('login_extra_css')); ?></textarea>
            <p class="description"><?php echo __('Applied only to the login page, after all other styles above.', 'fiber-admin'); ?></p>
        </fieldset>
		<?php
	}
}