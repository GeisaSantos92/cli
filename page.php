<?php
/**
 * Template padrão de páginas.
 *
 * Páginas 100% mapeadas com ACF ganham template dedicado
 * (page-{slug}.php) montado com template-parts. Este arquivo cobre as
 * páginas de conteúdo livre (editor).
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
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class( 'entry' ); ?>>
			<header class="entry__header">
				<h1 class="entry__title"><?php the_title(); ?></h1>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
			<figure class="entry__thumbnail">
				<?php the_post_thumbnail( 'large' ); ?>
			</figure>
			<?php endif; ?>

			<div class="entry__content">
				<?php the_content(); ?>
			</div>
		</article>
		<?php
	endwhile;
	?>
</main>

<?php get_footer(); ?>
