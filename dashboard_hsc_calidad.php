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
            margin-left: 50px;
            padding: 20px;
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
        .option-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 30px;
        }
        .option-container .option {
            background: #0066cc;
            color: white;
            padding: 15px;
            border-radius: 8px;
            width: 200px;
            text-align: center;
            cursor: pointer;
        }
        .option-container .option:hover {
            background: #0055b3;
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
        <h3>Menu Calidad</h3>
        <br>

        <a href="ver_formularios_calidad.php">Ver Formularios</a>
        <a href="reportes_calidad.php">Generar Reportes</a>
        <a href="cerrarsesion.php">Cerrar sesión</a>
        <br>
        <br>
    </div>

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="main-content">
        <br>
        <div class="header-container">
            <h1 class="page-title">Dashboard de Supervisión de Calidad</h1>
        </div>
        <hr>
        <br>

        <!-- Opciones de supervisión -->
        <div class="option-container">
            <div class="option" onclick="location.href='ver_formularios_calidad.php'">
                <h3>Ver Formularios</h3>
                <p>Accede a todos los formularios ingresados por los usuarios.</p>
            </div>
            <div class="option" onclick="location.href='reportes_calidad.php'">
                <h3>Generar Reportes</h3>
                <p>Genera reportes detallados de la calidad de los datos dirigidos a funcionarios.</p>
            </div>
        </div>
    </div>

</body>
</html>
