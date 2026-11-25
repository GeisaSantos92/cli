<?php
/**
 * CLI Flow — Cards de funcionalidades.
 *
 * Grid de 4 colunas logo abaixo da faixa "Não achou o seu?": ícone em
 * quadrado azul degradê + título + texto. Até 6 cards (os excedentes descem
 * para a linha de baixo).
 * Sem texto fixo: todo conteúdo vem do ACF (aba "8 · Cards").
 *
 * Campos ACF (group_cli_cli_flow):
 *   cf_cards_titulo        — título da seção (opcional)
 *   cf_cards_{1-6}_icone   — ligatura do Material Symbols
 *   cf_cards_{1-6}_titulo  — título do card
 *   cf_cards_{1-6}_texto   — texto descritivo do card
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo_secao = cliconnect_campo_pagina( 'cf_cards_titulo' );

$cards = cliconnect_lista_numerada_pagina(
	'cf_cards_%d_titulo',
	6,
	static function ( $i, $card_titulo ) {
		return array(
			'titulo' => $card_titulo,
			'texto'  => cliconnect_campo_pagina( "cf_cards_{$i}_texto" ),
			'icone'  => cliconnect_campo_pagina( "cf_cards_{$i}_icone" ),
		);
	}
);

if ( ! $cards ) {
	return;
}
?>
<section class="cf-cards">
	<div class="container">

		<?php if ( $titulo_secao ) : ?>
			<h2 class="cf-cards__titulo"><?php echo esc_html( $titulo_secao ); ?></h2>
		<?php endif; ?>

		<ul class="cf-cards__grid" role="list">
			<?php foreach ( $cards as $card ) : ?>
				<li class="cf-cards__card">
					<?php if ( $card['icone'] ) : ?>
						<span class="cf-icone"><?php echo cliconnect_icone_ms( $card['icone'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escapa internamente. ?></span>
					<?php endif; ?>
					<h3 class="cf-cards__card-titulo"><?php echo esc_html( $card['titulo'] ); ?></h3>
					<?php if ( $card['texto'] ) : ?>
						<p class="cf-cards__card-texto"><?php echo esc_html( $card['texto'] ); ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</section>
