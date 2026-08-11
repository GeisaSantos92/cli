<?php
/**
 * Blog — Seção newsletter: CTA de assinatura de conteúdo.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<section class="blog-newsletter secao--azul">
	<div class="container">
		<div class="blog-newsletter__conteudo">

			<div class="blog-newsletter__texto">
				<h2 class="blog-newsletter__titulo">
					<?php esc_html_e( 'Se mantenha atualizado', 'cli' ); ?>
				</h2>
				<p class="blog-newsletter__subtitulo">
					<?php esc_html_e( 'Conteúdos, análises e boas práticas para apoiar decisões em tecnologia e operações da sua empresa', 'cli' ); ?>
				</p>
			</div>

			<div class="blog-newsletter__campo">
				<form class="blog-newsletter__form" method="post" action="<?php echo esc_url( get_permalink() ); ?>#newsletter" id="newsletter">
					<?php wp_nonce_field( 'blog_newsletter', 'blog_newsletter_nonce' ); ?>
					<div class="blog-newsletter__input-wrapper">
						<input
							type="email"
							name="newsletter_email"
							id="newsletter_email"
							class="blog-newsletter__input"
							placeholder="<?php esc_attr_e( 'Digite seu e-mail', 'cli' ); ?>"
							required
							aria-label="<?php esc_attr_e( 'Seu e-mail', 'cli' ); ?>"
						>
						<button type="submit" class="blog-newsletter__botao">
							<?php esc_html_e( 'Assinar', 'cli' ); ?>
						</button>
					</div>
					<p class="blog-newsletter__aviso">
						<?php esc_html_e( 'Ao enviar este formulário, você concorda com nossa Política de Privacidade.', 'cli' ); ?>
					</p>
				</form>
			</div>

		</div>
	</div>
</section>
