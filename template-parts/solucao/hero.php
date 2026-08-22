<?php
/**
 * Solução — Seção Hero.
 *
 * Campos ACF (group_cli_solucao, aba "1 · Hero"):
 *   solucao_hero_eyebrow, solucao_hero_titulo, solucao_hero_titulo_destaque,
 *   solucao_hero_titulo_fim, solucao_hero_corpo, solucao_hero_btn1_texto, solucao_hero_btn1_url,
 *   solucao_hero_btn2_texto, solucao_hero_btn2_url, solucao_hero_imagem.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow         = cliconnect_campo_pagina( 'solucao_hero_eyebrow' );
$titulo          = cliconnect_campo_pagina( 'solucao_hero_titulo' );
$titulo_destaque = cliconnect_campo_pagina( 'solucao_hero_titulo_destaque' );
$titulo_fim      = cliconnect_campo_pagina( 'solucao_hero_titulo_fim' );
$corpo           = cliconnect_campo_pagina( 'solucao_hero_corpo' );
$btn1_texto      = cliconnect_campo_pagina( 'solucao_hero_btn1_texto' );
$btn1_url        = cliconnect_campo_pagina( 'solucao_hero_btn1_url' );
$btn2_texto      = cliconnect_campo_pagina( 'solucao_hero_btn2_texto' );
$btn2_url        = cliconnect_campo_pagina( 'solucao_hero_btn2_url' );
$imagem_id       = cliconnect_campo_pagina( 'solucao_hero_imagem', 0 );

if ( ! $titulo && ! $titulo_destaque ) {
	return;
}

// Com continuação, o título vira uma frase corrida e o destaque azul fica no meio dela.
$titulo_classe = $titulo_fim ? 'sh-hero__titulo sh-hero__titulo--fluido' : 'sh-hero__titulo';
?>
<section class="sh-hero" aria-label="<?php esc_attr_e( 'Apresentação', 'cli' ); ?>">
	<div class="container">
		<div class="sh-hero__inner">

			<!-- Chamada -->
			<div class="sh-hero__chamada">

				<?php if ( $eyebrow ) : ?>
					<p class="eyebrow sh-hero__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>

				<h1 class="<?php echo esc_attr( $titulo_classe ); ?>">
					<?php if ( $titulo ) : ?>
						<span class="sh-hero__titulo-linha"><?php echo esc_html( $titulo ); ?></span>
					<?php endif; ?>
					<?php if ( $titulo_destaque ) : ?>
						<span class="sh-hero__titulo-destaque"><?php echo esc_html( $titulo_destaque ); ?></span>
					<?php endif; ?>
					<?php if ( $titulo_fim ) : ?>
						<span class="sh-hero__titulo-linha"><?php echo esc_html( $titulo_fim ); ?></span>
					<?php endif; ?>
				</h1>

				<?php if ( $corpo ) : ?>
					<p class="sh-hero__corpo"><?php echo esc_html( $corpo ); ?></p>
				<?php endif; ?>

				<?php if ( $btn1_url || $btn2_url ) : ?>
					<div class="sh-hero__botoes">
						<?php if ( $btn1_url && $btn1_texto ) : ?>
							<a class="botao botao--primario" href="<?php echo esc_url( $btn1_url ); ?>">
								<?php echo esc_html( $btn1_texto ); ?>
							</a>
						<?php endif; ?>

						<?php if ( $btn2_url && $btn2_texto ) : ?>
							<a class="botao botao--contorno" href="<?php echo esc_url( $btn2_url ); ?>">
								<?php echo esc_html( $btn2_texto ); ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

			</div><!-- .sh-hero__chamada -->

			<!-- Imagem da solução -->
			<div class="sh-hero__imagem-wrap" aria-hidden="true">
				<?php echo cliconnect_imagem_tema( 'solucao-hero-grade.png', array( 'alt' => '', 'class' => 'sh-hero__grade' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

				<?php if ( $imagem_id ) : ?>
					<?php echo wp_get_attachment_image( $imagem_id, 'large', false, array( 'class' => 'sh-hero__imagem', 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endif; ?>

				<div class="sh-hero__sinal">
					<?php echo cliconnect_imagem_tema( 'solucao-hero-sinal.svg', array( 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div><!-- .sh-hero__imagem-wrap -->

		</div><!-- .sh-hero__inner -->
	</div><!-- .container -->
</section><!-- .sh-hero -->
