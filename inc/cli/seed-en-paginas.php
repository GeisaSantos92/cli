<?php
/**
 * Seed — texto em inglês das páginas.
 *
 * Um método por página, no mesmo desenho de `preencher_*()` do seed em
 * português. Só entram aqui os campos **de texto**: imagens, ícones, números e
 * relações são copiados do original por `traduzir_post()`.
 *
 * O inglês é tradução de trabalho, feita a partir do português aprovado no
 * Figma — precisa de revisão de idioma antes de ir ao ar.
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
 * Conteúdo em inglês das páginas do site.
 */
trait Cliconnect_Seed_En_Paginas {

	/**
	 * Slug em português => slug e título em inglês.
	 *
	 * A ordem é a mesma de `criar_paginas()`, para as duas listas serem
	 * conferidas lado a lado.
	 *
	 * Os slugs precisam ser **diferentes** dos slugs em português: compartilhar
	 * slug entre idiomas é recurso do Polylang Pro; no free o WordPress
	 * acrescenta `-2` em silêncio. Daí `articles`, `cli-connect-platform` e
	 * `cli-signature-service` em vez de repetir o slug do português.
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function paginas_en() {
		return array(
			'home'             => array( 'home-en', 'Home' ),
			'blog'             => array( 'articles', 'Blog' ),
			'contato'          => array( 'contact', 'Contact' ),
			'plataforma'       => array( 'platform', 'Platform' ),
			'cli-connect'      => array( 'cli-connect-platform', 'CLI Connect' ),
			'cli-signature'    => array( 'cli-signature-service', 'CLI Signature' ),
			'solucoes'         => array( 'solutions', 'Solutions' ),
			'integracao-sap'   => array( 'sap-integration', 'SAP Integration' ),
			'sistemas'         => array( 'systems', 'Systems' ),
			'trabalhe-conosco' => array( 'careers', 'Careers' ),
			'privacidade'      => array( 'privacy-policy', 'Privacy Policy' ),
			'termos'           => array( 'terms-of-use', 'Terms of Use' ),
		);
	}

	/* =====================================================================
	   HOME
	   ===================================================================== */

	/**
	 * Campos de texto da home em inglês.
	 *
	 * @return array<string,mixed>
	 */
	protected function texto_en_home() {
		return array(
			'hero_eyebrow'          => 'Powered By Boomi',
			'hero_titulo_destaque'  => 'Unlimited integrations.',
			'hero_titulo'           => 'Predictable cost. No surprises.',
			'hero_subtitulo'        => 'Integrate every one of your systems and put custom AI agents to work across your processes.',
			'hero_botao'            => $this->link_traduzido( 'Book a demo', '/contato/' ),
			'agentes_legenda'       => '30,000+ integrations ready to use',
			'camadas_titulo'        => "Everything you need.\nAt a predictable cost.",
			'camadas_texto'         => "Pay a flat fee and use our integration service as much as you want.\nThe more your operation grows, the more you gain.",
			'camadas_botao'         => $this->link_traduzido( 'See what is included', '/plataforma/' ),
			'boomi_eyebrow'         => 'Global platform',
			'boomi_titulo'          => 'World-class technology with support built for the Brazilian market',
			'boomi_texto'           => '<p>Get the same platform that large global companies use to integrate their systems, with the added advantage of <strong>specialised support for the Brazilian market</strong>, affordable pricing and managed service included.</p>',
			'metrica_1_numero'      => '+200',
			'metrica_1_rotulo'      => 'integrations per week',
			'metrica_2_numero'      => '5 days',
			'metrica_2_rotulo'      => 'until your integration is live',
			'metrica_3_numero'      => '+30k',
			'metrica_3_rotulo'      => 'integrations already built',
			'midia_1_eyebrow'       => 'Enterprise AI',
			'midia_1_titulo'        => 'Build, govern and scale agents',
			'midia_1_texto'         => 'Create specialised agents, connect your systems and follow the whole operation in a single environment.',
			'midia_1_topico_1'      => 'Specialised agents for each area',
			'midia_1_topico_2'      => 'Connected to your company systems',
			'midia_1_topico_3'      => 'Centralised governance and monitoring',
			'midia_2_eyebrow'       => 'In practice',
			'midia_2_titulo'        => "Talk to your data.\nThe agent does the rest.",
			'midia_2_texto'         => 'Ask questions, run processes and get answers grounded in your own operational data.',
			'midia_2_topico_1'      => 'Queries several systems at once',
			'midia_2_topico_2'      => 'Runs flows with no manual step',
			'midia_2_topico_3'      => 'Keeps the full history of the operation',
			'cases_botao'           => $this->link_traduzido( 'See our case studies', '/cases/' ),
			'eventos_eyebrow'       => 'Automatic events',
			'eventos_titulo'        => 'Your operation responds to business changes in real time',
			'compliance_eyebrow'    => 'Compliance & security',
			'compliance_titulo'     => 'We lead the market when it comes to compliance and security',
			'compliance_texto'      => 'Your data, processes and integrations protected by the highest global standards.',
			'integracoes_eyebrow'   => 'Ready-made integrations',
			'integracoes_titulo'    => "Your integration may\nalready be built",
			'integracoes_texto'     => 'We connect SAP, Protheus, VTEX, Mercado Eletrônico, Salesforce, Senior, MV and dozens of other systems. Many of the integrations you need are already in our catalogue.',
			'integracoes_botao'     => $this->link_traduzido( 'Integrate now', '/contato/' ),
			'departamento_1'        => 'Procurement',
			'departamento_2'        => 'Customer Service',
			'departamento_3'        => 'Logistics',
			'departamento_4'        => 'Tax',
			'departamento_5'        => 'Finance',
			'departamento_6'        => 'HR',
			'departamentos_titulo'  => "Integrate every department\nof your company",
			'departamentos_texto'   => 'From finance to customer service, build integrated flows and bring all your company information into a single screen, with no hassle.',
			'departamentos_botao'   => $this->link_traduzido( 'Request a demo', '/contato/' ),
			'prova_texto'           => "500+ companies have already chosen\nto automate their processes",
			'frase_texto'           => 'Your systems talk to each other.',
			'frase_texto_b'         => 'You focus on',
			'frase_destaque'        => 'what matters',
			'suporte_eyebrow'       => 'Support whenever you need it',
			'suporte_titulo'        => 'You are never on your own.',
			'suporte_texto'         => '<p>We offer <strong>human support</strong> for the moments you need it most. Our team monitors, maintains and evolves your integrations. If something goes wrong, we are already fixing it before you notice.</p>',
			'suporte_botao'         => $this->link_traduzido( 'See our support channels', '/contato/' ),
			'blog_titulo'           => 'The latest from our blog',
			'blog_link'             => $this->link_traduzido( 'See all posts', '/blog/' ),
			'faq_eyebrow'           => 'FAQ',
			'faq_titulo'            => 'Frequently Asked Questions',
		);
	}

