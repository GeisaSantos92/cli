<?php
/**
 * Meta tags de SEO, Open Graph, Twitter Card e Schema.org (JSON-LD).
 *
 * Política (issue #148): com o Rank Math ativo, **ele** é a fonte de verdade do
 * <head> — o tema não compete. Sem o plugin, tudo aqui volta a valer, gerado
 * com dados que o tema já tem (ACF, Customizer, wp_get_document_title).
 *
 * Sem essa guarda o front sai com duas `description`, dois blocos Open Graph /
 * Twitter e duas Organization em JSON-LD.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Há um plugin de SEO cuidando do <head>?
 *
 * Hoje o site usa Rank Math. A checagem é por constante/classe do plugin, não
 * por caminho de arquivo, para continuar valendo se ele for movido ou
 * renomeado.
 *
 * @return bool
 */
function cliconnect_plugin_seo_ativo() {
	return defined( 'RANK_MATH_VERSION' ) || class_exists( 'RankMath' );
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
 * Hookado em wp_head com prioridade 5 (antes dos scripts do tema). Só imprime
 * quando não há plugin de SEO ativo — é o fallback do tema.
 *
 * @return void
 */
function cliconnect_print_seo_meta() {
	if ( cliconnect_plugin_seo_ativo() ) {
		return;
	}

	$desc     = cliconnect_meta_description();
	$og_image = cliconnect_og_image_url();
	$og_url   = is_singular() ? (string) get_permalink() : home_url( '/' );
	$og_type  = is_singular( array( 'post', 'cli_case' ) ) ? 'article' : 'website';
	$titulo   = wp_get_document_title();

	// Locale do idioma atual (Polylang ou fallback pt_BR).
	$locale = function_exists( 'pll_current_language' )
		? (string) pll_current_language( 'locale' )
		: get_locale();

	if ( $desc ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	}

	// Open Graph.
	printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( $og_type ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $titulo ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $og_url ) );
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:locale" content="%s">' . "\n", esc_attr( $locale ) );

	if ( $desc ) {
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
	}

	if ( $og_image ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $og_image ) );
		// Dimensões mínimas recomendadas para WhatsApp/Facebook/LinkedIn.
		printf( '<meta property="og:image:width" content="1200">' . "\n" );
		printf( '<meta property="og:image:height" content="630">' . "\n" );
	}

	// og:locale:alternate — versões traduzidas disponíveis (Polylang).
	if ( function_exists( 'pll_the_languages' ) ) {
		$linguas = pll_the_languages( array( 'raw' => 1 ) );
		foreach ( $linguas as $lingua ) {
			if ( ! empty( $lingua['current_lang'] ) ) {
				continue;
			}
			if ( ! empty( $lingua['locale'] ) ) {
				printf( '<meta property="og:locale:alternate" content="%s">' . "\n", esc_attr( $lingua['locale'] ) );
			}
		}
	}

	// Canonical — aponta para a URL canônica da página atual.
	printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $og_url ) );

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
 * Inclui redes sociais registradas no Customizer (sameAs), o logo e a URL
 * canônica — base para rich snippets de marca. Como a meta acima, só sai
 * quando não há plugin de SEO cuidando do grafo.
 *
 * @return void
 */
