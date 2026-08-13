<?php
/**
 * CLI Connect — Parceiro Oficial (Comparativo).
 *
 * Card branco de duas colunas: CLI Connect × Desenvolvimento tradicional.
 * Cada coluna: header centralizado + [gráfico | itens] lado a lado.
 * Banner de destaque absolutamente posicionado no rodapé do card.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'cc_parceiro_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'cc_parceiro_titulo' );
$texto   = cliconnect_campo_pagina( 'cc_parceiro_texto' );

if ( ! $titulo ) {
	return;
}

$esq_titulo = cliconnect_campo_pagina( 'cc_parceiro_esq_titulo' );
$esq_sub    = cliconnect_campo_pagina( 'cc_parceiro_esq_sub' );
$dir_titulo = cliconnect_campo_pagina( 'cc_parceiro_dir_titulo' );
$dir_sub    = cliconnect_campo_pagina( 'cc_parceiro_dir_sub' );
$destaque   = cliconnect_campo_pagina( 'cc_parceiro_destaque' );

$uri = get_template_directory_uri();

$grafico_cli = cliconnect_imagem_tema(
	'cc-parceiro-grafico-cli.png',
	array(
		'class'   => 'cc-parceiro__grafico',
		'alt'     => '',
		'loading' => 'eager',
	)
);

$grafico_dev = cliconnect_imagem_tema(
	'cc-parceiro-grafico-dev.png',
	array(
		'class'   => 'cc-parceiro__grafico',
		'alt'     => '',
		'loading' => 'eager',
	)
);
?>
<section class="cc-parceiro">
	<div class="container">

		<div class="cc-parceiro__header">
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>

			<h2 class="cc-parceiro__titulo"><?php echo esc_html( $titulo ); ?></h2>

			<?php if ( $texto ) : ?>
				<p class="cc-parceiro__corpo"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>
		</div>

		<div class="cc-parceiro__card">

			<?php /* Coluna esquerda — CLI Connect ----------------------------- */ ?>
			<div class="cc-parceiro__col cc-parceiro__col--cli">

				<div class="cc-parceiro__col-header">
					<?php if ( $esq_titulo ) : ?>
						<strong class="cc-parceiro__col-nome cc-parceiro__col-nome--cli">
							<?php echo esc_html( $esq_titulo ); ?>
						</strong>
					<?php endif; ?>
					<?php if ( $esq_sub ) : ?>
						<span class="cc-parceiro__col-sub"><?php echo esc_html( $esq_sub ); ?></span>
					<?php endif; ?>
				</div>

				<div class="cc-parceiro__col-inner">
					<?php if ( $grafico_cli ) : ?>
						<div class="cc-parceiro__grafico-wrap">
							<?php echo $grafico_cli; // phpcs:ignore WordPress.Security.EscapeOutput -- montado com escape em cliconnect_imagem_tema(). ?>
						</div>
					<?php endif; ?>

					<ul class="cc-parceiro__lista">
						<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
							<?php $item = cliconnect_campo_pagina( "cc_parceiro_esq_item_{$i}" ); ?>
							<?php if ( $item ) : ?>
							<li class="cc-parceiro__item">
								<span class="cc-parceiro__pill cc-parceiro__pill--cli">
									<span class="cc-parceiro__icone cc-parceiro__icone--esq-<?php echo (int) $i; ?>" aria-hidden="true"></span>
								</span>
								<span class="cc-parceiro__item-texto"><?php echo esc_html( $item ); ?></span>
							</li>
							<?php endif; ?>
						<?php endfor; ?>
					</ul>
				</div>

			</div>

			<?php /* VS badge + divisor --------------------------------------- */ ?>
			<div class="cc-parceiro__vs-wrap" aria-hidden="true">
				<span class="cc-parceiro__vs">VS</span>
			</div>

			<?php /* Coluna direita — Desenvolvimento ------------------------- */ ?>
			<div class="cc-parceiro__col cc-parceiro__col--dev">

				<div class="cc-parceiro__col-header">
					<?php if ( $dir_titulo ) : ?>
						<strong class="cc-parceiro__col-nome cc-parceiro__col-nome--dev">
							<?php echo esc_html( $dir_titulo ); ?>
						</strong>
					<?php endif; ?>
					<?php if ( $dir_sub ) : ?>
						<span class="cc-parceiro__col-sub"><?php echo esc_html( $dir_sub ); ?></span>
					<?php endif; ?>
				</div>

				<div class="cc-parceiro__col-inner">
					<?php if ( $grafico_dev ) : ?>
						<div class="cc-parceiro__grafico-wrap">
							<?php echo $grafico_dev; // phpcs:ignore WordPress.Security.EscapeOutput -- montado com escape em cliconnect_imagem_tema(). ?>
						</div>
					<?php endif; ?>

					<ul class="cc-parceiro__lista">
						<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
							<?php $item = cliconnect_campo_pagina( "cc_parceiro_dir_item_{$i}" ); ?>
							<?php if ( $item ) : ?>
							<li class="cc-parceiro__item">
								<span class="cc-parceiro__pill cc-parceiro__pill--dev">
									<span class="cc-parceiro__icone cc-parceiro__icone--dir-<?php echo (int) $i; ?>" aria-hidden="true"></span>
								</span>
								<span class="cc-parceiro__item-texto cc-parceiro__item-texto--dev"><?php echo esc_html( $item ); ?></span>
							</li>
							<?php endif; ?>
						<?php endfor; ?>
					</ul>
				</div>

			</div>

			<?php /* Banner de destaque --------------------------------------- */ ?>
			<?php if ( $destaque ) : ?>
			<div class="cc-parceiro__destaque">
				<img src="<?php echo esc_url( $uri . '/assets/img/cc-parceiro-icon-destaque.svg' ); ?>"
					class="cc-parceiro__destaque-icone" width="40" height="40"
					alt="" aria-hidden="true" focusable="false">
				<p class="cc-parceiro__destaque-texto">
					<?php echo wp_kses( $destaque, array( 'strong' => array() ) ); ?>
				</p>
			</div>
			<?php endif; ?>

		</div>
	</div>
</section>
