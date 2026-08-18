<?php
/**
 * Taxonomy: cli_categoria_solucao — Listagem por categoria ou tipo de solução.
 *
 * Um template serve tanto categorias pai (Tecnologia) quanto tipos filhos (SAP).
 * URL pai:  /solucoes/tecnologia/
 * URL filho: /solucoes/tecnologia/sap/
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$termo_atual = get_queried_object();
$termo_pai   = ( $termo_atual->parent ) ? get_term( $termo_atual->parent, 'cli_categoria_solucao' ) : null;
?>

<main class="sl-listagem" id="conteudo-principal">

	<div class="container">
		<div class="sl-listagem__inner">

			<!-- Sidebar: navegação por categoria -->
			<aside class="sl-sidebar" aria-label="<?php esc_attr_e( 'Filtrar por categoria', 'cli' ); ?>">
				<p class="sl-sidebar__titulo"><?php esc_html_e( 'Categorias', 'cli' ); ?></p>

				<nav class="sl-sidebar__nav">
					<a class="sl-sidebar__link"
					   href="<?php echo esc_url( get_post_type_archive_link( 'cli_solucao' ) ); ?>">
						<?php esc_html_e( 'Ver todos', 'cli' ); ?>
					</a>

					<?php
					$categorias_pai = get_terms(
						array(
							'taxonomy'   => 'cli_categoria_solucao',
							'parent'     => 0,
							'hide_empty' => false,
						)
					);

					foreach ( $categorias_pai as $categoria ) :
						// Categoria atual é este pai, ou um filho deste pai?
						$categoria_ativa = (
							$termo_atual->term_id === $categoria->term_id ||
							$termo_atual->parent  === $categoria->term_id
						);

						$filhos = get_terms(
							array(
								'taxonomy'   => 'cli_categoria_solucao',
								'parent'     => $categoria->term_id,
								'hide_empty' => false,
							)
						);
						?>
						<div class="sl-sidebar__grupo <?php echo $categoria_ativa ? 'sl-sidebar__grupo--aberto' : ''; ?>">
							<a class="sl-sidebar__link sl-sidebar__link--pai <?php echo $categoria->term_id === $termo_atual->term_id ? 'sl-sidebar__link--ativo' : ''; ?>"
							   href="<?php echo esc_url( get_term_link( $categoria ) ); ?>">
								<?php echo esc_html( $categoria->name ); ?>
							</a>

							<?php if ( $filhos && $categoria_ativa ) : ?>
								<div class="sl-sidebar__filhos">
									<?php foreach ( $filhos as $filho ) : ?>
										<a class="sl-sidebar__link sl-sidebar__link--filho <?php echo $filho->term_id === $termo_atual->term_id ? 'sl-sidebar__link--ativo' : ''; ?>"
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

			<!-- Conteúdo: grid filtrado -->
			<div class="sl-conteudo">
				<header class="sl-conteudo__cabecalho">
					<?php if ( $termo_pai ) : ?>
						<a class="sl-conteudo__voltar"
						   href="<?php echo esc_url( get_term_link( $termo_pai ) ); ?>">
							← <?php echo esc_html( $termo_pai->name ); ?>
						</a>
					<?php endif; ?>

					<h1 class="sl-conteudo__titulo"><?php echo esc_html( $termo_atual->name ); ?></h1>

					<?php if ( $termo_atual->description ) : ?>
						<p class="sl-conteudo__descricao"><?php echo esc_html( $termo_atual->description ); ?></p>
					<?php endif; ?>
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

					<?php cliconnect_paginacao(); ?>

				<?php else : ?>
					<p class="sl-conteudo__vazio">
						<?php esc_html_e( 'Nenhuma solução encontrada nesta categoria.', 'cli' ); ?>
					</p>
				<?php endif; ?>
			</div>

		</div><!-- .sl-listagem__inner -->
	</div><!-- .container -->

</main>

<?php get_footer(); ?>
