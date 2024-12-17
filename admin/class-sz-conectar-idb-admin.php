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
     * The ID of this plugin.
     *
     * @var string $plugin_name
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @var string $version
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version     The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        // Hooks
        add_action('admin_menu', array($this, 'add_plugin_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
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
    }

    /**
     * Enqueue admin styles.
     *
     * @since 1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'sz-conectar-idb-admin-css',
            plugin_dir_url(__FILE__) . 'css/sz-conectar-idb-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Enqueue admin scripts.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'sz-conectar-idb-admin-js',
            plugin_dir_url(__FILE__) . 'js/sz-conectar-idb-admin.js',
            array('jquery'),
            $this->version,
            true
        );
    }

    /**
     * Display the dashboard page for the SZ Conectar IDB plugin.
     */
    public function display_admin_dashboard() {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Welcome to SZ Conectar IDB', 'sz-conectar-idb') . '</h1>';
        echo '<p>' . esc_html__('Manage the settings of your plugin here.', 'sz-conectar-idb') . '</p>';
        echo '</div>';
    }

    /**
     * Display the "Frases de Acesso" page.
     */
    public function display_access_phrases_page() {
        include plugin_dir_path(__FILE__) . 'partials/access-phrases.php';
    }
}
