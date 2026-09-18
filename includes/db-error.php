<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * DB Error
 */
class Fiber_Admin_DB_Error{
	public function __construct(){
		add_action('admin_init', [$this, 'fiad_db_error_file']);
		register_activation_hook(FIBERADMIN_FILENAME, [$this, 'fiad_db_error_file']);
		register_deactivation_hook(FIBERADMIN_FILENAME, [$this, 'fiad_remove_db_error_file']);
	}
	
	public function fiad_db_error_file(){
		$db_error_added  = fiad_get_db_error_option('db_error_added');
		$enable          = fiad_get_db_error_option('db_error_enable');
		$db_error_option = get_option('fiad_db_error');
		if($enable && !$db_error_added){
			// add rules to .htaccess file in wp-content if it exists
			$content_htaccess = WP_CONTENT_DIR . '/.htaccess';
			if(file_exists($content_htaccess)){
				$lines   = [];
				$lines[] = '<Files db-error.php>';
				$lines[] = '    Allow From All';
				$lines[] = '    Satisfy any';
				$lines[] = '</Files>';
				
				insert_with_markers($content_htaccess, 'FIBER ADMIN DB ERROR PAGE', $lines);
			}
			
			// generate DB Error content based on settings
			$db_error_message = fiad_get_db_error_option('db_error_message');
			$title            = fiad_get_db_error_option('db_error_title');
			$logo             = fiad_get_db_error_option('db_error_logo');
			$logo_width       = fiad_get_db_error_option('db_error_logo_width');
			$logo_height      = fiad_get_db_error_option('db_error_logo_height');
			$bg_color         = fiad_get_db_error_option('db_error_bg');
			$style            = $bg_color ? 'body.db-error {background-color: ' . $bg_color . '}' : '';
			$server           = $_SERVER;
			$http             = fiad_array_key_exists('HTTPS', $server) ? "https://" : "http://";
			$http_host        = $http . fiad_array_key_exists('HTTP_HOST', $server);

			// inherit colors and fonts from the active theme's own front-end output, falling back to a neutral default
			$theme_style      = fiad_get_theme_style();
			$page_bg          = fiad_array_key_exists('background', $theme_style, '#f6f3ee');
			$page_text        = fiad_array_key_exists('text', $theme_style, '#1c1a17');
			$accent           = fiad_array_key_exists('accent', $theme_style, '#c05621');
			$body_font        = fiad_array_key_exists('body_font_css', $theme_style, '');
			$heading_font     = fiad_array_key_exists('heading_font_css', $theme_style, '');
			$font_face_css    = fiad_array_key_exists('font_face_css', $theme_style, '');
			$font_links       = fiad_array_key_exists('font_links', $theme_style, []);

			if(!$body_font && !$heading_font){
				// nothing usable found on the theme's own pages: fall back to our own default pairing + its Google Fonts import
				$body_font    = "'IBM Plex Sans', -apple-system, sans-serif";
				$heading_font = "'Fraunces', Georgia, serif";
				$font_links[] = 'https://fonts.googleapis.com/css2?family=Fraunces:wght@600&family=IBM+Plex+Sans:wght@400;500&display=swap';
			}else{
				// a font was found for one slot only: keep the other on safe system fonts rather than guessing an external font to load
				$body_font    = $body_font ? : "-apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif";
				$heading_font = $heading_font ? : 'Georgia, serif';
			}

			$card_bg          = fiad_is_dark_color($page_bg) ? fiad_mix_colors($page_bg, '#ffffff', 0.08) : '#ffffff';
			$muted_text       = fiad_mix_colors($page_text, $card_bg, 0.45);

			$font_link_tags = '';
			foreach($font_links as $font_link){
				$font_link_tags .= '<link rel="stylesheet" href="' . esc_url($font_link) . '"/>';
			}
			
			$php = '<?php';
			$php .= PHP_EOL;
			$php .= 'header(\'HTTP/1.1 503 Service Temporarily Unavailable\');';
			$php .= PHP_EOL;
			$php .= 'header(\'Status: 503 Service Temporarily Unavailable\');';
			$php .= PHP_EOL;
			$php .= 'header(\'Retry-After: 3600\');';
			$php .= PHP_EOL;
			$php .= '$absolute_url = "' . $http_host . '" . explode($_SERVER[\'DOCUMENT_ROOT\'], __DIR__)[1];';
			$php .= PHP_EOL;
			$php .= '?>';
			
			$html = $php;
			$html .= '<!DOCTYPE HTML>';
			$html .= '<html ' . get_language_attributes() . '>';
			$html .= '<head>';
			$html .= '<meta charset="' . get_bloginfo('charset') . '"/>';
			$html .= '<meta name="viewport" content="width=device-width, initial-scale=1"/>';
			$html .= '<title>' . $title . '</title>';
			$html .= $font_link_tags;
			$html .= "<style>
					" . $font_face_css . "
			        * {box-sizing:border-box}
			        body.db-error {margin:0;padding:0;font-family:" . $body_font . ";background:" . $page_bg . ";color:" . $page_text . "}
			        " . $style . "
			        .db-error__container {min-height:100vh;display:flex;align-items:center;justify-content:center;padding:32px 20px}
			        .db-error__card {width:100%;max-width:480px;background:" . $card_bg . ";border-top:3px solid " . $accent . ";border-radius:12px;padding:48px 40px;box-shadow:0 20px 40px -20px rgba(0,0,0,.2);text-align:center}
			        .db-error__logo {margin:0 auto 24px;max-width:200px}
			        .db-error__logo img {max-width:100%;height:auto;display:block;margin:0 auto}
			        .db-error__content h1, .db-error__content h2, .db-error__content h3, .db-error__content h4, .db-error__content h5, .db-error__content h6 {font-family:" . $heading_font . ";font-weight:600;font-size:26px;line-height:1.3;margin:0 0 12px;color:" . $page_text . "}
			        .db-error__content p {font-size:15px;line-height:1.6;color:" . $muted_text . ";font-weight:400;margin:0}
			        .db-error__content a {color:" . $accent . "}
			        @media only screen and (max-width:480px) {
			            .db-error__card {padding:36px 24px}
			            .db-error__content h1, .db-error__content h2, .db-error__content h3, .db-error__content h4, .db-error__content h5, .db-error__content h6 {font-size:22px}
			        }
					</style>";
			$html .= '<link rel="icon" type="image/png" href="<?= $absolute_url; ?>' . fiad_get_file_upload_path(get_site_icon_url()) . '"/>';
			$html .= '</head>';
			$html .= '<body class="db-error">';

			$html .= '<div class="db-error__container">';
			$html .= '<div class="db-error__card">';

			if($logo){
				$html .= '<div class="db-error__logo">';
				$html .= '<img src="<?= $absolute_url; ?>' . fiad_get_file_upload_path($logo) . '"  alt="' . get_bloginfo('name') . '" width="' . $logo_width . '" height="' . $logo_height . '"/>';
				$html .= '</div>';
			}

			$html .= '<div class="db-error__content">';
			$html .= stripslashes($db_error_message);
			$html .= '</div>';

			$html .= '</div>'; // db-error__card
			$html .= '</div>'; // db-error__container

			$html .= '</body>'; // db-error
			$html .= '</html>';
			
			file_put_contents(WP_CONTENT_DIR . '/db-error.php', $html);
			
			$db_error_option['db_error_added'] = true;
		}else{
			if(fiad_check_db_error_file() && !$enable){
				wp_delete_file(WP_CONTENT_DIR . '/db-error.php');
				$db_error_option['db_error_added'] = false;
			}
		}
		
		update_option('fiad_db_error', $db_error_option);
	}
	
	public function fiad_remove_db_error_file(){
		// Delete db-error.php on deactivate
		if(fiad_check_db_error_file()){
			$db_error_option = get_option('fiad_db_error');
			wp_delete_file(WP_CONTENT_DIR . '/db-error.php');
			$db_error_option['db_error_added'] = false;
			update_option('fiad_db_error', $db_error_option);
		}
	}
}

new Fiber_Admin_DB_Error();