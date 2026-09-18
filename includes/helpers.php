<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Helper functions
 */

if(!function_exists('fiad_get_option')){
	function fiad_get_option($key, $fiber_admin){
		if(is_array($fiber_admin)){
			if(array_key_exists($key, $fiber_admin)){
				return $fiber_admin[$key];
			}
		}
		
		return '';
	}
}

if(!function_exists('fiad_get_general_option')){
	function fiad_get_general_option($key){
		return fiad_get_option($key, get_option('fiber_admin'));
	}
}

if(!function_exists('fiad_get_miscellaneous_option')){
	function fiad_get_miscellaneous_option($key){
		return fiad_get_option($key, get_option('fiad_miscellaneous'));
	}
}

if(!function_exists('fiad_get_cpo_option')){
	function fiad_get_cpo_option($key){
		return fiad_get_option($key, get_option('fiad_cpo'));
	}
}

if(!function_exists('fiad_is_screen_sortable')){
	function fiad_is_screen_sortable(){
		if(is_admin()){
			$post_types = fiad_get_cpo_option('post_types');
			$taxonomies = fiad_get_cpo_option('taxonomies');
			
			if(!function_exists('get_current_screen')){
				require_once ABSPATH . '/wp-admin/includes/screen.php';
			}
			$screen = get_current_screen();
			
			if(($post_types || $taxonomies) && $screen){
				if($screen->taxonomy && $taxonomies && strpos($screen->base, 'edit') !== false){
					$screen_tax = $screen->taxonomy;
					if(in_array($screen_tax, $taxonomies)){
						return true;
					}
				}elseif($post_types && $screen->base == 'edit'){
					$screen_post_type = $screen->post_type;
					if(in_array($screen_post_type, $post_types)){
						return true;
					}
				}
			}
		}
		
		return false;
	}
}

if(!function_exists('fiad_get_duplicate_option')){
	function fiad_get_duplicate_option($key){
		return fiad_get_option($key, get_option('fiad_duplicate'));
	}
}

if(!function_exists('fiad_is_admin_user_role')){
	function fiad_is_admin_user_role(){
		if(is_user_logged_in()){
			if(current_user_can('edit_posts')){
				return true;
			}
		}
		
		return false;
	}
}

if(!function_exists('fiad_get_db_error_option')){
	function fiad_get_db_error_option($key){
		return fiad_get_option($key, get_option('fiad_db_error'));
	}
}

if(!function_exists('fiad_check_db_error_file')){
	function fiad_check_db_error_file(){
		return (file_exists(WP_CONTENT_DIR . '/db-error.php'));
	}
}

if(!function_exists('fiad_resolve_theme_preset')){
	/* Resolves a theme.json "var:preset|type|slug" reference to its actual value from the active theme's global settings. */
	function fiad_resolve_theme_preset($value, $settings){
		if(!$value || strpos($value, 'var:preset|') !== 0){
			return $value;
		}

		$parts = explode('|', $value);
		if(count($parts) !== 3){
			return '';
		}

		list(, $type, $slug) = $parts;
		$presets = [
			'color'       => $settings['color']['palette'] ?? [],
			'font-family' => $settings['typography']['fontFamilies'] ?? [],
		];
		$value_key = ($type === 'color') ? 'color' : 'fontFamily';

		foreach($presets[$type] ?? [] as $preset){
			if(($preset['slug'] ?? '') === $slug){
				return $preset[$value_key] ?? '';
			}
		}

		return '';
	}
}

if(!function_exists('fiad_get_google_font_name')){
	/* Extracts a loadable Google Fonts family name from a CSS font-family value, or '' if it's a generic/system stack. */
	function fiad_get_google_font_name($font_family_css){
		$first_font = trim(explode(',', $font_family_css)[0], " \t\n\r\0\x0B\"'");
		$generic    = ['inherit', 'serif', 'sans-serif', 'monospace', 'cursive', 'fantasy', 'system-ui', '-apple-system', 'ui-sans-serif', 'ui-serif', 'ui-monospace'];

		if(!$first_font || in_array(strtolower($first_font), $generic, true)){
			return '';
		}

		return $first_font;
	}
}

