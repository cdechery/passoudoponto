jQuery.extend({
    handleError: function( s, xhr, status, e ) {
        // If a local callback was specified, fire it
        if ( s.error ) {
            s.error( xhr, status, e );
        	setErrorDiv( xhr.responseText );
        }
        // If we have some XML response text (e.g. from an AJAX call) then log it in the console
        else if(xhr.responseText) {
        	console.log(xhr.responseText);
			setErrorDiv( xhr.responseText );
		}
    }
});

function setErrorDiv( errorData ) {
	$('#error-details').html( errorData );
}

var nonJSONret = $.parseJSON( '{ "status": "nonJSONreturn", '
	+'"msg": "'+lang['dist_general_error']+'" }' );

function myParseJSON( jsonString ) {
	try {
		var json = $.parseJSON( jsonString );
		return json;
	} catch(err) {
		setErrorDiv( jsonString );
		return nonJSONret;
	}
}

function countOcurrences(str, value){
   var regExp = new RegExp(value, "gi");
   return str.match(regExp) ? str.match(regExp).length : 0;  
}

function logonFB() {
	location.href = site_root+'login/web_fb';
}

function go_home() {
	location.href = site_root;
}

function msg_general_error( msg ) {
	if( msg==null ) {
		msg = lang['dist_general_error'];
	}
	
	new Messi( msg, {title: 'Oops...', titleClass: 'anim error', 
		buttons: [{id: 0, label: 'Fechar', val: 'X'}] });
}

function msg_success(msg, title, modal, custom_action) {
	if( modal==null ) {
		modal = false;
	}

	if( custom_action ) {
		new Messi(msg, {title: title, 
			titleClass: 'anim success', modal: modal,
			buttons: [{id: 0, label: 'OK', val: 'S'}], 
			callback: custom_action } );
	} else {
		new Messi(msg, {title: title, 
			titleClass: 'anim success', modal: modal } );
	}
}

function msg_confirm(msg, title, custom_action) {
	new Messi(msg, {title: title, modal: true,
		buttons: [{id: 0, label: 'Sim', val: 'S'}, 
		{id: 1, label: 'Não', val: 'N'}], 
		callback: custom_action } );
}

function msg_error( msg, title ) {
	if( title==null ) {
		title = 'Oops...';
	}

	new Messi( msg, {title: title, titleClass: 'anim error', 
		modal: true, buttons: [{id: 0, label: 'Fechar', val: 'X'}]} );
}

function msg_general( msg, title, modal ) {
	new Messi( msg, {title: title, modal: modal} );
}

function load_infowindow_content(infowindow, id){
	$.ajax({
		url: site_root +'ocorrencia/map_infowindow/' + id,
		success: function(data) {
			$('.iw_loading_box').html(data).fadeIn('slow');
		}
	});
}

function img_preload( arrayOfImages ) {
    $(arrayOfImages).each(function () {
        $('<img />').attr('src',this).appendTo('body').hide();
    });
}

