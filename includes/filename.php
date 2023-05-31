<?php

// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Default functions
 */
class Fiber_Admin_Filename{
	public function __construct(){
		// Auto Convert File Name
		if(fiad_get_miscellaneous_option('auto_convert_file_name')){
			add_filter('add_attachment', [$this, 'fiad_auto_convert_file_name']);
		}
	}
	
	// Auto Convert File Name
	public function fiad_auto_convert_file_name($post_id){
		// Get path info of orginal file
		$original_path = get_attached_file($post_id);
		$path_info     = pathinfo($original_path);
		$dirname       = ev_array_key_exists('dirname', $path_info);
		$filename      = ev_array_key_exists('filename', $path_info);
		$extension     = ev_array_key_exists('extension', $path_info);

		// Santize filename
		$sanitized_filename = sanitize_file_name($filename);
		$sanitized_filename = wp_unique_filename($dirname, $sanitized_filename);

		$fiber_path = $dirname . "/" . $sanitized_filename . "." . $extension;

		// Rename the file and update it's location in WP
		rename($original_path, $fiber_path);
		update_attached_file($post_id, $fiber_path);

		// Register filter to update metadata.
		add_filter('wp_update_attachment_metadata', function($data, $post_id) use ($fiber_path){
			return wp_generate_attachment_metadata($post_id, $fiber_path);
		}, 10, 2);
	}
}

new Fiber_Admin_Filename();