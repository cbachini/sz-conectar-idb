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

// Register activation and deactivation hooks.
register_activation_hook(__FILE__, ['Sz_Conectar_Idb_Activator', 'activate']);
register_deactivation_hook(__FILE__, ['Sz_Conectar_Idb_Deactivator', 'deactivate']);

// Initialize the plugin.
function run_sz_conectar_idb() {
    $plugin = new Sz_Conectar_Idb();
    $plugin->run();
}
run_sz_conectar_idb();

function sz_conectar_idb_enqueue_public_scripts() {
    // Enfileira o script público apenas no frontend
    if (!is_admin()) {
        wp_enqueue_script(
            'sz-conectar-idb-public', // Handle do script
            plugin_dir_url(__FILE__) . 'public/js/sz-conectar-idb-public.js', // Caminho do script
            ['jquery'], // Dependência
            '1.0', // Versão
            true // Carregar no footer
        );

        // Passa o URL do AJAX para o script
        wp_localize_script('sz-conectar-idb-public', 'ajaxurl', admin_url('admin-ajax.php'));
    }
}
add_action('wp_enqueue_scripts', 'sz_conectar_idb_enqueue_public_scripts');

