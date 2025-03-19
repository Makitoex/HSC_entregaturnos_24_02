<?php
// CONEXIÓN A BD
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sistema_entrega_turnos_hsc";

// CREAR CONEXIÓN
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Verifica CONEXIÓN
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// Verifica si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // VARIABLES A INSERTAR (Verifica si existen en $_POST)
    $fecha = $_POST['fecha'] ?? '';
    $tipoturno = $_POST['tipoturno'] ?? '';
    $anestesiologo_turno = $_POST['anestesiologo_turno'] ?? '';
    $tens_turno = $_POST['tens_turno'] ?? '';
    $biopsias_recibidas = $_POST['biopsias_recibidas'] ?? '';
    $biopsias_entregadas = $_POST['biopsias_entregadas'] ?? '';
    $revision_carro_paro = $_POST['revision_carro_paro'] ?? '';
    $revision_carro_recuperacion = $_POST['revision_carro_recuperacion'] ?? '';
    $stock_minimo_medicamentos = $_POST['stock_minimo_medicamentos'] ?? '';
    $stock_minimo_insumos = $_POST['stock_minimo_insumos'] ?? '';
    $limpieza_pyxis = $_POST['limpieza_pyxis'] ?? '';
    $limpieza_bodegas = $_POST['limpieza_bodegas'] ?? '';
    $registro_temperatura_refrigerador = $_POST['registro_temperatura_refrigerador'] ?? '';
    $registro_temperatura_ambiental = $_POST['registro_temperatura_ambiental'] ?? '';
    $novedades = $_POST['novedades'] ?? '';
    $pendientes = $_POST['pendientes'] ?? '';
    $funcionario_saliente_1 = $_POST['funcionario_saliente_1'] ?? '';
    $nombre_funcionario_saliente_1 = $_POST['nombre_funcionario_saliente_1'] ?? '';
    $pin_funcionario_saliente_1 = $_POST['pin_funcionario_saliente_1'] ?? '';
    $funcionario_entrante_1 = $_POST['funcionario_entrante_1'] ?? '';
    $nombre_funcionario_entrante_1 = $_POST['nombre_funcionario_entrante_1'] ?? '';

    // CONSULTA SQL CORREGIDA
    $stmt = $mysqli->prepare("INSERT INTO formulario_turnos_pb_enfermeros (
        fecha, tipoturno, anestesiologo_turno, tens_turno, biopsias_recibidas, biopsias_entregadas, 
        revision_carro_paro, revision_carro_recuperacion, stock_minimo_medicamentos, stock_minimo_insumos, 
        limpieza_pyxis, limpieza_bodegas, registro_temperatura_refrigerador, registro_temperatura_ambiental, 
        novedades, pendientes, funcionario_saliente_1, nombre_funcionario_saliente_1, 
        pin_funcionario_saliente_1, funcionario_entrante_1, nombre_funcionario_entrante_1
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // VERIFICAR SI LA CONSULTA SE PREPARÓ CORRECTAMENTE
    if (!$stmt) {
        die("Error en la preparación: " . $mysqli->error);
    }

    // ASIGNAR LOS VALORES A LA CONSULTA
    $stmt->bind_param(
        "sssssssssssssssssssss",
        $fecha, $tipoturno, $anestesiologo_turno, $tens_turno, $biopsias_recibidas, $biopsias_entregadas,
        $revision_carro_paro, $revision_carro_recuperacion, $stock_minimo_medicamentos, $stock_minimo_insumos,
        $limpieza_pyxis, $limpieza_bodegas, $registro_temperatura_refrigerador, $registro_temperatura_ambiental,
        $novedades, $pendientes, $funcionario_saliente_1, $nombre_funcionario_saliente_1,
        $pin_funcionario_saliente_1, $funcionario_entrante_1, $nombre_funcionario_entrante_1
    );

    // EJECUTAR LA CONSULTA
    if ($stmt->execute()) {
        // Obtener el ID del formulario insertado
        $id_formulario = $stmt->insert_id;

        // Redirigir al PHP que genera el PDF 
        header("Location: generar_pdf_pb_enfermeros.php?id=" . $id_formulario);
        exit();
    } else {
        die("Error al insertar: " . $stmt->error);
    }

    // CERRAR CONSULTA Y CONEXIÓN
    $stmt->close();
    $mysqli->close();
}
?>