$(function() {

	$('#welcome #close').on('click', function(){
		$('#map #welcome').hide();
	});

	$('#user-btn').on('mouseover', function(){
		$('#user-menu').css('display','block').hover(
			function() {
				$(this).show();
			},
			function() {
				$(this).hide();
			}
		);
	});
	
	$('#pref_email').submit(function(e) {
		e.preventDefault();
		$.post($("#pref_email").attr("action"),
			$("#pref_email").serialize(), function(data) {
			var json = myParseJSON( data );
			if( json.status == "OK" ) {
				msg_success(json.msg, lang['dist_lbl_success'], true);
			} else {
				msg_error(json.msg);
			}
		}).fail( function() { msg_general_error(); } );
		return false;
	});

	$('#usuario_insert').submit(function(e) {
		e.preventDefault();
		$.post($("#usuario_insert").attr("action"),
			$("#usuario_insert").serialize(), function(data) {
			var json = myParseJSON( data );
			if( json.status=="OK" ) {
				if( json.fb ) {
					msg_success( json.msg, lang['dist_lbl_success'],
						true, function(val) { logonFB(); });
				} else {
					msg_success(json.msg, lang['dist_lbl_success'],
						true, function(val) { location.href=site_root+'login'; });
				}
			} else {
				msg_error( json.msg );
			}
		}).fail( function() { msg_general_error(); } );
		return false;
	});

	$('#usuario_update').submit(function(e) {
		e.preventDefault();
		$.post($("#usuario_update").attr("action"),
			$("#usuario_update").serialize(), function(data) {
			var json = myParseJSON( data );
			if( json.status=="OK" ) {
				msg_success( json.msg, lang['dist_lbl_success'], true);
			} else {
				msg_error( json.msg );
			}
		}).fail( function() { msg_general_error(); } );
		return false;
	});

	$('#item_insert').submit(function(e) {
		e.preventDefault();
		$.post($("#item_insert").attr("action"), $("#item_insert").serialize(), function(data) {
			var json = myParseJSON( data );
			if( json.status=="OK" ) {
				msg_success( json.msg, lang['dist_lbl_success'], true,
					function(val) { location.href = site_root+'usuario/meus_itens'; } );
			} else {
				msg_error( json.msg );
			}
		}).fail( function() { msg_general_error(); } );
		return false;
	});

	$('#item_update').submit(function(e) {
		e.preventDefault();
		$.post($("#item_update").attr("action"),
			$("#item_update").serialize(), function(data) {
			var json = myParseJSON( data );
			if( json.status=="OK" ) {
				msg_success( json.msg, lang['dist_lbl_success'], true);
			} else {
				msg_error( json.msg );
			}
		}).fail( function() { msg_general_error(); } );
		return false;
	});

	$(document).on('click', '.itemdel', function(e) {
		e.preventDefault();
		var itemid = $(this).data('itemid');
		var parentDiv = $(this).parents('.item_single');

		msg_confirm('Tem certeza que deseja remover este item?', 'Remover',
			function( val ) { 
				if( val=='S' ) {
					$.post(site_root+'item/delete/'+itemid, function(data) {
						var json = myParseJSON(data);
						if (json.status=="OK") {
							parentDiv.remove(); 
						} else {
							msg_error( json.msg );				
						}
					} ).fail( function() { msg_general_error(); } );
				}
			} );

		return false;
	});

	$(document).on('click', '.item-modify', function(e) {
		e.preventDefault();
		var itemid = $(this).data('itemid');
		location.href = site_root+'item/modificar/'+itemid;
		return false;
	});

	$(document).on('click', '.item-status', function(e){
		e.preventDefault();
		var btn = $(this);
		var itemstatus = btn.attr('data-status');
		if (itemstatus == 'A') {
			activ_deactiv_item(btn, 'I');
		} else {
			activ_deactiv_item(btn, 'A');
		}
		return false;
	});

	$(document).on('click', '.item-doado', function(e){
		e.preventDefault();
		var btn = $(this);
		var itemstatus = btn.attr('data-status');
		if (itemstatus == 'D') {
			msg_general('Este item já foi marcado como doado', 'Atenção');
		} else {
			msg_confirm('Essa ação é irreversível e você não poderá mais modificar este item.',
				'Confirmar doação',
				function(val) { if (val=='S') { marca_item_doado(btn); } } );
		}
		return false;
	});

	$('#interesse_insert').submit(function(e) {
		e.preventDefault();
		$.post($("#interesse_insert").attr("action"),
			$("#interesse_insert").serialize(), function(data) {
			var json = myParseJSON( data );
			if( json.status=="OK" ) {
				msg_success('O novo ineresse foi cadastrado', lang['dist_lbl_success'], true);
				var interesseData = $.get( site_root +'interesse/get_single/'+json.user+'/'+json.cat );
                interesseData.success(function(data) {
                	$('#interesses_none').hide();
                    $('#interesse-view table').append(data);
                });
			} else {
				msg_error( json.msg );
			}
		}).fail( function() { msg_general_error(); } );
		return false;
	});

	$(document).on('click', '.update_interesse_btn', function(e) {
		e.preventDefault();
		var btn = $(this);
		var raio = $('#raio_'+btn.data('catid')).val();
		$.ajax({
			url 		: site_root + 'interesse/update/'+btn.data('catid')+'/'+raio,
			contentType : 'charset=utf-8',
			dataType	: 'json',
			success     : function (data) {
				if ( data.status === "success" ) {
					msg_success( data.msg, lang['dist_lbl_success'], true);
				} else {
					msg_error( data.msg );
				}
			},
			error : function (data, status, e) {
				msg_general_error();
			}
		});
		return false;
	});

	$(document).on('click', '.activ_interesse_btn', function(e) {
		e.preventDefault();
		var btn = $(this);
		if( !btn.hasClass('blue') ) {
			activ_deactiv_interesse(btn, 'activate');
		} else {
			activ_deactiv_interesse(btn, 'deactivate');
		}
		return false;
	});

	$('#contato form').submit(function(e) {
		e.preventDefault();

		var action = $("#contato form").attr("action");
		var formdata = $("#contato form").serialize();

		$.post(action, formdata, function(data) {
			var json = myParseJSON( data );
			if( json.status=="OK" ) {
				msg_success('Sua mensagem foi enviada!', 'Sucesso', true);
			} else {
				msg_error( json.msg );
			}
		}).fail( function() { msg_general_error(); } );
		return false;
	});
});

var avatarUploadInProgress = false;

