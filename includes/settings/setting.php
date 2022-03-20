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
		add_action('admin_menu', array($this, 'fiad_setting'));
		add_action('admin_init', array($this, 'fiad_setting_init'));
		
		// register styles
		add_action("admin_enqueue_scripts", array($this, 'fiad_styles'));
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
			array($this, 'fiad_setting_html'),
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
			$this->fiad_setting_tabs($_GET['tab']);
		}else{
			$this->fiad_setting_tabs();
		}
		echo '</nav>';
		
		// content
		echo '<div class="tab-content">';
		echo '<form class="fiber-admin" method="POST" action="' . $form_action . '">';
		wp_nonce_field("fiber-admin");
		if(isset ($_GET['tab'])){
			$this->fiad_setting_tab_content($_GET['tab']);
		}else{
			$this->fiad_setting_tab_content();
		}
		echo '</form>';
		echo '</div>';
	}
	
	public function fiad_setting_tabs($current = 'white-label'){
		$tabs = array(
			'white-label'   => 'White Label',
			'cpo'           => 'Custom Post Order',
			'duplicate'     => 'Duplicate Post',
			'db-error'      => 'Database Error',
			'miscellaneous' => 'Miscellaneous',
		);
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
			default:
				$white_label = new Fiber_Admin_White_Label_Settings();
				$white_label->fiad_enqueue_scripts();
				$white_label->fiad_page_init();
				break;
		}
		
		do_settings_sections('fiber-admin-' . $current);
		
		if($current == 'db-error'){
			echo '<input type="submit" name="fiber-admin-submit" id="fiber-admin-submit" class="button button-primary" value="Save Changes">';
			if(!fiad_check_db_error_file()){
				?>
                <p class="description"><?php echo __('Preview is not available. Please enable "Activate" option and save the settings first!', 'fiber-admin'); ?></p>
				<?php
			}else{
				$txt_preview = __('Preview', 'fiber-admin');
				?>
                <a class="button" href="<?php echo content_url('db-error.php'); ?>" target="_blank"
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
				default:
					$option_key = 'fiber_admin';
					break;
			}
			
			if(isset($_POST[$option_key])){
				$options = $new_options = $_POST[$option_key];
				foreach($options as $key => $value){
					if($key != 'db_error_message' && !is_array($new_options[$key])){
						$new_options[$key] = sanitize_text_field($value);
					}
				}
			}else{
				$new_options = array();
			}
			
			update_option($option_key, $new_options);
		}
	}
}

new Fiber_Admin_Setting();