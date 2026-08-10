<?php
/**
 * Trabalhe Conosco — O jeito CLI de ser (lista + texto explicativo).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo = cliconnect_campo_pagina( 'jeito_titulo' );
$texto  = cliconnect_campo_pagina( 'jeito_texto' );

$itens = array();
for ( $i = 1; $i <= 5; $i++ ) {
	$item = cliconnect_campo_pagina( "jeito_item_{$i}_titulo" );
	if ( $item ) {
		$itens[] = $item;
	}
}

if ( ! $titulo && ! $itens ) {
	return;
}
?>

<section class="secao tc-jeito">
	<div class="container tc-jeito__inner">

		<div class="tc-jeito__esquerda">
			<?php if ( $titulo ) : ?>
				<h2 class="tc-jeito__titulo"><?php echo esc_html( $titulo ); ?></h2>
			<?php endif; ?>

			<?php if ( $itens ) : ?>
				<ul class="tc-jeito__lista">
					<?php foreach ( $itens as $item ) : ?>
						<li class="tc-jeito__item">
							<span class="tc-jeito__marcador" aria-hidden="true">
								<?php echo cliconnect_icone( 'verificado', 20 ); // SVG estático. ?>
							</span>
							<?php echo esc_html( $item ); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<div class="tc-jeito__direita">
			<?php if ( $texto ) : ?>
				<p class="tc-jeito__texto"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>
			<?php cliconnect_botao_pagina( 'jeito_botao', 'botao botao--primario' ); ?>
		</div>

	</div>
</section>
