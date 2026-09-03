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

/**
 * Imprime o iframe <noscript> do GTM logo após a abertura do <body>.
 *
 * Necessário para rastrear usuários com JavaScript desabilitado.
 * Extrai o ID GTM-XXXXXX do snippet salvo no Customizer — sem duplicar
 * a configuração. Respeita as mesmas condições de cliconnect_print_analytics().
 *
 * @return void
 */
function cliconnect_print_gtm_noscript() {
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		return;
	}

	if ( is_user_logged_in() ) {
		return;
	}

	$snippet = (string) ( get_theme_mod( 'cliconnect_ga_tag' ) ?? '' );

	// Extrai o GTM ID do snippet (ex.: GTM-P2W894RT).
	if ( ! preg_match( '/GTM-[A-Z0-9]+/', $snippet, $matches ) ) {
		return;
	}

	$gtm_id = $matches[0];

	printf(
		'<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=%s" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>' . "\n",
		esc_attr( $gtm_id )
	);
}
add_action( 'wp_body_open', 'cliconnect_print_gtm_noscript', 1 );
