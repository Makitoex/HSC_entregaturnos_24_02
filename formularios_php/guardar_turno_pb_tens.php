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

include '/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';

// Verificar si las variables $_POST están definidas antes de usarlas
$fecha = isset($_POST['fecha']) ? $_POST['fecha'] : null;
$tipoturno = isset($_POST['tipoturno']) ? $_POST['tipoturno'] : null;
$nombre_anestesiologo_turno = isset($_POST['nombre_anestesiologo_turno']) ? $_POST['nombre_anestesiologo_turno'] : null;
$nombre_enfermera_turno = isset($_POST['nombre_enfermera_turno']) ? $_POST['nombre_enfermera_turno'] : null;
$nombre_tecanestesia_turno = isset($_POST['nombre_tecanestesia_turno']) ? $_POST['nombre_tecanestesia_turno'] : null;
$reposicion_peridural = isset($_POST['reposicion_peridural']) ? $_POST['reposicion_peridural'] : null;
$reposicion_carro_anestesia = isset($_POST['reposicion_carro_anestesia']) ? $_POST['reposicion_carro_anestesia'] : null;
$eliminacion_medicamentos = isset($_POST['eliminacion_medicamentos']) ? $_POST['eliminacion_medicamentos'] : null;
$novedades = isset($_POST['novedades']) ? $_POST['novedades'] : null;
$arsenalero = isset($_POST['arsenalero']) ? $_POST['arsenalero'] : null;
$insumos_empresa_externa = isset($_POST['insumos_empresa_externa']) ? $_POST['insumos_empresa_externa'] : null;
$stock_instrumental = isset($_POST['stock_instrumental']) ? $_POST['stock_instrumental'] : null;
$novedades_instrumental = isset($_POST['novedades_instrumental']) ? $_POST['novedades_instrumental'] : null;
$pabellonero = isset($_POST['pabellonero']) ? $_POST['pabellonero'] : null;
$reposicion_carro_recuperacion = isset($_POST['reposicion_carro_recuperacion']) ? $_POST['reposicion_carro_recuperacion'] : null;
$cambio_humidificadores = isset($_POST['cambio_humidificadores']) ? $_POST['cambio_humidificadores'] : null;
$revision_temperaturas_pabellones = isset($_POST['revision_temperaturas_pabellones']) ? $_POST['revision_temperaturas_pabellones'] : null;
$pacientes_cma = isset($_POST['pacientes_cma']) ? $_POST['pacientes_cma'] : null;
$biopsias_ordenes_recibidas = isset($_POST['biopsias_ordenes_recibidas']) ? $_POST['biopsias_ordenes_recibidas'] : null;
$biopsias_ordenes_entregadas = isset($_POST['biopsias_ordenes_entregadas']) ? $_POST['biopsias_ordenes_entregadas'] : null;
$limpieza_pyxis = isset($_POST['limpieza_pyxis']) ? $_POST['limpieza_pyxis'] : null;
$limpieza_bodegas = isset($_POST['limpieza_bodegas']) ? $_POST['limpieza_bodegas'] : null;
$funcionario_tecanestesia = isset($_POST['funcionario_tecanestesia']) ? $_POST['funcionario_tecanestesia'] : null;
$contrasena_tecanestesia = isset($_POST['contrasena_tecanestesia']) ? $_POST['contrasena_tecanestesia'] : null;
$nombre_funcionario_tecanestesia = isset($_POST['nombre_funcionario_tecanestesia']) ? $_POST['nombre_funcionario_tecanestesia'] : null;
$funcionario_arsenalero = isset($_POST['funcionario_arsenalero']) ? $_POST['funcionario_arsenalero'] : null;
$contrasena_arsenalero = isset($_POST['contrasena_arsenalero']) ? $_POST['contrasena_arsenalero'] : null;
$nombre_funcionario_arsenalero = isset($_POST['nombre_funcionario_arsenalero']) ? $_POST['nombre_funcionario_arsenalero'] : null;
$funcionario_pabellonero = isset($_POST['funcionario_pabellonero']) ? $_POST['funcionario_pabellonero'] : null;
$contrasena_pabellonero = isset($_POST['contrasena_pabellonero']) ? $_POST['contrasena_pabellonero'] : null;
$nombre_funcionario_pabellonero = isset($_POST['nombre_funcionario_pabellonero']) ? $_POST['nombre_funcionario_pabellonero'] : null;

// Insertar en turnos_pabellon_tens
$query = "INSERT INTO formulario_turnos_pb_tens (
    fecha, tipoturno, nombre_anestesiologo_turno, nombre_enfermera_turno, nombre_tecanestesia_turno,
    reposicion_peridural, reposicion_carro_anestesia, eliminacion_medicamentos, novedades, arsenalero,
    insumos_empresa_externa, stock_instrumental, novedades_instrumental, pabellonero,
    reposicion_carro_recuperacion, cambio_humidificadores, revision_temperaturas_pabellones,
    pacientes_cma, biopsias_ordenes_recibidas, biopsias_ordenes_entregadas, limpieza_pyxis, limpieza_bodegas,
    funcionario_tecanestesia, contrasena_tecanestesia, nombre_funcionario_tecanestesia,
    funcionario_arsenalero, contrasena_arsenalero, nombre_funcionario_arsenalero,
    funcionario_pabellonero, contrasena_pabellonero, nombre_funcionario_pabellonero
) VALUES (
    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? , ? 
)";

// Preparar la consulta
$stmt = $conn->prepare($query);

// Verificar
if (!$stmt) {
    die("Error en la preparación: " . $conn->error);
}

// Asignar los valores a la consulta
$stmt->bind_param(
    "sssssssssssssssssssssssssssssss",
    $fecha, $tipoturno, $nombre_anestesiologo_turno, $nombre_enfermera_turno, $nombre_tecanestesia_turno,
    $reposicion_peridural, $reposicion_carro_anestesia, $eliminacion_medicamentos, $novedades, $arsenalero,
    $insumos_empresa_externa, $stock_instrumental, $novedades_instrumental, $pabellonero,
    $reposicion_carro_recuperacion, $cambio_humidificadores, $revision_temperaturas_pabellones,
    $pacientes_cma, $biopsias_ordenes_recibidas, $biopsias_ordenes_entregadas, $limpieza_pyxis, $limpieza_bodegas,
    $funcionario_tecanestesia, $contrasena_tecanestesia, $nombre_funcionario_tecanestesia,
    $funcionario_arsenalero, $contrasena_arsenalero, $nombre_funcionario_arsenalero,
    $funcionario_pabellonero, $contrasena_pabellonero, $nombre_funcionario_pabellonero
);

// Ejecutar la consulta
if ($stmt->execute()) {
    // Obtener el ID del formulario insertado
    $id_formulario = $stmt->insert_id;

    if ($id_formulario > 0) {
        // Redirigir al PHP que genera el PDF 
        header("Location: generar_pdf_pb_tens.php?id=" . $id_formulario);
        exit();
    } else {
        die("Error: No se pudo obtener el ID del formulario.");
    }
} else {
    echo "Error al insertar: " . $stmt->error;
}

// Cerrar consulta y conexión
$stmt->close();
$conn->close();
?>