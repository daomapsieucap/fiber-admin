<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Custom Post Order
 */
class Fiber_Admin_Setting_CPO{
	public function __construct(){
	}
	
	public function fiad_cpo_init(){
		register_setting(
			'fiad_cpo_group',
			'fiad_cpo',
			array($this, 'sanitize_text_field')
		);
		
		add_settings_section(
			'fiad_cpo_section',
			'<span class="dashicons dashicons-list-view"></span> Setting',
			array($this, 'fiad_section_info'),
			'fiber-admin-cpo'
		);
		
		add_settings_field(
			'post_types', // id
			'Post Types', // title
			array($this, 'fiad_cpo_post_types'), // callback
			'fiber-admin-cpo', // page
			'fiad_cpo_section' // section
		);
		
		add_settings_field(
			'override_default_query', // id
			'Override Default Query', // title
			array($this, 'fiad_cpo_override_query'), // callback
			'fiber-admin-cpo', // page
			'fiad_cpo_section' // section
		);
		
		add_settings_field(
			'taxonomies', // id
			'Taxonomies', // title
			array($this, 'fiad_cpo_taxonomies'), // callback
			'fiber-admin-cpo', // page
			'fiad_cpo_section' // section
		);
		
		add_settings_field(
			'override_default_tax_query', // id
			'Override Default Taxonomy Query', // title
			array($this, 'fiad_cpo_override_tax_query'), // callback
			'fiber-admin-cpo', // page
			'fiad_cpo_section' // section
		);
	}
	
	public function fiad_section_info(){
	}
	
	public function fiad_cpo_post_types(){
		$post_types          = get_post_types(array('public' => true), 'objects');
		$selected_post_types = fiad_get_cpo_option('post_types');
		if(!$selected_post_types){
			$selected_post_types = array();
		}
		?>
        <fieldset>
            <label for="post_types">
                <select class="fiber-admin-selection--multiple" id="post_types" name='fiad_cpo[post_types][]' multiple>
					<?php
					if($post_types){
						foreach($post_types as $slug => $post_type){
							$selected = '';
							if(in_array($slug, $selected_post_types)){
								$selected = 'selected';
							}
							?>
                            <option value="<?php echo $slug; ?>" <?php echo $selected; ?>><?php echo $post_type->label; ?></option>
							<?php
						}
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
	
	public function fiad_cpo_override_query(){
		?>
        <fieldset>
            <label for="override_default_query" class="fiber-admin-toggle">
                <input type="checkbox" name="fiad_cpo[override_default_query]"
                       id="override_default_query"
                       value="yes" <?php checked(esc_attr(fiad_get_cpo_option('override_default_query')), 'yes'); ?> />
                <span class="slider round"></span>
            </label>
        </fieldset>
		<?php
	}
	
	public function fiad_cpo_taxonomies(){
		$taxonomies          = get_taxonomies(array(), 'objects');
		$selected_taxonomies = fiad_get_cpo_option('taxonomies');
		if(!$selected_taxonomies){
			$selected_taxonomies = array();
		}
		$exclude_slugs = array(
			'nav_menu',
			'link_category',
			'post_format',
			'wp_theme',
			'product_type',
			'product_visibility',
			'product_shipping_class'
		);
		?>
        <fieldset>
            <label for="taxonomies">
                <select class="fiber-admin-selection--multiple" id="taxonomies" name='fiad_cpo[taxonomies][]' multiple>
					<?php
					if($taxonomies){
						foreach($taxonomies as $slug => $taxonomy){
							if(!in_array($slug, $exclude_slugs)){
								$selected = '';
								if(in_array($slug, $selected_taxonomies)){
									$selected = 'selected';
								}
								?>
                                <option value="<?php echo $slug; ?>" <?php echo $selected; ?>><?php echo $taxonomy->label; ?></option>
								<?php
							}
						}
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
	
	public function fiad_cpo_override_tax_query(){
		?>
        <fieldset>
            <label for="override_default_tax_query" class="fiber-admin-toggle">
                <input type="checkbox" name="fiad_cpo[override_default_tax_query]"
                       id="override_default_tax_query"
                       value="yes" <?php checked(esc_attr(fiad_get_cpo_option('override_default_tax_query')), 'yes'); ?> />
                <span class="slider round"></span>
            </label>
        </fieldset>
		<?php
	}
}