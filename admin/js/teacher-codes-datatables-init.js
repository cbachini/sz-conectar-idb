jQuery(document).ready(function ($) {
    var table = $('#teacher-codes-table').DataTable({
        ajax: {
            url: TeacherCodesAjax.ajax_url, // Definido no wp_localize_script
            type: 'POST',
            data: {
                action: 'fetch_teacher_codes', // Ação AJAX
                security: TeacherCodesAjax.nonce
            }
        },
        processing: true,
        serverSide: false, // Não há processamento no servidor neste exemplo
        columns: [
            { data: 'id' },
            { data: 'school_name' },
            { data: 'access_code' },
            { data: 'max_uses' },
            { data: 'current_uses' },
            { data: 'is_active', render: function (data) {
                return data == 1 ? 'Sim' : 'Não';
            }},
            { data: 'valid_until', render: function (data) {
                return data ? data : '—';
            }},
        ],
        order: [[0, 'asc']],
        pageLength: 20
    });

    // Ações em lote
    $('#bulk-action-apply').on('click', function () {
        var selected_ids = [];
        $('.row-checkbox:checked').each(function () {
            selected_ids.push($(this).val());
        });

        var bulk_action = $('#bulk-action-select').val();
        if (bulk_action && selected_ids.length > 0) {
            $.post(TeacherCodesAjax.ajax_url, {
                action: 'apply_bulk_teacher_codes',
                bulk_action: bulk_action,
                selected_ids: selected_ids,
                security: TeacherCodesAjax.nonce
            }, function (response) {
                if (response.success) {
                    alert(response.data);
                    table.ajax.reload(); // Recarrega os dados na tabela
                } else {
                    alert('Erro: ' + response.data);
                }
            });
        } else {
            alert('Selecione ao menos um item e uma ação.');
        }
    });
});
