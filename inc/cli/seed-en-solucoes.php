<?php
/**
 * Seed — texto em inglês das soluções (taxonomia, landings e menus).
 *
 * A taxonomia `cli_categoria_solucao` é traduzível: cada categoria e cada tipo
 * tem um termo em inglês vinculado ao português, e é ele que os menus e os
 * cards em inglês apontam.
 *
 * Cada landing tem um método `texto_en_solucao_*()` com **apenas os campos de
 * texto**; imagens, ícones, logos e as FAQ vinculadas vêm copiados do original
 * por `traduzir_post()`.
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
 * Conteúdo em inglês das soluções.
 */
trait Cliconnect_Seed_En_Solucoes {

	/**
	 * Termos da taxonomia: chave do seed => [nome em inglês, slug em inglês].
	 *
	 * Mesma ordem de `criar_solucoes()`, para as duas listas serem conferidas
	 * lado a lado.
	 *
	 * Terceiro elemento (opcional): slug do **post** em inglês. Só aparece onde
	 * o nome é igual em português — compartilhar slug entre idiomas é recurso
	 * do Polylang Pro, e sem ele o WordPress acrescenta um `-2` em silêncio.
	 *
	 * @return array<string,array{0:string,1:string,2?:string}>
	 */
	protected function termos_solucao_en() {
		return array(
			// Categorias (nível 1).
			'tecnologia'                         => array( 'Technology', 'technology' ),
			'industria'                          => array( 'Industry', 'industry' ),
			'departamento'                       => array( 'Department', 'department' ),
			'nuvem'                              => array( 'Cloud', 'cloud' ),
			'por-iniciativa'                     => array( 'By Initiative', 'by-initiative' ),

			// Tecnologia.
			'claude'                             => array( 'Claude', 'claude-en', 'claude-integration' ),
			'chatgpt'                            => array( 'ChatGPT', 'chatgpt-en', 'chatgpt-integration' ),
			'sap'                                => array( 'SAP', 'sap-en', 'sap-integration' ),
			'salesforce'                         => array( 'Salesforce', 'salesforce-en', 'salesforce-integration' ),
			'totvs-protheus'                     => array( 'TOTVS Protheus', 'totvs-protheus-en', 'totvs-protheus-integration' ),
			'sankhya'                            => array( 'Sankhya', 'sankhya-en', 'sankhya-integration' ),
			'senior'                             => array( 'Senior', 'senior-en', 'senior-integration' ),
			'dynamics-365'                       => array( 'Dynamics 365', 'dynamics-365-en', 'dynamics-365-integration' ),

			// Tecnologia — landings das branches de solução por tecnologia.
			'salesforce-sales-cloud'             => array( 'Salesforce Sales Cloud', 'salesforce-sales-cloud-en', 'salesforce-sales-cloud-integration' ),
			'salesforce-service-cloud'           => array( 'Salesforce Service Cloud', 'salesforce-service-cloud-en', 'salesforce-service-cloud-integration' ),
			'salesforce-marketing-cloud'         => array( 'Salesforce Marketing Cloud', 'salesforce-marketing-cloud-en', 'salesforce-marketing-cloud-integration' ),
			'totvs-datasul'                      => array( 'TOTVS Datasul', 'totvs-datasul-en', 'totvs-datasul-integration' ),
			'totvs-winthor'                      => array( 'TOTVS Winthor', 'totvs-winthor-en', 'totvs-winthor-integration' ),
			'totvs-logix'                        => array( 'TOTVS Logix', 'totvs-logix-en', 'totvs-logix-integration' ),
			'rd-station-crm'                     => array( 'RD Station CRM', 'rd-station-crm-en', 'rd-station-crm-integration' ),
			'rd-station-marketing'               => array( 'RD Station Marketing', 'rd-station-marketing-en', 'rd-station-marketing-integration' ),
			'thomson-reuters-tax-one'            => array( 'Thomson Reuters Tax One', 'thomson-reuters-tax-one-en', 'thomson-reuters-tax-one-integration' ),
			'freshservice'                       => array( 'Freshservice', 'freshservice-en', 'freshservice-integration' ),
			'servicenow'                         => array( 'ServiceNow', 'servicenow-en', 'servicenow-integration' ),
			'portal-de-api'                      => array( 'API Portal / MCP Server', 'api-portal-mcp-server' ),
			'zendesk'                            => array( 'Zendesk', 'zendesk-en', 'zendesk-integration' ),
			'bionexo'                            => array( 'Bionexo', 'bionexo-en', 'bionexo-integration' ),
			'tasy'                               => array( 'Tasy', 'tasy-en', 'tasy-integration' ),
			'mv'                                 => array( 'MV', 'mv-en', 'mv-integration' ),
			'vtex'                               => array( 'VTEX', 'vtex-en', 'vtex-integration' ),
			'shopify'                            => array( 'Shopify', 'shopify-en', 'shopify-integration' ),
			'magento'                            => array( 'Magento / Adobe Commerce', 'magento-adobe-commerce-en', 'magento-adobe-commerce-integration' ),
			'onblox'                             => array( 'OnBlox (WMS/TMS)', 'onblox-wms-tms-en', 'onblox-wms-tms-integration' ),
			'narwal'                             => array( 'Narwal (Foreign Trade)', 'narwal-foreign-trade' ),
			'neogrid'                            => array( 'Neogrid', 'neogrid-en', 'neogrid-integration' ),
			'target-sistemas'                    => array( 'Target Sistemas (Distribution ERP)', 'target-sistemas-distribution-erp' ),
			'sap-business-one'                   => array( 'SAP Business One', 'sap-business-one-en', 'sap-business-one-integration' ),
			'sap-ecc'                            => array( 'SAP ECC', 'sap-ecc-en', 'sap-ecc-integration' ),
			'oracle-netsuite'                    => array( 'Oracle NetSuite', 'oracle-netsuite-en', 'oracle-netsuite-integration' ),

			// Tecnologia — catálogo de tecnologias e landings de IA.
			'hubspot-crm'                        => array( 'HubSpot CRM', 'hubspot-crm-en', 'hubspot-crm-integration' ),
			'totvs-consinco'                     => array( 'TOTVS Consinco', 'totvs-consinco-en', 'totvs-consinco-integration' ),
			'totvs-linx'                         => array( 'TOTVS Linx', 'totvs-linx-en', 'totvs-linx-integration' ),
			'totvs-rm'                           => array( 'TOTVS RM', 'totvs-rm-en', 'totvs-rm-integration' ),
			'arius-erp'                          => array( 'Arius ERP', 'arius-erp-en', 'arius-erp-integration' ),
			'ciss-poder-erp'                     => array( 'CISS Poder ERP', 'ciss-poder-erp-en', 'ciss-poder-erp-integration' ),
			'ifs-cloud'                          => array( 'IFS Cloud', 'ifs-cloud-en', 'ifs-cloud-integration' ),
			'qad-redzone'                        => array( 'QAD Redzone', 'qad-redzone-en', 'qad-redzone-integration' ),
			'rp-info'                            => array( 'RP Info', 'rp-info-en', 'rp-info-integration' ),
			'viasoft'                            => array( 'Viasoft', 'viasoft-en', 'viasoft-integration' ),
			'onclick-erp'                        => array( 'Onclick ERP', 'onclick-erp-en', 'onclick-erp-integration' ),
			'propz'                              => array( 'Propz', 'propz-en', 'propz-integration' ),
			'microsoft-teams'                    => array( 'Microsoft Teams', 'microsoft-teams-en', 'microsoft-teams-integration' ),
			'snowflake'                          => array( 'Snowflake', 'snowflake-en', 'snowflake-integration' ),
			'databricks'                         => array( 'Databricks', 'databricks-en', 'databricks-integration' ),
			'microsoft-azure'                    => array( 'Microsoft Azure', 'microsoft-azure-en', 'microsoft-azure-integration' ),
			'gemini'                             => array( 'Gemini', 'gemini-en', 'gemini-integration' ),

			// Indústria.
			'servicos-financeiros'               => array( 'Financial Services', 'financial-services' ),
			'manufatura'                         => array( 'Manufacturing', 'manufacturing' ),
			'logistica-3pl'                      => array( 'Logistics (3PL)', 'logistics-3pl' ),
			'software-isv'                       => array( 'Software (ISV)', 'software-isv-en', 'software-isv-solutions' ),
			'varejo'                             => array( 'Retail', 'retail' ),
			'hotelaria-e-turismo'                => array( 'Hospitality and Travel', 'hospitality-and-travel' ),
			'seguros'                            => array( 'Insurance', 'insurance' ),

			// Departamento.
			'recursos-humanos-rh'                => array( 'Human Resources (HR)', 'human-resources-hr' ),
			'operacoes-de-receita-revops'        => array( 'Revenue Operations (RevOps)', 'revenue-operations-revops' ),
			'marketing'                          => array( 'Marketing', 'marketing-en', 'marketing-integration' ),
			'financeiro'                         => array( 'Finance', 'finance' ),

			// Nuvem.
			'aws'                                => array( 'AWS', 'aws-en', 'aws-integration' ),
			'google-cloud'                       => array( 'Google Cloud', 'google-cloud-en', 'google-cloud-integration' ),
			'azure'                              => array( 'Azure', 'azure-en', 'azure-integration' ),

			// Por Iniciativa.
			'atualizacao-de-sistemas-legados'    => array( 'Legacy System Modernisation', 'legacy-system-modernisation' ),
			'pedido-ao-recebimento'              => array( 'Order to Cash', 'order-to-cash' ),
			'ia-corporativa'                     => array( 'Enterprise AI', 'enterprise-ai' ),
			'compras-ao-pagamento'               => array( 'Source to Pay', 'source-to-pay' ),
			'jornada-do-colaborador'             => array( 'Employee Journey', 'employee-journey' ),
			'soberania-de-dados'                 => array( 'Data Sovereignty', 'data-sovereignty' ),
			'visao-360-do-cliente'               => array( '360° Customer View', '360-customer-view' ),
			'modernizacao-de-erp'                => array( 'ERP Modernisation', 'erp-modernisation' ),
			'integracao-pos-fusao'               => array( 'Post-Merger Integration', 'post-merger-integration' ),
			'centro-de-excelencia-em-integracao' => array( 'Integration Center of Excellence', 'integration-center-of-excellence' ),
		);
	}

	/* =====================================================================
	   MENUS
	   ===================================================================== */

	/**
	 * Monta os três menus em inglês, espelhando `criar_menus()`.
	 *
	 * @param array<string,int> $termos_en chave => term_id em inglês.
	 * @return void
	 */
	protected function criar_menus_en( $termos_en ) {
		$tax           = 'cli_categoria_solucao';
		$solucoes_base = home_url( '/' . $this->lang . '/solucoes/' );
		$cases_url     = home_url( '/' . $this->lang . '/cases/' );
		$blog_url      = $this->url_pagina_traduzida( 'blog' );

		$turl = function ( $chave ) use ( $termos_en, $tax, $solucoes_base ) {
			if ( empty( $termos_en[ $chave ] ) ) {
				return $solucoes_base;
			}

			$link = get_term_link( (int) $termos_en[ $chave ], $tax );

			return is_wp_error( $link ) ? $solucoes_base : $link;
		};

		$solucoes_mega = array(
			array(
				'titulo' => 'Technology',
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
				),
			),
			array(
				'titulo' => 'Industry',
				'url'    => $turl( 'industria' ),
				'filhos' => array(
					'Financial Services'     => $turl( 'servicos-financeiros' ),
					'Manufacturing'          => $turl( 'manufatura' ),
					'Logistics (3PL)'        => $turl( 'logistica-3pl' ),
					'Software (ISV)'         => $turl( 'software-isv' ),
					'Retail'                 => $turl( 'varejo' ),
					'Hospitality and Travel' => $turl( 'hotelaria-e-turismo' ),
					'Insurance'              => $turl( 'seguros' ),
				),
			),
			array(
				'titulo' => 'Department',
				'url'    => $turl( 'departamento' ),
				'filhos' => array(
					'Human Resources (HR)'        => $turl( 'recursos-humanos-rh' ),
					'Revenue Operations (RevOps)' => $turl( 'operacoes-de-receita-revops' ),
					'Marketing'                   => $turl( 'marketing' ),
					'Finance'                     => $turl( 'financeiro' ),
				),
			),
			array(
				'titulo' => 'Cloud',
				'url'    => $turl( 'nuvem' ),
				'filhos' => array(
					'AWS'          => $turl( 'aws' ),
					'Google Cloud' => $turl( 'google-cloud' ),
					'Azure'        => $turl( 'azure' ),
				),
			),
			array(
				'titulo' => 'By Initiative',
				'url'    => $turl( 'por-iniciativa' ),
				'filhos' => array(
					'Legacy System Modernisation' => $turl( 'atualizacao-de-sistemas-legados' ),
					'Order to Cash'               => $turl( 'pedido-ao-recebimento' ),
					'Enterprise AI'               => $turl( 'ia-corporativa' ),
					'Source to Pay'               => $turl( 'compras-ao-pagamento' ),
					'Employee Journey'            => $turl( 'jornada-do-colaborador' ),
					'Data Sovereignty'            => $turl( 'soberania-de-dados' ),
					'360° Customer View'          => $turl( 'visao-360-do-cliente' ),
					'ERP Modernisation'           => $turl( 'modernizacao-de-erp' ),
				),
			),
			array(
				'titulo' => 'See all',
				'url'    => $solucoes_base,
			),
		);

		$descricao_produto = 'Integrate all of your systems and put custom AI agents to work across your processes.';

		$this->montar_menu_traduzido(
			'principal',
			'CLI — Main Menu (EN)',
			array(
				array(
					'titulo' => 'Platform',
					'url'    => $this->url_pagina_traduzida( 'plataforma' ),
					'filhos' => array(
						array(
							'titulo'    => 'CLI Connect',
							'url'       => $this->url_pagina_traduzida( 'cli-connect' ),
							'descricao' => $descricao_produto,
						),
						array(
							'titulo'    => 'CLI Signature',
							'url'       => $this->url_pagina_traduzida( 'cli-signature' ),
							'descricao' => $descricao_produto,
						),
					),
				),
				array(
					'titulo' => 'Solutions',
					'url'    => $solucoes_base,
					'filhos' => $solucoes_mega,
				),
				array( 'titulo' => 'SAP Integration', 'url' => $this->url_pagina_traduzida( 'integracao-sap' ) ),
				array( 'titulo' => 'Case studies', 'url' => $cases_url ),
				array( 'titulo' => 'Blog', 'url' => $blog_url ),
				array( 'titulo' => 'Contact', 'url' => $this->url_pagina_traduzida( 'contato' ) ),
			)
		);

