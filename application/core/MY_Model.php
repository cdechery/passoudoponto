<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model {
	
	protected $table;
	protected $pk = "id";
	protected $params;
	
	public function __construct() {
		parent::__construct();

		// paramss settings available to all Models
		$this->params = $this->config->item('site_params');

		$this->table = NULL;
	}
	
	public function is_owner($user_id, $model_id) {
		
		if( $this->table===NULL ) {
			show_error("Erro no metodod check_owner() - tabela não definida em ".get_class($this), 500);
		}
		
		if( empty($user_id) || empty($model_id) ) {
			return FALSE;
		}
		
		$result = $this->db->get_where($this->table, array($this->pk => $model_id,
			'usuario_id'=>$user_id));
		return ( count($result) && $result->num_rows()>0 );
	}
}