<?php
/**
 * Seed — texto em espanhol das landings de solução: Indústria, departamento e iniciativa.
 *
 * O texto das landings em espanhol é grande demais para um arquivo só, então
 * está dividido por família de solução — o despacho continua sendo por nome de
 * método (`texto_es_solucao_*`), como em `seed-i18n.php`.
 *
 * Cada método traz **apenas os campos de texto**; imagens, ícones, logos e as
 * FAQ vinculadas vêm copiados do original por `traduzir_post()`. Os rótulos
 * repetidos ficam em `base_solucao_es()` (inc/cli/seed-es-solucoes.php).
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
 * Indústria, departamento e iniciativa — texto em espanhol.
 */
trait Cliconnect_Seed_Es_Solucoes_Negocio {

	/**
	 * Serviços Financeiros.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_servicos_financeiros() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'         => 'Para servicios financieros',
				'solucao_hero_titulo'          => 'De la implementación a la producción en semanas.',
				'solucao_hero_titulo_destaque' => 'Porque los bancos no esperan.',
				'solucao_hero_corpo'           => 'Conecte sistemas bancarios, plataformas digitales y soluciones de seguridad en una única arquitectura de integración preparada para evolucionar continuamente.',
				'solucao_hero_btn2_texto'      => 'Conozca la plataforma',
				'solucao_metrica_1_numero'     => '95%',
				'solucao_metrica_1_rotulo'     => 'más rápida la verificación de identidad',
				'solucao_metrica_2_numero'     => '24.000',
				'solucao_metrica_2_rotulo'     => 'horas de trabajo manual eliminadas',
				'solucao_metrica_3_numero'     => '5%',
				'solucao_metrica_3_rotulo'     => 'de aumento en el NPS',
				'solucao_pilares_titulo'       => 'Integraciones más rápidas, seguras e inteligentes',
				'solucao_pilares_1_titulo'     => 'Compliance desde la arquitectura',
				'solucao_pilares_1_desc'       => 'Control de accesos, trazabilidad y gobernanza para entornos altamente regulados.',
				'solucao_pilares_2_titulo'     => 'Integraciones que evolucionan junto con el negocio',
				'solucao_pilares_2_desc'       => 'Nuevos flujos, cambios y mejoras forman parte de la operación, sin iniciar un nuevo proyecto en cada cambio.',
				'solucao_pilares_3_titulo'     => 'Datos preparados para automatización e IA',
				'solucao_pilares_3_desc'       => 'Transforme información dispersa en procesos conectados, listos para alimentar agentes inteligentes y análisis en tiempo real.',
				'solucao_logos_texto'          => 'Integramos los servicios financieros de grandes empresas',
				'solucao_casos_titulo'         => 'Integraciones más rápidas, seguras e inteligentes',
				'solucao_casos_1_titulo'       => 'Core Banking conectado',
				'solucao_casos_1_desc'         => 'Integre sistemas bancarios a ERP, CRM y plataformas digitales.',
				'solucao_casos_2_titulo'       => 'Pagos en tiempo real',
				'solucao_casos_2_desc'         => 'Automatice el intercambio de información entre instituciones financieras y sistemas internos.',
				'solucao_casos_3_titulo'       => 'Prevención del fraude',
				'solucao_casos_3_desc'         => 'Conecte motores antifraude, plataformas analíticas y canales digitales.',
				'solucao_casos_4_titulo'       => 'Crédito automatizado',
				'solucao_casos_4_desc'         => 'Orqueste validaciones, documentos y aprobaciones entre múltiples sistemas.',
				'solucao_casos_5_titulo'       => 'Visión 360º del cliente',
				'solucao_casos_5_desc'         => 'Centralice datos financieros, comerciales y operativos en un único recorrido.',
				'solucao_casos_6_titulo'       => 'Datos para Inteligencia Artificial',
				'solucao_casos_6_desc'         => 'Ponga a disposición información confiable para agentes inteligentes y análisis avanzados.',
			)
		);
	}

	/**
	 * Manufatura.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_manufatura() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'         => 'Para manufactura',
				'solucao_hero_titulo'          => 'Conecte su fábrica desde la planta de producción hasta la nube',
				'solucao_hero_titulo_destaque' => 'sin interrumpir la operación.',
				'solucao_hero_corpo'           => 'Integre SAP S/4HANA, MES, WMS, Salesforce y sistemas industriales para acelerar proyectos, aumentar la visibilidad operativa y modernizar la manufactura con seguridad.',
				'solucao_hero_btn2_texto'      => 'Conozca la plataforma',
				'solucao_metrica_1_numero'     => '4x',
				'solucao_metrica_1_rotulo'     => 'más rápido el registro de proveedores',
				'solucao_metrica_2_numero'     => '50%',
				'solucao_metrica_2_rotulo'     => 'de ganancia de eficiencia',
				'solucao_metrica_3_numero'     => '30s',
				'solucao_metrica_3_rotulo'     => 'para el procesamiento automatizado de pedidos',
				'solucao_pilares_titulo'       => 'Modernice su operación industrial con integraciones preparadas para escalar',
				'solucao_pilares_1_titulo'     => 'Visualice toda la operación en tiempo real',
				'solucao_pilares_1_desc'       => 'Conecte producción, inventario y logística para seguir indicadores actualizados en toda la fábrica.',
				'solucao_pilares_2_titulo'     => 'Conecte fábrica y nube con seguridad',
				'solucao_pilares_2_desc'       => 'Integre entornos industriales a la nube utilizando arquitectura zero-trust sin comprometer la operación.',
				'solucao_pilares_3_titulo'     => 'Alimente iniciativas de IA continuamente',
				'solucao_pilares_3_desc'       => 'Ponga a disposición datos de producción en tiempo real para analytics, IA y automatizaciones inteligentes.',
				'solucao_logos_texto'          => 'Integramos la manufactura de grandes empresas.',
				'solucao_casos_titulo'         => 'Automatice los principales procesos de la manufactura',
				'solucao_casos_1_titulo'       => 'Migre a SAP S/4HANA sin downtime',
				'solucao_casos_1_desc'         => 'Conecte sistemas durante la migración preservando la continuidad de las operaciones industriales.',
				'solucao_casos_2_titulo'       => 'Automatice el ciclo Order-to-Cash',
				'solucao_casos_2_desc'         => 'Integre pedidos, facturación y logística para reducir retrasos y retrabajo operativo.',
				'solucao_casos_3_titulo'       => 'Digitalice el Procure-to-Pay',
				'solucao_casos_3_desc'         => 'Conecte SAP Ariba, ERP y proveedores para acelerar compras y aprobaciones.',
				'solucao_casos_4_titulo'       => 'Alimente la IA con datos de producción',
				'solucao_casos_4_desc'         => 'Envíe datos industriales continuamente a plataformas analíticas y modelos de inteligencia artificial.',
				'solucao_casos_5_titulo'       => 'Conecte OT y cloud con seguridad',
				'solucao_casos_5_desc'         => 'Integre MES, IoT y equipos industriales a las plataformas de datos sin abrir el firewall.',
			)
		);
	}

	/**
	 * Logística (3PL).
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_logistica_3pl() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'         => 'Para logística',
				'solucao_hero_titulo'          => 'Conecte clientes, transportistas y sistemas logísticos',
				'solucao_hero_titulo_destaque' => 'en una única plataforma',
				'solucao_hero_corpo'           => 'Integre ERP, WMS, transportistas y marketplaces para acelerar el onboarding de nuevos clientes, automatizar operaciones y escalar su logística con previsibilidad.',
				'solucao_hero_btn2_texto'      => 'Conozca la plataforma',
				'solucao_metrica_1_numero'     => '80%',
				'solucao_metrica_1_rotulo'     => 'de aumento en la precisión de datos en tiempo real',
				'solucao_metrica_2_numero'     => '50%',
				'solucao_metrica_2_rotulo'     => 'de reducción del tiempo de integración de socios y sistemas',
				'solucao_metrica_3_numero'     => '1',
				'solucao_metrica_3_rotulo'     => 'única plataforma para conectar a todos los clientes',
				'solucao_pilares_titulo'       => 'Escale su operación logística sin aumentar la complejidad',
				'solucao_pilares_1_titulo'     => 'Acelere el onboarding de nuevos clientes',
				'solucao_pilares_1_desc'       => 'Reutilice integraciones entre ERP y WMS para reducir el tiempo de implementación de nuevos contratos.',
				'solucao_pilares_2_titulo'     => 'Sincronice inventarios automáticamente',
				'solucao_pilares_2_desc'       => 'Mantenga las posiciones de inventario actualizadas entre clientes, operadores logísticos y sistemas corporativos.',
				'solucao_pilares_3_titulo'     => 'Automatice documentos con IA',
				'solucao_pilares_3_desc'       => 'Extraiga información de PDF y correos electrónicos para iniciar procesos logísticos automáticamente.',
				'solucao_logos_texto'          => 'Integramos la logística de grandes empresas.',
				'solucao_casos_titulo'         => 'Automatice los principales procesos logísticos',
				'solucao_casos_1_titulo'       => 'Sincronice posiciones de inventario',
				'solucao_casos_1_desc'         => 'Actualice saldos automáticamente entre WMS, ERP y los sistemas de los clientes en tiempo real.',
				'solucao_casos_2_titulo'       => 'Automatice pedidos multicanal',
				'solucao_casos_2_desc'         => 'Reciba pedidos de marketplaces y diríjalos automáticamente a preparación y expedición.',
				'solucao_casos_3_titulo'       => 'Conecte múltiples transportistas',
				'solucao_casos_3_desc'         => 'Centralice integraciones con transportistas sin desarrollar conexiones individuales para cada operación.',
				'solucao_casos_4_titulo'       => 'Automatice devoluciones',
				'solucao_casos_4_desc'         => 'Gestione procesos de RMA entre clientes, transportistas y centros de distribución automáticamente.',
				'solucao_casos_5_titulo'       => 'Prevea picos de demanda con IA',
				'solucao_casos_5_desc'         => 'Utilice datos operativos para anticipar volúmenes y mejorar la planificación logística.',
			)
		);
	}

	/**
	 * Software (ISV).
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_software_isv() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'         => 'Para software',
				'solucao_hero_titulo'          => 'Entregue integraciones nativas a sus clientes',
				'solucao_hero_titulo_destaque' => 'sin reconstruir conectores en cada proyecto',
				'solucao_hero_corpo'           => 'Conecte su producto a ERP, CRM y aplicaciones corporativas utilizando integraciones reutilizables, APIs nativas y una plataforma preparada para escalar su software.',
				'solucao_hero_btn2_texto'      => 'Conozca la plataforma',
				'solucao_metrica_1_numero'     => '4x',
				'solucao_metrica_1_rotulo'     => 'más rápido para la entrega de proyectos de integración y desarrollo',
				'solucao_metrica_2_numero'     => '350%',
				'solucao_metrica_2_rotulo'     => 'de aumento en el ROI en entornos de tecnología',
				'solucao_metrica_3_numero'     => '5 días',
				'solucao_metrica_3_rotulo'     => 'para la primera integración',
				'solucao_pilares_titulo'       => 'Convierta las integraciones en ventaja competitiva',
				'solucao_pilares_1_titulo'     => 'Conecte cualquier ERP o CRM',
				'solucao_pilares_1_desc'       => 'Amplíe la compatibilidad de su producto con integraciones listas para diferentes plataformas corporativas.',
				'solucao_pilares_2_titulo'     => 'Entregue integraciones en minutos',
				'solucao_pilares_2_desc'       => 'Implemente el primer pipeline rápidamente utilizando conectores reutilizables y arquitectura low-code.',
				'solucao_pilares_3_titulo'     => 'Escale sin aumentar costos',
				'solucao_pilares_3_desc'       => 'Crezca según el consumo de la plataforma sin cobrar ni mantener conectores individuales.',
				'solucao_logos_texto'          => 'Integramos softwares de grandes empresas.',
				'solucao_casos_titulo'         => 'Entregue integraciones como parte de su producto',
				'solucao_casos_1_titulo'       => 'Ponga a disposición integraciones nativas',
				'solucao_casos_1_desc'         => 'Utilice componentes reutilizables para conectar su software a los principales sistemas corporativos.',
				'solucao_casos_2_titulo'       => 'Cree agentes de IA con MCP',
				'solucao_casos_2_desc'         => 'Desarrolle agentes inteligentes expuestos como servidores MCP integrados a su producto.',
				'solucao_casos_3_titulo'       => 'Implemente en el entorno del cliente',
				'solucao_casos_3_desc'         => 'Ejecute integraciones en la infraestructura del cliente sin VPN ni puertos abiertos.',
				'solucao_casos_4_titulo'       => 'Monitoree a todos los clientes',
				'solucao_casos_4_desc'         => 'Centralice métricas, ejecuciones e integraciones en un único panel operativo.',
				'solucao_casos_5_titulo'       => 'Conecte cualquier modelo de IA',
				'solucao_casos_5_desc'         => 'Orqueste diferentes proveedores de LLM directamente en los flujos de integración del producto.',
			)
		);
	}
	/**
	 * Varejo.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_varejo() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'         => 'Para el retail',
				'solucao_hero_titulo'          => 'Conecte',
				'solucao_hero_titulo_destaque' => 'todo el recorrido',
				'solucao_hero_titulo_fim'      => 'de compra, del carrito a la entrega.',
				'solucao_hero_corpo'           => 'Integre e-commerce, ERP, logística, CRM y marketplaces para ofrecer experiencias consistentes, acelerar entregas y evolucionar su operación sin interrupciones.',
				'solucao_hero_btn2_texto'      => 'Conozca la plataforma',
				'solucao_metrica_1_numero'     => '70%',
				'solucao_metrica_1_rotulo'     => 'Reducción en el tiempo de entrega.',
				'solucao_metrica_2_numero'     => '40%',
				'solucao_metrica_2_rotulo'     => 'más rápido el registro de proveedores',
				'solucao_metrica_3_numero'     => '1600%',
				'solucao_metrica_3_rotulo'     => 'de ROI en 10 meses',
				'solucao_pilares_titulo'       => 'Convierta los datos conectados en mejores experiencias de compra',
				'solucao_pilares_1_titulo'     => 'Unifique la visión del cliente',
				'solucao_pilares_1_desc'       => 'Centralice información de ventas, atención y logística para personalizar cada interacción con los consumidores.',
				'solucao_pilares_2_titulo'     => 'Migre plataformas sin interrumpir las ventas',
				'solucao_pilares_2_desc'       => 'Cambie de plataforma de e-commerce manteniendo operaciones, pedidos e integraciones funcionando normalmente.',
				'solucao_pilares_3_titulo'     => 'Automatice entregas con inteligencia artificial',
				'solucao_pilares_3_desc'       => 'Optimice rutas, decisiones logísticas y procesos de entrega utilizando datos en tiempo real.',
				'solucao_logos_texto'          => 'Integramos el retail de grandes empresas.',
				'solucao_casos_titulo'         => 'Automatice toda la operación del retail',
				'solucao_casos_1_titulo'       => 'Conecte experiencias de compra',
				'solucao_casos_1_desc'         => 'Integre canales físicos y digitales para ofrecer recorridos consistentes en todos los puntos de contacto.',
				'solucao_casos_2_titulo'       => 'Optimice la última milla',
				'solucao_casos_2_desc'         => 'Automatice entregas utilizando datos operativos para reducir costos y mejorar los plazos.',
				'solucao_casos_3_titulo'       => 'Integre canales de social commerce',
				'solucao_casos_3_desc'         => 'Conecte los pedidos originados en las redes sociales a los sistemas comerciales y logísticos.',
				'solucao_casos_4_titulo'       => 'Migre su ERP a la nube',
				'solucao_casos_4_desc'         => 'Modernice su arquitectura preservando las integraciones y la continuidad de las operaciones comerciales.',
				'solucao_casos_5_titulo'       => 'Personalice recomendaciones con IA',
				'solucao_casos_5_desc'         => 'Utilice datos integrados para recomendar productos según el comportamiento y el historial de compras.',
				'solucao_casos_6_titulo'       => 'Automatice la logística inversa',
				'solucao_casos_6_desc'         => 'Gestione devoluciones, reembolsos y viabilidad de reventa con flujos inteligentes automatizados.',
			)
		);
	}

	/**
	 * Hotelaria e Turismo.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_hotelaria_e_turismo() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'         => 'Para hotelería',
				'solucao_hero_titulo'          => 'Conecte datos, propiedades y huéspedes',
				'solucao_hero_titulo_destaque' => 'en una experiencia integrada',
				'solucao_hero_corpo'           => 'Integre PMS, CRM, motores de reservas y sistemas operativos para eliminar el overbooking, personalizar la atención y acelerar la expansión de la red hotelera.',
				'solucao_hero_btn2_texto'      => 'Conozca la plataforma',
				'solucao_metrica_1_numero'     => '17.000+',
				'solucao_metrica_1_rotulo'     => 'huéspedes y residentes gestionados por flujos sincronizados',
				'solucao_metrica_2_numero'     => '100%',
				'solucao_metrica_2_rotulo'     => 'de automatización lograda en el trabajo manual para cambios de reservas',
				'solucao_metrica_3_numero'     => '10x',
				'solucao_metrica_3_rotulo'     => 'más rápido el lanzamiento de nuevos servicios',
				'solucao_pilares_titulo'       => 'Conecte toda la operación hotelera en una única plataforma',
				'solucao_pilares_1_titulo'     => 'Sincronice inventarios en tiempo real',
				'solucao_pilares_1_desc'       => 'Mantenga la disponibilidad de habitaciones actualizada entre canales para evitar overbooking y retrabajo.',
				'solucao_pilares_2_titulo'     => 'Personalice la experiencia del huésped',
				'solucao_pilares_2_desc'       => 'Unifique los perfiles de huéspedes para ofrecer una atención personalizada utilizando inteligencia artificial.',
				'solucao_pilares_3_titulo'     => 'Expanda nuevas unidades rápidamente',
				'solucao_pilares_3_desc'       => 'Estandarice integraciones reutilizando componentes en nuevas propiedades y franquicias de la red.',
				'solucao_logos_texto'          => 'Integramos la hotelería de grandes empresas.',
				'solucao_casos_titulo'         => 'Automatice los principales procesos de la hotelería',
				'solucao_casos_1_titulo'       => 'Conecte PMS y CRM',
				'solucao_casos_1_desc'         => 'Sincronice reservas, preferencias e historial de los huéspedes entre sistemas automáticamente.',
				'solucao_casos_2_titulo'       => 'Automatice programas de fidelidad',
				'solucao_casos_2_desc'         => 'Integre POS, reservas y loyalty para ofrecer beneficios en todos los canales.',
				'solucao_casos_3_titulo'       => 'Unifique los informes de las propiedades',
				'solucao_casos_3_desc'         => 'Centralice indicadores operativos y financieros de todas las unidades en un solo panel.',
				'solucao_casos_4_titulo'       => 'Actualice precios dinámicamente',
				'solucao_casos_4_desc'         => 'Utilice datos de ocupación para automatizar estrategias de tarificación en tiempo real.',
				'solucao_casos_5_titulo'       => 'Automatice la gobernanza de las habitaciones',
				'solucao_casos_5_desc'         => 'Integre housekeeping, reservas y operación para agilizar liberaciones y limpieza de las habitaciones.',
			)
		);
	}

	/**
	 * Seguros.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_seguros() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'         => 'Para seguros',
				'solucao_hero_titulo'          => 'Conecte sistemas heredados y',
				'solucao_hero_titulo_destaque' => 'acelere el lanzamiento',
				'solucao_hero_titulo_fim'      => 'de nuevos productos de seguros',
				'solucao_hero_corpo'           => 'Integre Guidewire, Duck Creek, Salesforce y otras aplicaciones sin sustituir su core, modernizando las operaciones con seguridad y velocidad.',
				'solucao_hero_btn2_texto'      => 'Conozca la plataforma',
				'solucao_metrica_1_numero'     => '10 min',
				'solucao_metrica_1_rotulo'     => 'de tiempo total en la suscripción de riesgos',
				'solucao_metrica_2_numero'     => '6',
				'solucao_metrica_2_rotulo'     => 'para el retorno financiero de sistemas heredados de seguros',
				'solucao_metrica_3_numero'     => '100%',
				'solucao_metrica_3_rotulo'     => 'de conformidad regulatoria lograda en el intercambio de datos confidenciales',
				'solucao_pilares_titulo'       => 'Su operación aseguradora lista para el futuro',
				'solucao_pilares_1_titulo'     => 'Sincronice datos en tiempo real',
				'solucao_pilares_1_desc'       => 'Conecte pólizas, siniestros y canales de distribución con información siempre actualizada.',
				'solucao_pilares_2_titulo'     => 'Automatice decisiones con IA',
				'solucao_pilares_2_desc'       => 'Utilice inteligencia artificial para agilizar el underwriting y la clasificación inicial de siniestros.',
				'solucao_pilares_3_titulo'     => 'Conecte corredores en tiempo real',
				'solucao_pilares_3_desc'       => 'Ponga a disposición de los socios comerciales información actualizada mediante portales integrados.',
				'solucao_logos_texto'          => 'Integramos los sistemas de seguros de grandes empresas.',
				'solucao_casos_titulo'         => 'Automatice los principales procesos del mercado asegurador',
				'solucao_casos_1_titulo'       => 'Conecte sistemas core al CRM',
				'solucao_casos_1_desc'         => 'Integre Guidewire, Duck Creek y otras plataformas a los sistemas comerciales de la aseguradora.',
				'solucao_casos_2_titulo'       => 'Automatice la gestión de siniestros',
				'solucao_casos_2_desc'         => 'Integre apertura, análisis, prevención del fraude y pago en un único flujo.',
				'solucao_casos_3_titulo'       => 'Sincronice portales de corredores',
				'solucao_casos_3_desc'         => 'Mantenga a agentes y socios actualizados con información consistente sobre clientes y pólizas.',
				'solucao_casos_4_titulo'       => 'Atienda los requisitos del Open Insurance',
				'solucao_casos_4_desc'         => 'Integre sistemas siguiendo los estándares regulatorios y los requisitos definidos por la SUSEP.',
				'solucao_casos_5_titulo'       => 'Acelere el underwriting con IA',
				'solucao_casos_5_desc'         => 'Utilice modelos inteligentes para apoyar el análisis de riesgo y la emisión de nuevas pólizas.',
			)
		);
	}

	/**
	 * Recursos Humanos (RH).
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_recursos_humanos_rh() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'         => 'Para su RR. HH.',
				'solucao_hero_titulo'          => 'Conecte todo el ciclo de vida del colaborador en',
				'solucao_hero_titulo_destaque' => 'una única operación',
				'solucao_hero_corpo'           => 'Integre HRIS, nómina, ATS y sistemas corporativos para automatizar el recorrido del colaborador y mantener la información siempre sincronizada.',
				'solucao_hero_btn2_texto'      => 'Conozca la plataforma',
				'solucao_metrica_1_numero'     => '70%',
				'solucao_metrica_1_rotulo'     => 'de reducción en el tiempo de procesamiento de la nómina',
				'solucao_metrica_2_numero'     => '90%',
				'solucao_metrica_2_rotulo'     => 'de ahorro proyectado en costos continuos de mantenimiento',
				'solucao_metrica_3_numero'     => '40%',
				'solucao_metrica_3_rotulo'     => 'de disminución en el tiempo dedicado a la carga manual de datos',
				'solucao_pilares_titulo'       => 'Automatice toda la operación de RR. HH.',
				'solucao_pilares_1_titulo'     => 'Automatice el recorrido del colaborador',
				'solucao_pilares_1_desc'       => 'Sincronice admisiones, movimientos y desvinculaciones entre todos los sistemas para eliminar tareas manuales y garantizar datos consistentes.',
				'solucao_pilares_2_titulo'     => 'Mantenga la nómina sincronizada',
				'solucao_pilares_2_desc'       => 'Actualice automáticamente los datos entre el HRIS y la nómina para reducir inconsistencias y simplificar el cierre mensual.',
				'solucao_pilares_3_titulo'     => 'Proteja los datos sensibles',
				'solucao_pilares_3_desc'       => 'Aplique enmascaramiento de la información personal durante las integraciones para atender los requisitos de la LGPD y fortalecer la gobernanza.',
				'solucao_casos_titulo'         => 'Automatice procesos críticos de RR. HH.',
				'solucao_casos_1_titulo'       => 'Orqueste el ciclo del empleado',
				'solucao_casos_1_desc'         => 'Actualice HRIS, identidad, nómina y plataformas de capacitación simultáneamente cada vez que un colaborador entra o sale de la empresa.',
				'solucao_casos_2_titulo'       => 'Sincronice HRIS y nómina',
				'solucao_casos_2_desc'         => 'Garantice que los cambios de registro y los movimientos se reflejen automáticamente en la nómina.',
				'solucao_casos_3_titulo'       => 'Automatice nuevas contrataciones',
				'solucao_casos_3_desc'         => 'Envíe automáticamente los candidatos aprobados del ATS al HRIS, eliminando registros duplicados y actividades manuales.',
				'solucao_casos_4_titulo'       => 'Revoque accesos automáticamente',
				'solucao_casos_4_desc'         => 'Elimine permisos y cuentas pocos minutos después de la desvinculación para aumentar la seguridad y reducir riesgos operativos.',
				'solucao_casos_5_titulo'       => 'Anticipe riesgos de desvinculación',
				'solucao_casos_5_desc'         => 'Utilice agentes de IA para identificar señales de retención y apoyar decisiones antes de perder talentos.',
				'solucao_casos_6_titulo'       => 'Automatice movimientos internos',
				'solucao_casos_6_desc'         => 'Actualice cargos, equipos y permisos cada vez que haya cambios.',
				'solucao_dif_titulo'           => 'Privacidad integrada a las automatizaciones',
				'solucao_dif_corpo'            => 'Proteja la información sensible durante todo el movimiento entre sistemas con detección y enmascaramiento automático de datos personales antes de la integración.',
				'solucao_dif_topico_1'         => 'Detecte datos sensibles automáticamente',
				'solucao_dif_topico_2'         => 'Enmascare la información antes de la integración',
				'solucao_dif_topico_3'         => 'Atienda los requisitos de la LGPD con gobernanza',
			)
		);
	}
	/**
	 * Operações de Receita (RevOps).
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_operacoes_de_receita_revops() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'Para sus operaciones de ingresos',
				'solucao_hero_titulo'      => 'Conecte toda la operación de ingresos.',
				'solucao_hero_corpo'       => 'Sincronice CRM, marketing y customer success en tiempo real para eliminar cuellos de botella, acelerar los handoffs y mantener todo el embudo siempre actualizado.',
				'solucao_hero_btn2_texto'  => 'Conozca la plataforma',
				'solucao_pilares_titulo'   => 'Una operación de ingresos conectada',
				'solucao_pilares_1_titulo' => 'Unifique los datos de ingresos',
				'solucao_pilares_1_desc'   => 'Conecte marketing, ventas y customer success para priorizar oportunidades con información consistente en todo el ciclo comercial.',
				'solucao_pilares_2_titulo' => 'Automatice los handoffs',
				'solucao_pilares_2_desc'   => 'Transfiera clientes entre ventas y customer success automáticamente, reduciendo retrasos y eliminando procesos manuales.',
				'solucao_pilares_3_titulo' => 'Mantenga el pipeline limpio',
				'solucao_pilares_3_desc'   => 'Actualice los registros continuamente para evitar duplicidades, inconsistencias y decisiones basadas en información desactualizada.',
				'solucao_logos_texto'      => 'Integramos las principales plataformas de CRM, marketing, ventas y customer success utilizadas por grandes empresas.',
				'solucao_casos_titulo'     => 'Automatice todo el flujo de ingresos',
				'solucao_casos_1_titulo'   => 'Priorice leads automáticamente',
				'solucao_casos_1_desc'     => 'Combine datos de CRM, automatización de marketing y enriquecimiento para calificar oportunidades con más precisión.',
				'solucao_casos_2_titulo'   => 'Unifique múltiples CRM',
				'solucao_casos_2_desc'     => 'Consolide la información comercial de diferentes CRM para obtener una visión única del pipeline.',
				'solucao_casos_3_titulo'   => 'Active la posventa',
				'solucao_casos_3_desc'     => 'Dispare automáticamente procesos de customer success cuando se gane una oportunidad y preserve todo el contexto de la venta.',
				'solucao_casos_4_titulo'   => 'Corrija datos comerciales',
				'solucao_casos_4_desc'     => 'Identifique y actualice registros inconsistentes para mantener oportunidades, contactos y previsiones comerciales confiables.',
				'solucao_casos_5_titulo'   => 'Monitoree la salud de los clientes',
				'solucao_casos_5_desc'     => 'Combine datos de producto, soporte y NPS para identificar riesgos y oportunidades de expansión.',
				'solucao_casos_6_titulo'   => 'Automatice movimientos internos',
				'solucao_casos_6_desc'     => 'Actualice cargos, equipos y permisos cada vez que haya cambios.',
				'solucao_dif_titulo'       => 'Más autonomía para RevOps',
				'solucao_dif_corpo'        => 'Permita que el equipo de RevOps cree, ajuste y monitoree integraciones utilizando un builder visual con IA, sin depender de desarrollo dedicado.',
				'solucao_dif_topico_1'     => 'Cree integraciones con builder visual',
				'solucao_dif_topico_2'     => 'Automatice flujos con apoyo de IA',
				'solucao_dif_topico_3'     => 'Reduzca la dependencia del equipo de TI',
			)
		);
	}

	/**
	 * Marketing.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_marketing() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'Para su marketing',
				'solucao_hero_titulo'      => 'Conecte marketing, CRM y analytics en tiempo real',
				'solucao_hero_corpo'       => 'Elimine las filas de TI, sincronice la información en tiempo real y entregue campañas más relevantes con automatización inteligente entre todas las plataformas de su ecosistema.',
				'solucao_hero_btn2_texto'  => 'Conozca la plataforma',
				'solucao_metrica_1_numero' => '127%',
				'solucao_metrica_1_rotulo' => 'de crecimiento en reconocimiento de marca',
				'solucao_metrica_2_numero' => '50%',
				'solucao_metrica_2_rotulo' => 'de aumento en la generación de pipeline',
				'solucao_metrica_3_numero' => '22%',
				'solucao_metrica_3_rotulo' => 'de crecimiento promedio mensual en el embudo de ventas',
				'solucao_pilares_titulo'   => 'Marketing conectado de principio a fin',
				'solucao_pilares_1_titulo' => 'Leads siempre sincronizados',
				'solucao_pilares_1_desc'   => 'Mantenga el CRM y la plataforma de automatización alineados en tiempo real para evitar contactos desactualizados y aumentar la eficiencia de las campañas.',
				'solucao_pilares_2_titulo' => 'Escalabilidad bajo demanda',
				'solucao_pilares_2_desc'   => 'Absorba grandes volúmenes de leads en lanzamientos y campañas estacionales sin comprometer el rendimiento ni exigir intervención manual.',
				'solucao_pilares_3_titulo' => 'Personalización con gobernanza',
				'solucao_pilares_3_desc'   => 'Utilice IA para enriquecer audiencias y segmentaciones manteniendo la conformidad con LGPD, GDPR y las políticas corporativas de datos.',
				'solucao_logos_texto'      => 'Grandes empresas integran su Marketing con CLI Connect',
				'solucao_casos_titulo'     => 'Automatice todo el ciclo de las campañas',
				'solucao_casos_1_titulo'   => 'Sincronice leads en tiempo real',
				'solucao_casos_1_desc'     => 'Envíe nuevos leads entre las plataformas de automatización y el CRM en segundos, manteniendo a los equipos de marketing y ventas siempre alineados.',
				'solucao_casos_2_titulo'   => 'Centralice la atribución de campañas',
				'solucao_casos_2_desc'     => 'Conecte Google Ads, LinkedIn y plataformas de automatización para consolidar resultados y atribuciones en un único flujo.',
				'solucao_casos_3_titulo'   => 'Enriquezca leads con IA',
				'solucao_casos_3_desc'     => 'Dispare agentes de IA tras el envío de formularios para buscar información y calificar contactos automáticamente.',
				'solucao_casos_4_titulo'   => 'Orqueste audiencias inteligentes',
				'solucao_casos_4_desc'     => 'Actualice segmentos automáticamente utilizando IA y datos de múltiples sistemas para campañas más relevantes.',
				'solucao_casos_5_titulo'   => 'Cierre el ciclo de atribución',
				'solucao_casos_5_desc'     => 'Conecte marketing, CRM y ERP para medir la contribución de las campañas hasta la generación efectiva de ingresos.',
				'solucao_casos_6_titulo'   => 'Automatice movimientos internos',
				'solucao_casos_6_desc'     => 'Actualice cargos, equipos y permisos cada vez que haya cambios.',
				'solucao_dif_titulo'       => 'Datos listos para actuar',
				'solucao_dif_corpo'        => 'Sustituya las sincronizaciones por lotes por integraciones en tiempo real para acelerar campañas, reducir inconsistencias y mantener a marketing y ventas trabajando con los mismos datos.',
				'solucao_dif_topico_1'     => 'Sincronice leads en menos de 60 segundos',
				'solucao_dif_topico_2'     => 'Elimine los retrasos entre marketing y CRM',
				'solucao_dif_topico_3'     => 'Monitoree integraciones en tiempo real',
			)
		);
	}

	/**
	 * Financeiro.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_financeiro() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para sus finanzas',
				'solucao_hero_titulo'      => 'Conecte todo el ecosistema financiero.',
				'solucao_hero_corpo'       => 'Integre ERP, bancos y plataformas de planificación para acelerar cierres, automatizar auditorías y mantener todas las unidades de negocio sincronizadas.',
				'solucao_hero_btn2_texto'  => 'Conozca la plataforma',
				'solucao_metrica_1_numero' => '7 días',
				'solucao_metrica_1_rotulo' => 'de tiempo del cierre contable mensual',
				'solucao_metrica_2_numero' => '5x',
				'solucao_metrica_2_rotulo' => 'de aumento en el procesamiento de pedidos',
				'solucao_metrica_3_numero' => '50%',
				'solucao_metrica_3_rotulo' => 'reducción del tiempo de cierre mensual',
				'solucao_pilares_titulo'   => 'Más control para la operación financiera',
				'solucao_pilares_1_titulo' => 'Acelere el cierre contable',
				'solucao_pilares_1_desc'   => 'Sincronice información entre los ERP y los sistemas financieros para reducir actividades manuales y concluir el cierre con más rapidez.',
				'solucao_pilares_2_titulo' => 'Automatice la auditoría',
				'solucao_pilares_2_desc'   => 'Registre todos los movimientos con trazabilidad completa para simplificar auditorías y aumentar la confiabilidad de los procesos.',
				'solucao_pilares_3_titulo' => 'Unifique sus ERP',
				'solucao_pilares_3_desc'   => 'Mantenga datos financieros consistentes entre diferentes unidades de negocio, filiales y sistemas corporativos.',
				'solucao_logos_texto'      => 'Integramos los principales ERP, bancos y plataformas financieras utilizados por grandes empresas.',
				'solucao_casos_titulo'     => 'Automatice los principales procesos financieros',
				'solucao_casos_1_titulo'   => 'Consolide datos contables',
				'solucao_casos_1_desc'     => 'Sincronice información entre diferentes ERP para consolidar balances y obtener una visión financiera unificada.',
				'solucao_casos_2_titulo'   => 'Automatice conciliaciones bancarias',
				'solucao_casos_2_desc'     => 'Integre bancos vía host-to-host para realizar conciliaciones diarias con más agilidad y menos intervención manual.',
				'solucao_casos_3_titulo'   => 'Optimice las cuentas por pagar',
				'solucao_casos_3_desc'     => 'Conecte plataformas de compras y ERP para automatizar el matching de tres vías y reducir el retrabajo operativo.',
				'solucao_casos_4_titulo'   => 'Reconozca ingresos automáticamente',
				'solucao_casos_4_desc'     => 'Envíe las ventas aprobadas al ERP en tiempo real y acelere los procesos de contabilización de los ingresos.',
				'solucao_casos_5_titulo'   => 'Alimente la planificación financiera',
				'solucao_casos_5_desc'     => 'Actualice las plataformas de FP&A automáticamente con datos del ERP para mejorar previsiones y análisis financieros.',
				'solucao_casos_6_titulo'   => 'Automatice movimientos internos',
				'solucao_casos_6_desc'     => 'Actualice cargos, equipos y permisos cada vez que haya cambios.',
				'solucao_dif_titulo'       => 'Integraciones bajo su control',
				'solucao_dif_corpo'        => 'Ejecute integraciones dentro de la infraestructura de su empresa para garantizar la soberanía de los datos, mayor control operativo y conformidad con las políticas corporativas.',
				'solucao_dif_topico_1'     => 'Ejecute integraciones en su propia nube',
				'solucao_dif_topico_2'     => 'Mantenga los datos bajo gobernanza corporativa',
				'solucao_dif_topico_3'     => 'Reduzca riesgos de conformidad financiera',
			)
		);
	}

	/**
	 * Atualização de Sistemas Legados.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_atualizacao_de_sistemas_legados() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'         => 'sustitución del sistema heredado',
				'solucao_hero_titulo'          => 'Reconstruya su arquitectura.',
				'solucao_hero_titulo_destaque' => 'Preserve su operación.',
				'solucao_hero_corpo'           => 'Sustituya plataformas heredadas como TIBCO e IBM MQ por una arquitectura moderna de integración, preservando sus sistemas existentes y manteniendo la operación en funcionamiento durante toda la transición.',
				'solucao_pilares_titulo'       => 'Reconstruya su capa de integración sin reconstruir sus sistemas',
				'solucao_pilares_1_titulo'     => 'Democratice la integración',
				'solucao_pilares_1_desc'       => 'Reduzca la dependencia de especialistas en tecnologías heredadas con una plataforma visual, más simple de evolucionar y mantener.',
				'solucao_pilares_2_titulo'     => 'Construya sobre estándares abiertos',
				'solucao_pilares_2_desc'       => 'Desarrolle integraciones utilizando estándares modernos y portables, evitando crear una nueva dependencia tecnológica.',
				'solucao_pilares_3_titulo'     => 'Evolucione hacia eventos en tiempo real',
				'solucao_pilares_3_desc'       => 'Sustituya los procesos por lotes por una arquitectura orientada a eventos, preparada para aplicaciones modernas e integraciones distribuidas.',
				'solucao_casos_titulo'         => 'Integraciones más rápidas, seguras e inteligentes',
				'solucao_casos_1_titulo'       => 'Reconstruya las rutas de TIBCO BusinessWorks',
				'solucao_casos_1_desc'         => 'Transforme las integraciones existentes en flujos visuales más simples de mantener y evolucionar.',
				'solucao_casos_2_titulo'       => 'Conecte mainframes sin VPN',
				'solucao_casos_2_desc'         => 'Integre entornos z/OS y AS/400 utilizando Runtime, sin alterar la infraestructura de red.',
				'solucao_casos_3_titulo'       => 'Sustituya IBM MQ por eventos',
				'solucao_casos_3_desc'         => 'Convierta las integraciones basadas en colas a una arquitectura orientada a eventos con Kafka.',
				'solucao_casos_4_titulo'       => 'Exponga los ERP heredados por APIs',
				'solucao_casos_4_desc'         => 'Ponga SAP ECC y Oracle EBS a disposición a través de APIs modernas sin alterar el core de las aplicaciones.',
				'solucao_casos_5_titulo'       => 'Conecte aplicaciones SaaS el primer día',
				'solucao_casos_5_desc'         => 'Integre Salesforce, ServiceNow, Workday y otras plataformas a la nueva arquitectura sin depender del antiguo ESB.',
				'solucao_dif_titulo'           => 'Su próximo proyecto no debería empezar por el legado',
				'solucao_dif_corpo'            => 'Reconstruya la capa de integración en una arquitectura moderna sin exigir que los sistemas existentes sean sustituidos.',
				'solucao_dif_topico_1'         => 'Evolucione la arquitectura sin exponer sistemas críticos',
				'solucao_dif_topico_2'         => 'Modernice mainframes sin comprometer la seguridad',
				'solucao_dif_topico_3'         => 'Conecte aplicaciones heredadas con aislamiento del core',
			)
		);
	}
	/**
	 * Pedido ao Recebimento.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_pedido_ao_recebimento() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'         => 'del pedido al cobro (o2c)',
				'solucao_hero_titulo'          => 'Conecte ventas, facturación y cobro en',
				'solucao_hero_titulo_destaque' => 'un único flujo',
				'solucao_hero_corpo'           => 'Acelere el ciclo completo de ingresos conectando CRM, ERP, bancos y sistemas de pago en una operación integrada, rastreable y sin etapas manuales.',
				'solucao_metrica_1_numero'     => '7 días',
				'solucao_metrica_1_rotulo'     => 'más rápido en su cierre financiero',
				'solucao_metrica_2_numero'     => '95%',
				'solucao_metrica_2_rotulo'     => 'más rapidez en la creación de pedidos',
				'solucao_metrica_3_numero'     => '6.000',
				'solucao_metrica_3_rotulo'     => 'horas de trabajo manual ahorradas anualmente',
				'solucao_pilares_titulo'       => 'Reconstruya su arquitectura, no su negocio.',
				'solucao_pilares_1_titulo'     => 'Elimine el retrabajo operativo',
				'solucao_pilares_1_desc'       => 'Automatice el intercambio de datos entre pedido, facturación y cobranza, eliminando los registros manuales.',
				'solucao_pilares_2_titulo'     => 'Reciba más rápido',
				'solucao_pilares_2_desc'       => 'Reduzca el tiempo entre el cierre de la venta, la emisión del cobro y el reconocimiento de la caja.',
				'solucao_pilares_3_titulo'     => 'Tenga visibilidad completa',
				'solucao_pilares_3_desc'       => 'Siga cada etapa del pedido al cobro con datos consistentes entre todas las áreas.',
				'solucao_casos_titulo'         => 'Integraciones más rápidas, seguras e inteligentes',
				'solucao_casos_1_titulo'       => 'Facture automáticamente',
				'solucao_casos_1_desc'         => 'Convierta los pedidos cerrados en el CRM en facturación y emisión de la nota fiscal en el ERP.',
				'solucao_casos_2_titulo'       => 'Concilie los cobros',
				'solucao_casos_2_desc'         => 'Compare automáticamente los pagos recibidos con bancos y adquirentes.',
				'solucao_casos_3_titulo'       => 'Avise sobre la morosidad',
				'solucao_casos_3_desc'         => 'Dispare alertas automáticas al equipo comercial siempre que haya retrasos de pago.',
				'solucao_casos_4_titulo'       => 'Monitoree el DSO',
				'solucao_casos_4_desc'         => 'Consolide los indicadores de plazo promedio de cobro en un único panel de gestión.',
				'solucao_casos_5_titulo'       => 'Sincronice la operación',
				'solucao_casos_5_desc'         => 'Comparta el estado de los pedidos entre ventas, finanzas y logística en tiempo real.',
				'solucao_casos_6_titulo'       => 'Actualice datos continuamente',
				'solucao_casos_6_desc'         => 'Propague los cambios entre CRM, ERP y sistemas financieros sin intervenciones manuales.',
				'solucao_dif_titulo'           => 'Garantice trazabilidad completa en todo el ciclo financiero',
				'solucao_dif_corpo'            => 'Proteja la información financiera y siga cada etapa del pedido al cobro con total transparencia y gobernanza.',
				'solucao_dif_topico_1'         => 'Auditoría completa de los procesos',
				'solucao_dif_topico_2'         => 'Datos protegidos de punta a punta',
				'solucao_dif_topico_3'         => 'Historial detallado de las transacciones',
				'solucao_acel_titulo'          => 'Modelo listo para comenzar',
				'solucao_acel_corpo'           => 'Comience rápidamente con un flujo preconfigurado que conecta pedido, facturación, cobranza y conciliación financiera.',
				'solucao_acel_topico_1'        => 'Genere facturaciones automáticamente',
				'solucao_acel_topico_2'        => 'Emita cobros integrados',
				'solucao_acel_topico_3'        => 'Concilie los cobros con los bancos',
			)
		);
	}

	/**
	 * IA Corporativa.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_ia_corporativa() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'IA corporativa',
				'solucao_hero_titulo'      => 'Sus datos corporativos listos para agentes de IA',
				'solucao_hero_corpo'       => 'Conecte Salesforce, SAP, TOTVS, Senior, ServiceNow y otros sistemas empresariales a cualquier LLM para crear agentes inteligentes que entienden datos y ejecutan acciones.',
				'solucao_pilares_titulo'   => 'Convierta los datos en inteligencia operativa',
				'solucao_pilares_1_titulo' => 'Trabaje con datos en vivo',
				'solucao_pilares_1_desc'   => 'Permita que los agentes de IA consulten información actual de sus sistemas, sustituyendo decisiones basadas en datos desactualizados.',
				'solucao_pilares_2_titulo' => 'Automatice workflows complejos',
				'solucao_pilares_2_desc'   => 'Cree agentes capaces de ejecutar múltiples etapas de procesos, reduciendo tareas manuales y acelerando operaciones.',
				'solucao_pilares_3_titulo' => 'Aplique seguridad desde el inicio',
				'solucao_pilares_3_desc'   => 'Use controles de PII y guardrails para garantizar que los agentes actúen dentro de las reglas de la empresa.',
				'solucao_diagrama_titulo'  => 'Una nueva forma de conectar la IA a sus sistemas',
				'solucao_casos_titulo'     => 'Aplique IA a los procesos del negocio',
				'solucao_casos_1_titulo'   => 'Cree agentes en tiempo real',
				'solucao_casos_1_desc'     => 'Genere resúmenes inteligentes para los vendedores usando información actualizada de clientes, operaciones y sistemas corporativos.',
				'solucao_casos_2_titulo'   => 'Conecte la IA al conocimiento interno',
				'solucao_casos_2_desc'     => 'Use RAG con Confluence y SharePoint para crear respuestas basadas en el conocimiento de su empresa.',
				'solucao_casos_3_titulo'   => 'Exponga herramientas vía MCP',
				'solucao_casos_3_desc'     => 'Transforme recursos de Salesforce y SAP en herramientas disponibles para agentes de IA autenticados.',
				'solucao_casos_4_titulo'   => 'Automatice operaciones con IA',
				'solucao_casos_4_desc'     => 'Automatice tareas como la apertura de incidentes en ServiceNow sin depender de procesos manuales.',
				'solucao_casos_5_titulo'   => 'Dispare la IA por eventos',
				'solucao_casos_5_desc'     => 'Ejecute modelos de lenguaje automáticamente cuando ocurran eventos, sin depender de consultas constantes.',
				'solucao_dif_titulo'       => 'IA conectada con seguridad corporativa',
				'solucao_dif_corpo'        => 'Acceda a entornos críticos sin depender de VPN complejas',
				'solucao_dif_topico_1'     => 'Runtime para conexión directa con mainframes',
				'solucao_dif_topico_2'     => 'Menos aprobaciones de infraestructura',
				'solucao_dif_topico_3'     => 'Migración más rápida de sistemas heredados',
			)
		);
	}

	/**
	 * Compras ao Pagamento.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_compras_ao_pagamento() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'de las compras al pago (s2p)',
				'solucao_hero_titulo'      => 'Del proveedor al pago, sin hojas de cálculo en el medio',
				'solucao_hero_corpo'       => 'Conecte compras, ERP, contratos y bancos en un flujo único para controlar cada etapa del ciclo de suministros con trazabilidad.',
				'solucao_metrica_1_numero' => '26.000',
				'solucao_metrica_1_rotulo' => 'horas de compras eliminadas',
				'solucao_metrica_2_numero' => '80x',
				'solucao_metrica_2_rotulo' => 'más rápido en el procesamiento de facturas',
				'solucao_metrica_3_numero' => '70%',
				'solucao_metrica_3_rotulo' => 'más rápido en el registro de proveedores',
				'solucao_pilares_titulo'   => 'Control total del ciclo de compras',
				'solucao_pilares_1_titulo' => 'Conecte todo el flujo de compras',
				'solucao_pilares_1_desc'   => 'Integre cotización, aprobación, pedido y pago en un único proceso rastreable y conectado.',
				'solucao_pilares_2_titulo' => 'Elimine las aprobaciones manuales',
				'solucao_pilares_2_desc'   => 'Reduzca el tiempo del ciclo de compras eliminando dependencias de correos y procesos manuales.',
				'solucao_pilares_3_titulo' => 'Tenga visión de los gastos',
				'solucao_pilares_3_desc'   => 'Siga los gastos en tiempo real para tomar decisiones financieras con más precisión.',
				'solucao_casos_titulo'     => 'Automatice cada etapa del suministro',
				'solucao_casos_1_titulo'   => 'Genere pedidos automáticamente',
				'solucao_casos_1_desc'     => 'Transforme las requisiciones aprobadas en pedidos de compra en el ERP sin intervenciones manuales.',
				'solucao_casos_2_titulo'   => 'Automatice el matching de 3 vías',
				'solucao_casos_2_desc'     => 'Valide pedido, recepción y nota fiscal automáticamente antes de liberar los pagos.',
				'solucao_casos_3_titulo'   => 'Dispare pagos automáticamente',
				'solucao_casos_3_desc'     => 'Ejecute los pagos a proveedores tras la aprobación y la verificación de los documentos necesarios.',
				'solucao_casos_4_titulo'   => 'Consolide gastos estratégicos',
				'solucao_casos_4_desc'     => 'Unifique los gastos por categoría y proveedor para mejorar negociaciones y decisiones de compra.',
				'solucao_casos_5_titulo'   => 'Rastree todo el ciclo de compras',
				'solucao_casos_5_desc'     => 'Siga cada etapa desde la requisición hasta el pago con historial completo y visión operativa.',
				'solucao_dif_titulo'       => 'Gobernanza en cada transacción',
				'solucao_dif_corpo'        => 'Garantice el control sobre aprobaciones y pagos con trazabilidad completa y separación entre funciones críticas.',
				'solucao_dif_topico_1'     => 'Historial completo de aprobaciones',
				'solucao_dif_topico_2'     => 'Segregación entre aprobar y pagar',
				'solucao_dif_topico_3'     => 'Control sobre todo el flujo financiero',
				'solucao_acel_titulo'      => 'Modelo listo para comenzar',
				'solucao_acel_corpo'       => 'Comience rápidamente con un flujo preconfigurado que conecta pedido, facturación, cobranza y conciliación financiera.',
				'solucao_acel_topico_1'    => 'Requisición → aprobación → pedido',
				'solucao_acel_topico_2'    => 'Matching de 3 vías automatizado',
				'solucao_acel_topico_3'    => 'Pago tras la verificación',
			)
		);
	}

	/**
	 * Jornada do Colaborador.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_jornada_do_colaborador() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'recorrido del colaborador (h2r)',
				'solucao_hero_titulo'      => 'Del primer día a la desvinculación, todos los sistemas de RR. HH. actualizados.',
				'solucao_hero_corpo'       => 'Orqueste el ciclo de vida completo del colaborador conectando RR. HH., nómina, accesos y beneficios en un único flujo automatizado.',
				'solucao_metrica_1_numero' => '5x',
				'solucao_metrica_1_rotulo' => 'más rápida la toma de decisión de la contratación',
				'solucao_metrica_2_numero' => '75%',
				'solucao_metrica_2_rotulo' => 'más rápida la integración de la fuerza laboral',
				'solucao_metrica_3_numero' => '95%',
				'solucao_metrica_3_rotulo' => 'menos trabajo manual por parte de los usuarios',
				'solucao_pilares_titulo'   => 'Automatice cada momento del recorrido del colaborador',
				'solucao_pilares_1_titulo' => 'Sincronice eventos automáticamente',
				'solucao_pilares_1_desc'   => 'Actualice todos los sistemas satélite a partir de eventos como admisión, promoción y desvinculación.',
				'solucao_pilares_2_titulo' => 'Acelere el onboarding',
				'solucao_pilares_2_desc'   => 'Reduzca el tiempo de activación de nuevos colaboradores de días a horas con procesos conectados.',
				'solucao_pilares_3_titulo' => 'Revoque accesos en la desvinculación',
				'solucao_pilares_3_desc'   => 'Elimine riesgos garantizando que los accesos físicos y digitales se eliminen automáticamente.',
				'solucao_casos_titulo'     => 'Automatice el ciclo de vida del colaborador',
				'solucao_casos_1_titulo'   => 'Automatice admisiones completas',
				'solucao_casos_1_desc'     => 'Conecte HRIS, nómina, correo, accesos, beneficios y LMS en una única activación.',
				'solucao_casos_2_titulo'   => 'Actualice los cambios de cargo',
				'solucao_casos_2_desc'     => 'Sincronice el nivel de acceso y la banda salarial automáticamente durante promociones y movimientos internos.',
				'solucao_casos_3_titulo'   => 'Ejecute desvinculaciones seguras',
				'solucao_casos_3_desc'     => 'Revoque accesos físicos y lógicos en minutos, reduciendo riesgos tras la salida del colaborador.',
				'solucao_casos_4_titulo'   => 'Analice datos de los colaboradores',
				'solucao_casos_4_desc'     => 'Consolide información del ciclo de vida para análisis de rotación y antigüedad.',
				'solucao_casos_5_titulo'   => 'Conecte sistemas satélite de RR. HH.',
				'solucao_casos_5_desc'     => 'Garantice que todos los sistemas relacionados reciban actualizaciones sin depender de checklists manuales.',
				'solucao_dif_titulo'       => 'Seguridad en cada cambio de estado',
				'solucao_dif_corpo'        => 'Proteja los datos sensibles de los colaboradores con controles de seguridad y trazabilidad en cada actualización.',
				'solucao_dif_topico_1'     => 'Enmascaramiento de PII en tránsito',
				'solucao_dif_topico_2'     => 'Auditoría completa de los cambios',
				'solucao_dif_topico_3'     => 'Trazabilidad de cada evento',
				'solucao_acel_titulo'      => 'Modelo listo para comenzar',
				'solucao_acel_corpo'       => 'Comience rápidamente con un flujo preconfigurado que conecta pedido, facturación, cobranza y conciliación financiera.',
				'solucao_acel_topico_1'    => 'Evento de RR. HH. → todos los sistemas',
				'solucao_acel_topico_2'    => 'Admisión automatizada de punta a punta',
				'solucao_acel_topico_3'    => 'Promoción y desvinculación sincronizadas',
			)
		);
	}
	/**
	 * Soberania de Dados.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_soberania_de_dados() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'soberanía de datos',
				'solucao_hero_titulo'      => 'Procese y almacene los datos donde su operación lo exige.',
				'solucao_hero_corpo'       => 'Ejecute CLI Connect powered by Boomi dentro del entorno del propio cliente, garantizando que los datos sensibles permanezcan en la jurisdicción definida por el negocio y por la regulación.',
				'solucao_pilares_titulo'   => 'Control total sobre la residencia de los datos',
				'solucao_pilares_1_titulo' => 'Implemente en su entorno',
				'solucao_pilares_1_desc'   => 'Ejecute integraciones en la nube o en la infraestructura propia del cliente, usando AWS, Azure, GCP o un datacenter interno.',
				'solucao_pilares_2_titulo' => 'Mantenga los datos bajo su control',
				'solucao_pilares_2_desc'   => 'Garantice que la información sensible no transite ni se almacene en entornos compartidos.',
				'solucao_pilares_3_titulo' => 'Atienda las regulaciones de datos',
				'solucao_pilares_3_desc'   => 'Cumpla los requisitos de residencia de datos para sectores como el financiero, la salud y el sector público.',
				'solucao_casos_titulo'     => 'Aplique la soberanía de datos en la práctica',
				'solucao_casos_1_titulo'   => 'Implemente por región regulatoria',
				'solucao_casos_1_desc'     => 'Ejecute pipelines dentro de la nube o de la región exigida por las regulaciones locales de datos.',
				'solucao_casos_2_titulo'   => 'Proteja datos sensibles',
				'solucao_casos_2_desc'     => 'Procese información financiera y de salud sin sacar los datos de la jurisdicción definida.',
				'solucao_casos_3_titulo'   => 'Opere en múltiples países',
				'solucao_casos_3_desc'     => 'Cree arquitecturas multirregión para atender diferentes leyes de datos en cada mercado.',
				'solucao_casos_4_titulo'   => 'Compruebe la conformidad',
				'solucao_casos_4_desc'     => 'Audite dónde se procesó cada dato para demostrar control y atender los requisitos regulatorios.',
				'solucao_casos_5_titulo'   => 'Controle entornos críticos',
				'solucao_casos_5_desc'     => 'Mantenga las integraciones ejecutándose dentro de la infraestructura elegida por su organización.',
				'solucao_dif_titulo'       => 'Soberanía garantizada por la arquitectura',
				'solucao_dif_corpo'        => 'A diferencia de los entornos compartidos, la plataforma se ejecuta dentro del entorno del cliente, garantizando el control sobre los datos y el procesamiento.',
				'solucao_dif_topico_1'     => 'Entorno dedicado al cliente',
				'solucao_dif_topico_2'     => 'Control sobre procesamiento y almacenamiento',
				'solucao_dif_topico_3'     => 'Arquitectura sin compartir datos',
				'solucao_acel_titulo'      => 'Modelo listo para comenzar',
				'solucao_acel_corpo'       => 'Comience rápidamente con un flujo preconfigurado que conecta pedido, facturación, cobranza y conciliación financiera.',
				'solucao_acel_topico_1'    => 'Elección de la región de implementación',
				'solucao_acel_topico_2'    => 'Modelo listo para entornos regulados',
				'solucao_acel_topico_3'    => 'Ejecución en AWS, Azure o GCP',
			)
		);
	}

	/**
	 * Visão 360° do Cliente.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_visao_360_do_cliente() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'visión 360°',
				'solucao_hero_titulo'      => 'Una única visión del cliente en todos los sistemas',
				'solucao_hero_corpo'       => 'Consolide datos de CRM, ERP, soporte y producto en una visión 360º actualizada en tiempo real para equipos y agentes de IA.',
				'solucao_pilares_titulo'   => 'Convierta los datos dispersos en contexto completo',
				'solucao_pilares_1_titulo' => 'Unifique la identidad del cliente',
				'solucao_pilares_1_desc'   => 'Consolide información de CRM, ERP, soporte y producto para crear un perfil único y consistente.',
				'solucao_pilares_2_titulo' => 'Actualice la información en tiempo real',
				'solucao_pilares_2_desc'   => 'Mantenga la visión del cliente siempre sincronizada, sin depender de cargas batch ni de informes desfasados.',
				'solucao_pilares_3_titulo' => 'Comparta el mismo contexto',
				'solucao_pilares_3_desc'   => 'Ponga una visión unificada a disposición de ventas, soporte, customer success y agentes de inteligencia artificial.',
				'solucao_casos_titulo'     => 'Ponga al cliente en el centro de las operaciones',
				'solucao_casos_1_titulo'   => 'Resuelva identidades duplicadas',
				'solucao_casos_1_desc'     => 'Reconcilie múltiples identificadores entre CRM, ERP y soporte para crear un único perfil de cliente.',
				'solucao_casos_2_titulo'   => 'Unifique el historial del cliente',
				'solucao_casos_2_desc'     => 'Reúna pedidos, tickets y uso del producto en una única visión para customer success.',
				'solucao_casos_3_titulo'   => 'Proporcione contexto a los agentes de IA',
				'solucao_casos_3_desc'     => 'Entregue información completa del cliente antes de cada interacción automatizada o asistida.',
				'solucao_casos_4_titulo'   => 'Segmente campañas en tiempo real',
				'solucao_casos_4_desc'     => 'Actualice las audiencias de marketing utilizando datos consolidados de todos los sistemas conectados.',
				'solucao_casos_5_titulo'   => 'Mejore las decisiones de atención',
				'solucao_casos_5_desc'     => 'Permita que los equipos consulten el contexto completo del cliente durante cualquier atención.',
				'solucao_dif_titulo'       => 'Gobernanza para datos unificados',
				'solucao_dif_corpo'        => 'Controle cómo cada sistema accede al perfil unificado del cliente, garantizando conformidad y calidad de los datos.',
				'solucao_dif_topico_1'     => 'Gobernanza compatible con LGPD y GDPR',
				'solucao_dif_topico_2'     => 'Control de lectura y escritura por sistema',
				'solucao_dif_topico_3'     => 'Gestión centralizada de los atributos del cliente',
				'solucao_acel_titulo'      => 'Modelo listo para comenzar',
				'solucao_acel_corpo'       => 'Comience rápidamente con un flujo preconfigurado que conecta pedido, facturación, cobranza y conciliación financiera.',
				'solucao_acel_topico_1'    => 'Resolución automática de identidad',
				'solucao_acel_topico_2'    => 'Visión 360º actualizada en tiempo real',
				'solucao_acel_topico_3'    => 'Contexto único para equipos e IA',
			)
		);
	}

	/**
	 * Integração Pós-Fusão.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_integracao_pos_fusao() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'integración posfusión',
				'solucao_hero_titulo'      => 'Integre las empresas adquiridas desde el primer día',
				'solucao_hero_corpo'       => 'Conecte sistemas críticos sin abrir puertos de firewall y acelere la captura de sinergias mientras ocurre la consolidación de TI.',
				'solucao_pilares_titulo'   => 'Acelere los resultados tras una adquisición',
				'solucao_pilares_1_titulo' => 'Active sistemas antes del cierre',
				'solucao_pilares_1_desc'   => 'Ponga a disposición identidad, nómina y ERP antes de la conclusión del negocio para garantizar la continuidad operativa.',
				'solucao_pilares_2_titulo' => 'Entregue las sinergias en el plazo',
				'solucao_pilares_2_desc'   => 'Conecte entornos con dual-ERP y cumpla los objetivos de integración sin esperar una consolidación completa.',
				'solucao_pilares_3_titulo' => 'Reutilice integraciones en nuevas adquisiciones',
				'solucao_pilares_3_desc'   => 'Cree cápsulas reutilizables para acelerar nuevas integraciones manteniendo estándares consistentes entre empresas.',
				'solucao_casos_titulo'     => 'Integre operaciones sin retrasar el negocio',
				'solucao_casos_1_titulo'   => 'Unifique identidades corporativas',
				'solucao_casos_1_desc'     => 'Conecte Entra ID y Okta para habilitar el acceso único a los colaboradores de las empresas integradas.',
				'solucao_casos_2_titulo'   => 'Sincronice múltiples ERP',
				'solucao_casos_2_desc'     => 'Integre SAP y Oracle Fusion durante la transición sin depender de la consolidación definitiva de los sistemas.',
				'solucao_casos_3_titulo'   => 'Consolide datos de RR. HH.',
				'solucao_casos_3_desc'     => 'Conecte Workday y Oracle HCM para unificar procesos e información después de la fusión.',
				'solucao_casos_4_titulo'   => 'Migre su CRM',
				'solucao_casos_4_desc'     => 'Transfiera la información comercial entre plataformas manteniendo la continuidad en la relación con los clientes.',
				'solucao_casos_5_titulo'   => 'Unifique datos analíticos',
				'solucao_casos_5_desc'     => 'Conecte Snowflake y BigQuery para crear una visión consolidada de las operaciones combinadas.',
				'solucao_dif_titulo'       => 'Integración segura desde el Día 1',
				'solucao_dif_corpo'        => 'Conecte rápidamente los sistemas adquiridos con una arquitectura preparada para entornos corporativos, sin depender de cambios complejos en la infraestructura.',
				'solucao_dif_topico_1'     => 'Runtime con conexión outbound-only',
				'solucao_dif_topico_2'     => 'Deploy multi-cloud o Kubernetes gestionado',
				'solucao_dif_topico_3'     => '300+ conectores sin costo adicional',
			)
		);
	}

	/**
	 * Centro de Excelência em Integração.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_centro_de_excelencia_em_integracao() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'centro de excelencia en integración',
				'solucao_hero_titulo'      => 'Convierta las integraciones en un activo reutilizable de la empresa',
				'solucao_hero_corpo'       => 'Cree un Centro de Excelencia de Integración con catálogo compartido, estándares de desarrollo y gobernanza para acelerar nuevos proyectos.',
				'solucao_pilares_titulo'   => 'Estandarice las integraciones en toda la organización',
				'solucao_pilares_1_titulo' => 'Reutilice las integraciones existentes',
				'solucao_pilares_1_desc'   => 'Centralice pipelines y cápsulas reutilizables para reducir el retrabajo y acelerar nuevos proyectos.',
				'solucao_pilares_2_titulo' => 'Estandarice el desarrollo',
				'solucao_pilares_2_desc'   => 'Defina estándares únicos de nomenclatura, autenticación y tratamiento de errores en todas las integraciones.',
				'solucao_pilares_3_titulo' => 'Fortalezca la gobernanza',
				'solucao_pilares_3_desc'   => 'Controle quién crea, modifica y publica las integraciones críticas con procesos estandarizados de aprobación.',
				'solucao_casos_titulo'     => 'Escale las integraciones con gobernanza',
				'solucao_casos_1_titulo'   => 'Centralice integraciones reutilizables',
				'solucao_casos_1_desc'     => 'Ponga a disposición un catálogo interno de integraciones para acelerar cualquier nuevo proyecto.',
				'solucao_casos_2_titulo'   => 'Estandarice errores y reintentos',
				'solucao_casos_2_desc'     => 'Garantice que todos los pipelines utilicen las mismas reglas de tratamiento y recuperación de fallas.',
				'solucao_casos_3_titulo'   => 'Apruebe integraciones antes de producción',
				'solucao_casos_3_desc'     => 'Implemente flujos de revisión y aprobación para garantizar calidad y conformidad antes del deploy.',
				'solucao_casos_4_titulo'   => 'Monitoree costo y desempeño',
				'solucao_casos_4_desc'     => 'Centralice métricas de uso, rendimiento y consumo para optimizar continuamente sus integraciones.',
				'solucao_casos_5_titulo'   => 'Evite integraciones duplicadas',
				'solucao_casos_5_desc'     => 'Permita que los equipos reutilicen componentes existentes en lugar de reconstruir flujos ya desarrollados.',
				'solucao_dif_titulo'       => 'Gobernanza para integraciones críticas',
				'solucao_dif_corpo'        => 'Implemente controles que garanticen seguridad, trazabilidad y calidad durante todo el ciclo de desarrollo de las integraciones.',
				'solucao_dif_topico_1'     => 'Control de acceso por función',
				'solucao_dif_topico_2'     => 'Flujo de revisión y aprobación',
				'solucao_dif_topico_3'     => 'Auditoría de cambios en pipelines',
				'solucao_acel_titulo'      => 'Modelo listo para comenzar',
				'solucao_acel_corpo'       => 'Comience rápidamente con un flujo preconfigurado que conecta pedido, facturación, cobranza y conciliación financiera.',
				'solucao_acel_topico_1'    => 'Catálogo de cápsulas reutilizables',
				'solucao_acel_topico_2'    => 'Estándares únicos para nuevos proyectos',
				'solucao_acel_topico_3'    => 'Gobernanza lista para escalar',
			)
		);
	}
}
