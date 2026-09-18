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

if(!function_exists('fiad_fetch_url_body')){
	/* Fetches a URL's body via a short-timeout GET request, returning '' on any failure. */
	function fiad_fetch_url_body($url, $timeout = 5){
		$response = wp_remote_get($url, ['timeout' => $timeout, 'sslverify' => false]);
		if(is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200){
			return '';
		}

		return wp_remote_retrieve_body($response);
	}
}

if(!function_exists('fiad_resolve_relative_url')){
	/* Resolves a possibly-relative URL, as found inside a CSS file, against that file's own URL. */
	function fiad_resolve_relative_url($url, $base_url){
		$url = trim($url, " \t\n\r\0\x0B\"'");

		if(!$url || strpos($url, 'data:') === 0 || preg_match('#^https?://#i', $url)){
			return $url;
		}
		if(strpos($url, '//') === 0){
			return (is_ssl() ? 'https:' : 'http:') . $url;
		}

		$base = wp_parse_url($base_url);
		$host = fiad_array_key_exists('host', $base);
		if(!$host){
			return $url;
		}

		$port   = fiad_array_key_exists('port', $base);
		$origin = fiad_array_key_exists('scheme', $base) . '://' . $host . ($port ? ':' . $port : '');

		if(strpos($url, '/') === 0){
			return $origin . $url;
		}

		$path     = fiad_array_key_exists('path', $base, '/');
		$segments = explode('/', trim(dirname($path), '/'));
		foreach(explode('/', $url) as $part){
			if($part === '.' || $part === ''){
				continue;
			}
			if($part === '..'){
				array_pop($segments);
			}else{
				$segments[] = $part;
			}
		}

		return $origin . '/' . implode('/', $segments);
	}
}

if(!function_exists('fiad_extract_font_faces')){
	/* Pulls @font-face blocks out of a stylesheet's CSS text, rewriting any relative url() paths to absolute ones. */
	function fiad_extract_font_faces($css, $stylesheet_url){
		if(!preg_match_all('/@font-face\s*{[^}]*}/is', $css, $matches)){
			return '';
		}

		$blocks = [];
		foreach($matches[0] as $block){
			$blocks[] = preg_replace_callback('/url\(([^)]+)\)/i', function($match) use ($stylesheet_url){
				return 'url(' . fiad_resolve_relative_url($match[1], $stylesheet_url) . ')';
			}, $block);
		}

		return implode('', $blocks);
	}
}

if(!function_exists('fiad_extract_root_variables')){
	/* Parses every :root{...} custom property declaration out of a stylesheet's CSS text into a flat [name => value] map. */
	function fiad_extract_root_variables($css){
		$props = [];

		if(preg_match_all('/:root\s*{([^}]*)}/is', $css, $blocks)){
			foreach($blocks[1] as $block){
				if(preg_match_all('/(--[a-zA-Z0-9-_]+)\s*:\s*([^;]+);/', $block, $matches, PREG_SET_ORDER)){
					foreach($matches as $match){
						$props[$match[1]] = trim($match[2]);
					}
				}
			}
		}

		return $props;
	}
}

if(!function_exists('fiad_resolve_css_var')){
	/* Follows a chain of var(--name, fallback) references against a custom-property map down to a literal value. */
	function fiad_resolve_css_var($value, $props, $depth = 0){
		if($depth > 5){
			return $value;
		}

		if(preg_match('/^var\(\s*(--[a-zA-Z0-9-_]+)\s*(?:,\s*(.+))?\)$/i', trim($value), $matches)){
			$referenced = fiad_array_key_exists($matches[1], $props);
			if($referenced){
				return fiad_resolve_css_var($referenced, $props, $depth + 1);
			}

			$fallback = fiad_array_key_exists(2, $matches);

			return $fallback ? trim($fallback) : $value;
		}

		return $value;
	}
}

