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

		WP_CLI::log( '— Montando menus…' );
		$this->criar_menus( $paginas );

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

		$arquivos = glob( $dir . '/*.{png,jpg,jpeg}', GLOB_BRACE );
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
		$base = preg_replace( '/^(cliente|integracao|selo|evento|case|midia|logo|boomi|suporte|blog|cc)-/', '', $base );

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
			array( 'HSBC', 'cliente-hsbc', false ),
			array( 'Unimed', 'cliente-unimed', false ),
			array( 'Martins', 'cliente-martins', false ),
			array( 'Culligan', 'cliente-culligan', false ),
			array( 'Arcom', 'cliente-arcom', false ),
			array( 'Seculus', 'cliente-seculus', false ),
			array( 'Grupo Ferroeste', 'cliente-grupo-ferroeste', false ),
			array( 'Panasonic', 'cliente-panasonic', true ),
			array( 'Cocamar', 'cliente-cocamar', true ),
			array( 'Localiza', 'cliente-localiza', true ),
			array( 'RodOil', 'cliente-rodoil', false ),
			array( 'Albaugh', 'cliente-albaugh', false ),
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
			'agentes_legenda'       => 'Mais de 30.000 integrações prontas para uso',

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
	   MENUS
	   ===================================================================== */

	/**
	 * Cria os três menus do tema e os atribui às locations.
	 *
	 * @param array $paginas slug => ID.
	 * @return void
	 */
	protected function criar_menus( $paginas ) {
		$cases_url = get_post_type_archive_link( 'cli_case' );
		$blog_url  = get_permalink( (int) get_option( 'page_for_posts' ) );

		$solucoes = array(
			'Tecnologias'                    => '/solucoes/',
			'Atualização de Sistemas Legados' => '/solucoes/',
			'IA Corporativa'                 => '/solucoes/',
			'Portal de APIs e Servidores MCP' => '/solucoes/',
			'Compras ao Pagamento'           => '/solucoes/',
			'Visão 360° do Cliente'          => '/solucoes/',
			'Modernização de ERP'            => '/solucoes/',
		);

		/*
		 * Soluções no menu principal tem três níveis: os filhos viram os títulos
		 * das colunas do mega menu e os netos, os links. O item sem filhos no
		 * fim é renderizado como o "Ver todos" do rodapé do painel.
		 */
		$solucoes_mega = array(
			array(
				'titulo' => 'Tecnologia',
				'url'    => '/solucoes/',
				'filhos' => array(
					'Claude'          => '/solucoes/',
					'ChatGPT'         => '/solucoes/',
					'SAP'             => '/solucoes/',
					'Salesforce'      => '/solucoes/',
					'TOTVS Protheus'  => '/solucoes/',
					'Sankhya'         => '/solucoes/',
					'Senior'          => '/solucoes/',
					'Dynamics 365'    => '/solucoes/',
				),
			),
			array(
				'titulo' => 'Indústria',
				'url'    => '/solucoes/',
				'filhos' => array(
					'Serviços Financeiros' => '/solucoes/',
					'Manufatura'           => '/solucoes/',
					'Logística (3PL)'      => '/solucoes/',
					'Software (ISV)'       => '/solucoes/',
					'Varejo'               => '/solucoes/',
					'Hotelaria e Turismo'  => '/solucoes/',
					'Seguros'              => '/solucoes/',
				),
			),
			array(
				'titulo' => 'Departamento',
				'url'    => '/solucoes/',
				'filhos' => array(
					'Recursos Humanos (RH)'          => '/solucoes/',
					'Operações de Receita (RevOps)'  => '/solucoes/',
					'Marketing'                      => '/solucoes/',
					'Financeiro'                     => '/solucoes/',
				),
			),
			array(
				'titulo' => 'Nuvem',
				'url'    => '/solucoes/',
				'filhos' => array(
					'AWS'          => '/solucoes/',
					'Google Cloud' => '/solucoes/',
					'Azure'        => '/solucoes/',
				),
			),
			array(
				'titulo' => 'Por Iniciativa',
				'url'    => '/solucoes/',
				'filhos' => array(
					'Atualização de Sistemas Legados' => '/solucoes/',
					'Pedido ao Recebimento'           => '/solucoes/',
					'IA Corporativa'                  => '/solucoes/',
					'Compras ao Pagamento'            => '/solucoes/',
					'Jornada do Colaborador'          => '/solucoes/',
					'Soberania de Dados'              => '/solucoes/',
					'Visão 360° do Cliente'           => '/solucoes/',
					'Modernização de ERP'             => '/solucoes/',
				),
			),
			array( 'titulo' => 'Ver todos', 'url' => '/solucoes/' ),
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
					'url'    => '/solucoes/',
					'filhos' => $solucoes_mega,
				),
				array( 'titulo' => 'Integração SAP', 'url' => '/integracao-sap/' ),
				array( 'titulo' => 'Cases', 'url' => $cases_url ),
				array( 'titulo' => 'Blog', 'url' => $blog_url ),
				array( 'titulo' => 'Contato', 'url' => '/contato/' ),
			)
		);

		// --- Rodapé (colunas) ------------------------------------------------
		$this->montar_menu(
			'rodape',
			'CLI — Rodapé',
			array(
				array(
					'titulo' => 'Plataforma',
					'url'    => '/plataforma/',
					'filhos' => array(
						'CLI Connect'   => '/cli-connect/',
						'CLI Signature' => '/cli-signature/',
					),
				),
				array(
					'titulo' => 'Sistemas',
					'url'    => '/sistemas/',
					'filhos' => array(
						'Claude'     => '/sistemas/',
						'ChatGPT'    => '/sistemas/',
						'SAP'        => '/sistemas/',
						'Salesforce' => '/sistemas/',
						'TOTVS'      => '/sistemas/',
						'Senior'     => '/sistemas/',
						array(
							'titulo'  => 'Ver todos',
							'url'     => '/sistemas/',
							'classes' => 'link-ver-todos',
						),
					),
				),
				array(
					'titulo' => 'Soluções',
					'url'    => '/solucoes/',
					'filhos' => $solucoes,
				),
				array(
					'titulo' => 'Recursos',
					'url'    => '/contato/',
					'filhos' => array(
						'Cases'                    => $cases_url,
						'Blog'                     => $blog_url,
						'Trabalhe Conosco'         => '/trabalhe-conosco/',
						'Contato'                  => '/contato/',
						'Política de Privacidade'  => '/privacidade/',
						'Termos de Uso'            => '/termos/',
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
			'cc_boomi_titulo'    => 'Tecnologia de classe mundial',
			'cc_boomi_subtitulo' => 'Boomi — reconhecida pelo Gartner como líder em iPaaS e API Management por anos consecutivos',
			'cc_boomi_texto'     => 'A CLI Connect é powered by Boomi, a plataforma de integração mais completa do mercado. Com ela, você conecta qualquer sistema com segurança, escalabilidade e suporte especializado.',
			'cc_boomi_badge_1'   => 'Líder Gartner - iPaaS',
			'cc_boomi_badge_2'   => 'Líder Gartner - API',
			'cc_boomi_imagem'    => $this->img( 'cc-boomi' ),

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

			// 7. Pilares — eyebrow e título.
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
	 * Converte um caminho relativo em URL absoluta do site.
	 *
	 * @param string $url Caminho ou URL.
	 * @return string
	 */
	protected function url_absoluta( $url ) {
		$url = (string) $url;

		return ( '/' === substr( $url, 0, 1 ) ) ? home_url( $url ) : $url;
	}
}

WP_CLI::add_command( 'cliconnect seed', 'Cliconnect_Seed' );
