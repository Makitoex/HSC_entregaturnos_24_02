<?php
session_start();

// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir archivo de conexión
include '/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';

// Establecer la codificación de caracteres
mysqli_set_charset($conn, "utf8mb4");

$fecha_actual = date("Y-m-d");

// CONSULTA PARA OBTENER LOS FUNCIONARIOS DE UCI QUE TIENEN id_servicio = 7
$sql = "SELECT id_funcionarios, id_profesion, nombre_funcionarios, rut_funcionarios, pin_funcionarios FROM funcionarios_pabellon WHERE id_servicio = 11 ORDER BY id_funcionarios";
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
    <title>Entrega de Turno Pabellon EU</title>

    <!-- ICONO DE PAGINA -->
    <link rel="icon" href="imagen/logohsc.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <?php include '/laragon/www/Sistema_entrega_turnos_HSC/navbar.php'; ?>
</head>

<body>
    <div class="container mt-4">
        <br>
        <h2 class="text-center">Entrega De Turno Enfermeros Pabellon</h2>
        <p class="text-center">Hospital Santa Cruz</p>
        <form action="guardar_turno_pb_enfermeros.php" method="POST">
            <br><br>
            <br>
            <!-- FECHA Y TURNO -->
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

            <!-- SELECCIÓN DE FUNCIONARIOS -->
            <div class="row">
                <div class="col-md-6">
                    <label for="anestesiologo_turno">Anestesiologo de Turno:</label>
                    <input type="text" id="anestesiologo_turno" name="anestesiologo_turno" class="form-control" placeholder="Ej: Juan, Dr. Gómez">
                </div>
                <div class="col-md-6">
                    <label for="tens_turno">Personal Técnico de Turno:</label>
                    <input type="text" id="tens_turno" name="tens_turno" class="form-control" placeholder="Ej: Juan, Pedro">
                </div>
            </div>
            <br>

            <!-- CANTIDAD DE BIOPSIAS Y ÓRDENES -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="biopsias_recibidas">Cantidad de Biopsias y Órdenes Recibidas:</label>
                        <input type="number" id="biopsias_recibidas" name="biopsias_recibidas" value="0" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="biopsias_entregadas">Cantidad de Biopsias y Órdenes Entregadas:</label>
                        <input type="number" id="biopsias_entregadas" name="biopsias_entregadas" value="0" class="form-control">
                    </div>
                </div>
            </div>
            <br>

            <!-- SECCIÓN DE REVISIONES -->
            <h6 style="text-align: center;">Revisión de Equipos y Registros</h6>
            <div class="row">
                <div class="col-md-6">
                    <label for="revision_carro_paro">Revisión Carro de Paro:</label>
                    <select id="revision_carro_paro" name="revision_carro_paro" class="form-select form-select-sm">
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                        <option value="no_corresponde">No Corresponde</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="revision_carro_recuperacion">Revisión de Carro de Recuperación:</label>
                    <select id="revision_carro_recuperacion" name="revision_carro_recuperacion" class="form-select form-select-sm">
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                        <option value="no_corresponde">No Corresponde</option>
                    </select>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-6">
                    <label for="stock_minimo_medicamentos">Stock Mínimo Medicamentos:</label>
                    <select id="stock_minimo_medicamentos" name="stock_minimo_medicamentos" class="form-select form-select-sm">
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                        <option value="no_corresponde">No Corresponde</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="stock_minimo_insumos">Stock Mínimo de Insumos:</label>
                    <select id="stock_minimo_insumos" name="stock_minimo_insumos" class="form-select form-select-sm">
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                        <option value="no_corresponde">No Corresponde</option>
                    </select>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-6">
                    <label for="limpieza_pyxis">Limpieza de Pyxis:</label>
                    <select id="limpieza_pyxis" name="limpieza_pyxis" class="form-select form-select-sm">
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                        <option value="no_corresponde">No Corresponde</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="limpieza_bodegas">Limpieza de Bodegas:</label>
                    <select id="limpieza_bodegas" name="limpieza_bodegas" class="form-select form-select-sm">
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                        <option value="no_corresponde">No Corresponde</option>
                    </select>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-6">
                    <label for="registro_temperatura_refrigerador">Registro de Temperatura Refrigerador:</label>
                    <select id="registro_temperatura_refrigerador" name="registro_temperatura_refrigerador" class="form-select form-select-sm">
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                        <option value="no_corresponde">No Corresponde</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="registro_temperatura_ambiental">Registro Temperatura Ambiental:</label>
                    <select id="registro_temperatura_ambiental" name="registro_temperatura_ambiental" class="form-select form-select-sm">
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                        <option value="no_corresponde">No Corresponde</option>
                    </select>
                </div>
            </div>
            <br>
            <br>
            <h6 style="text-align: center;">Comentarios:</h6>
            <br>
            <br>
            <!-- NUEVAS SECCIONES: NOVEDADES Y PENDIENTES -->
            <div class="row">
                <div class="col-md-12">
                    <label for="novedades">Novedades:</label>
                    <textarea id="novedades" name="novedades" class="form-control" rows="4" placeholder="Escribe las novedades del turno aquí..."></textarea>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <label for="pendientes">Pendientes:</label>
                    <textarea id="pendientes" name="pendientes" class="form-control" rows="4" placeholder="Escribe los pendientes aquí..."></textarea>
                </div>
            </div>
            <br>
  <!-- ENFERMEROS TURNANTES -->
<h6 class="text-center">Enfermeros turnantes:</h6>
<br>
<div class="row">
    <!-- Enfermero Saliente 1 -->
    <div class="col-md-6 mb-3">
        <label for="funcionario_saliente_1">Enfermero Saliente 1</label>
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
        <input type="password" id="contrasena_saliente_1" name="contrasena_saliente_1" placeholder="Ingrese Contraseña Enfermero 1" required class="form-control form-control-sm mt-2">
    </div>

    <!-- Enfermero Entrante 1 -->
    <div class="col-md-6 mb-3">
        <label for="funcionario_entrante_1">Enfermero Entrante 1</label>
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
                echo "<option value=''>No hay enfermeros disponibles</option>";
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
    <button type="submit" onclick="return validarYEnviarenfermeros();" class="btn btn-danger">Entregar Turno</button>
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
        document.getElementById(pinId).value = pinFuncionario;
    }

    function validarYEnviarenfermeros(event) {
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
        if (!validarYEnviarenfermeros(event)) {
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
