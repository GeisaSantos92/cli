<?php
/**
 * Home — "Tudo o que você precisa. Com custo previsível."
 *
 * A ilustração isométrica (placas, rótulos e descrições) é uma peça fechada:
 * entra como asset do tema, não como campos do ACF. Editáveis aqui são só o
 * título, o texto de apoio e o botão da chamada.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo = cliconnect_campo( 'camadas_titulo' );
$texto  = cliconnect_campo( 'camadas_texto' );

$grafico = cliconnect_imagem_tema(
	'section-camadas.png',
	array(
		'class'  => 'camadas__imagem',
		'alt'    => __( 'Camadas da plataforma: biblioteca de integrações, serviço incluso e plataforma global', 'cli' ),
		'width'  => 1312,
		'height' => 606,
	)
);
?>

<section class="camadas">
	<div class="container">

		<?php if ( $grafico ) : ?>
			<div class="camadas__grafico">
				<?php echo $grafico; // phpcs:ignore WordPress.Security.EscapeOutput -- montado com escape em cliconnect_imagem_tema(). ?>
			</div>
		<?php endif; ?>

		<div class="camadas__chamada">
			<?php if ( $titulo ) : ?>
				<h2 class="camadas__titulo"><?php echo esc_html( $titulo ); ?></h2>
			<?php endif; ?>

			<?php if ( $texto ) : ?>
				<p class="camadas__texto"><?php echo nl2br( esc_html( $texto ) ); ?></p>
			<?php endif; ?>

			<?php cliconnect_botao( 'camadas_botao' ); ?>
		</div>

	</div>
</section>
