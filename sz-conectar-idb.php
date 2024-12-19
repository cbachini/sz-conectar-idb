<?php

/**
 *
 * @link              https://soyuz.com.br
 * @since             2.0.0
 * @package           Sz_Conectar_Idb
 *
 * @wordpress-plugin
 * Plugin Name:       Soyuz / Mixirica (IDB)
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

// Abort if this file is called directly.
if (!defined('WPINC')) {
    die;
}

// Define plugin version.
define('SZ_CONECTAR_IDB_VERSION', '2.0.0');

/**
 * Plugin Activation and Deactivation.
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
 * Core Plugin Class Initialization.
 */
require plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb.php';

function run_sz_conectar_idb() {
    $plugin = new Sz_Conectar_Idb();
    $plugin->run();
}
run_sz_conectar_idb();

/**
 * Functions Specific to Access Codes Validation
 */
function validar_codigo_professor() {
    global $wpdb;

    $codigo = isset($_POST['codigo']) ? sanitize_text_field($_POST['codigo']) : '';

    if (!empty($codigo)) {
        $table_name = $wpdb->prefix . 'sz_access_codes';

        $code = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE access_code = %s AND is_active = 1",
            $codigo
        ));

        if ($code) {
            if ($code->current_uses >= $code->max_uses) {
                wp_send_json_error(__('Código de acesso inválido ou já utilizado.', 'sz-conectar-idb'));
            } else {
                $wpdb->update(
                    $table_name,
                    ['current_uses' => $code->current_uses + 1],
                    ['id' => $code->id]
                );
                wp_send_json_success(__('Código de acesso válido.', 'sz-conectar-idb'));
            }
        } else {
            wp_send_json_error(__('Código de acesso inválido.', 'sz-conectar-idb'));
        }
    } else {
        wp_send_json_error(__('Código não fornecido.', 'sz-conectar-idb'));
    }
}

add_action('wp_ajax_validar_codigo_professor', 'validar_codigo_professor');
add_action('wp_ajax_nopriv_validar_codigo_professor', 'validar_codigo_professor');

/**
 * Functions Specific to Tasting Codes Validation
 */
function validar_codigo_degustacao() {
    global $wpdb;

    $codigo = isset($_POST['codigo']) ? sanitize_text_field($_POST['codigo']) : '';

    if (!empty($codigo)) {
        $table_name = $wpdb->prefix . 'sz_tasting_codes';

        $code = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE code = %s",
            $codigo
        ));

        if ($code) {
            wp_send_json_success(__('Código de degustação válido.', 'sz-conectar-idb'));
        } else {
            wp_send_json_error(__('Código de degustação inválido.', 'sz-conectar-idb'));
        }
    } else {
        wp_send_json_error(__('Código não fornecido.', 'sz-conectar-idb'));
    }
}

add_action('wp_ajax_validar_codigo_degustacao', 'validar_codigo_degustacao');
add_action('wp_ajax_nopriv_validar_codigo_degustacao', 'validar_codigo_degustacao');

/**
 * Functions Specific to Phrases Validation and Riddle Generation
 */
function validar_frase_acesso() {
    global $wpdb;

    $resposta = isset($_POST['resposta']) ? sanitize_text_field($_POST['resposta']) : '';
    $charada_id = isset($_POST['charada_id']) ? intval($_POST['charada_id']) : 0;

    if (!empty($resposta) && $charada_id > 0) {
        $table_name = $wpdb->prefix . 'sz_access_phrases';

        $phrase = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d AND resposta = %s",
            $charada_id,
            $resposta
        ));

        if ($phrase) {
            wp_send_json_success(__('Resposta correta!', 'sz-conectar-idb'));
        } else {
            wp_send_json_error(__('Resposta incorreta.', 'sz-conectar-idb'));
        }
    } else {
        wp_send_json_error(__('Dados insuficientes.', 'sz-conectar-idb'));
    }
}

add_action('wp_ajax_validar_frase_acesso', 'validar_frase_acesso');
add_action('wp_ajax_nopriv_validar_frase_acesso', 'validar_frase_acesso');

function gerar_charada_ajax() {
    global $wpdb;

    if (isset($_POST['grupo_id'])) {
        $grupo_id = intval($_POST['grupo_id']);
        $table_name = $wpdb->prefix . 'sz_access_phrases';

        $charada = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE grupo_id = %d ORDER BY RAND() LIMIT 1",
            $grupo_id
        ));

        if ($charada) {
            $output = '<input type="hidden" name="charada_id" value="' . esc_attr($charada->id) . '">';
            $output .= '<p><strong>' . __('Charada:', 'sz-conectar-idb') . '</strong> ' . esc_html($charada->pergunta) . '</p>';
            $output .= '<input size="1" type="text" name="resposta" placeholder="' . __('Resposta', 'sz-conectar-idb') . '" required>';
            $output .= '<button type="submit" class="button-primary">' . __('Enviar', 'sz-conectar-idb') . '</button>';
            wp_send_json_success($output);
        } else {
            wp_send_json_error(__('Nenhuma charada disponível para este grupo.', 'sz-conectar-idb'));
        }
    } else {
        wp_send_json_error(__('Grupo não especificado.', 'sz-conectar-idb'));
    }
}

add_action('wp_ajax_gerar_charada_ajax', 'gerar_charada_ajax');
add_action('wp_ajax_nopriv_gerar_charada_ajax', 'gerar_charada_ajax');
