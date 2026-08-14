<?php
/**
 * Home — frase de impacto animada (duas frases cicladas).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$frase_a  = cliconnect_campo( 'frase_texto' );
$frase_b  = cliconnect_campo( 'frase_texto_b' );
$destaque = cliconnect_campo( 'frase_destaque' );

if ( ! $frase_a && ! $frase_b ) {
	return;
}
?>

<section class="frase">
	<div class="container">
		<div class="frase__palco">

			<?php if ( $frase_a ) : ?>
			<p class="frase__texto frase__texto--a">
				<?php echo esc_html( $frase_a ); ?>
			</p>
			<?php endif; ?>

			<?php if ( $frase_b ) : ?>
			<p class="frase__texto frase__texto--b">
				<?php echo esc_html( $frase_b ); ?>
				<?php if ( $destaque ) : ?>
				<em class="frase__destaque"><?php echo esc_html( $destaque ); ?></em>
				<?php endif; ?>
			</p>
			<?php endif; ?>

		</div>
	</div>
</section>