if(!function_exists('fiad_find_css_var_by_hint')){
	/* Finds the first custom property whose name contains one of the given hints (checked in order), resolved to a literal value. */
	function fiad_find_css_var_by_hint($props, $hints){
		foreach($hints as $hint){
			foreach($props as $name => $value){
				if(stripos($name, $hint) !== false){
					return fiad_resolve_css_var($value, $props);
				}
			}
		}

		return '';
	}
}

if(!function_exists('fiad_get_theme_style')){
	/* Scrapes the site's own homepage for its real colors, fonts, and font files (inline <style> blocks + linked stylesheets), so a generated page can match the active theme's actual look instead of guessing at it. Returns an empty array (caller applies its own defaults) if the theme's styling can't be determined, e.g. loopback requests are blocked or nothing usable was found. */
	function fiad_get_theme_style(){
		$theme_style = [];

		$home_html = fiad_fetch_url_body(home_url('/'));
		if(!$home_html || !class_exists('DOMDocument')){
			return $theme_style;
		}

		libxml_use_internal_errors(true);
		$dom = new DOMDocument();
		$dom->loadHTML($home_html);
		libxml_clear_errors();

		$stylesheet_urls = [];
		foreach($dom->getElementsByTagName('link') as $link){
			if(strtolower($link->getAttribute('rel')) !== 'stylesheet'){
				continue;
			}

			$href = $link->getAttribute('href');
			if($href){
				$stylesheet_urls[] = fiad_resolve_relative_url($href, home_url('/'));
			}

			if(count($stylesheet_urls) >= 12){
				break;
			}
		}

		$css_chunks = [];
		foreach($dom->getElementsByTagName('style') as $style_tag){
			$css_chunks[] = ['css' => $style_tag->textContent, 'url' => home_url('/')];
		}

		$font_provider_hosts = ['fonts.googleapis.com', 'fonts.bunny.net', 'use.typekit.net'];
		$font_links          = [];

		foreach($stylesheet_urls as $stylesheet_url){
			if(in_array(wp_parse_url($stylesheet_url, PHP_URL_HOST), $font_provider_hosts, true)){
				// the theme already loads this font from its provider directly: reuse that link as-is instead of guessing a font name ourselves
				$font_links[] = $stylesheet_url;
				continue;
			}

			$css = fiad_fetch_url_body($stylesheet_url, 4);
			if($css){
				$css_chunks[] = ['css' => $css, 'url' => $stylesheet_url];
			}
		}

		$props     = [];
		$font_face = '';
		foreach($css_chunks as $chunk){
			$props     = array_merge($props, fiad_extract_root_variables($chunk['css']));
			$font_face .= fiad_extract_font_faces($chunk['css'], $chunk['url']);
		}

		if(!$props && !$font_face && !$font_links){
			return $theme_style;
		}

		$background = fiad_find_css_var_by_hint($props, ['background', 'bg']);
		$text       = fiad_find_css_var_by_hint($props, ['text']);
		$accent     = fiad_find_css_var_by_hint($props, ['primary', 'accent', 'brand']);
		$heading    = fiad_find_css_var_by_hint($props, ['heading']);
		$body_font  = fiad_find_css_var_by_hint($props, ['font-primary', 'font-body', 'primary-font', 'body-font', 'font-base']);

		if($background && fiad_hex_to_rgb($background)){
			$theme_style['background'] = $background;
		}
		if($text && fiad_hex_to_rgb($text)){
			$theme_style['text'] = $text;
		}
		if($accent && fiad_hex_to_rgb($accent)){
			$theme_style['accent'] = $accent;
		}elseif($heading && fiad_hex_to_rgb($heading)){
			$theme_style['accent'] = $heading;
		}
		if($body_font){
			$theme_style['body_font_css']    = $body_font;
			$theme_style['heading_font_css'] = $body_font;
		}
		if($font_face){
			$theme_style['font_face_css'] = $font_face;
		}
		if($font_links){
			$theme_style['font_links'] = array_unique($font_links);
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