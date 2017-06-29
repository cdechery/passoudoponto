$('#email_queroitem').submit(function(e) {
	e.preventDefault();

	var action = $("#email_queroitem").attr("action");
	var formdata = $("#email_queroitem").serialize();

	$.post(action, formdata, function(data) {

		var json = myParseJSON( data );
		if( json.status=="OK" ) {
			$.fancybox.close();
			msg_success('O email foi enviado!', 'Sucesso', true);
		} else {
			msg_error( json.msg );
		}
	}).fail( function() { general_error(); } );
	return false;
});

$('#email_contato_inst').submit(function(e) {
	e.preventDefault();

	var action = $("#email_contato_inst").attr("action");
	var formdata = $("#email_contato_inst").serialize();

	$.post(action, formdata, function(data) {

		var json = myParseJSON( data );
		if( json.status=="OK" ) {
			$.fancybox.close();
			msg_success('O email foi enviado!', 'Sucesso', true);
		} else {
			msg_error( json.msg );
		}
	}).fail( function() { general_error(); } );
	return false;
});