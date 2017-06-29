<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mapa_model extends MY_Model {
	
	public function __construct() {
		parent::__construct();
	}

	public function get_all() {

		$this->db->select('o.id, o.data_hora, t.id tipo_id, t.nome, o.num_onibus,
			t.icone, u.id usuario_id, u.nome, u.avatar, o.lat, o.lng', FALSE);
		$this->db->from('ocorrencia o');
		$this->db->join('tipo_ocorrencia t', 't.id = o.tipo_ocorrencia_id');
		$this->db->join('usuario u', 'u.id = o.usuario_id');
		$this->db->order_by('u.id', 'asc');

		return $this->db->get()->result();
	}
}
