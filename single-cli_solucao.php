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
	'diagrama',
	'logos',
	'casos',
	'selos',
	'diferencial',
	'plataforma',
	'aceleradores',
	'faq',
);

// Parte das soluções inverte Diferencial e Selos no design (campo da aba Diferencial).
if ( cliconnect_campo_pagina( 'solucao_dif_antes_selos' ) ) {
	$cliconnect_pos_selos = array_search( 'selos', $cliconnect_secoes, true );
	$cliconnect_pos_dif   = array_search( 'diferencial', $cliconnect_secoes, true );

	if ( false !== $cliconnect_pos_selos && false !== $cliconnect_pos_dif ) {
		$cliconnect_secoes[ $cliconnect_pos_selos ] = 'diferencial';
		$cliconnect_secoes[ $cliconnect_pos_dif ]   = 'selos';
	}
}

foreach ( $cliconnect_secoes as $cliconnect_secao ) {
	get_template_part( 'template-parts/solucao/' . $cliconnect_secao );
}

get_footer();
