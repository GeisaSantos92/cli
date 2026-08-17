<?php
/**
 * CLI Signature — Para quem? (Cenários de uso).
 *
 * Header centralizado + grid 3×2 de cards com ícone de check,
 * título e descrição. Campos fixos numerados de 1 a 6 (ACF Free).
 *
 * Campos ACF (group_cli_cli_signature, aba "2 · Para quem"):
 *   cs_cenarios_eyebrow       — eyebrow
 *   cs_cenarios_titulo        — título H2
 *   cs_cenarios_texto         — parágrafo de apoio
 *   cs_cenarios_{1-6}_titulo  — título de cada card
 *   cs_cenarios_{1-6}_texto   — descrição de cada card
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow   = cliconnect_campo_pagina( 'cs_cenarios_eyebrow' );
$titulo    = cliconnect_campo_pagina( 'cs_cenarios_titulo' );
$subtitulo = cliconnect_campo_pagina( 'cs_cenarios_texto' );

$cards = array();
for ( $i = 1; $i <= 6; $i++ ) {
	$card_titulo = cliconnect_campo_pagina( 'cs_cenarios_' . $i . '_titulo' );
	$card_texto  = cliconnect_campo_pagina( 'cs_cenarios_' . $i . '_texto' );

	if ( $card_titulo ) {
		$cards[] = array(
			'titulo' => $card_titulo,
			'texto'  => $card_texto ?? '',
		);
	}
}

if ( ! $titulo && ! $cards ) {
	return;
}
?>
<section class="cs-cenarios secao">
	<div class="container">

		<header class="cs-cenarios__header">
			<?php if ( $eyebrow ) : ?>
				<p class="cs-cenarios__eyebrow eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<?php if ( $titulo ) : ?>
				<h2 class="cs-cenarios__titulo"><?php echo esc_html( $titulo ); ?></h2>
			<?php endif; ?>

			<?php if ( $subtitulo ) : ?>
				<p class="cs-cenarios__subtitulo"><?php echo esc_html( $subtitulo ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( $cards ) : ?>
			<ul class="cs-cenarios__grid" role="list">
				<?php foreach ( $cards as $card ) : ?>
					<li class="cs-card-cenario">

						<span class="cs-card-cenario__icone-wrap" aria-hidden="true">
							<?php echo cliconnect_icone( 'check', 16 ); // Saída controlada: cliconnect_icone valida contra lista. ?>
						</span>

						<div class="cs-card-cenario__corpo">
							<p class="cs-card-cenario__titulo"><?php echo esc_html( $card['titulo'] ); ?></p>
							<?php if ( $card['texto'] ) : ?>
								<p class="cs-card-cenario__texto"><?php echo esc_html( $card['texto'] ); ?></p>
							<?php endif; ?>
						</div>

					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>
</section>