		$this->montar_menu_traduzido(
			'rodape',
			'CLI — Footer (EN)',
			array(
				array(
					'titulo' => 'col-platform-resources',
					'url'    => '#',
					'filhos' => array(
						array(
							'titulo' => 'Platform',
							'url'    => $this->url_pagina_traduzida( 'plataforma' ),
							'filhos' => array(
								'CLI Connect'   => $this->url_pagina_traduzida( 'cli-connect' ),
								'CLI Signature' => $this->url_pagina_traduzida( 'cli-signature' ),
							),
						),
						array(
							'titulo' => 'Resources',
							'url'    => $this->url_pagina_traduzida( 'contato' ),
							'filhos' => array(
								'Case studies' => $cases_url,
								'Blog'         => $blog_url,
								'Careers'      => $this->url_pagina_traduzida( 'trabalhe-conosco' ),
								'Contact'      => $this->url_pagina_traduzida( 'contato' ),
							),
						),
					),
				),
				array(
					'titulo' => 'col-technology',
					'url'    => '#',
					'filhos' => array(
						array(
							'titulo' => 'Technology',
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
									'titulo'  => 'See all',
									'url'     => $turl( 'tecnologia' ),
									'classes' => 'link-ver-todos',
								),
							),
						),
					),
				),
				array(
					'titulo' => 'col-industry',
					'url'    => '#',
					'filhos' => array(
						array(
							'titulo' => 'Industry',
							'url'    => $turl( 'industria' ),
							'filhos' => array(
								'Financial Services'     => $turl( 'servicos-financeiros' ),
								'Manufacturing'          => $turl( 'manufatura' ),
								'Logistics (3PL)'        => $turl( 'logistica-3pl' ),
								'Software (ISV)'         => $turl( 'software-isv' ),
								'Retail'                 => $turl( 'varejo' ),
								'Hospitality and Travel' => $turl( 'hotelaria-e-turismo' ),
								'Insurance'              => $turl( 'seguros' ),
							),
						),
					),
				),
				array(
					'titulo' => 'col-department-cloud',
					'url'    => '#',
					'filhos' => array(
						array(
							'titulo' => 'Department',
							'url'    => $turl( 'departamento' ),
							'filhos' => array(
								'Human Resources (HR)'        => $turl( 'recursos-humanos-rh' ),
								'Revenue Operations (RevOps)' => $turl( 'operacoes-de-receita-revops' ),
								'Marketing'                   => $turl( 'marketing' ),
								'Finance'                     => $turl( 'financeiro' ),
							),
						),
						array(
							'titulo' => 'Cloud',
							'url'    => $turl( 'nuvem' ),
							'filhos' => array(
								'AWS'          => $turl( 'aws' ),
								'Google Cloud' => $turl( 'google-cloud' ),
								'Azure'        => $turl( 'azure' ),
							),
						),
					),
				),
				array(
					'titulo' => 'col-initiatives',
					'url'    => '#',
					'filhos' => array(
						array(
							'titulo' => 'By Initiative',
							'url'    => $turl( 'por-iniciativa' ),
							'filhos' => array(
								'Legacy System Modernisation' => $turl( 'atualizacao-de-sistemas-legados' ),
								'Enterprise AI'               => $turl( 'ia-corporativa' ),
								'Source to Pay'               => $turl( 'compras-ao-pagamento' ),
								'Order to Cash'               => $turl( 'pedido-ao-recebimento' ),
								'Employee Journey'            => $turl( 'jornada-do-colaborador' ),
								'Data Sovereignty'            => $turl( 'soberania-de-dados' ),
								'360° Customer View'          => $turl( 'visao-360-do-cliente' ),
								'ERP Modernisation'           => $turl( 'modernizacao-de-erp' ),
							),
						),
					),
				),
			)
		);

		$this->montar_menu_traduzido( 'rodape_legal', 'CLI — Footer Legal (EN)', array() );
	}

	/* =====================================================================
	   LANDINGS
	   ===================================================================== */

	/**
	 * Rótulos que se repetem em toda landing de solução.
	 *
	 * Entram em todas as soluções por `solucao_en()`. Campo vazio no português
	 * é ignorado por `copiar_campos_acf()`, então trazer o CTA de Casos de Uso
	 * aqui não liga a seção onde ela não existe.
	 *
	 * @return array<string,string>
	 */
	protected function base_solucao_en() {
		return array(
			'solucao_hero_btn1_texto' => 'Book a demo',
			'solucao_hero_btn1_url'   => '/en/contact/',
			'solucao_hero_btn2_texto' => 'Explore the platform',
			'solucao_hero_btn2_url'   => '/en/platform/',
			'solucao_pilares_eyebrow' => 'Pillars',
			'solucao_casos_eyebrow'   => 'Use cases',
			'solucao_casos_cta_texto' => 'Book a demo',
			'solucao_casos_cta_url'   => '/en/contact/',
			'solucao_selos_eyebrow'   => 'compliance & security',
			'solucao_selos_titulo'    => 'We lead the market when it comes to compliance and security',
			'solucao_selos_corpo'     => 'Your data, processes and integrations protected by the highest global standards.',
			'solucao_faq_titulo'      => 'Frequently Asked Questions',
			'solucao_dif_eyebrow'     => 'Technical differentiator',
			'solucao_acel_eyebrow'    => 'Integration accelerators',
			'solucao_acel_btn_texto'  => 'Book a demo',
			'solucao_acel_btn_url'    => '/en/contact/',
		);
	}

	/**
	 * Junta os rótulos comuns aos campos próprios de uma landing.
	 *
	 * @param array<string,string> $campos Campos específicos da solução.
	 * @return array<string,string>
	 */
	protected function solucao_en( $campos ) {
		return array_merge( $this->base_solucao_en(), $campos );
	}

	/* --- Indústria ------------------------------------------------------- */

	/**
	 * Serviços Financeiros.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_servicos_financeiros() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'         => 'For financial services',
				'solucao_hero_titulo'          => 'From rollout to production in weeks.',
				'solucao_hero_titulo_destaque' => 'Because banks do not wait.',
				'solucao_hero_corpo'           => 'Connect banking systems, digital platforms and security solutions in a single integration architecture built to keep evolving.',
				'solucao_metrica_1_numero'     => '95%',
				'solucao_metrica_1_rotulo'     => 'faster identity verification',
				'solucao_metrica_2_numero'     => '24,000',
				'solucao_metrica_2_rotulo'     => 'hours of manual work removed',
				'solucao_metrica_3_numero'     => '5%',
				'solucao_metrica_3_rotulo'     => 'increase in NPS',
				'solucao_pilares_titulo'       => 'Integrations that are faster, safer and smarter',
				'solucao_pilares_1_titulo'     => 'Compliance from the architecture up',
				'solucao_pilares_1_desc'       => 'Access control, traceability and governance for highly regulated environments.',
				'solucao_pilares_2_titulo'     => 'Integrations that evolve with the business',
				'solucao_pilares_2_desc'       => 'New flows, changes and improvements are part of the operation, with no new project at every change.',
				'solucao_pilares_3_titulo'     => 'Data ready for automation and AI',
				'solucao_pilares_3_desc'       => 'Turn scattered information into connected processes, ready to feed intelligent agents and real-time analysis.',
				'solucao_logos_texto'          => 'We integrate financial services for major companies',
				'solucao_casos_titulo'         => 'Integrations that are faster, safer and smarter',
				'solucao_casos_1_titulo'       => 'Core Banking connected',
				'solucao_casos_1_desc'         => 'Integrate banking systems with ERPs, CRMs and digital platforms.',
				'solucao_casos_2_titulo'       => 'Real-time payments',
				'solucao_casos_2_desc'         => 'Automate the exchange of information between financial institutions and internal systems.',
				'solucao_casos_3_titulo'       => 'Fraud prevention',
				'solucao_casos_3_desc'         => 'Connect anti-fraud engines, analytics platforms and digital channels.',
				'solucao_casos_4_titulo'       => 'Automated credit',
				'solucao_casos_4_desc'         => 'Orchestrate checks, documents and approvals across several systems.',
				'solucao_casos_5_titulo'       => '360° customer view',
				'solucao_casos_5_desc'         => 'Bring financial, commercial and operational data into a single journey.',
				'solucao_casos_6_titulo'       => 'Data for Artificial Intelligence',
				'solucao_casos_6_desc'         => 'Make reliable information available to intelligent agents and advanced analysis.',
			)
		);
	}

	/**
	 * Manufatura.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_manufatura() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'         => 'For manufacturing',
				'solucao_hero_titulo'          => 'Connect your plant from the shop floor to the cloud',
				'solucao_hero_titulo_destaque' => 'without stopping the operation.',
				'solucao_hero_corpo'           => 'Integrate SAP S/4HANA, MES, WMS, Salesforce and industrial systems to speed up projects, increase operational visibility and modernise manufacturing safely.',
				'solucao_metrica_1_numero'     => '4x',
				'solucao_metrica_1_rotulo'     => 'faster supplier onboarding',
				'solucao_metrica_2_numero'     => '50%',
				'solucao_metrica_2_rotulo'     => 'gain in efficiency',
				'solucao_metrica_3_numero'     => '30s',
				'solucao_metrica_3_rotulo'     => 'for automated order processing',
				'solucao_pilares_titulo'       => 'Modernise your industrial operation with integrations built to scale',
				'solucao_pilares_1_titulo'     => 'See the whole operation in real time',
				'solucao_pilares_1_desc'       => 'Connect production, stock and logistics to follow up-to-date indicators across the plant.',
				'solucao_pilares_2_titulo'     => 'Connect plant and cloud securely',
				'solucao_pilares_2_desc'       => 'Integrate industrial environments with the cloud using zero-trust architecture, without disrupting the operation.',
				'solucao_pilares_3_titulo'     => 'Feed AI initiatives continuously',
				'solucao_pilares_3_desc'       => 'Make production data available in real time for analytics, AI and intelligent automation.',
				'solucao_logos_texto'          => 'We integrate manufacturing for major companies.',
				'solucao_casos_titulo'         => 'Automate the core manufacturing processes',
				'solucao_casos_1_titulo'       => 'Move to SAP S/4HANA with no downtime',
				'solucao_casos_1_desc'         => 'Connect systems during the migration while industrial operations keep running.',
				'solucao_casos_2_titulo'       => 'Automate the Order-to-Cash cycle',
				'solucao_casos_2_desc'         => 'Integrate orders, billing and logistics to cut delays and operational rework.',
				'solucao_casos_3_titulo'       => 'Digitise Procure-to-Pay',
				'solucao_casos_3_desc'         => 'Connect SAP Ariba, ERP and suppliers to speed up purchasing and approvals.',
				'solucao_casos_4_titulo'       => 'Feed AI with production data',
				'solucao_casos_4_desc'         => 'Send industrial data continuously to analytics platforms and AI models.',
				'solucao_casos_5_titulo'       => 'Connect OT and cloud securely',
				'solucao_casos_5_desc'         => 'Integrate MES, IoT and industrial equipment with data platforms without opening the firewall.',
			)
		);
	}

	/**
	 * Logística (3PL).
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_logistica_3pl() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'         => 'For logistics',
				'solucao_hero_titulo'          => 'Connect customers, carriers and logistics systems',
				'solucao_hero_titulo_destaque' => 'on a single platform',
				'solucao_hero_corpo'           => 'Integrate ERPs, WMS, carriers and marketplaces to speed up onboarding of new customers, automate operations and scale your logistics predictably.',
				'solucao_metrica_1_numero'     => '80%',
				'solucao_metrica_1_rotulo'     => 'increase in real-time data accuracy',
				'solucao_metrica_2_numero'     => '50%',
				'solucao_metrica_2_rotulo'     => 'less time to integrate partners and systems',
				'solucao_metrica_3_numero'     => '1',
				'solucao_metrica_3_rotulo'     => 'single platform to connect every customer',
				'solucao_pilares_titulo'       => 'Scale your logistics operation without adding complexity',
				'solucao_pilares_1_titulo'     => 'Speed up onboarding of new customers',
				'solucao_pilares_1_desc'       => 'Reuse integrations between ERPs and WMS to shorten the rollout of new contracts.',
				'solucao_pilares_2_titulo'     => 'Synchronise stock automatically',
				'solucao_pilares_2_desc'       => 'Keep stock positions up to date across customers, logistics operators and enterprise systems.',
				'solucao_pilares_3_titulo'     => 'Automate documents with AI',
				'solucao_pilares_3_desc'       => 'Extract information from PDFs and emails to start logistics processes automatically.',
				'solucao_logos_texto'          => 'We integrate logistics for major companies.',
				'solucao_casos_titulo'         => 'Automate the core logistics processes',
				'solucao_casos_1_titulo'       => 'Synchronise stock positions',
				'solucao_casos_1_desc'         => 'Update balances automatically between WMS, ERP and customer systems in real time.',
				'solucao_casos_2_titulo'       => 'Automate multichannel orders',
				'solucao_casos_2_desc'         => 'Receive orders from marketplaces and route them automatically to picking and dispatch.',
				'solucao_casos_3_titulo'       => 'Connect several carriers',
				'solucao_casos_3_desc'         => 'Centralise carrier integrations without building a separate connection for each operation.',
				'solucao_casos_4_titulo'       => 'Automate returns',
				'solucao_casos_4_desc'         => 'Manage RMA processes between customers, carriers and distribution centres automatically.',
				'solucao_casos_5_titulo'       => 'Predict demand peaks with AI',
				'solucao_casos_5_desc'         => 'Use operational data to anticipate volumes and improve logistics planning.',
			)
		);
	}

	/**
	 * Software (ISV).
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_software_isv() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'         => 'For software companies',
				'solucao_hero_titulo'          => 'Ship native integrations to your customers',
				'solucao_hero_titulo_destaque' => 'without rebuilding connectors for every project',
				'solucao_hero_corpo'           => 'Connect your product to ERPs, CRMs and enterprise applications using reusable integrations, native APIs and a platform built to scale your software.',
				'solucao_metrica_1_numero'     => '4x',
				'solucao_metrica_1_rotulo'     => 'faster delivery of integration and development projects',
				'solucao_metrica_2_numero'     => '350%',
				'solucao_metrica_2_rotulo'     => 'increase in ROI in technology environments',
				'solucao_metrica_3_numero'     => '5 days',
				'solucao_metrica_3_rotulo'     => 'to the first integration',
				'solucao_pilares_titulo'       => 'Turn integrations into a competitive advantage',
				'solucao_pilares_1_titulo'     => 'Connect any ERP or CRM',
				'solucao_pilares_1_desc'       => 'Widen your product compatibility with integrations ready for different enterprise platforms.',
				'solucao_pilares_2_titulo'     => 'Deliver integrations in minutes',
				'solucao_pilares_2_desc'       => 'Roll out the first pipeline quickly using reusable connectors and a low-code architecture.',
				'solucao_pilares_3_titulo'     => 'Scale without raising costs',
				'solucao_pilares_3_desc'       => 'Grow with platform usage, with no per-connector charges to bill or maintain.',
				'solucao_logos_texto'          => 'We integrate software products for major companies.',
				'solucao_casos_titulo'         => 'Ship integrations as part of your product',
				'solucao_casos_1_titulo'       => 'Offer native integrations',
				'solucao_casos_1_desc'         => 'Use reusable components to connect your software to the main enterprise systems.',
				'solucao_casos_2_titulo'       => 'Build AI agents with MCP',
				'solucao_casos_2_desc'         => 'Develop intelligent agents exposed as MCP servers integrated into your product.',
				'solucao_casos_3_titulo'       => 'Deploy in the customer environment',
				'solucao_casos_3_desc'         => 'Run integrations on the customer infrastructure with no VPN or open ports.',
				'solucao_casos_4_titulo'       => 'Monitor every customer',
				'solucao_casos_4_desc'         => 'Centralise metrics, executions and integrations in a single operational dashboard.',
				'solucao_casos_5_titulo'       => 'Connect any AI model',
				'solucao_casos_5_desc'         => 'Orchestrate different LLM providers directly in the product integration flows.',
			)
		);
	}

	/**
	 * Varejo.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_varejo() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'         => 'For retail',
				'solucao_hero_titulo'          => 'Connect',
				'solucao_hero_titulo_destaque' => 'the whole journey',
				'solucao_hero_titulo_fim'      => 'from cart to doorstep.',
				'solucao_hero_corpo'           => 'Integrate e-commerce, ERP, logistics, CRM and marketplaces to deliver consistent experiences, speed up deliveries and evolve your operation without interruption.',
				'solucao_metrica_1_numero'     => '70%',
				'solucao_metrica_1_rotulo'     => 'Reduction in delivery time.',
				'solucao_metrica_2_numero'     => '40%',
				'solucao_metrica_2_rotulo'     => 'faster supplier onboarding',
				'solucao_metrica_3_numero'     => '1600%',
				'solucao_metrica_3_rotulo'     => 'ROI in 10 months',
				'solucao_pilares_titulo'       => 'Turn connected data into better shopping experiences',
				'solucao_pilares_1_titulo'     => 'Unify the customer view',
				'solucao_pilares_1_desc'       => 'Bring sales, service and logistics information together to personalise every interaction with shoppers.',
				'solucao_pilares_2_titulo'     => 'Change platforms without stopping sales',
				'solucao_pilares_2_desc'       => 'Switch e-commerce platforms while operations, orders and integrations keep running normally.',
				'solucao_pilares_3_titulo'     => 'Automate deliveries with artificial intelligence',
				'solucao_pilares_3_desc'       => 'Optimise routes, logistics decisions and delivery processes using real-time data.',
				'solucao_logos_texto'          => 'We integrate retail for major companies.',
				'solucao_casos_titulo'         => 'Automate the entire retail operation',
				'solucao_casos_1_titulo'       => 'Connect shopping experiences',
				'solucao_casos_1_desc'         => 'Integrate physical and digital channels to offer consistent journeys at every touchpoint.',
				'solucao_casos_2_titulo'       => 'Optimise the last mile',
				'solucao_casos_2_desc'         => 'Automate deliveries using operational data to cut costs and improve lead times.',
				'solucao_casos_3_titulo'       => 'Integrate social commerce channels',
				'solucao_casos_3_desc'         => 'Connect orders placed on social networks to commercial and logistics systems.',
				'solucao_casos_4_titulo'       => 'Move your ERP to the cloud',
				'solucao_casos_4_desc'         => 'Modernise your architecture while preserving integrations and commercial continuity.',
				'solucao_casos_5_titulo'       => 'Personalise recommendations with AI',
				'solucao_casos_5_desc'         => 'Use integrated data to recommend products based on behaviour and purchase history.',
				'solucao_casos_6_titulo'       => 'Automate reverse logistics',
				'solucao_casos_6_desc'         => 'Manage returns, refunds and resale viability with intelligent automated flows.',
			)
		);
	}

	/**
	 * Hotelaria e Turismo.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_hotelaria_e_turismo() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'         => 'For hospitality',
				'solucao_hero_titulo'          => 'Connect data, properties and guests',
				'solucao_hero_titulo_destaque' => 'in one integrated experience',
				'solucao_hero_corpo'           => 'Integrate PMS, CRM, booking engines and operational systems to eliminate overbooking, personalise service and speed up the expansion of the hotel group.',
				'solucao_metrica_1_numero'     => '17,000+',
				'solucao_metrica_1_rotulo'     => 'guests and residents managed through synchronised flows',
				'solucao_metrica_2_numero'     => '100%',
				'solucao_metrica_2_rotulo'     => 'of the manual work on booking changes automated',
				'solucao_metrica_3_numero'     => '10x',
				'solucao_metrica_3_rotulo'     => 'faster launch of new services',
				'solucao_pilares_titulo'       => 'Connect the whole hotel operation on a single platform',
				'solucao_pilares_1_titulo'     => 'Synchronise inventory in real time',
				'solucao_pilares_1_desc'       => 'Keep room availability up to date across channels to avoid overbooking and rework.',
				'solucao_pilares_2_titulo'     => 'Personalise the guest experience',
				'solucao_pilares_2_desc'       => 'Unify guest profiles to offer personalised service using artificial intelligence.',
				'solucao_pilares_3_titulo'     => 'Open new properties quickly',
				'solucao_pilares_3_desc'       => 'Standardise integrations by reusing components across new properties and franchises.',
				'solucao_logos_texto'          => 'We integrate hospitality for major companies.',
				'solucao_casos_titulo'         => 'Automate the core hospitality processes',
				'solucao_casos_1_titulo'       => 'Connect PMS and CRM',
				'solucao_casos_1_desc'         => 'Synchronise bookings, preferences and guest history between systems automatically.',
				'solucao_casos_2_titulo'       => 'Automate loyalty programmes',
				'solucao_casos_2_desc'         => 'Integrate POS, bookings and loyalty to offer benefits across every channel.',
				'solucao_casos_3_titulo'       => 'Unify reporting across properties',
				'solucao_casos_3_desc'         => 'Bring operational and financial indicators from every property into one dashboard.',
				'solucao_casos_4_titulo'       => 'Update prices dynamically',
				'solucao_casos_4_desc'         => 'Use occupancy data to automate pricing strategies in real time.',
				'solucao_casos_5_titulo'       => 'Automate room governance',
				'solucao_casos_5_desc'         => 'Integrate housekeeping, bookings and operations to speed up room release and cleaning.',
			)
		);
	}

	/**
	 * Seguros.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_seguros() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'         => 'For insurance',
				'solucao_hero_titulo'          => 'Connect legacy systems and',
				'solucao_hero_titulo_destaque' => 'speed up the launch',
				'solucao_hero_titulo_fim'      => 'of new insurance products',
				'solucao_hero_corpo'           => 'Integrate Guidewire, Duck Creek, Salesforce and other applications without replacing your core, modernising operations safely and quickly.',
				'solucao_metrica_1_numero'     => '10 min',
				'solucao_metrica_1_rotulo'     => 'total time to underwrite a risk',
				'solucao_metrica_2_numero'     => '6',
				'solucao_metrica_2_rotulo'     => 'months to payback on legacy insurance systems',
				'solucao_metrica_3_numero'     => '100%',
				'solucao_metrica_3_rotulo'     => 'regulatory compliance achieved on the exchange of confidential data',
				'solucao_pilares_titulo'       => 'Your insurance operation ready for what comes next',
				'solucao_pilares_1_titulo'     => 'Synchronise data in real time',
				'solucao_pilares_1_desc'       => 'Connect policies, claims and distribution channels with information that is always current.',
				'solucao_pilares_2_titulo'     => 'Automate decisions with AI',
				'solucao_pilares_2_desc'       => 'Use artificial intelligence to speed up underwriting and first-line claims triage.',
				'solucao_pilares_3_titulo'     => 'Connect brokers in real time',
				'solucao_pilares_3_desc'       => 'Give commercial partners up-to-date information through integrated portals.',
				'solucao_logos_texto'          => 'We integrate insurance systems for major companies.',
				'solucao_casos_titulo'         => 'Automate the core processes of the insurance market',
				'solucao_casos_1_titulo'       => 'Connect core systems to the CRM',
				'solucao_casos_1_desc'         => 'Integrate Guidewire, Duck Creek and other platforms with the commercial systems of the insurer.',
				'solucao_casos_2_titulo'       => 'Automate claims management',
				'solucao_casos_2_desc'         => 'Bring notification, assessment, fraud prevention and payment into a single flow.',
				'solucao_casos_3_titulo'       => 'Synchronise broker portals',
				'solucao_casos_3_desc'         => 'Keep agents and partners up to date with consistent information about customers and policies.',
				'solucao_casos_4_titulo'       => 'Meet Open Insurance requirements',
				'solucao_casos_4_desc'         => 'Integrate systems following the regulatory standards and requirements set by SUSEP.',
				'solucao_casos_5_titulo'       => 'Speed up underwriting with AI',
				'solucao_casos_5_desc'         => 'Use intelligent models to support risk analysis and the issuing of new policies.',
			)
		);
	}

	/* --- Departamento ----------------------------------------------------- */

	/**
	 * Recursos Humanos (RH).
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_recursos_humanos_rh() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'         => 'For your HR team',
				'solucao_hero_titulo'          => 'Connect the whole employee lifecycle in',
				'solucao_hero_titulo_destaque' => 'a single operation',
				'solucao_hero_corpo'           => 'Integrate HRIS, payroll, ATS and enterprise systems to automate the employee journey and keep information always in sync.',
				'solucao_metrica_1_numero'     => '70%',
				'solucao_metrica_1_rotulo'     => 'reduction in payroll processing time',
				'solucao_metrica_2_numero'     => '90%',
				'solucao_metrica_2_rotulo'     => 'projected saving on ongoing maintenance costs',
				'solucao_metrica_3_numero'     => '40%',
				'solucao_metrica_3_rotulo'     => 'less time spent on manual data entry',
				'solucao_pilares_titulo'       => 'Automate the entire HR operation',
				'solucao_pilares_1_titulo'     => 'Automate the employee journey',
				'solucao_pilares_1_desc'       => 'Synchronise hires, moves and leavers across every system to remove manual tasks and keep data consistent.',
				'solucao_pilares_2_titulo'     => 'Keep payroll in sync',
				'solucao_pilares_2_desc'       => 'Update data automatically between HRIS and payroll to reduce inconsistencies and simplify the monthly close.',
				'solucao_pilares_3_titulo'     => 'Protect sensitive data',
				'solucao_pilares_3_desc'       => 'Mask personal information during integrations to meet LGPD requirements and strengthen governance.',
				'solucao_casos_titulo'         => 'Automate critical HR processes',
				'solucao_casos_1_titulo'       => 'Orchestrate the employee lifecycle',
				'solucao_casos_1_desc'         => 'Update HRIS, identity, payroll and training platforms at the same time whenever someone joins or leaves the company.',
				'solucao_casos_2_titulo'       => 'Synchronise HRIS and payroll',
				'solucao_casos_2_desc'         => 'Make sure record changes and moves are reflected in payroll automatically.',
				'solucao_casos_3_titulo'       => 'Automate new hires',
				'solucao_casos_3_desc'         => 'Send approved candidates from the ATS to the HRIS automatically, removing duplicate records and manual work.',
				'solucao_casos_4_titulo'       => 'Revoke access automatically',
				'solucao_casos_4_desc'         => 'Remove permissions and accounts within minutes of an exit to increase security and reduce operational risk.',
				'solucao_casos_5_titulo'       => 'Anticipate attrition risk',
				'solucao_casos_5_desc'         => 'Use AI agents to spot retention signals and support decisions before talent is lost.',
				'solucao_casos_6_titulo'       => 'Automate internal moves',
				'solucao_casos_6_desc'         => 'Update roles, teams and permissions whenever something changes.',
				'solucao_dif_titulo'           => 'Privacy built into the automation',
				'solucao_dif_corpo'            => 'Protect sensitive information throughout its movement between systems, with automatic detection and masking of personal data before the integration runs.',
				'solucao_dif_topico_1'         => 'Detect sensitive data automatically',
				'solucao_dif_topico_2'         => 'Mask information before the integration',
				'solucao_dif_topico_3'         => 'Meet LGPD requirements with governance',
			)
		);
	}

	/**
	 * Operações de Receita (RevOps).
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_operacoes_de_receita_revops() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'For your revenue operations',
				'solucao_hero_titulo'      => 'Connect the whole revenue operation.',
				'solucao_hero_corpo'       => 'Synchronise CRM, marketing and customer success in real time to remove bottlenecks, speed up handoffs and keep the whole funnel up to date.',
				'solucao_pilares_titulo'   => 'One connected revenue operation',
				'solucao_pilares_1_titulo' => 'Unify revenue data',
				'solucao_pilares_1_desc'   => 'Connect marketing, sales and customer success to prioritise opportunities with consistent information across the commercial cycle.',
				'solucao_pilares_2_titulo' => 'Automate the handoffs',
				'solucao_pilares_2_desc'   => 'Pass customers from sales to customer success automatically, cutting delays and removing manual steps.',
				'solucao_pilares_3_titulo' => 'Keep the pipeline clean',
				'solucao_pilares_3_desc'   => 'Update records continuously to avoid duplicates, inconsistencies and decisions based on stale information.',
				'solucao_logos_texto'      => 'We integrate the leading CRM, marketing, sales and customer success platforms used by major companies.',
				'solucao_casos_titulo'     => 'Automate the entire revenue flow',
				'solucao_casos_1_titulo'   => 'Prioritise leads automatically',
				'solucao_casos_1_desc'     => 'Combine CRM, marketing automation and enrichment data to qualify opportunities more accurately.',
				'solucao_casos_2_titulo'   => 'Unify multiple CRMs',
				'solucao_casos_2_desc'     => 'Consolidate commercial information from different CRMs into a single view of the pipeline.',
				'solucao_casos_3_titulo'   => 'Trigger post-sale',
				'solucao_casos_3_desc'     => 'Start customer success processes automatically when an opportunity is won, keeping all the context of the sale.',
				'solucao_casos_4_titulo'   => 'Fix commercial data',
				'solucao_casos_4_desc'     => 'Spot and update inconsistent records to keep opportunities, contacts and forecasts reliable.',
				'solucao_casos_5_titulo'   => 'Monitor customer health',
				'solucao_casos_5_desc'     => 'Combine product, support and NPS data to identify risks and expansion opportunities.',
				'solucao_casos_6_titulo'   => 'Automate internal moves',
				'solucao_casos_6_desc'     => 'Update roles, teams and permissions whenever something changes.',
				'solucao_dif_titulo'       => 'More autonomy for RevOps',
				'solucao_dif_corpo'        => 'Let the RevOps team build, adjust and monitor integrations with a visual, AI-assisted builder, without depending on dedicated development.',
				'solucao_dif_topico_1'     => 'Build integrations in a visual builder',
				'solucao_dif_topico_2'     => 'Automate flows with AI support',
				'solucao_dif_topico_3'     => 'Depend less on the IT team',
			)
		);
	}

	/**
	 * Marketing.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_marketing() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'For your marketing team',
				'solucao_hero_titulo'      => 'Connect marketing, CRM and analytics in real time',
				'solucao_hero_corpo'       => 'Get out of the IT queue, synchronise information in real time and deliver more relevant campaigns with intelligent automation across every platform in your ecosystem.',
				'solucao_metrica_1_numero' => '127%',
				'solucao_metrica_1_rotulo' => 'growth in brand awareness',
				'solucao_metrica_2_numero' => '50%',
				'solucao_metrica_2_rotulo' => 'increase in pipeline generation',
				'solucao_metrica_3_numero' => '22%',
				'solucao_metrica_3_rotulo' => 'average monthly growth in the sales funnel',
				'solucao_pilares_titulo'   => 'Marketing connected end to end',
				'solucao_pilares_1_titulo' => 'Leads always in sync',
				'solucao_pilares_1_desc'   => 'Keep the CRM and the automation platform aligned in real time to avoid stale contacts and make campaigns more efficient.',
				'solucao_pilares_2_titulo' => 'Scalability on demand',
				'solucao_pilares_2_desc'   => 'Absorb large lead volumes during launches and seasonal campaigns without hurting performance or needing manual work.',
				'solucao_pilares_3_titulo' => 'Personalisation with governance',
				'solucao_pilares_3_desc'   => 'Use AI to enrich audiences and segments while staying compliant with LGPD, GDPR and corporate data policies.',
				'solucao_logos_texto'      => 'Major companies integrate their marketing with CLI Connect',
				'solucao_casos_titulo'     => 'Automate the whole campaign cycle',
				'solucao_casos_1_titulo'   => 'Synchronise leads in real time',
				'solucao_casos_1_desc'     => 'Move new leads between automation platforms and the CRM in seconds, keeping marketing and sales aligned.',
				'solucao_casos_2_titulo'   => 'Centralise campaign attribution',
				'solucao_casos_2_desc'     => 'Connect Google Ads, LinkedIn and automation platforms to consolidate results and attribution in a single flow.',
				'solucao_casos_3_titulo'   => 'Enrich leads with AI',
				'solucao_casos_3_desc'     => 'Trigger AI agents after a form submission to research information and qualify contacts automatically.',
				'solucao_casos_4_titulo'   => 'Orchestrate intelligent audiences',
				'solucao_casos_4_desc'     => 'Update segments automatically using AI and data from several systems for more relevant campaigns.',
				'solucao_casos_5_titulo'   => 'Close the attribution loop',
				'solucao_casos_5_desc'     => 'Connect marketing, CRM and ERP to measure how campaigns contribute to revenue actually generated.',
				'solucao_casos_6_titulo'   => 'Automate internal moves',
				'solucao_casos_6_desc'     => 'Update roles, teams and permissions whenever something changes.',
				'solucao_dif_titulo'       => 'Data ready to act on',
				'solucao_dif_corpo'        => 'Replace batch syncs with real-time integrations to speed up campaigns, cut inconsistencies and keep marketing and sales working from the same data.',
				'solucao_dif_topico_1'     => 'Sync leads in under 60 seconds',
				'solucao_dif_topico_2'     => 'Remove the lag between marketing and CRM',
				'solucao_dif_topico_3'     => 'Monitor integrations in real time',
			)
		);
	}

	/**
	 * Financeiro.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_financeiro() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your finance team',
				'solucao_hero_titulo'      => 'Connect the whole financial ecosystem.',
				'solucao_hero_corpo'       => 'Integrate ERPs, banks and planning platforms to speed up the close, automate audits and keep every business unit in sync.',
				'solucao_metrica_1_numero' => '7 days',
				'solucao_metrica_1_rotulo' => 'to complete the monthly accounting close',
				'solucao_metrica_2_numero' => '5x',
				'solucao_metrica_2_rotulo' => 'increase in order processing',
				'solucao_metrica_3_numero' => '50%',
				'solucao_metrica_3_rotulo' => 'reduction in monthly close time',
				'solucao_pilares_titulo'   => 'More control over the finance operation',
				'solucao_pilares_1_titulo' => 'Speed up the accounting close',
				'solucao_pilares_1_desc'   => 'Synchronise information between ERPs and financial systems to cut manual work and finish the close faster.',
				'solucao_pilares_2_titulo' => 'Automate the audit trail',
				'solucao_pilares_2_desc'   => 'Record every movement with full traceability to simplify audits and make processes more reliable.',
				'solucao_pilares_3_titulo' => 'Unify your ERPs',
				'solucao_pilares_3_desc'   => 'Keep financial data consistent across business units, subsidiaries and enterprise systems.',
				'solucao_logos_texto'      => 'We integrate the main ERPs, banks and financial platforms used by major companies.',
				'solucao_casos_titulo'     => 'Automate the core financial processes',
				'solucao_casos_1_titulo'   => 'Consolidate accounting data',
				'solucao_casos_1_desc'     => 'Synchronise information across different ERPs to consolidate trial balances and get a unified financial view.',
				'solucao_casos_2_titulo'   => 'Automate bank reconciliation',
				'solucao_casos_2_desc'     => 'Integrate banks host-to-host to run daily reconciliation faster and with less manual work.',
				'solucao_casos_3_titulo'   => 'Optimise accounts payable',
				'solucao_casos_3_desc'     => 'Connect purchasing platforms and the ERP to automate three-way matching and reduce operational rework.',
				'solucao_casos_4_titulo'   => 'Recognise revenue automatically',
				'solucao_casos_4_desc'     => 'Send approved sales to the ERP in real time and speed up revenue accounting.',
				'solucao_casos_5_titulo'   => 'Feed financial planning',
				'solucao_casos_5_desc'     => 'Update FP&A platforms automatically with ERP data to improve forecasts and financial analysis.',
				'solucao_casos_6_titulo'   => 'Automate internal moves',
				'solucao_casos_6_desc'     => 'Update roles, teams and permissions whenever something changes.',
				'solucao_dif_titulo'       => 'Integrations under your control',
				'solucao_dif_corpo'        => 'Run integrations inside your own company infrastructure for data sovereignty, greater operational control and compliance with corporate policy.',
				'solucao_dif_topico_1'     => 'Run integrations in your own cloud',
				'solucao_dif_topico_2'     => 'Keep data under corporate governance',
				'solucao_dif_topico_3'     => 'Reduce financial compliance risk',
			)
		);
	}

	/* --- Por Iniciativa --------------------------------------------------- */

	/**
	 * Atualização de Sistemas Legados.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_atualizacao_de_sistemas_legados() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'         => 'legacy system replacement',
				'solucao_hero_titulo'          => 'Rebuild your architecture.',
				'solucao_hero_titulo_destaque' => 'Keep your operation running.',
				'solucao_hero_corpo'           => 'Replace legacy platforms such as TIBCO and IBM MQ with a modern integration architecture, keeping your existing systems and your operation running throughout the transition.',
				'solucao_pilares_titulo'       => 'Rebuild your integration layer without rebuilding your systems',
				'solucao_pilares_1_titulo'     => 'Open integration up to more people',
				'solucao_pilares_1_desc'       => 'Depend less on legacy technology specialists with a visual platform that is simpler to evolve and maintain.',
				'solucao_pilares_2_titulo'     => 'Build on open standards',
				'solucao_pilares_2_desc'       => 'Develop integrations on modern, portable standards, avoiding a new technology lock-in.',
				'solucao_pilares_3_titulo'     => 'Move to real-time events',
				'solucao_pilares_3_desc'       => 'Replace batch processes with an event-driven architecture, ready for modern applications and distributed integrations.',
				'solucao_casos_titulo'         => 'Integrations that are faster, safer and smarter',
				'solucao_casos_1_titulo'       => 'Rebuild TIBCO BusinessWorks routes',
				'solucao_casos_1_desc'         => 'Turn existing integrations into visual flows that are simpler to maintain and evolve.',
				'solucao_casos_2_titulo'       => 'Connect mainframes without VPN',
				'solucao_casos_2_desc'         => 'Integrate z/OS and AS/400 environments using the Runtime, with no change to the network infrastructure.',
				'solucao_casos_3_titulo'       => 'Replace IBM MQ with events',
				'solucao_casos_3_desc'         => 'Convert queue-based integrations to an event-driven architecture with Kafka.',
				'solucao_casos_4_titulo'       => 'Expose legacy ERPs through APIs',
				'solucao_casos_4_desc'         => 'Make SAP ECC and Oracle EBS available through modern APIs without changing the application core.',
				'solucao_casos_5_titulo'       => 'Connect SaaS applications on day one',
				'solucao_casos_5_desc'         => 'Integrate Salesforce, ServiceNow, Workday and other platforms with the new architecture without relying on the old ESB.',
				'solucao_dif_titulo'           => 'Your next project should not start with the legacy',
				'solucao_dif_corpo'            => 'Rebuild the integration layer on a modern architecture without requiring the existing systems to be replaced.',
				'solucao_dif_topico_1'         => 'Evolve the architecture without exposing critical systems',
				'solucao_dif_topico_2'         => 'Modernise mainframes without compromising security',
				'solucao_dif_topico_3'         => 'Connect legacy applications with the core isolated',
			)
		);
	}

	/**
	 * Integração Pós-Fusão.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_integracao_pos_fusao() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'post-merger integration',
				'solucao_hero_titulo'      => 'Integrate acquired companies from day one',
				'solucao_hero_corpo'       => 'Connect critical systems without opening firewall ports and start capturing synergies while the IT consolidation is still under way.',
				'solucao_pilares_titulo'   => 'Get to results faster after an acquisition',
				'solucao_pilares_1_titulo' => 'Turn systems on before closing',
				'solucao_pilares_1_desc'   => 'Have identity, payroll and ERP available before the deal closes to guarantee operational continuity.',
				'solucao_pilares_2_titulo' => 'Deliver synergies on schedule',
				'solucao_pilares_2_desc'   => 'Connect dual-ERP environments and hit integration targets without waiting for a full consolidation.',
				'solucao_pilares_3_titulo' => 'Reuse integrations across acquisitions',
				'solucao_pilares_3_desc'   => 'Build reusable capsules to speed up new integrations while keeping standards consistent between companies.',
				'solucao_casos_titulo'     => 'Integrate operations without slowing the business',
				'solucao_casos_1_titulo'   => 'Unify corporate identities',
				'solucao_casos_1_desc'     => 'Connect Entra ID and Okta to give single sign-on to employees of the integrated companies.',
				'solucao_casos_2_titulo'   => 'Synchronise multiple ERPs',
				'solucao_casos_2_desc'     => 'Integrate SAP and Oracle Fusion during the transition without waiting for the final consolidation.',
				'solucao_casos_3_titulo'   => 'Consolidate HR data',
				'solucao_casos_3_desc'     => 'Connect Workday and Oracle HCM to unify processes and information after the merger.',
				'solucao_casos_4_titulo'   => 'Migrate your CRM',
				'solucao_casos_4_desc'     => 'Move commercial information between platforms while keeping customer relationships running.',
				'solucao_casos_5_titulo'   => 'Unify analytics data',
				'solucao_casos_5_desc'     => 'Connect Snowflake and BigQuery to build a consolidated view of the combined operations.',
				'solucao_dif_titulo'       => 'Secure integration from Day 1',
				'solucao_dif_corpo'        => 'Connect acquired systems quickly on an architecture built for enterprise environments, with no complex infrastructure changes required.',
				'solucao_dif_topico_1'     => 'Runtime with outbound-only connection',
				'solucao_dif_topico_2'     => 'Multi-cloud or managed Kubernetes deployment',
				'solucao_dif_topico_3'     => '300+ connectors at no extra cost',
			)
		);
	}

	/**
	 * IA Corporativa.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_ia_corporativa() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'enterprise AI',
				'solucao_hero_titulo'      => 'Your enterprise data, ready for AI agents',
				'solucao_hero_corpo'       => 'Connect Salesforce, SAP, TOTVS, Senior, ServiceNow and other enterprise systems to any LLM to build intelligent agents that understand data and take action.',
				'solucao_pilares_titulo'   => 'Turn data into operational intelligence',
				'solucao_pilares_1_titulo' => 'Work with live data',
				'solucao_pilares_1_desc'   => 'Let AI agents query current information from your systems, instead of deciding on stale data.',
				'solucao_pilares_2_titulo' => 'Automate complex workflows',
				'solucao_pilares_2_desc'   => 'Build agents that can run multi-step processes, cutting manual tasks and speeding up operations.',
				'solucao_pilares_3_titulo' => 'Apply security from the start',
				'solucao_pilares_3_desc'   => 'Use PII controls and guardrails to keep agents acting within company rules.',
				'solucao_diagrama_titulo'  => 'A new way to connect AI to your systems',
				'solucao_casos_titulo'     => 'Apply AI to your business processes',
				'solucao_casos_1_titulo'   => 'Build real-time agents',
				'solucao_casos_1_desc'     => 'Generate intelligent summaries for sales reps using current customer, operational and enterprise data.',
				'solucao_casos_2_titulo'   => 'Connect AI to internal knowledge',
				'solucao_casos_2_desc'     => 'Use RAG with Confluence and SharePoint to build answers grounded in your company knowledge.',
				'solucao_casos_3_titulo'   => 'Expose tools through MCP',
				'solucao_casos_3_desc'     => 'Turn Salesforce and SAP capabilities into tools available to authenticated AI agents.',
				'solucao_casos_4_titulo'   => 'Automate operations with AI',
				'solucao_casos_4_desc'     => 'Automate tasks such as raising incidents in ServiceNow without relying on manual processes.',
				'solucao_casos_5_titulo'   => 'Trigger AI from events',
				'solucao_casos_5_desc'     => 'Run language models automatically when events happen, with no constant polling.',
				'solucao_dif_titulo'       => 'AI connected with enterprise security',
				'solucao_dif_corpo'        => 'Reach critical environments without depending on complex VPNs',
				'solucao_dif_topico_1'     => 'Runtime for a direct connection to mainframes',
				'solucao_dif_topico_2'     => 'Fewer infrastructure approvals',
				'solucao_dif_topico_3'     => 'Faster migration of legacy systems',
			)
		);
	}

	/**
	 * Visão 360° do Cliente.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_visao_360_do_cliente() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => '360° view',
				'solucao_hero_titulo'      => 'One single view of the customer across every system',
				'solucao_hero_corpo'       => 'Consolidate CRM, ERP, support and product data into a 360° view kept current in real time for both teams and AI agents.',
				'solucao_pilares_titulo'   => 'Turn scattered data into complete context',
				'solucao_pilares_1_titulo' => 'Unify the customer identity',
				'solucao_pilares_1_desc'   => 'Consolidate CRM, ERP, support and product information into a single, consistent profile.',
				'solucao_pilares_2_titulo' => 'Update information in real time',
				'solucao_pilares_2_desc'   => 'Keep the customer view always in sync, with no batch loads or outdated reports.',
				'solucao_pilares_3_titulo' => 'Share the same context',
				'solucao_pilares_3_desc'   => 'Give sales, support, customer success and AI agents the same unified view.',
				'solucao_casos_titulo'     => 'Put the customer at the centre of operations',
				'solucao_casos_1_titulo'   => 'Resolve duplicate identities',
				'solucao_casos_1_desc'     => 'Reconcile multiple identifiers across CRM, ERP and support to build a single customer profile.',
				'solucao_casos_2_titulo'   => 'Unify the customer history',
				'solucao_casos_2_desc'     => 'Bring orders, tickets and product usage into a single view for customer success.',
				'solucao_casos_3_titulo'   => 'Give AI agents context',
				'solucao_casos_3_desc'     => 'Deliver the full customer picture before every automated or assisted interaction.',
				'solucao_casos_4_titulo'   => 'Segment campaigns in real time',
				'solucao_casos_4_desc'     => 'Update marketing audiences using consolidated data from every connected system.',
				'solucao_casos_5_titulo'   => 'Improve service decisions',
				'solucao_casos_5_desc'     => 'Let teams see the full customer context during any interaction.',
				'solucao_dif_titulo'       => 'Governance for unified data',
				'solucao_dif_corpo'        => 'Control how each system reaches the unified customer profile, keeping data compliant and reliable.',
				'solucao_dif_topico_1'     => 'Governance compatible with LGPD and GDPR',
				'solucao_dif_topico_2'     => 'Read and write control per system',
				'solucao_dif_topico_3'     => 'Centralised management of customer attributes',
				'solucao_acel_titulo'      => 'A template to start from',
				'solucao_acel_corpo'       => 'Get going quickly with a pre-configured flow connecting order, billing, collection and financial reconciliation.',
				'solucao_acel_topico_1'    => 'Automatic identity resolution',
				'solucao_acel_topico_2'    => '360° view kept current in real time',
				'solucao_acel_topico_3'    => 'One context for teams and AI',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * Compras ao Pagamento (S2P).
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_compras_ao_pagamento() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'source to pay (s2p)',
				'solucao_hero_titulo'      => 'From supplier to payment, with no spreadsheets in between',
				'solucao_hero_corpo'       => 'Connect purchasing, ERP, contracts and banks in a single flow to control every step of the supply cycle with full traceability.',
				'solucao_metrica_1_numero' => '26,000',
				'solucao_metrica_1_rotulo' => 'purchasing hours removed',
				'solucao_metrica_2_numero' => '80x',
				'solucao_metrica_2_rotulo' => 'faster invoice processing',
				'solucao_metrica_3_numero' => '70%',
				'solucao_metrica_3_rotulo' => 'faster supplier onboarding',
				'solucao_pilares_titulo'   => 'Full control of the purchasing cycle',
				'solucao_pilares_1_titulo' => 'Connect the whole purchasing flow',
				'solucao_pilares_1_desc'   => 'Bring quoting, approval, ordering and payment into a single traceable, connected process.',
				'solucao_pilares_2_titulo' => 'Remove manual approvals',
				'solucao_pilares_2_desc'   => 'Shorten the purchasing cycle by removing dependencies on emails and manual steps.',
				'solucao_pilares_3_titulo' => 'See where the money goes',
				'solucao_pilares_3_desc'   => 'Follow spend in real time to make financial decisions more accurately.',
				'solucao_casos_titulo'     => 'Automate every step of procurement',
				'solucao_casos_1_titulo'   => 'Raise purchase orders automatically',
				'solucao_casos_1_desc'     => 'Turn approved requisitions into purchase orders in the ERP with no manual step.',
				'solucao_casos_2_titulo'   => 'Automate three-way matching',
				'solucao_casos_2_desc'     => 'Validate order, receipt and invoice automatically before releasing payment.',
				'solucao_casos_3_titulo'   => 'Trigger payments automatically',
				'solucao_casos_3_desc'     => 'Pay suppliers once the required documents are approved and checked.',
				'solucao_casos_4_titulo'   => 'Consolidate strategic spend',
				'solucao_casos_4_desc'     => 'Unify spend by category and supplier to improve negotiations and purchasing decisions.',
				'solucao_casos_5_titulo'   => 'Track the whole purchasing cycle',
				'solucao_casos_5_desc'     => 'Follow every step from requisition to payment with a complete history and operational view.',
				'solucao_dif_titulo'       => 'Governance on every transaction',
				'solucao_dif_corpo'        => 'Keep control over approvals and payments with full traceability and separation between critical duties.',
				'solucao_dif_topico_1'     => 'Complete approval history',
				'solucao_dif_topico_2'     => 'Segregation between approving and paying',
				'solucao_dif_topico_3'     => 'Control over the whole financial flow',
				'solucao_acel_titulo'      => 'A template to start from',
				'solucao_acel_corpo'       => 'Get going quickly with a pre-configured flow connecting order, billing, collection and financial reconciliation.',
				'solucao_acel_topico_1'    => 'Requisition → approval → order',
				'solucao_acel_topico_2'    => 'Automated three-way matching',
				'solucao_acel_topico_3'    => 'Payment after checking',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * Soberania de Dados.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_soberania_de_dados() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'data sovereignty',
				'solucao_hero_titulo'      => 'Process and store data where your operation requires.',
				'solucao_hero_corpo'       => 'Run CLI Connect powered by Boomi inside the customer environment, so sensitive data stays in the jurisdiction the business and the regulator require.',
				'solucao_pilares_titulo'   => 'Full control over data residency',
				'solucao_pilares_1_titulo' => 'Deploy in your own environment',
				'solucao_pilares_1_desc'   => 'Run integrations on the customer cloud or infrastructure, using AWS, Azure, GCP or an internal data centre.',
				'solucao_pilares_2_titulo' => 'Keep data under your control',
				'solucao_pilares_2_desc'   => 'Make sure sensitive information never travels through or rests in shared environments.',
				'solucao_pilares_3_titulo' => 'Meet data regulations',
				'solucao_pilares_3_desc'   => 'Comply with data residency requirements for sectors such as financial services, healthcare and the public sector.',
				'solucao_casos_titulo'     => 'Data sovereignty in practice',
				'solucao_casos_1_titulo'   => 'Deploy by regulatory region',
				'solucao_casos_1_desc'     => 'Run pipelines inside the cloud or region required by local data regulation.',
				'solucao_casos_2_titulo'   => 'Protect sensitive data',
				'solucao_casos_2_desc'     => 'Process financial and health information without taking data out of the defined jurisdiction.',
				'solucao_casos_3_titulo'   => 'Operate across countries',
				'solucao_casos_3_desc'     => 'Build multi-region architectures to meet different data laws in each market.',
				'solucao_casos_4_titulo'   => 'Evidence compliance',
				'solucao_casos_4_desc'     => 'Audit where each piece of data was processed to demonstrate control and meet regulatory requirements.',
				'solucao_casos_5_titulo'   => 'Control critical environments',
				'solucao_casos_5_desc'     => 'Keep integrations running inside the infrastructure your organisation chose.',
				'solucao_dif_titulo'       => 'Sovereignty guaranteed by the architecture',
				'solucao_dif_corpo'        => 'Unlike shared environments, the platform runs inside the customer environment, keeping data and processing under their control.',
				'solucao_dif_topico_1'     => 'Environment dedicated to the customer',
				'solucao_dif_topico_2'     => 'Control over processing and storage',
				'solucao_dif_topico_3'     => 'An architecture with no shared data',
				'solucao_acel_titulo'      => 'A template to start from',
				'solucao_acel_corpo'       => 'Get going quickly with a pre-configured flow connecting order, billing, collection and financial reconciliation.',
				'solucao_acel_topico_1'    => 'Choose the deployment region',
				'solucao_acel_topico_2'    => 'A template ready for regulated environments',
				'solucao_acel_topico_3'    => 'Runs on AWS, Azure or GCP',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * Centro de Excelência em Integração.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_centro_de_excelencia_em_integracao() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'integration center of excellence',
				'solucao_hero_titulo'      => 'Turn integrations into a reusable company asset',
				'solucao_hero_corpo'       => 'Build an Integration Center of Excellence with a shared catalogue, development standards and governance to speed up every new project.',
				'solucao_pilares_titulo'   => 'Standardise integrations across the organisation',
				'solucao_pilares_1_titulo' => 'Reuse existing integrations',
				'solucao_pilares_1_desc'   => 'Centralise reusable pipelines and capsules to cut rework and speed up new projects.',
				'solucao_pilares_2_titulo' => 'Standardise development',
				'solucao_pilares_2_desc'   => 'Set single standards for naming, authentication and error handling across every integration.',
				'solucao_pilares_3_titulo' => 'Strengthen governance',
				'solucao_pilares_3_desc'   => 'Control who creates, changes and publishes critical integrations with standard approval processes.',
				'solucao_casos_titulo'     => 'Scale integrations with governance',
				'solucao_casos_1_titulo'   => 'Centralise reusable integrations',
				'solucao_casos_1_desc'     => 'Offer an internal catalogue of integrations to speed up any new project.',
				'solucao_casos_2_titulo'   => 'Standardise errors and retries',
				'solucao_casos_2_desc'     => 'Make sure every pipeline uses the same rules for handling and recovering from failures.',
				'solucao_casos_3_titulo'   => 'Approve integrations before production',
				'solucao_casos_3_desc'     => 'Put review and approval flows in place to guarantee quality and compliance before deployment.',
				'solucao_casos_4_titulo'   => 'Monitor cost and performance',
				'solucao_casos_4_desc'     => 'Centralise usage, performance and consumption metrics to keep optimising your integrations.',
				'solucao_casos_5_titulo'   => 'Avoid duplicate integrations',
				'solucao_casos_5_desc'     => 'Let teams reuse existing components instead of rebuilding flows that already exist.',
				'solucao_dif_titulo'       => 'Governance for critical integrations',
				'solucao_dif_corpo'        => 'Put controls in place that guarantee security, traceability and quality across the whole integration development cycle.',
				'solucao_dif_topico_1'     => 'Role-based access control',
				'solucao_dif_topico_2'     => 'Review and approval flow',
				'solucao_dif_topico_3'     => 'Audit trail of pipeline changes',
				'solucao_acel_titulo'      => 'A template to start from',
				'solucao_acel_corpo'       => 'Get going quickly with a pre-configured flow connecting order, billing, collection and financial reconciliation.',
				'solucao_acel_topico_1'    => 'A catalogue of reusable capsules',
				'solucao_acel_topico_2'    => 'Single standards for new projects',
				'solucao_acel_topico_3'    => 'Governance ready to scale',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * Jornada do Colaborador (H2R).
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_jornada_do_colaborador() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'employee journey (h2r)',
				'solucao_hero_titulo'      => 'From day one to the exit, every HR system up to date.',
				'solucao_hero_corpo'       => 'Orchestrate the complete employee lifecycle by connecting HR, payroll, access and benefits in a single automated flow.',
				'solucao_metrica_1_numero' => '5x',
				'solucao_metrica_1_rotulo' => 'faster hiring decisions',
				'solucao_metrica_2_numero' => '75%',
				'solucao_metrica_2_rotulo' => 'faster workforce onboarding',
				'solucao_metrica_3_numero' => '95%',
				'solucao_metrica_3_rotulo' => 'less manual work for users',
				'solucao_pilares_titulo'   => 'Automate every moment of the employee journey',
				'solucao_pilares_1_titulo' => 'Synchronise events automatically',
				'solucao_pilares_1_desc'   => 'Update every satellite system from events such as hiring, promotion and exit.',
				'solucao_pilares_2_titulo' => 'Speed up onboarding',
				'solucao_pilares_2_desc'   => 'Cut the time to activate a new joiner from days to hours with connected processes.',
				'solucao_pilares_3_titulo' => 'Revoke access at exit',
				'solucao_pilares_3_desc'   => 'Remove risk by making sure physical and digital access is revoked automatically.',
				'solucao_casos_titulo'     => 'Automate the employee lifecycle',
				'solucao_casos_1_titulo'   => 'Automate complete onboarding',
				'solucao_casos_1_desc'     => 'Connect HRIS, payroll, email, access, benefits and LMS in a single activation.',
				'solucao_casos_2_titulo'   => 'Update role changes',
				'solucao_casos_2_desc'     => 'Synchronise access level and salary band automatically during promotions and internal moves.',
				'solucao_casos_3_titulo'   => 'Run secure offboarding',
				'solucao_casos_3_desc'     => 'Revoke physical and logical access within minutes, reducing risk after someone leaves.',
				'solucao_casos_4_titulo'   => 'Analyse employee data',
				'solucao_casos_4_desc'     => 'Consolidate lifecycle information for turnover and tenure analysis.',
				'solucao_casos_5_titulo'   => 'Connect satellite HR systems',
				'solucao_casos_5_desc'     => 'Make sure every related system gets its updates without relying on manual checklists.',
				'solucao_dif_titulo'       => 'Security at every status change',
				'solucao_dif_corpo'        => 'Protect sensitive employee data with security controls and traceability on every update.',
				'solucao_dif_topico_1'     => 'PII masking in transit',
				'solucao_dif_topico_2'     => 'Complete audit trail of changes',
				'solucao_dif_topico_3'     => 'Traceability of every event',
				'solucao_acel_titulo'      => 'A template to start from',
				'solucao_acel_corpo'       => 'Get going quickly with a pre-configured flow connecting order, billing, collection and financial reconciliation.',
				'solucao_acel_topico_1'    => 'HR event → every system',
				'solucao_acel_topico_2'    => 'End-to-end automated onboarding',
				'solucao_acel_topico_3'    => 'Promotion and exit in sync',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * Pedido ao Recebimento (O2C).
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_pedido_ao_recebimento() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'         => 'order to cash (o2c)',
				'solucao_hero_titulo'          => 'Connect sales, billing and collection in',
				'solucao_hero_titulo_destaque' => 'a single flow',
				'solucao_hero_corpo'           => 'Speed up the full revenue cycle by connecting CRM, ERP, banks and payment systems into one integrated, traceable operation with no manual steps.',
				'solucao_metrica_1_numero'     => '7 days',
				'solucao_metrica_1_rotulo'     => 'faster financial close',
				'solucao_metrica_2_numero'     => '95%',
				'solucao_metrica_2_rotulo'     => 'faster order creation',
				'solucao_metrica_3_numero'     => '6,000',
				'solucao_metrica_3_rotulo'     => 'hours of manual work saved every year',
				'solucao_pilares_titulo'       => 'Rebuild your architecture, not your business.',
				'solucao_pilares_1_titulo'     => 'Remove operational rework',
				'solucao_pilares_1_desc'       => 'Automate the exchange of data between order, billing and collection, removing manual entries.',
				'solucao_pilares_2_titulo'     => 'Get paid faster',
				'solucao_pilares_2_desc'       => 'Shorten the time between closing the sale, issuing the invoice and recognising the cash.',
				'solucao_pilares_3_titulo'     => 'See the whole picture',
				'solucao_pilares_3_desc'       => 'Follow every step from order to cash with consistent data across every area.',
				'solucao_casos_titulo'         => 'Integrations that are faster, safer and smarter',
				'solucao_casos_1_titulo'       => 'Invoice automatically',
				'solucao_casos_1_desc'         => 'Turn closed CRM deals into billing and invoice issuing in the ERP.',
				'solucao_casos_2_titulo'       => 'Reconcile receipts',
				'solucao_casos_2_desc'         => 'Match payments received against banks and acquirers automatically.',
				'solucao_casos_3_titulo'       => 'Flag overdue accounts',
				'solucao_casos_3_desc'         => 'Send automatic alerts to the sales team whenever a payment is late.',
				'solucao_casos_4_titulo'       => 'Monitor DSO',
				'solucao_casos_4_desc'         => 'Consolidate days-sales-outstanding indicators in a single management dashboard.',
				'solucao_casos_5_titulo'       => 'Keep the operation in sync',
				'solucao_casos_5_desc'         => 'Share order status between sales, finance and logistics in real time.',
				'solucao_casos_6_titulo'       => 'Update data continuously',
				'solucao_casos_6_desc'         => 'Propagate changes between CRM, ERP and financial systems with no manual step.',
				'solucao_dif_titulo'           => 'Full traceability across the financial cycle',
				'solucao_dif_corpo'            => 'Protect financial information and follow every step from order to cash with complete transparency and governance.',
				'solucao_dif_topico_1'         => 'Complete process audit trail',
				'solucao_dif_topico_2'         => 'Data protected end to end',
				'solucao_dif_topico_3'         => 'Detailed transaction history',
				'solucao_acel_titulo'          => 'A template to start from',
				'solucao_acel_corpo'           => 'Get going quickly with a pre-configured flow connecting order, billing, collection and financial reconciliation.',
				'solucao_acel_topico_1'        => 'Generate billing automatically',
				'solucao_acel_topico_2'        => 'Issue integrated invoices',
				'solucao_acel_topico_3'        => 'Reconcile receipts with banks',
				'solucao_acel_topico_4'        => 'And much more...',
			)
		);
	}

	/* --- Tecnologia ------------------------------------------------------- */

	/**
	 * SAP.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_sap() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your SAP',
				'solucao_hero_titulo'      => 'Speed up your SAP S/4HANA migration without disrupting the operation',
				'solucao_hero_corpo'       => 'Connect SAP to the rest of your ecosystem, preserve a Clean Core environment and run the migration with more safety, speed and predictability.',
				'solucao_pilares_titulo'   => 'Everything you need to integrate your SAP',
				'solucao_pilares_1_titulo' => 'Migrate with confidence',
				'solucao_pilares_1_desc'   => 'Run your SAP S/4HANA migration on an architecture built to reduce risk, rework and impact on the operation.',
				'solucao_pilares_2_titulo' => 'Unify the whole ecosystem',
				'solucao_pilares_2_desc'   => 'Integrate SAP, Salesforce, Workday, ServiceNow and other applications on a single platform, simplifying integration management.',
				'solucao_pilares_3_titulo' => 'Cut integration costs',
				'solucao_pilares_3_desc'   => 'Use a certified SAP add-on, connectors (RFC, IDoc, BAPI) and protocols (OData, REST, SOAP). All at no extra cost.',
				'solucao_casos_titulo'     => 'Automate the most critical SAP processes',
				'solucao_casos_1_titulo'   => 'Integrated order to cash',
				'solucao_casos_1_desc'     => 'Synchronise opportunities, orders and invoicing between Salesforce and SAP S/4HANA automatically.',
				'solucao_casos_2_titulo'   => 'Migration with no interruptions',
				'solucao_casos_2_desc'     => 'Run the coexistence period between SAP ECC and S/4HANA keeping both in sync throughout the transition.',
				'solucao_casos_3_titulo'   => 'SAP connected to AI',
				'solucao_casos_3_desc'     => 'Let AI agents query SAP information to speed up analysis and operations.',
				'solucao_casos_4_titulo'   => 'Automate corporate procurement',
				'solucao_casos_4_desc'     => 'Integrate SAP with the leading procurement systems, such as Ariba and Coupa, removing operational rework.',
				'solucao_casos_5_titulo'   => 'Send data to Analytics',
				'solucao_casos_5_desc'     => 'Feed platforms such as Snowflake and BigQuery with up-to-date SAP data automatically.',
				'solucao_dif_titulo'       => 'Native, secure integration built for SAP environments',
				'solucao_dif_corpo'        => 'Connect your SAP environment using native platform resources, preserving the security of the infrastructure and reducing the need for intermediate components.',
				'solucao_dif_topico_1'     => 'Use the native RFC, BAPI and IDoc connectors.',
				'solucao_dif_topico_2'     => 'Use the native add-on, SOAP, OData and REST',
				'solucao_dif_topico_3'     => 'Preserve the Clean Core architecture.',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Centralise every SAP integration',
				'solucao_plat_corpo'       => 'Replace isolated integrations with a single platform able to connect SAP, enterprise applications, data and automations under centralised governance.',
				'solucao_plat_topico_1'    => 'Reuse integrations across different projects.',
				'solucao_plat_topico_2'    => 'Standardise the whole integration architecture.',
				'solucao_plat_topico_3'    => 'Cut ongoing maintenance costs.',
				'solucao_acel_titulo'      => 'Start from integrations that are already proven',
				'solucao_acel_corpo'       => 'Use ready-made templates to speed up the rollout of the most common integrations between SAP and the leading market systems.',
				'solucao_acel_topico_1'    => 'Take advantage of Order-to-Cash templates.',
				'solucao_acel_topico_2'    => 'Cut rollout time.',
				'solucao_acel_topico_3'    => 'Adapt flows to your environment.',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * Salesforce.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_salesforce() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'         => 'For your Salesforce',
				'solucao_hero_titulo'          => 'Salesforce without barriers.',
				'solucao_hero_titulo_destaque' => 'Integration without limits.',
				'solucao_hero_corpo'           => 'Connect Salesforce to any ERP or database with complete flexibility and remove the limits from your data architecture.',
				'solucao_pilares_titulo'       => 'Integrations that are faster, safer and smarter',
				'solucao_pilares_1_titulo'     => 'Automated approval flows',
				'solucao_pilares_1_desc'       => 'Trigger approval flows automatically whenever something changes in Salesforce',
				'solucao_pilares_2_titulo'     => 'Auditable bulk operations',
				'solucao_pilares_2_desc'       => 'Run bulk operations with full traceability, centralised auditing and more safety for large-scale changes.',
				'solucao_pilares_3_titulo'     => 'Secure integration with internal environments',
				'solucao_pilares_3_desc'       => 'Connect Salesforce to internal company systems without opening firewall ports, preserving the security of the corporate infrastructure.',
				'solucao_casos_titulo'         => 'Integrations that are faster, safer and smarter',
				'solucao_casos_1_titulo'       => 'Lead-to-Quote',
				'solucao_casos_1_desc'         => 'Automate the process from lead generation to the commercial proposal, connecting Salesforce to the tools responsible for qualification, approval and sales.',
				'solucao_casos_2_titulo'       => 'Order synchronisation',
				'solucao_casos_2_desc'         => 'Keep orders, customers and products in sync between Salesforce and ERPs such as SAP or NetSuite, in real time or on a schedule.',
				'solucao_casos_3_titulo'       => 'Hub for multiple Salesforce orgs',
				'solucao_casos_3_desc'         => 'Centralise integrations from different Salesforce environments in a single architecture, simplifying governance and reducing operational complexity.',
				'solucao_casos_4_titulo'       => 'Audiences for Marketing',
				'solucao_casos_4_desc'         => 'Share segments and audiences automatically between Salesforce and marketing platforms, keeping campaigns up to date.',
				'solucao_casos_5_titulo'       => 'Data Warehouse integration',
				'solucao_casos_5_desc'         => 'Send Salesforce information to analytics platforms such as Snowflake and BigQuery to consolidate indicators and support data-driven decisions.',
				'solucao_dif_titulo'           => 'An architecture built for enterprise environments',
				'solucao_dif_corpo'            => 'Whatever technology your company runs, CLI Connect applies integration best practice to guarantee security, governance and high availability, respecting the particularities of each system.',
				'solucao_dif_topico_1'         => 'Full support for the Salesforce REST APIs',
				'solucao_dif_topico_2'         => 'Automate events with the Subscription API.',
				'solucao_dif_topico_3'         => 'Authenticate integrations with the JWT Bearer Flow.',
				'solucao_plat_eyebrow'         => 'one platform',
				'solucao_plat_titulo'          => 'A single environment to connect the whole ecosystem',
				'solucao_plat_corpo'           => 'Connect Salesforce to the rest of your company systems on a single platform and remove isolated integrations, manual processes and rework as your ecosystem grows.',
				'solucao_plat_topico_1'        => 'Centralise your whole ecosystem',
				'solucao_plat_topico_2'        => 'Remove isolated integrations',
				'solucao_plat_topico_3'        => 'Evolve without adding complexity',
				'solucao_acel_titulo'          => 'A template to start from',
				'solucao_acel_corpo'           => 'Use a ready-made template to synchronise customers, products, orders or opportunities between Salesforce and the ERP.',
				'solucao_acel_topico_1'        => 'Customer records',
				'solucao_acel_topico_2'        => 'Order synchronisation',
				'solucao_acel_topico_3'        => 'Product updates',
				'solucao_acel_topico_4'        => 'And much more...',
			)
		);
	}

	/**
	 * TOTVS Protheus.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_totvs_protheus() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Protheus',
				'solucao_hero_titulo'      => 'Integrate TOTVS Protheus with any system without drawn-out projects',
				'solucao_hero_corpo'       => 'Connect Protheus to CRM, e-commerce, banks and tax platforms using ready-made integrations, reducing customisation, speeding up rollouts and preserving the stability of your ERP.',
				'solucao_pilares_titulo'   => 'Simplify the integration of your Protheus',
				'solucao_pilares_1_titulo' => 'Remove custom integrations',
				'solucao_pilares_1_desc'   => 'Reduce your dependence on AdvPL: connect new systems quickly using standard ExecAuto and Protheus entry points.',
				'solucao_pilares_2_titulo' => 'Reuse ready-made integrations',
				'solucao_pilares_2_desc'   => 'Use accelerators for orders, customers, stock and tax processes to cut rollout time.',
				'solucao_pilares_3_titulo' => 'New integrations in days',
				'solucao_pilares_3_desc'   => 'Connect new systems in days, not months, on an architecture built to expand.',
				'solucao_casos_titulo'     => 'Connect the main Protheus processes',
				'solucao_casos_1_titulo'   => 'Automate e-commerce orders',
				'solucao_casos_1_desc'     => 'Send orders to Protheus automatically, reducing rework and speeding up invoicing.',
				'solucao_casos_2_titulo'   => 'Customer records always up to date',
				'solucao_casos_2_desc'     => 'Synchronise records across CRM, e-commerce and Protheus using REST APIs.',
				'solucao_casos_3_titulo'   => 'Automate tax documents',
				'solucao_casos_3_desc'     => 'Integrate the issuing and lookup of tax documents directly into your financial processes.',
				'solucao_casos_4_titulo'   => 'Control stock across branches',
				'solucao_casos_4_desc'     => 'Update balances between units automatically, avoiding operational discrepancies.',
				'solucao_casos_5_titulo'   => 'Connect Protheus to Salesforce',
				'solucao_casos_5_desc'     => 'Share information between ERP and CRM to remove commercial and operational rework.',
				'solucao_dif_titulo'       => 'Secure connectivity for Protheus environments',
				'solucao_dif_corpo'        => 'Connect Protheus using Runtime and outbound communication, preserving the company infrastructure, with native REST support and ExecAuto calling the standard Protheus MATA routines.',
				'solucao_dif_topico_1'     => 'Avoid opening firewall ports.',
				'solucao_dif_topico_2'     => 'Native REST and ExecAuto calling standard Protheus MATA routines.',
				'solucao_dif_topico_3'     => 'Preserve the security of the internal environment.',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'One platform to integrate your whole ecosystem',
				'solucao_plat_corpo'       => 'Centralise every integration across Protheus, Salesforce, banks and e-commerce on a single platform, reusing proven components and reducing operational complexity.',
				'solucao_plat_topico_1'    => 'Reuse integrations already in production.',
				'solucao_plat_topico_2'    => 'Cut back on new development projects.',
				'solucao_plat_topico_3'    => 'Centralise all integration governance.',
				'solucao_acel_titulo'      => 'Start from ready-made integrations',
				'solucao_acel_corpo'       => 'Roll out recurring scenarios between Protheus and other systems using pre-configured templates, adapted quickly to your environment.',
				'solucao_acel_topico_1'    => 'Roll out order synchronisation quickly.',
				'solucao_acel_topico_2'    => 'Reuse templates for master records.',
				'solucao_acel_topico_3'    => 'Adapt flows to your process.',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * Sankhya.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_sankhya() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Sankhya',
				'solucao_hero_titulo'      => 'Integrate Sankhya with your whole ecosystem without giving up governance',
				'solucao_hero_corpo'       => 'Connect Sankhya to CRM, e-commerce, banks and tax systems through the official API Gateway to automate processes, preserve the ERP business rules and remove parallel integrations.',
				'solucao_pilares_titulo'   => 'Connect Sankhya securely and at scale',
				'solucao_pilares_1_titulo' => 'Use the official API Gateway',
				'solucao_pilares_1_desc'   => 'Integrate Sankhya through the official platform services, preserving business rules and avoiding direct access to the database.',
				'solucao_pilares_2_titulo' => 'Respect the ERP governance',
				'solucao_pilares_2_desc'   => 'Make sure every integration goes through the native Sankhya authorisation layer, keeping control and security over your data.',
				'solucao_pilares_3_titulo' => 'Remove parallel integrations',
				'solucao_pilares_3_desc'   => 'Centralise the connections between ERP, CRM and e-commerce to reduce rework, simplify maintenance and speed up new projects.',
				'solucao_casos_titulo'     => 'Automate the main Sankhya processes',
				'solucao_casos_1_titulo'   => 'Synchronise e-commerce orders',
				'solucao_casos_1_desc'     => 'Send orders to Sankhya automatically through the official API Gateway, reducing rework and speeding up invoicing.',
				'solucao_casos_2_titulo'   => 'Update products and stock',
				'solucao_casos_2_desc'     => 'Make Sankhya products and balances available to your sales channels in real time using the official datasets.',
				'solucao_casos_3_titulo'   => 'Automate financial processes',
				'solucao_casos_3_desc'     => 'Integrate accounts receivable, banks and financial reconciliation using the Sankhya financial entities.',
				'solucao_casos_4_titulo'   => 'Connect CRM and ERP',
				'solucao_casos_4_desc'     => 'Synchronise leads, customers and opportunities between the CRM and Sankhya to remove manual entry and keep information up to date.',
				'solucao_casos_5_titulo'   => 'Make data available to AI',
				'solucao_casos_5_desc'     => 'Expose Sankhya information to Artificial Intelligence agents using governed APIs and MCP servers.',
				'solucao_dif_titulo'       => 'Integrations that respect the Sankhya architecture',
				'solucao_dif_corpo'        => 'Every integration goes through the native Sankhya authorisation layer, using the integration user and explicit permissions, avoiding direct access to the database and preserving the governance of the ERP.',
				'solucao_dif_topico_1'     => 'Use the official API Gateway.',
				'solucao_dif_topico_2'     => 'Respect permissions per entity.',
				'solucao_dif_topico_3'     => 'Avoid direct access to the database.',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Centralise every Sankhya integration',
				'solucao_plat_corpo'       => 'Growing companies tend to accumulate integrations across CRM, e-commerce and sales apps. Use a single platform to centralise governance and reuse integrations without multiplying projects.',
				'solucao_plat_topico_1'    => 'Centralise all governance.',
				'solucao_plat_topico_2'    => 'Reuse the integrations you already have.',
				'solucao_plat_topico_3'    => 'Cut back on point-to-point integrations.',
				'solucao_acel_titulo'      => 'Start with integrations that are ready to go',
				'solucao_acel_corpo'       => 'Use a pre-configured template to synchronise orders and customers across CRM, e-commerce and Sankhya, cutting rollout time and speeding up new projects.',
				'solucao_acel_topico_1'    => 'Roll out orders quickly.',
				'solucao_acel_topico_2'    => 'Reuse proven templates.',
				'solucao_acel_topico_3'    => 'Adapt flows to your business.',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * Senior.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_senior() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Senior',
				'solucao_hero_titulo'      => 'Connect Senior and automate the whole employee journey',
				'solucao_hero_corpo'       => 'Integrate Senior with payroll, time and attendance, benefits, access and identity systems to remove manual processes, protect sensitive data and speed up the whole HR operation.',
				'solucao_pilares_titulo'   => 'Turn HR into a connected process',
				'solucao_pilares_1_titulo' => 'Automate the employee lifecycle',
				'solucao_pilares_1_desc'   => 'Orchestrate hires, moves and leavers between Senior and every system that takes part in the employee journey.',
				'solucao_pilares_2_titulo' => 'Connect Senior securely',
				'solucao_pilares_2_desc'   => 'Integrate through the official Senior web services, preserving business rules and reducing the need for custom development.',
				'solucao_pilares_3_titulo' => 'Protect sensitive data automatically',
				'solucao_pilares_3_desc'   => 'Mask information such as tax IDs, salaries and bank details during the integration, keeping compliance and traceability.',
				'solucao_casos_titulo'     => 'Automate the main HR processes',
				'solucao_casos_1_titulo'   => 'Orchestrate hires and leavers',
				'solucao_casos_1_desc'     => 'Automate the creation and revocation of access, benefits and corporate systems whenever the headcount changes.',
				'solucao_casos_2_titulo'   => 'Integrate payroll and attendance',
				'solucao_casos_2_desc'     => 'Synchronise electronic time records with payroll to reduce inconsistencies and operational rework.',
				'solucao_casos_3_titulo'   => 'Automate benefits management',
				'solucao_casos_3_desc'     => 'Integrate meal voucher, food allowance, health plan and other benefit providers directly into Senior.',
				'solucao_casos_4_titulo'   => 'Centralise HR indicators',
				'solucao_casos_4_desc'     => 'Consolidate headcount, hire, leaver and move data to feed BI platforms in real time.',
				'solucao_casos_5_titulo'   => 'Revoke access automatically',
				'solucao_casos_5_desc'     => 'Make sure physical and digital access is removed automatically when an employee leaves.',
				'solucao_dif_titulo'       => 'Security for critical HR data',
				'solucao_dif_corpo'        => 'The platform identifies and masks sensitive Senior information automatically before it travels between systems, keeping auditing and compliance throughout the integration.',
				'solucao_dif_topico_1'     => 'Mask tax IDs and salaries automatically.',
				'solucao_dif_topico_2'     => 'Protect bank details in transit.',
				'solucao_dif_topico_3'     => 'Audit every integration that runs.',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Connect the whole HR ecosystem',
				'solucao_plat_corpo'       => 'Remove isolated integrations between Senior, Active Directory, benefits, time and attendance, LMS and other systems using a single integration platform with centralised management.',
				'solucao_plat_topico_1'    => 'Centralise HR integrations.',
				'solucao_plat_topico_2'    => 'Reduce your dependence on the IT team.',
				'solucao_plat_topico_3'    => 'Adjust flows more quickly.',
				'solucao_acel_titulo'      => 'Start with ready-made HR flows',
				'solucao_acel_corpo'       => 'Use a ready-made template to automate hires, moves and leavers, cutting rollout time and speeding up new projects.',
				'solucao_acel_topico_1'    => 'Roll out JML flows quickly.',
				'solucao_acel_topico_2'    => 'Reuse templates that are already proven.',
				'solucao_acel_topico_3'    => 'Adapt processes to your HR.',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * Dynamics 365.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_dynamics_365() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Microsoft Dynamics',
				'solucao_hero_titulo'      => 'Integrate Microsoft Dynamics 365 without being limited to the Power Platform',
				'solucao_hero_corpo'       => 'Connect Dynamics 365, Business Central and Finance & Operations to the rest of your operation on a single platform to automate processes, share data and remove isolated integrations.',
				'solucao_pilares_titulo'   => 'Connect the whole Microsoft ecosystem',
				'solucao_pilares_1_titulo' => 'Use the native Dynamics APIs',
				'solucao_pilares_1_desc'   => 'Integrate through OData and the Dynamics 365 Web API to preserve the Microsoft architecture and speed up new projects.',
				'solucao_pilares_2_titulo' => 'Connect different systems',
				'solucao_pilares_2_desc'   => 'Orchestrate Dynamics, SAP, Salesforce, TOTVS and other enterprise applications on a single integration platform.',
				'solucao_pilares_3_titulo' => 'Reduce your dependence on the Power Platform',
				'solucao_pilares_3_desc'   => 'Use a central integration layer for complex enterprise scenarios, keeping flexibility and scalability.',
				'solucao_casos_titulo'     => 'Automate the main Dynamics processes',
				'solucao_casos_1_titulo'   => 'Synchronise CRM and ERP',
				'solucao_casos_1_desc'     => 'Share opportunities, accounts and customers between Dynamics CRM and corporate ERPs automatically.',
				'solucao_casos_2_titulo'   => 'Automate financial processes',
				'solucao_casos_2_desc'     => 'Integrate Dynamics 365 Finance & Operations with banks and financial reconciliation platforms.',
				'solucao_casos_3_titulo'   => 'Connect Business Central to e-commerce',
				'solucao_casos_3_desc'     => 'Synchronise orders, customers and stock between Business Central and your sales channels.',
				'solucao_casos_4_titulo'   => 'Centralise master data',
				'solucao_casos_4_desc'     => 'Keep customers, products and records in sync between Dynamics and other corporate systems.',
				'solucao_casos_5_titulo'   => 'Make data available to Artificial Intelligence',
				'solucao_casos_5_desc'     => 'Expose Dynamics information to AI agents using governed APIs and MCP servers.',
				'solucao_dif_titulo'       => 'Enterprise security built into the Microsoft ecosystem',
				'solucao_dif_corpo'        => 'Integrations authenticate through Azure AD (Microsoft Entra ID) with OAuth2 and support for multi-tenant environments, preserving the security standards of Dynamics 365.',
				'solucao_dif_topico_1'     => 'Authenticate through Azure AD.',
				'solucao_dif_topico_2'     => 'Support multi-tenant Dynamics environments.',
				'solucao_dif_topico_3'     => 'Protect integrations with OAuth2.',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'One platform for your whole Microsoft environment',
				'solucao_plat_corpo'       => 'Companies running Dynamics often live alongside other ERPs and CRMs. Centralise every integration on a single platform to simplify projects, acquisitions and multi-ERP operations.',
				'solucao_plat_topico_1'    => 'Connect Dynamics and other ERPs.',
				'solucao_plat_topico_2'    => 'Remove integration silos.',
				'solucao_plat_topico_3'    => 'Simplify M&A scenarios.',
				'solucao_acel_titulo'      => 'Start from ready-made integrations',
				'solucao_acel_corpo'       => 'Quickly roll out a pre-configured template to synchronise accounts, opportunities and orders between Dynamics 365 and external systems using OData and the Web API.',
				'solucao_acel_topico_1'    => 'Roll out integrations quickly.',
				'solucao_acel_topico_2'    => 'Reuse templates that are already proven.',
				'solucao_acel_topico_3'    => 'Adapt flows to your business.',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}


	/* --- Tecnologia ------------------------------------------------------ */
	/**
	 * Salesforce Sales Cloud.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_salesforce_sales_cloud() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Salesforce Sales Cloud',
				'solucao_hero_titulo'      => 'Connect Salesforce Sales Cloud to the ERP and speed up the whole sales cycle',
				'solucao_hero_corpo'       => 'Automate the journey from lead to invoice by integrating Sales Cloud with the ERP, CPQ, finance and the rest of the company systems, removing rework and keeping data in sync at every stage of the sale.',
				'solucao_pilares_titulo'   => 'Connect the whole commercial journey',
				'solucao_pilares_1_titulo' => 'Automate from lead to invoice',
				'solucao_pilares_1_desc'   => 'Connect marketing, CRM, CPQ, ERP and finance to turn opportunities into orders with no manual steps.',
				'solucao_pilares_2_titulo' => 'Keep sales and ERP in sync',
				'solucao_pilares_2_desc'   => 'Keep opportunities, accounts, customers and orders up to date between Salesforce and the ERP, in both directions.',
				'solucao_pilares_3_titulo' => 'Trigger processes in real time',
				'solucao_pilares_3_desc'   => 'Fire approvals, notifications and automations the moment an important record changes in Salesforce.',
				'solucao_casos_titulo'     => 'Automate the main Sales Cloud processes',
				'solucao_casos_1_titulo'   => 'Automate the lead-to-quote process',
				'solucao_casos_1_desc'     => 'Connect marketing, Sales Cloud and CPQ to produce proposals faster and cut manual steps.',
				'solucao_casos_2_titulo'   => 'Sync orders with the ERP',
				'solucao_casos_2_desc'     => 'Update ERP orders in Sales Cloud automatically, on a schedule or in real time.',
				'solucao_casos_3_titulo'   => 'Generate orders automatically',
				'solucao_casos_3_desc'     => 'Turn won opportunities into ERP orders with no re-keying and no operational step.',
				'solucao_casos_4_titulo'   => 'Connect several Salesforce orgs',
				'solucao_casos_4_desc'     => 'Centralise data across different Salesforce instances while keeping commercial information in sync.',
				'solucao_casos_5_titulo'   => 'Get alerts on opportunities',
				'solucao_casos_5_desc'     => 'Fire notifications and processes whenever an opportunity changes stage during a negotiation.',
				'solucao_dif_titulo'       => 'Integrations ready for Salesforce in production',
				'solucao_dif_corpo'        => 'Use every major REST API operation, real-time events and secure authentication to build scalable integrations without compromising the Salesforce architecture.',
				'solucao_dif_topico_1'     => 'Use the official Salesforce APIs.',
				'solucao_dif_topico_2'     => 'Automate events in real time.',
				'solucao_dif_topico_3'     => 'Protect connections with the JWT Bearer Flow.',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Bring the whole commercial journey onto one platform',
				'solucao_plat_corpo'       => 'Sales Cloud depends on the ERP, CPQ and billing to close a sale. Centralise all those integrations on a single platform to cut costs, simplify the architecture and ship new automations faster.',
				'solucao_plat_topico_1'    => 'Centralise the integrations of the sales cycle.',
				'solucao_plat_topico_2'    => 'Reuse flows across different projects.',
				'solucao_plat_topico_3'    => 'Depend less on one-off development.',
				'solucao_acel_titulo'      => 'Start with automations already structured',
				'solucao_acel_corpo'       => 'Use a ready-made template to automate the whole journey between Sales Cloud, CPQ and the ERP, shortening the rollout and delivering value sooner.',
				'solucao_acel_topico_1'    => 'Roll out integrations in days.',
				'solucao_acel_topico_2'    => 'Reuse templates already proven.',
				'solucao_acel_topico_3'    => 'Shape flows around your sales process.',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * Salesforce Service Cloud.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_salesforce_service_cloud() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Salesforce Service Cloud',
				'solucao_hero_titulo'      => 'Connect Salesforce Service Cloud and give agents the full context',
				'solucao_hero_corpo'       => 'Integrate Service Cloud with the ERP, billing, field service and support channels so your team resolves cases faster, without hopping between systems.',
				'solucao_pilares_titulo'   => 'Connect the whole support operation',
				'solucao_pilares_1_titulo' => 'Enrich every interaction',
				'solucao_pilares_1_desc'   => 'Bring order, billing and customer history data straight into the case, with no lookups in other systems.',
				'solucao_pilares_2_titulo' => 'Orchestrate every channel',
				'solucao_pilares_2_desc'   => 'Connect telephony, WhatsApp, chat and the other channels to Service Cloud to centralise the whole support journey.',
				'solucao_pilares_3_titulo' => 'Automate SLAs and escalations',
				'solucao_pilares_3_desc'   => 'Trigger rules, notifications and handovers automatically from support events and enterprise integrations.',
				'solucao_casos_titulo'     => 'Automate the main support processes',
				'solucao_casos_1_titulo'   => 'Enrich cases with ERP data',
				'solucao_casos_1_desc'     => 'Show billing, order and contract information in real time while the agent is with the customer.',
				'solucao_casos_2_titulo'   => 'Integrate field teams',
				'solucao_casos_2_desc'     => 'Connect work orders and field service systems to follow the job through to completion.',
				'solucao_casos_3_titulo'   => 'Automate refunds',
				'solucao_casos_3_desc'     => 'Trigger chargeback and refund processes automatically once a case is resolved.',
				'solucao_casos_4_titulo'   => 'Sync the knowledge base',
				'solucao_casos_4_desc'     => 'Keep content aligned between Service Cloud and self-service portals with no manual steps.',
				'solucao_casos_5_titulo'   => 'Get proactive SLA alerts',
				'solucao_casos_5_desc'     => 'Fire notifications and escalations in real time whenever an SLA is at risk.',
				'solucao_dif_titulo'       => 'Support data that is current across every system',
				'solucao_dif_corpo'        => 'Use real-time events through the Subscription API to keep Service Cloud in sync with the ERP, billing and other applications, so decisions rest on current information.',
				'solucao_dif_topico_1'     => 'Update data in real time',
				'solucao_dif_topico_2'     => 'Avoid stale information during support',
				'solucao_dif_topico_3'     => 'Connect events across every system',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'One platform to centralise the whole support operation',
				'solucao_plat_corpo'       => 'Much of the time spent on a case goes into looking things up elsewhere. Centralise those integrations to deliver the full context inside Service Cloud and resolve cases faster.',
				'solucao_plat_topico_1'    => 'Centralise customer data',
				'solucao_plat_topico_2'    => 'Cut the switching between systems',
				'solucao_plat_topico_3'    => 'Shorten the time to resolution',
				'solucao_acel_titulo'      => 'Start with support integrations ready',
				'solucao_acel_corpo'       => 'Use a pre-configured template to query billing, orders and ERP information straight from Service Cloud and shorten the rollout.',
				'solucao_acel_topico_1'    => 'Roll out lookups quickly',
				'solucao_acel_topico_2'    => 'Reuse proven templates',
				'solucao_acel_topico_3'    => 'Shape flows around your business',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * Salesforce Marketing Cloud.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_salesforce_marketing_cloud() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Salesforce Marketing Cloud',
				'solucao_hero_titulo'      => 'Feed your marketing journeys with live sales and product data',
				'solucao_hero_corpo'       => 'Connect Salesforce Marketing Cloud to the CRM, e-commerce and data warehouse to build campaigns on real behaviour, with data that is current at every customer interaction.',
				'solucao_pilares_titulo'   => 'Turn data into journeys that land',
				'solucao_pilares_1_titulo' => 'Trigger journeys from real events',
				'solucao_pilares_1_desc'   => 'Start campaigns automatically from purchases, product usage and support interactions.',
				'solucao_pilares_2_titulo' => 'Sync audiences in real time',
				'solucao_pilares_2_desc'   => 'Keep lists and segments continuously up to date across Marketing Cloud, CRM and ERP.',
				'solucao_pilares_3_titulo' => 'Personalise with the full picture',
				'solucao_pilares_3_desc'   => 'Enrich contact profiles with sales, product and behavioural information.',
				'solucao_casos_titulo'     => 'Automate journeys driven by data',
				'solucao_casos_1_titulo'   => 'Trigger journeys automatically',
				'solucao_casos_1_desc'     => 'Fire Journey Builder from e-commerce and ERP events in real time.',
				'solucao_casos_2_titulo'   => 'Sync commercial audiences',
				'solucao_casos_2_desc'     => 'Keep segments connected across Marketing Cloud, Sales Cloud and Service Cloud.',
				'solucao_casos_3_titulo'   => 'Enrich customer profiles',
				'solucao_casos_3_desc'     => 'Add product usage data to build more personal experiences.',
				'solucao_casos_4_titulo'   => 'Close the loop on campaign attribution',
				'solucao_casos_4_desc'     => 'Connect marketing, CRM and ERP to follow the impact through to revenue.',
				'solucao_casos_5_titulo'   => 'Apply opt-outs automatically',
				'solucao_casos_5_desc'     => 'Propagate contact preferences across every connected channel.',
				'solucao_dif_titulo'       => 'Secure integrations for marketing data',
				'solucao_dif_corpo'        => 'Use the Marketing Cloud REST and SOAP APIs with consent controls, so preferences follow the contact across every connected system.',
				'solucao_dif_topico_1'     => 'Use the official REST and SOAP APIs',
				'solucao_dif_topico_2'     => 'Propagate consent across systems',
				'solucao_dif_topico_3'     => 'Control opt-outs on every channel',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Centralise journeys on connected data',
				'solucao_plat_corpo'       => 'Connect Marketing Cloud to sales, product and support on a single platform to build journeys on real behaviour rather than on stale lists.',
				'solucao_plat_topico_1'    => 'Connect real business events',
				'solucao_plat_topico_2'    => 'Remove manual list exports',
				'solucao_plat_topico_3'    => 'Unify data across commercial teams',
				'solucao_acel_titulo'      => 'Start with journeys already structured',
				'solucao_acel_corpo'       => 'Use a ready-made template to trigger Journey Builder from ERP and e-commerce events and speed up your marketing operation.',
				'solucao_acel_topico_1'    => 'Set up events quickly',
				'solucao_acel_topico_2'    => 'Reuse proven flows',
				'solucao_acel_topico_3'    => 'Shape journeys around the business',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * TOTVS Datasul.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_totvs_datasul() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Datasul',
				'solucao_hero_titulo'      => 'Connect TOTVS Datasul without interrupting your industrial operation',
				'solucao_hero_corpo'       => 'Integrate Datasul with MES, CRM, B2B portals and BI platforms on a single platform. Share information across plants, automate processes and modernise the operation without touching the ERP core.',
				'solucao_pilares_titulo'   => 'Connect the whole industrial operation',
				'solucao_pilares_1_titulo' => 'Integrate manufacturing faster',
				'solucao_pilares_1_desc'   => 'Connect Datasul to production, sales and logistics systems without long projects or complex development.',
				'solucao_pilares_2_titulo' => 'Standardise information across plants',
				'solucao_pilares_2_desc'   => 'Centralise production and stock data so decisions across the operation are faster and more reliable.',
				'solucao_pilares_3_titulo' => 'Depend less on specialists',
				'solucao_pilares_3_desc'   => 'Simplify new integrations without depending continuously on teams that know Progress 4GL.',
				'solucao_casos_titulo'     => 'Automate the processes that move your plant',
				'solucao_casos_1_titulo'   => 'Sync production orders',
				'solucao_casos_1_desc'     => 'Connect the MES to Datasul to update orders automatically throughout the industrial operation.',
				'solucao_casos_2_titulo'   => 'Consolidate stock across plants',
				'solucao_casos_2_desc'     => 'Share stock balances between units to give the operation more visibility.',
				'solucao_casos_3_titulo'   => 'Automate B2B orders',
				'solucao_casos_3_desc'     => 'Integrate customer portals directly with Datasul to cut rework and process orders faster.',
				'solucao_casos_4_titulo'   => 'Centralise the financial close',
				'solucao_casos_4_desc'     => 'Consolidate information across units to simplify the corporate close.',
				'solucao_casos_5_titulo'   => 'Make data available to AI',
				'solucao_casos_5_desc'     => 'Let AI agents query Datasul information safely, through governed integrations.',
				'solucao_dif_titulo'       => 'Connectivity built for industrial environments',
				'solucao_dif_corpo'        => 'Connect Datasul through the Progress/EMS protocol, with processing running inside the company infrastructure to preserve security and performance.',
				'solucao_dif_topico_1'     => 'Use native Progress/EMS connectivity.',
				'solucao_dif_topico_2'     => 'Keep the database protected internally.',
				'solucao_dif_topico_3'     => 'Deploy inside your own infrastructure.',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Integrate different ERPs on the same platform',
				'solucao_plat_corpo'       => 'Companies that grew through acquisitions often run more than one ERP. Centralise Datasul, SAP, Protheus and other systems in a single integration layer.',
				'solucao_plat_topico_1'    => 'Reuse the integrations already in place.',
				'solucao_plat_topico_2'    => 'Reduce new development projects.',
				'solucao_plat_topico_3'    => 'Centralise the governance of every integration.',
				'solucao_acel_titulo'      => 'Roll out integrations in less time',
				'solucao_acel_corpo'       => 'Use ready-made templates to synchronise production orders and B2B orders, cutting the implementation effort and speeding up new projects.',
				'solucao_acel_topico_1'    => 'Build on templates for production orders.',
				'solucao_acel_topico_2'    => 'Reuse flows for B2B orders.',
				'solucao_acel_topico_3'    => 'Adapt quickly to your environment.',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * TOTVS Winthor.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_totvs_winthor() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Winthor',
				'solucao_hero_titulo'      => 'Integrate TOTVS Winthor and speed up your whole commercial operation',
				'solucao_hero_corpo'       => 'Connect Winthor to field sales, B2B e-commerce, carriers and banks to automate processes, cut rework and keep orders, prices and deliveries in sync.',
				'solucao_pilares_titulo'   => 'Connect your distribution operation',
				'solucao_pilares_1_titulo' => 'Update prices automatically',
				'solucao_pilares_1_desc'   => 'Synchronise price lists and promotions in real time for the whole sales team, cutting inconsistencies and speeding up negotiations.',
				'solucao_pilares_2_titulo' => 'Automate sales orders',
				'solucao_pilares_2_desc'   => 'Integrate pre-sales apps and digital channels with Winthor to remove manual keying and invoice faster.',
				'solucao_pilares_3_titulo' => 'Add integrations easily',
				'solucao_pilares_3_desc'   => 'Connect new systems on an architecture built to grow alongside your operation.',
				'solucao_casos_titulo'     => 'Automate the main Winthor processes',
				'solucao_casos_1_titulo'   => 'Sync field sales orders',
				'solucao_casos_1_desc'     => 'Send orders from the commercial apps to Winthor automatically, with no rework.',
				'solucao_casos_2_titulo'   => 'Update prices in real time',
				'solucao_casos_2_desc'     => 'Push price and discount changes out to reps and digital channels immediately.',
				'solucao_casos_3_titulo'   => 'Integrate carriers',
				'solucao_casos_3_desc'     => 'Automate label sending, tracking and delivery status updates.',
				'solucao_casos_4_titulo'   => 'Reconcile receipts automatically',
				'solucao_casos_4_desc'     => 'Connect banks and acquirers to simplify financial reconciliation.',
				'solucao_casos_5_titulo'   => 'Consolidate sales across branches',
				'solucao_casos_5_desc'     => 'Bring the commercial indicators of every unit into a single view.',
				'solucao_dif_titulo'       => 'Connectivity built for high-volume operations',
				'solucao_dif_corpo'        => 'The platform uses connectors dedicated to the Winthor automatic routines and web services, supporting the large order volumes typical of distributors and wholesalers.',
				'solucao_dif_topico_1'     => 'Process large order volumes.',
				'solucao_dif_topico_2'     => 'Use the native Winthor connectors.',
				'solucao_dif_topico_3'     => 'Keep the operation stable.',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Centralise every distribution integration',
				'solucao_plat_corpo'       => 'Connect Winthor, commercial apps, carriers and banks on a single platform, reusing integrations and cutting the number of new projects.',
				'solucao_plat_topico_1'    => 'Reuse the integrations you have.',
				'solucao_plat_topico_2'    => 'Centralise governance.',
				'solucao_plat_topico_3'    => 'Reduce new development.',
				'solucao_acel_titulo'      => 'Start from integrations that are ready',
				'solucao_acel_corpo'       => 'Roll out the main integration scenarios quickly.',
				'solucao_acel_topico_1'    => 'Roll out order integration quickly.',
				'solucao_acel_topico_2'    => 'Reuse templates for price lists.',
				'solucao_acel_topico_3'    => 'Shape flows around your process.',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * TOTVS Logix.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_totvs_logix() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Logix',
				'solucao_hero_titulo'      => 'Connect TOTVS Logix and keep your stock in sync across every channel',
				'solucao_hero_corpo'       => 'Integrate Logix with ERPs, marketplaces and carriers to automate the logistics operation, remove stock discrepancies and fulfil orders faster, with no manual steps.',
				'solucao_pilares_titulo'   => 'Keep your logistics connected in real time',
				'solucao_pilares_1_titulo' => 'Synchronise stock automatically',
				'solucao_pilares_1_desc'   => 'Update balances across Logix, marketplaces and sales channels in real time to avoid discrepancies and improve product availability.',
				'solucao_pilares_2_titulo' => 'Automate the whole dispatch process',
				'solucao_pilares_2_desc'   => 'Orchestrate picking, packing and dispatch from the orders you receive, cutting rework and raising warehouse productivity.',
				'solucao_pilares_3_titulo' => 'Avoid losses from overselling',
				'solucao_pilares_3_desc'   => 'Share stock information across every channel so you only sell what is genuinely available.',
				'solucao_casos_titulo'     => 'Automate the whole logistics operation',
				'solucao_casos_1_titulo'   => 'Sync stock with marketplaces',
				'solucao_casos_1_desc'     => 'Update stock automatically between Logix, Amazon, Mercado Livre, Magalu and other sales channels.',
				'solucao_casos_2_titulo'   => 'Send orders to the right centre',
				'solucao_casos_2_desc'     => 'Route each order automatically to the most suitable distribution centre, following your operating rules.',
				'solucao_casos_3_titulo'   => 'Integrate carriers',
				'solucao_casos_3_desc'     => 'Automate label printing, tracking and delivery status updates.',
				'solucao_casos_4_titulo'   => 'Update the ERP in real time',
				'solucao_casos_4_desc'     => 'Synchronise picking, dispatch and invoicing automatically between Logix and the ERP.',
				'solucao_casos_5_titulo'   => 'Automate returns',
				'solucao_casos_5_desc'     => 'Handle returns and stock re-entry with no manual steps and no operational rework.',
				'solucao_dif_titulo'       => 'Scalability for high-volume logistics operations',
				'solucao_dif_corpo'        => 'The platform handles large transaction volumes with automatic scaling, staying stable through Black Friday and other seasonal peaks.',
				'solucao_dif_topico_1'     => 'Handle operational peaks with stability.',
				'solucao_dif_topico_2'     => 'Scale pipelines automatically.',
				'solucao_dif_topico_3'     => 'Keep the operation available under heavy demand.',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Centralise every logistics integration',
				'solucao_plat_corpo'       => 'Connect Logix, marketplaces, carriers and ERPs on a single platform to keep stock in sync in real time and remove the isolated integrations that cause delays and overselling.',
				'solucao_plat_topico_1'    => 'Centralise every integration.',
				'solucao_plat_topico_2'    => 'Synchronise stock in real time.',
				'solucao_plat_topico_3'    => 'Cut down on isolated integration projects.',
				'solucao_acel_titulo'      => 'Start from integrations that are ready',
				'solucao_acel_corpo'       => 'Roll out synchronisation scenarios between Logix and marketplaces quickly, using pre-configured templates that shorten the implementation.',
				'solucao_acel_topico_1'    => 'Roll out integrations faster.',
				'solucao_acel_topico_2'    => 'Reuse templates already proven.',
				'solucao_acel_topico_3'    => 'Shape flows around your operation.',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * RD Station CRM.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_rd_station_crm() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your RD Station',
				'solucao_hero_titulo'      => 'Connect RD Station CRM to the ERP and to marketing without leaning on spreadsheets',
				'solucao_hero_corpo'       => 'Automate the link between sales, ERP, e-commerce and marketing tools to keep data in sync, remove manual rework and follow the growth of your commercial operation.',
				'solucao_pilares_titulo'   => 'Scale your commercial operation, connected',
				'solucao_pilares_1_titulo' => 'Sync sales with the ERP',
				'solucao_pilares_1_desc'   => 'Send won deals to the ERP automatically and remove the manual keying of orders, invoices and customer records.',
				'solucao_pilares_2_titulo' => 'Enrich leads automatically',
				'solucao_pilares_2_desc'   => 'Top up RD Station lead data with information from the ERP and other sources to qualify each opportunity better.',
				'solucao_pilares_3_titulo' => 'Connect systems as you grow',
				'solucao_pilares_3_desc'   => 'Integrate RD Station CRM with new tools as the operation grows, without rewriting the integrations you already have.',
				'solucao_casos_titulo'     => 'Automate the processes of the sales cycle',
				'solucao_casos_1_titulo'   => 'Send won deals to the ERP',
				'solucao_casos_1_desc'     => 'When a deal closes in RD Station CRM, trigger the order or contract in the ERP automatically, with no manual step.',
				'solucao_casos_2_titulo'   => 'Enrich lead data',
				'solucao_casos_2_desc'     => 'Sync customer information between the CRM and the ERP to keep commercial and financial history in one record.',
				'solucao_casos_3_titulo'   => 'Connect purchase history',
				'solucao_casos_3_desc'     => 'Surface ERP order and invoice data inside RD Station CRM so reps can follow the history of each account.',
				'solucao_casos_4_titulo'   => 'Consolidate data for BI',
				'solucao_casos_4_desc'     => 'Bring CRM sales metrics together with ERP financial data in business intelligence dashboards.',
				'solucao_casos_5_titulo'   => 'Distribute leads automatically',
				'solucao_casos_5_desc'     => 'Route qualified leads to reps based on territory, segment or available capacity.',
				'solucao_dif_titulo'       => 'Secure integrations on the official API',
				'solucao_dif_corpo'        => 'The integrations use the official RD Station CRM REST API with OAuth2 authentication and one token per integration, giving traceability and fine-grained access control.',
				'solucao_dif_topico_1'     => 'Use the official RD Station REST API.',
				'solucao_dif_topico_2'     => 'Control access through permissions.',
				'solucao_dif_topico_3'     => 'Protect connections with individual tokens.',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Bring your commercial connections onto a single platform',
				'solucao_plat_corpo'       => 'Growing companies pick up new tools along the way. Centralise every RD Station CRM integration on one platform to simplify management and scale with no rework.',
				'solucao_plat_topico_1'    => 'Connect CRM and ERP at scale.',
				'solucao_plat_topico_2'    => 'Cut manual work with automation.',
				'solucao_plat_topico_3'    => 'Evolve your systems without switching tools.',
				'solucao_acel_titulo'      => 'Start from an integration that is ready',
				'solucao_acel_corpo'       => 'Roll out proven commercial flows to sync won deals, customer data and purchase history between RD Station CRM and your ERP.',
				'solucao_acel_topico_1'    => 'Roll out proven commercial flows.',
				'solucao_acel_topico_2'    => 'Synchronise sales automatically.',
				'solucao_acel_topico_3'    => 'Shape the rules around your process.',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * RD Station Marketing.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_rd_station_marketing() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your RD Station Marketing',
				'solucao_hero_titulo'      => 'Automate your marketing with real-time sales and product data',
				'solucao_hero_corpo'       => 'Connect RD Station Marketing to the CRM, ERP and analytics tools to turn leads into opportunities with up-to-date data at every stage of the funnel.',
				'solucao_pilares_titulo'   => 'Connect marketing to the sales cycle',
				'solucao_pilares_1_titulo' => 'Sync qualified leads',
				'solucao_pilares_1_desc'   => 'Send MQLs and SQLs to the sales CRM automatically, with no delays and no manual steps.',
				'solucao_pilares_2_titulo' => 'Capture conversion events',
				'solucao_pilares_2_desc'   => 'Use webhooks and the REST API to react quickly to the interactions that matter.',
				'solucao_pilares_3_titulo' => 'Unify the funnel data',
				'solucao_pilares_3_desc'   => 'Connect marketing, sales and revenue to follow the whole journey through to closing.',
				'solucao_casos_titulo'     => 'Automate your marketing and sales processes',
				'solucao_casos_1_titulo'   => 'Send MQLs to the CRM',
				'solucao_casos_1_desc'     => 'Sync qualified leads from RD Station Marketing with the CRM in real time.',
				'solucao_casos_2_titulo'   => 'Trigger automations from events',
				'solucao_casos_2_desc'     => 'Start marketing flows from product or sales actions.',
				'solucao_casos_3_titulo'   => 'Measure campaign attribution',
				'solucao_casos_3_desc'     => 'Connect campaigns to the CRM and ERP to follow the impact through to revenue.',
				'solucao_casos_4_titulo'   => 'Enrich lead data',
				'solucao_casos_4_desc'     => 'Combine external information to build fuller commercial profiles.',
				'solucao_casos_5_titulo'   => 'Remove converted customers',
				'solucao_casos_5_desc'     => 'Take contacts who have already bought out of the nurture flows automatically.',
				'solucao_dif_titulo'       => 'Reliable integrations through the official API',
				'solucao_dif_corpo'        => 'Connect RD Station Marketing using webhooks and the REST API, with contact deduplication keeping marketing and sales aligned.',
				'solucao_dif_topico_1'     => 'Use webhooks for fast events',
				'solucao_dif_topico_2'     => 'Connect through the official REST API',
				'solucao_dif_topico_3'     => 'Avoid duplicate contacts',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Connect the whole sales funnel on one platform',
				'solucao_plat_corpo'       => 'Marketing and sales lose ground when they work from disconnected data. Centralise the integrations to follow the customer from the first click to the invoiced order.',
				'solucao_plat_topico_1'    => 'Unify marketing and sales data',
				'solucao_plat_topico_2'    => 'Remove manual spreadsheet matching',
				'solucao_plat_topico_3'    => 'Connect the whole commercial journey',
				'solucao_acel_titulo'      => 'Start with leads already structured',
				'solucao_acel_corpo'       => 'Use a ready-made template to sync qualified leads from RD Station Marketing with any CRM and speed up the handover from marketing to sales.',
				'solucao_acel_topico_1'    => 'Connect MQLs automatically',
				'solucao_acel_topico_2'    => 'Reuse flows already proven',
				'solucao_acel_topico_3'    => 'Shape the rules around your process',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * Thomson Reuters Tax One.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_thomson_reuters_tax_one() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Tax One',
				'solucao_hero_titulo'      => 'Centralise tax management and remove tax risk from your company',
				'solucao_hero_corpo'       => 'Connect your ecosystem to Thomson Reuters Tax One with ease. Bring tax calculation together, simplify statutory filings and keep the operation compliant.',
				'solucao_pilares_titulo'   => 'Centralise the tax calculation of your operation',
				'solucao_pilares_1_titulo' => 'Centralise the tax rules',
				'solucao_pilares_1_desc'   => 'Apply the same tax engine across every system that issues documents in the company.',
				'solucao_pilares_2_titulo' => 'Cut tax discrepancies',
				'solucao_pilares_2_desc'   => 'Keep ERP, e-commerce and billing aligned on consistent tax calculations.',
				'solucao_pilares_3_titulo' => 'Audit every calculation',
				'solucao_pilares_3_desc'   => 'Get full traceability of every call made to the tax engine.',
				'solucao_casos_titulo'     => 'Automate the main tax processes',
				'solucao_casos_1_titulo'   => 'Calculate tax at checkout',
				'solucao_casos_1_desc'     => 'Query the tax engine in real time during e-commerce purchases to apply the right taxes.',
				'solucao_casos_2_titulo'   => 'Connect several ERPs',
				'solucao_casos_2_desc'     => 'Centralise tax calculation across SAP, Totvs, Dynamics and the other ERPs in the group.',
				'solucao_casos_3_titulo'   => 'Reprocess tax documents',
				'solucao_casos_3_desc'     => 'Run batch calculations to reconcile documents and correct tax inconsistencies.',
				'solucao_casos_4_titulo'   => 'Update tax rules automatically',
				'solucao_casos_4_desc'     => 'Synchronise tax changes between the tax engine and the source systems.',
				'solucao_casos_5_titulo'   => 'Centralise tax audits',
				'solucao_casos_5_desc'     => 'Follow every query to the tax engine in a single audit trail.',
				'solucao_dif_titulo'       => 'Audit every calculation with tax safety',
				'solucao_dif_corpo'        => 'Centralise every call to the tax engine, with access control by source system and full traceability for tax compliance.',
				'solucao_dif_topico_1'     => 'Record every tax call',
				'solucao_dif_topico_2'     => 'Control access by source system',
				'solucao_dif_topico_3'     => 'Guarantee traceability for audits',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Bring tax calculation onto one platform',
				'solucao_plat_corpo'       => 'Companies running several ERPs need the same tax rule at every point of issue. Centralise the connections and reduce the risk of inconsistent calculations.',
				'solucao_plat_topico_1'    => 'Centralise rules across different ERPs',
				'solucao_plat_topico_2'    => 'Standardise calculation across units',
				'solucao_plat_topico_3'    => 'Reduce the risk of tax assessments',
				'solucao_acel_titulo'      => 'Start from a ready-made tax template',
				'solucao_acel_corpo'       => 'Use a centralised tax calculation template to connect checkout, ERP and the tax engine faster.',
				'solucao_acel_topico_1'    => 'Connect several ERPs quickly',
				'solucao_acel_topico_2'    => 'Reuse proven tax flows',
				'solucao_acel_topico_3'    => 'Speed up new tax integrations',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * Freshservice.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_freshservice() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Freshservice',
				'solucao_hero_titulo'      => 'Get more out of Freshservice by building complete processes with no new modules',
				'solucao_hero_corpo'       => 'Connect Freshservice to the internal systems to build forms, approvals and service catalogues that write straight into the ERP, CRM or databases, cutting licensing costs.',
				'solucao_pilares_titulo'   => 'Turn Freshservice into a process platform',
				'solucao_pilares_1_titulo' => 'Build processes inside Freshservice',
				'solucao_pilares_1_desc'   => 'Create forms, approvals and catalogues wired into the internal company systems.',
				'solucao_pilares_2_titulo' => 'Buy fewer extra licences',
				'solucao_pilares_2_desc'   => 'Avoid additional modules to connect Freshservice processes to other enterprise applications.',
				'solucao_pilares_3_titulo' => 'Reuse the workflows you build',
				'solucao_pilares_3_desc'   => 'Turn every process you develop into a reusable flow for the next requirement.',
				'solucao_casos_titulo'     => 'Automate critical processes through Freshservice',
				'solucao_casos_1_titulo'   => 'Raise purchase requests in Freshservice',
				'solucao_casos_1_desc'     => 'Build purchase forms that record orders straight in the ERP, with no extra modules.',
				'solucao_casos_2_titulo'   => 'Automate internal access',
				'solucao_casos_2_desc'     => 'Connect the service catalogue to Active Directory or Okta to provision access automatically.',
				'solucao_casos_3_titulo'   => 'Automate employee onboarding',
				'solucao_casos_3_desc'     => 'Trigger simultaneous onboarding in payroll, email and internal systems from Freshservice.',
				'solucao_casos_4_titulo'   => 'Open tickets automatically',
				'solucao_casos_4_desc'     => 'Turn monitoring, HR and security events into service desk requests.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make Freshservice tickets and processes available to intelligent support agents.',
				'solucao_dif_titulo'       => 'Secure integrations for critical processes',
				'solucao_dif_corpo'        => 'Use the Freshservice REST APIs with secure authentication and access control to connect processes in line with internal policy.',
				'solucao_dif_topico_1'     => 'Use the official REST APIs',
				'solucao_dif_topico_2'     => 'Control access by department',
				'solucao_dif_topico_3'     => 'Protect connections with an API key',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Centralise processes without depending on modules',
				'solucao_plat_corpo'       => 'Let Freshservice be the interface for your processes while the integration platform connects and writes data into the internal systems.',
				'solucao_plat_topico_1'    => 'Centralise enterprise workflows',
				'solucao_plat_topico_2'    => 'Avoid new licences from the vendor',
				'solucao_plat_topico_3'    => 'Connect systems with no add-ons',
				'solucao_acel_titulo'      => 'Start with processes already structured',
				'solucao_acel_corpo'       => 'Use a ready-made template to connect Freshservice forms and catalogues to the internal systems faster.',
				'solucao_acel_topico_1'    => 'Connect processes in minutes',
				'solucao_acel_topico_2'    => 'Reuse workflows already proven',
				'solucao_acel_topico_3'    => 'Shape flows around the business',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * ServiceNow.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_servicenow() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your ServiceNow',
				'solucao_hero_titulo'      => 'Get more out of ServiceNow without paying for more modules',
				'solucao_hero_corpo'       => 'Build complete processes in ServiceNow and connect the ERP, CRM and internal systems directly, with no additional integration modules.',
				'solucao_pilares_titulo'   => 'Turn ServiceNow into a process hub',
				'solucao_pilares_1_titulo' => 'Build connected processes',
				'solucao_pilares_1_desc'   => 'Use the catalogue, approvals and flows wired into the internal systems.',
				'solucao_pilares_2_titulo' => 'Cut licensing costs',
				'solucao_pilares_2_desc'   => 'Avoid paid modules for every new integration you need.',
				'solucao_pilares_3_titulo' => 'Orchestrate end to end',
				'solucao_pilares_3_desc'   => 'Capture requests, validate the data and write it into the systems.',
				'solucao_casos_titulo'     => 'Automate processes connected to ServiceNow',
				'solucao_casos_1_titulo'   => 'Automate product registration',
				'solucao_casos_1_desc'     => 'Run the approvals in ServiceNow and write the data into the ERP.',
				'solucao_casos_2_titulo'   => 'Open incidents automatically',
				'solucao_casos_2_desc'     => 'Receive events from AI and other systems straight into ServiceNow.',
				'solucao_casos_3_titulo'   => 'Synchronise the CMDB',
				'solucao_casos_3_desc'     => 'Connect infrastructure data with no additional Spokes.',
				'solucao_casos_4_titulo'   => 'Validate approvals in the ERP',
				'solucao_casos_4_desc'     => 'Check budget and stock before approving a change.',
				'solucao_casos_5_titulo'   => 'Automate corporate access',
				'solucao_casos_5_desc'     => 'Trigger provisioning from HR events.',
				'solucao_dif_titulo'       => 'ServiceNow integrations with full governance',
				'solucao_dif_corpo'        => 'Connect ServiceNow through the API with secure authentication and two-way events, keeping the audit trail in one place and without paid connectors.',
				'solucao_dif_topico_1'     => 'Use the official ServiceNow APIs',
				'solucao_dif_topico_2'     => 'Control access through authentication',
				'solucao_dif_topico_3'     => 'Audit every event',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Connect processes without capping growth',
				'solucao_plat_corpo'       => 'Use ServiceNow to orchestrate the experience while CLI Connect connects the internal systems, with no rise in licensing costs.',
				'solucao_plat_topico_1'    => 'Centralise enterprise integrations',
				'solucao_plat_topico_2'    => 'Depend less on Spokes',
				'solucao_plat_topico_3'    => 'Scale up new processes',
				'solucao_acel_titulo'      => 'Start with processes already structured',
				'solucao_acel_corpo'       => 'Use a ready-made template to build processes in ServiceNow and write data straight into an ERP such as Totvs or SAP.',
				'solucao_acel_topico_1'    => 'Set up processes quickly',
				'solucao_acel_topico_2'    => 'Adapt the entities you already have',
				'solucao_acel_topico_3'    => 'Speed up new automations',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * Portal de API / MCP Server.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_portal_de_api() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'api and mcp server',
				'solucao_hero_titulo'      => 'Turn any internal system into an API or an AI tool',
				'solucao_hero_corpo'       => 'Expose systems such as ERP, CRM, ITSM and databases as standardised APIs or MCP servers, ready to be consumed by applications, teams and AI agents.',
				'solucao_pilares_titulo'   => 'Open up access to the internal systems',
				'solucao_pilares_1_titulo' => 'Publish APIs with no extra code',
				'solucao_pilares_1_desc'   => 'Turn existing pipelines into documented REST APIs that new projects can reuse.',
				'solucao_pilares_2_titulo' => 'Connect AI agents to the systems',
				'solucao_pilares_2_desc'   => 'Expose processes as authenticated MCP tools so agents can query them and act.',
				'solucao_pilares_3_titulo' => 'Centralise access governance',
				'solucao_pilares_3_desc'   => 'Control the consumers, permissions and scopes of every system published in the portal.',
				'solucao_casos_titulo'     => 'Get more out of the systems you have connected',
				'solucao_casos_1_titulo'   => 'Build APIs for enterprise systems',
				'solucao_casos_1_desc'     => 'Expose Totvs, Sankhya or SAP as single APIs for reusable queries and operations.',
				'solucao_casos_2_titulo'   => 'Connect AI agents to the ERP',
				'solucao_casos_2_desc'     => 'Let agents check stock and create orders using natural language.',
				'solucao_casos_3_titulo'   => 'Build an internal API catalogue',
				'solucao_casos_3_desc'     => 'Help teams find and reuse existing integrations instead of rebuilding them.',
				'solucao_casos_4_titulo'   => 'Modernise legacy access',
				'solucao_casos_4_desc'     => 'Expose mainframes and ESBs as modern APIs without revealing the old protocols.',
				'solucao_casos_5_titulo'   => 'Control your API consumers',
				'solucao_casos_5_desc'     => 'Manage access, limits and audit trails by user, system or agent.',
				'solucao_dif_titulo'       => 'Secure APIs for people and agents',
				'solucao_dif_corpo'        => 'Every API or MCP Server you publish inherits the security of the platform: authentication, scope control and protection for sensitive data.',
				'solucao_dif_topico_1'     => 'Protect APIs with token authentication',
				'solucao_dif_topico_2'     => 'Control scopes per consumer',
				'solucao_dif_topico_3'     => 'Protect sensitive data with guardrails',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Unify access to every system',
				'solucao_plat_corpo'       => 'Connect your internal systems once and reuse those capabilities as APIs or AI tools, instead of rebuilding integrations for each project.',
				'solucao_plat_topico_1'    => 'Centralise access to enterprise systems',
				'solucao_plat_topico_2'    => 'Reuse the integrations already built',
				'solucao_plat_topico_3'    => 'Avoid redundant new development',
				'solucao_acel_titulo'      => 'Turn the integrations you have into APIs',
				'solucao_acel_corpo'       => 'Publish pipelines you have already built as documented endpoints or MCP tools, with no new development project.',
				'solucao_acel_topico_1'    => 'Convert pipelines quickly',
				'solucao_acel_topico_2'    => 'Reuse existing integrations',
				'solucao_acel_topico_3'    => 'Publish APIs in a few clicks',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * Zendesk.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_zendesk() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Zendesk',
				'solucao_hero_titulo'      => 'Get more out of Zendesk by building complete processes with no extra apps',
				'solucao_hero_corpo'       => 'Connect Zendesk to the ERP, CRM and billing systems to build forms and support flows that read from and write to the internal systems directly.',
				'solucao_pilares_titulo'   => 'Turn Zendesk into a connected service desk',
				'solucao_pilares_1_titulo' => 'Connect support to the internal systems',
				'solucao_pilares_1_desc'   => 'Integrate Zendesk tickets, forms and macros directly with the ERP, CRM and billing.',
				'solucao_pilares_2_titulo' => 'Rely on fewer extra apps',
				'solucao_pilares_2_desc'   => 'Stop depending on paid Marketplace apps for every new integration.',
				'solucao_pilares_3_titulo' => 'Enrich tickets automatically',
				'solucao_pilares_3_desc'   => 'Look up order, customer and invoice data without leaving the support screen.',
				'solucao_casos_titulo'     => 'Automate the support processes that matter most',
				'solucao_casos_1_titulo'   => 'Automate refund requests',
				'solucao_casos_1_desc'     => 'Read and write financial information in the ERP straight from the Zendesk ticket.',
				'solucao_casos_2_titulo'   => 'Enrich tickets in real time',
				'solucao_casos_2_desc'     => 'Show ERP order and invoice data while the agent is with the customer.',
				'solucao_casos_3_titulo'   => 'Create tickets automatically',
				'solucao_casos_3_desc'     => 'Turn ERP, e-commerce and monitoring events into support requests.',
				'solucao_casos_4_titulo'   => 'Keep support and sales in sync',
				'solucao_casos_4_desc'     => 'Keep ticket status aligned between Zendesk and the CRM platforms.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make Zendesk tickets and macros available as tools for intelligent agents.',
				'solucao_dif_titulo'       => 'Secure integrations for customer service',
				'solucao_dif_corpo'        => 'Connect Zendesk through the official APIs with secure authentication and permission control by agent and department.',
				'solucao_dif_topico_1'     => 'Use the Zendesk REST API',
				'solucao_dif_topico_2'     => 'Protect connections with OAuth',
				'solucao_dif_topico_3'     => 'Control permissions per agent',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Centralise integrations beyond Zendesk',
				'solucao_plat_corpo'       => 'Let Zendesk focus on the customer experience while the platform connects and moves data across the internal systems, with no extra apps.',
				'solucao_plat_topico_1'    => 'Centralise support integrations',
				'solucao_plat_topico_2'    => 'Depend less on the Marketplace',
				'solucao_plat_topico_3'    => 'Scale processes predictably',
				'solucao_acel_titulo'      => 'Start with processes already structured',
				'solucao_acel_corpo'       => 'Use a ready-made template to connect Zendesk tickets to the ERP and CRM in read and write processes.',
				'solucao_acel_topico_1'    => 'Connect processes quickly',
				'solucao_acel_topico_2'    => 'Reuse support flows',
				'solucao_acel_topico_3'    => 'Shape integrations around the business',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * Bionexo.
	 *
	 * O caso de uso 5 repete o texto do Zendesk também no português — resíduo
	 * do seed original, mantido aqui para os dois idiomas dizerem o mesmo.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_bionexo() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Bionexo',
				'solucao_hero_titulo'      => 'Connect the largest B2B healthcare marketplace to your ERP',
				'solucao_hero_corpo'       => 'Integrate Bionexo purchasing, contracts and billing with the finance ERP and the HIS to remove rework and keep data in sync across the hospital operation.',
				'solucao_pilares_titulo'   => 'Connect hospital purchasing to the internal systems',
				'solucao_pilares_1_titulo' => 'Synchronise orders automatically',
				'solucao_pilares_1_desc'   => 'Connect Bionexo orders to the finance ERP and stock with no manual steps.',
				'solucao_pilares_2_titulo' => 'Centralise supplier negotiations',
				'solucao_pilares_2_desc'   => 'Keep contracts, prices and commercial terms in sync with the internal systems.',
				'solucao_pilares_3_titulo' => 'Cut operational rework',
				'solucao_pilares_3_desc'   => 'Remove manual re-keying between the marketplace, the ERP and hospital systems.',
				'solucao_casos_titulo'     => 'Automate your hospital purchasing processes',
				'solucao_casos_1_titulo'   => 'Sync purchase orders',
				'solucao_casos_1_desc'     => 'Send Bionexo orders straight to the hospital ERP with no manual intervention.',
				'solucao_casos_2_titulo'   => 'Reconcile invoices automatically',
				'solucao_casos_2_desc'     => 'Match invoices received through the marketplace with internal financial records.',
				'solucao_casos_3_titulo'   => 'Update contracts and prices',
				'solucao_casos_3_desc'     => 'Synchronise supplier negotiations into the supply management system.',
				'solucao_casos_4_titulo'   => 'Consolidate purchasing data',
				'solucao_casos_4_desc'     => 'Centralise information for hospital cost and efficiency analysis.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make Zendesk tickets and macros available as tools for intelligent agents.',
				'solucao_dif_titulo'       => 'Secure integrations for healthcare',
				'solucao_dif_corpo'        => 'Connect Bionexo through the official APIs with secure authentication and data protection in line with the LGPD.',
				'solucao_dif_topico_1'     => 'Use the Bionexo REST API',
				'solucao_dif_topico_2'     => 'Protect access with tokens',
				'solucao_dif_topico_3'     => 'Protect data in line with the LGPD',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Bring purchasing and hospital systems together',
				'solucao_plat_corpo'       => 'Connect the marketplace, the HIS and the finance ERP on a single platform to remove spreadsheets and close the operational loop.',
				'solucao_plat_topico_1'    => 'Centralise purchasing flows',
				'solucao_plat_topico_2'    => 'Connect several hospital systems',
				'solucao_plat_topico_3'    => 'Remove manual processes',
				'solucao_acel_titulo'      => 'Start with purchasing already integrated',
				'solucao_acel_corpo'       => 'Use a ready-made template to connect Bionexo orders to the hospital ERP and speed up automation.',
				'solucao_acel_topico_1'    => 'Connect orders quickly',
				'solucao_acel_topico_2'    => 'Reuse hospital flows',
				'solucao_acel_topico_3'    => 'Adapt internal processes',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * Tasy.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_tasy() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Tasy',
				'solucao_hero_titulo'      => 'Connect the core of the hospital operation to the whole ecosystem',
				'solucao_hero_corpo'       => 'Integrate Tasy with laboratories, health insurers, the enterprise ERP and AI agents to connect clinical and financial data without changing the hospital core.',
				'solucao_pilares_titulo'   => 'Get more out of your Tasy data',
				'solucao_pilares_1_titulo' => 'Build on the standard Tasy APIs',
				'solucao_pilares_1_desc'   => 'Use the Tasy Open API to build documented, secure and scalable integrations.',
				'solucao_pilares_2_titulo' => 'Connect TISS billing',
				'solucao_pilares_2_desc'   => 'Integrate Tasy with health insurers to automate the billing process.',
				'solucao_pilares_3_titulo' => 'Centralise hospital data',
				'solucao_pilares_3_desc'   => 'Bring clinical and financial information together for analysis without touching the main system.',
				'solucao_casos_titulo'     => 'Automate the critical hospital processes',
				'solucao_casos_1_titulo'   => 'Automate TISS billing',
				'solucao_casos_1_desc'     => 'Connect Tasy to health insurers to speed up the billing process.',
				'solucao_casos_2_titulo'   => 'Sync laboratory results',
				'solucao_casos_2_desc'     => 'Integrate LIS systems with the medical record to publish results automatically.',
				'solucao_casos_3_titulo'   => 'Reconcile financial data',
				'solucao_casos_3_desc'     => 'Connect Tasy and the enterprise ERP to consolidate financial information.',
				'solucao_casos_4_titulo'   => 'Consolidate hospital networks',
				'solucao_casos_4_desc'     => 'Standardise integrations across several units and hospital systems.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make clinical data available to administrative agents without exposing the clinical core.',
				'solucao_dif_titulo'       => 'Secure integrations for hospital data',
				'solucao_dif_corpo'        => 'Use the Tasy Open API with authentication, encryption and access control to protect sensitive health information.',
				'solucao_dif_topico_1'     => 'Use the official Tasy APIs',
				'solucao_dif_topico_2'     => 'Protect sensitive health data',
				'solucao_dif_topico_3'     => 'Control access in line with the LGPD',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Unify complex hospital operations',
				'solucao_plat_corpo'       => 'Build one integration layer to connect several Tasy units and hospital systems without customising the clinical core.',
				'solucao_plat_topico_1'    => 'Standardise integrations across units',
				'solucao_plat_topico_2'    => 'Centralise hospital billing',
				'solucao_plat_topico_3'    => 'Simplify financial consolidation',
				'solucao_acel_titulo'      => 'Start with hospital integrations ready',
				'solucao_acel_corpo'       => 'Use a structured template to connect the Tasy Open API to the finance ERP and to health insurers.',
				'solucao_acel_topico_1'    => 'Connect systems quickly',
				'solucao_acel_topico_2'    => 'Reuse hospital flows',
				'solucao_acel_topico_3'    => 'Speed up new integrations',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * MV.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_mv() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your SOUL MV',
				'solucao_hero_titulo'      => 'Connect SOUL MV to the full digital hospital ecosystem',
				'solucao_hero_corpo'       => 'Integrate MV with the laboratory, diagnostic imaging, the enterprise ERP and billing to bring clinical, administrative and financial processes into one operation.',
				'solucao_pilares_titulo'   => 'Extend what SOUL MV can connect to',
				'solucao_pilares_1_titulo' => 'Integrate clinical systems',
				'solucao_pilares_1_desc'   => 'Connect RIS, PACS, LIS and exam portals to MV with real-time data exchange.',
				'solucao_pilares_2_titulo' => 'Cut hospital claim rejections',
				'solucao_pilares_2_desc'   => 'Validate exam request details before the exam happens and avoid inconsistencies.',
				'solucao_pilares_3_titulo' => 'Centralise financial data',
				'solucao_pilares_3_desc'   => 'Consolidate information across hospital units and enterprise systems.',
				'solucao_casos_titulo'     => 'Automate the hospital processes that matter most',
				'solucao_casos_1_titulo'   => 'Integrate RIS and PACS with MV',
				'solucao_casos_1_desc'     => 'Check allergies and clinical history during exams without switching systems.',
				'solucao_casos_2_titulo'   => 'Automate hospital billing',
				'solucao_casos_2_desc'     => 'Synchronise billing and claim rejection data with health insurers.',
				'solucao_casos_3_titulo'   => 'Reconcile finances across units',
				'solucao_casos_3_desc'     => 'Connect MV and the enterprise ERP to consolidate financial results.',
				'solucao_casos_4_titulo'   => 'Automate internal access',
				'solucao_casos_4_desc'     => 'Provision access to supporting systems from events in MV.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make clinical data available to administrative agents without exposing the clinical core.',
				'solucao_dif_titulo'       => 'Secure integrations for healthcare',
				'solucao_dif_corpo'        => 'Connect MV through APIs with an audit trail and security controls that protect clinical data in line with the LGPD.',
				'solucao_dif_topico_1'     => 'Use the MV system APIs',
				'solucao_dif_topico_2'     => 'Audit hospital integrations',
				'solucao_dif_topico_3'     => 'Protect sensitive clinical data',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Unify the systems of a hospital network',
				'solucao_plat_corpo'       => 'Centralise integrations across different HIS, ERPs and clinical systems so each unit does not repeat the same project.',
				'solucao_plat_topico_1'    => 'Connect different hospital platforms',
				'solucao_plat_topico_2'    => 'Standardise integrations across units',
				'solucao_plat_topico_3'    => 'Reduce maintenance effort',
				'solucao_acel_titulo'      => 'Start with hospital integrations ready',
				'solucao_acel_corpo'       => 'Use a structured template to connect MV, RIS/PACS, LIS and the finance ERP faster.',
				'solucao_acel_topico_1'    => 'Connect systems quickly',
				'solucao_acel_topico_2'    => 'Reuse hospital flows',
				'solucao_acel_topico_3'    => 'Speed up new automations',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * VTEX.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_vtex() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your VTEX',
				'solucao_hero_titulo'      => 'Connect your e-commerce to the ERP in real time',
				'solucao_hero_corpo'       => 'Integrate VTEX with the ERP, WMS and payment systems to synchronise orders, stock and omnichannel operations quickly and safely.',
				'solucao_pilares_titulo'   => 'Scale your digital operation, connected',
				'solucao_pilares_1_titulo' => 'Synchronise orders automatically',
				'solucao_pilares_1_desc'   => 'Connect VTEX orders to the ERP in real time, with no manual steps.',
				'solucao_pilares_2_titulo' => 'Keep omnichannel stock current',
				'solucao_pilares_2_desc'   => 'Keep physical stores, marketplaces and digital channels in sync.',
				'solucao_pilares_3_titulo' => 'Build on the native VTEX APIs',
				'solucao_pilares_3_desc'   => 'Use the API-first architecture to integrate catalogue, orders and operations.',
				'solucao_casos_titulo'     => 'Automate your e-commerce operations',
				'solucao_casos_1_titulo'   => 'Sync orders with the ERP',
				'solucao_casos_1_desc'     => 'Send VTEX orders to the ERP automatically to speed up processing.',
				'solucao_casos_2_titulo'   => 'Update stock across channels',
				'solucao_casos_2_desc'     => 'Connect physical store, marketplace and e-commerce with synchronised stock.',
				'solucao_casos_3_titulo'   => 'Integrate payments and finance',
				'solucao_casos_3_desc'     => 'Reconcile digital transactions with the internal finance systems.',
				'solucao_casos_4_titulo'   => 'Automate ship-from-store',
				'solucao_casos_4_desc'     => 'Turn physical stores into fulfilment points for digital orders.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make e-commerce data available so AI agents can automate service and operations.',
				'solucao_dif_titulo'       => 'Integrations built to scale',
				'solucao_dif_corpo'        => 'Connect VTEX through the official APIs with secure authentication to support high-volume digital operations.',
				'solucao_dif_topico_1'     => 'Use VTEX IO and the REST API',
				'solucao_dif_topico_2'     => 'Authenticate with App Token',
				'solucao_dif_topico_3'     => 'Absorb sales peaks',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Bring your commerce ecosystem together',
				'solucao_plat_corpo'       => 'Connect VTEX, ERP, WMS and payments on a single platform to keep your operation in sync across the whole buying journey.',
				'solucao_plat_topico_1'    => 'Centralise commercial integrations',
				'solucao_plat_topico_2'    => 'Absorb operational peaks',
				'solucao_plat_topico_3'    => 'Keep systems in sync',
				'solucao_acel_titulo'      => 'Start with e-commerce integrations ready',
				'solucao_acel_corpo'       => 'Use a ready-made template to connect VTEX to the ERP with structured order, stock and tax flows.',
				'solucao_acel_topico_1'    => 'Connect operations quickly',
				'solucao_acel_topico_2'    => 'Reuse commercial flows',
				'solucao_acel_topico_3'    => 'Speed up new integrations',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * Shopify.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_shopify() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Shopify',
				'solucao_hero_titulo'      => 'Connect your Shopify store to the ERP without relying on generic plugins',
				'solucao_hero_corpo'       => 'Integrate Shopify, ERP, Brazilian tax systems and WMS to automate orders, stock and financial operations with rules shaped around your business.',
				'solucao_pilares_titulo'   => 'Scale your Shopify operation, connected',
				'solucao_pilares_1_titulo' => 'Meet Brazilian tax rules',
				'solucao_pilares_1_desc'   => 'Connect Shopify to Brazilian tax systems to automate NF-e and specific tax processes.',
				'solucao_pilares_2_titulo' => 'Synchronise multichannel stock',
				'solucao_pilares_2_desc'   => 'Keep stock up to date across Shopify, the ERP and every sales channel.',
				'solucao_pilares_3_titulo' => 'Integrate Shopify Plus',
				'solucao_pilares_3_desc'   => 'Support the advanced operations of large brands on Shopify or Shopify Plus.',
				'solucao_casos_titulo'     => 'Automate your digital store processes',
				'solucao_casos_1_titulo'   => 'Issue NF-e automatically',
				'solucao_casos_1_desc'     => 'Connect Shopify orders to the tax system to generate electronic documents.',
				'solucao_casos_2_titulo'   => 'Sync multichannel stock',
				'solucao_casos_2_desc'     => 'Update availability across ERP, Shopify and marketplaces automatically.',
				'solucao_casos_3_titulo'   => 'Reconcile digital payments',
				'solucao_casos_3_desc'     => 'Connect payment gateways to finance to make reconciliation easier.',
				'solucao_casos_4_titulo'   => 'Automate returns',
				'solucao_casos_4_desc'     => 'Integrate the returns process across Shopify, the ERP and internal operations.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make store data available so AI agents can automate service and operations.',
				'solucao_dif_titulo'       => 'Native integrations for Shopify',
				'solucao_dif_corpo'        => 'Connect your operation through the Shopify Admin API, GraphQL and webhooks to synchronise events in real time, securely.',
				'solucao_dif_topico_1'     => 'Use the Shopify Admin API',
				'solucao_dif_topico_2'     => 'Capture events with webhooks',
				'solucao_dif_topico_3'     => 'Connect through GraphQL',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Go past the limits of Shopify apps',
				'solucao_plat_corpo'       => 'Shopify apps solve generic scenarios. A dedicated integration platform connects tax rules, several ERPs and genuinely complex operations.',
				'solucao_plat_topico_1'    => 'Centralise commercial integrations',
				'solucao_plat_topico_2'    => 'Shape the rules around the business',
				'solucao_plat_topico_3'    => 'Depend less on third parties',
				'solucao_acel_titulo'      => 'Start with a Shopify operation already integrated',
				'solucao_acel_corpo'       => 'Use a ready-made template to connect Shopify to the ERP and automate Brazilian tax document issuing from day one.',
				'solucao_acel_topico_1'    => 'Connect the ERP quickly',
				'solucao_acel_topico_2'    => 'Automate tax processes',
				'solucao_acel_topico_3'    => 'Speed up new integrations',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * Magento / Adobe Commerce.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_magento() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Magento',
				'solucao_hero_titulo'      => 'Connect Magento to your enterprise stack without piling up extensions',
				'solucao_hero_corpo'       => 'Integrate Magento and Adobe Commerce with the ERP, PIM and payment systems to scale your digital operation without loading the platform core with customisations.',
				'solucao_pilares_titulo'   => 'Scale your digital commerce, connected',
				'solucao_pilares_1_titulo' => 'Integrate any Magento architecture',
				'solucao_pilares_1_desc'   => 'Connect on-premises environments and Adobe Commerce Cloud through one central layer.',
				'solucao_pilares_2_titulo' => 'Synchronise catalogue and pricing',
				'solucao_pilares_2_desc'   => 'Keep products and commercial information up to date from the PIM.',
				'solucao_pilares_3_titulo' => 'Connect Brazilian payment methods',
				'solucao_pilares_3_desc'   => 'Integrate several payment gateways into the checkout of your digital operation.',
				'solucao_casos_titulo'     => 'Automate your digital commerce operations',
				'solucao_casos_1_titulo'   => 'Sync orders with the ERP',
				'solucao_casos_1_desc'     => 'Send Magento orders to the ERP automatically to speed up processing.',
				'solucao_casos_2_titulo'   => 'Centralise the catalogue through a PIM',
				'solucao_casos_2_desc'     => 'Update products and prices in Magento from a single source.',
				'solucao_casos_3_titulo'   => 'Reconcile payments automatically',
				'solucao_casos_3_desc'     => 'Integrate gateways and anti-fraud with finance to cut discrepancies.',
				'solucao_casos_4_titulo'   => 'Automate returns',
				'solucao_casos_4_desc'     => 'Connect returns and reverse logistics to the internal systems.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make e-commerce data available so agents can automate service and operations.',
				'solucao_dif_titulo'       => 'Secure integrations for Magento',
				'solucao_dif_corpo'        => 'Connect Magento through the REST and GraphQL APIs, with scoped integration tokens protecting every access.',
				'solucao_dif_topico_1'     => 'Use the REST and GraphQL APIs',
				'solucao_dif_topico_2'     => 'Control access by scope',
				'solucao_dif_topico_3'     => 'Protect enterprise integrations',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Evolve without depending on extensions',
				'solucao_plat_corpo'       => 'An external integration layer reduces customisation inside Magento, makes upgrades easier and connects enterprise systems with more flexibility.',
				'solucao_plat_topico_1'    => 'Centralise external integrations',
				'solucao_plat_topico_2'    => 'Change less in the core',
				'solucao_plat_topico_3'    => 'Simplify future updates',
				'solucao_acel_titulo'      => 'Start with commerce already integrated',
				'solucao_acel_corpo'       => 'Use a ready-made template to connect Magento or Adobe Commerce to the ERP and PIM through structured flows.',
				'solucao_acel_topico_1'    => 'Connect systems quickly',
				'solucao_acel_topico_2'    => 'Reuse commercial integrations',
				'solucao_acel_topico_3'    => 'Speed up new projects',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * OnBlox (WMS/TMS).
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_onblox() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your OnBlox',
				'solucao_hero_titulo'      => 'Connect WMS and TMS to the ERP and carriers in real time',
				'solucao_hero_corpo'       => 'Integrate OnBlox with ERPs, e-commerce and tracking apps to synchronise stock, logistics operations and fleet management with no manual steps.',
				'solucao_pilares_titulo'   => 'Connect your whole logistics operation',
				'solucao_pilares_1_titulo' => 'Synchronise inventory automatically',
				'solucao_pilares_1_desc'   => 'Keep stock aligned across WMS, ERP and sales channels in real time.',
				'solucao_pilares_2_titulo' => 'Integrate fleet management',
				'solucao_pilares_2_desc'   => 'Connect maintenance, documents and licences to the finance systems.',
				'solucao_pilares_3_titulo' => 'Speed up logistics rollouts',
				'solucao_pilares_3_desc'   => 'Cut integration time with flows already prepared for logistics operations.',
				'solucao_casos_titulo'     => 'Automate the logistics processes that matter most',
				'solucao_casos_1_titulo'   => 'Sync stock with the ERP',
				'solucao_casos_1_desc'     => 'Connect OnBlox to the ERP and marketplaces to update availability automatically.',
				'solucao_casos_2_titulo'   => 'Route orders automatically',
				'solucao_casos_2_desc'     => 'Send each order to the most suitable distribution centre.',
				'solucao_casos_3_titulo'   => 'Connect fleet tracking',
				'solucao_casos_3_desc'     => 'Integrate carriers and tracking apps into the logistics ecosystem.',
				'solucao_casos_4_titulo'   => 'Update dispatches in real time',
				'solucao_casos_4_desc'     => 'Send picking and shipping status straight to the ERP.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make logistics data available so agents can automate service and operations.',
				'solucao_dif_titulo'       => 'Integrations built for heavy operations',
				'solucao_dif_corpo'        => 'Connect many handheld scanners and mobile devices with high data volumes across warehouse and fleet operations.',
				'solucao_dif_topico_1'     => 'Handle high operational volume',
				'solucao_dif_topico_2'     => 'Connect many mobile devices',
				'solucao_dif_topico_3'     => 'Keep data in sync',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Bring logistics and enterprise systems together',
				'solucao_plat_corpo'       => 'Connect warehouse, fleet, ERP and finance on a single platform to remove spreadsheets and manual processes.',
				'solucao_plat_topico_1'    => 'Centralise logistics data',
				'solucao_plat_topico_2'    => 'Connect operations to finance',
				'solucao_plat_topico_3'    => 'Remove manual exports',
				'solucao_acel_titulo'      => 'Start with logistics already integrated',
				'solucao_acel_corpo'       => 'Use a ready-made template to connect OnBlox to the ERP and speed up the automation of your logistics processes.',
				'solucao_acel_topico_1'    => 'Connect the WMS quickly',
				'solucao_acel_topico_2'    => 'Reuse logistics flows',
				'solucao_acel_topico_3'    => 'Speed up new integrations',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * Narwal (Comex).
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_narwal() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Narwal',
				'solucao_hero_titulo'      => 'Connect foreign trade to the ERP, from order to customs clearance',
				'solucao_hero_corpo'       => 'Integrate Narwal with the finance ERP and the official foreign trade bodies to automate imports, exports and operating costs with no manual steps.',
				'solucao_pilares_titulo'   => 'Connect your whole foreign trade operation',
				'solucao_pilares_1_titulo' => 'Integrate international orders',
				'solucao_pilares_1_desc'   => 'Connect international purchases and sales from Narwal straight into the enterprise ERP.',
				'solucao_pilares_2_titulo' => 'Synchronise official channels',
				'solucao_pilares_2_desc'   => 'Integrate Siscomex, Siscarga, Mantra and other foreign trade environments.',
				'solucao_pilares_3_titulo' => 'Automate import costs',
				'solucao_pilares_3_desc'   => 'Update freight, customs clearance and expenses directly in finance.',
				'solucao_casos_titulo'     => 'Automate your foreign trade processes',
				'solucao_casos_1_titulo'   => 'Sync import orders',
				'solucao_casos_1_desc'     => 'Connect Narwal processes to the ERP to remove manual entry.',
				'solucao_casos_2_titulo'   => 'Update costs automatically',
				'solucao_casos_2_desc'     => 'Send freight and clearance expenses straight to finance.',
				'solucao_casos_3_titulo'   => 'Consolidate foreign trade operations',
				'solucao_casos_3_desc'     => 'Centralise data from different branches for strategic analysis.',
				'solucao_casos_4_titulo'   => 'Track shipments automatically',
				'solucao_casos_4_desc'     => 'Trigger ETD and ETA alerts to the connected systems.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make foreign trade data available so agents can automate administrative processes.',
				'solucao_dif_titulo'       => 'Secure integrations for foreign trade',
				'solucao_dif_corpo'        => 'Connect Narwal through dedicated APIs, with an audit trail across the import and export stages for tighter operational control.',
				'solucao_dif_topico_1'     => 'Integrate through dedicated APIs',
				'solucao_dif_topico_2'     => 'Audit every stage of the process',
				'solucao_dif_topico_3'     => 'Protect certified operations',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Bring foreign trade and finance together',
				'solucao_plat_corpo'       => 'Connect shipments, costs and financial entries to remove the manual controls between Narwal and the ERP.',
				'solucao_plat_topico_1'    => 'Centralise foreign trade data',
				'solucao_plat_topico_2'    => 'Automate financial entries',
				'solucao_plat_topico_3'    => 'Cut down on spreadsheet controls',
				'solucao_acel_titulo'      => 'Start with foreign trade already integrated',
				'solucao_acel_corpo'       => 'Use a ready-made template to connect Narwal processes to the finance ERP and speed up your operation.',
				'solucao_acel_topico_1'    => 'Connect processes quickly',
				'solucao_acel_topico_2'    => 'Reuse import flows',
				'solucao_acel_topico_3'    => 'Speed up finance integrations',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * Neogrid.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_neogrid() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Neogrid',
				'solucao_hero_titulo'      => 'Connect your EDI network to the ERP without relying on off-the-shelf connectors alone',
				'solucao_hero_corpo'       => 'Integrate the Neogrid EDI and retail visibility ecosystem with ERPs, BI and enterprise systems to take your operation beyond the native connections.',
				'solucao_pilares_titulo'   => 'Get more out of your Neogrid network',
				'solucao_pilares_1_titulo' => 'Connect any enterprise ERP',
				'solucao_pilares_1_desc'   => 'Integrate Neogrid with ERPs beyond the native connectors already on the market.',
				'solucao_pilares_2_titulo' => 'Synchronise commercial data',
				'solucao_pilares_2_desc'   => 'Connect orders, invoices and retail information to the internal ERP.',
				'solucao_pilares_3_titulo' => 'Centralise the network data',
				'solucao_pilares_3_desc'   => 'Consolidate sales and stock information from several trading partners.',
				'solucao_casos_titulo'     => 'Automate the processes of your commercial chain',
				'solucao_casos_1_titulo'   => 'Translate EDI orders automatically',
				'solucao_casos_1_desc'     => 'Convert orders received through Neogrid into the format of the internal ERP.',
				'solucao_casos_2_titulo'   => 'Connect data to enterprise BI',
				'solucao_casos_2_desc'     => 'Send retail and distribution information into strategic analysis.',
				'solucao_casos_3_titulo'   => 'Integrate invoices with finance',
				'solucao_casos_3_desc'     => 'Connect the tax documents that travel through Neogrid to the finance systems.',
				'solucao_casos_4_titulo'   => 'Monitor stockouts and inventory',
				'solucao_casos_4_desc'     => 'Consolidate commercial indicators for sales and operations teams.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make commercial and retail data available so agents can automate service and analysis.',
				'solucao_dif_titulo'       => 'Translate EDI safely',
				'solucao_dif_corpo'        => 'Connect Neogrid APIs and EDI formats with message translation, so different systems understand each other.',
				'solucao_dif_topico_1'     => 'Integrate Neogrid APIs and EDI',
				'solucao_dif_topico_2'     => 'Translate formats automatically',
				'solucao_dif_topico_3'     => 'Connect heterogeneous ERPs',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Connect beyond the main ERP',
				'solucao_plat_corpo'       => 'Extend the Neogrid ecosystem by connecting EDI and visibility data to new systems, instead of limiting the operation to the connectors that already exist.',
				'solucao_plat_topico_1'    => 'Centralise supply chain data',
				'solucao_plat_topico_2'    => 'Connect additional systems',
				'solucao_plat_topico_3'    => 'Extend the integrations you have',
				'solucao_acel_titulo'      => 'Start with commercial data already connected',
				'solucao_acel_corpo'       => 'Use a ready-made template to connect Neogrid to ERP and BI with structured commercial data flows.',
				'solucao_acel_topico_1'    => 'Connect EDI quickly',
				'solucao_acel_topico_2'    => 'Reuse commercial flows',
				'solucao_acel_topico_3'    => 'Speed up new integrations',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * Target Sistemas (ERP de Distribuição).
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_target_sistemas() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Target',
				'solucao_hero_titulo'      => 'Connect your distribution ERP to industry, customers and finance',
				'solucao_hero_corpo'       => 'Integrate Target ERP with industrial partners, field sales, banks and logistics systems to automate distribution operations at scale.',
				'solucao_pilares_titulo'   => 'Scale your distribution operation, connected',
				'solucao_pilares_1_titulo' => 'Connect distribution flows',
				'solucao_pilares_1_desc'   => 'Integrate the tax, logistics and commercial processes of wholesale into the wider ecosystem.',
				'solucao_pilares_2_titulo' => 'Automate EDI integrations',
				'solucao_pilares_2_desc'   => 'Connect industrial suppliers to Target with automatic exchange of information.',
				'solucao_pilares_3_titulo' => 'Synchronise field sales',
				'solucao_pilares_3_desc'   => 'Keep mobile orders up to date in the ERP in real time.',
				'solucao_casos_titulo'     => 'Automate your distribution processes',
				'solucao_casos_1_titulo'   => 'Connect EDI with manufacturers',
				'solucao_casos_1_desc'     => 'Automate the exchange of sell-out data with partner suppliers.',
				'solucao_casos_2_titulo'   => 'Sync mobile orders',
				'solucao_casos_2_desc'     => 'Send field sales orders straight into Target ERP.',
				'solucao_casos_3_titulo'   => 'Reconcile financial operations',
				'solucao_casos_3_desc'     => 'Integrate banks and financial processes across several companies.',
				'solucao_casos_4_titulo'   => 'Connect logistics to the ERP',
				'solucao_casos_4_desc'     => 'Integrate WMS and route planning to control delivery operations.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make distribution data available so agents can automate service and analysis.',
				'solucao_dif_titulo'       => 'Integrations built for distribution scale',
				'solucao_dif_corpo'        => 'Connect high-volume operations while keeping performance across many branches, SKUs and simultaneous integrations.',
				'solucao_dif_topico_1'     => 'Handle high data volumes',
				'solucao_dif_topico_2'     => 'Connect multiple branches',
				'solucao_dif_topico_3'     => 'Scale commercial operations',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Bring your distribution connections together',
				'solucao_plat_corpo'       => 'Centralise EDI, field sales and logistics integrations to cut the effort of connecting each new partner.',
				'solucao_plat_topico_1'    => 'Centralise industrial integrations',
				'solucao_plat_topico_2'    => 'Onboard suppliers faster',
				'solucao_plat_topico_3'    => 'Cut out repetitive projects',
				'solucao_acel_titulo'      => 'Start with distribution already connected',
				'solucao_acel_corpo'       => 'Use a ready-made template to connect Target ERP to industrial partners and field sales apps.',
				'solucao_acel_topico_1'    => 'Connect suppliers quickly',
				'solucao_acel_topico_2'    => 'Reuse EDI flows',
				'solucao_acel_topico_3'    => 'Onboard new partners faster',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * SAP Business One.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_sap_business_one() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your SAP B1',
				'solucao_hero_titulo'      => 'Connect SAP Business One without needing a dedicated SAP team',
				'solucao_hero_corpo'       => 'Integrate SAP B1 with e-commerce, CRM and tax systems to grow your operation on an integration layer built for scaling companies.',
				'solucao_pilares_titulo'   => 'Scale your SAP Business One, connected',
				'solucao_pilares_1_titulo' => 'Use the native SAP B1 APIs',
				'solucao_pilares_1_desc'   => 'Connect systems through the REST Service Layer and the official DI API.',
				'solucao_pilares_2_titulo' => 'Simplify operations with no SAP team',
				'solucao_pilares_2_desc'   => 'Automate SAP B1 processes without depending on dedicated specialists.',
				'solucao_pilares_3_titulo' => 'Reuse your SAP integrations',
				'solucao_pilares_3_desc'   => 'Adapt components already used in S/4HANA projects for Business One.',
				'solucao_casos_titulo'     => 'Automate your SAP Business One processes',
				'solucao_casos_1_titulo'   => 'Integrate e-commerce orders',
				'solucao_casos_1_desc'     => 'Send digital orders to SAP B1 with no manual entry.',
				'solucao_casos_2_titulo'   => 'Automate tax processes',
				'solucao_casos_2_desc'     => 'Connect Brazilian tax document issuing to SAP Business One.',
				'solucao_casos_3_titulo'   => 'Consolidate multi-branch stock',
				'solucao_casos_3_desc'     => 'Centralise stock information across different units.',
				'solucao_casos_4_titulo'   => 'Connect CRM to ERP',
				'solucao_casos_4_desc'     => 'Synchronise sales across Salesforce, HubSpot and SAP B1.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make SAP B1 data available so agents can automate service and analysis.',
				'solucao_dif_titulo'       => 'Native integrations for SAP B1',
				'solucao_dif_corpo'        => 'Use the SAP Business One REST/OData Service Layer with secure authentication to connect enterprise applications.',
				'solucao_dif_topico_1'     => 'Use the official Service Layer',
				'solucao_dif_topico_2'     => 'Connect through REST and OData',
				'solucao_dif_topico_3'     => 'Protect authenticated sessions',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Get your SAP ready to grow',
				'solucao_plat_corpo'       => 'Keep the same integration layer as you move from SAP Business One to S/4HANA, or run different versions side by side.',
				'solucao_plat_topico_1'    => 'Reuse your SAP integrations',
				'solucao_plat_topico_2'    => 'Avoid future rebuilds',
				'solucao_plat_topico_3'    => 'Standardise enterprise architecture',
				'solucao_acel_titulo'      => 'Start with SAP Business One already connected',
				'solucao_acel_corpo'       => 'Use a ready-made template to integrate SAP B1 with e-commerce, CRM and tax systems through structured flows.',
				'solucao_acel_topico_1'    => 'Connect systems quickly',
				'solucao_acel_topico_2'    => 'Reuse SAP integrations',
				'solucao_acel_topico_3'    => 'Speed up new projects',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * SAP ECC.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_sap_ecc() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your SAP ECC',
				'solucao_hero_titulo'      => 'Connect your SAP ECC to the cloud without waiting for the S/4HANA migration',
				'solucao_hero_corpo'       => 'Modernise SAP ECC 6.0 connectivity by integrating SaaS, e-commerce and enterprise applications without replacing the current ERP.',
				'solucao_pilares_titulo'   => 'Modernise your SAP ECC while it stays in production',
				'solucao_pilares_1_titulo' => 'Build on native SAP integrations',
				'solucao_pilares_1_desc'   => 'Connect ECC through RFC, BAPI and IDoc without changing existing processes.',
				'solucao_pilares_2_titulo' => 'Protect your legacy environment',
				'solucao_pilares_2_desc'   => 'Connect external systems without exposing the on-premises ECC to the internet.',
				'solucao_pilares_3_titulo' => 'Connect modern applications',
				'solucao_pilares_3_desc'   => 'Integrate Salesforce, e-commerce and SaaS while ECC keeps running.',
				'solucao_casos_titulo'     => 'Connect your SAP ECC processes',
				'solucao_casos_1_titulo'   => 'Sync digital orders',
				'solucao_casos_1_desc'     => 'Integrate e-commerce with ECC to automate order entry.',
				'solucao_casos_2_titulo'   => 'Connect ECC to the CRM',
				'solucao_casos_2_desc'     => 'Synchronise commercial data across Salesforce, CRM and ERP.',
				'solucao_casos_3_titulo'   => 'Migrate without stopping the operation',
				'solucao_casos_3_desc'     => 'Run ECC and S/4HANA scenarios side by side during the transition.',
				'solucao_casos_4_titulo'   => 'Open up your ECC data',
				'solucao_casos_4_desc'     => 'Expose legacy information as modern APIs for other applications.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make ECC data available to AI agents without exposing the system core.',
				'solucao_dif_titulo'       => 'Secure connectivity for SAP ECC',
				'solucao_dif_corpo'        => 'Use RFC, BAPI and IDoc with a secure Runtime to integrate ECC without exposing legacy environments to the internet.',
				'solucao_dif_topico_1'     => 'Use native SAP protocols',
				'solucao_dif_topico_2'     => 'Protect on-premises connections',
				'solucao_dif_topico_3'     => 'Avoid external exposure',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Modernise ahead of S/4HANA',
				'solucao_plat_corpo'       => 'The same platform that will connect your future S/4HANA connects your current ECC, so the architecture keeps evolving with no rebuilds.',
				'solucao_plat_topico_1'    => 'Connect ECC today',
				'solucao_plat_topico_2'    => 'Prepare the future migration',
				'solucao_plat_topico_3'    => 'Reuse the integrations you already have',
				'solucao_acel_titulo'      => 'Start with SAP ECC already connected',
				'solucao_acel_corpo'       => 'Use a ready-made template to connect ECC to modern SaaS systems such as Salesforce and e-commerce platforms.',
				'solucao_acel_topico_1'    => 'Connect SaaS quickly',
				'solucao_acel_topico_2'    => 'Reuse SAP patterns',
				'solucao_acel_topico_3'    => 'Speed up modernisation',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}
	/**
	 * Oracle NetSuite.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_oracle_netsuite() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your NetSuite',
				'solucao_hero_titulo'      => 'Connect NetSuite to your stack without relying on SuiteScript alone',
				'solucao_hero_corpo'       => 'Integrate NetSuite with e-commerce, CRM and finance systems using native APIs, without excessive SuiteScript customisation.',
				'solucao_pilares_titulo'   => 'Scale your NetSuite, connected',
				'solucao_pilares_1_titulo' => 'Use the native NetSuite APIs',
				'solucao_pilares_1_desc'   => 'Integrate through SuiteTalk REST/SOAP and RESTlets without excessive custom code.',
				'solucao_pilares_2_titulo' => 'Simplify global operations',
				'solucao_pilares_2_desc'   => 'Manage multi-subsidiary integrations with standardised financial processes.',
				'solucao_pilares_3_titulo' => 'Cut back on SuiteScript customisation',
				'solucao_pilares_3_desc'   => 'Replace one-off scripts with reusable integrations that need no maintenance.',
				'solucao_casos_titulo'     => 'Automate your NetSuite processes',
				'solucao_casos_1_titulo'   => 'Sync digital orders',
				'solucao_casos_1_desc'     => 'Integrate e-commerce with NetSuite to automate order entry.',
				'solucao_casos_2_titulo'   => 'Consolidate global finances',
				'solucao_casos_2_desc'     => 'Synchronise financial data across subsidiaries automatically.',
				'solucao_casos_3_titulo'   => 'Connect CRM to finance',
				'solucao_casos_3_desc'     => 'Connect Salesforce to NetSuite to unify commercial and financial data.',
				'solucao_casos_4_titulo'   => 'Automate revenue recognition',
				'solucao_casos_4_desc'     => 'Process sales events in NetSuite with no manual intervention.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make NetSuite data available to AI agents without exposing the core.',
				'solucao_dif_titulo'       => 'Secure integrations for NetSuite',
				'solucao_dif_corpo'        => 'Use TBA and OAuth 2.0 to authenticate NetSuite integrations with enterprise-grade security, without exposing credentials.',
				'solucao_dif_topico_1'     => 'Use TBA and OAuth 2.0',
				'solucao_dif_topico_2'     => 'Protect corporate access',
				'solucao_dif_topico_3'     => 'Integrate through official APIs',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Scale multi-subsidiary operations',
				'solucao_plat_corpo'       => 'The same platform that connects one subsidiary replicates NetSuite OneWorld integrations across the whole group.',
				'solucao_plat_topico_1'    => 'Replicate global integrations',
				'solucao_plat_topico_2'    => 'Standardise financial processes',
				'solucao_plat_topico_3'    => 'Reduce one-off development',
				'solucao_acel_titulo'      => 'Start with NetSuite already integrated',
				'solucao_acel_corpo'       => 'Use a ready-made template to integrate NetSuite with e-commerce, CRM and finance systems through structured flows.',
				'solucao_acel_topico_1'    => 'Connect systems quickly',
				'solucao_acel_topico_2'    => 'Reuse NetSuite integrations',
				'solucao_acel_topico_3'    => 'Speed up new projects',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * Gemini.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_gemini() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'integrate your Gemini',
				'solucao_hero_titulo'      => 'Connect Gemini to your enterprise systems and data',
				'solucao_hero_corpo'       => 'Gemini reaches your data, orchestrates systems and takes action precisely — all of it integrated into how the company runs.',
				'solucao_pilares_titulo'   => 'Make Gemini part of the operation',
				'solucao_pilares_1_titulo' => 'Connect Gemini to your data',
				'solucao_pilares_1_desc'   => 'Bring information from enterprise systems to the model and generate answers based on the real context of the operation.',
				'solucao_pilares_2_titulo' => 'Orchestrate multiple applications',
				'solucao_pilares_2_desc'   => 'Combine Gemini with the ERP, CRM, databases and other applications in automated flows.',
				'solucao_pilares_3_titulo' => 'Query systems in natural language',
				'solucao_pilares_3_desc'   => 'Let teams find customer, order and operational information without moving between different systems.',
				'solucao_casos_titulo'     => 'Apply Gemini to your business processes',
				'solucao_casos_1_titulo'   => 'Query ERP data with AI',
				'solucao_casos_2_titulo'   => 'Analyse documents automatically',
				'solucao_casos_3_titulo'   => 'Automate customer service',
				'solucao_casos_4_titulo'   => 'Classify requests',
				'solucao_casos_5_titulo'   => 'Generate operational analysis',
				'solucao_diagrama_titulo'  => 'A new way to connect AI to your systems',
				'solucao_int_eyebrow'      => 'integrations',
				'solucao_int_titulo'       => 'Integrate all of your systems with Gemini',
				'solucao_int_subtitulo'    => 'Thousands of integrations ready to use',
				'solucao_dif_titulo'       => 'Integrate AI with control over your data',
				'solucao_dif_corpo'        => 'Connect Gemini to the company systems with control over data, access and actions, to scale artificial intelligence without losing governance.',
				'solucao_dif_topico_1'     => 'Control which data reaches the models',
				'solucao_dif_topico_2'     => 'Protect data in transit and at rest',
				'solucao_dif_topico_3'     => 'Apply rules before running actions',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Centralise AI and integrations on one platform',
				'solucao_plat_corpo'       => 'Avoid building isolated connections for every use case. Centralise Gemini, systems and processes to scale new agents on the same architecture.',
				'solucao_plat_topico_1'    => 'Connect Gemini to multiple systems',
				'solucao_plat_topico_2'    => 'Reuse connections in new agents',
				'solucao_plat_topico_3'    => 'Orchestrate AI inside the processes',
				'solucao_acel_eyebrow'     => 'MCP server',
				'solucao_acel_titulo'      => 'Give Gemini tools without having to develop APIs',
				'solucao_acel_corpo'       => 'Turn enterprise processes into Tools for Gemini, defining exactly which information it can query and which actions it can run.',
				'solucao_acel_topico_1'    => 'Turn processes into AI tools',
				'solucao_acel_topico_2'    => 'Control inputs, outputs and information',
				'solucao_acel_topico_3'    => 'Expose it all through the MCP Server',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * Microsoft Azure.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_microsoft_azure() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Azure',
				'solucao_hero_titulo'      => 'Speed up Azure adoption while keeping your core connected',
				'solucao_hero_corpo'       => 'Integrate Azure services, SAP, Salesforce and legacy systems on one platform to evolve your cloud architecture without interrupting the operations you already run.',
				'solucao_pilares_titulo'   => 'Evolve your Microsoft architecture at scale',
				'solucao_pilares_1_titulo' => 'Connect Azure services natively',
				'solucao_pilares_1_desc'   => 'Use ready-made connectors for Azure data and messaging.',
				'solucao_pilares_2_titulo' => 'Speed up real-time events',
				'solucao_pilares_2_desc'   => 'Adopt event-driven architectures without rebuilding integrations.',
				'solucao_pilares_3_titulo' => 'Integrate the Microsoft ecosystem',
				'solucao_pilares_3_desc'   => 'Connect Azure, Dynamics 365, Teams and Azure AD.',
				'solucao_casos_titulo'     => 'Automate processes connected to Azure',
				'solucao_casos_1_titulo'   => 'Capture events in real time',
				'solucao_casos_1_desc'     => 'Send business events to analytics using Event Hubs.',
				'solucao_casos_2_titulo'   => 'Decouple systems with queues',
				'solucao_casos_2_desc'     => 'Use Service Bus to connect legacy systems and new services.',
				'solucao_casos_3_titulo'   => 'Store data with low latency',
				'solucao_casos_3_desc'     => 'Use CosmosDB for high-performance global scenarios.',
				'solucao_casos_4_titulo'   => 'Automate corporate files',
				'solucao_casos_4_desc'     => 'Process documents using Blob Storage and DataLake.',
				'solucao_casos_5_titulo'   => 'Centralise secrets management',
				'solucao_casos_5_desc'     => 'Protect integration credentials with Azure Key Vault.',
				'solucao_casos_cta_texto'  => 'Talk to a specialist',
				'solucao_dif_titulo'       => 'Azure integrations with native security',
				'solucao_dif_corpo'        => 'Connect Azure services using OAuth2, Azure AD and Key Vault to control access and protect credentials across every flow.',
				'solucao_dif_topico_1'     => 'Authenticate through Azure AD',
				'solucao_dif_topico_2'     => 'Protect secrets with Key Vault',
				'solucao_dif_topico_3'     => 'Control access centrally',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Connect the whole Microsoft ecosystem',
				'solucao_plat_corpo'       => 'Centralise integrations between Azure, Microsoft applications and enterprise systems to speed up new initiatives with no added complexity.',
				'solucao_plat_topico_1'    => 'Integrate data and applications',
				'solucao_plat_topico_2'    => 'Reuse existing pipelines',
				'solucao_plat_topico_3'    => 'Evolve the architecture gradually',
				'solucao_acel_titulo'      => 'Start with Azure events already structured',
				'solucao_acel_corpo'       => 'Use a ready-made template to connect business events to Event Hubs and Service Bus, speeding up your event-driven architecture.',
				'solucao_acel_topico_1'    => 'Set events up quickly',
				'solucao_acel_topico_2'    => 'Cut back on custom development',
				'solucao_acel_topico_3'    => 'Speed up cloud adoption',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * Databricks.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_databricks() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Databricks',
				'solucao_hero_titulo'      => 'Connect Databricks to the core of the business with data always ready for AI',
				'solucao_hero_corpo'       => 'Integrate Databricks with your transactional, ERP and CRM systems to feed machine learning models in real time and turn enterprise data into intelligent decisions.',
				'solucao_pilares_titulo'   => 'Get data ready for advanced intelligence',
				'solucao_pilares_1_titulo' => 'Ingest data continuously',
				'solucao_pilares_1_desc'   => 'Connect operational systems to Databricks in real time.',
				'solucao_pilares_2_titulo' => 'Feed AI models',
				'solucao_pilares_2_desc'   => 'Make current data available to machine learning and intelligent agents.',
				'solucao_pilares_3_titulo' => 'Turn predictions into actions',
				'solucao_pilares_3_desc'   => 'Send analytical results back to the ERP and CRM automatically.',
				'solucao_casos_titulo'     => 'Apply intelligence with connected data',
				'solucao_casos_1_titulo'   => 'Train predictive models',
				'solucao_casos_1_desc'     => 'Use ERP and CRM data to predict churn, demand and risk.',
				'solucao_casos_2_titulo'   => 'Give context to AI agents',
				'solucao_casos_2_desc'     => 'Feed intelligent agents with current enterprise information.',
				'solucao_casos_3_titulo'   => 'Send scores to the systems',
				'solucao_casos_3_desc'     => 'Return model results to support operational decisions.',
				'solucao_casos_4_titulo'   => 'Consolidate analytical data',
				'solucao_casos_4_desc'     => 'Bring multiple sources together for advanced enterprise analysis.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make enterprise data available to AI agents without exposing the core of the systems.',
				'solucao_dif_titulo'       => 'Data prepared for AI, securely',
				'solucao_dif_corpo'        => 'Connect Databricks through APIs and Delta Sharing while keeping secure authentication, governance and protection of the sensitive data the models use.',
				'solucao_dif_topico_1'     => 'Use the official Databricks APIs',
				'solucao_dif_topico_2'     => 'Protect sensitive data',
				'solucao_dif_topico_3'     => 'Control access by token',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Connect data and decisions on one platform',
				'solucao_plat_corpo'       => 'Centralise the connection between operational systems, Databricks and business applications to close the loop between data and action.',
				'solucao_plat_topico_1'    => 'Integrate enterprise data',
				'solucao_plat_topico_2'    => 'Reuse existing pipelines',
				'solucao_plat_topico_3'    => 'Apply AI across the processes',
				'solucao_acel_titulo'      => 'Start with AI flows already structured',
				'solucao_acel_corpo'       => 'Use a ready-made template to take data into Databricks, generate analytical results and return actions to the enterprise systems.',
				'solucao_acel_topico_1'    => 'Connect data quickly',
				'solucao_acel_topico_2'    => 'Speed up model training',
				'solucao_acel_topico_3'    => 'Automate intelligent actions',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * Snowflake.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_snowflake() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Snowflake',
				'solucao_hero_titulo'      => 'Connect Snowflake to the core of the business with data always ready for analysis',
				'solucao_hero_corpo'       => 'Integrate Snowflake with your transactional, CRM and ERP systems to feed analytical pipelines in real time and clear the data silos that hold strategic decisions back.',
				'solucao_pilares_titulo'   => 'Unified data, faster decisions',
				'solucao_pilares_1_titulo' => 'Continuous data ingestion',
				'solucao_pilares_1_desc'   => 'Feed Snowflake with data from the ERP, CRM and legacy systems in an automated, reliable way.',
				'solucao_pilares_2_titulo' => 'Transformations with no extra code',
				'solucao_pilares_2_desc'   => 'Process, normalise and enrich data before loading it into Snowflake using Boomi visual flows.',
				'solucao_pilares_3_titulo' => 'Centralised governance',
				'solucao_pilares_3_desc'   => 'Control which data reaches Snowflake, with source traceability and compliance with LGPD and GDPR.',
				'solucao_casos_titulo'     => 'Turn data into competitive advantage',
				'solucao_casos_1_titulo'   => 'Sync the ERP with the Data Cloud',
				'solucao_casos_1_desc'     => 'Move financial and operational transactions from the ERP to Snowflake in real time for up-to-date analysis.',
				'solucao_casos_2_titulo'   => 'Unify CRM data',
				'solucao_casos_2_desc'     => 'Consolidate leads, opportunities and customer history in Snowflake for a 360° view of the sales pipeline.',
				'solucao_casos_3_titulo'   => 'Automate marketing pipelines',
				'solucao_casos_3_desc'     => 'Feed attribution and segmentation models with campaign data centralised in Snowflake.',
				'solucao_casos_4_titulo'   => 'Integrate e-commerce data',
				'solucao_casos_4_desc'     => 'Send orders, returns and browsing behaviour to Snowflake and feed sales dashboards in real time.',
				'solucao_casos_5_titulo'   => 'Feed AI agents',
				'solucao_casos_5_desc'     => 'Make structured Snowflake data available to machine learning models and AI agents that automate operational decisions.',
				'solucao_dif_titulo'       => 'Native integration with the Snowflake Data Cloud',
				'solucao_dif_corpo'        => 'Connect Snowflake using the certified Boomi connector with support for OAuth 2.0 and key-pair authentication, for maximum security in moving data.',
				'solucao_dif_topico_1'     => 'Certified Boomi connector for Snowflake',
				'solucao_dif_topico_2'     => 'OAuth 2.0 and key-pair authentication',
				'solucao_dif_topico_3'     => 'Support for bulk load and streaming',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'A central hub for all of your data',
				'solucao_plat_corpo'       => 'With Boomi as the integration layer, you connect any system to Snowflake with no custom ETL scripts, speeding up the delivery of insight and reducing the technical debt of fragmented pipelines.',
				'solucao_plat_topico_1'    => 'Clear out fragmented ETL pipelines',
				'solucao_plat_topico_2'    => 'Connect any system to Snowflake',
				'solucao_plat_topico_3'    => 'Speed up time-to-insight for the data team',
				'solucao_acel_titulo'      => 'Start ingesting data into Snowflake today',
				'solucao_acel_corpo'       => 'Use ready-made templates to connect the ERP, CRM and operational systems to Snowflake with structured flows and end-to-end traceability.',
				'solucao_acel_topico_1'    => 'Connect the ERP and CRM quickly',
				'solucao_acel_topico_2'    => 'Reuse data pipelines',
				'solucao_acel_topico_3'    => 'Speed up Data Cloud projects',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * Microsoft Teams.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_microsoft_teams() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Microsoft Teams',
				'solucao_hero_titulo'      => 'Turn Microsoft Teams into a channel for taking action on processes',
				'solucao_hero_corpo'       => 'Connect approvals, notifications and AI agents to the internal systems to speed up decisions without depending on email or manual processes.',
				'solucao_pilares_titulo'   => 'Bring processes to where the teams already work',
				'solucao_pilares_1_titulo' => 'Approve processes in Teams',
				'solucao_pilares_1_desc'   => 'Run approvals straight from adaptive cards.',
				'solucao_pilares_2_titulo' => 'Talk to the internal systems',
				'solucao_pilares_2_desc'   => 'Let bots query enterprise data inside Teams.',
				'solucao_pilares_3_titulo' => 'Cut back on manual handovers',
				'solucao_pilares_3_desc'   => 'Replace email with automated actions.',
				'solucao_casos_titulo'     => 'Automate processes inside Teams',
				'solucao_casos_1_titulo'   => 'Approve requests in Teams',
				'solucao_casos_1_desc'     => 'Send purchase or holiday approvals to the ERP.',
				'solucao_casos_2_titulo'   => 'Raise incident alerts automatically',
				'solucao_casos_2_desc'     => 'Notify teams about ServiceNow or Freshservice events.',
				'solucao_casos_3_titulo'   => 'Query systems with AI',
				'solucao_casos_3_desc'     => 'Let bots look up stock and orders.',
				'solucao_casos_4_titulo'   => 'Monitor critical events',
				'solucao_casos_4_desc'     => 'Trigger alerts for SLAs and important operations.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make enterprise data available to AI agents through Teams using governed APIs and MCP Servers.',
				'solucao_dif_titulo'       => 'Secure integrations with Microsoft Teams',
				'solucao_dif_corpo'        => 'Connect Teams through the Microsoft Graph API and Bot Framework using Azure AD authentication, with control by team and channel.',
				'solucao_dif_topico_1'     => 'Use the Microsoft Graph API.',
				'solucao_dif_topico_2'     => 'Authenticate through Azure AD.',
				'solucao_dif_topico_3'     => 'Control access by channel.',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Connect communication and operation',
				'solucao_plat_corpo'       => 'Centralise business events on one platform to bring teams closer to the enterprise systems.',
				'solucao_plat_topico_1'    => 'Integrate the internal systems.',
				'solucao_plat_topico_2'    => 'Centralise operational notifications.',
				'solucao_plat_topico_3'    => 'Automate actions in Teams.',
				'solucao_acel_titulo'      => 'Start with processes already connected',
				'solucao_acel_corpo'       => 'Use a ready-made template to turn enterprise processes into approvals and notifications inside Teams.',
				'solucao_acel_topico_1'    => 'Set flows up quickly.',
				'solucao_acel_topico_2'    => 'Reuse approved templates.',
				'solucao_acel_topico_3'    => 'Speed up operational decisions.',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * Propz.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_propz() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Propz',
				'solucao_hero_titulo'      => 'Connect Propz intelligence to your company data',
				'solucao_hero_corpo'       => 'Integrate the POS, e-commerce and ERP to feed retail personalisation with current data and activate offers on the right channel.',
				'solucao_pilares_titulo'   => 'Turn data into personalised experiences',
				'solucao_pilares_1_titulo' => 'Feed data in real time',
				'solucao_pilares_1_desc'   => 'Connect POS, e-commerce and ERP sales to Propz.',
				'solucao_pilares_2_titulo' => 'Activate offers automatically',
				'solucao_pilares_2_desc'   => 'Send personalised campaigns to the digital channels.',
				'solucao_pilares_3_titulo' => 'Centralise purchase history',
				'solucao_pilares_3_desc'   => 'Unify multichannel data to understand consumers.',
				'solucao_casos_titulo'     => 'Automate personalisation processes',
				'solucao_casos_1_titulo'   => 'Send sales to Propz',
				'solucao_casos_1_desc'     => 'Update consumer intelligence with sales data.',
				'solucao_casos_2_titulo'   => 'Distribute personalised offers',
				'solucao_casos_2_desc'     => 'Activate Propz campaigns in app, SMS and email.',
				'solucao_casos_3_titulo'   => 'Consolidate multichannel purchases',
				'solucao_casos_3_desc'     => 'Unify history for customer segmentation.',
				'solucao_casos_4_titulo'   => 'Measure campaign results',
				'solucao_casos_4_desc'     => 'Return campaign data to the CRM and ERP.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make consumer data available to AI agents using governed APIs and MCP Servers.',
				'solucao_dif_titulo'       => 'Secure integrations for customer data',
				'solucao_dif_corpo'        => 'Connect Propz through the REST API with data governance and controls aligned to LGPD requirements.',
				'solucao_dif_topico_1'     => 'Protect consumer data.',
				'solucao_dif_topico_2'     => 'Control access per integration.',
				'solucao_dif_topico_3'     => 'Govern data in line with LGPD.',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Connect data and personalisation',
				'solucao_plat_corpo'       => 'Centralise data going in and out between Propz, the digital channels and the internal systems with no manual processes.',
				'solucao_plat_topico_1'    => 'Integrate activation channels.',
				'solucao_plat_topico_2'    => 'Centralise commercial data.',
				'solucao_plat_topico_3'    => 'Automate personalised journeys.',
				'solucao_acel_titulo'      => 'Start with personalised retail',
				'solucao_acel_corpo'       => 'Use a ready-made template to connect sales, Propz and the activation channels in one complete flow.',
				'solucao_acel_topico_1'    => 'Connect data quickly.',
				'solucao_acel_topico_2'    => 'Reuse campaign flows.',
				'solucao_acel_topico_3'    => 'Speed up commercial personalisation.',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * Onclick ERP.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_onclick_erp() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Onclick',
				'solucao_hero_titulo'      => 'Connect the Onclick ERP to e-commerce, manufacturing and distribution',
				'solucao_hero_corpo'       => 'Integrate retail, marketplaces, sales and tax processes to keep stock, orders and operations in sync across every channel.',
				'solucao_pilares_titulo'   => 'Get more out of Onclick',
				'solucao_pilares_1_titulo' => 'Connect every module',
				'solucao_pilares_1_desc'   => 'Integrate retail, e-commerce, manufacturing, distribution and services.',
				'solucao_pilares_2_titulo' => 'Sync omnichannel stock',
				'solucao_pilares_2_desc'   => 'Keep physical stores and digital channels always up to date.',
				'solucao_pilares_3_titulo' => 'Centralise tax processes',
				'solucao_pilares_3_desc'   => 'Integrate tax and accounting information automatically.',
				'solucao_casos_titulo'     => 'Automate processes with Onclick',
				'solucao_casos_1_titulo'   => 'Sync digital orders',
				'solucao_casos_1_desc'     => 'Send e-commerce orders straight into the ERP.',
				'solucao_casos_2_titulo'   => 'Integrate marketplaces',
				'solucao_casos_2_desc'     => 'Centralise stock and sales from multiple channels.',
				'solucao_casos_3_titulo'   => 'Automate the sales force',
				'solucao_casos_3_desc'     => 'Connect mobile reps to the ERP processes.',
				'solucao_casos_4_titulo'   => 'Consolidate service orders',
				'solucao_casos_4_desc'     => 'Centralise service operations in a single flow.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make ERP data available to AI agents using governed APIs and MCP Servers.',
				'solucao_dif_titulo'       => 'Integrations shaped around Onclick',
				'solucao_dif_corpo'        => 'Connect the different Onclick modules with an architecture prepared for retail, manufacturing, distribution and services.',
				'solucao_dif_topico_1'     => 'Integrate specialised modules.',
				'solucao_dif_topico_2'     => 'Adapt operational flows.',
				'solucao_dif_topico_3'     => 'Connect multiple channels.',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Unify your omnichannel operation',
				'solucao_plat_corpo'       => 'Centralise stores, e-commerce and marketplaces in a single integration layer to avoid out-of-date stock.',
				'solucao_plat_topico_1'    => 'Sync the sales channels.',
				'solucao_plat_topico_2'    => 'Centralise commercial data.',
				'solucao_plat_topico_3'    => 'Avoid disconnected processes.',
				'solucao_acel_titulo'      => 'Start with e-commerce already integrated',
				'solucao_acel_corpo'       => 'Use a ready-made template to connect Onclick to the main digital channels and marketplaces.',
				'solucao_acel_topico_1'    => 'Connect channels quickly.',
				'solucao_acel_topico_2'    => 'Reuse commercial flows.',
				'solucao_acel_topico_3'    => 'Speed up new integrations.',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * Viasoft.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_viasoft() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Viasoft',
				'solucao_hero_titulo'      => 'Connect Viasoft to the rest of the operation',
				'solucao_hero_corpo'       => 'Integrate ERPs specialised in agribusiness, fuels and manufacturing with banks, tax and commercial systems to unify processes.',
				'solucao_pilares_titulo'   => 'Get more out of Viasoft',
				'solucao_pilares_1_titulo' => 'Connect specialised verticals',
				'solucao_pilares_1_desc'   => 'Integrate Agrotitan, Filt IA+ and Viasoft solutions by segment.',
				'solucao_pilares_2_titulo' => 'Automate tax processes',
				'solucao_pilares_2_desc'   => 'Connect tax obligations according to each business vertical.',
				'solucao_pilares_3_titulo' => 'Integrate financial operations',
				'solucao_pilares_3_desc'   => 'Synchronise banks and financial processes automatically.',
				'solucao_casos_titulo'     => 'Automate the Viasoft processes',
				'solucao_casos_1_titulo'   => 'Integrate sales with finance',
				'solucao_casos_1_desc'     => 'Synchronise agricultural sales and commercial operations with finance.',
				'solucao_casos_2_titulo'   => 'Automate tax processes',
				'solucao_casos_2_desc'     => 'Connect NF-e and SPED according to each segment.',
				'solucao_casos_3_titulo'   => 'Reconcile banking operations',
				'solucao_casos_3_desc'     => 'Automate reconciliation for dealers and cooperatives.',
				'solucao_casos_4_titulo'   => 'Consolidate operational data',
				'solucao_casos_4_desc'     => 'Unify multi-site information for strategic analysis.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make operational data available to intelligent agents without exposing the Viasoft core.',
				'solucao_dif_titulo'       => 'Integrations shaped around your segment',
				'solucao_dif_corpo'        => 'Connect operations with the specific tax and regulatory rules for agribusiness, fuels and the other verticals Viasoft serves.',
				'solucao_dif_topico_1'     => 'Adapt integrations by vertical',
				'solucao_dif_topico_2'     => 'Meet specific regulatory rules',
				'solucao_dif_topico_3'     => 'Connect specialised operations',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Unify the different Viasoft verticals',
				'solucao_plat_corpo'       => 'Centralise financial and tax data from different operations in a single integration layer.',
				'solucao_plat_topico_1'    => 'Integrate multiple businesses',
				'solucao_plat_topico_2'    => 'Centralise enterprise information',
				'solucao_plat_topico_3'    => 'Avoid isolated integrations',
				'solucao_acel_titulo'      => 'Start with operations already connected',
				'solucao_acel_corpo'       => 'Use a ready-made template to connect Viasoft to finance and tax quickly.',
				'solucao_acel_topico_1'    => 'Connect data quickly',
				'solucao_acel_topico_2'    => 'Reuse validated flows',
				'solucao_acel_topico_3'    => 'Speed up new integrations',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * RP Info.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_rp_info() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your RP Info',
				'solucao_hero_titulo'      => 'Connect RP Info from the checkout to the distribution centre',
				'solucao_hero_corpo'       => 'Integrate the store front, ERP, suppliers and BI to synchronise retail sales, stock and operations in real time.',
				'solucao_pilares_titulo'   => 'Scale your retail, connected',
				'solucao_pilares_1_titulo' => 'Connect the retail operations',
				'solucao_pilares_1_desc'   => 'Integrate Flex ERP, RPDV, Mix, Target and Task into the commercial ecosystem.',
				'solucao_pilares_2_titulo' => 'Sync sales in real time',
				'solucao_pilares_2_desc'   => 'Connect checkout transactions to the ERP with no manual processes.',
				'solucao_pilares_3_titulo' => 'Integrate suppliers through EDI',
				'solucao_pilares_3_desc'   => 'Automate the exchange of data with trading partners.',
				'solucao_casos_titulo'     => 'Automate the RP Info retail processes',
				'solucao_casos_1_titulo'   => 'Sync POS sales',
				'solucao_casos_1_desc'     => 'Update RPDV sales in Flex ERP in real time.',
				'solucao_casos_2_titulo'   => 'Connect suppliers through EDI',
				'solucao_casos_2_desc'     => 'Automate orders and information with trading partners.',
				'solucao_casos_3_titulo'   => 'Consolidate multi-store sales',
				'solucao_casos_3_desc'     => 'Centralise results from different sites for analysis.',
				'solucao_casos_4_titulo'   => 'Centralise tax processes',
				'solucao_casos_4_desc'     => 'Integrate SPED and NF-e into the enterprise processes.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make operational data available to intelligent agents without exposing the RP Info core.',
				'solucao_dif_titulo'       => 'Integrations for retail at scale',
				'solucao_dif_corpo'        => 'Connect operations with thousands of checkouts using an architecture prepared for high transaction volume.',
				'solucao_dif_topico_1'     => 'Process sales at scale',
				'solucao_dif_topico_2'     => 'Synchronise data quickly',
				'solucao_dif_topico_3'     => 'Support multiple checkouts',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Unify the retail data',
				'solucao_plat_corpo'       => 'Centralise sales, stock and suppliers in a single integration layer without depending on batch processes.',
				'solucao_plat_topico_1'    => 'Consolidate sales in real time',
				'solucao_plat_topico_2'    => 'Centralise operational data',
				'solucao_plat_topico_3'    => 'Cut back on manual processes',
				'solucao_acel_titulo'      => 'Start with retail already integrated',
				'solucao_acel_corpo'       => 'Use a ready-made template to connect RP Info to supplier EDI and analytics platforms.',
				'solucao_acel_topico_1'    => 'Connect suppliers quickly',
				'solucao_acel_topico_2'    => 'Reuse retail flows',
				'solucao_acel_topico_3'    => 'Speed up new integrations',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * QAD Redzone.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_qad_redzone() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your QAD Redzone',
				'solucao_hero_titulo'      => 'Connect QAD Redzone to the ERP and the shop floor in real time',
				'solucao_hero_corpo'       => 'Integrate line productivity, manufacturing and quality with QAD ERP and enterprise BI to turn operational data into fast decisions.',
				'solucao_pilares_titulo'   => 'Turn factory data into value',
				'solucao_pilares_1_titulo' => 'Monitor productivity in real time',
				'solucao_pilares_1_desc'   => 'Synchronise OEE and line performance data automatically.',
				'solucao_pilares_2_titulo' => 'Integrate with QAD ERP',
				'solucao_pilares_2_desc'   => 'Connect shop-floor execution to the enterprise processes in the ERP.',
				'solucao_pilares_3_titulo' => 'Connect the factory and BI',
				'solucao_pilares_3_desc'   => 'Take operational data into strategic enterprise analysis.',
				'solucao_casos_titulo'     => 'Automate the manufacturing processes',
				'solucao_casos_1_titulo'   => 'Integrate OEE with the ERP',
				'solucao_casos_1_desc'     => 'Send Redzone productivity indicators to QAD ERP.',
				'solucao_casos_2_titulo'   => 'Control quality end to end',
				'solucao_casos_2_desc'     => 'Connect non-conformities to the quality processes.',
				'solucao_casos_3_titulo'   => 'Consolidate multi-plant production',
				'solucao_casos_3_desc'     => 'Centralise industrial data from different production sites.',
				'solucao_casos_4_titulo'   => 'Raise alerts for line stoppages',
				'solucao_casos_4_desc'     => 'Trigger real-time alerts for preventive maintenance.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make operational data available to intelligent agents without exposing the Redzone core.',
				'solucao_dif_titulo'       => 'Integrations for real-time manufacturing',
				'solucao_dif_corpo'        => 'Process large volumes of industrial data with connectivity prepared for sensors and continuous production operations.',
				'solucao_dif_topico_1'     => 'Process data at high volume',
				'solucao_dif_topico_2'     => 'Connect industrial events',
				'solucao_dif_topico_3'     => 'Follow production in real time',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Connect your whole industrial operation',
				'solucao_plat_corpo'       => 'Centralise shop-floor, ERP and BI data to clear out isolated information and get more value from Redzone.',
				'solucao_plat_topico_1'    => 'Integrate factory and office',
				'solucao_plat_topico_2'    => 'Centralise production data',
				'solucao_plat_topico_3'    => 'Widen operational visibility',
				'solucao_acel_titulo'      => 'Start with manufacturing connected',
				'solucao_acel_corpo'       => 'Use a ready-made template to connect QAD Redzone to QAD ERP and enterprise analytics platforms.',
				'solucao_acel_topico_1'    => 'Connect data quickly',
				'solucao_acel_topico_2'    => 'Reuse industrial patterns',
				'solucao_acel_topico_3'    => 'Speed up factory projects',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * IFS Cloud.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_ifs_cloud() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your IFS Cloud',
				'solucao_hero_titulo'      => 'Connect IFS Cloud to the rest of the industrial operation',
				'solucao_hero_corpo'       => 'Integrate the ERP, asset management and field service with MES, IoT and enterprise systems to turn operational data into faster decisions.',
				'solucao_pilares_titulo'   => 'Get more out of IFS Cloud',
				'solucao_pilares_1_titulo' => 'Use the native IFS APIs',
				'solucao_pilares_1_desc'   => 'Connect systems using the official IFS Cloud REST API.',
				'solucao_pilares_2_titulo' => 'Connect industrial assets',
				'solucao_pilares_2_desc'   => 'Integrate maintenance, sensors and operational data in real time.',
				'solucao_pilares_3_titulo' => 'Scale field service',
				'solucao_pilares_3_desc'   => 'Connect field teams, the CRM and the service processes.',
				'solucao_casos_titulo'     => 'Automate processes with IFS Cloud',
				'solucao_casos_1_titulo'   => 'Integrate maintenance and IoT',
				'solucao_casos_1_desc'     => 'Connect EAM work orders to sensors and industrial data.',
				'solucao_casos_2_titulo'   => 'Connect field service to the CRM',
				'solucao_casos_2_desc'     => 'Synchronise field visits with the commercial processes.',
				'solucao_casos_3_titulo'   => 'Consolidate financial data',
				'solucao_casos_3_desc'     => 'Integrate IFS and the enterprise ERP for a single financial view.',
				'solucao_casos_4_titulo'   => 'Expose data to AI',
				'solucao_casos_4_desc'     => 'Make assets available as tools for intelligent agents.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make operational data available to administrative agents without exposing the IFS core.',
				'solucao_dif_titulo'       => 'Secure integrations for IFS Cloud',
				'solucao_dif_corpo'        => 'Connect enterprise applications using OAuth2 authentication through the IFS Cloud REST API, with security and control.',
				'solucao_dif_topico_1'     => 'Use secure OAuth2',
				'solucao_dif_topico_2'     => 'Connect official APIs',
				'solucao_dif_topico_3'     => 'Protect industrial data',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Centralise industrial asset data',
				'solucao_plat_corpo'       => 'Connect maintenance, the ERP and artificial intelligence in a single layer without changing the IFS Cloud core.',
				'solucao_plat_topico_1'    => 'Integrate enterprise systems',
				'solucao_plat_topico_2'    => 'Avoid customising IFS',
				'solucao_plat_topico_3'    => 'Scale industrial operations',
				'solucao_acel_titulo'      => 'Start with assets already connected',
				'solucao_acel_corpo'       => 'Use a ready-made template to connect IFS EAM/FSM to the enterprise ERP and IoT platforms.',
				'solucao_acel_topico_1'    => 'Connect IoT quickly',
				'solucao_acel_topico_2'    => 'Reuse industrial flows',
				'solucao_acel_topico_3'    => 'Speed up new projects',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * CISS Poder ERP.
	 *
	 * O caso de uso 5 fala de dados assistenciais também no português —
	 * resíduo do seed original, mantido aqui para os dois idiomas dizerem
	 * o mesmo.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_ciss_poder_erp() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your CISS',
				'solucao_hero_titulo'      => 'Connect CISSPoder to the whole retail operation',
				'solucao_hero_corpo'       => 'Integrate purchasing, stock, POS, e-commerce, suppliers and finance to keep the entire retail operation in sync in real time.',
				'solucao_pilares_titulo'   => 'Integrate the whole retail operation',
				'solucao_pilares_1_titulo' => 'Sync sales and stock',
				'solucao_pilares_1_desc'   => 'Connect the POS, e-commerce and marketplaces to CISSPoder to keep sales and stock up to date.',
				'solucao_pilares_2_titulo' => 'Connect suppliers',
				'solucao_pilares_2_desc'   => 'Automate supplier orders, invoices and information through EDI, cutting back on manual entry.',
				'solucao_pilares_3_titulo' => 'Integrate purchasing and replenishment',
				'solucao_pilares_3_desc'   => 'Take sales and stock data into more efficient purchasing and replenishment processes.',
				'solucao_casos_titulo'     => 'Automate the retail processes',
				'solucao_casos_1_titulo'   => 'Sync POS sales',
				'solucao_casos_1_desc'     => 'Take store sales into CISSPoder in real time and keep the operation up to date.',
				'solucao_casos_2_titulo'   => 'Connect the e-commerce',
				'solucao_casos_2_desc'     => 'Integrate orders and stock between CISSPoder and the digital sales channels.',
				'solucao_casos_3_titulo'   => 'Integrate suppliers through EDI',
				'solucao_casos_3_desc'     => 'Automate the receipt of orders and documents sent by suppliers.',
				'solucao_casos_4_titulo'   => 'Automate replenishment',
				'solucao_casos_4_desc'     => 'Connect sales, stock and supply to speed up replenishment orders.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make care data available to administrative agents without exposing the clinical core.',
				'solucao_dif_titulo'       => 'Integrations for high-volume operations',
				'solucao_dif_corpo'        => 'Connect CISSPoder to the systems that hold your operation up, keeping sales, stock and purchasing data in sync even across chains with many stores.',
				'solucao_dif_topico_1'     => 'Process large volumes of transactions',
				'solucao_dif_topico_2'     => 'Synchronise data in real time',
				'solucao_dif_topico_3'     => 'Connect multiple stores and systems',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'A connected retail operation',
				'solucao_plat_corpo'       => 'CISSPoder already centralises retail management. CLI Connect widens that reach by connecting the ERP to the systems that make up the operation.',
				'solucao_plat_topico_1'    => 'Connect the POS and e-commerce',
				'solucao_plat_topico_2'    => 'Integrate suppliers and the WMS',
				'solucao_plat_topico_3'    => 'Centralise data across stores',
				'solucao_acel_titulo'      => 'Start with a ready-made integration',
				'solucao_acel_corpo'       => 'Use a ready-made template to connect CISSPoder to the main systems in the retail operation and speed up the rollout.',
				'solucao_acel_topico_1'    => 'Connect the POS and e-commerce',
				'solucao_acel_topico_2'    => 'Automate supplier integrations',
				'solucao_acel_topico_3'    => 'Reuse flows across stores',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * Arius ERP.
	 *
	 * O hero fala de varejo enquanto o resto da landing fala de indústria —
	 * resíduo do seed original, mantido aqui para os dois idiomas dizerem
	 * o mesmo.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_arius_erp() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Arius',
				'solucao_hero_titulo'      => 'Synchronise Arius ERP with your whole sales ecosystem',
				'solucao_hero_corpo'       => 'Integrate store management, the POS and the financial back office to clear out manual controls and get full visibility over your retail in real time.',
				'solucao_pilares_titulo'   => 'Scale your industrial operation, connected',
				'solucao_pilares_1_titulo' => 'Connect industrial systems',
				'solucao_pilares_1_desc'   => 'Integrate Arius ERP with MES and shop-floor applications.',
				'solucao_pilares_2_titulo' => 'Automate production and management',
				'solucao_pilares_2_desc'   => 'Synchronise production orders and operational data automatically.',
				'solucao_pilares_3_titulo' => 'Cut back on manual controls',
				'solucao_pilares_3_desc'   => 'Replace spreadsheets with processes connected across areas.',
				'solucao_casos_titulo'     => 'Automate industrial processes with Arius',
				'solucao_casos_1_titulo'   => 'Connect production to the ERP',
				'solucao_casos_1_desc'     => 'Synchronise production orders between MES and Arius.',
				'solucao_casos_2_titulo'   => 'Integrate tax processes',
				'solucao_casos_2_desc'     => 'Automate tax document issuing and ERP financial data.',
				'solucao_casos_3_titulo'   => 'Consolidate industrial stock',
				'solucao_casos_3_desc'     => 'Centralise stock information across multiple plants.',
				'solucao_casos_4_titulo'   => 'Connect the CRM to Arius',
				'solucao_casos_4_desc'     => 'Integrate commercial orders with industrial planning.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make industrial data available to administrative agents without exposing the operational core.',
				'solucao_dif_titulo'       => 'Dedicated integrations for Arius ERP',
				'solucao_dif_corpo'        => 'Use connectors adapted to the Arius protocol, deployed inside the customer environment for greater operational control.',
				'solucao_dif_topico_1'     => 'Use dedicated connectors',
				'solucao_dif_topico_2'     => 'Deploy in the internal environment',
				'solucao_dif_topico_3'     => 'Control industrial integrations',
				'solucao_plat_titulo'      => 'Connect your industry as it evolves',
				'solucao_plat_corpo'       => 'Centralise integrations between Arius, MES, the CRM and new systems without depending on developers specialised in the ERP.',
				'solucao_plat_topico_1'    => 'Depend less on technical resources',
				'solucao_plat_topico_2'    => 'Centralise new systems',
				'solucao_plat_topico_3'    => 'Scale industrial processes',
				'solucao_acel_titulo'      => 'Start with Arius already integrated',
				'solucao_acel_corpo'       => 'Use a ready-made template to connect Arius ERP to MES and the CRM with structured industrial flows.',
				'solucao_acel_topico_1'    => 'Connect MES quickly',
				'solucao_acel_topico_2'    => 'Reuse industrial integrations',
				'solucao_acel_topico_3'    => 'Speed up new projects',
			)
		);
	}

	/**
	 * TOTVS RM.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_totvs_rm() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your TOTVS RM',
				'solucao_hero_titulo'      => 'Connect TOTVS RM to every satellite system',
				'solucao_hero_corpo'       => 'Integrate HR, education and back office with payroll, time tracking, portals and enterprise applications to automate complete journeys.',
				'solucao_pilares_titulo'   => 'Get more out of TOTVS RM',
				'solucao_pilares_1_titulo' => 'Connect the RM modules',
				'solucao_pilares_1_desc'   => 'Integrate RM Folha, RM Núcleo and RM Backoffice with the external systems.',
				'solucao_pilares_2_titulo' => 'Automate complete journeys',
				'solucao_pilares_2_desc'   => 'Orchestrate employee and student cycles across different platforms.',
				'solucao_pilares_3_titulo' => 'Use the native web services',
				'solucao_pilares_3_desc'   => 'Connect applications using the official TOTVS RM resources.',
				'solucao_casos_titulo'     => 'Automate the TOTVS RM processes',
				'solucao_casos_1_titulo'   => 'Orchestrate onboarding and offboarding',
				'solucao_casos_1_desc'     => 'Connect RM to AD, benefits and the LMS automatically.',
				'solucao_casos_2_titulo'   => 'Integrate the academic journey',
				'solucao_casos_2_desc'     => 'Synchronise RM Núcleo with portals and education platforms.',
				'solucao_casos_3_titulo'   => 'Connect finance and banks',
				'solucao_casos_3_desc'     => 'Automate back-office financial processes with banking institutions.',
				'solucao_casos_4_titulo'   => 'Consolidate data for BI',
				'solucao_casos_4_desc'     => 'Unify HR and education information for strategic analysis.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make HR data available to administrative agents without exposing the system core.',
				'solucao_dif_titulo'       => 'Secure integrations for TOTVS RM',
				'solucao_dif_corpo'        => 'Protect employee and student data with masking of information in transit and full auditing of the processes.',
				'solucao_dif_topico_1'     => 'Protect sensitive personal data',
				'solucao_dif_topico_2'     => 'Audit every movement',
				'solucao_dif_topico_3'     => 'Control the information shared',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Centralise the business journeys',
				'solucao_plat_corpo'       => 'Replace one-off integrations between RM and satellite systems with a single layer of reusable processes.',
				'solucao_plat_topico_1'    => 'Reuse existing pipelines',
				'solucao_plat_topico_2'    => 'Connect multiple systems',
				'solucao_plat_topico_3'    => 'Simplify complex architectures',
				'solucao_acel_titulo'      => 'Start with RM connected',
				'solucao_acel_corpo'       => 'Use a ready-made template to integrate HR and education RM with the satellite systems of the organisation.',
				'solucao_acel_topico_1'    => 'Connect systems quickly',
				'solucao_acel_topico_2'    => 'Reuse ready-made processes',
				'solucao_acel_topico_3'    => 'Speed up new automations',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * TOTVS Linx.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_totvs_linx() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Linx',
				'solucao_hero_titulo'      => 'Connect Linx from the POS to the enterprise ERP',
				'solucao_hero_corpo'       => 'Integrate Linx solutions for retail, fashion, filling stations and pharmacies with the ERP, CRM and loyalty programmes to centralise commercial operations.',
				'solucao_pilares_titulo'   => 'Scale your Linx operation, connected',
				'solucao_pilares_1_titulo' => 'Connect the Linx verticals',
				'solucao_pilares_1_desc'   => 'Integrate fashion, retail, filling station and pharmacy operations into the enterprise ecosystem.',
				'solucao_pilares_2_titulo' => 'Sync sales in real time',
				'solucao_pilares_2_desc'   => 'Connect POS transactions to the financial systems automatically.',
				'solucao_pilares_3_titulo' => 'Integrate loyalty and CRM',
				'solucao_pilares_3_desc'   => 'Connect customer data to the relationship programmes.',
				'solucao_casos_titulo'     => 'Automate the Linx retail processes',
				'solucao_casos_1_titulo'   => 'Sync sales with the ERP',
				'solucao_casos_1_desc'     => 'Send Linx POS transactions to enterprise finance automatically.',
				'solucao_casos_2_titulo'   => 'Connect loyalty programmes',
				'solucao_casos_2_desc'     => 'Integrate customer data with the CRM and relationship platforms.',
				'solucao_casos_3_titulo'   => 'Consolidate multi-store sales',
				'solucao_casos_3_desc'     => 'Centralise results from different stores and retail brands.',
				'solucao_casos_4_titulo'   => 'Integrate tax documents',
				'solucao_casos_4_desc'     => 'Connect SAT, NF-e and NFC-e in one centralised operation.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make retail data available to administrative agents without exposing the system core.',
				'solucao_dif_titulo'       => 'Integrations for high sales volume',
				'solucao_dif_corpo'        => 'Connect POS operations with real-time processing to support large volumes of commercial transactions.',
				'solucao_dif_topico_1'     => 'Process sales in real time',
				'solucao_dif_topico_2'     => 'Support high transaction volume',
				'solucao_dif_topico_3'     => 'Connect multiple sites',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Unify the retail operations',
				'solucao_plat_corpo'       => 'Centralise data from the different Linx solutions to connect sales, finance and the CRM without customising the existing systems.',
				'solucao_plat_topico_1'    => 'Consolidate multiple retail brands',
				'solucao_plat_topico_2'    => 'Centralise commercial data',
				'solucao_plat_topico_3'    => 'Avoid complex customisation',
				'solucao_acel_titulo'      => 'Start with retail already integrated',
				'solucao_acel_corpo'       => 'Use a ready-made template to connect the Linx POS to the financial ERP and loyalty programmes.',
				'solucao_acel_topico_1'    => 'Connect POS terminals quickly',
				'solucao_acel_topico_2'    => 'Reuse commercial flows',
				'solucao_acel_topico_3'    => 'Speed up new integrations',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * TOTVS Consinco.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_totvs_consinco() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Consinco',
				'solucao_hero_titulo'      => 'Connect Consinco from the shelf to the distribution centre',
				'solucao_hero_corpo'       => 'Integrate the food retail ERP with the POS, e-commerce and suppliers to synchronise prices, stock and operations across the whole chain.',
				'solucao_pilares_titulo'   => 'Integrate the whole food retail operation',
				'solucao_pilares_1_titulo' => 'Connect purchasing and operations',
				'solucao_pilares_1_desc'   => 'Integrate retail purchasing, pricing and promotion processes.',
				'solucao_pilares_2_titulo' => 'Automate EDI connections',
				'solucao_pilares_2_desc'   => 'Synchronise data with suppliers with no manual processes.',
				'solucao_pilares_3_titulo' => 'Unify prices and channels',
				'solucao_pilares_3_desc'   => 'Keep the physical store and the digital one always aligned.',
				'solucao_casos_titulo'     => 'Automate the food retail processes',
				'solucao_casos_1_titulo'   => 'Sync prices and promotions',
				'solucao_casos_1_desc'     => 'Update prices across Consinco, the POS and e-commerce automatically.',
				'solucao_casos_2_titulo'   => 'Integrate suppliers through EDI',
				'solucao_casos_2_desc'     => 'Connect partner manufacturers to the purchasing flow.',
				'solucao_casos_3_titulo'   => 'Consolidate chain-wide sales',
				'solucao_casos_3_desc'     => 'Centralise multi-store sales data for BI.',
				'solucao_casos_4_titulo'   => 'Automate stock replenishment',
				'solucao_casos_4_desc'     => 'Use sales turnover to support automatic supply.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make retail data available to AI agents without exposing the system core.',
				'solucao_dif_titulo'       => 'Integrations built for high volume',
				'solucao_dif_corpo'        => 'Connect supermarket operations with thousands of SKUs and multiple stores while keeping performance, stability and continuous processing.',
				'solucao_dif_topico_1'     => 'Support large transaction volumes',
				'solucao_dif_topico_2'     => 'Connect multiple stores',
				'solucao_dif_topico_3'     => 'Process data continuously',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Centralise the connections of the whole chain',
				'solucao_plat_corpo'       => 'Unify EDI, POS and e-commerce integrations on a single platform to reduce operational effort and bring new partners on faster.',
				'solucao_plat_topico_1'    => 'Centralise EDI integrations',
				'solucao_plat_topico_2'    => 'Shorten supplier onboarding',
				'solucao_plat_topico_3'    => 'Reuse existing connections',
				'solucao_acel_titulo'      => 'Start with structured integrations',
				'solucao_acel_corpo'       => 'Use a ready-made template to connect Consinco, EDI suppliers, the POS and e-commerce more quickly.',
				'solucao_acel_topico_1'    => 'Connect suppliers quickly',
				'solucao_acel_topico_2'    => 'Adapt existing flows',
				'solucao_acel_topico_3'    => 'Speed up new integrations',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * HubSpot CRM.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_hubspot_crm() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your HubSpot',
				'solucao_hero_titulo'      => 'Connect HubSpot to the ERP and the rest of the sales funnel',
				'solucao_hero_corpo'       => 'Integrate the CRM, marketing, e-commerce and billing to turn opportunities into connected operations, without relying on Marketplace apps alone.',
				'solucao_pilares_titulo'   => 'Get more out of HubSpot CRM',
				'solucao_pilares_1_titulo' => 'Convert sales automatically',
				'solucao_pilares_1_desc'   => 'Turn closed deals into ERP orders with no rework.',
				'solucao_pilares_2_titulo' => 'Enrich commercial data',
				'solucao_pilares_2_desc'   => 'Update contacts and companies with information from other systems.',
				'solucao_pilares_3_titulo' => 'Get past the Marketplace limits',
				'solucao_pilares_3_desc'   => 'Build integrations for the specific scenarios of your business.',
				'solucao_casos_titulo'     => 'Automate the HubSpot CRM processes',
				'solucao_casos_1_titulo'   => 'Send sales to the ERP',
				'solucao_casos_1_desc'     => 'Create orders automatically once deals are closed.',
				'solucao_casos_2_titulo'   => 'Enrich contacts automatically',
				'solucao_casos_2_desc'     => 'Combine product, support and customer behaviour data.',
				'solucao_casos_3_titulo'   => 'Integrate e-commerce with the CRM',
				'solucao_casos_3_desc'     => 'Make purchase history available in the commercial relationship.',
				'solucao_casos_4_titulo'   => 'Consolidate marketing data',
				'solucao_casos_4_desc'     => 'Centralise the sales funnel and campaigns for strategic analysis.',
				'solucao_casos_5_titulo'   => 'Connect AI agents',
				'solucao_casos_5_desc'     => 'Make CRM data available to AI agents using governed APIs and MCP Servers.',
				'solucao_dif_titulo'       => 'Secure integrations for HubSpot',
				'solucao_dif_corpo'        => 'Use the official HubSpot REST API with access control, private tokens and permissions defined by scope.',
				'solucao_dif_topico_1'     => 'Use the official APIs.',
				'solucao_dif_topico_2'     => 'Control permissions by scope.',
				'solucao_dif_topico_3'     => 'Protect commercial data.',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Connect your whole commercial ecosystem',
				'solucao_plat_corpo'       => 'Centralise the CRM, ERP and operational systems in a single integration layer that keeps up as the company grows.',
				'solucao_plat_topico_1'    => 'Integrate multiple systems.',
				'solucao_plat_topico_2'    => 'Scale commercial processes.',
				'solucao_plat_topico_3'    => 'Avoid isolated connections.',
				'solucao_acel_titulo'      => 'Start with sales connected',
				'solucao_acel_corpo'       => 'Use a ready-made template to turn deals closed in HubSpot into orders in the ERP.',
				'solucao_acel_topico_1'    => 'Automate sales quickly.',
				'solucao_acel_topico_2'    => 'Reuse commercial flows.',
				'solucao_acel_topico_3'    => 'Speed up new integrations.',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * Azure.
	 *
	 * Mesmo texto da landing Microsoft Azure do catálogo de tecnologias —
	 * as duas páginas repetem o conteúdo também no português.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_azure() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Azure',
				'solucao_hero_titulo'      => 'Speed up Azure adoption while keeping your core connected',
				'solucao_hero_corpo'       => 'Integrate Azure services, SAP, Salesforce and legacy systems on one platform to evolve your cloud architecture without interrupting the operations you already run.',
				'solucao_pilares_titulo'   => 'Evolve your Microsoft architecture at scale',
				'solucao_pilares_1_titulo' => 'Connect Azure services natively',
				'solucao_pilares_1_desc'   => 'Use ready-made connectors for Azure data and messaging.',
				'solucao_pilares_2_titulo' => 'Speed up real-time events',
				'solucao_pilares_2_desc'   => 'Adopt event-driven architectures without rebuilding integrations.',
				'solucao_pilares_3_titulo' => 'Integrate the Microsoft ecosystem',
				'solucao_pilares_3_desc'   => 'Connect Azure, Dynamics 365, Teams and Azure AD.',
				'solucao_casos_titulo'     => 'Automate processes connected to Azure',
				'solucao_casos_1_titulo'   => 'Capture events in real time',
				'solucao_casos_1_desc'     => 'Send business events to analytics using Event Hubs.',
				'solucao_casos_2_titulo'   => 'Decouple systems with queues',
				'solucao_casos_2_desc'     => 'Use Service Bus to connect legacy systems and new services.',
				'solucao_casos_3_titulo'   => 'Store data with low latency',
				'solucao_casos_3_desc'     => 'Use CosmosDB for high-performance global scenarios.',
				'solucao_casos_4_titulo'   => 'Automate corporate files',
				'solucao_casos_4_desc'     => 'Process documents using Blob Storage and DataLake.',
				'solucao_casos_5_titulo'   => 'Centralise secrets management',
				'solucao_casos_5_desc'     => 'Protect integration credentials with Azure Key Vault.',
				'solucao_casos_cta_texto'  => 'Talk to a specialist',
				'solucao_dif_titulo'       => 'Azure integrations with native security',
				'solucao_dif_corpo'        => 'Connect Azure services using OAuth2, Azure AD and Key Vault to control access and protect credentials across every flow.',
				'solucao_dif_topico_1'     => 'Authenticate through Azure AD',
				'solucao_dif_topico_2'     => 'Protect secrets with Key Vault',
				'solucao_dif_topico_3'     => 'Control access centrally',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Connect the whole Microsoft ecosystem',
				'solucao_plat_corpo'       => 'Centralise integrations between Azure, Microsoft applications and enterprise systems to speed up new initiatives with no added complexity.',
				'solucao_plat_topico_1'    => 'Integrate data and applications',
				'solucao_plat_topico_2'    => 'Reuse existing pipelines',
				'solucao_plat_topico_3'    => 'Evolve the architecture gradually',
				'solucao_acel_titulo'      => 'Start with Azure events already structured',
				'solucao_acel_corpo'       => 'Use a ready-made template to connect business events to Event Hubs and Service Bus, speeding up your event-driven architecture.',
				'solucao_acel_topico_1'    => 'Set events up quickly',
				'solucao_acel_topico_2'    => 'Cut back on custom development',
				'solucao_acel_topico_3'    => 'Speed up cloud adoption',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * Google Cloud.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_google_cloud() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your Google Cloud',
				'solucao_hero_titulo'      => 'Speed up Google Cloud adoption by connecting data and AI',
				'solucao_hero_corpo'       => 'Integrate the ERP, CRM and operational systems with BigQuery and Vertex AI to speed up data and artificial intelligence initiatives without disconnecting your legacy.',
				'solucao_pilares_titulo'   => 'Turn data into intelligence on GCP',
				'solucao_pilares_1_titulo' => 'Connect BigQuery and Vertex AI',
				'solucao_pilares_1_desc'   => 'Take enterprise data into analytics and AI agents.',
				'solucao_pilares_2_titulo' => 'Adopt events at scale',
				'solucao_pilares_2_desc'   => 'Use Pub/Sub to connect systems in real time.',
				'solucao_pilares_3_titulo' => 'Integrate without replacing legacy systems',
				'solucao_pilares_3_desc'   => 'Connect existing environments as your cloud journey evolves.',
				'solucao_casos_titulo'     => 'Automate data flows on GCP',
				'solucao_casos_1_titulo'   => 'Feed BigQuery',
				'solucao_casos_1_desc'     => 'Send ERP and CRM data through for up-to-date analysis.',
				'solucao_casos_2_titulo'   => 'Decouple systems with Pub/Sub',
				'solucao_casos_2_desc'     => 'Distribute events across applications with no direct dependencies.',
				'solucao_casos_3_titulo'   => 'Prepare data for AI',
				'solucao_casos_3_desc'     => 'Update Vertex AI models with enterprise context.',
				'solucao_casos_4_titulo'   => 'Process files in the cloud',
				'solucao_casos_4_desc'     => 'Store and process documents using Cloud Storage.',
				'solucao_casos_5_titulo'   => 'Run reverse ETL',
				'solucao_casos_5_desc'     => 'Send analytical results back to the operational systems.',
				'solucao_casos_cta_texto'  => 'Talk to a specialist',
				'solucao_dif_titulo'       => 'GCP integrations with enterprise security',
				'solucao_dif_corpo'        => 'Connect Google Cloud services using IAM, Service Accounts and Cloud KMS to protect access, keys and data throughout the operation.',
				'solucao_dif_topico_1'     => 'Authenticate through Service Accounts',
				'solucao_dif_topico_2'     => 'Protect keys with Cloud KMS',
				'solucao_dif_topico_3'     => 'Control access through IAM',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Connect data, AI and the operation',
				'solucao_plat_corpo'       => 'Centralise the integration between enterprise systems and Google Cloud services to speed up data initiatives without creating isolated pipelines.',
				'solucao_plat_topico_1'    => 'Integrate enterprise systems',
				'solucao_plat_topico_2'    => 'Reuse existing flows',
				'solucao_plat_topico_3'    => 'Speed up AI initiatives',
				'solucao_acel_titulo'      => 'Start with data ready for AI',
				'solucao_acel_corpo'       => 'Use a structured template to connect the ERP and CRM to BigQuery and Vertex AI with data that is always current.',
				'solucao_acel_topico_1'    => 'Connect sources quickly',
				'solucao_acel_topico_2'    => 'Cut back on custom projects',
				'solucao_acel_topico_3'    => 'Speed up cloud adoption',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * AWS.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_aws() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'for your AWS',
				'solucao_hero_titulo'      => 'Speed up AWS adoption without rewriting the integrations you already have',
				'solucao_hero_corpo'       => 'Connect AWS services, ERPs, CRMs and legacy systems on the same platform to evolve your cloud architecture without interrupting current operations.',
				'solucao_pilares_titulo'   => 'Evolve your cloud architecture securely',
				'solucao_pilares_1_titulo' => 'Connect AWS services natively',
				'solucao_pilares_1_desc'   => 'Use ready-made connectors to integrate AWS services with no specific development.',
				'solucao_pilares_2_titulo' => 'Adopt events at scale',
				'solucao_pilares_2_desc'   => 'Put event-driven architectures in place without rebuilding the integrations you already have.',
				'solucao_pilares_3_titulo' => 'Migrate incrementally',
				'solucao_pilares_3_desc'   => 'Connect legacy systems and AWS workloads as your cloud journey evolves.',
				'solucao_casos_titulo'     => 'Automate processes connected to AWS',
				'solucao_casos_1_titulo'   => 'Trigger flows from events',
				'solucao_casos_1_desc'     => 'Start AWS pipelines from ERP and CRM events.',
				'solucao_casos_2_titulo'   => 'Orchestrate Lambda functions',
				'solucao_casos_2_desc'     => 'Bring serverless functions into complete integration flows.',
				'solucao_casos_3_titulo'   => 'Decouple systems with queues',
				'solucao_casos_3_desc'     => 'Use SNS and SQS to connect applications with more flexibility.',
				'solucao_casos_4_titulo'   => 'Monitor cloud operations',
				'solucao_casos_4_desc'     => 'Follow AWS and legacy pipelines in one centralised view.',
				'solucao_casos_5_titulo'   => 'Migrate workloads gradually',
				'solucao_casos_5_desc'     => 'Move to ECS without interrupting the integrations you already have.',
				'solucao_casos_cta_texto'  => 'Talk to a specialist',
				'solucao_dif_titulo'       => 'AWS integrations with enterprise security',
				'solucao_dif_corpo'        => 'Connect AWS services using IAM/STS authentication, key management through KMS and encryption to protect data throughout the operation.',
				'solucao_dif_topico_1'     => 'Authenticate connections through IAM',
				'solucao_dif_topico_2'     => 'Protect data with KMS',
				'solucao_dif_topico_3'     => 'Encrypt data in transit',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Connect legacy and cloud in one place',
				'solucao_plat_corpo'       => 'Centralise the communication between existing systems and new AWS services to speed up the transformation without building throwaway integrations.',
				'solucao_plat_topico_1'    => 'Integrate legacy systems',
				'solucao_plat_topico_2'    => 'Connect cloud-native services',
				'solucao_plat_topico_3'    => 'Evolve with no interruptions',
				'solucao_acel_titulo'      => 'Start with AWS events already structured',
				'solucao_acel_corpo'       => 'Use a ready-made template to connect business events to EventBridge, Lambda and SNS, speeding up your event-driven architecture.',
				'solucao_acel_topico_1'    => 'Set events up quickly',
				'solucao_acel_topico_2'    => 'Reuse existing flows',
				'solucao_acel_topico_3'    => 'Speed up cloud adoption',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

	/**
	 * ChatGPT.
	 *
	 * A landing em português só tem o diagrama, o bloco de integrações e o
	 * acelerador de MCP Server — as demais seções não têm texto.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_chatgpt() {
		return $this->solucao_en(
			array(
				'solucao_diagrama_titulo' => 'A new way to connect AI to your systems',
				'solucao_int_eyebrow'     => 'integrations',
				'solucao_int_titulo'      => 'Integrate all of your systems with ChatGPT',
				'solucao_int_subtitulo'   => 'Thousands of integrations ready to use',
				'solucao_acel_eyebrow'    => 'MCP server',
				'solucao_acel_titulo'     => 'Give ChatGPT tools without having to develop APIs',
				'solucao_acel_corpo'      => 'Turn enterprise processes into Tools for ChatGPT, defining exactly which information it can query and which actions it can run.',
				'solucao_acel_topico_1'   => 'Turn processes into AI tools',
				'solucao_acel_topico_2'   => 'Control inputs, outputs and information',
				'solucao_acel_topico_3'   => 'Expose it all through the MCP Server',
				'solucao_acel_topico_4'   => 'And much more...',
			)
		);
	}

	/**
	 * Claude.
	 *
	 * @return array<string,string>
	 */
	protected function texto_en_solucao_claude() {
		return $this->solucao_en(
			array(
				'solucao_hero_eyebrow'     => 'integrate your Claude',
				'solucao_hero_titulo'      => 'Turn enterprise knowledge into action with Claude',
				'solucao_hero_corpo'       => 'Claude connects documents, data and enterprise systems to look up information, interpret context and take action precisely and under control.',
				'solucao_pilares_titulo'   => 'Turn knowledge into decisions',
				'solucao_pilares_1_titulo' => 'Analyse large volumes of information',
				'solucao_pilares_1_desc'   => 'Process documents, history and records to draw out relevant insight without depending on manual searches.',
				'solucao_pilares_2_titulo' => 'Query the company knowledge',
				'solucao_pilares_2_desc'   => 'Connect internal sources — wikis, databases, policies — so Claude answers with the real context of the business.',
				'solucao_pilares_3_titulo' => 'Run tools',
				'solucao_pilares_3_desc'   => 'Create a sales order, update a CRM or raise a ticket — Claude acts on the systems with the right permissions.',
				'solucao_casos_titulo'     => 'Apply Claude where knowledge matters',
				'solucao_casos_1_titulo'   => 'Review contracts automatically',
				'solucao_casos_2_titulo'   => 'Query internal policies',
				'solucao_casos_3_titulo'   => 'Analyse customer requests',
				'solucao_casos_4_titulo'   => 'Compare commercial proposals',
				'solucao_casos_5_titulo'   => 'Summarise operational history',
				'solucao_diagrama_titulo'  => 'A new way to connect AI to your systems',
				'solucao_int_eyebrow'      => 'integrations',
				'solucao_int_titulo'       => 'Integrate all of your systems with Claude',
				'solucao_int_subtitulo'    => 'Thousands of integrations ready to use',
				'solucao_dif_titulo'       => 'Integrate AI with control over your data',
				'solucao_dif_corpo'        => 'Connect Claude to the enterprise systems while keeping control over data, permissions and actions — with no compromise on security or governance.',
				'solucao_dif_topico_1'     => 'Control which data reaches the models',
				'solucao_dif_topico_2'     => 'Protect data in transit and at rest',
				'solucao_dif_topico_3'     => 'Apply rules before running actions',
				'solucao_plat_eyebrow'     => 'one platform',
				'solucao_plat_titulo'      => 'Centralise knowledge, systems and processes',
				'solucao_plat_corpo'       => 'Claude delivers more value when it can reach the context it needs. The CLI Connect platform connects sources, orchestrates flows and keeps traceability.',
				'solucao_plat_topico_1'    => 'Connect different sources of information',
				'solucao_plat_topico_2'    => 'Reuse data in new processes',
				'solucao_plat_topico_3'    => 'Orchestrate results across systems',
				'solucao_acel_eyebrow'     => 'MCP server',
				'solucao_acel_titulo'      => 'Give Claude tools without having to develop APIs',
				'solucao_acel_corpo'       => 'Turn enterprise processes into Tools for Claude, defining exactly which information it can query and which actions it can run.',
				'solucao_acel_topico_1'    => 'Turn processes into AI tools',
				'solucao_acel_topico_2'    => 'Control inputs, outputs and information',
				'solucao_acel_topico_3'    => 'Expose it all through the MCP Server',
				'solucao_acel_topico_4'    => 'And much more...',
			)
		);
	}

}
