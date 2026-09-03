<?php
/**
 * Tamanhos de imagem customizados do tema.
 *
 * Registra tamanhos específicos para cada contexto do layout, evitando que o
 * WordPress sirva imagens genéricas (medium/large) maiores ou menores que o
 * necessário. Após alterar esses valores, rodar:
 *
 *   ./bin/wp media regenerate --yes
 *
 * Contextos e dimensões (baseados no design system e CSS do tema):
 *
 * | Handle            | Uso                                       | Dimensão     |
 * |-------------------|-------------------------------------------|--------------|
 * | cli-blog-card     | Thumbnail nos cards da listagem do blog   | 600 × 400 px |
 * | cli-blog-destaque | Imagem do post em destaque no topo        | 1200 × 630px |
 * | cli-case-hero     | Foto de capa no single de case            | 1200 × 800px |
 * | cli-logo          | Logos de clientes e cases (esteiras)      | 320 × 90 px  |
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra os tamanhos de imagem customizados.
 *
 * @return void
 */
function cliconnect_register_image_sizes() {
	// Cards da listagem do blog (grid de artigos).
	add_image_size( 'cli-blog-card', 600, 400, true );

	// Post em destaque no topo da página do blog — proporção OG-friendly.
	add_image_size( 'cli-blog-destaque', 1200, 630, true );

	// Hero do single de case (foto de capa, object-fit: cover).
	add_image_size( 'cli-case-hero', 1200, 800, true );

	// Logos de clientes e cases: max 290×87px no CSS, margem de 10%.
	// Sem crop (false) para preservar proporção do logo.
	add_image_size( 'cli-logo', 320, 90, false );
}
add_action( 'after_setup_theme', 'cliconnect_register_image_sizes' );
