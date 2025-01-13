(function ($) {
    'use strict';

    $(document).ready(function () {
        // Seleciona o campo de código e o formulário
        const codigoField = $('#form-field-codigo');
        const form = codigoField.closest('form.elementor-form');
        const submitButton = form.find('button[type="submit"]');

        if (!codigoField.length || !form.length || !submitButton.length) {
            console.warn('Elemento(s) necessário(s) não encontrado(s): campo de código, formulário ou botão de submissão.');
            return;
        }

        console.log('Campo de código e formulário encontrados. Iniciando validação.');

        const formIdField = form.find('input[name="form_id"]');
        const formIdValue = formIdField.val();

        // Determina o tipo de formulário com base no valor de form_id
        const formType = formIdValue === '605fd56' 
            ? 'professor' 
            : formIdValue === '1b426a8' 
            ? 'degustacao' 
            : null;

        if (!formType) {
            console.error('Tipo de formulário não identificado. Verifique os valores de form_id.');
            return;
        }

        console.log('Tipo de formulário identificado:', formType);

        const codigoMessage = $('<div></div>').css({
            marginTop: '5px',
            fontSize: '14px',
        });
        codigoField.parent().append(codigoMessage);

        const loadingIndicator = $('<div>Validando...</div>').css({
            marginTop: '5px',
            fontSize: '14px',
            color: 'blue',
            display: 'none',
        });
        codigoField.parent().append(loadingIndicator);

        let isCodeValid = false;

        // Desabilita o botão de submissão inicialmente
        submitButton.prop('disabled', true);
        console.log('Botão de submissão desabilitado inicialmente.');

        codigoField.on('blur', function () {
            const codigo = codigoField.val().trim();

            console.log('Campo de código perdeu o foco.');
            console.log('Valor do código:', codigo);
            console.log('Tipo de formulário:', formType);

            codigoMessage.text('');
            codigoField.css({ borderColor: '' });
            isCodeValid = false;
            submitButton.prop('disabled', true);
            console.log('Mensagens limpas e botão desabilitado.');

            if (!codigo) {
                console.warn('Campo de código está vazio.');
                codigoMessage.text('O código é obrigatório.').css('color', 'red');
                return;
            }

            loadingIndicator.show();
            console.log('Indicador de carregamento exibido.');

            $.ajax({
                method: 'POST',
                dataType: 'json',
                url: szConectarAjax.ajaxurl,
                data: {
                    action: 'validate_access_code',
                    codigo: codigo,
                    form_type: formType,
                    _wpnonce: szConectarAjax.nonce,
                },
                beforeSend: function () {
                    console.log('Requisição AJAX enviada:', {
                        action: 'validate_access_code',
                        codigo: codigo,
                        form_type: formType,
                        _wpnonce: szConectarAjax.nonce,
                    });
                },
            })
                .done(function (response) {
                    loadingIndicator.hide();
                    console.log('Resposta recebida:', response);

                    if (response.success) {
                        console.info('Código validado com sucesso:', response.data.message);
                        console.log('Usuários que já usaram este código:', response.data.total_users);
                        codigoMessage.text(response.data.message).css('color', 'green');
                        codigoField.css({ borderColor: 'green' });
                        isCodeValid = true;
                        submitButton.prop('disabled', false);
                        console.log('Botão de submissão habilitado.');
                    } else {
                        console.warn('Erro na validação do código:', response.data.message);
                        codigoMessage.text(response.data.message).css('color', 'red');
                        codigoField.css({ borderColor: 'red' });
                        submitButton.prop('disabled', true);
                    }
                })
                .fail(function (jqXHR, textStatus) {
                    loadingIndicator.hide();
                    console.error('Erro na requisição AJAX:', textStatus);
                    codigoMessage.text('Erro ao validar o código.').css('color', 'red');
                    submitButton.prop('disabled', true);
                });
        });

        form.on('submit', function (e) {
            console.log('Evento de submissão acionado.');

            if (!isCodeValid) {
                e.preventDefault();
                console.warn('Tentativa de envio com código inválido.');
                codigoMessage.text('Por favor, valide o código antes de enviar.').css('color', 'red');
            } else {
                console.log('Formulário enviado com sucesso.');
            }
        });
    });
})(jQuery);
