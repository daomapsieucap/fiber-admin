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
		// Cleanup file name
		if(fiad_get_miscellaneous_option('auto_convert_file_name')){
			add_filter('sanitize_file_name', [$this, 'fiad_auto_convert_file_name'], 10, 2);
		}
	}
	
	public function fiad_handle_special_char($sanitized_filename){
		$sanitized_filename = preg_replace('/[^A-Za-z0-9- ]/', '-', $sanitized_filename); // Remove special char not specified default by WordPress
		$sanitized_filename = str_replace('_', '-', $sanitized_filename); // Replace _ with -
		$sanitized_filename = preg_replace('/\.{2,}/', '-', $sanitized_filename); // Replace a row of . with only 1 .
		$sanitized_filename = preg_replace('/-{2,}/', '-', $sanitized_filename); // Replace a row of - with only 1 -
		$sanitized_filename = str_replace('-.', '.', $sanitized_filename); // Remove - before extension
		$sanitized_filename = trim($sanitized_filename, '-'); // Remove - at the start
		$sanitized_filename = rtrim($sanitized_filename, '-'); // Remove - at the end
		
		return $sanitized_filename;
	}
	
	// Auto Convert File Name
	public function fiad_auto_convert_file_name($filename, $filename_raw){
		//handle urlencode case
		// Before: Really%20Ugly%20Filename--That-Is_Too Common…..png
		// Default of Wordpress: ReAlly20Ugly20Filename-_-That_-_Is_Too-Common….pdf (still remains the '20')
		// After ReAllyUglyFilename-_-That_-_Is_Too-Common….pdf (still remain some special char but get rid of the '20')
		$sanitized_filename = $filename;
		$url_decode_raw     = urldecode($filename_raw);
		$sanitized_filename = str_split($sanitized_filename);
		if($url_decode_raw != $filename_raw){
			$count = 0;
			foreach(str_split($url_decode_raw) as $index => $char){
				if($char === ' '){
					if($count == 0){
						$sanitized_filename[$index - 1] = '';
						$sanitized_filename[$index]     = '-';
					}else{
						$sanitized_filename[$index]     = '';
						$sanitized_filename[$index + 1] = '-';
					}
					$count ++;
				}
			}
		}
		$sanitized_filename = implode('', $sanitized_filename);
		
		//special char case
		$sanitized_filename = $this->fiad_handle_special_char($sanitized_filename);
		
		//handle dot in file name case (not the extension)
		for($i = strlen($sanitized_filename) - 1;$i >= 0;$i --){
			if($sanitized_filename[$i] == '-'){
				$sanitized_filename[$i] = '.';
				break;
			}
		}
		
		//lower case the filename
		return strtolower($sanitized_filename);
	}
}

new Fiber_Admin_Filename();