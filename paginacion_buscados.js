$(document).ready(function() {
    // Función para la búsqueda en tiempo real
    $('#searchInput').on('input', function() {
        // Obtener el texto de búsqueda y convertirlo a minúsculas
        var searchText = $(this).val().toLowerCase();
        // Iterar sobre cada fila de la tabla
        $('#toolTable tbody tr').each(function() {
            // Obtener el texto de la fila actual y convertirlo a minúsculas
            var rowText = $(this).text().toLowerCase();
            // Comprobar si el texto de la fila contiene el texto de búsqueda
            if (rowText.indexOf(searchText) === -1) {
                // Ocultar la fila si no coincide con el texto de búsqueda
                $(this).hide();
            } else {
                // Mostrar la fila si coincide con el texto de búsqueda
                $(this).show();
            }
        });
    });

    // Paginación
    var itemsPerPage = 5; // Número de elementos por página
    var currentPage = 0; // Página actual

    var rows = $('#toolTable tbody tr'); // Filas de la tabla
    var rowsCount = rows.length; // Número total de filas
    var pageCount = Math.ceil(rowsCount / itemsPerPage); // Número total de páginas

    // Mostrar la primera página
    showPage(0);

    // Función para mostrar una página específica
    function showPage(page) {
        var start = page * itemsPerPage; // Índice inicial de la página
        var end = start + itemsPerPage; // Índice final de la página

        rows.hide(); // Ocultar todas las filas
        rows.slice(start, end).show(); // Mostrar solo las filas de la página actual

        // Actualizar el estado de la página actual
        currentPage = page;
    }

    // Manejar el evento de clic en los botones de paginación
    $('#toolTable').after('<div id="pagination" class="mt-3"></div>');

    // Generar botones de paginación
    for (var i = 0; i < pageCount; i++) {
        $('#pagination').append('<button class="pageBtn btn btn-secondary mr-1">' + (i + 1) + '</button>');
    }

    // Asignar evento de clic a los botones de paginación
    $('#pagination').on('click', '.pageBtn', function() {
        var pageNum = $(this).text(); // Obtener el número de página desde el botón
        showPage(parseInt(pageNum) - 1); // Mostrar la página correspondiente
    });

    // Establecer el estado inicial de la paginación
    $('.pageBtn').eq(0).addClass('active');
});
