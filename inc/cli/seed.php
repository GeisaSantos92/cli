<?php
/**
 * Comando WP-CLI: popula o site com o conteúdo da home aprovada no Figma.
 *
 * Uso:
 *   ./bin/wp cliconnect seed          # cria o que faltar (idempotente)
 *   ./bin/wp cliconnect seed --reset  # apaga o conteúdo do seed e recria
 *
 * Idempotência: cada objeto criado guarda a meta `_cliconnect_seed` com um slug
 * estável. Rodar de novo atualiza em vez de duplicar.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * Seeder do conteúdo inicial do CLI Connect.
 */
class Cliconnect_Seed {

	/**
	 * Chave de meta que marca objetos criados pelo seed.
	 */
	const META = '_cliconnect_seed';

	/**
	 * Cache de anexos importados (slug do arquivo => attachment ID).
	 *
	 * @var array<string,int>
	 */
	protected $midia = array();

	/**
	 * Popula o site com o conteúdo do Figma.
	 *
	 * ## OPTIONS
	 *
	 * [--reset]
	 * : Apaga tudo que o seed criou antes de recriar.
	 *
	 * @param array $args       Argumentos posicionais.
	 * @param array $assoc_args Flags.
	 * @return void
	 */
	public function __invoke( $args, $assoc_args ) {
		$reset = ! empty( $assoc_args['reset'] );

		if ( $reset ) {
			$this->reset();
		}

		WP_CLI::log( '— Importando mídia…' );
		$this->importar_midia();

		WP_CLI::log( '— Criando páginas…' );
		$paginas = $this->criar_paginas();

		WP_CLI::log( '— Criando CPTs…' );
		$this->criar_integracoes();
		$this->criar_clientes();
		$this->criar_agentes();
		$this->criar_eventos();
		$this->criar_selos();
		$this->criar_faq();
		$cases = $this->criar_cases();

		WP_CLI::log( '— Criando posts do blog…' );
		$this->criar_posts();

		WP_CLI::log( '— Preenchendo a home…' );
		$this->preencher_home( $paginas['home'], $cases );

		WP_CLI::log( '— Preenchendo Trabalhe Conosco…' );
		$this->preencher_trabalhe_conosco( $paginas['trabalhe-conosco'] );

		WP_CLI::log( '— Preenchendo CLI Connect…' );
		$this->preencher_cli_connect( $paginas['cli-connect'] );

		WP_CLI::log( '— Preenchendo CLI Signature…' );
		$this->preencher_cli_signature( $paginas['cli-signature'] );

		WP_CLI::log( '— Preenchendo Integração SAP…' );
		$this->preencher_integracao_sap( $paginas['integracao-sap'] );

		WP_CLI::log( '— Preenchendo Contato…' );
		$this->preencher_contato( $paginas['contato'] );

		WP_CLI::log( '— Criando Soluções (taxonomia)…' );
		$termos_solucao = $this->criar_solucoes();

		WP_CLI::log( '— Preenchendo Salesforce…' );
		$this->preencher_solucao_salesforce();

		WP_CLI::log( '— Preenchendo Serviços Financeiros…' );
		$this->preencher_solucao_servicos_financeiros();

		WP_CLI::log( '— Preenchendo Manufatura…' );
		$this->preencher_solucao_manufatura();

		WP_CLI::log( '— Montando menus…' );
		$this->criar_menus( $paginas, $termos_solucao );

		WP_CLI::log( '— Ajustando o Customizer…' );
		$this->configurar_customizer();

		flush_rewrite_rules();

		WP_CLI::success( 'Conteúdo do CLI Connect populado.' );
	}

	/* =====================================================================
	   INFRAESTRUTURA
	   ===================================================================== */

	/**
	 * Apaga todo o conteúdo criado por execuções anteriores.
	 *
	 * @return void
	 */
	protected function reset() {
		$ids = get_posts(
			array(
				'post_type'      => 'any',
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_key'       => self::META, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			)
		);

		foreach ( $ids as $id ) {
			wp_delete_post( $id, true );
		}

		foreach ( array( 'principal', 'rodape', 'rodape_legal' ) as $slug ) {
			$menu = wp_get_nav_menu_object( 'cli-' . $slug );

			if ( $menu ) {
				wp_delete_nav_menu( $menu->term_id );
			}
		}

		// Remove termos de taxonomia criados pelo seed.
		$termos_seed = get_terms(
			array(
				'taxonomy'   => 'cli_categoria_solucao',
				'hide_empty' => false,
				'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					array(
						'key'     => self::META,
						'compare' => 'EXISTS',
					),
				),
			)
		);
		foreach ( $termos_seed as $termo ) {
			wp_delete_term( $termo->term_id, 'cli_categoria_solucao' );
		}

