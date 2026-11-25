<?php
/**
 * CLI Flow — Selos de compliance e segurança.
 *
 * Faixa azul-marinho com brilho embaixo: título branco centralizado e um
 * cartão branco com os selos (CPT cli_selo, por menu_order) numa linha,
 * separados por fios.
 *
 * Campos ACF (group_cli_cli_flow, aba "15b · Selos"):
 *   cf_selos_titulo — título acima do cartão
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$selos = cliconnect_posts( 'cli_selo', 10 );

if ( ! $selos ) {
	return;
}

$titulo = cliconnect_campo_pagina( 'cf_selos_titulo' );
?>
<section class="cf-selos">
	<div class="container">

		<?php if ( $titulo ) : ?>
			<h2 class="cf-selos__titulo"><?php echo esc_html( $titulo ); ?></h2>
		<?php endif; ?>

		<ul class="cf-selos__cartao" role="list">
			<?php foreach ( $selos as $selo ) : ?>
				<li class="cf-selos__item">
					<?php
					echo cliconnect_thumb( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_thumb é wrapper de wp_get_attachment_image que escapa internamente.
						$selo->ID,
						'medium',
						array(
							'alt'   => get_the_title( $selo ),
							'class' => 'cf-selos__img',
						)
					);
					?>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</section>
