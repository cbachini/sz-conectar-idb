<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Sz_Conectar_IDB
 * @subpackage Sz_Conectar_IDB/admin
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class SZ_Conectar_IDB_Admin {

    /**
     * Initialize the class and set its properties.
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    /**
     * Register the administration menu for this plugin.
     */
    public function add_plugin_admin_menu() {
        add_menu_page(
            __('SZ Conectar IDB', 'sz-conectar-idb'),
            __('SZ Conectar IDB', 'sz-conectar-idb'),
            'manage_options',
            'sz-conectar-idb',
            array($this, 'display_admin_dashboard'),
            'dashicons-welcome-learn-more',
            20
        );

        add_submenu_page(
            'sz-conectar-idb',
            __('Frases de Acesso', 'sz-conectar-idb'),
            __('Frases de Acesso', 'sz-conectar-idb'),
            'manage_options',
            'sz-conectar-idb-access-phrases',
            array($this, 'display_access_phrases_page')
        );

        add_submenu_page(
            'sz-conectar-idb',
            __('Código para o Professor', 'sz-conectar-idb'),
            __('Código para o Professor', 'sz-conectar-idb'),
            'manage_options',
            'sz-conectar-idb-teacher-codes',
            array($this, 'display_teacher_codes_page')
        );

        add_submenu_page(
            'sz-conectar-idb',
            __('Códigos de Degustação', 'sz-conectar-idb'),
            __('Códigos de Degustação', 'sz-conectar-idb'),
            'manage_options',
            'sz-conectar-idb-tasting-codes',
            array($this, 'display_tasting_codes_page')
        );
    }

    /**
     * Enqueue styles and scripts for the admin area.
     */
    public function enqueue_admin_assets($hook_suffix) {
        // Verifica se a página pertence ao plugin.
        if (strpos($hook_suffix, 'sz-conectar-idb') === false) {
            return;
        }

        // Carregar Bootstrap CSS
        wp_enqueue_style(
            'bootstrap-css',
            'https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css',
            array(),
            '5.3.0',
            'all'
        );

        // Carregar Bootstrap JS
        wp_enqueue_script(
            'bootstrap-js',
            'https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js',
            array('jquery'),
            '5.3.0',
            true
        );

        // Carregar CSS e JS customizados do plugin
        wp_enqueue_style(
            'sz-conectar-idb-admin-css',
            plugin_dir_url(__FILE__) . 'css/sz-conectar-idb-admin.css',
            array('bootstrap-css'),
            '1.0.0',
            'all'
        );

        wp_enqueue_script(
            'sz-conectar-idb-admin-js',
            plugin_dir_url(__FILE__) . 'js/sz-conectar-idb-admin.js',
            array('jquery', 'bootstrap-js'),
            '1.0.0',
            true
        );
    }

    /**
     * Display the dashboard page for the SZ Conectar IDB plugin.
     */
    public function display_admin_dashboard() {
        echo '<div class="wrap"><h1>' . esc_html(__('SZ Conectar IDB Dashboard', 'sz-conectar-idb')) . '</h1></div>';
    }

    /**
     * Display the Frases de Acesso page.
     */
    public function display_access_phrases_page() {
        include plugin_dir_path(__FILE__) . 'partials/access-phrases.php';
    }

    /**
     * Display the Código para o Professor page.
     */
    public function display_teacher_codes_page() {
        include plugin_dir_path(__FILE__) . 'partials/teacher-codes.php';
    }

    /**
     * Display the Códigos de Degustação page.
     */
    public function display_tasting_codes_page() {
        include plugin_dir_path(__FILE__) . 'partials/tasting-codes.php';
    }
}
