<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ocorrencia_model extends MY_Model {
	
	public function __construct() {
		parent::__construct();
		$this->table = "ocorrencia";
	}

	public function get( $ocorrencia_id ) {
		$this->db->select('o.id, tpo.nome, tpo.icone,
			o.usuario_id, o.lat, o.lng, o.num_onibus, o.num_ordem,
			date_format(o.data_hora, \'%d/%m/%Y %H:%i:%S\') as dthr_format', FALSE);
		$this->db->from('ocorrencia o');
		$this->db->join('tipo_ocorrencia tpo', 'tpo.id = o.tipo_ocorrencia_id');
		$this->db->where('o.id', $ocorrencia_id);

		return $this->db->get()->row_array();
	}

	public function get_list( $ocorrencia_ids ) {
		$this->db->select('o.id, tpo.nome, tpo.icone, o.num_onibus, o.num_ordem,
			o.usuario_id, o.lat, o.lng, f.id foto_id, f.nome_arquivo,
			date_format(o.data_hora, \'%d/%m/%Y\') as dthr_format', FALSE);
		$this->db->from('ocorrencia o');
		$this->db->join('foto f', 'o.id = f.ocorrencia_id', 'left');
		$this->db->join('tipo_ocorrencia tpo', 'tpo.id = o.tipo_ocorrencia_id');
		$this->db->group_by('o.id');
		$this->db->order_by('o.data_hora', 'desc');
		$ocorrencias = $this->db->get()->result();

		return $ocorrencias;
	}

	public function get_user_ocorrencias( $usuario_id ) {
		$this->db->select('o.id, tpo.nome, tpo.icone, o.lat, o.lng, f.id foto_id, 
			f.nome_arquivo, o.num_onibus, o.num_ordem,
			date_format(o.data_hora, \'%d/%m/%Y\') as dthr_format', FALSE);
		$this->db->from('ocorrencia o');
		$this->db->join('foto f', 'o.id = f.ocorrencia_id', 'left');
		$this->db->join('tipo_ocorrencia tpo', 'tpo.id = o.tipo_ocorrencia_id');
		$this->db->where('o.usuario_id', $usuario_id);
		$this->db->group_by('o.id');
		$this->db->order_by('o.data_hora', 'desc');
		$ocorrencias = $this->db->get()->result();

		return $ocorrencias;
	}

	public function get_nulladdr_ocorrencias(  ) {
		$this->db->select('o.id, o.lat, o.lng, o.tipo_ocorrencia_id, o.num_onibus, o.num_ordem');
		$this->db->from('ocorrencia o');
		$this->db->where('o.bairro', NULL);
		$this->db->or_where('o.bairro', '');
		$this->db->group_by('o.id');
		$ocorrencias = $this->db->get()->result();

		return $ocorrencias;
	}

	public function insert( $oc_data ) {
		$insert_data = array(
			'lat' => $oc_data['lat'],
			'lng' => $oc_data['lng'],
			'tipo_ocorrencia_id' => $oc_data['tipo'],
			'usuario_id' => $oc_data['usuario_id'],
			'num_onibus' => $oc_data['nr_onibus'],
			'num_ordem' => $oc_data['nr_ordem']
		);

		if( !empty($oc_data['bairro']) ) {
			$insert_data['bairro'] = $oc_data['bairro'];
			$insert_data['logradouro'] = $oc_data['logradouro'];
		}

		$this->db->set('data_hora', 'NOW()', false);

		if( $this->db->insert('ocorrencia', $insert_data ) ) {
			return $this->db->insert_id();
		} else {
			return 0;
		}
	}

	public function delete( $ocorrencia_id ) {
		if( empty($ocorrencia_id) ) {
			return false;
		};

		$this->load->model('foto_model');
		$fotos = $this->foto_model->get_ocorrencia_fotos( $ocorrencia_id );
		foreach ($fotos as $foto) {
			$this->foto_model->delete( $foto->id );
		}

		if ($this->db->delete('ocorrencia', array('id'=>$ocorrencia_id))) {
			return true;
		} else {
			return false;
		}
	}

	public function update( $oc_data ) {
		$upd_data = array(
			'tipo_ocorrencia_id' => $oc_data['tipo'],
			'num_onibus' => $oc_data['nr_onibus'],
			'num_ordem' => $oc_data['nr_ordem']
		);

		if( !empty($oc_data['lat']) && !empty($oc_data['lng']) ) {
			$upd_data['lat'] = $oc_data['lat'];
			$upd_data['lng'] = $oc_data['lng'];
		}

		if( !empty($oc_data['bairro']) ) {
			$upd_data['bairro'] = $oc_data['bairro'];
			$upd_data['logradouro'] = $oc_data['logradouro'];
		}

		if( $this->db->update('ocorrencia', $upd_data,
			array('id'=>$oc_data['id']) ) ) {
			return true;
		} else {
			return false;
		}
	}
}
