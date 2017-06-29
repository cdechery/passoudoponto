<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Item extends MY_Controller { 

	public function __construct() {
		parent::__construct();
		$this->load->model('item_model');
		$this->load->helper('xlang');
	}

	public function get_single($item_id) {
		$item = $this->item_model->get( $item_id );
		$imagens = $this->get_images( $item_id );

		$arrImgs = array();
		foreach ($imagens as $img) {
			$arrImgs[] = $img->nome_arquivo;
		}

		// converto o result de array para objeto
		$itemObj = json_decode(json_encode($item), FALSE);

		$this->load->view( 'item_single', array('data'=>$itemObj, 
			'imagens'=>$arrImgs) );

	}

	public function get_categorias() {
		$this->load->model('categoria_model');
		$categorias = $this->categoria_model->get_all();
		$this->load_ajax('categorias', array('categorias'=>$categorias ));
	}

	public function novo() {
		if( !$this->is_user_logged_in ) {
			$this->show_access_error();
		}

		$this->load->model('categoria_model');
		$categorias = $this->categoria_model->get_all();

		$this->load->model('situacao_model');
		$situacoes = $this->situacao_model->get_all();

		$head_data = array('min_template'=>'image_upload', "title"=>"Novo Item");
		$this->load->view('head', $head_data);

		$temp_id = $this->item_model->get_temp_id($this->login_data['user_id']);

		$data = array('action' => 'insert',
			'temp_id'=>$temp_id,
			'situacoes'=>$situacoes,
			'categorias'=>$categorias);
		$this->load->view('item_form', array('data'=>$data) );

		$this->load->view('foot');
	}

	public function delete($item_id) {
		if( $this->item_model->delete( $item_id ) ) {
			$status = "OK";
			$msg = 'O Item foi removido com sucesso';
		} else {
			$status = "ERROR";
			$msg = 'Não foi possível remover o Item';
		}
		echo json_encode(array('status' => $status, 'msg' => $msg) );
	}

	public function insert() {
		$status = "";
		$msg = "";
		$new_id = 0;

		if( !$this->is_user_logged_in ) {
			$status = "ERROR";
			$msg = xlang('dist_errsess_expire');
			echo json_encode(array('status' => $status,
				'msg' => $msg) );
			return;
		}

		$input = $this->input->post(NULL, TRUE);

		$this->load->helper('form');
		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('','</br>');

		$this->form_validation->set_rules('titulo', 'Título',
			'required|min_length[10]|max_length[70]');
		$this->form_validation->set_rules('desc', 'Descrição',
			'required|min_length[10]|max_length[250]');
		$this->form_validation->set_rules('categ', 'Categoria', 'required');
		$this->form_validation->set_rules('sit', 'Situação', 'required');

		$item_data['usuario_id'] = $this->login_data['user_id'];

		if ($this->form_validation->run() == FALSE) {
			$status = "ERROR";
			$msg = validation_errors();
		} else {

			$new_id = $this->item_model->insert( $input );
			if( $new_id ) {
				$this->load->model('image_model');
				$this->load->model('notificacao_model');

				@$this->image_model->move_temp_images($item_data['usuario_id'],
					$new_id, $input['temp_id'] );

				@$this->notificacao_model->insert_item( $new_id );

				$status = "OK";
				$msg = 'O Item foi incluído com sucesso';
			} else {
				$status = "ERROR";
				$msg = 'Não foi possível incluir o Item';
			}
		}

		echo json_encode( array('status'=>$status,
			'user_id'=>$item_data['usuario_id'],
			'msg'=>$msg, 'item_id' => $new_id) );
	}

	public function update() {
		$status = "";
		$msg = "";

		if( !$this->is_user_logged_in ) {
			$status = "ERROR";
			$msg = xlang('dist_errsess_expire');
			echo json_encode(array('status' => $status,
				'msg' => $msg) );
			return;
		}

		$input = $this->input->post(NULL, TRUE);

		$this->load->helper('form');
		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('','</br>');

		$this->form_validation->set_rules('titulo', 'Título',
			'required|min_length[10]|max_length[70]');
		$this->form_validation->set_rules('desc', 'Descrição',
			'required|min_length[10]|max_length[250]');
		$this->form_validation->set_rules('categ', 'Categoria', 'required');
		$this->form_validation->set_rules('sit', 'Situação', 'required');

		if ($this->form_validation->run() == FALSE) {
			$status = "ERROR";
			$msg = validation_errors();
		} else {

			$item_data['usuario_id'] = $this->login_data['user_id'];

			if( $this->item_model->update( $input ) ) {
				$status = "OK";
				$msg = 'O Item foi atualizado com sucesso';
			} else {
				$status = "ERROR";
				$msg = 'Não foi possível atualizar o Item';
			}
		}

		echo json_encode( array('status'=>$status, 'msg'=>$msg ) );
	}

	public function changestatus( $id ) {
		if( !$this->is_user_logged_in ) {
			$result = "ERROR";
			$msg = xlang('dist_errsess_expire');

			echo json_encode( array('result'=>$result,
				'status'=>'', 'msg'=>$msg ) );
			return;
		}

		$status = $this->input->post('status');
		$statusname = $status === 'A' ? 'Ativo' : 'Inativo';
		if($this->item_model->change_status($id, $status)) {
			$result = "OK";
			$statusvalue = $status;
			$msg = 'O Status de seu Item foi atualizado para '.$statusname;
		} else {
			$result = "ERROR";
			$statusvalue = NULL;
			$msg = 'O Status de seu Item não foi atualizado';
		}

		echo json_encode( array('result'=>$result,
			'status'=>$statusvalue, 'msg'=>$msg ) );
	}

	public function doado($id) {
		if( !$this->is_user_logged_in ) {
			$msg = xlang('dist_errsess_expire');

			echo json_encode( array('status'=>'ERROR',
				'msg'=>$msg ) );
			return;
		}

		$status = $this->input->post('status');
		if($this->item_model->given($id, $status)) {
			$result = "OK";
			$statusvalue = $status;
			$msg = 'O Item foi marcado como doado.';
		} else {
			$result = "ERROR";
			$statusvalue = NULL;
			$msg = 'Ocorreu uma falha ao marcar o item como Doado';
		}
		echo json_encode( array('result'=>$result, 'status'=>$statusvalue, 'msg'=>$msg) );
	}

	public function modificar( $item_id ) {
		$msg = $this->check_owner($this->item_model, $item_id);
		if( $msg ) {
			show_error( $msg );
		}

		$item_data = $this->item_model->get( $item_id );
		$item_data['action'] = 'update';
		$item_data['temp_id'] = "0";

		$images = $this->get_images( $item_id );

		$this->load->model('categoria_model');
		$categorias = $this->categoria_model->get_all();

		$this->load->model('situacao_model');
		$situacoes = $this->situacao_model->get_all();

		$head_data = array("min_template"=>"image_upload",
			"title"=>"Modificar Item");

		$this->load->view('head', $head_data);

		$this->load->view('item_form',
			array('data'=>$item_data, 'images'=>$images,
			'categorias'=>$categorias,
			'situacoes'=>$situacoes) );

		$this->load->view('foot');
	}

	public function get_images( $item_id ) {
		$this->load->model('image_model');
		return $this->image_model->get_item_images( $item_id );
	}

	public function map_view( $item_id ) {
		$item_data = $this->item_model->get( $item_id );
		$img_data = $this->get_images( $item_id );

		$this->load_ajax('item_view', 
			array('idata'=>$item_data, 'imgdata'=>$img_data));
	}

	public function listar() {
		$item_ids = func_get_args();

		$items = $this->item_model->get_list( $item_ids );

		$arrItems = array();
		foreach ($items as $item) {
			$arrItems[ $item->id ]['data'] = $item;
			if( !empty($item->nome_arquivo ) ) {
				$arrItems[ $item->id ]['imagens'][] = $item->nome_arquivo;
			} else {
				$arrItems[ $item->id ]['imagens'] = array();
			}
		}

		$head_data = array('min_template'=>'image_upload',
			"title"=>"Listar Itens");
		$this->load->view('head', $head_data);

		$this->load->helper('image_helper');
		$this->load->view('item_list', array('items'=>$arrItems));

		$this->load->view('foot'); // fecha tag section
    }
}