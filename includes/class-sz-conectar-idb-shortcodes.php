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
            <input type="hidden" name="action" value="validar_charada_resposta">
            <input type="hidden" name="id" id="charada-id">
            <p id="charada-pergunta"></p>
            <input type="text" name="resposta" placeholder="<?php _e('Resposta', 'sz-conectar-idb'); ?>" required>
            <button type="submit"><?php _e('Enviar', 'sz-conectar-idb'); ?></button>
        </form>
        <div id="loading-message" style="display:none;"><?php _e('Verificando a resposta...', 'sz-conectar-idb'); ?></div>
        <div id="result-message"></div>

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
                            $('#charada-id').val(response.data.id);
                            $('#charada-pergunta').text(response.data.pergunta);
                        } else {
                            $('#result-message').html('<p style="color:red;">' + response.data + '</p>');
                        }
                    },
                    error: function () {
                        $('#result-message').html('<p style="color:red;"><?php _e("Erro ao carregar a charada. Tente novamente mais tarde.", "sz-conectar-idb"); ?></p>');
                    }
                });
            }

            $('#charada-form').submit(function (e) {
                e.preventDefault();
                $('#loading-message').show();
                $('#result-message').empty();

                const formData = $(this).serialize();

                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        $('#loading-message').hide();

                        if (response.success) {
                            $('#result-message').html('<p style="color:green;"><?php _e("Resposta correta! Acessando o conteúdo...", "sz-conectar-idb"); ?></p>');
                            $('#audiobook, #ebook').show();
                            $('#aviso-bloqueio').hide();
                        } else {
                            $('#result-message').html('<p style="color:red;">' + response.data + '</p>');
                        }
                    },
                    error: function () {
                        $('#loading-message').hide();
                        $('#result-message').html('<p style="color:red;"><?php _e("Erro ao verificar a resposta. Tente novamente.", "sz-conectar-idb"); ?></p>');
                    }
                });
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }
}
