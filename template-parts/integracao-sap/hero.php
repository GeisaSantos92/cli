<?php
/**
 * Integração SAP — Hero.
 *
 * Layout 2 colunas: título bicolor + texto + CTA à esquerda, imagem à direita.
 * Título: parte azul (--cor-primaria) + parte escura na mesma linha/bloco.
 * LCP: imagem carregada eager + fetchpriority high.
 *
 * Campos ACF (group_cli_integracao_sap, aba "1 · Hero"):
 *   sap_hero_titulo_azul   — primeiro trecho do H1 (cor brand)
 *   sap_hero_titulo_escuro — segundo trecho do H1 (cor texto)
 *   sap_hero_texto         — parágrafo de apoio
 *   sap_hero_botao         — link do CTA
 *   sap_hero_imagem        — imagem da coluna direita (attachment ID)
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo_azul   = cliconnect_campo_pagina( 'sap_hero_titulo_azul' );
$titulo_escuro = cliconnect_campo_pagina( 'sap_hero_titulo_escuro' );
$texto         = cliconnect_campo_pagina( 'sap_hero_texto' );
$botao         = cliconnect_campo_pagina( 'sap_hero_botao' );
$imagem_id     = cliconnect_campo_pagina( 'sap_hero_imagem' );

if ( ! $titulo_azul && ! $titulo_escuro ) {
	return;
}
?>
<section class="sap-hero">
	<div class="container sap-hero__inner">

		<div class="sap-hero__conteudo">
			<?php if ( $titulo_azul || $titulo_escuro ) : ?>
				<h1 class="sap-hero__titulo">
					<?php if ( $titulo_azul ) : ?>
						<span class="sap-hero__titulo-azul"><?php echo esc_html( $titulo_azul ); ?> </span>
					<?php endif; ?>
					<?php if ( $titulo_escuro ) : ?>
						<?php echo esc_html( $titulo_escuro ); ?>
					<?php endif; ?>
				</h1>
			<?php endif; ?>

			<?php if ( $texto ) : ?>
				<p class="sap-hero__texto"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>

			<?php if ( $botao ) : ?>
				<div class="sap-hero__acoes">
					<?php cliconnect_botao( $botao ); ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $imagem_id ) : ?>
			<div class="sap-hero__imagem-wrap">
				<?php
				echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapa internamente.
					$imagem_id,
					'large',
					false,
					array(
						'class'          => 'sap-hero__img',
						'loading'        => 'eager',
						'fetchpriority'  => 'high',
						'alt'            => __( 'Diagrama de integração SAP com CLI Connect', 'cli' ),
						'decoding'       => 'sync',
					)
				);
				?>
			</div>
		<?php endif; ?>

	</div>
</section>
