<?php
/**
 * Grupo ACF da landing page de Solução.
 *
 * Template compartilhado por todas as páginas de solução (page-solucao.php).
 * Cada seção usa uma aba; seções opcionais ficam em abas separadas para que o
 * cliente deixe em branco quando a página não tiver aquela seção.
 *
 * Localização: page_template == page-solucao.php.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra o grupo de campos das landing pages de Solução.
 *
 * @return void
 */
function cliconnect_acf_fields_solucao_pagina() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$ps_text = static function ( $key, $label, $name, $extra = array() ) {
		return array_merge(
			array(
				'key'   => 'field_ps_' . $key,
				'label' => $label,
				'name'  => $name,
				'type'  => 'text',
			),
			$extra
		);
	};

	$ps_textarea = static function ( $key, $label, $name, $rows = 3 ) {
		return array(
			'key'       => 'field_ps_' . $key,
			'label'     => $label,
			'name'      => $name,
			'type'      => 'textarea',
			'rows'      => $rows,
			'new_lines' => '',
		);
	};

	$ps_image = static function ( $key, $label, $name, $instructions = '' ) {
		return array(
			'key'           => 'field_ps_' . $key,
			'label'         => $label,
			'name'          => $name,
			'type'          => 'image',
			'return_format' => 'id',
			'preview_size'  => 'medium',
			'instructions'  => $instructions,
		);
	};

	$ps_tab = static function ( $key, $label ) {
		return array(
			'key'       => 'field_ps_tab_' . $key,
			'label'     => $label,
			'name'      => '',
			'type'      => 'tab',
			'placement' => 'left',
		);
	};

	$fields = array();

	/* --- 1. HERO --------------------------------------------------------------- */
	$fields[] = $ps_tab( 'hero', '1 · Hero' );
	$fields[] = $ps_text( 'hero_eyebrow', 'Eyebrow', 'ps_hero_eyebrow', array(
		'instructions' => 'Pequena legenda acima do título. Ex.: "Para o seu Salesforce".',
	) );
	$fields[] = $ps_text( 'hero_titulo', 'Título — linha 1 (escuro)', 'ps_hero_titulo' );
	$fields[] = $ps_text( 'hero_titulo_destaque', 'Título — linha 2 (azul)', 'ps_hero_titulo_destaque' );
	$fields[] = $ps_textarea( 'hero_corpo', 'Corpo', 'ps_hero_corpo', 3 );
	$fields[] = $ps_text( 'hero_btn1_texto', 'Botão primário — Texto', 'ps_hero_btn1_texto' );
	$fields[] = $ps_text( 'hero_btn1_url', 'Botão primário — URL', 'ps_hero_btn1_url' );
	$fields[] = $ps_text( 'hero_btn2_texto', 'Botão secundário — Texto', 'ps_hero_btn2_texto' );
	$fields[] = $ps_text( 'hero_btn2_url', 'Botão secundário — URL', 'ps_hero_btn2_url' );
	$fields[] = $ps_image(
		'hero_imagem',
		'Imagem (coluna direita)',
		'ps_hero_imagem',
		'Visual da solução — logos conectados, diagrama, etc. Proporção quadrada recomendada.'
	);

	acf_add_local_field_group(
		array(
			'key'      => 'group_cli_solucao_pagina',
			'title'    => 'Solução — Conteúdo',
			'fields'   => $fields,
			'location' => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'page-solucao.php',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'cliconnect_acf_fields_solucao_pagina' );
