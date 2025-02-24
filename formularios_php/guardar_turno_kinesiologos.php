<?php
// CONEXIÓN A BD
$servername = "localhost";  
$username = "root";         
$password = "";            
$dbname = "sistema_entrega_turnos_hsc"; 

// CREAR CONEXIÓN
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Verifica CONEXION
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// INSERTAR EN formulario_turnos_uti_tens
$stmt = $mysqli->prepare("INSERT INTO formulario_turnos_uti_kinesiologos (
    fecha, tipoturno, camas_ocupadas, camas_disponibles, 
    cant_pacientes_fallecidos, detalles_pacientes_fallecidos, eventos_detalle, 
    acv_detalle, cantidad_setsuccion, Eventoskine_detalle, comentarios_detalle, 
    funcionario_saliente_1, contrasena_saliente_1, funcionario_entrante_1
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ,?)");

// VERIFICA
if (!$stmt) {
    die("Error en la preparación: " . $mysqli->error);
}

// VARIABLES A INSERTAR
$fecha = $_POST['fecha'];
$tipoturno = $_POST['tipoturno'];
$camas_ocupadas = $_POST['camas_ocupadas'];
$camas_disponibles = $_POST['camas_disponibles'];
$cant_pacientes_fallecidos = $_POST['cant_pacientes_fallecidos'];
$detalles_pacientes_fallecidos = $_POST['detalles_pacientes_fallecidos'];
$eventos_detalle = $_POST['eventos_detalle'];
$acv_detalle = $_POST['acv_detalle'];
$cantidad_setsuccion = $_POST['cantidad_setsuccion'];
$eventoskine_detalle = $_POST['Eventoskine_detalle'];
$comentarios_detalle = $_POST['comentarios_detalle'];
$funcionario_saliente_1 = $_POST['funcionario_saliente_1'];
$contrasena_saliente_1 = $_POST['contrasena_saliente_1'];
$funcionario_entrante_1 = $_POST['funcionario_entrante_1'];

// ASIGNAR LOS VALORES A LA CONSULTA
$stmt->bind_param(
    "ssiiisssisssss", 
    $fecha, $tipoturno, $camas_ocupadas, $camas_disponibles, 
    $cant_pacientes_fallecidos, $detalles_pacientes_fallecidos, $eventos_detalle, 
    $acv_detalle, $cantidad_setsuccion, $eventoskine_detalle, $comentarios_detalle, 
    $funcionario_saliente_1, $contrasena_saliente_1, $funcionario_entrante_1
);

// EJECUTAR LA CONSULTA
if ($stmt->execute()) {
    // OBTENER EL ID DEL FORMULARIO INSERTADO
    $id_formulario = $mysqli->insert_id;

    if ($id_formulario > 0) {
        // REDIRIGIR AL PHP QUE GENERA EL PDF 
        header("Location: generar_pdf_kinesiologos.php?id=" . $id_formulario);
        exit();
    } else {
        die("Error: No se pudo obtener el ID del formulario.");
    }
} else {
    echo "Error al insertar: " . $stmt->error;
}

// CERRAR CONSULTA Y CONEXIÓN
$stmt->close();
$mysqli->close();  
?>
