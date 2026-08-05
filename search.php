<?php
/**
 * Template de resultados de busca.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main">

	<header class="archive-header">
		<h1 class="archive-header__title">
			<?php
			/* translators: %s: termo buscado */
			printf( esc_html__( 'Resultados para: %s', 'cli' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
			?>
		</h1>
	</header>

	<?php get_search_form(); ?>

	<?php if ( have_posts() ) : ?>

	<div class="archive-grid">
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content', 'search' );
		endwhile;
		?>
	</div>

	<?php
	get_template_part(
		'template-parts/pagination',
		null,
		array(
			'current_page' => max( 1, (int) get_query_var( 'paged' ) ),
			'total_pages'  => (int) $GLOBALS['wp_query']->max_num_pages,
			'add_args'     => array( 's' => get_search_query() ),
		)
	);
	?>

	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>

</main>

<?php get_footer(); ?>
