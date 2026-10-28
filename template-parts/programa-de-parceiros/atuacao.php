<?php
/**
 * Programa de Parceiros — Atuação conjunta ("Concentre-se no que você já faz…").
 *
 * Cabeçalho centralizado + duas colunas de atribuições (parceiro × CLI Connect),
 * cada uma com rótulo, título, etiqueta, checklist e rodapé. A coluna da CLI
 * leva o selo de topo e a borda em destaque.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'pp_atuacao_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'pp_atuacao_titulo' );
$texto   = cliconnect_campo_pagina( 'pp_atuacao_texto' );

$colunas = array();
foreach ( array( 'parceiro', 'cli' ) as $lado ) {
	$titulo_coluna = cliconnect_campo_pagina( "pp_atuacao_{$lado}_titulo" );

	if ( ! $titulo_coluna ) {
		continue;
	}

	$colunas[] = array(
		'lado'   => $lado,
		'selo'   => cliconnect_campo_pagina( "pp_atuacao_{$lado}_selo" ),
		'rotulo' => cliconnect_campo_pagina( "pp_atuacao_{$lado}_rotulo" ),
		'titulo' => $titulo_coluna,
		'tag'    => cliconnect_campo_pagina( "pp_atuacao_{$lado}_tag" ),
		'itens'  => cliconnect_lista_numerada_pagina( "pp_atuacao_{$lado}_%d", 6 ),
		'rodape' => cliconnect_campo_pagina( "pp_atuacao_{$lado}_rodape" ),
	);
}

if ( ! $titulo && ! $colunas ) {
	return;
}
?>
<section class="secao pp-atuacao">
	<div class="container">
		<header class="pp-cabecalho">
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<?php if ( $titulo ) : ?>
				<h2 class="pp-cabecalho__titulo"><?php echo esc_html( $titulo ); ?></h2>
			<?php endif; ?>
			<?php if ( $texto ) : ?>
				<p class="pp-cabecalho__texto pp-atuacao__texto"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( $colunas ) : ?>
			<div class="pp-atuacao__colunas">
				<?php foreach ( $colunas as $coluna ) : ?>
					<article class="pp-atuacao__coluna pp-atuacao__coluna--<?php echo esc_attr( $coluna['lado'] ); ?>">
						<?php if ( $coluna['selo'] ) : ?>
							<span class="pp-atuacao__selo"><?php echo esc_html( $coluna['selo'] ); ?></span>
						<?php endif; ?>

						<header class="pp-atuacao__topo">
							<div>
								<?php if ( $coluna['rotulo'] ) : ?>
									<span class="pp-atuacao__rotulo"><?php echo esc_html( $coluna['rotulo'] ); ?></span>
								<?php endif; ?>
								<h3 class="pp-atuacao__titulo"><?php echo esc_html( $coluna['titulo'] ); ?></h3>
							</div>
							<?php if ( $coluna['tag'] ) : ?>
								<span class="pp-atuacao__tag"><?php echo esc_html( $coluna['tag'] ); ?></span>
							<?php endif; ?>
						</header>

						<?php if ( $coluna['itens'] ) : ?>
							<ul class="pp-atuacao__lista">
								<?php foreach ( $coluna['itens'] as $item ) : ?>
									<li class="pp-atuacao__item">
										<?php echo cliconnect_icone_ms( 'cli' === $coluna['lado'] ? 'verified' : 'check_circle' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_icone_ms escapa a ligatura. ?>
										<?php echo esc_html( $item ); ?>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>

						<?php if ( $coluna['rodape'] ) : ?>
							<p class="pp-atuacao__rodape"><?php echo esc_html( $coluna['rodape'] ); ?></p>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
