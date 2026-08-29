<?php
/**
 * Opções globais do tema via Customizer API.
 *
 * Substitui a Options Page do ACF Pro (ausente no ACF Free).
 * Registra a seção "Opções do Tema" com controles para logos, redes sociais,
 * contatos e scripts de rastreamento.
 *
 * Consumo em templates: get_theme_mod( 'cliconnect_logo_claro' ) ?? '', etc.
 * A injeção do script de analytics é responsabilidade de inc/analytics.php.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Redes sociais suportadas pelo tema (slug => rótulo).
 *
 * Fonte única para o Customizer (controles) e o footer (ícones).
 * Adicione/remova redes aqui e crie o ícone correspondente em
 * cliconnect_social_icon() (inc/template-tags.php).
 *
 * @return array<string,string>
 */
function cliconnect_social_networks() {
	return array(
		'instagram' => __( 'Instagram', 'cli' ),
		'facebook'  => __( 'Facebook', 'cli' ),
		'linkedin'  => __( 'LinkedIn', 'cli' ),
		'youtube'   => __( 'YouTube', 'cli' ),
		'tiktok'    => __( 'TikTok', 'cli' ),
		'x'         => __( 'X / Twitter', 'cli' ),
	);
}

/**
 * Registra a seção e controles do Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Manager do Customizer.
 * @return void
 */
