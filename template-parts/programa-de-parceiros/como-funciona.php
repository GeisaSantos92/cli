<?php
/**
 * Programa de Parceiros — Como funciona.
 *
 * Quatro passos numerados, cada um com título, texto e ilustração na base do
 * cartão. O id `como-funciona` é o alvo do botão secundário do hero.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo = cliconnect_campo_pagina( 'pp_como_titulo' );

$passos = array();
for ( $i = 1; $i <= 4; $i++ ) {
	$titulo_passo = cliconnect_campo_pagina( "pp_como_{$i}_titulo" );

	if ( $titulo_passo ) {
		$passos[] = array(
			'titulo' => $titulo_passo,
			'texto'  => cliconnect_campo_pagina( "pp_como_{$i}_texto" ),
			'imagem' => absint( cliconnect_campo_pagina( "pp_como_{$i}_imagem", 0 ) ),
		);
	}
}

if ( ! $passos ) {
	return;
}
?>
<section class="secao pp-como" id="como-funciona">
	<div class="container">
		<?php if ( $titulo ) : ?>
			<h2 class="pp-cabecalho__titulo pp-como__titulo"><?php echo esc_html( $titulo ); ?></h2>
		<?php endif; ?>

		<ol class="pp-como__passos">
			<?php foreach ( $passos as $indice => $passo ) : ?>
				<li class="pp-como__passo">
					<div class="pp-como__corpo">
						<span class="pp-como__numero"><?php echo esc_html( sprintf( '%02d', $indice + 1 ) ); ?></span>
						<h3 class="pp-como__passo-titulo"><?php echo esc_html( $passo['titulo'] ); ?></h3>
						<?php if ( $passo['texto'] ) : ?>
							<p class="pp-como__passo-texto"><?php echo esc_html( $passo['texto'] ); ?></p>
						<?php endif; ?>
					</div>
					<?php if ( $passo['imagem'] ) : ?>
						<div class="pp-como__imagem">
							<?php
							echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapa internamente.
								$passo['imagem'],
								'medium_large',
								false,
								array(
									'alt'     => '',
									'loading' => 'lazy',
								)
							);
							?>
						</div>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
