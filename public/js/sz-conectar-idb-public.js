(function ($) {
    'use strict';

    $(window).on('elementor/frontend/init', function () {
        const formSelector = '.elementor-form'; // Selector padrão do formulário

        $(document).on('blur', '#form-field-codigo', function () {
            const $field = $(this);
            const codigo = $field.val().trim();
            const formId = $field.closest(formSelector).find('input[name="form_id"]').val();
            const formType = formId === '1b426a8' ? 'degustacao' : 'professor';

            const $message = $('<div class="validation-message"></div>');
            $field.siblings('.validation-message').remove();
            $field.after($message);

            if (!codigo) {
                $message.text('O código é obrigatório.').css('color', 'red');
                return;
            }

            $message.text('Validando código...').css('color', 'blue');

            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'validate_access_code',
                    codigo: codigo,
                    form_type: formType,
                    nonce: myAjax.nonce,
                },
                success: function (response) {
                    if (response.success) {
                        $message.text(response.data.message).css('color', 'green');
                        $field.data('valid', true);
                    } else {
                        $message.text(response.data.message).css('color', 'red');
                        $field.data('valid', false);
                    }
                },
                error: function () {
                    $message.text('Erro ao validar o código.').css('color', 'red');
                },
            });
        });

        $(document).on('submit', formSelector, function (e) {
            const $field = $(this).find('#form-field-codigo');
            if ($field.data('valid') !== true) {
                e.preventDefault();
                alert('Por favor, corrija o código antes de enviar.');
            }
        });
    });
})(jQuery);
