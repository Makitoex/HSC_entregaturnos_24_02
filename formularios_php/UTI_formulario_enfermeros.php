<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Establecer la conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "sistema_entrega_turnos_hsc");

// Verificar si la conexión ha fallado
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Establecer el charset
mysqli_set_charset($conn, "utf8mb4");

// Verificar si el usuario inició sesión
if (!isset($_SESSION['id_usuarios'])) {
    header("Location: index.php");
    exit();
}

$fecha_actual = date("Y-m-d");

// CONSULTA PARA OBTENER LOS FUNCIONARIOS DE UTI
$sql = "SELECT id_funcionarios, id_profesion, nombre_funcionarios, rut_funcionarios, pin_funcionarios FROM funcionarios_uti WHERE id_servicio = 1 ORDER BY id_funcionarios";
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
    <title>Entrega de Turno Enfermeros UTI</title>

    <!-- ICONO DE PAGINA -->
    <link rel="icon" href="imagen/logohsc.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <?php include '/laragon/www/Sistema_entrega_turnos_HSC/navbar.php'; ?>

</head>

<body>
    <br>
    <!--CONTENEDOR PRINCIPAL-->
    <div class="container">
        <br><br><br>
        <h2 align="center">Entrega De Turno Enfermeros UTI</h2>
        <p align="center">Hospital Santa Cruz</p>
        <form action="guardar_turno_enfermeros.php" method="POST">
            <br>
            <!--FECHA Y TURNO-->
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
            </div>
            <br>
            <hr>
            <br>
            <h6 style="text-align: center;">Funcionarios en Turno:</h6>
            <br>

            <!-- SELECCION DE FUNCIONARIOS -->
            <form id="formTurno">
                <div class="row">
                    <div class="col-md-3">
                        <label for="medico_turno">Médicos de Turno:</label>
                        <input type="text" id="medico_turno" name="medico_turno" class="form-control" placeholder="Ej: Dr. Pérez, Dr. Gómez">
                    </div>

                    <div class="col-md-3">
                        <label for="tens_turno">Tens de Turno:</label>
                        <input type="text" id="tens_turno" name="tens_turno" class="form-control" placeholder="Ej: Juan, Pedro">
                    </div>

                    <div class="col-md-3">
                        <label for="auxiliar_turno">Auxiliares de Turno:</label>
                        <input type="text" id="auxiliar_turno" name="auxiliar_turno" class="form-control" placeholder="Ej: María, José">
                    </div>

                    <div class="col-md-3">
                        <label for="kinesiologo_turno">Kinesiólogos de Turno:</label>
                        <input type="text" id="kinesiologo_turno" name="kinesiologo_turno" class="form-control" placeholder="Ej: Luis, Ana">
                    </div>
                </div>
                <br>

                <hr>
                <!-- CONTROLES MEDICO , CARRO Y BOTIQUIN -->
                <br>
                <div class="row">
                    <div class="col-md-4">
                        <label for="controlmedico">Control por medico residente:</label>
                        <br><br>
                        <select id="turno" name="control_medico_residente" class="form-select form-select-sm" required>
                            <option value="control_si">Si , todos los pacientes controlados</option>
                            <option value="control_si_pero">Si , pero faltaron pacientes</option>
                            <option value="control_no">No se realiza control medico</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="carrodeparos">Carro de paros:</label>
                        <br><br>
                        <select id="carrodeparos" name="carro_paros" class="form-select form-select-sm" required>
                            <option value="carro_no">No se utiliza</option>
                            <option value="carro_si">Si , se utilizó</option>
                            <option value="carro_renovo">Se renovó en turno</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="botiquin">Botiquin:</label>
                        <br><br>
                        <select id="botiquin" name="botiquin" class="form-select form-select-sm" required>
                            <option value="botiquin_completo">Completo</option>
                            <option value="botiquin_vacio">Vacio</option>
                            <option value="botiquin_parcialmente">Parcialmente</option>
                        </select>
                        <br>
                    </div>

                    <!--ROW con camas ocupadas , dispo , reservadas-->
                    <br><br><br><br><br>
                    <div class="row">
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
                            <br>
                        </div>
                        <br>
                    </div>
                    <br><br><br><br>

                    <!-- PACIENTES FALLECIDOS -->
                    <h6 align="center">Pacientes Fallecidos</h6>
                    <label for="pacientesfallecidos">Cantidad pacientes fallecidos:</label>
                    <br><br>
                    <select id="cantpacientesfallecidos" name="cant_pacientes_fallecidos" class="form-select form-select-sm" onchange="mostrarCampoTexto()" required>
                        <option value="">Selecciona...</option>
                        <option value="0">0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                    <br>
                    <div id="detallespacientesf" style="display: none;">
                        <label for="detallespacientesf"> Ingrese detalles:</label>
                        <textarea id="detallespacientesf" name="detalles_pacientes_fallecidos" class="form-control form-control-sm"></textarea>
                    <br>
                    </div>
                    <br>
                    <hr>
                    <!--ROW Tabla 1 Medicamentos-->
                    <h6 align="center">Medicamentos</h6>
                    <br>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Medicamento</th>
                                        <th>Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Propofol</td>
                                        <td><input type="text" value="0" name="medicamento_propofol" required></td>
                                    </tr>
                                    <tr>
                                        <td>Precedex</td>
                                        <td><input type="number" name="medicamento_precedex" min="0" step="0.1" value="0" required></td>
                                    </tr>
                                    <tr>
                                        <td>Fenobarbital</td>
                                        <td><input type="number" name="medicamento_fenobarbital" min="0" step="0.1" value="0" required></td>
                                    </tr>
                                    <tr>
                                        <td>Alprazolam 0.5mg</td>
                                        <td><input type="number" name="medicamento_alprazolam_0_5mg" min="0" step="0.1" value="0" required></td>
                                    </tr>
                                    <tr>
                                        <td>Haldol 5mg</td>
                                        <td><input type="number" name="medicamento_haldol_5mg" min="0" step="0.1" value="0" required></td>
                                    </tr>
                                    <tr>
                                        <td>Diazepam VO</td>
                                        <td><input type="number" name="medicamento_diazepam_vo" min="0" step="0.1" value="0" required></td>
                                    </tr>
                                    <tr>
                                        <td>Diazepam EV</td>
                                        <td><input type="number" name="medicamento_diazepam_ev" min="0" step="0.1" value="0" required></td>
                                    </tr>
                                    <tr>
                                        <td>Clonazepam 0.5mg</td>
                                        <td><input type="number" name="medicamento_clonazepam_0_5mg" min="0" step="0.1" value="0" required></td>
                                    </tr>
                                    <tr>
                                        <td>Clonazepam 2mg</td>
                                        <td><input type="number" name="medicamento_clonazepam_2mg" min="0" step="0.1" value="0" required></td>
                                    </tr>
                                    <tr>
                                        <td>Haloperidol 1mg</td>
                                        <td><input type="number" name="medicamento_haloperidol_1mg" min="0" step="0.1" value="0" required></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!--ROW Tabla 2 Medicamentos-->
                        <div class="col-md-6">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Medicamento</th>
                                        <th>Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Ketamina</td>
                                        <td><input type="number" name="medicamento_ketamina" min="0" step="0.1" value="0" required></td>
                                    </tr>
                                    <tr>
                                        <td>Ramifentanilo 1mg</td>
                                        <td><input type="number" name="medicamento_ramifentanilo_1mg" min="0" step="0.1" value="0" required></td>
                                    </tr>
                                    <tr>
                                        <td>Lorazepam 2mg</td>
                                        <td><input type="number" name="medicamento_lorazepam_2mg" min="0" step="0.1" value="0" required></td>
                                    </tr>
                                    <tr>
                                        <td>Matadona</td>
                                        <td><input type="number" name="medicamento_metadona" min="0" step="0.1" value="0" required></td>
                                    </tr>
                                    <tr>
                                        <td>Morfina</td>
                                        <td><input type="number" name="medicamento_morfina" min="0" step="0.1" value="0" required></td>
                                    </tr>
                                    <tr>
                                        <td>Midazolam 5mg</td>
                                        <td><input type="number" name="medicamento_midazolam_5mg" min="0" step="0.1" value="0" required></td>
                                    </tr>
                                    <tr>
                                        <td>Midazolam 50mg</td>
                                        <td><input type="number" name="medicamento_midazolam_50mg" min="0" step="0.1" value="0" required></td>
                                    </tr>
                                    <tr>
                                        <td>Fentanilo 0.1mg</td>
                                        <td><input type="number" name="medicamento_fentanilo_0_1mg" min="0" step="0.1" value="0" required></td>
                                    </tr>
                                    <tr>
                                        <td>Fentanilo 0.5mg</td>
                                        <td><input type="number" name="medicamento_fentanilo_0_5mg" min="0" step="0.1" value="0" required></td>
                                    </tr>
                                    <tr>
                                        <td>Otros: </td>
                                        <td><input type="text" name="medicamento_otros" min="0" step="1" reqired></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <!--LABEL TRASLADOS-->
                    <label for="traslados_detalle">Traslados en curso o pendientes:</label>
                    <textarea id="traslados_detalle" name="traslados_detalle" rows="3" cols="40" placeholder="Describa los traslados en curso o si estan pendientes..."></textarea>

                    <br>
                    <!--LABEL EVENTOS ADVER-->
                    <label for="eventos_detalle">Eventos Adversos:</label>
                    <textarea id="eventos_detalle" name="eventos_detalle" rows="3" cols="40" placeholder="Describa los eventos adversos y/o centinelas ocurridos durante el turno..."></textarea>

                    <br>
                    <!--LABEL COMENTARIOS-->
                    <label for="comentarios_detalle">Comentarios clínicos/administrativos relevantes:</label>
                    <textarea id="comentarios_detalle" name="comentarios_detalle" rows="3" cols="40" placeholder="Deje su comentario..."></textarea>
                </div>
                <br>
                <hr>
                <!--Row con funcionarios , seleccion y contrasea-->
                <h6 style="text-align: center;">Enfermeros turnantes:</h6>
                <br>
                <div class="row">
                    <!--INPUT SALIENTE 1 -->
                    <div class="col-md-3">
                        <label for="funcionario_saliente_1">Enfermero Saliente 1</label>
                        <select id="funcionario_saliente_1" name="funcionario_saliente_1" class="form-select form-select-sm" required>
                            <option value="">-Seleccione Funcionario-</option>
                            <?php
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
                        <br>
                        <!--INPUT SALIENTE 2 -->
                        <input type="password" id="contrasena_saliente_1" name="contrasena_saliente_1" placeholder="Ingrese Contraseña Enf.1" required>
                    </div>
                    <div class="col-md-3">
                        <label for="funcionario_saliente_2">Enfermero Saliente 2</label>
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
                                echo "<option value=''>No hay enfermeros disponibles</option>";
                            }
                            ?>
                        </select>
                        <br>
                        <input type="password" id="contrasena_saliente_2" name="contrasena_saliente_2" placeholder="Ingrese Contraseña Enf.2" required>
                    </div>
                    <!--INPUT ENTRANTE 1 -->
                    <div class="col-md-3">
                        <label for="funcionario_entrante_1">Enfermero Entrante 1</label>
                        <select id="funcionario_entrante_1" name="funcionario_entrante_1" class="form-select form-select-sm" required>
                            <option value="">-Seleccione Funcionario-</option>
                            <?php
                            mysqli_data_seek($result, 0); //vuelve a leer los datos del resultado
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $id_funcionarios = $row['id_funcionarios'];
                                    $nombre_funcionarios = $row['nombre_funcionarios'];
                                    $rut_funcionarios = $row['rut_funcionarios'];
                                    echo "<option value='$id_funcionarios'>$nombre_funcionarios - $rut_funcionarios</option>";
                                }
                            } else {
                                echo "<option value=''>No hay enfermeros disponibles</option>";
                            }
                            ?>
                        </select>
                        <br>
                    </div>
                    <!--INPUT ENFEMERO ENTRANTE 2 -->
                    <div class="col-md-3">
                        <label for="funcionario_entrante_2">Enfermero Entrante 2</label>
                        <select id="funcionario_entrante_2" name="funcionario_entrante_2" class="form-select form-select-sm" required>
                            <option value="">-Seleccione Funcionario-</option>
                            <?php
                            mysqli_data_seek($result, 0); //vuelve a leer los datos del resultado
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $id_funcionarios = $row['id_funcionarios'];
                                    $nombre_funcionarios = $row['nombre_funcionarios'];
                                    $rut_funcionarios = $row['rut_funcionarios'];
                                    echo "<option value='$id_funcionarios'>$nombre_funcionarios - $rut_funcionarios</option>";
                                }
                            } else {
                                echo "<option value=''>No hay enfermeros disponibles</option>";
                            }
                            ?>
                        </select>
                        <br>
                        <br>
                    </div>
                </div>
                <br><br>
                <!--Boton eENVIAR FORMULARIO-->
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