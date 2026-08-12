<?php
/**
 * CLI Connect — Operações Críticas.
 *
 * Layout 2 colunas: texto + bullets à esquerda, ilustração estática à direita.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow   = cliconnect_campo_pagina( 'cc_operacoes_eyebrow' );
$titulo    = cliconnect_campo_pagina( 'cc_operacoes_titulo' );
$titulo_2  = cliconnect_campo_pagina( 'cc_operacoes_titulo_2' );
$texto     = cliconnect_campo_pagina( 'cc_operacoes_texto' );

$bullets = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$bullet = cliconnect_campo_pagina( "cc_operacoes_bullet_{$i}" );
	if ( $bullet ) {
		$bullets[] = $bullet;
	}
}

$ilustracao = cliconnect_imagem_tema(
	'cc-operacoes.png',
	array(
		'class'  => 'cc-operacoes__img',
		'alt'    => __( 'Ilustração de operações críticas protegidas pela CLI Connect', 'cli' ),
		'width'  => 640,
		'height' => 427,
	)
);

if ( ! $titulo && ! $ilustracao ) {
	return;
}
?>
<section class="cc-operacoes">
	<div class="cc-operacoes__inner container">

		<div class="cc-operacoes__texto">
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>

			<?php if ( $titulo || $titulo_2 ) : ?>
				<h2 class="cc-operacoes__titulo">
					<?php if ( $titulo ) : ?>
						<span class="cc-operacoes__titulo-linha"><?php echo esc_html( $titulo ); ?></span>
					<?php endif; ?>
					<?php if ( $titulo_2 ) : ?>
						<span class="cc-operacoes__titulo-linha"><?php echo esc_html( $titulo_2 ); ?></span>
					<?php endif; ?>
				</h2>
			<?php endif; ?>

			<?php if ( $texto ) : ?>
				<p class="cc-operacoes__corpo"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>

			<?php if ( $bullets ) : ?>
				<ul class="cc-operacoes__bullets">
					<?php foreach ( $bullets as $bullet ) : ?>
						<li class="cc-operacoes__bullet"><?php echo esc_html( $bullet ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<?php if ( $ilustracao ) : ?>
			<div class="cc-operacoes__visual">
				<?php echo $ilustracao; // phpcs:ignore WordPress.Security.EscapeOutput -- montado com escape em cliconnect_imagem_tema(). ?>
			</div>
		<?php endif; ?>

	</div>
</section>
