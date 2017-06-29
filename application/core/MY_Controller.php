<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
	protected $login_data = array('logged_in'=>FALSE);
	protected $params = array();
	protected $is_user_logged_in = FALSE;

	public function __construct() {

		parent::__construct();
		$this->load->helper('xlogin');
		$this->load->helper('cookie');

		// params settings available to all Controllers
		$this->params = $this->config->item('site_params');

		// $this->validate_access_token();

		$request_headers = $this->input->request_headers();
		if( array_key_exists('Origin', $request_headers) ) {
			$origin = $request_headers['Origin'];
			$allowed_urls = $this->allowed_urls();
			if( in_array($origin, $allowed_urls) ) {
				header('Access-Control-Allow-Origin: '.$origin );
			}
		}
		
		$this->login_data = $this->check_session();
		$this->is_user_logged_in = $this->login_data["logged_in"];

		// load 'login_status' to the views
		$this->load->vars( array('login_data' => $this->login_data,
			'params'=>$this->params)  );
		
		header('Content-type: text/html; charset='.$this->config->item('charset'));
	}

	private function validate_access_token() {

		// ignore local calls
		if( $_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR'] ) {
			return;
		}

		$token = FALSE;

		$request_headers = $this->input->request_headers();
		if( array_key_exists('Pdpaccesstoken', $request_headers) ) {
			$token = $request_headers['Pdpaccesstoken'];
			$token = in_array($token, $this->params['access_tokens']);
		}

		if( !$token ) {
			$this->output->set_status_header(403, 'Forbidden');
			die('Forbidden');
		}
	}

	protected function sanitize_input( &$input_array, $fields ) {
		if( !is_array($input_array) ) {
			$input_array = array();
		}

		$dummy_array = array();
		foreach ($fields as $key => $value) {
			$dummy_array[ $value ] = NULL;
		}

		$input_array = array_merge( $dummy_array, $input_array );
	}

	protected function require_auth() {
		if( ENVIRONMENT=='production' ) {
			list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':',
				base64_decode( substr($_SERVER['HTTP_AUTHORIZATION'],6) ) );
		}

		$user = (isset($_SERVER['PHP_AUTH_USER']))?$_SERVER['PHP_AUTH_USER']:"";
		$pass = (isset($_SERVER['PHP_AUTH_PW']))?$_SERVER['PHP_AUTH_PW']:"";

		$basic_auth = $this->params['basic_auth'];

		$validated = ($user==$basic_auth['user'] && $pass==$basic_auth['pass']);

		if ( !$validated ) {
			header('WWW-Authenticate: Basic realm="PassouDoPonto.org"');
			header('HTTP/1.0 401 Unauthorized');
			die ("Not authorized");
		}
	}
	
	protected function check_owner( $model, $id ) {
		$status = $msg = NULL;

		if( empty($id) ) {
			$status = REQ_ERROR;
			$msg = "Identificador inválido";
		} else {
			$this->load->helper('xlang');
			if( !$this->is_user_logged_in ) {
				$status = REQ_AUTH_ERROR;
				$msg = xlang('dist_errsess_expire');
			} else {
				if( ! $model->is_owner( $this->login_data['user_id'], $id ) ) {
					$status = REQ_PERM_ERROR;
					$msg = xlang('dist_errperm');
				}
			}
			
		}
		
		return array($status, $msg);
	}

	protected function check_session() {
		$cookie = $this->input->cookie('DoacoesUserCookie');
		$fbReg = $this->input->cookie('FbRegPending');
		$session = $this->session->all_userdata();

		if( $cookie!=FALSE && !isset($session['user_id']) ) {
			$session = set_user_session( $cookie );
		} else if( $fbReg && !isset($session['fbuserdata']) ) {
			deauth_facebook();
			delete_cookie('FbRegPending');
		}

		$login_status = array('user_id' => 0,
			'logged_in'=>FALSE,
			'name'=>'',
			'type'=>'',
			'email'=>'');

		if( isset($session["user_id"]) ) {
			$login_status = array_merge( $login_status, $session );
		}
		
		return $login_status;
	}
	
	private function allowed_urls() {
		$alurls = array( rtrim( base_url(), '/') );
		$custom_urls = $this->config->item('allowed_urls');
		if( !empty($custom_urls) ) {
			$alurls = array_merge( $alurls, $this->config->item('allowed_urls') );
		}
		return $alurls;
	}

	protected function load_iframe($view_name, $data = array(), $return = FALSE) {
		if( !$return ) {
			$this->load->view('iframe_head', $data, FALSE);
			$this->load->view($view_name, $data, FALSE);
		} else {
			$out = $this->load->view('iframe_head', $data, TRUE);
			$out .= $this->load->view($view_name, $data, TRUE);
			return $out;
		}
	}

	protected function load_ajax($view_name, $data = array(), $return = FALSE) {
		if( !$return ) {
			$this->load->view('ajax_head', $data, FALSE);
			$this->load->view($view_name, $data, FALSE);
		} else {
			$out = $this->load->view('ajax_head', $data, TRUE);
			$out .= $this->load->view($view_name, $data, TRUE);
			return $out;
		}
	}

	public function show_access_error($type = "") {
		$this->load->helper('xlang');
		$this->load->helper('xerror');

		if( $type==="" ) {
			show_error( xlang('dist_errsess_expire'),
				403, $this->params['erro_acesso']);
		} else {
			show_error_windowed( xlang('dist_errsess_expire'),
				200, $this->params['erro_acesso'], $type);
		}
	}
}

