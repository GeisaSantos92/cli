<?php
/**
 * Template tags — helpers de renderização usados pelo chrome do tema.
 *
 * Centraliza lógica repetida de header/footer (logo com fallback textual,
 * ícones de redes sociais) para os templates ficarem só com marcação.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Arquivos SVG do logo que acompanham o tema, por variante.
 *
 * O logo é asset do tema (assets/img/), não conteúdo: entra como SVG estático
 * para não depender de upload e não perder nitidez. O media control do
 * Customizer continua existindo como sobrescrita opcional.
 *
 * @return array<string,array{arquivo:string,largura:int,altura:int}>
 */
function cliconnect_logos_tema() {
	return array(
		'escuro' => array(
			'arquivo' => 'logo-header.svg',
			'largura' => 176,
			'altura'  => 46,
		),
		'claro'  => array(
			'arquivo' => 'logo-footer.svg',
			'largura' => 373,
			'altura'  => 98,
		),
	);
}

/**
 * Renderiza o logo do tema, com fallback textual.
 *
 * Ordem: logo do Customizer (se cadastrado) → SVG de assets/img/ → nome do site.
 *
 * @param string $variant 'claro' (fundos escuros) ou 'escuro' (fundos claros).
 * @param int    $width   Largura de exibição em px.
 * @return void
 */
function cliconnect_logo( $variant = 'claro', $width = 120 ) {
	$logo_id = absint( get_theme_mod( 'cliconnect_logo_' . $variant ) ?? 0 );
	$width   = absint( $width );
	$svg     = cliconnect_logos_tema()[ $variant ] ?? null;

	if ( ! $logo_id && $svg ) {
		$html = cliconnect_imagem_tema(
			$svg['arquivo'],
			array(
				'class'   => 'site-logo site-logo--' . $variant,
				'alt'     => get_bloginfo( 'name' ),
				'width'   => $svg['largura'],
				'height'  => $svg['altura'],
				'style'   => 'width:' . $width . 'px;height:auto;',
				'loading' => 'eager',
			)
		);

		if ( $html ) {
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput -- montado com escape em cliconnect_imagem_tema().

			return;
		}
	}

	if ( $logo_id ) {
		echo wp_get_attachment_image(
			$logo_id,
			'full',
			false,
			array(
				'class' => 'site-logo site-logo--' . esc_attr( $variant ),
				'style' => 'width:' . $width . 'px;height:auto;',
				'alt'   => esc_attr( get_bloginfo( 'name' ) ),
			)
		); // wp_get_attachment_image já escapa.

		return;
	}

	// Fallback textual enquanto o logo não é cadastrado no Customizer.
	printf(
		'<span class="site-logo site-logo--text site-logo--%1$s">%2$s</span>',
		esc_attr( $variant ),
		esc_html( get_bloginfo( 'name' ) )
	);
}

/**
 * Botões de ação do cabeçalho (Portal do Cliente + CTA).
 *
 * Textos e URLs vêm do Customizer; o bloco é reaproveitado no drawer mobile.
 *
 * @return void
 */
function cliconnect_header_acoes() {
	$portal_texto = cliconnect_mod_traduzido( 'cliconnect_portal_texto' );
	$portal_url   = get_theme_mod( 'cliconnect_portal_url' ) ?? '';
	$cta_texto    = cliconnect_mod_traduzido( 'cliconnect_header_cta_texto' );
	$cta_url      = get_theme_mod( 'cliconnect_header_cta_url' ) ?? '';

	if ( $portal_texto && $portal_url ) {
		printf(
			'<a class="site-header__link-cliente" href="%1$s">%2$s</a>',
			esc_url( $portal_url ),
			esc_html( $portal_texto )
		);
	}

	if ( $cta_texto && $cta_url ) {
		printf(
			'<a class="site-header__cta" href="%1$s">%2$s</a>',
			esc_url( $cta_url ),
			esc_html( $cta_texto )
		);
	}
}

/**
 * Seletor de idiomas do Polylang (botão de globo + dropdown com bandeiras).
 *
 * Silencioso quando o plugin está inativo ou só há um idioma configurado —
 * o tema nunca depende do plugin para renderizar. O idioma atual fica de fora
 * da lista: o dropdown oferece só para onde dá para ir.
 *
 * @return void
 */
