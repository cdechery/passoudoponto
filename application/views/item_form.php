<?php
	$descricao = $status = $titulo = "";
	$categoria_id = $situacao_id = "";
	$id = "0";

	if( !empty($data) ) {
		extract($data);
	}
	if( empty($images) ) {
		$images = array();
	}

	$actions = array("insert"=>xlabel('insert'),
		"update"=>xlabel('update'));
?>
<div id="newitem" class="roundbox clearfix">

	<h2>Cadastro de Item</h2>

	<form method="POST" name="itemData" action="<?php echo base_url()?>item/<?php echo $action; ?>" id="item_<?php echo $action?>">
		<input type="hidden" name="id" id="id" value="<?php echo $id ?>">
		<input type="hidden" name="temp_id" id="temp_id" value="<?php echo $temp_id ?>">
		<input type="hidden" name="usuario_id" id="usuario_id" value="<?php echo $login_data['user_id'] ?>">
	    <input type="hidden" name="thumbs" id="thumbs" value="<?php echo implode('|',$params['image_settings']['thumb_sizes'])?>"/>
		<div class="form-group">
			<label>Categoria:</label>
			<select name="categ">
				<option value=""></option>
				<?php
					$selected = "";
					foreach ($categorias as $cat) {
						$selected = ($cat->id==$categoria_id)?"selected":"";
						echo '<option value="'.$cat->id.'" '.$selected.'>'.$cat->nome.'</option>\n';
					}
				?>
			</select>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo base_url('item/get_categorias')?>" class='catbox fancybox.ajax'><i class="fa fa-question-circle"></i></a>
		</div>
		<div class="form-group">
			<label>Situação:</label>
			<select name="sit">
				<option value=""></option>
				<?php
					$selected = "";
					foreach ($situacoes as $sit) {
						$selected = ($sit->id==$situacao_id)?"selected":"";
						echo '<option value="'.$sit->id.'" '.$selected.'>'.$sit->descricao.'</option>\n';
					}
				?>
			</select>
		</div>
		<div class="form-group">
			<label>Nome do item:</label>
			<input type="text" name="titulo" value="<?php echo $titulo ?>" title="Título" />
		</div>
		<div class="form-group">
			<label>Descrição:</label>
			<textarea name="desc" id="item_desc" title="Descrição" rows="6" cols="50"/><?php echo $descricao?></textarea>
		</div>
		<div class="form-group">
			<input type="submit" value="<?php echo $actions[ $action ]; ?>"/>
			<div id="charNum" style="float: right; font-size: small; padding-right: 20px"></div>
		</div>
	</form>

	<aside id="image" class="col">
		<form method="post" action="<?php echo base_url();?>image/upload_item_image" id="upload_item_image" enctype="multipart/form-data">
			<div class="form-group">
				<label>imagens do item</label>
					<div>
	<?php

	$num_imgs = 0;
	$max_imgs = $params['max_item_imgs'];

	if( $action!="insert" ) {

		// Imagens do item, se houver
		foreach($images as $img) {
			$num_imgs++;
			$thumb = item_image($img->nome_arquivo, 120);
	?>
			<input type="file" name="item_file_<?php echo $img->id?>" style="display: none;" id="item_file_<?php echo $img->id?>" onChange="do_upload_item_image(<?php echo $img->id?>, false);" />
			<a href="#" onclick="document.getElementById('item_file_<?php echo $img->id?>').click();"/>
				<img title="Alterar imagem" src="<?php echo $thumb?>" id="item_img_<?php echo $img->id?>" data-imgid="<?php echo $img->id?>">
			</a>
	<?php
		} // for item imagens do item

		if( $num_imgs < $max_imgs ) { // existem imagens, é um update
			for($i=$num_imgs; $i<$max_imgs; $i++) {
	?>
				<input type="file" name="item_file_<?php echo $i?>" style="display: none;" id="item_file_<?php echo $i?>" onchange="do_upload_item_image(<?php echo $i?>, true);" />
				<a href="#" onclick="document.getElementById('item_file_<?php echo $i?>').click();"/>
					<img title="Enviar imagem" src="<?php echo item_image(null, 120)?>" id="item_img_<?php echo $i?>" data-imgid="0">
				</a>
	<?php
			} // for imagens default
		}

	} else { // item novo?
		for($i=1; $i<=$max_imgs; $i++) {
	?>		
			<input type="file" name="file_<?php echo $i?>" style="display: none;" id="file_<?php echo $i?>" onchange="do_upload_item_image(<?php echo $i?>,true);"/>
			<a href="#" onclick="document.getElementById('file_<?php echo $i?>').click();"/>
				<img title="Enviar imagem" src="<?php echo item_image(null, 120)?>" id="img_<?php echo $i?>" data-newid="0"/>
			</a>
	<?php
		} //for imagens default novo item
	} // if novo item
	?>
				</div>
			</div>
		</form>

	</aside>

</div>

<script>
	$('#item_desc').keyup(function() {
	  var max = 250;
	  var len = $(this).val().length;
	  if (len >= max) {
	    $('#charNum').text('Tamanho máximo atingido!');
		$(this).val( $(this).val().substring(0, max-1) );
	  } else {
	    var char = max - len;
	    $('#charNum').text('Você ainda tem '+char+' caracteres');
	    return true;
	  }
	});

	$(document).ready(function() {
		$(".catbox").fancybox({
			padding		: 25,
			maxWidth	: 400,
			maxHeight	: 300,
			fitToView	: false,
			width		: '90%',
			height		: '90%',
			autoSize	: false,
			type		: 'ajax',
			closeClick	: false,
			openEffect	: 'none',
			closeEffect	: 'none'
		});
	});
</script>
