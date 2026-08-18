<?php
/**
 * Integração SAP — Eventos Automáticos.
 *
 * Painel de 5 colunas que mostra o fluxo trigger → ação automática.
 * Cada coluna tem 3 etapas (58 px) ligadas por linhas gradiente com "+" SVG.
 * Cores e ícones por coluna são fixos no design.
 *
 * Campos ACF (group_cli_integracao_sap, aba "12 · Automação"):
 *   sap_aut_eyebrow        — eyebrow
 *   sap_aut_titulo         — título H2 (2 linhas, textarea)
 *   sap_aut_texto          — texto de apoio (textarea)
 *   sap_aut_{1-5}_etapa1   — texto do trigger (bold, com ícone)
 *   sap_aut_{1-5}_etapa2   — segunda etapa
 *   sap_aut_{1-5}_etapa3   — terceira etapa
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'sap_aut_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'sap_aut_titulo' );
$texto   = cliconnect_campo_pagina( 'sap_aut_texto' );

$colunas = array();
for ( $i = 1; $i <= 5; $i++ ) {
	$e1 = cliconnect_campo_pagina( 'sap_aut_' . $i . '_etapa1' );
	$e2 = cliconnect_campo_pagina( 'sap_aut_' . $i . '_etapa2' );
	$e3 = cliconnect_campo_pagina( 'sap_aut_' . $i . '_etapa3' );
	if ( $e1 ) {
		$colunas[] = array(
			'num'    => $i,
			'etapa1' => $e1,
			'etapa2' => $e2 ?? '',
			'etapa3' => $e3 ?? '',
		);
	}
}

if ( ! $titulo || ! $colunas ) {
	return;
}

$uri  = get_template_directory_uri();
$plus = $uri . '/assets/img/sap-aut-plus.svg';
$bg   = $uri . '/assets/img/sap-aut-bg.png';
?>
<section class="sap-automacao">
	<div class="container sap-automacao__inner">

		<!-- Header -->
		<div class="sap-automacao__header">
			<?php if ( $eyebrow ) : ?>
				<p class="sap-automacao__eyebrow eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>
			<h2 class="sap-automacao__titulo"><?php echo nl2br( esc_html( $titulo ) ); ?></h2>
			<?php if ( $texto ) : ?>
				<p class="sap-automacao__texto"><?php echo nl2br( esc_html( $texto ) ); ?></p>
			<?php endif; ?>
		</div>

		<!-- Painel de colunas -->
		<div class="sap-automacao__painel">
			<div class="sap-automacao__painel-bg" aria-hidden="true">
				<img src="<?php echo esc_url( $bg ); ?>" alt="" loading="lazy" decoding="async">
			</div>

			<div class="sap-automacao__colunas">
				<?php foreach ( $colunas as $col ) :
					$n = $col['num'];
				?>
				<div class="sap-automacao__col sap-automacao__col--<?php echo $n; ?>">

					<!-- Etapa 1 — trigger com ícone -->
					<div class="sap-automacao__etapa sap-automacao__etapa--trigger">
						<span class="sap-automacao__icone-wrap" aria-hidden="true">
							<span class="sap-automacao__icone"></span>
						</span>
						<span class="sap-automacao__etapa-texto sap-automacao__etapa-texto--bold">
							<?php echo esc_html( $col['etapa1'] ); ?>
						</span>
					</div>

					<!-- Linha conectora 1 -->
					<div class="sap-automacao__linha" aria-hidden="true">
						<span class="sap-automacao__linha-seg"></span>
						<img class="sap-automacao__plus" src="<?php echo esc_url( $plus ); ?>" alt="" width="9" height="9" loading="lazy" decoding="async">
						<span class="sap-automacao__linha-seg"></span>
					</div>

					<!-- Etapa 2 -->
					<div class="sap-automacao__etapa">
						<span class="sap-automacao__etapa-texto">
							<?php echo esc_html( $col['etapa2'] ); ?>
						</span>
					</div>

					<!-- Linha conectora 2 -->
					<div class="sap-automacao__linha" aria-hidden="true">
						<span class="sap-automacao__linha-seg"></span>
						<img class="sap-automacao__plus" src="<?php echo esc_url( $plus ); ?>" alt="" width="9" height="9" loading="lazy" decoding="async">
						<span class="sap-automacao__linha-seg"></span>
					</div>

					<!-- Etapa 3 -->
					<div class="sap-automacao__etapa">
						<span class="sap-automacao__etapa-texto">
							<?php echo esc_html( $col['etapa3'] ); ?>
						</span>
					</div>

				</div>
				<?php endforeach; ?>
			</div>
		</div>

	</div>
</section>
