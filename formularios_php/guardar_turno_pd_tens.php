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
include 'C:/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';
mysqli_set_charset($conn, "utf8mb4");

// Obtener los datos del formulario
$fecha = $_POST['fecha'];
$tipoturno = $_POST['tipoturno'];
$horario = $_POST['horario'];
$nombre_tens_sala = $_POST['nombre_tens_sala'];
$paciente_1 = $_POST['paciente_1'];
$paciente_2 = $_POST['paciente_2'];
$paciente_3 = $_POST['paciente_3'];
$paciente_4 = $_POST['paciente_4'];
$paciente_5 = $_POST['paciente_5'];
$paciente_6 = $_POST['paciente_6'];
$novedades = $_POST['novedades'];
$equipos_prestamo = $_POST['equipos_prestamo'];
$servicio = $_POST['servicio'];
$observaciones = isset($_POST['observaciones']) ? $_POST['observaciones'] : ''; // Ensure 'observaciones' is set
$funcionario_saliente_1 = $_POST['funcionario_saliente_1'];
$nombre_funcionario_saliente_1 = $_POST['nombre_funcionario_saliente_1'];
$pin_funcionario_saliente_1 = $_POST['pin_funcionario_saliente_1'];
$contrasena_saliente_1 = $_POST['contrasena_saliente_1'];
$funcionario_entrante_1 = $_POST['funcionario_entrante_1'];
$nombre_funcionario_entrante_1 = $_POST['nombre_funcionario_entrante_1'];
$pin_funcionario_entrante_1 = $_POST['pin_funcionario_entrante_1'];

// Obtener los valores de los checkboxes
$saturometros = isset($_POST['saturometros']) ? 1 : 0;
$alcoholes = isset($_POST['alcoholes']) ? 1 : 0;
$pendrive_pediatria = isset($_POST['pendrive_pediatria']) ? 1 : 0;
$tablillas = isset($_POST['tablillas']) ? 1 : 0;
$libros = isset($_POST['libros']) ? 1 : 0;
$pecheras = isset($_POST['pecheras']) ? 1 : 0;
$rotulos = isset($_POST['rotulos']) ? 1 : 0;
$material_esteril = isset($_POST['material_esteril']) ? 1 : 0;
$fichas_medicas = isset($_POST['fichas_medicas']) ? 1 : 0;
$actualizacion_egresos = isset($_POST['actualizacion_egresos']) ? 1 : 0;
$carro_insumos = isset($_POST['carro_insumos']) ? 1 : 0;
$salas_con_epp = isset($_POST['salas_con_epp']) ? 1 : 0;

$banos = isset($_POST['banos']) ? $_POST['banos'] : 'no';
$pisos = isset($_POST['pisos']) ? $_POST['pisos'] : 'no';
$da_aviso = isset($_POST['da_aviso']) ? $_POST['da_aviso'] : 'no';
$chatas = isset($_POST['chatas']) ? $_POST['chatas'] : 'no';
$aseo_terminal = isset($_POST['aseo_terminal']) ? $_POST['aseo_terminal'] : 'no';

// Insertar los datos en la base de datos
$sql = "INSERT INTO formulario_turnos_pd_tens_pediatria (
    fecha, tipoturno, horario, nombre_tens_sala, 
    paciente_1, paciente_2, paciente_3, paciente_4, paciente_5, paciente_6, 
    novedades, equipos_prestamo, servicio, observaciones, 
    funcionario_saliente_1, nombre_funcionario_saliente_1, pin_funcionario_saliente_1, contrasena_saliente_1, 
    funcionario_entrante_1, nombre_funcionario_entrante_1, pin_funcionario_entrante_1, 
    saturometros, alcoholes, pendrive_pediatria, tablillas, libros, pecheras, 
    rotulos, material_esteril, fichas_medicas, actualizacion_egresos, carro_insumos, salas_con_epp,
    banos, pisos, da_aviso, chatas, aseo_terminal
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? , ? , ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssssssssssssssssssssssssssssssssssssss", 
    $fecha, $tipoturno, $horario, $nombre_tens_sala, 
    $paciente_1, $paciente_2, $paciente_3, $paciente_4, $paciente_5, $paciente_6, 
    $novedades, $equipos_prestamo, $servicio, $observaciones, 
    $funcionario_saliente_1, $nombre_funcionario_saliente_1, $pin_funcionario_saliente_1, $contrasena_saliente_1, 
    $funcionario_entrante_1, $nombre_funcionario_entrante_1, $pin_funcionario_entrante_1, 
    $saturometros, $alcoholes, $pendrive_pediatria, $tablillas, $libros, $pecheras, 
    $rotulos, $material_esteril, $fichas_medicas, $actualizacion_egresos, $carro_insumos, $salas_con_epp,
    $banos, $pisos, $da_aviso, $chatas, $aseo_terminal
);

if ($stmt->execute()) {
    // Guardar los datos en la sesión para generar el PDF
    $_SESSION['fecha'] = $fecha;
    $_SESSION['tipoturno'] = $tipoturno;
    $_SESSION['horario'] = $horario;
    $_SESSION['nombre_tens_sala'] = $nombre_tens_sala;
    $_SESSION['paciente_1'] = $paciente_1;
    $_SESSION['paciente_2'] = $paciente_2;
    $_SESSION['paciente_3'] = $paciente_3;
    $_SESSION['paciente_4'] = $paciente_4;
    $_SESSION['paciente_5'] = $paciente_5;
    $_SESSION['paciente_6'] = $paciente_6;
    $_SESSION['novedades'] = $novedades;
    $_SESSION['equipos_prestamo'] = $equipos_prestamo;
    $_SESSION['servicio'] = $servicio;
    $_SESSION['observaciones'] = $observaciones;
    $_SESSION['nombre_funcionario_saliente_1'] = $nombre_funcionario_saliente_1;
    $_SESSION['nombre_funcionario_entrante_1'] = $nombre_funcionario_entrante_1;

    // Redirigir para generar el PDF
    header("Location: generar_pdf_pd_pediatria_tens.php?id=" . $stmt->insert_id);
    exit();
} else {
    echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
}

$stmt->close();
$conn->close();
?>