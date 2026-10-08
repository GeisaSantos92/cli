<?php
/**
 * Breadcrumb de página institucional: Início › Título da página.
 *
 * Usado em page-privacidade.php. CSS em theme.css (.post-breadcrumb) — a
 * largura padrão de 800px acompanha a coluna de leitura da página.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cliconnect_titulo = get_the_title();
?>

<nav class="post-breadcrumb" aria-label="<?php esc_attr_e( 'Localização', 'cli' ); ?>">
	<div class="post-breadcrumb__inner">

		<a
			class="post-breadcrumb__home"
			href="<?php echo esc_url( home_url( '/' ) ); ?>"
			aria-label="<?php esc_attr_e( 'Início', 'cli' ); ?>"
		>
			<?php echo cliconnect_icone( 'casa', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_icone retorna SVG estático do tema. ?>
		</a>

		<?php echo cliconnect_icone( 'chevron-direita', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_icone retorna SVG estático do tema. ?>

		<span class="post-breadcrumb__atual" aria-current="page">
			<?php echo esc_html( $cliconnect_titulo ); ?>
		</span>

	</div>
</nav>
