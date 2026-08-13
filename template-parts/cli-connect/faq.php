<?php
/**
 * CLI Connect — FAQ (reaproveitamento da seção da Home).
 *
 * Reutiliza template-parts/home/faq.php que busca o CPT cli_faq
 * e os campos globais do Customizer (faq_eyebrow / faq_titulo).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_template_part( 'template-parts/home/faq' );
