<?php
/**
 * Home — "Sua integração pode já estar pronta".
 *
 * Cartão centralizado no container, vazado entre a seção azul-clara de cima e a
 * branca de baixo: o painel azul cobre a metade esquerda da malha de logos
 * (CPT cli_integracao), cortando os que ficam atrás dele.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$integracoes = cliconnect_posts( 'cli_integracao', 24 );

$eyebrow = cliconnect_campo( 'integracoes_eyebrow' );
$titulo  = cliconnect_campo( 'integracoes_titulo' );
$texto   = cliconnect_campo( 'integracoes_texto' );

if ( ! $titulo ) {
	return;
}
?>

<section class="integracoes">
	<div class="container">
		<div class="integracoes__wrapper">

			<?php if ( $integracoes ) : ?>
			<?php
			/*
			 * A malha é decorativa (aria-hidden): repetimos o catálogo até fechar
			 * a grade, como no Figma, em vez de deixar células vazias.
			 */
			$celulas = 72;
			$malha   = array();

			while ( count( $malha ) < $celulas ) {
				$malha = array_merge( $malha, $integracoes );
			}

			$malha = array_slice( $malha, 0, $celulas );
			?>
			<div class="integracoes__malha" aria-hidden="true">
				<?php foreach ( $malha as $integracao ) : ?>
					<span class="integracoes__item">
						<?php echo cliconnect_thumb( $integracao->ID, 'medium', array( 'alt' => '' ) ); ?>
					</span>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<div class="integracoes__painel">
				<?php if ( $eyebrow ) : ?>
					<span class="eyebrow eyebrow--clara"><?php echo esc_html( $eyebrow ); ?></span>
				<?php endif; ?>

				<h2 class="integracoes__titulo"><?php echo nl2br( esc_html( $titulo ) ); ?></h2>

				<?php if ( $texto ) : ?>
					<p class="integracoes__texto"><?php echo esc_html( $texto ); ?></p>
				<?php endif; ?>

				<?php cliconnect_botao( 'integracoes_botao', 'botao botao--branco', '' ); ?>
			</div>

		</div>
	</div>
</section>
