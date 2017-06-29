<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tipo_ocorrencia_model extends MY_Model {
	
	public function __construct() {
		parent::__construct();
	}

	public function get_all() {
		$tipos =  $this->db->get('tipo_ocorrencia')->result();
		return $tipos;
	}

	public function get_by_id( $tipo_id ) {
		$tipos = $this->db->get_where('tipo_ocorrencia', array('id'=>$cat_id))->result();
		return $tipos;
	}

}
