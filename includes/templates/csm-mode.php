<?php
$mode            = fiad_get_csm_mode_option('mode');
$is_maintenance  = $mode == 'maintenance';
$content_page_id = fiad_get_csm_mode_option('page');

global $post;
$post = get_post($content_page_id);
setup_postdata($post);

$title     = get_the_title();
$id        = get_the_ID();
$content   = get_post_meta($id, 'fiad_csm_content', true);
$bg_url    = get_post_meta($id, 'fiad_csm_background_image', true);
$logo_url  = get_post_meta($id, 'fiad_csm_logo', true);
$content   = apply_filters('the_content', $content); // keep content stay exactly the same with editor
$bg_html   = $bg_url ? '<div class="site-content-image"><img src="' . $bg_url . '" alt="Background image" /></div>' : '';
$logo_html = $logo_url ? '<div class="site-content-logo"><img src="' . $logo_url . '" alt="Logo" /></div>' : '';
if($is_maintenance){
	header('HTTP/3 503 Service Temporarily Unavailable', true, 503);
	header('Status: 503 Service Temporarily Unavailable');
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta http-equiv="Content-Type" content="text/html" charset=<?php bloginfo('charset'); ?>/>
	<?php do_action('fiad_script_head'); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title; ?></title>
	<?= apply_filters('fiad_csm_extra_css', '', $mode); ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php
do_action('fiad_script_body');
if($content){
	if($bg_html){
		echo str_replace('#background', $bg_html, $content);
	}
	if($logo_html){
		echo str_replace('#logo', $logo_html, $content);
	}
}
wp_reset_postdata();
?>
<?= apply_filters('fiad_csm_extra_js', '', $mode); ?>
</body>
</html>
