<?php
/**
 * Post interna — Compartilhar: links das redes sociais e botão de copiar link.
 *
 * Só links de compartilhamento de cada rede: nenhum SDK ou script de terceiro
 * carrega na página. O botão de copiar depende de assets/js/single.js e fica
 * escondido sem JS (atributo hidden, removido pelo script).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cliconnect_url    = rawurlencode( get_permalink() );
$cliconnect_titulo = rawurlencode( wp_strip_all_tags( get_the_title() ) );

$cliconnect_redes = array(
	array(
		'rotulo' => __( 'Compartilhar no LinkedIn', 'cli' ),
		'icone'  => cliconnect_social_icon( 'linkedin' ),
		'url'    => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $cliconnect_url,
	),
	array(
		'rotulo' => __( 'Compartilhar no WhatsApp', 'cli' ),
		'icone'  => cliconnect_icone( 'whatsapp', 22 ),
		'url'    => 'https://api.whatsapp.com/send?text=' . $cliconnect_titulo . '%20' . $cliconnect_url,
	),
	array(
		'rotulo' => __( 'Compartilhar no X', 'cli' ),
		'icone'  => cliconnect_social_icon( 'x' ),
		'url'    => 'https://x.com/intent/post?url=' . $cliconnect_url . '&text=' . $cliconnect_titulo,
	),
	array(
		'rotulo' => __( 'Compartilhar no Facebook', 'cli' ),
		'icone'  => cliconnect_social_icon( 'facebook' ),
		'url'    => 'https://www.facebook.com/sharer/sharer.php?u=' . $cliconnect_url,
	),
	array(
		'rotulo' => __( 'Enviar por e-mail', 'cli' ),
		'icone'  => cliconnect_icone( 'email', 22 ),
		'url'    => 'mailto:?subject=' . $cliconnect_titulo . '&body=' . $cliconnect_url,
	),
);
?>

<div class="post-compartilhar">
	<span class="post-compartilhar__label">
		<?php esc_html_e( 'Compartilhar', 'cli' ); ?>
	</span>

	<ul class="post-compartilhar__lista">
		<?php foreach ( $cliconnect_redes as $cliconnect_rede ) : ?>
			<?php $cliconnect_externo = 0 === strpos( $cliconnect_rede['url'], 'https://' ); ?>
			<li>
				<a
					class="post-compartilhar__botao"
					href="<?php echo esc_url( $cliconnect_rede['url'], array( 'https', 'mailto' ) ); ?>"
					<?php if ( $cliconnect_externo ) : ?>
						target="_blank" rel="noopener noreferrer"
					<?php endif; ?>
					aria-label="<?php echo esc_attr( $cliconnect_rede['rotulo'] ); ?>"
					title="<?php echo esc_attr( $cliconnect_rede['rotulo'] ); ?>"
				>
					<?php echo $cliconnect_rede['icone']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG estático do tema (inc/icons.php, inc/template-tags.php). ?>
				</a>
			</li>
		<?php endforeach; ?>

		<li>
			<button
				type="button"
				class="post-compartilhar__botao"
				data-copiar-link="<?php echo esc_url( get_permalink() ); ?>"
				data-copiado="<?php esc_attr_e( 'Link copiado', 'cli' ); ?>"
				aria-label="<?php esc_attr_e( 'Copiar link', 'cli' ); ?>"
				title="<?php esc_attr_e( 'Copiar link', 'cli' ); ?>"
				hidden
			>
				<?php echo cliconnect_icone( 'link', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_icone retorna SVG estático do tema. ?>
			</button>
		</li>
	</ul>

	<span class="post-compartilhar__aviso" role="status" aria-live="polite"></span>
</div>
