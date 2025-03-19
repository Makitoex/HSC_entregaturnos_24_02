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
    // Validación y sanitización de datos recibidos
    $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : null;
    $tipoturno = isset($_POST['tipoturno']) ? $_POST['tipoturno'] : null;
    $observaciones_equipo = isset($_POST['observaciones_equipo']) ? $_POST['observaciones_equipo'] : null;
    $tecnicas_calibradas = isset($_POST['tecnicas_calibradas']) ? $_POST['tecnicas_calibradas'] : null;
    $observaciones_quimica = isset($_POST['observaciones_quimica']) ? $_POST['observaciones_quimica'] : null;
    $quimica = isset($_POST['quimica']) ? 1 : 0;
    $hormonas = isset($_POST['hormonas']) ? 1 : 0;
    $gases_elp = isset($_POST['gases_elp']) ? 1 : 0;
    $crd = isset($_POST['crd']) ? 1 : 0;
    $vih_hepb = isset($_POST['vih_hepb']) ? 1 : 0;
    $sp = isset($_POST['sp']) ? 1 : 0;
    $mantencion = isset($_POST['mantencion']) ? $_POST['mantencion'] : null;
    $cobas_c311 = isset($_POST['cobas_c311']) ? 1 : 0;
    $cobas_c111 = isset($_POST['cobas_c111']) ? 1 : 0;
    $transfusiones = isset($_POST['transfusiones']) ? $_POST['transfusiones'] : null;
    $gr_0 = isset($_POST['gr_0']) ? 1 : 0;
    $gr_a = isset($_POST['gr_a']) ? 1 : 0;
    $gr_oneg = isset($_POST['gr_oneg']) ? 1 : 0;
    $gr_b = isset($_POST['gr_b']) ? 1 : 0;
    $gr_ab = isset($_POST['gr_ab']) ? 1 : 0;
    $pfc_o = isset($_POST['pfc_o']) ? 1 : 0;
    $pfc_a = isset($_POST['pfc_a']) ? 1 : 0;
    $pfc_b = isset($_POST['pfc_b']) ? 1 : 0;
    $pfc_ab = isset($_POST['pfc_ab']) ? 1 : 0;
    $muestras_pendientes = isset($_POST['muestras_pendientes']) ? $_POST['muestras_pendientes'] : null;
    $valores_criticos = isset($_POST['valores_criticos']) ? $_POST['valores_criticos'] : null;
    $gram_hemocultivo = isset($_POST['gram_hemocultivo']) ? $_POST['gram_hemocultivo'] : null;
    $gram_liquidos = isset($_POST['gram_liquidos']) ? $_POST['gram_liquidos'] : null;
    $paneles_pendientes = isset($_POST['paneles_pendientes']) ? $_POST['paneles_pendientes'] : null;
    $cambios_lote = isset($_POST['cambios_lote']) ? $_POST['cambios_lote'] : null;
    $insumos_criticos = isset($_POST['insumos_criticos']) ? $_POST['insumos_criticos'] : null;
    $pendientes_covid = isset($_POST['pendientes_covid']) ? $_POST['pendientes_covid'] : null;

    // Nuevas columnas
    $observaciones_equipo_largo = isset($_POST['observaciones_equipo_largo']) ? $_POST['observaciones_equipo_largo'] : null;
    $mantencion_largo = isset($_POST['mantencion_largo']) ? $_POST['mantencion_largo'] : null;
    $cobas_e411_largo = isset($_POST['cobas_e411_largo']) ? 1 : 0;
    $transfusiones_largo = isset($_POST['transfusiones_largo']) ? $_POST['transfusiones_largo'] : null;
    $gr_0_largo = isset($_POST['gr_0_largo']) ? 1 : 0;
    $gr_a_largo = isset($_POST['gr_a_largo']) ? 1 : 0;
    $gr_oneg_largo = isset($_POST['gr_oneg_largo']) ? 1 : 0;
    $gr_ab_largo = isset($_POST['gr_ab_largo']) ? 1 : 0;
    $pfc_o_largo = isset($_POST['pfc_o_largo']) ? 1 : 0;
    $pfc_a_largo = isset($_POST['pfc_a_largo']) ? 1 : 0;
    $pfc_b_largo = isset($_POST['pfc_b_largo']) ? 1 : 0;
    $pfc_ab_largo = isset($_POST['pfc_ab_largo']) ? 1 : 0;
    $muestras_pendientes_largo = isset($_POST['muestras_pendientes_largo']) ? $_POST['muestras_pendientes_largo'] : null;
    $valores_criticos_largo = isset($_POST['valores_criticos_largo']) ? $_POST['valores_criticos_largo'] : null;
    $gram_hemocultivo_largo = isset($_POST['gram_hemocultivo_largo']) ? $_POST['gram_hemocultivo_largo'] : null;
    $gram_liquidos_largo = isset($_POST['gram_liquidos_largo']) ? $_POST['gram_liquidos_largo'] : null;
    $paneles_pendientes_largo = isset($_POST['paneles_pendientes_largo']) ? $_POST['paneles_pendientes_largo'] : null;
    $cambios_lote_largo = isset($_POST['cambios_lote_largo']) ? $_POST['cambios_lote_largo'] : null;
    $insumos_criticos_largo = isset($_POST['insumos_criticos_largo']) ? $_POST['insumos_criticos_largo'] : null;
    $pendientes_covid_largo = isset($_POST['pendientes_covid_largo']) ? $_POST['pendientes_covid_largo'] : null;

    // Funcionario saliente y entrante
    $funcionario_saliente = isset($_POST['funcionario_saliente']) ? trim($_POST['funcionario_saliente']) : null;
    $nombre_funcionario_saliente = isset($_POST['nombre_funcionario_saliente']) ? trim($_POST['nombre_funcionario_saliente']) : null;
    $funcionario_entrante = isset($_POST['funcionario_entrante']) ? trim($_POST['funcionario_entrante']) : null;
    $nombre_funcionario_entrante = isset($_POST['nombre_funcionario_entrante']) ? trim($_POST['nombre_funcionario_entrante']) : null;
    $contrasena_saliente = isset($_POST['contrasena_saliente']) ? trim($_POST['contrasena_saliente']) : null;

    // Preparar la consulta SQL
    $sql = "INSERT INTO formulario_turnos_mb_tecnologos_medicos (
        fecha, tipoturno, observaciones_equipo, tecnicas_calibradas, observaciones_quimica,
        quimica, hormonas, gases_elp, crd, vih_hepb, sp, mantencion, cobas_c311, cobas_c111,
        transfusiones, gr_0, gr_a, gr_oneg, gr_b, gr_ab, pfc_o, pfc_a, pfc_b, pfc_ab,
        muestras_pendientes, valores_criticos, gram_hemocultivo, gram_liquidos, paneles_pendientes,
        cambios_lote, insumos_criticos, pendientes_covid, funcionario_saliente,
        nombre_funcionario_saliente, funcionario_entrante, nombre_funcionario_entrante, contrasena_saliente,
        observaciones_equipo_largo, mantencion_largo, cobas_e411_largo, transfusiones_largo, 
        gr_0_largo, gr_a_largo, gr_oneg_largo, gr_ab_largo, pfc_o_largo, pfc_a_largo, pfc_b_largo, 
        pfc_ab_largo, muestras_pendientes_largo, valores_criticos_largo, gram_hemocultivo_largo, 
        gram_liquidos_largo, paneles_pendientes_largo, cambios_lote_largo, insumos_criticos_largo, 
        pendientes_covid_largo
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? , ?
    )";

    // Preparar y ejecutar la consulta con parámetros
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, 'sssssiiiiiisiisiiiiiiiiissssssssisissssisiiiiiiiissssssss', 
            $fecha, $tipoturno, $observaciones_equipo, $tecnicas_calibradas, $observaciones_quimica,
            $quimica, $hormonas, $gases_elp, $crd, $vih_hepb, $sp, $mantencion, $cobas_c311, $cobas_c111,
            $transfusiones, $gr_0, $gr_a, $gr_oneg, $gr_b, $gr_ab, $pfc_o, $pfc_a, $pfc_b, $pfc_ab,
            $muestras_pendientes, $valores_criticos, $gram_hemocultivo, $gram_liquidos, $paneles_pendientes,
            $cambios_lote, $insumos_criticos, $pendientes_covid, $funcionario_saliente,
            $nombre_funcionario_saliente, $funcionario_entrante, $nombre_funcionario_entrante, $contrasena_saliente,
            $observaciones_equipo_largo, $mantencion_largo, $cobas_e411_largo, $transfusiones_largo, 
            $gr_0_largo, $gr_a_largo, $gr_oneg_largo, $gr_ab_largo, $pfc_o_largo, $pfc_a_largo, $pfc_b_largo, 
            $pfc_ab_largo, $muestras_pendientes_largo, $valores_criticos_largo, $gram_hemocultivo_largo, 
            $gram_liquidos_largo, $paneles_pendientes_largo, $cambios_lote_largo, $insumos_criticos_largo, 
            $pendientes_covid_largo
        );

        if (mysqli_stmt_execute($stmt)) {
            // OBTENER EL ID DEL FORMULARIO INSERTADO
            $id_formulario = mysqli_insert_id($conn);

            if ($id_formulario > 0) {
                // REDIRIGIR AL PHP QUE GENERA EL PDF 
                header("Location: generar_pdf_mb_microbiologia_tecnologos.php?id=" . $id_formulario);
                exit();
            } else {
                die("Error: No se pudo obtener el ID del formulario.");
            }
        } else {
            // Log de errores para evitar mostrar detalles sensibles al usuario
            error_log("Error al guardar el registro: " . mysqli_error($conn));
            echo "Ocurrió un error al guardar el registro. Por favor, intente más tarde.";
        }

        // Cerrar el statement y la conexión
        mysqli_stmt_close($stmt);
    } else {
        // Log de error si la preparación de la consulta falla
        error_log("Error en la preparación de la consulta: " . mysqli_error($conn));
        echo "Ocurrió un error al preparar la consulta. Por favor, intente más tarde.";
    }

    mysqli_close($conn);
}
?>