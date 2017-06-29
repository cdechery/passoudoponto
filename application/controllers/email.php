<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email extends MY_Controller { 
	public function __construct() {
		parent::__construct();
		$this->load->helper('xerror');
	}

	public function quer_item( $item_id = 0 ) {

		if( !$this->is_user_logged_in ) {
			$this->show_access_error("ajax");
		}

		$this->load->model('item_model');
		$item = $this->item_model->get( $item_id );

		$this->load->model('usuario_model');
		$from_user = $this->usuario_model->get_data( $this->login_data['user_id'] );
		$to_user = $this->usuario_model->get_data( $item['usuario_id'] );

		$tipo_from = $from_user['tipo'];
		$fg_email_pessoa = $to_user['fg_de_pessoa_email'];
		$fg_email_inst = $to_user['fg_de_inst_email'];

		if( ($tipo_from=="I" && $fg_email_inst=='N') ||
			($tipo_from=="P" && $fg_email_pessoa=='N') ) {

			show_error_windowed( 'O usuário optou por não receber mensagens desse tipo',
				200, 'Impossível enviar mensagem', 'ajax');			
		}

		$this->load_ajax('email_item_form',
			array('item'=>$item, 'from_user'=>$from_user,
				'to_user'=>$to_user) );
	}

	public function enviar_quer_item() {
		$this->load->library('email');
		$this->load->helper('email');

		$status = "";
		$msg = "";

		$form_data = $this->input->post(NULL, TRUE);

		$this->load->model('item_model');
		$item = $this->item_model->get( $form_data['item_id'] );

		$assunto = $form_data['assunto'];
		if( empty($assunto) ) {
			$assunto = "Me interessei por um item seu";
		}

		$msg = "<h3>Oi ".$form_data['para_nome'].",</h3>";
		$msg .="o(a) usuário(a) <b>".$form_data['de_nome']."</b> se interessou pelo seu item: <b>".$item['titulo']."</b><br><br>";
		$msg .= "Para entrar em contato com ele(a), basta responder a este email.";
		if( !empty($form_data['corpo']) ) { 
			$msg .= "<br><br>Abaixo a mensagem que ele(a) deixou pra você: <br>";
			$msg .= "<div style='padding-left:2em'>".nl2br($form_data['corpo'])."</div>";
		}
		$corpo = $this->load->view('email_quer_item',
			array('corpo'=>$msg), TRUE);

		$params = array(
			'to_email'=> $form_data['para_email'],
			'to_name'=>$form_data['para_nome'],
			'from_email'=>'noreply@interessa.org',
			'reply_to'=> $form_data['de_email'],
			'from_name'=>$form_data['de_nome']." - Interessa?",
			'subject'=>$assunto,
			'body'=>$corpo
		);

		if( send_email( $params ) ) {
			$status = "OK";
			$msg = "Email enviado com sucesso";
			$this->item_model->update_msg_count( $item['id'] );
		} else {
			$status = "ERROR";
			$msg = "Não foi possível enviar o email";
		}

		echo json_encode( array('status'=>$status, 'msg'=>$msg) );
	}

	public function contato_inst( $inst_id = 0 ) {
		if( !$this->is_user_logged_in ) {
			$this->show_access_error("ajax");
		}

		$this->load->model('usuario_model');

		$from_user = $this->usuario_model->get_data( $this->login_data['user_id'] );
		$to_user = $this->usuario_model->get_data( $inst_id );

		$tipo_from = $from_user['tipo'];
		$fg_email_pessoa = $to_user['fg_de_pessoa_email'];
		$fg_email_inst = $to_user['fg_de_inst_email'];

		if( ($tipo_from=="I" && $fg_email_inst=='N') ||
			($tipo_from=="P" && $fg_email_pessoa=='N') ) {

			show_error_windowed('O usuário optou por não receber mensagens desse tipo',
				200, 'Impossível enviar mensagem', 'ajax');			
		}

		$this->load_ajax('email_contato_inst_form',
			array('from_user'=>$from_user,
				'to_user'=>$to_user) );
	}

	public function enviar_contato_inst() {
		$this->load->library('email');
		$this->load->helper('email');

		$status = "";
		$msg = "";

		$form_data = $this->input->post(NULL, TRUE);

		$this->load->helper('form');
		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('','</br>');

		$this->form_validation->set_rules('assunto', 'Assunto',
			'required|min_length[3]|max_length[40]');
		$this->form_validation->set_rules('corpo', 'Mensagem',
			'required|min_length[20]');

		if ($this->form_validation->run() == FALSE) {
			$status = "ERROR";
			$msg = validation_errors();
		} else {
			$msg = "<h3>Temos um recado para: ".$form_data['para_nome']."</h3>";
			$msg .= "o(a) <b>".$form_data['de_nome']."</b> usou nosso site ".
					"para te mandar uma mensagem, veja abaixo:<br><br>";
			$msg .= "<div style='padding-left:2em'>".nl2br($form_data['corpo'])."</div>";
			$corpo = $this->load->view('email_contato_inst',
				array('corpo'=>$msg), TRUE);

			$params = array(
				'to_email'=> $form_data['para_email'],
				'to_name'=>$form_data['para_nome'],
				'from_email'=>'noreply@interessa.org',
				'from_name'=>$form_data['de_nome']." - Interessa?",
				'reply_to'=> $form_data['de_email'],
				'subject'=>$form_data['assunto'],
				'body'=>$corpo
			);

			if( send_email($params) ) {
				$status = "OK";
				$msg = "Email enviado com sucesso";
			} else {
				$status = "ERROR";
				$msg = "Não foi possível enviar o email, tente mais tarde";
			}
		}

		echo json_encode( array('status'=>$status,'msg'=>$msg) );
	}

}