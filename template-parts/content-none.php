<?php
/**
 * Template Part: mensagem de "nenhum conteúdo encontrado".
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<section class="no-results">
	<h2 class="no-results__title"><?php esc_html_e( 'Nada encontrado', 'cli' ); ?></h2>

	<?php if ( is_search() ) : ?>
	<p><?php esc_html_e( 'Nenhum resultado para a sua busca. Tente outras palavras.', 'cli' ); ?></p>
	<?php else : ?>
	<p><?php esc_html_e( 'Ainda não há conteúdo publicado aqui.', 'cli' ); ?></p>
	<?php endif; ?>
</section>
