<?php
/**
 * Captação de leads: formulário Web-to-Lead do Salesforce + UTMs.
 *
 * O site tem um único formulário (template-parts/form-lead.php), usado em
 * dois lugares:
 * - página Contato, que devolve o visitante para a própria página com
 *   `?enviado=1` (mensagem de sucesso e gatilho de conversão dos anúncios);
 * - popup do botão flutuante de WhatsApp (template-parts/lead-popup.php), que
 *   devolve o visitante para a conversa no WhatsApp.
 *
 * O envio é o POST nativo do formulário para o Salesforce — sem backend nem
 * plugin. O script assets/js/utm-web-to-lead.js (carregado em todo o site)
 * guarda as UTMs da página de entrada e preenche com elas os campos ocultos
 * `00N...` do formulário (campos de UTM do Lead).
 *
 * Configuração no Customizer, seção "Salesforce — Web-to-Lead": ID da org e
 * os IDs `00N...` dos campos de UTM (vazio = IDs padrão do tema).
 *
 * Guia completo: docs/lead-salesforce.md.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Org do Salesforce que recebe os leads (valor inicial do Customizer).
 */
const CLICONNECT_SF_ORG_ID = '00Das000004fL2z';

/**
 * Endpoint do Web-to-Lead.
 */
const CLICONNECT_SF_ENDPOINT = 'https://webto.salesforce.com/servlet/servlet.WebToLead?encoding=UTF-8';

/**
 * Parâmetros de origem lidos da URL (chave => rótulo no Customizer).
 *
 * Fonte única: o Customizer cria um campo de ID por parâmetro e o script de
 * UTMs lê exatamente estes parâmetros.
 *
 * @return array<string,string>
 */
function cliconnect_lead_parametros() {
	return array(
		'utm_source'   => 'UTM Source',
		'utm_medium'   => 'UTM Medium',
		'utm_campaign' => 'UTM Campaign',
		'utm_term'     => 'UTM Term',
		'utm_content'  => 'UTM Content',
		'gclid'        => 'GCLID',
		'fbclid'       => 'FBCLID',
	);
}

/**
 * ID da org do Salesforce (Customizer, com o valor da CLI como padrão).
 *
 * @return string
 */
function cliconnect_lead_org_id() {
	$org = get_theme_mod( 'cliconnect_sf_org_id', CLICONNECT_SF_ORG_ID ) ?? '';

	return '' !== $org ? (string) $org : CLICONNECT_SF_ORG_ID;
}

/**
 * IDs `00N...` dos campos de UTM do Lead (valor inicial do Customizer).
 *
 * Tirados do Web-to-Lead gerado pelo Salesforce em 25/11/2026. Os IDs
 * diferenciam maiúsculas de minúsculas (`...2Ka` e `...2KZ` são campos
 * diferentes). Os mesmos IDs estão fixos em assets/js/utm-web-to-lead.js:
 * se o Salesforce recriar um campo, atualize os dois.
 *
 * @return array<string,string>
 */
function cliconnect_lead_campos_padrao() {
	return array(
		'utm_source'   => '00NbJ00000db2Kc',
		'utm_medium'   => '00NbJ00000db2Kb',
		'utm_campaign' => '00NbJ00000db2KZ',
		'utm_term'     => '00NbJ00000db2Kd',
		'utm_content'  => '00NbJ00000db2Ka',
		'gclid'        => '00NbJ00000db2KY',
		'fbclid'       => '00NbJ00000db2KX',
	);
}

/**
 * Mapa parâmetro => ID `00N...` do campo personalizado no Lead.
 *
 * Campo vazio no Customizer = vale o ID padrão (cliconnect_lead_campos_padrao()).
 *
 * @return array<string,string>
 */
