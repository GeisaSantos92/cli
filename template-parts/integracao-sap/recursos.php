<?php
/**
 * Integração SAP — Libere Recursos (horas trabalhadas).
 *
 * Layout: 2 colunas — conteúdo + card de métrica à esquerda, imagem à direita.
 *
 * Campos ACF (group_cli_integracao_sap, aba "5 · Libere Recursos"):
 *   sap_rec_eyebrow          — eyebrow
 *   sap_rec_titulo           — título H3
 *   sap_rec_texto            — texto de apoio
 *   sap_rec_metrica_numero   — número da métrica (ex.: "65%")
 *   sap_rec_metrica_label    — rótulo da métrica
 *   sap_rec_imagem           — imagem base (direita)
 *   sap_rec_imagem_overlay   — camada superior da imagem
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow        = cliconnect_campo_pagina( 'sap_rec_eyebrow' );
$titulo         = cliconnect_campo_pagina( 'sap_rec_titulo' );
$texto          = cliconnect_campo_pagina( 'sap_rec_texto' );
$metrica_numero = cliconnect_campo_pagina( 'sap_rec_metrica_numero' );
$metrica_label  = cliconnect_campo_pagina( 'sap_rec_metrica_label' );
$imagem         = cliconnect_campo_pagina( 'sap_rec_imagem' );
$imagem_overlay = cliconnect_campo_pagina( 'sap_rec_imagem_overlay' );

if ( ! $titulo ) {
	return;
}
?>
<section class="sap-recursos">
	<div class="container sap-recursos__inner">

		<div class="sap-recursos__conteudo">
			<?php if ( $eyebrow ) : ?>
				<p class="sap-recursos__eyebrow eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<h3 class="sap-recursos__titulo"><?php echo esc_html( $titulo ); ?></h3>

			<?php if ( $texto ) : ?>
				<p class="sap-recursos__texto"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>

			<?php if ( $metrica_numero || $metrica_label ) : ?>
				<div class="sap-recursos__card">
					<?php if ( $metrica_numero ) : ?>
						<p class="sap-recursos__card-numero"><?php echo esc_html( $metrica_numero ); ?></p>
					<?php endif; ?>
					<?php if ( $metrica_label ) : ?>
						<p class="sap-recursos__card-label"><?php echo esc_html( $metrica_label ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $imagem ) : ?>
			<div class="sap-recursos__imagem-wrap" aria-hidden="true">
				<?php
				$img_src = wp_get_attachment_image_url( $imagem, 'large' );
				if ( $img_src ) :
					?>
					<img
						class="sap-recursos__img"
						src="<?php echo esc_url( $img_src ); ?>"
						alt=""
						width="760"
						height="507"
						loading="lazy"
						decoding="async"
					>
				<?php endif; ?>
				<?php if ( $imagem_overlay ) : ?>
					<?php $overlay_src = wp_get_attachment_image_url( $imagem_overlay, 'large' ); ?>
					<?php if ( $overlay_src ) : ?>
						<img
							class="sap-recursos__img sap-recursos__img--overlay"
							src="<?php echo esc_url( $overlay_src ); ?>"
							alt=""
							width="760"
							height="507"
							loading="lazy"
							decoding="async"
						>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>

	</div>
</section>
