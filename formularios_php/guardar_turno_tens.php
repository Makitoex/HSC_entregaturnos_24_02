<?php
// CONEXIÓN A BD
$servername = "localhost";  
$username = "root";         
$password = "";            
$dbname = "sistema_entrega_turnos_hsc"; 

// CREAR CONEXIÓN
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// CONSULTA PARA INSERTAR EN formulario_turnos_uti_tens
$stmt = $mysqli->prepare("INSERT INTO formulario_turnos_uti_tens (
    fecha, tipoturno, medico_turno, control_medico_residente, 
    camas_ocupadas, camas_disponibles, camas_reservadas, cant_pacientes_fallecidos, 
    detalles_pacientes_fallecidos, eventos_detalle, comentarios_detalle, 
    funcionario_saliente_1, funcionario_saliente_2, funcionario_saliente_3, 
    contrasena_saliente_1, contrasena_saliente_2, contrasena_saliente_3, 
    funcionario_entrante_1, funcionario_entrante_2, funcionario_entrante_3 , nombre_funcionario_entrante_1 ,nombre_funcionario_entrante_2 , nombre_funcionario_entrante_3,
    nombre_funcionario_saliente_1,nombre_funcionario_saliente_2,nombre_funcionario_saliente_3
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? , ?,?,?,?,?,?)");

// VERIFICAR QUE LA CONSULTA SE PREPARÓ CORRECTAMENTE
if (!$stmt) {
    die("Error en la preparación: " . $mysqli->error);
}

// VARIABLES A INSERTAR
$fecha = $_POST['fecha'];
$tipoturno = $_POST['tipoturno'];
$medico_turno = $_POST['medico_turno'];
$control_medico_residente = $_POST['control_medico_residente'];
$camas_ocupadas = $_POST['camas_ocupadas'];
$camas_disponibles = $_POST['camas_disponibles'];
$camas_reservadas = $_POST['camas_reservadas'];
$cant_pacientes_fallecidos = $_POST['cant_pacientes_fallecidos'];
$detalles_pacientes_fallecidos = $_POST['detalles_pacientes_fallecidos'];
$eventos_detalle = $_POST['eventos_detalle'];
$comentarios_detalle = $_POST['comentarios_detalle'];
$funcionario_saliente_1 = $_POST['funcionario_saliente_1'];
$funcionario_saliente_2 = $_POST['funcionario_saliente_2'];
$funcionario_saliente_3 = $_POST['funcionario_saliente_3'];
$contrasena_saliente_1 = $_POST['contrasena_saliente_1'];
$contrasena_saliente_2 = $_POST['contrasena_saliente_2'];
$contrasena_saliente_3 = $_POST['contrasena_saliente_3'];
$funcionario_entrante_1 = $_POST['funcionario_entrante_1'];
$funcionario_entrante_2 = $_POST['funcionario_entrante_2'];
$funcionario_entrante_3 = $_POST['funcionario_entrante_3'];
$nombre_funcionario_saliente_1 = $_POST['nombre_funcionario_saliente_1'];
$nombre_funcionario_saliente_2 = $_POST['nombre_funcionario_saliente_2'];
$nombre_funcionario_saliente_3 = $_POST['nombre_funcionario_saliente_3'];
$nombre_funcionario_entrante_1 = $_POST['nombre_funcionario_entrante_1'];
$nombre_funcionario_entrante_2 = $_POST['nombre_funcionario_entrante_2'];
$nombre_funcionario_entrante_3 = $_POST['nombre_funcionario_entrante_3'];
// ASIGNAR LOS VALORES A LA CONSULTA
$stmt->bind_param(
    "ssssiiisssssssssssssssssss",
    $fecha, $tipoturno, $medico_turno, $control_medico_residente, 
    $camas_ocupadas, $camas_disponibles, $camas_reservadas, $cant_pacientes_fallecidos, 
    $detalles_pacientes_fallecidos, $eventos_detalle, $comentarios_detalle, 
    $funcionario_saliente_1, $funcionario_saliente_2, $funcionario_saliente_3, 
    $contrasena_saliente_1, $contrasena_saliente_2, $contrasena_saliente_3, 
    $funcionario_entrante_1, $funcionario_entrante_2, $funcionario_entrante_3 , $nombre_funcionario_saliente_1 , $nombre_funcionario_saliente_2 , $nombre_funcionario_saliente_3 ,
    $nombre_funcionario_entrante_1 ,$nombre_funcionario_entrante_2,$nombre_funcionario_entrante_3
);

// EJECUTAR LA CONSULTA
if ($stmt->execute()) {
    // OBTENER EL ID DEL FORMULARIO INSERTADO
    $id_formulario = $mysqli->insert_id;

    if ($id_formulario > 0) {
        // REDIRIGIR AL PHP QUE GENERA EL PDF 
        header("Location: generar_pdf_uti_tens.php?id=" . $id_formulario);
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
