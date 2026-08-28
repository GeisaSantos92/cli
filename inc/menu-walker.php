<?php
/**
 * Walker do menu principal.
 *
 * O formato do dropdown sai da própria estrutura do menu, sem opção extra:
 *
 * - item de 1º nível sem filhos       → link simples;
 * - filhos sem netos ("Plataforma")   → painel de cartões (título + descrição
 *   do item de menu);
 * - filhos com netos ("Soluções")     → mega menu em colunas, onde cada filho é
 *   o título de uma coluna e os netos são os links. Um filho sem netos vira o
 *   link "Ver todos" no rodapé do painel.
 *
 * Nos itens com submenu entra também um botão acessível (aria-expanded +
 * aria-controls). No desktop o painel abre por hover/focus-within via CSS;
 * o botão existe para toque e teclado.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Menu principal com painel de cartões e mega menu.
 */
class Cliconnect_Walker_Nav_Menu extends Walker_Nav_Menu {

	/**
	 * IDs dos filhos de cada item (ID do pai => IDs dos filhos).
	 *
	 * @var array<int,int[]>
	 */
	protected $filhos = array();

	/**
	 * ID do item cujo submenu está sendo aberto.
	 *
	 * @var int
	 */
	protected $item_atual = 0;

	/**
	 * Formato do painel do item de 1º nível em curso ('cartoes' ou 'mega').
	 *
	 * @var string
	 */
	protected $painel = 'cartoes';

	/**
	 * Indexa a árvore antes de percorrer, para saber quem tem filhos e netos.
	 *
	 * @param array $elements  Itens do menu.
	 * @param int   $max_depth Profundidade máxima.
	 * @param mixed ...$args   Argumentos do wp_nav_menu.
	 * @return string
	 */
	public function walk( $elements, $max_depth, ...$args ) {
		$this->filhos = array();

		foreach ( (array) $elements as $elemento ) {
			$this->filhos[ (int) $elemento->menu_item_parent ][] = (int) $elemento->ID;
		}

		return parent::walk( $elements, $max_depth, ...$args );
	}

