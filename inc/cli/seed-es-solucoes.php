<?php
/**
 * Seed — soluções em espanhol: taxonomia e menus.
 *
 * Etapa 1 da tradução: cada categoria e cada tipo ganha termo em espanhol, e as
 * landings nascem como **stub** — título e categoria traduzidos, seções de texto
 * vazias. É `traduzir_solucoes()` que aplica o stub quando o idioma não tem um
 * método `texto_es_solucao_*()`, para o catálogo e os menus funcionarem sem
 * deixar português no meio de uma página em espanhol.
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
 * Soluções em espanhol.
 */
trait Cliconnect_Seed_Es_Solucoes {

	/**
	 * Termos da taxonomia: chave do seed => [nome, slug do termo, slug do post].
	 *
	 * Mesma ordem de `criar_solucoes()`. O terceiro elemento só aparece onde o
	 * nome em espanhol é igual ao português — compartilhar slug entre idiomas é
	 * recurso do Polylang Pro, e sem ele o WordPress acrescenta um `-2`.
	 *
	 * @return array<string,array{0:string,1:string,2?:string}>
	 */
	protected function termos_solucao_es() {
		return array(
			// Categorias (nível 1).
			'tecnologia'                         => array( 'Tecnología', 'tecnologia-es' ),
			'industria'                          => array( 'Industria', 'industria-es' ),
			'departamento'                       => array( 'Departamento', 'departamento-es' ),
			'nuvem'                              => array( 'Nube', 'nube' ),
			'por-iniciativa'                     => array( 'Por Iniciativa', 'por-iniciativa-es' ),

			// Tecnologia.
			'claude'                             => array( 'Claude', 'claude-es', 'claude-integracion' ),
			'chatgpt'                            => array( 'ChatGPT', 'chatgpt-es', 'chatgpt-integracion' ),
			'sap'                                => array( 'SAP', 'sap-es', 'sap-integracion' ),
			'salesforce'                         => array( 'Salesforce', 'salesforce-es', 'salesforce-integracion' ),
			'totvs-protheus'                     => array( 'TOTVS Protheus', 'totvs-protheus-es', 'totvs-protheus-integracion' ),
			'sankhya'                            => array( 'Sankhya', 'sankhya-es', 'sankhya-integracion' ),
			'senior'                             => array( 'Senior', 'senior-es', 'senior-integracion' ),
			'dynamics-365'                       => array( 'Dynamics 365', 'dynamics-365-es', 'dynamics-365-integracion' ),
			'salesforce-sales-cloud'             => array( 'Salesforce Sales Cloud', 'salesforce-sales-cloud-es', 'salesforce-sales-cloud-integracion' ),
			'salesforce-service-cloud'           => array( 'Salesforce Service Cloud', 'salesforce-service-cloud-es', 'salesforce-service-cloud-integracion' ),
			'salesforce-marketing-cloud'         => array( 'Salesforce Marketing Cloud', 'salesforce-marketing-cloud-es', 'salesforce-marketing-cloud-integracion' ),
			'totvs-datasul'                      => array( 'TOTVS Datasul', 'totvs-datasul-es', 'totvs-datasul-integracion' ),
			'totvs-winthor'                      => array( 'TOTVS Winthor', 'totvs-winthor-es', 'totvs-winthor-integracion' ),
			'totvs-logix'                        => array( 'TOTVS Logix', 'totvs-logix-es', 'totvs-logix-integracion' ),
			'rd-station-crm'                     => array( 'RD Station CRM', 'rd-station-crm-es', 'rd-station-crm-integracion' ),
			'rd-station-marketing'               => array( 'RD Station Marketing', 'rd-station-marketing-es', 'rd-station-marketing-integracion' ),
			'thomson-reuters-tax-one'            => array( 'Thomson Reuters Tax One', 'thomson-reuters-tax-one-es', 'thomson-reuters-tax-one-integracion' ),
			'freshservice'                       => array( 'Freshservice', 'freshservice-es', 'freshservice-integracion' ),
			'servicenow'                         => array( 'ServiceNow', 'servicenow-es', 'servicenow-integracion' ),
			'portal-de-api'                      => array( 'Portal de API / MCP Server', 'portal-de-api-mcp-server-es' ),
			'zendesk'                            => array( 'Zendesk', 'zendesk-es', 'zendesk-integracion' ),
			'bionexo'                            => array( 'Bionexo', 'bionexo-es', 'bionexo-integracion' ),
			'tasy'                               => array( 'Tasy', 'tasy-es', 'tasy-integracion' ),
			'mv'                                 => array( 'MV', 'mv-es', 'mv-integracion' ),
			'vtex'                               => array( 'VTEX', 'vtex-es', 'vtex-integracion' ),
			'shopify'                            => array( 'Shopify', 'shopify-es', 'shopify-integracion' ),
			'magento'                            => array( 'Magento / Adobe Commerce', 'magento-adobe-commerce-es' ),
			'onblox'                             => array( 'OnBlox (WMS/TMS)', 'onblox-wms-tms-es' ),
			'narwal'                             => array( 'Narwal (Comercio Exterior)', 'narwal-comercio-exterior' ),
			'neogrid'                            => array( 'Neogrid', 'neogrid-es', 'neogrid-integracion' ),
			'target-sistemas'                    => array( 'Target Sistemas (ERP Distribución)', 'target-sistemas-erp-distribucion' ),
			'sap-business-one'                   => array( 'SAP Business One', 'sap-business-one-es', 'sap-business-one-integracion' ),
			'sap-ecc'                            => array( 'SAP ECC', 'sap-ecc-es', 'sap-ecc-integracion' ),
			'oracle-netsuite'                    => array( 'Oracle NetSuite', 'oracle-netsuite-es', 'oracle-netsuite-integracion' ),

			// Tecnologia — catálogo de tecnologias e landings de IA.
			'hubspot-crm'                        => array( 'HubSpot CRM', 'hubspot-crm-es', 'hubspot-crm-integracion' ),
			'totvs-consinco'                     => array( 'TOTVS Consinco', 'totvs-consinco-es', 'totvs-consinco-integracion' ),
			'totvs-linx'                         => array( 'TOTVS Linx', 'totvs-linx-es', 'totvs-linx-integracion' ),
			'totvs-rm'                           => array( 'TOTVS RM', 'totvs-rm-es', 'totvs-rm-integracion' ),
			'arius-erp'                          => array( 'Arius ERP', 'arius-erp-es', 'arius-erp-integracion' ),
			'ciss-poder-erp'                     => array( 'CISS Poder ERP', 'ciss-poder-erp-es', 'ciss-poder-erp-integracion' ),
			'ifs-cloud'                          => array( 'IFS Cloud', 'ifs-cloud-es', 'ifs-cloud-integracion' ),
			'qad-redzone'                        => array( 'QAD Redzone', 'qad-redzone-es', 'qad-redzone-integracion' ),
			'rp-info'                            => array( 'RP Info', 'rp-info-es', 'rp-info-integracion' ),
			'viasoft'                            => array( 'Viasoft', 'viasoft-es', 'viasoft-integracion' ),
			'onclick-erp'                        => array( 'Onclick ERP', 'onclick-erp-es', 'onclick-erp-integracion' ),
			'propz'                              => array( 'Propz', 'propz-es', 'propz-integracion' ),
			'microsoft-teams'                    => array( 'Microsoft Teams', 'microsoft-teams-es', 'microsoft-teams-integracion' ),
			'snowflake'                          => array( 'Snowflake', 'snowflake-es', 'snowflake-integracion' ),
			'databricks'                         => array( 'Databricks', 'databricks-es', 'databricks-integracion' ),
			'microsoft-azure'                    => array( 'Microsoft Azure', 'microsoft-azure-es', 'microsoft-azure-integracion' ),
			'gemini'                             => array( 'Gemini', 'gemini-es', 'gemini-integracion' ),

			// Indústria.
			'servicos-financeiros'               => array( 'Servicios Financieros', 'servicios-financieros' ),
			'manufatura'                         => array( 'Manufactura', 'manufactura' ),
			'logistica-3pl'                      => array( 'Logística (3PL)', 'logistica-3pl-es' ),
			'software-isv'                       => array( 'Software (ISV)', 'software-isv-es', 'software-isv-soluciones' ),
			'varejo'                             => array( 'Retail', 'retail-es' ),
			'hotelaria-e-turismo'                => array( 'Hotelería y Turismo', 'hoteleria-y-turismo' ),
			'seguros'                            => array( 'Seguros', 'seguros-es', 'seguros-integracion' ),

			// Departamento.
			'recursos-humanos-rh'                => array( 'Recursos Humanos (RR. HH.)', 'recursos-humanos-rrhh' ),
			'operacoes-de-receita-revops'        => array( 'Operaciones de Ingresos (RevOps)', 'operaciones-de-ingresos-revops' ),
			'marketing'                          => array( 'Marketing', 'marketing-es', 'marketing-integracion' ),
			'financeiro'                         => array( 'Finanzas', 'finanzas' ),

			// Nuvem.
			'aws'                                => array( 'AWS', 'aws-es', 'aws-integracion' ),
			'google-cloud'                       => array( 'Google Cloud', 'google-cloud-es', 'google-cloud-integracion' ),
			'azure'                              => array( 'Azure', 'azure-es', 'azure-integracion' ),

			// Por Iniciativa.
			'atualizacao-de-sistemas-legados'    => array( 'Modernización de Sistemas Heredados', 'modernizacion-de-sistemas-heredados' ),
			'pedido-ao-recebimento'              => array( 'Del Pedido al Cobro', 'del-pedido-al-cobro' ),
			'ia-corporativa'                     => array( 'IA Corporativa', 'ia-corporativa-es' ),
			'compras-ao-pagamento'               => array( 'De la Compra al Pago', 'de-la-compra-al-pago' ),
			'jornada-do-colaborador'             => array( 'Recorrido del Colaborador', 'recorrido-del-colaborador' ),
			'soberania-de-dados'                 => array( 'Soberanía de Datos', 'soberania-de-datos' ),
			'visao-360-do-cliente'               => array( 'Visión 360° del Cliente', 'vision-360-del-cliente' ),
			'modernizacao-de-erp'                => array( 'Modernización de ERP', 'modernizacion-de-erp' ),
			'integracao-pos-fusao'               => array( 'Integración Posfusión', 'integracion-posfusion' ),
			'centro-de-excelencia-em-integracao' => array( 'Centro de Excelencia en Integración', 'centro-de-excelencia-en-integracion' ),
		);
	}

