<?php

if (!class_exists('Sz_Conectar_Idb')) {
    class Sz_Conectar_Idb {

        protected $loader;

        public function __construct() {
            $this->load_dependencies();
            $this->define_ajax_hooks();
            $this->define_shortcodes();
        }

        private function load_dependencies() {
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sz-conectar-idb-loader.php';
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sz-conectar-idb-shortcodes.php';

            $this->loader = new Sz_Conectar_Idb_Loader();
        }

        private function define_ajax_hooks() {
            $this->loader->add_action('wp_ajax_gerar_charada_ajax', 'Sz_Conectar_Idb_Shortcodes', 'gerar_charada_ajax');
            $this->loader->add_action('wp_ajax_nopriv_gerar_charada_ajax', 'Sz_Conectar_Idb_Shortcodes', 'gerar_charada_ajax');
            $this->loader->add_action('wp_ajax_validar_charada_resposta', 'Sz_Conectar_Idb_Shortcodes', 'validar_charada_resposta');
            $this->loader->add_action('wp_ajax_nopriv_validar_charada_resposta', 'Sz_Conectar_Idb_Shortcodes', 'validar_charada_resposta');
        }

        private function define_shortcodes() {
            Sz_Conectar_Idb_Shortcodes::init();
        }

        public function run() {
            $this->loader->run();
        }
    }
}
