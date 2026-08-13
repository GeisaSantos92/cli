<?php
/**
 * CLI Connect — Diferenciais.
 *
 * Header centralizado + tabela comparativa 3 colunas (labels | CLI Connect | Outras)
 * com 9 linhas de funcionalidades + botão CTA.
 * Colunas verticais independentes com altura fixa por linha garantem alinhamento.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'cc_dif_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'cc_dif_titulo' );
$texto   = cliconnect_campo_pagina( 'cc_dif_texto' );

if ( ! $titulo ) {
	return;
}

$linhas = array();
for ( $i = 1; $i <= 9; $i++ ) {
	$linha = cliconnect_campo_pagina( "cc_dif_row_{$i}" );
	if ( $linha ) {
		$linhas[] = $linha;
	}
}

$uri = get_template_directory_uri();
?>
<section class="cc-dif">
	<div class="container">

		<?php /* Header --------------------------------------------------------- */ ?>
		<header class="cc-dif__header">
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>

			<h2 class="cc-dif__titulo"><?php echo esc_html( $titulo ); ?></h2>

			<?php if ( $texto ) : ?>
				<p class="cc-dif__subtitulo"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>
		</header>

		<?php /* Tabela --------------------------------------------------------- */ ?>
		<div class="cc-dif__tabela">

			<?php /* Coluna 1 — Labels */ ?>
			<div class="cc-dif__col cc-dif__col--labels">
				<div class="cc-dif__th" aria-hidden="true"></div>
				<?php foreach ( $linhas as $linha ) : ?>
					<div class="cc-dif__td cc-dif__td--label">
						<?php echo esc_html( $linha ); ?>
					</div>
				<?php endforeach; ?>
			</div>

			<?php /* Coluna 2 — CLI Connect */ ?>
			<div class="cc-dif__col cc-dif__col--cli">
				<div class="cc-dif__th cc-dif__th--cli">
					<img src="<?php echo esc_url( $uri . '/assets/img/cc-dif-logo-cli.png' ); ?>"
						width="170" height="27"
						alt="CLI Connect"
						loading="lazy">
				</div>
				<?php foreach ( $linhas as $linha ) : ?>
					<div class="cc-dif__td cc-dif__td--cli">
						<span class="cc-dif__badge cc-dif__badge--check" aria-label="<?php esc_attr_e( 'Sim', 'cli' ); ?>">
							<span class="cc-dif__icone cc-dif__icone--check" aria-hidden="true"></span>
						</span>
					</div>
				<?php endforeach; ?>
			</div>

			<?php /* Coluna 3 — Outras plataformas */ ?>
			<div class="cc-dif__col cc-dif__col--outras">
				<div class="cc-dif__th cc-dif__th--outras">
					<?php esc_html_e( 'Outras plataformas', 'cli' ); ?>
				</div>
				<?php foreach ( $linhas as $linha ) : ?>
					<div class="cc-dif__td cc-dif__td--outras">
						<span class="cc-dif__badge cc-dif__badge--x" aria-label="<?php esc_attr_e( 'Não', 'cli' ); ?>">
							<span class="cc-dif__icone cc-dif__icone--x" aria-hidden="true"></span>
						</span>
					</div>
				<?php endforeach; ?>
			</div>

		</div>

		<?php /* CTA ------------------------------------------------------------ */ ?>
		<div class="cc-dif__cta">
			<?php cliconnect_botao_pagina( 'cc_dif_botao', 'botao botao--primario cc-dif__botao', 'seta-direita' ); ?>
		</div>

	</div>
</section>
