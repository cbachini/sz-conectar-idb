<?php

class Sz_Conectar_Idb_Codes {

    public static function init() {
        add_action('wp_ajax_nopriv_validate_access_code', [__CLASS__, 'validate_access_code']);
        add_action('wp_ajax_validate_access_code', [__CLASS__, 'validate_access_code']);

        add_filter('wp_authenticate_user', [__CLASS__, 'check_code_expiration_on_login']);

        add_action('user_register', [__CLASS__, 'update_current_uses_for_all']);
        add_action('profile_update', [__CLASS__, 'update_current_uses_for_all']);
        add_action('delete_user', [__CLASS__, 'update_current_uses_for_all']);

        add_action('updated_user_meta', [__CLASS__, 'on_user_meta_change'], 10, 4);
        add_action('deleted_user_meta', [__CLASS__, 'on_user_meta_change'], 10, 4);
        add_action('added_user_meta', [__CLASS__, 'on_user_meta_change'], 10, 4);
    }

    public static function check_code_expiration_on_login($user) {
        global $wpdb;

        $role = $user->roles[0];
        $table_name = ($role === 'customer') 
            ? $wpdb->prefix . 'sz_access_codes' 
            : (($role === 'previewer') ? $wpdb->prefix . 'sz_tasting_codes' : null);

        if (!$table_name) return $user;

        $codigo = get_user_meta($user->ID, 'codigo', true);
        if (!$codigo) return $user;

        $code = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE access_code = %s", $codigo));
        if (!$code || ($code->valid_until !== null && strtotime($code->valid_until) < time())) {
            wp_logout();
            wp_redirect(home_url());
            exit;
        }

        return $user;
    }

    public static function validate_access_code() {
        check_ajax_referer('validate_access_code_nonce', '_wpnonce');

        if (empty($_POST['codigo']) || empty($_POST['form_type'])) {
            wp_send_json_error(['message' => 'Parâmetros inválidos.']);
        }

        global $wpdb;
        $codigo = sanitize_text_field($_POST['codigo']);
        $table_name = ($_POST['form_type'] === 'professor') 
            ? $wpdb->prefix . 'sz_access_codes' 
            : $wpdb->prefix . 'sz_tasting_codes';

        $code = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE access_code = %s AND is_active = 1", $codigo));

        if (!$code) wp_send_json_error(['message' => 'Código inválido ou inativo.']);
        if ($code->current_uses >= $code->max_uses) wp_send_json_error(['message' => 'Limite de uso atingido.']);
        if ($code->valid_until !== null && strtotime($code->valid_until) < time()) wp_send_json_error(['message' => 'O código está expirado.']);

        $total_usuarios = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->usermeta} WHERE meta_key = 'codigo' AND meta_value = %s", $codigo));
        $wpdb->update($table_name, ['current_uses' => $total_usuarios], ['access_code' => $codigo]);

        wp_send_json_success(['message' => 'Código válido!', 'remaining_uses' => $code->max_uses - $total_usuarios]);
    }

    public static function update_current_uses_for_all() {
        global $wpdb;

        $query = "SELECT meta_value AS access_code, COUNT(*) AS total_users FROM {$wpdb->usermeta} WHERE meta_key = 'codigo' GROUP BY meta_value";
        $codes = $wpdb->get_results($query, ARRAY_A);

        if ($codes) {
            foreach ($codes as $code) {
                $wpdb->update("{$wpdb->prefix}sz_access_codes", ['current_uses' => $code['total_users']], ['access_code' => $code['access_code']]);
            }
        }
    }

    public static function on_user_meta_change($meta_id, $object_id, $meta_key, $_meta_value) {
        if ($meta_key === 'codigo') {
            self::update_current_uses_for_all();
        }
    }
}

Sz_Conectar_Idb_Codes::init();
