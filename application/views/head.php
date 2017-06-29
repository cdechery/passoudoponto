<!DOCTYPE html>
<html>
<head>
<meta charset="<?php echo $this->config->item('charset');?>"/>
<meta property="og:title" content="Passou do Ponto" />
<meta property="og:description" content="Somos um site que ajuda aqueles que querem doar aquilo que está sobrando em casa a encontrar interessados. Pessoas e instiuições podem se cadastrar para disponibilizar itens para doação ou para procurarem aquilo que está disponível, em uma interface de mapa amigável e intuitiva." />
<meta property="og:image" content="<?php echo img_url('site_icon.png')?>" />
<?php
	if( !isset($title) ) {
		echo "ERROR: Title not defined!";
		return;
	}
	
	if( !isset($min_template) ) {
		$min_template = "basic";
	}

	$min_debug = "";
	if( ENVIRONMENT!='production' ) {
		$min_debug = "&debug=true";
	}

	if( !isset($login_data) ) {
		$login_data['logged_in'] = FALSE;
	}

	$bodyId = "";
	if( isset($home) ) {
		$bodyId = "id='home'";
	}

?>
<?php if( ENVIRONMENT=='production'): ?>
<script async>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-54303875-1', 'auto');
  ga('send', 'pageview');

</script>
<?php endif; ?>
<script type="application/javascript" src="<?php echo static_url('javascript')?>"></script>
<script type="application/javascript" src="<?php echo static_url('min/g='.$min_template.'_js'.$min_debug)?>"></script>
<!-- <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,900' rel='stylesheet' type='text/css'> -->
<link rel="stylesheet" type="text/css" href="<?php echo static_url('min/g='.$min_template.'_css'.$min_debug)?>"/>
<link rel="shortcut icon" href="<?php echo img_url('favicon.ico')?>" type="image/x-icon">
<link rel="icon" href="<?php echo img_url('favicon.ico')?>" type="image/x-icon">
<!--[if lt IE 9]>
<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<?php
	if( !empty($cust_js) ) {
?>
<script type="application/javascript" src="<?php echo static_url('min/f='.implode(",",$cust_js))?>"></script>
<?php
	}

	if( !empty($cust_css) ) {
?>
<link rel="stylesheet" type="text/css" href="<?php echo static_url('min/f='.implode(",",$cust_css).$min_debug)?>"/>
<?php
	}
?>
<title><?php echo $title; ?></title>
<script>
	$(document).ready(function(){
		/* 'sticky footer' */
		var dh = $(document).height(); //document height here
		var hh = $('header').height(); //header height
		var fh = $('footer').height(); //footer height
		var wh = Number(dh - hh - fh); //this is the height for the wrapper
		$('section.contents').css('min-height', wh); //set the height for the wrapper div
	});
</script>
</head>
<body <?php echo $bodyId?>>
<script type="text/javascript">
	img_preload([
		'<?php echo img_url("ajax-loader.gif")?>',
		'<?php echo img_url("ajax-loader-80.gif")?>',
		'<?php echo img_url("ajax-loader-120.gif")?>',
		'<?php echo img_url("ajax-loader-200.gif")?>',
		'<?php echo img_url("ajax-loader-small.gif")?>',
		'<?php echo img_url("connecting.gif")?>'
	]);
</script>
<?php
	$wait_img = img_url('connecting.gif');
	$fbReg = $this->input->cookie('FbRegPending');
	$fbLogin = $this->session->userdata('FbLoginPending');
	$enableFB = (ENVIRONMENT=='production');
	// para forçar exibição. comitar comentado
	$enableFB = true;
	
	$runFB = $enableFB && false == $fbLogin &&
		false == $login_data['logged_in'] && false == $fbReg;

	if( $runFB ) {
?>
<script>
	window.fbAsyncInit = function() {	
		FB.init({
			appId      : '<?php echo $params["facebook"]["appId"]?>', // App ID
			status     : true, // check login status
			cookie     : true, // enable cookies to allow the server to access the session
			xfbml      : true,  // parse XFBML
			version	   : 'v2.0'
		});
		FB.Event.subscribe('auth.authResponseChange', function(response) {
			if (response.status === 'connected') {
				new Messi('Estamos fazendo seu login no Facebook, aguarde '+
					'<img src="<?php echo $wait_img?>">',
					{ title: 'Conectando ...', modal: true } );
				logonFB();
			} else if (response.status === 'not_authorized') {
				FB.login();
			} else {
				FB.login();
			}
		});
	};

	function fb_login() {
	    FB.login( function() {}, { scope: 'email,public_profile' } );
	}

	(function(d){
		var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
		if (d.getElementById(id)) {return;}
		js = d.createElement('script'); js.id = id; js.async = true;
		js.src = "//connect.facebook.net/pt_BR/sdk.js";
		ref.parentNode.insertBefore(js, ref);
	}(document));
</script>
<?php
	 }
?>
<header id="main">
	<div class="wrap960">
		<h1><a href="<?php echo base_url();?>">Interessa ?</a></h1>
		<nav id="top">
			<ul>
				<li>
					<a href="<?php echo base_url('sobre')?>">Sobre</a>
				</li>
				<li>
					<a href="<?php echo base_url('contato')?>">Contato</a>
				</li>
					<?php if( $login_data["logged_in"] ) : ?>
						<li id="user-btn">
							<a href=""><?php echo $login_data["name"]?>&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-down"></i></a>
							<div id="user-menu">
								<ul>
									<li><a href="<?php echo base_url('usuario/meus_itens')?>"><i class="fa fa-plus-square-o">&nbsp;</i>&nbsp;Meus Itens</a></li>
									<li><a href="<?php echo base_url('usuario/interesses')?>"><i class="fa fa-list-ul"></i>&nbsp;Meus Interesses</a></li>
									<li><a href="<?php echo base_url('usuario/modificar')?>"><i class="fa fa-edit"></i>&nbsp;Editar perfil</a></li>
									<li><a href="<?php echo base_url('usuario/pref_email')?>"><i class="fa fa-envelope-o"></i>&nbsp;Preferências de email</a></li>
									<li><a href="<?php echo base_url('usuario/logout')?>"><i class="fa fa-power-off"></i>&nbsp;Logout</a></li>
								</ul>
							</div>
						</li>
						<?php else : ?>
						<li id="register">
							<a href="<?php echo base_url('login')?>">ENTRAR</a>
						</li>
					<?php endif; // if logged_in ?> 
				<?php if( $runFB ) : ?>
					<li id="facebook" style="text-align: middle;">
						<?php echo fblogin_button() ?>
					</li>
				<?php endif; // if logged_in ?> 
			</ul>
		</nav>
	</div>
</header>
<?php if( empty($home) ): ?>
	<section class="contents">
		<div class="wrap960">
<?php endif; ?>
