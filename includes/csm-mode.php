<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Enable Coming Soon/Maintenance Mode
 */
class Fiber_Admin_CSM_Mode{
	public function __construct(){
		add_filter('theme_page_templates', [$this, 'fiad_csm_page_templates']);
		// Only apply when enable
		if(fiad_get_csm_mode_option('enable')){
			add_filter('template_include', [$this, 'fiad_csm_load_template']);
		}
		
		// create page when saving option the first time
		add_filter('pre_update_option_fiad_csm_mode', [$this, 'fiad_create_default_csm_page']);
		add_filter('pre_update_option_fiad_csm_mode', [$this, 'fiad_add_default_css']);
		
		// Apply for both enable and preview mode
		add_action('wp_enqueue_scripts', [$this, 'fiad_dequeue_all_for_csm'], PHP_INT_MAX);
		add_filter('fiad_csm_extra_css', [$this, 'fiad_csm_extra_css']);
		add_filter('fiad_csm_extra_js', [$this, 'fiad_csm_extra_js']);
		add_filter('template_include', [$this, 'fiad_preview_csm_page']);
		
		// customize edit page
		add_filter('vc_is_valid_post_type_be', [$this, 'fiad_disable_vc_editor'], 10, 2);
		add_action('add_meta_boxes', [$this, 'fiad_add_featured_box'], PHP_INT_MAX);
		add_action('save_post', [$this, 'fiad_save_postdata']);
		add_action('admin_enqueue_scripts', [$this, 'fiad_enqueue_customize_assets']);
		add_filter('wpseo_metabox_prio', 'low');
	}
	
	public function fiad_csm_page_templates($templates){
		$templates[FIBERADMIN_CSM_TEMPLATE] = "Coming Soon/Maintenance";
		
		return $templates;
	}
	
	//No Header & Footer Page
	public function fiad_csm_load_template($template){
		if(!fiad_is_admin_user_role()){
			return FIBERADMIN_CSM_PATH;
		}
		
		return $template;
	}
	
	public function fiad_preview_csm_page($template){
		//Sanitizes a string into a slug, which can be used in URLs or HTML attributes.
		if(fiad_is_preview() && fiad_is_admin_user_role()){
			return FIBERADMIN_CSM_PATH;
		}
		
		return $template;
	}
	
	public function fiad_csm_extra_css(){
		$extra_css = wp_unslash(fiad_get_csm_mode_option('csm_extra_css'));
		if($extra_css){
			return "<style>$extra_css</style>";
		}
		
		return '';
	}
	
	public function fiad_csm_extra_js(){
		$extra_js = wp_unslash(fiad_get_csm_mode_option('csm_extra_js'));
		if($extra_js){
			return "<script>$extra_js</script>";
		}
		
		return '';
	}
	
	public function fiad_add_default_css($value){
		$mode = fiad_get_csm_mode_option('mode');
		if(!$mode){
			$default_css_path       = FIBERADMIN_ASSETS_URL . 'generate-pages/csm-mode/default-css.txt';
			$value['csm_extra_css'] = fiad_file_get_content($default_css_path);
		}
		
		return $value;
	}
	
	public function fiad_dequeue_all_for_csm(){
		$csm_enable = fiad_get_csm_mode_option('enable');
		
		if(fiad_is_preview() //dequeue when preview mode
		   || !fiad_is_admin_user_role() && $csm_enable // dequeue when activate
		){
			fiad_dequeue_assets();
		}
	}
	
	public function fiad_create_default_csm_page($value){
		$csm_mode    = fiad_get_csm_mode_option('mode');
		$page_titles = [
			'coming-soon' => 'Coming Soon',
			'maintenance' => 'Maintenance',
		];
		if(!$csm_mode){
			foreach($page_titles as $mode => $title){
				$content_url = FIBERADMIN_ASSETS_URL . 'generate-pages/csm-mode/' . $mode . '.txt';
				$post_args   = [
					'post_type'     => 'page',
					'post_title'    => $title,
					'post_status'   => 'publish',
					'page_template' => FIBERADMIN_CSM_TEMPLATE,
				];
				$post_id     = wp_insert_post($post_args);
				update_post_meta(
					$post_id,
					'fiad_csm_content',
					fiad_file_get_content($content_url)
				);
			}
		}
		
		return $value;
	}
	
	/**
	 * Disable Editor
	 */
	
	public function fiad_disable_vc_editor($enabled){
		if(fiad_is_csm_template()){
			remove_post_type_support('page', 'editor');
			
			return false;
		}
		
		return $enabled;
	}
	
	public function fiad_add_featured_box(){
		if(fiad_is_csm_template()){
			add_meta_box(
				'fiad_csm_content',
				'Coming Soon / Maintenance Content',
				[$this, 'fiad_csm_content_html'],
				'page',
				'normal',
				'high'
			);
		}
	}
	
	public function fiad_csm_content_html($post){
		$csm_content = get_post_meta($post->ID, 'fiad_csm_content', true);
		?>
        <fieldset class="fiber-admin-editor">
			<?php
			wp_editor($csm_content['content'], 'fiad_csm_content-editor', [ // don't set id same with meta box id => conflict css
				'default_editor' => 'tinymce',
				'textarea_name'  => 'fiad_csm_content[content]',
				'media_buttons'  => false,
				'textarea_rows'  => 20,
			]);
			?>
        </fieldset>
        <fieldset class="fiad_csm_logo">
            <div class="fiber-admin-preview">
                <img src="<?php echo esc_url($csm_content['logo']); ?>"
                     alt="<?php echo esc_attr(get_bloginfo('name')); ?>"/>
            </div>
            <label>
                Logo
                <input class="regular-text" type="text" name="fiad_csm_content[logo]"
                       placeholder="<?php echo __('Input / Choose your Logo image', 'fiber-admin'); ?>"
                       value="<?php echo esc_url($csm_content['logo']); ?>"/>
            </label>
            <button class="button fiber-admin-upload"><?php echo __('Select Image', 'fiber-admin'); ?></button>
        </fieldset>
        <fieldset class="fiad_csm_background_image">
            <div class="fiber-admin-preview thickbox">
                <img src="<?php echo esc_url($csm_content['background']); ?>"
                     alt="<?php echo esc_attr(get_bloginfo('name')); ?>"/>
            </div>
            <label>
                Background
                <input class="regular-text" type="text" name="fiad_csm_content[background]"
                       placeholder="<?php echo __('Input / Choose your Background image', 'fiber-admin'); ?>"
                       value="<?php echo esc_url($csm_content['background']); ?>"/>
            </label>
            <button class="button fiber-admin-upload"><?php echo __('Select Image', 'fiber-admin'); ?></button>
        </fieldset>
		<?php
	}
	
	public function fiad_save_postdata($post_id){
		if(fiad_is_csm_template()){
			update_post_meta($post_id, 'fiad_csm_content', fiad_array_key_exists('fiad_csm_content', $_POST));
		}
	}
	
	public function fiad_enqueue_customize_assets(){
		if(fiad_is_csm_template()){
			wp_enqueue_media();
			$suffix = '';
			if(!FIBERADMIN_DEV_MODE){
				$suffix = '.min';
			}
			wp_enqueue_script('fiad-csm-assets', FIBERADMIN_ASSETS_URL . 'js/fiber-csm' . $suffix . '.js', ['jquery'], FIBERADMIN_VERSION);
		}
	}
}

new Fiber_Admin_CSM_Mode();