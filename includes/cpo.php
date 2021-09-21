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
		add_action('admin_enqueue_scripts', array($this, 'fiad_cpo_scripts'));
		
		add_action("wp_ajax_fiad_cpo_update", array($this, 'fiad_cpo_update'));
		add_action("wp_ajax_nopriv_fiad_cpo_update", array($this, 'fiad_cpo_update'));
		
		add_action("wp_ajax_fiad_cpo_tax_update", array($this, 'fiad_cpo_tax_update'));
		add_action("wp_ajax_nopriv_fiad_cpo_tax_update", array($this, 'fiad_cpo_tax_update'));
		
		add_action('pre_get_posts', array($this, 'fiad_cpo_update_order'));
		add_filter('get_terms_orderby', array($this, 'fiad_cpo_update_term_order'), 10, 3);
	}
	
	public function fiad_cpo_scripts(){
		if(fiad_is_screen_sortable()){
			wp_enqueue_script('fiber-admin-cpo', FIBERADMIN_ASSETS_URL . 'js/fiber-cpo.js', array('jquery-ui-sortable'), FIBERADMIN_VERSION, true);
			wp_localize_script(
				'fiber-admin-cpo',
				'fiad_cpo',
				array('ajax_url' => admin_url('admin-ajax.php'))
			);
		}
	}
	
	public function fiad_cpo_update(){
		
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
	
	public function fiad_cpo_update_order($query){
		if($query->is_main_query()){
			if(is_admin()){
				// Change post order by default in admin
				if(function_exists('get_current_screen')){
					$screen = get_current_screen();
					if(fiad_is_screen_sortable() && !$screen->taxonomy){
						$query->set('orderby', 'menu_order');
						$query->set('order', 'ASC');
					}
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
	
	public function fiad_cpo_tax_update(){
		
		if(!$_POST || (!$_POST['cpo_data'] && !$_POST['taxonomy'])){
			return false;
		}
		
		global $wpdb;
		
		parse_str($_POST['cpo_data'], $taxonomy_order);
		
		$term_ids = $taxonomy_order['tag'];
		$taxonomy = $_POST['taxonomy'];
		
		if($term_ids){
			$order_start = 0;
			
			// Get minimum item
			$pre_terms_args = array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'number'     => 1,
				'orderby'    => 'term_order',
				'order'      => 'ASC',
				'include'    => $term_ids
			);
			
			$pre_terms = get_terms($pre_terms_args);
			if(!empty($pre_terms) && !is_wp_error($pre_terms)){
				$order_start = $pre_terms[0]->term_order;
			}
			
			// Update term order
			$update_term_args = array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'orderby'    => 'include',
				'order'      => 'ASC',
				'include'    => $term_ids,
				'fields'     => 'ids'
			);
			$update_terms     = get_terms($update_term_args);
			if(!empty($update_terms) && !is_wp_error($update_terms)){
				foreach($update_terms as $term_id){
					$wpdb->update($wpdb->terms, array('term_order' => $order_start), array('term_id' => $term_id));
					$order_start ++;
				}
			}
		}
		
		die();
	}
	
	public function fiad_cpo_update_term_order($orderby, $query_vars, $taxonomies){
		if(is_admin()){
			// Change taxonomy order by default in admin
			if(function_exists('get_current_screen')){
				$screen = get_current_screen();
				if(fiad_is_screen_sortable() && $screen->taxonomy){
					return 't.term_order';
				}
			}
		}else{
			// Only update post order on frontend if enable option
			if(fiad_get_cpo_option('override_default_tax_query')){
				return 't.term_order';
			}
		}
		
		return $query_vars['orderby'] == 'term_order' ? 't.term_order' : $orderby;
	}
}

new Fiber_Admin_CPO();