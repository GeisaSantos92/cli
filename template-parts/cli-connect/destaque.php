<?php
/**
 * CLI Connect — Destaque CTA.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo = cliconnect_campo_pagina( 'cc_destaque_titulo' );
$texto  = cliconnect_campo_pagina( 'cc_destaque_texto' );
$botao  = cliconnect_campo_pagina( 'cc_destaque_botao' );

if ( ! $titulo ) {
	return;
}
?>
<section class="cc-destaque">
	<div class="container cc-destaque__inner">
		<h2 class="cc-destaque__titulo"><?php echo esc_html( $titulo ); ?></h2>

		<?php if ( $texto ) : ?>
			<p class="cc-destaque__texto"><?php echo esc_html( $texto ); ?></p>
		<?php endif; ?>

		<?php if ( $botao ) : ?>
			<a class="cc-destaque__botao botao botao--primario" href="<?php echo esc_url( $botao['url'] ?? '' ); ?>"<?php echo $botao['target'] ? ' target="_blank" rel="noopener"' : ''; ?>>
				<?php echo esc_html( $botao['title'] ?? '' ); ?>
			</a>
		<?php endif; ?>
	</div>
</section>
