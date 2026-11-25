<?php
/**
 * CLI Flow — Grade de sistemas integrados.
 *
 * Grade de 8 colunas com o logo de cada sistema numa caixa branca de borda
 * sutil, logo abaixo da seção de Integrações. Posições vazias são puladas.
 *
 * Campos ACF (group_cli_cli_flow, aba "10b · Sistemas"):
 *   cf_sistemas_logo_{1-24} — logo (ID do anexo; o texto alternativo vem do anexo)
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$logos = cliconnect_lista_numerada_pagina( 'cf_sistemas_logo_%d', 24 );
$logos = array_filter( array_map( 'absint', $logos ) );

if ( ! $logos ) {
	return;
}
?>
<section class="cf-sistemas">
	<div class="container">
		<ul class="cf-sistemas__grade" role="list">
			<?php foreach ( $logos as $logo_id ) : ?>
				<li class="cf-sistemas__item">
					<?php
					echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapa internamente.
						$logo_id,
						'medium',
						false,
						array(
							'class'    => 'cf-sistemas__logo',
							'loading'  => 'lazy',
							'decoding' => 'async',
						)
					);
					?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
