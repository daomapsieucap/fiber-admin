<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * White Label Page
 */
class Fiber_Admin_White_Label_Settings{
	
	private $fiber_admin;
	
	public function __construct(){
		$this->fiber_admin = get_option('fiber_admin');
		
		add_action('admin_menu', array($this, 'fiber_white_label_admin'));
		add_action('admin_init', array($this, 'fiber_admin_page_init'));
		
		// Register scripts
		add_action("admin_enqueue_scripts", array($this, 'fiber_enqueue_scripts'));
	}
	
	public function fiber_enqueue_scripts(){
		// Upload field
		wp_enqueue_media();
		
		// Colorpicker field
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script('wp-color-picker');
		
		// Plugin scripts
		wp_enqueue_script('fiber-admin', FIBERADMIN_ASSETS_URL . 'js/admin.js', array('jquery'), FIBERADMIN_VERSION);
	}
	
	public function fiber_white_label_admin(){
		add_submenu_page(
			'fiber-admin',
			'Fiber Admin White Label',
			'White Label',
			'manage_options',
			'fiber-admin',
			array($this, 'fiber_admin_white_label_admin_page')
		);
	}
	
	public function fiber_admin_white_label_admin_page(){
		?>
        <div class="wrap">
            <h2>Fiber Admin White Label</h2>
			<?php settings_errors(); ?>

            <form class="fiber-admin" method="post" action="options.php">
				<?php
				settings_fields('fiber_admin_white_label_group');
				do_settings_sections('fiber-admin');
				
				submit_button();
				?>
            </form>
        </div>
		<?php
	}
	
	public function fiber_admin_page_init(){
		register_setting(
			'fiber_admin_white_label_group',
			'fiber_admin',
			array($this, 'sanitize_text_field')
		);
		
		add_settings_section(
			'fiber_admin_branding_section',
			'<span class="dashicons dashicons-wordpress"></span> Branding',
			array($this, 'fiber_admin_section_info'),
			'fiber-admin'
		);
		
		add_settings_field(
			'hide_wordpress_branding', // id
			'Hide WordPress Branding', // title
			array($this, 'fiber_hide_wordpress_branding'), // callback
			'fiber-admin', // page
			'fiber_admin_branding_section' // section
		);
		
		add_settings_section(
			'fiber_admin_white_label_section',
			'<span class="dashicons dashicons-admin-network"></span> Login',
			array($this, 'fiber_admin_section_info'),
			'fiber-admin'
		);
		
		add_settings_field(
			'login_logo',
			'Logo',
			array($this, 'fiber_login_logo'),
			'fiber-admin',
			'fiber_admin_white_label_section'
		);
		
		add_settings_field(
			'login_logo_size',
			'Logo size',
			array($this, 'fiber_login_logo_size'),
			'fiber-admin',
			'fiber_admin_white_label_section'
		);
		
		add_settings_field(
			'login_bg_color',
			'Background Color / Image',
			array($this, 'fiber_login_bg'),
			'fiber-admin',
			'fiber_admin_white_label_section'
		);
		
		add_settings_field(
			'form_color',
			'Form',
			array($this, 'fiber_form'),
			'fiber-admin',
			'fiber_admin_white_label_section'
		);
		
		add_settings_field(
			'login_extra_css',
			'Extra CSS',
			array($this, 'fiber_login_extra_css'),
			'fiber-admin',
			'fiber_admin_white_label_section'
		);
	}
	
	public function fiber_admin_section_info(){
	}
	
	public function fiber_hide_wordpress_branding(){
		?>
        <fieldset>
            <label for="hide_wordpress_branding" class="fiber-admin-toggle">
                <input type="checkbox" name="fiber_admin[hide_wordpress_branding]" id="hide_wordpress_branding"
                       value="yes" <?php checked(esc_attr($this->fiber_admin['hide_wordpress_branding']), 'yes'); ?> />
                <span class="slider round"></span>
            </label>
        </fieldset>
		<?php
	}
	
	public function fiber_login_logo(){
		$logo = esc_attr($this->fiber_admin['login_logo']);
		?>
        <fieldset class="fiber-admin-input__img">
            <div class="fiber-preview">
                <img src="<?php echo $logo; ?>" alt="<?php echo get_bloginfo('name'); ?>"/>
            </div>
            <label>
                <input class="regular-text" type="text" name="fiber_admin[login_logo]"
                       placeholder="<?php echo __('Input or choose your logo URL', 'fiber-admin'); ?>"
                       value="<?php echo $logo; ?>"/>
            </label>
            <button class="button fiberadmin-upload"><?php echo __('Insert / Replace Image', 'fiberadmin'); ?></button>
        </fieldset>
		<?php
	}
	
