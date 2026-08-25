<?php
/**
 * Seed — texto em inglês das FAQ das landings de solução.
 *
 * `solucao_faq_itens` é um relationship com `cli_faq`, e o CPT é traduzível:
 * sem a versão em inglês de cada pergunta, o Polylang filtra a lista por idioma
 * e a seção de FAQ **some** da landing em inglês, em silêncio. Por isso estas
 * traduções rodam antes das landings — é `traduzir_referencia()` que troca os
 * IDs do português pelos do inglês.
 *
 * As FAQ gerais da home ficam em `seed-en-cpts.php`.
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
 * Conteúdo em inglês das FAQ de solução.
 */
trait Cliconnect_Seed_En_Faq {

	/**
	 * FAQ das soluções: slug do seed => [pergunta, resposta].
	 *
	 * Agrupadas por solução, na mesma ordem em que aparecem na landing.
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function faq_solucoes_en() {
		return array_merge(
			$this->faq_en_industria(),
			$this->faq_en_departamento(),
			$this->faq_en_iniciativa(),
			$this->faq_en_tecnologia()
		);
	}

	/**
	 * FAQ das soluções de Tecnologia.
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function faq_en_tecnologia() {
		return array(
			// Salesforce.
			'sf-apis'       => array(
				'Which Salesforce APIs are supported?',
				'<p>CLI Connect supports the main Salesforce REST APIs, including the REST API, Bulk API, Streaming API (Push Topics) and the Subscription API (Platform Events). Which API is used is decided by the data volume and the real-time event requirements of each integration.</p>',
			),
			'sf-firewall'   => array(
				'Can Salesforce be integrated with an on-premises ERP without opening firewall ports?',
				'<p>Yes. CLI Connect uses the Boomi Atom, an integration agent installed inside the corporate network that communicates outbound with the cloud platform. No inbound firewall ports are needed, so the security of the internal infrastructure is fully preserved.</p>',
			),
			'sf-mulesoft'   => array(
				'How does CLI Connect compare to MuleSoft?',
				'<p>CLI Connect uses Boomi as its integration platform, which offers a low-code interface, a more predictable pricing model and a lower cost of operation than MuleSoft. On top of that, the CLI Connect managed model includes running, monitoring and ongoing support, so there is no need for an internal team dedicated to the platform.</p>',
			),
			'sf-atualizacoes' => array(
				'Do the integrations keep working after Salesforce updates?',
				'<p>Yes. Salesforce maintains backward compatibility across its versioned APIs, and CLI Connect follows every release to keep the integrations stable. The monitoring team validates the critical flows at each update and raises preventive support when needed.</p>',
			),
			'sf-produtos'   => array(
				'Which Salesforce products can be integrated?',
				'<p>Sales Cloud, Marketing Cloud, Service Cloud, Revenue Cloud, Data Cloud and the other solutions on the Salesforce platform can all be integrated using the same integration architecture.</p>',
			),

			// Oracle NetSuite.
			'oracle-netsuite-suiteScript'              => array(
				'How do you cut the dependency on custom SuiteScript?',
				'<p>CLI Connect uses the native NetSuite APIs — SuiteTalk REST/SOAP and RESTlets — to build reusable integrations without a script written for each project. Instead of developing and maintaining SuiteScript for every integration, the platform concentrates the flows in configurable connectors, which cuts the amount of custom code and the maintenance effort over time.</p>',
			),
			'oracle-netsuite-subsidiarias'             => array(
				'Can the same integration be replicated for new subsidiaries?',
				'<p>Yes. With NetSuite OneWorld, CLI Connect replicates integrations across subsidiaries without rebuilding them. The platform handles segmentation by subsidiary, applies the business rules specific to each entity and standardises financial and operational data flows globally, so consistency does not depend on extra development for every subsidiary that joins.</p>',
			),
			'oracle-netsuite-tba-oauth2'               => array(
				'How does TBA/OAuth2 authentication work?',
				'<p>NetSuite supports Token-Based Authentication (TBA) and OAuth 2.0 as authentication mechanisms for API integrations. CLI Connect uses those credentials to establish secure connections without storing user passwords, following enterprise security best practice. Access is governed by NetSuite roles and permissions, so each integration operates only within the scope it was authorised for.</p>',
			),

			// SAP ECC.
			'sap-ecc-firewall'                         => array(
				'Can ECC be connected without opening firewall ports?',
				'<p>Yes. CLI Connect uses the Boomi Atom installed inside the on-premises ECC network, which establishes secure outbound connections with no inbound firewall ports. The local Runtime talks to the cloud platform over an encrypted channel, so SAP ECC stays completely isolated from the internet while still exchanging data with external systems.</p>',
			),
			'sap-ecc-cutover-s4hana'                   => array(
				'How does the parallel cutover with S/4HANA work?',
				'<p>During the migration to S/4HANA, CLI Connect runs both systems at once on the same integration platform. Data flows can be routed to ECC, to S/4HANA or to both, depending on the phase of the project, so there is no need to rebuild integrations after the cutover. Transaction history and mapping rules are preserved throughout the transition.</p>',
			),
			'sap-ecc-pos-migracao'                     => array(
				'What changes in the integration after the move to S/4HANA?',
				'<p>With CLI Connect, almost nothing has to be rebuilt. The platform abstracts the differences between the ECC RFC/BAPI and the S/4HANA OData/BAPIs, adapting the connectors automatically. Integrations with external systems such as Salesforce, e-commerce and tax platforms keep working with no change to the business flows already configured.</p>',
			),

			// SAP Business One.
			'sap-business-one-service-layer'           => array(
				'How does integration through the SAP B1 Service Layer work?',
				'<p>CLI Connect uses the official SAP Business One REST/OData Service Layer to connect enterprise applications without relying on fragile customisation. Secure session authentication means every request is validated before it reaches the SAP B1 database, and the integration model hides the complexity of the native API, delivering ready-made mappings for orders, customers, products and tax documents.</p>',
			),
			'sap-business-one-migracao-s4hana'         => array(
				'Can the integrations be migrated when the company moves to S/4HANA?',
				'<p>Yes. The CLI Connect integration layer is agnostic to the SAP version. When you move from Business One to S/4HANA, the data flows stay on the platform and only the destination connector is reconfigured, preserving the whole history of mappings, business rules and connections with external systems, with no rebuild from scratch.</p>',
			),
			'sap-business-one-multiplas-filiais'       => array(
				'How do you handle several branches in SAP B1?',
				'<p>CLI Connect works with several SAP Business One companies at the same time, routing transactions to the right branch based on configurable business rules. You can consolidate orders from different channels into specific branches, synchronise price lists and stock across units and produce integrated financial reports with no additional customisation in SAP.</p>',
			),

			// Target Sistemas.
			'target-sistemas-edi-onboarding'           => array(
				'How do you speed up onboarding a new supplier over EDI?',
				'<p>CLI Connect provides a ready-made EDI integration accelerator that shortens supplier onboarding in Target ERP. The template includes pre-configured mappings for the main order, invoice and delivery confirmation formats, so there is no development from scratch and new partners can be connected in days rather than weeks.</p>',
			),
			'target-sistemas-forca-vendas'             => array(
				'Can several field sales apps be integrated with Target?',
				'<p>Yes. The CLI Connect platform acts as a central hub between Target ERP and several field sales apps at once. Orders captured in the field are transmitted to the ERP in real time, with stock, price list and commercial terms synchronised by team or region, and no individual customisation needed in each app.</p>',
			),
			'target-sistemas-financeiro-multi-empresa' => array(
				'How does multi-company financial consolidation work?',
				'<p>CLI Connect centralises the flow of financial data between branches and the holding company through governed integrations with Target ERP. Bank reconciliation, transfers between companies and consolidated reporting are automated, so the finance systems of each entity stay in sync with no manual intervention and with full traceability of every transaction.</p>',
			),

			// Neogrid.
			'neogrid-erp-nativo'                       => array(
				'How do you integrate Neogrid with an ERP outside the native connectors?',
				'<p>CLI Connect works as an integration layer independent of the native Neogrid connectors, so any ERP can be connected through the Boomi platform. It does this by translating the Neogrid EDI formats and APIs into the standard of the destination ERP, with no custom development in either system.</p>',
			),
			'neogrid-bi-varejo'                        => array(
				'Can retail visibility data be taken into enterprise BI?',
				'<p>Yes. The integration extracts the sell-out, stockout and inventory data available in Neogrid and forwards it to the enterprise BI in real time or in scheduled batches. Sales and operations teams can then decide on current data, with no manual exports and no intermediate spreadsheets.</p>',
			),
			'neogrid-traducao-edi'                     => array(
				'How does EDI order translation work?',
				'<p>The CLI Connect platform receives orders in the EDI format Neogrid transmits and translates them automatically into the native format of the internal ERP — XML, JSON or a proprietary layout. The process is audited with a log of every transaction, which gives traceability and makes diagnosis straightforward whenever something does not match.</p>',
			),

			// Narwal (Comex).
			'narwal-custos-importacao'                 => array(
				'How do you synchronise import costs with the ERP automatically?',
				'<p>CLI Connect integrates Narwal with the ERP through a dedicated integration layer, transmitting freight, customs clearance expenses and duties automatically as soon as they are recorded in the system. That removes manual entries, cuts reconciliation errors and keeps finance reflecting the real cost of each import.</p>',
			),
			'narwal-multiplas-filiais'                 => array(
				'Can several branches and foreign trade operations be integrated?',
				'<p>Yes. The CLI Connect platform supports multi-company environments, so the foreign trade operations of different branches can be centralised in a single integration with the ERP. Each branch keeps its own visibility while the data is consolidated for corporate financial and operational analysis.</p>',
			),
			'narwal-duimp'                             => array(
				'How does it work with the move to the DUIMP?',
				'<p>The integration is adapted to the new DUIMP model (the single import declaration), connecting Narwal processes to the Brazilian federal revenue systems and to the ERP in a way that is compatible with the new scheme. That keeps the data flow between foreign trade and finance running through the transition.</p>',
			),

			// OnBlox (WMS/TMS).
			'onblox-estoque-erp'                       => array(
				'How do you synchronise stock between OnBlox and the ERP?',
				'<p>CLI Connect builds a real-time integration between OnBlox and the ERP, transmitting stock movements automatically at every warehouse transaction. That removes manual reprocessing, reduces inventory discrepancies and keeps the sales channels showing the stock that is actually available.</p>',
			),
			'onblox-frota-financeiro'                  => array(
				'Can fleet data be integrated with finance?',
				'<p>Yes. The integration connects the OnBlox fleet management module to the finance system, automating the transfer of maintenance, fuel and fine costs. Accounting entries are then produced accurately and operating cost reports stay current with no manual intervention.</p>',
			),
			'onblox-multiplos-cds'                     => array(
				'How does it work with several distribution centres?',
				'<p>The CLI Connect platform supports several distribution centres, routing orders automatically to the most suitable one based on proximity, stock availability and operational capacity. Each centre works integrated with the ERP and the e-commerce, and the whole logistics operation stays visible from one place.</p>',
			),

			// Magento / Adobe Commerce.
			'magento-extensoes'                        => array(
				'How do you reduce the dependency on custom Magento extensions?',
				'<p>CLI Connect builds an integration layer outside Magento, moving business logic — order, catalogue and payment synchronisation — onto the Boomi platform. That cuts the number of extensions installed, makes version upgrades simpler and keeps the Magento core stable and fast.</p>',
			),
			'magento-pim'                              => array(
				'Can the catalogue be centralised through a PIM?',
				'<p>Yes. The integration connects the PIM to Magento to synchronise products, descriptions, prices and attributes automatically. When something is updated in the PIM it propagates to Magento with no manual import, which keeps the catalogue consistent across every channel.</p>',
			),
			'magento-pagamentos'                       => array(
				'How does payment and anti-fraud reconciliation work?',
				'<p>The integration connects the payment gateways and anti-fraud systems to company finance, automating the reconciliation of transactions made in Magento. Discrepancies are identified and handled in one place, which cuts manual errors and speeds up the financial close.</p>',
			),

			// Shopify.
			'shopify-nfe'                                => array(
				'How does CLI Connect powered by Boomi handle NF-e issuing from Shopify?',
				'<p>The Boomi platform connects the orders received in Shopify to the Brazilian tax system, generating the NF-e automatically with the right product, tax and recipient details. It happens in real time once the order is confirmed, with no manual intervention and in line with the tax rules in force.</p>',
			),
			'shopify-estoque-multicanal'                 => array(
				'Can stock be synchronised across several channels?',
				'<p>Yes. CLI Connect integrates Shopify with the ERP, WMS and marketplaces, keeping stock up to date in real time on every channel. When a sale happens anywhere, the balance is decremented automatically everywhere else, which removes overselling and keeps the operation consistent.</p>',
			),
			'shopify-plus'                               => array(
				'Does it work with Shopify Plus?',
				'<p>Yes. The integration supports both Shopify and Shopify Plus, drawing on the advanced APIs available in the Plus version for more complex automation, such as custom checkout flows, multiple stores and B2B operations.</p>',
			),

			// VTEX.
			'vtex-pico-trafego'                          => array(
				'How does CLI Connect powered by Boomi handle traffic peaks such as Black Friday?',
				'<p>The Boomi platform runs on an elastic cloud architecture, scaling automatically to absorb order volumes well above normal. Through Black Friday the connectors keep processing orders, stock updates and payments with the same reliability, with no manual intervention and no infrastructure tuning.</p>',
			),
			'vtex-multiplos-marketplaces'                => array(
				'Can stock be synchronised between VTEX and several marketplaces?',
				'<p>Yes. CLI Connect integrates VTEX with the main marketplaces, keeping stock up to date in real time on every channel. When a sale happens on any channel, stock is decremented automatically on the others, which avoids overselling and keeps the buying experience consistent.</p>',
			),
			'vtex-ship-from-store'                       => array(
				'How does ship-from-store fulfilment work?',
				'<p>The integration matches the orders received in VTEX with the physical stores eligible to ship them, based on proximity, available stock and operational capacity. Picking, packing and dispatch are then managed by the integration between VTEX and the WMS or the store point-of-sale system.</p>',
			),

			// MV.
			'mv-reduzir-glosas-ris-pacs'                 => array(
				'How do you cut claim rejections with a RIS/PACS to MV integration?',
				'CLI Connect validates the details of the exam request automatically before the exam happens, cross-checking data between RIS/PACS and MV to catch the inconsistencies that cause claim rejections before they occur.',
			),
			'mv-conectar-mv-tasy-rede-hospitalar'        => array(
				'Can MV and Tasy be connected in the same hospital network?',
				'Yes. The platform works as a neutral integration layer and can orchestrate flows between MV and Tasy, so hospital networks running different HIS can share data in a standard way.',
			),
			'mv-consolidacao-financeira-multi-unidade'   => array(
				'How does multi-unit financial consolidation work?',
				'CLI Connect collects financial data from each MV unit and centralises it in the enterprise ERP, removing manual consolidation in spreadsheets and giving a single view of the financial result across the network.',
			),

			// Tasy.
			'tasy-cli-connect-tasy-open-api'             => array(
				'How does CLI Connect powered by Boomi use the Tasy Open API?',
				'CLI Connect connects to the Tasy Open API with secure authentication and orchestrates the integration flows between Tasy and external systems such as the ERP, health insurers and laboratories — without changing the hospital core.',
			),
			'tasy-faturamento-tiss-multiplas-operadoras' => array(
				'Can TISS billing be integrated for several health insurers?',
				'Yes. The platform allows connectors to be configured for different health insurers, processing claims and authorisation responses centrally and automatically.',
			),
			'tasy-consolidacao-financeira-multi-hospital' => array(
				'How does multi-hospital financial consolidation work?',
				'CLI Connect builds a single integration layer that collects financial data from several Tasy units and sends it to the enterprise ERP, removing manual reconciliation work.',
			),

			// Bionexo.
			'bionexo-sincronizar-pedidos-erp'            => array(
				'How do you synchronise Bionexo purchase orders straight with the ERP?',
				'CLI Connect connects through the Bionexo API and fires an event at every newly approved order, writing it automatically into the hospital ERP — Totvs or SAP, for example — with no manual intervention.',
			),
			'bionexo-conciliar-notas-fiscais'            => array(
				'Can invoices be reconciled automatically?',
				'Yes. The platform captures the invoices issued through Bionexo and reconciles them against the purchase orders recorded in the ERP, flagging discrepancies and removing the manual checking step.',
			),
			'bionexo-multiplas-unidades-hospitalares'    => array(
				'How does it work with several hospital units?',
				'The integration supports several units in a single setup: each unit can have its own credentials and independent flows, all centralised and monitored on the same platform.',
			),

			// Zendesk.
			'zendesk-processo-sem-app-pago'              => array(
				'How do you build a Zendesk process that writes to another system with no paid app?',
				'CLI Connect works as an external integration layer: Zendesk fires an event through a webhook, the platform receives it, processes it and writes the data into the destination system — SAP or Totvs, for example — with no paid Marketplace app involved.',
			),
			'zendesk-enriquecer-ticket-erp-crm'          => array(
				'How do you enrich a ticket with ERP and CRM data in real time?',
				'When a ticket is opened, CLI Connect queries the ERP and the CRM in parallel using the customer email or ID and returns the data — orders, invoices, contracts — into the ticket through the Zendesk API, with no manual step from the agent.',
			),
			'zendesk-tickets-macros-agentes-ia'          => array(
				'Can tickets and macros be exposed as tools for AI agents?',
				'Yes. CLI Connect publishes the Zendesk endpoints as authenticated MCP tools. AI agents can query tickets, apply macros and update fields using natural language, with permissions controlled by scope.',
			),
			'zendesk-sincronizacao-status-crm'           => array(
				'How does status synchronisation between Zendesk and the CRM work?',
				'CLI Connect watches for status changes in Zendesk through a webhook and replicates the state in the CRM in real time. The reverse also works: updates in the CRM are reflected automatically in the Zendesk ticket.',
			),

			// Portal de API / MCP Server.
			'portal-de-api-diferenca-api-mcp'            => array(
				'What is the difference between publishing an API and exposing an MCP server?',
				'An API publishes documented REST endpoints for systems and applications to consume. An MCP Server exposes authenticated tools for AI agents, which use natural language to discover and run the actions available in the portal.',
			),
			'portal-de-api-agente-descobre-ferramentas'  => array(
				'How does an AI agent find and use the tools published in the portal?',
				'The agent connects to the portal MCP server, which lists the available tools automatically with name, description and parameters. The agent then picks and runs the right tool, with authentication and scope control inherited from the platform.',
			),
			'portal-de-api-limitar-acesso-consumidor'    => array(
				'Can the access of each consumer be limited?',
				'Yes. Each consumer — a system, a person or an agent — gets its own credentials with defined scopes. The portal controls which APIs and tools each consumer can reach, and audits every call.',
			),
			'portal-de-api-pipeline-vira-api'            => array(
				'Can an existing pipeline become an API with no rework?',
				'Yes. The API Portal publishes Boomi pipelines you have already built as documented REST endpoints in a few clicks, with no new development project and no integration rewritten.',
			),

			// ServiceNow.
			'servicenow-processo-sem-modulo'             => array(
				'How do you build a ServiceNow process that writes directly into another system, with no native integration module?',
				'<p>CLI Connect works as an external integration layer: ServiceNow fires an event through the API, the platform receives it, processes it and writes the data into the destination system — SAP or Totvs, for example — with no Spokes and no additional ServiceNow modules.</p>',
			),
			'servicenow-cadastro-produtos-totvs'         => array(
				'How does the product registration to Totvs ERP example work?',
				'<p>The user fills in a form in the ServiceNow service catalogue. Once it is approved, CLI Connect receives the payload, validates the data and calls the Totvs API to create the product. ServiceNow gets the confirmation and closes the ticket automatically.</p>',
			),
			'servicenow-agente-ia-incidente'             => array(
				'How does an AI agent open a ServiceNow incident automatically?',
				'<p>The AI agent sends a request to CLI Connect with the event data. The platform formats the payload to the ServiceNow schema and creates the incident through the REST API, including category, urgency and description — with no human involved.</p>',
			),

			// Freshservice.
			'freshservice-processo-sem-modulo'           => array(
				'Can a business process be built in Freshservice without buying an extra module?',
				'<p>Yes. CLI Connect lets you build forms, approvals and service catalogues that integrate directly with the internal systems — ERP and CRM, for instance — with no need to buy additional Freshservice modules.</p>',
			),
			'freshservice-formulario-grava-sistema'      => array(
				'How does a Freshservice form write straight into another internal system?',
				'<p>The integration runs through the Freshservice REST APIs. When a user submits a form, CLI Connect triggers the integration flow, which translates and sends the data to the destination system — SAP, Totvs or Active Directory, for example — in real time.</p>',
			),
			'freshservice-abrir-tickets-automaticamente' => array(
				'How do you open tickets automatically from another system?',
				'<p>Events from external systems — monitoring alerts, HR events or security incidents — call the CLI Connect API, which creates the matching tickets in Freshservice with the right data and priority.</p>',
			),

			// Thomson Reuters Tax One.
			'tax-one-divergencia-calculo'                => array(
				'How do you avoid tax calculation differences between the ERP and the e-commerce?',
				'<p>CLI Connect centralises every call to the Tax One engine at a single integration point. Both the ERP and the e-commerce query the same tax engine, so the tax calculated at checkout is identical to the one recorded on the invoice the ERP issues, and the discrepancies disappear.</p>',
			),
			'tax-one-multiplos-erps'                     => array(
				'Can the tax engine be centralised for several ERPs?',
				'<p>Yes. CLI Connect can connect different ERPs — SAP, TOTVS and Dynamics, for example — to the same Tax One engine. Each system makes its calculation calls independently, but all of them go through the same tax rule configuration, which keeps the whole organisation consistent.</p>',
			),
			'tax-one-auditoria-chamadas'                 => array(
				'How does the audit of calls to the calculation engine work?',
				'<p>CLI Connect records every call made to Tax One, including the source system, the parameters sent, the result returned and the timestamp. That audit trail is available for consultation, which makes it straightforward to evidence a calculation during a tax inspection.</p>',
			),

			// RD Station Marketing.
			'rd-station-marketing-nutricao'              => array(
				'How do you stop a lead who has already bought from receiving nurture emails?',
				'<p>CLI Connect can automatically remove the lead from the active RD Station Marketing lists as soon as it sees a won deal or a converted customer in the CRM. Contacts who have already bought stop receiving nurture flows automatically, with no manual step from the marketing team.</p>',
			),
			'rd-station-marketing-atribuicao'            => array(
				'Can campaign attribution be measured through to the close in the ERP?',
				'<p>Yes. CLI Connect connects RD Station Marketing campaign data with the sales and billing records in the ERP. You can then trace the lead journey from the first click on a campaign to the invoiced order, which gives real visibility of the ROI of each marketing action.</p>',
			),
			'rd-station-marketing-webhooks'              => array(
				'How does the real-time webhook integration work?',
				'<p>RD Station Marketing sends events through a webhook as soon as something happens — a form submitted, a lead qualified, an automation completed. CLI Connect receives those events, validates the payload and triggers the configured flows immediately, with no polling. Average latency is a matter of seconds, so the data reaches the CRM or ERP practically in real time.</p>',
			),

			// RD Station CRM.
			'rd-station-crm-erp'                         => array(
				'How do you synchronise closed deals straight with the ERP?',
				'<p>When a deal closes in RD Station CRM, CLI Connect picks the event up through a webhook and triggers the configured integration flow automatically — creating the order, contract or customer record in the ERP with no manual step. The field mapping is defined once and can be adjusted to the rules of your sales process.</p>',
			),
			'rd-station-crm-multiplas-contas'            => array(
				'Can several RD Station accounts from different business units be connected?',
				'<p>Yes. CLI Connect supports several simultaneous connections to separate RD Station CRM accounts. Each business unit works with its own credentials and independent flows, all centralised on a single integration platform to keep governance simple.</p>',
			),
			'rd-station-crm-rate-limit'                  => array(
				'How do you deal with the RD Station CRM API rate limits?',
				'<p>CLI Connect manages the RD Station CRM API rate limits automatically through queues and retries with exponential backoff. During volume peaks — bulk imports or large campaigns — the data is processed in a controlled way, with no errors and no records lost.</p>',
			),

			// Salesforce Marketing Cloud.
			'mc-jornada-evento-externo'                  => array(
				'How do you trigger a journey from an event outside Salesforce?',
				'<p>CLI Connect uses the Marketing Cloud Event API together with native ERP and e-commerce connectors. When an external event happens — a purchase or a product update — the integration sends the payload straight to Journey Builder, which starts the matching journey with no manual step.</p>',
			),
			'mc-optout-todos-canais'                     => array(
				'How do you make sure an opt-out propagates to every channel?',
				'<p>CLI Connect keeps contact preferences in sync across Marketing Cloud, the CRM and the other connected systems. When a contact opts out on any channel, the integration propagates it in real time to every point of communication, which keeps you compliant and stops unwanted sends.</p>',
			),
			'mc-segmentos-tempo-real'                    => array(
				'Can audience segments be synchronised with the CRM in real time?',
				'<p>Yes. CLI Connect keeps the Marketing Cloud Data Extensions up to date from business events in the CRM, ERP and e-commerce platforms. Segments and lists reflect real purchase, product usage and support data, with no manual exports and no periodic scheduling.</p>',
			),

			// Salesforce Service Cloud.
			'svc-erp-tempo-real'                         => array(
				'How does Service Cloud receive ERP information in real time?',
				'<p>CLI Connect uses the Salesforce Subscription API together with webhooks and native ERP connectors to propagate events in real time. When an order is updated in the ERP, the matching case in Service Cloud receives the updated data automatically, with no manual lookup.</p>',
			),
			'svc-reembolso-automatico'                   => array(
				'Can refund processes be automated from a case?',
				'<p>Yes. CLI Connect lets you build flows where closing a case with a given status automatically triggers the chargeback or refund process in the ERP or finance system. The agent does not need to open any other system to start it.</p>',
			),
			'svc-whatsapp-telefonia'                     => array(
				'How does the integration with WhatsApp and telephony work?',
				'<p>CLI Connect connects telephony platforms and messaging channels such as WhatsApp to Service Cloud through the official APIs. Interactions are recorded automatically as cases or activities, which keeps the whole support journey in one place with no duplicated data.</p>',
			),

			// Salesforce Sales Cloud.
			'sc-oportunidades-tempo-real'                => array(
				'How does real-time opportunity synchronisation work?',
				'<p>CLI Connect uses the Salesforce Subscription API (Platform Events / Change Data Capture) to capture opportunity changes the moment they happen. As soon as a record is updated in Sales Cloud, the event is processed and the data is propagated to the ERP or destination system, with no manual polling.</p>',
			),
			'sc-multiplas-orgs'                          => array(
				'Can several Salesforce orgs be connected to the same integration?',
				'<p>Yes. CLI Connect supports several Salesforce orgs in a single integration project. Each organisation is configured as an independent connection, so data flows between different Sales Cloud instances and the enterprise systems can be centralised without duplicating architecture.</p>',
			),
			'sc-vs-mulesoft'                             => array(
				'How does CLI Connect compare to MuleSoft for integrating Sales Cloud?',
				'<p>CLI Connect is a more affordable and faster route to integrating Sales Cloud with ERPs and enterprise systems. Where MuleSoft calls for specialist teams and long implementation cycles, CLI Connect offers ready-made accelerators, a quicker rollout and a lower total cost of ownership — while keeping enterprise governance, security and scalability.</p>',
			),

			// TOTVS Logix.
			'logix-overselling'                          => array(
				'How do you avoid overselling across several sales channels?',
				'<p>CLI Connect synchronises the Logix stock balance in real time with every connected channel — marketplaces, e-commerce and the physical store. Whenever a sale is confirmed, the platform updates the other channels automatically, which removes the risk of selling a product that is no longer available.</p>',
			),
			'logix-cds'                                  => array(
				'Does CLI Connect support several distribution centres?',
				'<p>Yes. The platform lets you map routing rules by region, product type or stock capacity, sending each order to the right distribution centre automatically. The movements of every centre are reflected in Logix in a consolidated view.</p>',
			),
			'logix-transportadoras'                      => array(
				'How does the integration with carriers work?',
				'<p>CLI Connect automates sending the order data to the carrier once it is dispatched in Logix, receives the tracking code and updates the delivery status in the ERP and in the sales channels. That removes manual entries and cuts errors in logistics follow-up.</p>',
			),

			// TOTVS Winthor.
			'winthor-volume'                             => array(
				'Does the platform support operations with a large order volume?',
				'<p>Yes. CLI Connect uses connectors dedicated to the Winthor automatic routines and web services, designed for the high order volumes typical of distributors and wholesalers. The platform processes large batches without compromising the stability of the ERP.</p>',
			),
			'winthor-forca-vendas'                       => array(
				'Can several field sales apps be integrated?',
				'<p>Yes. CLI Connect connects different pre-sales and field sales apps to Winthor at the same time, concentrating order intake in a single integration layer. That removes manual keying and makes sure every channel feeds the ERP automatically and in a standard way.</p>',
			),
			'winthor-transportadoras'                    => array(
				'How does the integration with carriers work?',
				'<p>CLI Connect automates sending delivery data to the partner carriers, including label generation, manifest transmission and receiving tracking events. Delivery status is updated automatically in Winthor, which keeps customers and the operation informed with no manual intervention.</p>',
			),

			// TOTVS Datasul.
			'datasul-banco-direto'                       => array(
				'Does CLI Connect access the Datasul database directly?',
				'<p>No. CLI Connect uses the Progress/EMS protocol to talk to Datasul natively and securely, with no direct database access. Processing happens inside the company infrastructure, which preserves data integrity and the corporate security policies.</p>',
			),
			'datasul-versoes'                            => array(
				'Can different versions of Datasul be integrated?',
				'<p>Yes. CLI Connect supports several Datasul versions, adapting the connectors to the APIs available in each environment. There is no need to upgrade the ERP before you start integrating — the platform works with what your current installation offers.</p>',
			),
			'datasul-sap'                                => array(
				'Can I integrate Datasul and SAP in the same company?',
				'<p>Yes. CLI Connect is a single platform that integrates different ERPs at the same time, Datasul and SAP included. Companies that grew through acquisitions and run several systems can centralise every integration in one governance layer.</p>',
			),
			'datasul-mes'                                => array(
				'How do you integrate Datasul with the MES?',
				'<p>CLI Connect offers ready-made connectors and accelerators to synchronise production orders between Datasul and MES systems. The integration updates the status of the orders automatically throughout the industrial operation, removing manual rework and reducing errors on the shop floor.</p>',
			),
			'datasul-bi-ia'                              => array(
				'Can the data be used in BI or Artificial Intelligence?',
				'<p>Yes. CLI Connect makes Datasul data available to BI platforms such as Power BI and Tableau, and lets AI agents query ERP information safely through governed integrations. That turns operational data into raw material for analysis and intelligent automation.</p>',
			),

			// SAP.
			'sap-integracao'                             => array(
				'How does CLI Connect integrate with SAP?',
				'<p>CLI Connect uses a native add-on certified by SAP, alongside RFC, BAPI, IDoc, OData, REST and SOAP connectors. That approach keeps compatibility with the main SAP ECC and S/4HANA versions, preserves the Clean Core architecture and removes any need for unsupported modifications in the system.</p>',
			),
			'sap-versoes'                                => array(
				'Which SAP versions are supported?',
				'<p>The platform supports SAP ECC (from version 6.0 onwards) and SAP S/4HANA, both Cloud and On-Premises. Which connector fits — RFC/BAPI for legacy processes or OData/REST APIs for S/4HANA — is decided during the integration architecture stage.</p>',
			),
			'sap-implantacao'                            => array(
				'How long does an implementation take?',
				'<p>It depends on how complex the scenario is, but projects that start from ready-made accelerators can reach production in a few weeks. CLI Connect provides pre-built templates for the most common scenarios — Order-to-Cash, Procure-to-Pay and the ECC to S/4HANA migration — which cuts the timeline considerably.</p>',
			),
			'sap-atualizacoes'                           => array(
				'Do SAP updates affect the integrations?',
				'<p>Integrations built with the native add-on and SAP-supported APIs follow the update cycle without breaking. CLI Connect tracks each release and validates the critical flows preventively, raising support whenever an adjustment is needed after an update.</p>',
			),
			'sap-cleancore'                              => array(
				'How do you preserve the Clean Core during the migration?',
				'<p>The main way to guarantee a Clean Core is to avoid modifying the SAP core directly. CLI Connect uses APIs and extensions supported by SAP itself — the certified add-on, OData, RFC and BAPI — keeping all the integration logic on the iPaaS platform, outside the ERP core. That makes future updates easier and lowers the operational risk of moving to S/4HANA.</p>',
			),

			// TOTVS Protheus.
			'totvs-sem-vpn'                              => array(
				'How does CLI Connect integrate Protheus without a VPN?',
				'<p>CLI Connect uses an agent (the Boomi Atom) installed inside the corporate network, which communicates outbound with the cloud platform. There are no firewall ports to open and no VPN to configure — Protheus stays protected while the integrations run normally.</p>',
			),
			'totvs-advpl'                                => array(
				'Do you need to develop AdvPL routines for every integration?',
				'<p>No. CLI Connect uses ExecAuto calling the standard Protheus MATA routines and the native REST APIs, which removes the need for custom AdvPL development in most scenarios. That reduces the dependency on the Protheus environment and makes the integrations more stable and easier to maintain.</p>',
			),
			'totvs-legados'                              => array(
				'Does the solution work in legacy Protheus environments?',
				'<p>Yes. CLI Connect supports legacy Protheus versions, using connectors compatible with the APIs available in each environment. The integration architecture is adapted to what your environment offers, with no immediate ERP upgrade required.</p>',
			),
			'totvs-filiais'                              => array(
				'How does integration across several branches work?',
				'<p>CLI Connect centralises the integrations of every branch on a single platform, synchronising stock, orders and records between units automatically. That removes operational discrepancies and keeps every branch working from the same information in real time.</p>',
			),
			'totvs-prazo'                                => array(
				'How long does it take to put an integration into production?',
				'<p>With the ready-made CLI Connect accelerators, most integrations between Protheus and other systems go live in a few days. More complex scenarios go through a quick assessment first, but the pre-configured templates still shorten the timeline considerably compared with a custom project.</p>',
			),

			// Sankhya.
			'sankhya-banco-direto'                       => array(
				'Does CLI Connect access the Sankhya database directly?',
				'<p>No. CLI Connect uses the official Sankhya API Gateway for every operation. There is no direct database access, which preserves the integrity of the business rules and the governance of the platform.</p>',
			),
			'sankhya-autenticacao'                       => array(
				'How does authentication work in the Sankhya integrations?',
				'<p>Authentication uses the native Sankhya integration user, with credentials configured on the CLI Connect platform. Each integration operates with the permissions explicitly assigned to that user, which keeps traceability and access control in place.</p>',
			),
			'sankhya-permissoes'                         => array(
				'Can you limit which data each integration reaches?',
				'<p>Yes. Permissions are defined in Sankhya itself, by entity and operation (read, write, delete). CLI Connect respects that configuration, so each integration only reaches the data the ERP administrator has authorised.</p>',
			),

			// Senior.
			'senior-dados-sensiveis'                     => array(
				'How does CLI Connect protect sensitive Senior data?',
				'<p>The platform automatically masks sensitive fields — tax ID, salary and bank details — before the information travels between systems. The whole process is written to an audit log, which gives traceability and keeps you compliant with the LGPD.</p>',
			),
			'senior-multiplas-filiais'                   => array(
				'Can several branches on different Senior databases be integrated?',
				'<p>Yes. CLI Connect supports multi-company and multi-database scenarios, so branches on separate Senior instances can be integrated on the same platform. Routing and data mapping rules are configured per company, which gives isolation with centralised governance.</p>',
			),
			'senior-tempo-implantacao'                   => array(
				'How long does it take to automate the joiner and leaver flow?',
				'<p>With the CLI Connect JML (Joiner, Mover, Leaver) accelerators, joiner and leaver projects can be rolled out in weeks. The pre-configured templates cut the development effort and leave room for quick adjustments to the specific rules of each company.</p>',
			),

			// Dynamics 365.
			'dynamics365-business-central'               => array(
				'Does CLI Connect work with Dynamics 365 Business Central and Finance & Operations at the same time?',
				'<p>Yes. CLI Connect supports several products of the Dynamics 365 family in parallel. Each product is configured as an independent connection on the platform, so you can orchestrate data between Business Central, Finance & Operations and other enterprise systems in a single integration project.</p>',
			),
			'dynamics365-autenticacao'                   => array(
				'How does CLI Connect authenticate with Microsoft Dynamics?',
				'<p>Authentication runs through Azure AD (Microsoft Entra ID) using OAuth2 with registered application credentials. That model means no user password is stored, and access can be audited and revoked centrally by the tenant administrator.</p>',
			),
			'dynamics365-power-automate'                 => array(
				'Can integrations built in Power Automate be replaced?',
				'<p>Yes. CLI Connect offers an enterprise integration layer that replaces Power Automate flows in scenarios with high volume, complex logic or a need for centralised governance. The migration happens gradually, with no interruption to the operation.</p>',
			),
		);
	}

	/**
	 * FAQ das soluções Por Iniciativa.
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function faq_en_iniciativa() {
		return array(
			// Atualização de Sistemas Legados.
			'legado-mainframe-rede'      => array(
				'Can mainframes be connected without changing the network infrastructure?',
				'<p>Yes. The Runtime is installed inside the environment itself, on the same side of the firewall the mainframe already lives on, and it is the Runtime that opens the connection outward — not the other way round. z/OS and AS/400 environments stay where they are, with the same network rules, no dedicated VPN and no new port exposed to the internet. All that changes is who talks to those systems: the integration layer, instead of each application on its own.</p>',
			),
			'legado-esb-transicao'       => array(
				'Do the integrations keep running while the ESB is being replaced?',
				'<p>They do. The transition happens route by route: the new architecture runs alongside the current ESB and each flow is only redirected after it has run in production with the same result as the old one. While a route is being rebuilt the legacy version stays active, which means you can step back without stopping the operation. The ESB is only switched off when no flow depends on it any more.</p>',
			),
			'legado-esb-vs-ipaas'        => array(
				'What changes when you replace an ESB with a modern integration platform?',
				'<p>A traditional ESB concentrates the rules in proprietary code, needs specialists in that specific technology and treats every change as a new project. The modern platform puts those same flows in a visual environment, on open and portable standards, with execution history, error handling and access governance in one place. In practice the gain is not only technical: it is depending less on a small group of people to keep the integration standing.</p>',
			),
			'legado-prazo-reconstrucao'  => array(
				'How long does it take to rebuild existing integrations?',
				'<p>It depends far more on how many business rules are buried in the old route than on the source technology. Straightforward flows — one read, one transformation, one delivery — are usually rebuilt and certified in weeks. What stretches the timeline is the archaeology: undocumented routes, transformations written as code inside the ESB and rules nobody remembers have to be mapped before they can be rewritten. That is why the replacement happens in waves, starting with the highest-volume, lowest-complexity routes.</p>',
			),
			'legado-pos-desativacao'     => array(
				'What happens once the ESB is fully decommissioned?',
				'<p>The operation lives on the new layer, and the licence and support cost of the old platform leaves the budget. From then on integration stops being a project and becomes routine: new connections, rule changes and flow adjustments all come through the same environment, with centralised monitoring and no need for a legacy technology specialist. It is also the moment the event-driven architecture starts to pay off — new applications connect to what already exists instead of opening yet another point-to-point integration.</p>',
			),

			// Integração Pós-Fusão.
			'ipf-runtime-firewall'       => array(
				'How does the Runtime remove the firewall problem on Day 1?',
				'<p>The runtime runs inside the acquired company environment and opens the connection outbound — it reaches for the platform, never the other way round. Because there is no inbound port to publish, there is no new edge rule, no fixed IP to negotiate and no exception for the other company security team to approve. That is exactly what tends to hold up the first weeks of an acquisition, when the two networks are still independent and nobody wants to relax the perimeter.</p>',
			),
			'ipf-antes-consolidacao-ti'  => array(
				'Can systems be connected before the IT consolidation?',
				'<p>Yes, and it is the most common scenario. The integration happens at the data and process layer, on the systems as they are today — two ERPs, two identity directories, two payrolls. Identity, HR and ERP can be connected even before the deal closes, so the operation stays on its feet on day one. The final consolidation follows its own schedule without blocking the capture of synergies.</p>',
			),
			'ipf-velocidade-deploy'      => array(
				'How fast can the operation be deployment-ready for Day 1?',
				'<p>The runtime comes up on multi-cloud or managed Kubernetes, so provisioning the environment is a matter of hours, not a project. What sets the schedule is access: credential approval on the source and destination systems, and sign-off on the design by the areas involved. With more than 300 connectors ready at no extra cost, the critical Day 1 flows — access, payroll, orders — normally ship in weeks rather than months.</p>',
			),
			'ipf-pipelines-pos-projeto'  => array(
				'Do the pipelines keep producing value after the integration project?',
				'<p>They do. Each integration is built as a reusable capsule, with the mapping pattern, error handling and governance already defined. Once the incorporation is finished, those capsules become the company repertoire for the next acquisition: whatever was built to connect an ERP or an identity directory is reused instead of starting from scratch. They also keep supporting the running operation — record synchronisation, analytics data and processes between the combined units.</p>',
			),
			'ipf-substituir-middleware-legado' => array(
				'How do you replace the legacy middleware of the acquired company?',
				'<p>The swap happens flow by flow, with no big bang. The first step is to inventory what the old middleware actually runs and how often; then the flows are rebuilt on the platform and run in parallel with the legacy, with results compared before the cutover. Each migrated flow is switched off on the old side only once it is stable, which keeps the operation running throughout the transition and avoids concentrating risk on a single date.</p>',
			),

			// IA Corporativa.
			'ia-provedores-llm'          => array(
				'Which LLM providers are supported natively?',
				'<p>The connection to the model is just another endpoint of the integration, so it works with any provider that exposes an API — the major cloud vendors and open models hosted on your own infrastructure included. In practice that means the agent talks to the model through the same path it uses for the ERP: credentials kept in one place, the call logged and cost limits applied before the request leaves.</p>',
			),
			'ia-pipelines-mcp'           => array(
				'How do integration pipelines become MCP tools?',
				'<p>A flow that already exists — look up an order in SAP, raise a ticket in ServiceNow, fetch a customer record from Salesforce — is published as a tool, with a description of what it does, the parameters it takes and what it returns. The agent sees that tool in the catalogue and calls it when needed, with no direct access to the source system: authentication, role-based permission control and the execution log stay in the integration layer.</p>',
			),
			'ia-vs-data-factory-glue'    => array(
				'How is this different from Azure Data Factory or AWS Glue?',
				'<p>Data Factory and Glue are data pipeline tools: they move volume from one point to another in batches, to feed a data warehouse. Enterprise AI needs something different — a live answer to a specific question, at the moment the agent asks, plus the ability to take an action back in the source system. That is why the layer here is application integration rather than ETL, and why it exposes tools and events as well as tables.</p>',
			),
			'ia-tempo-rag'               => array(
				'How long does it take to put RAG into production?',
				'<p>With the Confluence and SharePoint connectors ready, the schedule depends less on development and more on access and curation: approving credentials, deciding which spaces go into the base and defining who can see what. A well-scoped first pass usually ships in weeks. What stretches the timeline is a disorganised document base and permissions inherited inconsistently at the source — in those cases the clean-up is more work than the integration.</p>',
			),
			'ia-troca-de-provedor'       => array(
				'Does my architecture stay flexible if I change AI provider?',
				'<p>Yes, because the model sits behind the integration layer, not in the middle of it. The business rules, the published tools, the guardrails and the execution history belong to the platform; changing provider means changing a credential and an endpoint at one end, with everything else staying put. It also lets you run more than one model in parallel — one for simple tasks, another for the expensive ones — without duplicating the integration.</p>',
			),

			// Pedido ao Recebimento (O2C).
			'o2c-tempo-venda-recebimento' => array(
				'How do you shorten the time between closing the sale and getting paid?',
				'<p>What usually stretches the cycle is not the sale or the invoice itself, but the wait between them: the deal closed in the CRM that only becomes billing when someone retypes it, the invoice issued that only produces a charge the next day. By connecting CRM, ERP and the billing system in a single flow, each step triggers the next as soon as the previous one ends, with no overnight batch and no retyping. The gain shows up first in data consistency — order, invoice and receivable carrying the same figures — and only then in the average timeline.</p>',
			),
			'o2c-multiplos-erps-crms'    => array(
				'Can several ERPs and CRMs run in the same Order-to-Cash flow?',
				'<p>Yes, and it is the most common scenario in companies with several business units or a history of acquisitions. The transformation rules live in the integration layer, not inside each system, so one more ERP joins as a new endpoint on the flow that already exists, reusing the order, billing and reconciliation design. The real work is reconciling the master records — customer, product, payment terms — which usually differ between the bases.</p>',
			),
			'o2c-conciliacao-bancos'     => array(
				'How does automatic reconciliation with banks and acquirers work?',
				'<p>The integration receives the return files and statements from the institutions, matches each payment against the corresponding receivable in the ERP and settles what clears. Whatever does not match — a different amount, a partial payment, an acquirer fee deducted — is set aside in an exception queue with the reason, so finance handles case by case instead of checking everything by hand. Every attempt is logged, which supports the audit and allows reprocessing without settling twice.</p>',
			),
			'o2c-status-do-pedido'       => array(
				'How can you follow an order status from start to finish?',
				'<p>Because the flow goes through a single layer, each order carries an identifier that travels across CRM, ERP, billing and collection. That makes an end-to-end view possible — which step the order is on, when it got there, what failed and what was reprocessed — without checking system by system. Sales, finance and logistics look at the same status, which resolves most of the discrepancies between areas before they turn into rework.</p>',
			),
			'o2c-sistemas-do-fluxo'      => array(
				'Which systems can be part of the Order-to-Cash flow?',
				'<p>Typically the CRM where the deal is closed, the ERP that invoices, the billing platform or payment method, the banks and acquirers that confirm receipt, and the management tools that consume the days-outstanding indicators. Logistics systems also join the flow when delivery gates invoicing, as do credit and collections platforms when there is a limit check or a dunning ladder. The choice depends less on the connector catalogue and more on where the manual steps sit today.</p>',
			),

			// Compras ao Pagamento (S2P).
			's2p-matching-3-vias'        => array(
				'How do you automate three-way matching between order, receipt and invoice?',
				'<p>The integration reads the three documents where they originate — the purchase order in the ERP, the goods receipt in the warehouse or WMS and the invoice sent by the supplier — and compares quantity, price and commercial terms line by line. When all three match within the tolerances the company has set, the invoice goes straight to payment; when there is a discrepancy the flow stops and notifies the owner with the exact reason. The finance team stops checking spreadsheet by spreadsheet and handles only the exceptions.</p>',
			),
			's2p-visibilidade-gastos'    => array(
				'Can finance get real-time visibility of spend?',
				'<p>Yes. Because every step of the cycle passes through the integration, the financial commitment is recorded the moment it happens: the requisition approved, the order issued, the receipt confirmed and the invoice released. That data is consolidated by category, cost centre and supplier and sent to the ERP or the company BI tool, which means committed spend can be followed before it becomes booked expense — and negotiations can be based on real volume per supplier.</p>',
			),
			's2p-segregacao-funcoes'     => array(
				'How does segregation of duties between approval and payment work?',
				'<p>Approving and paying are separate steps of the flow, with separate permissions: whoever authorises the purchase is not the one who releases the payment, and the integration respects the roles already defined in the ERP and the approval system. Every transition records who acted, when and on which document, building a complete approval history available for audit. No payment is triggered before the previous step has been completed by an authorised role.</p>',
			),

			// Jornada do Colaborador (H2R).
			'h2r-desligamento-acessos'   => array(
				'How do you guarantee that an exit revokes every access automatically?',
				'<p>The exit recorded in the HR system becomes a single event the integration distributes to every system tied to that person — identity directory, email, VPN, ERP, benefits, physical access control and the business tools they used. Each revocation returns a confirmation, and anything that does not confirm shows up as an outstanding item instead of slipping through. The window between the exit and the access cut is measured in minutes, not in days of manual checklist.</p>',
			),
			'h2r-admissao-multiplos-sistemas' => array(
				'Can onboarding be orchestrated across several systems at once?',
				'<p>Yes. The hire approved in the HRIS triggers a single activation that creates the employee in payroll, opens the email account, provisions access according to the role, enrols them in benefits and signs them up for the LMS tracks. Steps that do not depend on each other happen in parallel, and those that do respect the order — the badge is only requested once the identity exists, for instance. HR follows progress in one place and the new joiner arrives on day one with everything ready.</p>',
			),
			'h2r-auditoria-ciclo-de-vida' => array(
				'How does the audit trail work across the employee lifecycle?',
				'<p>Every status change — hire, promotion, transfer, salary band change and exit — is recorded with the event that caused it, the systems updated, the time of each update and the result each one returned. Sensitive personal data travels masked, so the history stays auditable without exposing PII. That record is available for internal and external audit and is the same base used for turnover and tenure analysis.</p>',
			),

			// Soberania de Dados.
			'soberania-jurisdicao'       => array(
				'How does CLI Connect powered by Boomi guarantee data never leaves the required jurisdiction?',
				'<p>The execution engine runs inside the environment you specify — your own AWS, Azure or GCP account, or the company internal data centre. That is where data is read, transformed and written; the platform control plane handles configuration, versioning and monitoring, not the content that moves. In practice, a record that originates in one region only leaves it if a flow you designed yourself tells it to.</p>',
			),
			'soberania-multi-regiao'     => array(
				'Is multi-region deployment possible for operations in several countries?',
				'<p>Yes, and it is the most common design in international operations: one execution environment per country or regulatory bloc, each with its own data boundary, all administered from a single place. Flows are built once and distributed to each region, which avoids keeping parallel teams looking after nearly identical integrations — and lets a local rule, where one exists, appear as an explicit exception rather than a whole copy of the project.</p>',
			),
			'soberania-auditoria'        => array(
				'How do you audit where data was processed?',
				'<p>Every execution records which environment processed it, when, with which flow version and with what result. That history is what answers an auditor: instead of a policy statement, you show the execution trail by region. The records can be kept in your own environment and exported to whichever observability or compliance tool the company already uses.</p>',
			),

			// Centro de Excelência em Integração.
			'cei-catalogo-reutilizavel'  => array(
				'How do you structure an internal catalogue of reusable integrations?',
				'<p>The catalogue starts with an inventory of what already exists: every pipeline in production becomes an entry with an owner, the systems it connects, its input and output contract and its criticality. From there, the parts that repeat across projects — authentication, error handling, transforming the same business object — are extracted into versioned capsules published to the whole organisation. The gain shows up on the second project: instead of rebuilding the connection from scratch, the team assembles the integration from pieces that are already certified.</p>',
			),
			'cei-governanca-aprovacao'   => array(
				'How does the approval governance for new integrations work?',
				'<p>Every integration goes through a review flow before reaching production: whoever builds is not whoever approves, and promotion between environments requires sign-off from a technical owner in the Center of Excellence. What gets checked at that stage is always the same set — adherence to the naming standard, credentials kept in the vault, error handling and retries configured, and no duplication against the catalogue. Access is role-based, so creating, changing and publishing are separate permissions.</p>',
			),
			'cei-custo-e-performance'    => array(
				'Can cost and performance be measured centrally for each integration?',
				'<p>Yes. Because every integration runs on the same platform and follows the same instrumentation standard, volume processed, execution time, error rate and resource consumption are available in a single dashboard, broken down by integration, by connected system and by owning area. That data is what supports the next decisions of the Center of Excellence: which flows to optimise, which to consolidate and which to retire for low usage.</p>',
			),

			// Visão 360° do Cliente.
			'v360-resolucao-identidade'  => array(
				'How do you resolve one customer identity across different systems?',
				'<p>Each system stores the customer under its own key — a code in the ERP, an ID in the CRM, an email in support — which is why the same company shows up three times with different data. Identity resolution cross-references those identifiers with matching rules (tax ID, email domain, legal name) and keeps a table of equivalences between them. The unified profile becomes the reference, and each system carries on with the key it already uses, with no record migration.</p>',
			),
			'v360-tempo-real-ou-batch'   => array(
				'Is the 360° view updated in real time or in batches?',
				'<p>In real time: every relevant change in a connected system — an order invoiced, a ticket closed, a record corrected — triggers the update of the unified profile as it happens, without waiting for the overnight window. Batch loads are still available for what makes sense to process in bulk, such as the initial history load or legacy bases, but the day-to-day operation does not depend on them.</p>',
			),
			'v360-contexto-agente-ia'    => array(
				'How does an AI agent use this unified view?',
				'<p>The agent queries the unified profile before answering or acting, and gets in one go what would otherwise be scattered across CRM, ERP, support and product: current contracts, open orders, recent tickets and product usage. With that context the answer stops being generic, and the actions taken — raising a ticket, updating a record, escalating a case — happen on current data. The same read and write controls per system apply to the agent, so it only sees and changes what it has been authorised to.</p>',
			),
		);
	}

	/**
	 * FAQ das soluções de Departamento.
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function faq_en_departamento() {
		return array(
			// Recursos Humanos (RH).
			'rh-hris-folha-prazo'        => array(
				'How long does it take to integrate the HRIS with payroll?',
				'<p>The timeline depends far less on development than on access to the systems and the quality of the records. When the HRIS and payroll expose documented APIs and credentials are already approved, the flow for hires, moves and leavers usually goes live in weeks. What stretches the schedule is reconciling records that differ between the two systems and certifying with the vendor. Because the components are reusable, the first integration is the slowest and the ones after it build on what already exists.</p>',
			),
			'rh-autonomia-do-time'       => array(
				'Can HR manage the integrations without the development team?',
				'<p>Day to day, yes. The HR team follows executions, sees where a record stopped and reprocesses what failed from its own dashboard, without raising a ticket. Structural changes — adding a new system to the flow or changing the rules for a field — still go through whoever maintains the integration, but they start from ready-made components, so they are configuration changes rather than projects.</p>',
			),
			'rh-criterios-plataforma'    => array(
				'What should I look at when choosing an integration platform for HR?',
				'<p>Three points matter more than the connector list. The first is how personal data is handled: the platform has to detect and mask sensitive information before moving it, not after. The second is traceability — every hire, move and exit should leave a record of when it ran, where it went and what happened if it failed. The third is reuse: flows built as components lower the cost of each new integration, while point-to-point integrations add maintenance with every system you connect.</p>',
			),
			'rh-dados-sensiveis'         => array(
				'How is sensitive employee data protected during integrations?',
				'<p>The protection happens inside the flow itself. Fields such as tax IDs, bank details and health information are identified automatically and masked before they move on to the destination system, so only those who need the full value receive it. Traffic is encrypted end to end, access is granted by role and every movement is written to an audit trail — that is what keeps LGPD requirements met without relying on manual discipline.</p>',
			),
			'rh-mudanca-de-api'          => array(
				'How do HRIS vendor API changes affect the integrations?',
				'<p>The impact stays contained in the translation layer. Each system talks to a common internal format, so a new HRIS API version only requires adjusting the piece that talks to it — the rest of the flow, including payroll, identity and training, stays untouched. New versions are certified in a separate environment before going live, and monitoring flags when an endpoint changes behaviour, instead of the failure appearing at the payroll close.</p>',
			),

			// Operações de Receita (RevOps).
			'revops-crm-automacao-prazo' => array(
				'How long does it take to connect the CRM and the marketing automation platform?',
				'<p>Development is usually the short part: CRMs and marketing automation tools have well-documented APIs and ready-made connectors, so a lead and opportunity flow goes live within a few weeks. What stretches the schedule is the business decision — deciding which system owns each field, what counts as a qualified lead and how to handle the duplicates already in the base. It is worth starting with one flow, putting it into production and widening from there.</p>',
			),
			'revops-sem-desenvolvedor'   => array(
				'Can the RevOps team build integrations without dedicated developers?',
				'<p>Yes, for most of the day-to-day work. The visual builder assembles the flow by dragging and connecting steps, with AI support for mapping fields and suggesting transformations, and someone who knows the commercial process can create, adjust and monitor the automation without writing code. IT still steps in where it makes sense — approving credentials, reviewing critical flows and cases that need more elaborate logic — but stops being the bottleneck for every small change.</p>',
			),
			'revops-ponto-a-ponto-ipaas' => array(
				'What is the difference between a point-to-point integration and an iPaaS?',
				'<p>A point-to-point integration links two systems directly and works well while there are only two. The problem shows up at scale: every new tool multiplies the connections, each with its own logic and its own error handling, and nobody sees the whole. An iPaaS puts a layer in the middle — systems talk to it, not to each other. That centralises monitoring, reuses mappings and means swapping a tool is a change to one section, not to the whole web.</p>',
			),
			'revops-mudanca-api'         => array(
				'How do system API changes affect the integrations?',
				'<p>Version changes are expected and handled in the integration layer, not in each flow. Because the mapping between a system format and the internal format is isolated, an API change usually means adjusting one point, without touching the flows that depend on it. Monitoring reports the failure as soon as it happens, affected messages are held in a queue and reprocessed after the fix, with no record lost.</p>',
			),
			'revops-protecao-dados'      => array(
				'How is commercial data protected during the integrations?',
				'<p>Traffic is encrypted end to end and the credentials of each system live in a vault, never inside the flow. Access is granted by role, so whoever operates the automation does not need to see the sensitive content moving through it, and every action is written to an audit trail — who changed what, when and with what result. The operation follows the compliance and security standards listed on this page.</p>',
			),

			// Marketing.
			'mkt-velocidade-sincronizacao' => array(
				'How fast is the sync between the marketing platform and the CRM?',
				'<p>The flows work by event, not in batches: as soon as a lead is created or updated the message enters the queue and reaches the other system within seconds — the project benchmark is keeping the cycle under a minute. What usually weighs on that time is not the integration platform but the API limits of the destination system, which are respected automatically to avoid being throttled.</p>',
			),
			'mkt-marketing-operations'   => array(
				'Can the Marketing Operations team manage the integrations without IT?',
				'<p>Largely, yes. Flow design is low-code and the monitoring dashboards show volume, errors and reprocessing without requiring anyone to read a technical log, so changes to mapping, fields and segmentation rules stay with the marketing team. IT remains in the loop for what is theirs — approving credentials, granting access to internal systems and setting data policy — but stops being a queue for every campaign change.</p>',
			),
			'mkt-ipaas-vs-nativas'       => array(
				'What is the difference between an iPaaS and the native integrations of marketing automation platforms?',
				'<p>Native integrations handle the pair of systems they were built for, with the mapping the vendor chose to offer. An iPaaS works in the middle: it connects marketing, CRM, ERP, paid media and analytics with the same logic, lets you transform and enrich data in transit, applies your own deduplication rules and keeps the whole history auditable in one place. In practice the native option is enough while the ecosystem is small; the iPaaS is what supports growth without multiplying point-to-point connections.</p>',
			),
			'mkt-criterios-plataforma'   => array(
				'What should I look at when choosing an integration platform for Marketing?',
				'<p>Five points are worth checking: connector coverage for the tools already in use; the ability to process launch peaks and seasonal campaigns without losing a message; how much autonomy the marketing team gains to operate without raising a ticket; visibility over errors and reprocessing; and how personal data is handled, including where it travels and where it is stored. Total cost counts too — beyond the licence, think about who will operate and monitor the platform day to day.</p>',
			),
			'mkt-lgpd-gdpr'              => array(
				'How does the platform keep LGPD and GDPR compliance while data is in transit?',
				'<p>Data travels encrypted end to end and the connection to internal systems runs through an agent that communicates outbound, with no inbound firewall ports exposed. Every flow records who accessed what and when, which supports the audit and erasure requests both laws provide for. Sensitive fields can be masked or simply never travel, and consent and opt-out rules are applied in the flow itself, so a contact who withdrew permission stops being distributed to the other platforms.</p>',
			),

			// Financeiro.
			'fin-tempo-erp'              => array(
				'How long does it take to integrate SAP, Oracle or NetSuite?',
				'<p>All three already have ready-made connectors, so the schedule depends less on development and more on access to the environment. Common finance flows — trial balance, journal entries, accounts payable — usually ship within weeks, counted from credential approval and sign-off on the design by the accounting team. What stretches the timeline is heavy ERP customisation and a chart of accounts that differs between units, not the connection itself.</p>',
			),
			'fin-autonomia-financeiro'   => array(
				'Can the finance team follow the integrations without IT?',
				'<p>Yes. Day-to-day monitoring — did the overnight batch run, how many entries came through, which record failed and why — sits in an operations dashboard the finance team accesses directly, with reprocessing of whatever failed without raising a ticket. What stays with IT is the structural change: building a new flow, changing a credential or altering a business rule.</p>',
			),
			'fin-nativa-vs-ipaas'        => array(
				'What is the difference between native ERP integrations and an iPaaS?',
				'<p>A native integration handles the pair of systems it was built for, but every new endpoint becomes a separate project, with its own rules, its own log and its own maintenance. An iPaaS puts a single layer between all the systems: transformation rules, execution history, error handling and access governance live in one place, and a new business unit reuses what already exists instead of starting over.</p>',
			),
			'fin-criterios-plataforma'   => array(
				'What should I look at when choosing an integration platform for Finance?',
				'<p>Start with traceability: every movement needs a complete record of what came in, what went out and who changed it, because that is what supports the audit. Then check the connector catalogue for the ERPs and banks you already use, the behaviour on failure (reprocessing without duplicating an entry), role-based access control and where execution happens — inside your own infrastructure, when corporate policy requires it. Finally, look at how it evolves: financial integration changes constantly, and needing a new project for every adjustment gets expensive.</p>',
			),
			'fin-atualizacao-apis'       => array(
				'How do ERP API updates affect the integrations?',
				'<p>The impact stays contained in the connection layer. When a vendor publishes a new version, it is the connector that gets updated — the flows, the rules and the destinations stay as they are. Changes announced in advance are certified in a separate environment before going live; when something breaks without warning, messages are held and reprocessed after the fix, with no entry lost and none duplicated.</p>',
			),
		);
	}

	/**
	 * FAQ das soluções de Indústria.
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function faq_en_industria() {
		return array(
			// Serviços Financeiros.
			'sf-fin-core-banking'        => array(
				'How long does it take to integrate a core banking system?',
				'<p>The first flows usually go live in weeks, not months. The timeline depends on the core in use, the data volume and the security checks required, but the rollout happens in stages: the critical integrations go first and the rest follow in later waves, without holding up the operation.</p>',
			),
			'sf-fin-legados'             => array(
				'Does CLI work with legacy systems?',
				'<p>Yes. Beyond REST and SOAP APIs, the platform connects to databases, files, message queues and internal services that expose no API at all. For on-premises environments the communication runs through an agent installed inside the corporate network, with no inbound firewall ports opened.</p>',
			),
			'sf-fin-open-finance'        => array(
				'How do the integrations keep up with Open Finance?',
				'<p>The integrations sit on a governed API layer, with versioning, access control and end-to-end traceability. That lets you expose and consume partner services at the pace the regulation and the Open Finance phases move, without rewriting the architecture at every change.</p>',
			),
			'sf-fin-dados-ia'            => array(
				'Can legacy data be used in AI projects?',
				'<p>Yes. The integrations normalise and connect information that is scattered across systems today, leaving it in a reliable, traceable shape. It is that prepared set of data that feeds intelligent agents, decision engines and advanced analysis.</p>',
			),
			'sf-fin-dados-sensiveis'     => array(
				'How does CLI protect sensitive data during integrations?',
				'<p>Data travels encrypted, credentials are kept in a vault and every access is logged for audit. The operation follows the compliance standards of the platform — SOC 2, ISO 27001, PCI DSS and LGPD/GDPR, among others — and the flows are designed to expose only the information each system actually needs.</p>',
			),

			// Manufatura.
			'mf-ipaas-vs-sap'            => array(
				'What is the difference between an iPaaS and SAP Integration Suite or SAP MII?',
				'<p>SAP Integration Suite and SAP MII handle what starts and ends inside the SAP world very well. An iPaaS treats integration as an independent layer: the same environment connects SAP S/4HANA, MES, WMS, Salesforce, industrial systems and cloud services, with a single view of governance and monitoring. In practice the two coexist — the iPaaS takes on the flows that cross system boundaries and stops every new project from becoming another point-to-point integration.</p>',
			),
			'mf-ot-nuvem-seguranca'      => array(
				'Can industrial equipment be connected to the cloud securely?',
				'<p>Yes. Communication with the industrial environment runs through an agent installed inside the network itself, which opens the connection outbound — no inbound firewall ports are needed. On top of that sits a zero-trust architecture: data encrypted in transit, credentials in a vault and every access logged, with each flow seeing only the information it needs.</p>',
			),
			'mf-mulesoft'                => array(
				'Can CLI Connect replace platforms such as MuleSoft?',
				'<p>Yes, that kind of replacement is a common project scenario. The assessment maps the existing integrations, the volume processed and the governance requirements, and the migration happens in waves: the critical flows go first and the rest follow in stages, with both environments running in parallel until the cutover. The gain is usually in maintenance cost and in how quickly new flows can be built.</p>',
			),
			'mf-compliance-industrial'   => array(
				'Does the platform meet industrial compliance requirements?',
				'<p>The platform operates under the security and privacy standards listed on this page — SOC 2, ISO 27001, ISO 27701, ISO 27018, PCI DSS and GDPR/LGPD, among others. For industry, what usually matters most is traceability: every flow execution is logged, with version history and an audit trail, which supports quality and process validation requirements.</p>',
			),
			'mf-iot-volume'              => array(
				'How does the platform handle large volumes of IoT sensor data?',
				'<p>Processing is elastic and works as a continuous stream, with queues that absorb collection peaks without losing a message. Instead of dumping raw data into the destination, the flows filter, aggregate and normalise it on the way — so only what has analytical value reaches the data platforms and the AI models, cutting storage cost and response time.</p>',
			),

			// Logística (3PL).
			'lg-onboarding-cliente'      => array(
				'How long does it take to onboard a new customer?',
				'<p>The timeline depends on how many systems are in the flow, but the gain comes from reuse: the connectors for ERPs and WMS already exist and are reused from one contract to the next. In practice, what used to be an integration project from scratch becomes the configuration of a flow that is already proven — that is what underpins the 50% reduction in partner and system integration time mentioned on this page.</p>',
			),
			'lg-avaliar-plataforma-3pl'  => array(
				'What should you look for in a platform for 3PL operators?',
				'<p>Three points usually decide it: whether the platform reuses integrations across customers or forces you to start from scratch with every contract; whether it governs cloud systems and systems installed on the customer infrastructure in the same environment; and whether the commercial model follows seasonal peaks without requiring capacity contracted all year round. It is also worth looking at the audit trail, since the operator is accountable for third-party data.</p>',
			),
			'lg-erp-on-premises'         => array(
				'Does the platform connect to on-premises ERPs?',
				'<p>Yes. The connection to ERPs and WMS installed on the customer network runs through an agent inside that infrastructure, which opens the communication outbound — with no inbound firewall ports exposed. Cloud and on-premises flows sit under the same governance and monitoring environment.</p>',
			),
			'lg-custo-alto-volume'       => array(
				'How does pricing work for high-volume operations?',
				'<p>Sizing is based on the volume processed and the number of active integrations, not the number of users. Because the flows filter and aggregate data on the way, the cost of large operations tends to grow less than proportionally to the number of orders and events, and seasonal peaks are absorbed by elastic processing.</p>',
			),
			'lg-multiplas-transportadoras' => array(
				'Can several carriers be integrated without building one integration per carrier?',
				'<p>Yes — it is one of the use cases on this page. Instead of a dedicated connection per carrier, the integration is centralised: pickup, tracking and delivery go through a common flow and each carrier is just another configuration. Adding a new one stops being a development project.</p>',
			),

			// Software (ISV).
			'isv-tempo-primeira-integracao' => array(
				'How long does it take to build a native integration with Salesforce or SAP?',
				'<p>The first integration usually goes live in about five days. The gain comes from not starting from scratch: the connectors for Salesforce, SAP and other enterprise systems already exist, and the work is concentrated on mapping fields and business rules in a low-code environment. The next integrations are faster still, because they reuse the components built for the first one.</p>',
			),
			'isv-mudanca-api-parceiro'   => array(
				'What happens when a partner API changes?',
				'<p>The update happens in the integration layer, not inside your product. Because the connector is maintained on the platform and shared by every customer using it, the change is applied once and covers the whole base — instead of becoming a fix per customer. Centralised monitoring shows which flows were affected before it reaches the end user.</p>',
			),
			'isv-isolamento-multi-tenant' => array(
				'How does data isolation work in multi-tenant environments?',
				'<p>Each customer operates with their own credentials and execution environment, and a flow only ever sees the data of the tenant it belongs to. Where the scenario requires it, execution happens inside the customer infrastructure, with no VPN or open ports — the sensitive data never leaves their perimeter, and the central dashboard receives only the execution records.</p>',
			),
			'isv-custo-conectores-internos' => array(
				'What does it really cost to maintain connectors built in house?',
				'<p>The visible cost is building them; what weighs is maintaining them. Every in-house connector becomes proprietary code that has to keep up with API, authentication and volume changes, and that effort grows with your customer base. With reusable integrations, the product team stops maintaining individual connectors and the operation scales with platform usage.</p>',
			),
			'isv-cargas-elevadas'        => array(
				'Does the platform support very high processing loads?',
				'<p>Yes. Processing is elastic and uses queues that absorb peaks without losing a message, which means the same environment serves a small customer and an operation with millions of executions a month. The operational dashboard tracks volume, latency and failures per customer, and capacity follows usage with no need to rewrite the flows.</p>',
			),

			// Varejo.
			'vj-composable-commerce'     => array(
				'Why is integration essential to a composable commerce strategy?',
				'<p>Composable commerce swaps the single platform for hand-picked pieces — storefront, cart, search, payment, OMS — and that is exactly what shifts the weight onto the integration layer. Without it, every new piece becomes a point-to-point connection to all the others. With an integration layer in the middle, each system talks once to that layer, and swapping a component stops meaning rebuilding the whole architecture.</p>',
			),
			'vj-experiencia-cliente'     => array(
				'How does integration improve the customer experience?',
				'<p>Most of the friction shoppers feel comes from data that does not line up: stock that differs between store and site, an order the service team cannot see, a promotion that only works in one channel. When sales, service, stock and logistics share the same up-to-date information, the journey is consistent at every touchpoint — and the service team answers with the full history at hand.</p>',
			),
			'vj-cadeia-suprimentos'      => array(
				'How can supply chain uncertainty be made less damaging?',
				'<p>By shortening the gap between what happens in the chain and what the operation can see. With suppliers, ERP, WMS and carriers integrated, a stockout, a supply delay or a change of lead time shows up while there is still time to react — move stock between stores, call an alternative supplier or reschedule replenishment — instead of becoming a surprise at the month-end close.</p>',
			),
			'vj-ultima-milha'            => array(
				'Does CLI Connect help with last-mile optimisation?',
				'<p>Yes. The platform connects the order to logistics systems, carriers and routing engines, so the choice of fulfilment origin, carrier and route accounts for real stock, promised lead time and cost. Tracking comes back along the same path and feeds both the customer updates and the operational indicators, with no spreadsheet in between.</p>',
			),
			// Seguros.
			'sg-prazo-guidewire-duck-creek' => array(
				'How long does it take to integrate Guidewire or Duck Creek?',
				'<p>The timeline depends on how many processes go into the first wave, not on the size of the core. Because the connection is made through an integration layer on top of existing APIs, a well-scoped flow — issuing a policy or opening a claim, say — usually ships in weeks rather than months. The usual path is to start with a high-volume process, put it into production and keep widening from there.</p>',
			),
			'sg-plataforma-vs-conectores' => array(
				'What is the advantage of a platform over native connectors?',
				'<p>Native connectors handle one pair of systems well, but every new endpoint becomes its own project, with its own monitoring and its own maintenance. A platform treats integration as a single layer: the same environment connects the insurance core, the CRM, the broker portals and cloud services, with centralised governance, versioning and audit trail. The gain shows up as the number of integrations grows.</p>',
			),
			'sg-criterios-escolha'       => array(
				'What should insurers look at when choosing an integration platform?',
				'<p>Four points usually decide it: whether the platform talks to the core systems of the market without bespoke development; whether it meets the regulatory requirements for handling confidential data; whether it logs every execution in an auditable way; and whether the internal team can build new flows without depending on a third party. The fifth point, less often mentioned, is the cost of keeping the integrations alive over the years.</p>',
			),
			'sg-modernizar-sem-trocar-core' => array(
				'Can the operation be modernised without replacing the core system?',
				'<p>Yes — that is precisely the point of this approach. The core stays the source of truth for policies and claims, and the integration layer exposes that data to the digital channels, the CRM and partners. In practice the insurer launches new products and experiences on the system it already has, without carrying the risk and the timeline of a full replacement.</p>',
			),
			'sg-open-insurance'          => array(
				'How does the platform meet Brazilian Open Insurance requirements?',
				'<p>Open Insurance requires exposing and consuming data through standardised APIs, with customer consent and traceability for every exchange. The platform covers that design: it publishes APIs to the standards set by SUSEP, controls authentication and the scope of each consent, and keeps a record of every call. Regulatory compliance therefore rests on the same layer that already connects the internal systems.</p>',
			),

			// Hotelaria e Turismo.
			'ht-pms-legado'              => array(
				'Can legacy PMS installed on site be integrated?',
				'<p>Yes. Older PMS running on the property server are still the most common case in hospitality, and there is no need to replace them to integrate. The connection runs through an agent installed inside the hotel network, which talks to the system through whatever it already offers — database, file, web service or queue — and opens the communication outbound, with no inbound firewall ports exposed. The PMS stays as it is and starts feeding the other systems.</p>',
			),
			'ht-pos-fidelidade'          => array(
				'How can POS systems be integrated with the loyalty programme in real time?',
				'<p>Every charge recorded at the POS — restaurant, bar, spa, minibar — becomes an event the platform sends straight to the loyalty programme, already matched to the guest profile through the active booking. The return path is automatic too: balance, tier and benefits flow back to the POS and the PMS, so the discount or the perk shows up during the same interaction, without the operator checking another system.</p>',
			),
			'ht-tempo-producao'          => array(
				'How long does it take to put an integration into production?',
				'<p>It depends far less on development time than on access to the systems. Flows using ready-made connectors and a documented API usually ship in weeks; what stretches the schedule is credential approval, certification with the PMS vendor and cleaning up records. Because the components are reusable, the first integration is the slowest and the ones after it build on what already exists.</p>',
			),
			'ht-alta-demanda'            => array(
				'Does the platform handle large booking volumes at peak times?',
				'<p>Yes, and the peak is exactly what the architecture was designed for. Processing is elastic and queue-based, so a public holiday, high season or a flash promotion lengthens the queue without breaking the flow or losing a message. If a destination system slows down or goes offline, messages are held and reprocessed automatically when it returns, preserving the event order for each booking.</p>',
			),
			'ht-franquias-padronizacao'  => array(
				'How can integrations be standardised across franchises with different systems?',
				'<p>The standardisation happens in the middle, not at the endpoints. You define a single format for booking, guest and charge, and each property only gets the piece that translates its own system into that format — the rest of the flow is the same across the group. A new property joins by reusing the model, and whoever runs the group sees every property through the same indicators, even with a different PMS in each one.</p>',
			),

			'vj-visao-360'               => array(
				'What are the benefits of building a 360° customer view?',
				'<p>Bringing purchases, service contacts, returns and marketing interactions into a single profile changes what the operation can do: recommendations based on real history, campaigns that do not push a product the customer already bought, service that does not ask for the same information twice, and a reliable read on repeat business and customer value over time.</p>',
			),
		);
	}
}
