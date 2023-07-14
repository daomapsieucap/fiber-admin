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
	<?= get_field("script_head", "option"); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title; ?></title>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?= get_field("script_body", "option"); ?>
<div class="site-content">
    <div class="container">
		<?php
		if(!$content){
			?>
            <article>
                <h1><?= $is_maintenance ? "We&rsquo;ll be back soon!" : "Coming Soon"; ?></h1>
                <div>
                    <p>
						<?=
						$is_maintenance ? 'Sorry for the inconvenience but we&rsquo;re performing some maintenance at the moment. If you
                            need to you can always contact us, otherwise we&rsquo;ll be back online
                            shortly!' : 'Our website is currently undergoing scheduled maintenance. We Should be back shortly. Thank you for your patience.';
						?>
                    </p>
                    <a href="mailto:#"
                       title="<?= $title; ?>"><?= $is_maintenance ? '&mdash; The Team' : 'Notify Us'; ?></a>
                </div>
            </article>
			<?php
		}else{
			echo apply_filters('the_content', $content);
		}
		?>
    </div>
</div>
</body>
</html>
