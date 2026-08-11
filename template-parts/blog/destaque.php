<?php
/**
 * Blog — Seção destaque: breadcrumb + post principal em evidência.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$posts_destaque = cliconnect_posts(
	'post',
	1,
	array(
		'orderby' => 'date',
		'order'   => 'DESC',
	)
);

if ( ! $posts_destaque ) {
	return;
}

$post_destaque = $posts_destaque[0];
$titulo        = get_the_title( $post_destaque );
$permalink     = get_permalink( $post_destaque );
$excerpt       = get_the_excerpt( $post_destaque );
$data          = get_the_date( 'j F Y', $post_destaque );
$categorias    = get_the_category( $post_destaque->ID );
$categoria     = ! empty( $categorias ) ? $categorias[0]->name : '';
?>

<section class="blog-destaque">
	<div class="container">

		<!-- Breadcrumb -->
		<nav class="blog-destaque__breadcrumb" aria-label="<?php esc_attr_e( 'Localização', 'cli' ); ?>">
			<a
				class="blog-destaque__breadcrumb-home"
				href="<?php echo esc_url( home_url( '/' ) ); ?>"
				aria-label="<?php esc_attr_e( 'Início', 'cli' ); ?>"
			>
				<?php echo cliconnect_icone( 'casa', 20 ); // SVG estático — lista fechada. ?>
			</a>
			<?php echo cliconnect_icone( 'seta-direita', 16 ); ?>
			<span class="blog-destaque__breadcrumb-atual">
				<?php esc_html_e( 'Blog', 'cli' ); ?>
			</span>
		</nav>

		<!-- Post em destaque -->
		<a
			class="blog-destaque__conteudo"
			href="<?php echo esc_url( $permalink ); ?>"
			aria-label="<?php echo esc_attr( $titulo ); ?>"
		>
			<div class="blog-destaque__imagem">
				<?php echo cliconnect_thumb( $post_destaque->ID, 'large', array( 'alt' => '' ) ); ?>
			</div>

			<div class="blog-destaque__info">
				<?php if ( $categoria ) : ?>
					<div class="blog-tag">
						<span><?php echo esc_html( mb_strtoupper( $categoria ) ); ?></span>
					</div>
				<?php endif; ?>

				<h1 class="blog-destaque__titulo">
					<?php echo esc_html( $titulo ); ?>
				</h1>

				<p class="blog-destaque__data">
					<?php echo esc_html( $data ); ?>
				</p>

				<?php if ( $excerpt ) : ?>
					<p class="blog-destaque__excerpt">
						<?php echo esc_html( $excerpt ); ?>
					</p>
				<?php endif; ?>
			</div>
		</a>

	</div>
</section>
