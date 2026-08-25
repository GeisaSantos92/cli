<?php
/**
 * Grupo ACF da página inicial.
 *
 * Todo o texto da home é editável aqui — nada de conteúdo fixo no template.
 * As listas que crescem (agentes, integrações, cases, FAQ...) vêm dos CPTs de
 * inc/cpt.php; aqui ficam só os textos das seções e os conjuntos de tamanho
 * fixo (métricas, camadas, departamentos, canais), em campos numerados —
 * o ACF Free não tem Repeater.
 *
 * Local: página definida como "Página inicial" em Configurações → Leitura.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper: campo de texto simples.
 *
 * @param string $key   Sufixo da chave (field_home_{key}).
 * @param string $label Rótulo.
 * @param string $name  Nome do campo.
 * @param array  $extra Sobrescritas.
 * @return array
 */
function cliconnect_acf_text( $key, $label, $name, $extra = array() ) {
	return array_merge(
		array(
			'key'   => 'field_home_' . $key,
			'label' => $label,
			'name'  => $name,
			'type'  => 'text',
		),
		$extra
	);
}

/**
 * Helper: aba do grupo ACF.
 *
 * @param string $key   Sufixo da chave.
 * @param string $label Rótulo da aba.
 * @return array
 */
function cliconnect_acf_tab( $key, $label ) {
	return array(
		'key'       => 'field_home_tab_' . $key,
		'label'     => $label,
		'name'      => '',
		'type'      => 'tab',
		'placement' => 'left',
	);
}

/**
 * Helper: campo de link (retorna array com url/title/target).
 *
 * @param string $key   Sufixo da chave.
 * @param string $label Rótulo.
 * @param string $name  Nome do campo.
 * @return array
 */
function cliconnect_acf_link( $key, $label, $name ) {
	return array(
		'key'           => 'field_home_' . $key,
		'label'         => $label,
		'name'          => $name,
		'type'          => 'link',
		'return_format' => 'array',
	);
}

/**
 * Registra o grupo de campos da home.
 *
 * @return void
 */
