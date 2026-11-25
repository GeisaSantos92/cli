<?php
/**
 * CLI Flow — Preço ("Tudo incluso. Com custo previsível.").
 *
 * Fundo azul-claro, cabeçalho centralizado (linha escura + linha azul +
 * texto) e até 3 cards numerados (01, 02, 03) com título e texto.
 *
 * Campos ACF (group_cli_cli_flow, aba "15 · Preço"):
 *   cf_preco_titulo / cf_preco_titulo_destaque / cf_preco_texto
 *   cf_preco_{1-3}_titulo / _texto
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo   = cliconnect_campo_pagina( 'cf_preco_titulo' );
$destaque = cliconnect_campo_pagina( 'cf_preco_titulo_destaque' );
$texto    = cliconnect_campo_pagina( 'cf_preco_texto' );

if ( ! $titulo && ! $destaque ) {
	return;
}

$cards = cliconnect_lista_numerada_pagina(
	'cf_preco_%d_titulo',
	3,
	static function ( $i, $card_titulo ) {
		return array(
			'titulo' => $card_titulo,
			'texto'  => cliconnect_campo_pagina( "cf_preco_{$i}_texto" ),
		);
	}
);
?>
<section class="cf-preco">
	<div class="container">

		<header class="cf-preco__header">
			<h2 class="cf-preco__titulo">
				<?php if ( $titulo ) : ?>
					<span class="cf-preco__titulo-linha"><?php echo esc_html( $titulo ); ?></span>
				<?php endif; ?>
				<?php if ( $destaque ) : ?>
					<span class="cf-preco__titulo-linha cf-preco__titulo-linha--destaque"><?php echo esc_html( $destaque ); ?></span>
				<?php endif; ?>
			</h2>
			<?php if ( $texto ) : ?>
				<p class="cf-preco__texto"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( $cards ) : ?>
			<ol class="cf-preco__grid" role="list">
				<?php foreach ( $cards as $n => $card ) : ?>
					<li class="cf-preco__card">
						<span class="cf-preco__numero" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $n + 1 ) ); ?></span>
						<h3 class="cf-preco__card-titulo"><?php echo esc_html( $card['titulo'] ); ?></h3>
						<?php if ( $card['texto'] ) : ?>
							<p class="cf-preco__card-texto"><?php echo esc_html( $card['texto'] ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>

	</div>
</section>
