<?php
/**
 * Cases privados (temporário): só os cases da Petroreconcavo ficam públicos.
 * Os demais cases individuais só aparecem para quem está logado; para
 * visitantes, a página de um case restrito responde 404 e as listagens
 * (arquivo e seleções da home) mostram apenas os cases liberados.
 *
 * A checagem roda em parse_request, antes da consulta: assim o case nem chega
 * a ser carregado, e o título e o schema dos plugins de SEO não expõem o nome.
 *
 * Para publicar todos os cases, remova o require de /inc/cases-privados.php em
 * functions.php.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Prefixo do slug dos cases liberados para visitantes (inclui traduções).
 *
 * @return string
 */
function cliconnect_cases_prefixo_publico() {
	return 'petroreconcavo';
}

/**
 * IDs dos cases liberados para visitantes.
 *
 * @return int[]
 */
function cliconnect_cases_ids_publicos() {
	global $wpdb;

	$ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'cli_case' AND post_name LIKE %s",
			$wpdb->esc_like( cliconnect_cases_prefixo_publico() ) . '%'
		)
	);

	return array_map( 'intval', $ids ) ? array_map( 'intval', $ids ) : array( 0 );
}

/**
 * Limita as consultas de listagem de cases aos liberados para visitantes.
 *
 * @param WP_Query $query Consulta em preparação.
 * @return void
 */
function cliconnect_cases_filtrar_listagem( $query ) {
	if ( is_user_logged_in() || is_admin() ) {
		return;
	}

	$tipo = $query->get( 'post_type' );
	if ( 'cli_case' !== $tipo && array( 'cli_case' ) !== $tipo ) {
		return;
	}

	if ( $query->get( 'name' ) || $query->get( 'p' ) ) {
		return;
	}

	$query->set( 'post__in', cliconnect_cases_ids_publicos() );
}
add_action( 'pre_get_posts', 'cliconnect_cases_filtrar_listagem' );

/**
 * Transforma a requisição de um case restrito em 404 para visitantes.
 *
 * @param WP $wp Objeto de requisição do WordPress.
 * @return void
 */
function cliconnect_cases_exigir_login( $wp ) {
	if ( is_user_logged_in() ) {
		return;
	}

	$vars = $wp->query_vars;

	if ( empty( $vars['cli_case'] ) ) {
		return;
	}

	if ( 0 === strpos( (string) $vars['cli_case'], cliconnect_cases_prefixo_publico() ) ) {
		return;
	}

	unset( $wp->query_vars['post_type'], $wp->query_vars['cli_case'], $wp->query_vars['name'] );
	$wp->query_vars['error'] = '404';
}
add_action( 'parse_request', 'cliconnect_cases_exigir_login' );
