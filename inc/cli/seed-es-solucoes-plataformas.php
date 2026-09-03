<?php
/**
 * Seed — texto em espanhol das landings de solução: CRM, atendimento e comércio.
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
 * CRM, atendimento e comércio — texto em espanhol.
 */
trait Cliconnect_Seed_Es_Solucoes_Plataformas {


	/**
	 * Salesforce.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_salesforce() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'         => 'Para su Salesforce',
				'solucao_hero_titulo'          => 'Salesforce sin barreras.',
				'solucao_hero_titulo_destaque' => 'Integración sin límites.',
				'solucao_hero_corpo'           => 'Conecte Salesforce a cualquier ERP o base de datos con total flexibilidad y elimine los límites de su arquitectura de datos.',
				'solucao_pilares_titulo'       => "Integraciones más rápidas,\nseguras e inteligentes",
				'solucao_pilares_1_titulo'     => 'Flujos de aprobación automatizados',
				'solucao_pilares_1_desc'       => 'Dispare flujos de aprobación automáticamente cada vez que haya cambios en Salesforce',
				'solucao_pilares_2_titulo'     => 'Operaciones masivas auditables',
				'solucao_pilares_2_desc'       => 'Ejecute operaciones masivas con trazabilidad completa, auditoría centralizada y más seguridad para cambios a gran escala.',
				'solucao_pilares_3_titulo'     => 'Integración segura con entornos internos',
				'solucao_pilares_3_desc'       => 'Conecte Salesforce a los sistemas internos de la empresa sin abrir puertos en el firewall, preservando la seguridad de la infraestructura corporativa.',
				'solucao_casos_titulo'         => "Integraciones más rápidas,\nseguras e inteligentes",
				'solucao_casos_1_titulo'       => 'Lead-to-Quote',
				'solucao_casos_1_desc'         => 'Automatice el proceso desde la generación del lead hasta la creación de la propuesta comercial, conectando Salesforce a las herramientas de calificación, aprobación y ventas.',
				'solucao_casos_2_titulo'       => 'Sincronización de pedidos',
				'solucao_casos_2_desc'         => 'Mantenga pedidos, clientes y productos sincronizados entre Salesforce y ERP como SAP o NetSuite mediante integraciones en tiempo real o programadas.',
				'solucao_casos_3_titulo'       => 'Hub para múltiples organizaciones Salesforce',
				'solucao_casos_3_desc'         => 'Centralice integraciones de diferentes entornos Salesforce en una única arquitectura, simplificando la gobernanza y reduciendo la complejidad operativa.',
				'solucao_casos_4_titulo'       => 'Audiencias para Marketing',
				'solucao_casos_4_desc'         => 'Comparta segmentos y públicos automáticamente entre Salesforce y las plataformas de marketing, manteniendo las campañas siempre actualizadas.',
				'solucao_casos_5_titulo'       => 'Integración con Data Warehouse',
				'solucao_casos_5_desc'         => 'Envíe información de Salesforce a plataformas analíticas como Snowflake y BigQuery para consolidar indicadores y apoyar decisiones basadas en datos.',
				'solucao_dif_titulo'           => 'Una arquitectura preparada para entornos corporativos',
				'solucao_dif_corpo'            => 'Sea cual sea la tecnología que utilice su empresa, CLI Connect aplica las mejores prácticas de integración para garantizar seguridad, gobernanza y alta disponibilidad, respetando las particularidades de cada sistema.',
				'solucao_dif_topico_1'         => 'Soporte completo a las APIs REST de Salesforce',
				'solucao_dif_topico_2'         => 'Automatice eventos con la Subscription API.',
				'solucao_dif_topico_3'         => 'Autentique integraciones con JWT Bearer Flow.',
				'solucao_plat_titulo'          => 'Un único entorno para conectar todo el ecosistema',
				'solucao_plat_corpo'           => 'Conecte Salesforce a los demás sistemas de la empresa en una sola plataforma y elimine integraciones aisladas, procesos manuales y retrabajo a medida que su ecosistema evoluciona.',
				'solucao_plat_topico_1'        => 'Centralice todo su ecosistema',
				'solucao_plat_topico_2'        => 'Elimine integraciones aisladas',
				'solucao_plat_topico_3'        => 'Evolucione sin aumentar la complejidad',
				'solucao_acel_titulo'          => 'Modelo listo para comenzar',
				'solucao_acel_corpo'           => 'Utilice un modelo listo para sincronizar clientes, productos, pedidos u oportunidades entre Salesforce y el ERP.',
				'solucao_acel_topico_1'        => 'Registro de clientes',
				'solucao_acel_topico_2'        => 'Sincronización de pedidos',
				'solucao_acel_topico_3'        => 'Actualización de productos',
			)
		);
	}

	/**
	 * Salesforce Sales Cloud.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_salesforce_sales_cloud() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Salesforce Sales Cloud',
				'solucao_hero_titulo'      => 'Conecte Salesforce Sales Cloud al ERP y acelere todo el ciclo de ventas',
				'solucao_hero_corpo'       => 'Automatice el recorrido del lead a la facturación integrando Sales Cloud con ERP, CPQ, finanzas y los demás sistemas de la empresa, eliminando retrabajo y garantizando datos sincronizados en cada etapa de la venta.',
				'solucao_pilares_titulo'   => 'Conecte todo el recorrido comercial',
				'solucao_pilares_1_titulo' => 'Automatice del lead a la facturación',
				'solucao_pilares_1_desc'   => 'Conecte marketing, CRM, CPQ, ERP y finanzas para transformar oportunidades en pedidos sin procesos manuales.',
				'solucao_pilares_2_titulo' => 'Sincronice ventas y ERP',
				'solucao_pilares_2_desc'   => 'Mantenga oportunidades, cuentas, clientes y pedidos actualizados entre Salesforce y el ERP de forma bidireccional.',
				'solucao_pilares_3_titulo' => 'Active procesos en tiempo real',
				'solucao_pilares_3_desc'   => 'Dispare aprobaciones, notificaciones y automatizaciones de inmediato cada vez que se modifique un registro importante en Salesforce.',
				'solucao_casos_titulo'     => 'Automatice los principales procesos de Sales Cloud',
				'solucao_casos_1_titulo'   => 'Automatice el proceso Lead-to-Quote',
				'solucao_casos_1_desc'     => 'Conecte marketing, Sales Cloud y CPQ para acelerar la generación de propuestas y reducir etapas manuales.',
				'solucao_casos_2_titulo'   => 'Sincronice pedidos con el ERP',
				'solucao_casos_2_desc'     => 'Actualice los pedidos del ERP automáticamente en Sales Cloud en procesos programados o en tiempo real.',
				'solucao_casos_3_titulo'   => 'Genere pedidos automáticamente',
				'solucao_casos_3_desc'     => 'Transforme oportunidades ganadas en pedidos en el ERP sin volver a digitar ni intervención operativa.',
				'solucao_casos_4_titulo'   => 'Conecte múltiples organizaciones Salesforce',
				'solucao_casos_4_desc'     => 'Centralice datos entre diferentes instancias Salesforce manteniendo la información comercial sincronizada.',
				'solucao_casos_5_titulo'   => 'Reciba alertas de oportunidades',
				'solucao_casos_5_desc'     => 'Dispare notificaciones y procesos cada vez que las oportunidades cambien de etapa durante la negociación.',
				'solucao_dif_titulo'       => 'Integraciones preparadas para producción Salesforce',
				'solucao_dif_corpo'        => 'Utilice todas las principales operaciones de la REST API, eventos en tiempo real y autenticación segura para construir integraciones escalables sin comprometer la arquitectura de Salesforce.',
				'solucao_dif_topico_1'     => 'Utilice las APIs oficiales de Salesforce.',
				'solucao_dif_topico_2'     => 'Automatice eventos en tiempo real.',
				'solucao_dif_topico_3'     => 'Proteja las conexiones con JWT Bearer Flow.',
				'solucao_plat_titulo'      => 'Centralice todo el recorrido comercial en una plataforma',
				'solucao_plat_corpo'       => 'Sales Cloud depende del ERP, del CPQ y de la facturación para concluir una venta. Centralice todas esas integraciones en una única plataforma para reducir costos, simplificar la arquitectura y acelerar nuevas automatizaciones.',
				'solucao_plat_topico_1'    => 'Centralice las integraciones del ciclo comercial.',
				'solucao_plat_topico_2'    => 'Reutilice flujos entre diferentes proyectos.',
				'solucao_plat_topico_3'    => 'Reduzca la dependencia de desarrollos específicos.',
				'solucao_acel_titulo'      => 'Comience con automatizaciones ya estructuradas',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para automatizar todo el recorrido entre Sales Cloud, CPQ y ERP, reduciendo el tiempo de implantación y acelerando la entrega de valor.',
				'solucao_acel_topico_1'    => 'Implante integraciones en pocos días.',
				'solucao_acel_topico_2'    => 'Reutilice modelos ya validados.',
				'solucao_acel_topico_3'    => 'Adapte los flujos a su proceso comercial.',
			)
		);
	}

	/**
	 * Salesforce Service Cloud.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_salesforce_service_cloud() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Salesforce Service Cloud',
				'solucao_hero_titulo'      => 'Conecte Salesforce Service Cloud y brinde atención con contexto completo',
				'solucao_hero_corpo'       => 'Integre Service Cloud al ERP, la facturación, el field service y los canales de atención para que su equipo resuelva los tickets más rápido, sin alternar entre sistemas.',
				'solucao_pilares_titulo'   => 'Conecte toda la operación de atención',
				'solucao_pilares_1_titulo' => 'Enriquezca cada atención',
				'solucao_pilares_1_desc'   => 'Ponga los datos de pedidos, facturación e historial del cliente directamente en el caso, sin depender de consultas en otros sistemas.',
				'solucao_pilares_2_titulo' => 'Orqueste todos los canales',
				'solucao_pilares_2_desc'   => 'Conecte telefonía, WhatsApp, chat y los demás canales a Service Cloud para centralizar todo el recorrido de atención.',
				'solucao_pilares_3_titulo' => 'Automatice SLAs y escalamientos',
				'solucao_pilares_3_desc'   => 'Dispare reglas, notificaciones y derivaciones automáticamente según los eventos de la atención y las integraciones corporativas.',
				'solucao_casos_titulo'     => 'Automatice los principales procesos de atención',
				'solucao_casos_1_titulo'   => 'Enriquezca los casos con datos del ERP',
				'solucao_casos_1_desc'     => 'Presente información de facturación, pedidos y contratos en tiempo real durante la atención al cliente.',
				'solucao_casos_2_titulo'   => 'Integre a los equipos de campo',
				'solucao_casos_2_desc'     => 'Conecte órdenes de servicio y sistemas de field service para acompañar toda la ejecución de la atención.',
				'solucao_casos_3_titulo'   => 'Automatice reembolsos',
				'solucao_casos_3_desc'     => 'Dispare procesos de reversión y reembolso automáticamente tras la resolución de un caso.',
				'solucao_casos_4_titulo'   => 'Sincronice la base de conocimiento',
				'solucao_casos_4_desc'     => 'Mantenga los contenidos actualizados entre Service Cloud y los portales de autoservicio sin procesos manuales.',
				'solucao_casos_5_titulo'   => 'Reciba alertas proactivas de SLA',
				'solucao_casos_5_desc'     => 'Active notificaciones y escalamientos en tiempo real cada vez que un SLA esté en riesgo.',
				'solucao_dif_titulo'       => 'Atención siempre actualizada entre todos los sistemas',
				'solucao_dif_corpo'        => 'Utilice eventos en tiempo real vía Subscription API para mantener Service Cloud sincronizado con el ERP, la facturación y las demás aplicaciones, garantizando decisiones basadas en información actual.',
				'solucao_dif_topico_1'     => 'Actualice datos en tiempo real',
				'solucao_dif_topico_2'     => 'Evite información desactualizada en la atención',
				'solucao_dif_topico_3'     => 'Integre eventos entre todos los sistemas',
				'solucao_plat_titulo'      => 'Una plataforma para centralizar toda la atención',
				'solucao_plat_corpo'       => 'Gran parte del tiempo de atención se pierde consultando otros sistemas. Centralice esas integraciones para entregar todo el contexto directamente en Service Cloud y acelerar la resolución de los casos.',
				'solucao_plat_topico_1'    => 'Centralice los datos del cliente',
				'solucao_plat_topico_2'    => 'Reduzca los cambios entre sistemas',
				'solucao_plat_topico_3'    => 'Acelere el tiempo de resolución',
				'solucao_acel_titulo'      => 'Comience con integraciones listas para la atención',
				'solucao_acel_corpo'       => 'Utilice un modelo preconfigurado para consultar facturación, pedidos e información del ERP directamente en Service Cloud, reduciendo el tiempo de implantación.',
				'solucao_acel_topico_1'    => 'Implante consultas rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice modelos validados',
				'solucao_acel_topico_3'    => 'Adapte los flujos a su negocio',
			)
		);
	}

	/**
	 * Salesforce Marketing Cloud.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_salesforce_marketing_cloud() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Salesforce Marketing Cloud',
				'solucao_hero_titulo'      => 'Alimente sus recorridos de marketing con datos vivos de ventas y producto',
				'solucao_hero_corpo'       => 'Conecte Salesforce Marketing Cloud al CRM, al e-commerce y al data warehouse para crear campañas basadas en comportamientos reales, con datos actualizados en cada interacción con el cliente.',
				'solucao_pilares_titulo'   => 'Convierta los datos en recorridos relevantes',
				'solucao_pilares_1_titulo' => 'Dispare recorridos por eventos reales',
				'solucao_pilares_1_desc'   => 'Active campañas automáticamente a partir de compras, uso de producto e interacciones de soporte.',
				'solucao_pilares_2_titulo' => 'Sincronice audiencias en tiempo real',
				'solucao_pilares_2_desc'   => 'Mantenga listas y segmentos actualizados entre Marketing Cloud, CRM y ERP de forma continua.',
				'solucao_pilares_3_titulo' => 'Personalice con datos completos',
				'solucao_pilares_3_desc'   => 'Enriquezca los perfiles de contacto con información de ventas, producto y comportamiento.',
				'solucao_casos_titulo'     => 'Automatice recorridos orientados por datos',
				'solucao_casos_1_titulo'   => 'Dispare recorridos automáticamente',
				'solucao_casos_1_desc'     => 'Active Journey Builder a partir de eventos de e-commerce y ERP en tiempo real.',
				'solucao_casos_2_titulo'   => 'Sincronice audiencias comerciales',
				'solucao_casos_2_desc'     => 'Conecte segmentos entre Marketing Cloud, Sales Cloud y Service Cloud de forma continua.',
				'solucao_casos_3_titulo'   => 'Enriquezca perfiles de clientes',
				'solucao_casos_3_desc'     => 'Agregue datos de uso de producto para crear experiencias más personalizadas.',
				'solucao_casos_4_titulo'   => 'Cierre la atribución de campañas',
				'solucao_casos_4_desc'     => 'Conecte marketing, CRM y ERP para seguir el impacto hasta los ingresos.',
				'solucao_casos_5_titulo'   => 'Aplique el opt-out automáticamente',
				'solucao_casos_5_desc'     => 'Propague las preferencias de contacto entre todos los canales conectados.',
				'solucao_dif_titulo'       => 'Integraciones seguras para datos de marketing',
				'solucao_dif_corpo'        => 'Utilice las APIs REST y SOAP de Marketing Cloud con controles de consentimiento para garantizar que las preferencias acompañen a todos los sistemas conectados.',
				'solucao_dif_topico_1'     => 'Utilice las APIs oficiales REST y SOAP',
				'solucao_dif_topico_2'     => 'Propague consentimientos entre sistemas',
				'solucao_dif_topico_3'     => 'Controle los opt-outs en todos los canales',
				'solucao_plat_titulo'      => 'Centralice recorridos con datos conectados',
				'solucao_plat_corpo'       => 'Conecte Marketing Cloud a ventas, producto y soporte en una única plataforma para crear recorridos basados en el comportamiento real, no en listas antiguas.',
				'solucao_plat_topico_1'    => 'Conecte eventos reales del negocio',
				'solucao_plat_topico_2'    => 'Elimine exportaciones manuales de listas',
				'solucao_plat_topico_3'    => 'Unifique datos entre áreas comerciales',
				'solucao_acel_titulo'      => 'Comience con recorridos ya estructurados',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para disparar recorridos en Journey Builder a partir de eventos del ERP y del e-commerce, acelerando su operación de marketing.',
				'solucao_acel_topico_1'    => 'Configure eventos rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice flujos validados',
				'solucao_acel_topico_3'    => 'Adapte los recorridos al negocio',
			)
		);
	}

	/**
	 * RD Station CRM.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_rd_station_crm() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su RD Station',
				'solucao_hero_titulo'      => 'Conecte RD Station CRM al ERP y al marketing sin depender de planillas',
				'solucao_hero_corpo'       => 'Automatice la conexión entre ventas, ERP, e-commerce y herramientas de marketing para mantener los datos sincronizados, eliminar el retrabajo manual y acompañar el crecimiento de la operación comercial.',
				'solucao_pilares_titulo'   => 'Escale su operación comercial conectada',
				'solucao_pilares_1_titulo' => 'Sincronice ventas con el ERP',
				'solucao_pilares_1_desc'   => 'Envíe los negocios ganados automáticamente al ERP y elimine la digitación manual de pedidos, facturas y registros de clientes.',
				'solucao_pilares_2_titulo' => 'Enriquezca leads automáticamente',
				'solucao_pilares_2_desc'   => 'Complemente los datos de leads de RD Station con información del ERP y de otras fuentes para calificar mejor cada oportunidad.',
				'solucao_pilares_3_titulo' => 'Conecte sistemas en crecimiento',
				'solucao_pilares_3_desc'   => 'Integre RD Station CRM a nuevas herramientas a medida que la operación crece, sin reescribir las integraciones existentes.',
				'solucao_casos_titulo'     => 'Automatice procesos del ciclo comercial',
				'solucao_casos_1_titulo'   => 'Envíe negocios ganados al ERP',
				'solucao_casos_1_desc'     => 'Al cerrar un negocio en RD Station CRM, dispare automáticamente la creación del pedido o contrato en el ERP sin intervención manual.',
				'solucao_casos_2_titulo'   => 'Enriquezca los datos de leads',
				'solucao_casos_2_desc'     => 'Sincronice la información de clientes entre el CRM y el ERP para mantener el historial comercial y financiero en un único registro.',
				'solucao_casos_3_titulo'   => 'Conecte el historial de compras',
				'solucao_casos_3_desc'     => 'Ponga los datos de pedidos y facturas del ERP directamente en RD Station CRM para que los vendedores sigan el historial de cada cuenta.',
				'solucao_casos_4_titulo'   => 'Consolide datos para BI',
				'solucao_casos_4_desc'     => 'Unifique las métricas de ventas del CRM con los datos financieros del ERP en tableros de inteligencia de negocios.',
				'solucao_casos_5_titulo'   => 'Distribuya leads automáticamente',
				'solucao_casos_5_desc'     => 'Enrute los leads calificados a los vendedores según reglas de territorio, segmento o capacidad de atención.',
				'solucao_dif_titulo'       => 'Integraciones seguras con API oficial',
				'solucao_dif_corpo'        => 'Las integraciones utilizan la API REST oficial de RD Station CRM con autenticación por OAuth2 y tokens individuales por integración, garantizando trazabilidad y control de acceso granular.',
				'solucao_dif_topico_1'     => 'Utilice la API REST oficial de RD Station.',
				'solucao_dif_topico_2'     => 'Controle los accesos por permisos.',
				'solucao_dif_topico_3'     => 'Proteja las conexiones con tokens individuales.',
				'solucao_plat_titulo'      => 'Centralice sus conexiones comerciales en una única plataforma',
				'solucao_plat_corpo'       => 'Las empresas que crecen adoptan nuevas herramientas con el tiempo. Centralice todas las integraciones de RD Station CRM en una plataforma única para simplificar la gestión y escalar sin retrabajo.',
				'solucao_plat_topico_1'    => 'Conecte CRM y ERP a escala.',
				'solucao_plat_topico_2'    => 'Reduzca procesos manuales con automatizaciones.',
				'solucao_plat_topico_3'    => 'Evolucione los sistemas sin cambiar de herramienta.',
				'solucao_acel_titulo'      => 'Comience con una integración lista',
				'solucao_acel_corpo'       => 'Implante flujos comerciales ya validados para sincronizar negocios ganados, datos de clientes e historial de compras entre RD Station CRM y su ERP.',
				'solucao_acel_topico_1'    => 'Implante flujos comerciales validados.',
				'solucao_acel_topico_2'    => 'Sincronice las ventas automáticamente.',
				'solucao_acel_topico_3'    => 'Adapte las reglas a su proceso.',
			)
		);
	}

	/**
	 * RD Station Marketing.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_rd_station_marketing() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su RD Station Marketing',
				'solucao_hero_titulo'      => 'Automatice su marketing con datos de ventas y producto en tiempo real',
				'solucao_hero_corpo'       => 'Conecte RD Station Marketing al CRM, al ERP y a las herramientas de analytics para transformar leads en oportunidades con datos actualizados en cada etapa del embudo comercial.',
				'solucao_pilares_titulo'   => 'Conecte el marketing al ciclo comercial',
				'solucao_pilares_1_titulo' => 'Sincronice leads calificados',
				'solucao_pilares_1_desc'   => 'Envíe MQLs y SQLs automáticamente al CRM de ventas sin demoras ni procesos manuales.',
				'solucao_pilares_2_titulo' => 'Capture eventos de conversión',
				'solucao_pilares_2_desc'   => 'Use webhooks y API REST para reaccionar rápidamente a las interacciones relevantes de los clientes.',
				'solucao_pilares_3_titulo' => 'Unifique los datos del embudo',
				'solucao_pilares_3_desc'   => 'Conecte marketing, ventas e ingresos para acompañar todo el recorrido hasta el cierre.',
				'solucao_casos_titulo'     => 'Automatice procesos de marketing y ventas',
				'solucao_casos_1_titulo'   => 'Envíe MQLs al CRM',
				'solucao_casos_1_desc'     => 'Sincronice los leads calificados de RD Station Marketing con el CRM en tiempo real.',
				'solucao_casos_2_titulo'   => 'Dispare automatizaciones por eventos',
				'solucao_casos_2_desc'     => 'Active flujos de marketing a partir de acciones de producto o de ventas.',
				'solucao_casos_3_titulo'   => 'Mida la atribución de campañas',
				'solucao_casos_3_desc'     => 'Conecte las campañas al CRM y al ERP para seguir el impacto hasta los ingresos.',
				'solucao_casos_4_titulo'   => 'Enriquezca los datos de leads',
				'solucao_casos_4_desc'     => 'Combine información externa para crear perfiles comerciales más completos.',
				'solucao_casos_5_titulo'   => 'Retire a los clientes convertidos',
				'solucao_casos_5_desc'     => 'Saque automáticamente los contactos vendidos de los flujos de nutrición.',
				'solucao_dif_titulo'       => 'Integraciones confiables vía API oficial',
				'solucao_dif_corpo'        => 'Conecte RD Station Marketing utilizando webhooks y API REST con deduplicación de contactos para mantener alineados a marketing y ventas.',
				'solucao_dif_topico_1'     => 'Utilice webhooks para eventos rápidos',
				'solucao_dif_topico_2'     => 'Conecte vía API REST oficial',
				'solucao_dif_topico_3'     => 'Evite la duplicidad entre contactos',
				'solucao_plat_titulo'      => 'Conecte todo el embudo comercial en una plataforma',
				'solucao_plat_corpo'       => 'Marketing y ventas pierden eficiencia cuando trabajan con datos desconectados. Centralice las integraciones para acompañar al cliente desde el primer clic hasta el pedido facturado.',
				'solucao_plat_topico_1'    => 'Unifique datos de marketing y ventas',
				'solucao_plat_topico_2'    => 'Elimine cruces manuales de planillas',
				'solucao_plat_topico_3'    => 'Conecte todo el recorrido comercial',
				'solucao_acel_titulo'      => 'Comience con leads ya estructurados',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para sincronizar los leads calificados de RD Station Marketing con cualquier CRM y acelere el traspaso entre marketing y ventas.',
				'solucao_acel_topico_1'    => 'Conecte MQLs automáticamente',
				'solucao_acel_topico_2'    => 'Reutilice flujos ya validados',
				'solucao_acel_topico_3'    => 'Adapte las reglas a su proceso',
			)
		);
	}

	/**
	 * HubSpot CRM.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_hubspot_crm() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su HubSpot',
				'solucao_hero_titulo'      => 'Conecte HubSpot al ERP y al resto del embudo comercial',
				'solucao_hero_corpo'       => 'Integre CRM, marketing, e-commerce y facturación para transformar oportunidades en operaciones conectadas sin depender solo de las apps del Marketplace.',
				'solucao_pilares_titulo'   => 'Amplíe el potencial de HubSpot CRM',
				'solucao_pilares_1_titulo' => 'Convierta ventas automáticamente',
				'solucao_pilares_1_desc'   => 'Transforme los negocios cerrados en pedidos en el ERP sin retrabajo.',
				'solucao_pilares_2_titulo' => 'Enriquezca los datos comerciales',
				'solucao_pilares_2_desc'   => 'Actualice contactos y empresas con información de otros sistemas.',
				'solucao_pilares_3_titulo' => 'Supere las limitaciones del Marketplace',
				'solucao_pilares_3_desc'   => 'Cree integraciones para escenarios específicos de su negocio.',
				'solucao_casos_titulo'     => 'Automatice procesos de HubSpot CRM',
				'solucao_casos_1_titulo'   => 'Envíe las ventas al ERP',
				'solucao_casos_1_desc'     => 'Cree pedidos automáticamente tras el cierre de los negocios.',
				'solucao_casos_2_titulo'   => 'Enriquezca contactos automáticamente',
				'solucao_casos_2_desc'     => 'Combine datos de producto, soporte y comportamiento del cliente.',
				'solucao_casos_3_titulo'   => 'Integre el e-commerce al CRM',
				'solucao_casos_3_desc'     => 'Ponga el historial de compras al servicio de la relación comercial.',
				'solucao_casos_4_titulo'   => 'Consolide los datos de marketing',
				'solucao_casos_4_desc'     => 'Centralice el embudo comercial y las campañas para el análisis estratégico.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga los datos del CRM a disposición de agentes de IA mediante APIs gobernadas y servidores MCP.',
				'solucao_dif_titulo'       => 'Integraciones seguras para HubSpot',
				'solucao_dif_corpo'        => 'Utilice la API REST oficial de HubSpot con control de acceso, tokens privados y permisos definidos por alcance.',
				'solucao_dif_topico_1'     => 'Utilice las APIs oficiales.',
				'solucao_dif_topico_2'     => 'Controle los permisos por alcance.',
				'solucao_dif_topico_3'     => 'Proteja los datos comerciales.',
				'solucao_plat_titulo'      => 'Conecte todo su ecosistema comercial',
				'solucao_plat_corpo'       => 'Centralice CRM, ERP y sistemas operativos en una única capa de integración para acompañar el crecimiento de la empresa.',
				'solucao_plat_topico_1'    => 'Integre múltiples sistemas.',
				'solucao_plat_topico_2'    => 'Escale los procesos comerciales.',
				'solucao_plat_topico_3'    => 'Evite las conexiones aisladas.',
				'solucao_acel_titulo'      => 'Comience con las ventas conectadas',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para transformar los negocios cerrados en HubSpot en pedidos en el ERP.',
				'solucao_acel_topico_1'    => 'Automatice las ventas rápidamente.',
				'solucao_acel_topico_2'    => 'Reutilice los flujos comerciales.',
				'solucao_acel_topico_3'    => 'Acelere nuevas integraciones.',
			)
		);
	}

	/**
	 * Propz.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_propz() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Propz',
				'solucao_hero_titulo'      => 'Conecte la inteligencia de Propz a los datos de su empresa',
				'solucao_hero_corpo'       => 'Integre PDV, e-commerce y ERP para alimentar la personalización del retail con datos actualizados y activar ofertas en el canal correcto.',
				'solucao_pilares_titulo'   => 'Convierta los datos en experiencias personalizadas',
				'solucao_pilares_1_titulo' => 'Alimente datos en tiempo real',
				'solucao_pilares_1_desc'   => 'Conecte las ventas del PDV, el e-commerce y el ERP a Propz.',
				'solucao_pilares_2_titulo' => 'Active ofertas automáticamente',
				'solucao_pilares_2_desc'   => 'Envíe campañas personalizadas a los canales digitales.',
				'solucao_pilares_3_titulo' => 'Centralice el historial de compras',
				'solucao_pilares_3_desc'   => 'Unifique datos multicanal para entender a los consumidores.',
				'solucao_casos_titulo'     => 'Automatice procesos de personalización',
				'solucao_casos_1_titulo'   => 'Envíe las ventas a Propz',
				'solucao_casos_1_desc'     => 'Actualice la inteligencia de consumo con datos de venta.',
				'solucao_casos_2_titulo'   => 'Distribuya ofertas personalizadas',
				'solucao_casos_2_desc'     => 'Active campañas de Propz en app, SMS y correo electrónico.',
				'solucao_casos_3_titulo'   => 'Consolide las compras multicanal',
				'solucao_casos_3_desc'     => 'Unifique el historial para la segmentación de clientes.',
				'solucao_casos_4_titulo'   => 'Mida los resultados de las campañas',
				'solucao_casos_4_desc'     => 'Devuelva los datos de campaña al CRM y al ERP.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga los datos de los consumidores a disposición de agentes de IA mediante APIs gobernadas y servidores MCP.',
				'solucao_dif_titulo'       => 'Integraciones seguras para datos de clientes',
				'solucao_dif_corpo'        => 'Conecte Propz vía API REST con gobernanza de datos y controles alineados a las exigencias de la LGPD.',
				'solucao_dif_topico_1'     => 'Proteja los datos de los consumidores.',
				'solucao_dif_topico_2'     => 'Controle los accesos por integración.',
				'solucao_dif_topico_3'     => 'Gobierne los datos conforme a la LGPD.',
				'solucao_plat_titulo'      => 'Conecte datos y personalización',
				'solucao_plat_corpo'       => 'Centralice la entrada y la salida de datos entre Propz, los canales digitales y los sistemas internos sin procesos manuales.',
				'solucao_plat_topico_1'    => 'Integre canales de activación.',
				'solucao_plat_topico_2'    => 'Centralice los datos comerciales.',
				'solucao_plat_topico_3'    => 'Automatice recorridos personalizados.',
				'solucao_acel_titulo'      => 'Comience con el retail personalizado',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para conectar ventas, Propz y canales de activación en un flujo completo.',
				'solucao_acel_topico_1'    => 'Conecte los datos rápidamente.',
				'solucao_acel_topico_2'    => 'Reutilice flujos de campaña.',
				'solucao_acel_topico_3'    => 'Acelere la personalización comercial.',
			)
		);
	}

	/**
	 * Zendesk.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_zendesk() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Zendesk',
				'solucao_hero_titulo'      => 'Potencie Zendesk creando procesos completos sin apps adicionales',
				'solucao_hero_corpo'       => 'Conecte Zendesk al ERP, al CRM y a los sistemas de facturación para crear formularios y flujos de atención que consultan y graban información directamente en los sistemas internos.',
				'solucao_pilares_titulo'   => 'Convierta Zendesk en una central de atención conectada',
				'solucao_pilares_1_titulo' => 'Conecte la atención a los sistemas internos',
				'solucao_pilares_1_desc'   => 'Integre tickets, formularios y macros de Zendesk directamente al ERP, al CRM y a la facturación.',
				'solucao_pilares_2_titulo' => 'Reduzca las apps adicionales',
				'solucao_pilares_2_desc'   => 'Evite depender de aplicaciones pagas del Marketplace para cada nueva integración.',
				'solucao_pilares_3_titulo' => 'Enriquezca los tickets automáticamente',
				'solucao_pilares_3_desc'   => 'Consulte datos de pedidos, clientes y facturas sin salir de la pantalla de atención.',
				'solucao_casos_titulo'     => 'Automatice procesos críticos de atención',
				'solucao_casos_1_titulo'   => 'Automatice las solicitudes de reembolso',
				'solucao_casos_1_desc'     => 'Consulte y grabe información financiera en el ERP directamente desde el ticket de Zendesk.',
				'solucao_casos_2_titulo'   => 'Enriquezca los tickets en tiempo real',
				'solucao_casos_2_desc'     => 'Muestre datos de pedidos y facturas del ERP durante la atención al cliente.',
				'solucao_casos_3_titulo'   => 'Cree tickets automáticamente',
				'solucao_casos_3_desc'     => 'Transforme eventos del ERP, del e-commerce y del monitoreo en tickets de soporte.',
				'solucao_casos_4_titulo'   => 'Sincronice atención y ventas',
				'solucao_casos_4_desc'     => 'Mantenga el estado de los tickets actualizado entre Zendesk y las plataformas CRM.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga los tickets y las macros de Zendesk como herramientas para agentes inteligentes.',
				'solucao_dif_titulo'       => 'Integraciones seguras para la atención',
				'solucao_dif_corpo'        => 'Conecte Zendesk usando APIs oficiales con autenticación segura y control de permisos según agentes y departamentos.',
				'solucao_dif_topico_1'     => 'Utilice la Zendesk REST API',
				'solucao_dif_topico_2'     => 'Proteja las conexiones con OAuth',
				'solucao_dif_topico_3'     => 'Controle los permisos por agente',
				'solucao_plat_titulo'      => 'Centralice integraciones más allá de Zendesk',
				'solucao_plat_corpo'       => 'Deje Zendesk enfocado en la experiencia del cliente mientras la plataforma conecta y mueve los datos en los sistemas internos sin apps adicionales.',
				'solucao_plat_topico_1'    => 'Centralice las integraciones de atención',
				'solucao_plat_topico_2'    => 'Reduzca la dependencia del Marketplace',
				'solucao_plat_topico_3'    => 'Escale procesos con previsibilidad',
				'solucao_acel_titulo'      => 'Comience con procesos ya estructurados',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para conectar los tickets de Zendesk al ERP y al CRM en procesos de consulta y grabación.',
				'solucao_acel_topico_1'    => 'Conecte procesos rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice flujos de atención',
				'solucao_acel_topico_3'    => 'Adapte las integraciones al negocio',
			)
		);
	}

	/**
	 * ServiceNow.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_servicenow() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su ServiceNow',
				'solucao_hero_titulo'      => 'Potencie ServiceNow sin pagar por más módulos',
				'solucao_hero_corpo'       => 'Construya procesos completos en ServiceNow y conecte directamente el ERP, el CRM y los sistemas internos sin depender de módulos adicionales de integración.',
				'solucao_pilares_titulo'   => 'Convierta ServiceNow en una central de procesos',
				'solucao_pilares_1_titulo' => 'Cree procesos conectados',
				'solucao_pilares_1_desc'   => 'Use catálogo, aprobaciones y flujos vinculados a los sistemas internos.',
				'solucao_pilares_2_titulo' => 'Reduzca los costos de licenciamiento',
				'solucao_pilares_2_desc'   => 'Evite módulos pagos para cada nueva integración necesaria.',
				'solucao_pilares_3_titulo' => 'Orqueste de punta a punta',
				'solucao_pilares_3_desc'   => 'Capture solicitudes, valide datos y grabe en los sistemas.',
				'solucao_casos_titulo'     => 'Automatice procesos conectados a ServiceNow',
				'solucao_casos_1_titulo'   => 'Automatice el registro de productos',
				'solucao_casos_1_desc'     => 'Cree aprobaciones en ServiceNow y grabe los datos en el ERP.',
				'solucao_casos_2_titulo'   => 'Abra incidentes automáticamente',
				'solucao_casos_2_desc'     => 'Reciba eventos de IA y de otros sistemas directamente en ServiceNow.',
				'solucao_casos_3_titulo'   => 'Sincronice la CMDB',
				'solucao_casos_3_desc'     => 'Conecte datos de infraestructura sin Spokes adicionales.',
				'solucao_casos_4_titulo'   => 'Valide las aprobaciones en el ERP',
				'solucao_casos_4_desc'     => 'Consulte presupuesto e inventario antes de aprobar cambios.',
				'solucao_casos_5_titulo'   => 'Automatice los accesos corporativos',
				'solucao_casos_5_desc'     => 'Dispare aprovisionamientos a partir de eventos de RR. HH.',
				'solucao_dif_titulo'       => 'Integraciones ServiceNow con gobernanza completa',
				'solucao_dif_corpo'        => 'Conecte ServiceNow vía API con autenticación segura y eventos bidireccionales manteniendo la auditoría centralizada sin depender de conectores pagos.',
				'solucao_dif_topico_1'     => 'Utilice las APIs oficiales de ServiceNow',
				'solucao_dif_topico_2'     => 'Controle los accesos por autenticación',
				'solucao_dif_topico_3'     => 'Audite todos los eventos',
				'solucao_plat_titulo'      => 'Conecte procesos sin limitar el crecimiento',
				'solucao_plat_corpo'       => 'Use ServiceNow para orquestar experiencias mientras CLI Connect conecta los sistemas internos sin aumentar los costos de licenciamiento.',
				'solucao_plat_topico_1'    => 'Centralice las integraciones corporativas',
				'solucao_plat_topico_2'    => 'Reduzca la dependencia de Spokes',
				'solucao_plat_topico_3'    => 'Escale nuevos procesos',
				'solucao_acel_titulo'      => 'Comience con procesos ya estructurados',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para crear procesos en ServiceNow y grabar datos directamente en ERP como Totvs o SAP.',
				'solucao_acel_topico_1'    => 'Configure procesos rápidamente',
				'solucao_acel_topico_2'    => 'Adapte entidades existentes',
				'solucao_acel_topico_3'    => 'Acelere nuevas automatizaciones',
			)
		);
	}

	/**
	 * Freshservice.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_freshservice() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Freshservice',
				'solucao_hero_titulo'      => 'Potencie Freshservice creando procesos completos sin nuevos módulos',
				'solucao_hero_corpo'       => 'Conecte Freshservice a los sistemas internos para crear formularios, aprobaciones y catálogos de servicio que graban directamente en el ERP, el CRM o las bases de datos, reduciendo los costos de licenciamiento.',
				'solucao_pilares_titulo'   => 'Convierta Freshservice en una plataforma de procesos',
				'solucao_pilares_1_titulo' => 'Cree procesos dentro de Freshservice',
				'solucao_pilares_1_desc'   => 'Construya formularios, aprobaciones y catálogos conectados a los sistemas internos de la empresa.',
				'solucao_pilares_2_titulo' => 'Reduzca las licencias adicionales',
				'solucao_pilares_2_desc'   => 'Evite módulos extra para integrar los procesos de Freshservice con otras aplicaciones corporativas.',
				'solucao_pilares_3_titulo' => 'Reutilice los workflows creados',
				'solucao_pilares_3_desc'   => 'Transforme cada proceso desarrollado en un flujo reutilizable para nuevas necesidades.',
				'solucao_casos_titulo'     => 'Automatice procesos críticos desde Freshservice',
				'solucao_casos_1_titulo'   => 'Solicite compras por Freshservice',
				'solucao_casos_1_desc'     => 'Cree formularios de compra que registran los pedidos directamente en el ERP sin módulos adicionales.',
				'solucao_casos_2_titulo'   => 'Automatice los accesos internos',
				'solucao_casos_2_desc'     => 'Conecte el catálogo de servicios a Active Directory u Okta para aprovisionar accesos automáticamente.',
				'solucao_casos_3_titulo'   => 'Automatice el onboarding de colaboradores',
				'solucao_casos_3_desc'     => 'Dispare altas simultáneas en nómina, correo y sistemas internos desde Freshservice.',
				'solucao_casos_4_titulo'   => 'Abra tickets automáticamente',
				'solucao_casos_4_desc'     => 'Transforme eventos de monitoreo, RR. HH. y seguridad en tickets del Service Desk.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga los tickets y procesos de Freshservice a disposición de agentes inteligentes de soporte.',
				'solucao_dif_titulo'       => 'Integraciones seguras para procesos críticos',
				'solucao_dif_corpo'        => 'Utilice las APIs REST de Freshservice con autenticación segura y control de acceso para conectar procesos según las políticas internas.',
				'solucao_dif_topico_1'     => 'Utilice las APIs REST oficiales',
				'solucao_dif_topico_2'     => 'Controle los accesos por departamento',
				'solucao_dif_topico_3'     => 'Proteja las conexiones con API Key',
				'solucao_plat_titulo'      => 'Centralice procesos sin depender de módulos',
				'solucao_plat_corpo'       => 'Convierta Freshservice en la interfaz de los procesos mientras la plataforma de integración conecta y graba los datos en los sistemas internos.',
				'solucao_plat_topico_1'    => 'Centralice los workflows corporativos',
				'solucao_plat_topico_2'    => 'Evite nuevos licenciamientos del proveedor',
				'solucao_plat_topico_3'    => 'Conecte sistemas sin add-ons',
				'solucao_acel_titulo'      => 'Comience con procesos ya estructurados',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para conectar los formularios y catálogos de Freshservice a los sistemas internos con más velocidad.',
				'solucao_acel_topico_1'    => 'Conecte procesos en pocos minutos',
				'solucao_acel_topico_2'    => 'Reutilice workflows ya validados',
				'solucao_acel_topico_3'    => 'Adapte los flujos al negocio',
			)
		);
	}

	/**
	 * VTEX.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_vtex() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su VTEX',
				'solucao_hero_titulo'      => 'Conecte su e-commerce al ERP en tiempo real',
				'solucao_hero_corpo'       => 'Integre VTEX al ERP, al WMS y a los sistemas de pago para sincronizar pedidos, inventario y operaciones omnicanal con velocidad y seguridad.',
				'solucao_pilares_titulo'   => 'Escale su operación digital conectada',
				'solucao_pilares_1_titulo' => 'Sincronice pedidos automáticamente',
				'solucao_pilares_1_desc'   => 'Conecte los pedidos de VTEX al ERP en tiempo real sin procesos manuales.',
				'solucao_pilares_2_titulo' => 'Actualice el inventario omnicanal',
				'solucao_pilares_2_desc'   => 'Mantenga tiendas físicas, marketplaces y canales digitales sincronizados.',
				'solucao_pilares_3_titulo' => 'Aproveche las APIs nativas de VTEX',
				'solucao_pilares_3_desc'   => 'Utilice la arquitectura API-first para integrar catálogo, pedidos y operaciones.',
				'solucao_casos_titulo'     => 'Automatice operaciones de e-commerce',
				'solucao_casos_1_titulo'   => 'Sincronice pedidos con el ERP',
				'solucao_casos_1_desc'     => 'Envíe los pedidos de VTEX al ERP automáticamente para acelerar el procesamiento.',
				'solucao_casos_2_titulo'   => 'Actualice el inventario entre canales',
				'solucao_casos_2_desc'     => 'Conecte tienda física, marketplace y e-commerce con el inventario sincronizado.',
				'solucao_casos_3_titulo'   => 'Integre pagos y finanzas',
				'solucao_casos_3_desc'     => 'Concilie las transacciones digitales con los sistemas financieros internos.',
				'solucao_casos_4_titulo'   => 'Automatice el ship from store',
				'solucao_casos_4_desc'     => 'Convierta las tiendas físicas en puntos de fulfillment para los pedidos digitales.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga los datos del e-commerce a disposición de agentes de IA para automatizar la atención y las operaciones.',
				'solucao_dif_titulo'       => 'Integraciones preparadas para escalar',
				'solucao_dif_corpo'        => 'Conecte VTEX usando APIs oficiales con autenticación segura para soportar operaciones digitales de alto volumen.',
				'solucao_dif_topico_1'     => 'Utilice VTEX IO y REST API',
				'solucao_dif_topico_2'     => 'Autentique con App Token',
				'solucao_dif_topico_3'     => 'Soporte picos de ventas',
				'solucao_plat_titulo'      => 'Unifique su ecosistema de comercio',
				'solucao_plat_corpo'       => 'Conecte VTEX, ERP, WMS y pagos en una única plataforma para mantener su operación sincronizada durante todo el recorrido de compra.',
				'solucao_plat_topico_1'    => 'Centralice las integraciones comerciales',
				'solucao_plat_topico_2'    => 'Absorba los picos operativos',
				'solucao_plat_topico_3'    => 'Mantenga los sistemas sincronizados',
				'solucao_acel_titulo'      => 'Comience con integraciones de e-commerce',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para conectar VTEX al ERP con pedidos, inventario y procesos fiscales estructurados.',
				'solucao_acel_topico_1'    => 'Conecte operaciones rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice flujos comerciales',
				'solucao_acel_topico_3'    => 'Acelere nuevas integraciones',
			)
		);
	}

	/**
	 * Shopify.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_shopify() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Shopify',
				'solucao_hero_titulo'      => 'Conecte su tienda Shopify al ERP sin depender de plugins genéricos',
				'solucao_hero_corpo'       => 'Integre Shopify, ERP, sistemas fiscales brasileños y WMS para automatizar pedidos, inventario y operaciones financieras con reglas adaptadas a su negocio.',
				'solucao_pilares_titulo'   => 'Escale su operación Shopify conectada',
				'solucao_pilares_1_titulo' => 'Cumpla las reglas fiscales brasileñas',
				'solucao_pilares_1_desc'   => 'Conecte Shopify al fiscal brasileño para automatizar la NF-e y los procesos tributarios específicos.',
				'solucao_pilares_2_titulo' => 'Sincronice inventarios multicanal',
				'solucao_pilares_2_desc'   => 'Mantenga el inventario actualizado entre Shopify, el ERP y los diferentes canales de venta.',
				'solucao_pilares_3_titulo' => 'Integre Shopify Plus',
				'solucao_pilares_3_desc'   => 'Soporte operaciones avanzadas de grandes marcas usando Shopify o Shopify Plus.',
				'solucao_casos_titulo'     => 'Automatice procesos de la tienda digital',
				'solucao_casos_1_titulo'   => 'Emita la NF-e automáticamente',
				'solucao_casos_1_desc'     => 'Conecte los pedidos de Shopify al sistema fiscal para generar documentos electrónicos.',
				'solucao_casos_2_titulo'   => 'Sincronice el inventario multicanal',
				'solucao_casos_2_desc'     => 'Actualice la disponibilidad entre el ERP, Shopify y los marketplaces automáticamente.',
				'solucao_casos_3_titulo'   => 'Concilie los pagos digitales',
				'solucao_casos_3_desc'     => 'Conecte las pasarelas de pago al área financiera para facilitar las conciliaciones.',
				'solucao_casos_4_titulo'   => 'Automatice las devoluciones',
				'solucao_casos_4_desc'     => 'Integre los procesos de retorno entre Shopify, el ERP y las operaciones internas.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga los datos de la tienda a disposición de agentes de IA para automatizar la atención y las operaciones.',
				'solucao_dif_titulo'       => 'Integraciones nativas para Shopify',
				'solucao_dif_corpo'        => 'Conecte su operación usando Shopify Admin API, GraphQL y Webhooks para sincronizar eventos en tiempo real con seguridad.',
				'solucao_dif_topico_1'     => 'Utilice la Shopify Admin API',
				'solucao_dif_topico_2'     => 'Capture eventos con Webhooks',
				'solucao_dif_topico_3'     => 'Conecte vía GraphQL',
				'solucao_plat_titulo'      => 'Supere los límites de las aplicaciones Shopify',
				'solucao_plat_corpo'       => 'Las apps de Shopify resuelven escenarios genéricos. Una plataforma de integración dedicada conecta reglas fiscales, múltiples ERP y operaciones complejas.',
				'solucao_plat_topico_1'    => 'Centralice las integraciones comerciales',
				'solucao_plat_topico_2'    => 'Adapte las reglas al negocio',
				'solucao_plat_topico_3'    => 'Reduzca la dependencia de terceros',
				'solucao_acel_titulo'      => 'Comience con una operación Shopify integrada',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para conectar Shopify al ERP y automatizar la emisión fiscal brasileña desde el inicio.',
				'solucao_acel_topico_1'    => 'Conecte el ERP rápidamente',
				'solucao_acel_topico_2'    => 'Automatice los procesos fiscales',
				'solucao_acel_topico_3'    => 'Acelere nuevas integraciones',
			)
		);
	}

	/**
	 * Magento / Adobe Commerce.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_magento() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Magento',
				'solucao_hero_titulo'      => 'Conecte Magento a su stack corporativo sin multiplicar extensiones',
				'solucao_hero_corpo'       => 'Integre Magento y Adobe Commerce al ERP, al PIM y a los sistemas de pago para escalar su operación digital sin sobrecargar el core de la plataforma con personalizaciones.',
				'solucao_pilares_titulo'   => 'Escale su comercio digital conectado',
				'solucao_pilares_1_titulo' => 'Integre cualquier arquitectura Magento',
				'solucao_pilares_1_desc'   => 'Conecte entornos on-premises y Adobe Commerce Cloud con una capa centralizada.',
				'solucao_pilares_2_titulo' => 'Sincronice catálogo y precios',
				'solucao_pilares_2_desc'   => 'Mantenga los productos y la información comercial actualizados a partir del PIM.',
				'solucao_pilares_3_titulo' => 'Conecte los pagos brasileños',
				'solucao_pilares_3_desc'   => 'Integre múltiples pasarelas de pago al checkout de la operación digital.',
				'solucao_casos_titulo'     => 'Automatice operaciones de comercio digital',
				'solucao_casos_1_titulo'   => 'Sincronice pedidos con el ERP',
				'solucao_casos_1_desc'     => 'Envíe los pedidos de Magento al ERP automáticamente para acelerar el procesamiento.',
				'solucao_casos_2_titulo'   => 'Centralice el catálogo vía PIM',
				'solucao_casos_2_desc'     => 'Actualice productos y precios en Magento a partir de una fuente única.',
				'solucao_casos_3_titulo'   => 'Concilie pagos automáticamente',
				'solucao_casos_3_desc'     => 'Integre pasarelas y antifraude al área financiera para reducir divergencias.',
				'solucao_casos_4_titulo'   => 'Automatice las devoluciones',
				'solucao_casos_4_desc'     => 'Conecte los procesos de retorno y la logística inversa a los sistemas internos.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga los datos del e-commerce a disposición de agentes para automatizar la atención y las operaciones.',
				'solucao_dif_titulo'       => 'Integraciones seguras para Magento',
				'solucao_dif_corpo'        => 'Conecte Magento usando REST y GraphQL API con tokens de integración por alcance para proteger cada acceso.',
				'solucao_dif_topico_1'     => 'Utilice REST y GraphQL API',
				'solucao_dif_topico_2'     => 'Controle los accesos por alcance',
				'solucao_dif_topico_3'     => 'Proteja las integraciones corporativas',
				'solucao_plat_titulo'      => 'Evolucione sin depender de extensiones',
				'solucao_plat_corpo'       => 'Una capa externa de integración reduce las personalizaciones en Magento, facilita las actualizaciones y conecta los sistemas corporativos con más flexibilidad.',
				'solucao_plat_topico_1'    => 'Centralice las integraciones externas',
				'solucao_plat_topico_2'    => 'Reduzca los cambios en el core',
				'solucao_plat_topico_3'    => 'Simplifique las actualizaciones futuras',
				'solucao_acel_titulo'      => 'Comience con el comercio integrado',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para conectar Magento o Adobe Commerce al ERP y al PIM con flujos estructurados.',
				'solucao_acel_topico_1'    => 'Conecte sistemas rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice integraciones comerciales',
				'solucao_acel_topico_3'    => 'Acelere nuevos proyectos',
			)
		);
	}

	/**
	 * OnBlox (WMS/TMS).
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_onblox() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su onblox',
				'solucao_hero_titulo'      => 'Conecte WMS y TMS al ERP y a las transportadoras en tiempo real',
				'solucao_hero_corpo'       => 'Integre OnBlox a los ERP, al e-commerce y a las aplicaciones de rastreo para sincronizar inventario, operaciones logísticas y gestión de flota sin procesos manuales.',
				'solucao_pilares_titulo'   => 'Conecte toda su operación logística',
				'solucao_pilares_1_titulo' => 'Sincronice el inventario automáticamente',
				'solucao_pilares_1_desc'   => 'Mantenga el inventario alineado entre WMS, ERP y canales de venta en tiempo real.',
				'solucao_pilares_2_titulo' => 'Integre la gestión de flota',
				'solucao_pilares_2_desc'   => 'Conecte mantenimiento, documentos y licencias a los sistemas financieros.',
				'solucao_pilares_3_titulo' => 'Acelere las implantaciones logísticas',
				'solucao_pilares_3_desc'   => 'Reduzca el tiempo de integración con flujos preparados para operaciones de logística.',
				'solucao_casos_titulo'     => 'Automatice procesos logísticos críticos',
				'solucao_casos_1_titulo'   => 'Sincronice el inventario con el ERP',
				'solucao_casos_1_desc'     => 'Conecte OnBlox al ERP y a los marketplaces para actualizar la disponibilidad automáticamente.',
				'solucao_casos_2_titulo'   => 'Direccione pedidos automáticamente',
				'solucao_casos_2_desc'     => 'Enrute los pedidos al centro de distribución más adecuado.',
				'solucao_casos_3_titulo'   => 'Conecte el rastreo de flota',
				'solucao_casos_3_desc'     => 'Integre transportadoras y aplicaciones de rastreo al ecosistema logístico.',
				'solucao_casos_4_titulo'   => 'Actualice las expediciones en tiempo real',
				'solucao_casos_4_desc'     => 'Envíe el estado de preparación y envío directamente al ERP.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga los datos logísticos a disposición de agentes para automatizar la atención y las operaciones.',
				'solucao_dif_titulo'       => 'Integraciones preparadas para alta operación',
				'solucao_dif_corpo'        => 'Conecte múltiples colectores y dispositivos móviles con alto volumen de datos para operaciones de almacén y flota.',
				'solucao_dif_topico_1'     => 'Soporte alto volumen operativo',
				'solucao_dif_topico_2'     => 'Conecte múltiples dispositivos móviles',
				'solucao_dif_topico_3'     => 'Mantenga los datos sincronizados',
				'solucao_plat_titulo'      => 'Unifique logística y sistemas corporativos',
				'solucao_plat_corpo'       => 'Conecte almacén, flota, ERP y finanzas en una única plataforma para eliminar planillas y procesos manuales.',
				'solucao_plat_topico_1'    => 'Centralice los datos logísticos',
				'solucao_plat_topico_2'    => 'Conecte las operaciones al área financiera',
				'solucao_plat_topico_3'    => 'Elimine las exportaciones manuales',
				'solucao_acel_titulo'      => 'Comience con la logística integrada',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para conectar OnBlox al ERP y acelerar la automatización de los procesos logísticos.',
				'solucao_acel_topico_1'    => 'Conecte el WMS rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice flujos logísticos',
				'solucao_acel_topico_3'    => 'Acelere nuevas integraciones',
			)
		);
	}

	/**
	 * Narwal (Comex).
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_narwal() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Narwal',
				'solucao_hero_titulo'      => 'Conecte el comercio exterior al ERP, del pedido al despacho aduanero',
				'solucao_hero_corpo'       => 'Integre Narwal al ERP financiero y a los organismos oficiales de comercio exterior para automatizar importaciones, exportaciones y costos operativos sin procesos manuales.',
				'solucao_pilares_titulo'   => 'Conecte toda la operación de comercio exterior',
				'solucao_pilares_1_titulo' => 'Integre pedidos internacionales',
				'solucao_pilares_1_desc'   => 'Conecte las compras y ventas internacionales de Narwal directamente al ERP corporativo.',
				'solucao_pilares_2_titulo' => 'Sincronice los canales oficiales',
				'solucao_pilares_2_desc'   => 'Integre Siscomex, Siscarga, Mantra y otros entornos de comercio exterior.',
				'solucao_pilares_3_titulo' => 'Automatice los costos de importación',
				'solucao_pilares_3_desc'   => 'Actualice fletes, despachos aduaneros y gastos directamente en el área financiera.',
				'solucao_casos_titulo'     => 'Automatice procesos de comercio exterior',
				'solucao_casos_1_titulo'   => 'Sincronice los pedidos de importación',
				'solucao_casos_1_desc'     => 'Conecte los procesos de Narwal al ERP para eliminar los registros manuales.',
				'solucao_casos_2_titulo'   => 'Actualice los costos automáticamente',
				'solucao_casos_2_desc'     => 'Envíe fletes y gastos de despacho directamente al área financiera.',
				'solucao_casos_3_titulo'   => 'Consolide las operaciones de comex',
				'solucao_casos_3_desc'     => 'Centralice datos de diferentes filiales para análisis estratégicos.',
				'solucao_casos_4_titulo'   => 'Siga los embarques automáticamente',
				'solucao_casos_4_desc'     => 'Dispare alertas de ETD y ETA hacia los sistemas conectados.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga los datos de comercio exterior a disposición de agentes para automatizar procesos administrativos.',
				'solucao_dif_titulo'       => 'Integraciones seguras para el comercio exterior',
				'solucao_dif_corpo'        => 'Conecte Narwal mediante APIs dedicadas con auditoría de las etapas de importación y exportación para un mayor control operativo.',
				'solucao_dif_topico_1'     => 'Integre vía APIs dedicadas',
				'solucao_dif_topico_2'     => 'Audite las etapas del proceso',
				'solucao_dif_topico_3'     => 'Proteja las operaciones certificadas',
				'solucao_plat_titulo'      => 'Unifique el comex y la operación financiera',
				'solucao_plat_corpo'       => 'Conecte embarques, costos y asientos financieros para eliminar los controles manuales entre Narwal y el ERP.',
				'solucao_plat_topico_1'    => 'Centralice los datos de comercio exterior',
				'solucao_plat_topico_2'    => 'Automatice los asientos financieros',
				'solucao_plat_topico_3'    => 'Reduzca los controles en planillas',
				'solucao_acel_titulo'      => 'Comience con el comex integrado',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para conectar los procesos de Narwal al ERP financiero y acelerar su operación.',
				'solucao_acel_topico_1'    => 'Conecte procesos rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice flujos de importación',
				'solucao_acel_topico_3'    => 'Acelere las integraciones financieras',
			)
		);
	}

	/**
	 * Portal de API / MCP Server.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_portal_de_api() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'api y mcp server',
				'solucao_hero_titulo'      => 'Transforme cualquier sistema interno en API o herramienta de IA',
				'solucao_hero_corpo'       => 'Exponga sistemas como ERP, CRM, ITSM y bases de datos como APIs estandarizadas o servidores MCP listos para el consumo de aplicaciones, equipos y agentes de IA.',
				'solucao_pilares_titulo'   => 'Democratice el acceso a los sistemas internos',
				'solucao_pilares_1_titulo' => 'Publique APIs sin código adicional',
				'solucao_pilares_1_desc'   => 'Transforme los pipelines existentes en APIs REST documentadas y reutilizables por nuevos proyectos.',
				'solucao_pilares_2_titulo' => 'Conecte agentes de IA a los sistemas',
				'solucao_pilares_2_desc'   => 'Exponga procesos como herramientas MCP autenticadas para que los agentes consulten y ejecuten acciones.',
				'solucao_pilares_3_titulo' => 'Centralice la gobernanza de acceso',
				'solucao_pilares_3_desc'   => 'Controle consumidores, permisos y alcances de cada sistema disponible en el portal.',
				'solucao_casos_titulo'     => 'Amplíe el uso de los sistemas conectados',
				'solucao_casos_1_titulo'   => 'Cree APIs de sistemas corporativos',
				'solucao_casos_1_desc'     => 'Exponga Totvs, Sankhya o SAP como APIs únicas para consultas y operaciones reutilizables.',
				'solucao_casos_2_titulo'   => 'Conecte agentes de IA al ERP',
				'solucao_casos_2_desc'     => 'Permita que los agentes consulten el inventario y creen pedidos usando lenguaje natural.',
				'solucao_casos_3_titulo'   => 'Cree un catálogo interno de APIs',
				'solucao_casos_3_desc'     => 'Ayude a los equipos a descubrir y reutilizar integraciones existentes sin retrabajo.',
				'solucao_casos_4_titulo'   => 'Modernice los accesos heredados',
				'solucao_casos_4_desc'     => 'Exponga mainframes y ESB como APIs modernas sin revelar protocolos antiguos.',
				'solucao_casos_5_titulo'   => 'Controle los consumidores de APIs',
				'solucao_casos_5_desc'     => 'Gestione accesos, límites y auditorías por usuario, sistema o agente.',
				'solucao_dif_titulo'       => 'APIs seguras para humanos y agentes',
				'solucao_dif_corpo'        => 'Cada API o MCP Server publicado hereda la seguridad de la plataforma con autenticación, control de alcance y protección de datos sensibles.',
				'solucao_dif_topico_1'     => 'Proteja las APIs con autenticación por token',
				'solucao_dif_topico_2'     => 'Controle los alcances por consumidor',
				'solucao_dif_topico_3'     => 'Proteja los datos sensibles con guardrails',
				'solucao_plat_titulo'      => 'Unifique el acceso a todos los sistemas',
				'solucao_plat_corpo'       => 'Conecte una sola vez sus sistemas internos y reutilice esas capacidades como APIs o herramientas de IA sin reconstruir integraciones para cada proyecto.',
				'solucao_plat_topico_1'    => 'Centralice el acceso a los sistemas corporativos',
				'solucao_plat_topico_2'    => 'Reutilice las integraciones ya construidas',
				'solucao_plat_topico_3'    => 'Evite nuevos desarrollos redundantes',
				'solucao_acel_titulo'      => 'Transforme las integraciones existentes en APIs',
				'solucao_acel_corpo'       => 'Publique los pipelines ya construidos como endpoints documentados o herramientas MCP sin crear nuevos proyectos de desarrollo.',
				'solucao_acel_topico_1'    => 'Convierta pipelines rápidamente',
				'solucao_acel_topico_2'    => 'Aproveche las integraciones existentes',
				'solucao_acel_topico_3'    => 'Publique APIs en pocos clics',
			)
		);
	}

	/**
	 * Microsoft Teams.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_microsoft_teams() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Microsoft Teams',
				'solucao_hero_titulo'      => 'Convierta Microsoft Teams en un canal de acción para sus procesos',
				'solucao_hero_corpo'       => 'Conecte aprobaciones, notificaciones y agentes de IA a los sistemas internos para acelerar decisiones sin depender de correos ni procesos manuales.',
				'solucao_pilares_titulo'   => 'Lleve los procesos a donde trabajan los equipos',
				'solucao_pilares_1_titulo' => 'Apruebe procesos en Teams',
				'solucao_pilares_1_desc'   => 'Ejecute aprobaciones directamente en tarjetas adaptables.',
				'solucao_pilares_2_titulo' => 'Converse con los sistemas internos',
				'solucao_pilares_2_desc'   => 'Permita que los bots consulten datos corporativos en Teams.',
				'solucao_pilares_3_titulo' => 'Reduzca los intercambios manuales',
				'solucao_pilares_3_desc'   => 'Sustituya los correos por acciones automatizadas.',
				'solucao_casos_titulo'     => 'Automatice procesos dentro de Teams',
				'solucao_casos_1_titulo'   => 'Apruebe pedidos en Teams',
				'solucao_casos_1_desc'     => 'Envíe las aprobaciones de compras o vacaciones al ERP.',
				'solucao_casos_2_titulo'   => 'Alerte incidentes automáticamente',
				'solucao_casos_2_desc'     => 'Notifique a los equipos sobre eventos de ServiceNow o Freshservice.',
				'solucao_casos_3_titulo'   => 'Consulte sistemas con IA',
				'solucao_casos_3_desc'     => 'Permita que los bots consulten inventario y pedidos.',
				'solucao_casos_4_titulo'   => 'Monitoree eventos críticos',
				'solucao_casos_4_desc'     => 'Dispare alertas de SLA y de operaciones importantes.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga los datos corporativos a disposición de agentes de IA vía Teams mediante APIs gobernadas y servidores MCP.',
				'solucao_dif_titulo'       => 'Integraciones seguras con Microsoft Teams',
				'solucao_dif_corpo'        => 'Conecte Teams vía Microsoft Graph API y Bot Framework usando autenticación Azure AD con control por equipo y canal.',
				'solucao_dif_topico_1'     => 'Utilice la Microsoft Graph API.',
				'solucao_dif_topico_2'     => 'Autentique vía Azure AD.',
				'solucao_dif_topico_3'     => 'Controle los accesos por canal.',
				'solucao_plat_titulo'      => 'Conecte comunicación y operación',
				'solucao_plat_corpo'       => 'Centralice los eventos del negocio en una plataforma única para acercar los equipos a los sistemas corporativos.',
				'solucao_plat_topico_1'    => 'Integre los sistemas internos.',
				'solucao_plat_topico_2'    => 'Centralice las notificaciones operativas.',
				'solucao_plat_topico_3'    => 'Automatice acciones en Teams.',
				'solucao_acel_titulo'      => 'Comience con procesos conectados',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para transformar los procesos corporativos en aprobaciones y notificaciones dentro de Teams.',
				'solucao_acel_topico_1'    => 'Configure flujos rápidamente.',
				'solucao_acel_topico_2'    => 'Reutilice modelos aprobados.',
				'solucao_acel_topico_3'    => 'Acelere las decisiones operativas.',
			)
		);
	}

	/**
	 * Thomson Reuters Tax One.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_thomson_reuters_tax_one() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Tax One',
				'solucao_hero_titulo'      => 'Centralice la gestión tributaria y elimine los riesgos fiscales de su empresa',
				'solucao_hero_corpo'       => 'Conecte fácilmente el ecosistema de su empresa a Thomson Reuters Tax One. Unifique el cálculo de impuestos, simplifique las obligaciones accesorias y garantice seguridad fiscal.',
				'solucao_pilares_titulo'   => 'Centralice el cálculo fiscal de su operación',
				'solucao_pilares_1_titulo' => 'Centralice las reglas tributarias',
				'solucao_pilares_1_desc'   => 'Aplique el mismo motor de cálculo fiscal en todos los sistemas que generan documentos en la empresa.',
				'solucao_pilares_2_titulo' => 'Reduzca las divergencias fiscales',
				'solucao_pilares_2_desc'   => 'Mantenga el ERP, el e-commerce y la facturación alineados con cálculos tributarios consistentes.',
				'solucao_pilares_3_titulo' => 'Audite cada cálculo realizado',
				'solucao_pilares_3_desc'   => 'Tenga trazabilidad completa de todas las llamadas hechas al motor fiscal.',
				'solucao_casos_titulo'     => 'Automatice los principales procesos fiscales',
				'solucao_casos_1_titulo'   => 'Calcule impuestos en el checkout',
				'solucao_casos_1_desc'     => 'Consulte el motor fiscal en tiempo real durante las compras en el e-commerce para aplicar los tributos correctamente.',
				'solucao_casos_2_titulo'   => 'Conecte múltiples ERP',
				'solucao_casos_2_desc'     => 'Centralice el cálculo fiscal entre SAP, Totvs, Dynamics y otros ERP de la organización.',
				'solucao_casos_3_titulo'   => 'Reprocese documentos fiscales',
				'solucao_casos_3_desc'     => 'Ejecute cálculos por lote para conciliar documentos y corregir inconsistencias tributarias.',
				'solucao_casos_4_titulo'   => 'Actualice las reglas fiscales automáticamente',
				'solucao_casos_4_desc'     => 'Sincronice los cambios tributarios entre el motor fiscal y los sistemas de origen.',
				'solucao_casos_5_titulo'   => 'Centralice las auditorías fiscales',
				'solucao_casos_5_desc'     => 'Siga todas las consultas al motor fiscal en una única traza de auditoría.',
				'solucao_dif_titulo'       => 'Audite cada cálculo con seguridad fiscal',
				'solucao_dif_corpo'        => 'Centralice todas las llamadas al motor fiscal con control de acceso por sistema de origen y trazabilidad completa para el compliance tributario.',
				'solucao_dif_topico_1'     => 'Registre todas las llamadas fiscales',
				'solucao_dif_topico_2'     => 'Controle los accesos por sistema de origen',
				'solucao_dif_topico_3'     => 'Garantice trazabilidad para las auditorías',
				'solucao_plat_titulo'      => 'Unifique los cálculos fiscales en una plataforma',
				'solucao_plat_corpo'       => 'Las empresas con múltiples ERP necesitan garantizar la misma regla tributaria en todos los puntos de emisión. Centralice las conexiones y reduzca los riesgos de cálculos inconsistentes.',
				'solucao_plat_topico_1'    => 'Centralice las reglas entre diferentes ERP',
				'solucao_plat_topico_2'    => 'Estandarice los cálculos entre unidades',
				'solucao_plat_topico_3'    => 'Reduzca el riesgo de sanciones fiscales',
				'solucao_acel_titulo'      => 'Comience con un modelo fiscal listo',
				'solucao_acel_corpo'       => 'Utilice una plantilla de cálculo tributario centralizado para conectar checkout, ERP y motor fiscal con más velocidad.',
				'solucao_acel_topico_1'    => 'Conecte múltiples ERP rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice flujos fiscales validados',
				'solucao_acel_topico_3'    => 'Acelere nuevas integraciones tributarias',
			)
		);
	}

	/**
	 * Bionexo.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_bionexo() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Bionexo',
				'solucao_hero_titulo'      => 'Conecte el mayor marketplace B2B de salud a su ERP',
				'solucao_hero_corpo'       => 'Integre compras, contratos y facturación de Bionexo al ERP financiero y al HIS de la institución para eliminar retrabajo y garantizar datos sincronizados en toda la operación hospitalaria.',
				'solucao_pilares_titulo'   => 'Conecte las compras hospitalarias a los sistemas internos',
				'solucao_pilares_1_titulo' => 'Sincronice pedidos automáticamente',
				'solucao_pilares_1_desc'   => 'Conecte los pedidos de Bionexo al ERP financiero y al inventario sin procesos manuales.',
				'solucao_pilares_2_titulo' => 'Centralice las negociaciones con proveedores',
				'solucao_pilares_2_desc'   => 'Mantenga contratos, precios y condiciones comerciales sincronizados con los sistemas internos.',
				'solucao_pilares_3_titulo' => 'Reduzca el retrabajo operativo',
				'solucao_pilares_3_desc'   => 'Elimine las digitaciones manuales entre marketplace, ERP y sistemas hospitalarios.',
				'solucao_casos_titulo'     => 'Automatice procesos de compras hospitalarias',
				'solucao_casos_1_titulo'   => 'Sincronice las órdenes de compra',
				'solucao_casos_1_desc'     => 'Envíe los pedidos de Bionexo directamente al ERP hospitalario sin intervención manual.',
				'solucao_casos_2_titulo'   => 'Concilie facturas automáticamente',
				'solucao_casos_2_desc'     => 'Relacione las facturas recibidas por el marketplace con los registros financieros internos.',
				'solucao_casos_3_titulo'   => 'Actualice contratos y precios',
				'solucao_casos_3_desc'     => 'Sincronice las negociaciones realizadas con los proveedores en el sistema de suministros.',
				'solucao_casos_4_titulo'   => 'Consolide los datos de compras',
				'solucao_casos_4_desc'     => 'Centralice la información para análisis de costo y eficiencia hospitalaria.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga los tickets y las macros de Zendesk como herramientas para agentes inteligentes.',
				'solucao_dif_titulo'       => 'Integraciones seguras para la salud',
				'solucao_dif_corpo'        => 'Conecte Bionexo usando APIs oficiales con autenticación segura y protección de datos conforme a los requisitos de la LGPD.',
				'solucao_dif_topico_1'     => 'Utilice la API REST de Bionexo',
				'solucao_dif_topico_2'     => 'Proteja los accesos con tokens',
				'solucao_dif_topico_3'     => 'Proteja los datos conforme a la LGPD',
				'solucao_plat_titulo'      => 'Unifique compras y sistemas hospitalarios',
				'solucao_plat_corpo'       => 'Conecte marketplace, HIS y ERP financiero en una única plataforma para eliminar planillas y cerrar el ciclo operativo.',
				'solucao_plat_topico_1'    => 'Centralice los flujos de compras',
				'solucao_plat_topico_2'    => 'Conecte múltiples sistemas hospitalarios',
				'solucao_plat_topico_3'    => 'Elimine los procesos manuales',
				'solucao_acel_titulo'      => 'Comience con las compras integradas',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para conectar los pedidos de Bionexo al ERP hospitalario y acelerar la automatización.',
				'solucao_acel_topico_1'    => 'Conecte pedidos rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice flujos hospitalarios',
				'solucao_acel_topico_3'    => 'Adapte los procesos internos',
			)
		);
	}

	/**
	 * Tasy.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_tasy() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Tasy',
				'solucao_hero_titulo'      => 'Conecte el núcleo de la operación hospitalaria a todo el ecosistema',
				'solucao_hero_corpo'       => 'Integre Tasy a laboratorios, aseguradoras de salud, ERP corporativo y agentes de IA para conectar datos asistenciales y financieros sin alterar el core hospitalario.',
				'solucao_pilares_titulo'   => 'Amplíe el valor de los datos de Tasy',
				'solucao_pilares_1_titulo' => 'Aproveche las APIs estandarizadas de Tasy',
				'solucao_pilares_1_desc'   => 'Utilice la Tasy Open API para crear integraciones documentadas, seguras y escalables.',
				'solucao_pilares_2_titulo' => 'Conecte la facturación TISS',
				'solucao_pilares_2_desc'   => 'Integre Tasy a las aseguradoras de salud para automatizar los procesos de facturación.',
				'solucao_pilares_3_titulo' => 'Centralice los datos hospitalarios',
				'solucao_pilares_3_desc'   => 'Unifique la información clínica y financiera para análisis sin alterar el sistema principal.',
				'solucao_casos_titulo'     => 'Automatice procesos hospitalarios críticos',
				'solucao_casos_1_titulo'   => 'Automatice la facturación TISS',
				'solucao_casos_1_desc'     => 'Conecte Tasy a las aseguradoras de salud para agilizar los procesos de facturación.',
				'solucao_casos_2_titulo'   => 'Sincronice los resultados de laboratorio',
				'solucao_casos_2_desc'     => 'Integre los sistemas LIS a la historia clínica para poner los resultados a disposición automáticamente.',
				'solucao_casos_3_titulo'   => 'Concilie los datos financieros',
				'solucao_casos_3_desc'     => 'Conecte Tasy y el ERP corporativo para consolidar la información financiera.',
				'solucao_casos_4_titulo'   => 'Consolide redes hospitalarias',
				'solucao_casos_4_desc'     => 'Estandarice las integraciones entre múltiples unidades y sistemas hospitalarios.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga los datos asistenciales a disposición de agentes administrativos sin exponer el core clínico.',
				'solucao_dif_titulo'       => 'Integraciones seguras para datos hospitalarios',
				'solucao_dif_corpo'        => 'Utilice la Tasy Open API con autenticación, cifrado y control de acceso para proteger la información sensible de salud.',
				'solucao_dif_topico_1'     => 'Utilice las APIs oficiales de Tasy',
				'solucao_dif_topico_2'     => 'Proteja los datos sensibles de salud',
				'solucao_dif_topico_3'     => 'Controle los accesos conforme a la LGPD',
				'solucao_plat_titulo'      => 'Unifique operaciones hospitalarias complejas',
				'solucao_plat_corpo'       => 'Cree una capa única de integración para conectar múltiples unidades Tasy y sistemas hospitalarios sin personalizar el core asistencial.',
				'solucao_plat_topico_1'    => 'Estandarice las integraciones entre unidades',
				'solucao_plat_topico_2'    => 'Centralice la facturación hospitalaria',
				'solucao_plat_topico_3'    => 'Simplifique la consolidación financiera',
				'solucao_acel_titulo'      => 'Comience con integraciones hospitalarias listas',
				'solucao_acel_corpo'       => 'Utilice un modelo estructurado para conectar la Tasy Open API al ERP financiero y a las aseguradoras de salud.',
				'solucao_acel_topico_1'    => 'Conecte sistemas rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice flujos hospitalarios',
				'solucao_acel_topico_3'    => 'Acelere nuevas integraciones',
			)
		);
	}

	/**
	 * MV.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_mv() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su SOUL MV',
				'solucao_hero_titulo'      => 'Conecte SOUL MV al ecosistema completo del hospital digital',
				'solucao_hero_corpo'       => 'Integre MV al laboratorio, al diagnóstico por imagen, al ERP corporativo y a la facturación para conectar los procesos asistenciales, administrativos y financieros en una única operación.',
				'solucao_pilares_titulo'   => 'Amplíe la conectividad de SOUL MV',
				'solucao_pilares_1_titulo' => 'Integre los sistemas asistenciales',
				'solucao_pilares_1_desc'   => 'Conecte RIS, PACS, LIS y portales de exámenes a MV con intercambio de datos en tiempo real.',
				'solucao_pilares_2_titulo' => 'Reduzca las glosas hospitalarias',
				'solucao_pilares_2_desc'   => 'Valide la información de las órdenes de examen antes de la ejecución y evite inconsistencias.',
				'solucao_pilares_3_titulo' => 'Centralice los datos financieros',
				'solucao_pilares_3_desc'   => 'Consolide la información entre unidades hospitalarias y sistemas corporativos.',
				'solucao_casos_titulo'     => 'Automatice procesos hospitalarios esenciales',
				'solucao_casos_1_titulo'   => 'Integre RIS y PACS a MV',
				'solucao_casos_1_desc'     => 'Consulte alergias e historial clínico durante los exámenes sin cambiar de sistema.',
				'solucao_casos_2_titulo'   => 'Automatice la facturación hospitalaria',
				'solucao_casos_2_desc'     => 'Sincronice la información de facturación y glosas con las aseguradoras de salud.',
				'solucao_casos_3_titulo'   => 'Concilie las finanzas entre unidades',
				'solucao_casos_3_desc'     => 'Conecte MV y el ERP corporativo para consolidar los resultados financieros.',
				'solucao_casos_4_titulo'   => 'Automatice los accesos internos',
				'solucao_casos_4_desc'     => 'Aprovisione accesos en los sistemas de apoyo a partir de eventos de MV.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga los datos asistenciales a disposición de agentes administrativos sin exponer el core clínico.',
				'solucao_dif_titulo'       => 'Integraciones seguras para la salud',
				'solucao_dif_corpo'        => 'Conecte MV usando APIs con traza de auditoría y controles de seguridad para proteger los datos clínicos conforme a la LGPD.',
				'solucao_dif_topico_1'     => 'Utilice las APIs del sistema MV',
				'solucao_dif_topico_2'     => 'Audite las integraciones hospitalarias',
				'solucao_dif_topico_3'     => 'Proteja los datos clínicos sensibles',
				'solucao_plat_titulo'      => 'Unifique los sistemas de redes hospitalarias',
				'solucao_plat_corpo'       => 'Centralice las integraciones entre diferentes HIS, ERP y sistemas asistenciales para evitar proyectos duplicados en cada unidad.',
				'solucao_plat_topico_1'    => 'Conecte diferentes plataformas hospitalarias',
				'solucao_plat_topico_2'    => 'Estandarice las integraciones entre unidades',
				'solucao_plat_topico_3'    => 'Reduzca los esfuerzos de mantenimiento',
				'solucao_acel_titulo'      => 'Comience con integraciones hospitalarias listas',
				'solucao_acel_corpo'       => 'Utilice un modelo estructurado para conectar MV, RIS/PACS, LIS y el ERP financiero con más velocidad.',
				'solucao_acel_topico_1'    => 'Conecte sistemas rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice flujos hospitalarios',
				'solucao_acel_topico_3'    => 'Acelere nuevas automatizaciones',
			)
		);
	}
}
