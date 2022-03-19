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
		//add_action('admin_init', array($this, 'fiad_setting_init'));
		
		// register styles
		add_action("admin_enqueue_scripts", array($this, 'fiad_styles'));
	}
	
	public function fiad_styles($hook_suffix){
		if(strpos($hook_suffix, 'fiber-admin') !== false){
			wp_enqueue_style('fiber-admin', FIBERADMIN_ASSETS_URL . 'css/fiber-admin.css', false, FIBERADMIN_VERSION);
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
		echo '<form class="fiber-admin" method="post" action="options.php">';
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
				$db_error = new Fiber_Admin_DB_Error_Settings();
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
		submit_button();
	}
}

new Fiber_Admin_Setting();