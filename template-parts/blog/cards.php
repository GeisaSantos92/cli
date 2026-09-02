<?php
/**
 * Blog — Seção cards: grade com os demais artigos da página atual.
 *
 * @package Cliconnect
 *
 * @var array $args {
 *     @type WP_Post[] $posts Posts do grid (todos, exceto o destaque na 1ª página).
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$posts_grade = $args['posts'] ?? array();

if ( ! $posts_grade ) {
	return;
}
?>

<section class="blog-cards">
	<div class="container">
		<div class="blog-cards__grade">

			<?php foreach ( $posts_grade as $artigo ) :
				$titulo    = get_the_title( $artigo );
				$permalink = get_permalink( $artigo );
				$data      = get_the_date( 'j F Y', $artigo );
				$cats      = get_the_category( $artigo->ID );
				$categoria = ! empty( $cats ) ? $cats[0]->name : '';
			?>

				<a
					class="blog-card"
					href="<?php echo esc_url( $permalink ); ?>"
					aria-label="<?php echo esc_attr( $titulo ); ?>"
				>
					<div class="blog-card__imagem">
						<?php echo cliconnect_thumb( $artigo->ID, 'medium', array( 'alt' => '' ) ); ?>
					</div>

					<div class="blog-card__corpo">
						<?php if ( $categoria ) : ?>
							<div class="blog-tag">
								<span><?php echo esc_html( mb_strtoupper( $categoria ) ); ?></span>
							</div>
						<?php endif; ?>

						<h2 class="blog-card__titulo">
							<?php echo esc_html( $titulo ); ?>
						</h2>

						<p class="blog-card__data">
							<?php echo esc_html( $data ); ?>
						</p>
					</div>
				</a>

			<?php endforeach; ?>

		</div>
	</div>
</section>
