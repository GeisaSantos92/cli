/**
 * CLI Connect — animação scroll-triggered da seção "Implantação rápida".
 *
 * Mesmo padrão de assets/js/page-integracao-sap.js: adiciona `.cc-impl-animar`
 * imediatamente (etapas partem de opacity:0) e `.is-visible` quando a seção
 * entra no viewport — dispara o fade-in.
 *
 * @package Cliconnect
 */
( function () {
	'use strict';

	var section = document.querySelector( '.cc-impl' );
	if ( ! section ) {
		return;
	}

	section.classList.add( 'cc-impl-animar' );

	var observer = new IntersectionObserver(
		function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'is-visible' );
					observer.unobserve( entry.target );
				}
			} );
		},
		{ threshold: 0.2 }
	);

	observer.observe( section );
}() );
