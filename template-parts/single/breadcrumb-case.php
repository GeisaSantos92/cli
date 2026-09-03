<?php
/**
 * Breadcrumb: Início › Cases › Título do case.
 *
 * Usado em single-cli_case.php. CSS em theme.css (.post-breadcrumb).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cliconnect_titulo    = get_the_title();
$cliconnect_arquivo   = get_post_type_archive_link( 'cli_case' );
?>

<nav class="post-breadcrumb post-breadcrumb--larga" aria-label="<?php esc_attr_e( 'Localização', 'cli' ); ?>">
	<div class="post-breadcrumb__inner">

		<a
			class="post-breadcrumb__home"
			href="<?php echo esc_url( home_url( '/' ) ); ?>"
			aria-label="<?php esc_attr_e( 'Início', 'cli' ); ?>"
		>
			<?php echo cliconnect_icone( 'casa', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>

		<?php echo cliconnect_icone( 'seta-direita', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

		<?php if ( $cliconnect_arquivo ) : ?>
			<a class="post-breadcrumb__link" href="<?php echo esc_url( $cliconnect_arquivo ); ?>">
				<?php esc_html_e( 'Cases', 'cli' ); ?>
			</a>
		<?php else : ?>
			<span class="post-breadcrumb__link"><?php esc_html_e( 'Cases', 'cli' ); ?></span>
		<?php endif; ?>

		<?php echo cliconnect_icone( 'seta-direita', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

		<span class="post-breadcrumb__atual">
			<?php echo esc_html( $cliconnect_titulo ); ?>
		</span>

	</div>
</nav>
