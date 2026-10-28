<?php
/**
 * Programa de Parceiros — Hero.
 *
 * Fundo escuro com a foto do aperto de mãos à direita, título, texto com
 * destaque em negrito (*trecho*), dois botões e três métricas.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow   = cliconnect_campo_pagina( 'pp_hero_eyebrow' );
$titulo    = cliconnect_campo_pagina( 'pp_hero_titulo' );
$texto     = cliconnect_campo_pagina( 'pp_hero_texto' );
$imagem_id = absint( cliconnect_campo_pagina( 'pp_hero_imagem', 0 ) );

$metricas = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$numero = cliconnect_campo_pagina( "pp_hero_metrica_{$i}_numero" );

	if ( $numero ) {
		$metricas[] = array(
			'numero' => $numero,
			'rotulo' => cliconnect_campo_pagina( "pp_hero_metrica_{$i}_rotulo" ),
		);
	}
}

if ( ! $titulo ) {
	return;
}
?>
<section class="pp-hero">
	<?php if ( $imagem_id ) : ?>
		<div class="pp-hero__fundo" aria-hidden="true">
			<?php
			echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapa internamente.
				$imagem_id,
				'full',
				false,
				array(
					'alt'           => '',
					'fetchpriority' => 'high',
					'loading'       => 'eager',
				)
			);
			?>
		</div>
	<?php endif; ?>

	<div class="container pp-hero__inner">
		<?php if ( $eyebrow ) : ?>
			<span class="eyebrow eyebrow--clara"><?php echo esc_html( $eyebrow ); ?></span>
		<?php endif; ?>

		<h1 class="pp-hero__titulo"><?php echo esc_html( $titulo ); ?></h1>

		<?php if ( $texto ) : ?>
			<p class="pp-hero__texto"><?php echo cliconnect_enfase( $texto, 'strong' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_enfase escapa o texto antes de aplicar a tag. ?></p>
		<?php endif; ?>

		<div class="pp-hero__botoes">
			<?php cliconnect_botao_pagina( 'pp_hero_botao' ); ?>
			<?php cliconnect_botao_pagina( 'pp_hero_botao_2', 'botao botao--branco', '' ); ?>
		</div>

		<?php if ( $metricas ) : ?>
			<dl class="pp-hero__metricas">
				<?php foreach ( $metricas as $metrica ) : ?>
					<div class="pp-hero__metrica">
						<dt><?php echo esc_html( $metrica['numero'] ); ?></dt>
						<dd><?php echo esc_html( $metrica['rotulo'] ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
		<?php endif; ?>
	</div>
</section>
