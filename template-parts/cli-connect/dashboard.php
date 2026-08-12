<?php
/**
 * CLI Connect — Acompanhe em Tempo Real (Dashboard).
 *
 * Layout 2 colunas: texto + CTA à esquerda, screenshot do portal à direita.
 * Padding assimétrico: 80px top, 120px bottom (conforme Figma).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'cc_dashboard_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'cc_dashboard_titulo' );
$texto   = cliconnect_campo_pagina( 'cc_dashboard_texto' );

$screenshot = cliconnect_imagem_tema(
	'cc-dashboard.png',
	array(
		'class'  => 'cc-dashboard__img',
		'alt'    => __( 'Screenshot do portal CLI Connect — visão geral de projetos e solicitações', 'cli' ),
		'width'  => 640,
		'height' => 427,
	)
);

if ( ! $titulo ) {
	return;
}
?>
<section class="cc-dashboard">
	<div class="cc-dashboard__inner container">

		<div class="cc-dashboard__texto">
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>

			<?php if ( $titulo ) : ?>
				<h2 class="cc-dashboard__titulo"><?php echo esc_html( $titulo ); ?></h2>
			<?php endif; ?>

			<?php if ( $texto ) : ?>
				<p class="cc-dashboard__corpo"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>

			<?php cliconnect_botao_pagina( 'cc_dashboard_botao', 'botao botao--primario cc-dashboard__botao' ); ?>
		</div>

		<?php if ( $screenshot ) : ?>
			<div class="cc-dashboard__visual">
				<?php echo $screenshot; // phpcs:ignore WordPress.Security.EscapeOutput -- montado com escape em cliconnect_imagem_tema(). ?>
			</div>
		<?php endif; ?>

	</div>
</section>
