<?php
/**
 * Contato — Formulário.
 *
 * Seção de duas colunas: esquerda com título, texto e cards de contato;
 * direita com formulário CF7.
 *
 * Campos ACF (group_cli_contato, aba "1 · Formulário"):
 *   ct_form_titulo       — título principal
 *   ct_form_texto        — parágrafo de apoio
 *   ct_form_email        — e-mail de contato (exibido no card)
 *   ct_form_telefone     — telefone (exibido no card)
 *   ct_form_facebook_url — URL do Facebook
 *   ct_form_instagram_url — URL do Instagram
 *   ct_form_whatsapp_url — URL do WhatsApp
 *   ct_form_cf7_id       — ID do formulário Contact Form 7
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo   = cliconnect_campo_pagina( 'ct_form_titulo' );
$texto    = cliconnect_campo_pagina( 'ct_form_texto' );
$email    = cliconnect_campo_pagina( 'ct_form_email' );
$telefone = cliconnect_campo_pagina( 'ct_form_telefone' );
$fb_url   = cliconnect_campo_pagina( 'ct_form_facebook_url' );
$ig_url   = cliconnect_campo_pagina( 'ct_form_instagram_url' );
$wa_url   = cliconnect_campo_pagina( 'ct_form_whatsapp_url' );

// ID do formulário CF7: usa o campo ACF; se vazio, busca pelo slug do seed.
$cf7_id = (int) ( cliconnect_campo_pagina( 'ct_form_cf7_id', 0 ) );
if ( ! $cf7_id && post_type_exists( 'wpcf7_contact_form' ) ) {
	$cf7_posts = get_posts(
		array(
			'post_type'      => 'wpcf7_contact_form',
			'name'           => 'contato-cli',
			'posts_per_page' => 1,
			'post_status'    => 'publish',
		)
	);
	$cf7_id = $cf7_posts ? (int) $cf7_posts[0]->ID : 0;
}

if ( ! $titulo ) {
	return;
}
?>
<section class="ct-formulario">

	<div class="container ct-formulario__inner">

		<!-- Coluna esquerda: informações de contato -->
		<div class="ct-formulario__col-info">

			<h1 class="ct-formulario__titulo"><?php echo esc_html( $titulo ); ?></h1>

			<?php if ( $texto ) : ?>
				<p class="ct-formulario__texto"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>

			<div class="ct-formulario__cards">

				<?php if ( $email || $telefone ) : ?>
					<div class="ct-card">
						<p class="ct-card__titulo"><?php esc_html_e( 'Entre em contato', 'cli' ); ?></p>
						<div class="ct-card__itens">
							<?php if ( $email ) : ?>
								<div class="ct-card__item">
									<span class="ct-card__label"><?php esc_html_e( 'E-mail', 'cli' ); ?></span>
									<a class="ct-card__valor" href="mailto:<?php echo esc_attr( $email ); ?>">
										<?php echo esc_html( $email ); ?>
									</a>
								</div>
							<?php endif; ?>
							<?php if ( $telefone ) : ?>
								<div class="ct-card__item">
									<span class="ct-card__label"><?php esc_html_e( 'Telefone', 'cli' ); ?></span>
									<span class="ct-card__valor"><?php echo esc_html( $telefone ); ?></span>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $fb_url || $ig_url || $wa_url ) : ?>
					<div class="ct-card">
						<p class="ct-card__titulo"><?php esc_html_e( 'Nossas redes', 'cli' ); ?></p>
						<div class="ct-redes">
							<?php if ( $fb_url ) : ?>
								<a class="ct-rede"
								   href="<?php echo esc_url( $fb_url ); ?>"
								   target="_blank"
								   rel="noopener noreferrer"
								   aria-label="<?php esc_attr_e( 'Facebook', 'cli' ); ?>">
									<img src="<?php echo esc_url( get_theme_file_uri( '/assets/img/social-facebook.svg' ) ); ?>" width="40" height="40" alt="" aria-hidden="true">
								</a>
							<?php endif; ?>
							<?php if ( $ig_url ) : ?>
								<a class="ct-rede"
								   href="<?php echo esc_url( $ig_url ); ?>"
								   target="_blank"
								   rel="noopener noreferrer"
								   aria-label="<?php esc_attr_e( 'Instagram', 'cli' ); ?>">
									<img src="<?php echo esc_url( get_theme_file_uri( '/assets/img/social-instagram.svg' ) ); ?>" width="40" height="40" alt="" aria-hidden="true">
								</a>
							<?php endif; ?>
							<?php if ( $wa_url ) : ?>
								<a class="ct-rede"
								   href="<?php echo esc_url( $wa_url ); ?>"
								   target="_blank"
								   rel="noopener noreferrer"
								   aria-label="<?php esc_attr_e( 'WhatsApp', 'cli' ); ?>">
									<img src="<?php echo esc_url( get_theme_file_uri( '/assets/img/social-whatsapp.svg' ) ); ?>" width="40" height="40" alt="" aria-hidden="true">
								</a>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

			</div><!-- .ct-formulario__cards -->

		</div><!-- .ct-formulario__col-info -->

		<!-- Coluna direita: formulário CF7 -->
		<div class="ct-formulario__col-form">
			<div class="ct-form-wrapper">
				<?php if ( $cf7_id && shortcode_exists( 'contact-form-7' ) ) : ?>
					<?php echo do_shortcode( '[contact-form-7 id="' . absint( $cf7_id ) . '"]' ); ?>
				<?php else : ?>
					<p class="ct-form-aviso">
						<?php esc_html_e( 'Formulário não configurado. Insira o ID do formulário CF7 no painel ACF.', 'cli' ); ?>
					</p>
				<?php endif; ?>
			</div>
		</div><!-- .ct-formulario__col-form -->

	</div><!-- .ct-formulario__inner -->

</section><!-- .ct-formulario -->
