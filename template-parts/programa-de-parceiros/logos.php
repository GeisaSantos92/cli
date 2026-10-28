<?php
/**
 * Programa de Parceiros — Faixa de logos de clientes.
 *
 * Esteira de logos do CPT `cli_cliente`, no mesmo padrão da home e da página
 * de contato (template-parts/home/clientes.php): a lista é duplicada para o
 * loop infinito. A quantidade é editável na página (0 esconde a faixa).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$quantidade = absint( cliconnect_campo_pagina( 'pp_logos_quantidade', 0 ) );
$clientes   = $quantidade ? cliconnect_posts( 'cli_cliente', $quantidade ) : array();

if ( ! $clientes ) {
	return;
}
?>
<section class="pp-logos" aria-label="<?php esc_attr_e( 'Clientes', 'cli' ); ?>">
	<div class="clientes__pista">
		<div class="clientes__trilha">
			<?php for ( $passada = 0; $passada < 2; $passada++ ) : ?>
				<?php foreach ( $clientes as $cliente ) : ?>
					<span class="clientes__logo" <?php echo $passada ? 'aria-hidden="true"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- string literal hardcoded. ?>>
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
