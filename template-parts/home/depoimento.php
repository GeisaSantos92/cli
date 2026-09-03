<?php
/**
 * Home — case em destaque (card azul) + dois cards de métrica.
 *
 * Os três cards vêm do CPT cli_case, escolhidos nos campos da home.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$destaque_id = absint( cliconnect_campo( 'case_destaque', 0 ) );
$metricas    = (array) cliconnect_campo( 'cases_metricas', array() );

if ( ! $destaque_id && ! $metricas ) {
	return;
}
?>

<section class="secao depoimento">
	<div class="container">

		<?php if ( $destaque_id ) : ?>
			<?php
			$logo    = absint( get_field( 'logo', $destaque_id ) ?? 0 );
			$citacao = get_field( 'citacao', $destaque_id ) ?? '';
			$autor   = get_field( 'autor', $destaque_id ) ?? '';
			$cargo   = get_field( 'cargo', $destaque_id ) ?? '';
			$retrato = absint( get_field( 'retrato', $destaque_id ) ?? 0 );
			$video   = get_field( 'video', $destaque_id ) ?? '';
			?>
			<article class="depoimento__destaque">

				<div class="depoimento__conteudo">
					<?php if ( $logo ) : ?>
						<span class="depoimento__logo">
							<?php echo wp_get_attachment_image( $logo, 'cli-logo', false, array( 'alt' => get_the_title( $destaque_id ) ) ); ?>
						</span>
					<?php endif; ?>

					<?php if ( $citacao ) : ?>
						<blockquote class="depoimento__citacao">&ldquo;<?php echo esc_html( $citacao ); ?>&rdquo;</blockquote>
					<?php endif; ?>

					<div>
						<?php if ( $autor ) : ?>
							<p class="depoimento__autor"><?php echo esc_html( $autor ); ?></p>
						<?php endif; ?>
						<?php if ( $cargo ) : ?>
							<p class="depoimento__cargo"><?php echo esc_html( $cargo ); ?></p>
						<?php endif; ?>
					</div>
				</div>

				<?php if ( $retrato ) : ?>
				<div class="depoimento__midia">
					<?php echo wp_get_attachment_image( $retrato, 'large', false, array( 'alt' => '' ) ); ?>

					<?php if ( $video ) : ?>
						<a
							class="depoimento__play"
							href="<?php echo esc_url( $video ); ?>"
							target="_blank"
							rel="noopener noreferrer"
						>
							<span><?php echo cliconnect_icone( 'play', 30 ); // SVG estático. ?></span>
							<span class="visually-hidden">
								<?php
								/* translators: %s: nome do case */
								printf( esc_html__( 'Assistir ao depoimento de %s', 'cli' ), esc_html( get_the_title( $destaque_id ) ) );
								?>
							</span>
						</a>
					<?php endif; ?>
				</div>
				<?php endif; ?>

			</article>
		<?php endif; ?>

		<?php if ( $metricas ) : ?>
		<div class="depoimento__metricas">
			<?php foreach ( $metricas as $case_id ) : ?>
				<?php
				$case_id = absint( $case_id );
				$numero  = get_field( 'metrica_numero', $case_id ) ?? '';
				$texto   = get_field( 'metrica_texto', $case_id ) ?? '';
				$logo    = absint( get_field( 'logo', $case_id ) ?? 0 );
				?>
				<article class="case-metrica">
					<?php if ( $numero ) : ?>
						<p class="case-metrica__numero"><?php echo esc_html( $numero ); ?></p>
					<?php endif; ?>

					<?php if ( $texto ) : ?>
						<p class="case-metrica__texto"><?php echo esc_html( $texto ); ?></p>
					<?php endif; ?>

					<?php if ( $logo ) : ?>
						<span class="case-metrica__logo">
							<?php echo wp_get_attachment_image( $logo, 'cli-logo', false, array( 'alt' => get_the_title( $case_id ) ) ); ?>
						</span>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<div class="depoimento__acoes">
			<?php cliconnect_botao( 'cases_botao' ); ?>
		</div>

	</div>
</section>
