<?php
/**
 * CLI Connect — Solução (4 cards "Tudo em um única fatura").
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo = cliconnect_campo_pagina( 'cc_solucao_titulo' );

if ( ! $titulo ) {
	return;
}

$cards = array();
for ( $i = 1; $i <= 4; $i++ ) {
	$card_titulo = cliconnect_campo_pagina( "cc_solucao_{$i}_titulo" );
	if ( $card_titulo ) {
		$cards[] = $card_titulo;
	}
}
?>
<section class="cc-solucao secao">
	<div class="container">
		<h2 class="cc-solucao__titulo"><?php echo esc_html( $titulo ); ?></h2>

		<?php if ( $cards ) : ?>
			<div class="cc-solucao__cards">
				<?php foreach ( $cards as $card_titulo ) : ?>
					<div class="cc-solucao__card">
						<div class="cc-solucao__card-imagem">
							<?php cliconnect_imagem_tema( 'cc-solucao-bg.png', array( 'class' => 'cc-solucao__card-img', 'alt' => '' ) ); ?>
						</div>
						<div class="cc-solucao__card-corpo">
							<h3 class="cc-solucao__card-titulo"><?php echo esc_html( $card_titulo ); ?></h3>
							<div class="cc-solucao__card-linha"></div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
