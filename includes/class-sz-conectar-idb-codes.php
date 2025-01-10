<?php

// Evita redefinição da classe
if (!class_exists('Sz_Conectar_Idb_Codes')) {
    class Sz_Conectar_Idb_Codes {

        /**
         * Inicializa a classe registrando as ações AJAX
         */
        public static function init() {
            add_action('wp_ajax_nopriv_validate_access_code', [__CLASS__, 'validate_access_code']);
            add_action('wp_ajax_validate_access_code', [__CLASS__, 'validate_access_code']);
        }

        /**
         * Valida o código de acesso via AJAX
         */
        public static function validate_access_code() {
            if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'validate_access_code_nonce')) {
                wp_send_json_error(['message' => 'Nonce inválido ou ausente.']);
            }

            if (empty($_POST['codigo'])) {
                wp_send_json_error(['message' => 'O código de acesso é obrigatório.']);
            }

            if (empty($_POST['form_type'])) {
                wp_send_json_error(['message' => 'Tipo de formulário não especificado.']);
            }

            global $wpdb;
            $codigo = sanitize_text_field($_POST['codigo']);
            $form_type = sanitize_text_field($_POST['form_type']);

            $table_name = $form_type === 'professor' 
                ? $wpdb->prefix . 'sz_access_codes' 
                : $wpdb->prefix . 'sz_tasting_codes';

            $query = $wpdb->prepare("SELECT * FROM $table_name WHERE access_code = %s AND is_active = 1", $codigo);
            $code = $wpdb->get_row($query);

            if (!$code) {
                wp_send_json_error(['message' => 'Código inválido ou inativo.']);
            }

            wp_send_json_success(['message' => 'Código válido!']);
        }
    }
}
