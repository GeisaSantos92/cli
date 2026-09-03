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
 * Envia HTTP Security Headers em todas as respostas front-end.
 *
 * - X-Content-Type-Options: impede MIME sniffing.
 * - X-Frame-Options: bloqueia carregamento do site em iframe (clickjacking).
 * - Referrer-Policy: limita o referrer enviado a sites externos.
 * - Permissions-Policy: desativa features de hardware desnecessárias.
 *
 * @return void
 */
function cliconnect_security_headers() {
	if ( is_admin() ) {
		return;
	}

	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );
}
add_action( 'send_headers', 'cliconnect_security_headers' );

/**
 * Desativa o XML-RPC — não há uso legítimo neste site.
 *
 * Previne brute force amplificado via system.multicall e uso do servidor
 * como vetor de DDoS por amplificação de pingback.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Bloqueia a enumeração de usuários via REST API para não autenticados.
 *
 * Por padrão /wp-json/wp/v2/users/ expõe login e nome de todos os usuários
 * sem autenticação, reduzindo o esforço de ataques de brute force pela metade.
 *
 * @param array $endpoints Endpoints REST registrados.
 * @return array Endpoints sem a rota de usuários para anônimos.
 */
function cliconnect_rest_ocultar_usuarios( $endpoints ) {
	if ( is_user_logged_in() ) {
		return $endpoints;
	}

	unset( $endpoints['/wp/v2/users'] );
	unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );

	return $endpoints;
}
add_filter( 'rest_endpoints', 'cliconnect_rest_ocultar_usuarios' );

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
