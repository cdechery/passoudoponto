<?php
	$nome = $sobrenome = $lat = $lng = $email = "";
	$data_nascimento = $sexo = $login = $avatar = $action = "";
	$id = "";

	if( !empty($data) ) {
		extract($data);

		if( !empty($data_nascimento) ) {
			$dt_parts = explode('-', $data_nascimento );
			$data_nascimento = $dt_parts[2]."/".$dt_parts[1]."/".$dt_parts[0];
		}
	}

	$actions = array("insert"=>xlabel('insert'), "update"=>xlabel('update'));
	$fbReg = $this->input->cookie('FbRegPending');

	$hiddenAvatar = "";
	$fromFacebook = false;
	if( $fbReg ) {
		// veio do facebook
		$fromFacebook = true;
		$hiddenAvatar = '<input type="hidden" name="avatar" value="'.$avatar.'">';
	}

	$avatar = user_avatar($avatar, 200);

	$login_disabled = "";
	if( $action=="update" ) {
		$login_disabled = "disabled";
	}

	$lblTipo = "Pessoa";
	if( $tipo=="I" ) { // Instituicao
		$lblTipo = "Instituição";
	}
?>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true&libraries=places"></script>
<script type="text/javascript">
//<![CDATA[
var map; // Global declaration of the map
var userLocMarker = null;
var placesService;
var placesAutocomplete;

function updateFormLatLng(lat, lng) {
	document.userData.lat.value = lat;
	document.userData.lng.value = lng;
}

function createMarker( markerOptions ) {
	if( userLocMarker!=null ) {
		return false;
	}

	var marker = new google.maps.Marker( markerOptions );
	marker.set("content", "Sua localização");

	userLocMarker = marker;
	google.maps.event.addListener(marker, "dragend", function(event) {
		updateFormLatLng(event.latLng.lat(), event.latLng.lng());
	});
		
	updateFormLatLng( marker.getPosition().lat(),
		marker.getPosition().lng() );
}

function initialize() {
	
	var myLatlng = new google.maps.LatLng( <?php echo $params['mapa']['default_loc']?> );
	var myOptions = {
  		zoom: 13,
		center: myLatlng,
  		mapTypeId: google.maps.MapTypeId.ROADMAP}
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	google.maps.event.addListener(map, "dblclick", function(event) {
		if( userLocMarker!=null ) {
			userLocMarker.setMap(null);
			userLocMarker = null;
		}
		createMarker({ map: map, position:event.latLng, draggable: true });
	});

	var autocompleteOptions = { }
	var autocompleteInput = document.getElementById('myPlaceTextBox');
				
	placesAutocomplete = new google.maps.places.Autocomplete(autocompleteInput, autocompleteOptions);
	placesAutocomplete.bindTo('bounds', map);
	google.maps.event.addListener(placesAutocomplete, 'place_changed', function() {
		var loc = placesAutocomplete.getPlace().geometry.location;
		createMarker({ map: map, position:loc, draggable: true });
		map.setCenter(loc);
	});	

<?php
	if( !empty($lat) && !empty($lng) ) {
?>
	var myLatlng = new google.maps.LatLng(<?php echo $lat?>, <?php echo $lng?>);	
	createMarker( { map: map, position:myLatlng, draggable: true } );
	map.setZoom(15);
	map.setCenter( myLatlng );
<?php		
	} 
?>
} // initialize

window.onload = initialize;
//]]>
<?php
	if( $fromFacebook ) {
?>
	new Messi('Para finalizar o cadastro, precisamos de mais algumas informações.<br>Por favor preencha os campos em branco.',
		{ title: 'Cadastro via Facebook', modal: true } );
<?php	
	}
?>
</script>

