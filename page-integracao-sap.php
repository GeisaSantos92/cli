<?php
/**
 * Template Name: Integração SAP
 *
 * Página de solução Integração SAP. Orquestra os template-parts de cada seção.
 * Zero texto fixo: todo conteúdo vem do grupo ACF `group_cli_integracao_sap`.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$cliconnect_secoes = array(
	'hero',
	'velocidade',
	'conectar',
	'sincronizar',
	'recursos',
	'depoimento',
	'sistemas',
	'cleancore',
	'integracoes',
	'migracao',
	'beneficios',
	'automacao',
	'faq',
);

foreach ( $cliconnect_secoes as $cliconnect_secao ) {
	get_template_part( 'template-parts/integracao-sap/' . $cliconnect_secao );
}

get_footer();
