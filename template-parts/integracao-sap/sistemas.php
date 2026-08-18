<?php
/**
 * Integração SAP — Sistemas conectáveis.
 *
 * Header centralizado + grid de cards com os sistemas que o SAP conecta.
 *
 * Campos ACF (group_cli_integracao_sap, aba "7 · Sistemas"):
 *   sap_sis_titulo     — título H2 (2 linhas, quebra com \n)
 *   sap_sis_subtitulo  — subtítulo
 *   sap_sis_{1-10}     — nome de cada sistema
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo    = cliconnect_campo_pagina( 'sap_sis_titulo' );
$subtitulo = cliconnect_campo_pagina( 'sap_sis_subtitulo' );

$sistemas = array();
for ( $i = 1; $i <= 10; $i++ ) {
	$val = cliconnect_campo_pagina( 'sap_sis_' . $i );
	if ( $val ) {
		$sistemas[] = $val;
	}
}

if ( ! $titulo ) {
	return;
}
?>
<section class="sap-sistemas">
	<div class="container sap-sistemas__inner">

		<div class="sap-sistemas__header">
			<h2 class="sap-sistemas__titulo"><?php echo esc_html( $titulo ); ?></h2>

			<?php if ( $subtitulo ) : ?>
				<p class="sap-sistemas__subtitulo"><?php echo esc_html( $subtitulo ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $sistemas ) : ?>
			<ul class="sap-sistemas__grid" role="list">
				<?php foreach ( $sistemas as $sistema ) : ?>
					<li class="sap-sistemas__card"><?php echo esc_html( $sistema ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>
</section>
