<?php
/**
 * CLI Connect — Vantagens.
 *
 * Header centralizado (eyebrow + H3 + subtítulo) + grid 3×2 de cards.
 * Cada card: ícone SVG (mask-image) + título + texto descritivo.
 * Ícones são decorativos e fixos em assets/img/cc-vantag-icon-{n}.svg.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'cc_vantag_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'cc_vantag_titulo' );
$texto   = cliconnect_campo_pagina( 'cc_vantag_texto' );

if ( ! $titulo ) {
	return;
}

$uri = get_template_directory_uri();
?>
<section class="cc-vantagens">
	<div class="container">

		<header class="cc-vantagens__header">
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>

			<h2 class="cc-vantagens__titulo"><?php echo esc_html( $titulo ); ?></h2>

			<?php if ( $texto ) : ?>
				<p class="cc-vantagens__subtitulo"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>
		</header>

		<div class="cc-vantagens__grid">
			<?php for ( $i = 1; $i <= 6; $i++ ) : ?>
				<?php
				$card_titulo = cliconnect_campo_pagina( "cc_vantag_{$i}_titulo" );
				$card_texto  = cliconnect_campo_pagina( "cc_vantag_{$i}_texto" );
				if ( ! $card_titulo ) {
					continue;
				}
				?>
				<div class="cc-vantagens__card">
					<div class="cc-vantagens__icone-wrap" aria-hidden="true">
						<span class="cc-vantagens__icone cc-vantagens__icone--<?php echo (int) $i; ?>"></span>
					</div>
					<h3 class="cc-vantagens__card-titulo"><?php echo esc_html( $card_titulo ); ?></h3>
					<?php if ( $card_texto ) : ?>
						<p class="cc-vantagens__card-texto"><?php echo esc_html( $card_texto ); ?></p>
					<?php endif; ?>
				</div>
			<?php endfor; ?>
		</div>

	</div>
</section>
