<?php
/*
 * Custom Xumb helper to provide session manipulation and login
 * functionality across Xumb.
 * Remember to call this from a Controller or a View before any output
 */
function set_user_session( $user_data, $set_cookie = FALSE ) {

	$CI = & get_instance();
	
	$session = $CI->session->all_userdata();
	if( isset($session["logged_in"]) ) {
		return $session;
	}

	// Se um array foi passado, basta colocar os dados na sessao
	// se nao for um array, é o user_data = ID 
	if( !is_array($user_data) ) {
		$CI->load->model('usuario_model');
		$user_data = $CI->usuario_model->get_data( $user_data );
	}

	if( FALSE!=$user_data ) {
		$session_data = array('logged_in'=>TRUE,
		  	'user_id' => $user_data['id'],
		  	'name' => $user_data['nome'],
		  	'email' => $user_data['email'] );

		$CI->session->set_userdata( $session_data );
		if( $set_cookie ) {
			$CI->input->set_cookie('PDPUserCookie', '1', 2592000 );
		}
		return $session_data; 
	} else {
		return false;
	}
}

function deauth_facebook() {
	$CI = & get_instance();

    $params = $CI->config->item('site_params');
    $CI->load->library("facebook", $params['facebook'] );

	try {
	    if( $fbuser ) {
		    $fbuser = $CI->facebook->getUser();
        	$revoke = $CI->facebook->api("/me/permissions", "DELETE");
        	return true;
        } else {
        	return false;
        }
	} catch (FacebookApiException $e) {
		error_log("Deauth: ".$e);
		return false;
	}
}