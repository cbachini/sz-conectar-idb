<?php

class Sz_Conectar_Idb_Codes {

    public static function init() {
        // Registra as ações AJAX para usuários autenticados e não autenticados
        add_action('wp_ajax_validate_access_code', [self::class, 'ajax_validate_access_code']);
        add_action('wp_ajax_nopriv_validate_access_code', [self::class, 'ajax_validate_access_code']);
    }

    /**
     * Função para validar o código de acesso via AJAX.
     */
    public static function ajax_validate_access_code() {
        global $wpdb;

        // Verifica se os parâmetros foram enviados corretamente
        if (!isset($_POST['codigo']) || empty($_POST['codigo'])) {
            wp_send_json_error(['message' => __('O código de acesso é obrigatório.', 'sz-conectar-idb')]);
        }

        if (!isset($_POST['form_type']) || empty($_POST['form_type'])) {
            wp_send_json_error(['message' => __('Tipo de formulário não especificado.', 'sz-conectar-idb')]);
        }

        // Sanitiza os dados recebidos
        $codigo = sanitize_text_field($_POST['codigo']);
        $form_type = sanitize_text_field($_POST['form_type']);

        // Determina a tabela correta com base no tipo de formulário
        if ($form_type === 'degustacao') {
            $table_name = $wpdb->prefix . 'sz_tasting_codes';
        } elseif ($form_type === 'professor') {
            $table_name = $wpdb->prefix . 'sz_access_codes';
        } else {
            wp_send_json_error(['message' => __('Tipo de formulário inválido.', 'sz-conectar-idb')]);
        }

        // Consulta o banco de dados para verificar o código
        $code = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE access_code = %s AND is_active = 1",
            $codigo
        ));

        if (!$code) {
            wp_send_json_error(['message' => __('Código de acesso inválido ou inativo.', 'sz-conectar-idb')]);
        }

        // Retorna sucesso com uma mensagem amigável
        wp_send_json_success(['message' => __('Código de acesso válido!', 'sz-conectar-idb')]);
    }
}

// Inicializa a classe para registrar os hooks
Sz_Conectar_Idb_Codes::init();
