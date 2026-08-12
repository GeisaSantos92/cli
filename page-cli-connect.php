<?php
/**
 * Template Name: CLI Connect
 *
 * Página de produto CLI Connect. Orquestra os template-parts de cada seção.
 * Zero texto fixo: todo conteúdo vem do grupo ACF `group_cli_cli_connect`.
 *
 * @package Cliconnect
 */

get_header();

$cliconnect_secoes = array(
	'hero',
	'brands',
	'solucao',
	'boomi',
	'metricas',
	'destaque',
	'integracoes',
	'depoimento',
	'pilares',
	'agentes',
	'departamentos',
	'reforma',
);

foreach ( $cliconnect_secoes as $cliconnect_secao ) {
	get_template_part( 'template-parts/cli-connect/' . $cliconnect_secao );
}

get_footer();
