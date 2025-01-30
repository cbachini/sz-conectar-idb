<?php
/**
 * Partials for the "Códigos de Degustação" admin page.
 * @package Sz_Conectar_IDB
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

global $wpdb;
$table_name = $wpdb->prefix . 'sz_tasting_codes';

// Processa o formulário de adição de novo código
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_tasting_code'])) {
    $school_name = sanitize_text_field($_POST['school_name']);
    $access_code = sanitize_text_field($_POST['access_code']);
    $max_uses = intval($_POST['max_uses']);
    $valid_until = sanitize_text_field($_POST['valid_until']);

    // Validação no backend para evitar duplicidade
    $exists_in_access = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}sz_access_codes WHERE access_code = %s", 
        $access_code
    ));
    $exists_in_tasting = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}sz_tasting_codes WHERE access_code = %s", 
        $access_code
    ));

    if ($exists_in_access > 0 || $exists_in_tasting > 0) {
        wp_die(__('O código de acesso já existe. Escolha um código diferente.', 'sz-conectar-idb'));
    }

    if (!empty($valid_until) && strtotime($valid_until) < time()) {
        wp_die(__('A data de validade não pode estar no passado.', 'sz-conectar-idb'));
    }

    // Inserção segura no banco após validações
    if (!empty($school_name) && !empty($access_code)) {
        $wpdb->insert($table_name, [
            'school_name'   => $school_name,
            'access_code'   => $access_code,
            'max_uses'      => $max_uses,
            'current_uses'  => 0,
            'is_active'     => 1,
            'valid_until'   => $valid_until ? $valid_until : null,
        ]);
        wp_redirect($_SERVER['REQUEST_URI']);
        exit;
    }
}

// Processa ações em massa
if (isset($_POST['bulk_action_apply']) && !empty($_POST['selected_codes'])) {
    $action = sanitize_text_field($_POST['bulk_action']);
    $selected_ids = array_map('intval', $_POST['selected_codes']);

    if ($action === 'activate') {
        $wpdb->query("UPDATE $table_name SET is_active = 1 WHERE id IN (" . implode(',', $selected_ids) . ")");
    } elseif ($action === 'deactivate') {
        $wpdb->query("UPDATE $table_name SET is_active = 0 WHERE id IN (" . implode(',', $selected_ids) . ")");
    }
    wp_redirect($_SERVER['REQUEST_URI']);
    exit;
}

// Busca os códigos existentes
$codes = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC");
?>

<div class="wrap">
    <h1><?php echo esc_html(__('Códigos de Degustação', 'sz-conectar-idb')); ?></h1>

    <!-- Formulário de Adição -->
    <form method="post" id="tasting-code-form">
        <h2><?php _e('Adicionar Novo Código', 'sz-conectar-idb'); ?></h2>
        <table class="form-table">
            <tr>
                <th><?php _e('Nome da Escola', 'sz-conectar-idb'); ?></th>
                <td><input type="text" name="school_name" id="school_name" class="regular-text" required></td>
            </tr>
            <tr>
                <th><?php _e('Código de Acesso', 'sz-conectar-idb'); ?></th>
                <td><input type="text" name="access_code" id="access_code" class="regular-text" required></td>
            </tr>
            <tr>
                <th><?php _e('Máximo de Usos', 'sz-conectar-idb'); ?></th>
                <td><input type="number" name="max_uses" value="1" id="max_uses" class="small-text" required></td>
            </tr>
            <tr>
                <th><?php _e('Válido Até', 'sz-conectar-idb'); ?></th>
                <td><input type="date" name="valid_until" id="valid_until" class="regular-text"></td>
            </tr>
        </table>
        <?php submit_button(__('Adicionar Código', 'sz-conectar-idb'), 'primary', 'new_tasting_code'); ?>
    </form>
</div>

<!-- Scripts -->
<script>
    jQuery(document).ready(function ($) {
        $('#tasting-codes-table').DataTable({
            "order": [[0, "desc"]],
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
            }
        });

        $('#select-all').on('click', function () {
            $('input[name="selected_codes[]"]').prop('checked', this.checked);
        });
    });
</script>
