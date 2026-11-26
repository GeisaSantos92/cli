/* CLI Connect: UTMs do anúncio -> campos de UTM do Lead no Salesforce (Web-to-Lead)
   Versão 3 (26/11/2026). Ver docs/lead-salesforce.md.

   - Registra as UTMs assim que o visitante chega ao site, em QUALQUER página.
   - Guarda por 90 dias no navegador, então a navegação não apaga a origem.
   - Preenche os campos 00N... de QUALQUER formulário Web-to-Lead da página:
     no carregamento, quando um formulário aparece depois (popup) e no envio.
   - Não depende do campo de mensagem (description).

   Instalação: carregar em todas as páginas (inc/enqueue.php já faz isso).
   Pode ser carregado como arquivo .js (enqueue) ou colado entre <script></script>. */
(function () {
  'use strict';
  if (window.__cliUtmLoaded) return; // evita rodar 2x
  window.__cliUtmLoaded = true;

  /* IDs dos campos de UTM do Lead (Web-to-Lead gerado em 25/11/2026, org 00Das000004fL2z) */
  var SF_FIELDS = {
    utm_source:   '00NbJ00000db2Kc', // UTM Source
    utm_medium:   '00NbJ00000db2Kb', // UTM Medium
    utm_campaign: '00NbJ00000db2KZ', // UTM Campaign
    utm_term:     '00NbJ00000db2Kd', // UTM Term
    utm_content:  '00NbJ00000db2Ka', // UTM Content
    gclid:        '00NbJ00000db2KY', // GCLID
    fbclid:       '00NbJ00000db2KX'  // FBCLID
  };
  var EXPIRE_DAYS = 90;
  var KEY = 'cli_utms';
  var FORM = 'form[action*="WebToLead"]';

  /* Configuração externa opcional (window.cliconnectLead, ex.: wp_add_inline_script).
     Só valores PREENCHIDOS substituem os de cima: um ID vazio nunca apaga o ID correto. */
  var ext = window.cliconnectLead || {};
  if (ext.campos) {
    Object.keys(SF_FIELDS).forEach(function (k) {
      if (ext.campos[k]) SF_FIELDS[k] = String(ext.campos[k]).trim();
    });
  }
  if (ext.expiraDias > 0) EXPIRE_DAYS = Number(ext.expiraDias);

  var PARAMS = Object.keys(SF_FIELDS);

  /* 1) Captura: lê a URL ao chegar e guarda no navegador */
  function readUrl() {
    var found = {}, has = false, q;
    try { q = new URLSearchParams(window.location.search); } catch (e) { return null; }
    PARAMS.forEach(function (p) {
      var v = q.get(p);
      if (v) { found[p] = v.trim().slice(0, 255); has = true; }
    });
    return has ? found : null;
  }

  function loadSaved() {
    try {
      var saved = JSON.parse(window.localStorage.getItem(KEY));
      if (saved && saved.utms && Date.now() - saved.ts < EXPIRE_DAYS * 864e5) return saved.utms;
    } catch (e) {}
    return null;
  }

  var utms = readUrl();
  if (utms) {
    // Chegou por link com UTM: vale o último clique em anúncio.
    try { window.localStorage.setItem(KEY, JSON.stringify({ utms: utms, ts: Date.now() })); } catch (e) {}
  } else {
    utms = loadSaved() || {};
  }

  /* 2) Preenche os campos de UTM do formulário */
  function setField(form, name, value) {
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

  function fillForm(form) {
    if (!form || !form.matches || !form.matches(FORM)) return;
    PARAMS.forEach(function (p) { setField(form, SF_FIELDS[p], utms[p]); });
  }

  function fillAll() {
    var forms = document.querySelectorAll(FORM);
    for (var i = 0; i < forms.length; i++) fillForm(forms[i]);
  }

  // a) ao carregar a página
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fillAll);
  else fillAll();

  // b) quando um formulário entra na página depois (popup, modal, carregamento tardio)
  if (window.MutationObserver) {
    var pending = false;
    new MutationObserver(function () {
      if (pending) return;
      pending = true;
      setTimeout(function () { pending = false; fillAll(); }, 50);
    }).observe(document.documentElement, { childList: true, subtree: true });
  }

  // c) no envio, em qualquer formulário Web-to-Lead (fase de captura: roda antes de outros scripts)
  document.addEventListener('submit', function (ev) { fillForm(ev.target); }, true);

  // Para conferir no console: CLIUtm.utms
  window.CLIUtm = { utms: utms, fields: SF_FIELDS, fill: fillAll };
})();
