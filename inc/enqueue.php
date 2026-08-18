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

	// Página CLI Signature.
	if ( cliconnect_e_pagina( 'cli-signature' ) ) {
		wp_enqueue_style(
			'cliconnect-cli-signature',
			get_theme_file_uri( '/assets/css/page-cli-signature.css' ),
			array( 'cliconnect-theme' ),
			cliconnect_asset_version( '/assets/css/page-cli-signature.css' )
		);
	}

	// Página Integração SAP.
	if ( cliconnect_e_pagina( 'integracao-sap' ) ) {
		wp_enqueue_style(
			'cliconnect-integracao-sap',
			get_theme_file_uri( '/assets/css/page-integracao-sap.css' ),
			array( 'cliconnect-theme' ),
			cliconnect_asset_version( '/assets/css/page-integracao-sap.css' )
		);
	}

	// Página Contato.
	if ( cliconnect_e_pagina( 'contato' ) ) {
		wp_enqueue_style(
			'cliconnect-contato',
			get_theme_file_uri( '/assets/css/page-contato.css' ),
			array( 'cliconnect-theme' ),
			cliconnect_asset_version( '/assets/css/page-contato.css' )
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

	// Landing page de Solução (page-solucao.php — compartilhada por todas as soluções).
	if ( is_page_template( 'page-solucao.php' ) ) {
		wp_enqueue_style(
			'cliconnect-solucao-pagina',
			get_theme_file_uri( '/assets/css/page-solucao.css' ),
			array( 'cliconnect-theme' ),
			cliconnect_asset_version( '/assets/css/page-solucao.css' )
		);
	}

	// Listagem de Soluções (archive e taxonomy).
	if ( is_post_type_archive( 'cli_solucao' ) || is_tax( 'cli_categoria_solucao' ) ) {
		wp_enqueue_style(
			'cliconnect-solucoes',
			get_theme_file_uri( '/assets/css/archive-solucao.css' ),
			array( 'cliconnect-theme' ),
			cliconnect_asset_version( '/assets/css/archive-solucao.css' )
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
 * Adiciona o atributo `defer` ao script principal do tema.
 *
 * O script já está no footer (5º parâmetro = true), mas `defer` garante que
 * o parser HTML não seja bloqueado durante a execução — melhora FCP/LCP.
 * O JS usa ready() que detecta readyState corretamente com ou sem defer.
 *
 * @param string $tag    HTML da tag <script>.
 * @param string $handle Handle do script.
 * @return string HTML modificado.
 */
function cliconnect_defer_script( $tag, $handle ) {
	if ( 'cliconnect-theme' !== $handle ) {
		return $tag;
	}

	if ( str_contains( $tag, ' defer' ) ) {
		return $tag;
	}

	return str_replace( ' src=', ' defer src=', $tag );
}
add_filter( 'script_loader_tag', 'cliconnect_defer_script', 10, 2 );

/**
 * Pré-carrega as fontes do texto corrido (evita FOUT no conteúdo acima da dobra).
 *
 * @return void
 */
function cliconnect_preload_fonts() {
	$fontes = array(
		'/assets/fonts/inter-400_700-latin.woff2',    // corpo do texto
		'/assets/fonts/rajdhani-600-latin.woff2',     // títulos (H1, H2) — LCP font
		'/assets/fonts/mona-sans-600-latin.woff2',    // eyebrow above-the-fold
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
