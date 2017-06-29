<?php if ( !defined('BASEPATH')) exit('No direct script access allowed');

class Login extends MY_Controller {
	
	public function __construct() {
		parent::__construct();
		$this->load->helper('xlang');	
		$this->load->helper('cookie');	
	}

	// public function _remap( $param ) {
	// 	if( $param=="verificar" ) {
	// 		$this->verificar( $param );
	// 	} else if( $param=="fblogin" ) {
	// 		$this->fblogin( $param );
	// 	} else if( $param=="app_fblogin" ) {
	// 		$this->app_fblogin( $param );
	// 	} else {
	// 		$this->index( $param );
	// 	}
	// }
	
	public function index( $next="", $msg="" ) {

		if( $next=="index") {
			$next = "";
		}

		$head_data = array("min_template"=>"image_view",
			"title"=>$this->params['titulo_site'].": Login");

		$this->load->view('head', $head_data);
		$this->load->view('login',
			array('next'=>$next, 'msg'=>$msg) );
		$this->load->view('foot');
	}

	public function user_pass() {
		$status = $msg = "";

		$this->load->model('usuario_model');

		$form_data = $this->input->post(NULL, TRUE);

		$user_data = $this->usuario_model->check_login( $form_data['login'],
			$form_data['password'] );

		if( $user_data ) {
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
			$cookie = isset($form_data['lembrar']) ||
					strstr($user_agent, 'iPhone')!=FALSE ||
					strstr($user_agent, 'Android')!=FALSE;

			$session_data = set_user_session( $user_data, $cookie );

			$status = REQ_SUCCESS;
			$msg = "Login efetuado com sucesso";
		} else {
			$status = REQ_AUTH_ERROR;
			$msg = "Falha no login, tente novamente";
		}

		echo json_encode( array('status'=>$status, 'msg'=>$msg) );
	}

	public function app_fb( $access_token = "" ) {
		$fbuser = null;
		$status = $msg = "";
		
		//$fbuser = $this->curl_graph_getuser( $access_token );

	        $this->load->library("facebook",$this->params['facebook'] );
	        $this->facebook->setAccessToken( $access_token );
	        
		$fbuser = $this->facebook->api('/me?fields=email,first_name,last_name');

		if( $fbuser!=FALSE  ) {
			$this->load->model('usuario_model');
			$usuario = $this->usuario_model->get_data_email( $fbuser['email'] );

			if( FALSE!=$usuario ) {
				set_user_session( $usuario['id'] );
				$status = REQ_SUCCESS;
				$msg = "Login efetuado com sucesso";
			} else { // novo
				$this->load->model('usuario_model');
				$udata = array('login'=>$fbuser['email'],
							'email'=>$fbuser['email'],
							'nome'=>$fbuser['first_name'],
							'sobrenome'=>$fbuser['last_name']);

				$this->load->model('foto_model');
				$avatar = @$this->foto_model->import_fb_avatar( $fbuser['id'] );
				$udata['avatar'] = ( FALSE!=$avatar )?$avatar:"";

				$new_id = $this->usuario_model->insert( $udata );
				if( $new_id > 0 ) {
					set_user_session( $new_id, TRUE );
					$status = REQ_SUCCESS;
					$msg = "Login e novo cadastro efetuados com sucesso";
				} else {
					$status = REQ_AUTH_ERROR;
					$msg = "Não foi possível cadastrar novo Usuário";
				}
			}
		} else {
			error_log("Login: não foi possivel conectar com o facebook");
			$status = REQ_AUTH_ERROR;
			$msg = "Não foi possível cadastrar novo Usuário via Facebook";
		}

		echo json_encode( array('status'=>$status, 'msg'=>$msg) );
	}

	public function web_fb() {
		$fbuser = null;
		$logoutURL = null;
		$loginURL = null;

		$this->session->set_userdata('FbLoginPending', "1");

	        // load the facebook library
	        $this->load->library("facebook",$this->params['facebook'] );
	
		try {
	        // Get User ID
	        $fbuser = $this->facebook->getUser();
	        
	        if( $fbuser ) {
				// Proceed knowing you have a logged in user who's authenticated.
				$fbuser = $this->facebook->api('/me?fields=email,first_name,last_name');

				$this->load->model('usuario_model');
				$usuario = $this->usuario_model->get_data_email( $fbuser['email'] );

				if( FALSE!=$usuario ) {
					set_user_session( $usuario['id'] );
					$this->session->unset_userdata('FbLoginPending');
					redirect( base_url() );
				} else { //novo
					$this->session->unset_userdata('FbLoginPending');

					$this->load->model('foto_model');
					$avatar = @$this->foto_model->import_fb_avatar( $fbuser['id'] );
					$fbuser['avatar'] = ( FALSE!=$avatar )?$avatar:"";

					$this->load->model('usuario_model');
					$udata = array('login'=>$fbuser['email'],
								'email'=>$fbuser['email'],
								'nome'=>$fbuser['first_name'],
								'sobrenome'=>$fbuser['last_name']);

					$new_id = $this->usuario_model->insert( $udata );
					if( $new_id > 0 ) {
						set_user_session( $new_id );
						redirect( base_url() );
					} else {
		        		$this->index("", "Erro ao cadastrar novo Usuario");
					}
				}
			} else {
				error_log("Login: não foi possivel conectar com o facebook");
        		$this->index("", "Falha ao conectar com o Facebook, faça aqui o login ou registro.");
			}
		} catch( FacebookApiException $e ) {
			error_log("Login: ".$e);
        	$this->index("", "Falha ao conectar com o Facebook, faça aqui o login ou registro.");
		}
	}
}