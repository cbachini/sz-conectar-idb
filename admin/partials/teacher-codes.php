<?php

/**
 * Partials for the "Códigos para o Professor" admin page.
 *
 * @package Sz_Conectar_IDB
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Check if the user has the right capability to access this page.
if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.', 'sz-conectar-idb'));
}

global $wpdb;
$table_name = $wpdb->prefix . 'access_codes';

// Retrieve existing teacher codes.
$teacher_codes = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC");
?>

<div class="wrap">
    <h1><?php echo esc_html(__('Códigos para o Professor', 'sz-conectar-idb')); ?></h1>

    <!-- Bulk Actions Dropdown -->
    <div style="margin-bottom: 10px;">
        <select id="bulk-action-menu">
            <option value=""><?php echo esc_html(__('Bulk Actions', 'sz-conectar-idb')); ?></option>
            <option value="activate"><?php echo esc_html(__('Tornar Ativo', 'sz-conectar-idb')); ?></option>
            <option value="deactivate"><?php echo esc_html(__('Tornar Inativo', 'sz-conectar-idb')); ?></option>
        </select>
        <button id="apply-bulk-action" class="button"><?php echo esc_html(__('Apply', 'sz-conectar-idb')); ?></button>
    </div>

    <!-- Existing Codes Table -->
    <table id="teacher-codes-table" class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><input type="checkbox" id="select-all"></th>
                <th><?php echo esc_html(__('ID', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('School Name', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Access Code', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Max Uses', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Current Uses', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Valid Until', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Active', 'sz-conectar-idb')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($teacher_codes)) : ?>
                <?php foreach ($teacher_codes as $code) : ?>
                    <tr>
                        <td><input type="checkbox" class="select-row" value="<?php echo esc_attr($code->id); ?>"></td>
                        <td><?php echo esc_html($code->id); ?></td>
                        <td><?php echo esc_html($code->school_name); ?></td>
                        <td><?php echo esc_html($code->access_code); ?></td>
                        <td><?php echo esc_html($code->max_uses); ?></td>
                        <td><?php echo esc_html($code->current_uses); ?></td>
                        <td><?php echo esc_html($code->valid_until ? $code->valid_until : '-'); ?></td>
                        <td><?php echo $code->is_active ? __('Yes', 'sz-conectar-idb') : __('No', 'sz-conectar-idb'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="8"><?php echo esc_html(__('No teacher codes found.', 'sz-conectar-idb')); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
jQuery(document).ready(function($) {
    // Initialize DataTables
    $('#teacher-codes-table').DataTable({
        "pageLength": 20,
        "order": [[1, "desc"]],
        "columnDefs": [{ "orderable": false, "targets": [0] }]
    });

    // Select/Deselect all rows
    $('#select-all').on('click', function() {
        $('.select-row').prop('checked', this.checked);
    });

    // Bulk action handling
    $('#apply-bulk-action').on('click', function() {
        var action = $('#bulk-action-menu').val();
        var selected = $('.select-row:checked').map(function() { return this.value; }).get();

        if (!action || selected.length === 0) {
            alert('<?php echo esc_js(__('Please select an action and at least one row.', 'sz-conectar-idb')); ?>');
            return;
        }

        $.post(ajaxurl, {
            action: 'apply_bulk_teacher_codes',
            bulk_action: action,
            selected_ids: selected,
            security: '<?php echo wp_create_nonce("bulk_teacher_codes_action"); ?>'
        }, function(response) {
            var res = JSON.parse(response);
            if (res.success) {
                alert(res.message);
                location.reload();
            } else {
                alert('<?php echo esc_js(__('An error occurred.', 'sz-conectar-idb')); ?>');
            }
        });
    });
});
</script>
