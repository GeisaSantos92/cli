<?php
/**
 * Seed — texto em espanhol das páginas.
 *
 * Um método por página, no mesmo desenho de `preencher_*()` do seed em
 * português. Só entram aqui os campos **de texto**: imagens, ícones, números e
 * relações são copiados do original por `traduzir_post()`.
 *
 * O espanhol é tradução de trabalho, feita a partir do português aprovado no
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
 * Conteúdo em espanhol das páginas do site.
 */
trait Cliconnect_Seed_Es_Paginas {

	/**
	 * Slug em português => slug e título em espanhol.
	 *
	 * Mesma ordem de `criar_paginas()`, para as duas listas serem conferidas
	 * lado a lado.
	 *
	 * Os slugs precisam ser diferentes dos do português **e** dos do inglês:
	 * compartilhar slug entre idiomas é recurso do Polylang Pro; no free o
	 * WordPress acrescenta `-2` em silêncio. Daí `plataforma-de-integracion` e
	 * `sistemas-conectados`, onde a palavra em espanhol é idêntica à portuguesa.
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function paginas_es() {
		return array(
			'home'             => array( 'inicio', 'Inicio' ),
			'blog'             => array( 'articulos', 'Blog' ),
			'contato'          => array( 'contacto', 'Contacto' ),
			'plataforma'       => array( 'plataforma-de-integracion', 'Plataforma' ),
			'cli-connect'      => array( 'cli-connect-plataforma', 'CLI Connect' ),
			'cli-flow'         => array( 'cli-flow-plataforma', 'CLI Flow' ),
			'cli-signature'    => array( 'cli-signature-servicio', 'CLI Signature' ),
			'solucoes'         => array( 'soluciones', 'Soluciones' ),
			'integracao-sap'   => array( 'integracion-sap', 'Integración SAP' ),
			'sistemas'         => array( 'sistemas-conectados', 'Sistemas' ),
			'trabalhe-conosco' => array( 'trabaja-con-nosotros', 'Trabaja con nosotros' ),
			'privacidade'      => array( 'politica-de-privacidad', 'Política de Privacidad' ),
			'termos'           => array( 'terminos-de-uso', 'Términos de Uso' ),
			'programa-de-parceiros' => array( 'programa-de-socios', 'Programa de Socios' ),
		);
	}

	/**
	 * Title e meta description das páginas, em espanhol.
	 *
	 * Chave = slug da página em português (o mesmo do seed base).
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function seo_es() {
		return array(
			'home'             => array(
				'CLI Connect — integraciones ilimitadas, costo previsible',
				'Conecte ERP, CRM, e-commerce y nube en una sola capa de integración powered by Boomi. Costo fijo, integraciones ilimitadas y equipo experto.',
			),
			'blog'             => array(
				'Blog de CLI Connect — integración de sistemas en la práctica',
				'Artículos sobre integración de sistemas, ERP, CRM y automatización de procesos, escritos por quienes ponen proyectos en producción.',
			),
			'contato'          => array(
				'Hable con CLI Connect',
				'Cuéntele a nuestro equipo sobre su integración. Respondemos rápido por correo electrónico, teléfono o WhatsApp.',
			),
			'plataforma'       => array(
				'Plataforma de integración — CLI Connect',
				'Una única capa para conectar ERP, CRM, e-commerce y nube, con gobernanza, monitoreo y más de 300 conectores listos.',
			),
			'cli-connect'      => array(
				'CLI Connect — integración como servicio continuo',
				'Costo fijo, integraciones ilimitadas y un equipo dedicado que cuida su capa de integración, sin un proyecto nuevo por cada conexión.',
			),
			'cli-signature'    => array(
				'CLI Signature — un squad dedicado de integración',
				'Gerente de proyecto y arquitecto dedicados a su hoja de ruta de integración, con gobernanza, ritos de seguimiento y previsibilidad.',
			),
			'solucoes'         => array(
				'Soluciones de integración por sistema y por área',
				'Catálogo de integraciones por ERP, CRM, nube, industria y área de negocio — de SAP a Salesforce, con conectores listos.',
			),
			'integracao-sap'   => array(
				'Integración SAP — conecte SAP con el resto de la operación',
				'Integre SAP ECC, S/4HANA y Business One con CRM, e-commerce, fiscal y datos, sin desarrollo punto a punto.',
			),
			'sistemas'         => array(
				'Sistemas integrados por CLI Connect',
				'Más de 300 conectores listos para ERP, CRM, e-commerce, nube y herramientas de datos. Vea si su sistema ya está en la lista.',
			),
			'trabalhe-conosco' => array(
				'Trabaje con nosotros — CLI Connect',
				'Vacantes, cultura y beneficios de un equipo que vive de integrar sistemas críticos. Vea cómo es trabajar en CLI Connect.',
			),
			'privacidade'      => array(
				'Política de Privacidad — CLI Connect',
				'Cómo CLI Connect recoge, usa y protege los datos personales de quienes visitan el sitio y contratan nuestros servicios.',
			),
			'termos'           => array(
				'Términos de Uso — CLI Connect',
				'Condiciones de uso del sitio y de los servicios de CLI Connect.',
			),
		);
	}

	/**
	 * Strings de opção (Customizer e descrição do site) em espanhol.
	 *
	 * @return array<string,string>
	 */
	protected function strings_polylang_es() {
		return array(
			'Portal do Cliente'                                       => 'Portal del Cliente',
			'Acessar Plataforma'                                      => 'Acceder a la Plataforma',
			"Planeje a evolução\ndas suas integrações"                 => "Planifique la evolución\nde sus integraciones",
			'Fale conosco'                                 => 'Hable con nosotros',
			'Usamos cookies para melhorar sua experiência. Ao continuar navegando, você concorda com a nossa' => 'Usamos cookies para mejorar su experiencia. Al continuar navegando, acepta nuestra',
			'política de privacidade'                                  => 'política de privacidad',
			'Concordar'                                                => 'Aceptar',
			'Integrações ilimitadas. Custo previsível. Sem surpresas.' => 'Integraciones ilimitadas. Costo previsible. Sin sorpresas.',
		);
	}

	/* =====================================================================
	   HOME
	   ===================================================================== */

	/**
	 * Campos de texto da home em espanhol.
	 *
	 * @return array<string,mixed>
	 */
	protected function texto_es_home() {
		return array(
			'hero_eyebrow'         => 'Powered By Boomi',
			'hero_titulo_destaque' => 'Integraciones ilimitadas.',
			'hero_titulo'          => 'Costo previsible. Sin sorpresas.',
			'hero_subtitulo'       => 'Integre todos sus sistemas y ponga agentes de IA personalizados a trabajar en sus procesos.',
			'hero_botao'           => $this->link_traduzido( 'Solicite una demostración', '/contato/' ),
			'agentes_legenda'      => 'Más de 30.000 integraciones listas para usar',
			'camadas_titulo'       => "Todo lo que necesita.\nCon un costo previsible.",
			'camadas_texto'        => "Pague una tarifa fija y use nuestro servicio de integración sin límites.\nCuanto más crece su operación, más se beneficia.",
			'camadas_botao'        => $this->link_traduzido( 'Vea qué está incluido', '/contato/' ),
			'boomi_eyebrow'        => 'Plataforma global',
			'boomi_titulo'         => 'Tecnología de clase mundial con soporte para el mercado latinoamericano',
			'boomi_texto'          => '<p>Acceda a la misma plataforma que las grandes empresas globales usan para integrar sus sistemas, con la ventaja de un <strong>soporte especializado</strong>, un precio accesible y el servicio gestionado incluido.</p>',
			'metrica_1_numero'     => '+200',
			'metrica_1_rotulo'     => 'integraciones por semana',
			'metrica_2_numero'     => '5 días',
			'metrica_2_rotulo'     => 'hasta que su integración esté lista',
			'metrica_3_numero'     => '+30 mil',
			'metrica_3_rotulo'     => 'integraciones ya construidas',
			'midia_1_eyebrow'      => 'IA corporativa',
			'midia_1_titulo'       => 'Cree, gobierne y escale agentes',
			'midia_1_texto'        => 'Cree agentes especializados, conecte sus sistemas y siga toda la operación en un único entorno.',
			'midia_1_topico_1'     => 'Agentes especializados por área',
			'midia_1_topico_2'     => 'Conectados a los sistemas de la empresa',
			'midia_1_topico_3'     => 'Gobierno y monitoreo centralizados',
			'midia_2_eyebrow'      => 'En la práctica',
			'midia_2_titulo'       => "Converse con sus datos.\nEl agente hace el resto.",
			'midia_2_texto'        => 'Haga preguntas, ejecute procesos y obtenga respuestas basadas en los datos de su operación.',
			'midia_2_topico_1'     => 'Consulta varios sistemas a la vez',
			'midia_2_topico_2'     => 'Ejecuta flujos sin intervención manual',
			'midia_2_topico_3'     => 'Conserva todo el historial de la operación',
			'cases_botao'          => $this->link_traduzido( 'Vea nuestros casos de éxito', '/cases/' ),
			'eventos_eyebrow'      => 'Eventos automáticos',
			'eventos_titulo'       => 'Su operación responde en tiempo real a los cambios del negocio',
			'compliance_eyebrow'   => 'Compliance y seguridad',
			'compliance_titulo'    => 'Lideramos el mercado cuando se trata de compliance y seguridad',
			'compliance_texto'     => 'Sus datos, procesos e integraciones protegidos por los más altos estándares globales.',
			'integracoes_eyebrow'  => 'Integraciones listas',
			'integracoes_titulo'   => "Su integración puede\nya estar lista",
			'integracoes_texto'    => 'Conectamos SAP, Protheus, VTEX, Mercado Eletrônico, Salesforce, Senior, MV y decenas de otros sistemas. Muchas de las integraciones que necesita ya están en nuestro catálogo.',
			'integracoes_botao'    => $this->link_traduzido( 'Integrar ahora', '/contato/' ),
			'departamento_1'       => 'Compras',
			'departamento_2'       => 'Atención al cliente',
			'departamento_3'       => 'Logística',
			'departamento_4'       => 'Fiscal',
			'departamento_5'       => 'Finanzas',
			'departamento_6'       => 'RR. HH.',
			'departamentos_titulo' => "Integre todos los departamentos\nde su empresa",
			'departamentos_texto'  => 'Desde finanzas hasta atención al cliente, cree flujos integrados y centralice toda la información de su empresa en una sola pantalla, sin complicaciones.',
			'departamentos_botao'  => $this->link_traduzido( 'Solicitar demostración', '/contato/' ),
			'prova_texto'          => "Más de 500 empresas ya decidieron\nautomatizar sus procesos",
			'frase_texto'          => 'Sus sistemas se hablan entre sí.',
			'frase_texto_b'        => 'Usted se ocupa de',
			'frase_destaque'       => 'lo que importa',
			'suporte_eyebrow'      => 'Atención cuando la necesite',
			'suporte_titulo'       => 'Nunca se queda solo.',
			'suporte_texto'        => '<p>Ofrecemos <strong>soporte humano</strong> para cuando más lo necesita. Nuestro equipo monitorea, mantiene y evoluciona sus integraciones. Si algo falla, ya lo estamos resolviendo antes de que usted lo note.</p>',
			'suporte_botao'        => $this->link_traduzido( 'Acceder al Portal del Cliente', 'https://cliconsultoria.my.site.com/cliente', '_blank' ),
			'blog_titulo'          => 'Lo último de nuestro blog',
			'blog_link'            => $this->link_traduzido( 'Ver todas las publicaciones', '/blog/' ),
			'faq_eyebrow'          => 'FAQ',
			'faq_titulo'           => 'Preguntas Frecuentes',
		);
	}

