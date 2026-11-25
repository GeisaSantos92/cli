<?php
/**
 * Migração única: botões de WhatsApp gravados no banco → página Contato.
 *
 * Roda sozinha uma vez, na primeira requisição depois do deploy (sem comando
 * no servidor), e marca a opção `cliconnect_migracao_whatsapp`. Também pode
 * ser chamada pelo WP-CLI: `./bin/wp cliconnect migrar-whatsapp [--dry-run]`.
 *
 * O que muda:
 * - Campos ACF (texto ou Link) cujo valor é um link de WhatsApp → permalink do
 *   Contato no idioma do post; o Link perde o `target="_blank"`.
 * - Theme mod `cliconnect_cta_botao_url` (CTA azul do rodapé) → vazio, que o
 *   footer resolve para o Contato do idioma atual; o texto
 *   "Fale conosco no WhatsApp" → "Fale conosco", com tradução no Polylang.
 *
 * O que fica: o botão flutuante (`cliconnect_whatsapp_url`) e o ícone da
 * página Contato (`ct_form_whatsapp_url`) — ambos abrem o popup de lead.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Versão da migração. Troque o valor para forçar uma nova execução.
 */
const CLICONNECT_MIGRACAO_WHATSAPP = '1';

/**
 * Executa a migração uma única vez (depois do Polylang carregar).
 *
 * @return void
 */
function cliconnect_migracao_whatsapp_automatica() {
	if ( CLICONNECT_MIGRACAO_WHATSAPP === get_option( 'cliconnect_migracao_whatsapp' ) ) {
		return;
	}

	// Marca antes de rodar: duas requisições simultâneas não repetem o trabalho
	// (e, se repetissem, o resultado é o mesmo).
	update_option( 'cliconnect_migracao_whatsapp', CLICONNECT_MIGRACAO_WHATSAPP, true );

	cliconnect_migrar_whatsapp();
}
add_action( 'init', 'cliconnect_migracao_whatsapp_automatica', 99 );

/**
 * Troca os CTAs de WhatsApp gravados no banco pela página Contato.
 *
 * @param bool $simular Só lista o que mudaria, sem gravar.
 * @return string[] Uma linha por valor trocado.
 */
function cliconnect_migrar_whatsapp( $simular = false ) {
	global $wpdb;

	$manter = array( 'ct_form_whatsapp_url' );
	$log    = array();

	// Metas públicas (sem "_") com link de WhatsApp, fora das revisões.
	$linhas = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery -- migração única.
		"SELECT pm.post_id, pm.meta_key
		   FROM {$wpdb->postmeta} pm
		   JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		  WHERE p.post_type <> 'revision'
		    AND pm.meta_key NOT LIKE '\\_%'
		    AND ( pm.meta_value LIKE '%wa.me/%' OR pm.meta_value LIKE '%api.whatsapp.com/%' )"
	);

	foreach ( (array) $linhas as $linha ) {
		if ( in_array( $linha->meta_key, $manter, true ) ) {
			continue;
		}

		$post_id = (int) $linha->post_id;
		$idioma  = function_exists( 'pll_get_post_language' ) ? (string) pll_get_post_language( $post_id ) : '';
		$valor   = get_post_meta( $post_id, $linha->meta_key, true );
		$novo    = cliconnect_migracao_trocar_valor( $valor, cliconnect_url_contato( $idioma ) );

		if ( $novo === $valor ) {
			continue;
		}

		$log[] = sprintf( '#%d %s (%s)', $post_id, $linha->meta_key, get_post_type( $post_id ) );

		if ( ! $simular ) {
			update_post_meta( $post_id, $linha->meta_key, $novo );
		}
	}

	// CTA azul do rodapé.
	if ( cliconnect_e_link_whatsapp( (string) ( get_theme_mod( 'cliconnect_cta_botao_url' ) ?? '' ) ) ) {
		$log[] = 'theme mod cliconnect_cta_botao_url → vazio (Contato do idioma atual)';
		if ( ! $simular ) {
			set_theme_mod( 'cliconnect_cta_botao_url', '' );
		}
	}

	if ( 'Fale conosco no WhatsApp' === ( get_theme_mod( 'cliconnect_cta_botao_texto' ) ?? '' ) ) {
		$log[] = 'theme mod cliconnect_cta_botao_texto → "Fale conosco"';
		if ( ! $simular ) {
			set_theme_mod( 'cliconnect_cta_botao_texto', 'Fale conosco' );
		}
	}

	return array_merge( $log, cliconnect_migracao_traduzir_cta( $simular ) );
}

/**
 * Troca o link de WhatsApp de um valor de campo (texto ou Link do ACF).
 *
 * @param mixed  $valor   Valor do meta.
 * @param string $contato URL do Contato.
 * @return mixed
 */
function cliconnect_migracao_trocar_valor( $valor, $contato ) {
	if ( is_string( $valor ) ) {
		return cliconnect_e_link_whatsapp( trim( $valor ) ) ? $contato : $valor;
	}

	if ( is_array( $valor ) && isset( $valor['url'] ) && cliconnect_e_link_whatsapp( (string) $valor['url'] ) ) {
		$valor['url']    = $contato;
		$valor['target'] = '';
	}

	return $valor;
}

/**
 * Grava no Polylang a tradução de "Fale conosco" (texto do CTA do rodapé).
 *
 * Mesmos textos de strings_polylang_{idioma}() no seed; só grava o que falta.
 *
 * @param bool $simular Só lista, sem gravar.
 * @return string[]
 */
function cliconnect_migracao_traduzir_cta( $simular ) {
	$original = 'Fale conosco';
	$log      = array();

	if ( ! class_exists( 'PLL_MO' ) || ! function_exists( 'PLL' ) || empty( PLL()->model ) ) {
		return $log;
	}

	$traducoes = array(
		'en' => 'Talk to us',
		'es' => 'Hable con nosotros',
	);

	foreach ( $traducoes as $slug => $traduzido ) {
		$idioma = PLL()->model->get_language( $slug );

		if ( ! $idioma ) {
			continue;
		}

		$mo = new PLL_MO();
		$mo->import_from_db( $idioma );

		if ( $traduzido === $mo->translate( $original ) ) {
			continue;
		}

		$log[] = sprintf( 'string do Polylang "%s" (%s) → "%s"', $original, $slug, $traduzido );

		if ( ! $simular ) {
			$mo->add_entry( $mo->make_entry( $original, $traduzido ) );
			$mo->export_to_db( $idioma );
		}
	}

	return $log;
}
