<?php
/**
 * CLI Flow — Dúvidas Frequentes.
 *
 * Mesmo CSS e JS do FAQ da home (.faq + data-faq-gatilho), com as perguntas
 * próprias da página: CPT `cli_faq` escolhido no relationship `cf_faq_itens`
 * (título = pergunta, conteúdo = resposta). Como no Figma, todas começam
 * abertas; o acordeão continua funcionando ao clicar.
 *
 * Campos ACF (group_cli_cli_flow, aba "16 · FAQ"):
 *   cf_faq_eyebrow / cf_faq_titulo / cf_faq_itens
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'cf_faq_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'cf_faq_titulo' );
$itens   = array_filter(
	(array) cliconnect_campo_pagina( 'cf_faq_itens', array() ),
	static function ( $item ) {
		return $item instanceof WP_Post;
	}
);

if ( ! $itens ) {
	return;
}
?>
<section class="secao faq cf-faq">
	<div class="container">

		<header class="secao__cabecalho">
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<?php if ( $titulo ) : ?>
				<h2 class="secao__titulo"><?php echo esc_html( $titulo ); ?></h2>
			<?php endif; ?>
		</header>

		<div class="faq__lista">
			<?php foreach ( array_values( $itens ) as $indice => $item ) : ?>
				<?php $id_resposta = 'cf-faq-resposta-' . ( $indice + 1 ); ?>
				<div class="faq__item">
					<h3>
						<button
							class="faq__gatilho"
							type="button"
							aria-expanded="true"
							aria-controls="<?php echo esc_attr( $id_resposta ); ?>"
							data-faq-gatilho
						>
							<span><?php echo esc_html( get_the_title( $item ) ); ?></span>
							<?php echo cliconnect_icone( 'chevron-baixo', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_icone retorna SVG estático do tema. ?>
						</button>
					</h3>

					<div class="faq__resposta is-open" id="<?php echo esc_attr( $id_resposta ); ?>">
						<?php echo wp_kses_post( wpautop( $item->post_content ) ); ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>
