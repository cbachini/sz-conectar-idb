(function ($) {
    'use strict';

    $(document).ready(function () {
        console.log('Iniciando script público...');

        // Verifica se o campo de código, formulário e botão de submissão estão presentes
        const codigoField = $('#form-field-codigo');
        const form = codigoField.closest('form.elementor-form');
        const submitButton = form.find('button[type="submit"]');

        if (codigoField.length && form.length && submitButton.length) {
            console.log('Formulário e campo de código encontrados.');

            const formIdField = form.find('input[name="form_id"]');
            const formIdValue = formIdField.val();

            const formType = formIdValue === '605fd56'
                ? 'professor'
                : formIdValue === '1b426a8'
                ? 'degustacao'
                : null;

            if (!formType) {
                console.error('Tipo de formulário não identificado.');
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

            submitButton.prop('disabled', true);
            console.log('Botão de submissão desabilitado inicialmente.');

            codigoField.on('blur', function () {
                const codigo = codigoField.val().trim();

                console.log('Campo de código perdeu o foco. Valor do código:', codigo);

                codigoMessage.text('');
                codigoField.css({ borderColor: '' });
                isCodeValid = false;
                submitButton.prop('disabled', true);

                if (!codigo) {
                    console.warn('Campo de código está vazio.');
                    codigoMessage.text('O código é obrigatório.').css('color', 'red');
                    return;
                }

                loadingIndicator.show();
                console.log('Enviando requisição para validação do código.');

                // Envia requisição AJAX para validação
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
                })
                    .done(function (response) {
                        loadingIndicator.hide();
                        console.log('Resposta da validação recebida:', response);

                        if (response.success) {
                            console.info('Código validado com sucesso.');
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
        } else {
            console.warn('Elemento(s) necessário(s) não encontrado(s): campo de código, formulário ou botão de submissão.');
        }

        // Nova funcionalidade: Interação com charadas
        const charadaForm = $('#charada-form');
        const charadaContainer = $('#charada-container');
        const loadingMessage = $('#loading-message');
        const resultMessage = $('#result-message');

        function carregarCharada(grupoId) {
            console.log('Carregando charada para o grupo:', grupoId);
            $.ajax({
                url: szConectarAjax.ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'gerar_charada_ajax',
                    grupo_id: grupoId,
                },
                beforeSend: function () {
                    charadaContainer.html('<p>Carregando charada...</p>');
                },
                success: function (response) {
                    if (response.success) {
                        console.log('Charada carregada com sucesso:', response.data);
                        const charadaHtml = `
                            <input type="hidden" name="charada_id" value="${response.data.id}">
                            <p><strong>Charada:</strong> ${response.data.pergunta}</p>
                            <input type="text" name="resposta" placeholder="Resposta" required>
                            <button type="submit">Enviar</button>
                        `;
                        charadaContainer.html(charadaHtml);
                    } else {
                        console.warn('Nenhuma charada disponível:', response.data);
                        charadaContainer.html(`<p>${response.data}</p>`);
                    }
                },
                error: function () {
                    console.error('Erro ao carregar a charada.');
                    charadaContainer.html('<p>Erro ao carregar a charada. Tente novamente mais tarde.</p>');
                },
            });
        }

        charadaForm.on('submit', function (e) {
            e.preventDefault();

            const resposta = charadaForm.find('input[name="resposta"]').val();
            const charadaId = charadaForm.find('input[name="charada_id"]').val();

            console.log('Enviando resposta para validação:', { resposta, charadaId });

            if (!resposta || !charadaId) {
                resultMessage.html('<p style="color: red;">Por favor, preencha a resposta.</p>');
                return;
            }

            loadingMessage.show();
            resultMessage.empty();

            $.ajax({
                url: szConectarAjax.ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'validar_charada_resposta',
                    id: charadaId,
                    resposta: resposta,
                },
                success: function (response) {
                    loadingMessage.hide();
                    console.log('Resposta da charada recebida:', response);

                    if (response.success) {
                        resultMessage.html('<p style="color: green;">Resposta correta!</p>');
                        console.log('Resposta correta! Liberando elementos bloqueados.');
                        $('#audiobook, #ebook').show(); // Desbloqueia os elementos
                        $('#aviso-bloqueio').hide(); // Oculta o aviso de bloqueio
                    } else {
                        resultMessage.html('<p style="color: red;">Resposta incorreta. Tente novamente.</p>');
                        console.warn('Resposta incorreta.');
                    }
                },
                error: function () {
                    loadingMessage.hide();
                    resultMessage.html('<p style="color: red;">Erro ao verificar a resposta. Tente novamente mais tarde.</p>');
                    console.error('Erro na validação da resposta.');
                },
            });
        });

        carregarCharada(1); // Exemplo: grupo 1
    });
})(jQuery);
