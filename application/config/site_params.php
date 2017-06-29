<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| Parametros do site
| -------------------------------------------------------------------
*/

$config['site_params'] = array(
	'mapa' => array('default_loc'=>'-22.9035,-43.2096',
		'default_loc_name'=>'Rio de Janeiro'),
	'erro_generico' => 'Ocorreu um erro inesperado',
	'erro_acesso' => 'Acesso negado',
	'titulo_site' => 'Passou do Ponto',
	'image_settings' => array(
		'thumb_sizes' => array(40, 80, 120), // size of thumbs to generate
		'allowed_types' => array('jpeg', 'jpg', 'png'),
		'min_image_size' => '200'
	),
	'max_ocorrencia_fotos' => 3,
	'upload' => array(
		'path' => './files/',
		'max_size' => (8*1024)
	),
	'update_tool' => array('password'=>'###changethis###',
		'skip_files'=>'' ),
	'facebook' => array('appId' => '721835171296249',
        		'secret' => 'e22b21b6979077f9bf631df320b7a12c'),
	'basic_auth' => array('user'=>'admin', 'pass'=>'admin'),
	'access_tokens' => array('mK1Ri1304R2qrBAnS44b8otUxUpCvq4j',
		'c10HTNA364qUT5kpX34Mzms8momOepEb')
);

if( ENVIRONMENT=='production' ) {
	$config['site_params']['facebook'] = array('appId' => '649645738441266',
        'secret' => '8d37a4c4dc26a772b0d9cafffab5169e');
}
