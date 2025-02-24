<?php
session_start();
if (!isset($_SESSION['id_usuarios'])) {
    header("Location: index.php");
    exit();
}
include 'conexion.php';

$id_usuario = $_SESSION['id_usuarios'];

// Consultar el servicio del usuario
$sql_servicio = "SELECT s.id_servicios, s.nombre_servicio 
                 FROM usuarios u
                 LEFT JOIN servicios s ON u.id_servicio = s.id_servicios
                 WHERE u.id_usuarios = ?";
$stmt = $conn->prepare($sql_servicio);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result_servicio = $stmt->get_result();
$row_servicio = $result_servicio->fetch_assoc();
$nombre_servicio = $row_servicio['nombre_servicio'] ?? "No asignado";
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Admin</title>
    <link rel="stylesheet" href="css/hojadeestilosmenu.css">
    <link rel="icon" href="imagen/logohsc.ico" type="image/x-icon">
    <style>
        .search-container {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            width: 100%;
            max-width: 300px;
            margin-bottom: 15px;
            border: 2px solid rgb(182, 10, 10);
            border-radius: 25px;
            overflow: hidden;
            background-color: rgb(255, 238, 238);
        }

        .search-container input {
            flex: 1;
            padding: 10px;
            border: none;
            outline: none;
            font-size: 16px;
            background: transparent;
        }

        .table-container {
            width: 100%;
            margin-left: auto;
            background: #ffffff;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
            display: block;
            overflow-x: auto;
            overflow-y: auto;
            max-height: 400px;
        }

        .selector-container {
            display: flex;
            align-items: center;
            margin: 20px 0;
            flex-wrap: wrap;
            gap: 5px;
        }

        .selector-container select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid rgb(0, 0, 0);
            background-color: #f8f9fa;
            margin-right: 20px;
            margin-bottom: 10px;
        }

        .selector-container button {
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            background-color: rgb(182, 10, 10);
            color: white;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .selector-container button:hover {
            background-color: rgb(6, 23, 177);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 11px;
        }

        table,
        th,
        td {
            border: 2px solid #ddd;
        }

        th,
        td {
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: rgb(6, 60, 177);
            color: white;
        }

        td {
            word-wrap: break-word;
            max-width: 120px;
            text-overflow: ellipsis;
            overflow: hidden;
        }

        /* Estilos para el modal */
        #passwordModal {
            display: none; 
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Fondo semi-transparente */
            z-index: 9999; /* Asegura que se muestre sobre otros elementos */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            width: 300px;
            max-width: 90%;
            text-align: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
            color: rgb(6, 60, 177); /* Color personalizado para el título */
            font-size: 20px;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            background-color: #f8f9fa;
            outline: none;
            transition: border-color 0.3s;
        }

        input[type="password"]:focus {
            border-color: rgb(182, 10, 10);
        }

        button {
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            background-color: rgb(182, 10, 10);
            color: white;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            width: auto; /* Ajustar el tamaño del botón */
            margin: 5px 0;
        }

        button:hover {
            background-color: rgb(6, 23, 177);
        }

        button:focus {
            outline: none;
        }

        button[type="button"] {
            background-color: #ccc;
        }

        button[type="button"]:hover {
            background-color: #999;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination button {
            padding: 5px 10px;
            border: 1px solid #ddd;
            background-color: white;
            cursor: pointer;
        }

        .pagination button.active {
            background-color: rgb(182, 10, 10);
            color: white;
        }
    </style>
</head>

