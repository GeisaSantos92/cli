<?php
/**
 * auditar-traducao.php — o que ainda falta traduzir no site.
 *
 * Rodado por auditar-traducao.sh via `./bin/wp eval-file`. Só lê dados:
 * não altera nada.
 *
 * @package Cliconnect
 */

if ( ! function_exists( 'pll_languages_list' ) ) {
	WP_CLI::error( 'Polylang inativo. Ative o plugin antes de auditar a tradução.' );
}

$idiomas = pll_languages_list();

if ( ! $idiomas ) {
	WP_CLI::error( 'Nenhum idioma configurado no Polylang (Idiomas → Idiomas).' );
}

$opcoes  = get_option( 'polylang' );
$opcoes  = is_array( $opcoes ) ? $opcoes : array();
$tema    = get_stylesheet();
$padrao  = ( count( $idiomas ) > 0 ) ? $idiomas[0] : '';

WP_CLI::log( 'Idiomas: ' . implode( ', ', $idiomas ) . ' (padrão: ' . $padrao . ')' );
WP_CLI::log( '' );

/* --- 1. Tipos habilitados ------------------------------------------------- */

$habilitados = $opcoes['post_types'] ?? array();
$taxonomias  = $opcoes['taxonomies'] ?? array();

WP_CLI::log( '── Tipos habilitados para tradução' );
WP_CLI::log( '   post types: ' . ( $habilitados ? implode( ', ', $habilitados ) : '(nenhum além de post/page)' ) );
WP_CLI::log( '   taxonomias: ' . ( $taxonomias ? implode( ', ', $taxonomias ) : '(nenhuma)' ) );

$cpts_do_tema = get_post_types(
	array(
		'_builtin' => false,
	),
	'names'
);

$fora = array_diff( $cpts_do_tema, $habilitados );

if ( $fora ) {
	WP_CLI::log( '   fora da tradução: ' . implode( ', ', $fora ) );
	WP_CLI::log( '   (catálogo de logo normalmente NÃO deve entrar — ver references/camadas.md)' );
}

WP_CLI::log( '' );

/* --- 2. Conteúdo sem tradução --------------------------------------------- */

WP_CLI::log( '── Conteúdo sem tradução' );

$tipos = array_merge( array( 'page', 'post' ), array_values( $habilitados ) );
$tipos = array_values( array_unique( array_filter( $tipos ) ) );

foreach ( $tipos as $tipo ) {
	if ( ! post_type_exists( $tipo ) ) {
		continue;
	}

	$posts = get_posts(
		array(
			'post_type'      => $tipo,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'lang'           => '',
		)
	);

	$faltando = array();

	foreach ( $posts as $post_id ) {
		$lang = pll_get_post_language( $post_id );

		if ( $lang !== $padrao ) {
			continue;
		}

		$traducoes = pll_get_post_translations( $post_id );

		foreach ( $idiomas as $idioma ) {
			if ( $idioma === $padrao ) {
				continue;
			}

			if ( empty( $traducoes[ $idioma ] ) ) {
				$faltando[] = sprintf( '#%d %s [%s]', $post_id, get_the_title( $post_id ), $idioma );
			}
		}
	}

	$rotulo = sprintf( '   %-18s %d de %d sem tradução', $tipo, count( $faltando ), count( $posts ) );
	WP_CLI::log( $rotulo );

	foreach ( array_slice( $faltando, 0, 10 ) as $item ) {
		WP_CLI::log( '      · ' . $item );
	}

	if ( count( $faltando ) > 10 ) {
		WP_CLI::log( sprintf( '      … e mais %d', count( $faltando ) - 10 ) );
	}
}

WP_CLI::log( '' );

/* --- 3. Menus por idioma --------------------------------------------------- */

WP_CLI::log( '── Menus por location e idioma' );

$nav = $opcoes['nav_menus'][ $tema ] ?? array();

foreach ( array_keys( get_registered_nav_menus() ) as $location ) {
	$linha = '   ' . str_pad( $location, 16 );

	foreach ( $idiomas as $idioma ) {
		$menu_id = $nav[ $location ][ $idioma ] ?? 0;
		$linha  .= sprintf( '%s=%s  ', $idioma, $menu_id ? '#' . $menu_id : 'VAZIO' );
	}

	WP_CLI::log( $linha );
}

WP_CLI::log( '   (VAZIO ⇒ wp_nav_menu() renderiza nada nesse idioma)' );
WP_CLI::log( '' );

/* --- 4. Home por idioma ---------------------------------------------------- */

WP_CLI::log( '── Página inicial por idioma' );

$home_id = (int) get_option( 'page_on_front' );

if ( ! $home_id ) {
	WP_CLI::log( '   (o site não usa página estática como home)' );
} else {
	$traducoes = pll_get_post_translations( $home_id );

	foreach ( $idiomas as $idioma ) {
		$id = $traducoes[ $idioma ] ?? 0;
		WP_CLI::log( sprintf( '   %-4s %s', $idioma, $id ? '#' . $id . ' — ' . get_the_title( $id ) : 'SEM PÁGINA' ) );
	}
}

WP_CLI::log( '' );

/* --- 5. Strings do tema ---------------------------------------------------- */

WP_CLI::log( '── Strings do tema (gettext)' );

$dir = get_theme_file_path( '/languages' );
$mo  = glob( $dir . '/*.mo' ) ?: array();
$po  = glob( $dir . '/*.po' ) ?: array();

WP_CLI::log( sprintf( '   .po: %d   .mo: %d', count( $po ), count( $mo ) ) );

if ( ! $mo ) {
	WP_CLI::log( '   Nenhum .mo — a interface do tema não está traduzida.' );
	WP_CLI::log( '   Gere com: ./bin/wp i18n make-pot . languages/cli.pot --domain=cli' );
} else {
	foreach ( $mo as $arquivo ) {
		WP_CLI::log( '   · ' . basename( $arquivo ) );
	}
}

WP_CLI::log( '' );
WP_CLI::success( 'Auditoria de tradução concluída.' );
