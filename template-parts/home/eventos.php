<?php
/**
 * Home — "Sua operação responde em tempo real às mudanças do negócio".
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eventos = cliconnect_posts( 'cli_evento' );

if ( ! $eventos ) {
	return;
}

$eyebrow = cliconnect_campo( 'eventos_eyebrow' );
$titulo  = cliconnect_campo( 'eventos_titulo' );
?>

<section class="secao eventos">
	<div class="container">

		<header class="secao__cabecalho">
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<?php if ( $titulo ) : ?>
				<h2 class="secao__titulo"><?php echo esc_html( $titulo ); ?></h2>
			<?php endif; ?>
		</header>

		<div class="eventos__grid">
			<?php foreach ( $eventos as $evento ) : ?>
				<?php $descricao = get_field( 'descricao', $evento->ID ) ?? ''; ?>
				<article class="evento-card">

					<?php if ( has_post_thumbnail( $evento->ID ) ) : ?>
						<div class="evento-card__imagem">
							<?php echo cliconnect_thumb( $evento->ID, 'large', array( 'alt' => get_the_title( $evento ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_thumb é wrapper de wp_get_attachment_image que escapa internamente. ?>
						</div>
					<?php endif; ?>

					<div class="evento-card__corpo">
						<h3 class="evento-card__titulo"><?php echo esc_html( get_the_title( $evento ) ); ?></h3>
						<?php if ( $descricao ) : ?>
							<p class="evento-card__texto"><?php echo esc_html( $descricao ); ?></p>
						<?php endif; ?>
					</div>

				</article>
			<?php endforeach; ?>
		</div>

	</div>
</section>
