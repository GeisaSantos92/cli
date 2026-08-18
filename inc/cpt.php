<?php
/**
 * Custom Post Types do CLI Connect.
 *
 * Conteúdos que se repetem no site (e que o cliente precisa manter sozinho)
 * viram CPT — nunca campo repetidor gigante nem HTML fixo no template.
 *
 * - cli_agente      Agentes de IA exibidos na esteira da home.
 * - cli_integracao  Sistemas integrados (SAP, TOTVS, ...) — órbita do hero,
 *                   malha "Sua integração pode já estar pronta" e cards de agente.
 * - cli_cliente     Logos de clientes/parceiros (esteira e prova social).
 * - cli_case        Cases de sucesso: depoimento em vídeo e cards de métrica.
 * - cli_evento      Cards de "Eventos Automáticos" da home.
 * - cli_faq         Perguntas frequentes.
 * - cli_selo        Selos de compliance e certificações.
 *
 * Como o ACF Free não tem Repeater, TODA lista que pode crescer vira CPT.
 * Só conjuntos de tamanho fixo (métricas, camadas, departamentos, canais)
 * ficam como campos numerados no grupo ACF da home.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Monta o array de labels de um CPT a partir do singular/plural.
 *
 * @param string $singular Nome no singular (ex.: 'Agente').
 * @param string $plural   Nome no plural (ex.: 'Agentes').
 * @param string $genero   'm' (masculino) ou 'f' (feminino), para as concordâncias.
 * @return array<string,string>
 */
function cliconnect_cpt_labels( $singular, $plural, $genero = 'm' ) {
	$novo   = ( 'f' === $genero ) ? 'Nova' : 'Novo';
	$o      = ( 'f' === $genero ) ? 'a' : 'o';
	$nenhum = ( 'f' === $genero ) ? 'Nenhuma' : 'Nenhum';

	return array(
		'name'               => $plural,
		'singular_name'      => $singular,
		'menu_name'          => $plural,
		'add_new'            => sprintf( '%s %s', $novo, $singular ),
		'add_new_item'       => sprintf( 'Adicionar %s %s', $novo, $singular ),
		'edit_item'          => sprintf( 'Editar %s', $singular ),
		'new_item'           => sprintf( '%s %s', $novo, $singular ),
		'view_item'          => sprintf( 'Ver %s', $singular ),
		'view_items'         => sprintf( 'Ver %s', $plural ),
		'search_items'       => sprintf( 'Buscar %s', $plural ),
		'not_found'          => sprintf( '%s %s encontrad%s.', $nenhum, $singular, $o ),
		'not_found_in_trash' => sprintf( '%s %s na lixeira.', $nenhum, $singular ),
		'all_items'          => sprintf( 'Tod%ss %s', $o, $plural ),
		'archives'           => sprintf( 'Arquivo de %s', $plural ),
	);
}

/**
 * Registra os CPTs do tema.
 *
 * @return void
 */
