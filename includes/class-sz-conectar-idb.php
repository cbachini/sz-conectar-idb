<?php

if (!class_exists('Sz_Conectar_Idb')) {
    class Sz_Conectar_Idb {

        protected $loader;
        protected $plugin_name;
        protected $version;

        public function __construct() {
            if (defined('SZ_CONECTAR_IDB_VERSION')) {
                $this->version = SZ_CONECTAR_IDB_VERSION;
            } else {
                $this->version = '1.0.0';
            }
            $this->plugin_name = 'sz-conectar-idb';

            $this->load_dependencies();
            $this->set_locale();
            $this->define_admin_hooks();
            $this->define_public_hooks();
            $this->define_shortcodes();
        }

        private function load_dependencies() {
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sz-conectar-idb-loader.php';
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sz-conectar-idb-i18n.php';
            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-sz-conectar-idb-admin.php';
            require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-sz-conectar-idb-public.php';
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sz-conectar-idb-shortcodes.php';

            $this->loader = new Sz_Conectar_Idb_Loader();
        }

        private function set_locale() {
            $plugin_i18n = new Sz_Conectar_Idb_i18n();
            $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
        }

        private function define_admin_hooks() {
            $plugin_admin = new Sz_Conectar_Idb_Admin($this->get_plugin_name(), $this->get_version());
            $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
            $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        }

        private function define_public_hooks() {
            $plugin_public = new Sz_Conectar_Idb_Public($this->get_plugin_name(), $this->get_version());
            $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
            $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        }

        private function define_shortcodes() {
            add_shortcode('gerar_charada', ['Sz_Conectar_Idb_Shortcodes', 'gerar_charada_shortcode']);
        }

        public function run() {
            $this->loader->run();
        }

        public function get_plugin_name() {
            return $this->plugin_name;
        }

        public function get_loader() {
            return $this->loader;
        }

        public function get_version() {
            return $this->version;
        }
    }
}
