jQuery(document).ready(function ($) {
    $('#teacher-codes-table').DataTable({
        "pageLength": 20, // Paginação de 20 em 20
        "order": [[0, "asc"]], // Ordena pela primeira coluna (ID) em ordem crescente
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/Portuguese-Brasil.json"
        }
    });
});
