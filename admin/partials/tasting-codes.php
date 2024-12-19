<?php

/**
 * Partials for the "Códigos de Degustação" admin page.
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

// Handle form submission for adding a new tasting code.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sz_add_tasting_code_nonce']) && wp_verify_nonce($_POST['sz_add_tasting_code_nonce'], 'sz_add_tasting_code')) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sz_tasting_codes';

    $school_name = sanitize_text_field($_POST['school_name']);
    $access_code = sanitize_text_field($_POST['access_code']);
    $max_uses = intval($_POST['max_uses']);
    $valid_until = !empty($_POST['valid_until']) ? sanitize_text_field($_POST['valid_until']) : null;

    if (!empty($school_name) && !empty($access_code) && $max_uses > 0) {
        $wpdb->insert(
            $table_name,
            array(
                'school_name' => $school_name,
                'access_code' => $access_code,
                'max_uses' => $max_uses,
                'current_uses' => 0,
                'is_active' => 1,
                'valid_until' => $valid_until
            )
        );
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Tasting code added successfully.', 'sz-conectar-idb') . '</p></div>';
    } else {
        echo '<div class="notice notice-error is-dismissible"><p>' . __('Please fill in all required fields.', 'sz-conectar-idb') . '</p></div>';
    }
}

// Retrieve existing tasting codes.
global $wpdb;
$table_name = $wpdb->prefix . 'sz_tasting_codes';
$tasting_codes = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id ASC");
?>

<div class="wrap">
    <h1><?php echo esc_html(__('Códigos de Degustação', 'sz-conectar-idb')); ?></h1>

    <form method="post" action="">
        <?php wp_nonce_field('sz_add_tasting_code', 'sz_add_tasting_code_nonce'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label for="school_name"><?php echo esc_html(__('School Name', 'sz-conectar-idb')); ?></label>
                </th>
                <td>
                    <input type="text" id="school_name" name="school_name" class="regular-text" required>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="access_code"><?php echo esc_html(__('Access Code', 'sz-conectar-idb')); ?></label>
                </th>
                <td>
                    <input type="text" id="access_code" name="access_code" class="regular-text" required>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="max_uses"><?php echo esc_html(__('Maximum Uses', 'sz-conectar-idb')); ?></label>
                </th>
                <td>
                    <input type="number" id="max_uses" name="max_uses" class="small-text" min="1" required>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="valid_until"><?php echo esc_html(__('Valid Until (Optional)', 'sz-conectar-idb')); ?></label>
                </th>
                <td>
                    <input type="date" id="valid_until" name="valid_until" class="regular-text">
                </td>
            </tr>
        </table>
        <?php submit_button(__('Add Tasting Code', 'sz-conectar-idb')); ?>
    </form>

    <hr>

    <h2><?php echo esc_html(__('Existing Tasting Codes', 'sz-conectar-idb')); ?></h2>

    <table id="tasting-codes-table" class="display wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php echo esc_html(__('ID', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('School Name', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Access Code', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Maximum Uses', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Current Uses', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Active', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Valid Until', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Actions', 'sz-conectar-idb')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($tasting_codes)) : ?>
                <?php foreach ($tasting_codes as $code) : ?>
                    <tr>
                        <td><?php echo esc_html($code->id); ?></td>
                        <td><?php echo esc_html($code->school_name); ?></td>
                        <td><?php echo esc_html($code->access_code); ?></td>
                        <td><?php echo esc_html($code->max_uses); ?></td>
                        <td><?php echo esc_html($code->current_uses); ?></td>
                        <td><?php echo esc_html($code->is_active ? 'Yes' : 'No'); ?></td>
                        <td><?php echo esc_html($code->valid_until ? date_i18n(get_option('date_format'), strtotime($code->valid_until)) : '-'); ?></td>
                        <td>
                            <a href="#" class="button button-secondary" onclick="deleteTastingCode(<?php echo esc_js($code->id); ?>)">
                                <?php echo esc_html(__('Delete', 'sz-conectar-idb')); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="8"><?php echo esc_html(__('No tasting codes found.', 'sz-conectar-idb')); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    function deleteTastingCode(id) {
        if (confirm('<?php echo esc_js(__('Are you sure you want to delete this tasting code?', 'sz-conectar-idb')); ?>')) {
            var data = {
                action: 'delete_tasting_code',
                id: id,
                security: '<?php echo wp_create_nonce('delete_tasting_code_nonce'); ?>'
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
