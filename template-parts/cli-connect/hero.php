<?php
/**
 * CLI Connect — Hero.
 *
 * Grid 2 colunas: texto à esquerda, diagrama orbital à direita.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow          = cliconnect_campo_pagina( 'cc_hero_eyebrow' );
$titulo           = cliconnect_campo_pagina( 'cc_hero_titulo' );
$titulo_destaque  = cliconnect_campo_pagina( 'cc_hero_titulo_destaque' );
$texto            = cliconnect_campo_pagina( 'cc_hero_texto' );
$botao            = cliconnect_campo_pagina( 'cc_hero_botao' );
$imagem           = cliconnect_campo_pagina( 'cc_hero_imagem' );

if ( ! $titulo && ! $titulo_destaque ) {
	return;
}
?>
<section class="cc-hero">
	<div class="cc-hero__inner container">

		<div class="cc-hero__conteudo">
			<?php if ( $eyebrow ) : ?>
				<p class="cc-hero__eyebrow eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<h1 class="cc-hero__titulo">
				<?php if ( $titulo ) : ?>
					<span class="cc-hero__titulo-linha"><?php echo esc_html( $titulo ); ?></span>
				<?php endif; ?>
				<?php if ( $titulo_destaque ) : ?>
					<span class="cc-hero__titulo-destaque"><?php echo esc_html( $titulo_destaque ); ?></span>
				<?php endif; ?>
			</h1>

			<?php if ( $texto ) : ?>
				<p class="cc-hero__texto"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>

			<?php if ( $botao ) : ?>
				<a class="cc-hero__botao botao botao--primario" href="<?php echo esc_url( $botao['url'] ?? '' ); ?>"<?php echo ! empty( $botao['target'] ) ? ' target="_blank" rel="noopener"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- string literal hardcoded. ?>>
					<?php echo esc_html( $botao['title'] ?? '' ); ?>
				</a>
			<?php endif; ?>
		</div>

		<?php if ( $imagem ) : ?>
			<div class="cc-hero__orbita" aria-hidden="true">
				<?php echo wp_get_attachment_image( (int) $imagem, 'large', false, array( 'class' => 'cc-hero__orbita-img', 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapa internamente. ?>
			</div>
		<?php endif; ?>

	</div>
</section>
