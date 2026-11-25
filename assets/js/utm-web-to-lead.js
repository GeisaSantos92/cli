/* ==========================================================================
   CLI Connect — UTMs do anúncio -> Salesforce Web-to-Lead.

   Carregado em TODAS as páginas (inc/enqueue.php): as UTMs só existem na URL
   da página de entrada, então o script precisa rodar nela, seja qual for.
   Ver docs/lead-salesforce.md.

   1) No carregamento, lê utm_* / gclid / fbclid da URL e guarda no
      localStorage (vale por `expiraDias`; um clique novo substitui o anterior).
   2) No envio de um formulário Web-to-Lead, cria os campos ocultos 00N... e
      acrescenta o bloco "Origem do lead" ao fim da Descrição.

   Configuração em window.cliconnectLead (wp_add_inline_script):
     campos     — { utm_source: '00N...', ... }; vazio = só na Descrição.
     descricao  — acrescenta o bloco de origem à Descrição.
     expiraDias — validade das UTMs guardadas.
   ========================================================================== */
(function () {
	'use strict';

	if (window.__cliUtmLoaded) return; // evita rodar 2x se for incluído duas vezes
	window.__cliUtmLoaded = true;

	var config = window.cliconnectLead || {};
	var SF_FIELDS = config.campos || {
		utm_source: '', utm_medium: '', utm_campaign: '', utm_term: '',
		utm_content: '', gclid: '', fbclid: ''
	};
	var APPEND_TO_DESCRIPTION = config.descricao !== false;
	var EXPIRE_DAYS = config.expiraDias || 90;
	var KEY = 'cli_utms';
	var FORM = 'form[action*="WebToLead"]';
	var PARAMS = Object.keys(SF_FIELDS);

	// 1) Lê as UTMs da URL e guarda no navegador (sobrevive à troca de página).
	function getUtms() {
		var found = {}, has = false;
		var q = new URLSearchParams(location.search);
		PARAMS.forEach(function (p) {
			var v = q.get(p);
			if (v) { found[p] = v.trim().slice(0, 255); has = true; }
		});
		try {
			if (has) {
				localStorage.setItem(KEY, JSON.stringify({ utms: found, ts: Date.now() }));
				return found;
			}
			var saved = JSON.parse(localStorage.getItem(KEY));
			if (saved && Date.now() - saved.ts < EXPIRE_DAYS * 864e5) return saved.utms;
		} catch (e) {}
		return found;
	}
	var utms = getUtms();

	function setHidden(form, name, value) {
		if (!name) return;
		var el = form.querySelector('[name="' + name + '"]');
		if (!el) {
			el = document.createElement('input');
			el.type = 'hidden';
			el.name = name;
			form.appendChild(el);
		}
		el.value = value || '';
	}

	// 2) No envio do formulário, junta as UTMs aos campos preenchidos.
	document.addEventListener('submit', function (ev) {
		var form = ev.target;
		if (!form.matches || !form.matches(FORM)) return;
		if (ev.defaultPrevented) return; // envio barrado (ex.: armadilha anti-robô)

		PARAMS.forEach(function (p) { setHidden(form, SF_FIELDS[p], utms[p]); });

		if (APPEND_TO_DESCRIPTION) {
			var desc = form.querySelector('[name="description"]');
			if (desc && !desc.dataset.utm) {
				var lines = PARAMS.filter(function (p) { return utms[p]; })
					.map(function (p) { return p + ': ' + utms[p]; });
				var contexto = [];
				if (form.dataset.leadOrigem) contexto.push('formulario: ' + form.dataset.leadOrigem);
				contexto.push('pagina: ' + location.pathname);
				desc.value += (desc.value ? '\n\n' : '') + '--- Origem do lead ---\n' +
					(lines.length ? lines.join('\n') : 'Sem UTM (orgânico/direto)') +
					'\n' + contexto.join('\n');
				desc.dataset.utm = '1';
			}
		}
	}, true);
})();
