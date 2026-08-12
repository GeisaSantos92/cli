<?php
/**
 * Enfileiramento de assets (CSS/JS).
 *
 * Regras (ver docs/code-standards.md):
 * - Nada de <link>/<script> no HTML: tudo entra por wp_enqueue_*.
 * - Cache-busting por filemtime() (versão automática por arquivo).
 * - Caminhos via get_theme_file_uri()/get_theme_file_path().
 * - Handles prefixados com `cliconnect-`.
 * - CSS/JS por contexto: a home carrega front-page.css só em is_front_page().
 *
 * Fontes são auto-hospedadas (assets/fonts/*.woff2): zero requisição a CDN.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retorna a versão de cache-busting de um asset (filemtime), com fallback.
 *
 * @param string $relative Caminho relativo à raiz do tema.
 * @return string|false Timestamp de modificação, ou a versão do tema como fallback.
 */
function cliconnect_asset_version( $relative ) {
	$path = get_theme_file_path( $relative );

	if ( file_exists( $path ) ) {
		return (string) filemtime( $path );
	}

	return wp_get_theme()->get( 'Version' );
}

/**
 * Enfileira os estilos e scripts do tema.
 *
 * @return void
 */
function cliconnect_enqueue_assets() {
	// @font-face das quatro famílias do design system.
	wp_enqueue_style(
		'cliconnect-fonts',
		get_theme_file_uri( '/assets/css/fonts.css' ),
		array(),
		cliconnect_asset_version( '/assets/css/fonts.css' )
	);

	// Tokens, reset, chrome e componentes globais.
	wp_enqueue_style(
		'cliconnect-theme',
		get_theme_file_uri( '/assets/css/theme.css' ),
		array( 'cliconnect-fonts' ),
		cliconnect_asset_version( '/assets/css/theme.css' )
	);

	// Seções da página inicial.
	if ( is_front_page() ) {
		wp_enqueue_style(
			'cliconnect-front-page',
			get_theme_file_uri( '/assets/css/front-page.css' ),
			array( 'cliconnect-theme' ),
			cliconnect_asset_version( '/assets/css/front-page.css' )
		);
	}

	// Arquivo de cases.
	if ( is_post_type_archive( 'cli_case' ) ) {
		wp_enqueue_style(
			'cliconnect-cases',
			get_theme_file_uri( '/assets/css/cases.css' ),
			array( 'cliconnect-theme' ),
			cliconnect_asset_version( '/assets/css/cases.css' )
		);
	}

	// Página individual de case.
	if ( is_singular( 'cli_case' ) ) {
		wp_enqueue_style(
			'cliconnect-case-single',
			get_theme_file_uri( '/assets/css/case-single.css' ),
			array( 'cliconnect-theme' ),
			cliconnect_asset_version( '/assets/css/case-single.css' )
		);
	}

	// Listagem do blog (home.php — índice de posts nativos).
	if ( is_home() ) {
		wp_enqueue_style(
			'cliconnect-blog',
			get_theme_file_uri( '/assets/css/blog.css' ),
			array( 'cliconnect-theme' ),
			cliconnect_asset_version( '/assets/css/blog.css' )
		);
	}

	// Página interna de post do blog.
	if ( is_singular( 'post' ) ) {
		wp_enqueue_style(
			'cliconnect-single',
			get_theme_file_uri( '/assets/css/single.css' ),
			array( 'cliconnect-theme' ),
			cliconnect_asset_version( '/assets/css/single.css' )
		);
	}

	// Página Trabalhe Conosco.
	if ( cliconnect_e_pagina( 'trabalhe-conosco' ) ) {
		wp_enqueue_style(
			'cliconnect-trabalhe-conosco',
			get_theme_file_uri( '/assets/css/page-trabalhe-conosco.css' ),
			array( 'cliconnect-theme' ),
			cliconnect_asset_version( '/assets/css/page-trabalhe-conosco.css' )
		);
	}

	// Página CLI Connect.
	if ( cliconnect_e_pagina( 'cli-connect' ) ) {
		wp_enqueue_style(
			'cliconnect-cli-connect',
			get_theme_file_uri( '/assets/css/page-cli-connect.css' ),
			array( 'cliconnect-theme' ),
			cliconnect_asset_version( '/assets/css/page-cli-connect.css' )
		);
	}

	// Comportamentos vanilla (menu, submenus, acordeão do FAQ).
	wp_enqueue_script(
		'cliconnect-theme',
		get_theme_file_uri( '/assets/js/theme.js' ),
		array(),
		cliconnect_asset_version( '/assets/js/theme.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'cliconnect_enqueue_assets' );

/**
 * Pré-carrega as fontes do texto corrido (evita FOUT no conteúdo acima da dobra).
 *
 * @return void
 */
function cliconnect_preload_fonts() {
	$fontes = array(
		'/assets/fonts/inter-400_700-latin.woff2',
		'/assets/fonts/rajdhani-600-latin.woff2',
	);

	foreach ( $fontes as $fonte ) {
		if ( ! file_exists( get_theme_file_path( $fonte ) ) ) {
			continue;
		}

		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( get_theme_file_uri( $fonte ) )
		);
	}
}
add_action( 'wp_head', 'cliconnect_preload_fonts', 1 );
