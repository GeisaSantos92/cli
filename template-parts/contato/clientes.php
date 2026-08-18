<?php
/**
 * Contato — Carrossel de logos de clientes.
 *
 * Mesmo padrão da home (template-parts/home/clientes.php):
 * trilha infinita com duplicação de logos e animação marquee.
 *
 * Campos ACF (group_cli_contato, aba "2 · Clientes"):
 *   ct_clientes_subtitulo — texto acima dos logos
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$subtitulo = cliconnect_campo_pagina( 'ct_clientes_subtitulo' );
$clientes  = cliconnect_posts( 'cli_cliente' );

if ( ! $clientes ) {
	return;
}
?>
<section class="ct-clientes" aria-label="<?php esc_attr_e( 'Clientes que confiam na CLI Connect', 'cli' ); ?>">

	<?php if ( $subtitulo ) : ?>
		<p class="ct-clientes__subtitulo"><?php echo esc_html( $subtitulo ); ?></p>
	<?php endif; ?>

	<div class="clientes__pista">
		<div class="clientes__trilha">
			<?php for ( $passada = 0; $passada < 2; $passada++ ) : ?>
				<?php foreach ( $clientes as $cliente ) : ?>
					<span class="clientes__logo" <?php echo $passada ? 'aria-hidden="true"' : ''; ?>>
						<?php
						echo cliconnect_thumb( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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

</section><!-- .ct-clientes -->
