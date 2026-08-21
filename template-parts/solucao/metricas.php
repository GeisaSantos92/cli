<?php
/**
 * Solução — Faixa de métricas.
 *
 * Tira horizontal logo abaixo do hero: até três números grandes, cada um com
 * um rótulo curto ao lado, separados por divisórias verticais.
 *
 * Campos ACF (group_cli_solucao, aba "2 · Métricas"):
 *   solucao_metrica_{1..3}_numero, solucao_metrica_{1..3}_rotulo.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$metricas = cliconnect_lista_numerada_pagina(
	'solucao_metrica_%d_numero',
	3,
	static function ( $i, $numero ) {
		return array(
			'numero' => $numero,
			'rotulo' => cliconnect_campo_pagina( "solucao_metrica_{$i}_rotulo" ),
		);
	}
);

if ( ! $metricas ) {
	return;
}
?>
<section class="sh-metricas" aria-label="<?php esc_attr_e( 'Números da solução', 'cli' ); ?>">
	<div class="container">
		<div class="sh-metricas__grid">
			<?php foreach ( $metricas as $metrica ) : ?>
				<div class="sh-metrica">
					<p class="sh-metrica__numero"><?php echo esc_html( $metrica['numero'] ); ?></p>
					<?php if ( $metrica['rotulo'] ) : ?>
						<p class="sh-metrica__rotulo"><?php echo esc_html( $metrica['rotulo'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div><!-- .sh-metricas__grid -->
	</div><!-- .container -->
</section><!-- .sh-metricas -->
