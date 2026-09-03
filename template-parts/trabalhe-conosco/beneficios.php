<?php
/**
 * Trabalhe Conosco — Benefícios (6 cards).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow   = cliconnect_campo_pagina( 'beneficios_eyebrow' );
$titulo    = cliconnect_campo_pagina( 'beneficios_titulo' );
$subtitulo = cliconnect_campo_pagina( 'beneficios_subtitulo' );

$beneficios = array();
for ( $i = 1; $i <= 6; $i++ ) {
	$icone = cliconnect_campo_pagina( "beneficio_{$i}_icone" );
	$nome  = cliconnect_campo_pagina( "beneficio_{$i}_titulo" );
	$texto = cliconnect_campo_pagina( "beneficio_{$i}_texto" );

	if ( $nome ) {
		$beneficios[] = compact( 'icone', 'nome', 'texto' );
	}
}

if ( ! $titulo && ! $beneficios ) {
	return;
}
?>

<section class="secao tc-beneficios">
	<div class="container">

		<header class="secao__cabecalho">
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<?php if ( $titulo ) : ?>
				<h2 class="secao__titulo"><?php echo esc_html( $titulo ); ?></h2>
			<?php endif; ?>
			<?php if ( $subtitulo ) : ?>
				<p class="secao__subtitulo"><?php echo esc_html( $subtitulo ); ?></p>
			<?php endif; ?>
		</header>

		<div class="tc-beneficios__grade">
			<?php foreach ( $beneficios as $beneficio ) : ?>
				<article class="tc-beneficio">
					<?php if ( $beneficio['icone'] ) : ?>
						<span class="tc-beneficio__icone">
							<?php echo cliconnect_icone_ms( $beneficio['icone'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_icone_ms retorna HTML seguro de ícone Material Symbols. ?>
						</span>
					<?php endif; ?>
					<h3 class="tc-beneficio__titulo"><?php echo esc_html( $beneficio['nome'] ); ?></h3>
					<?php if ( $beneficio['texto'] ) : ?>
						<p class="tc-beneficio__texto"><?php echo esc_html( $beneficio['texto'] ); ?></p>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>

	</div>
</section>
