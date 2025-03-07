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

// INSERTAR EN formulario_turnos_im_tecnologos_medicos
$stmt = $mysqli->prepare("INSERT INTO formulario_turnos_im_tecnologos_medicos (
    fecha, tipoturno, rx_pendientes, tc_pendientes, portatil_pendientes, 
    rx_equiposoperativos, tc_equiposoperativos, portatil_equiposoperativos, 
    pacs_enviados, prueba_enviados, syngovia_enviados, codigo_carroparos, 
    carrodeparos, carroutilizado, salasyrx, inyectora, cd_grabados, 
    cd_grabadosotroturno, eventosadversos, pacientessospecha, novedades, 
    funcionario_saliente_1, contrasena_saliente_1, funcionario_entrante_1 ,
    nombre_funcionario_saliente_1, nombre_funcionario_entrante_1
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? , ? , ?)");

// VERIFICA
if (!$stmt) {
    die("Error en la preparación: " . $mysqli->error);
}

// VARIABLES A INSERTAR
$fecha = $_POST['fecha'];
$tipoturno = $_POST['tipoturno'];
$rx_pendientes = $_POST['rx_pendientes'];
$tc_pendientes = $_POST['tc_pendientes'];
$portatil_pendientes = $_POST['portatil_pendientes'];
$rx_equiposoperativos = $_POST['rx_equiposoperativos'];
$tc_equiposoperativos = $_POST['tc_equiposoperativos'];
$portatil_equiposoperativos = $_POST['portatil_equiposoperativos'];
$pacs_enviados = $_POST['pacs_enviados'];
$prueba_enviados = $_POST['prueba_enviados'];
$syngovia_enviados = $_POST['syngovia_enviados'];
$codigo_carroparos = $_POST['codigo_carroparos'];
$carrodeparos = $_POST['carrodeparos'];
$carroutilizado = $_POST['carroutilizado'];
$salasyrx = $_POST['salasyrx'];
$inyectora = $_POST['inyectora'];
$cd_grabados = $_POST['cd_grabados'];
$cd_grabadosotroturno = $_POST['cd_grabadosotroturno'];
$eventosadversos = $_POST['eventosadversos'];
$pacientessospecha = $_POST['pacientessospecha'];
$novedades = $_POST['novedades'];
$funcionario_saliente_1 = $_POST['funcionario_saliente_1'];
$contrasena_saliente_1 = $_POST['contrasena_saliente_1'];
$funcionario_entrante_1 = $_POST['funcionario_entrante_1'];
$nombre_funcionario_saliente_1 = $_POST['nombre_funcionario_saliente_1'];
$nombre_funcionario_entrante_1 = $_POST['nombre_funcionario_entrante_1'];

// ASIGNAR LOS VALORES A LA CONSULTA
$stmt->bind_param(
    "ssssssssssssssssssssssssss", 
    $fecha, $tipoturno, $rx_pendientes, $tc_pendientes, $portatil_pendientes, 
    $rx_equiposoperativos, $tc_equiposoperativos, $portatil_equiposoperativos, 
    $pacs_enviados, $prueba_enviados, $syngovia_enviados, $codigo_carroparos, 
    $carrodeparos, $carroutilizado, $salasyrx, $inyectora, $cd_grabados, 
    $cd_grabadosotroturno, $eventosadversos, $pacientessospecha, $novedades, 
    $funcionario_saliente_1, $contrasena_saliente_1, $funcionario_entrante_1 ,
    $nombre_funcionario_saliente_1, $nombre_funcionario_entrante_1
);

// EJECUTAR LA CONSULTA
if ($stmt->execute()) {
    // OBTENER EL ID DEL FORMULARIO INSERTADO
    $id_formulario = $mysqli->insert_id;

    if ($id_formulario > 0) {
        // REDIRIGIR AL PHP QUE GENERA EL PDF 
        header("Location: generar_pdf_im_tecnologos_medicos.php?id=" . $id_formulario);
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