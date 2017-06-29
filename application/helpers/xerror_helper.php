<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Custom error_handling helper
 */
	function show_error_windowed($message, $status_code = 500, $heading = NULL, $type="ajax") {
		$_error =& load_class('Exceptions', 'core');
		echo $_error->show_error_windowed($heading, $message, 'error_general', $status_code, $type);
		exit;
	}
 ?>