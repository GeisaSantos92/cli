<?php
/**
 * Taxonomy: cli_categoria_solucao — Catálogo de soluções por categoria.
 *
 * Parent term (parent=0): layout catálogo com busca + filtro por tipo.
 * Child  term (parent≠0): layout simples com navegação lateral.
 *
 * URL pai:   /solucoes/tecnologia/
 * URL filho: /solucoes/tecnologia/sap/
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$termo_atual = get_queried_object();
$is_pai      = 0 === (int) $termo_atual->parent;

if ( $is_pai ) :
	/*
	 * ── Catálogo (termo pai) ─────────────────────────────────────────────────
	 * Layout conforme Figma node 14903-11053.
	 *
	 * O filtro client-side usa data-tipo (espaço-separado) em cada card.
	 * O mapa abaixo deriva os tipos a partir do slug do post — sem campo ACF
	 * extra, sem nova taxonomy.
	 *
	 * Categorias: erp · crm · atendimento · marketing · pessoas
	 *             analytics · ia · financeiro · ecommerce
	 */
	$tipos_map = array(
		'claude'                      => 'ia',
		'chatgpt'                     => 'ia',
		'gemini'                      => 'ia',
		'sap'                         => 'erp',
		'sap-business-one'            => 'erp',
		'sap-ecc'                     => 'erp',
		'salesforce'                  => 'crm',
		'salesforce-sales-cloud'      => 'crm',
		'salesforce-service-cloud'    => 'atendimento crm',
		'salesforce-marketing-cloud'  => 'marketing',
		'totvs-protheus'              => 'erp',
		'totvs-datasul'               => 'erp',
		'totvs-winthor'               => 'erp',
		'totvs-logix'                 => 'erp',
		'totvs-consinco'              => 'erp',
		'totvs-linx'                  => 'erp',
		'totvs-rm'                    => 'erp',
		'sankhya'                     => 'erp',
		'senior'                      => 'erp pessoas',
		'dynamics-365'                => 'erp crm',
		'oracle-netsuite'             => 'erp financeiro',
		'arius-erp'                   => 'erp',
		'ciss-poder-erp'              => 'erp',
		'ifs-cloud'                   => 'erp',
		'qad-redzone'                 => 'erp',
		'rp-info'                     => 'erp',
		'viasoft'                     => 'erp',
		'onclick-erp'                 => 'erp',
		'rd-station-crm'              => 'crm',
		'rd-station-marketing'        => 'marketing',
		'hubspot-crm'                 => 'crm marketing',
		'propz'                       => 'marketing ecommerce',
		'thomson-reuters-tax-one'     => 'financeiro',
		'freshservice'                => 'atendimento',
		'servicenow'                  => 'atendimento',
		'zendesk'                     => 'atendimento',
		'microsoft-teams'             => 'atendimento',
		'portal-de-api'               => 'analytics',
		'aws'                         => 'analytics',
		'microsoft-azure'             => 'analytics',
		'google-cloud'                => 'analytics',
		'snowflake'                   => 'analytics',
		'databricks'                  => 'analytics',
		'bionexo'                     => 'ecommerce',
		'neogrid'                     => 'ecommerce analytics',
		'vtex'                        => 'ecommerce',
		'shopify'                     => 'ecommerce',
		'magento'                     => 'ecommerce',
		'target-sistemas'             => 'erp ecommerce',
		'onblox'                      => 'ecommerce analytics',
		'narwal'                      => 'ecommerce analytics',
		'tasy'                        => 'atendimento',
		'mv'                          => 'atendimento',
	);

	$categorias_sidebar = array(
		array( 'slug' => 'erp',         'label' => 'ERP',                              'ico' => 'erp' ),
		array( 'slug' => 'crm',         'label' => 'CRM',                              'ico' => 'crm' ),
		array( 'slug' => 'atendimento', 'label' => 'Atendimento (ITSM / Service Desk)', 'ico' => 'atendimento' ),
		array( 'slug' => 'marketing',   'label' => 'Automação de Marketing',            'ico' => 'marketing' ),
		array( 'slug' => 'pessoas',     'label' => 'Gestão de Pessoas (HCM)',           'ico' => 'pessoas' ),
		array( 'slug' => 'analytics',   'label' => 'Dados & Analytics',                'ico' => 'analytics' ),
		array( 'slug' => 'ia',          'label' => 'Inteligência Artificial',           'ico' => 'ia' ),
		array( 'slug' => 'financeiro',  'label' => 'Financeiro e Bancos',              'ico' => 'financeiro' ),
		array( 'slug' => 'ecommerce',   'label' => 'E-commerce & Marketplace',         'ico' => 'ecommerce' ),
	);

	// Query sem paginação — o filtro client-side precisa de todos os cards visíveis.
	$todos = new WP_Query( array(
		'post_type'      => 'cli_solucao',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
		'no_found_rows'  => true,
		'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy'         => 'cli_categoria_solucao',
				'field'            => 'term_id',
				'terms'            => $termo_atual->term_id,
				'include_children' => true,
			),
		),
	) );
	?>

