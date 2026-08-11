<?php
/**
 * Blog — índice de postagens nativas do WordPress.
 *
 * Exibido quando Settings > Reading "Posts page" aponta para esta URL.
 * Orquestra as três seções via template-parts/blog/.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-blog">
	<?php
	$cliconnect_secoes = array( 'destaque', 'newsletter', 'cards' );

	foreach ( $cliconnect_secoes as $cliconnect_secao ) {
		get_template_part( 'template-parts/blog/' . $cliconnect_secao );
	}
	?>
</main>

<?php
get_footer();
