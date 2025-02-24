<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// CONEXION A BD (EL INCLUDE CONEXION.PHP NO TOMABA)
$servername = "localhost";  
$username = "root";         
$password = "";            
$dbname = "sistema_entrega_turnos_hsc"; 

// CEAR CONECCION
$mysqli = new mysqli($servername, $username, $password, $dbname);


// Verificar conexión
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// CONSULTA DE LAS 43 COLUMNAS ASIGNADAS AL FORM
$stmt = $mysqli->prepare("INSERT INTO formulario_turnos_uti_enfermeros (
    fecha, tipoturno, medico_turno, tens_turno, auxiliar_turno, kinesiologo_turno, 
    control_medico_residente, carro_paros, botiquin, 
    camas_ocupadas, camas_disponibles, camas_reservadas, cant_pacientes_fallecidos, 
    detalles_pacientes_fallecidos, medicamento_propofol, medicamento_precedex, 
    medicamento_fenobarbital, medicamento_alprazolam_0_5mg, medicamento_haldol_5mg, 
    medicamento_diazepam_vo, medicamento_diazepam_ev, medicamento_clonazepam_0_5mg, 
    medicamento_clonazepam_2mg, medicamento_haloperidol_1mg, medicamento_ketamina, 
    medicamento_ramifentanilo_1mg, medicamento_lorazepam_2mg, medicamento_metadona, 
    medicamento_morfina, medicamento_midazolam_5mg, medicamento_midazolam_50mg, 
    medicamento_fentanilo_0_1mg, medicamento_fentanilo_0_5mg, medicamento_otros, 
    traslados_detalle, eventos_detalle, comentarios_detalle, 
    funcionario_saliente_1, funcionario_saliente_2, contrasena_saliente_1, contrasena_saliente_2, 
    funcionario_entrante_1, funcionario_entrante_2
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

// VERIFICA QUE LA CONSULTA ESTE CORRECTAMENTE INICIADA
if (!$stmt) {
    die("Error en la preparación: " . $mysqli->error);
}

// VARIABLES A INSERTAR
$fecha = $_POST['fecha'];
$tipoturno = $_POST['tipoturno'];
$medico_turno = $_POST['medico_turno'];
$tens_turno = $_POST['tens_turno'];
$auxiliar_turno = $_POST['auxiliar_turno'];
$kinesiologo_turno = $_POST['kinesiologo_turno'];
$control_medico_residente = $_POST['control_medico_residente'];
$carro_paros = $_POST['carro_paros'];
$botiquin = $_POST['botiquin'];
$camas_ocupadas = $_POST['camas_ocupadas'];
$camas_disponibles = $_POST['camas_disponibles'];
$camas_reservadas = $_POST['camas_reservadas'];
$cant_pacientes_fallecidos = $_POST['cant_pacientes_fallecidos'];
$detalles_pacientes_fallecidos = $_POST['detalles_pacientes_fallecidos'];
$medicamento_propofol = $_POST['medicamento_propofol'];
$medicamento_precedex = $_POST['medicamento_precedex'];
$medicamento_fenobarbital = $_POST['medicamento_fenobarbital'];
$medicamento_alprazolam_0_5mg = $_POST['medicamento_alprazolam_0_5mg'];
$medicamento_haldol_5mg = $_POST['medicamento_haldol_5mg'];
$medicamento_diazepam_vo = $_POST['medicamento_diazepam_vo'];
$medicamento_diazepam_ev = $_POST['medicamento_diazepam_ev'];
$medicamento_clonazepam_0_5mg = $_POST['medicamento_clonazepam_0_5mg'];
$medicamento_clonazepam_2mg = $_POST['medicamento_clonazepam_2mg'];
$medicamento_haloperidol_1mg = $_POST['medicamento_haloperidol_1mg'];
$medicamento_ketamina = $_POST['medicamento_ketamina'];
$medicamento_ramifentanilo_1mg = $_POST['medicamento_ramifentanilo_1mg'];
$medicamento_lorazepam_2mg = $_POST['medicamento_lorazepam_2mg'];
$medicamento_metadona = $_POST['medicamento_metadona'];
$medicamento_morfina = $_POST['medicamento_morfina'];
$medicamento_midazolam_5mg = $_POST['medicamento_midazolam_5mg'];
$medicamento_midazolam_50mg = $_POST['medicamento_midazolam_50mg'];
$medicamento_fentanilo_0_1mg = $_POST['medicamento_fentanilo_0_1mg'];
$medicamento_fentanilo_0_5mg = $_POST['medicamento_fentanilo_0_5mg'];
$medicamento_otros = $_POST['medicamento_otros'];
$traslados_detalle = $_POST['traslados_detalle'];
$eventos_detalle = $_POST['eventos_detalle'];
$comentarios_detalle = $_POST['comentarios_detalle'];
$funcionario_saliente_1 = $_POST['funcionario_saliente_1'];
$funcionario_saliente_2 = $_POST['funcionario_saliente_2'];
$contrasena_saliente_1 = $_POST['contrasena_saliente_1'];
$contrasena_saliente_2 = $_POST['contrasena_saliente_2'];
$funcionario_entrante_1 = $_POST['funcionario_entrante_1'];
$funcionario_entrante_2 = $_POST['funcionario_entrante_2'];

// VALORES ASIGNADOS A LA CONSULTA
$stmt->bind_param(
    "sssssssssiiiisiiiiiiiiiiiiiiiiiiissssssssss",
    $fecha, $tipoturno, $medico_turno, $tens_turno, $auxiliar_turno, $kinesiologo_turno, 
    $control_medico_residente, $carro_paros, $botiquin, 
    $camas_ocupadas, $camas_disponibles, $camas_reservadas, $cant_pacientes_fallecidos, 
    $detalles_pacientes_fallecidos, $medicamento_propofol, $medicamento_precedex, 
    $medicamento_fenobarbital, $medicamento_alprazolam_0_5mg, $medicamento_haldol_5mg, 
    $medicamento_diazepam_vo, $medicamento_diazepam_ev, $medicamento_clonazepam_0_5mg, 
    $medicamento_clonazepam_2mg, $medicamento_haloperidol_1mg, $medicamento_ketamina, 
    $medicamento_ramifentanilo_1mg, $medicamento_lorazepam_2mg, $medicamento_metadona, 
    $medicamento_morfina, $medicamento_midazolam_5mg, $medicamento_midazolam_50mg, 
    $medicamento_fentanilo_0_1mg, $medicamento_fentanilo_0_5mg, $medicamento_otros, 
    $traslados_detalle, $eventos_detalle, $comentarios_detalle, 
    $funcionario_saliente_1, $funcionario_saliente_2, $contrasena_saliente_1, $contrasena_saliente_2, 
    $funcionario_entrante_1, $funcionario_entrante_2
);

// EJECUTA LA CONSULTA 1 VEZ
if ($stmt->execute()) {
    // OBTIENE EL ID DEL FORMULARIO INSERTADO
    $id_formulario = $mysqli->insert_id;

    if ($id_formulario > 0) {
        // REDIRIGE AL PHP QUE GENERA EL PDF 
        header("Location: generar_pdf_enfermeros.php?id=" . $id_formulario);
        exit();
    } else {
        die("Error: No se pudo obtener el ID del formulario.");
    }
} else {
    echo "Error al insertar: " . $stmt->error;
}

// CIRRA CONSULTA Y CONEXION
$stmt->close();
$mysqli->close();
