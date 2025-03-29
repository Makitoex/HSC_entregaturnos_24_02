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
        WHERE id_servicio = 13
        AND id_profesion IN (7, 3) 
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
    <title>Entrega de turnos Anestesistas</title>
    <link rel="icon" href="imagen/logohsc.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php include '/laragon/www/Sistema_entrega_turnos_HSC/navbar.php'; ?>
    <style>
        .highlight {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: black;
            padding: 1rem;
            border-radius: .25rem;
        }

        .form-label,
        .form-control {
            font-size: 0.875rem;
            /* smaller font size */
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <div class="container p-4 border rounded">
            <h2 class="text-center" style="font-size: 1.5rem;">Entrega De Turno Anestesistas Servicio de Pabellon</h2>
            <p class="text-center" style="font-size: 0.875rem;">Hospital Santa Cruz</p>

            <form action="guardar_turno_pb_anestesiologos.php" method="POST">
                <div class="row">
                    <!-- Fecha y turno -->
                    <div class="col-md-4 mb-3">
                        <label for="fecha" class="form-label">Fecha:</label>
                        <input type="date" id="fecha" name="fecha" value="<?php echo $fecha_actual; ?>" class="form-control form-control-sm" readonly />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="tipoturno" class="form-label">Selecciona el turno:</label>
                        <select id="tipoturno" name="tipoturno" class="form-select form-select-sm" required>
                            <option value="turno_largo">Turno Largo</option>
                            <option value="turno_noche">Turno Noche</option>
                        </select>
                    </div>
                </div>
                <hr>

                <!-- Paciente 1 -->
                <h6 style="font-size: 1rem;">Paciente 1</h6>
                <div class="mb-3">
                    <label for="nombre1" class="form-label">Nombre:</label>
                    <input type="text" id="nombre1" name="nombre1" class="form-control form-control-sm" required>
                </div>
                <div class="mb-3">
                    <label for="detalles1" class="form-label">Detalles:</label>
                    <input type="text" id="detalles1" name="detalles1" class="form-control form-control-sm">
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="peridural1" class="form-label">Peridural:</label>
                        <input type="text" id="peridural1" name="peridural1" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="cateter_reg1" class="form-label">Catéter Reg:</label>
                        <input type="text" id="cateter_reg1" name="cateter_reg1" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="analgev1" class="form-label">Analgev:</label>
                        <input type="text" id="analgev1" name="analgev1" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="otro1" class="form-label">Otro:</label>
                        <input type="text" id="otro1" name="otro1" class="form-control form-control-sm">
                    </div>
                </div>
                <hr>

                <!-- Paciente 2 -->
                <h6 style="font-size: 1rem;">Paciente 2</h6>
                <div class="mb-3">
                    <label for="nombre2" class="form-label">Nombre:</label>
                    <input type="text" id="nombre2" name="nombre2" class="form-control form-control-sm">
                </div>
                <div class="mb-3">
                    <label for="detalles2" class="form-label">Detalles:</label>
                    <input type="text" id="detalles2" name="detalles2" class="form-control form-control-sm">
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="peridural2" class="form-label">Peridural:</label>
                        <input type="text" id="peridural2" name="peridural2" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="cateter_reg2" class="form-label">Catéter Reg:</label>
                        <input type="text" id="cateter_reg2" name="cateter_reg2" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="analgev2" class="form-label">Analgev:</label>
                        <input type="text" id="analgev2" name="analgev2" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="otro2" class="form-label">Otro:</label>
                        <input type="text" id="otro2" name="otro2" class="form-control form-control-sm">
                    </div>
                </div>
                <hr>

                <!-- Paciente 3 -->
                <h6 style="font-size: 1rem;">Paciente 3</h6>
                <div class="mb-3">
                    <label for="nombre3" class="form-label">Nombre:</label>
                    <input type="text" id="nombre3" name="nombre3" class="form-control form-control-sm">
                </div>
                <div class="mb-3">
                    <label for="detalles3" class="form-label">Detalles:</label>
                    <input type="text" id="detalles3" name="detalles3" class="form-control form-control-sm">
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="peridural3" class="form-label">Peridural:</label>
                        <input type="text" id="peridural3" name="peridural3" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="cateter_reg3" class="form-label">Catéter Reg:</label>
                        <input type="text" id="cateter_reg3" name="cateter_reg3" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="analgev3" class="form-label">Analgev:</label>
                        <input type="text" id="analgev3" name="analgev3" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="otro3" class="form-label">Otro:</label>
                        <input type="text" id="otro3" name="otro3" class="form-control form-control-sm">
                    </div>
                </div>
                <hr>

                <!-- Paciente 4 -->
                <h6 style="font-size: 1rem;">Paciente 4</h6>
                <div class="mb-3">
                    <label for="nombre4" class="form-label">Nombre:</label>
                    <input type="text" id="nombre4" name="nombre4" class="form-control form-control-sm">
                </div>
                <div class="mb-3">
                    <label for="detalles4" class="form-label">Detalles:</label>
                    <input type="text" id="detalles4" name="detalles4" class="form-control form-control-sm">
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="peridural4" class="form-label">Peridural:</label>
                        <input type="text" id="peridural4" name="peridural4" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="cateter_reg4" class="form-label">Catéter Reg:</label>
                        <input type="text" id="cateter_reg4" name="cateter_reg4" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="analgev4" class="form-label">Analgev:</label>
                        <input type="text" id="analgev4" name="analgev4" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="otro4" class="form-label">Otro:</label>
                        <input type="text" id="otro4" name="otro4" class="form-control form-control-sm">
                    </div>
                </div>
                <hr>

                <!-- Paciente 5 -->
                <h6 style="font-size: 1rem;">Paciente 5</h6>
                <div class="mb-3">
                    <label for="nombre5" class="form-label">Nombre:</label>
                    <input type="text" id="nombre5" name="nombre5" class="form-control form-control-sm">
                </div>
                <div class="mb-3">
                    <label for="detalles5" class="form-label">Detalles:</label>
                    <input type="text" id="detalles5" name="detalles5" class="form-control form-control-sm">
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="peridural5" class="form-label">Peridural:</label>
                        <input type="text" id="peridural5" name="peridural5" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="cateter_reg5" class="form-label">Catéter Reg:</label>
                        <input type="text" id="cateter_reg5" name="cateter_reg5" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="analgev5" class="form-label">Analgev:</label>
                        <input type="text" id="analgev5" name="analgev5" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="otro5" class="form-label">Otro:</label>
                        <input type="text" id="otro5" name="otro5" class="form-control form-control-sm">
                    </div>
                </div>
                <hr>
                  <!-- Paciente 6 -->
                  <h6 style="font-size: 1rem;">Paciente 6</h6>
                <div class="mb-3">
                    <label for="nombre6" class="form-label">Nombre:</label>
                    <input type="text" id="nombre6" name="nombre6" class="form-control form-control-sm">
                </div>
                <div class="mb-3">
                    <label for="detalles6" class="form-label">Detalles:</label>
                    <input type="text" id="detalles6" name="detalles6" class="form-control form-control-sm">
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="peridural6" class="form-label">Peridural:</label>
                        <input type="text" id="peridural6" name="peridural6" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="cateter_reg6" class="form-label">Catéter Reg:</label>
                        <input type="text" id="cateter_reg6" name="cateter_reg6" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="analgev6" class="form-label">Analgev:</label>
                        <input type="text" id="analgev6" name="analgev6" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="otro6" class="form-label">Otro:</label>
                        <input type="text" id="otro6" name="otro6" class="form-control form-control-sm">
                    </div>
                </div>
                <hr>

                  <!-- Paciente 7 -->
                  <h6 style="font-size: 1rem;">Paciente 7</h6>
                <div class="mb-3">
                    <label for="nombre7" class="form-label">Nombre:</label>
                    <input type="text" id="nombre7" name="nombre7" class="form-control form-control-sm">
                </div>
                <div class="mb-3">
                    <label for="detalles7" class="form-label">Detalles:</label>
                    <input type="text" id="detalles7" name="detalles7" class="form-control form-control-sm">
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="peridural7" class="form-label">Peridural:</label>
                        <input type="text" id="peridural7" name="peridural7" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="cateter_reg7" class="form-label">Catéter Reg:</label>
                        <input type="text" id="cateter_reg7" name="cateter_reg7" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="analgev7" class="form-label">Analgev:</label>
                        <input type="text" id="analgev7" name="analgev7" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="otro7" class="form-label">Otro:</label>
                        <input type="text" id="otro7" name="otro7" class="form-control form-control-sm">
                    </div>
                </div>
                <hr>
                <!-- ANESTESISTAS TURNANTES -->
<h6 class="text-center">Anestesistas turnantes:</h6>
<br>
<div class="row">
    <!-- Anestesista Saliente 1 -->
    <div class="col-md-6 mb-3">
        <label for="funcionario_saliente_1" class="form-label">Anestesista Saliente 1</label>
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

    <!-- Anestesista Entrante 1 -->
    <div class="col-md-6 mb-3">
        <label for="funcionario_entrante_1" class="form-label">Anestesista Entrante 1</label>
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