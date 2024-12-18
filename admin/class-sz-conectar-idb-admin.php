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
            __('Painel', 'sz-conectar-idb'), // Título da página
            __('Mixirica', 'sz-conectar-idb'), // Nome do menu
            'manage_options',
            'mixirica',
            array($this, 'render_main_dashboard'), // Callback da página principal
            '', // Ícone via CSS
            25
        );

        // Submenu: Painel (redundante mas explícito para organização)
        add_submenu_page(
            'mixirica',
            __('Painel', 'sz-conectar-idb'),
            __('Painel', 'sz-conectar-idb'),
            'manage_options',
            'mixirica',
            array($this, 'render_main_dashboard')
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
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . '../js/sz-conectar-idb-admin.js',
            array('jquery'),
            $this->version,
            true
        );
    }

    /**
     * Render the main Mixirica dashboard page (Painel).
     */
    public function render_main_dashboard() {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Mixirica - Painel Principal', 'sz-conectar-idb') . '</h1>';
        echo '<p>' . esc_html__('Bem-vindo ao painel principal do Mixirica. Escolha uma opção no menu.', 'sz-conectar-idb') . '</p>';
        echo '</div>';
    }

    /**
     * Render the Frases de Acesso page.
     */
    public function render_access_phrases_page() {
        include plugin_dir_path(__FILE__) . 'partials/phrases-table.php';
    }

    /**
     * Render the Códigos para o Professor page.
     */
    public function render_teacher_codes_page() {
        include plugin_dir_path(__FILE__) . 'partials/codes-table.php';
    }

    /**
     * Render the Códigos de Degustação page.
     */
    public function render_tasting_codes_page() {
        include plugin_dir_path(__FILE__) . 'partials/tasting-codes-table.php';
    }
}