<main class="cat-catalogo" id="conteudo-principal">

	<!-- Chamada -->
	<div class="cat-catalogo__chamada">
		<div class="container">
			<h1 class="cat-catalogo__titulo"><?php echo esc_html( $termo_atual->name ); ?></h1>
			<p class="cat-catalogo__subtitulo"><?php esc_html_e( 'Integre todos os sistemas da sua empresa e automatize seus fluxos de trabalho de ponta a ponta. Clique na ferramenta desejada abaixo para ver os detalhes, vantagens e casos de uso de cada integração.', 'cli' ); ?></p>
		</div>
	</div>

	<!-- Corpo -->
	<div class="cat-catalogo__corpo">
		<div class="container">
			<div class="cat-painel">

				<!-- Barra de busca (largura total do painel) -->
				<div class="cat-busca">
					<label for="cat-busca-campo" class="visually-hidden">
						<?php esc_html_e( 'Pesquisar por alguma tecnologia', 'cli' ); ?>
					</label>
					<div class="cat-busca__campo-wrap">
						<img
							class="cat-busca__ico"
							src="<?php echo esc_url( get_theme_file_uri( '/assets/img/catalogo-ico-busca.svg' ) ); ?>"
							alt=""
							width="24"
							height="24"
							aria-hidden="true"
						>
						<input
							id="cat-busca-campo"
							class="cat-busca__campo"
							type="search"
							placeholder="<?php esc_attr_e( 'Pesquisar por alguma tecnologia', 'cli' ); ?>"
							autocomplete="off"
						>
					</div>
				</div>

				<div class="cat-catalogo__inner">

					<!-- Sidebar de categorias funcionais -->
					<aside class="cat-sidebar" aria-label="<?php esc_attr_e( 'Filtrar por categoria', 'cli' ); ?>">
						<p class="cat-sidebar__titulo"><?php esc_html_e( 'Categorias', 'cli' ); ?></p>
						<ul class="cat-sidebar__lista" role="list">
							<?php foreach ( $categorias_sidebar as $cat ) :
								$ico_uri = get_theme_file_uri( '/assets/img/catalogo-ico-' . $cat['ico'] . '.svg' );
								$ico_ok  = file_exists( get_theme_file_path( '/assets/img/catalogo-ico-' . $cat['ico'] . '.svg' ) );
							?>
							<li>
								<button
									class="cat-sidebar__btn"
									type="button"
									data-tipo="<?php echo esc_attr( $cat['slug'] ); ?>"
									aria-pressed="false"
								>
									<?php if ( $ico_ok ) : ?>
										<img
											src="<?php echo esc_url( $ico_uri ); ?>"
											alt=""
											width="24"
											height="24"
											loading="lazy"
											aria-hidden="true"
										>
									<?php endif; ?>
									<?php echo esc_html( $cat['label'] ); ?>
								</button>
							</li>
							<?php endforeach; ?>
						</ul>
					</aside>

					<!-- Conteúdo: grade -->
					<div class="cat-conteudo">

						<!-- Grade de tecnologias -->
					<?php if ( $todos->have_posts() ) : ?>
						<div class="cat-grade" id="cat-grade" role="list" aria-live="polite">
							<?php
							while ( $todos->have_posts() ) :
								$todos->the_post();
								$slug  = get_post_field( 'post_name' );
								$tipos = $tipos_map[ $slug ] ?? '';
								$nome  = get_the_title();
								$url   = get_permalink();
							?>
							<?php $logo_html = cliconnect_logo_integracao( $nome ); ?>
							<article
								class="cat-card"
								data-tipo="<?php echo esc_attr( $tipos ); ?>"
								data-nome="<?php echo esc_attr( mb_strtolower( $nome ) ); ?>"
								role="listitem"
							>
								<a class="cat-card__link" href="<?php echo esc_url( $url ); ?>">
									<div class="cat-card__logo-box">
										<?php if ( $logo_html ) : ?>
											<span class="cat-card__logo-wrap">
												<?php echo $logo_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
											</span>
										<?php else : ?>
											<span class="cat-card__iniciais" aria-hidden="true">
												<?php echo esc_html( mb_substr( $nome, 0, 2 ) ); ?>
											</span>
										<?php endif; ?>
									</div>
									<p class="cat-card__nome"><?php echo esc_html( $nome ); ?></p>
								</a>
							</article>
							<?php endwhile; wp_reset_postdata(); ?>
						</div>

						<p class="cat-sem-resultado" hidden aria-live="polite">
							<?php esc_html_e( 'Nenhuma tecnologia encontrada.', 'cli' ); ?>
						</p>

					<?php else : ?>
						<p class="cat-sem-resultado">
							<?php esc_html_e( 'Nenhuma tecnologia encontrada nesta categoria.', 'cli' ); ?>
						</p>
					<?php endif; ?>

					</div><!-- .cat-conteudo -->

				</div><!-- .cat-catalogo__inner -->
			</div><!-- .cat-painel -->
		</div><!-- .container -->
	</div><!-- .cat-catalogo__corpo -->

