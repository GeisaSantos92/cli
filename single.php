<?php
/**
 * Template de post individual (Blog).
 *
 * Orquestra as seções via template-parts/single/.
 * CPTs com visual próprio usam single-{cpt}.php.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-single">
	<?php
	while ( have_posts() ) :
		the_post();

		get_template_part( 'template-parts/single/breadcrumb' );
		get_template_part( 'template-parts/single/materia' );

	endwhile;
	?>
</main>

<?php get_footer(); ?>
