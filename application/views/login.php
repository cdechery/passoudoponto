<script>
$(document).ready(function() {
	// esqueceu senha
	$("#lembrasenha a").fancybox({
		fitToView	: false,
		width		: '400px',
		height		: '150px',
		autoSize	: false,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none'
	});
	$(".escolhetipo_box").fancybox({
		padding		: 25,
		fitToView	: false,
		width		: '630px',
		height		: '330px',
		autoSize	: false,
		type		: 'ajax',
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none'
	});
});
</script>

<div id="login-page" class="roundbox">

	<header>
		<h3>Login</h3>
	</header>
	
	<div id="erromsg"><?php echo $msg?></div>

	<form method="post" action="<?php echo base_url()?>login/user_pass">
		<input type="hidden" name="next" value="<?php echo $next?>">
		<div class="form-group">
			<input type="text" name="login" id="login" placeholder="Login" />
		</div>
		<div class="form-group">
			<input type="password" name="password" id="password" placeholder="Senha" />
		</div>
		<div id="lembrasenha" class="form-group checkbox clearfix">
			<a href="<?php echo base_url()?>usuario/reset_password" data-fancybox-type="iframe">
				<?php echo xlang('dist_resetpw_link')?>
			</a>
			<label>
				<input type="checkbox" name="lembrar"> Manter conectado?
			</label>
		</div>
		<div class="form-group">
			<input type="submit" value="Fazer login">
		</div>
	</form>

</div>

<div id="link_cadastro">
	Novo por aqui?<br><a href="<?php echo base_url('usuario/escolhe_tipo/w')?>" class="signup link escolhetipo_box fancybox.ajax">Preencha o cadastro</a> ou <a  class="signup link" href="#" onClick="fb_login();">Conecte-se <i class="fa fa-facebook-square"></i></a>
</div>

