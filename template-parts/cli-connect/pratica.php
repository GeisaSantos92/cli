<?php
/**
 * CLI Connect — Na Prática.
 *
 * Layout 2 colunas: texto + bullets à esquerda, imagem do agente à direita.
 * Fundo branco, padding vertical padrão.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'cc_np_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'cc_np_titulo' );
$texto   = cliconnect_campo_pagina( 'cc_np_texto' );
$imagem  = cliconnect_campo_pagina( 'cc_np_imagem' );

$bullets = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$bullet = cliconnect_campo_pagina( "cc_np_bullet_{$i}" );
	if ( $bullet ) {
		$bullets[] = $bullet;
	}
}

if ( ! $titulo ) {
	return;
}
?>
<section class="cc-pratica">
	<div class="cc-pratica__inner container">

		<div class="cc-pratica__texto">
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>

			<h2 class="cc-pratica__titulo"><?php echo esc_html( $titulo ); ?></h2>

			<?php if ( $texto ) : ?>
				<p class="cc-pratica__corpo"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>

			<?php if ( $bullets ) : ?>
				<ul class="cc-pratica__bullets">
					<?php foreach ( $bullets as $bullet ) : ?>
						<li class="cc-pratica__bullet"><?php echo esc_html( $bullet ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<?php if ( $imagem ) : ?>
			<div class="cc-pratica__visual">
				<?php echo wp_get_attachment_image( (int) $imagem, 'large', false, array( 'class' => 'cc-pratica__img', 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapa internamente. ?>
			</div>
		<?php endif; ?>

	</div>
</section>
