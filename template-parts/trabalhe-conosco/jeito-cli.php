<?php
/**
 * Trabalhe Conosco — O jeito CLI de ser.
 *
 * Layout: linha de cabeçalho (título | intro), seguida de 5 linhas numeradas
 * (número + título em azul | descrição), botão CTA centralizado abaixo.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo = cliconnect_campo_pagina( 'jeito_titulo' );
$texto  = cliconnect_campo_pagina( 'jeito_texto' );

$itens = array();
for ( $i = 1; $i <= 5; $i++ ) {
	$nome_item  = cliconnect_campo_pagina( "jeito_item_{$i}_titulo" );
	$texto_item = cliconnect_campo_pagina( "jeito_item_{$i}_texto" );
	if ( $nome_item ) {
		$itens[] = array(
			'titulo' => $nome_item,
			'texto'  => $texto_item,
		);
	}
}

if ( ! $titulo && ! $itens ) {
	return;
}
?>

<section class="secao tc-jeito">
	<div class="container">

		<div class="tc-jeito__cabecalho">
			<?php if ( $titulo ) : ?>
				<h2 class="tc-jeito__titulo"><?php echo esc_html( $titulo ); ?></h2>
			<?php endif; ?>
			<?php if ( $texto ) : ?>
				<p class="tc-jeito__intro"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $itens ) : ?>
			<ol class="tc-jeito__lista">
				<?php foreach ( $itens as $indice => $item ) : ?>
					<li class="tc-jeito__item">
						<div class="tc-jeito__esquerda">
							<span class="tc-jeito__numero" aria-hidden="true"><?php echo esc_html( str_pad( $indice + 1, 2, '0', STR_PAD_LEFT ) ); ?></span>
							<strong class="tc-jeito__nome"><?php echo esc_html( $item['titulo'] ); ?></strong>
						</div>
						<?php if ( $item['texto'] ) : ?>
							<span class="tc-jeito__descricao"><?php echo esc_html( $item['texto'] ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>

		<?php
		$botao = cliconnect_campo_pagina( 'jeito_botao', array() );
		if ( ! empty( $botao['url'] ) && ! empty( $botao['title'] ) ) :
		?>
			<div class="tc-jeito__rodape">
				<a
					class="botao botao--primario"
					href="<?php echo esc_url( $botao['url'] ); ?>"
					<?php echo ( '_blank' === ( $botao['target'] ?? '' ) ) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
				>
					<?php echo esc_html( $botao['title'] ); ?>
					<?php echo cliconnect_icone( 'seta-direita', 20 ); ?>
				</a>
			</div>
		<?php endif; ?>

	</div>
</section>
