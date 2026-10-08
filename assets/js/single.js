/* ==========================================================================
   CLI Connect — post interno do blog.

   Enfileirado só em is_singular('post') (inc/enqueue.php).
   Botão "copiar link" da faixa de compartilhamento: fica com `hidden` no HTML
   e só aparece quando o navegador oferece a Clipboard API.
   ========================================================================== */
(function () {
	'use strict';

	function ready(fn) {
		if (document.readyState !== 'loading') fn();
		else document.addEventListener('DOMContentLoaded', fn);
	}

	ready(function () {
		var botao = document.querySelector('[data-copiar-link]');
		if (!botao || !navigator.clipboard) return;

		var aviso = document.querySelector('.post-compartilhar__aviso');
		var timer;

		botao.hidden = false;

		botao.addEventListener('click', function () {
			navigator.clipboard.writeText(botao.getAttribute('data-copiar-link')).then(function () {
				botao.classList.add('is-copiado');
				if (aviso) aviso.textContent = botao.getAttribute('data-copiado');

				clearTimeout(timer);
				timer = setTimeout(function () {
					botao.classList.remove('is-copiado');
					if (aviso) aviso.textContent = '';
				}, 2500);
			});
		});
	});
})();
