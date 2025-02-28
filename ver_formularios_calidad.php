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
    "formulario_turnos_uti_tens"
];

// Función para obtener las columnas de una tabla
function obtenerColumnas($conexion, $tabla)
{
    $query = "DESCRIBE $tabla";
    $resultado = mysqli_query($conexion, $query);
    $columnas = [];
    while ($columna = mysqli_fetch_assoc($resultado)) {
        $columnas[] = $columna['Field'];
    }
    return $columnas;
}

// Función para obtener las filas de una tabla con filtros
function obtenerFilas($conexion, $tabla, $search = '', $startDate = '', $endDate = '')
{
    $query = "SELECT * FROM $tabla";
    $conditions = [];

    if ($search) {
        $conditions[] = "CONCAT_WS(' ', " . implode(", ", obtenerColumnas($conexion, $tabla)) . ") LIKE '%$search%'";
    }

    if ($startDate && $endDate) {
        $conditions[] = "fecha BETWEEN '$startDate' AND '$endDate'";
    }

    if (count($conditions) > 0) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $resultado = mysqli_query($conexion, $query);
    $filas = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $filas[] = $fila;
    }
    return $filas;
}

include 'navbar_calidad.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualización de Formularios</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Estilos generales */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4f8;
        }

        .container {
            width: 85%;
            margin: 0 auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        h1,
        h2 {
            text-align: center;
            color: #333;
        }

        hr {
            border: 1px solid #e0e0e0;
        }

        /* Estilos del selector y formulario */
        .selector-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        select,
        input[type="text"],
        input[type="date"],
        button {
            padding: 12px 18px;
            font-size: 16px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        select,
        input[type="text"],
        input[type="date"] {
            width: 250px;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Estilos de la tabla */
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

        /* Paginación */
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
            color: #007bff; /* Cambia el color del texto de los botones */
            transition: all 0.3s ease;
        }

        .pagination button.active {
            background-color:rgb(0, 255, 221);
            color: white;
        }

        .pagination button:hover {
            background-color: #ddd;
        }

        .pagination button.active:hover {
            background-color: #0056b3;
        }

        /* Barra de búsqueda */
        .search-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
        }

        .search-container input[type="text"],
        .search-container input[type="date"] {
            width: 200px;
        }

        .search-container button {
            background-color:rgb(201, 11, 11);
            border: none;
            padding: 10px 20px;
            color: white;
            cursor: pointer;
        }

        .search-container button:hover {
            background-color:rgb(7, 69, 163);
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Formularios Registrados</h1>
        <hr>
        <h2 id="selectedTableTitle" style="color: #555;"></h2>
        <div class="selector-container">
            <select id="tableSelector">
                <option value="">--Seleccione--</option>
                <?php foreach ($tablas as $tabla) { ?>
                    <option value="<?php echo $tabla; ?>"><?php echo str_replace('_', ' ', ucwords($tabla)); ?></option>
                <?php } ?>
            </select>
            <button onclick="loadTable()">Cargar Tabla</button>
            <form action="generar_excel.php" method="post" style="display: inline-block; width: 100%;">
                <input type="hidden" name="table" id="selectedTable">
                <button type="submit">Generar Excel</button>
            </form>
        </div>

        <!-- Barra de búsqueda general y por fecha -->
        <div class="search-container">
            <input type="text" id="searchText" placeholder="Buscar registros..." oninput="loadTable()">
            <input type="date" id="startDate" onchange="loadTable()">
            <input type="date" id="endDate" onchange="loadTable()">
            <button onclick="loadTable()">Buscar</button>
        </div>

        <!-- Contenedor de tablas -->
        <div class="table-container" id="tableContainer">
            <table id="dataTable">
                <thead>
                    <tr id="tableHeader"></tr>
                </thead>
                <tbody id="tableBody"></tbody>
            </table>
        </div>

        <!-- Paginación -->
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

            fetch('cargar_tabla.php?table=' + tableSelector + '&search=' + searchText + '&startDate=' + startDate + '&endDate=' + endDate)
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

                            // Crear el botón "Generar PDF"
                            const td = document.createElement('td');
                            const generatePdfButton = document.createElement('button');
                            generatePdfButton.textContent = "Generar PDF";
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

                // Asegurarse de que todos los botones tengan texto visible
                button.style.color = '#007bff'; 

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