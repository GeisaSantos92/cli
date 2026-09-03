<?php
/**
 * Trabalhe Conosco — Últimas do blog.
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

$titulo = cliconnect_campo_pagina( 'tc_blog_titulo' );
?>

<section class="secao blog tc-blog">
	<div class="container">

		<?php if ( $titulo ) : ?>
			<header class="blog__cabecalho">
				<h2 class="blog__titulo"><?php echo esc_html( $titulo ); ?></h2>
			</header>
		<?php endif; ?>

		<div class="blog__grid">
			<?php foreach ( $posts_recentes as $artigo ) : ?>
				<article class="card">
					<?php if ( has_post_thumbnail( $artigo->ID ) ) : ?>
						<a class="card__media" href="<?php echo esc_url( get_permalink( $artigo ) ); ?>">
							<?php echo cliconnect_thumb( $artigo->ID, 'large', array( 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_thumb é wrapper de wp_get_attachment_image que escapa internamente. ?>
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
