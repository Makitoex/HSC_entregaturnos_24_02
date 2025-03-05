<?php
session_start();

// Verificar si el usuario inició sesión
if (!isset($_SESSION['id_usuarios'])) {
    header("Location: index.php");
    exit();
}

// Configuración de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir conexión a la base de datos
require_once '/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';

// Configurar el conjunto de caracteres a UTF-8
mysqli_set_charset($conn, "utf8mb4");

// Obtener fecha actual
$fecha_actual = date("Y-m-d");

// CONSULTA PARA OBTENER LOS FUNCIONARIOS DE IMAGENOLOGÍA (id_servicio = 8)
$sql = "SELECT id_funcionarios, nombre_funcionarios, rut_funcionarios, pin_funcionarios 
        FROM funcionarios_imagenologia 
        WHERE id_servicio = 8 
        ORDER BY id_funcionarios";

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
    <title>Entrega de Turnos TM</title>
    <link rel="icon" href="imagen/logohsc.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php include '/laragon/www/Sistema_entrega_turnos_HSC/navbar.php'; ?>
</head>

<body>
    <div class="container mt-4">
        <h2 class="text-center">Entrega de Turno Tecnólogos Médicos</h2>
        <p class="text-center">Hospital Santa Cruz</p>

        <form id="formEntregaTurno" action="guardar_turno_im_tecnologosmedicos.php" method="POST">
            <br>

            <!-- FECHA Y TURNO -->
            <div class="row">
                <div class="col-md-2">
                    <label for="fecha">Fecha:</label>
                    <input type="date" id="fecha" name="fecha" value="<?= $fecha_actual ?>" class="form-control form-control-sm" readonly>
                </div>
                <div class="col-md-2">
                    <label for="tipoturno">Selecciona el turno:</label>
                    <select id="tipoturno" name="tipoturno" class="form-select form-select-sm" required>
                        <option value="turno_largo">Turno Largo</option>
                        <option value="turno_noche">Turno Noche</option>
                    </select>
                </div>
            </div>
            <br>

            <!-- EXÁMENES -->
            <h5>Exámenes:</h5>
            <h6>Exámenes Pendientes:</h6>
            <div class="row">
                <div class="col-md-4">
                    <label for="rx_pendientes">RX:</label>
                    <input type="text" id="rx_pendientes" name="rx_pendientes" class="form-control" placeholder="Ingrese RX pendientes" required>
                </div>
                <div class="col-md-4">
                    <label for="tc_pendientes">TC:</label>
                    <input type="text" id="tc_pendientes" name="tc_pendientes" class="form-control" placeholder="Ingrese TC pendientes" required>
                </div>
                <div class="col-md-4">
                    <label for="portatil_pendientes">Portátil:</label>
                    <input type="text" id="portatil_pendientes" name="portatil_pendientes" class="form-control" placeholder="Ingrese portatiles pendientes" required>
                </div>
            </div>
            <br>

            <h6>Equipos Operativos:</h6>
            <div class="row">
                <div class="col-md-4">
                    <label for="rx_equiposoperativos">RX:</label>
                    <input type="text" id="rx_equiposoperativos" name="rx_equiposoperativos" class="form-control" placeholder="Ingrese RX operativos">
                </div>
                <div class="col-md-4">
                    <label for="tc_equiposoperativos">TC:</label>
                    <input type="text" id="tc_equiposoperativos" name="tc_equiposoperativos" class="form-control" placeholder="Ingrese TC operativos">
                </div>
                <div class="col-md-4">
                    <label for="portatil_equiposoperativos">Portátil:</label>
                    <input type="text" id="portatil_equiposoperativos" name="portatil_equiposoperativos" class="form-control" placeholder="Ingrese portátiles operativos">
                </div>
            </div>
            <br>

            <h6>Exámenes Enviados:</h6>
            <div class="row">
                <div class="col-md-4">
                    <label for="pacs_enviados">PACS:</label>
                    <input type="text" id="pacs_enviados" name="pacs_enviados" class="form-control" placeholder="Ingrese PACS enviados">
                </div>
                <div class="col-md-4">
                    <label for="prueba_enviados">Prueba:</label>
                    <input type="text" id="prueba_enviados" name="prueba_enviados" class="form-control" placeholder="Ingrese prueba enviados">
                </div>
                <div class="col-md-4">
                    <label for="syngovia_enviados">Syngovia:</label>
                    <input type="text" id="syngovia_enviados" name="syngovia_enviados" class="form-control" placeholder="Ingrese Syngovia enviados">
                </div>
            </div>
            <br>

            <h5>Carro de Paros:</h5>
            <div class="row">
                <div class="col-md-7">
                    <label for="codigo_carroparos">Código:</label>
                    <input type="text" id="codigo_carroparos" name="codigo_carroparos" class="form-control" placeholder="Ingrese Código">
                </div>
                <div class="col-md-3">
                    <label for="carrodeparos">Abierto? :</label>
                    <select id="carrodeparos" name="carrodeparos" class="form-select form-select-sm" required>
                        <option value="No_carrodeparos">No, no fue abierto</option>
                        <option value="Si_carrodeparos">Sí, fue abierto</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3" id="carroutilizado-container" style="display: none;">
                <div class="col-md-12">
                    <label for="carroutilizado">Si abrió el carro de paros ¿falta algo? Indique:</label>
                    <textarea id="carroutilizado" name="carroutilizado" class="form-control" placeholder="Si el carro de paros fue abierto, falta algo? Indique..."></textarea>
                </div>
            </div>
            <br>

            <h5>General:</h5>
            <div class="row">
                <div class="col-md-10">
                    <label for="salasyrx">Salas limpias y estadísticas RX:</label>
                    <input type="text" id="salasyrx" name="salasyrx" class="form-control" placeholder="Ingrese...">
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-10">
                    <label for="inyectora">Inyectora:</label>
                    <input type="text" id="inyectora" name="inyectora" class="form-control" placeholder="Ingrese...">
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-10">
                    <label for="cd_grabados">CD grabados:</label>
                    <input type="text" id="cd_grabados" name="cd_grabados" class="form-control" placeholder="Ingrese...">
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-10">
                    <label for="cd_grabadosotroturno">CD grabados en otro turno:</label>
                    <input type="text" id="cd_grabadosotroturno" name="cd_grabadosotroturno" class="form-control" placeholder="Ingrese...">
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-10">
                    <label for="eventosadversos">Eventos adversos o centinela:</label>
                    <input type="text" id="eventosadversos" name="eventosadversos" class="form-control" placeholder="Ingrese...">
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-10">
                    <label for="pacientessospecha">N° pacientes sospecha o + COVID:</label>
                    <input type="text" id="pacientessospecha" name="pacientessospecha" class="form-control" placeholder="Ingrese...">
                </div>
            </div>
            <br>
            <div class="mb-3">
                <label for="novedades">Novedades:</label>
                <textarea id="novedades" name="novedades" rows="3" class="form-control" placeholder="Deje su comentario..."></textarea>
            </div>
            <hr>

            <!-- SELECCIÓN DE TECNÓLOGOS MÉDICOS -->
            <h6 class="text-center">Tecnólogos Médicos Turnantes:</h6>
            <br>
            <div class="row">
                <!-- TM Saliente 1 -->
                <div class="col-md-6 mb-3">
                    <label for="funcionario_saliente_1">Tecnologo Medico Saliente 1</label>
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
                            echo "<option value=''>No hay TM disponibles</option>";
                        }
                        ?>
                    </select>
                    <input type="hidden" id="nombre_funcionario_saliente_1" name="nombre_funcionario_saliente_1">
                    <input type="password" id="contrasena_saliente_1" name="contrasena_saliente_1" placeholder="Ingrese Contraseña TM" required class="form-control form-control-sm mt-2">
                </div>

                <!-- TM Entrante 1 -->
                <div class="col-md-6 mb-3">
                    <label for="funcionario_entrante_1">Tecnologo Medico Entrante 1</label>
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
                            echo "<option value=''>No hay TM disponibles</option>";
                        }
                        ?>
                    </select>
                    <input type="hidden" id="nombre_funcionario_entrante_1" name="nombre_funcionario_entrante_1">
                </div>
            </div>

            <br><br>

            <!-- BOTÓN DE ENVÍO -->
            <div class="d-grid gap-2 col-4 mx-auto">
                <button type="submit" class="btn btn-danger">Entregar Turno</button>
            </div>
        </form>
    </div>

    <script>
    // Función para actualizar el nombre del funcionario automáticamente
    function setNombreFuncionario(funcionarioSelectId, funcionarioNombreId) {
        var selectElement = document.getElementById(funcionarioSelectId);
        if (!selectElement) {
            console.error("Error: No se encontró el select de funcionarios con ID " + funcionarioSelectId);
            return;
        }

        var selectedOption = selectElement.options[selectElement.selectedIndex];
        var nombreFuncionario = selectedOption.getAttribute("data-nombre");

        if (nombreFuncionario) {
            document.getElementById(funcionarioNombreId).value = nombreFuncionario;
        } else {
            console.error("No se encontró el nombre del funcionario");
        }
    }
    // Mostrar u ocultar el campo carroutilizado
    document.addEventListener("DOMContentLoaded", function () {
        var carroDeParos = document.getElementById('carrodeparos');
        var reasonContainer = document.getElementById('carroutilizado-container');

        if (carroDeParos && reasonContainer) {
            carroDeParos.addEventListener('change', function() {
                reasonContainer.style.display = this.value === 'Si_carrodeparos' ? 'block' : 'none';
            });
        }
    });

    // Validar formulario antes de enviarlo
    document.addEventListener("DOMContentLoaded", function () {
        var form = document.getElementById('formEntregaTurno');
        
        if (form) {
            form.addEventListener('submit', function(event) {
                validarYEnviar(event);
            });
        } else {
            console.error("Error: No se encontró el formulario con ID 'formEntregaTurno'");
        }
    });

    function validarYEnviar(event) {
        const funcionario1 = document.getElementById('funcionario_saliente_1');
        const contrasena1 = document.getElementById('contrasena_saliente_1');

        if (!funcionario1 || !contrasena1) {
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

        // Obtener el PIN del funcionario seleccionado
        const selectedOption = funcionario1.options[funcionario1.selectedIndex];
        const pinCorrecto1 = selectedOption.getAttribute('data-pin');

        if (!pinCorrecto1) {
            alert('Error: No se encontró el PIN del funcionario.');
            event.preventDefault();
            return false;
        }

        if (contrasena1.value.trim() !== pinCorrecto1.trim()) {
            alert('El PIN del Funcionario es incorrecto.');
            event.preventDefault();
            return false;
        }

        // Si todo es correcto, permitir el envío del formulario
        return true;
    }
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>