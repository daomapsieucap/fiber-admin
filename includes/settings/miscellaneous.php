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

            <form class="fiber-admin" method="post" action="options.php">
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
			'fiad_miscellaneous_section',
			'<span class="dashicons dashicons-admin-generic"></span> General',
			array($this, 'fiad_section_info'),
			'fiber-admin-miscellaneous'
		);
		
		add_settings_field(
			'enable_auto_update', // id
			'Enable auto update', // title
			array($this, 'fiad_enable_auto_update'), // callback
			'fiber-admin-miscellaneous', // page
			'fiad_miscellaneous_section' // section
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
			'disable_image_protection', // id
			'Disable Image Protection ', // title
			array($this, 'fiad_disable_image_protection'), // callback
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
		
		add_settings_field(
			'enable_svg', // id
			'Enable SVG', // title
			array($this, 'fiad_enable_svg'), // callback
			'fiber-admin-miscellaneous', // page
			'fiad_content_section' // section
		);
		
		add_settings_field(
			'disable_content_protection', // id
			'Disable Content Protection', // title
			array($this, 'fiad_disable_content_protection'), // callback
			'fiber-admin-miscellaneous', // page
			'fiad_content_section' // section
		);
	}
	
	public function fiad_section_info(){
	}
	
	public function fiad_enable_auto_update(){
		?>
        <fieldset>
            <label for="enable_auto_update" class="fiber-admin-toggle">
                <input type="checkbox" name="fiad_miscellaneous[enable_auto_update]" id="enable_auto_update"
                       value="yes" <?php checked(esc_attr(fiad_get_miscellaneous_option('enable_auto_update')), 'yes'); ?> />
                <span class="slider round"></span>
            </label>
        </fieldset>
		<?php
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
	
	public function fiad_disable_image_protection(){
		?>
        <fieldset>
            <label for="disable_image_protection" class="fiber-admin-toggle">
                <input type="checkbox" name="fiad_miscellaneous[disable_image_protection]"
                       id="disable_image_protection"
                       value="yes" <?php checked(esc_attr(fiad_get_miscellaneous_option('disable_image_protection')), 'yes'); ?> />
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
	
	public function fiad_enable_svg(){
		?>
        <fieldset>
            <label for="enable_svg" class="fiber-admin-toggle">
                <input type="checkbox" name="fiad_miscellaneous[enable_svg]"
                       id="enable_svg"
                       value="yes" <?php checked(esc_attr(fiad_get_miscellaneous_option('enable_svg')), 'yes'); ?> />
                <span class="slider round"></span>
            </label>
        </fieldset>
		<?php
	}
	
	public function fiad_disable_content_protection(){
		?>
        <fieldset>
            <label for="disable_content_protection" class="fiber-admin-toggle">
                <input type="checkbox" name="fiad_miscellaneous[disable_content_protection]"
                       id="disable_content_protection"
                       value="yes" <?php checked(esc_attr(fiad_get_miscellaneous_option('disable_content_protection')), 'yes'); ?> />
                <span class="slider round"></span>
            </label>
        </fieldset>
		<?php
	}
}

new Fiber_Admin_Miscellaneous();