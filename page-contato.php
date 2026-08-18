<?php
/**
 * Template Name: Contato
 *
 * Página de contato. Orquestra os template-parts de cada seção.
 * Zero texto fixo: todo conteúdo vem do grupo ACF `group_cli_contato`.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$cliconnect_secoes = array(
	'formulario',
	'clientes',
);

foreach ( $cliconnect_secoes as $cliconnect_secao ) {
	get_template_part( 'template-parts/contato/' . $cliconnect_secao );
}

get_footer();
