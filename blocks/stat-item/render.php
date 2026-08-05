<?php
/**
 * cliconnect/stat-item — render no front (um indicador da faixa de estatísticas).
 *
 * @package Cliconnect
 *
 * @var array $attributes Atributos do bloco.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$numero = $attributes['numero'] ?? '';
$rotulo = $attributes['rotulo'] ?? '';
?>
<div class="cliconnect-bl-stat">
	<span class="cliconnect-bl-stat-numero"><?php echo wp_kses_post( $numero ); ?></span>
	<span class="cliconnect-bl-stat-rotulo"><?php echo wp_kses_post( $rotulo ); ?></span>
</div>
