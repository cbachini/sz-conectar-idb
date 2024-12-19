<?php

/**
 * The core plugin class.
 *
 * This class defines the core functionality of the plugin, including
 * internationalization, admin hooks, public hooks, and AJAX handlers.
 *
 * @since      1.0.0
 * @package    Sz_Conectar_Idb
 * @subpackage Sz_Conectar_Idb/includes
 */
class Sz_Conectar_Idb {

    /**
     * The loader that's responsible for maintaining and registering all hooks.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Sz_Conectar_Idb_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Initialize the core functionality of the plugin.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->plugin_name = 'sz-conectar-idb';
        $this->version = defined('SZ_CONECTAR_IDB_VERSION') ? SZ_CONECTAR_IDB_VERSION : '1.0.0';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_ajax_hooks();
    }

    /**
     * Load required dependencies for the plugin.
     *
     * @since    1.0.0
     */
    private function load_dependencies() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sz-conectar-idb-loader.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sz-conectar-idb-i18n.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-sz-conectar-idb-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-sz-conectar-idb-public.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sz-conectar-idb-codes.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sz-conectar-idb-riddles.php';

        $this->loader = new Sz_Conectar_Idb_Loader();
    }

    /**
     * Define the locale for the plugin for internationalization.
     *
     * @since    1.0.0
     */
    private function set_locale() {
        $plugin_i18n = new Sz_Conectar_Idb_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register admin hooks.
     *
     * @since    1.0.0
     */
    private function define_admin_hooks() {
        $plugin_admin = new Sz_Conectar_Idb_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_menu', $plugin_admin, 'register_admin_menu');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
    }

    /**
     * Register public-facing hooks.
     *
     * @since    1.0.0
     */
    private function define_public_hooks() {
        $plugin_public = new Sz_Conectar_Idb_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        $this->loader->add_shortcode('gerar_charada', $plugin_public, 'gerar_charada_shortcode');
    }

    /**
     * Register AJAX hooks for riddles and access codes.
     *
     * @since    1.0.0
     */
    private function define_ajax_hooks() {
        Sz_Conectar_Idb_Codes::init();
        Sz_Conectar_Idb_Riddles::init();
    }

    /**
     * Run the loader to execute all hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * Get the plugin name.
     *
     * @since    1.0.0
     * @return   string    The plugin name.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * Get the loader instance.
     *
     * @since    1.0.0
     * @return   Sz_Conectar_Idb_Loader    The loader instance.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Get the plugin version.
     *
     * @since    1.0.0
     * @return   string    The plugin version.
     */
    public function get_version() {
        return $this->version;
    }
}
