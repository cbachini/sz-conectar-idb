<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

global $wpdb;
$table_name = $wpdb->prefix . 'frases_acesso';

// Handle form submission to add a new phrase
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_access_phrase'])) {
    $group_id = intval($_POST['group_id']);
    $question = sanitize_text_field($_POST['question']);
    $answer   = sanitize_text_field($_POST['answer']);

    $wpdb->insert($table_name, [
        'grupo_id' => $group_id,
        'pergunta' => $question,
        'resposta' => $answer,
    ]);

    echo '<div class="notice notice-success is-dismissible"><p>Frase de acesso adicionada com sucesso!</p></div>';
}

// Fetch all phrases from the database
$phrases = $wpdb->get_results("SELECT * FROM $table_name");
?>

<div class="wrap">
    <h1><?php echo esc_html__('Gerenciamento de Frases de Acesso', 'sz-conectar-idb'); ?></h1>

    <!-- Formulário para adicionar novas frases -->
    <h2><?php echo esc_html__('Adicionar Nova Frase de Acesso', 'sz-conectar-idb'); ?></h2>
    <form method="post" id="add-access-phrase-form">
        <table class="form-table">
            <tr>
                <th scope="row"><?php echo esc_html__('ID do Grupo', 'sz-conectar-idb'); ?></th>
                <td><input type="number" name="group_id" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><?php echo esc_html__('Pergunta', 'sz-conectar-idb'); ?></th>
                <td><textarea name="question" class="large-text" rows="3" required></textarea></td>
            </tr>
            <tr>
                <th scope="row"><?php echo esc_html__('Resposta', 'sz-conectar-idb'); ?></th>
                <td><input type="text" name="answer" class="regular-text" required></td>
            </tr>
        </table>
        <?php submit_button(__('Adicionar Frase', 'sz-conectar-idb'), 'primary', 'new_access_phrase'); ?>
    </form>

    <hr>

    <!-- Tabela de frases existentes -->
    <h2><?php echo esc_html__('Frases de Acesso Existentes', 'sz-conectar-idb'); ?></h2>
    <table id="access-phrases-table" class="wp-list-table widefat fixed striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>ID do Grupo</th>
            <th>Pergunta</th>
            <th>Resposta</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($frases as $frase): ?>
            <tr>
                <td><?php echo esc_html($frase->id); ?></td>
                <td><?php echo esc_html($frase->grupo_id); ?></td>
                <td><?php echo esc_html($frase->pergunta); ?></td>
                <td><?php echo esc_html($frase->resposta); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<!-- DataTables Integration -->
<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#access-phrases-table').DataTable({
        responsive: true,
        pageLength: 25,
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
        }
    });
});
</script>
