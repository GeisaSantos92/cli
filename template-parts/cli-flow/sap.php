<?php
/**
 * CLI Flow — "Do pedido ao SAP sem ninguém redigitar".
 *
 * Faixa azul-marinho: à esquerda eyebrow + título + passos numerados + botão;
 * à direita um cartão que ilustra a requisição de compra com a pré-análise do
 * agente (verificações OK/Atenção, resumo e botões de decisão). O cartão é
 * HTML para o texto continuar editável e traduzível — os botões dele são só
 * ilustração (spans, não clicáveis).
 *
 * Campos ACF (group_cli_cli_flow, aba "9 · SAP"):
 *   cf_sap_eyebrow, cf_sap_titulo, cf_sap_titulo_destaque
 *   cf_sap_{1-4}_titulo / _texto     — passos
 *   cf_sap_botao                     — CTA
 *   cf_sap_card_titulo / _subtitulo / _selo / _selo_logo / _agente
 *   cf_sap_check_{1-5}_tipo (ok|atencao) / _selo / _titulo / _texto
 *   cf_sap_card_resumo_titulo / _resumo_texto
 *   cf_sap_card_botao_{1-3}
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow  = cliconnect_campo_pagina( 'cf_sap_eyebrow' );
$titulo   = cliconnect_campo_pagina( 'cf_sap_titulo' );
$destaque = cliconnect_campo_pagina( 'cf_sap_titulo_destaque' );
$botao    = cliconnect_campo_pagina( 'cf_sap_botao' );

if ( ! $titulo && ! $destaque ) {
	return;
}

$passos = cliconnect_lista_numerada_pagina(
	'cf_sap_%d_titulo',
	4,
	static function ( $i, $passo_titulo ) {
		return array(
			'titulo' => $passo_titulo,
			'texto'  => cliconnect_campo_pagina( "cf_sap_{$i}_texto" ),
		);
	}
);

$card = array(
	'titulo'        => cliconnect_campo_pagina( 'cf_sap_card_titulo' ),
	'subtitulo'     => cliconnect_campo_pagina( 'cf_sap_card_subtitulo' ),
	'selo'          => cliconnect_campo_pagina( 'cf_sap_card_selo' ),
	'selo_logo'     => (int) ( cliconnect_campo_pagina( 'cf_sap_card_selo_logo' ) ?? 0 ),
	'agente'        => cliconnect_campo_pagina( 'cf_sap_card_agente' ),
	'resumo_titulo' => cliconnect_campo_pagina( 'cf_sap_card_resumo_titulo' ),
	'resumo_texto'  => cliconnect_campo_pagina( 'cf_sap_card_resumo_texto' ),
);

$checks = cliconnect_lista_numerada_pagina(
	'cf_sap_check_%d_titulo',
	5,
	static function ( $i, $check_titulo ) {
		return array(
			'tipo'   => 'atencao' === cliconnect_campo_pagina( "cf_sap_check_{$i}_tipo" ) ? 'atencao' : 'ok',
			'selo'   => cliconnect_campo_pagina( "cf_sap_check_{$i}_selo" ),
			'titulo' => $check_titulo,
			'texto'  => cliconnect_campo_pagina( "cf_sap_check_{$i}_texto" ),
		);
	}
);

$botoes_card = array_filter(
	array(
		cliconnect_campo_pagina( 'cf_sap_card_botao_1' ),
		cliconnect_campo_pagina( 'cf_sap_card_botao_2' ),
		cliconnect_campo_pagina( 'cf_sap_card_botao_3' ),
	)
);
?>
<section class="cf-sap">
	<div class="container cf-sap__inner">

		<div class="cf-sap__conteudo">
			<?php if ( $eyebrow ) : ?>
				<p class="cf-sap__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<h2 class="cf-sap__titulo">
				<?php if ( $titulo ) : ?>
					<span class="cf-sap__titulo-linha"><?php echo esc_html( $titulo ); ?></span>
				<?php endif; ?>
				<?php if ( $destaque ) : ?>
					<span class="cf-sap__titulo-linha"><?php echo esc_html( $destaque ); ?></span>
				<?php endif; ?>
			</h2>

			<?php if ( $passos ) : ?>
				<ol class="cf-sap__passos" role="list">
					<?php foreach ( $passos as $n => $passo ) : ?>
						<li class="cf-sap__passo">
							<span class="cf-sap__passo-numero" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $n + 1 ) ); ?></span>
							<div>
								<h3 class="cf-sap__passo-titulo"><?php echo esc_html( $passo['titulo'] ); ?></h3>
								<?php if ( $passo['texto'] ) : ?>
									<p class="cf-sap__passo-texto"><?php echo esc_html( $passo['texto'] ); ?></p>
								<?php endif; ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>

			<?php if ( $botao ) : ?>
				<?php cliconnect_botao_pagina( 'cf_sap_botao', 'botao botao--primario cf-sap__botao', '' ); ?>
			<?php endif; ?>
		</div>

		<?php if ( $card['titulo'] || $checks ) : ?>
			<div class="cf-sap__card">
				<div class="cf-sap__card-topo">
					<div>
						<?php if ( $card['titulo'] ) : ?>
							<p class="cf-sap__card-titulo"><?php echo esc_html( $card['titulo'] ); ?></p>
						<?php endif; ?>
						<?php if ( $card['subtitulo'] ) : ?>
							<p class="cf-sap__card-subtitulo"><?php echo esc_html( $card['subtitulo'] ); ?></p>
						<?php endif; ?>
					</div>
					<?php if ( $card['selo'] ) : ?>
						<span class="cf-sap__selo">
							<?php if ( $card['selo_logo'] ) : ?>
								<?php echo wp_get_attachment_image( $card['selo_logo'], 'medium', false, array( 'class' => 'cf-sap__selo-logo', 'alt' => '', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapa internamente. ?>
							<?php endif; ?>
							<?php echo esc_html( $card['selo'] ); ?>
						</span>
					<?php endif; ?>
				</div>

				<div class="cf-sap__card-corpo">
					<?php if ( $card['agente'] ) : ?>
						<p class="cf-sap__agente"><?php echo cliconnect_icone_ms( 'auto_awesome' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escapa internamente. ?><?php echo esc_html( $card['agente'] ); ?></p>
					<?php endif; ?>

					<?php if ( $checks ) : ?>
						<ul class="cf-sap__checks" role="list">
							<?php foreach ( $checks as $check ) : ?>
								<li class="cf-sap__check">
									<?php if ( $check['selo'] ) : ?>
										<span class="cf-sap__check-selo cf-sap__check-selo--<?php echo esc_attr( $check['tipo'] ); ?>"><?php echo esc_html( $check['selo'] ); ?></span>
									<?php endif; ?>
									<div>
										<p class="cf-sap__check-titulo"><?php echo esc_html( $check['titulo'] ); ?></p>
										<?php if ( $check['texto'] ) : ?>
											<p class="cf-sap__check-texto"><?php echo esc_html( $check['texto'] ); ?></p>
										<?php endif; ?>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( $card['resumo_titulo'] || $card['resumo_texto'] ) : ?>
						<div class="cf-sap__resumo">
							<?php if ( $card['resumo_titulo'] ) : ?>
								<p class="cf-sap__resumo-titulo"><?php echo esc_html( $card['resumo_titulo'] ); ?></p>
							<?php endif; ?>
							<?php if ( $card['resumo_texto'] ) : ?>
								<p class="cf-sap__resumo-texto"><?php echo esc_html( $card['resumo_texto'] ); ?></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( $botoes_card ) : ?>
						<div class="cf-sap__decisao" aria-hidden="true">
							<?php foreach ( array_values( $botoes_card ) as $b => $rotulo ) : ?>
								<span class="cf-sap__decisao-botao cf-sap__decisao-botao--<?php echo esc_attr( (string) ( $b + 1 ) ); ?>"><?php echo esc_html( $rotulo ); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>

	</div>
</section>
