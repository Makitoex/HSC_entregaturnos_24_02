<?php
session_start();
if (!isset($_SESSION['id_usuarios'])) {
    header("Location: index.php");
    exit();
}
include 'conexion.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuarios'])) {
    header("Location: index.php");
    exit();
}

$id_usuario = $_SESSION['id_usuarios'];
$registros_por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina_actual - 1) * $registros_por_pagina;

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


echo "ONLINE: " . htmlspecialchars($nombre_servicio);

if (stripos($nombre_servicio, "uti_tens") !== false) {
    $tabla = "formulario_turnos_uti_tens";
    $query = "SELECT 
                t.id,
                t.fecha,
                fs1.nombre_funcionarios AS funcionario_saliente_1,
                fs2.nombre_funcionarios AS funcionario_saliente_2,
                fs3.nombre_funcionarios AS funcionario_saliente_3,
                fe1.nombre_funcionarios AS funcionario_entrante_1,
                fe2.nombre_funcionarios AS funcionario_entrante_2,
                fe3.nombre_funcionarios AS funcionario_entrante_3,
                t.tipoturno
              FROM $tabla t
              LEFT JOIN funcionarios_uti fs1 ON t.funcionario_saliente_1 = fs1.id_funcionarios
              LEFT JOIN funcionarios_uti fs2 ON t.funcionario_saliente_2 = fs2.id_funcionarios
              LEFT JOIN funcionarios_uti fs3 ON t.funcionario_saliente_3 = fs3.id_funcionarios
              LEFT JOIN funcionarios_uti fe1 ON t.funcionario_entrante_1 = fe1.id_funcionarios
              LEFT JOIN funcionarios_uti fe2 ON t.funcionario_entrante_2 = fe2.id_funcionarios
              LEFT JOIN funcionarios_uti fe3 ON t.funcionario_entrante_3 = fe3.id_funcionarios
              ORDER BY t.id DESC
              LIMIT ?, ?";
    $total_query = "SELECT COUNT(*) AS total FROM $tabla";
} elseif (stripos($nombre_servicio, "uci_tens") !== false) {
    $tabla = "formulario_turnos_uci_tens";
    $query = "SELECT 
                t.id,
                t.fecha,
                fs1.nombre_funcionarios AS funcionario_saliente_1,
                fs2.nombre_funcionarios AS funcionario_saliente_2,
                fs3.nombre_funcionarios AS funcionario_saliente_3,
                fe1.nombre_funcionarios AS funcionario_entrante_1,
                fe2.nombre_funcionarios AS funcionario_entrante_2,
                fe3.nombre_funcionarios AS funcionario_entrante_3,
                t.tipoturno
              FROM $tabla t
              LEFT JOIN funcionarios_uci fs1 ON t.funcionario_saliente_1 = fs1.id_funcionarios
              LEFT JOIN funcionarios_uci fs2 ON t.funcionario_saliente_2 = fs2.id_funcionarios
              LEFT JOIN funcionarios_uci fs3 ON t.funcionario_saliente_3 = fs3.id_funcionarios
              LEFT JOIN funcionarios_uci fe1 ON t.funcionario_entrante_1 = fe1.id_funcionarios
              LEFT JOIN funcionarios_uci fe2 ON t.funcionario_entrante_2 = fe2.id_funcionarios
              LEFT JOIN funcionarios_uci fe3 ON t.funcionario_entrante_3 = fe3.id_funcionarios
              ORDER BY t.id DESC
              LIMIT ?, ?";
    $total_query = "SELECT COUNT(*) AS total FROM $tabla";
} elseif (stripos($nombre_servicio, "upc_medicos") !== false) {
    $tabla = "formulario_turnos_upc_medicos";
    $query = "SELECT 
                m.id,
                m.fecha,
                fd1.nombre_funcionarios AS funcionario_saliente_1,
                fd2.nombre_funcionarios AS funcionario_entrante_1
              FROM $tabla m
              LEFT JOIN funcionarios_uti fd1 ON m.funcionario_saliente_1 = fd1.id_funcionarios
              LEFT JOIN funcionarios_uti fd2 ON m.funcionario_entrante_1 = fd2.id_funcionarios
              ORDER BY m.id DESC
              LIMIT ?, ?";
    $total_query = "SELECT COUNT(*) AS total FROM $tabla";
} elseif (stripos($nombre_servicio, "pd_tens") !== false) {
    $tabla = "formulario_turnos_pd_tens_pediatria";
    $query = "SELECT 
                m.id,
                m.fecha,
                m.tipoturno,
                fd1.nombre_funcionarios AS funcionario_saliente_1,
                fd2.nombre_funcionarios AS funcionario_entrante_1
              FROM $tabla m
              LEFT JOIN funcionarios_pediatria fd1 ON m.funcionario_saliente_1 = fd1.id_funcionarios
              LEFT JOIN funcionarios_pediatria fd2 ON m.funcionario_entrante_1 = fd2.id_funcionarios
              ORDER BY m.id DESC
              LIMIT ?, ?";
    $total_query = "SELECT COUNT(*) AS total FROM $tabla";
} elseif (stripos($nombre_servicio, "PB_anestesiologos") !== false) {
    $tabla = "formulario_turnos_pb_anestesistas";
    $query = "SELECT 
                m.id,
                m.fecha,
                m.tipoturno,
                fd1.nombre_funcionarios AS funcionario_saliente_1,
                fd2.nombre_funcionarios AS funcionario_entrante_1
              FROM $tabla m
              LEFT JOIN funcionarios_pabellon fd1 ON m.funcionario_saliente_1 = fd1.id_funcionarios
              LEFT JOIN funcionarios_pabellon fd2 ON m.funcionario_entrante_1 = fd2.id_funcionarios
              ORDER BY m.id DESC
              LIMIT ?, ?";
    $total_query = "SELECT COUNT(*) AS total FROM $tabla";
} elseif (stripos($nombre_servicio, "pb_tens") !== false) {
    $tabla = "formulario_turnos_pb_tens";
    $query = "SELECT 
                m.id,
                m.fecha,
                m.tipoturno,
                m.pabellonero,
                m.arsenalero,
                m.nombre_tecanestesia_turno,
                fk1.nombre_funcionarios AS nombre_funcionario_tecanestesia,
                fk2.nombre_funcionarios AS nombre_funcionario_arsenalero,
                fk3.nombre_funcionarios AS nombre_funcionario_pabellonero
              FROM $tabla m
              LEFT JOIN funcionarios_pabellon fk1 ON m.funcionario_tecanestesia = fk1.id_funcionarios
              LEFT JOIN funcionarios_pabellon fk2 ON m.funcionario_arsenalero = fk2.id_funcionarios
              LEFT JOIN funcionarios_pabellon fk3 ON m.funcionario_pabellonero = fk3.id_funcionarios
              ORDER BY m.id DESC
              LIMIT ?, ?";
    $total_query = "SELECT COUNT(*) AS total FROM $tabla";
} elseif (stripos($nombre_servicio, "uti_enfermeros") !== false) {
    $tabla = "formulario_turnos_uti_enfermeros";
    $query = "SELECT 
                f.id, 
                f.fecha, 
                fs1.nombre_funcionarios AS funcionario_saliente_1, 
                fs2.nombre_funcionarios AS funcionario_saliente_2, 
                fe1.nombre_funcionarios AS funcionario_entrante_1, 
                fe2.nombre_funcionarios AS funcionario_entrante_2,
                f.tipoturno
              FROM $tabla f
              LEFT JOIN funcionarios_uti fs1 ON f.funcionario_saliente_1 = fs1.id_funcionarios
              LEFT JOIN funcionarios_uti fs2 ON f.funcionario_saliente_2 = fs2.id_funcionarios
              LEFT JOIN funcionarios_uti fe1 ON f.funcionario_entrante_1 = fe1.id_funcionarios
              LEFT JOIN funcionarios_uti fe2 ON f.funcionario_entrante_2 = fe2.id_funcionarios
              ORDER BY f.id DESC
              LIMIT ?, ?";
    $total_query = "SELECT COUNT(*) AS total FROM $tabla";
} elseif (stripos($nombre_servicio, "uti_kinesiologos") !== false) {
    $tabla = "formulario_turnos_uti_kinesiologos";
    $query = "SELECT 
                k.id, 
                k.fecha, 
                fk1.nombre_funcionarios AS funcionario_saliente_1,  
                ke1.nombre_funcionarios AS funcionario_entrante_1,
                k.tipoturno
              FROM $tabla k
              LEFT JOIN funcionarios_uti fk1 ON k.funcionario_saliente_1 = fk1.id_funcionarios
              LEFT JOIN funcionarios_uti ke1 ON k.funcionario_entrante_1 = ke1.id_funcionarios
              ORDER BY k.id DESC
              LIMIT ?, ?";
    $total_query = "SELECT COUNT(*) AS total FROM $tabla";
} elseif (stripos($nombre_servicio, "uti_kinesiologos") !== false) {
    $tabla = "formulario_turnos_uti_kinesiologos";
    $query = "SELECT 
                            k.id, 
                            k.fecha, 
                            fk1.nombre_funcionarios AS funcionario_saliente_1,  
                            ke1.nombre_funcionarios AS funcionario_entrante_1,
                            k.tipoturno
                          FROM $tabla k
                          LEFT JOIN funcionarios_uti fk1 ON k.funcionario_saliente_1 = fk1.id_funcionarios
                          LEFT JOIN funcionarios_uti ke1 ON k.funcionario_entrante_1 = ke1.id_funcionarios
                          ORDER BY k.id DESC
                          LIMIT ?, ?";
    $total_query = "SELECT COUNT(*) AS total FROM $tabla";
} elseif (stripos($nombre_servicio, "pb_enfermeros") !== false) {
    $tabla = "formulario_turnos_pb_enfermeros";
    $query = "SELECT 
                k.id, 
                k.fecha, 
                fk1.nombre_funcionarios AS funcionario_saliente_1,  
                ke1.nombre_funcionarios AS funcionario_entrante_1,
                k.tipoturno
              FROM $tabla k
              LEFT JOIN funcionarios_pabellon fk1 ON k.funcionario_saliente_1 = fk1.id_funcionarios
              LEFT JOIN funcionarios_pabellon ke1 ON k.funcionario_entrante_1 = ke1.id_funcionarios
              ORDER BY k.id DESC
              LIMIT ?, ?";
    $total_query = "SELECT COUNT(*) AS total FROM $tabla";
} elseif (stripos($nombre_servicio, "uci_enfermeros") !== false) {
    $tabla = "formulario_turnos_uci_enfermeros";
    $query = "SELECT 
                f.id, 
                f.fecha, 
                fs1.nombre_funcionarios AS funcionario_saliente_1, 
                fs2.nombre_funcionarios AS funcionario_saliente_2, 
                fe1.nombre_funcionarios AS funcionario_entrante_1, 
                fe2.nombre_funcionarios AS funcionario_entrante_2,
                f.tipoturno
              FROM $tabla f
              LEFT JOIN funcionarios_uci fs1 ON f.funcionario_saliente_1 = fs1.id_funcionarios
              LEFT JOIN funcionarios_uci fs2 ON f.funcionario_saliente_2 = fs2.id_funcionarios
              LEFT JOIN funcionarios_uci fe1 ON f.funcionario_entrante_1 = fe1.id_funcionarios
              LEFT JOIN funcionarios_uci fe2 ON f.funcionario_entrante_2 = fe2.id_funcionarios
              ORDER BY f.id DESC
              LIMIT ?, ?";
    $total_query = "SELECT COUNT(*) AS total FROM $tabla";
} elseif (stripos($nombre_servicio, "im_tecnologos") !== false) {
    $tabla = "formulario_turnos_im_tecnologos_medicos";
    $query = "SELECT 
                tm.id,
                tm.fecha,
                fs1.nombre_funcionarios AS funcionario_saliente_1,
                fe1.nombre_funcionarios AS funcionario_entrante_1,
                tm.tipoturno
              FROM $tabla tm
              LEFT JOIN funcionarios_imagenologia fe1 ON tm.funcionario_saliente_1 = fs1.id_funcionarios
              LEFT JOIN funcionarios_imagenologia fe1 ON tm.funcionario_entrante_1 = fs1.id_funcionarios
              ORDER BY tm.id DESC
              LIMIT ?, ?";
    $total_query = "SELECT COUNT(*) AS total FROM $tabla";
} elseif (stripos($nombre_servicio, "mb_microbiologia_tens") !== false) {
    $tabla = "formulario_turnos_mb_tens";
    $query = "SELECT 
                tm.id, 
                tm.fecha,
                fs1.nombre_funcionarios AS funcionario_saliente_1,
                fe1.nombre_funcionarios AS funcionario_entrante_1,
                tm.tipoturno
              FROM $tabla tm
              LEFT JOIN funcionarios_microbiologia fs1 ON tm.funcionario_saliente_1 = fs1.id_funcionarios
              LEFT JOIN funcionarios_microbiologia fe1 ON tm.funcionario_entrante_1 = fe1.id_funcionarios
              ORDER BY tm.id DESC
              LIMIT ?, ?";
    $total_query = "SELECT COUNT(*) AS total FROM $tabla";
} elseif (stripos($nombre_servicio, "MB_microbiologia_tecnologos") !== false) {
    $tabla = "formulario_turnos_mb_tecnologos_medicos";
    $query = "SELECT 
                tm.id, 
                tm.fecha,
                fs1.nombre_funcionarios AS funcionario_saliente_1,
                fe1.nombre_funcionarios AS funcionario_entrante_1,
                tm.tipoturno
              FROM $tabla tm
              LEFT JOIN funcionarios_microbiologia fs1 ON tm.funcionario_saliente = fs1.id_funcionarios
              LEFT JOIN funcionarios_microbiologia fe1 ON tm.funcionario_entrante = fe1.id_funcionarios
              ORDER BY tm.id DESC
              LIMIT ?, ?";
    $total_query = "SELECT COUNT(*) AS total FROM $tabla";
} else {
    // Default
    $tabla = "formulario_turnos_uti_enfermeros";
    $query = "SELECT 
                f.id, 
                f.fecha, 
                fs1.nombre_funcionarios AS funcionario_saliente_1, 
                fs2.nombre_funcionarios AS funcionario_saliente_2, 
                fe1.nombre_funcionarios AS funcionario_entrante_1, 
                fe2.nombre_funcionarios AS funcionario_entrante_2,
                f.tipoturno
              FROM $tabla f
              LEFT JOIN funcionarios_uti fs1 ON f.funcionario_saliente_1 = fs1.id_funcionarios
              LEFT JOIN funcionarios_uti fs2 ON f.funcionario_saliente_2 = fs2.id_funcionarios
              LEFT JOIN funcionarios_uti fe1 ON f.funcionario_entrante_1 = fe1.id_funcionarios
              LEFT JOIN funcionarios_uti fe2 ON f.funcionario_entrante_2 = fe2.id_funcionarios
              ORDER BY f.id DESC
              LIMIT ?, ?";
    $total_query = "SELECT COUNT(*) AS total FROM $tabla";
}
if (!empty($total_query)) {
    $total_result = mysqli_query($conn, $total_query);
    if ($total_result) {
        $total_row = mysqli_fetch_assoc($total_result);
        $total_registros = $total_row['total'];
        $total_paginas = ceil($total_registros / $registros_por_pagina);
    } else {
        die("Error al obtener el total de registros: " . mysqli_error($conn));
    }
} else {
    die("Error: $total_query es vacio o invalido.");
}

