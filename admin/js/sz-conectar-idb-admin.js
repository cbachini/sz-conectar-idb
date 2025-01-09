(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

})( jQuery );

document.addEventListener('DOMContentLoaded', function () {
    const codigoField = document.querySelector('#form-field-codigo');
    const codigoMessage = document.createElement('div'); // Mensagem de erro ou sucesso
    codigoMessage.style.marginTop = '5px';
    codigoMessage.style.fontSize = '14px';
    codigoField.parentNode.appendChild(codigoMessage);

    // Evento de blur
    codigoField.addEventListener('blur', function () {
        const codigo = codigoField.value.trim();
        const formType = document.querySelector('input[name="form_id"]').value === '1b426a8' ? 'degustacao' : 'professor';

        // Reseta mensagens e estilos
        codigoMessage.textContent = '';
        codigoMessage.style.color = '';
        codigoField.style.borderColor = '';

        if (!codigo) {
            codigoMessage.textContent = 'O código de acesso é obrigatório.';
            codigoMessage.style.color = 'red';
            codigoField.style.borderColor = 'red';
            return;
        }

        // Mostra indicador de carregamento
        codigoMessage.textContent = 'Validando código...';
        codigoMessage.style.color = 'blue';

        // Envia a requisição AJAX
        fetch(ajaxurl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'validate_access_code',
                codigo: codigo,
                form_type: formType,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    codigoMessage.textContent = data.data.message;
                    codigoMessage.style.color = 'green';
                    codigoField.style.borderColor = 'green';
                    codigoField.setAttribute('data-valid', 'true');
                } else {
                    codigoMessage.textContent = data.data.message;
                    codigoMessage.style.color = 'red';
                    codigoField.style.borderColor = 'red';
                    codigoField.setAttribute('data-valid', 'false');
                }
            })
            .catch((error) => {
                codigoMessage.textContent = 'Erro ao validar o código. Tente novamente.';
                codigoMessage.style.color = 'red';
                codigoField.style.borderColor = 'red';
                console.error('Erro na validação AJAX:', error);
            });
    });

    // Bloqueia envio se o código não for válido
    const form = codigoField.closest('form');
    form.addEventListener('submit', function (event) {
        if (codigoField.getAttribute('data-valid') !== 'true') {
            event.preventDefault();
            codigoMessage.textContent = 'Por favor, corrija o código antes de enviar.';
            codigoMessage.style.color = 'red';
        }
    });
});
