<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Interesse extends MY_Controller { 
	
	public function __construct() {
		parent::__construct();
		$this->load->model('interesse_model');
	}

	public function get_single($user_id, $categoria_id) {
		$int = $this->interesse_model->get($user_id, $categoria_id);
		$this->load->view( 'interesse_single', array('interesse'=> $int) );
	}

	public function insert() {
		$status = "";
		$msg = "";
		$new_id = 0;

		$inter_data = $this->input->post(NULL, TRUE);

		$this->load->helper('form');
		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('','</br>');

		$this->form_validation->set_rules('categ', 'Categoria', 'required');
		//$this->form_validation->set_rules('raio', 'Raio de Busca', 'required');

		if ($this->form_validation->run() == FALSE) {
			$status = "ERROR";
			$msg = validation_errors();
		} else {
			$existente = $this->interesse_model->get( $this->login_data['user_id'],
				$inter_data['categ'] );

			if( $existente ) {
				$status = "ERROR";
				$msg = "Já existe um Interesse para esta Categoria";
				echo json_encode( array('status'=>$status, 'msg'=>$msg) );
				return;
			}

			$inter_data['user_id'] = $this->login_data['user_id'];

			if( $this->interesse_model->insert( $inter_data ) ) {
				$status = "OK";
				$msg = 'O Interesse foi incluído com sucesso';
			} else {
				$status = "ERROR";
				$msg = 'Não foi possível incluir o interesse';
			}
		}

		echo json_encode( array('status'=>$status, 'msg'=>$msg,
			'user'=>$this->login_data['user_id'],
			'cat'=>$inter_data['categ']) );
	}

	public function activate( $categoria_id ) {
		$status = "";
		$msg = "";

		$msg = $this->check_owner($this->interesse_model,$categoria_id);
		if( $msg ) {
			$status = "error";
			echo json_encode( array('status'=>$status, 'msg'=>$msg) );
			return;
		}

		$user_id = $this->login_data['user_id'];

		if( $this->interesse_model->activate($categoria_id, $user_id) ) {
			$status = "success";
			$msg = "O Interesse foi ativado com sucesso";
		} else {
			$status = "error";
			$msg = "Ocorreu uma falha ao ativar o Interesse, tente novamente";
		}

		echo json_encode( array('status'=>$status, 'msg'=>$msg) );
	}

	public function deactivate( $categoria_id ) {
		$status = "";
		$msg = "";

		$msg = $this->check_owner($this->interesse_model,$categoria_id);
		if( $msg ) {
			$status = "error";
			echo json_encode( array('status'=>$status, 'msg'=>$msg) );
			return;
		}

		$user_id = $this->login_data['user_id'];

		if( $this->interesse_model->deactivate($categoria_id, $user_id) ) {
			$status = "success";
			$msg = "O Interesse foi desativado com sucesso";
		} else {
			$status = "error";
			$msg = "Ocorreu uma falha ao desativar o Interesse, tente novamente";
		}

		echo json_encode( array('status'=>$status, 'msg'=>$msg) );
	}

	public function update( $categoria_id, $raio ) {
		$status = "";
		$msg = "";

		$msg = $this->check_owner($this->interesse_model,$categoria_id);
		if( $msg ) {
			$status = "error";
			echo json_encode( array('status'=>$status, 'msg'=>$msg) );
			return;
		}

		$inter_data = array('user_id'=>$this->login_data['user_id'],
			'cat_id'=> $categoria_id,
			'raio'=>$raio );

		if( $this->interesse_model->update( $inter_data ) ) {
			$status = "success";
			$msg = 'O Interesse foi atualizado com sucesso';
		} else {
			$status = "error";
			$msg = 'Não foi possível atualizar o interesse';
		}

		echo json_encode( array('status'=>$status, 'msg'=>$msg) );
	}

	public function purge_old() {
		$this->require_auth();

		header('Content-Type: text/plain');

		$this->load->library('email');
		$this->load->helper('email');

		output_log('Iniciando processo de limpeza de Interesses');

		$dias_pessoa = $this->params['validade_interesse_pessoa'];
		$dias_inst = $this->params['validade_interesse_inst'];

		$old_ints = $this->interesse_model->get_old( $dias_pessoa, $dias_inst );
		output_log('Foram encontrados '.count($old_ints).' expirados');

		if( count($old_ints)==0 ) {
			output_log('Nada a fazer. Terminando processo!');
			return;
		}

		$categorias = array();
		$user_id = $cat_id = 0;
		$nome = $email = "";

		output_log('Processando notificacoes e exclusao de Interesses');
		$fg_notif = "";
		foreach ($old_ints as $int) {

			if( $user_id!=0 && $user_id!=$int->usuario_id ) {
				if( $fg_notif ) {
					$this->notify_delete($email, $categorias, $nome);
				}
				$categorias = array();
			}

			$fg_notif = $int->fg_geral_email=='S';

			$categorias[] = $int->nome_cat;
			$user_id = $int->usuario_id;
			$cat_id = $int->categoria_id;
			$nome = $int->nome_usuario;
			$email = $int->email;

			$this->interesse_model->delete($cat_id, $user_id);
		}

		if( $fg_notif ) {
			$this->notify_delete($email, $categorias, $nome);
		}

		output_log('Fim do processo de exclusao de Interesses');
	}

	private function notify_delete($para, $cats, $nome) {

		$corpo = $this->load->view('email_notif_interesses',
			array('categorias'=>$cats, 'nome'=>$nome), TRUE );

		$params = array(
			'to_email'=> $para,
			'from_email'=>'noreply@interessa.org',
			'from_name'=> 'Interessa.org',
			'subject'=> 'Interesses expirados',
			'body'=>$corpo
		);

		return send_email( $params );
	}
} // Image class
