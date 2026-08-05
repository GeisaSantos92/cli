<?php
/**
 * Template Name: Landing Page (canvas)
 * Template Post Type: page
 *
 * Canvas para landing pages montadas 100% com blocos (inc/blocks.php):
 * renderiza o conteúdo cru entre header e footer, sem container nem
 * tipografia de entry — cada bloco de seção controla a própria largura e
 * fundo, como os template-parts fazem nas páginas institucionais.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<main id="primary" class="site-main site-main--canvas">
		<?php the_content(); ?>
	</main>
	<?php
endwhile;

get_footer();