	/* =====================================================================
	   CONTATO
	   ===================================================================== */

	/**
	 * Campos de texto da página Contato em inglês.
	 *
	 * E-mail, telefone, redes sociais e o ID do formulário CF7 não são texto
	 * traduzível — ficam de fora e são copiados do original.
	 *
	 * @return array<string,mixed>
	 */
	protected function texto_en_contato() {
		$campos = array(
			'ct_clientes_subtitulo' => 'Major companies trust CLI',
			'ct_form_titulo'        => 'Request a proposal for your operation',
			'ct_form_texto'         => 'Ask questions, explore what is possible and find out how CLI can support your operation with integration, automation and enterprise AI.',
		);

		$formulario = $this->criar_form_cf7_en();

		if ( $formulario ) {
			$campos['ct_form_cf7_id'] = (string) $formulario;
		}

		return $campos;
	}

	/* =====================================================================
	   TRABALHE CONOSCO
	   ===================================================================== */

	/**
	 * Formulário do Contact Form 7 em inglês.
	 *
	 * @return int ID do formulário, 0 em caso de falha.
	 */
	protected function criar_form_cf7_en() {
		return $this->criar_form_cf7_traduzido(
			'contact-cli',
			'Contact CLI',
			'<label>Name
[text* ct-nome placeholder "Name"]</label>

<label>Phone
[tel* ct-telefone placeholder "+55 (00) 00000-0000"]</label>

<label>Email
[email* ct-email placeholder "Email"]</label>

<label>Message
[textarea* ct-mensagem placeholder "Type your message"]</label>

[acceptance ct-aceite] By submitting, I agree to receive communications from CLI[/acceptance]

[submit "Send"]',
			array(
				'mail_sent_ok'               => 'Message sent. We will be in touch shortly.',
				'mail_sent_ng'               => 'Something went wrong. Please try again.',
				'validation_error'           => 'Please fill in the required fields before sending.',
				'spam'                       => 'There seems to be a problem with the submission.',
				'accept_terms'               => 'You need to accept the terms to continue.',
				'invalid_required'           => 'This field is required.',
				'invalid_too_long'           => 'This text is too long.',
				'invalid_too_short'          => 'This text is too short.',
				'invalid_date'               => 'Invalid date.',
				'date_too_early'             => 'That date is too early.',
				'date_too_late'              => 'That date is too late.',
				'invalid_number'             => 'Invalid number.',
				'number_too_small'           => 'That number is too small.',
				'number_too_large'           => 'That number is too large.',
				'invalid_email'              => 'Invalid email address.',
				'invalid_url'                => 'Invalid URL.',
				'invalid_tel'                => 'Invalid phone number.',
				'upload_failed'              => 'Upload failed.',
				'upload_file_type_invalid'   => 'Invalid file type.',
				'upload_file_too_large'      => 'That file is too large.',
				'upload_failed_php_error'    => 'There was an error uploading the file.',
				'upload_file_count_exceeded' => 'Too many files.',
				'quiz_answer_not_correct'    => 'That answer is not correct.',
			)
		);
	}

	/**
	 * Title e meta description das páginas, em inglês.
	 *
	 * Chave = slug da página em português (o mesmo do seed base).
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function seo_en() {
		return array(
			'home'             => array(
				'CLI Connect — unlimited integrations, predictable cost',
				'Connect ERP, CRM, e-commerce and cloud in a single integration layer powered by Boomi. Fixed cost, unlimited integrations, expert team.',
			),
			'blog'             => array(
				'CLI Connect blog — systems integration in practice',
				'Articles on systems integration, ERP, CRM and process automation, written by the people who put projects into production.',
			),
			'contato'          => array(
				'Talk to CLI Connect',
				'Tell our team about your integration. We reply quickly by email, phone or WhatsApp.',
			),
			'plataforma'       => array(
				'Integration platform — CLI Connect',
				'One layer to connect ERP, CRM, e-commerce and cloud, with governance, monitoring and more than 300 ready-made connectors.',
			),
			'cli-connect'      => array(
				'CLI Connect — integration as a continuous service',
				'Fixed cost, unlimited integrations and a dedicated team looking after your integration layer — no new project for every connection.',
			),
			'cli-signature'    => array(
				'CLI Signature — a dedicated integration squad',
				'A project manager and an architect dedicated to your integration roadmap, with governance, regular rituals and predictable delivery.',
			),
			'solucoes'         => array(
				'Integration solutions by system and by area',
				'A catalogue of integrations by ERP, CRM, cloud, industry and business area — from SAP to Salesforce, with ready-made connectors.',
			),
			'integracao-sap'   => array(
				'SAP integration — connect SAP to the rest of your operation',
				'Integrate SAP ECC, S/4HANA and Business One with CRM, e-commerce, tax and data systems, without point-to-point development.',
			),
			'sistemas'         => array(
				'Systems integrated by CLI Connect',
				'More than 300 ready-made connectors for ERP, CRM, e-commerce, cloud and data tools. Check whether your system is already on the list.',
			),
			'trabalhe-conosco' => array(
				'Work with us — CLI Connect',
				'Openings, culture and benefits at a team that lives on integrating critical systems. See what working at CLI Connect is like.',
			),
			'privacidade'      => array(
				'Privacy Policy — CLI Connect',
				'How CLI Connect collects, uses and protects the personal data of people who visit the site and hire our services.',
			),
			'termos'           => array(
				'Terms of Use — CLI Connect',
				'Conditions for using the CLI Connect website and services.',
			),
		);
	}

	/**
	 * Strings de opção (Customizer e descrição do site) em inglês.
	 *
	 * @return array<string,string>
	 */
	protected function strings_polylang_en() {
		return array(
			'Portal do Cliente'                                       => 'Client Portal',
			'Acessar Plataforma'                                      => 'Access Platform',
			"Planeje a evolução\ndas suas integrações"                 => "Plan the evolution\nof your integrations",
			'Fale conosco no WhatsApp'                                 => 'Talk to us on WhatsApp',
			'Usamos cookies para melhorar sua experiência. Ao continuar navegando, você concorda com a nossa' => 'We use cookies to improve your experience. By continuing to browse, you agree to our',
			'política de privacidade'                                  => 'privacy policy',
			'Concordar'                                                => 'Agree',
			'Integrações ilimitadas. Custo previsível. Sem surpresas.' => 'Unlimited integrations. Predictable cost. No surprises.',
		);
	}

