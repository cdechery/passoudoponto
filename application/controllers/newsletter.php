<?php if ( !defined('BASEPATH')) exit('No direct script access allowed');

class Newsletter extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->require_auth();
	}

	public function index() {
		$this->load->view('newsletter');
	}

	public function enviar() {
		$time_start = microtime(true);

		$this->load->library('email');
		$this->load->helper('email');

		$this->load->model('usuario_model');

		$msg = $this->input->post('msg');
		$assunto = $this->input->post('assunto');

		$list = $this->usuario_model->get_emails_newsletter();

		$count = 0;
		$countErr = 0;
		foreach ($list as $dest) {
			$corpo = "<h3>Olá ".$dest->nome.",</h3>";
			$corpo .= nl2br($msg);
			$corpo .= "<p>Um abraço,<br>Equipe Interessa.org";

			$params = array(
				'to_email'=> $dest->email,
				'to_name'=> $dest->nome,
				'from_email'=> 'noreply@interessa.org',
				'from_name'=> 'Interessa.org',
				'subject'=>$assunto,
				'body'=>$corpo
			);

			if( send_email( $params ) ) {
				$count++;
			} else {
				$countErr++;
			}
		}

		$time_end = microtime(true);
		$time = $time_end - $time_start;

		echo "<h1>Foram enviados ".$count." emails. Ocorreu erro em ".$countErr." emails</h1>";
		echo "Tempo de execução: ".$time."s";
	}
}