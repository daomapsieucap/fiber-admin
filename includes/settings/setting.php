<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Setting Page
 */
class Fiber_Admin_Setting{
	
	public function __construct(){
		add_action('admin_menu', [$this, 'fiad_setting']);
		add_action('admin_init', [$this, 'fiad_setting_init']);
		
		// register styles
		add_action("admin_enqueue_scripts", [$this, 'fiad_styles']);
	}
	
	public function fiad_styles($hook_suffix){
		if(strpos($hook_suffix, 'fiber-admin') !== false){
			wp_enqueue_style('fiber-admin', FIBERADMIN_ASSETS_URL . 'css/fiber-admin.css', false, FIBERADMIN_VERSION);
		}
	}
	
	public function fiad_setting_init(){
		if(isset($_POST['fiber-admin-submit'])){
			check_admin_referer("fiber-admin");
			$this->fiad_save_options();
			$updated_parameters = 'updated=true';
			if(isset($_GET['tab'])){
				$updated_parameters = 'updated=true&tab=' . $_GET['tab'];
			}
			wp_redirect(admin_url('options-general.php?page=fiber-admin&' . $updated_parameters));
			exit;
		}
	}
	
	public function fiad_setting(){
		add_submenu_page(
			'options-general.php',
			'Fiber Admin',
			'Fiber Admin',
			'manage_options',
			'fiber-admin',
			[$this, 'fiad_setting_html']
		);
	}
	
	public function fiad_setting_html(){
		// check user capabilities
		if(!current_user_can('manage_options')){
			return;
		}
		
		$form_action = admin_url("options-general.php?page=fiber-admin");
		if(isset ($_GET['tab'])){
			$form_action = admin_url("options-general.php?page=fiber-admin&tab=" . $_GET['tab']);
		}
		
		// nav
		echo '<nav class="nav-tab-wrapper">';
		if(isset ($_GET['tab'])){
			$this->fiad_setting_tab_navs($_GET['tab']);
		}else{
			$this->fiad_setting_tab_navs();
		}
		echo '</nav>';
		
		// content
		echo '<div class="tab-content">';
		echo '<div class="wrap">';
		echo '<form class="fiber-admin" method="POST" action="' . $form_action . '">';
		
		wp_nonce_field("fiber-admin");
		
		$current_tab = 'white-label';
		if(isset ($_GET['tab'])){
			$current_tab = $_GET['tab'];
		}
		echo '<h1>' . $this->fiad_setting_tabs()[$current_tab] . '</h1>';
		$this->fiad_setting_tab_content($current_tab);
		
		echo '</form>';
		echo '</div>';
		echo '</div>';
	}
	
	public function fiad_setting_tabs(){
		return [
			'white-label'   => 'White Label',
			'cpo'           => 'Custom Post Order',
			'duplicate'     => 'Duplicate Post',
			'db-error'      => 'Database Error',
			'csm-mode'      => 'Coming Soon & Maintenance Mode',
			'miscellaneous' => 'Miscellaneous',
		];
	}
	
	public function fiad_setting_tab_navs($current = 'white-label'){
		$tabs = $this->fiad_setting_tabs();
		foreach($tabs as $tab => $name){
			$class = ($tab == $current) ? ' nav-tab-active' : '';
			echo "<a class='nav-tab$class' href='?page=fiber-admin&tab=$tab' title='$name'>$name</a>";
			
		}
	}
	
