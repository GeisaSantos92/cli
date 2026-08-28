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

	use Cliconnect_Seed_I18n;
	use Cliconnect_Seed_En_Paginas;
	use Cliconnect_Seed_En_Cpts;
	use Cliconnect_Seed_En_Faq;
	use Cliconnect_Seed_En_Solucoes;
	use Cliconnect_Seed_Es_Paginas;
	use Cliconnect_Seed_Es_Cpts;
	use Cliconnect_Seed_Es_Solucoes;
	use Cliconnect_Seed_Es_Solucoes_Erp;
	use Cliconnect_Seed_Es_Solucoes_Plataformas;
	use Cliconnect_Seed_Es_Solucoes_Ia_Nuvem;
	use Cliconnect_Seed_Es_Solucoes_Negocio;
	use Cliconnect_Seed_Es_Faq;

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
	 * [--traducao]
	 * : Roda só as camadas traduzidas, sem tocar no conteúdo em português.
	 *
	 * @param array $args       Argumentos posicionais.
	 * @param array $assoc_args Flags.
	 * @return void
	 */
	public function __invoke( $args, $assoc_args ) {
		$reset = ! empty( $assoc_args['reset'] );

		if ( ! empty( $assoc_args['traducao'] ) ) {
			$this->traduzir_site(
				$this->ids_das_paginas(),
				$this->ids_dos_termos_solucao()
			);

			flush_rewrite_rules();
			WP_CLI::success( 'Camadas traduzidas atualizadas.' );

			return;
		}

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

		WP_CLI::log( '— Preenchendo HubSpot CRM…' );
		$this->preencher_solucao_hubspot_crm();
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

		WP_CLI::log( '— Preenchendo Onclick ERP…' );
		$this->preencher_solucao_onclick_erp();

		WP_CLI::log( '— Preenchendo Propz…' );
		$this->preencher_solucao_propz();

		WP_CLI::log( '— Preenchendo Microsoft Teams…' );
		$this->preencher_solucao_microsoft_teams();

		WP_CLI::log( '— Preenchendo Snowflake…' );
		$this->preencher_solucao_snowflake();

		WP_CLI::log( '— Preenchendo Databricks…' );
		$this->preencher_solucao_databricks();

		WP_CLI::log( '— Preenchendo AWS…' );
		$this->preencher_solucao_aws();

		WP_CLI::log( '— Preenchendo Microsoft Azure…' );
		$this->preencher_solucao_microsoft_azure();

		WP_CLI::log( '— Preenchendo Google Cloud…' );
		$this->preencher_solucao_google_cloud();

		WP_CLI::log( '— Preenchendo Serviços Financeiros…' );
		$this->preencher_solucao_servicos_financeiros();

		WP_CLI::log( '— Preenchendo Manufatura…' );
		$this->preencher_solucao_manufatura();

		WP_CLI::log( '— Preenchendo Software (ISV)…' );
		$this->preencher_solucao_software_isv();

		WP_CLI::log( '— Preenchendo Logística (3PL)…' );
		$this->preencher_solucao_logistica_3pl();

		WP_CLI::log( '— Preenchendo Varejo…' );
		$this->preencher_solucao_varejo();

		WP_CLI::log( '— Preenchendo Seguros…' );
		$this->preencher_solucao_seguros();

		WP_CLI::log( '— Preenchendo Hotelaria e Turismo…' );
		$this->preencher_solucao_hotelaria_e_turismo();

		WP_CLI::log( '— Preenchendo Recursos Humanos (RH)…' );
		$this->preencher_solucao_recursos_humanos_rh();

		WP_CLI::log( '— Preenchendo Marketing…' );
		$this->preencher_solucao_marketing();

		WP_CLI::log( '— Preenchendo Operações de Receita (RevOps)…' );
		$this->preencher_solucao_operacoes_de_receita_revops();

		WP_CLI::log( '— Preenchendo Financeiro…' );
		$this->preencher_solucao_financeiro();

		WP_CLI::log( '— Preenchendo Atualização de Sistemas Legados…' );
		$this->preencher_solucao_atualizacao_de_sistemas_legados();

		WP_CLI::log( '— Preenchendo Integração Pós-Fusão…' );
		$this->preencher_solucao_integracao_pos_fusao();

		WP_CLI::log( '— Preenchendo Compras ao Pagamento (S2P)…' );
		$this->preencher_solucao_compras_ao_pagamento();

		WP_CLI::log( '— Preenchendo Gemini…' );
		$this->preencher_solucao_gemini();

		WP_CLI::log( '— Preenchendo Claude…' );
		$this->preencher_solucao_claude();

		WP_CLI::log( '— Preenchendo ChatGPT…' );
		$this->preencher_solucao_chatgpt();

		WP_CLI::log( '— Preenchendo IA Corporativa…' );
		$this->preencher_solucao_ia_corporativa();

		WP_CLI::log( '— Preenchendo Visão 360° do Cliente…' );
		$this->preencher_solucao_visao_360_do_cliente();

		WP_CLI::log( '— Preenchendo Soberania de Dados…' );
		$this->preencher_solucao_soberania_de_dados();

		WP_CLI::log( '— Preenchendo Centro de Excelência em Integração…' );
		$this->preencher_solucao_centro_de_excelencia_em_integracao();

		WP_CLI::log( '— Preenchendo Jornada do Colaborador (H2R)…' );
		$this->preencher_solucao_jornada_do_colaborador();

		WP_CLI::log( '— Preenchendo Pedido ao Recebimento (O2C)…' );
		$this->preencher_solucao_pedido_ao_recebimento();

		WP_CLI::log( '— Montando menus…' );
		$this->criar_menus( $paginas, $termos_solucao );

		WP_CLI::log( '— Ajustando o Customizer…' );
		$this->configurar_customizer();

		WP_CLI::log( '— Criando as versões traduzidas…' );
		$this->traduzir_site( $paginas, $termos_solucao );

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

		$arquivos = glob( $dir . '/*.{png,jpg,jpeg,svg,webp}', GLOB_BRACE );
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

	/**
	 * Recupera os IDs das páginas do seed sem recriá-las.
	 *
	 * @return array<string,int> slug => ID.
	 */
	protected function ids_das_paginas() {
		$ids = array();

		foreach ( array_keys( $this->paginas_en() ) as $slug ) {
			$ids[ $slug ] = $this->id_do_seed( 'pagina:' . $slug, 'page' );
		}

		return $ids;
	}

	/**
	 * Recupera os IDs dos termos de solução do seed sem recriá-los.
	 *
	 * @return array<string,int> chave => term_id.
	 */
	protected function ids_dos_termos_solucao() {
		$ids = array();

		$termos = get_terms(
			array(
				'taxonomy'   => 'cli_categoria_solucao',
				'hide_empty' => false,
				'lang'       => '',
			)
		);

		foreach ( (array) $termos as $termo ) {
			if ( is_wp_error( $termo ) ) {
				continue;
			}

			$chave = get_term_meta( $termo->term_id, self::META, true );

			if ( $chave && ! preg_match( '/:[a-z]{2}(_[A-Z]{2})?$/', $chave ) ) {
				$ids[ $chave ] = (int) $termo->term_id;
			}
		}

		return $ids;
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
		// A ordem define a órbita do hero: os 8 primeiros vão para o lado
		// esquerdo e os 8 seguintes para o direito (ver template-parts/home/hero.php).
		$itens = array(
			array( 'VTEX', 'integracao-vtex', true ),
			array( 'Claude', 'integracao-claude', true ),
			array( 'TOTVS', 'integracao-totvs', true ),
			array( 'SAP', 'integracao-sap', true ),
			array( 'IFS', 'integracao-ifs', true ),
			array( 'Zendesk', 'integracao-zendesk', true ),
			array( 'Sankhya', 'integracao-sankhya', true ),
			array( 'WhatsApp', 'integracao-whatsapp', true ),
			array( 'Thomson Reuters', 'integracao-thomson-reuters', true ),
			array( 'MV', 'integracao-mv', true ),
			array( 'OpenAI', 'integracao-openai', true ),
			array( 'ServiceNow', 'integracao-servicenow', true ),
			array( 'Salesforce', 'integracao-salesforce', true ),
			array( 'Senior', 'integracao-senior', true ),
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
			array( 'Moura', 'cliente-moura', false ),
			array( 'Sustentare', 'cliente-sustentare', false ),
			array( 'Clamper', 'cliente-clamper', false ),
			array( 'Legrand', 'cliente-legrand', false ),
			array( 'Neogrid', 'cliente-neogrid', false ),
			array( 'Zukkin', 'cliente-zukkin', false ),
			array( 'B2List', 'cliente-b2list', false ),
			array( 'Peixoto', 'cliente-peixoto', false ),
			array( 'SEG Imob', 'cliente-seg-imob', false ),
			array( 'Utrip', 'cliente-utrip', false ),
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
			array( 'ISO/IEC 27701', 'selo-iso-27701' ),
			array( 'ISO/IEC 27001', 'selo-iso-27001' ),
			array( 'ISO/IEC 27018', 'selo-iso-27018' ),
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
			'metrica_3_numero'      => '+30 mil',
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
			'cases_metricas'        => array_values( array_filter( array( $cases['petro'] ?? 0, $cases['moura2'] ?? 0 ) ) ),
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
				'nome'   => 'Tecnologias',
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
					'hubspot-crm'                    => 'HubSpot CRM',
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
				'onclick-erp'                    => 'Onclick ERP',
				'propz'                          => 'Propz',
				'microsoft-teams'                => 'Microsoft Teams',
				'snowflake'                      => 'Snowflake',
				'databricks'                     => 'Databricks',
				'aws'                            => 'AWS',
				'microsoft-azure'                => 'Microsoft Azure',
				'google-cloud'                   => 'Google Cloud',
				'gemini'                         => 'Gemini',
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
					'integracao-pos-fusao'            => 'Integração Pós-Fusão',
					'centro-de-excelencia-em-integracao' => 'Centro de Excelência em Integração',
				),
			),
		);

		// Mapa retornado: chave_pai => term_id e chave_filho => term_id.
		$ids = array();

		foreach ( $hierarquia as $chave_pai => $dados_pai ) {
			// Termo pai — busca por slug para sobreviver a renomeações.
			$termo_pai = get_term_by( 'slug', $chave_pai, $tax );
			if ( $termo_pai ) {
				$pai_id = (int) $termo_pai->term_id;
				if ( $termo_pai->name !== $dados_pai['nome'] ) {
					wp_update_term( $pai_id, $tax, array( 'name' => $dados_pai['nome'] ) );
				}
			} else {
				$ins = wp_insert_term( $dados_pai['nome'], $tax, array( 'slug' => $chave_pai ) );
				if ( is_wp_error( $ins ) ) {
					WP_CLI::warning( "  Categoria \"{$dados_pai['nome']}\": " . $ins->get_error_message() );
					continue;
				}
				$pai_id = (int) $ins['term_id'];
			}
			update_term_meta( $pai_id, self::META, $chave_pai );
			$ids[ $chave_pai ] = $pai_id;

			// Termos filhos + posts — busca por slug para sobreviver a renomeações.
			foreach ( $dados_pai['filhos'] as $chave_filho => $nome_filho ) {
				$termo_filho = get_term_by( 'slug', $chave_filho, $tax );
				if ( $termo_filho ) {
					$filho_id = (int) $termo_filho->term_id;
					if ( $termo_filho->name !== $nome_filho ) {
						wp_update_term( $filho_id, $tax, array( 'name' => $nome_filho ) );
					}
				} else {
					$ins_filho = wp_insert_term( $nome_filho, $tax, array( 'slug' => $chave_filho, 'parent' => $pai_id ) );
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
					// Logo para os cards do catálogo (sem efeito se o arquivo não existir).
					$this->definir_thumb( $post_id, 'catalogo-logo-' . $chave_filho );
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
					'Claude'         => $purl( 'claude' ),
					'ChatGPT'        => $purl( 'chatgpt' ),
					'SAP'            => $purl( 'sap' ),
					'Salesforce'     => $purl( 'salesforce' ),
					'TOTVS Protheus' => $purl( 'totvs-protheus' ),
					'Sankhya'        => $purl( 'sankhya' ),
					'Senior'         => $purl( 'senior' ),
					'Dynamics 365'   => $purl( 'dynamics-365' ),
					array(
						'titulo'  => 'Ver todos',
						'url'     => $turl( 'tecnologia' ),
						'classes' => 'link-ver-todos',
					),
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
					array( 'titulo' => 'AWS', 'url' => $purl( 'aws' ), 'classes' => 'link-sem-logo' ),
					array( 'titulo' => 'Google Cloud', 'url' => $purl( 'google-cloud' ), 'classes' => 'link-sem-logo' ),
					array( 'titulo' => 'Microsoft Azure', 'url' => $purl( 'microsoft-azure' ), 'classes' => 'link-sem-logo' ),
				),
			),
			array(
				'titulo' => 'Por Iniciativa',
				'url'    => $turl( 'por-iniciativa' ),
				'filhos' => array(
					'Atualização de Sistemas Legados'    => $purl( 'atualizacao-de-sistemas-legados' ),
					'Pedido ao Recebimento (O2C)'        => $purl( 'pedido-ao-recebimento' ),
					'IA Corporativa'                     => $purl( 'ia-corporativa' ),
					'Compras ao Pagamento (S2P)'         => $purl( 'compras-ao-pagamento' ),
					'Jornada do Colaborador (H2R)'       => $purl( 'jornada-do-colaborador' ),
					'Soberania de Dados'                 => $purl( 'soberania-de-dados' ),
					'Visão 360° do Cliente'              => $purl( 'visao-360-do-cliente' ),
					'Integração Pós-Fusão'               => $purl( 'integracao-pos-fusao' ),
					'Centro de Excelência em Integração' => $purl( 'centro-de-excelencia-em-integracao' ),
				),
			),
		);

		$descricao_produto    = 'Integre todos os seus sistemas e coloque agentes de IA personalizados para trabalhar em seus processos.';
		$descricao_signature  = 'Uma experiência dedicada para empresas que conduzem projetos críticos e exigem um nível superior de acompanhamento, governança e suporte.';

		// --- Menu principal --------------------------------------------------
		$this->montar_menu(
			'principal',
			'CLI — Menu Principal',
			array(
				array(
					// Sem URL: o item só abre o painel de cartões (issue #93).
					'titulo' => 'Plataforma',
					'url'    => '#',
					'filhos' => array(
						array(
							'titulo'    => 'CLI Connect',
							'url'       => '/cli-connect/',
							'descricao' => $descricao_produto,
						),
						array(
							'titulo'    => 'CLI Signature',
							'url'       => '/cli-signature/',
							'descricao' => $descricao_signature,
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
		/*
		 * As colunas de soluções são as mesmas do mega menu. Reaproveitar a árvore
		 * do $solucoes_mega — em vez de repetir as listas — é o que impede os dois
		 * menus de voltarem a divergir, que foi o problema da issue #105.
		 */
		$coluna = function ( $titulo ) use ( $solucoes_mega ) {
			foreach ( $solucoes_mega as $grupo ) {
				if ( $titulo === $grupo['titulo'] ) {
					return $grupo;
				}
			}

			return array( 'titulo' => $titulo, 'url' => '#', 'filhos' => array() );
		};

		// O rodapé do Figma usa outros dois rótulos para as mesmas colunas.
		$renomear = function ( $grupo, $titulo ) {
			$grupo['titulo'] = $titulo;

			return $grupo;
		};

		$this->montar_menu(
			'rodape',
			'CLI — Rodapé',
			array(
				// Coluna 1: Sistemas
				array(
					'titulo' => 'col-sistemas',
					'url'    => '#',
					'filhos' => array( $renomear( $coluna( 'Tecnologia' ), 'Sistemas' ) ),
				),
				// Coluna 2: Indústria
				array(
					'titulo' => 'col-industria',
					'url'    => '#',
					'filhos' => array( $coluna( 'Indústria' ) ),
				),
				// Coluna 3: Departamento + Nuvem
				array(
					'titulo' => 'col-departamento-nuvem',
					'url'    => '#',
					'filhos' => array( $coluna( 'Departamento' ), $coluna( 'Nuvem' ) ),
				),
				// Coluna 4: Iniciativas
				array(
					'titulo' => 'col-iniciativas',
					'url'    => '#',
					'filhos' => array( $renomear( $coluna( 'Por Iniciativa' ), 'Iniciativas' ) ),
				),
				// Coluna 5: Plataforma + Recursos
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
							'url'    => '#',
							'filhos' => array(
								'Cases'            => $cases_url,
								'Blog'             => $blog_url,
								'Trabalhe Conosco' => '/trabalhe-conosco/',
								'Contato'          => '/contato/',
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
	 * sobre_foto_1 e dep_foto são populados via seed (tc-sobre-equipe.png e
	 * tc-dep-foto-vitoria.png em assets/seed/).
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
			'sobre_foto_1'  => $this->img( 'tc-sobre-equipe' ),

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

			'valor_1_icone'  => 'shield',
			'valor_1_titulo' => 'Confiança',
			'valor_1_texto'  => 'Agimos com transparência, segurança e respeito. Cumprimos o que prometemos e construímos relações de confiança duradouras com clientes e equipes.',

			'valor_2_icone'  => 'verified',
			'valor_2_titulo' => 'Igualdade',
			'valor_2_texto'  => 'Damos oportunidade a quem deseja crescer, valorizando o talento e o desenvolvimento de cada pessoa independentemente de sua origem.',

			'valor_3_icone'  => 'group',
			'valor_3_titulo' => 'Sucesso do Cliente',
			'valor_3_texto'  => 'O problema do cliente é nosso. Resolvemos com conhecimento de negócio e nos orgulhamos de cada entrega bem-sucedida.',

			'valor_4_icone'  => 'lightbulb',
			'valor_4_titulo' => 'Inovação',
			'valor_4_texto'  => 'Estimulamos novas ideias e a criatividade para antecipar tendências e gerar soluções inovadoras com responsabilidade.',

			'valor_5_icone'  => 'group',
			'valor_5_titulo' => 'Colaboração',
			'valor_5_texto'  => 'Somos uma equipe unida. Compartilhamos conhecimento, conquistas e aprendizados com espírito de parceria e harmonia.',

			// 6. Depoimento.
			'dep_foto'  => $this->img( 'tc-dep-foto-vitoria' ),
			'dep_nome'  => 'Vitória Nunes',
			'dep_cargo' => 'Tech Lead',
			'dep_texto' => 'O trabalho em equipe na CLI é real e acontece no dia a dia. Contar com um time que se ajuda para resolver problemas complexos e que está em total sintonia com ferramentas inovadoras torna a nossa rotina leve e realizadora. No final das contas, o sucesso das nossas entregas é fruto desse ecossistema, onde recebemos apoio de todas as áreas da empresa.',

			// 7. Benefícios.
			'beneficios_eyebrow'   => 'BENEFÍCIOS',
			'beneficios_titulo'    => 'Tudo para que você possa fazer o seu melhor trabalho.',
			'beneficios_subtitulo' => 'Sabemos que você precisa de estrutura para dar o seu melhor. Por isso oferecemos benefícios que fazem diferença no dia a dia.',

			'beneficio_1_icone'  => 'favorite',
			'beneficio_1_titulo' => 'Saúde e Bem-estar',
			'beneficio_1_texto'  => 'Plano de saúde Bradesco e plano odontológico Odontomais, com cobertura ampla para você e seus dependentes.',

			'beneficio_2_icone'  => 'home',
			'beneficio_2_titulo' => 'Trabalho Remoto',
			'beneficio_2_texto'  => 'Auxílio mensal para pagar os custos do home office e manter sua rotina de trabalho remoto confortável.',

			'beneficio_3_icone'  => 'restaurant',
			'beneficio_3_titulo' => 'Alimentação',
			'beneficio_3_texto'  => 'Auxílio mensal em pix, que você pode usar como quiser para sua alimentação ao longo do mês.',

			'beneficio_4_icone'  => 'group',
			'beneficio_4_titulo' => 'Apoio à Família',
			'beneficio_4_texto'  => 'Auxílio-creche para filhos de até 5 anos, porque sabemos que a família também faz parte do sucesso de cada um.',

			'beneficio_5_icone'  => 'verified',
			'beneficio_5_titulo' => 'Qualidade de vida',
			'beneficio_5_texto'  => 'Acesso ao TotalPass: academias, esportes e atividades de bem-estar para manter a saúde física em dia.',

			'beneficio_6_icone'  => 'cake',
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
			'sap_dep_frase' => '“R$ 6 milhões economizados em horas de desenvolvimento ABAP”',
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

			// 2 · Métricas.
			'solucao_metrica_1_numero'     => '4x',
			'solucao_metrica_1_rotulo'     => 'mais rápido o cadastro de fornecedores',
			'solucao_metrica_2_numero'     => '50%',
			'solucao_metrica_2_rotulo'     => 'de ganho de eficiência',
			'solucao_metrica_3_numero'     => '30s',
			'solucao_metrica_3_rotulo'     => 'para o processamento automatizado de pedidos',

			// 3 · Pilares.
			'solucao_pilares_eyebrow'      => 'Pilares',
			'solucao_pilares_titulo'       => 'Modernize sua operação industrial com integrações preparadas para escala',
			'solucao_pilares_1_icone'      => $this->img( 'manufatura-pilar-1' ),
			'solucao_pilares_1_titulo'     => 'Visualize toda a operação em tempo real',
			'solucao_pilares_1_desc'       => 'Conecte produção, estoque e logística para acompanhar indicadores atualizados em toda a fábrica.',
			'solucao_pilares_2_icone'      => $this->img( 'manufatura-pilar-2' ),
			'solucao_pilares_2_titulo'     => 'Conecte fábrica e nuvem com segurança',
			'solucao_pilares_2_desc'       => 'Integre ambientes industriais à nuvem utilizando arquitetura zero-trust sem comprometer a operação.',
			'solucao_pilares_3_icone'      => $this->img( 'manufatura-pilar-3' ),
			'solucao_pilares_3_titulo'     => 'Alimente iniciativas de IA continuamente',
			'solucao_pilares_3_desc'       => 'Disponibilize dados da produção em tempo real para analytics, IA e automações inteligentes.',

			// 4 · Logos.
			'solucao_logos_texto'          => 'Integramos a manufatura de grandes empresas.',
			'solucao_logos_clientes'       => array_values(
				array_filter(
					array(
						$this->id_do_seed( 'cliente:seculus', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:moura', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:sustentare', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:clamper', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:legrand', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:culligan', 'cli_cliente' ),
					)
				)
			),

			// 5 · Casos de Uso.
			'solucao_casos_eyebrow'        => 'Casos de uso',
			'solucao_casos_titulo'         => 'Automatize os principais processos da manufatura',
			'solucao_casos_1_icone'        => $this->img( 'manufatura-caso-1' ),
			'solucao_casos_1_titulo'       => 'Migre para SAP S/4HANA sem downtime',
			'solucao_casos_1_desc'         => 'Conecte sistemas durante a migração preservando a continuidade das operações industriais.',
			'solucao_casos_2_icone'        => $this->img( 'manufatura-caso-2' ),
			'solucao_casos_2_titulo'       => 'Automatize o ciclo Order-to-Cash',
			'solucao_casos_2_desc'         => 'Integre pedidos, faturamento e logística para reduzir atrasos e retrabalho operacional.',
			'solucao_casos_3_icone'        => $this->img( 'manufatura-caso-3' ),
			'solucao_casos_3_titulo'       => 'Digitalize o Procure-to-Pay',
			'solucao_casos_3_desc'         => 'Conecte SAP Ariba, ERP e fornecedores para acelerar compras e aprovações.',
			'solucao_casos_4_icone'        => $this->img( 'manufatura-caso-4' ),
			'solucao_casos_4_titulo'       => 'Alimente IA com dados da produção',
			'solucao_casos_4_desc'         => 'Envie dados industriais continuamente para plataformas analíticas e modelos de inteligência artificial.',
			'solucao_casos_5_icone'        => $this->img( 'manufatura-caso-5' ),
			'solucao_casos_5_titulo'       => 'Conecte OT e cloud com segurança',
			'solucao_casos_5_desc'         => 'Integre MES, IoT e equipamentos industriais às plataformas de dados sem abrir o firewall.',
			'solucao_casos_cta_texto'      => 'Agende uma demonstração',
			'solucao_casos_cta_url'        => '/contato/',

			// 6 · Selos.
			'solucao_selos_eyebrow'        => 'compliance & segurança',
			'solucao_selos_titulo'         => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'          => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_manufatura_faq( $post_id );

		WP_CLI::log( "  Manufatura preenchido (ID: {$post_id})." );
	}

	/**
	 * Cria os posts cli_faq de Manufatura e vincula à solução.
	 *
	 * ATENÇÃO — texto provisório: o Figma mostra apenas as perguntas (o
	 * accordion está fechado no design). As respostas foram redigidas a partir
	 * do que a própria landing afirma nas seções anteriores e seguem pendentes
	 * de validação do cliente.
	 *
	 * @param int $post_id ID do post cli_solucao de Manufatura.
	 * @return void
	 */
	protected function preencher_manufatura_faq( $post_id ) {
		$itens = array(
			array(
				'faq:mf-ipaas-vs-sap',
				'Qual a diferença entre uma iPaaS e o SAP Integration Suite ou SAP MII?',
				'<p>O SAP Integration Suite e o SAP MII resolvem muito bem o que nasce e termina dentro do mundo SAP. Uma iPaaS trata a integração como uma camada independente: o mesmo ambiente conecta SAP S/4HANA, MES, WMS, Salesforce, sistemas industriais e serviços de nuvem, com governança e monitoramento únicos. Na prática, os dois convivem — a iPaaS assume os fluxos que atravessam fronteiras entre sistemas e evita que cada novo projeto vire uma integração ponto a ponto.</p>',
			),
			array(
				'faq:mf-ot-nuvem-seguranca',
				'É possível conectar equipamentos industriais à nuvem com segurança?',
				'<p>Sim. A comunicação com o ambiente industrial é feita por um agente instalado dentro da própria rede, que abre a conexão de dentro para fora — não é preciso expor portas de entrada no firewall. Sobre isso funciona uma arquitetura zero-trust: dados criptografados em trânsito, credenciais em cofre e cada acesso registrado, com cada fluxo enxergando apenas as informações de que precisa.</p>',
			),
			array(
				'faq:mf-mulesoft',
				'O CLI Connect pode substituir plataformas como MuleSoft?',
				'<p>Sim, esse tipo de substituição é um cenário comum de projeto. A avaliação passa por mapear as integrações existentes, o volume processado e as necessidades de governança, e a migração é feita por ondas: os fluxos críticos entram primeiro e os demais seguem em etapas, mantendo os dois ambientes em paralelo até o corte. O ganho costuma estar no custo de manutenção e na velocidade de criar fluxos novos.</p>',
			),
			array(
				'faq:mf-compliance-industrial',
				'A plataforma atende requisitos de compliance industrial?',
				'<p>A plataforma opera sob os padrões de segurança e privacidade listados nesta página — SOC 2, ISO 27001, ISO 27701, ISO 27018, PCI DSS e GDPR/LGPD, entre outros. Para a indústria, o que costuma pesar é a rastreabilidade: cada execução de fluxo fica registrada, com histórico de versões e trilha de auditoria, o que sustenta exigências de qualidade e de validação de processos.</p>',
			),
			array(
				'faq:mf-iot-volume',
				'Como a plataforma lida com grandes volumes de dados de sensores IoT?',
				'<p>O processamento é elástico e trabalha em fluxo contínuo, com filas que absorvem picos de coleta sem perder mensagem. Em vez de despejar o dado bruto no destino, os fluxos filtram, agregam e normalizam ainda no caminho — assim só o que tem uso analítico chega às plataformas de dados e aos modelos de IA, reduzindo custo de armazenamento e tempo de resposta.</p>',
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

		WP_CLI::log( sprintf( '  Manufatura FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/**
	 * Preenche os campos ACF do post cli_solucao "Software (ISV)".
	 *
	 * Landing page da indústria de software. Preenchida seção a seção; as
	 * ainda não preenchidas ficam vazias e, portanto, invisíveis.
	 *
	 * @return void
	 */
	protected function preencher_solucao_software_isv() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:software-isv', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  Software (ISV): post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'         => 'Para softwares',
			'solucao_hero_titulo'          => 'Entregue integrações nativas para seus clientes',
			'solucao_hero_titulo_destaque' => 'sem reconstruir conectores a cada projeto',
			'solucao_hero_corpo'           => 'Conecte seu produto a ERPs, CRMs e aplicações corporativas utilizando integrações reutilizáveis, APIs nativas e uma plataforma preparada para escalar seu software.',
			'solucao_hero_btn1_texto'      => 'Agende uma demonstração',
			'solucao_hero_btn1_url'        => '/contato/',
			'solucao_hero_btn2_texto'      => 'Conheça a plataforma',
			'solucao_hero_btn2_url'        => '/plataforma/',
			'solucao_hero_imagem'          => $this->img( 'software-isv-hero' ),

			// 2 · Métricas.
			'solucao_metrica_1_numero'     => '4x',
			'solucao_metrica_1_rotulo'     => 'mais rápido para entrega de projetos de integração e desenvolvimento',
			'solucao_metrica_2_numero'     => '350%',
			'solucao_metrica_2_rotulo'     => 'de aumento no ROI em ambientes de tecnologia',
			'solucao_metrica_3_numero'     => '5 dias',
			'solucao_metrica_3_rotulo'     => 'para a primeira integração',

			// 3 · Pilares.
			'solucao_pilares_eyebrow'      => 'Pilares',
			'solucao_pilares_titulo'       => 'Transforme integrações em vantagem competitiva',
			'solucao_pilares_1_icone'      => $this->img( 'software-isv-pilar-1' ),
			'solucao_pilares_1_titulo'     => 'Conecte qualquer ERP ou CRM',
			'solucao_pilares_1_desc'       => 'Amplie a compatibilidade do seu produto com integrações prontas para diferentes plataformas corporativas.',
			'solucao_pilares_2_icone'      => $this->img( 'software-isv-pilar-2' ),
			'solucao_pilares_2_titulo'     => 'Entregue integrações em minutos',
			'solucao_pilares_2_desc'       => 'Implemente a primeira pipeline rapidamente utilizando conectores reutilizáveis e arquitetura low-code.',
			'solucao_pilares_3_icone'      => $this->img( 'software-isv-pilar-3' ),
			'solucao_pilares_3_titulo'     => 'Escalone sem aumentar custos',
			'solucao_pilares_3_desc'       => 'Cresça conforme o consumo da plataforma sem cobrar ou manter conectores individuais.',

			// 4 · Logos.
			'solucao_logos_texto'          => 'Integramos softwares de grandes empresas.',
			'solucao_logos_clientes'       => array_values(
				array_filter(
					array(
						$this->id_do_seed( 'cliente:neogrid', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:zukkin', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:b2list', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:thomson-reuters', 'cli_cliente' ),
					)
				)
			),

			// 5 · Casos de Uso.
			'solucao_casos_eyebrow'        => 'Casos de uso',
			'solucao_casos_titulo'         => 'Entregue integrações como parte do seu produto',
			'solucao_casos_1_icone'        => $this->img( 'software-isv-caso-1' ),
			'solucao_casos_1_titulo'       => 'Disponibilize integrações nativas',
			'solucao_casos_1_desc'         => 'Utilize componentes reutilizáveis para conectar seu software aos principais sistemas corporativos.',
			'solucao_casos_2_icone'        => $this->img( 'software-isv-caso-2' ),
			'solucao_casos_2_titulo'       => 'Crie agentes de IA com MCP',
			'solucao_casos_2_desc'         => 'Desenvolva agentes inteligentes expostos como servidores MCP integrados ao seu produto.',
			'solucao_casos_3_icone'        => $this->img( 'software-isv-caso-3' ),
			'solucao_casos_3_titulo'       => 'Implante no ambiente do cliente',
			'solucao_casos_3_desc'         => 'Execute integrações na infraestrutura do cliente sem VPN ou portas abertas.',
			'solucao_casos_4_icone'        => $this->img( 'software-isv-caso-4' ),
			'solucao_casos_4_titulo'       => 'Monitore todos os clientes',
			'solucao_casos_4_desc'         => 'Centralize métricas, execuções e integrações em um único painel operacional.',
			'solucao_casos_5_icone'        => $this->img( 'software-isv-caso-5' ),
			'solucao_casos_5_titulo'       => 'Conecte qualquer modelo de IA',
			'solucao_casos_5_desc'         => 'Orquestre diferentes provedores de LLM diretamente nos fluxos de integração do produto.',
			'solucao_casos_cta_texto'      => 'Agende uma demonstração',
			'solucao_casos_cta_url'        => '/contato/',

			// 6 · Selos.
			'solucao_selos_eyebrow'        => 'compliance & segurança',
			'solucao_selos_titulo'         => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'          => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_software_isv_faq( $post_id );

		WP_CLI::log( "  Software (ISV) preenchido (ID: {$post_id})." );
	}

	/**
	 * Cria os posts cli_faq de Software (ISV) e vincula à solução.
	 *
	 * ATENÇÃO — texto provisório: o Figma mostra apenas as perguntas (o
	 * accordion está fechado no design). As respostas foram redigidas a partir
	 * do que a própria landing afirma nas seções anteriores e seguem pendentes
	 * de validação do cliente.
	 *
	 * @param int $post_id ID do post cli_solucao de Software (ISV).
	 * @return void
	 */
	protected function preencher_software_isv_faq( $post_id ) {
		$itens = array(
			array(
				'faq:isv-tempo-primeira-integracao',
				'Quanto tempo leva para criar uma integração nativa com Salesforce ou SAP?',
				'<p>A primeira integração costuma entrar no ar em cerca de cinco dias. O ganho vem de não começar do zero: os conectores para Salesforce, SAP e demais sistemas corporativos já existem, e o trabalho fica concentrado em mapear campos e regras de negócio em ambiente low-code. As integrações seguintes são ainda mais rápidas, porque reaproveitam os componentes construídos na primeira.</p>',
			),
			array(
				'faq:isv-mudanca-api-parceiro',
				'O que acontece quando a API de um parceiro é alterada?',
				'<p>A atualização acontece na camada de integração, não dentro do seu produto. Como o conector é mantido na plataforma e compartilhado por todos os clientes que o utilizam, a mudança é aplicada uma vez e vale para toda a base — em vez de virar uma correção por cliente. O monitoramento centralizado mostra quais fluxos foram afetados antes que isso chegue ao usuário final.</p>',
			),
			array(
				'faq:isv-isolamento-multi-tenant',
				'Como funciona o isolamento de dados em ambientes multi-tenant?',
				'<p>Cada cliente opera com credenciais e ambiente de execução próprios, e um fluxo só enxerga os dados do tenant a que pertence. Quando o cenário exige, a execução acontece dentro da infraestrutura do próprio cliente, sem VPN nem portas abertas — o dado sensível não sai do perímetro dele, e o painel central recebe apenas os registros de execução.</p>',
			),
			array(
				'faq:isv-custo-conectores-internos',
				'Qual o custo real de manter conectores desenvolvidos internamente?',
				'<p>O custo visível é o da construção; o que pesa é a manutenção. Cada conector interno vira código proprietário que precisa acompanhar mudanças de API, autenticação e volume, e esse esforço cresce junto com a base de clientes. Com integrações reutilizáveis, o time de produto para de manter conectores individuais e a operação escala conforme o consumo da plataforma.</p>',
			),
			array(
				'faq:isv-cargas-elevadas',
				'A plataforma suporta cargas de processamento muito elevadas?',
				'<p>Sim. O processamento é elástico e trabalha com filas que absorvem picos sem perder mensagem, o que permite atender desde um cliente pequeno até operações com milhões de execuções por mês no mesmo ambiente. O painel operacional acompanha volume, latência e falhas por cliente, e a capacidade acompanha o consumo sem exigir reescrita dos fluxos.</p>',
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

		WP_CLI::log( sprintf( '  Software (ISV) FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/**
	 * Preenche os campos ACF do post cli_solucao "Logística (3PL)".
	 *
	 * Landing page da indústria de logística. Preenchida seção a seção; as
	 * ainda não preenchidas ficam vazias e, portanto, invisíveis.
	 *
	 * @return void
	 */
	protected function preencher_solucao_logistica_3pl() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:logistica-3pl', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  Logística (3PL): post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'         => 'Para logística',
			'solucao_hero_titulo'          => 'Conecte clientes, transportadoras e sistemas logísticos',
			'solucao_hero_titulo_destaque' => 'em uma única plataforma',
			'solucao_hero_corpo'           => 'Integre ERPs, WMS, transportadoras e marketplaces para acelerar o onboarding de novos clientes, automatizar operações e escalar sua logística com previsibilidade.',
			'solucao_hero_btn1_texto'      => 'Agende uma demonstração',
			'solucao_hero_btn1_url'        => '/contato/',
			'solucao_hero_btn2_texto'      => 'Conheça a plataforma',
			'solucao_hero_btn2_url'        => '/plataforma/',
			'solucao_hero_imagem'          => $this->img( 'logistica-3pl-hero' ),

			// 2 · Métricas.
			'solucao_metrica_1_numero'     => '80%',
			'solucao_metrica_1_rotulo'     => 'de aumento na precisão de dados em tempo real',
			'solucao_metrica_2_numero'     => '50%',
			'solucao_metrica_2_rotulo'     => 'de redução do tempo de integração de parceiros e sistemas',
			'solucao_metrica_3_numero'     => '1',
			'solucao_metrica_3_rotulo'     => 'única plataforma para conectar todos os clientes',

			// 3 · Pilares.
			'solucao_pilares_eyebrow'      => 'Pilares',
			'solucao_pilares_titulo'       => 'Escale sua operação logística sem aumentar a complexidade',
			'solucao_pilares_1_icone'      => $this->img( 'logistica-3pl-pilar-1' ),
			'solucao_pilares_1_titulo'     => 'Acelere o onboarding de novos clientes',
			'solucao_pilares_1_desc'       => 'Reutilize integrações entre ERPs e WMS para reduzir o tempo de implantação de novos contratos.',
			'solucao_pilares_2_icone'      => $this->img( 'logistica-3pl-pilar-2' ),
			'solucao_pilares_2_titulo'     => 'Sincronize estoques automaticamente',
			'solucao_pilares_2_desc'       => 'Mantenha posições de estoque atualizadas entre clientes, operadores logísticos e sistemas corporativos.',
			'solucao_pilares_3_icone'      => $this->img( 'logistica-3pl-pilar-3' ),
			'solucao_pilares_3_titulo'     => 'Automatize documentos com IA',
			'solucao_pilares_3_desc'       => 'Extraia informações de PDFs e e-mails para iniciar processos logísticos automaticamente.',

			// 4 · Logos.
			'solucao_logos_texto'          => 'Integramos a logística de grandes empresas.',
			'solucao_logos_clientes'       => array_values(
				array_filter(
					array(
						$this->id_do_seed( 'cliente:martins', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:arcom', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:peixoto', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:real', 'cli_cliente' ),
					)
				)
			),

			// 5 · Casos de Uso.
			'solucao_casos_eyebrow'        => 'Casos de uso',
			'solucao_casos_titulo'         => 'Automatize os principais processos logísticos',
			'solucao_casos_1_icone'        => $this->img( 'logistica-3pl-caso-1' ),
			'solucao_casos_1_titulo'       => 'Sincronize posições de estoque',
			'solucao_casos_1_desc'         => 'Atualize saldos automaticamente entre WMS, ERP e sistemas dos clientes em tempo real.',
			'solucao_casos_2_icone'        => $this->img( 'logistica-3pl-caso-2' ),
			'solucao_casos_2_titulo'       => 'Automatize pedidos multicanal',
			'solucao_casos_2_desc'         => 'Receba pedidos de marketplaces e direcione automaticamente para separação e expedição.',
			'solucao_casos_3_icone'        => $this->img( 'logistica-3pl-caso-3' ),
			'solucao_casos_3_titulo'       => 'Conecte múltiplas transportadoras',
			'solucao_casos_3_desc'         => 'Centralize integrações com transportadoras sem desenvolver conexões individuais para cada operação.',
			'solucao_casos_4_icone'        => $this->img( 'logistica-3pl-caso-4' ),
			'solucao_casos_4_titulo'       => 'Automatize devoluções',
			'solucao_casos_4_desc'         => 'Gerencie processos de RMA entre clientes, transportadoras e centros de distribuição automaticamente.',
			'solucao_casos_5_icone'        => $this->img( 'logistica-3pl-caso-5' ),
			'solucao_casos_5_titulo'       => 'Preveja picos de demanda com IA',
			'solucao_casos_5_desc'         => 'Utilize dados operacionais para antecipar volumes e melhorar o planejamento logístico.',
			'solucao_casos_cta_texto'      => 'Agende uma demonstração',
			'solucao_casos_cta_url'        => '/contato/',

			// 6 · Selos.
			'solucao_selos_eyebrow'        => 'compliance & segurança',
			'solucao_selos_titulo'         => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'          => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_logistica_3pl_faq( $post_id );

		WP_CLI::log( "  Logística (3PL) preenchido (ID: {$post_id})." );
	}

	/**
	 * Cria os posts cli_faq de Logística (3PL) e vincula à solução.
	 *
	 * ATENÇÃO — texto provisório: o Figma mostra apenas as perguntas (o
	 * accordion está fechado no design). As respostas foram redigidas a partir
	 * do que a própria landing afirma nas seções anteriores e seguem pendentes
	 * de validação do cliente.
	 *
	 * @param int $post_id ID do post cli_solucao de Logística (3PL).
	 * @return void
	 */
	protected function preencher_logistica_3pl_faq( $post_id ) {
		$itens = array(
			array(
				'faq:lg-onboarding-cliente',
				'Quanto tempo leva para integrar um novo cliente?',
				'<p>O prazo depende de quantos sistemas entram no fluxo, mas o ganho vem da reutilização: os conectores para ERPs e WMS já existem e são reaproveitados de um contrato para o outro. Na prática, o que era um projeto de integração do zero passa a ser a configuração de um fluxo já validado — é o que sustenta a redução de 50% no tempo de integração de parceiros e sistemas citada nesta página.</p>',
			),
			array(
				'faq:lg-avaliar-plataforma-3pl',
				'O que avaliar em uma plataforma para operadores logísticos 3PL?',
				'<p>Três pontos costumam decidir a escolha: se a plataforma reaproveita integrações entre clientes ou obriga a começar do zero a cada contrato; se governa no mesmo ambiente os sistemas em nuvem e os instalados na infraestrutura do cliente; e se o modelo acompanha picos sazonais sem exigir capacidade contratada o ano inteiro. Vale olhar também a trilha de auditoria, já que o operador responde por dados de terceiros.</p>',
			),
			array(
				'faq:lg-erp-on-premises',
				'A plataforma conecta ERPs instalados on-premises?',
				'<p>Sim. A conexão com ERPs e WMS instalados na rede do cliente é feita por um agente dentro da própria infraestrutura, que abre a comunicação de dentro para fora — sem expor portas de entrada no firewall. Fluxos em nuvem e on-premises ficam sob o mesmo ambiente de governança e monitoramento.</p>',
			),
			array(
				'faq:lg-custo-alto-volume',
				'Como funciona o custo para operações com alto volume?',
				'<p>O dimensionamento considera o volume processado e a quantidade de integrações ativas, não o número de usuários. Como os fluxos filtram e agregam os dados ainda no caminho, o custo de operações grandes tende a crescer menos que proporcionalmente ao número de pedidos e eventos, e os picos sazonais são absorvidos pelo processamento elástico.</p>',
			),
			array(
				'faq:lg-multiplas-transportadoras',
				'É possível integrar várias transportadoras sem criar uma integração para cada uma?',
				'<p>Sim — é um dos casos de uso desta página. Em vez de uma conexão dedicada por transportadora, a integração é centralizada: coleta, rastreio e entrega passam por um fluxo comum e cada transportadora entra como mais uma configuração. Incluir uma nova deixa de ser um projeto de desenvolvimento.</p>',
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

		WP_CLI::log( sprintf( '  Logística (3PL) FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/**
	 * Preenche os campos ACF do post cli_solucao "Varejo".
	 *
	 * Cresce uma seção por rodada, na ordem do Figma.
	 *
	 * @return void
	 */
	protected function preencher_solucao_varejo() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:varejo', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  Varejo: post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'         => 'Para o varejo',
			'solucao_hero_titulo'          => 'Conecte',
			'solucao_hero_titulo_destaque' => 'toda a jornada',
			'solucao_hero_titulo_fim'      => 'de compra, do carrinho à entrega.',
			'solucao_hero_corpo'           => 'Integre e-commerce, ERP, logística, CRM e marketplaces para oferecer experiências consistentes, acelerar entregas e evoluir sua operação sem interrupções.',
			'solucao_hero_btn1_texto'      => 'Agende uma demonstração',
			'solucao_hero_btn1_url'        => '/contato/',
			'solucao_hero_btn2_texto'      => 'Conheça a plataforma',
			'solucao_hero_btn2_url'        => '/plataforma/',
			'solucao_hero_imagem'          => $this->img( 'varejo-hero' ),

			// 2 · Métricas.
			'solucao_metrica_1_numero'     => '70%',
			'solucao_metrica_1_rotulo'     => 'Redução no tempo de entrega.',
			'solucao_metrica_2_numero'     => '40%',
			'solucao_metrica_2_rotulo'     => 'mais rápido o cadastro de fornecedores',
			'solucao_metrica_3_numero'     => '1600%',
			'solucao_metrica_3_rotulo'     => 'de ROI em 10 meses',

			// 3 · Pilares.
			'solucao_pilares_eyebrow'      => 'Pilares',
			'solucao_pilares_titulo'       => 'Transforme dados conectados em melhores experiências de compra',
			'solucao_pilares_1_icone'      => $this->img( 'varejo-pilar-1' ),
			'solucao_pilares_1_titulo'     => 'Unifique a visão do cliente',
			'solucao_pilares_1_desc'       => 'Centralize informações de vendas, atendimento e logística para personalizar cada interação com consumidores.',
			'solucao_pilares_2_icone'      => $this->img( 'varejo-pilar-2' ),
			'solucao_pilares_2_titulo'     => 'Migre plataformas sem interromper vendas',
			'solucao_pilares_2_desc'       => 'Troque plataformas de e-commerce mantendo operações, pedidos e integrações funcionando normalmente.',
			'solucao_pilares_3_icone'      => $this->img( 'varejo-pilar-3' ),
			'solucao_pilares_3_titulo'     => 'Automatize entregas com inteligência artificial',
			'solucao_pilares_3_desc'       => 'Otimize rotas, decisões logísticas e processos de entrega utilizando dados em tempo real.',

			// 4 · Logos.
			'solucao_logos_texto'          => 'Integramos o varejo de grandes empresas.',
			'solucao_logos_clientes'       => array_values(
				array_filter(
					array(
						$this->id_do_seed( 'cliente:indiana', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:arcom', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:martins', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:real', 'cli_cliente' ),
					)
				)
			),

			// 5 · Casos de Uso. O frame não traz o card CTA azul, então
			// solucao_casos_cta_* fica vazio e o template o omite.
			'solucao_casos_eyebrow'        => 'Casos de uso',
			'solucao_casos_titulo'         => 'Automatize toda a operação do varejo',
			'solucao_casos_1_icone'        => $this->img( 'varejo-caso-1' ),
			'solucao_casos_1_titulo'       => 'Conecte experiências de compra',
			'solucao_casos_1_desc'         => 'Integre canais físicos e digitais para oferecer jornadas consistentes em todos os pontos de contato.',
			'solucao_casos_2_icone'        => $this->img( 'varejo-caso-2' ),
			'solucao_casos_2_titulo'       => 'Otimize a última milha',
			'solucao_casos_2_desc'         => 'Automatize entregas utilizando dados operacionais para reduzir custos e melhorar prazos.',
			'solucao_casos_3_icone'        => $this->img( 'varejo-caso-3' ),
			'solucao_casos_3_titulo'       => 'Integre canais de social commerce',
			'solucao_casos_3_desc'         => 'Conecte pedidos originados nas redes sociais aos sistemas comerciais e logísticos.',
			'solucao_casos_4_icone'        => $this->img( 'varejo-caso-4' ),
			'solucao_casos_4_titulo'       => 'Migre seu ERP para a nuvem',
			'solucao_casos_4_desc'         => 'Modernize sua arquitetura preservando integrações e continuidade das operações comerciais.',
			'solucao_casos_5_icone'        => $this->img( 'varejo-caso-5' ),
			'solucao_casos_5_titulo'       => 'Personalize recomendações com IA',
			'solucao_casos_5_desc'         => 'Utilize dados integrados para recomendar produtos conforme comportamento e histórico de compras.',
			'solucao_casos_6_icone'        => $this->img( 'varejo-caso-6' ),
			'solucao_casos_6_titulo'       => 'Automatize a logística reversa',
			'solucao_casos_6_desc'         => 'Gerencie devoluções, reembolsos e viabilidade de revenda com fluxos inteligentes automatizados.',

			// 6 · Selos. Os 10 badges são assets estáticos do tema; a seção só
			// traz o texto.
			'solucao_selos_eyebrow'        => 'compliance & segurança',
			'solucao_selos_titulo'         => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'          => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_varejo_faq( $post_id );

		WP_CLI::log( sprintf( '  Varejo: %d campos preenchidos.', count( $campos ) ) );
	}

	/**
	 * Preenche os campos ACF do post cli_solucao "Seguros".
	 *
	 * Landing page da indústria de seguros, montada a partir dos frames
	 * "Seção - Hero" a "Seção - FAQ" do Figma. Cresce uma seção por rodada, na
	 * ordem do Figma; as não preenchidas ficam vazias e, portanto, invisíveis.
	 *
	 * @return void
	 */
	protected function preencher_solucao_seguros() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:seguros', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  Seguros: post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'         => 'Para seguros',
			'solucao_hero_titulo'          => 'Conecte sistemas legados e',
			'solucao_hero_titulo_destaque' => 'acelere o lançamento',
			'solucao_hero_titulo_fim'      => 'de novos produtos de seguros',
			'solucao_hero_corpo'           => 'Integre Guidewire, Duck Creek, Salesforce e outras aplicações sem substituir seu core, modernizando operações com segurança e velocidade.',
			'solucao_hero_btn1_texto'      => 'Agende uma demonstração',
			'solucao_hero_btn1_url'        => '/contato/',
			'solucao_hero_btn2_texto'      => 'Conheça a plataforma',
			'solucao_hero_btn2_url'        => '/plataforma/',
			'solucao_hero_imagem'          => $this->img( 'seguros-hero' ),

			// 2 · Métricas.
			'solucao_metrica_1_numero'     => '10 min',
			'solucao_metrica_1_rotulo'     => 'de tempo total na subscrição de riscos',
			'solucao_metrica_2_numero'     => '6',
			'solucao_metrica_2_rotulo'     => 'para o retorno financeiro de sistemas legados de seguros',
			'solucao_metrica_3_numero'     => '100%',
			'solucao_metrica_3_rotulo'     => 'de conformidade regulatória alcançada na troca de dados sigilosos',

			// 3 · Pilares.
			'solucao_pilares_eyebrow'      => 'Pilares',
			'solucao_pilares_titulo'       => 'Sua operação seguradora pronta para o futuro',
			'solucao_pilares_1_icone'      => $this->img( 'seguros-pilar-1' ),
			'solucao_pilares_1_titulo'     => 'Sincronize dados em tempo real',
			'solucao_pilares_1_desc'       => 'Conecte apólices, sinistros e canais de distribuição com informações sempre atualizadas.',
			'solucao_pilares_2_icone'      => $this->img( 'seguros-pilar-2' ),
			'solucao_pilares_2_titulo'     => 'Automatize decisões com IA',
			'solucao_pilares_2_desc'       => 'Utilize inteligência artificial para agilizar underwriting e triagem inicial de sinistros.',
			'solucao_pilares_3_icone'      => $this->img( 'seguros-pilar-3' ),
			'solucao_pilares_3_titulo'     => 'Conecte corretores em tempo real',
			'solucao_pilares_3_desc'       => 'Disponibilize informações atualizadas para parceiros comerciais por meio de portais integrados.',

			// 4 · Logos.
			'solucao_logos_texto'          => 'Integramos os sistemas de seguros de grandes empresas.',
			'solucao_logos_clientes'       => array_values(
				array_filter(
					array(
						$this->id_do_seed( 'cliente:seg-imob', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:bnp-paribas-cardif', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:hsbc', 'cli_cliente' ),
					)
				)
			),

			// 5 · Casos de Uso.
			'solucao_casos_eyebrow'        => 'Casos de uso',
			'solucao_casos_titulo'         => 'Automatize os principais processos do mercado segurador',
			'solucao_casos_1_icone'        => $this->img( 'seguros-caso-1' ),
			'solucao_casos_1_titulo'       => 'Conecte sistemas core ao CRM',
			'solucao_casos_1_desc'         => 'Integre Guidewire, Duck Creek e outras plataformas aos sistemas comerciais da seguradora.',
			'solucao_casos_2_icone'        => $this->img( 'seguros-caso-2' ),
			'solucao_casos_2_titulo'       => 'Automatize a gestão de sinistros',
			'solucao_casos_2_desc'         => 'Integre abertura, análise, prevenção à fraude e pagamento em um único fluxo.',
			'solucao_casos_3_icone'        => $this->img( 'seguros-caso-3' ),
			'solucao_casos_3_titulo'       => 'Sincronize portais de corretores',
			'solucao_casos_3_desc'         => 'Mantenha agentes e parceiros atualizados com informações consistentes sobre clientes e apólices.',
			'solucao_casos_4_icone'        => $this->img( 'seguros-caso-4' ),
			'solucao_casos_4_titulo'       => 'Atenda requisitos do Open Insurance',
			'solucao_casos_4_desc'         => 'Integre sistemas seguindo padrões regulatórios e requisitos definidos pela SUSEP.',
			'solucao_casos_5_icone'        => $this->img( 'seguros-caso-5' ),
			'solucao_casos_5_titulo'       => 'Acelere o underwriting com IA',
			'solucao_casos_5_desc'         => 'Utilize modelos inteligentes para apoiar análises de risco e emissão de novas apólices.',
			'solucao_casos_cta_texto'      => 'Agende uma demonstração',
			'solucao_casos_cta_url'        => '/contato/',

			// 6 · Selos.
			'solucao_selos_eyebrow'        => 'compliance & segurança',
			'solucao_selos_titulo'         => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'          => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_seguros_faq( $post_id );

		WP_CLI::log( "  Seguros preenchido (ID: {$post_id})." );
	}

	/**
	 * Cria os posts cli_faq de Seguros e vincula à solução.
	 *
	 * ATENÇÃO — texto provisório: o Figma mostra apenas as perguntas (o
	 * accordion está fechado no design). As respostas foram redigidas a partir
	 * do que a própria landing afirma nas seções anteriores e seguem pendentes
	 * de validação do cliente.
	 *
	 * @param int $post_id ID do post cli_solucao de Seguros.
	 * @return void
	 */
	protected function preencher_seguros_faq( $post_id ) {
		$itens = array(
			array(
				'faq:sg-prazo-guidewire-duck-creek',
				'Quanto tempo leva para integrar Guidewire ou Duck Creek?',
				'<p>O prazo depende de quantos processos entram na primeira onda, não do tamanho do core. Como a conexão é feita por uma camada de integração sobre APIs já existentes, um fluxo bem delimitado — emissão de apólice ou abertura de sinistro, por exemplo — costuma sair em semanas, e não em meses. O caminho usual é começar por um processo de alto volume, colocá-lo em produção e seguir ampliando a partir dele.</p>',
			),
			array(
				'faq:sg-plataforma-vs-conectores',
				'Qual a vantagem de utilizar uma plataforma em vez de conectores nativos?',
				'<p>Conectores nativos resolvem bem um par de sistemas por vez, mas cada nova ponta vira um projeto próprio, com seu próprio monitoramento e sua própria manutenção. Uma plataforma trata a integração como camada única: o mesmo ambiente conecta o core de seguros, o CRM, os portais de corretores e os serviços de nuvem, com governança, versionamento e trilha de auditoria centralizados. O ganho aparece quando o número de integrações cresce.</p>',
			),
			array(
				'faq:sg-criterios-escolha',
				'O que as seguradoras devem avaliar ao escolher uma plataforma de integração?',
				'<p>Quatro pontos costumam decidir: se a plataforma conversa com os sistemas core do mercado sem desenvolvimento sob medida; se atende às exigências regulatórias de tratamento de dados sigilosos; se registra cada execução de forma auditável; e se a equipe interna consegue criar fluxos novos sem depender de terceiros. O quinto ponto, menos citado, é o custo de manter as integrações vivas ao longo dos anos.</p>',
			),
			array(
				'faq:sg-modernizar-sem-trocar-core',
				'É possível modernizar a operação sem substituir o sistema core?',
				'<p>Sim — é justamente a proposta desta abordagem. O core continua sendo a fonte da verdade para apólices e sinistros, e a camada de integração expõe esses dados para os canais digitais, o CRM e os parceiros. Na prática, a seguradora lança produtos e experiências novas sobre o sistema que já tem, sem carregar o risco e o prazo de uma substituição completa.</p>',
			),
			array(
				'faq:sg-open-insurance',
				'Como a plataforma atende aos requisitos do Open Insurance brasileiro?',
				'<p>O Open Insurance exige expor e consumir dados por APIs padronizadas, com consentimento do cliente e rastreabilidade de cada troca. A plataforma cobre esse desenho: publica APIs nos padrões definidos pela SUSEP, controla autenticação e escopo de cada consentimento e mantém registro de todas as chamadas. Assim, a adequação regulatória se apoia na mesma camada que já conecta os sistemas internos.</p>',
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

		WP_CLI::log( sprintf( '  Seguros FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/**
	 * Cria os cli_faq do Varejo e vincula ao relationship da seção 7.
	 *
	 * As cinco perguntas vêm do Figma, mas o accordion está fechado no desenho —
	 * as respostas abaixo são RASCUNHO redigido aqui e precisam de revisão do
	 * cliente antes de ir ao ar.
	 *
	 * @param int $post_id ID do post cli_solucao.
	 * @return void
	 */
	protected function preencher_varejo_faq( $post_id ) {
		$itens = array(
			array(
				'faq:vj-composable-commerce',
				'Por que a integração é essencial para uma estratégia de composable commerce?',
				'<p>Composable commerce troca a plataforma única por peças escolhidas a dedo — vitrine, carrinho, busca, pagamento, OMS — e é justamente isso que transfere o peso para a camada de integração. Sem ela, cada peça nova vira uma conexão ponto a ponto com todas as outras. Com uma camada de integração no meio, cada sistema conversa uma vez só com essa camada, e trocar um componente deixa de significar refazer a arquitetura inteira.</p>',
			),
			array(
				'faq:vj-experiencia-cliente',
				'Como a integração melhora a experiência do cliente?',
				'<p>A maior parte da fricção percebida pelo consumidor nasce de dado desencontrado: estoque que não bate entre loja e site, pedido que o atendimento não enxerga, promoção que só vale em um canal. Quando vendas, atendimento, estoque e logística compartilham a mesma informação atualizada, a jornada fica consistente em qualquer ponto de contato — e o atendimento passa a responder com o histórico completo em mãos.</p>',
			),
			array(
				'faq:vj-cadeia-suprimentos',
				'Como reduzir os impactos das incertezas na cadeia de suprimentos?',
				'<p>Reduzindo o tempo entre o que acontece na cadeia e o que a operação enxerga. Com fornecedores, ERP, WMS e transportadoras integrados, ruptura de estoque, atraso de fornecimento e mudança de prazo aparecem enquanto ainda dá para reagir — remanejar estoque entre lojas, acionar um fornecedor alternativo ou reprogramar a reposição — em vez de virarem surpresa no fechamento do mês.</p>',
			),
			array(
				'faq:vj-ultima-milha',
				'O CLI Connect ajuda na otimização da última milha?',
				'<p>Sim. A plataforma conecta o pedido aos sistemas de logística, transportadoras e roteirizadores, de modo que a decisão de origem da entrega, a escolha da transportadora e o roteiro considerem estoque real, prazo prometido e custo. O rastreamento volta pelo mesmo caminho e alimenta o acompanhamento do cliente e os indicadores da operação, sem planilha no meio.</p>',
			),
			array(
				'faq:vj-visao-360',
				'Quais os benefícios de construir uma visão 360º do cliente?',
				'<p>Reunir compras, atendimentos, devoluções e interações de marketing em um perfil único muda o que a operação consegue fazer: recomendação baseada em histórico real, campanhas que não repetem oferta de produto já comprado, atendimento que não pede a mesma informação duas vezes e uma leitura confiável de recorrência e valor do cliente ao longo do tempo.</p>',
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

		WP_CLI::log( sprintf( '  Varejo FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/**
	 * Preenche os campos ACF do post cli_solucao "Hotelaria e Turismo".
	 *
	 * Landing da indústria de hotelaria. Cresce uma seção por rodada, na ordem
	 * do Figma; as seções ainda não preenchidas ficam vazias e, portanto,
	 * invisíveis no front.
	 *
	 * @return void
	 */
	protected function preencher_solucao_hotelaria_e_turismo() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:hotelaria-e-turismo', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  Hotelaria e Turismo: post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'         => 'Para hotelaria',
			'solucao_hero_titulo'          => 'Conecte dados, propriedades e hóspedes',
			'solucao_hero_titulo_destaque' => 'em uma experiência integrada',
			'solucao_hero_corpo'           => 'Integre PMS, CRM, motores de reservas e sistemas operacionais para eliminar overbooking, personalizar o atendimento e acelerar a expansão da rede hoteleira.',
			'solucao_hero_btn1_texto'      => 'Agende uma demonstração',
			'solucao_hero_btn1_url'        => '/contato/',
			'solucao_hero_btn2_texto'      => 'Conheça a plataforma',
			'solucao_hero_btn2_url'        => '/plataforma/',
			'solucao_hero_imagem'          => $this->img( 'hotelaria-e-turismo-hero' ),

			// 2 · Métricas.
			'solucao_metrica_1_numero'     => '17.000+',
			'solucao_metrica_1_rotulo'     => 'hóspedes e residentes gerenciados por fluxos sincronizados',
			'solucao_metrica_2_numero'     => '100%',
			'solucao_metrica_2_rotulo'     => 'de automação alcançada no trabalho manual para alterações de reservas',
			'solucao_metrica_3_numero'     => '10x',
			'solucao_metrica_3_rotulo'     => 'mais rápido o tempo de lançamento de novos serviços',

			// 3 · Pilares.
			'solucao_pilares_eyebrow'      => 'Pilares',
			'solucao_pilares_titulo'       => 'Conecte toda a operação hoteleira em uma única plataforma',
			'solucao_pilares_1_icone'      => $this->img( 'hotelaria-e-turismo-pilar-1' ),
			'solucao_pilares_1_titulo'     => 'Sincronize inventários em tempo real',
			'solucao_pilares_1_desc'       => 'Mantenha disponibilidade de quartos atualizada entre canais para evitar overbooking e retrabalho.',
			'solucao_pilares_2_icone'      => $this->img( 'hotelaria-e-turismo-pilar-2' ),
			'solucao_pilares_2_titulo'     => 'Personalize a experiência do hóspede',
			'solucao_pilares_2_desc'       => 'Unifique perfis de hóspedes para oferecer atendimento personalizado utilizando inteligência artificial.',
			'solucao_pilares_3_icone'      => $this->img( 'hotelaria-e-turismo-pilar-3' ),
			'solucao_pilares_3_titulo'     => 'Expanda novas unidades rapidamente',
			'solucao_pilares_3_desc'       => 'Padronize integrações reutilizando componentes em novas propriedades e franquias da rede.',

			// 4 · Logos.
			'solucao_logos_texto'          => 'Integramos a hotelaria de grandes empresas.',
			'solucao_logos_clientes'       => array_values(
				array_filter(
					array(
						$this->id_do_seed( 'cliente:utrip', 'cli_cliente' ),
					)
				)
			),

			// 5 · Casos de Uso.
			'solucao_casos_eyebrow'        => 'Casos de uso',
			'solucao_casos_titulo'         => 'Automatize os principais processos da hotelaria',
			'solucao_casos_1_icone'        => $this->img( 'hotelaria-e-turismo-caso-1' ),
			'solucao_casos_1_titulo'       => 'Conecte PMS e CRM',
			'solucao_casos_1_desc'         => 'Sincronize reservas, preferências e histórico dos hóspedes entre sistemas automaticamente.',
			'solucao_casos_2_icone'        => $this->img( 'hotelaria-e-turismo-caso-2' ),
			'solucao_casos_2_titulo'       => 'Automatize programas de fidelidade',
			'solucao_casos_2_desc'         => 'Integre POS, reservas e loyalty para oferecer benefícios em todos os canais.',
			'solucao_casos_3_icone'        => $this->img( 'hotelaria-e-turismo-caso-3' ),
			'solucao_casos_3_titulo'       => 'Unifique relatórios das propriedades',
			'solucao_casos_3_desc'         => 'Centralize indicadores operacionais e financeiros de todas as unidades em um painel.',
			'solucao_casos_4_icone'        => $this->img( 'hotelaria-e-turismo-caso-4' ),
			'solucao_casos_4_titulo'       => 'Atualize preços dinamicamente',
			'solucao_casos_4_desc'         => 'Utilize dados de ocupação para automatizar estratégias de precificação em tempo real.',
			'solucao_casos_5_icone'        => $this->img( 'hotelaria-e-turismo-caso-5' ),
			'solucao_casos_5_titulo'       => 'Automatize a governança dos quartos',
			'solucao_casos_5_desc'         => 'Integre housekeeping, reservas e operação para agilizar liberações e limpeza dos apartamentos.',
			'solucao_casos_cta_texto'      => 'Agende uma demonstração',
			'solucao_casos_cta_url'        => '/contato/',

			// 6 · Selos.
			'solucao_selos_eyebrow'        => 'compliance & segurança',
			'solucao_selos_titulo'         => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'          => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_hotelaria_e_turismo_faq( $post_id );

		WP_CLI::log( "  Hotelaria e Turismo preenchido (ID: {$post_id})." );
	}

	/**
	 * Cria os posts cli_faq de Hotelaria e Turismo e vincula à solução.
	 *
	 * ATENÇÃO — texto provisório: o Figma mostra apenas as perguntas (o
	 * accordion está fechado no design). As respostas foram redigidas a partir
	 * do que a própria landing afirma nas seções anteriores e seguem pendentes
	 * de validação do cliente.
	 *
	 * @param int $post_id ID do post cli_solucao de Hotelaria e Turismo.
	 * @return void
	 */
	protected function preencher_hotelaria_e_turismo_faq( $post_id ) {
		$itens = array(
			array(
				'faq:ht-pms-legado',
				'É possível integrar PMS legados instalados localmente?',
				'<p>Sim. PMS antigos rodando no servidor da propriedade continuam sendo o caso mais comum na hotelaria, e não é preciso trocá-los para integrar. A conexão é feita por um agente instalado dentro da própria rede do hotel, que fala com o sistema pelo recurso que ele já oferece — banco de dados, arquivo, serviço web ou fila — e abre a comunicação de dentro para fora, sem expor portas de entrada no firewall. O PMS segue como está e passa a alimentar os demais sistemas.</p>',
			),
			array(
				'faq:ht-pos-fidelidade',
				'Como integrar sistemas de POS ao programa de fidelidade em tempo real?',
				'<p>Cada consumo registrado no POS — restaurante, bar, spa, frigobar — vira um evento que a plataforma envia na hora ao programa de fidelidade, já associado ao perfil do hóspede pela reserva ativa. O caminho de volta também é automático: saldo, categoria e benefícios voltam ao POS e ao PMS, de modo que o desconto ou a cortesia aparecem no mesmo atendimento, sem o operador consultar outro sistema.</p>',
			),
			array(
				'faq:ht-tempo-producao',
				'Quanto tempo leva para colocar uma integração em produção?',
				'<p>Depende muito menos do prazo de desenvolvimento do que do acesso aos sistemas. Fluxos que usam conectores já prontos e uma API documentada costumam entrar em semanas; o que estica o cronograma é liberação de credencial, homologação com o fornecedor do PMS e limpeza de cadastro. Como os componentes são reutilizáveis, a primeira integração é a mais demorada e as seguintes aproveitam o que já foi construído.</p>',
			),
			array(
				'faq:ht-alta-demanda',
				'A plataforma suporta grandes volumes de reservas em períodos de alta demanda?',
				'<p>Sim, e é justamente para o pico que a arquitetura foi pensada. O processamento é elástico e trabalha sobre filas, então feriado, alta temporada ou uma promoção relâmpago aumentam a fila sem derrubar o fluxo nem perder mensagem. Se um sistema de destino fica lento ou indisponível, as mensagens ficam retidas e são reprocessadas automaticamente quando ele volta, preservando a ordem dos eventos de cada reserva.</p>',
			),
			array(
				'faq:ht-franquias-padronizacao',
				'Como padronizar integrações entre franquias com sistemas diferentes?',
				'<p>A padronização acontece no meio do caminho, não nas pontas. Define-se um formato único para reserva, hóspede e consumo, e cada propriedade ganha apenas o trecho de tradução do seu sistema para esse formato — o restante do fluxo é o mesmo para toda a rede. Uma unidade nova entra reaproveitando o modelo, e quem opera a rede passa a enxergar todas as propriedades pelos mesmos indicadores, mesmo com PMS diferentes em cada uma.</p>',
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

		WP_CLI::log( sprintf( '  Hotelaria e Turismo FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/**
	 * Preenche os campos ACF do post cli_solucao "Marketing".
	 *
	 * @return void
	 */
	protected function preencher_solucao_marketing() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:marketing', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  Marketing: post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'     => 'Para o seu marketing',
			'solucao_hero_titulo'      => 'Conecte marketing, CRM e analytics em tempo real',
			'solucao_hero_corpo'       => 'Elimine filas de TI, sincronize informações em tempo real e entregue campanhas mais relevantes com automação inteligente entre todas as plataformas do seu ecossistema.',
			'solucao_hero_btn1_texto'  => 'Agende uma demonstração',
			'solucao_hero_btn1_url'    => '/contato/',
			'solucao_hero_btn2_texto'  => 'Conheça a plataforma',
			'solucao_hero_btn2_url'    => '/plataforma/',
			'solucao_hero_imagem'      => $this->img( 'marketing-hero' ),

			// 2 · Métricas.
			'solucao_metrica_1_numero' => '127%',
			'solucao_metrica_1_rotulo' => 'de crescimento em reconhecimento de marca',
			'solucao_metrica_2_numero' => '50%',
			'solucao_metrica_2_rotulo' => 'de aumento na geração de pipeline',
			'solucao_metrica_3_numero' => '22%',
			'solucao_metrica_3_rotulo' => 'de crescimento médio mensal no funil de vendas',

			// 3 · Pilares.
			'solucao_pilares_eyebrow'  => 'Pilares',
			'solucao_pilares_titulo'   => 'Marketing conectado do início ao fim',
			'solucao_pilares_1_icone'  => $this->img( 'marketing-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Leads sempre sincronizados',
			'solucao_pilares_1_desc'   => 'Mantenha CRM e plataforma de automação alinhados em tempo real para evitar contatos desatualizados e aumentar a eficiência das campanhas.',
			'solucao_pilares_2_icone'  => $this->img( 'marketing-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Escalabilidade sob demanda',
			'solucao_pilares_2_desc'   => 'Absorva grandes volumes de leads em lançamentos e campanhas sazonais sem comprometer desempenho ou exigir intervenção manual.',
			'solucao_pilares_3_icone'  => $this->img( 'marketing-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Personalização com governança',
			'solucao_pilares_3_desc'   => 'Utilize IA para enriquecer audiências e segmentações mantendo conformidade com LGPD, GDPR e políticas corporativas de dados.',

			// 4 · Logos.
			'solucao_logos_texto'      => 'Grandes empresas integram o seu Marketing com o CLI Connect',
			'solucao_logos_clientes'   => array_values(
				array_filter(
					array(
						$this->id_do_seed( 'cliente:unimed', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:cocamar', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:localiza', 'cli_cliente' ),
					)
				)
			),

			// 5 · Casos de Uso.
			'solucao_casos_eyebrow'    => 'Casos de uso',
			'solucao_casos_titulo'     => 'Automatize todo o ciclo das campanhas',
			'solucao_casos_1_icone'    => $this->img( 'marketing-caso-1' ),
			'solucao_casos_1_titulo'   => 'Sincronize leads em tempo real',
			'solucao_casos_1_desc'     => 'Envie novos leads entre plataformas de automação e CRM em segundos, mantendo equipes de marketing e vendas sempre alinhadas.',
			'solucao_casos_2_icone'    => $this->img( 'marketing-caso-2' ),
			'solucao_casos_2_titulo'   => 'Centralize atribuição de campanhas',
			'solucao_casos_2_desc'     => 'Conecte Google Ads, LinkedIn e plataformas de automação para consolidar resultados e atribuições em um único fluxo.',
			'solucao_casos_3_icone'    => $this->img( 'marketing-caso-3' ),
			'solucao_casos_3_titulo'   => 'Enriqueça leads com IA',
			'solucao_casos_3_desc'     => 'Dispare agentes de IA após o envio de formulários para pesquisar informações e qualificar contatos automaticamente.',
			'solucao_casos_4_icone'    => $this->img( 'marketing-caso-4' ),
			'solucao_casos_4_titulo'   => 'Orquestre audiências inteligentes',
			'solucao_casos_4_desc'     => 'Atualize segmentos automaticamente utilizando IA e dados de múltiplos sistemas para campanhas mais relevantes.',
			'solucao_casos_5_icone'    => $this->img( 'marketing-caso-5' ),
			'solucao_casos_5_titulo'   => 'Feche o ciclo de atribuição',
			'solucao_casos_5_desc'     => 'Conecte marketing, CRM e ERP para medir a contribuição das campanhas até a geração efetiva de receita.',
			'solucao_casos_6_icone'    => $this->img( 'marketing-caso-6' ),
			'solucao_casos_6_titulo'   => 'Automatize movimentações internas',
			'solucao_casos_6_desc'     => 'Atualize cargos, equipes e permissões sempre que houver mudanças.',

			// 6 · Diferencial Técnico.
			'solucao_dif_eyebrow'      => 'Diferencial técnico',
			'solucao_dif_titulo'       => 'Dados prontos para agir',
			'solucao_dif_corpo'        => 'Substitua sincronizações em lote por integrações em tempo real para acelerar campanhas, reduzir inconsistências e manter marketing e vendas trabalhando com os mesmos dados.',
			'solucao_dif_topico_1'     => 'Sincronize leads em menos de 60 segundos',
			'solucao_dif_topico_2'     => 'Elimine atrasos entre marketing e CRM',
			'solucao_dif_topico_3'     => 'Monitore integrações em tempo real',
			'solucao_dif_imagem'       => $this->img( 'marketing-dif' ),

			// 7 · Selos.
			'solucao_selos_eyebrow'    => 'compliance & segurança',
			'solucao_selos_titulo'     => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'      => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_marketing_faq( $post_id );

		WP_CLI::log( "  Marketing preenchido (ID: {$post_id})." );
	}

	/**
	 * Cria os posts cli_faq de Marketing e vincula à solução.
	 *
	 * ATENÇÃO — texto provisório: o Figma mostra apenas as perguntas (o
	 * accordion está fechado no design). As respostas foram redigidas a partir
	 * do que a própria landing afirma nas seções anteriores e seguem pendentes
	 * de validação do cliente.
	 *
	 * @param int $post_id ID do post cli_solucao de Marketing.
	 * @return void
	 */
	protected function preencher_marketing_faq( $post_id ) {
		$itens = array(
			array(
				'faq:mkt-velocidade-sincronizacao',
				'Qual é a velocidade da sincronização entre a plataforma de marketing e o CRM?',
				'<p>Os fluxos trabalham por evento, não por lote: assim que um lead é criado ou atualizado, a mensagem entra na fila e chega ao outro sistema em poucos segundos — a referência de projeto é manter o ciclo abaixo de um minuto. O que costuma pesar nesse tempo não é a plataforma de integração e sim os limites de API do sistema de destino, que são respeitados automaticamente para evitar bloqueio por excesso de chamadas.</p>',
			),
			array(
				'faq:mkt-marketing-operations',
				'O time de Marketing Operations consegue gerenciar as integrações sem depender da TI?',
				'<p>Em grande parte, sim. O desenho dos fluxos é low-code e os painéis de acompanhamento mostram volume, erros e reprocessamento sem exigir leitura de log técnico, então ajustes de mapeamento, campos e regras de segmentação ficam com o próprio time de marketing. A TI continua no circuito para o que é de sua alçada — liberar credenciais, aprovar acessos a sistemas internos e definir políticas de dados —, mas deixa de ser fila para cada mudança de campanha.</p>',
			),
			array(
				'faq:mkt-ipaas-vs-nativas',
				'Qual a diferença entre uma iPaaS e as integrações nativas das plataformas de automação de marketing?',
				'<p>As integrações nativas resolvem bem o par de sistemas para o qual foram feitas, com o mapeamento que o fornecedor decidiu oferecer. Uma iPaaS trabalha no meio do caminho: conecta marketing, CRM, ERP, mídia paga e analytics com a mesma lógica, permite transformar e enriquecer o dado em trânsito, aplica regras próprias de deduplicação e deixa todo o histórico auditável em um só lugar. Na prática, a nativa é suficiente enquanto o ecossistema é pequeno; a iPaaS é o que sustenta o crescimento sem multiplicar conexões ponto a ponto.</p>',
			),
			array(
				'faq:mkt-criterios-plataforma',
				'Quais critérios devo avaliar ao escolher uma plataforma de integração para Marketing?',
				'<p>Vale olhar cinco pontos: a cobertura de conectores para as ferramentas que já estão em uso; a capacidade de processar picos de lançamento e campanhas sazonais sem perder mensagem; a autonomia que o time de marketing ganha para operar sem abrir chamado; a visibilidade sobre erros e reprocessamento; e o tratamento de dados pessoais, incluindo por onde eles trafegam e onde ficam armazenados. O custo total também conta — além da licença, considere quem vai operar e monitorar a plataforma no dia a dia.</p>',
			),
			array(
				'faq:mkt-lgpd-gdpr',
				'Como a plataforma garante conformidade com LGPD e GDPR durante o trânsito dos dados?',
				'<p>Os dados trafegam criptografados de ponta a ponta e a conexão com sistemas internos é feita por um agente que se comunica de dentro para fora, sem expor portas de entrada no firewall. Cada fluxo registra quem acessou o quê e quando, o que sustenta pedidos de auditoria e de exclusão previstos nas duas leis. Campos sensíveis podem ser mascarados ou simplesmente não transitar, e as regras de consentimento e opt-out são aplicadas no próprio fluxo, de modo que um contato que revogou a permissão deixa de ser distribuído para as demais plataformas.</p>',
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

		WP_CLI::log( sprintf( '  Marketing FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/**
	 * Preenche os campos ACF do post cli_solucao "Operações de Receita (RevOps)".
	 *
	 * Landing do departamento de RevOps. O design não traz a faixa de métricas
	 * nem as seções de Plataforma e Aceleradores: esses campos ficam vazios e,
	 * portanto, invisíveis no front.
	 *
	 * @return void
	 */
	protected function preencher_solucao_operacoes_de_receita_revops() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:operacoes-de-receita-revops', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  Operações de Receita (RevOps): post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'     => 'Para suas operações de receita',
			'solucao_hero_titulo'      => 'Conecte toda a operação de receita.',
			'solucao_hero_corpo'       => 'Sincronize CRM, marketing e customer success em tempo real para eliminar gargalos, acelerar handoffs e manter todo o funil sempre atualizado.',
			'solucao_hero_btn1_texto'  => 'Agende uma demonstração',
			'solucao_hero_btn1_url'    => '/contato/',
			'solucao_hero_btn2_texto'  => 'Conheça a plataforma',
			'solucao_hero_btn2_url'    => '/plataforma/',
			'solucao_hero_imagem'      => $this->img( 'operacoes-de-receita-revops-hero' ),

			// 3 · Pilares.
			'solucao_pilares_eyebrow'  => 'Pilares',
			'solucao_pilares_titulo'   => 'Uma operação de receita conectada',
			'solucao_pilares_1_icone'  => $this->img( 'operacoes-de-receita-revops-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Unifique dados de receita',
			'solucao_pilares_1_desc'   => 'Conecte marketing, vendas e customer success para priorizar oportunidades com informações consistentes em todo o ciclo comercial.',
			'solucao_pilares_2_icone'  => $this->img( 'operacoes-de-receita-revops-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Automatize os handoffs',
			'solucao_pilares_2_desc'   => 'Transfira clientes entre vendas e customer success automaticamente, reduzindo atrasos e eliminando processos manuais.',
			'solucao_pilares_3_icone'  => $this->img( 'operacoes-de-receita-revops-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Mantenha o pipeline limpo',
			'solucao_pilares_3_desc'   => 'Atualize registros continuamente para evitar duplicidades, inconsistências e decisões baseadas em informações desatualizadas.',

			// 4 · Logos.
			'solucao_logos_texto'      => 'Integramos as principais plataformas de CRM, marketing, vendas e customer success utilizadas por grandes empresas.',
			'solucao_logos_clientes'   => array_values(
				array_filter(
					array(
						$this->id_do_seed( 'cliente:unimed', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:cocamar', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:localiza', 'cli_cliente' ),
					)
				)
			),

			// 5 · Casos de Uso.
			'solucao_casos_eyebrow'    => 'Casos de uso',
			'solucao_casos_titulo'     => 'Automatize todo o fluxo de receita',
			'solucao_casos_1_icone'    => $this->img( 'operacoes-de-receita-revops-caso-1' ),
			'solucao_casos_1_titulo'   => 'Priorize leads automaticamente',
			'solucao_casos_1_desc'     => 'Combine dados de CRM, automação de marketing e enriquecimento para qualificar oportunidades com mais precisão.',
			'solucao_casos_2_icone'    => $this->img( 'operacoes-de-receita-revops-caso-2' ),
			'solucao_casos_2_titulo'   => 'Unifique múltiplos CRMs',
			'solucao_casos_2_desc'     => 'Consolide informações comerciais de diferentes CRMs para obter uma visão única do pipeline.',
			'solucao_casos_3_icone'    => $this->img( 'operacoes-de-receita-revops-caso-3' ),
			'solucao_casos_3_titulo'   => 'Ative o pós-venda',
			'solucao_casos_3_desc'     => 'Dispare automaticamente processos de customer success quando uma oportunidade for ganha e preserve todo o contexto da venda.',
			'solucao_casos_4_icone'    => $this->img( 'operacoes-de-receita-revops-caso-4' ),
			'solucao_casos_4_titulo'   => 'Corrija dados comerciais',
			'solucao_casos_4_desc'     => 'Identifique e atualize registros inconsistentes para manter oportunidades, contatos e previsões comerciais confiáveis.',
			'solucao_casos_5_icone'    => $this->img( 'operacoes-de-receita-revops-caso-5' ),
			'solucao_casos_5_titulo'   => 'Monitore a saúde dos clientes',
			'solucao_casos_5_desc'     => 'Combine dados de produto, suporte e NPS para identificar riscos e oportunidades de expansão.',
			'solucao_casos_6_icone'    => $this->img( 'operacoes-de-receita-revops-caso-6' ),
			'solucao_casos_6_titulo'   => 'Automatize movimentações internas',
			'solucao_casos_6_desc'     => 'Atualize cargos, equipes e permissões sempre que houver mudanças.',

			// 6 · Selos.
			'solucao_selos_eyebrow'    => 'compliance & segurança',
			'solucao_selos_titulo'     => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'      => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',

			// 7 · Diferencial.
			'solucao_dif_eyebrow'      => 'Diferencial técnico',
			'solucao_dif_titulo'       => 'Mais autonomia para RevOps',
			'solucao_dif_corpo'        => 'Permita que a equipe de RevOps crie, ajuste e monitore integrações utilizando um builder visual com IA, sem depender de desenvolvimento dedicado.',
			'solucao_dif_topico_1'     => 'Crie integrações com builder visual',
			'solucao_dif_topico_2'     => 'Automatize fluxos com apoio de IA',
			'solucao_dif_topico_3'     => 'Reduza a dependência da equipe de TI',
			'solucao_dif_imagem'       => $this->img( 'operacoes-de-receita-revops-dif' ),
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_operacoes_de_receita_revops_faq( $post_id );

		WP_CLI::log( "  Operações de Receita (RevOps) preenchido (ID: {$post_id})." );
	}

	/**
	 * Cria os posts cli_faq de Operações de Receita (RevOps) e vincula à solução.
	 *
	 * ATENÇÃO — texto provisório: o Figma mostra apenas as perguntas (o
	 * accordion está fechado no design). As respostas foram redigidas a partir
	 * do que a própria landing afirma nas seções anteriores e seguem pendentes
	 * de validação do cliente.
	 *
	 * @param int $post_id ID do post cli_solucao de Operações de Receita (RevOps).
	 * @return void
	 */
	protected function preencher_operacoes_de_receita_revops_faq( $post_id ) {
		$itens = array(
			array(
				'faq:revops-crm-automacao-prazo',
				'Quanto tempo leva para conectar CRM e plataforma de automação de marketing?',
				'<p>O desenvolvimento costuma ser a parte curta: CRM e ferramentas de automação de marketing têm APIs bem documentadas e conectores prontos, então um fluxo de leads e oportunidades entra no ar em poucas semanas. O que estica o cronograma é a decisão de negócio — definir qual sistema manda em cada campo, o que caracteriza um lead qualificado e como tratar a base duplicada que já existe. Vale começar por um fluxo só, colocá-lo em produção e ampliar a partir dele.</p>',
			),
			array(
				'faq:revops-sem-desenvolvedor',
				'O time de RevOps consegue criar integrações sem desenvolvedores dedicados?',
				'<p>Sim, para a maior parte do dia a dia. O builder visual monta o fluxo arrastando e conectando etapas, com apoio de IA na hora de mapear campos e sugerir tratamentos, e quem conhece o processo comercial consegue criar, ajustar e monitorar as automações sem escrever código. A TI continua entrando onde faz sentido — liberação de credenciais, revisão de fluxos críticos e casos que exigem lógica mais elaborada —, mas deixa de ser gargalo para cada pequeno ajuste.</p>',
			),
			array(
				'faq:revops-ponto-a-ponto-ipaas',
				'Qual a diferença entre uma integração ponto a ponto e uma iPaaS?',
				'<p>Uma integração ponto a ponto liga dois sistemas diretamente e resolve bem enquanto são dois. O problema aparece na escala: cada nova ferramenta multiplica as conexões, cada uma com sua própria lógica e seu próprio tratamento de erro, e ninguém enxerga o conjunto. Uma iPaaS coloca uma camada no meio — os sistemas conversam com ela, não entre si. Isso centraliza o monitoramento, reaproveita mapeamentos e faz com que trocar uma ferramenta signifique refazer um trecho, não a teia inteira.</p>',
			),
			array(
				'faq:revops-mudanca-api',
				'Como mudanças nas APIs dos sistemas impactam as integrações?',
				'<p>Mudanças de versão são esperadas e tratadas na camada de integração, não em cada fluxo. Como o mapeamento entre o formato de cada sistema e o formato interno fica isolado, uma alteração de API costuma exigir ajuste em um ponto só, sem tocar nos fluxos que dependem dele. O monitoramento acusa a falha assim que ela acontece, as mensagens afetadas ficam retidas em fila e são reprocessadas depois da correção, sem perda de registro.</p>',
			),
			array(
				'faq:revops-protecao-dados',
				'Como os dados comerciais são protegidos durante as integrações?',
				'<p>O tráfego é criptografado ponta a ponta e as credenciais de cada sistema ficam em cofre, nunca dentro do fluxo. O acesso é concedido por perfil, de modo que quem opera as automações não precisa enxergar o conteúdo sensível que passa por elas, e todo movimento fica registrado em trilha de auditoria — quem alterou o quê, quando e com qual resultado. A operação segue os padrões de compliance e segurança listados nesta página.</p>',
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

		WP_CLI::log( sprintf( '  Operações de Receita (RevOps) FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/**
	 * Preenche os campos ACF do post cli_solucao "Financeiro" (Departamento).
	 *
	 * Landing do departamento financeiro. O design inverte Diferencial e Selos
	 * em relação à ordem padrão — daí `solucao_dif_antes_selos`.
	 *
	 * @return void
	 */
	protected function preencher_solucao_financeiro() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:financeiro', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  Financeiro: post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'      => 'para o seu financeiro',
			'solucao_hero_titulo'       => 'Conecte todo o ecossistema financeiro.',
			'solucao_hero_corpo'        => 'Integre ERPs, bancos e plataformas de planejamento para acelerar fechamentos, automatizar auditorias e manter todas as unidades de negócio sincronizadas.',
			'solucao_hero_btn1_texto'   => 'Agende uma demonstração',
			'solucao_hero_btn1_url'     => '/contato/',
			'solucao_hero_btn2_texto'   => 'Conheça a plataforma',
			'solucao_hero_btn2_url'     => '/plataforma/',
			'solucao_hero_imagem'       => $this->img( 'financeiro-hero' ),

			// 2 · Métricas.
			'solucao_metrica_1_numero'  => '7 dias',
			'solucao_metrica_1_rotulo'  => 'de tempo do fechamento contábil mensal',
			'solucao_metrica_2_numero'  => '5x',
			'solucao_metrica_2_rotulo'  => 'de aumento no processamento de pedidos',
			'solucao_metrica_3_numero'  => '50%',
			'solucao_metrica_3_rotulo'  => 'redução do tempo de fechamento mensal',

			// 3 · Pilares.
			'solucao_pilares_eyebrow'   => 'Pilares',
			'solucao_pilares_titulo'    => 'Mais controle para a operação financeira',
			'solucao_pilares_1_icone'   => $this->img( 'financeiro-pilar-1' ),
			'solucao_pilares_1_titulo'  => 'Acelere o fechamento contábil',
			'solucao_pilares_1_desc'    => 'Sincronize informações entre ERPs e sistemas financeiros para reduzir atividades manuais e concluir o fechamento com mais rapidez.',
			'solucao_pilares_2_icone'   => $this->img( 'financeiro-pilar-2' ),
			'solucao_pilares_2_titulo'  => 'Automatize a auditoria',
			'solucao_pilares_2_desc'    => 'Registre todas as movimentações com rastreabilidade completa para simplificar auditorias e aumentar a confiabilidade dos processos.',
			'solucao_pilares_3_icone'   => $this->img( 'financeiro-pilar-3' ),
			'solucao_pilares_3_titulo'  => 'Unifique os seus ERPs',
			'solucao_pilares_3_desc'    => 'Mantenha dados financeiros consistentes entre diferentes unidades de negócio, filiais e sistemas corporativos.',

			// 4 · Logos.
			'solucao_logos_texto'       => 'Integramos os principais ERPs, bancos e plataformas financeiras utilizados por grandes empresas.',
			'solucao_logos_clientes'    => array_values(
				array_filter(
					array(
						$this->id_do_seed( 'cliente:unidas', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:seculus', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:localiza', 'cli_cliente' ),
					)
				)
			),

			// 5 · Casos de Uso.
			'solucao_casos_eyebrow'     => 'Casos de uso',
			'solucao_casos_titulo'      => 'Automatize os principais processos financeiros',
			'solucao_casos_1_icone'     => $this->img( 'financeiro-caso-1' ),
			'solucao_casos_1_titulo'    => 'Consolide dados contábeis',
			'solucao_casos_1_desc'      => 'Sincronize informações entre diferentes ERPs para consolidar balancetes e obter uma visão financeira unificada.',
			'solucao_casos_2_icone'     => $this->img( 'financeiro-caso-2' ),
			'solucao_casos_2_titulo'    => 'Automatize conciliações bancárias',
			'solucao_casos_2_desc'      => 'Integre bancos via host-to-host para realizar conciliações diárias com mais agilidade e menos intervenção manual.',
			'solucao_casos_3_icone'     => $this->img( 'financeiro-caso-3' ),
			'solucao_casos_3_titulo'    => 'Otimize contas a pagar',
			'solucao_casos_3_desc'      => 'Conecte plataformas de compras e ERP para automatizar o matching de três vias e reduzir retrabalho operacional.',
			'solucao_casos_4_icone'     => $this->img( 'financeiro-caso-4' ),
			'solucao_casos_4_titulo'    => 'Reconheça receitas automaticamente',
			'solucao_casos_4_desc'      => 'Envie vendas aprovadas para o ERP em tempo real e acelere os processos de contabilização da receita.',
			'solucao_casos_5_icone'     => $this->img( 'financeiro-caso-5' ),
			'solucao_casos_5_titulo'    => 'Alimente o planejamento financeiro',
			'solucao_casos_5_desc'      => 'Atualize plataformas de FP&A automaticamente com dados do ERP para melhorar previsões e análises financeiras.',
			'solucao_casos_6_icone'     => $this->img( 'financeiro-caso-6' ),
			'solucao_casos_6_titulo'    => 'Automatize movimentações internas',
			'solucao_casos_6_desc'      => 'Atualize cargos, equipes e permissões sempre que houver mudanças.',

			// 6 · Diferencial (antes dos Selos neste design).
			'solucao_dif_eyebrow'       => 'diferencial técnico',
			'solucao_dif_titulo'        => 'Integrações sob seu controle',
			'solucao_dif_corpo'         => 'Execute integrações dentro da infraestrutura da sua empresa para garantir soberania dos dados, maior controle operacional e conformidade com políticas corporativas.',
			'solucao_dif_topico_1'      => 'Execute integrações na sua própria nuvem',
			'solucao_dif_topico_2'      => 'Mantenha dados sob governança corporativa',
			'solucao_dif_topico_3'      => 'Reduza riscos de conformidade financeira',
			'solucao_dif_imagem'        => $this->img( 'financeiro-diferencial' ),
			'solucao_dif_antes_selos'   => 1,

			// 7 · Selos.
			'solucao_selos_eyebrow'     => 'compliance & segurança',
			'solucao_selos_titulo'      => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'       => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_financeiro_faq( $post_id );

		WP_CLI::log( "  Financeiro preenchido (ID: {$post_id})." );
	}

	/**
	 * Cria os posts cli_faq do Financeiro e vincula à solução.
	 *
	 * ATENÇÃO — texto provisório: o Figma mostra apenas as perguntas (o
	 * accordion está fechado no design). As respostas foram redigidas a partir
	 * do que a própria landing afirma nas seções anteriores e seguem pendentes
	 * de validação do cliente.
	 *
	 * @param int $post_id ID do post cli_solucao do Financeiro.
	 * @return void
	 */
	protected function preencher_financeiro_faq( $post_id ) {
		$itens = array(
			array(
				'faq:fin-tempo-erp',
				'Quanto tempo leva para integrar SAP, Oracle ou NetSuite?',
				'<p>Os três já contam com conectores prontos, então o cronograma depende menos do desenvolvimento e mais do acesso ao ambiente. Fluxos comuns do financeiro — balancete, lançamentos contábeis, contas a pagar — costumam entrar em semanas, contadas a partir da liberação de credencial e do aceite do desenho pelo time contábil. O que estica o prazo é customização pesada no ERP e divergência de plano de contas entre unidades, não a conexão em si.</p>',
			),
			array(
				'faq:fin-autonomia-financeiro',
				'O time financeiro consegue acompanhar as integrações sem depender da TI?',
				'<p>Sim. O acompanhamento do dia a dia — se o lote da noite rodou, quantos lançamentos entraram, qual registro falhou e por quê — fica em um painel de operação que a área financeira acessa direto, com reprocessamento do que deu erro sem abrir chamado. O que continua com a TI é a mudança estrutural: criar um fluxo novo, alterar credencial ou mexer em regra de negócio.</p>',
			),
			array(
				'faq:fin-nativa-vs-ipaas',
				'Qual a diferença entre integrações nativas do ERP e uma iPaaS?',
				'<p>A integração nativa resolve bem o par de sistemas para o qual foi feita, mas cada nova ponta vira um projeto isolado, com sua própria regra, seu próprio log e sua própria manutenção. A iPaaS coloca uma camada única entre todos os sistemas: as regras de transformação, o histórico de execução, o tratamento de erro e a governança de acesso ficam em um só lugar, e uma unidade de negócio nova reaproveita o que já foi construído em vez de recomeçar.</p>',
			),
			array(
				'faq:fin-criterios-plataforma',
				'Quais critérios devo avaliar ao escolher uma plataforma de integração para Finanças?',
				'<p>Comece pela rastreabilidade: toda movimentação precisa ter registro completo do que entrou, do que saiu e de quem alterou, porque é isso que sustenta a auditoria. Depois verifique o catálogo de conectores para os ERPs e bancos que você já usa, o comportamento em caso de falha (reprocessamento sem duplicar lançamento), o controle de acessos por perfil e onde a execução acontece — dentro da sua infraestrutura, quando a política corporativa exigir. Por último, avalie o modelo de evolução: integração financeira muda o tempo todo, e depender de um novo projeto a cada ajuste sai caro.</p>',
			),
			array(
				'faq:fin-atualizacao-apis',
				'Como atualizações de APIs dos ERPs impactam as integrações?',
				'<p>O impacto fica contido na camada de conexão. Quando o fornecedor publica uma versão nova, é o conector que é atualizado — os fluxos, as regras e os destinos seguem como estão. Mudanças anunciadas com antecedência são homologadas em ambiente separado antes de entrar em produção; quando algo quebra sem aviso, as mensagens ficam retidas e são reprocessadas depois da correção, sem perda de lançamento nem duplicidade.</p>',
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

		WP_CLI::log( sprintf( '  Financeiro FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}


	/**
	 * Preenche os campos ACF do post cli_solucao "Pedido ao Recebimento".
	 *
	 * Landing "Iniciativa — Pedido ao Recebimento (O2C)". Todos os textos vêm
	 * dos frames do Figma (arquivo "CLI Connect (Copy)", nó 16678:117015 e
	 * seguintes); a única exceção são as respostas do FAQ, redigidas aqui —
	 * ver preencher_pedido_ao_recebimento_faq().
	 *
	 * @return void
	 */
	protected function preencher_solucao_pedido_ao_recebimento() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:pedido-ao-recebimento', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  Pedido ao Recebimento: post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'         => 'do pedido ao recebimento (o2c)',
			'solucao_hero_titulo'          => 'Conecte vendas, faturamento e recebimento em',
			'solucao_hero_titulo_destaque' => 'um único fluxo',
			'solucao_hero_titulo_fluido'   => true,
			'solucao_hero_corpo'           => 'Acelere o ciclo completo de receita conectando CRM, ERP, bancos e sistemas de pagamento em uma operação integrada, rastreável e sem etapas manuais.',
			'solucao_hero_btn1_texto'      => 'Agende uma demonstração',
			'solucao_hero_btn1_url'        => '/contato/',
			'solucao_hero_imagem'          => $this->img( 'pedido-ao-recebimento-hero' ),

			// 2 · Métricas.
			'solucao_metrica_1_numero'     => '7 dias',
			'solucao_metrica_1_rotulo'     => 'mais rápido no seu fechamento financeiro',
			'solucao_metrica_2_numero'     => '95%',
			'solucao_metrica_2_rotulo'     => 'mais rapidez na criação de pedidos',
			'solucao_metrica_3_numero'     => '6.000',
			'solucao_metrica_3_rotulo'     => 'horas de trabalho manual economizadas anualmente',

			// 3 · Pilares.
			'solucao_pilares_eyebrow'      => 'pilares',
			'solucao_pilares_titulo'       => 'Reconstrua sua arquitetura, não o seu negócio.',
			'solucao_pilares_1_icone'      => $this->img( 'pedido-ao-recebimento-pilar-1' ),
			'solucao_pilares_1_titulo'     => 'Elimine retrabalho operacional',
			'solucao_pilares_1_desc'       => 'Automatize a troca de dados entre pedido, faturamento e cobrança, eliminando lançamentos manuais.',
			'solucao_pilares_2_icone'      => $this->img( 'pedido-ao-recebimento-pilar-2' ),
			'solucao_pilares_2_titulo'     => 'Receba mais rápido',
			'solucao_pilares_2_desc'       => 'Reduza o tempo entre o fechamento da venda, a emissão da cobrança e o reconhecimento do caixa.',
			'solucao_pilares_3_icone'      => $this->img( 'pedido-ao-recebimento-pilar-3' ),
			'solucao_pilares_3_titulo'     => 'Tenha visibilidade completa',
			'solucao_pilares_3_desc'       => 'Acompanhe cada etapa do pedido ao recebimento com dados consistentes entre todas as áreas.',

			// 4 · Casos de Uso.
			'solucao_casos_eyebrow'        => 'casos de uso',
			'solucao_casos_titulo'         => 'Integrações mais rápidas, seguras e inteligentes',
			'solucao_casos_1_icone'        => $this->img( 'pedido-ao-recebimento-caso-1' ),
			'solucao_casos_1_titulo'       => 'Fature automaticamente',
			'solucao_casos_1_desc'         => 'Converta pedidos fechados no CRM em faturamento e emissão de nota fiscal no ERP.',
			'solucao_casos_2_icone'        => $this->img( 'pedido-ao-recebimento-caso-2' ),
			'solucao_casos_2_titulo'       => 'Concilie recebimentos',
			'solucao_casos_2_desc'         => 'Compare automaticamente pagamentos recebidos com bancos e adquirentes.',
			'solucao_casos_3_icone'        => $this->img( 'pedido-ao-recebimento-caso-3' ),
			'solucao_casos_3_titulo'       => 'Avise sobre inadimplência',
			'solucao_casos_3_desc'         => 'Dispare alertas automáticos ao time comercial sempre que houver atrasos de pagamento.',
			'solucao_casos_4_icone'        => $this->img( 'pedido-ao-recebimento-caso-4' ),
			'solucao_casos_4_titulo'       => 'Monitore o DSO',
			'solucao_casos_4_desc'         => 'Consolide indicadores de prazo médio de recebimento em um único painel de gestão.',
			'solucao_casos_5_icone'        => $this->img( 'pedido-ao-recebimento-caso-5' ),
			'solucao_casos_5_titulo'       => 'Sincronize a operação',
			'solucao_casos_5_desc'         => 'Compartilhe o status dos pedidos entre vendas, financeiro e logística em tempo real.',
			'solucao_casos_6_icone'        => $this->img( 'pedido-ao-recebimento-caso-6' ),
			'solucao_casos_6_titulo'       => 'Atualize dados continuamente',
			'solucao_casos_6_desc'         => 'Propague alterações entre CRM, ERP e sistemas financeiros sem intervenções manuais.',

			// 5 · Diferencial (antes dos Selos neste design).
			'solucao_dif_eyebrow'          => 'diferencial técnico',
			'solucao_dif_titulo'           => 'Garanta rastreabilidade completa em todo o ciclo financeiro',
			'solucao_dif_corpo'            => 'Proteja as informações financeiras e acompanhe cada etapa do pedido ao recebimento com total transparência e governança.',
			'solucao_dif_topico_1'         => 'Auditoria completa dos processos',
			'solucao_dif_topico_2'         => 'Dados protegidos ponta a ponta',
			'solucao_dif_topico_3'         => 'Histórico detalhado das transações',
			'solucao_dif_imagem'           => $this->img( 'pedido-ao-recebimento-diferencial' ),
			'solucao_dif_antes_selos'      => 1,

			// 6 · Aceleradores.
			'solucao_acel_eyebrow'         => 'aceleradores de integração',
			'solucao_acel_titulo'          => 'Modelo pronto para começar',
			'solucao_acel_corpo'           => 'Comece rapidamente com um fluxo pré-configurado que conecta pedido, faturamento, cobrança e conciliação financeira.',
			'solucao_acel_topico_1'        => 'Gere faturamentos automaticamente',
			'solucao_acel_topico_2'        => 'Emita cobranças integradas',
			'solucao_acel_topico_3'        => 'Concilie recebimentos com bancos',
			'solucao_acel_topico_4'        => 'E muito mais...',
			'solucao_acel_btn_texto'       => 'Começar agora',
			'solucao_acel_btn_url'         => '/contato/',
			'solucao_acel_imagem'          => $this->img( 'pedido-ao-recebimento-aceleradores' ),

			// 7 · Selos.
			'solucao_selos_eyebrow'        => 'compliance & segurança',
			'solucao_selos_titulo'         => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'          => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_pedido_ao_recebimento_faq( $post_id );

		WP_CLI::log( "  Pedido ao Recebimento preenchido (ID: {$post_id})." );
	}

	/**
	 * Cria os posts cli_faq de Pedido ao Recebimento e vincula à solução.
	 *
	 * ATENÇÃO — texto provisório: o Figma mostra apenas as perguntas (o
	 * accordion está fechado no design). As respostas foram redigidas a partir
	 * do que a própria landing afirma nas seções anteriores e seguem pendentes
	 * de validação do cliente.
	 *
	 * @param int $post_id ID do post cli_solucao de Pedido ao Recebimento.
	 * @return void
	 */
	protected function preencher_pedido_ao_recebimento_faq( $post_id ) {
		$itens = array(
			array(
				'faq:o2c-tempo-venda-recebimento',
				'Como reduzir o tempo entre o fechamento da venda e o recebimento?',
				'<p>O que costuma alongar o ciclo não é a venda nem a cobrança em si, mas a espera entre elas: o pedido fechado no CRM que só vira faturamento quando alguém redigita, a nota emitida que só gera cobrança no dia seguinte. Conectando CRM, ERP e sistema de cobrança em um fluxo único, cada etapa dispara a próxima assim que a anterior termina, sem lote noturno e sem digitação. O ganho aparece antes na consistência dos dados — pedido, nota e título com os mesmos valores — e só depois no prazo médio.</p>',
			),
			array(
				'faq:o2c-multiplos-erps-crms',
				'É possível conectar múltiplos ERPs e CRMs no mesmo fluxo Order-to-Cash?',
				'<p>Sim, e é o cenário mais comum em empresas com várias unidades de negócio ou que passaram por aquisições. As regras de transformação ficam na camada de integração, não dentro de cada sistema, então um ERP a mais entra como uma ponta nova no fluxo que já existe, reaproveitando o desenho de pedido, faturamento e conciliação. O trabalho real está em conciliar os cadastros — cliente, produto, condição de pagamento — que costumam divergir entre as bases.</p>',
			),
			array(
				'faq:o2c-conciliacao-bancos',
				'Como funciona a conciliação automática com bancos e adquirentes?',
				'<p>A integração recebe os arquivos de retorno e extratos das instituições, casa cada pagamento com o título correspondente no ERP e baixa o que fechou. O que não casa — valor divergente, pagamento parcial, taxa de adquirente descontada — fica separado em uma fila de exceção com o motivo, para o financeiro tratar caso a caso em vez de conferir tudo à mão. O histórico de cada tentativa fica registrado, o que sustenta a auditoria e permite reprocessar sem duplicar baixa.</p>',
			),
			array(
				'faq:o2c-status-do-pedido',
				'Como acompanhar o status de um pedido do início ao fim?',
				'<p>Como o fluxo passa por uma camada única, cada pedido carrega um identificador que atravessa CRM, ERP, faturamento e cobrança. Isso permite montar uma visão de ponta a ponta — em que etapa o pedido está, quando entrou nela, o que falhou e o que foi reprocessado — sem consultar sistema por sistema. Vendas, financeiro e logística passam a olhar o mesmo status, o que resolve boa parte das divergências entre áreas antes que virem retrabalho.</p>',
			),
			array(
				'faq:o2c-sistemas-do-fluxo',
				'Quais sistemas podem fazer parte do fluxo Order-to-Cash?',
				'<p>Tipicamente o CRM onde o pedido é fechado, o ERP que fatura e emite a nota fiscal, a plataforma de cobrança ou meio de pagamento, os bancos e adquirentes que confirmam o recebimento, e as ferramentas de gestão que consomem os indicadores de prazo médio. Também entram no fluxo sistemas de logística, quando a entrega condiciona o faturamento, e plataformas de crédito e cobrança, quando há análise de limite ou régua de inadimplência. A escolha depende menos do catálogo de conectores e mais de onde estão hoje as etapas manuais.</p>',
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

		WP_CLI::log( sprintf( '  Pedido ao Recebimento FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/**
	 * Preenche os campos ACF do post cli_solucao "Atualização de Sistemas
	 * Legados" (Por Iniciativa — Legacy Modernization).
	 *
	 * O design cobre seis seções: Hero, Pilares, Casos de Uso, Diferencial
	 * Técnico, Selos e FAQ. Métricas, Logos, Plataforma e Aceleradores ficam
	 * vazios de propósito e os template-parts os omitem. Como no Financeiro,
	 * o Diferencial vem antes dos Selos — daí `solucao_dif_antes_selos`.
	 *
	 * @return void
	 */
	protected function preencher_solucao_atualizacao_de_sistemas_legados() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:atualizacao-de-sistemas-legados', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  Atualização de Sistemas Legados: post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero (o segundo botão está oculto no Figma).
			'solucao_hero_eyebrow'         => 'substituição do sistema legado',
			'solucao_hero_titulo'          => 'Reconstrua sua arquitetura.',
			'solucao_hero_titulo_destaque' => 'Preserve sua operação.',
			'solucao_hero_corpo'           => 'Substitua plataformas legadas como TIBCO e IBM MQ por uma arquitetura moderna de integração, preservando seus sistemas existentes e mantendo a operação funcionando durante toda a transição.',
			'solucao_hero_btn1_texto'      => 'Agende uma demonstração',
			'solucao_hero_btn1_url'        => '/contato/',
			'solucao_hero_btn2_texto'      => '',
			'solucao_hero_btn2_url'        => '',
			'solucao_hero_imagem'          => $this->img( 'atualizacao-de-sistemas-legados-hero' ),

			// 3 · Pilares.
			'solucao_pilares_eyebrow'      => 'pilares',
			'solucao_pilares_titulo'       => 'Reconstrua sua camada de integração sem reconstruir seus sistemas',
			'solucao_pilares_1_icone'      => $this->img( 'atualizacao-de-sistemas-legados-pilar-1' ),
			'solucao_pilares_1_titulo'     => 'Democratize a integração',
			'solucao_pilares_1_desc'       => 'Reduza a dependência de especialistas em tecnologias legadas com uma plataforma visual, mais simples de evoluir e manter.',
			'solucao_pilares_2_icone'      => $this->img( 'atualizacao-de-sistemas-legados-pilar-2' ),
			'solucao_pilares_2_titulo'     => 'Construa sobre padrões abertos',
			'solucao_pilares_2_desc'       => 'Desenvolva integrações utilizando padrões modernos e portáveis, evitando criar uma nova dependência tecnológica.',
			'solucao_pilares_3_icone'      => $this->img( 'atualizacao-de-sistemas-legados-pilar-3' ),
			'solucao_pilares_3_titulo'     => 'Evolua para eventos em tempo real',
			'solucao_pilares_3_desc'       => 'Substitua processos em lote por uma arquitetura orientada a eventos, preparada para aplicações modernas e integrações distribuídas.',

			// 5 · Casos de Uso (cinco cards + o card azul de CTA).
			'solucao_casos_eyebrow'        => 'casos de uso',
			'solucao_casos_titulo'         => 'Integrações mais rápidas, seguras e inteligentes',
			'solucao_casos_1_icone'        => $this->img( 'atualizacao-de-sistemas-legados-caso-1' ),
			'solucao_casos_1_titulo'       => 'Reconstrua rotas do TIBCO BusinessWorks',
			'solucao_casos_1_desc'         => 'Transforme integrações existentes em fluxos visuais mais simples de manter e evoluir.',
			'solucao_casos_2_icone'        => $this->img( 'atualizacao-de-sistemas-legados-caso-2' ),
			'solucao_casos_2_titulo'       => 'Conecte mainframes sem VPN',
			'solucao_casos_2_desc'         => 'Integre ambientes z/OS e AS/400 utilizando Runtime, sem alterar a infraestrutura de rede.',
			'solucao_casos_3_icone'        => $this->img( 'atualizacao-de-sistemas-legados-caso-3' ),
			'solucao_casos_3_titulo'       => 'Substitua IBM MQ por eventos',
			'solucao_casos_3_desc'         => 'Converta integrações baseadas em filas para uma arquitetura orientada a eventos com Kafka.',
			'solucao_casos_4_icone'        => $this->img( 'atualizacao-de-sistemas-legados-caso-4' ),
			'solucao_casos_4_titulo'       => 'Exponha ERPs legados por APIs',
			'solucao_casos_4_desc'         => 'Disponibilize SAP ECC e Oracle EBS através de APIs modernas sem alterar o core das aplicações.',
			'solucao_casos_5_icone'        => $this->img( 'atualizacao-de-sistemas-legados-caso-5' ),
			'solucao_casos_5_titulo'       => 'Conecte aplicações SaaS no primeiro dia',
			'solucao_casos_5_desc'         => 'Integre Salesforce, ServiceNow, Workday e outras plataformas à nova arquitetura sem depender do antigo ESB.',
			'solucao_casos_cta_texto'      => 'Agende uma demonstração',
			'solucao_casos_cta_url'        => '/contato/',

			// 6 · Diferencial (antes dos Selos neste design).
			'solucao_dif_eyebrow'          => 'diferencial técnico',
			'solucao_dif_titulo'           => 'Seu próximo projeto não deveria começar pelo legado',
			'solucao_dif_corpo'            => 'Reconstrua a camada de integração em uma arquitetura moderna sem exigir que os sistemas existentes sejam substituídos.',
			'solucao_dif_topico_1'         => 'Evolua a arquitetura sem expor sistemas críticos',
			'solucao_dif_topico_2'         => 'Modernize mainframes sem comprometer a segurança',
			'solucao_dif_topico_3'         => 'Conecte aplicações legadas com isolamento do core',
			'solucao_dif_imagem'           => $this->img( 'atualizacao-de-sistemas-legados-diferencial' ),
			'solucao_dif_antes_selos'      => 1,

			// 7 · Selos.
			'solucao_selos_eyebrow'        => 'compliance & segurança',
			'solucao_selos_titulo'         => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'          => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_atualizacao_de_sistemas_legados_faq( $post_id );

		WP_CLI::log( "  Atualização de Sistemas Legados preenchido (ID: {$post_id})." );
	}

	/**
	 * Cria os posts cli_faq da Atualização de Sistemas Legados e vincula à solução.
	 *
	 * ATENÇÃO — texto provisório: o Figma mostra apenas as perguntas (o
	 * accordion está fechado no design). As respostas foram redigidas a partir
	 * do que a própria landing afirma nas seções anteriores e seguem pendentes
	 * de validação do cliente.
	 *
	 * @param int $post_id ID do post cli_solucao da Atualização de Sistemas Legados.
	 * @return void
	 */
	protected function preencher_atualizacao_de_sistemas_legados_faq( $post_id ) {
		$itens = array(
			array(
				'faq:legado-mainframe-rede',
				'É possível conectar mainframes sem alterar a infraestrutura de rede?',
				'<p>Sim. O Runtime é instalado dentro do próprio ambiente, do mesmo lado do firewall em que o mainframe já vive, e é ele que abre a conexão para fora — não o contrário. Ambientes z/OS e AS/400 continuam onde estão, com as mesmas regras de rede, sem VPN dedicada nem porta nova exposta para a internet. O que muda é apenas quem passa a conversar com esses sistemas: a camada de integração, em vez de cada aplicação isoladamente.</p>',
			),
			array(
				'faq:legado-esb-transicao',
				'As integrações continuam funcionando durante a substituição do ESB?',
				'<p>Continuam. A transição é feita rota a rota: a nova arquitetura sobe em paralelo ao ESB atual e cada fluxo só é redirecionado depois de rodar em produção com o mesmo resultado do antigo. Enquanto uma rota está sendo reconstruída, a versão legada segue ativa, o que permite voltar atrás sem parar a operação. O ESB só é desligado quando não resta nenhum fluxo dependendo dele.</p>',
			),
			array(
				'faq:legado-esb-vs-ipaas',
				'Qual a diferença entre substituir um ESB por uma plataforma moderna de integração?',
				'<p>O ESB tradicional concentra as regras em código proprietário, exige especialistas na tecnologia específica e trata cada mudança como um novo projeto. A plataforma moderna coloca os mesmos fluxos em um ambiente visual, sobre padrões abertos e portáveis, com histórico de execução, tratamento de erro e governança de acesso em um só lugar. Na prática, o ganho não é só de tecnologia: é a redução da dependência de um grupo pequeno de pessoas para manter a integração de pé.</p>',
			),
			array(
				'faq:legado-prazo-reconstrucao',
				'Quanto tempo leva para reconstruir integrações existentes?',
				'<p>Depende muito mais da quantidade de regras de negócio embutidas na rota antiga do que da tecnologia de origem. Fluxos diretos — uma leitura, uma transformação, uma entrega — costumam ser reconstruídos e homologados em semanas. O que estica o prazo é a arqueologia: rotas sem documentação, transformações escritas em código dentro do ESB e regras que ninguém mais conhece precisam ser mapeadas antes de serem reescritas. Por isso a substituição é feita por ondas, começando pelas rotas de maior volume e menor complexidade.</p>',
			),
			array(
				'faq:legado-pos-desativacao',
				'O que acontece depois que o ESB é completamente desativado?',
				'<p>A operação passa a viver na nova camada, e o custo de licença e sustentação da plataforma antiga sai da conta. A partir daí, integração deixa de ser projeto e vira rotina: novas conexões, mudanças de regra e ajustes de fluxo entram pelo mesmo ambiente, com monitoramento centralizado e sem precisar de um especialista da tecnologia legada. É também o momento em que a arquitetura orientada a eventos começa a render — aplicações novas se conectam ao que já existe em vez de abrir mais uma integração ponto a ponto.</p>',
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

		WP_CLI::log( sprintf( '  Atualização de Sistemas Legados FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/**
	 * Preenche os campos ACF do post cli_solucao "Integração Pós-Fusão".
	 *
	 * O design cobre seis seções — Hero, Pilares, Casos de Uso, Diferencial,
	 * Selos e FAQ. Métricas, Logos, Plataforma e Aceleradores não existem
	 * neste layout e ficam vazias (cada template-part retorna cedo).
	 *
	 * @return void
	 */
	protected function preencher_solucao_integracao_pos_fusao() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:integracao-pos-fusao', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  Integração Pós-Fusão: post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'     => 'integração pós-fusão',
			'solucao_hero_titulo'      => 'Integre empresas adquiridas desde o primeiro dia',
			'solucao_hero_corpo'       => 'Conecte sistemas críticos sem abrir portas de firewall e acelere a captura de sinergias enquanto a consolidação de TI acontece.',
			'solucao_hero_btn1_texto'  => 'Agende uma demonstração',
			'solucao_hero_btn1_url'    => '/contato/',
			'solucao_hero_imagem'      => $this->img( 'integracao-pos-fusao-hero' ),

			// 2 · Pilares.
			'solucao_pilares_eyebrow'  => 'pilares',
			'solucao_pilares_titulo'   => 'Acelere resultados após uma aquisição',
			'solucao_pilares_1_icone'  => $this->img( 'integracao-pos-fusao-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Ative sistemas antes do fechamento',
			'solucao_pilares_1_desc'   => 'Disponibilize identidade, folha e ERP antes da conclusão do negócio para garantir continuidade operacional.',
			'solucao_pilares_2_icone'  => $this->img( 'integracao-pos-fusao-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Entregue sinergias no prazo',
			'solucao_pilares_2_desc'   => 'Conecte ambientes com dual-ERP e cumpra objetivos de integração sem esperar uma consolidação completa.',
			'solucao_pilares_3_icone'  => $this->img( 'integracao-pos-fusao-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Reutilize integrações em aquisições',
			'solucao_pilares_3_desc'   => 'Crie cápsulas reutilizáveis para acelerar novas integrações mantendo padrões consistentes entre empresas.',

			// 3 · Casos de Uso.
			'solucao_casos_eyebrow'    => 'casos de uso',
			'solucao_casos_titulo'     => 'Integre operações sem atrasar o negócio',
			'solucao_casos_1_icone'    => $this->img( 'integracao-pos-fusao-caso-1' ),
			'solucao_casos_1_titulo'   => 'Unifique identidades corporativas',
			'solucao_casos_1_desc'     => 'Conecte Entra ID e Okta para habilitar acesso único aos colaboradores das empresas integradas.',
			'solucao_casos_2_icone'    => $this->img( 'integracao-pos-fusao-caso-2' ),
			'solucao_casos_2_titulo'   => 'Sincronize múltiplos ERPs',
			'solucao_casos_2_desc'     => 'Integre SAP e Oracle Fusion durante a transição sem depender da consolidação definitiva dos sistemas.',
			'solucao_casos_3_icone'    => $this->img( 'integracao-pos-fusao-caso-3' ),
			'solucao_casos_3_titulo'   => 'Consolide dados de RH',
			'solucao_casos_3_desc'     => 'Conecte Workday e Oracle HCM para unificar processos e informações após a fusão.',
			'solucao_casos_4_icone'    => $this->img( 'integracao-pos-fusao-caso-4' ),
			'solucao_casos_4_titulo'   => 'Migre seu CRM',
			'solucao_casos_4_desc'     => 'Transfira informações comerciais entre plataformas mantendo continuidade no relacionamento com clientes.',
			'solucao_casos_5_icone'    => $this->img( 'integracao-pos-fusao-caso-5' ),
			'solucao_casos_5_titulo'   => 'Unifique dados analíticos',
			'solucao_casos_5_desc'     => 'Conecte Snowflake e BigQuery para criar uma visão consolidada das operações combinadas.',
			'solucao_casos_cta_texto'  => 'Agende uma demonstração',
			'solucao_casos_cta_url'    => '/contato/',

			// 4 · Diferencial (antes dos Selos neste design).
			'solucao_dif_eyebrow'      => 'diferencial técnico',
			'solucao_dif_titulo'       => 'Integração segura desde o Dia 1',
			'solucao_dif_corpo'        => 'Conecte sistemas adquiridos rapidamente com uma arquitetura preparada para ambientes corporativos, sem depender de alterações complexas na infraestrutura.',
			'solucao_dif_topico_1'     => 'Runtime com conexão outbound-only',
			'solucao_dif_topico_2'     => 'Deploy multi-cloud ou Kubernetes gerenciado',
			'solucao_dif_topico_3'     => '300+ conectores sem custo adicional',
			'solucao_dif_imagem'       => $this->img( 'integracao-pos-fusao-diferencial' ),
			'solucao_dif_antes_selos'  => 1,

			// 5 · Selos.
			'solucao_selos_eyebrow'    => 'compliance & segurança',
			'solucao_selos_titulo'     => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'      => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_integracao_pos_fusao_faq( $post_id );

		WP_CLI::log( "  Integração Pós-Fusão preenchido (ID: {$post_id})." );
	}

	/**
	 * Cria os posts cli_faq da Integração Pós-Fusão e vincula à solução.
	 *
	 * ATENÇÃO — texto provisório: o Figma mostra apenas as perguntas (o
	 * accordion está fechado no design). As respostas foram redigidas a partir
	 * do que a própria landing afirma nas seções anteriores e seguem pendentes
	 * de validação do cliente.
	 *
	 * @param int $post_id ID do post cli_solucao da Integração Pós-Fusão.
	 * @return void
	 */
	protected function preencher_integracao_pos_fusao_faq( $post_id ) {
		$itens = array(
			array(
				'faq:ipf-runtime-firewall',
				'Como o Runtime elimina o problema de firewall no Dia 1?',
				'<p>O runtime roda dentro do ambiente da empresa adquirida e abre a conexão de dentro para fora — é ele que procura a plataforma, nunca o contrário. Como não existe porta de entrada a ser publicada, não há regra de borda nova, IP fixo a negociar nem exceção a ser aprovada pelo time de segurança da outra companhia. É justamente esse ponto que costuma travar as primeiras semanas de uma aquisição, quando as duas redes ainda são independentes e ninguém quer flexibilizar o perímetro.</p>',
			),
			array(
				'faq:ipf-antes-consolidacao-ti',
				'É possível conectar sistemas antes da consolidação de TI?',
				'<p>Sim, e é o cenário mais comum. A integração acontece na camada de dados e processos, sobre os sistemas como eles estão hoje — dois ERPs, dois diretórios de identidade, duas folhas. Identidade, RH e ERP podem ser conectados antes mesmo do fechamento do negócio, para que a operação continue de pé no primeiro dia. A consolidação definitiva segue seu próprio cronograma, sem bloquear a captura de sinergias.</p>',
			),
			array(
				'faq:ipf-velocidade-deploy',
				'Qual a velocidade de deploy para deixar a operação pronta no Dia 1?',
				'<p>O runtime sobe em multi-cloud ou em Kubernetes gerenciado, então o provisionamento do ambiente é questão de horas, não de projeto. O que define o cronograma é o acesso: liberação de credencial nos sistemas de origem e destino, e o aceite do desenho pelas áreas envolvidas. Com mais de 300 conectores prontos e sem custo adicional, os fluxos críticos do Dia 1 — acesso, folha, pedidos — normalmente entram em semanas, e não em meses.</p>',
			),
			array(
				'faq:ipf-pipelines-pos-projeto',
				'Os pipelines continuam gerando valor após o projeto de integração?',
				'<p>Continuam. Cada integração é construída como cápsula reutilizável, com o padrão de mapeamento, tratamento de erro e governança já definidos. Terminada a incorporação, essas cápsulas viram o repertório da empresa para a próxima aquisição: o que foi feito para conectar um ERP ou um diretório de identidade é reaproveitado, em vez de recomeçar do zero. Elas também seguem sustentando a operação corrente — sincronização de cadastros, dados analíticos e processos entre as unidades combinadas.</p>',
			),
			array(
				'faq:ipf-substituir-middleware-legado',
				'Como substituir o middleware legado da empresa adquirida?',
				'<p>A troca é feita fluxo a fluxo, sem big bang. O primeiro passo é inventariar o que o middleware antigo realmente executa e com que frequência; em seguida os fluxos são reconstruídos na plataforma e rodam em paralelo com o legado, com comparação de resultados antes do corte. Cada fluxo migrado é desligado do lado antigo só depois de estável, o que mantém a operação funcionando durante toda a transição e evita concentrar risco em uma única data.</p>',
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

		WP_CLI::log( sprintf( '  Integração Pós-Fusão FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/**
	 * Preenche os campos ACF do post cli_solucao "Visão 360° do Cliente".
	 *
	 * O design cobre sete seções — Hero, Pilares, Casos de Uso, Diferencial,
	 * Aceleradores, Selos e FAQ. Métricas, Logos, Diagrama e Plataforma não
	 * existem neste layout e ficam vazias (cada template-part retorna cedo).
	 * O Diferencial vem antes dos Selos (solucao_dif_antes_selos).
	 *
	 * @return void
	 */
	protected function preencher_solucao_visao_360_do_cliente() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:visao-360-do-cliente', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  Visão 360° do Cliente: post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'     => 'visão 360°',
			'solucao_hero_titulo'      => 'Uma única visão do cliente em todos os sistemas',
			'solucao_hero_corpo'       => 'Consolide dados de CRM, ERP, suporte e produto em uma visão 360º atualizada em tempo real para equipes e agentes de IA.',
			'solucao_hero_btn1_texto'  => 'Agende uma demonstração',
			'solucao_hero_btn1_url'    => '/contato/',
			'solucao_hero_imagem'      => $this->img( 'visao-360-do-cliente-hero' ),

			// 2 · Pilares.
			'solucao_pilares_eyebrow'  => 'pilares',
			'solucao_pilares_titulo'   => 'Transforme dados dispersos em contexto completo',
			'solucao_pilares_1_icone'  => $this->img( 'visao-360-do-cliente-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Unifique a identidade do cliente',
			'solucao_pilares_1_desc'   => 'Consolide informações de CRM, ERP, suporte e produto para criar um perfil único e consistente.',
			'solucao_pilares_2_icone'  => $this->img( 'visao-360-do-cliente-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Atualize informações em tempo real',
			'solucao_pilares_2_desc'   => 'Mantenha a visão do cliente sempre sincronizada, sem depender de cargas batch ou relatórios defasados.',
			'solucao_pilares_3_icone'  => $this->img( 'visao-360-do-cliente-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Compartilhe o mesmo contexto',
			'solucao_pilares_3_desc'   => 'Disponibilize uma visão unificada para vendas, suporte, customer success e agentes de inteligência artificial.',

			// 3 · Casos de Uso.
			'solucao_casos_eyebrow'    => 'casos de uso',
			'solucao_casos_titulo'     => 'Coloque o cliente no centro das operações',
			'solucao_casos_1_icone'    => $this->img( 'visao-360-do-cliente-caso-1' ),
			'solucao_casos_1_titulo'   => 'Resolva identidades duplicadas',
			'solucao_casos_1_desc'     => 'Reconcilie múltiplos identificadores entre CRM, ERP e suporte para criar um único perfil de cliente.',
			'solucao_casos_2_icone'    => $this->img( 'visao-360-do-cliente-caso-2' ),
			'solucao_casos_2_titulo'   => 'Unifique o histórico do cliente',
			'solucao_casos_2_desc'     => 'Reúna pedidos, chamados e uso do produto em uma única visão para customer success.',
			'solucao_casos_3_icone'    => $this->img( 'visao-360-do-cliente-caso-3' ),
			'solucao_casos_3_titulo'   => 'Forneça contexto para agentes de IA',
			'solucao_casos_3_desc'     => 'Entregue informações completas do cliente antes de cada interação automatizada ou assistida.',
			'solucao_casos_4_icone'    => $this->img( 'visao-360-do-cliente-caso-4' ),
			'solucao_casos_4_titulo'   => 'Segmente campanhas em tempo real',
			'solucao_casos_4_desc'     => 'Atualize públicos de marketing utilizando dados consolidados de todos os sistemas conectados.',
			'solucao_casos_5_icone'    => $this->img( 'visao-360-do-cliente-caso-5' ),
			'solucao_casos_5_titulo'   => 'Melhore decisões de atendimento',
			'solucao_casos_5_desc'     => 'Permita que equipes consultem o contexto completo do cliente durante qualquer atendimento.',
			'solucao_casos_cta_texto'  => 'Agende uma demonstração',
			'solucao_casos_cta_url'    => '/contato/',

			// 4 · Diferencial (antes dos Selos neste design).
			'solucao_dif_eyebrow'      => 'diferencial técnico',
			'solucao_dif_titulo'       => 'Governança para dados unificados',
			'solucao_dif_corpo'        => 'Controle como cada sistema acessa o perfil unificado do cliente, garantindo conformidade e qualidade dos dados.',
			'solucao_dif_topico_1'     => 'Governança compatível com LGPD e GDPR',
			'solucao_dif_topico_2'     => 'Controle de leitura e escrita por sistema',
			'solucao_dif_topico_3'     => 'Gestão centralizada dos atributos do cliente',
			'solucao_dif_imagem'       => $this->img( 'visao-360-do-cliente-diferencial' ),
			'solucao_dif_antes_selos'  => 1,

			// 5 · Aceleradores.
			'solucao_acel_eyebrow'     => 'aceleradores de integração',
			'solucao_acel_titulo'      => 'Modelo pronto para começar',
			'solucao_acel_corpo'       => 'Comece rapidamente com um fluxo pré-configurado que conecta pedido, faturamento, cobrança e conciliação financeira.',
			'solucao_acel_topico_1'    => 'Resolução automática de identidade',
			'solucao_acel_topico_2'    => 'Visão 360º atualizada em tempo real',
			'solucao_acel_topico_3'    => 'Contexto único para equipes e IA',
			'solucao_acel_topico_4'    => 'E muito mais...',
			'solucao_acel_btn_texto'   => 'Começar agora',
			'solucao_acel_btn_url'     => '/contato/',
			'solucao_acel_imagem'      => $this->img( 'visao-360-do-cliente-aceleradores' ),

			// 6 · Selos.
			'solucao_selos_eyebrow'    => 'compliance & segurança',
			'solucao_selos_titulo'     => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'      => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_visao_360_do_cliente_faq( $post_id );

		WP_CLI::log( "  Visão 360° do Cliente preenchido (ID: {$post_id})." );
	}

	/**
	 * Cria os posts cli_faq da Visão 360° do Cliente e vincula à solução.
	 *
	 * ATENÇÃO — texto provisório: o Figma mostra apenas as perguntas (o
	 * accordion está fechado no design). As respostas foram redigidas a partir
	 * do que a própria landing afirma nas seções anteriores e seguem pendentes
	 * de validação do cliente.
	 *
	 * @param int $post_id ID do post cli_solucao da Visão 360° do Cliente.
	 * @return void
	 */
	protected function preencher_visao_360_do_cliente_faq( $post_id ) {
		$itens = array(
			array(
				'faq:v360-resolucao-identidade',
				'Como resolver a identidade de um cliente entre sistemas diferentes?',
				'<p>Cada sistema guarda o cliente com a sua própria chave — código no ERP, ID no CRM, e-mail no suporte — e é por isso que a mesma empresa aparece três vezes com dados diferentes. A resolução de identidade cruza esses identificadores por regras de correspondência (documento, domínio de e-mail, razão social) e mantém uma tabela de equivalências entre eles. O perfil unificado passa a ser a referência, e cada sistema continua funcionando com a chave que já usa, sem migração de cadastro.</p>',
			),
			array(
				'faq:v360-tempo-real-ou-batch',
				'A visão 360º é atualizada em tempo real ou em batch?',
				'<p>Em tempo real: cada alteração relevante em um sistema conectado — um pedido faturado, um chamado encerrado, um dado cadastral corrigido — dispara a atualização do perfil unificado no momento em que acontece, sem esperar a janela da noite. Cargas em lote continuam disponíveis para o que faz sentido processar em bloco, como a carga inicial de histórico ou bases legadas, mas a operação do dia a dia não depende delas.</p>',
			),
			array(
				'faq:v360-contexto-agente-ia',
				'Como um agente de IA utiliza essa visão unificada?',
				'<p>O agente consulta o perfil unificado antes de responder ou agir, e recebe de uma vez o que estaria espalhado entre CRM, ERP, suporte e produto: contratos vigentes, pedidos em aberto, chamados recentes e uso do produto. Com esse contexto, a resposta deixa de ser genérica e as ações executadas — abrir um chamado, atualizar um cadastro, escalar um caso — acontecem sobre dados atuais. Os mesmos controles de leitura e escrita por sistema valem para o agente, então ele só enxerga e altera o que foi autorizado.</p>',
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

		WP_CLI::log( sprintf( '  Visão 360° do Cliente FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/**
	 * Preenche os campos ACF do post cli_solucao "IA Corporativa".
	 *
	 * Landing sem Métricas, Logos, Plataforma e Aceleradores: o Figma dessa
	 * solução vai de Hero → Pilares → Diagrama → Casos de Uso → Diferencial →
	 * Selos → FAQ. O Diferencial vem antes dos Selos (solucao_dif_antes_selos).
	 *
	 * @return void
	 */
	protected function preencher_solucao_ia_corporativa() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:ia-corporativa', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  IA Corporativa: post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'      => 'IA corporativa',
			'solucao_hero_titulo'       => 'Seus dados corporativos prontos para agentes de IA',
			'solucao_hero_corpo'        => 'Conecte Salesforce, SAP, TOTVS, Senior, ServiceNow e outros sistemas empresariais a qualquer LLM para criar agentes inteligentes que entendem dados e executam ações.',
			'solucao_hero_btn1_texto'   => 'Agende uma demonstração',
			'solucao_hero_btn1_url'     => '/contato/',
			'solucao_hero_imagem'       => $this->img( 'ia-corporativa-hero' ),

			// 3 · Pilares.
			'solucao_pilares_eyebrow'   => 'pilares',
			'solucao_pilares_titulo'    => 'Transforme dados em inteligência operacional',
			'solucao_pilares_1_icone'   => $this->img( 'ia-corporativa-pilar-1' ),
			'solucao_pilares_1_titulo'  => 'Trabalhe com dados ao vivo',
			'solucao_pilares_1_desc'    => 'Permita que agentes de IA consultem informações atuais dos seus sistemas, substituindo decisões baseadas em dados desatualizados.',
			'solucao_pilares_2_icone'   => $this->img( 'ia-corporativa-pilar-2' ),
			'solucao_pilares_2_titulo'  => 'Automatize workflows complexos',
			'solucao_pilares_2_desc'    => 'Crie agentes capazes de executar múltiplas etapas de processos, reduzindo tarefas manuais e acelerando operações.',
			'solucao_pilares_3_icone'   => $this->img( 'ia-corporativa-pilar-3' ),
			'solucao_pilares_3_titulo'  => 'Aplique segurança desde o início',
			'solucao_pilares_3_desc'    => 'Use controles de PII e guardrails para garantir que agentes atuem dentro das regras da empresa.',

			// 11 · Diagrama (renderiza logo depois dos Pilares).
			'solucao_diagrama_titulo'   => 'Um novo jeito de conectar IA aos seus sistemas',
			'solucao_diagrama_imagem'   => $this->img( 'ia-corporativa-diagrama' ),

			// 5 · Casos de Uso.
			'solucao_casos_eyebrow'     => 'casos de uso',
			'solucao_casos_titulo'      => 'Aplique IA nos processos do negócio',
			'solucao_casos_1_icone'     => $this->img( 'ia-corporativa-caso-1' ),
			'solucao_casos_1_titulo'    => 'Crie agentes em tempo real',
			'solucao_casos_1_desc'      => 'Gere resumos inteligentes para vendedores usando informações atualizadas de clientes, operações e sistemas corporativos.',
			'solucao_casos_2_icone'     => $this->img( 'ia-corporativa-caso-2' ),
			'solucao_casos_2_titulo'    => 'Conecte IA ao conhecimento interno',
			'solucao_casos_2_desc'      => 'Use RAG com Confluence e SharePoint para criar respostas baseadas no conhecimento da sua empresa.',
			'solucao_casos_3_icone'     => $this->img( 'ia-corporativa-caso-3' ),
			'solucao_casos_3_titulo'    => 'Exponha ferramentas via MCP',
			'solucao_casos_3_desc'      => 'Transforme recursos de Salesforce e SAP em ferramentas disponíveis para agentes de IA autenticados.',
			'solucao_casos_4_icone'     => $this->img( 'ia-corporativa-caso-4' ),
			'solucao_casos_4_titulo'    => 'Automatize operações com IA',
			'solucao_casos_4_desc'      => 'Automatize tarefas como abertura de incidentes no ServiceNow sem depender de processos manuais.',
			'solucao_casos_5_icone'     => $this->img( 'ia-corporativa-caso-5' ),
			'solucao_casos_5_titulo'    => 'Dispare IA por eventos',
			'solucao_casos_5_desc'      => 'Execute modelos de linguagem automaticamente quando eventos acontecerem, sem depender de consultas constantes.',
			'solucao_casos_cta_texto'   => 'Agende uma demonstração',
			'solucao_casos_cta_url'     => '/contato/',

			// 7 · Diferencial (antes dos Selos neste design).
			'solucao_dif_eyebrow'       => 'diferencial técnico',
			'solucao_dif_titulo'        => 'IA conectada com segurança corporativa',
			'solucao_dif_corpo'         => 'Acesse ambientes críticos sem depender de VPNs complexas',
			'solucao_dif_topico_1'      => 'Runtime para conexão direta com mainframes',
			'solucao_dif_topico_2'      => 'Menos aprovações de infraestrutura',
			'solucao_dif_topico_3'      => 'Migração mais rápida de sistemas legados',
			'solucao_dif_imagem'        => $this->img( 'ia-corporativa-dif' ),
			'solucao_dif_antes_selos'   => 1,

			// 6 · Selos.
			'solucao_selos_eyebrow'     => 'compliance & segurança',
			'solucao_selos_titulo'      => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'       => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_ia_corporativa_faq( $post_id );

		WP_CLI::log( "  IA Corporativa preenchida (ID: {$post_id})." );
	}

	/**
	 * Cria os posts cli_faq da IA Corporativa e vincula à solução.
	 *
	 * ATENÇÃO — texto provisório: o Figma mostra apenas as perguntas (o
	 * accordion está fechado no design). As respostas foram redigidas a partir
	 * do que a própria landing afirma nas seções anteriores e seguem pendentes
	 * de validação do cliente.
	 *
	 * @param int $post_id ID do post cli_solucao da IA Corporativa.
	 * @return void
	 */
	protected function preencher_ia_corporativa_faq( $post_id ) {
		$itens = array(
			array(
				'faq:ia-provedores-llm',
				'Quais provedores de LLM são suportados nativamente?',
				'<p>A conexão com o modelo é só mais uma ponta da integração, então vale para qualquer provedor que exponha API — os grandes fornecedores de nuvem e os modelos abertos hospedados na sua própria infraestrutura inclusive. Na prática isso significa que o agente conversa com o modelo pelo mesmo caminho por onde conversa com o ERP: credencial guardada em um só lugar, chamada registrada e limite de custo aplicado antes de a requisição sair.</p>',
			),
			array(
				'faq:ia-pipelines-mcp',
				'Como pipelines de integração viram ferramentas MCP?',
				'<p>Um fluxo que já existe — consultar um pedido no SAP, abrir um chamado no ServiceNow, buscar a ficha de um cliente no Salesforce — é publicado como ferramenta, com a descrição do que faz, os parâmetros que aceita e o retorno que devolve. O agente passa a enxergar essa ferramenta no catálogo e a chamar quando precisar, sem acesso direto ao sistema de origem: a autenticação, o controle de permissão por perfil e o registro da execução continuam na camada de integração.</p>',
			),
			array(
				'faq:ia-vs-data-factory-glue',
				'Qual a diferença entre essa abordagem e Azure Data Factory ou AWS Glue?',
				'<p>Data Factory e Glue são ferramentas de pipeline de dados: movem volume de um ponto a outro em lote, para alimentar um data warehouse. O que a IA corporativa exige é diferente — resposta ao vivo, para uma pergunta específica, no instante em que o agente pergunta, e a capacidade de executar uma ação de volta no sistema de origem. É por isso que a camada aqui é de integração de aplicações e não de ETL, e por isso ela expõe ferramentas e eventos além de tabelas.</p>',
			),
			array(
				'faq:ia-tempo-rag',
				'Quanto tempo leva para colocar RAG em produção?',
				'<p>Com os conectores de Confluence e SharePoint já prontos, o cronograma depende menos do desenvolvimento e mais do acesso e da curadoria: liberar credencial, decidir quais espaços entram na base e definir quem pode ver o quê. Um primeiro escopo bem delimitado costuma entrar em semanas. O que estica o prazo é base documental desorganizada e permissão herdada de forma inconsistente na origem — nesses casos o trabalho de limpeza é maior que o de integração.</p>',
			),
			array(
				'faq:ia-troca-de-provedor',
				'Minha arquitetura continua flexível ao trocar o provedor de IA?',
				'<p>Sim, porque o modelo fica atrás da camada de integração, não no meio dela. As regras de negócio, as ferramentas publicadas, os guardrails e o histórico de execução pertencem à plataforma; trocar de provedor é trocar a credencial e o endpoint de uma ponta, mantendo tudo o mais no lugar. Isso também permite rodar mais de um modelo em paralelo — um para tarefas simples, outro para as caras — sem duplicar a integração.</p>',
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

		WP_CLI::log( sprintf( '  IA Corporativa FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/**
	 * Preenche os campos ACF do post cli_solucao "Compras ao Pagamento (S2P)".
	 *
	 * O design cobre oito seções — Hero, Métricas, Pilares, Casos de Uso,
	 * Diferencial, Aceleradores, Selos e FAQ. Logos, Diagrama e Plataforma não
	 * existem neste layout e ficam vazias (cada template-part retorna cedo).
	 *
	 * @return void
	 */
	protected function preencher_solucao_compras_ao_pagamento() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:compras-ao-pagamento', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  Compras ao Pagamento: post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'     => 'das compras ao pagamento (s2p)',
			'solucao_hero_titulo'      => 'Do fornecedor ao pagamento, sem planilhas no meio',
			'solucao_hero_corpo'       => 'Conecte compras, ERP, contratos e bancos em um fluxo único para controlar cada etapa do ciclo de suprimentos com rastreabilidade.',
			'solucao_hero_btn1_texto'  => 'Agende uma demonstração',
			'solucao_hero_btn1_url'    => '/contato/',
			'solucao_hero_imagem'      => $this->img( 'compras-ao-pagamento-hero' ),

			// 2 · Métricas.
			'solucao_metrica_1_numero' => '26.000',
			'solucao_metrica_1_rotulo' => 'horas de compras eliminadas',
			'solucao_metrica_2_numero' => '80x',
			'solucao_metrica_2_rotulo' => 'mais rápido no processamento de faturas',
			'solucao_metrica_3_numero' => '70%',
			'solucao_metrica_3_rotulo' => 'mais rápido no cadastro de fornecedores',

			// 3 · Pilares.
			'solucao_pilares_eyebrow'  => 'pilares',
			'solucao_pilares_titulo'   => 'Controle total do ciclo de compras',
			'solucao_pilares_1_icone'  => $this->img( 'compras-ao-pagamento-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Conecte todo o fluxo de compras',
			'solucao_pilares_1_desc'   => 'Integre cotação, aprovação, pedido e pagamento em um único processo rastreável e conectado.',
			'solucao_pilares_2_icone'  => $this->img( 'compras-ao-pagamento-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Elimine aprovações manuais',
			'solucao_pilares_2_desc'   => 'Reduza o tempo do ciclo de compras removendo dependências de e-mails e processos manuais.',
			'solucao_pilares_3_icone'  => $this->img( 'compras-ao-pagamento-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Tenha visão dos gastos',
			'solucao_pilares_3_desc'   => 'Acompanhe despesas em tempo real para tomar decisões financeiras com mais precisão.',

			// 4 · Casos de Uso.
			'solucao_casos_eyebrow'    => 'casos de uso',
			'solucao_casos_titulo'     => 'Automatize cada etapa do suprimento',
			'solucao_casos_1_icone'    => $this->img( 'compras-ao-pagamento-caso-1' ),
			'solucao_casos_1_titulo'   => 'Gere pedidos automaticamente',
			'solucao_casos_1_desc'     => 'Transforme requisições aprovadas em pedidos de compra no ERP sem intervenções manuais.',
			'solucao_casos_2_icone'    => $this->img( 'compras-ao-pagamento-caso-2' ),
			'solucao_casos_2_titulo'   => 'Automatize o matching de 3 vias',
			'solucao_casos_2_desc'     => 'Valide pedido, recebimento e nota fiscal automaticamente antes de liberar pagamentos.',
			'solucao_casos_3_icone'    => $this->img( 'compras-ao-pagamento-caso-3' ),
			'solucao_casos_3_titulo'   => 'Dispare pagamentos automaticamente',
			'solucao_casos_3_desc'     => 'Execute pagamentos a fornecedores após aprovação e conferência dos documentos necessários.',
			'solucao_casos_4_icone'    => $this->img( 'compras-ao-pagamento-caso-4' ),
			'solucao_casos_4_titulo'   => 'Consolide gastos estratégicos',
			'solucao_casos_4_desc'     => 'Unifique despesas por categoria e fornecedor para melhorar negociações e decisões de compra.',
			'solucao_casos_5_icone'    => $this->img( 'compras-ao-pagamento-caso-5' ),
			'solucao_casos_5_titulo'   => 'Rastreie todo o ciclo de compras',
			'solucao_casos_5_desc'     => 'Acompanhe cada etapa da requisição ao pagamento com histórico completo e visão operacional.',
			'solucao_casos_cta_texto'  => 'Agende uma demonstração',
			'solucao_casos_cta_url'    => '/contato/',

			// 5 · Diferencial (antes dos Selos neste design).
			'solucao_dif_eyebrow'      => 'diferencial técnico',
			'solucao_dif_titulo'       => 'Governança em cada transação',
			'solucao_dif_corpo'        => 'Garanta controle sobre aprovações e pagamentos com rastreabilidade completa e separação entre funções críticas.',
			'solucao_dif_topico_1'     => 'Histórico completo de aprovações',
			'solucao_dif_topico_2'     => 'Segregação entre aprovar e pagar',
			'solucao_dif_topico_3'     => 'Controle sobre todo o fluxo financeiro',
			'solucao_dif_imagem'       => $this->img( 'compras-ao-pagamento-diferencial' ),
			'solucao_dif_antes_selos'  => 1,

			// 6 · Aceleradores.
			'solucao_acel_eyebrow'     => 'aceleradores de integração',
			'solucao_acel_titulo'      => 'Modelo pronto para começar',
			'solucao_acel_corpo'       => 'Comece rapidamente com um fluxo pré-configurado que conecta pedido, faturamento, cobrança e conciliação financeira.',
			'solucao_acel_topico_1'    => 'Requisição → aprovação → pedido',
			'solucao_acel_topico_2'    => 'Matching de 3 vias automatizado',
			'solucao_acel_topico_3'    => 'Pagamento após conferência',
			'solucao_acel_topico_4'    => 'E muito mais...',
			'solucao_acel_btn_texto'   => 'Começar agora',
			'solucao_acel_btn_url'     => '',
			'solucao_acel_imagem'      => $this->img( 'compras-ao-pagamento-aceleradores' ),

			// 7 · Selos.
			'solucao_selos_eyebrow'    => 'compliance & segurança',
			'solucao_selos_titulo'     => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'      => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_compras_ao_pagamento_faq( $post_id );

		WP_CLI::log( "  Compras ao Pagamento preenchido (ID: {$post_id})." );
	}

	/**
	 * Cria os posts cli_faq de Compras ao Pagamento (S2P) e vincula à solução.
	 *
	 * ATENÇÃO — texto provisório: o Figma mostra apenas as perguntas (o
	 * accordion está fechado no design). As respostas foram redigidas a partir
	 * do que a própria landing afirma nas seções anteriores e seguem pendentes
	 * de validação do cliente.
	 *
	 * @param int $post_id ID do post cli_solucao de Compras ao Pagamento.
	 * @return void
	 */
	protected function preencher_compras_ao_pagamento_faq( $post_id ) {
		$itens = array(
			array(
				'faq:s2p-matching-3-vias',
				'Como automatizar o matching de 3 vias entre pedido, recebimento e nota fiscal?',
				'<p>A integração lê os três documentos onde eles nascem — o pedido de compra no ERP, o registro de recebimento no almoxarifado ou no WMS e a nota fiscal enviada pelo fornecedor — e compara item a item quantidade, preço e condição comercial. Quando os três batem dentro das tolerâncias definidas pela empresa, a fatura segue direto para pagamento; quando há divergência, o fluxo para e aciona o responsável com o motivo exato da diferença. O time financeiro deixa de conferir planilha por planilha e passa a tratar apenas as exceções.</p>',
			),
			array(
				'faq:s2p-visibilidade-gastos',
				'É possível dar visibilidade de gastos em tempo real ao financeiro?',
				'<p>Sim. Como cada etapa do ciclo passa pela integração, o compromisso financeiro é registrado no momento em que acontece: a requisição aprovada, o pedido emitido, o recebimento confirmado e a fatura liberada. Esses dados são consolidados por categoria, centro de custo e fornecedor e enviados ao ERP ou à ferramenta de BI da empresa, o que permite acompanhar o gasto comprometido antes de ele virar despesa contabilizada — e negociar com base no volume real por fornecedor.</p>',
			),
			array(
				'faq:s2p-segregacao-funcoes',
				'Como funciona a segregação de funções entre aprovação e pagamento?',
				'<p>Aprovar e pagar são etapas distintas do fluxo, com permissões distintas: quem autoriza a compra não é quem executa a liberação financeira, e a integração respeita os papéis já definidos no ERP e no sistema de aprovação. Cada transição registra quem agiu, quando e sobre qual documento, formando um histórico completo de aprovações disponível para auditoria. Nenhum pagamento é disparado sem que a etapa anterior tenha sido concluída pelo perfil autorizado.</p>',
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

		WP_CLI::log( sprintf( '  Compras ao Pagamento FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/**
	 * Preenche os campos ACF do post cli_solucao "Soberania de Dados".
	 *
	 * O design cobre sete seções — Hero, Pilares, Casos de Uso, Diferencial,
	 * Aceleradores, Selos e FAQ. Métricas, Logos, Diagrama e Plataforma não
	 * existem neste layout e ficam vazias (cada template-part retorna cedo).
	 *
	 * @return void
	 */
	protected function preencher_solucao_soberania_de_dados() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:soberania-de-dados', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  Soberania de Dados: post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'     => 'soberania de dados',
			'solucao_hero_titulo'      => 'Processe e armazene dados onde sua operação exige.',
			'solucao_hero_corpo'       => 'Execute a CLI Connect powered by Boomi dentro do ambiente do próprio cliente, garantindo que dados sensíveis permaneçam na jurisdição definida pelo negócio e pela regulamentação.',
			'solucao_hero_btn1_texto'  => 'Agende uma demonstração',
			'solucao_hero_btn1_url'    => '/contato/',
			'solucao_hero_imagem'      => $this->img( 'soberania-de-dados-hero' ),

			// 3 · Pilares.
			'solucao_pilares_eyebrow'  => 'pilares',
			'solucao_pilares_titulo'   => 'Controle total sobre a residência dos dados',
			'solucao_pilares_1_icone'  => $this->img( 'soberania-de-dados-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Implante no seu ambiente',
			'solucao_pilares_1_desc'   => 'Execute integrações na nuvem ou infraestrutura própria do cliente, usando AWS, Azure, GCP ou datacenter interno.',
			'solucao_pilares_2_icone'  => $this->img( 'soberania-de-dados-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Mantenha dados sob seu controle',
			'solucao_pilares_2_desc'   => 'Garanta que informações sensíveis não transitem ou sejam armazenadas em ambientes compartilhados.',
			'solucao_pilares_3_icone'  => $this->img( 'soberania-de-dados-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Atenda regulações de dados',
			'solucao_pilares_3_desc'   => 'Cumpra requisitos de residência de dados para setores como financeiro, saúde e setor público.',

			// 5 · Casos de Uso.
			'solucao_casos_eyebrow'    => 'casos de uso',
			'solucao_casos_titulo'     => 'Aplique soberania de dados na prática',
			'solucao_casos_1_icone'    => $this->img( 'soberania-de-dados-caso-1' ),
			'solucao_casos_1_titulo'   => 'Implante por região regulatória',
			'solucao_casos_1_desc'     => 'Execute pipelines dentro da nuvem ou região exigida por regulamentações locais de dados.',
			'solucao_casos_2_icone'    => $this->img( 'soberania-de-dados-caso-2' ),
			'solucao_casos_2_titulo'   => 'Proteja dados sensíveis',
			'solucao_casos_2_desc'     => 'Processe informações financeiras e de saúde sem remover dados da jurisdição definida.',
			'solucao_casos_3_icone'    => $this->img( 'soberania-de-dados-caso-3' ),
			'solucao_casos_3_titulo'   => 'Opere em múltiplos países',
			'solucao_casos_3_desc'     => 'Crie arquiteturas multi-região para atender diferentes leis de dados em cada mercado.',
			'solucao_casos_4_icone'    => $this->img( 'soberania-de-dados-caso-4' ),
			'solucao_casos_4_titulo'   => 'Comprove conformidade',
			'solucao_casos_4_desc'     => 'Audite onde cada dado foi processado para demonstrar controle e atender requisitos regulatórios.',
			'solucao_casos_5_icone'    => $this->img( 'soberania-de-dados-caso-5' ),
			'solucao_casos_5_titulo'   => 'Controle ambientes críticos',
			'solucao_casos_5_desc'     => 'Mantenha integrações executando dentro da infraestrutura escolhida pela sua organização.',
			'solucao_casos_cta_texto'  => 'Agende uma demonstração',
			'solucao_casos_cta_url'    => '/contato/',

			// 7 · Diferencial (antes dos Selos neste design).
			'solucao_dif_eyebrow'      => 'diferencial técnico',
			'solucao_dif_titulo'       => 'Soberania garantida pela arquitetura',
			'solucao_dif_corpo'        => 'Diferente de ambientes compartilhados, a plataforma executa dentro do ambiente do cliente, garantindo controle sobre dados e processamento.',
			'solucao_dif_topico_1'     => 'Ambiente dedicado ao cliente',
			'solucao_dif_topico_2'     => 'Controle sobre processamento e armazenamento',
			'solucao_dif_topico_3'     => 'Arquitetura sem compartilhamento de dados',
			'solucao_dif_imagem'       => $this->img( 'soberania-de-dados-dif' ),
			'solucao_dif_antes_selos'  => 1,

			// 6 · Selos.
			'solucao_selos_eyebrow'    => 'compliance & segurança',
			'solucao_selos_titulo'     => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'      => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',

			// 9 · Aceleradores.
			'solucao_acel_eyebrow'     => 'Aceleradores de integração',
			'solucao_acel_titulo'      => 'Modelo pronto para começar',
			'solucao_acel_corpo'       => 'Comece rapidamente com um fluxo pré-configurado que conecta pedido, faturamento, cobrança e conciliação financeira.',
			'solucao_acel_topico_1'    => 'Escolha da região de implantação',
			'solucao_acel_topico_2'    => 'Modelo pronto para ambientes regulados',
			'solucao_acel_topico_3'    => 'Execução em AWS, Azure ou GCP',
			'solucao_acel_topico_4'    => 'E muito mais...',
			'solucao_acel_btn_texto'   => 'Começar agora',
			'solucao_acel_btn_url'     => '/contato/',
			'solucao_acel_imagem'      => $this->img( 'soberania-de-dados-acel' ),
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_soberania_de_dados_faq( $post_id );

		WP_CLI::log( "  Soberania de Dados preenchida (ID: {$post_id})." );
	}

	/**
	 * Cria os posts cli_faq da Soberania de Dados e vincula à solução.
	 *
	 * ATENÇÃO — texto provisório: o Figma mostra apenas as perguntas (o
	 * accordion está fechado no design). As respostas foram redigidas a partir
	 * do que a própria landing afirma nas seções anteriores e seguem pendentes
	 * de validação do cliente.
	 *
	 * @param int $post_id ID do post cli_solucao da Soberania de Dados.
	 * @return void
	 */
	protected function preencher_soberania_de_dados_faq( $post_id ) {
		$itens = array(
			array(
				'faq:soberania-jurisdicao',
				'Como a CLI Connect powered by Boomi garante que os dados não saiam da jurisdição exigida?',
				'<p>O motor de execução roda dentro do ambiente que você indicar — a sua conta na AWS, Azure ou GCP, ou o datacenter interno da empresa. É lá que o dado é lido, transformado e gravado; o plano de controle da plataforma cuida de configuração, versionamento e monitoramento, e não do conteúdo que trafega. Na prática, um registro que nasce em uma região só sai dela se um fluxo que você mesmo desenhou mandar sair.</p>',
			),
			array(
				'faq:soberania-multi-regiao',
				'É possível ter deploy multi-região para operações em vários países?',
				'<p>Sim, e é o desenho mais comum em operações internacionais: um ambiente de execução por país ou bloco regulatório, cada um com a sua própria fronteira de dados, todos administrados de um único lugar. Os fluxos são construídos uma vez e distribuídos para cada região, o que evita manter times paralelos cuidando de integrações quase idênticas — e permite que uma regra local, quando existe, apareça como exceção explícita e não como uma cópia inteira do projeto.</p>',
			),
			array(
				'faq:soberania-auditoria',
				'Como funciona a auditoria de onde os dados foram processados?',
				'<p>Cada execução deixa registro de qual ambiente a processou, quando, com qual versão do fluxo e com que resultado. Esse histórico é o que sustenta a resposta a um auditor: em vez de uma declaração de política, você mostra o rastro de execução por região. Os registros podem ser mantidos no seu próprio ambiente e exportados para a ferramenta de observabilidade ou de compliance que a empresa já usa.</p>',
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

		WP_CLI::log( sprintf( '  Soberania de Dados FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/**
	 * Preenche os campos ACF do post cli_solucao "Centro de Excelência em Integração".
	 *
	 * Landing sem Métricas, Logos, Diagrama e Plataforma: o Figma vai de
	 * Hero → Pilares → Casos de Uso → Diferencial → Aceleradores → Selos → FAQ.
	 * Os Selos fecham a página logo antes do FAQ — daí `solucao_dif_antes_selos`,
	 * que no template joga a faixa de selos para depois dos Aceleradores.
	 *
	 * ATENÇÃO — o corpo da seção Aceleradores está exatamente como no Figma
	 * ("conecta pedido, faturamento, cobrança e conciliação financeira"), texto
	 * herdado da landing de Compras ao Pagamento e provavelmente um resíduo do
	 * design. Mantido fiel à referência, pendente de decisão do cliente.
	 *
	 * @return void
	 */
	protected function preencher_solucao_centro_de_excelencia_em_integracao() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:centro-de-excelencia-em-integracao', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  Centro de Excelência em Integração: post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];
		$prefixo = 'centro-de-excelencia-em-integracao';

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'     => 'centro de excelência em integração',
			'solucao_hero_titulo'      => 'Transforme integrações em um ativo reutilizável da empresa',
			'solucao_hero_corpo'       => 'Crie um Centro de Excelência de Integração com catálogo compartilhado, padrões de desenvolvimento e governança para acelerar novos projetos.',
			'solucao_hero_btn1_texto'  => 'Agende uma demonstração',
			'solucao_hero_btn1_url'    => '/contato/',
			'solucao_hero_imagem'      => $this->img( $prefixo . '-hero' ),

			// 3 · Pilares.
			'solucao_pilares_eyebrow'  => 'pilares',
			'solucao_pilares_titulo'   => 'Padronize integrações em toda a organização',
			'solucao_pilares_1_icone'  => $this->img( $prefixo . '-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Reutilize integrações existentes',
			'solucao_pilares_1_desc'   => 'Centralize pipelines e cápsulas reutilizáveis para reduzir retrabalho e acelerar novos projetos.',
			'solucao_pilares_2_icone'  => $this->img( $prefixo . '-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Padronize o desenvolvimento',
			'solucao_pilares_2_desc'   => 'Defina padrões únicos para nomenclatura, autenticação e tratamento de erros em todas as integrações.',
			'solucao_pilares_3_icone'  => $this->img( $prefixo . '-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Fortaleça a governança',
			'solucao_pilares_3_desc'   => 'Controle quem cria, altera e publica integrações críticas com processos padronizados de aprovação.',

			// 5 · Casos de Uso.
			'solucao_casos_eyebrow'    => 'casos de uso',
			'solucao_casos_titulo'     => 'Escalone integrações com governança',
			'solucao_casos_1_icone'    => $this->img( $prefixo . '-caso-1' ),
			'solucao_casos_1_titulo'   => 'Centralize integrações reutilizáveis',
			'solucao_casos_1_desc'     => 'Disponibilize um catálogo interno de integrações para acelerar qualquer novo projeto.',
			'solucao_casos_2_icone'    => $this->img( $prefixo . '-caso-2' ),
			'solucao_casos_2_titulo'   => 'Padronize erros e retentativas',
			'solucao_casos_2_desc'     => 'Garanta que todos os pipelines utilizem as mesmas regras de tratamento e recuperação de falhas.',
			'solucao_casos_3_icone'    => $this->img( $prefixo . '-caso-3' ),
			'solucao_casos_3_titulo'   => 'Aprove integrações antes da produção',
			'solucao_casos_3_desc'     => 'Implemente fluxos de revisão e aprovação para garantir qualidade e conformidade antes do deploy.',
			'solucao_casos_4_icone'    => $this->img( $prefixo . '-caso-4' ),
			'solucao_casos_4_titulo'   => 'Monitore custo e desempenho',
			'solucao_casos_4_desc'     => 'Centralize métricas de uso, performance e consumo para otimizar continuamente suas integrações.',
			'solucao_casos_5_icone'    => $this->img( $prefixo . '-caso-5' ),
			'solucao_casos_5_titulo'   => 'Evite integrações duplicadas',
			'solucao_casos_5_desc'     => 'Permita que equipes reutilizem componentes existentes em vez de reconstruir fluxos já desenvolvidos.',
			'solucao_casos_cta_texto'  => 'Agende uma demonstração',
			'solucao_casos_cta_url'    => '/contato/',

			// 7 · Diferencial (antes dos Selos neste design).
			'solucao_dif_eyebrow'      => 'diferencial técnico',
			'solucao_dif_titulo'       => 'Governança para integrações críticas',
			'solucao_dif_corpo'        => 'Implemente controles que garantem segurança, rastreabilidade e qualidade durante todo o ciclo de desenvolvimento das integrações.',
			'solucao_dif_topico_1'     => 'Controle de acesso por função',
			'solucao_dif_topico_2'     => 'Fluxo de revisão e aprovação',
			'solucao_dif_topico_3'     => 'Auditoria de alterações em pipelines',
			'solucao_dif_imagem'       => $this->img( $prefixo . '-diferencial' ),
			'solucao_dif_antes_selos'  => 1,

			// 9 · Aceleradores.
			'solucao_acel_eyebrow'     => 'Aceleradores de integração',
			'solucao_acel_titulo'      => 'Modelo pronto para começar',
			'solucao_acel_corpo'       => 'Comece rapidamente com um fluxo pré-configurado que conecta pedido, faturamento, cobrança e conciliação financeira.',
			'solucao_acel_topico_1'    => 'Catálogo de cápsulas reutilizáveis',
			'solucao_acel_topico_2'    => 'Padrões únicos para novos projetos',
			'solucao_acel_topico_3'    => 'Governança pronta para escalar',
			'solucao_acel_topico_4'    => 'E muito mais...',
			'solucao_acel_btn_texto'   => 'Começar agora',
			'solucao_acel_btn_url'     => '/contato/',
			'solucao_acel_imagem'      => $this->img( $prefixo . '-aceleradores' ),

			// 6 · Selos.
			'solucao_selos_eyebrow'    => 'compliance & segurança',
			'solucao_selos_titulo'     => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'      => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_centro_de_excelencia_em_integracao_faq( $post_id );

		WP_CLI::log( "  Centro de Excelência em Integração preenchida (ID: {$post_id})." );
	}

	/**
	 * Cria os posts cli_faq do Centro de Excelência em Integração e vincula à solução.
	 *
	 * ATENÇÃO — texto provisório: o Figma mostra apenas as perguntas (o
	 * accordion está fechado no design). As respostas foram redigidas a partir
	 * do que a própria landing afirma nas seções anteriores e seguem pendentes
	 * de validação do cliente.
	 *
	 * @param int $post_id ID do post cli_solucao do Centro de Excelência em Integração.
	 * @return void
	 */
	protected function preencher_centro_de_excelencia_em_integracao_faq( $post_id ) {
		$itens = array(
			array(
				'faq:cei-catalogo-reutilizavel',
				'Como estruturar um catálogo interno de integrações reutilizáveis?',
				'<p>O catálogo começa pelo inventário do que já existe: cada pipeline em produção vira uma entrada com dono, sistemas conectados, contrato de entrada e saída e nível de criticidade. A partir daí, os trechos que se repetem entre projetos — autenticação, tratamento de erro, transformação de um mesmo objeto de negócio — são extraídos em cápsulas versionadas, publicadas para toda a organização. O ganho aparece no segundo projeto: em vez de reconstruir a conexão do zero, a equipe monta a integração a partir de peças já homologadas.</p>',
			),
			array(
				'faq:cei-governanca-aprovacao',
				'Como funciona a governança de aprovação de novas integrações?',
				'<p>Toda integração passa por um fluxo de revisão antes de chegar à produção: quem constrói não é quem aprova, e a promoção entre ambientes exige o aceite de um responsável técnico do Centro de Excelência. O que é verificado nessa etapa é sempre o mesmo conjunto — aderência ao padrão de nomenclatura, credenciais guardadas no cofre, tratamento de erro e retentativa configurados, e ausência de duplicidade em relação ao catálogo. O acesso é controlado por função, de forma que criar, alterar e publicar sejam permissões distintas.</p>',
			),
			array(
				'faq:cei-custo-e-performance',
				'É possível medir custo e performance de cada integração centralizadamente?',
				'<p>Sim. Como todas as integrações rodam sob a mesma plataforma e seguem o mesmo padrão de instrumentação, volume processado, tempo de execução, taxa de erro e consumo de recursos ficam disponíveis em um painel único, com corte por integração, por sistema conectado e por área responsável. É esse dado que sustenta as decisões seguintes do Centro de Excelência: quais fluxos otimizar, quais consolidar e quais aposentar por baixo uso.</p>',
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

		WP_CLI::log( sprintf( '  Centro de Excelência em Integração FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/**
	 * Preenche os campos ACF do post cli_solucao "Jornada do Colaborador (H2R)".
	 *
	 * O design cobre oito seções — Hero, Métricas, Pilares, Casos de Uso,
	 * Diferencial, Aceleradores, Selos e FAQ. Logos, Diagrama e Plataforma não
	 * existem neste layout e ficam vazias (cada template-part retorna cedo).
	 *
	 * ATENÇÃO — o corpo da seção Aceleradores está exatamente como no Figma
	 * ("conecta pedido, faturamento, cobrança e conciliação financeira"), texto
	 * herdado da landing de Compras ao Pagamento e provavelmente um resíduo do
	 * design. Mantido fiel à referência, pendente de decisão do cliente.
	 *
	 * @return void
	 */
	protected function preencher_solucao_jornada_do_colaborador() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:jornada-do-colaborador', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  Jornada do Colaborador: post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'     => 'jornada do colaborador (h2r)',
			'solucao_hero_titulo'      => 'Do primeiro dia ao desligamento, todos os sistemas de RH atualizados.',
			'solucao_hero_corpo'       => 'Orquestre o ciclo de vida completo do colaborador conectando RH, folha, acessos e benefícios em um único fluxo automatizado.',
			'solucao_hero_btn1_texto'  => 'Agende uma demonstração',
			'solucao_hero_btn1_url'    => '/contato/',
			'solucao_hero_imagem'      => $this->img( 'jornada-do-colaborador-hero' ),

			// 2 · Métricas.
			'solucao_metrica_1_numero' => '5x',
			'solucao_metrica_1_rotulo' => 'mais rápida a tomada de decisão da contratação',
			'solucao_metrica_2_numero' => '75%',
			'solucao_metrica_2_rotulo' => 'mais rápida a integração da força de trabalho',
			'solucao_metrica_3_numero' => '95%',
			'solucao_metrica_3_rotulo' => 'menos trabalho manual por parte dos usuários',

			// 3 · Pilares.
			'solucao_pilares_eyebrow'  => 'pilares',
			'solucao_pilares_titulo'   => 'Automatize cada momento da jornada do colaborador',
			'solucao_pilares_1_icone'  => $this->img( 'jornada-do-colaborador-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Sincronize eventos automaticamente',
			'solucao_pilares_1_desc'   => 'Atualize todos os sistemas satélites a partir de eventos como admissão, promoção e desligamento.',
			'solucao_pilares_2_icone'  => $this->img( 'jornada-do-colaborador-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Acelere o onboarding',
			'solucao_pilares_2_desc'   => 'Reduza o tempo de ativação de novos colaboradores de dias para horas com processos conectados.',
			'solucao_pilares_3_icone'  => $this->img( 'jornada-do-colaborador-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Revogue acessos no desligamento',
			'solucao_pilares_3_desc'   => 'Elimine riscos garantindo que acessos físicos e digitais sejam removidos automaticamente.',

			// 4 · Casos de Uso.
			'solucao_casos_eyebrow'    => 'casos de uso',
			'solucao_casos_titulo'     => 'Automatize o ciclo de vida do colaborador',
			'solucao_casos_1_icone'    => $this->img( 'jornada-do-colaborador-caso-1' ),
			'solucao_casos_1_titulo'   => 'Automatize admissões completas',
			'solucao_casos_1_desc'     => 'Conecte HRIS, folha, e-mail, acessos, benefícios e LMS em uma única ativação.',
			'solucao_casos_2_icone'    => $this->img( 'jornada-do-colaborador-caso-2' ),
			'solucao_casos_2_titulo'   => 'Atualize mudanças de cargo',
			'solucao_casos_2_desc'     => 'Sincronize nível de acesso e faixa salarial automaticamente durante promoções e movimentações internas.',
			'solucao_casos_3_icone'    => $this->img( 'jornada-do-colaborador-caso-3' ),
			'solucao_casos_3_titulo'   => 'Execute desligamentos seguros',
			'solucao_casos_3_desc'     => 'Revogue acessos físicos e lógicos em minutos, reduzindo riscos após a saída do colaborador.',
			'solucao_casos_4_icone'    => $this->img( 'jornada-do-colaborador-caso-4' ),
			'solucao_casos_4_titulo'   => 'Analise dados de colaboradores',
			'solucao_casos_4_desc'     => 'Consolide informações do ciclo de vida para análises de turnover e tempo de casa.',
			'solucao_casos_5_icone'    => $this->img( 'jornada-do-colaborador-caso-5' ),
			'solucao_casos_5_titulo'   => 'Conecte sistemas satélites de RH',
			'solucao_casos_5_desc'     => 'Garanta que todos os sistemas relacionados recebam atualizações sem depender de checklists manuais.',
			'solucao_casos_cta_texto'  => 'Agende uma demonstração',
			'solucao_casos_cta_url'    => '/contato/',

			// 5 · Diferencial (antes dos Selos neste design).
			'solucao_dif_eyebrow'      => 'diferencial técnico',
			'solucao_dif_titulo'       => 'Segurança em cada mudança de status',
			'solucao_dif_corpo'        => 'Proteja dados sensíveis de colaboradores com controles de segurança e rastreabilidade em cada atualização.',
			'solucao_dif_topico_1'     => 'Mascaramento de PII em trânsito',
			'solucao_dif_topico_2'     => 'Auditoria completa de alterações',
			'solucao_dif_topico_3'     => 'Rastreabilidade de cada evento',
			'solucao_dif_imagem'       => $this->img( 'jornada-do-colaborador-diferencial' ),
			'solucao_dif_antes_selos'  => 1,

			// 6 · Aceleradores.
			'solucao_acel_eyebrow'     => 'aceleradores de integração',
			'solucao_acel_titulo'      => 'Modelo pronto para começar',
			'solucao_acel_corpo'       => 'Comece rapidamente com um fluxo pré-configurado que conecta pedido, faturamento, cobrança e conciliação financeira.',
			'solucao_acel_topico_1'    => 'Evento RH → todos os sistemas',
			'solucao_acel_topico_2'    => 'Admissão automatizada ponta a ponta',
			'solucao_acel_topico_3'    => 'Promoção e desligamento sincronizados',
			'solucao_acel_topico_4'    => 'E muito mais...',
			'solucao_acel_btn_texto'   => 'Começar agora',
			'solucao_acel_btn_url'     => '',
			'solucao_acel_imagem'      => $this->img( 'jornada-do-colaborador-aceleradores' ),

			// 7 · Selos.
			'solucao_selos_eyebrow'    => 'compliance & segurança',
			'solucao_selos_titulo'     => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'      => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_jornada_do_colaborador_faq( $post_id );

		WP_CLI::log( "  Jornada do Colaborador preenchido (ID: {$post_id})." );
	}

	/**
	 * Cria os posts cli_faq de Jornada do Colaborador (H2R) e vincula à solução.
	 *
	 * ATENÇÃO — texto provisório: o Figma mostra apenas as perguntas (o
	 * accordion está fechado no design). As respostas foram redigidas a partir
	 * do que a própria landing afirma nas seções anteriores e seguem pendentes
	 * de validação do cliente.
	 *
	 * @param int $post_id ID do post cli_solucao de Jornada do Colaborador.
	 * @return void
	 */
	protected function preencher_jornada_do_colaborador_faq( $post_id ) {
		$itens = array(
			array(
				'faq:h2r-desligamento-acessos',
				'Como garantir que o desligamento revogue todos os acessos automaticamente?',
				'<p>O desligamento registrado no sistema de RH vira um evento único que a integração distribui para todos os sistemas ligados àquele colaborador — diretório de identidade, e-mail, VPN, ERP, benefícios, controle de acesso físico e as ferramentas de negócio que ele usava. Cada revogação devolve uma confirmação, e o que não confirma fica visível como pendência em vez de passar despercebido. A janela entre a saída e o corte de acesso passa a ser medida em minutos, não em dias de checklist manual.</p>',
			),
			array(
				'faq:h2r-admissao-multiplos-sistemas',
				'É possível orquestrar admissão em múltiplos sistemas simultaneamente?',
				'<p>Sim. A admissão aprovada no HRIS dispara uma única ativação que cria o colaborador na folha, abre a conta de e-mail, provisiona os acessos conforme o cargo, matricula nos benefícios e inscreve nas trilhas do LMS. As etapas que não dependem umas das outras acontecem em paralelo, e as que dependem respeitam a ordem — o crachá só é solicitado depois que a identidade existe, por exemplo. O RH acompanha o andamento em um lugar só e o novo colaborador chega no primeiro dia com tudo liberado.</p>',
			),
			array(
				'faq:h2r-auditoria-ciclo-de-vida',
				'Como funciona a auditoria de mudanças no ciclo de vida do colaborador?',
				'<p>Toda mudança de status — admissão, promoção, transferência, alteração de faixa salarial e desligamento — é registrada com o evento que a originou, os sistemas atualizados, o horário de cada atualização e o resultado devolvido por cada um. Dados pessoais sensíveis trafegam mascarados, de modo que o histórico permanece auditável sem expor PII. Esse registro fica disponível para auditoria interna e externa e é a mesma base usada para análises de turnover e tempo de casa.</p>',
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

		WP_CLI::log( sprintf( '  Jornada do Colaborador FAQ: %d perguntas vinculadas.', count( $ids ) ) );
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

	// -------------------------------------------------------------------------
	// HUBSPOT CRM
	// -------------------------------------------------------------------------

	protected function preencher_solucao_hubspot_crm() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:hubspot-crm',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "hubspot-crm" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu HubSpot',
			'solucao_hero_titulo'     => 'Conecte o HubSpot ao ERP e ao restante do funil comercial',
			'solucao_hero_corpo'      => 'Integre CRM, marketing, e-commerce e faturamento para transformar oportunidades em operações conectadas sem depender apenas dos apps do Marketplace.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucoes/tecnologia/hubspot-crm/',
			'solucao_hero_imagem'     => $this->img( 'hubspot-crm-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Amplie o potencial do HubSpot CRM',
			'solucao_pilares_1_icone'  => $this->img( 'hubspot-crm-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Converta vendas automaticamente',
			'solucao_pilares_1_desc'   => 'Transforme negócios fechados em pedidos no ERP sem retrabalho.',
			'solucao_pilares_2_icone'  => $this->img( 'hubspot-crm-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Enriqueça dados comerciais',
			'solucao_pilares_2_desc'   => 'Atualize contatos e empresas com informações de outros sistemas.',
			'solucao_pilares_3_icone'  => $this->img( 'hubspot-crm-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Supere limitações do Marketplace',
			'solucao_pilares_3_desc'   => 'Crie integrações para cenários específicos do seu negócio.',

			// 3 · Casos de uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize processos do HubSpot CRM',
			'solucao_casos_1_icone'   => $this->img( 'hubspot-crm-caso-1' ),
			'solucao_casos_1_titulo'  => 'Envie vendas ao ERP',
			'solucao_casos_1_desc'    => 'Crie pedidos automaticamente após fechamento de negócios.',
			'solucao_casos_2_icone'   => $this->img( 'hubspot-crm-caso-2' ),
			'solucao_casos_2_titulo'  => 'Enriqueça contatos automaticamente',
			'solucao_casos_2_desc'    => 'Combine dados de produto, suporte e comportamento do cliente.',
			'solucao_casos_3_icone'   => $this->img( 'hubspot-crm-caso-3' ),
			'solucao_casos_3_titulo'  => 'Integre e-commerce ao CRM',
			'solucao_casos_3_desc'    => 'Disponibilize histórico de compras no relacionamento comercial.',
			'solucao_casos_4_icone'   => $this->img( 'hubspot-crm-caso-4' ),
			'solucao_casos_4_titulo'  => 'Consolide dados de marketing',
			'solucao_casos_4_desc'    => 'Centralize funil comercial e campanhas para análise estratégica.',
			'solucao_casos_5_icone'   => $this->img( 'hubspot-crm-caso-5' ),
			'solucao_casos_5_titulo'  => 'Conecte agentes de IA',
			'solucao_casos_5_desc'    => 'Disponibilize dados do CRM para agentes de IA utilizando APIs governadas e servidores MCP.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',

			// 5 · Diferencial técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações seguras para HubSpot',
			'solucao_dif_corpo'    => 'Utilize a API REST oficial do HubSpot com controle de acesso, tokens privados e permissões definidas por escopo.',
			'solucao_dif_topico_1' => 'Utilize APIs oficiais.',
			'solucao_dif_topico_2' => 'Controle permissões por escopo.',
			'solucao_dif_topico_3' => 'Proteja dados comerciais.',
			'solucao_dif_imagem'   => $this->img( 'hubspot-crm-dif' ),

			// 6 · Plataforma única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Conecte todo seu ecossistema comercial',
			'solucao_plat_corpo'    => 'Centralize CRM, ERP e sistemas operacionais em uma única camada de integração para acompanhar o crescimento da empresa.',
			'solucao_plat_topico_1' => 'Integre múltiplos sistemas.',
			'solucao_plat_topico_2' => 'Escale processos comerciais.',
			'solucao_plat_topico_3' => 'Evite conexões isoladas.',
			'solucao_plat_imagem'   => $this->img( 'hubspot-crm-plat' ),

			// 7 · Aceleradores.
			'solucao_acel_eyebrow'  => 'Aceleradores de integração',
			'solucao_acel_titulo'   => 'Comece com vendas conectadas',
			'solucao_acel_corpo'    => 'Utilize um modelo pronto para transformar negócios fechados no HubSpot em pedidos no ERP.',
			'solucao_acel_topico_1' => 'Automatize vendas rapidamente.',
			'solucao_acel_topico_2' => 'Reutilize fluxos comerciais.',
			'solucao_acel_topico_3' => 'Acelere novas integrações.',
			'solucao_acel_topico_4' => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'hubspot-crm-acel' ),
		);
		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		// 8 · FAQ.
		$faq_ids = $this->criar_faq_hubspot_crm();
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $faq_ids, $post_id );

		WP_CLI::log( "  HubSpot CRM preenchido (ID: {$post_id})." );
	}

	protected function criar_faq_hubspot_crm(): array {
		$items = array(
			array(
				'seed_key' => 'faq:hubspot-crm-erp',
				'titulo'   => 'Como sincronizar negócios fechados do HubSpot direto com o ERP?',
				'corpo'    => '<p>Ao fechar um negócio no HubSpot CRM, a CLI Connect detecta o evento via webhook e aciona automaticamente o fluxo de integração configurado — criando o pedido, contrato ou cadastro de cliente no ERP sem intervenção manual. O mapeamento de campos é definido uma vez e pode ser ajustado conforme as regras do seu processo comercial.</p>',
			),
			array(
				'seed_key' => 'faq:hubspot-crm-multiplos-portais',
				'titulo'   => 'É possível conectar múltiplos portais HubSpot de unidades diferentes?',
				'corpo'    => '<p>Sim. A CLI Connect suporta múltiplas conexões simultâneas com portais distintos do HubSpot CRM. Cada unidade de negócio opera com seu próprio conjunto de credenciais e fluxos independentes, centralizados em uma única plataforma de integração para facilitar a governança.</p>',
			),
			array(
				'seed_key' => 'faq:hubspot-crm-rate-limit',
				'titulo'   => 'Como lidar com limites de taxa (rate limit) da API?',
				'corpo'    => '<p>A CLI Connect gerencia automaticamente os limites de taxa da API do HubSpot por meio de filas e mecanismos de retry com backoff exponencial. Em picos de volume — como importações em lote ou campanhas de grande escala — os dados são processados de forma controlada, sem erros ou perda de registros.</p>',
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
		WP_CLI::log( sprintf( '  HubSpot CRM FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
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

	// -------------------------------------------------------------------------
	// ONCLICK ERP
	// -------------------------------------------------------------------------

	protected function preencher_solucao_onclick_erp() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:onclick-erp',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "onclick-erp" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu Onclick',
			'solucao_hero_titulo'     => 'Conecte o ERP Onclick ao e-commerce, indústria e distribuição',
			'solucao_hero_corpo'      => 'Integre varejo, marketplaces, vendas e processos fiscais para manter estoque, pedidos e operações sincronizados em todos os canais.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucoes/tecnologia/onclick-erp/',
			'solucao_hero_imagem'     => $this->img( 'onclick-erp-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Amplie o potencial do Onclick',
			'solucao_pilares_1_icone'  => $this->img( 'onclick-erp-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Conecte todos os módulos',
			'solucao_pilares_1_desc'   => 'Integre varejo, e-commerce, indústria, distribuição e serviços.',
			'solucao_pilares_2_icone'  => $this->img( 'onclick-erp-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Sincronize estoques omnichannel',
			'solucao_pilares_2_desc'   => 'Mantenha lojas físicas e canais digitais sempre atualizados.',
			'solucao_pilares_3_icone'  => $this->img( 'onclick-erp-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Centralize processos fiscais',
			'solucao_pilares_3_desc'   => 'Integre informações fiscais e contábeis automaticamente.',

			// 3 · Casos de uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize processos com Onclick',
			'solucao_casos_1_icone'   => $this->img( 'onclick-erp-caso-1' ),
			'solucao_casos_1_titulo'  => 'Sincronize pedidos digitais',
			'solucao_casos_1_desc'    => 'Envie pedidos de e-commerce diretamente para o ERP.',
			'solucao_casos_2_icone'   => $this->img( 'onclick-erp-caso-2' ),
			'solucao_casos_2_titulo'  => 'Integre marketplaces',
			'solucao_casos_2_desc'    => 'Centralize estoque e vendas de múltiplos canais.',
			'solucao_casos_3_icone'   => $this->img( 'onclick-erp-caso-3' ),
			'solucao_casos_3_titulo'  => 'Automatize força de vendas',
			'solucao_casos_3_desc'    => 'Conecte vendedores móveis aos processos do ERP.',
			'solucao_casos_4_icone'   => $this->img( 'onclick-erp-caso-4' ),
			'solucao_casos_4_titulo'  => 'Consolide ordens de serviço',
			'solucao_casos_4_desc'    => 'Centralize operações de serviços em um único fluxo.',
			'solucao_casos_5_icone'   => $this->img( 'onclick-erp-caso-5' ),
			'solucao_casos_5_titulo'  => 'Conecte agentes de IA',
			'solucao_casos_5_desc'    => 'Disponibilize dados do ERP para agentes de IA utilizando APIs governadas e servidores MCP.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',

			// 5 · Diferencial técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações adaptadas ao Onclick',
			'solucao_dif_corpo'    => 'Conecte diferentes módulos do Onclick com uma arquitetura preparada para varejo, indústria, distribuição e serviços.',
			'solucao_dif_topico_1' => 'Integre módulos especializados.',
			'solucao_dif_topico_2' => 'Adapte fluxos operacionais.',
			'solucao_dif_topico_3' => 'Conecte múltiplos canais.',
			'solucao_dif_imagem'   => $this->img( 'onclick-erp-dif' ),

			// 6 · Plataforma única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Unifique sua operação omnichannel',
			'solucao_plat_corpo'    => 'Centralize lojas, e-commerce e marketplaces em uma única camada de integração para evitar estoques desatualizados.',
			'solucao_plat_topico_1' => 'Sincronize canais de venda.',
			'solucao_plat_topico_2' => 'Centralize dados comerciais.',
			'solucao_plat_topico_3' => 'Evite processos desconectados.',
			'solucao_plat_imagem'   => $this->img( 'onclick-erp-plat' ),

			// 7 · Aceleradores.
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com e-commerce integrado',
			'solucao_acel_corpo'     => 'Utilize um modelo pronto para conectar Onclick aos principais canais digitais e marketplaces.',
			'solucao_acel_topico_1'  => 'Conecte canais rapidamente.',
			'solucao_acel_topico_2'  => 'Reutilize fluxos comerciais.',
			'solucao_acel_topico_3'  => 'Acelere novas integrações.',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'onclick-erp-acel' ),
		);
		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		// 8 · FAQ.
		$faq_ids = $this->criar_faq_onclick_erp();
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $faq_ids, $post_id );

		WP_CLI::log( '  Onclick ERP: todas as seções preenchidas.' );
	}

	protected function criar_faq_onclick_erp(): array {
		$items = array(
			array(
				'seed_key' => 'faq:onclick-erp-estoque-omnichannel',
				'titulo'   => 'Como sincronizar estoque entre loja física e e-commerce no Onclick?',
				'corpo'    => '<p>A CLI Connect monitora eventos de movimentação de estoque no Onclick ERP e replica as atualizações em tempo real para os canais digitais configurados — e-commerce próprio, marketplaces e PDV. O fluxo é bidirecional: vendas digitais também debitam o estoque do ERP automaticamente, eliminando divergências e rupturas.</p>',
			),
			array(
				'seed_key' => 'faq:onclick-erp-marketplaces',
				'titulo'   => 'É possível integrar múltiplos marketplaces simultaneamente?',
				'corpo'    => '<p>Sim. A CLI Connect suporta conexões simultâneas com múltiplos marketplaces — como Mercado Livre, Amazon, Shopee e outros — todos integrados ao Onclick ERP em um único projeto. Cada canal opera com seu próprio mapeamento de categorias, preços e regras de frete, com monitoramento centralizado na plataforma.</p>',
			),
			array(
				'seed_key' => 'faq:onclick-erp-forca-de-vendas',
				'titulo'   => 'Como funciona a automação da força de vendas?',
				'corpo'    => '<p>A CLI Connect conecta o aplicativo de força de vendas ao Onclick ERP, sincronizando pedidos, tabelas de preço, limites de crédito e disponibilidade de estoque em tempo real. Vendedores externos operam com informações atualizadas e os pedidos são transmitidos automaticamente para o ERP, sem necessidade de redigitação ou conciliação manual.</p>',
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
		WP_CLI::log( sprintf( '  Onclick ERP FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}

	/* =====================================================================
	   PROPZ
	   ===================================================================== */

	/**
	 * Preenche os campos ACF do post cli_solucao "Propz".
	 */
	protected function preencher_solucao_propz() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:propz',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "propz" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu Propz',
			'solucao_hero_titulo'     => 'Conecte a inteligência da Propz aos dados da sua empresa',
			'solucao_hero_corpo'      => 'Integre PDV, e-commerce e ERP para alimentar personalização de varejo com dados atualizados e ativar ofertas no canal certo.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucoes/tecnologia/propz/',
			'solucao_hero_imagem'     => $this->img( 'propz-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Transforme dados em experiências personalizadas',
			'solucao_pilares_1_icone'  => $this->img( 'propz-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Alimente dados em tempo real',
			'solucao_pilares_1_desc'   => 'Conecte vendas do PDV, e-commerce e ERP à Propz.',
			'solucao_pilares_2_icone'  => $this->img( 'propz-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Ative ofertas automaticamente',
			'solucao_pilares_2_desc'   => 'Envie campanhas personalizadas para canais digitais.',
			'solucao_pilares_3_icone'  => $this->img( 'propz-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Centralize histórico de compras',
			'solucao_pilares_3_desc'   => 'Unifique dados multi-canal para entender consumidores.',

			// 3 · Casos de uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize processos de personalização',
			'solucao_casos_1_icone'   => $this->img( 'propz-caso-1' ),
			'solucao_casos_1_titulo'  => 'Envie vendas para Propz',
			'solucao_casos_1_desc'    => 'Atualize inteligência de consumo com dados de venda.',
			'solucao_casos_2_icone'   => $this->img( 'propz-caso-2' ),
			'solucao_casos_2_titulo'  => 'Distribua ofertas personalizadas',
			'solucao_casos_2_desc'    => 'Ative campanhas Propz em app, SMS e e-mail.',
			'solucao_casos_3_icone'   => $this->img( 'propz-caso-3' ),
			'solucao_casos_3_titulo'  => 'Consolide compras multi-canal',
			'solucao_casos_3_desc'    => 'Unifique histórico para segmentação de clientes.',
			'solucao_casos_4_icone'   => $this->img( 'propz-caso-4' ),
			'solucao_casos_4_titulo'  => 'Meça resultados de campanhas',
			'solucao_casos_4_desc'    => 'Retorne dados de campanha ao CRM e ERP.',
			'solucao_casos_5_icone'   => $this->img( 'propz-caso-5' ),
			'solucao_casos_5_titulo'  => 'Conecte agentes de IA',
			'solucao_casos_5_desc'    => 'Disponibilize dados de consumidores para agentes de IA utilizando APIs governadas e servidores MCP.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',

			// 5 · Diferencial técnico.
			'solucao_dif_eyebrow' => 'diferencial técnico',
			'solucao_dif_titulo'  => 'Integrações seguras para dados de clientes',
			'solucao_dif_corpo'   => 'Conecte a Propz via API REST com governança de dados e controles alinhados às exigências da LGPD.',
			'solucao_dif_topico_1' => 'Proteja dados de consumidores.',
			'solucao_dif_topico_2' => 'Controle acessos por integração.',
			'solucao_dif_topico_3' => 'Governar dados conforme LGPD.',
			'solucao_dif_imagem'   => $this->img( 'propz-dif' ),

			// 6 · Plataforma única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Conecte dados e personalização',
			'solucao_plat_corpo'    => 'Centralize entrada e saída de dados entre Propz, canais digitais e sistemas internos sem processos manuais.',
			'solucao_plat_topico_1' => 'Integre canais de ativação.',
			'solucao_plat_topico_2' => 'Centralize dados comerciais.',
			'solucao_plat_topico_3' => 'Automatize jornadas personalizadas.',
			'solucao_plat_imagem'   => $this->img( 'propz-plat' ),

			// 7 · Aceleradores.
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com varejo personalizado',
			'solucao_acel_corpo'     => 'Utilize um modelo pronto para conectar vendas, Propz e canais de ativação em um fluxo completo.',
			'solucao_acel_topico_1'  => 'Conecte dados rapidamente.',
			'solucao_acel_topico_2'  => 'Reutilize fluxos de campanha.',
			'solucao_acel_topico_3'  => 'Acelere personalização comercial.',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'propz-acel' ),
		);
		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		// 8 · FAQ.
		$faq_ids = $this->criar_faq_propz();
		if ( ! empty( $faq_ids ) ) {
			update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
			update_field( 'solucao_faq_itens', $faq_ids, $post_id );
		}

		WP_CLI::log( '  Propz: todas as seções preenchidas.' );
	}

	/**
	 * Cria/atualiza os posts cli_faq para a solução Propz.
	 *
	 * @return int[]
	 */
	protected function criar_faq_propz(): array {
		$items = array(
			array(
				'seed_key' => 'faq:propz-dados-venda',
				'titulo'   => 'Como alimentar a Propz com dados de venda em tempo real?',
				'corpo'    => '<p>A CLI Connect monitora eventos de venda no PDV, e-commerce e ERP e os envia automaticamente para a Propz em tempo real. Cada transação atualiza o histórico de compras do consumidor, permitindo que a plataforma recalcule ofertas e segmentações sem atrasos — sem nenhuma exportação manual ou lote periódico.</p>',
			),
			array(
				'seed_key' => 'faq:propz-ofertas-canais',
				'titulo'   => 'É possível devolver ofertas geradas pela Propz para o app/SMS automaticamente?',
				'corpo'    => '<p>Sim. A CLI Connect recebe as campanhas publicadas pela Propz e as distribui automaticamente para os canais configurados — aplicativo, SMS, e-mail e push. O fluxo é bidirecional: dados de venda entram na Propz e as ofertas personalizadas saem para os canais digitais sem intervenção manual.</p>',
			),
			array(
				'seed_key' => 'faq:propz-atribuicao-resultados',
				'titulo'   => 'Como funciona a atribuição de resultados de campanha no CRM/ERP?',
				'corpo'    => '<p>A CLI Connect captura os eventos de conversão registrados pela Propz — compras realizadas após ativação de oferta — e os retorna ao CRM e ao ERP com os atributos de campanha. Isso permite que gestores visualizem ROI, taxa de conversão e receita incremental diretamente nas ferramentas de gestão, sem cruzamentos manuais de dados.</p>',
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
		WP_CLI::log( sprintf( '  Propz FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}

	/* =====================================================================
	   MICROSOFT TEAMS
	   ===================================================================== */

	/**
	 * Preenche os campos ACF do post cli_solucao "Microsoft Teams".
	 */
	protected function preencher_solucao_microsoft_teams() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:microsoft-teams',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "microsoft-teams" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu Microsoft Teams',
			'solucao_hero_titulo'     => 'Transforme o Microsoft Teams em canal de ação para processos',
			'solucao_hero_corpo'      => 'Conecte aprovações, notificações e agentes de IA aos sistemas internos para acelerar decisões sem depender de e-mails ou processos manuais.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucoes/tecnologia/microsoft-teams/',
			'solucao_hero_imagem'     => $this->img( 'microsoft-teams-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Leve processos para onde as equipes trabalham',
			'solucao_pilares_1_icone'  => $this->img( 'microsoft-teams-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Aprove processos no Teams',
			'solucao_pilares_1_desc'   => 'Execute aprovações diretamente em cards adaptativos.',
			'solucao_pilares_2_icone'  => $this->img( 'microsoft-teams-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Converse com sistemas internos',
			'solucao_pilares_2_desc'   => 'Permita bots consultarem dados corporativos no Teams.',
			'solucao_pilares_3_icone'  => $this->img( 'microsoft-teams-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Reduza trocas manuais',
			'solucao_pilares_3_desc'   => 'Substitua e-mails por ações automatizadas.',

			// 3 · Casos de uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize processos dentro do Teams',
			'solucao_casos_1_icone'   => $this->img( 'microsoft-teams-caso-1' ),
			'solucao_casos_1_titulo'  => 'Aprove pedidos no Teams',
			'solucao_casos_1_desc'    => 'Envie aprovações de compras ou férias ao ERP.',
			'solucao_casos_2_icone'   => $this->img( 'microsoft-teams-caso-2' ),
			'solucao_casos_2_titulo'  => 'Alerte incidentes automaticamente',
			'solucao_casos_2_desc'    => 'Notifique equipes sobre eventos de ServiceNow ou Freshservice.',
			'solucao_casos_3_icone'   => $this->img( 'microsoft-teams-caso-3' ),
			'solucao_casos_3_titulo'  => 'Consulte sistemas com IA',
			'solucao_casos_3_desc'    => 'Permita bots consultarem estoque e pedidos.',
			'solucao_casos_4_icone'   => $this->img( 'microsoft-teams-caso-4' ),
			'solucao_casos_4_titulo'  => 'Monitore eventos críticos',
			'solucao_casos_4_desc'    => 'Dispare alertas de SLA e operações importantes.',
			'solucao_casos_5_icone'   => $this->img( 'microsoft-teams-caso-5' ),
			'solucao_casos_5_titulo'  => 'Conecte agentes de IA',
			'solucao_casos_5_desc'    => 'Disponibilize dados corporativos para agentes de IA via Teams utilizando APIs governadas e servidores MCP.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',

			// 5 · Diferencial técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integrações seguras com Microsoft Teams',
			'solucao_dif_corpo'    => 'Conecte Teams via Microsoft Graph API e Bot Framework usando autenticação Azure AD com controle por equipe e canal.',
			'solucao_dif_topico_1' => 'Utilize Microsoft Graph API.',
			'solucao_dif_topico_2' => 'Autentique via Azure AD.',
			'solucao_dif_topico_3' => 'Controle acessos por canal.',
			'solucao_dif_imagem'   => $this->img( 'microsoft-teams-dif' ),

			// 6 · Plataforma única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Conecte comunicação e operação',
			'solucao_plat_corpo'    => 'Centralize eventos de negócio em uma plataforma única para aproximar equipes dos sistemas corporativos.',
			'solucao_plat_topico_1' => 'Integre sistemas internos.',
			'solucao_plat_topico_2' => 'Centralize notificações operacionais.',
			'solucao_plat_topico_3' => 'Automatize ações no Teams.',
			'solucao_plat_imagem'   => $this->img( 'microsoft-teams-plat' ),

			// 7 · Aceleradores.
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com processos conectados',
			'solucao_acel_corpo'     => 'Utilize um modelo pronto para transformar processos corporativos em aprovações e notificações dentro do Teams.',
			'solucao_acel_topico_1'  => 'Configure fluxos rapidamente.',
			'solucao_acel_topico_2'  => 'Reutilize modelos aprovados.',
			'solucao_acel_topico_3'  => 'Acelere decisões operacionais.',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'microsoft-teams-acel' ),
		);
		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		// 8 · FAQ.
		$faq_ids = $this->criar_faq_microsoft_teams();
		if ( ! empty( $faq_ids ) ) {
			update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
			update_field( 'solucao_faq_itens', $faq_ids, $post_id );
		}

		WP_CLI::log( '  Microsoft Teams: todas as seções preenchidas.' );
	}

	/**
	 * Cria/atualiza os posts cli_faq para a solução Microsoft Teams.
	 *
	 * @return int[]
	 */
	protected function criar_faq_microsoft_teams(): array {
		$items = array(
			array(
				'seed_key' => 'faq:microsoft-teams-aprovacao-card',
				'titulo'   => 'Como criar uma aprovação de processo direto em um card do Teams?',
				'corpo'    => '<p>A CLI Connect conecta seus sistemas corporativos — ERP, CRM ou ITSM — ao Microsoft Teams via Bot Framework e Microsoft Graph API. Ao disparar um evento de aprovação (compra, férias, proposta), a plataforma envia automaticamente um card adaptativo no canal configurado. O aprovador clica "Aprovar" ou "Rejeitar" diretamente no Teams e a resposta é gravada no sistema de origem sem nenhuma troca de e-mails.</p>',
			),
			array(
				'seed_key' => 'faq:microsoft-teams-bot-erp',
				'titulo'   => 'É possível ter um bot de IA no Teams consultando o ERP?',
				'corpo'    => '<p>Sim. A CLI Connect expõe dados do ERP como estoque, pedidos e status de clientes como endpoints seguros consumíveis por bots do Teams. Com autenticação via Azure AD, membros da equipe consultam informações corporativas conversando com o bot no Teams — sem precisar acessar o sistema legado diretamente. O bot pode também acionar ações, como abrir ordens de serviço ou atualizar registros.</p>',
			),
			array(
				'seed_key' => 'faq:microsoft-teams-azure-ad',
				'titulo'   => 'Como funciona a autenticação via Azure AD?',
				'corpo'    => '<p>A CLI Connect utiliza o fluxo OAuth 2.0 do Azure Active Directory para autenticar chamadas entre o Teams e os sistemas integrados. Cada integração é registrada como um aplicativo no Azure AD com escopos de permissão específicos por equipe e canal. Isso garante que apenas usuários autorizados consigam disparar ações ou consultar dados, respeitando as políticas de segurança corporativa sem expor credenciais nos fluxos.</p>',
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
		WP_CLI::log( sprintf( '  Microsoft Teams FAQ: %d perguntas vinculadas.', count( $ids ) ) );
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

	/**
	 * Preenche os campos ACF da solução Snowflake.
	 */
	protected function preencher_solucao_snowflake() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:snowflake',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "snowflake" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu Snowflake',
			'solucao_hero_titulo'     => 'Conecte Snowflake ao core do negócio com dados sempre prontos para análise',
			'solucao_hero_corpo'      => 'Integre Snowflake aos seus sistemas transacionais, CRM e ERP para alimentar pipelines analíticos em tempo real e eliminar silos de dados que travam decisões estratégicas.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/snowflake/',
			'solucao_hero_imagem'     => $this->img( 'snowflake-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Dados unificados, decisões mais rápidas',
			'solucao_pilares_1_icone'  => $this->img( 'snowflake-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Ingestão contínua de dados',
			'solucao_pilares_1_desc'   => 'Alimente o Snowflake com dados de ERP, CRM e sistemas legados de forma automatizada e confiável.',
			'solucao_pilares_2_icone'  => $this->img( 'snowflake-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Transformações sem código extra',
			'solucao_pilares_2_desc'   => 'Processe, normalize e enriqueça dados antes de carregá-los no Snowflake usando fluxos visuais da Boomi.',
			'solucao_pilares_3_icone'  => $this->img( 'snowflake-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Governança centralizada',
			'solucao_pilares_3_desc'   => 'Controle quais dados chegam ao Snowflake, com rastreabilidade de origem e conformidade com LGPD e GDPR.',

			// 3 · Casos de uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Transforme dados em vantagem competitiva',
			'solucao_casos_1_icone'   => $this->img( 'snowflake-caso-1' ),
			'solucao_casos_1_titulo'  => 'Sincronize ERP com o Data Cloud',
			'solucao_casos_1_desc'    => 'Transfira transações financeiras e operacionais do ERP para o Snowflake em tempo real para análises atualizadas.',
			'solucao_casos_2_icone'   => $this->img( 'snowflake-caso-2' ),
			'solucao_casos_2_titulo'  => 'Unifique dados de CRM',
			'solucao_casos_2_desc'    => 'Consolide leads, oportunidades e histórico de clientes no Snowflake para visões 360° do pipeline comercial.',
			'solucao_casos_3_icone'   => $this->img( 'snowflake-caso-3' ),
			'solucao_casos_3_titulo'  => 'Automatize pipelines de marketing',
			'solucao_casos_3_desc'    => 'Alimente modelos de atribuição e segmentação com dados de campanhas centralizados no Snowflake.',
			'solucao_casos_4_icone'   => $this->img( 'snowflake-caso-4' ),
			'solucao_casos_4_titulo'  => 'Integre dados de e-commerce',
			'solucao_casos_4_desc'    => 'Envie pedidos, devoluções e comportamento de navegação para o Snowflake e alimente dashboards de vendas em tempo real.',
			'solucao_casos_5_icone'   => $this->img( 'snowflake-caso-5' ),
			'solucao_casos_5_titulo'  => 'Alimente agentes de IA',
			'solucao_casos_5_desc'    => 'Disponibilize dados estruturados do Snowflake para modelos de machine learning e agentes de IA que automatizam decisões operacionais.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',

			// 4 · Selos.
			'solucao_selos_eyebrow'   => 'compliance & segurança',
			'solucao_selos_titulo'    => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'     => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',

			// 5 · Diferencial Técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Integração nativa com a Snowflake Data Cloud',
			'solucao_dif_corpo'    => 'Conecte o Snowflake usando o conector certificado da Boomi com suporte a autenticação OAuth 2.0 e key-pair, garantindo segurança máxima no transporte de dados.',
			'solucao_dif_topico_1' => 'Conector certificado Boomi para Snowflake',
			'solucao_dif_topico_2' => 'Autenticação OAuth 2.0 e key-pair',
			'solucao_dif_topico_3' => 'Suporte a bulk load e streaming',
			'solucao_dif_imagem'   => $this->img( 'snowflake-dif' ),

			// 6 · Plataforma Única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Um hub central para todos os seus dados',
			'solucao_plat_corpo'    => 'Com a Boomi como camada de integração, você conecta qualquer sistema ao Snowflake sem scripts ETL customizados, acelerando a entrega de insights e reduzindo a dívida técnica de pipelines fragmentados.',
			'solucao_plat_topico_1' => 'Elimine pipelines ETL fragmentados',
			'solucao_plat_topico_2' => 'Conecte qualquer sistema ao Snowflake',
			'solucao_plat_topico_3' => 'Acelere time-to-insight da equipe de dados',
			'solucao_plat_imagem'   => $this->img( 'snowflake-plat' ),

			// 7 · Aceleradores.
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece a ingerir dados no Snowflake hoje',
			'solucao_acel_corpo'     => 'Use modelos prontos para conectar ERP, CRM e sistemas operacionais ao Snowflake com fluxos estruturados e rastreabilidade de ponta a ponta.',
			'solucao_acel_topico_1'  => 'Conecte ERP e CRM rapidamente',
			'solucao_acel_topico_2'  => 'Reutilize pipelines de dados',
			'solucao_acel_topico_3'  => 'Acelere projetos de Data Cloud',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'snowflake-acel' ),
		);
		foreach ( $campos as $chave => $valor ) {
			update_field( $chave, $valor, $post_id );
		}

		// 8 · FAQ.
		$faq_ids = $this->criar_faq_snowflake( $post_id );
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $faq_ids, $post_id );

		WP_CLI::log( '  Snowflake: todas as seções preenchidas.' );
	}

	/**
	 * Cria/atualiza os posts cli_faq para a solução Snowflake.
	 *
	 * @return int[]
	 */
	protected function criar_faq_snowflake( $post_id ) {
		$itens = array(
			array(
				'faq:snowflake-ingestao',
				'Como a CLI Connect alimenta o Snowflake com dados do ERP?',
				'<p>A integração usa o conector certificado da Boomi para o Snowflake, transferindo dados transacionais do ERP — como pedidos, faturamento e estoque — de forma contínua e rastreável. Os fluxos são configurados visualmente, sem necessidade de scripts ETL customizados, e suportam bulk load para grandes volumes e streaming para dados em tempo real.</p>',
			),
			array(
				'faq:snowflake-seguranca',
				'Quais mecanismos de segurança são usados na conexão com o Snowflake?',
				'<p>A CLI Connect utiliza autenticação OAuth 2.0 e key-pair para garantir que apenas sistemas autorizados acessem o Snowflake. Todo tráfego é criptografado em trânsito e os acessos são auditados, mantendo conformidade com LGPD, GDPR e as políticas internas de governança de dados da empresa.</p>',
			),
			array(
				'faq:snowflake-transformacoes',
				'É possível transformar dados antes de carregá-los no Snowflake?',
				'<p>Sim. Os fluxos de integração da Boomi permitem normalizar, enriquecer e filtrar dados antes de enviá-los ao Snowflake. Isso inclui conversões de formato, deduplicação, validação de campos e mapeamento de esquemas, garantindo que apenas dados de qualidade cheguem ao Data Warehouse para análise.</p>',
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

		WP_CLI::log( sprintf( '  Snowflake FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}

	/**
	 * Preenche os campos ACF da solução Databricks.
	 */
	protected function preencher_solucao_databricks() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:databricks',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Post cli_solucao "databricks" não encontrado.' );
			return;
		}
		$post_id = (int) $posts[0];
		$campos  = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'para o seu Databricks',
			'solucao_hero_titulo'     => 'Conecte o Databricks ao core do negócio com dados sempre prontos para IA',
			'solucao_hero_corpo'      => 'Integre Databricks aos seus sistemas transacionais, ERP e CRM para alimentar modelos de machine learning em tempo real e transformar dados corporativos em decisões inteligentes.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/databricks/',
			'solucao_hero_imagem'     => $this->img( 'databricks-hero' ),

			// 2 · Pilares.
			'solucao_pilares_titulo'   => 'Prepare dados para inteligência avançada',
			'solucao_pilares_1_icone'  => $this->img( 'databricks-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Ingestione dados continuamente',
			'solucao_pilares_1_desc'   => 'Conecte sistemas operacionais ao Databricks em tempo real.',
			'solucao_pilares_2_icone'  => $this->img( 'databricks-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Alimente modelos de IA',
			'solucao_pilares_2_desc'   => 'Disponibilize dados atualizados para machine learning e agentes inteligentes.',
			'solucao_pilares_3_icone'  => $this->img( 'databricks-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Transforme previsões em ações',
			'solucao_pilares_3_desc'   => 'Retorne resultados analíticos para ERP e CRM automaticamente.',

			// 3 · Casos de uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Aplique inteligência com dados conectados',
			'solucao_casos_1_icone'   => $this->img( 'databricks-caso-1' ),
			'solucao_casos_1_titulo'  => 'Treine modelos preditivos',
			'solucao_casos_1_desc'    => 'Use dados de ERP e CRM para prever churn, demanda e riscos.',
			'solucao_casos_2_icone'   => $this->img( 'databricks-caso-2' ),
			'solucao_casos_2_titulo'  => 'Dê contexto aos agentes de IA',
			'solucao_casos_2_desc'    => 'Alimente agentes inteligentes com informações corporativas atualizadas.',
			'solucao_casos_3_icone'   => $this->img( 'databricks-caso-3' ),
			'solucao_casos_3_titulo'  => 'Envie scores para sistemas',
			'solucao_casos_3_desc'    => 'Retorne resultados de modelos para apoiar decisões operacionais.',
			'solucao_casos_4_icone'   => $this->img( 'databricks-caso-4' ),
			'solucao_casos_4_titulo'  => 'Consolide dados analíticos',
			'solucao_casos_4_desc'    => 'Una múltiplas fontes para análises corporativas avançadas.',
			'solucao_casos_5_icone'   => $this->img( 'databricks-caso-5' ),
			'solucao_casos_5_titulo'  => 'Conecte agentes de IA',
			'solucao_casos_5_desc'    => 'Disponibilize dados corporativos para agentes de IA sem expor o core dos sistemas.',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',

			// 4 · Selos.
			'solucao_selos_eyebrow'   => 'compliance & segurança',
			'solucao_selos_titulo'    => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'     => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',

			// 5 · Diferencial Técnico.
			'solucao_dif_eyebrow'  => 'diferencial técnico',
			'solucao_dif_titulo'   => 'Dados preparados para IA com segurança',
			'solucao_dif_corpo'    => 'Conecte o Databricks via APIs e Delta Sharing mantendo autenticação segura, governança e proteção dos dados sensíveis utilizados pelos modelos.',
			'solucao_dif_topico_1' => 'Utilize APIs oficiais do Databricks',
			'solucao_dif_topico_2' => 'Proteja dados sensíveis',
			'solucao_dif_topico_3' => 'Controle acessos por token',
			'solucao_dif_imagem'   => $this->img( 'databricks-dif' ),

			// 6 · Plataforma Única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Conecte dados e decisões em uma plataforma',
			'solucao_plat_corpo'    => 'Centralize a conexão entre sistemas operacionais, Databricks e aplicações de negócio para fechar o ciclo entre dados e ações.',
			'solucao_plat_topico_1' => 'Integre dados corporativos',
			'solucao_plat_topico_2' => 'Reutilize pipelines existentes',
			'solucao_plat_topico_3' => 'Aplique IA nos processos',
			'solucao_plat_imagem'   => $this->img( 'databricks-plat' ),

			// 7 · Aceleradores.
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com fluxos de IA estruturados',
			'solucao_acel_corpo'     => 'Utilize um modelo pronto para levar dados ao Databricks, gerar resultados analíticos e devolver ações aos sistemas corporativos.',
			'solucao_acel_topico_1'  => 'Conecte dados rapidamente',
			'solucao_acel_topico_2'  => 'Acelere treinamentos de modelos',
			'solucao_acel_topico_3'  => 'Automatize ações inteligentes',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'databricks-acel' ),
		);
		foreach ( $campos as $chave => $valor ) {
			update_field( $chave, $valor, $post_id );
		}

		// 8 · FAQ.
		$faq_ids = $this->criar_faq_databricks( $post_id );
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $faq_ids, $post_id );

		WP_CLI::log( '  Databricks: todas as seções preenchidas.' );
	}

	/**
	 * Cria/atualiza os posts cli_faq para a solução Databricks.
	 *
	 * @return int[]
	 */
	protected function criar_faq_databricks( $post_id ) {
		$itens = array(
			array(
				'faq:databricks-ingestao',
				'Como levar dados operacionais para o Databricks em tempo real?',
				'<p>A CLI Connect usa o conector certificado da Boomi para o Databricks, transferindo dados de ERP, CRM e sistemas legados de forma contínua e rastreável. Os fluxos são configurados visualmente, sem scripts ETL customizados, e suportam ingestão em lote e streaming para manter os dados sempre atualizados para os modelos de machine learning.</p>',
			),
			array(
				'faq:databricks-writeback',
				'É possível devolver o resultado de um modelo de IA para o ERP automaticamente?',
				'<p>Sim. Após o Databricks gerar scores, previsões ou recomendações, a integração escreve os resultados de volta nos sistemas de origem — ERP, CRM ou plataformas operacionais — de forma automatizada. Isso fecha o ciclo entre dado e ação sem intervenção manual, acelerando decisões em vendas, supply chain e finanças.</p>',
			),
			array(
				'faq:databricks-governanca',
				'Como funciona a governança de dados sensíveis nesse fluxo?',
				'<p>A CLI Connect utiliza as APIs oficiais do Databricks com autenticação por token e Delta Sharing para controlar precisamente quais dados chegam aos modelos. Campos sensíveis podem ser mascarados ou excluídos antes da ingestão, garantindo conformidade com LGPD e GDPR e mantendo auditoria completa de cada acesso ao ambiente analítico.</p>',
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

		WP_CLI::log( sprintf( '  Databricks FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}

	// ─────────────────────────────────────────────
	// AWS
	// ─────────────────────────────────────────────

	protected function preencher_solucao_aws() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:aws',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Solução AWS não encontrada — pulando.' );
			return;
		}
		$post_id = (int) $posts[0];

		$campos = array(
			// 1 Hero
			'solucao_hero_eyebrow'    => 'para o seu AWS',
			'solucao_hero_titulo'     => 'Acelere a adoção da AWS sem reescrever integrações existentes',
			'solucao_hero_corpo'      => 'Conecte serviços AWS, ERPs, CRMs e sistemas legados em uma mesma plataforma para evoluir sua arquitetura cloud sem interromper operações atuais.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/aws/',
			'solucao_hero_imagem'     => $this->img( 'aws-hero' ),

			// 2 Pilares
			'solucao_pilares_titulo'  => 'Evolua sua arquitetura cloud com segurança',
			'solucao_pilares_1_icone' => $this->img( 'aws-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Conecte serviços AWS nativamente',
			'solucao_pilares_1_desc'  => 'Use conectores prontos para integrar serviços AWS sem desenvolvimento específico.',
			'solucao_pilares_2_icone' => $this->img( 'aws-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Adote eventos em escala',
			'solucao_pilares_2_desc'  => 'Implemente arquiteturas orientadas a eventos sem reconstruir integrações existentes.',
			'solucao_pilares_3_icone' => $this->img( 'aws-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Migre de forma incremental',
			'solucao_pilares_3_desc'  => 'Conecte sistemas legados e workloads AWS durante sua evolução cloud.',

			// 3 Casos
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize processos conectados à AWS',
			'solucao_casos_1_icone'   => $this->img( 'aws-caso-1' ),
			'solucao_casos_1_titulo'  => 'Dispare fluxos por eventos',
			'solucao_casos_1_desc'    => 'Acione pipelines AWS a partir de eventos de ERP e CRM.',
			'solucao_casos_2_icone'   => $this->img( 'aws-caso-2' ),
			'solucao_casos_2_titulo'  => 'Orquestre funções Lambda',
			'solucao_casos_2_desc'    => 'Inclua funções serverless em fluxos completos de integração.',
			'solucao_casos_3_icone'   => $this->img( 'aws-caso-3' ),
			'solucao_casos_3_titulo'  => 'Desacople sistemas com filas',
			'solucao_casos_3_desc'    => 'Use SNS e SQS para conectar aplicações com mais flexibilidade.',
			'solucao_casos_4_icone'   => $this->img( 'aws-caso-4' ),
			'solucao_casos_4_titulo'  => 'Monitore operações cloud',
			'solucao_casos_4_desc'    => 'Acompanhe pipelines AWS e legados em uma visão centralizada.',
			'solucao_casos_5_icone'   => $this->img( 'aws-caso-5' ),
			'solucao_casos_5_titulo'  => 'Migre workloads gradualmente',
			'solucao_casos_5_desc'    => 'Evolua para ECS sem interromper integrações existentes.',
			'solucao_casos_cta_texto' => 'Fale com especialista',
			'solucao_casos_cta_url'   => '/contato/',

			// 4 Selos
			'solucao_selos_eyebrow'   => 'compliance & segurança',
			'solucao_selos_titulo'    => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'     => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',

			// 5 Diferencial
			'solucao_dif_eyebrow'    => 'diferencial técnico',
			'solucao_dif_titulo'     => 'Integrações AWS com segurança corporativa',
			'solucao_dif_corpo'      => 'Conecte serviços AWS utilizando autenticação IAM/STS, gestão de chaves via KMS e criptografia para proteger dados durante toda a operação.',
			'solucao_dif_topico_1'   => 'Autentique conexões via IAM',
			'solucao_dif_topico_2'   => 'Proteja dados com KMS',
			'solucao_dif_topico_3'   => 'Criptografe dados em trânsito',
			'solucao_dif_imagem'     => $this->img( 'aws-dif' ),

			// 6 Plataforma
			'solucao_plat_eyebrow'   => 'plataforma única',
			'solucao_plat_titulo'    => 'Conecte legado e cloud em um só lugar',
			'solucao_plat_corpo'     => 'Centralize a comunicação entre sistemas existentes e novos serviços AWS para acelerar a transformação sem criar integrações descartáveis.',
			'solucao_plat_topico_1'  => 'Integre sistemas legados',
			'solucao_plat_topico_2'  => 'Conecte serviços cloud-native',
			'solucao_plat_topico_3'  => 'Evolua sem interrupções',
			'solucao_plat_imagem'    => $this->img( 'aws-plat' ),

			// 7 Aceleradores
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com eventos AWS estruturados',
			'solucao_acel_corpo'     => 'Utilize um modelo pronto para conectar eventos de negócio ao EventBridge, Lambda e SNS acelerando sua arquitetura orientada a eventos.',
			'solucao_acel_topico_1'  => 'Configure eventos rapidamente',
			'solucao_acel_topico_2'  => 'Reaproveite fluxos existentes',
			'solucao_acel_topico_3'  => 'Acelere adoção cloud',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'aws-acel' ),
		);

		foreach ( $campos as $chave => $valor ) {
			update_field( $chave, $valor, $post_id );
		}

		$faq_ids = $this->criar_faq_aws( $post_id );
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $faq_ids, $post_id );

		WP_CLI::log( '  AWS: todas as seções preenchidas.' );
	}

	protected function criar_faq_aws( int $solucao_id ): array {
		$itens = array(
			array(
				'faq:aws:1',
				'Quais serviços AWS têm conector nativo na CLI Connect powered by Boomi?',
				'A plataforma oferece conectores nativos para os principais serviços AWS, incluindo S3, Lambda, SQS, SNS, EventBridge, DynamoDB, RDS, API Gateway e mais. Esses conectores eliminam a necessidade de desenvolvimento específico para integrar seu ecossistema AWS.',
			),
			array(
				'faq:aws:2',
				'Como a CLI Connect powered by Boomi ajuda na migração incremental para AWS?',
				'A plataforma permite conectar sistemas legados e workloads AWS simultaneamente, possibilitando uma migração gradual sem interromper operações. Você pode evoluir sua arquitetura em etapas, mantendo integrações existentes funcionando enquanto novos serviços cloud são adotados.',
			),
			array(
				'faq:aws:3',
				'Como funciona a autenticação via IAM/STS?',
				'A integração utiliza roles e políticas IAM para autenticar conexões com serviços AWS, com suporte a STS para credenciais temporárias. Isso garante acesso controlado e auditável, seguindo as melhores práticas de segurança da AWS sem armazenar credenciais fixas.',
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

		WP_CLI::log( sprintf( '  AWS FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}

	// ─────────────────────────────────────────────
	// Microsoft Azure
	// ─────────────────────────────────────────────

	protected function preencher_solucao_microsoft_azure() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:microsoft-azure',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Solução Microsoft Azure não encontrada — pulando.' );
			return;
		}
		$post_id = (int) $posts[0];

		$campos = array(
			// 1 Hero
			'solucao_hero_eyebrow'    => 'para o seu Azure',
			'solucao_hero_titulo'     => 'Acelere a adoção do Azure mantendo seu core conectado',
			'solucao_hero_corpo'      => 'Integre serviços Azure, SAP, Salesforce e sistemas legados em uma única plataforma para evoluir sua arquitetura cloud sem interromper operações existentes.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/microsoft-azure/',
			'solucao_hero_imagem'     => $this->img( 'microsoft-azure-hero' ),

			// 2 Pilares
			'solucao_pilares_titulo'   => 'Evolua sua arquitetura Microsoft com escala',
			'solucao_pilares_1_icone'  => $this->img( 'microsoft-azure-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Conecte serviços Azure nativamente',
			'solucao_pilares_1_desc'   => 'Utilize conectores prontos para dados e mensageria Azure.',
			'solucao_pilares_2_icone'  => $this->img( 'microsoft-azure-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Acelere eventos em tempo real',
			'solucao_pilares_2_desc'   => 'Adote arquiteturas orientadas a eventos sem reconstruir integrações.',
			'solucao_pilares_3_icone'  => $this->img( 'microsoft-azure-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Integre o ecossistema Microsoft',
			'solucao_pilares_3_desc'   => 'Conecte Azure, Dynamics 365, Teams e Azure AD.',

			// 3 Casos
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize processos conectados ao Azure',
			'solucao_casos_1_icone'   => $this->img( 'microsoft-azure-caso-1' ),
			'solucao_casos_1_titulo'  => 'Capture eventos em tempo real',
			'solucao_casos_1_desc'    => 'Envie eventos de negócio para analytics usando Event Hubs.',
			'solucao_casos_2_icone'   => $this->img( 'microsoft-azure-caso-2' ),
			'solucao_casos_2_titulo'  => 'Desacople sistemas com filas',
			'solucao_casos_2_desc'    => 'Use Service Bus para conectar legados e novos serviços.',
			'solucao_casos_3_icone'   => $this->img( 'microsoft-azure-caso-3' ),
			'solucao_casos_3_titulo'  => 'Armazene dados com baixa latência',
			'solucao_casos_3_desc'    => 'Utilize CosmosDB para cenários globais de alta performance.',
			'solucao_casos_4_icone'   => $this->img( 'microsoft-azure-caso-4' ),
			'solucao_casos_4_titulo'  => 'Automatize arquivos corporativos',
			'solucao_casos_4_desc'    => 'Processe documentos usando Blob Storage e DataLake.',
			'solucao_casos_5_icone'   => $this->img( 'microsoft-azure-caso-5' ),
			'solucao_casos_5_titulo'  => 'Centralize gestão de segredos',
			'solucao_casos_5_desc'    => 'Proteja credenciais de integração com Azure Key Vault.',
			'solucao_casos_cta_texto' => 'Fale com especialista',
			'solucao_casos_cta_url'   => '/contato/',

			// 4 Selos
			'solucao_selos_eyebrow'   => 'compliance & segurança',
			'solucao_selos_titulo'    => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'     => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',

			// 5 Diferencial
			'solucao_dif_eyebrow'    => 'diferencial técnico',
			'solucao_dif_titulo'     => 'Integrações Azure com segurança nativa',
			'solucao_dif_corpo'      => 'Conecte serviços Azure usando OAuth2, Azure AD e Key Vault para controlar acessos e proteger credenciais em todos os fluxos.',
			'solucao_dif_topico_1'   => 'Autentique via Azure AD',
			'solucao_dif_topico_2'   => 'Proteja segredos com Key Vault',
			'solucao_dif_topico_3'   => 'Controle acessos centralmente',
			'solucao_dif_imagem'     => $this->img( 'microsoft-azure-dif' ),

			// 6 Plataforma
			'solucao_plat_eyebrow'   => 'plataforma única',
			'solucao_plat_titulo'    => 'Conecte todo ecossistema Microsoft',
			'solucao_plat_corpo'     => 'Centralize integrações entre Azure, aplicações Microsoft e sistemas corporativos para acelerar novas iniciativas sem complexidade adicional.',
			'solucao_plat_topico_1'  => 'Integre dados e aplicações',
			'solucao_plat_topico_2'  => 'Reaproveite pipelines existentes',
			'solucao_plat_topico_3'  => 'Evolua arquitetura gradualmente',
			'solucao_plat_imagem'    => $this->img( 'microsoft-azure-plat' ),

			// 7 Aceleradores
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com eventos Azure estruturados',
			'solucao_acel_corpo'     => 'Utilize um modelo pronto para conectar eventos de negócio ao Event Hubs e Service Bus acelerando sua arquitetura orientada a eventos.',
			'solucao_acel_topico_1'  => 'Configure eventos rapidamente',
			'solucao_acel_topico_2'  => 'Reduza desenvolvimento customizado',
			'solucao_acel_topico_3'  => 'Acelere adoção cloud',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'microsoft-azure-acel' ),
		);

		foreach ( $campos as $chave => $valor ) {
			update_field( $chave, $valor, $post_id );
		}

		$faq_ids = $this->criar_faq_microsoft_azure( $post_id );
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $faq_ids, $post_id );

		WP_CLI::log( '  Microsoft Azure: todas as seções preenchidas.' );
	}

	protected function criar_faq_microsoft_azure( int $solucao_id ): array {
		$itens = array(
			array(
				'faq:microsoft-azure:1',
				'Quais serviços Azure têm conector nativo na CLI Connect powered by Boomi?',
				'A plataforma oferece conectores nativos para os principais serviços Azure, incluindo Event Hubs, Service Bus, CosmosDB, Blob Storage, Azure AD, Key Vault, Functions e API Management. Esses conectores eliminam a necessidade de desenvolvimento específico para integrar seu ecossistema Microsoft.',
			),
			array(
				'faq:microsoft-azure:2',
				'Como funciona a gestão de segredos via Key Vault?',
				'A CLI Connect powered by Boomi integra-se nativamente ao Azure Key Vault para armazenar e recuperar segredos, chaves e certificados usados nas conexões. Isso elimina credenciais fixas nos pipelines e garante que todos os acessos sejam auditáveis e rotativos conforme as políticas de segurança corporativas.',
			),
			array(
				'faq:microsoft-azure:3',
				'É possível combinar Azure com Dynamics 365 e Teams no mesmo pipeline?',
				'Sim. A plataforma permite orquestrar fluxos que envolvem múltiplos serviços do ecossistema Microsoft em um único pipeline — por exemplo, capturar um evento no Azure Event Hubs, atualizar um registro no Dynamics 365 e notificar uma equipe via Teams, tudo de forma integrada e sem código customizado.',
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

		WP_CLI::log( sprintf( '  Microsoft Azure FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}

	// ─────────────────────────────────────────────
	// Google Cloud
	// ─────────────────────────────────────────────

	protected function preencher_solucao_google_cloud() {
		$posts = get_posts( array(
			'post_type'  => 'cli_solucao',
			'meta_key'   => '_cliconnect_seed',
			'meta_value' => 'solucao:google-cloud',
			'fields'     => 'ids',
		) );
		if ( empty( $posts ) ) {
			WP_CLI::warning( 'Solução Google Cloud não encontrada — pulando.' );
			return;
		}
		$post_id = (int) $posts[0];

		$campos = array(
			// 1 Hero
			'solucao_hero_eyebrow'    => 'para o seu Google Cloud',
			'solucao_hero_titulo'     => 'Acelere a adoção do Google Cloud conectando dados e IA',
			'solucao_hero_corpo'      => 'Integre ERP, CRM e sistemas operacionais ao BigQuery e Vertex AI para acelerar iniciativas de dados e inteligência artificial sem desconectar seu legado.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/solucao/google-cloud/',
			'solucao_hero_imagem'     => $this->img( 'google-cloud-hero' ),

			// 2 Pilares
			'solucao_pilares_titulo'   => 'Transforme dados em inteligência no GCP',
			'solucao_pilares_1_icone'  => $this->img( 'google-cloud-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Conecte BigQuery e Vertex AI',
			'solucao_pilares_1_desc'   => 'Leve dados corporativos para analytics e agentes de IA.',
			'solucao_pilares_2_icone'  => $this->img( 'google-cloud-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Adote eventos em escala',
			'solucao_pilares_2_desc'   => 'Use Pub/Sub para conectar sistemas em tempo real.',
			'solucao_pilares_3_icone'  => $this->img( 'google-cloud-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Integre sem substituir legados',
			'solucao_pilares_3_desc'   => 'Conecte ambientes existentes durante sua evolução cloud.',

			// 3 Casos
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Automatize fluxos de dados no GCP',
			'solucao_casos_1_icone'   => $this->img( 'google-cloud-caso-1' ),
			'solucao_casos_1_titulo'  => 'Alimente o BigQuery',
			'solucao_casos_1_desc'    => 'Envie dados de ERP e CRM para análises atualizadas.',
			'solucao_casos_2_icone'   => $this->img( 'google-cloud-caso-2' ),
			'solucao_casos_2_titulo'  => 'Desacople sistemas com Pub/Sub',
			'solucao_casos_2_desc'    => 'Distribua eventos entre aplicações sem dependências diretas.',
			'solucao_casos_3_icone'   => $this->img( 'google-cloud-caso-3' ),
			'solucao_casos_3_titulo'  => 'Prepare dados para IA',
			'solucao_casos_3_desc'    => 'Atualize modelos Vertex AI com contexto corporativo.',
			'solucao_casos_4_icone'   => $this->img( 'google-cloud-caso-4' ),
			'solucao_casos_4_titulo'  => 'Processe arquivos na nuvem',
			'solucao_casos_4_desc'    => 'Armazene e processe documentos usando Cloud Storage.',
			'solucao_casos_5_icone'   => $this->img( 'google-cloud-caso-5' ),
			'solucao_casos_5_titulo'  => 'Execute reverse ETL',
			'solucao_casos_5_desc'    => 'Envie resultados analíticos para sistemas operacionais.',
			'solucao_casos_cta_texto' => 'Fale com especialista',
			'solucao_casos_cta_url'   => '/contato/',

			// 4 Selos
			'solucao_selos_eyebrow'   => 'compliance & segurança',
			'solucao_selos_titulo'    => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'     => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',

			// 5 Diferencial
			'solucao_dif_eyebrow'    => 'diferencial técnico',
			'solucao_dif_titulo'     => 'Integrações GCP com segurança corporativa',
			'solucao_dif_corpo'      => 'Conecte serviços Google Cloud usando IAM, Service Accounts e Cloud KMS para proteger acessos, chaves e dados durante toda a operação.',
			'solucao_dif_topico_1'   => 'Autentique via Service Accounts',
			'solucao_dif_topico_2'   => 'Proteja chaves com Cloud KMS',
			'solucao_dif_topico_3'   => 'Controle acessos via IAM',
			'solucao_dif_imagem'     => $this->img( 'google-cloud-dif' ),

			// 6 Plataforma
			'solucao_plat_eyebrow'   => 'plataforma única',
			'solucao_plat_titulo'    => 'Conecte dados, IA e operação',
			'solucao_plat_corpo'     => 'Centralize a integração entre sistemas corporativos e serviços Google Cloud para acelerar iniciativas de dados sem criar pipelines isolados.',
			'solucao_plat_topico_1'  => 'Integre sistemas corporativos',
			'solucao_plat_topico_2'  => 'Reaproveite fluxos existentes',
			'solucao_plat_topico_3'  => 'Acelere iniciativas de IA',
			'solucao_plat_imagem'    => $this->img( 'google-cloud-plat' ),

			// 7 Aceleradores
			'solucao_acel_eyebrow'   => 'Aceleradores de integração',
			'solucao_acel_titulo'    => 'Comece com dados prontos para IA',
			'solucao_acel_corpo'     => 'Utilize um modelo estruturado para conectar ERP e CRM ao BigQuery e Vertex AI com dados sempre atualizados.',
			'solucao_acel_topico_1'  => 'Conecte fontes rapidamente',
			'solucao_acel_topico_2'  => 'Reduza projetos customizados',
			'solucao_acel_topico_3'  => 'Acelere adoção cloud',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'google-cloud-acel' ),
		);

		foreach ( $campos as $chave => $valor ) {
			update_field( $chave, $valor, $post_id );
		}

		$faq_ids = $this->criar_faq_google_cloud( $post_id );
		update_field( 'solucao_faq_titulo', 'Dúvidas Frequentes', $post_id );
		update_field( 'solucao_faq_itens', $faq_ids, $post_id );

		WP_CLI::log( '  Google Cloud: todas as seções preenchidas.' );
	}

	protected function criar_faq_google_cloud( int $solucao_id ): array {
		$itens = array(
			array(
				'faq:google-cloud:1',
				'Como a CLI Connect powered by Boomi acelera a adoção de BigQuery e Vertex AI?',
				'A plataforma oferece conectores nativos para BigQuery e Vertex AI, permitindo enviar dados de ERP, CRM e sistemas operacionais diretamente para análises e modelos de IA. Isso elimina integrações customizadas e reduz o tempo de entrega de iniciativas de dados e inteligência artificial.',
			),
			array(
				'faq:google-cloud:2',
				'É possível fazer reverse ETL do BigQuery para sistemas operacionais?',
				'Sim. A CLI Connect powered by Boomi suporta fluxos bidirecional, permitindo que resultados analíticos do BigQuery sejam enviados de volta para sistemas operacionais como ERP e CRM. Isso garante que decisões baseadas em dados se reflitam automaticamente nos processos de negócio.',
			),
			array(
				'faq:google-cloud:3',
				'Como funciona a arquitetura orientada a eventos via Pub/Sub?',
				'A plataforma integra-se nativamente ao Google Cloud Pub/Sub para distribuir eventos entre aplicações de forma assíncrona e desacoplada. Você pode configurar triggers que publicam ou consomem mensagens do Pub/Sub dentro de fluxos de integração completos, sem código customizado.',
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

		WP_CLI::log( sprintf( '  Google Cloud FAQ: %d perguntas vinculadas.', count( $ids ) ) );
		return $ids;
	}

	/* =====================================================================
	   RECURSOS HUMANOS (RH)
	   ===================================================================== */

	/**
	 * Preenche os campos ACF do post cli_solucao "Recursos Humanos (RH)".
	 *
	 * @return void
	 */
	protected function preencher_solucao_recursos_humanos_rh() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:recursos-humanos-rh', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  Recursos Humanos (RH): post não encontrado — verifique se criar_solucoes() foi executado.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'         => 'Para o seu RH',
			'solucao_hero_titulo'          => 'Conecte todo o ciclo de vida do colaborador em',
			'solucao_hero_titulo_destaque' => 'uma única operação',
			'solucao_hero_titulo_fluido'   => true,
			'solucao_hero_corpo'           => 'Integre HRIS, folha de pagamento, ATS e sistemas corporativos para automatizar a jornada do colaborador e manter informações sempre sincronizadas.',
			'solucao_hero_btn1_texto'      => 'Agende uma demonstração',
			'solucao_hero_btn1_url'        => '/contato/',
			'solucao_hero_btn2_texto'      => 'Conheça a plataforma',
			'solucao_hero_btn2_url'        => '/plataforma/',
			'solucao_hero_imagem'          => $this->img( 'recursos-humanos-rh-hero' ),

			// 2 · Métricas.
			'solucao_metrica_1_numero'     => '70%',
			'solucao_metrica_1_rotulo'     => 'de redução no tempo de processamento da folha de pagamento',
			'solucao_metrica_2_numero'     => '90%',
			'solucao_metrica_2_rotulo'     => 'de economia projetada em custos contínuos de manutenção',
			'solucao_metrica_3_numero'     => '40%',
			'solucao_metrica_3_rotulo'     => 'de diminuição no tempo gasto com entrada manual de dados',

			// 3 · Pilares.
			'solucao_pilares_eyebrow'      => 'Pilares',
			'solucao_pilares_titulo'       => 'Automatize toda a operação de RH',
			'solucao_pilares_1_icone'      => $this->img( 'recursos-humanos-rh-pilar-1' ),
			'solucao_pilares_1_titulo'     => 'Automatize a jornada do colaborador',
			'solucao_pilares_1_desc'       => 'Sincronize admissões, movimentações e desligamentos entre todos os sistemas para eliminar tarefas manuais e garantir dados consistentes.',
			'solucao_pilares_2_icone'      => $this->img( 'recursos-humanos-rh-pilar-2' ),
			'solucao_pilares_2_titulo'     => 'Mantenha a folha sincronizada',
			'solucao_pilares_2_desc'       => 'Atualize automaticamente dados entre HRIS e folha de pagamento para reduzir inconsistências e simplificar o fechamento mensal.',
			'solucao_pilares_3_icone'      => $this->img( 'recursos-humanos-rh-pilar-3' ),
			'solucao_pilares_3_titulo'     => 'Proteja dados sensíveis',
			'solucao_pilares_3_desc'       => 'Aplique mascaramento de informações pessoais durante as integrações para atender requisitos de LGPD e fortalecer a governança.',

			// 4 · Casos de Uso.
			'solucao_casos_eyebrow'        => 'casos de uso',
			'solucao_casos_titulo'         => 'Automatize processos críticos de RH',
			'solucao_casos_1_icone'        => $this->img( 'recursos-humanos-rh-caso-1' ),
			'solucao_casos_1_titulo'       => 'Orquestre o ciclo do funcionário',
			'solucao_casos_1_desc'         => 'Atualize HRIS, identidade, folha e plataformas de treinamento simultaneamente sempre que um colaborador entrar ou sair da empresa.',
			'solucao_casos_2_icone'        => $this->img( 'recursos-humanos-rh-caso-2' ),
			'solucao_casos_2_titulo'       => 'Sincronize HRIS e folha',
			'solucao_casos_2_desc'         => 'Garanta que alterações cadastrais e movimentações sejam refletidas automaticamente na folha de pagamento.',
			'solucao_casos_3_icone'        => $this->img( 'recursos-humanos-rh-caso-3' ),
			'solucao_casos_3_titulo'       => 'Automatize novas contratações',
			'solucao_casos_3_desc'         => 'Envie candidatos aprovados do ATS para o HRIS automaticamente, eliminando cadastros duplicados e atividades manuais.',
			'solucao_casos_4_icone'        => $this->img( 'recursos-humanos-rh-caso-4' ),
			'solucao_casos_4_titulo'       => 'Revogue acessos automaticamente',
			'solucao_casos_4_desc'         => 'Remova permissões e contas poucos minutos após o desligamento para aumentar a segurança e reduzir riscos operacionais.',
			'solucao_casos_5_icone'        => $this->img( 'recursos-humanos-rh-caso-5' ),
			'solucao_casos_5_titulo'       => 'Antecipe riscos de desligamento',
			'solucao_casos_5_desc'         => 'Utilize agentes de IA para identificar sinais de retenção e apoiar decisões antes da perda de talentos.',
			'solucao_casos_6_icone'        => $this->img( 'recursos-humanos-rh-caso-6' ),
			'solucao_casos_6_titulo'       => 'Automatize movimentações internas',
			'solucao_casos_6_desc'         => 'Atualize cargos, equipes e permissões sempre que houver mudanças.',

			// 5 · Diferencial Técnico (o design do RH inverte Diferencial e Selos).
			'solucao_dif_eyebrow'          => 'diferencial técnico',
			'solucao_dif_titulo'           => 'Privacidade integrada às automações',
			'solucao_dif_corpo'            => 'Proteja informações sensíveis durante toda a movimentação entre sistemas com detecção e mascaramento automático de dados pessoais antes da integração.',
			'solucao_dif_topico_1'         => 'Detecte dados sensíveis automaticamente',
			'solucao_dif_topico_2'         => 'Mascare informações antes da integração',
			'solucao_dif_topico_3'         => 'Atenda requisitos de LGPD com governança',
			'solucao_dif_imagem'           => $this->img( 'recursos-humanos-rh-dif' ),
			'solucao_dif_antes_selos'      => true,

			// 6 · Selos.
			'solucao_selos_eyebrow'        => 'compliance & segurança',
			'solucao_selos_titulo'         => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'          => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_recursos_humanos_rh_faq( $post_id );

		WP_CLI::log( "  Recursos Humanos (RH) preenchido (ID: {$post_id})." );
	}

	/**
	 * Cria os posts cli_faq de Recursos Humanos (RH) e vincula à solução.
	 *
	 * ATENÇÃO — texto provisório: o Figma mostra apenas as perguntas (o
	 * accordion está fechado no design). As respostas foram redigidas a partir
	 * do que a própria landing afirma nas seções anteriores e seguem pendentes
	 * de validação do cliente.
	 *
	 * @param int $post_id ID do post cli_solucao de Recursos Humanos (RH).
	 * @return void
	 */
	protected function preencher_recursos_humanos_rh_faq( $post_id ) {
		$itens = array(
			array(
				'faq:rh-hris-folha-prazo',
				'Quanto tempo leva para integrar o HRIS à folha de pagamento?',
				'<p>O prazo depende bem menos do desenvolvimento do que do acesso aos sistemas e da qualidade do cadastro. Quando o HRIS e a folha expõem APIs documentadas e as credenciais já estão liberadas, o fluxo de admissões, movimentações e desligamentos costuma entrar em produção em semanas. O que estica o cronograma é a conciliação de cadastros divergentes entre os dois sistemas e a homologação com o fornecedor. Como os componentes são reutilizáveis, a primeira integração é a mais demorada e as seguintes aproveitam o que já foi construído.</p>',
			),
			array(
				'faq:rh-autonomia-do-time',
				'O RH consegue gerenciar integrações sem depender da equipe de desenvolvimento?',
				'<p>No dia a dia, sim. O time de RH acompanha as execuções, vê onde um registro parou e reprocessa o que falhou por um painel próprio, sem abrir chamado. Mudanças estruturais — incluir um sistema novo no fluxo ou alterar as regras de um campo — continuam passando por quem mantém a integração, mas partem de componentes prontos, então são ajustes de configuração e não de projeto.</p>',
			),
			array(
				'faq:rh-criterios-plataforma',
				'Quais critérios devo avaliar ao escolher uma plataforma de integração para RH?',
				'<p>Três pontos pesam mais do que a lista de conectores. O primeiro é o tratamento de dados pessoais: a plataforma precisa detectar e mascarar informações sensíveis antes de movimentá-las, não depois. O segundo é a rastreabilidade — cada admissão, movimentação e desligamento deve deixar registro de quando passou, para onde foi e o que aconteceu se falhou. O terceiro é o reaproveitamento: fluxos montados como componentes reduzem o custo de cada nova integração, enquanto integrações ponto a ponto crescem em manutenção a cada sistema adicionado.</p>',
			),
			array(
				'faq:rh-dados-sensiveis',
				'Como os dados sensíveis dos colaboradores são protegidos durante as integrações?',
				'<p>A proteção acontece dentro do próprio fluxo. Campos como CPF, dados bancários e informações de saúde são identificados automaticamente e mascarados antes de seguirem para o sistema de destino, de modo que apenas quem precisa do dado completo o recebe. O tráfego é criptografado ponta a ponta, o acesso é concedido por perfil e cada movimentação fica registrada em trilha de auditoria — é o que sustenta o atendimento aos requisitos de LGPD sem depender de disciplina manual.</p>',
			),
			array(
				'faq:rh-mudanca-de-api',
				'Como mudanças nas APIs dos fornecedores de HRIS impactam as integrações?',
				'<p>O impacto fica contido na camada de tradução. Cada sistema conversa com um formato interno comum, então uma versão nova da API do HRIS exige ajustar apenas o trecho que fala com ele — o restante do fluxo, incluindo folha, identidade e treinamento, segue inalterado. As versões novas são homologadas em ambiente separado antes de entrar em produção, e o monitoramento avisa quando um endpoint muda de comportamento, em vez de a falha aparecer no fechamento da folha.</p>',
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

		WP_CLI::log( sprintf( '  Recursos Humanos (RH) FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/**
	 * Preenche a solução Gemini.
	 *
	 * Todas as seções: Hero, Pilares, Diagrama, Integrações, Casos, Selos,
	 * Diferencial, Plataforma, Aceleradores e FAQ.
	 *
	 * @return void
	 */
	protected function preencher_solucao_gemini() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:gemini', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  Gemini: post não encontrado — verifique se o CPT existe com _cliconnect_seed = solucao:gemini.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'integre o seu gemini',
			'solucao_hero_titulo'     => 'Conecte o Gemini aos seus sistemas e dados corporativos',
			'solucao_hero_corpo'      => 'Gemini acessa dados, orquestra sistemas e executa ações com precisão — tudo integrado à operação da empresa.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/plataforma/',
			'solucao_hero_imagem'     => $this->img( 'gemini-hero' ),

			// 3 · Pilares.
			'solucao_pilares_titulo'   => 'Transforme Gemini em parte da operação',
			'solucao_pilares_1_icone'  => $this->img( 'gemini-pilar-1' ),
			'solucao_pilares_1_titulo' => 'Conecte Gemini aos seus dados',
			'solucao_pilares_1_desc'   => 'Leve informações de sistemas corporativos para o modelo e gere respostas baseadas no contexto real da operação.',
			'solucao_pilares_2_icone'  => $this->img( 'gemini-pilar-2' ),
			'solucao_pilares_2_titulo' => 'Orquestre múltiplas aplicações',
			'solucao_pilares_2_desc'   => 'Combine Gemini com ERP, CRM, bancos de dados e outras aplicações em fluxos automatizados.',
			'solucao_pilares_3_icone'  => $this->img( 'gemini-pilar-3' ),
			'solucao_pilares_3_titulo' => 'Consulte sistemas em linguagem natural',
			'solucao_pilares_3_desc'   => 'Permita que equipes encontrem informações de clientes, pedidos e operações sem navegar por diferentes sistemas.',

			// 5 · Casos de uso.
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_titulo'    => 'Aplique Gemini aos processos do negócio',
			'solucao_casos_1_icone'   => $this->img( 'gemini-caso-1' ),
			'solucao_casos_1_titulo'  => 'Consulte dados do ERP com IA',
			'solucao_casos_2_icone'   => $this->img( 'gemini-caso-2' ),
			'solucao_casos_2_titulo'  => 'Analise documentos automaticamente',
			'solucao_casos_3_icone'   => $this->img( 'gemini-caso-3' ),
			'solucao_casos_3_titulo'  => 'Automatize o atendimento',
			'solucao_casos_4_icone'   => $this->img( 'gemini-caso-4' ),
			'solucao_casos_4_titulo'  => 'Classifique solicitações',
			'solucao_casos_5_icone'   => $this->img( 'gemini-caso-5' ),
			'solucao_casos_5_titulo'  => 'Gere análises operacionais',
			'solucao_casos_cta_texto' => 'Agende uma demonstração',
			'solucao_casos_cta_url'   => '/contato/',

			// 11 · Diagrama — motor de integração.
			'solucao_diagrama_titulo' => 'Um novo jeito de conectar IA aos seus sistemas',
			'solucao_diagrama_imagem' => $this->img( 'gemini-motor' ),

			// 12 · Integrações — grade de logos.
			'solucao_int_eyebrow'   => 'integrações',
			'solucao_int_titulo'    => 'Integre todos os seus sistema com o Gemini',
			'solucao_int_imagem'    => $this->img( 'gemini-integracoes' ),
			'solucao_int_subtitulo' => 'Milhares de integrações já prontas para uso',

			// 7 · Selos — compliance & segurança.
			'solucao_selos_eyebrow' => 'compliance & segurança',
			'solucao_selos_titulo'  => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'   => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',

			// 8 · Diferencial técnico.
			'solucao_dif_eyebrow'     => 'diferencial técnico',
			'solucao_dif_titulo'      => 'Integre IA com controle sobre seus dados',
			'solucao_dif_corpo'       => 'Conecte Gemini aos sistemas da empresa com controle sobre dados, acessos e ações para escalar inteligência artificial sem perder governança.',
			'solucao_dif_topico_1'    => 'Controle quais dados chegam aos modelos',
			'solucao_dif_topico_2'    => 'Proteja dados em trânsito e repouso',
			'solucao_dif_topico_3'    => 'Aplique regras antes de executar ações',
			'solucao_dif_imagem'      => $this->img( 'gemini-dif' ),
			'solucao_dif_antes_selos' => 0,

			// 6 · Plataforma única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Centralize IA e integrações em uma plataforma',
			'solucao_plat_corpo'    => 'Evite criar conexões isoladas para cada caso de uso. Centralize Gemini, sistemas e processos para escalar novos agentes usando a mesma arquitetura.',
			'solucao_plat_topico_1' => 'Conecte Gemini a múltiplos sistemas',
			'solucao_plat_topico_2' => 'Reutilize conexões em novos agentes',
			'solucao_plat_topico_3' => 'Orquestre IA dentro dos processos',
			'solucao_plat_imagem'   => $this->img( 'gemini-plat' ),

			// 9 · Aceleradores — MCP Server.
			'solucao_acel_eyebrow'   => 'MCP server',
			'solucao_acel_titulo'    => 'Dê ferramentas ao Gemini sem a necessidade de desenvolver APIs',
			'solucao_acel_corpo'     => 'Transforme processos corporativos em Tools para o Gemini, definindo exatamente quais informações ele pode consultar e quais ações pode executar.',
			'solucao_acel_topico_1'  => 'Transforme processos em ferramentas de IA',
			'solucao_acel_topico_2'  => 'Controle entradas, saídas e informações',
			'solucao_acel_topico_3'  => 'Disponibilize tudo pelo MCP Server',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'gemini-mcp' ),
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_gemini_faq( $post_id );

		WP_CLI::log( "  Gemini preenchido (ID: {$post_id})." );
	}

	/**
	 * Cria os posts cli_faq do Gemini e os vincula à solução.
	 *
	 * @param int $post_id ID do post cli_solucao do Gemini.
	 * @return void
	 */
	protected function preencher_gemini_faq( $post_id ) {
		$itens = array(
			array(
				'faq:gemini-conectar-sistemas',
				'Como conectar o Gemini aos sistemas da empresa?',
				'<p>A conexão é feita pela camada de integração da CLI Connect: o Gemini acessa sistemas, bases de dados e processos corporativos via MCP Server, que expõe recursos como ferramentas. Não é necessário desenvolver APIs ou modificar os sistemas de origem — a plataforma gerencia autenticação, permissões e rastreabilidade de ponta a ponta.</p>',
			),
			array(
				'faq:gemini-dados-internos',
				'É possível usar dados internos para dar contexto ao Gemini?',
				'<p>Sim. A CLI Connect conecta o Gemini a fontes internas — ERP, CRM, bases de dados, documentos e wikis — enviando o contexto relevante em cada consulta. Os dados trafegam apenas durante a execução e não são usados para retreinar o modelo, garantindo privacidade e conformidade.</p>',
			),
			array(
				'faq:gemini-executar-acoes',
				'O Gemini pode executar ações nos sistemas conectados?',
				'<p>Sim. Além de consultar informações, o Gemini pode executar ações — como criar pedidos, atualizar registros ou acionar processos — desde que a ferramenta correspondente esteja publicada no MCP Server e o perfil do usuário tenha permissão. Cada execução fica registrada na plataforma para auditoria.</p>',
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

		WP_CLI::log( sprintf( '  Gemini FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/**
	 * Preenche a solução Claude.
	 *
	 * Todas as seções: Hero, Pilares, Diagrama, Integrações, Casos, Selos,
	 * Diferencial, Plataforma, Aceleradores e FAQ.
	 *
	 * @return void
	 */
	protected function preencher_solucao_claude() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:claude', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  Claude: post não encontrado — verifique se o CPT existe com _cliconnect_seed = solucao:claude.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 1 · Hero.
			'solucao_hero_eyebrow'    => 'integre o seu claude',
			'solucao_hero_titulo'     => 'Transforme conhecimento empresarial em ações com Claude',
			'solucao_hero_corpo'      => 'Claude conecta documentos, dados e sistemas corporativos para consultar informações, interpretar contexto e executar ações com precisão e controle.',
			'solucao_hero_btn1_texto' => 'Agende uma demonstração',
			'solucao_hero_btn1_url'   => '/contato/',
			'solucao_hero_btn2_texto' => 'Conheça nossa solução',
			'solucao_hero_btn2_url'   => '/plataforma/',
			'solucao_hero_imagem'     => $this->img( 'claude-hero' ),

			// 3 · Pilares.
			'solucao_pilares_titulo'    => 'Converta conhecimento em decisões',
			'solucao_pilares_1_icone'   => $this->img( 'claude-pilar-1' ),
			'solucao_pilares_1_titulo'  => 'Analise grandes volumes de informação',
			'solucao_pilares_1_desc'    => 'Processe documentos, históricos e registros para extrair insights relevantes sem depender de buscas manuais.',
			'solucao_pilares_2_icone'   => $this->img( 'claude-pilar-2' ),
			'solucao_pilares_2_titulo'  => 'Consulte o conhecimento da empresa',
			'solucao_pilares_2_desc'    => 'Conecte fontes internas — wikis, bases de dados, políticas — para que Claude responda com contexto real do negócio.',
			'solucao_pilares_3_icone'   => $this->img( 'claude-pilar-3' ),
			'solucao_pilares_3_titulo'  => 'Execute ferramentas',
			'solucao_pilares_3_desc'    => 'Crie um pedido de venda, atualize um CRM ou abra um chamado — Claude age nos sistemas com as permissões certas.',

			// 5 · Casos de uso.
			'solucao_casos_eyebrow'    => 'casos de uso',
			'solucao_casos_titulo'     => 'Aplique Claude onde conhecimento importa',
			'solucao_casos_1_icone'    => $this->img( 'claude-caso-1' ),
			'solucao_casos_1_titulo'   => 'Revise contratos automaticamente',
			'solucao_casos_2_icone'    => $this->img( 'claude-caso-2' ),
			'solucao_casos_2_titulo'   => 'Consulte políticas internas',
			'solucao_casos_3_icone'    => $this->img( 'claude-caso-3' ),
			'solucao_casos_3_titulo'   => 'Analise solicitações de clientes',
			'solucao_casos_4_icone'    => $this->img( 'claude-caso-4' ),
			'solucao_casos_4_titulo'   => 'Compare propostas comerciais',
			'solucao_casos_5_icone'    => $this->img( 'claude-caso-5' ),
			'solucao_casos_5_titulo'   => 'Resuma históricos operacionais',
			'solucao_casos_cta_texto'  => 'Agende uma demonstração',
			'solucao_casos_cta_url'    => '/contato/',

			// 11 · Diagrama — motor de integração.
			'solucao_diagrama_titulo' => 'Um novo jeito de conectar IA aos seus sistemas',
			'solucao_diagrama_imagem' => $this->img( 'chatgpt-motor' ),

			// 12 · Integrações — grade de logos.
			'solucao_int_eyebrow'   => 'integrações',
			'solucao_int_titulo'    => 'Integre todos os seus sistema com o Claude',
			'solucao_int_imagem'    => $this->img( 'claude-integracoes' ),
			'solucao_int_subtitulo' => 'Milhares de integrações já prontas para uso',

			// 7 · Selos — compliance & segurança.
			'solucao_selos_eyebrow' => 'compliance & segurança',
			'solucao_selos_titulo'  => 'Lideramos o mercado quando assunto é compliance e segurança',
			'solucao_selos_corpo'   => 'Seus dados, processos e integrações protegidos pelos mais altos padrões globais.',

			// 8 · Diferencial técnico.
			'solucao_dif_eyebrow'     => 'diferencial técnico',
			'solucao_dif_titulo'      => 'Integre IA com controle sobre seus dados',
			'solucao_dif_corpo'       => 'Conecte Claude aos sistemas corporativos mantendo controle sobre dados, permissões e ações — sem comprometer segurança ou governança.',
			'solucao_dif_topico_1'    => 'Controle quais dados chegam aos modelos',
			'solucao_dif_topico_2'    => 'Proteja dados em trânsito e repouso',
			'solucao_dif_topico_3'    => 'Aplique regras antes de executar ações',
			'solucao_dif_imagem'      => $this->img( 'claude-dif' ),
			'solucao_dif_antes_selos' => 0,

			// 6 · Plataforma única.
			'solucao_plat_eyebrow'  => 'plataforma única',
			'solucao_plat_titulo'   => 'Centralize conhecimento, sistemas e processos',
			'solucao_plat_corpo'    => 'Claude gera mais valor quando consegue acessar o contexto necessário. A plataforma CLI Connect conecta fontes, orquestra fluxos e mantém rastreabilidade.',
			'solucao_plat_topico_1' => 'Conecte diferentes fontes de informação',
			'solucao_plat_topico_2' => 'Reaproveite dados em novos processos',
			'solucao_plat_topico_3' => 'Orquestre resultados entre sistemas',
			'solucao_plat_imagem'   => $this->img( 'claude-plat' ),

			// 9 · Aceleradores — MCP Server.
			'solucao_acel_eyebrow'   => 'MCP server',
			'solucao_acel_titulo'    => 'Dê ferramentas ao Claude sem a necessidade de desenvolver APIs',
			'solucao_acel_corpo'     => 'Transforme processos corporativos em Tools para o Claude, definindo exatamente quais informações ele pode consultar e quais ações pode executar.',
			'solucao_acel_topico_1'  => 'Transforme processos em ferramentas de IA',
			'solucao_acel_topico_2'  => 'Controle entradas, saídas e informações',
			'solucao_acel_topico_3'  => 'Disponibilize tudo pelo MCP Server',
			'solucao_acel_topico_4'  => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'   => '/contato/',
			'solucao_acel_imagem'    => $this->img( 'claude-mcp' ),
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_claude_faq( $post_id );

		WP_CLI::log( "  Claude preenchido (ID: {$post_id})." );
	}

	/**
	 * Cria os posts cli_faq do Claude e os vincula à solução.
	 *
	 * @param int $post_id ID do post cli_solucao do Claude.
	 * @return void
	 */
	protected function preencher_claude_faq( $post_id ) {
		$itens = array(
			array(
				'faq:claude-conectar-documentos',
				'Como conectar Claude aos documentos e dados da empresa?',
				'<p>A conexão é feita pela camada de integração da CLI Connect: Claude acessa documentos, bases de dados e sistemas via MCP Server, que expõe os recursos corporativos como ferramentas. Não é necessário desenvolver APIs ou modificar as fontes de origem — a plataforma cria o canal seguro e gerencia autenticação, permissões e rastreabilidade.</p>',
			),
			array(
				'faq:claude-analisar-contratos',
				'É possível usar Claude para analisar contratos e outros documentos?',
				'<p>Sim. Claude é especialmente eficaz na leitura e interpretação de documentos longos — contratos, relatórios, políticas e históricos. A plataforma CLI Connect envia o contexto relevante ao modelo e retorna os insights de forma estruturada, sem armazenar o conteúdo dos documentos após a execução.</p>',
			),
			array(
				'faq:claude-executar-acoes',
				'É possível fazer o Claude executar ações nos sistemas?',
				'<p>Sim. Além de consultar e analisar informações, Claude pode executar ações — como criar um pedido no ERP, atualizar um registro no CRM ou abrir um chamado — desde que a ferramenta correspondente esteja publicada no MCP Server e o perfil do usuário tenha permissão. Cada execução fica registrada na plataforma para auditoria.</p>',
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

		WP_CLI::log( sprintf( '  Claude FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}

	/**
	 * Preenche a solução ChatGPT.
	 *
	 * Seções: 11 · Diagrama, 12 · Integrações, 9 · Aceleradores e 10 · FAQ.
	 * Hero, Pilares, Diferencial, Selos e Plataforma foram cadastrados manualmente.
	 *
	 * @return void
	 */
	protected function preencher_solucao_chatgpt() {
		$posts = get_posts(
			array(
				'post_type'      => 'cli_solucao',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'solucao:chatgpt', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! $posts ) {
			WP_CLI::warning( '  ChatGPT: post não encontrado — verifique se o CPT existe com _cliconnect_seed = solucao:chatgpt.' );
			return;
		}

		$post_id = (int) $posts[0];

		$campos = array(
			// 11 · Diagrama — motor de integração.
			'solucao_diagrama_titulo' => 'Um novo jeito de conectar IA aos seus sistemas',
			'solucao_diagrama_imagem' => $this->img( 'chatgpt-motor' ),

			// 12 · Integrações — grade de logos.
			'solucao_int_eyebrow'   => 'integrações',
			'solucao_int_titulo'    => 'Integre todos os seus sistema com o ChatGPT',
			'solucao_int_imagem'    => $this->img( 'chatgpt-integracoes' ),
			'solucao_int_subtitulo' => 'Milhares de integrações já prontas para uso',

			// 9 · Aceleradores — MCP Server.
			'solucao_acel_eyebrow'  => 'MCP server',
			'solucao_acel_titulo'   => 'Dê ferramentas ao ChatGPT sem a necessidade de desenvolver APIs',
			'solucao_acel_corpo'    => 'Transforme processos corporativos em Tools para o ChatGPT, definindo exatamente quais informações ele pode consultar e quais ações pode executar.',
			'solucao_acel_topico_1' => 'Transforme processos em ferramentas de IA',
			'solucao_acel_topico_2' => 'Controle entradas, saídas e informações',
			'solucao_acel_topico_3' => 'Disponibilize tudo pelo MCP Server',
			'solucao_acel_topico_4' => 'E muito mais...',
			'solucao_acel_btn_texto' => 'Começar agora',
			'solucao_acel_btn_url'  => '/contato/',
			'solucao_acel_imagem'   => $this->img( 'chatgpt-mcp' ),
		);

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $post_id );
		}

		$this->preencher_chatgpt_faq( $post_id );

		WP_CLI::log( "  ChatGPT preenchido (ID: {$post_id})." );
	}

	/**
	 * Cria os posts cli_faq do ChatGPT e os vincula à solução.
	 *
	 * As respostas não aparecem no Figma (accordion fechado), mas foram redigidas
	 * a partir do que a landing afirma nas demais seções — pendentes de validação.
	 *
	 * @param int $post_id ID do post cli_solucao do ChatGPT.
	 * @return void
	 */
	protected function preencher_chatgpt_faq( $post_id ) {
		$itens = array(
			array(
				'faq:chatgpt-conectar-sistemas',
				'Como conectar o ChatGPT aos sistemas da empresa?',
				'<p>A conexão é feita pela camada de integração da CLI Connect: o ChatGPT acessa os sistemas via MCP Server, que expõe os processos corporativos como ferramentas. Não é necessário desenvolver APIs ou modificar os sistemas de origem — a plataforma cria o canal seguro e gerencia autenticação, permissões e rastreabilidade de ponta a ponta.</p>',
			),
			array(
				'faq:chatgpt-dados-treinamento',
				'Os dados da empresa são usados para treinar os modelos?',
				'<p>Não. Os dados trafegam apenas durante a execução de uma consulta e não são retidos pela OpenAI para treinamento quando a API é usada em ambiente corporativo. Além disso, a CLI Connect controla quais informações chegam ao modelo, permitindo anonimização e mascaramento de dados sensíveis antes de qualquer chamada.</p>',
			),
			array(
				'faq:chatgpt-executar-acoes',
				'É possível fazer o ChatGPT executar ações nos sistemas?',
				'<p>Sim. Além de consultar informações, o ChatGPT pode executar ações — como criar um pedido no ERP, atualizar um registro no CRM ou abrir um chamado no ServiceNow — desde que a ferramenta correspondente esteja publicada no MCP Server e o perfil do usuário tenha permissão para aquela ação. Cada execução fica registrada na plataforma para auditoria.</p>',
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

		WP_CLI::log( sprintf( '  ChatGPT FAQ: %d perguntas vinculadas.', count( $ids ) ) );
	}
}

WP_CLI::add_command( 'cliconnect seed', 'Cliconnect_Seed' );
