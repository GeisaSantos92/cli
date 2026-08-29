<?php
/**
 * Grupo ACF da página Política de Privacidade.
 *
 * Página institucional de texto longo: cabeçalho curto + corpo rico editável.
 * Zero texto fixo no template — tudo sai daqui.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra o grupo de campos da página Política de Privacidade.
 *
 * @return void
 */
function cliconnect_acf_fields_privacidade() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'            => 'group_cli_privacidade',
			'title'          => 'Política de Privacidade — Conteúdo',
			'hide_on_screen' => array( 'the_content', 'excerpt', 'custom_fields', 'discussion', 'comments' ),
			'fields'         => array(
				array(
					'key'   => 'field_pv_titulo',
					'label' => 'Título',
					'name'  => 'pv_titulo',
					'type'  => 'text',
				),
				array(
					'key'          => 'field_pv_lead',
					'label'        => 'Lead',
					'name'         => 'pv_lead',
					'type'         => 'textarea',
					'rows'         => 3,
					'new_lines'    => '',
					'instructions' => 'Uma ou duas frases abaixo do título. Opcional.',
				),
				array(
					'key'          => 'field_pv_atualizado_em',
					'label'        => 'Atualizado em',
					'name'         => 'pv_atualizado_em',
					'type'         => 'text',
					'instructions' => 'Texto exibido acima do corpo (ex.: "Atualizado em 28 de agosto de 2026").',
				),
				array(
					'key'          => 'field_pv_corpo',
					'label'        => 'Corpo',
					'name'         => 'pv_corpo',
					'type'         => 'wysiwyg',
					'tabs'         => 'all',
					'media_upload' => 0,
					'delay'        => 0,
					'instructions' => 'Texto da política. Use H2 para as seções e H3 para subitens.',
				),
			),
			'location'       => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'page-privacidade.php',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'cliconnect_acf_fields_privacidade' );