	/**
	 * Campos de texto da página Trabalhe Conosco em inglês.
	 *
	 * Os campos `*_icone` são chaves de ícone, não texto: ficam de fora e são
	 * copiados do original.
	 *
	 * @return array<string,mixed>
	 */
	protected function texto_en_trabalhe_conosco() {
		return array(
			'hero_eyebrow'         => 'CAREERS',
			'hero_titulo'          => 'Build solutions that move large companies forward.',
			'hero_texto'           => 'At CLI you join a team that connects technologies, simplifies operations and helps companies evolve every day. Work remotely, take on challenging projects and grow alongside people who are passionate about innovation.',
			'hero_botao'           => $this->link_traduzido( 'See our openings', '/trabalhe-conosco/#vagas' ),
			'sobre_titulo'         => 'We are CLI',
			'sobre_texto_1'        => 'With 13 years of history, we are a technology and partnership company that connects culture, people and solutions. Our purpose is to keep turning technical skill into human capability, generating real impact for clients, partners and the world.',
			'sobre_texto_2'        => 'We have a solid track record, with more than 75 active clients and over 500 integrations ready to use. We believe great solutions are built by teams that collaborate, learn constantly and have the autonomy to make things happen.',
			'tc_metrica_1_numero'  => '13',
			'tc_metrica_1_rotulo'  => 'years of history',
			'tc_metrica_2_numero'  => '+80',
			'tc_metrica_2_rotulo'  => 'active clients',
			'tc_metrica_3_numero'  => '30k',
			'tc_metrica_3_rotulo'  => 'integrations already built',
			'tc_frase_parte_1'     => 'Technology connects systems.',
			'tc_frase_parte_2'     => 'But it is people who transform businesses.',
			'valores_eyebrow'      => 'VALUES',
			'valores_titulo'       => 'More than integrating technology, we integrate people',
			'valores_cta'          => $this->link_traduzido( 'See our openings', '/trabalhe-conosco/#vagas' ),
			'valor_1_titulo'       => 'Trust',
			'valor_1_texto'        => 'We act with transparency, safety and respect. We deliver what we promise and build lasting relationships of trust with clients and teams.',
			'valor_2_titulo'       => 'Equality',
			'valor_2_texto'        => 'We give opportunities to everyone who wants to grow, valuing the talent and development of each person regardless of their background.',
			'valor_3_titulo'       => 'Customer Success',
			'valor_3_texto'        => "The client's problem is ours. We solve it with real business knowledge and take pride in every successful delivery.",
			'valor_4_titulo'       => 'Innovation',
			'valor_4_texto'        => 'We encourage new ideas and creativity to anticipate trends and build innovative solutions responsibly.',
			'valor_5_titulo'       => 'Collaboration',
			'valor_5_texto'        => 'We are one team. We share knowledge, achievements and lessons learned in a spirit of partnership and harmony.',
			'dep_cargo'            => 'Tech Team',
			'dep_texto'            => 'Teamwork at CLI is real and it happens every day. Having a team that helps each other solve complex problems, in full sync with innovative tools, makes our routine light and rewarding. In the end, the success of our deliveries comes from this ecosystem, where we get support from every area of the company.',
			'beneficios_eyebrow'   => 'BENEFITS',
			'beneficios_titulo'    => 'Everything you need to do your best work.',
			'beneficios_subtitulo' => 'We know you need the right structure to give your best. That is why we offer benefits that make a difference day to day.',
			'beneficio_1_titulo'   => 'Health and Wellbeing',
			'beneficio_1_texto'    => 'Bradesco health plan and Odontomais dental plan, with broad cover for you and your dependants.',
			'beneficio_2_titulo'   => 'Remote Work',
			'beneficio_2_texto'    => 'A monthly allowance to cover home office costs and keep your remote routine comfortable.',
			'beneficio_3_titulo'   => 'Meals',
			'beneficio_3_texto'    => 'A monthly allowance paid by pix, which you can use as you like for your meals throughout the month.',
			'beneficio_4_titulo'   => 'Family Support',
			'beneficio_4_texto'    => 'Childcare allowance for children up to 5 years old, because family is part of everyone success too.',
			'beneficio_5_titulo'   => 'Quality of life',
			'beneficio_5_texto'    => 'Access to TotalPass: gyms, sports and wellbeing activities to keep your physical health on track.',
			'beneficio_6_titulo'   => 'Birthday Day Off',
			'beneficio_6_texto'    => 'Take the day off on your birthday. You deserve to celebrate however you like.',
			'jeito_titulo'         => 'The CLI way',
			'jeito_texto'          => 'More than rules, these principles guide the way we work every day.',
			'jeito_item_1_titulo'  => 'Transparency first',
			'jeito_item_1_texto'   => 'Even when it is hard, we choose to speak and listen clearly.',
			'jeito_item_2_titulo'  => 'Ownership',
			'jeito_item_2_texto'   => 'We take on the problem of the client, and of the company, as our own.',
			'jeito_item_3_titulo'  => 'Careful listening',
			'jeito_item_3_texto'   => 'We ask for help, take feedback and change course when it makes sense.',
			'jeito_item_4_titulo'  => 'Technical depth',
			'jeito_item_4_texto'   => 'We study, document and record. Learning is part of the job.',
			'jeito_item_5_titulo'  => 'Sharing',
			'jeito_item_5_texto'   => 'We share knowledge, time and opportunities.',
			'jeito_botao'          => $this->link_traduzido( 'See openings', '/trabalhe-conosco/#vagas' ),
			'tc_blog_titulo'       => 'Get to know CLI better',
		);
	}

