<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['id_usuarios'])) {
    header("Location: index.php");
    exit();
}
include 'conexion.php';

// Obtener los nombres de las tablas
$tablas = [
    "formulario_turnos_uci_enfermeros",
    "formulario_turnos_uci_tens",
    "formulario_turnos_uci_kinesiologos",
    "formulario_turnos_upc_medicos",
    "formulario_turnos_uti_enfermeros",
    "formulario_turnos_uti_kinesiologos",
    "formulario_turnos_uti_tens",
    "formulario_turnos_im_tecnologos_medicos"
];

include 'navbar_calidad.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualización de Formularios</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Montserrat:wght@700&display=swap');

        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4f8;
        }

        .container {
            width: 100%;
            margin: 30px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        h1 {
            text-align: center;
            color: #333;
            font-family: 'Montserrat', sans-serif;
            font-size: 2.5em;
        }

        h2 {
            text-align: center;
            color: #555;
        }

        hr {
            border: 1px solid #e0e0e0;
        }

        .selector-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .custom-select,
        .form-control,
        .btn {
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border: 1px solid #e0e0e0;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 10px;
        }

        .pagination button {
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f4f4f4;
            cursor: pointer;
            color: #007bff;
            transition: all 0.3s ease;
        }

        .pagination button.active {
            background-color: #007bff;
            color: white;
        }

        .pagination button:hover {
            background-color: #0056b3;
            color: white;
        }

        .pagination button.active:hover {
            background-color: #0056b3;
        }

        .search-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
        }

        .search-container .form-control {
            width: 200px;
        }

        .search-container .btn {
            background-color: #007bff;
            border: none;
            color: white;
        }

        .search-container .btn:hover {
            background-color: #0056b3;
        }

        .logo {
            display: block;
            margin-bottom: 20px;
            max-width: 100px;
        }
    </style>
</head>

<body>
    <div class="container">
        <img src="imagen/logohsf02.jpg" alt="Logo HSF" class="logo float-left">
        <h1>Formularios Registrados</h1>
        <hr>
        <h2 id="selectedTableTitle"></h2>
        <div class="selector-container">
            <select id="tableSelector" class="custom-select">
                <option value="">--Seleccione--</option>
                <?php foreach ($tablas as $tabla) { ?>
                    <option value="<?php echo $tabla; ?>"><?php echo str_replace('_', ' ', ucwords($tabla)); ?></option>
                <?php } ?>
            </select>
            <button class="btn btn-primary" onclick="loadTable()">Cargar Tabla</button>
            <form action="generar_excel.php" method="post" style="display: inline-block;">
                <input type="hidden" name="table" id="selectedTable">
                <button type="submit" class="btn btn-success">Generar Excel</button>
            </form>
        </div>

        <div class="search-container">
            <input type="text" id="searchText" class="form-control" placeholder="Buscar registros..." oninput="loadTable()">
            <input type="date" id="startDate" class="form-control" onchange="loadTable()">
            <input type="date" id="endDate" class="form-control" onchange="loadTable()">
            <button class="btn btn-danger" onclick="loadTable()">Buscar</button>
        </div>

        <div class="table-container" id="tableContainer">
            <table id="dataTable" class="table table-striped">
                <thead>
                    <tr id="tableHeader"></tr>
                </thead>
                <tbody id="tableBody"></tbody>
            </table>
        </div>

        <div class="pagination" id="pagination"></div>
    </div>

    <script>
        let currentPage = 1;
        const rowsPerPage = 10;

        function loadTable() {
            const tableSelector = document.getElementById('tableSelector').value;
            const searchText = document.getElementById('searchText').value;
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const tableHeader = document.getElementById('tableHeader');
            const tableBody = document.getElementById('tableBody');
            const pagination = document.getElementById('pagination');
            const selectedTableTitle = document.getElementById('selectedTableTitle');

            document.getElementById('selectedTable').value = tableSelector;

            if (tableSelector) {
                selectedTableTitle.textContent = "Visualizando: " + tableSelector.replace(/_/g, ' ').toUpperCase();
            } else {
                selectedTableTitle.textContent = "";
            }

            if (tableSelector === "") return;

            fetch(`cargar_tabla.php?table=${tableSelector}&search=${searchText}&startDate=${startDate}&endDate=${endDate}`)
                .then(response => response.json())
                .then(data => {
                    tableHeader.innerHTML = '';
                    tableBody.innerHTML = '';
                    currentPage = 1;

                    if (data.columns.length > 0) {
                        data.columns.forEach(column => {
                            const th = document.createElement('th');
                            th.textContent = column;
                            tableHeader.appendChild(th);
                        });
                    }

                    if (data.rows.length > 0) {
                        data.rows.forEach((row, index) => {
                            const tr = document.createElement('tr');
                            row.forEach(cell => {
                                const td = document.createElement('td');
                                td.textContent = cell;
                                tr.appendChild(td);
                            });

                            // Añadir botón para generar PDF
                            const td = document.createElement('td');
                            const generatePdfButton = document.createElement('button');
                            generatePdfButton.textContent = "Generar PDF";
                            generatePdfButton.classList.add('btn', 'btn-outline-primary');
                            generatePdfButton.onclick = function() {
                                generarPdf(row, data.columns, tableSelector);
                            };
                            td.appendChild(generatePdfButton);
                            tr.appendChild(td);

                            tableBody.appendChild(tr);
                        });

                        updatePagination();
                    }
                })
                .catch(error => {
                    console.error('Error al cargar la tabla:', error);
                });
        }

        function updatePagination() {
            const tableBody = document.getElementById('tableBody');
            const rows = tableBody.getElementsByTagName('tr');
            const totalPages = Math.ceil(rows.length / rowsPerPage);
            const pagination = document.getElementById('pagination');

            pagination.innerHTML = '';

            for (let i = 1; i <= totalPages; i++) {
                const button = document.createElement('button');
                button.textContent = i;
                button.classList.add('btn', 'btn-light');

                if (i === currentPage) {
                    button.classList.add('active');
                }

                button.addEventListener('click', () => {
                    currentPage = i;
                    displayRows();
                    updatePagination();
                });
                pagination.appendChild(button);
            }

            displayRows();
        }

        function displayRows() {
            const tableBody = document.getElementById('tableBody');
            const rows = tableBody.getElementsByTagName('tr');
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            for (let i = 0; i < rows.length; i++) {
                if (i >= start && i < end) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }

        function generarPdf(rowData, columns, tableName) {
            fetch('generar_pdf.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        table: tableName,
                        columns: columns,
                        data: rowData
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.blob();
                })
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = 'registro.pdf';
                    link.click();
                })
                .catch(error => console.error('Error al generar el PDF:', error));
        }
    </script>
</body>

</html>