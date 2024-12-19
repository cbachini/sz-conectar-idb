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

if (!defined('WPINC')) {
    die;
}

// Define a versão do plugin
define('SZ_CONECTAR_IDB_VERSION', '2.0.0');

/**
 * Funções de ativação e desativação
 */
function activate_sz_conectar_idb() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-activator.php';
    Sz_Conectar_Idb_Activator::activate();
}

function deactivate_sz_conectar_idb() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-deactivator.php';
    Sz_Conectar_Idb_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_sz_conectar_idb');
register_deactivation_hook(__FILE__, 'deactivate_sz_conectar_idb');

/**
 * Inclui arquivos das funcionalidades
 */
require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-codes.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-riddles.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-shortcodes.php';
