<?php
/**
 * Template padrão de archives (categorias, tags, datas e CPTs sem
 * archive-{cpt}.php próprio).
 *
 * Listagens usam archives nativos + pre_get_posts para ajustes de query —
 * nunca query custom em page template (evita 404 de paginação).
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
		<h1 class="archive-header__title"><?php the_archive_title(); ?></h1>
		<?php the_archive_description( '<div class="archive-header__description">', '</div>' ); ?>
	</header>

	<?php if ( have_posts() ) : ?>

	<div class="archive-grid">
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content', get_post_type() );
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
		)
	);
	?>

	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>

</main>

<?php get_footer(); ?>
