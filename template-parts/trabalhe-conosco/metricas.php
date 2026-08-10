<?php
/**
 * Trabalhe Conosco — Métricas (13 anos / +80 clientes / 30 mil integrações).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$metricas = cliconnect_lista_numerada_pagina(
	'tc_metrica_%d_numero',
	3,
	static function ( $i, $numero ) {
		return array(
			'numero' => $numero,
			'rotulo' => cliconnect_campo_pagina( "tc_metrica_{$i}_rotulo" ),
		);
	}
);

if ( ! $metricas ) {
	return;
}
?>

<section class="secao tc-metricas">
	<div class="container">
		<div class="metricas__grid">
			<?php foreach ( $metricas as $metrica ) : ?>
				<div class="metrica">
					<p class="metrica__numero"><?php echo esc_html( $metrica['numero'] ); ?></p>
					<?php if ( $metrica['rotulo'] ) : ?>
						<p class="metrica__rotulo"><?php echo esc_html( $metrica['rotulo'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