function cliconnect_customize_register( $wp_customize ) {
	// Seção única do tema, logo após identidade visual (~150).
	$wp_customize->add_section(
		'cliconnect_options',
		array(
			'title'    => __( 'Opções do Tema', 'cli' ),
			'priority' => 160,
		)
	);

	/*
	 * --- LOGOS ---
	 * Dois logos (claro para fundos escuros, escuro para fundos claros),
	 * salvos como attachment ID e renderizados com wp_get_attachment_image()
	 * via cliconnect_logo() (inc/template-tags.php).
	 */

	$wp_customize->add_setting(
		'cliconnect_logo_claro',
		array(
			'type'              => 'theme_mod',
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'cliconnect_logo_claro',
			array(
				'label'       => __( 'Logo Claro', 'cli' ),
				'description' => __( 'Logo branca/clara para fundos escuros (cabeçalho)', 'cli' ),
				'section'     => 'cliconnect_options',
				'mime_type'   => 'image',
			)
		)
	);

	$wp_customize->add_setting(
		'cliconnect_logo_escuro',
		array(
			'type'              => 'theme_mod',
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'cliconnect_logo_escuro',
			array(
				'label'       => __( 'Logo Escuro', 'cli' ),
				'description' => __( 'Logo colorida para fundos claros (rodapé, login)', 'cli' ),
				'section'     => 'cliconnect_options',
				'mime_type'   => 'image',
			)
		)
	);

	/*
	 * --- REDES SOCIAIS (URLs) ---
	 * Controles gerados a partir de cliconnect_social_networks() — uma fonte só.
	 */
	foreach ( cliconnect_social_networks() as $slug => $label ) {
		$setting = 'cliconnect_social_' . $slug;

		$wp_customize->add_setting(
			$setting,
			array(
				'type'              => 'theme_mod',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			$setting,
			array(
				'label'       => $label,
				/* translators: %s: nome da rede social */
				'description' => sprintf( __( 'URL do perfil no %s', 'cli' ), $label ),
				'section'     => 'cliconnect_options',
				'type'        => 'url',
			)
		);
	}

	/*
	 * --- CONTATOS ---
	 */

	$wp_customize->add_setting(
		'cliconnect_phone',
		array(
			'type'              => 'theme_mod',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'cliconnect_phone',
		array(
			'label'       => __( 'Telefone', 'cli' ),
			'description' => __( 'Número de telefone principal (ex.: +55 11 98765-4321)', 'cli' ),
			'section'     => 'cliconnect_options',
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'cliconnect_email_geral',
		array(
			'type'              => 'theme_mod',
			'sanitize_callback' => 'sanitize_email',
		)
	);

	$wp_customize->add_control(
		'cliconnect_email_geral',
		array(
			'label'       => __( 'E-mail Geral', 'cli' ),
			'description' => __( 'E-mail de contato geral', 'cli' ),
			'section'     => 'cliconnect_options',
			'type'        => 'email',
		)
	);

	/*
	 * --- CABEÇALHO: BOTÕES DE AÇÃO ---
	 * Renderizados por cliconnect_header_acoes() (inc/template-tags.php).
	 */
	$cliconnect_textos = array(
		'cliconnect_portal_texto'     => array( 'Cabeçalho — texto do link secundário', 'Ex.: Portal do Cliente', 'text' ),
		'cliconnect_portal_url'       => array( 'Cabeçalho — URL do link secundário', '', 'url' ),
		'cliconnect_header_cta_texto' => array( 'Cabeçalho — texto do botão principal', 'Ex.: Acessar Plataforma', 'text' ),
		'cliconnect_header_cta_url'   => array( 'Cabeçalho — URL do botão principal', '', 'url' ),
		'cliconnect_cta_botao_texto'  => array( 'Rodapé — texto do botão do CTA', 'Ex.: Fale conosco no WhatsApp', 'text' ),
		'cliconnect_cta_botao_url'    => array( 'Rodapé — URL do botão do CTA', '', 'url' ),
		'cliconnect_whatsapp_url'     => array( 'URL do botão flutuante de WhatsApp', 'Deixe vazio para não exibir o botão.', 'url' ),
		'cliconnect_cookies_texto'      => array( 'Cookies — texto do banner', 'Deixe vazio para não exibir o banner.', 'text' ),
		'cliconnect_cookies_link_texto' => array( 'Cookies — rótulo do link', 'Ex.: política de privacidade', 'text' ),
		'cliconnect_cookies_botao'      => array( 'Cookies — texto do botão', 'Ex.: Concordar', 'text' ),
	);

	foreach ( $cliconnect_textos as $cliconnect_id => $cliconnect_config ) {
		list( $cliconnect_label, $cliconnect_desc, $cliconnect_tipo ) = $cliconnect_config;

		$wp_customize->add_setting(
			$cliconnect_id,
			array(
				'type'              => 'theme_mod',
				'sanitize_callback' => ( 'url' === $cliconnect_tipo ) ? 'esc_url_raw' : 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			$cliconnect_id,
			array(
				'label'       => $cliconnect_label,
				'description' => $cliconnect_desc,
				'section'     => 'cliconnect_options',
				'type'        => $cliconnect_tipo,
			)
		);
	}

	/*
	 * --- RODAPÉ: TÍTULO DO CTA ---
	 */
	$wp_customize->add_setting(
		'cliconnect_cta_titulo',
		array(
			'type'              => 'theme_mod',
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);

	$wp_customize->add_control(
		'cliconnect_cta_titulo',
		array(
			'label'       => __( 'Rodapé — título do CTA', 'cli' ),
			'description' => __( 'Bloco azul acima do rodapé. Use quebras de linha para separar as linhas do título.', 'cli' ),
			'section'     => 'cliconnect_options',
			'type'        => 'textarea',
		)
	);

	/*
	 * --- SCRIPTS DE RASTREAMENTO ---
	 * A injeção no wp_head é responsabilidade de inc/analytics.php
	 * (apenas fora de WP_DEBUG e para visitantes não logados).
	 */

	$wp_customize->add_setting(
		'cliconnect_ga_tag',
		array(
			'type'              => 'theme_mod',
			'sanitize_callback' => 'cliconnect_sanitize_tracking_snippet',
		)
	);

	$wp_customize->add_control(
		'cliconnect_ga_tag',
		array(
			'label'       => __( 'Tag Google Analytics / GTM', 'cli' ),
			'description' => __( 'Cole aqui o snippet completo de Google Analytics ou Google Tag Manager. Só é impresso em produção (WP_DEBUG desligado) e para visitantes não logados.', 'cli' ),
			'section'     => 'cliconnect_options',
			'type'        => 'textarea',
		)
	);
}
add_action( 'customize_register', 'cliconnect_customize_register' );

/**
 * Sanitiza o snippet de rastreamento.
 *
 * Só administradores com unfiltered_html podem salvar o snippet com <script>;
 * para os demais, o conteúdo passa por wp_kses_post (que remove scripts).
 *
 * @param string $value Snippet informado no Customizer.
 * @return string
 */
function cliconnect_sanitize_tracking_snippet( $value ) {
	$value = (string) $value;

	if ( current_user_can( 'unfiltered_html' ) ) {
		return $value;
	}

	return wp_kses_post( $value );
}
