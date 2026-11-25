<?php
/**
 * CLI Flow — CTA "Não achou o seu?".
 *
 * Faixa arredondada dentro do container, degradê azul-marinho: duas linhas
 * de texto à esquerda (a segunda em negrito) e botão primário à direita.
 * Sem texto fixo: todo conteúdo vem do ACF (aba "7 · CTA IA").
 *
 * Campos ACF (group_cli_cli_flow):
 *   cf_ia_titulo          — linha 1 (peso regular)
 *   cf_ia_titulo_destaque — linha 2 (negrito)
 *   cf_ia_botao           — link do botão CTA
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo          = cliconnect_campo_pagina( 'cf_ia_titulo' );
$titulo_destaque = cliconnect_campo_pagina( 'cf_ia_titulo_destaque' );
$botao           = cliconnect_campo_pagina( 'cf_ia_botao' );

if ( ! $titulo && ! $titulo_destaque && ! $botao ) {
	return;
}
?>
<section class="cf-ia-cta">
	<div class="container">
		<div class="cf-ia-cta__faixa">

			<?php if ( $titulo || $titulo_destaque ) : ?>
				<h2 class="cf-ia-cta__titulo">
					<?php if ( $titulo ) : ?>
						<span class="cf-ia-cta__titulo-linha"><?php echo esc_html( $titulo ); ?></span>
					<?php endif; ?>
					<?php if ( $titulo_destaque ) : ?>
						<strong class="cf-ia-cta__titulo-linha cf-ia-cta__titulo-linha--destaque"><?php echo esc_html( $titulo_destaque ); ?></strong>
					<?php endif; ?>
				</h2>
			<?php endif; ?>

			<?php if ( $botao ) : ?>
				<?php cliconnect_botao_pagina( 'cf_ia_botao', 'botao botao--primario cf-ia-cta__botao', 'seta-direita' ); ?>
			<?php endif; ?>

		</div>
	</div>
</section>
