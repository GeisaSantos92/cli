<?php
/**
 * CLI Connect — Depoimento.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$texto = cliconnect_campo_pagina( 'cc_dep_texto' );
$nome  = cliconnect_campo_pagina( 'cc_dep_nome' );
$cargo = cliconnect_campo_pagina( 'cc_dep_cargo' );
$foto  = cliconnect_campo_pagina( 'cc_dep_foto' );
$botao = cliconnect_campo_pagina( 'cc_dep_botao' );

if ( ! $texto ) {
	return;
}
?>
<section class="cc-depoimento secao">
	<div class="container cc-depoimento__inner">
		<?php if ( $foto ) : ?>
			<div class="cc-depoimento__foto">
				<?php echo wp_get_attachment_image( (int) $foto, 'medium', false, array( 'class' => 'cc-depoimento__img', 'alt' => esc_attr( $nome ) ) ); ?>
			</div>
		<?php endif; ?>

		<div class="cc-depoimento__conteudo">
			<blockquote class="cc-depoimento__citacao">
				<p>"<?php echo esc_html( $texto ); ?>"</p>
			</blockquote>

			<div class="cc-depoimento__autor">
				<?php if ( $nome ) : ?>
					<strong class="cc-depoimento__nome"><?php echo esc_html( $nome ); ?></strong>
				<?php endif; ?>
				<?php if ( $cargo ) : ?>
					<span class="cc-depoimento__cargo"><?php echo esc_html( $cargo ); ?></span>
				<?php endif; ?>
			</div>

			<?php if ( $botao ) : ?>
				<a class="cc-depoimento__botao botao botao--secundario" href="<?php echo esc_url( $botao['url'] ?? '' ); ?>"<?php echo $botao['target'] ? ' target="_blank" rel="noopener"' : ''; ?>>
					<?php echo esc_html( $botao['title'] ?? '' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
