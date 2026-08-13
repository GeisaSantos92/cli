<?php
/**
 * Home — "Integre todos os departamentos da sua empresa".
 *
 * Diagrama montado em CSS: 6 nós (campos numerados) ligados ao bloco central
 * com o logo do produto. A prova social usa os Clientes marcados como tal.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo = cliconnect_campo( 'departamentos_titulo' );
$texto  = cliconnect_campo( 'departamentos_texto' );
$prova  = cliconnect_campo( 'prova_texto' );

if ( ! $titulo ) {
	return;
}

$logos_prova = cliconnect_posts(
	'cli_cliente',
	3,
	array(
		'meta_key'   => 'prova_social', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		'meta_value' => '1',            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
	)
);
?>

<section class="secao departamentos">
	<div class="container">
		<div class="departamentos__grid">

			<?php
			echo cliconnect_imagem_tema( // phpcs:ignore WordPress.Security.EscapeOutput -- montado com escape em cliconnect_imagem_tema().
				'section-departamentos.png',
				array(
					'class'  => 'departamentos__imagem',
					'alt'    => __( 'Diagrama de departamentos: Compras, Atendimento e Logística (esquerda) conectados ao logo da CLI Connect, e Fiscal, Financeiro e RH (direita)', 'cli' ),
					'width'  => 614,
					'height' => 305,
				)
			);
			?>

			<div class="departamentos__texto">
				<h2 class="departamentos__titulo"><?php echo nl2br( esc_html( $titulo ) ); ?></h2>

				<?php if ( $texto ) : ?>
					<p class="departamentos__descricao"><?php echo esc_html( $texto ); ?></p>
				<?php endif; ?>

				<?php cliconnect_botao( 'departamentos_botao' ); ?>

				<?php if ( $prova ) : ?>
				<div class="departamentos__prova">
					<?php if ( $logos_prova ) : ?>
						<div class="departamentos__avatares">
							<?php foreach ( $logos_prova as $logo_cliente ) : ?>
								<span class="departamentos__avatar">
									<?php
									// 'medium' e não 'thumbnail': o thumbnail é cortado em quadrado
									// e decepa as laterais dos logos horizontais.
									echo cliconnect_thumb( $logo_cliente->ID, 'medium', array( 'alt' => get_the_title( $logo_cliente ) ) );
									?>
								</span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<p class="departamentos__prova-texto"><?php echo nl2br( esc_html( $prova ) ); ?></p>
				</div>
				<?php endif; ?>
			</div>

		</div>
	</div>
</section>
