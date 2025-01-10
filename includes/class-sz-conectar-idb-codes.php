<?php

class Sz_Conectar_Idb_Codes {

    /**
     * Inicializa a classe registrando as ações AJAX
     */
    public static function init() {
        // Registra as ações AJAX
        add_action('wp_ajax_nopriv_validate_access_code', [__CLASS__, 'validate_access_code']);
        add_action('wp_ajax_validate_access_code', [__CLASS__, 'validate_access_code']);
    }

    /**
     * Validação do Código de Acesso via AJAX
     */
    public static function validate_access_code() {
        // Verifica o nonce
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'validate_access_code_nonce')) {
            wp_send_json_error(['message' => 'Nonce inválido ou ausente.']);
        }
    
        // Valida os parâmetros
        if (empty($_POST['codigo'])) {
            wp_send_json_error(['message' => 'O código de acesso é obrigatório.']);
        }
    
        if (empty($_POST['form_type'])) {
            wp_send_json_error(['message' => 'Tipo de formulário não especificado.']);
        }
    
        global $wpdb;
        $codigo = sanitize_text_field($_POST['codigo']);
        $form_type = sanitize_text_field($_POST['form_type']);
    
        // Define a tabela com base no tipo de formulário
        $table_name = $form_type === 'professor' 
            ? $wpdb->prefix . 'sz_access_codes' 
            : $wpdb->prefix . 'sz_tasting_codes';
    
        // Consulta o banco de dados
        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE access_code = %s AND is_active = 1", $codigo);
        $code = $wpdb->get_row($query);
    
        if (!$code) {
            wp_send_json_error(['message' => 'Código inválido ou inativo.']);
        }
    
        wp_send_json_success(['message' => 'Código válido!']);
    }
}

// Inicializa a classe
Sz_Conectar_Idb_Codes::init();
