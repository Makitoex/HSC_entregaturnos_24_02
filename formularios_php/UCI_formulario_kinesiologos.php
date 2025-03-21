<?php
session_start();

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['id_usuarios'])) {
    header("Location: index.php");
    exit();
}

// Display errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
include '/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';

// Check if the connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set character encoding
mysqli_set_charset($conn, "utf8mb4");

$fecha_actual = date("Y-m-d");

// Query to get UCI staff with id_servicio = 6
$sql = "SELECT id_funcionarios, id_profesion, nombre_funcionarios, rut_funcionarios, pin_funcionarios FROM funcionarios_uci WHERE id_servicio = 6 ORDER BY id_funcionarios";
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
    <title>Entrega de Turno UCI KINE</title>

    <!-- ICONO DE PAGINA -->
    <link rel="icon" href="imagen/logohsc.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <?php include '/laragon/www/Sistema_entrega_turnos_HSC/navbar.php'; ?>
</head>

<body>
    <div class="container mt-4">
        <h2 class="text-center">Entrega De Turno Kinesiologos UCI</h2>
        <p class="text-center">Hospital Santa Cruz</p>
        <br>
        <form action="guardar_turno_uci_kinesiologos.php" method="POST" onsubmit="return validarYEnviarkineuci();">
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
                <label for="Eventoskine_detalle">Eventos adversos asociados a Kinesioterapia: </label>
                <textarea id="Eventoskine_detalle" name="Eventoskine_detalle" rows="3" class="form-control" placeholder="Deje su observacion..."></textarea>
            </div>
            <div class="mb-3">
                <label for="comentarios_detalle">Comentarios clínicos/administrativos relevantes</label>
                <textarea id="comentarios_detalle" name="comentarios_detalle" rows="3" class="form-control" placeholder="Deje su observacion..."></textarea>
            </div>
            <hr>
            <br>

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
            <br>
            <!-- KINE TURNANTES -->
            <h6 class="text-center">Kinesiologos turnantes:</h6>
            <br>
            <div class="row">
                <!-- Kinesiologo Saliente 1 -->
                <div class="col-md-6 mb-3">
                    <label for="funcionario_saliente_1">Kinesiologo Saliente 1</label>
                    <select id="funcionario_saliente_1" name="funcionario_saliente_1" class="form-select form-select-sm" required onchange="setNombreFuncionario('funcionario_saliente_1', 'nombre_funcionario_saliente_1')">
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
                    <input type="hidden" id="nombre_funcionario_saliente_1" name="nombre_funcionario_saliente_1">
                    <input type="password" id="contrasena_saliente_1" name="contrasena_saliente_1" placeholder="Ingrese Contraseña Kine.1" required class="form-control form-control-sm mt-2">
                </div>

                <!-- Kinesiologo Entrante 1 -->
                <div class="col-md-6 mb-3">
                    <label for="funcionario_entrante_1">Kinesiologo Entrante 1</label>
                    <select id="funcionario_entrante_1" name="funcionario_entrante_1" class="form-select form-select-sm" required onchange="setNombreFuncionario('funcionario_entrante_1', 'nombre_funcionario_entrante_1')">
                        <option value="">-Seleccione Funcionario-</option>
                        <?php
                        mysqli_data_seek($result, 0); //vuelve a leer los datos del resultado
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $id_funcionarios = $row['id_funcionarios'];
                                $nombre_funcionarios = $row['nombre_funcionarios'];
                                $rut_funcionarios = $row['rut_funcionarios'];
                                $pin_funcionarios = $row['pin_funcionarios'];
                                echo "<option value='$id_funcionarios' data-nombre='$nombre_funcionarios'>$nombre_funcionarios - $rut_funcionarios</option>";
                            }
                        } else {
                            echo "<option value=''>No hay Kine disponibles</option>";
                        }
                        ?>
                    </select>
                    <input type="hidden" id="nombre_funcionario_entrante_1" name="nombre_funcionario_entrante_1">
                </div>

                <br><br>

                <!-- OPCIONES SI ES DOMINGO -->
                <div id="opciones_domingo" style="display: none;">
                    <hr>
                    <h6 class="text-center">Revisión stock Ventilación Mecánica (domingo)</h6>
                    <br>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="filtros_hme">Filtros HME:</label>
                            <input type="number" id="filtros_hme" name="filtros_hme" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label for="filtros_antibacterianos">Filtros Antibacterianos:</label>
                            <input type="number" id="filtros_antibacterianos" name="filtros_antibacterianos" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label for="filtros_traqueostomia">Filtros de Traqueostomía:</label>
                            <input type="number" id="filtros_traqueostomia" name="filtros_traqueostomia" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="sonda_succion_cerrada">Sonda Succión Cerrada:</label>
                            <input type="number" id="sonda_succion_cerrada" name="sonda_succion_cerrada" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label for="corrugado_una_rama">Corrugado una rama:</label>
                            <input type="number" id="corrugado_una_rama" name="corrugado_una_rama" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label for="corrugado_dos_ramas">Corrugado dos ramas:</label>
                            <input type="number" id="corrugado_dos_ramas" name="corrugado_dos_ramas" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="adaptador_idm">Adaptador IDM:</label>
                            <input type="number" id="adaptador_idm" name="adaptador_idm" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label for="adaptador_nbz">Adaptador NBZ:</label>
                            <input type="number" id="adaptador_nbz" name="adaptador_nbz" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label for="tubo_t">Tubo T:</label>
                            <input type="number" id="tubo_t" name="tubo_t" class="form-control form-control-sm">
                        </div>
                    </div>
                    <br>
                    <h6 class="text-center">Mascarillas y fijaciones VMNI ordenadas por talla:</h6>
                    <br>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="mascarillas_talla_s">S:</label>
                            <input type="number" id="mascarillas_talla_s" name="mascarillas_talla_s" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label for="mascarillas_talla_l">L:</label>
                            <input type="number" id="mascarillas_talla_l" name="mascarillas_talla_l" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label for="mascarillas_talla_xl">XL:</label>
                            <input type="number" id="mascarillas_talla_xl" name="mascarillas_talla_xl" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <!-- BOTÓN ENVIAR -->
                <div class="d-grid gap-2 col-4 mx-auto">
                    <button type="submit" class="btn btn-danger">Entregar Turno</button>
                </div>
        </form>
    </div>

    <script>
        function mostrarOpcionesDomingo() {
            const fecha = new Date(document.getElementById('fecha').value);
            const diaSemana = fecha.getUTCDay(); // 0 es domingo, 6 es sábado

            if (diaSemana === 0) {
                document.getElementById('opciones_domingo').style.display = 'block';
            } else {
                document.getElementById('opciones_domingo').style.display = 'none';
            }
        }

        function mostrarCampoTexto() {
            const cantidad = document.getElementById('cantpacientesfallecidos').value;
            if (cantidad > 0) {
                document.getElementById('detallespacientesf').style.display = 'block';
            } else {
                document.getElementById('detallespacientesf').style.display = 'none';
            }
        }

        function setNombreFuncionario(funcionarioSelectId, funcionarioNombreId) {
            var selectElement = document.getElementById(funcionarioSelectId);
            var selectedOption = selectElement.options[selectElement.selectedIndex];

            // Depuración: Verificar el valor que se está obteniendo
            var nombreFuncionario = selectedOption.getAttribute("data-nombre");
            console.log("Funcionario seleccionado: ", nombreFuncionario);

            if (nombreFuncionario) {
                document.getElementById(funcionarioNombreId).value = nombreFuncionario;
            } else {
                console.error("No se encontró el nombre del funcionario");
            }
        }


        document.addEventListener('DOMContentLoaded', function() {
            mostrarOpcionesDomingo();
        });

        function validarYEnviarkineuci() {
            // Obtener los valores de los campos de contraseña y PIN de los funcionarios
            const contrasenaSaliente1 = document.getElementById('contrasena_saliente_1').value.trim(); // trim() para quitar espacios
            const funcionarioSaliente1 = document.getElementById('funcionario_saliente_1');
            const pinSaliente1 = funcionarioSaliente1.options[funcionarioSaliente1.selectedIndex].getAttribute('data-pin').trim(); // trim() también para el PIN

            // Depuración: Verifica los valores que estás comparando
            console.log('Contraseña ingresada:', contrasenaSaliente1);
            console.log('PIN de funcionario saliente:', pinSaliente1);

            if (contrasenaSaliente1 !== pinSaliente1) {
                alert('La contraseña del Funcionario saliente 1 es incorrecta.');
                return false; // Detener el envío del formulario si las contraseñas no coinciden
            }

            return true; // Si las contraseñas coinciden, enviar el formulario
        }
    </script>
    <script src="/js/funcion_agregarfuncionario.js"></script>
    <script src="/js/funcion_obtenerpin.js"></script>
    <script src="/js/funcion_validaryEnviarkineuci.js"></script>
    <script src="/js/funcion_mostrarcampotexto.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>