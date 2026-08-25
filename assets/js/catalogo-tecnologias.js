/**
 * Catálogo de Tecnologias — busca e filtro por categoria (client-side).
 *
 * Sem dependências de build. Carregado somente nas páginas de catálogo
 * (termo pai de cli_categoria_solucao).
 *
 * @package Cliconnect
 */

( function () {
	'use strict';

	var grade     = document.getElementById( 'cat-grade' );
	var busca     = document.getElementById( 'cat-busca-campo' );
	var botoes    = document.querySelectorAll( '.cat-sidebar__btn' );
	var semResult = document.querySelector( '.cat-sem-resultado' );

	if ( ! grade ) {
		return;
	}

	var tipoAtivo = '';

	/**
	 * Aplica os filtros de tipo e busca a todos os cards.
	 * Mostra a mensagem "sem resultado" quando nenhum card passa.
	 */
	function aplicarFiltros() {
		var query    = busca ? busca.value.trim().toLowerCase() : '';
		var cards    = grade.querySelectorAll( '.cat-card' );
		var visiveis = 0;

		cards.forEach( function ( card ) {
			var nome  = card.getAttribute( 'data-nome' ) || '';
			var tipos = card.getAttribute( 'data-tipo' ) || '';

			var passaBusca = ! query || nome.indexOf( query ) !== -1;
			var passaTipo  = ! tipoAtivo || tipos.split( ' ' ).indexOf( tipoAtivo ) !== -1;

			var mostrar = passaBusca && passaTipo;
			card.hidden = ! mostrar;

			if ( mostrar ) {
				visiveis++;
			}
		} );

		if ( semResult ) {
			semResult.hidden = visiveis > 0;
		}
	}

	// Busca em tempo real.
	if ( busca ) {
		busca.addEventListener( 'input', aplicarFiltros );
	}

	// Filtro por categoria.
	botoes.forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			var tipo = btn.getAttribute( 'data-tipo' );

			// Toggle: clique no ativo remove o filtro.
			if ( tipoAtivo === tipo ) {
				tipoAtivo = '';
			} else {
				tipoAtivo = tipo;
			}

			// Atualiza estado visual + aria-pressed.
			botoes.forEach( function ( b ) {
				var ativo = b.getAttribute( 'data-tipo' ) === tipoAtivo;
				b.classList.toggle( 'cat-sidebar__btn--ativo', ativo );
				b.setAttribute( 'aria-pressed', ativo ? 'true' : 'false' );
			} );

			aplicarFiltros();
		} );
	} );
}() );
