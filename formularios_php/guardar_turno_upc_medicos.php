<?php
session_start();

// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'conexion.php';
mysqli_set_charset($conn, "utf8mb4");

date_default_timezone_set('America/Santiago');

// CONEXIÓN A BD
$servername = "localhost";  
$username = "root";         
$password = "";            
$dbname = "sistema_entrega_turnos_hsc"; 

// Crear conexión
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// CONSULTA PARA INSERTAR EN formulario_turnos_upc_medicos
$stmt = $mysqli->prepare("INSERT INTO formulario_turnos_upc_medicos (
    fecha, hora, cama_hospitalizado, nombre_hospitalizado, edad_hospitalizado, 
    eih_hospitalizado, diagnosticos_hospitalizado, novedades_hospitalizado, 
    planes_hospitalizado, nombre_egresado, destino_egresado, motivo_de_egreso, 
    nombre_fallecido, edad_fallecido, hora_fallecido, diagnosticos_fallecido, 
    servicio_fallecido, nombre_rechazadas, rut_rechazado, diagnostico_rechazado, 
    servicio_rechazado, motivo_rechazo, funcionario_saliente_1, 
    contrasena_saliente_1, funcionario_entrante_1, especialidad_saliente, 
    especialidad_entrante
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? , ?)");

// Verificar que la consulta se preparó correctamente
if (!$stmt) {
    die("Error en la preparación: " . $mysqli->error);
}

// VARIABLES A INSERTAR
$fecha = $_POST['fecha'];
$hora = $_POST['hora'];
$cama_hospitalizado = $_POST['cama_hospitalizado'];
$nombre_hospitalizado = $_POST['nombre_hospitalizado'];
$edad_hospitalizado = $_POST['edad_hospitalizado'];
$eih_hospitalizado = $_POST['eih_hospitalizado'];
$diagnosticos_hospitalizado = $_POST['diagnosticos_hospitalizado'];
$novedades_hospitalizado = $_POST['novedades_hospitalizado'];
$planes_hospitalizado = $_POST['planes_hospitalizado'];
$nombre_egresado = $_POST['nombre_egresado'];
$destino_egresado = $_POST['destino_egresado'];
$motivo_de_egreso = $_POST['motivo_de_egreso'];
$nombre_fallecido = $_POST['nombre_fallecido'];
$edad_fallecido = $_POST['edad_fallecido'];
$hora_fallecido = $_POST['hora_fallecido'];
$diagnosticos_fallecido = $_POST['diagnosticos_fallecido'];
$servicio_fallecido = $_POST['servicio_fallecido'];
$nombre_rechazadas = $_POST['nombre_rechazadas'];
$rut_rechazado = $_POST['rut_rechazado'];
$diagnostico_rechazado = $_POST['diagnostico_rechazado'];
$servicio_rechazado = $_POST['servicio_rechazado'];
$motivo_rechazo = $_POST['motivo_rechazo'];
$funcionario_saliente_1 = $_POST['funcionario_saliente_1'];
$contrasena_saliente_1 = $_POST['contrasena_saliente_1'];
$funcionario_entrante_1 = $_POST['funcionario_entrante_1'];
$especialidad_saliente = $_POST['especialidad_saliente'];
$especialidad_entrante = $_POST['especialidad_entrante'];

// Asignar los valores a la consulta
$stmt->bind_param(
    "ssissssssssssssssssssssssss", 
    $fecha, $hora, $cama_hospitalizado, $nombre_hospitalizado, $edad_hospitalizado, 
    $eih_hospitalizado, $diagnosticos_hospitalizado, $novedades_hospitalizado, 
    $planes_hospitalizado, $nombre_egresado, $destino_egresado, $motivo_de_egreso, 
    $nombre_fallecido, $edad_fallecido, $hora_fallecido, $diagnosticos_fallecido, 
    $servicio_fallecido, $nombre_rechazadas, $rut_rechazado, $diagnostico_rechazado, 
    $servicio_rechazado, $motivo_rechazo, $funcionario_saliente_1, 
    $contrasena_saliente_1, $funcionario_entrante_1, $especialidad_saliente, 
    $especialidad_entrante
);

// Ejecutar la consulta
if ($stmt->execute()) {
    // Obtener el ID del formulario insertado
    $id_formulario = $mysqli->insert_id;

    if ($id_formulario > 0) {
        // Redirigir al PHP que genera el PDF 
        header("Location: menu.php?id=" . $id_formulario);
        exit();
    } else {
        die("Error: No se pudo obtener el ID del formulario.");
    }
} else {
    echo "Error al insertar: " . $stmt->error;
}

// Cerrar consulta y conexión
$stmt->close();
$mysqli->close();  
?>