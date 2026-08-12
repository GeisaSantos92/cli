<?php
/**
 * CLI Connect — Métricas.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$itens = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$numero = cliconnect_campo_pagina( "cc_metrica_{$i}_numero" );
	$rotulo = cliconnect_campo_pagina( "cc_metrica_{$i}_rotulo" );
	if ( $numero ) {
		$itens[] = array(
			'numero' => $numero,
			'rotulo' => $rotulo,
		);
	}
}

if ( ! $itens ) {
	return;
}
?>
<section class="cc-metricas secao">
	<div class="container">
		<ul class="cc-metricas__lista">
			<?php foreach ( $itens as $item ) : ?>
				<li class="cc-metricas__item">
					<strong class="cc-metricas__numero"><?php echo esc_html( $item['numero'] ); ?></strong>
					<?php if ( $item['rotulo'] ) : ?>
						<span class="cc-metricas__rotulo"><?php echo esc_html( $item['rotulo'] ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
