<?php
/**
 * Programa de Parceiros — Estrutura ("Você encontra a oportunidade…").
 *
 * Acordeão nativo (<details> com o mesmo `name`: abrir um item fecha os
 * outros, sem JS) à esquerda e ilustração à direita. O primeiro item nasce
 * aberto, como no layout.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo    = cliconnect_campo_pagina( 'pp_estrutura_titulo' );
$texto     = cliconnect_campo_pagina( 'pp_estrutura_texto' );
$imagem_id = absint( cliconnect_campo_pagina( 'pp_estrutura_imagem', 0 ) );

$itens = array();
for ( $i = 1; $i <= 4; $i++ ) {
	$titulo_item = cliconnect_campo_pagina( "pp_estrutura_{$i}_titulo" );

	if ( $titulo_item ) {
		$itens[] = array(
			'titulo' => $titulo_item,
			'texto'  => cliconnect_campo_pagina( "pp_estrutura_{$i}_texto" ),
		);
	}
}

if ( ! $titulo && ! $itens ) {
	return;
}
?>
<section class="secao pp-estrutura">
	<div class="container pp-estrutura__grade">
		<div class="pp-estrutura__conteudo">
			<?php if ( $titulo ) : ?>
				<h2 class="pp-estrutura__titulo"><?php echo nl2br( esc_html( $titulo ) ); ?></h2>
			<?php endif; ?>
			<?php if ( $texto ) : ?>
				<p class="pp-estrutura__texto"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>

			<?php if ( $itens ) : ?>
				<div class="pp-estrutura__itens">
					<?php foreach ( $itens as $indice => $item ) : ?>
						<details class="pp-estrutura__item" name="pp-estrutura"<?php echo 0 === $indice ? ' open' : ''; ?>>
							<summary class="pp-estrutura__item-titulo"><?php echo esc_html( $item['titulo'] ); ?></summary>
							<?php if ( $item['texto'] ) : ?>
								<p class="pp-estrutura__item-texto"><?php echo esc_html( $item['texto'] ); ?></p>
							<?php endif; ?>
						</details>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $imagem_id ) : ?>
			<div class="pp-estrutura__imagem">
				<?php
				echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapa internamente.
					$imagem_id,
					'large',
					false,
					array(
						'alt'     => '',
						'loading' => 'lazy',
					)
				);
				?>
			</div>
		<?php endif; ?>
	</div>
</section>
