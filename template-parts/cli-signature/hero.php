<?php
/**
 * CLI Signature — Hero.
 *
 * Layout de duas colunas sobre fundo escuro com foto de fundo:
 * texto à esquerda, coluna direita vazia que empurra o texto
 * para longe da área com o rosto da foto.
 *
 * Campos ACF (group_cli_cli_signature, aba "1 · Hero"):
 *   cs_hero_eyebrow — eyebrow (texto curto, ex.: "CLI SIGNATURE")
 *   cs_hero_titulo  — título principal
 *   cs_hero_texto   — parágrafo de apoio
 *   cs_hero_botao   — link do CTA (array url/title/target)
 *   cs_hero_bg      — imagem de fundo (ID do anexo)
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'cs_hero_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'cs_hero_titulo' );
$texto   = cliconnect_campo_pagina( 'cs_hero_texto' );
$botao   = cliconnect_campo_pagina( 'cs_hero_botao' );
$bg_id   = (int) ( cliconnect_campo_pagina( 'cs_hero_bg' ) ?? 0 );

if ( ! $titulo ) {
	return;
}
?>
<section class="cs-hero">

	<?php if ( $bg_id ) : ?>
		<div class="cs-hero__bg" aria-hidden="true">
			<?php
			echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapa internamente.
				$bg_id,
				'full',
				false,
				array(
					'class'         => 'cs-hero__bg-img',
					'alt'           => '',
					'loading'       => 'eager',
					'fetchpriority' => 'high',
					'decoding'      => 'async',
				)
			);
			?>
		</div>
	<?php endif; ?>

	<div class="cs-hero__overlay" aria-hidden="true"></div>

	<div class="container cs-hero__inner">

		<div class="cs-hero__conteudo">

			<?php if ( $eyebrow ) : ?>
				<p class="cs-hero__eyebrow eyebrow eyebrow--clara"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<h1 class="cs-hero__titulo"><?php echo esc_html( $titulo ); ?></h1>

			<?php if ( $texto ) : ?>
				<p class="cs-hero__texto"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>

			<?php if ( $botao ) : ?>
				<div class="cs-hero__acoes">
					<a class="botao botao--primario"
					   href="<?php echo esc_url( $botao['url'] ?? '' ); ?>"
					   <?php echo ! empty( $botao['target'] ) ? 'target="_blank" rel="noopener"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- string literal hardcoded. ?>>
						<?php echo esc_html( $botao['title'] ?? '' ); ?>
					</a>
				</div>
			<?php endif; ?>

		</div>

		<div class="cs-hero__imagem" aria-hidden="true"></div>

	</div>

</section>
