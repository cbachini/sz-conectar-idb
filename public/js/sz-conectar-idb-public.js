(function ($) {
    'use strict';

    $(document).ready(function () {
        const codigoField = $('#form-field-codigo');
        if (!codigoField.length) return;

        const codigoMessage = $('<div></div>').css({
            marginTop: '5px',
            fontSize: '14px',
        });
        codigoField.parent().append(codigoMessage);

        codigoField.on('blur', function () {
            const codigo = codigoField.val().trim();
            const formId = $('input[name="form_id"]').val();
            const formType = formId === '1b426a8' ? 'degustacao' : 'professor';

            console.log('Iniciando validação do código...');
            console.log('Código digitado:', codigo);
            console.log('Tipo de formulário:', formType);

            codigoMessage.text('Validando código...').css('color', 'blue');

            $.ajax({
                url: myAjax.ajaxurl,
                method: 'POST',
                dataType: 'json',
                data: {
                    action: 'validate_access_code',
                    codigo: codigo,
                    form_type: formType,
                    nonce: myAjax.nonce,
                },
                success: function (response) {
                    console.log('Resposta AJAX recebida:', response);
                    if (response.success) {
                        codigoMessage.text(response.data.message).css('color', 'green');
                        codigoField.css({ borderColor: 'green' });
                    } else {
                        codigoMessage.text(response.data.message).css('color', 'red');
                        codigoField.css({ borderColor: 'red' });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Erro na requisição AJAX:', error);
                    console.error('Detalhes:', xhr.responseText);
                    codigoMessage.text('Erro ao validar o código.').css('color', 'red');
                },
            });
        });
    });
})(jQuery);