	/* =====================================================================
	   INTEGRAÇÃO SAP
	   ===================================================================== */

	/**
	 * Campos de texto da página Integração SAP em espanhol.
	 *
	 * @return array<string,mixed>
	 */
	protected function texto_es_integracao_sap() {
		return array(
			'sap_hero_titulo_azul'   => 'Amplíe las capacidades de SAP',
			'sap_hero_titulo_escuro' => 'sin aumentar la complejidad de su operación',
			'sap_hero_texto'         => 'Conecte su SAP S/4HANA y otros sistemas críticos con una estructura preparada para operaciones complejas, eventos automáticos y evolución continua.',
			'sap_hero_botao'         => $this->link_traduzido( 'Solicite una demostración', '/contato/' ),
			'sap_vel_eyebrow'        => 'optimice su tiempo',
			'sap_vel_titulo'         => 'Más velocidad para el negocio.',
			'sap_vel_texto'          => 'Reduzca el esfuerzo técnico necesario para integrar SAP e implemente nuevos proyectos con mucha más agilidad.',
			'sap_vel_sem_label'      => 'SIN CLI CONNECT',
			'sap_vel_sem_tempo'      => '1 MES',
			'sap_vel_sem_1'          => "Enviar\nsolicitud",
			'sap_vel_sem_2'          => "Definir\nla necesidad",
			'sap_vel_sem_3'          => "Esperar el\ndesarrollo",
			'sap_vel_sem_4'          => "Transferencia\nde datos",
			'sap_vel_sem_5'          => "Datos\ndisponibles",
			'sap_vel_sem_6'          => 'Mantenimiento',
			'sap_vel_sem_7'          => 'Pruebas y QA',
			'sap_vel_com_label'      => 'CON CLI CONNECT',
			'sap_vel_com_tempo'      => '5 DÍAS',
			'sap_vel_com_1'          => "Enviar\nsolicitud",
			'sap_vel_com_2'          => "Definir\nla necesidad",
			'sap_vel_com_3'          => "Datos\ndisponibles",
			'sap_con_eyebrow'        => 'SAP INTEGRADO',
			'sap_con_titulo'         => "Su SAP listo para\nconectar lo que viene",
			'sap_con_texto'          => 'Integre aplicaciones modernas, plataformas digitales e iniciativas de inteligencia artificial sin comprometer la estabilidad de los procesos críticos de la empresa.',
			'sap_sin_eyebrow'        => 'SAP SINCRONIZADO',
			'sap_sin_titulo'         => "Actualizaciones automáticas\ncada vez que algo cambia en SAP",
			'sap_sin_texto'          => 'Pedidos, registros, inventarios y demás información se sincronizan automáticamente con los sistemas conectados, manteniendo toda la operación al día sin procesos manuales.',
			'sap_rec_eyebrow'        => 'LIBERE RECURSOS',
			'sap_rec_titulo'         => 'Reduzca la cantidad de horas trabajadas',
			'sap_rec_texto'          => 'Evite proyectos extensos de desarrollo para conectar nuevos sistemas y procesos.',
			'sap_rec_metrica_numero' => '65%',
			'sap_rec_metrica_label'  => 'de reducción en las horas trabajadas',
			'sap_dep_cargo'          => 'Head of Digital and Technology en Panasonic',
			'sap_dep_frase'          => '"Antes hablaba de segundos de respuesta. Hoy hablo de milisegundos."',
			'sap_dep_botao'          => $this->link_traduzido( 'Vea el caso', $this->caminho_do_case( 'panasonic' ) ),
			'sap_sis_titulo'         => "Conecte SAP a los sistemas\nque mueven su operación",
			'sap_sis_subtitulo'      => 'Integre y gobierne su operación sin importar la tecnología que utilice',
			'sap_sis_1'              => 'CRM',
			'sap_sis_2'              => 'E-commerce',
			'sap_sis_3'              => 'Fiscal',
			'sap_sis_4'              => 'Marketplace',
			'sap_sis_5'              => 'BI',
			'sap_sis_6'              => 'Finanzas',
			'sap_sis_7'              => 'RR. HH.',
			'sap_sis_8'              => 'Sitios web',
			'sap_sis_9'              => 'Aplicaciones',
			'sap_sis_10'             => 'Agente de IA',
			'sap_cc_eyebrow'         => 'PRESERVE SU CLEAN CORE',
			'sap_cc_titulo'          => 'Aproveche su estándar',
			'sap_cc_texto'           => 'Innovación a medida con respeto absoluto por su núcleo. Preserve su Clean Core y actualice su SAP sin miedo.',
			'sap_cc_1_titulo'        => 'Implantación Ágil',
			'sap_cc_1_texto'         => 'Soluciones plug-and-play que dialogan de forma nativa con su SAP y reducen el tiempo de configuración de meses a semanas.',
			'sap_cc_2_titulo'        => 'Actualizaciones sin dolores de cabeza',
			'sap_cc_2_texto'         => 'Actualice su SAP a las versiones más recientes sin romper personalizaciones ni detener su operación.',
			'sap_cc_3_titulo'        => 'Reducción del costo de mantenimiento',
			'sap_cc_3_texto'         => 'Elimine el gasto excesivo en mantenimiento y pruebas de código personalizado ("Z") en cada nuevo ciclo de SAP.',
			'sap_int_eyebrow'        => 'INTEGRACIONES INCLUIDAS',
			'sap_int_titulo'         => "Empiece más rápido con plantillas\nya usadas en entornos reales",
			'sap_int_botao'          => $this->link_traduzido( 'Solicite una demostración', '/contato/' ),
			'sap_int_nota'           => 'Más de 30.000 integraciones listas para usar',
			'sap_int_1_titulo'       => 'SAP + Salesforce',
			'sap_int_1_desc'         => 'Sincronización comercial y de atención.',
			'sap_int_2_titulo'       => 'SAP + VTEX',
			'sap_int_2_desc'         => 'Pedidos, clientes, inventario y facturación.',
			'sap_int_3_titulo'       => 'SAP + RD Station',
			'sap_int_3_desc'         => 'Marketing y ventas alineados.',
			'sap_int_4_titulo'       => 'SAP + Senior',
			'sap_int_4_desc'         => 'RR. HH. y nómina sincronizados automáticamente.',
			'sap_int_5_titulo'       => 'SAP + Sankhya',
			'sap_int_5_desc'         => 'Procesos entre ERP sin retrabajo.',
			'sap_int_6_titulo'       => 'SAP + Thompson Reuters',
			'sap_int_6_desc'         => 'Obligaciones fiscales siempre integradas.',
			'sap_int_7_titulo'       => 'SAP + MV Saúde',
			'sap_int_7_desc'         => 'Datos clínicos y financieros conectados.',
			'sap_int_8_titulo'       => 'SAP + Tasy',
			'sap_int_8_desc'         => 'Información hospitalaria sincronizada.',
			'sap_mig_titulo'         => 'Su migración a SAP S/4HANA con riesgo cero y sin sorpresas',
			'sap_mig_texto'          => 'El soporte de SAP ECC termina en 2027. Planifique su transición ahora y acceda a los mejores especialistas del mercado.',
			'sap_mig_botao'          => $this->link_traduzido( 'Migrar ahora', '/contato/' ),
			'sap_ben_eyebrow'        => 'BENEFICIOS',
			'sap_ben_titulo'         => 'Los beneficios de CLI Connect para su SAP',
			'sap_ben_botao'          => $this->link_traduzido( 'Contáctenos', '/contato/' ),
			'sap_ben_1_rotulo'       => '01 - Especialización SAP',
			'sap_ben_1_desc'         => 'Experiencia en proyectos de integración con SAP S/4HANA.',
			'sap_ben_2_rotulo'       => '02 - Servicio Gestionado',
			'sap_ben_2_desc'         => 'Monitoreo continuo, soporte especializado y evolución constante de su plataforma de integración.',
			'sap_ben_3_rotulo'       => '03 - Mensualidad previsible',
			'sap_ben_3_desc'         => 'Modelo de contratación por suscripción con costos fijos y previsibles, sin sorpresas en el presupuesto.',
			'sap_ben_4_rotulo'       => '04 - Conectores listos',
			'sap_ben_4_desc'         => 'Más de 30.000 conectores listos para uso inmediato, acelerando el tiempo de implantación.',
			'sap_ben_5_rotulo'       => '05 - Gobierno operativo',
			'sap_ben_5_desc'         => 'Visibilidad total de los flujos de integración con trazabilidad, alertas y gestión centralizada.',
			'sap_ben_6_rotulo'       => '06 - Plataforma líder global',
			'sap_ben_6_desc'         => 'Tecnología Boomi, líder en el Cuadrante Mágico de Gartner para plataformas de integración.',
			'sap_aut_eyebrow'        => 'EVENTOS AUTOMÁTICOS',
			'sap_aut_titulo'         => "Convierta los eventos de SAP\nen acciones automáticas",
			'sap_aut_texto'          => "Integre SAP a su operación y convierta eventos en ejecución automática,\nsin interrupciones manuales.",
			'sap_aut_1_etapa1'       => 'Pedido aprobado en SAP',
			'sap_aut_1_etapa2'       => 'Facturación iniciada',
			'sap_aut_1_etapa3'       => 'Cliente notificado',
			'sap_aut_2_etapa1'       => 'Producto actualizado',
			'sap_aut_2_etapa2'       => 'Canales sincronizados',
			'sap_aut_2_etapa3'       => 'Operación actualizada',
			'sap_aut_3_etapa1'       => 'Inventario mínimo',
			'sap_aut_3_etapa2'       => 'Proveedor contactado',
			'sap_aut_3_etapa3'       => 'Reposición iniciada',
			'sap_aut_4_etapa1'       => 'Nueva regulación',
			'sap_aut_4_etapa2'       => 'Impactos identificados',
			'sap_aut_4_etapa3'       => 'Áreas notificadas',
			'sap_aut_5_etapa1'       => 'Indicador fuera de meta',
			'sap_aut_5_etapa2'       => 'Gerente alertado',
			'sap_aut_5_etapa3'       => 'Plan de acción iniciado',
			'sap_faq_eyebrow'        => 'FAQ',
			'sap_faq_titulo'         => 'Preguntas Frecuentes',
			'sap_faq_1_pergunta'     => '¿CLI Connect funciona con entornos SAP S/4HANA on-premise y en la nube?',
			'sap_faq_1_resposta'     => 'Sí. La plataforma de CLI Connect es compatible con entornos SAP S/4HANA tanto on-premise como en la nube (incluido SAP BTP), lo que garantiza flexibilidad sin importar la infraestructura que haya elegido su empresa.',
			'sap_faq_2_pergunta'     => '¿Hace falta desarrollar integraciones en ABAP para conectar SAP S/4HANA?',
			'sap_faq_2_resposta'     => 'No. CLI Connect usa conectores nativos y las API estándar de SAP, lo que elimina la necesidad de desarrollo en ABAP. Eso preserva el Clean Core de su SAP y reduce drásticamente el costo y el tiempo de implantación.',
			'sap_faq_3_pergunta'     => '¿Cuánto tarda en entrar en operación la primera integración?',
			'sap_faq_3_resposta'     => 'Con las plantillas listas de CLI Connect, la primera integración puede entrar en operación en hasta 5 días hábiles, según la complejidad del proceso. Nuestro equipo acompaña todo el proceso de configuración y pruebas.',
		);
	}

