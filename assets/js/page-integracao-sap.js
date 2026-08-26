/**
 * Integração SAP — animação scroll-triggered da seção velocidade.
 *
 * Adiciona `.sap-vel-animar` imediatamente (etapas partem de opacity:0)
 * e `.is-visible` quando a seção entra no viewport — dispara o fade-in.
 *
 * @package Cliconnect
 */
( function () {
	'use strict';

	var section = document.querySelector( '.sap-velocidade' );
	if ( ! section ) {
		return;
	}

	section.classList.add( 'sap-vel-animar' );

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
