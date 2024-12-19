<?php

class Sz_Conectar_Idb_Shortcodes {

    public static function init() {
        add_shortcode('gerar_charada', [self::class, 'gerar_charada_shortcode']);
    }

    public static function gerar_charada_ajax_callback() {
        check_ajax_referer('gerar_charada_nonce', '_wpnonce');

        if (!isset($_POST['grupo_id'])) {
            wp_send_json_error(['message' => 'O parâmetro grupo_id está ausente.'], 400);
        }

        $grupo_id = sanitize_text_field($_POST['grupo_id']);

        $resposta = [
            'success' => true,
            'data' => [
                'id' => 1,
                'pergunta' => 'Qual é o animal que voa à noite?'
            ]
        ];

        wp_send_json($resposta);
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
                    data: { 
                        action: 'gerar_charada_ajax', 
                        grupo_id: grupoId, 
                        _wpnonce: '<?php echo wp_create_nonce("gerar_charada_nonce"); ?>' 
                    },
                    success: function (response) {
                        $('#charada-container').html(`
                            <p><strong><?php _e('Charada:', 'sz-conectar-idb'); ?></strong> ${response.data.pergunta}</p>
                        `);
                    }
                });
            }
        });
        </script>
        <?php
        return ob_get_clean();
    }
}
