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
    $school_name = sanitize_text_field($_POST['school_name']);
    $access_code = sanitize_text_field($_POST['teacher_code']);
    $max_uses = intval($_POST['max_uses']);
    $valid_until = !empty($_POST['valid_until']) ? sanitize_text_field($_POST['valid_until']) : null;

    if (!empty($access_code) && !empty($school_name)) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'access_codes';
        $wpdb->insert(
            $table_name,
            array(
                'school_name' => $school_name,
                'access_code' => $access_code,
                'max_uses' => $max_uses,
                'valid_until' => $valid_until,
                'current_uses' => 0,
                'is_active' => 1,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            )
        );
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Teacher code added successfully.', 'sz-conectar-idb') . '</p></div>';
    } else {
        echo '<div class="notice notice-error is-dismissible"><p>' . __('Please fill in all required fields.', 'sz-conectar-idb') . '</p></div>';
    }
}

// Retrieve existing teacher codes.
global $wpdb;
$table_name = $wpdb->prefix . 'access_codes';
$teacher_codes = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
?>

<div class="wrap">
    <h1><?php echo esc_html(__('Códigos para o Professor', 'sz-conectar-idb')); ?></h1>

    <h2><?php echo esc_html(__('Adicionar Novo Código', 'sz-conectar-idb')); ?></h2>
    <form method="post" action="">
        <?php wp_nonce_field('sz_add_teacher_code', 'sz_add_teacher_code_nonce'); ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="school_name"><?php echo esc_html(__('Nome da Escola', 'sz-conectar-idb')); ?></label>
                </th>
                <td>
                    <input type="text" id="school_name" name="school_name" class="regular-text" required>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="teacher_code"><?php echo esc_html(__('Código de Acesso', 'sz-conectar-idb')); ?></label>
                </th>
                <td>
                    <input type="text" id="teacher_code" name="teacher_code" class="regular-text" required>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="max_uses"><?php echo esc_html(__('Máximo de Usos', 'sz-conectar-idb')); ?></label>
                </th>
                <td>
                    <input type="number" id="max_uses" name="max_uses" class="regular-text" required>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="valid_until"><?php echo esc_html(__('Válido Até', 'sz-conectar-idb')); ?></label>
                </th>
                <td>
                    <input type="date" id="valid_until" name="valid_until" class="regular-text">
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
                <th><?php echo esc_html(__('Nome da Escola', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Código de Acesso', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Máximo de Usos', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Usos Atuais', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Válido Até', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Ativo', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Criado Em', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Atualizado Em', 'sz-conectar-idb')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($teacher_codes as $code): ?>
                <tr>
                    <td><?php echo esc_html($code->id); ?></td>
                    <td><?php echo esc_html($code->school_name); ?></td>
                    <td><?php echo esc_html($code->access_code); ?></td>
                    <td><?php echo esc_html($code->max_uses); ?></td>
                    <td><?php echo esc_html($code->current_uses); ?></td>
                    <td>
                        <?php 
                        echo !empty($code->valid_until) 
                            ? esc_html(date('d/m/Y', strtotime($code->valid_until))) 
                            : __('-', 'sz-conectar-idb'); 
                        ?>
                    </td>
                    <td><?php echo $code->is_active ? __('Sim', 'sz-conectar-idb') : __('Não', 'sz-conectar-idb'); ?></td>
                    <td><?php echo esc_html(date('d/m/Y H:i:s', strtotime($code->created_at))); ?></td>
                    <td><?php echo esc_html(date('d/m/Y H:i:s', strtotime($code->updated_at))); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
jQuery(document).ready(function($) {
    $('#teacher-codes-table').DataTable({
        "paging": true,
        "ordering": true,
        "searching": true,
        "order": [[0, "asc"]],
        "language": {
            "emptyTable": "<?php echo esc_js(__('Nenhum dado disponível na tabela', 'sz-conectar-idb')); ?>",
            "search": "<?php echo esc_js(__('Buscar:', 'sz-conectar-idb')); ?>",
            "lengthMenu": "<?php echo esc_js(__('Mostrar _MENU_ registros', 'sz-conectar-idb')); ?>",
            "paginate": {
                "previous": "<?php echo esc_js(__('Anterior', 'sz-conectar-idb')); ?>",
                "next": "<?php echo esc_js(__('Próximo', 'sz-conectar-idb')); ?>"
            }
        }
    });
});
</script>
