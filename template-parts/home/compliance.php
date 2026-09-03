<?php
/**
 * Home — "Lideramos o mercado quando assunto é compliance e segurança".
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$selos = cliconnect_posts( 'cli_selo', 10 );

if ( ! $selos ) {
	return;
}

$eyebrow = cliconnect_campo( 'compliance_eyebrow' );
$titulo  = cliconnect_campo( 'compliance_titulo' );
$texto   = cliconnect_campo( 'compliance_texto' );
?>

<section class="secao compliance">
	<div class="container">

		<header class="secao__cabecalho">
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<?php if ( $titulo ) : ?>
				<h2 class="secao__titulo compliance__titulo"><?php echo esc_html( $titulo ); ?></h2>
			<?php endif; ?>
			<?php if ( $texto ) : ?>
				<p class="secao__descricao"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>
		</header>

		<div class="compliance__grid">
			<?php foreach ( $selos as $selo ) : ?>
				<div class="compliance__selo">
					<?php
					echo cliconnect_thumb( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_thumb é wrapper de wp_get_attachment_image que escapa internamente.
						$selo->ID,
						'medium',
						array( 'alt' => get_the_title( $selo ) )
					);
					?>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>