//consulta principal
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}
$stmt->bind_param("ii", $inicio, $registros_por_pagina);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Entrega de Turnos</title>
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
            border: 2px solid #007bff;
            border-radius: 25px;
            overflow: hidden;
            background-color: #f8f9fa;
        }

        .search-container input {
            flex: 1;
            padding: 10px;
            border: none;
            outline: none;
            font-size: 16px;
            background: transparent;
        }

        table {
            width: 95%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #ffffff;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #0056b3;
            color: white;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }

        tr:hover {
            background: #d9e4f5;
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
        <h2 class="menu-title">Menú Principal</h2>
        <?php if (stripos($nombre_servicio, "uci_enfermeros") !== false): ?>
            <a href="/formularios_php/UCI_formulario_enfermeros.php?tipo=uci_enfermeros">Entregar Turno UCI Enfermeros</a>
        <?php elseif (stripos($nombre_servicio, "pb_enfermeros") !== false): ?>
            <a href="/formularios_php/PB_formulario_enfermeria.php?tipo=pb_enfermeros">Entregar Turno EU Pabellon</a>
        <?php elseif (stripos($nombre_servicio, "pd_tens") !== false): ?>
            <a href="/formularios_php/UTI_pediatria_formulario_tens.php?tipo=pd_pediatria">Entregar Turno Pediatria TENS</a>
        <?php elseif (stripos($nombre_servicio, "PB_anestesiologos") !== false): ?>
            <a href="/formularios_php/PB_formulario_anestesiologos.php?tipo=pb_anestesiologos">Entregar Turno Anestesiologos Pabellon</a>
        <?php elseif (stripos($nombre_servicio, "pb_tens") !== false): ?>
            <a href="/formularios_php/PB_formulario_tens.php?tipo=pb_tens">Entregar Turno TENS PB</a>
        <?php elseif (stripos($nombre_servicio, "Mb_microbiologia_tens") !== false): ?>
            <a href="/formularios_php/MB_formulario_tens.php?tipo=tens">Entregar Turno TENS MB</a>
        <?php elseif (stripos($nombre_servicio, "enfermeros") !== false): ?>
            <a href="/formularios_php/UTI_formulario_enfermeros.php?tipo=uti">Entregar Turno Enfermeros UTI</a>
        <?php elseif (stripos($nombre_servicio, "uci_tens") !== false): ?>
            <a href="/formularios_php/UCI_formulario_tens.php?tipo=uci_tens">Entregar Turno TENS UCI</a>
        <?php elseif (stripos($nombre_servicio, "uti_tens") !== false): ?>
            <a href="/formularios_php/UTI_formulario_tens.php?tipo=tens">Entregar Turno TENS</a>
        <?php elseif (stripos($nombre_servicio, "uci_kinesiologos") !== false): ?>
            <a href="/formularios_php/UCI_formulario_kinesiologos.php?tipo=kinesio">Entregar Turno Kinesiología UCI</a>
        <?php elseif (stripos($nombre_servicio, "kinesiologos") !== false): ?>
            <a href="/formularios_php/UTI_formulario_kinesiologos.php?tipo=kinesio">Entregar Turno Kinesiología UTI</a>
        <?php elseif (stripos($nombre_servicio, "upc_medicos") !== false): ?>
            <a href="/formularios_php/UPC_formulario_medicos.php?tipo=medicos">Entregar Turno UPC Medicos</a>
        <?php elseif (stripos($nombre_servicio, "MB_microbiologia_tecnologos") !== false): ?>
            <a href="/formularios_php/MB_formulario_tecnologos_medicos.php?tipo=MB_microbiologia_tecnologos">Entregar Turno Tecnólogos Médicos MB</a>
        <?php elseif (stripos($nombre_servicio, "tecnologos") !== false): ?>
            <a href="/formularios_php/TM_formulario_tecnologosmedicos.php?tipo=tecnologos_medicos">Entregar Turno Tecnólogos Médicos</a>
        <?php else: ?>
            <p>No hay formularios disponibles para este servicio.</p>
        <?php endif; ?>
        <br>
        <hr>
        <br>
        <a href="reportes_usuario.php">Reportes Calidad</a>
        <br>
        <a href="cerrarsesion.php">Cerrar sesión</a>
    </div>
    <br>
    <!-- CONTENEDOR PRINCIPAL -->
    <div class="main-content">
        <br>
        <div class="header-container">
            <h1 class="page-title">Entrega de Turnos de <?= htmlspecialchars($nombre_servicio); ?></h1>
        </div>
        <hr>
        <br>
        <h2>Turnos Entregados:</h2>

        <!-- BARRA DE BÚSQUEDA -->
        <div class="search-container">
            <input type="text" id="filtrar_tabla" placeholder="Buscar..." onkeyup="filtrarTabla()">
        </div>
        <!-- TABLA DE REGISTROS -->
        <div class="table-container">
            <table border="1">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Funcionarios Salientes</th>
                        <th>Funcionarios Entrantes</th>
                        <th>Tipo de Turno</th>
                        <th>PDF</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['fecha']) ?></td>
                            <td>
                            <?php
