<?php
/**
 * Home — últimas postagens do blog (post nativo).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$posts_recentes = cliconnect_posts(
	'post',
	3,
	array(
		'orderby' => 'date',
		'order'   => 'DESC',
	)
);

if ( ! $posts_recentes ) {
	return;
}

$titulo = cliconnect_campo( 'blog_titulo' );
$link   = cliconnect_campo( 'blog_link', array() );
?>

<section class="secao blog">
	<div class="container">

		<header class="blog__cabecalho">
			<?php if ( $titulo ) : ?>
				<h2 class="blog__titulo"><?php echo esc_html( $titulo ); ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( $link['url'] ) ) : ?>
				<a class="link-seta" href="<?php echo esc_url( $link['url'] ); ?>">
					<?php echo esc_html( $link['title'] ?? '' ); ?>
					<?php echo cliconnect_icone( 'seta-direita', 16 ); // SVG estático. ?>
				</a>
			<?php endif; ?>
		</header>

		<div class="blog__grid">
			<?php foreach ( $posts_recentes as $artigo ) : ?>
				<article class="card">
					<?php if ( has_post_thumbnail( $artigo->ID ) ) : ?>
						<a class="card__media" href="<?php echo esc_url( get_permalink( $artigo ) ); ?>">
							<?php echo cliconnect_thumb( $artigo->ID, 'large', array( 'alt' => get_the_title( $artigo ) ) ); ?>
						</a>
					<?php endif; ?>

					<div class="card__body">
						<h3 class="card__title">
							<a href="<?php echo esc_url( get_permalink( $artigo ) ); ?>">
								<?php echo esc_html( get_the_title( $artigo ) ); ?>
							</a>
						</h3>
					</div>
				</article>
			<?php endforeach; ?>
		</div>

	</div>
</section>
