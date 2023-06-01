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
			add_filter('sanitize_file_name', [$this, 'fiad_auto_convert_file_name'],10,2);
		}
	}
	
	// Auto Convert File Name
	public function fiad_auto_convert_file_name($filename, $filename_raw){
		$sanitized_filename = str_replace('%20', '-', $filename_raw); // Replace %20 with -

		$sanitized_filename = remove_accents($sanitized_filename); // Convert accent char to regular char
		$sanitized_filename = preg_replace('/[^A-Za-z0-9-. ]/', '-', $sanitized_filename); // Remove all non-alphanumeric except .
		$sanitized_filename = preg_replace('/\.+/', '.', $sanitized_filename); // Replace a row of . with only 1 .
		$sanitized_filename = preg_replace('/-+/', '-', $sanitized_filename); // Replace a row of - with only 1 -
		$sanitized_filename = str_replace('-.', '.', $sanitized_filename); // Remove last - if at the end
		
		$fiber_filename = strtolower($sanitized_filename); // Lowercase
		
		return $fiber_filename;
	}
}

new Fiber_Admin_Filename();