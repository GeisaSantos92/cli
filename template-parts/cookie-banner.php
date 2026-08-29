<?php
/**
 * Banner de cookies — cartão flutuante no canto inferior esquerdo.
 *
 * Informativo, com um clique: o texto, o rótulo do link e o do botão vêm do
 * Customizer (traduzidos pelo Polylang). Quem esconde e lembra a escolha é o
 * theme.js, via localStorage — sem cookie e sem plugin de CMP.
 *
 * O canto esquerdo é proposital: o WhatsApp e o "voltar ao topo" ficam à
 * direita.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cliconnect_texto = cliconnect_mod_traduzido( 'cliconnect_cookies_texto' );

if ( ! $cliconnect_texto ) {
	return;
}

$cliconnect_botao      = cliconnect_mod_traduzido( 'cliconnect_cookies_botao' );
$cliconnect_link_texto = cliconnect_mod_traduzido( 'cliconnect_cookies_link_texto' );
$cliconnect_link_url   = cliconnect_url_privacidade();
?>

<aside
	class="cookie-banner"
	data-cookie-banner
	hidden
	aria-label="<?php esc_attr_e( 'Aviso de cookies', 'cli' ); ?>"
>
	<p class="cookie-banner__texto">
		<?php echo esc_html( $cliconnect_texto ); ?>
		<?php if ( $cliconnect_link_texto && $cliconnect_link_url ) : ?>
			<a class="cookie-banner__link" href="<?php echo esc_url( $cliconnect_link_url ); ?>"><?php echo esc_html( $cliconnect_link_texto ); ?></a>.
		<?php endif; ?>
	</p>

	<?php if ( $cliconnect_botao ) : ?>
		<button class="cookie-banner__botao botao botao--primario" type="button" data-cookie-aceitar>
			<?php echo esc_html( $cliconnect_botao ); ?>
		</button>
	<?php endif; ?>
</aside>
