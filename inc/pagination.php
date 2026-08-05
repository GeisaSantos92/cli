<?php
/**
 * Paginação numerada (archives e listagens com filtro).
 *
 * Botões com números, reticências e setas anterior/próxima. Toda a aparência
 * vive em classes CSS (.pagination*) em assets/css/theme.css — nada de cores
 * ou estilos inline, para o tema não carregar identidade visual no PHP.
 *
 * Uso direto: cliconnect_pagination_render( $current, $total, $add_args );
 * Ou via template part: get_template_part( 'template-parts/pagination', null, [...] ).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Calcula a lista de itens da paginação (números + reticências "...").
 *
 * Sempre mostra a primeira e a última página, e uma janela de
 * current-1..current+1 no meio.
 *
 * @param int $current Página atual (1-indexed).
 * @param int $total   Total de páginas.
 * @return array<int|string> Lista de números de página e '...'.
 */
function cliconnect_pagination_page_items( $current, $total ) {
	if ( $total <= 5 ) {
		return range( 1, $total );
	}

	$items = array( 1 );
	$lo    = max( 2, $current - 1 );
	$hi    = min( $total - 1, $current + 1 );

	if ( $lo > 2 ) {
		$items[] = '...';
	}
	for ( $i = $lo; $i <= $hi; $i++ ) {
		$items[] = $i;
	}
	if ( $hi < $total - 1 ) {
		$items[] = '...';
	}
	$items[] = $total;

	return $items;
}

/**
 * Renderiza a paginação numerada.
 *
 * @param int   $current  Página atual (1-indexed).
 * @param int   $total    Total de páginas.
 * @param array $add_args Query args a preservar nos links (ex.: s, filtros).
 * @return void
 */
function cliconnect_pagination_render( $current, $total, $add_args = array() ) {
	if ( $total <= 1 ) {
		return;
	}

	$add_args = array_filter( $add_args );

	$link = static function ( $page ) use ( $add_args ) {
		$url = get_pagenum_link( $page );
		return $add_args ? add_query_arg( $add_args, $url ) : $url;
	};

	$arrow_prev = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>';
	$arrow_next = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>';
	?>
	<nav class="pagination" aria-label="<?php esc_attr_e( 'Navegação de páginas', 'cli' ); ?>">

		<?php if ( $current > 1 ) : ?>
		<a
			href="<?php echo esc_url( $link( $current - 1 ) ); ?>"
			class="pagination__btn pagination__btn--arrow"
			aria-label="<?php esc_attr_e( 'Página anterior', 'cli' ); ?>"
		>
			<?php echo $arrow_prev; // phpcs:ignore WordPress.Security.EscapeOutput -- SVG estático. ?>
		</a>
		<?php else : ?>
		<span class="pagination__btn pagination__btn--arrow is-disabled" aria-hidden="true">
			<?php echo $arrow_prev; // phpcs:ignore WordPress.Security.EscapeOutput -- SVG estático. ?>
		</span>
		<?php endif; ?>

		<?php foreach ( cliconnect_pagination_page_items( $current, $total ) as $item ) : ?>
			<?php if ( '...' === $item ) : ?>
			<span class="pagination__ellipsis">&hellip;</span>
			<?php elseif ( (int) $item === $current ) : ?>
			<span class="pagination__btn is-current" aria-current="page">
				<?php echo esc_html( $item ); ?>
			</span>
			<?php else : ?>
			<a href="<?php echo esc_url( $link( $item ) ); ?>" class="pagination__btn">
				<?php echo esc_html( $item ); ?>
			</a>
			<?php endif; ?>
		<?php endforeach; ?>

		<?php if ( $current < $total ) : ?>
		<a
			href="<?php echo esc_url( $link( $current + 1 ) ); ?>"
			class="pagination__btn pagination__btn--arrow"
			aria-label="<?php esc_attr_e( 'Próxima página', 'cli' ); ?>"
		>
			<?php echo $arrow_next; // phpcs:ignore WordPress.Security.EscapeOutput -- SVG estático. ?>
		</a>
		<?php else : ?>
		<span class="pagination__btn pagination__btn--arrow is-disabled" aria-hidden="true">
			<?php echo $arrow_next; // phpcs:ignore WordPress.Security.EscapeOutput -- SVG estático. ?>
		</span>
		<?php endif; ?>

	</nav>
	<?php
}
