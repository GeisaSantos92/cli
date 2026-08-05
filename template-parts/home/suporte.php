<?php
/**
 * Home — "Você não fica sozinho nunca" (canais de atendimento).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo( 'suporte_eyebrow' );
$titulo  = cliconnect_campo( 'suporte_titulo' );
$texto   = cliconnect_campo( 'suporte_texto' );

/*
 * O anel de canais é uma peça fechada (retrato, ícones e rótulos fazem parte
 * da arte): entra como asset do tema, não como campos do ACF.
 */
$visual = cliconnect_imagem_tema(
	'section-atendimento.png',
	array(
		'class'  => 'suporte__imagem',
		'alt'    => __( 'Canais de atendimento: portal, WhatsApp e e-mail', 'cli' ),
		'width'  => 636,
		'height' => 520,
	)
);

if ( ! $titulo ) {
	return;
}
?>

<section class="secao suporte">
	<div class="container">
		<div class="suporte__grid">

			<div class="suporte__texto">
				<?php if ( $eyebrow ) : ?>
					<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
				<?php endif; ?>

				<h2 class="suporte__titulo"><?php echo esc_html( $titulo ); ?></h2>

				<?php if ( $texto ) : ?>
					<div class="suporte__descricao"><?php echo wp_kses_post( $texto ); ?></div>
				<?php endif; ?>

				<?php cliconnect_botao( 'suporte_botao', 'botao botao--primario', '' ); ?>
			</div>

			<?php if ( $visual ) : ?>
				<div class="suporte__visual">
					<?php echo $visual; // phpcs:ignore WordPress.Security.EscapeOutput -- montado com escape em cliconnect_imagem_tema(). ?>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>
