<?php
/**
 * Home — "Tecnologia de classe mundial" (plataforma Boomi).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo( 'boomi_eyebrow' );
$titulo  = cliconnect_campo( 'boomi_titulo' );
$texto   = cliconnect_campo( 'boomi_texto' );

/*
 * O cartão da direita é uma peça fechada (selo, logo e nota fazem parte da
 * arte): entra como asset do tema, não como campo do ACF.
 */
$cartao = cliconnect_imagem_tema(
	'section-plataforma-boomi.png',
	array(
		'class'  => 'boomi__cartao-imagem',
		'alt'    => __( 'Powered by Boomi — a única com 11 certificações globais do AICPA', 'cli' ),
		'width'  => 604,
		'height' => 403,
	)
);

if ( ! $titulo && ! $cartao ) {
	return;
}
?>

<section class="secao boomi">
	<div class="container">
		<div class="boomi__grid">

			<div class="boomi__texto">
				<?php if ( $eyebrow ) : ?>
					<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
				<?php endif; ?>

				<h2><?php echo esc_html( $titulo ); ?></h2>

				<?php if ( $texto ) : ?>
					<div class="boomi__descricao"><?php echo wp_kses_post( $texto ); ?></div>
				<?php endif; ?>
			</div>

			<?php if ( $cartao ) : ?>
				<div class="boomi__cartao">
					<?php echo $cartao; // phpcs:ignore WordPress.Security.EscapeOutput -- montado com escape em cliconnect_imagem_tema(). ?>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>