	/* =====================================================================
	   CLI SIGNATURE
	   ===================================================================== */

	/**
	 * Campos de texto da página CLI Signature em inglês.
	 *
	 * @return array<string,mixed>
	 */
	protected function texto_en_cli_signature() {
		return array(
			'cs_hero_eyebrow'          => 'CLI SIGNATURE',
			'cs_hero_titulo'           => 'Critical projects demand more than execution. They demand a signature.',
			'cs_hero_texto'            => 'The premium tier of CLI Connect, for companies running critical projects with dedicated specialists, executive governance and continuous follow-up.',
			'cs_hero_botao'            => $this->link_traduzido( 'Book a demo', '/contato/' ),
			'cs_cenarios_eyebrow'      => 'When the challenge demands more',
			'cs_cenarios_titulo'       => 'Who is CLI Signature for?',
			'cs_cenarios_texto'        => 'Ideal for companies in highly complex scenarios that require specialist follow-up.',
			'cs_cenarios_1_titulo'     => 'Digital Transformation',
			'cs_cenarios_1_texto'      => 'Architecture modernisation, legacy replacement, new digital channels and omnichannel journeys.',
			'cs_cenarios_2_titulo'     => 'Critical Integrations',
			'cs_cenarios_2_texto'      => 'SAP, Salesforce, Totvs, ERPs, CRMs, e-commerce, tax, data, APIs and business platforms.',
			'cs_cenarios_3_titulo'     => 'Complex Environments',
			'cs_cenarios_3_texto'      => 'Integrations that need to run with stability, traceability and continuous support.',
			'cs_cenarios_4_titulo'     => 'Multiple Stakeholders',
			'cs_cenarios_4_texto'      => 'IT, business, vendors, consultancies, internal squads and executive areas.',
			'cs_cenarios_5_titulo'     => 'Strategic Initiatives',
			'cs_cenarios_5_texto'      => 'Roadmap, prioritisation, risk management, architecture, SLA and executive communication.',
			'cs_cenarios_6_titulo'     => 'Mission-Critical Operations',
			'cs_cenarios_6_texto'      => 'Processes that cannot be interrupted and require monitoring, governance and fast response.',
			'cs_pilares_eyebrow'       => 'enterprise model',
			'cs_pilares_titulo'        => 'The enterprise experience of CLI Connect',
			'cs_pilares_texto'         => 'CLI Signature extends the CLI Connect experience with a dedicated layer of governance, executive service and continuous evolution for strategic operations.',
			'cs_pilares_1_titulo'      => 'CLI Technical Excellence',
			'cs_pilares_1_texto'       => 'Specialists in Integration, APIs, Data, AI, iPaaS, Salesforce, SAP, ERPs, Tax and enterprise platforms.',
			'cs_pilares_2_titulo'      => 'Executive Governance',
			'cs_pilares_2_texto'       => 'Regular rituals, indicators, strategic follow-up and planned evolution.',
			'cs_pilares_3_titulo'      => 'Exclusive Follow-up',
			'cs_pilares_3_texto'       => 'A dedicated Project/Relationship Manager and Architect to make sure technical decisions are sound and aligned with the business.',
			'cs_diferenciais_titulo_1' => 'More than a platform.',
			'cs_diferenciais_titulo_2' => 'An operation under continuous care.',
			'cs_diferenciais_texto'    => 'CLI Signature extends the CLI Connect experience with a dedicated layer of governance, executive service and continuous evolution for strategic operations.',
			'cs_diferenciais_1_titulo' => 'Dedicated specialists',
			'cs_diferenciais_1_texto'  => 'Professionals following your operation closely.',
			'cs_diferenciais_2_titulo' => 'Executive governance',
			'cs_diferenciais_2_texto'  => 'Meetings, indicators and planned evolution.',
			'cs_diferenciais_3_titulo' => 'Priority service',
			'cs_diferenciais_3_texto'  => 'Dedicated flows for critical demands.',
			'cs_diferenciais_4_titulo' => 'Continuous evolution',
			'cs_diferenciais_4_texto'  => 'New integrations and improvements are part of the service.',
			'cs_diferenciais_5_titulo' => 'Monitoring',
			'cs_diferenciais_5_texto'  => 'Constant visibility over the whole operation.',
			'cs_diferenciais_6_titulo' => 'Operational excellence',
			'cs_diferenciais_6_texto'  => 'Best practice from architecture through to sustainment.',
			'cs_operacao_eyebrow'      => 'Managed Operation',
			'cs_operacao_titulo_1'     => 'Secure a continuous operation',
			'cs_operacao_titulo_2'     => 'that is ready to evolve',
			'cs_operacao_texto'        => 'Count on an operation structured to sustain, monitor and continuously evolve your environment, with an agreed SLA, operational governance and defined processes for more predictability and efficiency.',
			'cs_operacao_1_titulo'     => 'Service Catalogue',
			'cs_operacao_1_texto'      => 'A structured service desk with an agreed SLA, prioritisation by criticality and organised demand management.',
			'cs_operacao_2_titulo'     => 'Incident Management',
			'cs_operacao_2_texto'      => 'Resolve incidents quickly, with traceability and indicators that give visibility over the service.',
			'cs_operacao_3_titulo'     => 'Evolutionary Improvements',
			'cs_operacao_3_texto'      => 'Continuously evolve your environment with operational monitoring, planned improvements and a knowledge base that stays up to date.',
			'cs_operacao_4_titulo'     => 'Documentation',
			'cs_operacao_4_texto'      => 'Keep complete technical documentation and an organised knowledge base to guarantee operational continuity and consistency.',
			'cs_gestor_titulo'         => 'Dedicated Project and Relationship Manager',
			'cs_gestor_texto'          => 'Have a single point of contact following your customer journey. Organise priorities, run governance rituals with confidence and keep communication clear between business, technology and operations.',
			'cs_gestor_botao'          => $this->link_traduzido( 'Book a demo', '/contato/' ),
			'cs_gestor_1_titulo'       => 'Roadmap Follow-up',
			'cs_gestor_2_titulo'       => 'Backlog Governance',
			'cs_gestor_3_titulo'       => 'Executive and Operational Meetings',
			'cs_gestor_4_titulo'       => 'Priority Management',
			'cs_gestor_5_titulo'       => 'Stakeholder Communication',
			'cs_gestor_6_titulo'       => 'SLA and Indicator Tracking',
			'cs_gestor_7_titulo'       => 'Continuous Evolution Plan',
			'cs_arquiteto_titulo'      => 'Dedicated Architect',
			'cs_arquiteto_texto'       => 'A senior specialist responsible for making sure technical decisions are aligned with the strategy, scalability, security and evolution of the company.',
			'cs_arquiteto_botao'       => $this->link_traduzido( 'Book a demo', '/contato/' ),
			'cs_arquiteto_1_titulo'    => 'Architecture Design',
			'cs_arquiteto_2_titulo'    => 'Technical Review of Solutions',
			'cs_arquiteto_3_titulo'    => 'Standards Definition',
			'cs_arquiteto_4_titulo'    => 'Support on Critical Decisions',
			'cs_arquiteto_5_titulo'    => 'API and Integration Strategy',
			'cs_arquiteto_6_titulo'    => 'Technical Risk Assessment',
			'cs_arquiteto_7_titulo'    => 'Modernisation Roadmap',
		);
	}

