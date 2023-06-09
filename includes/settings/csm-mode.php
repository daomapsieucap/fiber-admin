<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Coming Soon & Maintenance Mode
 */
class Fiber_Admin_CSM_Mode_Settings{
	public function __construct(){
	}

	public function fiad_csm_mode_init(){
		register_setting(
			'fiad_csm_mode_group',
			'fiad_csm_mode',
			[$this, 'sanitize_text_field']
		);

		add_settings_section(
			'fiad_csm_general_section',
			'<span class="dashicons dashicons-admin-generic"></span> General',
			[$this, 'fiad_section_info'],
			'fiber-admin-csm-mode'
		);

		add_settings_field(
			'enable_maintenance_mode', // id
			'Enable Maintenance Mode', // title
			[$this, 'fiad_enable_maintenance_mode'], // callback
			'fiber-admin-csm-mode', // page
			'fiad_csm_general_section' // section
		);

		add_settings_field(
			'enable_coming_soon_mode', // id
			'Enable Coming Soon Mode', // title
			[$this, 'fiad_enable_coming_soon_mode'], // callback
			'fiber-admin-csm-mode', // page
			'fiad_csm_general_section' // section
		);

		add_settings_section(
			'fiad_csm_content_section',
			'<span class="dashicons dashicons-editor-table"></span> Content',
			[$this, 'fiad_section_info'],
			'fiber-admin-csm-mode'
		);

		add_settings_field(
			'csm_mode_page', // id
			'Display Page', // title
			[$this, 'fiad_csm_mode_page'], // callback
			'fiber-admin-csm-mode', // page
			'fiad_csm_content_section' // section
		);

		add_settings_field(
			'csm_mode_extra_css', // id
			'Extra CSS', // title
			[$this, 'fiad_csm_mode_extra_css'], // callback
			'fiber-admin-csm-mode', // page
			'fiad_csm_content_section' // section
		);

		add_settings_field(
			'csm_mode_extra_js', // id
			'Extra JS', // title
			[$this, 'fiad_csm_mode_extra_js'], // callback
			'fiber-admin-csm-mode', // page
			'fiad_csm_content_section' // section
		);
	}

	public function fiad_section_info(){
	}

	public function fiad_enable_maintenance_mode(){
		?>
        <fieldset>
            <label for="enable_maintenance_mode" class="fiber-admin-toggle">
                <input type="checkbox" name="fiad_csm_mode[enable_maintenance_mode]"
                       id="enable_maintenance_mode"
                       value="yes" <?php checked(esc_attr(fiad_get_csm_mode_option('enable_maintenance_mode')), 'yes'); ?> />
                <span class="slider round"></span>
            </label>
        </fieldset>
		<?php
	}
	
	public function fiad_enable_coming_soon_mode(){
		?>
        <fieldset>
            <label for="enable_coming_soon_mode" class="fiber-admin-toggle">
                <input type="checkbox" name="fiad_csm_mode[enable_coming_soon_mode]"
                       id="enable_coming_soon_mode"
                       value="yes" <?php checked(esc_attr(fiad_get_csm_mode_option('enable_coming_soon_mode')), 'yes'); ?> />
                <span class="slider round"></span>
            </label>
        </fieldset>
		<?php
	}

	public function fiad_csm_mode_page(){
		$selected_post_types = fiad_get_csm_mode_option('csm_mode_page');
		$args                = [
			'post_type'        => 'page',
			'suppress_filters' => true,
		];
		$page_query          = new WP_Query($args);
		?>
        <fieldset>
            <label for="csm_mode_page">
                <select class="fiber-admin-selection--multiple" name="fiad_csm_mode[csm_mode_page][]"
                        id="csm_mode_page" multiple>
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

	public function fiad_csm_mode_extra_css(){
		?>
        <fieldset>
            <textarea
                    name="fiad_csm_mode[csm_mode_extra_css]"><?php echo esc_html(fiad_get_csm_mode_option('csm_mode_extra_css')); ?></textarea>
        </fieldset>
		<?php
	}

	public function fiad_csm_mode_extra_js(){
		?>
        <fieldset>
            <textarea
                    name="fiad_csm_mode[csm_mode_extra_js]"><?php echo fiad_get_csm_mode_option('csm_mode_extra_js'); ?></textarea>
        </fieldset>
		<?php
	}
}