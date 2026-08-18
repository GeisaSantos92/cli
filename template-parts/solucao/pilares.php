<?php
/**
 * Solução — Seção Pilares.
 *
 * Campos ACF (group_cli_solucao, aba "2 · Pilares"):
 *   solucao_pilares_titulo,
 *   solucao_pilares_{1,2,3}_icone (image ID), solucao_pilares_{1,2,3}_titulo,
 *   solucao_pilares_{1,2,3}_desc.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo = cliconnect_campo_pagina( 'solucao_pilares_titulo' );

$cards = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$icone_id = cliconnect_campo_pagina( "solucao_pilares_{$i}_icone", 0 );
	$card_tit = cliconnect_campo_pagina( "solucao_pilares_{$i}_titulo" );
	$card_desc = cliconnect_campo_pagina( "solucao_pilares_{$i}_desc" );

	if ( $card_tit || $card_desc ) {
		$cards[] = array(
			'icone_id' => (int) $icone_id,
			'titulo'   => $card_tit,
			'desc'     => $card_desc,
		);
	}
}

if ( ! $titulo && empty( $cards ) ) {
	return;
}
?>
<section class="sp-pilares" aria-label="<?php esc_attr_e( 'Pilares', 'cli' ); ?>">
	<div class="container">
		<div class="sp-pilares__inner">

			<?php if ( $titulo ) : ?>
				<h2 class="sp-pilares__titulo"><?php echo esc_html( $titulo ); ?></h2>
			<?php endif; ?>

			<?php if ( $cards ) : ?>
				<div class="sp-pilares__grid">
					<?php foreach ( $cards as $card ) : ?>
						<div class="sp-pilares__card">

							<div class="sp-pilares__icone-wrap" aria-hidden="true">
								<?php if ( $card['icone_id'] ) :
									$icone_url = wp_get_attachment_image_url( $card['icone_id'], 'full' );
									if ( $icone_url ) : ?>
										<span class="sp-pilares__icone" style="mask-image: url('<?php echo esc_url( $icone_url ); ?>'); -webkit-mask-image: url('<?php echo esc_url( $icone_url ); ?>');"></span>
									<?php endif;
								endif; ?>
							</div>

							<?php if ( $card['titulo'] ) : ?>
								<h3 class="sp-pilares__card-titulo"><?php echo esc_html( $card['titulo'] ); ?></h3>
							<?php endif; ?>

							<?php if ( $card['desc'] ) : ?>
								<p class="sp-pilares__card-desc"><?php echo esc_html( $card['desc'] ); ?></p>
							<?php endif; ?>

						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>
