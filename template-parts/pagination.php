<?php
/**
 * Template Part: paginação numerada.
 *
 * Wrapper fino sobre cliconnect_pagination_render() (inc/pagination.php).
 *
 * @package Cliconnect
 *
 * @var array $args {
 *     @type int   $current_page Página atual (1-indexed).
 *     @type int   $total_pages  Total de páginas.
 *     @type array $add_args     Query args a preservar nos links (ex.: s, filtros).
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

cliconnect_pagination_render(
	$args['current_page'] ?? 1,
	$args['total_pages'] ?? 1,
	$args['add_args'] ?? array()
);