function cliconnect_register_post_types() {
	// --- Agentes de IA ------------------------------------------------------
	register_post_type(
		'cli_agente',
		array(
			'labels'        => cliconnect_cpt_labels( 'Agente', 'Agentes de IA' ),
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-superhero',
			'menu_position' => 21,
			'supports'      => array( 'title', 'page-attributes' ),
			'has_archive'   => false,
			'rewrite'       => false,
		)
	);

	// --- Integrações / sistemas --------------------------------------------
	register_post_type(
		'cli_integracao',
		array(
			'labels'        => cliconnect_cpt_labels( 'Integração', 'Integrações', 'f' ),
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-networking',
			'menu_position' => 22,
			'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
			'has_archive'   => false,
			'rewrite'       => false,
		)
	);

	// --- Clientes / parceiros ----------------------------------------------
	register_post_type(
		'cli_cliente',
		array(
			'labels'        => cliconnect_cpt_labels( 'Cliente', 'Clientes' ),
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-groups',
			'menu_position' => 23,
			'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
			'has_archive'   => false,
			'rewrite'       => false,
		)
	);

	// --- Cases de sucesso ---------------------------------------------------
	register_post_type(
		'cli_case',
		array(
			'labels'        => cliconnect_cpt_labels( 'Case', 'Cases' ),
			'public'        => true,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-awards',
			'menu_position' => 24,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
			'has_archive'   => 'cases',
			'rewrite'       => array(
				'slug'       => 'cases',
				'with_front' => false,
			),
		)
	);

	// --- Eventos automáticos (cards ilustrados da home) ---------------------
	register_post_type(
		'cli_evento',
		array(
			'labels'        => cliconnect_cpt_labels( 'Evento', 'Eventos Automáticos' ),
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-controls-repeat',
			'menu_position' => 25,
			'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
			'has_archive'   => false,
			'rewrite'       => false,
		)
	);

	// --- FAQ ----------------------------------------------------------------
	register_post_type(
		'cli_faq',
		array(
			'labels'        => cliconnect_cpt_labels( 'Pergunta', 'Perguntas Frequentes', 'f' ),
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-editor-help',
			'menu_position' => 26,
			'supports'      => array( 'title', 'editor', 'page-attributes' ),
			'has_archive'   => false,
			'rewrite'       => false,
		)
	);

	// --- Selos de compliance ------------------------------------------------
	register_post_type(
		'cli_selo',
		array(
			'labels'        => cliconnect_cpt_labels( 'Selo', 'Selos & Certificações' ),
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-shield-alt',
			'menu_position' => 27,
			'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
			'has_archive'   => false,
			'rewrite'       => false,
		)
	);

	// --- Soluções (catálogo público — tecnologias, segmentos, iniciativas) --
	register_post_type(
		'cli_solucao',
		array(
			'labels'        => cliconnect_cpt_labels( 'Solução', 'Soluções', 'f' ),
			'public'        => true,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-chart-area',
			'menu_position' => 28,
			'supports'      => array( 'title', 'thumbnail', 'excerpt', 'page-attributes' ),
			'has_archive'   => 'solucoes',
			'rewrite'       => array(
				'slug'       => 'solucao',
				'with_front' => false,
			),
		)
	);
}
add_action( 'init', 'cliconnect_register_post_types' );

/**
 * Taxonomia de categorias para as integrações (ERP, E-commerce, CRM, ...).
 *
 * @return void
 */
function cliconnect_register_taxonomies() {
	register_taxonomy(
		'cli_categoria_integracao',
		array( 'cli_integracao' ),
		array(
			'labels'            => array(
				'name'          => 'Categorias de Integração',
				'singular_name' => 'Categoria de Integração',
				'menu_name'     => 'Categorias',
				'all_items'     => 'Todas as Categorias',
				'edit_item'     => 'Editar Categoria',
				'add_new_item'  => 'Adicionar Categoria',
			),
			'public'            => false,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'hierarchical'      => true,
			'rewrite'           => false,
		)
	);

	// --- Categorias de Solução (hierárquica: Tecnologia > SAP, etc.) --------
	register_taxonomy(
		'cli_categoria_solucao',
		array( 'cli_solucao' ),
		array(
			'labels'            => array(
				'name'          => 'Categorias de Solução',
				'singular_name' => 'Categoria de Solução',
				'menu_name'     => 'Categorias',
				'all_items'     => 'Todas as Categorias',
				'edit_item'     => 'Editar Categoria',
				'add_new_item'  => 'Adicionar Categoria',
				'search_items'  => 'Buscar Categorias',
			),
			'public'            => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'hierarchical'      => true,
			'rewrite'           => array(
				'slug'         => 'solucoes',
				'hierarchical' => true,
				'with_front'   => false,
			),
		)
	);

	register_taxonomy(
		'cli_segmento',
		array( 'cli_case' ),
		array(
			'labels'            => array(
				'name'          => 'Segmentos',
				'singular_name' => 'Segmento',
				'menu_name'     => 'Segmentos',
				'all_items'     => 'Todos os Segmentos',
				'edit_item'     => 'Editar Segmento',
				'add_new_item'  => 'Adicionar Segmento',
			),
			'public'            => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'hierarchical'      => true,
			'rewrite'           => array( 'slug' => 'segmento' ),
		)
	);
}
add_action( 'init', 'cliconnect_register_taxonomies' );

/**
 * Ordena os CPTs "de catálogo" pelo campo Ordem (menu_order) no admin.
 *
 * @param WP_Query $query Query em execução.
 * @return void
 */
function cliconnect_admin_order_cpts( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}

	$ordenaveis = array( 'cli_agente', 'cli_integracao', 'cli_cliente', 'cli_evento', 'cli_faq', 'cli_selo', 'cli_solucao' );

	if ( in_array( $query->get( 'post_type' ), $ordenaveis, true ) && ! $query->get( 'orderby' ) ) {
		$query->set( 'orderby', 'menu_order title' );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'cliconnect_admin_order_cpts' );
