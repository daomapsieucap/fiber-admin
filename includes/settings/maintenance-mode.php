<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Maintenance Mode
 */
class Fiber_Admin_Maintenance_Mode{
	public function __construct(){
	}
	
	public function fiad_maintenance_mode_init(){
		register_setting(
			'fiad_maintenance_mode_group',
			'fiad_maintenance_mode',
			array($this, 'sanitize_text_field')
		);
		
		add_settings_section(
			'fiad_maintenance_mode_section',
			'<span class="dashicons dashicons-admin-generic"></span> General',
			[$this, 'fiad_section_info'],
			'fiber-admin-maintenance-mode'
		);
		
		add_settings_field(
			'put_to_maintenance', // id
			'Put To Maintenance', // title
			[$this, 'fiad_put_to_maintenance'], // callback
			'fiber-admin-maintenance-mode', // page
			'fiad_maintenance_mode_section' // section
		);
	}
	
	public function fiad_section_info(){
	}
	
	public function fiad_put_to_maintenance(){
		?>
		<fieldset>
			<label for="put_to_maintenance" class="fiber-admin-toggle">
				<input type="checkbox" name="fiad_maintenance_mode[put_to_maintenance]" id="put_to_maintenance"
				       value="yes" <?php checked(esc_attr(fiad_get_miscellaneous_option('put_to_maintenance')), 'yes'); ?> />
				<span class="slider round"></span>
			</label>
		</fieldset>
		<?php
	}
}