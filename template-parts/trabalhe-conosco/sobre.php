<?php
/**
 * Trabalhe Conosco — Somos a CLI (fundo azul escuro + grade de fotos).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo  = cliconnect_campo_pagina( 'sobre_titulo' );
$texto_1 = cliconnect_campo_pagina( 'sobre_texto_1' );
$texto_2 = cliconnect_campo_pagina( 'sobre_texto_2' );

if ( ! $titulo && ! $texto_1 ) {
	return;
}

$foto_id = absint( cliconnect_campo_pagina( 'sobre_foto_1', 0 ) );
?>

<section class="tc-sobre">
	<div class="container tc-sobre__inner">

		<div class="tc-sobre__texto">
			<?php if ( $titulo ) : ?>
				<h2 class="tc-sobre__titulo"><?php echo esc_html( $titulo ); ?></h2>
			<?php endif; ?>

			<?php if ( $texto_1 ) : ?>
				<p class="tc-sobre__paragrafo"><?php echo esc_html( $texto_1 ); ?></p>
			<?php endif; ?>

			<?php if ( $texto_2 ) : ?>
				<p class="tc-sobre__paragrafo"><?php echo esc_html( $texto_2 ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $foto_id ) : ?>
			<div class="tc-sobre__imagem" aria-hidden="true">
				<?php echo wp_get_attachment_image( $foto_id, 'large', false, array( 'alt' => '', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapa internamente. ?>
			</div>
		<?php endif; ?>

	</div>
</section>
