<?php

// Exit if accessed directly
if(!defined('ABSPATH')){
	exit;
}

/**
 * Cleanup file name
 */
class Fiber_Admin_Filename{
	public function __construct(){
		// Cleanup file name
		add_filter('sanitize_file_name_chars', [$this, 'fiad_special_chars']);
		add_filter('sanitize_file_name', [$this, 'fiad_cleanup_file_name'], 10, 2);
	}
	
	/*
	 * Return fiad special chars
	 */
	public function fiad_special_chars(){
		return [
			'?',
			'.',
			'[',
			']',
			'/',
			'\\',
			'=',
			'<',
			'>',
			':',
			';',
			',',
			"'",
			'"',
			'&',
			'$',
			'#',
			'*',
			'(',
			')',
			'|',
			'~',
			'`',
			'!',
			'{',
			'}',
			'+',
			'’',
			'«',
			'»',
			'”',
			'“',
			chr(0),
		];
	}
	
	public function fiad_handle_special_chars($sanitized_filename){
		$sanitized_filename = preg_replace('/[^A-Za-z0-9- ]/', '-', $sanitized_filename); // Remove special char not specified default by WordPress
		$sanitized_filename = preg_replace('/-{2,}/', '-', $sanitized_filename); // Replace a row of - with only 1 -
		$sanitized_filename = trim($sanitized_filename, '-'); // Remove - at the start
		
		// Remove - at the end
		return rtrim($sanitized_filename, '-');
	}
	
	// Cleanup file name
	public function fiad_cleanup_file_name($filename, $filename_raw){
		//variable
		$path_info          = pathinfo($filename);
		$file_extension     = fiad_array_key_exists('extension', $path_info);
		$sanitized_filename = basename($filename, "." . $file_extension);
		
		//special char case
		$sanitized_filename = $this->fiad_handle_special_chars($sanitized_filename);
		
		//lower case the filename
		return strtolower($sanitized_filename) . "." . $file_extension;
	}
}

new Fiber_Admin_Filename();