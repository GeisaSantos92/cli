<?php
/**
 * CLI Flow — CTA final ("Próximo passo").
 *
 * Cartão azul arredondado dentro do container, com recortes diagonais
 * claros à direita: eyebrow + título em duas linhas + texto à esquerda e
 * botão branco à direita. É a última seção antes do rodapé — nesta página
 * o CTA azul global do rodapé não aparece (ver footer.php).
 *
 * Campos ACF (group_cli_cli_flow, aba "17 · CTA Final"):
 *   cf_cta_eyebrow / cf_cta_titulo / cf_cta_titulo_destaque / cf_cta_texto / cf_cta_botao
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow  = cliconnect_campo_pagina( 'cf_cta_eyebrow' );
$titulo   = cliconnect_campo_pagina( 'cf_cta_titulo' );
$destaque = cliconnect_campo_pagina( 'cf_cta_titulo_destaque' );
$texto    = cliconnect_campo_pagina( 'cf_cta_texto' );

if ( ! $titulo && ! $destaque ) {
	return;
}
?>
<section class="cf-cta-final">
	<div class="container">
		<div class="cf-cta-final__cartao">

			<div class="cf-cta-final__conteudo">
				<?php if ( $eyebrow ) : ?>
					<p class="cf-cta-final__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>
				<h2 class="cf-cta-final__titulo">
					<?php if ( $titulo ) : ?>
						<span class="cf-cta-final__titulo-linha"><?php echo esc_html( $titulo ); ?></span>
					<?php endif; ?>
					<?php if ( $destaque ) : ?>
						<span class="cf-cta-final__titulo-linha"><?php echo esc_html( $destaque ); ?></span>
					<?php endif; ?>
				</h2>
				<?php if ( $texto ) : ?>
					<p class="cf-cta-final__texto"><?php echo esc_html( $texto ); ?></p>
				<?php endif; ?>
			</div>

			<?php cliconnect_botao_pagina( 'cf_cta_botao', 'botao botao--branco cf-cta-final__botao', 'seta-direita' ); ?>

		</div>
	</div>
</section>
