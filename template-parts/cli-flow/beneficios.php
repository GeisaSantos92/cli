<?php
/**
 * CLI Flow — Benefícios.
 *
 * Faixa arredondada azul-marinho (dentro do container) com até 3 benefícios
 * curtos, centralizados e separados por fios verticais.
 *
 * Campos ACF (group_cli_cli_flow, aba "11 · Benefícios"):
 *   cf_ben_{1-3}_titulo — benefício
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$itens = cliconnect_lista_numerada_pagina( 'cf_ben_%d_titulo', 3 );

if ( ! $itens ) {
	return;
}
?>
<section class="cf-beneficios">
	<div class="container">
		<ul class="cf-beneficios__faixa" role="list">
			<?php foreach ( $itens as $item ) : ?>
				<li class="cf-beneficios__item"><span><?php echo esc_html( $item ); ?></span></li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
