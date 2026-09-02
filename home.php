<?php
/**
 * Blog — índice de postagens nativas do WordPress.
 *
 * Exibido quando Settings > Reading "Posts page" aponta para esta URL.
 * Usa a query principal (ajustada por cliconnect_blog_query() em
 * inc/blog-filters.php) para paginar nativamente: na 1ª página, o post mais
 * recente vira destaque e os demais formam o grid; da 2ª página em diante,
 * só grid.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$cliconnect_post_destaque = null;
$cliconnect_posts_grade   = array();

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();

		if ( ! is_paged() && ! $cliconnect_post_destaque ) {
			$cliconnect_post_destaque = get_post();
			continue;
		}

		$cliconnect_posts_grade[] = get_post();
	}
}
?>

<main id="primary" class="site-blog">
	<?php
	if ( $cliconnect_post_destaque ) {
		get_template_part( 'template-parts/blog/destaque', null, array( 'post' => $cliconnect_post_destaque ) );
	}

	get_template_part( 'template-parts/blog/newsletter' );

	get_template_part( 'template-parts/blog/cards', null, array( 'posts' => $cliconnect_posts_grade ) );

	get_template_part(
		'template-parts/pagination',
		null,
		array(
			'current_page' => max( 1, (int) get_query_var( 'paged' ) ),
			'total_pages'  => (int) $GLOBALS['wp_query']->max_num_pages,
		)
	);
	?>
</main>

<?php
get_footer();
