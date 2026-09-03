<?php
/**
 * Trabalhe Conosco — Depoimento de colaboradora.
 *
 * Layout: coluna esquerda (foto + nome + cargo) | coluna direita (" + citação).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$foto  = absint( cliconnect_campo_pagina( 'dep_foto', 0 ) );
$nome  = cliconnect_campo_pagina( 'dep_nome' );
$cargo = cliconnect_campo_pagina( 'dep_cargo' );
$texto = cliconnect_campo_pagina( 'dep_texto' );

if ( ! $texto ) {
	return;
}
?>

<section class="secao tc-depoimento">
	<div class="container tc-depoimento__inner">

		<div class="tc-depoimento__autor">
			<?php if ( $foto ) : ?>
				<div class="tc-depoimento__foto">
					<?php echo wp_get_attachment_image( $foto, 'medium', false, array( 'alt' => esc_attr( $nome ), 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapa internamente. ?>
				</div>
			<?php endif; ?>
			<?php if ( $nome ) : ?>
				<p class="tc-depoimento__nome"><?php echo esc_html( $nome ); ?></p>
			<?php endif; ?>
			<?php if ( $cargo ) : ?>
				<p class="tc-depoimento__cargo"><?php echo esc_html( $cargo ); ?></p>
			<?php endif; ?>
		</div>

		<blockquote class="tc-depoimento__bloco">
			<p class="tc-depoimento__citacao"><?php echo esc_html( $texto ); ?></p>
		</blockquote>

	</div>
</section>
