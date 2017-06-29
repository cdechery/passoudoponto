<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Custom html assets helper
 */
	function meinteressa_button( $item_id ) {
		$CI =& get_instance();
		$CI->load->model('item_model');

		$btn_data = $CI->item_model->get_item_button_data( $item_id );

		$strqtd = "";
		$tooltip = "Ninguém se interessou por esse Item ainda, seja o primeiro!";
		$href = "href='".base_url("email/quer_item/".$item_id)."'";
		$class = "me_interessa active itembox fancybox.ajax";
		$icon = "";

		if( $btn_data['iqtd'] > 0 ) {
			$strqtd = " (".$btn_data['iqtd'].")";

			if( $btn_data['iqtd'] >= $btn_data['uqtd'] ) {
				$tooltip = "Esse Item já recebeu o máximo de mensagens de Interessados";
				$href = "";
				$class = "me_interessa disabled";
				$icon = "<i class='fa fa-meh-o'></i>&nbsp;";
			} else {
				if( $btn_data['iqtd']==1 ) {
					$tooltip = "Uma pessoa interessada apenas enviou mensagem para esse Item";
				} else {
					$tooltip = "".$btn_data['iqtd']." pessoas interessadas já enviaram mensagem para esse Item";
				}
			}
		}

		$ret = "<button id='btnitem".$item_id."' class='".$class."' ".$href." title='".$tooltip."' onClick='$(this).tipsy(\"hide\"); return false;'>".$icon."Me interessa!".$strqtd."</button>\n";
		$ret .= "<script type='text/javascript'>$('#btnitem".$item_id."').tipsy( {gravity: 's', opacity: 1 } );</script>";

		return $ret;
	}

	function adicionar_button($css="", $href="") {
		if( !empty($css) ) {
			$css = " style='".$css."'";
		}

		if( !empty($href) ) {
			$href = " href=\"#\" onClick=\"location.href='".$href."'\"";
		}
		return '<button class="adicionar"'.$css.''.$href.'><i class="fa fa-plus-circle"></i>&nbsp;&nbsp;adicionar</button>';
	}

	function fblogin_button($text = "") {
		if( empty($text) ) {
			$text = "Fazer login com";
		}

		return "<button class=\"signup btn\" onClick=\"fb_login();\">".$text." <i class=\"fa fa-facebook-square\"></i></button>";
	}
 ?>