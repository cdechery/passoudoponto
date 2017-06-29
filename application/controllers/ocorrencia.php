<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ocorrencia extends MY_Controller {
	
	public function __construct() {
		parent::__construct();
		$this->load->model('ocorrencia_model');
		$this->load->helper('xlang');
		$this->load->helper('cookie');
	}

	public function novo() {
		$this->load->model('tipo_ocorrencia_model');
		$tipos = $this->tipo_ocorrencia_model->get_all();
		$this->load->view('test_form.php', array('tipos'=>$tipos) );
	}

	public function insert() {
		$status = "";
		$msg = "";
		$new_id = 0;

		$oc_data = $this->input->post(NULL, TRUE);

		$this->sanitize_input($oc_data, array('tipo', 'usuario_id',
			'lat', 'lng', 'nr_onibus', 'nr_ordem') );

		$this->load->helper('form');
		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('','</br>');

		$this->form_validation->set_rules('tipo', 'Tipo de Ocorrência', 'required');
		$this->form_validation->set_rules('usuario_id', 'Usuário', 'required');
		$this->form_validation->set_rules('lat', 'Localização', 'required');
		$this->form_validation->set_rules('nr_onibus', 'Número do Ônibus', 'required');

		if ($this->form_validation->run() == FALSE) {
			$status = "ERROR";
			$msg = validation_errors();
		} else {
			$this->load->library('googlemaps');
			$get_addr = $this->googlemaps->get_address_from_lat_long($oc_data['lat'], 
				$oc_data['lng']);

			if( $get_addr['status']=="OK" ) {
				$oc_data['logradouro'] = $get_addr['address'][0];
				$oc_data['bairro'] = $get_addr['address'][1];
			}

			$new_id = $this->ocorrencia_model->insert( $oc_data );

			if( $new_id > 0 ) {
				$status = REQ_SUCCESS;
				$msg = 'Ocorrência incluída com sucesso!';
			} else {
				$status = REQ_ERROR;
				$msg = 'Ocoreu um erro ao incluir a Ocorrência';
			}
		}

		echo json_encode( array('status'=>$status, 'msg'=>$msg) );
	}

	public function get( $ocorrencia_id = 0 ) {
		$status = $msg = $data = "";
		$oc = $this->ocorrencia_model->get( $ocorrencia_id );

		if( $oc ) {
			$status = REQ_SUCCESS;
		} else {
			$status = REQ_ERROR;
			$msg = "Ocorrência não encontrada ou inválida";
		}

		echo json_encode( array('status'=>$status, 'msg'=>$msg,
			'data'=>$oc) );

	}

	public function update() {
		$status = "";
		$msg = "";

		$oc_data = $this->input->post(NULL, TRUE);

		$this->sanitize_input($oc_data, array('tipo', 'usuario_id',
			'lat', 'lng', 'nr_onibus', 'nr_ordem', 'id') );
		$oc_data['usuario_id'] = $this->login_data['user_id'];

		$ret = $this->check_owner($this->ocorrencia_model, $oc_data['id']);
		if( $ret[0] ) {
			echo json_encode( array('status'=>$ret[0], 'msg'=>$ret[1]) );
			return;
		}

		$this->load->helper('form');
		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('','</br>');

		$this->form_validation->set_rules('id', 'Ocorrência', 'required');
		$this->form_validation->set_rules('tipo', 'Tipo de Ocorrência', 'required');
		$this->form_validation->set_rules('nr_onibus', 'Número do Ônibus', 'required');

		if ($this->form_validation->run() == FALSE) {
			$status = "ERROR";
			$msg = validation_errors();
		} else {
			$new_id = $this->ocorrencia_model->update( $oc_data );

			if( $new_id > 0 ) {
				$status = REQ_SUCCESS;
				$msg = 'Ocorrência atualizada com sucesso!';
			} else {
				$status = REQ_ERROR;
				$msg = 'Ocoreu um erro ao atualizar a Ocorrência';
			}
		}

		echo json_encode( array('status'=>$status, 'msg'=>$msg) );
	}

	public function delete( $ocorrencia_id = 0 ) {
		$status = "";
		$msg = "";

		$ret = $this->check_owner($this->ocorrencia_model, $ocorrencia_id);
		if( $ret[0] ) {
			echo json_encode( array('status'=>$ret[0], 'msg'=>$ret[1]) );
			return;
		}

		$this->load->model('foto_model');
		$fotos = $this->foto_model->get_ocorrencia_fotos( $ocorrencia_id );
		if( $fotos ) {
			foreach( $fotos as $row ) {
				@$this->foto_model->delete( $row->id );
			}
		}

		if( $this->ocorrencia_model->delete($ocorrencia_id) ) {
			$status = REQ_SUCCESS;
			$msg = "Ocorrência excluida com sucesso!";
		} else {
			$status = REQ_ERROR;
			$msg = "Não foi possível excluir a Ocorrência";
		}

		echo json_encode( array('status'=>$status, 'msg'=>$msg) );
	}

	public function map_infowindow( $ocorrencia_id ) {
		$this->load->model('ocorrencia_model');
		$oc = $this->ocorrencia_model->get( $ocorrencia_id );

		$this->load->view('ocorrencia_infowindow', array('oc'=>$oc) );

	}

	public function get_all() {
		$status = $msg = $data = "";

		$this->load->model('mapa_model');
		$map_result = $this->mapa_model->get_all();
		if( $map_result ) {
			$status = REQ_SUCCESS;
		} else {
			$status = REQ_ERROR;
			$msg = "Erro ao obter Ocorrências";
		}

		echo json_encode( array('status'=>$status, 'msg'=>$msg,
			'data'=>$map_result) );
	}

	public function ref_get_tipos() {
		$status = $msg = $data = "";

		$this->load->model('tipo_ocorrencia_model');
		$res = $this->tipo_ocorrencia_model->get_all();
		if( $res ) {
			$status = REQ_SUCCESS;
		} else {
			$status = REQ_ERROR;
			$msg = "Erro ao obter Tipos de Ocorrência";
		}

		echo json_encode( array('status'=>$status, 'msg'=>$msg,
			'data'=>$res) );
	}

}
