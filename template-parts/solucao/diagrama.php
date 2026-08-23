<?php
/**
 * Solução — Seção Diagrama.
 *
 * Título centralizado sobre uma ilustração fechada (o texto já vem embutido na
 * arte, exportada do Figma). Renderiza logo depois dos Pilares.
 *
 * Campos ACF (group_cli_solucao, aba "11 · Diagrama"):
 *   solucao_diagrama_titulo, solucao_diagrama_imagem (image ID).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo    = cliconnect_campo_pagina( 'solucao_diagrama_titulo' );
$imagem_id = (int) cliconnect_campo_pagina( 'solucao_diagrama_imagem', 0 );

if ( ! $titulo && ! $imagem_id ) {
	return;
}
?>
<section class="sdg-diagrama" aria-label="<?php esc_attr_e( 'Diagrama da solução', 'cli' ); ?>">
	<div class="container">
		<div class="sdg-diagrama__inner">

			<?php if ( $titulo ) : ?>
				<h2 class="sdg-diagrama__titulo"><?php echo esc_html( $titulo ); ?></h2>
			<?php endif; ?>

			<?php if ( $imagem_id ) : ?>
				<div class="sdg-diagrama__imagem-wrap">
					<?php echo wp_get_attachment_image( $imagem_id, 'full', false, array( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'class'   => 'sdg-diagrama__imagem',
						'loading' => 'lazy',
						'alt'     => esc_attr( $titulo ),
					) ); ?>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>
