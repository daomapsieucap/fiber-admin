<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * DB Error Page
 */
class Fiber_Admin_DB_Error_Settings{
	
	public function __construct(){
		add_action('admin_menu', array($this, 'fiad_db_error'));
		add_action('admin_init', array($this, 'fiad_db_error_page_init'));
		
		// Register scripts
		add_action("admin_enqueue_scripts", array($this, 'fiad_enqueue_scripts'));
	}
	
	public function fiad_enqueue_scripts(){
		// Upload field
		wp_enqueue_media();
		
		// Color picker field
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script('wp-color-picker');
		
		// Plugin scripts
		wp_enqueue_script('fiad_db_error', FIBERADMIN_ASSETS_URL . 'js/fiber-admin.js', array('jquery'), FIBERADMIN_VERSION);
	}
	
	public function fiad_db_error(){
		add_submenu_page(
			'fiber-admin',
			'Database Error Page',
			'Database Error Page',
			'manage_options',
			'fiber-admin-db-error',
			array($this, 'fiad_db_error_page')
		);
	}
	
	public function fiad_db_error_page(){
		?>
        <div class="wrap">
            <h2>Database Error Page</h2>
			<?php settings_errors(); ?>

            <form class="fiber-admin" method="post" action="options.php">
				<?php
				settings_fields('fiad_db_error_group');
				do_settings_sections('fiad-db-error');
				
				submit_button();
				?>
            </form>
        </div>
		<?php
	}
	
	public function fiad_db_error_page_init(){
		register_setting(
			'fiad_db_error_group',
			'fiad_db_error',
			array($this, 'sanitize_text_field')
		);
		
		add_settings_section(
			'fiad_db_error_section',
			'<span class="dashicons dashicons-admin-generic"></span> General',
			array($this, 'fiad_section_info'),
			'fiad-db-error'
		);
		
		add_settings_field(
			'db_error_enable', // id
			'Activate', // title
			array($this, 'fiad_db_error_enable'), // callback
			'fiad-db-error', // page
			'fiad_db_error_section' // section
		);
		
		add_settings_field(
			'login_logo',
			'Logo',
			array($this, 'fiad_login_logo'),
			'fiad-db-error',
			'fiad_db_error_section'
		);
		
		add_settings_field(
			'login_logo_size',
			'Logo size',
			array($this, 'fiad_login_logo_size'),
			'fiad-db-error',
			'fiad_db_error_section'
		);
		
		add_settings_field(
			'login_bg_color',
			'Background Color / Image',
			array($this, 'fiad_login_bg'),
			'fiad-db-error',
			'fiad_db_error_section'
		);
		
		add_settings_field(
			'db_error_message',
			'Error Message',
			array($this, 'fiad_db_error_message'),
			'fiad-db-error',
			'fiad_db_error_section'
		);
		
		add_settings_field(
			'login_extra_css',
			'Extra CSS',
			array($this, 'fiad_login_extra_css'),
			'fiad-db-error',
			'fiad_db_error_section'
		);
	}
	
	public function fiad_section_info(){
	}
	
	public function fiad_db_error_enable(){
		?>
        <fieldset>
            <label for="hide_wordpress_branding" class="fiber-admin-toggle">
                <input type="checkbox" name="fiad_db_error[hide_wordpress_branding]" id="hide_wordpress_branding"
                       value="yes" <?php checked(esc_attr(fiad_get_db_error_option('hide_wordpress_branding')), 'yes'); ?> />
                <span class="slider round"></span>
            </label>
        </fieldset>
		<?php
	}
	
	public function fiad_login_logo(){
		$logo = fiad_get_db_error_option('login_logo');
		?>
        <fieldset class="fiber-admin-input__img">
            <div class="fiber-admin-preview">
                <img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>"/>
            </div>
            <label>
                <input class="regular-text" type="text" name="fiad_db_error[login_logo]"
                       placeholder="<?php echo __('Input / Choose your logo image', 'fiber-admin'); ?>"
                       value="<?php echo esc_url($logo); ?>"/>
            </label>
            <button class="button fiber-admin-upload"><?php echo __('Insert / Replace Image', 'fiber-admin'); ?></button>
        </fieldset>
		<?php
	}
	
	public function fiad_login_logo_size(){
		?>
        <fieldset class="fiber-admin-input__multiples">
            <label class="fiber-admin-input__label"
                   for="login_logo_width"><?php echo __('Width', 'fiber-admin'); ?></label>
            <input class="small-text" type="number" name="fiad_db_error[login_logo_width]" id="login_logo_width"
                   value="<?php echo esc_attr(fiad_get_db_error_option('login_logo_width')); ?>"/> px
            <br/>
            <label class="fiber-admin-input__label"
                   for="login_logo_height"><?php echo __('Height', 'fiber-admin'); ?></label>
            <input class="small-text" type="number" name="fiad_db_error[login_logo_height]" id="login_logo_height"
                   value="<?php echo esc_attr(fiad_get_db_error_option('login_logo_height')); ?>"/> px
        </fieldset>
		<?php
	}
	
	public function fiad_login_bg(){
		$bg_img = fiad_get_db_error_option('login_bg_img');
		?>
        <fieldset>
            <label>
                <input class="fiber-color-field" name="fiad_db_error[login_bg_color]" type="text"
                       value="<?php echo esc_attr(fiad_get_db_error_option('login_bg_color')); ?>"/>
            </label>
        </fieldset>
        <fieldset class="fiber-admin-input__img">
            <div class="fiber-admin-preview">
                <img src="<?php echo esc_url($bg_img); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>"/>
            </div>
            <label>
                <input class="regular-text" type="text" name="fiad_db_error[login_bg_img]"
                       placeholder="<?php echo __('Input / Choose your background image', 'fiber-admin'); ?>"
                       value="<?php echo esc_url($bg_img); ?>"/>
            </label>
            <button class="button fiber-admin-upload"><?php echo __('Insert / Replace Image', 'fiber-admin'); ?></button>
            <p class="description"><?php echo __('The minimum sizes should be 2000px width and 1000px height', 'fiber-admin'); ?></p>
        </fieldset>
		<?php
	}
	
	public function fiad_db_error_message(){
		?>
        <fieldset class="fiber-admin-editor">
			<?php
			$db_error_message      = fiad_get_db_error_option('db_error_message');
			$default_error_message = "<h4 style='text-align: center;'>503 Service Temporarily Unavailable</h4><p style='text-align: center;'>We're currently experiencing technical issues connecting to the database. Please check back soon.</p>";
			$db_error_message      = !empty($db_error_message) ? $db_error_message : $default_error_message;
			wp_editor($db_error_message, 'db_error_message', array(
				'textarea_name' => 'fiad_db_error[db_error_message]',
				'media_buttons' => false,
				'textarea_rows' => 5
			));
			?>
        </fieldset>
		<?php
	}
	
	public function fiad_login_extra_css(){
		?>
        <fieldset>
            <textarea
                    name="fiad_db_error[login_extra_css]"><?php echo esc_html(fiad_get_db_error_option('login_extra_css')); ?></textarea>
        </fieldset>
		<?php
	}
}

new Fiber_Admin_DB_Error_Settings();