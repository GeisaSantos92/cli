<?php
/**
 * CLI Flow — "Do seu jeito. Integrado aos seus grandes sistemas."
 *
 * Título à esquerda + selo "Até 80% menos licenças" pendurado à direita;
 * abaixo, uma janela do CLI Flow ("Consulta de serviços") que mistura dados
 * de dois sistemas (Protheus e HubSpot): busca + tabela à esquerda e painel
 * de detalhe à direita; legenda centralizada embaixo. A janela é HTML para o
 * texto continuar editável e traduzível — os botões dela são só ilustração.
 *
 * Campos ACF (group_cli_cli_flow, aba "10 · Integrações"):
 *   cf_int_titulo / _titulo_destaque
 *   cf_int_selo_antes / cf_int_destaque_numero / cf_int_selo_depois
 *   cf_int_sistema_{1-2}_nome / _icone       — fontes de dado (pílulas e ícones)
 *   cf_int_janela_titulo / cf_int_janela_fontes
 *   cf_int_busca_label / cf_int_busca_valor
 *   cf_int_col_{1-5} / cf_int_col_{1-5}_fonte (''|1|2)
 *   cf_int_linha_{1-4}                        — células separadas por " | "
 *   cf_int_detalhe_sub / cf_int_detalhe_titulo
 *   cf_int_bloco_{1-2}_titulo / _fonte / _linhas ("rótulo | valor" por linha)
 *   cf_int_botao_{1-2}                        — rótulos ilustrativos
 *   cf_int_legenda                            — aceita <strong>
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo   = cliconnect_campo_pagina( 'cf_int_titulo' );
$destaque = cliconnect_campo_pagina( 'cf_int_titulo_destaque' );

if ( ! $titulo && ! $destaque ) {
	return;
}

$selo = array(
	'antes'  => cliconnect_campo_pagina( 'cf_int_selo_antes' ),
	'numero' => cliconnect_campo_pagina( 'cf_int_destaque_numero' ),
	'depois' => cliconnect_campo_pagina( 'cf_int_selo_depois' ),
);

// Fontes de dado: índice => nome + ícone.
$sistemas = array();
for ( $i = 1; $i <= 2; $i++ ) {
	$sistemas[ $i ] = array(
		'nome'  => cliconnect_campo_pagina( "cf_int_sistema_{$i}_nome" ),
		'icone' => (int) ( cliconnect_campo_pagina( "cf_int_sistema_{$i}_icone" ) ?? 0 ),
	);
}

/**
 * Ícone pequeno de uma fonte de dado (vazio se a fonte não existir).
 *
 * @param array      $sistemas Fontes de dado.
 * @param int|string $fonte    Índice da fonte.
 * @return string
 */
$icone_fonte = static function ( $sistemas, $fonte ) {
	$fonte = (int) $fonte;
	if ( empty( $sistemas[ $fonte ]['icone'] ) ) {
		return '';
	}
	return wp_get_attachment_image( $sistemas[ $fonte ]['icone'], 'thumbnail', false, array( 'class' => 'cf-int__fonte-icone', 'alt' => '', 'loading' => 'lazy' ) );
};

$colunas = array();
for ( $c = 1; $c <= 5; $c++ ) {
	$colunas[] = array(
		'rotulo' => cliconnect_campo_pagina( "cf_int_col_{$c}" ),
		'fonte'  => cliconnect_campo_pagina( "cf_int_col_{$c}_fonte" ),
	);
}

$linhas = cliconnect_lista_numerada_pagina(
	'cf_int_linha_%d',
	4,
	static function ( $i, $linha ) {
		return array_map( 'trim', explode( '|', $linha ) );
	}
);

$blocos = array();
for ( $b = 1; $b <= 2; $b++ ) {
	$itens = array_filter( array_map( 'trim', explode( "\n", (string) cliconnect_campo_pagina( "cf_int_bloco_{$b}_linhas" ) ) ) );
	$blocos[] = array(
		'titulo' => cliconnect_campo_pagina( "cf_int_bloco_{$b}_titulo" ),
		'fonte'  => cliconnect_campo_pagina( "cf_int_bloco_{$b}_fonte" ),
		'tipo'   => 1 === $b ? 'valores' : 'negocios',
		'itens'  => array_map(
			static function ( $item ) {
				return array_map( 'trim', explode( '|', $item, 2 ) );
			},
			$itens
		),
	);
}

