<?php

if (!function_exists('validate_access_code')) {
    function validate_access_code($record, $ajax_handler) {
        // Obtém o ID do formulário
        $form_id = $record->get_form_settings('form_id');

        // Identifica a tabela correta com base no ID do formulário
        global $wpdb;

        if ($form_id === '1b426a8') {
            $table_name = $wpdb->prefix . 'sz_tasting_codes'; // Tabela de degustação
        } elseif ($form_id === '605fd56') {
            $table_name = $wpdb->prefix . 'sz_access_codes'; // Tabela de professores
        } else {
            return; // Ignorar outros formulários
        }

        // Obtém os campos do formulário
        $raw_fields = $record->get('fields');
        $fields = [];
        foreach ($raw_fields as $id => $field) {
            $fields[$field['name']] = $field;
        }

        // Obtém o valor do campo "codigo"
        $codigo = sanitize_text_field($fields['form_fields[codigo]']['value']);

        // Verifica se o código existe na tabela correspondente
        $code = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE access_code = %s AND is_active = 1",
            $codigo
        ));

        if (!$code) {
            $ajax_handler->add_error('form_fields[codigo]', __('Código de acesso inválido ou inativo.', 'sz-conectar-idb'));
            return;
        }

        // Caso o código seja válido, nenhuma ação adicional é necessária neste ponto.
    }

    add_action('elementor_pro/forms/validation', 'validate_access_code', 10, 2);
}
