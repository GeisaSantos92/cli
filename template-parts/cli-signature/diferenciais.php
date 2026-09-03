<?php
/**
 * CLI Signature — Diferenciais (Mais do que uma plataforma).
 *
 * Header centralizado com título em duas linhas + grid 3×2 de cards
 * com ícone em fundo azul, título e descrição.
 * Campos fixos 1–6 (ACF Free, sem Repeater).
 *
 * Campos ACF (group_cli_cli_signature, aba "4 · Diferenciais"):
 *   cs_diferenciais_titulo_1      — linha 1 do título H2
 *   cs_diferenciais_titulo_2      — linha 2 do título H2
 *   cs_diferenciais_texto         — parágrafo de apoio
 *   cs_diferenciais_{1-6}_titulo  — título do card
 *   cs_diferenciais_{1-6}_texto   — descrição do card
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo_1  = cliconnect_campo_pagina( 'cs_diferenciais_titulo_1' );
$titulo_2  = cliconnect_campo_pagina( 'cs_diferenciais_titulo_2' );
$subtitulo = cliconnect_campo_pagina( 'cs_diferenciais_texto' );

$cards = array();
for ( $i = 1; $i <= 6; $i++ ) {
	$card_titulo = cliconnect_campo_pagina( 'cs_diferenciais_' . $i . '_titulo' );
	$card_texto  = cliconnect_campo_pagina( 'cs_diferenciais_' . $i . '_texto' );

	if ( $card_titulo ) {
		$cards[] = array(
			'titulo' => $card_titulo,
			'texto'  => $card_texto ?? '',
		);
	}
}

if ( ! $titulo_1 && ! $cards ) {
	return;
}
?>
<section class="cs-diferenciais">
	<div class="container cs-diferenciais__inner">

		<header class="cs-diferenciais__header">
			<?php if ( $titulo_1 || $titulo_2 ) : ?>
				<h2 class="cs-diferenciais__titulo">
					<?php if ( $titulo_1 ) : ?>
						<span class="cs-diferenciais__titulo-linha"><?php echo esc_html( $titulo_1 ); ?></span>
					<?php endif; ?>
					<?php if ( $titulo_2 ) : ?>
						<span class="cs-diferenciais__titulo-linha"><?php echo esc_html( $titulo_2 ); ?></span>
					<?php endif; ?>
				</h2>
			<?php endif; ?>

			<?php if ( $subtitulo ) : ?>
				<p class="cs-diferenciais__subtitulo"><?php echo esc_html( $subtitulo ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( $cards ) : ?>
			<ul class="cs-diferenciais__grid" role="list">
				<?php foreach ( $cards as $card ) : ?>
					<li class="cs-dif-card">

						<span class="cs-dif-card__icone-wrap" aria-hidden="true">
							<?php echo cliconnect_icone_ms( 'automation' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_icone_ms retorna HTML seguro de ícone Material Symbols. ?>
						</span>

						<h3 class="cs-dif-card__titulo"><?php echo esc_html( $card['titulo'] ); ?></h3>

						<?php if ( $card['texto'] ) : ?>
							<p class="cs-dif-card__texto"><?php echo esc_html( $card['texto'] ); ?></p>
						<?php endif; ?>

					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>
</section>
