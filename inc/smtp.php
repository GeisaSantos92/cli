<?php
/**
 * Roteamento SMTP para MailPit em ambiente de desenvolvimento.
 *
 * Em produção este arquivo é inerte: o hook só é registrado quando
 * WP_DEBUG está ativo, que nunca deve ocorrer em produção.
 *
 * Porta SMTP do MailPit no LocalWP: varia por site. Defina no wp-config.php
 * se o padrão não servir:
 *   define( 'CLICONNECT_SMTP_HOST', '127.0.0.1' );
 *   define( 'CLICONNECT_SMTP_PORT', 10006 );
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
	return;
}

add_action(
	'phpmailer_init',
	static function ( $phpmailer ) {
		$host = defined( 'CLICONNECT_SMTP_HOST' ) ? CLICONNECT_SMTP_HOST : '127.0.0.1';
		$port = defined( 'CLICONNECT_SMTP_PORT' ) ? (int) CLICONNECT_SMTP_PORT : 10006;

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$phpmailer->IsSMTP();
		$phpmailer->Host        = $host;
		$phpmailer->Port        = $port;
		$phpmailer->SMTPAuth    = false;
		$phpmailer->SMTPAutoTLS = false;
		// phpcs:enable
	}
);
