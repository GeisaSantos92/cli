/* ==========================================================================
   CLI Connect — comportamentos da página Contato (page-contato.php).

   Enfileirado só em cliconnect_e_pagina( 'contato' ) (inc/enqueue.php).
   ========================================================================== */
(function () {
	'use strict';

	function ready(fn) {
		if (document.readyState !== 'loading') fn();
		else document.addEventListener('DOMContentLoaded', fn);
	}

	ready(function () {
		initMascaraTelefone();
	});

	/* ----- Máscara do telefone no formulário ------------------------------ */

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

		var campos = document.querySelectorAll('.ct-form-wrapper input[type="tel"]');
		if (!campos.length) return;

		Array.prototype.forEach.call(campos, function (campo) {
			campo.setAttribute('inputmode', 'tel');
			campo.setAttribute('maxlength', '16');

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
