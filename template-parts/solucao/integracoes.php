<?php
/**
 * Solução — Seção Integrações.
 *
 * Eyebrow + título centralizado + imagem de logos com fade na base + subtítulo.
 * Renderiza logo depois do Diagrama; retorna cedo se os campos estiverem vazios.
 *
 * Campos ACF (group_cli_solucao, aba "12 · Integrações"):
 *   solucao_int_eyebrow, solucao_int_titulo,
 *   solucao_int_imagem (image ID), solucao_int_subtitulo.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow   = cliconnect_campo_pagina( 'solucao_int_eyebrow' );
$titulo    = cliconnect_campo_pagina( 'solucao_int_titulo' );
$imagem_id = (int) cliconnect_campo_pagina( 'solucao_int_imagem', 0 );
$subtitulo = cliconnect_campo_pagina( 'solucao_int_subtitulo' );

if ( ! $titulo && ! $imagem_id ) {
	return;
}
?>
<section class="si-integracoes" aria-label="<?php esc_attr_e( 'Integrações', 'cli' ); ?>">
	<div class="container">
		<div class="si-integracoes__inner">

			<?php if ( $eyebrow || $titulo ) : ?>
				<div class="si-integracoes__header">
					<?php if ( $eyebrow ) : ?>
						<p class="eyebrow si-integracoes__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
					<?php endif; ?>

					<?php if ( $titulo ) : ?>
						<h2 class="si-integracoes__titulo"><?php echo esc_html( $titulo ); ?></h2>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $imagem_id ) : ?>
				<div class="si-integracoes__imagem-wrap" aria-hidden="true">
					<?php echo wp_get_attachment_image( $imagem_id, 'full', false, array( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'class'   => 'si-integracoes__imagem',
						'loading' => 'lazy',
						'alt'     => '',
					) ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $subtitulo ) : ?>
				<p class="si-integracoes__subtitulo"><?php echo esc_html( $subtitulo ); ?></p>
			<?php endif; ?>

		</div>
	</div>
</section>
