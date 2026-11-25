<?php
/**
 * Template Name: CLI Flow
 *
 * Página de produto CLI Flow. Orquestra os template-parts de cada seção.
 * Zero texto fixo: todo conteúdo vem do grupo ACF `group_cli_cli_flow`.
 *
 * @package Cliconnect
 */

get_header();

$cliconnect_secoes = array(
	'hero',
	'mockup',
	'logos',
	'dores',
	'casos',
	'ia-cta',
	'cards',
	'sap',
	'integracoes',
	'sistemas',
	'beneficios',
	'dashboard',
	'economia',
	'preco',
	'selos',
	'faq',
	'cta-final',
);

foreach ( $cliconnect_secoes as $cliconnect_secao ) {
	get_template_part( 'template-parts/cli-flow/' . $cliconnect_secao );
}

get_footer();
