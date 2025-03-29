<?php
session_start();

// Verificar si el usuario inició sesión
if (!isset($_SESSION['id_usuarios'])) {
    header("Location: index.php");
    exit();
}

// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir conexión a la base de datos
include '/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';
mysqli_set_charset($conn, "utf8mb4");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha = $_POST['fecha'];
    $tipoturno = $_POST['tipoturno'];

    // Paciente 1
    $nombre1 = $_POST['nombre1'];
    $detalles1 = $_POST['detalles1'];
    $peridural1 = $_POST['peridural1'];
    $cateter_reg1 = $_POST['cateter_reg1'];
    $analgev1 = $_POST['analgev1'];
    $otro1 = $_POST['otro1'];

    // Paciente 2
    $nombre2 = $_POST['nombre2'];
    $detalles2 = $_POST['detalles2'];
    $peridural2 = $_POST['peridural2'];
    $cateter_reg2 = $_POST['cateter_reg2'];
    $analgev2 = $_POST['analgev2'];
    $otro2 = $_POST['otro2'];

    // Paciente 3
    $nombre3 = $_POST['nombre3'];
    $detalles3 = $_POST['detalles3'];
    $peridural3 = $_POST['peridural3'];
    $cateter_reg3 = $_POST['cateter_reg3'];
    $analgev3 = $_POST['analgev3'];
    $otro3 = $_POST['otro3'];

    // Paciente 4
    $nombre4 = $_POST['nombre4'];
    $detalles4 = $_POST['detalles4'];
    $peridural4 = $_POST['peridural4'];
    $cateter_reg4 = $_POST['cateter_reg4'];
    $analgev4 = $_POST['analgev4'];
    $otro4 = $_POST['otro4'];

    // Paciente 5
    $nombre5 = $_POST['nombre5'];
    $detalles5 = $_POST['detalles5'];
    $peridural5 = $_POST['peridural5'];
    $cateter_reg5 = $_POST['cateter_reg5'];
    $analgev5 = $_POST['analgev5'];
    $otro5 = $_POST['otro5'];

    // Paciente 6
    $nombre6 = $_POST['nombre6'];
    $detalles6 = $_POST['detalles6'];
    $peridural6 = $_POST['peridural6'];
    $cateter_reg6 = $_POST['cateter_reg6'];
    $analgev6 = $_POST['analgev6'];
    $otro6 = $_POST['otro6'];

    // Paciente 7
    $nombre7 = $_POST['nombre7'];
    $detalles7 = $_POST['detalles7'];
    $peridural7 = $_POST['peridural7'];
    $cateter_reg7 = $_POST['cateter_reg7'];
    $analgev7 = $_POST['analgev7'];
    $otro7 = $_POST['otro7'];

    // Anestesistas
    $funcionario_saliente_1 = $_POST['funcionario_saliente_1'];
    $nombre_funcionario_saliente_1 = $_POST['nombre_funcionario_saliente_1'];
    $pin_funcionario_saliente_1 = $_POST['pin_funcionario_saliente_1'];
    $contrasena_saliente_1 = $_POST['contrasena_saliente_1'];
    $funcionario_entrante_1 = $_POST['funcionario_entrante_1'];
    $nombre_funcionario_entrante_1 = $_POST['nombre_funcionario_entrante_1'];
    $pin_funcionario_entrante_1 = $_POST['pin_funcionario_entrante_1'];

    $query = "INSERT INTO formulario_turnos_pb_anestesistas(fecha, tipoturno, nombre1, detalles1, peridural1, cateter_reg1, analgev1, otro1, nombre2, detalles2, peridural2, cateter_reg2, analgev2, otro2, nombre3, detalles3, peridural3, cateter_reg3, analgev3, otro3, nombre4, detalles4, peridural4, cateter_reg4, analgev4, otro4, nombre5, detalles5, peridural5, cateter_reg5, analgev5, otro5, nombre6, detalles6, peridural6, cateter_reg6, analgev6, otro6, nombre7, detalles7, peridural7, cateter_reg7, analgev7, otro7, funcionario_saliente_1, nombre_funcionario_saliente_1, pin_funcionario_saliente_1, contrasena_saliente_1, funcionario_entrante_1, nombre_funcionario_entrante_1, pin_funcionario_entrante_1) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssssssssssssssssssssssssssssssssssssssssssssss", $fecha, $tipoturno, $nombre1, $detalles1, $peridural1, $cateter_reg1, $analgev1, $otro1, $nombre2, $detalles2, $peridural2, $cateter_reg2, $analgev2, $otro2, $nombre3, $detalles3, $peridural3, $cateter_reg3, $analgev3, $otro3, $nombre4, $detalles4, $peridural4, $cateter_reg4, $analgev4, $otro4, $nombre5, $detalles5, $peridural5, $cateter_reg5, $analgev5, $otro5, $nombre6, $detalles6, $peridural6, $cateter_reg6, $analgev6, $otro6, $nombre7, $detalles7, $peridural7, $cateter_reg7, $analgev7, $otro7, $funcionario_saliente_1, $nombre_funcionario_saliente_1, $pin_funcionario_saliente_1, $contrasena_saliente_1, $funcionario_entrante_1, $nombre_funcionario_entrante_1, $pin_funcionario_entrante_1);

    if ($stmt->execute()) {
        $id_formulario = $stmt->insert_id;

        // Redirigir al PHP que genera el PDF 
        header("Location: generar_pdf_pb_anestesiologos.php?id=" . $id_formulario);
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
$stmt->close();
$conn->close();
}
?>