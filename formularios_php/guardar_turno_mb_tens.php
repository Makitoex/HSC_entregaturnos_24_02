<?php
// Conexión a BD
$servername = "localhost";  
$username = "root";         
$password = "";            
$dbname = "sistema_entrega_turnos_hsc"; 

// Crear conexión
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// Verificar si las variables $_POST están definidas antes de usarlas
$fecha = isset($_POST['fecha']) ? $_POST['fecha'] : null;
$tipoturno = isset($_POST['tipoturno']) ? $_POST['tipoturno'] : null;
$pendientes_quimica = isset($_POST['novedades_quimica']) ? $_POST['novedades_quimica'] : null;
$pendientes_hematologia = isset($_POST['novedades_hematologia']) ? $_POST['novedades_hematologia'] : null;
$pendientes_microbiologia = isset($_POST['novedades_microbiologia']) ? $_POST['novedades_microbiologia'] : null;
$pendientes_serologia = isset($_POST['novedades_serologia']) ? $_POST['novedades_serologia'] : null;
$pendientes_recepcion_muestras = isset($_POST['novedades_recepcion_muestras']) ? $_POST['novedades_recepcion_muestras'] : null;
$tarea_hoja_trabajo = isset($_POST['tarea_hoja_trabajo']) ? $_POST['tarea_hoja_trabajo'] : null;
$tarea_preparacion_cloro = isset($_POST['tarea_preparacion_cloro']) ? $_POST['tarea_preparacion_cloro'] : null;
$tarea_registro_temperaturas = isset($_POST['tarea_registro_temperaturas']) ? $_POST['tarea_registro_temperaturas'] : null;
$otras_observaciones = isset($_POST['otras_observaciones']) ? $_POST['otras_observaciones'] : null;
$limpieza_quimica = isset($_POST['limpieza_quimica']) ? $_POST['limpieza_quimica'] : null;
$limpieza_hematologia = isset($_POST['limpieza_hematologia']) ? $_POST['limpieza_hematologia'] : null;
$limpieza_orina = isset($_POST['limpieza_orina']) ? $_POST['limpieza_orina'] : null;
$limpieza_microbiologia = isset($_POST['limpieza_microbiologia']) ? $_POST['limpieza_microbiologia'] : null;
$limpieza_covid = isset($_POST['limpieza_covid']) ? $_POST['limpieza_covid'] : null;
$funcionario_saliente_1 = isset($_POST['funcionario_saliente_1']) ? $_POST['funcionario_saliente_1'] : null;
$contrasena_saliente_1 = isset($_POST['contrasena_saliente_1']) ? $_POST['contrasena_saliente_1'] : null;
$funcionario_entrante_1 = isset($_POST['funcionario_entrante_1']) ? $_POST['funcionario_entrante_1'] : null;
$nombre_funcionario_saliente_1 = isset($_POST['nombre_funcionario_saliente_1']) ? $_POST['nombre_funcionario_saliente_1'] : null;
$nombre_funcionario_entrante_1 = isset($_POST['nombre_funcionario_entrante_1']) ? $_POST['nombre_funcionario_entrante_1'] : null;

// Insertar en formulario_turnos_mb_tens
$stmt = $mysqli->prepare("INSERT INTO formulario_turnos_mb_tens (
    fecha, tipoturno, pendientes_quimica, pendientes_hematologia, 
    pendientes_microbiologia, pendientes_serologia, pendientes_recepcion_muestras,
    tarea_hoja_trabajo, tarea_preparacion_cloro, tarea_registro_temperaturas,
    otras_observaciones, limpieza_quimica, limpieza_hematologia, limpieza_orina, 
    limpieza_microbiologia, limpieza_covid, funcionario_saliente_1, contrasena_saliente_1, 
    funcionario_entrante_1, nombre_funcionario_saliente_1, nombre_funcionario_entrante_1
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

// Verifica
if (!$stmt) {
    die("Error en la preparación: " . $mysqli->error);
}

// Asignar los valores a la consulta
$stmt->bind_param(
    "sssssssssssssssssssss", 
    $fecha, $tipoturno, $pendientes_quimica, $pendientes_hematologia, 
    $pendientes_microbiologia, $pendientes_serologia, $pendientes_recepcion_muestras, 
    $tarea_hoja_trabajo, $tarea_preparacion_cloro, $tarea_registro_temperaturas, 
    $otras_observaciones, $limpieza_quimica, $limpieza_hematologia, $limpieza_orina, 
    $limpieza_microbiologia, $limpieza_covid, $funcionario_saliente_1, 
    $contrasena_saliente_1, $funcionario_entrante_1, $nombre_funcionario_saliente_1, 
    $nombre_funcionario_entrante_1
);

// Ejecutar la consulta
if ($stmt->execute()) {
    // Obtener el ID del formulario insertado
    $id_formulario = $mysqli->insert_id;

    if ($id_formulario > 0) {
        // Redirigir al PHP que genera el PDF 
        header("Location: generar_pdf_microbiologia.php?id=" . $id_formulario);
        exit();
    } else {
        die("Error: No se pudo obtener el ID del formulario.");
    }
} else {
    echo "Error al insertar: " . $stmt->error;
}

// Cerrar consulta y conexión
$stmt->close();
$mysqli->close();  
?>