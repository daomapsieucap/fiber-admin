<?php
$mode            = fiad_get_csm_mode_option('mode');
$is_maintenance  = $mode == 'maintenance';
$content_page_id = fiad_get_csm_mode_option('page');

$post    = get_post($content_page_id);
$content = $post->post_content;
$title   = $post->post_title;
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
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php do_action('fiad_script_body'); ?>
<div class="site-content">
    <div class="container">
		<?= apply_filters('the_content', $content); ?>
    </div>
</div>
</body>
</html>
