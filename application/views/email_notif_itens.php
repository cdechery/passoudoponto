<h3>Olá <?php echo $nome?>,</h3>
<p>De acordo com seus Interesses cadastrados em nosso site, estamos enviando este email com Itens nas categorias (e distâncias) que você configurou.</p>
<table width="95%" border="0" cellpadding="4" cellspacing="4">
	<tr>
<?php
	$qtd_itens = count($itens);
	$itens_exibidos = 0;
	$itens_por_linha = 3;
	$total_itens_exibidos = 0;

	foreach ($itens as $item) {
		$img = item_image($item['imagens'][0], 120);
?>
	<td height="140" width="33%" align="center" valign="top">
		<img src="<?php echo $img?>"><br>
		<?php echo trim($item['titulo'])?>
	</td>
<?php
		$itens_exibidos++;
		$total_itens_exibidos++;

		if( $total_itens_exibidos==$qtd_itens ) {
			for($i=($itens_por_linha-$itens_exibidos); $i>0; $i--) {
				echo "<td width=\"33%\">&nbsp;</td>\n";
				$itens_exibidos++;
			}
		}

		if( $itens_exibidos==$itens_por_linha ) {
			$itens_exibidos = 0;
			echo "	</tr>\n";
		} 
	} // foreach

?>
</table>
<?php
	$item_ids = array_keys( $itens );
	$url = base_url('item/listar/'.implode('/', $item_ids));

?>
<br>
<p style="clear: both">
Para ver mais detalhes sobre os itens e entrar em contato com os doadores, clique <a href="<?php echo $url?>">aqui</a>.
</p>