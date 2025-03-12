<?php
session_start();

// Verificar si el usuario inició sesión
if (!isset($_SESSION['id_usuarios'])) {
    header("Location: index.php");
    exit();
}

// Código para mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';

mysqli_set_charset($conn, "utf8mb4");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha = $_POST['fecha'] ?? null;
    $tipoturno = $_POST['tipoturno'] ?? null;
    $observaciones_equipo = $_POST['observaciones_equipo'] ?? null;
    $tecnicas_calibradas = $_POST['tecnicas_calibradas'] ?? null;
    $observaciones_quimica = $_POST['observaciones_quimica'] ?? null;
    $quimica = isset($_POST['quimica']) ? 1 : 0;
    $hormonas = isset($_POST['hormonas']) ? 1 : 0;
    $gases_elp = isset($_POST['gases_elp']) ? 1 : 0;
    $crd = isset($_POST['crd']) ? 1 : 0;
    $vih_hepb = isset($_POST['vih_hepb']) ? 1 : 0;
    $sp = isset($_POST['sp']) ? 1 : 0;
    $mantencion = $_POST['mantencion'] ?? null;
    $cobas_c311 = isset($_POST['cobas_c311']) ? 1 : 0;
    $cobas_c111 = isset($_POST['cobas_c111']) ? 1 : 0;
    $transfusiones = $_POST['transfusiones'] ?? null;
    $gr_0 = isset($_POST['gr_0']) ? 1 : 0;
    $gr_a = isset($_POST['gr_a']) ? 1 : 0;
    $gr_oneg = isset($_POST['gr_oneg']) ? 1 : 0;
    $gr_b = isset($_POST['gr_b']) ? 1 : 0;
    $gr_ab = isset($_POST['gr_ab']) ? 1 : 0;
    $pfc_o = isset($_POST['pfc_o']) ? 1 : 0;
    $pfc_a = isset($_POST['pfc_a']) ? 1 : 0;
    $pfc_b = isset($_POST['pfc_b']) ? 1 : 0;
    $pfc_ab = isset($_POST['pfc_ab']) ? 1 : 0;
    $muestras_pendientes = $_POST['muestras_pendientes'] ?? null;
    $valores_criticos = $_POST['valores_criticos'] ?? null;
    $gram_hemocultivo = $_POST['gram_hemocultivo'] ?? null;
    $gram_liquidos = $_POST['gram_liquidos'] ?? null;
    $paneles_pendientes = $_POST['paneles_pendientes'] ?? null;
    $cambios_lote = $_POST['cambios_lote'] ?? null;
    $insumos_criticos = $_POST['insumos_criticos'] ?? null;
    $pendientes_covid = $_POST['pendientes_covid'] ?? null;
    $funcionario_saliente_1 = $_POST['funcionario_saliente_1'] ?? null;
    $nombre_funcionario_saliente_1 = $_POST['nombre_funcionario_saliente_1'] ?? null;
    $funcionario_entrante_1 = $_POST['funcionario_entrante_1'] ?? null;
    $nombre_funcionario_entrante_1 = $_POST['nombre_funcionario_entrante_1'] ?? null;
    $contrasena_saliente_1 = $_POST['contrasena_saliente_1'] ?? null;

    $sql = "INSERT INTO formulario_turnos_mb_tecnologos_medicos (
        fecha, tipoturno, observaciones_equipo, tecnicas_calibradas, observaciones_quimica,
        quimica, hormonas, gases_elp, crd, vih_hepb, sp, mantencion, cobas_c311, cobas_c111,
        transfusiones, gr_0, gr_a, gr_oneg, gr_b, gr_ab, pfc_o, pfc_a, pfc_b, pfc_ab,
        muestras_pendientes, valores_criticos, gram_hemocultivo, gram_liquidos, paneles_pendientes,
        cambios_lote, insumos_criticos, pendientes_covid, funcionario_saliente_1,
        nombre_funcionario_saliente_1, funcionario_entrante_1,
        nombre_funcionario_entrante_1, contrasena_saliente_1
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? , ?
    )";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die('mysqli error: ' . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, 'ssssiiiiiiiiissiisiiiiiiiiissssssssss', 
    $fecha, $tipoturno, $observaciones_equipo, $tecnicas_calibradas, $observaciones_quimica,
    $quimica, $hormonas, $gases_elp, $crd, $vih_hepb, $sp, $mantencion, $cobas_c311, $cobas_c111,
    $transfusiones, $gr_0, $gr_a, $gr_oneg, $gr_b, $gr_ab, $pfc_o, $pfc_a, $pfc_b, $pfc_ab,
    $muestras_pendientes, $valores_criticos, $gram_hemocultivo, $gram_liquidos, $paneles_pendientes,
    $cambios_lote, $insumos_criticos, $pendientes_covid, $funcionario_saliente_1,
    $nombre_funcionario_saliente_1, $funcionario_entrante_1,
    $nombre_funcionario_entrante_1, $contrasena_saliente_1
);

    if (mysqli_stmt_execute($stmt)) {
        echo "Registro guardado exitosamente.";
    } else {
        echo "Error al guardar el registro: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>