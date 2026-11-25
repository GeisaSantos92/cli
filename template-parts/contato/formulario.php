<?php
/**
 * Contato — Formulário.
 *
 * Seção de duas colunas: esquerda com título, texto e cards de contato;
 * direita com o formulário de lead (Web-to-Lead do Salesforce,
 * template-parts/form-lead.php).
 *
 * Campos ACF (group_cli_contato, aba "1 · Formulário"):
 *   ct_form_titulo       — título principal
 *   ct_form_texto        — parágrafo de apoio
 *   ct_form_email        — e-mail de contato (exibido no card)
 *   ct_form_telefone     — telefone (exibido no card)
 *   ct_form_linkedin_url — URL do LinkedIn
 *   ct_form_instagram_url — URL do Instagram
 *   ct_form_whatsapp_url — URL do WhatsApp (o ícone abre o popup de lead)
 *
 * Depois do envio o Salesforce devolve o visitante para esta página com
 * `?enviado=1` (cliconnect_lead_retorno_contato()), e a coluna do formulário
 * mostra a confirmação no lugar dos campos.
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
$li_url   = cliconnect_campo_pagina( 'ct_form_linkedin_url' );
$ig_url   = cliconnect_campo_pagina( 'ct_form_instagram_url' );
$wa_url   = cliconnect_campo_pagina( 'ct_form_whatsapp_url' );

// Com popup disponível (WhatsApp do Customizer), o ícone passa por ele antes da conversa.
$wa_popup = $wa_url && cliconnect_e_link_whatsapp( $wa_url ) && ( get_theme_mod( 'cliconnect_whatsapp_url' ) ?? '' );

if ( ! $titulo ) {
	return;
}
?>
<section class="ct-formulario" id="formulario">

	<img class="ct-formulario__deco" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/ct-form-deco.svg' ); ?>" alt="" aria-hidden="true" loading="lazy" decoding="async">

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
									<a class="ct-card__valor" href="<?php echo esc_url( cliconnect_tel_href( $telefone ) ); ?>">
										<?php echo esc_html( $telefone ); ?>
									</a>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $li_url || $ig_url || $wa_url ) : ?>
					<div class="ct-card">
						<p class="ct-card__titulo"><?php esc_html_e( 'Nossas redes', 'cli' ); ?></p>
						<div class="ct-redes">
							<?php if ( $li_url ) : ?>
								<a class="ct-rede"
								   href="<?php echo esc_url( $li_url ); ?>"
								   target="_blank"
								   rel="noopener noreferrer"
								   aria-label="<?php esc_attr_e( 'LinkedIn', 'cli' ); ?>">
									<img src="<?php echo esc_url( get_theme_file_uri( '/assets/img/social-linkedin.svg' ) ); ?>" width="40" height="40" alt="" aria-hidden="true">
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
								   <?php if ( $wa_popup ) : ?>data-lead-popup aria-haspopup="dialog"<?php endif; ?>
								   aria-label="<?php esc_attr_e( 'WhatsApp', 'cli' ); ?>">
									<img src="<?php echo esc_url( get_theme_file_uri( '/assets/img/social-whatsapp.svg' ) ); ?>" width="40" height="40" alt="" aria-hidden="true">
								</a>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

			</div><!-- .ct-formulario__cards -->

		</div><!-- .ct-formulario__col-info -->

		<!-- Coluna direita: formulário de lead (Salesforce) -->
		<div class="ct-formulario__col-form">
			<div class="ct-form-wrapper">
				<?php if ( cliconnect_lead_enviado() ) : ?>
					<div class="form-lead__sucesso" role="status">
						<p class="form-lead__sucesso-titulo"><?php esc_html_e( 'Mensagem enviada!', 'cli' ); ?></p>
						<p><?php esc_html_e( 'Recebemos seus dados. Em breve nossa equipe entrará em contato.', 'cli' ); ?></p>
					</div>
				<?php else : ?>
					<?php
					get_template_part(
						'template-parts/form-lead',
						null,
						array(
							'retorno' => cliconnect_lead_retorno_contato(),
							'origem'  => 'contato',
							'prefixo' => 'ct-lead',
						)
					);
					?>
				<?php endif; ?>
			</div>
		</div><!-- .ct-formulario__col-form -->

	</div><!-- .ct-formulario__inner -->

</section><!-- .ct-formulario -->
