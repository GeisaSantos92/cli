<?php
/**
 * cliconnect/stats — render no front.
 *
 * Exemplo do padrão pai/filho: os filhos (cliconnect/stat-item) chegam já
 * renderizados em $content; este arquivo só monta a moldura da seção.
 *
 * @package Cliconnect
 *
 * @var array  $attributes Atributos do bloco.
 * @var string $content    Filhos renderizados (InnerBlocks).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$badge  = $attributes['badge'] ?? '';
$titulo = $attributes['titulo'] ?? '';
?>
<section class="cliconnect-bl-stats">
	<div class="cliconnect-bl-container">
		<?php if ( $badge || $titulo ) : ?>
		<div class="cliconnect-bl-stats-header">
			<?php if ( $badge ) : ?>
			<span class="cliconnect-bl-kicker cliconnect-bl-kicker--claro"><?php echo wp_kses_post( $badge ); ?></span>
			<?php endif; ?>
			<?php if ( $titulo ) : ?>
			<h2 class="cliconnect-bl-stats-titulo"><?php echo wp_kses_post( $titulo ); ?></h2>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<div class="cliconnect-bl-stats-grid">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- filhos já renderizados/escapados pelo Core. ?>
		</div>
	</div>
</section>
