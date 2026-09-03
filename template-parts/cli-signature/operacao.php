<?php
/**
 * CLI Signature — Operação Gerenciada.
 *
 * Layout: header centralizado (eyebrow + H2 + texto) + grid 2×2 de cards.
 * Cada card: ícone azul 40 px, título, texto descritivo.
 *
 * Campos ACF (group_cli_cli_signature, aba "7 · Operação Gerenciada"):
 *   cs_operacao_eyebrow     — eyebrow
 *   cs_operacao_titulo_1    — título H2 — linha 1
 *   cs_operacao_titulo_2    — título H2 — linha 2
 *   cs_operacao_texto       — parágrafo de apoio
 *   cs_operacao_{1-4}_titulo — título de cada card
 *   cs_operacao_{1-4}_texto  — texto de cada card
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow  = cliconnect_campo_pagina( 'cs_operacao_eyebrow' );
$titulo_1 = cliconnect_campo_pagina( 'cs_operacao_titulo_1' );
$titulo_2 = cliconnect_campo_pagina( 'cs_operacao_titulo_2' );
$texto    = cliconnect_campo_pagina( 'cs_operacao_texto' );

$cards = array();
for ( $i = 1; $i <= 4; $i++ ) {
	$titulo_card = cliconnect_campo_pagina( 'cs_operacao_' . $i . '_titulo' );
	$texto_card  = cliconnect_campo_pagina( 'cs_operacao_' . $i . '_texto' );
	if ( $titulo_card ) {
		$cards[] = array(
			'titulo' => $titulo_card,
			'texto'  => $texto_card,
		);
	}
}

if ( ! $titulo_1 && ! $cards ) {
	return;
}
?>
<section class="cs-operacao">
	<div class="container cs-operacao__inner">

		<div class="cs-operacao__header">
			<?php if ( $eyebrow ) : ?>
				<p class="cs-operacao__eyebrow eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<?php if ( $titulo_1 || $titulo_2 ) : ?>
				<h2 class="cs-operacao__titulo">
					<?php if ( $titulo_1 ) : ?>
						<span class="cs-operacao__titulo-linha"><?php echo esc_html( $titulo_1 ); ?></span>
					<?php endif; ?>
					<?php if ( $titulo_2 ) : ?>
						<span class="cs-operacao__titulo-linha"><?php echo esc_html( $titulo_2 ); ?></span>
					<?php endif; ?>
				</h2>
			<?php endif; ?>

			<?php if ( $texto ) : ?>
				<p class="cs-operacao__subtitulo"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $cards ) : ?>
			<ul class="cs-operacao__grid" role="list">
				<?php foreach ( $cards as $card ) : ?>
					<li class="cs-op-card">
						<div class="cs-op-card__icone-wrap" aria-hidden="true">
							<?php echo cliconnect_icone_ms( 'automation' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_icone_ms retorna HTML seguro de ícone Material Symbols. ?>
						</div>
						<p class="cs-op-card__titulo"><?php echo esc_html( $card['titulo'] ); ?></p>
						<?php if ( $card['texto'] ) : ?>
							<p class="cs-op-card__texto"><?php echo esc_html( $card['texto'] ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>
</section>
