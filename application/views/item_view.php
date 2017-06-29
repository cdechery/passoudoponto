<script type="text/javascript">
$(document).ready( function() {
	$.fancybox.update();
});
</script>
<header>
	<h3><?php echo $idata['titulo']?></h3>
</header>
<div id="main">
	<p style="min-height: 40px;"><?php echo nl2br(wordwrap($idata['descricao'],60))?></p>
	<?php if( isset($imgdata) && count($imgdata)>0 ): ?>
	<div class="imgs">
		<?php foreach ($imgdata as $img): ?>
		<a href="<?php echo user_img_url($img->nome_arquivo)?>" class="fancybox" rel="<?php echo $idata['id']?>" title="<?php echo $idata['titulo']?>"><img src="<?php echo item_image($img->nome_arquivo, 120)?>"></a>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
	<?php if( $login_data['logged_in'] ): ?>
	<div style="height: 30px;">
		<?php echo meinteressa_button( $idata['id'] ); ?>
	</div>
	<?php endif; ?>
</div>
