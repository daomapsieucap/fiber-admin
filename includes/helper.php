<?php
// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Helper functions
 */

if(!function_exists('fiad_get_option')){
	function fiad_get_option($key, $fiber_admin){
		if(is_array($fiber_admin)){
			if(array_key_exists($key, $fiber_admin)){
				return $fiber_admin[$key];
			}
		}
		
		return '';
	}
}

if(!function_exists('fiad_get_general_option')){
	function fiad_get_general_option($key){
		return fiad_get_option($key, get_option('fiber_admin'));
	}
}

if(!function_exists('fiad_get_miscellaneous_option')){
	function fiad_get_miscellaneous_option($key){
		return fiad_get_option($key, get_option('fiad_miscellaneous'));
	}
}

if(!function_exists('fiad_get_cpo_option')){
	function fiad_get_cpo_option($key){
		return fiad_get_option($key, get_option('fiad_cpo'));
	}
}

if(!function_exists('fiad_is_screen_sortable')){
	function fiad_is_screen_sortable(){
		if(is_admin()){
			$post_types = fiad_get_cpo_option('post_types');
			$taxonomies = fiad_get_cpo_option('taxonomies');
			
			if(!function_exists('get_current_screen')){
				require_once ABSPATH . '/wp-admin/includes/screen.php';
			}
			$screen = get_current_screen();
			
			if(($post_types || $taxonomies) && $screen){
				if($screen->taxonomy && $taxonomies && strpos($screen->base, 'edit') !== false){
					$screen_tax = $screen->taxonomy;
					if(in_array($screen_tax, $taxonomies)){
						return true;
					}
				}elseif($post_types && $screen->base == 'edit'){
					$screen_post_type = $screen->post_type;
					if(in_array($screen_post_type, $post_types)){
						return true;
					}
				}
			}
		}
		
		return false;
	}
}

if(!function_exists('fiad_get_duplicate_option')){
	function fiad_get_duplicate_option($key){
		return fiad_get_option($key, get_option('fiad_duplicate'));
	}
}

if(!function_exists('fiad_admin_user_role')){
	function fiad_is_admin_user_role(){
		if(is_user_logged_in()){
			if(current_user_can('edit_posts')){
				return true;
			}
		}
		
		return false;
	}
}

if(!function_exists('fiad_get_db_error_option')){
	function fiad_get_db_error_option($key){
		return fiad_get_option($key, get_option('fiad_db_error'));
	}
}

if(!function_exists('fiad_check_db_error_file')){
	function fiad_check_db_error_file(){
		return (file_exists(WP_CONTENT_DIR . '/db-error.php'));
	}
}

if(!function_exists('fiad_get_maintenance_mode_option')){
	function fiad_get_maintenance_mode_option($key){
		return fiad_get_option($key, get_option('fiad_maintenance_mode'));
	}
}

if(!function_exists('fiad_array_key_exists')){
	function fiad_array_key_exists($key, $array, $default = ''){
		if($array && is_array($array)){
			if(array_key_exists($key, $array)){
				return $array[$key] ? : $default;
			}
		}
		
		return $default;
	}
}

if(!function_exists('fiad_get_readable_filename')){
	function fiad_get_readable_filename($post_id){
		$file          = get_attached_file($post_id);
		$file_pathinfo = pathinfo($file);
		$file_name     = fiad_array_key_exists('filename', $file_pathinfo);
		
		//check if the file name contain index at the end
		$pattern = '/-\d+$/';
		if(preg_match($pattern, $file_name)){
			$file_name = preg_replace($pattern, '', $file_name);
		}
		
		$file_name = str_replace('-', ' ', $file_name);
		
		return ucwords($file_name);
	}
}

if(!function_exists('fiad_update_post_meta')){
	function fiad_update_post_meta($post_id, $post_title, $extra_args = []){
		$fiber_meta = [
			'ID'         => $post_id,
			'post_title' => $post_title,
		];
		if($extra_args){
			$fiber_meta = array_merge($fiber_meta, $extra_args);
		};
		wp_update_post($fiber_meta);
	}
}