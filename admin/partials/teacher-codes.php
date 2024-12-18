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

// Table name for storing access codes.
global $wpdb;
$table_name = $wpdb->prefix . 'access_codes';

// Handle form submission for adding a new teacher code.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_teacher_code_nonce']) && wp_verify_nonce($_POST['new_teacher_code_nonce'], 'add_teacher_code')) {
    $school_name = sanitize_text_field($_POST['school_name']);
    $teacher_code = sanitize_text_field($_POST['teacher_code']);
    $max_uses = intval($_POST['max_uses']);

    if (!empty($school_name) && !empty($teacher_code) && $max_uses > 0) {
        $wpdb->insert($table_name, [
            'school_name'   => $school_name,
            'access_code'   => $teacher_code,
            'max_uses'      => $max_uses,
            'current_uses'  => 0,
            'is_active'     => 1
        ]);

        echo '<div class="notice notice-success is-dismissible"><p>' . __('Teacher code added successfully!', 'sz-conectar-idb') . '</p></div>';
    } else {
        echo '<div class="notice notice-error is-dismissible"><p>' . __('Please fill in all fields correctly.', 'sz-conectar-idb') . '</p></div>';
    }
}

// Fetch existing teacher codes from access_codes table.
$teacher_codes = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC");

?>

<div class="wrap">
    <h1><?php echo esc_html(__('Códigos para o Professor', 'sz-conectar-idb')); ?></h1>

    <h2><?php echo esc_html(__('Adicionar Novo Código para o Professor', 'sz-conectar-idb')); ?></h2>
    <form method="post" action="">
        <?php wp_nonce_field('add_teacher_code', 'new_teacher_code_nonce'); ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="school_name"><?php echo esc_html(__('Nome da Escola', 'sz-conectar-idb')); ?></label></th>
                <td><input type="text" id="school_name" name="school_name" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="teacher_code"><?php echo esc_html(__('Código do Professor', 'sz-conectar-idb')); ?></label></th>
                <td><input type="text" id="teacher_code" name="teacher_code" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="max_uses"><?php echo esc_html(__('Máximo de Usos', 'sz-conectar-idb')); ?></label></th>
                <td><input type="number" id="max_uses" name="max_uses" class="regular-text" required></td>
            </tr>
        </table>
        <?php submit_button(__('Adicionar Código', 'sz-conectar-idb')); ?>
    </form>

    <hr>

    <h2><?php echo esc_html(__('Códigos para o Professor Existentes', 'sz-conectar-idb')); ?></h2>
    <table id="teacher-codes-table" class="wp-list-table widefat fixed striped">
    <thead>
        <tr>
            <th>ID</th>
            <th><?php echo esc_html(__('Code', 'sz-conectar-idb')); ?></th>
            <th><?php echo esc_html(__('Created At', 'sz-conectar-idb')); ?></th>
            <th><?php echo esc_html(__('Updated At', 'sz-conectar-idb')); ?></th>
            <th><?php echo esc_html(__('Actions', 'sz-conectar-idb')); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($teacher_codes as $index => $code): ?>
            <tr>
                <td><?php echo esc_html($code->id); ?></td>
                <td><?php echo esc_html($code->code); ?></td>
                <td><?php echo esc_html($code->created_at); ?></td>
                <td><?php echo esc_html($code->updated_at); ?></td>
                <td>
                    <a href="#" class="button button-secondary" onclick="deleteTeacherCode(<?php echo esc_js($code->id); ?>)">
                        <?php echo esc_html(__('Delete', 'sz-conectar-idb')); ?>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</div>

<script type="text/javascript">
    function deleteTeacherCode(id) {
        if (confirm('<?php echo esc_js(__('Tem certeza de que deseja deletar este código?', 'sz-conectar-idb')); ?>')) {
            jQuery.post(ajaxurl, {
                action: 'delete_teacher_code',
                id: id,
                security: '<?php echo wp_create_nonce('delete_teacher_code_nonce'); ?>'
            }, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('<?php echo esc_js(__('Ocorreu um erro ao deletar o código.', 'sz-conectar-idb')); ?>');
                }
            });
        }
    }
</script>
