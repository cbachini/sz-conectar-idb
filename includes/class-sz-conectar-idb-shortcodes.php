<?php

/**
 * Registro de shortcodes para o plugin.
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
                        }
                    }
                });
            }

            $('#charada-form').submit(function (e) {
                e.preventDefault();

                const formData = $(this).serialize();

                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            alert('<?php _e('Resposta correta!', 'sz-conectar-idb'); ?>');
                        } else {
                            alert('<?php _e('Resposta incorreta.', 'sz-conectar-idb'); ?>');
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

Sz_Conectar_Idb_Shortcodes::init();
