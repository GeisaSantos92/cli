<?php
/**
 * Home — números da operação (+200 / 5 dias / 30 mil).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$metricas = cliconnect_lista_numerada(
	'metrica_%d_numero',
	3,
	static function ( $i, $numero ) {
		return array(
			'numero' => $numero,
			'rotulo' => cliconnect_campo( "metrica_{$i}_rotulo" ),
		);
	}
);

if ( ! $metricas ) {
	return;
}
?>

<section class="secao metricas">
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
