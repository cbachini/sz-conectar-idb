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

        // Verifica se o campo "codigo" foi enviado
        if (empty($fields['form_fields[codigo]'])) {
            $ajax_handler->add_error('form_fields[codigo]', __('O código de acesso é obrigatório.', 'sz-conectar-idb'));
            return;
        }

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

        // Validação: Código expirado
        if (!is_null($code->valid_until) && strtotime($code->valid_until) < time()) {
            $ajax_handler->add_error('form_fields[codigo]', __('O código de acesso expirou.', 'sz-conectar-idb'));
            return;
        }

        // Validação: Limite de usos atingido
        if ($code->current_uses >= $code->max_uses) {
            $ajax_handler->add_error('form_fields[codigo]', __('O código de acesso já atingiu o limite de usos.', 'sz-conectar-idb'));
            return;
        }

        // Atualiza o uso do código na tabela correspondente
        $wpdb->update(
            $table_name,
            ['current_uses' => $code->current_uses + 1],
            ['id' => $code->id]
        );
    }

    add_action('elementor_pro/forms/validation', 'validate_access_code', 10, 2);
}
