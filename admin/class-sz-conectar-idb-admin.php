<?php

/**
 * Class responsible for managing admin menus and pages.
 *
 * @package Sz_Conectar_IDB
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class SZ_Conectar_IDB_Admin {

    private $plugin_name;
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $version     The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        // Hooks
        add_action('admin_menu', array($this, 'add_menus'));
        add_action('admin_head', array($this, 'add_custom_emoji_icon'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Register the main admin menu and submenus.
     */
    public function add_menus() {
        // Menu principal: Mixirica com página Painel
        add_menu_page(
            __('Painel Principal', 'sz-conectar-idb'),
            __('Mixirica', 'sz-conectar-idb'),
            'manage_options',
            'mixirica',
            array($this, 'render_main_dashboard'),
            '', // Ícone será adicionado via CSS
            25
        );

        // Submenu: Frases de Acesso
        add_submenu_page(
            'mixirica',
            __('Frases de Acesso', 'sz-conectar-idb'),
            __('Frases de Acesso', 'sz-conectar-idb'),
            'manage_options',
            'frases_acesso',
            array($this, 'render_access_phrases_page')
        );

        // Submenu: Códigos para o Professor
        add_submenu_page(
            'mixirica',
            __('Códigos para o Professor', 'sz-conectar-idb'),
            __('Códigos para o Professor', 'sz-conectar-idb'),
            'manage_options',
            'codigos_professor',
            array($this, 'render_teacher_codes_page')
        );

        // Submenu: Códigos de Degustação
        add_submenu_page(
            'mixirica',
            __('Códigos de Degustação', 'sz-conectar-idb'),
            __('Códigos de Degustação', 'sz-conectar-idb'),
            'manage_options',
            'codigos_degustacao',
            array($this, 'render_tasting_codes_page')
        );
    }

    /**
     * Add custom CSS to display an emoji as the menu icon.
     */
    public function add_custom_emoji_icon() {
        echo '<style>
            #adminmenu .toplevel_page_mixirica div.wp-menu-image:before {
                content: "🍊";
                font-size: 20px;
            }
        </style>';
    }

    /**
     * Enqueue admin styles.
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . '../css/sz-conectar-idb-admin.css',
            array(),
            $this->version
        );
    }

/**
 * Enqueue admin scripts.
 *
 * @param string $hook The current admin page hook.
 */
public function enqueue_scripts($hook) {
    // Verifica se estamos na página de Códigos para o Professor
    if (strpos($hook, 'codigos_professor') === false) {
        return; // Sai da função se não for a página correta
    }

    // Enfileirar o DataTables CSS
    wp_enqueue_style(
        'datatables-css',
        'https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css'
    );

    // Enfileirar o DataTables JS
    wp_enqueue_script(
        'datatables-js',
        'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js',
        array('jquery'),
        null,
        true
    );

    // Enfileirar o script personalizado para inicializar o DataTables
    wp_enqueue_script(
        'teacher-codes-datatables-init',
        plugin_dir_url(__FILE__) . '../js/teacher-codes-datatables-init.js',
        array('jquery', 'datatables-js'),
        null,
        true
    );
}
    

    /**
     * Render the main Mixirica dashboard page (Painel).
     */
    public function render_main_dashboard() {
        include plugin_dir_path(__FILE__) . 'partials/main-panel.php';
    }

    /**
     * Render the Frases de Acesso page.
     */
    public function render_access_phrases_page() {
        include plugin_dir_path(__FILE__) . 'partials/access-phrases.php';
    }

    /**
     * Render the Códigos para o Professor page.
     */
    public function render_teacher_codes_page() {
        include plugin_dir_path(__FILE__) . 'partials/teacher-codes.php';
    }

    /**
     * Render the Códigos de Degustação page.
     */
    public function render_tasting_codes_page() {
        include plugin_dir_path(__FILE__) . 'partials/tasting-codes.php';
    }
}
