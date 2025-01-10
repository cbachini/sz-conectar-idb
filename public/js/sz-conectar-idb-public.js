(function ($) {
    'use strict';

    $(document).ready(function () {
        // Seleciona o campo de código
        const codigoField = $('#form-field-codigo');
        if (!codigoField.length) return;

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

        // Evento de blur no campo
        codigoField.on('blur', function () {
            const codigo = codigoField.val().trim();
            const formType = 'professor'; // Ajuste conforme necessário

            // Limpa mensagens anteriores
            codigoMessage.text('');
            codigoField.css({ borderColor: '' });

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
                    } else {
                        codigoMessage.text(response.data.message).css('color', 'red');
                        codigoField.css({ borderColor: 'red' });
                    }
                })
                .fail(function (jqXHR, textStatus) {
                    // Oculta indicador de carregamento
                    loadingIndicator.hide();

                    let errorMessage = 'Erro ao validar o código.';
                    if (textStatus === 'timeout') {
                        errorMessage = 'A validação demorou muito. Tente novamente.';
                    } else if (textStatus === 'error') {
                        errorMessage = 'Erro na conexão com o servidor. Verifique sua conexão.';
                    } else if (textStatus === 'abort') {
                        errorMessage = 'A requisição foi cancelada. Tente novamente.';
                    }

                    codigoMessage.text(errorMessage).css('color', 'red');
                });
        });
    });
})(jQuery);
