<header id="itens" class="clearfix">
	<h2>Itens</h2>
	<?php echo adicionar_button("float: right;", base_url('item/novo') )?>
</header>

<div id="show_itens" class="clearfix">

	<?php
		foreach ($items as $item_id => $item) {
	?>
		<div class="item_single">
			<button class="itemdel" data-itemid="<?php echo $item_id; ?>"><i class="fa fa-times"></i></button>
			<div class="thumbs">
				<?php if( count($item['imagens']) ) {
					foreach ($item['imagens'] as $file) {
						$thumb = thumb_filename($file, 120);
						echo "<img src=".user_img_url($thumb)." />";
					}
				} else {
						echo "<div style='text-align: center;'>Sem imagens</div>";
				} ?>
			</div>
			<h3><?php echo $item['data']->titulo ?></h3>
			<p class="data-cadastro">Cadastrado em: <?php echo $item['data']->dtinc_format?></p>
			<div class="descricao"><?php echo nl2br(wordwrap($item['data']->descricao,70)) ?></div class="descricao">
			<div class="action">
				
				<?php if ($item['data']->status === 'D') { ?>
					<button class="item-modify disabled" disabled data-itemid="<?php echo $item_id; ?>" title="Editar Item"><i class="fa fa-pencil"></i>&nbsp;Editar Item</button>
					<button class="item-status disabled" disabled data-itemid="<?php echo $item_id; ?>" data-status="I"><i class="fa fa-check-square-o"></i>&nbsp;Item Ativo</button>
					<button class="item-doado active" data-itemid="<?php echo $item_id; ?>" data-status="D"><i class="fa fa-check-square-o"></i>&nbsp;Doado (em <?php echo $item['data']->dtdoa_format?>)</button>
				
				<?php } else { ?>
					
					<button class="item-modify" data-itemid="<?php echo $item_id; ?>" title="Editar Item"><i class="fa fa-pencil"></i>&nbsp;Editar Item</button>
					
					<?php if ($item['data']->status === 'A') { ?>
						<button class="item-status" data-itemid="<?php echo $item_id; ?>" data-status="A"><i class="fa fa-square-o"></i>&nbsp;Desativar Item</button>
						<button class="item-doado" data-itemid="<?php echo $item_id; ?>"><i class="fa fa-check-square-o"></i>&nbsp;Marcar como Doado</button>
					<?php } else /* item cancelado não pode ser marcado como doado */ { ?>
						<button class="item-status active" data-itemid="<?php echo $item_id; ?>" data-status="I"><i class="fa fa-check-square-o"></i>&nbsp;Ativar Item</button>
						<button class="item-doado disabled" disabled data-itemid="<?php echo $item_id; ?>"><i class="fa fa-check-square-o"></i>&nbsp;Marcar como Doado</button>
					<?php } ?>
				
				<?php } ?>
				
			</div>
		</div>
	<?php
		}
	?>	

</div>