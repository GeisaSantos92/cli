<?php
/**
 * Archive: cli_solucao — Listagem de todas as soluções.
 *
 * URL: /solucoes/
 * Exibe o catálogo completo de soluções com navegação lateral por categoria.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main class="sl-listagem" id="conteudo-principal">

	<div class="container">
		<div class="sl-listagem__inner">

			<!-- Sidebar: categorias de solução -->
			<aside class="sl-sidebar" aria-label="<?php esc_attr_e( 'Filtrar por categoria', 'cli' ); ?>">
				<p class="sl-sidebar__titulo"><?php esc_html_e( 'Categorias', 'cli' ); ?></p>

				<nav class="sl-sidebar__nav">
					<a class="sl-sidebar__link sl-sidebar__link--ativo"
					   href="<?php echo esc_url( get_post_type_archive_link( 'cli_solucao' ) ); ?>">
						<?php esc_html_e( 'Ver todos', 'cli' ); ?>
					</a>

					<?php
					$categorias = get_terms(
						array(
							'taxonomy'   => 'cli_categoria_solucao',
							'parent'     => 0,
							'hide_empty' => false,
						)
					);

					foreach ( $categorias as $categoria ) :
						$filhos = get_terms(
							array(
								'taxonomy'   => 'cli_categoria_solucao',
								'parent'     => $categoria->term_id,
								'hide_empty' => false,
							)
						);
						?>
						<div class="sl-sidebar__grupo">
							<a class="sl-sidebar__link sl-sidebar__link--pai"
							   href="<?php echo esc_url( get_term_link( $categoria ) ); ?>">
								<?php echo esc_html( $categoria->name ); ?>
							</a>

							<?php if ( $filhos ) : ?>
								<div class="sl-sidebar__filhos">
									<?php foreach ( $filhos as $filho ) : ?>
										<a class="sl-sidebar__link sl-sidebar__link--filho"
										   href="<?php echo esc_url( get_term_link( $filho ) ); ?>">
											<?php echo esc_html( $filho->name ); ?>
										</a>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</nav>
			</aside>

			<!-- Conteúdo: grid de soluções -->
			<div class="sl-conteudo">
				<header class="sl-conteudo__cabecalho">
					<h1 class="sl-conteudo__titulo"><?php esc_html_e( 'Todas as Soluções', 'cli' ); ?></h1>
				</header>

				<?php if ( have_posts() ) : ?>
					<div class="sl-grid">
						<?php
						while ( have_posts() ) :
							the_post();
							get_template_part( 'template-parts/solucao/card' );
						endwhile;
						?>
					</div>

					<?php
					get_template_part(
						'template-parts/pagination',
						null,
						array(
							'current_page' => max( 1, (int) get_query_var( 'paged' ) ),
							'total_pages'  => (int) $wp_query->max_num_pages,
						)
					);
					?>

				<?php else : ?>
					<p class="sl-conteudo__vazio"><?php esc_html_e( 'Nenhuma solução encontrada.', 'cli' ); ?></p>
				<?php endif; ?>
			</div>

		</div><!-- .sl-listagem__inner -->
	</div><!-- .container -->

</main>

<?php get_footer(); ?>
