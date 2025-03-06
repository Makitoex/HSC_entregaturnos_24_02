<?php
session_start();
include 'conexion.php';

// Verificar conexión
if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener usuario y contraseña
    $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : "";
    $contraseña = isset($_POST['contraseña']) ? trim($_POST['contraseña']) : "";

    if (empty($usuario) || empty($contraseña)) {
        $error_message = "Error: Todos los campos son obligatorios.";
    } else {
        $sql = $conn->prepare("SELECT id_usuarios, nombre, contraseña, id_servicio, rol FROM usuarios WHERE nombre = ?");
        $sql->bind_param("s", $usuario);
        $sql->execute();
        $result = $sql->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Verificar que la contraseña ingresada coincida con la almacenada en la BD
            if (password_verify($contraseña, $row['contraseña'])) {
                // Iniciar sesión
                $_SESSION['id_usuarios'] = $row['id_usuarios'];
                $_SESSION['usuario'] = $row['nombre'];
                $_SESSION['id_servicio'] = $row['id_servicio'];
                $_SESSION['rol'] = $row['rol']; // Guardar el rol en la sesión

                // Asegurar que no haya salida antes de la redirección
                ob_start();

                // Redirigir según el rol
                if ($row['rol'] == 1) {
                    header("Location: dashboard_hsc_admin.php"); // Redirigir a la página dashboard_admin
                    exit();
                } elseif ($row['rol'] == 2) {
                    header("Location: dashboard_hsc_calidad.php"); // Redirigir a la página de HSC_calidad
                    exit();
                } else {
                    header("Location: menu.php"); // Si no es ningun rol de los asignados se redirige a menu de usuarios
                    exit();
                }
            } else {
                $error_message = "Contraseña incorrecta.";
            }
        } else {
            $error_message = "Usuario no encontrado.";
        }

        $sql->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="imagen/logohsc.ico" type="image/x-icon">
    <title>Login - Entrega de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('imagen/20150720_121622.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            max-width: 500px;
            width: 100%;
        }

        .login-card {
            width: 100%;
            max-width: 300px;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.75);
            margin-right: 10px;
        }

        .login-logo {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: url('imagen/logohsc.ico') no-repeat center center;
            background-size: cover;
            z-index: 1;
        }

        .btn-primary {
            background-color: rgb(189, 33, 33);
            border-color: rgb(189, 33, 33);
            color: white;
        }

        .btn-primary:hover {
            background-color: rgb(53, 14, 97);
            border-color: rgb(42, 11, 77);
            color: white;
        }

        .error-message {
            color: red;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .form-label, .form-control, .btn {
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h5 class="text-center mb-3 text-terciary" style="font-size: 15px;">Entrega de Turnos Hospital Santa Cruz</h5>
        <h6 class="text-center mb-4 text-primary" style="font-size: 14px;">Iniciar Sesión</h6>

        <!-- mensaje de error , si existe -->
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="index.php" method="POST">
            <div class="mb-3">
                <label for="usuario" class="form-label">Usuario:</label>
                <input type="text" class="form-control" id="usuario" name="usuario" required>
            </div>
            <div class="mb-3">
                <label for="contraseña" class="form-label">Contraseña:</label>
                <input type="password" class="form-control" id="contraseña" name="contraseña" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">INGRESAR</button>
            <p class="text-center mt-3" style="font-size: 12px;">Si olvida su contraseña, por favor contacte a soporte TI</p>
        </form>
    </div>
    <div class="login-logo">
        <a href="https://hospitalsantacruz.cl/">
            <img src="imagen/logohsc.ico" alt="Logo" style="width: 60px; height: 60px; border-radius: 50%;"></a>
    </div>
</body>
</html>