<?php
/**
 * CLI Flow — Economia ("Até 80% menos licenças nos seus grandes sistemas").
 *
 * Cabeçalho centralizado (linha 1 azul, linha 2 escura, texto) e um cartão
 * com a comparação em barras: "sem CLI Flow" (barra cheia em degradê) e
 * "com CLI Flow" (a parte que ainda precisa de licença, em azul, e o resto
 * da barra com quem passa a usar o CLI Flow).
 *
 * Campos ACF (group_cli_cli_flow, aba "13 · Economia"):
 *   cf_eco_titulo (linha 1, azul) / cf_eco_titulo_destaque (linha 2) / cf_eco_texto
 *   cf_eco_card_titulo
 *   cf_eco_sem_label / cf_eco_sem_valor
 *   cf_eco_com_label / cf_eco_com_valor / cf_eco_com_percentual / cf_eco_com_barra
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$linha_1 = cliconnect_campo_pagina( 'cf_eco_titulo' );
$linha_2 = cliconnect_campo_pagina( 'cf_eco_titulo_destaque' );

if ( ! $linha_1 && ! $linha_2 ) {
	return;
}

$texto       = cliconnect_campo_pagina( 'cf_eco_texto' );
$card_titulo = cliconnect_campo_pagina( 'cf_eco_card_titulo' );
$sem         = array(
	'label' => cliconnect_campo_pagina( 'cf_eco_sem_label' ),
	'valor' => cliconnect_campo_pagina( 'cf_eco_sem_valor' ),
);
$com         = array(
	'label' => cliconnect_campo_pagina( 'cf_eco_com_label' ),
	'valor' => cliconnect_campo_pagina( 'cf_eco_com_valor' ),
	'barra' => cliconnect_campo_pagina( 'cf_eco_com_barra' ),
);
$percentual  = min( 100, max( 0, (int) cliconnect_campo_pagina( 'cf_eco_com_percentual' ) ) );
?>
<section class="cf-economia">
	<div class="container">

		<header class="cf-economia__header">
			<h2 class="cf-economia__titulo">
				<?php if ( $linha_1 ) : ?>
					<span class="cf-economia__titulo-linha cf-economia__titulo-linha--destaque"><?php echo esc_html( $linha_1 ); ?></span>
				<?php endif; ?>
				<?php if ( $linha_2 ) : ?>
					<span class="cf-economia__titulo-linha"><?php echo esc_html( $linha_2 ); ?></span>
				<?php endif; ?>
			</h2>
			<?php if ( $texto ) : ?>
				<p class="cf-economia__texto"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( $sem['label'] || $com['label'] ) : ?>
			<div class="cf-economia__card">
				<?php if ( $card_titulo ) : ?>
					<h3 class="cf-economia__card-titulo"><?php echo esc_html( $card_titulo ); ?></h3>
				<?php endif; ?>

				<?php if ( $sem['label'] ) : ?>
					<div class="cf-economia__linha">
						<p class="cf-economia__rotulos">
							<span class="cf-economia__rotulo"><?php echo esc_html( $sem['label'] ); ?></span>
							<span class="cf-economia__valor"><?php echo esc_html( $sem['valor'] ); ?></span>
						</p>
						<div class="cf-economia__barra cf-economia__barra--cheia" aria-hidden="true"></div>
					</div>
				<?php endif; ?>

				<?php if ( $com['label'] ) : ?>
					<div class="cf-economia__linha">
						<p class="cf-economia__rotulos">
							<span class="cf-economia__rotulo"><?php echo esc_html( $com['label'] ); ?></span>
							<span class="cf-economia__valor"><?php echo esc_html( $com['valor'] ); ?></span>
						</p>
						<div class="cf-economia__barra cf-economia__barra--parcial" style="--cf-preenchido: <?php echo esc_attr( (string) $percentual ); ?>%">
							<span class="cf-economia__barra-preenchida" aria-hidden="true"></span>
							<?php if ( $com['barra'] ) : ?>
								<span class="cf-economia__barra-texto"><?php echo esc_html( $com['barra'] ); ?></span>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

	</div>
</section>
