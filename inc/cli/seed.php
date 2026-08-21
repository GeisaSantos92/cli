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

		WP_CLI::log( '— Sincronizando home EN (Polylang)…' );
		$this->sincronizar_polylang_front( $paginas['home'] );

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

		WP_CLI::log( '— Preenchendo Salesforce Sales Cloud…' );
		$this->preencher_solucao_salesforce_sales_cloud();

		WP_CLI::log( '— Preenchendo SAP…' );
		$this->preencher_solucao_sap();

		WP_CLI::log( '— Preenchendo TOTVS Protheus…' );
		$this->preencher_solucao_totvs();

		WP_CLI::log( '— Preenchendo TOTVS Datasul…' );
		$this->preencher_solucao_datasul();

		WP_CLI::log( '— Preenchendo TOTVS Winthor…' );
		$this->preencher_solucao_winthor();

		WP_CLI::log( '— Preenchendo TOTVS Logix…' );
		$this->preencher_solucao_logix();

		WP_CLI::log( '— Preenchendo Senior…' );
		$this->preencher_solucao_senior();

		WP_CLI::log( '— Preenchendo Sankhya…' );
		$this->preencher_solucao_sankhya();

		WP_CLI::log( '— Preenchendo Salesforce Service Cloud…' );
		$this->preencher_solucao_salesforce_service_cloud();

		WP_CLI::log( '— Preenchendo Salesforce Marketing Cloud…' );
		$this->preencher_solucao_salesforce_marketing_cloud();

		WP_CLI::log( '— Preenchendo Microsoft Dynamics 365…' );
		$this->preencher_solucao_dynamics365();

		WP_CLI::log( '— Preenchendo RD Station CRM…' );
		$this->preencher_solucao_rd_station();

		WP_CLI::log( '— Preenchendo RD Station Marketing…' );
		$this->preencher_solucao_rd_station_marketing();
		$this->preencher_solucao_thomson_reuters_tax_one();
		$this->preencher_solucao_freshservice();
		$this->preencher_solucao_servicenow();
		$this->preencher_solucao_portal_de_api();
		$this->preencher_solucao_zendesk();
		$this->preencher_solucao_bionexo();
		$this->preencher_solucao_tasy();
		$this->preencher_solucao_mv();

		WP_CLI::log( '— Preenchendo VTEX…' );
		$this->preencher_solucao_vtex();

		WP_CLI::log( '— Preenchendo Shopify…' );
		$this->preencher_solucao_shopify();

		WP_CLI::log( '— Preenchendo Magento / Adobe Commerce…' );
		$this->preencher_solucao_magento();

		WP_CLI::log( '— Preenchendo OnBlox (WMS/TMS)…' );
		$this->preencher_solucao_onblox();

		WP_CLI::log( '— Preenchendo Narwal (Comex)…' );
		$this->preencher_solucao_narwal();

		WP_CLI::log( '— Preenchendo Neogrid…' );
		$this->preencher_solucao_neogrid();

		WP_CLI::log( '— Preenchendo Target Sistemas (ERP Distribuição)…' );
		$this->preencher_solucao_target_sistemas();

		WP_CLI::log( '— Preenchendo SAP Business One…' );
		$this->preencher_solucao_sap_business_one();

		WP_CLI::log( '— Preenchendo SAP ECC…' );
		$this->preencher_solucao_sap_ecc();

		WP_CLI::log( '— Preenchendo Oracle NetSuite…' );
		$this->preencher_solucao_oracle_netsuite();

		WP_CLI::log( '— Preenchendo TOTVS Consinco…' );
		$this->preencher_solucao_totvs_consinco();

		WP_CLI::log( '— Preenchendo TOTVS Linx…' );
		$this->preencher_solucao_totvs_linx();

		WP_CLI::log( '— Preenchendo TOTVS RM…' );
		$this->preencher_solucao_totvs_rm();

		WP_CLI::log( '— Preenchendo Arius ERP…' );
		$this->preencher_solucao_arius_erp();

		WP_CLI::log( '— Preenchendo CISS Poder ERP…' );
		$this->preencher_solucao_ciss_poder_erp();

		WP_CLI::log( '— Preenchendo IFS Cloud…' );
		$this->preencher_solucao_ifs_cloud();

		WP_CLI::log( '— Preenchendo QAD Redzone…' );
		$this->preencher_solucao_qad_redzone();

		WP_CLI::log( '— Preenchendo RP Info…' );
		$this->preencher_solucao_rp_info();

		WP_CLI::log( '— Preenchendo Viasoft…' );
		$this->preencher_solucao_viasoft();

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

		// Registrar a home PT no Polylang (sem copiar meta — feito depois do preencher_home).
		$this->registrar_polylang_front( $ids['home'] );

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
			'faq_eyebrow' => 'FAQ',
			'faq_titulo'  => 'Dúvidas Frequentes',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $home_id );
		}

		// FAQ — itens específicos da home.
		$faq_home = array(
			array( 'faq:home-o-que-faz',       'O que exatamente o CLI Connect faz?',                     '<p>O CLI Connect é uma plataforma de integração empresarial que conecta ERPs, CRMs, e-commerces e demais sistemas corporativos. Utilizamos a tecnologia da Boomi para criar, monitorar e manter fluxos de dados seguros, escaláveis e auditáveis entre os sistemas da sua empresa.</p>' ),
			array( 'faq:home-quanto-tempo',     'Quanto tempo demora o serviço?',                          '<p>O tempo varia conforme a complexidade das integrações. Projetos simples podem entrar em produção em poucas semanas; cenários mais complexos, com múltiplos sistemas e regras de negócio, podem levar alguns meses. Durante o diagnóstico inicial apresentamos um cronograma realista para o seu caso.</p>' ),
			array( 'faq:home-algo-parar',       'E se algo parar de funcionar?',                           '<p>Nossa equipe monitora as integrações continuamente. Em caso de falha, abrimos um chamado automaticamente e acionamos o time de suporte antes mesmo de você perceber o problema. Você também pode acionar o suporte a qualquer momento pelos nossos canais de atendimento.</p>' ),
			array( 'faq:home-dependencia',      'Vou depender da CLI para tudo?',                          '<p>Não. As integrações são construídas sobre a plataforma Boomi, que é de sua propriedade. A CLI Connect cuida da operação, evolução e suporte — mas você tem acesso ao ambiente e pode acionar outros parceiros Boomi se desejar. Nosso modelo é de parceria, não de lock-in.</p>' ),
			array( 'faq:home-contratacao',      'Como funciona o modelo de contratação?',                  '<p>Trabalhamos com projetos de implantação (escopo fechado) e contratos de serviço gerenciado (mensalidade por ambiente monitorado). O modelo mais adequado depende do seu momento: novos clientes geralmente começam pela implantação e evoluem para o serviço gerenciado após o go-live.</p>' ),
			array( 'faq:home-criar-integracoes', 'Posso criar minhas próprias integrações na CLI Connect?', '<p>Sim. A plataforma Boomi permite que times internos criem e editem integrações. A CLI Connect pode treinar sua equipe, fazer revisões de código e assumir a operação quando necessário. Muitos clientes optam por um modelo híbrido, onde desenvolvem internamente e contam com a CLI para suporte e monitoramento.</p>' ),
		);
		$faq_ids = array();
		foreach ( $faq_home as $ordem => list( $slug, $pergunta, $resposta ) ) {
			$faq_ids[] = (int) $this->upsert( $slug, array(
				'post_type'    => 'cli_faq',
				'post_title'   => $pergunta,
				'post_content' => $resposta,
				'menu_order'   => $ordem,
			) );
		}
		update_field( 'faq_itens', $faq_ids, $home_id );

		WP_CLI::log( sprintf( '  home: %d campos preenchidos, %d FAQs vinculados.', count( $campos ), count( $faq_ids ) ) );
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
					'salesforce-sales-cloud'         => 'Salesforce Sales Cloud',
					'salesforce-service-cloud'       => 'Salesforce Service Cloud',
				'salesforce-marketing-cloud'     => 'Salesforce Marketing Cloud',
					'totvs-protheus'                 => 'TOTVS Protheus',
					'totvs-datasul'                  => 'TOTVS Datasul',
					'totvs-winthor'                  => 'TOTVS Winthor',
					'totvs-logix'                    => 'TOTVS Logix',
					'sankhya'                        => 'Sankhya',
					'senior'                         => 'Senior',
					'dynamics-365'                   => 'Dynamics 365',
					'rd-station-crm'                 => 'RD Station CRM',
					'rd-station-marketing'           => 'RD Station Marketing',
					'thomson-reuters-tax-one'        => 'Thomson Reuters Tax One',
					'freshservice'                   => 'Freshservice',
					'servicenow'                     => 'ServiceNow',
					'portal-de-api'                  => 'Portal de API / MCP Server',
				'zendesk'                        => 'Zendesk',
				'bionexo'                        => 'Bionexo',
				'tasy'                           => 'Tasy',
				'mv'                             => 'MV',
				'vtex'                           => 'VTEX',
				'shopify'                        => 'Shopify',
				'magento'                        => 'Magento / Adobe Commerce',
				'onblox'                         => 'OnBlox (WMS/TMS)',
				'narwal'                         => 'Narwal (Comex)',
				'neogrid'                        => 'Neogrid',
				'target-sistemas'                => 'Target Sistemas (ERP Distribuição)',
				'sap-business-one'               => 'SAP Business One',
				'sap-ecc'                        => 'SAP ECC',
				'oracle-netsuite'                => 'Oracle NetSuite',
				'totvs-consinco'                 => 'TOTVS Consinco',
				'totvs-linx'                     => 'TOTVS Linx',
				'totvs-rm'                       => 'TOTVS RM',
				'arius-erp'                      => 'Arius ERP',
				'ciss-poder-erp'                 => 'CISS Poder ERP',
				'ifs-cloud'                      => 'IFS Cloud',
				'qad-redzone'                    => 'QAD Redzone',
				'rp-info'                        => 'RP Info',
				'viasoft'                        => 'Viasoft',
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
		 * Usado apenas para cabeçalhos de categoria (Tecnologia, Indústria, etc.).
		 */
		$turl = function ( $chave ) use ( $termos_solucao, $solucoes_base ) {
			if ( empty( $termos_solucao[ $chave ] ) ) {
				return $solucoes_base;
			}
			$link = get_term_link( (int) $termos_solucao[ $chave ], 'cli_categoria_solucao' );
			return is_wp_error( $link ) ? $solucoes_base : $link;
		};

		/*
		 * Helper: retorna o permalink do post cli_solucao pelo slug do seed.
		 * Usado para itens folha — aponta para a landing page real.
		 */
		$purl = function ( $chave ) use ( $solucoes_base ) {
			$posts = get_posts( array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,       // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:' . $chave, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			) );
			if ( ! $posts ) {
				return $solucoes_base;
			}
			$link = get_permalink( (int) $posts[0] );
			return $link ?: $solucoes_base;
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
					'Claude'                     => $purl( 'claude' ),
					'ChatGPT'                    => $purl( 'chatgpt' ),
					'SAP'                        => $purl( 'sap' ),
					'Salesforce'                 => $purl( 'salesforce' ),
					'Salesforce Sales Cloud'     => $purl( 'salesforce-sales-cloud' ),
					'Salesforce Service Cloud'   => $purl( 'salesforce-service-cloud' ),
					'Salesforce Marketing Cloud' => $purl( 'salesforce-marketing-cloud' ),
					'TOTVS Protheus'             => $purl( 'totvs-protheus' ),
					'TOTVS Datasul'              => $purl( 'totvs-datasul' ),
					'TOTVS Winthor'              => $purl( 'totvs-winthor' ),
					'TOTVS Logix'               => $purl( 'totvs-logix' ),
					'Sankhya'                    => $purl( 'sankhya' ),
					'Senior'                     => $purl( 'senior' ),
					'Dynamics 365'               => $purl( 'dynamics-365' ),
				),
			),
			array(
				'titulo' => 'Indústria',
				'url'    => $turl( 'industria' ),
				'filhos' => array(
					'Serviços Financeiros' => $purl( 'servicos-financeiros' ),
					'Manufatura'           => $purl( 'manufatura' ),
					'Logística (3PL)'      => $purl( 'logistica-3pl' ),
					'Software (ISV)'       => $purl( 'software-isv' ),
					'Varejo'               => $purl( 'varejo' ),
					'Hotelaria e Turismo'  => $purl( 'hotelaria-e-turismo' ),
					'Seguros'              => $purl( 'seguros' ),
				),
			),
			array(
				'titulo' => 'Departamento',
				'url'    => $turl( 'departamento' ),
				'filhos' => array(
					'Recursos Humanos (RH)'         => $purl( 'recursos-humanos-rh' ),
					'Operações de Receita (RevOps)' => $purl( 'operacoes-de-receita-revops' ),
					'Marketing'                     => $purl( 'marketing' ),
					'Financeiro'                    => $purl( 'financeiro' ),
				),
			),
			array(
				'titulo' => 'Nuvem',
				'url'    => $turl( 'nuvem' ),
				'filhos' => array(
					'AWS'          => $purl( 'aws' ),
					'Google Cloud' => $purl( 'google-cloud' ),
					'Azure'        => $purl( 'azure' ),
				),
			),
			array(
				'titulo' => 'Por Iniciativa',
				'url'    => $turl( 'por-iniciativa' ),
				'filhos' => array(
					'Atualização de Sistemas Legados' => $purl( 'atualizacao-de-sistemas-legados' ),
					'Pedido ao Recebimento'           => $purl( 'pedido-ao-recebimento' ),
					'IA Corporativa'                  => $purl( 'ia-corporativa' ),
					'Compras ao Pagamento'            => $purl( 'compras-ao-pagamento' ),
					'Jornada do Colaborador'          => $purl( 'jornada-do-colaborador' ),
					'Soberania de Dados'              => $purl( 'soberania-de-dados' ),
					'Visão 360° do Cliente'           => $purl( 'visao-360-do-cliente' ),
					'Modernização de ERP'             => $purl( 'modernizacao-de-erp' ),
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
								'Claude'                     => $purl( 'claude' ),
								'ChatGPT'                    => $purl( 'chatgpt' ),
								'SAP'                        => $purl( 'sap' ),
								'Salesforce'                 => $purl( 'salesforce' ),
								'Salesforce Sales Cloud'     => $purl( 'salesforce-sales-cloud' ),
								'Salesforce Service Cloud'   => $purl( 'salesforce-service-cloud' ),
								'Salesforce Marketing Cloud' => $purl( 'salesforce-marketing-cloud' ),
								'TOTVS Protheus'             => $purl( 'totvs-protheus' ),
								'Sankhya'                    => $purl( 'sankhya' ),
								'Senior'                     => $purl( 'senior' ),
								'Dynamics 365'               => $purl( 'dynamics-365' ),
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
								'Serviços Financeiros' => $purl( 'servicos-financeiros' ),
								'Manufatura'           => $purl( 'manufatura' ),
								'Logística (3PL)'      => $purl( 'logistica-3pl' ),
								'Software (ISV)'       => $purl( 'software-isv' ),
								'Varejo'               => $purl( 'varejo' ),
								'Hotelaria e Turismo'  => $purl( 'hotelaria-e-turismo' ),
								'Seguros'              => $purl( 'seguros' ),
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
								'Recursos Humanos (RH)'         => $purl( 'recursos-humanos-rh' ),
								'Operações de Receita (RevOps)' => $purl( 'operacoes-de-receita-revops' ),
								'Marketing'                     => $purl( 'marketing' ),
								'Financeiro'                    => $purl( 'financeiro' ),
							),
						),
						array(
							'titulo' => 'Nuvem',
							'url'    => $turl( 'nuvem' ),
							'filhos' => array(
								'AWS'          => $purl( 'aws' ),
								'Google Cloud' => $purl( 'google-cloud' ),
								'Azure'        => $purl( 'azure' ),
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
								'Atualização de Sistemas Legados' => $purl( 'atualizacao-de-sistemas-legados' ),
								'IA Corporativa'                  => $purl( 'ia-corporativa' ),
								'Compras ao Pagamento'            => $purl( 'compras-ao-pagamento' ),
								'Pedido ao Recebimento'           => $purl( 'pedido-ao-recebimento' ),
								'Jornada do Colaborador'          => $purl( 'jornada-do-colaborador' ),
								'Soberania de Dados'              => $purl( 'soberania-de-dados' ),
								'Visão 360° do Cliente'           => $purl( 'visao-360-do-cliente' ),
								'Modernização de ERP'             => $purl( 'modernizacao-de-erp' ),
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

	/**
	 * Registra a home PT no Polylang: atualiza page_on_front.pt e vincula
	 * a tradução EN. Chamado de criar_paginas() — sem copiar meta ainda.
	 *
	 * @param int $pt_id ID da home em PT.
	 * @return void
	 */
	protected function registrar_polylang_front( $pt_id ) {
		if ( ! function_exists( 'pll_save_post_translations' ) ) {
			return;
		}

		$opcoes = get_option( 'polylang' );
		if ( ! is_array( $opcoes ) ) {
			return;
		}

		$opcoes['page_on_front']['pt'] = (int) $pt_id;

		$en_id = 0;
		if ( ! empty( $opcoes['page_on_front']['en'] ) ) {
			$candidate = (int) $opcoes['page_on_front']['en'];
			if ( get_post_status( $candidate ) ) {
				$en_id = $candidate;
			}
		}

		update_option( 'polylang', $opcoes );

		if ( $en_id ) {
			pll_save_post_translations( array( 'pt' => (int) $pt_id, 'en' => $en_id ) );
		}
	}

	/**
	 * Copia os meta ACF da home PT para a home EN após preencher_home().
	 * Garante que a versão EN tenha o mesmo conteúdo que a PT.
	 *
	 * @param int $pt_id ID da home em PT.
	 * @return void
	 */
	protected function sincronizar_polylang_front( $pt_id ) {
		if ( ! function_exists( 'pll_get_post_language' ) ) {
			return;
		}

		$opcoes = get_option( 'polylang' );
		$en_id  = ! empty( $opcoes['page_on_front']['en'] ) ? (int) $opcoes['page_on_front']['en'] : 0;

		if ( ! $en_id || ! get_post_status( $en_id ) ) {
			WP_CLI::warning( '  Polylang: home EN não encontrada; conteúdo EN não sincronizado.' );
			return;
		}

		$skip = array( '_pll_translations', '_pll_language', '_thumbnail_id' );
		$meta = get_post_meta( $pt_id );
		foreach ( $meta as $key => $values ) {
			if ( in_array( $key, $skip, true ) || strpos( $key, 'pll_' ) === 0 ) {
				continue;
			}
			delete_post_meta( $en_id, $key );
			foreach ( $values as $val ) {
				add_post_meta( $en_id, $key, maybe_unserialize( $val ) );
			}
		}

		WP_CLI::log( "  Polylang home EN={$en_id}: meta copiadas de PT={$pt_id}." );
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

		// FAQ — 5 perguntas específicas da página CLI Connect.
		$faq_slugs = array(
			'faq:home-o-que-faz',
			'faq:home-quanto-tempo',
			'faq:home-algo-parar',
			'faq:home-dependencia',
			'faq:home-contratacao',
		);
		$faq_ids = array();
		foreach ( $faq_slugs as $slug ) {
			$post = get_posts( array(
				'post_type'      => 'cli_faq',
				'posts_per_page' => 1,
				'meta_key'       => self::META,
				'meta_value'     => $slug,
				'fields'         => 'ids',
			) );
			if ( $post ) {
				$faq_ids[] = (int) $post[0];
			}
		}
		if ( $faq_ids ) {
			update_field( 'faq_itens', $faq_ids, $pagina_id );
		}

		WP_CLI::log( sprintf( '  cli-connect: %d campos preenchidos, %d FAQs vinculados.', count( $campos ), count( $faq_ids ) ) );
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

	protected function preencher_solucao_salesforce_sales_cloud() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:salesforce-sales-cloud', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  Salesforce Sales Cloud: post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu Salesforce Sales Cloud',
			'solucao_hero_titulo'     => 'Conecte o Salesforce Sales Cloud ao ERP e acelere todo o ciclo de vendas',
			'solucao_hero_corpo'      => 'Automatize a jornada do lead ao faturamento integrando o Sales Cloud com ERP, CPQ, financeiro e demais sistemas da empresa, eliminando retrabalho e garantindo dados sincronizados em cada etapa da venda.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucoes/tecnologia/salesforce-sales-cloud/',
			'solucao_hero_imagem'     => $this->img( 'salesforce-sc-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Conecte toda a jornada comercial',
			'solucao_pilares_1_icone'  => $this->img( 'salesforce-sc-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Automatize do lead ao faturamento',
			'solucao_pilares_1_desc'   => 'Conecte marketing, CRM, CPQ, ERP e financeiro para transformar oportunidades em pedidos sem processos manuais.',
			'solucao_pilares_2_icone'  => $this->img( 'salesforce-sc-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Sincronize vendas e ERP',
			'solucao_pilares_2_desc'   => 'Mantenha oportunidades, contas, clientes e pedidos atualizados entre Salesforce e ERP de forma bidirecional.',
			'solucao_pilares_3_icone'  => $this->img( 'salesforce-sc-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Acione processos em tempo real',
			'solucao_pilares_3_desc'   => 'Dispare aprovações, notificações e automações imediatamente sempre que um registro importante for alterado no Salesforce.',

			// 3 · Casos de uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize os principais processos do Sales Cloud',
			'solucao_casos_1_icone'   => $this->img( 'salesforce-sc-caso-1' ),
			'solucao_casos_1_titulo'  => 'Automatize o processo Lead-to-Quote',
			'solucao_casos_1_desc'    => 'Conecte marketing, Sales Cloud e CPQ para acelerar a geração de propostas e reduzir etapas manuais.',
			'solucao_casos_2_icone'   => $this->img( 'salesforce-sc-caso-2' ),
			'solucao_casos_2_titulo'  => 'Sincronize pedidos com o ERP',
			'solucao_casos_2_desc'    => 'Atualize pedidos do ERP automaticamente no Sales Cloud em processos agendados ou em tempo real.',
			'solucao_casos_3_icone'   => $this->img( 'salesforce-sc-caso-3' ),
			'solucao_casos_3_titulo'  => 'Gere pedidos automaticamente',
			'solucao_casos_3_desc'    => 'Transforme oportunidades ganhas em pedidos no ERP sem redigitação ou intervenção operacional.',
			'solucao_casos_4_icone'   => $this->img( 'salesforce-sc-caso-4' ),
			'solucao_casos_4_titulo'  => 'Conecte múltiplas organizações Salesforce',
			'solucao_casos_4_desc'    => 'Centralize dados entre diferentes instâncias Salesforce mantendo informações comerciais sincronizadas.',
			'solucao_casos_5_icone'   => $this->img( 'salesforce-sc-caso-5' ),
			'solucao_casos_5_titulo'  => 'Receba alertas de oportunidades',
			'solucao_casos_5_desc'    => 'Dispare notificações e processos sempre que oportunidades mudarem de estágio durante a negociação.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',

			// 4 · Selos.
			'solucao_selos_eyebrow' => 'compliance & segurança',
			'solucao_selos_titulo'  => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'   => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',

			// 5 · Diferencial técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações preparadas para produção Salesforce',
			'solucao_dif_corpo'    => 'Utilize todas as principais operações da REST API, eventos em tempo real e autenticação segura para construir integrações escaláveis sem comprometer a arquitetura do Salesforce.',
			'solucao_dif_topico_1' => 'Utilize APIs oficiais do Salesforce.',
			'solucao_dif_topico_2' => 'Automatize eventos em tempo real.',
			'solucao_dif_topico_3' => 'Proteja conexões com JWT Bearer Flow.',
			'solucao_dif_imagem'   => $this->img( 'salesforce-sc-dif' ),
		);
		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		// 6 · Plataforma única.
		update_field( 'solucao_plat_eyebrow',  'plataforma única', $post_id );
		update_field( 'solucao_plat_titulo',   'Centralize toda a jornada comercial em uma plataforma', $post_id );
		update_field( 'solucao_plat_corpo',    'O Sales Cloud depende de ERP, CPQ e faturamento para concluir uma venda. Centralize todas essas integrações em uma única plataforma para reduzir custos, simplificar a arquitetura e acelerar novas automações.', $post_id );
		update_field( 'solucao_plat_topico_1', 'Centralize integrações do ciclo comercial.', $post_id );
		update_field( 'solucao_plat_topico_2', 'Reutilize fluxos entre diferentes projetos.', $post_id );
		update_field( 'solucao_plat_topico_3', 'Reduza dependência de desenvolvimentos específicos.', $post_id );
		update_field( 'solucao_plat_imagem',   $this->img( 'salesforce-sc-plataforma' ), $post_id );

		// 7 · Aceleradores.
		update_field( 'solucao_acel_eyebrow',  'Aceleradores de integração', $post_id );
		update_field( 'solucao_acel_titulo',   'Comece com automações já estruturadas', $post_id );
		update_field( 'solucao_acel_corpo',    'Utilize um modelo pronto para automatizar toda a jornada entre Sales Cloud, CPQ e ERP, reduzindo o tempo de implantação e acelerando a entrega de valor.', $post_id );
		update_field( 'solucao_acel_topico_1', 'Implante integrações em poucos dias.', $post_id );
		update_field( 'solucao_acel_topico_2', 'Reutilize modelos já validados.', $post_id );
		update_field( 'solucao_acel_topico_3', 'Adapte fluxos ao seu processo comercial.', $post_id );
		update_field( 'solucao_acel_topico_4', 'E muito mais...', $post_id );
		update_field( 'solucao_acel_btn_texto', 'Começar agora', $post_id );
		update_field( 'solucao_acel_btn_url',   '/contato/', $post_id );
		update_field( 'solucao_acel_imagem',   $this->img( 'salesforce-sc-aceleradores' ), $post_id );

		// 8 · FAQ.
		$this->preencher_salesforce_sc_faq( $post_id );

		WP_CLI::log( "  Salesforce Sales Cloud preenchido (ID: {$post_id})." );
	}

	protected function preencher_salesforce_sc_faq( $post_id ) {
		$itens = array(
			array(
				'faq:sc-oportunidades-tempo-real',
				'Como funciona a sincronização de oportunidades em tempo real?',
				'<p>A CLI Connect utiliza a Subscription API (Platform Events / Change Data Capture) do Salesforce para capturar alterações em oportunidades no momento em que ocorrem. Assim que um registro é atualizado no Sales Cloud, o evento é processado e os dados são propagados para o ERP ou sistema destino sem polling manual.</p>',
			),
			array(
				'faq:sc-multiplas-orgs',
				'É possível conectar múltiplas organizações Salesforce à mesma integração?',
				'<p>Sim. A CLI Connect suporta múltiplas orgs Salesforce em um único projeto de integração. Cada organização é configurada como uma conexão independente, permitindo centralizar fluxos de dados entre diferentes instâncias de Sales Cloud e os sistemas corporativos sem duplicar arquiteturas.</p>',
			),
			array(
				'faq:sc-vs-mulesoft',
				'Como a CLI Connect se compara ao MuleSoft para integrar o Sales Cloud?',
				'<p>A CLI Connect é uma alternativa mais acessível e ágil para integrar o Sales Cloud a ERPs e sistemas corporativos. Enquanto o MuleSoft exige equipes especializadas e ciclos longos de implementação, a CLI Connect oferece aceleradores prontos, implantação mais rápida e custo total de propriedade reduzido — mantendo governança, segurança e escalabilidade enterprise.</p>',
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
		update_field( 'solucao_faq_itens',  $ids, $post_id );
		WP_CLI::log( sprintf( '  Salesforce Sales Cloud FAQ: %d perguntas vinculadas.', count( $ids ) ) );
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

	/* =====================================================================
	   Salesforce Service Cloud
	   ===================================================================== */

	protected function preencher_solucao_salesforce_service_cloud() {
		$post_id = $this->upsert( 'solucao:salesforce-service-cloud', array(
			'post_type'  => 'cli_solucao',
			'post_title' => 'Salesforce Service Cloud',
			'post_status' => 'publish',
		) );

		$campos = array(
			// 1 · Hero
			'solucao_hero_eyebrow'    => 'para o seu Salesforce Service Cloud',
			'solucao_hero_titulo'     => 'Conecte o Salesforce Service Cloud e entregue atendimento com contexto completo',
			'solucao_hero_corpo'      => 'Integre o Service Cloud ao ERP, faturamento, field service e canais de atendimento para que sua equipe resolva chamados mais rapidamente, sem alternar entre sistemas.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucoes/tecnologia/salesforce-service-cloud/',
			'solucao_hero_imagem'     => $this->img( 'salesforce-svc-hero' ),
			// 2 · Pilares
			'solucao_pilares_titulo'   => 'Conecte toda a operação de atendimento',
			'solucao_pilares_1_icone'  => $this->img( 'salesforce-svc-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Enriqueça cada atendimento',
			'solucao_pilares_1_desc'   => 'Disponibilize dados de pedidos, faturamento e histórico do cliente diretamente no caso, sem depender de consultas em outros sistemas.',
			'solucao_pilares_2_icone'  => $this->img( 'salesforce-svc-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Orquestre todos os canais',
			'solucao_pilares_2_desc'   => 'Conecte telefonia, WhatsApp, chat e demais canais ao Service Cloud para centralizar toda a jornada de atendimento.',
			'solucao_pilares_3_icone'  => $this->img( 'salesforce-svc-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Automatize SLAs e escalonamentos',
			'solucao_pilares_3_desc'   => 'Dispare regras, notificações e encaminhamentos automaticamente conforme eventos do atendimento e integrações corporativas.',
			// 3 · Casos de uso
			'solucao_casos_eyebrow'  => 'casos de uso',
			'solucao_casos_titulo'   => 'Automatize os principais processos de atendimento',
			'solucao_casos_1_icone'  => $this->img( 'salesforce-svc-caso-1' ),
			'solucao_casos_1_titulo' => 'Enriqueça casos com dados do ERP',
			'solucao_casos_1_desc'   => 'Apresente informações de faturamento, pedidos e contratos em tempo real durante o atendimento ao cliente.',
			'solucao_casos_2_icone'  => $this->img( 'salesforce-svc-caso-2' ),
			'solucao_casos_2_titulo' => 'Integre equipes de campo',
			'solucao_casos_2_desc'   => 'Conecte ordens de serviço e sistemas de field service para acompanhar toda a execução do atendimento.',
			'solucao_casos_3_icone'  => $this->img( 'salesforce-svc-caso-3' ),
			'solucao_casos_3_titulo' => 'Automatize reembolsos',
			'solucao_casos_3_desc'   => 'Dispare processos de estorno e reembolso automaticamente após a resolução de um caso.',
			'solucao_casos_4_icone'  => $this->img( 'salesforce-svc-caso-4' ),
			'solucao_casos_4_titulo' => 'Sincronize a base de conhecimento',
			'solucao_casos_4_desc'   => 'Mantenha conteúdos atualizados entre o Service Cloud e portais de autoatendimento sem processos manuais.',
			'solucao_casos_5_icone'  => $this->img( 'salesforce-svc-caso-5' ),
			'solucao_casos_5_titulo' => 'Receba alertas proativos de SLA',
			'solucao_casos_5_desc'   => 'Acione notificações e escalonamentos em tempo real sempre que um SLA estiver em risco.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',
			// 4 · Selos
			'solucao_selos_eyebrow' => 'compliance & segurança',
			'solucao_selos_titulo'  => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'   => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
			// 5 · Diferencial
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Atendimento sempre atualizado entre todos os sistemas',
			'solucao_dif_corpo'    => 'Utilize eventos em tempo real via Subscription API para manter o Service Cloud sincronizado com ERP, faturamento e demais aplicações, garantindo decisões baseadas em informações atuais.',
			'solucao_dif_topico_1' => 'Atualize dados em tempo real',
			'solucao_dif_topico_2' => 'Evite informações desatualizadas no atendimento',
			'solucao_dif_topico_3' => 'Integre eventos entre todos os sistemas',
			'solucao_dif_imagem'   => $this->img( 'salesforce-svc-dif' ),
			// 6 · Plataforma
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Uma plataforma para centralizar todo o atendimento',
			'solucao_plat_corpo'    => 'Grande parte do tempo de atendimento é perdida consultando outros sistemas. Centralize essas integrações para entregar todo o contexto diretamente no Service Cloud e acelerar a resolução dos casos.',
			'solucao_plat_topico_1' => 'Centralize dados do cliente',
			'solucao_plat_topico_2' => 'Reduza trocas entre sistemas',
			'solucao_plat_topico_3' => 'Acelere o tempo de resolução',
			'solucao_plat_imagem'   => $this->img( 'salesforce-svc-plataforma' ),
			// 7 · Aceleradores
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com integrações prontas para atendimento',
			'solucao_acel_corpo'     => 'Utilize um modelo pré-configurado para consultar faturamento, pedidos e informações do ERP diretamente no Service Cloud, reduzindo o tempo de implantação.',
			'solucao_acel_topico_1'  => 'Implante consultas rapidamente',
			'solucao_acel_topico_2'  => 'Reutilize modelos validados',
			'solucao_acel_topico_3'  => 'Adapte fluxos ao seu negócio',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'salesforce-svc-aceleradores' ),
		);
		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_salesforce_svc_faq( $post_id );
		WP_CLI::log( "  Salesforce Service Cloud preenchido (ID: {$post_id})." );
	}

	protected function preencher_salesforce_svc_faq( $post_id ) {
		$itens = array(
			array(
				'faq:svc-erp-tempo-real',
				'Como o Service Cloud recebe informações do ERP em tempo real?',
				'<p>A CLI Connect utiliza a Subscription API do Salesforce combinada com webhooks e conectores nativos de ERP para propagar eventos em tempo real. Quando um pedido é atualizado no ERP, o caso correspondente no Service Cloud recebe os dados atualizados automaticamente, sem necessidade de consultas manuais.</p>',
			),
			array(
				'faq:svc-reembolso-automatico',
				'É possível automatizar processos de reembolso a partir de um caso?',
				'<p>Sim. A CLI Connect permite criar fluxos que, ao encerrar um caso com determinado status, disparam automaticamente o processo de estorno ou reembolso no ERP ou sistema financeiro. O agente de atendimento não precisa acessar nenhum outro sistema para iniciar o processo.</p>',
			),
			array(
				'faq:svc-whatsapp-telefonia',
				'Como funciona a integração com WhatsApp e telefonia?',
				'<p>A CLI Connect conecta plataformas de telefonia e canais de mensageria como WhatsApp ao Service Cloud via APIs oficiais. As interações são registradas automaticamente como casos ou atividades, centralizando toda a jornada de atendimento em um único lugar sem duplicidade de dados.</p>',
			),
		);
		$ids = array();
		foreach ( $itens as $ordem => list( $slug, $pergunta, $resposta ) ) {
			$ids[] = (int) $this->upsert( $slug, array(
				'post_type'    => 'cli_faq',
				'post_title'   => $pergunta,
				'post_content' => $resposta,
				'menu_order'   => $ordem,
			) );
		}
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens',  $ids, $post_id );
		WP_CLI::log( sprintf( '  Salesforce Service Cloud FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/* =====================================================================
	   Salesforce Marketing Cloud
	   ===================================================================== */

	protected function preencher_solucao_salesforce_marketing_cloud() {
		$post_id = $this->upsert( 'solucao:salesforce-marketing-cloud', array(
			'post_type'   => 'cli_solucao',
			'post_title'  => 'Salesforce Marketing Cloud',
			'post_status' => 'publish',
		) );

		$campos = array(
			// 1 · Hero
			'solucao_hero_eyebrow'    => 'para o seu Salesforce Marketing Cloud',
			'solucao_hero_titulo'     => 'Alimente suas jornadas de marketing com dados vivos de vendas e produto',
			'solucao_hero_corpo'      => 'Conecte o Salesforce Marketing Cloud ao CRM, e-commerce e data warehouse para criar campanhas baseadas em comportamentos reais, com dados atualizados em cada interação com o cliente.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucoes/tecnologia/salesforce-marketing-cloud/',
			'solucao_hero_imagem'     => $this->img( 'salesforce-mc-hero' ),
			// 2 · Pilares
			'solucao_pilares_titulo'   => 'Transforme dados em jornadas relevantes',
			'solucao_pilares_1_icone'  => $this->img( 'salesforce-mc-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Dispare jornadas por eventos reais',
			'solucao_pilares_1_desc'   => 'Ative campanhas automaticamente a partir de compras, uso de produto e interações de suporte.',
			'solucao_pilares_2_icone'  => $this->img( 'salesforce-mc-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Sincronize audiências em tempo real',
			'solucao_pilares_2_desc'   => 'Mantenha listas e segmentos atualizados entre Marketing Cloud, CRM e ERP continuamente.',
			'solucao_pilares_3_icone'  => $this->img( 'salesforce-mc-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Personalize com dados completos',
			'solucao_pilares_3_desc'   => 'Enriqueça perfis de contato com informações de vendas, produto e comportamento.',
			// 3 · Casos de uso
			'solucao_casos_eyebrow'  => 'casos de uso',
			'solucao_casos_titulo'   => 'Automatize jornadas orientadas por dados',
			'solucao_casos_1_icone'  => $this->img( 'salesforce-mc-caso-1' ),
			'solucao_casos_1_titulo' => 'Dispare jornadas automaticamente',
			'solucao_casos_1_desc'   => 'Acione o Journey Builder a partir de eventos de e-commerce e ERP em tempo real.',
			'solucao_casos_2_icone'  => $this->img( 'salesforce-mc-caso-2' ),
			'solucao_casos_2_titulo' => 'Sincronize audiências comerciais',
			'solucao_casos_2_desc'   => 'Conecte segmentos entre Marketing Cloud, Sales Cloud e Service Cloud continuamente.',
			'solucao_casos_3_icone'  => $this->img( 'salesforce-mc-caso-3' ),
			'solucao_casos_3_titulo' => 'Enriqueça perfis de clientes',
			'solucao_casos_3_desc'   => 'Adicione dados de uso de produto para criar experiências mais personalizadas.',
			'solucao_casos_4_icone'  => $this->img( 'salesforce-mc-caso-4' ),
			'solucao_casos_4_titulo' => 'Feche a atribuição de campanhas',
			'solucao_casos_4_desc'   => 'Conecte marketing, CRM e ERP para acompanhar impacto até a receita.',
			'solucao_casos_5_icone'  => $this->img( 'salesforce-mc-caso-5' ),
			'solucao_casos_5_titulo' => 'Aplique opt-out automaticamente',
			'solucao_casos_5_desc'   => 'Propague preferências de contato entre todos os canais conectados.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',
			// 4 · Selos
			'solucao_selos_eyebrow' => 'compliance & segurança',
			'solucao_selos_titulo'  => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'   => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
			// 5 · Diferencial
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações seguras para dados de marketing',
			'solucao_dif_corpo'    => 'Utilize APIs REST e SOAP do Marketing Cloud com controles de consentimento para garantir que preferências acompanhem todos os sistemas conectados.',
			'solucao_dif_topico_1' => 'Utilize APIs oficiais REST e SOAP',
			'solucao_dif_topico_2' => 'Propague consentimentos entre sistemas',
			'solucao_dif_topico_3' => 'Controle opt-outs em todos canais',
			'solucao_dif_imagem'   => $this->img( 'salesforce-mc-dif' ),
			// 6 · Plataforma
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Centralize jornadas com dados conectados',
			'solucao_plat_corpo'    => 'Conecte Marketing Cloud a vendas, produto e suporte em uma única plataforma para criar jornadas baseadas em comportamento real, não em listas antigas.',
			'solucao_plat_topico_1' => 'Conecte eventos reais de negócio',
			'solucao_plat_topico_2' => 'Elimine exportações manuais de listas',
			'solucao_plat_topico_3' => 'Unifique dados entre áreas comerciais',
			'solucao_plat_imagem'   => $this->img( 'salesforce-mc-plataforma' ),
			// 7 · Aceleradores
			'solucao_acel_eyebrow'  => 'Aceleradores de integração',
			'solucao_acel_titulo'   => 'Comece com jornadas já estruturadas',
			'solucao_acel_corpo'    => 'Utilize um modelo pronto para disparar jornadas no Journey Builder a partir de eventos de ERP e e-commerce, acelerando sua operação de marketing.',
			'solucao_acel_topico_1' => 'Configure eventos rapidamente',
			'solucao_acel_topico_2' => 'Reutilize fluxos validados',
			'solucao_acel_topico_3' => 'Adapte jornadas ao negócio',
			'solucao_acel_topico_4' => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'   => $this->img( 'salesforce-mc-aceleradores' ),
		);
		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_salesforce_mc_faq( $post_id );
		WP_CLI::log( "  Salesforce Marketing Cloud preenchido (ID: {$post_id})." );
	}

	protected function preencher_salesforce_mc_faq( $post_id ) {
		$itens = array(
			array(
				'faq:mc-jornada-evento-externo',
				'Como disparar uma jornada a partir de um evento fora do Salesforce?',
				'<p>A CLI Connect utiliza a API de Eventos do Marketing Cloud combinada com conectores nativos de ERP e e-commerce. Quando ocorre um evento externo — como uma compra ou atualização de produto — a integração envia o payload diretamente ao Journey Builder, acionando a jornada correspondente sem intervenção manual.</p>',
			),
			array(
				'faq:mc-optout-todos-canais',
				'Como garantir que um opt-out se propague para todos os canais?',
				'<p>A CLI Connect sincroniza preferências de contato entre o Marketing Cloud, o CRM e demais sistemas conectados. Quando um contato solicita opt-out em qualquer canal, a integração propaga a informação em tempo real para todos os pontos de comunicação, garantindo conformidade e evitando envios indesejados.</p>',
			),
			array(
				'faq:mc-segmentos-tempo-real',
				'É possível sincronizar segmentos de audiência em tempo real com o CRM?',
				'<p>Sim. A CLI Connect mantém os Data Extensions do Marketing Cloud atualizados a partir de eventos de negócio do CRM, ERP e plataformas de e-commerce. Segmentos e listas refletem dados reais de compras, uso de produto e interações de suporte sem necessidade de exportações manuais ou agendamentos periódicos.</p>',
			),
		);
		$ids = array();
		foreach ( $itens as $ordem => list( $slug, $pergunta, $resposta ) ) {
			$ids[] = (int) $this->upsert( $slug, array(
				'post_type'    => 'cli_faq',
				'post_title'   => $pergunta,
				'post_content' => $resposta,
				'menu_order'   => $ordem,
			) );
		}
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_eyebrow', 'FAQ', $post_id );
		update_field( 'solucao_faq_itens',  $ids, $post_id );
		WP_CLI::log( sprintf( '  Salesforce Marketing Cloud FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/* =====================================================================
	   SAP
	   ===================================================================== */

	/**
	 * Preenche todos os campos ACF do post cli_solucao do SAP.
	 *
	 * @return void
	 */
	protected function preencher_solucao_sap() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:sap', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  SAP: post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'         => 'para o seu SAP',
			'solucao_hero_titulo'          => 'Acelere sua migração para SAP S/4HANA sem comprometer a operação',
			'solucao_hero_titulo_destaque' => '',
			'solucao_hero_corpo'           => 'Conecte o SAP ao restante do seu ecossistema, preserve um ambiente Clean Core e conduza a migração com mais segurança, agilidade e previsibilidade.',
			'solucao_hero_btn1_texto'      => 'Agende uma demonstração',
			'solucao_hero_btn1_url'        => '/contato/',
			'solucao_hero_btn2_texto'      => 'Conheça nossa solução',
			'solucao_hero_btn2_url'        => '/solucoes/tecnologia/sap/',
			'solucao_hero_imagem'          => $this->img( 'sap-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Tudo o que você precisa para integrar seu SAP',
			'solucao_pilares_1_icone'  => $this->img( 'sap-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Migre com confiança',
			'solucao_pilares_1_desc'   => 'Conduza sua migração para o SAP S/4HANA com uma arquitetura preparada para reduzir riscos, retrabalho e impactos na operação.',
			'solucao_pilares_2_icone'  => $this->img( 'sap-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Unifique todo o ecossistema',
			'solucao_pilares_2_desc'   => 'Integre SAP, Salesforce, Workday, ServiceNow e outras aplicações em uma única plataforma, simplificando a gestão das integrações.',
			'solucao_pilares_3_icone'  => $this->img( 'sap-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Reduza custos de integração',
			'solucao_pilares_3_desc'   => 'Utilize Add-on SAP homologado, conectores (RFC, IDoc, BAPI) e protocolos (OData, REST, SOAP). Tudo sem custo adicional.',

			// 3 · Casos de Uso.
			'solucao_casos_eyebrow'  => 'casos de uso',
			'solucao_casos_titulo'   => 'Automatize os processos mais críticos do SAP',
			'solucao_casos_1_icone'  => $this->img( 'sap-caso-1' ),
			'solucao_casos_1_titulo' => 'Pedido ao recebimento integrado',
			'solucao_casos_1_desc'   => 'Sincronize automaticamente oportunidades, pedidos e faturamento entre Salesforce e SAP S/4HANA.',
			'solucao_casos_2_icone'  => $this->img( 'sap-caso-2' ),
			'solucao_casos_2_titulo' => 'Migração sem interrupções',
			'solucao_casos_2_desc'   => 'Execute o período de convivência entre SAP ECC e S/4HANA mantendo ambos sincronizados durante toda a transição.',
			'solucao_casos_3_icone'  => $this->img( 'sap-caso-3' ),
			'solucao_casos_3_titulo' => 'SAP conectado à IA',
			'solucao_casos_3_desc'   => 'Permita que agentes de IA consultem informações do SAP para acelerar análises e operações.',
			'solucao_casos_4_icone'  => $this->img( 'sap-caso-4' ),
			'solucao_casos_4_titulo' => 'Automatize compras corporativas',
			'solucao_casos_4_desc'   => 'Integre SAP aos principais sistemas de procurement, como Ariba e Coupa, eliminando retrabalho operacional.',
			'solucao_casos_5_icone'  => $this->img( 'sap-caso-5' ),
			'solucao_casos_5_titulo' => 'Envie dados para Analytics',
			'solucao_casos_5_desc'   => 'Alimente automaticamente plataformas como Snowflake e BigQuery com dados atualizados do SAP.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'  => '/contato/',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_sap_selos( $post_id );
		$this->preencher_sap_diferencial( $post_id );
		$this->preencher_sap_plataforma( $post_id );
		$this->preencher_sap_aceleradores( $post_id );
		$this->preencher_sap_faq( $post_id );

		WP_CLI::log( "  SAP preenchido (ID: {$post_id})." );
	}

	/**
	 * Preenche os campos ACF da seção Selos do SAP.
	 *
	 * @param int $post_id ID do post cli_solucao do SAP.
	 * @return void
	 */
	protected function preencher_sap_selos( $post_id ) {
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
	 * Preenche os campos ACF da seção Diferencial Técnico do SAP.
	 *
	 * @param int $post_id ID do post cli_solucao do SAP.
	 * @return void
	 */
	protected function preencher_sap_diferencial( $post_id ) {
		$campos = array(
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integração nativa, segura e preparada para ambientes SAP',
			'solucao_dif_corpo'    => 'Conecte seu ambiente SAP utilizando recursos nativos da plataforma, preservando a segurança da infraestrutura e reduzindo a necessidade de componentes intermediários.',
			'solucao_dif_topico_1' => 'Utilize conectores nativos RFC, BAPI e IDoc.',
			'solucao_dif_topico_2' => 'Utilize Add-on Nativo, SOAP, Odata, REST',
			'solucao_dif_topico_3' => 'Preserve a arquitetura Clean Core.',
			'solucao_dif_imagem'   => $this->img( 'sap-cleancore-1' ),
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}
	}

	/**
	 * Preenche os campos ACF da seção Plataforma Única do SAP.
	 *
	 * @param int $post_id ID do post cli_solucao do SAP.
	 * @return void
	 */
	protected function preencher_sap_plataforma( $post_id ) {
		$campos = array(
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Centralize todas as integrações do seu SAP',
			'solucao_plat_corpo'    => 'Substitua integrações isoladas por uma plataforma única capaz de conectar SAP, aplicações corporativas, dados e automações com governança centralizada.',
			'solucao_plat_topico_1' => 'Reutilize integrações entre diferentes projetos.',
			'solucao_plat_topico_2' => 'Padronize toda a arquitetura de integração.',
			'solucao_plat_topico_3' => 'Reduza custos de manutenção contínua.',
			'solucao_plat_imagem'   => $this->img( 'sap-conectar' ),
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}
	}

	/**
	 * Preenche os campos ACF da seção Aceleradores de Integração do SAP.
	 *
	 * @param int $post_id ID do post cli_solucao do SAP.
	 * @return void
	 */
	protected function preencher_sap_aceleradores( $post_id ) {
		$campos = array(
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece utilizando integrações já validadas',
			'solucao_acel_corpo'     => 'Utilize modelos prontos para acelerar a implantação das integrações mais comuns entre SAP e os principais sistemas do mercado.',
			'solucao_acel_topico_1'  => 'Aproveite modelos de Order-to-Cash.',
			'solucao_acel_topico_2'  => 'Reduza o tempo de implantação.',
			'solucao_acel_topico_3'  => 'Adapte fluxos ao seu ambiente.',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'sap-sincronizar' ),
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}
	}

	/**
	 * Cria os posts cli_faq do SAP e vincula à solução.
	 *
	 * @param int $post_id ID do post cli_solucao do SAP.
	 * @return void
	 */
	protected function preencher_sap_faq( $post_id ) {
		$itens = array(
			array(
				'faq:sap-integracao',
				'Como a CLI Connect se integra ao SAP?',
				'<p>A CLI Connect utiliza um Add-on nativo homologado pela SAP, além de conectores RFC, BAPI, IDoc, OData, REST e SOAP. Essa abordagem garante compatibilidade com as principais versões do SAP ECC e S/4HANA, preservando a arquitetura Clean Core e eliminando a necessidade de modificações não suportadas no sistema.</p>',
			),
			array(
				'faq:sap-versoes',
				'Quais versões do SAP são suportadas?',
				'<p>A plataforma suporta SAP ECC (incluindo versões 6.0 em diante) e SAP S/4HANA (Cloud e On-Premises). A escolha do conector adequado — RFC/BAPI para processos legados ou OData/APIs REST para S/4HANA — é definida durante a etapa de arquitetura da integração.</p>',
			),
			array(
				'faq:sap-implantacao',
				'Quanto tempo leva uma implantação?',
				'<p>O tempo varia conforme a complexidade do cenário, mas projetos com aceleradores prontos podem ser colocados em produção em poucas semanas. A CLI Connect disponibiliza modelos pré-construídos para os cenários mais comuns — como Order-to-Cash, Procure-to-Pay e migração ECC → S/4HANA — reduzindo significativamente o prazo de implantação.</p>',
			),
			array(
				'faq:sap-atualizacoes',
				'As atualizações do SAP impactam as integrações?',
				'<p>Integrações desenvolvidas com Add-on nativo e APIs suportadas pela SAP acompanham o ciclo de atualizações sem quebras. A CLI Connect monitora cada release e valida os fluxos críticos preventivamente, acionando o suporte quando algum ajuste for necessário após uma atualização.</p>',
			),
			array(
				'faq:sap-cleancore',
				'Como preservar o Clean Core durante a migração?',
				'<p>A principal forma de garantir o Clean Core é evitar modificações diretas no núcleo do SAP. A CLI Connect utiliza APIs e extensões suportadas pela própria SAP — Add-on homologado, OData, RFC e BAPI — mantendo toda a lógica de integração na plataforma iPaaS, fora do core do ERP. Isso facilita futuras atualizações e reduz o risco operacional durante a migração para S/4HANA.</p>',
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

		WP_CLI::log( sprintf( '  SAP FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/* =====================================================================
	   SALESFORCE FAQ
	   ===================================================================== */

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
	/* =====================================================================
	   TOTVS DATASUL
	   ===================================================================== */

	/**
	 * Preenche todos os campos ACF do post cli_solucao do TOTVS Datasul.
	 *
	 * @return void
	 */
	protected function preencher_solucao_datasul() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:totvs-datasul', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  TOTVS Datasul: post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'         => 'para o seu DATAsul',
			'solucao_hero_titulo'          => 'Conecte o TOTVS Datasul sem interromper sua operação industrial',
			'solucao_hero_titulo_destaque' => '',
			'solucao_hero_corpo'           => 'Integre o Datasul a MES, CRM, portais B2B e plataformas de BI utilizando uma única plataforma. Compartilhe informações entre plantas, automatize processos e modernize sua operação sem alterar o core do ERP.',
			'solucao_hero_btn1_texto'      => 'Agende uma demonstração',
			'solucao_hero_btn1_url'        => '/contato/',
			'solucao_hero_btn2_texto'      => 'Conheça nossa solução',
			'solucao_hero_btn2_url'        => '/solucoes/tecnologia/totvs-datasul/',
			'solucao_hero_imagem'          => $this->img( 'totvs-datasul-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Conecte toda a operação industrial',
			'solucao_pilares_1_icone'  => $this->img( 'totvs-datasul-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Integre sua manufatura com mais agilidade',
			'solucao_pilares_1_desc'   => 'Conecte o Datasul a sistemas de produção, vendas e logística sem projetos longos ou desenvolvimentos complexos.',
			'solucao_pilares_2_icone'  => $this->img( 'totvs-datasul-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Padronize informações entre plantas',
			'solucao_pilares_2_desc'   => 'Centralize dados de produção e estoque para garantir decisões mais rápidas e confiáveis em toda a operação.',
			'solucao_pilares_3_icone'  => $this->img( 'totvs-datasul-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Reduza a dependência de especialistas',
			'solucao_pilares_3_desc'   => 'Simplifique novas integrações sem depender continuamente de equipes especializadas em Progress 4GL.',
			'solucao_casos_eyebrow'    => 'casos de uso',
			'solucao_casos_titulo'     => 'Automatize os processos que movem sua fábrica',
			'solucao_casos_1_icone'    => $this->img( 'totvs-datasul-caso-1' ),
			'solucao_casos_1_titulo'   => 'Sincronize ordens de produção',
			'solucao_casos_1_desc'     => 'Conecte o MES ao Datasul para atualizar ordens automaticamente durante toda a operação industrial.',
			'solucao_casos_2_icone'    => $this->img( 'totvs-datasul-caso-2' ),
			'solucao_casos_2_titulo'   => 'Consolide estoques entre plantas',
			'solucao_casos_2_desc'     => 'Compartilhe saldos de estoque entre unidades para aumentar a visibilidade da operação.',
			'solucao_casos_3_icone'    => $this->img( 'totvs-datasul-caso-3' ),
			'solucao_casos_3_titulo'   => 'Automatize pedidos B2B',
			'solucao_casos_3_desc'     => 'Integre portais de clientes diretamente ao Datasul para reduzir retrabalho e acelerar o processamento.',
			'solucao_casos_4_icone'    => $this->img( 'totvs-datasul-caso-4' ),
			'solucao_casos_4_titulo'   => 'Centralize o fechamento financeiro',
			'solucao_casos_4_desc'     => 'Consolide informações entre diferentes unidades para simplificar o fechamento corporativo.',
			'solucao_casos_5_icone'    => $this->img( 'totvs-datasul-caso-5' ),
			'solucao_casos_5_titulo'   => 'Disponibilize dados para IA',
			'solucao_casos_5_desc'     => 'Permita que agentes de IA consultem informações do Datasul com segurança através de integrações governadas.',
			'solucao_casos_cta_texto'  => 'Agende uma demonstração',
			'solucao_casos_cta_url'    => '/contato/',
			// 4 · Selos.
			'solucao_selos_eyebrow'    => 'compliance & segurança',
			'solucao_selos_titulo'     => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'      => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
			// 5 · Diferencial Técnico.
			'solucao_dif_eyebrow'      => 'diferencial técnico',
			'solucao_dif_titulo'       => 'Conectividade preparada para ambientes industriais',
			'solucao_dif_corpo'        => 'Conecte o Datasul utilizando o protocolo Progress/EMS com processamento realizado dentro da infraestrutura da empresa, preservando segurança e desempenho.',
			'solucao_dif_topico_1'     => 'Utilize conectividade nativa Progress/EMS.',
			'solucao_dif_topico_2'     => 'Preserve o banco protegido internamente.',
			'solucao_dif_topico_3'     => 'Implante dentro da própria infraestrutura.',
			'solucao_dif_imagem'       => $this->img( 'totvs-datasul-dif' ),
			// 6 · Plataforma Única.
			'solucao_plat_eyebrow'     => 'plataforma única',
			'solucao_plat_titulo'      => 'Integre diferentes ERPs na mesma plataforma',
			'solucao_plat_corpo'       => 'Empresas que cresceram por aquisições frequentemente operam mais de um ERP. Centralize Datasul, SAP, Protheus e outros sistemas em uma única camada de integração.',
			'solucao_plat_topico_1'    => 'Reutilize integrações já implantadas.',
			'solucao_plat_topico_2'    => 'Reduza novos projetos de desenvolvimento.',
			'solucao_plat_topico_3'    => 'Centralize toda a governança das integrações.',
			'solucao_plat_imagem'      => $this->img( 'totvs-datasul-plataforma' ),
			// 7 · Aceleradores.
			'solucao_acel_eyebrow'     => 'Aceleradores de integração',
			'solucao_acel_titulo'      => 'Implemente integrações em menos tempo',
			'solucao_acel_corpo'       => 'Utilize modelos prontos para sincronizar ordens de produção e pedidos B2B, reduzindo o esforço de implantação e acelerando novos projetos.',
			'solucao_acel_topico_1'    => 'Aproveite modelos para ordens de produção.',
			'solucao_acel_topico_2'    => 'Reutilize fluxos para pedidos B2B.',
			'solucao_acel_topico_3'    => 'Adapte rapidamente ao seu ambiente.',
			'solucao_acel_topico_4'    => 'E muito mais...',
			'solucao_acel_btn_texto'   => 'Começar agora',
			'solucao_acel_btn_url'     => '/contato/',
			'solucao_acel_imagem'      => $this->img( 'totvs-datasul-aceleradores' ),
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_datasul_faq( $post_id );
		WP_CLI::log( "  TOTVS Datasul preenchido (ID: {$post_id})." );
	}

	/* =====================================================================
	   TOTVS WINTHOR
	   ===================================================================== */

	protected function preencher_solucao_winthor() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:totvs-winthor', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Solução TOTVS Winthor não encontrada.' );
			return;
		}
		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'         => 'para o seu Winthor',
			'solucao_hero_titulo'          => 'Integre o TOTVS Winthor e acelere toda sua operação comercial',
			'solucao_hero_titulo_destaque' => '',
			'solucao_hero_corpo'           => 'Conecte o Winthor à força de vendas, e-commerce B2B, transportadoras e bancos para automatizar processos, reduzir retrabalho e manter pedidos, preços e entregas sempre sincronizados.',
			'solucao_hero_btn1_texto'      => 'Agende uma demonstração',
			'solucao_hero_btn1_url'        => '/contato/',
			'solucao_hero_btn2_texto'      => 'Conheça nossa solução',
			'solucao_hero_btn2_url'        => '/solucoes/tecnologia/totvs-winthor/',
			'solucao_hero_imagem'          => $this->img( 'totvs-winthor-hero' ),
			// 2 · Pilares.
			'solucao_pilares_titulo'       => 'Conecte sua operação de distribuição',
			'solucao_pilares_1_icone'      => $this->img( 'totvs-winthor-pilar-1' ),
			'solucao_pilares_1_titulo'     => 'Atualize preços automaticamente',
			'solucao_pilares_1_desc'       => 'Sincronize tabelas de preços e promoções em tempo real para toda a equipe comercial, reduzindo inconsistências e acelerando negociações.',
			'solucao_pilares_2_icone'      => $this->img( 'totvs-winthor-pilar-2' ),
			'solucao_pilares_2_titulo'     => 'Automatize pedidos de venda',
			'solucao_pilares_2_desc'       => 'Integre aplicativos de pré-venda e canais digitais ao Winthor para eliminar digitação manual e acelerar o faturamento.',
			'solucao_pilares_3_icone'      => $this->img( 'totvs-winthor-pilar-3' ),
			'solucao_pilares_3_titulo'     => 'Expanda integrações com facilidade',
			'solucao_pilares_3_desc'       => 'Conecte novos sistemas utilizando uma arquitetura preparada para crescer junto com sua operação.',
			// 3 · Casos de Uso.
			'solucao_casos_eyebrow'        => 'casos de uso',
			'solucao_casos_titulo'         => 'Automatize os principais processos do Winthor',
			'solucao_casos_1_icone'        => $this->img( 'totvs-winthor-caso-1' ),
			'solucao_casos_1_titulo'       => 'Sincronize pedidos da força de vendas',
			'solucao_casos_1_desc'         => 'Envie automaticamente pedidos dos aplicativos comerciais para o Winthor sem retrabalho.',
			'solucao_casos_2_icone'        => $this->img( 'totvs-winthor-caso-2' ),
			'solucao_casos_2_titulo'       => 'Atualize preços em tempo real',
			'solucao_casos_2_desc'         => 'Distribua alterações de preços e descontos imediatamente para vendedores e canais digitais.',
			'solucao_casos_3_icone'        => $this->img( 'totvs-winthor-caso-3' ),
			'solucao_casos_3_titulo'       => 'Integre transportadoras',
			'solucao_casos_3_desc'         => 'Automatize envio de etiquetas, rastreamento e atualização do status das entregas.',
			'solucao_casos_4_icone'        => $this->img( 'totvs-winthor-caso-4' ),
			'solucao_casos_4_titulo'       => 'Concilie recebimentos automaticamente',
			'solucao_casos_4_desc'         => 'Conecte bancos e adquirentes para simplificar a conciliação financeira.',
			'solucao_casos_5_icone'        => $this->img( 'totvs-winthor-caso-5' ),
			'solucao_casos_5_titulo'       => 'Consolide vendas entre filiais',
			'solucao_casos_5_desc'         => 'Centralize indicadores comerciais de diferentes unidades em uma única visão.',
			'solucao_casos_cta_texto'      => 'Agende uma demonstração',
			'solucao_casos_cta_url'        => '/contato/',
			// 4 · Selos.
			'solucao_selos_eyebrow'        => 'compliance & segurança',
			'solucao_selos_titulo'         => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'          => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
			// 5 · Diferencial Técnico.
			'solucao_dif_eyebrow'          => 'diferencial técnico',
			'solucao_dif_titulo'           => 'Conectividade preparada para operações de alto volume',
			'solucao_dif_corpo'            => 'A plataforma utiliza conectores dedicados às rotinas automáticas e webservices do Winthor, suportando grandes volumes de pedidos típicos de distribuidores e atacadistas.',
			'solucao_dif_topico_1'         => 'Processe grandes volumes de pedidos.',
			'solucao_dif_topico_2'         => 'Utilize conectores nativos do Winthor.',
			'solucao_dif_topico_3'         => 'Preserve a estabilidade da operação.',
			'solucao_dif_imagem'           => $this->img( 'totvs-winthor-dif' ),
			// 6 · Plataforma Única.
			'solucao_plat_eyebrow'         => 'plataforma única',
			'solucao_plat_titulo'          => 'Centralize todas as integrações da distribuição',
			'solucao_plat_corpo'           => 'Conecte Winthor, aplicativos comerciais, transportadoras e bancos em uma única plataforma, reaproveitando integrações e reduzindo novos projetos.',
			'solucao_plat_topico_1'        => 'Reutilize integrações existentes.',
			'solucao_plat_topico_2'        => 'Centralize toda a governança.',
			'solucao_plat_topico_3'        => 'Reduza novos desenvolvimentos.',
			'solucao_plat_imagem'          => $this->img( 'totvs-winthor-plataforma' ),
			// 7 · Aceleradores.
			'solucao_acel_eyebrow'         => 'Aceleradores de integração',
			'solucao_acel_titulo'          => 'Comece utilizando integrações prontas',
			'solucao_acel_corpo'           => 'Implemente rapidamente os principais cenários de integração.',
			'solucao_acel_topico_1'        => 'Implante integração de pedidos rapidamente.',
			'solucao_acel_topico_2'        => 'Reutilize modelos para tabelas de preço.',
			'solucao_acel_topico_3'        => 'Adapte fluxos ao seu processo.',
			'solucao_acel_topico_4'        => 'E muito mais...',
			'solucao_acel_btn_texto'       => 'Começar agora',
			'solucao_acel_btn_url'         => '/contato/',
			'solucao_acel_imagem'          => $this->img( 'totvs-winthor-aceleradores' ),
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_winthor_faq( $post_id );
		WP_CLI::log( "  TOTVS Winthor preenchido (ID: {$post_id})." );
	}

	/* =====================================================================
	   TOTVS LOGIX
	   ===================================================================== */

	protected function preencher_solucao_logix() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:totvs-logix', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Solução TOTVS Logix não encontrada.' );
			return;
		}
		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'         => 'para o seu Logix',
			'solucao_hero_titulo'          => 'Conecte o TOTVS Logix e mantenha seu estoque sincronizado em todos os canais',
			'solucao_hero_titulo_destaque' => '',
			'solucao_hero_corpo'           => 'Integre o Logix a ERPs, marketplaces e transportadoras para automatizar a operação logística, eliminar divergências de estoque e acelerar o atendimento de pedidos sem processos manuais.',
			'solucao_hero_btn1_texto'      => 'Agende uma demonstração',
			'solucao_hero_btn1_url'        => '/contato/',
			'solucao_hero_btn2_texto'      => 'Conheça nossa solução',
			'solucao_hero_btn2_url'        => '/solucoes/tecnologia/totvs-logix/',
			'solucao_hero_imagem'          => $this->img( 'totvs-logix-hero' ),
			// 2 · Pilares.
			'solucao_pilares_titulo'       => 'Mantenha sua logística conectada em tempo real',
			'solucao_pilares_1_icone'      => $this->img( 'totvs-logix-pilar-1' ),
			'solucao_pilares_1_titulo'     => 'Sincronize estoques automaticamente',
			'solucao_pilares_1_desc'       => 'Atualize saldos entre o Logix, marketplaces e canais de venda em tempo real para evitar divergências e melhorar a disponibilidade dos produtos.',
			'solucao_pilares_2_icone'      => $this->img( 'totvs-logix-pilar-2' ),
			'solucao_pilares_2_titulo'     => 'Automatize toda a expedição',
			'solucao_pilares_2_desc'       => 'Orquestre picking, packing e expedição a partir dos pedidos recebidos, reduzindo retrabalho e aumentando a produtividade do armazém.',
			'solucao_pilares_3_icone'      => $this->img( 'totvs-logix-pilar-3' ),
			'solucao_pilares_3_titulo'     => 'Evite perdas por overselling',
			'solucao_pilares_3_desc'       => 'Compartilhe informações de estoque entre todos os canais para vender somente o que realmente está disponível.',
			// 3 · Casos de Uso.
			'solucao_casos_eyebrow'        => 'casos de uso',
			'solucao_casos_titulo'         => 'Automatize toda a operação logística',
			'solucao_casos_1_icone'        => $this->img( 'totvs-logix-caso-1' ),
			'solucao_casos_1_titulo'       => 'Sincronize estoques com marketplaces',
			'solucao_casos_1_desc'         => 'Atualize automaticamente o estoque entre Logix, Amazon, Mercado Livre, Magalu e outros canais de venda.',
			'solucao_casos_2_icone'        => $this->img( 'totvs-logix-caso-2' ),
			'solucao_casos_2_titulo'       => 'Direcione pedidos ao centro correto',
			'solucao_casos_2_desc'         => 'Roteie automaticamente cada pedido para o centro de distribuição mais adequado conforme regras da operação.',
			'solucao_casos_3_icone'        => $this->img( 'totvs-logix-caso-3' ),
			'solucao_casos_3_titulo'       => 'Integre transportadoras',
			'solucao_casos_3_desc'         => 'Automatize emissão de etiquetas, rastreamento e atualização do status de entrega.',
			'solucao_casos_4_icone'        => $this->img( 'totvs-logix-caso-4' ),
			'solucao_casos_4_titulo'       => 'Atualize o ERP em tempo real',
			'solucao_casos_4_desc'         => 'Sincronize separação, expedição e faturamento automaticamente entre o Logix e o ERP.',
			'solucao_casos_5_icone'        => $this->img( 'totvs-logix-caso-5' ),
			'solucao_casos_5_titulo'       => 'Automatize devoluções',
			'solucao_casos_5_desc'         => 'Controle devoluções e reentrada de estoque sem processos manuais ou retrabalho operacional.',
			'solucao_casos_cta_texto'      => 'Agende uma demonstração',
			'solucao_casos_cta_url'        => '/contato/',
			// 4 · Selos.
			'solucao_selos_eyebrow'        => 'compliance & segurança',
			'solucao_selos_titulo'         => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'          => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',

			// 5 · Diferencial Técnico.
			'solucao_dif_eyebrow'          => 'diferencial técnico',
			'solucao_dif_titulo'           => 'Escalabilidade para operações logísticas de alto volume',
			'solucao_dif_corpo'            => 'A plataforma suporta grandes volumes de transações com escalabilidade automática, garantindo estabilidade mesmo durante Black Friday e outras datas sazonais de alta demanda.',
			'solucao_dif_topico_1'         => 'Processe picos de operação com estabilidade.',
			'solucao_dif_topico_2'         => 'Escalone pipelines automaticamente.',
			'solucao_dif_topico_3'         => 'Mantenha a operação disponível em alta demanda.',
			'solucao_dif_imagem'           => $this->img( 'totvs-logix-dif' ),

			// 6 · Plataforma Única.
			'solucao_plat_eyebrow'         => 'plataforma única',
			'solucao_plat_titulo'          => 'Centralize toda a integração da sua logística',
			'solucao_plat_corpo'           => 'Conecte Logix, marketplaces, transportadoras e ERPs em uma única plataforma para manter estoques sincronizados em tempo real e eliminar integrações isoladas que geram atrasos e overselling.',
			'solucao_plat_topico_1'        => 'Centralize todas as integrações.',
			'solucao_plat_topico_2'        => 'Sincronize estoques em tempo real.',
			'solucao_plat_topico_3'        => 'Reduza projetos isolados de integração.',
			'solucao_plat_imagem'          => $this->img( 'totvs-logix-plataforma' ),

			// 7 · Aceleradores.
			'solucao_acel_eyebrow'         => 'Aceleradores de integração',
			'solucao_acel_titulo'          => 'Comece utilizando integrações prontas',
			'solucao_acel_corpo'           => 'Implemente rapidamente cenários de sincronização entre Logix e marketplaces utilizando modelos pré-configurados que reduzem o tempo de implantação.',
			'solucao_acel_topico_1'        => 'Implante integrações mais rapidamente.',
			'solucao_acel_topico_2'        => 'Reutilize modelos já validados.',
			'solucao_acel_topico_3'        => 'Adapte fluxos à sua operação.',
			'solucao_acel_topico_4'        => 'E muito mais...',
			'solucao_acel_btn_texto'       => 'Começar agora',
			'solucao_acel_btn_url'         => '/contato/',
			'solucao_acel_imagem'          => $this->img( 'totvs-logix-aceleradores' ),
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_logix_faq( $post_id );
		WP_CLI::log( "  TOTVS Logix preenchido (ID: {$post_id})." );
	}

	protected function preencher_logix_faq( $post_id ) {
		$itens = array(
			array(
				'faq:logix-overselling',
				'Como evitar overselling entre múltiplos canais de venda?',
				'<p>A CLI Connect sincroniza o saldo de estoque do Logix em tempo real com todos os canais conectados — marketplaces, e-commerce e loja física. Sempre que uma venda é confirmada, a plataforma atualiza automaticamente os demais canais, eliminando o risco de vender um produto que já não está disponível.</p>',
			),
			array(
				'faq:logix-cds',
				'A CLI Connect suporta múltiplos centros de distribuição?',
				'<p>Sim. A plataforma permite mapear regras de roteamento por região, tipo de produto ou capacidade de estoque, direcionando cada pedido ao centro de distribuição correto automaticamente. As movimentações de cada CD são refletidas de forma consolidada no Logix.</p>',
			),
			array(
				'faq:logix-transportadoras',
				'Como funciona a integração com transportadoras?',
				'<p>A CLI Connect automatiza o envio de dados do pedido à transportadora após a expedição no Logix, recebe o código de rastreamento e atualiza o status de entrega no ERP e nos canais de venda. O processo elimina lançamentos manuais e reduz erros de acompanhamento logístico.</p>',
			),
		);
		$ids = array();
		foreach ( $itens as $ordem => list( $slug, $pergunta, $resposta ) ) {
			$ids[] = (int) $this->upsert( $slug, array(
				'post_type'    => 'cli_faq',
				'post_title'   => $pergunta,
				'post_content' => $resposta,
				'menu_order'   => $ordem,
			) );
		}
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $ids, $post_id );
		WP_CLI::log( sprintf( '  Logix FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	protected function preencher_solucao_senior() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:senior',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "senior" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu Senior',
			'solucao_hero_titulo'     => 'Conecte o Senior e automatize toda a jornada do colaborador',
			'solucao_hero_corpo'      => 'Integre o Senior aos sistemas de folha, ponto, benefícios, acessos e identidade para eliminar processos manuais, proteger dados sensíveis e acelerar toda a operação de RH.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucoes/tecnologia/senior/',
			'solucao_hero_imagem'     => $this->img( 'senior-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Transforme o RH em um processo conectado',
			'solucao_pilares_1_icone'  => $this->img( 'senior-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Automatize o ciclo de vida do colaborador',
			'solucao_pilares_1_desc'   => 'Orquestre admissões, movimentações e desligamentos entre o Senior e todos os sistemas que participam da jornada do colaborador.',
			'solucao_pilares_2_icone'  => $this->img( 'senior-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Conecte o Senior com segurança',
			'solucao_pilares_2_desc'   => 'Integre utilizando os webservices oficiais do Senior, preservando regras de negócio e reduzindo a necessidade de desenvolvimentos personalizados.',
			'solucao_pilares_3_icone'  => $this->img( 'senior-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Proteja dados sensíveis automaticamente',
			'solucao_pilares_3_desc'   => 'Mascare informações como CPF, salário e dados bancários durante a integração, mantendo conformidade e rastreabilidade.',

			// 3 · Casos de Uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize os principais processos do RH',
			'solucao_casos_1_icone'   => $this->img( 'senior-caso-1' ),
			'solucao_casos_1_titulo'  => 'Orquestre admissões e desligamentos',
			'solucao_casos_1_desc'    => 'Automatize a criação e revogação de acessos, benefícios e sistemas corporativos sempre que houver mudanças no quadro de colaboradores.',
			'solucao_casos_2_icone'   => $this->img( 'senior-caso-2' ),
			'solucao_casos_2_titulo'  => 'Integre folha e ponto',
			'solucao_casos_2_desc'    => 'Sincronize registros do ponto eletrônico com a folha de pagamento para reduzir inconsistências e retrabalho operacional.',
			'solucao_casos_3_icone'   => $this->img( 'senior-caso-3' ),
			'solucao_casos_3_titulo'  => 'Automatize a gestão de benefícios',
			'solucao_casos_3_desc'    => 'Integre fornecedores de VR, VA, plano de saúde e demais benefícios diretamente ao Senior.',
			'solucao_casos_4_icone'   => $this->img( 'senior-caso-4' ),
			'solucao_casos_4_titulo'  => 'Centralize indicadores de RH',
			'solucao_casos_4_desc'    => 'Consolide dados de headcount, admissões, desligamentos e movimentações para alimentar plataformas de BI em tempo real.',
			'solucao_casos_5_icone'   => $this->img( 'senior-caso-5' ),
			'solucao_casos_5_titulo'  => 'Revogue acessos automaticamente',
			'solucao_casos_5_desc'    => 'Garanta que acessos físicos e digitais sejam removidos automaticamente durante o desligamento do colaborador.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',

			// 4 · Selos.
			'solucao_selos_eyebrow'   => 'compliance & segurança',
			'solucao_selos_titulo'    => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'     => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',

			// 5 · Diferencial Técnico.
			'solucao_dif_eyebrow'     => 'diferencial técnico',
			'solucao_dif_titulo'      => 'Segurança para dados críticos de RH',
			'solucao_dif_corpo'       => 'A plataforma identifica e mascara automaticamente informações sensíveis do Senior antes que elas trafeguem entre sistemas, mantendo auditoria e conformidade durante toda a integração.',
			'solucao_dif_topico_1'    => 'Mascare CPF e salários automaticamente.',
			'solucao_dif_topico_2'    => 'Proteja dados bancários em trânsito.',
			'solucao_dif_topico_3'    => 'Audite todas as integrações realizadas.',
			'solucao_dif_imagem'      => $this->img( 'senior-dif' ),

			// 6 · Plataforma Única.
			'solucao_plat_eyebrow'    => 'plataforma única',
			'solucao_plat_titulo'     => 'Conecte todo o ecossistema do RH',
			'solucao_plat_corpo'      => 'Elimine integrações isoladas entre Senior, Active Directory, benefícios, ponto, LMS e demais sistemas utilizando uma única plataforma de integração com gestão centralizada.',
			'solucao_plat_topico_1'   => 'Centralize integrações do RH.',
			'solucao_plat_topico_2'   => 'Reduza dependência da equipe de TI.',
			'solucao_plat_topico_3'   => 'Ajuste fluxos com mais agilidade.',
			'solucao_plat_imagem'     => $this->img( 'senior-plataforma' ),

			// 7 · Aceleradores.
			'solucao_acel_eyebrow'    => 'Aceleradores de integração',
			'solucao_acel_titulo'     => 'Comece com fluxos prontos de RH',
			'solucao_acel_corpo'      => 'Utilize um modelo pronto para automatizar admissões, movimentações e desligamentos, reduzindo o tempo de implantação e acelerando novos projetos.',
			'solucao_acel_topico_1'   => 'Implante fluxos JML rapidamente.',
			'solucao_acel_topico_2'   => 'Reutilize modelos já validados.',
			'solucao_acel_topico_3'   => 'Adapte processos ao seu RH.',
			'solucao_acel_topico_4'   => 'E muito mais...',
			'solucao_acel_btn_texto'  => 'Começar agora',
			'solucao_acel_btn_url'    => '/contato/',
			'solucao_acel_imagem'     => $this->img( 'senior-aceleradores' ),
		);
		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}
		$this->preencher_senior_faq( $post_id );
		WP_CLI::log( "  Senior preenchido (ID: {$post_id})." );
	}

	protected function preencher_solucao_sankhya() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:sankhya',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "sankhya" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu Sankhya',
			'solucao_hero_titulo'     => 'Integre o Sankhya com todo o seu ecossistema sem abrir mão da governança',
			'solucao_hero_corpo'      => 'Conecte o Sankhya a CRM, e-commerce, bancos e sistemas fiscais utilizando a API Gateway oficial para automatizar processos, preservar as regras do ERP e eliminar integrações paralelas.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucoes/tecnologia/sankhya/',
			'solucao_hero_imagem'     => $this->img( 'sankhya-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Conecte o Sankhya com segurança e escalabilidade',
			'solucao_pilares_1_icone'  => $this->img( 'sankhya-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Utilize a API Gateway oficial',
			'solucao_pilares_1_desc'   => 'Integre o Sankhya utilizando os serviços oficiais da plataforma, preservando regras de negócio e evitando acessos diretos ao banco de dados.',
			'solucao_pilares_2_icone'  => $this->img( 'sankhya-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Respeite a governança do ERP',
			'solucao_pilares_2_desc'   => 'Garanta que todas as integrações utilizem a camada de autorização nativa do Sankhya, mantendo controle e segurança sobre os dados.',
			'solucao_pilares_3_icone'  => $this->img( 'sankhya-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Elimine integrações paralelas',
			'solucao_pilares_3_desc'   => 'Centralize as conexões entre ERP, CRM e e-commerce para reduzir retrabalho, facilitar manutenções e acelerar novos projetos.',

			// 3 · Casos de Uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize os principais processos do Sankhya',
			'solucao_casos_1_icone'   => $this->img( 'sankhya-caso-1' ),
			'solucao_casos_1_titulo'  => 'Sincronize pedidos do e-commerce',
			'solucao_casos_1_desc'    => 'Envie pedidos automaticamente para o Sankhya utilizando a API Gateway oficial, reduzindo retrabalho e acelerando o faturamento.',
			'solucao_casos_2_icone'   => $this->img( 'sankhya-caso-2' ),
			'solucao_casos_2_titulo'  => 'Atualize produtos e estoques',
			'solucao_casos_2_desc'    => 'Disponibilize produtos e saldos do Sankhya para canais de venda em tempo real utilizando os datasets oficiais.',
			'solucao_casos_3_icone'   => $this->img( 'sankhya-caso-3' ),
			'solucao_casos_3_titulo'  => 'Automatize processos financeiros',
			'solucao_casos_3_desc'    => 'Integre contas a receber, bancos e conciliação financeira utilizando as entidades financeiras do Sankhya.',
			'solucao_casos_4_icone'   => $this->img( 'sankhya-caso-4' ),
			'solucao_casos_4_titulo'  => 'Conecte CRM e ERP',
			'solucao_casos_4_desc'    => 'Sincronize leads, clientes e oportunidades entre o CRM e o Sankhya para eliminar digitação manual e manter informações atualizadas.',
			'solucao_casos_5_icone'   => $this->img( 'sankhya-caso-5' ),
			'solucao_casos_5_titulo'  => 'Disponibilize dados para IA',
			'solucao_casos_5_desc'    => 'Exponha informações do Sankhya para agentes de Inteligência Artificial utilizando APIs e servidores MCP com governança.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',

			// 4 · Selos.
			'solucao_selos_eyebrow'   => 'compliance & segurança',
			'solucao_selos_titulo'    => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'     => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',

			// 5 · Diferencial Técnico.
			'solucao_dif_eyebrow'     => 'diferencial técnico',
			'solucao_dif_titulo'      => 'Integrações que respeitam a arquitetura do Sankhya',
			'solucao_dif_corpo'       => 'Todas as integrações utilizam a camada de autorização nativa do Sankhya por meio do usuário de integração e permissões explícitas, evitando acessos diretos ao banco de dados e preservando a governança do ERP.',
			'solucao_dif_topico_1'    => 'Utilize a API Gateway oficial.',
			'solucao_dif_topico_2'    => 'Respeite permissões por entidade.',
			'solucao_dif_topico_3'    => 'Evite acesso direto ao banco.',
			'solucao_dif_imagem'      => $this->img( 'sankhya-dif' ),

			// 6 · Plataforma Única.
			'solucao_plat_eyebrow'    => 'plataforma única',
			'solucao_plat_titulo'     => 'Centralize todas as integrações do Sankhya',
			'solucao_plat_corpo'      => 'Empresas em crescimento costumam acumular integrações entre CRM, e-commerce e aplicativos de vendas. Utilize uma única plataforma para centralizar a governança e reutilizar integrações sem multiplicar projetos.',
			'solucao_plat_topico_1'   => 'Centralize toda a governança.',
			'solucao_plat_topico_2'   => 'Reutilize integrações existentes.',
			'solucao_plat_topico_3'   => 'Reduza integrações ponto a ponto.',
			'solucao_plat_imagem'     => $this->img( 'sankhya-plataforma' ),

			// 7 · Aceleradores.
			'solucao_acel_eyebrow'    => 'Aceleradores de integração',
			'solucao_acel_titulo'     => 'Comece com integrações já prontas',
			'solucao_acel_corpo'      => 'Utilize um modelo pré-configurado para sincronizar pedidos e clientes entre CRM, e-commerce e Sankhya, reduzindo o tempo de implantação e acelerando novos projetos.',
			'solucao_acel_topico_1'   => 'Implante pedidos rapidamente.',
			'solucao_acel_topico_2'   => 'Reutilize modelos validados.',
			'solucao_acel_topico_3'   => 'Adapte fluxos ao seu negócio.',
			'solucao_acel_topico_4'   => 'E muito mais...',
			'solucao_acel_btn_texto'  => 'Começar agora',
			'solucao_acel_btn_url'    => '/contato/',
			'solucao_acel_imagem'     => $this->img( 'sankhya-aceleradores' ),
		);
		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}
		$this->preencher_sankhya_faq( $post_id );
		WP_CLI::log( "  Sankhya preenchido (ID: {$post_id})." );
	}

	protected function preencher_solucao_dynamics365() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:dynamics-365',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "dynamics-365" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu Microsoft Dynamics',
			'solucao_hero_titulo'     => 'Integre o Microsoft Dynamics 365 sem ficar limitado ao Power Platform',
			'solucao_hero_corpo'      => 'Conecte Dynamics 365, Business Central e Finance & Operations ao restante da sua operação utilizando uma única plataforma para automatizar processos, compartilhar dados e eliminar integrações isoladas.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucoes/tecnologia/dynamics-365/',
			'solucao_hero_imagem'     => $this->img( 'dynamics-365-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Conecte todo o ecossistema Microsoft',
			'solucao_pilares_1_icone'  => $this->img( 'dynamics-365-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Utilize APIs nativas do Dynamics',
			'solucao_pilares_1_desc'   => 'Integre utilizando OData e Dynamics 365 Web API para preservar a arquitetura da Microsoft e acelerar novos projetos.',
			'solucao_pilares_2_icone'  => $this->img( 'dynamics-365-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Conecte diferentes sistemas',
			'solucao_pilares_2_desc'   => 'Orquestre Dynamics, SAP, Salesforce, Totvs e outras aplicações corporativas em uma única plataforma de integração.',
			'solucao_pilares_3_icone'  => $this->img( 'dynamics-365-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Reduza a dependência do Power Platform',
			'solucao_pilares_3_desc'   => 'Utilize uma camada central de integração para cenários corporativos complexos, mantendo flexibilidade e escalabilidade.',

			// 3 · Casos de uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize os principais processos do Dynamics',
			'solucao_casos_1_icone'   => $this->img( 'dynamics-365-caso-1' ),
			'solucao_casos_1_titulo'  => 'Sincronize CRM e ERP',
			'solucao_casos_1_desc'    => 'Compartilhe oportunidades, contas e clientes entre o Dynamics CRM e ERPs corporativos automaticamente.',
			'solucao_casos_2_icone'   => $this->img( 'dynamics-365-caso-2' ),
			'solucao_casos_2_titulo'  => 'Automatize processos financeiros',
			'solucao_casos_2_desc'    => 'Integre o Dynamics 365 Finance & Operations a bancos e plataformas de conciliação financeira.',
			'solucao_casos_3_icone'   => $this->img( 'dynamics-365-caso-3' ),
			'solucao_casos_3_titulo'  => 'Conecte Business Central ao e-commerce',
			'solucao_casos_3_desc'    => 'Sincronize pedidos, clientes e estoques entre o Business Central e seus canais de venda.',
			'solucao_casos_4_icone'   => $this->img( 'dynamics-365-caso-4' ),
			'solucao_casos_4_titulo'  => 'Centralize dados mestres',
			'solucao_casos_4_desc'    => 'Mantenha clientes, produtos e cadastros sincronizados entre o Dynamics e outros sistemas corporativos.',
			'solucao_casos_5_icone'   => $this->img( 'dynamics-365-caso-5' ),
			'solucao_casos_5_titulo'  => 'Disponibilize dados para Inteligência Artificial',
			'solucao_casos_5_desc'    => 'Exponha informações do Dynamics para agentes de IA utilizando APIs governadas e servidores MCP.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',

			// 4 · Selos.
			'solucao_selos_eyebrow'   => 'compliance & segurança',
			'solucao_selos_titulo'    => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'     => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',

			// 5 · Diferencial técnico.
			'solucao_dif_eyebrow'     => 'diferencial técnico',
			'solucao_dif_titulo'      => 'Segurança corporativa integrada ao ecossistema Microsoft',
			'solucao_dif_corpo'       => 'As integrações utilizam autenticação via Azure AD (Microsoft Entra ID) com OAuth2 e suporte a ambientes multi-tenant, preservando os padrões de segurança do Dynamics 365.',
			'solucao_dif_topico_1'    => 'Utilize autenticação via Azure AD.',
			'solucao_dif_topico_2'    => 'Suporte ambientes Dynamics multi-tenant.',
			'solucao_dif_topico_3'    => 'Proteja integrações com OAuth2.',
			'solucao_dif_imagem'      => $this->img( 'dynamics-365-dif' ),

			// 6 · Plataforma única.
			'solucao_plat_eyebrow'    => 'plataforma única',
			'solucao_plat_titulo'     => 'Uma plataforma para todo o seu ambiente Microsoft',
			'solucao_plat_corpo'      => 'Empresas que utilizam Dynamics frequentemente convivem com outros ERPs e CRMs. Centralize todas as integrações em uma única plataforma para simplificar projetos, aquisições e operações multi-ERP.',
			'solucao_plat_topico_1'   => 'Conecte Dynamics e outros ERPs.',
			'solucao_plat_topico_2'   => 'Elimine silos de integração.',
			'solucao_plat_topico_3'   => 'Simplifique cenários de M&A.',
			'solucao_plat_imagem'     => $this->img( 'dynamics-365-plataforma' ),

			// 7 · Aceleradores.
			'solucao_acel_eyebrow'    => 'Aceleradores de integração',
			'solucao_acel_titulo'     => 'Comece utilizando integrações prontas',
			'solucao_acel_corpo'      => 'Implemente rapidamente um modelo pré-configurado para sincronizar contas, oportunidades e pedidos entre o Dynamics 365 e sistemas externos utilizando OData e Web API.',
			'solucao_acel_topico_1'   => 'Implante integrações rapidamente.',
			'solucao_acel_topico_2'   => 'Reutilize modelos já validados.',
			'solucao_acel_topico_3'   => 'Adapte fluxos ao seu negócio.',
			'solucao_acel_topico_4'   => 'E muito mais...',
			'solucao_acel_btn_texto'  => 'Começar agora',
			'solucao_acel_btn_url'    => '/contato/',
			'solucao_acel_imagem'     => $this->img( 'dynamics-365-aceleradores' ),
		);
		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		// 8 · FAQ.
		$this->preencher_dynamics365_faq( $post_id );

		WP_CLI::log( "  Microsoft Dynamics 365 preenchido (ID: {$post_id})." );
	}

	protected function preencher_dynamics365_faq( $post_id ) {
		$itens = array(
			array(
				'faq:dynamics365-business-central',
				'A CLI Connect funciona com Dynamics 365 Business Central e Finance & Operations ao mesmo tempo?',
				'<p>Sim. A CLI Connect suporta múltiplos produtos da família Dynamics 365 em paralelo. Cada produto é configurado como uma conexão independente na plataforma, permitindo orquestrar dados entre Business Central, Finance & Operations e outros sistemas corporativos em um único projeto de integração.</p>',
			),
			array(
				'faq:dynamics365-autenticacao',
				'Como a CLI Connect se autentica no Microsoft Dynamics?',
				'<p>A autenticação é realizada via Azure AD (Microsoft Entra ID) utilizando OAuth2 com credenciais de aplicativo registrado. Esse modelo garante que nenhuma senha de usuário seja armazenada e que os acessos possam ser auditados e revogados centralmente pelo administrador do tenant.</p>',
			),
			array(
				'faq:dynamics365-power-automate',
				'É possível substituir integrações desenvolvidas em Power Automate?',
				'<p>Sim. A CLI Connect oferece uma camada de integração corporativa que substitui fluxos do Power Automate em cenários de alta volume, lógica complexa ou necessidade de governança centralizada. A migração é feita de forma gradual, sem interrupção das operações.</p>',
			),
		);

		$ids = array();
		foreach ( $itens as $ordem => list( $slug, $pergunta, $resposta ) ) {
			$ids[] = (int) $this->upsert( $slug, array(
				'post_type'    => 'cli_faq',
				'post_title'   => $pergunta,
				'post_content' => $resposta,
				'menu_order'   => $ordem,
			) );
		}
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens',  $ids, $post_id );
		WP_CLI::log( sprintf( '  Dynamics 365 FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	protected function preencher_solucao_rd_station() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:rd-station-crm',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "rd-station-crm" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu RD Station',
			'solucao_hero_titulo'     => 'Conecte o RD Station CRM ao ERP e ao marketing sem depender de planilhas',
			'solucao_hero_corpo'      => 'Automatize a conexão entre vendas, ERP, e-commerce e ferramentas de marketing para manter dados sincronizados, eliminar retrabalho manual e acompanhar o crescimento da operação comercial.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/rd-station-crm/',
			'solucao_hero_imagem'     => $this->img( 'rd-station-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Escale sua operação comercial conectada',
			'solucao_pilares_1_icone'  => $this->img( 'rd-station-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Sincronize vendas com ERP',
			'solucao_pilares_1_desc'   => 'Envie negócios ganhos automaticamente ao ERP e elimine a digitação manual de pedidos, notas e cadastros de clientes.',
			'solucao_pilares_2_icone'  => $this->img( 'rd-station-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Enriqueça leads automaticamente',
			'solucao_pilares_2_desc'   => 'Complemente dados de leads do RD Station com informações do ERP e de outras fontes para qualificar melhor cada oportunidade.',
			'solucao_pilares_3_icone'  => $this->img( 'rd-station-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Conecte sistemas em crescimento',
			'solucao_pilares_3_desc'   => 'Integre o RD Station CRM a novas ferramentas conforme a operação cresce, sem reescrever integrações existentes.',

			// 3 · Casos de uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize processos do ciclo comercial',
			'solucao_casos_1_icone'   => $this->img( 'rd-station-caso-1' ),
			'solucao_casos_1_titulo'  => 'Envie negócios ganhos ao ERP',
			'solucao_casos_1_desc'    => 'Ao fechar um negócio no RD Station CRM, dispare automaticamente a criação do pedido ou contrato no ERP sem intervenção manual.',
			'solucao_casos_2_icone'   => $this->img( 'rd-station-caso-2' ),
			'solucao_casos_2_titulo'  => 'Enriqueça dados de leads',
			'solucao_casos_2_desc'    => 'Sincronize informações de clientes entre o CRM e o ERP para manter histórico comercial e financeiro em um único registro.',
			'solucao_casos_3_icone'   => $this->img( 'rd-station-caso-3' ),
			'solucao_casos_3_titulo'  => 'Conecte histórico de compras',
			'solucao_casos_3_desc'    => 'Disponibilize dados de pedidos e faturas do ERP diretamente no RD Station CRM para que vendedores acompanhem o histórico de cada conta.',
			'solucao_casos_4_icone'   => $this->img( 'rd-station-caso-4' ),
			'solucao_casos_4_titulo'  => 'Consolide dados para BI',
			'solucao_casos_4_desc'    => 'Unifique métricas de vendas do CRM com dados financeiros do ERP em dashboards de inteligência de negócios.',
			'solucao_casos_5_icone'   => $this->img( 'rd-station-caso-5' ),
			'solucao_casos_5_titulo'  => 'Distribua leads automaticamente',
			'solucao_casos_5_desc'    => 'Roteie leads qualificados para vendedores com base em regras de território, segmento ou capacidade de atendimento.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',

			// 4 · Selos.
			'solucao_selos_eyebrow'   => 'compliance & segurança',
			'solucao_selos_titulo'    => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'     => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',

			// 5 · Diferencial técnico.
			'solucao_dif_eyebrow'     => 'diferencial técnico',
			'solucao_dif_titulo'      => 'Integrações seguras com API oficial',
			'solucao_dif_corpo'       => 'As integrações utilizam a API REST oficial do RD Station CRM com autenticação por OAuth2 e tokens individuais por integração, garantindo rastreabilidade e controle de acesso granular.',
			'solucao_dif_topico_1'    => 'Utilize API REST oficial do RD Station.',
			'solucao_dif_topico_2'    => 'Controle acessos por permissões.',
			'solucao_dif_topico_3'    => 'Proteja conexões com tokens individuais.',
			'solucao_dif_imagem'      => $this->img( 'rd-station-dif' ),

			// 6 · Plataforma única.
			'solucao_plat_eyebrow'    => 'plataforma única',
			'solucao_plat_titulo'     => 'Centralize suas conexões comerciais em uma única plataforma',
			'solucao_plat_corpo'      => 'Empresas que crescem adotam novas ferramentas ao longo do tempo. Centralize todas as integrações do RD Station CRM em uma plataforma única para simplificar a gestão e escalar sem retrabalho.',
			'solucao_plat_topico_1'   => 'Conecte CRM e ERP em escala.',
			'solucao_plat_topico_2'   => 'Reduza processos manuais com automações.',
			'solucao_plat_topico_3'   => 'Evolua sistemas sem trocar ferramentas.',
			'solucao_plat_imagem'     => $this->img( 'rd-station-plat' ),

			// 7 · Aceleradores.
			'solucao_acel_eyebrow'    => 'Aceleradores de integração',
			'solucao_acel_titulo'     => 'Comece com uma integração pronta',
			'solucao_acel_corpo'      => 'Implante fluxos comerciais já validados para sincronizar negócios ganhos, dados de clientes e histórico de compras entre o RD Station CRM e o seu ERP.',
			'solucao_acel_topico_1'   => 'Implante fluxos comerciais validados.',
			'solucao_acel_topico_2'   => 'Sincronize vendas automaticamente.',
			'solucao_acel_topico_3'   => 'Adapte regras ao seu processo.',
			'solucao_acel_topico_4'   => 'E muito mais...',
			'solucao_acel_btn_texto'  => 'Começar agora',
			'solucao_acel_btn_url'    => '/contato/',
			'solucao_acel_imagem'     => $this->img( 'rd-station-acel' ),
		);
		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		// 8 · FAQ.
		$this->preencher_rd_station_faq( $post_id );

		WP_CLI::log( "  RD Station CRM preenchido (ID: {$post_id})." );
	}

	protected function preencher_rd_station_faq( $post_id ) {
		$itens = array(
			array(
				'faq:rd-station-crm-erp',
				'Como sincronizar negócios fechados diretamente com o ERP?',
				'<p>Ao fechar um negócio no RD Station CRM, a CLI Connect detecta o evento via webhook e aciona automaticamente o fluxo de integração configurado — criando o pedido, contrato ou cadastro de cliente no ERP sem intervenção manual. O mapeamento de campos é definido uma vez e pode ser ajustado conforme as regras do seu processo comercial.</p>',
			),
			array(
				'faq:rd-station-crm-multiplas-contas',
				'É possível conectar múltiplas contas RD Station de diferentes unidades de negócio?',
				'<p>Sim. A CLI Connect suporta múltiplas conexões simultâneas com contas distintas do RD Station CRM. Cada unidade de negócio opera com seu próprio conjunto de credenciais e fluxos independentes, centralizados em uma única plataforma de integração para facilitar a governança.</p>',
			),
			array(
				'faq:rd-station-crm-rate-limit',
				'Como lidar com limites de taxa da API do RD Station CRM?',
				'<p>A CLI Connect gerencia automaticamente os limites de taxa da API do RD Station CRM por meio de filas e mecanismos de retry com backoff exponencial. Em picos de volume — como importações em lote ou campanhas de grande escala — os dados são processados de forma controlada, sem erros ou perda de registros.</p>',
			),
		);

		$ids = array();
		foreach ( $itens as $ordem => list( $slug, $pergunta, $resposta ) ) {
			$ids[] = (int) $this->upsert( $slug, array(
				'post_type'    => 'cli_faq',
				'post_title'   => $pergunta,
				'post_content' => $resposta,
				'menu_order'   => $ordem,
			) );
		}
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens',  $ids, $post_id );
		WP_CLI::log( sprintf( '  RD Station CRM FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	protected function preencher_solucao_rd_station_marketing() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:rd-station-marketing',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "rd-station-marketing" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu RD Station Marketing',
			'solucao_hero_titulo'     => 'Automatize seu marketing com dados de vendas e produto em tempo real',
			'solucao_hero_corpo'      => 'Conecte o RD Station Marketing ao CRM, ERP e ferramentas de analytics para transformar leads em oportunidades com dados atualizados em cada etapa do funil comercial.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/rd-station-marketing/',
			'solucao_hero_imagem'     => $this->img( 'rd-station-marketing-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Conecte marketing ao ciclo comercial',
			'solucao_pilares_1_icone'  => $this->img( 'rd-station-marketing-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Sincronize leads qualificados',
			'solucao_pilares_1_desc'   => 'Envie MQLs e SQLs automaticamente para o CRM de vendas sem atrasos ou processos manuais.',
			'solucao_pilares_2_icone'  => $this->img( 'rd-station-marketing-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Capture eventos de conversão',
			'solucao_pilares_2_desc'   => 'Use webhooks e API REST para reagir rapidamente a interações relevantes dos clientes.',
			'solucao_pilares_3_icone'  => $this->img( 'rd-station-marketing-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Unifique dados do funil',
			'solucao_pilares_3_desc'   => 'Conecte marketing, vendas e receita para acompanhar toda jornada até o fechamento.',

			// 3 · Casos de uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize processos de marketing e vendas',
			'solucao_casos_1_icone'   => $this->img( 'rd-station-marketing-caso-1' ),
			'solucao_casos_1_titulo'  => 'Envie MQLs ao CRM',
			'solucao_casos_1_desc'    => 'Sincronize leads qualificados do RD Station Marketing com o CRM em tempo real.',
			'solucao_casos_2_icone'   => $this->img( 'rd-station-marketing-caso-2' ),
			'solucao_casos_2_titulo'  => 'Dispare automações por eventos',
			'solucao_casos_2_desc'    => 'Acione fluxos de marketing a partir de ações de produto ou vendas.',
			'solucao_casos_3_icone'   => $this->img( 'rd-station-marketing-caso-3' ),
			'solucao_casos_3_titulo'  => 'Meça atribuição de campanhas',
			'solucao_casos_3_desc'    => 'Conecte campanhas ao CRM e ERP para acompanhar impacto até a receita.',
			'solucao_casos_4_icone'   => $this->img( 'rd-station-marketing-caso-4' ),
			'solucao_casos_4_titulo'  => 'Enriqueça dados de leads',
			'solucao_casos_4_desc'    => 'Combine informações externas para criar perfis comerciais mais completos.',
			'solucao_casos_5_icone'   => $this->img( 'rd-station-marketing-caso-5' ),
			'solucao_casos_5_titulo'  => 'Remova clientes convertidos',
			'solucao_casos_5_desc'    => 'Retire automaticamente contatos vendidos das réguas de nutrição.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',

			// 4 · Selos.
			'solucao_selos_eyebrow' => 'compliance & segurança',
			'solucao_selos_titulo'  => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'   => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',

			// 5 · Diferencial técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações confiáveis via API oficial',
			'solucao_dif_corpo'    => 'Conecte o RD Station Marketing utilizando webhooks e API REST com deduplicação de contatos para manter marketing e vendas alinhados.',
			'solucao_dif_topico_1' => 'Utilize webhooks para eventos rápidos',
			'solucao_dif_topico_2' => 'Conecte via API REST oficial',
			'solucao_dif_topico_3' => 'Evite duplicidade entre contatos',
			'solucao_dif_imagem'   => $this->img( 'rd-station-marketing-dif' ),

			// 6 · Plataforma única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Conecte todo o funil comercial em uma plataforma',
			'solucao_plat_corpo'    => 'Marketing e vendas perdem eficiência quando trabalham com dados desconectados. Centralize integrações para acompanhar o cliente do primeiro clique ao pedido faturado.',
			'solucao_plat_topico_1' => 'Unifique dados de marketing e vendas',
			'solucao_plat_topico_2' => 'Elimine cruzamentos manuais de planilhas',
			'solucao_plat_topico_3' => 'Conecte toda jornada comercial',
			'solucao_plat_imagem'   => $this->img( 'rd-station-marketing-plat' ),

			// 7 · Aceleradores.
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com leads já estruturados',
			'solucao_acel_corpo'     => 'Utilize um modelo pronto para sincronizar leads qualificados do RD Station Marketing com qualquer CRM e acelere a passagem entre marketing e vendas.',
			'solucao_acel_topico_1'  => 'Conecte MQLs automaticamente',
			'solucao_acel_topico_2'  => 'Reutilize fluxos já validados',
			'solucao_acel_topico_3'  => 'Adapte regras ao seu processo',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'rd-station-marketing-acel' ),
		);

		foreach ( $campos as $campo => $valor ) {
			update_field( $campo, $valor, $post_id );
		}

		// 8 · FAQ.
		$this->preencher_rd_station_marketing_faq( $post_id );

		WP_CLI::log( "  RD Station Marketing preenchido (ID: {$post_id})." );
	}

	protected function preencher_rd_station_marketing_faq( $post_id ) {
		$itens = array(
			array(
				'faq:rd-station-marketing-nutricao',
				'Como evitar que um lead já fechado continue recebendo e-mails de nutrição?',
				'<p>A CLI Connect pode acionar automaticamente a remoção do lead das listas ativas do RD Station Marketing ao detectar um negócio ganho ou cliente convertido no CRM. Dessa forma, contatos que já fecharam uma venda deixam de receber fluxos de nutrição automaticamente, sem intervenção manual da equipe de marketing.</p>',
			),
			array(
				'faq:rd-station-marketing-atribuicao',
				'É possível medir atribuição de campanha até o fechamento no ERP?',
				'<p>Sim. A CLI Connect conecta os dados de campanha do RD Station Marketing com os registros de venda e faturamento do ERP. Com isso, é possível rastrear a jornada do lead desde o primeiro clique em uma campanha até o pedido faturado, gerando visibilidade sobre o ROI real de cada ação de marketing.</p>',
			),
			array(
				'faq:rd-station-marketing-webhooks',
				'Como funciona a integração via webhooks em tempo real?',
				'<p>O RD Station Marketing envia eventos via webhook assim que uma ação ocorre — formulário preenchido, lead qualificado, automação concluída. A CLI Connect recebe esses eventos, valida o payload e aciona os fluxos configurados de forma imediata, sem necessidade de polling. A latência média é de segundos, garantindo que os dados cheguem ao CRM ou ERP praticamente em tempo real.</p>',
			),
		);

		$ids = array();
		foreach ( $itens as $ordem => list( $slug, $pergunta, $resposta ) ) {
			$ids[] = (int) $this->upsert( $slug, array(
				'post_type'    => 'cli_faq',
				'post_title'   => $pergunta,
				'post_content' => $resposta,
				'menu_order'   => $ordem,
			) );
		}
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens',  $ids, $post_id );
		WP_CLI::log( sprintf( '  RD Station Marketing FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	protected function preencher_solucao_thomson_reuters_tax_one() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:thomson-reuters-tax-one',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "thomson-reuters-tax-one" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu Tax One',
			'solucao_hero_titulo'     => 'Centralize a gestão tributária e elimine riscos fiscais na sua empresa',
			'solucao_hero_corpo'      => 'Conecte facilmente o ecossistema da sua empresa ao Thomson Reuters Tax One. Unifique a apuração de impostos, simplifique as obrigações acessórias e garanta segurança fiscal.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/thomson-reuters-tax-one/',
			'solucao_hero_imagem'     => $this->img( 'thomson-reuters-tax-one-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Centralize o cálculo fiscal da sua operação',
			'solucao_pilares_1_icone'  => $this->img( 'thomson-reuters-tax-one-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Centralize regras tributárias',
			'solucao_pilares_1_desc'   => 'Aplique o mesmo motor de cálculo fiscal em todos os sistemas que geram documentos na empresa.',
			'solucao_pilares_2_icone'  => $this->img( 'thomson-reuters-tax-one-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Reduza divergências fiscais',
			'solucao_pilares_2_desc'   => 'Mantenha ERP, e-commerce e faturamento alinhados com cálculos tributários consistentes.',
			'solucao_pilares_3_icone'  => $this->img( 'thomson-reuters-tax-one-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Audite cada cálculo realizado',
			'solucao_pilares_3_desc'   => 'Tenha rastreabilidade completa de todas as chamadas feitas ao motor fiscal.',

			// 3 · Casos de uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize os principais processos fiscais',
			'solucao_casos_1_icone'   => $this->img( 'thomson-reuters-tax-one-caso-1' ),
			'solucao_casos_1_titulo'  => 'Calcule impostos no checkout',
			'solucao_casos_1_desc'    => 'Consulte o motor fiscal em tempo real durante compras no e-commerce para aplicar tributos corretamente.',
			'solucao_casos_2_icone'   => $this->img( 'thomson-reuters-tax-one-caso-2' ),
			'solucao_casos_2_titulo'  => 'Conecte múltiplos ERPs',
			'solucao_casos_2_desc'    => 'Centralize o cálculo fiscal entre SAP, Totvs, Dynamics e outros ERPs da organização.',
			'solucao_casos_3_icone'   => $this->img( 'thomson-reuters-tax-one-caso-3' ),
			'solucao_casos_3_titulo'  => 'Reprocesse documentos fiscais',
			'solucao_casos_3_desc'    => 'Execute cálculos em lote para reconciliar documentos e corrigir inconsistências tributárias.',
			'solucao_casos_4_icone'   => $this->img( 'thomson-reuters-tax-one-caso-4' ),
			'solucao_casos_4_titulo'  => 'Atualize regras fiscais automaticamente',
			'solucao_casos_4_desc'    => 'Sincronize alterações tributárias entre o motor fiscal e sistemas de origem.',
			'solucao_casos_5_icone'   => $this->img( 'thomson-reuters-tax-one-caso-5' ),
			'solucao_casos_5_titulo'  => 'Centralize auditorias fiscais',
			'solucao_casos_5_desc'    => 'Acompanhe todas as consultas ao motor fiscal em uma trilha única de auditoria.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',

			// 4 · Selos.
			'solucao_selos_eyebrow' => 'compliance & segurança',
			'solucao_selos_titulo'  => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'   => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',

			// 5 · Diferencial técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Audite cada cálculo com segurança fiscal',
			'solucao_dif_corpo'    => 'Centralize todas as chamadas ao motor fiscal com controle de acesso por sistema de origem e rastreabilidade completa para compliance tributário.',
			'solucao_dif_topico_1' => 'Registre todas as chamadas fiscais',
			'solucao_dif_topico_2' => 'Controle acessos por sistema origem',
			'solucao_dif_topico_3' => 'Garanta rastreabilidade para auditorias',
			'solucao_dif_imagem'   => $this->img( 'thomson-reuters-tax-one-dif' ),

			// 6 · Plataforma única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Unifique cálculos fiscais em uma plataforma',
			'solucao_plat_corpo'    => 'Empresas com múltiplos ERPs precisam garantir a mesma regra tributária em todos os pontos de emissão. Centralize conexões e reduza riscos de cálculos inconsistentes.',
			'solucao_plat_topico_1' => 'Centralize regras entre diferentes ERPs',
			'solucao_plat_topico_2' => 'Padronize cálculos entre unidades',
			'solucao_plat_topico_3' => 'Reduza riscos de autuações fiscais',
			'solucao_plat_imagem'   => $this->img( 'thomson-reuters-tax-one-plat' ),

			// 7 · Aceleradores.
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com um modelo fiscal pronto',
			'solucao_acel_corpo'     => 'Utilize um template de cálculo tributário centralizado para conectar checkout, ERP e motor fiscal com mais velocidade.',
			'solucao_acel_topico_1'  => 'Conecte múltiplos ERPs rapidamente',
			'solucao_acel_topico_2'  => 'Reutilize fluxos fiscais validados',
			'solucao_acel_topico_3'  => 'Acelere novas integrações tributárias',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'thomson-reuters-tax-one-acel' ),
		);

		foreach ( $campos as $campo => $valor ) {
			update_field( $campo, $valor, $post_id );
		}

		// 8 · FAQ.
		$this->preencher_thomson_reuters_tax_one_faq( $post_id );

		WP_CLI::log( "  Thomson Reuters Tax One preenchido (ID: {$post_id})." );
	}

	protected function preencher_thomson_reuters_tax_one_faq( $post_id ) {
		$itens = array(
			array(
				'faq:tax-one-divergencia-calculo',
				'Como evitar divergência de cálculo fiscal entre ERP e e-commerce?',
				'<p>A CLI Connect centraliza todas as chamadas ao motor do Tax One em um único ponto de integração. Tanto o ERP quanto o e-commerce consultam o mesmo motor fiscal, garantindo que o tributo calculado no checkout seja idêntico ao registrado na nota fiscal emitida pelo ERP, eliminando divergências de apuração.</p>',
			),
			array(
				'faq:tax-one-multiplos-erps',
				'É possível centralizar o motor fiscal para múltiplos ERPs?',
				'<p>Sim. A CLI Connect permite conectar diferentes ERPs — como SAP, TOTVS e Dynamics — ao mesmo motor do Tax One. Cada sistema realiza suas chamadas de cálculo de forma independente, mas todas passam pela mesma configuração de regras tributárias, garantindo consistência fiscal em toda a organização.</p>',
			),
			array(
				'faq:tax-one-auditoria-chamadas',
				'Como funciona a auditoria de chamadas ao motor de cálculo?',
				'<p>A CLI Connect registra cada chamada realizada ao Tax One, incluindo o sistema de origem, os parâmetros enviados, o resultado retornado e o timestamp da operação. Essa trilha de auditoria fica disponível para consulta, facilitando a comprovação de cálculos em processos de fiscalização tributária.</p>',
			),
		);

		$ids = array();
		foreach ( $itens as $ordem => list( $slug, $pergunta, $resposta ) ) {
			$ids[] = (int) $this->upsert( $slug, array(
				'post_type'    => 'cli_faq',
				'post_title'   => $pergunta,
				'post_content' => $resposta,
				'menu_order'   => $ordem,
			) );
		}
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens',  $ids, $post_id );
		WP_CLI::log( sprintf( '  Thomson Reuters Tax One FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	protected function preencher_solucao_freshservice() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:freshservice',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "freshservice" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu Freshservice',
			'solucao_hero_titulo'     => 'Potencialize o Freshservice criando processos completos sem novos módulos',
			'solucao_hero_corpo'      => 'Conecte o Freshservice aos sistemas internos para criar formulários, aprovações e catálogos de serviço que gravam diretamente no ERP, CRM ou bancos de dados, reduzindo custos de licenciamento.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/freshservice/',
			'solucao_hero_imagem'     => $this->img( 'freshservice-hero' ),
			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Transforme o Freshservice em uma plataforma de processos',
			'solucao_pilares_1_icone'  => $this->img( 'freshservice-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Crie processos dentro do Freshservice',
			'solucao_pilares_1_desc'   => 'Construa formulários, aprovações e catálogos conectados aos sistemas internos da empresa.',
			'solucao_pilares_2_icone'  => $this->img( 'freshservice-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Reduza licenças adicionais',
			'solucao_pilares_2_desc'   => 'Evite módulos extras para integrar processos do Freshservice com outras aplicações corporativas.',
			'solucao_pilares_3_icone'  => $this->img( 'freshservice-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Reutilize workflows criados',
			'solucao_pilares_3_desc'   => 'Transforme cada processo desenvolvido em um fluxo reutilizável para novas necessidades.',
			// 3 · Casos de uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize processos críticos pelo Freshservice',
			'solucao_casos_1_icone'   => $this->img( 'freshservice-caso-1' ),
			'solucao_casos_1_titulo'  => 'Solicite compras pelo Freshservice',
			'solucao_casos_1_desc'    => 'Crie formulários de compra que registram pedidos diretamente no ERP sem módulos adicionais.',
			'solucao_casos_2_icone'   => $this->img( 'freshservice-caso-2' ),
			'solucao_casos_2_titulo'  => 'Automatize acessos internos',
			'solucao_casos_2_desc'    => 'Conecte catálogo de serviços ao Active Directory ou Okta para provisionar acessos automaticamente.',
			'solucao_casos_3_icone'   => $this->img( 'freshservice-caso-3' ),
			'solucao_casos_3_titulo'  => 'Automatize onboarding de colaboradores',
			'solucao_casos_3_desc'    => 'Dispare admissões simultâneas em folha, e-mail e sistemas internos pelo Freshservice.',
			'solucao_casos_4_icone'   => $this->img( 'freshservice-caso-4' ),
			'solucao_casos_4_titulo'  => 'Abra tickets automaticamente',
			'solucao_casos_4_desc'    => 'Transforme eventos de monitoramento, RH e segurança em chamados no Service Desk.',
			'solucao_casos_5_icone'   => $this->img( 'freshservice-caso-5' ),
			'solucao_casos_5_titulo'  => 'Conecte agentes de IA',
			'solucao_casos_5_desc'    => 'Disponibilize tickets e processos do Freshservice para agentes inteligentes de suporte.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',
			// 4 · Selos.
			'solucao_selos_eyebrow' => 'compliance & segurança',
			'solucao_selos_titulo'  => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'   => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
			// 5 · Diferencial técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações seguras para processos críticos',
			'solucao_dif_corpo'    => 'Utilize APIs REST do Freshservice com autenticação segura e controle de acesso para conectar processos conforme políticas internas.',
			'solucao_dif_topico_1' => 'Utilize APIs REST oficiais',
			'solucao_dif_topico_2' => 'Controle acessos por departamento',
			'solucao_dif_topico_3' => 'Proteja conexões com API Key',
			'solucao_dif_imagem'   => $this->img( 'freshservice-dif' ),
			// 6 · Plataforma única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Centralize processos sem depender de módulos',
			'solucao_plat_corpo'    => 'Transforme o Freshservice na interface dos processos enquanto a plataforma de integração conecta e grava dados nos sistemas internos.',
			'solucao_plat_topico_1' => 'Centralize workflows corporativos',
			'solucao_plat_topico_2' => 'Evite novos licenciamentos do fornecedor',
			'solucao_plat_topico_3' => 'Conecte sistemas sem add-ons',
			'solucao_plat_imagem'   => $this->img( 'freshservice-plat' ),
			// 7 · Aceleradores.
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com processos já estruturados',
			'solucao_acel_corpo'     => 'Utilize um modelo pronto para conectar formulários e catálogos do Freshservice aos sistemas internos com mais velocidade.',
			'solucao_acel_topico_1'  => 'Conecte processos em poucos minutos',
			'solucao_acel_topico_2'  => 'Reutilize workflows já validados',
			'solucao_acel_topico_3'  => 'Adapte fluxos ao negócio',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'freshservice-acel' ),
		);

		foreach ( $campos as $campo => $valor ) {
			update_field( $campo, $valor, $post_id );
		}

		$this->preencher_freshservice_faq( $post_id );

		WP_CLI::log( "  Freshservice preenchido (ID: {$post_id})." );
	}

	protected function preencher_freshservice_faq( $post_id ) {
		$itens = array(
			array(
				'faq:freshservice-processo-sem-modulo',
				'É possível criar um processo de negócio no Freshservice sem comprar módulo adicional?',
				'<p>Sim. A CLI Connect permite criar formulários, aprovações e catálogos de serviço que se integram diretamente aos sistemas internos — como ERP e CRM — sem a necessidade de contratar módulos adicionais do Freshservice.</p>',
			),
			array(
				'faq:freshservice-formulario-grava-sistema',
				'Como um formulário do Freshservice grava diretamente em outro sistema interno?',
				'<p>A integração é feita via APIs REST do Freshservice. Quando um usuário submete um formulário, a CLI Connect aciona o fluxo de integração, que traduz e envia os dados para o sistema de destino — como SAP, Totvs ou Active Directory — em tempo real.</p>',
			),
			array(
				'faq:freshservice-abrir-tickets-automaticamente',
				'Como abrir tickets automaticamente a partir de outro sistema?',
				'<p>Eventos de sistemas externos — como alertas de monitoramento, eventos de RH ou ocorrências de segurança — disparam chamadas via API para a CLI Connect, que cria os tickets correspondentes no Freshservice com os dados e prioridades corretos.</p>',
			),
		);

		foreach ( $itens as [ $slug, $pergunta, $resposta ] ) {
			$faq_id = $this->upsert( $slug, array(
				'post_type'    => 'cli_faq',
				'post_title'   => $pergunta,
				'post_content' => $resposta,
				'post_status'  => 'publish',
			) );
		}

		$faq_ids = array();
		foreach ( $itens as [ $slug ] ) {
			$posts = get_posts( array(
				'post_type'  => 'cli_faq',
				'meta_key'   => self::META,
				'meta_value' => $slug,
				'fields'     => 'ids',
			) );
			if ( ! empty( $posts ) ) {
				$faq_ids[] = (int) $posts[0];
			}
		}

		if ( ! empty( $faq_ids ) ) {
			update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
			update_field( 'solucao_faq_itens', $faq_ids, $post_id );
			WP_CLI::log( '  Freshservice FAQ: ' . count( $faq_ids ) . ' perguntas vinculadas.' );
		}
	}

	protected function preencher_solucao_servicenow() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:servicenow',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "servicenow" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu ServiceNow',
			'solucao_hero_titulo'     => 'Potencialize o ServiceNow sem pagar por mais módulos',
			'solucao_hero_corpo'      => 'Construa processos completos no ServiceNow e conecte diretamente ERP, CRM e sistemas internos sem depender de módulos adicionais de integração.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/servicenow/',
			'solucao_hero_imagem'     => $this->img( 'servicenow-hero' ),
			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Transforme o ServiceNow em uma central de processos',
			'solucao_pilares_1_icone'  => $this->img( 'servicenow-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Crie processos conectados',
			'solucao_pilares_1_desc'   => 'Use catálogo, aprovações e fluxos ligados aos sistemas internos.',
			'solucao_pilares_2_icone'  => $this->img( 'servicenow-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Reduza custos de licenciamento',
			'solucao_pilares_2_desc'   => 'Evite módulos pagos para cada nova integração necessária.',
			'solucao_pilares_3_icone'  => $this->img( 'servicenow-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Orquestre ponta a ponta',
			'solucao_pilares_3_desc'   => 'Capture solicitações, valide dados e grave nos sistemas.',
			// 3 · Casos de uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize processos conectados ao ServiceNow',
			'solucao_casos_1_icone'   => $this->img( 'servicenow-caso-1' ),
			'solucao_casos_1_titulo'  => 'Automatize cadastro de produtos',
			'solucao_casos_1_desc'    => 'Crie aprovações no ServiceNow e grave dados no ERP.',
			'solucao_casos_2_icone'   => $this->img( 'servicenow-caso-2' ),
			'solucao_casos_2_titulo'  => 'Abra incidentes automaticamente',
			'solucao_casos_2_desc'    => 'Receba eventos de IA e sistemas diretamente no ServiceNow.',
			'solucao_casos_3_icone'   => $this->img( 'servicenow-caso-3' ),
			'solucao_casos_3_titulo'  => 'Sincronize a CMDB',
			'solucao_casos_3_desc'    => 'Conecte dados de infraestrutura sem Spokes adicionais.',
			'solucao_casos_4_icone'   => $this->img( 'servicenow-caso-4' ),
			'solucao_casos_4_titulo'  => 'Valide aprovações no ERP',
			'solucao_casos_4_desc'    => 'Consulte orçamento e estoque antes de aprovar mudanças.',
			'solucao_casos_5_icone'   => $this->img( 'servicenow-caso-5' ),
			'solucao_casos_5_titulo'  => 'Automatize acessos corporativos',
			'solucao_casos_5_desc'    => 'Dispare provisionamentos a partir de eventos de RH.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',
			// 4 · Selos.
			'solucao_selos_eyebrow' => 'compliance & segurança',
			'solucao_selos_titulo'  => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'   => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
			// 5 · Diferencial técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações ServiceNow com governança completa',
			'solucao_dif_corpo'    => 'Conecte o ServiceNow via API com autenticação segura e eventos bidirecionais mantendo auditoria centralizada sem depender de conectores pagos.',
			'solucao_dif_topico_1' => 'Utilize APIs oficiais do ServiceNow',
			'solucao_dif_topico_2' => 'Controle acessos por autenticação',
			'solucao_dif_topico_3' => 'Audite todos os eventos',
			'solucao_dif_imagem'   => $this->img( 'servicenow-dif' ),
			// 6 · Plataforma única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Conecte processos sem limitar crescimento',
			'solucao_plat_corpo'    => 'Use o ServiceNow para orquestrar experiências enquanto a CLI Connect conecta sistemas internos sem aumentar custos de licenciamento.',
			'solucao_plat_topico_1' => 'Centralize integrações corporativas',
			'solucao_plat_topico_2' => 'Reduza dependência de Spokes',
			'solucao_plat_topico_3' => 'Escale novos processos',
			'solucao_plat_imagem'   => $this->img( 'servicenow-plat' ),
			// 7 · Aceleradores.
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com processos já estruturados',
			'solucao_acel_corpo'     => 'Utilize um modelo pronto para criar processos no ServiceNow e gravar dados diretamente em ERP como Totvs ou SAP.',
			'solucao_acel_topico_1'  => 'Configure processos rapidamente',
			'solucao_acel_topico_2'  => 'Adapte entidades existentes',
			'solucao_acel_topico_3'  => 'Acelere novas automações',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'servicenow-acel' ),
		);

		foreach ( $campos as $campo => $valor ) {
			update_field( $campo, $valor, $post_id );
		}

		$this->preencher_servicenow_faq( $post_id );

		WP_CLI::log( "  ServiceNow preenchido (ID: {$post_id})." );
	}

	protected function preencher_solucao_portal_de_api() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:portal-de-api',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "portal-de-api" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'api e mcp server',
			'solucao_hero_titulo'     => 'Transforme qualquer sistema interno em API ou ferramenta de IA',
			'solucao_hero_corpo'      => 'Exponha sistemas como ERP, CRM, ITSM e bancos de dados como APIs padronizadas ou servidores MCP prontos para consumo por aplicações, equipes e agentes de IA.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/portal-de-api/',
			'solucao_hero_imagem'     => $this->img( 'portal-de-api-hero' ),
			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Democratize o acesso aos sistemas internos',
			'solucao_pilares_1_icone'  => $this->img( 'portal-de-api-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Publique APIs sem código adicional',
			'solucao_pilares_1_desc'   => 'Transforme pipelines existentes em APIs REST documentadas e reutilizáveis por novos projetos.',
			'solucao_pilares_2_icone'  => $this->img( 'portal-de-api-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Conecte agentes de IA aos sistemas',
			'solucao_pilares_2_desc'   => 'Exponha processos como ferramentas MCP autenticadas para agentes consultarem e executarem ações.',
			'solucao_pilares_3_icone'  => $this->img( 'portal-de-api-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Centralize governança de acesso',
			'solucao_pilares_3_desc'   => 'Controle consumidores, permissões e escopos de cada sistema disponibilizado no portal.',
			// 3 · Casos de uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Amplie o uso dos sistemas conectados',
			'solucao_casos_1_icone'   => $this->img( 'portal-de-api-caso-1' ),
			'solucao_casos_1_titulo'  => 'Crie APIs de sistemas corporativos',
			'solucao_casos_1_desc'    => 'Exponha Totvs, Sankhya ou SAP como APIs únicas para consultas e operações reutilizáveis.',
			'solucao_casos_2_icone'   => $this->img( 'portal-de-api-caso-2' ),
			'solucao_casos_2_titulo'  => 'Conecte agentes de IA ao ERP',
			'solucao_casos_2_desc'    => 'Permita que agentes consultem estoque e criem pedidos usando linguagem natural.',
			'solucao_casos_3_icone'   => $this->img( 'portal-de-api-caso-3' ),
			'solucao_casos_3_titulo'  => 'Crie um catálogo interno de APIs',
			'solucao_casos_3_desc'    => 'Ajude equipes a descobrir e reutilizar integrações existentes sem retrabalho.',
			'solucao_casos_4_icone'   => $this->img( 'portal-de-api-caso-4' ),
			'solucao_casos_4_titulo'  => 'Modernize acessos legados',
			'solucao_casos_4_desc'    => 'Exponha mainframes e ESBs como APIs modernas sem revelar protocolos antigos.',
			'solucao_casos_5_icone'   => $this->img( 'portal-de-api-caso-5' ),
			'solucao_casos_5_titulo'  => 'Controle consumidores de APIs',
			'solucao_casos_5_desc'    => 'Gerencie acessos, limites e auditorias por usuário, sistema ou agente.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',
			// 4 · Selos.
			'solucao_selos_eyebrow' => 'compliance & segurança',
			'solucao_selos_titulo'  => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'   => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
			// 5 · Diferencial técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'APIs seguras para humanos e agentes',
			'solucao_dif_corpo'    => 'Cada API ou MCP Server publicado herda segurança da plataforma com autenticação, controle de escopo e proteção de dados sensíveis.',
			'solucao_dif_topico_1' => 'Proteja APIs com autenticação por token',
			'solucao_dif_topico_2' => 'Controle escopos por consumidor',
			'solucao_dif_topico_3' => 'Proteja dados sensíveis com guardrails',
			'solucao_dif_imagem'   => $this->img( 'portal-de-api-dif' ),
			// 6 · Plataforma única.
			'solucao_plat_eyebrow' => 'plataforma única',
			'solucao_plat_titulo'  => 'Unifique acesso a todos os sistemas',
			'solucao_plat_corpo'   => 'Conecte uma vez seus sistemas internos e reutilize essas capacidades como APIs ou ferramentas de IA sem reconstruir integrações para cada projeto.',
			'solucao_plat_topico_1' => 'Centralize acesso aos sistemas corporativos',
			'solucao_plat_topico_2' => 'Reutilize integrações já construídas',
			'solucao_plat_topico_3' => 'Evite novos desenvolvimentos redundantes',
			'solucao_plat_imagem'  => $this->img( 'portal-de-api-plat' ),
			// 7 · Aceleradores.
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Transforme integrações existentes em APIs',
			'solucao_acel_corpo'     => 'Publique pipelines já construídos como endpoints documentados ou ferramentas MCP sem criar novos projetos de desenvolvimento.',
			'solucao_acel_topico_1'  => 'Converta pipelines rapidamente',
			'solucao_acel_topico_2'  => 'Reaproveite integrações existentes',
			'solucao_acel_topico_3'  => 'Disponibilize APIs em poucos cliques',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn1_texto' => 'Começar agora',
			'solucao_acel_btn1_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'portal-de-api-acel' ),
		);

		foreach ( $campos as $campo => $valor ) {
			update_field( $campo, $valor, $post_id );
		}

		$this->preencher_portal_de_api_faq( $post_id );

		WP_CLI::log( "  Portal de API / MCP Server preenchido (ID: {$post_id})." );
	}

	protected function preencher_portal_de_api_faq( $post_id ) {
		$itens = array(
			array(
				'faq:portal-de-api-diferenca-api-mcp',
				'Qual a diferença entre publicar uma API e expor um servidor MCP?',
				'API publica endpoints REST documentados para consumo por sistemas e aplicações. MCP Server expõe ferramentas autenticadas para consumo por agentes de IA, que usam linguagem natural para descobrir e executar ações disponíveis no Portal.',
			),
			array(
				'faq:portal-de-api-agente-descobre-ferramentas',
				'Como um agente de IA descobre e usa ferramentas publicadas no Portal?',
				'O agente conecta ao servidor MCP do Portal, que lista automaticamente as ferramentas disponíveis com nome, descrição e parâmetros. O agente seleciona e executa a ferramenta adequada com autenticação e controle de escopo herdados da plataforma.',
			),
			array(
				'faq:portal-de-api-limitar-acesso-consumidor',
				'É possível limitar o acesso de cada consumidor?',
				'Sim. Cada consumidor — seja um sistema, usuário ou agente — recebe credenciais próprias com escopos definidos. O Portal controla quais APIs e ferramentas cada consumidor pode acessar e audita todas as chamadas.',
			),
			array(
				'faq:portal-de-api-pipeline-vira-api',
				'Um pipeline existente pode virar API sem retrabalho?',
				'Sim. O Portal de API permite publicar pipelines Boomi já construídos como endpoints REST documentados com poucos cliques, sem criar novos projetos de desenvolvimento ou reescrever integrações.',
			),
		);
		$ids = array();
		foreach ( $itens as [ $slug, $pergunta, $resposta ] ) {
			$faq_id = $this->upsert( $slug, array(
				'post_type'    => 'cli_faq',
				'post_title'   => $pergunta,
				'post_content' => $resposta,
				'post_status'  => 'publish',
			) );
			$ids[] = $faq_id;
		}
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $ids, $post_id );
		WP_CLI::log( "  Portal de API / MCP Server FAQ: " . count( $ids ) . " perguntas vinculadas." );
	}

	protected function preencher_solucao_zendesk() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:zendesk',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "zendesk" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu Zendesk',
			'solucao_hero_titulo'     => 'Potencialize o Zendesk criando processos completos sem apps adicionais',
			'solucao_hero_corpo'      => 'Conecte o Zendesk ao ERP, CRM e sistemas de faturamento para criar formulários e fluxos de atendimento que consultam e gravam informações diretamente nos sistemas internos.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/zendesk/',
			'solucao_hero_imagem'     => $this->img( 'zendesk-hero' ),
			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Transforme o Zendesk em uma central de atendimento conectada',
			'solucao_pilares_1_icone'  => $this->img( 'zendesk-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Conecte atendimento aos sistemas internos',
			'solucao_pilares_1_desc'   => 'Integre tickets, formulários e macros do Zendesk diretamente ao ERP, CRM e faturamento.',
			'solucao_pilares_2_icone'  => $this->img( 'zendesk-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Reduza apps adicionais',
			'solucao_pilares_2_desc'   => 'Evite depender de aplicativos pagos do Marketplace para cada nova integração.',
			'solucao_pilares_3_icone'  => $this->img( 'zendesk-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Enriqueça tickets automaticamente',
			'solucao_pilares_3_desc'   => 'Consulte dados de pedidos, clientes e faturas sem sair da tela de atendimento.',
			// 3 · Casos de uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize processos críticos de atendimento',
			'solucao_casos_1_icone'   => $this->img( 'zendesk-caso-1' ),
			'solucao_casos_1_titulo'  => 'Automatize solicitações de reembolso',
			'solucao_casos_1_desc'    => 'Consulte e grave informações financeiras no ERP diretamente pelo ticket do Zendesk.',
			'solucao_casos_2_icone'   => $this->img( 'zendesk-caso-2' ),
			'solucao_casos_2_titulo'  => 'Enriqueça tickets em tempo real',
			'solucao_casos_2_desc'    => 'Exiba dados de pedidos e faturas do ERP durante o atendimento ao cliente.',
			'solucao_casos_3_icone'   => $this->img( 'zendesk-caso-3' ),
			'solucao_casos_3_titulo'  => 'Crie tickets automaticamente',
			'solucao_casos_3_desc'    => 'Transforme eventos de ERP, e-commerce e monitoramento em chamados de suporte.',
			'solucao_casos_4_icone'   => $this->img( 'zendesk-caso-4' ),
			'solucao_casos_4_titulo'  => 'Sincronize atendimento e vendas',
			'solucao_casos_4_desc'    => 'Mantenha status de tickets atualizados entre Zendesk e plataformas CRM.',
			'solucao_casos_5_icone'   => $this->img( 'zendesk-caso-5' ),
			'solucao_casos_5_titulo'  => 'Conecte agentes de IA',
			'solucao_casos_5_desc'    => 'Disponibilize tickets e macros do Zendesk como ferramentas para agentes inteligentes.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',
			// 4 · Selos.
			'solucao_selos_eyebrow' => 'compliance & segurança',
			'solucao_selos_titulo'  => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'   => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
			// 5 · Diferencial técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações seguras para atendimento',
			'solucao_dif_corpo'    => 'Conecte o Zendesk usando APIs oficiais com autenticação segura e controle de permissões conforme agentes e departamentos.',
			'solucao_dif_topico_1' => 'Utilize Zendesk REST API',
			'solucao_dif_topico_2' => 'Proteja conexões com OAuth',
			'solucao_dif_topico_3' => 'Controle permissões por agente',
			'solucao_dif_imagem'   => $this->img( 'zendesk-dif' ),
			// 6 · Plataforma única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Centralize integrações além do Zendesk',
			'solucao_plat_corpo'    => 'Deixe o Zendesk focado na experiência do cliente enquanto a plataforma conecta e movimenta dados nos sistemas internos sem apps adicionais.',
			'solucao_plat_topico_1' => 'Centralize integrações de atendimento',
			'solucao_plat_topico_2' => 'Reduza dependência do Marketplace',
			'solucao_plat_topico_3' => 'Escalone processos com previsibilidade',
			'solucao_plat_imagem'   => $this->img( 'zendesk-plat' ),
			// 7 · Aceleradores.
			'solucao_acel_eyebrow'    => 'Aceleradores de integração',
			'solucao_acel_titulo'     => 'Comece com processos já estruturados',
			'solucao_acel_corpo'      => 'Utilize um modelo pronto para conectar tickets do Zendesk ao ERP e CRM em processos de consulta e gravação.',
			'solucao_acel_topico_1'   => 'Conecte processos rapidamente',
			'solucao_acel_topico_2'   => 'Reutilize fluxos de atendimento',
			'solucao_acel_topico_3'   => 'Adapte integrações ao negócio',
			'solucao_acel_topico_4'   => 'E muito mais...',
			'solucao_acel_btn1_texto' => 'Começar agora',
			'solucao_acel_btn1_url'   => '/contato/',
			'solucao_acel_imagem'     => $this->img( 'zendesk-acel' ),
		);

		foreach ( $campos as $campo => $valor ) {
			update_field( $campo, $valor, $post_id );
		}

		$this->preencher_zendesk_faq( $post_id );

		WP_CLI::log( "  Zendesk preenchido (ID: {$post_id})." );
	}

	protected function preencher_solucao_bionexo() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:bionexo',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "bionexo" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu Bionexo',
			'solucao_hero_titulo'     => 'Conecte o maior marketplace B2B de saúde ao seu ERP',
			'solucao_hero_corpo'      => 'Integre compras, contratos e faturamento da Bionexo ao ERP financeiro e HIS da instituição para eliminar retrabalho e garantir dados sincronizados em toda a operação hospitalar.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/bionexo/',
			'solucao_hero_imagem'     => $this->img( 'bionexo-hero' ),
			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Conecte compras hospitalares aos sistemas internos',
			'solucao_pilares_1_icone'  => $this->img( 'bionexo-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Sincronize pedidos automaticamente',
			'solucao_pilares_1_desc'   => 'Conecte pedidos da Bionexo ao ERP financeiro e estoque sem processos manuais.',
			'solucao_pilares_2_icone'  => $this->img( 'bionexo-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Centralize negociações com fornecedores',
			'solucao_pilares_2_desc'   => 'Mantenha contratos, preços e condições comerciais sincronizados com sistemas internos.',
			'solucao_pilares_3_icone'  => $this->img( 'bionexo-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Reduza retrabalho operacional',
			'solucao_pilares_3_desc'   => 'Elimine digitações manuais entre marketplace, ERP e sistemas hospitalares.',
			// 3 · Casos de uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize processos de compras hospitalares',
			'solucao_casos_1_icone'   => $this->img( 'bionexo-caso-1' ),
			'solucao_casos_1_titulo'  => 'Sincronize pedidos de compra',
			'solucao_casos_1_desc'    => 'Envie pedidos da Bionexo diretamente para o ERP hospitalar sem intervenção manual.',
			'solucao_casos_2_icone'   => $this->img( 'bionexo-caso-2' ),
			'solucao_casos_2_titulo'  => 'Concilie notas fiscais automaticamente',
			'solucao_casos_2_desc'    => 'Relacione notas recebidas pelo marketplace com registros financeiros internos.',
			'solucao_casos_3_icone'   => $this->img( 'bionexo-caso-3' ),
			'solucao_casos_3_titulo'  => 'Atualize contratos e preços',
			'solucao_casos_3_desc'    => 'Sincronize negociações realizadas com fornecedores no sistema de suprimentos.',
			'solucao_casos_4_icone'   => $this->img( 'bionexo-caso-4' ),
			'solucao_casos_4_titulo'  => 'Consolide dados de compras',
			'solucao_casos_4_desc'    => 'Centralize informações para análises de custo e eficiência hospitalar.',
			'solucao_casos_5_icone'   => $this->img( 'bionexo-caso-5' ),
			'solucao_casos_5_titulo'  => 'Conecte agentes de IA',
			'solucao_casos_5_desc'    => 'Disponibilize tickets e macros do Zendesk como ferramentas para agentes inteligentes.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',
			// 4 · Selos.
			'solucao_selos_eyebrow' => 'compliance & segurança',
			'solucao_selos_titulo'  => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'   => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
			// 5 · Diferencial técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações seguras para saúde',
			'solucao_dif_corpo'    => 'Conecte a Bionexo usando APIs oficiais com autenticação segura e proteção de dados conforme requisitos da LGPD.',
			'solucao_dif_topico_1' => 'Utilize API REST da Bionexo',
			'solucao_dif_topico_2' => 'Proteja acessos com tokens',
			'solucao_dif_topico_3' => 'Proteja dados conforme LGPD',
			'solucao_dif_imagem'   => $this->img( 'bionexo-dif' ),
			// 6 · Plataforma única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Unifique compras e sistemas hospitalares',
			'solucao_plat_corpo'    => 'Conecte marketplace, HIS e ERP financeiro em uma única plataforma para eliminar planilhas e fechar o ciclo operacional.',
			'solucao_plat_topico_1' => 'Centralize fluxos de compras',
			'solucao_plat_topico_2' => 'Conecte múltiplos sistemas hospitalares',
			'solucao_plat_topico_3' => 'Elimine processos manuais',
			'solucao_plat_imagem'   => $this->img( 'bionexo-plat' ),
			// 7 · Aceleradores.
			'solucao_acel_eyebrow'    => 'Aceleradores de integração',
			'solucao_acel_titulo'     => 'Comece com compras integradas',
			'solucao_acel_corpo'      => 'Utilize um modelo pronto para conectar pedidos da Bionexo ao ERP hospitalar e acelerar a automação.',
			'solucao_acel_topico_1'   => 'Conecte pedidos rapidamente',
			'solucao_acel_topico_2'   => 'Reutilize fluxos hospitalares',
			'solucao_acel_topico_3'   => 'Adapte processos internos',
			'solucao_acel_topico_4'   => 'E muito mais...',
			'solucao_acel_btn1_texto' => 'Começar agora',
			'solucao_acel_btn1_url'   => '/contato/',
			'solucao_acel_imagem'     => $this->img( 'bionexo-acel' ),
		);

		foreach ( $campos as $campo => $valor ) {
			update_field( $campo, $valor, $post_id );
		}

		WP_CLI::log( "  Bionexo preenchido (ID: {$post_id})." );
		$this->preencher_bionexo_faq( $post_id );
	}

	protected function preencher_bionexo_faq( $post_id ) {
		$itens = array(
			array(
				'faq:bionexo-sincronizar-pedidos-erp',
				'Como sincronizar pedidos de compra da Bionexo direto com o ERP?',
				'A CLI Connect se conecta via API da Bionexo e dispara eventos a cada novo pedido aprovado, gravando automaticamente no ERP hospitalar — como Totvs ou SAP — sem intervenção manual.',
			),
			array(
				'faq:bionexo-conciliar-notas-fiscais',
				'É possível conciliar notas fiscais automaticamente?',
				'Sim. A plataforma captura as NFs emitidas pela Bionexo e as concilia com os pedidos de compra registrados no ERP, sinalizando divergências e eliminando o processo manual de conferência.',
			),
			array(
				'faq:bionexo-multiplas-unidades-hospitalares',
				'Como funciona com múltiplas unidades hospitalares?',
				'A integração suporta múltiplas unidades em uma única configuração: cada unidade pode ter suas credenciais e fluxos independentes, centralizados e monitorados na mesma plataforma.',
			),
		);

		$faq_ids = array();
		foreach ( $itens as [ $slug, $pergunta, $resposta ] ) {
			$faq_ids[] = $this->upsert( $slug, array(
				'post_type'    => 'cli_faq',
				'post_title'   => $pergunta,
				'post_content' => $resposta,
				'post_status'  => 'publish',
			) );
		}

		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $faq_ids, $post_id );
		WP_CLI::log( '  Bionexo FAQ: ' . count( $faq_ids ) . ' perguntas vinculadas.' );
	}

	protected function preencher_solucao_tasy() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:tasy',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "tasy" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu Tasy',
			'solucao_hero_titulo'     => 'Conecte o núcleo da operação hospitalar a todo o ecossistema',
			'solucao_hero_corpo'      => 'Integre o Tasy a laboratórios, operadoras de saúde, ERP corporativo e agentes de IA para conectar dados assistenciais e financeiros sem alterar o core hospitalar.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucoes/',
			'solucao_hero_imagem'     => $this->img( 'tasy-hero' ),
			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Amplie o valor dos dados do Tasy',
			'solucao_pilares_1_icone'  => $this->img( 'tasy-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Aproveite APIs padronizadas do Tasy',
			'solucao_pilares_1_desc'   => 'Utilize a Tasy Open API para criar integrações documentadas, seguras e escaláveis.',
			'solucao_pilares_2_icone'  => $this->img( 'tasy-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Conecte faturamento TISS',
			'solucao_pilares_2_desc'   => 'Integre o Tasy às operadoras de saúde para automatizar processos de faturamento.',
			'solucao_pilares_3_icone'  => $this->img( 'tasy-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Centralize dados hospitalares',
			'solucao_pilares_3_desc'   => 'Unifique informações clínicas e financeiras para análises sem alterar o sistema principal.',
			// 3 · Casos de uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize processos hospitalares críticos',
			'solucao_casos_1_titulo'   => 'Automatize faturamento TISS',
			'solucao_casos_1_desc'     => 'Conecte o Tasy às operadoras de saúde para agilizar processos de faturamento.',
			'solucao_casos_1_icone'    => $this->img( 'tasy-caso-1' ),
			'solucao_casos_2_titulo'   => 'Sincronize resultados laboratoriais',
			'solucao_casos_2_desc'     => 'Integre sistemas LIS ao prontuário para disponibilizar resultados automaticamente.',
			'solucao_casos_2_icone'    => $this->img( 'tasy-caso-2' ),
			'solucao_casos_3_titulo'   => 'Concilie dados financeiros',
			'solucao_casos_3_desc'     => 'Conecte Tasy e ERP corporativo para consolidar informações financeiras.',
			'solucao_casos_3_icone'    => $this->img( 'tasy-caso-3' ),
			'solucao_casos_4_titulo'   => 'Consolide redes hospitalares',
			'solucao_casos_4_desc'     => 'Padronize integrações entre múltiplas unidades e sistemas hospitalares.',
			'solucao_casos_4_icone'    => $this->img( 'tasy-caso-4' ),
			'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
			'solucao_casos_5_desc'     => 'Disponibilize dados assistenciais para agentes administrativos sem expor o core clínico.',
			'solucao_casos_5_icone'    => $this->img( 'tasy-caso-5' ),
			'solucao_casos_cta_texto'  => 'Agende uma demonstração',
			'solucao_casos_cta_url'    => '/contato/',
			// 4 · Selos.
			'solucao_selos_eyebrow'    => 'compliance & segurança',
			'solucao_selos_titulo'     => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'      => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
			// 5 · Diferencial técnico.
			'solucao_dif_eyebrow'      => 'diferencial técnico',
			'solucao_dif_titulo'       => 'Integrações seguras para dados hospitalares',
			'solucao_dif_corpo'        => 'Utilize a Tasy Open API com autenticação, criptografia e controle de acesso para proteger informações sensíveis de saúde.',
			'solucao_dif_topico_1'     => 'Utilize APIs oficiais do Tasy',
			'solucao_dif_topico_2'     => 'Proteja dados sensíveis de saúde',
			'solucao_dif_topico_3'     => 'Controle acessos conforme LGPD',
			'solucao_dif_imagem'       => $this->img( 'tasy-dif' ),
			// 6 · Plataforma única.
			'solucao_plat_eyebrow'     => 'plataforma única',
			'solucao_plat_titulo'      => 'Unifique operações hospitalares complexas',
			'solucao_plat_corpo'       => 'Crie uma camada única de integração para conectar múltiplas unidades Tasy e sistemas hospitalares sem customizar o core assistencial.',
			'solucao_plat_topico_1'    => 'Padronize integrações entre unidades',
			'solucao_plat_topico_2'    => 'Centralize faturamento hospitalar',
			'solucao_plat_topico_3'    => 'Simplifique consolidação financeira',
			'solucao_plat_imagem'      => $this->img( 'tasy-plat' ),
			// 7 · Aceleradores.
			'solucao_acel_eyebrow'     => 'Aceleradores de integração',
			'solucao_acel_titulo'      => 'Comece com integrações hospitalares prontas',
			'solucao_acel_corpo'       => 'Utilize um modelo estruturado para conectar Tasy Open API ao ERP financeiro e operadoras de saúde.',
			'solucao_acel_topico_1'    => 'Conecte sistemas rapidamente',
			'solucao_acel_topico_2'    => 'Reutilize fluxos hospitalares',
			'solucao_acel_topico_3'    => 'Acelere novas integrações',
			'solucao_acel_topico_4'    => 'E muito mais...',
			'solucao_acel_btn1_texto'  => 'Começar agora',
			'solucao_acel_btn1_url'    => '/contato/',
			'solucao_acel_imagem'      => $this->img( 'tasy-acel' ),
		);

		foreach ( $campos as $campo => $valor ) {
			update_field( $campo, $valor, $post_id );
		}

		WP_CLI::log( "  Tasy preenchido (ID: {$post_id})." );
		$this->preencher_tasy_faq( $post_id );
	}

	protected function preencher_tasy_faq( $post_id ) {
		$itens = array(
			array(
				'faq:tasy-cli-connect-tasy-open-api',
				'Como a CLI Connect powered by Boomi usa a Tasy Open API?',
				'A CLI Connect se conecta à Tasy Open API com autenticação segura e orquestra os fluxos de integração entre o Tasy e sistemas externos como ERP, operadoras de saúde e laboratórios — sem alterar o core hospitalar.',
			),
			array(
				'faq:tasy-faturamento-tiss-multiplas-operadoras',
				'É possível integrar faturamento TISS de múltiplas operadoras?',
				'Sim. A plataforma permite configurar conectores para diferentes operadoras de saúde, processando guias e retornos de autorização de forma centralizada e automatizada.',
			),
			array(
				'faq:tasy-consolidacao-financeira-multi-hospital',
				'Como funciona a consolidação financeira multi-hospital?',
				'A CLI Connect cria uma camada de integração única que coleta dados financeiros de múltiplas unidades Tasy e os envia ao ERP corporativo, eliminando processos manuais de conciliação.',
			),
		);

		$faq_ids = array();
		foreach ( $itens as [ $slug, $pergunta, $resposta ] ) {
			$faq_ids[] = $this->upsert( $slug, array(
				'post_type'    => 'cli_faq',
				'post_title'   => $pergunta,
				'post_content' => $resposta,
				'post_status'  => 'publish',
			) );
		}

		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $faq_ids, $post_id );
		WP_CLI::log( '  Tasy FAQ: ' . count( $faq_ids ) . ' perguntas vinculadas.' );
	}

	protected function preencher_solucao_mv() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:mv',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "mv" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu SOUL MV',
			'solucao_hero_titulo'     => 'Conecte o SOUL MV ao ecossistema completo do hospital digital',
			'solucao_hero_corpo'      => 'Integre o MV a laboratório, diagnóstico por imagem, ERP corporativo e faturamento para conectar processos assistenciais, administrativos e financeiros em uma única operação.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucoes/',
			'solucao_hero_imagem'      => $this->img( 'mv-hero' ),
			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Amplie a conectividade do SOUL MV',
			'solucao_pilares_1_icone'  => $this->img( 'mv-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Integre sistemas assistenciais',
			'solucao_pilares_1_desc'   => 'Conecte RIS, PACS, LIS e portais de exames ao MV com troca de dados em tempo real.',
			'solucao_pilares_2_icone'  => $this->img( 'mv-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Reduza glosas hospitalares',
			'solucao_pilares_2_desc'   => 'Valide informações dos pedidos de exame antes da execução e evite inconsistências.',
			'solucao_pilares_3_icone'  => $this->img( 'mv-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Centralize dados financeiros',
			'solucao_pilares_3_desc'   => 'Consolide informações entre unidades hospitalares e sistemas corporativos.',
			// 3 · Casos de uso.
			'solucao_casos_eyebrow'    => 'casos de uso',
			'solucao_casos_titulo'     => 'Automatize processos hospitalares essenciais',
			'solucao_casos_1_icone'    => $this->img( 'mv-caso-1' ),
			'solucao_casos_1_titulo'   => 'Integre RIS e PACS ao MV',
			'solucao_casos_1_desc'     => 'Consulte alergias e histórico clínico durante exames sem trocar de sistema.',
			'solucao_casos_2_icone'    => $this->img( 'mv-caso-2' ),
			'solucao_casos_2_titulo'   => 'Automatize faturamento hospitalar',
			'solucao_casos_2_desc'     => 'Sincronize informações de faturamento e glosas com operadoras de saúde.',
			'solucao_casos_3_icone'    => $this->img( 'mv-caso-3' ),
			'solucao_casos_3_titulo'   => 'Concilie finanças entre unidades',
			'solucao_casos_3_desc'     => 'Conecte MV e ERP corporativo para consolidar resultados financeiros.',
			'solucao_casos_4_icone'    => $this->img( 'mv-caso-4' ),
			'solucao_casos_4_titulo'   => 'Automatize acessos internos',
			'solucao_casos_4_desc'     => 'Provisione acessos em sistemas de apoio a partir de eventos do MV.',
			'solucao_casos_5_icone'    => $this->img( 'mv-caso-5' ),
			'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
			'solucao_casos_5_desc'     => 'Disponibilize dados assistenciais para agentes administrativos sem expor o core clínico.',
			'solucao_casos_cta_texto'  => 'Agende uma demonstração',
			'solucao_casos_cta_url'    => '/contato/',
			// 4 · Selos.
			'solucao_selos_eyebrow'    => 'compliance & segurança',
			'solucao_selos_titulo'     => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'      => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
			// 5 · Diferencial técnico.
			'solucao_dif_eyebrow'      => 'diferencial técnico',
			'solucao_dif_titulo'       => 'Integrações seguras para saúde',
			'solucao_dif_corpo'        => 'Conecte o MV usando APIs com trilha de auditoria e controles de segurança para proteger dados clínicos conforme a LGPD.',
			'solucao_dif_topico_1'     => 'Utilize APIs do sistema MV',
			'solucao_dif_topico_2'     => 'Audite integrações hospitalares',
			'solucao_dif_topico_3'     => 'Proteja dados clínicos sensíveis',
			'solucao_dif_imagem'       => $this->img( 'mv-dif' ),
			// 6 · Plataforma única.
			'solucao_plat_eyebrow'     => 'plataforma única',
			'solucao_plat_titulo'      => 'Unifique sistemas de redes hospitalares',
			'solucao_plat_corpo'       => 'Centralize integrações entre diferentes HIS, ERPs e sistemas assistenciais para evitar projetos duplicados em cada unidade.',
			'solucao_plat_topico_1'    => 'Conecte diferentes plataformas hospitalares',
			'solucao_plat_topico_2'    => 'Padronize integrações entre unidades',
			'solucao_plat_topico_3'    => 'Reduza esforços de manutenção',
			'solucao_plat_imagem'      => $this->img( 'mv-plat' ),
			// 7 · Aceleradores.
			'solucao_acel_eyebrow'     => 'Aceleradores de integração',
			'solucao_acel_titulo'      => 'Comece com integrações hospitalares prontas',
			'solucao_acel_corpo'       => 'Utilize um modelo estruturado para conectar MV, RIS/PACS, LIS e ERP financeiro com mais velocidade.',
			'solucao_acel_topico_1'    => 'Conecte sistemas rapidamente',
			'solucao_acel_topico_2'    => 'Reutilize fluxos hospitalares',
			'solucao_acel_topico_3'    => 'Acelere novas automações',
			'solucao_acel_topico_4'    => 'E muito mais...',
			'solucao_acel_btn1_texto'  => 'Começar agora',
			'solucao_acel_btn1_url'    => '/contato/',
			'solucao_acel_imagem'      => $this->img( 'mv-acel' ),
		);

		foreach ( $campos as $campo => $valor ) {
			update_field( $campo, $valor, $post_id );
		}

		WP_CLI::log( "  MV preenchido (ID: {$post_id})." );
		$this->preencher_mv_faq( $post_id );
	}

	protected function preencher_mv_faq( $post_id ) {
		$itens = array(
			array(
				'faq:mv-reduzir-glosas-ris-pacs',
				'Como reduzir glosas com integração RIS/PACS ↔ MV?',
				'A CLI Connect valida automaticamente as informações do pedido de exame antes da execução, cruzando dados entre RIS/PACS e MV para detectar inconsistências que geram glosas antes que elas ocorram.',
			),
			array(
				'faq:mv-conectar-mv-tasy-rede-hospitalar',
				'É possível conectar MV e Tasy na mesma rede hospitalar?',
				'Sim. A plataforma atua como camada de integração neutra e pode orquestrar fluxos entre MV e Tasy, permitindo que redes hospitalares com diferentes HIS compartilhem dados de forma padronizada.',
			),
			array(
				'faq:mv-consolidacao-financeira-multi-unidade',
				'Como funciona a consolidação financeira multi-unidade?',
				'A CLI Connect coleta dados financeiros de cada unidade MV e os centraliza no ERP corporativo, eliminando consolidações manuais em planilhas e garantindo visibilidade unificada do resultado financeiro da rede.',
			),
		);

		$faq_ids = array();
		foreach ( $itens as [ $slug, $pergunta, $resposta ] ) {
			$faq_ids[] = $this->upsert( $slug, array(
				'post_type'    => 'cli_faq',
				'post_title'   => $pergunta,
				'post_content' => $resposta,
				'post_status'  => 'publish',
			) );
		}

		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $faq_ids, $post_id );
		WP_CLI::log( '  MV FAQ: ' . count( $faq_ids ) . ' perguntas vinculadas.' );
	}

	protected function preencher_zendesk_faq( $post_id ) {
		$itens = array(
			array(
				'faq:zendesk-processo-sem-app-pago',
				'Como criar um processo no Zendesk que grava em outro sistema sem app pago?',
				'A CLI Connect atua como camada de integração externa: o Zendesk dispara um evento via webhook, a plataforma recebe, processa e grava os dados no sistema de destino — como SAP ou Totvs — sem necessidade de aplicativos pagos do Marketplace.',
			),
			array(
				'faq:zendesk-enriquecer-ticket-erp-crm',
				'Como enriquecer um ticket com dados de ERP e CRM em tempo real?',
				'Ao abrir um ticket, a CLI Connect consulta o ERP e o CRM em paralelo usando o e-mail ou ID do cliente e retorna os dados — pedidos, faturas, contratos — diretamente no ticket via API do Zendesk, sem intervenção manual do agente.',
			),
			array(
				'faq:zendesk-tickets-macros-agentes-ia',
				'É possível expor tickets e macros como ferramentas para agentes de IA?',
				'Sim. A CLI Connect publica os endpoints do Zendesk como ferramentas MCP autenticadas. Agentes de IA podem consultar tickets, aplicar macros e atualizar campos usando linguagem natural, com controle de permissões por escopo.',
			),
			array(
				'faq:zendesk-sincronizacao-status-crm',
				'Como funciona a sincronização de status entre Zendesk e CRM?',
				'A CLI Connect monitora mudanças de status no Zendesk via webhook e replica o estado no CRM em tempo real. O fluxo inverso também é suportado: atualizações no CRM refletem automaticamente no ticket do Zendesk.',
			),
		);
		$ids = array();
		foreach ( $itens as [ $slug, $pergunta, $resposta ] ) {
			$faq_id = $this->upsert( $slug, array(
				'post_type'    => 'cli_faq',
				'post_title'   => $pergunta,
				'post_content' => $resposta,
				'post_status'  => 'publish',
			) );
			$ids[] = $faq_id;
		}
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $ids, $post_id );
		WP_CLI::log( "  Zendesk FAQ: " . count( $ids ) . " perguntas vinculadas." );
	}

	protected function preencher_servicenow_faq( $post_id ) {
		$itens = array(
			array(
				'faq:servicenow-processo-sem-modulo',
				'Como criar um processo no ServiceNow que grava diretamente em outro sistema, sem módulo de integração nativo?',
				'<p>A CLI Connect atua como camada de integração externa: o ServiceNow dispara um evento via API, a plataforma recebe, processa e grava os dados no sistema de destino — como SAP ou Totvs — sem necessidade de Spokes ou módulos adicionais do ServiceNow.</p>',
			),
			array(
				'faq:servicenow-cadastro-produtos-totvs',
				'Como funciona o exemplo de cadastro de produtos → Totvs ERP?',
				'<p>O usuário preenche um formulário no catálogo de serviços do ServiceNow. Ao aprovado, a CLI Connect recebe o payload, valida os dados e chama a API do Totvs para criar o produto. O ServiceNow recebe a confirmação e fecha o ticket automaticamente.</p>',
			),
			array(
				'faq:servicenow-agente-ia-incidente',
				'Como um agente de IA abre um incidente no ServiceNow automaticamente?',
				'<p>O agente de IA envia uma requisição à CLI Connect com os dados do evento. A plataforma formata o payload conforme o schema do ServiceNow e cria o incidente via API REST, incluindo categoria, urgência e descrição — sem intervenção humana.</p>',
			),
		);

		foreach ( $itens as [ $slug, $pergunta, $resposta ] ) {
			$this->upsert( $slug, array(
				'post_type'    => 'cli_faq',
				'post_title'   => $pergunta,
				'post_content' => $resposta,
				'post_status'  => 'publish',
			) );
		}

		$faq_ids = array();
		foreach ( $itens as [ $slug ] ) {
			$posts = get_posts( array(
				'post_type'  => 'cli_faq',
				'meta_key'   => self::META,
				'meta_value' => $slug,
				'fields'     => 'ids',
			) );
			if ( ! empty( $posts ) ) {
				$faq_ids[] = (int) $posts[0];
			}
		}

		if ( ! empty( $faq_ids ) ) {
			update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
			update_field( 'solucao_faq_itens', $faq_ids, $post_id );
			WP_CLI::log( '  ServiceNow FAQ: ' . count( $faq_ids ) . ' perguntas vinculadas.' );
		}
	}

	protected function preencher_sankhya_faq( $post_id ) {
		$itens = array(
			array(
				'faq:sankhya-banco-direto',
				'A CLI Connect acessa diretamente o banco de dados do Sankhya?',
				'<p>Não. A CLI Connect utiliza exclusivamente a API Gateway oficial do Sankhya para todas as operações. Não há acesso direto ao banco de dados, preservando a integridade das regras de negócio e a governança da plataforma.</p>',
			),
			array(
				'faq:sankhya-autenticacao',
				'Como funciona a autenticação nas integrações com o Sankhya?',
				'<p>A autenticação é realizada por meio do usuário de integração nativo do Sankhya, com credenciais configuradas na plataforma da CLI Connect. Cada integração opera com as permissões explicitamente atribuídas a esse usuário, mantendo rastreabilidade e controle de acesso.</p>',
			),
			array(
				'faq:sankhya-permissoes',
				'É possível limitar quais dados cada integração pode acessar?',
				'<p>Sim. As permissões são definidas no próprio Sankhya por entidade e operação (leitura, escrita, exclusão). A CLI Connect respeita essas configurações, garantindo que cada integração acesse apenas os dados autorizados pelo administrador do ERP.</p>',
			),
		);
		$ids = array();
		foreach ( $itens as $ordem => list( $slug, $pergunta, $resposta ) ) {
			$ids[] = (int) $this->upsert( $slug, array(
				'post_type'    => 'cli_faq',
				'post_title'   => $pergunta,
				'post_content' => $resposta,
				'menu_order'   => $ordem,
			) );
		}
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $ids, $post_id );
		WP_CLI::log( sprintf( '  Sankhya FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	protected function preencher_senior_faq( $post_id ) {
		$itens = array(
			array(
				'faq:senior-dados-sensiveis',
				'Como a CLI Connect protege dados sensíveis do Senior?',
				'<p>A plataforma aplica mascaramento automático de campos sensíveis — como CPF, salário e dados bancários — antes que as informações trafeguem entre sistemas. Todo o processo é registrado em log de auditoria, garantindo rastreabilidade e conformidade com a LGPD.</p>',
			),
			array(
				'faq:senior-multiplas-filiais',
				'É possível integrar múltiplas filiais utilizando bases Senior diferentes?',
				'<p>Sim. A CLI Connect suporta cenários multi-empresa e multi-base, permitindo integrar filiais com instâncias Senior distintas na mesma plataforma. As regras de roteamento e mapeamento de dados são configuradas por empresa, garantindo isolamento e governança centralizados.</p>',
			),
			array(
				'faq:senior-tempo-implantacao',
				'Quanto tempo leva para automatizar o fluxo de admissão e desligamento?',
				'<p>Com os aceleradores JML (Joiner, Mover, Leaver) da CLI Connect, projetos de admissão e desligamento podem ser implantados em semanas. Os modelos pré-configurados reduzem o esforço de desenvolvimento e permitem adaptações rápidas às regras específicas de cada empresa.</p>',
			),
		);
		$ids = array();
		foreach ( $itens as $ordem => list( $slug, $pergunta, $resposta ) ) {
			$ids[] = (int) $this->upsert( $slug, array(
				'post_type'    => 'cli_faq',
				'post_title'   => $pergunta,
				'post_content' => $resposta,
				'menu_order'   => $ordem,
			) );
		}
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $ids, $post_id );
		WP_CLI::log( sprintf( '  Senior FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	protected function preencher_winthor_faq( $post_id ) {
		$itens = array(
			array(
				'faq:winthor-volume',
				'A plataforma suporta operações com grande volume de pedidos?',
				'<p>Sim. A CLI Connect utiliza conectores dedicados às rotinas automáticas e webservices do Winthor, projetados para suportar o alto volume de pedidos típico de distribuidores e atacadistas. A plataforma processa grandes lotes sem comprometer a estabilidade do ERP.</p>',
			),
			array(
				'faq:winthor-forca-vendas',
				'É possível integrar vários aplicativos de força de vendas?',
				'<p>Sim. A CLI Connect conecta simultaneamente diferentes aplicativos de pré-venda e força de vendas ao Winthor, centralizando o recebimento de pedidos em uma única camada de integração. Isso elimina a digitação manual e garante que todos os canais alimentem o ERP de forma automatizada e padronizada.</p>',
			),
			array(
				'faq:winthor-transportadoras',
				'Como funciona a integração com transportadoras?',
				'<p>A CLI Connect automatiza o envio de dados de entrega para as transportadoras parceiras, incluindo geração de etiquetas, transmissão de manifesto e recebimento de eventos de rastreamento. O status das entregas é atualizado automaticamente no Winthor, mantendo clientes e operação sempre informados sem intervenção manual.</p>',
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

		WP_CLI::log( sprintf( '  Winthor FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	protected function preencher_datasul_faq( $post_id ) {
		$itens = array(
			array(
				'faq:datasul-banco-direto',
				'A CLI Connect acessa o banco de dados do Datasul diretamente?',
				'<p>Não. A CLI Connect utiliza o protocolo Progress/EMS para comunicar com o Datasul de forma nativa e segura, sem acesso direto ao banco de dados. O processamento ocorre dentro da infraestrutura da empresa, preservando a integridade dos dados e as políticas de segurança corporativas.</p>',
			),
			array(
				'faq:datasul-versoes',
				'É possível integrar diferentes versões do Datasul?',
				'<p>Sim. A CLI Connect suporta múltiplas versões do Datasul, adaptando os conectores às APIs disponíveis em cada ambiente. Não é necessário atualizar o ERP para começar a integrar — a plataforma se ajusta ao que está disponível na sua instalação atual.</p>',
			),
			array(
				'faq:datasul-sap',
				'Posso integrar Datasul e SAP na mesma empresa?',
				'<p>Sim. A CLI Connect é uma plataforma unificada que permite integrar diferentes ERPs simultaneamente, incluindo Datasul e SAP. Empresas que cresceram por aquisições e operam múltiplos sistemas podem centralizar todas as integrações em uma única camada de governança.</p>',
			),
			array(
				'faq:datasul-mes',
				'Como integrar o Datasul ao MES?',
				'<p>A CLI Connect oferece conectores e aceleradores prontos para sincronizar ordens de produção entre o Datasul e sistemas MES. A integração atualiza automaticamente o status das ordens durante toda a operação industrial, eliminando retrabalho manual e reduzindo erros no chão de fábrica.</p>',
			),
			array(
				'faq:datasul-bi-ia',
				'Os dados podem ser utilizados em BI ou Inteligência Artificial?',
				'<p>Sim. A CLI Connect disponibiliza dados do Datasul para plataformas de BI como Power BI e Tableau, além de permitir que agentes de IA consultem informações do ERP com segurança através de integrações governadas. Isso transforma dados operacionais em insumo para análise e automação inteligente.</p>',
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

		WP_CLI::log( sprintf( '  Datasul FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/* =====================================================================
	   TOTVS PROTHEUS
	   ===================================================================== */

	/**
	 * Preenche todos os campos ACF do post cli_solucao do TOTVS Protheus.
	 *
	 * @return void
	 */
	protected function preencher_solucao_totvs() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:totvs-protheus', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  TOTVS: post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'         => 'para o seu Protheus',
			'solucao_hero_titulo'          => 'Integre o TOTVS Protheus com qualquer sistema sem projetos demorados',
			'solucao_hero_titulo_destaque' => '',
			'solucao_hero_corpo'           => 'Conecte o Protheus ao CRM, e-commerce, bancos e plataformas fiscais utilizando integrações prontas, reduzindo customizações, acelerando implantações e preservando a estabilidade do seu ERP.',
			'solucao_hero_btn1_texto'      => 'Agende uma demonstração',
			'solucao_hero_btn1_url'        => '/contato/',
			'solucao_hero_btn2_texto'      => 'Conheça nossa solução',
			'solucao_hero_btn2_url'        => '/solucoes/tecnologia/totvs-protheus/',
			'solucao_hero_imagem'          => $this->img( 'totvs-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Simplifique a integração do seu Protheus',
			'solucao_pilares_1_icone'  => $this->img( 'totvs-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Elimine integrações customizadas',
			'solucao_pilares_1_desc'   => 'Reduza a dependência de AdvPL: conecte novos sistemas com agilidade usando ExecAuto Padrões e pontos de entrada Protheus.',
			'solucao_pilares_2_icone'  => $this->img( 'totvs-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Reaproveite integrações prontas',
			'solucao_pilares_2_desc'   => 'Utilize aceleradores para pedidos, clientes, estoque e processos fiscais, diminuindo tempo de implantação.',
			'solucao_pilares_3_icone'  => $this->img( 'totvs-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Novas integrações em dias',
			'solucao_pilares_3_desc'   => 'Conecte novos sistemas em dias, não meses, utilizando uma arquitetura preparada para expansão.',

			// 3 · Casos de Uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Conecte os principais processos do Protheus',
			'solucao_casos_1_icone'   => $this->img( 'totvs-caso-1' ),
			'solucao_casos_1_titulo'  => 'Automatize pedidos do e-commerce',
			'solucao_casos_1_desc'    => 'Envie pedidos automaticamente para o Protheus, reduzindo retrabalho e acelerando o faturamento.',
			'solucao_casos_2_icone'   => $this->img( 'totvs-caso-2' ),
			'solucao_casos_2_titulo'  => 'Clientes sempre atualizados',
			'solucao_casos_2_desc'    => 'Sincronize cadastros entre CRM, e-commerce e Protheus utilizando APIs REST.',
			'solucao_casos_3_icone'   => $this->img( 'totvs-caso-3' ),
			'solucao_casos_3_titulo'  => 'Automatize documentos fiscais',
			'solucao_casos_3_desc'    => 'Integre a emissão e consulta de documentos fiscais diretamente aos processos financeiros.',
			'solucao_casos_4_icone'   => $this->img( 'totvs-caso-4' ),
			'solucao_casos_4_titulo'  => 'Controle estoques entre filiais',
			'solucao_casos_4_desc'    => 'Atualize saldos automaticamente entre unidades, evitando divergências operacionais.',
			'solucao_casos_5_icone'   => $this->img( 'totvs-caso-5' ),
			'solucao_casos_5_titulo'  => 'Conecte o Protheus ao Salesforce',
			'solucao_casos_5_desc'    => 'Compartilhe informações entre ERP e CRM para eliminar retrabalho comercial e operacional.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',

			// 4 · Selos.
			'solucao_selos_eyebrow' => 'compliance & segurança',
			'solucao_selos_titulo'  => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'   => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',

			// 5 · Diferencial Técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Conectividade segura para ambientes Protheus',
			'solucao_dif_corpo'    => 'Conecte o Protheus utilizando Runtime e comunicação outbound, preservando a infraestrutura da empresa e suportando REST nativamente e ExecAuto chamando as MATA Protheus Standard.',
			'solucao_dif_topico_1' => 'Evite abrir portas no firewall.',
			'solucao_dif_topico_2' => 'REST nativo e ExecAuto chamando MATA Protheus Standard.',
			'solucao_dif_topico_3' => 'Preserve a segurança do ambiente interno.',
			'solucao_dif_imagem'   => $this->img( 'totvs-dif' ),

			// 6 · Plataforma Única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Uma plataforma para integrar todo o seu ecossistema',
			'solucao_plat_corpo'    => 'Centralize todas as integrações do Protheus, Salesforce, bancos e e-commerce em uma única plataforma, reaproveitando componentes já validados e reduzindo a complexidade operacional.',
			'solucao_plat_topico_1' => 'Reutilize integrações já implantadas.',
			'solucao_plat_topico_2' => 'Reduza novos projetos de desenvolvimento.',
			'solucao_plat_topico_3' => 'Centralize toda a governança das integrações.',
			'solucao_plat_imagem'   => $this->img( 'totvs-plataforma' ),

			// 7 · Aceleradores.
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece utilizando integrações prontas',
			'solucao_acel_corpo'     => 'Implemente cenários recorrentes entre Protheus e outros sistemas utilizando modelos pré-configurados, adaptados rapidamente ao seu ambiente.',
			'solucao_acel_topico_1'  => 'Implante sincronização de pedidos rapidamente.',
			'solucao_acel_topico_2'  => 'Reutilize modelos para cadastros.',
			'solucao_acel_topico_3'  => 'Adapte fluxos ao seu processo.',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'totvs-aceleradores' ),
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_totvs_faq( $post_id );

		WP_CLI::log( "  TOTVS Protheus preenchido (ID: {$post_id})." );
	}

	/**
	 * Cria os posts cli_faq do TOTVS e vincula à solução.
	 *
	 * @param int $post_id ID do post cli_solucao do TOTVS.
	 * @return void
	 */
	protected function preencher_totvs_faq( $post_id ) {
		$itens = array(
			array(
				'faq:totvs-sem-vpn',
				'Como a CLI Connect integra o Protheus sem VPN?',
				'<p>A CLI Connect utiliza um agente (Boomi Atom) instalado dentro da rede corporativa que realiza comunicação outbound com a plataforma na nuvem. Não é necessário abrir portas no firewall nem configurar VPN — o Protheus permanece protegido enquanto as integrações operam normalmente.</p>',
			),
			array(
				'faq:totvs-advpl',
				'É necessário desenvolver rotinas AdvPL para cada integração?',
				'<p>Não. A CLI Connect utiliza ExecAuto chamando as MATA Protheus Standard e APIs REST nativas, eliminando a necessidade de desenvolvimento customizado em AdvPL para a maioria dos cenários. Isso reduz a dependência do ambiente Protheus e torna as integrações mais estáveis e fáceis de manter.</p>',
			),
			array(
				'faq:totvs-legados',
				'A solução funciona em ambientes Protheus legados?',
				'<p>Sim. A CLI Connect suporta versões legadas do Protheus, utilizando conectores compatíveis com as APIs disponíveis em cada ambiente. A arquitetura de integração é adaptada ao que está disponível no seu ambiente, sem exigir atualização imediata do ERP.</p>',
			),
			array(
				'faq:totvs-filiais',
				'Como funciona a integração entre múltiplas filiais?',
				'<p>A CLI Connect centraliza as integrações de todas as filiais em uma única plataforma, sincronizando estoques, pedidos e cadastros entre as unidades automaticamente. Isso elimina divergências operacionais e garante que todas as filiais trabalhem com as mesmas informações em tempo real.</p>',
			),
			array(
				'faq:totvs-prazo',
				'Quanto tempo leva para colocar uma integração em produção?',
				'<p>Com os aceleradores prontos da CLI Connect, a maioria das integrações entre o Protheus e outros sistemas vai a produção em poucos dias. Cenários mais complexos passam por um levantamento rápido antes da implantação, mas o uso de modelos pré-configurados reduz significativamente o prazo em relação a projetos customizados.</p>',
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

		WP_CLI::log( sprintf( '  TOTVS FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/* =====================================================================
	   VTEX
	   ===================================================================== */

	/**
	 * Preenche os campos ACF do post cli_solucao "VTEX" — hero apenas.
	 */
	protected function preencher_solucao_vtex() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:vtex',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "vtex" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu VTEX',
			'solucao_hero_titulo'     => 'Conecte seu e-commerce ao ERP em tempo real',
			'solucao_hero_corpo'      => 'Integre a VTEX ao ERP, WMS e sistemas de pagamento para sincronizar pedidos, estoque e operações omnichannel com velocidade e segurança.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/vtex/',
			'solucao_hero_imagem'     => $this->img( 'vtex-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Escale sua operação digital conectada',
			'solucao_pilares_1_icone'  => $this->img( 'vtex-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Sincronize pedidos automaticamente',
			'solucao_pilares_1_desc'   => 'Conecte pedidos da VTEX ao ERP em tempo real sem processos manuais.',
			'solucao_pilares_2_icone'  => $this->img( 'vtex-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Atualize estoque omnichannel',
			'solucao_pilares_2_desc'   => 'Mantenha lojas físicas, marketplaces e canais digitais sincronizados.',
			'solucao_pilares_3_icone'  => $this->img( 'vtex-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Aproveite APIs nativas da VTEX',
			'solucao_pilares_3_desc'   => 'Utilize arquitetura API-first para integrar catálogo, pedidos e operações.',

			// 3 · Casos de uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize operações de e-commerce',
			'solucao_casos_1_icone'   => $this->img( 'vtex-caso-1' ),
			'solucao_casos_1_titulo'  => 'Sincronize pedidos com ERP',
			'solucao_casos_1_desc'    => 'Envie pedidos da VTEX ao ERP automaticamente para acelerar processamento.',
			'solucao_casos_2_icone'   => $this->img( 'vtex-caso-2' ),
			'solucao_casos_2_titulo'  => 'Atualize estoque entre canais',
			'solucao_casos_2_desc'    => 'Conecte loja física, marketplace e e-commerce com estoque sincronizado.',
			'solucao_casos_3_icone'   => $this->img( 'vtex-caso-3' ),
			'solucao_casos_3_titulo'  => 'Integre pagamentos e financeiro',
			'solucao_casos_3_desc'    => 'Concilie transações digitais com sistemas financeiros internos.',
			'solucao_casos_4_icone'   => $this->img( 'vtex-caso-4' ),
			'solucao_casos_4_titulo'  => 'Automatize ship from store',
			'solucao_casos_4_desc'    => 'Transforme lojas físicas em pontos de fulfillment para pedidos digitais.',
			'solucao_casos_5_icone'   => $this->img( 'vtex-caso-5' ),
			'solucao_casos_5_titulo'  => 'Conecte agentes de IA',
			'solucao_casos_5_desc'    => 'Disponibilize dados de e-commerce para agentes de IA automatizarem atendimento e operações.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',

			// 4 · Selos.
			'solucao_selos_eyebrow'   => 'compliance & segurança',
			'solucao_selos_titulo'    => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'     => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',

			// 5 · Diferencial técnico.
			'solucao_dif_eyebrow'     => 'diferencial técnico',
			'solucao_dif_titulo'      => 'Integrações preparadas para escala',
			'solucao_dif_corpo'       => 'Conecte a VTEX usando APIs oficiais com autenticação segura para suportar operações digitais de alto volume.',
			'solucao_dif_topico_1'    => 'Utilize VTEX IO e REST API',
			'solucao_dif_topico_2'    => 'Autentique com App Token',
			'solucao_dif_topico_3'    => 'Suporte picos de vendas',
			'solucao_dif_imagem'      => $this->img( 'vtex-dif' ),

			// 6 · Plataforma única.
			'solucao_plat_eyebrow'    => 'plataforma única',
			'solucao_plat_titulo'     => 'Unifique seu ecossistema de comércio',
			'solucao_plat_corpo'      => 'Conecte VTEX, ERP, WMS e pagamentos em uma única plataforma para manter sua operação sincronizada durante toda a jornada de compra.',
			'solucao_plat_topico_1'   => 'Centralize integrações comerciais',
			'solucao_plat_topico_2'   => 'Absorva picos operacionais',
			'solucao_plat_topico_3'   => 'Mantenha sistemas sincronizados',
			'solucao_plat_imagem'     => $this->img( 'vtex-plat' ),

			// 7 · Aceleradores.
			'solucao_acel_eyebrow'    => 'Aceleradores de integração',
			'solucao_acel_titulo'     => 'Comece com integrações de e-commerce',
			'solucao_acel_corpo'      => 'Utilize um modelo pronto para conectar VTEX ao ERP com pedidos, estoque e processos fiscais estruturados.',
			'solucao_acel_topico_1'   => 'Conecte operações rapidamente',
			'solucao_acel_topico_2'   => 'Reutilize fluxos comerciais',
			'solucao_acel_topico_3'   => 'Acelere novas integrações',
			'solucao_acel_topico_4'   => 'E muito mais...',
			'solucao_acel_btn_texto'  => 'Começar agora',
			'solucao_acel_btn_url'    => '/contato/',
			'solucao_acel_imagem'     => $this->img( 'vtex-acel' ),
		);
		foreach ( $campos as $chave => $valor ) {
			update_field( $chave, $valor, $post_id );
		}

		// 8 · FAQ.
		$faq_ids = $this->criar_faq_vtex( $post_id );
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $faq_ids, $post_id );

		WP_CLI::log( '  VTEX: todas as seções preenchidas.' );
	}

	/**
	 * Cria os posts cli_faq da VTEX e os vincula à solução.
	 *
	 * @param int $post_id ID do post cli_solucao da VTEX.
	 * @return int[]
	 */
	protected function criar_faq_vtex( $post_id ) {
		$itens = array(
			array(
				'faq:vtex-pico-trafego',
				'Como a CLI Connect powered by Boomi lida com picos de tráfego como Black Friday?',
				'<p>A plataforma Boomi opera em arquitetura elástica na nuvem, escalando automaticamente para absorver volumes de pedidos superiores aos períodos normais. Durante a Black Friday, os conectores continuam processando pedidos, atualizações de estoque e pagamentos com a mesma confiabilidade, sem necessidade de intervenção manual ou ajuste de infraestrutura.</p>',
			),
			array(
				'faq:vtex-multiplos-marketplaces',
				'É possível sincronizar estoque entre VTEX e múltiplos marketplaces?',
				'<p>Sim. A CLI Connect integra a VTEX com os principais marketplaces do mercado, mantendo o estoque atualizado em tempo real em todos os canais. Quando uma venda ocorre em qualquer canal, o estoque é decrementado automaticamente nos demais, evitando overselling e garantindo uma experiência de compra consistente.</p>',
			),
			array(
				'faq:vtex-ship-from-store',
				'Como funciona o fulfillment ship-from-store?',
				'<p>A integração conecta os pedidos recebidos na VTEX às lojas físicas elegíveis para envio, com base em regras de proximidade, estoque disponível e capacidade operacional. O processo de separação, embalagem e despacho é gerenciado pela integração entre a VTEX e o WMS ou sistema de ponto de venda da loja.</p>',
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

		WP_CLI::log( sprintf( '  VTEX FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}

	/* =====================================================================
	   SHOPIFY
	   ===================================================================== */

	/**
	 * Preenche os campos ACF do post cli_solucao "Shopify" — hero apenas.
	 */
	protected function preencher_solucao_shopify() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:shopify',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "shopify" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu Shopify',
			'solucao_hero_titulo'     => 'Conecte sua loja Shopify ao ERP sem depender de plugins genéricos',
			'solucao_hero_corpo'      => 'Integre Shopify, ERP, sistemas fiscais brasileiros e WMS para automatizar pedidos, estoque e operações financeiras com regras adaptadas ao seu negócio.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/shopify/',
			'solucao_hero_imagem'     => $this->img( 'shopify-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Escale sua operação Shopify conectada',
			'solucao_pilares_1_icone'  => $this->img( 'shopify-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Atenda regras fiscais brasileiras',
			'solucao_pilares_1_desc'   => 'Conecte Shopify ao fiscal brasileiro para automatizar NF-e e processos tributários específicos.',
			'solucao_pilares_2_icone'  => $this->img( 'shopify-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Sincronize estoques multicanal',
			'solucao_pilares_2_desc'   => 'Mantenha estoque atualizado entre Shopify, ERP e diferentes canais de venda.',
			'solucao_pilares_3_icone'  => $this->img( 'shopify-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Integre Shopify Plus',
			'solucao_pilares_3_desc'   => 'Suporte operações avançadas de grandes marcas usando Shopify ou Shopify Plus.',

			// 3 · Casos de uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize processos da loja digital',
			'solucao_casos_1_icone'   => $this->img( 'shopify-caso-1' ),
			'solucao_casos_1_titulo'  => 'Emita NF-e automaticamente',
			'solucao_casos_1_desc'    => 'Conecte pedidos Shopify ao sistema fiscal para gerar documentos eletrônicos.',
			'solucao_casos_2_icone'   => $this->img( 'shopify-caso-2' ),
			'solucao_casos_2_titulo'  => 'Sincronize estoque multicanal',
			'solucao_casos_2_desc'    => 'Atualize disponibilidade entre ERP, Shopify e marketplaces automaticamente.',
			'solucao_casos_3_icone'   => $this->img( 'shopify-caso-3' ),
			'solucao_casos_3_titulo'  => 'Concilie pagamentos digitais',
			'solucao_casos_3_desc'    => 'Conecte gateways de pagamento ao financeiro para facilitar conciliações.',
			'solucao_casos_4_icone'   => $this->img( 'shopify-caso-4' ),
			'solucao_casos_4_titulo'  => 'Automatize devoluções',
			'solucao_casos_4_desc'    => 'Integre processos de retorno entre Shopify, ERP e operações internas.',
			'solucao_casos_5_icone'   => $this->img( 'shopify-caso-5' ),
			'solucao_casos_5_titulo'  => 'Conecte agentes de IA',
			'solucao_casos_5_desc'    => 'Disponibilize dados da loja para agentes de IA automatizarem atendimento e operações.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',

			// 4 · Selos.
			'solucao_selos_eyebrow'   => 'compliance & segurança',
			'solucao_selos_titulo'    => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'     => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',

			// 5 · Diferencial Técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações nativas para Shopify',
			'solucao_dif_corpo'    => 'Conecte sua operação usando Shopify Admin API, GraphQL e Webhooks para sincronizar eventos em tempo real com segurança.',
			'solucao_dif_topico_1' => 'Utilize Shopify Admin API',
			'solucao_dif_topico_2' => 'Capture eventos com Webhooks',
			'solucao_dif_topico_3' => 'Conecte via GraphQL',
			'solucao_dif_imagem'   => $this->img( 'shopify-dif' ),

			// 6 · Plataforma Única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Supere limites dos aplicativos Shopify',
			'solucao_plat_corpo'    => 'Apps da Shopify resolvem cenários genéricos. Uma plataforma de integração dedicada conecta regras fiscais, múltiplos ERPs e operações complexas.',
			'solucao_plat_topico_1' => 'Centralize integrações comerciais',
			'solucao_plat_topico_2' => 'Adapte regras ao negócio',
			'solucao_plat_topico_3' => 'Reduza dependência de terceiros',
			'solucao_plat_imagem'   => $this->img( 'shopify-plat' ),

			// 7 · Aceleradores.
			'solucao_acel_eyebrow'  => 'Aceleradores de integração',
			'solucao_acel_titulo'   => 'Comece com uma operação Shopify integrada',
			'solucao_acel_corpo'    => 'Utilize um modelo pronto para conectar Shopify ao ERP e automatizar emissão fiscal brasileira desde o início.',
			'solucao_acel_topico_1' => 'Conecte ERP rapidamente',
			'solucao_acel_topico_2' => 'Automatize processos fiscais',
			'solucao_acel_topico_3' => 'Acelere novas integrações',
			'solucao_acel_topico_4' => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'shopify-acel' ),
		);
		foreach ( $campos as $chave => $valor ) {
			update_field( $chave, $valor, $post_id );
		}

		// 8 · FAQ.
		$faq_ids = $this->criar_faq_shopify( $post_id );
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $faq_ids, $post_id );

		WP_CLI::log( '  Shopify: todas as seções preenchidas.' );
	}

	/**
	 * Cria/atualiza os posts cli_faq para a solução Shopify.
	 *
	 * @param int $post_id ID do post cli_solucao (não usado diretamente, mantido por consistência).
	 * @return int[]
	 */
	protected function criar_faq_shopify( $post_id ) {
		$itens = array(
			array(
				'faq:shopify-nfe',
				'Como a CLI Connect powered by Boomi resolve emissão de NF-e a partir do Shopify?',
				'<p>A plataforma Boomi conecta os pedidos recebidos no Shopify ao sistema fiscal brasileiro, gerando automaticamente a NF-e com os dados corretos de produto, tributação e destinatário. O processo ocorre em tempo real após a confirmação do pedido, sem intervenção manual e em conformidade com as regras tributárias vigentes.</p>',
			),
			array(
				'faq:shopify-estoque-multicanal',
				'É possível sincronizar estoque com múltiplos canais?',
				'<p>Sim. A CLI Connect integra o Shopify com ERP, WMS e marketplaces, mantendo o estoque atualizado em tempo real em todos os canais. Quando uma venda ocorre em qualquer ponto, o saldo é decrementado automaticamente nos demais, eliminando overselling e garantindo consistência operacional.</p>',
			),
			array(
				'faq:shopify-plus',
				'Funciona com Shopify Plus?',
				'<p>Sim. A integração suporta tanto o Shopify quanto o Shopify Plus, aproveitando as APIs avançadas disponíveis na versão Plus para automações mais complexas, como fluxos de checkout personalizados, múltiplas lojas e operações B2B.</p>',
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

		WP_CLI::log( sprintf( '  Shopify FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}

	/* =====================================================================
	   ONBLOX (WMS/TMS)
	   ===================================================================== */

	/**
	 * Preenche os campos ACF do post cli_solucao "OnBlox (WMS/TMS)".
	 */
	protected function preencher_solucao_onblox() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:onblox',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "onblox" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu onblox',
			'solucao_hero_titulo'     => 'Conecte WMS e TMS ao ERP e transportadoras em tempo real',
			'solucao_hero_corpo'      => 'Integre o OnBlox aos ERPs, e-commerce e aplicativos de rastreamento para sincronizar estoque, operações logísticas e gestão de frota sem processos manuais.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/onblox/',
			'solucao_hero_imagem'     => $this->img( 'onblox-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Conecte toda sua operação logística',
			'solucao_pilares_1_icone'  => $this->img( 'onblox-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Sincronize inventário automaticamente',
			'solucao_pilares_1_desc'   => 'Mantenha estoque alinhado entre WMS, ERP e canais de venda em tempo real.',
			'solucao_pilares_2_icone'  => $this->img( 'onblox-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Integre gestão de frota',
			'solucao_pilares_2_desc'   => 'Conecte manutenção, documentos e licenças aos sistemas financeiros.',
			'solucao_pilares_3_icone'  => $this->img( 'onblox-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Acelere implantações logísticas',
			'solucao_pilares_3_desc'   => 'Reduza tempo de integração com fluxos preparados para operações de logística.',

			// 3 · Casos de uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize processos logísticos críticos',
			'solucao_casos_1_icone'   => $this->img( 'onblox-caso-1' ),
			'solucao_casos_1_titulo'  => 'Sincronize estoque com ERP',
			'solucao_casos_1_desc'    => 'Conecte OnBlox ao ERP e marketplaces para atualizar disponibilidade automaticamente.',
			'solucao_casos_2_icone'   => $this->img( 'onblox-caso-2' ),
			'solucao_casos_2_titulo'  => 'Direcione pedidos automaticamente',
			'solucao_casos_2_desc'    => 'Roteie pedidos para o centro de distribuição mais adequado.',
			'solucao_casos_3_icone'   => $this->img( 'onblox-caso-3' ),
			'solucao_casos_3_titulo'  => 'Conecte rastreamento de frota',
			'solucao_casos_3_desc'    => 'Integre transportadoras e aplicativos de rastreio ao ecossistema logístico.',
			'solucao_casos_4_icone'   => $this->img( 'onblox-caso-4' ),
			'solucao_casos_4_titulo'  => 'Atualize expedições em tempo real',
			'solucao_casos_4_desc'    => 'Envie status de separação e envio diretamente ao ERP.',
			'solucao_casos_5_icone'   => $this->img( 'onblox-caso-5' ),
			'solucao_casos_5_titulo'  => 'Conecte agentes de IA',
			'solucao_casos_5_desc'    => 'Disponibilize dados logísticos para agentes automatizarem atendimento e operações.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',

			// 4 · Selos.
			'solucao_selos_eyebrow'   => 'compliance & segurança',
			'solucao_selos_titulo'    => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'     => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',

			// 5 · Diferencial Técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações preparadas para alta operação',
			'solucao_dif_corpo'    => 'Conecte múltiplos coletores e dispositivos móveis com alto volume de dados para operações de armazém e frota.',
			'solucao_dif_topico_1' => 'Suporte alto volume operacional',
			'solucao_dif_topico_2' => 'Conecte múltiplos dispositivos móveis',
			'solucao_dif_topico_3' => 'Mantenha dados sincronizados',
			'solucao_dif_imagem'   => $this->img( 'onblox-dif' ),

			// 6 · Plataforma Única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Unifique logística e sistemas corporativos',
			'solucao_plat_corpo'    => 'Conecte armazém, frota, ERP e financeiro em uma única plataforma para eliminar planilhas e processos manuais.',
			'solucao_plat_topico_1' => 'Centralize dados logísticos',
			'solucao_plat_topico_2' => 'Conecte operações ao financeiro',
			'solucao_plat_topico_3' => 'Elimine exportações manuais',
			'solucao_plat_imagem'   => $this->img( 'onblox-plat' ),

			// 7 · Aceleradores.
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com logística integrada',
			'solucao_acel_corpo'     => 'Utilize um modelo pronto para conectar OnBlox ao ERP e acelerar a automação dos processos logísticos.',
			'solucao_acel_topico_1'  => 'Conecte WMS rapidamente',
			'solucao_acel_topico_2'  => 'Reutilize fluxos logísticos',
			'solucao_acel_topico_3'  => 'Acelere novas integrações',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'onblox-acel' ),
		);
		foreach ( $campos as $chave => $valor ) {
			update_field( $chave, $valor, $post_id );
		}

		// 8 · FAQ.
		$faq_ids = $this->criar_faq_onblox( $post_id );
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $faq_ids, $post_id );

		WP_CLI::log( '  OnBlox: todas as seções preenchidas.' );
	}

	/**
	 * Cria/atualiza os posts cli_faq para a solução OnBlox.
	 *
	 * @return int[]
	 */
	protected function criar_faq_onblox( $post_id ) {
		$itens = array(
			array(
				'faq:onblox-estoque-erp',
				'Como sincronizar estoque entre OnBlox e o ERP?',
				'<p>A CLI Connect cria uma integração em tempo real entre o OnBlox e o ERP, transmitindo movimentações de estoque automaticamente a cada transação no armazém. Isso elimina reprocessamentos manuais, reduz divergências de inventário e garante que os canais de venda reflitam sempre a disponibilidade real do estoque.</p>',
			),
			array(
				'faq:onblox-frota-financeiro',
				'É possível integrar dados de frota ao financeiro?',
				'<p>Sim. A integração conecta o módulo de gestão de frota do OnBlox ao sistema financeiro, automatizando o envio de custos de manutenção, abastecimento e multas. Com isso, os lançamentos contábeis são gerados de forma precisa e os relatórios de custo operacional ficam sempre atualizados sem intervenção manual.</p>',
			),
			array(
				'faq:onblox-multiplos-cds',
				'Como funciona com múltiplos centros de distribuição?',
				'<p>A plataforma CLI Connect suporta múltiplos centros de distribuição, roteando pedidos automaticamente para o CD mais adequado com base em regras de proximidade, disponibilidade de estoque e capacidade operacional. Cada CD opera de forma integrada ao ERP e ao e-commerce, mantendo visibilidade centralizada de toda a operação logística.</p>',
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

		WP_CLI::log( sprintf( '  OnBlox FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}

	/* =====================================================================
	   NARWAL (COMEX)
	   ===================================================================== */

	/**
	 * Preenche os campos ACF do post cli_solucao "Narwal (Comex)".
	 */
	protected function preencher_solucao_narwal() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:narwal',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "narwal" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero
			'solucao_hero_eyebrow'    => 'para o seu Narwal',
			'solucao_hero_titulo'     => 'Conecte o comércio exterior ao ERP do pedido ao desembaraço',
			'solucao_hero_corpo'      => 'Integre o Narwal ao ERP financeiro e aos órgãos oficiais de comércio exterior para automatizar importações, exportações e custos operacionais sem processos manuais.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/narwal/',
			'solucao_hero_imagem'     => $this->img( 'narwal-hero' ),
			// 2 · Pilares
			'solucao_pilares_titulo'   => 'Conecte toda a operação de comércio exterior',
			'solucao_pilares_1_icone'  => $this->img( 'narwal-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Integre pedidos internacionais',
			'solucao_pilares_1_desc'   => 'Conecte compras e vendas internacionais do Narwal diretamente ao ERP corporativo.',
			'solucao_pilares_2_icone'  => $this->img( 'narwal-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Sincronize canais oficiais',
			'solucao_pilares_2_desc'   => 'Integre Siscomex, Siscarga, Mantra e outros ambientes de comércio exterior.',
			'solucao_pilares_3_icone'  => $this->img( 'narwal-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Automatize custos de importação',
			'solucao_pilares_3_desc'   => 'Atualize fretes, desembaraços e despesas diretamente no financeiro.',
			// 3 · Casos de uso
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize processos de comércio exterior',
			'solucao_casos_1_icone'   => $this->img( 'narwal-caso-1' ),
			'solucao_casos_1_titulo'  => 'Sincronize pedidos de importação',
			'solucao_casos_1_desc'    => 'Conecte processos do Narwal ao ERP para eliminar registros manuais.',
			'solucao_casos_2_icone'   => $this->img( 'narwal-caso-2' ),
			'solucao_casos_2_titulo'  => 'Atualize custos automaticamente',
			'solucao_casos_2_desc'    => 'Envie fretes e despesas de desembaraço diretamente ao financeiro.',
			'solucao_casos_3_icone'   => $this->img( 'narwal-caso-3' ),
			'solucao_casos_3_titulo'  => 'Consolide operações de comex',
			'solucao_casos_3_desc'    => 'Centralize dados de diferentes filiais para análises estratégicas.',
			'solucao_casos_4_icone'   => $this->img( 'narwal-caso-4' ),
			'solucao_casos_4_titulo'  => 'Acompanhe embarques automaticamente',
			'solucao_casos_4_desc'    => 'Dispare alertas de ETD e ETA para sistemas conectados.',
			'solucao_casos_5_icone'   => $this->img( 'narwal-caso-5' ),
			'solucao_casos_5_titulo'  => 'Conecte agentes de IA',
			'solucao_casos_5_desc'    => 'Disponibilize dados de comércio exterior para agentes automatizarem processos administrativos.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',
			// 4 · Selos
			'solucao_selos_eyebrow'   => 'compliance & segurança',
			'solucao_selos_titulo'    => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'     => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
			// 5 · Diferencial
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações seguras para comércio exterior',
			'solucao_dif_corpo'    => 'Conecte o Narwal por APIs dedicadas com auditoria das etapas de importação e exportação para maior controle operacional.',
			'solucao_dif_topico_1' => 'Integre via APIs dedicadas',
			'solucao_dif_topico_2' => 'Audite etapas do processo',
			'solucao_dif_topico_3' => 'Proteja operações certificadas',
			'solucao_dif_imagem'   => $this->img( 'narwal-dif' ),
			// 6 · Plataforma Única
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Unifique comex e operação financeira',
			'solucao_plat_corpo'    => 'Conecte embarques, custos e lançamentos financeiros para eliminar controles manuais entre Narwal e ERP.',
			'solucao_plat_topico_1' => 'Centralize dados de comércio exterior',
			'solucao_plat_topico_2' => 'Automatize lançamentos financeiros',
			'solucao_plat_topico_3' => 'Reduza controles em planilhas',
			'solucao_plat_imagem'   => $this->img( 'narwal-plat' ),
			// 7 · Aceleradores
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com comex integrado',
			'solucao_acel_corpo'     => 'Utilize um modelo pronto para conectar processos do Narwal ao ERP financeiro e acelerar sua operação.',
			'solucao_acel_topico_1'  => 'Conecte processos rapidamente',
			'solucao_acel_topico_2'  => 'Reutilize fluxos de importação',
			'solucao_acel_topico_3'  => 'Acelere integrações financeiras',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'narwal-acel' ),
		);
		foreach ( $campos as $chave => $valor ) {
			update_field( $chave, $valor, $post_id );
		}

		// 8 · FAQ.
		$faq_ids = $this->criar_faq_narwal( $post_id );
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $faq_ids, $post_id );

		WP_CLI::log( '  Narwal: todas as seções preenchidas.' );
	}

	/**
	 * Cria/atualiza os posts cli_faq para a solução Narwal.
	 *
	 * @return int[]
	 */
	protected function criar_faq_narwal( $post_id ) {
		$itens = array(
			array(
				'faq:narwal-custos-importacao',
				'Como sincronizar custos de importação com o ERP automaticamente?',
				'<p>A CLI Connect integra o Narwal ao ERP por meio de uma camada de integração dedicada, transmitindo automaticamente fretes, despesas de desembaraço e tributos aduaneiros assim que são registrados no sistema. Isso elimina a necessidade de lançamentos manuais, reduz erros de conciliação e garante que o financeiro reflita os custos reais de cada processo de importação.</p>',
			),
			array(
				'faq:narwal-multiplas-filiais',
				'É possível integrar múltiplas filiais e operações de comex?',
				'<p>Sim. A plataforma CLI Connect suporta ambientes multi-empresa, permitindo centralizar as operações de comércio exterior de diferentes filiais em uma única integração com o ERP. Cada filial mantém sua visibilidade individual, enquanto os dados são consolidados para análises financeiras e operacionais corporativas.</p>',
			),
			array(
				'faq:narwal-duimp',
				'Como funciona com a transição para a DUIMP?',
				'<p>A integração é adaptada ao novo modelo da DUIMP (Declaração Única de Importação), conectando os processos do Narwal aos sistemas da Receita Federal e ao ERP de forma compatível com a nova sistemática. Isso garante que a transição ocorra sem interrupções no fluxo de dados entre comex e financeiro.</p>',
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

		WP_CLI::log( sprintf( '  Narwal FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}

	/* =====================================================================
	   NEOGRID
	   ===================================================================== */

	/**
	 * Preenche os campos ACF do post cli_solucao "Neogrid".
	 */
	protected function preencher_solucao_neogrid() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:neogrid',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "neogrid" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero
			'solucao_hero_eyebrow'    => 'para o seu Neogrid',
			'solucao_hero_titulo'     => 'Conecte sua rede EDI ao ERP sem depender apenas de conectores prontos',
			'solucao_hero_corpo'      => 'Integre o ecossistema Neogrid de EDI e visibilidade de varejo aos ERPs, BI e sistemas corporativos para ampliar sua operação além das conexões nativas.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/neogrid/',
			'solucao_hero_imagem'     => $this->img( 'neogrid-hero' ),
			// 2 · Pilares
			'solucao_pilares_titulo'   => 'Amplie o valor da sua rede Neogrid',
			'solucao_pilares_1_icone'  => $this->img( 'neogrid-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Conecte qualquer ERP corporativo',
			'solucao_pilares_1_desc'   => 'Integre Neogrid a ERPs além dos conectores nativos já disponíveis no mercado.',
			'solucao_pilares_2_icone'  => $this->img( 'neogrid-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Sincronize dados comerciais',
			'solucao_pilares_2_desc'   => 'Conecte pedidos, notas fiscais e informações de varejo ao ERP interno.',
			'solucao_pilares_3_icone'  => $this->img( 'neogrid-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Centralize dados da rede',
			'solucao_pilares_3_desc'   => 'Consolide informações de vendas e estoque de múltiplos parceiros comerciais.',
			// 3 · Casos de uso
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize processos da cadeia comercial',
			'solucao_casos_1_icone'   => $this->img( 'neogrid-caso-1' ),
			'solucao_casos_1_titulo'  => 'Traduza pedidos EDI automaticamente',
			'solucao_casos_1_desc'    => 'Converta pedidos recebidos pela Neogrid para o formato do ERP interno.',
			'solucao_casos_2_icone'   => $this->img( 'neogrid-caso-2' ),
			'solucao_casos_2_titulo'  => 'Conecte dados ao BI corporativo',
			'solucao_casos_2_desc'    => 'Envie informações de varejo e distribuição para análises estratégicas.',
			'solucao_casos_3_icone'   => $this->img( 'neogrid-caso-3' ),
			'solucao_casos_3_titulo'  => 'Integre notas fiscais ao financeiro',
			'solucao_casos_3_desc'    => 'Conecte documentos fiscais trafegados na Neogrid aos sistemas financeiros.',
			'solucao_casos_4_icone'   => $this->img( 'neogrid-caso-4' ),
			'solucao_casos_4_titulo'  => 'Monitore ruptura e estoque',
			'solucao_casos_4_desc'    => 'Consolide indicadores comerciais para equipes de vendas e operações.',
			'solucao_casos_5_icone'   => $this->img( 'neogrid-caso-5' ),
			'solucao_casos_5_titulo'  => 'Conecte agentes de IA',
			'solucao_casos_5_desc'    => 'Disponibilize dados comerciais e de varejo para agentes automatizarem atendimento e análises.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',
			// 4 · Selos
			'solucao_selos_eyebrow'   => 'compliance & segurança',
			'solucao_selos_titulo'    => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'     => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
			// 5 · Diferencial
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Traduza EDI com segurança',
			'solucao_dif_corpo'    => 'Conecte APIs e formatos EDI da Neogrid com tradução de mensagens para garantir comunicação entre diferentes sistemas.',
			'solucao_dif_topico_1' => 'Integre APIs e EDI Neogrid',
			'solucao_dif_topico_2' => 'Traduza formatos automaticamente',
			'solucao_dif_topico_3' => 'Conecte ERPs heterogêneos',
			'solucao_dif_imagem'   => $this->img( 'neogrid-dif' ),
			// 6 · Plataforma Única
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Conecte além do ERP principal',
			'solucao_plat_corpo'    => 'Amplie o ecossistema Neogrid conectando dados de EDI e visibilidade a novos sistemas sem limitar a operação aos conectores existentes.',
			'solucao_plat_topico_1' => 'Centralize dados da cadeia',
			'solucao_plat_topico_2' => 'Conecte sistemas adicionais',
			'solucao_plat_topico_3' => 'Expanda integrações existentes',
			'solucao_plat_imagem'   => $this->img( 'neogrid-plat' ),
			// 7 · Aceleradores
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com dados comerciais conectados',
			'solucao_acel_corpo'     => 'Utilize um modelo pronto para conectar Neogrid a ERP e BI com fluxos estruturados de dados comerciais.',
			'solucao_acel_topico_1'  => 'Conecte EDI rapidamente',
			'solucao_acel_topico_2'  => 'Reutilize fluxos comerciais',
			'solucao_acel_topico_3'  => 'Acelere novas integrações',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'neogrid-acel' ),
		);
		foreach ( $campos as $chave => $valor ) {
			update_field( $chave, $valor, $post_id );
		}

		// 8 · FAQ.
		$faq_ids = $this->criar_faq_neogrid( $post_id );
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $faq_ids, $post_id );

		WP_CLI::log( '  Neogrid: todas as seções preenchidas.' );
	}

	/**
	 * Cria/atualiza os posts cli_faq para a solução Neogrid.
	 *
	 * @return int[]
	 */
	protected function criar_faq_neogrid( $post_id ) {
		$itens = array(
			array(
				'faq:neogrid-erp-nativo',
				'Como integrar Neogrid a um ERP fora dos conectores nativos?',
				'<p>A CLI Connect atua como uma camada de integração independente dos conectores nativos da Neogrid, permitindo conectar qualquer ERP por meio da plataforma Boomi. Isso é feito traduzindo os formatos EDI e APIs da Neogrid para o padrão do ERP de destino, sem necessidade de desenvolvimento customizado em cada sistema.</p>',
			),
			array(
				'faq:neogrid-bi-varejo',
				'É possível levar dados de visibilidade de varejo para o BI corporativo?',
				'<p>Sim. A integração extrai dados de sell-out, ruptura e estoque disponíveis na Neogrid e os encaminha para o BI corporativo em tempo real ou em lotes programados. Isso permite que equipes de vendas e operações tomem decisões baseadas em dados atualizados, sem exportações manuais ou planilhas intermediárias.</p>',
			),
			array(
				'faq:neogrid-traducao-edi',
				'Como funciona a tradução de pedidos EDI?',
				'<p>A plataforma CLI Connect recebe os pedidos no formato EDI transmitido pela Neogrid e realiza a tradução automática para o formato nativo do ERP interno — seja XML, JSON ou layouts proprietários. O processo é auditado com logs de cada transação, garantindo rastreabilidade e facilidade de diagnóstico em caso de divergência.</p>',
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

		WP_CLI::log( sprintf( '  Neogrid FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}

	/* =====================================================================
	   TARGET SISTEMAS (ERP DISTRIBUIÇÃO)
	   ===================================================================== */

	/**
	 * Preenche os campos ACF do post cli_solucao "Target Sistemas (ERP Distribuição)".
	 */
	protected function preencher_solucao_target_sistemas() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:target-sistemas',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "target-sistemas" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu Target',
			'solucao_hero_titulo'     => 'Conecte seu ERP de distribuição à indústria, clientes e financeiro',
			'solucao_hero_corpo'      => 'Integre o Target ERP aos parceiros industriais, força de vendas, bancos e sistemas logísticos para automatizar operações de distribuição com escala.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/target-sistemas/',
			'solucao_hero_imagem'     => $this->img( 'target-sistemas-hero' ),
			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Escale sua operação de distribuição conectada',
			'solucao_pilares_1_icone'  => $this->img( 'target-sistemas-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Conecte fluxos de distribuição',
			'solucao_pilares_1_desc'   => 'Integre processos fiscais, logísticos e comerciais do atacado ao ecossistema corporativo.',
			'solucao_pilares_2_icone'  => $this->img( 'target-sistemas-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Automatize integrações EDI',
			'solucao_pilares_2_desc'   => 'Conecte fornecedores industriais ao Target com troca automática de informações.',
			'solucao_pilares_3_icone'  => $this->img( 'target-sistemas-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Sincronize força de vendas',
			'solucao_pilares_3_desc'   => 'Mantenha pedidos móveis atualizados no ERP em tempo real.',
			// 3 · Casos de uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize processos de distribuição',
			'solucao_casos_1_icone'   => $this->img( 'target-sistemas-caso-1' ),
			'solucao_casos_1_titulo'  => 'Conecte EDI com indústrias',
			'solucao_casos_1_desc'    => 'Automatize troca de dados Sell Out com fornecedores parceiros.',
			'solucao_casos_2_icone'   => $this->img( 'target-sistemas-caso-2' ),
			'solucao_casos_2_titulo'  => 'Sincronize pedidos móveis',
			'solucao_casos_2_desc'    => 'Envie pedidos da força de vendas diretamente ao Target ERP.',
			'solucao_casos_3_icone'   => $this->img( 'target-sistemas-caso-3' ),
			'solucao_casos_3_titulo'  => 'Concilie operações financeiras',
			'solucao_casos_3_desc'    => 'Integre bancos e processos financeiros entre múltiplas empresas.',
			'solucao_casos_4_icone'   => $this->img( 'target-sistemas-caso-4' ),
			'solucao_casos_4_titulo'  => 'Conecte logística ao ERP',
			'solucao_casos_4_desc'    => 'Integre WMS e roteirização para controlar operações de entrega.',
			'solucao_casos_5_icone'   => $this->img( 'target-sistemas-caso-5' ),
			'solucao_casos_5_titulo'  => 'Conecte agentes de IA',
			'solucao_casos_5_desc'    => 'Disponibilize dados de distribuição para agentes automatizarem atendimento e análises.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',
			// 4 · Selos.
			'solucao_selos_eyebrow'   => 'compliance & segurança',
			'solucao_selos_titulo'    => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'     => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
			// 5 · Diferencial Técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações para escala distributiva',
			'solucao_dif_corpo'    => 'Conecte operações com alto volume transacional mantendo performance em múltiplas filiais, SKUs e integrações simultâneas.',
			'solucao_dif_topico_1' => 'Suporte alto volume de dados',
			'solucao_dif_topico_2' => 'Conecte múltiplas filiais',
			'solucao_dif_topico_3' => 'Escalone operações comerciais',
			'solucao_dif_imagem'   => $this->img( 'target-sistemas-dif' ),
			// 6 · Plataforma Única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Unifique conexões da distribuição',
			'solucao_plat_corpo'    => 'Centralize integrações EDI, força de vendas e sistemas logísticos para reduzir esforço de conexão com novos parceiros.',
			'solucao_plat_topico_1' => 'Centralize integrações industriais',
			'solucao_plat_topico_2' => 'Acelere novos fornecedores',
			'solucao_plat_topico_3' => 'Reduza projetos repetitivos',
			'solucao_plat_imagem'   => $this->img( 'target-sistemas-plat' ),
			// 7 · Aceleradores.
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com distribuição conectada',
			'solucao_acel_corpo'     => 'Utilize um modelo pronto para conectar Target ERP aos parceiros industriais e aplicativos de força de vendas.',
			'solucao_acel_topico_1'  => 'Conecte fornecedores rapidamente',
			'solucao_acel_topico_2'  => 'Reutilize fluxos EDI',
			'solucao_acel_topico_3'  => 'Acelere novos parceiros',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'target-sistemas-acel' ),
		);
		foreach ( $campos as $chave => $valor ) {
			update_field( $chave, $valor, $post_id );
		}

		// 8 · FAQ.
		$faq_ids = $this->criar_faq_target_sistemas( $post_id );
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $faq_ids, $post_id );

		WP_CLI::log( '  Target Sistemas: todas as seções preenchidas.' );
	}

	/**
	 * Cria/atualiza os posts cli_faq para a solução Target Sistemas.
	 *
	 * @return int[]
	 */
	protected function criar_faq_target_sistemas( $post_id ) {
		$itens = array(
			array(
				'faq:target-sistemas-edi-onboarding',
				'Como acelerar o onboarding de um novo fornecedor via EDI?',
				'<p>A CLI Connect disponibiliza um acelerador de integração pronto para EDI que reduz o tempo de onboarding de fornecedores no Target ERP. O modelo inclui mapeamentos pré-configurados para os principais formatos de pedido, nota fiscal e confirmação de entrega, eliminando o desenvolvimento do zero e permitindo que novos parceiros sejam conectados em dias em vez de semanas.</p>',
			),
			array(
				'faq:target-sistemas-forca-vendas',
				'É possível integrar múltiplos aplicativos de força de vendas ao Target?',
				'<p>Sim. A plataforma CLI Connect opera como hub central entre o Target ERP e diferentes aplicativos de força de vendas simultaneamente. Os pedidos capturados em campo são transmitidos em tempo real ao ERP, com sincronização de estoque, tabela de preços e condições comerciais por equipe ou região, sem necessidade de customização individual em cada aplicativo.</p>',
			),
			array(
				'faq:target-sistemas-financeiro-multi-empresa',
				'Como funciona a consolidação financeira multi-empresa?',
				'<p>A CLI Connect centraliza o fluxo de dados financeiros entre filiais e a holding por meio de integrações governadas com o Target ERP. Conciliações bancárias, transferências entre empresas e relatórios consolidados são automatizados, garantindo que os sistemas financeiros de cada entidade estejam sincronizados sem intervenção manual e com rastreabilidade completa de cada transação.</p>',
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

		WP_CLI::log( sprintf( '  Target Sistemas FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}

	/* =====================================================================
	   SAP BUSINESS ONE
	   ===================================================================== */

	/**
	 * Preenche os campos ACF do post cli_solucao "SAP Business One".
	 */
	protected function preencher_solucao_sap_business_one() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:sap-business-one',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "sap-business-one" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu SAP B1',
			'solucao_hero_titulo'     => 'Conecte o SAP Business One sem exigir uma equipe SAP dedicada',
			'solucao_hero_corpo'      => 'Integre o SAP B1 ao e-commerce, CRM e sistemas fiscais para ampliar sua operação com uma camada de integração preparada para empresas em crescimento.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/sap-business-one/',
			'solucao_hero_imagem'     => $this->img( 'sap-business-one-hero' ),
			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Escale seu SAP Business One conectado',
			'solucao_pilares_1_icone'  => $this->img( 'sap-business-one-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Use APIs nativas do SAP B1',
			'solucao_pilares_1_desc'   => 'Conecte sistemas através do Service Layer REST e DI API oficial.',
			'solucao_pilares_2_icone'  => $this->img( 'sap-business-one-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Simplifique operações sem equipe SAP',
			'solucao_pilares_2_desc'   => 'Automatize processos do SAP B1 sem depender de especialistas dedicados.',
			'solucao_pilares_3_icone'  => $this->img( 'sap-business-one-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Reaproveite integrações SAP',
			'solucao_pilares_3_desc'   => 'Adapte componentes já utilizados em projetos S/4HANA para o B1.',
			// 3 · Casos de uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize processos do SAP Business One',
			'solucao_casos_1_icone'   => $this->img( 'sap-business-one-caso-1' ),
			'solucao_casos_1_titulo'  => 'Integre pedidos do e-commerce',
			'solucao_casos_1_desc'    => 'Envie pedidos digitais ao SAP B1 sem registros manuais.',
			'solucao_casos_2_icone'   => $this->img( 'sap-business-one-caso-2' ),
			'solucao_casos_2_titulo'  => 'Automatize processos fiscais',
			'solucao_casos_2_desc'    => 'Conecte emissão fiscal brasileira ao SAP Business One.',
			'solucao_casos_3_icone'   => $this->img( 'sap-business-one-caso-3' ),
			'solucao_casos_3_titulo'  => 'Consolide estoque multi-filial',
			'solucao_casos_3_desc'    => 'Centralize informações de estoque entre diferentes unidades.',
			'solucao_casos_4_icone'   => $this->img( 'sap-business-one-caso-4' ),
			'solucao_casos_4_titulo'  => 'Conecte CRM ao ERP',
			'solucao_casos_4_desc'    => 'Sincronize vendas entre Salesforce, HubSpot e SAP B1.',
			'solucao_casos_5_icone'   => $this->img( 'sap-business-one-caso-5' ),
			'solucao_casos_5_titulo'  => 'Conecte agentes de IA',
			'solucao_casos_5_desc'    => 'Disponibilize dados do SAP B1 para agentes automatizarem atendimento e análises.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',
			// 4 · Selos.
			'solucao_selos_eyebrow'   => 'compliance & segurança',
			'solucao_selos_titulo'    => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'     => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
			// 5 · Diferencial Técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações nativas para SAP B1',
			'solucao_dif_corpo'    => 'Utilize o Service Layer REST/OData do SAP Business One com autenticação segura para conectar aplicações corporativas.',
			'solucao_dif_topico_1' => 'Utilize Service Layer oficial',
			'solucao_dif_topico_2' => 'Conecte via REST e OData',
			'solucao_dif_topico_3' => 'Proteja sessões autenticadas',
			'solucao_dif_imagem'   => $this->img( 'sap-business-one-dif' ),
			// 6 · Plataforma Única
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Prepare seu SAP para crescer',
			'solucao_plat_corpo'    => 'Mantenha a mesma camada de integração ao evoluir do SAP Business One para S/4HANA ou operar diferentes versões simultaneamente.',
			'solucao_plat_topico_1' => 'Reaproveite integrações SAP',
			'solucao_plat_topico_2' => 'Evite reconstruções futuras',
			'solucao_plat_topico_3' => 'Padronize arquiteturas corporativas',
			'solucao_plat_imagem'   => $this->img( 'sap-business-one-plat' ),
			// 7 · Aceleradores
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com SAP Business One conectado',
			'solucao_acel_corpo'     => 'Utilize um modelo pronto para integrar SAP B1 ao e-commerce, CRM e sistemas fiscais com fluxos estruturados.',
			'solucao_acel_topico_1'  => 'Conecte sistemas rapidamente',
			'solucao_acel_topico_2'  => 'Reutilize integrações SAP',
			'solucao_acel_topico_3'  => 'Acelere novos projetos',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'sap-business-one-acel' ),
		);
		foreach ( $campos as $chave => $valor ) {
			update_field( $chave, $valor, $post_id );
		}

		// 8 · FAQ.
		$faq_ids = $this->criar_faq_sap_business_one( $post_id );
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $faq_ids, $post_id );

		WP_CLI::log( '  SAP Business One: todas as seções preenchidas.' );
	}

	/**
	 * Cria/atualiza os posts cli_faq do SAP Business One e retorna seus IDs.
	 *
	 * @param int $post_id ID do post cli_solucao pai (não utilizado diretamente).
	 * @return int[]
	 */
	protected function criar_faq_sap_business_one( $post_id ) {
		$itens = array(
			array(
				'faq:sap-business-one-service-layer',
				'Como funciona a integração via Service Layer do SAP B1?',
				'<p>A CLI Connect utiliza o Service Layer REST/OData oficial do SAP Business One para conectar aplicações corporativas sem depender de customizações frágeis. A autenticação segura por sessão garante que cada requisição seja validada antes do acesso ao banco de dados do SAP B1, e o modelo de integração abstrai a complexidade da API nativa, entregando mapeamentos prontos para pedidos, clientes, produtos e documentos fiscais.</p>',
			),
			array(
				'faq:sap-business-one-migracao-s4hana',
				'É possível migrar as integrações quando a empresa evoluir para S/4HANA?',
				'<p>Sim. A camada de integração da CLI Connect é agnóstica à versão do SAP. Ao migrar do Business One para o S/4HANA, os fluxos de dados permanecem na plataforma e apenas o conector de destino é reconfigurado, preservando todo o histórico de mapeamentos, regras de negócio e conexões com sistemas externos sem reconstrução do zero.</p>',
			),
			array(
				'faq:sap-business-one-multiplas-filiais',
				'Como lidar com múltiplas filiais no SAP B1?',
				'<p>A CLI Connect opera com múltiplas empresas no SAP Business One simultaneamente, roteando transações para a filial correta com base em regras de negócio configuráveis. É possível consolidar pedidos de diferentes canais em filiais específicas, sincronizar tabelas de preços e estoques entre unidades e gerar relatórios financeiros integrados sem customizações adicionais no SAP.</p>',
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

		WP_CLI::log( sprintf( '  SAP Business One FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}

	/* =====================================================================
	   SAP ECC
	   ===================================================================== */

	/**
	 * Preenche os campos ACF do post cli_solucao "SAP ECC".
	 */
	protected function preencher_solucao_sap_ecc() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:sap-ecc',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "sap-ecc" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu SAP ECC',
			'solucao_hero_titulo'     => 'Conecte seu SAP ECC à nuvem sem esperar a migração para S/4HANA',
			'solucao_hero_corpo'      => 'Modernize a conectividade do SAP ECC 6.0 integrando sistemas SaaS, e-commerce e aplicações corporativas sem substituir o ERP atual.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/sap-ecc/',
			'solucao_hero_imagem'     => $this->img( 'sap-ecc-hero' ),
			// 2 · Pilares
			'solucao_pilares_titulo'   => 'Modernize seu SAP ECC em produção',
			'solucao_pilares_1_icone'  => $this->img( 'sap-ecc-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Aproveite integrações nativas SAP',
			'solucao_pilares_1_desc'   => 'Conecte ECC usando RFC, BAPI e IDoc sem alterar processos existentes.',
			'solucao_pilares_2_icone'  => $this->img( 'sap-ecc-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Proteja seu ambiente legado',
			'solucao_pilares_2_desc'   => 'Conecte sistemas externos sem expor o ECC on-premises à internet.',
			'solucao_pilares_3_icone'  => $this->img( 'sap-ecc-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Conecte aplicações modernas',
			'solucao_pilares_3_desc'   => 'Integre Salesforce, e-commerce e SaaS enquanto o ECC continua operando.',
			// 3 · Casos de Uso
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Conecte processos do SAP ECC',
			'solucao_casos_1_icone'   => $this->img( 'sap-ecc-caso-1' ),
			'solucao_casos_1_titulo'  => 'Sincronize pedidos digitais',
			'solucao_casos_1_desc'    => 'Integre e-commerce ao ECC para automatizar entrada de pedidos.',
			'solucao_casos_2_icone'   => $this->img( 'sap-ecc-caso-2' ),
			'solucao_casos_2_titulo'  => 'Conecte ECC ao CRM',
			'solucao_casos_2_desc'    => 'Sincronize dados comerciais entre Salesforce, CRM e ERP.',
			'solucao_casos_3_icone'   => $this->img( 'sap-ecc-caso-3' ),
			'solucao_casos_3_titulo'  => 'Migre sem interromper operações',
			'solucao_casos_3_desc'    => 'Execute cenários paralelos entre ECC e S/4HANA durante transição.',
			'solucao_casos_4_icone'   => $this->img( 'sap-ecc-caso-4' ),
			'solucao_casos_4_titulo'  => 'Disponibilize dados do ECC',
			'solucao_casos_4_desc'    => 'Exponha informações legadas como APIs modernas para aplicações.',
			'solucao_casos_5_icone'   => $this->img( 'sap-ecc-caso-5' ),
			'solucao_casos_5_titulo'  => 'Conecte agentes de IA',
			'solucao_casos_5_desc'    => 'Disponibilize dados do ECC para agentes de IA sem expor o core do sistema.',
			// 4 · Selos
			'solucao_selos_eyebrow' => 'compliance & segurança',
			'solucao_selos_titulo'  => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'   => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
			// 5 · Diferencial Técnico
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Conectividade segura para SAP ECC',
			'solucao_dif_corpo'    => 'Utilize RFC, BAPI e IDoc com Runtime seguro para integrar o ECC sem expor ambientes legados à internet.',
			'solucao_dif_topico_1' => 'Utilize protocolos SAP nativos',
			'solucao_dif_topico_2' => 'Proteja conexões on-premises',
			'solucao_dif_topico_3' => 'Evite exposição externa',
			'solucao_dif_imagem'   => $this->img( 'sap-ecc-dif' ),
			// 6 · Plataforma Única
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Modernize antes do S/4HANA',
			'solucao_plat_corpo'    => 'A mesma plataforma que conecta seu futuro S/4HANA conecta seu ECC atual, garantindo evolução contínua sem reconstruções.',
			'solucao_plat_topico_1' => 'Conecte ECC hoje',
			'solucao_plat_topico_2' => 'Prepare migração futura',
			'solucao_plat_topico_3' => 'Reaproveite integrações existentes',
			'solucao_plat_imagem'   => $this->img( 'sap-ecc-plat' ),
			// 7 · Aceleradores
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com SAP ECC conectado',
			'solucao_acel_corpo'     => 'Utilize um modelo pronto para conectar ECC a sistemas SaaS modernos como Salesforce e e-commerce.',
			'solucao_acel_topico_1'  => 'Conecte SaaS rapidamente',
			'solucao_acel_topico_2'  => 'Reutilize padrões SAP',
			'solucao_acel_topico_3'  => 'Acelere modernização',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'sap-ecc-acel' ),
		);
		foreach ( $campos as $chave => $valor ) {
			update_field( $chave, $valor, $post_id );
		}

		// 8 · FAQ.
		$faq_ids = $this->criar_faq_sap_ecc( $post_id );
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $faq_ids, $post_id );

		WP_CLI::log( '  SAP ECC: todas as seções preenchidas.' );
	}

	/**
	 * Cria/atualiza os posts cli_faq do SAP ECC e retorna seus IDs.
	 *
	 * @param int $post_id ID do post cli_solucao pai.
	 * @return int[]
	 */
	protected function criar_faq_sap_ecc( $post_id ) {
		$itens = array(
			array(
				'faq:sap-ecc-firewall',
				'É possível conectar o ECC sem abrir portas de firewall?',
				'<p>Sim. A CLI Connect utiliza o Boomi Atom instalado dentro da rede on-premises do ECC, que estabelece conexões de saída seguras sem exigir a abertura de portas de entrada no firewall. O Runtime local se comunica com a plataforma em nuvem de forma criptografada, permitindo que o SAP ECC permaneça completamente isolado da internet enquanto troca dados com sistemas externos.</p>',
			),
			array(
				'faq:sap-ecc-cutover-s4hana',
				'Como funciona o cutover em paralelo com o S/4HANA?',
				'<p>Durante a migração para S/4HANA, a CLI Connect opera os dois sistemas simultaneamente na mesma plataforma de integração. Os fluxos de dados podem ser roteados para ECC, S/4HANA ou ambos, conforme a fase do projeto, eliminando a necessidade de reconstruir integrações após o cutover. O histórico de transações e as regras de mapeamento são preservados durante toda a transição.</p>',
			),
			array(
				'faq:sap-ecc-pos-migracao',
				'O que muda na integração depois da migração para S/4HANA?',
				'<p>Com a CLI Connect, praticamente nada precisa ser reconstruído. A plataforma abstrai as diferenças entre RFC/BAPI do ECC e os OData/BAPIs do S/4HANA, adaptando os conectores automaticamente. As integrações com sistemas externos como Salesforce, e-commerce e sistemas fiscais continuam funcionando sem alteração nos fluxos de negócio já configurados.</p>',
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

		WP_CLI::log( sprintf( '  SAP ECC FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}

	/* =====================================================================
	   ORACLE NETSUITE
	   ===================================================================== */

	/**
	 * Preenche os campos ACF do post cli_solucao "Oracle NetSuite".
	 */
	protected function preencher_solucao_oracle_netsuite() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:oracle-netsuite',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "oracle-netsuite" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu NetSuite',
			'solucao_hero_titulo'     => 'Conecte o NetSuite ao seu stack sem depender só de SuiteScript',
			'solucao_hero_corpo'      => 'Integre NetSuite a e-commerce, CRM e sistemas financeiros usando APIs nativas sem customizações excessivas de SuiteScript.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/oracle-netsuite/',
			'solucao_hero_imagem'     => $this->img( 'oracle-netsuite-hero' ),
			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Escale seu NetSuite conectado',
			'solucao_pilares_1_icone'  => $this->img( 'oracle-netsuite-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Use APIs nativas do NetSuite',
			'solucao_pilares_1_desc'   => 'Integre via SuiteTalk REST/SOAP e RESTlets sem código customizado excessivo.',
			'solucao_pilares_2_icone'  => $this->img( 'oracle-netsuite-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Simplifique operações globais',
			'solucao_pilares_2_desc'   => 'Gerencie integrações multi-subsidiária com padronização de processos financeiros.',
			'solucao_pilares_3_icone'  => $this->img( 'oracle-netsuite-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Reduza customizações SuiteScript',
			'solucao_pilares_3_desc'   => 'Substitua scripts específicos por integrações reutilizáveis e sem manutenção.',
			// 3 · Casos de Uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize processos do NetSuite',
			'solucao_casos_1_icone'   => $this->img( 'oracle-netsuite-caso-1' ),
			'solucao_casos_1_titulo'  => 'Sincronize pedidos digitais',
			'solucao_casos_1_desc'    => 'Integre e-commerce ao NetSuite para automatizar entrada de pedidos.',
			'solucao_casos_2_icone'   => $this->img( 'oracle-netsuite-caso-2' ),
			'solucao_casos_2_titulo'  => 'Consolide finanças globais',
			'solucao_casos_2_desc'    => 'Sincronize dados financeiros entre subsidiárias automaticamente.',
			'solucao_casos_3_icone'   => $this->img( 'oracle-netsuite-caso-3' ),
			'solucao_casos_3_titulo'  => 'Integre CRM ao financeiro',
			'solucao_casos_3_desc'    => 'Conecte Salesforce ao NetSuite para unificar dados comerciais e financeiros.',
			'solucao_casos_4_icone'   => $this->img( 'oracle-netsuite-caso-4' ),
			'solucao_casos_4_titulo'  => 'Automatize reconhecimento de receita',
			'solucao_casos_4_desc'    => 'Processe eventos de venda no NetSuite sem intervenção manual.',
			'solucao_casos_5_icone'   => $this->img( 'oracle-netsuite-caso-5' ),
			'solucao_casos_5_titulo'  => 'Conecte agentes de IA',
			'solucao_casos_5_desc'    => 'Disponibilize dados do NetSuite para agentes de IA sem expor o core.',
			// 4 · Selos.
			'solucao_selos_eyebrow' => 'compliance & segurança',
			'solucao_selos_titulo'  => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'   => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
			// 5 · Diferencial Técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações seguras para NetSuite',
			'solucao_dif_corpo'    => 'Utilize TBA e OAuth 2.0 para autenticar integrações NetSuite com segurança corporativa sem expor credenciais.',
			'solucao_dif_topico_1' => 'Utilize TBA e OAuth 2.0',
			'solucao_dif_topico_2' => 'Proteja acessos corporativos',
			'solucao_dif_topico_3' => 'Integre APIs oficiais',
			'solucao_dif_imagem'   => $this->img( 'oracle-netsuite-dif' ),
			// 6 · Plataforma Única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Escale operações multi-subsidiária',
			'solucao_plat_corpo'    => 'A mesma plataforma que conecta uma subsidiária replica integrações NetSuite OneWorld para todo o grupo corporativo.',
			'solucao_plat_topico_1' => 'Replique integrações globais',
			'solucao_plat_topico_2' => 'Padronize processos financeiros',
			'solucao_plat_topico_3' => 'Reduza desenvolvimento específico',
			'solucao_plat_imagem'   => $this->img( 'oracle-netsuite-plat' ),
			// 7 · Aceleradores.
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com NetSuite integrado',
			'solucao_acel_corpo'     => 'Utilize um modelo pronto para integrar NetSuite a e-commerce, CRM e sistemas financeiros com fluxos estruturados.',
			'solucao_acel_topico_1'  => 'Conecte sistemas rapidamente',
			'solucao_acel_topico_2'  => 'Reutilize integrações NetSuite',
			'solucao_acel_topico_3'  => 'Acelere novos projetos',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'oracle-netsuite-acel' ),
			// CTA card azul (casos de uso).
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',
		);
		foreach ( $campos as $chave => $valor ) {
			update_field( $chave, $valor, $post_id );
		}

		// 8 · FAQ.
		$faq_ids = $this->criar_faq_oracle_netsuite( $post_id );
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $faq_ids, $post_id );

		WP_CLI::log( '  Oracle NetSuite: todas as seções preenchidas.' );
	}

	/**
	 * Cria/atualiza os posts cli_faq do Oracle NetSuite e retorna seus IDs.
	 *
	 * @param int $post_id ID do post cli_solucao pai.
	 * @return int[]
	 */
	protected function criar_faq_oracle_netsuite( $post_id ) {
		$itens = array(
			array(
				'faq:oracle-netsuite-suiteScript',
				'Como reduzir a dependência de SuiteScript customizado?',
				'<p>A CLI Connect utiliza as APIs nativas do NetSuite — SuiteTalk REST/SOAP e RESTlets — para criar integrações reutilizáveis sem precisar de scripts específicos por projeto. Em vez de desenvolver e manter SuiteScript para cada integração, a plataforma centraliza os fluxos em conectores configuráveis, reduzindo o volume de código customizado e o esforço de manutenção ao longo do tempo.</p>',
			),
			array(
				'faq:oracle-netsuite-subsidiarias',
				'É possível replicar a mesma integração para novas subsidiárias?',
				'<p>Sim. Com o NetSuite OneWorld, a CLI Connect permite replicar integrações entre subsidiárias sem reconstrução. A plataforma gerencia a segmentação por subsidiária, aplica as regras de negócio específicas de cada entidade e padroniza os fluxos de dados financeiros e operacionais globalmente, garantindo consistência sem desenvolvimento adicional para cada nova subsidiária incorporada.</p>',
			),
			array(
				'faq:oracle-netsuite-tba-oauth2',
				'Como funciona a autenticação via TBA/OAuth2?',
				'<p>O NetSuite suporta Token-Based Authentication (TBA) e OAuth 2.0 como mecanismos de autenticação para integrações via API. A CLI Connect utiliza essas credenciais para estabelecer conexões seguras sem armazenar senhas de usuário, seguindo as melhores práticas de segurança corporativa. O acesso é controlado por papéis e permissões do NetSuite, garantindo que cada integração opere apenas dentro do escopo autorizado.</p>',
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

		WP_CLI::log( sprintf( '  Oracle NetSuite FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}

	/* =====================================================================
	   TOTVS CONSINCO
	   ===================================================================== */

	/**
	 * Preenche os campos ACF do post cli_solucao "TOTVS Consinco".
	 */
	protected function preencher_solucao_totvs_consinco() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:totvs-consinco',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "totvs-consinco" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu Consinco',
			'solucao_hero_titulo'     => 'Conecte o Consinco da gôndola ao centro de distribuição',
			'solucao_hero_corpo'      => 'Integre o ERP de varejo alimentar com PDV, e-commerce e fornecedores para sincronizar preços, estoque e operações em toda a rede.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/totvs-consinco/',
			'solucao_hero_imagem'     => $this->img( 'totvs-consinco-hero' ),
			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Integre toda operação do varejo alimentar',
			'solucao_pilares_1_icone'  => $this->img( 'totvs-consinco-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Conecte compras e operações',
			'solucao_pilares_1_desc'   => 'Integre processos de compras, preços e promoções do varejo.',
			'solucao_pilares_2_icone'  => $this->img( 'totvs-consinco-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Automatize conexões EDI',
			'solucao_pilares_2_desc'   => 'Sincronize dados com fornecedores sem processos manuais.',
			'solucao_pilares_3_icone'  => $this->img( 'totvs-consinco-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Unifique preços e canais',
			'solucao_pilares_3_desc'   => 'Mantenha loja física e digital sempre alinhadas.',
			// 3 · Casos de Uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize processos do varejo alimentar',
			'solucao_casos_1_icone'   => $this->img( 'totvs-consinco-caso-1' ),
			'solucao_casos_1_titulo'  => 'Sincronize preços e promoções',
			'solucao_casos_1_desc'    => 'Atualize valores entre Consinco, PDV e e-commerce automaticamente.',
			'solucao_casos_2_icone'   => $this->img( 'totvs-consinco-caso-2' ),
			'solucao_casos_2_titulo'  => 'Integre fornecedores via EDI',
			'solucao_casos_2_desc'    => 'Conecte indústrias parceiras ao fluxo de compras.',
			'solucao_casos_3_icone'   => $this->img( 'totvs-consinco-caso-3' ),
			'solucao_casos_3_titulo'  => 'Consolide vendas da rede',
			'solucao_casos_3_desc'    => 'Centralize dados de vendas multi-loja para BI.',
			'solucao_casos_4_icone'   => $this->img( 'totvs-consinco-caso-4' ),
			'solucao_casos_4_titulo'  => 'Automatize reposição de estoque',
			'solucao_casos_4_desc'    => 'Use giro de vendas para apoiar abastecimento automático.',
			'solucao_casos_5_icone'   => $this->img( 'totvs-consinco-caso-5' ),
			'solucao_casos_5_titulo'  => 'Conecte agentes de IA',
			'solucao_casos_5_desc'    => 'Disponibilize dados do varejo para agentes de IA sem expor o core do sistema.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',
			// 4 · Selos.
			'solucao_selos_eyebrow' => 'compliance & segurança',
			'solucao_selos_titulo'  => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'   => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
			// 5 · Diferencial Técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações preparadas para alto volume',
			'solucao_dif_corpo'    => 'Conecte operações de supermercado com milhares de SKUs e múltiplas lojas mantendo performance, estabilidade e processamento contínuo.',
			'solucao_dif_topico_1' => 'Suporte grandes volumes transacionais',
			'solucao_dif_topico_2' => 'Conecte múltiplas lojas',
			'solucao_dif_topico_3' => 'Processe dados continuamente',
			'solucao_dif_imagem'   => $this->img( 'totvs-consinco-dif' ),
			// 6 · Plataforma Única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Centralize conexões de toda rede',
			'solucao_plat_corpo'    => 'Unifique integrações EDI, PDV e e-commerce em uma única plataforma para reduzir esforço operacional e acelerar novos parceiros.',
			'solucao_plat_topico_1' => 'Centralize integrações EDI',
			'solucao_plat_topico_2' => 'Reduza onboarding de fornecedores',
			'solucao_plat_topico_3' => 'Reutilize conexões existentes',
			'solucao_plat_imagem'   => $this->img( 'totvs-consinco-plat' ),
			// 7 · Aceleradores.
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com integrações estruturadas',
			'solucao_acel_corpo'     => 'Utilize um modelo pronto para conectar Consinco, fornecedores EDI, PDV e e-commerce com mais velocidade.',
			'solucao_acel_topico_1'  => 'Conecte fornecedores rapidamente',
			'solucao_acel_topico_2'  => 'Adapte fluxos existentes',
			'solucao_acel_topico_3'  => 'Acelere novas integrações',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'totvs-consinco-acel' ),
		);
		foreach ( $campos as $chave => $valor ) {
			update_field( $chave, $valor, $post_id );
		}

		// 8 · FAQ.
		$faq_ids = $this->criar_faq_totvs_consinco( $post_id );
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $faq_ids, $post_id );

		WP_CLI::log( '  TOTVS Consinco: todas as seções preenchidas.' );
	}

	/**
	 * Cria/atualiza os posts cli_faq do TOTVS Consinco e retorna seus IDs.
	 *
	 * @param int $post_id ID do post cli_solucao pai.
	 * @return int[]
	 */
	protected function criar_faq_totvs_consinco( $post_id ) {
		$itens = array(
			array(
				'faq:totvs-consinco-precos',
				'Como sincronizar preços entre loja física e digital?',
				'<p>A CLI Connect cria um fluxo centralizado que captura alterações de preços e promoções diretamente no Consinco e distribui automaticamente para o PDV e a plataforma de e-commerce. Qualquer mudança de tabela de preços, campanha promocional ou desconto é propagada em tempo real para todos os canais, eliminando divergências de valores e retrabalho manual nas equipes comerciais.</p>',
			),
			array(
				'faq:totvs-consinco-edi',
				'É possível integrar múltiplos fornecedores via EDI rapidamente?',
				'<p>Sim. A CLI Connect oferece aceleradores de integração EDI que padronizam o onboarding de novos fornecedores. Em vez de construir um mapeamento específico para cada parceiro, a plataforma reutiliza conectores EDI configuráveis que suportam os principais formatos do setor. Isso reduz o tempo de integração de semanas para dias e facilita a adição de novos fornecedores conforme a operação cresce.</p>',
			),
			array(
				'faq:totvs-consinco-reposicao',
				'Como funciona a reposição automática de estoque?',
				'<p>A CLI Connect conecta os dados de giro de vendas do Consinco com o sistema de compras e os fornecedores, criando um ciclo automatizado de reposição. Quando o estoque de um produto atinge o ponto de pedido definido, a plataforma dispara automaticamente o processo de compra com o fornecedor correspondente, sem necessidade de intervenção manual. Isso reduz rupturas de gôndola e excesso de estoque em toda a rede.</p>',
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

		WP_CLI::log( sprintf( '  TOTVS Consinco FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}

	/* =====================================================================
	   TOTVS LINX
	   ===================================================================== */

	/**
	 * Preenche os campos ACF do post cli_solucao "TOTVS Linx".
	 */
	protected function preencher_solucao_totvs_linx() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:totvs-linx',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "totvs-linx" não encontrado.' );
			return;
		}
		$post_id = $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu Linx',
			'solucao_hero_titulo'     => 'Conecte o Linx do PDV ao ERP corporativo',
			'solucao_hero_corpo'      => 'Integre soluções Linx de varejo, moda, postos e farmácias ao ERP, CRM e programas de fidelidade para centralizar operações comerciais.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/totvs-linx/',
			'solucao_hero_imagem'     => $this->img( 'totvs-linx-hero' ),
			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Escale sua operação Linx conectada',
			'solucao_pilares_1_icone'  => $this->img( 'totvs-linx-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Conecte verticais Linx',
			'solucao_pilares_1_desc'   => 'Integre operações de moda, varejo, postos e farmácias ao ecossistema corporativo.',
			'solucao_pilares_2_icone'  => $this->img( 'totvs-linx-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Sincronize vendas em tempo real',
			'solucao_pilares_2_desc'   => 'Conecte transações do PDV aos sistemas financeiros automaticamente.',
			'solucao_pilares_3_icone'  => $this->img( 'totvs-linx-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Integre fidelidade e CRM',
			'solucao_pilares_3_desc'   => 'Conecte dados de clientes aos programas de relacionamento.',
			// 3 · Casos de Uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize processos do varejo Linx',
			'solucao_casos_1_icone'   => $this->img( 'totvs-linx-caso-1' ),
			'solucao_casos_1_titulo'  => 'Sincronize vendas com ERP',
			'solucao_casos_1_desc'    => 'Envie transações do PDV Linx ao financeiro corporativo automaticamente.',
			'solucao_casos_2_icone'   => $this->img( 'totvs-linx-caso-2' ),
			'solucao_casos_2_titulo'  => 'Conecte programas de fidelidade',
			'solucao_casos_2_desc'    => 'Integre dados de clientes com CRM e plataformas de relacionamento.',
			'solucao_casos_3_icone'   => $this->img( 'totvs-linx-caso-3' ),
			'solucao_casos_3_titulo'  => 'Consolide vendas multi-loja',
			'solucao_casos_3_desc'    => 'Centralize resultados de diferentes lojas e bandeiras comerciais.',
			'solucao_casos_4_icone'   => $this->img( 'totvs-linx-caso-4' ),
			'solucao_casos_4_titulo'  => 'Integre documentos fiscais',
			'solucao_casos_4_desc'    => 'Conecte SAT, NF-e e NFC-e em uma operação centralizada.',
			'solucao_casos_5_icone'   => $this->img( 'totvs-linx-caso-5' ),
			'solucao_casos_5_titulo'  => 'Conecte agentes de IA',
			'solucao_casos_5_desc'    => 'Disponibilize dados do varejo para agentes administrativos sem expor o core do sistema.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',
			// 4 · Selos.
			'solucao_selos_eyebrow' => 'compliance & segurança',
			'solucao_selos_titulo'  => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'   => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
			// 5 · Diferencial Técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações para alto volume de vendas',
			'solucao_dif_corpo'    => 'Conecte operações de PDV com processamento em tempo real para suportar grandes volumes de transações comerciais.',
			'solucao_dif_topico_1' => 'Processe vendas em tempo real',
			'solucao_dif_topico_2' => 'Suporte alto volume transacional',
			'solucao_dif_topico_3' => 'Conecte múltiplas unidades',
			'solucao_dif_imagem'   => $this->img( 'totvs-linx-dif' ),
			// 6 · Plataforma Única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Unifique operações de varejo',
			'solucao_plat_corpo'    => 'Centralize dados de diferentes soluções Linx para conectar vendas, financeiro e CRM sem customizar sistemas existentes.',
			'solucao_plat_topico_1' => 'Consolide múltiplas bandeiras',
			'solucao_plat_topico_2' => 'Centralize dados comerciais',
			'solucao_plat_topico_3' => 'Evite customizações complexas',
			'solucao_plat_imagem'   => $this->img( 'totvs-linx-plat' ),
			// 7 · Aceleradores.
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com varejo integrado',
			'solucao_acel_corpo'     => 'Utilize um modelo pronto para conectar Linx PDV ao ERP financeiro e programas de fidelidade.',
			'solucao_acel_topico_1'  => 'Conecte PDVs rapidamente',
			'solucao_acel_topico_2'  => 'Reutilize fluxos comerciais',
			'solucao_acel_topico_3'  => 'Acelere novas integrações',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'totvs-linx-acel' ),
		);
		foreach ( $campos as $chave => $valor ) {
			update_field( $chave, $valor, $post_id );
		}
		$faq_ids = $this->criar_faq_totvs_linx();
		if ( ! empty( $faq_ids ) ) {
			update_field( 'solucao_faq_itens', $faq_ids, $post_id );
			WP_CLI::log( sprintf( '  TOTVS Linx FAQ: %d perguntas vinculadas.', count( $faq_ids ) ) );
		}
		WP_CLI::log( '  TOTVS Linx: todas as seções preenchidas.' );
	}

	/**
	 * Cria (ou reutiliza) os FAQs do TOTVS Linx.
	 */
	protected function criar_faq_totvs_linx() {
		$itens = array(
			array(
				'faq:totvs-linx-bandeiras',
				'Como consolidar vendas de múltiplas bandeiras Linx?',
				'<p>A CLI Connect cria um hub central que agrega transações de diferentes verticais Linx — moda, farmácias, postos, conveniência — e consolida os dados em um único repositório financeiro. Cada bandeira mantém sua operação independente no PDV, mas os resultados são consolidados automaticamente no ERP corporativo. Isso elimina reconciliações manuais e garante visibilidade em tempo real da performance de toda a rede.</p>',
			),
			array(
				'faq:totvs-linx-fidelidade',
				'É possível integrar com programas de fidelidade de terceiros?',
				'<p>Sim. A CLI Connect conecta o Linx a qualquer plataforma de CRM ou fidelidade via API, sejam soluções proprietárias ou de terceiros. Os dados de compra registrados no PDV são enviados automaticamente para o programa de fidelidade, que processa pontos e benefícios e retorna as informações ao caixa em tempo real. A integração é configurável e reutilizável para cada novo parceiro de fidelidade.</p>',
			),
			array(
				'faq:totvs-linx-fiscal',
				'Como funciona a integração fiscal centralizada (SAT/NFC-e)?',
				'<p>A CLI Connect centraliza a emissão e o armazenamento de documentos fiscais gerados pelo Linx PDV — SAT, NF-e e NFC-e — em um repositório único integrado ao ERP. Qualquer documento emitido pelas lojas é automaticamente transmitido, validado e armazenado de forma estruturada, facilitando obrigações acessórias, auditorias e conciliação fiscal sem depender de processos manuais por loja.</p>',
			),
		);
		$ids = array();
		foreach ( $itens as [ $seed_key, $titulo, $conteudo ] ) {
			$existing = get_posts( array(
				'post_type'  => 'cli_faq',
				'meta_key'   => '_cliconnect_seed',
				'meta_value' => $seed_key,
				'fields'     => 'ids',
			) );
			if ( ! empty( $existing ) ) {
				$ids[] = $existing[0];
				continue;
			}
			$faq_id = wp_insert_post( array(
				'post_type'    => 'cli_faq',
				'post_title'   => $titulo,
				'post_content' => $conteudo,
				'post_status'  => 'publish',
			) );
			if ( $faq_id && ! is_wp_error( $faq_id ) ) {
				update_post_meta( $faq_id, '_cliconnect_seed', $seed_key );
				$ids[] = $faq_id;
			}
		}
		WP_CLI::log( sprintf( '  TOTVS Linx FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}

	/* =====================================================================
	   TOTVS RM
	   ===================================================================== */

	/**
	 * Preenche os campos ACF do post cli_solucao "TOTVS RM".
	 */
	protected function preencher_solucao_totvs_rm() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:totvs-rm',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "totvs-rm" não encontrado.' );
			return;
		}
		$post_id = $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu TOTVS RM',
			'solucao_hero_titulo'     => 'Conecte o TOTVS RM a todos os sistemas satélites',
			'solucao_hero_corpo'      => 'Integre RH, educação e backoffice com folha, ponto, portais e aplicações corporativas para automatizar jornadas completas.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/totvs-rm/',
			'solucao_hero_imagem'     => $this->img( 'totvs-rm-hero' ),
			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Amplie o potencial do TOTVS RM',
			'solucao_pilares_1_icone'  => $this->img( 'totvs-rm-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Conecte módulos RM',
			'solucao_pilares_1_desc'   => 'Integre RM Folha, RM Núcleo e RM Backoffice aos sistemas externos.',
			'solucao_pilares_2_icone'  => $this->img( 'totvs-rm-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Automatize jornadas completas',
			'solucao_pilares_2_desc'   => 'Orquestre ciclos de colaboradores e alunos entre diferentes plataformas.',
			'solucao_pilares_3_icone'  => $this->img( 'totvs-rm-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Use webservices nativos',
			'solucao_pilares_3_desc'   => 'Conecte aplicações utilizando os recursos oficiais do TOTVS RM.',
			// 3 · Casos de Uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize processos do TOTVS RM',
			'solucao_casos_1_icone'   => $this->img( 'totvs-rm-caso-1' ),
			'solucao_casos_1_titulo'  => 'Orquestre admissão e desligamento',
			'solucao_casos_1_desc'    => 'Conecte RM a AD, benefícios e LMS automaticamente.',
			'solucao_casos_2_icone'   => $this->img( 'totvs-rm-caso-2' ),
			'solucao_casos_2_titulo'  => 'Integre jornada acadêmica',
			'solucao_casos_2_desc'    => 'Sincronize RM Núcleo com portais e plataformas educacionais.',
			'solucao_casos_3_icone'   => $this->img( 'totvs-rm-caso-3' ),
			'solucao_casos_3_titulo'  => 'Conecte financeiro e bancos',
			'solucao_casos_3_desc'    => 'Automatize processos financeiros do backoffice com instituições bancárias.',
			'solucao_casos_4_icone'   => $this->img( 'totvs-rm-caso-4' ),
			'solucao_casos_4_titulo'  => 'Consolide dados para BI',
			'solucao_casos_4_desc'    => 'Unifique informações de RH e educação para análises estratégicas.',
			'solucao_casos_5_icone'   => $this->img( 'totvs-rm-caso-5' ),
			'solucao_casos_5_titulo'  => 'Conecte agentes de IA',
			'solucao_casos_5_desc'    => 'Disponibilize dados de RH para agentes administrativos sem expor o core do sistema.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',
			// 4 · Selos.
			'solucao_selos_eyebrow' => 'compliance & segurança',
			'solucao_selos_titulo'  => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'   => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
			// 5 · Diferencial Técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações seguras para TOTVS RM',
			'solucao_dif_corpo'    => 'Proteja dados de colaboradores e alunos com mascaramento de informações em trânsito e auditoria completa dos processos.',
			'solucao_dif_topico_1' => 'Proteja dados pessoais sensíveis',
			'solucao_dif_topico_2' => 'Audite todas as movimentações',
			'solucao_dif_topico_3' => 'Controle informações compartilhadas',
			'solucao_dif_imagem'   => $this->img( 'totvs-rm-dif' ),
			// 6 · Plataforma Única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Centralize jornadas de negócio',
			'solucao_plat_corpo'    => 'Substitua integrações pontuais entre RM e sistemas satélites por uma camada única de processos reutilizáveis.',
			'solucao_plat_topico_1' => 'Reutilize pipelines existentes',
			'solucao_plat_topico_2' => 'Conecte múltiplos sistemas',
			'solucao_plat_topico_3' => 'Simplifique arquiteturas complexas',
			'solucao_plat_imagem'   => $this->img( 'totvs-rm-plat' ),
			// 7 · Aceleradores.
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com RM conectado',
			'solucao_acel_corpo'     => 'Utilize um modelo pronto para integrar RM de RH e educação aos sistemas satélites da organização.',
			'solucao_acel_topico_1'  => 'Conecte sistemas rapidamente',
			'solucao_acel_topico_2'  => 'Reaproveite processos prontos',
			'solucao_acel_topico_3'  => 'Acelere novas automações',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'totvs-rm-acel' ),
		);
		foreach ( $campos as $chave => $valor ) {
			update_field( $chave, $valor, $post_id );
		}
		$faq_ids = $this->criar_faq_totvs_rm();
		if ( ! empty( $faq_ids ) ) {
			update_field( 'solucao_faq_itens', $faq_ids, $post_id );
		}
		WP_CLI::log( '  TOTVS RM: todas as seções preenchidas.' );
	}

	/**
	 * Cria (ou reutiliza) os FAQs do TOTVS RM.
	 */
	protected function criar_faq_totvs_rm() {
		$itens = array(
			array(
				'faq:totvs-rm-admissao',
				'Como orquestrar admissão e desligamento entre o RM e outros sistemas?',
				'<p>A CLI Connect cria um processo centralizado que dispara automaticamente quando uma admissão ou desligamento é registrado no RM Folha. O fluxo provisiona ou desativa o colaborador no Active Directory, notifica a plataforma de benefícios e sincroniza o perfil no LMS — tudo sem intervenção manual. Cada etapa é auditada e pode ser monitorada em tempo real pelo painel da plataforma.</p>',
			),
			array(
				'faq:totvs-rm-nucleo',
				'É possível conectar o RM Núcleo a um portal do aluno de terceiros?',
				'<p>Sim. A CLI Connect utiliza os webservices nativos do RM Núcleo para expor dados acadêmicos de forma segura a portais de terceiros. Matrícula, notas, frequência e histórico são sincronizados automaticamente, sem necessidade de exportações manuais ou integrações customizadas. O portal externo recebe sempre os dados atualizados diretamente do sistema de origem.</p>',
			),
			array(
				'faq:totvs-rm-dados-rh',
				'Como o RM protege dados sensíveis de RH?',
				'<p>A CLI Connect aplica mascaramento de dados em trânsito para informações sensíveis como CPF, salário e dados médicos dos colaboradores. Todas as movimentações são registradas em log de auditoria com identificação do usuário, timestamp e dados trafegados. O acesso é controlado por perfis de permissão, garantindo que cada sistema satélite receba apenas as informações necessárias para sua operação.</p>',
			),
		);
		$ids = array();
		foreach ( $itens as [ $seed_key, $titulo, $conteudo ] ) {
			$existing = get_posts( array(
				'post_type'  => 'cli_faq',
				'meta_key'   => '_cliconnect_seed',
				'meta_value' => $seed_key,
				'fields'     => 'ids',
			) );
			if ( ! empty( $existing ) ) {
				$ids[] = $existing[0];
				continue;
			}
			$faq_id = wp_insert_post( array(
				'post_type'    => 'cli_faq',
				'post_title'   => $titulo,
				'post_content' => $conteudo,
				'post_status'  => 'publish',
			) );
			if ( $faq_id && ! is_wp_error( $faq_id ) ) {
				update_post_meta( $faq_id, '_cliconnect_seed', $seed_key );
				$ids[] = $faq_id;
			}
		}
		WP_CLI::log( sprintf( '  TOTVS RM FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}

	/* =====================================================================
	   ARIUS ERP
	   ===================================================================== */

	/**
	 * Preenche os campos ACF do post cli_solucao "Arius ERP".
	 */
	protected function preencher_solucao_arius_erp() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:arius-erp',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "arius-erp" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu Arius',
			'solucao_hero_titulo'     => 'Sincronize o Arius ERP com todo o seu ecossistema de vendas',
			'solucao_hero_corpo'      => 'Integre gestão de lojas, PDV e retaguarda financeira para eliminar controles manuais e garantir visibilidade total sobre o seu varejo em tempo real.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/arius-erp/',
			'solucao_hero_imagem'     => $this->img( 'arius-erp-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'      => 'Escale sua operação industrial conectada',
			'solucao_pilares_1_icone'     => $this->img( 'arius-erp-pilar-1' ),
			'solucao_pilares_1_titulo'    => 'Conecte sistemas industriais',
			'solucao_pilares_1_desc'      => 'Integre o Arius ERP com MES e aplicações do chão de fábrica.',
			'solucao_pilares_2_icone'     => $this->img( 'arius-erp-pilar-2' ),
			'solucao_pilares_2_titulo'    => 'Automatize produção e gestão',
			'solucao_pilares_2_desc'      => 'Sincronize ordens de produção e dados operacionais automaticamente.',
			'solucao_pilares_3_icone'     => $this->img( 'arius-erp-pilar-3' ),
			'solucao_pilares_3_titulo'    => 'Reduza controles manuais',
			'solucao_pilares_3_desc'      => 'Substitua planilhas por processos conectados entre áreas.',

			// 3 · Casos de uso.
			'solucao_casos_titulo'        => 'Automatize processos industriais com Arius',
			'solucao_casos_1_icone'       => $this->img( 'arius-erp-caso-1' ),
			'solucao_casos_1_titulo'      => 'Conecte produção ao ERP',
			'solucao_casos_1_desc'        => 'Sincronize ordens de produção entre MES e Arius.',
			'solucao_casos_2_icone'       => $this->img( 'arius-erp-caso-2' ),
			'solucao_casos_2_titulo'      => 'Integre processos fiscais',
			'solucao_casos_2_desc'        => 'Automatize emissão fiscal e dados financeiros do ERP.',
			'solucao_casos_3_icone'       => $this->img( 'arius-erp-caso-3' ),
			'solucao_casos_3_titulo'      => 'Consolide estoques industriais',
			'solucao_casos_3_desc'        => 'Centralize informações de estoque entre múltiplas plantas.',
			'solucao_casos_4_icone'       => $this->img( 'arius-erp-caso-4' ),
			'solucao_casos_4_titulo'      => 'Conecte CRM ao Arius',
			'solucao_casos_4_desc'        => 'Integre pedidos comerciais ao planejamento industrial.',
			'solucao_casos_5_icone'       => $this->img( 'arius-erp-caso-5' ),
			'solucao_casos_5_titulo'      => 'Conecte agentes de IA',
			'solucao_casos_5_desc'        => 'Disponibilize dados industriais para agentes administrativos sem expor o core operacional.',
			'solucao_casos_cta_texto'   => 'Agende uma demonstração',
			'solucao_casos_cta_url'     => '/contato/',

			// 4 · Diferencial técnico.
			'solucao_dif_titulo'        => 'Integrações dedicadas para Arius ERP',
			'solucao_dif_corpo'         => 'Utilize conectores adaptados ao protocolo do Arius com implantação dentro do ambiente do cliente para maior controle operacional.',
			'solucao_dif_topico_1'      => 'Use conectores dedicados',
			'solucao_dif_topico_2'      => 'Implante no ambiente interno',
			'solucao_dif_topico_3'      => 'Controle integrações industriais',
			'solucao_dif_imagem'        => $this->img( 'arius-erp-dif' ),

			// 5 · Plataforma única.
			'solucao_plat_titulo'       => 'Conecte sua indústria em evolução',
			'solucao_plat_corpo'        => 'Centralize integrações entre Arius, MES, CRM e novos sistemas sem depender de desenvolvedores especializados no ERP.',
			'solucao_plat_topico_1'     => 'Reduza dependência técnica',
			'solucao_plat_topico_2'     => 'Centralize novos sistemas',
			'solucao_plat_topico_3'     => 'Escale processos industriais',
			'solucao_plat_imagem'       => $this->img( 'arius-erp-plat' ),

			// 6 · Aceleradores.
			'solucao_acel_titulo'       => 'Comece com Arius integrado',
			'solucao_acel_corpo'        => 'Utilize um modelo pronto para conectar Arius ERP ao MES e CRM com fluxos industriais estruturados.',
			'solucao_acel_topico_1'     => 'Conecte MES rapidamente',
			'solucao_acel_topico_2'     => 'Reutilize integrações industriais',
			'solucao_acel_topico_3'     => 'Acelere novos projetos',
			'solucao_acel_btn_texto'    => 'Começar agora',
			'solucao_acel_btn_url'      => '/contato/',
			'solucao_acel_imagem'       => $this->img( 'arius-erp-acel' ),
		);
		foreach ( $campos as $key => $value ) {
			update_field( $key, $value, $post_id );
		}
		$faq_ids = $this->criar_faq_arius_erp();
		if ( ! empty( $faq_ids ) ) {
			update_field( 'solucao_faq_itens', $faq_ids, $post_id );
		}
		WP_CLI::log( sprintf( '  Arius ERP FAQ: %d perguntas vinculadas.', count( $faq_ids ) ) );
		WP_CLI::log( '  Arius ERP: todas as seções preenchidas.' );
	}

	/**
	 * Cria (ou reutiliza) os FAQs do Arius ERP.
	 *
	 * @return int[]
	 */
	protected function criar_faq_arius_erp(): array {
		$items = array(
			array(
				'seed_key' => 'faq:arius-erp-mes',
				'titulo'   => 'Como conectar o Arius ao chão de fábrica (MES)?',
				'corpo'    => 'A CLI Connect oferece conectores nativos para integrar o Arius ERP com sistemas MES, sincronizando ordens de produção, consumo de materiais e status de linha em tempo real, sem customizações no ERP.',
			),
			array(
				'seed_key' => 'faq:arius-erp-crm-pedidos',
				'titulo'   => 'É possível integrar com CRM para automatizar pedidos de venda?',
				'corpo'    => 'Sim. A integração entre CRM e Arius ERP permite que pedidos gerados no CRM sejam automaticamente criados no ERP, eliminando redigitação e reduzindo o tempo de ciclo de venda.',
			),
			array(
				'seed_key' => 'faq:arius-erp-estoque-multiplanta',
				'titulo'   => 'Como funciona a consolidação de estoque multi-planta?',
				'corpo'    => 'A CLI Connect centraliza dados de estoque de múltiplas plantas do Arius ERP em uma visão única, com sincronização automática de movimentações e disponibilidade em tempo real.',
			),
		);
		$ids = array();
		foreach ( $items as $item ) {
			$existing = get_posts( array(
				'post_type'  => 'cli_faq',
				'meta_key'   => '_cliconnect_seed',
				'meta_value' => $item['seed_key'],
				'fields'     => 'ids',
			) );
			if ( ! empty( $existing ) ) {
				$faq_id = (int) $existing[0];
				wp_update_post( array(
					'ID'           => $faq_id,
					'post_title'   => $item['titulo'],
					'post_content' => $item['corpo'],
				) );
				$ids[] = $faq_id;
				continue;
			}
			$faq_id = wp_insert_post( array(
				'post_type'    => 'cli_faq',
				'post_title'   => $item['titulo'],
				'post_status'  => 'publish',
				'post_content' => $item['corpo'],
			) );
			if ( $faq_id && ! is_wp_error( $faq_id ) ) {
				update_post_meta( $faq_id, '_cliconnect_seed', $item['seed_key'] );
				$ids[] = $faq_id;
			}
		}
		return $ids;
	}

	/* =====================================================================
	   CISS PODER ERP
	   ===================================================================== */

	/**
	 * Preenche os campos ACF do post cli_solucao "CISS Poder ERP".
	 */
	protected function preencher_solucao_ciss_poder_erp() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:ciss-poder-erp',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "ciss-poder-erp" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu CISS',
			'solucao_hero_titulo'     => 'Conecte o CISSPoder a toda a operação do varejo',
			'solucao_hero_corpo'      => 'Integre compras, estoque, PDV, e-commerce, fornecedores e financeiro para manter toda a operação varejista sincronizada em tempo real.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/ciss-poder-erp/',
			'solucao_hero_imagem'     => $this->img( 'ciss-poder-erp-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'      => 'Integre toda a operação varejista',
			'solucao_pilares_1_icone'     => $this->img( 'ciss-poder-erp-pilar-1' ),
			'solucao_pilares_1_titulo'    => 'Sincronize vendas e estoque',
			'solucao_pilares_1_desc'      => 'Conecte PDV, e-commerce e marketplaces ao CISSPoder para manter vendas e estoques atualizados.',
			'solucao_pilares_2_icone'     => $this->img( 'ciss-poder-erp-pilar-2' ),
			'solucao_pilares_2_titulo'    => 'Conecte fornecedores',
			'solucao_pilares_2_desc'      => 'Automatize pedidos, notas e informações de fornecedores por EDI, reduzindo lançamentos manuais.',
			'solucao_pilares_3_icone'     => $this->img( 'ciss-poder-erp-pilar-3' ),
			'solucao_pilares_3_titulo'    => 'Integre compras e abastecimento',
			'solucao_pilares_3_desc'      => 'Leve dados de vendas e estoque para processos de compra e reposição mais eficientes.',

			// 3 · Casos de uso.
			'solucao_casos_titulo'        => 'Automatize processos do varejo',
			'solucao_casos_1_icone'       => $this->img( 'ciss-poder-erp-caso-1' ),
			'solucao_casos_1_titulo'      => 'Sincronize vendas do PDV',
			'solucao_casos_1_desc'        => 'Leve as vendas das lojas para o CISSPoder em tempo real e mantenha a operação atualizada.',
			'solucao_casos_2_icone'       => $this->img( 'ciss-poder-erp-caso-2' ),
			'solucao_casos_2_titulo'      => 'Conecte o e-commerce',
			'solucao_casos_2_desc'        => 'Integre pedidos e estoque entre o CISSPoder e os canais digitais de venda.',
			'solucao_casos_3_icone'       => $this->img( 'ciss-poder-erp-caso-3' ),
			'solucao_casos_3_titulo'      => 'Integre fornecedores via EDI',
			'solucao_casos_3_desc'        => 'Automatize o recebimento de pedidos e documentos enviados por fornecedores.',
			'solucao_casos_4_icone'       => $this->img( 'ciss-poder-erp-caso-4' ),
			'solucao_casos_4_titulo'      => 'Automatize a reposição',
			'solucao_casos_4_desc'        => 'Conecte vendas, estoque e abastecimento para acelerar pedidos de reposição.',
			'solucao_casos_5_icone'       => $this->img( 'ciss-poder-erp-caso-5' ),
			'solucao_casos_5_titulo'      => 'Conecte agentes de IA',
			'solucao_casos_5_desc'        => 'Disponibilize dados assistenciais para agentes administrativos sem expor o core clínico.',
			'solucao_casos_cta_texto'     => 'Agende uma demonstração',
			'solucao_casos_cta_url'       => '/contato/',

			// 5 · Diferencial Técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações para operações de alto volume',
			'solucao_dif_corpo'    => 'Conecte o CISSPoder aos sistemas que sustentam sua operação, mantendo dados de vendas, estoque e compras sincronizados mesmo em redes com múltiplas lojas.',
			'solucao_dif_topico_1' => 'Processe grandes volumes de transações',
			'solucao_dif_topico_2' => 'Sincronize dados em tempo real',
			'solucao_dif_topico_3' => 'Conecte múltiplas lojas e sistemas',
			'solucao_dif_imagem'   => $this->img( 'ciss-poder-erp-dif' ),

			// 6 · Plataforma Única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Uma operação de varejo conectada',
			'solucao_plat_corpo'    => 'O CISSPoder já centraliza a gestão do varejo. A CLI Connect amplia essa capacidade conectando o ERP aos sistemas que fazem parte da operação.',
			'solucao_plat_topico_1' => 'Conecte PDV e e-commerce',
			'solucao_plat_topico_2' => 'Integre fornecedores e WMS',
			'solucao_plat_topico_3' => 'Centralize dados entre lojas',
			'solucao_plat_imagem'   => $this->img( 'ciss-poder-erp-plat' ),

			// 7 · Aceleradores.
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com uma integração pronta',
			'solucao_acel_corpo'     => 'Use um modelo pronto para conectar o CISSPoder aos principais sistemas da operação varejista e acelerar a implantação.',
			'solucao_acel_topico_1'  => 'Conecte PDV e e-commerce',
			'solucao_acel_topico_2'  => 'Automatize integrações com fornecedores',
			'solucao_acel_topico_3'  => 'Reutilize fluxos entre lojas',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'ciss-poder-erp-acel' ),
		);
		foreach ( $campos as $key => $value ) {
			update_field( $key, $value, $post_id );
		}
		$faq_ids = $this->criar_faq_ciss_poder_erp();
		if ( ! empty( $faq_ids ) ) {
			update_field( 'solucao_faq_itens', $faq_ids, $post_id );
		}

		WP_CLI::log( '  CISS Poder ERP: todas as seções preenchidas.' );
	}

	/**
	 * Cria/atualiza os posts cli_faq para a solução CISS Poder ERP.
	 *
	 * @return int[]
	 */
	protected function criar_faq_ciss_poder_erp(): array {
		$items = array(
			array(
				'seed_key' => 'faq:ciss-poder-erp-pdv',
				'titulo'   => 'Como sincronizar vendas do PDV com o CISSPoder?',
				'corpo'    => 'A CLI Connect integra os pontos de venda ao CISSPoder em tempo real, enviando automaticamente as transações realizadas nas lojas para o ERP. Isso elimina exportações manuais e mantém estoque e financeiro sempre atualizados.',
			),
			array(
				'seed_key' => 'faq:ciss-poder-erp-ecommerce',
				'titulo'   => 'É possível integrar o CISSPoder com e-commerce e marketplaces?',
				'corpo'    => 'Sim. A integração conecta o CISSPoder a plataformas de e-commerce e marketplaces, sincronizando pedidos, estoque e informações de produto de forma automatizada, com visibilidade centralizada da operação digital.',
			),
			array(
				'seed_key' => 'faq:ciss-poder-erp-edi',
				'titulo'   => 'Como integrar o CISSPoder aos fornecedores via EDI?',
				'corpo'    => 'A CLI Connect implementa a troca de documentos eletrônicos por EDI entre o CISSPoder e os fornecedores, automatizando o recebimento de pedidos, notas fiscais e confirmações sem necessidade de digitação manual.',
			),
		);

		$ids = array();
		foreach ( $items as $item ) {
			$existing = get_posts( array(
				'post_type'  => 'cli_faq',
				'meta_key'   => '_cliconnect_seed',
				'meta_value' => $item['seed_key'],
				'fields'     => 'ids',
			) );
			if ( ! empty( $existing ) ) {
				$id = (int) $existing[0];
				wp_update_post( array(
					'ID'           => $id,
					'post_title'   => $item['titulo'],
					'post_content' => $item['corpo'],
				) );
				$ids[] = $id;
				continue;
			}
			$id = wp_insert_post( array(
				'post_type'    => 'cli_faq',
				'post_status'  => 'publish',
				'post_title'   => $item['titulo'],
				'post_content' => $item['corpo'],
			) );
			if ( is_wp_error( $id ) ) {
				continue;
			}
			update_post_meta( $id, '_cliconnect_seed', $item['seed_key'] );
			$ids[] = $id;
		}

		WP_CLI::log( sprintf( '  CISS Poder ERP FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}

	/* =====================================================================
	   IFS CLOUD
	   ===================================================================== */

	/**
	 * Preenche os campos ACF do post cli_solucao "IFS Cloud".
	 */
	protected function preencher_solucao_ifs_cloud() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:ifs-cloud',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "ifs-cloud" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu IFS Cloud',
			'solucao_hero_titulo'     => 'Conecte o IFS Cloud ao restante da operação industrial',
			'solucao_hero_corpo'      => 'Integre ERP, gestão de ativos e field service com MES, IoT e sistemas corporativos para transformar dados operacionais em decisões mais rápidas.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/ifs-cloud/',
			'solucao_hero_imagem'     => $this->img( 'ifs-cloud-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'      => 'Amplie o potencial do IFS Cloud',
			'solucao_pilares_1_icone'     => $this->img( 'ifs-cloud-pilar-1' ),
			'solucao_pilares_1_titulo'    => 'Use APIs nativas do IFS',
			'solucao_pilares_1_desc'      => 'Conecte sistemas utilizando a REST API oficial do IFS Cloud.',
			'solucao_pilares_2_icone'     => $this->img( 'ifs-cloud-pilar-2' ),
			'solucao_pilares_2_titulo'    => 'Conecte ativos industriais',
			'solucao_pilares_2_desc'      => 'Integre manutenção, sensores e dados operacionais em tempo real.',
			'solucao_pilares_3_icone'     => $this->img( 'ifs-cloud-pilar-3' ),
			'solucao_pilares_3_titulo'    => 'Escale field service',
			'solucao_pilares_3_desc'      => 'Conecte equipes externas, CRM e processos de atendimento.',

			// 3 · Casos de uso.
			'solucao_casos_titulo'        => 'Automatize processos com IFS Cloud',
			'solucao_casos_1_icone'       => $this->img( 'ifs-cloud-caso-1' ),
			'solucao_casos_1_titulo'      => 'Integre manutenção e IoT',
			'solucao_casos_1_desc'        => 'Conecte ordens EAM com sensores e dados industriais.',
			'solucao_casos_2_icone'       => $this->img( 'ifs-cloud-caso-2' ),
			'solucao_casos_2_titulo'      => 'Conecte field service ao CRM',
			'solucao_casos_2_desc'        => 'Sincronize atendimentos externos com processos comerciais.',
			'solucao_casos_3_icone'       => $this->img( 'ifs-cloud-caso-3' ),
			'solucao_casos_3_titulo'      => 'Consolide dados financeiros',
			'solucao_casos_3_desc'        => 'Integre IFS e ERP corporativo para visão financeira única.',
			'solucao_casos_4_icone'       => $this->img( 'ifs-cloud-caso-4' ),
			'solucao_casos_4_titulo'      => 'Exponha dados para IA',
			'solucao_casos_4_desc'        => 'Disponibilize ativos como ferramentas para agentes inteligentes.',
			'solucao_casos_5_icone'       => $this->img( 'ifs-cloud-caso-5' ),
			'solucao_casos_5_titulo'      => 'Conecte agentes de IA',
			'solucao_casos_5_desc'        => 'Disponibilize dados operacionais para agentes administrativos sem expor o core do IFS.',
			'solucao_casos_cta_texto'     => 'Agende uma demonstração',
			'solucao_casos_cta_url'       => '/contato/',

			// 5 · Diferencial Técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações seguras para IFS Cloud',
			'solucao_dif_corpo'    => 'Conecte aplicações corporativas utilizando autenticação OAuth2 pela REST API do IFS Cloud com segurança e controle.',
			'solucao_dif_topico_1' => 'Utilize OAuth2 seguro',
			'solucao_dif_topico_2' => 'Conecte APIs oficiais',
			'solucao_dif_topico_3' => 'Proteja dados industriais',
			'solucao_dif_imagem'   => $this->img( 'ifs-cloud-dif' ),

			// 6 · Plataforma Única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Centralize dados de ativos industriais',
			'solucao_plat_corpo'    => 'Conecte manutenção, ERP e inteligência artificial em uma única camada sem alterar o core do IFS Cloud.',
			'solucao_plat_topico_1' => 'Integre sistemas corporativos',
			'solucao_plat_topico_2' => 'Evite customizar o IFS',
			'solucao_plat_topico_3' => 'Escale operações industriais',
			'solucao_plat_imagem'   => $this->img( 'ifs-cloud-plat' ),

			// 7 · Aceleradores.
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com ativos conectados',
			'solucao_acel_corpo'     => 'Utilize um modelo pronto para conectar IFS EAM/FSM ao ERP corporativo e plataformas IoT.',
			'solucao_acel_topico_1'  => 'Conecte IoT rapidamente',
			'solucao_acel_topico_2'  => 'Reutilize fluxos industriais',
			'solucao_acel_topico_3'  => 'Acelere novos projetos',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'ifs-cloud-acel' ),
		);
		foreach ( $campos as $key => $value ) {
			update_field( $key, $value, $post_id );
		}
		$faq_ids = $this->criar_faq_ifs_cloud();
		if ( ! empty( $faq_ids ) ) {
			update_field( 'solucao_faq_itens', $faq_ids, $post_id );
		}

		WP_CLI::log( '  IFS Cloud: todas as seções preenchidas.' );
	}

	/**
	 * Cria/atualiza os posts cli_faq para a solução IFS Cloud.
	 *
	 * @return int[]
	 */
	protected function criar_faq_ifs_cloud(): array {
		$items = array(
			array(
				'seed_key' => 'faq:ifs-cloud-ia-preditiva',
				'titulo'   => 'Como conectar dados de manutenção do IFS a um agente de IA preditiva?',
				'corpo'    => 'A CLI Connect expõe os dados de EAM do IFS Cloud como ferramentas consumíveis por agentes de IA, permitindo que modelos preditivos acessem histórico de ordens, status de ativos e alertas de sensores IoT sem acoplar diretamente ao core do sistema.',
			),
			array(
				'seed_key' => 'faq:ifs-cloud-fsm-crm',
				'titulo'   => 'É possível integrar field service (FSM) com o CRM?',
				'corpo'    => 'Sim. A integração conecta o módulo FSM do IFS Cloud ao CRM da operação, sincronizando chamados, agendamentos e histórico de atendimento. Isso garante que as equipes comerciais e de suporte tenham visibilidade unificada do cliente sem duplicar registros.',
			),
			array(
				'seed_key' => 'faq:ifs-cloud-consolidacao-financeira',
				'titulo'   => 'Como funciona a consolidação financeira entre IFS e ERP corporativo?',
				'corpo'    => 'A CLI Connect coleta lançamentos, centros de custo e dados contábeis do IFS Cloud e os envia automaticamente ao ERP corporativo, eliminando exportações manuais. O processo é auditado e configurável por período de fechamento, garantindo consistência nos relatórios financeiros consolidados.',
			),
		);

		$ids = array();
		foreach ( $items as $item ) {
			$existing = get_posts( array(
				'post_type'  => 'cli_faq',
				'meta_key'   => '_cliconnect_seed',
				'meta_value' => $item['seed_key'],
				'fields'     => 'ids',
			) );
			if ( ! empty( $existing ) ) {
				$faq_id = (int) $existing[0];
				wp_update_post( array(
					'ID'           => $faq_id,
					'post_title'   => $item['titulo'],
					'post_content' => $item['corpo'],
				) );
				$ids[] = $faq_id;
				continue;
			}
			$faq_id = wp_insert_post( array(
				'post_type'    => 'cli_faq',
				'post_title'   => $item['titulo'],
				'post_status'  => 'publish',
				'post_content' => $item['corpo'],
			) );
			if ( $faq_id && ! is_wp_error( $faq_id ) ) {
				update_post_meta( $faq_id, '_cliconnect_seed', $item['seed_key'] );
				$ids[] = $faq_id;
			}
		}

		WP_CLI::log( sprintf( '  IFS Cloud FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}

	/* =====================================================================
	   VIASOFT
	   ===================================================================== */

	/**
	 * Preenche os campos ACF do post cli_solucao "Viasoft".
	 */
	protected function preencher_solucao_viasoft() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:viasoft',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "viasoft" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu Viasoft',
			'solucao_hero_titulo'     => 'Conecte o Viasoft ao restante da operação',
			'solucao_hero_corpo'      => 'Integre ERPs especializados em agro, combustíveis e indústria com bancos, fiscal e sistemas comerciais para unificar processos.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/viasoft/',
			'solucao_hero_imagem'     => $this->img( 'viasoft-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'      => 'Amplie o potencial do Viasoft',
			'solucao_pilares_1_icone'     => $this->img( 'viasoft-pilar-1' ),
			'solucao_pilares_1_titulo'    => 'Conecte verticais especializadas',
			'solucao_pilares_1_desc'      => 'Integre Agrotitan, Filt IA+ e soluções Viasoft por segmento.',
			'solucao_pilares_2_icone'     => $this->img( 'viasoft-pilar-2' ),
			'solucao_pilares_2_titulo'    => 'Automatize processos fiscais',
			'solucao_pilares_2_desc'      => 'Conecte obrigações fiscais conforme cada vertical de negócio.',
			'solucao_pilares_3_icone'     => $this->img( 'viasoft-pilar-3' ),
			'solucao_pilares_3_titulo'    => 'Integre operações financeiras',
			'solucao_pilares_3_desc'      => 'Sincronize bancos e processos financeiros automaticamente.',

			// 3 · Casos de uso.
			'solucao_casos_titulo'        => 'Automatize processos do Viasoft',
			'solucao_casos_1_icone'       => $this->img( 'viasoft-caso-1' ),
			'solucao_casos_1_titulo'      => 'Integre vendas ao financeiro',
			'solucao_casos_1_desc'        => 'Sincronize vendas agrícolas e operações comerciais com financeiro.',
			'solucao_casos_2_icone'       => $this->img( 'viasoft-caso-2' ),
			'solucao_casos_2_titulo'      => 'Automatize processos fiscais',
			'solucao_casos_2_desc'        => 'Conecte NF-e e SPED conforme cada segmento.',
			'solucao_casos_3_icone'       => $this->img( 'viasoft-caso-3' ),
			'solucao_casos_3_titulo'      => 'Concilie operações bancárias',
			'solucao_casos_3_desc'        => 'Automatize conciliações de revendas e cooperativas.',
			'solucao_casos_4_icone'       => $this->img( 'viasoft-caso-4' ),
			'solucao_casos_4_titulo'      => 'Consolide dados operacionais',
			'solucao_casos_4_desc'        => 'Unifique informações multi-filial para análises estratégicas.',
			'solucao_casos_5_icone'       => $this->img( 'viasoft-caso-5' ),
			'solucao_casos_5_titulo'      => 'Conecte agentes de IA',
			'solucao_casos_5_desc'        => 'Disponibilize dados operacionais para agentes inteligentes sem expor o core do Viasoft.',
			'solucao_casos_cta_texto'     => 'Agende uma demonstração',
			'solucao_casos_cta_url'       => '/contato/',

			// 5 · Diferencial Técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações adaptadas ao seu segmento',
			'solucao_dif_corpo'    => 'Conecte operações com regras fiscais e regulatórias específicas para agro, combustíveis e demais verticais atendidas pelo Viasoft.',
			'solucao_dif_topico_1' => 'Adapte integrações por vertical',
			'solucao_dif_topico_2' => 'Atenda regras regulatórias específicas',
			'solucao_dif_topico_3' => 'Conecte operações especializadas',
			'solucao_dif_imagem'   => $this->img( 'viasoft-dif' ),

			// 6 · Plataforma Única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Unifique diferentes verticais Viasoft',
			'solucao_plat_corpo'    => 'Centralize dados financeiros e fiscais de diferentes operações em uma única camada de integração.',
			'solucao_plat_topico_1' => 'Integre múltiplos negócios',
			'solucao_plat_topico_2' => 'Centralize informações corporativas',
			'solucao_plat_topico_3' => 'Evite integrações isoladas',
			'solucao_plat_imagem'   => $this->img( 'viasoft-plat' ),

			// 7 · Aceleradores.
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com operações conectadas',
			'solucao_acel_corpo'     => 'Utilize um modelo pronto para conectar Viasoft ao financeiro e fiscal com rapidez.',
			'solucao_acel_topico_1'  => 'Conecte dados rapidamente',
			'solucao_acel_topico_2'  => 'Reutilize fluxos validados',
			'solucao_acel_topico_3'  => 'Acelere novas integrações',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'viasoft-acel' ),
		);
		foreach ( $campos as $key => $value ) {
			update_field( $key, $value, $post_id );
		}
		$faq_ids = $this->criar_faq_viasoft();
		if ( ! empty( $faq_ids ) ) {
			update_field( 'solucao_faq_itens', $faq_ids, $post_id );
		}

		WP_CLI::log( '  Viasoft: todas as seções preenchidas.' );
	}

	/**
	 * Cria/atualiza os posts cli_faq para a solução Viasoft.
	 *
	 * @return int[]
	 */
	protected function criar_faq_viasoft(): array {
		$items = array(
			array(
				'seed_key' => 'faq:viasoft-consolidacao-financeira',
				'titulo'   => 'Como consolidar dados financeiros entre diferentes verticais Viasoft?',
				'corpo'    => 'A CLI Connect centraliza dados financeiros e fiscais de múltiplas verticais do Viasoft — agro, combustíveis, indústria — em uma única camada de integração conectada ao BI corporativo. Isso permite análises consolidadas por negócio sem depender de exportações manuais por sistema.',
			),
			array(
				'seed_key' => 'faq:viasoft-defensivos-agricolas',
				'titulo'   => 'É possível integrar controle de validade de defensivos agrícolas ao ERP?',
				'corpo'    => 'Sim. A CLI Connect integra os dados de controle de defensivos agrícolas do Agrotitan ao Viasoft ERP, sincronizando validade, movimentação de estoque e alertas regulatórios automaticamente. Isso garante conformidade com as exigências do setor sem processos manuais.',
			),
			array(
				'seed_key' => 'faq:viasoft-integracao-fiscal',
				'titulo'   => 'Como funciona a integração fiscal por vertical?',
				'corpo'    => 'A CLI Connect adapta os fluxos de integração fiscal conforme as regras específicas de cada vertical do Viasoft. Para agro, combustíveis e indústria, os processos de NF-e, SPED e obrigações acessórias são conectados automaticamente ao ERP, respeitando as particularidades tributárias de cada segmento.',
			),
		);

		$ids = array();
		foreach ( $items as $item ) {
			$existing = get_posts( array(
				'post_type'  => 'cli_faq',
				'meta_key'   => '_cliconnect_seed',
				'meta_value' => $item['seed_key'],
				'fields'     => 'ids',
			) );
			if ( ! empty( $existing ) ) {
				$faq_id = (int) $existing[0];
				wp_update_post( array(
					'ID'           => $faq_id,
					'post_title'   => $item['titulo'],
					'post_content' => $item['corpo'],
				) );
				$ids[] = $faq_id;
				continue;
			}
			$faq_id = wp_insert_post( array(
				'post_type'    => 'cli_faq',
				'post_title'   => $item['titulo'],
				'post_status'  => 'publish',
				'post_content' => $item['corpo'],
			) );
			if ( $faq_id && ! is_wp_error( $faq_id ) ) {
				update_post_meta( $faq_id, '_cliconnect_seed', $item['seed_key'] );
				$ids[] = $faq_id;
			}
		}

		WP_CLI::log( sprintf( '  Viasoft FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}

	/* =====================================================================
	   RP INFO
	   ===================================================================== */

	/**
	 * Preenche os campos ACF do post cli_solucao "RP Info".
	 */
	protected function preencher_solucao_rp_info() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:rp-info',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "rp-info" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu RP Info',
			'solucao_hero_titulo'     => 'Conecte o RP Info do checkout ao centro de distribuição',
			'solucao_hero_corpo'      => 'Integre frente de loja, ERP, fornecedores e BI para sincronizar vendas, estoque e operações do varejo em tempo real.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/rp-info/',
			'solucao_hero_imagem'     => $this->img( 'rp-info-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'      => 'Escale seu varejo conectado',
			'solucao_pilares_1_icone'     => $this->img( 'rp-info-pilar-1' ),
			'solucao_pilares_1_titulo'    => 'Conecte operações de varejo',
			'solucao_pilares_1_desc'      => 'Integre Flex ERP, RPDV, Mix, Target e Task ao ecossistema comercial.',
			'solucao_pilares_2_icone'     => $this->img( 'rp-info-pilar-2' ),
			'solucao_pilares_2_titulo'    => 'Sincronize vendas em tempo real',
			'solucao_pilares_2_desc'      => 'Conecte transações do checkout ao ERP sem processos manuais.',
			'solucao_pilares_3_icone'     => $this->img( 'rp-info-pilar-3' ),
			'solucao_pilares_3_titulo'    => 'Integre fornecedores via EDI',
			'solucao_pilares_3_desc'      => 'Automatize troca de dados com parceiros comerciais.',

			// 3 · Casos de uso.
			'solucao_casos_titulo'        => 'Automatize processos do varejo RP Info',
			'solucao_casos_1_icone'       => $this->img( 'rp-info-caso-1' ),
			'solucao_casos_1_titulo'      => 'Sincronize vendas do PDV',
			'solucao_casos_1_desc'        => 'Atualize vendas do RPDV no Flex ERP em tempo real.',
			'solucao_casos_2_icone'       => $this->img( 'rp-info-caso-2' ),
			'solucao_casos_2_titulo'      => 'Conecte fornecedores via EDI',
			'solucao_casos_2_desc'        => 'Automatize pedidos e informações com parceiros comerciais.',
			'solucao_casos_3_icone'       => $this->img( 'rp-info-caso-3' ),
			'solucao_casos_3_titulo'      => 'Consolide vendas multi-loja',
			'solucao_casos_3_desc'        => 'Centralize resultados de diferentes unidades para análise.',
			'solucao_casos_4_icone'       => $this->img( 'rp-info-caso-4' ),
			'solucao_casos_4_titulo'      => 'Centralize processos fiscais',
			'solucao_casos_4_desc'        => 'Integre SPED e NF-e aos processos corporativos.',
			'solucao_casos_5_icone'       => $this->img( 'rp-info-caso-5' ),
			'solucao_casos_5_titulo'      => 'Conecte agentes de IA',
			'solucao_casos_5_desc'        => 'Disponibilize dados operacionais para agentes inteligentes sem expor o core do RP Info.',
			'solucao_casos_cta_texto'     => 'Agende uma demonstração',
			'solucao_casos_cta_url'       => '/contato/',

			// 5 · Diferencial Técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações para varejo em escala',
			'solucao_dif_corpo'    => 'Conecte operações com milhares de checkouts utilizando uma arquitetura preparada para alto volume transacional.',
			'solucao_dif_topico_1' => 'Processe vendas em escala',
			'solucao_dif_topico_2' => 'Sincronize dados rapidamente',
			'solucao_dif_topico_3' => 'Suporte múltiplos checkouts',
			'solucao_dif_imagem'   => $this->img( 'rp-info-dif' ),

			// 6 · Plataforma Única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Unifique dados do varejo',
			'solucao_plat_corpo'    => 'Centralize vendas, estoque e fornecedores em uma única camada de integração sem depender de processos batch.',
			'solucao_plat_topico_1' => 'Consolide vendas em tempo real',
			'solucao_plat_topico_2' => 'Centralize dados operacionais',
			'solucao_plat_topico_3' => 'Reduza processos manuais',
			'solucao_plat_imagem'   => $this->img( 'rp-info-plat' ),

			// 7 · Aceleradores.
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com varejo integrado',
			'solucao_acel_corpo'     => 'Utilize um modelo pronto para conectar RP Info ao EDI de fornecedores e plataformas analíticas.',
			'solucao_acel_topico_1'  => 'Conecte fornecedores rapidamente',
			'solucao_acel_topico_2'  => 'Reutilize fluxos de varejo',
			'solucao_acel_topico_3'  => 'Acelere novas integrações',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'rp-info-acel' ),
		);
		foreach ( $campos as $key => $value ) {
			update_field( $key, $value, $post_id );
		}
		$faq_ids = $this->criar_faq_rp_info();
		if ( ! empty( $faq_ids ) ) {
			update_field( 'solucao_faq_itens', $faq_ids, $post_id );
		}

		WP_CLI::log( '  RP Info: todas as seções preenchidas.' );
	}

	/**
	 * Cria/atualiza os posts cli_faq para a solução RP Info.
	 *
	 * @return int[]
	 */
	protected function criar_faq_rp_info(): array {
		$items = array(
			array(
				'seed_key' => 'faq:rp-info-pdv',
				'titulo'   => 'Como sincronizar vendas de frente de loja em tempo real?',
				'corpo'    => 'A CLI Connect integra o RPDV da RP Info ao Flex ERP, enviando automaticamente cada transação realizada no checkout em tempo real. Isso elimina fechamentos manuais e garante que estoque e faturamento estejam sempre atualizados sem depender de sincronizações periódicas.',
			),
			array(
				'seed_key' => 'faq:rp-info-edi',
				'titulo'   => 'É possível integrar com múltiplos fornecedores via EDI?',
				'corpo'    => 'Sim. A CLI Connect implementa o protocolo EDI para troca de pedidos, notas fiscais e confirmações de entrega entre o RP Info e fornecedores de diferentes formatos e padrões. A integração é configurável por parceiro e permite onboarding de novos fornecedores sem alterar o core do sistema.',
			),
			array(
				'seed_key' => 'faq:rp-info-multiloja',
				'titulo'   => 'Como funciona a consolidação de vendas multi-loja?',
				'corpo'    => 'A CLI Connect agrega dados de vendas de múltiplas lojas que utilizam o RP Info e os consolida em um repositório único conectado ao BI corporativo. Gestores têm visibilidade unificada de desempenho por loja, região e período, sem depender de exportações manuais de cada unidade.',
			),
		);

		$ids = array();
		foreach ( $items as $item ) {
			$existing = get_posts( array(
				'post_type'  => 'cli_faq',
				'meta_key'   => '_cliconnect_seed',
				'meta_value' => $item['seed_key'],
				'fields'     => 'ids',
			) );
			if ( ! empty( $existing ) ) {
				$faq_id = (int) $existing[0];
				wp_update_post( array(
					'ID'           => $faq_id,
					'post_title'   => $item['titulo'],
					'post_content' => $item['corpo'],
				) );
				$ids[] = $faq_id;
				continue;
			}
			$faq_id = wp_insert_post( array(
				'post_type'    => 'cli_faq',
				'post_title'   => $item['titulo'],
				'post_status'  => 'publish',
				'post_content' => $item['corpo'],
			) );
			if ( $faq_id && ! is_wp_error( $faq_id ) ) {
				update_post_meta( $faq_id, '_cliconnect_seed', $item['seed_key'] );
				$ids[] = $faq_id;
			}
		}

		WP_CLI::log( sprintf( '  RP Info FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}

	/* =====================================================================
	   QAD REDZONE
	   ===================================================================== */

	/**
	 * Preenche os campos ACF do post cli_solucao "QAD Redzone".
	 */
	protected function preencher_solucao_qad_redzone() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:qad-redzone',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "qad-redzone" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu QAD Redzone',
			'solucao_hero_titulo'     => 'Conecte o QAD Redzone ao ERP e ao chão de fábrica em tempo real',
			'solucao_hero_corpo'      => 'Integre produtividade de linha, manufatura e qualidade ao QAD ERP e BI corporativo para transformar dados operacionais em decisões rápidas.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/qad-redzone/',
			'solucao_hero_imagem'     => $this->img( 'qad-redzone-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'      => 'Transforme dados da fábrica em valor',
			'solucao_pilares_1_icone'     => $this->img( 'qad-redzone-pilar-1' ),
			'solucao_pilares_1_titulo'    => 'Monitore produtividade em tempo real',
			'solucao_pilares_1_desc'      => 'Sincronize dados de OEE e desempenho das linhas automaticamente.',
			'solucao_pilares_2_icone'     => $this->img( 'qad-redzone-pilar-2' ),
			'solucao_pilares_2_titulo'    => 'Integre com QAD ERP',
			'solucao_pilares_2_desc'      => 'Conecte execução industrial aos processos corporativos do ERP.',
			'solucao_pilares_3_icone'     => $this->img( 'qad-redzone-pilar-3' ),
			'solucao_pilares_3_titulo'    => 'Conecte fábrica e BI',
			'solucao_pilares_3_desc'      => 'Leve dados operacionais para análises estratégicas corporativas.',

			// 3 · Casos de uso.
			'solucao_casos_titulo'        => 'Automatize processos de manufatura',
			'solucao_casos_1_icone'       => $this->img( 'qad-redzone-caso-1' ),
			'solucao_casos_1_titulo'      => 'Integre OEE ao ERP',
			'solucao_casos_1_desc'        => 'Envie indicadores de produtividade do Redzone ao QAD ERP.',
			'solucao_casos_2_icone'       => $this->img( 'qad-redzone-caso-2' ),
			'solucao_casos_2_titulo'      => 'Controle qualidade integrada',
			'solucao_casos_2_desc'        => 'Conecte não conformidades aos processos de qualidade.',
			'solucao_casos_3_icone'       => $this->img( 'qad-redzone-caso-3' ),
			'solucao_casos_3_titulo'      => 'Consolide produção multi-planta',
			'solucao_casos_3_desc'        => 'Centralize dados industriais de diferentes unidades produtivas.',
			'solucao_casos_4_icone'       => $this->img( 'qad-redzone-caso-4' ),
			'solucao_casos_4_titulo'      => 'Alerte paradas de linha',
			'solucao_casos_4_desc'        => 'Dispare alertas em tempo real para manutenção preventiva.',
			'solucao_casos_5_icone'       => $this->img( 'qad-redzone-caso-5' ),
			'solucao_casos_5_titulo'      => 'Conecte agentes de IA',
			'solucao_casos_5_desc'        => 'Disponibilize dados operacionais para agentes inteligentes sem expor o core do Redzone.',
			'solucao_casos_cta_texto'     => 'Agende uma demonstração',
			'solucao_casos_cta_url'       => '/contato/',

			// 5 · Diferencial Técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações para manufatura em tempo real',
			'solucao_dif_corpo'    => 'Processe grandes volumes de dados industriais com conectividade preparada para sensores e operações contínuas de produção.',
			'solucao_dif_topico_1' => 'Processe dados em alto volume',
			'solucao_dif_topico_2' => 'Conecte eventos industriais',
			'solucao_dif_topico_3' => 'Acompanhe produção em tempo real',
			'solucao_dif_imagem'   => $this->img( 'qad-redzone-dif' ),

			// 6 · Plataforma Única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Conecte toda sua operação industrial',
			'solucao_plat_corpo'    => 'Centralize dados do chão de fábrica, ERP e BI para eliminar informações isoladas e ampliar o valor do Redzone.',
			'solucao_plat_topico_1' => 'Integre fábrica e escritório',
			'solucao_plat_topico_2' => 'Centralize dados produtivos',
			'solucao_plat_topico_3' => 'Amplie visibilidade operacional',
			'solucao_plat_imagem'   => $this->img( 'qad-redzone-plat' ),

			// 7 · Aceleradores.
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com manufatura conectada',
			'solucao_acel_corpo'     => 'Utilize um modelo pronto para conectar QAD Redzone ao QAD ERP e plataformas analíticas corporativas.',
			'solucao_acel_topico_1'  => 'Conecte dados rapidamente',
			'solucao_acel_topico_2'  => 'Reutilize padrões industriais',
			'solucao_acel_topico_3'  => 'Acelere projetos fabris',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'qad-redzone-acel' ),
		);
		foreach ( $campos as $key => $value ) {
			update_field( $key, $value, $post_id );
		}
		$faq_ids = $this->criar_faq_qad_redzone();
		if ( ! empty( $faq_ids ) ) {
			update_field( 'solucao_faq_itens', $faq_ids, $post_id );
		}

		WP_CLI::log( '  QAD Redzone: todas as seções preenchidas.' );
	}

	/**
	 * Cria/atualiza os posts cli_faq para a solução QAD Redzone.
	 *
	 * @return int[]
	 */
	protected function criar_faq_qad_redzone(): array {
		$items = array(
			array(
				'seed_key' => 'faq:qad-redzone-oee',
				'titulo'   => 'Como levar dados de OEE do Redzone para o QAD ERP?',
				'corpo'    => 'A CLI Connect captura os indicadores de OEE gerados pelo QAD Redzone em tempo real e os envia automaticamente ao QAD ERP, permitindo que gestores acompanhem desempenho de linha diretamente nos relatórios corporativos, sem exportações manuais.',
			),
			array(
				'seed_key' => 'faq:qad-redzone-alertas',
				'titulo'   => 'É possível gerar alertas de parada de linha em tempo real?',
				'corpo'    => 'Sim. A integração monitora os eventos de parada registrados no Redzone e aciona automaticamente notificações para equipes de manutenção, qualidade ou operações. Os alertas podem ser enviados por e-mail, mensagem ou integrados a sistemas de gestão de manutenção.',
			),
			array(
				'seed_key' => 'faq:qad-redzone-multiplanta',
				'titulo'   => 'Como funciona a consolidação multi-planta?',
				'corpo'    => 'A CLI Connect agrega dados de produtividade e qualidade de múltiplas plantas que utilizam o QAD Redzone em um repositório centralizado, conectado ao ERP e ao BI corporativo. Isso garante visibilidade unificada da operação industrial sem depender de consolidações manuais por planta.',
			),
		);

		$ids = array();
		foreach ( $items as $item ) {
			$existing = get_posts( array(
				'post_type'  => 'cli_faq',
				'meta_key'   => '_cliconnect_seed',
				'meta_value' => $item['seed_key'],
				'fields'     => 'ids',
			) );
			if ( ! empty( $existing ) ) {
				$faq_id = (int) $existing[0];
				wp_update_post( array(
					'ID'           => $faq_id,
					'post_title'   => $item['titulo'],
					'post_content' => $item['corpo'],
				) );
				$ids[] = $faq_id;
				continue;
			}
			$faq_id = wp_insert_post( array(
				'post_type'    => 'cli_faq',
				'post_title'   => $item['titulo'],
				'post_status'  => 'publish',
				'post_content' => $item['corpo'],
			) );
			if ( $faq_id && ! is_wp_error( $faq_id ) ) {
				update_post_meta( $faq_id, '_cliconnect_seed', $item['seed_key'] );
				$ids[] = $faq_id;
			}
		}

		WP_CLI::log( sprintf( '  QAD Redzone FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}

	/* =====================================================================
	   MAGENTO / ADOBE COMMERCE
	   ===================================================================== */

	/**
	 * Preenche os campos ACF do post cli_solucao "Magento / Adobe Commerce".
	 */
	protected function preencher_solucao_magento() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:magento',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "magento" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu Magento',
			'solucao_hero_titulo'     => 'Conecte Magento ao seu stack corporativo sem multiplicar extensões',
			'solucao_hero_corpo'      => 'Integre Magento e Adobe Commerce ao ERP, PIM e sistemas de pagamento para escalar sua operação digital sem sobrecarregar o core da plataforma com customizações.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/magento/',
			'solucao_hero_imagem'     => $this->img( 'magento-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Escale seu comércio digital conectado',
			'solucao_pilares_1_icone'  => $this->img( 'magento-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Integre qualquer arquitetura Magento',
			'solucao_pilares_1_desc'   => 'Conecte ambientes on-premises e Adobe Commerce Cloud com uma camada centralizada.',
			'solucao_pilares_2_icone'  => $this->img( 'magento-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Sincronize catálogo e preços',
			'solucao_pilares_2_desc'   => 'Mantenha produtos e informações comerciais atualizados a partir do PIM.',
			'solucao_pilares_3_icone'  => $this->img( 'magento-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Conecte pagamentos brasileiros',
			'solucao_pilares_3_desc'   => 'Integre múltiplos gateways de pagamento ao checkout da operação digital.',

			// 3 · Casos de uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize operações de comércio digital',
			'solucao_casos_1_icone'   => $this->img( 'magento-caso-1' ),
			'solucao_casos_1_titulo'  => 'Sincronize pedidos com ERP',
			'solucao_casos_1_desc'    => 'Envie pedidos Magento ao ERP automaticamente para acelerar processamento.',
			'solucao_casos_2_icone'   => $this->img( 'magento-caso-2' ),
			'solucao_casos_2_titulo'  => 'Centralize catálogo via PIM',
			'solucao_casos_2_desc'    => 'Atualize produtos e preços no Magento a partir de uma fonte única.',
			'solucao_casos_3_icone'   => $this->img( 'magento-caso-3' ),
			'solucao_casos_3_titulo'  => 'Concilie pagamentos automaticamente',
			'solucao_casos_3_desc'    => 'Integre gateways e antifraude ao financeiro para reduzir divergências.',
			'solucao_casos_4_icone'   => $this->img( 'magento-caso-4' ),
			'solucao_casos_4_titulo'  => 'Automatize devoluções',
			'solucao_casos_4_desc'    => 'Conecte processos de retorno e logística reversa aos sistemas internos.',
			'solucao_casos_5_icone'   => $this->img( 'magento-caso-5' ),
			'solucao_casos_5_titulo'  => 'Conecte agentes de IA',
			'solucao_casos_5_desc'    => 'Disponibilize dados do e-commerce para agentes automatizarem atendimento e operações.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',

			// 4 · Selos.
			'solucao_selos_eyebrow'   => 'compliance & segurança',
			'solucao_selos_titulo'    => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'     => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',

			// 5 · Diferencial Técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações seguras para Magento',
			'solucao_dif_corpo'    => 'Conecte Magento usando REST e GraphQL API com tokens de integração por escopo para proteger cada acesso.',
			'solucao_dif_topico_1' => 'Utilize REST e GraphQL API',
			'solucao_dif_topico_2' => 'Controle acessos por escopo',
			'solucao_dif_topico_3' => 'Proteja integrações corporativas',
			'solucao_dif_imagem'   => $this->img( 'magento-dif' ),

			// 6 · Plataforma Única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Evolua sem depender de extensões',
			'solucao_plat_corpo'    => 'Uma camada externa de integração reduz customizações no Magento, facilita upgrades e conecta sistemas corporativos com mais flexibilidade.',
			'solucao_plat_topico_1' => 'Centralize integrações externas',
			'solucao_plat_topico_2' => 'Reduza alterações no core',
			'solucao_plat_topico_3' => 'Simplifique atualizações futuras',
			'solucao_plat_imagem'   => $this->img( 'magento-plat' ),

			// 7 · Aceleradores.
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com comércio integrado',
			'solucao_acel_corpo'     => 'Utilize um modelo pronto para conectar Magento ou Adobe Commerce ao ERP e PIM com fluxos estruturados.',
			'solucao_acel_topico_1'  => 'Conecte sistemas rapidamente',
			'solucao_acel_topico_2'  => 'Reutilize integrações comerciais',
			'solucao_acel_topico_3'  => 'Acelere novos projetos',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'magento-acel' ),
		);
		foreach ( $campos as $chave => $valor ) {
			update_field( $chave, $valor, $post_id );
		}

		// 8 · FAQ.
		$faq_ids = $this->criar_faq_magento( $post_id );
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $faq_ids, $post_id );

		WP_CLI::log( '  Magento: todas as seções preenchidas.' );
	}

	/**
	 * Cria/atualiza os posts cli_faq para a solução Magento.
	 *
	 * @return int[]
	 */
	protected function criar_faq_magento( $post_id ) {
		$itens = array(
			array(
				'faq:magento-extensoes',
				'Como reduzir a dependência de extensões customizadas no Magento?',
				'<p>A CLI Connect cria uma camada de integração externa ao Magento, transferindo lógicas de negócio — como sincronização de pedidos, catálogo e pagamentos — para a plataforma Boomi. Isso reduz o número de extensões instaladas, simplifica atualizações de versão e mantém o core do Magento estável e performático.</p>',
			),
			array(
				'faq:magento-pim',
				'É possível centralizar o catálogo via PIM?',
				'<p>Sim. A integração conecta o PIM ao Magento para sincronizar produtos, descrições, preços e atributos de forma automatizada. Quando uma atualização é feita no PIM, ela se propaga ao Magento sem necessidade de importações manuais, garantindo consistência do catálogo em todos os canais.</p>',
			),
			array(
				'faq:magento-pagamentos',
				'Como funciona a conciliação de pagamentos e antifraude?',
				'<p>A integração conecta os gateways de pagamento e sistemas antifraude ao financeiro da empresa, automatizando a conciliação de transações realizadas no Magento. Divergências são identificadas e tratadas de forma centralizada, reduzindo erros manuais e acelerando o fechamento financeiro.</p>',
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

		WP_CLI::log( sprintf( '  Magento FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}
}

WP_CLI::add_command( 'cliconnect seed', 'Cliconnect_Seed' );
