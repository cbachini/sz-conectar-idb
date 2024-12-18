<?php

/**
 * Class responsible for managing admin menus, pages, and AJAX functionality.
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

        // AJAX Handlers
        add_action('wp_ajax_fetch_teacher_codes', array($this, 'fetch_teacher_codes'));
        add_action('wp_ajax_apply_bulk_teacher_codes', array($this, 'apply_bulk_teacher_codes'));
    }

    /**
     * Register the main admin menu and submenus.
     */
    public function add_menus() {
        add_menu_page(
            __('Painel Principal', 'sz-conectar-idb'),
            __('Mixirica', 'sz-conectar-idb'),
            'manage_options',
            'mixirica',
            array($this, 'render_main_dashboard'),
            '', // Ícone via CSS
            25
        );

        add_submenu_page(
            'mixirica',
            __('Códigos para o Professor', 'sz-conectar-idb'),
            __('Códigos para o Professor', 'sz-conectar-idb'),
            'manage_options',
            'codigos_professor',
            array($this, 'render_teacher_codes_page')
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

        // DataTables CSS
        wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css');
    }

    /**
     * Enqueue admin scripts.
     *
     * @param string $hook The current admin page hook.
     */
    public function enqueue_scripts($hook) {
        if ($hook === 'mixirica_page_codigos_professor') {
            wp_enqueue_script(
                'datatables-js',
                'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js',
                array('jquery'),
                null,
                true
            );

            wp_enqueue_script(
                'teacher-codes-init',
                plugin_dir_url(__FILE__) . '../js/teacher-codes-datatables-init.js',
                array('jquery', 'datatables-js'),
                null,
                true
            );

            wp_localize_script('teacher-codes-init', 'TeacherCodesAjax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('teacher_codes_nonce')
            ));
        }
    }

    /**
     * Render the main Mixirica dashboard page (Painel).
     */
    public function render_main_dashboard() {
        include plugin_dir_path(__FILE__) . 'partials/main-panel.php';
    }

    /**
     * Render the Códigos para o Professor page.
     */
    public function render_teacher_codes_page() {
        include plugin_dir_path(__FILE__) . 'partials/teacher-codes.php';
    }

    /**
     * Handle AJAX request to fetch teacher codes.
     */
    public function fetch_teacher_codes() {
        check_ajax_referer('teacher_codes_nonce', 'security');

        global $wpdb;
        $table_name = $wpdb->prefix . 'access_codes';

        // Fetch paginated data
        $start = intval($_POST['start']);
        $length = intval($_POST['length']);
        $search_value = sanitize_text_field($_POST['search']['value']);

        // Base query
        $where = "1=1";
        if (!empty($search_value)) {
            $where .= $wpdb->prepare(" AND (school_name LIKE %s OR access_code LIKE %s)", "%$search_value%", "%$search_value%");
        }

        $data = $wpdb->get_results("SELECT * FROM $table_name WHERE $where LIMIT $start, $length");
        $total_filtered = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE $where");
        $total_records = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

        // Return JSON response
        wp_send_json(array(
            'draw' => intval($_POST['draw']),
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_filtered,
            'data' => $data
        ));
    }

    /**
     * Handle bulk actions for teacher codes.
     */
    public function apply_bulk_teacher_codes() {
        check_ajax_referer('teacher_codes_nonce', 'security');

        global $wpdb;
        $table_name = $wpdb->prefix . 'access_codes';

        $ids = isset($_POST['selected_ids']) ? $_POST['selected_ids'] : array();
        $action = sanitize_text_field($_POST['bulk_action']);

        if (!empty($ids) && in_array($action, ['activate', 'deactivate'])) {
            $status = ($action === 'activate') ? 1 : 0;
            $ids_string = implode(',', array_map('intval', $ids));

            $wpdb->query("UPDATE $table_name SET is_active = $status WHERE id IN ($ids_string)");

            wp_send_json_success(__('Action applied successfully.', 'sz-conectar-idb'));
        }

        wp_send_json_error(__('Invalid request or no items selected.', 'sz-conectar-idb'));
    }
}