<div id="cadastro" class="roundbox clearfix">

	<h2>
		<?php echo $login_disabled ? "Editar perfil" : "Cadastro de ".$lblTipo; ?>
	</h2>
		<div class="col1">

			<?php if( $action=="update" || ($fromFacebook) ) { ?>
				<form method="post" action="<?php echo base_url();?>image/upload_avatar" id="upload_avatar" enctype="multipart/form-data">
					<div class="form-group">
						<input type="hidden" name="user_id" id="user_id" value="<?php echo $id; ?>">
						<input type="hidden" name="thumbs" id="thumbs" value="<?php echo implode('|',$params['image_settings']['thumb_sizes'])?>"/>
						<label>Sua imagem</label>
						<input type="file" id="userfile" name="userfile" style="display: none;" onChange="do_upload_avatar();" />
						<a href="#" id="foto" onClick="document.getElementById('userfile').click();">
							<img title="Clique aqui para alterar sua foto" id="user_avatar" src="<?php echo $avatar?>"/>
						</a>
					</div>
				</form>
			<?php } ?>

	<form method="POST" name="userData" action="<?php echo base_url()?>usuario/<?php echo $action; ?>" id="usuario_<?php echo $action?>">
		
		<?php echo $hiddenAvatar?>
		<input type="hidden" name="id" value="<?php echo $id ?>">
		<input type="hidden" name="lat" value="<?php echo $lat ?>">
		<input type="hidden" name="lng" value="<?php echo $lng ?>">
		<input type="hidden" name="tipo" value="<?php echo $tipo ?>">
			<div class="form-group">
				<label>Login</label>
				<input type="text" name="login" value="<?php echo $login; ?>" <?php echo $login_disabled; ?> title="Login" placeholder="Seu login" />
			</div>
			<div class="form-group">
				<label>Senha</label>
				<div class="form-group">
					<input type="password" class="horizontal" name="password" value="" placeholder="Escolha uma senha" >
					<input type="password" class="horizontal" name="password_2" value="" placeholder="Repita a senha">
				</div>
			</div>
			<div class="form-group">
				<label>Email</label>
				<input type="text" class="largeplus" name="email" value="<?php echo $email?>" title="Email" placeholder="Seu email" />
			</div>
			<div class="form-group">
				<label>Nome</label>
				<div class="form-group">
					<?php if( $tipo=="P") : ?>
						<input type="text" class="horizontal" name="nome" value="<?php echo $nome ?>" title="Nome" placeholder="Seu nome" />
						<input type="text" class="horizontal" name="sobrenome" value="<?php echo $sobrenome; ?>" title="Sobrenome" placeholder="Seu sobrenome" />
					<?php else: ?>
						<input type="text" class="largeplus" name="nome" value="<?php echo $nome ?>" title="Nome" placeholder="Seu nome" />
					<?php endif; ?>
				</div>
			</div>
			
			<?php
				if( $tipo=="P") {
					$sexoM = ($sexo=="M")?"checked":"";
					$sexoF = ($sexo=="F")?"checked":"";
			?>
						<div class="form-group">
							<div class="form-group-col">
								<label>Nascimento</label>
								<input type="text" id="dtnascimento" name="nascimento" value="<?php echo $data_nascimento; ?>" maxlength="10" title="Data de Nascimento" placeholder="DD/MM/AAAA" onKeyup="dateFormat(this);"/>
							</div>
							<div class="form-group-col">
								<label>Sexo</label>
								<div class="form-group">
									<input type="radio" name="sexo" value="M" <?php echo $sexoM?>> Masculino&nbsp;&nbsp;&nbsp;
									<input type="radio" name="sexo" value="F" <?php echo $sexoF?>> Feminino
								</div>
							</div>
						</div>
			<?php
				} // tipo==P
			?>
	
		</div>

		<div class="col2">
		
			<div id="loc" class="form-group">
				<label>Localização</label>
				<input type="text" id="myPlaceTextBox" placeholder="Digite sua localização"/>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo base_url('usuario/ajuda_localizacao')?>" class='locationbox fancybox.ajax'><i class="fa fa-question-circle"></i></a> 
			</div>
			
			<div id="map_canvas"></div>

		</div>

		<br clear="all"><br>

		<div class="form-group">
			<input type="submit" value="<?php echo $actions[ $action ]; ?>"/>
		</div>

	</form>

</div><!-- cadastro -->

<script>
	$( document ).ready(function() {
		$(".locationbox").fancybox({
			padding		: 25,
			width		: '400px',
			height		: '300px',
			autoSize	: false,
			type		: 'ajax',
			closeClick	: false,
			openEffect	: 'none',
			closeEffect	: 'none'
		});

		$(window).keydown(function(event){
			if(event.keyCode == 13) {
				event.preventDefault();
				return false;
			}
		});
	});

	function dateFormat( el ) {
		value = el.value;
		switch( value.length ) {
			case 2:
				el.value += '/';
				return false;
			case 5:
				el.value += '/';
				return false;
			default:
				break;
		}
	}	
</script>