	/**
	 * Monta os três menus em espanhol, espelhando `criar_menus()`.
	 *
	 * @param array<string,int> $termos chave => term_id em espanhol.
	 * @return void
	 */
	protected function criar_menus_es( $termos ) {
		$solucoes_base = home_url( '/' . $this->lang . '/solucoes/' );
		$cases_url     = home_url( '/' . $this->lang . '/cases/' );
		$blog_url      = $this->url_pagina_traduzida( 'blog' );

		$turl = function ( $chave ) use ( $termos ) {
			return $this->url_termo( $chave, $termos );
		};

		$solucoes_mega = array(
			array(
				'titulo' => 'Tecnología',
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
				'titulo' => 'Industria',
				'url'    => $turl( 'industria' ),
				'filhos' => array(
					'Servicios Financieros' => $turl( 'servicos-financeiros' ),
					'Manufactura'           => $turl( 'manufatura' ),
					'Logística (3PL)'       => $turl( 'logistica-3pl' ),
					'Software (ISV)'        => $turl( 'software-isv' ),
					'Retail'                => $turl( 'varejo' ),
					'Hotelería y Turismo'   => $turl( 'hotelaria-e-turismo' ),
					'Seguros'               => $turl( 'seguros' ),
				),
			),
			array(
				'titulo' => 'Departamento',
				'url'    => $turl( 'departamento' ),
				'filhos' => array(
					'Recursos Humanos (RR. HH.)'      => $turl( 'recursos-humanos-rh' ),
					'Operaciones de Ingresos (RevOps)' => $turl( 'operacoes-de-receita-revops' ),
					'Marketing'                       => $turl( 'marketing' ),
					'Finanzas'                        => $turl( 'financeiro' ),
				),
			),
			array(
				'titulo' => 'Nube',
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
					'Modernización de Sistemas Heredados' => $turl( 'atualizacao-de-sistemas-legados' ),
					'Del Pedido al Cobro'                 => $turl( 'pedido-ao-recebimento' ),
					'IA Corporativa'                      => $turl( 'ia-corporativa' ),
					'De la Compra al Pago'                => $turl( 'compras-ao-pagamento' ),
					'Recorrido del Colaborador'           => $turl( 'jornada-do-colaborador' ),
					'Soberanía de Datos'                  => $turl( 'soberania-de-dados' ),
					'Visión 360° del Cliente'             => $turl( 'visao-360-do-cliente' ),
					'Modernización de ERP'                => $turl( 'modernizacao-de-erp' ),
				),
			),
			array(
				'titulo' => 'Ver todas',
				'url'    => $solucoes_base,
			),
		);

