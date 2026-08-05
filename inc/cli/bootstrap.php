<?php
/**
 * Bootstrap dos comandos WP-CLI do tema.
 *
 * Carregado por functions.php apenas quando WP_CLI está definido — inerte em
 * requisições web. Registre aqui os comandos custom do projeto (ex.: seeders
 * que convertem um template estático em conteúdo do WordPress).
 *
 * Padrão de organização:
 * - Uma classe por comando/etapa: inc/cli/class-cliconnect-seed-*.php
 * - Registro: WP_CLI::add_command( 'cliconnect_seed', 'Cliconnect_Seed_Setup' );
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/*
 * Exemplo:
 *
 * require_once __DIR__ . '/class-cliconnect-seed-setup.php';
 * WP_CLI::add_command( 'cliconnect_seed', 'Cliconnect_Seed_Setup' );
 */
