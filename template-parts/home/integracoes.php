<?php
/**
 * Home — "Sua integração pode já estar pronta".
 *
 * Cartão centralizado no container, vazado entre a seção azul-clara de cima e a
 * branca de baixo: o painel azul cobre a metade esquerda da malha de logos
 * (CPT cli_integracao), cortando os que ficam atrás dele.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo( 'integracoes_eyebrow' );
$titulo  = cliconnect_campo( 'integracoes_titulo' );
$texto   = cliconnect_campo( 'integracoes_texto' );

if ( ! $titulo ) {
	return;
}
?>

<section class="integracoes">
	<div class="container">
		<div class="integracoes__wrapper">

			<div class="integracoes__imagem-wrap" aria-hidden="true">
				<?php
				echo cliconnect_imagem_tema( // phpcs:ignore WordPress.Security.EscapeOutput -- montado com escape em cliconnect_imagem_tema().
					'section-integracoes.png',
					array(
						'class'  => 'integracoes__imagem',
						'alt'    => '',
						'width'  => 616,
						'height' => 420,
					)
				);
				?>
			</div>

			<div class="integracoes__painel">
				<?php if ( $eyebrow ) : ?>
					<span class="eyebrow eyebrow--clara"><?php echo esc_html( $eyebrow ); ?></span>
				<?php endif; ?>

				<h2 class="integracoes__titulo"><?php echo nl2br( esc_html( $titulo ) ); ?></h2>

				<?php if ( $texto ) : ?>
					<p class="integracoes__texto"><?php echo esc_html( $texto ); ?></p>
				<?php endif; ?>

				<?php cliconnect_botao( 'integracoes_botao', 'botao botao--branco', '' ); ?>
			</div>

		</div>
	</div>
</section>
