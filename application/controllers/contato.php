<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contato extends MY_Controller {
	
	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$this->load->view('head', array('title'=>'Contato'));
		$this->load->view('contato');
		$this->load->view('foot');
	}

	public function enviar() {
		
		$this->load->library('email');
		$this->load->helper('email');
		
		$form_data = $this->input->post(NULL, TRUE);
		
		$this->load->helper('form');
		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('','</br>');

		$this->form_validation->set_rules('assunto', 'Assunto',	'required');
		$this->form_validation->set_rules('corpo', 'Mensagem',
			'required|min_length[20]');

		if ($this->form_validation->run() == FALSE) {
			$status = "ERROR";
			$msg = validation_errors(); 
		} else {
			$corpo = $this->load->view('email_contato',
				array('body'=>$form_data), TRUE);

			$params = array(
				'to_email'=> 'webmaster@interessa.org',
				'to_name'=>'Interessa.org',
				'from_email'=>$form_data['email'],
				'from_name'=>$form_data['nome'],
				'subject'=>$form_data['assunto'],
				'body'=>$corpo
			);

			if( send_email( $params ) ) {
				$status = "OK";
				$msg = "Email enviado com sucesso";
			} else {
				$status = "ERROR";
				$msg = "Não foi possível enviar o email";
			}
		}

		echo json_encode( array('status'=>$status, 'msg'=>$msg) );
	}
}