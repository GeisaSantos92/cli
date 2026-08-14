<?php
/**
 * CLI Connect — bootstrap do tema (maestro).
 *
 * Este arquivo NÃO contém lógica de negócio: apenas o setup mínimo do tema
 * (after_setup_theme) e o carregamento seguro dos módulos de `inc/`.
 * Cada responsabilidade vive no seu módulo. Ver docs/architecture.md.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Carrega um módulo de `inc/` com segurança (checa existência antes do require).
 *
 * @param string $relative Caminho relativo à raiz do tema (ex.: '/inc/enqueue.php').
 * @return void
 */
function cliconnect_require( $relative ) {
	$path = get_theme_file_path( $relative );

	if ( file_exists( $path ) ) {
		require $path;
	}
}

/**
 * Setup do tema: suportes, menus e text-domain.
 *
 * @return void
 */
function cliconnect_setup() {
	// Deixa o WordPress gerenciar a tag <title> do documento.
	add_theme_support( 'title-tag' );

	// Imagens destacadas (thumbnails) nos posts/páginas e CPTs.
	add_theme_support( 'post-thumbnails' );

	// Marcação HTML5 semântica para os recursos gerados pelo Core.
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Embeds (YouTube, etc.) responsivos no conteúdo.
	add_theme_support( 'responsive-embeds' );

	// Feeds automáticos (RSS/Atom) no <head>.
	add_theme_support( 'automatic-feed-links' );

	// Menus de navegação: cabeçalho e rodapé (colunas + linha legal).
	register_nav_menus(
		array(
			'principal'    => __( 'Cabeçalho — Menu Principal', 'cli' ),
			'rodape'       => __( 'Rodapé — Colunas de Links', 'cli' ),
			'rodape_legal' => __( 'Rodapé — Links Legais (linha inferior)', 'cli' ),
		)
	);

	// Traduções do tema (arquivos .mo em /languages).
	load_theme_textdomain( 'cli', get_theme_file_path( '/languages' ) );
}
add_action( 'after_setup_theme', 'cliconnect_setup' );

// Infraestrutura do tema.
cliconnect_require( '/inc/enqueue.php' );
cliconnect_require( '/inc/clean-head.php' );
cliconnect_require( '/inc/customizer.php' );
cliconnect_require( '/inc/analytics.php' );
cliconnect_require( '/inc/seo.php' );
cliconnect_require( '/inc/icons.php' );
cliconnect_require( '/inc/template-tags.php' );
cliconnect_require( '/inc/menu-walker.php' );
cliconnect_require( '/inc/helpers.php' );
cliconnect_require( '/inc/pagination.php' );
cliconnect_require( '/inc/login.php' );
cliconnect_require( '/inc/smtp.php' );

// Conteúdo do projeto: CPTs e campos ACF (locais, via código).
cliconnect_require( '/inc/cpt.php' );
cliconnect_require( '/inc/acf-fields-cpt.php' );
cliconnect_require( '/inc/acf-fields-home.php' );
cliconnect_require( '/inc/acf-fields-trabalhe-conosco.php' );
cliconnect_require( '/inc/acf-fields-cli-connect.php' );

// Comandos WP-CLI: carregados apenas no contexto de linha de comando,
// inertes em produção/requisições web.
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	cliconnect_require( '/inc/cli/bootstrap.php' );
	cliconnect_require( '/inc/cli/seed.php' );
}
