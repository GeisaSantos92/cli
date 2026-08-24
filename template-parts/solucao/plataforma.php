<?php
/**
 * Solução — Seção Plataforma Única.
 *
 * Fundo branco (var(--cor-fundo)). Layout idêntico ao Diferencial:
 * coluna de texto à esquerda + imagem à direita.
 *
 * Campos ACF (group_cli_solucao, aba "8 · Plataforma"):
 *   solucao_plat_eyebrow, solucao_plat_titulo, solucao_plat_corpo,
 *   solucao_plat_topico_{1,2,3}, solucao_plat_imagem (image ID).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow   = cliconnect_campo_pagina( 'solucao_plat_eyebrow' );
$titulo    = cliconnect_campo_pagina( 'solucao_plat_titulo' );
$corpo     = cliconnect_campo_pagina( 'solucao_plat_corpo' );
$imagem_id = (int) cliconnect_campo_pagina( 'solucao_plat_imagem', 0 );

$topicos = array_filter( array(
	cliconnect_campo_pagina( 'solucao_plat_topico_1' ),
	cliconnect_campo_pagina( 'solucao_plat_topico_2' ),
	cliconnect_campo_pagina( 'solucao_plat_topico_3' ),
) );

if ( ! $titulo && ! $corpo ) {
	return;
}

$bullet_url = get_theme_file_uri( '/assets/img/solucao-dif-bullet.svg' );
?>
<section class="spu-plat" aria-label="<?php esc_attr_e( 'Plataforma Única', 'cli' ); ?>">
	<div class="container">
		<div class="spu-plat__inner">

			<!-- Coluna de texto -->
			<div class="spu-plat__texto">

				<?php if ( $eyebrow ) : ?>
					<p class="eyebrow spu-plat__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>

				<?php if ( $titulo ) : ?>
					<h2 class="spu-plat__titulo"><?php echo esc_html( $titulo ); ?></h2>
				<?php endif; ?>

				<?php if ( $corpo ) : ?>
					<p class="spu-plat__corpo"><?php echo esc_html( $corpo ); ?></p>
				<?php endif; ?>

				<?php if ( $topicos ) : ?>
					<ul class="spu-plat__topicos">
						<?php foreach ( $topicos as $topico ) : ?>
							<li class="spu-plat__topico">
								<span
									class="spu-plat__bullet"
									aria-hidden="true"
									style="mask-image: url('<?php echo esc_url( $bullet_url ); ?>'); -webkit-mask-image: url('<?php echo esc_url( $bullet_url ); ?>');"
								></span>
								<span class="spu-plat__topico-texto"><?php echo esc_html( $topico ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

			</div><!-- .spu-plat__texto -->

			<!-- Coluna de imagem -->
			<?php if ( $imagem_id ) : ?>
				<div class="spu-plat__imagem-wrap" aria-hidden="true">
					<?php echo wp_get_attachment_image( $imagem_id, 'large', false, array( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'class'   => 'spu-plat__imagem',
						'loading' => 'lazy',
					) ); ?>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>