	/* =====================================================================
	   CLI CONNECT
	   ===================================================================== */

	/**
	 * Campos de texto da página CLI Connect em inglês.
	 *
	 * @return array<string,mixed>
	 */
	protected function texto_en_cli_connect() {
		return array(
			'cc_hero_titulo'          => 'Integrations that keep your',
			'cc_hero_titulo_destaque' => 'operation moving',
			'cc_hero_texto'           => 'Connect SAP, ERPs, CRMs and critical applications on a platform built for real-time operations. Automate business events with security, continuous monitoring and a single monthly fee.',
			'cc_hero_botao'           => $this->link_traduzido( 'Book a demo', '/contato/' ),
			'cc_brands_titulo'        => 'Major companies trust CLI',
			'cc_solucao_titulo'       => 'Everything you need in a single solution',
			'cc_solucao_1_titulo'     => 'Global Platform',
			'cc_solucao_1_texto'      => 'The platform licence is already included, so you can connect systems with security, scalability and globally recognised technology.',
			'cc_solucao_1_bullet_1'   => 'Licence included',
			'cc_solucao_1_bullet_2'   => 'Powered by Boomi',
			'cc_solucao_1_bullet_3'   => 'Enterprise scale',
			'cc_solucao_2_titulo'     => 'Service Included',
			'cc_solucao_2_texto'      => 'Your operation keeps evolving after go-live. Request improvements, new projects and continuous support within the same monthly fee.',
			'cc_solucao_2_bullet_1'   => 'New projects on demand',
			'cc_solucao_2_bullet_2'   => 'Continuous improvements',
			'cc_solucao_2_bullet_3'   => 'Incident management',
			'cc_solucao_3_titulo'     => 'Integration Library',
			'cc_solucao_3_texto'      => 'Start faster using integrations and connectors already built for the main systems on the market.',
			'cc_solucao_3_bullet_1'   => 'Ready-made connectors',
			'cc_solucao_3_bullet_2'   => 'The most widely used systems',
			'cc_solucao_3_bullet_3'   => 'Shorter go-live time',
			'cc_impl_eyebrow'         => 'Fast Deployment',
			'cc_impl_titulo'          => 'Fewer development hours.',
			'cc_impl_titulo_2'        => 'More speed for the business.',
			'cc_impl_texto'           => 'Cut the technical effort needed to integrate SAP and deliver new projects with more agility and quality.',
			'cc_impl_sem_label'       => 'Without CLI Connect',
			'cc_impl_sem_tempo'       => '1 Month',
			'cc_impl_sem_etapa_1'     => 'Submit request',
			'cc_impl_sem_etapa_2'     => 'Define the need',
			'cc_impl_sem_etapa_3'     => 'Wait for development',
			'cc_impl_sem_etapa_4'     => 'Data transfer',
			'cc_impl_sem_etapa_5'     => 'Data made available',
			'cc_impl_sem_etapa_6'     => 'Maintenance',
			'cc_impl_sem_etapa_7'     => 'Testing and QA',
			'cc_impl_com_label'       => 'With CLI Connect',
			'cc_impl_com_tempo'       => '5 Days',
			'cc_impl_com_etapa_1'     => 'Submit request',
			'cc_impl_com_etapa_2'     => 'Define the need',
			'cc_impl_com_etapa_3'     => 'Data made available',
			'cc_boomi_eyebrow'        => 'Global platform',
			'cc_boomi_titulo'         => 'World-class technology with support built for the Brazilian market',
			'cc_boomi_texto'          => 'When you hire CLI Connect you get the same platform that large global companies use to integrate their systems, with the added advantage of specialised support for the Brazilian market, affordable pricing and managed service included.',
			'cc_boomi_logos_texto'    => 'Companies using Boomi',
			'cc_boomi_logos_clientes' => array_values(
				array_filter(
					array(
						$this->id_do_seed( 'cliente:cargill', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:cisco', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:dell', 'cli_cliente' ),
					)
				)
			),
			'cc_operacoes_eyebrow'    => 'Critical Operations',
			'cc_operacoes_titulo'     => 'Some integrations',
			'cc_operacoes_titulo_2'   => 'simply cannot fail',
			'cc_operacoes_texto'      => 'Protect critical processes with integrations built to run continuously, without putting the business at risk.',
			'cc_operacoes_bullet_1'   => 'Industrial processes in continuous operation',
			'cc_operacoes_bullet_2'   => 'Orders, quotes and billing with no failures or delays',
			'cc_operacoes_bullet_3'   => 'Transactions and movements with no interruptions',
			'cc_dashboard_eyebrow'    => 'Follow it in real time',
			'cc_dashboard_titulo'     => 'Track every integration and request new work in one place',
			'cc_dashboard_texto'      => 'Get visibility over project progress, follow your requests and submit new demands whenever you need. All in one portal designed to keep your operation evolving continuously.',
			'cc_dashboard_botao'      => $this->link_traduzido( 'Book a demo', '#' ),
			'cc_dep_cargo'            => 'Sales Manager',
			'cc_dep_texto'            => 'With CLI Connect we restructured our governance and our financial processes.',
			'cc_dep_botao'            => $this->link_traduzido( 'Read the case study', '/cases/' ),
			'cc_dif_eyebrow'          => 'WHAT SETS US APART',
			'cc_dif_titulo'           => 'Designed to deliver continuous value',
			'cc_dif_texto'            => 'See what makes CLI Connect a simpler, more predictable option for growing operations.',
			'cc_dif_botao'            => $this->link_traduzido( 'Book a demo', '/contato/' ),
			'cc_dif_row_1'            => 'No extra cost per project',
			'cc_dif_row_2'            => 'No charge per execution, flow or message',
			'cc_dif_row_3'            => 'Expertise in systems used in the Brazilian market',
			'cc_dif_row_4'            => 'A platform leading on security and compliance',
			'cc_dif_row_5'            => 'Operation monitored and managed by CLI',
			'cc_dif_row_6'            => 'Pricing that matches the Brazilian reality',
			'cc_dif_row_7'            => 'Support for complex systems',
			'cc_dif_row_8'            => 'A library with more than 30,000 integrations',
			'cc_dif_row_9'            => 'Human, specialised service',
			'cc_vantag_eyebrow'       => 'ADVANTAGES',
			'cc_vantag_titulo'        => 'Why adopt CLI Connect',
			'cc_vantag_texto'         => 'From finance to customer service, build integrated flows and bring all your company information into a single screen, with no hassle.',
			'cc_vantag_1_titulo'      => 'More Productivity',
			'cc_vantag_1_texto'       => 'Automate repetitive tasks and free your teams for more strategic work of greater value to the business.',
			'cc_vantag_2_titulo'      => 'More Governance',
			'cc_vantag_2_texto'       => 'Define exactly what each agent can access, answer or run, with human approval at the critical points.',
			'cc_vantag_3_titulo'      => 'More Security',
			'cc_vantag_3_texto'       => 'Reduce the risk of exposing sensitive data and keep full control over how AI is used across the organisation.',
			'cc_vantag_4_titulo'      => 'More Integration',
			'cc_vantag_4_texto'       => 'Connect agents to the enterprise systems your company already uses — ERP, CRM, APIs and internal platforms.',
			'cc_vantag_5_titulo'      => 'More Speed',
			'cc_vantag_5_texto'       => 'Build and launch agents with a visual, simple and scalable approach — with less dependence on IT.',
			'cc_vantag_6_titulo'      => 'Cost Control',
			'cc_vantag_6_texto'       => 'Monitor consumption, token usage and operating limits for your agents to keep AI within the planned budget.',
			'cc_np_eyebrow'           => 'IN PRACTICE',
			'cc_np_titulo'            => 'Talk to your data. The agent does the rest.',
			'cc_np_texto'             => 'Ask questions, run processes and get answers grounded in your own operational data.',
			'cc_np_bullet_1'          => 'Queries several systems at once',
			'cc_np_bullet_2'          => 'Runs flows with no manual step',
			'cc_np_bullet_3'          => 'Keeps the full history of the operation',
			'cc_parceiro_eyebrow'     => 'TIME SAVINGS',
			'cc_parceiro_titulo'      => 'Integrate your systems with far less time and technical effort',
			'cc_parceiro_texto'       => 'With CLI Connect you remove the complexity and the development time of the traditional approach, making integrations up to 5x faster than solutions built from scratch.',
			'cc_parceiro_esq_titulo'  => 'CLI Connect',
			'cc_parceiro_esq_sub'     => 'Ready-to-use integrations, with automatic events and no development effort',
			'cc_parceiro_esq_item_1'  => 'Pre-built connectors, ready to use',
			'cc_parceiro_esq_item_2'  => 'Automatic, two-way events',
			'cc_parceiro_esq_item_3'  => 'Low-code configuration',
			'cc_parceiro_esq_item_4'  => 'Fast, safe deployment',
			'cc_parceiro_dir_titulo'  => 'Custom development',
			'cc_parceiro_dir_sub'     => 'The traditional approach, with more steps, dependencies and technical effort',
			'cc_parceiro_dir_item_1'  => 'Writing code inside SAP',
			'cc_parceiro_dir_item_2'  => 'Testing and fixes inside SAP',
			'cc_parceiro_dir_item_3'  => 'Dependence on specialist resources',
			'cc_parceiro_dir_item_4'  => 'Long development cycles',
			'cc_parceiro_destaque'    => 'Integrations delivered in up to <strong>5x less</strong> time. With more quality and more security.',
		);
	}

