<?php

/**
 * Classe para gerenciamento de shortcodes.
 */
class Sz_Conectar_Idb_Shortcodes {

    public static function init() {
        add_shortcode('gerar_charada', [self::class, 'gerar_charada_shortcode']);
    }

    public static function gerar_charada_shortcode() {
        ob_start();
        ?>
        <form id="charada-form">
            <div id="charada-container">
                <p><?php _e('Carregando charada...', 'sz-conectar-idb'); ?></p>
            </div>
            <div id="loading-message" style="display: none;"><?php _e('Verificando a resposta...', 'sz-conectar-idb'); ?></div>
            <div id="result-message"></div>
        </form>

        <script>
        jQuery(document).ready(function ($) {
            const grupoId = $('#numero-livro').text().trim();

            if (grupoId) {
                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: { action: 'gerar_charada_ajax', grupo_id: grupoId },
                    success: function (response) {
                        if (response.success) {
                            $('#charada-container').html(`
                                <input type="hidden" name="id" value="${response.data.id}">
                                <p><strong><?php _e('Charada:', 'sz-conectar-idb'); ?></strong> ${response.data.pergunta}</p>
                                <input type="text" name="resposta" placeholder="<?php _e('Resposta', 'sz-conectar-idb'); ?>" required>
                                <button type="submit"><?php _e('Enviar', 'sz-conectar-idb'); ?></button>
                            `);
                        } else {
                            $('#charada-container').html('<p><?php _e('Nenhuma charada disponível.', 'sz-conectar-idb'); ?></p>');
                        }
                    }
                });
            }

            $('#charada-form').submit(function (e) {
                e.preventDefault();
                $('#loading-message').show();
                $('#result-message').html('');

                const formData = $(this).serialize();

                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        $('#loading-message').hide();
                        if (response.success) {
                            $('#result-message').html('<p style="color: green;"><?php _e('Resposta correta!', 'sz-conectar-idb'); ?></p>');
                            $('#audiobook, #ebook').show();
                            $('#aviso-bloqueio').hide();
                        } else {
                            $('#result-message').html('<p style="color: red;"><?php _e('Resposta incorreta.', 'sz-conectar-idb'); ?></p>');
                        }
                    }
                });
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }
}
