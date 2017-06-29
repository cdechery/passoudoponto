<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Exceptions extends CI_Exceptions {

	private $CI;
	private $params;

	public function __construct() {
		parent::__construct();
		$this->CI =& get_instance();
		$this->params = $this->CI->config->item('site_params');
	}

	function show_error($heading, $message,
		$template = 'error_general', $status_code = 500) {

		set_status_header($status_code);

		if( $heading==NULL ) {
			$heading = $this->params['erro_generico'];
		}

		$head_data = array("title"=>$this->params['titulo_site'].": Erro");
		$head = $this->CI->load->view('head', $head_data, TRUE);

		$err_body = $this->CI->load->view('error',
			array('message'=>$message, 'heading'=>$heading), TRUE);
		$foot = $this->CI->load->view('foot', TRUE);
		
		if (ob_get_level() > $this->ob_level + 1) {
			ob_end_flush();
		}
		ob_start();

		echo $head;
		echo $err_body;
		echo $foot;

		$buffer = ob_get_contents();
		
		ob_end_clean();
		
		return $buffer;
	}

	function show_error_windowed($heading, $message,
		$template = 'error_general', $status_code = 500, $type="ajax") {

		set_status_header($status_code);

		if( $heading==NULL ) {
			$heading = $this->params['erro_generico'];
		}

		$head_data = array("title"=>$this->params['titulo_site'].": Erro");
		$head = $this->CI->load->view($type.'_head', $head_data, TRUE);

		$err_body = $this->CI->load->view('error_windowed',
			array('message'=>$message, 'heading'=>$heading), TRUE);
		
		if (ob_get_level() > $this->ob_level + 1) {
			ob_end_flush();
		}
		ob_start();

		echo $head;
		echo $err_body;

		$buffer = ob_get_contents();
		
		ob_end_clean();
		
		return $buffer;
	}
}
?>