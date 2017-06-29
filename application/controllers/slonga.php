<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Slonga extends MY_Controller {

	public function __construct() {
		parent::__construct();
	}

	public function index() {

		$this->load->library('googlemaps');
		$config = array();
		$config['center'] = $this->params['mapa']['default_loc'];
		$config['zoom'] = 'auto';	
		$config['geocodeCaching'] = FALSE;
		$config['minifyJS'] = TRUE;
		$config['places'] = FALSE;
		$config['cluster'] = FALSE;
		$config['sensor'] = TRUE;
		$config['places'] = TRUE;
		// if( ENVIRONMENT=='production') {
		// 	$config['https'] = TRUE;
		// 	$config['apiKey'] = 'AIzaSyBh29Uw40WRIA-lVdyM0xVIVqgDVdEpp10';
		// }
		$config['disableMapTypeControl'] = TRUE;
		$config['disableStreetViewControl'] = TRUE;
		$config['placesAutocompleteInputID'] = 'mapCenterTextBox';
		$config['placesAutocompleteBoundsMap'] = TRUE; // set results biased towards the maps viewport
		$config['placesAutocompleteOnChange'] = 'var place = placesAutocomplete.getPlace(); map.setCenter( place.geometry.location ); $(\'#exibindo_mapa\').html( place.address_components[0].long_name ); console.log( place );';

		$config['map_width'] = '90%';
		$config['map_height'] = '500px';

		$this->load->model('mapa_model');
		$map_result = $this->mapa_model->get_all();

		$this->load->model('tipo_ocorrencia_model');
		$tipos = $this->tipo_ocorrencia_model->get_all();

		$markers_created = array();
		foreach($map_result as $row) {
			
			// if( $row->usuario_id == $this->login_data['user_id'] ) {
			// 	continue;
			// }

			// if( in_array($row->user_id, $markers_created) ) {
			// 	$custom_js_init .= "marker_".$row->user_id."_settings.items.push( new Array('".$row->cat_id."', '".$row->sit_id."','".$row->int_id."') ) ;";
			// 	continue;
			// }

			$icon = $row->icone;
			if( $row->usuario_id == $this->login_data['user_id'] ) {
				$icon .="-me";
			}
			$icon .= ".png";

			// die( img_url($icon) );

			$marker = array();
			$marker['position'] = $row->lat.', '.$row->lng;
			$marker['infowindow_content'] = 'Ocorrencia';
			$marker['clickable'] = true;
			$marker['onclick'] = 'map.setCenter(this.position); map.panBy(0, -120);';
			$marker['icon'] = img_url( $icon );

			$marker['id'] = $row->id;

			$this->googlemaps->add_marker($marker);

			// $custom_js_global .= "var marker_".$row->user_id."_settings = {};\n";
			// $custom_js_init .= "marker_".$row->user_id."_settings[\"type\"] = '".$row->tipo."';\n";
			// $custom_js_init .= "marker_".$row->user_id."_settings[\"items\"] = new Array();\n";
			// $custom_js_init .= "marker_".$row->user_id."_settings.items.push( new Array('".$row->cat_id."','".$row->sit_id."','".$row->int_id."') );\n";
			// $custom_js_init .= "marker_".$row->user_id."_settings[\"mrk\"] = marker_".$row->user_id.";";
			// $custom_js_init .= "markers_settings.push( marker_".$row->user_id."_settings );";

			// $markers_created[] = $row->user_id;
		}

		// $user_location = "";
		// if( $this->is_user_logged_in ) {
		// 	$this->load->model('usuario_model');
		// 	$user_data = $this->usuario_model->get_data( $this->login_data['user_id'] );

		// 	$marker = array();
		// 	$marker['position'] = $user_data['lat'].', '.$user_data['lng'];
		// 	$user_location = $marker['position'];

		// 	$marker['infowindow_content'] = 'Você';
		// 	$marker['clickable'] = false;

		// 	$marker['id'] = $user_data['id'];

		// 	$this->googlemaps->add_marker( $marker );

		// 	$config['center'] = $user_data['lat'].', '.$user_data['lng'];
		// }

		$this->googlemaps->initialize($config);
		$map = $this->googlemaps->create_map();

		$view_data = array('map'=>$map,
			'tipos'=>$tipos );

		// $cust_js = array('js/map.js', 'js/jquery.tipsy.js');
		// $cust_css = array('css/tipsy.css');

		// $this->load->view('head', array('title'=>'Interessa.org',
		// 	'min_template'=>'image_view',
		// 	'cust_js'=>$cust_js, 'cust_css'=>$cust_css, 'home'=>1));
		
		$this->load->view("slonga", $view_data);
		// $this->load->view('foot');

	}
}