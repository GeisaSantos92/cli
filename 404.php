<?php
/**
 * Template de página não encontrada (404).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main">

	<section class="error-404">
		<h1 class="error-404__title"><?php esc_html_e( 'Página não encontrada', 'cli' ); ?></h1>
		<p class="error-404__text">
			<?php esc_html_e( 'O conteúdo que você procura não existe ou foi movido. Tente uma busca ou volte para a página inicial.', 'cli' ); ?>
		</p>

		<?php get_search_form(); ?>

		<p>
			<a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php esc_html_e( 'Voltar para a home', 'cli' ); ?>
			</a>
		</p>
	</section>

</main>

<?php get_footer(); ?>
