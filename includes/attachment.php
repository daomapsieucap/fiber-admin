<?php

// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Cleanup file name
 */
class Fiber_Admin_Attachment{
	public function __construct(){
		// Cleanup file name
		add_filter('sanitize_file_name_chars', [$this, 'fiad_special_chars']);
		add_filter('sanitize_file_name', [$this, 'fiad_cleanup_file_name'], 10, 2);
		add_filter('add_attachment', [$this, 'fiad_change_title']);
	}
	
	/*
	 * Return fiad special chars
	 */
	public function fiad_special_chars($special_chars){
		if(($key = array_search('%', $special_chars)) !== false){
			unset($special_chars[$key]);
		}
		
		return $special_chars;
	}
	
	public function fiad_handle_special_chars($sanitized_filename){
		//Replace all special chars and row of '-' with one '-' only
		$patterns           = ['/[^A-Za-z0-9- ]/', '/-{2,}/'];
		$sanitized_filename = preg_replace($patterns, '-', $sanitized_filename);
		
		// Remove - from the beginning and the end
		return trim($sanitized_filename, '-');
	}
	
	// Cleanup file name
	public function fiad_cleanup_file_name($filename, $filename_raw){
		//variable
		$path_info          = pathinfo($filename);
		$file_extension     = fiad_array_key_exists('extension', $path_info);
		$sanitized_filename = basename($filename, "." . $file_extension);
		
		$sanitized_filename = strtolower($sanitized_filename);
		
		//handle urlencoded chars
		preg_match_all('/%[0-9A-Fa-f]{2}/', $filename_raw, $matches);
		$urlencoded_chars = $matches[0];
		if($urlencoded_chars){
			$urlencoded_chars = array_map(function($char){
				return strtolower(trim($char, '%'));
			},$urlencoded_chars);
			$sanitized_filename = str_replace($urlencoded_chars, "", $sanitized_filename);
		}
		
		//special chars case
		$sanitized_filename = $this->fiad_handle_special_chars($sanitized_filename);
		
		return $sanitized_filename . "." . $file_extension;
	}
	
	public function fiad_change_title($post_id){
		fiad_update_post_meta($post_id, fiad_get_readable_filename($post_id));
	}
}

new Fiber_Admin_Attachment();