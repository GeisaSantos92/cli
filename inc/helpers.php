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
	if ( ! function_exists( 'get_field' ) ) {
		return $padrao;
	}

	/*
	 * get_option('page_on_front') pode vir do cache do WP com o ID da versão PT
	 * mesmo quando o Polylang já definiu o idioma como EN — o filtro chega tarde.
	 * Solução: parte do ID base (PT) e pede ao Polylang a tradução do idioma atual.
	 */
	$home_id = (int) get_option( 'page_on_front' );

	if ( function_exists( 'pll_current_language' ) && function_exists( 'pll_get_post' ) ) {
		$lang       = pll_current_language();
		$translated = $lang ? pll_get_post( $home_id, $lang ) : 0;
		if ( $translated ) {
			$home_id = $translated;
		}
	}

	if ( ! $home_id ) {
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

		$html .= ' ' . esc_attr( $nome ) . '="' . esc_attr( (string) $valor ) . '"';
	}

	$img = $html . '>';

	// Serve versão WebP quando existe um .webp correspondente ao lado do PNG/JPG.
	$webp_relativo = (string) preg_replace( '/\.(png|jpe?g)$/i', '.webp', $relativo );
	if (
		$webp_relativo !== $relativo
		&& file_exists( get_theme_file_path( $webp_relativo ) )
	) {
		return '<picture>'
			. '<source type="image/webp" srcset="' . esc_url( get_theme_file_uri( $webp_relativo ) ) . '">'
			. $img
			. '</picture>';
	}

	return $img;
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

		// Aliases: título do item no menu → slug da integração cadastrada.
		// (ChatGPT aparece no menu, mas o logo está na integração "OpenAI".)
		$aliases = array( 'chatgpt' => 'openai' );
		foreach ( $aliases as $alias => $slug ) {
			if ( ! isset( $indice[ $alias ] ) && isset( $indice[ $slug ] ) ) {
				$indice[ $alias ] = $indice[ $slug ];
			}
		}

		// Soluções (cli_solucao) também podem ter featured image usada como logo.
		foreach ( cliconnect_posts( 'cli_solucao' ) as $solucao ) {
			$chave = sanitize_title( $solucao->post_title );
			if ( ! isset( $indice[ $chave ] ) ) {
				$indice[ $chave ] = (int) $solucao->ID;
			}
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
 * Versão do cache de CPTs — incrementada no save/delete de qualquer post.
 *
 * @return int
 */
function cliconnect_posts_cache_version() {
	return (int) get_option( 'cliconnect_posts_cache_v', 1 );
}

/**
 * Invalida o cache de CPTs ao salvar ou excluir qualquer post.
 *
 * @param int $post_id ID do post afetado.
 * @return void
 */
function cliconnect_posts_cache_bust( $post_id ) {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}
	update_option( 'cliconnect_posts_cache_v', cliconnect_posts_cache_version() + 1, false );
}
add_action( 'save_post', 'cliconnect_posts_cache_bust' );
add_action( 'delete_post', 'cliconnect_posts_cache_bust' );

/**
 * Busca posts de um CPT de catálogo, já ordenados por menu_order.
 *
 * Resultados são armazenados em transients por 1 hora e invalidados
 * automaticamente sempre que qualquer post é salvo ou excluído.
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

	$version   = cliconnect_posts_cache_version();
	$cache_key = 'cliconnect_posts_v' . $version . '_' . md5( (string) wp_json_encode( $args ) );
	$cached    = get_transient( $cache_key );

	if ( false !== $cached ) {
		return $cached;
	}

	$posts = get_posts( $args );
	set_transient( $cache_key, $posts, HOUR_IN_SECONDS );

	return $posts;
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
 * A tradução tem post_name próprio (`/en/cli-signature-service/`), então um
 * `is_page( 'cli-signature' )` seco devolve false em todo idioma que não o
 * padrão — e o CSS da página, que é enfileirado por esta função, não carrega.
 * Por isso, quando o slug não casa direto, o teste é refeito contra a página
 * de origem, no idioma padrão. Mesma estratégia de
 * `cliconnect_template_da_traducao()` em inc/polylang.php.
 *
 * @param string $slug Post name da página, no idioma padrão.
 * @return bool
 */
function cliconnect_e_pagina( $slug ) {
	if ( is_page( $slug ) ) {
		return true;
	}

	if ( ! is_page() || ! function_exists( 'pll_get_post' ) || ! function_exists( 'pll_default_language' ) ) {
		return false;
	}

	$padrao = pll_default_language();
	$atual  = function_exists( 'pll_current_language' ) ? pll_current_language() : '';

	if ( ! $padrao || $padrao === $atual ) {
		return false;
	}

	$origem = (int) pll_get_post( get_queried_object_id(), $padrao );

	return $origem && $slug === get_post_field( 'post_name', $origem );
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

/**
 * Permalink da página Política de Privacidade no idioma corrente.
 *
 * Com Polylang, a tradução tem slug próprio: partimos da página em português
 * e pedimos ao plugin a versão do idioma atual. Sem o plugin (ou sem tradução)
 * devolve a própria página; sem a página, a URL previsível.
 *
 * @return string
 */
function cliconnect_url_privacidade() {
	$pagina = get_page_by_path( 'privacidade' );
	$id     = $pagina ? (int) $pagina->ID : 0;

	if ( $id && function_exists( 'pll_get_post' ) ) {
		$traduzida = (int) pll_get_post( $id );
		$id        = $traduzida ? $traduzida : $id;
	}

	$link = $id ? get_permalink( $id ) : '';

	return $link ? $link : home_url( '/privacidade/' );
}

/**
 * Monta o `href` de um telefone em E.164 a partir do número exibido.
 *
 * O texto visível fica no padrão brasileiro — `(31) 4042-2051` — e o link
 * precisa do formato discável. Números já em formato internacional (com `+`)
 * só perdem a pontuação; os demais recebem o código do Brasil.
 *
 * @param string $telefone Telefone como exibido na tela.
 * @return string `tel:+55...`, ou string vazia se não sobrar dígito.
 */
function cliconnect_tel_href( $telefone ) {
	$telefone = (string) ( $telefone ?? '' );
	$digitos  = preg_replace( '/\D/', '', $telefone );

	if ( ! $digitos ) {
		return '';
	}

	if ( ! str_starts_with( trim( $telefone ), '+' ) ) {
		$digitos = '55' . $digitos;
	}

	return 'tel:+' . $digitos;
}
