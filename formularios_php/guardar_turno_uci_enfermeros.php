<?php
// CONEXION A BD (EL INCLUDE CONEXION.PHP NO TOMABA)
$servername = "localhost";  
$username = "root";         
$password = "";            
$dbname = "sistema_entrega_turnos_hsc"; 

// CREAR CONEXION
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// CONSULTA DE LAS 37 COLUMNAS ASIGNADAS AL FORM
$stmt = $mysqli->prepare("INSERT INTO formulario_turnos_uci_enfermeros (
    fecha, tipoturno, medico_turno, tens_turno, auxiliar_turno, kinesiologo_turno, 
    controlmedico, carrodeparos, camas_ocupadas, camas_disponibles, camas_reservadas, cantpacientesfallecidos, 
    detallespacientesf, medicamento_ketamina, medicamento_haldol, medicamento_diazepam100ev, medicamento_diazepam100vo, 
    medicamento_rocuronio, medicamento_clonazepam, medicamento_midazolam5mg, medicamento_midazolam50mg, 
    medicamento_fentanilo0_1, medicamento_fentanilo_0_5mg, medicamento_lorazepam_4mg, medicamento_morfina, 
    medicamento_profolol, medicamento_quetiapina, medicamento_suxometonio, traslados_detalle, eventos_detalle, 
    comentarios_detalle, funcionario_saliente_1, contrasena_saliente_1, funcionario_saliente_2, contrasena_saliente_2, 
    funcionario_entrante_1, funcionario_entrante_2
) VALUES (? ,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

// VERIFICA QUE LA CONSULTA ESTE CORRECTAMENTE INICIADA
if (!$stmt) {
    die("Error en la preparación: " . $mysqli->error);
}

// VARIABLES A INSERTAR
$fecha = $_POST['fecha'] ?? NULL;
$tipoturno = $_POST['tipoturno'] ?? NULL;
$medico_turno = $_POST['medico_turno'] ?? NULL;
$tens_turno = $_POST['tens_turno'] ?? NULL;
$auxiliar_turno = $_POST['auxiliar_turno'] ?? NULL;
$kinesiologo_turno = $_POST['kinesiologo_turno'] ?? NULL;
$controlmedico = $_POST['controlmedico'] ?? NULL;
$carrodeparos = $_POST['carrodeparos'] ?? NULL;
$camas_ocupadas = $_POST['camas_ocupadas'] ?? NULL;
$camas_disponibles = $_POST['camas_disponibles'] ?? NULL;
$camas_reservadas = $_POST['camas_reservadas'] ?? NULL;
$cantpacientesfallecidos = $_POST['cantpacientesfallecidos'] ?? NULL;
$detallespacientesf = $_POST['detallespacientesf'] ?? NULL;
$medicamento_ketamina = $_POST['medicamento_ketamina'] ?? NULL;
$medicamento_haldol = $_POST['medicamento_haldol'] ?? NULL;
$medicamento_diazepam100ev = $_POST['medicamento_diazepam100ev'] ?? NULL;
$medicamento_diazepam100vo = $_POST['medicamento_diazepam100vo'] ?? NULL;
$medicamento_rocuronio = $_POST['medicamento_rocuronio'] ?? NULL;
$medicamento_clonazepam = $_POST['medicamento_clonazepam'] ?? NULL;
$medicamento_midazolam5mg = $_POST['medicamento_midazolam5mg'] ?? NULL;
$medicamento_midazolam50mg = $_POST['medicamento_midazolam50mg'] ?? NULL;
$medicamento_fentanilo0_1 = $_POST['medicamento_fentanilo0_1'] ?? NULL;
$medicamento_fentanilo_0_5mg = $_POST['medicamento_fentanilo_0_5mg'] ?? NULL;
$medicamento_lorazepam_4mg = $_POST['medicamento_lorazepam_4mg'] ?? NULL;
$medicamento_morfina = $_POST['medicamento_morfina'] ?? NULL;
$medicamento_profolol = $_POST['medicamento_profolol'] ?? NULL;
$medicamento_quetiapina = $_POST['medicamento_quetiapina'] ?? NULL;
$medicamento_suxometonio = $_POST['medicamento_suxometonio'] ?? NULL;
$traslados_detalle = $_POST['traslados_detalle'] ?? NULL;
$eventos_detalle = $_POST['eventos_detalle'] ?? NULL;
$comentarios_detalle = $_POST['comentarios_detalle'] ?? NULL;
$funcionario_saliente_1 = $_POST['funcionario_saliente_1'] ?? NULL;
$contrasena_saliente_1 = $_POST['contrasena_saliente_1'] ?? NULL;
$funcionario_saliente_2 = $_POST['funcionario_saliente_2'] ?? NULL;
$contrasena_saliente_2 = $_POST['contrasena_saliente_2'] ?? NULL;
$funcionario_entrante_1 = $_POST['funcionario_entrante_1'] ?? NULL;
$funcionario_entrante_2 = $_POST['funcionario_entrante_2'] ?? NULL;

// VALORES ASIGNADOS A LA CONSULTA
$stmt->bind_param(
    "sssssssssssssssiiiiiiiiiiiiiissssssss",
    $fecha, $tipoturno, $medico_turno, $tens_turno, $auxiliar_turno, $kinesiologo_turno, 
    $controlmedico, $carrodeparos, $camas_ocupadas, $camas_disponibles, $camas_reservadas, $cantpacientesfallecidos, 
    $detallespacientesf, $medicamento_ketamina, $medicamento_haldol, $medicamento_diazepam100ev, $medicamento_diazepam100vo, 
    $medicamento_rocuronio, $medicamento_clonazepam, $medicamento_midazolam5mg, $medicamento_midazolam50mg, 
    $medicamento_fentanilo0_1, $medicamento_fentanilo_0_5mg, $medicamento_lorazepam_4mg, $medicamento_morfina, 
    $medicamento_profolol, $medicamento_quetiapina, $medicamento_suxometonio, $traslados_detalle, $eventos_detalle, 
    $comentarios_detalle, $funcionario_saliente_1, $contrasena_saliente_1, $funcionario_saliente_2, $contrasena_saliente_2, 
    $funcionario_entrante_1, $funcionario_entrante_2
);

// EJECUTA LA CONSULTA 1 VEZ
if ($stmt->execute()) {
    // OBTIENE EL ID DEL FORMULARIO INSERTADO
    $id_formulario = $mysqli->insert_id;

    if ($id_formulario > 0) {
        // REDIRIGE AL PHP QUE GENERA EL PDF 
        header("Location: generar_pdf_uci_enfermeros.php?id=" . $id_formulario);
        exit();
    } else {
        die("Error: No se pudo obtener el ID del formulario.");
    }
} else {
    echo "Error al insertar: " . $stmt->error;
}

// CIERRA CONSULTA Y CONEXION
$stmt->close();
$mysqli->close();
?>