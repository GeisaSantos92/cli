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

$nos = cliconnect_lista_numerada( 'departamento_%d', 6 );

$titulo = cliconnect_campo( 'departamentos_titulo' );
$texto  = cliconnect_campo( 'departamentos_texto' );
$prova  = cliconnect_campo( 'prova_texto' );

if ( ! $titulo ) {
	return;
}

$metade   = (int) ceil( count( $nos ) / 2 );
$esquerda = array_slice( $nos, 0, $metade );
$direita  = array_slice( $nos, $metade );

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

			<div class="departamentos__diagrama" aria-hidden="true">
				<div class="departamentos__coluna departamentos__coluna--esquerda">
					<?php foreach ( $esquerda as $no ) : ?>
						<span class="departamentos__no"><?php echo esc_html( $no ); ?></span>
					<?php endforeach; ?>
				</div>

				<div class="departamentos__centro">
					<?php
					echo cliconnect_imagem_tema( // phpcs:ignore WordPress.Security.EscapeOutput -- montado com escape em cliconnect_imagem_tema().
						'logo-white.svg',
						array(
							'class'  => 'departamentos__centro-logo',
							'alt'    => '',
							'width'  => 122,
							'height' => 87,
						)
					);
					?>
				</div>

				<div class="departamentos__coluna departamentos__coluna--direita">
					<?php foreach ( $direita as $no ) : ?>
						<span class="departamentos__no"><?php echo esc_html( $no ); ?></span>
					<?php endforeach; ?>
				</div>
			</div>

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
