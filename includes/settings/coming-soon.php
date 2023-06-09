<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Coming Soon Mode
 */
class Fiber_Admin_Coming_Soon_Mode_Settings{
	public function __construct(){
	}
	
	public function fiad_coming_soon_init(){
		register_setting(
			'fiad_coming_soon_group',
			'fiad_coming_soon',
			[$this, 'sanitize_text_field']
		);
		
		add_settings_section(
			'fiad_coming_soon_section',
			'<span class="dashicons dashicons-admin-generic"></span> General',
			[$this, 'fiad_section_info'],
			'fiber-admin-maintenance-mode'
		);
		
		add_settings_field(
			'enable_coming_soon', // id
			'Enable Coming Soon Mode', // title
			[$this, 'fiad_enable_coming_soon'], // callback
			'fiber-admin-maintenance-mode', // page
			'fiad_coming_soon_section' // section
		);
		
		add_settings_section(
			'fiad_maintenance_content_section',
			'<span class="dashicons dashicons-editor-table"></span> Content',
			[$this, 'fiad_section_info'],
			'fiber-admin-maintenance-mode'
		);
		
		add_settings_field(
			'coming_soon_page', // id
			'Coming Soon Mode Page', // title
			[$this, 'fiad_coming_soon_page'], // callback
			'fiber-admin-maintenance-mode', // page
			'fiad_maintenance_content_section' // section
		);
		
		add_settings_field(
			'coming_soon_extra_css', // id
			'Extra CSS', // title
			[$this, 'fiad_coming_soon_extra_css'], // callback
			'fiber-admin-maintenance-mode', // page
			'fiad_maintenance_content_section' // section
		);
		
		add_settings_field(
			'coming_soon_extra_js', // id
			'Extra JS', // title
			[$this, 'fiad_coming_soon_extra_js'], // callback
			'fiber-admin-maintenance-mode', // page
			'fiad_maintenance_content_section' // section
		);
	}
	
	public function fiad_section_info(){
	}
	
	public function fiad_enable_coming_soon(){
		?>
		<fieldset>
			<label for="enable_coming_soon" class="fiber-admin-toggle">
				<input type="checkbox" name="fiad_coming_soon[enable_coming_soon]"
				       id="enable_coming_soon"
				       value="yes" <?php checked(esc_attr(fiad_get_coming_soon_option('enable_coming_soon')), 'yes'); ?> />
				<span class="slider round"></span>
			</label>
		</fieldset>
		<?php
	}
	
	public function fiad_coming_soon_page(){
		$selected_post_types = fiad_get_coming_soon_option('coming_soon_page');
		$args                = [
			'post_type'        => 'page',
			'suppress_filters' => true,
		];
		$page_query          = new WP_Query($args);
		?>
		<fieldset>
			<label for="coming_soon_page">
				<select class="fiber-admin-selection--multiple" name="fiad_coming_soon[coming_soon_page][]"
				        id="coming_soon_page" multiple>
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
	
	public function fiad_coming_soon_extra_css(){
		?>
		<fieldset>
            <textarea
	            name="fiad_coming_soon[coming_soon_extra_css]"><?php echo esc_html(fiad_get_coming_soon_option('coming_soon_extra_css')); ?></textarea>
		</fieldset>
		<?php
	}
	
	public function fiad_coming_soon_extra_js(){
		?>
		<fieldset>
            <textarea
	            name="fiad_coming_soon[coming_soon_extra_js]"><?php echo fiad_get_coming_soon_option('coming_soon_extra_js'); ?></textarea>
		</fieldset>
		<?php
	}
}