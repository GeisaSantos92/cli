<?php
/**
 * CLI Flow — Dores.
 *
 * Grid 3×2 de cartões de citação representando dores por departamento.
 * Cada cartão: frase entre aspas (as aspas vêm do CSS) + ícone em quadrado
 * azul degradê e rótulo do departamento na base.
 *
 * Campos ACF (group_cli_flow, aba "4 · Dores"):
 *   cf_dores_titulo          — H2 da seção
 *   cf_dores_{n}_citacao     — frase da dor (n = 1…6)
 *   cf_dores_{n}_departamento — rótulo do departamento (n = 1…6)
 *   cf_dores_{n}_icone       — ligatura do Material Symbols (n = 1…6)
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo = cliconnect_campo_pagina( 'cf_dores_titulo' );

if ( ! $titulo ) {
	return;
}

$cards = cliconnect_lista_numerada_pagina(
	'cf_dores_%d_citacao',
	6,
	static function ( $i, $citacao ) {
		return array(
			'citacao'      => $citacao,
			'departamento' => cliconnect_campo_pagina( "cf_dores_{$i}_departamento" ),
			'icone'        => cliconnect_campo_pagina( "cf_dores_{$i}_icone" ),
		);
	}
);

if ( ! $cards ) {
	return;
}
?>
<section class="cf-dores">
	<div class="container">

		<h2 class="cf-dores__titulo"><?php echo esc_html( $titulo ); ?></h2>

		<div class="cf-dores__grid">
			<?php foreach ( $cards as $card ) : ?>
				<?php if ( ! $card['citacao'] ) : ?>
					<?php continue; ?>
				<?php endif; ?>
				<div class="cf-dores__card">
					<blockquote class="cf-dores__citacao">
						<p><?php echo esc_html( $card['citacao'] ); ?></p>
					</blockquote>
					<?php if ( $card['departamento'] ) : ?>
						<p class="cf-dores__departamento">
							<?php if ( $card['icone'] ) : ?>
								<span class="cf-icone"><?php echo cliconnect_icone_ms( $card['icone'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escapa internamente. ?></span>
							<?php endif; ?>
							<?php echo esc_html( $card['departamento'] ); ?>
						</p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>