function cliconnect_acf_fields_home() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$fields = array();

	/* --- 1. HERO --------------------------------------------------------- */
	$fields[] = cliconnect_acf_tab( 'hero', '1 · Hero' );
	$fields[] = cliconnect_acf_text( 'hero_eyebrow', 'Selo acima do título', 'hero_eyebrow' );
	$fields[] = cliconnect_acf_text(
		'hero_titulo_destaque',
		'Título — 1ª linha (azul)',
		'hero_titulo_destaque',
		array( 'instructions' => 'Renderizada em azul, acima da segunda linha.' )
	);
	$fields[] = cliconnect_acf_text( 'hero_titulo', 'Título — 2ª linha', 'hero_titulo' );
	$fields[] = array(
		'key'       => 'field_home_hero_subtitulo',
		'label'     => 'Subtítulo',
		'name'      => 'hero_subtitulo',
		'type'      => 'textarea',
		'rows'      => 2,
		'new_lines' => '',
	);
	$fields[] = cliconnect_acf_link( 'hero_botao', 'Botão principal', 'hero_botao' );

	/* --- 2. AGENTES ------------------------------------------------------ */
	$fields[] = cliconnect_acf_tab( 'agentes', '2 · Agentes' );
	$fields[] = array(
		'key'     => 'field_home_agentes_msg',
		'label'   => '',
		'name'    => '',
		'type'    => 'message',
		'message' => 'Os cards vêm do CPT <strong>Agentes de IA</strong>. A ordem segue o campo <em>Ordem</em> de cada agente.',
	);
	$fields[] = cliconnect_acf_text( 'agentes_legenda', 'Legenda abaixo da esteira', 'agentes_legenda' );

	/* --- 3. CAMADAS ------------------------------------------------------ */
	$fields[] = cliconnect_acf_tab( 'camadas', '3 · Camadas' );
	$fields[] = array(
		'key'     => 'field_home_camadas_msg',
		'label'   => '',
		'name'    => '',
		'type'    => 'message',
		'message' => 'A ilustração das camadas é um asset do tema (assets/img/section-camadas.png) — rótulos e descrições fazem parte da arte.',
	);
	$fields[] = cliconnect_acf_text( 'camadas_titulo', 'Título da seção', 'camadas_titulo' );
	$fields[] = array(
		'key'       => 'field_home_camadas_texto',
		'label'     => 'Texto de apoio',
		'name'      => 'camadas_texto',
		'type'      => 'textarea',
		'rows'      => 2,
		'new_lines' => '',
	);
	$fields[] = cliconnect_acf_link( 'camadas_botao', 'Botão', 'camadas_botao' );

	/* --- 4. BOOMI -------------------------------------------------------- */
	$fields[] = cliconnect_acf_tab( 'boomi', '4 · Plataforma Boomi' );
	$fields[] = cliconnect_acf_text( 'boomi_eyebrow', 'Selo', 'boomi_eyebrow' );
	$fields[] = cliconnect_acf_text( 'boomi_titulo', 'Título', 'boomi_titulo' );
	$fields[] = array(
		'key'          => 'field_home_boomi_texto',
		'label'        => 'Texto',
		'name'         => 'boomi_texto',
		'type'         => 'wysiwyg',
		'tabs'         => 'visual',
		'toolbar'      => 'basic',
		'media_upload' => 0,
		'instructions' => 'Use negrito para destacar trechos em azul.',
	);
	$fields[] = array(
		'key'     => 'field_home_boomi_msg',
		'label'   => '',
		'name'    => '',
		'type'    => 'message',
		'message' => 'O cartão da direita é um asset do tema (assets/img/section-plataforma-boomi.png) — selo, logo e nota fazem parte da arte.',
	);

	/* --- 5. MÉTRICAS ----------------------------------------------------- */
	$fields[] = cliconnect_acf_tab( 'metricas', '5 · Métricas' );
	for ( $i = 1; $i <= 3; $i++ ) {
		$fields[] = cliconnect_acf_text( "metrica_{$i}_numero", "Métrica {$i} — número", "metrica_{$i}_numero" );
		$fields[] = cliconnect_acf_text( "metrica_{$i}_rotulo", "Métrica {$i} — descrição", "metrica_{$i}_rotulo" );
	}

	/* --- 6. BLOCOS MÍDIA + TEXTO ----------------------------------------- */
	for ( $b = 1; $b <= 2; $b++ ) {
		$fields[] = cliconnect_acf_tab( "midia_{$b}", ( 5 + $b ) . " · Bloco de mídia {$b}" );
		$fields[] = cliconnect_acf_text( "midia_{$b}_eyebrow", 'Selo', "midia_{$b}_eyebrow" );
		$fields[] = cliconnect_acf_text( "midia_{$b}_titulo", 'Título', "midia_{$b}_titulo" );
		$fields[] = array(
			'key'       => "field_home_midia_{$b}_texto",
			'label'     => 'Texto',
			'name'      => "midia_{$b}_texto",
			'type'      => 'textarea',
			'rows'      => 3,
			'new_lines' => '',
		);
		for ( $t = 1; $t <= 3; $t++ ) {
			$fields[] = cliconnect_acf_text( "midia_{$b}_topico_{$t}", "Tópico {$t}", "midia_{$b}_topico_{$t}" );
		}
		$fields[] = array(
			'key'           => "field_home_midia_{$b}_imagem",
			'label'         => 'Imagem',
			'name'          => "midia_{$b}_imagem",
			'type'          => 'image',
			'return_format' => 'id',
			'preview_size'  => 'medium',
		);
	}

	/* --- 8. DEPOIMENTO / CASES ------------------------------------------- */
	$fields[] = cliconnect_acf_tab( 'cases', '8 · Depoimento & Cases' );
	$fields[] = array(
		'key'           => 'field_home_case_destaque',
		'label'         => 'Case em destaque (card azul)',
		'name'          => 'case_destaque',
		'type'          => 'post_object',
		'post_type'     => array( 'cli_case' ),
		'return_format' => 'id',
		'ui'            => 1,
	);
	$fields[] = array(
		'key'           => 'field_home_cases_metricas',
		'label'         => 'Cases com métrica (2 cards brancos)',
		'name'          => 'cases_metricas',
		'type'          => 'relationship',
		'post_type'     => array( 'cli_case' ),
		'filters'       => array( 'search' ),
		'max'           => 2,
		'return_format' => 'id',
	);
	$fields[] = cliconnect_acf_link( 'cases_botao', 'Botão', 'cases_botao' );

	/* --- 9. EVENTOS ------------------------------------------------------ */
	$fields[] = cliconnect_acf_tab( 'eventos', '9 · Eventos automáticos' );
	$fields[] = array(
		'key'     => 'field_home_eventos_msg',
		'label'   => '',
		'name'    => '',
		'type'    => 'message',
		'message' => 'Os cards vêm do CPT <strong>Eventos Automáticos</strong>.',
	);
	$fields[] = cliconnect_acf_text( 'eventos_eyebrow', 'Selo', 'eventos_eyebrow' );
	$fields[] = cliconnect_acf_text( 'eventos_titulo', 'Título', 'eventos_titulo' );

	/* --- 10. COMPLIANCE -------------------------------------------------- */
	$fields[] = cliconnect_acf_tab( 'compliance', '10 · Compliance' );
	$fields[] = array(
		'key'     => 'field_home_compliance_msg',
		'label'   => '',
		'name'    => '',
		'type'    => 'message',
		'message' => 'Os selos vêm do CPT <strong>Selos &amp; Certificações</strong>.',
	);
	$fields[] = cliconnect_acf_text( 'compliance_eyebrow', 'Selo', 'compliance_eyebrow' );
	$fields[] = cliconnect_acf_text( 'compliance_titulo', 'Título', 'compliance_titulo' );
	$fields[] = array(
		'key'       => 'field_home_compliance_texto',
		'label'     => 'Texto',
		'name'      => 'compliance_texto',
		'type'      => 'textarea',
		'rows'      => 2,
		'new_lines' => '',
	);

	/* --- 11. INTEGRAÇÕES PRONTAS ----------------------------------------- */
	$fields[] = cliconnect_acf_tab( 'integracoes', '11 · Integrações prontas' );
	$fields[] = cliconnect_acf_text( 'integracoes_eyebrow', 'Selo', 'integracoes_eyebrow' );
	$fields[] = cliconnect_acf_text( 'integracoes_titulo', 'Título', 'integracoes_titulo' );
	$fields[] = array(
		'key'       => 'field_home_integracoes_texto',
		'label'     => 'Texto',
		'name'      => 'integracoes_texto',
		'type'      => 'textarea',
		'rows'      => 3,
		'new_lines' => '',
	);
	$fields[] = cliconnect_acf_link( 'integracoes_botao', 'Botão', 'integracoes_botao' );

	/* --- 12. DEPARTAMENTOS ----------------------------------------------- */
	$fields[] = cliconnect_acf_tab( 'departamentos', '12 · Departamentos' );
	for ( $i = 1; $i <= 6; $i++ ) {
		$fields[] = cliconnect_acf_text( "departamento_{$i}", "Departamento {$i}", "departamento_{$i}" );
	}
	$fields[] = cliconnect_acf_text( 'departamentos_titulo', 'Título', 'departamentos_titulo' );
	$fields[] = array(
		'key'       => 'field_home_departamentos_texto',
		'label'     => 'Texto',
		'name'      => 'departamentos_texto',
		'type'      => 'textarea',
		'rows'      => 3,
		'new_lines' => '',
	);
	$fields[] = cliconnect_acf_link( 'departamentos_botao', 'Botão', 'departamentos_botao' );
	$fields[] = array(
		'key'       => 'field_home_prova_texto',
		'label'     => 'Texto da prova social',
		'name'      => 'prova_texto',
		'type'      => 'textarea',
		'rows'      => 2,
		'new_lines' => '',
		'instructions' => 'Ex.: +500 empresas já decidiram automatizar seus processos. Os logos vêm dos Clientes marcados como "prova social".',
	);

	/* --- 13. FRASE ------------------------------------------------------- */
	$fields[] = cliconnect_acf_tab( 'frase', '13 · Frase de impacto' );
	$fields[] = cliconnect_acf_text( 'frase_texto',    'Frase A',    'frase_texto' );
	$fields[] = cliconnect_acf_text( 'frase_texto_b',  'Frase B (prefixo)',  'frase_texto_b' );
	$fields[] = cliconnect_acf_text( 'frase_destaque', 'Frase B (destaque em azul)', 'frase_destaque' );

	/* --- 14. SUPORTE ----------------------------------------------------- */
	$fields[] = cliconnect_acf_tab( 'suporte', '14 · Suporte' );
	$fields[] = cliconnect_acf_text( 'suporte_eyebrow', 'Selo', 'suporte_eyebrow' );
	$fields[] = cliconnect_acf_text( 'suporte_titulo', 'Título', 'suporte_titulo' );
	$fields[] = array(
		'key'          => 'field_home_suporte_texto',
		'label'        => 'Texto',
		'name'         => 'suporte_texto',
		'type'         => 'wysiwyg',
		'tabs'         => 'visual',
		'toolbar'      => 'basic',
		'media_upload' => 0,
	);
	$fields[] = cliconnect_acf_link( 'suporte_botao', 'Botão', 'suporte_botao' );
	$fields[] = array(
		'key'     => 'field_home_suporte_msg',
		'label'   => '',
		'name'    => '',
		'type'    => 'message',
		'message' => 'O anel de canais é um asset do tema (assets/img/section-atendimento.png) — retrato, ícones e rótulos fazem parte da arte.',
	);

	/* --- 15. BLOG -------------------------------------------------------- */
	$fields[] = cliconnect_acf_tab( 'blog', '15 · Blog' );
	$fields[] = cliconnect_acf_text( 'blog_titulo', 'Título', 'blog_titulo' );
	$fields[] = cliconnect_acf_link( 'blog_link', 'Link "ver todas"', 'blog_link' );

	/* --- 16. FAQ --------------------------------------------------------- */
	$fields[] = cliconnect_acf_tab( 'faq', '16 · FAQ' );
	$fields[] = array(
		'key'     => 'field_home_faq_msg',
		'label'   => '',
		'name'    => '',
		'type'    => 'message',
		'message' => 'As perguntas vêm do CPT <strong>Perguntas Frequentes</strong>.',
	);
	$fields[] = cliconnect_acf_text( 'faq_eyebrow', 'Selo', 'faq_eyebrow' );
	$fields[] = cliconnect_acf_text( 'faq_titulo', 'Título', 'faq_titulo' );
	$fields[] = array(
		'key'           => 'field_home_faq_itens',
		'label'         => 'Perguntas & Respostas',
		'name'          => 'faq_itens',
		'type'          => 'relationship',
		'post_type'     => array( 'cli_faq' ),
		'filters'       => array( 'search' ),
		'return_format' => 'object',
		'min'           => 0,
		'max'           => 10,
		'instructions'  => 'Selecione os FAQs que devem aparecer na home (máx. 10).',
	);

	acf_add_local_field_group(
		array(
			'key'            => 'group_cli_home',
			'title'          => 'Conteúdo da Página Inicial',
			'location'       => array(
				array(
					array(
						'param'    => 'page_type',
						'operator' => '==',
						'value'    => 'front_page',
					),
				),
			),
			'menu_order'     => 0,
			'position'       => 'normal',
			'style'          => 'default',
			'label_placement' => 'top',
			'hide_on_screen' => array( 'the_content', 'excerpt', 'custom_fields', 'discussion', 'comments' ),
			'fields'         => $fields,
		)
	);
}
add_action( 'acf/init', 'cliconnect_acf_fields_home' );
