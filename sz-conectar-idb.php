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
require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-codes.php';

// Register activation and deactivation hooks
register_activation_hook(__FILE__, ['Sz_Conectar_Idb_Activator', 'activate']);
register_deactivation_hook(__FILE__, ['Sz_Conectar_Idb_Deactivator', 'deactivate']);

// Initialize the plugin
add_action('init', function () {
    if (class_exists('Sz_Conectar_Idb_Codes')) {
        Sz_Conectar_Idb_Codes::init();
    }
});

/**
 * Enfileira scripts públicos e garante o nonce para AJAX
 */
function sz_conectar_idb_enqueue_public_scripts() {
    if (is_admin()) {
        return; // Não carrega no painel administrativo
    }

    wp_enqueue_script(
        'soyuz_sz_conectar_idb_public',
        plugin_dir_url(__FILE__) . 'public/js/sz-conectar-idb-public.js',
        ['jquery'],
        SZ_CONECTAR_IDB_VERSION,
        true
    );

    wp_localize_script('soyuz_sz_conectar_idb_public', 'szConectarAjax', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('validate_access_code_nonce'),
    ]);
}
add_action('wp_enqueue_scripts', 'sz_conectar_idb_enqueue_public_scripts');
