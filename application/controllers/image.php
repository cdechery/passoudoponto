<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Image extends MY_Controller { 
	
	public function __construct() {
		parent::__construct();
		$this->load->model('image_model');
		$this->load->helper('xlang');
	}

	public function update_item_image() {
		$this->upload_item_image(FALSE, TRUE);
	}

	public function upload_temp_item_image() {
		$this->upload_item_image(TRUE, FALSE);
	}

	public function upload_item_image($istemp=FALSE, $isupdate=FALSE) {
		$status = "";
		$msg = "";
		$file_id = "";

		if( !$this->is_user_logged_in ) {
			$status = "error";
			$msg = xlang('dist_errsess_expire');
			echo json_encode(array('status' => $status,
				'msg' => $msg, "file_id" => $file_id) );
			return;
		}

		$input = $this->input->post(NULL);
		$file_element_name = $input['file_tag_name'];

		$config['upload_path'] = $this->params['upload']['path'];
		$config['allowed_types'] = implode("|",$this->params['image_settings']['allowed_types']);
		$config['max_size']  = $this->params['upload']['max_size'];
		$config['encrypt_name'] = TRUE;
		
		$this->load->library('upload', $config);
		
		$min_image_size = $this->params['image_settings']['min_image_size'];

		if ( !$this->upload->do_upload( $file_element_name ) ) 	{
			$status = "error";
			$msg = $this->upload->display_errors('','');
		} else {
			$udata = $this->upload->data();

			if( $udata['image_height'] < $min_image_size || $udata['image_width'] < $min_image_size ) {
				$status="error";
				$msg = xlang('dist_min_image_size', $min_image_size);
			} else {
				$thumbSizes = $this->input->post('thumbs');
				if( !empty($thumbSizes) ) {
					$thumbSizes = explode("|", $thumbSizes );
				}

				$image_data = array('descricao'=>'');
				if( $isupdate ) {
					$image_data['item_id'] = $input['id'];
					$image_data['id'] = $input['img_id'];
					$file_id = $this->image_model->update( $udata,
						$image_data, $thumbSizes );

					$file_id = ($file_id)?$input['img_id']:false;
				} else if( $istemp ) {
					$image_data['item_id'] = 0;
					$image_data['temp_id'] = $input['temp_id'];
					$file_id = $this->image_model->insert( $udata,
						$image_data, $thumbSizes );
				} else {
					$image_data['item_id'] = $input['id'];
					$file_id = $this->image_model->insert( $udata,
						$image_data, $thumbSizes );
				}

				if( $file_id ) {
					$status = "success";
					$msg = xlang('dist_imgupload_ok');
				} else {
					@unlink( $udata['full_path'] );
					$status = "error";
					$msg = xlang('dist_imgupload_nok');
				}
			}
		}

		echo json_encode( array('status' => $status, 'msg' => $msg,
			"file_id" => $file_id) );
	} // upload_marker_imagem

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

	// public function delete_image( $id ) {
	// 	if( !$this->is_user_logged_in ) {
	// 		$status = "error";
	// 		$msg = xlang('dist_errsess_expire');
	// 		echo json_encode( array('status' => $status, 'msg' => $msg) );
	// 		return;
	// 	}

	// 	$status = $msg = "";
		
	// 	if( !$this->image_model->delete( $id ) ) {
 // 			$status = 'error';
 //      		$msg = xlang('dist_imgdel_nok');
	// 	} else {
	// 		$status = "success";
	// 		$msg = xlang('dist_imgdel_ok');
	// 	}
	// 	echo json_encode( array('status' => $status,
	// 		'msg' => $msg) );
	// }

	public function get_image($image_id) {
		$image_data = $this->image_model->get_by_id($image_id);
		$retJson = array('id'=>$image_data->id,
			'nome_arquivo'=>$image_data->nome_arquivo,
			'item_id'=>$image_data->item_id);

		$thumbSizes = $this->params['image_settings']['thumb_sizes'];
		foreach ($thumbSizes as $size) {
			$retJson['thumb'.$size] = thumb_filename($image_data->nome_arquivo, $size); 
		}

		echo json_encode( $retJson );
	}

} // Image class