<?php
// session_start();

// // verificar si el usuario inicio sesion
// if (!isset($_SESSION['id_usuarios'])) {
//     header("Location: index.php");
//     exit();
// }
// CODIGO ENTREGA ERRORES DE BERNARDO
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';

mysqli_set_charset($conn, "utf8mb4");

$fecha_actual = date("Y-m-d");

// CONSULTA PARA OBTENER LOS FUNCIONARIOS DE UCI
$sql = "SELECT id_funcionarios, id_profesion, nombre_funcionarios, rut_funcionarios, pin_funcionarios FROM funcionarios_uci WHERE id_servicio = 5 ORDER BY id_funcionarios";
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
        <h2 class="text-center">Entrega De Turno Tecnologos Medicos</h2>
        <p class="text-center">Hospital Santa Cruz</p>
        <br><br>

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
        <h5 style="text-align: left;">Exámenes:</h5>
        <br>
        <h6 style="text-align: left;">Examenes Pendientes:</h6>
        <br>

        <form id="formexamenespendientes">
            <div class="row">
                <div class="col-md-4">
                    <label for="rxpendientes">RX:</label>
                    <input type="text" id="rx_pendientes" name="rx_pendientes" class="form-control" placeholder="Ingrese RX pendientes">
                </div>

                <div class="col-md-4">
                    <label for="tcpendientes">TC:</label>
                    <input type="text" id="tc_pendientes" name="tc_pendientes" class="form-control" placeholder="Ingrese TC pendientes">
                </div>

                <div class="col-md-4">
                    <label for="portatilpendientes">Portatil:</label>
                    <input type="text" id="portatil_pendientes" name="portatil_pendientes" class="form-control" placeholder="Ingrese portatiles pendientes">
                </div>
            </div>
        </form>
        <br><br>
        <hr>
        <h6 style="text-align: left;">Equipos Operativos:</h6>
        <br>

        <form id="formequiposoperativos">
            <div class="row">
                <div class="col-md-4">
                    <label for="rxequiposoperativos">RX:</label>
                    <input type="text" id="rx_equiposoperativos" name="rx_equiposoperativos" class="form-control" placeholder="Ingrese...">
                </div>

                <div class="col-md-4">
                    <label for="tcequiposoperativos">TC:</label>
                    <input type="text" id="tc_equiposoperativos" name="tc_equiposoperativos" class="form-control" placeholder="Ingrese...">
                </div>

                <div class="col-md-4">
                    <label for="portatilequiposoperativos">Portatil:</label>
                    <input type="text" id="portatil_equiposoperativos" name="portatil_equiposoperativos" class="form-control" placeholder="Ingrese...">
                </div>
            </div>
        </form>
        <br><br>
        <hr>
        <h6 style="text-align: left;">Examenes Enviados:</h6>
        <br>

        <form id="formexamenesenviados">
            <div class="row">
                <div class="col-md-4">
                    <label for="pacs_enviados">PACS:</label>
                    <input type="text" id="pacs_enviados" name="pacs_enviados" class="form-control" placeholder="Ingrese...">
                </div>

                <div class="col-md-4">
                    <label for="prueba_enviados">Prueba:</label>
                    <input type="text" id="prueba_enviados" name="prueba_enviados" class="form-control" placeholder="Ingrese...">
                </div>

                <div class="col-md-4">
                    <label for="syngovia_enviados">Syngovia:</label>
                    <input type="text" id="syngovia_enviados" name="syngovia_enviados" class="form-control" placeholder="Ingrese...">
                </div>
            </div>
        </form>
        <br><br>
        <hr>
        <h5 style="text-align: left;">Carro de Paros:</h5>
        <br>

        <form id="formcarrodeparos">
            <div class="row">
                <div class="col-md-7">
                    <label for="codigo_carroparos">Codigo:</label>
                    <input type="text" id="codigo_carroparos" name="codigo_carroparos" class="form-control" placeholder="Ingrese Codigo">
                </div>

                <div class="col-md-3">
                    <label for="carrodeparos">Abierto? :</label>
                    <select id="carrodeparos" name="carrodeparos" class="form-select form-select-sm" required>
                        <option value="No_carrodeparos">No , no fue abierto</option>
                        <option value="Si_carrodeparos">Si , fue abierto</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3" id="carroutilizado-container" style="display: none;">
                <div class="col-md-12">
                    <label for="carroutilizado">Si abrió el carro de paros ¿falta algo? Indique:</label>
                    <textarea id="carroutilizado" name="carroutilizado" class="form-control" placeholder="Si el carro de paros fue abierto , falta algo? Indique..."></textarea>
                </div>
            </div>
        </form>
        <br><br>
        <hr>

        <h5 style="text-align: left;">General:</h5>
        <br>

        <form id="formsalasyrx">
            <div class="row">
                <div class="col-md-10">
                    <label for="salasyrx">Salas limpias y estadisticas RX:</label>
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
                    <label for="cdgrabados">CD grabados:</label>
                    <input type="text" id="cd_grabados" name="cd_grabados" class="form-control" placeholder="Ingrese...">
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-10">
                    <label for="cdgrabadosotroturno">CD grabados en otro turno:</label>
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
        </form>

        <br>
        <!-- KINE TURNANTES -->
        <h6 class="text-center">Entrega de Turnos:</h6>
        <br>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="funcionario_saliente_1">Tecnologo Medico Saliente 1</label>
                <select id="funcionario_saliente_1" name="funcionario_saliente_1" class="form-select form-select-sm" required>
                    <option value="">-Seleccione Funcionario-</option>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $id_funcionarios = $row['id_funcionarios'];
                            $nombre_funcionarios = $row['nombre_funcionarios'];
                            $rut_funcionarios = $row['rut_funcionarios'];
                            echo "<option value='$id_funcionarios'>$nombre_funcionarios - $rut_funcionarios</option>";
                        }
                    } else {
                        echo "<option value=''>No hay disponibles</option>";
                    }
                    ?>
                    <input type="password" id="contrasena_saliente_1" name="contrasena_saliente_1" placeholder="Ingrese Contraseña TM.1" required class="form-control form-control-sm mt-2">
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label for="funcionario_entrante_1">Tecnologo Medico Entrante 1</label>
                <select id="funcionario_entrante_1" name="funcionario_entrante_1" class="form-select form-select-sm" required>
                    <option value="">-Seleccione Funcionario-</option>
                    <?php
                    mysqli_data_seek($result, 0); // Reinicia el puntero de los resultados
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $id_funcionarios = $row['id_funcionarios'];
                            $nombre_funcionarios = $row['nombre_funcionarios'];
                            $rut_funcionarios = $row['rut_funcionarios'];
                            echo "<option value='$id_funcionarios'>$nombre_funcionarios - $rut_funcionarios</option>";
                        }
                    } else {
                        echo "<option value=''>No hay disponibles</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <br><br>
        <div class="d-grid gap-2 col-4 mx-auto">
            <button type="submit" onclick="return validarYEnviar();" class="btn btn-danger">Entregar Turno</button>
        </div>
        <br>
        <hr>
    </div>

    <!-- SCRIPT PARA ABRIR UN TEXTAREA DEL EN CASO DE QUE SE HAYA UTILIZADO EL CARRO -->
    <script>
        document.getElementById('carrodeparos').addEventListener('change', function() {
            var reasonContainer = document.getElementById('carroutilizado-container');
            if (this.value === 'Si_carrodeparos') {
                reasonContainer.style.display = 'block';
            } else {
                reasonContainer.style.display = 'none';
            }
        });
    </script>
</body>

</html>