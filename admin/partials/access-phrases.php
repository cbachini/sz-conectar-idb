<?php
/**
 * Partials for the "Frases de Acesso" admin page.
 *
 * @package Sz_Conectar_IDB
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Verifica se o usuário tem permissão para acessar a página
if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.', 'sz-conectar-idb'));
}

// Conexão com o banco de dados
global $wpdb;
$table_name = $wpdb->prefix . 'frases_acesso';

// Lógica de submissão do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['access_phrase_nonce'])) {
    check_admin_referer('add_access_phrase', 'access_phrase_nonce');

    $grupo_id = intval($_POST['grupo_id']);
    $pergunta = sanitize_text_field($_POST['pergunta']);
    $resposta = sanitize_text_field($_POST['resposta']);

    if (!empty($grupo_id) && !empty($pergunta) && !empty($resposta)) {
        $wpdb->insert(
            $table_name,
            array(
                'grupo_id' => $grupo_id,
                'pergunta' => $pergunta,
                'resposta' => $resposta
            ),
            array('%d', '%s', '%s') // Tipos de dados: integer, string, string
        );

        echo '<div class="notice notice-success is-dismissible"><p>' . __('Frase de acesso adicionada com sucesso.', 'sz-conectar-idb') . '</p></div>';
    } else {
        echo '<div class="notice notice-error is-dismissible"><p>' . __('Preencha todos os campos corretamente.', 'sz-conectar-idb') . '</p></div>';
    }
}

// Busca frases existentes no banco
$frases = $wpdb->get_results("SELECT * FROM $table_name");
?>

<div class="wrap">
    <h1><?php echo esc_html(__('Frases de Acesso', 'sz-conectar-idb')); ?></h1>

    <!-- Formulário para adicionar uma nova frase -->
    <h2><?php echo esc_html(__('Adicionar Nova Frase de Acesso', 'sz-conectar-idb')); ?></h2>
    <form method="post">
        <?php wp_nonce_field('add_access_phrase', 'access_phrase_nonce'); ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="grupo_id"><?php echo esc_html(__('ID do Grupo', 'sz-conectar-idb')); ?></label>
                </th>
                <td>
                    <input type="number" name="grupo_id" id="grupo_id" class="regular-text" required>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="pergunta"><?php echo esc_html(__('Pergunta', 'sz-conectar-idb')); ?></label>
                </th>
                <td>
                    <textarea name="pergunta" id="pergunta" class="large-text" rows="3" required></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="resposta"><?php echo esc_html(__('Resposta', 'sz-conectar-idb')); ?></label>
                </th>
                <td>
                    <input type="text" name="resposta" id="resposta" class="regular-text" required>
                </td>
            </tr>
        </table>
        <p class="submit">
            <button type="submit" class="button button-primary"><?php echo esc_html(__('Adicionar Frase', 'sz-conectar-idb')); ?></button>
        </p>
    </form>

    <!-- Tabela de frases -->
    <h2><?php echo esc_html(__('Frases de Acesso Existentes', 'sz-conectar-idb')); ?></h2>
    <table id="access-phrases-table" class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width: 2%;"><input type="checkbox" id="select-all"></th>
                <th style="width: 5%;"><?php echo esc_html(__('ID', 'sz-conectar-idb')); ?></th>
                <th style="width: 10%;"><?php echo esc_html(__('ID do Grupo', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Pergunta', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Resposta', 'sz-conectar-idb')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($frases)): ?>
                <?php foreach ($frases as $frase): ?>
                    <tr>
                        <td><input type="checkbox" class="row-checkbox" value="<?php echo esc_attr($frase->id); ?>"></td>
                        <td><?php echo esc_html($frase->id); ?></td>
                        <td><?php echo esc_html($frase->grupo_id); ?></td>
                        <td><?php echo esc_html($frase->pergunta); ?></td>
                        <td><?php echo esc_html($frase->resposta); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5"><?php echo esc_html(__('Nenhuma frase de acesso encontrada.', 'sz-conectar-idb')); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Inicialização do DataTables -->
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#access-phrases-table').DataTable({
            pageLength: 25,
            order: [[1, 'asc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            }
        });

        // Checkbox para selecionar todos os itens
        $('#select-all').on('click', function() {
            $('.row-checkbox').prop('checked', this.checked);
        });
    });
</script>
