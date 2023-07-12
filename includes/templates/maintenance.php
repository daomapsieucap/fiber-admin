<?php
header('HTTP/1.1 503 Service Temporarily Unavailable', true, 503);
header('Status: 503 Service Temporarily Unavailable');
header('Retry-After: 3600');
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head><title>Training Backend</title>
    <link rel="icon" type="image/png" href=""/><?php wp_head(); ?></head>
<body>
<div class="fiad-maintenance-content">
    <div class="ev-content-wrapper wpb_content_element wpb_text_column">
		<?php
		$content_page_id = '';
		$content_page_id = fiad_get_csm_mode_option('page');
		
		$post = get_post($content_page_id);
		?>
        <h1 class="fiad-maintenance-title"><?= $post->post_title; ?></h1>
	    <?= ev_vc_content($post->post_content); ?>
    </div>
</div><?php wp_footer(); ?></body>
</html>