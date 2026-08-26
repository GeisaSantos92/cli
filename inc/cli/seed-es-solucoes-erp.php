<?php
/**
 * Seed — texto em espanhol das landings de solução: ERPs.
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
 * ERPs — texto em espanhol.
 */
trait Cliconnect_Seed_Es_Solucoes_Erp {

	/**
	 * SAP.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_sap() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su SAP',
				'solucao_hero_titulo'      => 'Acelere su migración a SAP S/4HANA sin comprometer la operación',
				'solucao_hero_corpo'       => 'Conecte SAP al resto de su ecosistema, preserve un entorno Clean Core y conduzca la migración con más seguridad, agilidad y previsibilidad.',
				'solucao_pilares_titulo'   => 'Todo lo que necesita para integrar su SAP',
				'solucao_pilares_1_titulo' => 'Migre con confianza',
				'solucao_pilares_1_desc'   => 'Conduzca su migración a SAP S/4HANA con una arquitectura preparada para reducir riesgos, retrabajo e impactos en la operación.',
				'solucao_pilares_2_titulo' => 'Unifique todo el ecosistema',
				'solucao_pilares_2_desc'   => 'Integre SAP, Salesforce, Workday, ServiceNow y otras aplicaciones en una única plataforma, simplificando la gestión de las integraciones.',
				'solucao_pilares_3_titulo' => 'Reduzca costos de integración',
				'solucao_pilares_3_desc'   => 'Utilice Add-on SAP homologado, conectores (RFC, IDoc, BAPI) y protocolos (OData, REST, SOAP). Todo sin costo adicional.',
				'solucao_casos_titulo'     => 'Automatice los procesos más críticos de SAP',
				'solucao_casos_1_titulo'   => 'Pedido a recepción integrado',
				'solucao_casos_1_desc'     => 'Sincronice automáticamente oportunidades, pedidos y facturación entre Salesforce y SAP S/4HANA.',
				'solucao_casos_2_titulo'   => 'Migración sin interrupciones',
				'solucao_casos_2_desc'     => 'Ejecute el período de convivencia entre SAP ECC y S/4HANA manteniendo ambos sincronizados durante toda la transición.',
				'solucao_casos_3_titulo'   => 'SAP conectado a la IA',
				'solucao_casos_3_desc'     => 'Permita que los agentes de IA consulten información de SAP para acelerar análisis y operaciones.',
				'solucao_casos_4_titulo'   => 'Automatice las compras corporativas',
				'solucao_casos_4_desc'     => 'Integre SAP a los principales sistemas de procurement, como Ariba y Coupa, eliminando retrabajo operativo.',
				'solucao_casos_5_titulo'   => 'Envíe datos a Analytics',
				'solucao_casos_5_desc'     => 'Alimente automáticamente plataformas como Snowflake y BigQuery con datos actualizados de SAP.',
				'solucao_dif_titulo'       => 'Integración nativa, segura y preparada para entornos SAP',
				'solucao_dif_corpo'        => 'Conecte su entorno SAP utilizando recursos nativos de la plataforma, preservando la seguridad de la infraestructura y reduciendo la necesidad de componentes intermedios.',
				'solucao_dif_topico_1'     => 'Utilice conectores nativos RFC, BAPI e IDoc.',
				'solucao_dif_topico_2'     => 'Utilice Add-on Nativo, SOAP, OData, REST',
				'solucao_dif_topico_3'     => 'Preserve la arquitectura Clean Core.',
				'solucao_plat_titulo'      => 'Centralice todas las integraciones de su SAP',
				'solucao_plat_corpo'       => 'Sustituya integraciones aisladas por una plataforma única capaz de conectar SAP, aplicaciones corporativas, datos y automatizaciones con gobernanza centralizada.',
				'solucao_plat_topico_1'    => 'Reutilice integraciones entre distintos proyectos.',
				'solucao_plat_topico_2'    => 'Estandarice toda la arquitectura de integración.',
				'solucao_plat_topico_3'    => 'Reduzca costos de mantenimiento continuo.',
				'solucao_acel_titulo'      => 'Comience utilizando integraciones ya validadas',
				'solucao_acel_corpo'       => 'Utilice modelos listos para acelerar la implementación de las integraciones más comunes entre SAP y los principales sistemas del mercado.',
				'solucao_acel_topico_1'    => 'Aproveche modelos de Order-to-Cash.',
				'solucao_acel_topico_2'    => 'Reduzca el tiempo de implementación.',
				'solucao_acel_topico_3'    => 'Adapte flujos a su entorno.',
			)
		);
	}

	/**
	 * SAP Business One.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_sap_business_one() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su SAP B1',
				'solucao_hero_titulo'      => 'Conecte SAP Business One sin necesitar un equipo SAP dedicado',
				'solucao_hero_corpo'       => 'Integre SAP B1 al e-commerce, CRM y sistemas fiscales para ampliar su operación con una capa de integración preparada para empresas en crecimiento.',
				'solucao_pilares_titulo'   => 'Escale su SAP Business One conectado',
				'solucao_pilares_1_titulo' => 'Use APIs nativas de SAP B1',
				'solucao_pilares_1_desc'   => 'Conecte sistemas a través del Service Layer REST y de la DI API oficial.',
				'solucao_pilares_2_titulo' => 'Simplifique operaciones sin equipo SAP',
				'solucao_pilares_2_desc'   => 'Automatice procesos de SAP B1 sin depender de especialistas dedicados.',
				'solucao_pilares_3_titulo' => 'Reutilice integraciones SAP',
				'solucao_pilares_3_desc'   => 'Adapte componentes ya utilizados en proyectos S/4HANA para el B1.',
				'solucao_casos_titulo'     => 'Automatice procesos de SAP Business One',
				'solucao_casos_1_titulo'   => 'Integre pedidos del e-commerce',
				'solucao_casos_1_desc'     => 'Envíe pedidos digitales al SAP B1 sin registros manuales.',
				'solucao_casos_2_titulo'   => 'Automatice procesos fiscales',
				'solucao_casos_2_desc'     => 'Conecte la emisión fiscal brasileña a SAP Business One.',
				'solucao_casos_3_titulo'   => 'Consolide inventario multisucursal',
				'solucao_casos_3_desc'     => 'Centralice información de inventario entre distintas unidades.',
				'solucao_casos_4_titulo'   => 'Conecte el CRM al ERP',
				'solucao_casos_4_desc'     => 'Sincronice ventas entre Salesforce, HubSpot y SAP B1.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga datos de SAP B1 a disposición de agentes para automatizar la atención y los análisis.',
				'solucao_dif_titulo'       => 'Integraciones nativas para SAP B1',
				'solucao_dif_corpo'        => 'Utilice el Service Layer REST/OData de SAP Business One con autenticación segura para conectar aplicaciones corporativas.',
				'solucao_dif_topico_1'     => 'Utilice el Service Layer oficial',
				'solucao_dif_topico_2'     => 'Conecte vía REST y OData',
				'solucao_dif_topico_3'     => 'Proteja sesiones autenticadas',
				'solucao_plat_titulo'      => 'Prepare su SAP para crecer',
				'solucao_plat_corpo'       => 'Mantenga la misma capa de integración al evolucionar de SAP Business One a S/4HANA u operar distintas versiones simultáneamente.',
				'solucao_plat_topico_1'    => 'Reutilice integraciones SAP',
				'solucao_plat_topico_2'    => 'Evite reconstrucciones futuras',
				'solucao_plat_topico_3'    => 'Estandarice arquitecturas corporativas',
				'solucao_acel_titulo'      => 'Comience con SAP Business One conectado',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para integrar SAP B1 al e-commerce, CRM y sistemas fiscales con flujos estructurados.',
				'solucao_acel_topico_1'    => 'Conecte sistemas rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice integraciones SAP',
				'solucao_acel_topico_3'    => 'Acelere nuevos proyectos',
			)
		);
	}

	/**
	 * SAP ECC.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_sap_ecc() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su SAP ECC',
				'solucao_hero_titulo'      => 'Conecte su SAP ECC a la nube sin esperar la migración a S/4HANA',
				'solucao_hero_corpo'       => 'Modernice la conectividad de SAP ECC 6.0 integrando sistemas SaaS, e-commerce y aplicaciones corporativas sin sustituir el ERP actual.',
				'solucao_pilares_titulo'   => 'Modernice su SAP ECC en producción',
				'solucao_pilares_1_titulo' => 'Aproveche integraciones nativas SAP',
				'solucao_pilares_1_desc'   => 'Conecte el ECC usando RFC, BAPI e IDoc sin alterar los procesos existentes.',
				'solucao_pilares_2_titulo' => 'Proteja su entorno legado',
				'solucao_pilares_2_desc'   => 'Conecte sistemas externos sin exponer el ECC on-premises a internet.',
				'solucao_pilares_3_titulo' => 'Conecte aplicaciones modernas',
				'solucao_pilares_3_desc'   => 'Integre Salesforce, e-commerce y SaaS mientras el ECC sigue operando.',
				'solucao_casos_titulo'     => 'Conecte procesos de SAP ECC',
				'solucao_casos_1_titulo'   => 'Sincronice pedidos digitales',
				'solucao_casos_1_desc'     => 'Integre el e-commerce al ECC para automatizar la entrada de pedidos.',
				'solucao_casos_2_titulo'   => 'Conecte el ECC al CRM',
				'solucao_casos_2_desc'     => 'Sincronice datos comerciales entre Salesforce, CRM y ERP.',
				'solucao_casos_3_titulo'   => 'Migre sin interrumpir operaciones',
				'solucao_casos_3_desc'     => 'Ejecute escenarios paralelos entre ECC y S/4HANA durante la transición.',
				'solucao_casos_4_titulo'   => 'Exponga los datos del ECC',
				'solucao_casos_4_desc'     => 'Exponga información legada como APIs modernas para las aplicaciones.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga datos del ECC a disposición de agentes de IA sin exponer el core del sistema.',
				'solucao_dif_titulo'       => 'Conectividad segura para SAP ECC',
				'solucao_dif_corpo'        => 'Utilice RFC, BAPI e IDoc con un Runtime seguro para integrar el ECC sin exponer entornos legados a internet.',
				'solucao_dif_topico_1'     => 'Utilice protocolos SAP nativos',
				'solucao_dif_topico_2'     => 'Proteja conexiones on-premises',
				'solucao_dif_topico_3'     => 'Evite la exposición externa',
				'solucao_plat_titulo'      => 'Modernice antes del S/4HANA',
				'solucao_plat_corpo'       => 'La misma plataforma que conecta su futuro S/4HANA conecta su ECC actual, garantizando evolución continua sin reconstrucciones.',
				'solucao_plat_topico_1'    => 'Conecte el ECC hoy',
				'solucao_plat_topico_2'    => 'Prepare la migración futura',
				'solucao_plat_topico_3'    => 'Reutilice integraciones existentes',
				'solucao_acel_titulo'      => 'Comience con SAP ECC conectado',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para conectar el ECC a sistemas SaaS modernos como Salesforce y e-commerce.',
				'solucao_acel_topico_1'    => 'Conecte SaaS rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice estándares SAP',
				'solucao_acel_topico_3'    => 'Acelere la modernización',
			)
		);
	}

	/**
	 * Oracle NetSuite.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_oracle_netsuite() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su NetSuite',
				'solucao_hero_titulo'      => 'Conecte NetSuite a su stack sin depender solo de SuiteScript',
				'solucao_hero_corpo'       => 'Integre NetSuite al e-commerce, CRM y sistemas financieros usando APIs nativas sin personalizaciones excesivas de SuiteScript.',
				'solucao_pilares_titulo'   => 'Escale su NetSuite conectado',
				'solucao_pilares_1_titulo' => 'Use APIs nativas de NetSuite',
				'solucao_pilares_1_desc'   => 'Integre vía SuiteTalk REST/SOAP y RESTlets sin código personalizado excesivo.',
				'solucao_pilares_2_titulo' => 'Simplifique operaciones globales',
				'solucao_pilares_2_desc'   => 'Gestione integraciones multisubsidiaria con estandarización de procesos financieros.',
				'solucao_pilares_3_titulo' => 'Reduzca personalizaciones SuiteScript',
				'solucao_pilares_3_desc'   => 'Sustituya scripts específicos por integraciones reutilizables y sin mantenimiento.',
				'solucao_casos_titulo'     => 'Automatice procesos de NetSuite',
				'solucao_casos_1_titulo'   => 'Sincronice pedidos digitales',
				'solucao_casos_1_desc'     => 'Integre el e-commerce a NetSuite para automatizar la entrada de pedidos.',
				'solucao_casos_2_titulo'   => 'Consolide finanzas globales',
				'solucao_casos_2_desc'     => 'Sincronice datos financieros entre subsidiarias automáticamente.',
				'solucao_casos_3_titulo'   => 'Integre el CRM al financiero',
				'solucao_casos_3_desc'     => 'Conecte Salesforce a NetSuite para unificar datos comerciales y financieros.',
				'solucao_casos_4_titulo'   => 'Automatice el reconocimiento de ingresos',
				'solucao_casos_4_desc'     => 'Procese eventos de venta en NetSuite sin intervención manual.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga datos de NetSuite a disposición de agentes de IA sin exponer el core.',
				'solucao_dif_titulo'       => 'Integraciones seguras para NetSuite',
				'solucao_dif_corpo'        => 'Utilice TBA y OAuth 2.0 para autenticar integraciones NetSuite con seguridad corporativa sin exponer credenciales.',
				'solucao_dif_topico_1'     => 'Utilice TBA y OAuth 2.0',
				'solucao_dif_topico_2'     => 'Proteja accesos corporativos',
				'solucao_dif_topico_3'     => 'Integre APIs oficiales',
				'solucao_plat_titulo'      => 'Escale operaciones multisubsidiaria',
				'solucao_plat_corpo'       => 'La misma plataforma que conecta una subsidiaria replica las integraciones NetSuite OneWorld para todo el grupo corporativo.',
				'solucao_plat_topico_1'    => 'Replique integraciones globales',
				'solucao_plat_topico_2'    => 'Estandarice procesos financieros',
				'solucao_plat_topico_3'    => 'Reduzca desarrollo específico',
				'solucao_acel_titulo'      => 'Comience con NetSuite integrado',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para integrar NetSuite al e-commerce, CRM y sistemas financieros con flujos estructurados.',
				'solucao_acel_topico_1'    => 'Conecte sistemas rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice integraciones NetSuite',
				'solucao_acel_topico_3'    => 'Acelere nuevos proyectos',
			)
		);
	}

	/**
	 * TOTVS Protheus.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_totvs_protheus() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Protheus',
				'solucao_hero_titulo'      => 'Integre TOTVS Protheus con cualquier sistema sin proyectos largos',
				'solucao_hero_corpo'       => 'Conecte Protheus al CRM, e-commerce, bancos y plataformas fiscales utilizando integraciones listas, reduciendo personalizaciones, acelerando implementaciones y preservando la estabilidad de su ERP.',
				'solucao_pilares_titulo'   => 'Simplifique la integración de su Protheus',
				'solucao_pilares_1_titulo' => 'Elimine integraciones personalizadas',
				'solucao_pilares_1_desc'   => 'Reduzca la dependencia de AdvPL: conecte nuevos sistemas con agilidad usando ExecAuto estándar y puntos de entrada Protheus.',
				'solucao_pilares_2_titulo' => 'Reutilice integraciones listas',
				'solucao_pilares_2_desc'   => 'Utilice aceleradores para pedidos, clientes, inventario y procesos fiscales, disminuyendo el tiempo de implementación.',
				'solucao_pilares_3_titulo' => 'Nuevas integraciones en días',
				'solucao_pilares_3_desc'   => 'Conecte nuevos sistemas en días, no en meses, utilizando una arquitectura preparada para expandirse.',
				'solucao_casos_titulo'     => 'Conecte los principales procesos de Protheus',
				'solucao_casos_1_titulo'   => 'Automatice pedidos del e-commerce',
				'solucao_casos_1_desc'     => 'Envíe pedidos automáticamente al Protheus, reduciendo retrabajo y acelerando la facturación.',
				'solucao_casos_2_titulo'   => 'Clientes siempre actualizados',
				'solucao_casos_2_desc'     => 'Sincronice registros entre CRM, e-commerce y Protheus utilizando APIs REST.',
				'solucao_casos_3_titulo'   => 'Automatice documentos fiscales',
				'solucao_casos_3_desc'     => 'Integre la emisión y consulta de documentos fiscales directamente a los procesos financieros.',
				'solucao_casos_4_titulo'   => 'Controle inventarios entre sucursales',
				'solucao_casos_4_desc'     => 'Actualice saldos automáticamente entre unidades, evitando divergencias operativas.',
				'solucao_casos_5_titulo'   => 'Conecte Protheus a Salesforce',
				'solucao_casos_5_desc'     => 'Comparta información entre ERP y CRM para eliminar retrabajo comercial y operativo.',
				'solucao_dif_titulo'       => 'Conectividad segura para entornos Protheus',
				'solucao_dif_corpo'        => 'Conecte Protheus utilizando Runtime y comunicación outbound, preservando la infraestructura de la empresa y soportando REST de forma nativa y ExecAuto llamando a las MATA Protheus Standard.',
				'solucao_dif_topico_1'     => 'Evite abrir puertos en el firewall.',
				'solucao_dif_topico_2'     => 'REST nativo y ExecAuto llamando a MATA Protheus Standard.',
				'solucao_dif_topico_3'     => 'Preserve la seguridad del entorno interno.',
				'solucao_plat_titulo'      => 'Una plataforma para integrar todo su ecosistema',
				'solucao_plat_corpo'       => 'Centralice todas las integraciones de Protheus, Salesforce, bancos y e-commerce en una única plataforma, reutilizando componentes ya validados y reduciendo la complejidad operativa.',
				'solucao_plat_topico_1'    => 'Reutilice integraciones ya implementadas.',
				'solucao_plat_topico_2'    => 'Reduzca nuevos proyectos de desarrollo.',
				'solucao_plat_topico_3'    => 'Centralice toda la gobernanza de las integraciones.',
				'solucao_acel_titulo'      => 'Comience utilizando integraciones listas',
				'solucao_acel_corpo'       => 'Implemente escenarios recurrentes entre Protheus y otros sistemas utilizando modelos preconfigurados, adaptados rápidamente a su entorno.',
				'solucao_acel_topico_1'    => 'Implemente la sincronización de pedidos rápidamente.',
				'solucao_acel_topico_2'    => 'Reutilice modelos para registros.',
				'solucao_acel_topico_3'    => 'Adapte flujos a su proceso.',
			)
		);
	}

	/**
	 * TOTVS Datasul.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_totvs_datasul() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Datasul',
				'solucao_hero_titulo'      => 'Conecte TOTVS Datasul sin interrumpir su operación industrial',
				'solucao_hero_corpo'       => 'Integre Datasul a MES, CRM, portales B2B y plataformas de BI utilizando una única plataforma. Comparta información entre plantas, automatice procesos y modernice su operación sin alterar el core del ERP.',
				'solucao_pilares_titulo'   => 'Conecte toda la operación industrial',
				'solucao_pilares_1_titulo' => 'Integre su manufactura con más agilidad',
				'solucao_pilares_1_desc'   => 'Conecte Datasul a sistemas de producción, ventas y logística sin proyectos largos ni desarrollos complejos.',
				'solucao_pilares_2_titulo' => 'Estandarice información entre plantas',
				'solucao_pilares_2_desc'   => 'Centralice datos de producción e inventario para garantizar decisiones más rápidas y confiables en toda la operación.',
				'solucao_pilares_3_titulo' => 'Reduzca la dependencia de especialistas',
				'solucao_pilares_3_desc'   => 'Simplifique nuevas integraciones sin depender continuamente de equipos especializados en Progress 4GL.',
				'solucao_casos_titulo'     => 'Automatice los procesos que mueven su fábrica',
				'solucao_casos_1_titulo'   => 'Sincronice órdenes de producción',
				'solucao_casos_1_desc'     => 'Conecte el MES a Datasul para actualizar órdenes automáticamente durante toda la operación industrial.',
				'solucao_casos_2_titulo'   => 'Consolide inventarios entre plantas',
				'solucao_casos_2_desc'     => 'Comparta saldos de inventario entre unidades para aumentar la visibilidad de la operación.',
				'solucao_casos_3_titulo'   => 'Automatice pedidos B2B',
				'solucao_casos_3_desc'     => 'Integre portales de clientes directamente a Datasul para reducir retrabajo y acelerar el procesamiento.',
				'solucao_casos_4_titulo'   => 'Centralice el cierre financiero',
				'solucao_casos_4_desc'     => 'Consolide información entre distintas unidades para simplificar el cierre corporativo.',
				'solucao_casos_5_titulo'   => 'Ponga datos a disposición de la IA',
				'solucao_casos_5_desc'     => 'Permita que los agentes de IA consulten información de Datasul con seguridad mediante integraciones gobernadas.',
				'solucao_dif_titulo'       => 'Conectividad preparada para entornos industriales',
				'solucao_dif_corpo'        => 'Conecte Datasul utilizando el protocolo Progress/EMS con el procesamiento realizado dentro de la infraestructura de la empresa, preservando seguridad y desempeño.',
				'solucao_dif_topico_1'     => 'Utilice conectividad nativa Progress/EMS.',
				'solucao_dif_topico_2'     => 'Preserve la base de datos protegida internamente.',
				'solucao_dif_topico_3'     => 'Implemente dentro de su propia infraestructura.',
				'solucao_plat_titulo'      => 'Integre distintos ERP en la misma plataforma',
				'solucao_plat_corpo'       => 'Las empresas que crecieron por adquisiciones suelen operar más de un ERP. Centralice Datasul, SAP, Protheus y otros sistemas en una única capa de integración.',
				'solucao_plat_topico_1'    => 'Reutilice integraciones ya implementadas.',
				'solucao_plat_topico_2'    => 'Reduzca nuevos proyectos de desarrollo.',
				'solucao_plat_topico_3'    => 'Centralice toda la gobernanza de las integraciones.',
				'solucao_acel_titulo'      => 'Implemente integraciones en menos tiempo',
				'solucao_acel_corpo'       => 'Utilice modelos listos para sincronizar órdenes de producción y pedidos B2B, reduciendo el esfuerzo de implementación y acelerando nuevos proyectos.',
				'solucao_acel_topico_1'    => 'Aproveche modelos para órdenes de producción.',
				'solucao_acel_topico_2'    => 'Reutilice flujos para pedidos B2B.',
				'solucao_acel_topico_3'    => 'Adapte rápidamente a su entorno.',
			)
		);
	}

	/**
	 * TOTVS Winthor.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_totvs_winthor() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Winthor',
				'solucao_hero_titulo'      => 'Integre TOTVS Winthor y acelere toda su operación comercial',
				'solucao_hero_corpo'       => 'Conecte Winthor a la fuerza de ventas, e-commerce B2B, transportistas y bancos para automatizar procesos, reducir retrabajo y mantener pedidos, precios y entregas siempre sincronizados.',
				'solucao_pilares_titulo'   => 'Conecte su operación de distribución',
				'solucao_pilares_1_titulo' => 'Actualice precios automáticamente',
				'solucao_pilares_1_desc'   => 'Sincronice tablas de precios y promociones en tiempo real para todo el equipo comercial, reduciendo inconsistencias y acelerando negociaciones.',
				'solucao_pilares_2_titulo' => 'Automatice pedidos de venta',
				'solucao_pilares_2_desc'   => 'Integre aplicaciones de preventa y canales digitales al Winthor para eliminar la digitación manual y acelerar la facturación.',
				'solucao_pilares_3_titulo' => 'Amplíe integraciones con facilidad',
				'solucao_pilares_3_desc'   => 'Conecte nuevos sistemas utilizando una arquitectura preparada para crecer junto con su operación.',
				'solucao_casos_titulo'     => 'Automatice los principales procesos de Winthor',
				'solucao_casos_1_titulo'   => 'Sincronice pedidos de la fuerza de ventas',
				'solucao_casos_1_desc'     => 'Envíe automáticamente los pedidos de las aplicaciones comerciales al Winthor sin retrabajo.',
				'solucao_casos_2_titulo'   => 'Actualice precios en tiempo real',
				'solucao_casos_2_desc'     => 'Distribuya cambios de precios y descuentos de inmediato a vendedores y canales digitales.',
				'solucao_casos_3_titulo'   => 'Integre transportistas',
				'solucao_casos_3_desc'     => 'Automatice el envío de etiquetas, el rastreo y la actualización del estado de las entregas.',
				'solucao_casos_4_titulo'   => 'Concilie cobros automáticamente',
				'solucao_casos_4_desc'     => 'Conecte bancos y adquirentes para simplificar la conciliación financiera.',
				'solucao_casos_5_titulo'   => 'Consolide ventas entre sucursales',
				'solucao_casos_5_desc'     => 'Centralice indicadores comerciales de distintas unidades en una única vista.',
				'solucao_dif_titulo'       => 'Conectividad preparada para operaciones de alto volumen',
				'solucao_dif_corpo'        => 'La plataforma utiliza conectores dedicados a las rutinas automáticas y webservices de Winthor, soportando grandes volúmenes de pedidos típicos de distribuidores y mayoristas.',
				'solucao_dif_topico_1'     => 'Procese grandes volúmenes de pedidos.',
				'solucao_dif_topico_2'     => 'Utilice conectores nativos de Winthor.',
				'solucao_dif_topico_3'     => 'Preserve la estabilidad de la operación.',
				'solucao_plat_titulo'      => 'Centralice todas las integraciones de la distribución',
				'solucao_plat_corpo'       => 'Conecte Winthor, aplicaciones comerciales, transportistas y bancos en una única plataforma, reutilizando integraciones y reduciendo nuevos proyectos.',
				'solucao_plat_topico_1'    => 'Reutilice integraciones existentes.',
				'solucao_plat_topico_2'    => 'Centralice toda la gobernanza.',
				'solucao_plat_topico_3'    => 'Reduzca nuevos desarrollos.',
				'solucao_acel_titulo'      => 'Comience utilizando integraciones listas',
				'solucao_acel_corpo'       => 'Implemente rápidamente los principales escenarios de integración.',
				'solucao_acel_topico_1'    => 'Implemente la integración de pedidos rápidamente.',
				'solucao_acel_topico_2'    => 'Reutilice modelos para tablas de precios.',
				'solucao_acel_topico_3'    => 'Adapte flujos a su proceso.',
			)
		);
	}

	/**
	 * TOTVS Logix.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_totvs_logix() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Logix',
				'solucao_hero_titulo'      => 'Conecte TOTVS Logix y mantenga su inventario sincronizado en todos los canales',
				'solucao_hero_corpo'       => 'Integre Logix a ERP, marketplaces y transportistas para automatizar la operación logística, eliminar divergencias de inventario y acelerar la atención de pedidos sin procesos manuales.',
				'solucao_pilares_titulo'   => 'Mantenga su logística conectada en tiempo real',
				'solucao_pilares_1_titulo' => 'Sincronice inventarios automáticamente',
				'solucao_pilares_1_desc'   => 'Actualice saldos entre Logix, marketplaces y canales de venta en tiempo real para evitar divergencias y mejorar la disponibilidad de los productos.',
				'solucao_pilares_2_titulo' => 'Automatice toda la expedición',
				'solucao_pilares_2_desc'   => 'Orqueste picking, packing y expedición a partir de los pedidos recibidos, reduciendo retrabajo y aumentando la productividad del almacén.',
				'solucao_pilares_3_titulo' => 'Evite pérdidas por overselling',
				'solucao_pilares_3_desc'   => 'Comparta información de inventario entre todos los canales para vender solo lo que realmente está disponible.',
				'solucao_casos_titulo'     => 'Automatice toda la operación logística',
				'solucao_casos_1_titulo'   => 'Sincronice inventarios con marketplaces',
				'solucao_casos_1_desc'     => 'Actualice automáticamente el inventario entre Logix, Amazon, Mercado Libre, Magalu y otros canales de venta.',
				'solucao_casos_2_titulo'   => 'Dirija pedidos al centro correcto',
				'solucao_casos_2_desc'     => 'Enrute automáticamente cada pedido al centro de distribución más adecuado según las reglas de la operación.',
				'solucao_casos_3_titulo'   => 'Integre transportistas',
				'solucao_casos_3_desc'     => 'Automatice la emisión de etiquetas, el rastreo y la actualización del estado de entrega.',
				'solucao_casos_4_titulo'   => 'Actualice el ERP en tiempo real',
				'solucao_casos_4_desc'     => 'Sincronice separación, expedición y facturación automáticamente entre Logix y el ERP.',
				'solucao_casos_5_titulo'   => 'Automatice devoluciones',
				'solucao_casos_5_desc'     => 'Controle devoluciones y reingreso de inventario sin procesos manuales ni retrabajo operativo.',
				'solucao_dif_titulo'       => 'Escalabilidad para operaciones logísticas de alto volumen',
				'solucao_dif_corpo'        => 'La plataforma soporta grandes volúmenes de transacciones con escalabilidad automática, garantizando estabilidad incluso durante el Black Friday y otras fechas estacionales de alta demanda.',
				'solucao_dif_topico_1'     => 'Procese picos de operación con estabilidad.',
				'solucao_dif_topico_2'     => 'Escale pipelines automáticamente.',
				'solucao_dif_topico_3'     => 'Mantenga la operación disponible en alta demanda.',
				'solucao_plat_titulo'      => 'Centralice toda la integración de su logística',
				'solucao_plat_corpo'       => 'Conecte Logix, marketplaces, transportistas y ERP en una única plataforma para mantener inventarios sincronizados en tiempo real y eliminar integraciones aisladas que generan retrasos y overselling.',
				'solucao_plat_topico_1'    => 'Centralice todas las integraciones.',
				'solucao_plat_topico_2'    => 'Sincronice inventarios en tiempo real.',
				'solucao_plat_topico_3'    => 'Reduzca proyectos aislados de integración.',
				'solucao_acel_titulo'      => 'Comience utilizando integraciones listas',
				'solucao_acel_corpo'       => 'Implemente rápidamente escenarios de sincronización entre Logix y marketplaces utilizando modelos preconfigurados que reducen el tiempo de implementación.',
				'solucao_acel_topico_1'    => 'Implemente integraciones más rápidamente.',
				'solucao_acel_topico_2'    => 'Reutilice modelos ya validados.',
				'solucao_acel_topico_3'    => 'Adapte flujos a su operación.',
			)
		);
	}

	/**
	 * TOTVS Consinco.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_totvs_consinco() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Consinco',
				'solucao_hero_titulo'      => 'Conecte Consinco desde la góndola hasta el centro de distribución',
				'solucao_hero_corpo'       => 'Integre el ERP de retail alimentario con POS, e-commerce y proveedores para sincronizar precios, inventario y operaciones en toda la red.',
				'solucao_pilares_titulo'   => 'Integre toda la operación del retail alimentario',
				'solucao_pilares_1_titulo' => 'Conecte compras y operaciones',
				'solucao_pilares_1_desc'   => 'Integre procesos de compras, precios y promociones del retail.',
				'solucao_pilares_2_titulo' => 'Automatice conexiones EDI',
				'solucao_pilares_2_desc'   => 'Sincronice datos con proveedores sin procesos manuales.',
				'solucao_pilares_3_titulo' => 'Unifique precios y canales',
				'solucao_pilares_3_desc'   => 'Mantenga la tienda física y la digital siempre alineadas.',
				'solucao_casos_titulo'     => 'Automatice procesos del retail alimentario',
				'solucao_casos_1_titulo'   => 'Sincronice precios y promociones',
				'solucao_casos_1_desc'     => 'Actualice valores entre Consinco, POS y e-commerce automáticamente.',
				'solucao_casos_2_titulo'   => 'Integre proveedores vía EDI',
				'solucao_casos_2_desc'     => 'Conecte industrias asociadas al flujo de compras.',
				'solucao_casos_3_titulo'   => 'Consolide ventas de la red',
				'solucao_casos_3_desc'     => 'Centralice datos de ventas multitienda para BI.',
				'solucao_casos_4_titulo'   => 'Automatice la reposición de inventario',
				'solucao_casos_4_desc'     => 'Use la rotación de ventas para apoyar el abastecimiento automático.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga datos del retail a disposición de agentes de IA sin exponer el core del sistema.',
				'solucao_dif_titulo'       => 'Integraciones preparadas para alto volumen',
				'solucao_dif_corpo'        => 'Conecte operaciones de supermercado con miles de SKU y múltiples tiendas manteniendo rendimiento, estabilidad y procesamiento continuo.',
				'solucao_dif_topico_1'     => 'Soporte grandes volúmenes transaccionales',
				'solucao_dif_topico_2'     => 'Conecte múltiples tiendas',
				'solucao_dif_topico_3'     => 'Procese datos continuamente',
				'solucao_plat_titulo'      => 'Centralice las conexiones de toda la red',
				'solucao_plat_corpo'       => 'Unifique integraciones EDI, POS y e-commerce en una única plataforma para reducir el esfuerzo operativo y acelerar la incorporación de nuevos socios.',
				'solucao_plat_topico_1'    => 'Centralice integraciones EDI',
				'solucao_plat_topico_2'    => 'Reduzca el onboarding de proveedores',
				'solucao_plat_topico_3'    => 'Reutilice conexiones existentes',
				'solucao_acel_titulo'      => 'Comience con integraciones estructuradas',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para conectar Consinco, proveedores EDI, POS y e-commerce con más velocidad.',
				'solucao_acel_topico_1'    => 'Conecte proveedores rápidamente',
				'solucao_acel_topico_2'    => 'Adapte flujos existentes',
				'solucao_acel_topico_3'    => 'Acelere nuevas integraciones',
			)
		);
	}

	/**
	 * TOTVS Linx.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_totvs_linx() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Linx',
				'solucao_hero_titulo'      => 'Conecte Linx desde el POS al ERP corporativo',
				'solucao_hero_corpo'       => 'Integre las soluciones Linx de retail, moda, estaciones de servicio y farmacias al ERP, CRM y programas de fidelidad para centralizar las operaciones comerciales.',
				'solucao_pilares_titulo'   => 'Escale su operación Linx conectada',
				'solucao_pilares_1_titulo' => 'Conecte las verticales Linx',
				'solucao_pilares_1_desc'   => 'Integre operaciones de moda, retail, estaciones de servicio y farmacias al ecosistema corporativo.',
				'solucao_pilares_2_titulo' => 'Sincronice ventas en tiempo real',
				'solucao_pilares_2_desc'   => 'Conecte las transacciones del POS a los sistemas financieros automáticamente.',
				'solucao_pilares_3_titulo' => 'Integre fidelidad y CRM',
				'solucao_pilares_3_desc'   => 'Conecte los datos de clientes a los programas de relacionamiento.',
				'solucao_casos_titulo'     => 'Automatice procesos del retail Linx',
				'solucao_casos_1_titulo'   => 'Sincronice ventas con el ERP',
				'solucao_casos_1_desc'     => 'Envíe transacciones del POS Linx al financiero corporativo automáticamente.',
				'solucao_casos_2_titulo'   => 'Conecte programas de fidelidad',
				'solucao_casos_2_desc'     => 'Integre datos de clientes con CRM y plataformas de relacionamiento.',
				'solucao_casos_3_titulo'   => 'Consolide ventas multitienda',
				'solucao_casos_3_desc'     => 'Centralice resultados de distintas tiendas y marcas comerciales.',
				'solucao_casos_4_titulo'   => 'Integre documentos fiscales',
				'solucao_casos_4_desc'     => 'Conecte SAT, NF-e y NFC-e en una operación centralizada.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga datos del retail a disposición de agentes administrativos sin exponer el core del sistema.',
				'solucao_dif_titulo'       => 'Integraciones para alto volumen de ventas',
				'solucao_dif_corpo'        => 'Conecte operaciones de POS con procesamiento en tiempo real para soportar grandes volúmenes de transacciones comerciales.',
				'solucao_dif_topico_1'     => 'Procese ventas en tiempo real',
				'solucao_dif_topico_2'     => 'Soporte alto volumen transaccional',
				'solucao_dif_topico_3'     => 'Conecte múltiples unidades',
				'solucao_plat_titulo'      => 'Unifique las operaciones de retail',
				'solucao_plat_corpo'       => 'Centralice datos de distintas soluciones Linx para conectar ventas, financiero y CRM sin personalizar los sistemas existentes.',
				'solucao_plat_topico_1'    => 'Consolide múltiples marcas',
				'solucao_plat_topico_2'    => 'Centralice datos comerciales',
				'solucao_plat_topico_3'    => 'Evite personalizaciones complejas',
				'solucao_acel_titulo'      => 'Comience con el retail integrado',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para conectar Linx POS al ERP financiero y a los programas de fidelidad.',
				'solucao_acel_topico_1'    => 'Conecte los POS rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice flujos comerciales',
				'solucao_acel_topico_3'    => 'Acelere nuevas integraciones',
			)
		);
	}

	/**
	 * TOTVS RM.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_totvs_rm() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su TOTVS RM',
				'solucao_hero_titulo'      => 'Conecte TOTVS RM a todos los sistemas satélite',
				'solucao_hero_corpo'       => 'Integre RR. HH., educación y backoffice con nómina, control de asistencia, portales y aplicaciones corporativas para automatizar jornadas completas.',
				'solucao_pilares_titulo'   => 'Amplíe el potencial de TOTVS RM',
				'solucao_pilares_1_titulo' => 'Conecte los módulos RM',
				'solucao_pilares_1_desc'   => 'Integre RM Folha, RM Núcleo y RM Backoffice a los sistemas externos.',
				'solucao_pilares_2_titulo' => 'Automatice jornadas completas',
				'solucao_pilares_2_desc'   => 'Orqueste los ciclos de colaboradores y alumnos entre distintas plataformas.',
				'solucao_pilares_3_titulo' => 'Use webservices nativos',
				'solucao_pilares_3_desc'   => 'Conecte aplicaciones utilizando los recursos oficiales de TOTVS RM.',
				'solucao_casos_titulo'     => 'Automatice procesos de TOTVS RM',
				'solucao_casos_1_titulo'   => 'Orqueste admisión y desvinculación',
				'solucao_casos_1_desc'     => 'Conecte RM a AD, beneficios y LMS automáticamente.',
				'solucao_casos_2_titulo'   => 'Integre la jornada académica',
				'solucao_casos_2_desc'     => 'Sincronice RM Núcleo con portales y plataformas educativas.',
				'solucao_casos_3_titulo'   => 'Conecte finanzas y bancos',
				'solucao_casos_3_desc'     => 'Automatice procesos financieros del backoffice con instituciones bancarias.',
				'solucao_casos_4_titulo'   => 'Consolide datos para BI',
				'solucao_casos_4_desc'     => 'Unifique información de RR. HH. y educación para análisis estratégicos.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga datos de RR. HH. a disposición de agentes administrativos sin exponer el core del sistema.',
				'solucao_dif_titulo'       => 'Integraciones seguras para TOTVS RM',
				'solucao_dif_corpo'        => 'Proteja los datos de colaboradores y alumnos con enmascaramiento de la información en tránsito y auditoría completa de los procesos.',
				'solucao_dif_topico_1'     => 'Proteja datos personales sensibles',
				'solucao_dif_topico_2'     => 'Audite todos los movimientos',
				'solucao_dif_topico_3'     => 'Controle la información compartida',
				'solucao_plat_titulo'      => 'Centralice las jornadas de negocio',
				'solucao_plat_corpo'       => 'Sustituya integraciones puntuales entre RM y los sistemas satélite por una capa única de procesos reutilizables.',
				'solucao_plat_topico_1'    => 'Reutilice pipelines existentes',
				'solucao_plat_topico_2'    => 'Conecte múltiples sistemas',
				'solucao_plat_topico_3'    => 'Simplifique arquitecturas complejas',
				'solucao_acel_titulo'      => 'Comience con RM conectado',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para integrar RM de RR. HH. y educación a los sistemas satélite de la organización.',
				'solucao_acel_topico_1'    => 'Conecte sistemas rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice procesos listos',
				'solucao_acel_topico_3'    => 'Acelere nuevas automatizaciones',
			)
		);
	}

	/**
	 * Sankhya.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_sankhya() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Sankhya',
				'solucao_hero_titulo'      => 'Integre Sankhya con todo su ecosistema sin renunciar a la gobernanza',
				'solucao_hero_corpo'       => 'Conecte Sankhya a CRM, e-commerce, bancos y sistemas fiscales utilizando el API Gateway oficial para automatizar procesos, preservar las reglas del ERP y eliminar integraciones paralelas.',
				'solucao_pilares_titulo'   => 'Conecte Sankhya con seguridad y escalabilidad',
				'solucao_pilares_1_titulo' => 'Utilice el API Gateway oficial',
				'solucao_pilares_1_desc'   => 'Integre Sankhya utilizando los servicios oficiales de la plataforma, preservando las reglas de negocio y evitando accesos directos a la base de datos.',
				'solucao_pilares_2_titulo' => 'Respete la gobernanza del ERP',
				'solucao_pilares_2_desc'   => 'Garantice que todas las integraciones utilicen la capa de autorización nativa de Sankhya, manteniendo control y seguridad sobre los datos.',
				'solucao_pilares_3_titulo' => 'Elimine integraciones paralelas',
				'solucao_pilares_3_desc'   => 'Centralice las conexiones entre ERP, CRM y e-commerce para reducir retrabajo, facilitar mantenimientos y acelerar nuevos proyectos.',
				'solucao_casos_titulo'     => 'Automatice los principales procesos de Sankhya',
				'solucao_casos_1_titulo'   => 'Sincronice pedidos del e-commerce',
				'solucao_casos_1_desc'     => 'Envíe pedidos automáticamente a Sankhya utilizando el API Gateway oficial, reduciendo retrabajo y acelerando la facturación.',
				'solucao_casos_2_titulo'   => 'Actualice productos e inventarios',
				'solucao_casos_2_desc'     => 'Ponga productos y saldos de Sankhya a disposición de los canales de venta en tiempo real utilizando los datasets oficiales.',
				'solucao_casos_3_titulo'   => 'Automatice procesos financieros',
				'solucao_casos_3_desc'     => 'Integre cuentas por cobrar, bancos y conciliación financiera utilizando las entidades financieras de Sankhya.',
				'solucao_casos_4_titulo'   => 'Conecte CRM y ERP',
				'solucao_casos_4_desc'     => 'Sincronice leads, clientes y oportunidades entre el CRM y Sankhya para eliminar la digitación manual y mantener la información actualizada.',
				'solucao_casos_5_titulo'   => 'Ponga datos a disposición de la IA',
				'solucao_casos_5_desc'     => 'Exponga información de Sankhya a agentes de Inteligencia Artificial utilizando APIs y servidores MCP con gobernanza.',
				'solucao_dif_titulo'       => 'Integraciones que respetan la arquitectura de Sankhya',
				'solucao_dif_corpo'        => 'Todas las integraciones utilizan la capa de autorización nativa de Sankhya mediante el usuario de integración y permisos explícitos, evitando accesos directos a la base de datos y preservando la gobernanza del ERP.',
				'solucao_dif_topico_1'     => 'Utilice el API Gateway oficial.',
				'solucao_dif_topico_2'     => 'Respete permisos por entidad.',
				'solucao_dif_topico_3'     => 'Evite el acceso directo a la base.',
				'solucao_plat_titulo'      => 'Centralice todas las integraciones de Sankhya',
				'solucao_plat_corpo'       => 'Las empresas en crecimiento suelen acumular integraciones entre CRM, e-commerce y aplicaciones de ventas. Utilice una única plataforma para centralizar la gobernanza y reutilizar integraciones sin multiplicar proyectos.',
				'solucao_plat_topico_1'    => 'Centralice toda la gobernanza.',
				'solucao_plat_topico_2'    => 'Reutilice integraciones existentes.',
				'solucao_plat_topico_3'    => 'Reduzca integraciones punto a punto.',
				'solucao_acel_titulo'      => 'Comience con integraciones ya listas',
				'solucao_acel_corpo'       => 'Utilice un modelo preconfigurado para sincronizar pedidos y clientes entre CRM, e-commerce y Sankhya, reduciendo el tiempo de implementación y acelerando nuevos proyectos.',
				'solucao_acel_topico_1'    => 'Implemente pedidos rápidamente.',
				'solucao_acel_topico_2'    => 'Reutilice modelos validados.',
				'solucao_acel_topico_3'    => 'Adapte flujos a su negocio.',
			)
		);
	}

	/**
	 * Senior.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_senior() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Senior',
				'solucao_hero_titulo'      => 'Conecte Senior y automatice toda la jornada del colaborador',
				'solucao_hero_corpo'       => 'Integre Senior a los sistemas de nómina, control de asistencia, beneficios, accesos e identidad para eliminar procesos manuales, proteger datos sensibles y acelerar toda la operación de RR. HH.',
				'solucao_pilares_titulo'   => 'Transforme RR. HH. en un proceso conectado',
				'solucao_pilares_1_titulo' => 'Automatice el ciclo de vida del colaborador',
				'solucao_pilares_1_desc'   => 'Orqueste admisiones, movimientos y desvinculaciones entre Senior y todos los sistemas que participan en la jornada del colaborador.',
				'solucao_pilares_2_titulo' => 'Conecte Senior con seguridad',
				'solucao_pilares_2_desc'   => 'Integre utilizando los webservices oficiales de Senior, preservando las reglas de negocio y reduciendo la necesidad de desarrollos personalizados.',
				'solucao_pilares_3_titulo' => 'Proteja datos sensibles automáticamente',
				'solucao_pilares_3_desc'   => 'Enmascare información como CPF, salario y datos bancarios durante la integración, manteniendo conformidad y trazabilidad.',
				'solucao_casos_titulo'     => 'Automatice los principales procesos de RR. HH.',
				'solucao_casos_1_titulo'   => 'Orqueste admisiones y desvinculaciones',
				'solucao_casos_1_desc'     => 'Automatice la creación y revocación de accesos, beneficios y sistemas corporativos siempre que haya cambios en la plantilla de colaboradores.',
				'solucao_casos_2_titulo'   => 'Integre nómina y control de asistencia',
				'solucao_casos_2_desc'     => 'Sincronice los registros del control de asistencia con la nómina para reducir inconsistencias y retrabajo operativo.',
				'solucao_casos_3_titulo'   => 'Automatice la gestión de beneficios',
				'solucao_casos_3_desc'     => 'Integre proveedores de VR, VA, plan de salud y demás beneficios directamente a Senior.',
				'solucao_casos_4_titulo'   => 'Centralice indicadores de RR. HH.',
				'solucao_casos_4_desc'     => 'Consolide datos de headcount, admisiones, desvinculaciones y movimientos para alimentar plataformas de BI en tiempo real.',
				'solucao_casos_5_titulo'   => 'Revoque accesos automáticamente',
				'solucao_casos_5_desc'     => 'Garantice que los accesos físicos y digitales se eliminen automáticamente durante la desvinculación del colaborador.',
				'solucao_dif_titulo'       => 'Seguridad para datos críticos de RR. HH.',
				'solucao_dif_corpo'        => 'La plataforma identifica y enmascara automáticamente la información sensible de Senior antes de que circule entre sistemas, manteniendo auditoría y conformidad durante toda la integración.',
				'solucao_dif_topico_1'     => 'Enmascare CPF y salarios automáticamente.',
				'solucao_dif_topico_2'     => 'Proteja datos bancarios en tránsito.',
				'solucao_dif_topico_3'     => 'Audite todas las integraciones realizadas.',
				'solucao_plat_titulo'      => 'Conecte todo el ecosistema de RR. HH.',
				'solucao_plat_corpo'       => 'Elimine integraciones aisladas entre Senior, Active Directory, beneficios, control de asistencia, LMS y demás sistemas utilizando una única plataforma de integración con gestión centralizada.',
				'solucao_plat_topico_1'    => 'Centralice las integraciones de RR. HH.',
				'solucao_plat_topico_2'    => 'Reduzca la dependencia del equipo de TI.',
				'solucao_plat_topico_3'    => 'Ajuste flujos con más agilidad.',
				'solucao_acel_titulo'      => 'Comience con flujos listos de RR. HH.',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para automatizar admisiones, movimientos y desvinculaciones, reduciendo el tiempo de implementación y acelerando nuevos proyectos.',
				'solucao_acel_topico_1'    => 'Implemente flujos JML rápidamente.',
				'solucao_acel_topico_2'    => 'Reutilice modelos ya validados.',
				'solucao_acel_topico_3'    => 'Adapte procesos a su RR. HH.',
			)
		);
	}

	/**
	 * Dynamics 365.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_dynamics_365() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Microsoft Dynamics',
				'solucao_hero_titulo'      => 'Integre Microsoft Dynamics 365 sin quedar limitado a Power Platform',
				'solucao_hero_corpo'       => 'Conecte Dynamics 365, Business Central y Finance & Operations al resto de su operación utilizando una única plataforma para automatizar procesos, compartir datos y eliminar integraciones aisladas.',
				'solucao_pilares_titulo'   => 'Conecte todo el ecosistema Microsoft',
				'solucao_pilares_1_titulo' => 'Utilice APIs nativas de Dynamics',
				'solucao_pilares_1_desc'   => 'Integre utilizando OData y la Dynamics 365 Web API para preservar la arquitectura de Microsoft y acelerar nuevos proyectos.',
				'solucao_pilares_2_titulo' => 'Conecte distintos sistemas',
				'solucao_pilares_2_desc'   => 'Orqueste Dynamics, SAP, Salesforce, Totvs y otras aplicaciones corporativas en una única plataforma de integración.',
				'solucao_pilares_3_titulo' => 'Reduzca la dependencia de Power Platform',
				'solucao_pilares_3_desc'   => 'Utilice una capa central de integración para escenarios corporativos complejos, manteniendo flexibilidad y escalabilidad.',
				'solucao_casos_titulo'     => 'Automatice los principales procesos de Dynamics',
				'solucao_casos_1_titulo'   => 'Sincronice CRM y ERP',
				'solucao_casos_1_desc'     => 'Comparta oportunidades, cuentas y clientes entre Dynamics CRM y los ERP corporativos automáticamente.',
				'solucao_casos_2_titulo'   => 'Automatice procesos financieros',
				'solucao_casos_2_desc'     => 'Integre Dynamics 365 Finance & Operations a bancos y plataformas de conciliación financiera.',
				'solucao_casos_3_titulo'   => 'Conecte Business Central al e-commerce',
				'solucao_casos_3_desc'     => 'Sincronice pedidos, clientes e inventarios entre Business Central y sus canales de venta.',
				'solucao_casos_4_titulo'   => 'Centralice datos maestros',
				'solucao_casos_4_desc'     => 'Mantenga clientes, productos y registros sincronizados entre Dynamics y otros sistemas corporativos.',
				'solucao_casos_5_titulo'   => 'Ponga datos a disposición de la Inteligencia Artificial',
				'solucao_casos_5_desc'     => 'Exponga información de Dynamics a agentes de IA utilizando APIs gobernadas y servidores MCP.',
				'solucao_dif_titulo'       => 'Seguridad corporativa integrada al ecosistema Microsoft',
				'solucao_dif_corpo'        => 'Las integraciones utilizan autenticación vía Azure AD (Microsoft Entra ID) con OAuth2 y soporte para entornos multi-tenant, preservando los estándares de seguridad de Dynamics 365.',
				'solucao_dif_topico_1'     => 'Utilice autenticación vía Azure AD.',
				'solucao_dif_topico_2'     => 'Soporte entornos Dynamics multi-tenant.',
				'solucao_dif_topico_3'     => 'Proteja integraciones con OAuth2.',
				'solucao_plat_titulo'      => 'Una plataforma para todo su entorno Microsoft',
				'solucao_plat_corpo'       => 'Las empresas que utilizan Dynamics con frecuencia conviven con otros ERP y CRM. Centralice todas las integraciones en una única plataforma para simplificar proyectos, adquisiciones y operaciones multi-ERP.',
				'solucao_plat_topico_1'    => 'Conecte Dynamics y otros ERP.',
				'solucao_plat_topico_2'    => 'Elimine silos de integración.',
				'solucao_plat_topico_3'    => 'Simplifique escenarios de M&A.',
				'solucao_acel_titulo'      => 'Comience utilizando integraciones listas',
				'solucao_acel_corpo'       => 'Implemente rápidamente un modelo preconfigurado para sincronizar cuentas, oportunidades y pedidos entre Dynamics 365 y sistemas externos utilizando OData y Web API.',
				'solucao_acel_topico_1'    => 'Implemente integraciones rápidamente.',
				'solucao_acel_topico_2'    => 'Reutilice modelos ya validados.',
				'solucao_acel_topico_3'    => 'Adapte flujos a su negocio.',
			)
		);
	}

	/**
	 * Arius ERP.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_arius_erp() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Arius',
				'solucao_hero_titulo'      => 'Sincronice Arius ERP con todo su ecosistema de ventas',
				'solucao_hero_corpo'       => 'Integre la gestión de tiendas, el POS y la retaguardia financiera para eliminar controles manuales y garantizar visibilidad total sobre su retail en tiempo real.',
				'solucao_pilares_titulo'   => 'Escale su operación industrial conectada',
				'solucao_pilares_1_titulo' => 'Conecte sistemas industriales',
				'solucao_pilares_1_desc'   => 'Integre Arius ERP con MES y aplicaciones de la planta.',
				'solucao_pilares_2_titulo' => 'Automatice producción y gestión',
				'solucao_pilares_2_desc'   => 'Sincronice órdenes de producción y datos operativos automáticamente.',
				'solucao_pilares_3_titulo' => 'Reduzca los controles manuales',
				'solucao_pilares_3_desc'   => 'Sustituya planillas por procesos conectados entre áreas.',
				'solucao_casos_titulo'     => 'Automatice procesos industriales con Arius',
				'solucao_casos_1_titulo'   => 'Conecte la producción al ERP',
				'solucao_casos_1_desc'     => 'Sincronice órdenes de producción entre MES y Arius.',
				'solucao_casos_2_titulo'   => 'Integre procesos fiscales',
				'solucao_casos_2_desc'     => 'Automatice la emisión fiscal y los datos financieros del ERP.',
				'solucao_casos_3_titulo'   => 'Consolide inventarios industriales',
				'solucao_casos_3_desc'     => 'Centralice información de inventario entre múltiples plantas.',
				'solucao_casos_4_titulo'   => 'Conecte el CRM a Arius',
				'solucao_casos_4_desc'     => 'Integre pedidos comerciales a la planificación industrial.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga datos industriales a disposición de agentes administrativos sin exponer el core operativo.',
				'solucao_dif_titulo'       => 'Integraciones dedicadas para Arius ERP',
				'solucao_dif_corpo'        => 'Utilice conectores adaptados al protocolo de Arius con implementación dentro del entorno del cliente para un mayor control operativo.',
				'solucao_dif_topico_1'     => 'Use conectores dedicados',
				'solucao_dif_topico_2'     => 'Implemente en el entorno interno',
				'solucao_dif_topico_3'     => 'Controle integraciones industriales',
				'solucao_plat_titulo'      => 'Conecte su industria en evolución',
				'solucao_plat_corpo'       => 'Centralice integraciones entre Arius, MES, CRM y nuevos sistemas sin depender de desarrolladores especializados en el ERP.',
				'solucao_plat_topico_1'    => 'Reduzca la dependencia técnica',
				'solucao_plat_topico_2'    => 'Centralice nuevos sistemas',
				'solucao_plat_topico_3'    => 'Escale procesos industriales',
				'solucao_acel_titulo'      => 'Comience con Arius integrado',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para conectar Arius ERP al MES y al CRM con flujos industriales estructurados.',
				'solucao_acel_topico_1'    => 'Conecte el MES rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice integraciones industriales',
				'solucao_acel_topico_3'    => 'Acelere nuevos proyectos',
			)
		);
	}

	/**
	 * CISS Poder ERP.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_ciss_poder_erp() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su CISS',
				'solucao_hero_titulo'      => 'Conecte CISSPoder a toda la operación del retail',
				'solucao_hero_corpo'       => 'Integre compras, inventario, POS, e-commerce, proveedores y finanzas para mantener toda la operación minorista sincronizada en tiempo real.',
				'solucao_pilares_titulo'   => 'Integre toda la operación minorista',
				'solucao_pilares_1_titulo' => 'Sincronice ventas e inventario',
				'solucao_pilares_1_desc'   => 'Conecte POS, e-commerce y marketplaces al CISSPoder para mantener ventas e inventarios actualizados.',
				'solucao_pilares_2_titulo' => 'Conecte proveedores',
				'solucao_pilares_2_desc'   => 'Automatice pedidos, facturas e información de proveedores por EDI, reduciendo los registros manuales.',
				'solucao_pilares_3_titulo' => 'Integre compras y abastecimiento',
				'solucao_pilares_3_desc'   => 'Lleve datos de ventas e inventario a procesos de compra y reposición más eficientes.',
				'solucao_casos_titulo'     => 'Automatice procesos del retail',
				'solucao_casos_1_titulo'   => 'Sincronice las ventas del POS',
				'solucao_casos_1_desc'     => 'Lleve las ventas de las tiendas al CISSPoder en tiempo real y mantenga la operación actualizada.',
				'solucao_casos_2_titulo'   => 'Conecte el e-commerce',
				'solucao_casos_2_desc'     => 'Integre pedidos e inventario entre el CISSPoder y los canales digitales de venta.',
				'solucao_casos_3_titulo'   => 'Integre proveedores vía EDI',
				'solucao_casos_3_desc'     => 'Automatice la recepción de pedidos y documentos enviados por los proveedores.',
				'solucao_casos_4_titulo'   => 'Automatice la reposición',
				'solucao_casos_4_desc'     => 'Conecte ventas, inventario y abastecimiento para acelerar los pedidos de reposición.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga datos asistenciales a disposición de agentes administrativos sin exponer el core clínico.',
				'solucao_dif_titulo'       => 'Integraciones para operaciones de alto volumen',
				'solucao_dif_corpo'        => 'Conecte el CISSPoder a los sistemas que sostienen su operación, manteniendo datos de ventas, inventario y compras sincronizados incluso en redes con múltiples tiendas.',
				'solucao_dif_topico_1'     => 'Procese grandes volúmenes de transacciones',
				'solucao_dif_topico_2'     => 'Sincronice datos en tiempo real',
				'solucao_dif_topico_3'     => 'Conecte múltiples tiendas y sistemas',
				'solucao_plat_titulo'      => 'Una operación de retail conectada',
				'solucao_plat_corpo'       => 'El CISSPoder ya centraliza la gestión del retail. CLI Connect amplía esa capacidad conectando el ERP a los sistemas que forman parte de la operación.',
				'solucao_plat_topico_1'    => 'Conecte POS y e-commerce',
				'solucao_plat_topico_2'    => 'Integre proveedores y WMS',
				'solucao_plat_topico_3'    => 'Centralice datos entre tiendas',
				'solucao_acel_titulo'      => 'Comience con una integración lista',
				'solucao_acel_corpo'       => 'Use un modelo listo para conectar el CISSPoder a los principales sistemas de la operación minorista y acelerar la implementación.',
				'solucao_acel_topico_1'    => 'Conecte POS y e-commerce',
				'solucao_acel_topico_2'    => 'Automatice integraciones con proveedores',
				'solucao_acel_topico_3'    => 'Reutilice flujos entre tiendas',
			)
		);
	}

	/**
	 * IFS Cloud.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_ifs_cloud() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su IFS Cloud',
				'solucao_hero_titulo'      => 'Conecte IFS Cloud al resto de la operación industrial',
				'solucao_hero_corpo'       => 'Integre ERP, gestión de activos y field service con MES, IoT y sistemas corporativos para transformar los datos operativos en decisiones más rápidas.',
				'solucao_pilares_titulo'   => 'Amplíe el potencial de IFS Cloud',
				'solucao_pilares_1_titulo' => 'Use APIs nativas de IFS',
				'solucao_pilares_1_desc'   => 'Conecte sistemas utilizando la REST API oficial de IFS Cloud.',
				'solucao_pilares_2_titulo' => 'Conecte activos industriales',
				'solucao_pilares_2_desc'   => 'Integre mantenimiento, sensores y datos operativos en tiempo real.',
				'solucao_pilares_3_titulo' => 'Escale el field service',
				'solucao_pilares_3_desc'   => 'Conecte equipos externos, CRM y procesos de atención.',
				'solucao_casos_titulo'     => 'Automatice procesos con IFS Cloud',
				'solucao_casos_1_titulo'   => 'Integre mantenimiento e IoT',
				'solucao_casos_1_desc'     => 'Conecte órdenes EAM con sensores y datos industriales.',
				'solucao_casos_2_titulo'   => 'Conecte field service al CRM',
				'solucao_casos_2_desc'     => 'Sincronice las atenciones externas con los procesos comerciales.',
				'solucao_casos_3_titulo'   => 'Consolide datos financieros',
				'solucao_casos_3_desc'     => 'Integre IFS y el ERP corporativo para una visión financiera única.',
				'solucao_casos_4_titulo'   => 'Exponga datos para la IA',
				'solucao_casos_4_desc'     => 'Ponga los activos a disposición como herramientas para agentes inteligentes.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga datos operativos a disposición de agentes administrativos sin exponer el core de IFS.',
				'solucao_dif_titulo'       => 'Integraciones seguras para IFS Cloud',
				'solucao_dif_corpo'        => 'Conecte aplicaciones corporativas utilizando autenticación OAuth2 por la REST API de IFS Cloud con seguridad y control.',
				'solucao_dif_topico_1'     => 'Utilice OAuth2 seguro',
				'solucao_dif_topico_2'     => 'Conecte APIs oficiales',
				'solucao_dif_topico_3'     => 'Proteja datos industriales',
				'solucao_plat_titulo'      => 'Centralice datos de activos industriales',
				'solucao_plat_corpo'       => 'Conecte mantenimiento, ERP e inteligencia artificial en una única capa sin alterar el core de IFS Cloud.',
				'solucao_plat_topico_1'    => 'Integre sistemas corporativos',
				'solucao_plat_topico_2'    => 'Evite personalizar IFS',
				'solucao_plat_topico_3'    => 'Escale operaciones industriales',
				'solucao_acel_titulo'      => 'Comience con activos conectados',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para conectar IFS EAM/FSM al ERP corporativo y a plataformas IoT.',
				'solucao_acel_topico_1'    => 'Conecte IoT rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice flujos industriales',
				'solucao_acel_topico_3'    => 'Acelere nuevos proyectos',
			)
		);
	}

	/**
	 * QAD Redzone.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_qad_redzone() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su QAD Redzone',
				'solucao_hero_titulo'      => 'Conecte QAD Redzone al ERP y a la planta en tiempo real',
				'solucao_hero_corpo'       => 'Integre la productividad de línea, la manufactura y la calidad al QAD ERP y al BI corporativo para transformar los datos operativos en decisiones rápidas.',
				'solucao_pilares_titulo'   => 'Transforme los datos de la fábrica en valor',
				'solucao_pilares_1_titulo' => 'Monitoree la productividad en tiempo real',
				'solucao_pilares_1_desc'   => 'Sincronice datos de OEE y desempeño de las líneas automáticamente.',
				'solucao_pilares_2_titulo' => 'Integre con QAD ERP',
				'solucao_pilares_2_desc'   => 'Conecte la ejecución industrial a los procesos corporativos del ERP.',
				'solucao_pilares_3_titulo' => 'Conecte fábrica y BI',
				'solucao_pilares_3_desc'   => 'Lleve los datos operativos a análisis estratégicos corporativos.',
				'solucao_casos_titulo'     => 'Automatice procesos de manufactura',
				'solucao_casos_1_titulo'   => 'Integre el OEE al ERP',
				'solucao_casos_1_desc'     => 'Envíe los indicadores de productividad de Redzone al QAD ERP.',
				'solucao_casos_2_titulo'   => 'Controle la calidad integrada',
				'solucao_casos_2_desc'     => 'Conecte las no conformidades a los procesos de calidad.',
				'solucao_casos_3_titulo'   => 'Consolide producción multiplanta',
				'solucao_casos_3_desc'     => 'Centralice datos industriales de distintas unidades productivas.',
				'solucao_casos_4_titulo'   => 'Alerte paradas de línea',
				'solucao_casos_4_desc'     => 'Dispare alertas en tiempo real para el mantenimiento preventivo.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga datos operativos a disposición de agentes inteligentes sin exponer el core de Redzone.',
				'solucao_dif_titulo'       => 'Integraciones para manufactura en tiempo real',
				'solucao_dif_corpo'        => 'Procese grandes volúmenes de datos industriales con conectividad preparada para sensores y operaciones continuas de producción.',
				'solucao_dif_topico_1'     => 'Procese datos en alto volumen',
				'solucao_dif_topico_2'     => 'Conecte eventos industriales',
				'solucao_dif_topico_3'     => 'Acompañe la producción en tiempo real',
				'solucao_plat_titulo'      => 'Conecte toda su operación industrial',
				'solucao_plat_corpo'       => 'Centralice los datos de la planta, del ERP y del BI para eliminar información aislada y ampliar el valor de Redzone.',
				'solucao_plat_topico_1'    => 'Integre fábrica y oficina',
				'solucao_plat_topico_2'    => 'Centralice datos productivos',
				'solucao_plat_topico_3'    => 'Amplíe la visibilidad operativa',
				'solucao_acel_titulo'      => 'Comience con la manufactura conectada',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para conectar QAD Redzone al QAD ERP y a plataformas analíticas corporativas.',
				'solucao_acel_topico_1'    => 'Conecte datos rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice estándares industriales',
				'solucao_acel_topico_3'    => 'Acelere proyectos fabriles',
			)
		);
	}

	/**
	 * RP Info.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_rp_info() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su RP Info',
				'solucao_hero_titulo'      => 'Conecte RP Info desde la caja hasta el centro de distribución',
				'solucao_hero_corpo'       => 'Integre el frente de tienda, el ERP, los proveedores y el BI para sincronizar ventas, inventario y operaciones del retail en tiempo real.',
				'solucao_pilares_titulo'   => 'Escale su retail conectado',
				'solucao_pilares_1_titulo' => 'Conecte operaciones de retail',
				'solucao_pilares_1_desc'   => 'Integre Flex ERP, RPDV, Mix, Target y Task al ecosistema comercial.',
				'solucao_pilares_2_titulo' => 'Sincronice ventas en tiempo real',
				'solucao_pilares_2_desc'   => 'Conecte las transacciones de la caja al ERP sin procesos manuales.',
				'solucao_pilares_3_titulo' => 'Integre proveedores vía EDI',
				'solucao_pilares_3_desc'   => 'Automatice el intercambio de datos con los socios comerciales.',
				'solucao_casos_titulo'     => 'Automatice procesos del retail RP Info',
				'solucao_casos_1_titulo'   => 'Sincronice las ventas del POS',
				'solucao_casos_1_desc'     => 'Actualice las ventas del RPDV en Flex ERP en tiempo real.',
				'solucao_casos_2_titulo'   => 'Conecte proveedores vía EDI',
				'solucao_casos_2_desc'     => 'Automatice pedidos e información con los socios comerciales.',
				'solucao_casos_3_titulo'   => 'Consolide ventas multitienda',
				'solucao_casos_3_desc'     => 'Centralice resultados de distintas unidades para su análisis.',
				'solucao_casos_4_titulo'   => 'Centralice procesos fiscales',
				'solucao_casos_4_desc'     => 'Integre SPED y NF-e a los procesos corporativos.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga datos operativos a disposición de agentes inteligentes sin exponer el core de RP Info.',
				'solucao_dif_titulo'       => 'Integraciones para retail a escala',
				'solucao_dif_corpo'        => 'Conecte operaciones con miles de cajas utilizando una arquitectura preparada para alto volumen transaccional.',
				'solucao_dif_topico_1'     => 'Procese ventas a escala',
				'solucao_dif_topico_2'     => 'Sincronice datos rápidamente',
				'solucao_dif_topico_3'     => 'Soporte múltiples cajas',
				'solucao_plat_titulo'      => 'Unifique los datos del retail',
				'solucao_plat_corpo'       => 'Centralice ventas, inventario y proveedores en una única capa de integración sin depender de procesos batch.',
				'solucao_plat_topico_1'    => 'Consolide ventas en tiempo real',
				'solucao_plat_topico_2'    => 'Centralice datos operativos',
				'solucao_plat_topico_3'    => 'Reduzca procesos manuales',
				'solucao_acel_titulo'      => 'Comience con el retail integrado',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para conectar RP Info al EDI de proveedores y a plataformas analíticas.',
				'solucao_acel_topico_1'    => 'Conecte proveedores rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice flujos de retail',
				'solucao_acel_topico_3'    => 'Acelere nuevas integraciones',
			)
		);
	}

	/**
	 * Viasoft.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_viasoft() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Viasoft',
				'solucao_hero_titulo'      => 'Conecte Viasoft al resto de la operación',
				'solucao_hero_corpo'       => 'Integre los ERP especializados en agro, combustibles e industria con bancos, fiscal y sistemas comerciales para unificar procesos.',
				'solucao_pilares_titulo'   => 'Amplíe el potencial de Viasoft',
				'solucao_pilares_1_titulo' => 'Conecte verticales especializadas',
				'solucao_pilares_1_desc'   => 'Integre Agrotitan, Filt IA+ y las soluciones Viasoft por segmento.',
				'solucao_pilares_2_titulo' => 'Automatice procesos fiscales',
				'solucao_pilares_2_desc'   => 'Conecte las obligaciones fiscales según cada vertical de negocio.',
				'solucao_pilares_3_titulo' => 'Integre operaciones financieras',
				'solucao_pilares_3_desc'   => 'Sincronice bancos y procesos financieros automáticamente.',
				'solucao_casos_titulo'     => 'Automatice procesos de Viasoft',
				'solucao_casos_1_titulo'   => 'Integre ventas y finanzas',
				'solucao_casos_1_desc'     => 'Sincronice ventas agrícolas y operaciones comerciales con el área financiera.',
				'solucao_casos_2_titulo'   => 'Automatice procesos fiscales',
				'solucao_casos_2_desc'     => 'Conecte NF-e y SPED según cada segmento.',
				'solucao_casos_3_titulo'   => 'Concilie operaciones bancarias',
				'solucao_casos_3_desc'     => 'Automatice conciliaciones de distribuidores y cooperativas.',
				'solucao_casos_4_titulo'   => 'Consolide datos operativos',
				'solucao_casos_4_desc'     => 'Unifique información multisucursal para análisis estratégicos.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga datos operativos a disposición de agentes inteligentes sin exponer el core de Viasoft.',
				'solucao_dif_titulo'       => 'Integraciones adaptadas a su segmento',
				'solucao_dif_corpo'        => 'Conecte operaciones con reglas fiscales y regulatorias específicas para agro, combustibles y demás verticales atendidas por Viasoft.',
				'solucao_dif_topico_1'     => 'Adapte integraciones por vertical',
				'solucao_dif_topico_2'     => 'Cumpla reglas regulatorias específicas',
				'solucao_dif_topico_3'     => 'Conecte operaciones especializadas',
				'solucao_plat_titulo'      => 'Unifique distintas verticales Viasoft',
				'solucao_plat_corpo'       => 'Centralice datos financieros y fiscales de distintas operaciones en una única capa de integración.',
				'solucao_plat_topico_1'    => 'Integre múltiples negocios',
				'solucao_plat_topico_2'    => 'Centralice información corporativa',
				'solucao_plat_topico_3'    => 'Evite integraciones aisladas',
				'solucao_acel_titulo'      => 'Comience con operaciones conectadas',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para conectar Viasoft al área financiera y fiscal con rapidez.',
				'solucao_acel_topico_1'    => 'Conecte datos rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice flujos validados',
				'solucao_acel_topico_3'    => 'Acelere nuevas integraciones',
			)
		);
	}

	/**
	 * Onclick ERP.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_onclick_erp() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Onclick',
				'solucao_hero_titulo'      => 'Conecte el ERP Onclick al e-commerce, la industria y la distribución',
				'solucao_hero_corpo'       => 'Integre retail, marketplaces, ventas y procesos fiscales para mantener inventario, pedidos y operaciones sincronizados en todos los canales.',
				'solucao_pilares_titulo'   => 'Amplíe el potencial de Onclick',
				'solucao_pilares_1_titulo' => 'Conecte todos los módulos',
				'solucao_pilares_1_desc'   => 'Integre retail, e-commerce, industria, distribución y servicios.',
				'solucao_pilares_2_titulo' => 'Sincronice inventarios omnicanal',
				'solucao_pilares_2_desc'   => 'Mantenga las tiendas físicas y los canales digitales siempre actualizados.',
				'solucao_pilares_3_titulo' => 'Centralice procesos fiscales',
				'solucao_pilares_3_desc'   => 'Integre información fiscal y contable automáticamente.',
				'solucao_casos_titulo'     => 'Automatice procesos con Onclick',
				'solucao_casos_1_titulo'   => 'Sincronice pedidos digitales',
				'solucao_casos_1_desc'     => 'Envíe pedidos del e-commerce directamente al ERP.',
				'solucao_casos_2_titulo'   => 'Integre marketplaces',
				'solucao_casos_2_desc'     => 'Centralice inventario y ventas de múltiples canales.',
				'solucao_casos_3_titulo'   => 'Automatice la fuerza de ventas',
				'solucao_casos_3_desc'     => 'Conecte a los vendedores móviles con los procesos del ERP.',
				'solucao_casos_4_titulo'   => 'Consolide órdenes de servicio',
				'solucao_casos_4_desc'     => 'Centralice las operaciones de servicios en un único flujo.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga datos del ERP a disposición de agentes de IA utilizando APIs gobernadas y servidores MCP.',
				'solucao_dif_titulo'       => 'Integraciones adaptadas a Onclick',
				'solucao_dif_corpo'        => 'Conecte distintos módulos de Onclick con una arquitectura preparada para retail, industria, distribución y servicios.',
				'solucao_dif_topico_1'     => 'Integre módulos especializados.',
				'solucao_dif_topico_2'     => 'Adapte flujos operativos.',
				'solucao_dif_topico_3'     => 'Conecte múltiples canales.',
				'solucao_plat_titulo'      => 'Unifique su operación omnicanal',
				'solucao_plat_corpo'       => 'Centralice tiendas, e-commerce y marketplaces en una única capa de integración para evitar inventarios desactualizados.',
				'solucao_plat_topico_1'    => 'Sincronice canales de venta.',
				'solucao_plat_topico_2'    => 'Centralice datos comerciales.',
				'solucao_plat_topico_3'    => 'Evite procesos desconectados.',
				'solucao_acel_titulo'      => 'Comience con el e-commerce integrado',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para conectar Onclick a los principales canales digitales y marketplaces.',
				'solucao_acel_topico_1'    => 'Conecte canales rápidamente.',
				'solucao_acel_topico_2'    => 'Reutilice flujos comerciales.',
				'solucao_acel_topico_3'    => 'Acelere nuevas integraciones.',
			)
		);
	}

	/**
	 * Target Sistemas.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_target_sistemas() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Target',
				'solucao_hero_titulo'      => 'Conecte su ERP de distribución a la industria, los clientes y las finanzas',
				'solucao_hero_corpo'       => 'Integre Target ERP a los socios industriales, la fuerza de ventas, los bancos y los sistemas logísticos para automatizar operaciones de distribución con escala.',
				'solucao_pilares_titulo'   => 'Escale su operación de distribución conectada',
				'solucao_pilares_1_titulo' => 'Conecte flujos de distribución',
				'solucao_pilares_1_desc'   => 'Integre procesos fiscales, logísticos y comerciales del canal mayorista al ecosistema corporativo.',
				'solucao_pilares_2_titulo' => 'Automatice integraciones EDI',
				'solucao_pilares_2_desc'   => 'Conecte proveedores industriales a Target con intercambio automático de información.',
				'solucao_pilares_3_titulo' => 'Sincronice la fuerza de ventas',
				'solucao_pilares_3_desc'   => 'Mantenga los pedidos móviles actualizados en el ERP en tiempo real.',
				'solucao_casos_titulo'     => 'Automatice procesos de distribución',
				'solucao_casos_1_titulo'   => 'Conecte EDI con las industrias',
				'solucao_casos_1_desc'     => 'Automatice el intercambio de datos Sell Out con los proveedores asociados.',
				'solucao_casos_2_titulo'   => 'Sincronice pedidos móviles',
				'solucao_casos_2_desc'     => 'Envíe los pedidos de la fuerza de ventas directamente al Target ERP.',
				'solucao_casos_3_titulo'   => 'Concilie operaciones financieras',
				'solucao_casos_3_desc'     => 'Integre bancos y procesos financieros entre múltiples empresas.',
				'solucao_casos_4_titulo'   => 'Conecte la logística al ERP',
				'solucao_casos_4_desc'     => 'Integre WMS y ruteo para controlar las operaciones de entrega.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga datos de distribución a disposición de agentes para automatizar la atención y los análisis.',
				'solucao_dif_titulo'       => 'Integraciones para escala distributiva',
				'solucao_dif_corpo'        => 'Conecte operaciones con alto volumen transaccional manteniendo el rendimiento en múltiples sucursales, SKU e integraciones simultáneas.',
				'solucao_dif_topico_1'     => 'Soporte alto volumen de datos',
				'solucao_dif_topico_2'     => 'Conecte múltiples sucursales',
				'solucao_dif_topico_3'     => 'Escale operaciones comerciales',
				'solucao_plat_titulo'      => 'Unifique las conexiones de la distribución',
				'solucao_plat_corpo'       => 'Centralice integraciones EDI, fuerza de ventas y sistemas logísticos para reducir el esfuerzo de conexión con nuevos socios.',
				'solucao_plat_topico_1'    => 'Centralice integraciones industriales',
				'solucao_plat_topico_2'    => 'Acelere nuevos proveedores',
				'solucao_plat_topico_3'    => 'Reduzca proyectos repetitivos',
				'solucao_acel_titulo'      => 'Comience con la distribución conectada',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para conectar Target ERP a los socios industriales y a las aplicaciones de fuerza de ventas.',
				'solucao_acel_topico_1'    => 'Conecte proveedores rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice flujos EDI',
				'solucao_acel_topico_3'    => 'Acelere nuevos socios',
			)
		);
	}

	/**
	 * Neogrid.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_neogrid() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Neogrid',
				'solucao_hero_titulo'      => 'Conecte su red EDI al ERP sin depender solo de conectores listos',
				'solucao_hero_corpo'       => 'Integre el ecosistema Neogrid de EDI y visibilidad de retail a los ERP, BI y sistemas corporativos para ampliar su operación más allá de las conexiones nativas.',
				'solucao_pilares_titulo'   => 'Amplíe el valor de su red Neogrid',
				'solucao_pilares_1_titulo' => 'Conecte cualquier ERP corporativo',
				'solucao_pilares_1_desc'   => 'Integre Neogrid a ERP más allá de los conectores nativos ya disponibles en el mercado.',
				'solucao_pilares_2_titulo' => 'Sincronice datos comerciales',
				'solucao_pilares_2_desc'   => 'Conecte pedidos, facturas e información de retail al ERP interno.',
				'solucao_pilares_3_titulo' => 'Centralice los datos de la red',
				'solucao_pilares_3_desc'   => 'Consolide información de ventas e inventario de múltiples socios comerciales.',
				'solucao_casos_titulo'     => 'Automatice procesos de la cadena comercial',
				'solucao_casos_1_titulo'   => 'Traduzca pedidos EDI automáticamente',
				'solucao_casos_1_desc'     => 'Convierta los pedidos recibidos por Neogrid al formato del ERP interno.',
				'solucao_casos_2_titulo'   => 'Conecte datos al BI corporativo',
				'solucao_casos_2_desc'     => 'Envíe información de retail y distribución a los análisis estratégicos.',
				'solucao_casos_3_titulo'   => 'Integre facturas a las finanzas',
				'solucao_casos_3_desc'     => 'Conecte los documentos fiscales que circulan por Neogrid a los sistemas financieros.',
				'solucao_casos_4_titulo'   => 'Monitoree quiebres e inventario',
				'solucao_casos_4_desc'     => 'Consolide indicadores comerciales para los equipos de ventas y operaciones.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga datos comerciales y de retail a disposición de agentes para automatizar la atención y los análisis.',
				'solucao_dif_titulo'       => 'Traduzca EDI con seguridad',
				'solucao_dif_corpo'        => 'Conecte APIs y formatos EDI de Neogrid con traducción de mensajes para garantizar la comunicación entre distintos sistemas.',
				'solucao_dif_topico_1'     => 'Integre APIs y EDI Neogrid',
				'solucao_dif_topico_2'     => 'Traduzca formatos automáticamente',
				'solucao_dif_topico_3'     => 'Conecte ERP heterogéneos',
				'solucao_plat_titulo'      => 'Conecte más allá del ERP principal',
				'solucao_plat_corpo'       => 'Amplíe el ecosistema Neogrid conectando datos de EDI y visibilidad a nuevos sistemas sin limitar la operación a los conectores existentes.',
				'solucao_plat_topico_1'    => 'Centralice los datos de la cadena',
				'solucao_plat_topico_2'    => 'Conecte sistemas adicionales',
				'solucao_plat_topico_3'    => 'Expanda integraciones existentes',
				'solucao_acel_titulo'      => 'Comience con datos comerciales conectados',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para conectar Neogrid a ERP y BI con flujos estructurados de datos comerciales.',
				'solucao_acel_topico_1'    => 'Conecte EDI rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice flujos comerciales',
				'solucao_acel_topico_3'    => 'Acelere nuevas integraciones',
			)
		);
	}
}
