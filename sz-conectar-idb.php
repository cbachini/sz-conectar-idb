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

// Autoload includes.
require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-loader.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-activator.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-deactivator.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-i18n.php';
require_once plugin_dir_path(__FILE__) . 'admin/class-sz-conectar-idb-admin.php';
require_once plugin_dir_path(__FILE__) . 'public/class-sz-conectar-idb-public.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-codes.php'; // Adicionado para AJAX

// Register activation and deactivation hooks.
register_activation_hook(__FILE__, ['Sz_Conectar_Idb_Activator', 'activate']);
register_deactivation_hook(__FILE__, ['Sz_Conectar_Idb_Deactivator', 'deactivate']);

// Initialize the plugin.
function run_sz_conectar_idb() {
    $plugin = new Sz_Conectar_Idb();
    $plugin->run();

    // Garante o registro das ações AJAX
    if (class_exists('Sz_Conectar_Idb_Codes')) {
        add_action('init', ['Sz_Conectar_Idb_Codes', 'init']);
    }
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
        'soyuz_sz_conectar_idb_public',
        plugin_dir_url(__FILE__) . 'public/js/sz-conectar-idb-public.js',
        ['jquery'],
        SZ_CONECTAR_IDB_VERSION,
        true
    );

    // Passa a URL AJAX e o nonce para o script
    wp_localize_script('soyuz_sz_conectar_idb_public', 'szConectarAjax', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('validate_access_code_nonce'),
    ]);
}
add_action('wp_enqueue_scripts', 'sz_conectar_idb_enqueue_public_scripts');
