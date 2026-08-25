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
	 * Traduz agentes, eventos, cases e as FAQ gerais.
	 *
	 * @return void
	 */
	protected function traduzir_cpts() {
		$total = 0;

		foreach ( $this->agentes_en() as $slug => $dados ) {
			$total += $this->traduzir_post(
				'agente:' . $slug,
				'cli_agente',
				array( 'post_title' => $dados[0] ),
				array( 'descricao' => $dados[1] )
			) ? 1 : 0;
		}

		foreach ( $this->eventos_en() as $slug => $dados ) {
			$total += $this->traduzir_post(
				'evento:' . $slug,
				'cli_evento',
				array( 'post_title' => $dados[0] ),
				array( 'descricao' => $dados[1] )
			) ? 1 : 0;
		}

		foreach ( $this->cases_en() as $slug => $dados ) {
			$total += $this->traduzir_post(
				'case:' . $slug,
				'cli_case',
				array(
					'post_title'   => $dados['titulo'],
					'post_excerpt' => $dados['resumo'],
					'post_content' => $dados['conteudo'] ?? '',
				),
				$dados['campos'] ?? array()
			) ? 1 : 0;
		}

		foreach ( $this->faq_en() as $slug => $dados ) {
			$total += $this->traduzir_post(
				'faq:' . $slug,
				'cli_faq',
				array(
					'post_title'   => $dados[0],
					'post_content' => $dados[1],
				)
			) ? 1 : 0;
		}

		WP_CLI::log( sprintf( '  CPTs em inglês: %d itens.', $total ) );
	}

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
				'titulo'   => 'Accelerated insight production by 10%',
				'resumo'   => $resumo_padrao,
				'conteudo' => '<p>Panasonic connected ERP, e-commerce and tax systems in a single integration structure, with automatic events to synchronise information and update critical processes.</p>',
				'campos'   => array(
					'citacao'          => 'With CLI Connect we restructured our governance and our financial processes.',
					'cargo'            => 'Head of operations at Panasonic',
					'metrica_numero'   => '+85%',
					'metrica_texto'    => 'Less time to roll out new integrations',
					'metrica_numero_2' => '+60%',
					'metrica_texto_2'  => 'Fewer manual operational interventions',
					'desafio_titulo'   => 'Disconnected systems were holding the operation back',
					'desafio_texto'    => '<p>As the company grew, new platforms became part of the operation, including ERP, e-commerce, CRM and logistics systems. Exchanging information between those applications, however, relied on one-off integrations and processes that were barely standardised.</p><p>The team was constantly dealing with inconsistent data, manual updates and the difficulty of following critical business events in real time. Every new request meant more development, adding operational complexity and slowing down the response to the business areas.</p>',
					'solucao_titulo'   => 'A connected operation, ready to evolve',
					'solucao_texto'    => '<p>CLI rolled out a centralised integration architecture using CLI Connect, bringing the main systems of the operation into a single governed structure.</p><p>Beyond the integrations, automatic events were created to synchronise information, send operational notifications and update critical processes. The company also started using a library of ready-made automations, speeding up new requests and removing the need for a separate project per integration.</p>',
					'impacto_titulo'   => 'More agility, predictability and control',
					'impacto_texto'    => '<p>With the new integration architecture, Panasonic cut the time to roll out new connectors dramatically and removed most of the manual steps from the operational process, gaining real-time visibility over the whole data chain.</p>',
				),
			),
			'moura'               => array(
				'titulo' => 'Accelerated insight production by 10%',
				'resumo' => $resumo_padrao,
			),
			'petroreconcavo'      => array(
				'titulo' => 'Accelerated insight production by 10%',
				'resumo' => $resumo_padrao,
				'campos' => array(
					'metrica_numero' => '10%',
					'metrica_texto'  => 'Less time spent on triage',
				),
			),
			'moura-vendas'        => array(
				'titulo' => 'Avoided losing 15% of monthly sales',
				'resumo' => 'Integrating CRM, ERP and the e-commerce platform removed bottlenecks from the sales process and kept the operation running even at peak demand.',
				'campos' => array(
					'metrica_numero' => '15%',
					'metrica_texto'  => 'Of monthly sales preserved through real-time integration',
				),
			),
			'petroreconcavo-dados' => array(
				'titulo' => 'Optimised operations with unified data',
				'resumo' => 'Unifying operational data in a single integration architecture cut rework, removed inconsistencies and sped up decision making.',
				'campos' => array(
					'metrica_numero' => '+40%',
					'metrica_texto'  => 'Faster operational analysis',
				),
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
