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

// Incluir archivo de conexión
include '/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';

// Establecer la codificación de caracteres
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
            <form id="formulario_turno" action="guardar_turno_mb_tecnologos.php" method="POST" onsubmit="return validarYEnviar(event)">
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
                <div class="row mb-3">
                    <!-- TM SALIENTE -->
                    <div class="col-md-6">
                        <label for="funcionario_saliente">TM Entrega</label>
                        <select id="funcionario_saliente" name="funcionario_saliente" class="form-select form-select-sm" required onchange="setNombreYPinFuncionario('funcionario_saliente', 'nombre_funcionario_saliente', 'pin_funcionario_saliente')">
                            <option value="">-Seleccione Funcionario-</option>
                            <?php
                            mysqli_data_seek($result, 0);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='{$row['id_funcionarios']}' data-nombre='{$row['nombre_funcionarios']}' data-pin='{$row['pin_funcionarios']}'>
                                {$row['nombre_funcionarios']} - {$row['rut_funcionarios']}
                                </option>";
                            }
                            ?>
                        </select>
                        <input type="hidden" id="nombre_funcionario_saliente" name="nombre_funcionario_saliente">
                        <input type="hidden" id="pin_funcionario_saliente" name="pin_funcionario_saliente">
                        <input type="password" id="contrasena_saliente" name="contrasena_saliente" placeholder="Ingrese Contraseña" required class="form-control form-control-sm mt-2">
                    </div>

                    <!-- TM ENTRANTE -->
                    <div class="col-md-6">
                        <label for="funcionario_entrante">TM Recibe</label>
                        <select id="funcionario_entrante" name="funcionario_entrante" class="form-select form-select-sm" required onchange="setNombreYPinFuncionario('funcionario_entrante', 'nombre_funcionario_entrante', 'pin_funcionario_entrante')">
                            <option value="">-Seleccione Funcionario-</option>
                            <?php
                            mysqli_data_seek($result, 0);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='{$row['id_funcionarios']}' data-nombre='{$row['nombre_funcionarios']}' data-pin='{$row['pin_funcionarios']}'>
                                {$row['nombre_funcionarios']} - {$row['rut_funcionarios']}
                                </option>";
                            }
                            ?>
                        </select>
                        <input type="hidden" id="nombre_funcionario_entrante" name="nombre_funcionario_entrante">
                        <input type="hidden" id="pin_funcionario_entrante" name="pin_funcionario_entrante">
                    </div>
                </div>

                <div id="formulario_noche" style="display: none;">
                    <!-- Formulario para Turno NOCHE -->
                    <h5>Formulario Turno Noche</h5>
                    <input type="hidden" id="fecha_input_noche" name="fecha_noche" />
                    <input type="hidden" id="tipoturno_input_noche" name="tipoturno_noche" />

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

                    <!-- Hidden Fields for Funcionario -->
                    <input type="hidden" id="funcionario_saliente_noche" name="funcionario_saliente">
                    <input type="hidden" id="nombre_funcionario_saliente_noche" name="nombre_funcionario_saliente">
                    <input type="hidden" id="pin_funcionario_saliente_noche" name="pin_funcionario_saliente">
                </div>

                <div id="formulario_largo" style="display: none;">
                    <!-- Formulario para Turno Largo -->
                    <h5>Formulario Turno Largo</h5>
                    <input type="hidden" id="fecha_input_largo" name="fecha_largo" />
                    <input type="hidden" id="tipoturno_input_largo" name="tipoturno_largo" />
                    <div>
                        <h5>1.- Equipos y Sistema Informático</h5>
                        <div class="mb-3">
                            <label for="observaciones_equipo_largo">Observaciones:</label>
                            <textarea id="observaciones_equipo_largo" name="observaciones_equipo" class="form-control"></textarea>
                        </div>

                        <h5>2.- Mantenciones Equipos</h5>
                        <div class="mb-3">
                            <label for="mantencion_largo">Observaciones:</label>
                            <textarea id="mantencion_largo" name="mantencion" class="form-control"></textarea>
                        </div>

                        <h5>Equipos</h5>
                        <div class="mb-3">
                            <input type="checkbox" id="cobas_e411_largo" name="cobas_e411_largo"> Cobas E411<br>
                        </div>

                        <h5>3.- UMT</h5>
                        <div class="mb-3">
                            <label for="transfusiones_largo">Transfusiones Pendientes y Observaciones:</label>
                            <textarea id="transfusiones_largo" name="transfusiones_largo" class="form-control"></textarea>
                        </div>

                        <h5>STOCK</h5>
                        <div class="mb-3">
                            <input type="checkbox" id="gr_0_largo" name="gr_0_largo"> GR 0+<br>
                            <input type="checkbox" id="gr_a_largo" name="gr_a_largo"> GR A+<br>
                            <input type="checkbox" id="gr_oneg_largo" name="gr_oneg_largo"> GR ONEG<br>
                            <input type="checkbox" id="gr_ab_largo" name="gr_ab_largo"> GR AB+<br>
                            <input type="checkbox" id="pfc_o_largo" name="pfc_o_largo"> PFC O+<br>
                            <input type="checkbox" id="pfc_a_largo" name="pfc_a_largo"> PFC A+<br>
                            <input type="checkbox" id="pfc_b_largo" name="pfc_b_largo"> PFC B+<br>
                            <input type="checkbox" id="pfc_ab_largo" name="pfc_ab_largo"> PFC AB+<br>
                        </div>

                        <h5>4.- Muestras Urgentes</h5>
                        <div class="mb-3">
                            <label for="muestras_pendientes_largo">Pendientes:</label>
                            <textarea id="muestras_pendientes_largo" name="muestras_pendientes_largo" class="form-control"></textarea>
                        </div>

                        <h5>5.- Microbiología</h5>
                        <h6>Valores Críticos</h6>
                        <div class="mb-3">
                            <textarea id="valores_criticos_largo" name="valores_criticos_largo" class="form-control"></textarea>
                        </div>

                        <table class="table table-bordered mb-3">
                            <tr>
                                <th>Valores</th>
                                <th>Observaciones</th>
                            </tr>
                            <tr>
                                <td>Gram Hemocultivo</td>
                                <td><input type="text" name="gram_hemocultivo_largo" class="form-control"></td>
                            </tr>
                            <tr>
                                <td>Gram Líquidos</td>
                                <td><input type="text" name="gram_liquidos_largo" class="form-control"></td>
                            </tr>
                        </table>

                        <h5>Paneles Pendientes, Otras Observaciones</h5>
                        <div class="mb-3">
                            <textarea id="paneles_pendientes_largo" name="paneles_pendientes_largo" class="form-control"></textarea>
                        </div>

                        <h5>6.- Otras Observaciones, Cambios de Lote y Reactivos</h5>
                        <div class="mb-3">
                            <textarea id="cambios_lote_largo" name="cambios_lote_largo" class="form-control"></textarea>
                        </div>

                        <h5>7.- Insumos o Reactivos Críticos</h5>
                        <div class="mb-3">
                            <textarea id="insumos_criticos_largo" name="insumos_criticos_largo" class="form-control"></textarea>
                        </div>

                        <h5>8.- Pendientes COVID</h5>
                        <div class="mb-3">
                            <textarea id="pendientes_covid_largo" name="pendientes_covid_largo" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary">Guardar Turno</button>
                </div>
            </form>
        </div>
    </div>
    <script>
    function mostrarFormulario() {
        var tipoTurno = document.getElementById('tipoturno').value;
        document.getElementById('formulario_largo').style.display = tipoTurno === 'turno_largo' ? 'block' : 'none';
        document.getElementById('formulario_noche').style.display = tipoTurno === 'turno_noche' ? 'block' : 'none';
    }

    function setNombreYPinFuncionario(selectId, nombreId, pinId) {
        var selectElement = document.getElementById(selectId);
        var selectedOption = selectElement.options[selectElement.selectedIndex];

        if (selectedOption) {
            var nombreFuncionario = selectedOption.getAttribute('data-nombre') || "";
            var pinFuncionario = selectedOption.getAttribute('data-pin') || "";

            document.getElementById(nombreId).value = nombreFuncionario;
            document.getElementById(pinId).value = pinFuncionario;

            // Actualizar campos ocultos en los formularios
            document.getElementById(selectId + '_noche').value = selectElement.value;
            document.getElementById(nombreId + '_noche').value = nombreFuncionario;
            document.getElementById(pinId + '_noche').value = pinFuncionario;

            document.getElementById(selectId + '_largo').value = selectElement.value;
            document.getElementById(nombreId + '_largo').value = nombreFuncionario;
            document.getElementById(pinId + '_largo').value = pinFuncionario;
        }
    }

    function validarYEnviar(event) {
        const funcionarioSaliente = document.getElementById('funcionario_saliente');
        const contrasenaSaliente = document.getElementById('contrasena_saliente');
        const pinCorrectoSaliente = funcionarioSaliente.options[funcionarioSaliente.selectedIndex].getAttribute('data-pin');

        if (!funcionarioSaliente || !contrasenaSaliente || !pinCorrectoSaliente) {
            alert('Error: No se encontraron los campos de funcionarios o PIN.');
            event.preventDefault();
            return false;
        }

        // Verificar que se haya seleccionado un funcionario
        if (funcionarioSaliente.value === "") {
            alert('Por favor, selecciona el funcionario saliente.');
            event.preventDefault();
            return false;
        }

        if (contrasenaSaliente.value.trim() !== pinCorrectoSaliente.trim()) {
            alert('El PIN del Funcionario Saliente es incorrecto.');
            event.preventDefault();
            return false;
        }

        // Guardar la contraseña ingresada
        document.getElementById('contrasena_saliente_noche').value = contrasenaSaliente.value;
        document.getElementById('contrasena_saliente_largo').value = contrasenaSaliente.value;

        return true;
    }

    document.querySelector('form').addEventListener('submit', function(event) {
        if (!validarYEnviar(event)) {
            event.preventDefault();
        }
    });
</script>