// Lógica para mostrar los funcionarios salientes según el servicio
if (stripos($nombre_servicio, "mb_microbiologia_tecnologos") !== false || stripos($nombre_servicio, "mb_microbiologia_tens") !== false) {
    echo htmlspecialchars($row['funcionario_saliente_1'] ?? 'N/A');
} elseif (stripos($nombre_servicio, "uci_enfermeros") !== false || stripos($nombre_servicio, "pb_enfermeros") !== false) {
    echo htmlspecialchars($row['funcionario_saliente_1'] ?? '') . " " .
        htmlspecialchars($row['funcionario_saliente_2'] ?? '');
} elseif (stripos($nombre_servicio, "kinesiologos") !== false || stripos($nombre_servicio, "uci_kinesiologos") !== false || stripos($nombre_servicio, "upc_medicos") !== false || stripos($nombre_servicio, "im_tecnologos") !== false) {
    echo htmlspecialchars($row['funcionario_saliente_1'] ?? 'N/A');
} elseif (stripos($nombre_servicio, "uti_tens") !== false || stripos($nombre_servicio, "uci_tens") !== false) {
    echo htmlspecialchars($row['funcionario_saliente_1'] ?? 'N/A') . ", " .
        htmlspecialchars($row['funcionario_saliente_2'] ?? 'N/A') . ", " .
        htmlspecialchars($row['funcionario_saliente_3'] ?? 'N/A');
} elseif (stripos($nombre_servicio, "pb_tens") !== false) {
    echo htmlspecialchars($row['pabellonero'] ?? 'N/A') . ", " .
        htmlspecialchars($row['arsenalero'] ?? 'N/A') . ", " .
        htmlspecialchars($row['nombre_tecanestesia_turno'] ?? 'N/A');
} elseif (stripos($nombre_servicio, "PB_anestesiologos") !== false) {
    echo htmlspecialchars($row['funcionario_saliente_1'] ?? '') . " " .
        htmlspecialchars($row['funcionario_saliente_2'] ?? '');
} elseif (stripos($nombre_servicio, "pd_tens") !== false) {
    echo htmlspecialchars($row['funcionario_saliente_1'] ?? 'N/A');
} else {
    echo htmlspecialchars($row['funcionario_saliente_1'] ?? 'N/A') . ", " .
        htmlspecialchars($row['funcionario_saliente_2'] ?? 'N/A');
}
?>
                            </td>
                            <td>
                            <?php
