<?php
/**
 * Single: Case de Sucesso — CLI Connect.
 *
 * Template da página individual de um case (CPT cli_case), fiel ao Figma
 * (arquivo "CLI Connect", frame "Cases — single").
 *
 * Seções:
 *   1. Intro  — imagem (retrato) + título + subtítulo (excerpt).
 *   2. Body   — barra lateral sticky (tecnologias + nav âncora) + conteúdo
 *               em três blocos: Desafio, Solução, Impacto.
 *   3. Outros — grade com até 3 cases relacionados.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	/* -----------------------------------------------------------------------
	   Campos ACF + dados do post
	   --------------------------------------------------------------------- */
	$titulo          = get_the_title();
	$excerpt         = get_the_excerpt();
	$retrato_id      = absint( get_field( 'retrato' ) ?? 0 );
	$video_url       = get_field( 'video' ) ?? '';
	$citacao         = get_field( 'citacao' ) ?? '';
	$autor           = get_field( 'autor' ) ?? '';
	$cargo           = get_field( 'cargo' ) ?? '';
	$metrica_1_num   = get_field( 'metrica_numero' ) ?? '';
	$metrica_1_txt   = get_field( 'metrica_texto' ) ?? '';
	$metrica_2_num   = get_field( 'metrica_numero_2' ) ?? '';
	$metrica_2_txt   = get_field( 'metrica_texto_2' ) ?? '';
	$integracoes     = get_field( 'integracoes' ) ?? array();
	$desafio_titulo  = get_field( 'desafio_titulo' ) ?? '';
	$desafio_texto   = get_field( 'desafio_texto' ) ?? '';
	$solucao_titulo  = get_field( 'solucao_titulo' ) ?? '';
	$solucao_texto   = get_field( 'solucao_texto' ) ?? '';
	$impacto_titulo  = get_field( 'impacto_titulo' ) ?? '';
	$impacto_texto   = get_field( 'impacto_texto' ) ?? '';
	$tem_metricas    = $metrica_1_num || $metrica_2_num;

	/* Até 3 outros cases publicados (excluindo o atual). */
	$outros_cases = cliconnect_posts(
		'cli_case',
		3,
		array(
			'post__not_in' => array( get_the_ID() ),
			/*
			 * Só o arquivo /cases/ respeita `oculto_no_arquivo`: no frame, os
			 * cases que servem de card de métrica na home aparecem aqui.
			 */
			'meta_query'   => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => 'em_andamento',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => 'em_andamento',
					'value'   => '1',
					'compare' => '!=',
				),
				'relation' => 'OR',
			),
		)
	);
	?>

<?php get_template_part( 'template-parts/single/breadcrumb-case' ); ?>

