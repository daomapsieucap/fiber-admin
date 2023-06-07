<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Maintenance Mode
 */
class Fiber_Admin_Maintenance_Mode_Settings{
	public function __construct(){
	}
	
	public function fiad_maintenance_mode_init(){
		register_setting(
			'fiad_maintenance_mode_group',
			'fiad_maintenance_mode',
			[$this, 'sanitize_text_field']
		);
		
		add_settings_section(
			'fiad_maintenance_mode_section',
			'<span class="dashicons dashicons-admin-generic"></span> General',
			[$this, 'fiad_section_info'],
			'fiber-admin-maintenance-mode'
		);
		
		add_settings_field(
			'enable_maintenance_mode', // id
			'Enable Maintenance Mode', // title
			[$this, 'fiad_enable_maintenance_mode'], // callback
			'fiber-admin-maintenance-mode', // page
			'fiad_maintenance_mode_section' // section
		);
		
		add_settings_section(
			'fiad_maintenance_content_section',
			'<span class="dashicons dashicons-editor-table"></span> Content',
			[$this, 'fiad_section_info'],
			'fiber-admin-maintenance-mode'
		);
		
		add_settings_field(
			'maintenance_mode_page', // id
			'Maintenance Mode Page', // title
			[$this, 'fiad_maintenance_mode_page'], // callback
			'fiber-admin-maintenance-mode', // page
			'fiad_maintenance_content_section' // section
		);
		
		add_settings_field(
			'maintenance_mode_extra_css', // id
			'Extra CSS', // title
			[$this, 'fiad_maintenance_mode_extra_css'], // callback
			'fiber-admin-maintenance-mode', // page
			'fiad_maintenance_content_section' // section
		);
		
		add_settings_field(
			'maintenance_mode_extra_js', // id
			'Extra JS', // title
			[$this, 'fiad_maintenance_mode_extra_js'], // callback
			'fiber-admin-maintenance-mode', // page
			'fiad_maintenance_content_section' // section
		);
	}
	
	public function fiad_section_info(){
	}
	
	public function fiad_enable_maintenance_mode(){
		?>
        <fieldset>
            <label for="enable_maintenance_mode" class="fiber-admin-toggle">
                <input type="checkbox" name="fiad_maintenance_mode[enable_maintenance_mode]"
                       id="enable_maintenance_mode"
                       value="yes" <?php checked(esc_attr(fiad_get_maintenance_mode_option('enable_maintenance_mode')), 'yes'); ?> />
                <span class="slider round"></span>
            </label>
        </fieldset>
		<?php
	}
	
	public function fiad_maintenance_mode_page(){
		$selected_post_types = fiad_get_maintenance_mode_option('maintenance_mode_page');
		$args                = [
			'post_type'        => 'page',
			'suppress_filters' => true,
		];
		$page_query          = new WP_Query($args);
		?>
        <fieldset>
            <label for="maintenance_mode_page">
                <select class="fiber-admin-selection--multiple" name="fiad_maintenance_mode[maintenance_mode_page][]"
                        id="maintenance_mode_page" multiple>
					<?php
					while($page_query->have_posts()){
						$page_query->the_post();
						$id       = get_the_ID();
						$selected = (int)$selected_post_types[0] == $id ? 'selected' : '';
						?>
                        <option value="<?= $id; ?>" <?= $selected; ?>><?= get_the_title(); ?></option>
						<?php
					}
					?>
                </select>
            </label>
            <p class="description">
                Select multiple items with <strong>Ctrl-Click</strong> for Windows or <strong>Cmd-Click</strong> for Mac
            </p>
        </fieldset>
		<?php
	}
	
	public function fiad_maintenance_mode_extra_css(){
		?>
        <fieldset>
            <textarea
                    name="fiad_maintenance_mode[maintenance_mode_extra_css]"><?php echo esc_html(fiad_get_maintenance_mode_option('maintenance_mode_extra_css')); ?></textarea>
        </fieldset>
		<?php
	}
	
	public function fiad_maintenance_mode_extra_js(){
		?>
        <fieldset>
            <textarea
                    name="fiad_maintenance_mode[maintenance_mode_extra_js]"><?php echo esc_html(fiad_get_maintenance_mode_option('maintenance_mode_extra_js')); ?></textarea>
        </fieldset>
		<?php
	}
}