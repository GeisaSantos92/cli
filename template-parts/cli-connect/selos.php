<?php
/**
 * CLI Connect — Selos (reaproveitamento da seção da Home).
 *
 * Reutiliza template-parts/home/compliance.php que busca o CPT cli_selo
 * e os campos globais do Customizer (compliance_eyebrow/titulo/texto).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_template_part( 'template-parts/home/compliance' );
