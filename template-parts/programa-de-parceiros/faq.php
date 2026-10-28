<?php
/**
 * Programa de Parceiros — Dúvidas Frequentes.
 *
 * Mesmo CSS e JS do FAQ da home (.faq + data-faq-gatilho). As perguntas vêm
 * do CPT `cli_faq`, escolhidas no campo relationship `pp_faq_itens`: o título
 * do item é a pergunta e o conteúdo, a resposta.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'pp_faq_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'pp_faq_titulo' );
$itens   = cliconnect_campo_pagina( 'pp_faq_itens', array() );
$itens   = array_filter(
	(array) $itens,
	static function ( $item ) {
		return $item instanceof WP_Post;
	}
);

if ( ! $itens ) {
	return;
}
?>
<section class="secao faq pp-faq">
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
				<?php $id_resposta = 'pp-faq-resposta-' . ( $indice + 1 ); ?>
				<div class="faq__item">
					<h3>
						<button
							class="faq__gatilho"
							type="button"
							aria-expanded="false"
							aria-controls="<?php echo esc_attr( $id_resposta ); ?>"
							data-faq-gatilho
						>
							<span><?php echo esc_html( get_the_title( $item ) ); ?></span>
							<?php echo cliconnect_icone( 'chevron-baixo', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_icone retorna SVG estático do tema. ?>
						</button>
					</h3>

					<div class="faq__resposta" id="<?php echo esc_attr( $id_resposta ); ?>" hidden>
						<?php echo wp_kses_post( wpautop( $item->post_content ) ); ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>
