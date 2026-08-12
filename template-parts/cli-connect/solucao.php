<?php
/**
 * CLI Connect — Solução (3 cards: Plataforma Global, Serviço Incluso, Biblioteca de Integrações).
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
for ( $i = 1; $i <= 3; $i++ ) {
	$card_titulo = cliconnect_campo_pagina( "cc_solucao_{$i}_titulo" );
	if ( ! $card_titulo ) {
		continue;
	}
	$bullets = array();
	for ( $j = 1; $j <= 3; $j++ ) {
		$bullet = cliconnect_campo_pagina( "cc_solucao_{$i}_bullet_{$j}" );
		if ( $bullet ) {
			$bullets[] = $bullet;
		}
	}
	$cards[] = array(
		'imagem'  => cliconnect_campo_pagina( "cc_solucao_{$i}_imagem" ),
		'titulo'  => $card_titulo,
		'texto'   => cliconnect_campo_pagina( "cc_solucao_{$i}_texto" ),
		'bullets' => $bullets,
	);
}
?>
<section class="cc-solucao">
	<div class="container">
		<h2 class="cc-solucao__titulo"><?php echo esc_html( $titulo ); ?></h2>

		<?php if ( $cards ) : ?>
			<div class="cc-solucao__cards">
				<?php foreach ( $cards as $card ) : ?>
					<div class="cc-solucao__card">
						<?php if ( $card['imagem'] ) : ?>
							<div class="cc-solucao__card-imagem">
								<?php echo wp_get_attachment_image( (int) $card['imagem'], 'large', false, array( 'class' => 'cc-solucao__card-img', 'alt' => '' ) ); ?>
							</div>
						<?php endif; ?>

						<div class="cc-solucao__card-corpo">
							<h3 class="cc-solucao__card-titulo"><?php echo esc_html( $card['titulo'] ); ?></h3>

							<?php if ( $card['texto'] ) : ?>
								<p class="cc-solucao__card-texto"><?php echo esc_html( $card['texto'] ); ?></p>
							<?php endif; ?>

							<?php if ( $card['bullets'] ) : ?>
								<ul class="cc-solucao__card-bullets">
									<?php foreach ( $card['bullets'] as $bullet ) : ?>
										<li class="cc-solucao__card-bullet">
											<?php echo cliconnect_icone( 'verificado', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
											<span><?php echo esc_html( $bullet ); ?></span>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
