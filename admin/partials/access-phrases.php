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

// Inicializa a variável $frases
$frases = [];

// Conexão com o banco de dados
global $wpdb;
$table_name = $wpdb->prefix . 'frases_acesso';

// Consulta ao banco de dados
$frases = $wpdb->get_results("SELECT * FROM $table_name");

?>

<div class="wrap">
    <h1><?php echo esc_html(__('Frases de Acesso', 'sz-conectar-idb')); ?></h1>

    <!-- Formulário para adicionar uma nova frase -->
    <h2><?php echo esc_html(__('Adicionar Nova Frase de Acesso', 'sz-conectar-idb')); ?></h2>
    <form method="post" id="add-access-phrase-form">
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

    <!-- Ações em massa -->
    <h2><?php echo esc_html(__('Frases de Acesso Existentes', 'sz-conectar-idb')); ?></h2>
    <div class="bulk-actions">
        <select id="bulk-action-select" class="button">
            <option value=""><?php echo esc_html(__('Ações em Massa', 'sz-conectar-idb')); ?></option>
            <option value="delete"><?php echo esc_html(__('Apagar Selecionados', 'sz-conectar-idb')); ?></option>
        </select>
        <button id="apply-bulk-action" class="button button-secondary"><?php echo esc_html(__('Aplicar', 'sz-conectar-idb')); ?></button>
    </div>

    <!-- Tabela de frases -->
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

<!-- Inicialização do DataTables e Ações em Massa -->
<script type="text/javascript">
    jQuery(document).ready(function($) {
        var table = $('#access-phrases-table').DataTable({
            pageLength: 25,
            order: [[1, 'asc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            },
            columnDefs: [
                { orderable: false, targets: 0 } // Desativa ordenação na coluna do checkbox
            ]
        });

        // Selecionar todos os checkboxes
        $('#select-all').on('click', function() {
            $('.row-checkbox').prop('checked', this.checked);
        });

        // Aplicar ação em massa
        $('#apply-bulk-action').on('click', function() {
            var action = $('#bulk-action-select').val();
            var selected = $('.row-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            if (action === 'delete' && selected.length > 0) {
                if (confirm('<?php echo esc_js(__('Tem certeza que deseja apagar os itens selecionados?', 'sz-conectar-idb')); ?>')) {
                    $.post(ajaxurl, {
                        action: 'delete_access_phrases',
                        ids: selected,
                        security: '<?php echo wp_create_nonce('delete_access_phrases_nonce'); ?>'
                    }, function(response) {
                        if (response.success) {
                            alert(response.data.message);
                            location.reload();
                        } else {
                            alert(response.data.message);
                        }
                    });
                }
            } else {
                alert('<?php echo esc_js(__('Selecione uma ação e pelo menos um item.', 'sz-conectar-idb')); ?>');
            }
        });
    });
</script>

<?php
// Handler AJAX no functions.php ou no arquivo responsável:
add_action('wp_ajax_delete_access_phrases', function() {
    check_ajax_referer('delete_access_phrases_nonce', 'security');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => __('Permissão negada.', 'sz-conectar-idb')]);
    }

    $ids = isset($_POST['ids']) ? array_map('intval', $_POST['ids']) : [];
    if (!empty($ids)) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'frases_acesso';
        $wpdb->query("DELETE FROM $table_name WHERE id IN (" . implode(',', $ids) . ")");
        wp_send_json_success(['message' => __('Itens apagados com sucesso.', 'sz-conectar-idb')]);
    } else {
        wp_send_json_error(['message' => __('Nenhum item selecionado.', 'sz-conectar-idb')]);
    }
});
?>
