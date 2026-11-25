<?php
/**
 * Case — bloco de vídeo de entrevista (issue #232).
 *
 * Capa + botão de play + legenda "Nome - Cargo". O play abre o vídeo num
 * overlay na própria página (assets/js/theme.js, initVideoOverlay): o
 * iframe do YouTube só é criado no clique. Sem URL, a capa aparece sozinha.
 *
 * Argumentos ($args):
 *   url   — URL do vídeo (YouTube).
 *   capa  — ID do anexo da capa.
 *   nome  — nome de quem fala.
 *   cargo — cargo de quem fala.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$url   = (string) ( $args['url'] ?? '' );
$capa  = absint( $args['capa'] ?? 0 );
$nome  = (string) ( $args['nome'] ?? '' );
$cargo = (string) ( $args['cargo'] ?? '' );

if ( ! $url && ! $capa ) {
	return;
}

$embed   = cliconnect_youtube_embed( $url );
$rotulo  = $nome ? sprintf( /* translators: %s: nome de quem fala no vídeo. */ __( 'Assistir ao vídeo de %s', 'cli' ), $nome ) : __( 'Assistir ao vídeo', 'cli' );
$icone   = cliconnect_imagem_tema( 'play-circle.svg', array( 'alt' => '', 'class' => 'case-video__play-icon' ) );
?>
<figure class="case-video-bloco">
	<div class="case-video">
		<?php if ( $capa ) : ?>
			<?php echo wp_get_attachment_image( $capa, 'full', false, array( 'class' => 'case-video__thumb', 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapa internamente. ?>
		<?php endif; ?>

		<?php if ( $url ) : ?>
			<?php // O link é o fallback sem JS; com JS, abre o overlay. ?>
			<a
				class="case-video__play"
				href="<?php echo esc_url( $url ); ?>"
				target="_blank"
				rel="noopener noreferrer"
				aria-label="<?php echo esc_attr( $rotulo ); ?>"
				<?php if ( $embed ) : ?>
					data-video-overlay="<?php echo esc_url( $embed ); ?>"
					data-video-fechar="<?php esc_attr_e( 'Fechar vídeo', 'cli' ); ?>"
				<?php endif; ?>
			>
				<?php echo $icone; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_imagem_tema retorna HTML seguro do tema. ?>
			</a>
		<?php else : ?>
			<span class="case-video__play" aria-hidden="true">
				<?php echo $icone; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_imagem_tema retorna HTML seguro do tema. ?>
			</span>
		<?php endif; ?>
	</div>

	<?php if ( $nome || $cargo ) : ?>
		<figcaption class="case-video__legenda">
			<?php if ( $nome ) : ?>
				<strong><?php echo esc_html( $nome ); ?></strong>
			<?php endif; ?>
			<?php if ( $nome && $cargo ) : ?>
				<span aria-hidden="true"> - </span>
			<?php endif; ?>
			<?php if ( $cargo ) : ?>
				<span><?php echo esc_html( $cargo ); ?></span>
			<?php endif; ?>
		</figcaption>
	<?php endif; ?>
</figure>
