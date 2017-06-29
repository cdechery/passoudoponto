<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuario_model extends MY_Model {

	public function __construct() 	{
		parent::__construct();
		$this->table = "usuario";
	}
	
	public function get_data( $id ) {
		if(empty($id) || $id==0) {
			return false;
		}

		$this->db->select('id, login, nome, sobrenome, email,
			avatar, date_format(dt_nasc, \'%d/%m/%Y\') as nasc', FALSE);
		return $this->db->get_where('usuario',
			array('id'=>$id))->row_array();
	}

	public function get_data_email( $email ) {
		return $this->db->get_where('usuario',
			array('email'=>$email))->row_array();
	}

	public function check_login($login, $password) {
		$encrypted_pwd = md5($password);

		$ret = $this->db->get_where('usuario', array('login'=>$login,
			'senha'=>$encrypted_pwd) );

		if( $ret->num_rows() > 0 ) {
			return $ret->row_array();
		} else {
			return FALSE;
		}
	}

	public function email_exists($email, $except_user_id = 0) {
		$query = $this->db->get_where('usuario',
			array('email'=> $email,
				'id !=' => $except_user_id ) );

		return $query->num_rows() > 0;
	}

	public function insert($user_data) {

		$insert_data = array(
			'login' => $user_data['login'],
			'nome' => $user_data['nome'],
			'sobrenome' => $user_data['sobrenome'],
			'email' => $user_data['email']
		);

		if( !empty($user_data['password']) ) {
			$insert_data['senha'] = md5( $user_data['password'] );
		}

		if( !empty($user_data['nascimento']) ) {
			$dt_parts = explode('/', $user_data['nascimento'] );
			$data = $dt_parts[2]."-".$dt_parts[1]."-".$dt_parts[0];
			$insert_data['dt_nasc'] = $data;
		}

		if( !empty($user_data['avatar']) ) {
			$insert_data['avatar'] = $user_data['avatar'];
		}

		if( $this->db->insert('usuario', $insert_data ) ) {
			$query = $this->db->query('SELECT LAST_INSERT_ID()');
			$row = $query->row_array();
			$LastIdInserted = $row['LAST_INSERT_ID()'];
			return $LastIdInserted;
		} else {
			return 0;
		}
	}

	public function update($user_data, $id) {

		if( empty($id) || $id==0 ) {
			return false;
		}

		$upd_data = array(
			'nome' => $user_data['nome'],
			'sobrenome' => $user_data['sobrenome'],
			'email' => $user_data['email'],
		);

		$dt_parts = explode('/', $user_data['nascimento'] );
		$data = $dt_parts[2]."-".$dt_parts[1]."-".$dt_parts[0];

		$upd_data['dt_nasc'] = $data;

		if( !empty($user_data['password']) ) {
			$upd_data['senha'] = md5($user_data['password']);
		}
		
		return( $this->db->update('usuario', 
			$upd_data, array('id' => $id) ) );
	}

	// public function update_pref_email($pref_data, $user_id) {
	// 	$upd_data = array(
	// 		'fg_geral_email' => $pref_data['fg_geral_email'],
	// 		'fg_notif_int_email' => $pref_data['fg_notif_int_email'],
	// 		'fg_de_inst_email' => $pref_data['fg_de_inst_email'],
	// 		'fg_de_pessoa_email' => $pref_data['fg_de_pessoa_email'],
	// 		'lim_emails_item' => $pref_data['lim_emails_item'],
	// 	);

	// 	return( $this->db->update('usuario', 
	// 		$upd_data, array('id' => $user_id) ) );
	// }

	// public function get_emails_newsletter() {
	// 	$this->db->select('nome, email');
	// 	$this->db->where('fg_geral_email = \'S\'');
	// 	return $this->db->get('usuario')->result();
	// }


	public function update_password($email, $new_pwd) {
		if( empty($email) || empty($new_pwd) ) {
			return false;
		}

		$upd_data = array( 'senha'=>md5($new_pwd) );
		return( $this->db->update('usuario', $upd_data, array('email' => $email)) );
	}

	public function update_avatar($img_data, $user_id, $thumb_sizes = array() ) {
		if( empty($img_data) || $user_id==0 ) {
			return false;
		}

		$upd_data = array(
			'avatar' => $img_data['file_name']
		);

		$this->load->helper('image_helper');
		if( count($thumb_sizes) ) {
			foreach( $thumb_sizes as $size ) {
				create_square_cropped_thumb( $img_data['full_path'], $size );
			}
		}

		return( $this->db->update('usuario', $upd_data, array('id' => $user_id)) );
	}
}
?>