		WP_CLI::log( sprintf( '  reset: %d objetos removidos.', count( $ids ) ) );
	}

	/**
	 * Cria (ou atualiza) um post identificado pelo slug do seed.
	 *
	 * @param string $slug  Identificador estável do seed.
	 * @param array  $dados Argumentos de wp_insert_post.
	 * @return int ID do post.
	 */
	protected function upsert( $slug, $dados ) {
		$existente = get_posts(
			array(
				'post_type'      => $dados['post_type'] ?? 'post',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => $slug,        // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		$dados = wp_parse_args( $dados, array( 'post_status' => 'publish' ) );

		if ( $existente ) {
			$dados['ID'] = $existente[0];
			wp_update_post( $dados );

			return $existente[0];
		}

		$id = wp_insert_post( $dados, true );

		if ( is_wp_error( $id ) ) {
			WP_CLI::warning( sprintf( 'Falha ao criar "%s": %s', $slug, $id->get_error_message() ) );

			return 0;
		}

		update_post_meta( $id, self::META, $slug );

		return $id;
	}

	/**
	 * Importa os arquivos de assets/seed/ para a biblioteca de mídia.
	 *
	 * @return void
	 */
	protected function importar_midia() {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$dir = get_theme_file_path( '/assets/seed' );

		if ( ! is_dir( $dir ) ) {
			WP_CLI::warning( 'Pasta assets/seed não encontrada — o seed seguirá sem imagens.' );

			return;
		}

		// Permite SVG no contexto WP-CLI (seed apenas).
		add_filter(
			'upload_mimes',
			static function ( $mimes ) {
				$mimes['svg'] = 'image/svg+xml';
				return $mimes;
			}
		);

		$arquivos = glob( $dir . '/*.{png,jpg,jpeg,svg}', GLOB_BRACE );
		$novos    = 0;

		foreach ( $arquivos as $arquivo ) {
			$nome = basename( $arquivo );
			$slug = 'midia:' . pathinfo( $nome, PATHINFO_FILENAME );

			$existente = get_posts(
				array(
					'post_type'      => 'attachment',
					'post_status'    => 'any',
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'meta_key'       => self::META, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					'meta_value'     => $slug,      // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				)
			);

			if ( $existente ) {
				$this->midia[ pathinfo( $nome, PATHINFO_FILENAME ) ] = (int) $existente[0];

				continue;
			}

			$tmp = wp_tempnam( $nome );
			copy( $arquivo, $tmp );

			$id = media_handle_sideload(
				array(
					'name'     => $nome,
					'tmp_name' => $tmp,
				),
				0,
				$this->titulo_da_midia( $nome )
			);

			if ( is_wp_error( $id ) ) {
				@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				WP_CLI::warning( sprintf( 'Falha ao importar %s: %s', $nome, $id->get_error_message() ) );

				continue;
			}

			update_post_meta( $id, self::META, $slug );
			update_post_meta( $id, '_wp_attachment_image_alt', $this->titulo_da_midia( $nome ) );

			$this->midia[ pathinfo( $nome, PATHINFO_FILENAME ) ] = (int) $id;
			++$novos;
		}

		WP_CLI::log( sprintf( '  mídia: %d arquivos (%d novos).', count( $this->midia ), $novos ) );
	}

	/**
	 * Título legível a partir do nome do arquivo de mídia.
	 *
	 * @param string $nome Nome do arquivo.
	 * @return string
	 */
	protected function titulo_da_midia( $nome ) {
		$base = pathinfo( $nome, PATHINFO_FILENAME );
		$base = preg_replace( '/^(cliente|integracao|selo|evento|case|midia|logo|boomi|suporte|blog|cc|cs)-/', '', $base );

		return ucwords( str_replace( '-', ' ', $base ) );
	}

	/**
	 * ID do anexo importado a partir do nome base do arquivo.
	 *
	 * @param string $nome Nome sem extensão (ex.: 'cliente-hsbc').
	 * @return int
	 */
	protected function img( $nome ) {
		return $this->midia[ $nome ] ?? 0;
	}

	/**
	 * Busca o ID de um post criado por upsert() a partir do slug do seed.
	 *
	 * @param string $slug      Identificador estável do seed (ex.: 'cliente:hsbc').
	 * @param string $post_type Tipo do post.
	 * @return int ID do post, ou 0 se não existir.
	 */
	protected function id_do_seed( $slug, $post_type ) {
		$posts = get_posts(
			array(
				'post_type'      => $post_type,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => $slug,      // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		return $posts ? (int) $posts[0] : 0;
	}

	/* =====================================================================
	   PÁGINAS
	   ===================================================================== */

	/**
	 * Cria as páginas do site e define home/blog.
	 *
	 * @return array<string,int> slug => ID.
	 */
	protected function criar_paginas() {
		$definicoes = array(
			'home'            => 'Home',
			'blog'            => 'Blog',
			'contato'         => 'Contato',
			'plataforma'      => 'Plataforma',
			'cli-connect'     => 'CLI Connect',
			'cli-signature'   => 'CLI Signature',
			'solucoes'        => 'Soluções',
			'integracao-sap'  => 'Integração SAP',
			'sistemas'        => 'Sistemas',
			'trabalhe-conosco' => 'Trabalhe Conosco',
			'privacidade'     => 'Política de Privacidade',
			'termos'          => 'Termos de Uso',
		);

		$ids = array();

		foreach ( $definicoes as $slug => $titulo ) {
			$ids[ $slug ] = $this->upsert(
				'pagina:' . $slug,
				array(
					'post_type'    => 'page',
					'post_title'   => $titulo,
					'post_name'    => $slug,
					'post_content' => '',
				)
			);
		}

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['home'] );
		update_option( 'page_for_posts', $ids['blog'] );

		WP_CLI::log( sprintf( '  páginas: %d (home #%d, blog #%d).', count( $ids ), $ids['home'], $ids['blog'] ) );

		return $ids;
	}

	/* =====================================================================
	   CPTs
	   ===================================================================== */

	/**
	 * Integrações (sistemas conectados).
	 *
	 * @return void
	 */
	protected function criar_integracoes() {
		$itens = array(
			array( 'SAP', 'integracao-sap', true ),
			array( 'TOTVS', 'integracao-totvs', true ),
			array( 'Salesforce', 'integracao-salesforce', true ),
			array( 'Senior', 'integracao-senior', true ),
			array( 'Sankhya', 'integracao-sankhya', true ),
			array( 'Zendesk', 'integracao-zendesk', true ),
			array( 'IFS', 'integracao-ifs', true ),
			array( 'WhatsApp', 'integracao-whatsapp', true ),
			array( 'ServiceNow', 'integracao-servicenow', true ),
			array( 'OpenAI', 'integracao-openai', true ),
			array( 'Thomson Reuters', 'integracao-thomson-reuters', true ),
			array( 'HubSpot', 'integracao-hubspot', true ),
			array( 'TASY', 'integracao-tasy', true ),
		);

		foreach ( $itens as $ordem => $item ) {
			list( $nome, $arquivo, $hero ) = $item;

			$id = $this->upsert(
				'integracao:' . sanitize_title( $nome ),
				array(
					'post_type'  => 'cli_integracao',
					'post_title' => $nome,
					'menu_order' => $ordem,
				)
			);

			if ( ! $id ) {
				continue;
			}

			$this->definir_thumb( $id, $arquivo );
			update_field( 'destaque_hero', $hero ? 1 : 0, $id );
		}

		WP_CLI::log( sprintf( '  integrações: %d.', count( $itens ) ) );
	}

	/**
	 * Clientes e parceiros.
	 *
	 * @return void
	 */
	protected function criar_clientes() {
		$itens = array(
			array( 'Panasonic', 'cliente-panasonic', true ),
			array( 'Cocamar', 'cliente-cocamar', true ),
			array( 'Localiza', 'cliente-localiza', true ),
			array( 'HSBC', 'cliente-hsbc', false ),
			array( 'Unimed', 'cliente-unimed', false ),
			array( 'Martins', 'cliente-martins', false ),
			array( 'Culligan', 'cliente-culligan', false ),
			array( 'Arcom', 'cliente-arcom', false ),
			array( 'Seculus', 'cliente-seculus', false ),
			array( 'Grupo Ferroeste', 'cliente-grupo-ferroeste', false ),
			array( 'Rodoil', 'cliente-rodoil', false ),
			array( 'Albaugh', 'cliente-albaugh', false ),
			array( 'Real', 'cliente-real', false ),
			array( 'BNP Paribas Cardif', 'cliente-bnp-paribas-cardif', false ),
			array( 'Thomson Reuters', 'cliente-thomson-reuters', false ),
			array( 'Unidas', 'cliente-unidas', false ),
			array( 'BRZ', 'cliente-brz', false ),
			array( 'SBC', 'cliente-sbc', false ),
			array( 'Indiana', 'cliente-indiana', false ),
		);

		foreach ( $itens as $ordem => $item ) {
			list( $nome, $arquivo, $prova ) = $item;

			$id = $this->upsert(
				'cliente:' . sanitize_title( $nome ),
				array(
					'post_type'  => 'cli_cliente',
					'post_title' => $nome,
					'menu_order' => $ordem,
				)
			);

			if ( ! $id ) {
				continue;
			}

			$this->definir_thumb( $id, $arquivo );
			update_field( 'prova_social', $prova ? 1 : 0, $id );
		}

		WP_CLI::log( sprintf( '  clientes: %d.', count( $itens ) ) );
	}

	/**
	 * Agentes de IA da esteira.
	 *
	 * @return void
	 */
	protected function criar_agentes() {
		$itens = array(
			array(
				'Copiloto de Vendas B2B',
				'vendas',
				'Auxilia representantes recomendando produtos, checando estoque e aplicando regras de preço de grandes contas.',
				array( 'SAP', 'Salesforce', 'TOTVS' ),
			),
			array(
				'Conciliação Fiscal Automatizada',
				'fiscal',
				'Captura notas fiscais de entrada, valida impostos retidos e aponta divergências antes do fechamento.',
				array( 'SAP', 'Thomson Reuters', 'Senior' ),
			),
			array(
				'Assistente de Pós-Venda e Logística',
				'logistica',
				'Rastreia entregas complexas, prevê atrasos e avisa o cliente final proativamente sobre o status do pedido.',
				array( 'SAP', 'WhatsApp', 'Senior' ),
			),
			array(
				'Analista de Crédito e Compliance',
				'credito',
				'Avalia o risco de novos clientes cruzando dados internos com birôs de crédito para liberar pedidos.',
				array( 'SAP', 'Thomson Reuters', 'ServiceNow' ),
			),
			array(
				'Triagem de Suporte Nível 1',
				'suporte',
				'Rastreio de pedidos automatizado, cotação de frete dinâmica e comunicação de status para o cliente final.',
				array( 'Zendesk', 'WhatsApp', 'OpenAI' ),
			),
			array(
				'Automação da sincronização de pedidos',
				'automacao',
				'Integra vendas instantaneamente entre a ponta comercial e o faturamento para eliminar erros manuais.',
				array( 'SAP', 'Salesforce' ),
			),
			array(
				'Automação do agendamento de consulta',
				'agenda',
				'Realiza marcações automáticas com pacientes via WhatsApp, atualizando as agendas médicas em tempo real.',
				array( 'TASY', 'WhatsApp', 'OpenAI' ),
			),
			array(
				'Sincronização automática de estoque',
				'estoque',
				'Sincroniza o saldo físico do armazém central com as lojas virtuais para evitar vendas sem produto.',
				array( 'SAP', 'TOTVS' ),
			),
			array(
				'Simulação dos novos impostos da Reforma Tributária',
				'fiscal',
				'Analisa o histórico de faturamento e simula os impactos fiscais da transição para o novo modelo de impostos.',
				array( 'SAP', 'Thomson Reuters' ),
			),
		);

		foreach ( $itens as $ordem => $item ) {
			list( $titulo, $icone, $descricao, $integracoes ) = $item;

			$id = $this->upsert(
				'agente:' . sanitize_title( $titulo ),
				array(
					'post_type'  => 'cli_agente',
					'post_title' => $titulo,
					'menu_order' => $ordem,
				)
			);

			if ( ! $id ) {
				continue;
			}

			update_field( 'icone', $icone, $id );
			update_field( 'descricao', $descricao, $id );
			update_field( 'status', 'Ativo', $id );
			update_field( 'integracoes', $this->ids_por_titulo( 'cli_integracao', $integracoes ), $id );
		}

		WP_CLI::log( sprintf( '  agentes: %d.', count( $itens ) ) );
	}

	/**
	 * Cards de eventos automáticos.
	 *
	 * @return void
	 */
	protected function criar_eventos() {
		$itens = array(
			array(
				'Informações sempre sincronizadas',
				'Evite divergências entre sistemas e garanta que todas as áreas trabalhem com os mesmos dados.',
				'evento-sincronizadas',
			),
			array(
				'Respostas mais rápidas ao negócio',
				'Atualizações acontecem automaticamente, sem filas, conferências ou intervenções da TI.',
				'evento-respostas-rapidas',
			),
			array(
				'Mais visibilidade sobre processos críticos',
				'Monitore processos críticos em tempo real e identifique situações que exigem atenção imediatamente.',
				'evento-visibilidade',
			),
			array(
				'Se adapte a mudanças regulatórias',
				'Transforme alterações normativas em ações automáticas, reduzindo riscos e acelerando a adaptação da empresa.',
				'evento-regulatorias',
			),
		);

		foreach ( $itens as $ordem => $item ) {
			list( $titulo, $descricao, $arquivo ) = $item;

			$id = $this->upsert(
				'evento:' . sanitize_title( $titulo ),
				array(
					'post_type'  => 'cli_evento',
					'post_title' => $titulo,
					'menu_order' => $ordem,
				)
			);

			if ( ! $id ) {
				continue;
			}

			update_field( 'descricao', $descricao, $id );
			$this->definir_thumb( $id, $arquivo );
		}

		WP_CLI::log( sprintf( '  eventos: %d.', count( $itens ) ) );
	}

	/**
	 * Selos de compliance.
	 *
	 * @return void
	 */
	protected function criar_selos() {
		$itens = array(
			array( 'AICPA SOC 2', 'selo-aicpa-soc-2' ),
			array( 'AICPA SOC', 'selo-aicpa-soc' ),
			array( 'ISO/IEC 27001', 'selo-iso-27001' ),
			array( 'Oracle Certified Specialist', 'selo-oracle-certified' ),
			array( 'EU GDPR Compliant', 'selo-eu-gdpr' ),
			array( 'FedRAMP', 'selo-fedramp' ),
			array( 'StateRAMP Authorized', 'selo-stateramp' ),
			array( 'IRAP', 'selo-irap' ),
			array( 'PCI DSS Certified', 'selo-pci-dss' ),
		);

		foreach ( $itens as $ordem => $item ) {
			list( $nome, $arquivo ) = $item;

			$id = $this->upsert(
				'selo:' . sanitize_title( $nome ),
				array(
					'post_type'  => 'cli_selo',
					'post_title' => $nome,
					'menu_order' => $ordem,
				)
			);

			if ( $id ) {
				$this->definir_thumb( $id, $arquivo );
			}
		}

		WP_CLI::log( sprintf( '  selos: %d.', count( $itens ) ) );
	}

	/**
	 * Perguntas frequentes.
	 *
	 * @return void
	 */
	protected function criar_faq() {
		$itens = array(
			array(
				'O que exatamente o CLI Connect faz?',
				'<p>O CLI Connect conecta os sistemas que a sua empresa já usa — ERP, e-commerce, CRM, sistemas fiscais e logísticos — em uma única estrutura governada. Além das integrações, você ganha eventos automáticos que disparam ações entre os sistemas e uma biblioteca com mais de 30.000 automações prontas para usar.</p>',
			),
			array(
				'Quanto tempo demora o serviço?',
				'<p>A maior parte das integrações fica pronta em até <strong>5 dias</strong>, porque partimos de conectores e receitas já validados. Projetos que envolvem regras de negócio muito específicas passam por um levantamento rápido antes de entrar na fila de implantação.</p>',
			),
			array(
				'E se algo parar de funcionar?',
				'<p>O monitoramento é nosso, não seu. A equipe acompanha as integrações em tempo real e, na maioria dos casos, já está resolvendo antes de você perceber. O suporte é humanizado e está disponível pelo portal, e-mail e WhatsApp.</p>',
			),
			array(
				'Vou depender da CLI para tudo?',
				'<p>Não. Toda a operação fica documentada e visível para o seu time no painel, e as integrações rodam sobre a plataforma Boomi — padrão global de mercado. Você mantém a governança e escolhe o quanto quer delegar.</p>',
			),
			array(
				'Como funciona o modelo de contratação?',
				'<p>É uma <strong>mensalidade fixa</strong>, com integrações ilimitadas e serviço gerenciado incluso. Não há cobrança por volume de chamadas nem por nova integração: quanto mais a sua operação cresce, mais você se beneficia do modelo.</p>',
			),
			array(
				'Posso criar minhas próprias integrações na CLI Connect?',
				'<p>Sim. Além da biblioteca com mais de 30.000 automações prontas, a plataforma Boomi permite que o seu time crie conectores e fluxos personalizados. A CLI Connect apoia na estruturação e documentação dessas integrações, garantindo que sigam as melhores práticas de governança e performance.</p>',
			),
		);

		foreach ( $itens as $ordem => $item ) {
			list( $pergunta, $resposta ) = $item;

			$this->upsert(
				'faq:' . sanitize_title( $pergunta ),
				array(
					'post_type'    => 'cli_faq',
					'post_title'   => $pergunta,
					'post_content' => $resposta,
					'menu_order'   => $ordem,
				)
			);
		}

		WP_CLI::log( sprintf( '  FAQ: %d.', count( $itens ) ) );
	}

	/**
	 * Cases de sucesso.
	 *
	 * @return array<string,int> Chave => ID.
	 */
	protected function criar_cases() {
		$excerpt_padrao = 'A implementação do CLI Connect permitiu integrar sistemas, automatizar eventos e aumentar a visibilidade sobre toda a operação.';

		// 1. Panasonic — duas métricas.
		$panasonic = $this->upsert(
			'case:panasonic',
			array(
				'post_type'    => 'cli_case',
				'post_title'   => 'Acelerou a produção de insights em 10%',
				'post_excerpt' => $excerpt_padrao,
				'post_content' => '<p>A Panasonic conectou ERP, e-commerce e sistemas fiscais em uma estrutura única de integrações, com eventos automáticos para sincronização de informações e atualização de processos críticos.</p>',
				'menu_order'   => 0,
			)
		);

		update_field( 'logo', $this->img( 'case-logo-panasonic' ), $panasonic );
		update_field( 'citacao', 'Com a CLI Connect, nós reestruturamos nossa governança e nossos processos financeiros.', $panasonic );
		update_field( 'autor', 'João da Silva', $panasonic );
		update_field( 'cargo', 'Head de operações na Panasonic', $panasonic );
		update_field( 'retrato', $this->img( 'case-retrato' ), $panasonic );
		update_field( 'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', $panasonic );
		update_field( 'metrica_numero', '+85%', $panasonic );
		update_field( 'metrica_texto', 'Redução no tempo de implementação de novas integrações', $panasonic );
		update_field( 'metrica_numero_2', '+60%', $panasonic );
		update_field( 'metrica_texto_2', 'Menos intervenções operacionais manuais', $panasonic );
		update_field( 'desafio_titulo', 'Sistemas desconectados dificultavam a operação', $panasonic );
		update_field( 'desafio_texto', '<p>Com o crescimento da empresa, novas plataformas passaram a fazer parte da operação, incluindo ERP, e-commerce, CRM e sistemas logísticos. No entanto, a troca de informações entre essas aplicações dependia de integrações pontuais e processos pouco padronizados.</p><p>A equipe precisava lidar constantemente com dados inconsistentes, atualizações manuais e dificuldades para acompanhar eventos críticos do negócio em tempo real. Cada nova demanda exigia desenvolvimento adicional, aumentando a complexidade operacional e o tempo de resposta às áreas de negócio.</p>', $panasonic );
		update_field( 'solucao_titulo', 'Uma operação conectada e preparada para evoluir', $panasonic );
		update_field( 'solucao_texto', '<p>A CLI implementou uma arquitetura centralizada de integrações utilizando o CLI Connect, conectando os principais sistemas da operação em uma única estrutura governada.</p><p>Além das integrações, foram criados eventos automáticos para sincronização de informações, notificações operacionais e atualização de processos críticos. A empresa também passou a utilizar uma biblioteca de automações prontas, acelerando a implementação de novas demandas e reduzindo a necessidade de projetos isolados para cada integração.</p>', $panasonic );
		update_field( 'impacto_titulo', 'Mais agilidade, previsibilidade e controle', $panasonic );
		update_field( 'impacto_texto', '<p>Com a nova arquitetura de integrações, a Panasonic reduziu drasticamente o tempo de implementação de novos conectores e eliminou grande parte das intervenções manuais no processo operacional, ganhando visibilidade em tempo real sobre toda a cadeia de dados.</p>', $panasonic );
		$this->definir_thumb( $panasonic, 'case-panasonic' );

		// 2. Moura — sem métricas.
		$moura = $this->upsert(
			'case:moura',
			array(
				'post_type'    => 'cli_case',
				'post_title'   => 'Acelerou a produção de insights em 10%',
				'post_excerpt' => $excerpt_padrao,
				'menu_order'   => 1,
			)
		);

		update_field( 'logo', $this->img( 'case-logo-moura' ), $moura );
		$this->definir_thumb( $moura, 'case-moura' );

		// 3. PetroRecôncavo — uma métrica.
		$petro = $this->upsert(
			'case:petroreconcavo',
			array(
				'post_type'    => 'cli_case',
				'post_title'   => 'Acelerou a produção de insights em 10%',
				'post_excerpt' => $excerpt_padrao,
				'menu_order'   => 2,
			)
		);

		update_field( 'logo', $this->img( 'case-logo-petroreconcavo' ), $petro );
		update_field( 'metrica_numero', '10%', $petro );
		update_field( 'metrica_texto', 'Redução de tempo gasto na triagem', $petro );
		$this->definir_thumb( $petro, 'case-petroreconcavo' );

		// 4. Moura clone — "Evitou perda de 15% das vendas mensais".
		$moura2 = $this->upsert(
			'case:moura-vendas',
			array(
				'post_type'    => 'cli_case',
				'post_title'   => 'Evitou perda de 15% das vendas mensais',
				'post_excerpt' => 'A integração entre CRM, ERP e plataforma de e-commerce eliminou gargalos no processo de vendas e garantiu continuidade operacional mesmo em picos de demanda.',
				'menu_order'   => 3,
			)
		);

		update_field( 'logo', $this->img( 'case-logo-moura' ), $moura2 );
		update_field( 'metrica_numero', '15%', $moura2 );
		update_field( 'metrica_texto', 'De vendas mensais preservadas com integração em tempo real', $moura2 );
		$this->definir_thumb( $moura2, 'case-moura' );

		// 5. PetroRecôncavo clone — "Otimizou operações com dados unificados".
		$petro2 = $this->upsert(
			'case:petroreconcavo-dados',
			array(
				'post_type'    => 'cli_case',
				'post_title'   => 'Otimizou operações com dados unificados',
				'post_excerpt' => 'A unificação dos dados operacionais em uma única arquitetura de integrações reduziu retrabalho, eliminou inconsistências e acelerou a tomada de decisão.',
				'menu_order'   => 4,
			)
		);

		update_field( 'logo', $this->img( 'case-logo-petroreconcavo' ), $petro2 );
		update_field( 'metrica_numero', '+40%', $petro2 );
		update_field( 'metrica_texto', 'De ganho em velocidade de análise operacional', $petro2 );
		$this->definir_thumb( $petro2, 'case-petroreconcavo' );

		WP_CLI::log( '  cases: 5.' );

		return array(
			'panasonic' => $panasonic,
			'moura'     => $moura,
			'petro'     => $petro,
			'moura2'    => $moura2,
			'petro2'    => $petro2,
		);
	}

	/**
	 * Posts do blog exibidos na home.
	 *
	 * @return void
	 */
	protected function criar_posts() {
		$itens = array(
			array(
				'Panasonic agiliza insights e processa dados quatro vezes mais rápido com a CLI Connect',
				'<p>Com ERP, e-commerce e sistemas fiscais conectados em uma única estrutura, a Panasonic passou a acompanhar eventos críticos do negócio em tempo real.</p>',
				'blog-1',
			),
			array(
				'Reforma tributária: como preparar suas integrações para o novo modelo de impostos',
				'<p>A transição exige simular impactos, adequar campos e manter todos os sistemas seguindo a nova legislação. Veja como automatizar essa adaptação.</p>',
				'blog-1',
			),
			array(
				'iPaaS na prática: por que o custo previsível muda o jogo das integrações',
				'<p>Cobrança por volume de chamadas penaliza justamente quem cresce. Entenda o modelo de mensalidade fixa com integrações ilimitadas.</p>',
				'blog-1',
			),
		);

		foreach ( $itens as $item ) {
			list( $titulo, $conteudo, $arquivo ) = $item;

			$id = $this->upsert(
				'post:' . sanitize_title( $titulo ),
				array(
					'post_type'    => 'post',
					'post_title'   => $titulo,
					'post_content' => $conteudo,
				)
			);

			if ( $id ) {
				$this->definir_thumb( $id, $arquivo );
			}
		}

		WP_CLI::log( sprintf( '  posts: %d.', count( $itens ) ) );
	}

	/* =====================================================================
	   HOME (campos ACF)
	   ===================================================================== */

	/**
	 * Preenche os campos ACF da página inicial.
	 *
	 * @param int   $home_id ID da home.
	 * @param array $cases   IDs dos cases criados.
	 * @return void
	 */
	protected function preencher_home( $home_id, $cases ) {
		if ( ! $home_id ) {
			return;
		}

		$campos = array(
			// 1. Hero.
			'hero_eyebrow'          => 'Powered By Boomi',
			'hero_titulo_destaque'  => 'Integrações ilimitadas.',
			'hero_titulo'           => 'Custo previsível. Sem surpresas.',
			'hero_subtitulo'        => 'Integre todos os seus sistemas e coloque agentes de IA personalizados para trabalhar em seus processos.',
			'hero_botao'            => $this->link( 'Agende uma demonstração', '/contato/' ),

			// 2. Agentes.
			'agentes_legenda'       => '+30 mil integrações prontas para uso',

			// 3. Camadas (a ilustração é asset do tema).
			'camadas_titulo'        => "Tudo o que você precisa.\nCom custo previsível.",
			'camadas_texto'         => "Pague um custo fixo e use à vontade nosso serviço de integração.\nQuanto mais a sua operação cresce, mais você se beneficia.",
			'camadas_botao'         => $this->link( 'Entenda o que está incluso', '/plataforma/' ),

			// 4. Boomi.
			'boomi_eyebrow'         => 'Plataforma global',
			'boomi_titulo'          => 'Tecnologia de classe mundial com suporte para o mercado brasileiro',
			'boomi_texto'           => '<p>Tenha acesso à mesma plataforma que grandes empresas globais usam para integrar seus sistemas, mas com o diferencial do <strong>suporte especializado no mercado brasileiro</strong>, preço acessível e serviço gerenciado incluso.</p>',

			// 5. Métricas.
			'metrica_1_numero'      => '+200',
			'metrica_1_rotulo'      => 'integrações por semana',
			'metrica_2_numero'      => '5 dias',
			'metrica_2_rotulo'      => 'até a sua integração estar pronta',
			'metrica_3_numero'      => '30 mil',
			'metrica_3_rotulo'      => 'integrações já prontas para uso',

			// 6. Bloco de mídia 1 — IA corporativa.
			'midia_1_eyebrow'       => 'IA corporativa',
			'midia_1_titulo'        => 'Crie, governe e escale agentes',
			'midia_1_texto'         => 'Crie agentes especializados, conecte seus sistemas e acompanhe toda a operação em um único ambiente.',
			'midia_1_topico_1'      => 'Agentes especializados por área',
			'midia_1_topico_2'      => 'Conectados aos sistemas da empresa',
			'midia_1_topico_3'      => 'Governança e monitoramento centralizados',
			'midia_1_imagem'        => $this->img( 'midia-agentes' ),

			// 7. Bloco de mídia 2 — Na prática.
			'midia_2_eyebrow'       => 'Na prática',
			'midia_2_titulo'        => "Converse com seus dados.\nO agente faz o restante.",
			'midia_2_texto'         => 'Faça perguntas, execute processos e obtenha respostas baseadas nos dados da sua operação.',
			'midia_2_topico_1'      => 'Consulta múltiplos sistemas simultaneamente',
			'midia_2_topico_2'      => 'Executa fluxos sem intervenção manual',
			'midia_2_topico_3'      => 'Mantém todo o histórico da operação',
			'midia_2_imagem'        => $this->img( 'midia-chat' ),

			// 8. Depoimento e cases.
			'case_destaque'         => $cases['panasonic'] ?? 0,
			'cases_metricas'        => array_values( array_filter( array( $cases['petro'] ?? 0 ) ) ),
			'cases_botao'           => $this->link( 'Confira nossos cases', get_post_type_archive_link( 'cli_case' ) ),

			// 9. Eventos.
			'eventos_eyebrow'       => 'Eventos automáticos',
			'eventos_titulo'        => 'Sua operação responde em tempo real às mudanças do negócio',

			// 10. Compliance.
			'compliance_eyebrow'    => 'Compliance & segurança',
			'compliance_titulo'     => 'Lideramos o mercado quando assunto é compliance e segurança',
			'compliance_texto'      => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',

			// 11. Integrações prontas.
			'integracoes_eyebrow'   => 'Integrações prontas',
			'integracoes_titulo'    => "Sua integração pode\njá estar pronta",
			'integracoes_texto'     => 'Conectamos SAP, Protheus, VTEX, Mercado Eletrônico, Salesforce, Senior, MV e dezenas de outros sistemas. Muitas das integrações que você precisa já existem no nosso catálogo.',
			'integracoes_botao'     => $this->link( 'Integrar agora', '/contato/' ),

			// 12. Departamentos.
			'departamento_1'        => 'Compras',
			'departamento_2'        => 'Atendimento',
			'departamento_3'        => 'Logística',
			'departamento_4'        => 'Fiscal',
			'departamento_5'        => 'Financeiro',
			'departamento_6'        => 'RH',
			'departamentos_titulo'  => "Integre todos os departamentos\nda sua empresa",
			'departamentos_texto'   => 'Do financeiro ao atendimento, crie fluxos integrados e centralize todas as informações da sua empresa em uma única tela, sem complicação.',
			'departamentos_botao'   => $this->link( 'Solicitar demonstração', '/contato/' ),
			'prova_texto'           => "+500 empresas já decidiram\nautomatizar seus processos",

			// 13. Frase.
			'frase_texto'           => 'Seus sistemas falam entre si.',
			'frase_texto_b'         => 'Você cuida do',
			'frase_destaque'        => 'que importa',

			// 14. Suporte.
			'suporte_eyebrow'       => 'Atendimento quando precisar',
			'suporte_titulo'        => 'Você não fica sozinho nunca.',
			'suporte_texto'         => '<p>Oferecemos <strong>suporte humanizado</strong> para quando você mais precisa. Nossa equipe monitora, mantém e evolui suas integrações. Se algo der errado, já estamos resolvendo antes de você perceber.</p>',
			'suporte_botao'         => $this->link( 'Ver canais de atendimento', '/contato/' ),

			// 15. Blog.
			'blog_titulo'           => 'Confira as últimas do nosso blog',
			'blog_link'             => $this->link( 'Ver todas as postagens', get_permalink( (int) get_option( 'page_for_posts' ) ) ),

			// 16. FAQ.
			'faq_eyebrow'           => 'FAQ',
			'faq_titulo'            => 'Dúvidas Frequentes',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $home_id );
		}

		WP_CLI::log( sprintf( '  home: %d campos preenchidos.', count( $campos ) ) );
	}

	/**
	 * Monta o array de um campo ACF do tipo Link.
	 *
	 * @param string $titulo Texto do botão.
	 * @param string $url    URL de destino (relativa ou absoluta).
	 * @return array
	 */
	protected function link( $titulo, $url ) {
		if ( $url && '/' === substr( (string) $url, 0, 1 ) ) {
			$url = home_url( $url );
		}

		return array(
			'title'  => $titulo,
			'url'    => (string) $url,
			'target' => '',
		);
	}

	/* =====================================================================
	   SOLUÇÕES (CPT + TAXONOMIA)
	   ===================================================================== */

	/**
	 * Cria a hierarquia de termos cli_categoria_solucao e os posts cli_solucao.
	 *
	 * Retorna um mapa [chave_interna => term_id] dos termos pai, para que
	 * criar_menus() possa gerar URLs reais via get_term_link().
	 *
	 * @return array<string,int>
	 */
	protected function criar_solucoes() {
		$tax = 'cli_categoria_solucao';

		$hierarquia = array(
			'tecnologia'     => array(
				'nome'   => 'Tecnologia',
				'filhos' => array(
					'claude'                         => 'Claude',
					'chatgpt'                        => 'ChatGPT',
					'sap'                            => 'SAP',
					'salesforce'                     => 'Salesforce',
					'totvs-protheus'                 => 'TOTVS Protheus',
					'sankhya'                        => 'Sankhya',
					'senior'                         => 'Senior',
					'dynamics-365'                   => 'Dynamics 365',
				),
			),
			'industria'      => array(
				'nome'   => 'Indústria',
				'filhos' => array(
					'servicos-financeiros'           => 'Serviços Financeiros',
					'manufatura'                     => 'Manufatura',
					'logistica-3pl'                  => 'Logística (3PL)',
					'software-isv'                   => 'Software (ISV)',
					'varejo'                         => 'Varejo',
					'hotelaria-e-turismo'            => 'Hotelaria e Turismo',
					'seguros'                        => 'Seguros',
				),
			),
			'departamento'   => array(
				'nome'   => 'Departamento',
				'filhos' => array(
					'recursos-humanos-rh'            => 'Recursos Humanos (RH)',
					'operacoes-de-receita-revops'    => 'Operações de Receita (RevOps)',
					'marketing'                      => 'Marketing',
					'financeiro'                     => 'Financeiro',
				),
			),
			'nuvem'          => array(
				'nome'   => 'Nuvem',
				'filhos' => array(
					'aws'                            => 'AWS',
					'google-cloud'                   => 'Google Cloud',
					'azure'                          => 'Azure',
				),
			),
			'por-iniciativa' => array(
				'nome'   => 'Por Iniciativa',
				'filhos' => array(
					'atualizacao-de-sistemas-legados' => 'Atualização de Sistemas Legados',
					'pedido-ao-recebimento'           => 'Pedido ao Recebimento',
					'ia-corporativa'                  => 'IA Corporativa',
					'compras-ao-pagamento'            => 'Compras ao Pagamento',
					'jornada-do-colaborador'          => 'Jornada do Colaborador',
					'soberania-de-dados'              => 'Soberania de Dados',
					'visao-360-do-cliente'            => 'Visão 360° do Cliente',
					'modernizacao-de-erp'             => 'Modernização de ERP',
				),
			),
		);

		// Mapa retornado: chave_pai => term_id e chave_filho => term_id.
		$ids = array();

		foreach ( $hierarquia as $chave_pai => $dados_pai ) {
			// Termo pai.
			$existe_pai = term_exists( $dados_pai['nome'], $tax );
			if ( $existe_pai ) {
				$pai_id = (int) ( is_array( $existe_pai ) ? $existe_pai['term_id'] : $existe_pai );
			} else {
				$ins = wp_insert_term( $dados_pai['nome'], $tax );
				if ( is_wp_error( $ins ) ) {
					WP_CLI::warning( "  Categoria \"{$dados_pai['nome']}\": " . $ins->get_error_message() );
					continue;
				}
				$pai_id = (int) $ins['term_id'];
			}
			update_term_meta( $pai_id, self::META, $chave_pai );
			$ids[ $chave_pai ] = $pai_id;

			// Termos filhos + posts.
			foreach ( $dados_pai['filhos'] as $chave_filho => $nome_filho ) {
				$existe_filho = term_exists( $nome_filho, $tax, $pai_id );
				if ( $existe_filho ) {
					$filho_id = (int) ( is_array( $existe_filho ) ? $existe_filho['term_id'] : $existe_filho );
				} else {
					$ins_filho = wp_insert_term( $nome_filho, $tax, array( 'parent' => $pai_id ) );
					if ( is_wp_error( $ins_filho ) ) {
						WP_CLI::warning( "  Tipo \"{$nome_filho}\": " . $ins_filho->get_error_message() );
						continue;
					}
					$filho_id = (int) $ins_filho['term_id'];
				}
				update_term_meta( $filho_id, self::META, $chave_filho );
				$ids[ $chave_filho ] = $filho_id;

				// Post cli_solucao para este tipo (stub sem imagem — cliente adiciona depois).
				$post_id = $this->upsert(
					'solucao:' . $chave_filho,
					array(
						'post_title'  => $nome_filho,
						'post_type'   => 'cli_solucao',
						'post_status' => 'publish',
					)
				);

				if ( $post_id ) {
					wp_set_object_terms( $post_id, $filho_id, $tax );
				}
			}
		}

		WP_CLI::log( sprintf( '  soluções: %d categorias, %d tipos.', count( $hierarquia ), count( $ids ) - count( $hierarquia ) ) );

		return $ids;
	}

	/* =====================================================================
	   MENUS
	   ===================================================================== */

	/**
	 * Cria os três menus do tema e os atribui às locations.
	 *
	 * @param array $paginas        slug => ID.
	 * @param array $termos_solucao chave => term_id dos termos de solução.
	 * @return void
	 */
	protected function criar_menus( $paginas, $termos_solucao = array() ) {
		$cases_url     = get_post_type_archive_link( 'cli_case' );
		$blog_url      = get_permalink( (int) get_option( 'page_for_posts' ) );
		$solucoes_base = get_post_type_archive_link( 'cli_solucao' ) ?: home_url( '/solucoes/' );

		/*
		 * Helper: retorna a URL do termo de taxonomia pelo mapa de IDs.
		 * Fallback para $solucoes_base se o termo não foi criado.
		 */
		$turl = function ( $chave ) use ( $termos_solucao, $solucoes_base ) {
			if ( empty( $termos_solucao[ $chave ] ) ) {
				return $solucoes_base;
			}
			$link = get_term_link( (int) $termos_solucao[ $chave ], 'cli_categoria_solucao' );
			return is_wp_error( $link ) ? $solucoes_base : $link;
		};

		/*
		 * Soluções no menu principal tem três níveis: os filhos viram os títulos
		 * das colunas do mega menu e os netos, os links. O item sem filhos no
		 * fim é renderizado como o "Ver todos" do rodapé do painel.
		 */
		$solucoes_mega = array(
			array(
				'titulo' => 'Tecnologia',
				'url'    => $turl( 'tecnologia' ),
				'filhos' => array(
					'Claude'          => $turl( 'claude' ),
					'ChatGPT'         => $turl( 'chatgpt' ),
					'SAP'             => $turl( 'sap' ),
					'Salesforce'      => $turl( 'salesforce' ),
					'TOTVS Protheus'  => $turl( 'totvs-protheus' ),
					'Sankhya'         => $turl( 'sankhya' ),
					'Senior'          => $turl( 'senior' ),
					'Dynamics 365'    => $turl( 'dynamics-365' ),
				),
			),
			array(
				'titulo' => 'Indústria',
				'url'    => $turl( 'industria' ),
				'filhos' => array(
					'Serviços Financeiros' => $turl( 'servicos-financeiros' ),
					'Manufatura'           => $turl( 'manufatura' ),
					'Logística (3PL)'      => $turl( 'logistica-3pl' ),
					'Software (ISV)'       => $turl( 'software-isv' ),
					'Varejo'               => $turl( 'varejo' ),
					'Hotelaria e Turismo'  => $turl( 'hotelaria-e-turismo' ),
					'Seguros'              => $turl( 'seguros' ),
				),
			),
			array(
				'titulo' => 'Departamento',
				'url'    => $turl( 'departamento' ),
				'filhos' => array(
					'Recursos Humanos (RH)'         => $turl( 'recursos-humanos-rh' ),
					'Operações de Receita (RevOps)' => $turl( 'operacoes-de-receita-revops' ),
					'Marketing'                     => $turl( 'marketing' ),
					'Financeiro'                    => $turl( 'financeiro' ),
				),
			),
			array(
				'titulo' => 'Nuvem',
				'url'    => $turl( 'nuvem' ),
				'filhos' => array(
					'AWS'          => $turl( 'aws' ),
					'Google Cloud' => $turl( 'google-cloud' ),
					'Azure'        => $turl( 'azure' ),
				),
			),
			array(
				'titulo' => 'Por Iniciativa',
				'url'    => $turl( 'por-iniciativa' ),
				'filhos' => array(
					'Atualização de Sistemas Legados' => $turl( 'atualizacao-de-sistemas-legados' ),
					'Pedido ao Recebimento'           => $turl( 'pedido-ao-recebimento' ),
					'IA Corporativa'                  => $turl( 'ia-corporativa' ),
					'Compras ao Pagamento'            => $turl( 'compras-ao-pagamento' ),
					'Jornada do Colaborador'          => $turl( 'jornada-do-colaborador' ),
					'Soberania de Dados'              => $turl( 'soberania-de-dados' ),
					'Visão 360° do Cliente'           => $turl( 'visao-360-do-cliente' ),
					'Modernização de ERP'             => $turl( 'modernizacao-de-erp' ),
				),
			),
			array( 'titulo' => 'Ver todos', 'url' => $solucoes_base ),
		);

		$descricao_produto = 'Integre todos os seus sistemas e coloque agentes de IA personalizados para trabalhar em seus processos.';

		// --- Menu principal --------------------------------------------------
		$this->montar_menu(
			'principal',
			'CLI — Menu Principal',
			array(
				array(
					'titulo' => 'Plataforma',
					'url'    => '/plataforma/',
					'filhos' => array(
						array(
							'titulo'    => 'CLI Connect',
							'url'       => '/cli-connect/',
							'descricao' => $descricao_produto,
						),
						array(
							'titulo'    => 'CLI Signature',
							'url'       => '/cli-signature/',
							'descricao' => $descricao_produto,
						),
					),
				),
				array(
					'titulo' => 'Soluções',
					'url'    => $solucoes_base,
					'filhos' => $solucoes_mega,
				),
				array( 'titulo' => 'Integração SAP', 'url' => '/integracao-sap/' ),
				array( 'titulo' => 'Cases', 'url' => $cases_url ),
				array( 'titulo' => 'Blog', 'url' => $blog_url ),
				array( 'titulo' => 'Contato', 'url' => '/contato/' ),
			)
		);

		// --- Rodapé (colunas) — depth=3 ----------------------------------------
		// Nível 1 = coluna visual (grupo); nível 2 = seção; nível 3 = links.
		$this->montar_menu(
			'rodape',
			'CLI — Rodapé',
			array(
				// Coluna 1: Plataforma + Recursos
				array(
					'titulo' => 'col-plataforma-recursos',
					'url'    => '#',
					'filhos' => array(
						array(
							'titulo' => 'Plataforma',
							'url'    => '/plataforma/',
							'filhos' => array(
								'CLI Connect'   => '/cli-connect/',
								'CLI Signature' => '/cli-signature/',
							),
						),
						array(
							'titulo' => 'Recursos',
							'url'    => '/contato/',
							'filhos' => array(
								'Cases'            => $cases_url,
								'Blog'             => $blog_url,
								'Trabalhe Conosco' => '/trabalhe-conosco/',
								'Contato'          => '/contato/',
							),
						),
					),
				),
				// Coluna 2: Tecnologia
				array(
					'titulo' => 'col-tecnologia',
					'url'    => '#',
					'filhos' => array(
						array(
							'titulo' => 'Tecnologia',
							'url'    => $turl( 'tecnologia' ),
							'filhos' => array(
								'Claude'         => $turl( 'claude' ),
								'ChatGPT'        => $turl( 'chatgpt' ),
								'SAP'            => $turl( 'sap' ),
								'Salesforce'     => $turl( 'salesforce' ),
								'TOTVS Protheus' => $turl( 'totvs-protheus' ),
								'Sankhya'        => $turl( 'sankhya' ),
								'Senior'         => $turl( 'senior' ),
								'Dynamics 365'   => $turl( 'dynamics-365' ),
								array(
									'titulo'  => 'Ver todos',
									'url'     => $turl( 'tecnologia' ),
									'classes' => 'link-ver-todos',
								),
							),
						),
					),
				),
				// Coluna 3: Indústria
				array(
					'titulo' => 'col-industria',
					'url'    => '#',
					'filhos' => array(
						array(
							'titulo' => 'Indústria',
							'url'    => $turl( 'industria' ),
							'filhos' => array(
								'Serviços Financeiros' => $turl( 'servicos-financeiros' ),
								'Manufatura'           => $turl( 'manufatura' ),
								'Logística (3PL)'      => $turl( 'logistica-3pl' ),
								'Software (ISV)'       => $turl( 'software-isv' ),
								'Varejo'               => $turl( 'varejo' ),
								'Hotelaria e Turismo'  => $turl( 'hotelaria-e-turismo' ),
								'Seguros'              => $turl( 'seguros' ),
							),
						),
					),
				),
				// Coluna 4: Departamento + Nuvem
				array(
					'titulo' => 'col-departamento-nuvem',
					'url'    => '#',
					'filhos' => array(
						array(
							'titulo' => 'Departamento',
							'url'    => $turl( 'departamento' ),
							'filhos' => array(
								'Recursos Humanos (RH)'         => $turl( 'recursos-humanos-rh' ),
								'Operações de Receita (RevOps)' => $turl( 'operacoes-de-receita-revops' ),
								'Marketing'                     => $turl( 'marketing' ),
								'Financeiro'                    => $turl( 'financeiro' ),
							),
						),
						array(
							'titulo' => 'Nuvem',
							'url'    => $turl( 'nuvem' ),
							'filhos' => array(
								'AWS'          => $turl( 'aws' ),
								'Google Cloud' => $turl( 'google-cloud' ),
								'Azure'        => $turl( 'azure' ),
							),
						),
					),
				),
				// Coluna 5: Por Iniciativa
				array(
					'titulo' => 'col-iniciativas',
					'url'    => '#',
					'filhos' => array(
						array(
							'titulo' => 'Por Iniciativa',
							'url'    => $turl( 'por-iniciativa' ),
							'filhos' => array(
								'Atualização de Sistemas Legados' => $turl( 'atualizacao-de-sistemas-legados' ),
								'IA Corporativa'                  => $turl( 'ia-corporativa' ),
								'Compras ao Pagamento'            => $turl( 'compras-ao-pagamento' ),
								'Pedido ao Recebimento'           => $turl( 'pedido-ao-recebimento' ),
								'Jornada do Colaborador'          => $turl( 'jornada-do-colaborador' ),
								'Soberania de Dados'              => $turl( 'soberania-de-dados' ),
								'Visão 360° do Cliente'           => $turl( 'visao-360-do-cliente' ),
								'Modernização de ERP'             => $turl( 'modernizacao-de-erp' ),
							),
						),
					),
				),
			)
		);

		// --- Rodapé legal ----------------------------------------------------
		$this->montar_menu(
			'rodape_legal',
			'CLI — Rodapé Legal',
			array()
		);
	}

	/**
	 * Cria/recria um menu e atribui à location.
	 *
	 * @param string $location Slug da location registrada.
	 * @param string $nome     Nome legível do menu.
	 * @param array  $itens    Itens (titulo, url, filhos).
	 * @return void
	 */
	protected function montar_menu( $location, $nome, $itens ) {
		$slug = 'cli-' . $location;
		$menu = wp_get_nav_menu_object( $slug );

		if ( $menu ) {
			wp_delete_nav_menu( $menu->term_id );
		}

		$menu_id = wp_create_nav_menu( $slug );

		if ( is_wp_error( $menu_id ) ) {
			WP_CLI::warning( sprintf( 'Falha ao criar o menu %s.', $location ) );

			return;
		}

		wp_update_term( $menu_id, 'nav_menu', array( 'name' => $nome ) );

		$this->inserir_itens( $menu_id, $itens, 0 );

		$locations              = get_theme_mod( 'nav_menu_locations', array() );
		$locations[ $location ] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );

		$this->sincronizar_polylang( $location, $menu_id );

		WP_CLI::log( sprintf( '  menu "%s" → location %s.', $nome, $location ) );
	}

	/**
	 * Insere recursivamente uma lista de itens sob um item pai.
	 *
	 * @param int   $menu_id ID do menu.
	 * @param array $itens   Itens no formato aceito por normalizar_itens().
	 * @param int   $pai     ID do item pai (0 no 1º nível).
	 * @return void
	 */
	protected function inserir_itens( $menu_id, $itens, $pai ) {
		foreach ( $this->normalizar_itens( $itens ) as $item ) {
			$id = wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'       => $item['titulo'],
					'menu-item-url'         => $this->url_absoluta( $item['url'] ),
					'menu-item-description' => $item['descricao'],
					'menu-item-classes'     => $item['classes'],
					'menu-item-status'      => 'publish',
					'menu-item-type'        => 'custom',
					'menu-item-parent-id'   => $pai,
				)
			);

			if ( is_wp_error( $id ) || ! $item['filhos'] ) {
				continue;
			}

			$this->inserir_itens( $menu_id, $item['filhos'], (int) $id );
		}
	}

	/**
	 * Normaliza itens de menu.
	 *
	 * Aceita a forma curta ('Título' => '/url/') e a completa (array com
	 * titulo, url, descricao, classes e filhos) — assim as listas simples
	 * seguem legíveis e só quem precisa de descrição ou submenu se estende.
	 *
	 * @param array $itens Itens em qualquer uma das duas formas.
	 * @return array<int,array>
	 */
	protected function normalizar_itens( $itens ) {
		$saida = array();

		foreach ( $itens as $chave => $item ) {
			if ( ! is_array( $item ) ) {
				$item = array(
					'titulo' => $chave,
					'url'    => $item,
				);
			}

			$saida[] = wp_parse_args(
				$item,
				array(
					'titulo'    => '',
					'url'       => '',
					'descricao' => '',
					'classes'   => '',
					'filhos'    => array(),
				)
			);
		}

		return $saida;
	}

	/**
	 * Registra o menu nas locations do Polylang (que são por idioma).
	 *
	 * Sem isso o Polylang sobrescreve `theme_mod_nav_menu_locations` no front e
	 * o wp_nav_menu sai vazio, mesmo com a location atribuída no tema.
	 *
	 * @param string $location Slug da location.
	 * @param int    $menu_id  ID do menu.
	 * @return void
	 */
	protected function sincronizar_polylang( $location, $menu_id ) {
		if ( ! function_exists( 'pll_languages_list' ) ) {
			return;
		}

		$idiomas = pll_languages_list();

		if ( ! $idiomas ) {
			return;
		}

		$opcoes = get_option( 'polylang' );

		if ( ! is_array( $opcoes ) ) {
			return;
		}

		$tema = get_stylesheet();

		if ( ! isset( $opcoes['nav_menus'] ) || ! is_array( $opcoes['nav_menus'] ) ) {
			$opcoes['nav_menus'] = array();
		}

		foreach ( $idiomas as $lang ) {
			$opcoes['nav_menus'][ $tema ][ $location ][ $lang ] = $menu_id;
		}

		update_option( 'polylang', $opcoes );
	}

	/* =====================================================================
	   CUSTOMIZER
	   ===================================================================== */

	/**
	 * Define os theme mods usados por header, rodapé e login.
	 *
	 * @return void
	 */
	protected function configurar_customizer() {
		/*
		 * Os logos não entram aqui: o tema já traz os SVGs em assets/img/
		 * (ver cliconnect_logos_tema()). O media control do Customizer segue
		 * disponível para quem quiser sobrescrever.
		 */
		$mods = array(
			'cliconnect_portal_texto'     => 'Portal do Cliente',
			'cliconnect_portal_url'       => 'https://portal.cliconnect.com.br/',
			'cliconnect_header_cta_texto' => 'Acessar Plataforma',
			'cliconnect_header_cta_url'   => 'https://plataforma.cliconnect.com.br/',
			'cliconnect_cta_titulo'       => "Planeje a evolução\ndas suas integrações",
			'cliconnect_cta_botao_texto'  => 'Fale conosco no Whatsapp',
			'cliconnect_cta_botao_url'    => 'https://wa.me/5511999999999',
			'cliconnect_whatsapp_url'     => 'https://wa.me/5511999999999',
			'cliconnect_social_linkedin'  => 'https://www.linkedin.com/company/cliconnect/',
			'cliconnect_social_instagram' => 'https://www.instagram.com/cliconnect/',
			'cliconnect_social_youtube'   => 'https://www.youtube.com/@cliconnect',
			'cliconnect_phone'            => '+55 11 99999-9999',
			'cliconnect_email_geral'      => 'contato@cliconnect.com.br',
		);

		foreach ( $mods as $chave => $valor ) {
			set_theme_mod( $chave, $valor );
		}

		/*
		 * Opções que saíram do tema (logos agora vêm de assets/img/, assinatura
		 * da agência virou template-part). Removidas para o seed não deixar
		 * valores antigos mandando no front.
		 */
		$obsoletos = array(
			'cliconnect_logo_escuro',
			'cliconnect_logo_claro',
			'cliconnect_agencia_nome',
			'cliconnect_agencia_url',
		);

		foreach ( $obsoletos as $chave ) {
			remove_theme_mod( $chave );
		}

		update_option( 'blogname', 'CLI Connect' );
		update_option( 'blogdescription', 'Integrações ilimitadas. Custo previsível. Sem surpresas.' );

		WP_CLI::log( sprintf( '  customizer: %d opções.', count( $mods ) ) );
	}

	/* =====================================================================
	   UTILITÁRIOS
	   ===================================================================== */

	/**
	 * Preenche os campos ACF da página Trabalhe Conosco.
	 *
	 * Fotos da equipe (sobre_foto_*) e foto do depoimento (dep_foto) precisam
	 * de imagens reais do cliente — os campos ficam vazios no seed.
	 *
	 * @param int $page_id ID da página.
	 * @return void
	 */
	protected function preencher_trabalhe_conosco( $page_id ) {
		if ( ! $page_id ) {
			return;
		}

		$campos = array(
			// 1. Hero.
			'hero_eyebrow' => 'TRABALHE CONOSCO',
			'hero_titulo'  => 'Construa soluções que movem grandes empresas.',
			'hero_texto'   => 'Na CLI, você faz parte de um time que conecta tecnologias, simplifica operações e ajuda empresas a evoluírem todos os dias. Trabalhe remotamente, participe de projetos desafiadores e cresça ao lado de profissionais apaixonados por inovação.',
			'hero_botao'   => $this->link( 'Veja nossas vagas', '/trabalhe-conosco/#vagas' ),

			// 2. Somos a CLI.
			'sobre_titulo'  => 'Somos a CLI',
			'sobre_texto_1' => 'Com 13 anos de história, somos uma empresa de tecnologia e parceria que conecta cultura, pessoas e soluções. Nossa proposta é transformar continuamente técnico em capacidade humana, gerando impacto real para clientes, parceiros e para o mundo.',
			'sobre_texto_2' => 'Temos uma trajetória sólida, com mais de 75 clientes ativos e mais de 500 integrações prontas para uso. Acreditamos que grandes soluções são construídas por equipes que colaboram, aprendem constantemente e têm autonomia para fazer acontecer.',

			// 3. Métricas.
			'tc_metrica_1_numero' => '13',
			'tc_metrica_1_rotulo' => 'anos de história',
			'tc_metrica_2_numero' => '+80',
			'tc_metrica_2_rotulo' => 'clientes ativos',
			'tc_metrica_3_numero' => '30 mil',
			'tc_metrica_3_rotulo' => 'Integrações já prontas para uso',

			// 4. Frase.
			'tc_frase_parte_1' => 'A tecnologia conecta sistemas.',
			'tc_frase_parte_2' => 'Mas são as pessoas que transformam negócios.',

			// 5. Valores.
			'valores_eyebrow' => 'VALORES',
			'valores_titulo'  => 'Mais do que integrar tecnologia, nós integramos pessoas',
			'valores_cta'     => $this->link( 'Confira nossas vagas', '/trabalhe-conosco/#vagas' ),

			'valor_1_icone'  => 'escudo',
			'valor_1_titulo' => 'Confiança',
			'valor_1_texto'  => 'Agimos com transparência, segurança e respeito. Cumprimos o que prometemos e construímos relações de confiança duradouras com clientes e equipes.',

			'valor_2_icone'  => 'verificado',
			'valor_2_titulo' => 'Igualdade',
			'valor_2_texto'  => 'Damos oportunidade a quem deseja crescer, valorizando o talento e o desenvolvimento de cada pessoa independentemente de sua origem.',

			'valor_3_icone'  => 'grupo',
			'valor_3_titulo' => 'Sucesso do Cliente',
			'valor_3_texto'  => 'O problema do cliente é nosso. Resolvemos com conhecimento de negócio e nos orgulhamos de cada entrega bem-sucedida.',

			'valor_4_icone'  => 'lampada',
			'valor_4_titulo' => 'Inovação',
			'valor_4_texto'  => 'Estimulamos novas ideias e a criatividade para antecipar tendências e gerar soluções inovadoras com responsabilidade.',

			'valor_5_icone'  => 'grupo',
			'valor_5_titulo' => 'Colaboração',
			'valor_5_texto'  => 'Somos uma equipe unida. Compartilhamos conhecimento, conquistas e aprendizados com espírito de parceria e harmonia.',

			// 6. Depoimento.
			'dep_nome'  => 'Vitória Nunes',
			'dep_cargo' => 'Tech Lead',
			'dep_texto' => 'O trabalho em equipe na CLI é real e acontece no dia a dia. Contar com um time que se ajuda para resolver problemas complexos e que está em total sintonia com ferramentas inovadoras torna a nossa rotina leve e realizadora. No final das contas, o sucesso das nossas entregas é fruto desse ecossistema, onde recebemos apoio de todas as áreas da empresa.',

			// 7. Benefícios.
			'beneficios_eyebrow'   => 'BENEFÍCIOS',
			'beneficios_titulo'    => 'Tudo para que você possa fazer o seu melhor trabalho.',
			'beneficios_subtitulo' => 'Sabemos que você precisa de estrutura para dar o seu melhor. Por isso oferecemos benefícios que fazem diferença no dia a dia.',

			'beneficio_1_icone'  => 'coracao',
			'beneficio_1_titulo' => 'Saúde e Bem-estar',
			'beneficio_1_texto'  => 'Plano de saúde Bradesco e plano odontológico Odontomais, com cobertura ampla para você e seus dependentes.',

			'beneficio_2_icone'  => 'casa',
			'beneficio_2_titulo' => 'Trabalho Remoto',
			'beneficio_2_texto'  => 'Auxílio mensal para pagar os custos do home office e manter sua rotina de trabalho remoto confortável.',

			'beneficio_3_icone'  => 'refeicao',
			'beneficio_3_titulo' => 'Alimentação',
			'beneficio_3_texto'  => 'Auxílio mensal em pix, que você pode usar como quiser para sua alimentação ao longo do mês.',

			'beneficio_4_icone'  => 'grupo',
			'beneficio_4_titulo' => 'Apoio à Família',
			'beneficio_4_texto'  => 'Auxílio-creche para filhos de até 5 anos, porque sabemos que a família também faz parte do sucesso de cada um.',

			'beneficio_5_icone'  => 'verificado',
			'beneficio_5_titulo' => 'Qualidade de vida',
			'beneficio_5_texto'  => 'Acesso ao TotalPass: academias, esportes e atividades de bem-estar para manter a saúde física em dia.',

			'beneficio_6_icone'  => 'bolo',
			'beneficio_6_titulo' => 'Day Off de aniversário',
			'beneficio_6_texto'  => 'No dia que você fizer anos, tire uma folga. Você merece celebrar do jeito que quiser.',

			// 8. Jeito CLI.
			'jeito_titulo'         => 'O jeito CLI de ser',
			'jeito_texto'          => 'Mais do que regras, estes princípios orientam a forma como trabalhamos todos os dias.',
			'jeito_item_1_titulo'  => 'Transparência primeiro',
			'jeito_item_1_texto'   => 'Mesmo quando é difícil, escolhemos dizer e ouvir com clareza.',
			'jeito_item_2_titulo'  => 'Protagonismo',
			'jeito_item_2_texto'   => 'Encaramos o problema do cliente, e da empresa, como nosso.',
			'jeito_item_3_titulo'  => 'Escuta atenta',
			'jeito_item_3_texto'   => 'Pedimos ajuda, recebemos feedback e mudamos a rota quando faz sentido.',
			'jeito_item_4_titulo'  => 'Profundidade técnica',
			'jeito_item_4_texto'   => 'Estudamos, documentamos, registramos. Aprender é parte do trabalho.',
			'jeito_item_5_titulo'  => 'Compartilhamento',
			'jeito_item_5_texto'   => 'Compartilhamos conhecimento, tempo e oportunidades.',
			'jeito_botao'          => $this->link( 'Ver vagas', '/trabalhe-conosco/#vagas' ),

			// 9. Blog.
			'tc_blog_titulo' => 'Conheça mais sobre a CLI',
		);

		// Garante que o template nomeado está atribuído à página (necessário para
		// a regra de localização do ACF: page_template == page-trabalhe-conosco.php).
		update_post_meta( $page_id, '_wp_page_template', 'page-trabalhe-conosco.php' );

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $page_id );
		}

		WP_CLI::log( sprintf( '  trabalhe-conosco: %d campos preenchidos.', count( $campos ) ) );
	}

	/**
	 * Preenche os campos ACF da página CLI Connect.
	 *
	 * @param int $pagina_id ID da página.
	 * @return void
	 */
	protected function preencher_cli_connect( $pagina_id ) {
		if ( ! $pagina_id ) {
			return;
		}

		update_post_meta( $pagina_id, '_wp_page_template', 'page-cli-connect.php' );

		$campos = array(
			// 1. Hero.
			'cc_hero_eyebrow'          => '',
			'cc_hero_titulo'           => 'Integrações que mantêm a',
			'cc_hero_titulo_destaque'  => 'sua operação em movimento',
			'cc_hero_texto'            => 'Conecte SAP, ERPs, CRMs e aplicações críticas em uma plataforma preparada para operações em tempo real. Automatize eventos de negócio com segurança, monitoramento contínuo e uma única mensalidade.',
			'cc_hero_botao'            => $this->link( 'Agende uma demonstração', '/contato/' ),
			'cc_hero_imagem'           => $this->img( 'cc-hero-orbit' ),

			// 2. Brands.
			'cc_brands_titulo' => 'Grandes empresas confiam na CLI',

			// 3. Solução.
			'cc_solucao_titulo'      => 'Tudo o que você precisa em uma única solução',

			'cc_solucao_1_imagem'    => $this->img( 'cc-solucao-1' ),
			'cc_solucao_1_titulo'    => 'Plataforma Global',
			'cc_solucao_1_texto'     => 'Licença da plataforma já inclusa para conectar sistemas com segurança, escalabilidade e tecnologia reconhecida mundialmente.',
			'cc_solucao_1_bullet_1'  => 'Licença inclusa',
			'cc_solucao_1_bullet_2'  => 'Powered by Boomi',
			'cc_solucao_1_bullet_3'  => 'Escala enterprise',

			'cc_solucao_2_imagem'    => $this->img( 'cc-solucao-2' ),
			'cc_solucao_2_titulo'    => 'Serviço Incluso',
			'cc_solucao_2_texto'     => 'Sua operação continua evoluindo após a implantação. Solicite melhorias, novos projetos e suporte contínuo dentro da mesma mensalidade.',
			'cc_solucao_2_bullet_1'  => 'Novos projetos sob demanda',
			'cc_solucao_2_bullet_2'  => 'Melhorias contínuas',
			'cc_solucao_2_bullet_3'  => 'Gestão de incidentes',

			'cc_solucao_3_imagem'    => $this->img( 'cc-solucao-3' ),
			'cc_solucao_3_titulo'    => 'Biblioteca de Integrações',
			'cc_solucao_3_texto'     => 'Comece mais rápido utilizando integrações e conectores já prontos para os principais sistemas do mercado.',
			'cc_solucao_3_bullet_1'  => 'Conectores prontos',
			'cc_solucao_3_bullet_2'  => 'Sistemas mais utilizados',
			'cc_solucao_3_bullet_3'  => 'Menor tempo de implantação',

			// 4. Implantação.
			'cc_impl_eyebrow'    => 'Implantação Rápida',
			'cc_impl_titulo'     => 'Menos horas de desenvolvimento.',
			'cc_impl_titulo_2'   => 'Mais velocidade para o negócio.',
			'cc_impl_texto'      => 'Reduza o esforço técnico necessário para integrar o SAP e implemente novos projetos com mais agilidade e qualidade.',
			'cc_impl_sem_label'  => 'Sem CLI Connect',
			'cc_impl_sem_tempo'  => '1 Mês',
			'cc_impl_sem_etapa_1' => 'Enviar solicitação',
			'cc_impl_sem_etapa_2' => 'Definir a necessidade',
			'cc_impl_sem_etapa_3' => 'Aguardar programação',
			'cc_impl_sem_etapa_4' => 'Transferência de dados',
			'cc_impl_sem_etapa_5' => 'Dados disponibilizados',
			'cc_impl_sem_etapa_6' => 'Manutenção',
			'cc_impl_sem_etapa_7' => 'Teste e QA',
			'cc_impl_com_label'  => 'Com CLI Connect',
			'cc_impl_com_tempo'  => '5 Dias',
			'cc_impl_com_etapa_1' => 'Enviar solicitação',
			'cc_impl_com_etapa_2' => 'Definir a necessidade',
			'cc_impl_com_etapa_3' => 'Dados disponibilizados',

			// 5. Boomi.
			'cc_boomi_eyebrow' => 'Plataforma global',
			'cc_boomi_titulo'  => 'Tecnologia de classe mundial com suporte para o mercado brasileiro',
			'cc_boomi_texto'   => 'Quando você contrata a CLI Connect, tem acesso à mesma plataforma que grandes empresas globais usam para integrar seus sistemas, mas com o diferencial do suporte especializado no mercado brasileiro, preço acessível e serviço gerenciado incluso.',

			// 6. Operações Críticas.
			'cc_operacoes_eyebrow'  => 'Operações Críticas',
			'cc_operacoes_titulo'   => 'Algumas integrações',
			'cc_operacoes_titulo_2' => 'simplesmente não podem falhar',
			'cc_operacoes_texto'    => 'Proteja processos críticos com integrações preparadas para operar continuamente, sem comprometer o negócio.',
			'cc_operacoes_bullet_1' => 'Processos industriais em operações contínua',
			'cc_operacoes_bullet_2' => 'Pedidos, cotações e faturamento sem falhas ou atrasos',
			'cc_operacoes_bullet_3' => 'Transações e movimentações sem interrupções',

			// 7. Dashboard (Tempo Real).
			'cc_dashboard_eyebrow' => 'Acompanhe em tempo real',
			'cc_dashboard_titulo'  => 'Acompanhe cada integração e solicite novas demandas em um só lugar',
			'cc_dashboard_texto'   => 'Tenha visibilidade do andamento dos projetos, acompanhe solicitações e envie novas demandas sempre que precisar. Tudo centralizado em um portal pensado para manter sua operação evoluindo continuamente.',
			'cc_dashboard_botao'   => array(
				'title'  => 'Agende uma demonstração',
				'url'    => '#',
				'target' => '',
			),

			// 4. Métricas.
			'cc_metrica_1_numero' => '+150',
			'cc_metrica_1_rotulo' => 'integrações por semana',
			'cc_metrica_2_numero' => '+1.000',
			'cc_metrica_2_rotulo' => 'integrações por mês',
			'cc_metrica_3_numero' => '5 dias',
			'cc_metrica_3_rotulo' => 'tempo médio de implantação',

			// 5. Destaque CTA.
			'cc_destaque_titulo' => 'A forma mais segura de conectar, evoluir e governar sua operação.',
			'cc_destaque_texto'  => 'Tenha uma operação conectada com custos previsível, suporte especializado e uma estrutura preparada para acompanhar as mudanças do seu negócio.',
			'cc_destaque_botao'  => $this->link( 'Fale conosco no Whatsapp', '/contato/' ),

			// 6. Depoimento.
			'cc_dep_foto'  => $this->img( 'cc-dep-joao' ),
			'cc_dep_nome'  => 'João Carvalho',
			'cc_dep_cargo' => 'Gerente de Vendas',
			'cc_dep_texto' => 'Com a CLI Connect, nós reestruturamos nossa governança e nossos processos financeiros.',
			'cc_dep_botao' => $this->link( 'Confira o case', '/cases/' ),

			// 8c. Diferenciais.
			'cc_dif_eyebrow' => 'NOSSOS DIFERENCIAIS',
			'cc_dif_titulo'  => 'Projetada para entregar valor contínuo',
			'cc_dif_texto'   => 'Conheça os diferenciais que tornam a CLI Connect uma alternativa mais simples e previsível para operações em crescimento.',
			'cc_dif_botao'   => $this->link( 'Agende uma demonstração', '/contato/' ),
			'cc_dif_row_1'   => 'Sem custo adicional por projeto',
			'cc_dif_row_2'   => 'Sem cobrança por execução, fluxo ou mensagem',
			'cc_dif_row_3'   => 'Expertise em sistemas do mercado brasileiro',
			'cc_dif_row_4'   => 'Plataforma líder em segurança e compliance',
			'cc_dif_row_5'   => 'Operação monitorada e gerenciada pela CLI',
			'cc_dif_row_6'   => 'Preço condizente com a realidade brasileira',
			'cc_dif_row_7'   => 'Suporte para sistemas complexos',
			'cc_dif_row_8'   => 'Biblioteca com mais de 30.000 integrações',
			'cc_dif_row_9'   => 'Atendimento humanizado e especializado',

			// 8b. Vantagens.
			'cc_vantag_eyebrow'  => 'VANTAGENS',
			'cc_vantag_titulo'   => 'Por que adotar o CLI Connect',
			'cc_vantag_texto'    => 'Do financeiro ao atendimento, crie fluxos integrados e centralize todas as informações da sua empresa em uma única tela, sem complicação.',
			'cc_vantag_1_titulo' => 'Mais Produtividade',
			'cc_vantag_1_texto'  => 'Automatize tarefas repetitivas e libere seus times para atividades mais estratégicas e de maior valor para o negócio.',
			'cc_vantag_2_titulo' => 'Mais Governança',
			'cc_vantag_2_texto'  => 'Defina exatamente o que cada agente pode acessar, responder ou executar, com aprovações humanas nos pontos críticos.',
			'cc_vantag_3_titulo' => 'Mais Segurança',
			'cc_vantag_3_texto'  => 'Reduza riscos de exposição de dados sensíveis e mantenha controle total sobre o uso da IA em toda a organização.',
			'cc_vantag_4_titulo' => 'Mais Integração',
			'cc_vantag_4_texto'  => 'Conecte agentes aos sistemas corporativos que sua empresa já utiliza — ERP, CRM, APIs e plataformas internas.',
			'cc_vantag_5_titulo' => 'Mais Velocidade',
			'cc_vantag_5_texto'  => 'Crie e coloque agentes em operação com uma abordagem visual, simples e escalável — com menor dependência de TI.',
			'cc_vantag_6_titulo' => 'Controle de Custos',
			'cc_vantag_6_texto'  => 'Monitore consumo, uso de tokens e limites de operação dos agentes para manter a IA dentro do orçamento planejado.',

			// 8. Na Prática.
			'cc_np_eyebrow'   => 'NA PRÁTICA',
			'cc_np_titulo'    => 'Converse com seus dados. O agente faz o restante.',
			'cc_np_texto'     => 'Faça perguntas, execute processos e obtenha respostas baseadas nos dados da sua operação.',
			'cc_np_bullet_1'  => 'Consulta múltiplos sistemas simultaneamente',
			'cc_np_bullet_2'  => 'Executa fluxos sem intervenção manual',
			'cc_np_bullet_3'  => 'Mantém todo o histórico da operação',
			'cc_np_imagem'    => $this->img( 'cc-na-pratica' ),

			// 7. Parceiro Oficial (Comparativo).
			'cc_parceiro_eyebrow'   => 'PARCEIRO OFICIAL',
			'cc_parceiro_titulo'    => 'Integre seus sistemas com muito menos tempo e esforço técnico',
			'cc_parceiro_texto'     => 'Com o CLI Connect você elimina a complexidade e o tempo de desenvolvimento tradicional, permitindo integrações até 5x mais rápidas que soluções baseadas no desenvolvimento tradicional.',
			'cc_parceiro_esq_titulo' => 'CLI Connect',
			'cc_parceiro_esq_sub'   => 'Integrações prontas para usar, com eventos automáticos e sem esforço de desenvolvimento',
			'cc_parceiro_esq_item_1' => 'Conectores pré-construídos e prontos para uso',
			'cc_parceiro_esq_item_2' => 'Eventos automáticos e bidirecionais',
			'cc_parceiro_esq_item_3' => 'Configuração com baixo código',
			'cc_parceiro_esq_item_4' => 'Implantação rápida e segura',
			'cc_parceiro_dir_titulo' => 'Desenvolvimento',
			'cc_parceiro_dir_sub'   => 'Abordagem tradicional com mais etapas, dependências e esforço técnico',
			'cc_parceiro_dir_item_1' => 'Desenvolvimento de código no SAP',
			'cc_parceiro_dir_item_2' => 'Testes e correções no SAP',
			'cc_parceiro_dir_item_3' => 'Dependência de recursos especialistas',
			'cc_parceiro_dir_item_4' => 'Ciclos longos de desenvolvimento',
			'cc_parceiro_destaque'  => 'Integrações feitas em até <strong>5x menos</strong> tempo. Com mais qualidade e mais segurança.',

			// 8. Pilares — eyebrow e título.
			'cc_pilares_eyebrow' => 'VANTAGENS',
			'cc_pilares_titulo'  => 'Tudo o que você precisa em uma única solução',

			// Pilar 1: Plataforma Global.
			'cc_pilar_1_imagem'  => $this->img( 'cc-pilar-1' ),
			'cc_pilar_1_titulo'  => 'Plataforma Global',
			'cc_pilar_1_texto'   => 'Licença da plataforma já inclusa para conectar sistemas com segurança, escalabilidade e tecnologia reconhecida mundialmente.',
			'cc_pilar_1_item_1'  => 'Licença inclusa',
			'cc_pilar_1_item_2'  => 'Powered by Boomi',
			'cc_pilar_1_item_3'  => 'Escala enterprise',

			// Pilar 2: Receitas de Automação.
			'cc_pilar_2_titulo' => 'Receitas de Automação',
			'cc_pilar_2_texto'  => 'Centenas de receitas prontas para os principais sistemas do mercado, acelerando suas integrações e reduzindo o tempo de desenvolvimento.',
			'cc_pilar_2_item_1' => '+500 receitas prontas',
			'cc_pilar_2_item_2' => 'Atualização contínua',
			'cc_pilar_2_item_3' => 'Expansão sob demanda',

			// Pilar 3: Serviço Gerenciado.
			'cc_pilar_3_titulo' => 'Serviço Gerenciado',
			'cc_pilar_3_texto'  => 'Nossa equipe cuida de toda a operação para você, com monitoramento 24h, governança e suporte especializado.',
			'cc_pilar_3_item_1' => 'Monitoramento 24/7',
			'cc_pilar_3_item_2' => 'SLA garantido',
			'cc_pilar_3_item_3' => 'Equipe certificada',

			// 8. AgentStudio.
			'cc_agentes_eyebrow' => 'PARCEIRO OFICIAL',
			'cc_agentes_titulo'  => 'Crie, valide e governe agentes de IA com confiança',
			'cc_agentes_texto'   => 'Uma estrutura completa para desenvolver agentes alinhados ao seu negócio, testar comportamentos antes da implantação e manter controle total da operação em escala.',

			// AgentStudio Card 1: Agent Designer.
			'cc_agente_1_titulo'  => 'Agent Designer',
			'cc_agente_1_texto'   => 'Desenvolva agentes capazes de consultar informações, analisar dados e executar ações em sistemas corporativos, com regras, objetivos e comportamentos definidos de acordo com os processos da empresa.',
			'cc_agente_1_item_1'  => 'Objetivos e responsabilidades definidos',
			'cc_agente_1_item_2'  => 'Regras e limites de atuação configuráveis',
			'cc_agente_1_item_3'  => 'Contexto alinhado ao negócio',

			// AgentStudio Card 2: Agent Garden.
			'cc_agente_2_titulo'  => 'Agent Garden',
			'cc_agente_2_texto'   => 'Teste cenários, refine instruções e verifique resultados antes da implantação. Uma etapa essencial para garantir que agentes atuem de acordo com as regras e expectativas da organização.',
			'cc_agente_2_item_1'  => 'Simulação de cenários reais',
			'cc_agente_2_item_2'  => 'Validação antes da implantação',
			'cc_agente_2_item_3'  => 'Ajustes contínuos de comportamento',

			// AgentStudio Card 3: Control Tower.
			'cc_agente_3_titulo'  => 'Control Tower',
			'cc_agente_3_texto'   => 'Acompanhe desempenho, utilização e atividades dos agentes em um único ambiente, com visibilidade completa para garantir segurança, conformidade e evolução contínua.',
			'cc_agente_3_item_1'  => 'Monitoramento centralizado',
			'cc_agente_3_item_2'  => 'Auditoria e rastreabilidade completas',
			'cc_agente_3_item_3'  => 'Visibilidade sobre uso e desempenho',

			// 9. Departamentos.
			'cc_dep_secao_titulo' => 'Automatize todos os departamentos da sua empresa',
			'cc_dep_secao_texto'  => 'Do financeiro ao atendimento, crie fluxos integrados e centralize todas as informações da sua empresa em uma única tela, sem complicação.',

			'cc_dep_1_numero' => '01',
			'cc_dep_1_titulo' => 'Conectamos seus sistemas.',
			'cc_dep_1_texto'  => 'Integramos ERP, CRM, e-commerce e plataformas fiscais em uma base única e conectada.',

			'cc_dep_2_numero' => '02',
			'cc_dep_2_titulo' => 'Automatizamos seus processos.',
			'cc_dep_2_texto'  => 'Criamos fluxos de automação que eliminam tarefas manuais e reduzem erros operacionais.',

			'cc_dep_3_numero' => '03',
			'cc_dep_3_titulo' => 'Garantimos visibilidade total.',
			'cc_dep_3_texto'  => 'Centralize dados de diferentes sistemas e tome decisões baseadas em informação confiável em tempo real.',

			// 10. Reforma Tributária.
			'cc_reforma_eyebrow'  => 'REFORMA TRIBUTÁRIA',
			'cc_reforma_titulo'   => 'A Reforma Tributária mudou as regras. Automatize e garanta vantagem competitiva',
			'cc_reforma_texto'    => 'Automatize os fluxos da sua empresa e antecipe os ajustes operacionais para a Reforma Tributária',
			'cc_reforma_1_titulo' => 'Proteja a margem do negócio contra erros de cálculo tributário',
			'cc_reforma_2_titulo' => 'Automatize obrigações acessórias sem aumentar equipe',
			'cc_reforma_3_titulo' => 'Reduza o tempo de fechamento fiscal e minimize riscos de autuação',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $pagina_id );
		}

		WP_CLI::log( sprintf( '  cli-connect: %d campos preenchidos.', count( $campos ) ) );
	}

	/**
	 * Preenche os campos ACF da página CLI Signature.
	 *
	 * @param int $pagina_id ID da página.
	 * @return void
	 */
	protected function preencher_cli_signature( $pagina_id ) {
		if ( ! $pagina_id ) {
			return;
		}

		update_post_meta( $pagina_id, '_wp_page_template', 'page-cli-signature.php' );

		$campos = array(
			// 1. Hero.
			'cs_hero_eyebrow' => 'CLI SIGNATURE',
			'cs_hero_titulo'  => 'Projetos críticos exigem mais do que execução. Exigem assinatura.',
			'cs_hero_texto'   => 'A modalidade premium da CLI Connect para empresas que operam projetos críticos com especialistas dedicados, governança executiva e acompanhamento contínuo.',
			'cs_hero_botao'   => $this->link( 'Agende uma demonstração', '/contato/' ),
			'cs_hero_bg'      => $this->img( 'cs-hero-bg' ),

			// 2. Para quem (Cenários).
			'cs_cenarios_eyebrow' => 'Quando o desafio exige mais',
			'cs_cenarios_titulo'  => 'Para quem é o CLI Signature?',
			'cs_cenarios_texto'   => 'Ideal para empresas em cenários de alta complexidade que exigem acompanhamento especializado.',

			'cs_cenarios_1_titulo' => 'Transformação Digital',
			'cs_cenarios_1_texto'  => 'Modernização de arquitetura, substituição de legados, novos canais digitais e jornadas omnichannel.',

			'cs_cenarios_2_titulo' => 'Integrações Críticas',
			'cs_cenarios_2_texto'  => 'SAP, Salesforce, Totvs, ERPs, CRMs, e-commerce, fiscal, dados, APIs e plataformas de negócio.',

			'cs_cenarios_3_titulo' => 'Ambientes Complexos',
			'cs_cenarios_3_texto'  => 'Integrações que precisam funcionar com estabilidade, rastreabilidade e suporte contínuo.',

			'cs_cenarios_4_titulo' => 'Múltiplos Stakeholders',
			'cs_cenarios_4_texto'  => 'TI, negócio, fornecedores, consultorias, squads internos e áreas executivas.',

			'cs_cenarios_5_titulo' => 'Iniciativas Estratégicas',
			'cs_cenarios_5_texto'  => 'Roadmap, priorização, gestão de riscos, arquitetura, SLA e comunicação executiva.',

			'cs_cenarios_6_titulo' => 'Operações de Missão Crítica',
			'cs_cenarios_6_texto'  => 'Processos que não podem sofrer interrupções e exigem monitoramento, governança e resposta rápida.',

			// 3. Pilares.
			'cs_pilares_eyebrow' => 'modelo enterprise',
			'cs_pilares_titulo'  => 'A experiência enterprise da CLI Connect',
			'cs_pilares_texto'   => 'O CLI Signature amplia a experiência da CLI Connect com uma camada dedicada de governança, atendimento executivo e evolução contínua para operações estratégicas.',

			'cs_pilares_1_imagem' => $this->img( 'cs-pilar-1' ),
			'cs_pilares_1_titulo' => 'Excelência Técnica CLI',
			'cs_pilares_1_texto'  => 'Especialistas em Integrações, APIs, Dados, IA, iPaaS, Salesforce, SAP, ERPs, Fiscal e plataformas corporativas.',

			'cs_pilares_2_imagem' => $this->img( 'cs-pilar-2' ),
			'cs_pilares_2_titulo' => 'Governança Executiva',
			'cs_pilares_2_texto'  => 'Rituais periódicos, indicadores, acompanhamento estratégico e evolução planejada.',

			'cs_pilares_3_imagem' => $this->img( 'cs-pilar-3' ),
			'cs_pilares_3_titulo' => 'Acompanhamento Exclusivo',
			'cs_pilares_3_texto'  => 'Gestor de Projeto/Relacionamento e Arquiteto dedicados para garantir decisões técnicas sólidas e alinhadas ao negócio.',

			// 4. Diferenciais.
			'cs_diferenciais_titulo_1' => 'Mais do que uma plataforma.',
			'cs_diferenciais_titulo_2' => 'Uma operação continuamente acompanhada.',
			'cs_diferenciais_texto'    => 'O CLI Signature amplia a experiência da CLI Connect com uma camada dedicada de governança, atendimento executivo e evolução contínua para operações estratégicas.',

			'cs_diferenciais_1_titulo' => 'Especialistas dedicados',
			'cs_diferenciais_1_texto'  => 'Profissionais acompanhando sua operação de forma próxima.',

			'cs_diferenciais_2_titulo' => 'Governança executiva',
			'cs_diferenciais_2_texto'  => 'Reuniões, indicadores e evolução planejada.',

			'cs_diferenciais_3_titulo' => 'Atendimento prioritário',
			'cs_diferenciais_3_texto'  => 'Fluxos exclusivos para demandas críticas.',

			'cs_diferenciais_4_titulo' => 'Evolução contínua',
			'cs_diferenciais_4_texto'  => 'Novas integrações e melhorias fazem parte do serviço.',

			'cs_diferenciais_5_titulo' => 'Monitoramento',
			'cs_diferenciais_5_texto'  => 'Visibilidade constante sobre toda a operação.',

			'cs_diferenciais_6_titulo' => 'Excelência operacional',
			'cs_diferenciais_6_texto'  => 'Boas práticas desde a arquitetura até a sustentação.',

			// 7. Operação Gerenciada.
			'cs_operacao_eyebrow'  => 'Operação Gerenciada',
			'cs_operacao_titulo_1' => 'Garanta uma operação contínua',
			'cs_operacao_titulo_2' => 'e preparada para evoluir',
			'cs_operacao_texto'    => 'Conte com uma operação estruturada para sustentar, monitorar e evoluir continuamente seu ambiente, com SLA acordado, governança operacional e processos definidos para garantir mais previsibilidade e eficiência.',

			'cs_operacao_1_titulo' => 'Catálogo de Serviços',
			'cs_operacao_1_texto'  => 'Tenha um atendimento estruturado com SLA acordado, priorização por criticidade e gestão organizada das demandas.',

			'cs_operacao_2_titulo' => 'Gestão de Incidentes',
			'cs_operacao_2_texto'  => 'Resolva ocorrências com agilidade, rastreabilidade e indicadores que proporcionam visibilidade sobre o atendimento.',

			'cs_operacao_3_titulo' => 'Melhorias Evolutivas',
			'cs_operacao_3_texto'  => 'Evolua continuamente seu ambiente com monitoramento operacional, melhorias planejadas e uma base de conhecimento sempre atualizada.',

			'cs_operacao_4_titulo' => 'Documentação',
			'cs_operacao_4_texto'  => 'Mantenha uma documentação técnica completa e uma base de conhecimento organizada para garantir continuidade e padronização operacional.',

			// 5. Gestor de Projeto.
			'cs_gestor_titulo'   => 'Gestor de Projeto e Relacionamento Dedicado',
			'cs_gestor_texto'    => 'Tenha um ponto focal exclusivo para acompanhar a jornada do seu cliente. Organize prioridades, conduza ritos de governança com maestria e garanta uma comunicação clara e fluida entre negócio, tecnologia e operação.',
			'cs_gestor_botao'    => $this->link( 'Agende uma demonstração', '/contato/' ),
			'cs_gestor_1_titulo' => 'Acompanhamento do Roadmap',
			'cs_gestor_2_titulo' => 'Governança de Backlog',
			'cs_gestor_3_titulo' => 'Reuniões Executivas e Operacionais',
			'cs_gestor_4_titulo' => 'Gestão de Prioridades',
			'cs_gestor_5_titulo' => 'Comunicação com Stakeholders',
			'cs_gestor_6_titulo' => 'Acompanhamento de SLA e Indicadores',
			'cs_gestor_7_titulo' => 'Plano de Evolução Contínua',

			// 6. Arquiteto Dedicado.
			'cs_arquiteto_titulo'   => 'Arquiteto Dedicado',
			'cs_arquiteto_texto'    => 'Um especialista sênior responsável por garantir que as decisões técnicas estejam alinhadas à estratégia, escalabilidade, segurança e evolução da empresa.',
			'cs_arquiteto_botao'    => $this->link( 'Agende uma demonstração', '/contato/' ),
			'cs_arquiteto_1_titulo' => 'Desenho de Arquitetura',
			'cs_arquiteto_2_titulo' => 'Revisão Técnica de Soluções',
			'cs_arquiteto_3_titulo' => 'Definição de Padrões',
			'cs_arquiteto_4_titulo' => 'Apoio em Decisões Críticas',
			'cs_arquiteto_5_titulo' => 'Estratégia de APIs e Integrações',
			'cs_arquiteto_6_titulo' => 'Avaliação de Riscos Técnicos',
			'cs_arquiteto_7_titulo' => 'Roadmap de Modernização',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $pagina_id );
		}

		WP_CLI::log( sprintf( '  cli-signature: %d campos preenchidos.', count( $campos ) ) );
	}

	/**
	 * Preenche os campos ACF da página Integração SAP.
	 *
	 * @param int $pagina_id ID da página.
	 * @return void
	 */
	protected function preencher_integracao_sap( $pagina_id ) {
		if ( ! $pagina_id ) {
			return;
		}

		update_post_meta( $pagina_id, '_wp_page_template', 'page-integracao-sap.php' );

		$campos = array(
			// 1. Hero.
			'sap_hero_titulo_azul'   => 'Expanda as capacidades do SAP',
			'sap_hero_titulo_escuro' => 'sem aumentar a complexidade da sua operação',
			'sap_hero_texto'         => 'Conecte seu SAP S/4HANA e outros sistemas críticos com uma estrutura preparada para operações complexas, eventos automáticos e evolução contínua.',
			'sap_hero_botao'         => $this->link( 'Agende uma demonstração', '/contato/' ),
			'sap_hero_imagem'        => $this->img( 'sap-hero' ),

			// 2. Velocidade.
			'sap_vel_eyebrow'    => 'otimize seu tempo',
			'sap_vel_titulo'     => 'Mais velocidade para o negócio.',
			'sap_vel_texto'      => 'Reduza o esforço técnico necessário para integrar o SAP e implemente novos projetos com muito mais agilidade.',
			'sap_vel_sem_label'  => 'SEM CLI CONNECT',
			'sap_vel_sem_tempo'  => '1 MÊS',
			'sap_vel_sem_1'      => "Enviar\nsolicitação",
			'sap_vel_sem_2'      => "Definir\na necessidade",
			'sap_vel_sem_3'      => "Aguardar\nprogramação",
			'sap_vel_sem_4'      => "Transferência\nde dados",
			'sap_vel_sem_5'      => "Dados\ndisponibilizados",
			'sap_vel_sem_6'      => 'Manutenção',
			'sap_vel_sem_7'      => 'Teste e QA',
			'sap_vel_com_label'  => 'COM CLI CONNECT',
			'sap_vel_com_tempo'  => '5 DIAS',
			'sap_vel_com_1'      => "Enviar\nsolicitação",
			'sap_vel_com_2'      => "Definir\na necessidade",
			'sap_vel_com_3'      => "Dados\ndisponibilizados",

			// 3. SAP Integrado (Conectar).
			'sap_con_eyebrow' => 'SAP INTEGRADO',
			'sap_con_titulo'  => "Seu SAP pronto para\nconectar o que vem pela frente",
			'sap_con_texto'   => 'Uma plataforma de integração que cresce com o seu negócio, permitindo conectar novos sistemas, canais e processos sem retrabalho.',
			'sap_con_imagem'  => $this->img( 'sap-conectar' ),

			// 4. SAP Sincronizado.
			'sap_sin_eyebrow' => 'SAP SINCRONIZADO',
			'sap_sin_titulo'  => "Atualizações automáticas\nsempre que algo muda no SAP",
			'sap_sin_texto'   => 'Pedidos, cadastros, estoques e outras informações são sincronizados automaticamente com os sistemas conectados, mantendo toda a operação atualizada sem processos manuais.',
			'sap_sin_imagem'  => $this->img( 'sap-sincronizar' ),

			// 5. Libere Recursos.
			'sap_rec_eyebrow'        => 'LIBERE RECURSOS',
			'sap_rec_titulo'         => 'Reduza o número de horas trabalhadas',
			'sap_rec_texto'          => 'Evite projetos extensos de desenvolvimento para conectar novos sistemas e processos.',
			'sap_rec_metrica_numero' => '65%',
			'sap_rec_metrica_label'  => 'de redução nas horas trabalhadas',
			'sap_rec_imagem'         => $this->img( 'sap-recursos' ),
			'sap_rec_imagem_overlay' => $this->img( 'sap-recursos-overlay' ),

			// 6. Depoimento.
			'sap_dep_foto'  => $this->img( 'sap-depoimento-foto' ),
			'sap_dep_nome'  => 'João Carvalho',
			'sap_dep_cargo' => 'Gerente de Vendas',
			'sap_dep_frase' => '"R$ 6 milhões economizados em horas de desenvolvimento ABAP"',
			'sap_dep_botao' => $this->link( 'Confira o case', '/cases/' ),

			// 7. Sistemas.
			'sap_sis_titulo'    => "Conecte o SAP aos sistemas\nque movem sua operação",
			'sap_sis_subtitulo' => 'Integre e governe sua operação independentemente da tecnologia utilizada',
			'sap_sis_1'         => 'CRM',
			'sap_sis_2'         => 'E-commerce',
			'sap_sis_3'         => 'Fiscal',
			'sap_sis_4'         => 'Marketplace',
			'sap_sis_5'         => 'BI',
			'sap_sis_6'         => 'Financeiro',
			'sap_sis_7'         => 'RH',
			'sap_sis_8'         => 'Sites',
			'sap_sis_9'         => 'Aplicativos',
			'sap_sis_10'        => 'Agente de IA',

			// 8. Clean Core.
			'sap_cc_eyebrow'   => 'PRESERVE SEU CLEAN CORE',
			'sap_cc_titulo'    => 'Aproveite o seu standard',
			'sap_cc_texto'     => 'Inovação sob medida com respeito absoluto ao seu núcleo. Preserve seu Clean Core e atualize seu SAP sem medo.',
			'sap_cc_1_titulo'  => 'Implantação Ágil',
			'sap_cc_1_texto'   => 'Soluções plug-and-play que conversam nativamente com seu SAP, reduzindo o tempo de setup de meses para semanas.',
			'sap_cc_1_imagem'  => $this->img( 'sap-cleancore-1' ),
			'sap_cc_2_titulo'  => 'Upgrades sem dor de cabeça',
			'sap_cc_2_texto'   => 'Atualize seu SAP para as versões mais recentes sem quebrar customizações ou paralisar sua operação.',
			'sap_cc_2_imagem'  => $this->img( 'sap-cleancore-2' ),
			'sap_cc_3_titulo'  => 'Redução do custo com manutenção',
			'sap_cc_3_texto'   => 'Elimine os gastos exorbitantes com manutenção e testes de códigos customizados ("Z") a cada novo ciclo da SAP.',
			'sap_cc_3_imagem'  => $this->img( 'sap-cleancore-3a' ),
			'sap_cc_3_imagem_b' => $this->img( 'sap-cleancore-3b' ),

			// 9. Integrações inclusas.
			'sap_int_eyebrow'   => 'INTEGRAÇÕES INCLUSAS',
			'sap_int_titulo'    => "Comece mais rápido com modelos\njá utilizados em ambientes reais",
			'sap_int_botao'     => $this->link( 'Agende uma demonstração', '/contato/' ),
			'sap_int_nota'      => 'Mais de 30.000 integrações prontas para uso',
			'sap_int_1_titulo'  => 'SAP + Salesforce',
			'sap_int_1_desc'    => 'Sincronização comercial e atendimento.',
			'sap_int_1_logo'    => $this->img( 'sap-int-salesforce' ),
			'sap_int_2_titulo'  => 'SAP + VTEX',
			'sap_int_2_desc'    => 'Pedidos, clientes, estoque e faturamento.',
			'sap_int_2_logo'    => $this->img( 'sap-int-vtex' ),
			'sap_int_3_titulo'  => 'SAP + RD Station',
			'sap_int_3_desc'    => 'Marketing e vendas alinhados.',
			'sap_int_3_logo'    => $this->img( 'sap-int-rdstation' ),
			'sap_int_4_titulo'  => 'SAP + Senior',
			'sap_int_4_desc'    => 'RH e folha sincronizados automaticamente.',
			'sap_int_4_logo'    => $this->img( 'sap-int-senior' ),
			'sap_int_5_titulo'  => 'SAP + Sankhya',
			'sap_int_5_desc'    => 'Processos entre ERPs sem retrabalho.',
			'sap_int_5_logo'    => $this->img( 'sap-int-sankhya' ),
			'sap_int_6_titulo'  => 'SAP + Thompson Reuters',
			'sap_int_6_desc'    => 'Obrigações fiscais sempre integradas.',
			'sap_int_6_logo'    => $this->img( 'sap-int-thomson' ),
			'sap_int_7_titulo'  => 'SAP + MV Saúde',
			'sap_int_7_desc'    => 'Dados clínicos e financeiros conectados.',
			'sap_int_7_logo'    => $this->img( 'sap-int-mv' ),
			'sap_int_8_titulo'  => 'SAP + Tasy',
			'sap_int_8_desc'    => 'Informações hospitalares sincronizadas.',
			'sap_int_8_logo'    => $this->img( 'sap-int-tasy' ),

			// 10. Migração ECC → S/4HANA.
			'sap_mig_titulo' => 'Sua migração para o SAP S/4HANA com risco zero e sem surpresas',
			'sap_mig_texto'  => 'O suporte ao SAP ECC termina em 2027. Planeje sua transição agora e tenha acesso aos melhores especialistas do mercado.',
			'sap_mig_botao'  => $this->link( 'Migrar agora', '/contato/' ),

			// 11. Benefícios.
			'sap_ben_eyebrow'   => 'BENEFÍCIOS',
			'sap_ben_titulo'    => 'Os benefícios da CLI Connect para o seu SAP',
			'sap_ben_botao'     => $this->link( 'Entrar em contato', '/contato/' ),
			'sap_ben_1_rotulo'  => '01 - Especialização SAP',
			'sap_ben_1_desc'    => 'Experiência em projetos envolvendo integrações com o SAP S/4HANA.',
			'sap_ben_2_rotulo'  => '02 - Serviço Gerenciado',
			'sap_ben_2_desc'    => 'Monitoramento contínuo, suporte especializado e evolução constante da sua plataforma de integração.',
			'sap_ben_3_rotulo'  => '03 - Mensalidade previsível',
			'sap_ben_3_desc'    => 'Modelo de contratação por assinatura com custos fixos e previsíveis, sem surpresas no orçamento.',
			'sap_ben_4_rotulo'  => '04 - Conectores prontos',
			'sap_ben_4_desc'    => 'Mais de 30.000 conectores prontos para uso imediato, acelerando o tempo de implantação.',
			'sap_ben_5_rotulo'  => '05 - Governança operacional',
			'sap_ben_5_desc'    => 'Visibilidade total dos fluxos de integração com rastreabilidade, alertas e gestão centralizada.',
			'sap_ben_6_rotulo'  => '06 - Plataforma líder global',
			'sap_ben_6_desc'    => 'Tecnologia Boomi — líder no Gartner Magic Quadrant para plataformas de integração.',

			// 12. Automação.
			'sap_aut_eyebrow'  => 'EVENTOS AUTOMÁTICOS',
			'sap_aut_titulo'   => "Transforme eventos do SAP\nem ações automáticas",
			'sap_aut_texto'    => "Integre o SAP à sua operação e converta eventos em execução automática,\nsem interrupções manuais.",
			'sap_aut_1_etapa1' => 'Pedido aprovado no SAP',
			'sap_aut_1_etapa2' => 'Faturamento iniciado',
			'sap_aut_1_etapa3' => 'Cliente notificado',
			'sap_aut_2_etapa1' => 'Produto atualizado',
			'sap_aut_2_etapa2' => 'Canais sincronizados',
			'sap_aut_2_etapa3' => 'Operação atualizada',
			'sap_aut_3_etapa1' => 'Estoque mínimo',
			'sap_aut_3_etapa2' => 'Fornecedor acionado',
			'sap_aut_3_etapa3' => 'Reposição iniciada',
			'sap_aut_4_etapa1' => 'Nova regulação',
			'sap_aut_4_etapa2' => 'Impactos identificados',
			'sap_aut_4_etapa3' => 'Áreas notificadas',
			'sap_aut_5_etapa1' => 'Indicador fora da meta',
			'sap_aut_5_etapa2' => 'Gestor alertado',
			'sap_aut_5_etapa3' => 'Plano de ação iniciado',

			// --- 13. FAQ --------------------------------------------------
			'sap_faq_eyebrow'    => 'FAQ',
			'sap_faq_titulo'     => 'Dúvidas Frequentes',
			'sap_faq_1_pergunta' => 'A CLI Connect funciona com ambientes SAP S/4HANA on-premises e na nuvem?',
			'sap_faq_1_resposta' => 'Sim. A plataforma da CLI Connect é compatível com ambientes SAP S/4HANA tanto on-premises quanto em nuvem (incluindo SAP BTP), garantindo flexibilidade independentemente da infraestrutura escolhida pela sua empresa.',
			'sap_faq_2_pergunta' => 'É preciso desenvolver integrações em ABAP para conectar o SAP S/4HANA?',
			'sap_faq_2_resposta' => 'Não. A CLI Connect utiliza conectores nativos e APIs padrão do SAP, eliminando a necessidade de desenvolvimento em ABAP. Isso preserva o Clean Core do seu SAP e reduz drasticamente o custo e o tempo de implantação.',
			'sap_faq_3_pergunta' => 'Quanto tempo leva para colocar a primeira integração em operação?',
			'sap_faq_3_resposta' => 'Com os modelos prontos da CLI Connect, a primeira integração pode entrar em operação em até 5 dias úteis, dependendo da complexidade do processo. Nossa equipe acompanha todo o processo de configuração e testes.',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $pagina_id );
		}

		WP_CLI::log( sprintf( '  integracao-sap: %d campos preenchidos.', count( $campos ) ) );
	}

	/**
	 * Define a imagem destacada a partir do nome base de um asset do seed.
	 *
	 * @param int    $post_id ID do post.
	 * @param string $arquivo Nome base do arquivo (sem extensão).
	 * @return void
	 */
	protected function definir_thumb( $post_id, $arquivo ) {
		$attachment_id = $this->img( $arquivo );

		if ( $attachment_id ) {
			set_post_thumbnail( $post_id, $attachment_id );
		}
	}

	/**
	 * IDs de posts de um CPT a partir de uma lista de títulos.
	 *
	 * @param string   $post_type Slug do CPT.
	 * @param string[] $titulos   Títulos exatos.
	 * @return int[]
	 */
	protected function ids_por_titulo( $post_type, $titulos ) {
		$ids = array();

		foreach ( $titulos as $titulo ) {
			$post = get_page_by_path( sanitize_title( $titulo ), OBJECT, $post_type );

			if ( $post ) {
				$ids[] = $post->ID;
			}
		}

		return $ids;
	}

	/**
	 * Preenche os campos ACF da página Contato.
	 *
	 * @param int $pagina_id ID da página.
	 * @return void
	 */
	protected function preencher_contato( $pagina_id ) {
		if ( ! $pagina_id ) {
			return;
		}

		update_post_meta( $pagina_id, '_wp_page_template', 'page-contato.php' );

		// Cria (ou recupera) o formulário CF7 — idempotente pelo slug.
		$cf7_id = $this->criar_form_cf7_contato();

		$campos = array(
			// 1. Formulário.
			// 2. Clientes.
			'ct_clientes_subtitulo' => 'Grandes empresas confiam na CLI',

			// 1. Formulário.
			'ct_form_titulo'        => 'Solicite uma proposta para sua operação',
			'ct_form_texto'         => 'Tire dúvidas, avalie possibilidades e descubra como a CLI pode apoiar sua operação com integrações, automação e IA corporativa.',
			'ct_form_email'         => 'atendimento@cliconsultoria.com.br',
			'ct_form_telefone'      => '(31) 4042-2051',
			'ct_form_facebook_url'  => 'https://www.facebook.com/cliconsultoria',
			'ct_form_instagram_url' => 'https://www.instagram.com/cliconsultoria',
			'ct_form_whatsapp_url'  => 'https://wa.me/553140422051',
			'ct_form_cf7_id'        => $cf7_id,
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $pagina_id );
		}
	}

	/**
	 * Cria o formulário Contact Form 7 da página Contato.
	 *
	 * Idempotente: se já existir um post wpcf7_contact_form com o slug
	 * "contato-cli", retorna o ID existente sem duplicar.
	 *
	 * @return int ID do post CF7, ou 0 em caso de erro.
	 */
	protected function criar_form_cf7_contato() {
		if ( ! post_type_exists( 'wpcf7_contact_form' ) ) {
			WP_CLI::warning( 'Contact Form 7 não está ativo — formulário não criado.' );
			return 0;
		}

		// Verifica se já existe.
		$existente = get_posts(
			array(
				'post_type'      => 'wpcf7_contact_form',
				'name'           => 'contato-cli',
				'posts_per_page' => 1,
				'post_status'    => 'publish',
			)
		);

		if ( $existente ) {
			return (int) $existente[0]->ID;
		}

		// Template do formulário (shortcodes CF7).
		$form_template = '<label>Nome
[text* ct-nome placeholder "Nome"]</label>

<label>Telefone
[tel* ct-telefone placeholder "Telefone"]</label>

<label>E-mail
[email* ct-email placeholder "E-mail"]</label>

<label>Mensagem
[textarea* ct-mensagem placeholder "Digite sua mensagem"]</label>

[acceptance ct-aceite] Ao enviar, concordo em receber comunicações da CLI[/acceptance]

[submit "Enviar"]';

		$cf7_id = wp_insert_post(
			array(
				'post_title'  => 'Contato CLI',
				'post_name'   => 'contato-cli',
				'post_type'   => 'wpcf7_contact_form',
				'post_status' => 'publish',
			)
		);

		if ( is_wp_error( $cf7_id ) || ! $cf7_id ) {
			WP_CLI::warning( 'Falha ao criar formulário CF7: ' . ( is_wp_error( $cf7_id ) ? $cf7_id->get_error_message() : 'erro desconhecido' ) );
			return 0;
		}

		$admin_email = (string) get_option( 'admin_email' );
		$blog_name   = (string) get_option( 'blogname' );

		update_post_meta( $cf7_id, '_form', $form_template );

		update_post_meta(
			$cf7_id,
			'_mail',
			array(
				'active'             => true,
				'recipient'          => $admin_email,
				'sender'             => $blog_name . ' <' . $admin_email . '>',
				'subject'            => '[' . $blog_name . '] Novo contato — [ct-nome]',
				'body'               => "Nome: [ct-nome]\nTelefone: [ct-telefone]\nE-mail: [ct-email]\n\nMensagem:\n[ct-mensagem]",
				'additional_headers' => 'Reply-To: [ct-email]',
				'attachments'        => '',
				'use_html'           => false,
				'exclude_blank'      => false,
			)
		);

		update_post_meta(
			$cf7_id,
			'_mail_2',
			array(
				'active'             => false,
				'recipient'          => '',
				'sender'             => '',
				'subject'            => '',
				'body'               => '',
				'additional_headers' => '',
				'attachments'        => '',
				'use_html'           => false,
				'exclude_blank'      => false,
			)
		);

		update_post_meta(
			$cf7_id,
			'_messages',
			array(
				'mail_sent_ok'               => 'Mensagem enviada com sucesso. Em breve entraremos em contato.',
				'mail_sent_ng'               => 'Ocorreu um erro. Por favor, tente novamente.',
				'validation_error'           => 'Preencha os campos obrigatórios antes de enviar.',
				'spam'                       => 'Parece que há um problema com o envio.',
				'accept_terms'               => 'É necessário aceitar os termos para continuar.',
				'invalid_required'           => 'Campo obrigatório.',
				'invalid_too_long'           => 'Texto muito longo.',
				'invalid_too_short'          => 'Texto muito curto.',
				'invalid_date'               => 'Data inválida.',
				'date_too_early'             => 'Data muito antiga.',
				'date_too_late'              => 'Data muito recente.',
				'invalid_number'             => 'Número inválido.',
				'number_too_small'           => 'Número muito pequeno.',
				'number_too_large'           => 'Número muito grande.',
				'invalid_email'              => 'E-mail inválido.',
				'invalid_url'               => 'URL inválida.',
				'invalid_tel'               => 'Telefone inválido.',
				'upload_failed'              => 'Upload falhou.',
				'upload_file_type_invalid'   => 'Tipo de arquivo inválido.',
				'upload_file_too_large'      => 'Arquivo muito grande.',
				'upload_failed_php_error'    => 'Erro ao fazer upload.',
				'upload_file_count_exceeded' => 'Muitos arquivos.',
				'quiz_answer_not_correct'    => 'Resposta incorreta.',
			)
		);

		update_post_meta( $cf7_id, '_additional_settings', '' );

		WP_CLI::log( "  Formulário CF7 criado (ID: {$cf7_id})." );

		return $cf7_id;
	}

	/**
	 * Converte um caminho relativo em URL absoluta do site.
	 *
	 * @param string $url Caminho ou URL.
	 * @return string
	 */
	protected function url_absoluta( $url ) {
		$url = (string) $url;

		return ( '/' === substr( $url, 0, 1 ) ) ? home_url( $url ) : $url;
	}

	/* =====================================================================
	   SOLUÇÕES — LANDING PAGES
	   ===================================================================== */

	/**
	 * Preenche os campos ACF do post cli_solucao "Serviços Financeiros".
	 *
	 * Landing page da indústria de serviços financeiros. Preenchida seção a
	 * seção; as ainda não preenchidas ficam vazias e, portanto, invisíveis.
	 *
	 * @return void
	 */
	protected function preencher_solucao_servicos_financeiros() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:servicos-financeiros', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  Serviços Financeiros: post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'         => 'Para serviços financeiros',
			'solucao_hero_titulo'          => 'Da implementação à produção em semanas.',
			'solucao_hero_titulo_destaque' => 'Porque bancos não esperam.',
			'solucao_hero_corpo'           => 'Conecte sistemas bancários, plataformas digitais e soluções de segurança em uma única arquitetura de integração preparada para evoluir continuamente.',
			'solucao_hero_btn1_texto'      => 'Agende uma demonstração',
			'solucao_hero_btn1_url'        => '/contato/',
			'solucao_hero_btn2_texto'      => 'Conheça a plataforma',
			'solucao_hero_btn2_url'        => '/plataforma/',
			'solucao_hero_imagem'          => $this->img( 'servicos-financeiros-hero' ),

			// 2 · Métricas.
			'solucao_metrica_1_numero'     => '95%',
			'solucao_metrica_1_rotulo'     => 'mais rápida a verificação de identidade',
			'solucao_metrica_2_numero'     => '24.000',
			'solucao_metrica_2_rotulo'     => 'horas de trabalho manual eliminadas',
			'solucao_metrica_3_numero'     => '5%',
			'solucao_metrica_3_rotulo'     => 'de aumento no NPS',

			// 3 · Pilares.
			'solucao_pilares_eyebrow'      => 'Pilares',
			'solucao_pilares_titulo'       => 'Integrações mais rápidas, seguras e inteligentes',
			'solucao_pilares_1_icone'      => $this->img( 'servicos-financeiros-pilar-1' ),
			'solucao_pilares_1_titulo'     => 'Compliance desde a arquitetura',
			'solucao_pilares_1_desc'       => 'Controle de acessos, rastreabilidade e governança para ambientes altamente regulados.',
			'solucao_pilares_2_icone'      => $this->img( 'servicos-financeiros-pilar-2' ),
			'solucao_pilares_2_titulo'     => 'Integrações que evoluem junto com o negócio',
			'solucao_pilares_2_desc'       => 'Novos fluxos, alterações e melhorias fazem parte da operação, sem iniciar um novo projeto a cada mudança.',
			'solucao_pilares_3_icone'      => $this->img( 'servicos-financeiros-pilar-3' ),
			'solucao_pilares_3_titulo'     => 'Dados preparados para automação e IA',
			'solucao_pilares_3_desc'       => 'Transforme informações dispersas em processos conectados, prontos para alimentar agentes inteligentes e análises em tempo real.',

			// 4 · Logos.
			'solucao_logos_texto'          => 'Integramos os serviços financeiros de grandes empresas',
			'solucao_logos_clientes'       => array_values(
				array_filter(
					array(
						$this->id_do_seed( 'cliente:bnp-paribas-cardif', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:hsbc', 'cli_cliente' ),
					)
				)
			),

			// 5 · Casos de Uso.
			'solucao_casos_eyebrow'        => 'Casos de uso',
			'solucao_casos_titulo'         => 'Integrações mais rápidas, seguras e inteligentes',
			'solucao_casos_1_icone'        => $this->img( 'servicos-financeiros-caso-1' ),
			'solucao_casos_1_titulo'       => 'Core Banking conectado',
			'solucao_casos_1_desc'         => 'Integre sistemas bancários a ERPs, CRMs e plataformas digitais.',
			'solucao_casos_2_icone'        => $this->img( 'servicos-financeiros-caso-2' ),
			'solucao_casos_2_titulo'       => 'Pagamentos em tempo real',
			'solucao_casos_2_desc'         => 'Automatize a troca de informações entre instituições financeiras e sistemas internos.',
			'solucao_casos_3_icone'        => $this->img( 'servicos-financeiros-caso-3' ),
			'solucao_casos_3_titulo'       => 'Prevenção à fraude',
			'solucao_casos_3_desc'         => 'Conecte motores antifraude, plataformas analíticas e canais digitais.',
			'solucao_casos_4_icone'        => $this->img( 'servicos-financeiros-caso-4' ),
			'solucao_casos_4_titulo'       => 'Crédito automatizado',
			'solucao_casos_4_desc'         => 'Orquestre validações, documentos e aprovações entre múltiplos sistemas.',
			'solucao_casos_5_icone'        => $this->img( 'servicos-financeiros-caso-5' ),
			'solucao_casos_5_titulo'       => 'Visão 360º do cliente',
			'solucao_casos_5_desc'         => 'Centralize dados financeiros, comerciais e operacionais em uma única jornada.',
			'solucao_casos_6_icone'        => $this->img( 'servicos-financeiros-caso-6' ),
			'solucao_casos_6_titulo'       => 'Dados para Inteligência Artificial',
			'solucao_casos_6_desc'         => 'Disponibilize informações confiáveis para agentes inteligentes e análises avançadas.',
			// Sem card CTA azul nesta landing — os seis cards fecham duas linhas.
			'solucao_casos_cta_texto'      => '',
			'solucao_casos_cta_url'        => '',

			// 6 · Selos.
			'solucao_selos_eyebrow'        => 'compliance & segurança',
			'solucao_selos_titulo'         => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'          => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_servicos_financeiros_faq( $post_id );

		WP_CLI::log( "  Serviços Financeiros preenchido (ID: {$post_id})." );
	}

	/**
	 * Cria os posts cli_faq de Serviços Financeiros e vincula à solução.
	 *
	 * O Figma mostra apenas as perguntas (accordion fechado); as respostas
	 * foram redigidas a partir do que a própria landing afirma e seguem
	 * pendentes de validação do cliente.
	 *
	 * @param int $post_id ID do post cli_solucao de Serviços Financeiros.
	 * @return void
	 */
	protected function preencher_servicos_financeiros_faq( $post_id ) {
		$itens = array(
			array(
				'faq:sf-fin-core-banking',
				'Quanto tempo leva para integrar um core banking?',
				'<p>Os primeiros fluxos costumam entrar em produção em semanas, não em meses. O prazo depende do core utilizado, do volume de dados e das validações de segurança exigidas, mas a implementação é feita por etapas: as integrações críticas sobem primeiro e as demais entram em ondas seguintes, sem travar a operação.</p>',
			),
			array(
				'faq:sf-fin-legados',
				'A CLI funciona com sistemas legados?',
				'<p>Sim. Além das APIs REST e SOAP, a plataforma se conecta a bancos de dados, arquivos, filas de mensageria e serviços internos que não expõem API. Para ambientes on-premises, a comunicação é feita por um agente instalado dentro da rede corporativa, sem abrir portas de entrada no firewall.</p>',
			),
			array(
				'faq:sf-fin-open-finance',
				'Como as integrações acompanham iniciativas de Open Finance?',
				'<p>As integrações são construídas sobre uma camada de APIs governada, com versionamento, controle de acessos e rastreabilidade de ponta a ponta. Isso permite expor e consumir serviços de parceiros no ritmo em que a regulação e as fases do Open Finance avançam, sem reescrever a arquitetura a cada mudança.</p>',
			),
			array(
				'faq:sf-fin-dados-ia',
				'É possível utilizar dados legados em projetos de IA?',
				'<p>Sim. As integrações normalizam e conectam informações que hoje estão dispersas entre sistemas, deixando-as em um formato confiável e rastreável. É esse conjunto tratado que alimenta agentes inteligentes, motores de decisão e análises avançadas.</p>',
			),
			array(
				'faq:sf-fin-dados-sensiveis',
				'Como a CLI protege dados sensíveis durante as integrações?',
				'<p>Os dados trafegam criptografados, as credenciais ficam em cofre e cada acesso é registrado para auditoria. A operação segue os padrões de compliance da plataforma — SOC 2, ISO 27001, PCI DSS e LGPD/GDPR, entre outros — e os fluxos são desenhados para expor apenas as informações necessárias a cada sistema.</p>',
			),
		);

		$ids = array();
		foreach ( $itens as $ordem => list( $slug, $pergunta, $resposta ) ) {
			$ids[] = (int) $this->upsert(
				$slug,
				array(
					'post_type'    => 'cli_faq',
					'post_title'   => $pergunta,
					'post_content' => $resposta,
					'menu_order'   => $ordem,
				)
			);
		}

		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $ids, $post_id );

		WP_CLI::log( sprintf( '  Serviços Financeiros FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/**
	 * Preenche os campos ACF do post cli_solucao "Manufatura".
	 *
	 * Landing page da indústria de manufatura. Preenchida seção a seção; as
	 * ainda não preenchidas ficam vazias e, portanto, invisíveis.
	 *
	 * @return void
	 */
	protected function preencher_solucao_manufatura() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:manufatura', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  Manufatura: post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'         => 'Para manufatura',
			'solucao_hero_titulo'          => 'Conecte sua fábrica do chão de produção à nuvem',
			'solucao_hero_titulo_destaque' => 'sem interromper a operação.',
			'solucao_hero_corpo'           => 'Integre SAP S/4HANA, MES, WMS, Salesforce e sistemas industriais para acelerar projetos, aumentar a visibilidade operacional e modernizar a manufatura com segurança.',
			'solucao_hero_btn1_texto'      => 'Agende uma demonstração',
			'solucao_hero_btn1_url'        => '/contato/',
			'solucao_hero_btn2_texto'      => 'Conheça a plataforma',
			'solucao_hero_btn2_url'        => '/plataforma/',
			'solucao_hero_imagem'          => $this->img( 'manufatura-hero' ),
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		WP_CLI::log( "  Manufatura preenchido (ID: {$post_id})." );
	}

	/**
	 * Preenche os campos ACF do post cli_solucao "Salesforce".
	 *
	 * @return void
	 */
	protected function preencher_solucao_salesforce() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:salesforce', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  Salesforce: post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'         => 'Para o seu Salesforce',
			'solucao_hero_titulo'          => 'Salesforce sem barreiras.',
			'solucao_hero_titulo_destaque' => 'Integração sem limites.',
			'solucao_hero_corpo'           => 'Conecte o Salesforce a qualquer ERP ou banco de dados com flexibilidade total e elimine os limites da sua arquitetura de dados.',
			'solucao_hero_btn1_texto'      => 'Agende uma demonstração',
			'solucao_hero_btn1_url'        => '/contato/',
			'solucao_hero_btn2_texto'      => 'Conheça nossa solução',
			'solucao_hero_btn2_url'        => '/solucoes/tecnologia/salesforce/',
			'solucao_hero_imagem'          => $this->img( 'salesforce-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Integrações mais rápidas, seguras e inteligentes',
			'solucao_pilares_1_icone'  => $this->img( 'salesforce-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Fluxos de aprovação automatizados',
			'solucao_pilares_1_desc'   => 'Dispare fluxos de aprovação automaticamente sempre que houver mudanças no Salesforce',
			'solucao_pilares_2_icone'  => $this->img( 'salesforce-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Operações em massa auditáveis',
			'solucao_pilares_2_desc'   => 'Execute operações em massa com rastreabilidade completa, auditoria centralizada e mais segurança para alterações em grande escala.',
			'solucao_pilares_3_icone'  => $this->img( 'salesforce-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Integração segura com ambientes internos',
			'solucao_pilares_3_desc'   => 'Conecte o Salesforce aos sistemas internos da empresa sem abrir portas no firewall, preservando a segurança da infraestrutura corporativa.',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_salesforce_casos( $post_id );
		$this->preencher_salesforce_diferencial( $post_id );
		$this->preencher_salesforce_selos( $post_id );
		$this->preencher_salesforce_plataforma( $post_id );
		$this->preencher_salesforce_aceleradores( $post_id );
		$this->preencher_salesforce_faq( $post_id );

		WP_CLI::log( "  Salesforce preenchido (ID: {$post_id})." );
	}

	/**
	 * Preenche os campos ACF da seção Diferencial Técnico do Salesforce.
	 *
	 * @param int $post_id ID do post cli_solucao do Salesforce.
	 * @return void
	 */
	protected function preencher_salesforce_diferencial( $post_id ) {
		$campos = array(
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Uma arquitetura preparada para ambientes corporativos',
			'solucao_dif_corpo'    => 'Independentemente da tecnologia utilizada pela sua empresa, a CLI Connect aplica as melhores práticas de integração para garantir segurança, governança e alta disponibilidade, respeitando as particularidades de cada sistema.',
			'solucao_dif_topico_1' => 'Suporte completo às APIs REST do Salesforce',
			'solucao_dif_topico_2' => 'Automatize eventos com a Subscription API.',
			'solucao_dif_topico_3' => 'Autentique integrações com JWT Bearer Flow.',
			'solucao_dif_imagem'   => $this->img( 'salesforce-dif' ),
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}
	}

	/**
	 * Preenche os campos ACF da seção Selos do Salesforce.
	 *
	 * @param int $post_id ID do post cli_solucao do Salesforce.
	 * @return void
	 */
	protected function preencher_salesforce_selos( $post_id ) {
		$campos = array(
			'solucao_selos_eyebrow' => 'compliance & segurança',
			'solucao_selos_titulo'  => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'   => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}
	}

	/**
	 * Preenche os campos ACF da seção Aceleradores de Integração do Salesforce.
	 *
	 * @param int $post_id ID do post cli_solucao do Salesforce.
	 * @return void
	 */
	protected function preencher_salesforce_aceleradores( $post_id ) {
		$campos = array(
			'solucao_acel_eyebrow'  => 'aceleradores de integração',
			'solucao_acel_titulo'   => 'Modelo pronto para começar',
			'solucao_acel_corpo'    => 'Utilize um modelo pronto para sincronizar clientes, produtos, pedidos ou oportunidades entre o Salesforce e o ERP.',
			'solucao_acel_topico_1' => 'Cadastro de clientes',
			'solucao_acel_topico_2' => 'Sincronização de pedidos',
			'solucao_acel_topico_3' => 'Atualização de produtos',
			'solucao_acel_topico_4' => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'  => '',
			'solucao_acel_imagem'   => $this->img( 'salesforce-aceleradores' ),
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}
	}

	/**
	 * Preenche os campos ACF da seção Plataforma Única do Salesforce.
	 *
	 * @param int $post_id ID do post cli_solucao do Salesforce.
	 * @return void
	 */
	protected function preencher_salesforce_plataforma( $post_id ) {
		$campos = array(
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Um único ambiente para conectar todo o ecossistema',
			'solucao_plat_corpo'    => 'Conecte o Salesforce aos demais sistemas da empresa em uma única plataforma e elimine integrações isoladas, processos manuais e retrabalho à medida que seu ecossistema evolui.',
			'solucao_plat_topico_1' => 'Centralize todo o seu ecossistema',
			'solucao_plat_topico_2' => 'Elimine integrações isoladas',
			'solucao_plat_topico_3' => 'Evolua sem aumentar a complexidade',
			'solucao_plat_imagem'   => $this->img( 'salesforce-plataforma' ),
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}
	}

	/**
	 * Cria os posts cli_faq do Salesforce e vincula à solução.
	 *
	 * @param int $post_id ID do post cli_solucao do Salesforce.
	 * @return void
	 */
	protected function preencher_salesforce_faq( $post_id ) {
		$itens = array(
			array(
				'faq:sf-apis',
				'Quais APIs do Salesforce são suportadas?',
				'<p>A CLI Connect suporta as principais APIs REST do Salesforce, incluindo a REST API, Bulk API, Streaming API (Push Topics) e a Subscription API (Platform Events). A escolha da API é feita de acordo com o volume de dados e a necessidade de eventos em tempo real de cada integração.</p>',
			),
			array(
				'faq:sf-firewall',
				'É possível integrar o Salesforce a um ERP on-premises sem abrir portas no firewall?',
				'<p>Sim. A CLI Connect utiliza a Boomi Atom, um agente de integração instalado dentro da rede corporativa que faz a comunicação de saída com a plataforma na nuvem. Não é necessário abrir portas de entrada no firewall, preservando totalmente a segurança da infraestrutura interna.</p>',
			),
			array(
				'faq:sf-mulesoft',
				'Como a CLI Connect se compara ao MuleSoft?',
				'<p>A CLI Connect utiliza o Boomi como plataforma de integração, que oferece uma interface low-code, modelo de preço mais previsível e menor custo de operação em comparação ao MuleSoft. Além disso, o modelo gerenciado da CLI Connect inclui a operação, o monitoramento e o suporte continuado, eliminando a necessidade de uma equipe interna dedicada à plataforma.</p>',
			),
			array(
				'faq:sf-atualizacoes',
				'As integrações continuam funcionando após atualizações do Salesforce?',
				'<p>Sim. O Salesforce mantém retrocompatibilidade nas suas APIs versionadas, e a CLI Connect acompanha cada release para garantir que as integrações permaneçam estáveis. O time de monitoramento valida os fluxos críticos a cada atualização e aciona o suporte preventivo quando necessário.</p>',
			),
			array(
				'faq:sf-produtos',
				'Quais produtos Salesforce podem ser integrados?',
				'<p>É possível integrar Sales Cloud, Marketing Cloud, Service Cloud, Revenue Cloud, Data Cloud e demais soluções da plataforma Salesforce utilizando a mesma arquitetura de integração.</p>',
			),
		);

		$ids = array();
		foreach ( $itens as $ordem => list( $slug, $pergunta, $resposta ) ) {
			$ids[] = (int) $this->upsert(
				$slug,
				array(
					'post_type'    => 'cli_faq',
					'post_title'   => $pergunta,
					'post_content' => $resposta,
					'menu_order'   => $ordem,
				)
			);
		}

		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $ids, $post_id );

		WP_CLI::log( sprintf( '  Salesforce FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/**
	 * Preenche os campos ACF da seção Casos de Uso do Salesforce.
	 *
	 * Chamado dentro de preencher_solucao_salesforce().
	 *
	 * @param int $post_id ID do post cli_solucao do Salesforce.
	 * @return void
	 */
	protected function preencher_salesforce_casos( $post_id ) {
		$campos = array(
			// 3 · Casos de Uso.
			'solucao_casos_eyebrow' => 'casos de uso',
			'solucao_casos_titulo'  => 'Integrações mais rápidas, seguras e inteligentes',

			'solucao_casos_1_icone'  => $this->img( 'salesforce-caso-1' ),
			'solucao_casos_1_titulo' => 'Lead-to-Quote',
			'solucao_casos_1_desc'   => 'Automatize o processo desde a geração do lead até a criação da proposta comercial, conectando o Salesforce às ferramentas responsáveis pela qualificação, aprovação e vendas.',

			'solucao_casos_2_icone'  => $this->img( 'salesforce-caso-2' ),
			'solucao_casos_2_titulo' => 'Sincronização de pedidos',
			'solucao_casos_2_desc'   => 'Mantenha pedidos, clientes e produtos sincronizados entre o Salesforce e ERPs como SAP ou NetSuite por meio de integrações em tempo real ou programadas.',

			'solucao_casos_3_icone'  => $this->img( 'salesforce-caso-3' ),
			'solucao_casos_3_titulo' => 'Hub para múltiplas organizações Salesforce',
			'solucao_casos_3_desc'   => 'Centralize integrações de diferentes ambientes Salesforce em uma única arquitetura, simplificando a governança e reduzindo a complexidade operacional.',

			'solucao_casos_4_icone'  => $this->img( 'salesforce-caso-4' ),
			'solucao_casos_4_titulo' => 'Audiências para Marketing',
			'solucao_casos_4_desc'   => 'Compartilhe segmentos e públicos automaticamente entre o Salesforce e plataformas de marketing, mantendo campanhas sempre atualizadas.',

			'solucao_casos_5_icone'  => $this->img( 'salesforce-caso-5' ),
			'solucao_casos_5_titulo' => 'Integração com Data Warehouse',
			'solucao_casos_5_desc'   => 'Envie informações do Salesforce para plataformas analíticas como Snowflake e BigQuery para consolidar indicadores e apoiar decisões baseadas em dados.',

			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}
	}
}

WP_CLI::add_command( 'cliconnect seed', 'Cliconnect_Seed' );
