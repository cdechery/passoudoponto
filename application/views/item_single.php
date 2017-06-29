<div class="item_single">
<?php
	if( count($imagens) ) {
			$thumb = thumb_filename($imagens[0], 220);
?>
		<img src="<?php echo user_img_url($thumb)?>">
<?php
	}
?>	
	<div>
		<h3><?php echo $data->titulo ?></h3>
		<p><?php echo $data->descricao ?></p>
	</div>
</div>