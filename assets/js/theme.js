/* ==========================================================================
   CLI Connect — comportamentos vanilla do tema.

   Mantenha este arquivo enxuto: interações específicas de página vão em
   arquivos próprios, enfileirados por contexto em inc/enqueue.php.
   ========================================================================== */
(function () {
	'use strict';

	document.documentElement.classList.add('js');

	function ready(fn) {
		if (document.readyState !== 'loading') fn();
		else document.addEventListener('DOMContentLoaded', fn);
	}

	ready(function () {
		initMobileNav();
		initSubmenus();
		initSeletorIdiomas();
		initVoltarAoTopo();
		initFaq();
		initMetricasCounters();
		initCaseScrollSpy();
	});

	/* ----- Seletor de idiomas (globo do header) ---------------------------- */
	function initSeletorIdiomas() {
		var seletor = document.querySelector('[data-idiomas]');
		if (!seletor) return;

		var gatilho = seletor.querySelector('.site-header__idiomas-gatilho');
		if (!gatilho) return;

		function fechar() {
			seletor.classList.remove('is-open');
			gatilho.setAttribute('aria-expanded', 'false');
		}

		gatilho.addEventListener('click', function () {
			var aberto = seletor.classList.toggle('is-open');
			gatilho.setAttribute('aria-expanded', aberto ? 'true' : 'false');
		});

		document.addEventListener('click', function (e) {
			if (!seletor.contains(e.target)) fechar();
		});

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') fechar();
		});
	}

	/* ----- Botão de voltar ao topo ----------------------------------------
	 *
	 * Aparece depois de uma dobra de rolagem. O scroll suave fica com o CSS
	 * (html { scroll-behavior: smooth }), que já respeita prefers-reduced-motion.
	 */
	function initVoltarAoTopo() {
		var botao = document.querySelector('[data-voltar-ao-topo]');
		if (!botao) return;

		function atualizar() {
			botao.classList.toggle('is-visivel', window.scrollY > window.innerHeight * 0.6);
		}

		botao.addEventListener('click', function () {
			window.scrollTo({ top: 0 });
		});

		window.addEventListener('scroll', atualizar, { passive: true });
		atualizar();
	}

	/* ----- Contagem dos números (métricas da home e dos cases) -------------
	 *
	 * Os valores são texto livre do ACF ("+200", "5 dias", "30 mil", "10%"):
	 * animamos só o primeiro trecho numérico e devolvemos prefixo e sufixo
	 * intactos, senão a animação apagaria o "+", o "%" e as palavras.
	 */
	function initMetricasCounters() {
		var numeros = document.querySelectorAll('.metrica__numero, .case-metrica__numero');
		if (!numeros.length || !('IntersectionObserver' in window)) return;

		// Sem animação para quem pediu menos movimento: o valor final já está no HTML.
		if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

		var observador = new IntersectionObserver(function (entradas) {
			entradas.forEach(function (entrada) {
				if (!entrada.isIntersecting) return;

				observador.unobserve(entrada.target);
				animarNumero(entrada.target);
			});
		}, { threshold: 0.4 });

		Array.prototype.forEach.call(numeros, function (numero) {
			observador.observe(numero);
		});
	}

	function animarNumero(elemento) {
		var texto = elemento.textContent;
		var achado = texto.match(/\d[\d.,]*/);
		if (!achado) return;

		var bruto = achado[0];
		var alvo = parseInt(bruto.replace(/[.,]/g, ''), 10);
		if (isNaN(alvo) || alvo === 0) return;

		var prefixo = texto.slice(0, achado.index);
		var sufixo = texto.slice(achado.index + bruto.length);
		// Só reformata com separador de milhar se o valor original já usava um.
		var agrupa = /[.,]/.test(bruto);
		var duracao = 1600;
		var inicio = null;

		function render(valor) {
			var formatado = agrupa ? valor.toLocaleString('pt-BR') : String(valor);
			elemento.textContent = prefixo + formatado + sufixo;
		}

		function passo(agora) {
			if (inicio === null) inicio = agora;

			var progresso = Math.min((agora - inicio) / duracao, 1);
			// ease-out cúbico: desacelera ao chegar no valor final.
			var suave = 1 - Math.pow(1 - progresso, 3);

			render(Math.round(alvo * suave));

			if (progresso < 1) requestAnimationFrame(passo);
		}

		render(0);
		requestAnimationFrame(passo);
	}

	/* ----- Menu mobile (botão [data-nav-toggle] + .site-nav.is-open) ------- */
	function initMobileNav() {
		var toggle = document.querySelector('[data-nav-toggle]');
		var nav = document.getElementById('site-nav');
		if (!toggle || !nav) return;

		toggle.addEventListener('click', function () {
			var open = nav.classList.toggle('is-open');
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			toggle.setAttribute('aria-label', open ? 'Fechar menu' : 'Abrir menu');
		});

		// Fecha com Esc e devolve o foco ao botão.
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && nav.classList.contains('is-open')) {
				nav.classList.remove('is-open');
				toggle.setAttribute('aria-expanded', 'false');
				toggle.focus();
			}
		});
	}

	/* ----- Dropdowns do menu principal ------------------------------------ */
	function initSubmenus() {
		var toggles = document.querySelectorAll('[data-submenu-toggle]');
		if (!toggles.length) return;

		function fecharTodos(exceto) {
			Array.prototype.forEach.call(toggles, function (btn) {
				var item = btn.closest('.site-nav__item--has-children');
				if (!item || item === exceto) return;
				item.classList.remove('is-open');
				btn.setAttribute('aria-expanded', 'false');
			});
		}

		Array.prototype.forEach.call(toggles, function (btn) {
			btn.addEventListener('click', function (e) {
				e.preventDefault();
				var item = btn.closest('.site-nav__item--has-children');
				if (!item) return;

				var aberto = item.classList.toggle('is-open');
				btn.setAttribute('aria-expanded', aberto ? 'true' : 'false');

				if (aberto) fecharTodos(item);
			});
		});

		// Clique fora fecha os dropdowns abertos.
		document.addEventListener('click', function (e) {
			if (!e.target.closest('.site-nav__item--has-children')) fecharTodos(null);
		});

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') fecharTodos(null);
		});
	}

	/* ----- Acordeão do FAQ ------------------------------------------------- */
	function initFaq() {
		var gatilhos = document.querySelectorAll('[data-faq-gatilho]');
		if (!gatilhos.length) return;

		Array.prototype.forEach.call(gatilhos, function (gatilho) {
			gatilho.addEventListener('click', function () {
				var id = gatilho.getAttribute('aria-controls');
				var resposta = id ? document.getElementById(id) : null;
				if (!resposta) return;

				var aberto = gatilho.getAttribute('aria-expanded') === 'true';

				// Só uma resposta aberta por vez.
				Array.prototype.forEach.call(gatilhos, function (outro) {
					if (outro === gatilho) return;
					var outroId = outro.getAttribute('aria-controls');
					var outraResposta = outroId ? document.getElementById(outroId) : null;
					outro.setAttribute('aria-expanded', 'false');
					if (outraResposta) {
						outraResposta.classList.remove('is-open');
						outraResposta.hidden = true;
					}
				});

				gatilho.setAttribute('aria-expanded', aberto ? 'false' : 'true');
				resposta.classList.toggle('is-open', !aberto);
				resposta.hidden = aberto;
			});
		});
	}

	/* ----- Scroll spy da página de case (sidebar âncora) ------------------- */

	function initCaseScrollSpy() {
		var links = document.querySelectorAll('.case-topicos__link');
		var sections = document.querySelectorAll('[data-scroll-spy-section]');

		if (!links.length || !sections.length) return;

		var activeClass = 'case-topicos__link--active';

		function setActive(id) {
			Array.prototype.forEach.call(links, function (link) {
				var href = link.getAttribute('href');
				link.classList.toggle(activeClass, href === '#' + id);
			});
		}

		// Define a primeira seção como ativa ao carregar.
		if (sections[0]) {
			setActive(sections[0].id);
		}

		var observer = new IntersectionObserver(
			function (entries) {
				Array.prototype.forEach.call(entries, function (entry) {
					if (entry.isIntersecting) {
						setActive(entry.target.id);
					}
				});
			},
			{ rootMargin: '-20% 0px -70% 0px', threshold: 0 }
		);

		Array.prototype.forEach.call(sections, function (section) {
			observer.observe(section);
		});
	}

})();
