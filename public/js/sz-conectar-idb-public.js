(function ($) {
    'use strict';

    $(document).ready(function () {
        // Seleciona o campo de código de acesso
        const codigoField = $('#form-field-codigo');
        if (!codigoField.length) return; // Sai se o campo não existir

        // Cria o elemento para exibir mensagens (erro ou sucesso)
        const codigoMessage = $('<div></div>').css({
            marginTop: '5px',
            fontSize: '14px',
        });
        codigoField.parent().append(codigoMessage); // Adiciona a mensagem abaixo do campo

        // Evento disparado quando o usuário sai do campo (blur)
        codigoField.on('blur', function () {
            const codigo = codigoField.val().trim(); // Obtém o valor digitado no campo
            const formType = $('input[name="form_id"]').val() === '1b426a8' ? 'degustacao' : 'professor'; // Identifica o tipo de formulário

            // Reseta mensagens e estilos para um novo estado
            codigoMessage.text('');
            codigoMessage.css({ color: '' });
            codigoField.css({ borderColor: '' });

            // Se o campo está vazio, exibe uma mensagem de erro
            if (!codigo) {
                codigoMessage.text('O código de acesso é obrigatório.');
                codigoMessage.css({ color: 'red' });
                codigoField.css({ borderColor: 'red' });
                return;
            }

            // Mostra um indicador de carregamento enquanto valida o código
            codigoMessage.text('Validando código...');
            codigoMessage.css({ color: 'blue' });

            // Envia a requisição AJAX para validar o código
            $.ajax({
                url: ajaxurl, // URL do WordPress para AJAX (admin-ajax.php)
                type: 'POST', // Método HTTP
                dataType: 'json', // Tipo de resposta esperada
                data: {
                    action: 'validate_access_code', // Ação registrada no backend
                    codigo: codigo, // Código digitado pelo usuário
                    form_type: formType, // Tipo de formulário (degustacao ou professor)
                },
                success: function (response) {
                    // Se o código for válido
                    if (response.success) {
                        codigoMessage.text(response.data.message); // Mensagem de sucesso
                        codigoMessage.css({ color: 'green' });
                        codigoField.css({ borderColor: 'green' });
                        codigoField.attr('data-valid', 'true'); // Marca o campo como válido
                    } else {
                        // Se o código for inválido
                        codigoMessage.text(response.data.message); // Mensagem de erro do backend
                        codigoMessage.css({ color: 'red' });
                        codigoField.css({ borderColor: 'red' });
                        codigoField.attr('data-valid', 'false'); // Marca o campo como inválido
                    }
                },
                error: function (xhr, status, error) {
                    // Em caso de erro na requisição AJAX
                    codigoMessage.text('Erro ao validar o código. Tente novamente.');
                    codigoMessage.css({ color: 'red' });
                    codigoField.css({ borderColor: 'red' });
                    console.error('Erro na validação AJAX:', error); // Log no console para depuração
                },
            });
        });

        // Evento de submissão do formulário
        const form = codigoField.closest('form'); // Seleciona o formulário mais próximo do campo
        form.on('submit', function (event) {
            // Impede o envio se o código não for válido
            if (codigoField.attr('data-valid') !== 'true') {
                event.preventDefault(); // Cancela a submissão do formulário
                codigoMessage.text('Por favor, corrija o código antes de enviar.');
                codigoMessage.css({ color: 'red' });
            }
        });
    });
})(jQuery);
