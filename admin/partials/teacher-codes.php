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
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                ' . __('Teacher code added successfully.', 'sz-conectar-idb') . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    } else {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                ' . __('Please enter a valid teacher code.', 'sz-conectar-idb') . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
}

// Retrieve existing teacher codes.
global $wpdb;
$table_name = $wpdb->prefix . 'sz_teacher_codes';
$teacher_codes = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
?>

<div class="container mt-4">
    <h1 class="text-primary mb-4"><?php echo esc_html(__('Códigos para o Professor', 'sz-conectar-idb')); ?></h1>

    <!-- Formulário -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><?php echo esc_html(__('Adicionar Novo Código', 'sz-conectar-idb')); ?></h5>
        </div>
        <div class="card-body">
            <form method="post" action="">
                <?php wp_nonce_field('sz_add_teacher_code', 'sz_add_teacher_code_nonce'); ?>
                <div class="mb-3">
                    <label for="teacher_code" class="form-label"><?php echo esc_html(__('Código do Professor', 'sz-conectar-idb')); ?></label>
                    <input type="text" id="teacher_code" name="teacher_code" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success"><?php echo esc_html(__('Adicionar Código', 'sz-conectar-idb')); ?></button>
            </form>
        </div>
    </div>

    <!-- Tabela -->
    <div class="card">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><?php echo esc_html(__('Códigos Existentes', 'sz-conectar-idb')); ?></h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col"><?php echo esc_html(__('Código', 'sz-conectar-idb')); ?></th>
                        <th scope="col"><?php echo esc_html(__('Criado em', 'sz-conectar-idb')); ?></th>
                        <th scope="col"><?php echo esc_html(__('Atualizado em', 'sz-conectar-idb')); ?></th>
                        <th scope="col"><?php echo esc_html(__('Ações', 'sz-conectar-idb')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($teacher_codes)) : ?>
                        <?php foreach ($teacher_codes as $index => $code) : ?>
                            <tr>
                                <th scope="row"><?php echo esc_html($index + 1); ?></th>
                                <td><?php echo esc_html($code->code); ?></td>
                                <td><?php echo esc_html($code->created_at); ?></td>
                                <td><?php echo esc_html($code->updated_at); ?></td>
                                <td>
                                    <button class="btn btn-danger btn-sm" onclick="deleteTeacherCode(<?php echo esc_js($code->id); ?>)">
                                        <?php echo esc_html(__('Excluir', 'sz-conectar-idb')); ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="text-center"><?php echo esc_html(__('Nenhum código encontrado.', 'sz-conectar-idb')); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Script de Deleção -->
<script type="text/javascript">
    function deleteTeacherCode(id) {
        if (confirm('<?php echo esc_js(__('Tem certeza que deseja excluir este código?', 'sz-conectar-idb')); ?>')) {
            var data = {
                action: 'delete_teacher_code',
                id: id,
                security: '<?php echo wp_create_nonce('delete_teacher_code_nonce'); ?>'
            };

            jQuery.post(ajaxurl, data, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('<?php echo esc_js(__('Ocorreu um erro ao excluir o código.', 'sz-conectar-idb')); ?>');
                }
            });
        }
    }
</script>
