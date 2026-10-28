<?php
/**
 * Programa de Parceiros — Perfil do parceiro.
 *
 * Duas colunas: cabeçalho à esquerda, lista de perfis (seta + título + texto)
 * à direita, separados por linha.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'pp_perfil_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'pp_perfil_titulo' );
$texto   = cliconnect_campo_pagina( 'pp_perfil_texto' );

$perfis = array();
for ( $i = 1; $i <= 4; $i++ ) {
	$titulo_perfil = cliconnect_campo_pagina( "pp_perfil_{$i}_titulo" );

	if ( $titulo_perfil ) {
		$perfis[] = array(
			'titulo' => $titulo_perfil,
			'texto'  => cliconnect_campo_pagina( "pp_perfil_{$i}_texto" ),
		);
	}
}

if ( ! $titulo && ! $perfis ) {
	return;
}
?>
<section class="secao pp-perfil">
	<div class="container pp-perfil__grade">
		<header class="pp-perfil__cabecalho">
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<?php if ( $titulo ) : ?>
				<h2 class="pp-perfil__titulo"><?php echo esc_html( $titulo ); ?></h2>
			<?php endif; ?>
			<?php if ( $texto ) : ?>
				<p class="pp-perfil__texto"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( $perfis ) : ?>
			<ul class="pp-perfil__lista">
				<?php foreach ( $perfis as $perfil ) : ?>
					<li class="pp-perfil__item">
						<h3 class="pp-perfil__item-titulo">
							<?php echo cliconnect_icone( 'seta-direita', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_icone retorna SVG estático do tema. ?>
							<?php echo esc_html( $perfil['titulo'] ); ?>
						</h3>
						<?php if ( $perfil['texto'] ) : ?>
							<p class="pp-perfil__item-texto"><?php echo esc_html( $perfil['texto'] ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
