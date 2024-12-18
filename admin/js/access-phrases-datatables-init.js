jQuery(document).ready(function ($) {
    $('#access-phrases-table').DataTable({
        pageLength: 25, // Mostra 25 registros por página
        lengthMenu: [10, 25, 50, 100], // Opções de quantidade de registros
        order: [[0, 'asc']], // Ordenação padrão pela primeira coluna (ID)
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' // Tradução em português
        }
    });
});