if(!function_exists('fiad_get_theme_style')){
	/* Pulls background/text/accent colors and body/heading fonts from the active theme's global styles (theme.json), so generated pages can inherit the site's own look instead of a fixed default. */
	function fiad_get_theme_style(){
		$theme_style = [];

		if(!function_exists('wp_get_global_settings') || !function_exists('wp_get_global_styles')){
			return $theme_style;
		}

		$settings = wp_get_global_settings();
		$styles   = wp_get_global_styles();

		$background = fiad_resolve_theme_preset($styles['color']['background'] ?? '', $settings);
		$text       = fiad_resolve_theme_preset($styles['color']['text'] ?? '', $settings);

		if($background){
			$theme_style['background'] = $background;
		}
		if($text){
			$theme_style['text'] = $text;
		}

		$palette = $settings['color']['palette'] ?? [];
		foreach(['primary', 'accent', 'contrast', 'tertiary'] as $slug){
			foreach($palette as $entry){
				if(($entry['slug'] ?? '') === $slug && !empty($entry['color'])){
					$theme_style['accent'] = $entry['color'];
					break 2;
				}
			}
		}
		if(empty($theme_style['accent']) && !empty($palette[0]['color'])){
			$theme_style['accent'] = $palette[0]['color'];
		}

		$body_font    = fiad_resolve_theme_preset($styles['typography']['fontFamily'] ?? '', $settings);
		$heading_font = fiad_resolve_theme_preset($styles['elements']['heading']['typography']['fontFamily'] ?? '', $settings);

		$google_fonts = [];

		if($body_font){
			$theme_style['body_font_css'] = $body_font;
			$name = fiad_get_google_font_name($body_font);
			if($name){
				$google_fonts[] = $name;
			}
		}

		if($heading_font){
			$theme_style['heading_font_css'] = $heading_font;
			$name = fiad_get_google_font_name($heading_font);
			if($name){
				$google_fonts[] = $name;
			}
		}

		$google_fonts = array_unique($google_fonts);
		if($google_fonts){
			$families = [];
			foreach($google_fonts as $font_name){
				$families[] = 'family=' . str_replace(' ', '+', $font_name) . ':wght@400;500;600;700';
			}
			$theme_style['font_import'] = 'https://fonts.googleapis.com/css2?' . implode('&', $families) . '&display=swap';
		}

		return $theme_style;
	}
}

if(!function_exists('fiad_hex_to_rgb')){
	/* Converts a 3- or 6-digit hex color to an [r, g, b] array, or null if the value isn't a hex color. */
	function fiad_hex_to_rgb($color){
		if(!preg_match('/^#?([0-9a-f]{3}|[0-9a-f]{6})$/i', trim($color), $matches)){
			return null;
		}

		$hex = $matches[1];
		if(strlen($hex) === 3){
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}

		return array_map('hexdec', str_split($hex, 2));
	}
}

if(!function_exists('fiad_mix_colors')){
	/* Blends $color toward $mix_with by $weight (0-1). Returns $color unchanged if either value isn't a hex color. */
	function fiad_mix_colors($color, $mix_with, $weight){
		$rgb_a = fiad_hex_to_rgb($color);
		$rgb_b = fiad_hex_to_rgb($mix_with);
		if(!$rgb_a || !$rgb_b){
			return $color;
		}

		$mixed = [];
		foreach($rgb_a as $i => $channel){
			$mixed[] = (int) round($channel + ($rgb_b[$i] - $channel) * $weight);
		}

		return sprintf('#%02x%02x%02x', $mixed[0], $mixed[1], $mixed[2]);
	}
}

if(!function_exists('fiad_is_dark_color')){
	/* Estimates whether a hex color is dark, to decide if a surface on top of it needs light or dark text. */
	function fiad_is_dark_color($color){
		$rgb = fiad_hex_to_rgb($color);
		if(!$rgb){
			return false;
		}

		$luminance = (0.299 * $rgb[0] + 0.587 * $rgb[1] + 0.114 * $rgb[2]) / 255;

		return $luminance < 0.5;
	}
}

if(!function_exists('fiad_array_key_exists')){
	function fiad_array_key_exists($key, $array, $default = ''){
		if($array && is_array($array)){
			if(array_key_exists($key, $array)){
				return $array[$key] ? : $default;
			}
		}
		
		return $default;
	}
}

if(!function_exists('fiad_dequeue_assets')){
	function fiad_dequeue_assets(){
		global $wp_scripts, $wp_styles;
		
		$exclude_assets = ['jquery-core', 'admin-bar'];
		foreach($wp_scripts->registered as $registered){
			$handle = $registered->handle;
			if(!in_array($handle, $exclude_assets)){
				wp_deregister_script($handle);
			}
		}
		
		foreach($wp_styles->registered as $registered){
			$handle = $registered->handle;
			if(!in_array($handle, $exclude_assets)){
				wp_deregister_style($handle);
			}
		}
	}
}

