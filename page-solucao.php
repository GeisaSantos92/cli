<?php
/**
 * Template Name: Solução
 *
 * Landing page compartilhada por todas as soluções (Salesforce, SAP, etc.).
 * Seções opcionais: cada template-part retorna cedo quando seus campos estão
 * vazios, tornando a seção invisível para páginas que não a usam.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$cliconnect_secoes = array(
	'hero',
);

foreach ( $cliconnect_secoes as $cliconnect_secao ) {
	get_template_part( 'template-parts/solucao/' . $cliconnect_secao );
}

get_footer();
