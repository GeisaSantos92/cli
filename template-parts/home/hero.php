<?php
/**
 * Home — Hero.
 *
 * Título, subtítulo e CTA vêm do ACF da página inicial. Os logos que flutuam
 * ao redor são as Integrações marcadas com "Exibir na órbita do Hero", divididas
 * em dois grupos (esquerda/direita).
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

$metade    = (int) ceil( count( $orbita ) / 2 );
$esquerda  = array_slice( $orbita, 0, $metade );
$direita   = array_slice( $orbita, $metade );
?>

<section class="hero">

	<?php if ( $orbita ) : ?>
	<div class="hero__orbita" aria-hidden="true">
		<?php foreach ( array( 'esquerda' => $esquerda, 'direita' => $direita ) as $lado => $logos ) : ?>
			<?php if ( ! $logos ) { continue; } ?>
			<div class="hero__orbita-lado hero__orbita-lado--<?php echo esc_attr( $lado ); ?>">
				<?php foreach ( $logos as $logo ) : ?>
					<span class="hero__logo">
						<?php echo cliconnect_thumb( $logo->ID, 'medium', array( 'alt' => '' ) ); // wp_get_attachment_image escapa. ?>
					</span>
				<?php endforeach; ?>
			</div>
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
