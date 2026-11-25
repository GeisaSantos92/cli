<?php
/**
 * WP-CLI: `wp cliconnect migrar-whatsapp [--dry-run]`.
 *
 * A migração em si mora em inc/lead-migracao.php e já roda sozinha uma vez
 * depois do deploy. Este comando serve para simular ou repetir à mão.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Troca os CTAs de WhatsApp gravados no banco pela página Contato.
 *
 * @param array $args       Argumentos posicionais (não usados).
 * @param array $assoc_args Argumentos nomeados (--dry-run).
 * @return void
 */
function cliconnect_cli_migrar_whatsapp( $args, $assoc_args ) {
	unset( $args );

	$simular = ! empty( $assoc_args['dry-run'] );
	$log     = cliconnect_migrar_whatsapp( $simular );

	foreach ( $log as $linha ) {
		WP_CLI::log( '  ' . $linha );
	}

	if ( ! $simular ) {
		update_option( 'cliconnect_migracao_whatsapp', CLICONNECT_MIGRACAO_WHATSAPP, true );
	}

	WP_CLI::success(
		sprintf(
			$simular ? '%d valor(es) seriam trocados (simulação).' : '%d valor(es) trocados.',
			count( $log )
		)
	);
}

WP_CLI::add_command(
	'cliconnect migrar-whatsapp',
	'cliconnect_cli_migrar_whatsapp',
	array(
		'shortdesc' => 'Troca os CTAs de WhatsApp gravados no banco pela página Contato.',
		'synopsis'  => array(
			array(
				'type'        => 'flag',
				'name'        => 'dry-run',
				'description' => 'Só lista o que mudaria, sem gravar.',
				'optional'    => true,
			),
		),
	)
);
