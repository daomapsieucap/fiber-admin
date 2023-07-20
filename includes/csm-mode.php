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
			add_filter('template_include', [$this, 'fiad_csm_content']);
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
		add_action('add_meta_boxes', [$this, 'fiad_add_featured_box'], 1);
		add_action('save_post', [$this, 'fiad_save_postdata']);
		add_action('admin_enqueue_scripts', [$this, 'fiad_enqueue_customize_assets']);
		add_filter('wpseo_metabox_prio', 'low');
	}
	
	public function fiad_csm_page_templates($templates){
		$templates[FIBERADMIN_CSM_TEMPLATE] = "Coming Soon/Maintenance";
		
		return $templates;
	}
	
	//No Header & Footer Page
	public function fiad_csm_content($template){
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
		$extra_css = fiad_get_csm_mode_option('csm_extra_css');
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
			
			add_meta_box(
				'fiad_csm_logo',
				'Coming Soon / Maintenance Logo',
				[$this, 'fiad_csm_logo_html'],
				'page',
				'normal',
				'high'
			);
			
			add_meta_box(
				'fiad_csm_background_image',
				'Coming Soon / Maintenance Background Image',
				[$this, 'fiad_csm_background_image_html'],
				'page',
				'normal',
				'high'
			);
		}
	}
	
	public function fiad_csm_content_html($post){
		$csm_content = get_post_meta($post->ID, 'fiad_csm_content', true);
		wp_editor($csm_content, 'db_error_message', [
			'default_editor' => 'tinymce',
			'textarea_name'  => 'fiad_db_error[db_error_message]',
			'media_buttons'  => false,
			'textarea_rows'  => 5,
		]);
	}
	
	public function fiad_csm_logo_html($post){
		$value = get_post_meta($post->ID, 'fiad_csm_logo', true);
		?>
        <fieldset class="fiad_csm_logo">
            <div class="fiber-admin-preview">
                <img src="<?php echo esc_url($value); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>"/>
            </div>
            <label>
                <input class="regular-text" type="text" name="fiad_csm_logo"
                       placeholder="<?php echo __('Input / Choose your Logo image', 'fiber-admin'); ?>"
                       value="<?php echo esc_url($value); ?>"/>
            </label>
            <button class="button fiber-admin-upload"><?php echo __('Select Image', 'fiber-admin'); ?></button>
        </fieldset>
		<?php
	}
	
	public function fiad_csm_background_image_html($post){
		$value = get_post_meta($post->ID, 'fiad_csm_background_image', true);
        var_dump($value);
		?>
        <fieldset class="fiad_csm_background_image">
            <div class="fiber-admin-preview thickbox">
                <img src="<?php echo esc_url($value); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>"/>
            </div>
            <label>
                <input class="regular-text" type="text" name="fiad_csm_background_image"
                       placeholder="<?php echo __('Input / Choose your Background image', 'fiber-admin'); ?>"
                       value="<?php echo esc_url($value); ?>"/>
            </label>
            <button class="button fiber-admin-upload"><?php echo __('Select Image', 'fiber-admin'); ?></button>
        </fieldset>
		<?php
	}
	
	public function fiad_save_postdata($post_id){
		if(fiad_is_csm_template()){
			update_post_meta(
				$post_id,
				'fiad_csm_content',
				$_POST['fiad_csm_content']
			);
			
			update_post_meta(
				$post_id,
				'fiad_csm_logo',
				$_POST['fiad_csm_logo']
			);
			
			update_post_meta(
				$post_id,
				'fiad_csm_background_image',
				$_POST['fiad_csm_background_image']
			);
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