	/* =====================================================================
	   INTEGRAÇÃO SAP
	   ===================================================================== */

	/**
	 * Campos de texto da página Integração SAP em inglês.
	 *
	 * @return array<string,mixed>
	 */
	protected function texto_en_integracao_sap() {
		return array(
			'sap_hero_titulo_azul'   => 'Expand what SAP can do',
			'sap_hero_titulo_escuro' => 'without adding complexity to your operation',
			'sap_hero_texto'         => 'Connect your SAP S/4HANA and other critical systems with a structure built for complex operations, automatic events and continuous evolution.',
			'sap_hero_botao'         => $this->link_traduzido( 'Book a demo', '/contato/' ),
			'sap_vel_eyebrow'        => 'make the most of your time',
			'sap_vel_titulo'         => 'More speed for the business.',
			'sap_vel_texto'          => 'Cut the technical effort needed to integrate SAP and deliver new projects far more quickly.',
			'sap_vel_sem_label'      => 'WITHOUT CLI CONNECT',
			'sap_vel_sem_tempo'      => '1 MONTH',
			'sap_vel_sem_1'          => "Submit\nrequest",
			'sap_vel_sem_2'          => "Define\nthe need",
			'sap_vel_sem_3'          => "Wait for\ndevelopment",
			'sap_vel_sem_4'          => "Data\ntransfer",
			'sap_vel_sem_5'          => "Data made\navailable",
			'sap_vel_sem_6'          => 'Maintenance',
			'sap_vel_sem_7'          => 'Testing and QA',
			'sap_vel_com_label'      => 'WITH CLI CONNECT',
			'sap_vel_com_tempo'      => '5 DAYS',
			'sap_vel_com_1'          => "Submit\nrequest",
			'sap_vel_com_2'          => "Define\nthe need",
			'sap_vel_com_3'          => "Data made\navailable",
			'sap_con_eyebrow'        => 'SAP INTEGRATED',
			'sap_con_titulo'         => "Your SAP ready to connect\nwhatever comes next",
			'sap_con_texto'          => 'Integrate modern applications, digital platforms and artificial intelligence initiatives without compromising the stability of the company’s critical processes.',
			'sap_sin_eyebrow'        => 'SAP IN SYNC',
			'sap_sin_titulo'         => "Automatic updates\nwhenever something changes in SAP",
			'sap_sin_texto'          => 'Orders, records, stock and other information are synchronised automatically with the connected systems, keeping the whole operation up to date with no manual work.',
			'sap_rec_eyebrow'        => 'FREE UP RESOURCES',
			'sap_rec_titulo'         => 'Cut the number of hours worked',
			'sap_rec_texto'          => 'Avoid long development projects just to connect new systems and processes.',
			'sap_rec_metrica_numero' => '65%',
			'sap_rec_metrica_label'  => 'reduction in hours worked',
			'sap_dep_cargo'          => 'Sales Manager',
			'sap_dep_frase'          => '"R$ 6 million saved in ABAP development hours"',
			'sap_dep_botao'          => $this->link_traduzido( 'Read the case study', '/cases/' ),
			'sap_sis_titulo'         => "Connect SAP to the systems\nthat move your operation",
			'sap_sis_subtitulo'      => 'Integrate and govern your operation whatever technology you use',
			'sap_sis_1'              => 'CRM',
			'sap_sis_2'              => 'E-commerce',
			'sap_sis_3'              => 'Tax',
			'sap_sis_4'              => 'Marketplace',
			'sap_sis_5'              => 'BI',
			'sap_sis_6'              => 'Finance',
			'sap_sis_7'              => 'HR',
			'sap_sis_8'              => 'Websites',
			'sap_sis_9'              => 'Apps',
			'sap_sis_10'             => 'AI Agent',
			'sap_cc_eyebrow'         => 'PRESERVE YOUR CLEAN CORE',
			'sap_cc_titulo'          => 'Make the most of your standard',
			'sap_cc_texto'           => 'Tailored innovation with absolute respect for your core. Preserve your Clean Core and upgrade SAP without fear.',
			'sap_cc_1_titulo'        => 'Agile Deployment',
			'sap_cc_1_texto'         => 'Plug-and-play solutions that talk natively to your SAP, cutting setup time from months to weeks.',
			'sap_cc_2_titulo'        => 'Upgrades without the headache',
			'sap_cc_2_texto'         => 'Move SAP to the latest versions without breaking customisations or stopping your operation.',
			'sap_cc_3_titulo'        => 'Lower maintenance cost',
			'sap_cc_3_texto'         => 'Eliminate the huge spend on maintaining and testing custom ("Z") code at every new SAP cycle.',
			'sap_int_eyebrow'        => 'INTEGRATIONS INCLUDED',
			'sap_int_titulo'         => "Start faster with templates\nalready running in real environments",
			'sap_int_botao'          => $this->link_traduzido( 'Book a demo', '/contato/' ),
			'sap_int_nota'           => 'More than 30,000 integrations ready to use',
			'sap_int_1_titulo'       => 'SAP + Salesforce',
			'sap_int_1_desc'         => 'Sales and service in sync.',
			'sap_int_2_titulo'       => 'SAP + VTEX',
			'sap_int_2_desc'         => 'Orders, customers, stock and billing.',
			'sap_int_3_titulo'       => 'SAP + RD Station',
			'sap_int_3_desc'         => 'Marketing and sales aligned.',
			'sap_int_4_titulo'       => 'SAP + Senior',
			'sap_int_4_desc'         => 'HR and payroll synchronised automatically.',
			'sap_int_5_titulo'       => 'SAP + Sankhya',
			'sap_int_5_desc'         => 'Processes across ERPs with no rework.',
			'sap_int_6_titulo'       => 'SAP + Thompson Reuters',
			'sap_int_6_desc'         => 'Tax obligations always integrated.',
			'sap_int_7_titulo'       => 'SAP + MV Saúde',
			'sap_int_7_desc'         => 'Clinical and financial data connected.',
			'sap_int_8_titulo'       => 'SAP + Tasy',
			'sap_int_8_desc'         => 'Hospital information synchronised.',
			'sap_mig_titulo'         => 'Your migration to SAP S/4HANA with zero risk and no surprises',
			'sap_mig_texto'          => 'Support for SAP ECC ends in 2027. Plan your transition now and get access to the best specialists on the market.',
			'sap_mig_botao'          => $this->link_traduzido( 'Migrate now', '/contato/' ),
			'sap_ben_eyebrow'        => 'BENEFITS',
			'sap_ben_titulo'         => 'What CLI Connect brings to your SAP',
			'sap_ben_botao'          => $this->link_traduzido( 'Get in touch', '/contato/' ),
			'sap_ben_1_rotulo'       => '01 - SAP expertise',
			'sap_ben_1_desc'         => 'Experience on projects involving SAP S/4HANA integrations.',
			'sap_ben_2_rotulo'       => '02 - Managed service',
			'sap_ben_2_desc'         => 'Continuous monitoring, specialist support and constant evolution of your integration platform.',
			'sap_ben_3_rotulo'       => '03 - Predictable monthly fee',
			'sap_ben_3_desc'         => 'A subscription model with fixed, predictable costs and no budget surprises.',
			'sap_ben_4_rotulo'       => '04 - Ready-made connectors',
			'sap_ben_4_desc'         => 'More than 30,000 connectors ready for immediate use, speeding up deployment.',
			'sap_ben_5_rotulo'       => '05 - Operational governance',
			'sap_ben_5_desc'         => 'Full visibility of integration flows with traceability, alerts and centralised management.',
			'sap_ben_6_rotulo'       => '06 - Global leading platform',
			'sap_ben_6_desc'         => 'Boomi technology — a leader in the Gartner Magic Quadrant for integration platforms.',
			'sap_aut_eyebrow'        => 'AUTOMATIC EVENTS',
			'sap_aut_titulo'         => "Turn SAP events\ninto automatic actions",
			'sap_aut_texto'          => "Integrate SAP into your operation and turn events into automatic execution,\nwith no manual interruptions.",
			'sap_aut_1_etapa1'       => 'Order approved in SAP',
			'sap_aut_1_etapa2'       => 'Billing started',
			'sap_aut_1_etapa3'       => 'Customer notified',
			'sap_aut_2_etapa1'       => 'Product updated',
			'sap_aut_2_etapa2'       => 'Channels synchronised',
			'sap_aut_2_etapa3'       => 'Operation updated',
			'sap_aut_3_etapa1'       => 'Minimum stock reached',
			'sap_aut_3_etapa2'       => 'Supplier triggered',
			'sap_aut_3_etapa3'       => 'Replenishment started',
			'sap_aut_4_etapa1'       => 'New regulation',
			'sap_aut_4_etapa2'       => 'Impacts identified',
			'sap_aut_4_etapa3'       => 'Teams notified',
			'sap_aut_5_etapa1'       => 'Indicator off target',
			'sap_aut_5_etapa2'       => 'Manager alerted',
			'sap_aut_5_etapa3'       => 'Action plan started',
			'sap_faq_eyebrow'        => 'FAQ',
			'sap_faq_titulo'         => 'Frequently Asked Questions',
			'sap_faq_1_pergunta'     => 'Does CLI Connect work with SAP S/4HANA on-premises and in the cloud?',
			'sap_faq_1_resposta'     => 'Yes. The CLI Connect platform works with SAP S/4HANA environments both on-premises and in the cloud (including SAP BTP), giving you flexibility whatever infrastructure your company has chosen.',
			'sap_faq_2_pergunta'     => 'Do we need ABAP development to connect SAP S/4HANA?',
			'sap_faq_2_resposta'     => 'No. CLI Connect uses native connectors and standard SAP APIs, removing the need for ABAP development. That preserves your SAP Clean Core and cuts deployment cost and time dramatically.',
			'sap_faq_3_pergunta'     => 'How long does it take to put the first integration live?',
			'sap_faq_3_resposta'     => 'With the ready-made CLI Connect templates, the first integration can go live in up to 5 working days, depending on how complex the process is. Our team follows the whole configuration and testing process.',
		);
	}

