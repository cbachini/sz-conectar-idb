<?php

class Sz_Conectar_Idb_Codes {

    /**
     * Inicializa a classe registrando as ações AJAX e os hooks de usuário
     */
    public static function init() {
        // Registra as ações AJAX
        add_action('wp_ajax_nopriv_validate_access_code', [__CLASS__, 'validate_access_code']);
        add_action('wp_ajax_validate_access_code', [__CLASS__, 'validate_access_code']);

        // Hook para verificar o código ao logar
        add_filter('wp_authenticate_user', [__CLASS__, 'check_code_expiration_on_login']);
    }

    /**
     * Verifica a expiração do código ao logar e impede o login, se necessário
     */
    public static function check_code_expiration_on_login($user) {
        global $wpdb;

        // Obtém o role do usuário
        $role = $user->roles[0];

        // Define a tabela com base no role do usuário
        $table_name = $role === 'customer' 
            ? $wpdb->prefix . 'sz_access_codes' 
            : ($role === 'previewer' ? $wpdb->prefix . 'sz_tasting_codes' : null);

        if (!$table_name) {
            return $user; // Se o role não for customer ou previewer, permite o login
        }

        // Obtém o código do usuário
        $codigo = get_user_meta($user->ID, 'codigo', true);

        if (!$codigo) {
            return $user; // Se o usuário não tem um código associado, permite o login
        }

        // Consulta o código no banco de dados
        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE access_code = %s", $codigo);
        $code = $wpdb->get_row($query);

        if (!$code) {
            return $user; // Código não encontrado ou inválido, permite o login
        }

        // Verifica a validade do código
        if ($code->valid_until !== null && strtotime($code->valid_until) < time()) {
            // Código expirado: impede o login e retorna um erro
            return new WP_Error('codigo_expirado', __('Seu código de acesso está expirado. Entre em contato com o suporte.'));
        }

        return $user; // Código válido, permite o login
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
        if ($code->current_uses >= $code->max_uses) {
            wp_send_json_error(['message' => 'O limite de uso deste código foi atingido.']);
        }

        // Validação da data de validade (ignora validação se o campo valid_until for NULL)
        if ($code->valid_until !== null && strtotime($code->valid_until) < time()) {
            wp_send_json_error(['message' => 'O código está expirado.']);
        }

        // Conta o número de usuários que já utilizam este código (campo ACF "codigo")
        $total_usuarios = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value = %s",
                'codigo', // Nome do campo ACF
                $codigo   // Valor do código
            )
        );

        // Atualiza o campo `current_uses` na tabela com o valor calculado
        $wpdb->update(
            $table_name,
            ['current_uses' => $total_usuarios],
            ['access_code' => $codigo],
            ['%d'],
            ['%s']
        );

        // Resposta de sucesso
        wp_send_json_success([
            'message' => 'Código válido!',
            'remaining_uses' => $code->max_uses - $total_usuarios,
            'total_users' => $total_usuarios // Retorna o total de usuários que utilizaram o código
        ]);
    }
}

// Inicializa a classe
Sz_Conectar_Idb_Codes::init();