function cliconnect_seletor_idiomas() {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return;
	}

	/*
	 * hide_if_empty => 0: o site é bilíngue por definição, então o seletor
	 * aparece desde o começo — sem ele o Polylang esconde o idioma enquanto
	 * ainda não há conteúdo traduzido e o globo some do header.
	 */
	$idiomas = pll_the_languages(
		array(
			'raw'                    => 1,
			'hide_if_no_translation' => 0,
			'hide_if_empty'          => 0,
			'echo'                   => 0,
		)
	);

	if ( ! is_array( $idiomas ) || count( $idiomas ) < 2 ) {
		return;
	}

	$outros = array_filter(
		$idiomas,
		static function ( $idioma ) {
			return empty( $idioma['current_lang'] );
		}
	);

	if ( ! $outros ) {
		return;
	}

	?>
	<div class="site-header__idiomas" data-idiomas>
		<button
			class="site-header__idiomas-gatilho"
			type="button"
			aria-expanded="false"
			aria-controls="seletor-idiomas"
			aria-label="<?php esc_attr_e( 'Mudar o idioma', 'cli' ); ?>"
		>
			<?php echo cliconnect_icone( 'portal', 20 ); // SVG estático. ?>
			<?php echo cliconnect_icone( 'chevron-baixo', 16 ); // SVG estático. ?>
		</button>

		<ul class="site-header__idiomas-lista" id="seletor-idiomas">
			<?php foreach ( $outros as $idioma ) : ?>
				<li>
					<a
						class="site-header__idioma"
						href="<?php echo esc_url( $idioma['url'] ?? '' ); ?>"
						lang="<?php echo esc_attr( $idioma['locale'] ?? '' ); ?>"
						hreflang="<?php echo esc_attr( $idioma['locale'] ?? '' ); ?>"
					>
						<?php echo cliconnect_bandeira( $idioma ); // phpcs:ignore WordPress.Security.EscapeOutput -- filtrado em cliconnect_bandeira(). ?>
						<?php echo esc_html( $idioma['name'] ?? $idioma['slug'] ?? '' ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
}

/**
 * Bandeira de um idioma do Polylang, filtrada para saída.
 *
 * O Polylang devolve a bandeira ora como <img>, ora como URL, conforme a
 * configuração — aqui os dois casos viram a mesma <img>.
 *
 * @param array $idioma Item devolvido por pll_the_languages( raw ).
 * @return string HTML da bandeira, ou string vazia.
 */
function cliconnect_bandeira( $idioma ) {
	$bandeira = $idioma['flag'] ?? '';

	if ( ! $bandeira ) {
		return '';
	}

	if ( '<' === substr( $bandeira, 0, 1 ) ) {
		return wp_kses(
			$bandeira,
			array(
				'img' => array(
					'src'    => array(),
					'alt'    => array(),
					'width'  => array(),
					'height' => array(),
					'class'  => array(),
					'style'  => array(),
				),
			)
		);
	}

	return sprintf(
		'<img class="site-header__bandeira" src="%1$s" alt="" width="20" height="14" loading="lazy" decoding="async">',
		esc_url( $bandeira )
	);
}

/**
 * SVG do ícone de uma rede social (cliconnect_social_networks()).
 *
 * @param string $network Slug da rede (instagram, facebook, linkedin, youtube, tiktok, x).
 * @return string SVG do ícone, ou string vazia se desconhecido.
 */
function cliconnect_social_icon( $network ) {
	$paths = array(
		'instagram' => 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z',
		'facebook'  => 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z',
		'linkedin'  => 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z',
		'youtube'   => 'M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z',
		'tiktok'    => 'M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z',
		'x'         => 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z',
	);

	if ( ! isset( $paths[ $network ] ) ) {
		return '';
	}

	return '<svg viewBox="0 0 24 24" fill="currentColor" width="22" height="22" aria-hidden="true"><path d="'
		. $paths[ $network ]
		. '"></path></svg>';
}
