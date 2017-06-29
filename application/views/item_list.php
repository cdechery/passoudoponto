<?php if( !empty($user) ): ?>
	<header id="user_itens" class="clearfix">
		<h3><img style="vertical-align: middle" src="<?php echo user_avatar($user['avatar'], 80) ?>">
		mostrando itens disponíveis de <i><?php echo $user['nome']?></i></h3>
	</header>
<?php endif; ?>

<div id="show_itens" class="clearfix">

<?php
	if( count($items)==0 ) {
		echo "<h3>Nenhum item para exibir</h3>";		
	}

	foreach ($items as $item_id => $item) {
?>
		<div class="item_single">
			<div class="thumbs">
				<?php if( count($item['imagens']) ) {
					foreach ($item['imagens'] as $file) {
						$thumb = thumb_filename($file, 120);
						echo "<a href='".user_img_url($file)."' class='fancybox' rel='fotos_".$item_id."' title='".$item['data']->titulo."'><img src=".user_img_url($thumb)." /></a>";
					}
				} else {
						echo "<div style='text-align: center;'>Sem imagens</div>";
				} ?>
			</div>
			<h3><?php echo $item['data']->titulo ?></h3>
			<p class="data-cadastro">Cadastrado em: <?php echo $item['data']->dtinc_format?></p>
			<div class="descricao"><?php echo nl2br(wordwrap($item['data']->descricao,70)) ?></div>
			<div class="action">
				<?php if ($item['data']->status == 'D'): { ?>
					<button class="item-list disabled"><i class="fa fa-check-square-o"></i>&nbsp;Este item já foi Doado (em <?php echo $item['data']->dtdoa_format?>)</button>
				<?php } elseif ( $login_data['logged_in'] ) : ?>
					<?php echo meinteressa_button( $item['data']->item_id ) ?>
				<?php endif; ?>
				
			</div>
		</div>
<?php
	} // foreach items
?>	
</div>
<script type="text/javascript">
	$(".itembox").fancybox({
		wrapCSS		: 'fancybox-item',
		padding		: 0,
		fitToView	: false,
		minWidth	: '500px',
		autoSize	: true,
		type		: 'ajax',
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none'
	});
</script>