if(!function_exists('fiad_code_editor')){
	function fiad_code_editor($type, $id){
		// Enqueue code editor and settings for manipulating HTML.
		$settings = wp_enqueue_code_editor(['type' => $type]);
		
		// Return if the editor was not enqueued.
		if(false === $settings){
			return;
		}
		
		wp_add_inline_script(
			'code-editor',
			sprintf(
				'jQuery( function() { wp.codeEditor.initialize( "' . $id . '", %s ); } );',
				wp_json_encode($settings)
			)
		);
	}
}

if(!function_exists('fiad_file_get_content')){
	function fiad_file_get_content($file_url){
		$curl_session = curl_init($file_url);
		curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
		
		return curl_exec($curl_session);
	}
}

if(!function_exists('fiad_image_upload_field')){
	/* Renders a core-style image upload field (thumbnail + set/remove links, backed by wp.media()), optionally enforcing a minimum image size. */
	function fiad_image_upload_field($name, $value, $set_label, $remove_label, $description = '', $min_width = 0, $min_height = 0){
		?>
        <fieldset class="fiber-admin-input__img" data-min-width="<?php echo esc_attr($min_width); ?>" data-min-height="<?php echo esc_attr($min_height); ?>">
            <input type="hidden" class="fiber-admin-image-value" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_url($value); ?>"/>
            <div class="fiber-admin-image-wrap hide-if-no-js">
                <a href="#" class="fiber-admin-image-thumbnail <?php echo $value ? '' : 'fiber-admin-image-thumbnail--empty'; ?>" data-empty-label="<?php echo esc_attr($set_label); ?>">
                    <?php if($value): ?>
                        <img src="<?php echo esc_url($value); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>"/>
                    <?php else: ?>
                        <?php echo esc_html($set_label); ?>
                    <?php endif; ?>
                </a>
                <button type="button" class="fiber-admin-remove-image" <?php echo $value ? '' : 'style="display:none;"'; ?> title="<?php echo esc_attr($remove_label); ?>">
                    <span class="dashicons dashicons-no-alt"></span>
                    <span class="screen-reader-text"><?php echo esc_html($remove_label); ?></span>
                </button>
            </div>
			<?php if($description): ?>
                <p class="hide-if-no-js fiber-admin-field-note"><?php echo esc_html($description); ?></p>
			<?php endif; ?>
        </fieldset>
		<?php
	}
}

if(!function_exists('fiad_logo_size_field')){
	/* Renders a core-style width/height field pair, matching WP core's Settings > Media Thumbnail size field. */
	function fiad_logo_size_field($name_prefix, $width_key, $height_key, $width_value, $height_value, $legend_text){
		$width_name  = $name_prefix . '[' . $width_key . ']';
		$height_name = $name_prefix . '[' . $height_key . ']';
		?>
        <fieldset class="fiber-admin-input__size">
            <legend class="screen-reader-text"><span><?php echo esc_html($legend_text); ?></span></legend>
            <label for="<?php echo esc_attr($width_key); ?>"><?php _e('Width', 'fiber-admin'); ?></label>
            <input name="<?php echo esc_attr($width_name); ?>" type="number" step="1" min="0"
                   id="<?php echo esc_attr($width_key); ?>" class="small-text"
                   value="<?php echo esc_attr($width_value); ?>"/> px
            <span aria-hidden="true">x</span>
            <label for="<?php echo esc_attr($height_key); ?>"><?php _e('Height', 'fiber-admin'); ?></label>
            <input name="<?php echo esc_attr($height_name); ?>" type="number" step="1" min="0"
                   id="<?php echo esc_attr($height_key); ?>" class="small-text"
                   value="<?php echo esc_attr($height_value); ?>"/> px
        </fieldset>
		<?php
	}
}

if(!function_exists('fiad_get_file_upload_path')){
	function fiad_get_file_upload_path($url){
		return explode('wp-content', $url)[1];
	}
}

/**
 * Detect popular caching plugins and clear their cache programmatically.
 */
if(!function_exists('fiad_clear_cache')){
	function fiad_clear_cache(){
		// WP Rocket
		if(function_exists('rocket_clean_domain')){
			rocket_clean_domain();
		}

		// W3 Total Cache
		if(function_exists('w3tc_flush_all')){
			w3tc_flush_all();
		}

		// WP Super Cache
		if(function_exists('wp_cache_clear_cache')){
			wp_cache_clear_cache();
		}

		// WP Fastest Cache
		if(class_exists('WpFastestCache')){
			$wp_fastest_cache = new WpFastestCache();
			$wp_fastest_cache->deleteCache(true);
		}

		// LiteSpeed Cache
		if(has_action('litespeed_purge_all')){
			do_action('litespeed_purge_all');
		}
	}
}