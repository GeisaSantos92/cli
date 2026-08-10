<?php
/**
 * Helpers de conteúdo usados pelos templates da home.
 *
 * Concentram a leitura do ACF (sempre com coalescência nula), a renderização de
 * botões vindos do campo Link e as consultas aos CPTs de catálogo — para os
 * template-parts ficarem só com marcação.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lê um campo ACF da página inicial.
 *
 * @param string $nome   Nome do campo.
 * @param mixed  $padrao Valor devolvido quando o campo está vazio.
 * @return mixed
 */
function cliconnect_campo( $nome, $padrao = '' ) {
	static $home_id = null;

	if ( null === $home_id ) {
		$home_id = (int) get_option( 'page_on_front' );
	}

	if ( ! $home_id || ! function_exists( 'get_field' ) ) {
		return $padrao;
	}

	$valor = get_field( $nome, $home_id ) ?? '';

	if ( '' === $valor || null === $valor || array() === $valor ) {
		return $padrao;
	}

	return $valor;
}

/**
 * Renderiza um botão a partir de um campo ACF do tipo Link.
 *
 * @param array|string $link    Retorno do campo Link (array) ou nome do campo.
 * @param string       $classes Classes CSS do botão.
 * @param string       $icone   Chave de cliconnect_icone() exibida após o texto.
 * @return void
 */
function cliconnect_botao( $link, $classes = 'botao botao--primario', $icone = 'seta-direita' ) {
	if ( is_string( $link ) ) {
		$link = cliconnect_campo( $link, array() );
	}

	$url   = $link['url'] ?? '';
	$texto = $link['title'] ?? '';

	if ( ! $url || ! $texto ) {
		return;
	}

	$target = $link['target'] ?? '';
	$rel    = ( '_blank' === $target ) ? ' rel="noopener noreferrer"' : '';

	printf(
		'<a class="%1$s" href="%2$s"%3$s%4$s>%5$s%6$s</a>',
		esc_attr( $classes ),
		esc_url( $url ),
		$target ? ' target="' . esc_attr( $target ) . '"' : '',
		$rel, // Literal seguro montado acima.
		esc_html( $texto ),
		cliconnect_icone( $icone ) // SVG estático de lista fechada.
	);
}

/**
 * Monta um <img> para um arquivo estático de assets/img/.
 *
 * Ilustrações fechadas (o planeta do rodapé, a pilha de camadas, o cartão da
 * Boomi) são assets do tema, não conteúdo editável: entram por aqui em vez de
 * virar campo do ACF. Devolve string vazia quando o arquivo não existe.
 *
 * @param string $arquivo   Nome do arquivo dentro de assets/img/.
 * @param array  $atributos Atributos extras da tag (alt, width, height, class…).
 * @return string HTML da imagem, ou string vazia.
 */
function cliconnect_imagem_tema( $arquivo, $atributos = array() ) {
	$relativo = '/assets/img/' . ltrim( (string) $arquivo, '/' );

	if ( ! file_exists( get_theme_file_path( $relativo ) ) ) {
		return '';
	}

	$atributos = wp_parse_args(
		$atributos,
		array(
			'alt'      => '',
			'loading'  => 'lazy',
			'decoding' => 'async',
		)
	);

	$html = '<img src="' . esc_url( get_theme_file_uri( $relativo ) ) . '"';

	foreach ( $atributos as $nome => $valor ) {
		if ( null === $valor || '' === $valor ) {
			// alt="" é obrigatório em imagem decorativa: não pode ser descartado.
			if ( 'alt' !== $nome ) {
				continue;
			}

			$valor = '';
		}

		$html .= ' ' . esc_attr( $nome ) . '="' . esc_attr( $valor ) . '"';
	}

	return $html . '>';
}

/**
 * Logo de uma Integração (CPT cli_integracao) pelo nome.
 *
 * Usado pelo mega menu de Soluções: quando o item do menu tem o mesmo nome de
 * uma integração cadastrada, o logo dela vira o ícone do link. O índice
 * nome => attachment é montado uma vez por requisição.
 *
 * @param string $nome Nome exibido no item de menu.
 * @return string HTML da imagem, ou string vazia quando não há correspondência.
 */
function cliconnect_logo_integracao( $nome ) {
	static $indice = null;

	if ( null === $indice ) {
		$indice = array();

		foreach ( cliconnect_posts( 'cli_integracao' ) as $integracao ) {
			$indice[ sanitize_title( $integracao->post_title ) ] = (int) $integracao->ID;
		}
	}

	$chave = sanitize_title( (string) $nome );

	if ( ! isset( $indice[ $chave ] ) ) {
		// "TOTVS Protheus" também aproveita o logo cadastrado como "TOTVS".
		foreach ( $indice as $slug => $id ) {
			if ( 0 === strpos( $chave, $slug . '-' ) ) {
				$chave = $slug;
				break;
			}
		}
	}

	if ( ! isset( $indice[ $chave ] ) ) {
		return '';
	}

	// 'medium' e não 'thumbnail': o tamanho thumbnail é cortado em quadrado e
	// decepa as laterais de logos horizontais.
	return cliconnect_thumb( $indice[ $chave ], 'medium', array( 'alt' => '' ) );
}

/**
 * Busca posts de um CPT de catálogo, já ordenados por menu_order.
 *
 * @param string $post_type Slug do CPT.
 * @param int    $limite    Quantidade máxima (-1 para todos).
 * @param array  $extra     Argumentos adicionais de WP_Query.
 * @return WP_Post[]
 */
