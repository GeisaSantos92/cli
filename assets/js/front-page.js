/* ==========================================================================
   CLI Connect — comportamentos da home (front-page.php).

   Enfileirado só em is_front_page() (inc/enqueue.php).
   ========================================================================== */
(function () {
	'use strict';

	function ready(fn) {
		if (document.readyState !== 'loading') fn();
		else document.addEventListener('DOMContentLoaded', fn);
	}

	ready(function () {
		initOrbitaHero();
	});

	/* ----- Órbita do hero: linhas e entrada dos logos ---------------------- */

	/*
	 * O CSS posiciona as 16 bolhas em porcentagens tiradas do Figma; o que ele
	 * não sabe é onde o título termina em cada largura. Aqui medimos o centro
	 * real do <h1> e, para cada bolha, escrevemos duas coisas:
	 *
	 *   --start-x / --start-y   vetor até esse centro (a bolha "nasce" nele)
	 *   <line> + gradiente      traço do centro até a bolha, desenhado no CSS
	 *
	 * Tudo em pixels do próprio hero, recalculado no resize — sem coordenada
	 * fixa, sem viewBox.
	 */
	function initOrbitaHero() {
		var hero = document.querySelector('.hero');
		if (!hero) return;

		var titulo = hero.querySelector('.hero__titulo');
		var grupo = hero.querySelector('.hero__linhas-grupo');
		var defs = hero.querySelector('.hero__linhas-defs');
		var logos = hero.querySelectorAll('.hero__logo');

		if (!titulo || !grupo || !defs || !logos.length) return;

		var SVG_NS = 'http://www.w3.org/2000/svg';
		var COR_LINHA = '#3551f2';

		/*
		 * Centro do elemento em pixels do hero, somando a cadeia de offsetParent.
		 * De propósito não usa getBoundingClientRect(): durante o `animation-delay`
		 * a bolha ainda está deslocada para o centro do título, e o retângulo
		 * devolveria a posição de partida — as linhas nasceriam com comprimento
		 * zero. `offsetLeft/offsetTop` ignoram transform e dão o destino real.
		 */
		function centroNoHero(el) {
			var x = el.offsetWidth / 2;
			var y = el.offsetHeight / 2;

			for (var no = el; no && no !== hero; no = no.offsetParent) {
				x += no.offsetLeft;
				y += no.offsetTop;
			}

			return { x: x, y: y };
		}

		function atualizarOrbita() {
			// Sem altura o hero ainda não foi pintado (display:none, por ex.).
			if (!hero.offsetWidth || !hero.offsetHeight) return;

			var centro = centroNoHero(titulo);
			var centroX = centro.x;
			var centroY = centro.y;

			// A função roda de novo a cada resize: recomeça do zero.
			grupo.innerHTML = '';
			defs.innerHTML = '';

			Array.prototype.forEach.call(logos, function (logo, indice) {
				if (!logo.offsetWidth) return;

				var alvo = centroNoHero(logo);
				var logoX = alvo.x;
				var logoY = alvo.y;

				// Ponto de partida da entrada: o centro do título.
				logo.style.setProperty('--start-x', centroX - logoX + 'px');
				logo.style.setProperty('--start-y', centroY - logoY + 'px');

				var distanciaX = logoX - centroX;
				var distanciaY = logoY - centroY;
				var comprimento = Math.sqrt(distanciaX * distanciaX + distanciaY * distanciaY);

				defs.appendChild(criarGradiente(indice, centroX, centroY, logoX, logoY));

				var linha = document.createElementNS(SVG_NS, 'line');
				linha.setAttribute('x1', centroX);
				linha.setAttribute('y1', centroY);
				linha.setAttribute('x2', logoX);
				linha.setAttribute('y2', logoY);
				linha.setAttribute('stroke', 'url(#hero-linha-' + indice + ')');
				linha.style.setProperty('--linha-comprimento', comprimento + 'px');

				grupo.appendChild(linha);
			});
		}

		/*
		 * Um gradiente por linha, em coordenadas do SVG (userSpaceOnUse): quase
		 * invisível junto ao título e mais presente ao chegar na bolha.
		 */
		function criarGradiente(indice, x1, y1, x2, y2) {
			var gradiente = document.createElementNS(SVG_NS, 'linearGradient');

			gradiente.setAttribute('id', 'hero-linha-' + indice);
			gradiente.setAttribute('gradientUnits', 'userSpaceOnUse');
			gradiente.setAttribute('x1', x1);
			gradiente.setAttribute('y1', y1);
			gradiente.setAttribute('x2', x2);
			gradiente.setAttribute('y2', y2);

			[[0, 0], [0.85, 0.05], [1, 0.28]].forEach(function (parada) {
				var stop = document.createElementNS(SVG_NS, 'stop');

				stop.setAttribute('offset', parada[0]);
				stop.setAttribute('stop-color', COR_LINHA);
				stop.setAttribute('stop-opacity', parada[1]);
				gradiente.appendChild(stop);
			});

			return gradiente;
		}

		atualizarOrbita();

		// Depois do load as imagens já ocupam o espaço final — remede.
		window.addEventListener('load', atualizarOrbita);

		var timer;
		window.addEventListener('resize', function () {
			clearTimeout(timer);
			timer = setTimeout(atualizarOrbita, 150);
		});
	}

})();
