<?php
/**
 * Home — frase de impacto ("Seus sistemas falam entre si.").
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$frase = cliconnect_campo( 'frase_texto' );

if ( ! $frase ) {
	return;
}
?>

<section class="frase">
	<div class="container">
		<p class="frase__texto"><?php echo esc_html( $frase ); ?></p>
	</div>
</section>
