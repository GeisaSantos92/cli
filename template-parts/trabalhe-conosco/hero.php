<?php
/**
 * Trabalhe Conosco — Hero.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( 'hero_eyebrow' );
$titulo  = cliconnect_campo_pagina( 'hero_titulo' );
$texto   = cliconnect_campo_pagina( 'hero_texto' );

if ( ! $titulo ) {
	return;
}
?>

<section class="tc-hero">
	<div class="container tc-hero__inner">

		<?php if ( $eyebrow ) : ?>
			<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
		<?php endif; ?>

		<h1 class="tc-hero__titulo"><?php echo esc_html( $titulo ); ?></h1>

		<?php if ( $texto ) : ?>
			<p class="tc-hero__texto"><?php echo esc_html( $texto ); ?></p>
		<?php endif; ?>

		<div class="tc-hero__acoes">
			<?php cliconnect_botao_pagina( 'hero_botao' ); ?>
		</div>

	</div>
</section>
