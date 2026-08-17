<?php
/**
 * Grupo ACF da página CLI Signature.
 *
 * Zero texto fixo: todo conteúdo editável sai daqui.
 * Localização por page_template == page-cli-signature.php.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra o grupo de campos da página CLI Signature.
 *
 * @return void
 */
function cliconnect_acf_fields_cli_signature() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$cs_text = static function ( $key, $label, $name, $extra = array() ) {
		return array_merge(
			array(
				'key'   => 'field_cs_' . $key,
				'label' => $label,
				'name'  => $name,
				'type'  => 'text',
			),
			$extra
		);
	};

	$cs_tab = static function ( $key, $label ) {
		return array(
			'key'       => 'field_cs_tab_' . $key,
			'label'     => $label,
			'name'      => '',
			'type'      => 'tab',
			'placement' => 'left',
		);
	};

	$cs_link = static function ( $key, $label, $name ) {
		return array(
			'key'           => 'field_cs_' . $key,
			'label'         => $label,
			'name'          => $name,
			'type'          => 'link',
			'return_format' => 'array',
		);
	};

	$cs_textarea = static function ( $key, $label, $name, $rows = 3 ) {
		return array(
			'key'       => 'field_cs_' . $key,
			'label'     => $label,
			'name'      => $name,
			'type'      => 'textarea',
			'rows'      => $rows,
			'new_lines' => '',
		);
	};

	$cs_image = static function ( $key, $label, $name ) {
		return array(
			'key'           => 'field_cs_' . $key,
			'label'         => $label,
			'name'          => $name,
			'type'          => 'image',
			'return_format' => 'id',
			'preview_size'  => 'medium',
		);
	};

	$fields = array();

	/* --- 1. HERO ------------------------------------------------------------ */
	$fields[] = $cs_tab( 'hero', '1 · Hero' );
	$fields[] = $cs_text( 'hero_eyebrow', 'Eyebrow', 'cs_hero_eyebrow' );
	$fields[] = $cs_text( 'hero_titulo', 'Título', 'cs_hero_titulo' );
	$fields[] = $cs_textarea( 'hero_texto', 'Texto de apoio', 'cs_hero_texto', 3 );
	$fields[] = $cs_link( 'hero_botao', 'Botão', 'cs_hero_botao' );
	$fields[] = $cs_image( 'hero_bg', 'Imagem de fundo', 'cs_hero_bg' );

	/* --- 2. PARA QUEM (CENÁRIOS) -------------------------------------------- */
	$fields[] = $cs_tab( 'cenarios', '2 · Para quem' );
	$fields[] = $cs_text( 'cenarios_eyebrow', 'Eyebrow', 'cs_cenarios_eyebrow' );
	$fields[] = $cs_text( 'cenarios_titulo', 'Título', 'cs_cenarios_titulo' );
	$fields[] = $cs_textarea( 'cenarios_texto', 'Texto de apoio', 'cs_cenarios_texto', 2 );
	for ( $i = 1; $i <= 6; $i++ ) {
		$fields[] = $cs_text( "cenarios_{$i}_titulo", "Card {$i} — título", "cs_cenarios_{$i}_titulo" );
		$fields[] = $cs_textarea( "cenarios_{$i}_texto", "Card {$i} — descrição", "cs_cenarios_{$i}_texto", 2 );
	}

	/* --- 3. PILARES (EXPERIÊNCIA ENTERPRISE) --------------------------------- */
	$fields[] = $cs_tab( 'pilares', '3 · Pilares' );
	$fields[] = $cs_text( 'pilares_eyebrow', 'Eyebrow', 'cs_pilares_eyebrow' );
	$fields[] = $cs_text( 'pilares_titulo', 'Título', 'cs_pilares_titulo' );
	$fields[] = $cs_textarea( 'pilares_texto', 'Texto de apoio', 'cs_pilares_texto', 2 );
	for ( $i = 1; $i <= 3; $i++ ) {
		$fields[] = $cs_image( "pilares_{$i}_imagem", "Card {$i} — imagem", "cs_pilares_{$i}_imagem" );
		$fields[] = $cs_text( "pilares_{$i}_titulo", "Card {$i} — título", "cs_pilares_{$i}_titulo" );
		$fields[] = $cs_textarea( "pilares_{$i}_texto", "Card {$i} — descrição", "cs_pilares_{$i}_texto", 2 );
	}

	/* --- 4. DIFERENCIAIS ----------------------------------------------------- */
	$fields[] = $cs_tab( 'diferenciais', '4 · Diferenciais' );
	$fields[] = $cs_text( 'diferenciais_titulo_1', 'Título — linha 1', 'cs_diferenciais_titulo_1' );
	$fields[] = $cs_text( 'diferenciais_titulo_2', 'Título — linha 2', 'cs_diferenciais_titulo_2' );
	$fields[] = $cs_textarea( 'diferenciais_texto', 'Texto de apoio', 'cs_diferenciais_texto', 2 );
	for ( $i = 1; $i <= 6; $i++ ) {
		$fields[] = $cs_text( "diferenciais_{$i}_titulo", "Card {$i} — título", "cs_diferenciais_{$i}_titulo" );
		$fields[] = $cs_textarea( "diferenciais_{$i}_texto", "Card {$i} — descrição", "cs_diferenciais_{$i}_texto", 2 );
	}

	/* --- 7. OPERAÇÃO GERENCIADA ------------------------------------------------ */
	$fields[] = $cs_tab( 'operacao', '7 · Operação Gerenciada' );
	$fields[] = $cs_text( 'operacao_eyebrow', 'Eyebrow', 'cs_operacao_eyebrow' );
	$fields[] = $cs_text( 'operacao_titulo_1', 'Título — linha 1', 'cs_operacao_titulo_1' );
	$fields[] = $cs_text( 'operacao_titulo_2', 'Título — linha 2', 'cs_operacao_titulo_2' );
	$fields[] = $cs_textarea( 'operacao_texto', 'Texto de apoio', 'cs_operacao_texto', 2 );
	for ( $i = 1; $i <= 4; $i++ ) {
		$fields[] = $cs_text( "operacao_{$i}_titulo", "Card {$i} — título", "cs_operacao_{$i}_titulo" );
		$fields[] = $cs_textarea( "operacao_{$i}_texto", "Card {$i} — descrição", "cs_operacao_{$i}_texto", 2 );
	}

	/* --- 5. GESTOR DE PROJETO -------------------------------------------------- */
	$fields[] = $cs_tab( 'gestor', '5 · Gestor de Projeto' );
	$fields[] = $cs_text( 'gestor_titulo', 'Título', 'cs_gestor_titulo' );
	$fields[] = $cs_textarea( 'gestor_texto', 'Texto de apoio', 'cs_gestor_texto', 3 );
	$fields[] = $cs_link( 'gestor_botao', 'Botão', 'cs_gestor_botao' );
	for ( $i = 1; $i <= 7; $i++ ) {
		$fields[] = $cs_text( "gestor_{$i}_titulo", "Tópico {$i} — título", "cs_gestor_{$i}_titulo" );
	}

	/* --- 6. ARQUITETO DEDICADO ------------------------------------------------- */
	$fields[] = $cs_tab( 'arquiteto', '6 · Arquiteto' );
	$fields[] = $cs_text( 'arquiteto_titulo', 'Título', 'cs_arquiteto_titulo' );
	$fields[] = $cs_textarea( 'arquiteto_texto', 'Texto de apoio', 'cs_arquiteto_texto', 3 );
	$fields[] = $cs_link( 'arquiteto_botao', 'Botão', 'cs_arquiteto_botao' );
	for ( $i = 1; $i <= 7; $i++ ) {
		$fields[] = $cs_text( "arquiteto_{$i}_titulo", "Tópico {$i} — título", "cs_arquiteto_{$i}_titulo" );
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_cli_cli_signature',
			'title'    => 'CLI Signature — Conteúdo',
			'fields'   => $fields,
			'location' => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'page-cli-signature.php',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'cliconnect_acf_fields_cli_signature' );
