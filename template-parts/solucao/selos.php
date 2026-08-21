<?php
/**
 * Solução — Seção Selos (Compliance & Segurança).
 *
 * Campos ACF (group_cli_solucao, aba "6 · Selos"):
 *   solucao_selos_eyebrow, solucao_selos_titulo, solucao_selos_corpo.
 *
 * Os badges de certificação são assets estáticos do tema (mesmos em todas as
 * páginas de solução) — ficam em assets/img/selos-*.png.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'solucao_selos_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'solucao_selos_titulo' );
$corpo   = cliconnect_campo_pagina( 'solucao_selos_corpo' );

if ( ! $titulo && ! $corpo ) {
	return;
}

/**
 * Badges: cada item define o arquivo de imagem e o alt text.
 * O badge "iso-sprite" usa crop CSS para mostrar duas variantes diferentes
 * da mesma imagem (ISO 27701 na linha 1 col 3; ISO 27018 na linha 1 col 5).
 */
$badges = array(
	// Linha 1.
	array( 'img' => 'selos-soc2.png',       'alt' => 'SOC 2',        'crop' => '' ),
	array( 'img' => 'selos-soc.png',        'alt' => 'SOC',          'crop' => '' ),
	array( 'img' => 'selos-iso-sprite.png', 'alt' => 'ISO 27701',    'crop' => 'iso-27701' ),
	array( 'img' => 'selos-iso27001.png',   'alt' => 'ISO 27001',    'crop' => '' ),
	array( 'img' => 'selos-iso-sprite.png', 'alt' => 'ISO 27018',    'crop' => 'iso-27018' ),
	// Linha 2.
	array( 'img' => 'selos-eu-gdpr.png',    'alt' => 'EU GDPR',      'crop' => '' ),
	array( 'img' => 'selos-fedramp.png',    'alt' => 'FedRAMP',      'crop' => '' ),
	array( 'img' => 'selos-stateramp.png',  'alt' => 'StateRAMP',    'crop' => '' ),
	array( 'img' => 'selos-irap.png',       'alt' => 'IRAP',         'crop' => '' ),
	array( 'img' => 'selos-pci-dss.png',    'alt' => 'PCI DSS',      'crop' => '' ),
);
?>
<section class="ss-selos" aria-label="<?php esc_attr_e( 'Compliance e Segurança', 'cli' ); ?>">
	<div class="container">
		<div class="ss-selos__inner">

			<div class="ss-selos__header">
				<?php if ( $eyebrow ) : ?>
					<p class="eyebrow ss-selos__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>

				<?php if ( $titulo ) : ?>
					<h2 class="ss-selos__titulo"><?php echo esc_html( $titulo ); ?></h2>
				<?php endif; ?>

				<?php if ( $corpo ) : ?>
					<p class="ss-selos__corpo"><?php echo esc_html( $corpo ); ?></p>
				<?php endif; ?>
			</div>

			<div class="ss-selos__grade" aria-label="<?php esc_attr_e( 'Certificações', 'cli' ); ?>">
				<?php foreach ( $badges as $badge ) :
					$src = get_theme_file_uri( '/assets/img/' . $badge['img'] );
				?>
					<div class="ss-selos__celula">
						<?php if ( $badge['crop'] ) : ?>
							<div class="ss-selos__badge-crop ss-selos__badge-crop--<?php echo esc_attr( $badge['crop'] ); ?>" aria-label="<?php echo esc_attr( $badge['alt'] ); ?>">
								<img
									src="<?php echo esc_url( $src ); ?>"
									alt="<?php echo esc_attr( $badge['alt'] ); ?>"
									class="ss-selos__badge-sprite"
									loading="lazy"
								>
							</div>
						<?php else : ?>
							<img
								src="<?php echo esc_url( $src ); ?>"
								alt="<?php echo esc_attr( $badge['alt'] ); ?>"
								class="ss-selos__badge"
								loading="lazy"
								width="120"
								height="120"
							>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>

		</div>
	</div>
</section>
