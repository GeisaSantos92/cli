<?php
/**
 * Card de Solução — usado nas listagens archive e taxonomy.
 *
 * Dados:
 *   - Título do post (nome da solução)
 *   - Imagem destacada (logo)
 *   - ACF: solucao_descricao, solucao_url
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$descricao   = get_field( 'solucao_descricao' ) ?? '';
$url_solucao = get_field( 'solucao_url' ) ?? '';
$url         = $url_solucao ?: get_permalink();
$nome        = get_the_title();
?>
<article class="sl-card">
	<a class="sl-card__link" href="<?php echo esc_url( $url ); ?>"
	   <?php echo $url_solucao ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>

		<div class="sl-card__logo">
			<?php
			if ( has_post_thumbnail() ) {
				echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapa internamente.
					get_post_thumbnail_id(),
					'medium',
					false,
					array(
						'alt'     => esc_attr( $nome ),
						'loading' => 'lazy',
					)
				);
			} else {
				echo '<span class="sl-card__sem-logo">' . esc_html( mb_substr( $nome, 0, 2 ) ) . '</span>';
			}
			?>
		</div>

		<div class="sl-card__info">
			<p class="sl-card__nome"><?php echo esc_html( $nome ); ?></p>
			<?php if ( $descricao ) : ?>
				<p class="sl-card__descricao"><?php echo esc_html( $descricao ); ?></p>
			<?php endif; ?>
		</div>

	</a>
</article>