		$descricao_produto = 'Integre todos sus sistemas y ponga agentes de IA personalizados a trabajar en sus procesos.';

		$this->montar_menu_traduzido(
			'principal',
			'CLI — Menú Principal (ES)',
			array(
				array(
					'titulo' => 'Plataforma',
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
					'titulo' => 'Soluciones',
					'url'    => $solucoes_base,
					'filhos' => $solucoes_mega,
				),
				array( 'titulo' => 'Integración SAP', 'url' => $this->url_pagina_traduzida( 'integracao-sap' ) ),
				array( 'titulo' => 'Casos de éxito', 'url' => $cases_url ),
				array( 'titulo' => 'Blog', 'url' => $blog_url ),
				array( 'titulo' => 'Contacto', 'url' => $this->url_pagina_traduzida( 'contato' ) ),
			)
		);

		$this->montar_menu_traduzido(
			'rodape',
			'CLI — Pie de página (ES)',
			array(
				array(
					'titulo' => 'col-plataforma-recursos',
					'url'    => '#',
					'filhos' => array(
						array(
							'titulo' => 'Plataforma',
							'url'    => $this->url_pagina_traduzida( 'plataforma' ),
							'filhos' => array(
								'CLI Connect'   => $this->url_pagina_traduzida( 'cli-connect' ),
								'CLI Signature' => $this->url_pagina_traduzida( 'cli-signature' ),
							),
						),
						array(
							'titulo' => 'Recursos',
							'url'    => $this->url_pagina_traduzida( 'contato' ),
							'filhos' => array(
								'Casos de éxito'        => $cases_url,
								'Blog'                  => $blog_url,
								'Trabaja con nosotros'  => $this->url_pagina_traduzida( 'trabalhe-conosco' ),
								'Contacto'              => $this->url_pagina_traduzida( 'contato' ),
							),
						),
					),
				),
				array(
					'titulo' => 'col-tecnologia',
					'url'    => '#',
					'filhos' => array(
						array(
							'titulo' => 'Tecnología',
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
									'titulo'  => 'Ver todas',
									'url'     => $turl( 'tecnologia' ),
									'classes' => 'link-ver-todos',
								),
							),
						),
					),
				),
				array(
					'titulo' => 'col-industria',
					'url'    => '#',
					'filhos' => array(
						array(
							'titulo' => 'Industria',
							'url'    => $turl( 'industria' ),
							'filhos' => array(
								'Servicios Financieros' => $turl( 'servicos-financeiros' ),
								'Manufactura'           => $turl( 'manufatura' ),
								'Logística (3PL)'       => $turl( 'logistica-3pl' ),
								'Software (ISV)'        => $turl( 'software-isv' ),
								'Retail'                => $turl( 'varejo' ),
								'Hotelería y Turismo'   => $turl( 'hotelaria-e-turismo' ),
								'Seguros'               => $turl( 'seguros' ),
							),
						),
					),
				),
				array(
					'titulo' => 'col-departamento-nube',
					'url'    => '#',
					'filhos' => array(
						array(
							'titulo' => 'Departamento',
							'url'    => $turl( 'departamento' ),
							'filhos' => array(
								'Recursos Humanos (RR. HH.)'       => $turl( 'recursos-humanos-rh' ),
								'Operaciones de Ingresos (RevOps)' => $turl( 'operacoes-de-receita-revops' ),
								'Marketing'                        => $turl( 'marketing' ),
								'Finanzas'                         => $turl( 'financeiro' ),
							),
						),
						array(
							'titulo' => 'Nube',
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
					'titulo' => 'col-iniciativas',
					'url'    => '#',
					'filhos' => array(
						array(
							'titulo' => 'Por Iniciativa',
							'url'    => $turl( 'por-iniciativa' ),
							'filhos' => array(
								'Modernización de Sistemas Heredados' => $turl( 'atualizacao-de-sistemas-legados' ),
								'IA Corporativa'                      => $turl( 'ia-corporativa' ),
								'De la Compra al Pago'                => $turl( 'compras-ao-pagamento' ),
								'Del Pedido al Cobro'                 => $turl( 'pedido-ao-recebimento' ),
								'Recorrido del Colaborador'           => $turl( 'jornada-do-colaborador' ),
								'Soberanía de Datos'                  => $turl( 'soberania-de-dados' ),
								'Visión 360° del Cliente'             => $turl( 'visao-360-do-cliente' ),
								'Modernización de ERP'                => $turl( 'modernizacao-de-erp' ),
							),
						),
					),
				),
			)
		);

