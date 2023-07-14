<?php
$mode           = fiad_get_csm_mode_option('mode');
$is_maintenance = $mode == 'maintenance';
$title          = $is_maintenance ? "Maintenance" : "Coming Soon";
if($is_maintenance){
	header('HTTP/1.1 503 Service Temporarily Unavailable', true, 503);
	header('Status: 503 Service Temporarily Unavailable');
}
?>
<!DOCTYPE HTML>
<html <?php language_attributes(); ?>>
<head>
    <title><?= $title; ?></title>
	<?php wp_head(); ?>
</head>
<body class="fiad-csm-mode">
<div class="fiad-maintenance-content">
	<?php
	$content_page_id = '';
	$content_page_id = fiad_get_csm_mode_option('page');
	
	$post    = get_post($content_page_id);
	$content = $post->post_content;
	?>
    <h1 class="fiad-maintenance-title"><?= $post->post_title; ?></h1>
	<?= apply_filters('the_content', $content); ?>
</div>
<?php wp_footer(); ?>
</body>
</html>