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
		add_filter('pre_update_option_fiad_csm_mode', [$this, 'fiad_csm_add_default']);
		
		// Apply for both enable and preview mode
		add_action('wp_enqueue_scripts', [$this, 'fiad_csm_dequeue_all_assets'], PHP_INT_MAX);
		add_action('wp_enqueue_scripts', [$this, 'fiad_csm_enqueue_jquery'], PHP_INT_MAX);
		add_filter('fiad_csm_extra_css', [$this, 'fiad_csm_extra_css']);
		add_filter('fiad_csm_extra_js', [$this, 'fiad_csm_extra_js']);
		add_filter('template_include', [$this, 'fiad_csm_page_preview']);
		
		// customize edit page
		add_filter('vc_is_valid_post_type_be', [$this, 'fiad_csm_disable_vc_editor'], 10, 2);
		add_action('add_meta_boxes', [$this, 'fiad_csm_add_metabox'], PHP_INT_MAX);
		add_action('save_post', [$this, 'fiad_csm_save_postdata']);
		add_action('admin_enqueue_scripts', [$this, 'fiad_csm_enqueue_customize_assets']);
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
	
	public function fiad_csm_page_preview($template){
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
	
	public function fiad_csm_enqueue_jquery(){
		$extra_js = wp_unslash(fiad_get_csm_mode_option('csm_extra_js'));
		if(strpos($extra_js, 'jQuery(document).ready') !== false){
			wp_enqueue_script('jquery-core');
		}
	}
	
	public function fiad_csm_add_default($value){
		$mode = fiad_get_csm_mode_option('mode');
		if(!$mode){
			$default_css_path       = FIBERADMIN_ASSETS_URL . 'generate-pages/csm-mode/default-css.txt';
			$value['csm_extra_css'] = fiad_file_get_content($default_css_path);
		}
		
		return $value;
	}
	
	public function fiad_csm_dequeue_all_assets(){
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
				$content_url         = FIBERADMIN_ASSETS_URL . 'generate-pages/csm-mode/' . $mode . '.txt';
				$post_args           = [
					'post_type'     => 'page',
					'post_title'    => $title,
					'post_status'   => 'publish',
					'page_template' => FIBERADMIN_CSM_TEMPLATE,
				];
				$post_id             = wp_insert_post($post_args);
				$csm_default_content = [
					'content'    => fiad_file_get_content($content_url),
					'background' => "https://colorlib.com/etc/cs/images/countdown-3-1600x900.jpg",
					'logo'       => get_site_icon_url(),
				];
				update_post_meta(
					$post_id,
					'fiad_csm_content',
					$csm_default_content
				);
			}
		}
		
		return $value;
	}
	
	/**
	 * Disable Editor
	 */
	
	public function fiad_csm_disable_vc_editor($enabled){
		if(fiad_is_csm_template()){
			remove_post_type_support('page', 'editor');
			
			return false;
		}
		
		return $enabled;
	}
	
	public function fiad_csm_add_metabox(){
		if(fiad_is_csm_template()){
			add_meta_box(
				'fiad_csm_content',
				'Coming Soon / Maintenance',
				[$this, 'fiad_csm_metabox_html'],
				'page',
				'normal',
				'high'
			);
		}
	}
	
	public function fiad_csm_metabox_html($post){
		$csm_content = get_post_meta($post->ID, 'fiad_csm_content', true);
		?>
        <fieldset class="fiber-admin-metabox fiad_csm_editor">
            <h2>Content</h2>
			<?php
			wp_editor($csm_content['content'], 'fiad_csm_content-editor', [ // don't set id same with meta box id => conflict css
				'default_editor' => 'tinymce',
				'textarea_name'  => 'fiad_csm_content[content]',
				'media_buttons'  => false,
				'textarea_rows'  => 10,
			]);
			?>
        </fieldset>
        <fieldset class="fiber-admin-metabox fiad_csm_logo">
            <h2>Logo</h2>
            <div class="fiber-admin-preview">
                <img src="<?php echo esc_url($csm_content['logo']); ?>"
                     alt="<?php echo esc_attr(get_bloginfo('name')); ?>"/>
            </div>
            <label>
                <input class="regular-text" type="text" name="fiad_csm_content[logo]"
                       placeholder="<?php echo __('Input / Choose your Logo image', 'fiber-admin'); ?>"
                       value="<?php echo esc_url($csm_content['logo']); ?>"/>
            </label>
            <button class="button fiber-admin-upload"><?php echo __('Select Image', 'fiber-admin'); ?></button>
        </fieldset>
        <fieldset class="fiber-admin-metabox fiad_csm_background_image">
            <h2>Background</h2>
            <div class="fiber-admin-preview">
                <img src="<?php echo esc_url($csm_content['background']); ?>"
                     alt="<?php echo esc_attr(get_bloginfo('name')); ?>"/>
            </div>
            <label>
                <input class="regular-text" type="text" name="fiad_csm_content[background]"
                       placeholder="<?php echo __('Input / Choose your Background image', 'fiber-admin'); ?>"
                       value="<?php echo esc_url($csm_content['background']); ?>"/>
            </label>
            <button class="button fiber-admin-upload"><?php echo __('Select Image', 'fiber-admin'); ?></button>
        </fieldset>
		<?php
	}
	
	public function fiad_csm_save_postdata($post_id){
		if(fiad_is_csm_template()){
			update_post_meta($post_id, 'fiad_csm_content', fiad_array_key_exists('fiad_csm_content', $_POST));
		}
	}
	
	public function fiad_csm_enqueue_customize_assets(){
		if(fiad_is_csm_template()){
			wp_enqueue_media();
			$suffix = '';
			if(!FIBERADMIN_DEV_MODE){
				$suffix = '.min';
			}
			wp_enqueue_script('fiad-csm-assets', FIBERADMIN_ASSETS_URL . 'js/fiber-admin' . $suffix . '.js', ['jquery'], FIBERADMIN_VERSION);
			wp_enqueue_style('fiad-csm-css', FIBERADMIN_ASSETS_URL . 'css/fiber-csm.css', false, FIBERADMIN_VERSION);
		}
	}
}

new Fiber_Admin_CSM_Mode();