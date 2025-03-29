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
        FROM funcionarios_pabellon 
        WHERE id_servicio = 12
        AND id_profesion IN (7, 3) 
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
    <title>Entrega de turnos TENS</title>
    <link rel="icon" href="imagen/logohsc.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <?php include '/laragon/www/Sistema_entrega_turnos_HSC/navbar.php'; ?>
    <style>
        .highlight {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: rgb(0, 0, 0);
            padding: 1rem;
            border-radius: .25rem;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <div class="container p-4 border rounded">
            <h2 class="text-center">Entrega De Turno TENS Servicio de Pabellon</h2>
            <p class="text-center">Hospital Santa Cruz</p>

            <form action="guardar_turno_pb_tens.php" method="POST">
                <div class="row">
                    <!-- Fecha y turno -->
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
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6 mb-2 highlight">
                        <label for="nombre_anestesiologo_turno">Nombre Anestesiologo de turno:</label>
                        <input type="text" id="nombre_anestesiologo_turno" name="nombre_anestesiologo_turno" class="form-control">
                    </div>
                    <div class="col-md-6 mb-2 highlight">
                        <label for="nombre_enfermera_turno">Nombre Enfermera de turno:</label>
                        <input type="text" id="nombre_enfermera_turno" name="nombre_enfermera_turno" class="form-control">
                    </div>
                </div>

                <hr>

                <!-- Nombre Tec. Anestesia de turno -->
                <div class="mb-3 highlight">
                    <label for="nombre_tecanestesia_turno">Nombre Tec. Anestesia de turno:</label>
                    <input type="text" id="nombre_tecanestesia_turno" name="nombre_tecanestesia_turno" class="form-control">
                </div>

                <!-- Reposiciones y eliminaciones -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Reposición bandeja de Peridural:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reposicion_peridural" value="si" id="reposicion_peridural_si">
                            <label class="form-check-label" for="reposicion_peridural_si">Sí</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reposicion_peridural" value="no" id="reposicion_peridural_no">
                            <label class="form-check-label" for="reposicion_peridural_no">No</label>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Reposición carro de Anestesia:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reposicion_carro_anestesia" value="si" id="reposicion_carro_anestesia_si">
                            <label class="form-check-label" for="reposicion_carro_anestesia_si">Sí</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reposicion_carro_anestesia" value="no" id="reposicion_carro_anestesia_no">
                            <label class="form-check-label" for="reposicion_carro_anestesia_no">No</label>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6 mb-3">
                        <label>Eliminación de medicamentos sobrantes de bandeja:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="eliminacion_medicamentos" value="si" id="eliminacion_medicamentos_si">
                            <label class="form-check-label" for="eliminacion_medicamentos_si">Sí</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="eliminacion_medicamentos" value="no" id="eliminacion_medicamentos_no">
                            <label class="form-check-label" for="eliminacion_medicamentos_no">No</label>
                        </div>
                    </div>
                </div>

                <!-- Novedades -->
                <div class="mb-3">
                    <label for="novedades">Novedades:</label>
                    <textarea id="novedades" name="novedades" class="form-control"></textarea>
                </div>

                <hr>

                <!-- Arsenalero -->
                <div class="mb-3 highlight">
                    <label for="arsenalero">Nombre Arsenalero de turno:</label>
                    <input type="text" id="arsenalero" name="arsenalero" class="form-control">
                </div>

                <!-- Recibo de insumos -->
                <div class="mb-3">
                    <label>Se recibe insumos y/o instrumental empresa externa:</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="insumos_empresa_externa" value="si" id="insumos_empresa_externa_si">
                        <label class="form-check-label" for="insumos_empresa_externa_si">Sí</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="insumos_empresa_externa" value="no" id="insumos_empresa_externa_no">
                        <label class="form-check-label" for="insumos_empresa_externa_no">No</label>
                    </div>
                </div>

                <!-- Stock de instrumental -->
                <div class="mb-3">
                    <label>Stock de insumos e instrumental para tabla quirúrgica:</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="stock_instrumental" value="si" id="stock_instrumental_si">
                        <label class="form-check-label" for="stock_instrumental_si">Sí</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="stock_instrumental" value="no" id="stock_instrumental_no">
                        <label class="form-check-label" for="stock_instrumental_no">No</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="novedades_instrumental">Novedades/Instrumental recibido:</label>
                    <textarea id="novedades_instrumental" name="novedades_instrumental" class="form-control"></textarea>
                </div>

                <hr>

                <!-- Pabellonero -->
                <div class="mb-3 highlight">
                    <label for="pabellonero">Nombre Pabellonero de turno:</label>
                    <input type="text" id="pabellonero" name="pabellonero" class="form-control">
                </div>

                <div class="row">
                    <!-- Reposición carro de recuperación -->
                    <div class="col-md-6 mb-3">
                        <label>Reposición carro de recuperación:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reposicion_carro_recuperacion" value="si" id="reposicion_carro_recuperacion_si">
                            <label class="form-check-label" for="reposicion_carro_recuperacion_si">Sí</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="reposicion_carro_recuperacion" value="no" id="reposicion_carro_recuperacion_no">
                            <label class="form-check-label" for="reposicion_carro_recuperacion_no">No</label>
                        </div>
                    </div>

                    <!-- Cambio de humidificadores -->
                    <div class="col-md-6 mb-3">
                        <label>Cambio de humidificadores:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cambio_humidificadores" value="si" id="cambio_humidificadores_si">
                            <label class="form-check-label" for="cambio_humidificadores_si">Sí</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cambio_humidificadores" value="no" id="cambio_humidificadores_no">
                            <label class="form-check-label" for="cambio_humidificadores_no">No</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Revisión y registro temperaturas de pabellones -->
                    <div class="col-md-6 mb-3">
                        <label>Revisión y registro temperaturas de pabellones:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="revision_temperaturas_pabellones" value="si" id="revision_temperaturas_pabellones_si">
                            <label class="form-check-label" for="revision_temperaturas_pabellones_si">Sí</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="revision_temperaturas_pabellones" value="no" id="revision_temperaturas_pabellones_no">
                            <label class="form-check-label" for="revision_temperaturas_pabellones_no">No</label>
                        </div>
                    </div>

                    <!-- Pacientes ingresados CMA -->
                    <div class="col-md-6 mb-3">
                        <label>Pacientes ingresados CMA:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="pacientes_cma" value="si" id="pacientes_cma_si">
                            <label class="form-check-label" for="pacientes_cma_si">Sí</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="pacientes_cma" value="no" id="pacientes_cma_no">
                            <label class="form-check-label" for="pacientes_cma_no">No</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Cantidad de biopsias y órdenes recibidas -->
                    <div class="col-md-6 mb-3">
                        <label for="biopsias_ordenes_recibidas">Cantidad de biopsias y órdenes recibidas:</label>
                        <input type="number" id="biopsias_ordenes_recibidas" name="biopsias_ordenes_recibidas" class="form-control">
                    </div>

                    <!-- Cantidad de biopsias y órdenes entregadas -->
                    <div class="col-md-6 mb-3">
                        <label for="biopsias_ordenes_entregadas">Cantidad de biopsias y órdenes entregadas:</label>
                        <input type="number" id="biopsias_ordenes_entregadas" name="biopsias_ordenes_entregadas" class="form-control">
                    </div>
                </div>

                <hr>
                <h5 class="text-center">Fin de semana</h5>

                <!-- Limpieza del Pyxis -->
                <div class="mb-3">
                    <label>Limpieza del Pyxis:</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="limpieza_pyxis" value="si" id="limpieza_pyxis_si">
                        <label class="form-check-label" for="limpieza_pyxis_si">Sí</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="limpieza_pyxis" value="no" id="limpieza_pyxis_no">
                        <label class="form-check-label" for="limpieza_pyxis_no">No</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="limpieza_pyxis" value="no_corresponde" id="limpieza_pyxis_no_corresponde">
                        <label class="form-check-label" for="limpieza_pyxis_no_corresponde">No corresponde</label>
                    </div>
                </div>

                <!-- Limpieza de Bodegas -->
                <div class="mb-3">
                    <label>Limpieza de Bodegas:</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="limpieza_bodegas" value="si" id="limpieza_bodegas_si">
                        <label class="form-check-label" for="limpieza_bodegas_si">Sí</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="limpieza_bodegas" value="no" id="limpieza_bodegas_no">
                        <label class="form-check-label" for="limpieza_bodegas_no">No</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="limpieza_bodegas" value="no_corresponde" id="limpieza_bodegas_no_corresponde">
                        <label class="form-check-label" for="limpieza_bodegas_no_corresponde">No corresponde</label>
                    </div>
                </div>

                <hr>

                <h5 class="text-center"> ENTRANTES A TURNO</h5>

                <br>

                <form action="guardar_turno_pb_tens.php" method="POST">
                    <div class="row">
                        <!-- Tec. Anestesia -->
                        <div class="col-md-4 mb-3">
                            <label for="funcionario_tecanestesia">Tec. Anestesia</label>
                            <select id="funcionario_tecanestesia" name="funcionario_tecanestesia" class="form-select" required>
                                <option value="">Seleccionar funcionario</option>
                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <option value="<?php echo $row['id_funcionarios']; ?>" data-nombre="<?php echo $row['nombre_funcionarios']; ?>" data-pin="<?php echo $row['pin_funcionarios']; ?>">
                                        <?php echo $row['nombre_funcionarios'] . ' - ' . $row['rut_funcionarios']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <input type="password" id="contrasena_tecanestesia" name="contrasena_tecanestesia" placeholder="Ingrese PIN" required class="form-control mt-2">
                            <input type="hidden" id="nombre_funcionario_tecanestesia" name="nombre_funcionario_tecanestesia">
                        </div>

                        <!-- Arsenalero -->
                        <div class="col-md-4 mb-3">
                            <label for="funcionario_arsenalero">Arsenalero</label>
                            <select id="funcionario_arsenalero" name="funcionario_arsenalero" class="form-select" required>
                                <option value="">Seleccionar funcionario</option>
                                <?php mysqli_data_seek($result, 0); ?>
                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <option value="<?php echo $row['id_funcionarios']; ?>" data-nombre="<?php echo $row['nombre_funcionarios']; ?>" data-pin="<?php echo $row['pin_funcionarios']; ?>">
                                        <?php echo $row['nombre_funcionarios'] . ' - ' . $row['rut_funcionarios']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <input type="password" id="contrasena_arsenalero" name="contrasena_arsenalero" placeholder="Ingrese PIN" required class="form-control mt-2">
                            <input type="hidden" id="nombre_funcionario_arsenalero" name="nombre_funcionario_arsenalero">
                        </div>

                        <!-- Pabellonero -->
                        <div class="col-md-4 mb-3">
                            <label for="funcionario_pabellonero">Pabellonero</label>
                            <select id="funcionario_pabellonero" name="funcionario_pabellonero" class="form-select" required>
                                <option value="">Seleccionar funcionario</option>
                                <?php mysqli_data_seek($result, 0); ?>
                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <option value="<?php echo $row['id_funcionarios']; ?>" data-nombre="<?php echo $row['nombre_funcionarios']; ?>" data-pin="<?php echo $row['pin_funcionarios']; ?>">
                                        <?php echo $row['nombre_funcionarios'] . ' - ' . $row['rut_funcionarios']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <input type="password" id="contrasena_pabellonero" name="contrasena_pabellonero" placeholder="Ingrese PIN" required class="form-control mt-2">
                            <input type="hidden" id="nombre_funcionario_pabellonero" name="nombre_funcionario_pabellonero">
                        </div>
                    </div>

                    <hr>

                    <!-- Botón de envío -->
                    <div class="mb-3 text-center">
                        <button type="submit" class="btn btn-primary" onclick="return validarCredenciales();">Guardar Turno</button>
                    </div>
                </form>

                <script>
                    function validarCredenciales() {
                        let valid = true;

                        ['tecanestesia', 'arsenalero', 'pabellonero'].forEach(function(funcionario) {
                            let select = document.getElementById('funcionario_' + funcionario);
                            let password = document.getElementById('contrasena_' + funcionario);
                            let nombre = select.options[select.selectedIndex].getAttribute('data-nombre');
                            let pinCorrecto = select.options[select.selectedIndex].getAttribute('data-pin');

                            document.getElementById('nombre_funcionario_' + funcionario).value = nombre;

                            if (password.value.trim() !== pinCorrecto.trim()) {
                                alert('PIN incorrecto para ' + funcionario.replace('_', ' '));
                                valid = false;
                            }
                        });

                        return valid;
                    }
                </script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>