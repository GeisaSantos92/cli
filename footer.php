<?php
/**
 * Footer do tema.
 *
 * Estrutura (fiel ao Figma):
 * - Bloco CTA azul, sobreposto à transição para o rodapé escuro (Customizer).
 * - Faixa branca da agência (template-parts/rodape-agencia.php), com o
 *   copyright do cliente e os links legais.
 * - Rodapé escuro: barra superior com logo + "powered by boomi" à esquerda
 *   e ícones sociais à direita (Customizer); abaixo, colunas de links
 *   (menu "rodape", onde cada item de 1º nível é o título de uma coluna).
 * - Botões flutuantes: voltar ao topo e WhatsApp (Customizer).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cta_titulo = get_theme_mod( 'cliconnect_cta_titulo' ) ?? '';
$cta_texto  = get_theme_mod( 'cliconnect_cta_botao_texto' ) ?? '';
$cta_url    = get_theme_mod( 'cliconnect_cta_botao_url' ) ?? '';
$whatsapp   = get_theme_mod( 'cliconnect_whatsapp_url' ) ?? '';
$linkedin   = get_theme_mod( 'cliconnect_social_linkedin' ) ?? '';
$instagram  = get_theme_mod( 'cliconnect_social_instagram' ) ?? '';
?>

<?php if ( $cta_titulo && ! is_page_template( 'page-trabalhe-conosco.php' ) ) : ?>
<section class="footer-cta">
	<div class="footer-cta__inner">
		<div class="footer-cta__caixa">
			<h2 class="footer-cta__titulo"><?php echo nl2br( esc_html( $cta_titulo ) ); ?></h2>

			<?php if ( $cta_texto && $cta_url ) : ?>
				<a class="botao botao--branco" href="<?php echo esc_url( $cta_url ); ?>">
					<?php echo cliconnect_icone( 'whatsapp' ); // SVG estático. ?>
					<?php echo esc_html( $cta_texto ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<footer class="site-footer">
	<span class="site-footer__planeta" aria-hidden="true">
		<?php
		echo cliconnect_imagem_tema( // phpcs:ignore WordPress.Security.EscapeOutput -- montado com escape em cliconnect_imagem_tema().
			'planeta-footer.png',
			array(
				'alt'    => '',
				'width'  => 1440,
				'height' => 369,
			)
		);
		?>
	</span>

	<div class="site-footer__inner">
		<div class="site-footer__top">

			<div class="site-footer__brand-wrap">
				<a class="site-footer__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php cliconnect_logo( 'claro', 200 ); ?>
				</a>
			</div>

			<?php if ( $linkedin || $instagram ) : ?>
			<div class="site-footer__sociais">
				<?php if ( $linkedin ) : ?>
				<a
					class="site-footer__social-link"
					href="<?php echo esc_url( $linkedin ); ?>"
					target="_blank"
					rel="noopener noreferrer"
					aria-label="<?php esc_attr_e( 'LinkedIn', 'cli' ); ?>"
				>
					<?php echo cliconnect_social_icon( 'linkedin' ); // phpcs:ignore WordPress.Security.EscapeOutput -- SVG gerado internamente. ?>
				</a>
				<?php endif; ?>
				<?php if ( $instagram ) : ?>
				<a
					class="site-footer__social-link"
					href="<?php echo esc_url( $instagram ); ?>"
					target="_blank"
					rel="noopener noreferrer"
					aria-label="<?php esc_attr_e( 'Instagram', 'cli' ); ?>"
				>
					<?php echo cliconnect_social_icon( 'instagram' ); // phpcs:ignore WordPress.Security.EscapeOutput -- SVG gerado internamente. ?>
				</a>
				<?php endif; ?>
			</div>
			<?php endif; ?>

		</div>

		<?php if ( has_nav_menu( 'rodape' ) ) : ?>
		<div class="site-footer__links">
			<nav aria-label="<?php esc_attr_e( 'Links do rodapé', 'cli' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'rodape',
						'container'      => false,
						'menu_class'     => 'site-footer__grid',
						'fallback_cb'    => false,
						'depth'          => 3,
					)
				);
				?>
			</nav>
		</div>
		<?php endif; ?>

		<div class="site-footer__espaco" aria-hidden="true"></div>
	</div>
</footer>

<?php get_template_part( 'template-parts/rodape-agencia' ); ?>

<button
	class="voltar-ao-topo"
	type="button"
	data-voltar-ao-topo
	aria-label="<?php esc_attr_e( 'Voltar ao topo', 'cli' ); ?>"
>
	<?php echo cliconnect_icone( 'chevron-cima', 28 ); // SVG estático. ?>
</button>

<?php if ( $whatsapp ) : ?>
<a
	class="whatsapp-flutuante"
	href="<?php echo esc_url( $whatsapp ); ?>"
	target="_blank"
	rel="noopener noreferrer"
	aria-label="<?php esc_attr_e( 'Falar no WhatsApp', 'cli' ); ?>"
>
	<?php echo cliconnect_icone( 'whatsapp', 32 ); // SVG estático. ?>
</a>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
