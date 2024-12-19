<?php

/**
 * Classe para validação de códigos de acesso.
 */
class Sz_Conectar_Idb_Codes {

    public static function init() {
        add_action('wp_ajax_validar_codigo_acesso', [self::class, 'validar_codigo_acesso']);
        add_action('wp_ajax_nopriv_validar_codigo_acesso', [self::class, 'validar_codigo_acesso']);
    }

    public static function validar_codigo_acesso() {
        global $wpdb;

        $codigo = isset($_POST['codigo']) ? sanitize_text_field($_POST['codigo']) : '';

        if (!empty($codigo)) {
            $table_name = $wpdb->prefix . 'access_codes';
            $code = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_name WHERE access_code = %s AND is_active = 1",
                $codigo
            ));

            if ($code) {
                if ($code->current_uses >= $code->max_uses) {
                    wp_send_json_error(__('Código inválido ou já utilizado.', 'sz-conectar-idb'));
                } else {
                    $wpdb->update($table_name, ['current_uses' => $code->current_uses + 1], ['id' => $code->id]);
                    wp_send_json_success(__('Código válido.', 'sz-conectar-idb'));
                }
            } else {
                wp_send_json_error(__('Código inválido.', 'sz-conectar-idb'));
            }
        } else {
            wp_send_json_error(__('Código não fornecido.', 'sz-conectar-idb'));
        }
    }
}
