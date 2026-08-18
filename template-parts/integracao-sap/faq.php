<?php
/**
 * Integração SAP — Dúvidas Frequentes (acordeão acessível).
 *
 * Mesmo CSS e JS do FAQ da home (classe .faq + .faq__lista).
 * Conteúdo via campos ACF inline (group_cli_integracao_sap, aba "13 · FAQ"):
 *   sap_faq_eyebrow      — eyebrow
 *   sap_faq_titulo       — título H2
 *   sap_faq_{1-3}_pergunta — texto da pergunta
 *   sap_faq_{1-3}_resposta — texto da resposta (textarea)
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'sap_faq_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'sap_faq_titulo' );

$itens = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$pergunta = cliconnect_campo_pagina( 'sap_faq_' . $i . '_pergunta' );
	$resposta = cliconnect_campo_pagina( 'sap_faq_' . $i . '_resposta' );
	if ( $pergunta ) {
		$itens[] = array(
			'pergunta' => $pergunta,
			'resposta' => $resposta ?? '',
		);
	}
}

if ( ! $titulo || ! $itens ) {
	return;
}
?>
<section class="secao faq">
	<div class="container">

		<header class="secao__cabecalho">
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<h2 class="secao__titulo"><?php echo esc_html( $titulo ); ?></h2>
		</header>

		<div class="faq__lista">
			<?php foreach ( $itens as $indice => $item ) :
				$id_resposta = 'sap-faq-resposta-' . ( $indice + 1 );
			?>
			<div class="faq__item">
				<h3>
					<button
						class="faq__gatilho"
						type="button"
						aria-expanded="false"
						aria-controls="<?php echo esc_attr( $id_resposta ); ?>"
						data-faq-gatilho
					>
						<span><?php echo esc_html( $item['pergunta'] ); ?></span>
						<?php echo cliconnect_icone( 'chevron-baixo', 20 ); // SVG estático. ?>
					</button>
				</h3>

				<div class="faq__resposta" id="<?php echo esc_attr( $id_resposta ); ?>" hidden>
					<?php echo wp_kses_post( wpautop( $item['resposta'] ) ); ?>
				</div>
			</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>
