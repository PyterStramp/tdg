document.addEventListener("DOMContentLoaded", function () {

    const tableA = document.getElementById('tableA');
    const tableB = document.getElementById('tableB');

    // Función para resaltar resultados de aprendizaje en la tabla B
    function highlightRAInTableB(raId) {
        const rowsB = tableB.querySelectorAll('tbody tr');
        rowsB.forEach(row => {
            const raCell = row.cells[0];
            if (raCell && raCell.textContent === raId) {
                row.classList.add('highlight-ra');
            } else {
                row.classList.remove('highlight-ra');
            }
        });
    }

    // Función para resaltar la columna en la tabla A
    function highlightColumnInTableA(raId) {
        const rowsA = tableA.querySelectorAll('tbody tr');
        rowsA.forEach(row => {
            const cells = row.cells;
            for (let i = 2; i < cells.length; i++) { // Las columnas de RA empiezan en la 3ra columna (índice 2)
                if (cells[i].textContent === raId) {
                    cells[i].classList.add('highlight');
                } else {
                    cells[i].classList.remove('highlight');
                }
            }
        });
    }

    // Añadir el evento de click a las filas de la tabla B
    tableB.addEventListener('click', function (event) {
        const clickedRow = event.target.closest('tr');
        if (clickedRow) {
            const raId = clickedRow.cells[0].textContent;
            highlightRAInTableB(raId);
            highlightColumnInTableA(raId);
        }
    });

    // Añadir el evento de click a las celdas de la tabla A para seleccionar RA
    tableA.addEventListener('click', function (event) {
        const clickedCell = event.target.closest('td');
        if (clickedCell && clickedCell !== clickedCell.parentNode.cells[0]) {
            const raId = clickedCell.textContent;
            highlightRAInTableB(raId);
            highlightColumnInTableA(raId);
        }
    });

});
