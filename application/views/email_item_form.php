<?php
	$avatar_from = $avatar_to = "";
	$avatar_from = user_avatar($from_user['avatar'], 40);
	$avatar_to = user_avatar($to_user['avatar'], 40);
?>
<header>
	<h3>Interesse de Item</h3>
</header>

<div id="main">
	
	<form method="POST" id="email_queroitem" action="<?php echo base_url('email/enviar_quer_item')?>">
		<input type="hidden" name="de_email" value="<?php echo $from_user['email']?>">
		<input type="hidden" name="de_nome" value="<?php echo $from_user['nome']?>">
		<input type="hidden" name="para_email" value="<?php echo $to_user['email']?>">
		<input type="hidden" name="para_nome" value="<?php echo $to_user['nome']?>">
		<input type="hidden" name="item_id" value="<?php echo $item['id']?>">
		
		<div class="de de-para">
			<img src="<?php echo $avatar_from?>"> <?php echo $from_user['nome']?>
			<i class="fa fa-chevron-right"></i>
		</div>
		
		<div class="de-para">
			<img src="<?php echo $avatar_to?>"> <?php echo $to_user['nome']?>
		</div>
		
		<div class="form-group">
			<label>Assunto: </label>
			<input type="text" name="assunto" placeholder="Eu quero o <?php echo $item['titulo']?>">
		</div>
		
		<div class="form-group">
			<label>Mensagem (opcional): </label>
			<textarea cols="30" rows="4" name="corpo"></textarea>
		</div>
		
		<div class="form-group submit">
			<input type="submit" value="Enviar">
		</div>

	</form>	

</div>

