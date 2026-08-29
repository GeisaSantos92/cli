<?php
/**
 * Grupo ACF da página Contato.
 *
 * Zero texto fixo: todo conteúdo editável sai daqui.
 * Localização por page_template == page-contato.php.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra o grupo de campos da página Contato.
 *
 * @return void
 */
function cliconnect_acf_fields_contato() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$ct_text = static function ( $key, $label, $name, $extra = array() ) {
		return array_merge(
			array(
				'key'   => 'field_ct_' . $key,
				'label' => $label,
				'name'  => $name,
				'type'  => 'text',
			),
			$extra
		);
	};

	$ct_textarea = static function ( $key, $label, $name, $rows = 3 ) {
		return array(
			'key'       => 'field_ct_' . $key,
			'label'     => $label,
			'name'      => $name,
			'type'      => 'textarea',
			'rows'      => $rows,
			'new_lines' => '',
		);
	};

	$ct_tab = static function ( $key, $label ) {
		return array(
			'key'       => 'field_ct_tab_' . $key,
			'label'     => $label,
			'name'      => '',
			'type'      => 'tab',
			'placement' => 'left',
		);
	};

	$fields = array();

	/* --- 2. CLIENTES ----------------------------------------------------------- */
	$fields[] = $ct_tab( 'clientes', '2 · Clientes' );
	$fields[] = $ct_text( 'clientes_subtitulo', 'Subtítulo', 'ct_clientes_subtitulo' );

	/* --- 1. FORMULÁRIO DE CONTATO ------------------------------------------- */
	$fields[] = $ct_tab( 'formulario', '1 · Formulário' );
	$fields[] = $ct_text( 'form_titulo', 'Título', 'ct_form_titulo' );
	$fields[] = $ct_textarea( 'form_texto', 'Texto de apoio', 'ct_form_texto', 3 );
	$fields[] = $ct_text( 'form_email', 'E-mail de contato', 'ct_form_email' );
	$fields[] = $ct_text( 'form_telefone', 'Telefone', 'ct_form_telefone' );
	$fields[] = $ct_text( 'form_linkedin_url', 'URL — LinkedIn', 'ct_form_linkedin_url' );
	$fields[] = $ct_text( 'form_instagram_url', 'URL — Instagram', 'ct_form_instagram_url' );
	$fields[] = $ct_text( 'form_whatsapp_url', 'URL — WhatsApp', 'ct_form_whatsapp_url' );
	$fields[] = array_merge(
		$ct_text( 'form_cf7_id', 'ID do formulário CF7', 'ct_form_cf7_id' ),
		array(
			'instructions' => 'Insira o ID numérico do formulário criado no Contact Form 7.',
		)
	);

	acf_add_local_field_group(
		array(
			'key'      => 'group_cli_contato',
			'title'    => 'Contato — Conteúdo',
			'fields'   => $fields,
			'location' => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'page-contato.php',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'cliconnect_acf_fields_contato' );
