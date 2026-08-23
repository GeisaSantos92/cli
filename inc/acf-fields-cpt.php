<?php
/**
 * Grupos ACF (locais, via código) dos CPTs do CLI Connect.
 *
 * Nada é cadastrado pelo painel do ACF: os grupos vivem aqui e viajam com o
 * tema no git. Só campos disponíveis no ACF Free (sem Repeater/Group/Gallery).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ícones disponíveis para os cards de agente (chave => rótulo).
 *
 * Os SVGs correspondentes ficam em cliconnect_icone() (inc/template-tags.php).
 *
 * @return array<string,string>
 */
function cliconnect_icones_agente() {
	return array(
		'vendas'     => 'Vendas / carrinho',
		'fiscal'     => 'Fiscal / documento',
		'logistica'  => 'Logística / entrega',
		'credito'    => 'Crédito / banco',
		'suporte'    => 'Suporte / atendimento',
		'estoque'    => 'Estoque / caixa',
		'agenda'     => 'Agenda / calendário',
		'automacao'  => 'Automação / raio',
	);
}

/**
 * Registra os grupos de campos dos CPTs.
 *
 * @return void
 */
function cliconnect_acf_fields_cpt() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	/* =====================================================================
	   AGENTE DE IA
	   ===================================================================== */
	acf_add_local_field_group(
		array(
			'key'                   => 'group_cli_agente',
			'title'                 => 'Dados do Agente',
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'cli_agente',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'hide_on_screen'        => array( 'the_content', 'excerpt', 'custom_fields', 'discussion', 'comments' ),
			'fields'                => array(
				array(
					'key'          => 'field_agente_icone',
					'label'        => 'Ícone',
					'name'         => 'icone',
					'type'         => 'select',
					'instructions' => 'Ícone exibido no topo do card.',
					'choices'      => cliconnect_icones_agente(),
					'default_value' => 'automacao',
					'return_format' => 'value',
					'ui'           => 1,
				),
				array(
					'key'          => 'field_agente_descricao',
					'label'        => 'Descrição',
					'name'         => 'descricao',
					'type'         => 'textarea',
					'instructions' => 'Uma ou duas frases sobre o que o agente faz.',
					'rows'         => 3,
					'new_lines'    => '',
					'required'     => 1,
				),
				array(
					'key'          => 'field_agente_status',
					'label'        => 'Rótulo de status',
					'name'         => 'status',
					'type'         => 'text',
					'default_value' => 'Ativo',
					'instructions' => 'Texto do selo verde no canto do card.',
				),
				array(
					'key'           => 'field_agente_integracoes',
					'label'         => 'Integrações usadas',
					'name'          => 'integracoes',
					'type'          => 'relationship',
					'instructions'  => 'Logos exibidos no rodapé do card (recomendado: 3).',
					'post_type'     => array( 'cli_integracao' ),
					'filters'       => array( 'search' ),
					'max'           => 4,
					'return_format' => 'id',
				),
			),
		)
	);

	/* =====================================================================
	   INTEGRAÇÃO
	   ===================================================================== */
	acf_add_local_field_group(
		array(
			'key'            => 'group_cli_integracao',
			'title'          => 'Dados da Integração',
			'location'       => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'cli_integracao',
					),
				),
			),
			'menu_order'     => 0,
			'position'       => 'normal',
			'hide_on_screen' => array( 'the_content', 'excerpt', 'custom_fields', 'discussion', 'comments' ),
			'fields'         => array(
				array(
					'key'          => 'field_integracao_msg',
					'label'        => '',
					'name'         => '',
					'type'         => 'message',
					'message'      => 'O <strong>logo</strong> do sistema é a <em>Imagem destacada</em> (coluna lateral). Use PNG/SVG com fundo transparente.',
				),
				array(
					'key'           => 'field_integracao_url',
					'label'         => 'Link (opcional)',
					'name'          => 'url',
					'type'          => 'url',
					'instructions'  => 'Página interna que detalha essa integração, se houver.',
				),
				array(
					'key'           => 'field_integracao_destaque_hero',
					'label'         => 'Exibir na órbita do Hero',
					'name'          => 'destaque_hero',
					'type'          => 'true_false',
					'instructions'  => 'Marque para o logo aparecer flutuando ao redor do título da home.',
					'ui'            => 1,
					'default_value' => 0,
				),
			),
		)
	);

	/* =====================================================================
	   CLIENTE / PARCEIRO
	   ===================================================================== */
	acf_add_local_field_group(
		array(
			'key'            => 'group_cli_cliente',
			'title'          => 'Dados do Cliente',
			'location'       => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'cli_cliente',
					),
				),
			),
			'menu_order'     => 0,
			'position'       => 'normal',
			'hide_on_screen' => array( 'the_content', 'excerpt', 'custom_fields', 'discussion', 'comments' ),
			'fields'         => array(
				array(
					'key'     => 'field_cliente_msg',
					'label'   => '',
					'name'    => '',
					'type'    => 'message',
					'message' => 'O <strong>logo</strong> do cliente é a <em>Imagem destacada</em>. Use PNG/SVG com fundo transparente.',
				),
				array(
					'key'           => 'field_cliente_prova_social',
					'label'         => 'Usar na prova social',
					'name'          => 'prova_social',
					'type'          => 'true_false',
					'instructions'  => 'Exibe o logo no bloco "+500 empresas já decidiram automatizar seus processos".',
					'ui'            => 1,
					'default_value' => 0,
				),
			),
		)
	);

	/* =====================================================================
	   CASE DE SUCESSO
	   ===================================================================== */
	acf_add_local_field_group(
		array(
			'key'            => 'group_cli_case',
			'title'          => 'Dados do Case',
			'location'       => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'cli_case',
					),
				),
			),
			'menu_order'     => 0,
			'position'       => 'normal',
			'hide_on_screen' => array( 'custom_fields', 'discussion', 'comments' ),
			'fields'         => array(
				array(
					'key'   => 'field_case_tab_depoimento',
					'label' => 'Depoimento',
					'name'  => '',
					'type'  => 'tab',
				),
				array(
					'key'          => 'field_case_logo',
					'label'        => 'Logo do cliente',
					'name'         => 'logo',
					'type'         => 'image',
					'return_format' => 'id',
					'preview_size' => 'medium',
					'instructions' => 'Logo exibido sobre o card azul e no card de métrica.',
				),
				array(
					'key'          => 'field_case_citacao',
					'label'        => 'Citação',
					'name'         => 'citacao',
					'type'         => 'textarea',
					'rows'         => 3,
					'new_lines'    => '',
					'instructions' => 'Frase do depoimento, sem aspas (o tema adiciona).',
				),
				array(
					'key'   => 'field_case_autor',
					'label' => 'Nome de quem depõe',
					'name'  => 'autor',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_case_cargo',
					'label' => 'Cargo',
					'name'  => 'cargo',
					'type'  => 'text',
				),
				array(
					'key'           => 'field_case_retrato',
					'label'         => 'Foto / thumbnail do vídeo',
					'name'          => 'retrato',
					'type'          => 'image',
					'return_format' => 'id',
					'preview_size'  => 'medium',
				),
				array(
					'key'          => 'field_case_video',
					'label'        => 'URL do vídeo',
					'name'         => 'video',
					'type'         => 'url',
					'instructions' => 'YouTube ou Vimeo. Se preenchido, exibe o botão de play.',
				),
				array(
					'key'   => 'field_case_tab_metrica',
					'label' => 'Métrica',
					'name'  => '',
					'type'  => 'tab',
				),
				array(
					'key'          => 'field_case_metrica_numero',
					'label'        => 'Número da métrica 1',
					'name'         => 'metrica_numero',
					'type'         => 'text',
					'instructions' => 'Ex.: 10%, +200%, 4x.',
				),
				array(
					'key'          => 'field_case_metrica_texto',
					'label'        => 'Texto da métrica 1',
					'name'         => 'metrica_texto',
					'type'         => 'text',
					'instructions' => 'Ex.: De tempo economizado na produção de insights.',
				),
				array(
					'key'          => 'field_case_metrica_numero_2',
					'label'        => 'Número da métrica 2',
					'name'         => 'metrica_numero_2',
					'type'         => 'text',
					'instructions' => 'Segunda métrica (opcional). Ex.: +60%, 3x.',
				),
				array(
					'key'          => 'field_case_metrica_texto_2',
					'label'        => 'Texto da métrica 2',
					'name'         => 'metrica_texto_2',
					'type'         => 'text',
					'instructions' => 'Descrição da segunda métrica.',
				),
				array(
					'key'   => 'field_case_tab_conteudo',
					'label' => 'Conteúdo',
					'name'  => '',
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_case_integracoes',
					'label'         => 'Tecnologias utilizadas',
					'name'          => 'integracoes',
					'type'          => 'relationship',
					'instructions'  => 'Logos exibidos na barra lateral da página do case.',
					'post_type'     => array( 'cli_integracao' ),
					'filters'       => array( 'search' ),
					'return_format' => 'id',
				),
				array(
					'key'          => 'field_case_desafio_titulo',
					'label'        => 'Título — Desafio',
					'name'         => 'desafio_titulo',
					'type'         => 'text',
				),
				array(
					'key'          => 'field_case_desafio_texto',
					'label'        => 'Texto — Desafio',
					'name'         => 'desafio_texto',
					'type'         => 'wysiwyg',
					'tabs'         => 'all',
					'toolbar'      => 'full',
					'media_upload' => 0,
				),
				array(
					'key'          => 'field_case_solucao_titulo',
					'label'        => 'Título — Solução',
					'name'         => 'solucao_titulo',
					'type'         => 'text',
				),
				array(
					'key'          => 'field_case_solucao_texto',
					'label'        => 'Texto — Solução',
					'name'         => 'solucao_texto',
					'type'         => 'wysiwyg',
					'tabs'         => 'all',
					'toolbar'      => 'full',
					'media_upload' => 0,
				),
				array(
					'key'          => 'field_case_impacto_titulo',
					'label'        => 'Título — Impacto',
					'name'         => 'impacto_titulo',
					'type'         => 'text',
				),
				array(
					'key'          => 'field_case_impacto_texto',
					'label'        => 'Texto — Impacto',
					'name'         => 'impacto_texto',
					'type'         => 'wysiwyg',
					'tabs'         => 'all',
					'toolbar'      => 'full',
					'media_upload' => 0,
				),
				array(
					'key'   => 'field_case_tab_opcoes',
					'label' => 'Opções',
					'name'  => '',
					'type'  => 'tab',
				),
				array(
					'key'          => 'field_case_em_andamento',
					'label'        => 'Case em andamento',
					'name'         => 'em_andamento',
					'type'         => 'true_false',
					'instructions' => 'Marque quando o case ainda não tem conteúdo completo. Aparece na listagem apenas com o logo e o rótulo "Case em andamento".',
					'default_value' => 0,
					'ui'           => 1,
				),
			),
		)
	);

	/* =====================================================================
	   EVENTO AUTOMÁTICO
	   ===================================================================== */
	acf_add_local_field_group(
		array(
			'key'            => 'group_cli_evento',
			'title'          => 'Dados do Evento',
			'location'       => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'cli_evento',
					),
				),
			),
			'menu_order'     => 0,
			'position'       => 'normal',
			'hide_on_screen' => array( 'the_content', 'excerpt', 'custom_fields', 'discussion', 'comments' ),
			'fields'         => array(
				array(
					'key'     => 'field_evento_msg',
					'label'   => '',
					'name'    => '',
					'type'    => 'message',
					'message' => 'A <strong>ilustração</strong> do card é a <em>Imagem destacada</em> (proporção 16:10).',
				),
				array(
					'key'      => 'field_evento_descricao',
					'label'    => 'Descrição',
					'name'     => 'descricao',
					'type'     => 'textarea',
					'rows'     => 3,
					'new_lines' => '',
					'required' => 1,
				),
			),
		)
	);

	/* =====================================================================
	   FAQ
	   ===================================================================== */
	acf_add_local_field_group(
		array(
			'key'            => 'group_cli_faq',
			'title'          => 'Resposta',
			'location'       => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'cli_faq',
					),
				),
			),
			'menu_order'     => 0,
			'position'       => 'normal',
			'hide_on_screen' => array( 'custom_fields', 'discussion', 'comments', 'excerpt' ),
			'fields'         => array(
				array(
					'key'     => 'field_faq_msg',
					'label'   => '',
					'name'    => '',
					'type'    => 'message',
					'message' => 'O <strong>título</strong> do post é a pergunta. A resposta vai no editor abaixo.',
				),
			),
		)
	);

	/* =====================================================================
	   SELO / CERTIFICAÇÃO
	   ===================================================================== */
	acf_add_local_field_group(
		array(
			'key'            => 'group_cli_selo',
			'title'          => 'Dados do Selo',
			'location'       => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'cli_selo',
					),
				),
			),
			'menu_order'     => 0,
			'position'       => 'normal',
			'hide_on_screen' => array( 'the_content', 'excerpt', 'custom_fields', 'discussion', 'comments' ),
			'fields'         => array(
				array(
					'key'     => 'field_selo_msg',
					'label'   => '',
					'name'    => '',
					'type'    => 'message',
					'message' => 'A <strong>imagem do selo</strong> é a <em>Imagem destacada</em>. O título é usado como texto alternativo.',
				),
			),
		)
	);
	/* =====================================================================
	   SOLUÇÃO
	   O CPT cli_solucao é ao mesmo tempo o item do catálogo (listagens) e a
	   landing page da solução (single-cli_solucao.php). Campos divididos em abas:
	   - Catálogo: logo (imagem destacada) + descrição curta (card da listagem)
	   - Seções da landing: hero, e demais seções a serem adicionadas por Figma.
	   ===================================================================== */
	acf_add_local_field_group(
		array(
			'key'            => 'group_cli_solucao',
			'title'          => 'Solução',
			'location'       => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'cli_solucao',
					),
				),
			),
			'menu_order'     => 0,
			'position'       => 'normal',
			'hide_on_screen' => array( 'the_content', 'custom_fields', 'discussion', 'comments' ),
			'fields'         => array(

				/* --- Aba: Catálogo ----------------------------------------- */
				array(
					'key'       => 'field_solucao_tab_catalogo',
					'label'     => 'Catálogo',
					'name'      => '',
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'     => 'field_solucao_msg',
					'label'   => '',
					'name'    => '',
					'type'    => 'message',
					'message' => 'O <strong>logo</strong> da solução é a <em>Imagem destacada</em>. Use PNG/SVG com fundo transparente.',
				),
				array(
					'key'          => 'field_solucao_descricao',
					'label'        => 'Descrição curta',
					'name'         => 'solucao_descricao',
					'type'         => 'textarea',
					'rows'         => 2,
					'new_lines'    => '',
					'instructions' => 'Uma linha exibida no card da listagem.',
				),

				/* --- Aba: Hero --------------------------------------------- */
				array(
					'key'       => 'field_solucao_tab_hero',
					'label'     => '1 · Hero',
					'name'      => '',
					'type'      => 'tab',
					'placement' => 'left',
				),
				array(
					'key'          => 'field_solucao_hero_eyebrow',
					'label'        => 'Eyebrow',
					'name'         => 'solucao_hero_eyebrow',
					'type'         => 'text',
					'instructions' => 'Ex.: "Para o seu Salesforce".',
				),
				array(
					'key'   => 'field_solucao_hero_titulo',
					'label' => 'Título — linha 1 (escuro)',
					'name'  => 'solucao_hero_titulo',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_solucao_hero_titulo_destaque',
					'label' => 'Título — linha 2 (azul)',
					'name'  => 'solucao_hero_titulo_destaque',
					'type'  => 'text',
				),
				array(
					'key'          => 'field_solucao_hero_titulo_fim',
					'label'        => 'Título — continuação (escuro)',
					'name'         => 'solucao_hero_titulo_fim',
					'type'         => 'text',
					'instructions' => 'Opcional. Preenchido, o título passa a correr como uma frase só e o trecho azul fica no meio dela; vazio, as duas primeiras partes seguem em linhas separadas.',
				),
				array(
					'key'           => 'field_solucao_hero_titulo_fluido',
					'label'         => 'Título em frase corrida',
					'name'          => 'solucao_hero_titulo_fluido',
					'type'          => 'true_false',
					'instructions'  => 'Marque quando o trecho azul encerra a frase mas deve continuar na mesma linha do texto escuro. Com a continuação preenchida o modo já é automático.',
					'ui'            => 1,
					'default_value' => 0,
				),
				array(
					'key'    => 'field_solucao_hero_corpo',
					'label'  => 'Corpo',
					'name'   => 'solucao_hero_corpo',
					'type'   => 'textarea',
					'rows'   => 3,
					'new_lines' => '',
				),
				array(
					'key'   => 'field_solucao_hero_btn1_texto',
					'label' => 'Botão primário — Texto',
					'name'  => 'solucao_hero_btn1_texto',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_solucao_hero_btn1_url',
					'label' => 'Botão primário — URL',
					'name'  => 'solucao_hero_btn1_url',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_solucao_hero_btn2_texto',
					'label' => 'Botão secundário — Texto',
					'name'  => 'solucao_hero_btn2_texto',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_solucao_hero_btn2_url',
					'label' => 'Botão secundário — URL',
					'name'  => 'solucao_hero_btn2_url',
					'type'  => 'text',
				),
				array(
					'key'           => 'field_solucao_hero_imagem',
					'label'         => 'Imagem (coluna direita)',
					'name'          => 'solucao_hero_imagem',
					'type'          => 'image',
					'return_format' => 'id',
					'preview_size'  => 'medium',
					'instructions'  => 'Visual da solução — logos conectados, diagrama, etc. Proporção quadrada.',
				),

				// ----- Aba: 2 · Métricas ---------------------------------------
				array(
					'key'   => 'field_solucao_metricas_tab',
					'label' => '2 · Métricas',
					'name'  => '',
					'type'  => 'tab',
				),
				array(
					'key'     => 'field_solucao_metricas_msg',
					'label'   => '',
					'name'    => '',
					'type'    => 'message',
					'message' => 'Faixa de números logo abaixo do hero. Deixe o número em branco para esconder a métrica; sem nenhum número, a faixa inteira some.',
				),
				array(
					'key'          => 'field_solucao_metrica_1_numero',
					'label'        => 'Métrica 1 — Número',
					'name'         => 'solucao_metrica_1_numero',
					'type'         => 'text',
					'instructions' => 'Ex.: "95%", "24.000".',
				),
				array(
					'key'          => 'field_solucao_metrica_1_rotulo',
					'label'        => 'Métrica 1 — Rótulo',
					'name'         => 'solucao_metrica_1_rotulo',
					'type'         => 'text',
					'instructions' => 'Frase curta ao lado do número.',
				),
				array(
					'key'          => 'field_solucao_metrica_2_numero',
					'label'        => 'Métrica 2 — Número',
					'name'         => 'solucao_metrica_2_numero',
					'type'         => 'text',
					'instructions' => 'Ex.: "95%", "24.000".',
				),
				array(
					'key'          => 'field_solucao_metrica_2_rotulo',
					'label'        => 'Métrica 2 — Rótulo',
					'name'         => 'solucao_metrica_2_rotulo',
					'type'         => 'text',
					'instructions' => 'Frase curta ao lado do número.',
				),
				array(
					'key'          => 'field_solucao_metrica_3_numero',
					'label'        => 'Métrica 3 — Número',
					'name'         => 'solucao_metrica_3_numero',
					'type'         => 'text',
					'instructions' => 'Ex.: "95%", "24.000".',
				),
				array(
					'key'          => 'field_solucao_metrica_3_rotulo',
					'label'        => 'Métrica 3 — Rótulo',
					'name'         => 'solucao_metrica_3_rotulo',
					'type'         => 'text',
					'instructions' => 'Frase curta ao lado do número.',
				),

				// ----- Aba: 3 · Pilares ----------------------------------------
				array(
					'key'   => 'field_solucao_pilares_tab',
					'label' => '3 · Pilares',
					'name'  => '',
					'type'  => 'tab',
				),
				array(
					'key'          => 'field_solucao_pilares_eyebrow',
					'label'        => 'Eyebrow',
					'name'         => 'solucao_pilares_eyebrow',
					'type'         => 'text',
					'instructions' => 'Ex.: "Pilares". Deixe vazio para ocultar.',
				),
				array(
					'key'          => 'field_solucao_pilares_titulo',
					'label'        => 'Título da seção',
					'name'         => 'solucao_pilares_titulo',
					'type'         => 'text',
					'instructions' => 'Ex.: "Integrações mais rápidas, seguras e inteligentes"',
				),
				// Card 1
				array(
					'key'           => 'field_solucao_pilares_1_icone',
					'label'         => 'Card 1 — Ícone',
					'name'          => 'solucao_pilares_1_icone',
					'type'          => 'image',
					'return_format' => 'id',
					'preview_size'  => 'thumbnail',
					'instructions'  => 'SVG ou PNG do ícone (será exibido em branco sobre fundo azul).',
					'mime_types'    => 'svg, png',
				),
				array(
					'key'   => 'field_solucao_pilares_1_titulo',
					'label' => 'Card 1 — Título',
					'name'  => 'solucao_pilares_1_titulo',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_solucao_pilares_1_desc',
					'label' => 'Card 1 — Descrição',
					'name'  => 'solucao_pilares_1_desc',
					'type'  => 'textarea',
					'rows'  => 3,
				),
				// Card 2
				array(
					'key'           => 'field_solucao_pilares_2_icone',
					'label'         => 'Card 2 — Ícone',
					'name'          => 'solucao_pilares_2_icone',
					'type'          => 'image',
					'return_format' => 'id',
					'preview_size'  => 'thumbnail',
					'instructions'  => 'SVG ou PNG do ícone (será exibido em branco sobre fundo azul).',
					'mime_types'    => 'svg, png',
				),
				array(
					'key'   => 'field_solucao_pilares_2_titulo',
					'label' => 'Card 2 — Título',
					'name'  => 'solucao_pilares_2_titulo',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_solucao_pilares_2_desc',
					'label' => 'Card 2 — Descrição',
					'name'  => 'solucao_pilares_2_desc',
					'type'  => 'textarea',
					'rows'  => 3,
				),
				// Card 3
				array(
					'key'           => 'field_solucao_pilares_3_icone',
					'label'         => 'Card 3 — Ícone',
					'name'          => 'solucao_pilares_3_icone',
					'type'          => 'image',
					'return_format' => 'id',
					'preview_size'  => 'thumbnail',
					'instructions'  => 'SVG ou PNG do ícone (será exibido em branco sobre fundo azul).',
					'mime_types'    => 'svg, png',
				),
				array(
					'key'   => 'field_solucao_pilares_3_titulo',
					'label' => 'Card 3 — Título',
					'name'  => 'solucao_pilares_3_titulo',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_solucao_pilares_3_desc',
					'label' => 'Card 3 — Descrição',
					'name'  => 'solucao_pilares_3_desc',
					'type'  => 'textarea',
					'rows'  => 3,
				),

			// ----- Aba: 3 · Casos de Uso ---------------------------------------
			// ----- Aba: 4 · Logos ------------------------------------------
			array(
				'key'   => 'field_solucao_logos_tab',
				'label' => '4 · Logos',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'          => 'field_solucao_logos_texto',
				'label'        => 'Microcopy',
				'name'         => 'solucao_logos_texto',
				'type'         => 'text',
				'instructions' => 'Ex.: "Integramos os serviços financeiros de grandes empresas". Vazio esconde a faixa.',
			),
			array(
				'key'           => 'field_solucao_logos_clientes',
				'label'         => 'Clientes',
				'name'          => 'solucao_logos_clientes',
				'type'          => 'relationship',
				'post_type'     => array( 'cli_cliente' ),
				'filters'       => array( 'search' ),
				'return_format' => 'id',
				'min'           => 0,
				'max'           => 4,
				'instructions'  => 'Logos exibidos ao lado do texto (recomendado: 2 a 4). O logo é a imagem destacada do cliente.',
			),

			array(
				'key'   => 'field_solucao_casos_tab',
				'label' => '5 · Casos de Uso',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'          => 'field_solucao_casos_eyebrow',
				'label'        => 'Eyebrow',
				'name'         => 'solucao_casos_eyebrow',
				'type'         => 'text',
				'instructions' => 'Ex.: "casos de uso"',
			),
			array(
				'key'   => 'field_solucao_casos_titulo',
				'label' => 'Título da seção',
				'name'  => 'solucao_casos_titulo',
				'type'  => 'text',
			),
			array(
				'key'           => 'field_solucao_casos_1_icone',
				'label'         => 'Card 1 — Ícone',
				'name'          => 'solucao_casos_1_icone',
				'type'          => 'image',
				'return_format' => 'id',
				'preview_size'  => 'thumbnail',
				'mime_types'    => 'svg, png',
			),
			array(
				'key'   => 'field_solucao_casos_1_titulo',
				'label' => 'Card 1 — Título',
				'name'  => 'solucao_casos_1_titulo',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_solucao_casos_1_desc',
				'label' => 'Card 1 — Descrição',
				'name'  => 'solucao_casos_1_desc',
				'type'  => 'textarea',
				'rows'  => 3,
			),
			array(
				'key'           => 'field_solucao_casos_2_icone',
				'label'         => 'Card 2 — Ícone',
				'name'          => 'solucao_casos_2_icone',
				'type'          => 'image',
				'return_format' => 'id',
				'preview_size'  => 'thumbnail',
				'mime_types'    => 'svg, png',
			),
			array(
				'key'   => 'field_solucao_casos_2_titulo',
				'label' => 'Card 2 — Título',
				'name'  => 'solucao_casos_2_titulo',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_solucao_casos_2_desc',
				'label' => 'Card 2 — Descrição',
				'name'  => 'solucao_casos_2_desc',
				'type'  => 'textarea',
				'rows'  => 3,
			),
			array(
				'key'           => 'field_solucao_casos_3_icone',
				'label'         => 'Card 3 — Ícone',
				'name'          => 'solucao_casos_3_icone',
				'type'          => 'image',
				'return_format' => 'id',
				'preview_size'  => 'thumbnail',
				'mime_types'    => 'svg, png',
			),
			array(
				'key'   => 'field_solucao_casos_3_titulo',
				'label' => 'Card 3 — Título',
				'name'  => 'solucao_casos_3_titulo',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_solucao_casos_3_desc',
				'label' => 'Card 3 — Descrição',
				'name'  => 'solucao_casos_3_desc',
				'type'  => 'textarea',
				'rows'  => 3,
			),
			array(
				'key'           => 'field_solucao_casos_4_icone',
				'label'         => 'Card 4 — Ícone',
				'name'          => 'solucao_casos_4_icone',
				'type'          => 'image',
				'return_format' => 'id',
				'preview_size'  => 'thumbnail',
				'mime_types'    => 'svg, png',
			),
			array(
				'key'   => 'field_solucao_casos_4_titulo',
				'label' => 'Card 4 — Título',
				'name'  => 'solucao_casos_4_titulo',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_solucao_casos_4_desc',
				'label' => 'Card 4 — Descrição',
				'name'  => 'solucao_casos_4_desc',
				'type'  => 'textarea',
				'rows'  => 3,
			),
			array(
				'key'           => 'field_solucao_casos_5_icone',
				'label'         => 'Card 5 — Ícone',
				'name'          => 'solucao_casos_5_icone',
				'type'          => 'image',
				'return_format' => 'id',
				'preview_size'  => 'thumbnail',
				'mime_types'    => 'svg, png',
			),
			array(
				'key'   => 'field_solucao_casos_5_titulo',
				'label' => 'Card 5 — Título',
				'name'  => 'solucao_casos_5_titulo',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_solucao_casos_5_desc',
				'label' => 'Card 5 — Descrição',
				'name'  => 'solucao_casos_5_desc',
				'type'  => 'textarea',
				'rows'  => 3,
			),
			array(
				'key'           => 'field_solucao_casos_6_icone',
				'label'         => 'Card 6 — Ícone',
				'name'          => 'solucao_casos_6_icone',
				'type'          => 'image',
				'return_format' => 'id',
				'preview_size'  => 'thumbnail',
				'mime_types'    => 'svg, png',
			),
			array(
				'key'   => 'field_solucao_casos_6_titulo',
				'label' => 'Card 6 — Título',
				'name'  => 'solucao_casos_6_titulo',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_solucao_casos_6_desc',
				'label' => 'Card 6 — Descrição',
				'name'  => 'solucao_casos_6_desc',
				'type'  => 'textarea',
				'rows'  => 3,
			),
			// Card CTA azul (opcional — deixe o texto vazio para não exibir).
			array(
				'key'          => 'field_solucao_casos_cta_texto',
				'label'        => 'CTA — Texto',
				'name'         => 'solucao_casos_cta_texto',
				'type'         => 'text',
				'instructions' => 'Ex.: "Agende uma demonstração". Deixe vazio para ocultar o card azul.',
			),
			array(
				'key'   => 'field_solucao_casos_cta_url',
				'label' => 'CTA — URL',
				'name'  => 'solucao_casos_cta_url',
				'type'  => 'text',
			),

			// ----- Aba: 7 · Diferencial Técnico --------------------------------
			array(
				'key'   => 'field_solucao_dif_tab',
				'label' => '7 · Diferencial',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'          => 'field_solucao_dif_eyebrow',
				'label'        => 'Eyebrow',
				'name'         => 'solucao_dif_eyebrow',
				'type'         => 'text',
				'instructions' => 'Ex.: "diferencial técnico"',
			),
			array(
				'key'   => 'field_solucao_dif_titulo',
				'label' => 'Título',
				'name'  => 'solucao_dif_titulo',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_solucao_dif_corpo',
				'label' => 'Corpo',
				'name'  => 'solucao_dif_corpo',
				'type'  => 'textarea',
				'rows'  => 3,
			),
			array(
				'key'   => 'field_solucao_dif_topico_1',
				'label' => 'Tópico 1',
				'name'  => 'solucao_dif_topico_1',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_solucao_dif_topico_2',
				'label' => 'Tópico 2',
				'name'  => 'solucao_dif_topico_2',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_solucao_dif_topico_3',
				'label' => 'Tópico 3',
				'name'  => 'solucao_dif_topico_3',
				'type'  => 'text',
			),
			array(
				'key'           => 'field_solucao_dif_imagem',
				'label'         => 'Imagem (coluna direita)',
				'name'          => 'solucao_dif_imagem',
				'type'          => 'image',
				'return_format' => 'id',
				'preview_size'  => 'medium',
			),
			array(
				'key'           => 'field_solucao_dif_antes_selos',
				'label'         => 'Exibir antes dos Selos',
				'name'          => 'solucao_dif_antes_selos',
				'type'          => 'true_false',
				'instructions'  => 'Por padrão a seção Selos aparece antes do Diferencial. Marque quando o design da solução inverter as duas.',
				'ui'            => 1,
				'default_value' => 0,
			),

			// ----- Aba: 4 · Selos -------------------------------------------
			array(
				'key'   => 'field_solucao_selos_tab',
				'label' => '6 · Selos',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'          => 'field_solucao_selos_eyebrow',
				'label'        => 'Eyebrow',
				'name'         => 'solucao_selos_eyebrow',
				'type'         => 'text',
				'instructions' => 'Ex.: "compliance & segurança"',
			),
			array(
				'key'   => 'field_solucao_selos_titulo',
				'label' => 'Título',
				'name'  => 'solucao_selos_titulo',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_solucao_selos_corpo',
				'label' => 'Corpo',
				'name'  => 'solucao_selos_corpo',
				'type'  => 'textarea',
				'rows'  => 2,
			),

			// ----- Aba: 7 · Aceleradores ------------------------------------
			array(
				'key'   => 'field_solucao_acel_tab',
				'label' => '9 · Aceleradores',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'          => 'field_solucao_acel_eyebrow',
				'label'        => 'Eyebrow',
				'name'         => 'solucao_acel_eyebrow',
				'type'         => 'text',
				'instructions' => 'Ex.: "aceleradores de integração"',
			),
			array(
				'key'   => 'field_solucao_acel_titulo',
				'label' => 'Título',
				'name'  => 'solucao_acel_titulo',
				'type'  => 'text',
			),
			array(
				'key'  => 'field_solucao_acel_corpo',
				'label' => 'Corpo',
				'name'  => 'solucao_acel_corpo',
				'type'  => 'textarea',
				'rows'  => 3,
			),
			array(
				'key'   => 'field_solucao_acel_topico_1',
				'label' => 'Tópico 1',
				'name'  => 'solucao_acel_topico_1',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_solucao_acel_topico_2',
				'label' => 'Tópico 2',
				'name'  => 'solucao_acel_topico_2',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_solucao_acel_topico_3',
				'label' => 'Tópico 3',
				'name'  => 'solucao_acel_topico_3',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_solucao_acel_topico_4',
				'label' => 'Tópico 4',
				'name'  => 'solucao_acel_topico_4',
				'type'  => 'text',
			),
			array(
				'key'          => 'field_solucao_acel_btn_texto',
				'label'        => 'Botão — Texto',
				'name'         => 'solucao_acel_btn_texto',
				'type'         => 'text',
				'instructions' => 'Ex.: "Começar agora". Deixe vazio para ocultar o botão.',
			),
			array(
				'key'  => 'field_solucao_acel_btn_url',
				'label' => 'Botão — URL',
				'name'  => 'solucao_acel_btn_url',
				'type'  => 'text',
			),
			array(
				'key'           => 'field_solucao_acel_imagem',
				'label'         => 'Imagem (coluna direita)',
				'name'          => 'solucao_acel_imagem',
				'type'          => 'image',
				'return_format' => 'id',
				'preview_size'  => 'medium',
			),

			// ----- Aba: 6 · Plataforma Única --------------------------------
			array(
				'key'   => 'field_solucao_plat_tab',
				'label' => '8 · Plataforma',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'          => 'field_solucao_plat_eyebrow',
				'label'        => 'Eyebrow',
				'name'         => 'solucao_plat_eyebrow',
				'type'         => 'text',
				'instructions' => 'Ex.: "plataforma única"',
			),
			array(
				'key'   => 'field_solucao_plat_titulo',
				'label' => 'Título',
				'name'  => 'solucao_plat_titulo',
				'type'  => 'text',
			),
			array(
				'key'  => 'field_solucao_plat_corpo',
				'label' => 'Corpo',
				'name'  => 'solucao_plat_corpo',
				'type'  => 'textarea',
				'rows'  => 3,
			),
			array(
				'key'   => 'field_solucao_plat_topico_1',
				'label' => 'Tópico 1',
				'name'  => 'solucao_plat_topico_1',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_solucao_plat_topico_2',
				'label' => 'Tópico 2',
				'name'  => 'solucao_plat_topico_2',
				'type'  => 'text',
			),
			array(
				'key'   => 'field_solucao_plat_topico_3',
				'label' => 'Tópico 3',
				'name'  => 'solucao_plat_topico_3',
				'type'  => 'text',
			),
			array(
				'key'           => 'field_solucao_plat_imagem',
				'label'         => 'Imagem (coluna direita)',
				'name'          => 'solucao_plat_imagem',
				'type'          => 'image',
				'return_format' => 'id',
				'preview_size'  => 'medium',
			),

			// ----- Aba: 8 · FAQ ---------------------------------------------
			array(
				'key'   => 'field_solucao_faq_tab',
				'label' => '10 · FAQ',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'          => 'field_solucao_faq_titulo',
				'label'        => 'Título da seção',
				'name'         => 'solucao_faq_titulo',
				'type'         => 'text',
				'default_value' => 'Dúvidas Frequentes',
				'instructions' => 'Deixe vazio para ocultar a seção.',
			),
			array(
				'key'           => 'field_solucao_faq_itens',
				'label'         => 'Perguntas & Respostas',
				'name'          => 'solucao_faq_itens',
				'type'          => 'relationship',
				'post_type'     => array( 'cli_faq' ),
				'filters'       => array( 'search' ),
				'return_format' => 'object',
				'min'           => 0,
				'max'           => 10,
				'instructions'  => 'Selecione os FAQs que devem aparecer nesta solução (máx. 10). O título do post é a pergunta; o conteúdo do post é a resposta.',
			),

			// ----- Aba: 11 · Diagrama ---------------------------------------
			// Renderiza logo depois dos Pilares; o número da aba é só a ordem
			// em que a seção entrou no catálogo, não a posição na página.
			array(
				'key'   => 'field_solucao_diagrama_tab',
				'label' => '11 · Diagrama',
				'name'  => '',
				'type'  => 'tab',
			),
			array(
				'key'          => 'field_solucao_diagrama_titulo',
				'label'        => 'Título da seção',
				'name'         => 'solucao_diagrama_titulo',
				'type'         => 'text',
				'instructions' => 'Deixe vazio para ocultar a seção.',
			),
			array(
				'key'           => 'field_solucao_diagrama_imagem',
				'label'         => 'Ilustração',
				'name'          => 'solucao_diagrama_imagem',
				'type'          => 'image',
				'return_format' => 'id',
				'preview_size'  => 'medium',
				'instructions'  => 'Ilustração fechada, com o texto já embutido na arte, exportada do Figma em 2x.',
			),

			),
		)
	);
}
add_action( 'acf/init', 'cliconnect_acf_fields_cpt' );
