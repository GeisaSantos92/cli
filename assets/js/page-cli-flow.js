/**
 * CLI Flow — interatividade da página.
 *
 * - Hero: slider de telas com troca lateral suave (as abas deslocam o trilho).
 * - Casos de uso: abas de área, lista de processos e um único player de
 *   vídeo que troca de fonte por processo (estratégia da issue #231).
 * - Como funciona: vídeo que só carrega perto da tela, toca visível e pausa
 *   fora dela.
 *
 * @package Cliconnect
 */
( function () {
	'use strict';

	var temObserver = 'IntersectionObserver' in window;

	/**
	 * Marca uma aba como ativa dentro do seu grupo.
	 *
	 * @param {NodeList} abas Abas do grupo.
	 * @param {Element}  ativa Aba clicada.
	 */
	function ativarAba( abas, ativa ) {
		abas.forEach( function ( aba ) {
			var eh = aba === ativa;
			aba.classList.toggle( 'ativo', eh );
			aba.setAttribute( 'aria-selected', eh ? 'true' : 'false' );
		} );
	}

	/* =====================================================================
	   HERO — slider com troca lateral
	   ===================================================================== */
	( function () {
		var abas    = document.querySelectorAll( '[data-cf-mockup-tab]' );
		var trilho  = document.querySelector( '[data-cf-mockup-trilho]' );

		if ( ! abas.length || ! trilho ) {
			return;
		}

		var paineis = trilho.querySelectorAll( '.cf-mockup__painel' );

		abas.forEach( function ( aba, indice ) {
			aba.addEventListener( 'click', function () {
				ativarAba( abas, aba );
				trilho.style.setProperty( '--cf-slide', indice );
				paineis.forEach( function ( painel, i ) {
					painel.classList.toggle( 'ativo', i === indice );
					if ( i === indice ) {
						painel.removeAttribute( 'aria-hidden' );
					} else {
						painel.setAttribute( 'aria-hidden', 'true' );
					}
				} );
			} );
		} );
	} )();

	/* =====================================================================
	   CASOS DE USO — abas, processos e player único
	   ===================================================================== */
	( function () {
		var secao = document.querySelector( '[data-cf-casos]' );

		if ( ! secao ) {
			return;
		}

		var abas    = secao.querySelectorAll( '[data-cf-caso-tab]' );
		var paineis = secao.querySelectorAll( '.cf-casos__painel' );
		var player  = secao.querySelector( '[data-cf-player]' );
		var video   = secao.querySelector( '[data-cf-video]' );
		var titulo  = secao.querySelector( '[data-cf-caso-titulo]' );
		var atual   = secao.querySelector( '[data-cf-caso-item].ativo' );
		var liberado = ! temObserver; // Sem observer, carrega direto.
		var visivel  = ! temObserver;

		/** Toca o vídeo se ele estiver na tela e tiver fonte. */
		function tocar() {
			if ( visivel && video.querySelector( 'source' ) ) {
				var promessa = video.play();
				if ( promessa && promessa.catch ) {
					promessa.catch( function () {} );
				}
			}
		}

		/**
		 * Põe no player o vídeo e a capa do processo. Só um vídeo por vez:
		 * as fontes anteriores saem antes de entrar as novas.
		 *
		 * @param {Element} item Botão do processo.
		 */
		function carregar( item ) {
			var webm = item.getAttribute( 'data-webm' );
			var mp4  = item.getAttribute( 'data-mp4' );
			var capa = item.getAttribute( 'data-capa' );

			if ( titulo ) {
				titulo.textContent = item.textContent;
			}
			video.setAttribute( 'aria-label', item.textContent );

			video.pause();
			while ( video.firstChild ) {
				video.removeChild( video.firstChild );
			}

			if ( ! webm && ! mp4 ) {
				video.removeAttribute( 'poster' );
				video.load(); // Interrompe qualquer download anterior.
				player.classList.add( 'sem-video' );
				return;
			}

			// WebM primeiro, MP4 depois.
			[ [ webm, 'video/webm' ], [ mp4, 'video/mp4' ] ].forEach( function ( fonte ) {
				if ( fonte[0] ) {
					var source  = document.createElement( 'source' );
					source.src  = fonte[0];
					source.type = fonte[1];
					video.appendChild( source );
				}
			} );

			if ( capa ) {
				video.setAttribute( 'poster', capa );
			} else {
				video.removeAttribute( 'poster' );
			}

			player.classList.remove( 'sem-video' );
			video.load();
			tocar();
		}

		/**
		 * Seleciona um processo (botão) e, se a seção já foi liberada,
		 * troca o player.
		 *
		 * @param {Element} item Botão do processo.
		 */
		function selecionar( item ) {
			if ( ! item ) {
				return;
			}
			secao.querySelectorAll( '[data-cf-caso-item]' ).forEach( function ( i ) {
				var eh = i === item;
				i.classList.toggle( 'ativo', eh );
				i.setAttribute( 'aria-pressed', eh ? 'true' : 'false' );
			} );
			atual = item;
			if ( liberado ) {
				carregar( item );
			} else if ( titulo ) {
				titulo.textContent = item.textContent;
			}
		}

		// Abas de área: mostram a lista e selecionam o primeiro processo dela.
		abas.forEach( function ( aba ) {
			aba.addEventListener( 'click', function () {
				var idx = aba.getAttribute( 'data-cf-caso-tab' );
				ativarAba( abas, aba );
				paineis.forEach( function ( painel ) {
					painel.classList.toggle( 'ativo', painel.getAttribute( 'data-cf-panel' ) === idx );
				} );
				var painel = secao.querySelector( '.cf-casos__painel[data-cf-panel="' + idx + '"]' );
				selecionar( painel ? painel.querySelector( '[data-cf-caso-item]' ) : null );
			} );
		} );

		// Processos.
		secao.querySelectorAll( '[data-cf-caso-item]' ).forEach( function ( item ) {
			item.addEventListener( 'click', function () {
				selecionar( item );
			} );
		} );

		if ( ! temObserver ) {
			carregar( atual );
			return;
		}

		// O primeiro vídeo só carrega quando a seção chega a ~200px da tela.
		var aproximacao = new IntersectionObserver( function ( entradas ) {
			if ( entradas[0].isIntersecting ) {
				liberado = true;
				aproximacao.disconnect();
				carregar( atual );
			}
		}, { rootMargin: '200px 0px' } );
		aproximacao.observe( secao );

		// Toca visível; pausa fora da tela.
		new IntersectionObserver( function ( entradas ) {
			visivel = entradas[0].isIntersecting;
			if ( visivel ) {
				tocar();
			} else {
				video.pause();
			}
		}, { threshold: 0.25 } ).observe( player );
	} )();

	/* =====================================================================
	   COMO FUNCIONA — vídeo preguiçoso
	   ===================================================================== */
	document.querySelectorAll( '[data-cf-video-lazy]' ).forEach( function ( video ) {
		var tocar = function () {
			var promessa = video.play();
			if ( promessa && promessa.catch ) {
				promessa.catch( function () {} );
			}
		};

		if ( ! temObserver ) {
			tocar();
			return;
		}

		// Com preload="none", nada é baixado até o play() perto da tela.
		new IntersectionObserver( function ( entradas ) {
			if ( entradas[0].isIntersecting ) {
				tocar();
			} else {
				video.pause();
			}
		}, { rootMargin: '200px 0px', threshold: 0 } ).observe( video );
	} );
} )();
