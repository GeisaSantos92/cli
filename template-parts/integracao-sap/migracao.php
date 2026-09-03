<?php
/**
 * Integração SAP — Migração ECC → S/4HANA.
 *
 * Fundo bicolor: #e7efff (50% topo) + #fff (50% base).
 * Card azul com título, texto e botão branco à esquerda;
 * ilustração ECC→S/4HANA à direita.
 *
 * Campos ACF (group_cli_integracao_sap, aba "10 · Migração"):
 *   sap_mig_titulo  — título H3
 *   sap_mig_texto   — texto de apoio
 *   sap_mig_botao   — botão CTA
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo = cliconnect_campo_pagina( 'sap_mig_titulo' );
$texto  = cliconnect_campo_pagina( 'sap_mig_texto' );
$botao  = cliconnect_campo_pagina( 'sap_mig_botao' );

if ( ! $titulo ) {
	return;
}

$uri      = get_template_directory_uri();
$diagonal = $uri . '/assets/img/sap-mig-diagonal.svg';
$sap_logo = $uri . '/assets/img/sap-mig-sap-logo.png';
$cloud    = $uri . '/assets/img/sap-mig-cloud.svg';
?>
<section class="sap-migracao">
	<div class="container">
		<div class="sap-migracao__card">

			<!-- Decorativo: shape diagonal escura à direita -->
			<img class="sap-migracao__diagonal" src="<?php echo esc_url( $diagonal ); ?>" alt="" aria-hidden="true" loading="lazy" decoding="async">

			<!-- Conteúdo esquerdo -->
			<div class="sap-migracao__conteudo">
				<h2 class="sap-migracao__titulo"><?php echo esc_html( $titulo ); ?></h2>

				<?php if ( $texto ) : ?>
					<p class="sap-migracao__texto"><?php echo esc_html( $texto ); ?></p>
				<?php endif; ?>

				<?php if ( $botao && ! empty( $botao['url'] ) ) : ?>
					<div class="sap-migracao__acoes">
						<a
							class="sap-migracao__botao"
							href="<?php echo esc_url( $botao['url'] ); ?>"
							<?php echo ( '_blank' === ( $botao['target'] ?? '' ) ) ? 'target="_blank" rel="noopener noreferrer"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- string literal hardcoded. ?>
						>
							<?php echo esc_html( $botao['title'] ); ?>
							<?php echo cliconnect_icone( 'seta-direita', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_icone retorna SVG estático do tema. ?>
						</a>
					</div>
				<?php endif; ?>
			</div>

			<!-- Ilustração ECC → S/4HANA -->
			<div class="sap-migracao__ilustracao" aria-hidden="true">
				<div class="sap-migracao__ecc-hana">

					<div class="sap-migracao__logo-card">
						<img src="<?php echo esc_url( $sap_logo ); ?>" alt="" class="sap-migracao__sap-img" loading="lazy" decoding="async">
						<span class="sap-migracao__logo-label">ECC</span>
					</div>

					<div class="sap-migracao__seta-wrap">
						<img src="<?php echo esc_url( $cloud ); ?>" alt="" class="sap-migracao__cloud" loading="lazy" decoding="async">
					</div>

					<div class="sap-migracao__logo-card">
						<img src="<?php echo esc_url( $sap_logo ); ?>" alt="" class="sap-migracao__sap-img" loading="lazy" decoding="async">
						<span class="sap-migracao__logo-label">S/4 HANA</span>
					</div>

				</div>
			</div>

		</div>
	</div>
</section>
