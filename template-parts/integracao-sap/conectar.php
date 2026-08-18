<?php
/**
 * Integração SAP — SAP Integrado (Conectar).
 *
 * Layout: 2 colunas — conteúdo à esquerda, imagem à direita.
 * Sem botão/CTA nesta seção.
 *
 * Campos ACF (group_cli_integracao_sap, aba "3 · SAP Integrado"):
 *   sap_con_eyebrow  — eyebrow
 *   sap_con_titulo   — título H3
 *   sap_con_texto    — texto de apoio
 *   sap_con_imagem   — imagem (direita)
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'sap_con_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'sap_con_titulo' );
$texto   = cliconnect_campo_pagina( 'sap_con_texto' );
$imagem  = cliconnect_campo_pagina( 'sap_con_imagem' );

if ( ! $titulo ) {
	return;
}
?>
<section class="sap-conectar">
	<div class="container sap-conectar__inner">

		<div class="sap-conectar__conteudo">
			<?php if ( $eyebrow ) : ?>
				<p class="sap-conectar__eyebrow eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<h3 class="sap-conectar__titulo"><?php echo esc_html( $titulo ); ?></h3>

			<?php if ( $texto ) : ?>
				<p class="sap-conectar__texto"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $imagem ) : ?>
			<div class="sap-conectar__imagem-wrap">
				<?php
				$img_src = wp_get_attachment_image_url( $imagem, 'large' );
				$img_alt = get_post_meta( $imagem, '_wp_attachment_image_alt', true );
				if ( $img_src ) :
					?>
					<img
						class="sap-conectar__img"
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
