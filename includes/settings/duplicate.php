<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Duplicate post
 */
class Fiber_Admin_Setting_Duplicate{
	public function __construct(){
	}
	
	public function fiad_duplicate_init(){
		register_setting(
			'fiad_duplicate_group',
			'fiad_duplicate',
			[$this, 'sanitize_text_field']
		);
		
		add_settings_section(
			'fiad_duplicate_section',
			'<span class="dashicons dashicons-list-view"></span> Setting',
			[$this, 'fiad_section_info'],
			'fiber-admin-duplicate'
		);
		
		add_settings_field(
			'post_types', // id
			'Exclude Post Types', // title
			[$this, 'fiad_duplicate_post_types'], // callback
			'fiber-admin-duplicate', // page
			'fiad_duplicate_section' // section
		);
	}
	
	public function fiad_section_info(){
	}
	
	public function fiad_duplicate_post_types(){
		$post_types          = get_post_types(['public' => true], 'objects');
		$selected_post_types = fiad_get_duplicate_option('exclude_post_types');
		if(!$selected_post_types){
			$selected_post_types = [];
		}
		$post_type_order = array_flip(array_keys($post_types));
		uasort($post_types, function($a, $b) use ($post_type_order){
			if($a->_builtin !== $b->_builtin){
				return $a->_builtin ? -1 : 1;
			}
			return $post_type_order[$a->name] <=> $post_type_order[$b->name];
		});
		?>
        <fieldset>
            <div class="fiber-admin-checkbox-list" id="exclude_post_types">
					<?php
					if($post_types){
						foreach($post_types as $slug => $post_type){
							if($slug == 'attachment'){
								continue;
							}
							?>
                            <label>
                                <input type="checkbox" name='fiad_duplicate[exclude_post_types][]' value="<?php echo esc_attr($slug); ?>" <?php checked(in_array($slug, $selected_post_types), true); ?> />
                                <?php echo $post_type->label; ?>
                            </label>
							<?php
						}
					}
					?>
            </div>
        </fieldset>
		<?php
	}
}