function cliconnect_posts( $post_type, $limite = -1, $extra = array() ) {
	$args = array_merge(
		array(
			'post_type'              => $post_type,
			'post_status'            => 'publish',
			'posts_per_page'         => $limite,
			'orderby'                => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
		),
		$extra
	);

	return get_posts( $args );
}

/**
 * Imagem destacada de um post como <img>, com fallback silencioso.
 *
 * @param int    $post_id  ID do post.
 * @param string $tamanho  Tamanho registrado.
 * @param array  $atributos Atributos extras da tag.
 * @return string HTML da imagem, ou string vazia.
 */
function cliconnect_thumb( $post_id, $tamanho = 'medium', $atributos = array() ) {
	$thumb_id = get_post_thumbnail_id( $post_id );

	if ( ! $thumb_id ) {
		return '';
	}

	$atributos = wp_parse_args(
		$atributos,
		array(
			'alt'     => get_the_title( $post_id ),
			'loading' => 'lazy',
		)
	);

	return wp_get_attachment_image( $thumb_id, $tamanho, false, $atributos );
}

/**
 * Imagem de um campo ACF (que retorna ID) como <img>.
 *
 * @param string $campo    Nome do campo na home.
 * @param string $tamanho  Tamanho registrado.
 * @param array  $atributos Atributos extras.
 * @return string HTML da imagem, ou string vazia.
 */
function cliconnect_campo_imagem( $campo, $tamanho = 'large', $atributos = array() ) {
	$id = absint( cliconnect_campo( $campo, 0 ) );

	if ( ! $id ) {
		return '';
	}

	$atributos = wp_parse_args( $atributos, array( 'loading' => 'lazy' ) );

	return wp_get_attachment_image( $id, $tamanho, false, $atributos );
}

/**
 * Monta uma lista de itens numerados vindos de campos ACF planos.
 *
 * O ACF Free não tem Repeater: conjuntos de tamanho fixo usam campos
 * `prefixo_1`, `prefixo_2`, ... Esta função os agrupa e descarta os vazios.
 *
 * @param string   $molde    Molde do nome do campo, com %d (ex.: 'metrica_%d_numero').
 * @param int      $total    Quantidade de posições a checar.
 * @param callable $callback Recebe o índice e devolve o item montado.
 * @return array Itens não vazios.
 */
function cliconnect_lista_numerada( $molde, $total, $callback = null ) {
	$itens = array();

	for ( $i = 1; $i <= $total; $i++ ) {
		$valor = cliconnect_campo( sprintf( $molde, $i ), '' );

		if ( '' === $valor ) {
			continue;
		}

		$itens[] = is_callable( $callback ) ? $callback( $i, $valor ) : $valor;
	}

	return $itens;
}

/**
 * Verifica se a página atual é identificada pelo slug informado.
 *
 * @param string $slug Post name da página.
 * @return bool
 */
function cliconnect_e_pagina( $slug ) {
	return is_page( $slug );
}

/**
 * Lê um campo ACF da página atualmente renderizada.
 *
 * Equivalente de cliconnect_campo() para páginas internas: lê do objeto
 * consultado (get_queried_object_id) em vez da página inicial.
 *
 * @param string $nome   Nome do campo.
 * @param mixed  $padrao Valor devolvido quando o campo está vazio.
 * @return mixed
 */
function cliconnect_campo_pagina( $nome, $padrao = '' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $padrao;
	}

	$page_id = get_queried_object_id();

	if ( ! $page_id ) {
		return $padrao;
	}

	$valor = get_field( $nome, $page_id ) ?? '';

	if ( '' === $valor || null === $valor || array() === $valor ) {
		return $padrao;
	}

	return $valor;
}

/**
 * Renderiza um botão a partir de um campo ACF do tipo Link da página atual.
 *
 * @param string $campo   Nome do campo Link.
 * @param string $classes Classes CSS do botão.
 * @param string $icone   Ícone após o texto.
 * @return void
 */
function cliconnect_botao_pagina( $campo, $classes = 'botao botao--primario', $icone = 'seta-direita' ) {
	cliconnect_botao( cliconnect_campo_pagina( $campo, array() ), $classes, $icone );
}

/**
 * Lista numerada de campos ACF da página atual.
 *
 * @param string   $molde    Molde com %d.
 * @param int      $total    Quantidade de posições.
 * @param callable $callback Recebe o índice e o valor.
 * @return array
 */
function cliconnect_lista_numerada_pagina( $molde, $total, $callback = null ) {
	$itens = array();

	for ( $i = 1; $i <= $total; $i++ ) {
		$valor = cliconnect_campo_pagina( sprintf( $molde, $i ), '' );

		if ( '' === $valor ) {
			continue;
		}

		$itens[] = is_callable( $callback ) ? $callback( $i, $valor ) : $valor;
	}

	return $itens;
}

/**
 * Aplica ênfase (*trecho*) num texto simples, escapando o resto.
 *
 * Permite o cliente destacar parte de uma frase sem abrir um editor completo.
 *
 * @param string $texto Texto com *marcações*.
 * @param string $tag   Tag HTML aplicada ao trecho destacado.
 * @return string HTML seguro.
 */
function cliconnect_enfase( $texto, $tag = 'em' ) {
	$escapado = esc_html( (string) $texto );

	return preg_replace(
		'/\*([^*]+)\*/',
		'<' . $tag . '>$1</' . $tag . '>',
		$escapado
	);
}
