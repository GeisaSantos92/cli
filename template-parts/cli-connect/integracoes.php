<?php
/**
 * CLI Connect — Faixa de integrações (marquee).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$integracoes = get_posts(
	array(
		'post_type'      => 'cli_integracao',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	)
);

if ( ! $integracoes ) {
	return;
}
?>
<section class="cc-integracoes secao">
	<div class="cc-integracoes__trilha" aria-hidden="true">
		<div class="cc-integracoes__faixa">
			<?php foreach ( $integracoes as $integracao ) : ?>
				<div class="cc-integracoes__logo">
					<?php echo get_the_post_thumbnail( $integracao, 'thumbnail', array( 'alt' => esc_attr( $integracao->post_title ) ) ); ?>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="cc-integracoes__faixa" aria-hidden="true">
			<?php foreach ( $integracoes as $integracao ) : ?>
				<div class="cc-integracoes__logo">
					<?php echo get_the_post_thumbnail( $integracao, 'thumbnail', array( 'alt' => '' ) ); ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
