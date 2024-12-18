<?php
/**
 * Partials for the "Códigos para o Professor" admin page.
 *
 * @package    Sz_Conectar_IDB
 * @subpackage Sz_Conectar_IDB/admin/partials
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

global $wpdb;
$table_name = $wpdb->prefix . 'access_codes';

// Processa o formulário de adição de novo código
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_teacher_code'])) {
    $school_name = sanitize_text_field($_POST['school_name']);
    $access_code = sanitize_text_field($_POST['access_code']);
    $max_uses = intval($_POST['max_uses']);
    $valid_until = sanitize_text_field($_POST['valid_until']);

    if (!empty($school_name) && !empty($access_code)) {
        $wpdb->insert($table_name, [
            'school_name'   => $school_name,
            'access_code'   => $access_code,
            'max_uses'      => $max_uses,
            'current_uses'  => 0,
            'is_active'     => 1,
            'valid_until'   => $valid_until ? $valid_until : null,
        ]);
        wp_redirect($_SERVER['REQUEST_URI']);
        exit;
    }
}

// Processa ações em lote
if (isset($_POST['bulk_action_apply']) && !empty($_POST['selected_codes'])) {
    $action = sanitize_text_field($_POST['bulk_action']);
    $selected_ids = array_map('intval', $_POST['selected_codes']);

    if ($action === 'activate') {
        $wpdb->query("UPDATE $table_name SET is_active = 1 WHERE id IN (" . implode(',', $selected_ids) . ")");
    } elseif ($action === 'deactivate') {
        $wpdb->query("UPDATE $table_name SET is_active = 0 WHERE id IN (" . implode(',', $selected_ids) . ")");
    }
    wp_redirect($_SERVER['REQUEST_URI']);
    exit;
}

// Busca os códigos existentes
$codes = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC");
?>

<div class="wrap">
    <h1><?php echo esc_html(__('Códigos para o Professor', 'sz-conectar-idb')); ?></h1>

    <!-- Formulário de Adição -->
    <form method="post" action="">
        <h2><?php _e('Adicionar Novo Código', 'sz-conectar-idb'); ?></h2>
        <table class="form-table">
            <tr>
                <th><?php _e('Nome da Escola', 'sz-conectar-idb'); ?></th>
                <td><input type="text" name="school_name" class="regular-text" required></td>
            </tr>
            <tr>
                <th><?php _e('Código de Acesso', 'sz-conectar-idb'); ?></th>
                <td><input type="text" name="access_code" class="regular-text" required></td>
            </tr>
            <tr>
                <th><?php _e('Máximo de Usos', 'sz-conectar-idb'); ?></th>
                <td><input type="number" name="max_uses" value="1" class="small-text"></td>
            </tr>
            <tr>
                <th><?php _e('Válido Até', 'sz-conectar-idb'); ?></th>
                <td><input type="date" name="valid_until" class="regular-text"></td>
            </tr>
        </table>
        <?php submit_button(__('Adicionar Código', 'sz-conectar-idb'), 'primary', 'new_teacher_code'); ?>
    </form>

    <!-- Ações em Lote -->
    <form method="post" action="">
        <h2><?php _e('Códigos Existentes', 'sz-conectar-idb'); ?></h2>
        <div style="margin-bottom: 10px;">
            <select name="bulk_action" id="bulk-action-select">
                <option value=""><?php _e('Ações com Selecionados', 'sz-conectar-idb'); ?></option>
                <option value="activate"><?php _e('Ativar', 'sz-conectar-idb'); ?></option>
                <option value="deactivate"><?php _e('Desativar', 'sz-conectar-idb'); ?></option>
            </select>
            <button type="submit" name="bulk_action_apply" class="button"><?php _e('Aplicar', 'sz-conectar-idb'); ?></button>
        </div>

        <!-- Tabela -->
        <table id="teacher-codes-table" class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width: 2%;"><input type="checkbox" id="select-all"></th>
                    <th><?php _e('ID', 'sz-conectar-idb'); ?></th>
                    <th><?php _e('Nome da Escola', 'sz-conectar-idb'); ?></th>
                    <th><?php _e('Código de Acesso', 'sz-conectar-idb'); ?></th>
                    <th><?php _e('Máximo de Usos', 'sz-conectar-idb'); ?></th>
                    <th><?php _e('Usos Atuais', 'sz-conectar-idb'); ?></th>
                    <th><?php _e('Ativo', 'sz-conectar-idb'); ?></th>
                    <th><?php _e('Válido Até', 'sz-conectar-idb'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($codes as $code) : ?>
                    <tr>
                        <td><input type="checkbox" name="selected_codes[]" value="<?php echo esc_attr($code->id); ?>"></td>
                        <td><?php echo esc_html($code->id); ?></td>
                        <td><?php echo esc_html($code->school_name); ?></td>
                        <td><?php echo esc_html($code->access_code); ?></td>
                        <td><?php echo esc_html($code->max_uses); ?></td>
                        <td><?php echo esc_html($code->current_uses); ?></td>
                        <td><?php echo $code->is_active ? __('Sim', 'sz-conectar-idb') : __('Não', 'sz-conectar-idb'); ?></td>
                        <td><?php echo $code->valid_until ? esc_html($code->valid_until) : '—'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>
</div>

<!-- Scripts -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />

<script>
    jQuery(document).ready(function ($) {
        $('#teacher-codes-table').DataTable({
            "order": [[0, "desc"]],
            "pageLength": 20
        });

        $('#select-all').on('click', function () {
            $('input[name="selected_codes[]"]').prop('checked', this.checked);
        });
    });
</script>
