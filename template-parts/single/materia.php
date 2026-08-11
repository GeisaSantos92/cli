<?php
/**
 * Post interna — Matéria: título, subtítulo, data, imagem, conteúdo e tags.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cliconnect_titulo    = get_the_title();
$cliconnect_subtitulo = get_the_excerpt();
$cliconnect_data      = get_the_date( 'j F Y' );
$cliconnect_cats      = get_the_category();
?>

<section class="post-secao">
	<div class="post-secao__inner">

		<article <?php post_class( 'post-materia' ); ?>>

			<!-- Cabeçalho -->
			<header class="post-materia__heading">
				<h1 class="post-materia__titulo">
					<?php echo esc_html( $cliconnect_titulo ); ?>
				</h1>
			</header>

			<?php if ( $cliconnect_subtitulo ) : ?>
			<div class="post-materia__subtitulo-wrapper">
				<p class="post-materia__subtitulo">
					<?php echo esc_html( $cliconnect_subtitulo ); ?>
				</p>
			</div>
			<?php endif; ?>

			<div class="post-materia__data-wrapper">
				<p class="post-materia__data">
					<?php
					printf(
						/* translators: %s: data de publicação */
						esc_html__( 'Publicado em: %s', 'cli' ),
						esc_html( $cliconnect_data )
					);
					?>
				</p>
			</div>

			<!-- Corpo: imagem + texto -->
			<div class="post-materia__corpo">

				<?php if ( has_post_thumbnail() ) : ?>
				<div class="post-materia__imagem">
					<?php the_post_thumbnail( 'large', array( 'loading' => 'eager' ) ); ?>
				</div>
				<?php endif; ?>

				<div class="post-materia__texto">
					<?php the_content(); ?>
				</div>

			</div>

			<!-- Tags / categorias -->
			<?php if ( $cliconnect_cats ) : ?>
			<div class="post-materia__tags">
				<span class="post-materia__tags-label">
					<?php esc_html_e( 'Assuntos', 'cli' ); ?>
				</span>
				<div class="post-materia__tags-lista">
					<?php foreach ( $cliconnect_cats as $cliconnect_cat ) : ?>
						<div class="blog-tag">
							<span><?php echo esc_html( mb_strtoupper( $cliconnect_cat->name ) ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>

		</article>

	</div>
</section>
