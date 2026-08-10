<?php
/**
 * Trabalhe Conosco — Frase de impacto em duas partes.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$parte_1 = cliconnect_campo_pagina( 'tc_frase_parte_1' );
$parte_2 = cliconnect_campo_pagina( 'tc_frase_parte_2' );

if ( ! $parte_1 && ! $parte_2 ) {
	return;
}
?>

<section class="secao tc-frase">
	<div class="container">
		<p class="tc-frase__texto">
			<?php if ( $parte_1 ) : ?>
				<?php echo esc_html( $parte_1 ); ?>
			<?php endif; ?>
			<?php if ( $parte_2 ) : ?>
				<span class="tc-frase__destaque"><?php echo esc_html( $parte_2 ); ?></span>
			<?php endif; ?>
		</p>
	</div>
</section>