function cliconnect_print_schema_org() {
	if ( cliconnect_plugin_seo_ativo() || ! is_front_page() ) {
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

/**
 * Imprime JSON-LD para singles dos CPTs públicos (cli_case e cli_solucao).
 *
 * Só age quando não há plugin de SEO ativo. Usa dados disponíveis no post
 * sem depender de campos ACF específicos para não quebrar se os campos mudarem.
 *
 * - cli_case    → Article (case study com imagem e descrição)
 * - cli_solucao → Service (solução/produto com provider Organization)
 *
 * @return void
 */
function cliconnect_print_schema_cpt() {
	if ( cliconnect_plugin_seo_ativo() ) {
		return;
	}

	if ( ! is_singular( array( 'cli_case', 'cli_solucao' ) ) ) {
		return;
	}

	$post_type = get_post_type();
	$titulo    = wp_strip_all_tags( get_the_title() );
	$url       = (string) get_permalink();
	$descricao = '';

	global $post;
	if ( $post->post_excerpt ) {
		$descricao = wp_strip_all_tags( $post->post_excerpt );
	} elseif ( $post->post_content ) {
		$descricao = wp_trim_words( wp_strip_all_tags( $post->post_content ), 30, '' );
	}

	$imagem_url = '';
	if ( has_post_thumbnail() ) {
		$src = wp_get_attachment_image_url( (int) get_post_thumbnail_id(), 'cli-case-hero' );
		if ( $src ) {
			$imagem_url = $src;
		}
	}

	if ( 'cli_case' === $post_type ) {
		$schema = array(
			'@context'  => 'https://schema.org',
			'@type'     => 'Article',
			'headline'  => $titulo,
			'url'       => $url,
			'publisher' => array(
				'@type' => 'Organization',
				'name'  => get_bloginfo( 'name' ),
				'url'   => home_url( '/' ),
			),
		);

		if ( $descricao ) {
			$schema['description'] = $descricao;
		}
		if ( $imagem_url ) {
			$schema['image'] = $imagem_url;
		}
	} else {
		// cli_solucao → Service.
		$schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Service',
			'name'        => $titulo,
			'url'         => $url,
			'provider'    => array(
				'@type' => 'Organization',
				'name'  => get_bloginfo( 'name' ),
				'url'   => home_url( '/' ),
			),
		);

		if ( $descricao ) {
			$schema['description'] = $descricao;
		}
		if ( $imagem_url ) {
			$schema['image'] = $imagem_url;
		}
	}

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
	);
}
add_action( 'wp_head', 'cliconnect_print_schema_cpt', 6 );

/**
 * Fallback de alt text para campos ACF de imagem sem alt preenchido.
 *
 * Quando o cliente faz upload sem preencher o campo "Texto alternativo" na
 * biblioteca de mídia, wp_get_attachment_image() retorna alt="". Este filtro
 * preenche o alt com o título do anexo (derivado do nome do arquivo) como
 * último recurso — apenas quando nenhum alt foi armazenado no banco.
 *
 * Não interfere com imagens explicitamente decorativas (alt="" passado pelo
 * tema nos atributos), pois nesses casos o alt já está no array $attr.
 *
 * @param array      $attr       Atributos da tag <img>.
 * @param WP_Post    $attachment Post do anexo.
 * @return array Atributos com alt preenchido se estava vazio.
 */
