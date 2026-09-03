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

/* ==========================================================================
   Proteção básica contra brute force via Transients
   — max 10 tentativas por IP em janela de 15 minutos.
   ========================================================================== */

define( 'CLICONNECT_LOGIN_MAX_TENTATIVAS', 10 );
define( 'CLICONNECT_LOGIN_BLOQUEIO_SEG', 15 * MINUTE_IN_SECONDS );

/**
 * Retorna o IP do solicitante sem confiar em cabeçalhos de proxy.
 *
 * @return string IP sanitizado.
 */
function cliconnect_login_obter_ip() {
	return preg_replace( '/[^0-9a-fA-F:.]/', '', (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ) );
}

/**
 * Chave de transient para o IP informado.
 *
 * @param string $ip IP do solicitante.
 * @return string
 */
function cliconnect_login_chave( $ip ) {
	return 'cliconnect_login_' . md5( $ip );
}

/**
 * Incrementa o contador de falhas no transient após tentativa inválida.
 *
 * @return void
 */
function cliconnect_login_registrar_falha() {
	$ip    = cliconnect_login_obter_ip();
	$chave = cliconnect_login_chave( $ip );
	set_transient( $chave, (int) get_transient( $chave ) + 1, CLICONNECT_LOGIN_BLOQUEIO_SEG );
}
add_action( 'wp_login_failed', 'cliconnect_login_registrar_falha' );

/**
 * Bloqueia o login quando o IP ultrapassou o limite de tentativas.
 *
 * @param WP_User|WP_Error|null $user     Resultado anterior do authenticate.
 * @param string                $username Usuário submetido.
 * @param string                $password Senha submetida.
 * @return WP_User|WP_Error|null
 */
function cliconnect_login_verificar_bloqueio( $user, $username, $password ) {
	if ( empty( $username ) && empty( $password ) ) {
		return $user;
	}

	$falhas = (int) get_transient( cliconnect_login_chave( cliconnect_login_obter_ip() ) );

	if ( $falhas >= CLICONNECT_LOGIN_MAX_TENTATIVAS ) {
		return new WP_Error(
			'cliconnect_too_many_attempts',
			__( 'Muitas tentativas de login malsucedidas. Tente novamente em 15 minutos.', 'cli' )
		);
	}

	return $user;
}
add_filter( 'authenticate', 'cliconnect_login_verificar_bloqueio', 30, 3 );

/**
 * Zera o contador de falhas para o IP após login bem-sucedido.
 *
 * @return void
 */
function cliconnect_login_limpar_falhas() {
	delete_transient( cliconnect_login_chave( cliconnect_login_obter_ip() ) );
}
add_action( 'wp_login', 'cliconnect_login_limpar_falhas' );

/**
 * Normaliza as mensagens de erro de login para não revelar se o usuário existe.
 *
 * @param WP_Error $errors   Objeto de erros da tela de login.
 * @return WP_Error
 */
function cliconnect_login_normalizar_erros( $errors ) {
	if ( $errors->has_errors() && ! $errors->get_error_code() === 'cliconnect_too_many_attempts' ) {
		$codes = $errors->get_error_codes();
		$skip  = array( 'cliconnect_too_many_attempts', 'empty_username', 'empty_password' );
		foreach ( $codes as $code ) {
			if ( ! in_array( $code, $skip, true ) ) {
				$errors->remove( $code );
				$errors->add( $code, __( 'Nome de usuário ou senha inválidos.', 'cli' ) );
			}
		}
	}

	return $errors;
}
add_filter( 'wp_login_errors', 'cliconnect_login_normalizar_erros' );
