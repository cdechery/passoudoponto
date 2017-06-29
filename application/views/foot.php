		</div><!-- wrap960 -->
	</section>
</div><!-- wrap -->

<footer>
	<div class="wrap960">
		
		<nav>
			<a href="<?php echo base_url('termos')?>">Termos de Serviço</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo base_url('sobre')?>">Sobre o site</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo base_url('contato')?>">Fale conosco</a>&nbsp;&nbsp;|&nbsp;&nbsp;
			<?php if( $login_data['logged_in'] ): ?>
			 <a href="<?php echo base_url('usuario/logout')?>">Sair</a>
			<?php else: ?>
			 <a id="escolhe_tipo_link" href="<?php echo base_url('usuario/novo')?>" class="escolhetipo_box fancybox.ajax">Faça seu cadastro</a></p>
			<?php endif; ?>
		</nav>

	</div>
</footer>
 
 <?php if( ENVIRONMENT!='production' ) { ?>
	<div id="error-details" style="display: none">&nbsp;</div>
<?php } // if ENV ?>
</body>
</html>