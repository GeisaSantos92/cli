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
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
							<path d="M16.175 13H5C4.71667 13 4.47917 12.9042 4.2875 12.7125C4.09583 12.5208 4 12.2833 4 12C4 11.7167 4.09583 11.4792 4.2875 11.2875C4.47917 11.0958 4.71667 11 5 11H16.175L11.275 6.1C11.075 5.9 10.9792 5.66667 10.9875 5.4C10.9958 5.13333 11.1 4.9 11.3 4.7C11.5 4.51667 11.7333 4.42083 12 4.4125C12.2667 4.40417 12.5 4.5 12.7 4.7L19.3 11.3C19.4 11.4 19.4708 11.5083 19.5125 11.625C19.5542 11.7417 19.575 11.8667 19.575 12C19.575 12.1333 19.5542 12.2583 19.5125 12.375C19.4708 12.4917 19.4 12.6 19.3 12.7L12.7 19.3C12.5167 19.4833 12.2875 19.575 12.0125 19.575C11.7375 19.575 11.5 19.4833 11.3 19.3C11.1 19.1 11 18.8625 11 18.5875C11 18.3125 11.1 18.075 11.3 17.875L16.175 13Z" fill="currentColor"/>
						</svg>
					</a>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>
