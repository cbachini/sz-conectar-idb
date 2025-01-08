<?php

if (!class_exists('Sz_Conectar_Idb_Codes')) {
    class Sz_Conectar_Idb_Codes {

        public static function init() {
            // Adiciona validação AJAX para formulários Elementor
            add_action('wp_ajax_validar_codigo_acesso', [self::class, 'validar_codigo_acesso']);
            add_action('wp_ajax_nopriv_validar_codigo_acesso', [self::class, 'validar_codigo_acesso']);
            
            // Hook para Elementor Forms
            add_action('elementor_pro/forms/new_record', [self::class, 'validate_elementor_form'], 10, 2);
        }

        /**
         * Valida o código de acesso via AJAX
         */
        public static function validar_codigo_acesso() {
            global $wpdb;

            $codigo = isset($_POST['codigo']) ? sanitize_text_field($_POST['codigo']) : '';
            $tipo = isset($_POST['tipo']) ? sanitize_text_field($_POST['tipo']) : '';

            if (!empty($codigo) && in_array($tipo, ['professor', 'degustacao'])) {
                $table_name = $wpdb->prefix . 'sz_access_codes';

                $code = $wpdb->get_row($wpdb->prepare(
                    "SELECT * FROM $table_name WHERE access_code = %s AND is_active = 1",
                    $codigo
                ));

                if ($code) {
                    // Validação de data de validade
                    if (strtotime($code->valid_until) < time()) {
                        wp_send_json_error(__('O código de acesso expirou.', 'sz-conectar-idb'));
                    }

                    // Validação de limite de usos
                    if ($code->current_uses >= $code->max_uses) {
                        wp_send_json_error(__('O código de acesso já atingiu o limite de usos.', 'sz-conectar-idb'));
                    }

                    wp_send_json_success(__('Código de acesso válido.', 'sz-conectar-idb'));
                } else {
                    wp_send_json_error(__('Código de acesso inválido.', 'sz-conectar-idb'));
                }
            } else {
                wp_send_json_error(__('Dados insuficientes.', 'sz-conectar-idb'));
            }
        }

        /**
         * Validações específicas para Elementor Forms
         */
        public static function validate_elementor_form($record, $handler) {
            // Certifica-se de processar apenas formulários do Elementor
            $form_name = $record->get_form_settings('form_name');
            if (!in_array($form_name, ['Cadastro', 'Cadastro Trial'])) {
                return;
            }

            $raw_fields = $record->get('fields');
            $fields = [];
            foreach ($raw_fields as $id => $field) {
                $fields[$field['name']] = $field['value'];
            }

            // Validação do campo "Código"
            if (empty($fields['form_fields[codigo]'])) {
                $handler->add_error('form_fields[codigo]', __('O código de acesso é obrigatório.', 'sz-conectar-idb'));
                return;
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'sz_access_codes';
            $codigo = sanitize_text_field($fields['form_fields[codigo]']);

            $code = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_name WHERE access_code = %s AND is_active = 1",
                $codigo
            ));

            if (!$code) {
                $handler->add_error('form_fields[codigo]', __('Código de acesso inválido.', 'sz-conectar-idb'));
                return;
            }

            if (strtotime($code->valid_until) < time()) {
                $handler->add_error('form_fields[codigo]', __('O código de acesso expirou.', 'sz-conectar-idb'));
                return;
            }

            if ($code->current_uses >= $code->max_uses) {
                $handler->add_error('form_fields[codigo]', __('O código de acesso já atingiu o limite de usos.', 'sz-conectar-idb'));
                return;
            }

            // Atualiza o uso do código
            $wpdb->update(
                $table_name,
                ['current_uses' => $code->current_uses + 1],
                ['id' => $code->id]
            );
        }
    }

    Sz_Conectar_Idb_Codes::init();
}
