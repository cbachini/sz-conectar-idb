<?php

/**
 * Plugin Name:       Soyuz - IDB
 * Plugin URI:        https://soyuz.com.br
 * Description:       Plugin para validação de códigos e charadas no IDB.
 * Version:           2.0.0
 * Author:            Soyuz
 * Author URI:        https://soyuz.com.br/
 * Text Domain:       sz-conectar-idb
 * Domain Path:       /languages
 */

// Abort if called directly.
if (!defined('WPINC')) {
    die;
}

// Define plugin version.
define('SZ_CONECTAR_IDB_VERSION', '2.0.0');

/**
 * Autoload classes and initialize the plugin.
 */
require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-admin.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-riddles.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-codes.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-shortcodes.php';

/**
 * Initialize all components.
 */
function sz_conectar_idb_init() {
    Sz_Conectar_Idb_Admin::init();
    Sz_Conectar_Idb_Riddles::init();
    Sz_Conectar_Idb_Codes::init();
    Sz_Conectar_Idb_Shortcodes::init();
}
add_action('plugins_loaded', 'sz_conectar_idb_init');
