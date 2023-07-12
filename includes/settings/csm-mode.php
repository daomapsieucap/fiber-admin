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
			'enable', // id
			'Activate', // title
			[$this, 'fiad_enable_csm_mode'], // callback
			'fiber-admin-csm-mode', // page
			'fiad_csm_general_section' // section
		);
		
		add_settings_field(
			'mode', // id
			'Mode', // title
			[$this, 'fiad_mode_selector'], // callback
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
			'page', // id
			'Select Page', // title
			[$this, 'fiad_csm_mode_page'], // callback
			'fiber-admin-csm-mode', // page
			'fiad_csm_content_section' // section
		);
		
		add_settings_field(
			'csm_extra_css', // id
			'Extra CSS', // title
			[$this, 'fiad_csm_mode_extra_css'], // callback
			'fiber-admin-csm-mode', // page
			'fiad_csm_content_section' // section
		);
		
		add_settings_field(
			'csm_extra_js', // id
			'Extra JS', // title
			[$this, 'fiad_csm_mode_extra_js'], // callback
			'fiber-admin-csm-mode', // page
			'fiad_csm_content_section' // section
		);
	}
	
	public function fiad_section_info(){
	}
	
	public function fiad_enable_csm_mode(){
		?>
        <fieldset>
            <label for="enable" class="fiber-admin-toggle">
                <input type="checkbox" name="fiad_csm_mode[enable]"
                       id="enable"
                       value="yes" <?php checked(esc_attr(fiad_get_csm_mode_option('enable')), 'yes'); ?> />
                <span class="slider round"></span>
            </label>
        </fieldset>
		<?php
	}
	
	public function fiad_mode_selector(){
		$selected_mode = fiad_get_csm_mode_option('mode');
		?>
        <fieldset>
            <label for="mode">
                <select class="fiber-admin-selection--multiple" name="fiad_csm_mode[mode]"
                        id="mode">
                    <option value="maintenance" <?= $selected_mode == 'maintenance' ? 'selected' : ''; ?>>Maintenance
                    </option>
                    <option value="coming-soon" <?= $selected_mode == 'coming-soon' ? 'selected' : ''; ?>>Coming Soon
                    </option>
                </select>
            </label>
        </fieldset>
		<?php
	}
	
	public function fiad_csm_mode_page(){
		$selected_page = fiad_get_csm_mode_option('page');
		$pages         = get_pages();
		?>
        <fieldset>
            <label for="csm_mode_page">
                <select class="fiber-admin-selection--multiple" name="fiad_csm_mode[page]"
                        id="csm_mode_page">
					<?php
					foreach($pages as $page){
						$selected = $selected_page == $page->ID ? 'selected' : '';
						?>
                        <option value="<?= $page->ID; ?>" <?= $selected; ?>><?= $page->post_title; ?></option>
						<?php
					}
					?>
                </select>
            </label>
        </fieldset>
		<?php
	}
	
	public function fiad_csm_mode_extra_css(){
		// Enqueue code editor and settings for manipulating HTML.
		$settings = wp_enqueue_code_editor(['type' => 'text/css']);
		
		// Return if the editor was not enqueued.
		if(false === $settings){
			return;
		}
		
		wp_add_inline_script(
			'code-editor',
			sprintf(
				'jQuery( function() { wp.codeEditor.initialize( "csm-extra-css", %s ); } );',
				wp_json_encode($settings)
			)
		);
		
		?>
        <fieldset>
            <textarea
                    id="csm-extra-css"
                    name="fiad_csm_mode[csm_extra_css]"><?php echo esc_html(fiad_get_csm_mode_option('csm_extra_css')); ?></textarea>
        </fieldset>
		<?php
	}
	
	public function fiad_csm_mode_extra_js(){
		// Enqueue code editor and settings for manipulating HTML.
		$settings = wp_enqueue_code_editor(['type' => 'javascript']);
		
		// Return if the editor was not enqueued.
		if(false === $settings){
			return;
		}
		
		wp_add_inline_script(
			'code-editor',
			sprintf(
				'jQuery( function() { wp.codeEditor.initialize( "csm-extra-js", %s ); } );',
				wp_json_encode($settings)
			)
		);
		
		?>
        <fieldset>
            <textarea
                    id="csm-extra-js"
                    name="fiad_csm_mode[csm_extra_js]"><?php echo fiad_get_csm_mode_option('csm_extra_js'); ?></textarea>
        </fieldset>
		<?php
	}
}