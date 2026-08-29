<?php
/**
 * Política de Privacidade — corpo: conteúdo rico editável no admin.
 *
 * Campo:
 *   pv_corpo — WYSIWYG com as seções da política
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$corpo = cliconnect_campo_pagina( 'pv_corpo' );

if ( ! $corpo ) {
	return;
}
?>

<div class="pv-corpo">
	<?php echo wp_kses_post( $corpo ); ?>
</div>
