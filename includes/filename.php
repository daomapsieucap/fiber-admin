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
			add_filter('sanitize_file_name', [$this, 'fiad_auto_convert_file_name'], 10, 2);
		}
	}
	
	// Auto Convert File Name
	public function fiad_auto_convert_file_name($filename, $filename_raw){
		$sanitized_filename = $filename;
		$url_decode_raw     = urldecode($filename_raw);
		$sanitized_filename = str_split($sanitized_filename);
		if($url_decode_raw != $filename_raw){
			$count = 0;
			foreach (str_split($url_decode_raw) as $index => $char) {
				if ($char === ' ') {
					if ($count == 0) {
						$sanitized_filename[$index-1] = '';
						$sanitized_filename[$index] = '-';
					} else {
						$sanitized_filename[$index] = '';
						$sanitized_filename[$index+1] = '-';
					}
					$count++;
				}
			}
		}
		$sanitized_filename = implode('',$sanitized_filename);
		
		$sanitized_filename = preg_replace('/[^A-Za-z0-9-\. ]/', '-', $sanitized_filename); // Remove special char not specified default by WordPress
		$sanitized_filename = str_replace('_', '-', $sanitized_filename); // Replace _ with -
		$sanitized_filename = preg_replace('/\.+/', '-', $sanitized_filename); // Replace a row of . with only 1 .
		$sanitized_filename = preg_replace('/-+/', '-', $sanitized_filename); // Replace a row of - with only 1 -
		$sanitized_filename = str_replace('-.', '.', $sanitized_filename); // Remove - before extension
		$sanitized_filename = trim($sanitized_filename, '-'); // Remove - at the start
		$sanitized_filename = rtrim($sanitized_filename, '-'); // Remove - at the end
		
		return strtolower($sanitized_filename);
	}
}

new Fiber_Admin_Filename();