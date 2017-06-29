<?php 
	$divClass = $colWidth = "";
	if( isset($page) ) {
		$divClass = "class='roundbox clearfix'";
		$colWidth = "style='width:430px'";
	}
?>

<div id="cadastro_window" <?php echo $divClass?>>
	<p>No <strong>interessa.org</strong> você pode se cadastrar de duas maneiras: como Pessoa ou Instituição.<br>
	Veja qual a que se encaixa melhor em seu perfil:</p>
	<div class="col" <?php echo $colWidth?>>
		<img src="<?php echo img_url('pixel.gif')?>" id="pessoa">
		<h3>Pessoa</h3>
		<p>Para fazer e receber doações - foco em <b>fazer</b>. Só aparecem no mapa Pessoas com Itens disponíveis para doar.</p>
		<div class="go"><a href="<?php echo base_url('usuario/novo/P')?>">Faça seu cadastro como Pessoa >></a></div>
	</div>
	<div class="col" <?php echo $colWidth?>>
		<img src="<?php echo img_url('pixel.gif')?>" id="instit">
		<h3>Instituição</h3>
		<p>Para fazer e receber doações - foco em <b>receber</b>. Todas aparecem no mapa, independente de Itens para doar. Os itens de Instituições não aparecem no mapa, apenas na listagem.</p>
		<div class="go"><a href="<?php echo base_url('usuario/novo/I')?>">Faça seu cadastro como Instituição >></a></div>
	</div>
</div>
		