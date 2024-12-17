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
    $new_code = sanitize_text_field($_POST['tasting_code']);

    if (!empty($new_code)) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'sz_tasting_codes';
        $wpdb->insert(
            $table_name,
            array(
                'code' => $new_code,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            )
        );
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Tasting code added successfully.', 'sz-conectar-idb') . '</p></div>';
    } else {
        echo '<div class="notice notice-error is-dismissible"><p>' . __('Please enter a valid tasting code.', 'sz-conectar-idb') . '</p></div>';
    }
}

// Retrieve existing tasting codes.
global $wpdb;
$table_name = $wpdb->prefix . 'sz_tasting_codes';
$tasting_codes = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
?>

<div class="wrap">
    <h1><?php echo esc_html(__('Códigos de Degustação', 'sz-conectar-idb')); ?></h1>

    <form method="post" action="">
        <?php wp_nonce_field('sz_add_tasting_code', 'sz_add_tasting_code_nonce'); ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="tasting_code"><?php echo esc_html(__('New Tasting Code', 'sz-conectar-idb')); ?></label>
                </th>
                <td>
                    <input type="text" id="tasting_code" name="tasting_code" class="regular-text" required>
                </td>
            </tr>
        </table>
        
        <?php submit_button(__('Add Code', 'sz-conectar-idb')); ?>
    </form>

    <hr>
    <h2><?php echo esc_html(__('Existing Tasting Codes', 'sz-conectar-idb')); ?></h2>

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
            <?php if (!empty($tasting_codes)) : ?>
                <?php foreach ($tasting_codes as $index => $code) : ?>
                    <tr>
                        <td><?php echo esc_html($index + 1); ?></td>
                        <td><?php echo esc_html($code->code); ?></td>
                        <td><?php echo esc_html($code->created_at); ?></td>
                        <td><?php echo esc_html($code->updated_at); ?></td>
                        <td>
                            <a href="#" class="button button-secondary" onclick="deleteTastingCode(<?php echo esc_js($code->id); ?>)"><?php echo esc_html(__('Delete', 'sz-conectar-idb')); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5"><?php echo esc_html(__('No tasting codes found.', 'sz-conectar-idb')); ?></td>
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
