<?php
/**
 * CLI Connect — Boomi.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo    = cliconnect_campo_pagina( 'cc_boomi_titulo' );
$subtitulo = cliconnect_campo_pagina( 'cc_boomi_subtitulo' );
$texto     = cliconnect_campo_pagina( 'cc_boomi_texto' );
$badge_1   = cliconnect_campo_pagina( 'cc_boomi_badge_1' );
$badge_2   = cliconnect_campo_pagina( 'cc_boomi_badge_2' );
$imagem    = cliconnect_campo_pagina( 'cc_boomi_imagem' );

if ( ! $titulo ) {
	return;
}
?>
<section class="cc-boomi secao">
	<div class="container cc-boomi__inner">
		<div class="cc-boomi__texto">
			<h2 class="cc-boomi__titulo"><?php echo esc_html( $titulo ); ?></h2>

			<?php if ( $subtitulo ) : ?>
				<p class="cc-boomi__subtitulo"><?php echo esc_html( $subtitulo ); ?></p>
			<?php endif; ?>

			<?php if ( $texto ) : ?>
				<p class="cc-boomi__corpo"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>

			<?php if ( $badge_1 || $badge_2 ) : ?>
				<div class="cc-boomi__badges">
					<?php if ( $badge_1 ) : ?>
						<span class="cc-boomi__badge"><?php echo esc_html( $badge_1 ); ?></span>
					<?php endif; ?>
					<?php if ( $badge_2 ) : ?>
						<span class="cc-boomi__badge"><?php echo esc_html( $badge_2 ); ?></span>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $imagem ) : ?>
			<div class="cc-boomi__visual">
				<?php echo wp_get_attachment_image( (int) $imagem, 'large', false, array( 'class' => 'cc-boomi__img', 'alt' => '' ) ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
