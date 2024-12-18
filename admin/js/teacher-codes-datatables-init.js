jQuery(document).ready(function($) {
    $('#teacher-codes-table').DataTable({
        "paging": true,
        "ordering": true,
        "searching": true,
        "order": [[ 0, "desc" ]],
        "language": {
            "emptyTable": "<?php echo esc_js(__('Nenhum dado disponível na tabela', 'sz-conectar-idb')); ?>",
            "search": "<?php echo esc_js(__('Buscar', 'sz-conectar-idb')); ?>:",
            "lengthMenu": "<?php echo esc_js(__('Mostrar _MENU_ entradas', 'sz-conectar-idb')); ?>",
            "paginate": {
                "previous": "<?php echo esc_js(__('Anterior', 'sz-conectar-idb')); ?>",
                "next": "<?php echo esc_js(__('Próximo', 'sz-conectar-idb')); ?>"
            }
        }
    });
});

