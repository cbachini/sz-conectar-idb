jQuery(document).ready(function ($) {
    var table = $('#teacher-codes-table').DataTable({
        ajax: {
            url: ajaxurl, // URL do admin-ajax.php do WordPress
            type: 'POST',
            data: { action: 'fetch_teacher_codes' } // Ação para buscar os dados
        },
        processing: true,
        serverSide: true,
        columns: [
            { data: 'id' },
            { data: 'school_name' },
            { data: 'access_code' },
            { data: 'max_uses' },
            { data: 'current_uses' },
            { data: 'is_active', render: function (data) {
                return data == 1 ? 'Sim' : 'Não';
            }},
            { data: 'valid_until' }
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
            $.post(ajaxurl, {
                action: 'apply_bulk_teacher_codes',
                bulk_action: bulk_action,
                selected_ids: selected_ids,
                security: $('#bulk-action-nonce').val()
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
