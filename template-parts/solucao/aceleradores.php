<?php
/**
 * Solução — Seção Aceleradores de Integração.
 *
 * Fundo branco (var(--cor-fundo)). Layout: coluna de texto à esquerda
 * (eyebrow, título, corpo, 4 tópicos, botão CTA) + imagem à direita.
 *
 * Campos ACF (group_cli_solucao, aba "7 · Aceleradores"):
 *   solucao_acel_eyebrow, solucao_acel_titulo, solucao_acel_corpo,
 *   solucao_acel_topico_{1,2,3,4}, solucao_acel_btn_texto,
 *   solucao_acel_btn_url, solucao_acel_imagem (image ID).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow   = cliconnect_campo_pagina( 'solucao_acel_eyebrow' );
$titulo    = cliconnect_campo_pagina( 'solucao_acel_titulo' );
$corpo     = cliconnect_campo_pagina( 'solucao_acel_corpo' );
$btn_texto = cliconnect_campo_pagina( 'solucao_acel_btn_texto' );
$btn_url   = cliconnect_campo_pagina( 'solucao_acel_btn_url' );
$imagem_id = (int) cliconnect_campo_pagina( 'solucao_acel_imagem', 0 );

$topicos = array_filter( array(
	cliconnect_campo_pagina( 'solucao_acel_topico_1' ),
	cliconnect_campo_pagina( 'solucao_acel_topico_2' ),
	cliconnect_campo_pagina( 'solucao_acel_topico_3' ),
	cliconnect_campo_pagina( 'solucao_acel_topico_4' ),
) );

if ( ! $titulo && ! $corpo ) {
	return;
}

$bullet_url = get_theme_file_uri( '/assets/img/solucao-dif-bullet.svg' );
?>
<section class="sacel-acel" aria-label="<?php esc_attr_e( 'Aceleradores de Integração', 'cli' ); ?>">
	<div class="container">
		<div class="sacel-acel__inner">

			<!-- Coluna de texto -->
			<div class="sacel-acel__texto">

				<?php if ( $eyebrow ) : ?>
					<p class="eyebrow sacel-acel__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>

				<?php if ( $titulo ) : ?>
					<h2 class="sacel-acel__titulo"><?php echo esc_html( $titulo ); ?></h2>
				<?php endif; ?>

				<?php if ( $corpo ) : ?>
					<p class="sacel-acel__corpo"><?php echo esc_html( $corpo ); ?></p>
				<?php endif; ?>

				<?php if ( $topicos ) : ?>
					<ul class="sacel-acel__topicos">
						<?php foreach ( $topicos as $topico ) : ?>
							<li class="sacel-acel__topico">
								<span
									class="sacel-acel__bullet"
									aria-hidden="true"
									style="mask-image: url('<?php echo esc_url( $bullet_url ); ?>'); -webkit-mask-image: url('<?php echo esc_url( $bullet_url ); ?>');"
								></span>
								<span class="sacel-acel__topico-texto"><?php echo esc_html( $topico ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( $btn_texto ) : ?>
					<div class="sacel-acel__btn-wrap">
						<?php if ( $btn_url ) : ?>
							<a href="<?php echo esc_url( $btn_url ); ?>" class="botao botao--primario sacel-acel__btn">
								<?php echo esc_html( $btn_texto ); ?>
							</a>
						<?php else : ?>
							<span class="botao botao--primario sacel-acel__btn">
								<?php echo esc_html( $btn_texto ); ?>
							</span>
						<?php endif; ?>
					</div>
				<?php endif; ?>

			</div><!-- .sacel-acel__texto -->

			<!-- Coluna de imagem -->
			<?php if ( $imagem_id ) : ?>
				<div class="sacel-acel__imagem-wrap" aria-hidden="true">
					<?php echo wp_get_attachment_image( $imagem_id, 'large', false, array( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'class'   => 'sacel-acel__imagem',
						'loading' => 'lazy',
					) ); ?>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>
