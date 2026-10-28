<?php
/**
 * Programa de Parceiros — Parceria ("Expanda sua carteira de cliente…").
 *
 * Cabeçalho centralizado + três cartões com ícone (Material Symbols).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'pp_parceria_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'pp_parceria_titulo' );
$texto   = cliconnect_campo_pagina( 'pp_parceria_texto' );

$cartoes = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$titulo_cartao = cliconnect_campo_pagina( "pp_parceria_{$i}_titulo" );

	if ( $titulo_cartao ) {
		$cartoes[] = array(
			'icone'  => cliconnect_campo_pagina( "pp_parceria_{$i}_icone" ),
			'titulo' => $titulo_cartao,
			'texto'  => cliconnect_campo_pagina( "pp_parceria_{$i}_texto" ),
		);
	}
}

if ( ! $titulo && ! $cartoes ) {
	return;
}
?>
<section class="secao pp-parceria">
	<div class="container">
		<header class="pp-cabecalho">
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<?php if ( $titulo ) : ?>
				<h2 class="pp-cabecalho__titulo pp-parceria__titulo"><?php echo esc_html( $titulo ); ?></h2>
			<?php endif; ?>
			<?php if ( $texto ) : ?>
				<p class="pp-cabecalho__texto"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( $cartoes ) : ?>
			<ul class="pp-parceria__cartoes">
				<?php foreach ( $cartoes as $cartao ) : ?>
					<li class="pp-parceria__cartao">
						<?php if ( $cartao['icone'] ) : ?>
							<span class="pp-parceria__icone"><?php echo cliconnect_icone_ms( $cartao['icone'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_icone_ms escapa a ligatura. ?></span>
						<?php endif; ?>
						<h3 class="pp-parceria__cartao-titulo"><?php echo esc_html( $cartao['titulo'] ); ?></h3>
						<?php if ( $cartao['texto'] ) : ?>
							<p class="pp-parceria__cartao-texto"><?php echo esc_html( $cartao['texto'] ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
