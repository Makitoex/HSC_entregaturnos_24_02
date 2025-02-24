<?php
session_start();

// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';
mysqli_set_charset($conn, "utf8mb4");

date_default_timezone_set('America/Santiago');
$fecha_actual = date("Y-m-d"); // La fecha se obtiene en formato 'YYYY-MM-DD'

// Consulta para obtener los funcionarios de UTI
$sql = "SELECT id_funcionarios, id_profesion, nombre_funcionarios, rut_funcionarios, pin_funcionarios FROM funcionarios_uti WHERE id_servicio = 4 ORDER BY id_funcionarios";
$result = mysqli_query($conn, $sql);
$result1 = mysqli_query($conn, $sql);

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

    <!-- Icono de página -->
    <link rel="icon" href="imagen/logohsc.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        textarea[name="diagnosticos_hospitalizado"],
        textarea[name="novedades_hospitalizado"],
        textarea[name="planes_hospitalizado"] {
            width: 100%;
            min-width: 220px;
            border: 2px solid #9999;
            border-radius: 4px;
            padding: 10px;
        }

        textarea[name="nombre"] {
            width: 80;
            min-width: 190px;
        }
    </style>
    <?php include '/laragon/www/Sistema_entrega_turnos_HSC/navbar.php'; ?>
</head>

