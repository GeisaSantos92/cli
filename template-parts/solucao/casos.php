<?php
/**
 * Solução — Seção Casos de Uso.
 *
 * Campos ACF (group_cli_solucao, aba "5 · Casos de Uso"):
 *   solucao_casos_eyebrow, solucao_casos_titulo,
 *   solucao_casos_{1-6}_icone (image ID), solucao_casos_{1-6}_titulo,
 *   solucao_casos_{1-6}_desc,
 *   solucao_casos_cta_texto, solucao_casos_cta_url.
 *
 * O card CTA azul (último) só é exibido quando solucao_casos_cta_texto
 * estiver preenchido. Sem CTA, os seis cards fecham duas linhas de três.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'solucao_casos_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'solucao_casos_titulo' );

$cards = array();
for ( $i = 1; $i <= 6; $i++ ) {
	$card_tit  = cliconnect_campo_pagina( "solucao_casos_{$i}_titulo" );
	$card_desc = cliconnect_campo_pagina( "solucao_casos_{$i}_desc" );

	if ( $card_tit || $card_desc ) {
		$cards[] = array(
			'icone_id' => (int) cliconnect_campo_pagina( "solucao_casos_{$i}_icone", 0 ),
			'titulo'   => $card_tit,
			'desc'     => $card_desc,
		);
	}
}

$cta_texto = cliconnect_campo_pagina( 'solucao_casos_cta_texto' );
$cta_url   = cliconnect_campo_pagina( 'solucao_casos_cta_url' );

if ( ! $titulo && empty( $cards ) && ! $cta_texto ) {
	return;
}

$arrow_url = get_theme_file_uri( '/assets/img/solucao-casos-arrow.svg' );
?>
<section class="sc-casos" aria-label="<?php esc_attr_e( 'Casos de Uso', 'cli' ); ?>">
	<div class="container">
		<div class="sc-casos__inner">

			<div class="sc-casos__header">
				<?php if ( $eyebrow ) : ?>
					<p class="eyebrow sc-casos__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>

				<?php if ( $titulo ) : ?>
					<h2 class="sc-casos__titulo"><?php echo nl2br( esc_html( $titulo ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
				<?php endif; ?>
			</div>

			<?php if ( $cards || $cta_texto ) : ?>
				<div class="sc-casos__grid">

					<?php foreach ( $cards as $card ) : ?>
						<div class="sc-casos__card">

							<div class="sc-casos__icone-wrap" aria-hidden="true">
								<?php if ( $card['icone_id'] ) :
									$icone_url = wp_get_attachment_image_url( $card['icone_id'], 'full' );
									if ( $icone_url ) : ?>
										<span class="sc-casos__icone" style="mask-image: url('<?php echo esc_url( $icone_url ); ?>'); -webkit-mask-image: url('<?php echo esc_url( $icone_url ); ?>');"></span>
									<?php endif;
								endif; ?>
							</div>

							<?php if ( $card['titulo'] ) : ?>
								<h3 class="sc-casos__card-titulo"><?php echo esc_html( $card['titulo'] ); ?></h3>
							<?php endif; ?>

							<?php if ( $card['desc'] ) : ?>
								<p class="sc-casos__card-desc"><?php echo esc_html( $card['desc'] ); ?></p>
							<?php endif; ?>

						</div>
					<?php endforeach; ?>

					<?php if ( $cta_texto ) : ?>
						<?php $tag = $cta_url ? 'a' : 'div'; ?>
						<<?php echo esc_attr( $tag ); ?>
							class="sc-casos__card sc-casos__card--cta"
							<?php if ( $cta_url ) : ?>
								href="<?php echo esc_url( $cta_url ); ?>"
							<?php endif; ?>
						>
							<p class="sc-casos__cta-texto"><?php echo esc_html( $cta_texto ); ?></p>
							<span
								class="sc-casos__cta-arrow"
								aria-hidden="true"
								style="mask-image: url('<?php echo esc_url( $arrow_url ); ?>'); -webkit-mask-image: url('<?php echo esc_url( $arrow_url ); ?>');"
							></span>
						</<?php echo esc_attr( $tag ); ?>>
					<?php endif; ?>

				</div>
			<?php endif; ?>

		</div>
	</div>
</section>
