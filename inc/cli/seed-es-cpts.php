<?php
/**
 * Seed — texto em espanhol dos CPTs de conteúdo.
 *
 * Agentes, eventos, cases e as FAQ gerais da home. Catálogos de logo
 * (`cli_cliente`, `cli_integracao`, `cli_selo`) ficam de fora de propósito:
 * logo não tem idioma.
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
 * Conteúdo em espanhol dos CPTs.
 */
trait Cliconnect_Seed_Es_Cpts {

	/**
	 * Agentes de IA: slug do seed => [título, descrição].
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function agentes_es() {
		return array(
			'copiloto-de-vendas-b2b'                            => array(
				'Copiloto de Ventas B2B',
				'Ayuda a los representantes recomendando productos, verificando inventario y aplicando las reglas de precio de las grandes cuentas.',
			),
			'conciliacao-fiscal-automatizada'                   => array(
				'Conciliación Fiscal Automatizada',
				'Captura las facturas de entrada, valida los impuestos retenidos y señala diferencias antes del cierre.',
			),
			'assistente-de-pos-venda-e-logistica'               => array(
				'Asistente de Posventa y Logística',
				'Rastrea entregas complejas, prevé retrasos y avisa proactivamente al cliente final sobre el estado del pedido.',
			),
			'analista-de-credito-e-compliance'                  => array(
				'Analista de Crédito y Compliance',
				'Evalúa el riesgo de nuevos clientes cruzando datos internos con burós de crédito para liberar pedidos.',
			),
			'triagem-de-suporte-nivel-1'                        => array(
				'Triaje de Soporte Nivel 1',
				'Seguimiento automatizado de pedidos, cotización dinámica de flete y comunicación de estado al cliente final.',
			),
			'automacao-da-sincronizacao-de-pedidos'             => array(
				'Automatización de la sincronización de pedidos',
				'Integra las ventas al instante entre el área comercial y la facturación para eliminar errores manuales.',
			),
			'automacao-do-agendamento-de-consulta'              => array(
				'Automatización de la programación de citas',
				'Agenda citas con pacientes por WhatsApp y actualiza las agendas médicas en tiempo real.',
			),
			'sincronizacao-automatica-de-estoque'               => array(
				'Sincronización automática de inventario',
				'Sincroniza el saldo físico del almacén central con las tiendas en línea para evitar ventas sin producto.',
			),
			'simulacao-dos-novos-impostos-da-reforma-tributaria' => array(
				'Simulación de la Reforma Tributaria',
				'Analiza el historial de facturación y simula el impacto fiscal de la transición al nuevo modelo de impuestos.',
			),
		);
	}

	/**
	 * Eventos automáticos: slug do seed => [título, descrição].
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function eventos_es() {
		return array(
			'informacoes-sempre-sincronizadas'           => array(
				'Información siempre sincronizada',
				'Evite diferencias entre sistemas y garantice que todas las áreas trabajen con los mismos datos.',
			),
			'respostas-mais-rapidas-ao-negocio'          => array(
				'Respuestas más rápidas para el negocio',
				'Las actualizaciones ocurren automáticamente, sin colas, conciliaciones ni intervención de TI.',
			),
			'mais-visibilidade-sobre-processos-criticos' => array(
				'Más visibilidad sobre los procesos críticos',
				'Monitoree procesos críticos en tiempo real e identifique de inmediato las situaciones que exigen atención.',
			),
			'se-adapte-a-mudancas-regulatorias'          => array(
				'Adáptese a los cambios regulatorios',
				'Convierta los cambios normativos en acciones automáticas, reduciendo riesgos y acelerando la adaptación de la empresa.',
			),
		);
	}

	/**
	 * Cases: slug do seed => título, resumo, conteúdo e campos.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	protected function cases_es() {
		$resumo_padrao = 'La implementación de CLI Connect permitió integrar sistemas, automatizar eventos y aumentar la visibilidad sobre toda la operación.';

		return array(
			'panasonic'            => array(
				'titulo'   => 'Redujo en un 97% el tiempo de respuesta de las integraciones con SAP S/4HANA',
				'resumo'   => 'Con CLI como socio de integración, Panasonic conectó el nuevo SAP S/4HANA a sus sistemas satélite, llevó las respuestas de los segundos a los milisegundos y liberó el 40% del tiempo del equipo de TI para innovar.',
				'conteudo' => '<p>Con CLI como socio de integración, Panasonic conectó el nuevo SAP S/4HANA a sus sistemas satélite, llevó las respuestas de los segundos a los milisegundos y liberó el 40% del tiempo del equipo de TI para innovar.</p>',
				'campos'   => array(
					'citacao'          => 'Antes hablaba de segundos de respuesta. Hoy hablo de milisegundos.',
					'cargo'            => 'Head of Digital and Technology en Panasonic',
					'metrica_numero'   => '-97%',
					'metrica_texto'    => 'en el tiempo de respuesta de las integraciones entre SAP S/4HANA y los sistemas satélite',
					'metrica_numero_2' => '40%',
					'metrica_texto_2'  => 'del tiempo del equipo de TI liberado para nuevas tecnologías',
					'desafio_titulo'   => 'Un nuevo SAP tenía que comunicarse con todo el ecosistema',
					'desafio_texto'    => '<p>En 2023, Panasonic do Brasil decidió acelerar su transformación digital. El punto de partida fue SAP for Pana, el programa que llevó SAP S/4HANA a la compañía en modalidad greenfield, construido desde cero y acompañado de una revisión completa de procesos. Con foco en el comercio minorista de línea blanca y línea marrón y plantas en Manaus (AM), Extrema (MG) y São José dos Campos (SP), la empresa depende de muchos sistemas satélite que intercambian datos todo el tiempo con el ERP y entre las unidades.</p><p>Al reevaluar el entorno, el equipo identificó los sistemas que concentraban los cuellos de botella. El desafío era integrar el nuevo S/4HANA a ese ecosistema sin tocar los sistemas satélite, en una arquitectura fuera del estándar del mercado: sin integrador SAP y con una plataforma de integración, tecnología nueva para la empresa, como integrador principal. Para una compañía japonesa, con una cultura fuertemente orientada a procesos, la elección tenía que garantizar una ganancia real de rendimiento.</p>',
					'solucao_titulo'   => 'Quien entendió la necesidad fue quien implementó',
					'solucao_texto'    => '<p>CLI llegó a Panasonic por recomendación de Salesforce y participó en la evaluación de la plataforma de integración: presentó cases, mostró la ganancia que la tecnología podía aportar y cómo acelerar la implementación. A partir de esa conversación, el proyecto adoptó una arquitectura propia, con la plataforma de integración como integrador principal y toda la comunicación entre S/4HANA y los sistemas satélite realizada directamente por ella. El trabajo se centró en el interfaceamiento: cada integración se diseñó para la necesidad del proceso, sin exigir cambios en los sistemas existentes.</p><p>El diferencial, según Fabiano, fue la cercanía. Con la participación directa de Bruno, socio fundador de CLI Connect, quien mapeó la necesidad fue quien diseñó la solución y la implementó junto al equipo de Panasonic, sin traspaso entre el área comercial y el equipo de entrega. En casi un año de proyecto, los equipos ajustaron juntos el ritmo, reforzaron la atención en los procesos más complejos y, después de la implementación, volvieron al entorno para mapear e incorporar nuevas oportunidades. A lo largo del camino, CLI capacitó al equipo de Panasonic para mantener las interfaces por su cuenta.</p>',
					'impacto_titulo'   => 'Respuestas en milisegundos y una TI más estratégica',
					'impacto_texto'    => '<p>Seis meses después del go-live, con el entorno todavía en estabilización, el tiempo de respuesta entre S/4HANA y los sistemas satélite cayó un 97%, y la rutina de cada profesional ganó entre un 21% y un 25% en rendimiento. Los procesos que circulan entre Manaus, Extrema y São José dos Campos ganaron velocidad, y el equipo de TI pasó a tener un 40% de margen de tiempo, reinvertido en nuevas tecnologías en lugar de reducir la plantilla. Con un equipo de AMS especializado en la plataforma de integración, Panasonic mantiene sus propias interfaces, y hoy es el área de negocio la que busca a TI para hacer posibles nuevas soluciones.</p>',
				),
			),
			'moura'                => array(
				'titulo' => 'Aceleró la producción de insights en un 10%',
				'resumo' => $resumo_padrao,
			),
			'petroreconcavo'       => array(
				'titulo' => 'Aceleró la producción de insights en un 10%',
				'resumo' => $resumo_padrao,
				'campos' => array(
					'metrica_numero' => '10%',
					'metrica_texto'  => 'Menos tiempo dedicado al triaje',
				),
			),
			'moura-vendas'         => array(
				'titulo' => 'Evitó la pérdida del 15% de las ventas mensuales',
				'resumo' => 'La integración entre CRM, ERP y la plataforma de e-commerce eliminó cuellos de botella en el proceso de ventas y garantizó la continuidad operativa incluso en picos de demanda.',
				'campos' => array(
					'metrica_numero' => '15%',
					'metrica_texto'  => 'De ventas mensuales preservadas con integración en tiempo real',
				),
			),
			'petroreconcavo-dados' => array(
				'titulo' => 'Optimizó operaciones con datos unificados',
				'resumo' => 'La unificación de los datos operativos en una única arquitectura de integraciones redujo el retrabajo, eliminó inconsistencias y aceleró la toma de decisiones.',
				'campos' => array(
					'metrica_numero' => '+40%',
					'metrica_texto'  => 'De ganancia en velocidad de análisis operativo',
				),
			),
		);
	}

	/**
	 * FAQ gerais da home: slug do seed => [pergunta, resposta].
	 *
	 * @return array<string,array{0:string,1:string}>
	 */
	protected function faq_es() {
		return array(
			'o-que-exatamente-o-cli-connect-faz'                     => array(
				'¿Qué hace exactamente CLI Connect?',
				'<p>CLI Connect conecta los sistemas que su empresa ya usa —ERP, e-commerce, CRM, sistemas fiscales y logísticos— en una única estructura gobernada. Además de las integraciones, obtiene eventos automáticos que disparan acciones entre sistemas y una biblioteca con más de 30.000 automatizaciones listas para usar.</p>',
			),
			'quanto-tempo-demora-o-servico'                          => array(
				'¿Cuánto tarda el servicio?',
				'<p>La mayoría de las integraciones queda lista en hasta 5 días, porque partimos de conectores y recetas ya validados. Los proyectos con reglas de negocio muy específicas pasan por un relevamiento rápido antes de entrar en la fila de implantación.</p>',
			),
			'e-se-algo-parar-de-funcionar'                           => array(
				'¿Y si algo deja de funcionar?',
				'<p>El monitoreo es nuestro, no suyo. El equipo sigue las integraciones en tiempo real y, en la mayoría de los casos, ya está resolviendo el problema antes de que usted lo note. El soporte es humano y está disponible por el portal, el correo y WhatsApp.</p>',
			),
			'vou-depender-da-cli-para-tudo'                          => array(
				'¿Voy a depender de CLI para todo?',
				'<p>No. Toda la operación queda documentada y visible para su equipo en el panel, y las integraciones corren sobre la plataforma Boomi, un estándar global del mercado. Usted mantiene el gobierno y decide cuánto quiere delegar.</p>',
			),
			'como-funciona-o-modelo-de-contratacao'                  => array(
				'¿Cómo funciona el modelo de contratación?',
				'<p>Es una mensualidad fija, con integraciones ilimitadas y servicio gestionado incluido. No hay cobro por volumen de llamadas ni por nueva integración: cuanto más crece su operación, más se beneficia del modelo.</p>',
			),
			'posso-criar-minhas-proprias-integracoes-na-cli-connect' => array(
				'¿Puedo crear mis propias integraciones en CLI Connect?',
				'<p>Sí. Además de la biblioteca con más de 30.000 automatizaciones listas, la plataforma Boomi permite que su equipo cree conectores y flujos personalizados. CLI Connect apoya la estructuración y documentación de esas integraciones para que sigan las mejores prácticas de gobierno y rendimiento.</p>',
			),
		);
	}
}
