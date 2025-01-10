<?php
/**
 * Plugin Name:       Soyuz - IDB
 * Plugin URI:        https://soyuz.com.br
 * Description:       Um plugin para adicionar funcionalidades ao site do IDB
 * Version:           2.0.0
 * Author:            Soyuz
 * Author URI:        https://soyuz.com.br/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sz-conectar-idb
 * Domain Path:       /languages
 */

// Abort if this file is called directly.
if (!defined('WPINC')) {
    die;
}

// Define plugin version.
define('SZ_CONECTAR_IDB_VERSION', '2.0.0');

// Load required files for the plugin
require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-loader.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-activator.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-deactivator.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-i18n.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-codes.php';
require_once plugin_dir_path(__FILE__) . 'admin/class-sz-conectar-idb-admin.php';
require_once plugin_dir_path(__FILE__) . 'public/class-sz-conectar-idb-public.php';

// Register activation and deactivation hooks
register_activation_hook(__FILE__, ['Sz_Conectar_Idb_Activator', 'activate']);
register_deactivation_hook(__FILE__, ['Sz_Conectar_Idb_Deactivator', 'deactivate']);

// Initialize the plugin
function run_sz_conectar_idb() {
    // Inicializa o núcleo do plugin
    $plugin = new Sz_Conectar_Idb();
    $plugin->run();

    // Garante o registro das ações AJAX em tempo real
    add_action('init', ['Sz_Conectar_Idb_Codes', 'init']);
}
run_sz_conectar_idb();

/**
 * Enfileira scripts públicos e garante o nonce para AJAX
 */
function sz_conectar_idb_enqueue_public_scripts() {
    if (is_admin()) {
        return; // Não carrega no painel administrativo
    }

    wp_enqueue_script(
        'sz-conectar-idb-public',
        plugin_dir_url(__FILE__) . 'public/js/sz-conectar-idb-public.js',
        ['jquery'],
        SZ_CONECTAR_IDB_VERSION,
        true
    );

    // Passa a URL AJAX e o nonce para o script
    wp_localize_script('sz-conectar-idb-public', 'szConectarAjax', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('validate_access_code_nonce'),
    ]);
}
add_action('wp_enqueue_scripts', 'sz_conectar_idb_enqueue_public_scripts');

/**
 * Carrega os arquivos de tradução
 */
function sz_conectar_idb_load_textdomain() {
    load_plugin_textdomain('sz-conectar-idb', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'sz_conectar_idb_load_textdomain');

/**
 * Classe responsável por registrar e gerenciar as ações AJAX
 */
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
