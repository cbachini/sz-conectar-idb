<?php

if (!class_exists('Sz_Conectar_Idb_Codes')) {
    class Sz_Conectar_Idb_Codes {

        public static function init() {
            // Hook para Elementor Forms
            add_action('elementor_pro/forms/new_record', [self::class, 'validate_elementor_form'], 10, 2);
        }

        /**
         * Validações específicas para Elementor Forms
         */
        public static function validate_elementor_form($record, $handler) {
            // Certifica-se de processar apenas formulários relevantes
            $form_name = $record->get_form_settings('form_name');

            // Identifica o formulário e a tabela correta
            global $wpdb;
            if ($form_name === 'Cadastro') {
                $table_name = $wpdb->prefix . 'sz_access_codes'; // Tabela de professores
            } elseif ($form_name === 'Cadastro Trial') {
                $table_name = $wpdb->prefix . 'sz_tasting_codes'; // Tabela de degustação
            } else {
                return; // Ignorar outros formulários
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

            $codigo = sanitize_text_field($fields['form_fields[codigo]']);

            // Verifica se o código existe na tabela correspondente
            $code = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_name WHERE access_code = %s AND is_active = 1",
                $codigo
            ));

            if (!$code) {
                $handler->add_error('form_fields[codigo]', __('Código de acesso inválido ou inativo.', 'sz-conectar-idb'));
                return;
            }

            // Validação: Código expirado
            if (!is_null($code->valid_until) && strtotime($code->valid_until) < time()) {
                $handler->add_error('form_fields[codigo]', __('O código de acesso expirou.', 'sz-conectar-idb'));
                return;
            }

            // Validação: Limite de usos atingido
            if ($code->current_uses >= $code->max_uses) {
                $handler->add_error('form_fields[codigo]', __('O código de acesso já atingiu o limite de usos.', 'sz-conectar-idb'));
                return;
            }

            // Atualiza o uso do código na tabela correspondente
            $wpdb->update(
                $table_name,
                ['current_uses' => $code->current_uses + 1],
                ['id' => $code->id]
            );
        }
    }

    Sz_Conectar_Idb_Codes::init();
}
