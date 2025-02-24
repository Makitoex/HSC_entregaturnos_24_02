<?php
session_start();

if (!isset($_SESSION['id_usuarios'])) {
    header("Location: index.php");
    exit();
}
// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include '/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';

// Establecer la codificación de caracteres
mysqli_set_charset($conn, "utf8mb4");

$fecha_actual = date("Y-m-d");

// CONSULTA PARA OBTENER LOS FUNCIONARIOS DE UTI QUE TIENEN id_servicio = 2
$sql = "SELECT id_funcionarios, id_profesion, nombre_funcionarios, rut_funcionarios, pin_funcionarios FROM funcionarios_uti WHERE id_servicio = 3 ORDER BY id_funcionarios";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/hojadeestilos.css">
    <title>Entrega de Turno UTI</title>

    <!-- ICONO DE PAGINA -->
    <link rel="icon" href="imagen/logohsc.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <?php include '/laragon/www/Sistema_entrega_turnos_HSC/navbar.php'; ?>
</head>

<body>
    <div class="container mt-4">
        <h2 class="text-center">Entrega De Turno Kinesiologos UTI</h2>
        <p class="text-center">Hospital Santa Cruz</p>
        <br>
        <form action="guardar_turno_kinesiologos.php" method="POST">
            <!-- FECHA Y TURNO -->
            <div class="row mb-3">
                <div class="col-md-2">
                    <label for="fecha">Fecha:</label>
                    <input type="date" id="fecha" name="fecha" value="<?php echo $fecha_actual; ?>" class="form-control form-control-sm" readonly />
                </div>
                <div class="col-md-2">
                    <label for="tipoturno">Selecciona el turno:</label>
                    <select id="tipoturno" name="tipoturno" class="form-select form-select-sm" required>
                        <option value="turno_largo">Turno Largo</option>
                        <option value="turno_noche">Turno Noche</option>
                    </select>
                </div>
            </div>

            <hr>

            <!-- CAMAS DISPONIBLES -->
            <h6 class="text-center">Camas disponibles</h6>
            <br>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="camas_ocupadas">Nº de camas ocupadas hasta la entrega de turno:</label>
                    <input type="number" id="camas_ocupadas" name="camas_ocupadas" class="form-control form-control-sm" required max="20">
                </div>
                <div class="col-md-4">
                    <label for="camas_disponibles">Nº de camas disponibles hasta la entrega de turno:</label>
                    <input type="number" id="camas_disponibles" name="camas_disponibles" class="form-control form-control-sm" required max="20">
                </div>
            </div>
            <br>
            <hr>

            <!-- PACIENTES FALLECIDOS -->
            <h6 class="text-center">Pacientes Fallecidos</h6>
            <div class="mb-3">
                <label for="pacientesfallecidos">Cantidad pacientes fallecidos:</label>
                <select id="cantpacientesfallecidos" name="cant_pacientes_fallecidos" class="form-select form-select-sm" onchange="mostrarCampoTexto()" required>
                    <option value="">Selecciona...</option>
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div id="detallespacientesf" style="display: none;">
                <label for="detallespacientesf">Ingrese detalles:</label>
                <textarea id="detallespacientesf" name="detalles_pacientes_fallecidos" class="form-control form-control-sm"></textarea>
            </div>

            <hr>

            <!-- EVENTOS Y COMENTARIOS -->
            <h6 class="text-center">Eventos</h6>
            <div class="mb-3">
                <label for="eventos_detalle">Pacientes Trasladados:</label>
                <textarea id="eventos_detalle" name="eventos_detalle" rows="3" class="form-control" placeholder="Indique Traslado de pacientes..."></textarea>
            </div>
            <div class="mb-3">
                <label for="acv_detallekine">Pacientes ACV derivados a APS:</label>
                <textarea id="acv_detallekine" name="acv_detalle" rows="3" class="form-control" placeholder="Deje su observacion..."></textarea>
            </div>

            <!-- CANTIDADES -->
            <h6 class="text-center">Cantidades</h6>
            <br>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="cantidad_setsuccion">Set de Succión en cada unidad (cantidad): :</label>
                    <input type="number" id="cantidad_setsuccion" name="cantidad_setsuccion" class="form-control form-control-sm" required max="10000">
                </div>
            </div>
            <br>
            <hr>
            <div class="mb-3">
                <label for="Eventoskine_detalle">Eventos adversos asociados a Kinesioterapia: </label>
                <textarea id="Eventoskine_detalle" name="Eventoskine_detalle" rows="3" class="form-control" placeholder="Deje su observacion..."></textarea>
            </div>
            <div class="mb-3">
                <label for="comentarios_detalle">Comentarios clínicos/administrativos relevantes</label>
                <textarea id="comentarios_detalle" name="comentarios_detalle" rows="3" class="form-control" placeholder="Deje su observacion..."></textarea>
            </div>
            <hr>
            <br>
            <!-- KINE TURNANTES -->
            <h6 class="text-center">Kinesiologos turnantes:</h6>
            <br>
            <div class="row">
                <!-- Kinesiologo Saliente 1 -->
                <div class="col-md-6 mb-3">
                    <label for="funcionario_saliente_1">Kinesiologo Saliente 1</label>
                    <select id="funcionario_saliente_1" name="funcionario_saliente_1" class="form-select form-select-sm" required>
                        <option value="">-Seleccione Funcionario-</option>
                        <?php
                        mysqli_data_seek($result, 0); //vuelve a leer los datos del resultado
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $id_funcionarios = $row['id_funcionarios'];
                                $nombre_funcionarios = $row['nombre_funcionarios'];
                                $rut_funcionarios = $row['rut_funcionarios'];
                                $pin_funcionarios = $row['pin_funcionarios'];
                                echo "<option value='$id_funcionarios' data-pin='$pin_funcionarios'>$nombre_funcionarios - $rut_funcionarios</option>";
                            }
                        } else {
                            echo "<option value=''>No hay enfermeros disponibles</option>";
                        }
                        ?>
                    </select>
                    <input type="password" id="contrasena_saliente_1" name="contrasena_saliente_1" placeholder="Ingrese Contraseña Kine.1" required class="form-control form-control-sm mt-2">
                </div>

                <!-- Kinesiologo Entrante 1 -->
                <div class="col-md-6 mb-3">
                    <label for="funcionario_entrante_1">Kinesiologo Entrante 1</label>
                    <select id="funcionario_entrante_1" name="funcionario_entrante_1" class="form-select form-select-sm" required>
                        <option value="">-Seleccione Funcionario-</option>
                        <?php
                        mysqli_data_seek($result, 0); //vuelve a leer los datos del resultado
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $id_funcionarios = $row['id_funcionarios'];
                                $nombre_funcionarios = $row['nombre_funcionarios'];
                                $rut_funcionarios = $row['rut_funcionarios'];
                                $pin_funcionarios = $row['pin_funcionarios'];
                                echo "<option value='$id_funcionarios' data-pin='$pin_funcionarios'>$nombre_funcionarios - $rut_funcionarios</option>";
                            }
                        } else {
                            echo "<option value=''>No hay Kine disponibles</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <br><br>

            <!-- BOTÓN ENVIAR -->
            <div class="d-grid gap-2 col-4 mx-auto">
                <button type="submit" onclick="return validarYEnviarkine();" class="btn btn-danger">Entregar Turno</button>
            </div>
        </form>
    </div>

    <script src="/js/funcion_agregarfuncionario.js"></script>
    <script src="/js/funcion_obtenerpin.js"></script>
    <script src="/js/funcion_validaryEnviarkine.js"></script>
    <script src="/js/funcion_mostrarcampotexto.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-1CmRugG5aX5+I65R5BxDJjkGrtGk5r0PZ8iFv/V3+6Q/3D3De0hN/y4XXMn+Q3fj" crossorigin="anonymous"></script>
</body>

</html>