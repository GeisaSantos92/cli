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

$alt_grafico = __( 'Camadas da plataforma: biblioteca de integrações, serviço incluso e plataforma global', 'cli' );
$src_desktop = esc_url( get_theme_file_uri( '/assets/img/section-camadas.png' ) );
$src_mobile  = esc_url( get_theme_file_uri( '/assets/img/section-camadas-mobile.png' ) );
$tem_mobile  = file_exists( get_theme_file_path( '/assets/img/section-camadas-mobile.png' ) );

$grafico  = '<picture>';
if ( $tem_mobile ) {
	$grafico .= '<source media="(max-width: 780px)" srcset="' . $src_mobile . '" width="1312" height="2038">';
}
$grafico .= '<img'
	. ' class="camadas__imagem"'
	. ' src="' . $src_desktop . '"'
	. ' alt="' . esc_attr( $alt_grafico ) . '"'
	. ' width="1312"'
	. ' height="606"'
	. ' loading="lazy"'
	. ' decoding="async"'
	. '>';
$grafico .= '</picture>';
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
