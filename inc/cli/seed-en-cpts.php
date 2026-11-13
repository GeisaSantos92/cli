<?php
/**
 * Seed — texto em inglês dos CPTs de conteúdo e dos menus.
 *
 * Agentes, eventos, cases e as FAQ gerais da home. Catálogos de logo
 * (`cli_cliente`, `cli_integracao`, `cli_selo`) ficam de fora de propósito:
 * logo não tem idioma, e habilitá-los no Polylang só faria a esteira sumir do
 * site em inglês.
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
 * Conteúdo em inglês dos CPTs e dos menus.
 */
trait Cliconnect_Seed_En_Cpts {

	/**
	 * Agentes de IA: slug do seed => [título, descrição].
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function agentes_en() {
		return array(
			'copiloto-de-vendas-b2b'                            => array(
				'B2B Sales Copilot',
				'Helps reps recommend products, check stock and apply the pricing rules of key accounts.',
			),
			'conciliacao-fiscal-automatizada'                   => array(
				'Automated Tax Reconciliation',
				'Captures incoming invoices, validates withheld taxes and flags discrepancies before closing.',
			),
			'assistente-de-pos-venda-e-logistica'               => array(
				'After-Sales and Logistics Assistant',
				'Tracks complex deliveries, predicts delays and proactively tells the end customer where the order is.',
			),
			'analista-de-credito-e-compliance'                  => array(
				'Credit and Compliance Analyst',
				'Assesses the risk of new customers by cross-checking internal data with credit bureaus to release orders.',
			),
			'triagem-de-suporte-nivel-1'                        => array(
				'Level 1 Support Triage',
				'Automated order tracking, dynamic freight quoting and status updates for the end customer.',
			),
			'automacao-da-sincronizacao-de-pedidos'             => array(
				'Order Sync Automation',
				'Passes sales instantly from the commercial front end to billing, removing manual errors.',
			),
			'automacao-do-agendamento-de-consulta'              => array(
				'Appointment Scheduling Automation',
				'Books appointments with patients over WhatsApp, updating medical calendars in real time.',
			),
			'sincronizacao-automatica-de-estoque'               => array(
				'Automatic Inventory Sync',
				'Keeps the central warehouse balance in sync with the online stores so nothing is sold out of stock.',
			),
			'simulacao-dos-novos-impostos-da-reforma-tributaria' => array(
				'Tax Reform Simulation',
				'Analyses billing history and simulates the tax impact of moving to the new Brazilian tax model.',
			),
		);
	}

	/**
	 * Eventos automáticos: slug do seed => [título, descrição].
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function eventos_en() {
		return array(
			'informacoes-sempre-sincronizadas'             => array(
				'Information always in sync',
				'Avoid discrepancies between systems and make sure every area works from the same data.',
			),
			'respostas-mais-rapidas-ao-negocio'            => array(
				'Faster responses for the business',
				'Updates happen automatically, with no queues, reconciliation or IT involvement.',
			),
			'mais-visibilidade-sobre-processos-criticos'   => array(
				'More visibility over critical processes',
				'Monitor critical processes in real time and spot situations that need attention straight away.',
			),
			'se-adapte-a-mudancas-regulatorias'            => array(
				'Adapt to regulatory change',
				'Turn regulatory changes into automatic actions, cutting risk and speeding up how fast the company adapts.',
			),
		);
	}

	/**
	 * Cases: slug do seed => título, resumo, conteúdo e campos.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	protected function cases_en() {
		$resumo_padrao = 'Rolling out CLI Connect made it possible to integrate systems, automate events and gain visibility over the whole operation.';

		return array(
			'panasonic'           => array(
				'titulo'   => 'Cut SAP S/4HANA integration response time by 97%',
				'resumo'   => 'With CLI as its integration partner, Panasonic connected the new SAP S/4HANA to its satellite systems, brought response times down from seconds to milliseconds and freed up 40% of the IT team\'s time to innovate.',
				'conteudo' => '<p>With CLI as its integration partner, Panasonic connected the new SAP S/4HANA to its satellite systems, brought response times down from seconds to milliseconds and freed up 40% of the IT team\'s time to innovate.</p>',
				'campos'   => array(
					'citacao'          => 'I used to talk about response times in seconds. Today I\'m talking about milliseconds.',
					'cargo'            => 'Head of Digital and Technology at Panasonic',
					'metrica_numero'   => '-97%',
					'metrica_texto'    => 'in integration response time between SAP S/4HANA and the satellite systems',
					'metrica_numero_2' => '40%',
					'metrica_texto_2'  => 'of the IT team\'s time freed up for new technologies',
					'desafio_titulo'   => 'A new SAP had to talk to the entire ecosystem',
					'desafio_texto'    => '<p>In 2023, Panasonic Brazil decided to speed up its digital transformation. The starting point was SAP for Pana, the programme that brought SAP S/4HANA to the company as a greenfield implementation, built from scratch alongside a complete review of its processes. Focused on white goods and consumer electronics retail, with plants in Manaus (AM), Extrema (MG) and São José dos Campos (SP), the company relies on many satellite systems that constantly exchange data with the ERP and between its sites.</p><p>When reassessing the environment, the team identified the systems where the bottlenecks were concentrated. The challenge was to integrate the new S/4HANA with this ecosystem without touching the satellite systems, in an architecture that broke with market standards: no SAP integrator, and an integration platform, a new technology for the company, acting as the main integrator. For a Japanese company with a strongly process-driven culture, the choice had to deliver a real performance gain.</p>',
					'solucao_titulo'   => 'The people who understood the need were the ones who delivered it',
					'solucao_texto'    => '<p>CLI came to Panasonic on Salesforce\'s recommendation and took part in the evaluation of the integration platform: it presented cases, showed the gains the technology could bring and how to speed up the implementation. From that conversation, the project took on an architecture of its own, with the integration platform as the main integrator and all communication between S/4HANA and the satellite systems running directly through it. The work focused on interfacing: each integration was designed around the needs of the process, without requiring changes to the existing systems.</p><p>According to Fabiano, the difference was proximity. With the direct involvement of Bruno, founding partner of CLI Connect, the person who mapped the need was the one who designed the solution and implemented it alongside the Panasonic team, with no hand-off between sales and delivery. Over almost a year of project work, the teams adjusted the pace together, paid closer attention to the most complex processes and, after go-live, went back into the environment to map and take on new opportunities. Along the way, CLI trained the Panasonic team to maintain the interfaces on its own.</p>',
					'impacto_titulo'   => 'Millisecond responses and a more strategic IT team',
					'impacto_texto'    => '<p>Six months after go-live, with the environment still stabilising, response time between S/4HANA and the satellite systems fell by 97%, and each professional\'s daily routine gained 21% to 25% in performance. Processes flowing between Manaus, Extrema and São José dos Campos became faster, and the IT team gained a 40% time margin, reinvested in new technologies instead of reducing headcount. With an AMS team specialised in the integration platform, Panasonic maintains its own interfaces, and today it is the business side that comes to IT to make new solutions happen.</p>',
				),
			),
			'moura' => array(
				'titulo'   => 'Gets through the "Black Friday" of every month-end with no stoppages in revenue',
				'resumo'   => 'With CLI Connect as its partner since the project was conceived, Grupo Moura connected the new Salesforce CRM to its ERP through a centralised middleware, gained traceability across all integrations and watched service tickets fall month after month.',
				'conteudo' => '<p>With CLI Connect as its partner since the project was conceived, Grupo Moura connected the new Salesforce CRM to its ERP through a centralised middleware, gained traceability across all integrations and watched service tickets fall month after month.</p>',
				'campos'   => array(
					'citacao' => 'I joke that every month we have a Black Friday. Getting through it smoothly is what we look for in solutions and in partners that support us.',
					'cargo' => 'Systems Manager at Grupo Moura',
					'metrica_numero' => '0 stoppages',
					'metrica_texto' => 'in revenue during the month-end volume peak',
					'metrica_numero_2' => '~1 year',
					'metrica_texto_2' => 'of joint development, from conception to the first go-live',
					'desafio_titulo' => 'Fragile integrations in an operation that cannot stop',
					'desafio_texto' => '<p>Known as Baterias Moura, Grupo Moura works with every kind of energy storage, not only car batteries. For about seven years the company has been going through a technology restructuring in search of reliable, robust systems. The most recent stage was the customer journey: with the acquisition of the Salesforce CRM, starting with the sales force, the new platform had to be connected to the ERP.</p><p>Until then, systems communicated bank to bank, by exchanging files, with manual processes and rework. When something failed, finding the root cause took time, and information that did not reach the other side left data inconsistent, including in the numbers taken to the board. The risk grew at month-end, when the distribution operation concentrates a very high volume, a monthly "Black Friday" in which any stoppage means losing revenue.</p>',
					'solucao_titulo' => 'A single orchestrator, built together from the start',
					'solucao_texto' => '<p>Moura was looking for an established partner, with good implementation projects and approved by Salesforce. After talking with clients of different vendors, it chose CLI Connect. The solution was a middleware acting as the single orchestrator between Salesforce and the ERP: one channel, with visibility of where each piece of information passes and logs to trace any failure. The platform, recognised worldwide, was also approved by the information security area of Grupo Moura.</p><p>CLI Connect took part in every stage, from conception to architecture, development and best practices, over about a year of building up to the first go-live, and it still follows the operation today. Support goes beyond tickets: with Bruno Martins, founding partner of CLI Connect, and architects always close by, Moura consults CLI Connect before each new integration to assess volume and the best architecture. Together with the platform vendor, the teams are already designing the architecture for the next five years of growth.</p>',
					'impacto_titulo' => 'An operation cruising smoothly',
					'impacto_texto' => '<p>Since the first go-live, service desk tickets, which were natural at the start with a new tool for the company and its users, have fallen steadily. With end-to-end traceability, IT started to anticipate problems instead of reacting to them, and the continuous improvement cycle gained data to run on. Logistics, finance and the sales force receive fast, complete and reliable information, and the month-end peak goes through with no stoppages in revenue. All of this with a lean internal team, supported by CLI Connect specialists, and a fixed cost that removes surprises from the budget.</p>',
				),
			),
			'petroreconcavo' => array(
				'titulo'   => 'Integrated SAP into a 24/7 operation with no stoppages in almost two years',
				'resumo'   => 'With CLI Connect by Boomi, PetroRecôncavo centralised the integrations between the new SAP S/4HANA and its satellite systems, eliminated duplicate data entry between systems and now closes production on the 3rd day of the month.',
				'conteudo' => '<p>With CLI Connect by Boomi, PetroRecôncavo centralised the integrations between the new SAP S/4HANA and its satellite systems, eliminated duplicate data entry between systems and now closes production on the 3rd day of the month.</p>',
				'campos'   => array(
					'citacao' => 'With the systems integrated, in almost two years of operation we have never stopped. Production runs 24/7, and the areas stay integrated without depending on IT.',
					'cargo' => 'Systems Coordinator at PetroRecôncavo',
					'metrica_numero' => '0 stoppages',
					'metrica_texto' => 'in integrations over almost two years of 24/7 operation',
					'metrica_numero_2' => '3 days',
					'metrica_texto_2' => 'to close production, which used to take until the 8th day',
					'desafio_titulo' => 'A critical operation that cannot stop',
					'desafio_texto' => '<p>A pioneer among independent onshore oil and gas producers, PetroRecôncavo operates 57 concessions and over 2,000 wells in Bahia, Rio Grande do Norte and Sergipe, producing around 26,000 barrels a day. It is a 24/7, geographically dispersed operation in which any stoppage means barrels that are not produced. In 2022 the company began replacing its ERP and, with the Evolution project, implemented SAP S/4HANA in 2024.</p><p>SAP, however, does not work alone. Most of the satellite systems were not integrated, and the main one, IFS, used for asset management, had only simple integrations, without the robustness and control needed. Connections were point-to-point, with no orchestration or monitoring, and much data was typed into one system and re-typed into another, with back-and-forth emails and spreadsheets. Monthly closing took 10 to 11 business days, and the hybrid infrastructure, with on-premise and cloud environments, required a technology able to integrate both.</p>',
					'solucao_titulo' => 'A central middleware and a partner alongside',
					'solucao_texto' => '<p>CLI was alongside PetroRecôncavo from the choice of technology. During the selection for the Evolution project, it helped compare integrators until reaching Boomi, the best fit in cost-benefit, for the hybrid infrastructure and for the medium- and long-term plans. With CLI Connect by Boomi, the company gained a standard, centralised middleware, with governance, monitoring and traceability for SAP and non-SAP integrations. Ready-made connectors sped up the build, and features such as Event Streams, for messaging, and Data Hub, for master data, extend the use of the platform.</p><p>The knowledge of the CLI Connect by Boomi team and of the systems the company already used, such as IFS, was decisive in mapping the critical processes and setting the quality criteria for the deliveries. The fixed monthly cost gave predictability to the budget, both for operations and for new implementations. Throughout the project, the consultants transferred knowledge to the internal team, which also trained through Boomi Academy, and CLI remains a partner in support, with prompt service for incidents and change management.</p>',
					'impacto_titulo' => 'Faster closings and zero rework',
					'impacto_texto' => '<p>In almost two years of operation, the integrations never stopped, and duplicate data entry between systems came to an end, eliminating the risk of inconsistent data. Production closing, which used to reach the 8th day, now ends practically on the 3rd, and monthly closing dropped from 10 to 11 business days to the 5th business day. Travel reimbursements, production measurement, royalties, commercialisation and easements now run integrated, and the areas absorbed more volume without growing headcount. With a transport project under way and tests with Boomi AI agents, PetroRecôncavo keeps expanding its integration architecture with CLI.</p>',
				),
			),
			'moura-vendas' => array(
				'titulo'   => 'Gets through the "Black Friday" of every month-end with no stoppages in revenue',
				'resumo'   => 'With CLI Connect as its partner since the project was conceived, Grupo Moura connected the new Salesforce CRM to its ERP through a centralised middleware, gained traceability across all integrations and watched service tickets fall month after month.',
				'conteudo' => '<p>With CLI Connect as its partner since the project was conceived, Grupo Moura connected the new Salesforce CRM to its ERP through a centralised middleware, gained traceability across all integrations and watched service tickets fall month after month.</p>',
				'campos'   => array(
					'metrica_numero' => '~1 year',
					'metrica_texto' => 'of joint development, from conception to the first go-live',
				),
			),
			'petroreconcavo-dados' => array(
				'titulo'   => 'Integrated SAP into a 24/7 operation with no stoppages in almost two years',
				'resumo'   => 'With CLI Connect by Boomi, PetroRecôncavo centralised the integrations between the new SAP S/4HANA and its satellite systems, eliminated duplicate data entry between systems and now closes production on the 3rd day of the month.',
				'conteudo' => '<p>With CLI Connect by Boomi, PetroRecôncavo centralised the integrations between the new SAP S/4HANA and its satellite systems, eliminated duplicate data entry between systems and now closes production on the 3rd day of the month.</p>',
				'campos'   => array(
					'metrica_numero' => '0 stoppages',
					'metrica_texto' => 'in integrations over almost two years of 24/7 operation',
				),
			),
			'andamento-brz'     => array(
				'titulo'  => 'BRZ Empreendimentos',
				'resumo'  => '',
				'campos'  => array(),
			),
			'andamento-legrand' => array(
				'titulo'  => 'Legrand',
				'resumo'  => '',
				'campos'  => array(),
			),
			'andamento-sbc'     => array(
				'titulo'  => 'Brazilian Society of Cardiology',
				'resumo'  => '',
				'campos'  => array(),
			),
		);
	}

	/**
	 * FAQ gerais da home: slug do seed => [pergunta, resposta].
	 *
	 * As FAQ de cada landing de solução são traduzidas junto com a solução.
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function faq_en() {
		return array(
			'o-que-exatamente-o-cli-connect-faz'                     => array(
				'What exactly does CLI Connect do?',
				'<p>CLI Connect brings the systems your company already uses — ERP, e-commerce, CRM, tax and logistics systems — into a single governed structure. Beyond the integrations, you get automatic events that trigger actions between systems and a library with more than 30,000 ready-made automations.</p>',
			),
			'quanto-tempo-demora-o-servico'                          => array(
				'How long does it take?',
				'<p>Most integrations are ready within 5 days, because we start from connectors and recipes that are already proven. Projects with very specific business rules go through a quick assessment before entering the delivery queue.</p>',
			),
			'e-se-algo-parar-de-funcionar'                           => array(
				'What if something stops working?',
				'<p>The monitoring is ours, not yours. The team follows the integrations in real time and, in most cases, is already fixing the problem before you notice. Support is human and available through the portal, email and WhatsApp.</p>',
			),
			'vou-depender-da-cli-para-tudo'                          => array(
				'Will I depend on CLI for everything?',
				'<p>No. The whole operation is documented and visible to your team in the dashboard, and the integrations run on the Boomi platform — a global market standard. You keep governance and choose how much you want to delegate.</p>',
			),
			'como-funciona-o-modelo-de-contratacao'                  => array(
				'How does the contract model work?',
				'<p>It is a flat monthly fee, with unlimited integrations and managed service included. There is no charge per call volume or per new integration: the more your operation grows, the more the model works in your favour.</p>',
			),
			'posso-criar-minhas-proprias-integracoes-na-cli-connect' => array(
				'Can I build my own integrations in CLI Connect?',
				'<p>Yes. Beyond the library of more than 30,000 ready-made automations, the Boomi platform lets your team build custom connectors and flows. CLI Connect helps structure and document those integrations so they follow best practice for governance and performance.</p>',
			),
		);
	}
}
