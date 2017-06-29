<h3 style="margin-top:0;">Categorias</h3>
<?php
	foreach( $categorias as $cat ) {
		echo "<p><strong style='color: #2b72a3;'>".$cat->nome."</strong><br>\n";
		echo "<span style='font-size:.9em;'>".$cat->descricao."</span></p>\n";
	}
?>