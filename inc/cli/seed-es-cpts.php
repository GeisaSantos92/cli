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
			'moura' => array(
				'titulo'   => 'Atraviesa el "Black Friday" de cada cierre de mes sin paradas en la facturación',
				'resumo'   => 'Con CLI Connect como socio desde la concepción del proyecto, el Grupo Moura conectó el nuevo CRM Salesforce a su ERP mediante un middleware centralizado, ganó trazabilidad en todas las integraciones y vio caer los tickets mes a mes.',
				'conteudo' => '<p>Con CLI Connect como socio desde la concepción del proyecto, el Grupo Moura conectó el nuevo CRM Salesforce a su ERP mediante un middleware centralizado, ganó trazabilidad en todas las integraciones y vio caer los tickets mes a mes.</p>',
				'campos'   => array(
					'citacao' => 'Bromeo diciendo que cada mes tenemos un Black Friday. Pasar por él con fluidez es lo que buscamos en soluciones y en socios que nos apoyan.',
					'cargo' => 'Gerente de Sistemas del Grupo Moura',
					'metrica_numero' => '0 paradas',
					'metrica_texto' => 'en la facturación durante el pico de volumen de fin de mes',
					'metrica_numero_2' => '~1 año',
					'metrica_texto_2' => 'de construcción conjunta, desde la concepción hasta el primer go-live',
					'desafio_titulo' => 'Integraciones frágiles en una operación que no puede parar',
					'desafio_texto' => '<p>Conocido como Baterias Moura, el Grupo Moura trabaja con todo tipo de acumulación de energía, no solo baterías automotrices. Desde hace unos siete años, la empresa atraviesa una reestructuración tecnológica en busca de sistemas confiables y robustos. La etapa más reciente fue la jornada del cliente: con la adquisición del CRM Salesforce, comenzando por la fuerza de ventas, había que conectar la nueva plataforma al ERP.</p><p>Hasta entonces, la comunicación entre sistemas se hacía de base a base, mediante intercambio de archivos, con procesos manuales y retrabajo. Cuando algo fallaba, encontrar la causa raíz era lento, y una información que no llegaba al otro lado dejaba datos inconsistentes, incluso en los números llevados a la dirección. El riesgo crecía a fin de mes, cuando la operación de distribución concentra un volumen altísimo, un "Black Friday" mensual en el que cualquier parada significa dejar de facturar.</p>',
					'solucao_titulo' => 'Un orquestador único, construido en conjunto desde el inicio',
					'solucao_texto' => '<p>Moura buscaba un socio consolidado, con buenos proyectos de implantación y homologado por Salesforce. Tras conversar con clientes de distintos proveedores, eligió CLI Connect. La solución fue un middleware como orquestador único entre Salesforce y el ERP: un solo canal, con visibilidad de por dónde pasa cada información y registros para rastrear cualquier falla. La plataforma, reconocida mundialmente, también fue homologada por el área de seguridad de la información del Grupo Moura.</p><p>CLI Connect participó en todas las etapas, desde la concepción hasta la arquitectura, el desarrollo y las buenas prácticas, en cerca de un año de construcción hasta el primer go-live, y hoy acompaña la operación. La atención va más allá de los tickets: con Bruno Martins, socio fundador de CLI Connect, y arquitectos siempre cerca, Moura consulta a CLI Connect antes de cada nueva integración para evaluar el volumen y la mejor arquitectura. Junto con el fabricante de la plataforma, los equipos ya diseñan la arquitectura para los próximos cinco años de crecimiento.</p>',
					'impacto_titulo' => 'Una operación en vuelo de crucero',
					'impacto_texto' => '<p>Desde el primer go-live, los tickets de service desk, naturales al principio con una herramienta nueva para la empresa y sus usuarios, bajaron de forma continua. Con trazabilidad de punta a punta, la TI pasó a anticipar problemas en lugar de reaccionar a ellos, y el ciclo de mejora continua ganó datos para funcionar. Logística, finanzas y fuerza de ventas reciben información rápida, completa y confiable, y el pico de fin de mes pasa sin paradas en la facturación. Todo esto con un equipo interno reducido, apoyado por los especialistas de CLI Connect, y un costo fijo que quita sorpresas al presupuesto.</p>',
				),
			),
			'petroreconcavo' => array(
				'titulo'   => 'Integró SAP a una operación 24/7 sin ninguna parada en casi dos años',
				'resumo'   => 'Con CLI Connect by Boomi, PetroRecôncavo centralizó las integraciones entre el nuevo SAP S/4HANA y sus sistemas satélite, eliminó la doble digitación entre sistemas y ahora cierra la producción en el 3.er día del mes.',
				'conteudo' => '<p>Con CLI Connect by Boomi, PetroRecôncavo centralizó las integraciones entre el nuevo SAP S/4HANA y sus sistemas satélite, eliminó la doble digitación entre sistemas y ahora cierra la producción en el 3.er día del mes.</p>',
				'campos'   => array(
					'citacao' => 'Con los sistemas integrados, en casi dos años de operación nunca nos detuvimos. La producción funciona 24/7, y las áreas siguen integradas, sin depender de TI.',
					'cargo' => 'Coordinadora de Sistemas en PetroRecôncavo',
					'metrica_numero' => '0 paradas',
					'metrica_texto' => 'en las integraciones durante casi dos años de operación 24/7',
					'metrica_numero_2' => '3 días',
					'metrica_texto_2' => 'para cerrar la producción, que antes llegaba al 8.º día',
					'desafio_titulo' => 'Una operación crítica que no puede parar',
					'desafio_texto' => '<p>Pionera entre los productores independientes de petróleo y gas en tierra, PetroRecôncavo opera 57 concesiones y más de 2 mil pozos en Bahía, Río Grande del Norte y Sergipe, con una producción de alrededor de 26 mil barriles por día. Es una operación 24/7 y geográficamente dispersa, en la que cualquier parada significa barriles que dejan de producirse. En 2022, la empresa inició el cambio de su ERP y, con el proyecto Evolution, implementó SAP S/4HANA en 2024.</p><p>Sin embargo, SAP no funciona solo. La mayoría de los sistemas satélite no estaba integrada, y el principal, IFS, usado en la gestión de activos, solo tenía integraciones simples, sin la robustez y el control necesarios. Las conexiones eran punto a punto, sin orquestación ni monitoreo, y muchos datos se digitaban en un sistema y se volvían a digitar en otro, con idas y vueltas de correos y hojas de cálculo. El cierre mensual tomaba de 10 a 11 días hábiles, y la infraestructura híbrida, con entornos on-premise y en la nube, exigía una tecnología capaz de integrar ambos.</p>',
					'solucao_titulo' => 'Un middleware central y un socio al lado',
					'solucao_texto' => '<p>CLI acompañó a PetroRecôncavo desde la elección de la tecnología. En la selección del proyecto Evolution, ayudó a comparar integradores hasta llegar a Boomi, la opción más adecuada en costo-beneficio, para la infraestructura híbrida y para los planes de mediano y largo plazo. Con CLI Connect by Boomi, la empresa ganó un middleware estándar y centralizado, con gobernanza, monitoreo y trazabilidad de las integraciones SAP y no SAP. Los conectores listos aceleraron la construcción, y funciones como Event Streams, para mensajería, y Data Hub, para datos maestros, amplían el uso de la plataforma.</p><p>El conocimiento del equipo de CLI Connect by Boomi y de los sistemas que la empresa ya usaba, como IFS, fue decisivo para mapear los procesos críticos y definir los criterios de calidad de las entregas. El costo fijo mensual dio previsibilidad al presupuesto, tanto de la operación como de las nuevas implementaciones. A lo largo del proyecto, los consultores transfirieron conocimiento al equipo interno, que también se capacitó con Boomi Academy, y CLI sigue como socio en el soporte, con atención rápida para incidentes y gestión de cambios.</p>',
					'impacto_titulo' => 'Cierres más rápidos y cero retrabajo',
					'impacto_texto' => '<p>En casi dos años de operación, las integraciones nunca se detuvieron, y la doble digitación entre sistemas terminó, eliminando el riesgo de datos inconsistentes. El cierre de producción, que llegaba al 8.º día, hoy termina prácticamente el 3.er día, y el cierre mensal bajó de 10 a 11 días hábiles al 5.º día hábil. Los reembolsos de viaje, la medición de producción, las regalías, la comercialización y las servidumbres pasaron a funcionar integrados, y las áreas absorbieron más volumen sin aumentar la plantilla. Con un proyecto de transporte en marcha y pruebas con agentes de IA de Boomi, PetroRecôncavo sigue ampliando su arquitectura de integraciones con CLI.</p>',
				),
			),
			'moura-vendas' => array(
				'titulo'   => 'Atraviesa el "Black Friday" de cada cierre de mes sin paradas en la facturación',
				'resumo'   => 'Con CLI Connect como socio desde la concepción del proyecto, el Grupo Moura conectó el nuevo CRM Salesforce a su ERP mediante un middleware centralizado, ganó trazabilidad en todas las integraciones y vio caer los tickets mes a mes.',
				'conteudo' => '<p>Con CLI Connect como socio desde la concepción del proyecto, el Grupo Moura conectó el nuevo CRM Salesforce a su ERP mediante un middleware centralizado, ganó trazabilidad en todas las integraciones y vio caer los tickets mes a mes.</p>',
				'campos'   => array(
					'metrica_numero' => '~1 año',
					'metrica_texto' => 'de construcción conjunta, desde la concepción hasta el primer go-live',
				),
			),
			'petroreconcavo-dados' => array(
				'titulo'   => 'Integró SAP a una operación 24/7 sin ninguna parada en casi dos años',
				'resumo'   => 'Con CLI Connect by Boomi, PetroRecôncavo centralizó las integraciones entre el nuevo SAP S/4HANA y sus sistemas satélite, eliminó la doble digitación entre sistemas y ahora cierra la producción en el 3.er día del mes.',
				'conteudo' => '<p>Con CLI Connect by Boomi, PetroRecôncavo centralizó las integraciones entre el nuevo SAP S/4HANA y sus sistemas satélite, eliminó la doble digitación entre sistemas y ahora cierra la producción en el 3.er día del mes.</p>',
				'campos'   => array(
					'metrica_numero' => '0 paradas',
					'metrica_texto' => 'en las integraciones durante casi dos años de operación 24/7',
				),
			),
			'andamento-brz'     => array(
				'titulo'  => 'BRZ Empreendimentos',
				'resumo'  => '',
				'campos'  => array(),
			),
			'andamento-legrand' => array(
				'titulo'  => 'Legrand',
				'resumo'  => '',
				'campos'  => array(),
			),
			'andamento-sbc'     => array(
				'titulo'  => 'Sociedad Brasileña de Cardiología',
				'resumo'  => '',
				'campos'  => array(),
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
