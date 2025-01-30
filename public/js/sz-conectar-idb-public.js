(function ($) {
    'use strict';

    $(document).ready(function () {
        console.log('Iniciando validações públicas...');

        const form = $('.elementor-form');
        const codigoField = form.find('#form-field-codigo');
        const validUntilField = form.find('#form-field-valid-until');
        const submitButton = form.find('button[type="submit"]');
        let isCodeValid = false, isDateValid = true; // Inicialmente, consideramos a data válida

        // Inicialmente, desabilita o botão de envio
        submitButton.prop('disabled', true);

        // Função para verificar o estado geral das validações
        function toggleSubmitButton() {
            if (isCodeValid && isDateValid) {
                submitButton.prop('disabled', false);
            } else {
                submitButton.prop('disabled', true);
            }
        }

        // Validação do código no campo de código de acesso
        codigoField.on('blur', function () {
            const codigo = codigoField.val().trim();

            if (!codigo) {
                codigoField.css('border-color', 'red');
                alert('O campo de código de acesso é obrigatório.');
                isCodeValid = false;
                toggleSubmitButton();
                return;
            }

            // Envia requisição AJAX para verificar se o código existe
            $.ajax({
                method: 'POST',
                dataType: 'json',
                url: szConectarAjax.ajaxurl,
                data: {
                    action: 'check_teacher_code_exists',
                    access_code: codigo,
                    security: szConectarAjax.nonce,
                }
            })
                .done(function (response) {
                    if (response.success) {
                        codigoField.css('border-color', 'green');
                        isCodeValid = true;
                    } else {
                        codigoField.css('border-color', 'red');
                        alert('O código já existe. Por favor, insira um código diferente.');
                        isCodeValid = false;
                    }
                    toggleSubmitButton();
                })
                .fail(function () {
                    alert('Erro ao validar o código. Tente novamente.');
                    codigoField.css('border-color', 'red');
                    isCodeValid = false;
                    toggleSubmitButton();
                });
        });

        // Validação da data de validade
        validUntilField.on('blur', function () {
            const validUntil = validUntilField.val();
            const today = new Date().toISOString().split('T')[0]; // Data de hoje no formato YYYY-MM-DD

            if (validUntil && validUntil < today) {
                alert('A data de validade não pode estar no passado.');
                validUntilField.css('border-color', 'red');
                isDateValid = false;
            } else {
                validUntilField.css('border-color', 'green');
                isDateValid = true;
            }
            toggleSubmitButton();
        });

        // Validação final no momento do envio
        form.on('submit', function (e) {
            if (!isCodeValid || !isDateValid) {
                e.preventDefault();
                alert('Por favor, corrija os erros antes de enviar.');
            }
        });
    });
})(jQuery);
