<?php
/**
 * CLI Connect — Boomi.
 *
 * Layout idêntico à seção Boomi da home: grid 2 colunas,
 * texto à esquerda e cartão estático Boomi à direita.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'cc_boomi_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'cc_boomi_titulo' );
$texto   = cliconnect_campo_pagina( 'cc_boomi_texto' );

/*
 * O cartão da direita é a mesma peça fechada usada na home
 * (selo, logo e nota fazem parte da arte).
 */
$cartao = cliconnect_imagem_tema(
	'section-plataforma-boomi.png',
	array(
		'class'  => 'cc-boomi__cartao-img',
		'alt'    => __( 'Powered by Boomi — a única com 11 certificações globais do AICPA', 'cli' ),
		'width'  => 604,
		'height' => 403,
	)
);

if ( ! $titulo && ! $cartao ) {
	return;
}
?>
<section class="cc-boomi">
	<div class="cc-boomi__inner container">

		<div class="cc-boomi__texto">
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>

			<?php if ( $titulo ) : ?>
				<h2 class="cc-boomi__titulo"><?php echo esc_html( $titulo ); ?></h2>
			<?php endif; ?>

			<?php if ( $texto ) : ?>
				<p class="cc-boomi__corpo"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $cartao ) : ?>
			<div class="cc-boomi__cartao">
				<?php echo $cartao; // phpcs:ignore WordPress.Security.EscapeOutput -- montado com escape em cliconnect_imagem_tema(). ?>
			</div>
		<?php endif; ?>

	</div>
</section>