	/* =====================================================================
	   POLÍTICA DE PRIVACIDADE
	   ===================================================================== */

	/**
	 * Campos de texto da Política de Privacidade em inglês.
	 *
	 * @return array<string,mixed>
	 */
	protected function texto_en_privacidade() {
		return array(
			'pv_titulo'        => 'Privacy Policy',
			'pv_lead'          => 'This policy explains which personal data CLI Connect collects, why we collect it and what rights you have over it.',
			'pv_atualizado_em' => 'Last updated on August 28, 2026',
			'pv_corpo'         => '<p><strong>Draft pending legal review.</strong> The structure below covers the sections required by the Brazilian data protection law (LGPD, Law 13.709/2018) and must be reviewed and approved by CLI legal before final publication. The text is editable in the admin, with no deploy needed.</p>
<h2>1. Who controls your data</h2>
<p>CLI Connect is the controller of the personal data processed on this website and decides how and why it is used. Contact details for privacy matters are in the last section of this policy.</p>
<h2>2. What data we collect</h2>
<p>We collect only what we need in order to answer the people who reach out to us and to understand how the site is used:</p>
<ul>
<li><strong>Data you provide:</strong> name, e-mail, phone number and the content of the message sent through the forms on this site.</li>
<li><strong>Browsing data:</strong> pages visited, traffic source and technical information about your browser, collected through cookies and analytics tools.</li>
</ul>
<h2>3. Why we use it</h2>
<ul>
<li>To answer contact requests, proposals and demo bookings.</li>
<li>To send communications about our services, when you agree to receive them.</li>
<li>To understand how the site is used and improve the browsing experience.</li>
<li>To comply with legal and regulatory obligations.</li>
</ul>
<h2>4. Legal bases</h2>
<p>Processing relies on the data subject\'s consent, on the performance of a contract or preliminary procedures requested by the data subject, on compliance with a legal obligation and on the legitimate interest of CLI Connect, always within the limits of article 7 of the LGPD.</p>
<h2>5. Who we share it with</h2>
<p>We do not sell personal data. We share information only with suppliers that operate on our behalf — hosting, e-mail, CRM and analytics tools — always under contract and limited to what the service requires, or when required by a competent authority.</p>
<h2>6. Cookies</h2>
<p>We use cookies to keep the site working, remember preferences and measure audience. You can block or delete cookies in your browser settings; some features may stop working properly.</p>
<h2>7. How long we keep it</h2>
<p>We keep data for as long as the purposes in this policy require, or for the periods required by law. Once the purpose is fulfilled and the legal periods have expired, data is deleted or anonymised.</p>
<h2>8. Your rights</h2>
<p>The LGPD gives you the right to confirm that processing exists, access your data, correct incomplete or outdated data, request anonymisation, blocking or deletion, request portability, withdraw consent and be informed about who we share your data with. To exercise any of them, use the contact below.</p>
<h2>9. Security</h2>
<p>We apply technical and administrative measures to protect personal data against unauthorised access, loss, alteration or improper disclosure.</p>
<h2>10. Changes to this policy</h2>
<p>This policy may be updated at any time. The date of the latest update is shown at the top of this page.</p>
<h2>11. Talk to us about privacy</h2>
<p>Questions or requests about your personal data can be sent to <a href="mailto:atendimento@cliconsultoria.com.br">atendimento@cliconsultoria.com.br</a>.</p>',
		);
	}

	/* =====================================================================
	   UTILITÁRIOS
	   ===================================================================== */

}
