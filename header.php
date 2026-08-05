<?php
/**
 * Header do tema.
 *
 * Logo (Customizer), menu principal (menu "principal", com dropdowns), seletor
 * de idioma do Polylang e os dois botões de ação — todos configuráveis no
 * Customizer → Opções do Tema. Nenhum item é fixo no código.
 *
 * Assets enfileirados em inc/enqueue.php — nenhum <link>/<script> aqui.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#primary"><?php esc_html_e( 'Pular para o conteúdo', 'cli' ); ?></a>

<header class="site-header">
	<div class="site-header__inner">

		<a
			class="site-header__brand"
			href="<?php echo esc_url( home_url( '/' ) ); ?>"
			aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
		>
			<?php cliconnect_logo( 'escuro', 176 ); ?>
		</a>

		<nav
			id="site-nav"
			class="site-nav"
			aria-label="<?php esc_attr_e( 'Menu principal', 'cli' ); ?>"
		>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'principal',
					'container'      => false,
					'menu_class'     => 'site-nav__list',
					'fallback_cb'    => false,
					'walker'         => new Cliconnect_Walker_Nav_Menu(),
				)
			);
			?>

			<div class="site-nav__acoes-mobile">
				<?php cliconnect_header_acoes(); ?>
			</div>
		</nav>

		<div class="site-header__acoes">
			<?php cliconnect_seletor_idiomas(); ?>
			<?php cliconnect_header_acoes(); ?>
		</div>

		<button
			class="site-header__toggle"
			data-nav-toggle
			aria-controls="site-nav"
			aria-expanded="false"
			aria-label="<?php esc_attr_e( 'Abrir menu', 'cli' ); ?>"
		>
			<span class="site-header__toggle-bar" aria-hidden="true"></span>
			<span class="site-header__toggle-bar" aria-hidden="true"></span>
			<span class="site-header__toggle-bar" aria-hidden="true"></span>
		</button>

	</div>
</header>
