<?php

/**
 * Partials for the "Frases de Acesso" admin page.
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

// Handle form submission for adding a new access phrase.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sz_add_access_phrase_nonce']) && wp_verify_nonce($_POST['sz_add_access_phrase_nonce'], 'sz_add_access_phrase')) {
    $new_phrase = sanitize_text_field($_POST['access_phrase']);

    if (!empty($new_phrase)) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'sz_access_phrases';
        $wpdb->insert(
            $table_name,
            array(
                'phrase' => $new_phrase,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            )
        );
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Access phrase added successfully.', 'sz-conectar-idb') . '</p></div>';
    } else {
        echo '<div class="notice notice-error is-dismissible"><p>' . __('Please enter a valid access phrase.', 'sz-conectar-idb') . '</p></div>';
    }
}

// Retrieve existing access phrases.
global $wpdb;
$table_name = $wpdb->prefix . 'sz_access_phrases';
$access_phrases = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
?>

<div class="wrap">
    <h1 class="mb-4"><?php echo esc_html(__('Frases de Acesso', 'sz-conectar-idb')); ?></h1>

    <!-- Formulário para adicionar frases -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title"><?php echo esc_html(__('Add New Access Phrase', 'sz-conectar-idb')); ?></h5>
        </div>
        <div class="card-body">
            <form method="post" action="">
                <?php wp_nonce_field('sz_add_access_phrase', 'sz_add_access_phrase_nonce'); ?>
                <div class="mb-3">
                    <label for="access_phrase" class="form-label"><?php echo esc_html(__('New Access Phrase', 'sz-conectar-idb')); ?></label>
                    <input type="text" id="access_phrase" name="access_phrase" class="form-control" placeholder="Enter a new access phrase" required>
                </div>
                <button type="submit" class="btn btn-primary"><?php echo esc_html(__('Add Phrase', 'sz-conectar-idb')); ?></button>
            </form>
        </div>
    </div>

    <!-- Listagem de frases existentes -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title"><?php echo esc_html(__('Existing Access Phrases', 'sz-conectar-idb')); ?></h5>
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col"><?php echo esc_html(__('Phrase', 'sz-conectar-idb')); ?></th>
                        <th scope="col"><?php echo esc_html(__('Created At', 'sz-conectar-idb')); ?></th>
                        <th scope="col"><?php echo esc_html(__('Updated At', 'sz-conectar-idb')); ?></th>
                        <th scope="col"><?php echo esc_html(__('Actions', 'sz-conectar-idb')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($access_phrases)) : ?>
                        <?php foreach ($access_phrases as $index => $phrase) : ?>
                            <tr>
                                <th scope="row"><?php echo esc_html($index + 1); ?></th>
                                <td><?php echo esc_html($phrase->phrase); ?></td>
                                <td><?php echo esc_html($phrase->created_at); ?></td>
                                <td><?php echo esc_html($phrase->updated_at); ?></td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteAccessPhrase(<?php echo esc_js($phrase->id); ?>)">
                                        <?php echo esc_html(__('Delete', 'sz-conectar-idb')); ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="text-center"><?php echo esc_html(__('No access phrases found.', 'sz-conectar-idb')); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Script para exclusão via AJAX -->
<script type="text/javascript">
    function deleteAccessPhrase(id) {
        if (confirm('<?php echo esc_js(__('Are you sure you want to delete this access phrase?', 'sz-conectar-idb')); ?>')) {
            var data = {
                action: 'delete_access_phrase',
                id: id,
                security: '<?php echo wp_create_nonce('delete_access_phrase_nonce'); ?>'
            };

            jQuery.post(ajaxurl, data, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data || '<?php echo esc_js(__('An error occurred while trying to delete the phrase.', 'sz-conectar-idb')); ?>');
                }
            });
        }
    }
</script>
