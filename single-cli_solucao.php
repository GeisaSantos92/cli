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
	'integracoes',
	'logos',
	'casos',
	'selos',
	'diferencial',
	'plataforma',
	'aceleradores',
	'faq',
);

/*
 * Parte das soluções coloca o Diferencial antes dos Selos (campo da aba
 * Diferencial). Nesses designs a faixa de selos fecha a página, logo antes do
 * FAQ — depois também de Plataforma e Aceleradores, quando existirem.
 */
if ( cliconnect_campo_pagina( 'solucao_dif_antes_selos' ) ) {
	$cliconnect_pos_selos = array_search( 'selos', $cliconnect_secoes, true );
	$cliconnect_pos_faq   = array_search( 'faq', $cliconnect_secoes, true );

	if ( false !== $cliconnect_pos_selos && false !== $cliconnect_pos_faq ) {
		unset( $cliconnect_secoes[ $cliconnect_pos_selos ] );
		$cliconnect_secoes = array_values( $cliconnect_secoes );

		$cliconnect_pos_faq = array_search( 'faq', $cliconnect_secoes, true );
		array_splice( $cliconnect_secoes, $cliconnect_pos_faq, 0, array( 'selos' ) );
	}
}

foreach ( $cliconnect_secoes as $cliconnect_secao ) {
	get_template_part( 'template-parts/solucao/' . $cliconnect_secao );
}

get_footer();
