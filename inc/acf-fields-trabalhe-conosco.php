<?php
/**
 * Grupo ACF da página Trabalhe Conosco.
 *
 * Zero texto fixo: todo conteúdo editável sai daqui.
 * Campos numerados para conjuntos de tamanho fixo (ACF Free sem Repeater).
 * Localização por post_name — funciona em qualquer ambiente e com Polylang.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra o grupo de campos da página Trabalhe Conosco.
 *
 * @return void
 */
function cliconnect_acf_fields_trabalhe_conosco() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	// Helpers inline — prefixo tc_ para separar das chaves da home.
	$tc_text = static function ( $key, $label, $name, $extra = array() ) {
		return array_merge(
			array(
				'key'   => 'field_tc_' . $key,
				'label' => $label,
				'name'  => $name,
				'type'  => 'text',
			),
			$extra
		);
	};

	$tc_tab = static function ( $key, $label ) {
		return array(
			'key'       => 'field_tc_tab_' . $key,
			'label'     => $label,
			'name'      => '',
			'type'      => 'tab',
			'placement' => 'left',
		);
	};

	$tc_link = static function ( $key, $label, $name ) {
		return array(
			'key'           => 'field_tc_' . $key,
			'label'         => $label,
			'name'          => $name,
			'type'          => 'link',
			'return_format' => 'array',
		);
	};

	$tc_textarea = static function ( $key, $label, $name, $rows = 3 ) {
		return array(
			'key'       => 'field_tc_' . $key,
			'label'     => $label,
			'name'      => $name,
			'type'      => 'textarea',
			'rows'      => $rows,
			'new_lines' => '',
		);
	};

	$fields = array();

	/* --- 1. HERO --------------------------------------------------------- */
	$fields[] = $tc_tab( 'hero', '1 · Hero' );
	$fields[] = $tc_text( 'hero_eyebrow', 'Eyebrow', 'hero_eyebrow' );
	$fields[] = $tc_text( 'hero_titulo', 'Título', 'hero_titulo' );
	$fields[] = $tc_textarea( 'hero_texto', 'Texto de apoio', 'hero_texto' );
	$fields[] = $tc_link( 'hero_botao', 'Botão principal', 'hero_botao' );

	/* --- 2. SOBRE -------------------------------------------------------- */
	$fields[] = $tc_tab( 'sobre', '2 · Somos a CLI' );
	$fields[] = $tc_text( 'sobre_titulo', 'Título', 'sobre_titulo' );
	$fields[] = $tc_textarea( 'sobre_texto_1', 'Parágrafo 1', 'sobre_texto_1' );
	$fields[] = $tc_textarea( 'sobre_texto_2', 'Parágrafo 2', 'sobre_texto_2' );
	$fields[] = array(
		'key'           => 'field_tc_sobre_foto_1',
		'label'         => 'Foto da equipe',
		'name'          => 'sobre_foto_1',
		'type'          => 'image',
		'return_format' => 'id',
		'preview_size'  => 'medium',
	);

	/* --- 3. MÉTRICAS ----------------------------------------------------- */
	$fields[] = $tc_tab( 'metricas', '3 · Métricas' );
	for ( $i = 1; $i <= 3; $i++ ) {
		$fields[] = $tc_text( "metrica_{$i}_numero", "Métrica {$i} — número", "tc_metrica_{$i}_numero" );
		$fields[] = $tc_text( "metrica_{$i}_rotulo", "Métrica {$i} — descrição", "tc_metrica_{$i}_rotulo" );
	}

	/* --- 4. FRASE -------------------------------------------------------- */
	$fields[] = $tc_tab( 'frase', '4 · Frase de Impacto' );
	$fields[] = $tc_text( 'frase_parte_1', 'Parte 1 (texto simples)', 'tc_frase_parte_1' );
	$fields[] = $tc_text( 'frase_parte_2', 'Parte 2 (em azul)', 'tc_frase_parte_2' );

	/* --- 5. VALORES ------------------------------------------------------ */
	$fields[] = $tc_tab( 'valores', '5 · Valores' );
	$fields[] = $tc_text( 'valores_eyebrow', 'Eyebrow', 'valores_eyebrow' );
	$fields[] = $tc_text( 'valores_titulo', 'Título', 'valores_titulo' );
	$fields[] = $tc_link( 'valores_cta', 'Botão CTA (card azul)', 'valores_cta' );
	for ( $i = 1; $i <= 5; $i++ ) {
		$fields[] = $tc_text(
			"valor_{$i}_icone",
			"Valor {$i} — chave do ícone",
			"valor_{$i}_icone",
			array( 'instructions' => 'Chave de cliconnect_icone(). Ex.: escudo, lampada, grupo, verificado.' )
		);
		$fields[] = $tc_text( "valor_{$i}_titulo", "Valor {$i} — título", "valor_{$i}_titulo" );
		$fields[] = $tc_textarea( "valor_{$i}_texto", "Valor {$i} — texto", "valor_{$i}_texto", 2 );
	}

	/* --- 6. DEPOIMENTO --------------------------------------------------- */
	$fields[] = $tc_tab( 'depoimento', '6 · Depoimento' );
	$fields[] = array(
		'key'           => 'field_tc_dep_foto',
		'label'         => 'Foto',
		'name'          => 'dep_foto',
		'type'          => 'image',
		'return_format' => 'id',
		'preview_size'  => 'thumbnail',
	);
	$fields[] = $tc_text( 'dep_nome', 'Nome', 'dep_nome' );
	$fields[] = $tc_text( 'dep_cargo', 'Cargo / time', 'dep_cargo' );
	$fields[] = $tc_textarea( 'dep_texto', 'Citação', 'dep_texto', 4 );

	/* --- 7. BENEFÍCIOS --------------------------------------------------- */
	$fields[] = $tc_tab( 'beneficios', '7 · Benefícios' );
	$fields[] = $tc_text( 'beneficios_eyebrow', 'Eyebrow', 'beneficios_eyebrow' );
	$fields[] = $tc_text( 'beneficios_titulo', 'Título', 'beneficios_titulo' );
	$fields[] = $tc_textarea( 'beneficios_subtitulo', 'Subtítulo', 'beneficios_subtitulo', 2 );
	for ( $i = 1; $i <= 6; $i++ ) {
		$fields[] = $tc_text(
			"beneficio_{$i}_icone",
			"Benefício {$i} — chave do ícone",
			"beneficio_{$i}_icone",
			array( 'instructions' => 'Chave de cliconnect_icone(). Ex.: coracao, casa, refeicao, bolo.' )
		);
		$fields[] = $tc_text( "beneficio_{$i}_titulo", "Benefício {$i} — título", "beneficio_{$i}_titulo" );
		$fields[] = $tc_textarea( "beneficio_{$i}_texto", "Benefício {$i} — texto", "beneficio_{$i}_texto", 2 );
	}

	/* --- 8. JEITO CLI ---------------------------------------------------- */
	$fields[] = $tc_tab( 'jeito', '8 · O jeito CLI de ser' );
	$fields[] = $tc_text( 'jeito_titulo', 'Título (coluna esquerda)', 'jeito_titulo' );
	for ( $i = 1; $i <= 5; $i++ ) {
		$fields[] = $tc_text( "jeito_item_{$i}_titulo", "Item {$i}", "jeito_item_{$i}_titulo" );
	}
	$fields[] = $tc_textarea( 'jeito_texto', 'Texto explicativo (coluna direita)', 'jeito_texto', 4 );
	$fields[] = $tc_link( 'jeito_botao', 'Botão', 'jeito_botao' );

	/* --- 9. BLOG --------------------------------------------------------- */
	$fields[] = $tc_tab( 'blog', '9 · Blog' );
	$fields[] = $tc_text( 'blog_titulo', 'Título da seção', 'tc_blog_titulo' );

	acf_add_local_field_group(
		array(
			'key'      => 'group_cli_trabalhe_conosco',
			'title'    => 'Trabalhe Conosco — Conteúdo',
			'fields'   => $fields,
			'location' => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'page-trabalhe-conosco.php',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'cliconnect_acf_fields_trabalhe_conosco' );
