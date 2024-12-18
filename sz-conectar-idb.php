<?php

/**
 *
 * @link              https://soyuz.com.br
 * @since             1.0.0
 * @package           Sz_Conectar_Idb
 *
 * @wordpress-plugin
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

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('SZ_CONECTAR_IDB_VERSION', '2.0.0');

/**
 * Plugin Activation and Deactivation.
 */
function activate_sz_conectar_idb()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-activator.php';
    Sz_Conectar_Idb_Activator::activate();
}

function deactivate_sz_conectar_idb()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb-deactivator.php';
    Sz_Conectar_Idb_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_sz_conectar_idb');
register_deactivation_hook(__FILE__, 'deactivate_sz_conectar_idb');

/**
 * Core Plugin Class.
 */
require plugin_dir_path(__FILE__) . 'includes/class-sz-conectar-idb.php';

function run_sz_conectar_idb()
{
    $plugin = new Sz_Conectar_Idb();
    $plugin->run();
}
run_sz_conectar_idb();

/**
 * AJAX Validation Function.
 */
function validar_entrada()
{
    global $wpdb;

    $tipo = isset($_POST['tipo']) ? sanitize_text_field($_POST['tipo']) : '';
    $entrada = isset($_POST['entrada']) ? sanitize_text_field($_POST['entrada']) : '';

    if (!empty($tipo) && !empty($entrada)) {
        $table_name = $wpdb->prefix . ($tipo === 'codigo' ? 'access_codes' : 'frases_acesso');
        $coluna = $tipo === 'codigo' ? 'access_code' : 'resposta';
        $condicao_adicional = $tipo === 'codigo' ? ' AND is_active = 1' : '';

        $item = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE $coluna = %s $condicao_adicional",
            $entrada
        ));

        if ($item) {
            if ($tipo === 'codigo') {
                // Atualize o número de usos para códigos de acesso
                if ($item->current_uses >= $item->max_uses) {
                    wp_send_json_error('Código de acesso inválido ou já utilizado.');
                } else {
                    $wpdb->update(
                        "{$wpdb->prefix}access_codes",
                        ['current_uses' => $item->current_uses + 1],
                        ['id' => $item->id]
                    );
                }
            }
            wp_send_json_success('Entrada válida.');
        } else {
            wp_send_json_error('Entrada inválida.');
        }
    } else {
        wp_send_json_error('Dados insuficientes.');
    }
}

add_action('wp_ajax_validar_entrada', 'validar_entrada');
add_action('wp_ajax_nopriv_validar_entrada', 'validar_entrada');

/**
 * Shortcode para Gerar Charada e Integração com Bloqueio de Conteúdo.
 */