function do_upload_avatar() {

	if( avatarUploadInProgress ) return false;
	avatarUploadInProgress = true;

	var originalImg = $('#user_avatar').attr('src');
	$('#user_avatar').attr('src', site_root+'images/ajax-loader-200.gif');

	$.ajaxFileUpload({
		url 		   : site_root +'image/upload_avatar/',
		secureuri      : false,
		fileElementId  :'userfile',
		contentType    : 'application/json; charset=utf-8',
		dataType	   : 'json',
		data        : {
			'thumbs' : $('#thumbs').val()
		},
		success  : function (data) {
			if( data.status != 'error') {
				$('#user_avatar').attr('src',data.img_src);
				msg_success( data.msg, lang['dist_lbl_success'], true);
			} else {
				$('#user_avatar').attr('src', originalImg );
				msg_error( data.msg );
			}
		},
		error : function (data, status, e) {
			$('userfile').attr('src', originalImg );
			msg_general_error( lang['dist_error_upload'] );
		}
	});

	avatarUploadInProgress = false;
	return false;
}

var imgUploadInProgress = false;
function do_upload_item_image( img_id, isnew ) {

	if( imgUploadInProgress ) return false;
	imgUploadInProgress = true;

 	var img_tag_id = 'item_img_'+img_id;
	var file_tag_id = 'item_file_'+img_id;
	var action = '';

	if( $('#id').val()==0 && isnew ) {
		img_tag_id = 'img_'+img_id;
		file_tag_id = 'file_'+img_id;
		action = 'upload_temp_item_image';
	} else if( $('#id').val()!=0 && isnew ) {
		action = 'upload_item_image';
	} else {
		action = 'update_item_image';
	}

	var originalImg = $('#'+img_tag_id).attr('src');
	$('#'+img_tag_id).attr('src', site_root+'images/ajax-loader-120.gif');

    $.ajaxFileUpload({
        url : site_root +'image/'+action+'/',
        secureuri : false,
        fileElementId : file_tag_id,
        contentType : 'application/json; charset=utf-8',
        dataType        : 'json',
        data : {
            'id' : $('#id').val(),
            'thumbs' : $('#thumbs').val(),
            'file_tag_name': file_tag_id,
            'temp_id': $('#temp_id').val(),
            'img_id': img_id
        },
        success : function(data) {
            if( data.status != 'error') {
                var imageData = $.getJSON( site_root + 'image/get_image/'+data.file_id );
                imageData.success(function(imgdata) {
	                $('#'+img_tag_id).attr('src', site_root+'files/'+imgdata.thumb120);
	                if( isnew ) {
	                	$('#'+img_tag_id).data('newid', data.file_id);
	                }
                });
            } else {
				msg_error( data.msg );
				$('#'+img_tag_id).attr('src', originalImg);
            }
        },
        error : function (data, status, e) {
			msg_general_error( lang['dist_error_upload'] );
			$('#'+img_tag_id).attr('src', originalImg);
		}
    });

    imgUploadInProgress = false;
    return false;
}

function activ_deactiv_interesse( btn, action ) {
	$.ajax({
		url         : site_root + 'interesse/'+action+'/'+btn.data('catid'),
		contentType : 'charset=utf-8',
		dataType 	: 'json',
		success 	: function (data) {
			if ( data.status === "success" ) {
				if( action=='activate' ) {
					btn.html('<i class="fa fa-square-o"></i>&nbsp;Desativar');
					btn.addClass('blue');
					btn.parents('tr').removeClass('disabled');
				} else {
					btn.html('<i class="fa fa-check-square-o"></i>&nbsp;Ativar');
					btn.removeClass('blue');
					btn.parents('tr').addClass('disabled');
				}
				return true;
			} else {
				msg_error( data.msg );
				return false;
			}
		},
		error : function (data, status, e) {
			msg_general_error();
			return false;
		}
	});
}

function activ_deactiv_item(btn, action) {
	$.post( site_root+'item/changestatus/'+btn.data('itemid'),
		{ status: action } , function(data) {
		var json = myParseJSON(data);
		if (json.result === "OK") {
			msg_success( json.msg, lang['dist_lbl_success'], true);

			if (json.status === 'A') {
				btn.removeClass('active');
				btn.html('<i class="fa fa-square-o"></i>&nbsp;Desativar Item');
				btn.attr('data-status', 'A');
				btn.next().removeClass('disabled').attr('disabled', false);
			} else {
				btn.addClass('active');
				btn.html('<i class="fa fa-check-square-o"></i>&nbsp;Ativar Item');
				btn.attr('data-status', 'I');
				btn.next().addClass('disabled').attr('disabled', true);
			};
		} else {
			msg_error( json.msg );
		}
	}).fail( function() { msg_general_error(); } );;
}

function marca_item_doado(btn) {
	$.post(site_root+'item/doado/'+btn.data('itemid'), { status: 'D' }, function(data) {
		var json = myParseJSON( data );
		if (json.result === "OK") {
			msg_success('O item foi marcado como \'Doado\'', 'Obrigado!', true);
			btn.addClass('active');
			btn.html('<i class="fa fa-check-square-o"></i>&nbsp;Doado');
			btn.attr('data-status', 'D');
			btn.siblings('button').addClass('disabled').attr('disabled', true);
		} else {
			msg_error( json.msg );
		};
	}).fail( function() { msg_general_error(); } );
};

