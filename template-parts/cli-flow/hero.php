<?php
/**
 * CLI Flow — Hero.
 *
 * Seção centralizada: eyebrow pill outline + H1 bicolor + texto com peso
 * misto + 2 botões retangulares + faixa de logos em caixinhas brancas.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow         = cliconnect_campo_pagina( 'cf_hero_eyebrow' );
$titulo          = cliconnect_campo_pagina( 'cf_hero_titulo' );
$titulo_destaque = cliconnect_campo_pagina( 'cf_hero_titulo_destaque' );
$texto           = cliconnect_campo_pagina( 'cf_hero_texto' );
$botao_1         = cliconnect_campo_pagina( 'cf_hero_botao_1', array() );
$botao_2         = cliconnect_campo_pagina( 'cf_hero_botao_2', array() );
$logos_label     = cliconnect_campo_pagina( 'cf_hero_logos_label' );

if ( ! $titulo && ! $titulo_destaque ) {
	return;
}

/*
 * Logos dos sistemas — importados pelo seed como attachments (cf-logo-*.png).
 * Lidos do campo numerado cf_hero_logo_{n} (image, return id).
 * Se ainda não houver campo dedicado, usa fallback com nomes fixos do seed.
 */
$logos_nomes = array( 'Senior', 'Thomson Reuters', 'SAP', 'Salesforce', 'Sankhya', 'IFS' );
?>
<section class="cf-hero">
	<div class="cf-hero__inner container">

		<div class="cf-hero__conteudo">
			<?php if ( $eyebrow ) : ?>
				<p class="cf-hero__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<h1 class="cf-hero__titulo">
				<?php if ( $titulo ) : ?>
					<span class="cf-hero__titulo-linha"><?php echo esc_html( $titulo ); ?></span>
				<?php endif; ?>
				<?php if ( $titulo_destaque ) : ?>
					<span class="cf-hero__titulo-destaque"><?php echo esc_html( $titulo_destaque ); ?></span>
				<?php endif; ?>
			</h1>

			<?php if ( $texto ) : ?>
				<p class="cf-hero__texto"><?php echo wp_kses_post( $texto ); ?></p>
			<?php endif; ?>

			<div class="cf-hero__acoes">
				<?php if ( ! empty( $botao_1['url'] ) ) : ?>
					<a class="cf-hero__botao cf-hero__botao--primario" href="<?php echo esc_url( $botao_1['url'] ); ?>"<?php echo ! empty( $botao_1['target'] ) ? ' target="_blank" rel="noopener"' : ''; ?>>
						<?php echo esc_html( $botao_1['title'] ?? '' ); ?>
					</a>
				<?php endif; ?>
				<?php if ( ! empty( $botao_2['url'] ) ) : ?>
					<a class="cf-hero__botao cf-hero__botao--secundario" href="<?php echo esc_url( $botao_2['url'] ); ?>"<?php echo ! empty( $botao_2['target'] ) ? ' target="_blank" rel="noopener"' : ''; ?>>
						<?php echo esc_html( $botao_2['title'] ?? '' ); ?>
					</a>
				<?php endif; ?>
			</div>

			<?php if ( $logos_label ) : ?>
				<div class="cf-hero__logos">
					<p class="cf-hero__logos-label"><?php echo esc_html( $logos_label ); ?></p>
					<div class="cf-hero__logos-grid">
						<?php
						for ( $i = 1; $i <= 6; $i++ ) :
							$logo_id = cliconnect_campo_pagina( "cf_hero_logo_{$i}" );
							if ( ! $logo_id ) {
								continue;
							}
							$alt = $logos_nomes[ $i - 1 ] ?? '';
							?>
							<span class="cf-hero__logo-card">
								<?php echo wp_get_attachment_image( (int) $logo_id, 'medium', false, array( 'class' => 'cf-hero__logo-img', 'alt' => $alt, 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</span>
						<?php endfor; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>

	</div>
</section>
