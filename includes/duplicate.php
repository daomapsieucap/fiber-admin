<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Duplicate post
 */
class Fiber_Admin_Duplicate{
	public function __construct(){
		// for non-hierarchy post types
		add_filter('post_row_actions', array($this, 'fiad_duplicate_link'), 10, 2);
		// for hierarchy post types
		add_filter('page_row_actions', array($this, 'fiad_duplicate_link'), 10, 2);
		
		add_action('admin_action_fiad_duplicate_post_as_draft', array($this, 'fiad_duplicate_post_as_draft'));
		
		add_action('admin_notices', array($this, 'fiad_duplication_admin_notice'));
	}
	
	public function fiad_duplicate_link($actions, $post){
		$duplicate_post_types = fiad_get_duplicate_option('post_types');
		if(!$duplicate_post_types){
			return $actions;
		}
		
		if(!current_user_can('edit_posts')){
			return $actions;
		}
		
		//check for your post type
		if(in_array($post->post_type, $duplicate_post_types)){
			$url = wp_nonce_url(
				add_query_arg(
					array(
						'action' => 'fiad_duplicate_post_as_draft',
						'post'   => $post->ID,
					),
					'admin.php'
				),
				basename(__FILE__),
				'duplicate_nonce'
			);
			
			$actions['duplicate'] = '<a href="' . $url . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
		}
		
		return $actions;
	}
	
	public function fiad_duplicate_post_as_draft(){
		
		// check if post ID has been provided and action
		if(empty($_GET['post'])){
			wp_die('No post to duplicate has been provided!');
		}
		
		// Nonce verification
		if(!isset($_GET['duplicate_nonce']) || !wp_verify_nonce($_GET['duplicate_nonce'], basename(__FILE__))){
			return;
		}
		
		// Get the original post id
		$post_id = absint($_GET['post']);
		
		// And all the original post data then
		$post = get_post($post_id);
		
		/*
		 * if you don't want current user to be the new post author,
		 * then change next couple of lines to this: $new_post_author = $post->post_author;
		 */
		$current_user    = wp_get_current_user();
		$new_post_author = $current_user->ID;
		
		// if post data exists (I am sure it is, but just in a case), create the post duplicate
		if($post){
			
			// new post data array
			$args = array(
				'comment_status' => $post->comment_status,
				'ping_status'    => $post->ping_status,
				'post_author'    => $new_post_author,
				'post_content'   => $post->post_content,
				'post_excerpt'   => $post->post_excerpt,
				'post_name'      => $post->post_name,
				'post_parent'    => $post->post_parent,
				'post_password'  => $post->post_password,
				'post_status'    => 'draft',
				'post_title'     => $post->post_title,
				'post_type'      => $post->post_type,
				'to_ping'        => $post->to_ping
			);
			
			// insert the post by wp_insert_post() function
			$new_post_id = wp_insert_post($args);
			
			/*
			 * get all current post terms ad set them to the new post draft
			 */
			$taxonomies = get_object_taxonomies(get_post_type($post)); // returns array of taxonomy names for post type, ex array("category", "post_tag");
			if($taxonomies){
				foreach($taxonomies as $taxonomy){
					$post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
					wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
				}
			}
			
			// duplicate all post meta
			$post_meta = get_post_meta($post_id);
			if($post_meta){
				
				foreach($post_meta as $meta_key => $meta_values){
					
					if('_wp_old_slug' == $meta_key){ // do nothing for this meta key
						continue;
					}
					
					foreach($meta_values as $meta_value){
						add_post_meta($new_post_id, $meta_key, $meta_value);
					}
				}
			}
			
			wp_safe_redirect(
				add_query_arg(
					array(
						'post_type' => ('post' !== get_post_type($post) ? get_post_type($post) : false),
						'saved'     => 'post_duplication_created' // just a custom slug here
					),
					admin_url('edit.php')
				)
			);
			exit;
			
		}else{
			wp_die('Post creation failed, could not find original post.');
		}
		
	}
	
	public function fiad_duplication_admin_notice(){
		
		// Get the current screen
		$screen = get_current_screen();
		
		if('edit' !== $screen->base){
			return;
		}
		
		//Checks if settings updated
		if(isset($_GET['saved']) && 'post_duplication_created' == $_GET['saved']){
			echo '<div class="notice notice-success is-dismissible"><p>Post copy created.</p></div>';
		}
	}
	
}

new Fiber_Admin_Duplicate();