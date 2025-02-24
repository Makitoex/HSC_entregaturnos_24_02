<?php
session_start();

if (!isset($_SESSION['id_usuarios'])) {
    header("Location: index.php");
    exit();
}
//Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir archivo de conexión
include '/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';

// Establecer la codificación de caracteres
mysqli_set_charset($conn, "utf8mb4");

$fecha_actual = date("Y-m-d");

// CONSULTA PARA OBTENER LOS FUNCIONARIOS DE UCI QUE TIENEN id_servicio = 7
$sql = "SELECT id_funcionarios, id_profesion, nombre_funcionarios, rut_funcionarios, pin_funcionarios FROM funcionarios_uci WHERE id_servicio = 7 ORDER BY id_funcionarios";
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
    <title>Entrega de Turno UCI</title>

    <!-- ICONO DE PAGINA -->
    <link rel="icon" href="imagen/logohsc.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <?php include '/laragon/www/Sistema_entrega_turnos_HSC/navbar.php'; ?>
</head>

<body>
    <div class="container mt-4">
        <h2 class="text-center">Entrega De Turno TENS UCI</h2>
        <p class="text-center">Hospital Santa Cruz</p>
        <br>
        <form action="guardar_turno_uci_tens.php" method="POST">
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

            <!-- MÉDICO DE TURNO -->
            <h6 class="text-center">Medico de turno</h6>
            <br>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="medico_turno">Médicos de Turno:</label>
                    <input type="text" id="medico_turno" name="medico_turno" class="form-control" placeholder="Ej: Dr. Pérez, Dr. Gómez">
                </div>
            </div>
            <br>
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
                <div class="col-md-4">
                    <label for="camas_reservadas">Nº de camas reservadas:</label>
                    <input type="number" id="camas_reservadas" name="camas_reservadas" class="form-control form-control-sm" required max="20">
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
                <label for="eventos_detalle">Eventos Adversos:</label>
                <textarea id="eventos_detalle" name="eventos_detalle" rows="3" class="form-control" placeholder="Describa los eventos adversos y/o centinelas ocurridos durante el turno..."></textarea>
            </div>
            <div class="mb-3">
                <label for="comentarios_detalle">Comentarios clínicos/administrativos relevantes:</label>
                <textarea id="comentarios_detalle" name="comentarios_detalle" rows="3" class="form-control" placeholder="Deje su comentario..."></textarea>
            </div>

            <hr>
            <br>
            <!-- TENS TURNANTES -->
            <h6 class="text-center">TENS turnantes:</h6>
            <br>
            <div class="row">
                <div class="col-md-4">
                    <label for="funcionario_saliente_1">TENS Saliente 1</label>
                    <select id="funcionario_saliente_1" name="funcionario_saliente_1" class="form-select form-select-sm" required>
                        <option value="">-Seleccione Funcionario-</option>
                        <?php
                        mysqli_data_seek($result, 0); //vuelve a leer los datos del resultado
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $id_funcionarios = $row['id_funcionarios'];
                                $nombre_funcionarios = $row['nombre_funcionarios'];
                                $rut_funcionarios = $row['rut_funcionarios'];
                                $pin_funcionarios = $row['pin_funcionarios']; // Asegúrate de tener este campo en tu consulta SQL
                                echo "<option value='$id_funcionarios' data-pin='$pin_funcionarios'>$nombre_funcionarios - $rut_funcionarios</option>";
                            }
                        } else {
                            echo "<option value=''>No hay TENS disponibles</option>";
                        }
                        ?>

                    </select>
                    <input type="password" id="contrasena_saliente_1" name="contrasena_saliente_1" placeholder="Ingrese Contraseña Tens.1" required>
                </div>

                <div class="col-md-4">
                    <label for="funcionario_saliente_2">TENS Saliente 2</label>
                    <select id="funcionario_saliente_2" name="funcionario_saliente_2" class="form-select form-select-sm" required>
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
                            echo "<option value=''>No hay TENS disponibles</option>";
                        }
                        ?>

                    </select>
                    <input type="password" id="contrasena_saliente_2" name="contrasena_saliente_2" placeholder="Ingrese Contraseña Tens.2" required>
                </div>

                <div class="col-md-4">
                    <label for="funcionario_saliente_3">TENS Saliente 3</label>
                    <select id="funcionario_saliente_3" name="funcionario_saliente_3" class="form-select form-select-sm" required>
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
                            echo "<option value=''>No hay TENS disponibles</option>";
                        }
                        ?>
                    </select>
                    <input type="password" id="contrasena_saliente_3" name="contrasena_saliente_3" placeholder="Ingrese Contraseña Tens.3" required>
                </div>
            </div>
            <br><br>
            <div class="row">
                <div class="col-md-4">
                    <label for="funcionario_entrante_1">TENS Entrante 1</label>
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
                            echo "<option value=''>No hay TENS disponibles</option>";
                        }
                        ?>

                    </select>
                </div>

                <div class="col-md-4">
                    <label for="funcionario_entrante_2">TENS Entrante 2</label>
                    <select id="funcionario_entrante_2" name="funcionario_entrante_2" class="form-select form-select-sm" required>
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
                            echo "<option value=''>No hay TENS disponibles</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="funcionario_entrante_3">TENS Entrante 3</label>
                    <select id="funcionario_entrante_3" name="funcionario_entrante_3" class="form-select form-select-sm" required>
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
                            echo "<option value=''>No hay TENS disponibles</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <br><br>

            <!-- BOTÓN ENVIAR -->
            <div class="d-grid gap-2 col-4 mx-auto">
                <button type="submit" onclick="return validarYEnviar();" class="btn btn-danger">Entregar Turno</button>
            </div>
        </form>
    </div>

    <script src="/js/funcion_agregarfuncionario.js"></script>
    <script src="/js/funcion_obtenerpin.js"></script>
    <script src="/js/funcion_validaryEnviar.js"></script>
    <script src="/js/funcion_mostrarcampotexto.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-1CmRugG5aX5+I65R5BxDJjkGrtGk5r0PZ8iFv/V3+6Q/3D3De0hN/y4XXMn+Q3fj" crossorigin="anonymous"></script>
</body>

</html>