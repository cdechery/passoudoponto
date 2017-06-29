<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sobre extends MY_Controller {
	
	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$this->load->view('head', array('title'=>'Sobre o Interessa.org'));
		$this->load->view('sobre');
		$this->load->view('foot');
	}
}