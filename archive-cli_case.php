<?php
/**
 * Archive: Cases de Sucesso — CLI Connect.
 *
 * Exibe o índice de cases do CPT cli_case, fiel ao Figma
 * (arquivo "CLI Connect", frame "Cases").
 *
 * Não usa o loop principal: segue o padrão do tema de buscar
 * os CPTs via cliconnect_posts() para manter a mesma ordenação
 * por menu_order usada em toda a home.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// Busca todos os cases publicados, ordenados por menu_order.
$todos_cases     = cliconnect_posts( 'cli_case' );
$cases_completos = array();
$cases_andamento = array();

foreach ( $todos_cases as $case ) {
	// Cases que existem só como card de métrica em outra página ficam fora daqui.
	if ( get_field( 'oculto_no_arquivo', $case->ID ) ) {
		continue;
	}

	if ( get_field( 'em_andamento', $case->ID ) ) {
		$cases_andamento[] = $case;
	} else {
		$cases_completos[] = $case;
	}
}
?>

<main id="primary" class="site-cases">

	<!-- ================================================================
	     HERO — "Conheça os resultados alcançados por nossos clientes"
	     ================================================================ -->
	<section class="cases-hero">
		<div class="cases-hero__bg" aria-hidden="true">
			<?php echo cliconnect_imagem_tema( 'cases-hero-bg.png', array( 'alt' => '' ) ); ?>
			<?php echo cliconnect_imagem_tema( 'cases-hero-bg2.png', array( 'alt' => '' ) ); ?>
		</div>
		<div class="container">
			<div class="cases-hero__chamada">
				<h1 class="cases-hero__titulo">
					<?php esc_html_e( 'Conheça os resultados alcançados por nossos clientes', 'cli' ); ?>
				</h1>
				<p class="cases-hero__subtitulo">
					<?php esc_html_e( 'Explore projetos que ajudaram empresas a ganhar eficiência, previsibilidade e capacidade de evolução.', 'cli' ); ?>
				</p>
			</div>
		</div>
	</section>

	<!-- ================================================================
	     LISTA DE CASES
	     ================================================================ -->
	<section class="cases-lista">
		<div class="container">
			<div class="cases-lista__cards">

				<?php if ( $cases_completos ) : ?>
					<?php foreach ( $cases_completos as $i => $case ) : ?>

						<?php if ( $i > 0 ) : ?>
							<hr class="cases-separador" aria-hidden="true">
						<?php endif; ?>

						<?php
						$titulo        = get_the_title( $case );
						$permalink     = get_permalink( $case );
						$excerpt       = get_the_excerpt( $case );
						$metrica_1_num = get_field( 'metrica_numero', $case->ID ) ?? '';
						$metrica_1_txt = get_field( 'metrica_texto', $case->ID ) ?? '';
						$metrica_2_num = get_field( 'metrica_numero_2', $case->ID ) ?? '';
						$metrica_2_txt = get_field( 'metrica_texto_2', $case->ID ) ?? '';
						$tem_metricas  = $metrica_1_num || $metrica_2_num;
						?>

						<a
							class="case-container"
							href="<?php echo esc_url( $permalink ); ?>"
							aria-label="<?php echo esc_attr( $titulo ); ?>"
						>

							<!-- Gradiente azul-marinha com logo do cliente centralizado -->
							<div class="case-container__imagem-wrapper">
								<?php
								$logo_img_id = absint( get_field( 'logo', $case->ID ) ?? 0 );
								if ( $logo_img_id ) {
									echo wp_get_attachment_image( $logo_img_id, 'cli-logo', false, array( 'alt' => esc_attr( $titulo ) ) );
								}
								?>
							</div>

							<!-- Chamada: título + texto + métricas -->
							<div class="case-container__chamada<?php echo $tem_metricas ? ' case-container__chamada--metricas' : ''; ?>">
								<div class="case-container__header">

									<div class="case-container__headline">
										<h2 class="case-container__titulo">
											<?php echo esc_html( $titulo ); ?>
										</h2>
									</div>

									<?php if ( $excerpt ) : ?>
										<div class="case-container__body">
											<p class="case-container__texto">
												<?php echo esc_html( $excerpt ); ?>
											</p>
										</div>
									<?php endif; ?>

								</div>

								<?php if ( $tem_metricas ) : ?>
									<div class="case-container__metricas">

										<?php if ( $metrica_1_num ) : ?>
											<div class="case-metrica-card">
												<p class="case-metrica-card__numero">
													<?php echo esc_html( $metrica_1_num ); ?>
												</p>
												<?php if ( $metrica_1_txt ) : ?>
													<p class="case-metrica-card__texto">
														<?php echo esc_html( $metrica_1_txt ); ?>
													</p>
												<?php endif; ?>
											</div>
										<?php endif; ?>

										<?php if ( $metrica_2_num ) : ?>
											<div class="case-metrica-card">
												<p class="case-metrica-card__numero">
													<?php echo esc_html( $metrica_2_num ); ?>
												</p>
												<?php if ( $metrica_2_txt ) : ?>
													<p class="case-metrica-card__texto">
														<?php echo esc_html( $metrica_2_txt ); ?>
													</p>
												<?php endif; ?>
											</div>
										<?php endif; ?>

									</div>
								<?php endif; ?>

							</div>

						</a>

					<?php endforeach; ?>
				<?php endif; ?>

				<!-- Cases em andamento (sem conteúdo publicado ainda) -->
				<?php if ( $cases_andamento ) : ?>
					<div class="cases-andamento">
						<?php foreach ( $cases_andamento as $case ) : ?>
							<?php
							$logo_id = absint( get_field( 'logo', $case->ID ) ?? 0 );
							?>
							<div class="case-andamento">

								<div class="case-andamento__logo">
									<?php if ( $logo_id ) : ?>
										<?php echo wp_get_attachment_image( $logo_id, 'cli-logo', false, array( 'alt' => get_the_title( $case ) ) ); ?>
									<?php endif; ?>
								</div>

								<p class="case-andamento__status">
									<?php esc_html_e( 'Case em andamento', 'cli' ); ?>
								</p>

							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

			</div><!-- .cases-lista__cards -->
		</div><!-- .container -->
	</section>

</main>

<?php
get_footer();
