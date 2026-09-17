<?php
/**
 * Breadcrumb: Início › Soluções › [Categoria] › Título da solução.
 *
 * Usado em single-cli_solucao.php. CSS em theme.css (.post-breadcrumb).
 * A categoria (termo pai da taxonomia cli_categoria_solucao) é incluída
 * quando existe, refletindo a hierarquia do catálogo.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cliconnect_titulo  = get_the_title();
$cliconnect_arquivo = get_post_type_archive_link( 'cli_solucao' );

// Categoria pai da solução (termo pai de cli_categoria_solucao).
$cliconnect_termos   = get_the_terms( get_the_ID(), 'cli_categoria_solucao' );
$cliconnect_cat      = null;
$cliconnect_cat_link = '';

if ( $cliconnect_termos && ! is_wp_error( $cliconnect_termos ) ) {
	foreach ( $cliconnect_termos as $termo ) {
		// Prefere o termo pai (parent = 0) se houver.
		if ( 0 === (int) $termo->parent ) {
			$cliconnect_cat      = $termo;
			$cliconnect_cat_link = (string) get_term_link( $termo );
			break;
		}
	}
	// Fallback: posts têm apenas o termo filho (ex.: SAP, parent=Tecnologias).
	// Sobe um nível para usar o pai como categoria intermediária e evitar duplicar
	// o nome da solução no breadcrumb (ex.: "SAP → SAP").
	if ( ! $cliconnect_cat ) {
		$primeiro = $cliconnect_termos[0];
		if ( $primeiro->parent ) {
			$pai = get_term( (int) $primeiro->parent, 'cli_categoria_solucao' );
			if ( $pai && ! is_wp_error( $pai ) ) {
				$cliconnect_cat      = $pai;
				$cliconnect_cat_link = (string) get_term_link( $pai );
			}
		}
		if ( ! $cliconnect_cat ) {
			$cliconnect_cat      = $primeiro;
			$cliconnect_cat_link = (string) get_term_link( $cliconnect_cat );
		}
	}
}
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
				<?php esc_html_e( 'Soluções', 'cli' ); ?>
			</a>
		<?php else : ?>
			<span class="post-breadcrumb__link"><?php esc_html_e( 'Soluções', 'cli' ); ?></span>
		<?php endif; ?>

		<?php if ( $cliconnect_cat ) : ?>
			<?php echo cliconnect_icone( 'seta-direita', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php if ( $cliconnect_cat_link && ! is_wp_error( $cliconnect_cat_link ) ) : ?>
				<a class="post-breadcrumb__link" href="<?php echo esc_url( $cliconnect_cat_link ); ?>">
					<?php echo esc_html( $cliconnect_cat->name ); ?>
				</a>
			<?php else : ?>
				<span class="post-breadcrumb__link"><?php echo esc_html( $cliconnect_cat->name ); ?></span>
			<?php endif; ?>
		<?php endif; ?>

		<?php echo cliconnect_icone( 'seta-direita', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

		<span class="post-breadcrumb__atual">
			<?php echo esc_html( $cliconnect_titulo ); ?>
		</span>

	</div>
</nav>