function gerar_charada_shortcode()
{
    ob_start();
    ?>
    <form id="charada-form">
        <div id="charada-container">
            <p>Carregando charada...</p>
        </div>
        <div id="loading-message" style="display: none;">Verificando a resposta...</div>
        <div id="result-message"></div>
    </form>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            const grupoElement = document.getElementById('numero-livro');
            if (grupoElement) {
                const grupoId = grupoElement.innerText.trim();

                jQuery.ajax({
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'gerar_charada_ajax',
                        grupo_id: grupoId
                    },
                    success: function (response) {
                        if (response.success) {
                            console.log('Charada carregada com sucesso:', response.data);
                            document.getElementById('charada-container').innerHTML = response.data;
                        } else {
                            console.log('Nenhuma charada disponível:', response.data);
                            document.getElementById('charada-container').innerHTML = '<p>Nenhuma charada disponível para este grupo.</p>';
                        }
                    },
                    error: function () {
                        console.log('Erro ao carregar a charada.');
                        document.getElementById('charada-container').innerHTML = '<p>Erro ao carregar a charada. Tente novamente mais tarde.</p>';
                    }
                });
            } else {
                console.log('Elemento grupo não encontrado.');
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            var form = document.getElementById('charada-form');

            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault(); // Prevent the form from submitting the traditional way

                    document.getElementById('loading-message').style.display = 'block';
                    document.getElementById('result-message').innerHTML = '';

                    var formData = new FormData(form);
                    var resposta = formData.get('form_fields[resposta]');
                    console.log('Resposta enviada:', resposta);

                    var charadaId = formData.get('charada_id');

                    jQuery.ajax({
                        url: '<?php echo admin_url("admin-ajax.php"); ?>',
                        type: 'POST',
                        data: {
                            action: 'validar_charada_resposta',
                            charada_id: charadaId,
                            resposta: resposta
                        },
                        success: function (response) {
                            document.getElementById('loading-message').style.display = 'none';

                            if (response.success) {
                                console.log('Resposta válida:', response.data);
                                document.getElementById('result-message').innerHTML = '<p style="color: green;">Resposta correta!</p>';
                                document.getElementById('audiobook').style.display = 'block';
                                document.getElementById('ebook').style.display = 'block';
                                document.getElementById('aviso-bloqueio').style.display = 'none';
                            } else {
                                console.log('Resposta inválida:', response.data);
                                document.getElementById('result-message').innerHTML = '<p style="color: red;">Resposta incorreta. Tente novamente.</p>';
                            }
                        },
                        error: function () {
                            console.log('Erro ao verificar a resposta.');
                            document.getElementById('loading-message').style.display = 'none';
                            document.getElementById('result-message').innerHTML = '<p style="color: red;">Erro ao verificar a resposta. Tente novamente.</p>';
                        }
                    });
                });
            } else {
                console.log('Formulário de charada não encontrado.');
            }
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('gerar_charada', 'gerar_charada_shortcode');

function gerar_charada_ajax()
{
    global $wpdb;

    if (isset($_POST['grupo_id'])) {
        $grupo_id = intval($_POST['grupo_id']);

        $table_name = $wpdb->prefix . 'frases_acesso';
        $charada = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE grupo_id = %d ORDER BY RAND() LIMIT 1",
            $grupo_id
        ));

        if ($charada) {
            $output = '<input type="hidden" name="charada_id" value="' . esc_attr($charada->id) . '">';
            $output .= '<p><strong>Charada:</strong> ' . esc_html($charada->pergunta) . '</p>';
            $output .= '<input size="1" type="text" name="form_fields[resposta]" id="form-field-resposta" class="elementor-field elementor-size-xs elementor-field-textual" placeholder="Resposta" required="required" aria-required="true">';
            $output .= '<button type="submit" class="elementor-button elementor-size-xs">Enviar</button>';
            wp_send_json_success($output);
        } else {
            wp_send_json_error('Nenhuma charada disponível para este grupo.');
        }
    } else {
        wp_send_json_error('Grupo não especificado.');
    }
}

add_action('wp_ajax_gerar_charada_ajax', 'gerar_charada_ajax');
add_action('wp_ajax_nopriv_gerar_charada_ajax', 'gerar_charada_ajax');

function validar_charada_resposta()
{
    global $wpdb;

    if (isset($_POST['charada_id']) && isset($_POST['resposta'])) {
        $charada_id = intval($_POST['charada_id']);
        $resposta = sanitize_text_field($_POST['resposta']);

        $table_name = $wpdb->prefix . 'frases_acesso';
        $charada = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d AND resposta = %s",
            $charada_id,
            $resposta
        ));

        if ($charada) {
            wp_send_json_success('Resposta correta!');
        } else {
            wp_send_json_error('Resposta incorreta.');
        }
    } else {
        wp_send_json_error('Dados insuficientes.');
    }
}

function sz_conectar_idb_update_db() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'access_codes';

    // Verifica se a coluna valid_until existe
    $row = $wpdb->get_results("SHOW COLUMNS FROM `$table_name` LIKE 'valid_until'");

    if (empty($row)) {
        // Adiciona a coluna valid_until
        $wpdb->query("ALTER TABLE `$table_name` ADD `valid_until` DATE NULL AFTER `is_active`");
    }
}
register_activation_hook(__FILE__, 'sz_conectar_idb_update_db');

add_action('wp_ajax_validar_charada_resposta', 'validar_charada_resposta');
add_action('wp_ajax_nopriv_validar_charada_resposta', 'validar_charada_resposta');
