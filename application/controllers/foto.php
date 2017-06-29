<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Foto extends MY_Controller { 
	
	public function __construct() {
		parent::__construct();
		$this->load->model('foto_model');
		$this->load->helper('xlang');
	}

	public function test() {
		$this->load->view('test_upload');
	}

	public function get( $foto_id = 0 ) {
		$status = $msg = $data = "";

		$foto_data = $this->foto_model->get_by_id( $foto_id );
		if( $foto_data ) {
			$data = array('id'=>$foto_data->id,
				'nome_arquivo'=>$foto_data->nome_arquivo,
				'ocorrencia_id'=>$foto_data->ocorrencia_id);

			$thumbSizes = $this->params['image_settings']['thumb_sizes'];
			foreach ($thumbSizes as $size) {
				$data['thumb'.$size] = thumb_filename($foto_data->nome_arquivo, $size); 
			}
			$status = REQ_SUCCESS;
		} else {
			$status = REQ_ERROR;
			$msg = "Foto inválida ou não encontrada";
		}

		echo json_encode( array('status' => $status,
			'msg' => $msg, "data" => $data) );
	}

	public function ocorrencia( $ocorrencia_id = 0 ) {
		$status = $msg = ""; $final_data = array();

		if( $ocorrencia_id == 0 ) {
			$status = REQ_ERROR;
			$msg = "Ocorrência inválida ou não informada";
			echo json_encode( array('status' => $status,
				'msg' => $msg, "data" => $final_data) );
			return;
		}

		$status = REQ_SUCCESS;
		$fotos = $this->foto_model->get_ocorrencia_fotos( $ocorrencia_id );
		if( $fotos ) {
			foreach( $fotos as $row ) {
				$data = array('id'=>$row->id, 'nome_arquivo'=>$row->nome_arquivo);

				$thumbSizes = $this->params['image_settings']['thumb_sizes'];
				foreach ($thumbSizes as $size) {
					$data['thumb'.$size] = thumb_filename($row->nome_arquivo, $size); 
				}
				$final_data[] = $data;

			}
			$msg = "Encontradas ".count($fotos)." fotos";
		} else {
			$msg = "Sem fotos para essa Ocorrência";
		}

		echo json_encode( array('status' => $status,
			'msg' => $msg, "data" => $final_data) );
	}

	public function upload() {
		$status = "";
		$msg = "";
		$foto_id = "";

		$input = $this->input->post(NULL);

		if( empty($input['id']) ) {
			$status = REQ_ERROR;
			$msg = "ID de Ocorrência inválido";
			echo json_encode( array('status'=>$status, 'msg'=>$msg) );
			return;
		}

		$oc_id = $input['id'];
		$this->load->model('ocorrencia_model');
		$ret = $this->check_owner($this->ocorrencia_model, $oc_id );
		if( $ret[0] ) {
			echo json_encode( array('status'=>$ret[0], 'msg'=>$ret[1]) );
			return;
		}

		$config['upload_path'] = $this->params['upload']['path'];
		$config['allowed_types'] = implode("|",$this->params['image_settings']['allowed_types']);
		$config['max_size']  = $this->params['upload']['max_size'];
		$config['encrypt_name'] = TRUE;
		
		$count = $this->foto_model->get_oc_foto_count( $oc_id );
		$max_fotos = $this->params['max_ocorrencia_fotos'];
		if( $count >= $max_fotos ) {
			$status = REQ_ERROR;
			$msg = "Máximo de fotos (".$max_fotos.") por Ocorrência atingido";
			echo json_encode( array('status'=>$status, 'msg'=>$msg) );
			return;
		}

		$this->load->library('upload', $config);
		
		$min_image_size = $this->params['image_settings']['min_image_size'];
		$file_path = "";

		if ( !$this->upload->do_upload() ) 	{
			$status = REQ_ERROR;
			$msg = $this->upload->display_errors('','');
		} else {
			$udata = $this->upload->data();
			$file_path = $udata['full_path'];

			if( $udata['image_height'] < $min_image_size ||
				$udata['image_width'] < $min_image_size ) {
				
				$status= REQ_ERROR;
				$msg = xlang('dist_min_image_size', $min_image_size);
			} else {
				$thumbSizes = $this->params['image_settings']['thumb_sizes'];

				$udata['id'] = $oc_id;
				$foto_id = $this->foto_model->insert( $udata, $thumbSizes );

				if( $foto_id ) {
					$status = REQ_SUCCESS;
					$msg = xlang('dist_imgupload_ok');
				} else {
					$status = REQ_ERROR;
					$msg = xlang('dist_imgupload_nok');
				}
			}
		}

		if( $status != REQ_SUCCESS ) {
			@unlink( $file_path );
		}

		echo json_encode( array('status' => $status,
			'msg' => $msg, "foto_id" => $foto_id) );
	} // upload_ocorrencia_foto

	public function upload_avatar() {

		if( !$this->is_user_logged_in ) {
			$status = "error";
			$msg = xlang('dist_errsess_expire');
			echo json_encode( array('status' => $status, 'msg' => $msg) );
			return;
		}

		$this->load->model('usuario_model');

		$status = "";
		$msg = "";
		$img_src = "";

		$file_element_name = 'userfile';

		$upload_path = $this->params['upload']['path'];

		$config['upload_path'] = $upload_path;
		$config['allowed_types'] = implode("|",$this->params['image_settings']['allowed_types']);
		$config['max_size']  = $this->params['upload']['max_size'];
		$config['encrypt_name'] = TRUE;
		
		$this->load->library('upload', $config);
		
		$min_image_size = $this->params['image_settings']['min_image_size'];

		if ( !$this->upload->do_upload( $file_element_name ) ) 	{
			$status = "error";
			$msg = $this->upload->display_errors('','');
		} else {
			$upload_data = $this->upload->data();

			if( $upload_data['image_height']< $min_image_size ||
					$upload_data['image_width']< $min_image_size ) {
				$status="error";
				$msg = xlang('dist_min_image_size', $min_image_size);
			} else {
				$thumbSizes = $this->input->post('thumbs');
				if( !empty($thumbSizes) ) {
					$thumbSizes = explode("|", $thumbSizes );
				}

				$user_id = $this->login_data['user_id'];

				if( $this->usuario_model->update_avatar( $upload_data, $user_id, $thumbSizes ) ) {
					$status = "success";
					$msg = xlang('dist_imgupload_ok');
					$img_src = base_url().$upload_path.thumb_filename($upload_data['file_name'], 200);
				} else {
					@unlink( $data['full_path'] );
					$status = "error";
					$msg = xlang('dist_imgupload_nok');
				}
			}
		}

		echo json_encode(array('status' => $status, 'msg' => $msg, "img_src" => $img_src) );
	} // upload_marker_imagem

	public function delete( $ocorrencia_id = 0, $foto_id = 0 ) {
		$status = $msg = "";

		$this->load->model('ocorrencia_model');
		$ret = $this->check_owner($this->ocorrencia_model, $ocorrencia_id);
		if( $ret[0] ) {
			$status = $ret[0];
			$msg = $ret[1];
		} else {
			if( !$this->foto_model->delete( $foto_id ) ) {
				$status = REQ_ERROR;
				$msg = "Erro ao excluir a foto [".$foto_id."]";
			} else {
	 			$status = REQ_SUCCESS;
	      		$msg = "Foto excluída com sucesso";
			}
		}
		
		echo json_encode( array('status' => $status,
			'msg' => $msg) );
	}

} // Foto class