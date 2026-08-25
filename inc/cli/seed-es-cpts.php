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
				'titulo'   => 'Aceleró la producción de insights en un 10%',
				'resumo'   => $resumo_padrao,
				'conteudo' => '<p>Panasonic conectó ERP, e-commerce y sistemas fiscales en una única estructura de integraciones, con eventos automáticos para sincronizar información y actualizar procesos críticos.</p>',
				'campos'   => array(
					'citacao'          => 'Con CLI Connect reestructuramos nuestro gobierno y nuestros procesos financieros.',
					'cargo'            => 'Head de operaciones en Panasonic',
					'metrica_numero'   => '+85%',
					'metrica_texto'    => 'Menos tiempo para implementar nuevas integraciones',
					'metrica_numero_2' => '+60%',
					'metrica_texto_2'  => 'Menos intervenciones operativas manuales',
					'desafio_titulo'   => 'Sistemas desconectados dificultaban la operación',
					'desafio_texto'    => '<p>Con el crecimiento de la empresa, nuevas plataformas pasaron a formar parte de la operación, incluidos ERP, e-commerce, CRM y sistemas logísticos. Sin embargo, el intercambio de información entre esas aplicaciones dependía de integraciones puntuales y procesos poco estandarizados.</p><p>El equipo tenía que lidiar constantemente con datos inconsistentes, actualizaciones manuales y dificultades para seguir en tiempo real los eventos críticos del negocio. Cada nueva demanda exigía más desarrollo, lo que aumentaba la complejidad operativa y el tiempo de respuesta a las áreas de negocio.</p>',
					'solucao_titulo'   => 'Una operación conectada y preparada para evolucionar',
					'solucao_texto'    => '<p>CLI implementó una arquitectura centralizada de integraciones con CLI Connect, conectando los principales sistemas de la operación en una única estructura gobernada.</p><p>Además de las integraciones, se crearon eventos automáticos para sincronizar información, enviar notificaciones operativas y actualizar procesos críticos. La empresa también empezó a usar una biblioteca de automatizaciones listas, lo que aceleró la implementación de nuevas demandas y redujo la necesidad de proyectos aislados para cada integración.</p>',
					'impacto_titulo'   => 'Más agilidad, previsibilidad y control',
					'impacto_texto'    => '<p>Con la nueva arquitectura de integraciones, Panasonic redujo drásticamente el tiempo de implementación de nuevos conectores y eliminó gran parte de las intervenciones manuales del proceso operativo, ganando visibilidad en tiempo real sobre toda la cadena de datos.</p>',
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
