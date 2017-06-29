<?php if ( !defined('BASEPATH')) exit('No direct script access allowed');

class Obter_enderecos extends MY_Controller {

	private $limite = 3; // maximo de ocorrencias para processar por vez
	private $delay = 500; // em milisegundos, entre cada obtencao de endereço

	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$this->load->model('ocorrencia_model');
		$this->load->library('googlemaps');

		$ocorrencias = $this->ocorrencia_model->get_nulladdr_ocorrencias();
		$count = 0;
		foreach($ocorrencias as $row) {
			if( $count == $this->limite ) { break; }
			$count++;

			$upd_data = array(
				'tipo'=>$row->tipo_ocorrencia_id,
				'nr_onibus' => $row->num_onibus,
				'nr_ordem' => $row->num_ordem,
				'id' => $row->id
				);

			$get_addr = $this->googlemaps->get_address_from_lat_long($row->lat, 
				$row->lng);
			if( $get_addr['status']=="OK" ) {
				$upd_data['logradouro'] = $get_addr['address'][0];
				$upd_data['bairro'] = $get_addr['address'][1];
			}

			$this->ocorrencia_model->update($upd_data);

			usleep( $this->delay * 1000 );
		}
		echo "FIM";
	}
}