// Lógica para mostrar los funcionarios entrantes según el servicio
if (stripos($nombre_servicio, "mb_microbiologia_tecnologos") !== false || stripos($nombre_servicio, "mb_microbiologia_tens") !== false) {
    echo htmlspecialchars($row['funcionario_entrante_1'] ?? 'N/A');
} elseif (stripos($nombre_servicio, "uci_enfermeros") !== false || stripos($nombre_servicio, "pb_enfermeros") !== false) {
    echo htmlspecialchars($row['funcionario_entrante_1'] ?? '') . " " .
        htmlspecialchars($row['funcionario_entrante_2'] ?? '');
} elseif (stripos($nombre_servicio, "kinesiologos") !== false || stripos($nombre_servicio, "uci_kinesiologos") !== false || stripos($nombre_servicio, "upc_medicos") !== false || stripos($nombre_servicio, "im_tecnologos") !== false) {
    echo htmlspecialchars($row['funcionario_entrante_1'] ?? 'N/A');
} elseif (stripos($nombre_servicio, "uti_tens") !== false || stripos($nombre_servicio, "uci_tens") !== false) {
    echo htmlspecialchars($row['funcionario_entrante_1'] ?? 'N/A') . ", " .
        htmlspecialchars($row['funcionario_entrante_2'] ?? 'N/A') . ", " .
        htmlspecialchars($row['funcionario_entrante_3'] ?? 'N/A');
} elseif (stripos($nombre_servicio, "pb_tens") !== false) {
    echo htmlspecialchars($row['nombre_funcionario_tecanestesia'] ?? 'N/A') . ", " .
        htmlspecialchars($row['nombre_funcionario_arsenalero'] ?? 'N/A') . ", " .
        htmlspecialchars($row['nombre_funcionario_pabellonero'] ?? 'N/A');
} elseif (stripos($nombre_servicio, "PB_anestesiologos") !== false) {
    echo htmlspecialchars($row['funcionario_entrante_1'] ?? '') . " " .
        htmlspecialchars($row['funcionario_entrante_2'] ?? '');
} elseif (stripos($nombre_servicio, "pd_tens") !== false) {
    echo htmlspecialchars($row['funcionario_entrante_1'] ?? 'N/A');
} else {
    echo htmlspecialchars($row['funcionario_entrante_1'] ?? 'N/A') . ", " .
        htmlspecialchars($row['funcionario_entrante_2'] ?? 'N/A');
}
?>
                            </td>
                            <td>
                                <?= htmlspecialchars($row['tipoturno'] ?? 'POR HORAS') ?>
                            </td>
                            <td>
                                <?php
                                // Lógica para generar el PDF según el servicio
                                if (stripos($nombre_servicio, "mb_microbiologia_tecnologos") !== false) {
                                    echo '<a href="formularios_php/generar_pdf_mb_microbiologia_tecnologos.php?id=' . $row['id'] . '" target="_blank">Generar PDF</a>';
                                } elseif (stripos($nombre_servicio, "mb_microbiologia_tens") !== false) {
                                    echo '<a href="formularios_php/generar_pdf_mb_microbiologia_tens.php?id=' . $row['id'] . '" target="_blank">Generar PDF</a>';
                                } elseif (stripos($nombre_servicio, "pd_tens") !== false) {
                                    echo '<a href="formularios_php/generar_pdf_pd_pediatria_tens.php?id=' . $row['id'] . '" target="_blank">Generar PDF</a>';
                                } elseif (stripos($nombre_servicio, "pb_anestesiologos") !== false) {
                                    echo '<a href="formularios_php/generar_pdf_pb_anestesiologos.php?id=' . $row['id'] . '" target="_blank">Generar PDF</a>';
                                } elseif (stripos($nombre_servicio, "pb_tens") !== false) {
                                    echo '<a href="formularios_php/generar_pdf_pb_tens.php?id=' . $row['id'] . '" target="_blank">Generar PDF</a>';
                                } elseif (stripos($nombre_servicio, "uci_enfermeros") !== false) {
                                    echo '<a href="formularios_php/generar_pdf_uci_enfermeros.php?id=' . $row['id'] . '" target="_blank">Generar PDF</a>';
                                } elseif (stripos($nombre_servicio, "pb_enfermeros") !== false) {
                                    echo '<a href="formularios_php/generar_pdf_pb_enfermeros.php?id=' . $row['id'] . '" target="_blank">Generar PDF</a>';
                                } elseif (stripos($nombre_servicio, "enfermeros") !== false) {
                                    echo '<a href="formularios_php/generar_pdf_uti_enfermeros.php?id=' . $row['id'] . '" target="_blank">Generar PDF</a>';
                                } elseif (stripos($nombre_servicio, "uti_tens") !== false) {
                                    echo '<a href="formularios_php/generar_pdf_uti_tens.php?id=' . $row['id'] . '" target="_blank">Generar PDF</a>';
                                } elseif (stripos($nombre_servicio, "uci_tens") !== false) {
                                    echo '<a href="formularios_php/generar_pdf_uci_tens.php?id=' . $row['id'] . '" target="_blank">Generar PDF</a>';
                                } elseif (stripos($nombre_servicio, "uci_kinesiologos") !== false) {
                                    echo '<a href="formularios_php/generar_pdf_uci_kinesiologos.php?id=' . $row['id'] . '" target="_blank">Generar PDF</a>';
                                } elseif (stripos($nombre_servicio, "kinesiologos") !== false) {
                                    echo '<a href="formularios_php/generar_pdf_uti_kinesiologos.php?id=' . $row['id'] . '" target="_blank">Generar PDF</a>';
                                } elseif (stripos($nombre_servicio, "upc_medicos") !== false) {
                                    echo '<a href="formularios_php/generar_pdf_upc_medicos.php?id=' . $row['id'] . '" target="_blank">Generar PDF</a>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- PAGINACIÓN -->
        <div style="margin-top: 40px; text-align: center;">
            <?php
            if ($pagina_actual > 1): ?>
                <a href="?pagina=1" style="margin: 0 5px; padding: 5px 10px; background-color:#0056b3; color: white; text-decoration: none; border-radius: 5px;">Primero</a>
                <a href="?pagina=<?= $pagina_actual - 1 ?>" style="margin: 0 5px; padding: 5px 10px; background-color:#0056b3; color: white; text-decoration: none; border-radius: 5px;">&laquo; Anterior</a>
            <?php endif; ?>

            <?php
            // limite de páginas visibles
            $max_visible = 4;
            $start = max(1, $pagina_actual - 1);
            $end = min($total_paginas, $start + $max_visible - 1); 

    
            if ($end - $start + 1 < $max_visible) {
                $start = max(1, $end - $max_visible + 1);
            }

            for ($i = $start; $i <= $end; $i++): ?>
                <a href="?pagina=<?= $i ?>" style="margin: 0 5px; padding: 5px 10px; background-color: <?= $i == $pagina_actual ? '#28a745' : '#0056b3'; ?>; color: white; text-decoration: none; border-radius: 5px;">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php
    
            if ($pagina_actual < $total_paginas): ?>
                <a href="?pagina=<?= $pagina_actual + 1 ?>" style="margin: 0 5px; padding: 5px 10px; background-color:#0056b3; color: white; text-decoration: none; border-radius: 5px;">Siguiente &raquo;</a>
                <a href="?pagina=<?= $total_paginas ?>" style="margin: 0 5px; padding: 5px 10px; background-color:#0056b3; color: white; text-decoration: none; border-radius: 5px;">Último</a>
            <?php endif; ?>
        </div>

    </div>
    <script src="js/filtrar_tabla.js"></script>
</body>

</html>