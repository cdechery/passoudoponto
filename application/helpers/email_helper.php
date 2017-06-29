<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	Email Helper - Custom Helper
*/
	function send_email( $params ) {

		$CI =& get_instance();
		$sim_only = $CI->config->item('email_sim_only');
		$sim_only = $sim_only && (ENVIRONMENT!='production');

		extract( $params );

		if( empty($from_email) || empty($to_email) ||
			empty($subject) || empty($body) ) {

			$caller = $CI->router->method;
			log_message('error', 'Tentativa de enviar email com parametros invalidos ('.$caller.')');

			return FALSE;
		}

		$CI->email->clear();

		if( isset($from_name) ) {
			$CI->email->from( $from_email, $from_name );
		} else {
			$CI->email->from( $from_email );
		}

		if( isset($reply_to) ) {
			$CI->email->reply_to( $reply_to );
		}

		if( isset($to_name) ) {
			$CI->email->to( $to_email, $to_name );
		} else {
			$CI->email->to( $to_email );
		}

		$CI->email->subject( $subject );

		$emailmsg = $CI->load->view('email_head', NULL, TRUE);
		$emailmsg .= $body;
		$emailmsg .= $CI->load->view('email_foot',
			 array('email_para'=>$to_email), TRUE);

		if( $sim_only ) {
			
			$emailmsg .= '<!--';
			$emailmsg .= var_export($params, true);
			$emailmsg .= '-->';

			$tmp_emailfile = 'email'.uniqid().'.html';
			$file = fopen("./emails/".$tmp_emailfile, "w");
			fwrite($file, $emailmsg);
			fclose($file);
			return true;

		} else {
			$CI->email->message( $emailmsg );
			$ret = $CI->email->send();

			if( !$ret ) {
				error_log( $CI->email->print_debugger() );
			}
			
			return $ret;
		}
	}
?>