<?php
/**
 * Integração SAP — Depoimento / Case destaque.
 *
 * Card azul escuro com foto, nome, cargo, frase de impacto e botão de case.
 * Elementos decorativos (brilho e vector) são assets estáticos.
 *
 * Campos ACF (group_cli_integracao_sap, aba "6 · Depoimento"):
 *   sap_dep_foto   — foto do depoente
 *   sap_dep_nome   — nome
 *   sap_dep_cargo  — cargo
 *   sap_dep_frase  — frase/quote
 *   sap_dep_botao  — link "Confira o case"
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$foto  = cliconnect_campo_pagina( 'sap_dep_foto' );
$nome  = cliconnect_campo_pagina( 'sap_dep_nome' );
$cargo = cliconnect_campo_pagina( 'sap_dep_cargo' );
$frase = cliconnect_campo_pagina( 'sap_dep_frase' );
$botao = cliconnect_campo_pagina( 'sap_dep_botao' );

if ( ! $frase ) {
	return;
}

$brilho_url = get_template_directory_uri() . '/assets/img/sap-dep-brilho.svg';
$vector_url = get_template_directory_uri() . '/assets/img/sap-dep-vector.svg';
?>
<section class="sap-depoimento">
	<div class="container">
		<div class="sap-depoimento__card">

			<!-- Decorativo: brilho esquerdo -->
			<img class="sap-depoimento__brilho" src="<?php echo esc_url( $brilho_url ); ?>" alt="" aria-hidden="true" width="788" height="819" loading="lazy" decoding="async">

			<!-- Decorativo: vector direito -->
			<img class="sap-depoimento__vector" src="<?php echo esc_url( $vector_url ); ?>" alt="" aria-hidden="true" width="722" height="1077" loading="lazy" decoding="async">

			<!-- Perfil -->
			<div class="sap-depoimento__profile">
				<?php if ( $foto ) : ?>
					<div class="sap-depoimento__foto-wrap">
						<?php
						$foto_src = wp_get_attachment_image_url( $foto, 'thumbnail' );
						if ( $foto_src ) :
							?>
							<img
								class="sap-depoimento__foto"
								src="<?php echo esc_url( $foto_src ); ?>"
								alt="<?php echo esc_attr( $nome ); ?>"
								width="91"
								height="91"
								loading="lazy"
								decoding="async"
							>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="sap-depoimento__infos">
					<?php if ( $nome ) : ?>
						<p class="sap-depoimento__nome"><?php echo esc_html( $nome ); ?></p>
					<?php endif; ?>
					<?php if ( $cargo ) : ?>
						<p class="sap-depoimento__cargo"><?php echo esc_html( $cargo ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<!-- Frase -->
			<div class="sap-depoimento__frase-wrap">
				<p class="sap-depoimento__frase"><?php echo esc_html( $frase ); ?></p>
			</div>

			<!-- Botão -->
			<?php if ( $botao && ! empty( $botao['url'] ) ) : ?>
				<div class="sap-depoimento__botao-wrap">
					<a
						class="sap-depoimento__botao"
						href="<?php echo esc_url( $botao['url'] ); ?>"
						<?php echo ! empty( $botao['target'] ) ? 'target="' . esc_attr( $botao['target'] ) . '" rel="noopener noreferrer"' : ''; ?>
					>
						<?php echo esc_html( $botao['title'] ); ?>
						<?php cliconnect_icone( 'seta-direita', 24 ); ?>
					</a>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>
