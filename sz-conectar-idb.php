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
require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-codes.php'; // Certifique-se de carregar a classe de ações AJAX

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
