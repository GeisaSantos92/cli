<?php
/**
 * Integração do tema com o Polylang (site bilíngue pt/en).
 *
 * Três responsabilidades, todas blindadas com `function_exists()` para o tema
 * continuar funcionando com o plugin desativado (regra do projeto):
 *
 * 1. Template das traduções — `page-{slug}.php` casa pelo `post_name`, e a
 *    tradução tem outro slug (`/en/platform/`). Sem o filtro abaixo toda página
 *    em inglês cai no `page.php` genérico e perde o layout.
 * 2. Strings do Customizer — texto digitado pelo cliente é `theme_mod`, não é
 *    gettext nem post. Só traduz se for registrado com `pll_register_string()`
 *    e lido com `pll__()`.
 * 3. Leitura traduzida — `cliconnect_traduzir()` é o ponto único que os
 *    templates usam para ler esses textos.
 *
 * Conteúdo (páginas, CPTs, campos ACF) não passa por aqui: tradução é outro
 * post, criado e vinculado pelo seed (`inc/cli/seed-en.php`).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Textos do Customizer que vão para "Idiomas → Traduções de strings".
 *
 * Chave = nome do theme mod; valor = rótulo mostrado no painel do Polylang.
 * URLs, telefone e e-mail ficam de fora: não são texto traduzível.
 *
 * @return array<string,string>
 */
function cliconnect_strings_customizer() {
	return array(
		'cliconnect_portal_texto'     => __( 'Cabeçalho — texto do Portal do Cliente', 'cli' ),
		'cliconnect_header_cta_texto' => __( 'Cabeçalho — texto do CTA', 'cli' ),
		'cliconnect_cta_titulo'       => __( 'Rodapé — título do CTA', 'cli' ),
		'cliconnect_cta_botao_texto'  => __( 'Rodapé — texto do botão do CTA', 'cli' ),
	);
}

/**
 * Registra as strings do Customizer para tradução no Polylang.
 *
 * @return void
 */
function cliconnect_registrar_strings_polylang() {
	if ( ! function_exists( 'pll_register_string' ) ) {
		return;
	}

	foreach ( cliconnect_strings_customizer() as $mod => $rotulo ) {
		$valor = get_theme_mod( $mod ) ?? '';

		if ( '' === $valor ) {
			continue;
		}

		// Multiline: o título do CTA tem quebra de linha vinda do Figma.
		pll_register_string( $rotulo, $valor, 'CLI Connect', str_contains( (string) $valor, "\n" ) );
	}
}
add_action( 'init', 'cliconnect_registrar_strings_polylang' );

/**
 * Devolve a tradução de um texto registrado no Polylang.
 *
 * Sem o plugin (ou sem tradução cadastrada) devolve o texto original — nunca
 * string vazia.
 *
 * @param string $texto Texto no idioma padrão.
 * @return string
 */
function cliconnect_traduzir( $texto ) {
	$texto = (string) ( $texto ?? '' );

	if ( '' === $texto || ! function_exists( 'pll__' ) ) {
		return $texto;
	}

	$traduzido = pll__( $texto );

	return ( '' === $traduzido || null === $traduzido ) ? $texto : $traduzido;
}

/**
 * Lê um theme mod já traduzido pelo Polylang.
 *
 * @param string $mod    Nome do theme mod.
 * @param string $padrao Valor devolvido quando o mod está vazio.
 * @return string
 */
function cliconnect_mod_traduzido( $mod, $padrao = '' ) {
	$valor = get_theme_mod( $mod ) ?? '';

	if ( '' === $valor ) {
		return $padrao;
	}

	return cliconnect_traduzir( $valor );
}

/**
 * Faz a tradução de uma página usar o mesmo template da versão original.
 *
 * `page-{slug}.php` casa pelo `post_name`; a tradução tem slug próprio e não
 * casaria com nada. Aqui partimos da página no idioma padrão e reaproveitamos
 * o template dela.
 *
 * Só age quando a hierarquia caiu no `page.php` genérico — assim um Page
 * Template escolhido no painel (page-templates/) continua mandando.
 *
 * @param string $template Caminho do template resolvido pela hierarquia.
 * @return string
 */
function cliconnect_template_da_traducao( $template ) {
	if ( ! is_page() || 'page.php' !== basename( $template ) ) {
		return $template;
	}

	if ( ! function_exists( 'pll_get_post' ) || ! function_exists( 'pll_default_language' ) ) {
		return $template;
	}

	$padrao = pll_default_language();
	$atual  = function_exists( 'pll_current_language' ) ? pll_current_language() : '';

	if ( ! $padrao || $padrao === $atual ) {
		return $template;
	}

	$origem = (int) pll_get_post( get_queried_object_id(), $padrao );

	if ( ! $origem ) {
		return $template;
	}

	$slug = get_post_field( 'post_name', $origem );

	if ( ! $slug ) {
		return $template;
	}

	$alvo = locate_template( 'page-' . $slug . '.php' );

	return $alvo ? $alvo : $template;
}
add_filter( 'template_include', 'cliconnect_template_da_traducao', 20 );

/**
 * Faz o grupo de campos da home valer também para a home traduzida.
 *
 * A regra `page_type == front_page` do ACF compara com `page_on_front`, que
 * guarda só o ID em português. Sem isso, a home em inglês abre no painel sem
 * nenhum campo — e o cliente não consegue editá-la.
 *
 * @param bool  $result Resultado calculado pelo ACF.
 * @param array $rule   Regra avaliada (param, operator, value).
 * @param array $screen Contexto da tela (post_id, post_type, …).
 * @return bool
 */
function cliconnect_acf_front_page_traduzida( $result, $rule, $screen ) {
	if ( $result || 'front_page' !== ( $rule['value'] ?? '' ) || '==' !== ( $rule['operator'] ?? '' ) ) {
		return $result;
	}

	if ( ! function_exists( 'pll_get_post_translations' ) ) {
		return $result;
	}

	$post_id = (int) ( $screen['post_id'] ?? 0 );
	$home_id = (int) get_option( 'page_on_front' );

	if ( ! $post_id || ! $home_id ) {
		return $result;
	}

	$traducoes = array_map( 'intval', pll_get_post_translations( $home_id ) );

	return in_array( $post_id, $traducoes, true );
}
add_filter( 'acf/location/rule_match/page_type', 'cliconnect_acf_front_page_traduzida', 20, 3 );

/**
 * Deixa o título do archive de Soluções acompanhar o idioma.
 *
 * Os labels dos CPTs (`inc/cpt.php`) são texto fixo em português de propósito:
 * o painel é usado em português. O nome do archive, porém, vaza para o front —
 * é ele que forma a tag <title> de /en/solucoes/. Aqui devolvemos uma string
 * gettext, traduzida pelo .mo do tema.
 *
 * @param string $nome      Nome do post type.
 * @param string $post_type Slug do post type.
 * @return string
 */
function cliconnect_titulo_archive_traduzido( $nome, $post_type ) {
	if ( 'cli_solucao' === $post_type ) {
		return __( 'Soluções', 'cli' );
	}

	return $nome;
}
add_filter( 'post_type_archive_title', 'cliconnect_titulo_archive_traduzido', 10, 2 );