function cliconnect_lead_campos_sf() {
	$padrao = cliconnect_lead_campos_padrao();
	$campos = array();

	foreach ( array_keys( cliconnect_lead_parametros() ) as $parametro ) {
		$id                   = (string) ( get_theme_mod( 'cliconnect_sf_campo_' . $parametro ) ?? '' );
		$campos[ $parametro ] = '' !== $id ? $id : ( $padrao[ $parametro ] ?? '' );
	}

	return $campos;
}

/**
 * Sanitiza o ID de um registro do Salesforce (15 ou 18 caracteres alfanuméricos).
 *
 * @param string $valor Valor digitado no Customizer.
 * @return string ID válido ou string vazia.
 */
function cliconnect_sanitizar_id_salesforce( $valor ) {
	$valor = trim( (string) $valor );

	return preg_match( '/^[a-zA-Z0-9]{15}([a-zA-Z0-9]{3})?$/', $valor ) ? $valor : '';
}

/**
 * Diz se uma URL leva para uma conversa do WhatsApp.
 *
 * @param string $url URL a testar.
 * @return bool
 */
function cliconnect_e_link_whatsapp( $url ) {
	$host = (string) wp_parse_url( (string) $url, PHP_URL_HOST );

	return in_array( strtolower( $host ), array( 'wa.me', 'api.whatsapp.com', 'web.whatsapp.com', 'chat.whatsapp.com' ), true );
}

/**
 * Permalink da página Contato.
 *
 * Com Polylang, parte da página em português e pede a tradução do idioma
 * pedido (ou do atual). Sem a página, devolve a URL previsível.
 *
 * @param string $idioma Slug do idioma no Polylang ('' = idioma atual).
 * @return string
 */
function cliconnect_url_contato( $idioma = '' ) {
	$pagina = get_page_by_path( 'contato' );
	$id     = $pagina ? (int) $pagina->ID : 0;

	if ( $id && function_exists( 'pll_get_post' ) ) {
		$traduzida = (int) ( $idioma ? pll_get_post( $id, $idioma ) : pll_get_post( $id ) );
		$id        = $traduzida ? $traduzida : $id;
	}

	$link = $id ? get_permalink( $id ) : '';

	return $link ? (string) $link : home_url( '/contato/' );
}

/**
 * URL para onde o Salesforce devolve o visitante depois do envio pela página
 * Contato: a própria página, com `?enviado=1`, ancorada no formulário.
 *
 * @return string
 */
function cliconnect_lead_retorno_contato() {
	return add_query_arg( 'enviado', '1', cliconnect_url_contato() ) . '#formulario';
}

/**
 * Diz se a requisição atual é a volta do Salesforce depois de um envio.
 *
 * @return bool
 */
function cliconnect_lead_enviado() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- só exibe uma mensagem; não altera nada.
	return isset( $_GET['enviado'] ) && '1' === sanitize_key( wp_unslash( $_GET['enviado'] ) );
}

/**
 * Configuração entregue ao script de UTMs (window.cliconnectLead).
 *
 * @return array<string,mixed>
 */
function cliconnect_lead_config_js() {
	return array(
		'campos'     => cliconnect_lead_campos_sf(),
		'expiraDias' => 90,
	);
}

/**
 * Não deixa o `?enviado=1` da volta do Salesforce virar página indexável.
 *
 * @param array $robots Diretivas de robots.
 * @return array
 */
function cliconnect_lead_robots( $robots ) {
	if ( cliconnect_lead_enviado() ) {
		$robots['noindex'] = true;
	}

	return $robots;
}
add_filter( 'wp_robots', 'cliconnect_lead_robots' );

/**
 * Mesmo noindex quando o Rank Math assume a meta de robots.
 *
 * @param array $robots Diretivas do Rank Math (chave => valor).
 * @return array
 */
function cliconnect_lead_robots_rank_math( $robots ) {
	if ( cliconnect_lead_enviado() ) {
		unset( $robots['index'] );
		$robots['noindex'] = 'noindex';
	}

	return $robots;
}
add_filter( 'rank_math/frontend/robots', 'cliconnect_lead_robots_rank_math' );
