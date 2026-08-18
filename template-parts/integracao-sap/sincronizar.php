<?php
/**
 * Integração SAP — SAP Sincronizado.
 *
 * Layout: 2 colunas — conteúdo à esquerda, imagem à direita.
 * Sem botão/CTA nesta seção.
 *
 * Campos ACF (group_cli_integracao_sap, aba "4 · SAP Sincronizado"):
 *   sap_sin_eyebrow  — eyebrow
 *   sap_sin_titulo   — título H3
 *   sap_sin_texto    — texto de apoio
 *   sap_sin_imagem   — imagem (direita)
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'sap_sin_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'sap_sin_titulo' );
$texto   = cliconnect_campo_pagina( 'sap_sin_texto' );
$imagem  = cliconnect_campo_pagina( 'sap_sin_imagem' );

if ( ! $titulo ) {
	return;
}
?>
<section class="sap-sincronizar">
	<div class="container sap-sincronizar__inner">

		<div class="sap-sincronizar__conteudo">
			<?php if ( $eyebrow ) : ?>
				<p class="sap-sincronizar__eyebrow eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<h3 class="sap-sincronizar__titulo"><?php echo esc_html( $titulo ); ?></h3>

			<?php if ( $texto ) : ?>
				<p class="sap-sincronizar__texto"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $imagem ) : ?>
			<div class="sap-sincronizar__imagem-wrap">
				<?php
				$img_src = wp_get_attachment_image_url( $imagem, 'large' );
				$img_alt = get_post_meta( $imagem, '_wp_attachment_image_alt', true );
				if ( $img_src ) :
					?>
					<img
						class="sap-sincronizar__img"
						src="<?php echo esc_url( $img_src ); ?>"
						alt="<?php echo esc_attr( $img_alt ); ?>"
						width="760"
						height="507"
						loading="lazy"
						decoding="async"
					>
				<?php endif; ?>
			</div>
		<?php endif; ?>

	</div>
</section>
