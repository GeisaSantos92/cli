<?php
/**
 * Template Name: Trabalhe Conosco
 *
 * Página Trabalhe Conosco.
 *
 * Só orquestra: cada seção vira um template-part em
 * template-parts/trabalhe-conosco/. Todo o conteúdo vem do ACF da página
 * (inc/acf-fields-trabalhe-conosco.php) — nada é fixo aqui.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-trabalhe-conosco">
	<?php
	$cliconnect_secoes = array(
		'hero',
		'sobre',
		'metricas',
		'frase',
		'valores',
		'depoimento',
		'beneficios',
		'jeito-cli',
		'blog',
	);

	foreach ( $cliconnect_secoes as $cliconnect_secao ) {
		get_template_part( 'template-parts/trabalhe-conosco/' . $cliconnect_secao );
	}
	?>
</main>

<?php
get_footer();
