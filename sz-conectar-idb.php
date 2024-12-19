<?php

/**
 * Plugin Name:       Soyuz - IDB
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
 * AJAX Validation Function for Access Codes.
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
 * AJAX Validation Function for Riddles.
 */
function validar_charada_resposta() {
    global $wpdb;

    if (isset($_POST['charada_id']) && isset($_POST['resposta'])) {
        $charada_id = intval($_POST['charada_id']);
        $resposta = sanitize_text_field($_POST['resposta']);

        $table_name = $wpdb->prefix . 'sz_access_phrases';
        $charada = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d AND resposta = %s",
            $charada_id,
            $resposta
        ));

        if ($charada) {
            wp_send_json_success(__('Resposta correta!', 'sz-conectar-idb'));
        } else {
            wp_send_json_error(__('Resposta incorreta. Tente novamente.', 'sz-conectar-idb'));
        }
    } else {
        wp_send_json_error(__('Dados insuficientes.', 'sz-conectar-idb'));
    }
}

add_action('wp_ajax_validar_charada_resposta', 'validar_charada_resposta');
add_action('wp_ajax_nopriv_validar_charada_resposta', 'validar_charada_resposta');

/**
 * Shortcode for Generating Riddles.
 */
function gerar_charada_shortcode() {
    ob_start();
    ?>
    <form id="charada-form">
        <div id="charada-container">
            <p><?php _e('Carregando charada...', 'sz-conectar-idb'); ?></p>
        </div>
        <div id="loading-message" style="display: none;"><?php _e('Verificando a resposta...', 'sz-conectar-idb'); ?></div>
        <div id="result-message"></div>
    </form>

    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            const grupoElement = document.getElementById('numero-livro');
            if (grupoElement) {
                const grupoId = grupoElement.innerText.trim();

                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'gerar_charada_ajax',
                        grupo_id: grupoId
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#charada-container').html(response.data);
                        } else {
                            $('#charada-container').html('<p><?php _e('Nenhuma charada disponível para este grupo.', 'sz-conectar-idb'); ?></p>');
                        }
                    },
                    error: function () {
                        $('#charada-container').html('<p><?php _e('Erro ao carregar a charada. Tente novamente mais tarde.', 'sz-conectar-idb'); ?></p>');
                    }
                });
            }

            $('#charada-form').on('submit', function (e) {
                e.preventDefault();

                $('#loading-message').show();
                $('#result-message').html('');

                const formData = $(this).serialize();

                $.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        $('#loading-message').hide();

                        if (response.success) {
                            $('#result-message').html('<p style="color: green;"><?php _e('Resposta correta!', 'sz-conectar-idb'); ?></p>');
                        } else {
                            $('#result-message').html('<p style="color: red;"><?php _e('Resposta incorreta. Tente novamente.', 'sz-conectar-idb'); ?></p>');
                        }
                    },
                    error: function () {
                        $('#loading-message').hide();
                        $('#result-message').html('<p style="color: red;"><?php _e('Erro ao verificar a resposta. Tente novamente.', 'sz-conectar-idb'); ?></p>');
                    }
                });
            });
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('gerar_charada', 'gerar_charada_shortcode');

/**
 * AJAX for Generating Riddles.
 */
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
