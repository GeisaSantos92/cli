<?php
/**
 * Seed — texto em espanhol das landings de solução: IA, dados e nuvem.
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
 * IA, dados e nuvem — texto em espanhol.
 */
trait Cliconnect_Seed_Es_Solucoes_Ia_Nuvem {

	/**
	 * Claude.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_claude() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'integre su claude',
				'solucao_hero_titulo'      => 'Transforme el conocimiento empresarial en acciones con Claude',
				'solucao_hero_corpo'       => 'Claude conecta documentos, datos y sistemas corporativos para consultar información, interpretar el contexto y ejecutar acciones con precisión y control.',
				'solucao_pilares_titulo'   => 'Convierta el conocimiento en decisiones',
				'solucao_pilares_1_titulo' => 'Analice grandes volúmenes de información',
				'solucao_pilares_1_desc'   => 'Procese documentos, historiales y registros para extraer insights relevantes sin depender de búsquedas manuales.',
				'solucao_pilares_2_titulo' => 'Consulte el conocimiento de la empresa',
				'solucao_pilares_2_desc'   => 'Conecte fuentes internas — wikis, bases de datos, políticas — para que Claude responda con el contexto real del negocio.',
				'solucao_pilares_3_titulo' => 'Ejecute herramientas',
				'solucao_pilares_3_desc'   => 'Cree un pedido de venta, actualice un CRM o abra un ticket: Claude actúa en los sistemas con los permisos correctos.',
				'solucao_casos_titulo'     => 'Aplique Claude donde el conocimiento importa',
				'solucao_casos_1_titulo'   => 'Revise contratos automáticamente',
				'solucao_casos_2_titulo'   => 'Consulte políticas internas',
				'solucao_casos_3_titulo'   => 'Analice solicitudes de clientes',
				'solucao_casos_4_titulo'   => 'Compare propuestas comerciales',
				'solucao_casos_5_titulo'   => 'Resuma historiales operativos',
				'solucao_diagrama_titulo'  => 'Una nueva forma de conectar la IA a sus sistemas',
				'solucao_int_eyebrow'      => 'integraciones',
				'solucao_int_titulo'       => 'Integre todos sus sistemas con Claude',
				'solucao_int_subtitulo'    => 'Miles de integraciones listas para usar',
				'solucao_dif_titulo'       => 'Integre IA con control sobre sus datos',
				'solucao_dif_corpo'        => 'Conecte Claude a los sistemas corporativos manteniendo el control sobre los datos, los permisos y las acciones, sin comprometer la seguridad ni la gobernanza.',
				'solucao_dif_topico_1'     => 'Controle qué datos llegan a los modelos',
				'solucao_dif_topico_2'     => 'Proteja los datos en tránsito y en reposo',
				'solucao_dif_topico_3'     => 'Aplique reglas antes de ejecutar acciones',
				'solucao_plat_titulo'      => 'Centralice conocimiento, sistemas y procesos',
				'solucao_plat_corpo'       => 'Claude genera más valor cuando puede acceder al contexto necesario. La plataforma CLI Connect conecta fuentes, orquesta flujos y mantiene la trazabilidad.',
				'solucao_plat_topico_1'    => 'Conecte diferentes fuentes de información',
				'solucao_plat_topico_2'    => 'Reutilice datos en nuevos procesos',
				'solucao_plat_topico_3'    => 'Orqueste resultados entre sistemas',
				'solucao_acel_eyebrow'     => 'MCP server',
				'solucao_acel_titulo'      => 'Dé herramientas a Claude sin necesidad de desarrollar APIs',
				'solucao_acel_corpo'       => 'Transforme procesos corporativos en Tools para Claude, definiendo exactamente qué información puede consultar y qué acciones puede ejecutar.',
				'solucao_acel_topico_1'    => 'Convierta procesos en herramientas de IA',
				'solucao_acel_topico_2'    => 'Controle entradas, salidas e información',
				'solucao_acel_topico_3'    => 'Póngalo todo a disposición por el MCP Server',
			)
		);
	}

	/**
	 * ChatGPT.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_chatgpt() {
		return $this->solucao_es(
			array(
				'solucao_diagrama_titulo' => 'Una nueva forma de conectar la IA a sus sistemas',
				'solucao_int_eyebrow'     => 'integraciones',
				'solucao_int_titulo'      => 'Integre todos sus sistemas con ChatGPT',
				'solucao_int_subtitulo'   => 'Miles de integraciones listas para usar',
				'solucao_acel_eyebrow'    => 'MCP server',
				'solucao_acel_titulo'     => 'Dé herramientas a ChatGPT sin necesidad de desarrollar APIs',
				'solucao_acel_corpo'      => 'Transforme procesos corporativos en Tools para ChatGPT, definiendo exactamente qué información puede consultar y qué acciones puede ejecutar.',
				'solucao_acel_topico_1'   => 'Convierta procesos en herramientas de IA',
				'solucao_acel_topico_2'   => 'Controle entradas, salidas e información',
				'solucao_acel_topico_3'   => 'Póngalo todo a disposición por el MCP Server',
			)
		);
	}

	/**
	 * Gemini.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_gemini() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'integre su gemini',
				'solucao_hero_titulo'      => 'Conecte Gemini a sus sistemas y datos corporativos',
				'solucao_hero_corpo'       => 'Gemini accede a los datos, orquesta sistemas y ejecuta acciones con precisión, todo integrado a la operación de la empresa.',
				'solucao_pilares_titulo'   => 'Convierta Gemini en parte de la operación',
				'solucao_pilares_1_titulo' => 'Conecte Gemini a sus datos',
				'solucao_pilares_1_desc'   => 'Lleve información de los sistemas corporativos al modelo y genere respuestas basadas en el contexto real de la operación.',
				'solucao_pilares_2_titulo' => 'Orqueste múltiples aplicaciones',
				'solucao_pilares_2_desc'   => 'Combine Gemini con ERP, CRM, bases de datos y otras aplicaciones en flujos automatizados.',
				'solucao_pilares_3_titulo' => 'Consulte sistemas en lenguaje natural',
				'solucao_pilares_3_desc'   => 'Permita que los equipos encuentren información de clientes, pedidos y operaciones sin navegar por diferentes sistemas.',
				'solucao_casos_titulo'     => 'Aplique Gemini a los procesos del negocio',
				'solucao_casos_1_titulo'   => 'Consulte datos del ERP con IA',
				'solucao_casos_2_titulo'   => 'Analice documentos automáticamente',
				'solucao_casos_3_titulo'   => 'Automatice la atención al cliente',
				'solucao_casos_4_titulo'   => 'Clasifique solicitudes',
				'solucao_casos_5_titulo'   => 'Genere análisis operativos',
				'solucao_diagrama_titulo'  => 'Una nueva forma de conectar la IA a sus sistemas',
				'solucao_int_eyebrow'      => 'integraciones',
				'solucao_int_titulo'       => 'Integre todos sus sistemas con Gemini',
				'solucao_int_subtitulo'    => 'Miles de integraciones listas para usar',
				'solucao_dif_titulo'       => 'Integre IA con control sobre sus datos',
				'solucao_dif_corpo'        => 'Conecte Gemini a los sistemas de la empresa con control sobre los datos, los accesos y las acciones para escalar la inteligencia artificial sin perder gobernanza.',
				'solucao_dif_topico_1'     => 'Controle qué datos llegan a los modelos',
				'solucao_dif_topico_2'     => 'Proteja los datos en tránsito y en reposo',
				'solucao_dif_topico_3'     => 'Aplique reglas antes de ejecutar acciones',
				'solucao_plat_titulo'      => 'Centralice IA e integraciones en una plataforma',
				'solucao_plat_corpo'       => 'Evite crear conexiones aisladas para cada caso de uso. Centralice Gemini, sistemas y procesos para escalar nuevos agentes usando la misma arquitectura.',
				'solucao_plat_topico_1'    => 'Conecte Gemini a múltiples sistemas',
				'solucao_plat_topico_2'    => 'Reutilice conexiones en nuevos agentes',
				'solucao_plat_topico_3'    => 'Orqueste la IA dentro de los procesos',
				'solucao_acel_eyebrow'     => 'MCP server',
				'solucao_acel_titulo'      => 'Dé herramientas a Gemini sin necesidad de desarrollar APIs',
				'solucao_acel_corpo'       => 'Transforme procesos corporativos en Tools para Gemini, definiendo exactamente qué información puede consultar y qué acciones puede ejecutar.',
				'solucao_acel_topico_1'    => 'Convierta procesos en herramientas de IA',
				'solucao_acel_topico_2'    => 'Controle entradas, salidas e información',
				'solucao_acel_topico_3'    => 'Póngalo todo a disposición por el MCP Server',
			)
		);
	}

	/**
	 * Snowflake.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_snowflake() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Snowflake',
				'solucao_hero_titulo'      => 'Conecte Snowflake al core del negocio con datos siempre listos para el análisis',
				'solucao_hero_corpo'       => 'Integre Snowflake a sus sistemas transaccionales, CRM y ERP para alimentar pipelines analíticos en tiempo real y eliminar los silos de datos que frenan las decisiones estratégicas.',
				'solucao_pilares_titulo'   => 'Datos unificados, decisiones más rápidas',
				'solucao_pilares_1_titulo' => 'Ingesta continua de datos',
				'solucao_pilares_1_desc'   => 'Alimente Snowflake con datos de ERP, CRM y sistemas heredados de forma automatizada y confiable.',
				'solucao_pilares_2_titulo' => 'Transformaciones sin código extra',
				'solucao_pilares_2_desc'   => 'Procese, normalice y enriquezca los datos antes de cargarlos en Snowflake usando los flujos visuales de Boomi.',
				'solucao_pilares_3_titulo' => 'Gobernanza centralizada',
				'solucao_pilares_3_desc'   => 'Controle qué datos llegan a Snowflake, con trazabilidad de origen y conformidad con la LGPD y el GDPR.',
				'solucao_casos_titulo'     => 'Transforme los datos en ventaja competitiva',
				'solucao_casos_1_titulo'   => 'Sincronice el ERP con el Data Cloud',
				'solucao_casos_1_desc'     => 'Transfiera transacciones financieras y operativas del ERP a Snowflake en tiempo real para análisis actualizados.',
				'solucao_casos_2_titulo'   => 'Unifique los datos del CRM',
				'solucao_casos_2_desc'     => 'Consolide leads, oportunidades e historial de clientes en Snowflake para tener visiones 360° del pipeline comercial.',
				'solucao_casos_3_titulo'   => 'Automatice pipelines de marketing',
				'solucao_casos_3_desc'     => 'Alimente modelos de atribución y segmentación con datos de campañas centralizados en Snowflake.',
				'solucao_casos_4_titulo'   => 'Integre datos de e-commerce',
				'solucao_casos_4_desc'     => 'Envíe pedidos, devoluciones y comportamiento de navegación a Snowflake y alimente dashboards de ventas en tiempo real.',
				'solucao_casos_5_titulo'   => 'Alimente agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga datos estructurados de Snowflake a disposición de modelos de machine learning y agentes de IA que automatizan decisiones operativas.',
				'solucao_dif_titulo'       => 'Integración nativa con Snowflake Data Cloud',
				'solucao_dif_corpo'        => 'Conecte Snowflake usando el conector certificado de Boomi con soporte para autenticación OAuth 2.0 y key-pair, garantizando la máxima seguridad en el transporte de los datos.',
				'solucao_dif_topico_1'     => 'Conector certificado Boomi para Snowflake',
				'solucao_dif_topico_2'     => 'Autenticación OAuth 2.0 y key-pair',
				'solucao_dif_topico_3'     => 'Soporte para bulk load y streaming',
				'solucao_plat_titulo'      => 'Un hub central para todos sus datos',
				'solucao_plat_corpo'       => 'Con Boomi como capa de integración, usted conecta cualquier sistema a Snowflake sin scripts ETL personalizados, acelerando la entrega de insights y reduciendo la deuda técnica de los pipelines fragmentados.',
				'solucao_plat_topico_1'    => 'Elimine pipelines ETL fragmentados',
				'solucao_plat_topico_2'    => 'Conecte cualquier sistema a Snowflake',
				'solucao_plat_topico_3'    => 'Acelere el time-to-insight del equipo de datos',
				'solucao_acel_titulo'      => 'Empiece a ingerir datos en Snowflake hoy',
				'solucao_acel_corpo'       => 'Use modelos listos para conectar ERP, CRM y sistemas operativos a Snowflake con flujos estructurados y trazabilidad de punta a punta.',
				'solucao_acel_topico_1'    => 'Conecte ERP y CRM rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice pipelines de datos',
				'solucao_acel_topico_3'    => 'Acelere proyectos de Data Cloud',
			)
		);
	}

	/**
	 * Databricks.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_databricks() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Databricks',
				'solucao_hero_titulo'      => 'Conecte Databricks al core del negocio con datos siempre listos para la IA',
				'solucao_hero_corpo'       => 'Integre Databricks a sus sistemas transaccionales, ERP y CRM para alimentar modelos de machine learning en tiempo real y transformar los datos corporativos en decisiones inteligentes.',
				'solucao_pilares_titulo'   => 'Prepare los datos para la inteligencia avanzada',
				'solucao_pilares_1_titulo' => 'Ingiera datos de forma continua',
				'solucao_pilares_1_desc'   => 'Conecte los sistemas operativos a Databricks en tiempo real.',
				'solucao_pilares_2_titulo' => 'Alimente modelos de IA',
				'solucao_pilares_2_desc'   => 'Ponga datos actualizados a disposición del machine learning y de los agentes inteligentes.',
				'solucao_pilares_3_titulo' => 'Convierta previsiones en acciones',
				'solucao_pilares_3_desc'   => 'Devuelva los resultados analíticos al ERP y al CRM automáticamente.',
				'solucao_casos_titulo'     => 'Aplique inteligencia con datos conectados',
				'solucao_casos_1_titulo'   => 'Entrene modelos predictivos',
				'solucao_casos_1_desc'     => 'Use datos de ERP y CRM para prever churn, demanda y riesgos.',
				'solucao_casos_2_titulo'   => 'Dé contexto a los agentes de IA',
				'solucao_casos_2_desc'     => 'Alimente agentes inteligentes con información corporativa actualizada.',
				'solucao_casos_3_titulo'   => 'Envíe scores a los sistemas',
				'solucao_casos_3_desc'     => 'Devuelva los resultados de los modelos para apoyar decisiones operativas.',
				'solucao_casos_4_titulo'   => 'Consolide datos analíticos',
				'solucao_casos_4_desc'     => 'Una múltiples fuentes para análisis corporativos avanzados.',
				'solucao_casos_5_titulo'   => 'Conecte agentes de IA',
				'solucao_casos_5_desc'     => 'Ponga los datos corporativos a disposición de agentes de IA sin exponer el core de los sistemas.',
				'solucao_dif_titulo'       => 'Datos preparados para la IA con seguridad',
				'solucao_dif_corpo'        => 'Conecte Databricks vía APIs y Delta Sharing manteniendo autenticación segura, gobernanza y protección de los datos sensibles que utilizan los modelos.',
				'solucao_dif_topico_1'     => 'Utilice las APIs oficiales de Databricks',
				'solucao_dif_topico_2'     => 'Proteja los datos sensibles',
				'solucao_dif_topico_3'     => 'Controle los accesos por token',
				'solucao_plat_titulo'      => 'Conecte datos y decisiones en una plataforma',
				'solucao_plat_corpo'       => 'Centralice la conexión entre los sistemas operativos, Databricks y las aplicaciones de negocio para cerrar el ciclo entre datos y acciones.',
				'solucao_plat_topico_1'    => 'Integre datos corporativos',
				'solucao_plat_topico_2'    => 'Reutilice pipelines existentes',
				'solucao_plat_topico_3'    => 'Aplique IA en los procesos',
				'solucao_acel_titulo'      => 'Empiece con flujos de IA estructurados',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para llevar datos a Databricks, generar resultados analíticos y devolver acciones a los sistemas corporativos.',
				'solucao_acel_topico_1'    => 'Conecte datos rápidamente',
				'solucao_acel_topico_2'    => 'Acelere el entrenamiento de modelos',
				'solucao_acel_topico_3'    => 'Automatice acciones inteligentes',
			)
		);
	}

	/**
	 * AWS.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_aws() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su AWS',
				'solucao_hero_titulo'      => 'Acelere la adopción de AWS sin reescribir las integraciones existentes',
				'solucao_hero_corpo'       => 'Conecte servicios AWS, ERP, CRM y sistemas heredados en una misma plataforma para evolucionar su arquitectura cloud sin interrumpir las operaciones actuales.',
				'solucao_pilares_titulo'   => 'Evolucione su arquitectura cloud con seguridad',
				'solucao_pilares_1_titulo' => 'Conecte servicios AWS de forma nativa',
				'solucao_pilares_1_desc'   => 'Use conectores listos para integrar servicios AWS sin desarrollo específico.',
				'solucao_pilares_2_titulo' => 'Adopte eventos a escala',
				'solucao_pilares_2_desc'   => 'Implemente arquitecturas orientadas a eventos sin reconstruir las integraciones existentes.',
				'solucao_pilares_3_titulo' => 'Migre de forma incremental',
				'solucao_pilares_3_desc'   => 'Conecte sistemas heredados y workloads AWS durante su evolución cloud.',
				'solucao_casos_titulo'     => 'Automatice procesos conectados a AWS',
				'solucao_casos_1_titulo'   => 'Dispare flujos por eventos',
				'solucao_casos_1_desc'     => 'Active pipelines AWS a partir de eventos de ERP y CRM.',
				'solucao_casos_2_titulo'   => 'Orqueste funciones Lambda',
				'solucao_casos_2_desc'     => 'Incluya funciones serverless en flujos completos de integración.',
				'solucao_casos_3_titulo'   => 'Desacople sistemas con colas',
				'solucao_casos_3_desc'     => 'Use SNS y SQS para conectar aplicaciones con más flexibilidad.',
				'solucao_casos_4_titulo'   => 'Monitoree operaciones cloud',
				'solucao_casos_4_desc'     => 'Acompañe pipelines AWS y heredados en una vista centralizada.',
				'solucao_casos_5_titulo'   => 'Migre workloads gradualmente',
				'solucao_casos_5_desc'     => 'Evolucione hacia ECS sin interrumpir las integraciones existentes.',
				'solucao_casos_cta_texto'  => 'Hable con un especialista',
				'solucao_dif_titulo'       => 'Integraciones AWS con seguridad corporativa',
				'solucao_dif_corpo'        => 'Conecte servicios AWS utilizando autenticación IAM/STS, gestión de claves con KMS y cifrado para proteger los datos durante toda la operación.',
				'solucao_dif_topico_1'     => 'Autentique conexiones vía IAM',
				'solucao_dif_topico_2'     => 'Proteja los datos con KMS',
				'solucao_dif_topico_3'     => 'Cifre los datos en tránsito',
				'solucao_plat_titulo'      => 'Conecte legado y cloud en un solo lugar',
				'solucao_plat_corpo'       => 'Centralice la comunicación entre los sistemas existentes y los nuevos servicios AWS para acelerar la transformación sin crear integraciones desechables.',
				'solucao_plat_topico_1'    => 'Integre sistemas heredados',
				'solucao_plat_topico_2'    => 'Conecte servicios cloud-native',
				'solucao_plat_topico_3'    => 'Evolucione sin interrupciones',
				'solucao_acel_titulo'      => 'Empiece con eventos AWS estructurados',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para conectar eventos de negocio a EventBridge, Lambda y SNS acelerando su arquitectura orientada a eventos.',
				'solucao_acel_topico_1'    => 'Configure eventos rápidamente',
				'solucao_acel_topico_2'    => 'Reutilice flujos existentes',
				'solucao_acel_topico_3'    => 'Acelere la adopción cloud',
			)
		);
	}

	/**
	 * Microsoft Azure.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_microsoft_azure() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Azure',
				'solucao_hero_titulo'      => 'Acelere la adopción de Azure manteniendo su core conectado',
				'solucao_hero_corpo'       => 'Integre servicios Azure, SAP, Salesforce y sistemas heredados en una única plataforma para evolucionar su arquitectura cloud sin interrumpir las operaciones existentes.',
				'solucao_pilares_titulo'   => 'Evolucione su arquitectura Microsoft con escala',
				'solucao_pilares_1_titulo' => 'Conecte servicios Azure de forma nativa',
				'solucao_pilares_1_desc'   => 'Utilice conectores listos para datos y mensajería Azure.',
				'solucao_pilares_2_titulo' => 'Acelere eventos en tiempo real',
				'solucao_pilares_2_desc'   => 'Adopte arquitecturas orientadas a eventos sin reconstruir integraciones.',
				'solucao_pilares_3_titulo' => 'Integre el ecosistema Microsoft',
				'solucao_pilares_3_desc'   => 'Conecte Azure, Dynamics 365, Teams y Azure AD.',
				'solucao_casos_titulo'     => 'Automatice procesos conectados a Azure',
				'solucao_casos_1_titulo'   => 'Capture eventos en tiempo real',
				'solucao_casos_1_desc'     => 'Envíe eventos de negocio a analytics usando Event Hubs.',
				'solucao_casos_2_titulo'   => 'Desacople sistemas con colas',
				'solucao_casos_2_desc'     => 'Use Service Bus para conectar sistemas heredados y nuevos servicios.',
				'solucao_casos_3_titulo'   => 'Almacene datos con baja latencia',
				'solucao_casos_3_desc'     => 'Utilice CosmosDB para escenarios globales de alto rendimiento.',
				'solucao_casos_4_titulo'   => 'Automatice archivos corporativos',
				'solucao_casos_4_desc'     => 'Procese documentos usando Blob Storage y DataLake.',
				'solucao_casos_5_titulo'   => 'Centralice la gestión de secretos',
				'solucao_casos_5_desc'     => 'Proteja las credenciales de integración con Azure Key Vault.',
				'solucao_casos_cta_texto'  => 'Hable con un especialista',
				'solucao_dif_titulo'       => 'Integraciones Azure con seguridad nativa',
				'solucao_dif_corpo'        => 'Conecte servicios Azure usando OAuth2, Azure AD y Key Vault para controlar los accesos y proteger las credenciales en todos los flujos.',
				'solucao_dif_topico_1'     => 'Autentique vía Azure AD',
				'solucao_dif_topico_2'     => 'Proteja los secretos con Key Vault',
				'solucao_dif_topico_3'     => 'Controle los accesos de forma centralizada',
				'solucao_plat_titulo'      => 'Conecte todo el ecosistema Microsoft',
				'solucao_plat_corpo'       => 'Centralice las integraciones entre Azure, las aplicaciones Microsoft y los sistemas corporativos para acelerar nuevas iniciativas sin complejidad adicional.',
				'solucao_plat_topico_1'    => 'Integre datos y aplicaciones',
				'solucao_plat_topico_2'    => 'Reutilice pipelines existentes',
				'solucao_plat_topico_3'    => 'Evolucione la arquitectura gradualmente',
				'solucao_acel_titulo'      => 'Empiece con eventos Azure estructurados',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para conectar eventos de negocio a Event Hubs y Service Bus acelerando su arquitectura orientada a eventos.',
				'solucao_acel_topico_1'    => 'Configure eventos rápidamente',
				'solucao_acel_topico_2'    => 'Reduzca el desarrollo personalizado',
				'solucao_acel_topico_3'    => 'Acelere la adopción cloud',
			)
		);
	}

	/**
	 * Google Cloud.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_google_cloud() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Google Cloud',
				'solucao_hero_titulo'      => 'Acelere la adopción de Google Cloud conectando datos e IA',
				'solucao_hero_corpo'       => 'Integre ERP, CRM y sistemas operativos a BigQuery y Vertex AI para acelerar las iniciativas de datos e inteligencia artificial sin desconectar su legado.',
				'solucao_pilares_titulo'   => 'Transforme los datos en inteligencia en GCP',
				'solucao_pilares_1_titulo' => 'Conecte BigQuery y Vertex AI',
				'solucao_pilares_1_desc'   => 'Lleve los datos corporativos a analytics y a los agentes de IA.',
				'solucao_pilares_2_titulo' => 'Adopte eventos a escala',
				'solucao_pilares_2_desc'   => 'Use Pub/Sub para conectar sistemas en tiempo real.',
				'solucao_pilares_3_titulo' => 'Integre sin sustituir los legados',
				'solucao_pilares_3_desc'   => 'Conecte los entornos existentes durante su evolución cloud.',
				'solucao_casos_titulo'     => 'Automatice flujos de datos en GCP',
				'solucao_casos_1_titulo'   => 'Alimente BigQuery',
				'solucao_casos_1_desc'     => 'Envíe datos de ERP y CRM para análisis actualizados.',
				'solucao_casos_2_titulo'   => 'Desacople sistemas con Pub/Sub',
				'solucao_casos_2_desc'     => 'Distribuya eventos entre aplicaciones sin dependencias directas.',
				'solucao_casos_3_titulo'   => 'Prepare los datos para la IA',
				'solucao_casos_3_desc'     => 'Actualice los modelos de Vertex AI con contexto corporativo.',
				'solucao_casos_4_titulo'   => 'Procese archivos en la nube',
				'solucao_casos_4_desc'     => 'Almacene y procese documentos usando Cloud Storage.',
				'solucao_casos_5_titulo'   => 'Ejecute reverse ETL',
				'solucao_casos_5_desc'     => 'Envíe los resultados analíticos a los sistemas operativos.',
				'solucao_casos_cta_texto'  => 'Hable con un especialista',
				'solucao_dif_titulo'       => 'Integraciones GCP con seguridad corporativa',
				'solucao_dif_corpo'        => 'Conecte los servicios de Google Cloud usando IAM, Service Accounts y Cloud KMS para proteger accesos, claves y datos durante toda la operación.',
				'solucao_dif_topico_1'     => 'Autentique vía Service Accounts',
				'solucao_dif_topico_2'     => 'Proteja las claves con Cloud KMS',
				'solucao_dif_topico_3'     => 'Controle los accesos vía IAM',
				'solucao_plat_titulo'      => 'Conecte datos, IA y operación',
				'solucao_plat_corpo'       => 'Centralice la integración entre los sistemas corporativos y los servicios de Google Cloud para acelerar las iniciativas de datos sin crear pipelines aislados.',
				'solucao_plat_topico_1'    => 'Integre sistemas corporativos',
				'solucao_plat_topico_2'    => 'Reutilice flujos existentes',
				'solucao_plat_topico_3'    => 'Acelere iniciativas de IA',
				'solucao_acel_titulo'      => 'Empiece con datos listos para la IA',
				'solucao_acel_corpo'       => 'Utilice un modelo estructurado para conectar ERP y CRM a BigQuery y Vertex AI con datos siempre actualizados.',
				'solucao_acel_topico_1'    => 'Conecte fuentes rápidamente',
				'solucao_acel_topico_2'    => 'Reduzca los proyectos personalizados',
				'solucao_acel_topico_3'    => 'Acelere la adopción cloud',
			)
		);
	}

	/**
	 * Azure.
	 *
	 * @return array<string,string>
	 */
	protected function texto_es_solucao_azure() {
		return $this->solucao_es(
			array(
				'solucao_hero_eyebrow'     => 'para su Azure',
				'solucao_hero_titulo'      => 'Acelere la adopción de Azure manteniendo su core conectado',
				'solucao_hero_corpo'       => 'Integre servicios Azure, SAP, Salesforce y sistemas heredados en una única plataforma para evolucionar su arquitectura cloud sin interrumpir las operaciones existentes.',
				'solucao_pilares_titulo'   => 'Evolucione su arquitectura Microsoft con escala',
				'solucao_pilares_1_titulo' => 'Conecte servicios Azure de forma nativa',
				'solucao_pilares_1_desc'   => 'Utilice conectores listos para datos y mensajería Azure.',
				'solucao_pilares_2_titulo' => 'Acelere eventos en tiempo real',
				'solucao_pilares_2_desc'   => 'Adopte arquitecturas orientadas a eventos sin reconstruir integraciones.',
				'solucao_pilares_3_titulo' => 'Integre el ecosistema Microsoft',
				'solucao_pilares_3_desc'   => 'Conecte Azure, Dynamics 365, Teams y Azure AD.',
				'solucao_casos_titulo'     => 'Automatice procesos conectados a Azure',
				'solucao_casos_1_titulo'   => 'Capture eventos en tiempo real',
				'solucao_casos_1_desc'     => 'Envíe eventos de negocio a analytics usando Event Hubs.',
				'solucao_casos_2_titulo'   => 'Desacople sistemas con colas',
				'solucao_casos_2_desc'     => 'Use Service Bus para conectar sistemas heredados y nuevos servicios.',
				'solucao_casos_3_titulo'   => 'Almacene datos con baja latencia',
				'solucao_casos_3_desc'     => 'Utilice CosmosDB para escenarios globales de alto rendimiento.',
				'solucao_casos_4_titulo'   => 'Automatice archivos corporativos',
				'solucao_casos_4_desc'     => 'Procese documentos usando Blob Storage y DataLake.',
				'solucao_casos_5_titulo'   => 'Centralice la gestión de secretos',
				'solucao_casos_5_desc'     => 'Proteja las credenciales de integración con Azure Key Vault.',
				'solucao_casos_cta_texto'  => 'Hable con un especialista',
				'solucao_dif_titulo'       => 'Integraciones Azure con seguridad nativa',
				'solucao_dif_corpo'        => 'Conecte servicios Azure usando OAuth2, Azure AD y Key Vault para controlar los accesos y proteger las credenciales en todos los flujos.',
				'solucao_dif_topico_1'     => 'Autentique vía Azure AD',
				'solucao_dif_topico_2'     => 'Proteja los secretos con Key Vault',
				'solucao_dif_topico_3'     => 'Controle los accesos de forma centralizada',
				'solucao_plat_titulo'      => 'Conecte todo el ecosistema Microsoft',
				'solucao_plat_corpo'       => 'Centralice las integraciones entre Azure, las aplicaciones Microsoft y los sistemas corporativos para acelerar nuevas iniciativas sin complejidad adicional.',
				'solucao_plat_topico_1'    => 'Integre datos y aplicaciones',
				'solucao_plat_topico_2'    => 'Reutilice pipelines existentes',
				'solucao_plat_topico_3'    => 'Evolucione la arquitectura gradualmente',
				'solucao_acel_titulo'      => 'Empiece con eventos Azure estructurados',
				'solucao_acel_corpo'       => 'Utilice un modelo listo para conectar eventos de negocio a Event Hubs y Service Bus acelerando su arquitectura orientada a eventos.',
				'solucao_acel_topico_1'    => 'Configure eventos rápidamente',
				'solucao_acel_topico_2'    => 'Reduzca el desarrollo personalizado',
				'solucao_acel_topico_3'    => 'Acelere la adopción cloud',
			)
		);
	}
}
