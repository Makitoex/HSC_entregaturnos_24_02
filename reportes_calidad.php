<?php
session_start();
if (!isset($_SESSION['id_usuarios'])) {
    header("Location: index.php");
    exit();
}

include 'conexion.php';
include 'navbar_funcionarios.php';

$id_usuario = $_SESSION['id_usuarios'];
$mensaje = "";

// Obtener la lista de usuarios para seleccionar destinatario
$sql_usuarios = "SELECT id_usuarios, nombre FROM usuarios WHERE id_usuarios != ?";
$stmt_usuarios = $conn->prepare($sql_usuarios);
$stmt_usuarios->bind_param("i", $id_usuario);
$stmt_usuarios->execute();
$result_usuarios = $stmt_usuarios->get_result();

// Procesar el formulario de generación de reporte
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['reporte']) && !empty($_POST['id_usuario_destino'])) {
        $reporte = htmlspecialchars(trim($_POST['reporte']), ENT_QUOTES, 'UTF-8'); // Sanitización
        $id_usuario_destino = intval($_POST['id_usuario_destino']); // Convertir a número entero

        $sql_insert = "INSERT INTO reportes (id_usuario_origen, id_usuario_destino, reporte, fecha) VALUES (?, ?, ?, NOW())";
        if ($stmt = $conn->prepare($sql_insert)) {
            $stmt->bind_param("iis", $id_usuario, $id_usuario_destino, $reporte);
            if ($stmt->execute()) {
                $mensaje = "Reporte enviado exitosamente.";
            } else {
                $mensaje = "Error al enviar el reporte.";
            }
            $stmt->close();
        }
    } else {
        $mensaje = "Debe completar todos los campos.";
    }
}

// Paginación
$registros_por_pagina = 10; // Cambiar este número para ajustar el número de registros por página
$pagina_actual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Obtener historial de todos los reportes generados con paginación
$sql_historial = "SELECT r.id_reporte, u.nombre AS remitente, r.reporte, r.fecha, 
                  CASE WHEN r.id_usuario_destino = 0 THEN 'Sin destinatario' ELSE ud.nombre END AS destinatario
                  FROM reportes r
                  JOIN usuarios u ON r.id_usuario_origen = u.id_usuarios
                  LEFT JOIN usuarios ud ON r.id_usuario_destino = ud.id_usuarios
                  ORDER BY r.fecha DESC
                  LIMIT ?, ?";
$stmt_historial = $conn->prepare($sql_historial);
$stmt_historial->bind_param("ii", $offset, $registros_por_pagina);
$stmt_historial->execute();
$result_historial = $stmt_historial->get_result();

// Obtener el número total de reportes para la paginación
$sql_total = "SELECT COUNT(*) AS total FROM reportes";
$result_total = $conn->query($sql_total);
$total_registros = $result_total->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - HSC Calidad</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="imagen/logohsc.ico" type="image/x-icon">
    <style>
        body { font-family: 'Roboto', sans-serif; background-color: #f0f4f8; }
        .container { width: 90%; margin: 30px auto; padding: 30px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1); }
        h1 { text-align: center; color: #333; font-family: 'Montserrat', sans-serif; }
        .form-group { margin-bottom: 1.5rem; }
        .form-control { padding: 10px; font-size: 16px; border-radius: 5px; }
        .btn-primary { background-color: #007bff; border: none; padding: 10px 20px; font-size: 16px; border-radius: 5px; }
        .btn-primary:hover { background-color:rgb(179, 0, 0); }
        .table { margin-top: 20px; }
        .logo { width: 100px; display: block; margin: 0 auto 20px; }
        .alert { margin-top: 20px; }
        .pagination { justify-content: center; }
    </style>
</head>
<body>
    <div class="container">
        <img src="/imagen/logohsf02.jpg" alt="Logo HSC" class="logo">
        <h1>Generar Reporte a Funcionarios</h1>
        <hr>

        <!-- Mostrar mensaje de éxito o error -->
        <?php if (!empty($mensaje)) { ?>
        <div class="alert alert-info" role="alert">
            <?php echo $mensaje; ?>
        </div>
        <?php } ?>

        <!-- Formulario para generar reporte -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="id_usuario_destino">Destinatario:</label>
                <select class="form-control" id="id_usuario_destino" name="id_usuario_destino" required>
                    <option value="">Seleccione un usuario</option>
                    <?php while ($row = $result_usuarios->fetch_assoc()) { ?>
                        <option value="<?php echo $row['id_usuarios']; ?>">
                            <?php echo htmlspecialchars($row['nombre']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="reporte">Detalle del Reporte:</label>
                <textarea class="form-control" id="reporte" name="reporte" rows="6" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Reporte</button>
        </form>

        <!-- Historial de todos los reportes generados -->
        <h3 class="mt-5">Historial de Todos los Reportes </h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Remitente</th>
                    <th>Reporte</th>
                    <th>Fecha</th>
                    <th>Destinatario</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_historial->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id_reporte']; ?></td>
                    <td><?php echo htmlspecialchars($row['remitente']); ?></td>
                    <td><?php echo htmlspecialchars($row['reporte']); ?></td>
                    <td><?php echo $row['fecha']; ?></td>
                    <td><?php echo htmlspecialchars($row['destinatario']); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Paginación -->
        <nav>
            <ul class="pagination">
                <?php if ($pagina_actual > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?pagina=<?php echo $pagina_actual - 1; ?>" aria-label="Anterior">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($pagina_actual < $total_paginas): ?>
                    <li class="page-item">
                        <a class="page-link" href="?pagina=<?php echo $pagina_actual + 1; ?>" aria-label="Siguiente">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>