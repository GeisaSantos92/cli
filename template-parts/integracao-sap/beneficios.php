<?php
/**
 * Integração SAP — Benefícios da CLI Connect para o SAP.
 *
 * Grid 1fr:2fr — lista de tópicos à esquerda, painel de descrição à direita.
 * Troca de abas via JS progressivo.
 *
 * Campos ACF (group_cli_integracao_sap, aba "11 · Benefícios"):
 *   sap_ben_eyebrow      — eyebrow
 *   sap_ben_titulo       — título H2
 *   sap_ben_botao        — botão CTA
 *   sap_ben_{1-6}_rotulo — rótulo do tópico (ex.: "01 - Especialização SAP")
 *   sap_ben_{1-6}_desc   — descrição do painel
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'sap_ben_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'sap_ben_titulo' );
$botao   = cliconnect_campo_pagina( 'sap_ben_botao' );

$itens = array();
for ( $i = 1; $i <= 6; $i++ ) {
	$rotulo = cliconnect_campo_pagina( 'sap_ben_' . $i . '_rotulo' );
	$desc   = cliconnect_campo_pagina( 'sap_ben_' . $i . '_desc' );
	if ( $rotulo ) {
		$itens[] = array( 'rotulo' => $rotulo, 'desc' => $desc );
	}
}

if ( ! $titulo || ! $itens ) {
	return;
}

$uri    = get_template_directory_uri();
$elipse = $uri . '/assets/img/sap-ben-elipse.svg';
?>
<section class="sap-beneficios">
	<div class="container sap-beneficios__inner">

		<!-- Header -->
		<div class="sap-beneficios__header">
			<div class="sap-beneficios__header-esq">
				<?php if ( $eyebrow ) : ?>
					<p class="sap-beneficios__eyebrow eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>
				<h2 class="sap-beneficios__titulo"><?php echo esc_html( $titulo ); ?></h2>
			</div>
			<div class="sap-beneficios__header-dir">
				<?php cliconnect_botao( $botao ); ?>
			</div>
		</div>

		<!-- Grid: menu + painel -->
		<div class="sap-beneficios__grid" data-sap-tabs>

			<!-- Menu de tópicos -->
			<ul class="sap-beneficios__menu" role="tablist" aria-label="<?php echo esc_attr( $titulo ); ?>">
				<?php foreach ( $itens as $idx => $item ) : ?>
					<li role="presentation">
						<button
							class="sap-beneficios__topico<?php echo 0 === $idx ? ' sap-beneficios__topico--ativo' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- string literal hardcoded. ?>"
							role="tab"
							aria-selected="<?php echo 0 === $idx ? 'true' : 'false'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- string literal booleana. ?>"
							aria-controls="sap-ben-painel-<?php echo (int) $idx; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cast (int), seguro. ?>"
							id="sap-ben-tab-<?php echo (int) $idx; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cast (int), seguro. ?>"
							data-tab="<?php echo (int) $idx; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cast (int), seguro. ?>"
						>
							<?php echo esc_html( $item['rotulo'] ); ?>
						</button>
					</li>
				<?php endforeach; ?>
			</ul>

			<!-- Painéis de descrição -->
			<div class="sap-beneficios__paineis">
				<?php foreach ( $itens as $idx => $item ) : ?>
					<div
						class="sap-beneficios__painel<?php echo 0 === $idx ? ' sap-beneficios__painel--ativo' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- string literal hardcoded. ?>"
						role="tabpanel"
						id="sap-ben-painel-<?php echo (int) $idx; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cast (int), seguro. ?>"
						aria-labelledby="sap-ben-tab-<?php echo (int) $idx; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cast (int), seguro. ?>"
						<?php echo 0 !== $idx ? 'hidden' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- string literal hardcoded. ?>
					>
						<div class="sap-beneficios__check-wrap" aria-hidden="true">
							<?php echo cliconnect_icone( 'check', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_icone retorna SVG estático do tema. ?>
						</div>
						<p class="sap-beneficios__desc"><?php echo esc_html( $item['desc'] ); ?></p>
						<img class="sap-beneficios__elipse" src="<?php echo esc_url( $elipse ); ?>" alt="" aria-hidden="true" loading="lazy" decoding="async">
					</div>
				<?php endforeach; ?>
			</div>

		</div>

	</div>
</section>

<script>
(function () {
	var grid = document.querySelector('[data-sap-tabs]');
	if (!grid) return;
	var tabs = grid.querySelectorAll('[role="tab"]');
	var panels = grid.querySelectorAll('[role="tabpanel"]');
	function ativar(tab) {
		var idx = tab.dataset.tab;
		tabs.forEach(function (t) {
			t.classList.remove('sap-beneficios__topico--ativo');
			t.setAttribute('aria-selected', 'false');
		});
		panels.forEach(function (p) {
			p.classList.remove('sap-beneficios__painel--ativo');
			p.hidden = true;
		});
		tab.classList.add('sap-beneficios__topico--ativo');
		tab.setAttribute('aria-selected', 'true');
		var painel = document.getElementById('sap-ben-painel-' + idx);
		if (painel) { painel.classList.add('sap-beneficios__painel--ativo'); painel.hidden = false; }
	}
	tabs.forEach(function (tab) {
		tab.addEventListener('mouseenter', function () { ativar(this); });
		tab.addEventListener('focus', function () { ativar(this); });
	});
}());
</script>
