<?php
/**
 * Grupo ACF da página CLI Flow.
 *
 * Zero texto fixo: todo conteúdo editável sai daqui.
 * Localização por page_template == page-cli-flow.php.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra o grupo de campos da página CLI Flow.
 *
 * @return void
 */
function cliconnect_acf_fields_cli_flow() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$cf_text = static function ( $key, $label, $name, $extra = array() ) {
		return array_merge(
			array(
				'key'   => 'field_cf_' . $key,
				'label' => $label,
				'name'  => $name,
				'type'  => 'text',
			),
			$extra
		);
	};

	$cf_tab = static function ( $key, $label ) {
		return array(
			'key'       => 'field_cf_tab_' . $key,
			'label'     => $label,
			'name'      => '',
			'type'      => 'tab',
			'placement' => 'left',
		);
	};

	$cf_link = static function ( $key, $label, $name ) {
		return array(
			'key'           => 'field_cf_' . $key,
			'label'         => $label,
			'name'          => $name,
			'type'          => 'link',
			'return_format' => 'array',
		);
	};

	$cf_textarea = static function ( $key, $label, $name, $rows = 3 ) {
		return array(
			'key'       => 'field_cf_' . $key,
			'label'     => $label,
			'name'      => $name,
			'type'      => 'textarea',
			'rows'      => $rows,
			'new_lines' => '',
		);
	};

	$cf_image = static function ( $key, $label, $name ) {
		return array(
			'key'           => 'field_cf_' . $key,
			'label'         => $label,
			'name'          => $name,
			'type'          => 'image',
			'return_format' => 'id',
			'preview_size'  => 'medium',
		);
	};

	$fields = array();

	/* --- 1. HERO ------------------------------------------------------------ */
	$fields[] = $cf_tab( 'hero', '1 · Hero' );
	$fields[] = $cf_text( 'hero_eyebrow', 'Eyebrow', 'cf_hero_eyebrow' );
	$fields[] = $cf_text( 'hero_titulo', 'Título (linha 1)', 'cf_hero_titulo' );
	$fields[] = $cf_text( 'hero_titulo_destaque', 'Título (linha 2 — destaque azul)', 'cf_hero_titulo_destaque' );
	$fields[] = $cf_textarea( 'hero_texto', 'Texto de apoio', 'cf_hero_texto' );
	$fields[] = $cf_link( 'hero_botao_1', 'Botão primário', 'cf_hero_botao_1' );
	$fields[] = $cf_link( 'hero_botao_2', 'Botão secundário', 'cf_hero_botao_2' );
	$fields[] = $cf_text( 'hero_logos_label', 'Label dos logos (ex: Integrado aos seus sistemas)', 'cf_hero_logos_label' );
	for ( $i = 1; $i <= 6; $i++ ) {
		$fields[] = $cf_image( "hero_logo_{$i}", "Logo sistema {$i}", "cf_hero_logo_{$i}" );
	}

	/* --- 2. MOCKUP ---------------------------------------------------------- */
	$fields[] = $cf_tab( 'mockup', '2 · Mockup' );
	$fields[] = $cf_text( 'mockup_legenda', 'Legenda (ex: Exemplos mais solicitados)', 'cf_mockup_legenda' );
	for ( $i = 1; $i <= 3; $i++ ) {
		$labels = array( 'Portal do Cliente', 'Portal do Fornecedor', 'Cadastro de Produto' );
		$label  = $labels[ $i - 1 ];
		$fields[] = $cf_text( "mockup_{$i}_label", "Mockup {$i} — label ({$label})", "cf_mockup_{$i}_label" );
		$fields[] = $cf_image( "mockup_{$i}_imagem", "Mockup {$i} — imagem ({$label})", "cf_mockup_{$i}_imagem" );
	}

	/* --- 3. LOGOS ----------------------------------------------------------- */
	$fields[] = $cf_tab( 'logos', '3 · Logos' );
	$fields[] = $cf_text( 'logos_titulo', 'Título / legenda acima do marquee', 'cf_logos_titulo' );

	/* --- 4. DORES ----------------------------------------------------------- */
	$fields[] = $cf_tab( 'dores', '4 · Dores' );
	$fields[] = $cf_text( 'dores_titulo', 'Título da seção', 'cf_dores_titulo' );
	for ( $i = 1; $i <= 6; $i++ ) {
		$fields[] = $cf_textarea( "dores_{$i}_citacao", "Card {$i} — citação", "cf_dores_{$i}_citacao", 3 );
		$fields[] = $cf_text( "dores_{$i}_departamento", "Card {$i} — departamento", "cf_dores_{$i}_departamento" );
		$fields[] = $cf_text(
			"dores_{$i}_icone",
			"Card {$i} — ícone",
			"cf_dores_{$i}_icone",
			array( 'instructions' => 'Nome do ícone no Material Symbols (ex.: shopping_cart, payments, handshake, group, inventory_2, receipt_long).' )
		);
	}

	/* --- 6. CASOS DE USO ---------------------------------------------------- */
	$fields[] = $cf_tab( 'casos', '6 · Casos de Uso' );
	$fields[] = $cf_text( 'casos_titulo', 'Título (linha 1)', 'cf_casos_titulo' );
	$fields[] = $cf_text( 'casos_titulo_destaque', 'Título (linha 2 — destaque azul)', 'cf_casos_titulo_destaque' );
	$fields[] = $cf_text( 'casos_janela_marca', 'Janela — texto ao lado do símbolo da CLI (ex.: Flow)', 'cf_casos_janela_marca' );
	$fields[] = $cf_text( 'casos_selo_texto', 'Janela — selo (ex.: Integrado ao SAP)', 'cf_casos_selo_texto' );
	$fields[] = $cf_image( 'casos_selo_logo', 'Janela — logo do selo', 'cf_casos_selo_logo' );
	for ( $i = 1; $i <= 6; $i++ ) {
		$fields[] = $cf_text( "casos_{$i}_departamento", "Aba {$i} — departamento", "cf_casos_{$i}_departamento" );

		// Processos da aba: nome + vídeo (WebM e MP4) + capa. Sem vídeo, o
		// player mostra a janela do CLI Flow com o nome do processo.
		for ( $j = 1; $j <= 6; $j++ ) {
			$fields[] = $cf_text( "casos_{$i}_{$j}_nome", "Aba {$i} · processo {$j} — nome", "cf_casos_{$i}_{$j}_nome" );
			foreach ( array( 'webm' => 'WebM', 'mp4' => 'MP4' ) as $formato => $rotulo ) {
				$fields[] = array(
					'key'           => "field_cf_casos_{$i}_{$j}_{$formato}",
					'label'         => "Aba {$i} · processo {$j} — vídeo {$rotulo}",
					'name'          => "cf_casos_{$i}_{$j}_{$formato}",
					'type'          => 'file',
					'return_format' => 'id',
					'mime_types'    => $formato,
					'wrapper'       => array( 'width' => '33' ),
				);
			}
			$fields[] = array_merge(
				$cf_image( "casos_{$i}_{$j}_capa", "Aba {$i} · processo {$j} — capa do vídeo", "cf_casos_{$i}_{$j}_capa" ),
				array( 'wrapper' => array( 'width' => '33' ) )
			);
		}
	}

	/* --- 7. CTA IA ---------------------------------------------------------- */
	$fields[] = $cf_tab( 'ia', '7 · CTA IA' );
	$fields[] = $cf_text( 'ia_titulo', 'Título (linha 1)', 'cf_ia_titulo' );
	$fields[] = $cf_text( 'ia_titulo_destaque', 'Título (linha 2 — destaque)', 'cf_ia_titulo_destaque' );
	$fields[] = $cf_link( 'ia_botao', 'Botão', 'cf_ia_botao' );

	/* --- 8. CARDS ----------------------------------------------------------- */
	$fields[] = $cf_tab( 'cards', '8 · Cards' );
	$fields[] = $cf_text( 'cards_titulo', 'Título da seção', 'cf_cards_titulo' );
	for ( $i = 1; $i <= 6; $i++ ) {
		$fields[] = $cf_text(
			"cards_{$i}_icone",
			"Card {$i} — ícone",
			"cf_cards_{$i}_icone",
			array( 'instructions' => 'Nome do ícone no Material Symbols (ex.: account_tree, device_hub, captive_portal, check_circle).' )
		);
		$fields[] = $cf_text( "cards_{$i}_titulo", "Card {$i} — título", "cf_cards_{$i}_titulo" );
		$fields[] = $cf_textarea( "cards_{$i}_texto", "Card {$i} — texto", "cf_cards_{$i}_texto", 3 );
	}

	/* --- 9. SAP ------------------------------------------------------------- */
	$fields[] = $cf_tab( 'sap', '9 · SAP' );
	$fields[] = $cf_text( 'sap_eyebrow', 'Eyebrow', 'cf_sap_eyebrow' );
	$fields[] = $cf_text( 'sap_titulo', 'Título (linha 1)', 'cf_sap_titulo' );
	$fields[] = $cf_text( 'sap_titulo_destaque', 'Título (linha 2)', 'cf_sap_titulo_destaque' );
	for ( $i = 1; $i <= 4; $i++ ) {
		$fields[] = $cf_text( "sap_{$i}_titulo", "Passo {$i} — título", "cf_sap_{$i}_titulo" );
		$fields[] = $cf_textarea( "sap_{$i}_texto", "Passo {$i} — texto", "cf_sap_{$i}_texto", 2 );
	}
	$fields[] = $cf_link( 'sap_botao', 'Botão', 'cf_sap_botao' );

	// Cartão à direita: mockup da requisição com a pré-análise do agente.
	$fields[] = $cf_text( 'sap_card_titulo', 'Cartão — título (ex.: Requisição de compra)', 'cf_sap_card_titulo' );
	$fields[] = $cf_text( 'sap_card_subtitulo', 'Cartão — subtítulo (ex.: RC-0148 · R$ 18.450,00)', 'cf_sap_card_subtitulo' );
	$fields[] = $cf_text( 'sap_card_selo', 'Cartão — selo (ex.: Integrado ao SAP)', 'cf_sap_card_selo' );
	$fields[] = $cf_image( 'sap_card_selo_logo', 'Cartão — logo do selo', 'cf_sap_card_selo_logo' );
	$fields[] = $cf_text( 'sap_card_agente', 'Cartão — linha do agente (ex.: Pré-análise do Agente de Compras · 40 s)', 'cf_sap_card_agente' );
	for ( $i = 1; $i <= 5; $i++ ) {
		$fields[] = $cf_text(
			"sap_check_{$i}_tipo",
			"Verificação {$i} — tipo",
			"cf_sap_check_{$i}_tipo",
			array(
				'type'          => 'select',
				'choices'       => array(
					'ok'      => 'OK (verde)',
					'atencao' => 'Atenção (amarelo)',
				),
				'default_value' => 'ok',
			)
		);
		$fields[] = $cf_text( "sap_check_{$i}_selo", "Verificação {$i} — texto do selo (ex.: OK)", "cf_sap_check_{$i}_selo" );
		$fields[] = $cf_text( "sap_check_{$i}_titulo", "Verificação {$i} — título", "cf_sap_check_{$i}_titulo" );
		$fields[] = $cf_text( "sap_check_{$i}_texto", "Verificação {$i} — texto", "cf_sap_check_{$i}_texto" );
	}
	$fields[] = $cf_text( 'sap_card_resumo_titulo', 'Cartão — resumo (título)', 'cf_sap_card_resumo_titulo' );
	$fields[] = $cf_textarea( 'sap_card_resumo_texto', 'Cartão — resumo (texto)', 'cf_sap_card_resumo_texto', 2 );
	for ( $i = 1; $i <= 3; $i++ ) {
		$fields[] = $cf_text( "sap_card_botao_{$i}", "Cartão — botão {$i} (ilustrativo)", "cf_sap_card_botao_{$i}" );
	}

	/* --- 10. INTEGRAÇÕES ---------------------------------------------------- */
	$fields[] = $cf_tab( 'int', '10 · Integrações' );
	$fields[] = $cf_text( 'int_titulo', 'Título (linha 1)', 'cf_int_titulo' );
	$fields[] = $cf_text( 'int_titulo_destaque', 'Título (linhas em azul)', 'cf_int_titulo_destaque' );
	$fields[] = $cf_text( 'int_selo_antes', 'Selo — texto acima do número (ex.: Até)', 'cf_int_selo_antes' );
	$fields[] = $cf_text( 'int_destaque_numero', 'Selo — número (ex.: 80%)', 'cf_int_destaque_numero' );
	$fields[] = $cf_text( 'int_selo_depois', 'Selo — texto abaixo do número (ex.: menos licenças)', 'cf_int_selo_depois' );

	// Janela ilustrativa "Consulta de serviços".
	for ( $i = 1; $i <= 2; $i++ ) {
		$fields[] = $cf_text( "int_sistema_{$i}_nome", "Sistema {$i} — nome (ex.: Protheus)", "cf_int_sistema_{$i}_nome" );
		$fields[] = $cf_image( "int_sistema_{$i}_icone", "Sistema {$i} — ícone", "cf_int_sistema_{$i}_icone" );
	}
	$cf_fonte = array(
		'type'       => 'select',
		'choices'    => array(
			''  => '— sem ícone —',
			'1' => 'Sistema 1',
			'2' => 'Sistema 2',
		),
		'allow_null' => 1,
	);
	$fields[] = $cf_text( 'int_janela_titulo', 'Janela — título (ex.: Consulta de serviços)', 'cf_int_janela_titulo' );
	$fields[] = $cf_text( 'int_janela_fontes', 'Janela — texto antes dos sistemas (ex.: Dados ao vivo de)', 'cf_int_janela_fontes' );
	$fields[] = $cf_text( 'int_busca_label', 'Busca — rótulo', 'cf_int_busca_label' );
	$fields[] = $cf_text( 'int_busca_valor', 'Busca — valor digitado', 'cf_int_busca_valor' );
	for ( $c = 1; $c <= 5; $c++ ) {
		$fields[] = $cf_text( "int_col_{$c}", "Tabela — coluna {$c}", "cf_int_col_{$c}" );
		$fields[] = $cf_text( "int_col_{$c}_fonte", "Tabela — coluna {$c}: ícone do sistema", "cf_int_col_{$c}_fonte", $cf_fonte );
	}
	for ( $i = 1; $i <= 4; $i++ ) {
		$fields[] = $cf_text(
			"int_linha_{$i}",
			"Tabela — linha {$i}",
			"cf_int_linha_{$i}",
			array( 'instructions' => 'Uma célula por coluna, separadas por " | ". A linha 1 aparece selecionada.' )
		);
	}
	$fields[] = $cf_text( 'int_detalhe_sub', 'Detalhe — linha de cima (ex.: SV-101 · R$ 2.400,00 / mês)', 'cf_int_detalhe_sub' );
	$fields[] = $cf_text( 'int_detalhe_titulo', 'Detalhe — título', 'cf_int_detalhe_titulo' );
	for ( $b = 1; $b <= 2; $b++ ) {
		$fields[] = $cf_text( "int_bloco_{$b}_titulo", "Detalhe — bloco {$b}: título", "cf_int_bloco_{$b}_titulo" );
		$fields[] = $cf_text( "int_bloco_{$b}_fonte", "Detalhe — bloco {$b}: ícone do sistema", "cf_int_bloco_{$b}_fonte", $cf_fonte );
		$fields[] = array_merge(
			$cf_textarea( "int_bloco_{$b}_linhas", "Detalhe — bloco {$b}: linhas", "cf_int_bloco_{$b}_linhas", 3 ),
			array( 'instructions' => 'Uma por linha: "rótulo | valor". No bloco 1 o valor vai à direita; no bloco 2, na sequência do nome.' )
		);
	}
	$fields[] = $cf_text( 'int_botao_1', 'Detalhe — botão 1 (ilustrativo)', 'cf_int_botao_1' );
	$fields[] = $cf_text( 'int_botao_2', 'Detalhe — botão 2 (ilustrativo)', 'cf_int_botao_2' );
	$fields[] = $cf_textarea( 'int_legenda', 'Legenda abaixo da janela (aceita <strong>)', 'cf_int_legenda', 2 );

	/* --- 10b. SISTEMAS (grade de logos) ------------------------------------- */
	$fields[] = $cf_tab( 'sistemas', '10b · Sistemas' );
	for ( $i = 1; $i <= 24; $i++ ) {
		$fields[] = $cf_image( "sistemas_logo_{$i}", "Logo {$i}", "cf_sistemas_logo_{$i}" );
	}

	/* --- 11. BENEFÍCIOS ----------------------------------------------------- */
	$fields[] = $cf_tab( 'ben', '11 · Benefícios' );
	for ( $i = 1; $i <= 3; $i++ ) {
		$fields[] = $cf_text( "ben_{$i}_titulo", "Benefício {$i}", "cf_ben_{$i}_titulo" );
	}

	/* --- 12. DASHBOARD ------------------------------------------------------ */
	$fields[] = $cf_tab( 'dash', '12 · Como funciona' );
	$fields[] = $cf_text( 'dash_eyebrow', 'Eyebrow', 'cf_dash_eyebrow' );
	$fields[] = $cf_text( 'dash_titulo', 'Título (linha 1)', 'cf_dash_titulo' );
	$fields[] = $cf_text( 'dash_titulo_destaque', 'Título (linha 2 — destaque)', 'cf_dash_titulo_destaque' );
	$fields[] = $cf_textarea( 'dash_texto', 'Texto de apoio', 'cf_dash_texto', 2 );
	$fields[] = array(
		'key'           => 'field_cf_dash_video_webm',
		'label'         => 'Demonstração — vídeo (.webm)',
		'name'          => 'cf_dash_video_webm',
		'type'          => 'file',
		'return_format' => 'id',
		'mime_types'    => 'webm',
		'instructions'  => 'Fonte principal; o MP4 abaixo é o fallback.',
		'wrapper'       => array( 'width' => '33' ),
	);
	$fields[] = array(
		'key'           => 'field_cf_dash_video',
		'label'         => 'Demonstração — vídeo (.mp4)',
		'name'          => 'cf_dash_video',
		'type'          => 'file',
		'return_format' => 'id',
		'mime_types'    => 'mp4',
		'instructions'  => 'Com vídeo, ele toca em loop no painel (tem prioridade sobre a imagem).',
		'wrapper'       => array( 'width' => '33' ),
	);
	$fields[] = array_merge(
		$cf_image( 'dash_capa', 'Demonstração — capa do vídeo', 'cf_dash_capa' ),
		array( 'wrapper' => array( 'width' => '33' ) )
	);
	$fields[] = $cf_image( 'dash_imagem', 'Demonstração — imagem', 'cf_dash_imagem' );
	$fields[] = $cf_text( 'dash_card_titulo', 'Cartão de tela — título (usado sem vídeo/imagem)', 'cf_dash_card_titulo' );
	for ( $i = 1; $i <= 3; $i++ ) {
		$fields[] = $cf_text( "dash_etapa_{$i}_destaque", "Pílula {$i} — parte em negrito", "cf_dash_etapa_{$i}_destaque" );
		$fields[] = $cf_text( "dash_etapa_{$i}_texto", "Pílula {$i} — complemento", "cf_dash_etapa_{$i}_texto" );
	}

	/* --- 13. ECONOMIA ------------------------------------------------------- */
	$fields[] = $cf_tab( 'eco', '13 · Economia' );
	$fields[] = $cf_text( 'eco_titulo', 'Título (linha 1 — azul)', 'cf_eco_titulo' );
	$fields[] = $cf_text( 'eco_titulo_destaque', 'Título (linha 2)', 'cf_eco_titulo_destaque' );
	$fields[] = $cf_textarea( 'eco_texto', 'Texto de apoio', 'cf_eco_texto', 2 );
	$fields[] = $cf_text( 'eco_card_titulo', 'Cartão — título (ex.: Empresa com 100 usuários)', 'cf_eco_card_titulo' );
	$fields[] = $cf_text( 'eco_sem_label', 'Barra 1 — rótulo (ex.: Sem CLI Flow)', 'cf_eco_sem_label' );
	$fields[] = $cf_text( 'eco_sem_valor', 'Barra 1 — valor (ex.: 100 licenças completas)', 'cf_eco_sem_valor' );
	$fields[] = $cf_text( 'eco_com_label', 'Barra 2 — rótulo (ex.: Com CLI Flow)', 'cf_eco_com_label' );
	$fields[] = $cf_text( 'eco_com_valor', 'Barra 2 — valor (ex.: 20 licenças completas)', 'cf_eco_com_valor' );
	$fields[] = $cf_text(
		'eco_com_percentual',
		'Barra 2 — % preenchido em azul',
		'cf_eco_com_percentual',
		array(
			'type' => 'number',
			'min'  => 0,
			'max'  => 100,
		)
	);
	$fields[] = $cf_text( 'eco_com_barra', 'Barra 2 — texto no restante (ex.: 80 usuários no CLI Flow)', 'cf_eco_com_barra' );

	/* --- 15. PREÇO ---------------------------------------------------------- */
	$fields[] = $cf_tab( 'preco', '15 · Preço' );
	$fields[] = $cf_text( 'preco_titulo', 'Título (linha 1)', 'cf_preco_titulo' );
	$fields[] = $cf_text( 'preco_titulo_destaque', 'Título (linha 2 — destaque)', 'cf_preco_titulo_destaque' );
	$fields[] = $cf_textarea( 'preco_texto', 'Texto de apoio', 'cf_preco_texto', 2 );
	for ( $i = 1; $i <= 3; $i++ ) {
		$fields[] = $cf_text( "preco_{$i}_titulo", "Card {$i} — título", "cf_preco_{$i}_titulo" );
		$fields[] = $cf_textarea( "preco_{$i}_texto", "Card {$i} — texto", "cf_preco_{$i}_texto", 3 );
	}

	/* --- 15b. SELOS --------------------------------------------------------- */
	$fields[] = $cf_tab( 'selos', '15b · Selos' );
	$fields[] = array_merge(
		$cf_text( 'selos_titulo', 'Título', 'cf_selos_titulo' ),
		array( 'instructions' => 'Os selos vêm do cadastro de Selos (CPT), na ordem definida lá.' )
	);

	/* --- 15c. FAQ ---------------------------------------------------------- */
	$fields[] = $cf_tab( 'faq', '15c · FAQ' );
	$fields[] = $cf_text( 'faq_eyebrow', 'Eyebrow', 'cf_faq_eyebrow' );
	$fields[] = $cf_text( 'faq_titulo', 'Título', 'cf_faq_titulo' );
	$fields[] = array(
		'key'           => 'field_cf_faq_itens',
		'label'         => 'Perguntas',
		'name'          => 'cf_faq_itens',
		'type'          => 'relationship',
		'post_type'     => array( 'cli_faq' ),
		'filters'       => array( 'search' ),
		'return_format' => 'object',
		'instructions'  => 'A pergunta é o título do item de FAQ; a resposta, o conteúdo.',
	);

	/* --- 17. CTA FINAL ------------------------------------------------------ */
	$fields[] = $cf_tab( 'cta', '17 · CTA Final' );
	$fields[] = $cf_text( 'cta_eyebrow', 'Eyebrow', 'cf_cta_eyebrow' );
	$fields[] = $cf_text( 'cta_titulo', 'Título (linha 1)', 'cf_cta_titulo' );
	$fields[] = $cf_text( 'cta_titulo_destaque', 'Título (linha 2)', 'cf_cta_titulo_destaque' );
	$fields[] = $cf_textarea( 'cta_texto', 'Texto de apoio', 'cf_cta_texto', 3 );
	$fields[] = $cf_link( 'cta_botao', 'Botão', 'cf_cta_botao' );

	acf_add_local_field_group(
		array(
			'key'            => 'group_cli_cli_flow',
			'title'          => 'CLI Flow — Conteúdo',
			'fields'         => $fields,
			'location'       => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'page-cli-flow.php',
					),
				),
			),
			'hide_on_screen' => array(
				'the_content',
				'excerpt',
				'discussion',
				'comments',
				'revisions',
				'slug',
				'author',
				'format',
				'page_attributes',
				'featured_image',
				'send_trackbacks',
			),
		)
	);
}
add_action( 'acf/init', 'cliconnect_acf_fields_cli_flow' );
