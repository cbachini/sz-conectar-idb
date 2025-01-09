<?php

class Sz_Conectar_Idb_Codes {

    public static function init() {
        add_action('wp_ajax_validate_access_code', [self::class, 'ajax_validate_access_code']);
        add_action('wp_ajax_nopriv_validate_access_code', [self::class, 'ajax_validate_access_code']);
    }

    public static function ajax_validate_access_code() {
        // Verifica se o código foi enviado
        if (!isset($_POST['codigo']) || empty($_POST['codigo'])) {
            wp_send_json_error(['message' => __('O código de acesso é obrigatório.', 'sz-conectar-idb')]);
        }

        $codigo = sanitize_text_field($_POST['codigo']);
        global $wpdb;

        // Determina a tabela correta com base no tipo de formulário
        $form_type = sanitize_text_field($_POST['form_type']);
        if ($form_type === 'degustacao') {
            $table_name = $wpdb->prefix . 'sz_tasting_codes';
        } elseif ($form_type === 'professor') {
            $table_name = $wpdb->prefix . 'sz_access_codes';
        } else {
            wp_send_json_error(['message' => __('Tipo de formulário inválido.', 'sz-conectar-idb')]);
        }

        // Consulta o banco de dados
        $code = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE access_code = %s AND is_active = 1",
            $codigo
        ));

        if (!$code) {
            wp_send_json_error(['message' => __('Código de acesso inválido ou inativo.', 'sz-conectar-idb')]);
        }

        // Retorna sucesso
        wp_send_json_success(['message' => __('Código de acesso válido.', 'sz-conectar-idb')]);
    }
}

Sz_Conectar_Idb_Codes::init();
