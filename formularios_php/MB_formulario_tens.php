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

$fecha_actual = date("Y-m-d");

// Consulta para obtener los funcionarios de UCI
$sql = "SELECT id_funcionarios, id_profesion, nombre_funcionarios, rut_funcionarios, pin_funcionarios FROM funcionarios_microbiologia WHERE id_servicio = 9 ORDER BY id_funcionarios";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrega de turnos TM</title>
    <link rel="icon" href="imagen/logohsc.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <?php include '/laragon/www/Sistema_entrega_turnos_HSC/navbar.php'; ?>
</head>
<body>
    <div class="container mt-4">
        <br>
        <h2 class="text-center">Entrega De Turno TENS Microbiologia</h2>
        <p class="text-center">Hospital Santa Cruz</p>
        <form action="guardar_turno_mb_tens.php" method="POST">
        <br><br>

        <!-- Fecha y turno -->
        <div class="row">
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
            <br><br><br>
            <hr>
            <h6 align="left">General:</h6>
            <br><br>
            <form id="formsalasyrx">
                <div class="row">
                    <div class="mb-3">
                        <label for="novedades">Pendientes Quimica y Hormonas:</label>
                        <textarea id="novedades" name="novedades" rows="2" class="form-control" placeholder="Deje su comentario..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="novedades">Pendientes Hematologia:</label>
                        <textarea id="novedades" name="novedades" rows="2" class="form-control" placeholder="Deje su comentario..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="novedades">Pendientes Microbiologia:</label>
                        <textarea id="novedades" name="novedades" rows="2" class="form-control" placeholder="Deje su comentario..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="novedades">Pendientes Serologia y Hormonas:</label>
                        <textarea id="novedades" name="novedades" rows="2" class="form-control" placeholder="Deje su comentario..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="novedades">Recepcion de muestras para derivacion:</label>
                        <textarea id="novedades" name="novedades" rows="2" class="form-control" placeholder="Deje su comentario..."></textarea>
                    </div>
                    <hr>
                    <h6 align="left">Tareas a realizar:</h6>
                    <br><br>
                    <div class="col-md-4">
                        <label for="tipoturno">Hoja de trabajo Microbiologia:</label>
                        <select id="tipoturno" name="tipoturno" class="form-select form-select-sm" required>
                            <option value="turno_largo">No realizado</option>
                            <option value="turno_noche">Realizado</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="tipoturno">Preparacion Cloro:</label>
                        <select id="tipoturno" name="tipoturno" class="form-select form-select-sm" required>
                            <option value="turno_largo">No realizado</option>
                            <option value="turno_noche">Realizado</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="tipoturno">Registro de temperaturas:</label>
                        <select id="tipoturno" name="tipoturno" class="form-select form-select-sm" required>
                            <option value="turno_largo">No realizado</option>
                            <option value="turno_noche">Realizado</option>
                        </select>
                    </div>
                    <br><br><br>
                    <div class="mb-3">
                        <label for="novedades">Otras Observaciones:</label>
                        <textarea id="novedades" name="novedades" rows="2" class="form-control" placeholder="Deje su comentario..."></textarea>
                    </div>
                    <hr>
                    <h6 align="left">Limpieza y orden secciones:</h6>
                    <br><br>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="quimica">Quimica:</label>
                            <select id="quimica" name="quimica" class="form-select form-select-sm" required>
                                <option value="no_realizado">No realizado</option>
                                <option value="realizado">Realizado</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="hermatologia">Hermatologia:</label>
                            <select id="hermatologia" name="hermatologia" class="form-select form-select-sm" required>
                                <option value="no_realizado">No realizado</option>
                                <option value="realizado">Realizado</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="orina">Orina:</label>
                            <select id="orina" name="orina" class="form-select form-select-sm" required>
                                <option value="no_realizado">No realizado</option>
                                <option value="realizado">Realizado</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="microbiologia">Microbiologia:</label>
                            <select id="microbiologia" name="microbiologia" class="form-select form-select-sm" required>
                                <option value="no_realizado">No realizado</option>
                                <option value="realizado">Realizado</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="covid">COVID:</label>
                            <select id="covid" name="covid" class="form-select form-select-sm" required>
                                <option value="no_realizado">No realizado</option>
                                <option value="realizado">Realizado</option>
                            </select>
                        </div>
                    </div>
                    <br><br><br><br>
                    <hr>
                    <br>
                    <h6 class="text-center">Entrega de Turnos:</h6>
                    <br>
                    <br>
                     <!--  turnantes -->
            <h6 class="text-center">TENS turnantes:</h6>
            <br>
            <div class="row">
                <!-- TENS MB Saliente 1 -->
                <div class="col-md-6 mb-3">
                    <label for="funcionario_saliente_1">TENS Saliente 1</label>
                    <select id="funcionario_saliente_1" name="funcionario_saliente_1" class="form-select form-select-sm" required onchange="setNombreYPinFuncionario('funcionario_saliente_1', 'nombre_funcionario_saliente_1', 'pin_funcionario_saliente_1')">
                        <option value="">-Seleccione Funcionario-</option>
                        <?php
                        mysqli_data_seek($result, 0); //vuelve a leer los datos del resultado
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $id_funcionarios = $row['id_funcionarios'];
                                $nombre_funcionarios = $row['nombre_funcionarios'];
                                $rut_funcionarios = $row['rut_funcionarios'];
                                $pin_funcionarios = $row['pin_funcionarios'];
                                echo "<option value='$id_funcionarios' data-nombre='$nombre_funcionarios' data-pin='$pin_funcionarios'>$nombre_funcionarios - $rut_funcionarios</option>";
                            }
                        } else {
                            echo "<option value=''>No hay disponibles</option>";
                        }
                        ?>
                    </select>
                    <input type="hidden" id="nombre_funcionario_saliente_1" name="nombre_funcionario_saliente_1">
                    <input type="hidden" id="pin_funcionario_saliente_1" name="pin_funcionario_saliente_1">
                    <input type="password" id="contrasena_saliente_1" name="contrasena_saliente_1" placeholder="Ingrese Contraseña Kine.1" required class="form-control form-control-sm mt-2">
                </div>

                <!-- TENS MB Entrante 1 -->
                <div class="col-md-6 mb-3">
                    <label for="funcionario_entrante_1">TENS Entrante 1</label>
                    <select id="funcionario_entrante_1" name="funcionario_entrante_1" class="form-select form-select-sm" required onchange="setNombreYPinFuncionario('funcionario_entrante_1', 'nombre_funcionario_entrante_1', 'pin_funcionario_entrante_1')">
                        <option value="">-Seleccione Funcionario-</option>
                        <?php
                        mysqli_data_seek($result, 0); //vuelve a leer los datos del resultado
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $id_funcionarios = $row['id_funcionarios'];
                                $nombre_funcionarios = $row['nombre_funcionarios'];
                                $rut_funcionarios = $row['rut_funcionarios'];
                                $pin_funcionarios = $row['pin_funcionarios'];
                                echo "<option value='$id_funcionarios' data-nombre='$nombre_funcionarios' data-pin='$pin_funcionarios'>$nombre_funcionarios - $rut_funcionarios</option>";
                            }
                        } else {
                            echo "<option value=''>No hay Kine disponibles</option>";
                        }
                        ?>
                    </select>
                    <input type="hidden" id="nombre_funcionario_entrante_1" name="nombre_funcionario_entrante_1">
                    <input type="hidden" id="pin_funcionario_entrante_1" name="pin_funcionario_entrante_1">
                </div>
            </div>

            <br><br>

            <!-- Botón enviar -->
            <div class="d-grid gap-2 col-4 mx-auto">
                <button type="submit" onclick="return validarYEnviar();" class="btn btn-danger">Entregar Turno</button>
            </div>
        </form>
    </div>

    <script>
        function setNombreYPinFuncionario(selectId, nombreId, pinId) {
            var selectElement = document.getElementById(selectId);
            var selectedOption = selectElement.options[selectElement.selectedIndex];
            var nombreFuncionario = selectedOption.getAttribute('data-nombre');
            var pinFuncionario = selectedOption.getAttribute('data-pin');

            document.getElementById(nombreId).value = nombreFuncionario;
            document.getElementId(pinId).value = pinFuncionario;
        }

        function validarYEnviar(event) {
            const funcionario1 = document.getElementById('funcionario_saliente_1');
            const contraseña1 = document.getElementById('contrasena_saliente_1');

            if (!funcionario1 || !contraseña1) {
                alert('Error: No se encontraron los campos de funcionarios o PIN.');
                event.preventDefault();
                return false;
            }

            // Verificar que se haya seleccionado un funcionario
            if (funcionario1.value === "") {
                alert('Por favor, selecciona el funcionario.');
                event.preventDefault();
                return false;
            }

            // Obtener el PIN
            const pinCorrecto1 = funcionario1.options[funcionario1.selectedIndex].getAttribute('data-pin');

            if (!pinCorrecto1) {
                alert('Error: No se encontró el PIN del funcionario.');
                event.preventDefault();
                return false;
            }

            if (contraseña1.value.trim() !== pinCorrecto1.trim()) {
                alert('El PIN del Funcionario es incorrecto.');
                event.preventDefault();
                return false;
            }

            return true;
        }

        document.querySelector('form').addEventListener('submit', function(event) {
            if (!validarYEnviar(event)) {
                event.preventDefault();
            }
        });
    </script>
    <script src="/js/funcion_agregarfuncionario.js"></script>
    <script src="/js/funcion_obtenerpin.js"></script>
    <script src="/js/funcion_mostrarcampotexto.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-1CmRugG5aX5+I65R5BxDJjkGrtGk5r0PZ8iFv/V3+6Q/3D3De0hN/y4XXMn+Q3fj" crossorigin="anonymous"></script>
</body>
</html>