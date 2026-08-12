<?php
/**
 * CLI Connect — AgentStudio.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'cc_agentes_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'cc_agentes_titulo' );
$texto   = cliconnect_campo_pagina( 'cc_agentes_texto' );

if ( ! $titulo ) {
	return;
}

$cards = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$card_titulo = cliconnect_campo_pagina( "cc_agente_{$i}_titulo" );
	if ( ! $card_titulo ) {
		continue;
	}
	$itens = array();
	for ( $j = 1; $j <= 3; $j++ ) {
		$item = cliconnect_campo_pagina( "cc_agente_{$i}_item_{$j}" );
		if ( $item ) {
			$itens[] = $item;
		}
	}
	$cards[] = array(
		'titulo' => $card_titulo,
		'texto'  => cliconnect_campo_pagina( "cc_agente_{$i}_texto" ),
		'itens'  => $itens,
	);
}
?>
<section class="cc-agentes secao">
	<div class="container">
		<div class="cc-agentes__cabecalho">
			<?php if ( $eyebrow ) : ?>
				<p class="cc-agentes__eyebrow eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<h2 class="cc-agentes__titulo"><?php echo esc_html( $titulo ); ?></h2>

			<?php if ( $texto ) : ?>
				<p class="cc-agentes__texto"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $cards ) : ?>
			<div class="cc-agentes__cards">
				<?php foreach ( $cards as $card ) : ?>
					<article class="cc-agentes__card">
						<div class="cc-agentes__card-texto">
							<h3 class="cc-agentes__card-titulo"><?php echo esc_html( $card['titulo'] ); ?></h3>
							<?php if ( $card['texto'] ) : ?>
								<p class="cc-agentes__card-corpo"><?php echo esc_html( $card['texto'] ); ?></p>
							<?php endif; ?>
						</div>

						<?php if ( $card['itens'] ) : ?>
							<ul class="cc-agentes__card-lista">
								<?php foreach ( $card['itens'] as $item ) : ?>
									<li class="cc-agentes__card-item"><?php echo esc_html( $item ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
