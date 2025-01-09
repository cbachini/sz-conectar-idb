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

        // Evento de blur no campo
        codigoField.on('blur', function () {
            const codigo = codigoField.val().trim();
            const formType = 'professor'; // Ajuste conforme necessário

            // Limpa mensagens anteriores
            codigoMessage.text('');

            // Se o código estiver vazio, exibe uma mensagem
            if (!codigo) {
                codigoMessage.text('O código é obrigatório.').css('color', 'red');
                return;
            }

            // Envia a requisição AJAX
            $.ajax({
                method: 'POST',
                dataType: 'json',
                url: myAjax.ajaxurl, // URL do admin-ajax.php
                data: {
                    action: 'validate_access_code', // Ação no backend
                    codigo: codigo, // Código de acesso digitado
                    form_type: formType, // Tipo de formulário
                    _wpnonce: myAjax.nonce, // Nonce gerado pelo backend
                },
            })
                .done(function (response) {
                    if (response.success) {
                        codigoMessage.text(response.data.message).css('color', 'green');
                        codigoField.css({ borderColor: 'green' });
                    } else {
                        codigoMessage.text(response.data.message).css('color', 'red');
                        codigoField.css({ borderColor: 'red' });
                    }
                })
                .fail(function () {
                    codigoMessage.text('Erro ao validar o código.').css('color', 'red');
                });
        });
    });
})(jQuery);
