<?php
/**
 * Template Part: faixa de rodapé da agência (assinatura da Agência R8).
 *
 * Renderizada DEPOIS do <footer> do site (ver footer.php) — é uma faixa
 * independente, não substitui o rodapé. Traz o copyright do cliente e os links
 * legais à esquerda e o selo de autoria da agência à direita.
 *
 * Estilos: bloco ".rodape-agencia" em assets/css/theme.css.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cliconnect_copyright = sprintf(
	/* translators: 1: nome do site, 2: ano atual. */
	esc_html__( '© %1$s %2$s. Todos os direitos reservados.', 'cli' ),
	'<strong>' . esc_html( get_bloginfo( 'name' ) ) . '</strong>',
	esc_html( gmdate( 'Y' ) )
);
?>

<div class="rodape-agencia">
	<div class="container">
		<div class="rodape-agencia__flex">

			<div class="rodape-agencia__conteudo">
				<p><?php echo $cliconnect_copyright; // phpcs:ignore WordPress.Security.EscapeOutput -- partes escapadas acima. ?></p>

				<?php if ( has_nav_menu( 'rodape_legal' ) ) : ?>
				<nav aria-label="<?php esc_attr_e( 'Links legais', 'cli' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'rodape_legal',
							'container'      => false,
							'menu_class'     => 'rodape-agencia__legal',
							'fallback_cb'    => false,
							'depth'          => 1,
						)
					);
					?>
				</nav>
				<?php endif; ?>
			</div>

			<div class="rodape-agencia__logos">
				<a href="https://agenciar8.com.br" target="_blank" rel="noopener noreferrer"
					title="<?php esc_attr_e( 'Design e desenvolvimento', 'cli' ); ?>">
					<img src="https://agenciar8.com.br/footerlogos/agenciar8.png"
						alt="<?php esc_attr_e( 'Logo Agência R8', 'cli' ); ?>"
						width="33" height="34" loading="lazy" decoding="async">
				</a>
			</div>

		</div>
	</div>
</div>
