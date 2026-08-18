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
	   ===================================================================== */
	acf_add_local_field_group(
		array(
			'key'            => 'group_cli_solucao',
			'title'          => 'Dados da Solução',
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
				array(
					'key'          => 'field_solucao_url',
					'label'        => 'Link da página (opcional)',
					'name'         => 'solucao_url',
					'type'         => 'url',
					'instructions' => 'URL da landing page desta solução, se houver.',
				),
			),
		)
	);
}
add_action( 'acf/init', 'cliconnect_acf_fields_cpt' );