function cliconnect_alt_fallback( $attr, $attachment ) {
	// alt="" passado explicitamente pelo tema = decorativa. Respeita.
	if ( array_key_exists( 'alt', $attr ) ) {
		return $attr;
	}

	// alt do banco também vazio → usa o título do anexo como fallback.
	if ( empty( $attr['alt'] ) ) {
		$titulo = trim( get_the_title( $attachment->ID ) );
		if ( $titulo ) {
			$attr['alt'] = $titulo;
		}
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'cliconnect_alt_fallback', 20, 2 );

/**
 * Imprime JSON-LD BreadcrumbList para CPTs públicos e posts do blog.
 *
 * Só age quando não há plugin de SEO ativo. Gera a trilha correspondente
 * ao breadcrumb visual exibido pelo tema:
 * - Blog (singular):   Início › Blog › Título
 * - Case (singular):   Início › Cases › Título
 * - Solução (single):  Início › Soluções › [Categoria] › Título
 *
 * @return void
 */
function cliconnect_print_schema_breadcrumb() {
	if ( cliconnect_plugin_seo_ativo() ) {
		return;
	}

	if ( ! is_singular( array( 'post', 'cli_case', 'cli_solucao' ) ) ) {
		return;
	}

	$home_url   = home_url( '/' );
	$home_label = get_bloginfo( 'name' );
	$itens      = array(
		array(
			'@type'    => 'ListItem',
			'position' => 1,
			'name'     => $home_label,
			'item'     => $home_url,
		),
	);

	$post_type = get_post_type();

	if ( 'post' === $post_type ) {
		$blog_page_id  = (int) get_option( 'page_for_posts' );
		$blog_url      = $blog_page_id ? get_permalink( $blog_page_id ) : home_url( '/blog/' );
		$itens[]       = array(
			'@type'    => 'ListItem',
			'position' => 2,
			'name'     => __( 'Blog', 'cli' ),
			'item'     => $blog_url,
		);
		$itens[]       = array(
			'@type'    => 'ListItem',
			'position' => 3,
			'name'     => wp_strip_all_tags( get_the_title() ),
			'item'     => (string) get_permalink(),
		);
	} elseif ( 'cli_case' === $post_type ) {
		$arquivo_url = get_post_type_archive_link( 'cli_case' );
		if ( $arquivo_url ) {
			$itens[] = array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => __( 'Cases', 'cli' ),
				'item'     => $arquivo_url,
			);
		}
		$itens[] = array(
			'@type'    => 'ListItem',
			'position' => $arquivo_url ? 3 : 2,
			'name'     => wp_strip_all_tags( get_the_title() ),
			'item'     => (string) get_permalink(),
		);
	} elseif ( 'cli_solucao' === $post_type ) {
		$arquivo_url = get_post_type_archive_link( 'cli_solucao' );
		$posicao     = 2;

		if ( $arquivo_url ) {
			$itens[] = array(
				'@type'    => 'ListItem',
				'position' => $posicao++,
				'name'     => __( 'Soluções', 'cli' ),
				'item'     => $arquivo_url,
			);
		}

		$termos = get_the_terms( get_the_ID(), 'cli_categoria_solucao' );
		if ( $termos && ! is_wp_error( $termos ) ) {
			$cat = null;
			foreach ( $termos as $termo ) {
				if ( 0 === (int) $termo->parent ) {
					$cat = $termo;
					break;
				}
			}
			if ( ! $cat ) {
				$cat = $termos[0];
			}
			$cat_url = get_term_link( $cat );
			if ( ! is_wp_error( $cat_url ) ) {
				$itens[] = array(
					'@type'    => 'ListItem',
					'position' => $posicao++,
					'name'     => $cat->name,
					'item'     => $cat_url,
				);
			}
		}

		$itens[] = array(
			'@type'    => 'ListItem',
			'position' => $posicao,
			'name'     => wp_strip_all_tags( get_the_title() ),
			'item'     => (string) get_permalink(),
		);
	}

	$schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $itens,
	);

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
	);
}
add_action( 'wp_head', 'cliconnect_print_schema_breadcrumb', 6 );

/* ==========================================================================
   Sitemap nativo do WordPress — ajustes de qualidade de indexação
   Só age quando o Rank Math não está ativo (ele substitui o sitemap inteiro).
   ========================================================================== */

/**
 * Remove post types de conteúdo raso do sitemap nativo.
 *
 * - attachment: página de anexo não tem conteúdo próprio; indexá-la dilui a
 *   autoridade do domínio e raramente gera tráfego qualificado.
 *
 * @param WP_Sitemap_Provider[] $providers Mapa de provedores registrados.
 * @return WP_Sitemap_Provider[]
 */
function cliconnect_sitemap_remover_attachment( $providers ) {
	if ( cliconnect_plugin_seo_ativo() ) {
		return $providers;
	}

	if ( isset( $providers['posts'] ) && method_exists( $providers['posts'], 'get_object_subtypes' ) ) {
		// Filtramos no nível do post type, não no provider inteiro.
		add_filter(
			'wp_sitemaps_post_types',
			function ( $post_types ) {
				unset( $post_types['attachment'] );
				return $post_types;
			}
		);
	}

	return $providers;
}
add_filter( 'wp_sitemaps_add_provider', 'cliconnect_sitemap_remover_attachment', 10, 1 );

/**
 * Remove taxonomias sem conteúdo semântico do sitemap nativo.
 *
 * - post_format: raramente usado; quando vazio, a URL retorna 404 ou lista vazia.
 *
 * @param WP_Taxonomy[] $taxonomies Mapa de taxonomias candidatas ao sitemap.
 * @return WP_Taxonomy[]
 */
function cliconnect_sitemap_filtrar_taxonomias( $taxonomies ) {
	if ( cliconnect_plugin_seo_ativo() ) {
		return $taxonomies;
	}

	unset( $taxonomies['post_format'] );

	return $taxonomies;
}
add_filter( 'wp_sitemaps_taxonomies', 'cliconnect_sitemap_filtrar_taxonomias' );
