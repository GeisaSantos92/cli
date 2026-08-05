<?php
/**
 * cliconnect/secao-texto — render no front.
 *
 * Reproduz o corpo do page.php (container + entry__content) como bloco: dá
 * aos blocos nativos um contexto estilizado dentro do template canvas.
 *
 * @package Cliconnect
 *
 * @var string $content Filhos renderizados (InnerBlocks).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="cliconnect-bl-texto">
	<div class="cliconnect-bl-container">
		<div class="entry__content">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- filhos já renderizados/escapados pelo Core. ?>
		</div>
	</div>
</section>
