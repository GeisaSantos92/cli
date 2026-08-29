<?php
/**
 * Política de Privacidade — cabeçalho: título, lead e data de atualização.
 *
 * Campos:
 *   pv_titulo         — título da página
 *   pv_lead           — parágrafo curto (opcional)
 *   pv_atualizado_em  — texto da data de atualização (opcional)
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo     = cliconnect_campo_pagina( 'pv_titulo' );
$lead       = cliconnect_campo_pagina( 'pv_lead' );
$atualizado = cliconnect_campo_pagina( 'pv_atualizado_em' );

// Sem campo preenchido, o título da própria página evita uma página sem H1.
if ( ! $titulo ) {
	$titulo = get_the_title();
}
?>

<header class="pv-cabecalho">
	<h1 class="pv-cabecalho__titulo"><?php echo esc_html( $titulo ); ?></h1>

	<?php if ( $lead ) : ?>
		<p class="pv-cabecalho__lead"><?php echo esc_html( $lead ); ?></p>
	<?php endif; ?>

	<?php if ( $atualizado ) : ?>
		<p class="pv-cabecalho__data"><?php echo esc_html( $atualizado ); ?></p>
	<?php endif; ?>
</header>