<body>
    <div class="container mt-4">
        <h2 class="text-center">Entrega De Turno Medico UPC</h2>
        <p class="text-center">Hospital Santa Cruz</p>
        <br>
        <form action="guardar_turno_upc_medicos.php" method="POST">
            <!-- Fecha y Turno -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="fecha">Fecha:</label>
                    <input type="date" id="fecha" name="fecha" value="<?php echo $fecha_actual; ?>" class="form-control form-control-sm" readonly />
                </div>

                <div class="col-md-3">
                    <label for="hora">Hora:</label>
                    <input type="time" id="hora" name="hora" value="<?php echo date('H:i'); ?>" class="form-control form-control-sm" />
                </div>
            </div>

            <br>
            <hr>
            <br>
            <!-- TablaS DE ENTREGA -->
            <h6 align="center">Pacientes Hospitalizados</h6>
            <br>
            <table class="table table-bordered" id="tablaHospitalizados">
                <thead>
                    <tr>
                        <th>Cama</th>
                        <th>Nombre</th>
                        <th>Edad</th>
                        <th>EIH</th>
                        <th>Diagnósticos</th>
                        <th>Novedades</th>
                        <th>Planes/Pendientes</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="cama_hospitalizado" class="form-control form-control-sm"></td>
                        <td><textarea name="nombre_hospitalizado" rows="4" class="form-control form-control-sm"></textarea></td>
                        <td><input type="number" name="edad_hospitalizado" class="form-control form-control-sm"></td>
                        <td><input type="number" name="eih_hospitalizado" class="form-control form-control-sm"></td>
                        <td><textarea name="diagnosticos_hospitalizado" rows="4" class="form-control form-control-sm"></textarea></td>
                        <td><textarea name="novedades_hospitalizado" rows="4" class="form-control form-control-sm"></textarea></td>
                        <td><textarea name="planes_hospitalizado" rows="4" class="form-control form-control-sm"></textarea></td>
                        <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">X</button></td> <!-- Botón para eliminar -->
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-danger" onclick="agregarFilahospitalizados()">Agregar Paciente</button>
            <hr>
            <br>
            <h6 align="center">Pacientes Egresados</h6>
            <br>
            <table class="table table-bordered" id="tablaegresados">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Destino</th>
                        <th>Motivo de Egreso</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><textarea name="nombre_egresado" rows="1" class="form-control form-control-sm"></textarea></td>
                        <td><textarea name="destino_egresado" rows="3" class="form-control form-control-sm"></textarea></td>
                        <td><textarea name="motivo_de_egreso" rows="3" class="form-control form-control-sm"></textarea></td>
                        <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">X</button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-danger" onclick="agregarFilaegresados()">Agregar Paciente</button>
            <hr>
            <br>
            <h6 align="center">Pacientes Fallecidos</h6>
            <br>
            <table class="table table-bordered" id="tablafallecidos">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Edad</th>
                        <th>Hora</th>
                        <th>Diagnosticos</th>
                        <th>Servicio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><textarea name="nombre_fallecido" rows="2" class="form-control form-control-sm"></textarea></td>
                        <td><input type="number" name="edad_fallecido" class="form-control form-control-sm"></td>
                        <td><input type="time" name="hora_fallecido" class="form-control form-control-sm"></td>
                        <td><textarea name="diagnosticos_fallecido" rows="3" class="form-control form-control-sm"></textarea></td>
                        <td><textarea name="servicio_fallecido" rows="3" class="form-control form-control-sm"></textarea></td>
                        <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">X</button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-danger" onclick="agregarFilafallecidos()">Agregar Paciente</button>
            <br>
            <hr>
            <br>
            <h6 align="center">Solicitudes Rechazadas</h6>
            <br>
            <table class="table table-bordered" id="tablarechazadas">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>RUT</th>
                        <th>Diagnosticos</th>
                        <th>Servicio</th>
                        <th>Motivo de Rechazo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><textarea name="nombre_rechazadas" rows="2" class="form-control form-control-sm"></textarea></td>
                        <td><input type="text" name="rut_rechazado" class="form-control form-control-sm"></td>
                        <td><textarea name="diagnostico_rechazado" rows="3" class="form-control form-control-sm"></textarea></td>
                        <td><textarea name="servicio_rechazado" rows="3" class="form-control form-control-sm"></textarea></td>
                        <td><textarea name="motivo_rechazo" rows="3" class="form-control form-control-sm"></textarea></td>
                        <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">X</button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-danger" onclick="agregarFilarechazadas()">Agregar Paciente</button>
            <br>
            <hr>
            <br>
            <h6 align="center">Novedades</h6>
            <br>
            <textarea name="novedades" rows="3" class="form-control form-control-sm" placeholder="Ingrese novedades del turno..."></textarea>
            <hr>
            <br>
            <div class="row mb-3">
                <!-- Medico Entrante -->
                <div class="col-md-6">
                    <label for="funcionario_saliente_1">Medico Saliente :</label>
                    <select id="funcionario_saliente_1" name="funcionario_saliente_1" class="form-select form-select-sm" required>
                        <option value="">-Seleccione Funcionario-</option>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $id_funcionarios = $row['id_funcionarios'];
                                $nombre_funcionarios = $row['nombre_funcionarios'];
                                $rut_funcionarios = $row['rut_funcionarios'];
                                echo "<option value='$id_funcionarios'>$nombre_funcionarios - $rut_funcionarios</option>";
                            }
                        } else {
                            echo "<option value=''>No hay funcionarios disponibles</option>";
                        }
                        ?>
                        <br>
                    </select>
                    <br>
                    <label for="especialidad_saliente">Especialidad Médica (Saliente):</label>
                    <select id="especialidad_saliente" name="especialidad_saliente" class="form-select form-select-sm" required>
                        <option value="">-Seleccione Especialidad-</option>
                        <option value="Medicina General">Medicina General</option>
                        <option value="Medicina Interna">Medicina Interna </option>
                        <option value="Medicina de Urgencias">Medicina de Urgencias </option>
                        <option value="Cardiologia">Cardiología</option>
                        <option value="Dermatologia">Dermatología</option>
                        <option value="Endocrinologia">Endocrinología</option>
                        <option value="Gastroenterologia">Gastroenterología</option>
                        <option value="Geriatria">Geriatría</option>
                        <option value="Ginecologia y Obstetricia">Ginecología y Obstetricia</option>
                        <option value="Hematologia">Hematología</option>
                        <option value="Infectologia">Infectología</option>
                        <option value="Nefrologia">Nefrología</option>
                        <option value="Neumologia">Neumología</option>
                        <option value="Neurologia">Neurología</option>
                        <option value="Oncologia">Oncología</option>
                        <option value="Pediatria">Pediatría</option>
                        <option value="Psiquiatria">Psiquiatría</option>
                        <option value="Radiologia">Radiología</option>
                        <option value="Reumatologia">Reumatología</option>
                        <option value="Traumatologia y Ortopedia">Traumatología y Ortopedia</option>
                        <option value="Urologia">Urología</option>
                        <option value="Otra">--Otra--</option>
                    </select>
                    <br>
                    <input type="password" id="contrasena_saliente_1" name="contrasena_saliente_1" placeholder="Ingrese Contraseña..." required class="form-control form-control-sm">
                    <br>
                </div>

                <div class="col-md-6">
                    <label for="funcionario_entrante_1">Medico Entrante :</label>
                    <select id="funcionario_entrante_1" name="funcionario_entrante_1" class="form-select form-select-sm" required>
                        <option value="">-Seleccione Funcionario-</option>
                        <?php
                        if ($result1->num_rows > 0) {
                            while ($row1 = $result1->fetch_assoc()) {
                                $id_funcionarios = $row1['id_funcionarios'];
                                $nombre_funcionarios = $row1['nombre_funcionarios'];
                                $rut_funcionarios = $row1['rut_funcionarios'];
                                echo "<option value='$id_funcionarios'>$nombre_funcionarios - $rut_funcionarios</option>";
                            }
                        } else {
                            echo "<option value=''>No hay funcionarios disponibles</option>";
                        }
                        ?>
                    </select>
                    <br>
                    <label for="especialidad_entrante">Especialidad Médica (Entrante):</label>
                    <select id="especialidad_entrante" name="especialidad_entrante" class="form-select form-select-sm" required>
                        <option value="">-Seleccione Especialidad-</option>
                        <option value="Medicina General">Medicina General</option>
                        <option value="Medicina Interna">Medicina Interna </option>
                        <option value="Medicina de Urgencias">Medicina de Urgencias </option>
                        <option value="Cardiologia">Cardiología</option>
                        <option value="Dermatologia">Dermatología</option>
                        <option value="Endocrinologia">Endocrinología</option>
                        <option value="Gastroenterologia">Gastroenterología</option>
                        <option value="Geriatria">Geriatría</option>
                        <option value="Ginecologia y Obstetricia">Ginecología y Obstetricia</option>
                        <option value="Hematologia">Hematología</option>
                        <option value="Infectologia">Infectología</option>
                        <option value="Nefrologia">Nefrología</option>
                        <option value="Neumologia">Neumología</option>
                        <option value="Neurologia">Neurología</option>
                        <option value="Oncologia">Oncología</option>
                        <option value="Pediatria">Pediatría</option>
                        <option value="Psiquiatria">Psiquiatría</option>
                        <option value="Radiologia">Radiología</option>
                        <option value="Reumatologia">Reumatología</option>
                        <option value="Traumatologia y Ortopedia">Traumatología y Ortopedia</option>
                        <option value="Urologia">Urología</option>
                        <option value="Otra">--Otra--</option>
                    </select>
                </div>
            </div>
            <br>
            <hr>
            <br>

            <!-- Enviar Formulario -->
            <div class="d-grid gap-2 col-4 mx-auto">
                <button type="submit" class="btn btn-danger">Entregar Turno</button>
            </div>
        </form>
    </div>
</body>
<script src="/js/tabla_paciente_upcm.js"></script>
<script src="/js/funcion_validaryenviarupcmedico.js"></script>
<script src="/js/funcion_obtenerpin.js"></script>

</html>