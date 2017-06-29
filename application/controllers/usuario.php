<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuario extends MY_Controller {
	
	public function __construct() {
		parent::__construct();
		$this->load->model('usuario_model');
		$this->load->helper('xlang');
		$this->load->helper('cookie');
	}

	// public function test_login( $id = NULL ) {
	// 	$status = $msg = "";

	// 	if( empty($id) ) {
	// 		$status = REQ_ERROR;
	// 		$msg = "ID de usuário vazio/inválido";
	// 		echo json_encode( array('status'=>$status, 'msg'=>$msg) );
	// 		return;
	// 	}

	// 	$this->session->sess_destroy();
	// 	$this->session->sess_create();

	// 	$this->load->helper('xlogin');
	// 	if( set_user_session( $id ) ) {
	// 		$status = REQ_SUCCESS;
	// 		$msg = "Login efetuado com sucesso para ID=".$id;
	// 	} else {
	// 		$status = REQ_AUTH_ERROR;
	// 		$msg = "Não foi possível fazer Login para ID=".$id;
	// 	}

	// 	echo json_encode( array('status'=>$status, 'msg'=>$msg) );
	// }

	public function get_data( $id = NULL ) {
		$status = $data = $msg = "";

		if( empty($id) ) {
			if( $this->is_user_logged_in ) {
				$id = $this->login_data['user_id'];
			} else {
				$status = REQ_AUTH_ERROR;
				$msg = "Sessão inválida";
			}
		} else {
			$status = REQ_ERROR;
			$msg = "ID vazio ou inválido";
		}

		if( empty($status) ) {
			$data = $this->usuario_model->get_data( $id );
			if( $data ) {
				$status = REQ_SUCCESS;
				// a senha nao deve ser retornada
				unset( $data['senha'] );
			} else {
				$status = REQ_ERROR;
				$data = "";
				$msg = "Usuário [".$id."] não encontrado ou inválido";
			}
		}

		echo json_encode( array('status'=>$status, 'msg'=>$msg,
			'data'=>$data) );
	}

	public function app_logout() {
		$this->session->sess_destroy();
		delete_cookie('PDPUserCookie');
		delete_cookie('FbRegPending');

		echo json_encode( array('status'=>REQ_SUCCESS) );
	}

	public function logout() {
		$logoutFB = false;
		$logoutURL = "";

        $this->load->library("facebook", $this->params['facebook'] );

		try {
	        $fbuser = $this->facebook->getUser();
	        if( $fbuser ) {
    	    	$logoutFB = true;
	        	$fbuser = $this->facebook->api('/me');
	        	// $revoke = $this->facebook->api("/me/permissions", "DELETE");
				$logoutURL = $this->facebook->getLogoutUrl( array('acess_token'=>$fbuser['id'],
					'next'=>base_url()) );
			}
		} catch (FacebookApiException $e) {
			error_log("Logout: ".$e);
			$fbuser = null;
        }

		$this->session->sess_destroy();
		delete_cookie('PDPUserCookie');
		delete_cookie('FbRegPending');

		if( $logoutFB ) {
			redirect( $logoutURL );
		} else {
			redirect( base_url() );
		}
	}

	public function ocorrencias() {
		$status = $msg = $ocorrs = "";

		if( !$this->is_user_logged_in ) {
			$status = REQ_AUTH_ERROR;
			$msg = xlang('dist_errsess_expire');
		} else {
			$status = REQ_SUCCESS;
			$id = $this->login_data['user_id'];
			$this->load->model('ocorrencia_model');
			$ocorrs = $this->ocorrencia_model->get_user_ocorrencias( $id );
		}

		echo json_encode( array('status'=>$status, 
			'msg'=>$msg, 'data'=>$ocorrs) );
	}

	// public function pref_email() {
	// 	if( ! $this->is_user_logged_in ) {
	// 		$next = urlencode( base64_encode("usuario/pref_email") );
	// 		redirect( "login/".$next );
	// 	}

	// 	$udata = $this->usuario_model->get_data( $this->login_data['user_id'] );

	// 	$head_data = array("title"=>"Preferências de Email");
	// 	$this->load->view('head', $head_data);
	// 	$this->load->view('pref_email', array('data'=>$udata));
	// 	$this->load->view('foot');
	// }

	// public function salvar_pref_email() {
	// 	$status = $msg = "";

	// 	if( !$this->is_user_logged_in ) {
	// 		$status = "error";
	// 		$msg = xlang('dist_errsess_expire');
	// 	} else {
	// 		$user_data = $this->input->post(NULL, TRUE);

	// 		$user_data['fg_geral_email'] = isset($user_data['fg_geral_email'])?'S':'N';
	// 		$user_data['fg_notif_int_email'] = isset($user_data['fg_notif_int_email'])?'S':'N';
	// 		$user_data['fg_de_inst_email'] = isset($user_data['fg_de_inst_email'])?'S':'N';
	// 		$user_data['fg_de_pessoa_email'] = isset($user_data['fg_de_pessoa_email'])?'S':'N';
	// 		$lim = $user_data['lim_emails_item'];

	// 		if( empty($lim) || !is_numeric($lim) || $lim==0 || $lim>99 ) {
	// 			$status = "ERRO";
	// 			$msg = "Preencha corretamente o 'Limite'.<br>Deve ser um número inteiro maior que 0 e menor que 99";
	// 		} else {
	// 			if( $this->usuario_model->update_pref_email($user_data, 
	// 				$this->login_data['user_id']) ) {

	// 				$status = "OK";
	// 				$msg = "Preferências salvas com sucesso!";
	// 			} else {
	// 				$status = "ERRO";
	// 				$msg = "Ocorreu um erro ao salvar as preferências";
	// 			}
	// 		}
	// 	}

	// 	echo json_encode( array('status'=>$status, 'msg'=>$msg) );
	// }

	public function novo($tipo = NULL) {
		$this->load->helper('image_helper');
		$this->load->helper('form');
	
		if( $this->is_user_logged_in ) {
			redirect( base_url() );
		}

		// $head_data = array("title"=>"Novo Usuário",
		// 	"min_template"=>"image_view" );
		// $this->load->view('head', $head_data);

		$data = array('action' => 'insert');
		$fbReg = $this->input->cookie('FbRegPending');
		if( $fbReg ) {
			$fbdata = $this->session->userdata('fbuserdata');
			$data['nome'] = $fbdata['first_name'];
			$data['sobrenome'] = $fbdata['last_name'];
			$data['email'] = $fbdata['email'];
			$data['avatar'] = $fbdata['avatar'];
		}

		$this->load->view('user_form', array('data'=>$data,
				'tipo'=>$tipo) );
		// $this->load->view('foot');
	}

	public function insert() {
		$status = "";
		$msg = "";
		$new_id = 0;

		$user_data = $this->input->post(NULL, TRUE);
		$this->sanitize_input( $user_data, array('login', 'email', 
			'nome', 'sobrenome', 'nascimento', 'password') );

		$this->load->helper('form');
		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('','</br>');

		$this->form_validation->set_rules('login', 'Login',
			'required|min_length[6]|max_length[100]|is_unique[usuario.login]|xss_clean');
		$this->form_validation->set_rules('email', 'E-mail', 'required|is_unique[usuario.email]|valid_email');
		$this->form_validation->set_rules('nome', 'Nome', 'required|min_length[3]|max_length[120]');
		$this->form_validation->set_rules('sobrenome', 'Sobrenome', 'required|min_length[3]|max_length[40]');
		$this->form_validation->set_rules('nascimento', 'Nascimento', 'callback_bday_check');
		$this->form_validation->set_rules('password', 'Senha', 'required|min_length[6]|max_length[8]');
		$this->form_validation->set_rules('password_2', 'Confirmação de senha', 'required|matches[password]');

		if ($this->form_validation->run() == FALSE) {
			$status = "ERROR";
			$msg = validation_errors();
		} else {
			$new_id = $this->usuario_model->insert( $user_data );

			if( $new_id > 0 ) {
				$status = REQ_SUCCESS;
				$msg = xlang('dist_newuser_ok').'<br>Um email foi enviado confirmando seu cadastro';
				$this->email_boasvindas( $user_data );
			} else {
				$status = REQ_ERROR;
				$msg = xlang('dist_newuser_nok');
			}
		}

		echo json_encode( array('status'=>$status, 'msg'=>$msg) );
	}

	public function modificar() {
		if( !$this->is_user_logged_in ) {
			$this->show_access_error();
		}

		$this->load->helper('image_helper');
		$user_data = $this->usuario_model->get_data( $this->login_data['user_id'] );

		$head_data = array("min_template"=>"image_upload",
			"title"=>"Modificar Usuário");
		$this->load->view('head', $head_data);

		$user_data['action'] = 'update';

		if( !empty($user_data['avatar']) ) {
			$user_data['avatar'] = $user_data['avatar'];
		}

		$this->load->view('user_form', array('data'=>$user_data) );
		$this->load->view('foot');
	}

	public function update() {
		$status = "";
		$msg = "";

		if( !$this->is_user_logged_in ) {
			$status = REQ_AUTH_ERROR;
			$msg = xlang('dist_errsess_expire');
		} else {
			$user_data = $this->input->post(NULL, TRUE);

			$this->sanitize_input( $user_data, array('email', 
				'nome', 'sobrenome', 'nascimento', 'password') );

			$this->load->helper('form');
			$this->load->library('form_validation');

			$this->form_validation->set_error_delimiters('','</br>');

			$this->form_validation->set_rules('nome', 'Nome', 'required|min_length[3]|max_length[120]');
			$this->form_validation->set_rules('sobrenome', 'Sobrenome', 'required|min_length[3]|max_length[40]');
			$this->form_validation->set_rules('nascimento', 'Nascimento', 'required|callback_bday_check');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_email_check');
			$this->form_validation->set_rules('password', 'Senha', 'min_length[6]|max_length[8]');
			$this->form_validation->set_rules('password_2', 'Confirmação de senha', 'matches[password]');

			if ($this->form_validation->run() == FALSE) {
				$status = "ERROR";
				$msg = validation_errors();
			} else {
				$ret_update = $this->usuario_model->update( $user_data, $this->login_data['user_id'] );

				if( $ret_update ) {
					$status = REQ_SUCCESS;
					$msg = xlang('dist_upduser_ok');
				} else {
					$status = REQ_AUTH_ERROR;
					$msg = xlang('dist_upduser_nok');
				}
			}
		}

		echo json_encode( array('status'=>$status, 'msg'=>$msg) );
	}

	public function reset_password() {
		$action = $this->input->post('action', TRUE);
		$msg = ""; $status = "form";

		if( empty($action) ) {
			$action = "do_reset";
			$msg = xlang('dist_resetpw_email');
		} else {
			$email = $this->input->post('email', TRUE);

			if( !$this->usuario_model->email_exists($email) ) {
				$action = "form";
				$status = "error";
				$msg = xlang('dist_resetpw_email_nok');
			} else {
				// let's generate a new password
				// not a very tricky one, but feel free to improve this
				$pwd_len = "8";
				$letters = "abcdefghijklmnopqrstuvwxyz";
				$numbers = "1234567890";

				$letters_len = strlen($letters);
				$numbers_len = strlen($numbers);

				$new_pwd = "";
				for($i=0; $i<$pwd_len-1; $i++) {
					if( $i%2==0 ) {
						$idx = rand(0,$letters_len-1);
						$new_pwd .= $letters[$idx];
					} else {
						$idx = rand(0,$numbers_len-1);
						$new_pwd .= $numbers[$idx];
					}
				}

				if( $this->usuario_model->update_password($email, $new_pwd) ) {
					$status = "success";
					$msg = xlang('dist_resetpw_email_ok');
					$action = "success";

					$this->send_pwd_email($email, $new_pwd);
				} else {
					$status = "error";
					$msg = xlang('dist_resetpw_email_err');
					$action = "form";
				}
			}
		}

		$view_params = array('action'=>$action, 'msg'=>$msg, 'status'=>$status);
		$this->load_iframe('reset_password', $view_params);
	}

	public function email_check( $email ) {
		if( $this->usuario_model->email_exists($email, $this->login_data['user_id']) ) {
			$this->form_validation->set_message('email_check', xlang('dist_upduser_email') );
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function bday_check( $date ) {
		if( empty($date) ) { return TRUE; }

		$date_arr  = @explode('/', $date);

		if ( count($date_arr) == 3) {
		    if (@checkdate($date_arr[1], $date_arr[0], $date_arr[2])) {
		        return TRUE;
		    } else {
				$this->form_validation->set_message('bday_check',
					'Data de Nascimento inválida (formato dd/mm/yyyy)' );
		        return FALSE;
		    }
		} else {
			$this->form_validation->set_message('bday_check',
				'Data de Nascimento inválida (formato dd/mm/yyyy)' );
		    return FALSE;
		}
	}

	private function send_pwd_email($email, $password) {
		$this->load->library('email');
		$this->load->helper('email');

		$corpo = $this->load->view('email_reset_senha',
			array('password'=>$password), TRUE );

		$params = array(
			'to_email'=> $email,
			'from_email'=>'noreply@interessa.org',
			'from_name'=>"Interessa.org",
			'subject'=> "Sua nova senha",
			'body'=> $corpo
		);

		send_email( $params );
	}

	public function ajuda_localizacao() {
		$this->load_ajax('ajuda_localizacao');
	}

	private function email_boasvindas($user_data) {
		$this->load->library('email');
		$this->load->helper('email');

		$email_template = "email_boasvindas_pessoa";

		$corpo = $this->load->view($email_template,
			array('nome'=>$user_data['nome']), TRUE );

		$params = array(
			'to_email'=> $user_data['email'],
			'from_email'=>'noreply@interessa.org',
			'from_name'=> 'Interessa.org',
			'subject'=> 'Bem-vindo ao Interessa',
			'body'=>$corpo
		);

		return send_email( $params );
	}
}
?>
