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

// Check if the user has the right capability to access this page.
if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.', 'sz-conectar-idb'));
}

// Handle form submission for adding a new teacher code.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sz_add_teacher_code_nonce']) && wp_verify_nonce($_POST['sz_add_teacher_code_nonce'], 'sz_add_teacher_code')) {
    $new_code = sanitize_text_field($_POST['teacher_code']);

    if (!empty($new_code)) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'access_codes';
        $wpdb->insert(
            $table_name,
            array(
                'school_name' => 'Professor', // Default value for identification
                'access_code' => $new_code,
                'max_uses'    => 1, // Default max uses
                'current_uses'=> 0,
                'is_active'   => 1,
                'created_at'  => current_time('mysql'),
                'updated_at'  => current_time('mysql')
            )
        );
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Teacher code added successfully.', 'sz-conectar-idb') . '</p></div>';
    } else {
        echo '<div class="notice notice-error is-dismissible"><p>' . __('Please enter a valid teacher code.', 'sz-conectar-idb') . '</p></div>';
    }
}

// Retrieve existing teacher codes.
global $wpdb;
$table_name = $wpdb->prefix . 'access_codes';
$teacher_codes = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
?>

<div class="wrap">
    <h1><?php echo esc_html(__('Códigos para o Professor', 'sz-conectar-idb')); ?></h1>

    <form method="post" action="">
        <?php wp_nonce_field('sz_add_teacher_code', 'sz_add_teacher_code_nonce'); ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="teacher_code"><?php echo esc_html(__('New Teacher Code', 'sz-conectar-idb')); ?></label>
                </th>
                <td>
                    <input type="text" id="teacher_code" name="teacher_code" class="regular-text" required>
                </td>
            </tr>
        </table>
        
        <?php submit_button(__('Add Code', 'sz-conectar-idb')); ?>
    </form>

    <hr>
    <h2><?php echo esc_html(__('Existing Teacher Codes', 'sz-conectar-idb')); ?></h2>

    <table id="teacher-codes-table" class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php echo esc_html(__('ID', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Code', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Max Uses', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Current Uses', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Active', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Created At', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Updated At', 'sz-conectar-idb')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($teacher_codes as $code): ?>
                <tr>
                    <td><?php echo esc_html($code->id); ?></td>
                    <td><?php echo esc_html($code->access_code); ?></td>
                    <td><?php echo esc_html($code->max_uses); ?></td>
                    <td><?php echo esc_html($code->current_uses); ?></td>
                    <td><?php echo esc_html($code->is_active ? 'Yes' : 'No'); ?></td>
                    <td><?php echo esc_html($code->created_at); ?></td>
                    <td><?php echo esc_html($code->updated_at); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('#teacher-codes-table').DataTable({
            "pageLength": 20, // Paginação
            "order": [[0, "asc"]], // Ordenar pela primeira coluna (ID)
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/Portuguese-Brasil.json"
            }
        });
    });
</script>