	public function fiber_login_logo_size(){
		$width  = esc_attr($this->fiber_admin['login_logo_width']);
		$height = esc_attr($this->fiber_admin['login_logo_height']);
		?>
        <fieldset class="fiber-admin-input__multiples">
            <label class="fiber-admin-input__label"
                   for="login_logo_width"><?php echo __('Width', 'fiber-admin'); ?></label>
            <input class="small-text" type="number" name="fiber_admin[login_logo_width]" id="login_logo_width"
                   value="<?php echo $width; ?>"/> px
            <br/>
            <label class="fiber-admin-input__label"
                   for="login_logo_height"><?php echo __('Height', 'fiber-admin'); ?></label>
            <input class="small-text" type="number" name="fiber_admin[login_logo_height]" id="login_logo_height"
                   value="<?php echo $height; ?>"/> px
        </fieldset>
		<?php
	}
	
	public function fiber_login_bg(){
		$bg_color = $this->fiber_admin['login_bg_color'];
		$bg_img   = $this->fiber_admin['login_bg_img'];
		?>
        <fieldset>
            <label>
                <input class="fiber-color-field" name="fiber_admin[login_bg_color]" type="text"
                       value="<?php echo $bg_color; ?>"/>
            </label>
        </fieldset>
        <fieldset class="fiber-admin-input__img">
            <div class="fiber-preview">
                <img src="<?php echo $bg_img; ?>" alt="<?php echo get_bloginfo('name'); ?>"/>
            </div>
            <label>
                <input class="regular-text" type="text" name="fiber_admin[login_bg_img]"
                       placeholder="<?php echo __('Input or choose your background URL', 'fiber-admin'); ?>"
                       value="<?php echo $bg_img; ?>"/>
            </label>
            <button class="button fiberadmin-upload"><?php echo __('Insert / Replace Image', 'fiber-admin'); ?></button>
            <p class="description"><?php echo __('The minimum sizes should be 2000px width and 1000px height', 'fiber-admin'); ?></p>
        </fieldset>
		<?php
	}
	
	public function fiber_form(){
		$bg_color       = esc_attr($this->fiber_admin['form_bg_color']);
		$button_color   = esc_attr($this->fiber_admin['form_button_color']);
		$btn_text_color = esc_attr($this->fiber_admin['form_btn_text_color']);
		$link_color     = esc_attr($this->fiber_admin['form_link_color']);
		$disable_border = esc_attr($this->fiber_admin['form_disable_border']) ? 'checked' : '';
		?>
        <fieldset class="fiber-admin-input__multiples fiber-color-group">
            <label class="fiber-admin-input__label"
                   for="form_bg_color"><?php echo __('Background Color', 'fiber-admin'); ?></label>
            <input id="form_bg_color" class="fiber-color-field" name="fiber_admin[form_bg_color]" type="text"
                   value="<?php echo $bg_color; ?>"/>
            <br/>
            <label class="fiber-admin-input__label"
                   for="form_button_color"><?php echo __('Button Color', 'fiber-admin'); ?></label>
            <input id="form_button_color" class="fiber-color-field" name="fiber_admin[form_button_color]" type="text"
                   value="<?php echo $button_color; ?>"/>
            <br/>
            <label class="fiber-admin-input__label"
                   for="form_btn_text_color"><?php echo __('Button Text Color', 'fiber-admin'); ?></label>
            <input id="form_btn_text_color" class="fiber-color-field" name="fiber_admin[form_btn_text_color]"
                   type="text"
                   value="<?php echo $btn_text_color; ?>"/>
            <br/>
            <label class="fiber-admin-input__label"
                   for="form_link_color"><?php echo __('Link Color', 'fiber-admin'); ?></label>
            <input id="form_link_color" class="fiber-color-field" name="fiber_admin[form_link_color]" type="text"
                   value="<?php echo $link_color; ?>"/>
            <br/>
            <div class="fiber-admin-input__label"><?php echo __('Disable Form Border', 'fiber-admin'); ?></div>
            <label for="form_disable_border" class="fiber-admin-toggle">
                <input type="checkbox" name="fiber_admin[form_disable_border]" id="form_disable_border"
                       value="yes" <?php echo $disable_border; ?> />
                <span class="slider round"></span>
            </label>
        </fieldset>
		<?php
	}
	
	public function fiber_login_extra_css(){
		$login_extra_css = esc_textarea($this->fiber_admin['login_extra_css']);
		?>
        <fieldset>
            <textarea name="fiber_admin[login_extra_css]"><?php echo $login_extra_css; ?></textarea>
        </fieldset>
		<?php
	}
}

new Fiber_Admin_White_Label_Settings();