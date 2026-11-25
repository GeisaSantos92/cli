<?php
/**
 * CLI Flow — Logos de clientes.
 *
 * Marquee infinito de logos do CPT cli_cliente.
 * Padrão idêntico ao template-parts/cli-connect/brands.php.
 *
 * Campos ACF (group_cli_flow, aba "3 · Logos"):
 *   cf_logos_titulo — legenda opcional acima da pista
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo   = cliconnect_campo_pagina( 'cf_logos_titulo' );
$clientes = cliconnect_posts( 'cli_cliente' );

if ( ! $clientes ) {
	return;
}
?>
<section class="cf-logos">
	<?php if ( $titulo ) : ?>
		<p class="cf-logos__titulo"><?php echo esc_html( $titulo ); ?></p>
	<?php endif; ?>

	<div class="cf-logos__pista">
		<div class="cf-logos__trilha">
			<?php for ( $passada = 0; $passada < 2; $passada++ ) : ?>
				<?php foreach ( $clientes as $cliente ) : ?>
					<span class="cf-logos__logo"<?php echo $passada ? ' aria-hidden="true"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- string literal hardcoded. ?>>
						<?php
						echo cliconnect_thumb( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_thumb é wrapper de wp_get_attachment_image que escapa internamente.
							$cliente->ID,
							'medium',
							array( 'alt' => $passada ? '' : get_the_title( $cliente ) )
						);
						?>
					</span>
				<?php endforeach; ?>
			<?php endfor; ?>
		</div>
	</div>
</section>
