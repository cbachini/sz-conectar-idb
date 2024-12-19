<?php

if (!class_exists('Sz_Conectar_Idb')) {
    class Sz_Conectar_Idb {

        protected $loader;

        public function __construct() {
            $this->load_dependencies();
            $this->define_admin_hooks();
            $this->define_shortcodes();
        }

        private function load_dependencies() {
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sz-conectar-idb-loader.php';
            $this->loader = new Sz_Conectar_Idb_Loader();
        }

        private function define_admin_hooks() {
            $plugin_admin = new SZ_Conectar_IDB_Admin('sz-conectar-idb', SZ_CONECTAR_IDB_VERSION);
        }

        private function define_shortcodes() {
            Sz_Conectar_Idb_Shortcodes::init();
        }

        public function run() {
            $this->loader->run();
        }
    }
}
