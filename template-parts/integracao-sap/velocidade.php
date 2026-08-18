<?php
/**
 * Integração SAP — Velocidade (comparação Sem vs Com CLI Connect).
 *
 * Layout: header centralizado + card branco com duas barras de progresso.
 * Barra cinza: 7 etapas (processo antigo). Barra azul: 3 etapas ativas.
 *
 * Campos ACF (group_cli_integracao_sap, aba "2 · Velocidade"):
 *   sap_vel_eyebrow        — eyebrow
 *   sap_vel_titulo         — título H2
 *   sap_vel_texto          — texto de apoio
 *   sap_vel_sem_label      — rótulo da linha cinza (ex.: "SEM CLI CONNECT")
 *   sap_vel_sem_tempo      — tempo da linha cinza  (ex.: "1 MÊS")
 *   sap_vel_sem_{1-7}      — etiqueta de cada etapa cinza
 *   sap_vel_com_label      — rótulo da linha azul  (ex.: "COM CLI CONNECT")
 *   sap_vel_com_tempo      — tempo da linha azul   (ex.: "5 DIAS")
 *   sap_vel_com_{1-3}      — etiqueta de cada etapa azul
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow   = cliconnect_campo_pagina( 'sap_vel_eyebrow' );
$titulo    = cliconnect_campo_pagina( 'sap_vel_titulo' );
$texto     = cliconnect_campo_pagina( 'sap_vel_texto' );
$sem_label = cliconnect_campo_pagina( 'sap_vel_sem_label' );
$sem_tempo = cliconnect_campo_pagina( 'sap_vel_sem_tempo' );
$com_label = cliconnect_campo_pagina( 'sap_vel_com_label' );
$com_tempo = cliconnect_campo_pagina( 'sap_vel_com_tempo' );

$etapas_sem = array();
for ( $i = 1; $i <= 7; $i++ ) {
	$etapas_sem[] = cliconnect_campo_pagina( 'sap_vel_sem_' . $i );
}

$etapas_com = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$etapas_com[] = cliconnect_campo_pagina( 'sap_vel_com_' . $i );
}

if ( ! $titulo ) {
	return;
}
?>
<section class="sap-velocidade">
	<div class="container sap-velocidade__inner">

		<div class="sap-velocidade__header">
			<?php if ( $eyebrow ) : ?>
				<p class="sap-velocidade__eyebrow eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<?php if ( $titulo ) : ?>
				<h2 class="sap-velocidade__titulo"><?php echo esc_html( $titulo ); ?></h2>
			<?php endif; ?>

			<?php if ( $texto ) : ?>
				<p class="sap-velocidade__subtitulo"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>
		</div>

		<div class="sap-vel-card">

			<?php if ( $sem_label || $etapas_sem ) : ?>
				<div class="sap-vel-row">
					<div class="sap-vel-row__meta">
						<?php if ( $sem_label ) : ?>
							<span class="sap-vel-row__label sap-vel-row__label--cinza">
								<?php echo esc_html( $sem_label ); ?>
							</span>
						<?php endif; ?>
						<?php if ( $sem_tempo ) : ?>
							<span class="sap-vel-row__tempo sap-vel-row__tempo--cinza">
								<?php echo esc_html( $sem_tempo ); ?>
							</span>
						<?php endif; ?>
					</div>

					<div class="sap-vel-barra sap-vel-barra--cinza" role="list" aria-label="<?php echo esc_attr( $sem_label ); ?>">
						<?php foreach ( $etapas_sem as $etapa ) : ?>
							<div class="sap-vel-etapa sap-vel-etapa--cinza" role="listitem">
								<?php echo esc_html( $etapa ); ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $com_label || $etapas_com ) : ?>
				<div class="sap-vel-row">
					<div class="sap-vel-row__meta">
						<?php if ( $com_label ) : ?>
							<span class="sap-vel-row__label sap-vel-row__label--azul">
								<?php echo esc_html( $com_label ); ?>
							</span>
						<?php endif; ?>
						<?php if ( $com_tempo ) : ?>
							<span class="sap-vel-row__tempo sap-vel-row__tempo--azul">
								<?php echo esc_html( $com_tempo ); ?>
							</span>
						<?php endif; ?>
					</div>

					<div class="sap-vel-barra sap-vel-barra--azul" role="list" aria-label="<?php echo esc_attr( $com_label ); ?>">
						<?php
						// As 3 etapas azuis + 4 colunas vazias para manter a proporção 3/7.
						$total_cols = 7;
						$ativas     = count( $etapas_com );
						foreach ( $etapas_com as $etapa ) :
							?>
							<div class="sap-vel-etapa sap-vel-etapa--ativa" role="listitem">
								<?php echo esc_html( $etapa ); ?>
							</div>
						<?php endforeach; ?>
						<?php for ( $v = $ativas; $v < $total_cols; $v++ ) : ?>
							<div class="sap-vel-etapa sap-vel-etapa--vazia" aria-hidden="true"></div>
						<?php endfor; ?>
					</div>
				</div>
			<?php endif; ?>

		</div>

	</div>
</section>
