<?php
/**
 * Meta tags de SEO, Open Graph, Twitter Card e Schema.org (JSON-LD).
 *
 * Sem plugin: tudo gerado aqui com dados já disponíveis no tema
 * (ACF, Customizer, wp_get_document_title).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gera a meta description da página atual.
 *
 * Prioridade:
 *  - Home:     campo ACF `hero_subtitulo`
 *  - Singular: excerpt → 30 primeiras palavras do conteúdo
 *  - Fallback: tagline do site (Configurações → Tagline)
 *
 * @return string Texto sem HTML, máximo 160 caracteres.
 */
function cliconnect_meta_description() {
	$desc = '';

	if ( is_front_page() && function_exists( 'cliconnect_campo' ) ) {
		$desc = (string) ( cliconnect_campo( 'hero_subtitulo' ) ?? '' );
	} elseif ( is_singular() ) {
		global $post;
		$desc = $post->post_excerpt
			? $post->post_excerpt
			: wp_trim_words( wp_strip_all_tags( $post->post_content ?? '' ), 30, '' );
	}

	if ( ! $desc ) {
		$desc = get_bloginfo( 'description' );
	}

	return mb_substr( wp_strip_all_tags( $desc ), 0, 160 );
}

/**
 * Retorna a URL absoluta da imagem Open Graph.
 *
 * Prioridade:
 *  - Imagem destacada do post/página (singular)
 *  - Logo claro salvo no Customizer
 *
 * @return string URL da imagem, ou string vazia.
 */
function cliconnect_og_image_url() {
	if ( is_singular() && has_post_thumbnail() ) {
		$src = wp_get_attachment_image_url( (int) get_post_thumbnail_id(), 'large' );
		if ( $src ) {
			return $src;
		}
	}

	$logo_id = absint( get_theme_mod( 'cliconnect_logo_claro' ) ?? 0 );
	if ( $logo_id ) {
		$src = wp_get_attachment_image_url( $logo_id, 'large' );
		if ( $src ) {
			return $src;
		}
	}

	return '';
}

/**
 * Imprime meta description, Open Graph e Twitter Card no <head>.
 *
 * Hookado em wp_head com prioridade 5 (antes dos scripts do tema).
 *
 * @return void
 */
function cliconnect_print_seo_meta() {
	$desc     = cliconnect_meta_description();
	$og_image = cliconnect_og_image_url();
	$og_url   = is_singular() ? (string) get_permalink() : home_url( '/' );
	$og_type  = is_singular( 'post' ) ? 'article' : 'website';
	$titulo   = wp_get_document_title();

	if ( $desc ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	}

	// Open Graph.
	printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( $og_type ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $titulo ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $og_url ) );
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );

	if ( $desc ) {
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
	}

	if ( $og_image ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $og_image ) );
	}

	// Twitter / X Card.
	$twitter_card = $og_image ? 'summary_large_image' : 'summary';
	printf( '<meta name="twitter:card" content="%s">' . "\n", esc_attr( $twitter_card ) );
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $titulo ) );

	if ( $desc ) {
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $desc ) );
	}

	if ( $og_image ) {
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $og_image ) );
	}
}
add_action( 'wp_head', 'cliconnect_print_seo_meta', 5 );

/**
 * Imprime JSON-LD de Organization na home.
 *
 * Inclui redes sociais registradas no Customizer (sameAs),
 * o logo e a URL canônica — base para rich snippets de marca.
 *
 * @return void
 */
function cliconnect_print_schema_org() {
	if ( ! is_front_page() ) {
		return;
	}

	$logo_id  = absint( get_theme_mod( 'cliconnect_logo_claro' ) ?? 0 );
	$logo_url = $logo_id ? (string) wp_get_attachment_image_url( $logo_id, 'large' ) : '';

	$schema = array(
		'@context' => 'https://schema.org',
		'@type'    => 'Organization',
		'name'     => get_bloginfo( 'name' ),
		'url'      => home_url( '/' ),
	);

	if ( $logo_url ) {
		$schema['logo'] = $logo_url;
	}

	// Redes sociais do Customizer → sameAs.
	if ( function_exists( 'cliconnect_social_networks' ) ) {
		$same_as = array();
		foreach ( array_keys( cliconnect_social_networks() ) as $slug ) {
			$url = (string) ( get_theme_mod( 'cliconnect_social_' . $slug ) ?? '' );
			if ( $url ) {
				$same_as[] = $url;
			}
		}
		if ( $same_as ) {
			$schema['sameAs'] = $same_as;
		}
	}

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
	);
}
add_action( 'wp_head', 'cliconnect_print_schema_org', 6 );
