<?php
/**
 * CLI Signature — Arquiteto Dedicado.
 *
 * Layout 2 colunas: texto + CTA à esquerda, lista numerada de tópicos à direita.
 * Mesmas classes CSS de cs-especialista, padding invertido em relação à seção anterior.
 *
 * Campos ACF (group_cli_cli_signature, aba "6 · Arquiteto"):
 *   cs_arquiteto_titulo         — título H2
 *   cs_arquiteto_texto          — parágrafo de apoio
 *   cs_arquiteto_botao          — link do CTA
 *   cs_arquiteto_{1-7}_titulo   — título de cada tópico numerado
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo = cliconnect_campo_pagina( 'cs_arquiteto_titulo' );
$texto  = cliconnect_campo_pagina( 'cs_arquiteto_texto' );
$botao  = cliconnect_campo_pagina( 'cs_arquiteto_botao' );

$topicos = array();
for ( $i = 1; $i <= 7; $i++ ) {
	$item = cliconnect_campo_pagina( 'cs_arquiteto_' . $i . '_titulo' );
	if ( $item ) {
		$topicos[] = $item;
	}
}

if ( ! $titulo && ! $topicos ) {
	return;
}
?>
<section class="cs-especialista cs-especialista--arquiteto">
	<div class="container cs-especialista__inner">

		<div class="cs-especialista__conteudo">
			<?php if ( $titulo ) : ?>
				<h2 class="cs-especialista__titulo"><?php echo esc_html( $titulo ); ?></h2>
			<?php endif; ?>

			<?php if ( $texto ) : ?>
				<p class="cs-especialista__texto"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>

			<?php if ( $botao ) : ?>
				<div class="cs-especialista__acoes">
					<?php cliconnect_botao( $botao ); ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $topicos ) : ?>
			<ol class="cs-especialista__lista" role="list">
				<?php foreach ( $topicos as $indice => $topico ) : ?>
					<li class="cs-especialista-item">
						<span class="cs-especialista-item__num" aria-hidden="true">
							<?php echo esc_html( sprintf( '%02d', $indice + 1 ) ); ?>
						</span>
						<p class="cs-especialista-item__titulo"><?php echo esc_html( $topico ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>

	</div>
</section>
