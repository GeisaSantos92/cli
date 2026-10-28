<?php
/**
 * Programa de Parceiros — Próximo passo (caixa azul com botão).
 *
 * Substitui o CTA azul global do rodapé nesta página (ver footer.php).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'pp_cta_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'pp_cta_titulo' );
$texto   = cliconnect_campo_pagina( 'pp_cta_texto' );

if ( ! $titulo ) {
	return;
}
?>
<section class="pp-cta">
	<div class="container">
		<div class="pp-cta__caixa">
			<div class="pp-cta__conteudo">
				<?php if ( $eyebrow ) : ?>
					<span class="eyebrow pp-cta__eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
				<?php endif; ?>
				<h2 class="pp-cta__titulo"><?php echo esc_html( $titulo ); ?></h2>
				<?php if ( $texto ) : ?>
					<p class="pp-cta__texto"><?php echo esc_html( $texto ); ?></p>
				<?php endif; ?>
			</div>
			<?php cliconnect_botao_pagina( 'pp_cta_botao', 'botao botao--branco' ); ?>
		</div>
	</div>
</section>