<body>
    <!-- MENU LATERAL SIDENAV -->
    <div class="sidenav">
        <br>
        <img src="imagen/logohsf02.jpg" alt="Logo" class="logo">
        <br>
        <div class="user-info">
            <img src="imagen/206859.png" alt="Usuario" class="user-icon">
            <p>Sesión iniciada: <br><strong><?= htmlspecialchars($_SESSION['usuario']); ?></strong></p>
        </div>
        <br>
        <hr>
        <br>
        <h3>Administracion de Usuarios<h3>
        <br>
        <a href="#" onclick="openPasswordModal()">Usuarios</a> <!-- Nueva opción para usuarios con modal -->
        <a href="registrar_funcionario.php">Registrar Funcionario</a> <!-- Nueva opción para registrar funcionario -->
        <br>
        <hr>
        <br>
        <a href="cerrarsesion.php">Cerrar sesión</a>
        <br>
        <br>
    </div>
    <br>
    <!-- CONTENEDOR PRINCIPAL -->
    <div class="main-content">
        <br>
        <div class="header-container">
            <h1 class="page-title">Menu Admin</h1>
        </div>
        <hr>
        <br>
        <h2>Tablas:</h2>

        <!-- SELECTORES DE TABLAS Y FORMULARIOS -->
        <div class="selector-container">
            <select id="tableSelector">
                <option value="">--Seleccione--</option>
                <option value="formulario_turnos_uci_enfermeros">Turnos UCI Enfermeros</option>
                <option value="formulario_turnos_uci_tens">Turnos UCI Tens</option>
                <option value="formulario_turnos_uci_kinesiologos">Turnos UCI Kinesiologos</option>
                <option value="formulario_turnos_upc_medicos">Turnos UPC Médicos</option>
                <option value="formulario_turnos_uti_enfermeros">Turnos UTI Enfermeros</option>
                <option value="formulario_turnos_uti_kinesiologos">Turnos UTI Kinesiologos</option>
                <option value="formulario_turnos_uti_tens">Turnos UTI Tens</option>
            </select>
            <!-- barra busqueda -->
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Buscar en la tabla..." onkeyup="filtrartablasadmin()">
            </div>
        </div>
        <div class="selector-container">
            <button onclick="loadTable()">Cargar Tabla</button>
            <br>
            <button onclick="eliminartabla()">Eliminar Vista de la Tabla</button>
            <form action="generar_excel.php" method="post" style="display: inline;">
                <input type="hidden" name="table" id="selectedTable">
                <button type="submit">Generar Excel</button>
            </form>
        </div>
        <div id="successMessage" style="display: none; padding: 10px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; margin-top: 10px;">
            Datos cargados exitosamente.
        </div>

        <!-- CONTENEDORES DE TABLAS -->
        <div class="table-container" id="tableContainer">
            <table id="dataTable">
                <thead>
                    <tr id="tableHeader"></tr>
                </thead>
                <tbody id="tableBody">
                </tbody>
            </table>
        </div>
        <div class="pagination" id="pagination"></div>
    </div>

    <!-- MODAL PARA CONTRASEÑA -->
    <div id="passwordModal" style="display: none;"> <!-- Asegúrate de que el display inicial sea none -->
        <div class="modal-content">
            <h2>Ingrese Contraseña</h2>
            <form id="passwordForm" onsubmit="return validatePassword()">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
                <button type="submit">Enviar</button>
                <button type="button" onclick="closePasswordModal()">Cancelar</button>
            </form>
        </div>
    </div>

    <script src="js/funcion_eliminartablaadmin.js"></script>
    <script src="js/filtrar_tablasadmin.js"></script>
    <script>
        let currentPage = 1;
        const rowsPerPage = 10;

        function loadTable() {
            const tableSelector = document.getElementById('tableSelector').value;
            const tableHeader = document.getElementById('tableHeader');
            const tableBody = document.getElementById('tableBody');
            const successMessage = document.getElementById('successMessage');
            document.getElementById('selectedTable').value = tableSelector;

            fetch('cargar_tabla.php?table=' + tableSelector)
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

                        // Mostrar mensaje de éxito
                        successMessage.style.display = "block";

                        // Ocultar el mensaje después de 3 segundos
                        setTimeout(() => {
                            successMessage.style.display = "none";
                        }, 3000);

                        // Actualizar paginación
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

        function openPasswordModal() {
            document.getElementById('passwordModal').style.display = 'flex';
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').style.display = 'none';
        }

        function validatePassword() {
            const password = document.getElementById('password').value;

            // Aquí puedes realizar la validación de la contraseña
            if (password === '1234') { // Reemplaza 'contraseña_correcta' por la contraseña real
                window.location.href = 'usuarios.php';
                return false; // Evita el submit tradicional
            } else {
                alert('Contraseña incorrecta');
                return false; // Evita el submit tradicional
            }
        }
    </script>
</body>

</html>