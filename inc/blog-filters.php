<?php
/**
 * Blog — ajusta a query nativa do índice de posts (home.php).
 *
 * A "posts page" usa a query principal do WordPress (is_home()), então
 * paginação/offset ficam a cargo do pre_get_posts — nunca query custom no
 * template, senão a paginação quebra (ver comentário em archive.php).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define quantos posts a query principal do blog carrega por página:
 * 1 post em destaque + 6 no grid = 7.
 *
 * @param WP_Query $query Query em avaliação.
 * @return void
 */
function cliconnect_blog_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_home() ) {
		return;
	}

	$query->set( 'posts_per_page', 7 );
}
add_action( 'pre_get_posts', 'cliconnect_blog_query' );
