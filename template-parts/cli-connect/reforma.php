<?php
/**
 * CLI Connect — Reforma Tributária.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'cc_reforma_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'cc_reforma_titulo' );
$texto   = cliconnect_campo_pagina( 'cc_reforma_texto' );

if ( ! $titulo ) {
	return;
}

$cards = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$card_titulo = cliconnect_campo_pagina( "cc_reforma_{$i}_titulo" );
	if ( $card_titulo ) {
		$cards[] = $card_titulo;
	}
}
?>
<section class="cc-reforma secao">
	<div class="container">
		<div class="cc-reforma__cabecalho">
			<?php if ( $eyebrow ) : ?>
				<p class="cc-reforma__eyebrow eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<h2 class="cc-reforma__titulo"><?php echo esc_html( $titulo ); ?></h2>

			<?php if ( $texto ) : ?>
				<p class="cc-reforma__texto"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $cards ) : ?>
			<div class="cc-reforma__cards">
				<?php foreach ( $cards as $card_titulo ) : ?>
					<article class="cc-reforma__card">
						<div class="cc-reforma__card-icone">
							<?php echo cliconnect_icone( 'verificado' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<h3 class="cc-reforma__card-titulo"><?php echo esc_html( $card_titulo ); ?></h3>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
