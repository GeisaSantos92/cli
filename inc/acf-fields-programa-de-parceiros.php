<?php
/**
 * Grupo ACF da página Programa de Parceiros.
 *
 * Zero texto fixo: todo conteúdo editável sai daqui. Conjuntos de tamanho fixo
 * (métricas, cartões, passos, perfis, itens da estrutura, atribuições) usam
 * campos numerados — o ACF Free não tem Repeater. Logos e FAQ vêm dos CPTs
 * `cli_cliente` e `cli_faq`.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra o grupo de campos da página Programa de Parceiros.
 *
 * @return void
 */
function cliconnect_acf_fields_programa_de_parceiros() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$texto = static function ( $nome, $rotulo, $extra = array() ) {
		return array_merge(
			array(
				'key'   => 'field_' . $nome,
				'label' => $rotulo,
				'name'  => $nome,
				'type'  => 'text',
			),
			$extra
		);
	};

	$area = static function ( $nome, $rotulo, $linhas = 3, $extra = array() ) {
		return array_merge(
			array(
				'key'       => 'field_' . $nome,
				'label'     => $rotulo,
				'name'      => $nome,
				'type'      => 'textarea',
				'rows'      => $linhas,
				'new_lines' => '',
			),
			$extra
		);
	};

	$link = static function ( $nome, $rotulo ) {
		return array(
			'key'           => 'field_' . $nome,
			'label'         => $rotulo,
			'name'          => $nome,
			'type'          => 'link',
			'return_format' => 'array',
		);
	};

	$imagem = static function ( $nome, $rotulo ) {
		return array(
			'key'           => 'field_' . $nome,
			'label'         => $rotulo,
			'name'          => $nome,
			'type'          => 'image',
			'return_format' => 'id',
			'preview_size'  => 'medium',
		);
	};

	$aba = static function ( $chave, $rotulo ) {
		return array(
			'key'       => 'field_pp_aba_' . $chave,
			'label'     => $rotulo,
			'name'      => '',
			'type'      => 'tab',
			'placement' => 'left',
		);
	};

	$aviso = static function ( $chave, $mensagem ) {
		return array(
			'key'     => 'field_pp_aviso_' . $chave,
			'label'   => '',
			'name'    => '',
			'type'    => 'message',
			'message' => $mensagem,
		);
	};

	$icone_ms = array( 'instructions' => 'Nome do ícone no Material Symbols Rounded (ex.: open_in_full, center_focus_weak, paid).' );

	$fields = array();

	/* --- 1. HERO --------------------------------------------------------- */
	$fields[] = $aba( 'hero', '1 · Hero' );
	$fields[] = $texto( 'pp_hero_eyebrow', 'Eyebrow' );
	$fields[] = $texto( 'pp_hero_titulo', 'Título' );
	$fields[] = $area(
		'pp_hero_texto',
		'Texto de apoio',
		3,
		array( 'instructions' => 'Envolva um trecho em *asteriscos* para destacá-lo em negrito.' )
	);
	$fields[] = $link( 'pp_hero_botao', 'Botão principal' );
	$fields[] = $link( 'pp_hero_botao_2', 'Botão secundário' );
	$fields[] = $imagem( 'pp_hero_imagem', 'Imagem de fundo' );
	for ( $i = 1; $i <= 3; $i++ ) {
		$fields[] = $texto( "pp_hero_metrica_{$i}_numero", "Métrica {$i} — número" );
		$fields[] = $texto( "pp_hero_metrica_{$i}_rotulo", "Métrica {$i} — descrição" );
	}

	/* --- 2. PARCERIA ----------------------------------------------------- */
	$fields[] = $aba( 'parceria', '2 · Parceria' );
	$fields[] = $texto( 'pp_parceria_eyebrow', 'Eyebrow' );
	$fields[] = $texto( 'pp_parceria_titulo', 'Título' );
	$fields[] = $area( 'pp_parceria_texto', 'Texto de apoio', 2 );
	for ( $i = 1; $i <= 3; $i++ ) {
		$fields[] = $texto( "pp_parceria_{$i}_icone", "Cartão {$i} — ícone", $icone_ms );
		$fields[] = $texto( "pp_parceria_{$i}_titulo", "Cartão {$i} — título" );
		$fields[] = $area( "pp_parceria_{$i}_texto", "Cartão {$i} — texto", 2 );
	}

	/* --- 3. LOGOS -------------------------------------------------------- */
	$fields[] = $aba( 'logos', '3 · Logos de clientes' );
	$fields[] = $aviso( 'logos', 'Os logos vêm do cadastro de <strong>Clientes</strong>, na ordem definida lá.' );
	$fields[] = array(
		'key'           => 'field_pp_logos_quantidade',
		'label'         => 'Quantidade de logos',
		'name'          => 'pp_logos_quantidade',
		'type'          => 'number',
		'min'           => 0,
		'max'           => 12,
		'default_value' => 7,
		'instructions'  => '0 esconde a faixa.',
	);

	/* --- 4. COMO FUNCIONA ------------------------------------------------ */
	$fields[] = $aba( 'como', '4 · Como funciona' );
	$fields[] = $texto( 'pp_como_titulo', 'Título' );
	for ( $i = 1; $i <= 4; $i++ ) {
		$fields[] = $texto( "pp_como_{$i}_titulo", "Passo {$i} — título" );
		$fields[] = $area( "pp_como_{$i}_texto", "Passo {$i} — texto", 2 );
		$fields[] = $imagem( "pp_como_{$i}_imagem", "Passo {$i} — ilustração" );
	}

	/* --- 5. PERFIL ------------------------------------------------------- */
	$fields[] = $aba( 'perfil', '5 · Perfil do parceiro' );
	$fields[] = $texto( 'pp_perfil_eyebrow', 'Eyebrow' );
	$fields[] = $texto( 'pp_perfil_titulo', 'Título' );
	$fields[] = $area( 'pp_perfil_texto', 'Texto de apoio', 2 );
	for ( $i = 1; $i <= 4; $i++ ) {
		$fields[] = $texto( "pp_perfil_{$i}_titulo", "Perfil {$i} — título" );
		$fields[] = $area( "pp_perfil_{$i}_texto", "Perfil {$i} — texto", 2 );
	}

	/* --- 6. ESTRUTURA ---------------------------------------------------- */
	$fields[] = $aba( 'estrutura', '6 · Estrutura' );
	$fields[] = $area( 'pp_estrutura_titulo', 'Título', 2, array( 'instructions' => 'Cada linha vira uma linha do título.' ) );
	$fields[] = $area( 'pp_estrutura_texto', 'Texto de apoio', 2 );
	for ( $i = 1; $i <= 4; $i++ ) {
		$fields[] = $texto( "pp_estrutura_{$i}_titulo", "Item {$i} — título" );
		$fields[] = $area( "pp_estrutura_{$i}_texto", "Item {$i} — texto", 2, 1 === $i ? array( 'instructions' => 'O primeiro item já abre expandido.' ) : array() );
	}
	$fields[] = $imagem( 'pp_estrutura_imagem', 'Ilustração' );

	/* --- 7. ATUAÇÃO CONJUNTA --------------------------------------------- */
	$fields[] = $aba( 'atuacao', '7 · Atuação conjunta' );
	$fields[] = $texto( 'pp_atuacao_eyebrow', 'Eyebrow' );
	$fields[] = $texto( 'pp_atuacao_titulo', 'Título' );
	$fields[] = $area( 'pp_atuacao_texto', 'Texto de apoio', 2 );
	foreach ( array( 'parceiro' => 'Coluna do parceiro', 'cli' => 'Coluna da CLI Connect' ) as $lado => $rotulo ) {
		$fields[] = $texto( "pp_atuacao_{$lado}_selo", "{$rotulo} — selo de topo", array( 'instructions' => 'Opcional. Ex.: OPERAÇÃO TÉCNICA.' ) );
		$fields[] = $texto( "pp_atuacao_{$lado}_rotulo", "{$rotulo} — rótulo" );
		$fields[] = $texto( "pp_atuacao_{$lado}_titulo", "{$rotulo} — título" );
		$fields[] = $texto( "pp_atuacao_{$lado}_tag", "{$rotulo} — etiqueta" );
		for ( $i = 1; $i <= 6; $i++ ) {
			$fields[] = $texto( "pp_atuacao_{$lado}_{$i}", "{$rotulo} — item {$i}" );
		}
		$fields[] = $texto( "pp_atuacao_{$lado}_rodape", "{$rotulo} — rodapé" );
	}

	/* --- 8. FAQ ---------------------------------------------------------- */
	$fields[] = $aba( 'faq', '8 · FAQ' );
	$fields[] = $texto( 'pp_faq_eyebrow', 'Eyebrow' );
	$fields[] = $texto( 'pp_faq_titulo', 'Título' );
	$fields[] = array(
		'key'           => 'field_pp_faq_itens',
		'label'         => 'Perguntas',
		'name'          => 'pp_faq_itens',
		'type'          => 'relationship',
		'post_type'     => array( 'cli_faq' ),
		'filters'       => array( 'search' ),
		'return_format' => 'object',
		'instructions'  => 'A pergunta é o título do item de FAQ; a resposta, o conteúdo.',
	);

	/* --- 9. CTA ---------------------------------------------------------- */
	$fields[] = $aba( 'cta', '9 · Próximo passo' );
	$fields[] = $texto( 'pp_cta_eyebrow', 'Eyebrow' );
	$fields[] = $texto( 'pp_cta_titulo', 'Título' );
	$fields[] = $area( 'pp_cta_texto', 'Texto de apoio', 2 );
	$fields[] = $link( 'pp_cta_botao', 'Botão' );

	acf_add_local_field_group(
		array(
			'key'             => 'group_cli_programa_de_parceiros',
			'title'           => 'Conteúdo da Página Programa de Parceiros',
			'location'        => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'page-programa-de-parceiros.php',
					),
				),
			),
			'menu_order'      => 0,
			'position'        => 'normal',
			'style'           => 'default',
			'label_placement' => 'top',
			'hide_on_screen'  => array( 'the_content', 'excerpt', 'custom_fields', 'discussion', 'comments' ),
			'fields'          => $fields,
		)
	);
}
add_action( 'acf/init', 'cliconnect_acf_fields_programa_de_parceiros' );
