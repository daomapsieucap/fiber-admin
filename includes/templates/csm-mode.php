<?php
$mode            = fiad_get_csm_mode_option('mode');
$is_maintenance  = $mode == 'maintenance';
$content_page_id = fiad_get_csm_mode_option('page');

global $post;
$post = get_post($content_page_id);
setup_postdata($post);

$content = get_the_content();
$title   = preg_replace('/[^a-zA-Z]/', ' ', mb_convert_case($mode, MB_CASE_TITLE));
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
<?php do_action('fiad_script_body'); ?>
<div class="site-content">
    <div class="container">
		<?= apply_filters('the_content', $content); ?>
    </div>
</div>
<?php wp_reset_postdata(); ?>
<?= wp_unslash(apply_filters('fiad_csm_extra_js', '', $mode)); ?>
</body>
</html>
