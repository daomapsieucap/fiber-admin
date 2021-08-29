<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Miscellaneous page
 */
class Fiber_Admin_Miscellaneous{
	private $fiber_admin;
	
	public function __construct(){
		$this->fiber_admin = get_option('fiber_admin_miscellaneous');
		
		add_action('admin_menu', array($this, 'fiber_miscellaneous_admin'));
		add_action('admin_init', array($this, 'fiber_admin_miscellaneous_init'));
	}
	
	public function fiber_miscellaneous_admin(){
		add_submenu_page(
			'fiber-admin',
			'Fiber Admin Miscellaneous',
			'Miscellaneous',
			'manage_options',
			'fiber-admin-miscellaneous',
			array($this, 'fiber_admin_miscellaneous_admin_page')
		);
	}
	
	public function fiber_admin_miscellaneous_admin_page(){
		?>
        <div class="wrap">
            <h2>Fiber Admin Miscellaneous</h2>
			<?php settings_errors(); ?>

            <form method="post" action="options.php">
				<?php
				settings_fields('fiber_admin_miscellaneous_group');
				do_settings_sections('fiber-admin-miscellaneous');
				
				submit_button();
				?>
            </form>
        </div>
		<?php
	}
	
	public function fiber_admin_miscellaneous_init(){
		register_setting(
			'fiber_admin_miscellaneous_group',
			'fiber_admin_miscellaneous',
			array($this, 'fiber_miscellaneous_sanitize')
		);
		
		add_settings_section(
			'fiber_admin_image_section',
			'<span class="dashicons dashicons-format-image"></span> Image',
			array($this, 'fiber_admin_section_info'),
			'fiber-admin-miscellaneous'
		);
		
		add_settings_field(
			'auto_img_meta', // id
			'Auto Set Image Meta', // title
			array($this, 'fiber_auto_image_meta'), // callback
			'fiber-admin-miscellaneous', // page
			'fiber_admin_image_section' // section
		);
		
		add_settings_field(
			'disable_img_right_click', // id
			'Disable Image Right Click', // title
			array($this, 'fiber_disable_image_right_click'), // callback
			'fiber-admin-miscellaneous', // page
			'fiber_admin_image_section' // section
		);
	}
	
	public function fiber_admin_section_info(){
	}
	
	public function fiber_miscellaneous_sanitize($input){
		$sanitary_values = array();
		
		if($input['auto_img_meta']){
			$sanitary_values['auto_img_meta'] = true;
		}else{
			$sanitary_values['auto_img_meta'] = false;
		}
		
		if($input['disable_img_right_click']){
			$sanitary_values['disable_img_right_click'] = true;
		}else{
			$sanitary_values['disable_img_right_click'] = false;
		}
		
		return $sanitary_values;
	}
	
	public function fiber_auto_image_meta(){
		$checked = ($this->fiber_admin['auto_img_meta'] == true) ? 'checked' : '';
		?>
        <fieldset>
            <label for="auto_img_meta" class="fiber-admin-toggle">
                <input type="checkbox" name="fiber_admin_miscellaneous[auto_img_meta]" id="auto_img_meta"
                       value="yes" <?= $checked; ?> />
                <span class="slider round"></span>
            </label>
        </fieldset>
		<?php
	}
	
	public function fiber_disable_image_right_click(){
		$checked = ($this->fiber_admin['disable_img_right_click'] == true) ? 'checked' : '';
		?>
        <fieldset>
            <label for="disable_img_right_click" class="fiber-admin-toggle">
                <input type="checkbox" name="fiber_admin_miscellaneous[disable_img_right_click]"
                       id="disable_img_right_click"
                       value="yes" <?= $checked; ?> />
                <span class="slider round"></span>
            </label>
        </fieldset>
		<?php
	}
	
}

new Fiber_Admin_Miscellaneous();