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

// Consulta para obtener los funcionarios de microbiología
$sql = "SELECT id_funcionarios, id_profesion, nombre_funcionarios, rut_funcionarios, pin_funcionarios FROM funcionarios_microbiologia WHERE id_servicio = 10 ORDER BY id_funcionarios";
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
    <title>Entrega de Turno MB TM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="imagen/logohsc.ico" type="image/x-icon">
    <?php include '/laragon/www/Sistema_entrega_turnos_HSC/navbar.php'; ?>
</head>

<body>
    <div class="container mt-4">
        <h2 class="text-center">Entrega De Turno Tecnólogos Médicos MB</h2>
        <p class="text-center">Hospital Santa Cruz</p>
        <div class="row mb-3">
            <div class="col-md-2">
                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" value="<?php echo $fecha_actual; ?>" class="form-control form-control-sm" readonly />
            </div>
            <div class="col-md-2">
                <label for="tipoturno">Selecciona el turno:</label>
                <select id="tipoturno" name="tipoturno" class="form-select form-select-sm" required onchange="mostrarFormulario()">
                    <option value="">-Seleccione Turno-</option>
                    <option value="turno_noche">Turno Noche</option>
                    <option value="turno_largo">Turno Largo</option>
                </select>
            </div>
        </div>

        <form id="formulario_noche" action="guardar_turno_mb_tecnologos.php" method="POST" style="display: none;">
            <!-- Formulario para Turno NOCHE -->
            <h5>Formulario Turno Noche</h5>
            <input type="hidden" id="fecha_input_noche" name="fecha" />
            <input type="hidden" id="tipoturno_input_noche" name="tipoturno" />
            <body>
                <h5>1.- Equipos y Sistema Informático</h5>
                <div class="mb-3">
                    <label for="observaciones_equipo">Observaciones:</label>
                    <textarea id="observaciones_equipo" name="observaciones_equipo" class="form-control"></textarea>
                </div>

                <h5>2.- Control de Calidad e ingreso a Synergy QC</h5>
                <div class="mb-3">
                    <label for="tecnicas_calibradas">Técnicas Calibradas:</label>
                    <textarea id="tecnicas_calibradas" name="tecnicas_calibradas" class="form-control"></textarea>
                </div>
                <div class="mb-3">
                    <label for="observaciones_quimica">Observaciones:</label>
                    <textarea id="observaciones_quimica" name="observaciones_quimica" class="form-control"></textarea>
                </div>

                <h5>CHECK LIST</h5>
                <div class="mb-3">
                    <input type="checkbox" id="quimica" name="quimica"> QUÍMICA<br>
                    <input type="checkbox" id="hormonas" name="hormonas"> HORMONAS<br>
                    <input type="checkbox" id="gases_elp" name="gases_elp"> GASES Y ELP<br>
                    <input type="checkbox" id="crd" name="crd"> CRD<br>
                    <input type="checkbox" id="vih_hepb" name="vih_hepb"> VIH, HEP B<br>
                    <input type="checkbox" id="sp" name="sp"> SP<br>
                </div>

                <h5>3.- Mantenciones Equipos</h5>
                <div class="mb-3">
                    <label for="mantencion">Observaciones:</label>
                    <textarea id="mantencion" name="mantencion" class="form-control"></textarea>
                </div>

                <h5>Equipos</h5>
                <div class="mb-3">
                    <input type="checkbox" id="cobas_c311" name="cobas_c311"> Cobas C311<br>
                    <input type="checkbox" id="cobas_c111" name="cobas_c111"> Cobas C111<br>
                </div>

                <h5>4.- UMT</h5>
                <div class="mb-3">
                    <label for="transfusiones">Transfusiones Pendientes y Observaciones:</label>
                    <textarea id="transfusiones" name="transfusiones" class="form-control"></textarea>
                </div>

                <h5>STOCK</h5>
                <div class="mb-3">
                    <input type="checkbox" id="gr_0" name="gr_0"> GR 0+<br>
                    <input type="checkbox" id="gr_a" name="gr_a"> GR A+<br>
                    <input type="checkbox" id="gr_oneg" name="gr_oneg"> GR ONEG<br>
                    <input type="checkbox" id="gr_b" name="gr_b"> GR B+<br>
                    <input type="checkbox" id="gr_ab" name="gr_ab"> GR AB+<br>
                    <input type="checkbox" id="pfc_o" name="pfc_o"> PFC O+<br>
                    <input type="checkbox" id="pfc_a" name="pfc_a"> PFC A+<br>
                    <input type="checkbox" id="pfc_b" name="pfc_b"> PFC B+<br>
                    <input type="checkbox" id="pfc_ab" name="pfc_ab"> PFC AB+<br>
                </div>

                <h5>5.- Muestras Urgentes</h5>
                <div class="mb-3">
                    <label for="muestras_pendientes">Pendientes:</label>
                    <textarea id="muestras_pendientes" name="muestras_pendientes" class="form-control"></textarea>
                </div>

                <h5>6.- Microbiología</h5>
                <h6>Valores Críticos</h6>
                <div class="mb-3">
                    <textarea id="valores_criticos" name="valores_criticos" class="form-control"></textarea>
                </div>

                <table class="table table-bordered mb-3">
                    <tr>
                        <th>Valores</th>
                        <th>Observaciones</th>
                    </tr>
                    <tr>
                        <td>Gram Hemocultivo</td>
                        <td><input type="text" name="gram_hemocultivo" class="form-control"></td>
                    </tr>
                    <tr>
                        <td>Gram Líquidos</td>
                        <td><input type="text" name="gram_liquidos" class="form-control"></td>
                    </tr>
                </table>

                <h5>Paneles Pendientes, Otras Observaciones</h5>
                <div class="mb-3">
                    <textarea id="paneles_pendientes" name="paneles_pendientes" class="form-control"></textarea>
                </div>

                <h5>7.- Otras Observaciones, Cambios de Lote y Reactivos</h5>
                <div class="mb-3">
                    <textarea id="cambios_lote" name="cambios_lote" class="form-control"></textarea>
                </div>

                <h5>8.- Insumos o Reactivos Críticos</h5>
                <div class="mb-3">
                    <textarea id="insumos_criticos" name="insumos_criticos" class="form-control"></textarea>
                </div>

                <h5>9.- Pendientes COVID</h5>
                <div class="mb-3">
                    <textarea id="pendientes_covid" name="pendientes_covid" class="form-control"></textarea>
                </div>
                <br>
                <h4 class="text-center">Entrega de Turnos:</h4>
                <br>
                <div class="row">
                    <!-- TM Entrega -->
                    <div class="col-md-6 mb-3">
                        <label for="funcionario_saliente_1">TM Entrega</label>
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
                        <input type="password" id="contrasena_saliente_1" name="contrasena_saliente_1" placeholder="Ingrese Contraseña" required class="form-control form-control-sm mt-2">
                    </div>
                    <!-- TM Recibe -->
                    <div class="col-md-6 mb-3">
                        <label for="funcionario_entrante_1">TM Recibe</label>
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
                                echo "<option value=''>No hay disponibles</option>";
                            }
                            ?>
                        </select>
                        <input type="hidden" id="nombre_funcionario_entrante_1" name="nombre_funcionario_entrante_1">
                        <input type="hidden" id="pin_funcionario_entrante_1" name="pin_funcionario_entrante_1">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Enviar</button>
                        </form>
        </form>
        <form id="formulario_largo" action="guardar_turno_mb_tecnologos.php" method="POST" style="display: none;">
    <!-- Formulario para Turno Largo -->
    <h5>Formulario Turno Largo</h5>
    <input type="hidden" id="fecha_input_largo" name="fecha" />
    <input type="hidden" id="tipoturno_input_largo" name="tipoturno" />
    <div>
        <h5>1.- Equipos y Sistema Informático</h5>
        <div class="mb-3">
            <label for="observaciones_equipo">Observaciones:</label>
            <textarea id="observaciones_equipo" name="observaciones_equipo" class="form-control"></textarea>
        </div>

        <h5>2.- Mantenciones Equipos</h5>
        <div class="mb-3">
            <label for="mantencion">Observaciones:</label>
            <textarea id="mantencion" name="mantencion" class="form-control"></textarea>
        </div>

        <h5>Equipos</h5>
        <div class="mb-3">
            <input type="checkbox" id="cobas_c311" name="cobas_c311"> Cobas E411<br>
        </div>

        <h5>3.- UMT</h5>
        <div class="mb-3">
            <label for="transfusiones">Transfusiones Pendientes y Observaciones:</label>
            <textarea id="transfusiones" name="transfusiones" class="form-control"></textarea>
        </div>

        <h5>STOCK</h5>
        <div class="mb-3">
            <input type="checkbox" id="gr_0" name="gr_0"> GR 0+<br>
            <input type="checkbox" id="gr_a" name="gr_a"> GR A+<br>
            <input type="checkbox" id="gr_oneg" name="gr_oneg"> GR ONEG<br>
            <input type="checkbox" id="gr_ab" name="gr_ab"> GR AB+<br>
            <input type="checkbox" id="pfc_o" name="pfc_o"> PFC O+<br>
            <input type="checkbox" id="pfc_a" name="pfc_a"> PFC A+<br>
            <input type="checkbox" id="pfc_b" name="pfc_b"> PFC B+<br>
            <input type="checkbox" id="pfc_ab" name="pfc_ab"> PFC AB+<br>
        </div>

        <h5>4.- Muestras Urgentes</h5>
        <div class="mb-3">
            <label for="muestras_pendientes">Pendientes:</label>
            <textarea id="muestras_pendientes" name="muestras_pendientes" class="form-control"></textarea>
        </div>

        <h5>5.- Microbiología</h5>
        <h6>Valores Críticos</h6>
        <div class="mb-3">
            <textarea id="valores_criticos" name="valores_criticos" class="form-control"></textarea>
        </div>

        <table class="table table-bordered mb-3">
            <tr>
                <th>Valores</th>
                <th>Observaciones</th>
            </tr>
            <tr>
                <td>Gram Hemocultivo</td>
                <td><input type="text" name="gram_hemocultivo" class="form-control"></td>
            </tr>
            <tr>
                <td>Gram Líquidos</td>
                <td><input type="text" name="gram_liquidos" class="form-control"></td>
            </tr>
        </table>

        <h5>Paneles Pendientes, Otras Observaciones</h5>
        <div class="mb-3">
            <textarea id="paneles_pendientes" name="paneles_pendientes" class="form-control"></textarea>
        </div>

        <h5>6.- Otras Observaciones, Cambios de Lote y Reactivos</h5>
        <div class="mb-3">
            <textarea id="cambios_lote" name="cambios_lote" class="form-control"></textarea>
        </div>

        <h5>7.- Insumos o Reactivos Críticos</h5>
        <div class="mb-3">
            <textarea id="insumos_criticos" name="insumos_criticos" class="form-control"></textarea>
        </div>

        <h5>8.- Pendientes COVID</h5>
        <div class="mb-3">
            <textarea id="pendientes_covid" name="pendientes_covid" class="form-control"></textarea>
        </div>
        <br>
