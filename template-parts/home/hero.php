<?php
/**
 * Home — Hero.
 *
 * Título, subtítulo e CTA vêm do ACF da página inicial. Os logos que flutuam
 * ao redor são as Integrações marcadas com "Exibir na órbita do Hero".
 *
 * A órbita tem duas camadas na mesma caixa: as 16 bolhas, posicionadas em
 * porcentagens tiradas do frame do Figma (`.hero__logo--N`, no CSS), e uma
 * camada SVG vazia. Quem desenha as linhas é o front-page.js, medindo a posição
 * real do título e de cada bolha — assim elas continuam ancoradas em qualquer
 * largura e acompanham a animação de entrada.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow          = cliconnect_campo( 'hero_eyebrow' );
$titulo_destaque  = cliconnect_campo( 'hero_titulo_destaque' );
$titulo           = cliconnect_campo( 'hero_titulo' );
$subtitulo        = cliconnect_campo( 'hero_subtitulo' );

$orbita = cliconnect_posts(
	'cli_integracao',
	16,
	array(
		'meta_key'   => 'destaque_hero', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		'meta_value' => '1',             // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
	)
);

?>

<section class="hero">

	<?php if ( $orbita ) : ?>
	<div class="hero__orbita" aria-hidden="true">
		<svg class="hero__linhas" focusable="false" role="presentation">
			<defs class="hero__linhas-defs"></defs>
			<g class="hero__linhas-grupo"></g>
		</svg>

		<?php foreach ( $orbita as $indice => $logo ) : ?>
			<span class="hero__logo hero__logo--<?php echo (int) ( $indice + 1 ); ?>">
				<?php echo cliconnect_thumb( $logo->ID, 'medium', array( 'alt' => '' ) ); // wp_get_attachment_image escapa. ?>
			</span>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<div class="container hero__inner">

		<?php if ( $eyebrow ) : ?>
			<span class="eyebrow eyebrow--pill"><?php echo esc_html( $eyebrow ); ?></span>
		<?php endif; ?>

		<h1 class="hero__titulo">
			<?php if ( $titulo_destaque ) : ?>
				<span class="hero__linha-destaque"><?php echo esc_html( $titulo_destaque ); ?></span>
			<?php endif; ?>
			<?php echo esc_html( $titulo ); ?>
		</h1>

		<?php if ( $subtitulo ) : ?>
			<p class="hero__subtitulo"><?php echo esc_html( $subtitulo ); ?></p>
		<?php endif; ?>

		<div class="hero__acoes">
			<?php cliconnect_botao( 'hero_botao' ); ?>
		</div>

	</div>
</section>
