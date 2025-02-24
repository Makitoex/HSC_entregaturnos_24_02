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
function obtenerColumnas($conexion, $tabla) {
    $query = "DESCRIBE $tabla";
    $resultado = mysqli_query($conexion, $query);
    $columnas = [];
    while ($columna = mysqli_fetch_assoc($resultado)) {
        $columnas[] = $columna['Field'];
    }
    return $columnas;
}

// Función para obtener las filas de una tabla con filtros
function obtenerFilas($conexion, $tabla, $search = '', $startDate = '', $endDate = '') {
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
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f2f5;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 30px;
            color:rgb(0, 0, 0);
            text-align: center;
        }

        .selector-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .selector-container select,
        .selector-container button,
        .selector-container form button {
            padding: 12px 16px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solidrgb(0, 0, 0);
            background-color:rgb(179, 2, 2);
            color: white;
            cursor: pointer;
            width: 30%;
            transition: background-color 0.3s, border-color 0.3s;
            text-align: center;
        }

        .selector-container button:hover,
        .selector-container form button:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .search-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .search-container input {
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ccc;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 30%;
        }

        .search-container input[type="date"] {
            width: 20%;
        }

        .search-container button {
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solidrgb(25, 112, 18);
            background-color: #007BFF;
            color: white;
            cursor: pointer;
            width: 15%;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .search-container button:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .table-container {
            margin-top: 20px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        table th,
        table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f4f4f9;
            color: #333;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 5px;
        }

        .pagination button {
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            background-color: #007BFF;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .pagination button:hover {
            background-color: #0056b3;
        }

        .pagination button.active {
            background-color: #0056b3;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Formularios Registrados</h1>
        <br>
        <hr>
        <br>
        <h2 id="selectedTableTitle" style="text-align: center; color: #555;"></h2>
        <br>
        <!-- Selector de tablas -->
        <div class="selector-container">
            <br>
            <br>
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
                <tbody id="tableBody">
                </tbody>
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

    // Mostrar el nombre de la tabla seleccionada en el subtítulo
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
                data.rows.forEach(row => {
                    const tr = document.createElement('tr');
                    row.forEach(cell => {
                        const td = document.createElement('td');
                        td.textContent = cell;
                        tr.appendChild(td);
                    });
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
                if (i === currentPage) {
                    button.classList.add('active');
                }
                button.addEventListener('click', () => {
                    currentPage = i;
                    displayRows();
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
    </script>
</body>

</html>