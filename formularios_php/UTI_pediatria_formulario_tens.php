<?php
session_start();

// Verificar si el usuario inició sesión
if (!isset($_SESSION['id_usuarios'])) {
    header("Location: index.php");
    exit();
}

// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir conexión a la base de datos
include '/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';
mysqli_set_charset($conn, "utf8mb4");

$fecha_actual = date("Y-m-d");

// Consulta para obtener los funcionarios de Microbiología
$sql = "SELECT id_funcionarios, id_profesion, nombre_funcionarios, rut_funcionarios, pin_funcionarios 
        FROM funcionarios_pediatria
        WHERE id_servicio = 14
        AND id_profesion IN (3) 
        ORDER BY id_funcionarios";
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
    <title>Entrega turno TENS Pediatria</title>
    <link rel="icon" href="imagen/logohsc.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php include '/laragon/www/Sistema_entrega_turnos_HSC/navbar.php'; ?>
    <style>
        .highlight {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: rgb(0, 0, 0);
            padding: 1rem;
            border-radius: .25rem;
        }

        .table-bordered {
            border: 2px solid #dee2e6;
        }

        .table thead {
            background-color: #f5c6cb;
            color: white;
            text-align: center;
        }

        .table tbody tr:nth-child(odd) {
            background-color: #f8f9fa;
        }

        .table td,
        .table th {
            text-align: center;
            vertical-align: middle;
        }

        .form-control-sm {
            width: 100%;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-check {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <div class="container p-4 border rounded">
            <h2 class="text-center">Entrega de turno TENS - Servicio Pediatria</h2>
            <p class="text-center">Hospital Santa Cruz</p>
            <br>
            <form action="guardar_turno_pd_tens.php" method="POST">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="fecha">Fecha:</label>
                        <input type="date" id="fecha" name="fecha" value="<?php echo $fecha_actual; ?>" class="form-control form-control-sm" readonly />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="tipoturno">Selecciona el turno:</label>
                        <select id="tipoturno" name="tipoturno" class="form-select form-select-sm" required>
                            <option value="turno_largo">Turno Largo</option>
                            <option value="turno_noche">Turno Noche</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="horario">Horario:</label>
                        <input type="text" id="horario" name="horario" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="highlight mb-1">
                    <div class="col-md-9 mb-1">
                        <label for="nombre_tens_sala">Nombre Tens Sala:</label>
                        <input type="text" id="nombre_tens_sala" name="nombre_tens_sala" class="form-control form-control-sm">
                    </div>
                </div>
                <hr>
                <br>
                <h5 class="text-center">Resumen Evolucion Pacientes</h5>
                <br>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Habitación</th>
                            <th>Paciente</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>96-1</td>
                            <td><input type="text" name="paciente_1" class="form-control"></td>
                        </tr>
                        <tr>
                            <td>96-2</td>
                            <td><input type="text" name="paciente_2" class="form-control"></td>
                        </tr>
                        <tr>
                            <td>96-3</td>
                            <td><input type="text" name="paciente_3" class="form-control"></td>
                        </tr>
                        <tr>
                            <td>114-1</td>
                            <td><input type="text" name="paciente_4" class="form-control"></td>
                        </tr>
                        <tr>
                            <td>114-2</td>
                            <td><input type="text" name="paciente_5" class="form-control"></td>
                        </tr>
                        <tr>
                            <td>114-3</td>
                            <td><input type="text" name="paciente_6" class="form-control"></td>
                        </tr>
                    </tbody>
                </table>
                <div class="mb-3">
                    <label for="novedades">Novedades y Pendientes:</label>
                    <textarea id="novedades" name="novedades" class="form-control"></textarea>
                </div>

                <br>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Entrega de Insumos y Equipos</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="saturometros" name="saturometros">
                            <label class="form-check-label" for="saturometros">Saturometros</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="alcoholes" name="alcoholes">
                            <label class="form-check-label" for="alcoholes">Alcoholes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="pendrive_pediatria" name="pendrive_pediatria">
                            <label class="form-check-label" for="pendrive_pediatria">Pendrive Pediatría</label>
                        </div>

                        <div class="col-md-6">
                            <h6>Equipos en Préstamo</h6>
                            <label for="equipos_prestamo">Equipos en Préstamo:</label>
                            <input type="text" id="equipos_prestamo" name="equipos_prestamo" class="form-control mb-2">
                            <label for="servicio">Servicio:</label>
                            <input type="text" id="servicio" name="servicio" class="form-control">
                        </div>
                        <br>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Revisión y Actividades de Gestión Clínica</h6>
                                <div class="checkbox-group">
                                    <input type="checkbox" id="tablillas" name="tablillas">
                                    <label for="tablillas">Tablillas</label>
                                </div>
                                <div class="checkbox-group">
                                    <input type="checkbox" id="libros" name="libros">
                                    <label for="libros">Libros</label>
                                </div>
                                <div class="checkbox-group">
                                    <input type="checkbox" id="pecheras" name="pecheras">
                                    <label for="pecheras">Pecheras</label>
                                </div>
                                <div class="checkbox-group">
                                    <input type="checkbox" id="rotulos" name="rotulos">
                                    <label for="rotulos">Rótulos</label>
                                </div>
                                <div class="checkbox-group">
                                    <input type="checkbox" id="material_esteril" name="material_esteril">
                                    <label for="material_esteril">Entrega y Recepción Material Esteril</label>
                                </div>
                                <div class="checkbox-group">
                                    <input type="checkbox" id="fichas_medicas" name="fichas_medicas">
                                    <label for="fichas_medicas">Fichas Médicas Actualizadas</label>
                                </div>
                                <div class="checkbox-group">
                                    <input type="checkbox" id="actualizacion_egresos" name="actualizacion_egresos">
                                    <label for="actualizacion_egresos">Actualización de Egresos</label>
                                </div>
                                <div class="checkbox-group">
                                    <input type="checkbox" id="carro_insumos" name="carro_insumos">
                                    <label for="carro_insumos">Carro Insumos Repuesto</label>
                                </div>
                                <div class="checkbox-group">
                                    <input type="checkbox" id="salas_con_epp" name="salas_con_epp">
                                    <label for="salas_con_epp">Salas con EPP</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Aseo</h6>
                        <div class="form-check">
                            <label>Baños limpios:</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="banos" id="banos_si" value="si">
                                <label class="form-check-label" for="banos_si">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="banos" id="banos_no" value="no">
                                <label class="form-check-label" for="banos_no">NO</label>
                            </div>
                        </div>
                        <div class="form-check">
                            <label>Pisos limpios:</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="pisos" id="pisos_si" value="si">
                                <label class="form-check-label" for="pisos_si">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="pisos" id="pisos_no" value="no">
                                <label class="form-check-label" for="pisos_no">NO</label>
                            </div>
                        </div>
                        <div class="form-check">
                            <label>DA AVISO:</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="da_aviso" id="da_aviso_si" value="si">
                                <label class="form-check-label" for="da_aviso_si">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="da_aviso" id="da_aviso_no" value="no">
                                <label class="form-check-label" for="da_aviso_no">NO</label>
                            </div>
                        </div>
                        <div class="form-check">
                            <label>Chatas/patos limpios:</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="chatas" id="chatas_si" value="si">
                                <label class="form-check-label" for="chatas_si">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="chatas" id="chatas_no" value="no">
                                <label class="form-check-label" for="chatas_no">NO</label>
                            </div>
                        </div>
                        <div class="form-check">
                            <label>Aseo terminal correcto:</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="aseo_terminal" id="aseo_terminal_si" value="si">
                                <label class="form-check-label" for="aseo_terminal_si">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="aseo_terminal" id="aseo_terminal_no" value="no">
                                <label class="form-check-label" for="aseo_terminal_no">NO</label>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="mb-3">
                        <label for="observaciones">Observaciones:</label>
                        <textarea id="observaciones" name="observaciones" class="form-control"></textarea>
                    </div>
                </div>
                <br>
                <!-- TENS TURNANTES -->
                <h6 class="text-center">TENS Pediatria turnantes:</h6>
                <br>
                <div class="row">
                    <!-- TENS Saliente 1 -->
                    <div class="col-md-6 mb-3">
                        <label for="funcionario_saliente_1" class="form-label">Tens Pediatria Saliente 1</label>
                        <select id="funcionario_saliente_1" name="funcionario_saliente_1" class="form-select form-select-sm" required onchange="setNombreYPinFuncionario(this, 'nombre_funcionario_saliente_1', 'pin_funcionario_saliente_1')">
                            <option value="">-Seleccione Funcionario-</option>
                            <?php
                            while ($funcionario = mysqli_fetch_assoc($result)) {
                                echo "<option value='{$funcionario['id_funcionarios']}' data-nombre='{$funcionario['nombre_funcionarios']}' data-pin='{$funcionario['pin_funcionarios']}'>
                    {$funcionario['nombre_funcionarios']} - {$funcionario['rut_funcionarios']}
                </option>";
                            }
                            ?>
                        </select>
                        <input type="hidden" id="nombre_funcionario_saliente_1" name="nombre_funcionario_saliente_1">
                        <input type="hidden" id="pin_funcionario_saliente_1" name="pin_funcionario_saliente_1">
                        <input type="password" id="contrasena_saliente_1" name="contrasena_saliente_1" placeholder="Ingrese Contraseña Anestesista 1" required class="form-control form-control-sm mt-2">
                    </div>

                    <!-- TENS PD Entrante 1 -->
                    <div class="col-md-6 mb-3">
                        <label for="funcionario_entrante_1" class="form-label">Tens Pediatria Entrante 1</label>
                        <select id="funcionario_entrante_1" name="funcionario_entrante_1" class="form-select form-select-sm" required onchange="setNombreYPinFuncionario(this, 'nombre_funcionario_entrante_1', 'pin_funcionario_entrante_1')">
                            <option value="">-Seleccione Funcionario-</option>
                            <?php
                            mysqli_data_seek($result, 0); // Reset the result pointer to reuse the result set
                            while ($funcionario = mysqli_fetch_assoc($result)) {
                                echo "<option value='{$funcionario['id_funcionarios']}' data-nombre='{$funcionario['nombre_funcionarios']}' data-pin='{$funcionario['pin_funcionarios']}'>
                    {$funcionario['nombre_funcionarios']} - {$funcionario['rut_funcionarios']}
                </option>";
                            }
                            ?>
                        </select>
                        <input type="hidden" id="nombre_funcionario_entrante_1" name="nombre_funcionario_entrante_1">
                        <input type="hidden" id="pin_funcionario_entrante_1" name="pin_funcionario_entrante_1">
                    </div>
                </div>

                <br><br>

                <!-- BOTÓN ENVIAR -->
                <div class="d-grid gap-2 col-4 mx-auto">
                    <button type="submit" class="btn btn-danger">Entregar Turno</button>
                </div>
            </form>
        </div>

        <script>
            function setNombreYPinFuncionario(selectElement, nombreId, pinId) {
                var selectedOption = selectElement.options[selectElement.selectedIndex];
                document.getElementById(nombreId).value = selectedOption.getAttribute('data-nombre') || "";
                document.getElementById(pinId).value = selectedOption.getAttribute('data-pin') || "";
            }

            function validarYEnviaranestesistas(event) {
                const funcionarioSaliente = document.getElementById('funcionario_saliente_1');
                const contraseñaSaliente = document.getElementById('contrasena_saliente_1');

                if (!funcionarioSaliente.value) {
                    alert('Por favor, selecciona un anestesista saliente.');
                    event.preventDefault();
                    return false;
                }

                const pinCorrecto = funcionarioSaliente.options[funcionarioSaliente.selectedIndex].getAttribute('data-pin');

                if (!pinCorrecto || contraseñaSaliente.value.trim() !== pinCorrecto.trim()) {
                    alert('El PIN ingresado no es correcto.');
                    event.preventDefault();
                    return false;
                }

                return true;
            }

            document.querySelector('form').addEventListener('submit', function(event) {
                if (!validarYEnviaranestesistas(event)) {
                    event.preventDefault();
                }
            });
        </script>

        <script src="/js/funcion_agregarfuncionario.js"></script>
        <script src="/js/funcion_mostrarcampotexto.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    </div>
</body>

</html>