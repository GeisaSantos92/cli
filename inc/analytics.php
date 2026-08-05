<?php
/**
 * Injeção do snippet de analytics (GA/GTM) no wp_head.
 *
 * Lê o snippet salvo no Customizer (cliconnect_ga_tag) e o imprime apenas:
 * - fora de WP_DEBUG (ou seja, nunca em ambiente de desenvolvimento);
 * - para visitantes não logados (não polui métricas com a equipe).
 *
 * Sem plugin dedicado de analytics — ver docs/best-practices.md.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Imprime o snippet de rastreamento no <head> quando aplicável.
 *
 * @return void
 */
function cliconnect_print_analytics() {
	// Ambiente de desenvolvimento: nunca imprime.
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		return;
	}

	// Usuários logados (equipe/editores) não entram nas métricas.
	if ( is_user_logged_in() ) {
		return;
	}

	$snippet = get_theme_mod( 'cliconnect_ga_tag' ) ?? '';

	if ( '' === trim( (string) $snippet ) ) {
		return;
	}

	// Snippet de terceiros colado por admin (sanitizado no save do Customizer);
	// precisa sair como veio para o <script> do GA/GTM funcionar.
	echo "\n" . $snippet . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
}
add_action( 'wp_head', 'cliconnect_print_analytics', 20 );