	/* =====================================================================
	   CLI CONNECT
	   ===================================================================== */

	/**
	 * Campos de texto da página CLI Connect em espanhol.
	 *
	 * @return array<string,mixed>
	 */
	protected function texto_es_cli_connect() {
		return array(
			'cc_hero_titulo'          => 'Integraciones que mantienen su',
			'cc_hero_titulo_destaque' => 'operación en movimiento',
			'cc_hero_texto'           => 'Conecte SAP, ERP, CRM y aplicaciones críticas en una plataforma preparada para operaciones en tiempo real. Automatice eventos de negocio con seguridad, monitoreo continuo y una única mensualidad.',
			'cc_hero_botao'           => $this->link_traduzido( 'Solicite una demostración', '/contato/' ),
			'cc_brands_titulo'        => 'Grandes empresas confían en CLI',
			'cc_solucao_titulo'       => 'Todo lo que necesita en una sola solución',
			'cc_solucao_1_titulo'     => 'Plataforma Global',
			'cc_solucao_1_texto'      => 'La licencia de la plataforma ya está incluida para conectar sistemas con seguridad, escalabilidad y tecnología reconocida mundialmente.',
			'cc_solucao_1_bullet_1'   => 'Licencia incluida',
			'cc_solucao_1_bullet_2'   => 'Powered by Boomi',
			'cc_solucao_1_bullet_3'   => 'Escala enterprise',
			'cc_solucao_2_titulo'     => 'Servicio Incluido',
			'cc_solucao_2_texto'      => 'Su operación sigue evolucionando después de la implantación. Solicite mejoras, nuevos proyectos y soporte continuo dentro de la misma mensualidad.',
			'cc_solucao_2_bullet_1'   => 'Nuevos proyectos bajo demanda',
			'cc_solucao_2_bullet_2'   => 'Mejoras continuas',
			'cc_solucao_2_bullet_3'   => 'Gestión de incidentes',
			'cc_solucao_3_titulo'     => 'Biblioteca de Integraciones',
			'cc_solucao_3_texto'      => 'Empiece más rápido usando integraciones y conectores ya listos para los principales sistemas del mercado.',
			'cc_solucao_3_bullet_1'   => 'Conectores listos',
			'cc_solucao_3_bullet_2'   => 'Los sistemas más usados',
			'cc_solucao_3_bullet_3'   => 'Menor tiempo de implantación',
			'cc_impl_eyebrow'         => 'Implantación Rápida',
			'cc_impl_titulo'          => 'Menos horas de desarrollo.',
			'cc_impl_titulo_2'        => 'Más velocidad para el negocio.',
			'cc_impl_texto'           => 'Reduzca el esfuerzo técnico necesario para integrar SAP e implemente nuevos proyectos con más agilidad y calidad.',
			'cc_impl_sem_label'       => 'Sin CLI Connect',
			'cc_impl_sem_tempo'       => '1 Mes',
			'cc_impl_sem_etapa_1'     => 'Enviar solicitud',
			'cc_impl_sem_etapa_2'     => 'Definir la necesidad',
			'cc_impl_sem_etapa_3'     => 'Esperar el desarrollo',
			'cc_impl_sem_etapa_4'     => 'Transferencia de datos',
			'cc_impl_sem_etapa_5'     => 'Datos disponibles',
			'cc_impl_sem_etapa_6'     => 'Mantenimiento',
			'cc_impl_sem_etapa_7'     => 'Pruebas y QA',
			'cc_impl_com_label'       => 'Con CLI Connect',
			'cc_impl_com_tempo'       => '5 Días',
			'cc_impl_com_etapa_1'     => 'Enviar solicitud',
			'cc_impl_com_etapa_2'     => 'Definir la necesidad',
			'cc_impl_com_etapa_3'     => 'Datos disponibles',
			'cc_boomi_eyebrow'        => 'Plataforma global',
			'cc_boomi_titulo'         => 'Tecnología de clase mundial con soporte para el mercado latinoamericano',
			'cc_boomi_texto'          => 'Al contratar CLI Connect accede a la misma plataforma que las grandes empresas globales usan para integrar sus sistemas, con la ventaja de un soporte especializado, un precio accesible y el servicio gestionado incluido.',
			'cc_boomi_logos_texto'    => 'Empresas que usan Boomi',
			'cc_boomi_logos_clientes' => array_values(
				array_filter(
					array(
						$this->id_do_seed( 'cliente:cargill', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:cisco', 'cli_cliente' ),
						$this->id_do_seed( 'cliente:dell', 'cli_cliente' ),
					)
				)
			),
			'cc_operacoes_eyebrow'    => 'Operaciones Críticas',
			'cc_operacoes_titulo'     => 'Algunas integraciones',
			'cc_operacoes_titulo_2'   => 'simplemente no pueden fallar',
			'cc_operacoes_texto'      => 'Proteja procesos críticos con integraciones preparadas para operar de forma continua, sin comprometer el negocio.',
			'cc_operacoes_bullet_1'   => 'Procesos industriales en operación continua',
			'cc_operacoes_bullet_2'   => 'Pedidos, cotizaciones y facturación sin fallas ni retrasos',
			'cc_operacoes_bullet_3'   => 'Transacciones y movimientos sin interrupciones',
			'cc_dashboard_eyebrow'    => 'Siga en tiempo real',
			'cc_dashboard_titulo'     => 'Siga cada integración y solicite nuevas demandas en un solo lugar',
			'cc_dashboard_texto'      => 'Tenga visibilidad del avance de los proyectos, siga las solicitudes y envíe nuevas demandas siempre que lo necesite. Todo centralizado en un portal pensado para mantener su operación en evolución continua.',
			'cc_dashboard_botao'      => $this->link_traduzido( 'Solicite una demostración', '#' ),
			'cc_dep_cargo'            => 'Head of Digital and Technology en Panasonic',
			'cc_dep_texto'            => 'Antes hablaba de segundos de respuesta. Hoy hablo de milisegundos.',
			'cc_dep_botao'            => $this->link_traduzido( 'Vea el caso', $this->caminho_do_case( 'panasonic' ) ),
			'cc_dif_eyebrow'          => 'NUESTROS DIFERENCIALES',
			'cc_dif_titulo'           => 'Diseñada para entregar valor continuo',
			'cc_dif_texto'            => 'Conozca los diferenciales que hacen de CLI Connect una alternativa más simple y previsible para operaciones en crecimiento.',
			'cc_dif_botao'            => $this->link_traduzido( 'Solicite una demostración', '/contato/' ),
			'cc_dif_row_1'            => 'Sin costo adicional por proyecto',
			'cc_dif_row_2'            => 'Sin cobro por ejecución, flujo o mensaje',
			'cc_dif_row_3'            => 'Experiencia en los sistemas del mercado latinoamericano',
			'cc_dif_row_4'            => 'Plataforma líder en seguridad y compliance',
			'cc_dif_row_5'            => 'Operación monitoreada y gestionada por CLI',
			'cc_dif_row_6'            => 'Precio acorde con la realidad regional',
			'cc_dif_row_7'            => 'Soporte para sistemas complejos',
			'cc_dif_row_8'            => 'Biblioteca con más de 30.000 integraciones',
			'cc_dif_row_9'            => 'Atención humana y especializada',
			'cc_vantag_eyebrow'       => 'VENTAJAS',
			'cc_vantag_titulo'        => 'Por qué adoptar CLI Connect',
			'cc_vantag_texto'         => 'Desde finanzas hasta atención al cliente, cree flujos integrados y centralice toda la información de su empresa en una sola pantalla, sin complicaciones.',
			'cc_vantag_1_titulo'      => 'Más Productividad',
			'cc_vantag_1_texto'       => 'Automatice tareas repetitivas y libere a sus equipos para actividades más estratégicas y de mayor valor para el negocio.',
			'cc_vantag_2_titulo'      => 'Más Gobierno',
			'cc_vantag_2_texto'       => 'Defina exactamente a qué puede acceder, responder o ejecutar cada agente, con aprobaciones humanas en los puntos críticos.',
			'cc_vantag_3_titulo'      => 'Más Seguridad',
			'cc_vantag_3_texto'       => 'Reduzca los riesgos de exposición de datos sensibles y mantenga el control total sobre el uso de la IA en toda la organización.',
			'cc_vantag_4_titulo'      => 'Más Integración',
			'cc_vantag_4_texto'       => 'Conecte agentes a los sistemas corporativos que su empresa ya usa: ERP, CRM, API y plataformas internas.',
			'cc_vantag_5_titulo'      => 'Más Velocidad',
			'cc_vantag_5_texto'       => 'Cree y ponga agentes en operación con un enfoque visual, simple y escalable, con menor dependencia de TI.',
			'cc_vantag_6_titulo'      => 'Control de Costos',
			'cc_vantag_6_texto'       => 'Monitoree consumo, uso de tokens y límites de operación de los agentes para mantener la IA dentro del presupuesto previsto.',
			'cc_np_eyebrow'           => 'EN LA PRÁCTICA',
			'cc_np_titulo'            => 'Converse con sus datos. El agente hace el resto.',
			'cc_np_texto'             => 'Haga preguntas, ejecute procesos y obtenga respuestas basadas en los datos de su operación.',
			'cc_np_bullet_1'          => 'Consulta varios sistemas a la vez',
			'cc_np_bullet_2'          => 'Ejecuta flujos sin intervención manual',
			'cc_np_bullet_3'          => 'Conserva todo el historial de la operación',
			'cc_parceiro_eyebrow'     => 'AHORRO DE TIEMPO',
			'cc_parceiro_titulo'      => 'Integre sus sistemas con mucho menos tiempo y esfuerzo técnico',
			'cc_parceiro_texto'       => 'Con CLI Connect elimina la complejidad y el tiempo del desarrollo tradicional, logrando integraciones hasta 5 veces más rápidas que las soluciones construidas desde cero.',
			'cc_parceiro_esq_titulo'  => 'CLI Connect',
			'cc_parceiro_esq_sub'     => 'Integraciones listas para usar, con eventos automáticos y sin esfuerzo de desarrollo',
			'cc_parceiro_esq_item_1'  => 'Conectores preconstruidos y listos para usar',
			'cc_parceiro_esq_item_2'  => 'Eventos automáticos y bidireccionales',
			'cc_parceiro_esq_item_3'  => 'Configuración con poco código',
			'cc_parceiro_esq_item_4'  => 'Implantación rápida y segura',
			'cc_parceiro_dir_titulo'  => 'Desarrollo',
			'cc_parceiro_dir_sub'     => 'El enfoque tradicional, con más etapas, dependencias y esfuerzo técnico',
			'cc_parceiro_dir_item_1'  => 'Desarrollo de código en SAP',
			'cc_parceiro_dir_item_2'  => 'Pruebas y correcciones en SAP',
			'cc_parceiro_dir_item_3'  => 'Dependencia de recursos especializados',
			'cc_parceiro_dir_item_4'  => 'Ciclos largos de desarrollo',
			'cc_parceiro_destaque'    => 'Integraciones hechas en hasta <strong>5 veces menos</strong> tiempo. Con más calidad y más seguridad.',
		);
	}

	/* =====================================================================
	   TRABALHE CONOSCO
	   ===================================================================== */

	/**
	 * Campos de texto da página Trabalhe Conosco em espanhol.
	 *
	 * Os campos `*_icone` são chaves de ícone, não texto: ficam de fora e são
	 * copiados do original.
	 *
	 * @return array<string,mixed>
	 */
	protected function texto_es_trabalhe_conosco() {
		return array(
			'hero_eyebrow'         => 'TRABAJA CON NOSOTROS',
			'hero_titulo'          => 'Construya soluciones que mueven a grandes empresas.',
			'hero_texto'           => 'En CLI forma parte de un equipo que conecta tecnologías, simplifica operaciones y ayuda a las empresas a evolucionar todos los días. Trabaje en remoto, participe en proyectos desafiantes y crezca junto a profesionales apasionados por la innovación.',
			'hero_botao'           => $this->link_traduzido( 'Vea nuestras vacantes', 'https://cliconsultoria.inhire.app/vagas' ),
			'sobre_titulo'         => 'Somos CLI',
			'sobre_texto_1'        => 'Con 13 años de historia, somos una empresa de tecnología y colaboración que conecta cultura, personas y soluciones. Nuestro propósito es transformar continuamente la capacidad técnica en capacidad humana, generando un impacto real para clientes, socios y para el mundo.',
			'sobre_texto_2'        => 'Tenemos una trayectoria sólida, con más de 75 clientes activos y más de 500 integraciones listas para usar. Creemos que las grandes soluciones las construyen equipos que colaboran, aprenden constantemente y tienen autonomía para hacer que las cosas pasen.',
			'tc_metrica_1_numero'  => '13',
			'tc_metrica_1_rotulo'  => 'años de historia',
			'tc_metrica_2_numero'  => '+80',
			'tc_metrica_2_rotulo'  => 'clientes activos',
			'tc_metrica_3_numero'  => '30 mil',
			'tc_metrica_3_rotulo'  => 'integraciones ya construidas',
			'tc_frase_parte_1'     => 'La tecnología conecta sistemas.',
			'tc_frase_parte_2'     => 'Pero son las personas las que transforman negocios.',
			'valores_eyebrow'      => 'VALORES',
			'valores_titulo'       => 'Más que integrar tecnología, integramos personas',
			'valores_cta'          => $this->link_traduzido( 'Vea nuestras vacantes', 'https://cliconsultoria.inhire.app/vagas' ),
			'valor_1_titulo'       => 'Confianza',
			'valor_1_texto'        => 'Actuamos con transparencia, seguridad y respeto. Cumplimos lo que prometemos y construimos relaciones de confianza duraderas con clientes y equipos.',
			'valor_2_titulo'       => 'Igualdad',
			'valor_2_texto'        => 'Damos oportunidades a quien desea crecer, valorando el talento y el desarrollo de cada persona sin importar su origen.',
			'valor_3_titulo'       => 'Éxito del Cliente',
			'valor_3_texto'        => 'El problema del cliente es nuestro. Lo resolvemos con conocimiento del negocio y nos enorgullece cada entrega bien hecha.',
			'valor_4_titulo'       => 'Innovación',
			'valor_4_texto'        => 'Impulsamos nuevas ideas y la creatividad para anticipar tendencias y generar soluciones innovadoras con responsabilidad.',
			'valor_5_titulo'       => 'Colaboración',
			'valor_5_texto'        => 'Somos un equipo unido. Compartimos conocimiento, logros y aprendizajes con espíritu de colaboración y armonía.',
			'dep_cargo'            => 'Equipo Tech',
			'dep_texto'            => 'El trabajo en equipo en CLI es real y ocurre todos los días. Contar con un equipo que se ayuda para resolver problemas complejos y que está en total sintonía con herramientas innovadoras hace que nuestra rutina sea ligera y satisfactoria. Al final, el éxito de nuestras entregas es fruto de ese ecosistema, donde recibimos apoyo de todas las áreas de la empresa.',
			'beneficios_eyebrow'   => 'BENEFICIOS',
			'beneficios_titulo'    => 'Todo para que pueda hacer su mejor trabajo.',
			'beneficios_subtitulo' => 'Sabemos que necesita estructura para dar lo mejor de sí. Por eso ofrecemos beneficios que marcan la diferencia en el día a día.',
			'beneficio_1_titulo'   => 'Salud y Bienestar',
			'beneficio_1_texto'    => 'Plan de salud Bradesco y plan odontológico Odontomais, con amplia cobertura para usted y sus dependientes.',
			'beneficio_2_titulo'   => 'Trabajo Remoto',
			'beneficio_2_texto'    => 'Ayuda mensual para cubrir los costos de la oficina en casa y mantener cómoda su rutina de trabajo remoto.',
			'beneficio_3_titulo'   => 'Alimentación',
			'beneficio_3_texto'    => 'Ayuda mensual por pix, que puede usar como prefiera para su alimentación durante el mes.',
			'beneficio_4_titulo'   => 'Apoyo a la Familia',
			'beneficio_4_texto'    => 'Ayuda de guardería para hijos de hasta 5 años, porque la familia también forma parte del éxito de cada uno.',
			'beneficio_5_titulo'   => 'Calidad de vida',
			'beneficio_5_texto'    => 'Acceso a TotalPass: gimnasios, deportes y actividades de bienestar para mantener la salud física al día.',
			'beneficio_6_titulo'   => 'Día libre de cumpleaños',
			'beneficio_6_texto'    => 'El día de su cumpleaños, tómese el día libre. Merece celebrarlo como quiera.',
			'jeito_titulo'         => 'La forma de ser de CLI',
			'jeito_texto'          => 'Más que reglas, estos principios orientan la forma en que trabajamos todos los días.',
			'jeito_item_1_titulo'  => 'Transparencia primero',
			'jeito_item_1_texto'   => 'Incluso cuando es difícil, elegimos decir y escuchar con claridad.',
			'jeito_item_2_titulo'  => 'Protagonismo',
			'jeito_item_2_texto'   => 'Asumimos el problema del cliente, y de la empresa, como propio.',
			'jeito_item_3_titulo'  => 'Escucha atenta',
			'jeito_item_3_texto'   => 'Pedimos ayuda, recibimos feedback y cambiamos el rumbo cuando tiene sentido.',
			'jeito_item_4_titulo'  => 'Profundidad técnica',
			'jeito_item_4_texto'   => 'Estudiamos, documentamos, registramos. Aprender es parte del trabajo.',
			'jeito_item_5_titulo'  => 'Compartir',
			'jeito_item_5_texto'   => 'Compartimos conocimiento, tiempo y oportunidades.',
			'jeito_botao'          => $this->link_traduzido( 'Ver vacantes', 'https://cliconsultoria.inhire.app/vagas' ),
			'tc_blog_titulo'       => 'Conozca más sobre CLI',
		);
	}

	/* =====================================================================
	   CLI SIGNATURE
	   ===================================================================== */

	/**
	 * Campos de texto da página CLI Signature em espanhol.
	 *
	 * @return array<string,mixed>
	 */
	protected function texto_es_cli_signature() {
		return array(
			'cs_hero_eyebrow'          => 'CLI SIGNATURE',
			'cs_hero_titulo'           => 'Los proyectos críticos exigen más que ejecución. Exigen firma.',
			'cs_hero_texto'            => 'La modalidad premium de CLI Connect para empresas que operan proyectos críticos con especialistas dedicados, gobierno ejecutivo y acompañamiento continuo.',
			'cs_hero_botao'            => $this->link_traduzido( 'Solicite una demostración', '/contato/' ),
			'cs_cenarios_eyebrow'      => 'Cuando el desafío exige más',
			'cs_cenarios_titulo'       => '¿Para quién es CLI Signature?',
			'cs_cenarios_texto'        => 'Ideal para empresas en escenarios de alta complejidad que exigen acompañamiento especializado.',
			'cs_cenarios_1_titulo'     => 'Transformación Digital',
			'cs_cenarios_1_texto'      => 'Modernización de arquitectura, reemplazo de sistemas heredados, nuevos canales digitales y experiencias omnicanal.',
			'cs_cenarios_2_titulo'     => 'Integraciones Críticas',
			'cs_cenarios_2_texto'      => 'SAP, Salesforce, Totvs, ERP, CRM, e-commerce, fiscal, datos, API y plataformas de negocio.',
			'cs_cenarios_3_titulo'     => 'Entornos Complejos',
			'cs_cenarios_3_texto'      => 'Integraciones que necesitan funcionar con estabilidad, trazabilidad y soporte continuo.',
			'cs_cenarios_4_titulo'     => 'Múltiples Stakeholders',
			'cs_cenarios_4_texto'      => 'TI, negocio, proveedores, consultoras, equipos internos y áreas ejecutivas.',
			'cs_cenarios_5_titulo'     => 'Iniciativas Estratégicas',
			'cs_cenarios_5_texto'      => 'Roadmap, priorización, gestión de riesgos, arquitectura, SLA y comunicación ejecutiva.',
			'cs_cenarios_6_titulo'     => 'Operaciones de Misión Crítica',
			'cs_cenarios_6_texto'      => 'Procesos que no pueden interrumpirse y exigen monitoreo, gobierno y respuesta rápida.',
			'cs_pilares_eyebrow'       => 'modelo enterprise',
			'cs_pilares_titulo'        => 'La experiencia enterprise de CLI Connect',
			'cs_pilares_texto'         => 'CLI Signature amplía la experiencia de CLI Connect con una capa dedicada de gobierno, atención ejecutiva y evolución continua para operaciones estratégicas.',
			'cs_pilares_1_titulo'      => 'Excelencia Técnica CLI',
			'cs_pilares_1_texto'       => 'Especialistas en Integraciones, API, Datos, IA, iPaaS, Salesforce, SAP, ERP, Fiscal y plataformas corporativas.',
			'cs_pilares_2_titulo'      => 'Gobierno Ejecutivo',
			'cs_pilares_2_texto'       => 'Rituales periódicos, indicadores, acompañamiento estratégico y evolución planificada.',
			'cs_pilares_3_titulo'      => 'Acompañamiento Exclusivo',
			'cs_pilares_3_texto'       => 'Gerente de Proyecto/Relación y Arquitecto dedicados para garantizar decisiones técnicas sólidas y alineadas al negocio.',
			'cs_diferenciais_titulo_1' => 'Más que una plataforma.',
			'cs_diferenciais_titulo_2' => 'Una operación acompañada de forma continua.',
			'cs_diferenciais_texto'    => 'CLI Signature amplía la experiencia de CLI Connect con una capa dedicada de gobierno, atención ejecutiva y evolución continua para operaciones estratégicas.',
			'cs_diferenciais_1_titulo' => 'Especialistas dedicados',
			'cs_diferenciais_1_texto'  => 'Profesionales que siguen su operación de cerca.',
			'cs_diferenciais_2_titulo' => 'Gobierno ejecutivo',
			'cs_diferenciais_2_texto'  => 'Reuniones, indicadores y evolución planificada.',
			'cs_diferenciais_3_titulo' => 'Atención prioritaria',
			'cs_diferenciais_3_texto'  => 'Flujos exclusivos para demandas críticas.',
			'cs_diferenciais_4_titulo' => 'Evolución continua',
			'cs_diferenciais_4_texto'  => 'Nuevas integraciones y mejoras forman parte del servicio.',
			'cs_diferenciais_5_titulo' => 'Monitoreo',
			'cs_diferenciais_5_texto'  => 'Visibilidad constante sobre toda la operación.',
			'cs_diferenciais_6_titulo' => 'Excelencia operativa',
			'cs_diferenciais_6_texto'  => 'Buenas prácticas desde la arquitectura hasta el soporte.',
			'cs_operacao_eyebrow'      => 'Operación Gestionada',
			'cs_operacao_titulo_1'     => 'Garantice una operación continua',
			'cs_operacao_titulo_2'     => 'y preparada para evolucionar',
			'cs_operacao_texto'        => 'Cuente con una operación estructurada para sostener, monitorear y evolucionar continuamente su entorno, con SLA acordado, gobierno operativo y procesos definidos para lograr más previsibilidad y eficiencia.',
			'cs_operacao_1_titulo'     => 'Catálogo de Servicios',
			'cs_operacao_1_texto'      => 'Una atención estructurada con SLA acordado, priorización por criticidad y gestión organizada de las demandas.',
			'cs_operacao_2_titulo'     => 'Gestión de Incidentes',
			'cs_operacao_2_texto'      => 'Resuelva incidencias con agilidad, trazabilidad e indicadores que dan visibilidad sobre la atención.',
			'cs_operacao_3_titulo'     => 'Mejoras Evolutivas',
			'cs_operacao_3_texto'      => 'Evolucione continuamente su entorno con monitoreo operativo, mejoras planificadas y una base de conocimiento siempre actualizada.',
			'cs_operacao_4_titulo'     => 'Documentación',
			'cs_operacao_4_texto'      => 'Mantenga una documentación técnica completa y una base de conocimiento organizada para garantizar continuidad y estandarización operativa.',
			'cs_gestor_titulo'         => 'Gerente de Proyecto y Relación Dedicado',
			'cs_gestor_texto'          => 'Tenga un punto focal exclusivo para acompañar el recorrido de su cliente. Organice prioridades, conduzca los ritos de gobierno con maestría y garantice una comunicación clara y fluida entre negocio, tecnología y operación.',
			'cs_gestor_botao'          => $this->link_traduzido( 'Solicite una demostración', '/contato/' ),
			'cs_gestor_1_titulo'       => 'Seguimiento del Roadmap',
			'cs_gestor_2_titulo'       => 'Gobierno del Backlog',
			'cs_gestor_3_titulo'       => 'Reuniones Ejecutivas y Operativas',
			'cs_gestor_4_titulo'       => 'Gestión de Prioridades',
			'cs_gestor_5_titulo'       => 'Comunicación con Stakeholders',
			'cs_gestor_6_titulo'       => 'Seguimiento de SLA e Indicadores',
			'cs_gestor_7_titulo'       => 'Plan de Evolución Continua',
			'cs_arquiteto_titulo'      => 'Arquitecto Dedicado',
			'cs_arquiteto_texto'       => 'Un especialista sénior responsable de garantizar que las decisiones técnicas estén alineadas con la estrategia, la escalabilidad, la seguridad y la evolución de la empresa.',
			'cs_arquiteto_botao'       => $this->link_traduzido( 'Solicite una demostración', '/contato/' ),
			'cs_arquiteto_1_titulo'    => 'Diseño de Arquitectura',
			'cs_arquiteto_2_titulo'    => 'Revisión Técnica de Soluciones',
			'cs_arquiteto_3_titulo'    => 'Definición de Estándares',
			'cs_arquiteto_4_titulo'    => 'Apoyo en Decisiones Críticas',
			'cs_arquiteto_5_titulo'    => 'Estrategia de API e Integraciones',
			'cs_arquiteto_6_titulo'    => 'Evaluación de Riesgos Técnicos',
			'cs_arquiteto_7_titulo'    => 'Roadmap de Modernización',
		);
	}

	/* =====================================================================
	   CONTATO
	   ===================================================================== */

	/**
	 * Campos de texto da página Contato em espanhol.
	 *
	 * E-mail, telefone e redes sociais não são texto traduzível — ficam de
	 * fora e são copiados do original.
	 *
	 * @return array<string,mixed>
	 */
	protected function texto_es_contato() {
		$campos = array(
			'ct_clientes_subtitulo' => 'Grandes empresas confían en CLI',
			'ct_form_titulo'        => 'Solicite una propuesta para su operación',
			'ct_form_texto'         => 'Resuelva sus dudas, evalúe posibilidades y descubra cómo CLI puede apoyar su operación con integración, automatización e IA corporativa.',
		);

		return $campos;
	}

	/* =====================================================================
	   POLÍTICA DE PRIVACIDAD
	   ===================================================================== */

	/**
	 * Campos de texto de la Política de Privacidad en español.
	 *
	 * @return array<string,mixed>
	 */
	protected function texto_es_privacidade() {
		return array(
			'pv_titulo'        => 'Política de Privacidad',
			'pv_lead'          => 'Esta política explica qué datos personales recoge CLI Connect, por qué los recoge y cuáles son sus derechos sobre ellos.',
			'pv_atualizado_em' => 'Actualizado el 28 de agosto de 2026',
			'pv_corpo'         => '<p><strong>Borrador pendiente de revisión jurídica.</strong> La estructura siguiente cubre las secciones exigidas por la ley brasileña de protección de datos (LGPD, Ley 13.709/2018) y debe ser revisada y aprobada por el área jurídica de CLI antes de su publicación definitiva. El texto es editable en el panel, sin necesidad de despliegue.</p>
<h2>1. Quién es la responsable de sus datos</h2>
<p>CLI Connect es la responsable de los datos personales tratados en este sitio y decide cómo y para qué se utilizan. Los datos de contacto para asuntos de privacidad están en la última sección de esta política.</p>
<h2>2. Qué datos recogemos</h2>
<p>Recogemos solo los datos necesarios para responder a quien nos contacta y para entender cómo se usa el sitio:</p>
<ul>
<li><strong>Datos que usted facilita:</strong> nombre, correo electrónico, teléfono y el contenido del mensaje enviado por los formularios del sitio.</li>
<li><strong>Datos de navegación:</strong> páginas visitadas, origen del acceso e información técnica del navegador, recogidos mediante cookies y herramientas de análisis.</li>
</ul>
<h2>3. Para qué los usamos</h2>
<ul>
<li>Responder a solicitudes de contacto, propuestas y demostraciones.</li>
<li>Enviar comunicaciones sobre nuestros servicios, cuando usted lo autoriza.</li>
<li>Entender el uso del sitio y mejorar la experiencia de navegación.</li>
<li>Cumplir obligaciones legales y regulatorias.</li>
</ul>
<h2>4. Bases legales</h2>
<p>El tratamiento se apoya en el consentimiento del titular, en la ejecución de un contrato o de trámites previos solicitados por el titular, en el cumplimiento de una obligación legal y en el interés legítimo de CLI Connect, siempre dentro de los límites del artículo 7 de la LGPD.</p>
<h2>5. Con quién los compartimos</h2>
<p>No vendemos datos personales. Compartimos información únicamente con proveedores que operan por nuestra cuenta — alojamiento, correo, CRM y herramientas de análisis —, siempre bajo contrato y en la medida necesaria para la prestación del servicio, o cuando lo exija una autoridad competente.</p>
<h2>6. Cookies</h2>
<p>Usamos cookies para mantener el sitio en funcionamiento, recordar preferencias y medir audiencia. Puede bloquear o borrar las cookies en la configuración de su navegador; algunas funcionalidades pueden dejar de funcionar correctamente.</p>
<h2>7. Cuánto tiempo los conservamos</h2>
<p>Conservamos los datos durante el tiempo necesario para las finalidades de esta política o durante los plazos exigidos por ley. Cumplida la finalidad y vencidos los plazos legales, los datos se eliminan o se anonimizan.</p>
<h2>8. Sus derechos</h2>
<p>La LGPD le garantiza el derecho a confirmar la existencia del tratamiento, acceder a sus datos, corregir datos incompletos o desactualizados, solicitar la anonimización, el bloqueo o la eliminación, pedir la portabilidad, revocar el consentimiento y obtener información sobre con quién compartimos sus datos. Para ejercer cualquiera de ellos, utilice el contacto indicado abajo.</p>
<h2>9. Seguridad</h2>
<p>Adoptamos medidas técnicas y administrativas para proteger los datos personales contra accesos no autorizados, pérdida, alteración o divulgación indebida.</p>
<h2>10. Cambios en esta política</h2>
<p>Esta política puede actualizarse en cualquier momento. La fecha de la última actualización figura en la parte superior de esta página.</p>
<h2>11. Hable con nosotros sobre privacidad</h2>
<p>Las dudas o solicitudes relacionadas con sus datos personales pueden enviarse a <a href="mailto:atendimento@cliconsultoria.com.br">atendimento@cliconsultoria.com.br</a>.</p>',
		);
	}

	/* =====================================================================
	   PROGRAMA DE PARCEIROS
	   ===================================================================== */

	/**
	 * Campos de texto da página Programa de Parceiros em espanhol.
	 *
	 * Ícones, imagens, quantidade de logos e a lista de FAQ são copiados do
	 * original (a FAQ já traduzida, ver traduzir_idioma()).
	 *
	 * @return array<string,mixed>
	 */
	protected function texto_es_programa_de_parceiros() {
		$quero_ser_parceiro = $this->link_traduzido( 'Quiero ser socio', '/contato/' );

		return array(
			'pp_hero_eyebrow'            => 'PROGRAMA DE SOCIOS',
			'pp_hero_titulo'             => 'Gane un 10% en ingresos recurrentes mensuales',
			'pp_hero_texto'              => 'Recomiende oportunidades de integración, automatización y datos a CLI Connect, amplíe el valor que entrega a sus clientes y *reciba el 10%* de la facturación neta mensual.',
			'pp_hero_botao'              => $quero_ser_parceiro,
			'pp_hero_botao_2'            => $this->link_traduzido( 'Cómo funciona', '#como-funciona' ),
			'pp_hero_metrica_1_numero'   => '10%',
			'pp_hero_metrica_1_rotulo'   => 'Ingresos mensuales',
			'pp_hero_metrica_2_numero'   => '5 días',
			'pp_hero_metrica_2_rotulo'   => 'Para la primera solución',
			'pp_hero_metrica_3_numero'   => '+30 mil',
			'pp_hero_metrica_3_rotulo'   => 'Integraciones listas',
			'pp_parceria_eyebrow'        => 'ASOCIACIÓN',
			'pp_parceria_titulo'         => 'Amplíe su cartera de clientes ofreciendo nuevas capacidades',
			'pp_parceria_texto'          => 'Atienda demandas complejas de datos, automatización y tecnología en las cuentas que ya tiene o con nuevos clientes.',
			'pp_parceria_1_titulo'       => 'Amplíe su oferta',
			'pp_parceria_1_texto'        => 'Atienda nuevas demandas de su cartera con el portafolio y la capacidad técnica de CLI.',
			'pp_parceria_2_titulo'       => 'Enfóquese en lo importante',
			'pp_parceria_2_texto'        => 'No es necesario crear una nueva estructura especializada para cada demanda complementaria.',
			'pp_parceria_3_titulo'       => 'Cree nuevos ingresos',
			'pp_parceria_3_texto'        => 'Convierta los negocios originados por la asociación en una fuente de ingresos recurrentes.',
			'pp_como_titulo'             => 'Cómo funciona',
			'pp_como_1_titulo'           => 'Identifique',
			'pp_como_1_texto'            => 'Encuentre en su cartera una demanda que CLI Connect pueda atender.',
			'pp_como_2_titulo'           => 'Registre',
			'pp_como_2_texto'            => 'Presente la oportunidad a CLI Connect y regístrela según el proceso del programa.',
			'pp_como_3_titulo'           => 'Desarrolle',
			'pp_como_3_texto'            => 'CLI Connect y el socio trabajan juntos la oportunidad hasta la contratación por parte del cliente.',
			'pp_como_4_titulo'           => 'Reciba',
			'pp_como_4_texto'            => 'Con el pago del cliente, la comisión se calcula según las reglas de la asociación.',
			'pp_perfil_eyebrow'          => 'PERFIL DEL SOCIO',
			'pp_perfil_titulo'           => 'Para quien quiere ampliar su oferta',
			'pp_perfil_texto'            => 'Sume nuevos frentes tecnológicos a sus proyectos con el soporte técnico de CLI Connect y agregue capacidad a su operación.',
			'pp_perfil_1_titulo'         => 'Consultoras',
			'pp_perfil_1_texto'          => 'Amplíe su actuación con capacidades tecnológicas complementarias.',
			'pp_perfil_2_titulo'         => 'Integradoras y software houses',
			'pp_perfil_2_texto'          => 'Atienda proyectos que exigen conexiones con sistemas y entornos externos.',
			'pp_perfil_3_titulo'         => 'Agencias',
			'pp_perfil_3_texto'          => 'Extienda su entrega a las demandas técnicas que surgen en la operación de los clientes.',
			'pp_perfil_4_titulo'         => 'Revendedores y empresas de tecnología',
			'pp_perfil_4_texto'          => 'Sume nuevas soluciones a su portafolio sin tener que internalizar toda la especialización.',
			'pp_estrutura_titulo'        => "Usted encuentra la oportunidad.\nCLI entrega la estructura.",
			'pp_estrutura_texto'         => 'Amplíe lo que ofrece a sus clientes con una estructura especializada para desarrollar, implementar y sostener nuevas soluciones.',
			'pp_estrutura_1_titulo'      => 'Tecnología',
			'pp_estrutura_1_texto'       => 'La plataforma Boomi y capacidades propias para atender distintos escenarios de integración, automatización y datos.',
			'pp_estrutura_2_titulo'      => 'Especialistas',
			'pp_estrutura_2_texto'       => 'Un equipo técnico dedicado para diseñar, desarrollar e implementar cada solución junto con usted.',
			'pp_estrutura_3_titulo'      => 'Operación',
			'pp_estrutura_3_texto'       => 'Monitoreo, soporte y sostenimiento continuo de las integraciones una vez que el proyecto entra en producción.',
			'pp_estrutura_4_titulo'      => 'Gobernanza',
			'pp_estrutura_4_texto'       => 'Procesos, documentación y control de accesos que aportan seguridad y cumplimiento a cada entrega.',
			'pp_atuacao_eyebrow'         => 'ACTUACIÓN CONJUNTA',
			'pp_atuacao_titulo'          => 'Concéntrese en lo que ya hace mejor',
			'pp_atuacao_texto'           => 'Identifique oportunidades en su cartera, conecte CLI Connect con sus clientes y participe del desarrollo comercial. Nosotros aportamos la capacidad necesaria para hacer viable la solución.',
			'pp_atuacao_parceiro_rotulo' => 'ATRIBUCIONES',
			'pp_atuacao_parceiro_titulo' => 'Usted',
			'pp_atuacao_parceiro_tag'    => 'Socio',
			'pp_atuacao_parceiro_1'      => 'Identifique oportunidades en su cartera',
			'pp_atuacao_parceiro_2'      => 'Presente las oportunidades a CLI Connect',
			'pp_atuacao_parceiro_3'      => 'Participe del desarrollo comercial',
			'pp_atuacao_parceiro_rodape' => 'Foco total en la relación y en la originación del negocio.',
			'pp_atuacao_cli_selo'        => 'OPERACIÓN TÉCNICA',
			'pp_atuacao_cli_rotulo'      => 'ATRIBUCIONES',
			'pp_atuacao_cli_titulo'      => 'CLI Connect',
			'pp_atuacao_cli_tag'         => 'Hub & Infra',
			'pp_atuacao_cli_1'           => 'Evalúa y aprueba la oportunidad',
			'pp_atuacao_cli_2'           => 'Estructura la solución',
			'pp_atuacao_cli_3'           => 'Aprueba la propuesta comercial',
			'pp_atuacao_cli_4'           => 'Contrata y factura al cliente final',
			'pp_atuacao_cli_5'           => 'Opera los servicios CLI Connect',
			'pp_atuacao_cli_6'           => 'Calcula y paga la comisión',
			'pp_atuacao_cli_rodape'      => 'Garantía de cumplimiento, facturación y sostenimiento operativo.',
			'pp_faq_eyebrow'             => 'FAQ',
			'pp_faq_titulo'              => 'Preguntas Frecuentes',
			'pp_cta_eyebrow'             => 'PRÓXIMO PASO',
			'pp_cta_titulo'              => '¿Listo para convertir su cartera en ingresos recurrentes?',
			'pp_cta_texto'               => 'Conéctese con nuestros consultores corporativos y reciba la documentación completa de la asociación.',
			'pp_cta_botao'               => $quero_ser_parceiro,
		);
	}

	/* =====================================================================
	   CLI FLOW
	   ===================================================================== */

	/**
	 * Campos de texto de la página CLI Flow en español.
	 *
	 * Los datos de los mockups (valores, códigos, nombres de clientes) siguen
	 * los del portugués.
	 *
	 * @return array<string,mixed>
	 */
	protected function texto_es_cli_flow() {
		$demo = $this->link_traduzido( 'Agende una demostración', '/contato/' );

		return array(
			// 1. Hero.
			'cf_hero_eyebrow'           => 'CLI Flow - powered by Boomi',
			'cf_hero_titulo'            => 'Cree las pantallas que le faltan a su flujo',
			'cf_hero_titulo_destaque'   => 'ya integradas con sus sistemas',
			'cf_hero_texto'             => '<strong>Deje el correo y las planillas:</strong> arme portales, registros y aprobaciones sin código. Y, si lo prefiere, CLI los diseña con usted.',
			'cf_hero_botao_1'           => $demo,
			'cf_hero_botao_2'           => array(
				'title'  => 'Vea cómo funciona',
				'url'    => '#como-funciona',
				'target' => '',
			),
			'cf_hero_logos_label'       => 'Integrado con sus sistemas',

			// 2. Mockup.
			'cf_mockup_legenda'         => 'Ejemplos más solicitados',
			'cf_mockup_1_label'         => 'Portal del Cliente',
			'cf_mockup_2_label'         => 'Portal del Proveedor',
			'cf_mockup_3_label'         => 'Registro de Producto',

			// 4. Dores.
			'cf_dores_titulo'           => '¿Cuál de estos está frenando a su área?',
			'cf_dores_1_citacao'        => 'Un pedido detenido en una aprobación que nadie sabe dónde está.',
			'cf_dores_1_departamento'   => 'Compras',
			'cf_dores_2_citacao'        => 'Una factura que no coincide con el pedido y una multa por atraso.',
			'cf_dores_2_departamento'   => 'Finanzas',
			'cf_dores_3_citacao'        => 'Una venta que se enfría esperando la aprobación de crédito.',
			'cf_dores_3_departamento'   => 'Comercial',
			'cf_dores_4_citacao'        => 'Documentos en cinco lugares y un colaborador que llega sin acceso.',
			'cf_dores_4_departamento'   => 'Personas',
			'cf_dores_5_citacao'        => 'Un alta de proveedor que tarda semanas y vuelve con errores.',
			'cf_dores_5_departamento'   => 'Suministros',
			'cf_dores_6_citacao'        => 'Rechazos fiscales resueltos uno por uno, en una planilla.',
			'cf_dores_6_departamento'   => 'Fiscal',

			// 6. Casos de uso.
			'cf_casos_titulo'           => 'Empiece con un proceso listo.',
			'cf_casos_titulo_destaque'  => 'O diseñe el suyo.',
			'cf_casos_selo_texto'       => 'Integrado con SAP',
			'cf_casos_1_departamento'   => 'Compras y Finanzas',
			'cf_casos_1_1_nome'         => 'Solicitud de compra',
			'cf_casos_1_2_nome'         => 'Cuentas por pagar',
			'cf_casos_1_3_nome'         => 'Cuentas por cobrar y cobranza',
			'cf_casos_1_4_nome'         => 'Aviso de pago',
			'cf_casos_1_5_nome'         => 'Reembolso de gastos',
			'cf_casos_1_6_nome'         => 'Viajes corporativos',
			'cf_casos_2_departamento'   => 'Comercial',
			'cf_casos_2_1_nome'         => 'Aprobación de crédito',
			'cf_casos_2_2_nome'         => 'Onboarding de clientes',
			'cf_casos_2_3_nome'         => 'Calificación de leads',
			'cf_casos_2_4_nome'         => 'Encuesta de satisfacción (NPS)',
			'cf_casos_3_departamento'   => 'Suministros y Fiscal',
			'cf_casos_3_1_nome'         => 'Homologación de proveedores',
			'cf_casos_3_2_nome'         => 'Emisión de factura individual',
			'cf_casos_3_3_nome'         => 'Corrección de factura rechazada',
			'cf_casos_4_departamento'   => 'Personas',
			'cf_casos_4_1_nome'         => 'Reclutamiento y contratación',
			'cf_casos_4_2_nome'         => 'Solicitudes de RR. HH.',
			'cf_casos_4_3_nome'         => 'Desvinculación',
			'cf_casos_5_departamento'   => 'TI',
			'cf_casos_5_1_nome'         => 'Solicitud de accesos y equipos',
			'cf_casos_5_2_nome'         => 'Tickets de soporte',
			'cf_casos_6_departamento'   => 'Jurídico',
			'cf_casos_6_1_nome'         => 'Aprobación de contratos',
			'cf_casos_6_2_nome'         => 'Citaciones y plazos',

			// 7. CTA "Não achou o seu?".
			'cf_ia_titulo'              => '¿No encontró el suyo? Traiga el problema.',
			'cf_ia_titulo_destaque'     => 'CLI diseña la solución con usted.',
			'cf_ia_botao'               => $this->link_traduzido( 'Mapear mi proceso', '/contato/' ),

			// 8. Cards.
			'cf_cards_1_titulo'         => 'Un flujo con sus reglas',
			'cf_cards_1_texto'          => 'Defina etapas, plazos y niveles de aprobación como su empresa decida.',
			'cf_cards_2_titulo'         => 'Una pantalla que reúne varios sistemas',
			'cf_cards_2_texto'          => 'Dé al vendedor precios y contratos de Protheus y negocios de HubSpot en la misma pantalla.',
			'cf_cards_3_titulo'         => 'Un portal para clientes y proveedores',
			'cf_cards_3_texto'          => 'Reciba datos y certificados directo del proveedor; el registro ya nace en el ERP.',
			'cf_cards_4_titulo'         => 'Un panel con datos vivos',
			'cf_cards_4_texto'          => 'Siga indicadores leídos directo de sus sistemas, sin planillas de por medio.',

			// 9. SAP.
			'cf_sap_eyebrow'            => 'Facilitando su proceso',
			'cf_sap_titulo'             => 'Del pedido a SAP sin',
			'cf_sap_titulo_destaque'    => 'que nadie vuelva a digitar.',
			'cf_sap_1_titulo'           => 'Pida en una pantalla hecha para su proceso',
			'cf_sap_1_texto'            => 'Saldos, registros e historial de sus sistemas ya aparecen en pantalla.',
			'cf_sap_2_titulo'           => 'Apruebe con un clic, por correo o en Teams',
			'cf_sap_2_texto'            => 'El agente de IA revisa antes y entrega su opinión. La IA recomienda; usted decide.',
			'cf_sap_3_titulo'           => 'Listo: el pedido ya está en SAP',
			'cf_sap_3_texto'            => 'Cuando aprueba el último nivel, la orden de compra nace en SAP y todo queda en el historial para auditoría.',
			'cf_sap_botao'              => $this->link_traduzido( 'Solicitar demostración', '/contato/' ),
			'cf_sap_card_titulo'        => 'Solicitud de compra',
			'cf_sap_card_subtitulo'     => 'RC-0148 · R$ 18.450,00',
			'cf_sap_card_selo'          => 'Integrado con SAP',
			'cf_sap_card_agente'        => 'Preanálisis del Agente de Compras · 40 s',
			'cf_sap_check_1_selo'       => 'OK',
			'cf_sap_check_1_titulo'     => 'Saldo del centro de costo 4102',
			'cf_sap_check_1_texto'      => 'R$ 62.300 disponibles en SAP; después de la compra quedan R$ 43.850.',
			'cf_sap_check_2_selo'       => 'OK',
			'cf_sap_check_2_titulo'     => 'Proveedor homologado',
			'cf_sap_check_2_texto'      => 'Rolamentos Brasil Ltda. activo en el registro, sin pendientes fiscales.',
			'cf_sap_check_3_selo'       => 'Atención',
			'cf_sap_check_3_titulo'     => 'Precio del kit de sellado 6% más alto',
			'cf_sap_check_3_texto'      => 'Promedio de las 3 últimas compras: R$ 1.180,00. Diferencia total: R$ 840,00.',
			'cf_sap_check_4_selo'       => 'Atención',
			'cf_sap_check_4_titulo'     => '2 cotizaciones adjuntas',
			'cf_sap_check_4_texto'      => 'La política de compras pide 3 cotizaciones por encima de R$ 15 mil.',
			'cf_sap_check_5_selo'       => 'OK',
			'cf_sap_check_5_titulo'     => 'Sin pedido duplicado',
			'cf_sap_check_5_texto'      => 'Ningún pedido igual para el mismo centro de costo en los últimos 60 días.',
			'cf_sap_card_resumo_titulo' => 'Resumen para el aprobador',
			'cf_sap_card_resumo_texto'  => 'Compra dentro del presupuesto y con proveedor regular. Sugiero aprobar pidiendo una 3.ª cotización del kit de sellado o la justificación del precio.',
			'cf_sap_card_botao_1'       => 'Aprobar con salvedad',
			'cf_sap_card_botao_2'       => 'Pedir 3.ª cotización',
			'cf_sap_card_botao_3'       => 'Rechazar',

			// 10. Integrações.
			'cf_int_titulo'             => 'A su manera.',
			'cf_int_titulo_destaque'    => 'Integrado con sus grandes sistemas.',
			'cf_int_selo_antes'         => 'Hasta',
			'cf_int_selo_depois'        => 'menos licencias',
			'cf_int_janela_titulo'      => 'Consulta de servicios',
			'cf_int_janela_fontes'      => 'Datos en vivo de',
			'cf_int_busca_label'        => 'Buscar servicio',
			'cf_int_busca_valor'        => 'Soporte',
			'cf_int_col_1'              => 'Código',
			'cf_int_col_2'              => 'Servicio',
			'cf_int_col_3'              => 'Precio',
			'cf_int_col_4'              => 'Contratos activos',
			'cf_int_col_5'              => 'Negocios abiertos',
			'cf_int_linha_1'            => 'SV-101 | Soporte técnico mensual | R$ 2.400,00 / mes | 38 | 4 negocios · R$ 115 mil',
			'cf_int_linha_2'            => 'SV-205 | Implementación de sistema | R$ 18.000,00 | 12 | 2 negocios · R$ 36 mil',
			'cf_int_linha_3'            => 'SV-310 | Mantenimiento preventivo | R$ 950,00 / visita | 54 | 1 negocio · R$ 11 mil',
			'cf_int_linha_4'            => 'SV-412 | Capacitación in company | R$ 3.200,00 / grupo | 6 | —',
			'cf_int_detalhe_sub'        => 'SV-101 · R$ 2.400,00 / mes',
			'cf_int_detalhe_titulo'     => 'Soporte técnico mensual',
			'cf_int_bloco_1_titulo'     => 'Contratos activos por sucursal',
			'cf_int_bloco_1_linhas'     => "Uberlândia | 20 contratos\nBelo Horizonte | 12 contratos\nSão Paulo | 6 contratos",
			'cf_int_bloco_2_titulo'     => 'Negocios en HubSpot',
			'cf_int_bloco_2_linhas'     => "Grupo Andrade | 12 meses · Propuesta\nClínica Vida | 24 meses · Negociación\nEscola Horizonte | 12 meses · Calificado",
			'cf_int_botao_1'            => 'Crear propuesta',
			'cf_int_botao_2'            => 'Abrir orden de servicio',
			'cf_int_legenda'            => 'El vendedor consulta precios y contratos de Protheus y los negocios de HubSpot en la misma pantalla — <strong>sin licencia de Protheus.</strong>',

			// 11. Benefícios.
			'cf_ben_1_titulo'           => 'Usuarios ilimitados',
			'cf_ben_2_titulo'           => 'Procesos ilimitados',
			'cf_ben_3_titulo'           => 'Costo fijo mensual',

			// 12. Como funciona.
			'cf_dash_eyebrow'           => 'Cómo funciona',
			'cf_dash_titulo'            => 'Cualquier pantalla, dashboard o proceso.',
			'cf_dash_titulo_destaque'   => 'Totalmente integrado con sus sistemas.',
			'cf_dash_texto'             => 'La persona completa y aprueba en CLI Flow. CLI Connect lleva los datos a cada sistema, sin que nadie vuelva a digitar y reduciendo el volumen de licencias de los sistemas.',
			'cf_dash_card_titulo'       => 'Solicitud de compra',
			'cf_dash_etapa_1_texto'     => 'pantallas y decisiones',
			'cf_dash_etapa_2_texto'     => 'integración operada por CLI',
			'cf_dash_etapa_3_destaque'  => 'Sus sistemas',

			// 13. Economia.
			'cf_eco_titulo'             => 'Hasta 80% menos licencias',
			'cf_eco_titulo_destaque'    => 'en sus grandes sistemas',
			'cf_eco_texto'              => 'Quien solo solicita, aprueba o consulta no necesita una licencia completa en el sistema. Esas personas usan CLI Flow, con la información de esos sistemas directo en su pantalla.',
			'cf_eco_card_titulo'        => 'Empresa con 100 usuarios',
			'cf_eco_sem_label'          => 'Sin CLI Flow',
			'cf_eco_sem_valor'          => '100 licencias completas',
			'cf_eco_com_label'          => 'Con CLI Flow',
			'cf_eco_com_valor'          => '20 licencias completas',
			'cf_eco_com_barra'          => '80 usuarios en CLI Flow',

			// 15. Preço.
			'cf_preco_titulo'           => 'Todo incluido.',
			'cf_preco_titulo_destaque'  => 'Con costo previsible.',
			'cf_preco_texto'            => 'Integración, plataforma y soporte en un solo contrato, sin cobro por usuario y sin límite de procesos.',
			'cf_preco_1_titulo'         => 'Integración incluida',
			'cf_preco_1_texto'          => 'Cada proceso ya nace conectado a sus sistemas de gestión, CRM y fiscal — más de 30 mil integraciones listas detrás.',
			'cf_preco_2_titulo'         => 'Funciona donde usted lo necesita',
			'cf_preco_2_texto'          => 'Nube, CLI Connect Hosted con IP fija, o dentro de su red para sistemas on-premise.',
			'cf_preco_3_titulo'         => 'Seguro por defecto',
			'cf_preco_3_texto'          => 'Inicio de sesión corporativo, permisos por etapa y registro de quién decidió qué.',

			// 15b. Selos.
			'cf_selos_titulo'           => 'Lideramos el mercado en cumplimiento y seguridad',

			// 15c. FAQ.
			'cf_faq_titulo'             => 'Preguntas Frecuentes',

			// 17. CTA final.
			'cf_cta_eyebrow'            => 'Próximo paso',
			'cf_cta_titulo'             => 'Muéstrenos su proceso.',
			'cf_cta_titulo_destaque'    => 'En 30 minutos queda mapeado.',
			'cf_cta_texto'              => 'Traiga el proceso que más frena a su área, de compras a la contratación. Usted sale de la conversación con el proceso mapeado, los sistemas que toca y los próximos pasos.',
			'cf_cta_botao'              => $demo,
		);
	}
}
