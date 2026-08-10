<?php
/**
 * Trabalhe Conosco — Valores (5 cards + card CTA azul).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'valores_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'valores_titulo' );
$cta     = cliconnect_campo_pagina( 'valores_cta', array() );

$valores = array();
for ( $i = 1; $i <= 5; $i++ ) {
	$icone = cliconnect_campo_pagina( "valor_{$i}_icone" );
	$nome  = cliconnect_campo_pagina( "valor_{$i}_titulo" );
	$texto = cliconnect_campo_pagina( "valor_{$i}_texto" );

	if ( $nome ) {
		$valores[] = compact( 'icone', 'nome', 'texto' );
	}
}

if ( ! $titulo && ! $valores ) {
	return;
}
?>

<section class="secao tc-valores">
	<div class="container">

		<header class="secao__cabecalho">
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<?php if ( $titulo ) : ?>
				<h2 class="secao__titulo"><?php echo esc_html( $titulo ); ?></h2>
			<?php endif; ?>
		</header>

		<div class="tc-valores__grade">
			<?php foreach ( $valores as $valor ) : ?>
				<article class="tc-valor">
					<?php if ( $valor['icone'] ) : ?>
						<span class="tc-valor__icone">
							<?php echo cliconnect_icone( $valor['icone'], 24 ); // SVG estático de lista fechada. ?>
						</span>
					<?php endif; ?>
					<h3 class="tc-valor__titulo"><?php echo esc_html( $valor['nome'] ); ?></h3>
					<?php if ( $valor['texto'] ) : ?>
						<p class="tc-valor__texto"><?php echo esc_html( $valor['texto'] ); ?></p>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>

			<?php if ( ! empty( $cta['url'] ) && ! empty( $cta['title'] ) ) : ?>
				<a
					class="tc-valores__cta"
					href="<?php echo esc_url( $cta['url'] ); ?>"
					<?php echo ( '_blank' === ( $cta['target'] ?? '' ) ) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
				>
					<?php echo esc_html( $cta['title'] ); ?>
					<?php echo cliconnect_icone( 'seta-direita', 20 ); // SVG estático. ?>
				</a>
			<?php endif; ?>
		</div>

	</div>
</section>
