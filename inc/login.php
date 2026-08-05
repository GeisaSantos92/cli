<?php
/**
 * White-label da tela de login.
 *
 * Substitui a identidade visual do WordPress na tela /wp-login.php pelo logo
 * escuro do Customizer e pelas cores de marca definidas nas custom properties
 * abaixo (espelham as variáveis de assets/css/theme.css — ajuste as duas ao
 * personalizar a marca).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Injeta CSS inline na tela de login.
 *
 * Usa o logo escuro do Customizer (fundo claro). Se não houver logo cadastrado,
 * exibe apenas o nome do site como texto.
 *
 * @return void
 */
function cliconnect_login_styles() {
	$logo_id  = absint( get_theme_mod( 'cliconnect_logo_escuro' ) ?? 0 );
	$logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';

	$logo_css = $logo_url
		? 'background-image: url(' . esc_url( $logo_url ) . '); background-size: contain; background-repeat: no-repeat; background-position: center; width: 200px; height: 80px; text-indent: -9999px;'
		: 'background-image: none; width: 200px; height: 80px; text-indent: 0; font-size: 1.1rem; font-weight: 700; color: var(--login-primaria); display: flex; align-items: center; justify-content: center;';

	?>
	<style>
		:root {
			--login-primaria: #1f2937;
			--login-primaria-escura: #111827;
			--login-fundo: #f9fafb;
		}

		body.login {
			background-color: var(--login-fundo);
		}

		#login h1 a {
			<?php echo $logo_css; // phpcs:ignore WordPress.Security.EscapeOutput -- URL escapada acima. ?>
		}

		/* Inputs */
		.login input[type="text"]:focus,
		.login input[type="password"]:focus,
		.login input[type="email"]:focus {
			border-color: var(--login-primaria);
			box-shadow: 0 0 0 1px var(--login-primaria);
			outline: none;
		}

		/* Botão principal */
		.login #wp-submit,
		.login .button-primary {
			background-color: var(--login-primaria);
			border-color: var(--login-primaria-escura);
		}

		.login #wp-submit:hover,
		.login .button-primary:hover,
		.login #wp-submit:focus,
		.login .button-primary:focus {
			background-color: var(--login-primaria-escura);
			border-color: var(--login-primaria-escura);
		}

		/* Links */
		.login #nav a,
		.login #backtoblog a {
			color: var(--login-primaria);
		}

		.login #nav a:hover,
		.login #backtoblog a:hover {
			color: var(--login-primaria-escura);
		}
	</style>
	<?php
}
add_action( 'login_enqueue_scripts', 'cliconnect_login_styles' );

/**
 * Aponta o logo da tela de login para o site, não para wordpress.org.
 *
 * @return string URL da home.
 */
function cliconnect_login_logo_url() {
	return home_url( '/' );
}
add_filter( 'login_headerurl', 'cliconnect_login_logo_url' );

/**
 * Texto alternativo/title do logo na tela de login.
 *
 * @return string
 */
function cliconnect_login_logo_text() {
	return get_bloginfo( 'name' );
}
add_filter( 'login_headertext', 'cliconnect_login_logo_text' );
