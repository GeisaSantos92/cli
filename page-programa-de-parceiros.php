<?php
/**
 * Template Name: Programa de Parceiros
 *
 * Página Programa de Parceiros.
 *
 * Só orquestra: cada seção do layout vira um template-part em
 * template-parts/programa-de-parceiros/. Todo o conteúdo vem do ACF da própria
 * página (inc/acf-fields-programa-de-parceiros.php) ou dos CPTs `cli_cliente`
 * e `cli_faq` — nada é fixo aqui.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-programa-de-parceiros">
	<?php
	$cliconnect_secoes = array(
		'hero',
		'parceria',
		'logos',
		'como-funciona',
		'perfil',
		'estrutura',
		'atuacao',
		'faq',
		'cta',
	);

	foreach ( $cliconnect_secoes as $cliconnect_secao ) {
		get_template_part( 'template-parts/programa-de-parceiros/' . $cliconnect_secao );
	}
	?>
</main>

<?php
get_footer();
