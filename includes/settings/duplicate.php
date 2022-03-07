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
		add_action('admin_menu', array($this, 'fiad_duplicate'));
		add_action('admin_init', array($this, 'fiad_duplicate_init'));
	}
	
	public function fiad_duplicate(){
		add_submenu_page(
			'fiber-admin',
			'Fiber Admin Duplicate Post',
			'Duplicate Post',
			'manage_options',
			'fiber-admin-duplicate',
			array($this, 'fiad_duplicate_page')
		);
	}
	
	public function fiad_duplicate_page(){
		?>
        <div class="wrap">
            <h2>Fiber Admin Duplicate Post</h2>
			<?php settings_errors(); ?>

            <form class="fiber-admin" method="post" action="options.php">
				<?php
				settings_fields('fiad_duplicate_group');
				do_settings_sections('fiber-admin-duplicate');
				
				submit_button();
				?>
            </form>
        </div>
		<?php
	}
	
	public function fiad_duplicate_init(){
		register_setting(
			'fiad_duplicate_group',
			'fiad_duplicate',
			array($this, 'sanitize_text_field')
		);
		
		add_settings_section(
			'fiad_duplicate_section',
			'<span class="dashicons dashicons-list-view"></span> Setting',
			array($this, 'fiad_section_info'),
			'fiber-admin-duplicate'
		);
		
		add_settings_field(
			'post_types', // id
			'Exclude Post Types', // title
			array($this, 'fiad_duplicate_post_types'), // callback
			'fiber-admin-duplicate', // page
			'fiad_duplicate_section' // section
		);
	}
	
	public function fiad_section_info(){
	}
	
	public function fiad_duplicate_post_types(){
		$post_types          = get_post_types(array('public' => true), 'objects');
		$selected_post_types = fiad_get_duplicate_option('post_types');
		if(!$selected_post_types){
			$selected_post_types = array();
		}
		?>
        <fieldset>
            <label for="post_types">
                <select class="fiber-admin-selection--multiple" id="post_types"
                        name='fiad_duplicate[exclude_post_types][]'
                        multiple>
					<?php
					if($post_types){
						foreach($post_types as $slug => $post_type){
							if($slug == 'attachment'){
								continue;
							}
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
}

new Fiber_Admin_Setting_Duplicate();