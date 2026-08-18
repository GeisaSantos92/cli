<?php
/**
 * Integração SAP — Preserve seu Clean Core.
 *
 * Header centralizado + 3 cards com imagem, título e texto.
 * Card 3 tem duas imagens sobrepostas.
 *
 * Campos ACF (group_cli_integracao_sap, aba "8 · Clean Core"):
 *   sap_cc_eyebrow       — eyebrow
 *   sap_cc_titulo        — título H2
 *   sap_cc_texto         — texto de apoio
 *   sap_cc_{1-3}_titulo  — título do card
 *   sap_cc_{1-3}_texto   — texto do card
 *   sap_cc_{1-3}_imagem  — imagem do card
 *   sap_cc_3_imagem_b    — overlay da imagem do card 3
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'sap_cc_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'sap_cc_titulo' );
$texto   = cliconnect_campo_pagina( 'sap_cc_texto' );

$cards = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$cards[] = array(
		'titulo'  => cliconnect_campo_pagina( 'sap_cc_' . $i . '_titulo' ),
		'texto'   => cliconnect_campo_pagina( 'sap_cc_' . $i . '_texto' ),
		'imagem'  => cliconnect_campo_pagina( 'sap_cc_' . $i . '_imagem' ),
		'imagem_b' => 3 === $i ? cliconnect_campo_pagina( 'sap_cc_3_imagem_b' ) : null,
	);
}

if ( ! $titulo ) {
	return;
}
?>
<section class="sap-cleancore">
	<div class="container">

		<div class="sap-cleancore__header">
			<?php if ( $eyebrow ) : ?>
				<p class="sap-cleancore__eyebrow eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<h2 class="sap-cleancore__titulo"><?php echo esc_html( $titulo ); ?></h2>

			<?php if ( $texto ) : ?>
				<p class="sap-cleancore__subtitulo"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>
		</div>

		<div class="sap-cleancore__cards">
			<?php foreach ( $cards as $card ) : ?>
				<?php if ( ! $card['titulo'] ) : continue; endif; ?>
				<div class="sap-cleancore__card">

					<div class="sap-cleancore__card-img-wrap" aria-hidden="true">
						<?php if ( $card['imagem'] ) : ?>
							<?php $img_src = wp_get_attachment_image_url( $card['imagem'], 'large' ); ?>
							<?php if ( $img_src ) : ?>
								<img
									class="sap-cleancore__card-img"
									src="<?php echo esc_url( $img_src ); ?>"
									alt=""
									loading="lazy"
									decoding="async"
								>
							<?php endif; ?>
						<?php endif; ?>
						<?php if ( $card['imagem_b'] ) : ?>
							<?php $img_b_src = wp_get_attachment_image_url( $card['imagem_b'], 'large' ); ?>
							<?php if ( $img_b_src ) : ?>
								<img
									class="sap-cleancore__card-img sap-cleancore__card-img--overlay"
									src="<?php echo esc_url( $img_b_src ); ?>"
									alt=""
									loading="lazy"
									decoding="async"
								>
							<?php endif; ?>
						<?php endif; ?>
					</div>

					<div class="sap-cleancore__card-body">
						<p class="sap-cleancore__card-titulo"><?php echo esc_html( $card['titulo'] ); ?></p>
						<?php if ( $card['texto'] ) : ?>
							<p class="sap-cleancore__card-texto"><?php echo esc_html( $card['texto'] ); ?></p>
						<?php endif; ?>
					</div>

				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>
