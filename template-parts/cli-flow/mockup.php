<?php
/**
 * CLI Flow — Mockup com seletor flutuante.
 *
 * Slider de telas com seletor dark sobreposto na parte inferior da imagem.
 * As telas ficam lado a lado num trilho que desliza na troca de aba
 * (page-cli-flow.js). A moldura tem a proporção do Figma (1316×691) e
 * desbota a base para o branco; o fundo azul do hero continua atrás do
 * mockup até ~62% da altura dele, com a textura do Figma
 * (assets/img/cf-hero-textura.png) apoiada na divisa azul/branco.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$legenda = cliconnect_campo_pagina( 'cf_mockup_legenda' );

$abas = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$label  = cliconnect_campo_pagina( "cf_mockup_{$i}_label" );
	$imagem = cliconnect_campo_pagina( "cf_mockup_{$i}_imagem" );
	if ( $label || $imagem ) {
		$abas[] = array(
			'indice' => $i,
			'label'  => $label,
			'imagem' => $imagem,
		);
	}
}

if ( ! $abas ) {
	return;
}
?>
<section class="cf-mockup">
	<div class="container">

		<div class="cf-mockup__viewport">
			<?php /* Moldura: recorta a tela e desbota a base para o branco ---- */ ?>
			<div class="cf-mockup__moldura">
			<div class="cf-mockup__trilho" data-cf-mockup-trilho>
			<?php foreach ( $abas as $index => $aba ) : ?>
				<div
					class="cf-mockup__painel<?php echo 0 === $index ? ' ativo' : ''; ?>"
					data-cf-panel="<?php echo absint( $aba['indice'] ); ?>"
					<?php echo 0 === $index ? '' : 'aria-hidden="true"'; ?>
				>
					<?php if ( $aba['imagem'] ) : ?>
						<?php
						echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							(int) $aba['imagem'],
							'full',
							false,
							array(
								'class'   => 'cf-mockup__img',
								'alt'     => esc_attr( $aba['label'] ?? '' ),
								'loading' => 0 === $index ? 'eager' : 'lazy',
							)
						);
						?>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
			</div>
			</div>

			<?php /* Seletor flutuante (dark bar) ------------------------------ */ ?>
			<div class="cf-mockup__seletor">
				<div class="cf-mockup__seletor-bar" role="tablist" aria-label="<?php esc_attr_e( 'Capturas de tela', 'cli' ); ?>">
					<?php foreach ( $abas as $index => $aba ) : ?>
						<button
							class="cf-mockup__tab<?php echo 0 === $index ? ' ativo' : ''; ?>"
							role="tab"
							type="button"
							aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
							data-cf-mockup-tab="<?php echo absint( $aba['indice'] ); ?>"
						>
							<?php echo esc_html( $aba['label'] ); ?>
						</button>
					<?php endforeach; ?>
				</div>

				<?php if ( $legenda ) : ?>
					<p class="cf-mockup__legenda"><?php echo esc_html( $legenda ); ?></p>
				<?php endif; ?>
			</div>
		</div>

	</div>
</section>
