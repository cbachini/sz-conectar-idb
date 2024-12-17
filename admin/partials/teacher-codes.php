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
        $table_name = $wpdb->prefix . 'sz_teacher_codes';
        $wpdb->insert(
            $table_name,
            array(
                'code' => $new_code,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            )
        );
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Teacher code added successfully.', 'sz-conectar-idb') . '</p></div>';
    } else {
        echo '<div class="notice notice-error is-dismissible"><p>' . __('Please enter a valid teacher code.', 'sz-conectar-idb') . '</p></div>';
    }
}

// Retrieve existing teacher codes.
global $wpdb;
$table_name = $wpdb->prefix . 'sz_teacher_codes';
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

    <table class="widefat fixed">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col"><?php echo esc_html(__('Code', 'sz-conectar-idb')); ?></th>
                <th scope="col"><?php echo esc_html(__('Created At', 'sz-conectar-idb')); ?></th>
                <th scope="col"><?php echo esc_html(__('Updated At', 'sz-conectar-idb')); ?></th>
                <th scope="col"><?php echo esc_html(__('Actions', 'sz-conectar-idb')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($teacher_codes)) : ?>
                <?php foreach ($teacher_codes as $index => $code) : ?>
                    <tr>
                        <td><?php echo esc_html($index + 1); ?></td>
                        <td><?php echo esc_html($code->code); ?></td>
                        <td><?php echo esc_html($code->created_at); ?></td>
                        <td><?php echo esc_html($code->updated_at); ?></td>
                        <td>
                            <a href="#" class="button button-secondary" onclick="deleteTeacherCode(<?php echo esc_js($code->id); ?>)"><?php echo esc_html(__('Delete', 'sz-conectar-idb')); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5"><?php echo esc_html(__('No teacher codes found.', 'sz-conectar-idb')); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    function deleteTeacherCode(id) {
        if (confirm('<?php echo esc_js(__('Are you sure you want to delete this teacher code?', 'sz-conectar-idb')); ?>')) {
            var data = {
                action: 'delete_teacher_code',
                id: id,
                security: '<?php echo wp_create_nonce('delete_teacher_code_nonce'); ?>'
            };

            jQuery.post(ajaxurl, data, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('<?php echo esc_js(__('An error occurred while trying to delete the code.', 'sz-conectar-idb')); ?>');
                }
            });
        }
    }
</script>
