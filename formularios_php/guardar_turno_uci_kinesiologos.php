<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';

mysqli_set_charset($conn, "utf8mb4");

// POST CON VALIDACION
$fecha = isset($_POST['fecha']) ? $_POST['fecha'] : null;
$tipoturno = isset($_POST['tipoturno']) ? $_POST['tipoturno'] : null;
$camas_ocupadas = isset($_POST['camas_ocupadas']) ? $_POST['camas_ocupadas'] : null;
$camas_disponibles = isset($_POST['camas_disponibles']) ? $_POST['camas_disponibles'] : null;
$cant_pacientes_fallecidos = isset($_POST['cant_pacientes_fallecidos']) ? $_POST['cant_pacientes_fallecidos'] : null;
$detalles_pacientes_fallecidos = isset($_POST['detalles_pacientes_fallecidos']) ? $_POST['detalles_pacientes_fallecidos'] : null;
$eventos_detalle = isset($_POST['eventos_detalle']) ? $_POST['eventos_detalle'] : null;
$eventos_kine_detalle = isset($_POST['Eventoskine_detalle']) ? $_POST['Eventoskine_detalle'] : null;
$comentarios_detalle = isset($_POST['comentarios_detalle']) ? $_POST['comentarios_detalle'] : null;
$cantidad_setsuccion = isset($_POST['cantidad_setsuccion']) ? $_POST['cantidad_setsuccion'] : null;
$funcionario_saliente_1 = isset($_POST['funcionario_saliente_1']) ? $_POST['funcionario_saliente_1'] : null;
$contrasena_saliente_1 = isset($_POST['contrasena_saliente_1']) ? $_POST['contrasena_saliente_1']: null;
$funcionario_entrante_1 = isset($_POST['funcionario_entrante_1']) ? $_POST['funcionario_entrante_1'] : null;

//FUNCION PARA DETERMINAR DIA DE LA SEMANA DEPENDIENDO EL DIA 0 A 6 EN PHP
function esDomingo($fecha) {
    $diaSemana = date('w', strtotime($fecha)); // 0 es domingo, 6 es sábado
    return $diaSemana == 0;
}

$es_domingo = esDomingo($fecha);

if ($es_domingo) {
    $filtros_hme = isset($_POST['filtros_hme']) ? $_POST['filtros_hme'] : null;
    $filtros_antibacterianos = isset($_POST['filtros_antibacterianos']) ? $_POST['filtros_antibacterianos'] : null;
    $filtros_traqueostomia = isset($_POST['filtros_traqueostomia']) ? $_POST['filtros_traqueostomia'] : null;
    $sonda_succion_cerrada = isset($_POST['sonda_succion_cerrada']) ? $_POST['sonda_succion_cerrada'] : null;
    $corrugado_una_rama = isset($_POST['corrugado_una_rama']) ? $_POST['corrugado_una_rama'] : null;
    $corrugado_dos_ramas = isset($_POST['corrugado_dos_ramas']) ? $_POST['corrugado_dos_ramas'] : null;
    $adaptador_idm = isset($_POST['adaptador_idm']) ? $_POST['adaptador_idm'] : null;
    $adaptador_nbz = isset($_POST['adaptador_nbz']) ? $_POST['adaptador_nbz'] : null;
    $tubo_t = isset($_POST['tubo_t']) ? $_POST['tubo_t'] : null;
    $mascarillas_talla_s = isset($_POST['mascarillas_talla_s']) ? $_POST['mascarillas_talla_s'] : null;
    $mascarillas_talla_l = isset($_POST['mascarillas_talla_l']) ? $_POST['mascarillas_talla_l'] : null;
    $mascarillas_talla_xl = isset($_POST['mascarillas_talla_xl']) ? $_POST['mascarillas_talla_xl'] : null;
    $set_succion_unidad = isset($_POST['set_succion_unidad']) ? $_POST['set_succion_unidad'] : null;
} else {
    $filtros_hme = null;
    $filtros_antibacterianos = null;
    $filtros_traqueostomia = null;
    $sonda_succion_cerrada = null;
    $corrugado_una_rama = null;
    $corrugado_dos_ramas = null;
    $adaptador_idm = null;
    $adaptador_nbz = null;
    $tubo_t = null;
    $mascarillas_talla_s = null;
    $mascarillas_talla_l = null;
    $mascarillas_talla_xl = null;
    $set_succion_unidad = null;
}

// PREPARAR CONSULTA SQL  

$sql = "INSERT INTO formulario_turnos_uci_kinesiologos (
    fecha,
    tipoturno,
    es_domingo,
    camas_ocupadas,
    camas_disponibles,
    cant_pacientes_fallecidos,
    detalles_pacientes_fallecidos,
    eventos_detalle,
    eventos_kine_detalle,
    comentarios_detalle,
    cantidad_setsuccion,
    funcionario_saliente_1,
    contrasena_saliente_1,
    funcionario_entrante_1,
    filtros_hme,
    filtros_antibacterianos,
    filtros_traqueostomia,
    sonda_succion_cerrada,
    corrugado_una_rama,
    corrugado_dos_ramas,
    adaptador_idm,
    adaptador_nbz,
    tubo_t,
    mascarillas_talla_s,
    mascarillas_talla_l,
    mascarillas_talla_xl,
    set_succion_unidad
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";


$stmt = $conn->prepare($sql);


if ($stmt === false) {
    die("Error preparing the statement: " . $conn->error);
}

// PARAMETROS ENTREGADOS BIND
$stmt->bind_param(
    "ssiiissssisssiiiiiiiiiiiiii",
    $fecha,
    $tipoturno,
    $es_domingo,
    $camas_ocupadas,
    $camas_disponibles,
    $cant_pacientes_fallecidos,
    $detalles_pacientes_fallecidos,
    $eventos_detalle,
    $eventos_kine_detalle,
    $comentarios_detalle,
    $cantidad_setsuccion,
    $funcionario_saliente_1,
    $contrasena_saliente_1,
    $funcionario_entrante_1,
    $filtros_hme,
    $filtros_antibacterianos,
    $filtros_traqueostomia,
    $sonda_succion_cerrada,
    $corrugado_una_rama,
    $corrugado_dos_ramas,
    $adaptador_idm,
    $adaptador_nbz,
    $tubo_t,
    $mascarillas_talla_s,
    $mascarillas_talla_l,
    $mascarillas_talla_xl,
    $set_succion_unidad
);

// Ejecuta
if ($stmt->execute()) {
    // OBTENER EL ID DEL FORMULARIO INSERTADO
    $id_formulario = $conn->insert_id;

    if ($id_formulario > 0) {
        // REDIRIGIR AL PHP QUE GENERA EL PDF 
        header("Location: generar_pdf_uci_kinesiologos.php?id=" . $id_formulario);
        exit();
    } else {
        die("Error: No se pudo obtener el ID del formulario.");
    }
} else {
    echo "Error al insertar: " . $stmt->error;
}

// CIERRA STMT
$stmt->close();

// CIERRA LA CONEXION A BD
$conn->close();
?>