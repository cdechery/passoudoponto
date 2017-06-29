<?php if ( !defined('BASEPATH')) exit('No direct script access allowed');

class Manut_imagens extends MY_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$this->require_auth();

		header('Content-Type: text/plain');

		output_log('INICIO do processo de manutencao de imagens');

		$this->load->model('image_model');
		$path = $this->params['upload']['path'];
		$realpath = realpath( $path );
		$thumb_sizes = $this->params['image_settings']['thumb_sizes'];

		if( $handle = opendir($path) ) {
			$this->image_model->prepare_tmp_table();

			output_log('Carregando tabela temporaria de imagens');
			while (false !== ($file = readdir($handle))) {
				if( $file!="cgi-bin" && $file!="." && $file!=".." && 
					!strstr($file, "_t") && !strstr($file, "html") ) {

        			$this->image_model->insert_temp( $file );
        		}
    		}
		} else {
			output_log('Não foi possível abrir o diretorio de imagens!');
			return;
		}

		output_log('Obtendo imagens temporarias com mais de 24h');
		$old_tmp_imgs = $this->image_model->get_old_temp_images();

		output_log('Obtendo imagens sem referencia no banco de dados');
		$unlinked_imgs = $this->image_model->get_unlinked_images();

		output_log('Obtendo imagens que o item foi apagado');
		$orphan_imgs = $this->image_model->get_orphan_images();

		output_log('Apagando arquivos de imagem selecionados');
		$to_delete = array_merge($old_tmp_imgs, $unlinked_imgs, $orphan_imgs);
		foreach ($to_delete as $img) {
			@unlink( $path.$img->nome_arquivo );
			foreach ($thumb_sizes as $size) {
				$thumb = thumb_filename( $img->nome_arquivo, $size );
				@unlink( $path.$thumb );
			}
		}

		output_log(count($to_delete).' arquivos de imagens foram apagados');

		output_log('Limpando avatars com arquivos inexistentes');
		$this->image_model->clear_empty_avatars();
		output_log('Removendo imagens com arquivos inexistentes');
		$this->image_model->delete_empty_imgs();
		output_log('Removendo imagens temporarias');
		$this->image_model->purge_old_temp_images();

		output_log('Limpando imagens sem item');
		$this->image_model->delete_orphan_images();

		output_log('Verificando a existencia de todos os tamanhos de thumbs');

		$all_imgs = $this->image_model->get_all_images();
		output_log('Iniciando varredura de '.count($all_imgs).' imagens');

		$count_created_thumbs = 0;
		foreach ( $all_imgs as $img ) {
			foreach ( $thumb_sizes as $size ) {
				$thumb_name = thumb_filename( $img->nome_arquivo, $size );
				if( !file_exists( $path . $thumb_name) ) {
					create_square_cropped_thumb( $realpath."/".$img->nome_arquivo, $size );
					$count_created_thumbs++;
				}
			}
		}
		output_log('Foram criados '.$count_created_thumbs.' novos thumbnails');

		output_log('Apagando item_temp com mais de 24h');
		$this->load->model('item_model');
		$this->item_model->purge_old_temp();

		output_log('FIM do processo de manutencao de imagens');
	}
}