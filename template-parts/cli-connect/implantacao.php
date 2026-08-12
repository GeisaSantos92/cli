<?php
/**
 * CLI Connect — Implantação Rápida.
 *
 * Comparativo visual: "Sem CLI Connect" (barra cinza, 7 etapas, 1 mês) vs
 * "Com CLI Connect" (barra azul, 3 etapas, 5 dias).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow   = cliconnect_campo_pagina( 'cc_impl_eyebrow' );
$titulo    = cliconnect_campo_pagina( 'cc_impl_titulo' );
$titulo_2  = cliconnect_campo_pagina( 'cc_impl_titulo_2' );
$texto     = cliconnect_campo_pagina( 'cc_impl_texto' );

$sem_label = cliconnect_campo_pagina( 'cc_impl_sem_label' );
$sem_tempo = cliconnect_campo_pagina( 'cc_impl_sem_tempo' );
$com_label = cliconnect_campo_pagina( 'cc_impl_com_label' );
$com_tempo = cliconnect_campo_pagina( 'cc_impl_com_tempo' );

if ( ! $titulo ) {
	return;
}

$sem_etapas = array();
for ( $i = 1; $i <= 7; $i++ ) {
	$etapa = cliconnect_campo_pagina( "cc_impl_sem_etapa_{$i}" );
	if ( $etapa ) {
		$sem_etapas[] = $etapa;
	}
}

$com_etapas = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$etapa = cliconnect_campo_pagina( "cc_impl_com_etapa_{$i}" );
	if ( $etapa ) {
		$com_etapas[] = $etapa;
	}
}
?>
<section class="cc-impl">
	<div class="cc-impl__inner container">

		<div class="cc-impl__header">
			<?php if ( $eyebrow ) : ?>
				<p class="cc-impl__eyebrow eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<?php if ( $titulo || $titulo_2 ) : ?>
				<h2 class="cc-impl__titulo">
					<?php if ( $titulo ) : ?>
						<span class="cc-impl__titulo-linha"><?php echo esc_html( $titulo ); ?></span>
					<?php endif; ?>
					<?php if ( $titulo_2 ) : ?>
						<span class="cc-impl__titulo-linha"><?php echo esc_html( $titulo_2 ); ?></span>
					<?php endif; ?>
				</h2>
			<?php endif; ?>

			<?php if ( $texto ) : ?>
				<p class="cc-impl__texto"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>
		</div>

		<div class="cc-impl__card">

			<?php if ( $sem_etapas ) : ?>
				<div class="cc-impl__grupo">
					<div class="cc-impl__label-row">
						<?php if ( $sem_label ) : ?>
							<span class="cc-impl__label cc-impl__label--sem"><?php echo esc_html( $sem_label ); ?></span>
						<?php endif; ?>
						<span class="cc-impl__separador" aria-hidden="true"></span>
						<?php if ( $sem_tempo ) : ?>
							<span class="cc-impl__tempo cc-impl__tempo--sem"><?php echo esc_html( $sem_tempo ); ?></span>
						<?php endif; ?>
					</div>
					<div class="cc-impl__barra cc-impl__barra--sem">
						<?php foreach ( $sem_etapas as $etapa ) : ?>
							<div class="cc-impl__etapa"><?php echo esc_html( $etapa ); ?></div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $com_etapas ) : ?>
				<div class="cc-impl__grupo">
					<div class="cc-impl__label-row">
						<?php if ( $com_label ) : ?>
							<span class="cc-impl__label cc-impl__label--com"><?php echo esc_html( $com_label ); ?></span>
						<?php endif; ?>
						<span class="cc-impl__separador cc-impl__separador--com" aria-hidden="true"></span>
						<?php if ( $com_tempo ) : ?>
							<span class="cc-impl__tempo cc-impl__tempo--com"><?php echo esc_html( $com_tempo ); ?></span>
						<?php endif; ?>
					</div>
					<div class="cc-impl__barra cc-impl__barra--com">
						<div class="cc-impl__barra-azul">
							<?php foreach ( $com_etapas as $etapa ) : ?>
								<div class="cc-impl__etapa"><?php echo esc_html( $etapa ); ?></div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>
