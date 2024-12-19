<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

global $wpdb;
$table_name = $wpdb->prefix . 'sz_access_phrases';

// Handle form submission for adding new phrases
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_access_phrase_nonce']) && wp_verify_nonce($_POST['new_access_phrase_nonce'], 'add_access_phrase')) {
    $group_id = intval($_POST['group_id']);
    $question = sanitize_text_field($_POST['question']);
    $answer = sanitize_text_field($_POST['answer']);

    if (!empty($group_id) && !empty($question) && !empty($answer)) {
        $wpdb->insert(
            $table_name,
            [
                'grupo_id' => $group_id,
                'pergunta' => $question,
                'resposta' => $answer,
            ]
        );
        echo '<div class="notice notice-success is-dismissible"><p>Frase de acesso adicionada com sucesso!</p></div>';
    } else {
        echo '<div class="notice notice-error is-dismissible"><p>Todos os campos são obrigatórios!</p></div>';
    }
}

// Handle bulk delete action
if (isset($_POST['bulk_action']) && $_POST['bulk_action'] === 'delete' && !empty($_POST['selected_ids'])) {
    $selected_ids = array_map('intval', $_POST['selected_ids']);
    $ids = implode(',', $selected_ids);
    $wpdb->query("DELETE FROM $table_name WHERE id IN ($ids)");
    echo '<div class="notice notice-success is-dismissible"><p>Itens selecionados foram deletados com sucesso!</p></div>';
}

// Fetch all phrases
$phrases = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id ASC");

?>

<div class="wrap">
    <h1><?php echo esc_html__('Frases de Acesso', 'sz-conectar-idb'); ?></h1>

    <!-- Add New Phrase Form -->
    <h2><?php echo esc_html__('Adicionar Nova Frase', 'sz-conectar-idb'); ?></h2>
    <form method="post">
        <?php wp_nonce_field('add_access_phrase', 'new_access_phrase_nonce'); ?>
        <table class="form-table">
            <tr>
                <th><label for="group_id"><?php echo esc_html__('ID do Grupo', 'sz-conectar-idb'); ?></label></th>
                <td><input type="number" id="group_id" name="group_id" class="small-text" required></td>
            </tr>
            <tr>
                <th><label for="question"><?php echo esc_html__('Pergunta', 'sz-conectar-idb'); ?></label></th>
                <td><textarea id="question" name="question" class="large-text" rows="3" required></textarea></td>
            </tr>
            <tr>
                <th><label for="answer"><?php echo esc_html__('Resposta', 'sz-conectar-idb'); ?></label></th>
                <td><input type="text" id="answer" name="answer" class="regular-text" required></td>
            </tr>
        </table>
        <?php submit_button(__('Adicionar Frase', 'sz-conectar-idb')); ?>
    </form>

    <hr>

    <!-- Bulk Actions Form -->
    <form method="post" id="bulk-action-form">
        <h2><?php echo esc_html__('Frases Existentes', 'sz-conectar-idb'); ?></h2>
        <div style="margin-bottom: 10px;">
            <select name="bulk_action" id="bulk-action-select">
                <option value=""><?php echo esc_html__('Ações em Massa', 'sz-conectar-idb'); ?></option>
                <option value="delete"><?php echo esc_html__('Apagar Selecionados', 'sz-conectar-idb'); ?></option>
            </select>
            <button type="submit" class="button action" id="bulk-action-apply"><?php echo esc_html__('Aplicar', 'sz-conectar-idb'); ?></button>
        </div>

        <!-- Table of Existing Phrases -->
        <table id="phrases-table" class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width: 30px;"><input type="checkbox" id="select-all"></th>
                    <th style="width: 50px;"><?php echo esc_html__('ID', 'sz-conectar-idb'); ?></th>
                    <th style="width: 50px;"><?php echo esc_html__('ID do Grupo', 'sz-conectar-idb'); ?></th>
                    <th><?php echo esc_html__('Pergunta', 'sz-conectar-idb'); ?></th>
                    <th><?php echo esc_html__('Resposta', 'sz-conectar-idb'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($phrases)): ?>
                    <?php foreach ($phrases as $phrase): ?>
                        <tr>
                            <td><input type="checkbox" name="selected_ids[]" value="<?php echo esc_attr($phrase->id); ?>" class="row-checkbox"></td>
                            <td><?php echo esc_html($phrase->id); ?></td>
                            <td><?php echo esc_html($phrase->grupo_id); ?></td>
                            <td><?php echo esc_html($phrase->pergunta); ?></td>
                            <td><?php echo esc_html($phrase->resposta); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5"><?php echo esc_html__('Nenhuma frase encontrada.', 'sz-conectar-idb'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </form>
</div>

<!-- JavaScript to Handle Bulk Actions -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAllCheckbox = document.getElementById('select-all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');

    // Select/Deselect all checkboxes
    selectAllCheckbox.addEventListener('change', function () {
        rowCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
    });

    // Confirmation for bulk delete action
    document.getElementById('bulk-action-form').addEventListener('submit', function (e) {
        const action = document.getElementById('bulk-action-select').value;
        if (action === 'delete' && !confirm('<?php echo esc_js__('Tem certeza que deseja apagar os itens selecionados?', 'sz-conectar-idb'); ?>')) {
            e.preventDefault();
        }
    });
});
</script>
