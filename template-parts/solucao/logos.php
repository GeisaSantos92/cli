<?php
/**
 * Solução — Faixa de logos de clientes.
 *
 * Microcopy à esquerda e os logos dos clientes selecionados à direita, cada um
 * em sua célula com divisória vertical.
 *
 * Campos ACF (group_cli_solucao, aba "4 · Logos"):
 *   solucao_logos_texto, solucao_logos_clientes (relationship → cli_cliente).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$texto    = cliconnect_campo_pagina( 'solucao_logos_texto' );
$clientes = cliconnect_campo_pagina( 'solucao_logos_clientes', array() );

if ( ! is_array( $clientes ) ) {
	$clientes = array();
}

if ( ! $texto || ! $clientes ) {
	return;
}
?>
<section class="sh-logos" aria-label="<?php esc_attr_e( 'Clientes do segmento', 'cli' ); ?>">
	<div class="container">
		<div class="sh-logos__grid">

			<p class="sh-logos__texto"><?php echo esc_html( $texto ); ?></p>

			<div class="sh-logos__lista">
				<?php foreach ( $clientes as $cliente_id ) : ?>
					<?php $logo = cliconnect_thumb( (int) $cliente_id, 'medium' ); ?>
					<?php if ( $logo ) : ?>
						<div class="sh-logos__item">
							<?php echo $logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div><!-- .sh-logos__lista -->

		</div><!-- .sh-logos__grid -->
	</div><!-- .container -->
</section><!-- .sh-logos -->
