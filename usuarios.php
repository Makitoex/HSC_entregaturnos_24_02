<?php
session_start();
if (!isset($_SESSION['id_usuarios'])) {
    header("Location: index.php");
    exit();
}
include '/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';
include 'navbar_funcionarios.php';

$edit_mode = false;
$id_usuario = $nombre = $id_servicio = "";
$rol = 0;

if (isset($_GET['edit'])) {
    $id_usuario = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id_usuarios = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $usuario_data = $stmt->get_result()->fetch_assoc();
    if ($usuario_data) {
        $edit_mode = true;
        extract($usuario_data);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $id_servicio = $_POST['id_servicio'];
    $rol = $_POST['rol'] ?? 0;
    if (!empty($_POST['id_usuario'])) {
        $stmt = $conn->prepare("UPDATE usuarios SET nombre=?, id_servicio=?, rol=? WHERE id_usuarios=?");
        $stmt->bind_param("siii", $nombre, $id_servicio, $rol, $_POST['id_usuario']);
    } else {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, contraseña, id_servicio, rol) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $nombre, $password, $id_servicio, $rol);
    }
    if ($stmt->execute()) {
        header("Location: usuarios.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id_usuarios=?");
    $stmt->bind_param("i", $_GET['delete']);
    if ($stmt->execute()) {
        header("Location: usuarios.php");
        exit();
    }
}

$result = $conn->query("SELECT u.id_usuarios, u.nombre, u.id_servicio, u.rol, s.nombre_servicio FROM usuarios u LEFT JOIN servicios s ON u.id_servicio = s.id_servicios");
$servicios = $conn->query("SELECT id_servicios, nombre_servicio FROM servicios");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="imagen/logohsc.ico" type="image/x-icon">
    <title>Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container py-3">
    <div class="alert alert-warning p-2 small">
        <strong>¡Aviso!</strong> Solo <b>Administración e Informática</b> pueden acceder. No elimine usuarios <b>Admin</b>.
        <strong>¡PAGINA SOLO PARA CREAR USUARIOS OSEA ACCESO A FORMULARIOS Y SU MENU CORRESPONDIENTE!</strong> <b> NO ES PARA AGREGAR FUNCIONARIOS</b>.
    </div>

    <div class="card p-3">
        <h6 class="text-center"><?= $edit_mode ? "Editar" : "Nuevo" ?> Usuario</h6>
        <form method="POST">
            <input type="hidden" name="id_usuario" value="<?= $edit_mode ? $id_usuario : '' ?>">
            <input type="text" name="nombre" class="form-control form-control-sm mb-2" placeholder="Nombre" value="<?= htmlspecialchars($nombre) ?>" required>
            <?php if (!$edit_mode) { ?>
                <input type="password" name="password" class="form-control form-control-sm mb-2" placeholder="Contraseña" required>
            <?php } ?>
            <select name="id_servicio" class="form-select form-select-sm mb-2">
                <?php while ($row = $servicios->fetch_assoc()) { ?>
                    <option value="<?= $row['id_servicios'] ?>" <?= ($row['id_servicios'] == $id_servicio) ? "selected" : "" ?>>
                        <?= htmlspecialchars($row['nombre_servicio']) ?>
                    </option>
                <?php } ?>
                
                <option value="101" <?= (101 == $id_servicio) ? "selected" : "" ?>>HSC_calidad</option>
            </select>
            <select name="rol" class="form-select form-select-sm mb-2">
                <option value="0" <?= ($rol == 0) ? "selected" : "" ?>>Usuario</option>
                <option value="1" <?= ($rol == 1) ? "selected" : "" ?>>Administrador</option>
                <option value="2" <?= ($rol == 2) ? "selected" : "" ?>>HSC_calidad</option>
            </select>
            <div class="text-center">
                <button type="submit" class="btn btn-sm btn-success"><?= $edit_mode ? "Actualizar" : "Crear" ?></button>
                <?php if ($edit_mode) { ?><a href="usuarios.php" class="btn btn-sm btn-secondary">Cancelar</a><?php } ?>
            </div>
        </form>
    </div>

    <hr>
    <h6>Lista de Usuarios</h6>
    <table class="table table-sm table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Servicio</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id_usuarios'] ?></td>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td><?= htmlspecialchars($row['nombre_servicio']) ?></td>
                    <td>
                        <?php
                            if ($row['rol'] == 1) {
                                echo "Admin";
                            } elseif ($row['rol'] == 2) {
                                echo "HSC_calidad";
                            } else {
                                echo "Usuario";
                            }
                        ?>
                    </td>
                    <td>
                        <a href="usuarios.php?edit=<?= $row['id_usuarios'] ?>" class="btn btn-sm btn-warning">EDITAR</a>
                        <a href="usuarios.php?delete=<?= $row['id_usuarios'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este usuario?');">ELIMINAR</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>

</html>
