<?php
/**
 * Integração SAP — Integrações inclusas.
 *
 * Grid 2×4 de cards SAP ↔ parceiro, fade na segunda linha e CTA.
 * Logo SAP e bg decorativo são assets estáticos em assets/img/.
 *
 * Campos ACF (group_cli_integracao_sap, aba "9 · Integrações"):
 *   sap_int_eyebrow      — eyebrow
 *   sap_int_titulo       — título H2 (2 linhas)
 *   sap_int_botao        — botão CTA
 *   sap_int_nota         — texto abaixo do botão
 *   sap_int_{1-8}_titulo — "SAP + Parceiro"
 *   sap_int_{1-8}_desc   — descrição do card
 *   sap_int_{1-8}_logo   — logo do parceiro (imagem)
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'sap_int_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'sap_int_titulo' );
$botao   = cliconnect_campo_pagina( 'sap_int_botao' );
$nota    = cliconnect_campo_pagina( 'sap_int_nota' );

$integracoes = array();
for ( $i = 1; $i <= 8; $i++ ) {
	$integracoes[] = array(
		'titulo' => cliconnect_campo_pagina( 'sap_int_' . $i . '_titulo' ),
		'desc'   => cliconnect_campo_pagina( 'sap_int_' . $i . '_desc' ),
		'logo'   => cliconnect_campo_pagina( 'sap_int_' . $i . '_logo' ),
	);
}

if ( ! $titulo ) {
	return;
}

$uri        = get_template_directory_uri();
$sap_logo   = $uri . '/assets/img/sap-int-sap.png';
$int_bg     = $uri . '/assets/img/sap-int-bg.png';
$int_vector = $uri . '/assets/img/sap-int-vector.svg';
?>
<section class="sap-integracoes">
	<div class="container sap-integracoes__inner">

		<div class="sap-integracoes__header">
			<?php if ( $eyebrow ) : ?>
				<p class="sap-integracoes__eyebrow eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>
			<h2 class="sap-integracoes__titulo"><?php echo esc_html( $titulo ); ?></h2>
		</div>

		<div class="sap-integracoes__grid-wrap">
			<div class="sap-integracoes__grid">
				<?php foreach ( $integracoes as $int ) : ?>
					<?php if ( ! $int['titulo'] ) : continue; endif; ?>
					<div class="sap-int-card">

						<div class="sap-int-card__img-area" aria-hidden="true">
							<img class="sap-int-card__bg" src="<?php echo esc_url( $int_bg ); ?>" alt="" loading="lazy" decoding="async">
							<img class="sap-int-card__vector" src="<?php echo esc_url( $int_vector ); ?>" alt="" loading="lazy" decoding="async">

							<div class="sap-int-card__logos">
								<div class="sap-int-card__logo-wrap">
									<img class="sap-int-card__logo" src="<?php echo esc_url( $sap_logo ); ?>" alt="SAP" width="80" height="80" loading="lazy" decoding="async">
								</div>
								<div class="sap-int-card__linha" aria-hidden="true"></div>
								<div class="sap-int-card__logo-wrap">
									<?php if ( $int['logo'] ) : ?>
										<?php $logo_src = wp_get_attachment_image_url( $int['logo'], 'thumbnail' ); ?>
										<?php if ( $logo_src ) : ?>
											<img class="sap-int-card__logo" src="<?php echo esc_url( $logo_src ); ?>" alt="" width="80" height="80" loading="lazy" decoding="async">
										<?php endif; ?>
									<?php endif; ?>
								</div>
							</div>
						</div>

						<div class="sap-int-card__body">
							<p class="sap-int-card__titulo"><?php echo esc_html( $int['titulo'] ); ?></p>
							<?php if ( $int['desc'] ) : ?>
								<p class="sap-int-card__desc"><?php echo esc_html( $int['desc'] ); ?></p>
							<?php endif; ?>
						</div>

					</div>
				<?php endforeach; ?>
			</div>
			<div class="sap-integracoes__fade" aria-hidden="true"></div>
		</div>

		<div class="sap-integracoes__cta">
			<?php cliconnect_botao( $botao ); ?>
			<?php if ( $nota ) : ?>
				<p class="sap-integracoes__nota"><?php echo esc_html( $nota ); ?></p>
			<?php endif; ?>
		</div>

	</div>
</section>