<h4 class="text-center">Entrega de Turnos:</h4>
                <br>
                <div class="row">
                    <!-- TM Entrega -->
                    <div class="col-md-6 mb-3">
                        <label for="funcionario_saliente_1">TM Entrega</label>
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
                        <input type="password" id="contrasena_saliente_1" name="contrasena_saliente_1" placeholder="Ingrese Contraseña" required class="form-control form-control-sm mt-2">
                    </div>
                    <!-- TM Recibe -->
                    <div class="col-md-6 mb-3">
                        <label for="funcionario_entrante_1">TM Recibe</label>
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
                                echo "<option value=''>No hay disponibles</option>";
                            }
                            ?>
                        </select>
                        <input type="hidden" id="nombre_funcionario_entrante_1" name="nombre_funcionario_entrante_1">
                        <input type="hidden" id="pin_funcionario_entrante_1" name="pin_funcionario_entrante_1">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Enviar</button>
<script>
function mostrarFormulario() {
    var tipoTurno = document.getElementById('tipoturno').value;
    var formularioLargo = document.getElementById('formulario_largo');
    var formularioNoche = document.getElementById('formulario_noche');

    // Ocultar ambos formularios
    formularioLargo.style.display = 'none';
    formularioNoche.style.display = 'none';

    // Mostrar el formulario seleccionado
    if (tipoTurno === 'turno_largo') {
        formularioLargo.style.display = 'block';
    } else if (tipoTurno === 'turno_noche') {
        formularioNoche.style.display = 'block';
    }
}

