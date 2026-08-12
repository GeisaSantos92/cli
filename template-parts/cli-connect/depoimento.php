<?php
/**
 * CLI Connect — Depoimento.
 *
 * Card azul escuro horizontal: foto + nome/cargo | citação | botão.
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

if ( ! $texto ) {
	return;
}
?>
<section class="cc-depoimento">
	<div class="container">
		<div class="cc-depoimento__card">

			<div class="cc-depoimento__perfil">
				<?php if ( $foto ) : ?>
					<div class="cc-depoimento__foto-wrap">
						<?php echo wp_get_attachment_image( (int) $foto, 'medium', false, array( 'class' => 'cc-depoimento__foto-img', 'alt' => esc_attr( $nome ) ) ); ?>
					</div>
				<?php endif; ?>

				<div class="cc-depoimento__id">
					<?php if ( $nome ) : ?>
						<strong class="cc-depoimento__nome"><?php echo esc_html( $nome ); ?></strong>
					<?php endif; ?>
					<?php if ( $cargo ) : ?>
						<span class="cc-depoimento__cargo"><?php echo esc_html( $cargo ); ?></span>
					<?php endif; ?>
				</div>
			</div>

			<blockquote class="cc-depoimento__citacao">
				<p>"<?php echo esc_html( $texto ); ?>"</p>
			</blockquote>

			<div class="cc-depoimento__acao">
				<?php cliconnect_botao_pagina( 'cc_dep_botao', 'cc-depoimento__botao' ); ?>
			</div>

		</div>
	</div>
</section>
