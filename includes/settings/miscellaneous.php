<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Miscellaneous page
 */
class Fiber_Admin_Miscellaneous{
	public function __construct(){
		add_action('admin_menu', array($this, 'fiad_miscellaneous'));
		add_action('admin_init', array($this, 'fiad_miscellaneous_init'));
	}
	
	public function fiad_miscellaneous(){
		add_submenu_page(
			'fiber-admin',
			'Fiber Admin Miscellaneous',
			'Miscellaneous',
			'manage_options',
			'fiber-admin-miscellaneous',
			array($this, 'fiad_miscellaneous_page')
		);
	}
	
	public function fiad_miscellaneous_page(){
		?>
        <div class="wrap">
            <h2>Fiber Admin Miscellaneous</h2>
			<?php settings_errors(); ?>

            <form method="post" action="options.php">
				<?php
				settings_fields('fiad_miscellaneous_group');
				do_settings_sections('fiber-admin-miscellaneous');
				
				submit_button();
				?>
            </form>
        </div>
		<?php
	}
	
	public function fiad_miscellaneous_init(){
		register_setting(
			'fiad_miscellaneous_group',
			'fiad_miscellaneous',
			array($this, 'sanitize_text_field')
		);
		
		add_settings_section(
			'fiad_image_section',
			'<span class="dashicons dashicons-format-image"></span> Image',
			array($this, 'fiad_section_info'),
			'fiber-admin-miscellaneous'
		);
		
		add_settings_field(
			'auto_img_meta', // id
			'Auto Set Image Meta', // title
			array($this, 'fiad_auto_image_meta'), // callback
			'fiber-admin-miscellaneous', // page
			'fiad_image_section' // section
		);
		
		add_settings_field(
			'disable_img_right_click', // id
			'Disable Image Right Click', // title
			array($this, 'fiad_disable_image_right_click'), // callback
			'fiber-admin-miscellaneous', // page
			'fiad_image_section' // section
		);
		
		add_settings_section(
			'fiad_content_section',
			'<span class="dashicons dashicons-editor-table"></span> Content',
			array($this, 'fiad_section_info'),
			'fiber-admin-miscellaneous'
		);
		
		add_settings_field(
			'disable_email_converter', // id
			'Disable Convert Email Text to Link', // title
			array($this, 'fiad_disable_email_converter'), // callback
			'fiber-admin-miscellaneous', // page
			'fiad_content_section' // section
		);
	}
	
	public function fiad_section_info(){
	}
	
	public function fiad_auto_image_meta(){
		?>
        <fieldset>
            <label for="auto_img_meta" class="fiber-admin-toggle">
                <input type="checkbox" name="fiad_miscellaneous[auto_img_meta]" id="auto_img_meta"
                       value="yes" <?php checked(esc_attr(fiad_get_miscellaneous_option('auto_img_meta')), 'yes'); ?> />
                <span class="slider round"></span>
            </label>
        </fieldset>
		<?php
	}
	
	public function fiad_disable_image_right_click(){
		?>
        <fieldset>
            <label for="disable_img_right_click" class="fiber-admin-toggle">
                <input type="checkbox" name="fiad_miscellaneous[disable_img_right_click]"
                       id="disable_img_right_click"
                       value="yes" <?php checked(esc_attr(fiad_get_miscellaneous_option('disable_img_right_click')), 'yes'); ?> />
                <span class="slider round"></span>
            </label>
        </fieldset>
		<?php
	}
	
	public function fiad_disable_email_converter(){
		?>
        <fieldset>
            <label for="disable_email_converter" class="fiber-admin-toggle">
                <input type="checkbox" name="fiad_miscellaneous[disable_email_converter]"
                       id="disable_email_converter"
                       value="yes" <?php checked(esc_attr(fiad_get_miscellaneous_option('disable_email_converter')), 'yes'); ?> />
                <span class="slider round"></span>
            </label>
        </fieldset>
		<?php
	}
	
}

new Fiber_Admin_Miscellaneous();