function setNombreYPinFuncionario(selectId, nombreId, pinId) {
    var selectElement = document.getElementById(selectId);
    var selectedOption = selectElement.options[selectElement.selectedIndex];
    var nombreFuncionario = selectedOption.getAttribute('data-nombre');
    var pinFuncionario = selectedOption.getAttribute('data-pin');

    document.getElementById(nombreId).value = nombreFuncionario;
    document.getElementById(pinId).value = pinFuncionario;
}

function guardarDatos(event) {
    var tipoTurno = document.getElementById("tipoturno").value;
    var fecha = document.getElementById("fecha").value;

    // Asignar los valores a los campos ocultos para enviarlos al servidor
    if (document.getElementById("formulario_largo").style.display === 'block') {
        document.getElementById("fecha_input_largo").value = fecha;
        document.getElementById("tipoturno_input_largo").value = tipoTurno;
    } else if (document.getElementById("formulario_noche").style.display === 'block') {
        document.getElementById("fecha_input_noche").value = fecha;
        document.getElementById("tipoturno_input_noche").value = tipoTurno;
    }
}

// Función para validar formulario
function validarFormulario(event) {
    const formularioVisible = document.querySelector('form[style="display: block;"]');
    const inputsRequeridos = formularioVisible.querySelectorAll('input[required], textarea[required], select[required]');
    
    for (let input of inputsRequeridos) {
        if (!input.value || input.value.trim() === "") {
            alert('Por favor, complete todos los campos.');
            event.preventDefault();
            return false;
        }
    }
    
    // Aquí puedes agregar más validaciones específicas si es necesario
    return true;
}

document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(event) {
        var formularioLargo = document.getElementById('formulario_largo');
        var formularioNoche = document.getElementById('formulario_noche');

        // Verificar cuál formulario es visible y validar solo ese
        if ((formularioLargo.style.display === 'block' || formularioNoche.style.display === 'block') && !validarFormulario(event)) {
            event.preventDefault();
            return false;
        }

        guardarDatos(event);
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-1CmRugG5aX5+I65R5BxDJjkGrtGk5r0PZ8iFv/V3+6Q/3D3De0hN/y4XXMn+Q3fj" crossorigin="anonymous"></script>
</body>
</html>