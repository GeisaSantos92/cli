<?php
/**
 * Solução — Seção Diferencial Técnico.
 *
 * Fundo branco (var(--cor-fundo)), diferente das outras seções.
 *
 * Campos ACF (group_cli_solucao, aba "7 · Diferencial"):
 *   solucao_dif_eyebrow, solucao_dif_titulo, solucao_dif_corpo,
 *   solucao_dif_topico_{1,2,3}, solucao_dif_imagem (image ID).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow  = cliconnect_campo_pagina( 'solucao_dif_eyebrow' );
$titulo   = cliconnect_campo_pagina( 'solucao_dif_titulo' );
$corpo    = cliconnect_campo_pagina( 'solucao_dif_corpo' );
$imagem_id = (int) cliconnect_campo_pagina( 'solucao_dif_imagem', 0 );

$topicos = array_filter( array(
	cliconnect_campo_pagina( 'solucao_dif_topico_1' ),
	cliconnect_campo_pagina( 'solucao_dif_topico_2' ),
	cliconnect_campo_pagina( 'solucao_dif_topico_3' ),
) );

if ( ! $titulo && ! $corpo ) {
	return;
}

$bullet_url = get_theme_file_uri( '/assets/img/solucao-dif-bullet.svg' );
?>
<section class="sd-dif" aria-label="<?php esc_attr_e( 'Diferencial Técnico', 'cli' ); ?>">
	<div class="container">
		<div class="sd-dif__inner">

			<!-- Coluna de texto -->
			<div class="sd-dif__texto">

				<?php if ( $eyebrow ) : ?>
					<p class="eyebrow sd-dif__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>

				<?php if ( $titulo ) : ?>
					<h2 class="sd-dif__titulo"><?php echo esc_html( $titulo ); ?></h2>
				<?php endif; ?>

				<?php if ( $corpo ) : ?>
					<p class="sd-dif__corpo"><?php echo esc_html( $corpo ); ?></p>
				<?php endif; ?>

				<?php if ( $topicos ) : ?>
					<ul class="sd-dif__topicos">
						<?php foreach ( $topicos as $topico ) : ?>
							<li class="sd-dif__topico">
								<span
									class="sd-dif__bullet"
									aria-hidden="true"
									style="mask-image: url('<?php echo esc_url( $bullet_url ); ?>'); -webkit-mask-image: url('<?php echo esc_url( $bullet_url ); ?>');"
								></span>
								<span class="sd-dif__topico-texto"><?php echo esc_html( $topico ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

			</div><!-- .sd-dif__texto -->

			<!-- Coluna de imagem -->
			<?php if ( $imagem_id ) : ?>
				<div class="sd-dif__imagem-wrap" aria-hidden="true">
					<?php echo wp_get_attachment_image( $imagem_id, 'large', false, array( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'class'   => 'sd-dif__imagem',
						'loading' => 'lazy',
					) ); ?>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>
