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

// Handle form submission for adding a new teacher code.
global $wpdb;
$table_name = $wpdb->prefix . 'access_codes';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sz_add_teacher_code_nonce']) && wp_verify_nonce($_POST['sz_add_teacher_code_nonce'], 'sz_add_teacher_code')) {
    $new_code = sanitize_text_field($_POST['teacher_code']);
    $max_uses = intval($_POST['max_uses']);

    if (!empty($new_code) && $max_uses > 0) {
        $wpdb->insert(
            $table_name,
            array(
                'school_name'  => 'Teacher Code',
                'access_code'  => $new_code,
                'max_uses'     => $max_uses,
                'current_uses' => 0,
                'is_active'    => 1,
            )
        );
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Teacher code added successfully.', 'sz-conectar-idb') . '</p></div>';
    } else {
        echo '<div class="notice notice-error is-dismissible"><p>' . __('Please enter a valid code and max uses.', 'sz-conectar-idb') . '</p></div>';
    }
}

// Retrieve existing teacher codes.
$teacher_codes = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
?>

<div class="wrap">
    <h1><?php echo esc_html(__('Códigos para o Professor', 'sz-conectar-idb')); ?></h1>

    <!-- Formulário para adicionar novo código -->
    <h2><?php echo esc_html(__('Add New Teacher Code', 'sz-conectar-idb')); ?></h2>
    <form method="post" action="">
        <?php wp_nonce_field('sz_add_teacher_code', 'sz_add_teacher_code_nonce'); ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="teacher_code"><?php echo esc_html(__('Code', 'sz-conectar-idb')); ?></label>
                </th>
                <td>
                    <input type="text" id="teacher_code" name="teacher_code" class="regular-text" required>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="max_uses"><?php echo esc_html(__('Max Uses', 'sz-conectar-idb')); ?></label>
                </th>
                <td>
                    <input type="number" id="max_uses" name="max_uses" class="regular-text" min="1" required>
                </td>
            </tr>
        </table>
        <?php submit_button(__('Add Code', 'sz-conectar-idb')); ?>
    </form>

    <!-- Tabela DataTables para exibir os códigos -->
    <h2><?php echo esc_html(__('Existing Teacher Codes', 'sz-conectar-idb')); ?></h2>
    <table id="teacher-codes-table" class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Max Uses</th>
                <th>Current Uses</th>
                <th>Active</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($teacher_codes)) : ?>
                <?php foreach ($teacher_codes as $code) : ?>
                    <tr>
                        <td><?php echo esc_html($code->id); ?></td>
                        <td><?php echo esc_html($code->access_code); ?></td>
                        <td><?php echo esc_html($code->max_uses); ?></td>
                        <td><?php echo esc_html($code->current_uses); ?></td>
                        <td><?php echo $code->is_active ? 'Yes' : 'No'; ?></td>
                        <td><?php echo esc_html($code->created_at); ?></td>
                        <td><?php echo esc_html($code->updated_at); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="7"><?php echo esc_html(__('No teacher codes found.', 'sz-conectar-idb')); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Script para inicializar o DataTables -->
<script>
jQuery(document).ready(function($) {
    $('#teacher-codes-table').DataTable({
        "paging": true,
        "ordering": true,
        "searching": true,
        "order": [[ 0, "asc" ]],
        "language": {
            "emptyTable": "<?php echo esc_js(__('No data available in table', 'sz-conectar-idb')); ?>",
            "search": "<?php echo esc_js(__('Search', 'sz-conectar-idb')); ?>:",
            "lengthMenu": "<?php echo esc_js(__('Show _MENU_ entries', 'sz-conectar-idb')); ?>",
            "paginate": {
                "previous": "<?php echo esc_js(__('Previous', 'sz-conectar-idb')); ?>",
                "next": "<?php echo esc_js(__('Next', 'sz-conectar-idb')); ?>"
            }
        }
    });
});
</script>
