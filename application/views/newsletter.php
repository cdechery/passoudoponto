<html>
<head>
	<title>Newsletter</title>
	 <link type="text/css" rel="stylesheet" href="<?php echo base_url('css/doacoes.css')?>">
<script type="text/javascript">
	function valida(form) {
		if( form.assunto.value.length < 3 ) {
			alert('Assunto muito pequeno!');
			return false;
		}

		if( form.msg.value.length < 10 ) {
			alert('Mensagem muito pequena!');
			return false;
		}

		form.submit.disabled = true;
		form.submit.value = 'Enviando, aguarde...';			

		return true;
	}
</script>
</head>
<body style="margin: 20px">
<h2>Enviar Newsletter</h2>
<form method="post" action="<?php echo base_url('newsletter/enviar')?>" onSubmit="return valida(this);">
Assunto: <input type="text" name="assunto">
Mensagem (não precisa do 'abraço'):
<textarea name="msg" rows="4" cols="40">
</textarea>
<input name="submit" type="submit" value="Enviar">
<p><a href="<?php echo base_url('newsletter')?>">Enviar outra?</a></p>
</body>
</html>