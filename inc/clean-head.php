<?php
/**
 * Deep clean do wp_head — deixa o <head> enxuto.
 *
 * Remove tags e scripts que o WordPress injeta por padrão e que não usamos:
 * emojis, wlwmanifest, RSD, generator, shortlink e feeds de comentários.
 *
 * EXCEÇÃO IMPORTANTE (blocos Gutenberg):
 * Se o corpo das páginas/posts usar blocos, os estilos de bloco
 * (wp-block-library) NÃO devem ser removidos globalmente. Se um dia optar por
 * dequeue, ele deve ser CONDICIONAL, preservando os estilos em is_singular()
 * e demais contextos que renderizam blocos. Ver docs/architecture.md.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Remove metatags e links desnecessários do wp_head.
 *
 * @return void
 */
function cliconnect_clean_head() {
	// Windows Live Writer e EditURI/RSD (APIs legadas de blog).
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );

	// Versão do WordPress (generator) — reduz superfície de fingerprinting.
	remove_action( 'wp_head', 'wp_generator' );

	// Shortlink (?p=123) — não usamos.
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );

	// Links de posts adjacentes (prev/next) no <head>.
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );

	// Feed de comentários (mantém os feeds principais do automatic-feed-links).
	remove_action( 'wp_head', 'feed_links_extra', 3 );
}
add_action( 'init', 'cliconnect_clean_head' );

/**
 * Desativa completamente os scripts/estilos de emoji.
 *
 * Emojis nativos adicionam JS/CSS no <head> que não usamos (as fontes do
 * sistema já cobrem emojis). Mantém o HTML final mais leve.
 *
 * @return void
 */
function cliconnect_disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

	// Impede o WordPress de trocar emojis por imagens no TinyMCE.
	add_filter( 'tiny_mce_plugins', 'cliconnect_disable_emojis_tinymce' );

	// Remove o DNS-prefetch do CDN de emojis (s.w.org).
	add_filter( 'wp_resource_hints', 'cliconnect_remove_emoji_dns_prefetch', 10, 2 );
}
add_action( 'init', 'cliconnect_disable_emojis' );

/**
 * Remove o plugin de emoji do TinyMCE.
 *
 * @param array $plugins Plugins carregados pelo TinyMCE.
 * @return array Plugins sem o de emoji.
 */
function cliconnect_disable_emojis_tinymce( $plugins ) {
	if ( ! is_array( $plugins ) ) {
		return array();
	}

	return array_diff( $plugins, array( 'wpemoji' ) );
}

/**
 * Remove o DNS-prefetch do CDN de emojis dos resource hints.
 *
 * @param array  $urls          URLs de resource hints.
 * @param string $relation_type Tipo de relação (dns-prefetch, preconnect, etc.).
 * @return array URLs filtradas.
 */
function cliconnect_remove_emoji_dns_prefetch( $urls, $relation_type ) {
	if ( 'dns-prefetch' !== $relation_type ) {
		return $urls;
	}

	$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/' );

	return array_filter(
		$urls,
		static function ( $url ) use ( $emoji_svg_url ) {
			$href = is_array( $url ) && isset( $url['href'] ) ? $url['href'] : $url;
			return is_string( $href ) ? false === strpos( $href, $emoji_svg_url ) : true;
		}
	);
}