</main>

<?php else : ?>
	<?php
	/*
	 * ── Filho (child term) ──────────────────────────────────────────────────
	 */
	$termo_pai = is_int( $termo_atual->parent )
		? get_term( $termo_atual->parent, 'cli_categoria_solucao' )
		: null;
	?>
	<main class="sl-listagem" id="conteudo-principal">
		<div class="container">
			<div class="sl-listagem__inner">

				<aside class="sl-sidebar" aria-label="<?php esc_attr_e( 'Filtrar por categoria', 'cli' ); ?>">
					<p class="sl-sidebar__titulo"><?php esc_html_e( 'Categorias', 'cli' ); ?></p>
					<nav class="sl-sidebar__nav">
						<?php if ( $termo_pai && ! is_wp_error( $termo_pai ) ) :
							?>
							<a class="sl-sidebar__link sl-sidebar__link--pai"
							   href="<?php echo esc_url( get_term_link( $termo_pai ) ); ?>">
								← <?php echo esc_html( $termo_pai->name ); ?>
							</a>
							<?php
							$irmaos = get_terms( array(
								'taxonomy'   => 'cli_categoria_solucao',
								'parent'     => $termo_pai->term_id,
								'hide_empty' => false,
							) );
							foreach ( $irmaos as $irmao ) :
								$ativo = $irmao->term_id === $termo_atual->term_id ? ' sl-sidebar__link--ativo' : '';
							?>
								<a class="sl-sidebar__link sl-sidebar__link--filho<?php echo esc_attr( $ativo ); ?>"
								   href="<?php echo esc_url( get_term_link( $irmao ) ); ?>">
									<?php echo esc_html( $irmao->name ); ?>
								</a>
							<?php endforeach; ?>
						<?php endif; ?>
					</nav>
				</aside>

				<div class="sl-conteudo">
					<header class="sl-conteudo__cabecalho">
						<h1 class="sl-conteudo__titulo"><?php echo esc_html( $termo_atual->name ); ?></h1>
						<?php if ( $termo_atual->description ) : ?>
							<p class="sl-conteudo__descricao"><?php echo esc_html( $termo_atual->description ); ?></p>
						<?php endif; ?>
					</header>

					<?php if ( have_posts() ) : ?>
						<div class="sl-grid">
							<?php while ( have_posts() ) : the_post();
								get_template_part( 'template-parts/solucao/card' );
							endwhile; ?>
						</div>
						<?php cliconnect_paginacao(); ?>
					<?php else : ?>
						<p class="sl-conteudo__vazio">
							<?php esc_html_e( 'Nenhuma solução encontrada nesta categoria.', 'cli' ); ?>
						</p>
					<?php endif; ?>
				</div>

			</div>
		</div>
	</main>
<?php endif; ?>

<?php get_footer(); ?>
