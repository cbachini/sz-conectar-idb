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

?>

<div class="wrap">
    <h1><?php echo esc_html(__('Códigos para o Professor', 'sz-conectar-idb')); ?></h1>

    <!-- Formulário para adicionar novos códigos -->
    <h2><?php echo esc_html(__('Adicionar Novo Código', 'sz-conectar-idb')); ?></h2>
    <form id="add-teacher-code-form" method="post">
        <table class="form-table">
            <tr>
                <th scope="row"><label for="school_name"><?php echo esc_html(__('Nome da Escola', 'sz-conectar-idb')); ?></label></th>
                <td><input type="text" name="school_name" id="school_name" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="access_code"><?php echo esc_html(__('Código de Acesso', 'sz-conectar-idb')); ?></label></th>
                <td><input type="text" name="access_code" id="access_code" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="max_uses"><?php echo esc_html(__('Máximo de Usos', 'sz-conectar-idb')); ?></label></th>
                <td><input type="number" name="max_uses" id="max_uses" class="small-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="valid_until"><?php echo esc_html(__('Válido Até (Opcional)', 'sz-conectar-idb')); ?></label></th>
                <td><input type="date" name="valid_until" id="valid_until" class="regular-text"></td>
            </tr>
        </table>
        <p class="submit">
            <button type="button" id="add-teacher-code-btn" class="button button-primary"><?php echo esc_html(__('Adicionar Código', 'sz-conectar-idb')); ?></button>
        </p>
    </form>

    <!-- Filtro e Tabela -->
    <hr>
    <h2><?php echo esc_html(__('Códigos Existentes', 'sz-conectar-idb')); ?></h2>
    <div>
        <select id="bulk-action" class="button-secondary">
            <option value=""><?php echo esc_html(__('Ações com Selecionados', 'sz-conectar-idb')); ?></option>
            <option value="activate"><?php echo esc_html(__('Ativar', 'sz-conectar-idb')); ?></option>
            <option value="deactivate"><?php echo esc_html(__('Desativar', 'sz-conectar-idb')); ?></option>
        </select>
        <button type="button" id="apply-bulk-action" class="button"><?php echo esc_html(__('Aplicar', 'sz-conectar-idb')); ?></button>
    </div>

    <table id="teacher-codes-table" class="display wp-list-table widefat fixed striped" style="width:100%;">
        <thead>
            <tr>
                <th><input type="checkbox" id="select-all"></th>
                <th><?php echo esc_html(__('ID', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Nome da Escola', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Código de Acesso', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Máximo de Usos', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Usos Atuais', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Ativo', 'sz-conectar-idb')); ?></th>
                <th><?php echo esc_html(__('Válido Até', 'sz-conectar-idb')); ?></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
jQuery(document).ready(function($) {
    // Inicializa DataTables com AJAX
    var table = $('#teacher-codes-table').DataTable({
        ajax: {
            url: TeacherCodesAjax.ajax_url,
            type: 'POST',
            data: { action: 'fetch_teacher_codes', security: TeacherCodesAjax.nonce },
        },
        columns: [
            { data: 'id', render: function(data) { return `<input type="checkbox" class="row-checkbox" value="${data}">`; }},
            { data: 'id' },
            { data: 'school_name' },
            { data: 'access_code' },
            { data: 'max_uses' },
            { data: 'current_uses' },
            { data: 'is_active', render: function(data) { return data == 1 ? 'Sim' : 'Não'; }},
            { data: 'valid_until', render: function(data) { return data || '—'; }},
        ],
        columnDefs: [{ targets: 0, orderable: false }],
        order: [[1, 'asc']],
        pageLength: 10
    });

    // Adiciona novo código
    $('#add-teacher-code-btn').click(function() {
        var formData = {
            action: 'apply_bulk_teacher_codes',
            security: TeacherCodesAjax.nonce,
            new_code: {
                school_name: $('#school_name').val(),
                access_code: $('#access_code').val(),
                max_uses: $('#max_uses').val(),
                valid_until: $('#valid_until').val(),
            }
        };

        $.post(TeacherCodesAjax.ajax_url, formData, function(response) {
            if (response.success) {
                alert(response.data);
                table.ajax.reload();
                $('#add-teacher-code-form')[0].reset();
            } else {
                alert('Erro: ' + response.data);
            }
        });
    });

    // Aplica ação em lote
    $('#apply-bulk-action').click(function() {
        var action = $('#bulk-action').val();
        var selected = $('.row-checkbox:checked').map(function() { return this.value; }).get();

        if (!action || selected.length === 0) {
            alert('Selecione uma ação e pelo menos um item.');
            return;
        }

        $.post(TeacherCodesAjax.ajax_url, {
            action: 'apply_bulk_teacher_codes',
            security: TeacherCodesAjax.nonce,
            bulk_action: action,
            selected_ids: selected
        }, function(response) {
            if (response.success) {
                alert(response.data);
                table.ajax.reload();
            } else {
                alert('Erro: ' + response.data);
            }
        });
    });

    // Selecionar todos os checkboxes
    $('#select-all').click(function() {
        $('.row-checkbox').prop('checked', this.checked);
    });
});
</script>
