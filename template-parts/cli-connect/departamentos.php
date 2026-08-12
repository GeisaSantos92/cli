<?php
/**
 * CLI Connect — Departamentos (accordion).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo = cliconnect_campo_pagina( 'cc_dep_secao_titulo' );
$texto  = cliconnect_campo_pagina( 'cc_dep_secao_texto' );

if ( ! $titulo ) {
	return;
}

$itens = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$item_titulo = cliconnect_campo_pagina( "cc_dep_{$i}_titulo" );
	if ( ! $item_titulo ) {
		continue;
	}
	$itens[] = array(
		'numero' => cliconnect_campo_pagina( "cc_dep_{$i}_numero" ),
		'titulo' => $item_titulo,
		'texto'  => cliconnect_campo_pagina( "cc_dep_{$i}_texto" ),
	);
}
?>
<section class="cc-departamentos secao">
	<div class="container cc-departamentos__inner">
		<div class="cc-departamentos__esquerda">
			<h2 class="cc-departamentos__titulo"><?php echo esc_html( $titulo ); ?></h2>
			<?php if ( $texto ) : ?>
				<p class="cc-departamentos__texto"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $itens ) : ?>
			<ul class="cc-departamentos__lista">
				<?php foreach ( $itens as $item ) : ?>
					<li class="cc-departamentos__item">
						<?php if ( $item['numero'] ) : ?>
							<span class="cc-departamentos__numero"><?php echo esc_html( $item['numero'] ); ?></span>
						<?php endif; ?>
						<div class="cc-departamentos__detalhe">
							<strong class="cc-departamentos__item-titulo"><?php echo esc_html( $item['titulo'] ); ?></strong>
							<?php if ( $item['texto'] ) : ?>
								<p class="cc-departamentos__item-texto"><?php echo esc_html( $item['texto'] ); ?></p>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
