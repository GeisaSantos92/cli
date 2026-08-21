<?php
/**
 * Single: cli_solucao — Landing page de solução.
 *
 * Cada post do CPT é ao mesmo tempo o item do catálogo (listagens) e a
 * landing page da solução. Seções opcionais: cada template-part retorna cedo
 * quando seus campos ACF estão vazios, tornando a seção invisível.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$cliconnect_secoes = array(
	'hero',
	'metricas',
	'pilares',
	'logos',
	'casos',
	'selos',
	'diferencial',
	'plataforma',
	'aceleradores',
	'faq',
);

foreach ( $cliconnect_secoes as $cliconnect_secao ) {
	get_template_part( 'template-parts/solucao/' . $cliconnect_secao );
}

get_footer();
