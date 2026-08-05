<?php
/**
 * Fallback mínimo do tema (template de último recurso).
 *
 * Renderiza qualquer rota que não tenha template específico na hierarquia.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content', get_post_type() );
		endwhile;

		get_template_part(
			'template-parts/pagination',
			null,
			array(
				'current_page' => max( 1, (int) get_query_var( 'paged' ) ),
				'total_pages'  => (int) $GLOBALS['wp_query']->max_num_pages,
			)
		);
	else :
		get_template_part( 'template-parts/content', 'none' );
	endif;
	?>
</main>

<?php get_footer(); ?>
