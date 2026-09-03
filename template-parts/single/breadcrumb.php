<?php
/**
 * Post interna — Breadcrumb: Início › Blog › Título do post.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cliconnect_titulo = get_the_title();
$cliconnect_blog   = get_post_type_archive_link( 'post' );

if ( ! $cliconnect_blog ) {
	$cliconnect_blog_page = get_option( 'page_for_posts' );
	$cliconnect_blog      = $cliconnect_blog_page ? get_permalink( $cliconnect_blog_page ) : home_url( '/' );
}
?>

<nav class="post-breadcrumb" aria-label="<?php esc_attr_e( 'Localização', 'cli' ); ?>">
	<div class="post-breadcrumb__inner">

		<a
			class="post-breadcrumb__home"
			href="<?php echo esc_url( home_url( '/' ) ); ?>"
			aria-label="<?php esc_attr_e( 'Início', 'cli' ); ?>"
		>
			<?php echo cliconnect_icone( 'casa', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_icone retorna SVG estático do tema. ?>
		</a>

		<?php echo cliconnect_icone( 'seta-direita', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_icone retorna SVG estático do tema. ?>

		<a
			class="post-breadcrumb__link"
			href="<?php echo esc_url( $cliconnect_blog ); ?>"
		>
			<?php esc_html_e( 'Blog', 'cli' ); ?>
		</a>

		<?php echo cliconnect_icone( 'seta-direita', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_icone retorna SVG estático do tema. ?>

		<span class="post-breadcrumb__atual">
			<?php echo esc_html( $cliconnect_titulo ); ?>
		</span>

	</div>
</nav>
