<?php

class Sz_Conectar_Idb_Codes {

    /**
     * Inicializa a classe registrando as ações AJAX e os hooks de usuário
     */
    public static function init() {
        // Registra as ações AJAX
        add_action('wp_ajax_nopriv_validate_access_code', [__CLASS__, 'validate_access_code']);
        add_action('wp_ajax_validate_access_code', [__CLASS__, 'validate_access_code']);

        // Registra os hooks para atualizar o banco de dados ao criar/atualizar usuários
        add_action('user_register', [__CLASS__, 'update_code_usage']);
        add_action('profile_update', [__CLASS__, 'update_code_usage']);
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

        // Consulta o banco de dados para verificar o código
        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE access_code = %s AND is_active = 1", $codigo);
        $code = $wpdb->get_row($query);

        if (!$code) {
            wp_send_json_error(['message' => 'Código inválido ou inativo.']);
        }

        // Validação do limite de usos
        if ($code->used_count >= $code->max_uses) {
            wp_send_json_error(['message' => 'O limite de uso deste código foi atingido.']);
        }

        // Validação da data de validade
        if (strtotime($code->valid_until) < time()) {
            wp_send_json_error(['message' => 'O código está expirado.']);
        }

        // Conta o número de usuários que já utilizam este código (campo ACF "codigo")
        $total_usuarios = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value = %s",
                'codigo',
                $codigo
            )
        );

        // Resposta de sucesso
        wp_send_json_success([
            'message' => 'Código válido!',
            'remaining_uses' => $code->max_uses - $total_usuarios,
            'total_users' => $total_usuarios // Retorna o total de usuários que utilizaram o código
        ]);
    }

    /**
     * Atualiza o campo `used_count` no banco de dados quando usuários são criados ou atualizados
     */
    public static function update_code_usage($user_id) {
        global $wpdb;

        // Recupera o código do usuário a partir do campo ACF
        $codigo = get_field('codigo', 'user_' . $user_id);

        if (!$codigo) {
            return; // Se o usuário não tem um código associado, não faz nada
        }

        // Conta todos os usuários que possuem este código
        $total_usuarios = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value = %s",
                'codigo',
                $codigo
            )
        );

        // Define a tabela com base no código
        $table_name = $wpdb->prefix . 'sz_access_codes';

        // Atualiza o campo `used_count` no banco de dados
        $wpdb->update(
            $table_name,
            ['used_count' => $total_usuarios],
            ['access_code' => $codigo],
            ['%d'],
            ['%s']
        );
    }
}

// Inicializa a classe
Sz_Conectar_Idb_Codes::init();
