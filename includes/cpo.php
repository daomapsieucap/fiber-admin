<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Custom Post Order
 */
class Fiber_Admin_CPO{
	public function __construct(){
		add_action('load-edit.php', array($this, 'fiad_cpo_scripts'));
		
		add_action("wp_ajax_fiber_cpo_update", array($this, 'fiber_cpo_update'));
		add_action("wp_ajax_nopriv_fiber_cpo_update", array($this, 'fiber_cpo_update'));
		
		add_action('pre_get_posts', array($this, 'fiber_cpo_update_order'));
	}
	
	public function fiad_cpo_scripts(){
		if(fiad_is_screen_sortable()){
			wp_enqueue_script('fiber-admin-cpo', FIBERADMIN_ASSETS_URL . 'js/fiber-cpo.js', array('jquery-ui-sortable'), FIBERADMIN_VERSION, true);
			wp_localize_script(
				'fiber-admin-cpo',
				'fiber_cpo',
				array('ajax_url' => admin_url('admin-ajax.php'))
			);
		}
	}
	
	public function fiber_cpo_update(){
		
		if(!$_POST || (!$_POST['cpo_data'] && !$_POST['post_type'])){
			return false;
		}
		
		parse_str($_POST['cpo_data'], $cpo_order);
		
		global $wpdb;
		
		$post_type = $_POST['post_type'];
		
		$post_status = $_POST['post_status'] ? : 'publish';
		$post_status = $post_status == 'all' ? 'any' : $post_status;
		
		$post_ids = $cpo_order['post'];
		
		if($post_ids){
			$order_start = 0;
			
			// Get minimum post order
			$pre_posts_args  = array(
				'post_type'        => $post_type,
				'posts_per_page'   => 1,
				'post_status'      => $post_status,
				'orderby'          => 'menu_order',
				'order'            => 'ASC',
				'post__in'         => $post_ids,
				'suppress_filters' => false,
				'fields'           => 'ids'
			);
			$pre_posts_query = new WP_Query($pre_posts_args);
			if($pre_posts_query->have_posts()){
				$order_start_id   = $pre_posts_query->posts[0];
				$order_start_post = get_post($order_start_id);
				$order_start      = $order_start_post->menu_order;
			}
			
			// Update post order
			$update_posts_args  = array(
				'post_type'        => $post_type,
				'posts_per_page'   => - 1,
				'post_status'      => $post_status,
				'orderby'          => 'post__in',
				'order'            => 'ASC',
				'post__in'         => $post_ids,
				'suppress_filters' => false,
				'fields'           => 'ids'
			);
			$update_posts_query = new WP_Query($update_posts_args);
			if($update_posts_query->have_posts()){
				foreach($update_posts_query->posts as $id){
					$wpdb->update($wpdb->posts, array('menu_order' => $order_start), array('ID' => intval($id)));
					$order_start ++;
				}
			}
		}
		
		die();
	}
	
	public function fiber_cpo_update_order($query){
		if($query->is_main_query()){
			if(is_admin()){
				// Change post order by default in admin
				if(fiad_is_screen_sortable()){
					$query->set('orderby', 'menu_order');
					$query->set('order', 'ASC');
				}
			}else{
				// Only update post order on frontend if enable option
				if(fiad_get_cpo_option('override_default_query')){
					$query->set('orderby', 'menu_order');
					$query->set('order', 'ASC');
				}
			}
		}
	}
}

new Fiber_Admin_CPO();