$janela_titulo = cliconnect_campo_pagina( 'cf_int_janela_titulo' );
$janela_fontes = cliconnect_campo_pagina( 'cf_int_janela_fontes' );
$busca_label   = cliconnect_campo_pagina( 'cf_int_busca_label' );
$busca_valor   = cliconnect_campo_pagina( 'cf_int_busca_valor' );
$detalhe_sub   = cliconnect_campo_pagina( 'cf_int_detalhe_sub' );
$detalhe_tit   = cliconnect_campo_pagina( 'cf_int_detalhe_titulo' );
$botoes        = array_filter( array( cliconnect_campo_pagina( 'cf_int_botao_1' ), cliconnect_campo_pagina( 'cf_int_botao_2' ) ) );
$legenda       = cliconnect_campo_pagina( 'cf_int_legenda' );
?>
<section class="cf-int">
	<div class="container">

		<div class="cf-int__topo">
			<h2 class="cf-int__titulo">
				<?php if ( $titulo ) : ?>
					<span class="cf-int__titulo-linha"><?php echo esc_html( $titulo ); ?></span>
				<?php endif; ?>
				<?php if ( $destaque ) : ?>
					<span class="cf-int__titulo-linha cf-int__titulo-linha--destaque"><?php echo esc_html( $destaque ); ?></span>
				<?php endif; ?>
			</h2>

			<?php if ( $selo['numero'] ) : ?>
				<p class="cf-int__selo">
					<?php if ( $selo['antes'] ) : ?>
						<span class="cf-int__selo-antes"><?php echo esc_html( $selo['antes'] ); ?></span>
					<?php endif; ?>
					<strong class="cf-int__selo-numero"><?php echo esc_html( $selo['numero'] ); ?></strong>
					<?php if ( $selo['depois'] ) : ?>
						<span class="cf-int__selo-depois"><?php echo esc_html( $selo['depois'] ); ?></span>
					<?php endif; ?>
				</p>
			<?php endif; ?>
		</div>

		<?php /* Janela do CLI Flow ------------------------------------------- */ ?>
		<div class="cf-int__janela">
			<div class="cf-int__janela-topo">
				<span class="cf-int__janela-marca">
					<?php echo cliconnect_imagem_tema( 'cf-logo-cli.svg', array( 'class' => 'cf-int__janela-simbolo', 'alt' => 'CLI', 'width' => 74, 'height' => 19 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper escapa os atributos. ?>
					<?php $marca = cliconnect_campo_pagina( 'cf_casos_janela_marca' ); ?>
					<?php if ( $marca ) : ?>
						<span class="cf-int__janela-marca-texto"><?php echo esc_html( $marca ); ?></span>
					<?php endif; ?>
				</span>

				<?php if ( $janela_titulo ) : ?>
					<p class="cf-int__janela-titulo"><?php echo esc_html( $janela_titulo ); ?></p>
				<?php endif; ?>

				<p class="cf-int__fontes">
					<?php if ( $janela_fontes ) : ?>
						<span><?php echo esc_html( $janela_fontes ); ?></span>
					<?php endif; ?>
					<?php foreach ( $sistemas as $n => $sistema ) : ?>
						<?php if ( $sistema['nome'] ) : ?>
							<span class="cf-int__pilula cf-int__pilula--<?php echo esc_attr( (string) $n ); ?>">
								<?php echo $icone_fonte( $sistemas, $n ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapa internamente. ?>
								<?php echo esc_html( $sistema['nome'] ); ?>
							</span>
						<?php endif; ?>
					<?php endforeach; ?>
				</p>
			</div>

			<div class="cf-int__janela-corpo">
				<div class="cf-int__consulta">
					<?php if ( $busca_label ) : ?>
						<p class="cf-int__busca-label"><?php echo esc_html( $busca_label ); ?></p>
					<?php endif; ?>
					<?php if ( $busca_valor ) : ?>
						<p class="cf-int__busca"><?php echo esc_html( $busca_valor ); ?></p>
					<?php endif; ?>

					<?php if ( $linhas ) : ?>
						<table class="cf-int__tabela">
							<thead>
								<tr>
									<?php foreach ( $colunas as $coluna ) : ?>
										<th scope="col">
											<?php echo esc_html( $coluna['rotulo'] ); ?>
											<?php echo $icone_fonte( $sistemas, $coluna['fonte'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapa internamente. ?>
										</th>
									<?php endforeach; ?>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $linhas as $n => $celulas ) : ?>
									<tr<?php echo 0 === $n ? ' class="ativo"' : ''; ?>>
										<?php foreach ( $colunas as $c => $coluna ) : ?>
											<td><?php echo esc_html( $celulas[ $c ] ?? '' ); ?></td>
										<?php endforeach; ?>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php endif; ?>
				</div>

				<div class="cf-int__detalhe">
					<?php if ( $detalhe_sub ) : ?>
						<p class="cf-int__detalhe-sub"><?php echo esc_html( $detalhe_sub ); ?></p>
					<?php endif; ?>
					<?php if ( $detalhe_tit ) : ?>
						<p class="cf-int__detalhe-titulo"><?php echo esc_html( $detalhe_tit ); ?></p>
					<?php endif; ?>

					<?php foreach ( $blocos as $bloco ) : ?>
						<?php if ( ! $bloco['titulo'] && ! $bloco['itens'] ) : ?>
							<?php continue; ?>
						<?php endif; ?>
						<div class="cf-int__bloco cf-int__bloco--<?php echo esc_attr( $bloco['tipo'] ); ?>">
							<?php if ( $bloco['titulo'] ) : ?>
								<p class="cf-int__bloco-titulo">
									<?php echo esc_html( $bloco['titulo'] ); ?>
									<?php echo $icone_fonte( $sistemas, $bloco['fonte'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapa internamente. ?>
								</p>
							<?php endif; ?>
							<ul class="cf-int__bloco-lista" role="list">
								<?php foreach ( $bloco['itens'] as $item ) : ?>
									<li>
										<span class="cf-int__bloco-rotulo"><?php echo esc_html( $item[0] ); ?></span>
										<?php if ( isset( $item[1] ) ) : ?>
											<span class="cf-int__bloco-valor"><?php echo esc_html( 'negocios' === $bloco['tipo'] ? '· ' . $item[1] : $item[1] ); ?></span>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endforeach; ?>

					<?php if ( $botoes ) : ?>
						<div class="cf-int__acoes" aria-hidden="true">
							<?php foreach ( array_values( $botoes ) as $b => $rotulo ) : ?>
								<span class="cf-int__acao cf-int__acao--<?php echo esc_attr( (string) ( $b + 1 ) ); ?>"><?php echo esc_html( $rotulo ); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<?php if ( $legenda ) : ?>
			<p class="cf-int__legenda"><?php echo wp_kses( $legenda, array( 'strong' => array() ) ); ?></p>
		<?php endif; ?>

	</div>
</section>
