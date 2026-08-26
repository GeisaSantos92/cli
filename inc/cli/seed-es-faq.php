<?php
/**
 * Seed — texto em espanhol das FAQ das landings de solução.
 *
 * `solucao_faq_itens` é um relationship com `cli_faq`, e o CPT é traduzível:
 * sem a versão em espanhol de cada pergunta, o Polylang filtra a lista por
 * idioma e a seção de FAQ **some** da landing, em silêncio. Por isso estas
 * traduções rodam antes das landings — é `traduzir_referencia()` que troca os
 * IDs do português pelos do espanhol.
 *
 * As FAQ gerais da home ficam em `seed-es-cpts.php`.
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
 * Conteúdo em espanhol das FAQ de solução.
 */
trait Cliconnect_Seed_Es_Faq {

	/**
	 * FAQ das soluções: slug do seed => [pergunta, resposta].
	 *
	 * Agrupadas por solução, na mesma ordem em que aparecem na landing.
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function faq_solucoes_es() {
		return array_merge(
			$this->faq_es_bloco_1(),
			$this->faq_es_bloco_2(),
			$this->faq_es_bloco_3(),
			$this->faq_es_bloco_4(),
			$this->faq_es_bloco_5(),
			$this->faq_es_bloco_6(),
			$this->faq_es_bloco_7(),
			$this->faq_es_bloco_8(),
			$this->faq_es_bloco_9(),
			$this->faq_es_bloco_10(),
			$this->faq_es_bloco_11()
		);
	}

	/**
	 * Salesforce, SAP, TOTVS Protheus, TOTVS Datasul, TOTVS Winthor e TOTVS Logix.
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function faq_es_bloco_1() {
		return array(
			// Salesforce.
			'sf-apis' => array(
				'¿Qué APIs de Salesforce son compatibles?',
				'<p>CLI Connect es compatible con las principales APIs REST de Salesforce, incluidas la REST API, la Bulk API, la Streaming API (Push Topics) y la Subscription API (Platform Events). La elección de la API se hace según el volumen de datos y la necesidad de eventos en tiempo real de cada integración.</p>',
			),
			'sf-firewall' => array(
				'¿Es posible integrar Salesforce con un ERP on-premises sin abrir puertos en el firewall?',
				'<p>Sí. CLI Connect utiliza el Boomi Atom, un agente de integración instalado dentro de la red corporativa que se comunica de salida con la plataforma en la nube. No es necesario abrir puertos de entrada en el firewall, lo que preserva por completo la seguridad de la infraestructura interna.</p>',
			),
			'sf-mulesoft' => array(
				'¿Cómo se compara CLI Connect con MuleSoft?',
				'<p>CLI Connect utiliza Boomi como plataforma de integración, que ofrece una interfaz low-code, un modelo de precios más predecible y un menor costo de operación en comparación con MuleSoft. Además, el modelo gestionado de CLI Connect incluye la operación, el monitoreo y el soporte continuo, lo que elimina la necesidad de un equipo interno dedicado a la plataforma.</p>',
			),
			'sf-atualizacoes' => array(
				'¿Las integraciones siguen funcionando después de las actualizaciones de Salesforce?',
				'<p>Sí. Salesforce mantiene la retrocompatibilidad en sus APIs versionadas, y CLI Connect acompaña cada release para garantizar que las integraciones se mantengan estables. El equipo de monitoreo valida los flujos críticos en cada actualización y activa el soporte preventivo cuando es necesario.</p>',
			),
			'sf-produtos' => array(
				'¿Qué productos de Salesforce se pueden integrar?',
				'<p>Es posible integrar Sales Cloud, Marketing Cloud, Service Cloud, Revenue Cloud, Data Cloud y las demás soluciones de la plataforma Salesforce utilizando la misma arquitectura de integración.</p>',
			),

			// SAP.
			'sap-integracao' => array(
				'¿Cómo se integra CLI Connect con SAP?',
				'<p>CLI Connect utiliza un Add-on nativo homologado por SAP, además de conectores RFC, BAPI, IDoc, OData, REST y SOAP. Este enfoque garantiza compatibilidad con las principales versiones de SAP ECC y S/4HANA, preserva la arquitectura Clean Core y elimina la necesidad de modificaciones no soportadas en el sistema.</p>',
			),
			'sap-versoes' => array(
				'¿Qué versiones de SAP son compatibles?',
				'<p>La plataforma es compatible con SAP ECC (incluidas las versiones 6.0 en adelante) y SAP S/4HANA (Cloud y On-Premises). La elección del conector adecuado — RFC/BAPI para procesos legados u OData/APIs REST para S/4HANA — se define durante la etapa de arquitectura de la integración.</p>',
			),
			'sap-implantacao' => array(
				'¿Cuánto tiempo lleva una implementación?',
				'<p>El tiempo varía según la complejidad del escenario, pero los proyectos con aceleradores listos pueden entrar en producción en pocas semanas. CLI Connect ofrece plantillas preconstruidas para los escenarios más comunes — como Order-to-Cash, Procure-to-Pay y migración ECC → S/4HANA — lo que reduce significativamente el plazo de implementación.</p>',
			),
			'sap-atualizacoes' => array(
				'¿Las actualizaciones de SAP afectan a las integraciones?',
				'<p>Las integraciones desarrolladas con Add-on nativo y APIs soportadas por SAP acompañan el ciclo de actualizaciones sin roturas. CLI Connect monitorea cada release y valida los flujos críticos de forma preventiva, activando el soporte cuando algún ajuste es necesario después de una actualización.</p>',
			),
			'sap-cleancore' => array(
				'¿Cómo preservar el Clean Core durante la migración?',
				'<p>La principal forma de garantizar el Clean Core es evitar modificaciones directas en el núcleo de SAP. CLI Connect utiliza APIs y extensiones soportadas por la propia SAP — Add-on homologado, OData, RFC y BAPI — y mantiene toda la lógica de integración en la plataforma iPaaS, fuera del core del ERP. Esto facilita las actualizaciones futuras y reduce el riesgo operativo durante la migración a S/4HANA.</p>',
			),

			// TOTVS Protheus.
			'totvs-sem-vpn' => array(
				'¿Cómo integra CLI Connect Protheus sin VPN?',
				'<p>CLI Connect utiliza un agente (Boomi Atom) instalado dentro de la red corporativa que realiza la comunicación outbound con la plataforma en la nube. No es necesario abrir puertos en el firewall ni configurar VPN: Protheus permanece protegido mientras las integraciones operan con normalidad.</p>',
			),
			'totvs-advpl' => array(
				'¿Es necesario desarrollar rutinas AdvPL para cada integración?',
				'<p>No. CLI Connect utiliza ExecAuto llamando a las MATA Protheus Standard y APIs REST nativas, lo que elimina la necesidad de desarrollo personalizado en AdvPL en la mayoría de los escenarios. Esto reduce la dependencia del entorno Protheus y hace que las integraciones sean más estables y fáciles de mantener.</p>',
			),
			'totvs-legados' => array(
				'¿La solución funciona en entornos Protheus legados?',
				'<p>Sí. CLI Connect es compatible con versiones legadas de Protheus, utilizando conectores compatibles con las APIs disponibles en cada entorno. La arquitectura de integración se adapta a lo que existe en su entorno, sin exigir una actualización inmediata del ERP.</p>',
			),
			'totvs-filiais' => array(
				'¿Cómo funciona la integración entre varias sucursales?',
				'<p>CLI Connect centraliza las integraciones de todas las sucursales en una única plataforma, sincronizando inventarios, pedidos y catálogos entre las unidades de forma automática. Esto elimina divergencias operativas y garantiza que todas las sucursales trabajen con la misma información en tiempo real.</p>',
			),
			'totvs-prazo' => array(
				'¿Cuánto tiempo lleva poner una integración en producción?',
				'<p>Con los aceleradores listos de CLI Connect, la mayoría de las integraciones entre Protheus y otros sistemas entra en producción en pocos días. Los escenarios más complejos pasan por un relevamiento rápido antes de la implementación, pero el uso de plantillas preconfiguradas reduce significativamente el plazo frente a los proyectos personalizados.</p>',
			),

			// TOTVS Datasul.
			'datasul-banco-direto' => array(
				'¿CLI Connect accede directamente a la base de datos de Datasul?',
				'<p>No. CLI Connect utiliza el protocolo Progress/EMS para comunicarse con Datasul de forma nativa y segura, sin acceso directo a la base de datos. El procesamiento ocurre dentro de la infraestructura de la empresa, lo que preserva la integridad de los datos y las políticas de seguridad corporativas.</p>',
			),
			'datasul-versoes' => array(
				'¿Es posible integrar diferentes versiones de Datasul?',
				'<p>Sí. CLI Connect es compatible con múltiples versiones de Datasul y adapta los conectores a las APIs disponibles en cada entorno. No es necesario actualizar el ERP para empezar a integrar: la plataforma se ajusta a lo que ya existe en su instalación actual.</p>',
			),
			'datasul-sap' => array(
				'¿Puedo integrar Datasul y SAP en la misma empresa?',
				'<p>Sí. CLI Connect es una plataforma unificada que permite integrar diferentes ERPs simultáneamente, incluidos Datasul y SAP. Las empresas que crecieron por adquisiciones y operan múltiples sistemas pueden centralizar todas las integraciones en una única capa de gobernanza.</p>',
			),
			'datasul-mes' => array(
				'¿Cómo integrar Datasul con el MES?',
				'<p>CLI Connect ofrece conectores y aceleradores listos para sincronizar órdenes de producción entre Datasul y sistemas MES. La integración actualiza automáticamente el estado de las órdenes durante toda la operación industrial, lo que elimina el retrabajo manual y reduce errores en la planta.</p>',
			),
			'datasul-bi-ia' => array(
				'¿Los datos se pueden utilizar en BI o Inteligencia Artificial?',
				'<p>Sí. CLI Connect pone los datos de Datasul a disposición de plataformas de BI como Power BI y Tableau, además de permitir que agentes de IA consulten información del ERP con seguridad mediante integraciones gobernadas. Así, los datos operativos se convierten en insumo para el análisis y la automatización inteligente.</p>',
			),

			// TOTVS Winthor.
			'winthor-volume' => array(
				'¿La plataforma soporta operaciones con gran volumen de pedidos?',
				'<p>Sí. CLI Connect utiliza conectores dedicados a las rutinas automáticas y webservices de Winthor, diseñados para soportar el alto volumen de pedidos típico de distribuidores y mayoristas. La plataforma procesa grandes lotes sin comprometer la estabilidad del ERP.</p>',
			),
			'winthor-forca-vendas' => array(
				'¿Es posible integrar varias aplicaciones de fuerza de ventas?',
				'<p>Sí. CLI Connect conecta simultáneamente diferentes aplicaciones de preventa y fuerza de ventas con Winthor, y centraliza la recepción de pedidos en una única capa de integración. Esto elimina la digitación manual y garantiza que todos los canales alimenten el ERP de forma automatizada y estandarizada.</p>',
			),
			'winthor-transportadoras' => array(
				'¿Cómo funciona la integración con los transportistas?',
				'<p>CLI Connect automatiza el envío de los datos de entrega a los transportistas asociados, incluidas la generación de etiquetas, la transmisión del manifiesto y la recepción de eventos de rastreo. El estado de las entregas se actualiza automáticamente en Winthor, manteniendo informados a clientes y operación sin intervención manual.</p>',
			),

			// TOTVS Logix.
			'logix-overselling' => array(
				'¿Cómo evitar el overselling entre múltiples canales de venta?',
				'<p>CLI Connect sincroniza el saldo de inventario de Logix en tiempo real con todos los canales conectados — marketplaces, e-commerce y tienda física. Cada vez que se confirma una venta, la plataforma actualiza automáticamente los demás canales, lo que elimina el riesgo de vender un producto que ya no está disponible.</p>',
			),
			'logix-cds' => array(
				'¿CLI Connect soporta múltiples centros de distribución?',
				'<p>Sí. La plataforma permite mapear reglas de enrutamiento por región, tipo de producto o capacidad de inventario, y dirige cada pedido al centro de distribución correcto de forma automática. Los movimientos de cada CD se reflejan de manera consolidada en Logix.</p>',
			),
			'logix-transportadoras' => array(
				'¿Cómo funciona la integración con los transportistas?',
				'<p>CLI Connect automatiza el envío de los datos del pedido al transportista después de la expedición en Logix, recibe el código de rastreo y actualiza el estado de entrega en el ERP y en los canales de venta. El proceso elimina los registros manuales y reduce los errores de seguimiento logístico.</p>',
			),
		);
	}

	/**
	 * Senior, Sankhya, Salesforce Sales Cloud, Dynamics 365,
	 * Salesforce Service Cloud e as perguntas gerais da home.
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function faq_es_bloco_2() {
		return array(
			// Senior.
			'senior-dados-sensiveis' => array(
				'¿Cómo protege CLI Connect los datos sensibles de Senior?',
				'<p>La plataforma aplica enmascaramiento automático de campos sensibles — como documento de identidad, salario y datos bancarios — antes de que la información circule entre sistemas. Todo el proceso queda registrado en un log de auditoría, lo que garantiza trazabilidad y cumplimiento de la LGPD.</p>',
			),
			'senior-multiplas-filiais' => array(
				'¿Es posible integrar varias sucursales que usan bases Senior diferentes?',
				'<p>Sí. CLI Connect soporta escenarios multiempresa y multibase, y permite integrar sucursales con instancias Senior distintas en la misma plataforma. Las reglas de enrutamiento y el mapeo de datos se configuran por empresa, lo que garantiza aislamiento y gobernanza centralizada.</p>',
			),
			'senior-tempo-implantacao' => array(
				'¿Cuánto tiempo lleva automatizar el flujo de ingreso y salida de personal?',
				'<p>Con los aceleradores JML (Joiner, Mover, Leaver) de CLI Connect, los proyectos de ingreso y salida de personal pueden implementarse en semanas. Las plantillas preconfiguradas reducen el esfuerzo de desarrollo y permiten adaptaciones rápidas a las reglas específicas de cada empresa.</p>',
			),

			// Sankhya.
			'sankhya-banco-direto' => array(
				'¿CLI Connect accede directamente a la base de datos de Sankhya?',
				'<p>No. CLI Connect utiliza exclusivamente la API Gateway oficial de Sankhya para todas las operaciones. No hay acceso directo a la base de datos, lo que preserva la integridad de las reglas de negocio y la gobernanza de la plataforma.</p>',
			),
			'sankhya-autenticacao' => array(
				'¿Cómo funciona la autenticación en las integraciones con Sankhya?',
				'<p>La autenticación se realiza mediante el usuario de integración nativo de Sankhya, con credenciales configuradas en la plataforma de CLI Connect. Cada integración opera con los permisos asignados explícitamente a ese usuario, lo que mantiene la trazabilidad y el control de acceso.</p>',
			),
			'sankhya-permissoes' => array(
				'¿Es posible limitar qué datos puede acceder cada integración?',
				'<p>Sí. Los permisos se definen en el propio Sankhya por entidad y operación (lectura, escritura, eliminación). CLI Connect respeta esas configuraciones y garantiza que cada integración acceda únicamente a los datos autorizados por el administrador del ERP.</p>',
			),

			// Salesforce Sales Cloud.
			'sc-oportunidades-tempo-real' => array(
				'¿Cómo funciona la sincronización de oportunidades en tiempo real?',
				'<p>CLI Connect utiliza la Subscription API (Platform Events / Change Data Capture) de Salesforce para capturar los cambios en las oportunidades en el momento en que ocurren. En cuanto un registro se actualiza en Sales Cloud, el evento se procesa y los datos se propagan al ERP o sistema de destino sin polling manual.</p>',
			),
			'sc-multiplas-orgs' => array(
				'¿Es posible conectar varias organizaciones de Salesforce a la misma integración?',
				'<p>Sí. CLI Connect soporta múltiples orgs de Salesforce en un único proyecto de integración. Cada organización se configura como una conexión independiente, lo que permite centralizar los flujos de datos entre diferentes instancias de Sales Cloud y los sistemas corporativos sin duplicar arquitecturas.</p>',
			),
			'sc-vs-mulesoft' => array(
				'¿Cómo se compara CLI Connect con MuleSoft para integrar Sales Cloud?',
				'<p>CLI Connect es una alternativa más accesible y ágil para integrar Sales Cloud con ERPs y sistemas corporativos. Mientras MuleSoft exige equipos especializados y ciclos largos de implementación, CLI Connect ofrece aceleradores listos, una puesta en marcha más rápida y un costo total de propiedad reducido, manteniendo gobernanza, seguridad y escalabilidad enterprise.</p>',
			),

			// Dynamics 365.
			'dynamics365-business-central' => array(
				'¿CLI Connect funciona con Dynamics 365 Business Central y Finance & Operations al mismo tiempo?',
				'<p>Sí. CLI Connect soporta varios productos de la familia Dynamics 365 en paralelo. Cada producto se configura como una conexión independiente en la plataforma, lo que permite orquestar datos entre Business Central, Finance & Operations y otros sistemas corporativos en un único proyecto de integración.</p>',
			),
			'dynamics365-autenticacao' => array(
				'¿Cómo se autentica CLI Connect en Microsoft Dynamics?',
				'<p>La autenticación se realiza vía Azure AD (Microsoft Entra ID) utilizando OAuth2 con credenciales de una aplicación registrada. Este modelo garantiza que no se almacene ninguna contraseña de usuario y que los accesos puedan auditarse y revocarse de forma centralizada por el administrador del tenant.</p>',
			),
			'dynamics365-power-automate' => array(
				'¿Es posible reemplazar integraciones desarrolladas en Power Automate?',
				'<p>Sí. CLI Connect ofrece una capa de integración corporativa que reemplaza los flujos de Power Automate en escenarios de alto volumen, lógica compleja o necesidad de gobernanza centralizada. La migración se hace de forma gradual, sin interrumpir las operaciones.</p>',
			),

			// Salesforce Service Cloud.
			'svc-erp-tempo-real' => array(
				'¿Cómo recibe Service Cloud información del ERP en tiempo real?',
				'<p>CLI Connect utiliza la Subscription API de Salesforce combinada con webhooks y conectores nativos de ERP para propagar eventos en tiempo real. Cuando un pedido se actualiza en el ERP, el caso correspondiente en Service Cloud recibe los datos actualizados automáticamente, sin necesidad de consultas manuales.</p>',
			),
			'svc-reembolso-automatico' => array(
				'¿Es posible automatizar procesos de reembolso a partir de un caso?',
				'<p>Sí. CLI Connect permite crear flujos que, al cerrar un caso con determinado estado, disparan automáticamente el proceso de anulación o reembolso en el ERP o sistema financiero. El agente de atención no necesita acceder a ningún otro sistema para iniciar el proceso.</p>',
			),
			'svc-whatsapp-telefonia' => array(
				'¿Cómo funciona la integración con WhatsApp y telefonía?',
				'<p>CLI Connect conecta plataformas de telefonía y canales de mensajería como WhatsApp con Service Cloud vía APIs oficiales. Las interacciones se registran automáticamente como casos o actividades, lo que centraliza toda la jornada de atención en un único lugar sin duplicar datos.</p>',
			),

			// Home.
			'home-o-que-faz' => array(
				'¿Qué hace exactamente CLI Connect?',
				'<p>CLI Connect es una plataforma de integración empresarial que conecta ERPs, CRMs, e-commerce y demás sistemas corporativos. Utilizamos la tecnología de Boomi para crear, monitorear y mantener flujos de datos seguros, escalables y auditables entre los sistemas de su empresa.</p>',
			),
			'home-quanto-tempo' => array(
				'¿Cuánto tiempo tarda el servicio?',
				'<p>El tiempo varía según la complejidad de las integraciones. Los proyectos simples pueden entrar en producción en pocas semanas; los escenarios más complejos, con múltiples sistemas y reglas de negocio, pueden llevar algunos meses. Durante el diagnóstico inicial presentamos un cronograma realista para su caso.</p>',
			),
			'home-algo-parar' => array(
				'¿Y si algo deja de funcionar?',
				'<p>Nuestro equipo monitorea las integraciones de forma continua. En caso de falla, abrimos un ticket automáticamente y activamos al equipo de soporte incluso antes de que usted note el problema. Además, puede contactar al soporte en cualquier momento por nuestros canales de atención.</p>',
			),
			'home-dependencia' => array(
				'¿Voy a depender de CLI Connect para todo?',
				'<p>No. Las integraciones se construyen sobre la plataforma Boomi, que es de su propiedad. CLI Connect se encarga de la operación, la evolución y el soporte, pero usted tiene acceso al entorno y puede recurrir a otros socios de Boomi si lo desea. Nuestro modelo es de asociación, no de lock-in.</p>',
			),
			'home-contratacao' => array(
				'¿Cómo funciona el modelo de contratación?',
				'<p>Trabajamos con proyectos de implementación (alcance cerrado) y contratos de servicio gestionado (mensualidad por entorno monitoreado). El modelo más adecuado depende de su momento: los nuevos clientes suelen empezar por la implementación y evolucionan al servicio gestionado después del go-live.</p>',
			),
			'home-criar-integracoes' => array(
				'¿Puedo crear mis propias integraciones en CLI Connect?',
				'<p>Sí. La plataforma Boomi permite que los equipos internos creen y editen integraciones. CLI Connect puede capacitar a su equipo, hacer revisiones de código y asumir la operación cuando sea necesario. Muchos clientes optan por un modelo híbrido, en el que desarrollan internamente y cuentan con CLI Connect para el soporte y el monitoreo.</p>',
			),
		);
	}

	/**
	 * Salesforce Marketing Cloud, RD Station CRM, RD Station Marketing,
	 * Thomson Reuters Tax One, Freshservice, ServiceNow,
	 * Portal de API / MCP Server e Zendesk.
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function faq_es_bloco_3() {
		return array(
			// Salesforce Marketing Cloud.
			'mc-jornada-evento-externo' => array(
				'¿Cómo disparar un journey a partir de un evento fuera de Salesforce?',
				'<p>CLI Connect utiliza la API de Eventos de Marketing Cloud combinada con conectores nativos de ERP y e-commerce. Cuando ocurre un evento externo — como una compra o la actualización de un producto — la integración envía el payload directamente a Journey Builder y activa el journey correspondiente sin intervención manual.</p>',
			),
			'mc-optout-todos-canais' => array(
				'¿Cómo garantizar que un opt-out se propague a todos los canales?',
				'<p>CLI Connect sincroniza las preferencias de contacto entre Marketing Cloud, el CRM y los demás sistemas conectados. Cuando un contacto solicita el opt-out en cualquier canal, la integración propaga la información en tiempo real a todos los puntos de comunicación, lo que garantiza el cumplimiento y evita envíos no deseados.</p>',
			),
			'mc-segmentos-tempo-real' => array(
				'¿Es posible sincronizar segmentos de audiencia en tiempo real con el CRM?',
				'<p>Sí. CLI Connect mantiene actualizadas las Data Extensions de Marketing Cloud a partir de eventos de negocio del CRM, el ERP y las plataformas de e-commerce. Los segmentos y las listas reflejan datos reales de compras, uso de producto e interacciones de soporte sin necesidad de exportaciones manuales ni programaciones periódicas.</p>',
			),

			// RD Station CRM.
			'rd-station-crm-erp' => array(
				'¿Cómo sincronizar los negocios cerrados directamente con el ERP?',
				'<p>Al cerrar un negocio en RD Station CRM, CLI Connect detecta el evento vía webhook y activa automáticamente el flujo de integración configurado, creando el pedido, el contrato o el registro de cliente en el ERP sin intervención manual. El mapeo de campos se define una vez y puede ajustarse según las reglas de su proceso comercial.</p>',
			),
			'rd-station-crm-multiplas-contas' => array(
				'¿Es posible conectar varias cuentas de RD Station de diferentes unidades de negocio?',
				'<p>Sí. CLI Connect soporta múltiples conexiones simultáneas con cuentas distintas de RD Station CRM. Cada unidad de negocio opera con su propio conjunto de credenciales y flujos independientes, centralizados en una única plataforma de integración para facilitar la gobernanza.</p>',
			),
			'rd-station-crm-rate-limit' => array(
				'¿Cómo manejar los límites de tasa de la API de RD Station CRM?',
				'<p>CLI Connect gestiona automáticamente los límites de tasa de la API de RD Station CRM mediante colas y mecanismos de retry con backoff exponencial. En los picos de volumen — como importaciones por lotes o campañas de gran escala — los datos se procesan de forma controlada, sin errores ni pérdida de registros.</p>',
			),

			// RD Station Marketing.
			'rd-station-marketing-nutricao' => array(
				'¿Cómo evitar que un lead ya cerrado siga recibiendo correos de nutrición?',
				'<p>CLI Connect puede activar automáticamente la eliminación del lead de las listas activas de RD Station Marketing al detectar un negocio ganado o un cliente convertido en el CRM. Así, los contactos que ya cerraron una venta dejan de recibir flujos de nutrición de forma automática, sin intervención manual del equipo de marketing.</p>',
			),
			'rd-station-marketing-atribuicao' => array(
				'¿Es posible medir la atribución de campaña hasta el cierre en el ERP?',
				'<p>Sí. CLI Connect conecta los datos de campaña de RD Station Marketing con los registros de venta y facturación del ERP. Con esto es posible rastrear la jornada del lead desde el primer clic en una campaña hasta el pedido facturado, lo que genera visibilidad sobre el ROI real de cada acción de marketing.</p>',
			),
			'rd-station-marketing-webhooks' => array(
				'¿Cómo funciona la integración vía webhooks en tiempo real?',
				'<p>RD Station Marketing envía eventos vía webhook en cuanto ocurre una acción: formulario completado, lead calificado, automatización finalizada. CLI Connect recibe esos eventos, valida el payload y activa los flujos configurados de forma inmediata, sin necesidad de polling. La latencia media es de segundos, lo que garantiza que los datos lleguen al CRM o al ERP prácticamente en tiempo real.</p>',
			),

			// Thomson Reuters Tax One.
			'tax-one-divergencia-calculo' => array(
				'¿Cómo evitar divergencias de cálculo fiscal entre el ERP y el e-commerce?',
				'<p>CLI Connect centraliza todas las llamadas al motor de Tax One en un único punto de integración. Tanto el ERP como el e-commerce consultan el mismo motor fiscal, lo que garantiza que el tributo calculado en el checkout sea idéntico al registrado en la factura emitida por el ERP y elimina divergencias en la liquidación.</p>',
			),
			'tax-one-multiplos-erps' => array(
				'¿Es posible centralizar el motor fiscal para varios ERPs?',
				'<p>Sí. CLI Connect permite conectar diferentes ERPs — como SAP, TOTVS y Dynamics — al mismo motor de Tax One. Cada sistema realiza sus llamadas de cálculo de forma independiente, pero todas pasan por la misma configuración de reglas tributarias, lo que garantiza consistencia fiscal en toda la organización.</p>',
			),
			'tax-one-auditoria-chamadas' => array(
				'¿Cómo funciona la auditoría de las llamadas al motor de cálculo?',
				'<p>CLI Connect registra cada llamada realizada a Tax One, incluidos el sistema de origen, los parámetros enviados, el resultado devuelto y el timestamp de la operación. Esa traza de auditoría queda disponible para consulta y facilita la comprobación de los cálculos en procesos de fiscalización tributaria.</p>',
			),

			// Freshservice.
			'freshservice-processo-sem-modulo' => array(
				'¿Es posible crear un proceso de negocio en Freshservice sin comprar un módulo adicional?',
				'<p>Sí. CLI Connect permite crear formularios, aprobaciones y catálogos de servicio que se integran directamente con los sistemas internos — como el ERP y el CRM — sin necesidad de contratar módulos adicionales de Freshservice.</p>',
			),
			'freshservice-formulario-grava-sistema' => array(
				'¿Cómo hace un formulario de Freshservice para grabar directamente en otro sistema interno?',
				'<p>La integración se hace vía APIs REST de Freshservice. Cuando un usuario envía un formulario, CLI Connect activa el flujo de integración, que traduce y envía los datos al sistema de destino — como SAP, TOTVS o Active Directory — en tiempo real.</p>',
			),
			'freshservice-abrir-tickets-automaticamente' => array(
				'¿Cómo abrir tickets automáticamente a partir de otro sistema?',
				'<p>Los eventos de sistemas externos — como alertas de monitoreo, eventos de RR. HH. o incidentes de seguridad — disparan llamadas vía API a CLI Connect, que crea los tickets correspondientes en Freshservice con los datos y las prioridades correctos.</p>',
			),

			// ServiceNow.
			'servicenow-processo-sem-modulo' => array(
				'¿Cómo crear un proceso en ServiceNow que grabe directamente en otro sistema, sin un módulo de integración nativo?',
				'<p>CLI Connect actúa como capa de integración externa: ServiceNow dispara un evento vía API, la plataforma lo recibe, lo procesa y graba los datos en el sistema de destino — como SAP o TOTVS — sin necesidad de Spokes ni módulos adicionales de ServiceNow.</p>',
			),
			'servicenow-cadastro-produtos-totvs' => array(
				'¿Cómo funciona el ejemplo de registro de productos → TOTVS ERP?',
				'<p>El usuario completa un formulario en el catálogo de servicios de ServiceNow. Una vez aprobado, CLI Connect recibe el payload, valida los datos y llama a la API de TOTVS para crear el producto. ServiceNow recibe la confirmación y cierra el ticket automáticamente.</p>',
			),
			'servicenow-agente-ia-incidente' => array(
				'¿Cómo abre un agente de IA un incidente en ServiceNow automáticamente?',
				'<p>El agente de IA envía una solicitud a CLI Connect con los datos del evento. La plataforma formatea el payload según el schema de ServiceNow y crea el incidente vía API REST, incluyendo categoría, urgencia y descripción, sin intervención humana.</p>',
			),

			// Portal de API / MCP Server.
			'portal-de-api-diferenca-api-mcp' => array(
				'¿Cuál es la diferencia entre publicar una API y exponer un servidor MCP?',
				'Una API publica endpoints REST documentados para el consumo de sistemas y aplicaciones. El MCP Server expone herramientas autenticadas para el consumo de agentes de IA, que usan lenguaje natural para descubrir y ejecutar las acciones disponibles en el Portal.',
			),
			'portal-de-api-agente-descobre-ferramentas' => array(
				'¿Cómo descubre y usa un agente de IA las herramientas publicadas en el Portal?',
				'El agente se conecta al servidor MCP del Portal, que lista automáticamente las herramientas disponibles con nombre, descripción y parámetros. El agente selecciona y ejecuta la herramienta adecuada con autenticación y control de alcance heredados de la plataforma.',
			),
			'portal-de-api-limitar-acesso-consumidor' => array(
				'¿Es posible limitar el acceso de cada consumidor?',
				'Sí. Cada consumidor — sea un sistema, un usuario o un agente — recibe credenciales propias con alcances definidos. El Portal controla a qué APIs y herramientas puede acceder cada consumidor y audita todas las llamadas.',
			),
			'portal-de-api-pipeline-vira-api' => array(
				'¿Un pipeline existente puede convertirse en API sin retrabajo?',
				'Sí. El Portal de API permite publicar pipelines de Boomi ya construidos como endpoints REST documentados con pocos clics, sin crear nuevos proyectos de desarrollo ni reescribir integraciones.',
			),

			// Zendesk.
			'zendesk-processo-sem-app-pago' => array(
				'¿Cómo crear un proceso en Zendesk que grabe en otro sistema sin una app de pago?',
				'CLI Connect actúa como capa de integración externa: Zendesk dispara un evento vía webhook, la plataforma lo recibe, lo procesa y graba los datos en el sistema de destino — como SAP o TOTVS — sin necesidad de aplicaciones de pago del Marketplace.',
			),
			'zendesk-enriquecer-ticket-erp-crm' => array(
				'¿Cómo enriquecer un ticket con datos del ERP y del CRM en tiempo real?',
				'Al abrirse un ticket, CLI Connect consulta el ERP y el CRM en paralelo usando el correo o el ID del cliente y devuelve los datos — pedidos, facturas, contratos — directamente en el ticket vía API de Zendesk, sin intervención manual del agente.',
			),
			'zendesk-tickets-macros-agentes-ia' => array(
				'¿Es posible exponer tickets y macros como herramientas para agentes de IA?',
				'Sí. CLI Connect publica los endpoints de Zendesk como herramientas MCP autenticadas. Los agentes de IA pueden consultar tickets, aplicar macros y actualizar campos usando lenguaje natural, con control de permisos por alcance.',
			),
			'zendesk-sincronizacao-status-crm' => array(
				'¿Cómo funciona la sincronización de estados entre Zendesk y el CRM?',
				'CLI Connect monitorea los cambios de estado en Zendesk vía webhook y replica el estado en el CRM en tiempo real. El flujo inverso también es compatible: las actualizaciones en el CRM se reflejan automáticamente en el ticket de Zendesk.',
			),
		);
	}

	/**
	 * Bionexo, Tasy, MV, VTEX, Shopify, Magento / Adobe Commerce,
	 * OnBlox (WMS/TMS) e Narwal (Comex).
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function faq_es_bloco_4() {
		return array(
			// Bionexo.
			'bionexo-sincronizar-pedidos-erp' => array(
				'¿Cómo sincronizar los pedidos de compra de Bionexo directamente con el ERP?',
				'CLI Connect se conecta vía API de Bionexo y dispara eventos con cada nuevo pedido aprobado, grabándolo automáticamente en el ERP hospitalario — como TOTVS o SAP — sin intervención manual.',
			),
			'bionexo-conciliar-notas-fiscais' => array(
				'¿Es posible conciliar las facturas automáticamente?',
				'Sí. La plataforma captura las facturas emitidas por Bionexo y las concilia con los pedidos de compra registrados en el ERP, señalando las divergencias y eliminando el proceso manual de verificación.',
			),
			'bionexo-multiplas-unidades-hospitalares' => array(
				'¿Cómo funciona con varias unidades hospitalarias?',
				'La integración soporta varias unidades en una única configuración: cada unidad puede tener sus credenciales y flujos independientes, centralizados y monitoreados en la misma plataforma.',
			),

			// Tasy.
			'tasy-cli-connect-tasy-open-api' => array(
				'¿Cómo usa CLI Connect powered by Boomi la Tasy Open API?',
				'CLI Connect se conecta a la Tasy Open API con autenticación segura y orquesta los flujos de integración entre Tasy y sistemas externos como el ERP, las aseguradoras de salud y los laboratorios, sin alterar el core hospitalario.',
			),
			'tasy-faturamento-tiss-multiplas-operadoras' => array(
				'¿Es posible integrar la facturación TISS de varias aseguradoras?',
				'Sí. La plataforma permite configurar conectores para diferentes aseguradoras de salud y procesar las guías y los retornos de autorización de forma centralizada y automatizada.',
			),
			'tasy-consolidacao-financeira-multi-hospital' => array(
				'¿Cómo funciona la consolidación financiera multihospital?',
				'CLI Connect crea una capa de integración única que recoge los datos financieros de varias unidades Tasy y los envía al ERP corporativo, lo que elimina los procesos manuales de conciliación.',
			),

			// MV.
			'mv-reduzir-glosas-ris-pacs' => array(
				'¿Cómo reducir los rechazos de facturación con la integración RIS/PACS ↔ MV?',
				'CLI Connect valida automáticamente la información de la solicitud de examen antes de su ejecución, cruzando datos entre RIS/PACS y MV para detectar las inconsistencias que generan rechazos antes de que ocurran.',
			),
			'mv-conectar-mv-tasy-rede-hospitalar' => array(
				'¿Es posible conectar MV y Tasy en la misma red hospitalaria?',
				'Sí. La plataforma actúa como capa de integración neutral y puede orquestar flujos entre MV y Tasy, lo que permite que las redes hospitalarias con diferentes HIS compartan datos de forma estandarizada.',
			),
			'mv-consolidacao-financeira-multi-unidade' => array(
				'¿Cómo funciona la consolidación financiera multiunidad?',
				'CLI Connect recoge los datos financieros de cada unidad MV y los centraliza en el ERP corporativo, lo que elimina las consolidaciones manuales en hojas de cálculo y garantiza una visibilidad unificada del resultado financiero de la red.',
			),

			// VTEX.
			'vtex-pico-trafego' => array(
				'¿Cómo maneja CLI Connect powered by Boomi los picos de tráfico como el Black Friday?',
				'<p>La plataforma Boomi opera con una arquitectura elástica en la nube y escala automáticamente para absorber volúmenes de pedidos superiores a los de los períodos normales. Durante el Black Friday, los conectores siguen procesando pedidos, actualizaciones de inventario y pagos con la misma confiabilidad, sin necesidad de intervención manual ni ajustes de infraestructura.</p>',
			),
			'vtex-multiplos-marketplaces' => array(
				'¿Es posible sincronizar el inventario entre VTEX y varios marketplaces?',
				'<p>Sí. CLI Connect integra VTEX con los principales marketplaces del mercado y mantiene el inventario actualizado en tiempo real en todos los canales. Cuando ocurre una venta en cualquier canal, el inventario se descuenta automáticamente en los demás, lo que evita el overselling y garantiza una experiencia de compra consistente.</p>',
			),
			'vtex-ship-from-store' => array(
				'¿Cómo funciona el fulfillment ship-from-store?',
				'<p>La integración conecta los pedidos recibidos en VTEX con las tiendas físicas elegibles para el envío, con base en reglas de proximidad, inventario disponible y capacidad operativa. El proceso de picking, empaque y despacho se gestiona mediante la integración entre VTEX y el WMS o el sistema de punto de venta de la tienda.</p>',
			),

			// Shopify.
			'shopify-nfe' => array(
				'¿Cómo resuelve CLI Connect powered by Boomi la emisión de NF-e desde Shopify?',
				'<p>La plataforma Boomi conecta los pedidos recibidos en Shopify con el sistema fiscal brasileño y genera automáticamente la NF-e con los datos correctos de producto, tributación y destinatario. El proceso ocurre en tiempo real tras la confirmación del pedido, sin intervención manual y en conformidad con las reglas tributarias vigentes.</p>',
			),
			'shopify-estoque-multicanal' => array(
				'¿Es posible sincronizar el inventario con varios canales?',
				'<p>Sí. CLI Connect integra Shopify con el ERP, el WMS y los marketplaces, y mantiene el inventario actualizado en tiempo real en todos los canales. Cuando ocurre una venta en cualquier punto, el saldo se descuenta automáticamente en los demás, lo que elimina el overselling y garantiza consistencia operativa.</p>',
			),
			'shopify-plus' => array(
				'¿Funciona con Shopify Plus?',
				'<p>Sí. La integración es compatible tanto con Shopify como con Shopify Plus, y aprovecha las APIs avanzadas disponibles en la versión Plus para automatizaciones más complejas, como flujos de checkout personalizados, múltiples tiendas y operaciones B2B.</p>',
			),

			// Magento / Adobe Commerce.
			'magento-extensoes' => array(
				'¿Cómo reducir la dependencia de extensiones personalizadas en Magento?',
				'<p>CLI Connect crea una capa de integración externa a Magento y traslada las lógicas de negocio — como la sincronización de pedidos, catálogo y pagos — a la plataforma Boomi. Esto reduce la cantidad de extensiones instaladas, simplifica las actualizaciones de versión y mantiene el core de Magento estable y con buen rendimiento.</p>',
			),
			'magento-pim' => array(
				'¿Es posible centralizar el catálogo mediante un PIM?',
				'<p>Sí. La integración conecta el PIM con Magento para sincronizar productos, descripciones, precios y atributos de forma automatizada. Cuando se hace una actualización en el PIM, esta se propaga a Magento sin necesidad de importaciones manuales, lo que garantiza la consistencia del catálogo en todos los canales.</p>',
			),
			'magento-pagamentos' => array(
				'¿Cómo funciona la conciliación de pagos y el antifraude?',
				'<p>La integración conecta las pasarelas de pago y los sistemas antifraude con el área financiera de la empresa y automatiza la conciliación de las transacciones realizadas en Magento. Las divergencias se identifican y se tratan de forma centralizada, lo que reduce los errores manuales y acelera el cierre financiero.</p>',
			),

			// OnBlox (WMS/TMS).
			'onblox-estoque-erp' => array(
				'¿Cómo sincronizar el inventario entre OnBlox y el ERP?',
				'<p>CLI Connect crea una integración en tiempo real entre OnBlox y el ERP, y transmite los movimientos de inventario automáticamente con cada transacción en el almacén. Esto elimina los reprocesos manuales, reduce las divergencias de inventario y garantiza que los canales de venta reflejen siempre la disponibilidad real.</p>',
			),
			'onblox-frota-financeiro' => array(
				'¿Es posible integrar los datos de flota con el área financiera?',
				'<p>Sí. La integración conecta el módulo de gestión de flota de OnBlox con el sistema financiero y automatiza el envío de costos de mantenimiento, combustible y multas. Con esto, los asientos contables se generan con precisión y los informes de costo operativo se mantienen siempre actualizados sin intervención manual.</p>',
			),
			'onblox-multiplos-cds' => array(
				'¿Cómo funciona con varios centros de distribución?',
				'<p>La plataforma CLI Connect soporta múltiples centros de distribución y enruta los pedidos automáticamente al CD más adecuado con base en reglas de proximidad, disponibilidad de inventario y capacidad operativa. Cada CD opera integrado al ERP y al e-commerce, lo que mantiene la visibilidad centralizada de toda la operación logística.</p>',
			),

			// Narwal (Comex).
			'narwal-custos-importacao' => array(
				'¿Cómo sincronizar los costos de importación con el ERP automáticamente?',
				'<p>CLI Connect integra Narwal con el ERP mediante una capa de integración dedicada y transmite automáticamente fletes, gastos de despacho aduanero y tributos en cuanto se registran en el sistema. Esto elimina la necesidad de registros manuales, reduce los errores de conciliación y garantiza que el área financiera refleje los costos reales de cada proceso de importación.</p>',
			),
			'narwal-multiplas-filiais' => array(
				'¿Es posible integrar varias sucursales y operaciones de comercio exterior?',
				'<p>Sí. La plataforma CLI Connect soporta entornos multiempresa y permite centralizar las operaciones de comercio exterior de diferentes sucursales en una única integración con el ERP. Cada sucursal mantiene su visibilidad individual, mientras los datos se consolidan para los análisis financieros y operativos corporativos.</p>',
			),
			'narwal-duimp' => array(
				'¿Cómo funciona con la transición a la DUIMP?',
				'<p>La integración está adaptada al nuevo modelo de la DUIMP (Declaração Única de Importação) y conecta los procesos de Narwal con los sistemas de la Receita Federal y con el ERP de forma compatible con la nueva sistemática. Así, la transición ocurre sin interrupciones en el flujo de datos entre comercio exterior y finanzas.</p>',
			),
		);
	}

	/**
	 * Neogrid, Target Sistemas, SAP Business One, SAP ECC,
	 * Oracle NetSuite e Serviços Financeiros.
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function faq_es_bloco_5() {
		return array(
			// Neogrid.
			'neogrid-erp-nativo' => array(
				'¿Cómo integrar Neogrid con un ERP fuera de los conectores nativos?',
				'<p>CLI Connect actúa como una capa de integración independiente de los conectores nativos de Neogrid y permite conectar cualquier ERP mediante la plataforma Boomi. Esto se hace traduciendo los formatos EDI y las APIs de Neogrid al estándar del ERP de destino, sin necesidad de desarrollo personalizado en cada sistema.</p>',
			),
			'neogrid-bi-varejo' => array(
				'¿Es posible llevar los datos de visibilidad del retail al BI corporativo?',
				'<p>Sí. La integración extrae los datos de sell-out, quiebre de stock e inventario disponibles en Neogrid y los envía al BI corporativo en tiempo real o en lotes programados. Así, los equipos de ventas y operaciones toman decisiones basadas en datos actualizados, sin exportaciones manuales ni hojas de cálculo intermedias.</p>',
			),
			'neogrid-traducao-edi' => array(
				'¿Cómo funciona la traducción de pedidos EDI?',
				'<p>La plataforma CLI Connect recibe los pedidos en el formato EDI transmitido por Neogrid y realiza la traducción automática al formato nativo del ERP interno, sea XML, JSON o layouts propietarios. El proceso se audita con logs de cada transacción, lo que garantiza trazabilidad y facilidad de diagnóstico en caso de divergencia.</p>',
			),

			// Target Sistemas.
			'target-sistemas-edi-onboarding' => array(
				'¿Cómo acelerar el onboarding de un nuevo proveedor vía EDI?',
				'<p>CLI Connect ofrece un acelerador de integración listo para EDI que reduce el tiempo de onboarding de proveedores en Target ERP. La plantilla incluye mapeos preconfigurados para los principales formatos de pedido, factura y confirmación de entrega, lo que elimina el desarrollo desde cero y permite conectar nuevos socios en días en lugar de semanas.</p>',
			),
			'target-sistemas-forca-vendas' => array(
				'¿Es posible integrar varias aplicaciones de fuerza de ventas con Target?',
				'<p>Sí. La plataforma CLI Connect opera como hub central entre Target ERP y diferentes aplicaciones de fuerza de ventas simultáneamente. Los pedidos capturados en campo se transmiten en tiempo real al ERP, con sincronización de inventario, lista de precios y condiciones comerciales por equipo o región, sin necesidad de personalización individual en cada aplicación.</p>',
			),
			'target-sistemas-financeiro-multi-empresa' => array(
				'¿Cómo funciona la consolidación financiera multiempresa?',
				'<p>CLI Connect centraliza el flujo de datos financieros entre las sucursales y el holding mediante integraciones gobernadas con Target ERP. Las conciliaciones bancarias, las transferencias entre empresas y los informes consolidados se automatizan, lo que garantiza que los sistemas financieros de cada entidad estén sincronizados sin intervención manual y con trazabilidad completa de cada transacción.</p>',
			),

			// SAP Business One.
			'sap-business-one-service-layer' => array(
				'¿Cómo funciona la integración vía Service Layer de SAP B1?',
				'<p>CLI Connect utiliza el Service Layer REST/OData oficial de SAP Business One para conectar aplicaciones corporativas sin depender de personalizaciones frágiles. La autenticación segura por sesión garantiza que cada solicitud se valide antes de acceder a la base de datos de SAP B1, y el modelo de integración abstrae la complejidad de la API nativa entregando mapeos listos para pedidos, clientes, productos y documentos fiscales.</p>',
			),
			'sap-business-one-migracao-s4hana' => array(
				'¿Es posible migrar las integraciones cuando la empresa evolucione a S/4HANA?',
				'<p>Sí. La capa de integración de CLI Connect es agnóstica a la versión de SAP. Al migrar de Business One a S/4HANA, los flujos de datos permanecen en la plataforma y solo se reconfigura el conector de destino, lo que preserva todo el historial de mapeos, reglas de negocio y conexiones con sistemas externos sin reconstruir desde cero.</p>',
			),
			'sap-business-one-multiplas-filiais' => array(
				'¿Cómo manejar varias sucursales en SAP B1?',
				'<p>CLI Connect opera con múltiples empresas en SAP Business One de forma simultánea y enruta las transacciones a la sucursal correcta con base en reglas de negocio configurables. Es posible consolidar pedidos de diferentes canales en sucursales específicas, sincronizar listas de precios e inventarios entre unidades y generar informes financieros integrados sin personalizaciones adicionales en SAP.</p>',
			),

			// SAP ECC.
			'sap-ecc-firewall' => array(
				'¿Es posible conectar ECC sin abrir puertos del firewall?',
				'<p>Sí. CLI Connect utiliza el Boomi Atom instalado dentro de la red on-premises de ECC, que establece conexiones de salida seguras sin exigir la apertura de puertos de entrada en el firewall. El Runtime local se comunica con la plataforma en la nube de forma cifrada, lo que permite que SAP ECC permanezca completamente aislado de internet mientras intercambia datos con sistemas externos.</p>',
			),
			'sap-ecc-cutover-s4hana' => array(
				'¿Cómo funciona el cutover en paralelo con S/4HANA?',
				'<p>Durante la migración a S/4HANA, CLI Connect opera ambos sistemas simultáneamente en la misma plataforma de integración. Los flujos de datos pueden enrutarse a ECC, a S/4HANA o a ambos, según la fase del proyecto, lo que elimina la necesidad de reconstruir integraciones después del cutover. El historial de transacciones y las reglas de mapeo se preservan durante toda la transición.</p>',
			),
			'sap-ecc-pos-migracao' => array(
				'¿Qué cambia en la integración después de la migración a S/4HANA?',
				'<p>Con CLI Connect, prácticamente nada necesita reconstruirse. La plataforma abstrae las diferencias entre los RFC/BAPI de ECC y los OData/BAPI de S/4HANA y adapta los conectores automáticamente. Las integraciones con sistemas externos como Salesforce, e-commerce y sistemas fiscales siguen funcionando sin cambios en los flujos de negocio ya configurados.</p>',
			),

			// Oracle NetSuite.
			'oracle-netsuite-suiteScript' => array(
				'¿Cómo reducir la dependencia de SuiteScript personalizado?',
				'<p>CLI Connect utiliza las APIs nativas de NetSuite — SuiteTalk REST/SOAP y RESTlets — para crear integraciones reutilizables sin necesidad de scripts específicos por proyecto. En lugar de desarrollar y mantener SuiteScript para cada integración, la plataforma centraliza los flujos en conectores configurables, lo que reduce el volumen de código personalizado y el esfuerzo de mantenimiento a lo largo del tiempo.</p>',
			),
			'oracle-netsuite-subsidiarias' => array(
				'¿Es posible replicar la misma integración para nuevas subsidiarias?',
				'<p>Sí. Con NetSuite OneWorld, CLI Connect permite replicar integraciones entre subsidiarias sin reconstruirlas. La plataforma gestiona la segmentación por subsidiaria, aplica las reglas de negocio específicas de cada entidad y estandariza los flujos de datos financieros y operativos a nivel global, lo que garantiza consistencia sin desarrollo adicional para cada nueva subsidiaria incorporada.</p>',
			),
			'oracle-netsuite-tba-oauth2' => array(
				'¿Cómo funciona la autenticación vía TBA/OAuth2?',
				'<p>NetSuite admite Token-Based Authentication (TBA) y OAuth 2.0 como mecanismos de autenticación para las integraciones vía API. CLI Connect utiliza esas credenciales para establecer conexiones seguras sin almacenar contraseñas de usuario, siguiendo las mejores prácticas de seguridad corporativa. El acceso se controla por roles y permisos de NetSuite, lo que garantiza que cada integración opere solo dentro del alcance autorizado.</p>',
			),

			// Serviços Financeiros.
			'sf-fin-core-banking' => array(
				'¿Cuánto tiempo lleva integrar un core banking?',
				'<p>Los primeros flujos suelen entrar en producción en semanas, no en meses. El plazo depende del core utilizado, del volumen de datos y de las validaciones de seguridad exigidas, pero la implementación se hace por etapas: las integraciones críticas entran primero y las demás llegan en oleadas siguientes, sin frenar la operación.</p>',
			),
			'sf-fin-legados' => array(
				'¿CLI Connect funciona con sistemas legados?',
				'<p>Sí. Además de las APIs REST y SOAP, la plataforma se conecta a bases de datos, archivos, colas de mensajería y servicios internos que no exponen API. Para entornos on-premises, la comunicación se hace mediante un agente instalado dentro de la red corporativa, sin abrir puertos de entrada en el firewall.</p>',
			),
			'sf-fin-open-finance' => array(
				'¿Cómo acompañan las integraciones las iniciativas de Open Finance?',
				'<p>Las integraciones se construyen sobre una capa de APIs gobernada, con versionado, control de accesos y trazabilidad de extremo a extremo. Esto permite exponer y consumir servicios de socios al ritmo en que avanzan la regulación y las fases del Open Finance, sin reescribir la arquitectura en cada cambio.</p>',
			),
			'sf-fin-dados-ia' => array(
				'¿Es posible utilizar datos legados en proyectos de IA?',
				'<p>Sí. Las integraciones normalizan y conectan información que hoy está dispersa entre sistemas y la dejan en un formato confiable y trazable. Es ese conjunto tratado el que alimenta a los agentes inteligentes, los motores de decisión y los análisis avanzados.</p>',
			),
			'sf-fin-dados-sensiveis' => array(
				'¿Cómo protege CLI Connect los datos sensibles durante las integraciones?',
				'<p>Los datos viajan cifrados, las credenciales quedan en una bóveda y cada acceso se registra para auditoría. La operación sigue los estándares de compliance de la plataforma — SOC 2, ISO 27001, PCI DSS y LGPD/GDPR, entre otros — y los flujos se diseñan para exponer solo la información necesaria a cada sistema.</p>',
			),
		);
	}

	/**
	 * Manufatura, Software (ISV), Logística (3PL), Seguros e Varejo.
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function faq_es_bloco_6() {
		return array(
			// Manufatura.
			'mf-ipaas-vs-sap' => array(
				'¿Cuál es la diferencia entre un iPaaS y SAP Integration Suite o SAP MII?',
				'<p>SAP Integration Suite y SAP MII resuelven muy bien lo que nace y termina dentro del mundo SAP. Un iPaaS trata la integración como una capa independiente: el mismo entorno conecta SAP S/4HANA, MES, WMS, Salesforce, sistemas industriales y servicios en la nube, con gobernanza y monitoreo únicos. En la práctica, ambos conviven: el iPaaS asume los flujos que cruzan fronteras entre sistemas y evita que cada nuevo proyecto se convierta en una integración punto a punto.</p>',
			),
			'mf-ot-nuvem-seguranca' => array(
				'¿Es posible conectar equipos industriales a la nube con seguridad?',
				'<p>Sí. La comunicación con el entorno industrial se hace mediante un agente instalado dentro de la propia red, que abre la conexión de dentro hacia fuera: no es necesario exponer puertos de entrada en el firewall. Sobre eso funciona una arquitectura zero-trust: datos cifrados en tránsito, credenciales en bóveda y cada acceso registrado, con cada flujo viendo solo la información que necesita.</p>',
			),
			'mf-mulesoft' => array(
				'¿CLI Connect puede reemplazar plataformas como MuleSoft?',
				'<p>Sí, ese tipo de reemplazo es un escenario común de proyecto. La evaluación pasa por mapear las integraciones existentes, el volumen procesado y las necesidades de gobernanza, y la migración se hace por oleadas: los flujos críticos entran primero y los demás siguen por etapas, manteniendo ambos entornos en paralelo hasta el corte. La ganancia suele estar en el costo de mantenimiento y en la velocidad para crear flujos nuevos.</p>',
			),
			'mf-compliance-industrial' => array(
				'¿La plataforma cumple los requisitos de compliance industrial?',
				'<p>La plataforma opera bajo los estándares de seguridad y privacidad listados en esta página — SOC 2, ISO 27001, ISO 27701, ISO 27018, PCI DSS y GDPR/LGPD, entre otros. Para la industria, lo que suele pesar es la trazabilidad: cada ejecución de flujo queda registrada, con historial de versiones y traza de auditoría, lo que sustenta las exigencias de calidad y de validación de procesos.</p>',
			),
			'mf-iot-volume' => array(
				'¿Cómo maneja la plataforma grandes volúmenes de datos de sensores IoT?',
				'<p>El procesamiento es elástico y trabaja en flujo continuo, con colas que absorben los picos de recolección sin perder mensajes. En lugar de volcar el dato bruto en el destino, los flujos filtran, agregan y normalizan en el camino: así solo lo que tiene uso analítico llega a las plataformas de datos y a los modelos de IA, lo que reduce el costo de almacenamiento y el tiempo de respuesta.</p>',
			),

			// Software (ISV).
			'isv-tempo-primeira-integracao' => array(
				'¿Cuánto tiempo lleva crear una integración nativa con Salesforce o SAP?',
				'<p>La primera integración suele entrar en producción en unos cinco días. La ganancia viene de no empezar desde cero: los conectores para Salesforce, SAP y demás sistemas corporativos ya existen, y el trabajo se concentra en mapear campos y reglas de negocio en un entorno low-code. Las integraciones siguientes son aún más rápidas, porque reaprovechan los componentes construidos en la primera.</p>',
			),
			'isv-mudanca-api-parceiro' => array(
				'¿Qué pasa cuando cambia la API de un socio?',
				'<p>La actualización ocurre en la capa de integración, no dentro de su producto. Como el conector se mantiene en la plataforma y lo comparten todos los clientes que lo utilizan, el cambio se aplica una vez y vale para toda la base, en lugar de convertirse en una corrección por cliente. El monitoreo centralizado muestra qué flujos se vieron afectados antes de que eso llegue al usuario final.</p>',
			),
			'isv-isolamento-multi-tenant' => array(
				'¿Cómo funciona el aislamiento de datos en entornos multi-tenant?',
				'<p>Cada cliente opera con credenciales y entorno de ejecución propios, y un flujo solo ve los datos del tenant al que pertenece. Cuando el escenario lo exige, la ejecución ocurre dentro de la infraestructura del propio cliente, sin VPN ni puertos abiertos: el dato sensible no sale de su perímetro y el panel central recibe únicamente los registros de ejecución.</p>',
			),
			'isv-custo-conectores-internos' => array(
				'¿Cuál es el costo real de mantener conectores desarrollados internamente?',
				'<p>El costo visible es el de la construcción; lo que pesa es el mantenimiento. Cada conector interno se convierte en código propietario que debe acompañar los cambios de API, autenticación y volumen, y ese esfuerzo crece junto con la base de clientes. Con integraciones reutilizables, el equipo de producto deja de mantener conectores individuales y la operación escala según el consumo de la plataforma.</p>',
			),
			'isv-cargas-elevadas' => array(
				'¿La plataforma soporta cargas de procesamiento muy elevadas?',
				'<p>Sí. El procesamiento es elástico y trabaja con colas que absorben los picos sin perder mensajes, lo que permite atender desde un cliente pequeño hasta operaciones con millones de ejecuciones por mes en el mismo entorno. El panel operativo acompaña volumen, latencia y fallas por cliente, y la capacidad sigue al consumo sin exigir reescribir los flujos.</p>',
			),

			// Logística (3PL).
			'lg-onboarding-cliente' => array(
				'¿Cuánto tiempo lleva integrar a un nuevo cliente?',
				'<p>El plazo depende de cuántos sistemas entran en el flujo, pero la ganancia viene de la reutilización: los conectores para ERPs y WMS ya existen y se reaprovechan de un contrato a otro. En la práctica, lo que era un proyecto de integración desde cero pasa a ser la configuración de un flujo ya validado; es lo que sustenta la reducción del 50% en el tiempo de integración de socios y sistemas citada en esta página.</p>',
			),
			'lg-avaliar-plataforma-3pl' => array(
				'¿Qué evaluar en una plataforma para operadores logísticos 3PL?',
				'<p>Tres puntos suelen decidir la elección: si la plataforma reaprovecha integraciones entre clientes u obliga a empezar de cero en cada contrato; si gobierna en el mismo entorno los sistemas en la nube y los instalados en la infraestructura del cliente; y si el modelo acompaña los picos estacionales sin exigir capacidad contratada todo el año. Vale mirar también la traza de auditoría, ya que el operador responde por datos de terceros.</p>',
			),
			'lg-erp-on-premises' => array(
				'¿La plataforma conecta ERPs instalados on-premises?',
				'<p>Sí. La conexión con ERPs y WMS instalados en la red del cliente se hace mediante un agente dentro de la propia infraestructura, que abre la comunicación de dentro hacia fuera, sin exponer puertos de entrada en el firewall. Los flujos en la nube y on-premises quedan bajo el mismo entorno de gobernanza y monitoreo.</p>',
			),
			'lg-custo-alto-volume' => array(
				'¿Cómo funciona el costo para operaciones de alto volumen?',
				'<p>El dimensionamiento considera el volumen procesado y la cantidad de integraciones activas, no el número de usuarios. Como los flujos filtran y agregan los datos en el camino, el costo de las operaciones grandes tiende a crecer menos que proporcionalmente al número de pedidos y eventos, y los picos estacionales se absorben con el procesamiento elástico.</p>',
			),
			'lg-multiplas-transportadoras' => array(
				'¿Es posible integrar varios transportistas sin crear una integración para cada uno?',
				'<p>Sí, es uno de los casos de uso de esta página. En lugar de una conexión dedicada por transportista, la integración se centraliza: recolección, rastreo y entrega pasan por un flujo común y cada transportista entra como una configuración más. Incorporar uno nuevo deja de ser un proyecto de desarrollo.</p>',
			),

			// Seguros.
			'sg-prazo-guidewire-duck-creek' => array(
				'¿Cuánto tiempo lleva integrar Guidewire o Duck Creek?',
				'<p>El plazo depende de cuántos procesos entran en la primera oleada, no del tamaño del core. Como la conexión se hace mediante una capa de integración sobre APIs ya existentes, un flujo bien delimitado — la emisión de una póliza o la apertura de un siniestro, por ejemplo — suele salir en semanas y no en meses. El camino habitual es empezar por un proceso de alto volumen, ponerlo en producción y seguir ampliando a partir de él.</p>',
			),
			'sg-plataforma-vs-conectores' => array(
				'¿Cuál es la ventaja de utilizar una plataforma en lugar de conectores nativos?',
				'<p>Los conectores nativos resuelven bien un par de sistemas a la vez, pero cada nueva punta se convierte en un proyecto propio, con su propio monitoreo y su propio mantenimiento. Una plataforma trata la integración como una capa única: el mismo entorno conecta el core de seguros, el CRM, los portales de corredores y los servicios en la nube, con gobernanza, versionado y traza de auditoría centralizados. La ganancia aparece cuando crece el número de integraciones.</p>',
			),
			'sg-criterios-escolha' => array(
				'¿Qué deben evaluar las aseguradoras al elegir una plataforma de integración?',
				'<p>Cuatro puntos suelen decidir: si la plataforma dialoga con los sistemas core del mercado sin desarrollo a medida; si cumple las exigencias regulatorias de tratamiento de datos confidenciales; si registra cada ejecución de forma auditable; y si el equipo interno logra crear flujos nuevos sin depender de terceros. El quinto punto, menos citado, es el costo de mantener las integraciones vivas a lo largo de los años.</p>',
			),
			'sg-modernizar-sem-trocar-core' => array(
				'¿Es posible modernizar la operación sin sustituir el sistema core?',
				'<p>Sí, es justamente la propuesta de este enfoque. El core sigue siendo la fuente de verdad para pólizas y siniestros, y la capa de integración expone esos datos a los canales digitales, el CRM y los socios. En la práctica, la aseguradora lanza productos y experiencias nuevas sobre el sistema que ya tiene, sin cargar con el riesgo y el plazo de una sustitución completa.</p>',
			),
			'sg-open-insurance' => array(
				'¿Cómo atiende la plataforma los requisitos del Open Insurance brasileño?',
				'<p>El Open Insurance exige exponer y consumir datos mediante APIs estandarizadas, con consentimiento del cliente y trazabilidad de cada intercambio. La plataforma cubre ese diseño: publica APIs en los estándares definidos por la SUSEP, controla la autenticación y el alcance de cada consentimiento y mantiene el registro de todas las llamadas. Así, la adecuación regulatoria se apoya en la misma capa que ya conecta los sistemas internos.</p>',
			),

			// Varejo.
			'vj-composable-commerce' => array(
				'¿Por qué la integración es esencial para una estrategia de composable commerce?',
				'<p>El composable commerce cambia la plataforma única por piezas elegidas una a una — vitrina, carrito, búsqueda, pago, OMS — y es justamente eso lo que traslada el peso a la capa de integración. Sin ella, cada pieza nueva se convierte en una conexión punto a punto con todas las demás. Con una capa de integración en el medio, cada sistema dialoga una sola vez con esa capa, y cambiar un componente deja de significar rehacer toda la arquitectura.</p>',
			),
			'vj-experiencia-cliente' => array(
				'¿Cómo mejora la integración la experiencia del cliente?',
				'<p>La mayor parte de la fricción que percibe el consumidor nace de datos desencontrados: inventario que no coincide entre tienda y sitio, un pedido que la atención no ve, una promoción que solo vale en un canal. Cuando ventas, atención, inventario y logística comparten la misma información actualizada, la jornada es consistente en cualquier punto de contacto, y la atención responde con el historial completo a mano.</p>',
			),
			'vj-cadeia-suprimentos' => array(
				'¿Cómo reducir los impactos de la incertidumbre en la cadena de suministro?',
				'<p>Reduciendo el tiempo entre lo que ocurre en la cadena y lo que la operación ve. Con proveedores, ERP, WMS y transportistas integrados, el quiebre de stock, el retraso de suministro y el cambio de plazo aparecen cuando todavía se puede reaccionar — redistribuir inventario entre tiendas, activar un proveedor alternativo o reprogramar la reposición — en lugar de convertirse en una sorpresa en el cierre del mes.</p>',
			),
			'vj-ultima-milha' => array(
				'¿CLI Connect ayuda en la optimización de la última milla?',
				'<p>Sí. La plataforma conecta el pedido con los sistemas de logística, los transportistas y los ruteadores, de modo que la decisión sobre el origen de la entrega, la elección del transportista y la ruta consideren el inventario real, el plazo prometido y el costo. El rastreo vuelve por el mismo camino y alimenta el seguimiento del cliente y los indicadores de la operación, sin hojas de cálculo de por medio.</p>',
			),
			'vj-visao-360' => array(
				'¿Cuáles son los beneficios de construir una visión 360º del cliente?',
				'<p>Reunir compras, atenciones, devoluciones e interacciones de marketing en un perfil único cambia lo que la operación puede hacer: recomendaciones basadas en historial real, campañas que no repiten la oferta de un producto ya comprado, atención que no pide la misma información dos veces y una lectura confiable de la recurrencia y del valor del cliente a lo largo del tiempo.</p>',
			),
		);
	}

	/**
	 * Hotelaria e Turismo, Recursos Humanos (RH), Marketing,
	 * Operações de Receita (RevOps) e Financeiro.
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function faq_es_bloco_7() {
		return array(
			// Hotelaria e Turismo.
			'ht-pms-legado' => array(
				'¿Es posible integrar PMS legados instalados localmente?',
				'<p>Sí. Los PMS antiguos que corren en el servidor de la propiedad siguen siendo el caso más común en la hotelería, y no hace falta reemplazarlos para integrar. La conexión se hace mediante un agente instalado dentro de la propia red del hotel, que habla con el sistema por el recurso que ya ofrece — base de datos, archivo, servicio web o cola — y abre la comunicación de dentro hacia fuera, sin exponer puertos de entrada en el firewall. El PMS sigue como está y pasa a alimentar a los demás sistemas.</p>',
			),
			'ht-pos-fidelidade' => array(
				'¿Cómo integrar los sistemas de POS con el programa de fidelidad en tiempo real?',
				'<p>Cada consumo registrado en el POS — restaurante, bar, spa, frigobar — se convierte en un evento que la plataforma envía al instante al programa de fidelidad, ya asociado al perfil del huésped por la reserva activa. El camino de vuelta también es automático: saldo, categoría y beneficios regresan al POS y al PMS, de modo que el descuento o la cortesía aparecen en la misma atención, sin que el operador consulte otro sistema.</p>',
			),
			'ht-tempo-producao' => array(
				'¿Cuánto tiempo lleva poner una integración en producción?',
				'<p>Depende mucho menos del plazo de desarrollo que del acceso a los sistemas. Los flujos que usan conectores ya listos y una API documentada suelen entrar en semanas; lo que estira el cronograma es la liberación de credenciales, la homologación con el proveedor del PMS y la limpieza de los registros. Como los componentes son reutilizables, la primera integración es la más lenta y las siguientes aprovechan lo ya construido.</p>',
			),
			'ht-alta-demanda' => array(
				'¿La plataforma soporta grandes volúmenes de reservas en períodos de alta demanda?',
				'<p>Sí, y es justamente para el pico que se pensó la arquitectura. El procesamiento es elástico y trabaja sobre colas, así que un feriado, la temporada alta o una promoción relámpago aumentan la cola sin tumbar el flujo ni perder mensajes. Si un sistema de destino queda lento o indisponible, los mensajes se retienen y se reprocesan automáticamente cuando vuelve, preservando el orden de los eventos de cada reserva.</p>',
			),
			'ht-franquias-padronizacao' => array(
				'¿Cómo estandarizar integraciones entre franquicias con sistemas diferentes?',
				'<p>La estandarización ocurre en el medio del camino, no en las puntas. Se define un formato único para reserva, huésped y consumo, y cada propiedad recibe solo el tramo de traducción de su sistema a ese formato: el resto del flujo es el mismo para toda la red. Una unidad nueva entra reaprovechando el modelo, y quien opera la red pasa a ver todas las propiedades con los mismos indicadores, aun con PMS diferentes en cada una.</p>',
			),

			// Recursos Humanos (RH).
			'rh-hris-folha-prazo' => array(
				'¿Cuánto tiempo lleva integrar el HRIS con la nómina?',
				'<p>El plazo depende mucho menos del desarrollo que del acceso a los sistemas y de la calidad de los registros. Cuando el HRIS y la nómina exponen APIs documentadas y las credenciales ya están liberadas, el flujo de ingresos, movimientos y salidas suele entrar en producción en semanas. Lo que estira el cronograma es la conciliación de registros divergentes entre ambos sistemas y la homologación con el proveedor. Como los componentes son reutilizables, la primera integración es la más lenta y las siguientes aprovechan lo ya construido.</p>',
			),
			'rh-autonomia-do-time' => array(
				'¿RR. HH. puede gestionar las integraciones sin depender del equipo de desarrollo?',
				'<p>En el día a día, sí. El equipo de RR. HH. acompaña las ejecuciones, ve dónde se detuvo un registro y reprocesa lo que falló desde un panel propio, sin abrir un ticket. Los cambios estructurales — incorporar un sistema nuevo al flujo o alterar las reglas de un campo — siguen pasando por quien mantiene la integración, pero parten de componentes listos, así que son ajustes de configuración y no de proyecto.</p>',
			),
			'rh-criterios-plataforma' => array(
				'¿Qué criterios debo evaluar al elegir una plataforma de integración para RR. HH.?',
				'<p>Tres puntos pesan más que la lista de conectores. El primero es el tratamiento de datos personales: la plataforma debe detectar y enmascarar la información sensible antes de moverla, no después. El segundo es la trazabilidad: cada ingreso, movimiento y salida debe dejar registro de cuándo pasó, adónde fue y qué ocurrió si falló. El tercero es el reaprovechamiento: los flujos armados como componentes reducen el costo de cada nueva integración, mientras que las integraciones punto a punto crecen en mantenimiento con cada sistema añadido.</p>',
			),
			'rh-dados-sensiveis' => array(
				'¿Cómo se protegen los datos sensibles de los colaboradores durante las integraciones?',
				'<p>La protección ocurre dentro del propio flujo. Campos como el documento de identidad, los datos bancarios y la información de salud se identifican automáticamente y se enmascaran antes de seguir hacia el sistema de destino, de modo que solo quien necesita el dato completo lo recibe. El tráfico está cifrado de extremo a extremo, el acceso se concede por perfil y cada movimiento queda registrado en una traza de auditoría: es lo que sustenta el cumplimiento de los requisitos de la LGPD sin depender de la disciplina manual.</p>',
			),
			'rh-mudanca-de-api' => array(
				'¿Cómo impactan los cambios en las APIs de los proveedores de HRIS en las integraciones?',
				'<p>El impacto queda contenido en la capa de traducción. Cada sistema dialoga con un formato interno común, así que una versión nueva de la API del HRIS exige ajustar solo el tramo que habla con él: el resto del flujo, incluidas nómina, identidad y capacitación, sigue inalterado. Las versiones nuevas se homologan en un entorno separado antes de entrar en producción, y el monitoreo avisa cuando un endpoint cambia de comportamiento, en lugar de que la falla aparezca en el cierre de la nómina.</p>',
			),

			// Marketing.
			'mkt-velocidade-sincronizacao' => array(
				'¿Cuál es la velocidad de la sincronización entre la plataforma de marketing y el CRM?',
				'<p>Los flujos trabajan por evento, no por lote: en cuanto se crea o se actualiza un lead, el mensaje entra en la cola y llega al otro sistema en pocos segundos — la referencia de proyecto es mantener el ciclo por debajo de un minuto. Lo que suele pesar en ese tiempo no es la plataforma de integración, sino los límites de API del sistema de destino, que se respetan automáticamente para evitar bloqueos por exceso de llamadas.</p>',
			),
			'mkt-marketing-operations' => array(
				'¿El equipo de Marketing Operations puede gestionar las integraciones sin depender de TI?',
				'<p>En gran parte, sí. El diseño de los flujos es low-code y los paneles de seguimiento muestran volumen, errores y reprocesamiento sin exigir la lectura de logs técnicos, así que los ajustes de mapeo, campos y reglas de segmentación quedan en manos del propio equipo de marketing. TI sigue en el circuito para lo que le corresponde — liberar credenciales, aprobar accesos a sistemas internos y definir políticas de datos —, pero deja de ser una fila para cada cambio de campaña.</p>',
			),
			'mkt-ipaas-vs-nativas' => array(
				'¿Cuál es la diferencia entre un iPaaS y las integraciones nativas de las plataformas de automatización de marketing?',
				'<p>Las integraciones nativas resuelven bien el par de sistemas para el que fueron hechas, con el mapeo que el proveedor decidió ofrecer. Un iPaaS trabaja en el medio del camino: conecta marketing, CRM, ERP, medios pagos y analytics con la misma lógica, permite transformar y enriquecer el dato en tránsito, aplica reglas propias de deduplicación y deja todo el historial auditable en un solo lugar. En la práctica, la nativa alcanza mientras el ecosistema es pequeño; el iPaaS es lo que sustenta el crecimiento sin multiplicar conexiones punto a punto.</p>',
			),
			'mkt-criterios-plataforma' => array(
				'¿Qué criterios debo evaluar al elegir una plataforma de integración para Marketing?',
				'<p>Vale mirar cinco puntos: la cobertura de conectores para las herramientas que ya están en uso; la capacidad de procesar picos de lanzamiento y campañas estacionales sin perder mensajes; la autonomía que gana el equipo de marketing para operar sin abrir tickets; la visibilidad sobre errores y reprocesamiento; y el tratamiento de los datos personales, incluidos por dónde circulan y dónde se almacenan. El costo total también cuenta: además de la licencia, considere quién va a operar y monitorear la plataforma en el día a día.</p>',
			),
			'mkt-lgpd-gdpr' => array(
				'¿Cómo garantiza la plataforma el cumplimiento de la LGPD y el GDPR durante el tránsito de los datos?',
				'<p>Los datos viajan cifrados de extremo a extremo y la conexión con los sistemas internos se hace mediante un agente que se comunica de dentro hacia fuera, sin exponer puertos de entrada en el firewall. Cada flujo registra quién accedió a qué y cuándo, lo que sustenta las solicitudes de auditoría y de eliminación previstas en ambas leyes. Los campos sensibles pueden enmascararse o simplemente no circular, y las reglas de consentimiento y opt-out se aplican en el propio flujo, de modo que un contacto que revocó el permiso deja de distribuirse a las demás plataformas.</p>',
			),

			// Operações de Receita (RevOps).
			'revops-crm-automacao-prazo' => array(
				'¿Cuánto tiempo lleva conectar el CRM con la plataforma de automatización de marketing?',
				'<p>El desarrollo suele ser la parte corta: el CRM y las herramientas de automatización de marketing tienen APIs bien documentadas y conectores listos, así que un flujo de leads y oportunidades entra en producción en pocas semanas. Lo que estira el cronograma es la decisión de negocio: definir qué sistema manda en cada campo, qué caracteriza a un lead calificado y cómo tratar la base duplicada que ya existe. Conviene empezar por un solo flujo, ponerlo en producción y ampliar a partir de él.</p>',
			),
			'revops-sem-desenvolvedor' => array(
				'¿El equipo de RevOps puede crear integraciones sin desarrolladores dedicados?',
				'<p>Sí, para la mayor parte del día a día. El builder visual arma el flujo arrastrando y conectando etapas, con apoyo de IA a la hora de mapear campos y sugerir tratamientos, y quien conoce el proceso comercial logra crear, ajustar y monitorear las automatizaciones sin escribir código. TI sigue entrando donde tiene sentido — liberación de credenciales, revisión de flujos críticos y casos que exigen lógica más elaborada —, pero deja de ser un cuello de botella para cada pequeño ajuste.</p>',
			),
			'revops-ponto-a-ponto-ipaas' => array(
				'¿Cuál es la diferencia entre una integración punto a punto y un iPaaS?',
				'<p>Una integración punto a punto conecta dos sistemas directamente y funciona bien mientras sean dos. El problema aparece con la escala: cada nueva herramienta multiplica las conexiones, cada una con su propia lógica y su propio manejo de errores, y nadie ve el conjunto. Un iPaaS coloca una capa en el medio: los sistemas dialogan con ella, no entre sí. Eso centraliza el monitoreo, reaprovecha los mapeos y hace que cambiar una herramienta signifique rehacer un tramo y no toda la red.</p>',
			),
			'revops-mudanca-api' => array(
				'¿Cómo impactan los cambios en las APIs de los sistemas en las integraciones?',
				'<p>Los cambios de versión son esperables y se tratan en la capa de integración, no en cada flujo. Como el mapeo entre el formato de cada sistema y el formato interno queda aislado, una alteración de API suele exigir un ajuste en un solo punto, sin tocar los flujos que dependen de él. El monitoreo señala la falla en cuanto ocurre, los mensajes afectados quedan retenidos en cola y se reprocesan después de la corrección, sin pérdida de registros.</p>',
			),
			'revops-protecao-dados' => array(
				'¿Cómo se protegen los datos comerciales durante las integraciones?',
				'<p>El tráfico está cifrado de extremo a extremo y las credenciales de cada sistema quedan en una bóveda, nunca dentro del flujo. El acceso se concede por perfil, de modo que quien opera las automatizaciones no necesita ver el contenido sensible que pasa por ellas, y todo movimiento queda registrado en una traza de auditoría: quién cambió qué, cuándo y con qué resultado. La operación sigue los estándares de compliance y seguridad listados en esta página.</p>',
			),

			// Financeiro.
			'fin-tempo-erp' => array(
				'¿Cuánto tiempo lleva integrar SAP, Oracle o NetSuite?',
				'<p>Los tres ya cuentan con conectores listos, así que el cronograma depende menos del desarrollo y más del acceso al entorno. Los flujos habituales del área financiera — balance de comprobación, asientos contables, cuentas por pagar — suelen entrar en semanas, contadas desde la liberación de credenciales y la aceptación del diseño por parte del equipo contable. Lo que estira el plazo es la personalización pesada en el ERP y la divergencia de plan de cuentas entre unidades, no la conexión en sí.</p>',
			),
			'fin-autonomia-financeiro' => array(
				'¿El equipo financiero puede acompañar las integraciones sin depender de TI?',
				'<p>Sí. El seguimiento del día a día — si el lote de la noche corrió, cuántos asientos entraron, qué registro falló y por qué — queda en un panel de operación al que el área financiera accede directamente, con reprocesamiento de lo que dio error sin abrir un ticket. Lo que sigue con TI es el cambio estructural: crear un flujo nuevo, cambiar credenciales o modificar una regla de negocio.</p>',
			),
			'fin-nativa-vs-ipaas' => array(
				'¿Cuál es la diferencia entre las integraciones nativas del ERP y un iPaaS?',
				'<p>La integración nativa resuelve bien el par de sistemas para el que fue hecha, pero cada nueva punta se convierte en un proyecto aislado, con su propia regla, su propio log y su propio mantenimiento. El iPaaS coloca una capa única entre todos los sistemas: las reglas de transformación, el historial de ejecución, el manejo de errores y la gobernanza de acceso quedan en un solo lugar, y una unidad de negocio nueva reaprovecha lo ya construido en lugar de empezar de nuevo.</p>',
			),
			'fin-criterios-plataforma' => array(
				'¿Qué criterios debo evaluar al elegir una plataforma de integración para Finanzas?',
				'<p>Empiece por la trazabilidad: todo movimiento debe tener registro completo de lo que entró, de lo que salió y de quién lo modificó, porque es eso lo que sustenta la auditoría. Después verifique el catálogo de conectores para los ERPs y bancos que ya usa, el comportamiento ante fallas (reprocesamiento sin duplicar asientos), el control de accesos por perfil y dónde ocurre la ejecución — dentro de su infraestructura, cuando la política corporativa lo exija. Por último, evalúe el modelo de evolución: la integración financiera cambia todo el tiempo, y depender de un nuevo proyecto en cada ajuste sale caro.</p>',
			),
			'fin-atualizacao-apis' => array(
				'¿Cómo impactan las actualizaciones de las APIs de los ERPs en las integraciones?',
				'<p>El impacto queda contenido en la capa de conexión. Cuando el proveedor publica una versión nueva, es el conector el que se actualiza: los flujos, las reglas y los destinos siguen como están. Los cambios anunciados con antelación se homologan en un entorno separado antes de entrar en producción; cuando algo se rompe sin aviso, los mensajes quedan retenidos y se reprocesan después de la corrección, sin pérdida de asientos ni duplicidades.</p>',
			),
		);
	}

	/**
	 * Atualização de Sistemas Legados, Integração Pós-Fusão, IA Corporativa,
	 * Compras ao Pagamento (S2P), Visão 360° do Cliente e Soberania de Dados.
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function faq_es_bloco_8() {
		return array(
			// Atualização de Sistemas Legados.
			'legado-mainframe-rede' => array(
				'¿Es posible conectar mainframes sin alterar la infraestructura de red?',
				'<p>Sí. El Runtime se instala dentro del propio entorno, del mismo lado del firewall en el que ya vive el mainframe, y es él quien abre la conexión hacia fuera, no al revés. Los entornos z/OS y AS/400 siguen donde están, con las mismas reglas de red, sin VPN dedicada ni puertos nuevos expuestos a internet. Lo que cambia es solo quién pasa a dialogar con esos sistemas: la capa de integración, en lugar de cada aplicación por separado.</p>',
			),
			'legado-esb-transicao' => array(
				'¿Las integraciones siguen funcionando durante la sustitución del ESB?',
				'<p>Siguen funcionando. La transición se hace ruta por ruta: la nueva arquitectura se levanta en paralelo al ESB actual y cada flujo solo se redirige después de correr en producción con el mismo resultado que el anterior. Mientras una ruta se reconstruye, la versión legada sigue activa, lo que permite volver atrás sin detener la operación. El ESB se apaga únicamente cuando no queda ningún flujo dependiendo de él.</p>',
			),
			'legado-esb-vs-ipaas' => array(
				'¿Cuál es la diferencia entre sustituir un ESB por una plataforma moderna de integración?',
				'<p>El ESB tradicional concentra las reglas en código propietario, exige especialistas en la tecnología específica y trata cada cambio como un proyecto nuevo. La plataforma moderna lleva esos mismos flujos a un entorno visual, sobre estándares abiertos y portables, con historial de ejecución, manejo de errores y gobernanza de acceso en un solo lugar. En la práctica, la ganancia no es solo tecnológica: es la reducción de la dependencia de un grupo pequeño de personas para mantener la integración en pie.</p>',
			),
			'legado-prazo-reconstrucao' => array(
				'¿Cuánto tiempo lleva reconstruir las integraciones existentes?',
				'<p>Depende mucho más de la cantidad de reglas de negocio incrustadas en la ruta antigua que de la tecnología de origen. Los flujos directos — una lectura, una transformación, una entrega — suelen reconstruirse y homologarse en semanas. Lo que estira el plazo es la arqueología: rutas sin documentación, transformaciones escritas en código dentro del ESB y reglas que ya nadie conoce deben mapearse antes de reescribirse. Por eso la sustitución se hace por oleadas, empezando por las rutas de mayor volumen y menor complejidad.</p>',
			),
			'legado-pos-desativacao' => array(
				'¿Qué pasa después de que el ESB queda completamente desactivado?',
				'<p>La operación pasa a vivir en la nueva capa, y el costo de licencia y sostenimiento de la plataforma antigua sale de la cuenta. A partir de ahí, la integración deja de ser un proyecto y se vuelve rutina: nuevas conexiones, cambios de reglas y ajustes de flujo entran por el mismo entorno, con monitoreo centralizado y sin necesidad de un especialista en la tecnología legada. Es también el momento en que la arquitectura orientada a eventos empieza a rendir: las aplicaciones nuevas se conectan a lo que ya existe en lugar de abrir otra integración punto a punto.</p>',
			),

			// Integração Pós-Fusão.
			'ipf-runtime-firewall' => array(
				'¿Cómo elimina el Runtime el problema del firewall en el Día 1?',
				'<p>El runtime corre dentro del entorno de la empresa adquirida y abre la conexión de dentro hacia fuera: es él quien busca a la plataforma, nunca al revés. Como no existe un puerto de entrada que publicar, no hay una regla de borde nueva, ni IP fija que negociar, ni excepción que aprobar por el equipo de seguridad de la otra compañía. Es justamente ese punto el que suele trabar las primeras semanas de una adquisición, cuando las dos redes todavía son independientes y nadie quiere flexibilizar el perímetro.</p>',
			),
			'ipf-antes-consolidacao-ti' => array(
				'¿Es posible conectar sistemas antes de la consolidación de TI?',
				'<p>Sí, y es el escenario más común. La integración ocurre en la capa de datos y procesos, sobre los sistemas tal como están hoy: dos ERPs, dos directorios de identidad, dos nóminas. Identidad, RR. HH. y ERP pueden conectarse incluso antes del cierre del negocio, para que la operación siga en pie el primer día. La consolidación definitiva sigue su propio cronograma, sin bloquear la captura de sinergias.</p>',
			),
			'ipf-velocidade-deploy' => array(
				'¿Cuál es la velocidad de despliegue para dejar la operación lista en el Día 1?',
				'<p>El runtime se levanta en multi-cloud o en Kubernetes gestionado, así que el aprovisionamiento del entorno es cuestión de horas, no de un proyecto. Lo que define el cronograma es el acceso: la liberación de credenciales en los sistemas de origen y destino, y la aceptación del diseño por las áreas involucradas. Con más de 300 conectores listos y sin costo adicional, los flujos críticos del Día 1 — acceso, nómina, pedidos — normalmente entran en semanas y no en meses.</p>',
			),
			'ipf-pipelines-pos-projeto' => array(
				'¿Los pipelines siguen generando valor después del proyecto de integración?',
				'<p>Siguen. Cada integración se construye como una cápsula reutilizable, con el patrón de mapeo, el manejo de errores y la gobernanza ya definidos. Terminada la incorporación, esas cápsulas se vuelven el repertorio de la empresa para la próxima adquisición: lo que se hizo para conectar un ERP o un directorio de identidad se reaprovecha, en lugar de empezar de cero. También siguen sosteniendo la operación corriente: sincronización de registros, datos analíticos y procesos entre las unidades combinadas.</p>',
			),
			'ipf-substituir-middleware-legado' => array(
				'¿Cómo sustituir el middleware legado de la empresa adquirida?',
				'<p>El cambio se hace flujo por flujo, sin big bang. El primer paso es inventariar qué ejecuta realmente el middleware antiguo y con qué frecuencia; luego los flujos se reconstruyen en la plataforma y corren en paralelo con el legado, comparando resultados antes del corte. Cada flujo migrado se apaga del lado antiguo solo después de estar estable, lo que mantiene la operación funcionando durante toda la transición y evita concentrar el riesgo en una única fecha.</p>',
			),

			// IA Corporativa.
			'ia-provedores-llm' => array(
				'¿Qué proveedores de LLM son compatibles de forma nativa?',
				'<p>La conexión con el modelo es una punta más de la integración, así que vale para cualquier proveedor que exponga API, incluidos los grandes proveedores de nube y los modelos abiertos alojados en su propia infraestructura. En la práctica, eso significa que el agente dialoga con el modelo por el mismo camino por el que dialoga con el ERP: credenciales guardadas en un solo lugar, llamada registrada y límite de costo aplicado antes de que la solicitud salga.</p>',
			),
			'ia-pipelines-mcp' => array(
				'¿Cómo se convierten los pipelines de integración en herramientas MCP?',
				'<p>Un flujo que ya existe — consultar un pedido en SAP, abrir un ticket en ServiceNow, buscar la ficha de un cliente en Salesforce — se publica como herramienta, con la descripción de lo que hace, los parámetros que acepta y el retorno que devuelve. El agente pasa a ver esa herramienta en el catálogo y a llamarla cuando la necesite, sin acceso directo al sistema de origen: la autenticación, el control de permisos por perfil y el registro de la ejecución siguen en la capa de integración.</p>',
			),
			'ia-vs-data-factory-glue' => array(
				'¿Cuál es la diferencia entre este enfoque y Azure Data Factory o AWS Glue?',
				'<p>Data Factory y Glue son herramientas de pipeline de datos: mueven volumen de un punto a otro en lote, para alimentar un data warehouse. Lo que exige la IA corporativa es diferente: una respuesta en vivo, para una pregunta específica, en el instante en que el agente pregunta, y la capacidad de ejecutar una acción de vuelta en el sistema de origen. Por eso la capa aquí es de integración de aplicaciones y no de ETL, y por eso expone herramientas y eventos además de tablas.</p>',
			),
			'ia-tempo-rag' => array(
				'¿Cuánto tiempo lleva poner RAG en producción?',
				'<p>Con los conectores de Confluence y SharePoint ya listos, el cronograma depende menos del desarrollo y más del acceso y la curaduría: liberar credenciales, decidir qué espacios entran en la base y definir quién puede ver qué. Un primer alcance bien delimitado suele entrar en semanas. Lo que estira el plazo es una base documental desorganizada y permisos heredados de forma inconsistente en el origen: en esos casos el trabajo de limpieza es mayor que el de integración.</p>',
			),
			'ia-troca-de-provedor' => array(
				'¿Mi arquitectura sigue siendo flexible al cambiar de proveedor de IA?',
				'<p>Sí, porque el modelo queda detrás de la capa de integración, no en medio de ella. Las reglas de negocio, las herramientas publicadas, los guardrails y el historial de ejecución pertenecen a la plataforma; cambiar de proveedor es cambiar la credencial y el endpoint de una punta, manteniendo todo lo demás en su lugar. Esto también permite ejecutar más de un modelo en paralelo — uno para tareas simples, otro para las costosas — sin duplicar la integración.</p>',
			),

			// Compras ao Pagamento (S2P).
			's2p-matching-3-vias' => array(
				'¿Cómo automatizar el matching de 3 vías entre pedido, recepción y factura?',
				'<p>La integración lee los tres documentos donde nacen — el pedido de compra en el ERP, el registro de recepción en el almacén o en el WMS y la factura enviada por el proveedor — y compara ítem por ítem cantidad, precio y condición comercial. Cuando los tres coinciden dentro de las tolerancias definidas por la empresa, la factura sigue directo a pago; cuando hay divergencia, el flujo se detiene y avisa al responsable con el motivo exacto de la diferencia. El equipo financiero deja de revisar hoja por hoja y pasa a tratar solo las excepciones.</p>',
			),
			's2p-visibilidade-gastos' => array(
				'¿Es posible dar visibilidad del gasto en tiempo real al área financiera?',
				'<p>Sí. Como cada etapa del ciclo pasa por la integración, el compromiso financiero se registra en el momento en que ocurre: la requisición aprobada, el pedido emitido, la recepción confirmada y la factura liberada. Esos datos se consolidan por categoría, centro de costo y proveedor y se envían al ERP o a la herramienta de BI de la empresa, lo que permite acompañar el gasto comprometido antes de que se convierta en gasto contabilizado, y negociar con base en el volumen real por proveedor.</p>',
			),
			's2p-segregacao-funcoes' => array(
				'¿Cómo funciona la segregación de funciones entre aprobación y pago?',
				'<p>Aprobar y pagar son etapas distintas del flujo, con permisos distintos: quien autoriza la compra no es quien ejecuta la liberación financiera, y la integración respeta los roles ya definidos en el ERP y en el sistema de aprobación. Cada transición registra quién actuó, cuándo y sobre qué documento, formando un historial completo de aprobaciones disponible para auditoría. Ningún pago se dispara sin que la etapa anterior haya sido completada por el perfil autorizado.</p>',
			),

			// Visão 360° do Cliente.
			'v360-resolucao-identidade' => array(
				'¿Cómo resolver la identidad de un cliente entre sistemas diferentes?',
				'<p>Cada sistema guarda al cliente con su propia clave — el código en el ERP, el ID en el CRM, el correo en soporte — y por eso la misma empresa aparece tres veces con datos diferentes. La resolución de identidad cruza esos identificadores mediante reglas de correspondencia (documento, dominio de correo, razón social) y mantiene una tabla de equivalencias entre ellos. El perfil unificado pasa a ser la referencia, y cada sistema sigue funcionando con la clave que ya usa, sin migración de registros.</p>',
			),
			'v360-tempo-real-ou-batch' => array(
				'¿La visión 360º se actualiza en tiempo real o por lotes?',
				'<p>En tiempo real: cada cambio relevante en un sistema conectado — un pedido facturado, un ticket cerrado, un dato de registro corregido — dispara la actualización del perfil unificado en el momento en que ocurre, sin esperar la ventana nocturna. Las cargas por lotes siguen disponibles para lo que tiene sentido procesar en bloque, como la carga inicial de historial o las bases legadas, pero la operación del día a día no depende de ellas.</p>',
			),
			'v360-contexto-agente-ia' => array(
				'¿Cómo utiliza un agente de IA esa visión unificada?',
				'<p>El agente consulta el perfil unificado antes de responder o actuar, y recibe de una vez lo que estaría disperso entre CRM, ERP, soporte y producto: contratos vigentes, pedidos abiertos, tickets recientes y uso del producto. Con ese contexto, la respuesta deja de ser genérica y las acciones ejecutadas — abrir un ticket, actualizar un registro, escalar un caso — ocurren sobre datos actuales. Los mismos controles de lectura y escritura por sistema valen para el agente, así que solo ve y modifica lo autorizado.</p>',
			),

			// Soberania de Dados.
			'soberania-jurisdicao' => array(
				'¿Cómo garantiza CLI Connect powered by Boomi que los datos no salgan de la jurisdicción exigida?',
				'<p>El motor de ejecución corre dentro del entorno que usted indique: su cuenta en AWS, Azure o GCP, o el datacenter interno de la empresa. Es allí donde el dato se lee, se transforma y se graba; el plano de control de la plataforma se ocupa de la configuración, el versionado y el monitoreo, y no del contenido que circula. En la práctica, un registro que nace en una región solo sale de ella si un flujo que usted mismo diseñó lo manda salir.</p>',
			),
			'soberania-multi-regiao' => array(
				'¿Es posible tener un despliegue multirregión para operaciones en varios países?',
				'<p>Sí, y es el diseño más común en operaciones internacionales: un entorno de ejecución por país o bloque regulatorio, cada uno con su propia frontera de datos, todos administrados desde un único lugar. Los flujos se construyen una vez y se distribuyen a cada región, lo que evita mantener equipos paralelos cuidando integraciones casi idénticas, y permite que una regla local, cuando existe, aparezca como excepción explícita y no como una copia entera del proyecto.</p>',
			),
			'soberania-auditoria' => array(
				'¿Cómo funciona la auditoría de dónde se procesaron los datos?',
				'<p>Cada ejecución deja registro de qué entorno la procesó, cuándo, con qué versión del flujo y con qué resultado. Ese historial es lo que sustenta la respuesta a un auditor: en lugar de una declaración de política, usted muestra el rastro de ejecución por región. Los registros pueden mantenerse en su propio entorno y exportarse a la herramienta de observabilidad o de compliance que la empresa ya utiliza.</p>',
			),
		);
	}

	/**
	 * Centro de Excelência em Integração, Jornada do Colaborador (H2R),
	 * Pedido ao Recebimento (O2C), HubSpot CRM, TOTVS Consinco, TOTVS Linx,
	 * TOTVS RM e Arius ERP.
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function faq_es_bloco_9() {
		return array(
			// Centro de Excelência em Integração.
			'cei-catalogo-reutilizavel' => array(
				'¿Cómo estructurar un catálogo interno de integraciones reutilizables?',
				'<p>El catálogo empieza por el inventario de lo que ya existe: cada pipeline en producción se vuelve una entrada con responsable, sistemas conectados, contrato de entrada y salida y nivel de criticidad. A partir de ahí, los tramos que se repiten entre proyectos — autenticación, manejo de errores, transformación de un mismo objeto de negocio — se extraen en cápsulas versionadas, publicadas para toda la organización. La ganancia aparece en el segundo proyecto: en lugar de reconstruir la conexión desde cero, el equipo arma la integración a partir de piezas ya homologadas.</p>',
			),
			'cei-governanca-aprovacao' => array(
				'¿Cómo funciona la gobernanza de aprobación de nuevas integraciones?',
				'<p>Toda integración pasa por un flujo de revisión antes de llegar a producción: quien construye no es quien aprueba, y la promoción entre entornos exige la aceptación de un responsable técnico del Centro de Excelencia. Lo que se verifica en esa etapa es siempre el mismo conjunto: adherencia al estándar de nomenclatura, credenciales guardadas en la bóveda, manejo de errores y reintentos configurados, y ausencia de duplicidad respecto al catálogo. El acceso se controla por función, de forma que crear, modificar y publicar sean permisos distintos.</p>',
			),
			'cei-custo-e-performance' => array(
				'¿Es posible medir el costo y el rendimiento de cada integración de forma centralizada?',
				'<p>Sí. Como todas las integraciones corren en la misma plataforma y siguen el mismo estándar de instrumentación, el volumen procesado, el tiempo de ejecución, la tasa de error y el consumo de recursos quedan disponibles en un panel único, con corte por integración, por sistema conectado y por área responsable. Es ese dato el que sustenta las decisiones siguientes del Centro de Excelencia: qué flujos optimizar, cuáles consolidar y cuáles retirar por bajo uso.</p>',
			),

			// Jornada do Colaborador (H2R).
			'h2r-desligamento-acessos' => array(
				'¿Cómo garantizar que la salida de un colaborador revoque todos los accesos automáticamente?',
				'<p>La baja registrada en el sistema de RR. HH. se convierte en un evento único que la integración distribuye a todos los sistemas vinculados a ese colaborador: directorio de identidad, correo, VPN, ERP, beneficios, control de acceso físico y las herramientas de negocio que utilizaba. Cada revocación devuelve una confirmación, y lo que no confirma queda visible como pendiente en lugar de pasar inadvertido. La ventana entre la salida y el corte de acceso pasa a medirse en minutos, no en días de checklist manual.</p>',
			),
			'h2r-admissao-multiplos-sistemas' => array(
				'¿Es posible orquestar el ingreso de personal en varios sistemas simultáneamente?',
				'<p>Sí. El ingreso aprobado en el HRIS dispara una única activación que crea al colaborador en la nómina, abre la cuenta de correo, aprovisiona los accesos según el cargo, lo inscribe en los beneficios y lo matricula en las rutas del LMS. Las etapas que no dependen unas de otras ocurren en paralelo, y las que dependen respetan el orden: la credencial física solo se solicita después de que existe la identidad, por ejemplo. RR. HH. acompaña el avance en un solo lugar y el nuevo colaborador llega el primer día con todo liberado.</p>',
			),
			'h2r-auditoria-ciclo-de-vida' => array(
				'¿Cómo funciona la auditoría de cambios en el ciclo de vida del colaborador?',
				'<p>Todo cambio de estado — ingreso, promoción, traslado, cambio de banda salarial y salida — se registra con el evento que lo originó, los sistemas actualizados, la hora de cada actualización y el resultado devuelto por cada uno. Los datos personales sensibles circulan enmascarados, de modo que el historial se mantiene auditable sin exponer PII. Ese registro queda disponible para la auditoría interna y externa y es la misma base usada para los análisis de rotación y antigüedad.</p>',
			),

			// Pedido ao Recebimento (O2C).
			'o2c-tempo-venda-recebimento' => array(
				'¿Cómo reducir el tiempo entre el cierre de la venta y el cobro?',
				'<p>Lo que suele alargar el ciclo no es la venta ni la cobranza en sí, sino la espera entre ambas: el pedido cerrado en el CRM que solo se factura cuando alguien lo vuelve a digitar, la factura emitida que solo genera el cobro al día siguiente. Al conectar CRM, ERP y sistema de cobranza en un flujo único, cada etapa dispara la siguiente en cuanto termina la anterior, sin lote nocturno y sin digitación. La ganancia aparece primero en la consistencia de los datos — pedido, factura y título con los mismos valores — y solo después en el plazo promedio.</p>',
			),
			'o2c-multiplos-erps-crms' => array(
				'¿Es posible conectar varios ERPs y CRMs en el mismo flujo Order-to-Cash?',
				'<p>Sí, y es el escenario más común en empresas con varias unidades de negocio o que pasaron por adquisiciones. Las reglas de transformación quedan en la capa de integración, no dentro de cada sistema, así que un ERP más entra como una punta nueva del flujo que ya existe, reaprovechando el diseño de pedido, facturación y conciliación. El trabajo real está en conciliar los registros — cliente, producto, condición de pago — que suelen divergir entre las bases.</p>',
			),
			'o2c-conciliacao-bancos' => array(
				'¿Cómo funciona la conciliación automática con bancos y adquirentes?',
				'<p>La integración recibe los archivos de retorno y los extractos de las instituciones, cruza cada pago con el título correspondiente en el ERP y da de baja lo que cerró. Lo que no cruza — valor divergente, pago parcial, comisión de adquirente descontada — queda separado en una cola de excepción con el motivo, para que el área financiera lo trate caso por caso en lugar de revisarlo todo a mano. El historial de cada intento queda registrado, lo que sustenta la auditoría y permite reprocesar sin duplicar la baja.</p>',
			),
			'o2c-status-do-pedido' => array(
				'¿Cómo seguir el estado de un pedido de principio a fin?',
				'<p>Como el flujo pasa por una capa única, cada pedido lleva un identificador que atraviesa CRM, ERP, facturación y cobranza. Eso permite armar una visión de extremo a extremo — en qué etapa está el pedido, cuándo entró en ella, qué falló y qué se reprocesó — sin consultar sistema por sistema. Ventas, finanzas y logística pasan a mirar el mismo estado, lo que resuelve buena parte de las divergencias entre áreas antes de que se conviertan en retrabajo.</p>',
			),
			'o2c-sistemas-do-fluxo' => array(
				'¿Qué sistemas pueden formar parte del flujo Order-to-Cash?',
				'<p>Típicamente el CRM donde se cierra el pedido, el ERP que factura y emite el documento fiscal, la plataforma de cobranza o el medio de pago, los bancos y adquirentes que confirman el cobro, y las herramientas de gestión que consumen los indicadores de plazo promedio. También entran en el flujo los sistemas de logística, cuando la entrega condiciona la facturación, y las plataformas de crédito y cobranza, cuando hay análisis de límite o gestión de morosidad. La elección depende menos del catálogo de conectores y más de dónde están hoy las etapas manuales.</p>',
			),

			// HubSpot CRM.
			'hubspot-crm-erp' => array(
				'¿Cómo sincronizar los negocios cerrados de HubSpot directamente con el ERP?',
				'<p>Al cerrar un negocio en HubSpot CRM, CLI Connect detecta el evento vía webhook y activa automáticamente el flujo de integración configurado, creando el pedido, el contrato o el registro de cliente en el ERP sin intervención manual. El mapeo de campos se define una vez y puede ajustarse según las reglas de su proceso comercial.</p>',
			),
			'hubspot-crm-multiplos-portais' => array(
				'¿Es posible conectar varios portales de HubSpot de unidades diferentes?',
				'<p>Sí. CLI Connect soporta múltiples conexiones simultáneas con portales distintos de HubSpot CRM. Cada unidad de negocio opera con su propio conjunto de credenciales y flujos independientes, centralizados en una única plataforma de integración para facilitar la gobernanza.</p>',
			),
			'hubspot-crm-rate-limit' => array(
				'¿Cómo manejar los límites de tasa (rate limit) de la API?',
				'<p>CLI Connect gestiona automáticamente los límites de tasa de la API de HubSpot mediante colas y mecanismos de retry con backoff exponencial. En los picos de volumen — como importaciones por lotes o campañas de gran escala — los datos se procesan de forma controlada, sin errores ni pérdida de registros.</p>',
			),

			// TOTVS Consinco.
			'totvs-consinco-precos' => array(
				'¿Cómo sincronizar los precios entre la tienda física y la digital?',
				'<p>CLI Connect crea un flujo centralizado que captura los cambios de precios y promociones directamente en Consinco y los distribuye automáticamente al PDV y a la plataforma de e-commerce. Cualquier cambio de lista de precios, campaña promocional o descuento se propaga en tiempo real a todos los canales, lo que elimina divergencias de valores y retrabajo manual en los equipos comerciales.</p>',
			),
			'totvs-consinco-edi' => array(
				'¿Es posible integrar varios proveedores vía EDI rápidamente?',
				'<p>Sí. CLI Connect ofrece aceleradores de integración EDI que estandarizan el onboarding de nuevos proveedores. En lugar de construir un mapeo específico para cada socio, la plataforma reutiliza conectores EDI configurables compatibles con los principales formatos del sector. Esto reduce el tiempo de integración de semanas a días y facilita la incorporación de nuevos proveedores a medida que crece la operación.</p>',
			),
			'totvs-consinco-reposicao' => array(
				'¿Cómo funciona la reposición automática de inventario?',
				'<p>CLI Connect conecta los datos de rotación de ventas de Consinco con el sistema de compras y los proveedores, y crea un ciclo automatizado de reposición. Cuando el inventario de un producto alcanza el punto de pedido definido, la plataforma dispara automáticamente el proceso de compra con el proveedor correspondiente, sin necesidad de intervención manual. Esto reduce los quiebres de góndola y el exceso de inventario en toda la red.</p>',
			),

			// TOTVS Linx.
			'totvs-linx-bandeiras' => array(
				'¿Cómo consolidar las ventas de varias marcas Linx?',
				'<p>CLI Connect crea un hub central que agrega las transacciones de diferentes verticales de Linx — moda, farmacias, estaciones de servicio, conveniencia — y consolida los datos en un único repositorio financiero. Cada marca mantiene su operación independiente en el PDV, pero los resultados se consolidan automáticamente en el ERP corporativo. Esto elimina las reconciliaciones manuales y garantiza visibilidad en tiempo real del desempeño de toda la red.</p>',
			),
			'totvs-linx-fidelidade' => array(
				'¿Es posible integrar programas de fidelidad de terceros?',
				'<p>Sí. CLI Connect conecta Linx con cualquier plataforma de CRM o fidelidad vía API, sean soluciones propietarias o de terceros. Los datos de compra registrados en el PDV se envían automáticamente al programa de fidelidad, que procesa puntos y beneficios y devuelve la información a la caja en tiempo real. La integración es configurable y reutilizable para cada nuevo socio de fidelidad.</p>',
			),
			'totvs-linx-fiscal' => array(
				'¿Cómo funciona la integración fiscal centralizada (SAT/NFC-e)?',
				'<p>CLI Connect centraliza la emisión y el almacenamiento de los documentos fiscales generados por Linx PDV — SAT, NF-e y NFC-e — en un repositorio único integrado al ERP. Cualquier documento emitido por las tiendas se transmite, valida y almacena automáticamente de forma estructurada, lo que facilita las obligaciones accesorias, las auditorías y la conciliación fiscal sin depender de procesos manuales por tienda.</p>',
			),

			// TOTVS RM.
			'totvs-rm-admissao' => array(
				'¿Cómo orquestar el ingreso y la salida de personal entre RM y otros sistemas?',
				'<p>CLI Connect crea un proceso centralizado que se dispara automáticamente cuando un ingreso o una salida se registra en RM Folha. El flujo aprovisiona o desactiva al colaborador en el Active Directory, notifica a la plataforma de beneficios y sincroniza el perfil en el LMS, todo sin intervención manual. Cada etapa se audita y puede monitorearse en tiempo real desde el panel de la plataforma.</p>',
			),
			'totvs-rm-nucleo' => array(
				'¿Es posible conectar RM Núcleo con un portal del alumno de terceros?',
				'<p>Sí. CLI Connect utiliza los webservices nativos de RM Núcleo para exponer datos académicos de forma segura a portales de terceros. Matrícula, calificaciones, asistencia e historial se sincronizan automáticamente, sin necesidad de exportaciones manuales ni integraciones personalizadas. El portal externo recibe siempre los datos actualizados directamente del sistema de origen.</p>',
			),
			'totvs-rm-dados-rh' => array(
				'¿Cómo protege RM los datos sensibles de RR. HH.?',
				'<p>CLI Connect aplica enmascaramiento de datos en tránsito para información sensible como el documento de identidad, el salario y los datos médicos de los colaboradores. Todos los movimientos se registran en un log de auditoría con identificación del usuario, timestamp y datos transmitidos. El acceso se controla por perfiles de permiso, lo que garantiza que cada sistema satélite reciba solo la información necesaria para su operación.</p>',
			),

			// Arius ERP.
			'arius-erp-mes' => array(
				'¿Cómo conectar Arius con la planta de producción (MES)?',
				'CLI Connect ofrece conectores nativos para integrar Arius ERP con sistemas MES, sincronizando órdenes de producción, consumo de materiales y estado de línea en tiempo real, sin personalizaciones en el ERP.',
			),
			'arius-erp-crm-pedidos' => array(
				'¿Es posible integrar con el CRM para automatizar los pedidos de venta?',
				'Sí. La integración entre el CRM y Arius ERP permite que los pedidos generados en el CRM se creen automáticamente en el ERP, lo que elimina la redigitación y reduce el tiempo de ciclo de venta.',
			),
			'arius-erp-estoque-multiplanta' => array(
				'¿Cómo funciona la consolidación de inventario multiplanta?',
				'CLI Connect centraliza los datos de inventario de varias plantas de Arius ERP en una visión única, con sincronización automática de movimientos y disponibilidad en tiempo real.',
			),
		);
	}

	/**
	 * CISS Poder ERP, IFS Cloud, QAD Redzone, RP Info, Viasoft,
	 * Onclick ERP, Propz e Microsoft Teams.
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function faq_es_bloco_10() {
		return array(
			// CISS Poder ERP.
			'ciss-poder-erp-pdv' => array(
				'¿Cómo sincronizar las ventas del PDV con CISSPoder?',
				'CLI Connect integra los puntos de venta con CISSPoder en tiempo real y envía automáticamente al ERP las transacciones realizadas en las tiendas. Esto elimina las exportaciones manuales y mantiene el inventario y las finanzas siempre actualizados.',
			),
			'ciss-poder-erp-ecommerce' => array(
				'¿Es posible integrar CISSPoder con e-commerce y marketplaces?',
				'Sí. La integración conecta CISSPoder con plataformas de e-commerce y marketplaces, y sincroniza pedidos, inventario e información de producto de forma automatizada, con visibilidad centralizada de la operación digital.',
			),
			'ciss-poder-erp-edi' => array(
				'¿Cómo integrar CISSPoder con los proveedores vía EDI?',
				'CLI Connect implementa el intercambio de documentos electrónicos por EDI entre CISSPoder y los proveedores, y automatiza la recepción de pedidos, facturas y confirmaciones sin necesidad de digitación manual.',
			),

			// IFS Cloud.
			'ifs-cloud-ia-preditiva' => array(
				'¿Cómo conectar los datos de mantenimiento de IFS con un agente de IA predictiva?',
				'CLI Connect expone los datos de EAM de IFS Cloud como herramientas consumibles por agentes de IA, lo que permite que los modelos predictivos accedan al historial de órdenes, al estado de los activos y a las alertas de sensores IoT sin acoplarse directamente al core del sistema.',
			),
			'ifs-cloud-fsm-crm' => array(
				'¿Es posible integrar el field service (FSM) con el CRM?',
				'Sí. La integración conecta el módulo FSM de IFS Cloud con el CRM de la operación y sincroniza tickets, agendamientos e historial de atención. Así, los equipos comerciales y de soporte tienen visibilidad unificada del cliente sin duplicar registros.',
			),
			'ifs-cloud-consolidacao-financeira' => array(
				'¿Cómo funciona la consolidación financiera entre IFS y el ERP corporativo?',
				'CLI Connect recoge asientos, centros de costo y datos contables de IFS Cloud y los envía automáticamente al ERP corporativo, lo que elimina las exportaciones manuales. El proceso se audita y se configura por período de cierre, lo que garantiza consistencia en los informes financieros consolidados.',
			),

			// QAD Redzone.
			'qad-redzone-oee' => array(
				'¿Cómo llevar los datos de OEE de Redzone al QAD ERP?',
				'CLI Connect captura los indicadores de OEE generados por QAD Redzone en tiempo real y los envía automáticamente al QAD ERP, lo que permite que los gestores acompañen el desempeño de línea directamente en los informes corporativos, sin exportaciones manuales.',
			),
			'qad-redzone-alertas' => array(
				'¿Es posible generar alertas de parada de línea en tiempo real?',
				'Sí. La integración monitorea los eventos de parada registrados en Redzone y activa automáticamente notificaciones para los equipos de mantenimiento, calidad u operaciones. Las alertas pueden enviarse por correo, mensaje o integrarse a sistemas de gestión de mantenimiento.',
			),
			'qad-redzone-multiplanta' => array(
				'¿Cómo funciona la consolidación multiplanta?',
				'CLI Connect agrega los datos de productividad y calidad de varias plantas que utilizan QAD Redzone en un repositorio centralizado, conectado al ERP y al BI corporativo. Esto garantiza visibilidad unificada de la operación industrial sin depender de consolidaciones manuales por planta.',
			),

			// RP Info.
			'rp-info-pdv' => array(
				'¿Cómo sincronizar las ventas del frente de tienda en tiempo real?',
				'CLI Connect integra el RPDV de RP Info con Flex ERP y envía automáticamente cada transacción realizada en el checkout en tiempo real. Esto elimina los cierres manuales y garantiza que el inventario y la facturación estén siempre actualizados sin depender de sincronizaciones periódicas.',
			),
			'rp-info-edi' => array(
				'¿Es posible integrar varios proveedores vía EDI?',
				'Sí. CLI Connect implementa el protocolo EDI para el intercambio de pedidos, facturas y confirmaciones de entrega entre RP Info y proveedores de diferentes formatos y estándares. La integración se configura por socio y permite el onboarding de nuevos proveedores sin alterar el core del sistema.',
			),
			'rp-info-multiloja' => array(
				'¿Cómo funciona la consolidación de ventas multitienda?',
				'CLI Connect agrega los datos de ventas de varias tiendas que utilizan RP Info y los consolida en un repositorio único conectado al BI corporativo. Los gestores tienen visibilidad unificada del desempeño por tienda, región y período, sin depender de exportaciones manuales de cada unidad.',
			),

			// Viasoft.
			'viasoft-consolidacao-financeira' => array(
				'¿Cómo consolidar los datos financieros entre las diferentes verticales de Viasoft?',
				'CLI Connect centraliza los datos financieros y fiscales de varias verticales de Viasoft — agro, combustibles, industria — en una única capa de integración conectada al BI corporativo. Esto permite análisis consolidados por negocio sin depender de exportaciones manuales por sistema.',
			),
			'viasoft-defensivos-agricolas' => array(
				'¿Es posible integrar el control de vencimiento de agroquímicos con el ERP?',
				'Sí. CLI Connect integra los datos de control de agroquímicos de Agrotitan con Viasoft ERP y sincroniza automáticamente vencimientos, movimientos de inventario y alertas regulatorias. Esto garantiza el cumplimiento de las exigencias del sector sin procesos manuales.',
			),
			'viasoft-integracao-fiscal' => array(
				'¿Cómo funciona la integración fiscal por vertical?',
				'CLI Connect adapta los flujos de integración fiscal según las reglas específicas de cada vertical de Viasoft. Para agro, combustibles e industria, los procesos de NF-e, SPED y obligaciones accesorias se conectan automáticamente al ERP, respetando las particularidades tributarias de cada segmento.',
			),

			// Onclick ERP.
			'onclick-erp-estoque-omnichannel' => array(
				'¿Cómo sincronizar el inventario entre la tienda física y el e-commerce en Onclick?',
				'<p>CLI Connect monitorea los eventos de movimiento de inventario en Onclick ERP y replica las actualizaciones en tiempo real a los canales digitales configurados: e-commerce propio, marketplaces y PDV. El flujo es bidireccional: las ventas digitales también descuentan el inventario del ERP automáticamente, lo que elimina divergencias y quiebres de stock.</p>',
			),
			'onclick-erp-marketplaces' => array(
				'¿Es posible integrar varios marketplaces simultáneamente?',
				'<p>Sí. CLI Connect soporta conexiones simultáneas con múltiples marketplaces — como Mercado Libre, Amazon, Shopee y otros — todos integrados a Onclick ERP en un único proyecto. Cada canal opera con su propio mapeo de categorías, precios y reglas de envío, con monitoreo centralizado en la plataforma.</p>',
			),
			'onclick-erp-forca-de-vendas' => array(
				'¿Cómo funciona la automatización de la fuerza de ventas?',
				'<p>CLI Connect conecta la aplicación de fuerza de ventas con Onclick ERP y sincroniza pedidos, listas de precios, límites de crédito y disponibilidad de inventario en tiempo real. Los vendedores externos operan con información actualizada y los pedidos se transmiten automáticamente al ERP, sin necesidad de redigitación ni conciliación manual.</p>',
			),

			// Propz.
			'propz-dados-venda' => array(
				'¿Cómo alimentar Propz con datos de venta en tiempo real?',
				'<p>CLI Connect monitorea los eventos de venta en el PDV, el e-commerce y el ERP y los envía automáticamente a Propz en tiempo real. Cada transacción actualiza el historial de compras del consumidor, lo que permite que la plataforma recalcule ofertas y segmentaciones sin demoras, sin ninguna exportación manual ni lote periódico.</p>',
			),
			'propz-ofertas-canais' => array(
				'¿Es posible devolver automáticamente al app/SMS las ofertas generadas por Propz?',
				'<p>Sí. CLI Connect recibe las campañas publicadas por Propz y las distribuye automáticamente a los canales configurados: aplicación, SMS, correo y push. El flujo es bidireccional: los datos de venta entran en Propz y las ofertas personalizadas salen hacia los canales digitales sin intervención manual.</p>',
			),
			'propz-atribuicao-resultados' => array(
				'¿Cómo funciona la atribución de los resultados de campaña en el CRM/ERP?',
				'<p>CLI Connect captura los eventos de conversión registrados por Propz — compras realizadas después de la activación de una oferta — y los devuelve al CRM y al ERP con los atributos de campaña. Esto permite que los gestores visualicen ROI, tasa de conversión e ingreso incremental directamente en las herramientas de gestión, sin cruces manuales de datos.</p>',
			),

			// Microsoft Teams.
			'microsoft-teams-aprovacao-card' => array(
				'¿Cómo crear una aprobación de proceso directamente en una tarjeta de Teams?',
				'<p>CLI Connect conecta sus sistemas corporativos — ERP, CRM o ITSM — con Microsoft Teams vía Bot Framework y Microsoft Graph API. Al dispararse un evento de aprobación (compra, vacaciones, propuesta), la plataforma envía automáticamente una tarjeta adaptativa en el canal configurado. El aprobador hace clic en «Aprobar» o «Rechazar» directamente en Teams y la respuesta se graba en el sistema de origen sin ningún intercambio de correos.</p>',
			),
			'microsoft-teams-bot-erp' => array(
				'¿Es posible tener un bot de IA en Teams consultando el ERP?',
				'<p>Sí. CLI Connect expone datos del ERP como inventario, pedidos y estado de clientes en forma de endpoints seguros consumibles por bots de Teams. Con autenticación vía Azure AD, los miembros del equipo consultan información corporativa conversando con el bot en Teams, sin necesidad de acceder directamente al sistema legado. El bot también puede ejecutar acciones, como abrir órdenes de servicio o actualizar registros.</p>',
			),
			'microsoft-teams-azure-ad' => array(
				'¿Cómo funciona la autenticación vía Azure AD?',
				'<p>CLI Connect utiliza el flujo OAuth 2.0 de Azure Active Directory para autenticar las llamadas entre Teams y los sistemas integrados. Cada integración se registra como una aplicación en Azure AD con alcances de permiso específicos por equipo y canal. Esto garantiza que solo los usuarios autorizados puedan disparar acciones o consultar datos, respetando las políticas de seguridad corporativa sin exponer credenciales en los flujos.</p>',
			),
		);
	}

	/**
	 * Snowflake, Databricks, AWS, Microsoft Azure, Google Cloud,
	 * Gemini, Claude e ChatGPT.
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function faq_es_bloco_11() {
		return array(
			// Snowflake.
			'snowflake-ingestao' => array(
				'¿Cómo alimenta CLI Connect Snowflake con datos del ERP?',
				'<p>La integración usa el conector certificado de Boomi para Snowflake y transfiere datos transaccionales del ERP — como pedidos, facturación e inventario — de forma continua y trazable. Los flujos se configuran visualmente, sin necesidad de scripts ETL personalizados, y admiten bulk load para grandes volúmenes y streaming para datos en tiempo real.</p>',
			),
			'snowflake-seguranca' => array(
				'¿Qué mecanismos de seguridad se usan en la conexión con Snowflake?',
				'<p>CLI Connect utiliza autenticación OAuth 2.0 y key-pair para garantizar que solo los sistemas autorizados accedan a Snowflake. Todo el tráfico se cifra en tránsito y los accesos se auditan, lo que mantiene el cumplimiento de la LGPD, el GDPR y las políticas internas de gobernanza de datos de la empresa.</p>',
			),
			'snowflake-transformacoes' => array(
				'¿Es posible transformar los datos antes de cargarlos en Snowflake?',
				'<p>Sí. Los flujos de integración de Boomi permiten normalizar, enriquecer y filtrar los datos antes de enviarlos a Snowflake. Esto incluye conversiones de formato, deduplicación, validación de campos y mapeo de esquemas, lo que garantiza que solo lleguen datos de calidad al Data Warehouse para su análisis.</p>',
			),

			// Databricks.
			'databricks-ingestao' => array(
				'¿Cómo llevar datos operativos a Databricks en tiempo real?',
				'<p>CLI Connect usa el conector certificado de Boomi para Databricks y transfiere datos de ERP, CRM y sistemas legados de forma continua y trazable. Los flujos se configuran visualmente, sin scripts ETL personalizados, y admiten ingesta por lotes y streaming para mantener los datos siempre actualizados para los modelos de machine learning.</p>',
			),
			'databricks-writeback' => array(
				'¿Es posible devolver automáticamente al ERP el resultado de un modelo de IA?',
				'<p>Sí. Después de que Databricks genera scores, predicciones o recomendaciones, la integración escribe los resultados de vuelta en los sistemas de origen — ERP, CRM o plataformas operativas — de forma automatizada. Esto cierra el ciclo entre dato y acción sin intervención manual y acelera las decisiones en ventas, supply chain y finanzas.</p>',
			),
			'databricks-governanca' => array(
				'¿Cómo funciona la gobernanza de datos sensibles en ese flujo?',
				'<p>CLI Connect utiliza las APIs oficiales de Databricks con autenticación por token y Delta Sharing para controlar con precisión qué datos llegan a los modelos. Los campos sensibles pueden enmascararse o excluirse antes de la ingesta, lo que garantiza el cumplimiento de la LGPD y el GDPR y mantiene una auditoría completa de cada acceso al entorno analítico.</p>',
			),

			// AWS.
			'aws:1' => array(
				'¿Qué servicios de AWS tienen conector nativo en CLI Connect powered by Boomi?',
				'La plataforma ofrece conectores nativos para los principales servicios de AWS, incluidos S3, Lambda, SQS, SNS, EventBridge, DynamoDB, RDS, API Gateway y más. Esos conectores eliminan la necesidad de desarrollo específico para integrar su ecosistema AWS.',
			),
			'aws:2' => array(
				'¿Cómo ayuda CLI Connect powered by Boomi en la migración incremental a AWS?',
				'La plataforma permite conectar sistemas legados y workloads de AWS simultáneamente, lo que posibilita una migración gradual sin interrumpir las operaciones. Usted puede evolucionar su arquitectura por etapas, manteniendo las integraciones existentes en funcionamiento mientras adopta nuevos servicios en la nube.',
			),
			'aws:3' => array(
				'¿Cómo funciona la autenticación vía IAM/STS?',
				'La integración utiliza roles y políticas de IAM para autenticar las conexiones con los servicios de AWS, con soporte de STS para credenciales temporales. Esto garantiza un acceso controlado y auditable, siguiendo las mejores prácticas de seguridad de AWS y sin almacenar credenciales fijas.',
			),

			// Microsoft Azure.
			'microsoft-azure:1' => array(
				'¿Qué servicios de Azure tienen conector nativo en CLI Connect powered by Boomi?',
				'La plataforma ofrece conectores nativos para los principales servicios de Azure, incluidos Event Hubs, Service Bus, CosmosDB, Blob Storage, Azure AD, Key Vault, Functions y API Management. Esos conectores eliminan la necesidad de desarrollo específico para integrar su ecosistema Microsoft.',
			),
			'microsoft-azure:2' => array(
				'¿Cómo funciona la gestión de secretos vía Key Vault?',
				'CLI Connect powered by Boomi se integra de forma nativa con Azure Key Vault para almacenar y recuperar los secretos, claves y certificados usados en las conexiones. Esto elimina las credenciales fijas en los pipelines y garantiza que todos los accesos sean auditables y rotativos conforme a las políticas de seguridad corporativas.',
			),
			'microsoft-azure:3' => array(
				'¿Es posible combinar Azure con Dynamics 365 y Teams en el mismo pipeline?',
				'Sí. La plataforma permite orquestar flujos que involucran varios servicios del ecosistema Microsoft en un único pipeline: por ejemplo, capturar un evento en Azure Event Hubs, actualizar un registro en Dynamics 365 y notificar a un equipo vía Teams, todo de forma integrada y sin código personalizado.',
			),

			// Google Cloud.
			'google-cloud:1' => array(
				'¿Cómo acelera CLI Connect powered by Boomi la adopción de BigQuery y Vertex AI?',
				'La plataforma ofrece conectores nativos para BigQuery y Vertex AI, lo que permite enviar datos de ERP, CRM y sistemas operativos directamente a análisis y modelos de IA. Esto elimina las integraciones personalizadas y reduce el tiempo de entrega de las iniciativas de datos e inteligencia artificial.',
			),
			'google-cloud:2' => array(
				'¿Es posible hacer reverse ETL desde BigQuery hacia los sistemas operativos?',
				'Sí. CLI Connect powered by Boomi admite flujos bidireccionales, lo que permite enviar los resultados analíticos de BigQuery de vuelta a sistemas operativos como el ERP y el CRM. Así, las decisiones basadas en datos se reflejan automáticamente en los procesos de negocio.',
			),
			'google-cloud:3' => array(
				'¿Cómo funciona la arquitectura orientada a eventos vía Pub/Sub?',
				'La plataforma se integra de forma nativa con Google Cloud Pub/Sub para distribuir eventos entre aplicaciones de manera asíncrona y desacoplada. Usted puede configurar triggers que publican o consumen mensajes de Pub/Sub dentro de flujos de integración completos, sin código personalizado.',
			),

			// Gemini.
			'gemini-conectar-sistemas' => array(
				'¿Cómo conectar Gemini a los sistemas de la empresa?',
				'<p>La conexión se hace mediante la capa de integración de CLI Connect: Gemini accede a sistemas, bases de datos y procesos corporativos vía MCP Server, que expone los recursos como herramientas. No es necesario desarrollar APIs ni modificar los sistemas de origen: la plataforma gestiona la autenticación, los permisos y la trazabilidad de extremo a extremo.</p>',
			),
			'gemini-dados-internos' => array(
				'¿Es posible usar datos internos para dar contexto a Gemini?',
				'<p>Sí. CLI Connect conecta Gemini a fuentes internas — ERP, CRM, bases de datos, documentos y wikis — enviando el contexto relevante en cada consulta. Los datos circulan únicamente durante la ejecución y no se usan para reentrenar el modelo, lo que garantiza privacidad y cumplimiento.</p>',
			),
			'gemini-executar-acoes' => array(
				'¿Gemini puede ejecutar acciones en los sistemas conectados?',
				'<p>Sí. Además de consultar información, Gemini puede ejecutar acciones — como crear pedidos, actualizar registros o activar procesos — siempre que la herramienta correspondiente esté publicada en el MCP Server y el perfil del usuario tenga permiso. Cada ejecución queda registrada en la plataforma para auditoría.</p>',
			),

			// Claude.
			'claude-conectar-documentos' => array(
				'¿Cómo conectar Claude a los documentos y datos de la empresa?',
				'<p>La conexión se hace mediante la capa de integración de CLI Connect: Claude accede a documentos, bases de datos y sistemas vía MCP Server, que expone los recursos corporativos como herramientas. No es necesario desarrollar APIs ni modificar las fuentes de origen: la plataforma crea el canal seguro y gestiona la autenticación, los permisos y la trazabilidad.</p>',
			),
			'claude-analisar-contratos' => array(
				'¿Es posible usar Claude para analizar contratos y otros documentos?',
				'<p>Sí. Claude es especialmente eficaz en la lectura e interpretación de documentos largos: contratos, informes, políticas e historiales. La plataforma CLI Connect envía el contexto relevante al modelo y devuelve los insights de forma estructurada, sin almacenar el contenido de los documentos después de la ejecución.</p>',
			),
			'claude-executar-acoes' => array(
				'¿Es posible hacer que Claude ejecute acciones en los sistemas?',
				'<p>Sí. Además de consultar y analizar información, Claude puede ejecutar acciones — como crear un pedido en el ERP, actualizar un registro en el CRM o abrir un ticket — siempre que la herramienta correspondiente esté publicada en el MCP Server y el perfil del usuario tenga permiso. Cada ejecución queda registrada en la plataforma para auditoría.</p>',
			),

			// ChatGPT.
			'chatgpt-conectar-sistemas' => array(
				'¿Cómo conectar ChatGPT a los sistemas de la empresa?',
				'<p>La conexión se hace mediante la capa de integración de CLI Connect: ChatGPT accede a los sistemas vía MCP Server, que expone los procesos corporativos como herramientas. No es necesario desarrollar APIs ni modificar los sistemas de origen: la plataforma crea el canal seguro y gestiona la autenticación, los permisos y la trazabilidad de extremo a extremo.</p>',
			),
			'chatgpt-dados-treinamento' => array(
				'¿Los datos de la empresa se usan para entrenar los modelos?',
				'<p>No. Los datos circulan únicamente durante la ejecución de una consulta y OpenAI no los retiene para entrenamiento cuando la API se usa en un entorno corporativo. Además, CLI Connect controla qué información llega al modelo, lo que permite anonimizar y enmascarar los datos sensibles antes de cualquier llamada.</p>',
			),
			'chatgpt-executar-acoes' => array(
				'¿Es posible hacer que ChatGPT ejecute acciones en los sistemas?',
				'<p>Sí. Además de consultar información, ChatGPT puede ejecutar acciones — como crear un pedido en el ERP, actualizar un registro en el CRM o abrir un ticket en ServiceNow — siempre que la herramienta correspondiente esté publicada en el MCP Server y el perfil del usuario tenga permiso para esa acción. Cada ejecución queda registrada en la plataforma para auditoría.</p>',
			),
		);
	}
}
