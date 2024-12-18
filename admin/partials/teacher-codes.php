<?php

/**
 * Partials for the "Códigos para o Professor" admin page.
 *
 * @package    Sz_Conectar_IDB
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

// Handle form submission for adding a new teacher code.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sz_add_teacher_code_nonce']) && wp_verify_nonce($_POST['sz_add_teacher_code_nonce'], 'sz_add_teacher_code')) {
    $school_name = sanitize_text_field($_POST['school_name']);
    $access_code = sanitize_text_field($_POST['access_code']);
    $max_uses = intval($_POST['max_uses']);
    $valid_until = !empty($_POST['valid_until']) ? sanitize_text_field($_POST['valid_until']) : null;

    if (!empty($access_code)) {
        $wpdb->insert(
            $table_name,
            array(
                'school_name'   => $school_name,
                'access_code'   => $access_code,
                'max_uses'      => $max_uses,
                'valid_until'   => $valid_until,
                'current_uses'  => 0,
                'is_active'     => 1,
            )
        );
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Teacher code added successfully.', 'sz-conectar-idb') . '</p></div>';
    } else {
        echo '<div class="notice notice-error is-dismissible"><p>' . __('Please enter a valid teacher code.', 'sz-conectar-idb') . '</p></div>';
    }
}

// Retrieve existing teacher codes.
$teacher_codes = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC");
?>

<div class="wrap">
    <h1><?php echo esc_html(__('Códigos para o Professor', 'sz-conectar-idb')); ?></h1>

    <!-- Form to Add New Code -->
    <form method="post" action="">
        <?php wp_nonce_field('sz_add_teacher_code', 'sz_add_teacher_code_nonce'); ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="school_name"><?php echo esc_html(__('School Name', 'sz-conectar-idb')); ?></label></th>
                <td><input type="text" id="school_name" name="school_name" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="access_code"><?php echo esc_html(__('Access Code', 'sz-conectar-idb')); ?></label></th>
                <td><input type="text" id="access_code" name="access_code" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="max_uses"><?php echo esc_html(__('Max Uses', 'sz-conectar-idb')); ?></label></th>
                <td><input type="number" id="max_uses" name="max_uses" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="valid_until"><?php echo esc_html(__('Valid Until (Optional)', 'sz-conectar-idb')); ?></label></th>
                <td><input type="date" id="valid_until" name="valid_until" class="regular-text"></td>
            </tr>
        </table>
        <?php submit_button(__('Add Code', 'sz-conectar-idb')); ?>
    </form>

    <!-- Existing Codes Table -->
    <h2><?php echo esc_html(__('Existing Teacher Codes', 'sz-conectar-idb')); ?></h2>
    <table id="teacher-codes-table" class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
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
                        <td><?php echo esc_html($code->id); ?></td>
                        <td><?php echo esc_html($code->school_name); ?></td>
                        <td><?php echo esc_html($code->access_code); ?></td>
                        <td><?php echo esc_html($code->max_uses); ?></td>
                        <td><?php echo esc_html($code->current_uses); ?></td>
                        <td><?php echo esc_html($code->valid_until ? $code->valid_until : ''); ?></td>
                        <td><?php echo $code->is_active ? __('Yes', 'sz-conectar-idb') : __('No', 'sz-conectar-idb'); ?></td>
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

<script>
jQuery(document).ready(function($) {
    $('#teacher-codes-table').DataTable({
        "pageLength": 20,
        "order": [[0, "desc"]]
    });
});
</script>
