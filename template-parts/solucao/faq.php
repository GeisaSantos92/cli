<?php
/**
 * Solução — Seção FAQ (Dúvidas Frequentes).
 *
 * Accordion nativo com <details>/<summary>. Fundo branco.
 *
 * Campos ACF (group_cli_solucao, aba "10 · FAQ"):
 *   solucao_faq_titulo  — título da seção (padrão: "Dúvidas Frequentes")
 *   solucao_faq_itens   — relationship com cli_faq (obj WP_Post)
 *
 * O título do post cli_faq é a pergunta; o conteúdo do post é a resposta.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo = cliconnect_campo_pagina( 'solucao_faq_titulo' ) ?? '';
$itens  = cliconnect_campo_pagina( 'solucao_faq_itens' );

if ( ! $titulo && empty( $itens ) ) {
	return;
}

if ( ! is_array( $itens ) || empty( $itens ) ) {
	return;
}
$chevron_url = get_theme_file_uri( '/assets/img/faq-chevron.svg' );
?>
<section class="sfaq" aria-label="<?php esc_attr_e( 'Dúvidas Frequentes', 'cli' ); ?>" style="--sfaq-chevron: url('<?php echo esc_url( $chevron_url ); ?>')">
	<div class="container">
		<div class="sfaq__inner">

			<header class="sfaq__header">
				<p class="eyebrow"><?php esc_html_e( 'FAQ', 'cli' ); ?></p>
				<?php if ( $titulo ) : ?>
					<h2 class="sfaq__titulo"><?php echo esc_html( $titulo ); ?></h2>
				<?php endif; ?>
			</header>

			<div class="sfaq__lista" role="list">
				<?php foreach ( $itens as $faq_post ) :
					if ( ! ( $faq_post instanceof WP_Post ) ) {
						continue;
					}
					$pergunta = get_the_title( $faq_post );
					$resposta = apply_filters( 'the_content', get_post_field( 'post_content', $faq_post ) );
					if ( ! $pergunta || ! $resposta ) {
						continue;
					}
				?>
				<details class="sfaq__item" role="listitem">
					<summary class="sfaq__pergunta">
						<span class="sfaq__pergunta-texto"><?php echo esc_html( $pergunta ); ?></span>
						<span class="sfaq__icone" aria-hidden="true"></span>
					</summary>
					<div class="sfaq__resposta">
						<div class="sfaq__resposta-texto">
							<?php echo wp_kses_post( $resposta ); ?>
						</div>
					</div>
				</details>
				<?php endforeach; ?>
			</div>

		</div>
	</div>
</section>
