<?php
/**
 * CLI Signature — Compliance & Segurança (Selos).
 *
 * Texto global via Customizer (campos compliance_*).
 * Imagens via CPT cli_selo, ordenadas por menu_order.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$selos = cliconnect_posts( 'cli_selo' );

if ( ! $selos ) {
	return;
}

$eyebrow = cliconnect_campo( 'compliance_eyebrow' );
$titulo  = cliconnect_campo( 'compliance_titulo' );
$texto   = cliconnect_campo( 'compliance_texto' );
?>
<section class="cs-selos">
	<div class="container cs-selos__inner">

		<div class="cs-selos__header">
			<?php if ( $eyebrow ) : ?>
				<p class="cs-selos__eyebrow eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<?php if ( $titulo ) : ?>
				<h2 class="cs-selos__titulo"><?php echo esc_html( $titulo ); ?></h2>
			<?php endif; ?>

			<?php if ( $texto ) : ?>
				<p class="cs-selos__subtitulo"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>
		</div>

		<div class="cs-selos__card">
			<ul class="cs-selos__grid" role="list">
				<?php foreach ( $selos as $selo ) : ?>
					<li class="cs-selo-item">
						<?php
						echo wp_get_attachment_image(
							get_post_thumbnail_id( $selo->ID ),
							'medium',
							false,
							array(
								'alt'     => get_the_title( $selo ),
								'loading' => 'lazy',
							)
						);
						?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

	</div>
</section>
