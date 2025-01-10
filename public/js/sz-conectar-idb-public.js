(function ($) {
    'use strict';

    $(document).ready(function () {
        // Seleciona o campo de código e o formulário
        const codigoField = $('#form-field-codigo');
        const form = codigoField.closest('form.elementor-form'); // Seleciona o formulário do Elementor
        const submitButton = form.find('button[type="submit"]'); // Seleciona o botão de submissão

        if (!codigoField.length || !form.length || !submitButton.length) return;

        const codigoMessage = $('<div></div>').css({
            marginTop: '5px',
            fontSize: '14px',
        });
        codigoField.parent().append(codigoMessage);

        // Adiciona indicador de carregamento
        const loadingIndicator = $('<div>Validando...</div>').css({
            marginTop: '5px',
            fontSize: '14px',
            color: 'blue',
            display: 'none',
        });
        codigoField.parent().append(loadingIndicator);

        let isCodeValid = false; // Flag para rastrear se o código é válido

        // Desabilita o botão de submissão inicialmente
        submitButton.prop('disabled', true);

        // Evento de blur no campo
        codigoField.on('blur', function () {
            const codigo = codigoField.val().trim();
            const formType = 'professor'; // Ajuste conforme necessário

            // Limpa mensagens anteriores
            codigoMessage.text('');
            codigoField.css({ borderColor: '' });
            isCodeValid = false; // Reseta a flag de validação
            submitButton.prop('disabled', true); // Desabilita o botão enquanto valida

            // Se o código estiver vazio, exibe uma mensagem
            if (!codigo) {
                codigoMessage.text('O código é obrigatório.').css('color', 'red');
                return;
            }

            // Mostra indicador de carregamento
            loadingIndicator.show();

            // Envia a requisição AJAX
            $.ajax({
                method: 'POST',
                dataType: 'json',
                url: szConectarAjax.ajaxurl, // URL do admin-ajax.php
                data: {
                    action: 'validate_access_code', // Ação no backend
                    codigo: codigo, // Código de acesso digitado
                    form_type: formType, // Tipo de formulário
                    _wpnonce: szConectarAjax.nonce, // Nonce gerado pelo backend
                },
            })
                .done(function (response) {
                    // Oculta indicador de carregamento
                    loadingIndicator.hide();

                    if (response.success) {
                        codigoMessage.text(response.data.message).css('color', 'green');
                        codigoField.css({ borderColor: 'green' });
                        isCodeValid = true; // Marca como válido
                        submitButton.prop('disabled', false); // Habilita o botão
                    } else {
                        codigoMessage.text(response.data.message).css('color', 'red');
                        codigoField.css({ borderColor: 'red' });
                        submitButton.prop('disabled', true); // Mantém o botão desabilitado
                    }
                })
                .fail(function () {
                    loadingIndicator.hide();
                    codigoMessage.text('Erro ao validar o código.').css('color', 'red');
                    submitButton.prop('disabled', true); // Desabilita o botão em caso de erro
                });
        });

        // Evento de submissão do formulário
        form.on('submit', function (e) {
            if (!isCodeValid) {
                e.preventDefault(); // Impede a submissão do formulário
                codigoMessage.text('Por favor, valide o código antes de enviar.').css('color', 'red');
            }
        });
    });
})(jQuery);