		$this->montar_menu_traduzido(
			'rodape_legal',
			'CLI — Pie legal (ES)',
			array(
				'Política de Privacidad' => $this->url_pagina_traduzida( 'privacidade' ),
			)
		);
	}

	/* =====================================================================
	   RÓTULOS COMUNS DAS LANDINGS
	   ===================================================================== */

	/**
	 * Rótulos repetidos em toda landing de solução, em espanhol.
	 *
	 * Campo vazio no português continua vazio na tradução — `copiar_campos_acf()`
	 * ignora a chave —, então trazer aqui o CTA de Casos de Uso não liga a seção
	 * onde ela não existe. Cada landing sobrescreve o que fugir deste padrão.
	 *
	 * @return array<string,string>
	 */
	protected function base_solucao_es() {
		return array(
			'solucao_hero_btn1_texto' => 'Solicite una demostración',
			'solucao_hero_btn1_url'   => '/es/contacto/',
			'solucao_hero_btn2_texto' => 'Conozca nuestra solución',
			'solucao_hero_btn2_url'   => '/es/plataforma-de-integracion/',
			'solucao_pilares_eyebrow' => 'Pilares',
			'solucao_casos_eyebrow'   => 'casos de uso',
			'solucao_casos_cta_texto' => 'Solicite una demostración',
			'solucao_casos_cta_url'   => '/es/contacto/',
			'solucao_selos_eyebrow'   => 'compliance y seguridad',
			'solucao_selos_titulo'    => 'Lideramos el mercado cuando se trata de compliance y seguridad',
			'solucao_selos_corpo'     => 'Sus datos, procesos e integraciones protegidos por los más altos estándares globales.',
			'solucao_faq_titulo'      => 'Preguntas Frecuentes',
			'solucao_dif_eyebrow'     => 'diferencial técnico',
			'solucao_plat_eyebrow'    => 'plataforma única',
			'solucao_acel_eyebrow'    => 'Aceleradores de integración',
			'solucao_acel_btn_texto'  => 'Comenzar ahora',
			'solucao_acel_btn_url'    => '/es/contacto/',
			'solucao_acel_topico_4'   => 'Y mucho más...',
		);
	}

	/**
	 * Junta os rótulos comuns aos campos próprios de uma landing.
	 *
	 * @param array<string,string> $campos Campos específicos da solução.
	 * @return array<string,string>
	 */
	protected function solucao_es( $campos ) {
		return array_merge( $this->base_solucao_es(), $campos );
	}
}