	public function fiad_setting_tab_content($current = 'white-label'){
		switch($current){
			case 'cpo':
				$cpo = new Fiber_Admin_Setting_CPO();
				$cpo->fiad_cpo_init();
				break;
			case 'duplicate':
				$duplicate = new Fiber_Admin_Setting_Duplicate();
				$duplicate->fiad_duplicate_init();
				break;
			case 'db-error':
				$white_label = new Fiber_Admin_White_Label_Settings();
				$db_error    = new Fiber_Admin_DB_Error_Settings();
				
				$white_label->fiad_enqueue_scripts();
				$db_error->fiad_db_error_page_init();
				break;
			case 'miscellaneous':
				$miscellaneous = new Fiber_Admin_Miscellaneous();
				$miscellaneous->fiad_miscellaneous_init();
				break;
			case 'csm-mode':
				$csm_mode = new Fiber_Admin_CSM_Mode_Settings();
				$csm_mode->fiad_csm_mode_init();
				break;
			default:
				$white_label = new Fiber_Admin_White_Label_Settings();
				$white_label->fiad_enqueue_scripts();
				$white_label->fiad_page_init();
				break;
		}
		
		do_settings_sections('fiber-admin-' . $current);
		
		$this->fiad_preview_mode($current);
	}
	
	public function fiad_preview_mode($current){
		$message = __('Please enable "Activate" option and save the settings first!', 'fiber-admin');
		$url     = '';
		if($current == 'db-error'){
			$can_preview = fiad_check_db_error_file();
			$url         = content_url('db-error.php');
		}else{
			$mode        = fiad_get_csm_mode_option('mode');
			$can_preview = (bool) fiad_get_csm_mode_option('page');
			if(fiad_check_csm_mode_file()){
				$url = get_site_url() . '/' . $mode . '?preview=true';
			}
			$message = __('Please select page with correct page template for the mode', 'fiber-admin');
		}
		if($current == 'db-error' || $current == 'csm-mode'){
			echo '<input type="submit" name="fiber-admin-submit" id="fiber-admin-submit" class="button button-primary" value="Save Changes">';
			if(!$can_preview){
				?>
                <p class="description"><?php echo __('Preview is not available. ' . $message, 'fiber-admin'); ?></p>
				<?php
			}else{
				$txt_preview = __('Preview', 'fiber-admin');
				?>
                <a class="button" href="<?php echo $url; ?>" target="_blank"
                   title="<?php echo $txt_preview; ?>">
					<?php echo $txt_preview; ?>
                </a>
				<?php
			}
		}else{
			submit_button(null, 'primary', 'fiber-admin-submit');
		}
	}
	
	public function fiad_save_options(){
		global $pagenow;
		if($pagenow == 'options-general.php' && $_GET['page'] == 'fiber-admin'){
			$tab = 'white-label';
			if(isset ($_GET['tab'])){
				$tab = $_GET['tab'];
			}
			
			switch($tab){
				case 'cpo':
					$option_key = 'fiad_cpo';
					break;
				case 'duplicate':
					$option_key = 'fiad_duplicate';
					break;
				case 'db-error':
					$option_key = 'fiad_db_error';
					break;
				case 'miscellaneous':
					$option_key = 'fiad_miscellaneous';
					break;
				case 'csm-mode':
					$option_key = 'fiad_csm_mode';
					break;
				default:
					$option_key = 'fiber_admin';
					break;
			}
			
			$ignore_key = [
				'db_error_message',
				'csm_extra_css',
				'csm_extra_js',
				'db_error_extra_css',
				'login_extra_css',
			];
			
			if(isset($_POST[$option_key])){
				$options = $new_options = $_POST[$option_key];
				foreach($options as $key => $value){
					if(!in_array($key, $ignore_key) && !is_array($new_options[$key])){
						$new_options[$key] = sanitize_text_field($value);
					}elseif(in_array($key, $ignore_key)){
						$new_options[$key] = wp_unslash($value);
					}
				}
			}else{
				$new_options = [];
			}
			
			//prevent reset 'added' option of csm mode
			if($option_key == 'fiad_csm_mode'){
				$new_options['added_pages'] = fiad_get_csm_mode_option('added_pages');
				$new_options['added_css']   = fiad_get_csm_mode_option('added_css');
			}
			
			update_option($option_key, $new_options);
		}
	}
}

new Fiber_Admin_Setting();