	/**
	 * Abre o painel do dropdown (1º nível) ou a lista de uma coluna (2º nível).
	 *
	 * @param string   $output Markup acumulado (por referência).
	 * @param int      $depth  Nível atual.
	 * @param stdClass $args   Argumentos do wp_nav_menu.
	 * @return void
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		if ( 0 === $depth ) {
			$output .= sprintf(
				'<div class="site-nav__painel site-nav__painel--%1$s" id="submenu-%2$d"><div class="site-nav__painel-inner"><ul class="sub-menu">',
				esc_attr( $this->painel ),
				$this->item_atual
			);

			return;
		}

		$output .= '<ul class="site-nav__grupo-lista">';
	}

	/**
	 * Fecha o painel ou a lista da coluna.
	 *
	 * @param string   $output Markup acumulado (por referência).
	 * @param int      $depth  Nível atual.
	 * @param stdClass $args   Argumentos do wp_nav_menu.
	 * @return void
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= ( 0 === $depth ) ? '</ul></div></div>' : '</ul>';
	}

	/**
	 * Renderiza o item conforme o nível e o formato do painel.
	 *
	 * @param string   $output Markup acumulado (por referência).
	 * @param WP_Post  $item   Item do menu.
	 * @param int      $depth  Nível atual.
	 * @param stdClass $args   Argumentos do wp_nav_menu.
	 * @param int      $id     ID do menu.
	 * @return void
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$this->item_atual = (int) $item->ID;

		$tem_filhos = ! empty( $this->filhos[ (int) $item->ID ] );

		if ( 0 === $depth ) {
			$this->painel = $this->tem_netos( $item->ID ) ? 'mega' : 'cartoes';
		}

		$classes   = array_filter( (array) $item->classes );
		$classes[] = 'menu-item-' . $item->ID;

		if ( 0 === $depth && $tem_filhos ) {
			$classes[] = 'site-nav__item--has-children';
			$classes[] = 'site-nav__item--' . $this->painel;
		}

		if ( 1 === $depth ) {
			if ( 'cartoes' === $this->painel ) {
				$classes[] = 'site-nav__cartao-item';
			} else {
				$classes[] = $tem_filhos ? 'site-nav__grupo' : 'site-nav__rodape-painel';
			}
		}

		$output .= '<li class="' . esc_attr( implode( ' ', array_unique( $classes ) ) ) . '">';

		$link = $this->montar_link( $item, $depth, $tem_filhos );

		if ( 0 === $depth && $tem_filhos ) {
			$output .= '<span class="site-nav__row">' . $link . sprintf(
				'<button class="site-nav__toggle" type="button" aria-expanded="false" aria-controls="submenu-%1$d" data-submenu-toggle><span class="visually-hidden">%2$s</span>%3$s</button>',
				(int) $item->ID,
				sprintf(
					/* translators: %s: nome do item de menu */
					esc_html__( 'Abrir submenu de %s', 'cli' ),
					esc_html( $item->title )
				),
				cliconnect_icone( 'chevron-baixo', 16 )
			) . '</span>';

			return;
		}

		$output .= $link;
	}

	/**
	 * Fecha o item.
	 *
	 * @param string   $output Markup acumulado (por referência).
	 * @param WP_Post  $item   Item do menu.
	 * @param int      $depth  Nível atual.
	 * @param stdClass $args   Argumentos do wp_nav_menu.
	 * @return void
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		$output .= "</li>\n";
	}

	/**
	 * Um item de 1º nível tem netos? (define mega menu × painel de cartões)
	 *
	 * @param int $id ID do item.
	 * @return bool
	 */
	protected function tem_netos( $id ) {
		foreach ( $this->filhos[ (int) $id ] ?? array() as $filho ) {
			if ( ! empty( $this->filhos[ $filho ] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Monta o <a> do item, no formato do nível em que ele está.
	 *
	 * @param WP_Post $item       Item do menu.
	 * @param int     $depth      Nível atual.
	 * @param bool    $tem_filhos Se o item tem filhos.
	 * @return string
	 */
	protected function montar_link( $item, $depth, $tem_filhos ) {
		$atributos = $this->montar_atributos( $item );

		/*
		 * Item de 1º nível que existe só para abrir o painel: sem URL própria
		 * (vazia ou "#"), o rótulo não vira link. Quem abre o painel é o hover
		 * no desktop e o botão de toggle no toque e no teclado.
		 */
		if ( 0 === $depth && $tem_filhos && in_array( trim( (string) ( $item->url ?? '' ) ), array( '', '#' ), true ) ) {
			return sprintf(
				'<span class="site-nav__rotulo">%s</span>',
				esc_html( $item->title )
			);
		}

		// Cartão do painel de "Plataforma": título + descrição do item de menu.
		if ( 1 === $depth && 'cartoes' === $this->painel ) {
			$descricao = $item->description ?? '';

			return sprintf(
				'<a class="nav-cartao"%1$s><span class="nav-cartao__titulo">%2$s</span>%3$s<span class="nav-cartao__seta" aria-hidden="true">%4$s</span></a>',
				$atributos,
				esc_html( $item->title ),
				$descricao ? '<span class="nav-cartao__texto">' . esc_html( $descricao ) . '</span>' : '',
				cliconnect_icone( 'seta-nordeste', 20 )
			);
		}

		// Título de coluna do mega menu.
		if ( 1 === $depth && $tem_filhos ) {
			return sprintf(
				'<a class="site-nav__grupo-titulo"%1$s>%2$s</a>',
				$atributos,
				esc_html( $item->title )
			);
		}

		// "Ver todos" no rodapé do mega menu.
		if ( 1 === $depth ) {
			return sprintf(
				'<a class="site-nav__ver-todos"%1$s>%2$s%3$s</a>',
				$atributos,
				esc_html( $item->title ),
				cliconnect_icone( 'seta-nordeste', 16 )
			);
		}

		/*
		 * Link de coluna: ganha o logo quando existe uma Integração de mesmo
		 * nome — a menos que o item traga a classe `link-sem-logo`, usada onde
		 * a marca não deve aparecer (os provedores de nuvem, por exemplo).
		 */
		if ( $depth >= 2 ) {
			// "Ver todos" dentro de um grupo de coluna (class link-ver-todos no item).
			if ( in_array( 'link-ver-todos', (array) $item->classes, true ) ) {
				return sprintf(
					'<a class="site-nav__ver-todos"%1$s>%2$s%3$s</a>',
					$atributos,
					esc_html( $item->title ),
					cliconnect_icone( 'seta-nordeste', 16 )
				);
			}

			$sem_logo = in_array( 'link-sem-logo', (array) $item->classes, true );
			$logo     = $sem_logo ? '' : cliconnect_logo_integracao( $item->title );

			return sprintf(
				'<a%1$s>%2$s%3$s</a>',
				$atributos,
				$logo ? '<span class="site-nav__icone">' . $logo . '</span>' : '',
				esc_html( $item->title )
			);
		}

		return sprintf( '<a%1$s>%2$s</a>', $atributos, esc_html( $item->title ) );
	}

	/**
	 * Atributos HTML do link (href, target, rel, title), já escapados.
	 *
	 * @param WP_Post $item Item do menu.
	 * @return string
	 */
	protected function montar_atributos( $item ) {
		$atts = array(
			'title'  => $item->attr_title ?? '',
			'target' => $item->target ?? '',
			'rel'    => $item->xfn ?? '',
			'href'   => $item->url ?? '',
		);

		$saida = '';

		foreach ( $atts as $attr => $value ) {
			if ( '' === $value || null === $value ) {
				continue;
			}

			$value  = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
			$saida .= ' ' . $attr . '="' . $value . '"';
		}

		return $saida;
	}
}
