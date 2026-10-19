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

// A arte tem texto embutido: cada idioma tem a sua, no desktop e no mobile.
$arq_desktop = cliconnect_asset_do_idioma( '/assets/img/section-camadas.png' );
$arq_mobile  = cliconnect_asset_do_idioma( '/assets/img/section-camadas-mobile.png' );

/*
 * <source> da arte: a versão WebP vem antes do PNG quando existe ao lado.
 * As dimensões saem do próprio arquivo — a arte mobile muda de proporção
 * entre os idiomas.
 */
$fontes = static function ( $relativo, $media = '' ) {
	if ( ! file_exists( get_theme_file_path( $relativo ) ) ) {
		return '';
	}

	$tamanho = wp_getimagesize( get_theme_file_path( $relativo ) );
	$attr    = $media ? ' media="' . esc_attr( $media ) . '"' : '';
	$attr   .= $tamanho ? ' width="' . (int) $tamanho[0] . '" height="' . (int) $tamanho[1] . '"' : '';
	$webp    = (string) preg_replace( '/\.png$/i', '.webp', $relativo );
	$html    = '';

	if ( file_exists( get_theme_file_path( $webp ) ) ) {
		$html .= '<source type="image/webp"' . $attr . ' srcset="' . esc_url( get_theme_file_uri( $webp ) ) . '">';
	}

	if ( $media ) {
		$html .= '<source' . $attr . ' srcset="' . esc_url( get_theme_file_uri( $relativo ) ) . '">';
	}

	return $html;
};

$grafico  = '<picture>';
$grafico .= $fontes( $arq_mobile, '(max-width: 780px)' );
$grafico .= $fontes( $arq_desktop );
$grafico .= '<img'
	. ' class="camadas__imagem"'
	. ' src="' . esc_url( get_theme_file_uri( $arq_desktop ) ) . '"'
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
