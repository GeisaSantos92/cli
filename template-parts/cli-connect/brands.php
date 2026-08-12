<?php
/**
 * CLI Connect — Brands.
 *
 * Marquee infinito de logos de clientes. Logos em grayscale por padrão;
 * cor original no hover (via CSS filter).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo   = cliconnect_campo_pagina( 'cc_brands_titulo' );
$clientes = cliconnect_posts( 'cli_cliente' );

if ( ! $clientes ) {
	return;
}
?>
<section class="cc-brands">
	<?php if ( $titulo ) : ?>
		<p class="cc-brands__titulo"><?php echo esc_html( $titulo ); ?></p>
	<?php endif; ?>

	<div class="cc-brands__pista">
		<div class="cc-brands__trilha">
			<?php for ( $passada = 0; $passada < 2; $passada++ ) : ?>
				<?php foreach ( $clientes as $cliente ) : ?>
					<span class="cc-brands__logo"<?php echo $passada ? ' aria-hidden="true"' : ''; ?>>
						<?php
						echo cliconnect_thumb(
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
