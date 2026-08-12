<?php
/**
 * CLI Connect — Pilares (vantagens).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'cc_pilares_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'cc_pilares_titulo' );

if ( ! $titulo ) {
	return;
}

$cards = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$card_titulo = cliconnect_campo_pagina( "cc_pilar_{$i}_titulo" );
	if ( ! $card_titulo ) {
		continue;
	}
	$itens = array();
	for ( $j = 1; $j <= 3; $j++ ) {
		$item = cliconnect_campo_pagina( "cc_pilar_{$i}_item_{$j}" );
		if ( $item ) {
			$itens[] = $item;
		}
	}
	$cards[] = array(
		'imagem' => cliconnect_campo_pagina( "cc_pilar_{$i}_imagem" ),
		'titulo' => $card_titulo,
		'texto'  => cliconnect_campo_pagina( "cc_pilar_{$i}_texto" ),
		'itens'  => $itens,
	);
}
?>
<section class="cc-pilares secao">
	<div class="container">
		<?php if ( $eyebrow ) : ?>
			<p class="cc-pilares__eyebrow eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
		<?php endif; ?>

		<h2 class="cc-pilares__titulo"><?php echo esc_html( $titulo ); ?></h2>

		<?php if ( $cards ) : ?>
			<div class="cc-pilares__cards">
				<?php foreach ( $cards as $card ) : ?>
					<article class="cc-pilares__card">
						<?php if ( $card['imagem'] ) : ?>
							<div class="cc-pilares__card-img">
								<?php echo wp_get_attachment_image( (int) $card['imagem'], 'large', false, array( 'alt' => '' ) ); ?>
							</div>
						<?php endif; ?>

						<div class="cc-pilares__card-corpo">
							<h3 class="cc-pilares__card-titulo"><?php echo esc_html( $card['titulo'] ); ?></h3>

							<?php if ( $card['texto'] ) : ?>
								<p class="cc-pilares__card-texto"><?php echo esc_html( $card['texto'] ); ?></p>
							<?php endif; ?>

							<?php if ( $card['itens'] ) : ?>
								<ul class="cc-pilares__card-lista">
									<?php foreach ( $card['itens'] as $item ) : ?>
										<li class="cc-pilares__card-item"><?php echo esc_html( $item ); ?></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
