<?php
/**
 * Home — bloco reutilizável de texto + imagem.
 *
 * Usado duas vezes ("IA corporativa" e "Na prática"); o segundo bloco inverte a
 * ordem das colunas. Recebe via $args:
 *   - indice    (int)  número do bloco (1 ou 2), usado no nome dos campos ACF.
 *   - invertida (bool) se a imagem deve ficar à esquerda.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$indice    = absint( $args['indice'] ?? 1 );
$invertida = ! empty( $args['invertida'] );

$eyebrow = cliconnect_campo( "midia_{$indice}_eyebrow" );
$titulo  = cliconnect_campo( "midia_{$indice}_titulo" );
$texto   = cliconnect_campo( "midia_{$indice}_texto" );
$imagem  = cliconnect_campo_imagem( "midia_{$indice}_imagem", 'large' );

$topicos = cliconnect_lista_numerada( "midia_{$indice}_topico_%d", 3 );

if ( ! $titulo ) {
	return;
}
?>

<section class="secao midia<?php echo $invertida ? ' midia--invertida' : ''; ?>">
	<div class="container">
		<div class="midia__grid">

			<div class="midia__texto">
				<?php if ( $eyebrow ) : ?>
					<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
				<?php endif; ?>

				<h2 class="midia__titulo"><?php echo nl2br( esc_html( $titulo ) ); ?></h2>

				<?php if ( $texto ) : ?>
					<p class="midia__descricao"><?php echo esc_html( $texto ); ?></p>
				<?php endif; ?>

				<?php if ( $topicos ) : ?>
					<ul class="midia__topicos">
						<?php foreach ( $topicos as $topico ) : ?>
							<li class="midia__topico"><?php echo esc_html( $topico ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<?php if ( $imagem ) : ?>
				<div class="midia__imagem"><?php echo $imagem; // wp_get_attachment_image escapa. ?></div>
			<?php endif; ?>

		</div>
	</div>
</section>
