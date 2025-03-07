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
    <title>Dashboard de Calidad</title>
    <link rel="stylesheet" href="css/hojadeestilosmenu.css">
    <link rel="icon" href="imagen/logohsc.ico" type="image/x-icon">
    <style>
        /* Estilos para el sidenav */
        .sidenav {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: rgb(6, 60, 177); /* Fondo azul */
            padding-top: 20px;
            color: white;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidenav .logo {
            width: 100%;
            display: block;
            margin: 0 auto;
            border-radius: 8px;
        }

        .sidenav .user-info {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .sidenav .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .sidenav h3 {
            color: white;
            text-align: center;
            margin-top: 20px;
            font-size: 20px;
        }

        .sidenav a {
            display: block;
            padding: 10px;
            color: white;
            text-decoration: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .sidenav a:hover {
            background-color: rgb(182, 10, 10); /* Rojo al pasar el mouse */
            border-radius: 5px;
        }

        .sidenav hr {
            border: 0;
            border-top: 1px solid #bbb;
            margin: 20px 0;
        }

        .main-content {
            margin-left: 60px; /* Ajusta este valor si cambias el ancho del sidenav */
            padding: 10px; /* Reduce el padding para acercar el contenido al menú */
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 28px;
            color: #333;
        }

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
            margin: 0 auto;
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
            gap: 10px;
        }

        .selector-container select {
            padding: 10px;
            border-radius: 25px;
            border: 1px solid rgb(0, 0, 0);
            background-color: #f8f9fa;
            margin-right: 20px;
            margin-bottom: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            font-size: 14px;
            transition: all 0.3s;
        }

        .selector-container select:hover {
            background-color: rgb(255, 238, 238);
            border-color: rgb(182, 10, 10);
        }

        .selector-container button {
            padding: 10px 20px;
            border-radius: 25px;
            border: none;
            background-color: rgb(182, 10, 10);
            color: white;
            cursor: pointer;
            margin-bottom: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s;
        }

        .selector-container button:hover {
            background-color: rgb(6, 23, 177);
        }

        .selector-container .excel-button {
            background-color: rgb(0, 128, 0); /* Verde */
        }

        .selector-container .excel-button:hover {
            background-color: rgb(100, 12, 0); /* Verde oscuro al pasar el mouse */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 11px;
        }

        table, th, td {
            border: 2px solid #ddd;
        }

        th, td {
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
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #passwordModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999;
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
            color: rgb(6, 60, 177);
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
            transition: border-color 0.3s, all 0.3s ease-in-out;
        }

        input[type="password"]:focus {
            border-color: rgb(182, 10, 10);
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            padding: 10px 0;
        }

        .pagination button {
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 14px;
            border: 1px solid #ddd;
            background-color: white;
            cursor: pointer;
            margin: 0 5px;
        }

        .pagination button.disabled {
            background-color: #f0f0f0;
            cursor: not-allowed;
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
        <h3>MENU</h3>
        <br>
        <a href="#" onclick="openPasswordModal()">Usuarios</a>
        <a href="registrar_funcionario.php">Registrar Funcionario</a>
        <a href="ver_formularios_calidad.php">Ver Formularios</a>
        <a href="cerrarsesion.php">Cerrar sesión</a>
        <br>
        <br>
    </div>

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="main-content">
        <br>
        <div class="header-container">
            <h1 class="page-title">Dashboard de Admin </h1>
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
                <option value="formulario_turnos_im_tecnologos_medicos">Turnos Imagenologia TM</option>
            </select>
        </div>
        <div class="selector-container">
            <button onclick="loadTable()">Cargar Tabla</button>
            <button onclick="eliminartabla()">Eliminar Vista de la Tabla</button>
            <button onclick="location.reload();">Actualizar Página</button>
            <form action="generar_excel.php" method="post" style="display: inline;">
                <input type="hidden" name="table" id="selectedTable">
                <button type="submit" class="excel-button">Generar Excel</button>
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
        <div class="pagination" id="pagination" style="display: none;">
            <button id="prevButton" onclick="previousPage()">Anterior</button>
            <button id="nextButton" onclick="nextPage()">Siguiente</button>
        </div>
    </div>

    <!-- MODAL PARA CONTRASEÑA -->
    <div id="passwordModal" style="display: none;">
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
        let totalPages = 0;

        function loadTable() {
            const tableSelector = document.getElementById('tableSelector').value;
            const tableHeader = document.getElementById('tableHeader');
            const tableBody = document.getElementById('tableBody');
            const successMessage = document.getElementById('successMessage');
            const pagination = document.getElementById('pagination');
            document.getElementById('selectedTable').value = tableSelector;

            fetch(`cargar_tabla_admin.php?table=${tableSelector}&page=${currentPage}&rowsPerPage=${rowsPerPage}`)
                .then(response => response.json())
                .then(data => {
                    tableHeader.innerHTML = '';
                    tableBody.innerHTML = '';
                    
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

                        successMessage.style.display = "block";

                        setTimeout(() => {
                            successMessage.style.display = "none";
                        }, 3000);

                        totalPages = data.totalPages;
                        pagination.style.display = 'flex';
                        updatePaginationButtons();
                    } else {
                        pagination.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error al cargar la tabla:', error);
                });
        }

        function previousPage() {
            if (currentPage > 1) {
                currentPage--;
                loadTable();
            }
        }

        function nextPage() {
            if (currentPage < totalPages) {
                currentPage++;
                loadTable();
            }
        }

        function updatePaginationButtons() {
            const prevButton = document.getElementById('prevButton');
            const nextButton = document.getElementById('nextButton');

            prevButton.classList.toggle('disabled', currentPage === 1);
            nextButton.classList.toggle('disabled', currentPage === totalPages);
        }

        function filtrartablasadmin() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const tableBody = document.getElementById('tableBody');
            const rows = tableBody.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let match = false;
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].textContent.toLowerCase().indexOf(searchInput) > -1) {
                        match = true;
                        break;
                    }
                }
                rows[i].style.display = match ? '' : 'none';
            }
        }

        function eliminartabla() {
            const tableBody = document.getElementById('tableBody');
            tableBody.innerHTML = '';
            document.getElementById('pagination').style.display = 'none';
        }

        function openPasswordModal() {
            document.getElementById('passwordModal').style.display = 'flex';
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').style.display = 'none';
        }

        function validatePassword() {
            const password = document.getElementById('password').value;

            if (password === '1234') { 
                window.location.href = 'usuarios.php';
                return false;
            } else {
                alert('Contraseña incorrecta');
                return false;
            }
        }
    </script>
</body>
</html>