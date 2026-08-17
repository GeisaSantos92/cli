<?php
/**
 * CLI Signature — Pilares (A experiência enterprise).
 *
 * Header centralizado + 3 cards em linha, cada um com imagem,
 * título e descrição. Campos fixos 1-3 (ACF Free).
 *
 * Campos ACF (group_cli_cli_signature, aba "3 · Pilares"):
 *   cs_pilares_eyebrow        — eyebrow
 *   cs_pilares_titulo         — título H3
 *   cs_pilares_texto          — parágrafo de apoio
 *   cs_pilares_{1-3}_imagem   — imagem do card (ID do anexo)
 *   cs_pilares_{1-3}_titulo   — título do card
 *   cs_pilares_{1-3}_texto    — descrição do card
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow   = cliconnect_campo_pagina( 'cs_pilares_eyebrow' );
$titulo    = cliconnect_campo_pagina( 'cs_pilares_titulo' );
$subtitulo = cliconnect_campo_pagina( 'cs_pilares_texto' );

$cards = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$card_titulo  = cliconnect_campo_pagina( 'cs_pilares_' . $i . '_titulo' );
	$card_texto   = cliconnect_campo_pagina( 'cs_pilares_' . $i . '_texto' );
	$card_imagem  = (int) ( cliconnect_campo_pagina( 'cs_pilares_' . $i . '_imagem' ) ?? 0 );

	if ( $card_titulo ) {
		$cards[] = array(
			'titulo'  => $card_titulo,
			'texto'   => $card_texto ?? '',
			'imagem'  => $card_imagem,
		);
	}
}

if ( ! $titulo && ! $cards ) {
	return;
}
?>
<section class="cs-pilares">
	<div class="container">

		<header class="cs-pilares__header">
			<?php if ( $eyebrow ) : ?>
				<p class="cs-pilares__eyebrow eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<?php if ( $titulo ) : ?>
				<h2 class="cs-pilares__titulo"><?php echo esc_html( $titulo ); ?></h2>
			<?php endif; ?>

			<?php if ( $subtitulo ) : ?>
				<p class="cs-pilares__subtitulo"><?php echo esc_html( $subtitulo ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( $cards ) : ?>
			<div class="cs-pilares__grid">
				<?php foreach ( $cards as $card ) : ?>
					<article class="cs-pilar-card">

						<?php if ( $card['imagem'] ) : ?>
							<div class="cs-pilar-card__imagem">
								<?php
								echo wp_get_attachment_image(
									$card['imagem'],
									'large',
									false,
									array(
										'class'   => 'cs-pilar-card__img',
										'alt'     => '',
										'loading' => 'lazy',
										'decoding' => 'async',
									)
								);
								?>
							</div>
						<?php endif; ?>

						<div class="cs-pilar-card__corpo">
							<h3 class="cs-pilar-card__titulo"><?php echo esc_html( $card['titulo'] ); ?></h3>
							<?php if ( $card['texto'] ) : ?>
								<p class="cs-pilar-card__texto"><?php echo esc_html( $card['texto'] ); ?></p>
							<?php endif; ?>
						</div>

					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

	</div>
</section>
