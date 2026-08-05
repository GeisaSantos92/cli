<?php
/**
 * Página inicial do CLI Connect.
 *
 * Só orquestra: cada seção do Figma vira um template-part em
 * template-parts/home/. Todo o conteúdo vem do ACF da própria página inicial
 * ou dos CPTs de catálogo (inc/cpt.php) — nada é fixo aqui.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-home">
	<?php
	$cliconnect_secoes = array(
		'hero',          // Integrações ilimitadas.
		'agentes',       // Esteira de agentes de IA.
		'camadas',       // Tudo o que você precisa.
		'boomi',         // Tecnologia de classe mundial.
		'metricas',      // +200 / 5 dias / 30 mil.
		'clientes',      // Esteira de logos.
		'midia',         // Bloco 1 — IA corporativa.
		'midia',         // Bloco 2 — Na prática.
		'depoimento',    // Case em destaque + métricas.
		'eventos',       // Sua operação responde em tempo real.
		'compliance',    // Selos e certificações.
		'integracoes',   // Sua integração pode já estar pronta.
		'departamentos', // Integre todos os departamentos.
		'frase',         // Seus sistemas falam entre si.
		'suporte',       // Você não fica sozinho nunca.
		'blog',          // Últimas do blog.
		'faq',           // Dúvidas frequentes.
	);

	$cliconnect_bloco_midia = 0;

	foreach ( $cliconnect_secoes as $cliconnect_secao ) {
		$cliconnect_args = array();

		if ( 'midia' === $cliconnect_secao ) {
			++$cliconnect_bloco_midia;
			$cliconnect_args = array(
				'indice'    => $cliconnect_bloco_midia,
				'invertida' => ( 0 === $cliconnect_bloco_midia % 2 ),
			);
		}

		get_template_part( 'template-parts/home/' . $cliconnect_secao, null, $cliconnect_args );
	}
	?>
</main>

<?php
get_footer();
