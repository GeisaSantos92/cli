<?php
/**
 * Home — esteira de agentes de IA.
 *
 * Cards do CPT cli_agente. A trilha é duplicada (aria-hidden na cópia) para o
 * loop infinito do CSS ficar contínuo sem JS.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$agentes = cliconnect_posts( 'cli_agente' );

if ( ! $agentes ) {
	return;
}

$legenda = cliconnect_campo( 'agentes_legenda' );
?>

<section class="agentes">

	<div class="agentes__pista">
		<div class="agentes__trilha">
			<?php for ( $passada = 0; $passada < 2; $passada++ ) : ?>
				<?php foreach ( $agentes as $agente ) : ?>
					<?php
					$icone       = get_field( 'icone', $agente->ID ) ?? 'automacao';
					$descricao   = get_field( 'descricao', $agente->ID ) ?? '';
					$status      = get_field( 'status', $agente->ID ) ?? '';
					$integracoes = get_field( 'integracoes', $agente->ID ) ?? array();
					?>
					<article class="agente-card" <?php echo $passada ? 'aria-hidden="true"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- string literal hardcoded. ?>>

						<header class="agente-card__topo">
							<span class="agente-card__icone">
								<?php echo cliconnect_icone( $icone ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_icone retorna SVG estático do tema. ?>
							</span>
							<?php if ( $status ) : ?>
								<span class="agente-card__status"><?php echo esc_html( $status ); ?></span>
							<?php endif; ?>
						</header>

						<h3 class="agente-card__titulo"><?php echo esc_html( get_the_title( $agente ) ); ?></h3>

						<?php if ( $descricao ) : ?>
							<p class="agente-card__descricao"><?php echo esc_html( $descricao ); ?></p>
						<?php endif; ?>

						<?php if ( $integracoes ) : ?>
						<footer class="agente-card__rodape">
							<span class="agente-card__rotulo"><?php esc_html_e( 'Integrações', 'cli' ); ?></span>
							<div class="agente-card__integracoes">
								<?php foreach ( $integracoes as $integracao_id ) : ?>
									<span class="agente-card__integracao">
										<?php echo cliconnect_thumb( $integracao_id, 'medium', array( 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_thumb é wrapper de wp_get_attachment_image que escapa internamente. ?>
									</span>
								<?php endforeach; ?>
							</div>
						</footer>
						<?php endif; ?>

					</article>
				<?php endforeach; ?>
			<?php endfor; ?>
		</div>
	</div>

	<?php if ( $legenda ) : ?>
		<div class="container">
			<p class="agentes__legenda"><?php echo esc_html( $legenda ); ?></p>
		</div>
	<?php endif; ?>

</section>
