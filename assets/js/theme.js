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
		initFooterCta();
		initCookieBanner();
		initLeadPopup();
		initFormLead();
		initMascaraTelefone();
		initVideoOverlay();
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

		// No desktop, hover abre e fecha (mesmo padrão dos dropdowns do nav).
		seletor.addEventListener('mouseenter', function () {
			if (window.innerWidth < 1025) return;
			clearTimeout(seletor.__closeTimer);
			seletor.classList.add('is-open');
			gatilho.setAttribute('aria-expanded', 'true');
		});

		seletor.addEventListener('mouseleave', function () {
			if (window.innerWidth < 1025) return;
			seletor.__closeTimer = setTimeout(fechar, 100);
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
				clearTimeout(item.__closeTimer);
				item.classList.remove('is-open');
				btn.setAttribute('aria-expanded', 'false');
			});
		}

		Array.prototype.forEach.call(toggles, function (btn) {
			var item = btn.closest('.site-nav__item--has-children');
			if (!item) return;

			// Clique no botão de toggle (mobile e teclado).
			btn.addEventListener('click', function (e) {
				e.preventDefault();
				var aberto = item.classList.toggle('is-open');
				btn.setAttribute('aria-expanded', aberto ? 'true' : 'false');
				if (aberto) fecharTodos(item);
			});

			// No desktop, hover abre e fecha via JS — elimina o problema do gap
			// entre o <li> e o painel que fazia o CSS :hover perder o estado.
			// O delay de 100ms no mouseleave evita fechamento prematuro quando o
			// mouse passa brevemente por outro elemento do header antes de chegar
			// ao painel (especialmente no mega menu de Soluções, mais largo).
			item.addEventListener('mouseenter', function () {
				if (window.innerWidth < 1025) return;
				clearTimeout(item.__closeTimer);
				fecharTodos(item);
				item.classList.add('is-open');
				btn.setAttribute('aria-expanded', 'true');
			});

			item.addEventListener('mouseleave', function () {
				if (window.innerWidth < 1025) return;
				item.__closeTimer = setTimeout(function () {
					item.classList.remove('is-open');
					btn.setAttribute('aria-expanded', 'false');
				}, 100);
			});

			// No desktop, clicar no texto do item pai não deve navegar.
			var link = item.querySelector('.site-nav__row > a');
			if (link) {
				link.addEventListener('click', function (e) {
					if (window.innerWidth >= 1025) e.preventDefault();
				});
			}
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

	/* ----- Banner de cookies ---------------------------------------------- */

	/*
	 * O banner nasce com [hidden] no PHP. Só aparece quando não há aceite
	 * gravado — assim quem já concordou não vê nem o flash. A escolha fica em
	 * localStorage (mesma origem), não em cookie: é aviso, não consentimento
	 * granular.
	 */
	function initCookieBanner() {
		var banner = document.querySelector('[data-cookie-banner]');
		if (!banner) return;

		var CHAVE = 'cliconnect_cookies_ok';

		function lembrado() {
			try {
				return window.localStorage.getItem(CHAVE) === '1';
			} catch (e) {
				// Modo privado ou storage bloqueado: mostra o banner sempre.
				return false;
			}
		}

		if (lembrado()) return;

		banner.hidden = false;

		/*
		 * No celular o aviso é uma faixa no rodapé da tela: a altura dela sobe
		 * o WhatsApp e o "voltar ao topo" (CSS em .tem-aviso-cookies).
		 */
		var raiz = document.documentElement;

		function medir() {
			raiz.style.setProperty('--altura-aviso-cookies', banner.offsetHeight + 'px');
		}

		document.body.classList.add('tem-aviso-cookies');
		medir();
		window.addEventListener('resize', medir);

		var botao = banner.querySelector('[data-cookie-aceitar]');
		if (!botao) return;

		botao.addEventListener('click', function () {
			try {
				window.localStorage.setItem(CHAVE, '1');
			} catch (e) {
				// Sem storage, o banner volta no próximo carregamento.
			}

			banner.hidden = true;
			document.body.classList.remove('tem-aviso-cookies');
			window.removeEventListener('resize', medir);
		});
	}

	/* ----- Footer CTA: adapta gradiente à cor da seção anterior ----------- */
	function initFooterCta() {
		var cta = document.querySelector('.footer-cta');
		if (!cta) return;

		var secoes = Array.prototype.slice.call(document.querySelectorAll('section'));
		var idx    = secoes.indexOf(cta);
		if (idx <= 0) return;

		var cor = window.getComputedStyle(secoes[idx - 1]).backgroundColor;

		// Ignora transparente — o CSS já tem o fallback correto.
		if (!cor || cor === 'rgba(0, 0, 0, 0)' || cor === 'transparent') return;

		cta.style.backgroundImage =
			'linear-gradient(180deg, ' + cor + ' 0%, ' + cor + ' 50%, ' +
			'var(--cor-marca-profunda) 50%, var(--cor-marca-profunda) 100%)';
	}

	/* ----- Popup de lead (botão flutuante de WhatsApp) -------------------- */

	/*
	 * Todo elemento com [data-lead-popup] abre o <dialog> do formulário em vez
	 * de seguir o link. O formulário devolve o visitante para o WhatsApp
	 * depois do envio (retURL). Sem suporte a <dialog>, o link segue normal.
	 */
	function initLeadPopup() {
		var dialogo = document.querySelector('[data-lead-dialog]');
		if (!dialogo || typeof dialogo.showModal !== 'function') return;

		var gatilho = null;

		document.addEventListener('click', function (ev) {
			var alvo = ev.target.closest ? ev.target.closest('[data-lead-popup]') : null;
			if (!alvo) return;

			ev.preventDefault();
			gatilho = alvo;
			dialogo.showModal();
			document.documentElement.classList.add('lead-popup-aberto');

			var primeiro = dialogo.querySelector('input:not([type="hidden"]):not([tabindex="-1"])');
			if (primeiro) primeiro.focus();
		});

		dialogo.addEventListener('click', function (ev) {
			// Clique no fundo (fora da caixa) ou no botão de fechar.
			if (ev.target === dialogo || (ev.target.closest && ev.target.closest('[data-lead-fechar]'))) {
				dialogo.close();
			}
		});

		dialogo.addEventListener('close', function () {
			document.documentElement.classList.remove('lead-popup-aberto');
			if (gatilho) gatilho.focus();
		});
	}

	/* ----- Overlay de vídeo (cases, issue #232) ----------------------------- */

	/*
	 * Um link com [data-video-overlay="<url de embed>"] abre o vídeo num
	 * <dialog> sobre a página, como no hero da Ploomes. O iframe só é criado
	 * no clique (nada de YouTube carregando à toa) e é destruído ao fechar,
	 * o que também para o vídeo. ESC, clique fora e o botão fecham.
	 * Sem JS, o link abre o vídeo em outra aba.
	 */
	function initVideoOverlay() {
		var dialogo = null;
		var player = null;
		var fechar = null;
		var gatilho = null;

		function montar() {
			dialogo = document.createElement('dialog');
			dialogo.className = 'video-overlay';
			dialogo.innerHTML =
				'<div class="video-overlay__caixa">' +
				'<button type="button" class="video-overlay__fechar" data-video-overlay-fechar>' +
				'<span aria-hidden="true">&times;</span></button>' +
				'<div class="video-overlay__player"></div>' +
				'</div>';
			document.body.appendChild(dialogo);
			player = dialogo.querySelector('.video-overlay__player');
			fechar = dialogo.querySelector('[data-video-overlay-fechar]');

			dialogo.addEventListener('click', function (ev) {
				if (ev.target === dialogo || (ev.target.closest && ev.target.closest('[data-video-overlay-fechar]'))) {
					dialogo.close();
				}
			});

			dialogo.addEventListener('close', function () {
				player.innerHTML = '';
				document.documentElement.classList.remove('video-overlay-aberto');
				if (gatilho) gatilho.focus();
			});
		}

		document.addEventListener('click', function (ev) {
			var alvo = ev.target.closest ? ev.target.closest('[data-video-overlay]') : null;
			if (!alvo) return;

			if (!dialogo) montar();
			if (typeof dialogo.showModal !== 'function') return; // Navegador sem <dialog>: segue o link.

			ev.preventDefault();
			gatilho = alvo;
			fechar.setAttribute('aria-label', alvo.getAttribute('data-video-fechar') || '');

			var iframe = document.createElement('iframe');
			iframe.src = alvo.getAttribute('data-video-overlay');
			iframe.title = alvo.getAttribute('aria-label') || '';
			iframe.allow = 'autoplay; encrypted-media; picture-in-picture; fullscreen';
			iframe.setAttribute('allowfullscreen', '');
			player.appendChild(iframe);

			dialogo.showModal();
			document.documentElement.classList.add('video-overlay-aberto');
		});
	}

	/* ----- Formulário de lead: anti-robô e estado de envio ---------------- */

	/*
	 * O POST vai direto para o Salesforce, então a defesa contra robôs fica
	 * aqui: a armadilha [data-lead-armadilha] é invisível para pessoas. Se vier
	 * preenchida, o envio é barrado; se não, ela é desativada para não ir no POST.
	 */
	function initFormLead() {
		var forms = document.querySelectorAll('.form-lead');
		if (!forms.length) return;

		Array.prototype.forEach.call(forms, function (form) {
			form.addEventListener('submit', function (ev) {
				var armadilha = form.querySelector('[data-lead-armadilha]');

				if (armadilha && armadilha.value) {
					ev.preventDefault();
					return;
				}

				if (armadilha) armadilha.disabled = true;

				var botao = form.querySelector('[type="submit"]');
				if (botao) {
					botao.disabled = true;
					botao.setAttribute('aria-busy', 'true');
				}

				// Envio em nova aba (popup do WhatsApp): esta aba continua no site.
				if (form.target === '_blank') {
					setTimeout(function () { concluirNovaAba(form); }, 0);
				}
			});
		});

		/*
		 * O POST já saiu na aba nova: mostra a confirmação no lugar do
		 * formulário e o deixa limpo para um próximo envio (inclusive o bloco
		 * de origem das UTMs, marcado em data-utm).
		 */
		function concluirNovaAba(form) {
			var sucesso = form.parentNode.querySelector('[data-lead-sucesso]');

			form.reset();
			var descricao = form.querySelector('[name="description"]');
			if (descricao) delete descricao.dataset.utm;
			reativar(form);

			if (sucesso) {
				form.hidden = true;
				sucesso.hidden = false;
			}

			var dialogo = form.closest('[data-lead-dialog]');
			if (dialogo && sucesso) {
				dialogo.addEventListener('close', function () {
					form.hidden = false;
					sucesso.hidden = true;
				}, { once: true });
			}
		}

		function reativar(form) {
			var botao = form.querySelector('[type="submit"]');
			if (botao) {
				botao.disabled = false;
				botao.removeAttribute('aria-busy');
			}
			var armadilha = form.querySelector('[data-lead-armadilha]');
			if (armadilha) armadilha.disabled = false;
		}

		// Voltar pelo histórico (bfcache) devolve o botão desativado: reabilita.
		window.addEventListener('pageshow', function () {
			Array.prototype.forEach.call(forms, reativar);
		});
	}

	/* ----- Máscara do telefone no formulário de lead ---------------------- */

	/*
	 * Formata enquanto se digita, no padrão brasileiro: (00) 0000-0000 para
	 * fixo e (00) 00000-0000 a partir do nono dígito.
	 *
	 * Só entra em cena no site em português. Nas versões EN/ES o visitante
	 * costuma digitar número internacional, e uma máscara BR atrapalharia — lá
	 * o campo fica livre, com o placeholder indicando o formato.
	 */
	function initMascaraTelefone() {
		var idioma = document.documentElement.getAttribute('lang') || '';
		if (idioma.toLowerCase().indexOf('pt') !== 0) return;

		var campos = document.querySelectorAll('.form-lead input[type="tel"]');
		if (!campos.length) return;

		Array.prototype.forEach.call(campos, function (campo) {
			campo.setAttribute('inputmode', 'tel');

			campo.addEventListener('input', function () {
				var digitos = campo.value.replace(/\D/g, '').slice(0, 11);

				if (!digitos) {
					campo.value = '';
					return;
				}

				var texto = '(' + digitos.slice(0, 2);

				if (digitos.length > 2) {
					// Celular (9 dígitos) quebra depois do quinto; fixo, do quarto.
					var corte = digitos.length > 10 ? 7 : 6;
					texto += ') ' + digitos.slice(2, corte);

					if (digitos.length > corte) {
						texto += '-' + digitos.slice(corte);
					}
				}

				campo.value = texto;
			});
		});
	}

})();
