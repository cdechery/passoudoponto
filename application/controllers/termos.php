<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Termos extends MY_Controller {
	
	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$this->load->view('head', array('title'=>'Termos de Serviço'));
		$this->load->view('termos');
		$this->load->view('foot');
	}
}