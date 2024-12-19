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

// Register activation and deactivation hooks.
register_activation_hook(__FILE__, ['Sz_Conectar_Idb_Activator', 'activate']);
register_deactivation_hook(__FILE__, ['Sz_Conectar_Idb_Deactivator', 'deactivate']);

// Initialize the plugin.
function run_sz_conectar_idb() {
    $plugin = new Sz_Conectar_Idb();
    $plugin->run();
}
run_sz_conectar_idb();
