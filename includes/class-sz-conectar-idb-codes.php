<?php

class Sz_Conectar_Idb_Codes {

    /**
     * Validação do Código de Acesso via AJAX
     */
    public static function ajax_validate_access_code() {
        // Verifica o nonce
        check_ajax_referer('validate_access_code_nonce', 'nonce');

        global $wpdb;

        // Valida parâmetros
        if (empty($_POST['codigo'])) {
            wp_send_json_error(['message' => __('O código de acesso é obrigatório.', 'sz-conectar-idb')]);
        }

        if (empty($_POST['form_type'])) {
            wp_send_json_error(['message' => __('Tipo de formulário não especificado.', 'sz-conectar-idb')]);
        }

        $codigo = sanitize_text_field($_POST['codigo']);
        $form_type = sanitize_text_field($_POST['form_type']);

        // Determina a tabela correta
        $table_name = $form_type === 'professor' 
            ? $wpdb->prefix . 'sz_access_codes' 
            : $wpdb->prefix . 'sz_tasting_codes';

        // Consulta o banco de dados
        $code = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE access_code = %s AND is_active = 1",
            $codigo
        ));

        if (!$code) {
            wp_send_json_error(['message' => __('Código de acesso inválido ou inativo.', 'sz-conectar-idb')]);
        }

        wp_send_json_success(['message' => __('Código de acesso válido!', 'sz-conectar-idb')]);
    }

    public static function init() {
        add_action('wp_ajax_nopriv_validate_access_code', [Sz_Conectar_Idb_Codes::class, 'ajax_validate_access_code']);
    }
}

Sz_Conectar_Idb_Codes::init();
