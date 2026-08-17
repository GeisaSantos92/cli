<?php
/**
 * Template Name: CLI Signature
 *
 * Página de produto CLI Signature. Orquestra os template-parts de cada seção.
 * Zero texto fixo: todo conteúdo vem do grupo ACF `group_cli_cli_signature`.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$cliconnect_secoes = array(
	'hero',
	'cenarios',
	'pilares',
	'diferenciais',	
	'gestor',
	'arquiteto',
	'operacao',
	'selos',
);

foreach ( $cliconnect_secoes as $cliconnect_secao ) {
	get_template_part( 'template-parts/cli-signature/' . $cliconnect_secao );
}

get_footer();