<main id="primary" class="site-case-single">

	<!-- ====================================================================
	     1. INTRO — Imagem + Título + Texto
	     ==================================================================== -->
	<section class="case-intro">
		<div class="container">
			<div class="case-intro__grid">

				<?php if ( $retrato_id ) : ?>
					<div class="case-intro__imagem">
						<?php
						echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapa internamente.
							$retrato_id,
							'cli-case-hero',
							false,
							array( 'class' => 'case-intro__foto', 'alt' => esc_attr( $titulo ) )
						);
						?>
					</div>
				<?php endif; ?>

				<div class="case-intro__chamada">
					<div class="case-intro__headline">
						<h1 class="case-intro__titulo"><?php echo esc_html( $titulo ); ?></h1>
					</div>
					<?php if ( $excerpt ) : ?>
						<div class="case-intro__body">
							<p class="case-intro__subtitulo"><?php echo esc_html( $excerpt ); ?></p>
						</div>
					<?php endif; ?>
				</div>

			</div>
		</div>
	</section>

	<!-- ====================================================================
	     2. BODY — Sidebar sticky + Conteúdo em blocos
	     ==================================================================== -->
	<section class="case-body">
		<div class="container">
			<div class="case-body__grid">

				<!-- Sidebar ------------------------------------------------- -->
				<aside class="case-topicos">

					<?php if ( $integracoes ) : ?>
						<div class="case-topicos__tecnologias">
							<p class="case-topicos__label">
								<?php esc_html_e( 'Tecnologias Utilizadas', 'cli' ); ?>
							</p>
							<div class="case-topicos__logos">
								<?php foreach ( $integracoes as $integracao_id ) : ?>
									<?php $logo_id = get_post_thumbnail_id( $integracao_id ); ?>
									<?php if ( $logo_id ) : ?>
										<div class="case-topicos__logo-box">
											<?php
											/*
											 * 'full' evita o hard-crop 150×150 do tamanho 'thumbnail'
											 * do WordPress, que recortava logos horizontais fisicamente
											 * antes de chegarem ao CSS. Com a imagem original, o
											 * object-fit: contain do .case-topicos__logo-box img
											 * controla a exibição sem distorção.
											 */
											echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapa internamente.
												$logo_id,
												'full',
												false,
												array( 'alt' => esc_attr( get_the_title( $integracao_id ) ) )
											);
											?>
										</div>
									<?php endif; ?>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>

					<nav class="case-topicos__nav" aria-label="<?php esc_attr_e( 'Tópicos do case', 'cli' ); ?>">
						<?php if ( $desafio_titulo ) : ?>
							<a class="case-topicos__link" href="#desafio">
								<?php esc_html_e( 'Desafio', 'cli' ); ?>
							</a>
						<?php endif; ?>
						<?php if ( $solucao_titulo ) : ?>
							<a class="case-topicos__link" href="#solucao">
								<?php esc_html_e( 'Solução', 'cli' ); ?>
							</a>
						<?php endif; ?>
						<?php if ( $impacto_titulo || $tem_metricas ) : ?>
							<a class="case-topicos__link" href="#impacto">
								<?php esc_html_e( 'Impacto', 'cli' ); ?>
							</a>
						<?php endif; ?>
					</nav>

				</aside>

				<!-- Conteúdo ------------------------------------------------ -->
				<div class="case-body__content" data-scroll-spy-parent>

					<!-- Vídeo ----------------------------------------------- -->
					<?php if ( $video_url ) : ?>
						<div class="case-video">
							<?php if ( $retrato_id ) : ?>
								<?php
								echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapa internamente.
									$retrato_id,
									'full',
									false,
									array( 'class' => 'case-video__thumb', 'alt' => '' )
								);
								?>
							<?php endif; ?>
							<?php if ( $video_url ) : ?>
								<a
									class="case-video__play"
									href="<?php echo esc_url( $video_url ); ?>"
									target="_blank"
									rel="noopener noreferrer"
									aria-label="<?php esc_attr_e( 'Assistir ao vídeo', 'cli' ); ?>"
								>
									<?php echo cliconnect_imagem_tema( 'play-circle.svg', array( 'alt' => '', 'class' => 'case-video__play-icon' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_imagem_tema retorna HTML seguro do tema. ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<!-- Desafio --------------------------------------------- -->
					<?php if ( $desafio_titulo ) : ?>
						<section class="case-secao" id="desafio" data-scroll-spy-section>
							<div class="case-secao__heading">
								<div class="case-secao__eyebrow-wrapper">
									<span class="case-secao__eyebrow"><?php esc_html_e( 'Desafio', 'cli' ); ?></span>
								</div>
								<h2 class="case-secao__titulo"><?php echo esc_html( $desafio_titulo ); ?></h2>
							</div>

							<?php if ( $desafio_texto ) : ?>
								<div class="case-secao__body">
									<?php echo wp_kses_post( $desafio_texto ); ?>
								</div>
							<?php endif; ?>

							<?php if ( $citacao ) : ?>
								<blockquote class="case-citacao">
									<span class="case-citacao__aspas" aria-hidden="true">"</span>
									<div class="case-citacao__conteudo">
										<p class="case-citacao__texto"><?php echo esc_html( $citacao ); ?></p>
										<?php if ( $autor ) : ?>
											<p class="case-citacao__autor">
												<?php
												echo esc_html( $autor );
												if ( $cargo ) {
													echo esc_html( ' - ' . $cargo );
												}
												?>
											</p>
										<?php endif; ?>
									</div>
								</blockquote>
							<?php endif; ?>
						</section>
					<?php endif; ?>

					<!-- Solução --------------------------------------------- -->
					<?php if ( $solucao_titulo ) : ?>
						<section class="case-secao" id="solucao" data-scroll-spy-section>
							<div class="case-secao__heading">
								<div class="case-secao__eyebrow-wrapper">
									<span class="case-secao__eyebrow"><?php esc_html_e( 'A solução', 'cli' ); ?></span>
								</div>
								<h2 class="case-secao__titulo"><?php echo esc_html( $solucao_titulo ); ?></h2>
							</div>

							<?php if ( $solucao_texto ) : ?>
								<div class="case-secao__body">
									<?php echo wp_kses_post( $solucao_texto ); ?>
								</div>
							<?php endif; ?>
						</section>
					<?php endif; ?>

					<!-- Impacto --------------------------------------------- -->
					<?php if ( $impacto_titulo || $tem_metricas ) : ?>
						<section class="case-secao" id="impacto" data-scroll-spy-section>
							<?php if ( $impacto_titulo ) : ?>
								<div class="case-secao__heading">
									<div class="case-secao__eyebrow-wrapper">
										<span class="case-secao__eyebrow"><?php esc_html_e( 'O impacto', 'cli' ); ?></span>
									</div>
									<h2 class="case-secao__titulo"><?php echo esc_html( $impacto_titulo ); ?></h2>
								</div>
							<?php endif; ?>

							<?php if ( $impacto_texto ) : ?>
								<div class="case-secao__body">
									<?php echo wp_kses_post( $impacto_texto ); ?>
								</div>
							<?php endif; ?>

							<?php if ( $tem_metricas ) : ?>
								<div class="case-metricas-single">
									<?php if ( $metrica_1_num ) : ?>
										<div class="case-metrica-single">
											<p class="case-metrica-single__numero"><?php echo esc_html( $metrica_1_num ); ?></p>
											<?php if ( $metrica_1_txt ) : ?>
												<p class="case-metrica-single__texto"><?php echo esc_html( $metrica_1_txt ); ?></p>
											<?php endif; ?>
										</div>
									<?php endif; ?>
									<?php if ( $metrica_2_num ) : ?>
										<div class="case-metrica-single">
											<p class="case-metrica-single__numero"><?php echo esc_html( $metrica_2_num ); ?></p>
											<?php if ( $metrica_2_txt ) : ?>
												<p class="case-metrica-single__texto"><?php echo esc_html( $metrica_2_txt ); ?></p>
											<?php endif; ?>
										</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</section>
					<?php endif; ?>

				</div><!-- .case-body__content -->

			</div><!-- .case-body__grid -->
		</div><!-- .container -->
	</section>

	<!-- ====================================================================
	     3. OUTROS CASES
	     ==================================================================== -->
	<?php if ( $outros_cases ) : ?>
		<section class="cases-outros">
			<div class="container">

				<h2 class="cases-outros__titulo">
					<?php esc_html_e( 'Confira outros cases de sucesso', 'cli' ); ?>
				</h2>

				<div class="cases-outros__cards">
					<?php foreach ( $outros_cases as $outro ) : ?>
						<?php
						$outro_titulo    = get_the_title( $outro );
						$outro_permalink = get_permalink( $outro );
						$outro_logo_id   = absint( get_field( 'logo', $outro->ID ) ?? 0 );
						?>
						<a class="case-outro-card" href="<?php echo esc_url( $outro_permalink ); ?>" aria-label="<?php echo esc_attr( $outro_titulo ); ?>">
							<p class="case-outro-card__titulo"><?php echo esc_html( $outro_titulo ); ?></p>
							<div class="case-outro-card__rodape">
								<?php if ( $outro_logo_id ) : ?>
									<div class="case-outro-card__logo">
										<?php
										echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapa internamente.
											$outro_logo_id,
											'medium',
											false,
											array( 'alt' => esc_attr( $outro_titulo ) )
										);
										?>
									</div>
								<?php endif; ?>
								<span class="case-outro-card__link">
									<?php esc_html_e( 'Ver case', 'cli' ); ?>
								</span>
							</div>
						</a>
					<?php endforeach; ?>
				</div>

				<div class="cases-outros__botao-wrapper">
					<a class="botao botao--primario" href="<?php echo esc_url( get_post_type_archive_link( 'cli_case' ) ); ?>">
						<?php esc_html_e( 'Confira nossos cases', 'cli' ); ?>
					</a>
				</div>

			</div>
		</section>
	<?php endif; ?>

</main>

<?php
endwhile;
get_footer();
